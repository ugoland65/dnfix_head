<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\OrderGroupService;

class OrderGroupController extends BaseClass
{

    /**
     * 주문서 폼 수정
     */
    public function updateOrderGroup(Request $request)
    {

        try{

            $requestData = $request->all();
            $idx = $requestData['idx'] ?? null;

            $OrderGroupService = new OrderGroupService();
            $OrderGroupService->updateOrderGroup($requestData);

            return response()->json([
                'success' => true,
                'message' => '주문서 폼 수정 완료',
            ]);

        }
        catch (Throwable $e) {          
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 주문서 폼 그룹 수정
     */
    public function updateOrderGroupGroup(Request $request)
    {
        try{

            $requestData = $request->all();

            $OrderGroupService = new OrderGroupService();
            $OrderGroupService->updateOrderGroupGroup($requestData);

            return response()->json([
                'success' => true,
                'message' => '주문서 폼 그룹 수정 완료',
            ]);
            
        }
        catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 폼그룹 상품의 재입고 알림 요청 수를 일괄 수집한다.
     */
    public function syncOrderGroupRestockAlertCounts(Request $request)
    {
        try {
            $orderGroupService = new OrderGroupService();
            $result = $orderGroupService->syncOrderGroupRestockAlertCounts($request->all());

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? '재입고 알림 수집이 완료되었습니다.',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}