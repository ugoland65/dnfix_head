<?php
namespace App\Services;

use App\Auth\AdminAuth;
use App\Core\AuthAdmin;
use App\Models\WorkLogModel;
use App\Models\WorkViewCheckModel;
use App\Classes\UploadedFile;
use App\Utils\TelegramUtils;
use App\Services\WorkLogHistoryService;

class WorkService
{

    /**
     * 업무 로그 목록 조회
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getWorkLogList($criteria=null)
    {

        $category = $criteria['category'] ?? null;
        $state = $criteria['state'] ?? null;
        $keyword = $criteria['keyword'] ?? null;
        $scope = $criteria['scope'] ?? null;

        $paging = $criteria['paging'] ?? true;
        $perPage = $criteria['per_page'] ?? 100;
        $page = $criteria['page'] ?? 1;

        $query = WorkLogModel::query()
            ->select([
                'idx',
                'category',
                'subject',
                'state',
                'reg_idx',
                'target_mb',
                'reg_date',
                'cmt_b_count',
                'cmt_s_count'
            ])
            ->when($category, function($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($state, function($query) use ($state) {

                if( $state == '전체보기' ){
                    // do nothing
                }elseif( $state == '대기/확인' ){
                    $query->where('state', '대기');
                    $query->orWhere('state', '확인');
                } else {
                    $query->where('state', $state);
                }

            })
            ->when($keyword, function($query) use ($keyword) {
                $query->where('subject', 'like', '%'.$keyword.'%');
            })
            ->when($scope, function($query) use ($scope) {
                $auth = AdminAuth::user();
                $adIdx = $auth['sess_idx'] ?? null;
                $adIdxInt = (int) $adIdx;
                $targetMbText = '@' . $adIdxInt;

                if ($scope === 'my_all') {
                    $query->where('reg_idx', $adIdx);
                } elseif ($scope === 'my_pending') {
                    $query->where('reg_idx', $adIdx)
                        ->whereIn('state', ['대기', '확인']);
                } elseif ($scope === 'inbox_unchecked') {
                    $query->where('reg_idx', '!=', $adIdx)
                        ->whereIn('state', ['대기', '확인'])
                        ->whereRaw("INSTR(target_mb, :target_mb) > 0", ['target_mb' => $targetMbText])
                        ->whereRaw("NOT EXISTS (SELECT 1 FROM work_view_check WHERE mode = 'log' AND mb_idx = {$adIdxInt} AND tidx = work_log.idx)");
                } elseif ($scope === 'inbox_checked') {
                    $query->where('reg_idx', '!=', $adIdx)
                        ->whereIn('state', ['대기', '확인'])
                        ->whereRaw("INSTR(target_mb, :target_mb) > 0", ['target_mb' => $targetMbText])
                        ->whereRaw("EXISTS (SELECT 1 FROM work_view_check WHERE mode = 'log' AND mb_idx = {$adIdxInt} AND tidx = work_log.idx)");
                }
            })
            ->orderByRaw("(CASE WHEN state IN ('완료', '반려') THEN 1 ELSE 0 END) ASC")
            ->orderBy('idx', 'desc');

        if ($perPage !== null ) {
            $result = $query->paginate($perPage, $page);
        } else {
            $result = $query->get()->toArray();
        }

        $adminServices = new AdminServices();
        $mentionMappingData = $adminServices->getMentionMappingData();

        foreach ($result['data'] as &$row) {
            $row['reg_name'] = $mentionMappingData[$row['reg_idx']]['ad_name'] ?? '';
            $row['reg_nick'] = $mentionMappingData[$row['reg_idx']]['ad_nick'] ?? '';
            $row['reg_image'] = $mentionMappingData[$row['reg_idx']]['ad_image'] ?? '';

            $target_list = [];
            if( !empty($row['target_mb']) ){
                $target_mb_idx = explode('@', ltrim($row['target_mb'], '@'));
                foreach($target_mb_idx as $target_mb){
                    $target_list[] = [
                        'idx' => $target_mb,
                        'name' => $mentionMappingData[$target_mb]['ad_name'] ?? '',
                        'nick' => $mentionMappingData[$target_mb]['ad_nick'] ?? '',
                        'image' => $mentionMappingData[$target_mb]['ad_image'] ?? '',
                    ];
                }
                $row['target_list'] = $target_list;
            }
        }
        unset($row);

        return $result;
    }

    /**
     * 업무 로그 조회
     * 
     * @param int $idx
     * @return array
     */
    public function getWorkLog($idx)
    {
        $query = WorkLogModel::find($idx);

        $result = $query->toArray();

        $adminServices = new AdminServices();
        $mentionMappingData = $adminServices->getMentionMappingData();

        $result['reg_name'] = $mentionMappingData[$result['reg_idx']]['ad_name'] ?? '';
        $result['reg_nick'] = $mentionMappingData[$result['reg_idx']]['ad_nick'] ?? '';
        $result['reg_image'] = $mentionMappingData[$result['reg_idx']]['ad_image'] ?? '';

        
        $target_list = [];
        if( !empty($result['target_mb']) ){
            $target_mb_idx = explode('@', ltrim($result['target_mb'], '@'));
            foreach($target_mb_idx as $target_mb){
                $target_list[] = [
                    'idx' => $target_mb,
                    'name' => $mentionMappingData[$target_mb]['ad_name'] ?? '',
                    'nick' => $mentionMappingData[$target_mb]['ad_nick'] ?? '',
                    'image' => $mentionMappingData[$target_mb]['ad_image'] ?? '',
                ];
            }
            $result['target_list'] = $target_list;
        }

        $result['view_check'] = json_decode($result['view_check'] ?? '[]', true);
        $result['link'] = json_decode($result['link'] ?? '[]', true);

        return $result;
    }


