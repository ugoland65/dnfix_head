<?php

namespace App\Services;

use Exception;
use App\Models\OrderSheetModel;
use App\Models\CalendarModel;
use App\Models\AdminModel;
use App\Core\AuthAdmin;
use App\Classes\UploadedFile;
use App\Services\AdminActionLogService;
use App\Models\OrderGroupProductModel;
use App\Models\ProductModel;
use App\Models\ProductStockModel;
use App\Models\ProductStockUnitModel;
use App\Models\InspectionProcessLogModel;
use App\Models\ProductLabelModel;
use App\Models\ProductLabelMappingModel;

class OrderSheetService
{

    /**
     * 주문서 메인(레거시 skin.order_sheet.php 대체) 데이터 조회
     *
     * @param int $idx
     * @return array
     */
    public function getOrderSheetMainPageData(int $idx): array
    {
        $stateTextMap = [
            1 => '작성중',
            2 => '주문전송',
            4 => '입금완료',
            5 => '입고완료',
            7 => '주문종료',
        ];

        $orderSheet = [];
        if ($idx > 0) {
            $row = OrderSheetModel::query()
                ->from('ona_order as A')
                ->leftJoin('ona_order_group as B', 'B.oog_idx', '=', 'A.oo_form_idx')
                ->where('A.oo_idx', '=', $idx)
                ->select(['A.*', 'B.oog_name'])
                ->first();
            $orderSheet = $row ? $row->toArray() : [];
        }

        if (!is_array($orderSheet)) {
            $orderSheet = [];
        }

        $stockState = json_decode((string)($orderSheet['oo_stock'] ?? '{}'), true);
        if (!is_array($stockState)) {
            $stockState = [];
        }

        return [
            'orderSheetMain' => $orderSheet,
            'orderSheetStockState' => $stockState,
            'orderSheetStateTextMap' => $stateTextMap,
        ];
    }


    /**
     * 주문서 상세(레거시 skin.order_sheet_detail.php 대체) 데이터 조회
     *
     * @param array $data
     * @return array
     */
    public function getOrderSheetDetailData(array $data): array
    {
        $idx = (int)($data['idx'] ?? 0);
        if ($idx <= 0) {
            throw new Exception('주문서 번호가 없습니다.');
        }

        $requestedFormView = trim((string)($data['form_view'] ?? 'show'));
        if ($requestedFormView === '') {
            $requestedFormView = 'show';
        }

        $orderSheet = OrderSheetModel::query()
            ->from('ona_order as A')
            ->leftJoin('ona_order_group as B', 'B.oog_idx', '=', 'A.oo_form_idx')
            ->where('A.oo_idx', '=', $idx)
            ->select(['A.*', 'B.oog_name', 'B.oog_brand'])
            ->first();
        if (!$orderSheet) {
            throw new Exception('주문서 정보를 찾을 수 없습니다.');
        }
        $orderSheet = $orderSheet->toArray();

        $orderSecJson = json_decode((string)($orderSheet['oo_json'] ?? '[]'), true);
        if (!is_array($orderSecJson)) {
            $orderSecJson = [];
        }

        $orderSecData = [];
        foreach ($orderSecJson as $row) {
            if (!is_array($row)) {
                continue;
            }
            $bidx = (string)($row['bidx'] ?? '');
            if ($bidx === '') {
                continue;
            }

            $item = (float)($row['item'] ?? 0);
            $qty = (float)($row['qty'] ?? 0);
            $price = (float)($row['price'] ?? 0);
            $weight = (float)($row['weight'] ?? 0);

            $falseCount = (float)($row['false'] ?? 0);
            if ($falseCount > 0) {
                $item -= $falseCount;
                $qty -= (float)($row['false_sum_qty'] ?? 0);
                $price -= (float)($row['false_sum_price'] ?? 0);
                $weight -= (float)($row['false_sum_weight'] ?? 0);
            }

            $orderSecData[$bidx] = [
                'item' => $item,
                'qty' => $qty,
                'price' => $price,
                'weight' => $weight,
                'false' => (int)$falseCount,
            ];
        }

        $oogBrand = json_decode((string)($orderSheet['oog_brand'] ?? '[]'), true);
        if (!is_array($oogBrand)) {
            $oogBrand = [];
        }

        $oopIdxList = [];
        foreach ($oogBrand as $brandRow) {
            if (!is_array($brandRow)) {
                continue;
            }
            $oopIdx = (string)($brandRow['oop_idx'] ?? '');
            if ($oopIdx !== '') {
                $oopIdxList[] = $oopIdx;
            }
        }
        $oopIdxList = array_values(array_unique($oopIdxList));

        $groupProductMap = [];
        if (!empty($oopIdxList)) {
            $groupProducts = OrderGroupProductModel::query()
                ->select(['oop_idx', 'oop_data'])
                ->whereIn('oop_idx', $oopIdxList)
                ->get()
                ->toArray();
            foreach ($groupProducts as $row) {
                $oopIdx = (string)($row['oop_idx'] ?? '');
                if ($oopIdx === '') {
                    continue;
                }
                $oopData = (string)($row['oop_data'] ?? '');
                if ($oopData !== '' && substr($oopData, 0, 1) !== '[') {
                    $oopData = '[' . $oopData . ']';
                }
                $decoded = json_decode($oopData !== '' ? $oopData : '[]', true);
                if (!is_array($decoded)) {
                    $decoded = [];
                }
                $groupProductMap[$oopIdx] = $decoded;
            }
        }

        $groupSideRows = [];
        foreach ($oogBrand as $brandRow) {
            if (!is_array($brandRow)) {
                continue;
            }

            $oopIdx = (string)($brandRow['oop_idx'] ?? '');
            if ($oopIdx === '') {
                continue;
            }

            $sec = $orderSecData[$oopIdx] ?? [];
            $item = (float)($sec['item'] ?? 0);
            $qty = (float)($sec['qty'] ?? 0);
            $price = (float)($sec['price'] ?? 0);
            $weight = (float)($sec['weight'] ?? 0);
            $falseCount = (int)($sec['false'] ?? 0);

            $showWeight = '';
            if ($weight > 0) {
                if ($weight > 1000) {
                    $showWeight = number_format($weight * 0.001, 2) . 'kg';
                } else {
                    $showWeight = number_format($weight) . 'g';
                }
            }

            $groupSideRows[] = [
                'oop_idx' => $oopIdx,
                'name' => (string)($brandRow['name'] ?? ''),
                'item' => $item,
                'qty' => $qty,
                'price' => $price,
                'false' => $falseCount,
                'show_weight' => $showWeight,
                'oop_total_count' => count($groupProductMap[$oopIdx] ?? []),
                'has_order' => $qty > 0,
            ];
        }

        $formView = $requestedFormView;
        if (in_array((int)($orderSheet['oo_state'] ?? 0), [4, 5, 7], true)) {
            $formView = 'hidden';
        }

        return [
            'idx' => $idx,
            'open_oop_idx' => (string)($data['open_oop_idx'] ?? ''),
            'form_view' => $formView,
            'orderSheetMain' => $orderSheet,
            'groupSideRows' => $groupSideRows,
        ];
    }

    /**
     * 주문서 목록 조회
     * @param array $criteria
     * @return array
     */
    public function getOrderSheetList($criteria)
    {

        $page = (int)($criteria['page'] ?? ($criteria['pn'] ?? 1));
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int)($criteria['per_page'] ?? 100);
        if ($perPage < 1) {
            $perPage = 100;
        }

        $ooImport = (string)($criteria['oo_import'] ?? 'all');
        $ooState = (string)($criteria['oo_state'] ?? 'ing');
        $ooFormIdx = (int)($criteria['oo_form_idx'] ?? 0);
        $searchValue = trim((string)($criteria['search_value'] ?? ''));

        $query = OrderSheetModel::query()
            ->select(['ona_order.*', 'ona_order_group.oog_name'])
            ->leftJoin('ona_order_group', 'ona_order_group.oog_idx', '=', 'ona_order.oo_form_idx')
            ->orderBy('ona_order.oo_idx', 'desc');

        if ($ooImport === '수입') {
            $query->whereRaw("ona_order.oo_import IN ('수입','구매대행')");
        } elseif ($ooImport === '국내') {
            $query->where('ona_order.oo_import', '=', '국내');
        }

        if( !empty($ooState) ){
            if ($ooState == 'ing') {
                $query->whereRaw("ona_order.oo_state IN ('1','2','4','5')");
            } else if ($ooState == 'all') {
            } else {
                $query->where('ona_order.oo_state', '=', $ooState);
            }
        }

        if ($ooFormIdx > 0) {
            $query->where('ona_order.oo_form_idx', '=', $ooFormIdx);
        }

