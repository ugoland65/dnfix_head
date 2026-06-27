<?php
namespace App\Services;

use App\Classes\DB;
use App\Models\CsRequestModel;
use App\Models\CsRequestGroupModel;
use App\Auth\AdminAuth;
use App\Services\AdminServices;
use App\Models\AdminModel;
use App\Utils\TelegramUtils;

class CsRequestService
{

    /**
     * C/S 목록 조회
     * 
     * @return array
     */
    public function getCsRequestList($criteria)
    {
        
        $page = (int)($criteria['page'] ?? ($criteria['pn'] ?? 1));
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int)($criteria['per_page'] ?? 100);
        if ($perPage < 1) {
            $perPage = 100;
        }

        $cs_status = $criteria['cs_status'] ?? '요청+처리중';
        $keyword = $criteria['keyword'] ?? '';
        $order_no = trim((string)($criteria['order_no'] ?? ''));

        $query = CsRequestModel::query()
            ->when($cs_status !== '' && $cs_status !== 'all', function($query) use ($cs_status) {
                if( $cs_status == '요청+처리중' ){
                    $query->where(function($statusQuery) {
                        $statusQuery->where('cs_status', '요청');
                        $statusQuery->orWhere('cs_status', '처리중');
                    });
                }else{
                    $query->where('cs_status', $cs_status);
                }
            })
            ->when($order_no !== '', function($query) use ($order_no) {
                $query->where('order_no', $order_no);
            })
            ->when($keyword, function($query) use ($keyword) {
                $query->where(function($keywordQuery) use ($keyword) {
                    $keywordQuery->where('mem_id', 'like', '%'.$keyword.'%');
                    $keywordQuery->orWhere('mem_name', 'like', '%'.$keyword.'%');
                    $keywordQuery->orWhere('mem_phone', 'like', '%'.$keyword.'%');
                    $keywordQuery->orWhere('receiver_name', 'like', '%'.$keyword.'%');
                    $keywordQuery->orWhere('receiver_phone', 'like', '%'.$keyword.'%');
                    $keywordQuery->orWhere('order_no', 'like', '%'.$keyword.'%');
                });
            })
            ->orderBy('idx', 'desc')
            ->get();

        //$result = $query->toArray();
        $result = $query->paginate($perPage, $page);

        $adminMap = AdminModel::query()
            ->select(['idx', 'ad_id', 'ad_name', 'ad_nick', 'ad_image'])
            ->get()
            ->keyBy('idx')
            ->toArray();
         
        foreach($result['data'] as &$row){
            $row['reg_name'] = $adminMap[$row['reg_pk']]['ad_name'] ?? '';

            $targetList = [];
            if (!empty($row['target_mb'])) {
                $targetMbIdxs = explode('@', ltrim((string)$row['target_mb'], '@'));
                $targetMbIdxs = array_filter(array_map('trim', $targetMbIdxs), static function($value) {
                    return $value !== '';
                });

                foreach ($targetMbIdxs as $targetMbIdx) {
                    $targetList[] = [
                        'idx' => $targetMbIdx,
                        'name' => $adminMap[$targetMbIdx]['ad_name'] ?? '',
                        'image' => $adminMap[$targetMbIdx]['ad_image'] ?? '',
                    ];
                }
            }
            $row['target_list'] = $targetList;
        }
        unset($row);