    /**
     * 업무 요청 신규생성 저장
     * 
     * @param array $requestData
     * @return array
     */
    public function saveTaskRequest($data, $files)
    {

        $idx = $data['idx'] ?? null;

        $mode = $data['mode'] ?? 'create';
        $subject = $data['subject'] ?? '';
        $state = $data['state'] ?? '대기';
        $body = $data['body'] ?? '';
        $category = $data['category'] ?? '';
        $work_log_file = $data['work_log_file'] ?? [];
        $link = $data['link'] ?? [];
        $target_mb_idx = $data['target_mb_idx'] ?? [];

        if (!is_array($target_mb_idx)) {
            $target_mb_idx = [];
        }

        // 첨부파일 (기존 파일 유지 + 다중 업로드 지원)
        $existingFileData = null;
        $existingFileNames = [];
        $existingFileInfo = [];

        if (!empty($work_log_file)) {
            // 기존 파일 정보 파싱 (문자열 JSON 또는 배열)
            if (is_string($work_log_file)) {
                $decoded = json_decode($work_log_file, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $existingFileData = $decoded;
                }
            } elseif (is_array($work_log_file)) {
                $existingFileData = $work_log_file;
            }

            if (is_array($existingFileData)) {
                // 기존 파일명/메타데이터 추출
                if (isset($existingFileData['file_name']) && is_array($existingFileData['file_name'])) {
                    $existingFileNames = $existingFileData['file_name'];
                } elseif (array_is_list($existingFileData)) {
                    $existingFileNames = $existingFileData;
                }

                if (!empty($existingFileData['file_info']) && is_array($existingFileData['file_info'])) {
                    $existingFileInfo = $existingFileData['file_info'];
                }
            }
        }

        $uploadDir = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2)) . '/data/work_log';
        $savedFiles = [];
        $savedInfo = [];

        if (!empty($files['work_log_file'])) {
            // 신규 업로드 저장 (다중/단일 모두 지원)
            $fileInput = $files['work_log_file'];

            if (is_array($fileInput['name'] ?? null)) {
                foreach ($fileInput['name'] as $idxKey => $name) {
                    $singleFile = [
                        'name' => $name,
                        'tmp_name' => $fileInput['tmp_name'][$idxKey] ?? '',
                        'type' => $fileInput['type'][$idxKey] ?? null,
                        'size' => $fileInput['size'][$idxKey] ?? 0,
                        'error' => $fileInput['error'][$idxKey] ?? UPLOAD_ERR_NO_FILE,
                    ];

                    if ($singleFile['error'] === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }

                    $uploaded = new UploadedFile($singleFile);
                    if ($uploaded->isValid()) {
                        $savedPath = $uploaded->moveImage($uploadDir);
                        $savedName = basename($savedPath);
                        $savedFiles[] = $savedName;
                        // 파일 메타데이터 저장
                        $savedInfo[$savedName] = [
                            'name' => $savedName,
                            'original_name' => $uploaded->getClientOriginalName(),
                            'size' => $uploaded->getSize(),
                            'mime' => $uploaded->getMimeType(),
                        ];
                    }
                }
            } else {
                $uploaded = new UploadedFile($fileInput);
                if ($uploaded->isValid()) {
                    $savedPath = $uploaded->moveImage($uploadDir);
                    $savedName = basename($savedPath);
                    $savedFiles[] = $savedName;
                    // 파일 메타데이터 저장
                    $savedInfo[$savedName] = [
                        'name' => $savedName,
                        'original_name' => $uploaded->getClientOriginalName(),
                        'size' => $uploaded->getSize(),
                        'mime' => $uploaded->getMimeType(),
                    ];
                }
            }
        }

        // 기존 파일 + 신규 파일 병합
        $fileNames = array_values(array_unique(array_merge($existingFileNames, $savedFiles)));
        $fileInfo = array_merge($existingFileInfo, $savedInfo);

        // target_mb 저장 정책: 비어있으면 빈 문자열, 있으면 @id@id 형식
        $target_mb = '';
        if (!empty($target_mb_idx)) {
            $target_mb = implode('', array_map(function ($mb) {
                $mb = trim((string)$mb);
                return $mb !== '' ? '@' . $mb : '';
            }, $target_mb_idx));
        }

        $link_json = json_encode($link, JSON_UNESCAPED_UNICODE);

        $updateData = [
            'subject' => $subject,
            'state' => $state,
            'body' => $body,
            'category' => $category,
            'target_mb' => $target_mb,
            'link' => $link_json,
        ];

        $auth = AdminAuth::user();

        if (!empty($fileNames)) {
            // 파일 정보 JSON으로 저장
            $fileCode = $existingFileData['file_code'] ?? (date('YmdHis') . '_' . ($auth['sess_idx'] ?: '0'));
            $fileData = [
                'file_code' => $fileCode,
                'file_name' => $fileNames,
                'file_info' => $fileInfo,
            ];
            $updateData['file'] = json_encode($fileData, JSON_UNESCAPED_UNICODE);
        }

        $attributes = [];
        if (!empty($idx)) {
            $attributes['idx'] = $idx;
        }

        $existingWorkInfo = null;
        if ($mode == 'modify' && !empty($idx)) {
            $existingWorkInfo = WorkLogModel::find($idx);
            $existingWorkInfo = $existingWorkInfo ? $existingWorkInfo->toArray() : null;
        }

        if( $mode == 'create' ){

            $regData =[
                "reg" => [ 
                    "date" => date('Y-m-d H:i:s'), 
                    "id" => $auth['sess_id'], 
                    "idx" => $auth['sess_idx'],
                    "name" => $auth['sess_name'],
                    "ip" => $auth['ip'],
                    "domain" => $auth['domain']
                ]
            ];
        
            $regJson = json_encode($regData, JSON_UNESCAPED_UNICODE);

            $updateData['reg'] = $regJson;
            $updateData['reg_idx'] = $auth['sess_idx'];
            $updateData['reg_date'] = date('Y-m-d H:i:s');
            
        }

        $workInfo = WorkLogModel::updateOrCreate(
            $attributes,
            $updateData
        );

        $workLogHistoryService = new WorkLogHistoryService();

        $adminServices = new AdminServices();

        $telegram = new TelegramUtils();

        $in_message = "🟠 업무요청 - 참여자 지정되었습니다.\n\n";
        $in_message .= "<b>[" . $workInfo['idx'] . "] " . $workInfo['subject'] . "</b>\n\n";
        $in_message .= "( " . AuthAdmin::getSession('sess_name') . " :: " . date('Y-m-d H:i:s') . ")";

        $out_message = "🟠 업무요청 - 참여자 제거되었습니다.\n\n";
        $out_message .= "<b>[" . $workInfo['idx'] . "] " . $workInfo['subject'] . "</b>\n\n";
        $out_message .= "( " . AuthAdmin::getSession('sess_name') . " :: " . date('Y-m-d H:i:s') . ")";

        if( $mode == 'create' ){

            $after_json_data = [
                'state' => $workInfo['state'],
                'target_mb' => $workInfo['target_mb'],
            ];

            $after_json = json_encode($after_json_data, JSON_UNESCAPED_UNICODE);

            $workLogHistoryService->writeWorkLogHistory([
                'mode' => 'hand',
                'category' => '업무요청',
                'target_pk' => $workInfo['idx'],
                'action_mode' => 'create',
                'action_summary' => '업무 요청 신규생성',
                'action_date' => date('Y-m-d H:i:s'),
                'action_id' => $auth['sess_id'],
                'action_idx' => $auth['sess_idx'],
                'action_name' => $auth['sess_name'],
                'before_json' => [],
                'after_json' => $after_json,
                'diff_json' => [],
            ]);

            $mentionTargetTelegramIds = $adminServices->getMentionTargetTelegramId($target_mb_idx);
            foreach($mentionTargetTelegramIds as $mentionTargetTelegramId){
                $telegramResult = $telegram->sendMessage($mentionTargetTelegramId['ad_telegram_token'], $in_message, 'HTML');
            }

        }elseif( $mode == 'modify' ){

            $before_json_data = [
                'subject' => $existingWorkInfo['subject'] ?? '',
                'state' => $existingWorkInfo['state'] ?? '',
                'category' => $existingWorkInfo['category'] ?? '',
                'target_mb' => $existingWorkInfo['target_mb'] ?? '',
                'link' => $existingWorkInfo['link'] ?? '',
            ];
            $after_json_data = [
                'subject' => $subject,
                'state' => $state,
                'category' => $category,
                'target_mb' => $target_mb,
                'link' => $link_json,
            ];
            $diff_json_data = [];
            $changed_fields = [];

            $compareMap = [
                'subject' => '제목',
                'state' => '상태',
                'category' => '분류',
                'target_mb' => '참여자',
                'link' => '링크',
            ];

            foreach ($compareMap as $key => $label) {
                if (($before_json_data[$key] ?? '') !== ($after_json_data[$key] ?? '')) {
                    $diff_json_data[$key] = [$before_json_data[$key] ?? null, $after_json_data[$key] ?? null];
                    $changed_fields[] = $label;
                }
            }

            // body는 내용이 크므로 변경 여부만 기록
            $body_changed = (($existingWorkInfo['body'] ?? '') !== $body);
            $diff_json_data['body'] = $body_changed;
            if ($body_changed) {
                $changed_fields[] = '내용';
            }

            $action_body = !empty($changed_fields) ? (implode(', ', $changed_fields) . ' 변경') : '변경 없음';

            $workLogHistoryService->writeWorkLogHistory([
                'mode' => 'hand',
                'category' => '업무요청',
                'target_pk' => $workInfo['idx'],
                'action_mode' => 'modify',
                'action_summary' => '업무 요청 수정',
                'action_body' => $action_body,
                'action_date' => date('Y-m-d H:i:s'),
                'action_id' => $auth['sess_id'],
                'action_idx' => $auth['sess_idx'],
                'action_name' => $auth['sess_name'],
                'before_json' => $before_json_data,
                'after_json' => $after_json_data,
                'diff_json' => $diff_json_data,
            ]);

            $old_target_mb = $existingWorkInfo['target_mb'] ?? '';
            $old_target_mb_idxs = explode('@', ltrim($old_target_mb, '@'));
            $old_target_mb_idxs = array_filter(array_map('strval', $old_target_mb_idxs), static function ($value) {
                return $value !== '';
            });

            $new_target_mb_idxs = array_filter(array_map('strval', $target_mb_idx), static function ($value) {
                return $value !== '';
            });

            // 수정 시 참여자 변경 여부 비교 (변경 없으면 알림 미발송)
            $added_target_mb_idxs = array_values(array_diff($new_target_mb_idxs, $old_target_mb_idxs));
            $removed_target_mb_idxs = array_values(array_diff($old_target_mb_idxs, $new_target_mb_idxs));

            // 신규 참여자에게만 지정 알림 발송
            if (!empty($added_target_mb_idxs)) {
                $mentionTargetTelegramIds = $adminServices->getMentionTargetTelegramId($added_target_mb_idxs);
                foreach ($mentionTargetTelegramIds as $mentionTargetTelegramId) {
                    $telegramResult = $telegram->sendMessage($mentionTargetTelegramId['ad_telegram_token'], $in_message, 'HTML');
                }
            }

            // 제거된 참여자에게만 제거 알림 발송
            if (!empty($removed_target_mb_idxs)) {
                $mentionTargetTelegramIds = $adminServices->getMentionTargetTelegramId($removed_target_mb_idxs);
                foreach ($mentionTargetTelegramIds as $mentionTargetTelegramId) {
                    $telegramResult = $telegram->sendMessage($mentionTargetTelegramId['ad_telegram_token'], $out_message, 'HTML');
                }
            }

        }

        return $workInfo;

    }


    /**
     * 업무 로그 읽음 체크
     * 
     * @param int $idx
     * @return array
     */
    public function checkWorkLogRead($idx)
    {

        $workInfo = WorkLogModel::find($idx)->toArray();

        $auth = AdminAuth::user();

        $reg = [
            "idx" => $auth['sess_idx'],
            "date" => date('Y-m-d H:i:s'),
            "id" => $auth['sess_id'],
            "name" => $auth['sess_name'],
            "ip" => AdminAuth::getIp(),
            "domain" => AdminAuth::getDomain()
        ];

        //$regJson = json_encode($reg, JSON_UNESCAPED_UNICODE);

        $view_check = json_decode($workInfo['view_check'] ?? '[]', true);

        if( !empty($view_check) ){
            foreach($view_check as $view){
                if( $view['idx'] == $auth['sess_idx'] ){
                    return false;
                }
            }
            array_push($view_check, $reg);
        } else {
            $view_check = [$reg];
        }

        $view_checkJson = json_encode($view_check, JSON_UNESCAPED_UNICODE);
        $workInfo['view_check'] = $view_checkJson;
        WorkLogModel::where('idx', $idx)->update(['view_check' => $view_checkJson]);

        return true;
    }


    /**
     * 업무 로그 참여자 추가
     * 
     * @param int $idx
     * @return array
     */
    public function addParticipant($data)
    {

        $idx = $data['idx'] ?? null;
        $target_mb_idxs = $data['target_mb_idxs'] ?? [];
        if (!is_array($target_mb_idxs)) {
            $target_mb_idxs = [];
        }

        $workInfo = WorkLogModel::find($idx)->toArray();

        $adminServices = new AdminServices();
        $mentionMappingData = $adminServices->getMentionMappingData();

        $old_target_mb = $workInfo['target_mb'];
        $old_target_mb_idxs = explode('@', ltrim($workInfo['target_mb'], '@'));
        $old_target_mb_idxs = array_filter(array_map('strval', $old_target_mb_idxs), static function ($value) {
            return $value !== '';
        });

        // 신규 요청 idx 정리 (중복 제거 + 빈값 제거)
        $target_mb_idxs = array_filter(array_map('strval', $target_mb_idxs), static function ($value) {
            return $value !== '';
        });
        $target_mb_idxs = array_values(array_unique($target_mb_idxs));

        // 기존과 비교해 신규만 추출
        $new_target_mb_idxs = array_values(array_diff($target_mb_idxs, $old_target_mb_idxs));

        // 기존 + 신규 합쳐서 @idx 형태로 저장
        // 기존 순서 유지 + 신규는 뒤에 추가
        $merged_target_mb_idxs = array_merge($old_target_mb_idxs, $new_target_mb_idxs);
        $merged_target_mb_idxs = array_values(array_unique($merged_target_mb_idxs));
        $target_mb = '';
        if (!empty($merged_target_mb_idxs)) {
            $target_mb = implode('', array_map(function ($mb) {
                return '@' . $mb;
            }, $merged_target_mb_idxs));
        }

        WorkLogModel::where('idx', $idx)->update(['target_mb' => $target_mb]);

        $mentionTargetTelegramIds = $adminServices->getMentionTargetTelegramId($new_target_mb_idxs);

        $message = "🟠 업무요청 - 참여자 지정되었습니다.\n\n";
        $message .= "<b>[" . $workInfo['idx'] . "] " . $workInfo['subject'] . "</b>\n\n";
        $message .= "( " . AuthAdmin::getSession('sess_name') . " :: " . date('Y-m-d H:i:s') . ")";

        $telegram = new TelegramUtils();
        foreach($mentionTargetTelegramIds as $mentionTargetTelegramId){
            $telegramResult = $telegram->sendMessage($mentionTargetTelegramId['ad_telegram_token'], $message, 'HTML');
        }

        $result = [
            'old_target_mb_idxs' => $old_target_mb_idxs,
            'new_target_mb_idxs' => $new_target_mb_idxs,
        ];

        $before_json_data = [
            'target_mb' => $old_target_mb,
        ];
        $before_json = json_encode($before_json_data, JSON_UNESCAPED_UNICODE);

        $after_json_data = [
            'target_mb' => $target_mb,
        ];
        $after_json = json_encode($after_json_data, JSON_UNESCAPED_UNICODE);

        $new_target_mb_names = [];
        if( !empty($new_target_mb_idxs)){
            foreach($new_target_mb_idxs as $new_target_mb_idx){
                $new_target_mb_name = $mentionMappingData[$new_target_mb_idx]['ad_name'] ?? '';
                $new_target_mb_names[] = $new_target_mb_name;
            }
        }

        $action_body = implode(', ', $new_target_mb_names).' 참여자 추가지정';

        $auth = AdminAuth::user();
        $workLogHistoryService = new WorkLogHistoryService();

        $workLogHistoryService->writeWorkLogHistory([
            'mode' => 'hand',
            'category' => '업무요청',
            'target_pk' => $workInfo['idx'],
            'action_mode' => 'add_participant',
            'action_summary' => '참여자 추가지정',
            'action_body' => $action_body,
            'action_date' => date('Y-m-d H:i:s'),
            'action_id' => $auth['sess_id'],
            'action_idx' => $auth['sess_idx'],
            'action_name' => $auth['sess_name'],
            'before_json' => $before_json ?? [],
            'after_json' => $after_json ?? [],
            'diff_json' => [],
        ]);

        return $result;
    }

    
    /**
     * 업무 로그 참여자 제거
     * 
     * @param int $idx
     * @return array
     */
    public function removeParticipant($data)
    {
        $idx = $data['idx'] ?? null;

        $target_mb_idx = $data['target_mb_idx'] ?? null;
        if (empty($idx) || $target_mb_idx === null || $target_mb_idx === '') {
            return ['removed' => false, 'message' => '잘못된 요청입니다.'];
        }

        $workInfo = WorkLogModel::find($idx)->toArray();
        $old_target_mb_idxs = explode('@', ltrim($workInfo['target_mb'], '@'));
        $old_target_mb_idxs = array_filter(array_map('strval', $old_target_mb_idxs), static function ($value) {
            return $value !== '';
        });

        $removeId = (string)$target_mb_idx;
        $filtered = array_values(array_filter($old_target_mb_idxs, static function ($value) use ($removeId) {
            return $value !== $removeId;
        }));

        $target_mb = '';
        if (!empty($filtered)) {
            $target_mb = implode('', array_map(function ($mb) {
                return '@' . $mb;
            }, $filtered));
        }

        WorkLogModel::where('idx', $idx)->update(['target_mb' => $target_mb]);

        $adminServices = new AdminServices();
        $admin = $adminServices->getAdmin(['idx' => $removeId]);
        $mentionTargetTelegramId = $admin['ad_telegram_token'];

        if( $mentionTargetTelegramId ){
            $message = "🟠 업무요청 - 참여자 제거되었습니다.\n\n";
            $message .= "<b>[" . $workInfo['idx'] . "] " . $workInfo['subject'] . "</b>\n\n";
            $message .= "( " . AuthAdmin::getSession('sess_name') . " :: " . date('Y-m-d H:i:s') . ")";

            $telegram = new TelegramUtils();
            $telegramResult = $telegram->sendMessage($mentionTargetTelegramId, $message, 'HTML');
        }

        $auth = AdminAuth::user();
        $workLogHistoryService = new WorkLogHistoryService();

        $action_body = $admin['ad_name'].' 참여자 제거';

        $workLogHistoryService->writeWorkLogHistory([
            'mode' => 'hand',
            'category' => '업무요청',
            'target_pk' => $workInfo['idx'],
            'action_mode' => 'add_participant',
            'action_summary' => '참여자 제거',
            'action_body' => $action_body,
            'action_date' => date('Y-m-d H:i:s'),
            'action_id' => $auth['sess_id'],
            'action_idx' => $auth['sess_idx'],
            'action_name' => $auth['sess_name'],
        ]);

        return [
            'removed' => true,
            'removed_idx' => $removeId,
            'remaining_target_mb_idxs' => $filtered,
        ];
    }

    /**
     * 업무 상태 변경
     *
     * @param array $data
     * @return array
     */
    public function changeState($data)
    {

        $idx = $data['idx'] ?? null;
        $state = $data['state'] ?? null;
        $allowedStates = ['대기', '확인', '완료', '반려'];

        if (empty($idx) || empty($state) || !in_array($state, $allowedStates, true)) {
            throw new \InvalidArgumentException('유효하지 않은 요청입니다.');
        }

        $workInfo = WorkLogModel::find($idx)->toArray();
        if (empty($workInfo)) {
            throw new \InvalidArgumentException('대상을 찾을 수 없습니다.');
        }

        if (($workInfo['state'] ?? '') === $state) {
            return [
                'updated' => false,
                'state' => $state,
            ];
        }

        WorkLogModel::where('idx', $idx)->update([
            'state' => $state,
        ]);

        $message = "🟠 업무요청 - 상태 변경되었습니다.\n\n";
        $message .= "<b>[" . $workInfo['idx'] . "] " . $workInfo['subject'] . "</b>\n\n";
        $message .= "상태가 " . ($workInfo['state'] ?? '') . " → " . $state . " 로 변경되었습니다.\n\n";
        $message .= "( " . AuthAdmin::getSession('sess_name') . " :: " . date('Y-m-d H:i:s') . ")";

        $target_mb_idx = explode('@', ltrim($workInfo['target_mb'], '@'));

        $adminServices = new AdminServices();
        $mentionTargetTelegramIds = $adminServices->getMentionTargetTelegramId($target_mb_idx);
        
        $telegram = new TelegramUtils();

        foreach($mentionTargetTelegramIds as $mentionTargetTelegramId){
            $telegramResult = $telegram->sendMessage($mentionTargetTelegramId['ad_telegram_token'], $message, 'HTML');
        }

        $auth = AdminAuth::user();
        $workLogHistoryService = new WorkLogHistoryService();

        $workLogHistoryService->writeWorkLogHistory([
            'mode' => 'hand',
            'category' => '업무요청',
            'target_pk' => $idx,
            'action_mode' => 'change_state',
            'action_summary' => '상태 변경',
            'action_body' => ($workInfo['state'] ?? '') . ' → ' . $state,
            'action_date' => date('Y-m-d H:i:s'),
            'action_id' => $auth['sess_id'],
            'action_idx' => $auth['sess_idx'],
            'action_name' => $auth['sess_name'],
            'before_json' => [
                'state' => $workInfo['state'] ?? null,
            ],
            'after_json' => [
                'state' => $state,
            ],
            'diff_json' => [
                'state' => [$workInfo['state'] ?? null, $state],
            ],
        ]);

        return [
            'updated' => true,
            'state' => $state,
        ];
    }


    /**
     * 대시보드 카운트 (정규화 없이 최적화 버전)
     */
    public function getMyDashboardCounts($adIdx): array
    {
        
        $targetMbText = '@'.$adIdx;

        // 1) 내가 작성한 글 카운트
        /*
            SUM(category='업무일지') AS my_count1,
        */
        $my = WorkLogModel::query()
            ->selectRaw("
                SUM(category='프로젝트') AS my_count2,
                SUM(category='기획안')   AS my_count3,
                SUM(category='업무요청') AS my_count4,

                SUM(category='프로젝트' AND state IN ('대기','확인')) AS my_ing_count2,
                SUM(category='기획안'   AND state IN ('대기','확인')) AS my_ing_count3,
                SUM(category='업무요청' AND state IN ('대기','확인')) AS my_ing_count4
            ")
            ->where('reg_idx', '=', $adIdx)
            ->first();

        // 2) 내가 작성하지 않았고, 나에게 할당된 글(대기/확인) 중 읽음/안읽음 카운트
        // - work_view_check를 "내 것만" 먼저 추린 뒤 조인해서 조인 비용 최소화
        $adIdxInt = (int) $adIdx;
        $viewCheckSub = WorkViewCheckModel::query()
            ->select('tidx')
            ->whereRaw("mode = 'log' AND mb_idx = {$adIdxInt}")
            ->groupBy('tidx');

        $inbox = WorkLogModel::query()
            ->from('work_log as A')
            ->selectRaw("
                SUM(A.category='프로젝트' AND V.tidx IS NULL) AS my_count2,
                SUM(A.category='기획안'   AND V.tidx IS NULL) AS my_count3,
                SUM(A.category='업무요청' AND V.tidx IS NULL) AS my_count4,

                SUM(A.category='프로젝트' AND V.tidx IS NOT NULL) AS my_ing_count2,
                SUM(A.category='기획안'   AND V.tidx IS NOT NULL) AS my_ing_count3,
                SUM(A.category='업무요청' AND V.tidx IS NOT NULL) AS my_ing_count4
            ")
            ->joinSub($viewCheckSub, 'V', function ($join) {
                $join->on('V.tidx', '=', 'A.idx');
            }, 'LEFT')
            ->where('A.reg_idx', '!=', $adIdx)
            ->whereIn('A.category', ['프로젝트', '기획안', '업무요청'])
            ->whereIn('A.state', ['대기', '확인'])
            ->whereRaw("INSTR(A.target_mb, :target_mb) > 0", ['target_mb' => $targetMbText])
            ->first();

        return [
            'my' => $my ? $my->toArray() : [],
            'inbox' => $inbox ? $inbox->toArray() : [],
        ];
        
    }

}