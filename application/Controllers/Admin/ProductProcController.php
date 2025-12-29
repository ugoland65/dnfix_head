<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;

use App\Models\ProductStockModel;
use App\Models\RackModel;

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

            if (empty($rackCode)) {
                throw new Exception('랙코드를 입력해주세요.');
            }

            // 배열 값 검증 (빈 값 제거)
            $checkIdx = array_filter($checkIdx, function($value) {
                return !empty($value) && is_numeric($value);
            });

            if (empty($checkIdx)) {
                throw new Exception('유효한 상품이 선택되지 않았습니다.');
            }

            //랙 조회
            $rackInfo = RackModel::where('code', $rackCode)->first();
            if (empty($rackInfo)) {
                //랙 생성
                RackModel::create([
                    'code' => $rackCode,
                    'name' => $rackCode,
                    'memo' => '',
                    'prd' => '',
                ]);
            }

            // 랙코드 일괄 업데이트
            $updated = ProductStockModel::whereIn('ps_idx', $checkIdx)
                ->update([
                    'ps_rack_code' => $rackCode
                ]);

            $updatedCount = count($checkIdx);

            return response()->json([
                'success' => true,
                'message' => $updatedCount . '개 상품의 랙코드가 변경되었습니다.',
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