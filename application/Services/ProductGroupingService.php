<?php

namespace App\Services;

use Exception;
use App\Auth\AdminAuth;
use App\Core\AuthAdmin;
use App\Models\ProductGroupingModel;
use App\Models\ProductModel;
use App\Models\ProductPartnerModel;
use App\Services\ProductPartnerService;

class ProductGroupingService
{

    private $mode_text = [
        'op' => "운영",
        'sale' => "데이할인",
        'period' => "기간할인",
        'qty' => "수량체크",
        'event' => "기획전"
    ];

    private $prd_mode_text = [
        'prdDB' => "상품DB",
        'provider' => "공급사 상품",
    ];


    /**
     * 그룹핑 목록 조회
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getProductGroupingList($criteria)
    {

        $paging = $criteria['paging'] ?? true;
        $perPage = $criteria['per_page'] ?? 100;
        $page = $criteria['page'] ?? 1;

        $query = ProductGroupingModel::query();
        $query->orderBy('idx', 'desc');
        $query->paginate($criteria['per_page'] ?? 100, $criteria['page'] ?? 1);

        $productGroupingList = $paging ? $query->paginate($perPage, $page)
            : $query->get()->toArray();

        foreach ($productGroupingList['data'] as &$row) {
            $row['pg_mode_text'] = $this->mode_text[$row['pg_mode']] ?? "";
            $row['prd_mode_text'] = $this->prd_mode_text[$row['prd_mode']] ?? "";
            $rawData = $row['data'] ?? [];
            $decodedData = is_array($rawData) ? $rawData : json_decode((string)$rawData, true);
            $row['prd_count'] = is_array($decodedData) ? count($decodedData) : 0;

            $row['reg'] = json_decode($row['reg'] ?? '{}', true);
            $row['reg_date'] = date("y.m.d H:i", strtotime($row['reg']['d']['date'] ?? ""));
            $row['reg_name'] = $row['reg']['d']['name'] ?? "";
        }
        unset($row);

        return $productGroupingList;
        
    }


    /**
     * 그룹핑 셀렉트 목록
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getProductGroupingForSelect($criteria)
    {
        $criteria = is_array($criteria) ? $criteria : [];
        $pg_state = $criteria['pg_state'] ?? null;
        $prd_mode = $criteria['prd_mode'] ?? null;

        $query = ProductGroupingModel::query()
            ->select('idx', 'pg_subject')
            ->when($pg_state !== null && $pg_state !== '', function ($query) use ($pg_state) {
                $query->where('pg_state', $pg_state);
            })
            ->when($prd_mode !== null && $prd_mode !== '', function ($query) use ($prd_mode) {
                $query->where('prd_mode', $prd_mode);
            })
            ->orderBy('idx', 'desc')
            ->get()
            ->toArray();

        return $query;

    }


    /**
     * 그룹핑 상세
     * 
     * @param int $idx
     * @return array
     */
    public function getProductGrouping($idx)
    {

        $query = ProductGroupingModel::find($idx)
            ->toArray();
        
        $query['pg_mode_text'] = $this->mode_text[$query['pg_mode']] ?? "";
        $query['prd_mode_text'] = $this->prd_mode_text[$query['prd_mode']] ?? "";

        $rawData = $query['data'] ?? [];
        $decodedData = is_array($rawData) ? $rawData : json_decode((string)$rawData, true);
        $query['data'] = $decodedData;
        $query['prd_count'] = is_array($decodedData) ? count($decodedData) : 0;

        $query['prd_data'] = [];
        $prd_idxs = array_column($decodedData, 'idx');

        if( $query['prd_mode'] == 'prdDB' ){
            //$prd_data = $productGroupingService->getProductPartnerWhereInIdx($prd_idxs);

        }elseif( $query['prd_mode'] == 'provider' ){

            $productPartnerService = new ProductPartnerService();
            $query['prd_data'] = $productPartnerService->getProductPartnerWhereInIdx($prd_idxs);

            foreach($query['prd_data'] as $key => &$item){
                $item['memo_work'] = $query['data'][$key]['memo'] ?? '';
            }
            unset($item);

        }


        return $query;

    }


