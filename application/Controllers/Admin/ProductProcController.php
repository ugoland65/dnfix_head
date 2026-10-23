<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;

use App\Models\ProductStockModel;
use App\Models\RackModel;
use App\Services\AdminActionLogService;

/**
 * 상품 처리 컨트롤러
 */
class ProductProcController extends BaseClass 
{

    /**
     * 랙코드 일괄변경
     * 
     * @param Request $request
     * @return json
     */
    public function rackChangeBatch(Request $request)
    {

        try {

            // 요청 데이터 가져오기
            $checkIdx = $request->input('check_idx', []);
            $rackCode = $request->input('rack_code', '');

            // 유효성 검증
            if (empty($checkIdx) || !is_array($checkIdx)) {
                throw new Exception('선택된 상품이 없습니다.');
            }

            $rackCode = trim((string)$rackCode);
            if ($rackCode === '') {
                throw new Exception('랙코드를 입력해주세요.');
            }
            if (function_exists('mb_strlen') && mb_strlen($rackCode, 'UTF-8') > 100) {
                throw new Exception('랙 코드는 100자 이하로 입력해주세요.');
            }

            // 체크값은 상품번호(CD_IDX)다. 재고번호(ps_idx)로 갱신하면 다른 상품이 바뀐다.
            $prdIdxList = array_values(array_unique(array_filter(array_map('intval', $checkIdx), static function ($value) {
                return $value > 0;
            })));

            if (empty($prdIdxList)) {
                throw new Exception('유효한 상품이 선택되지 않았습니다.');
            }

            $stocks = ProductStockModel::query()
                ->whereIn('ps_prd_idx', $prdIdxList)
                ->get()
                ->toArray();
            if (empty($stocks)) {
                throw new Exception('선택한 상품의 재고 정보를 찾을 수 없습니다.');
            }

            $rackInfo = RackModel::where('code', $rackCode)->first();
            if (empty($rackInfo)) {
                RackModel::create([
                    'code' => $rackCode,
                    'name' => $rackCode,
                    'memo' => '',
                    'prd' => '',
                ]);
            }

            $actionLog = new AdminActionLogService();
            $updatedCount = 0;
            foreach ($stocks as $stock) {
                if (!is_array($stock)) {
                    $stock = $stock->toArray();
                }
                $psIdx = (int)($stock['ps_idx'] ?? 0);
                $prdIdx = (int)($stock['ps_prd_idx'] ?? 0);
                if ($psIdx <= 0 || $prdIdx <= 0) {
                    continue;
                }

                $beforeRack = (string)($stock['ps_rack_code'] ?? '');
                if ($beforeRack === $rackCode) {
                    continue;
                }

                ProductStockModel::query()
                    ->where('ps_idx', '=', $psIdx)
                    ->update([
                        'ps_rack_code' => $rackCode,
                    ]);
                $updatedCount++;

                $beforeData = ['ps_rack_code' => $beforeRack];
                $afterData = ['ps_rack_code' => $rackCode];
                try {
                    $actionLog->log([
                        'target_type' => 'product',
                        'target_table' => 'prd_stock',
                        'target_pk' => (string)$prdIdx,
                        'action_mode' => 'rack_code_update',
                        'action_summary' => '랙코드 일괄변경',
                        'before_json' => $beforeData,
                        'after_json' => $afterData,
                        'diff_json' => $actionLog->buildDiff($beforeData, $afterData),
                        'action_url' => $_SERVER['REQUEST_URI'] ?? null,
                        'source' => 'admin',
                    ]);
                } catch (Throwable $e) {
                }
            }

            $message = $updatedCount > 0
                ? $updatedCount . '개 상품의 랙코드가 변경되었습니다.'
                : '변경된 랙코드가 없습니다.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'updated_count' => $updatedCount
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

}