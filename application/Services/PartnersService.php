<?php

namespace App\Services;

use App\Core\BaseClass;
use App\Models\PartnersModel;
use App\Services\AdminActionLogService;

class PartnersService extends BaseClass {

    /**
     * 거래처 목록 조회
     * @param array $getData 파라미터
     * @param array|null $extraData 추가 파라미터
     * @return array
     */
    public function getPartnersList($getData, $extraData=null) {

        $payloadData = array_replace((array)$getData, (array)$extraData);

        $page = isset($payloadData['page']) ? $payloadData['page'] : 1;
        $perPage = isset($payloadData['per_page']) ? $payloadData['per_page'] : 100;

        $query = PartnersModel::query();

        if( isset($payloadData['category']) ){
            $query->where('category', $payloadData['category']);
        }

        /*
        $result = $query->orderBy('idx', 'DESC')
            ->paginate($perPage, $page);
        */
        $result = $query->orderBy('idx', 'DESC')
            ->get()
            ->toArray();

        return $result;

    }

    /**
     * 파트너 공급처 셀렉트바 조회
     * @param array|null $extraData 추가 파라미터
     * └ $extraData {string} $extraData['showMode'] : WHOLE_SUPPLIER - 성인용품도매(도매공급사)
     * └ $extraData {bool} $extraData['listActive'] : 목록 화면에서 사용 여부
     * @return array
     */
    public function getPartnersForSelect($extraData=null) {
        
        $query = PartnersModel::select('idx', 'name')
            ->orderBy('name', 'asc');

        if( $extraData['showMode'] == 'WHOLE_SUPPLIER' ){
            $query->whereIn('category', ['성인용품공급', '성인용품도매']);
        }

        /*
        if( $extraData['listActive'] ){
            $query->where('BD_LIST_ACTIVE', 'Y');
        }
        */

        $partnerList = $query->get()
            ->toArray();

        return $partnerList;

    }

    /**
     * 거래처 상세 정보를 폼에서 사용할 형태로 반환한다.
     */
    public function getPartnerInfo(int $idx): array
    {
        $partner = [];
        if ($idx > 0) {
            $partnerModel = PartnersModel::find($idx);
            if (!$partnerModel) {
                throw new \Exception('거래처 정보를 찾을 수 없습니다.');
            }
            $partner = $partnerModel->toArray();
        }

        $info = json_decode((string)($partner['info'] ?? '{}'), true);
        if (!is_array($info)) {
            $info = [];
        }

        $categories = [
            '성인용품공급',
            '성인용품도매',
            '성인용품해외',
            '운영제휴',
            '운영서비스',
            '부자재',
            '기타',
        ];
        $currentCategory = trim((string)($partner['category'] ?? ''));
        if ($currentCategory !== '' && !in_array($currentCategory, $categories, true)) {
            $categories[] = $currentCategory;
        }

        return [
            'partner' => $partner,
            'info' => $info,
            'categories' => $categories,
        ];
    }

    /**
     * 거래처를 등록하거나 수정한다.
     */
    public function savePartner(array $data): array
    {
        $idx = (int)($data['idx'] ?? 0);
        $name = trim((string)($data['name'] ?? ''));
        $category = trim((string)($data['category'] ?? ''));
        if ($name === '') {
            throw new \Exception('거래처명을 입력해주세요.');
        }
        if ($category === '') {
            throw new \Exception('거래처 종류를 선택해주세요.');
        }

        $info = [
            'nation' => trim((string)($data['nation'] ?? '한국')),
            'hp' => [
                'url' => trim((string)($data['hp_url'] ?? '')),
                'id' => trim((string)($data['hp_id'] ?? '')),
                'pw' => trim((string)($data['hp_pw'] ?? '')),
            ],
            'info' => [
                'tel' => trim((string)($data['tel'] ?? '')),
                'email' => trim((string)($data['email'] ?? '')),
            ],
            'keeper' => [
                'name' => trim((string)($data['keeper_name'] ?? '')),
                'rank' => trim((string)($data['keeper_rank'] ?? '')),
                'tel' => trim((string)($data['keeper_tel'] ?? '')),
            ],
        ];
        $saveData = [
            'name' => $name,
            'category' => $category,
            'info' => json_encode($info, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'memo' => trim((string)($data['memo'] ?? '')),
            'bank_name' => trim((string)($data['bank_name'] ?? '')),
            'bank_account' => trim((string)($data['bank_account'] ?? '')),
            'bank_account_name' => trim((string)($data['bank_account_name'] ?? '')),
        ];
        $actionUrl = trim((string)($data['action_url'] ?? ($_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI'] ?? '')));

        if ($idx <= 0) {
            $newIdx = PartnersModel::query()->insertGetId($saveData);
            $this->logPartnerSave((int)$newIdx, 'create', [], array_merge(['idx' => (int)$newIdx], $saveData), $actionUrl);

            return [
                'idx' => (int)$newIdx,
                'mode' => 'create',
                'message' => '거래처가 등록되었습니다.',
            ];
        }

        $partnerModel = PartnersModel::find($idx);
        if (!$partnerModel) {
            throw new \Exception('거래처 정보를 찾을 수 없습니다.');
        }
        $partner = $partnerModel->toArray();

        $updated = PartnersModel::query()->update($saveData, ['idx' => $idx]);
        if (!$updated) {
            throw new \Exception('거래처 수정에 실패했습니다.');
        }
        $this->logPartnerSave($idx, 'update', $partner, array_merge($partner, $saveData), $actionUrl);

        return [
            'idx' => $idx,
            'mode' => 'modify',
            'message' => '거래처가 수정되었습니다.',
        ];
    }

    private function logPartnerSave(int $idx, string $actionMode, array $before, array $after, string $actionUrl): void
    {
        $before = $this->sanitizePartnerLogData($before);
        $after = $this->sanitizePartnerLogData($after);
        $actionLog = new AdminActionLogService();

        try {
            $actionLog->log([
                'target_type' => 'partner',
                'target_table' => 'partners',
                'target_pk' => (string)$idx,
                'action_mode' => $actionMode,
                'action_summary' => $actionMode === 'create' ? '거래처 등록' : '거래처 수정',
                'before_json' => $before,
                'after_json' => $after,
                'diff_json' => $actionLog->buildDiff($before, $after),
                'action_url' => $actionUrl !== '' ? $actionUrl : null,
            ]);
        } catch (\Throwable $e) {
            // 로그 기록 실패가 거래처 저장 결과에 영향을 주지 않도록 한다.
        }
    }

    private function sanitizePartnerLogData(array $partner): array
    {
        $info = json_decode((string)($partner['info'] ?? '{}'), true);
        if (is_array($info) && isset($info['hp']['pw'])) {
            $info['hp']['pw'] = '[비공개]';
            $partner['info'] = $info;
        }

        return $partner;
    }

}