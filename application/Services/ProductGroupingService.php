<?php

namespace App\Services;

use Exception;
use App\Auth\AdminAuth;
use App\Core\AuthAdmin;
use App\Models\ProductGroupingModel;
use App\Models\ProductModel;
use App\Models\ProductPartnerModel;
use App\Services\ProductPartnerService;
use App\Models\ProductStockModel;

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

        $prd_mode = trim((string)($criteria['prd_mode'] ?? ''));
        $pg_mode = trim((string)($criteria['pg_mode'] ?? ''));
        $pg_state = trim((string)($criteria['pg_state'] ?? ''));
        $paging = $criteria['paging'] ?? true;
        $perPage = $criteria['per_page'] ?? 100;
        $page = $criteria['page'] ?? 1;

        $isAllValue = static function ($value): bool {
            $normalized = trim((string)$value);
            $normalizedLower = strtolower($normalized);
            return $normalized === '' || $normalizedLower === 'all' || $normalized === '전체';
        };

        $query = ProductGroupingModel::query()
            ->when(!$isAllValue($prd_mode), function ($query) use ($prd_mode) {
                $query->where('prd_mode', $prd_mode);
            })
            ->when(!$isAllValue($pg_mode), function ($query) use ($pg_mode) {
                $query->where('pg_mode', $pg_mode);
            })
            ->when(!$isAllValue($pg_state), function ($query) use ($pg_state) {
                $query->where('pg_state', $pg_state);
            })
            ->orderBy('idx', 'DESC');

        $productGroupingList = $paging ? $query->paginate($perPage, $page) : $query->get()->toArray();

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
            ->select('idx', 'pg_subject','public','pg_mode','prd_mode')
            ->when($pg_state !== null && $pg_state !== '', function ($query) use ($pg_state) {
                $query->where('pg_state', $pg_state);
            })
            ->when($prd_mode !== null && $prd_mode !== '', function ($query) use ($prd_mode) {
                $query->where('prd_mode', $prd_mode);
            })
            ->orderBy('idx', 'desc')
            ->get()
            ->toArray();
        
        foreach($query as $key => &$item){
            $item['pg_mode_text'] = $this->mode_text[$item['pg_mode']] ?? "";
            $item['prd_mode_text'] = $this->prd_mode_text[$item['prd_mode']] ?? "";
        }
        unset($item);

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
        if (!is_array($decodedData)) {
            $decodedData = [];
        }
        $query['data'] = $decodedData;
        $query['prd_count'] = is_array($decodedData) ? count($decodedData) : 0;

        $query['prd_data'] = [];
        $prd_idxs = array_column($decodedData, 'idx');

        if( $query['prd_mode'] == 'prdDB' ){

            $productService = new ProductService();
            $query['prd_data'] = $productService->getProductWhereInIdx($prd_idxs);

        }elseif( $query['prd_mode'] == 'provider' ){

            $productPartnerService = new ProductPartnerService();
            $query['prd_data'] = $productPartnerService->getProductPartnerWhereInIdx($prd_idxs);

        }

        // 조회 결과를 idx 기준으로 매핑한다.
        $prdDataMap = [];
        foreach ($query['prd_data'] as $item) {
            $mapKey = (string)($item['CD_IDX'] ?? ($item['idx'] ?? ''));
            if ($mapKey !== '') {
                $prdDataMap[$mapKey] = $item;
            }
        }

        // data 각 원소 안에 prd_data를 주입하고, memo_work도 같이 맞춘다.
        foreach ($query['data'] as &$row) {
            $rowIdx = (string)($row['idx'] ?? '');
            $matchedPrdData = $rowIdx !== '' ? ($prdDataMap[$rowIdx] ?? []) : [];
            $matchedPrdData['memo_work'] = $row['memo'] ?? '';
            $row['prd_data'] = $matchedPrdData;
        }
        unset($row);

        // 기존 뷰 호환을 위해 상단 prd_data도 memo_work를 채운 형태로 유지한다.
        /*
        @deprecated
        foreach ($query['prd_data'] as &$item) {
            $itemIdx = (string)($item['CD_IDX'] ?? ($item['idx'] ?? ''));
            $sourceRow = $itemIdx !== '' ? ($query['data'][array_search($itemIdx, array_map(function($r){
                return (string)($r['idx'] ?? '');
            }, $query['data']), true)] ?? []) : [];
            $item['memo_work'] = $sourceRow['memo'] ?? '';
        }
        unset($item);
        */

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

        if( $mode == 'product_db' ){
            $prd_mode = 'prdDB';
        }else if( $mode == 'product_stock' ){
            $prd_mode = 'prdDB';
        }else{
            $prd_mode = $mode;
        }

        $idx = $criteria['idx'] ?? null;
        $prd_idxs = $criteria['prd_idxs'] ?? [];
        if (!is_array($prd_idxs)) {
            $prd_idxs = is_string($prd_idxs) ? explode(',', $prd_idxs) : [];
        }
        $prd_idxs = array_values(array_filter($prd_idxs, static fn($v) => $v !== null && $v !== ''));

        if (empty($prd_idxs)) {
            throw new Exception('추가할 신규 상품이 없습니다.');
        }

        //dd($prd_idxs);

        $data_array = [];

        //신규 그룹핑 생성
        if( empty($idx) ){

            $pg_subject = $criteria['pg_subject'] ?? null;
            $public = $criteria['public'] ?? '공개';
            $pg_mode = $criteria['pg_mode'] ??  'op'; //운영
            $prd_mode = $prd_mode;
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

            $data_array = $this->buildGroupingRows($prd_mode, $prd_idxs);

            $data_json = json_encode($data_array, JSON_UNESCAPED_UNICODE);

            //dd($data_array);

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


            $data_array = $this->buildGroupingRows($prd_mode, $prd_idxs);

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

        $pg_mode = $inputData['pg_mode'] ?? 'op';
        $prd_mode = $inputData['prd_mode'] ?? 'prdDB';
        $public = $inputData['public'] ?? '공개';
        $pg_subject = $inputData['pg_subject'] ?? null;
        
        $pg_sday = $inputData['pg_sday'] ?? null;
        $pg_day = $inputData['pg_day'] ?? null;
        $pg_sday = ($pg_sday === null || $pg_sday === '') ? '0000-00-00' : $pg_sday;
        $pg_day = ($pg_day === null || $pg_day === '') ? '0000-00-00' : $pg_day;

        $prd_idx = $inputData['prd_idx'] ?? [];

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

        // 전달된 prd_idx 순서대로 data_json을 재정렬하고,
        // prd_idx에 없는 기존 row는 제거한다.
        $hasPrdIdxInput = array_key_exists('prd_idx', $inputData);
        if (!is_array($prd_idx)) {
            $prd_idx = is_string($prd_idx) ? explode(',', $prd_idx) : [];
        }
        $prd_idx = array_values(array_filter(array_map('trim', array_map('strval', $prd_idx)), static function($v){
            return $v !== '';
        }));

        if ($hasPrdIdxInput) {
            $dataMap = [];
            foreach ($data_json as $row) {
                $rowIdx = (string)($row['idx'] ?? '');
                if ($rowIdx !== '' && !isset($dataMap[$rowIdx])) {
                    $dataMap[$rowIdx] = $row;
                }
            }

            $reordered = [];
            foreach ($prd_idx as $rowIdx) {
                if (isset($dataMap[$rowIdx])) {
                    $reordered[] = $dataMap[$rowIdx];
                    unset($dataMap[$rowIdx]); // 중복 idx 입력 방지
                }
            }

            $data_json = $reordered;
        }

        $isEventMode = ($pg_mode === 'event');
        $isSaleMode = ($pg_mode === 'sale');
        $isPeriodMode = ($pg_mode === 'period');
        $isDiscountMode = $isEventMode || $isSaleMode || $isPeriodMode;
        $isEmptyDate = static function($date): bool {
            return $date === 0 || $date === '0' || $date === '0000-00-00' || empty($date);
        };

        if ($isPeriodMode && $isEmptyDate($pg_sday)) {
            throw new Exception('진행 시작일을 입력해주세요.');
        }
        if ($isPeriodMode && $isEmptyDate($pg_day)) {
            throw new Exception('진행 종료일을 입력해주세요.');
        }

        if (($isSaleMode || $isEventMode) && $isEmptyDate($pg_day)) {
            throw new Exception('진행일을 입력해주세요.');
        }

        foreach($data_json as $key => &$value){

            $value['memo'] = $pg_prd_memo[$key] ?? '';

            // 상품 DB일 경우
            if( $prd_mode == 'prdDB' ){
                
                if( $pg_state == '마감' ){

                    $_ps_idx = $value['stockidx'] ?? null;

                    // ps_idx가 비어있으면 건너뛰기
                    if (empty($_ps_idx) || $_ps_idx === null) {
                        continue;
                    }

                    $ps_data = ProductStockModel::find($_ps_idx);

                    if( empty($ps_data) ){
                        continue;
                    }

                    $ps_data = $ps_data->toArray();

                    $ps_sale_log_data = json_decode($ps_data['ps_sale_log'] ?? '[]', true);
                    if( !is_array($ps_sale_log_data) ){
                        $ps_sale_log_data = [];
                    }

                    $_is_log = true;
                    foreach($ps_sale_log_data as $sale_log){
                        if (!is_array($sale_log)) {
                            continue;
                        }

                        //등록된 로그가 있는지?
                        $logGroupingIdx = $sale_log['grouping_idx'] ?? null;
                        $logSalePer = $sale_log['sale_per'] ?? null;
                        $currentSalePer = $inputData['pg_prd_per'][$key] ?? null;
                        if ((string)$logGroupingIdx === (string)$idx && (string)$logSalePer === (string)$currentSalePer) {
                            $_is_log = false;
                            break;
                        }
                    }

                    $_ps_sale_date_old = $ps_data['ps_sale_date'] ?? '0000-00-00';
                    if( !empty($_ps_sale_date_old) && $_ps_sale_date_old != '0000-00-00' && $_ps_sale_date_old > $pg_day ){
                        $ps_sale_date = $_ps_sale_date_old;
                    }else{
                        $ps_sale_date = $pg_day;
                    }

                    $isValidPgDay = !empty($pg_day) && $pg_day !== '0000-00-00';
                    $isValidPgSday = !empty($pg_sday) && $pg_sday !== '0000-00-00';

                    //일일할인 일경우
                    if( $pg_mode == "sale" ){

                        $sale_mode = "day";

                        if ($isValidPgDay) {
                            $ps_in_sale_s = date("Y-m-d",strtotime($pg_day))." 17:00:00";
                            $ps_in_sale_e = date("Y-m-d",strtotime($pg_day." +1 days"))." 17:00:00";
                        } else {
                            $ps_in_sale_s = null;
                            $ps_in_sale_e = null;
                        }

                    //기간할인 일경우
                    }elseif( $pg_mode == "period" ){

                        $sale_mode = "period";

                        if ($isValidPgSday && $isValidPgDay) {
                            $ps_in_sale_s = date("Y-m-d",strtotime($pg_sday))." 17:00:00";
                            $ps_in_sale_e = date("Y-m-d",strtotime($pg_day))." 17:00:00";
                        } else {
                            $ps_in_sale_s = null;
                            $ps_in_sale_e = null;
                        }

                    }else{

                        $sale_mode = "";
                        $ps_in_sale_s = null;
                        $ps_in_sale_e = null;

                    }

                    //기간 period 데이 day
                    $sale_log_unit = [
                        "sale_mode" => $sale_mode ?? "",
                        "grouping_idx" => $idx,
                        "pg_subject" => $pg_subject ?? "",
                        "pg_sday" => $pg_sday,
                        "pg_day" => $pg_day,
                        "sale_per" => $inputData['pg_prd_per'][$key] ?? 0,
                        "original_price" => $inputData['original_sale_price'][$key] ?? 0,
                        "sale_price" => $inputData['dis_sale_price'][$key] ?? 0,
                        "margin_price" => $inputData['dis_margin_price'][$key] ?? 0,
                        "margin_per" => $inputData['dis_margin_per'][$key] ?? 0,
                        "d" => AuthAdmin::getConnectionInfo()
                    ];

                    if( $_is_log == true ){
                        if(is_array($ps_sale_log_data)){
                            array_unshift($ps_sale_log_data, $sale_log_unit);
                        }else{
                            $ps_sale_log_data = array($sale_log_unit);
                        }

                    }

                    $ps_sale_log = json_encode($ps_sale_log_data, JSON_UNESCAPED_UNICODE);
                    $ps_in_sale_data = json_encode($sale_log_unit, JSON_UNESCAPED_UNICODE);

                    $updateData = [
                        'ps_sale_date' => $ps_sale_date,
                        'ps_sale_log' => $ps_sale_log,
                        'ps_in_sale_s' => $ps_in_sale_s,
                        'ps_in_sale_e' => $ps_in_sale_e,
                        'ps_in_sale_data' => $ps_in_sale_data
                    ];

                    $result = ProductStockModel::where('ps_idx', $_ps_idx)->update($updateData);
                
                }

                // 기획전, 데이할인, 기간할인일 경우
                if ($isDiscountMode) {

                    $value['mode_data'] = [
                        'per' => $inputData['pg_prd_per'][$key] ?? 0,
                        'sale_price' => $inputData['dis_sale_price'][$key] ?? 0,
                        'margin_price' => $inputData['dis_margin_price'][$key] ?? 0,
                        'margin_per' => $inputData['dis_margin_per'][$key] ?? 0,
                    ];

                }

            }

        }
        unset($value);

        $data_json = json_encode($data_json, JSON_UNESCAPED_UNICODE);

        $updateData = [
            'pg_subject' => $pg_subject,
            'public' => $public,
            'pg_sday' => $pg_sday,
            'pg_day' => $pg_day,
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