        return $result;
    }


    /**
     * C/S 상태별 카운트 조회
     * 
     * @return array
     */
    public function getCsRequestCount()
    {
        $query = CsRequestModel::query()
            ->select(['cs_status', 'COUNT(*) as count'])
            ->where('cs_status', '!=', '처리완료')
            ->groupBy('cs_status')
            ->get()
            ->toArray();

        $result = $query;

        return $result;
    }


    /**
     * C/S 상세 조회
     * 
     * @param int $idx C/S 고유번호
     * @return array
     */
    public function getCsRequestDetail($idx)
    {
        $query = CsRequestModel::find($idx)
            ->toArray();

        return $query;

    }


    /**
     * C/S 처리 요청
     * 
     * @param array $requestData 요청 데이터
     * @return array
     */
    public function createCsRequest($data)
    {
        return $this->createSingleCsRequest($data);
    }

    /**
     * 다건 C/S 그룹 생성
     *
     * @param array $data 요청 데이터
     * @return array
     */
    public function createCsRequestGroup($data)
    {
        $orderNos = $data['orderNos'] ?? [];
        if (!is_array($orderNos)) {
            $orderNos = [$orderNos];
        }
        $orderNos = array_map('trim', $orderNos);
        $orderNos = array_filter($orderNos, static function($value) {
            return $value !== '';
        });
        $orderNos = array_values(array_unique($orderNos));

        if (empty($orderNos)) {
            throw new \InvalidArgumentException('주문번호가 없습니다.');
        }

        $category = $data['category'] ?? '출고준비';
        $actionDate = $data['actionDate'] ?? null;
        if ($category !== '출고지정일') {
            $actionDate = null;
        }

        $registrar = $this->getRegistrarContext();
        $csBody = $data['csBody'] ?? null;

        // 주문번호 1건이면 그룹 없이 단건 등록
        if (count($orderNos) === 1) {
            $itemPayload = $data;
            $itemPayload['orderNo'] = $orderNos[0];
            $itemPayload['category'] = $category;
            $itemPayload['actionDate'] = $actionDate;
            $itemPayload['csBody'] = $csBody;
            unset($itemPayload['groupCode']);

            $this->createSingleCsRequest($itemPayload, null, null, $registrar);

            return [
                'groupIdx' => null,
                'groupCode' => null,
                'createdCount' => 1,
                'isGrouped' => false,
            ];
        }

        $groupCode = $this->generateGroupCode();

        return DB::transaction(function() use ($orderNos, $category, $actionDate, $registrar, $groupCode, $csBody, $data) {
            $groupIdx = CsRequestGroupModel::query()->insertGetId([
                'group_code' => $groupCode,
                'category' => $category,
                'action_date' => $actionDate,
                'cs_body' => $csBody,
                'request_count' => 0,
                'reg_id' => $registrar['regId'],
                'reg_pk' => $registrar['regPk'],
                'reg_name' => $registrar['adminName'],
            ]);

            $createdCount = 0;
            $groupItemNo = 1;
            foreach ($orderNos as $orderNo) {
                $itemPayload = $data;
                $itemPayload['orderNo'] = $orderNo;
                $itemPayload['category'] = $category;
                $itemPayload['actionDate'] = $actionDate;
                $itemPayload['csBody'] = $csBody;
                $itemPayload['groupCode'] = $groupCode;

                $this->createSingleCsRequest($itemPayload, (int)$groupIdx, $groupItemNo, $registrar);
                $createdCount++;
                $groupItemNo++;
            }

            CsRequestGroupModel::update(
                ['idx' => (int)$groupIdx],
                ['request_count' => $createdCount]
            );

            return [
                'groupIdx' => (int)$groupIdx,
                'groupCode' => $groupCode,
                'createdCount' => $createdCount,
                'isGrouped' => true,
            ];
        });
    }

    /**
     * 단건 C/S 생성
     *
     * @param array $data 요청 데이터
     * @param int|null $csGroupIdx 그룹 IDX
     * @param int|null $groupItemNo 그룹 내 순번
     * @param array|null $registrar 등록자 캐시 정보
     * @return mixed
     */
    private function createSingleCsRequest(array $data, $csGroupIdx = null, $groupItemNo = null, $registrar = null)
    {
        if ($registrar === null) {
            $registrar = $this->getRegistrarContext();
        }

        $orderNo = trim((string)($data['orderNo'] ?? ''));
        if ($orderNo === '') {
            throw new \InvalidArgumentException('주문번호가 없습니다.');
        }

        $category = $data['category'] ?? '출고준비';
        $actionDate = $data['actionDate'] ?? null;
        if ($category !== '출고지정일') {
            $actionDate = null;
        }

        $orderDate = $data['orderDate'] ?? null;
        $paymentDt = $data['paymentDt'] ?? null;
        $memNo = $data['memNo'] ?? null;
        $memName = $data['memName'] ?? null;
        $memPhone = $data['memPhone'] ?? null;
        $receiverName = $data['receiverName'] ?? null;
        $receiverPhone = $data['receiverPhone'] ?? null;
        $groupNm = $data['groupNm'] ?? null;
        $csBody = $data['csBody'] ?? null;
        $targetMbIdx = $data['targetMbIdx'] ?? [];
        $targetMb = $this->buildTargetMb($targetMbIdx);

        $godoApiService = new GodoApiService();
        $godoGoodsInfo = $godoApiService->getGodoOrderInfo($orderNo);

        $orderDate = $godoGoodsInfo['regDt'] ?? $orderDate;
        $paymentDt = $godoGoodsInfo['paymentDt'] ?? $paymentDt;
        $memNo = $godoGoodsInfo['memNo'] ?? $memNo;
        $memId = trim((string)($godoGoodsInfo['memId'] ?? ($data['memId'] ?? '')));
        if ($memId === '') {
            $memId = trim((string)($memNo ?? ''));
        }
        $memName = $godoGoodsInfo['memNm'] ?? $memName;
        $memPhone = $godoGoodsInfo['cellPhone'] ?? $memPhone;
        $receiverName = $godoGoodsInfo['receiverName'] ?? $receiverName;
        $receiverPhone = $godoGoodsInfo['receiverCellPhone'] ?? $receiverPhone;
        $groupNm = $godoGoodsInfo['groupNm'] ?? $groupNm;

        $memNo = $this->normalizeMemNo($memNo);

        $inputData = [
            'category' => $category,
            'order_no' => $orderNo,
            'order_date' => $orderDate,
            'payment_date' => $paymentDt,
            'action_date' => $actionDate,
            'mem_no' => $memNo,
            'mem_id' => $memId,
            'mem_name' => $memName,
            'mem_phone' => $memPhone,
            'receiver_name' => $receiverName,
            'receiver_phone' => $receiverPhone,
            'group_nm' => $groupNm,
            'cs_group_idx' => $csGroupIdx,
            'group_item_no' => $groupItemNo,
            'target_mb' => $targetMb,
            'cs_status' => '요청',
            'cs_body' => $csBody,
            'reg_id' => $registrar['regId'],
            'reg_pk' => $registrar['regPk'],
        ];

        $csRequest = CsRequestModel::insert($inputData);
        $latest = CsRequestModel::query()
            ->select(['idx'])
            ->where('reg_pk', $registrar['regPk'])
            ->where('order_no', $orderNo)
            ->orderBy('idx', 'desc')
            ->first();
        $csIdx = 0;
        if (is_array($latest)) {
            $csIdx = (int)($latest['idx'] ?? 0);
        } elseif (is_object($latest)) {
            $csIdx = (int)($latest->idx ?? 0);
        }

        $telegram_chat_id = config('admin.telegram_chat_id');
        $chat_room_id = $telegram_chat_id['chat_room_ids']['cs']['chat_id'];

        //dd($chat_room_id);

        $telegram = new TelegramUtils();

        $message = "🟣 ".$category." C/S 요청\n";
        if (!empty($data['groupCode'])) {
            $message .= "그룹코드 : " . $data['groupCode'] . " \n";
        }
        $message .= "주문번호 : " .  $orderNo . " \n";
        $message .= "주문일시 : " . $this->formatDateText($orderDate) . "\n";
        $message .= "----------------------------\n";
        $message .= $csBody . "\n";
        $message .= "----------------------------\n";
        $message .= "요청일 : " . date('Y-m-d H:i:s') . "\n";
        $message .= "등록자 : " . $registrar['adminName'] . "\n";
        $message .= "----------------------------\n";

        $telegramResult = $telegram->sendMessage($chat_room_id, $message, 'HTML');
        //dd($telegramResult);

        if (!empty($targetMbIdx)) {
            $participantMessage = $this->buildParticipantTelegramMessage(
                '지정',
                $csIdx,
                $orderNo,
                $category,
                $registrar['adminName'],
                $data['groupCode'] ?? null
            );
            $this->notifyParticipants($targetMbIdx, $participantMessage);
        }

        return $csRequest;

    }

    /**
     * 등록자 컨텍스트 조회
     *
     * @return array
     */
    private function getRegistrarContext(): array
    {
        $admin = AdminAuth::user();
        $regId = $admin["sess_id"] ?? null;
        $regPk = $admin["sess_idx"] ?? null;

        $adminServices = new AdminServices();
        $adminData = $adminServices->getAdmin(['idx' => $regPk]);

        return [
            'regId' => $regId,
            'regPk' => $regPk,
            'adminName' => $adminData['ad_name'] ?? '',
        ];
    }

    /**
     * mem_no 정규화
     *
     * @param mixed $memNo
     * @return int|null
     */
    private function normalizeMemNo($memNo)
    {
        if (is_string($memNo)) {
            $memNo = trim($memNo);
        }

        if ($memNo === '' || $memNo === null) {
            return null;
        }

        if (is_numeric($memNo)) {
            return (int)$memNo;
        }

        return null;
    }

    /**
     * 그룹코드 생성
     *
     * @return string
     */
    private function generateGroupCode(): string
    {
        return strtoupper(bin2hex(random_bytes(13)));
    }

    /**
     * 날짜 표시 문자열 포맷
     *
     * @param mixed $value
     * @return string
     */
    private function formatDateText($value): string
    {
        if (empty($value)) {
            return '-';
        }

        $timestamp = strtotime((string)$value);
        if ($timestamp === false) {
            return (string)$value;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * 참여자 배열을 @id@id 문자열로 변환
     *
     * @param mixed $targetMbIdx
     * @return string
     */
    private function buildTargetMb($targetMbIdx): string
    {
        if (!is_array($targetMbIdx)) {
            $targetMbIdx = [$targetMbIdx];
        }

        $targetMbIdx = array_values(array_unique(array_filter(array_map(static function($value) {
            return trim((string)$value);
        }, $targetMbIdx), static function($value) {
            return $value !== '';
        })));

        if (empty($targetMbIdx)) {
            return '';
        }

        return implode('', array_map(static function($mb) {
            return '@'.$mb;
        }, $targetMbIdx));
    }


    /**
     * C/S 상태변경
     * 
     * @param array $requestData 요청 데이터
     * @return array
     */
    public function updateCsStatus($data)
    {
        $query = CsRequestModel::find($data['idx']);
        $beforeTargetIds = $this->parseTargetMbToIds($query->target_mb ?? '');
        $category = $data['category'] ?? ($query->category ?? null);
        $actionDate = $data['action_date'] ?? null;
        if ($category !== '출고지정일') {
            $actionDate = null;
        }

        $admin = AdminAuth::user();

        $processor_id = $admin["sess_id"] ?? null;
        $processor_pk = $admin["sess_idx"] ?? null;
        $processor_name = $admin["sess_name"] ?? null;
        $newTargetIds = $this->normalizeTargetMbIdxList($data['target_mb_idx'] ?? []);
        $removedTargetIds = array_values(array_diff($beforeTargetIds, $newTargetIds));
        $addedTargetIds = array_values(array_diff($newTargetIds, $beforeTargetIds));

        $update_data = [
            'category' => $category,
            'cs_status' => $data['cs_status'],
            'action_date' => $actionDate,
            'target_mb' => $this->buildTargetMb($data['target_mb_idx'] ?? []),
            'processor_id' => $processor_id,
            'processor_pk' => $processor_pk,
            'processor_name' => $processor_name,
            'process_action' => $data['process_action'] ?? null,
            'processor_date' => date('Y-m-d H:i:s'),
        ];

        $csRequest = CsRequestModel::update(['idx' => $data['idx']], $update_data);

        if (!empty($addedTargetIds)) {
            $message = $this->buildParticipantTelegramMessage(
                '지정',
                (int)$data['idx'],
                (string)($query->order_no ?? ''),
                (string)$category,
                (string)$processor_name,
                null
            );
            $this->notifyParticipants($addedTargetIds, $message);
        }

        if (!empty($removedTargetIds)) {
            $message = $this->buildParticipantTelegramMessage(
                '제거',
                (int)$data['idx'],
                (string)($query->order_no ?? ''),
                (string)$category,
                (string)$processor_name,
                null
            );
            $this->notifyParticipants($removedTargetIds, $message);
        }

        return $csRequest;

    }

    /**
     * 참여자 지정/제거 텔레그램 메시지 생성
     *
     * @param string $mode 지정|제거
     * @param int $csIdx
     * @param string $orderNo
     * @param string $category
     * @param string $actorName
     * @param string|null $groupCode
     * @return string
     */
    private function buildParticipantTelegramMessage(string $mode, int $csIdx, string $orderNo, string $category, string $actorName, $groupCode = null): string
    {
        $title = $mode === '제거' ? '🟣 C/S 참여자 제거' : '🟣 C/S 참여자 지정';
        $message = $title . "\n\n";
        $message .= "<b>[" . $csIdx . "] " . $orderNo . "</b>\n";
        $message .= "분류 : " . $category . "\n";
        if (!empty($groupCode)) {
            $message .= "그룹코드 : " . $groupCode . "\n";
        }
        $message .= "( " . $actorName . " :: " . date('Y-m-d H:i:s') . " )";

        return $message;
    }

    /**
     * 참여자 텔레그램 알림 발송
     *
     * @param array $targetMbIdx
     * @param string $message
     * @return void
     */
    private function notifyParticipants(array $targetMbIdx, string $message): void
    {
        $adminServices = new AdminServices();
        $mentionTargetTelegramIds = $adminServices->getMentionTargetTelegramId($targetMbIdx);
        if (empty($mentionTargetTelegramIds)) {
            return;
        }

        $telegram = new TelegramUtils();
        foreach ($mentionTargetTelegramIds as $mentionTargetTelegramId) {
            $token = trim((string)($mentionTargetTelegramId['ad_telegram_token'] ?? ''));
            if ($token === '') {
                continue;
            }
            $telegram->sendMessage($token, $message, 'HTML');
        }
    }

    /**
     * @id@id 문자열을 배열로 변환
     *
     * @param string $targetMb
     * @return array
     */
    private function parseTargetMbToIds(string $targetMb): array
    {
        $targetMb = trim($targetMb);
        if ($targetMb === '') {
            return [];
        }

        $items = explode('@', ltrim($targetMb, '@'));
        return $this->normalizeTargetMbIdxList($items);
    }

    /**
     * 참여자 배열 정규화
     *
     * @param mixed $targetMbIdx
     * @return array
     */
    private function normalizeTargetMbIdxList($targetMbIdx): array
    {
        if (!is_array($targetMbIdx)) {
            $targetMbIdx = [$targetMbIdx];
        }

        return array_values(array_unique(array_filter(array_map(static function($value) {
            return trim((string)$value);
        }, $targetMbIdx), static function($value) {
            return $value !== '';
        })));
    }
}   