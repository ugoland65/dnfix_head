<?php

namespace App\Services;

use Exception;
use App\Core\BaseClass;
use App\Models\ProductStockModel;
use App\Models\ProductStockUnitModel;
use App\Models\OrderPrdUnitModel;
use App\Services\ProductService;
use App\Core\AuthAdmin;
use App\Services\AdminActionLogService;
class ProductStockService extends BaseClass 
{

    /**
     * 재고코드 생성
     * @param array $requestData
     * @return 
     */
    public function createStockCode($data)
    {
        $prd_idx = $data['prd_idx'] ?? null;

        if( empty($prd_idx) ){
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $productStock = ProductStockModel::where('ps_prd_idx', $prd_idx)->first();
        if( !empty($productStock) ){
            throw new Exception('이미 재고코드가 있습니다.');
        }

        $now = date('Y-m-d H:i:s');
        $insertData = [
            'ps_prd_idx' => $prd_idx,
            'ps_rack_code' => '',
            'ps_stock' => 0,
            'ps_stock_hold' => 0,
            'ps_stock_all' => 0,
            'ps_income' => null,
            'ps_last_in' => null,
            'ps_update_date' => $now,
            'ps_in_date' => null,
            'ps_last_date' => $now,
            'ps_soldout_date' => $now,
            'ps_sale_date' => date('Y-m-d'),
            'ps_sale_log' => '',
            'ps_sale_data' => '{}',
            'ps_in_sale_s' => $now,
            'ps_in_sale_e' => $now,
            'ps_in_sale_data' => '',
            'ps_stock_object' => 'Y',
            'ps_alarm_count' => 0,
            'ps_alarm_message' => '',
            'ps_mode' => 'basic',
            'ps_kind' => '',
            'ps_name' => '',
            'ps_set_value' => '',
            'ps_alarm_yn' => 'N',
            'ps_cafe24_sms' => '',
            'is_sale_month' => 0,
            'is_sale_special' => 0,
        ];

        ProductStockModel::create($insertData);



        $adminActionLogService = new AdminActionLogService();
        $adminActionLogService->log([
            'target_type' => 'product',
            'target_table' => 'prd_stock',
            'target_pk' => (string)($prd_idx ?? ''),
            'action_mode' => 'create_stock_code',
            'action_summary' => '재고코드 생성',
            'action_url' => $_SERVER['REQUEST_URI'] ?? null,
        ]);

        return true;

    }


    /**
     * 상품 재고 Where In 조회
     * @param array $idxs
     * @return array
     */
    public function getProductStockWhereIn($ids) 
    {
        
        // 빈 배열이 전달된 경우 빈 배열 반환
        if (empty($ids)) {
            return [];
        }

        $prdStockList = ProductStockModel::select([
                'prd_stock.ps_idx', 'prd_stock.ps_rack_code', 'prd_stock.ps_stock',  'prd_stock.is_sale_month',
                'cd.CD_IDX', 'cd.CD_CODE', 'cd.CD_NAME', 'cd.cd_cost_price', 'cd.cd_size_fn', 'cd.cd_add_img', 'cd.img_mode', 'cd.CD_IMG',
            ])
            ->join('COMPARISON_DB as cd', 'prd_stock.ps_prd_idx', '=', 'cd.CD_IDX', 'LEFT')
            ->whereIn('prd_stock.ps_idx', $ids)
            ->get()
            ->keyBy('ps_idx')
            ->toArray();

        $productService = new ProductService();

        foreach ($prdStockList as &$prdStock) {
            $prdStock['cd_size_fn'] = json_decode($prdStock['cd_size_fn'] ?? '{}', true);
            $prdStock['cd_add_img'] = json_decode($prdStock['cd_add_img'] ?? '{}', true);
            if (!is_array($prdStock['cd_size_fn'])) {
                $prdStock['cd_size_fn'] = [];
            }

            $_cd_size_w = (float)($prdStock['cd_size_fn']['package']['W'] ?? 0);
            $_cd_size_h = (float)($prdStock['cd_size_fn']['package']['H'] ?? 0);
            $_cd_size_d = (float)($prdStock['cd_size_fn']['package']['D'] ?? 0);

            if( !empty($_cd_size_w) || !empty($_cd_size_h) || !empty($_cd_size_d) ){
                $_cd_size_volume = $_cd_size_w * $_cd_size_h * $_cd_size_d;
                $prdStock['package_volume'] = $_cd_size_volume;
                $prdStock['package_volume_m3'] = $_cd_size_volume / 1000000;
                $prdStock['package_volume_level'] = $productService->getVolumeLevel($_cd_size_volume);
            }else{
                $prdStock['package_volume'] = 0;
                $prdStock['package_volume_m3'] = 0;
                $prdStock['package_volume_level'] = 0;    
            }

        }
        unset($prdStock);

        return $prdStockList;
    }

    /**
     * 재고코드 로 상품 재고 1건 조회
     * 
     * @param string $code
     * @return array
     */
    public function getProductStockWhereInCode($code)
    {
        $psIdx = (int)trim((string)$code);
        if ($psIdx <= 0) {
            return [];
        }

        $row = ProductStockModel::select([
                'prd_stock.ps_idx',
                'prd_stock.ps_prd_idx',
                'prd_stock.ps_stock',
                'cd.CD_IDX',
                'cd.CD_CODE',
                'cd.CD_NAME',
            ])
            ->join('COMPARISON_DB as cd', 'prd_stock.ps_prd_idx', '=', 'cd.CD_IDX', 'LEFT')
            ->where('prd_stock.ps_idx', $psIdx)
            ->first();

        if (empty($row)) {
            return [];
        }

        return $row->toArray();
    }

    /**
     * 재고 변경 멱등성 토큰 처리 여부 확인
     *
     * @param string $stockToken
     * @return bool
     */
    public function hasStockChangeToken(string $stockToken): bool
    {
        $stockToken = trim($stockToken);
        if ($stockToken === '') {
            return false;
        }

        return ProductStockUnitModel::query()
            ->where('psu_token', '=', $stockToken)
            ->exists();
    }

    /**
     * 상품 재고 변경 등록
     * - 입/출고, 보류 전환 및 보류 재고 입/출고를 처리하고 재고 이력을 남긴다.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function registerStockChange(array $data): array
    {
        $psIdx = (int)($data['ps_idx'] ?? 0);
        $stockMode = trim((string)($data['stock_mode'] ?? ''));
        $stockKind = trim((string)($data['stock_kind'] ?? '조정'));
        $stockMemo = trim((string)($data['stock_memo'] ?? ''));
        $stockDay = trim((string)($data['stock_day'] ?? date('Y-m-d')));
        $stockQtyRaw = trim((string)($data['stock_qty'] ?? ''));
        $stockToken = trim((string)($data['psu_token'] ?? ''));

        if ($psIdx <= 0) {
            throw new Exception('재고코드가 올바르지 않습니다.');
        }
        if (!in_array($stockMode, ['plus', 'minus', 'to_hold', 'to_stock', 'plus_hold', 'minus_hold'], true)) {
            throw new Exception('재고 변경 종류가 올바르지 않습니다.');
        }
        if ($stockQtyRaw === '' || !preg_match('/^\d+$/', $stockQtyRaw)) {
            throw new Exception('수량을 입력해 주세요.');
        }
        if ($stockDay === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $stockDay)) {
            throw new Exception('날짜 형식이 올바르지 않습니다.');
        }

        $stockQty = (int)$stockQtyRaw;
        if ($stockToken !== '' && $this->hasStockChangeToken($stockToken)) {
            return $this->buildAlreadyProcessedStockChangeResult($psIdx);
        }

        $stock = ProductStockModel::query()
            ->where('ps_idx', '=', $psIdx)
            ->first();
        if (empty($stock)) {
            throw new Exception('상품 재고가 존재하지 않습니다.');
        }
        $stock = is_array($stock) ? $stock : $stock->toArray();

        $beforeStock = (int)($stock['ps_stock'] ?? 0);
        $beforeStockHold = (int)($stock['ps_stock_hold'] ?? 0);
        $beforeStockAll = (int)($stock['ps_stock_all'] ?? 0);
        $afterStock = $beforeStock;
        $afterStockHold = $beforeStockHold;
        $afterStockAll = $beforeStockAll;

        switch ($stockMode) {
            case 'plus':
                $afterStock += $stockQty;
                if ($stockKind === '신규입고') {
                    $afterStockAll += $stockQty;
                }
                break;
            case 'minus':
                $afterStock -= $stockQty;
                break;
            case 'to_hold':
                $afterStock -= $stockQty;
                $afterStockHold += $stockQty;
                break;
            case 'to_stock':
                $afterStock += $stockQty;
                $afterStockHold -= $stockQty;
                break;
            case 'plus_hold':
                $afterStockHold += $stockQty;
                break;
            case 'minus_hold':
                $afterStockHold -= $stockQty;
                break;
        }

        $now = date('Y-m-d H:i:s');
        $adminId = (string)(AuthAdmin::getSession('sess_id') ?? '');
        $adminName = (string)(AuthAdmin::getSession('sess_name') ?? '');
        $reg = json_encode([
            'reg' => [
                'mode' => 'prd_info',
                'info' => AuthAdmin::getConnectionInfo(),
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $unitRows = [];
        $appendUnitRow = function (string $mode, int $quantity, int $resultStock, ?string $token = null) use (&$unitRows, $psIdx, $stockDay, $stockKind, $stockMemo, $adminId, $now, $reg): void {
            $unitRows[] = [
                'psu_stock_idx' => $psIdx,
                'psu_day' => $stockDay,
                'psu_mode' => $mode,
                'psu_qry' => $quantity,
                'psu_stock' => $resultStock,
                'psu_kind' => $stockKind,
                'psu_memo' => $stockMemo,
                'psu_token' => $token,
                'psu_id' => $adminId,
                'psu_date' => time(),
                'reg' => $reg,
            ];
        };

        if ($stockMode === 'to_hold') {
            $appendUnitRow('minus_to_hold', $stockQty, $afterStock, $stockToken !== '' ? $stockToken . ':stock' : null);
            $appendUnitRow('to_hold', $stockQty, $afterStockHold, $stockToken !== '' ? $stockToken . ':hold' : null);
        } elseif ($stockMode === 'to_stock') {
            $appendUnitRow('plus_to_stock', $stockQty, $afterStock, $stockToken !== '' ? $stockToken . ':stock' : null);
            $appendUnitRow('to_stock', $stockQty, $afterStockHold, $stockToken !== '' ? $stockToken . ':hold' : null);
        } else {
            $unitResultStock = in_array($stockMode, ['plus_hold', 'minus_hold'], true) ? $afterStockHold : $afterStock;
            $appendUnitRow($stockMode, $stockQty, $unitResultStock, $stockToken !== '' ? $stockToken : null);
        }

        $connection = app('db');
        $ownsTransaction = $connection instanceof \PDO && !$connection->inTransaction();
        try {
            if ($ownsTransaction) {
                $connection->beginTransaction();
            }

            ProductStockModel::query()
                ->where('ps_idx', '=', $psIdx)
                ->update([
                    'ps_stock' => $afterStock,
                    'ps_stock_hold' => $afterStockHold,
                    'ps_stock_all' => $afterStockAll,
                    'ps_update_date' => $now,
                ]);

            foreach ($unitRows as $unitRow) {
                ProductStockUnitModel::query()->insert($unitRow);
            }

            if ($ownsTransaction && $connection->inTransaction()) {
                $connection->commit();
            }
        } catch (\Throwable $e) {
            if ($ownsTransaction && $connection instanceof \PDO && $connection->inTransaction()) {
                $connection->rollBack();
            }
            if (
                $stockToken !== ''
                && $e instanceof \PDOException
                && (
                    (string)$e->getCode() === '23000'
                    || stripos($e->getMessage(), 'duplicate') !== false
                )
            ) {
                return $this->buildAlreadyProcessedStockChangeResult($psIdx);
            }
            throw $e;
        }

        $afterData = [
            'ps_idx' => $psIdx,
            'ps_prd_idx' => (int)($stock['ps_prd_idx'] ?? 0),
            'ps_stock' => $afterStock,
            'ps_stock_hold' => $afterStockHold,
            'ps_stock_all' => $afterStockAll,
        ];
        try {
            $adminActionLogService = new AdminActionLogService();
            $beforeData = [
                'ps_idx' => $psIdx,
                'ps_prd_idx' => (int)($stock['ps_prd_idx'] ?? 0),
                'ps_stock' => $beforeStock,
                'ps_stock_hold' => $beforeStockHold,
                'ps_stock_all' => $beforeStockAll,
            ];
            $adminActionLogService->log([
                'target_type' => 'product',
                'target_table' => 'prd_stock',
                'target_pk' => (string)($stock['ps_prd_idx'] ?? ''),
                'action_mode' => 'register_stock_change',
                'action_summary' => '재고 변경등록 (' . $stockMode . ', ' . $stockQty . ')',
                'before_json' => $beforeData,
                'after_json' => $afterData,
                'diff_json' => $adminActionLogService->buildDiff($beforeData, $afterData),
                'action_url' => $_SERVER['REQUEST_URI'] ?? null,
            ]);
        } catch (\Throwable $e) {
            // 액션 로그 실패는 재고 처리 성공 여부에 영향을 주지 않는다.
        }

        return [
            'success' => true,
            'message' => '재고 변경이 완료되었습니다.',
            'stock' => $afterStock,
            'stock_hold' => $afterStockHold,
            'stock_all' => $afterStockAll,
            'ps_idx' => $psIdx,
        ];
    }

    /**
     * 멱등성 토큰이 이미 처리된 경우의 현재 재고 응답 생성
     *
     * @param int $psIdx
     * @return array
     */
    private function buildAlreadyProcessedStockChangeResult(int $psIdx): array
    {
        $currentStock = ProductStockModel::query()
            ->select(['ps_stock', 'ps_stock_hold', 'ps_stock_all'])
            ->where('ps_idx', '=', $psIdx)
            ->first();
        $currentStock = $currentStock ? (is_array($currentStock) ? $currentStock : $currentStock->toArray()) : [];

        return [
            'success' => true,
            'message' => '이미 처리된 재고 변경입니다.',
            'already_processed' => true,
            'stock' => (int)($currentStock['ps_stock'] ?? 0),
            'stock_hold' => (int)($currentStock['ps_stock_hold'] ?? 0),
            'stock_all' => (int)($currentStock['ps_stock_all'] ?? 0),
            'ps_idx' => $psIdx,
        ];
    }

    /**
     * 상품 재고 이력의 종류와 메모를 수정한다.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function updateStockChangeRecord(array $data): array
    {
        $unitIdx = (int)($data['idx'] ?? 0);
        $stockKind = trim((string)($data['stock_kind'] ?? ''));
        $stockMemo = trim((string)($data['stock_memo'] ?? ''));

        if ($unitIdx <= 0) {
            throw new Exception('재고 이력 번호가 올바르지 않습니다.');
        }

        $unit = ProductStockUnitModel::query()
            ->where('psu_idx', '=', $unitIdx)
            ->first();
        if (empty($unit)) {
            throw new Exception('재고 이력을 찾을 수 없습니다.');
        }
        $unit = is_array($unit) ? $unit : $unit->toArray();

        ProductStockUnitModel::query()
            ->where('psu_idx', '=', $unitIdx)
            ->update([
                'psu_kind' => $stockKind,
                'psu_memo' => $stockMemo,
            ]);

        return [
            'success' => true,
            'message' => '재고 이력이 수정되었습니다.',
            'idx' => $unitIdx,
        ];
    }


    /**
     * 상품 세일 설정
     * @param array $requestData
     * @return array
     */
    public function setProductSale($data)
    {

        $prd_idx = $data['prd_idx'] ?? null;
        $ps_idx = $data['ps_idx'] ?? null;
        $mode = $data['mode'] ?? null;

        if( empty($ps_idx) || empty($mode) ){
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $query = ProductStockModel::find($ps_idx);
        
        if( empty($query) ){
            throw new Exception('상품 재고가 존재하지 않습니다.');
        }

        $productStock = $query->toArray();

        $updateData = [];
        $message = '처리가 완료되었습니다.';

        $sale_data = json_decode($productStock['ps_sale_data'] ?? '{}', true);
        if (!is_array($sale_data)) {
            $sale_data = [];
        }

        $isMonthly = !empty($productStock['is_sale_month']);
        $isSpecial = !empty($productStock['is_sale_special']);

        if( $mode == 'monthly' ){
            if ($isSpecial) {
                $updateData['is_sale_special'] = 0;
                $sale_data['special']['off'] = [
                    'date' => date('Y-m-d'),
                    'reg' => AuthAdmin::getConnectionInfo()
                ];
                $message = '이미 특가할인중입니다. 특가할인을 해제하고 월간할인으로 지정합니다.';
            }
            $updateData['is_sale_month'] = 1;
        }

        if( $mode == 'special' ){
            if ($isMonthly) {
                $updateData['is_sale_month'] = 0;
                $sale_data['monthly']['off'] = [
                    'date' => date('Y-m-d'),
                    'reg' => AuthAdmin::getConnectionInfo()
                ];
                $message = '이미 월간할인중입니다. 월간할인을 해제하고 특가할인으로 지정합니다.';
            }
            $updateData['is_sale_special'] = 1;
        }

        $sale_data[$mode]['on'] = [
            'date' => date('Y-m-d'),
            'reg' => AuthAdmin::getConnectionInfo()
        ];
        $ps_sale_data = json_encode($sale_data, JSON_UNESCAPED_UNICODE);

        $updateData['ps_sale_data'] = $ps_sale_data;

        $beforeData = [
            'ps_idx' => (string)($productStock['ps_idx'] ?? ''),
            'ps_prd_idx' => (string)($productStock['ps_prd_idx'] ?? ''),
            'is_sale_month' => (int)($productStock['is_sale_month'] ?? 0),
            'is_sale_special' => (int)($productStock['is_sale_special'] ?? 0),
            'ps_sale_data' => (string)($productStock['ps_sale_data'] ?? ''),
        ];
        $afterData = array_merge($beforeData, [
            'is_sale_month' => isset($updateData['is_sale_month']) ? (int)$updateData['is_sale_month'] : (int)$beforeData['is_sale_month'],
            'is_sale_special' => isset($updateData['is_sale_special']) ? (int)$updateData['is_sale_special'] : (int)$beforeData['is_sale_special'],
            'ps_sale_data' => (string)$updateData['ps_sale_data'],
        ]);

        $result = ProductStockModel::where('ps_idx', $ps_idx)->update($updateData);

        if ($result) {
            $targetPrdIdx = !empty($prd_idx) ? $prd_idx : ($productStock['ps_prd_idx'] ?? null);
            $adminActionLogService = new AdminActionLogService();
            $diff = $adminActionLogService->buildDiff($beforeData, $afterData);
            $adminActionLogService->log([
                'target_type' => 'product',
                'target_table' => 'prd_stock',
                'target_pk' => (string)($targetPrdIdx ?? ''),
                'action_mode' => 'set_product_sale',
                'action_summary' => '상품 세일 설정 (' . $mode . ')',
                'before_json' => $beforeData,
                'after_json' => $afterData,
                'diff_json' => $diff,
                'action_url' => $_SERVER['REQUEST_URI'] ?? null,
            ]);
        }

        return [
            'success' => (bool)$result,
            'message' => $message,
        ];


    }


    /**
     * 상품 세일 해제
     * 
     * @param array $requestData
     * @return array
     */
    public function unsetProductSale($data)
    {
        $prd_idx = $data['prd_idx'] ?? null;
        $ps_idx = $data['ps_idx'] ?? null;
        $mode = $data['mode'] ?? null;

        if( empty($ps_idx) || empty($mode) ){
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $query = ProductStockModel::find($ps_idx);
        
        if( empty($query) ){
            throw new Exception('상품 재고가 존재하지 않습니다.');
        }

        $productStock = $query->toArray();

        $updateData = [];
        if( $mode == 'monthly' ){
            $updateData['is_sale_month'] = 0;
        }

        if( $mode == 'special' ){
            $updateData['is_sale_special'] = 0;
        }

        $sale_data = json_decode($productStock['ps_sale_data'] ?? '{}', true);
        if (!is_array($sale_data)) {
            $sale_data = [];
        }

        if (!isset($sale_data[$mode]) || !is_array($sale_data[$mode])) {
            $sale_data[$mode] = [];
        }

        $sale_data[$mode]['off'] = [
            'date' => date('Y-m-d'),
            'reg' => AuthAdmin::getConnectionInfo()
        ];

        $updateData['ps_sale_data'] = json_encode($sale_data, JSON_UNESCAPED_UNICODE);

        $beforeData = [
            'ps_idx' => (string)($productStock['ps_idx'] ?? ''),
            'ps_prd_idx' => (string)($productStock['ps_prd_idx'] ?? ''),
            'is_sale_month' => (int)($productStock['is_sale_month'] ?? 0),
            'is_sale_special' => (int)($productStock['is_sale_special'] ?? 0),
            'ps_sale_data' => (string)($productStock['ps_sale_data'] ?? ''),
        ];
        $afterData = array_merge($beforeData, [
            'is_sale_month' => isset($updateData['is_sale_month']) ? (int)$updateData['is_sale_month'] : (int)$beforeData['is_sale_month'],
            'is_sale_special' => isset($updateData['is_sale_special']) ? (int)$updateData['is_sale_special'] : (int)$beforeData['is_sale_special'],
            'ps_sale_data' => (string)$updateData['ps_sale_data'],
        ]);

        $result = ProductStockModel::where('ps_idx', $ps_idx)->update($updateData);

        if ($result) {
            $targetPrdIdx = !empty($prd_idx) ? $prd_idx : ($productStock['ps_prd_idx'] ?? null);
            $adminActionLogService = new AdminActionLogService();
            $diff = $adminActionLogService->buildDiff($beforeData, $afterData);
            $adminActionLogService->log([
                'target_type' => 'product',
                'target_table' => 'prd_stock',
                'target_pk' => (string)($targetPrdIdx ?? ''),
                'action_mode' => 'unset_product_sale',
                'action_summary' => '상품 세일 해제 (' . $mode . ')',
                'before_json' => $beforeData,
                'after_json' => $afterData,
                'diff_json' => $diff,
                'action_url' => $_SERVER['REQUEST_URI'] ?? null,
            ]);
        }

        return [
            'success' => (bool)$result,
            'message' => '처리가 완료되었습니다.',
        ];
    }

    /**
     * 상품 팝업 - 판매량/발주 요약
     *
     * @param int $prdIdx
     * @param int $psIdx
     * @param string $showMode 연간통계|월간통계
     * @param mixed $curY
     * @param mixed $curM
     * @return array
     */
    public function getStockChartPageData(int $prdIdx, int $psIdx, string $showMode = '연간통계', $curY = null, $curM = null): array
    {
        $nowY = (int)date('Y');
        $nowM = (int)date('n');

        $year = (int)($curY !== null && $curY !== '' ? $curY : $nowY);
        if ($year < 2000 || $year > 2100) {
            $year = $nowY;
        }

        $month = (int)($curM !== null && $curM !== '' ? $curM : $nowM);
        if ($month < 1 || $month > 12) {
            $month = $nowM;
        }

        if (!in_array($showMode, ['연간통계', '월간통계'], true)) {
            $showMode = '연간통계';
        }

        $yearlyRows = [];
        $weeklyRows = [];
        $avgAll = 0;
        $avgExcludeCurrent = 0;
        $monthSoldOutInfo = [
            'is_soldout_month' => false,
            'soldout_date_text' => '',
            'soldout_period_text' => '',
            'soldout_items' => [],
        ];

        if ($showMode === '연간통계') {
            $monthCount = ($year < $nowY) ? 12 : $nowM;
            $startTs = mktime(0, 0, 0, 1, 1, $year);
            $endDay = (int)date('t', mktime(0, 0, 0, $monthCount, 1, $year));
            $endTs = mktime(23, 59, 59, $monthCount, $endDay, $year);
            $units = $this->getStockUnitsInRange($psIdx, $startTs, $endTs);
            $prevUnit = $this->getLastAvailableStockUnitBefore($psIdx, $startTs);
            $timelineEndDay = ($year === $nowY)
                ? date('Y-m-d')
                : sprintf('%04d-%02d-%02d', $year, $monthCount, $endDay);
            $soldOutPeriods = $this->buildSoldOutPeriods($units, $prevUnit, $timelineEndDay);

            $byMonth = [];
            for ($m = 1; $m <= $monthCount; $m++) {
                $byMonth[$m] = [
                    'in_stock_count' => 0,
                    'in_stock' => 0,
                    'sale_stock' => 0,
                ];
            }

            foreach ($units as $unit) {
                $unitMonth = (int)date('n', (int)($unit['psu_date'] ?? 0));
                if (!isset($byMonth[$unitMonth])) {
                    continue;
                }

                $qty = (int)($unit['psu_qry'] ?? 0);
                if ($this->isNewInboundUnit($unit)) {
                    $byMonth[$unitMonth]['in_stock_count']++;
                    $byMonth[$unitMonth]['in_stock'] += $qty;
                }
                if ($this->isSaleUnit($unit)) {
                    $byMonth[$unitMonth]['sale_stock'] += $qty;
                }
            }

            $avgSum = 0;
            $avgCount = 0;
            $avgExcludeSum = 0;
            $avgExcludeCount = 0;

            for ($i = 0; $i < $monthCount; $i++) {
                $thisMonth = $monthCount - $i;
                $row = $byMonth[$thisMonth];
                $soldOutInfo = $this->buildMonthSoldOutInfo(
                    $year,
                    $thisMonth,
                    (int)$row['in_stock'],
                    $soldOutPeriods,
                    $timelineEndDay
                );
                $yearlyRows[] = array_merge([
                    'year' => $year,
                    'month' => $thisMonth,
                    'in_stock_count' => $row['in_stock_count'],
                    'in_stock' => $row['in_stock'],
                    'sale_stock' => $row['sale_stock'],
                ], $soldOutInfo);

                if (!empty($soldOutInfo['is_soldout_month'])) {
                    continue;
                }

                $avgCount++;
                $avgSum += $row['sale_stock'];
                if ($thisMonth !== $nowM) {
                    $avgExcludeCount++;
                    $avgExcludeSum += $row['sale_stock'];
                }
            }

            $avgAll = $avgCount > 0 ? round($avgSum / $avgCount, 1) : 0;
            $avgExcludeCurrent = $avgExcludeCount > 0 ? round($avgExcludeSum / $avgExcludeCount, 1) : 0;
            $yearlyRows = $this->enrichYearlyStatRows($yearlyRows, $year, $nowY, $nowM);
        } else {
            $weeks = $this->findWeeksInMonth(sprintf('%04d-%02d-01', $year, $month));
            $weekUnits = [];
            if (!empty($weeks)) {
                $firstStart = explode('-', (string)($weeks[0]['start'] ?? ''));
                $lastEnd = explode('-', (string)($weeks[count($weeks) - 1]['end'] ?? ''));
                if (count($firstStart) >= 3 && count($lastEnd) >= 3) {
                    $startTs = mktime(0, 0, 0, (int)$firstStart[1], (int)$firstStart[2], (int)$firstStart[0]);
                    $endTs = mktime(23, 59, 59, (int)$lastEnd[1], (int)$lastEnd[2], (int)$lastEnd[0]);
                    $weekUnits = $this->getStockUnitsInRange($psIdx, $startTs, $endTs);
                }
            }

            $weekNum = 0;
            foreach ($weeks as $week) {
                $weekNum++;
                $weekStart = (string)($week['start'] ?? '');
                $weekEnd = (string)($week['end'] ?? '');
                $startParts = explode('-', $weekStart);
                $endParts = explode('-', $weekEnd);
                if (count($startParts) < 3 || count($endParts) < 3) {
                    continue;
                }

                $weekStartTs = mktime(0, 0, 0, (int)$startParts[1], (int)$startParts[2], (int)$startParts[0]);
                $weekEndTs = mktime(23, 59, 59, (int)$endParts[1], (int)$endParts[2], (int)$endParts[0]);
                $saleStock = 0;
                foreach ($weekUnits as $unit) {
                    $unitTs = (int)($unit['psu_date'] ?? 0);
                    if ($unitTs < $weekStartTs || $unitTs > $weekEndTs) {
                        continue;
                    }
                    if ($this->isSaleUnit($unit)) {
                        $saleStock += (int)($unit['psu_qry'] ?? 0);
                    }
                }

                $weeklyRows[] = [
                    'week_num' => $weekNum,
                    'start' => $weekStart,
                    'end' => $weekEnd,
                    'sale_stock' => $saleStock,
                ];
            }
            $weeklyRows = $this->enrichWeeklyStatRows($weeklyRows);

            $monthStartTs = mktime(0, 0, 0, $month, 1, $year);
            $monthEndDayNum = (int)date('t', $monthStartTs);
            $monthUnits = $this->getStockUnitsInRange(
                $psIdx,
                $monthStartTs,
                mktime(23, 59, 59, $month, $monthEndDayNum, $year)
            );
            $monthLookaheadEnd = ($year === $nowY && $month === $nowM)
                ? date('Y-m-d')
                : sprintf('%04d-%02d-%02d', $year, $month, $monthEndDayNum);
            $monthSoldOutInfo = $this->buildMonthSoldOutInfo(
                $year,
                $month,
                $this->sumNewInboundQty($monthUnits),
                $this->buildSoldOutPeriods(
                    $monthUnits,
                    $this->getLastAvailableStockUnitBefore($psIdx, $monthStartTs),
                    $monthLookaheadEnd
                ),
                $monthLookaheadEnd
            );
        }

        $orderRows = $this->getRecentOrderHistoryByPrdIdx($prdIdx, 5);
        $inboundRows = $this->getRecentInboundSaleRows($psIdx, 10);

        return [
            'prd_idx' => $prdIdx,
            'ps_idx' => $psIdx,
            'show_mode' => $showMode,
            'cur_y' => $year,
            'cur_m' => $month,
            'current_month' => $nowM,
            'yearly_rows' => $yearlyRows,
            'weekly_rows' => $weeklyRows,
            'month_soldout_info' => $monthSoldOutInfo,
            'avg_all' => $avgAll,
            'avg_exclude_current' => $avgExcludeCurrent,
            'order_rows' => $orderRows,
            'inbound_rows' => $inboundRows,
            'insight' => $this->buildStockChartInsight($psIdx, $inboundRows, $orderRows),
        ];
    }

    /**
     * 상품 최근 발주 이력
     *
     * @param int $prdIdx
     * @param int $limit
     * @return array
     */
    private function getRecentOrderHistoryByPrdIdx(int $prdIdx, int $limit = 5): array
    {
        if ($prdIdx <= 0) {
            return [];
        }

        $rows = OrderPrdUnitModel::query()
            ->from('order_prd_unit as U')
            ->leftJoin('ona_order as O', 'O.oo_idx', '=', 'U.order_idx')
            ->leftJoin('ona_order_prd as G', 'G.oop_idx', '=', 'U.bidx')
            ->leftJoin(
                'order_sheet_product_memos as M',
                'M.oo_idx',
                '=',
                'U.order_idx',
                'M.oop_idx = U.bidx AND M.pidx = U.pidx'
            )
            ->select([
                'U.idx',
                'U.order_idx',
                'U.bidx',
                'U.pidx',
                'U.order_qty',
                'U.is_order_failed',
                'O.oo_name',
                'O.oo_in_date',
                'O.oo_date_data',
                'O.oo_state',
                'G.oop_name',
                'M.memo as product_memo',
            ])
            ->where('U.pidx', '=', $prdIdx)
            ->where('U.order_qty', '>', 0)
            ->where('U.is_order_failed', '=', 0)
            ->orderBy('U.idx', 'DESC')
            ->limit($limit)
            ->get()
            ->toArray();

        if (!is_array($rows) || empty($rows)) {
            return [];
        }

        $result = [];
        foreach ($rows as $row) {
            if (!empty($row['is_order_failed'])) {
                continue;
            }

            $orderIdx = (int)($row['order_idx'] ?? 0);
            $orderName = trim((string)($row['oo_name'] ?? ''));
            if ($orderName === '') {
                $orderName = trim((string)($row['oop_name'] ?? ''));
            }

            $stateChanged = $this->extractLatestStateChange(
                $row['oo_date_data'] ?? null,
                (int)($row['oo_state'] ?? 0)
            );
            $bidx = (int)($row['bidx'] ?? 0);
            $pidx = (int)($row['pidx'] ?? 0);
            $orderUrl = '';
            if ($orderIdx > 0) {
                $orderUrl = '/admin/order/sheet?idx=' . $orderIdx;
                if ($bidx > 0) {
                    $orderUrl .= '&bidx=' . $bidx;
                }
                if ($pidx > 0) {
                    $orderUrl .= '&pidx=' . $pidx;
                }
            }

            $result[] = [
                'order_idx' => $orderIdx,
                'order_name' => $orderName,
                'order_state' => (int)($row['oo_state'] ?? 0),
                'order_state_text' => $this->getOrderSheetStateText((int)($row['oo_state'] ?? 0)),
                'state_changed_at' => $stateChanged['date'],
                'state_changed_name' => $stateChanged['name'],
                'bidx' => $bidx,
                'pidx' => $pidx,
                'in_date' => $this->formatOrderDate((string)($row['oo_in_date'] ?? '')),
                'end_date' => $this->extractOrderEndDate($row['oo_date_data'] ?? null),
                'order_qty' => (int)($row['order_qty'] ?? 0),
                'order_memo' => html_entity_decode(trim((string)($row['product_memo'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'order_url' => $orderUrl,
                'inbound_gap_days' => 0,
            ];
        }

        $rowCount = count($result);
        for ($i = 0; $i < $rowCount - 1; $i++) {
            $newer = (string)($result[$i]['in_date'] ?? '');
            $older = (string)($result[$i + 1]['in_date'] ?? '');
            if ($newer !== '' && $older !== '' && $older <= $newer) {
                $result[$i]['inbound_gap_days'] = $this->diffDays($older, $newer);
            }
        }

        return $result;
    }

    /**
     * 주문서 상태 표시명
     *
     * @param int $state
     * @return string
     */
    private function getOrderSheetStateText(int $state): string
    {
        $map = [
            1 => '작성중',
            2 => '주문전송',
            4 => '입금완료',
            5 => '입고완료',
            7 => '주문종료',
        ];

        return $map[$state] ?? '';
    }

    /**
     * 현재 상태의 마지막 변경 이력
     *
     * @param mixed $dateData
     * @param int $currentState
     * @return array{date:string,name:string}
     */
    private function extractLatestStateChange($dateData, int $currentState): array
    {
        if (is_string($dateData) && $dateData !== '') {
            $dateData = json_decode($dateData, true);
        }
        if (!is_array($dateData)) {
            return ['date' => '', 'name' => ''];
        }

        $states = $dateData['state'] ?? [];
        if (!is_array($states)) {
            return ['date' => '', 'name' => ''];
        }

        for ($i = count($states) - 1; $i >= 0; $i--) {
            $stateLog = $states[$i] ?? [];
            if (!is_array($stateLog)) {
                continue;
            }
            if ((int)($stateLog['state_after'] ?? 0) !== $currentState) {
                continue;
            }

            $rawDate = trim((string)($stateLog['date'] ?? ''));
            $timestamp = $rawDate !== '' ? strtotime($rawDate) : false;
            $dateText = ($timestamp !== false) ? date('y.m.d H:i', $timestamp) : $rawDate;

            return [
                'date' => $dateText,
                'name' => trim((string)($stateLog['name'] ?? '')),
            ];
        }

        return ['date' => '', 'name' => ''];
    }

    /**
     * 주문서 주문종료일
     *
     * @param mixed $dateData
     * @return string
     */
    private function extractOrderEndDate($dateData): string
    {
        if (is_string($dateData) && $dateData !== '') {
            $dateData = json_decode($dateData, true);
        }
        if (!is_array($dateData)) {
            return '';
        }

        $states = $dateData['state'] ?? [];
        if (!is_array($states)) {
            return '';
        }

        $endDate = '';
        foreach ($states as $state) {
            if (!is_array($state)) {
                continue;
            }
            if ((int)($state['state_after'] ?? 0) === 7) {
                $endDate = $this->formatOrderDate((string)($state['date'] ?? ''));
            }
        }

        return $endDate;
    }

    /**
     * 주문서 날짜 표시값
     *
     * @param string $value
     * @return string
     */
    private function formatOrderDate(string $value): string
    {
        $value = trim($value);
        if ($value === '' || strpos($value, '0000-00-00') === 0) {
            return '';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return $value;
        }

        return date('Y-m-d', $timestamp);
    }

    /**
     * 기간 내 재고 이력
     *
     * @param int $psIdx
     * @param int $startTs
     * @param int $endTs
     * @return array
     */
    private function getStockUnitsInRange(int $psIdx, int $startTs, int $endTs): array
    {
        if ($psIdx <= 0) {
            return [];
        }

        $rows = ProductStockUnitModel::query()
            ->select(['psu_idx', 'psu_day', 'psu_mode', 'psu_kind', 'psu_qry', 'psu_stock', 'psu_date', 'psu_memo'])
            ->where('psu_stock_idx', '=', $psIdx)
            ->where('psu_date', '>=', $startTs)
            ->where('psu_date', '<=', $endTs)
            ->orderBy('psu_idx', 'ASC')
            ->get()
            ->toArray();

        return is_array($rows) ? $rows : [];
    }

    /**
     * 가용재고 이력만 사용 (보류 재고 이력 제외)
     *
     * @param array $unit
     * @return bool
     */
    private function isAvailableStockUnit(array $unit): bool
    {
        return in_array((string)($unit['psu_mode'] ?? ''), ['plus', 'minus', 'minus_to_hold', 'plus_to_stock'], true);
    }

    /**
     * 기준 시각 직전 가용재고
     *
     * @param int $psIdx
     * @param int $beforeTs
     * @return array
     */
    private function getLastAvailableStockUnitBefore(int $psIdx, int $beforeTs): array
    {
        if ($psIdx <= 0) {
            return [];
        }

        $rows = ProductStockUnitModel::query()
            ->select(['psu_idx', 'psu_day', 'psu_mode', 'psu_kind', 'psu_qry', 'psu_stock', 'psu_date'])
            ->where('psu_stock_idx', '=', $psIdx)
            ->where('psu_date', '<', $beforeTs)
            ->whereIn('psu_mode', ['plus', 'minus', 'minus_to_hold', 'plus_to_stock'])
            ->orderBy('psu_idx', 'DESC')
            ->limit(1)
            ->get()
            ->toArray();

        return (is_array($rows) && !empty($rows[0]) && is_array($rows[0])) ? $rows[0] : [];
    }

    /**
     * 재고 이력의 업무일
     *
     * @param array $unit
     * @return string
     */
    private function getStockUnitDay(array $unit): string
    {
        $day = trim((string)($unit['psu_day'] ?? ''));
        if ($day !== '' && strpos($day, '0000-00-00') !== 0) {
            return $day;
        }

        $ts = (int)($unit['psu_date'] ?? 0);
        return $ts > 0 ? date('Y-m-d', $ts) : '';
    }

    /**
     * 두 날짜 사이 일수
     *
     * @param string $startDay
     * @param string $endDay
     * @return int
     */
    private function diffDays(string $startDay, string $endDay): int
    {
        if ($startDay === '' || $endDay === '') {
            return 0;
        }

        try {
            return (int)(new \DateTime($startDay))->diff(new \DateTime($endDay))->days;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * 신규입고 수량 합계
     *
     * @param array $units
     * @return int
     */
    private function sumNewInboundQty(array $units): int
    {
        $sum = 0;
        foreach ($units as $unit) {
            if (is_array($unit) && $this->isNewInboundUnit($unit)) {
                $sum += (int)($unit['psu_qry'] ?? 0);
            }
        }

        return $sum;
    }

    /**
     * 가용재고 이력으로 품절 구간을 만든다.
     * - 시작: psu_stock <= 0
     * - 종료: 이후 psu_stock > 0 인 날(입고일)
     *
     * @param array $units
     * @param array $prevUnit
     * @param string $timelineEndDay
     * @return array
     */
    private function buildSoldOutPeriods(array $units, array $prevUnit, string $timelineEndDay): array
    {
        $availableUnits = [];
        foreach ($units as $unit) {
            if (is_array($unit) && $this->isAvailableStockUnit($unit)) {
                $availableUnits[] = $unit;
            }
        }

        usort($availableUnits, static function ($left, $right) {
            return ((int)($left['psu_idx'] ?? 0)) <=> ((int)($right['psu_idx'] ?? 0));
        });

        $soldOut = !empty($prevUnit) && (int)($prevUnit['psu_stock'] ?? 0) <= 0;
        $soldOutStart = $soldOut ? $this->getStockUnitDay($prevUnit) : '';
        $periods = [];

        foreach ($availableUnits as $unit) {
            $day = $this->getStockUnitDay($unit);
            if ($day === '') {
                continue;
            }

            $stock = (int)($unit['psu_stock'] ?? 0);
            if (!$soldOut && $stock <= 0) {
                $soldOut = true;
                $soldOutStart = $day;
                continue;
            }

            if ($soldOut && $stock > 0) {
                if ($soldOutStart !== '' && $soldOutStart <= $day) {
                    $periods[] = [
                        'start' => $soldOutStart,
                        'end' => $day,
                    ];
                }
                $soldOut = false;
                $soldOutStart = '';
            }
        }

        if ($soldOut && $soldOutStart !== '') {
            $endDay = $timelineEndDay !== '' ? $timelineEndDay : $soldOutStart;
            if ($soldOutStart <= $endDay) {
                $periods[] = [
                    'start' => $soldOutStart,
                    'end' => $endDay,
                ];
            }
        }

        return $periods;
    }

    /**
     * 해당 월과 겹치는 품절 구간만 표시한다.
     *
     * @param int $year
     * @param int $month
     * @param int $inStockQty
     * @param array $soldOutPeriods
     * @param string $timelineEndDay
     * @return array
     */
    private function buildMonthSoldOutInfo(
        int $year,
        int $month,
        int $inStockQty,
        array $soldOutPeriods,
        string $timelineEndDay
    ): array {
        $monthStart = sprintf('%04d-%02d-01', $year, $month);
        $monthLastDay = (int)date('t', strtotime($monthStart));
        $monthEnd = sprintf('%04d-%02d-%02d', $year, $month, $monthLastDay);
        if ($timelineEndDay !== '' && $timelineEndDay < $monthEnd) {
            $monthEnd = $timelineEndDay;
        }

        $soldOutItems = [];
        foreach ($soldOutPeriods as $period) {
            if (!is_array($period)) {
                continue;
            }

            $clipped = $this->clipSoldOutPeriodToMonth($period, $monthStart, $monthEnd);
            if ($clipped !== null) {
                $soldOutItems[] = $clipped;
            }
        }

        $isSoldOutMonth = $inStockQty <= 0 && $this->isMonthFullyCoveredBySoldOut($monthStart, $monthEnd, $soldOutPeriods);

        $dateTexts = [];
        $periodTexts = [];
        foreach ($soldOutItems as $item) {
            $dateTexts[] = ($item['soldout_date'] !== '') ? $item['soldout_date'] : '이월';
            $periodTexts[] = $item['soldout_start'] . ' ~ ' . $item['soldout_end'] . ' (' . (int)$item['soldout_days'] . '일)';
        }

        return [
            'is_soldout_month' => $isSoldOutMonth,
            'soldout_date_text' => implode("\n", $dateTexts),
            'soldout_period_text' => implode("\n", $periodTexts),
            'soldout_items' => $soldOutItems,
        ];
    }

    /**
     * 품절 구간을 해당 월 날짜로 자른다.
     *
     * @param array $period
     * @param string $monthStart
     * @param string $monthEnd
     * @return array|null
     */
    private function clipSoldOutPeriodToMonth(array $period, string $monthStart, string $monthEnd): ?array
    {
        $start = (string)($period['start'] ?? '');
        $end = (string)($period['end'] ?? '');
        if ($start === '' || $end === '' || $end < $monthStart || $start > $monthEnd) {
            return null;
        }

        $clipStart = $start < $monthStart ? $monthStart : $start;
        $clipEnd = $end > $monthEnd ? $monthEnd : $end;
        if ($clipStart > $clipEnd) {
            return null;
        }

        return [
            'soldout_date' => ($start >= $monthStart && $start <= $monthEnd) ? $start : '',
            'soldout_start' => $clipStart,
            'soldout_end' => $clipEnd,
            'soldout_days' => $this->diffDays($clipStart, $clipEnd) + 1,
        ];
    }

    /**
     * 해당 월 전체가 품절 구간으로 덮였는지
     *
     * @param string $monthStart
     * @param string $monthEnd
     * @param array $periods
     * @return bool
     */
    private function isMonthFullyCoveredBySoldOut(string $monthStart, string $monthEnd, array $periods): bool
    {
        foreach ($periods as $period) {
            if (!is_array($period)) {
                continue;
            }

            $start = (string)($period['start'] ?? '');
            $end = (string)($period['end'] ?? '');
            if ($start === '' || $end === '') {
                continue;
            }

            if ($start <= $monthStart && $end >= $monthEnd) {
                return true;
            }
        }

        return false;
    }

    /**
     * 연간 월별 행에 일판매/소진율/추정미판매를 붙인다.
     *
     * @param array $yearlyRows
     * @param int $year
     * @param int $nowY
     * @param int $nowM
     * @return array
     */
    private function enrichYearlyStatRows(array $yearlyRows, int $year, int $nowY, int $nowM): array
    {
        foreach ($yearlyRows as &$row) {
            $month = (int)($row['month'] ?? 0);
            $monthStart = sprintf('%04d-%02d-01', $year, $month);
            $isCurrentMonth = ($year === $nowY && $month === $nowM);
            $monthDays = $isCurrentMonth ? (int)date('j') : (int)date('t', strtotime($monthStart));

            $soldOutDays = 0;
            foreach (($row['soldout_items'] ?? []) as $item) {
                if (is_array($item)) {
                    $soldOutDays += (int)($item['soldout_days'] ?? 0);
                }
            }
            if ($soldOutDays > $monthDays) {
                $soldOutDays = $monthDays;
            }

            $inStockDays = max(0, $monthDays - $soldOutDays);
            $saleStock = (int)($row['sale_stock'] ?? 0);
            $inStock = (int)($row['in_stock'] ?? 0);
            $dailySale = $inStockDays > 0 ? round($saleStock / $inStockDays, 2) : 0.0;

            $row['month_days'] = $monthDays;
            $row['soldout_days'] = $soldOutDays;
            $row['instock_days'] = $inStockDays;
            $row['daily_sale'] = $dailySale;
            $row['is_current_month'] = $isCurrentMonth;
            $row['lost_sale'] = 0;
            $row['sell_through'] = $inStock > 0 ? round($saleStock / $inStock * 100, 1) : null;
        }
        unset($row);

        $normalSales = [];
        foreach ($yearlyRows as $row) {
            if (!empty($row['is_soldout_month']) || !empty($row['is_current_month'])) {
                continue;
            }
            $normalSales[] = (int)($row['sale_stock'] ?? 0);
        }
        $monthlyAvg = !empty($normalSales) ? (array_sum($normalSales) / count($normalSales)) : 0;
        $baselineDaily = $monthlyAvg > 0 ? round($monthlyAvg / 30, 2) : 0.0;

        foreach ($yearlyRows as &$row) {
            if (!empty($row['is_soldout_month'])) {
                $row['lost_sale'] = 0;
                continue;
            }
            $row['lost_sale'] = $baselineDaily > 0
                ? (int)round($baselineDaily * (int)($row['soldout_days'] ?? 0))
                : 0;
        }
        unset($row);

        return $yearlyRows;
    }

    /**
     * 주간 행에 전주대비를 붙인다.
     *
     * @param array $weeklyRows
     * @return array
     */
    private function enrichWeeklyStatRows(array $weeklyRows): array
    {
        $prevSale = null;
        foreach ($weeklyRows as &$row) {
            $sale = (int)($row['sale_stock'] ?? 0);
            $wow = null;
            if ($prevSale !== null && $prevSale > 0) {
                $wow = round((($sale - $prevSale) / $prevSale) * 100, 1);
            }
            $row['wow'] = $wow;
            $prevSale = $sale;
        }
        unset($row);

        return $weeklyRows;
    }

    /**
     * 월평균 기준 판매/발주 요약과 품절 예상
     * - 품절월(입고 없이 한 달 품절)은 평균·미판매에서 제외
     * - 리드: 주문서 작성 1주 + 입고 1주
     * - 급판매 권장발주는 최근 신규입고 수량(중앙값)을 넘지 않음
     *
     * @param int $psIdx
     * @param array $inboundRows
     * @param array $orderRows
     * @return array
     */
    private function buildStockChartInsight(int $psIdx, array $inboundRows, array $orderRows): array
    {
        $today = date('Y-m-d');
        $recentDays = 28;
        $recentStartDay = date('Y-m-d', strtotime('-' . ($recentDays - 1) . ' days'));
        $leadDays = 14;
        $cycleDays = 30;
        $safetyDays = 0;

        $currentStock = 0;
        $stockRow = ProductStockModel::query()
            ->select(['ps_stock'])
            ->where('ps_idx', '=', $psIdx)
            ->first();
        if ($stockRow) {
            $stockData = method_exists($stockRow, 'toArray') ? $stockRow->toArray() : (array)$stockRow;
            $currentStock = (int)($stockData['ps_stock'] ?? 0);
        }

        $monthStats = $this->getNormalMonthSaleStats($psIdx, 12);
        $monthlyAvg = (float)($monthStats['monthly_avg'] ?? 0);
        $dailyMonth = $monthlyAvg > 0 ? round($monthlyAvg / 30, 2) : 0.0;
        $sampleMonths = (int)($monthStats['sample_months'] ?? 0);
        $lostSale = (int)($monthStats['lost_sale'] ?? 0);

        $recentStartTs = strtotime($recentStartDay . ' 00:00:00');
        $recentUnits = $this->getStockUnitsInRange($psIdx, (int)$recentStartTs, time());
        $recentPrev = $this->getLastAvailableStockUnitBefore($psIdx, (int)$recentStartTs);
        $recentPeriods = $this->buildSoldOutPeriods($recentUnits, $recentPrev, $today);
        $soldOutDays28 = $this->countSoldOutDaysInRange($recentPeriods, $recentStartDay, $today);
        $inStockDays28 = max(0, $recentDays - $soldOutDays28);

        $sales28 = 0;
        foreach ($recentUnits as $unit) {
            if (!is_array($unit) || !$this->isSaleUnit($unit)) {
                continue;
            }
            $day = $this->getStockUnitDay($unit);
            if ($day >= $recentStartDay && $day <= $today) {
                $sales28 += (int)($unit['psu_qry'] ?? 0);
            }
        }

        $daily28 = $inStockDays28 > 0 ? round($sales28 / $inStockDays28, 2) : 0.0;
        $isSurge = $dailyMonth > 0 && $daily28 >= ($dailyMonth * 1.5);
        $useDaily = $isSurge && $daily28 > 0 ? $daily28 : $dailyMonth;
        $horizonDays = $cycleDays + $leadDays + $safetyDays;
        $typicalInbound = $this->getTypicalInboundQty($inboundRows);

        $coverDays = null;
        $soldOutAt = '';
        $forecastText = '';
        if ($currentStock <= 0) {
            $forecastText = '현재 품절입니다.';
        } elseif ($useDaily <= 0) {
            $forecastText = '최근 판매가 없어 품절 시점을 예상할 수 없습니다.';
        } else {
            $coverDays = (int)floor($currentStock / $useDaily);
            $soldOutAt = date('Y-m-d', strtotime('+' . $coverDays . ' days'));
            $dailyLabel = $isSurge ? '최근 ' . $recentDays . '일 일판매' : '월평균 일판매';
            $forecastText = '현재고 ' . $currentStock . '개가 ' . $dailyLabel . ' ' . $useDaily . '개 기준으로 약 ' . $coverDays . '일 후 품절 예정입니다.';
        }

        $baseRecommended = $this->calcRecommendedQty($dailyMonth, $horizonDays, $currentStock);
        $surgeRecommended = $this->calcRecommendedQty($daily28, $horizonDays, $currentStock);
        $systemRecommended = ($isSurge && $surgeRecommended > 0) ? $surgeRecommended : $baseRecommended;
        $recommended = $baseRecommended;
        $recommendedCapped = false;
        if ($isSurge && $surgeRecommended > $baseRecommended) {
            $cap = $typicalInbound > 0
                ? $typicalInbound
                : ($baseRecommended > 0 ? $baseRecommended * 3 : $surgeRecommended);
            $recommended = max($baseRecommended, min($surgeRecommended, $cap));
            $recommendedCapped = $recommended < $surgeRecommended;
        }

        $needOrderSoon = $coverDays !== null && $coverDays <= $leadDays;

        return [
            'current_stock' => $currentStock,
            'lookback_months' => 12,
            'sample_months' => $sampleMonths,
            'monthly_avg' => round($monthlyAvg, 1),
            'recent_days' => $recentDays,
            'daily_90' => $dailyMonth,
            'daily_month' => $dailyMonth,
            'daily_28' => $daily28,
            'use_daily' => $useDaily,
            'is_surge' => $isSurge,
            'lost_sale_90' => $lostSale,
            'lead_days' => $leadDays,
            'cycle_days' => $cycleDays,
            'safety_days' => $safetyDays,
            'typical_inbound' => $typicalInbound,
            'recommended_qty' => $recommended,
            'system_recommended_qty' => $systemRecommended,
            'recommended_capped' => $recommendedCapped,
            'cover_days' => $coverDays,
            'soldout_at' => $soldOutAt,
            'forecast_text' => $forecastText,
            'need_order_soon' => $needOrderSoon,
        ];
    }

    /**
     * 일판매 × 커버일수 − 현재고
     *
     * @param float $dailySale
     * @param int $horizonDays
     * @param int $currentStock
     * @return int
     */
    private function calcRecommendedQty(float $dailySale, int $horizonDays, int $currentStock): int
    {
        if ($dailySale <= 0) {
            return 0;
        }

        $recommended = (int)ceil(($dailySale * $horizonDays) - $currentStock);
        return $recommended > 0 ? $recommended : 0;
    }

    /**
     * 최근 신규입고 수량의 중앙값
     *
     * @param array $inboundRows
     * @return int
     */
    private function getTypicalInboundQty(array $inboundRows): int
    {
        $qtys = [];
        foreach ($inboundRows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $qty = (int)($row['psu_qry'] ?? 0);
            if ($qty > 0) {
                $qtys[] = $qty;
            }
        }

        if (empty($qtys)) {
            return 0;
        }

        sort($qtys);
        return (int)$qtys[(int)floor((count($qtys) - 1) / 2)];
    }

    /**
     * 최근 N개월 중 품절월을 제외한 월평균 판매
     *
     * @param int $psIdx
     * @param int $monthCount
     * @return array
     */
    private function getNormalMonthSaleStats(int $psIdx, int $monthCount = 12): array
    {
        $nowY = (int)date('Y');
        $nowM = (int)date('n');
        $months = [];
        for ($i = 0; $i < $monthCount; $i++) {
            $month = $nowM - $i;
            $year = $nowY;
            while ($month <= 0) {
                $month += 12;
                $year--;
            }
            $months[] = ['year' => $year, 'month' => $month];
        }

        $oldest = $months[count($months) - 1];
        $startTs = mktime(0, 0, 0, (int)$oldest['month'], 1, (int)$oldest['year']);
        $today = date('Y-m-d');
        $units = $this->getStockUnitsInRange($psIdx, $startTs, time());
        $prevUnit = $this->getLastAvailableStockUnitBefore($psIdx, $startTs);
        $periods = $this->buildSoldOutPeriods($units, $prevUnit, $today);

        $normalSales = [];
        $normalMonthSoldOutDays = [];
        foreach ($months as $index => $monthInfo) {
            $year = (int)$monthInfo['year'];
            $month = (int)$monthInfo['month'];
            $monthStart = sprintf('%04d-%02d-01', $year, $month);
            $isCurrent = ($index === 0);
            $monthLastDay = (int)date('t', strtotime($monthStart));
            $monthEnd = $isCurrent ? $today : sprintf('%04d-%02d-%02d', $year, $month, $monthLastDay);

            $inStockQty = 0;
            $saleStock = 0;
            foreach ($units as $unit) {
                if (!is_array($unit)) {
                    continue;
                }
                $day = $this->getStockUnitDay($unit);
                if ($day < $monthStart || $day > $monthEnd) {
                    continue;
                }
                if ($this->isNewInboundUnit($unit)) {
                    $inStockQty += (int)($unit['psu_qry'] ?? 0);
                }
                if ($this->isSaleUnit($unit)) {
                    $saleStock += (int)($unit['psu_qry'] ?? 0);
                }
            }

            $soldOutInfo = $this->buildMonthSoldOutInfo($year, $month, $inStockQty, $periods, $monthEnd);
            if (!empty($soldOutInfo['is_soldout_month'])) {
                continue;
            }

            if (!$isCurrent) {
                $normalSales[] = $saleStock;
            }

            $soldOutDays = 0;
            foreach (($soldOutInfo['soldout_items'] ?? []) as $item) {
                $soldOutDays += (int)($item['soldout_days'] ?? 0);
            }
            $normalMonthSoldOutDays[] = $soldOutDays;
        }

        $monthlyAvg = !empty($normalSales) ? (array_sum($normalSales) / count($normalSales)) : 0;
        $baselineDaily = $monthlyAvg > 0 ? $monthlyAvg / 30 : 0;
        $lostSale = 0;
        foreach ($normalMonthSoldOutDays as $soldOutDays) {
            $lostSale += $baselineDaily > 0 ? (int)round($baselineDaily * $soldOutDays) : 0;
        }

        return [
            'monthly_avg' => $monthlyAvg,
            'sample_months' => count($normalSales),
            'lost_sale' => $lostSale,
        ];
    }

    /**
     * 입고 간격으로 리드일을 추정한다.
     *
     * @param array $inboundRows
     * @param array $orderRows
     * @return int
     */
    private function estimateLeadDays(array $inboundRows, array $orderRows): int
    {
        $gaps = [];
        foreach ($orderRows as $row) {
            $gap = (int)($row['inbound_gap_days'] ?? 0);
            if ($gap > 0 && $gap <= 180) {
                $gaps[] = $gap;
            }
        }

        if (count($gaps) < 2) {
            $inboundDays = [];
            foreach ($inboundRows as $row) {
                $day = trim((string)($row['psu_day'] ?? ''));
                if ($day !== '') {
                    $inboundDays[] = $day;
                }
            }
            sort($inboundDays);
            for ($i = 1, $n = count($inboundDays); $i < $n; $i++) {
                $gap = $this->diffDays($inboundDays[$i - 1], $inboundDays[$i]);
                if ($gap > 0 && $gap <= 180) {
                    $gaps[] = $gap;
                }
            }
        }

        if (empty($gaps)) {
            return 20;
        }

        $leadDays = (int)round(array_sum($gaps) / count($gaps));
        if ($leadDays < 7) {
            return 7;
        }
        if ($leadDays > 90) {
            return 90;
        }

        return $leadDays;
    }

    /**
     * 기간 안 품절 일수
     *
     * @param array $periods
     * @param string $startDay
     * @param string $endDay
     * @return int
     */
    private function countSoldOutDaysInRange(array $periods, string $startDay, string $endDay): int
    {
        $days = 0;
        foreach ($periods as $period) {
            if (!is_array($period)) {
                continue;
            }
            $clipped = $this->clipSoldOutPeriodToMonth($period, $startDay, $endDay);
            if ($clipped !== null) {
                $days += (int)($clipped['soldout_days'] ?? 0);
            }
        }

        $maxDays = $this->diffDays($startDay, $endDay) + 1;
        if ($days > $maxDays) {
            return $maxDays;
        }

        return $days;
    }

    /**
     * 최근 신규입고와 입고 구간별 판매량
     *
     * @param int $psIdx
     * @param int $limit
     * @return array
     */
    private function getRecentInboundSaleRows(int $psIdx, int $limit = 10): array
    {
        if ($psIdx <= 0) {
            return [];
        }

        $inbounds = ProductStockUnitModel::query()
            ->select(['psu_idx', 'psu_day', 'psu_qry', 'psu_memo'])
            ->where('psu_kind', '=', '신규입고')
            ->where('psu_mode', '=', 'plus')
            ->where('psu_stock_idx', '=', $psIdx)
            ->orderBy('psu_idx', 'DESC')
            ->limit($limit)
            ->get()
            ->toArray();

        if (!is_array($inbounds) || empty($inbounds)) {
            return [];
        }

        $oldestDay = '';
        foreach ($inbounds as $inbound) {
            $day = trim((string)($inbound['psu_day'] ?? ''));
            if ($day === '') {
                continue;
            }
            if ($oldestDay === '' || $day < $oldestDay) {
                $oldestDay = $day;
            }
        }

        $saleUnits = [];
        if ($oldestDay !== '') {
            $oldestParts = explode('-', $oldestDay);
            if (count($oldestParts) >= 3) {
                $startTs = mktime(0, 0, 0, (int)$oldestParts[1], (int)$oldestParts[2], (int)$oldestParts[0]);
                $endTs = mktime(23, 59, 59, (int)date('n'), (int)date('j'), (int)date('Y'));
                $saleUnits = $this->getStockUnitsInRange($psIdx, $startTs, $endTs);
            }
        }

        $rows = [];
        $periodEnd = date('Y-m-d');
        foreach ($inbounds as $inbound) {
            $psuDay = trim((string)($inbound['psu_day'] ?? ''));
            if ($psuDay === '') {
                $psuDay = date('Y-m-d');
            }

            $dayCount = 0;
            try {
                $from = new \DateTime($psuDay);
                $to = new \DateTime($periodEnd);
                $dayCount = (int)$from->diff($to)->days;
            } catch (\Exception $e) {
                $dayCount = 0;
            }

            $startParts = explode('-', $psuDay);
            $endParts = explode('-', $periodEnd);
            $saleStock = 0;
            if (count($startParts) >= 3 && count($endParts) >= 3) {
                $startTs = mktime(0, 0, 0, (int)$startParts[1], (int)$startParts[2], (int)$startParts[0]);
                $endTs = mktime(23, 59, 59, (int)$endParts[1], (int)$endParts[2], (int)$endParts[0]);
                foreach ($saleUnits as $unit) {
                    $unitTs = (int)($unit['psu_date'] ?? 0);
                    if ($unitTs < $startTs || $unitTs > $endTs) {
                        continue;
                    }
                    if ($this->isSaleUnit($unit)) {
                        $saleStock += (int)($unit['psu_qry'] ?? 0);
                    }
                }
            }

            $qty = (int)($inbound['psu_qry'] ?? 0);
            $dailySale = $dayCount > 0 ? round($saleStock / $dayCount, 2) : 0;
            $sellThrough = $qty > 0 ? round($saleStock / $qty * 100, 1) : 0;

            $rows[] = [
                'psu_day' => $psuDay,
                'psu_memo' => (string)($inbound['psu_memo'] ?? ''),
                'psu_qry' => $qty,
                'period_text' => $psuDay . ' ~ ' . $periodEnd,
                'period_days' => $dayCount,
                'sale_stock' => $saleStock,
                'daily_sale' => $dailySale,
                'sell_through' => $sellThrough,
            ];

            $periodEnd = $psuDay;
        }

        return $rows;
    }

    /**
     * 해당 월의 ISO 주차(월~일)
     *
     * @param string $date Y-m-d
     * @return array
     */
    private function findWeeksInMonth(string $date): array
    {
        if ($date === '') {
            return [];
        }

        $day = date('w', strtotime($date));
        if ((int)$day !== 1) {
            $date = date('Y-m-d', strtotime('next monday', strtotime($date)));
        }

        $startWeek = (int)date('W', strtotime($date));
        $year = (int)date('Y', strtotime($date));
        $lastDay = date('Y-m-t', strtotime($date));
        $lastWeek = (int)date('W', strtotime($lastDay));

        $result = [];
        for ($week = $startWeek; $week <= $lastWeek; $week++) {
            $result[] = $this->getIsoWeekRange($week, $year);
        }

        return $result;
    }

    /**
     * ISO 주차의 월요일~일요일
     *
     * @param int $week
     * @param int $year
     * @return array{start:string,end:string}
     */
    private function getIsoWeekRange(int $week, int $year): array
    {
        $dateTime = new \DateTime();

        return [
            'start' => $dateTime->setISODate($year, $week, 1)->format('Y-m-d'),
            'end' => $dateTime->setISODate($year, $week, 7)->format('Y-m-d'),
        ];
    }

    /**
     * @param array $unit
     * @return bool
     */
    private function isNewInboundUnit(array $unit): bool
    {
        return ($unit['psu_mode'] ?? '') === 'plus' && ($unit['psu_kind'] ?? '') === '신규입고';
    }

    /**
     * @param array $unit
     * @return bool
     */
    private function isSaleUnit(array $unit): bool
    {
        $kind = (string)($unit['psu_kind'] ?? '');

        return ($unit['psu_mode'] ?? '') === 'minus' && ($kind === '판매' || $kind === '판매 (엑셀)');
    }

}