    /**
     * 그룹핑 상품 지정
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function productGroupingAddSave($criteria)
    {
        $criteria = is_array($criteria) ? $criteria : [];

        $mode = $criteria['mode'] ?? 'prdDB'; //prdDB: 상품DB, provider: 공급사 상품
        $idx = $criteria['idx'] ?? null;
        $prd_idxs = $criteria['prd_idxs'] ?? [];
        if (!is_array($prd_idxs)) {
            $prd_idxs = is_string($prd_idxs) ? explode(',', $prd_idxs) : [];
        }
        $prd_idxs = array_values(array_filter($prd_idxs, static fn($v) => $v !== null && $v !== ''));

        if (empty($prd_idxs)) {
            throw new Exception('추가할 신규 상품이 없습니다.');
        }


        $data_array = [];

        //신규 그룹핑 생성
        if( empty($idx) ){

            $pg_subject = $criteria['pg_subject'] ?? null;
            $public = $criteria['public'] ?? '공개';
            $pg_mode = $criteria['pg_mode'] ??  'op'; //운영
            $prd_mode = $criteria['prd_mode'] ?? $mode;
            $pg_state = $criteria['pg_state'] ?? '진행';
            $pg_sday = $criteria['pg_sday'] ?? null;
            $pg_day = $criteria['pg_day'] ?? null;
            $pg_sday = ($pg_sday === null || $pg_sday === '') ? '0000-00-00' : $pg_sday;
            $pg_day = ($pg_day === null || $pg_day === '') ? '0000-00-00' : $pg_day;
            $pg_memo = $criteria['pg_memo'] ?? '';
    
            $auth = AdminAuth::user();
            $adIdx = $auth['sess_idx'] ?? null;

            $reg = [
                'reg_mode' => 'new',
                'd' => AuthAdmin::getConnectionInfo()
            ];

            $reg = json_encode($reg, JSON_UNESCAPED_UNICODE);

            $data_array = $this->buildGroupingRows($mode, $prd_idxs);

            $data_json = json_encode($data_array, JSON_UNESCAPED_UNICODE);

            $inputData = [
                'pg_subject' => $pg_subject,
                'public' => $public,
                'pg_mode' => $pg_mode,
                'prd_mode' => $prd_mode,
                'pg_state' => $pg_state,
                'pg_sday' => $pg_sday,
                'pg_day' => $pg_day,
                'pg_memo' => $pg_memo,
                'data' => $data_json,
                'reg_admin_pk' => $adIdx,
                'reg' => $reg,
            ];

            $result = ProductGroupingModel::create($inputData);
            

        //기존 그룹핑에서 상품만 추가하기
        }else{

            $productGroupingData = ProductGroupingModel::find($idx);

            if( empty($productGroupingData) ){
                throw new Exception('그룹핑 데이터가 없습니다.');
            }

            $productGroupingData = $productGroupingData->toArray();

            $data_json = json_decode($productGroupingData['data'] ?? '[]', true);
            if( !is_array($data_json) ){
                $data_json = [];
            }

            // 기존 그룹 데이터에 이미 포함된 idx는 prd_idxs에서 제거하고,
            // 신규로 추가해야 할 idx만 남긴다.
            $existingIdxMap = [];
            foreach ($data_json as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $rowIdx = (string)($row['idx'] ?? '');
                if ($rowIdx !== '') {
                    $existingIdxMap[$rowIdx] = true;
                }
            }

            $prd_idxs = array_values(array_filter($prd_idxs, static function ($prdIdx) use ($existingIdxMap) {
                $key = (string)$prdIdx;
                return $key !== '' && !isset($existingIdxMap[$key]);
            }));


            $data_array = $this->buildGroupingRows($mode, $prd_idxs);

            $data_json = array_merge($data_json, $data_array);

            $data_json = json_encode($data_json, JSON_UNESCAPED_UNICODE);
            $result = ProductGroupingModel::where('idx', $idx)->update(['data' => $data_json]);

        }

        $savedIdx = $idx;
        if (empty($savedIdx) && is_array($result)) {
            $savedIdx = $result['idx'] ?? null;
        } elseif (empty($savedIdx) && is_object($result)) {
            $savedIdx = $result->idx ?? null;
        }

        return [
            'result' => $result,
            'idx' => $savedIdx,
            'added_count' => count($data_array),
        ];

    }


    /**
     * 모드에 맞는 그룹핑 data row를 생성한다.
     *
     * @param string $mode
     * @param array $prdIdxs
     * @return array
     */
    private function buildGroupingRows(string $mode, array $prdIdxs): array
    {
        if (empty($prdIdxs)) {
            return [];
        }

        $rows = [];

        // 상품 DB일 경우
        if ($mode === 'prdDB') {
            $productData = ProductModel::query()
                ->from('COMPARISON_DB as A')
                ->select('A.CD_IDX', 'A.CD_NAME', 'D.ps_idx')
                ->leftJoin('prd_stock as D', 'D.ps_prd_idx', '=', 'A.CD_IDX')
                ->whereIn('A.CD_IDX', $prdIdxs)
                ->get()
                ->keyBy('CD_IDX')
                ->toArray();

            foreach ($prdIdxs as $prdIdx) {
                $rows[] = [
                    'idx' => $prdIdx,
                    'stockidx' => $productData[$prdIdx]['ps_idx'] ?? '',
                    'pname' => $productData[$prdIdx]['CD_NAME'] ?? '',
                    'mode_data' => [],
                    'memo' => '',
                ];
            }

            return $rows;
        }

        // 공급사 상품일 경우
        if ($mode === 'provider' || $mode === 'provider_product') {
            $productData = ProductPartnerModel::whereIn('idx', $prdIdxs)
                ->get()
                ->keyBy('idx')
                ->toArray();

            foreach ($prdIdxs as $prdIdx) {
                $rows[] = [
                    'idx' => $prdIdx,
                    'pname' => $productData[$prdIdx]['name'] ?? '',
                    'mode_data' => [],
                    'memo' => '',
                ];
            }
        }

        return $rows;
    }


