<?php

namespace App\Services;

use Exception;
use App\Core\BaseClass;
use App\Models\ProductStockModel;
use App\Models\ProductStockUnitModel;
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

}