        if ($searchValue !== '') {
            $searchEscaped = addslashes($searchValue);
            $query->whereRaw(
                "(INSTR(ona_order.oo_name, '{$searchEscaped}') > 0
                OR INSTR(ona_order.oo_express_data, '{$searchEscaped}') > 0
                OR INSTR(ona_order.oo_tex_data, '{$searchEscaped}') > 0)"
            );
        }

        // 목록 검색 조건과 무관하게, 요약은 전체 DB 기준으로 별도 집계한다.
        $summaryRows = OrderSheetModel::query()
            ->selectRaw("
                ona_order.oo_state,
                CASE
                    WHEN ona_order.oo_import = '국내' THEN 'domestic'
                    ELSE 'import'
                END AS import_type,
                COUNT(*) AS order_count,
                COALESCE(SUM(
                    CASE
                        WHEN ona_order.oo_state IN ('1', '2') THEN
                            CASE
                                WHEN ona_order.oo_import = '국내' THEN ona_order.oo_sum_price
                                WHEN ona_order.oo_prd_currency IN ('엔', 'JPY', 'jpy')
                                    THEN ona_order.oo_sum_price * (COALESCE(ona_order.oo_prd_exchange_rate, 0) / 100)
                                ELSE ona_order.oo_sum_price * COALESCE(ona_order.oo_prd_exchange_rate, 0)
                            END
                        ELSE COALESCE(ona_order.oo_price_kr, 0)
                    END
                ), 0) AS pay_sum
            ")
            ->whereRaw("ona_order.oo_state IN ('1','2','4','5')")
            ->groupBy('ona_order.oo_state', 'import_type')
            ->get()
            ->toArray();

        $emptySplitSummary = [
            'domestic' => ['label' => '국내', 'count' => 0, 'pay_sum' => 0],
            'import' => ['label' => '수입', 'count' => 0, 'pay_sum' => 0],
        ];
        $summary = [
            'total' => [
                'count' => 0,
                'pay_sum' => 0,
                'split' => $emptySplitSummary,
            ],
            'states' => [
                '1' => ['label' => '작성중', 'count' => 0, 'pay_sum' => 0, 'split' => $emptySplitSummary],
                '2' => ['label' => '주문전송', 'count' => 0, 'pay_sum' => 0, 'split' => $emptySplitSummary],
                '4' => ['label' => '입금완료', 'count' => 0, 'pay_sum' => 0, 'split' => $emptySplitSummary],
                '5' => ['label' => '입고완료', 'count' => 0, 'pay_sum' => 0, 'split' => $emptySplitSummary],
            ],
        ];

        foreach ($summaryRows as $summaryRow) {
            $state = (string)($summaryRow['oo_state'] ?? '');
            $importType = (string)($summaryRow['import_type'] ?? 'import');
            if (!isset($summary['states'][$state])) {
                continue;
            }
            if (!isset($summary['states'][$state]['split'][$importType])) {
                $importType = 'import';
            }

            $count = (int)($summaryRow['order_count'] ?? 0);
            $paySum = (int)($summaryRow['pay_sum'] ?? 0);

            $summary['states'][$state]['count'] += $count;
            $summary['states'][$state]['pay_sum'] += $paySum;
            $summary['states'][$state]['split'][$importType]['count'] += $count;
            $summary['states'][$state]['split'][$importType]['pay_sum'] += $paySum;
            $summary['total']['count'] += $count;
            $summary['total']['pay_sum'] += $paySum;
            $summary['total']['split'][$importType]['count'] += $count;
            $summary['total']['split'][$importType]['pay_sum'] += $paySum;
        }

        $result = $query->paginate($perPage, $page);

        $stateTextMap = [
            1 => '작성중',
            2 => '주문전송',
            4 => '입금완료',
            5 => '입고완료',
            7 => '주문종료',
        ];

        foreach ($result['data'] as &$row) {
            $row['oo_price_data'] = json_decode($row['oo_price_data'] ?? '{}', true);
            if (!is_array($row['oo_price_data'])) {
                $row['oo_price_data'] = [];
            }

            /*
            $reg = json_decode($row['reg'] ?? '{}', true);
            if (!is_array($reg)) {
                $reg = [];
            }

            $showDate = '';
            $regDate = (string)($reg['reg']['date'] ?? '');
            $regName = (string)($reg['reg']['name'] ?? '');
            if ($regDate !== '') {
                $showDate = date('y.m.d H:i', strtotime($regDate)) . '<br>(' . $regName . ')';
            } else {
                $ooDate = (int)($row['oo_date'] ?? 0);
                if ($ooDate > 0) {
                    $showDate = date('Y.m.d H:i', $ooDate);
                }
            }
            $row['show_date'] = $showDate;
            */

            $state = (int)($row['oo_state'] ?? 0);
            if ($state === 2) {
                $row['tr_class'] = 'tr-2';
            } elseif ($state === 4) {
                $row['tr_class'] = 'tr-4';
            } elseif ($state === 7) {
                $row['tr_class'] = 'status_end';
            } else {
                $row['tr_class'] = 'tr-normal';
            }
            $row['oo_state_text'] = $stateTextMap[$state] ?? '';
        }
        unset($row);

        $result['summary'] = $summary;

        return $result;
    }


    /*
     * 주문서 정보 조회
     * @param int $idx
     * @return array
     */
    public function getOrderSheetInfo($idx)
    {

        $query = OrderSheetModel::find($idx);
        if (!$query) {
            throw new Exception("주문서 정보를 찾을 수 없습니다.");
        }

        $result = $query->toArray();

        $admin_query = AdminModel::query()
            ->select(['idx', 'ad_id', 'ad_name', 'ad_nick'])
            ->get()
            ->toArray();

        $adminMap = [];
        foreach ($admin_query as $row) {
            $adminMap[$row['ad_id']] = $row['ad_name'] ?? '';
        }

        $result['oo_price_data'] = json_decode($result['oo_price_data'] ?? '{}', true);
        $result['oo_date_data'] = json_decode($result['oo_date_data'] ?? '{}', true);
        $result['oo_upload_file'] = json_decode($result['oo_upload_file'] ?? '{}', true);
        $result['oo_express_data'] = json_decode($result['oo_express_data'] ?? '{}', true);
        $result['oo_approval_date'] = json_decode($result['oo_approval_date'] ?? '{}', true);
        $result['oo_tex_data'] = json_decode($result['oo_tex_data'] ?? '{}', true);
        $result['oo_json'] = json_decode($result['oo_json'] ?? '{}', true);
        $result['reg'] = json_decode($result['reg'] ?? '{}', true);
        $result['oo_stock'] = json_decode($result['oo_stock'] ?? '{}', true);

        if (!is_array($result['oo_upload_file'])) {
            $result['oo_upload_file'] = [];
        }
        if (!isset($result['oo_upload_file']['invoice']) || !is_array($result['oo_upload_file']['invoice'])) {
            $result['oo_upload_file']['invoice'] = [];
        }
        if (!isset($result['oo_upload_file']['import_declaration']) || !is_array($result['oo_upload_file']['import_declaration'])) {
            $result['oo_upload_file']['import_declaration'] = [];
        }

        // 결제 파일은 레거시(pay_file)와 신규(pay) 키를 모두 호환한다.
        $payFiles = [];
        if (isset($result['oo_upload_file']['pay']) && is_array($result['oo_upload_file']['pay'])) {
            $payFiles = $result['oo_upload_file']['pay'];
        }
        if (isset($result['oo_upload_file']['pay_file']) && is_array($result['oo_upload_file']['pay_file'])) {
            $payFiles = array_merge($payFiles, $result['oo_upload_file']['pay_file']);
        }
        if (!empty($payFiles)) {
            $seen = [];
            $deduped = [];
            foreach ($payFiles as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $dedupeKey = ($row['name'] ?? '') . '|' . ($row['date'] ?? '');
                if ($dedupeKey !== '|' && isset($seen[$dedupeKey])) {
                    continue;
                }
                $seen[$dedupeKey] = true;
                $deduped[] = $row;
            }
            $payFiles = $deduped;
        }
        $result['oo_upload_file']['pay'] = $payFiles;
        $result['oo_upload_file']['pay_file'] = $payFiles;

        //주문 관련 파일
        foreach( $result['oo_upload_file']['invoice'] as &$invoice ){
            $invoice['reg_name'] = $adminMap[$invoice['id']] ?? '';
        }
        unset($invoice);

        //결제 관련 파일
        foreach( $result['oo_upload_file']['pay'] as &$pay ){
            $pay['reg_name'] = $adminMap[$pay['id']] ?? '';
        }
        unset($pay);

        foreach( $result['oo_upload_file']['import_declaration'] as &$import_declaration ){
            $import_declaration['reg_name'] = $adminMap[$import_declaration['id']] ?? '';
        }
        unset($import_declaration);



        return $result;
    }

    /**
     * 주문서 재고 일괄등록 팝업 데이터 조회
     *
     * @param int $idx
     * @return array
     */
    public function getOrderSheetStockPopupData(int $idx): array
    {

        if ($idx <= 0) {
            throw new Exception('주문서 번호가 없습니다.');
        }

        $orderSheet = OrderSheetModel::query()
            ->select(['oo_idx', 'oo_name', 'oo_json', 'oo_stock'])
            ->where('oo_idx', '=', $idx)
            ->first();
        if (!$orderSheet) {
            throw new Exception('주문서 정보를 찾을 수 없습니다.');
        }
        $orderSheet = $orderSheet->toArray();

        $orderJson = json_decode((string)($orderSheet['oo_json'] ?? '[]'), true);
        if (!is_array($orderJson)) {
            $orderJson = [];
        }
        $stockState = json_decode((string)($orderSheet['oo_stock'] ?? '{}'), true);
        if (!is_array($stockState)) {
            $stockState = [];
        }

        $selpdRows = [];
        foreach ($orderJson as $groupRow) {
            if (!is_array($groupRow)) {
                continue;
            }
            $selpd = $groupRow['selpd'] ?? [];
            if (!is_array($selpd)) {
                continue;
            }

            foreach ($selpd as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $pidx = (int)($row['pidx'] ?? 0);
                if ($pidx <= 0) {
                    continue;
                }
                $selpdRows[] = [
                    'pidx' => $pidx,
                    'qty' => (int)($row['qty'] ?? 0),
                    'is_false' => !empty($row['false']),
                ];
            }
        }

        $pidxList = array_values(array_unique(array_map(static function ($row) {
            return (int)($row['pidx'] ?? 0);
        }, $selpdRows)));
        $pidxList = array_values(array_filter($pidxList, static function ($v) {
            return $v > 0;
        }));

        $productMap = [];
        if (!empty($pidxList)) {
            $productRows = ProductModel::query()
                ->from('COMPARISON_DB as A')
                ->leftJoin('prd_stock as B', 'B.ps_prd_idx', '=', 'A.CD_IDX')
                ->leftJoin('BRAND_DB as C', 'C.BD_IDX', '=', 'A.CD_BRAND_IDX')
                ->whereIn('A.CD_IDX', $pidxList)
                ->select([
                    'A.CD_IDX',
                    'A.CD_KIND_CODE',
                    'A.CD_NAME',
                    'A.CD_IMG',
                    'A.CD_CODE',
                    'A.CD_SIZE2',
                    'A.cd_hbti',
                    'A.cd_sale_price',
                    'A.cd_cost_price',
                    'A.cd_godo_code',
                    'A.is_discontinued',
                    'A.cd_weight_fn',
                    'B.ps_idx',
                    'B.ps_stock',
                    'B.ps_in_date',
                    'B.is_sale_month',
                    'B.is_sale_special',
                    'C.BD_NAME',
                ])
                ->get()
                ->toArray();

            foreach ($productRows as $row) {
                $productMap[(int)($row['CD_IDX'] ?? 0)] = $row;
            }
        }

        $stockProcessedByPsIdx = [];
        $stockProcessTokensByPsIdx = [];
        foreach ($productMap as $productRow) {
            $psIdx = (int)($productRow['ps_idx'] ?? 0);
            if ($psIdx <= 0) {
                continue;
            }
            $stockProcessTokensByPsIdx[$psIdx] = 'order_sheet_godo_stock:' . $idx . ':' . $psIdx;
        }
        if (!empty($stockProcessTokensByPsIdx)) {
            try {
                $processedStockRows = ProductStockUnitModel::query()
                    ->select(['psu_stock_idx', 'psu_token', 'psu_date', 'psu_id'])
                    ->whereIn('psu_token', array_values($stockProcessTokensByPsIdx))
                    ->get()
                    ->toArray();
                foreach ($processedStockRows as $processedStockRow) {
                    $psIdx = (int)($processedStockRow['psu_stock_idx'] ?? 0);
                    if ($psIdx <= 0) {
                        continue;
                    }
                    $processedTimestamp = (int)($processedStockRow['psu_date'] ?? 0);
                    $stockProcessedByPsIdx[$psIdx] = [
                        'processed_at' => $processedTimestamp > 0 ? date('Y-m-d H:i:s', $processedTimestamp) : '',
                        'processed_by' => (string)($processedStockRow['psu_id'] ?? ''),
                    ];
                }
            } catch (\Throwable $e) {
                $stockProcessedByPsIdx = [];
            }
        }

        $godoProcessLogByProductIdx = [];
        if (!empty($pidxList)) {
            try {
                $godoProcessLogs = InspectionProcessLogModel::query()
                    ->select([
                        'prd_idx',
                        'executor_admin_id',
                        'executor_admin_name',
                        'executed_at',
                        'result_content',
                    ])
                    ->where('location_code', '=', InspectionProcessLogService::LOCATION_ORDER_SHEET_STOCK_SINGLE)
                    ->where('relation_pk', '=', $idx)
                    ->whereIn('prd_idx', $pidxList)
                    ->orderBy('ipl_idx', 'desc')
                    ->get()
                    ->toArray();

                foreach ($godoProcessLogs as $godoProcessLog) {
                    $productIdx = (int)($godoProcessLog['prd_idx'] ?? 0);
                    if ($productIdx <= 0 || isset($godoProcessLogByProductIdx[$productIdx])) {
                        continue;
                    }
                    $resultContent = json_decode((string)($godoProcessLog['result_content'] ?? '{}'), true);
                    if (!is_array($resultContent)) {
                        $resultContent = [];
                    }
                    $godoProcessLogByProductIdx[$productIdx] = [
                        'status' => !empty($resultContent['success']) ? '처리완료' : '처리기록',
                        'executed_at' => (string)($godoProcessLog['executed_at'] ?? ''),
                        'executor_name' => (string)($godoProcessLog['executor_admin_name'] ?? ''),
                        'executor_id' => (string)($godoProcessLog['executor_admin_id'] ?? ''),
                    ];
                }
            } catch (\Throwable $e) {
                $godoProcessLogByProductIdx = [];
            }
        }

        $godoApiStartAt = microtime(true);
        $godoGoodsMap = [];
        $godoApiErrorMessage = '';
        $godoRestockApiErrorMessage = '';
        $restockCountByGoodsNo = [];
        $stockCodes = [];
        foreach ($productMap as $row) {
            $psIdx = trim((string)($row['ps_idx'] ?? ''));
            if ($psIdx !== '') {
                $stockCodes[] = $psIdx;
            }
        }
        $stockCodes = array_values(array_unique($stockCodes));
        if (!empty($stockCodes)) {
            try {

                $godoApiService = new GodoApiService();
                $godoGoodsRows = $godoApiService->getGodoGoodsInfoByStockCodes(implode(',', $stockCodes), "Y");

                //dump($godoGoodsRows);
                if (!is_array($godoGoodsRows)) {
                    $godoGoodsRows = [];
                }

                foreach ($godoGoodsRows as $godoRow) {
                    if (!is_array($godoRow)) {
                        continue;
                    }
                    $goodsCd = trim((string)($godoRow['goodsCd'] ?? ''));
                    if ($goodsCd === '') {
                        continue;
                    }
                    $godoGoodsMap[$goodsCd] = $godoRow;
                }
            } catch (\Throwable $e) {
                $godoApiErrorMessage = $e->getMessage();
            }
        }

        $goodsNos = [];
        foreach ($godoGoodsMap as $godoRow) {
            if (!is_array($godoRow)) {
                continue;
            }
            $goodsNo = trim((string)($godoRow['goodsNo'] ?? ''));
            if ($goodsNo !== '') {
                $goodsNos[] = $goodsNo;
            }
        }
        $goodsNos = array_values(array_unique($goodsNos));
        if (!empty($goodsNos)) {
            try {
                $godoApiService = $godoApiService ?? new GodoApiService();
                $restockRows = $godoApiService->getGodoGoodsRestockByGoodsNos(implode(',', $goodsNos), 'count');
                if (!is_array($restockRows)) {
                    $restockRows = [];
                }
                $restockCountByGoodsNo = $this->buildGodoRestockCountMap($restockRows);
            } catch (\Throwable $e) {
                $godoRestockApiErrorMessage = $e->getMessage();
            }
        }

        $productService = new ProductService();
        $stockItems = [];
        foreach ($selpdRows as $row) {
            $pidx = (int)($row['pidx'] ?? 0);
            $product = $productMap[$pidx] ?? [];
            if (empty($product)) {
                continue;
            }

            $cdImg = trim((string)($product['CD_IMG'] ?? ''));
            $imgPath = $cdImg !== '' ? ('/data/comparion/' . $cdImg) : '';
            $psIdx = (int)($product['ps_idx'] ?? 0);
            $psIdxKey = (string)$psIdx;
            $cdKindCode = trim((string)($product['CD_KIND_CODE'] ?? ''));
            $cdGodoCode = trim((string)($product['cd_godo_code'] ?? ''));
            $godoGoods = $godoGoodsMap[$psIdxKey] ?? [];
            $godoGoodsNo = trim((string)($godoGoods['goodsNo'] ?? ''));
            $onlyAdultFl = strtolower(trim((string)($godoGoods['onlyAdultFl'] ?? '')));
            $stockFl = strtolower(trim((string)($godoGoods['stockFl'] ?? '')));
            $soldOutFl = strtolower(trim((string)($godoGoods['soldOutFl'] ?? '')));
            $goodsModelNo = trim((string)($godoGoods['goodsModelNo'] ?? ''));
            $goodsPrice = trim((string)($godoGoods['goodsPrice'] ?? ''));
            $costPrice = trim((string)($godoGoods['costPrice'] ?? ''));
            $cdWeightData = json_decode($product['cd_weight_fn'] ?? '{}', true);
            if (!is_array($cdWeightData)) {
                $cdWeightData = [];
            }
            $marginInfo = $productService->calculateMarginInfo(
                (float)($product['cd_sale_price'] ?? 0),
                (float)($product['cd_cost_price'] ?? 0)
            );
            $godoCategoryLines = $this->buildGodoCategoryLines(
                (isset($godoGoods['categories']) && is_array($godoGoods['categories'])) ? $godoGoods['categories'] : []
            );
            $godoStockQty = 0;

            // 고도몰 현재고는 totalStock 컬럼을 우선 기준으로 사용
            if (isset($godoGoods['totalStock']) && is_numeric($godoGoods['totalStock'])) {
                $godoStockQty = (int)$godoGoods['totalStock'];
            } elseif (isset($godoGoods['stockCnt']) && is_numeric($godoGoods['stockCnt'])) {
                $godoStockQty = (int)$godoGoods['stockCnt'];
            } elseif (isset($godoGoods['stock']) && is_numeric($godoGoods['stock'])) {
                $godoStockQty = (int)$godoGoods['stock'];
            } elseif (isset($godoGoods['goodsStock']) && is_numeric($godoGoods['goodsStock'])) {
                $godoStockQty = (int)$godoGoods['goodsStock'];
            }
            $isGodoCodeMatched = ($godoGoodsNo !== '' && $cdGodoCode !== '' && $godoGoodsNo === $cdGodoCode);
            $restockRequestCount = 0;
            if ($godoGoodsNo !== '' && isset($restockCountByGoodsNo[$godoGoodsNo])) {
                $restockRequestCount = (int)$restockCountByGoodsNo[$godoGoodsNo];
            }

            $stockItems[] = [
                'pidx' => $pidx,
                'ps_idx' => $psIdx,
                'qty' => (int)($row['qty'] ?? 0),
                'is_false' => !empty($row['is_false']),
                'cd_kind_code' => $cdKindCode,
                'brand_name' => (string)($product['BD_NAME'] ?? ''),
                'name' => (string)($product['CD_NAME'] ?? ''),
                'barcode' => (string)($product['CD_CODE'] ?? ''),
                'cd_hbti' => (string)($product['cd_hbti'] ?? ''),
                'goods_price' => (string)($product['cd_sale_price'] ?? ''),
                'cost_price' => (string)($product['cd_cost_price'] ?? ''),
                'goods_weight' => (string)($cdWeightData['1'] ?? 0),
                'inner_length' => (string)($product['CD_SIZE2'] ?? ''),
                'margin_per' => (float)($marginInfo['margin_per'] ?? 0),
                'margin_grade' => (string)($marginInfo['margin_grade'] ?? ''),
                'stock_qty' => (int)($product['ps_stock'] ?? 0),
                'ps_in_date' => (string)($product['ps_in_date'] ?? ''),
                'img_path' => $imgPath,
                'cd_godo_code' => $cdGodoCode,
                'godo_goods_no' => $godoGoodsNo,
                'godo_stock_qty' => $godoStockQty,
                'godo_code_matched' => $isGodoCodeMatched,
                'godo_goods_found' => !empty($godoGoods),
                'godo_only_adult_fl' => $onlyAdultFl,
                'godo_stock_fl' => $stockFl,
                'godo_sold_out_fl' => $soldOutFl,
                'godo_goods_model_no' => $goodsModelNo,
                'godo_goods_price' => $goodsPrice,
                'godo_cost_price' => $costPrice,
                'is_sale_month' => (int)($product['is_sale_month'] ?? 0),
                'is_sale_special' => (int)($product['is_sale_special'] ?? 0),
                'is_discontinued' => (int)($product['is_discontinued'] ?? 0),
                'restock_request_count' => $restockRequestCount,
                'godo_category_lines' => $godoCategoryLines,
                'godo_process_log' => $godoProcessLogByProductIdx[$pidx] ?? null,
                'stock_processed' => $stockProcessedByPsIdx[$psIdx] ?? null,
            ];
        }

        $godoInfoLoadedAt = date('Y-m-d H:i:s');
        $godoInfoLoadMs = (int)round((microtime(true) - $godoApiStartAt) * 1000);

        return [
            'orderSheetIdx' => $idx,
            'orderSheetName' => (string)($orderSheet['oo_name'] ?? ''),
            'orderSheetStockState' => $stockState,
            'stockItems' => $stockItems,
            'godoApiErrorMessage' => $godoApiErrorMessage,
            'godoRestockApiErrorMessage' => $godoRestockApiErrorMessage,
            'godoInfoLoadedAt' => $godoInfoLoadedAt,
            'godoInfoLoadMs' => $godoInfoLoadMs,
            'defaultStockDay' => date('Y-m-d'),
            'defaultStockMemo' => ((string)($orderSheet['oo_name'] ?? '') . ' 입고'),
        ];
    }

    

    /**
     * 고도몰 재입고 알림 API 응답에서 goodsNo별 신청 수를 집계한다.
     * 응답 키/필드명이 환경마다 다를 수 있어 다중 키를 허용한다.
     *
     * @param array $restockRows
     * @return array<string,int>
     */
    private function buildGodoRestockCountMap(array $restockRows): array
    {
        $result = [];

        // count 모드 표준 응답: { status, mode, totalCount, goodsCounts:[{goodsNo,count}, ...] }
        if (isset($restockRows['goodsCounts']) && is_array($restockRows['goodsCounts'])) {
            foreach ($restockRows['goodsCounts'] as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $goodsNo = trim((string)($row['goodsNo'] ?? ''));
                if ($goodsNo === '') {
                    continue;
                }
                $result[$goodsNo] = (int)($row['count'] ?? 0);
            }
            return $result;
        }

        foreach ($restockRows as $rowKey => $row) {
            if (!is_array($row)) {
                if (!is_int($rowKey) && preg_match('/^\d+$/', (string)$rowKey) === 1 && is_numeric($row)) {
                    $result[(string)$rowKey] = (int)$row;
                }
                continue;
            }

            $goodsNo = trim((string)($row['goodsNo'] ?? ($row['goods_no'] ?? ($row['goodsno'] ?? ''))));
            if ($goodsNo === '' && !is_int($rowKey) && preg_match('/^\d+$/', (string)$rowKey) === 1) {
                $goodsNo = (string)$rowKey;
            }
            if ($goodsNo === '') {
                continue;
            }

            $count = null;
            $countFieldCandidates = [
                'count',
                'cnt',
                'total',
                'totalCount',
                'request_count',
                'restock_count',
                'reInquiryCnt',
                'alarmCnt',
                'member_count',
            ];
            foreach ($countFieldCandidates as $fieldName) {
                if (isset($row[$fieldName]) && is_numeric($row[$fieldName])) {
                    $count = (int)$row[$fieldName];
                    break;
                }
            }
            if ($count === null) {
                if (isset($row['list']) && is_array($row['list'])) {
                    $count = count($row['list']);
                } else {
                    // 건별 리스트 응답이면 해당 row 자체를 1건으로 집계
                    $count = 1;
                }
            }

            if (!isset($result[$goodsNo])) {
                $result[$goodsNo] = 0;
            }
            $result[$goodsNo] += max($count, 0);
        }

        return $result;
    }

    /**
     * 고도몰 categories 응답을 화면 표기용 카테고리 목록으로 변환한다.
     * 각 항목은 cateCd 기준 오름차순 정렬되며, 카테고리명 경로(line)와 코드(cateCd)를 포함한다.
     * 예) ['line' => '오나홀 > 중량별 > 600g ~ 799g', 'cateCd' => '001002003']
     *
     * @param array $categories
     * @return array<int,array{line:string,cateCd:string}>
     */
    private function buildGodoCategoryLines(array $categories): array
    {
        $lineRows = [];
        $seen = [];

        foreach ($categories as $categoryRow) {
            if (!is_array($categoryRow)) {
                continue;
            }

            $pathRows = (isset($categoryRow['path']) && is_array($categoryRow['path'])) ? $categoryRow['path'] : [];
            $pathNames = [];
            foreach ($pathRows as $pathRow) {
                if (!is_array($pathRow)) {
                    continue;
                }
                $cateNm = trim((string)($pathRow['cateNm'] ?? ''));
                if ($cateNm !== '') {
                    $pathNames[] = $cateNm;
                }
            }

            if (empty($pathNames)) {
                $cateNm = trim((string)($categoryRow['cateNm'] ?? ''));
                if ($cateNm !== '') {
                    $pathNames[] = $cateNm;
                }
            }

            if (empty($pathNames)) {
                continue;
            }

            $line = implode(' > ', $pathNames);
            if (isset($seen[$line])) {
                continue;
            }
            $seen[$line] = true;
            $lineRows[] = [
                'line' => $line,
                'cateCd' => trim((string)($categoryRow['cateCd'] ?? '')),
            ];
        }

        usort($lineRows, static function (array $a, array $b): int {
            $aCateCd = $a['cateCd'] ?? '';
            $bCateCd = $b['cateCd'] ?? '';
            return strcmp((string)$aCateCd, (string)$bCateCd);
        });

        return $lineRows;
    }

    /**
     * 주문서 재고 일괄등록 처리
     * (legacy processing.order_sheet.php의 os_allStock 리팩토링)
     *
     * @param array $requestData
     * @return array
     */
    public function orderSheetAllStock(array $requestData): array
    {
        $orderSheetIdx = (int)($requestData['os_idx'] ?? 0);
        if ($orderSheetIdx <= 0) {
            throw new Exception('주문서 번호가 없습니다.');
        }

        $stockDay = trim((string)($requestData['stock_day'] ?? ''));
        if ($stockDay === '') {
            $stockDay = date('Y-m-d');
        }
        $stockAllMemo = trim((string)($requestData['stock_all_memo'] ?? ''));

        $psIdxRows = $requestData['ps_idx'] ?? [];
        $qtyRows = $requestData['s_qty'] ?? [];
        $memoRows = $requestData['s_memo'] ?? [];
        if (!is_array($psIdxRows)) {
            $psIdxRows = [];
        }
        if (!is_array($qtyRows)) {
            $qtyRows = [];
        }
        if (!is_array($memoRows)) {
            $memoRows = [];
        }

        $sessionIdx = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $sessionId = (string)(AuthAdmin::getSession('sess_id') ?? '');
        $sessionName = (string)(AuthAdmin::getSession('sess_name') ?? '');
        $actionTime = date('Y-m-d H:i:s');
        $actionTimestamp = time();
        $reg = [
            'date' => $actionTime,
            'id' => $sessionId,
            'name' => $sessionName,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'domain' => $_SERVER['SERVER_NAME'] ?? '',
        ];

        $processedCount = 0;
        $processedRows = [];
        $skippedRows = [];
        foreach ($psIdxRows as $i => $psIdxRaw) {
            $psIdx = (int)$psIdxRaw;
            $qty = (int)($qtyRows[$i] ?? 0);
            if ($psIdx <= 0 || $qty <= 0) {
                continue;
            }

            $stockProcessToken = 'order_sheet_godo_stock:' . $orderSheetIdx . ':' . $psIdx;
            $alreadyProcessed = ProductStockUnitModel::query()
                ->where('psu_token', '=', $stockProcessToken)
                ->exists();
            if ($alreadyProcessed) {
                $skippedRows[] = [
                    'ps_idx' => $psIdx,
                    'qty' => $qty,
                    'reason' => '고도몰 단건 처리에서 이미 재고 반영됨',
                ];
                continue;
            }

            $memo = trim((string)($memoRows[$i] ?? ''));
            $lineMemo = $stockAllMemo;
            if ($memo !== '') {
                $lineMemo = $lineMemo !== '' ? ($lineMemo . ' - ' . $memo) : $memo;
            }

            $stockRow = ProductStockModel::query()
                ->select(['ps_stock', 'ps_stock_all'])
                ->where('ps_idx', '=', $psIdx)
                ->first();
            $stockRow = $stockRow ? $stockRow->toArray() : ['ps_stock' => 0, 'ps_stock_all' => 0];
            $afterStock = (int)($stockRow['ps_stock'] ?? 0) + $qty;

            $psIncome = [
                'os_idx' => $orderSheetIdx,
                'qty' => $qty,
                'reg' => $reg,
            ];
            $psLastIn = '( ' . $qty . ' ) ' . $lineMemo;

            ProductStockModel::query()
                ->where('ps_idx', '=', $psIdx)
                ->update([
                    'ps_stock' => $afterStock,
                    'ps_stock_all' => (int)($stockRow['ps_stock_all'] ?? 0) + $qty,
                    'ps_income' => json_encode($psIncome, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'ps_last_in' => $psLastIn,
                    'ps_update_date' => $actionTime,
                    'ps_in_date' => $actionTime,
                ]);

            ProductStockUnitModel::query()
                ->insert([
                    'psu_stock_idx' => $psIdx,
                    'psu_day' => $stockDay,
                    'psu_mode' => 'plus',
                    'psu_qry' => $qty,
                    'psu_stock' => $afterStock,
                    'psu_kind' => '신규입고',
                    'psu_memo' => $lineMemo,
                    'psu_token' => null,
                    'psu_id' => $sessionId,
                    'psu_date' => $actionTimestamp,
                    'reg' => '',
                ]);

            $processedCount++;
            $processedRows[] = [
                'ps_idx' => $psIdx,
                'qty' => $qty,
                'memo' => $lineMemo,
                'after_stock' => $afterStock,
            ];
        }

        $orderSheet = OrderSheetModel::query()
            ->select(['oo_date_data', 'oo_stock'])
            ->where('oo_idx', '=', $orderSheetIdx)
            ->first();
        $orderSheet = $orderSheet ? $orderSheet->toArray() : ['oo_date_data' => '{}', 'oo_stock' => '{}'];

        $ooDateData = json_decode((string)($orderSheet['oo_date_data'] ?? '{}'), true);
        $ooStock = json_decode((string)($orderSheet['oo_stock'] ?? '{}'), true);
        if (!is_array($ooDateData)) {
            $ooDateData = [];
        }
        if (!is_array($ooStock)) {
            $ooStock = [];
        }
        if (!isset($ooDateData['stock_state']) || !is_array($ooDateData['stock_state'])) {
            $ooDateData['stock_state'] = [];
        }

        $beforeState = trim((string)($ooStock['state'] ?? ''));
        if ($beforeState === '') {
            $beforeState = '첫등록';
        }

        $ooDateData['stock_state'][] = [
            'state_before' => $beforeState,
            'state_after' => 'in',
            'date' => $actionTime,
            'id' => $sessionId,
            'name' => $sessionName,
        ];
        $nextOoStock = [
            'state' => 'in',
            'reg' => $reg,
        ];

        OrderSheetModel::query()
            ->where('oo_idx', '=', $orderSheetIdx)
            ->update([
                'oo_date_data' => json_encode($ooDateData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'oo_stock' => json_encode($nextOoStock, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);

        $resultPayload = [
            'success' => true,
            'msg' => '완료',
            'message' => '재고가 일괄 등록되었습니다.',
            'processed_count' => $processedCount,
            'skipped_count' => count($skippedRows),
            'skipped_rows' => $skippedRows,
            'order_sheet_idx' => $orderSheetIdx,
            'stock_state' => $nextOoStock,
            'updated_by' => $sessionIdx,
        ];

        try {
            $inspectionProcessLogService = new InspectionProcessLogService();
            $inspectionProcessLogService->logOrderSheetAllStock([
                'relation_pk' => $orderSheetIdx,
                'executor_admin_idx' => $sessionIdx,
                'executor_admin_id' => $sessionId,
                'executor_admin_name' => $sessionName,
                'executed_at' => $actionTime,
                'process_content' => [
                    'stock_day' => $stockDay,
                    'stock_all_memo' => $stockAllMemo,
                    'requested_line_count' => count($psIdxRows),
                    'processed_rows' => $processedRows,
                    'skipped_rows' => $skippedRows,
                ],
                'result_content' => $resultPayload,
            ]);
        } catch (\Throwable $e) {
            // 로그 저장 실패는 메인 처리에 영향이 없도록 분리한다.
        }

        return $resultPayload;
    }

    /**
     * 주문서 재고 팝업 - 고도몰 단건 처리
     *
     * @param array $requestData
     * @return array
     * @throws Exception
     */
    public function orderSheetSingleGodoInspection(array $requestData): array
    {
        $goodsNo = trim((string)($requestData['goods_no'] ?? ''));
        $pidx = (int)($requestData['pidx'] ?? 0);
        $psIdx = (int)($requestData['ps_idx'] ?? 0);
        $orderSheetIdx = (int)($requestData['os_idx'] ?? 0);
        $stockQty = null;
        if (array_key_exists('stock_qty', $requestData)) {
            $stockQtyRaw = trim((string)$requestData['stock_qty']);
            if ($stockQtyRaw !== '' && is_numeric($stockQtyRaw)) {
                $stockQty = (int)$stockQtyRaw;
            }
        }
        $stockInputQty = null;
        if (array_key_exists('stock_input_qty', $requestData)) {
            $stockInputQtyRaw = trim((string)$requestData['stock_input_qty']);
            if ($stockInputQtyRaw !== '' && is_numeric($stockInputQtyRaw)) {
                $stockInputQty = (int)$stockInputQtyRaw;
            }
        }
        $intranetSalePrice = trim((string)($requestData['intranet_sale_price'] ?? ''));
        $columnUpdates = trim((string)($requestData['column_updates'] ?? ''));
        $addCategoryCds = trim((string)($requestData['add_category_cds'] ?? ''));
        $deleteCategoryCds = trim((string)($requestData['delete_category_cds'] ?? ''));
        $selectedAutoIssuesRaw = trim((string)($requestData['selected_auto_issues'] ?? ''));
        $selectedAutoIssues = [];
        if ($selectedAutoIssuesRaw !== '') {
            $selectedAutoIssues = array_values(array_filter(array_map('trim', explode(',', $selectedAutoIssuesRaw)), static function ($v) {
                return $v !== '';
            }));
        }
        $isManualSoldOutIssueSelected = in_array('현재 품절(수동) 상태', $selectedAutoIssues, true);

        if (($goodsNo === '' || $goodsNo === '0') && $pidx > 0) {
            $productRow = ProductModel::query()
                ->select(['CD_IDX', 'cd_godo_code'])
                ->where('CD_IDX', '=', $pidx)
                ->first();
            $productRow = $productRow ? $productRow->toArray() : [];
            $goodsNo = trim((string)($productRow['cd_godo_code'] ?? ''));
        }

        if ($goodsNo === '' || $goodsNo === '0') {
            throw new Exception('고도몰 상품번호가 없어 처리할 수 없습니다.');
        }

        if ($psIdx <= 0 && $pidx > 0) {
            $stockRow = ProductStockModel::query()
                ->select(['ps_idx'])
                ->where('ps_prd_idx', '=', $pidx)
                ->first();
            $stockRow = $stockRow ? (is_array($stockRow) ? $stockRow : $stockRow->toArray()) : [];
            $psIdx = (int)($stockRow['ps_idx'] ?? 0);
        }

        $stockProcessToken = '';
        $productStockService = new ProductStockService();
        if ($orderSheetIdx > 0 && $psIdx > 0) {
            $stockProcessToken = 'order_sheet_godo_stock:' . $orderSheetIdx . ':' . $psIdx;
            if ($productStockService->hasStockChangeToken($stockProcessToken)) {
                return [
                    'success' => true,
                    'msg' => '이미 처리됨',
                    'message' => '이미 재고 반영까지 완료된 상품입니다.',
                    'already_processed' => true,
                    'order_sheet_idx' => $orderSheetIdx,
                    'prd_idx' => $pidx,
                    'ps_idx' => $psIdx,
                    'goods_no' => $goodsNo,
                ];
            }
        }

        if ($isManualSoldOutIssueSelected && ($stockInputQty === null || $stockInputQty <= 0)) {
            return [
                'success' => true,
                'msg' => '재고수량 미입력으로 미처리',
                'message' => '현재 품절(수동) 상태 해제는 재고수량 입력 후 처리됩니다.',
                'goods_no' => $goodsNo,
                'stock_qty' => $stockQty,
                'column_updates' => $columnUpdates,
                'add_category_cds' => $addCategoryCds,
                'delete_category_cds' => $deleteCategoryCds,
                'skipped' => true,
            ];
        }

        if ($isManualSoldOutIssueSelected && $stockInputQty !== null && $stockInputQty > 0) {
            $columnUpdateMap = [];
            if ($columnUpdates !== '') {
                foreach (explode(',', $columnUpdates) as $pair) {
                    $pair = trim((string)$pair);
                    if ($pair === '' || strpos($pair, '=') === false) {
                        continue;
                    }
                    [$pairKey, $pairValue] = explode('=', $pair, 2);
                    $pairKey = trim((string)$pairKey);
                    if ($pairKey === '') {
                        continue;
                    }
                    $columnUpdateMap[$pairKey] = trim((string)$pairValue);
                }
            }
            $columnUpdateMap['godo_sold_out_fl'] = 'n';
            $columnUpdatePairs = [];
            foreach ($columnUpdateMap as $pairKey => $pairValue) {
                $columnUpdatePairs[] = $pairKey . '=' . $pairValue;
            }
            $columnUpdates = implode(',', $columnUpdatePairs);
        }

        $intranetUpdatedColumns = [];
        if (in_array('상품번호 불일치', $selectedAutoIssues, true) && $pidx > 0) {
            ProductModel::query()
                ->where('CD_IDX', '=', $pidx)
                ->update([
                    'cd_godo_code' => $goodsNo,
                ]);
            $intranetUpdatedColumns[] = 'cd_godo_code';
        }

        if ($pidx > 0 && $intranetSalePrice !== '' && is_numeric($intranetSalePrice)) {
            ProductModel::query()
                ->where('CD_IDX', '=', $pidx)
                ->update([
                    'cd_sale_price' => $intranetSalePrice,
                ]);
            $intranetUpdatedColumns[] = 'cd_sale_price';
        }

        $godoApiService = new GodoApiService();
        $responseData = $godoApiService->autoStockUpdateAndInspection([
            'goodsNo' => $goodsNo,
            'stockQty' => $stockQty,
            'columnUpdates' => $columnUpdates,
            'addCategoryCds' => $addCategoryCds,
            'deleteCategoryCds' => $deleteCategoryCds,
        ]);

        $stockChangeResult = [];
        if ($stockInputQty !== null && $stockInputQty > 0) {
            if ($psIdx <= 0 || $stockProcessToken === '') {
                throw new Exception('재고 반영에 필요한 주문서 또는 재고코드 정보가 없습니다.');
            }
            $stockChangeResult = $productStockService->registerStockChange([
                'ps_idx' => $psIdx,
                'stock_mode' => 'plus',
                'stock_kind' => '신규입고',
                'stock_qty' => $stockInputQty,
                'stock_day' => date('Y-m-d'),
                'stock_memo' => '주문서 고도몰 재고+검수 처리',
                'psu_token' => $stockProcessToken,
            ]);
        }

        $resultPayload = [
            'success' => true,
            'msg' => '완료',
            'message' => '고도몰 처리가 완료되었습니다.',
            'order_sheet_idx' => $orderSheetIdx,
            'prd_idx' => $pidx,
            'ps_idx' => $psIdx,
            'goods_no' => $goodsNo,
            'stock_qty' => $stockQty,
            'column_updates' => $columnUpdates,
            'add_category_cds' => $addCategoryCds,
            'delete_category_cds' => $deleteCategoryCds,
            'intranet_updated_columns' => $intranetUpdatedColumns,
            'response' => $responseData,
            'stock_change' => $stockChangeResult,
        ];

        try {
            $sessionIdx = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
            $sessionId = (string)(AuthAdmin::getSession('sess_id') ?? '');
            $sessionName = (string)(AuthAdmin::getSession('sess_name') ?? '');
            $executedAt = date('Y-m-d H:i:s');
            $inspectionProcessLogService = new InspectionProcessLogService();
            $inspectionProcessLogService->logProductSingleGodoInspection([
                'location_code' => InspectionProcessLogService::LOCATION_ORDER_SHEET_STOCK_SINGLE,
                'relation_pk' => $orderSheetIdx,
                'prd_idx' => $pidx,
                'ps_idx' => $psIdx,
                'godo_goods_no' => $goodsNo,
                'executor_admin_idx' => $sessionIdx,
                'executor_admin_id' => $sessionId,
                'executor_admin_name' => $sessionName,
                'executed_at' => $executedAt,
                'process_content' => [
                    'stock_qty' => $stockQty,
                    'stock_input_qty' => $stockInputQty,
                    'selected_auto_issues' => $selectedAutoIssues,
                    'intranet_sale_price' => $intranetSalePrice,
                    'column_updates' => $columnUpdates,
                    'add_category_cds' => $addCategoryCds,
                    'delete_category_cds' => $deleteCategoryCds,
                ],
                'result_content' => $resultPayload,
            ]);
            $resultPayload['processed_at'] = $executedAt;
            $resultPayload['processed_by'] = $sessionName !== '' ? $sessionName : $sessionId;
        } catch (\Throwable $e) {
            // 로그 저장 실패는 실제 고도몰 처리 성공/실패에 영향을 주지 않는다.
        }

        return $resultPayload;
    }


    /**
     * 주문서 생성
     * @param array $data
     * @return array
     */
    public function createOrderSheet($data)
    {

        $name = $data['oo_name'] ?? '';
        if (empty($name)) {
            throw new Exception("주문서명을 입력해주세요.");
        }

        $input_data = $this->buildOrderSheetInputData($data, []);

        $orderSheetModel = new OrderSheetModel();
        $orderSheetInsertResult = $orderSheetModel->insert($input_data);
        $afterData = $this->getOrderSheetForLog($orderSheetInsertResult);
        $this->writeOrderSheetActionLog(
            $orderSheetInsertResult,
            'create',
            '주문서 생성',
            [],
            $afterData
        );

        return [
            'status' => 'success',
            'message' => '주문서 저장 완료',
            'order_sheet_idx' => $orderSheetInsertResult,
        ];
    }

    /**
     * 주문서 저장
     * @param Request $request
     * @return array
     */
    public function saveOrderSheet($data)
    {
        $idx = $data['idx'] ?? null;
        if (empty($idx)) {
            throw new Exception("주문서 번호가 없습니다.");
        }

        $orderSheetInfo = OrderSheetModel::find($idx);
        if (!$orderSheetInfo) {
            throw new Exception("주문서를 찾을 수 없습니다.");
        }

        $date_data = json_decode($orderSheetInfo['oo_date_data'] ?? '{}', true);
        if (!is_array($date_data)) {
            $date_data = [];
        }

        $name = $data['oo_name'] ?? '';
        if (empty($name)) {
            throw new Exception("주문서명을 입력해주세요.");
        }

        $beforeData = $this->getOrderSheetForLog($idx);
        $input_data = $this->buildOrderSheetInputData($data, $date_data);

        $orderSheetModel = new OrderSheetModel();
        $orderSheetInsertResult = $orderSheetModel->update(['oo_idx' => $idx], $input_data);
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update',
            '주문서 수정',
            $beforeData,
            $afterData
        );

        return [
            'status' => 'success',
            'message' => '주문서 저장 완료',
            'order_sheet_idx' => $orderSheetInsertResult,
        ];

    }

    /**
     * 주문서 입력 데이터 생성
     * @param array $data
     * @param array $dateData
     * @return array
     */
    private function buildOrderSheetInputData(array $data, array $dateData): array
    {
        $po_name = $data['oo_po_name'] ?? '';
        $form_idx = $data['oo_form_idx'] ?? 0;
        $import = $data['oo_import'] ?? '';
        $sort = $data['oo_sort'] ?? 0;
        $state = $data['oo_state'] ?? 1;
        $order_send_date = $data['order_send_date'] ?? '';
        $change_price_mode = $data['change_price_mode'] ?? []; //가격변동사항 모드
        $change_price_body = $data['change_price_body'] ?? []; //가격변동사항 내용
        $change_price_price = $data['change_price_price'] ?? []; //가격변동사항 금액
        $pay_mode = $data['pay_mode'] ?? []; //결제 모드
        $pay_price = $data['pay_price'] ?? []; //결제 금액
        $pay_date = $data['pay_date'] ?? []; //결제 일자
        $pay_memo = $data['pay_memo'] ?? []; //결제 메모
        $prd_to_pay_exchange_rate_raw = trim((string)($data['prd_to_pay_exchange_rate'] ?? ''));
        $prd_to_pay_exchange_rate = $prd_to_pay_exchange_rate_raw === '' ? 0 : (float)preg_replace('/[,\s]/', '', $prd_to_pay_exchange_rate_raw); //환산 환율

        //dd($data);

        if (!is_array($change_price_mode)) $change_price_mode = [];
        if (!is_array($change_price_body)) $change_price_body = [];
        if (!is_array($change_price_price)) $change_price_price = [];
        if (!is_array($pay_mode)) $pay_mode = [];
        if (!is_array($pay_price)) $pay_price = [];
        if (!is_array($pay_date)) $pay_date = [];
        if (!is_array($pay_memo)) $pay_memo = [];

        $prd_currency = $data['oo_prd_currency'] ?? ''; //상품 가격 화폐
        $prd_exchange_rate_raw = trim((string)($data['oo_prd_exchange_rate'] ?? ''));
        $prd_exchange_rate = $prd_exchange_rate_raw === '' ? 0 : (float)preg_replace('/[,\s]/', '', $prd_exchange_rate_raw); //상품 가격 환율
        if ($import !== '국내' && $prd_currency !== '원' && $prd_exchange_rate <= 0) {
            throw new Exception('상품 통화가 원이 아닐 경우 적용환율은 필수입니다.');
        }

        $memo = $data['oo_memo'] ?? '';

        $sum_currency = $data['oo_sum_currency'] ?? ''; //주문 결제 가격 화폐
        $sum_exchange_rate_raw = trim((string)($data['oo_sum_exchange_rate'] ?? ''));
        $sum_exchange_rate = $sum_exchange_rate_raw === '' ? 0 : (float)preg_replace('/[,\s]/', '', $sum_exchange_rate_raw); //주문 결제 가격 환율

        $name = $data['oo_name'] ?? '';
        $prd_sum_price = (float)preg_replace('/[,\s]/', '', (string)($data['prd_sum_price'] ?? '0')); // 결제가격
        $oo_sum_price = (float)preg_replace('/[,\s]/', '', (string)($data['oo_sum_price'] ?? '0')); // 결제가격
        $oo_fn_price = (double)str_replace(',', '', $data['oo_fn_price'] ?? '0'); // 확정 주문 금액
        $pay_fee = (double)str_replace(',', '', $data['pay_fee'] ?? '0'); // 결제 수수료
        $in_date = trim((string)($data['in_date'] ?? '')); //입고일
        $inDateColumnValue = ($in_date === '') ? null : $in_date;
        $oo_price_kr = (double)str_replace(',', '', $data['oo_price_kr'] ?? '0'); // 최종 합계 결제액

        //가격변동사항
        $_change_price = [];
        foreach ($change_price_mode as $key => $value) {
            $_mode = $change_price_mode[$key] ?? "";
            $_body = $change_price_body[$key] ?? "";
            $_price = (double)str_replace(',', '', $change_price_price[$key] ?? '0');

            $_change_price[] = [
                'mode' => $_mode,
                'body' => $_body,
                'price' => $_price,
            ];
        }

        //결제처리
        $_add_pay_list = [];
        foreach ($pay_mode as $key => $value) {
            $_pay_mode = $pay_mode[$key] ?? "";
            $_pay_price = (double)str_replace(',', '', $pay_price[$key] ?? '0');
            $_pay_date = $pay_date[$key] ?? "";
            $_pay_memo = $pay_memo[$key] ?? "";

            $_add_pay_list[] = [
                'pay_mode' => $_pay_mode,
                'pay_price' => $_pay_price,
                'pay_date' => $_pay_date,
                'pay_memo' => $_pay_memo,
            ];
        }

        $price_data_json = [
            'prd_sum_price' => $prd_sum_price ?? 0,
            'price' => $oo_sum_price ?? 0,
            'currency' => $sum_currency,
            'change_price' => $_change_price ?? [],
            'pay_fee' => $pay_fee ?? 0,
            'pay_price' => $oo_price_kr ?? 0,
            'pay_list' => $_add_pay_list ?? [],
        ];
        $price_data = json_encode($price_data_json, JSON_UNESCAPED_UNICODE);

        $dateData['order_send_date'] = $order_send_date; //주문서 발송일
        $dateData['in_date'] = $in_date; //입고일
        $date_data = json_encode($dateData, JSON_UNESCAPED_UNICODE);

        $express_mode = $data['express_mode'] ?? '';
        $express_name = $data['express_name'] ?? '';
        $express_number = $data['express_number'] ?? '';
        $express_report_weight = $data['express_report_weight'] ?? '';
        $express_weight = $data['express_weight'] ?? '';
        $express_cbm = $data['express_cbm'] ?? '';
        $express_box = $data['express_box'] ?? '';
        $express_price_expected_date = trim((string)($data['express_price_expected_date'] ?? ''));
        $express_price_expected = (int)str_replace(',', '', (string)($data['express_price_expected'] ?? '0'));
        $express_price = (int)str_replace(',', '', (string)($data['express_price'] ?? '0'));
        $express_price_add = (int)str_replace(',', '', (string)($data['express_price_add'] ?? '0'));

        $express_data_json = [
            'mode' => $express_mode,
            'name' => $express_name,
            'number' => $express_number,
            'report_weight' => $express_report_weight,
            'weight' => $express_weight,
            'cbm' => $express_cbm,
            'box' => $express_box,
            'price_expected_date' => $express_price_expected_date,
            'price_expected' => $express_price_expected,
            'price' => $express_price,
            'price_add' => $express_price_add
        ];
        $express_data = json_encode($express_data_json, JSON_UNESCAPED_UNICODE);

        $tex_num = $data['tex_num'] ?? '';
        $tex_report_price = (int)str_replace(',', '', (string)($data['tex_report_price'] ?? '0'));
        $tex_duty_price = (int)str_replace(',', '', (string)($data['tex_duty_price'] ?? '0'));
        $tex_vat_price = (int)str_replace(',', '', (string)($data['tex_vat_price'] ?? '0'));
        $tex_commission = (int)str_replace(',', '', (string)($data['tex_commission'] ?? '0'));

        $tex_data_json = [
            'num' => $tex_num,
            'report_price' => $tex_report_price,
            'duty_price' => $tex_duty_price,
            'vat_price' => $tex_vat_price,
            'commission' => $tex_commission
        ];
        $tex_data = json_encode($tex_data_json, JSON_UNESCAPED_UNICODE);

        $reg_json = AuthAdmin::getConnectionInfo();
        $reg = json_encode($reg_json, JSON_UNESCAPED_UNICODE);

        // created_by 컬럼은 정수(PK) 컬럼이므로 sess_idx를 사용하고 정수로 강제 변환
        $created_by = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $created_name = AuthAdmin::getSession('sess_name');

        return [
            'oo_name' => $name ?? '',
            'oo_po_name' => $po_name ?? '',
            'oo_form_idx' => $form_idx ?? 0,
            'oo_import' => $import ?? '', //수입형태
            'oo_sort' => $sort ?? 0,
            'oo_state' => $state ?? 0,
            'oo_prd_currency' => $prd_currency ?? '', //상품 가격 화폐
            'oo_prd_exchange_rate' => $prd_exchange_rate ?? 0, //상품 가격 환율
            'oo_memo' => $memo ?? '',
            'oo_price_data' => $price_data ?? '',
            'oo_fn_price' => $oo_fn_price ?? 0, //확정 주문 금액
            'oo_express_data' => $express_data ?? '',
            'oo_tex_data' => $tex_data ?? '',
            'oo_date_data' => $date_data ?? '',
            'oo_sum_currency' => $sum_currency ?? '', //주문 결제 가격 화폐
            'oo_sum_exchange_rate' => $sum_exchange_rate ?? 0, //주문 결제 가격 환율
            'oo_sum_price' => $oo_sum_price ?? 0, //주문 결제 가격
            'oo_prd_to_pay_exchange_rate' => $prd_to_pay_exchange_rate ?? 0, //환산 환율
            'oo_in_date' => $inDateColumnValue, //입고일
            'reg' => $reg ?? '',
            'created_by' => $created_by,
            'created_name' => $created_name ?? '',
            'oo_price_kr' => $oo_price_kr ?? 0, //최종 합계 결제액
        ];
    }


    /**
     * 주문서 상태 변경 (레거시 processing.order_sheet.php 리팩토링)
     *
     * @param array $data
     * @return array
     */
    public function orderSheetState(array $data): array
    {
        $idx = $data['idx'] ?? null;
        $state = $data['state'] ?? null;
        $in_date = $data['in_date'] ?? '';

        if (empty($idx) || $state === null || $state === '') {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $row = OrderSheetModel::query()
            ->select(['oo_state', 'oo_date_data'])
            ->where('oo_idx', $idx)
            ->first();
        $row = $row ? $row->toArray() : null;

        if (empty($row)) {
            throw new Exception('주문서를 찾을 수 없습니다.');
        }

        $oo_state = $row['oo_state'] ?? '';
        $oo_date_data = json_decode($row['oo_date_data'] ?? '{}', true);
        if (!is_array($oo_date_data)) {
            $oo_date_data = [];
        }
        if (!isset($oo_date_data['state']) || !is_array($oo_date_data['state'])) {
            $oo_date_data['state'] = [];
        }

        if ($oo_state != $state) {
            $oo_date_data['in_date'] = $in_date;
            $oo_date_data['state'][] = [
                'state_before' => $oo_state,
                'state_after' => $state,
                'date' => date('Y-m-d H:i:s'),
                'id' => AuthAdmin::getSession('sess_id'),
                'name' => AuthAdmin::getSession('sess_name'),
            ];
        }

        $update_data = [
            'oo_state' => $state,
            'oo_date_data' => json_encode($oo_date_data, JSON_UNESCAPED_UNICODE),
        ];

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(['oo_idx' => $idx], $update_data);
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_state',
            '주문서 상태 변경',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }


    /**
     * 주문서 파일 등록
     * @param array $data
     * @param array $files
     * @return array
     */
    public function orderSheetFile(array $data, array $files): array
    {
        $idx = $data['idx'] ?? null;
        $smode = $data['smode'] ?? ($data['mode'] ?? '');
        if ($smode === 'pay_file') {
            $smode = 'pay';
        }
        $viewName = $data['sname'] ?? ($data['view_name'] ?? '');

        if (empty($idx) || empty($smode)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $fileKey = "upload_file_{$smode}";
        $file = $files['fileObj'] ?? ($files[$fileKey] ?? null);
        if (!$file && !empty($files)) {
            $firstFile = reset($files);
            $file = is_array($firstFile) ? $firstFile : null;
        }

        if (empty($file)) {
            throw new Exception('파일이 없습니다.');
        }

        $uploaded = new UploadedFile($file);

        $savePrefix = '';
        if ($smode === 'pay') {
            $savePrefix = "pay_file_{$idx}_";
        } elseif ($smode === 'import_declaration') {
            $savePrefix = "import_declaration_{$idx}_";
        } elseif ($smode === 'invoice') {
            $savePrefix = "invoice_{$idx}_";
        } else {
            throw new Exception('유효하지 않은 파일 구분입니다.');
        }

        $uploadDir = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2)) . '/data/uploads';
        $extension = $uploaded->getClientOriginalExtension();
        $saveFilename = $savePrefix . time() . ($extension ? '.' . $extension : '');
        $uploaded->move($uploadDir, $saveFilename);

        $orderRow = OrderSheetModel::query()
            ->select(['oo_upload_file'])
            ->where('oo_idx', $idx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];

        $uploadFile = json_decode($orderRow['oo_upload_file'] ?? '{}', true);
        if (!is_array($uploadFile)) {
            $uploadFile = [];
        }

        if (!isset($uploadFile[$smode]) || !is_array($uploadFile[$smode])) {
            $uploadFile[$smode] = [];
        }

        $sizeBytes = (int) $uploaded->getSize();
        $displaySize = $sizeBytes >= 1048576
            ? sprintf('%.2f MB', $sizeBytes / 1048576)
            : sprintf('%.0f KB', ceil($sizeBytes / 1024));

        $uploadRow = [
            'name' => $saveFilename,
            'view_name' => $viewName,
            'size' => $displaySize,
            'date' => date('Y-m-d H:i:s'),
            'id' => AuthAdmin::getSession('sess_id'),
            'reg_name' => AuthAdmin::getSession('sess_name'),
        ];
        $uploadFile[$smode][] = $uploadRow;

        // 결제 파일은 레거시 키(pay_file)와 동기화하여 양쪽 화면을 모두 지원한다.
        if ($smode === 'pay') {
            $uploadFile['pay_file'] = $uploadFile['pay'];
        }

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_upload_file' => json_encode($uploadFile, JSON_UNESCAPED_UNICODE)]
        );
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_file_upload',
            '주문서 파일 업로드',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
            'idx' => $idx,
            'filename' => $saveFilename,
            'view_name' => $viewName,
            'size' => $displaySize,
            'reg_id' => AuthAdmin::getSession('sess_id'),
            'reg_name' => AuthAdmin::getSession('sess_name'),
            'reg_date' => date('Y-m-d H:i:s'),
        ];
    }


    /**
     * 주문서 파일 삭제
     * @param array $data
     * @return array
     */
    public function orderSheetFileDelete(array $data): array
    {
        $idx = $data['idx'] ?? null;
        $smode = $data['smode'] ?? ($data['mode'] ?? '');
        if ($smode === 'pay_file') {
            $smode = 'pay';
        }
        $filename = $data['filename'] ?? '';

        if (empty($idx) || empty($smode) || empty($filename)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $uploadDir = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2)) . '/data/uploads';
        $targetPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
        if (is_file($targetPath)) {
            @unlink($targetPath);
        }

        $orderRow = OrderSheetModel::query()
            ->select(['oo_upload_file'])
            ->where('oo_idx', $idx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];

        $uploadFile = json_decode($orderRow['oo_upload_file'] ?? '{}', true);
        if (!is_array($uploadFile)) {
            $uploadFile = [];
        }
        if (!isset($uploadFile[$smode]) || !is_array($uploadFile[$smode])) {
            $uploadFile[$smode] = [];
        }

        $uploadFile[$smode] = array_values(array_filter(
            $uploadFile[$smode],
            function ($row) use ($filename) {
                return is_array($row) && ($row['name'] ?? '') !== $filename;
            }
        ));

        if ($smode === 'pay') {
            $legacyPayFile = isset($uploadFile['pay_file']) && is_array($uploadFile['pay_file']) ? $uploadFile['pay_file'] : [];
            $uploadFile['pay_file'] = array_values(array_filter(
                $legacyPayFile,
                function ($row) use ($filename) {
                    return is_array($row) && ($row['name'] ?? '') !== $filename;
                }
            ));
            $uploadFile['pay'] = $uploadFile['pay_file'];
        }

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_upload_file' => json_encode($uploadFile, JSON_UNESCAPED_UNICODE)]
        );
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_file_delete',
            '주문서 파일 삭제',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
            'idx' => $idx,
        ];
    }

    
    /**
     * 캘린더 결제기한 등록/수정
     * 
     * @param array $data
     * @return array
     */
    public function approvalPayment(array $data): array
    {
        $idx = $data['idx'] ?? null;
        $priceRaw = $data['price'] ?? '0';
        $apMode = $data['ap_mode'] ?? '';
        $date = $data['date'] ?? '';
        $memo = $data['memo'] ?? '';
        $calendarIdx = $data['calendar_idx'] ?? '';

        if (empty($idx) || empty($apMode) || empty($date)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $price = (int)str_replace(',', '', (string)$priceRaw);

        $orderRow = OrderSheetModel::query()
            ->select(['oo_name', 'oo_express_data', 'oo_approval_date'])
            ->where('oo_idx', $idx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];

        $ooName = $orderRow['oo_name'] ?? '';
        $expressData = json_decode($orderRow['oo_express_data'] ?? '{}', true);
        $approvalDate = json_decode($orderRow['oo_approval_date'] ?? '{}', true);

        if (!is_array($expressData)) {
            $expressData = [];
        }
        if (!is_array($approvalDate)) {
            $approvalDate = [];
        }

        $subject = '';
        $mode = '결제기한';
        $kind = '';

        if ($apMode === 'express') {
            $subject = $ooName . ' - 배송비 결제기한';
            $kind = '배송비';

            if ((empty($expressData['price']) || ($expressData['price'] ?? 0) == 0) && $price > 0) {
                $expressData['price'] = $price;
            }
        } elseif ($apMode === 'tax') {
            $subject = $ooName . ' - 관/부가세 결제기한';
            $kind = '관/부가세';
        } else {
            throw new Exception('결제기한 모드가 올바르지 않습니다.');
        }

        $dataJson = json_encode(['oo_idx' => $idx, 'price' => $price], JSON_UNESCAPED_UNICODE);
        $regInfo = AuthAdmin::getConnectionInfo();
        $regJson = json_encode(['reg' => $regInfo], JSON_UNESCAPED_UNICODE);

        if (!empty($calendarIdx)) {
            $calendarStateRow = CalendarModel::query()
                ->select(['state'])
                ->where('idx', '=', $calendarIdx)
                ->first();
            $calendarStateRow = $calendarStateRow ? $calendarStateRow->toArray() : [];
            $calendarState = $calendarStateRow['state'] ?? '';

            if ($calendarState === 'E') {
                throw new Exception('완료된 결제기한은 수정할 수 없습니다.');
            }
            if ($calendarState === 'C') {
                throw new Exception('취소된 결제기한은 수정할 수 없습니다.');
            }

            if ($calendarState !== '' && $calendarState !== 'I') {
                throw new Exception('진행중 상태에서만 수정할 수 있습니다.');
            }

            CalendarModel::query()
                ->where('idx', '=', $calendarIdx)
                ->update([
                    'subject' => $subject,
                    'kind' => $kind,
                    'mode' => $mode,
                    'date_s' => $date,
                    'date_e' => $date,
                    'data' => $dataJson,
                    'targrt_idx' => $idx,
                    'memo' => $memo,
                    'reg' => $regJson,
                ]);
        } else {
            $calendarIdx = CalendarModel::query()->insertGetId([
                'subject' => $subject,
                'kind' => $kind,
                'mode' => $mode,
                'date_s' => $date,
                'date_e' => $date,
                'data' => $dataJson,
                'targrt_idx' => $idx,
                'memo' => $memo,
                'comment_count' => 0,
                'reg' => $regJson,
            ]);
        }

        if (!isset($approvalDate[$apMode]) || !is_array($approvalDate[$apMode])) {
            $approvalDate[$apMode] = [];
        }
        $approvalDate[$apMode]['price'] = $price;
        $approvalDate[$apMode]['approval'] = [
            'date' => $date,
            'calendar_idx' => $calendarIdx,
            'reg' => $regInfo,
        ];

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(
            ['oo_idx' => $idx],
            [
                'oo_express_data' => json_encode($expressData, JSON_UNESCAPED_UNICODE),
                'oo_approval_date' => json_encode($approvalDate, JSON_UNESCAPED_UNICODE),
            ]
        );
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_approval_payment',
            '주문서 결제기한 등록/수정',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
            'calendar_idx' => $calendarIdx,
        ];
    }


    /**
     * 결제기한 캘린더 완료처리
     * @param array $data
     * @return array
     */
    public function calendarOk(array $data): array
    {
        $idx = $data['idx'] ?? null;
        $calendarIdx = $data['calendar_idx'] ?? null;
        $apMode = $data['ap_mode'] ?? '';

        if (empty($idx) || empty($calendarIdx) || empty($apMode)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $regInfo = AuthAdmin::getConnectionInfo();

        $calendarRow = CalendarModel::query()
            ->select(['reg'])
            ->where('idx', $calendarIdx)
            ->first();
        $calendarRow = $calendarRow ? $calendarRow->toArray() : [];

        $regJson = json_decode($calendarRow['reg'] ?? '{}', true);
        if (!is_array($regJson)) {
            $regJson = [];
        }
        $regJson['mod'][] = $regInfo;

        CalendarModel::query()
            ->where('idx', '=', $calendarIdx)
            ->update([
                'state' => 'E',
                'reg' => json_encode($regJson, JSON_UNESCAPED_UNICODE),
            ]);

        $orderRow = OrderSheetModel::query()
            ->select(['oo_approval_date'])
            ->where('oo_idx', $idx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];

        $approvalDate = json_decode($orderRow['oo_approval_date'] ?? '{}', true);
        if (!is_array($approvalDate)) {
            $approvalDate = [];
        }
        if (!isset($approvalDate[$apMode]) || !is_array($approvalDate[$apMode])) {
            $approvalDate[$apMode] = ['approval' => []];
        }
        if (!isset($approvalDate[$apMode]['approval']) || !is_array($approvalDate[$apMode]['approval'])) {
            $approvalDate[$apMode]['approval'] = [];
        }

        $approvalDate[$apMode]['approval']['calendar_state'] = 'E';
        $approvalDate[$apMode]['approval']['calendar_reg'] = $regInfo;

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_approval_date' => json_encode($approvalDate, JSON_UNESCAPED_UNICODE)]
        );
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_calendar_ok',
            '주문서 결제기한 완료처리',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }

    
    /**
     * 결제기한 캘린더 삭제
     * @param array $data
     * @return array
     */
    public function calendarDel(array $data): array
    {
        $idx = $data['idx'] ?? null;
        $calendarIdx = $data['calendar_idx'] ?? null;
        $apMode = $data['ap_mode'] ?? '';

        if (empty($idx) || empty($calendarIdx) || empty($apMode)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $orderRow = OrderSheetModel::query()
            ->select(['oo_approval_date'])
            ->where('oo_idx', $idx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];

        $approvalDate = json_decode($orderRow['oo_approval_date'] ?? '{}', true);
        if (!is_array($approvalDate)) {
            $approvalDate = [];
        }
        if (!isset($approvalDate[$apMode]) || !is_array($approvalDate[$apMode])) {
            $approvalDate[$apMode] = ['approval' => []];
        }
        if (!isset($approvalDate[$apMode]['approval']) || !is_array($approvalDate[$apMode]['approval'])) {
            $approvalDate[$apMode]['approval'] = [];
        }

        $approvalDate[$apMode]['approval']['calendar_idx'] = '';

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_approval_date' => json_encode($approvalDate, JSON_UNESCAPED_UNICODE)]
        );
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_calendar_delete',
            '주문서 결제기한 삭제',
            $beforeData,
            $afterData
        );

        CalendarModel::where('idx', $calendarIdx)->delete();

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }


    /**
     * 주문서 로그용 현재 데이터 조회
     * @param int|string|null $idx
     * @return array
     */
    private function getOrderSheetForLog($idx): array
    {
        if (empty($idx)) {
            return [];
        }

        $row = OrderSheetModel::query()
            ->where('oo_idx', '=', $idx)
            ->first();

        return $row ? $row->toArray() : [];
    }


    /**
     * 주문서 액션 로그 저장
     * @param int|string|null $idx
     * @param string $actionMode
     * @param string $actionSummary
     * @param array $beforeData
     * @param array $afterData
     * @return void
     */
    private function writeOrderSheetActionLog($idx, string $actionMode, string $actionSummary, array $beforeData, array $afterData): void
    {
        if (empty($idx)) {
            return;
        }

        try {
            $adminActionLogService = new AdminActionLogService();
            $diff = $adminActionLogService->buildDiff($beforeData, $afterData);
            $actionUrl = (string)($_SERVER['REQUEST_URI'] ?? '');

            $adminActionLogService->log([
                'target_type' => 'orderSheet',
                'target_table' => 'ona_order',
                'target_pk' => (string)$idx,
                'action_mode' => $actionMode,
                'action_summary' => $actionSummary,
                'before_json' => $beforeData,
                'after_json' => $afterData,
                'diff_json' => $diff,
                'action_url' => $actionUrl !== '' ? $actionUrl : null,
            ]);
        } catch (\Throwable $e) {
            // 로그 저장 실패는 메인 동작에 영향을 주지 않도록 분리한다.
        }
    }


    /**
     * 주문서 주문 상품목록
     * @param array $data
     * @return array
     */
    public function getOrderSheetDetailProduct(array $data): array
    {
        
        $oo_idx = $data['oo_idx'] ?? null;
        $oop_idx = $data['oop_idx'] ?? null;
        $form_view = $data['form_view'] ?? 'hidden';

        $currency_map = [
            '원' => 'KRW',
            '엔' => 'JPY',
            '위안' => 'CNY',
            '달러' => 'USD',
        ];

        if (empty($oo_idx) || empty($oop_idx)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $orderSheet = OrderSheetModel::query()
            ->select([
                'oo_idx', 
                'oo_name', 
                'oo_state', 
                'oo_import', 
                'oo_json', 
                'oo_false',
                'oo_prd_exchange_rate',
                'oo_prd_currency',
                'oo_sum_currency',
                'oo_sum_exchange_rate',
                'oo_prd_to_pay_exchange_rate'
                ])
            ->where('oo_idx', '=', $oo_idx)
            ->first();

        if (empty($orderSheet)) {
            throw new Exception('주문서를 찾을 수 없습니다.');
        }
        
        $orderGroupProduct = OrderGroupProductModel::find($oop_idx);
        if (empty($orderGroupProduct)) {
            throw new Exception('주문서 상품을 찾을 수 없습니다.');
        }

        $orderSheet = $orderSheet ? $orderSheet->toArray() : [];
        $orderGroupProduct = $orderGroupProduct ? $orderGroupProduct->toArray() : [];

        $orderSheet['oo_json'] = json_decode($orderSheet['oo_json'] ?? '[]', true);
        if (!is_array($orderSheet['oo_json'])) {
            $orderSheet['oo_json'] = [];
        }

        //주문실패 데이터
        $orderSheet['oo_false'] = json_decode($orderSheet['oo_false'] ?? '[]', true);
        if (!is_array($orderSheet['oo_false'])) {
            $orderSheet['oo_false'] = [];
        }

        $prd_currency_code = $currency_map[$orderSheet['oo_prd_currency']] ?? '';
        $pay_currency_code = $currency_map[$orderSheet['oo_sum_currency']] ?? '';

        $orderSheet['oo_prd_currency_code'] = $prd_currency_code;
        $orderSheet['oo_sum_currency_code'] = $pay_currency_code;

        // 상품 통화와 결제 통화가 다를 경우 체크
        $is_currency_mismatch = false;
        if( $prd_currency_code !== $pay_currency_code ) {
            $is_currency_mismatch = true;

            //수입주문일 경우 체크
            if( $orderSheet['oo_import'] !== '국내' ) {
                $is_currency_mismatch = true;
                $orderSheet['is_currency_mismatch'] = true;
            }
        }


        
        /*
        $oop_data = json_decode($orderGroupProduct['oop_data'] ?? '{}', true);
        if (!is_array($oop_data)) {
            $oop_data = [];
        }
        */
        $orderGroupProduct['oop_data'] = json_decode($orderGroupProduct['oop_data'] ?? '{}', true);
        if (!is_array($orderGroupProduct['oop_data'])) {
            $orderGroupProduct['oop_data'] = [];
        }
        $oop_data = $orderGroupProduct['oop_data'];

        // oo_json에서 bidx가 현재 oop_idx와 일치하는 그룹만 추출
        $matchedOrderGroup = [];
        foreach ($orderSheet['oo_json'] as $groupRow) {
            if (!is_array($groupRow)) {
                continue;
            }
            if ((string)($groupRow['bidx'] ?? '') === (string)$oop_idx) {
                $matchedOrderGroup = $groupRow;
                break;
            }
        }

        // selpd의 pidx 목록 기준으로 oop_data를 재구성
        $selpdRows = [];
        if (!empty($matchedOrderGroup) && is_array($matchedOrderGroup['selpd'] ?? null)) {
            $selpdRows = $matchedOrderGroup['selpd'];
        }

        $oopDataByIdx = [];
        foreach ($oop_data as $oopRow) {
            if (!is_array($oopRow)) {
                continue;
            }
            $rowIdx = (string)($oopRow['idx'] ?? '');
            if ($rowIdx !== '' && !isset($oopDataByIdx[$rowIdx])) {
                $oopDataByIdx[$rowIdx] = $oopRow;
            }
        }

        // form_view와 관계없이 selpd를 항상 매칭
        foreach ($selpdRows as $selpdRow) {
            if (!is_array($selpdRow)) {
                continue;
            }

            $pidx = (string)($selpdRow['pidx'] ?? '');
            if ($pidx === '' || !isset($oopDataByIdx[$pidx])) {
                continue;
            }

            $selpdRow['sum_price'] = (string)(((float)($selpdRow['price'] ?? 0)) * ((float)($selpdRow['qty'] ?? 0)));
            $oopDataByIdx[$pidx]['selpd'] = $selpdRow;
        }

        // oo_false를 pidx 기준으로 매칭
        $ooFalseByPidx = [];
        foreach ($orderSheet['oo_false'] as $falseRow) {
            if (!is_array($falseRow)) {
                continue;
            }
            $falsePidx = (string)($falseRow['pidx'] ?? '');
            if ($falsePidx === '' || isset($ooFalseByPidx[$falsePidx])) {
                continue;
            }
            $ooFalseByPidx[$falsePidx] = $falseRow;
        }

        $oopDataTotalBeforeHidden = count($oop_data);
        if (!empty($oopDataByIdx)) {
            $mergedOopData = [];
            foreach ($oop_data as $oopRow) {
                if (!is_array($oopRow)) {
                    continue;
                }
                $rowIdx = (string)($oopRow['idx'] ?? '');
                if ($rowIdx !== '' && isset($oopDataByIdx[$rowIdx])) {
                    $row = $oopDataByIdx[$rowIdx];
                    if (isset($ooFalseByPidx[$rowIdx])) {
                        $row['false_data'] = $ooFalseByPidx[$rowIdx];
                        $row['is_false'] = 'ok';
                    }
                    $mergedOopData[] = $row;
                } else {
                    if ($rowIdx !== '' && isset($ooFalseByPidx[$rowIdx])) {
                        $oopRow['false_data'] = $ooFalseByPidx[$rowIdx];
                        $oopRow['is_false'] = 'ok';
                    }
                    $mergedOopData[] = $oopRow;
                }
            }

            // hidden 필터 적용 전 전체 상품수
            $oopDataTotalBeforeHidden = count($mergedOopData);

            // hidden 모드에서는 selpd 매칭된 상품만 노출
            if ((string)$form_view === 'hidden') {
                $mergedOopData = array_values(array_filter($mergedOopData, static function ($row) {
                    return is_array($row) && !empty($row['selpd']);
                }));
            }

            $orderGroupProduct['oop_data'] = $mergedOopData;
            $oop_data = $orderGroupProduct['oop_data'];
        }
        $orderGroupProduct['oo_false_map'] = $ooFalseByPidx;

        // 전체 상품수(oop_data) / 선택 상품수(selpdRows) 집계
        $orderGroupProduct['oop_data_total_count'] = $oopDataTotalBeforeHidden;
        $orderGroupProduct['selpd_selected_count'] = count(array_filter($selpdRows, static function ($row) {
            return is_array($row);
        }));

        // oop_data 내 상품 idx를 중복 없이 추출 (whereIn 용)
        $prd_idxs = [];
        foreach ($oop_data as $item) {
            if (!is_array($item)) {
                continue;
            }

            $prdIdx = (int)($item['idx'] ?? 0);
            if ($prdIdx > 0) {
                $prd_idxs[] = $prdIdx;
            }
        }
        $prd_idxs = array_values(array_unique($prd_idxs));

        $orderSheetProductList = [];
        if (!empty($prd_idxs)) {
            $orderSheetProductList = ProductModel::query()
                ->from('COMPARISON_DB as A')
                ->leftJoin('prd_stock as B', 'B.ps_prd_idx', '=', 'A.CD_IDX')
                ->whereIn('A.CD_IDX', $prd_idxs)
                ->select([
                    'A.CD_IDX',
                    'A.CD_CODE',
                    'A.CD_CODE2',
                    'A.CD_CODE3',
                    'A.CD_NAME',
                    'A.CD_IMG',
                    'A.img_mode',
                    'A.CD_WEIGHT',
                    'A.CD_WEIGHT2',
                    'A.CD_WEIGHT3',
                    'A.cd_code_fn',
                    'A.cd_weight_fn',
                    'A.cd_size_fn',
                    'A.cd_price_fn',
                    'A.cd_memo3',
                    'A.is_discontinued',
                    'B.is_sale_month',
                    'B.is_sale_special',
                    'B.ps_idx',
                    'B.ps_stock',
                    'B.ps_in_date',
                    'B.ps_last_date',

                ])
                ->get()
                ->keyBy('CD_IDX')
                ->toArray();

        }

        if (!empty($orderSheetProductList) && !empty($prd_idxs)) {
            try {
                $mappingRows = ProductLabelMappingModel::query()
                    ->select(['product_idx', 'label_idx', 'display_order'])
                    ->where('product_type', 'prdDB')
                    ->whereIn('product_idx', $prd_idxs)
                    ->orderBy('product_idx', 'asc')
                    ->orderBy('display_order', 'asc')
                    ->orderBy('idx', 'asc')
                    ->get()
                    ->toArray();
            } catch (\Throwable $e) {
                $mappingRows = [];
            }

            $labelMap = [];
            if (!empty($mappingRows)) {
                $labelIdxs = array_values(array_unique(array_filter(array_map(function ($row) {
                    return (int)($row['label_idx'] ?? 0);
                }, $mappingRows), function ($idx) {
                    return $idx > 0;
                })));

                if (!empty($labelIdxs)) {
                    try {
                        $labelRows = ProductLabelModel::query()
                            ->select(['idx', 'label_code', 'label_name', 'icon_path', 'is_active'])
                            ->whereIn('idx', $labelIdxs)
                            ->where('is_active', 1)
                            ->get()
                            ->toArray();
                        foreach ($labelRows as $labelRow) {
                            $labelIdx = (int)($labelRow['idx'] ?? 0);
                            if ($labelIdx <= 0) {
                                continue;
                            }
                            $labelMap[$labelIdx] = [
                                'idx' => $labelIdx,
                                'label_code' => (string)($labelRow['label_code'] ?? ''),
                                'label_name' => (string)($labelRow['label_name'] ?? ''),
                                'icon_path' => (string)($labelRow['icon_path'] ?? ''),
                            ];
                        }
                    } catch (\Throwable $e) {
                        $labelMap = [];
                    }
                }
            }

            $labelsByProductIdx = [];
            foreach ($mappingRows as $mappingRow) {
                $productIdx = (int)($mappingRow['product_idx'] ?? 0);
                $labelIdx = (int)($mappingRow['label_idx'] ?? 0);
                if ($productIdx <= 0 || $labelIdx <= 0 || !isset($labelMap[$labelIdx])) {
                    continue;
                }
                if (!isset($labelsByProductIdx[$productIdx])) {
                    $labelsByProductIdx[$productIdx] = [];
                }
                $labelsByProductIdx[$productIdx][] = $labelMap[$labelIdx];
            }

            foreach ($orderSheetProductList as &$product) {
                $productIdx = (int)($product['CD_IDX'] ?? 0);
                $product['product_labels'] = $labelsByProductIdx[$productIdx] ?? [];
            }
            unset($product);
        }

        foreach ($orderSheetProductList as &$product) {
            if (!is_array($product)) {
                continue;
            }
            /*
            $product['cd_weight_fn'] = json_decode($product['cd_weight_fn'] ?? '{}', true);
            $product['cd_size_fn'] = json_decode($product['cd_size_fn'] ?? '{}', true);
            $product['cd_price_fn'] = json_decode($product['cd_price_fn'] ?? '{}', true);
            */

            if (is_string($product['cd_weight_fn'])) {
                $product['cd_weight_fn'] = json_decode($product['cd_weight_fn'], true);
            }
        
            if (is_string($product['cd_size_fn'])) {
                $product['cd_size_fn'] = json_decode($product['cd_size_fn'], true);
            }
        
            if (is_string($product['cd_price_fn'])) {
                $product['cd_price_fn'] = json_decode($product['cd_price_fn'], true);
            }

            $product['weight'] = $product['cd_weight_fn']['3'] ?? 0;
            if ($product['weight'] == 0) {
                $product['weight'] = max($product['cd_weight_fn']['1'] ?? 0, $product['cd_weight_fn']['2'] ?? 0);
            }

            $product['cbm'] = $product['cd_size_fn']['invoice']['cbm'] ?? 0;

            $product['unit_price'] = $product['cd_price_fn'][$orderGroupProduct['oop_code']] ?? 0;
            $product['invoice_price'] = $product['cd_price_fn']['invoice'][$orderGroupProduct['oop_code']] ?? 0;

            //환산 환율이 있을 경우 환산 환율로 계산
            if( $orderSheet['oo_prd_to_pay_exchange_rate'] > 0 ) {
                $product['pay_unit_price'] = $product['unit_price'] / $orderSheet['oo_prd_to_pay_exchange_rate'];
            } else {
                //환산 환율이 없을 경우 개별 저장한 가격으로 처리
                $product['pay_unit_price'] = $product['cd_price_fn']['currency'][$orderSheet['oo_sum_currency_code']][$orderGroupProduct['oop_code']] ?? 0;
            }
        }
        unset($product);

        //dump($orderGroupProduct['oop_data']);
        //$sumPriceTotal = 0;
        $toFloat = static function ($value): float {
            if (is_float($value) || is_int($value)) {
                return (float)$value;
            }
            if (is_string($value)) {
                $normalized = str_replace([',', ' '], '', trim($value));
                return is_numeric($normalized) ? (float)$normalized : 0.0;
            }
            return 0.0;
        };

        $oopDataTotalQty = 0.0;
        $oopDataTotalSumPrice = 0.0;
        $oopDataTotalWeightSumKg = 0.0;

        foreach ($orderGroupProduct['oop_data'] as &$item) {

            $orderItemPrice = $toFloat($item['price'] ?? ($item['selpd']['price'] ?? 0));
            $orderItemQty = $toFloat($item['qty'] ?? ($item['selpd']['qty'] ?? 0));
            $orderItemSumPrice = $orderItemPrice * $orderItemQty;
            $isFalseItem = ((string)($item['is_false'] ?? '') === 'ok');
            if (!$isFalseItem) {
                $oopDataTotalQty += $orderItemQty;
                $oopDataTotalSumPrice += $orderItemSumPrice;
            }

            $product = $orderSheetProductList[$item['idx']] ?? null;
            if (empty($product)) {
                continue;
            }

            if ($orderItemQty > 0) {
                $productWeight = $toFloat($product['weight'] ?? 0);
                $item['weight_sum'] = $productWeight * $orderItemQty;
                if( $item['weight_sum'] > 0) {
                    $item['weight_sum_kg'] = $item['weight_sum'] / 1000;
                } else {
                    $item['weight_sum_kg'] = 0;
                }
                if (!$isFalseItem) {
                    $oopDataTotalWeightSumKg += $item['weight_sum_kg'];
                }
            }

            $item['product'] = $product;

        }
        unset($item);
        $orderGroupProduct['oop_data_total_qty'] = $oopDataTotalQty;
        $orderGroupProduct['oop_data_total_sum_price'] = round($oopDataTotalSumPrice, 2);
        $orderGroupProduct['oop_data_total_weight_sum_kg'] = round($oopDataTotalWeightSumKg, 3);

        $result = [
            'orderSheet' => $orderSheet,
            'orderGroup' => $matchedOrderGroup,
            'orderGroupProduct' => $orderGroupProduct
        ];

       //dump($result);

        return $result;
    }


    /**
     * 주문서 주문그룹 상품 저장
     * 
     * 레거시 processing.order_sheet.php 의 orderSheet_groupOrder 로직을 서비스화한 메서드.
     * @param array $data
     * @return array
     */
    public function orderSheetSaveGroupProduct(array $data): array
    {
        $ooIdx = trim((string)($data['oo_idx'] ?? ''));
        $oopIdx = trim((string)($data['oop_idx'] ?? ''));
        if ($ooIdx === '' || $oopIdx === '') {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $sendIdx = $data['send_idx'] ?? [];
        $sendPrice = $data['send_price'] ?? [];
        $sendQty = $data['send_qty'] ?? [];
        $sendMemo = $data['send_memo'] ?? [];

        if (!is_array($sendIdx)) {
            $sendIdx = [];
        }
        if (!is_array($sendPrice)) {
            $sendPrice = [];
        }
        if (!is_array($sendQty)) {
            $sendQty = [];
        }
        if (!is_array($sendMemo)) {
            $sendMemo = [];
        }

        $toFloat = static function ($value): float {
            if ($value === null) {
                return 0.0;
            }
            if (is_string($value)) {
                $value = trim($value);
                if ($value === '') {
                    return 0.0;
                }
                $value = preg_replace('/[,\s]/', '', $value);
            }
            return is_numeric($value) ? (float)$value : 0.0;
        };

        $item = (int)($data['item'] ?? 0);
        $totalQty = (int)($data['total_qty'] ?? 0);
        $totalPrice = $toFloat($data['total_price'] ?? 0);
        $totalWeight = $toFloat($data['total_weight'] ?? 0);
        $totalCbm = $toFloat($data['total_cbm'] ?? 0);

        $orderRow = OrderSheetModel::query()
            ->select([
                'oo_idx',
                'oo_import',
                'oo_prd_currency',
                'oo_sum_currency',
                'oo_prd_to_pay_exchange_rate',
                'oo_json',
                'oo_price_data',
                'oo_sum_goods',
                'oo_sum_qty',
                'oo_sum_weight',
                'oo_sum_price',
                'oo_sum_cbm'
            ])
            ->where('oo_idx', '=', $ooIdx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];
        if (empty($orderRow)) {
            throw new Exception('주문서를 찾을 수 없습니다.');
        }

        $ooJson = json_decode($orderRow['oo_json'] ?? '[]', true);
        if (!is_array($ooJson)) {
            $ooJson = [];
        }

        $instSelpd = [];
        $sendCount = count($sendIdx);
        for ($i = 0; $i < $sendCount; $i++) {
            $memoRaw = (string)($sendMemo[$i] ?? '');
            $memoRaw = str_replace(["\r\n", "\r"], "\n", $memoRaw);
            $memoRaw = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $memoRaw) ?? '';

            $instSelpd[] = [
                'pidx' => (string)($sendIdx[$i] ?? ''),
                'price' => $toFloat($sendPrice[$i] ?? 0),
                'qty' => (int)$toFloat($sendQty[$i] ?? 0),
                'memo' => $memoRaw,
            ];
        }

        $saveData = [
            'bidx' => $oopIdx,
            'item' => $item,
            'qty' => $totalQty,
            'price' => $totalPrice,
            'weight' => $totalWeight,
            'cbm' => $totalCbm,
            'selpd' => $instSelpd,
        ];

        $ooSumGoods = 0;
        $ooSumQty = 0;
        $ooSumWeight = 0.0;
        $ooSumPrice = 0.0;
        $ooSumCbm = 0.0;

        $targetGroupFoundCount = 0;
        $instJsonSalt = [];

        foreach ($ooJson as $row) {
            if (!is_array($row)) {
                continue;
            }

            if ((string)($row['bidx'] ?? '') === $oopIdx) {
                $instJsonSalt[] = $saveData;
                $ooSumGoods += $item;
                $ooSumQty += $totalQty;
                $ooSumWeight += $totalWeight;
                $ooSumPrice += $totalPrice;
                $ooSumCbm += $totalCbm;
                $targetGroupFoundCount++;
                continue;
            }

            $instJsonSalt[] = $row;
            $ooSumGoods += (int)($row['item'] ?? 0);
            $ooSumQty += (int)($row['qty'] ?? 0);
            $ooSumWeight += $toFloat($row['weight'] ?? 0);
            $ooSumPrice += $toFloat($row['price'] ?? 0);
            $ooSumCbm += $toFloat($row['cbm'] ?? 0);

            $falseCount = (int)($row['false'] ?? 0);
            if ($falseCount > 0) {
                $ooSumGoods -= $falseCount;
                $ooSumQty -= (int)($row['false_sum_qty'] ?? 0);
                $ooSumWeight -= $toFloat($row['false_sum_weight'] ?? 0);
                $ooSumPrice -= $toFloat($row['false_sum_price'] ?? 0);
                $ooSumCbm -= $toFloat($row['false_sum_cbm'] ?? 0);
            }
        }

        if ($targetGroupFoundCount === 0) {
            $instJsonSalt[] = $saveData;
            $ooSumGoods += $item;
            $ooSumQty += $totalQty;
            $ooSumWeight += $totalWeight;
            $ooSumPrice += $totalPrice;
            $ooSumCbm += $totalCbm;
        }

        $encodedOoJson = json_encode($instJsonSalt, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encodedOoJson === false) {
            $encodedOoJson = '[]';
        }

        $ooPriceData = json_decode($orderRow['oo_price_data'] ?? '{}', true);
        if (!is_array($ooPriceData)) {
            $ooPriceData = [];
        }
        // 기존 oo_price_data 구조는 유지하고, 그룹 저장으로 계산된 상품 주문가격만 동기화한다.
        $ooPriceData['prd_sum_price'] = $ooSumPrice;
        $encodedOoPriceData = json_encode($ooPriceData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encodedOoPriceData === false) {
            $encodedOoPriceData = '{}';
        }

        $currencyToCode = static function ($currency): string {
            $raw = strtoupper(trim((string)$currency));
            if ($raw === '원') return 'KRW';
            if ($raw === '엔') return 'JPY';
            if ($raw === '위안') return 'CNY';
            if ($raw === '달러') return 'USD';
            return $raw;
        };
        $ooImport = trim((string)($orderRow['oo_import'] ?? ''));
        $prdCurrencyCode = $currencyToCode($orderRow['oo_prd_currency'] ?? '');
        $sumCurrencyCode = $currencyToCode($orderRow['oo_sum_currency'] ?? '');
        $prdToPayExchangeRate = $toFloat($orderRow['oo_prd_to_pay_exchange_rate'] ?? 0);

        $savedOoSumPrice = $ooSumPrice;
        if (
            $ooImport !== '국내' &&
            $prdCurrencyCode !== '' &&
            $sumCurrencyCode !== '' &&
            $prdCurrencyCode !== $sumCurrencyCode &&
            $prdToPayExchangeRate > 0
        ) {
            $savedOoSumPrice = round($ooSumPrice / $prdToPayExchangeRate, 2);
        }

        $beforeData = $this->getOrderSheetForLog($ooIdx);
        
        OrderSheetModel::query()
            ->where('oo_idx', '=', $ooIdx)
            ->update([
                'oo_json' => $encodedOoJson,
                'oo_price_data' => $encodedOoPriceData,
                'oo_sum_goods' => $ooSumGoods,
                'oo_sum_qty' => $ooSumQty,
                'oo_sum_weight' => $ooSumWeight,
                'oo_sum_price' => $savedOoSumPrice,
                'oo_sum_cbm' => $ooSumCbm,
            ]);

        $afterData = $this->getOrderSheetForLog($ooIdx);
        $this->writeOrderSheetActionLog(
            $ooIdx,
            'update_order_sheet_group_product',
            '주문서 주문그룹 상품 저장',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
            'group_sum_goods' => $item,
            'group_sum_qty' => $totalQty,
            'group_sum_weight' => $totalWeight,
            'group_sum_price' => $totalPrice,
            'oo_sum_goods' => $ooSumGoods,
            'oo_sum_qty' => $ooSumQty,
            'oo_sum_weight' => $ooSumWeight,
            'oo_sum_price' => $savedOoSumPrice,
            'oo_sum_cbm' => $ooSumCbm,
        ];
    }


    /**
     * 주문서 상품 실패 처리
     * 
     * @param array $data
     * @return array
     */
    public function orderSheetProductFalse(array $data)
    {

        $ooIdx = $data['oo_idx'] ?? '';
        $oopIdx = $data['oop_idx'] ?? '';
        $pidx = $data['pidx'] ?? '';
        $unitFalseMode = $data['unit_false_mode'] ?? '';
        $pidxMemo = (string)($data['pidx_memo'] ?? '');

        if ($ooIdx === '' || $oopIdx === '' || $pidx === '' || ($unitFalseMode !== 'out' && $unitFalseMode !== 'on')) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $toFloat = static function ($value): float {
            if (is_string($value)) {
                $value = preg_replace('/[,\s]/', '', $value);
            }
            return (float)$value;
        };

        $orderRow = OrderSheetModel::query()
            ->select(['oo_idx', 'oo_json', 'oo_false', 'oo_sum_goods', 'oo_sum_qty', 'oo_sum_weight', 'oo_sum_price'])
            ->where('oo_idx', '=', $ooIdx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];
        if (empty($orderRow)) {
            throw new Exception('주문서를 찾을 수 없습니다.');
        }

        $ooJson = json_decode($orderRow['oo_json'] ?? '[]', true);
        if (!is_array($ooJson)) {
            $ooJson = [];
        }

        $ooFalseRaw = (string)($orderRow['oo_false'] ?? '');
        $ooFalseRawTrim = trim($ooFalseRaw);
        if ($ooFalseRawTrim === '') {
            $falseJson = [];
        } else {
            $falseJson = json_decode($ooFalseRawTrim, true);
            if (!is_array($falseJson)) {
                $wrapped = '[' . $ooFalseRawTrim . ']';
                $falseJson = json_decode($wrapped, true);
                if (!is_array($falseJson)) {
                    $falseJson = [];
                }
            }
        }

        $newOoFalse = [];
        foreach ($falseJson as $row) {
            if (!is_array($row)) {
                continue;
            }

            $rowPidx = (string)($row['pidx'] ?? '');
            if ($unitFalseMode === 'out') {
                if ($rowPidx === (string)$pidx) {
                    throw new Exception('이미 실패처리된 상품입니다.');
                }
                $newOoFalse[] = $row;
            } else { // on
                if ($rowPidx !== (string)$pidx) {
                    $newOoFalse[] = $row;
                }
            }
        }

        $bidxJsonKey = null;
        foreach ($ooJson as $key => $row) {
            if (!is_array($row)) {
                continue;
            }
            if ((string)($row['bidx'] ?? '') === (string)$oopIdx) {
                $bidxJsonKey = $key;
                break;
            }
        }
        if ($bidxJsonKey === null) {
            throw new Exception('주문서 그룹 정보를 찾을 수 없습니다.');
        }

        $selpdRows = [];
        if (isset($ooJson[$bidxJsonKey]['selpd']) && is_array($ooJson[$bidxJsonKey]['selpd'])) {
            $selpdRows = $ooJson[$bidxJsonKey]['selpd'];
        }

        $unitQty = 0;
        $unitPrice = 0.0;
        $unitSumPrice = 0.0;
        foreach ($selpdRows as $selpdKey => $selpdRow) {
            if (!is_array($selpdRow)) {
                continue;
            }
            if ((string)($selpdRow['pidx'] ?? '') !== (string)$pidx) {
                continue;
            }

            if ($unitFalseMode === 'out') {
                $ooJson[$bidxJsonKey]['selpd'][$selpdKey]['false'] = true;
            } else {
                $ooJson[$bidxJsonKey]['selpd'][$selpdKey]['false'] = false;
            }

            $unitQty = (int)($ooJson[$bidxJsonKey]['selpd'][$selpdKey]['qty'] ?? 0);
            $unitPrice = $toFloat($ooJson[$bidxJsonKey]['selpd'][$selpdKey]['price'] ?? 0);
            $unitSumPrice = $unitPrice * $unitQty;
            break;
        }

        $productRow = ProductModel::query()
            ->select(['cd_weight_fn'])
            ->where('CD_IDX', '=', $pidx)
            ->first();
        $productRow = $productRow ? $productRow->toArray() : [];
        $cdWeightData = json_decode($productRow['cd_weight_fn'] ?? '{}', true);
        if (!is_array($cdWeightData)) {
            $cdWeightData = [];
        }

        $cdWeight1 = $toFloat($cdWeightData['1'] ?? 0);
        $cdWeight2 = $toFloat($cdWeightData['2'] ?? 0);
        $cdWeight3 = $toFloat($cdWeightData['3'] ?? 0);
        $weight = ($cdWeight3 > 0) ? $cdWeight3 : max($cdWeight1, $cdWeight2);
        $unitSumWeight = $weight * $unitQty;

        $oldFalse = (int)($ooJson[$bidxJsonKey]['false'] ?? 0);
        $oldFalseSumQty = (int)($ooJson[$bidxJsonKey]['false_sum_qty'] ?? 0);
        $oldFalseSumPrice = $toFloat($ooJson[$bidxJsonKey]['false_sum_price'] ?? 0);
        $oldFalseSumWeight = $toFloat($ooJson[$bidxJsonKey]['false_sum_weight'] ?? 0);

        $ooSumGoods = (int)($orderRow['oo_sum_goods'] ?? 0);
        $ooSumQty = (int)($orderRow['oo_sum_qty'] ?? 0);
        $ooSumWeight = $toFloat($orderRow['oo_sum_weight'] ?? 0);
        $ooSumPrice = $toFloat($orderRow['oo_sum_price'] ?? 0);

        if ($unitFalseMode === 'out') {
            $newOoFalse[] = [
                'pidx' => (string)$pidx,
                'price' => $unitPrice,
                'qty' => $unitQty,
                'memo' => $pidxMemo,
            ];

            $ooSumGoods = $ooSumGoods - 1;
            $ooSumQty = $ooSumQty - $unitQty;
            $ooSumWeight = $ooSumWeight - $unitSumWeight;
            $ooSumPrice = $ooSumPrice - $unitSumPrice;

            $ooJson[$bidxJsonKey]['false'] = ($oldFalse > 0) ? ($oldFalse + 1) : 1;
            $ooJson[$bidxJsonKey]['false_sum_qty'] = ($oldFalseSumQty > 0) ? ($oldFalseSumQty + $unitQty) : $unitQty;
            $ooJson[$bidxJsonKey]['false_sum_price'] = ($oldFalseSumPrice > 0) ? ($oldFalseSumPrice + $unitSumPrice) : $unitSumPrice;
            $ooJson[$bidxJsonKey]['false_sum_weight'] = ($oldFalseSumWeight > 0) ? ($oldFalseSumWeight + $unitSumWeight) : $unitSumWeight;
        } else { // on
            $ooSumGoods = $ooSumGoods + 1;
            $ooSumQty = $ooSumQty + $unitQty;
            $ooSumWeight = $ooSumWeight + $unitSumWeight;
            $ooSumPrice = $ooSumPrice + $unitSumPrice;

            $ooJson[$bidxJsonKey]['false'] = ($oldFalse > 0) ? ($oldFalse - 1) : 0;
            $ooJson[$bidxJsonKey]['false_sum_qty'] = ($oldFalseSumQty > 0) ? ($oldFalseSumQty - $unitQty) : 0;
            $ooJson[$bidxJsonKey]['false_sum_price'] = ($oldFalseSumPrice > 0) ? ($oldFalseSumPrice - $unitSumPrice) : 0;
            $ooJson[$bidxJsonKey]['false_sum_weight'] = ($oldFalseSumWeight > 0) ? ($oldFalseSumWeight - $unitSumWeight) : 0;
        }

        $beforeData = $this->getOrderSheetForLog($ooIdx);
        OrderSheetModel::query()
            ->where('oo_idx', '=', $ooIdx)
            ->update([
                'oo_json' => json_encode($ooJson, JSON_UNESCAPED_UNICODE),
                'oo_false' => count($newOoFalse) > 0 ? json_encode($newOoFalse, JSON_UNESCAPED_UNICODE) : '[]',
                'oo_sum_goods' => $ooSumGoods,
                'oo_sum_qty' => $ooSumQty,
                'oo_sum_weight' => $ooSumWeight,
                'oo_sum_price' => $ooSumPrice,
            ]);
        $afterData = $this->getOrderSheetForLog($ooIdx);
        $this->writeOrderSheetActionLog(
            $ooIdx,
            'update_order_sheet_product_false',
            $unitFalseMode === 'out' ? '주문서 상품 실패 처리' : '주문서 상품 실패 복원',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }


    /**
     * 주문서 상품 주문가격 등록
     * 
     * @param array $data
     * @return array
     */
    public function orderSheetProductNewPrice(array $data)
    {
        $cdIdx = $data['cd_idx'] ?? '';
        $priceRaw = $data['price'] ?? '0';
        $oopCode = $data['oop_code'] ?? '';
        $regMode = $data['reg_mode'] ?? 'newprice';
        $currencyCode = strtoupper(trim((string)($data['currency_code'] ?? '')));

        if (
            $cdIdx === '' ||
            $oopCode === '' ||
            ($regMode !== 'newprice' && $regMode !== 'newinvoiceprice' && $regMode !== 'newpayprice')
        ) {
            throw new Exception('필수 값이 누락되었습니다.');
        }
        if ($regMode === 'newpayprice' && !in_array($currencyCode, ['CNY', 'USD', 'JPY'], true)) {
            throw new Exception('지원하지 않는 통화코드입니다.');
        }

        $price = (float)preg_replace('/[,\s]/', '', (string)$priceRaw);
        if ($price <= 0) {
            throw new Exception('가격을 입력해주세요.');
        }

        $row = ProductModel::query()
            ->select(['CD_IDX', 'cd_price_fn', 'cd_price_history'])
            ->where('CD_IDX', '=', $cdIdx)
            ->first();
        $row = $row ? $row->toArray() : [];
        if (empty($row)) {
            throw new Exception('상품 정보를 찾을 수 없습니다.');
        }

        $cdPriceData = json_decode($row['cd_price_fn'] ?? '{}', true);
        if (!is_array($cdPriceData)) {
            $cdPriceData = [];
        }

        // 레거시 orderSheet_price_new 동작과 동일하게 신규 등록 이력은 단건으로 저장
        $priceHistory = [
            [
                'reg_mode' => $regMode,
                'oop_code' => $oopCode,
                'price' => $price,
                'date' => date('Y-m-d H:i:s'),
                'id' => AuthAdmin::getSession('sess_id'),
                'ip' => AuthAdmin::getIp(),
            ]
        ];

        if ($regMode === 'newinvoiceprice') {
            if (!isset($cdPriceData['invoice']) || !is_array($cdPriceData['invoice'])) {
                $cdPriceData['invoice'] = [];
            }
            $cdPriceData['invoice'][$oopCode] = $price;
        } elseif ($regMode === 'newpayprice') {
            if (!isset($cdPriceData['currency']) || !is_array($cdPriceData['currency'])) {
                $cdPriceData['currency'] = [];
            }
            if (!isset($cdPriceData['currency'][$currencyCode]) || !is_array($cdPriceData['currency'][$currencyCode])) {
                $cdPriceData['currency'][$currencyCode] = [];
            }
            $cdPriceData['currency'][$currencyCode][$oopCode] = $price;
        } else {
            $cdPriceData[$oopCode] = $price;
        }

        ProductModel::query()
            ->where('CD_IDX', '=', $cdIdx)
            ->update([
                'cd_price_fn' => json_encode($cdPriceData, JSON_UNESCAPED_UNICODE),
                'cd_price_history' => json_encode($priceHistory, JSON_UNESCAPED_UNICODE),
            ]);

        return [
            'success' => true,
            'msg' => '완료',
            'uprice' => $price,
        ];
    }


    /**
     * 주문서 상품 가격 변경
     * 
     * @param array $data
     * @return array
     */
    public function orderSheetProductPriceChange(array $data)
    {
        $cdIdx = $data['cd_idx'] ?? '';
        $valueRaw = $data['value'] ?? '0';
        $modMode = $data['mod_mode'] ?? '';
        $oopCode = $data['oop_code'] ?? '';
        $currencyCode = strtoupper(trim((string)($data['currency_code'] ?? '')));

        if ($cdIdx === '' || $oopCode === '' || ($modMode !== 'price' && $modMode !== 'invoicePrice' && $modMode !== 'payPrice')) {
            throw new Exception('필수 값이 누락되었습니다.');
        }
        if ($modMode === 'payPrice' && !in_array($currencyCode, ['CNY', 'USD', 'JPY'], true)) {
            throw new Exception('지원하지 않는 통화코드입니다.');
        }

        $price = (float)preg_replace('/[,\s]/', '', (string)$valueRaw);

        $row = ProductModel::query()
            ->select(['CD_IDX', 'cd_price_fn', 'cd_price_history'])
            ->where('CD_IDX', '=', $cdIdx)
            ->first();
        $row = $row ? $row->toArray() : [];
        if (empty($row)) {
            throw new Exception('상품 정보를 찾을 수 없습니다.');
        }

        $cdPriceData = json_decode($row['cd_price_fn'] ?? '{}', true);
        if (!is_array($cdPriceData)) {
            $cdPriceData = [];
        }
        $priceHistory = json_decode($row['cd_price_history'] ?? '[]', true);
        if (!is_array($priceHistory)) {
            $priceHistory = [];
        }

        $ordPrice = 0;
        if ($modMode === 'price') {
            $ordPrice = (float)($cdPriceData[$oopCode] ?? 0);
        } elseif ($modMode === 'invoicePrice') {
            if (!isset($cdPriceData['invoice']) || !is_array($cdPriceData['invoice'])) {
                $cdPriceData['invoice'] = [];
            }
            $ordPrice = (float)($cdPriceData['invoice'][$oopCode] ?? 0);
        } else { // payPrice
            if (!isset($cdPriceData['currency']) || !is_array($cdPriceData['currency'])) {
                $cdPriceData['currency'] = [];
            }
            if (!isset($cdPriceData['currency'][$currencyCode]) || !is_array($cdPriceData['currency'][$currencyCode])) {
                $cdPriceData['currency'][$currencyCode] = [];
            }
            $ordPrice = (float)($cdPriceData['currency'][$currencyCode][$oopCode] ?? 0);
        }

        $priceHistory[] = [
            'mod_mode' => $modMode,
            'oop_code' => $oopCode,
            'currency_code' => $currencyCode,
            'price' => $price,
            'ord_price' => $ordPrice,
            'date' => date('Y-m-d H:i:s'),
            'id' => AuthAdmin::getSession('sess_id'),
            'ip' => AuthAdmin::getIp(),
        ];

        if ($modMode === 'price') {
            $cdPriceData[$oopCode] = $price;
        } elseif ($modMode === 'invoicePrice') {
            $cdPriceData['invoice'][$oopCode] = $price;
        } else { // payPrice
            $cdPriceData['currency'][$currencyCode][$oopCode] = $price;
        }

        ProductModel::query()
            ->where('CD_IDX', '=', $cdIdx)
            ->update([
                'cd_price_fn' => json_encode($cdPriceData, JSON_UNESCAPED_UNICODE),
                'cd_price_history' => json_encode($priceHistory, JSON_UNESCAPED_UNICODE),
            ]);

        return [
            'success' => true,
            'msg' => '완료',
            'uprice' => $price,
        ];
    }

    /**
     * 주문서 상품 그룹 이동
     * 
     * @param array $data
     * @return array
     */
    public function orderSheetProductMoveGroup(array $data): array
    {
        $ooIdx = (int)($data['oo_idx'] ?? 0);
        $fromOopIdx = trim((string)($data['oop_idx'] ?? ''));
        $toOopIdx = trim((string)($data['to_oop_idx'] ?? ''));
        $pidx = trim((string)($data['pidx'] ?? ''));

        if ($ooIdx <= 0 || $fromOopIdx === '' || $toOopIdx === '' || $pidx === '') {
            throw new Exception('필수 값이 누락되었습니다.');
        }
        if ($fromOopIdx === $toOopIdx) {
            throw new Exception('동일한 그룹으로는 이동할 수 없습니다.');
        }

        $decodeOopData = static function ($raw): array {
            $text = trim((string)$raw);
            if ($text === '') {
                return [];
            }
            $decoded = json_decode($text, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            $wrapped = json_decode('[' . $text . ']', true);
            return is_array($wrapped) ? $wrapped : [];
        };

        $orderGroupRows = OrderGroupProductModel::query()
            ->select(['oop_idx', 'oop_data'])
            ->whereIn('oop_idx', [$fromOopIdx, $toOopIdx])
            ->get()
            ->toArray();
        if (count($orderGroupRows) < 2) {
            throw new Exception('이동 대상 그룹 정보를 찾을 수 없습니다.');
        }

        $groupMap = [];
        foreach ($orderGroupRows as $row) {
            $groupMap[(string)($row['oop_idx'] ?? '')] = $row;
        }
        if (!isset($groupMap[$fromOopIdx]) || !isset($groupMap[$toOopIdx])) {
            throw new Exception('이동 대상 그룹 정보를 찾을 수 없습니다.');
        }

        $fromData = $decodeOopData($groupMap[$fromOopIdx]['oop_data'] ?? '');
        $toData = $decodeOopData($groupMap[$toOopIdx]['oop_data'] ?? '');

        $movingRow = null;
        foreach ($fromData as $key => $row) {
            if (!is_array($row)) {
                continue;
            }
            if ((string)($row['idx'] ?? '') === $pidx) {
                $movingRow = $row;
                unset($fromData[$key]);
                break;
            }
        }
        if ($movingRow === null) {
            throw new Exception('이동할 상품을 찾을 수 없습니다.');
        }
        $fromData = array_values($fromData);

        // 대상 그룹 중복 제거 후 최상단 삽입
        $toFiltered = [];
        foreach ($toData as $row) {
            if (!is_array($row)) {
                continue;
            }
            if ((string)($row['idx'] ?? '') === $pidx) {
                continue;
            }
            $toFiltered[] = $row;
        }
        array_unshift($toFiltered, $movingRow);
        $toData = $toFiltered;

        OrderGroupProductModel::query()
            ->where('oop_idx', '=', $fromOopIdx)
            ->update([
                'oop_data' => json_encode($fromData, JSON_UNESCAPED_UNICODE),
            ]);
        OrderGroupProductModel::query()
            ->where('oop_idx', '=', $toOopIdx)
            ->update([
                'oop_data' => json_encode($toData, JSON_UNESCAPED_UNICODE),
            ]);

        // 주문 selpd 데이터가 있으면 동일하게 그룹 이동
        $orderRow = OrderSheetModel::query()
            ->select(['oo_idx', 'oo_json'])
            ->where('oo_idx', '=', $ooIdx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];
        if (!empty($orderRow)) {
            $ooJson = json_decode((string)($orderRow['oo_json'] ?? '[]'), true);
            if (!is_array($ooJson)) {
                $ooJson = [];
            }

            $fromGroupKey = null;
            $toGroupKey = null;
            foreach ($ooJson as $key => $groupRow) {
                if (!is_array($groupRow)) {
                    continue;
                }
                $bidx = (string)($groupRow['bidx'] ?? '');
                if ($bidx === $fromOopIdx) {
                    $fromGroupKey = $key;
                } elseif ($bidx === $toOopIdx) {
                    $toGroupKey = $key;
                }
            }

            if ($fromGroupKey !== null && $toGroupKey !== null) {
                $fromSelpd = $ooJson[$fromGroupKey]['selpd'] ?? [];
                $toSelpd = $ooJson[$toGroupKey]['selpd'] ?? [];
                if (!is_array($fromSelpd)) $fromSelpd = [];
                if (!is_array($toSelpd)) $toSelpd = [];

                $movedSelpd = null;
                foreach ($fromSelpd as $selpdKey => $selpdRow) {
                    if (!is_array($selpdRow)) {
                        continue;
                    }
                    if ((string)($selpdRow['pidx'] ?? '') === $pidx) {
                        $movedSelpd = $selpdRow;
                        unset($fromSelpd[$selpdKey]);
                        break;
                    }
                }
                $fromSelpd = array_values($fromSelpd);

                if ($movedSelpd !== null) {
                    $toSelpdFiltered = [];
                    foreach ($toSelpd as $selpdRow) {
                        if (!is_array($selpdRow)) {
                            continue;
                        }
                        if ((string)($selpdRow['pidx'] ?? '') === $pidx) {
                            continue;
                        }
                        $toSelpdFiltered[] = $selpdRow;
                    }
                    array_unshift($toSelpdFiltered, $movedSelpd);
                    $toSelpd = $toSelpdFiltered;

                    $sumBySelpd = static function (array $selpdRows): array {
                        $sumItem = 0;
                        $sumQty = 0;
                        $sumPrice = 0.0;
                        foreach ($selpdRows as $row) {
                            if (!is_array($row)) {
                                continue;
                            }
                            $qty = (float)($row['qty'] ?? 0);
                            if ($qty <= 0) {
                                continue;
                            }
                            $sumItem++;
                            $sumQty += $qty;
                            $sumPrice += ((float)($row['price'] ?? 0) * $qty);
                        }
                        return [
                            'item' => $sumItem,
                            'qty' => $sumQty,
                            'price' => $sumPrice,
                        ];
                    };

                    $fromSum = $sumBySelpd($fromSelpd);
                    $toSum = $sumBySelpd($toSelpd);

                    $ooJson[$fromGroupKey]['selpd'] = $fromSelpd;
                    $ooJson[$toGroupKey]['selpd'] = $toSelpd;

                    $ooJson[$fromGroupKey]['item'] = $fromSum['item'];
                    $ooJson[$fromGroupKey]['qty'] = $fromSum['qty'];
                    $ooJson[$fromGroupKey]['price'] = $fromSum['price'];

                    $ooJson[$toGroupKey]['item'] = $toSum['item'];
                    $ooJson[$toGroupKey]['qty'] = $toSum['qty'];
                    $ooJson[$toGroupKey]['price'] = $toSum['price'];

                    OrderSheetModel::query()
                        ->where('oo_idx', '=', $ooIdx)
                        ->update([
                            'oo_json' => json_encode($ooJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        ]);
                }
            }
        }

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }


    /**
     * 주문서 상품 단종처리
     * 
     * @param array $data
     * @return array
     */
    public function orderSheetProductSoldOut(array $data)
    {
        $oopIdx = $data['oop_idx'] ?? '';
        $soldOutMode = $data['soldoutmode'] ?? '';
        $pidx = (string)($data['pidx'] ?? '');

        if ($oopIdx === '' || $pidx === '' || ($soldOutMode !== 'out' && $soldOutMode !== 'on')) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $orderGroupProduct = OrderGroupProductModel::query()
            ->select(['oop_idx', 'oop_data'])
            ->where('oop_idx', '=', $oopIdx)
            ->first();
        $orderGroupProduct = $orderGroupProduct ? $orderGroupProduct->toArray() : [];
        if (empty($orderGroupProduct)) {
            throw new Exception('주문서 그룹 상품을 찾을 수 없습니다.');
        }

        $oopDataRaw = (string)($orderGroupProduct['oop_data'] ?? '');
        $oopDataTrim = trim($oopDataRaw);
        if ($oopDataTrim === '') {
            $oopJsonData = [];
        } else {
            $oopJsonData = json_decode($oopDataTrim, true);
            if (!is_array($oopJsonData)) {
                $wrapped = '[' . $oopDataTrim . ']';
                $oopJsonData = json_decode($wrapped, true);
                if (!is_array($oopJsonData)) {
                    $oopJsonData = [];
                }
            }
        }

        $targetKey = null;
        foreach ($oopJsonData as $key => $row) {
            if (is_array($row) && (string)($row['idx'] ?? '') === $pidx) {
                $targetKey = $key;
                break;
            }
        }

        $cdIdx = '';
        if ($targetKey !== null && isset($oopJsonData[$targetKey]) && is_array($oopJsonData[$targetKey])) {

            // 단종처리
            if ($soldOutMode === 'out') {
                $oopJsonData[$targetKey]['state'] = 'out';
                $isDiscontinued = 1;
            } else { // 단종해제
                $oopJsonData[$targetKey]['state'] = 'on';
                $isDiscontinued = 0;
            }
            $cdIdx = (string)($oopJsonData[$targetKey]['idx'] ?? '');
        } else {
            throw new Exception('대상 상품을 찾을 수 없습니다.');
        }

        OrderGroupProductModel::query()
            ->where('oop_idx', '=', $oopIdx)
            ->update([
                'oop_data' => json_encode($oopJsonData, JSON_UNESCAPED_UNICODE),
            ]);

        if ($cdIdx !== '') {
            $productService = new ProductService();
            try {
                if ((int)$isDiscontinued === 1) {
                    $productService->setProductDiscontinued([
                        'prd_idx' => (int)$cdIdx,
                    ]);
                } else {
                    $productService->unsetProductDiscontinued([
                        'prd_idx' => (int)$cdIdx,
                    ]);
                }
            } catch (Exception $e) {
                $message = (string)$e->getMessage();
                // 이미 동일 상태인 경우는 주문서 처리 흐름을 끊지 않는다.
                if (strpos($message, '이미 단종 처리된 상품입니다.') === false
                    && strpos($message, '이미 단종 해제된 상품입니다.') === false) {
                    throw $e;
                }
            }
        }

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }

    /**
     * 주문서 그룹 상품 노출 순서 변경 저장
     * 
     * (order_sheet_detail_product 화면의 tr 순서 저장)
     *
     * @param array $data
     * @return array
     */
    public function orderSheetProductOrderChange(array $data): array
    {
        $oopIdx = trim((string)($data['oop_idx'] ?? ''));
        $orderedIdx = $data['ordered_idx'] ?? [];

        if ($oopIdx === '') {
            throw new Exception('그룹 번호가 없습니다.');
        }
        if (!is_array($orderedIdx) || empty($orderedIdx)) {
            throw new Exception('변경할 순서 데이터가 없습니다.');
        }

        $normalizedOrder = [];
        foreach ($orderedIdx as $rowIdx) {
            $rowIdx = trim((string)$rowIdx);
            if ($rowIdx === '') {
                continue;
            }
            if (!isset($normalizedOrder[$rowIdx])) {
                $normalizedOrder[$rowIdx] = true;
            }
        }
        $orderedList = array_keys($normalizedOrder);
        if (empty($orderedList)) {
            throw new Exception('유효한 순서 데이터가 없습니다.');
        }

        $orderGroupProduct = OrderGroupProductModel::query()
            ->select(['oop_idx', 'oop_data'])
            ->where('oop_idx', '=', $oopIdx)
            ->first();
        $orderGroupProduct = $orderGroupProduct ? $orderGroupProduct->toArray() : [];
        if (empty($orderGroupProduct)) {
            throw new Exception('주문서 그룹 상품을 찾을 수 없습니다.');
        }

        $oopDataRaw = trim((string)($orderGroupProduct['oop_data'] ?? ''));
        if ($oopDataRaw === '') {
            $oopJsonData = [];
        } else {
            $oopJsonData = json_decode($oopDataRaw, true);
            if (!is_array($oopJsonData)) {
                $oopJsonData = json_decode('[' . $oopDataRaw . ']', true);
                if (!is_array($oopJsonData)) {
                    $oopJsonData = [];
                }
            }
        }

        if (empty($oopJsonData)) {
            throw new Exception('주문서 그룹 상품 데이터가 비어있습니다.');
        }

        $rowByIdx = [];
        foreach ($oopJsonData as $row) {
            if (!is_array($row)) {
                continue;
            }
            $idx = trim((string)($row['idx'] ?? ''));
            if ($idx === '') {
                continue;
            }
            if (!isset($rowByIdx[$idx])) {
                $rowByIdx[$idx] = $row;
            }
        }

        $reordered = [];
        $used = [];
        foreach ($orderedList as $idx) {
            if (!isset($rowByIdx[$idx])) {
                continue;
            }
            $reordered[] = $rowByIdx[$idx];
            $used[$idx] = true;
        }
        foreach ($oopJsonData as $row) {
            if (!is_array($row)) {
                continue;
            }
            $idx = trim((string)($row['idx'] ?? ''));
            if ($idx !== '' && isset($used[$idx])) {
                continue;
            }
            $reordered[] = $row;
        }

        OrderGroupProductModel::query()
            ->where('oop_idx', '=', $oopIdx)
            ->update([
                'oop_data' => json_encode($reordered, JSON_UNESCAPED_UNICODE),
            ]);

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }

}