    /**
     * 그룹핑 수정저장
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function productGroupingUpdate($inputData)
    {
        $inputData = is_array($inputData) ? $inputData : [];

        $idx = $inputData['idx'] ?? null;

        if( empty($idx) ){
            throw new Exception('그룹핑 데이터가 없습니다.');
        }

        $productGroupingData = ProductGroupingModel::find($idx);

        if( empty($productGroupingData) ){
            throw new Exception('그룹핑 데이터가 없습니다.');
        }

        $productGroupingData = $productGroupingData->toArray();

        $public = $inputData['public'] ?? '공개';
        $pg_subject = $inputData['pg_subject'] ?? null;

        if( empty($pg_subject) ){
            throw new Exception('그룹핑 제목을 입력해주세요.');
        }

        $pg_state = $inputData['pg_state'] ?? '진행';
        $pg_memo = $inputData['pg_memo'] ?? '';
        $pg_prd_memo = $inputData['pg_prd_memo'] ?? [];

        $data_json = json_decode($productGroupingData['data'] ?? '[]', true);
        if( !is_array($data_json) ){
            $data_json = [];
        }

        foreach($data_json as $key => &$value){

            $value['memo'] = $pg_prd_memo[$key] ?? '';

        }
        unset($value);

        $data_json = json_encode($data_json, JSON_UNESCAPED_UNICODE);

        $updateData = [
            'pg_subject' => $pg_subject,
            'public' => $public,
            'pg_state' => $pg_state,
            'pg_memo' => $pg_memo,
            'data' => $data_json,
        ];

        $result = ProductGroupingModel::where('idx', $idx)->update($updateData);

        return [
            'result' => $result,
            'idx' => $idx,
        ];

    }


}