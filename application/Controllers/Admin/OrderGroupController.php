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

    /**
     * 주문서폼 그룹 상품관리 화면
     */
    public function formGroupProductPage(Request $request)
    {
        try {
            $oopIdx = (int)($request->input('idx') ?? 0);
            $orderGroupService = new OrderGroupService();
            $data = $orderGroupService->getFormGroupProductPageData($oopIdx);

            return view('admin.order_sheet.order_sheet_form_group_info', $data);
        } catch (Throwable $e) {
            return view('admin.order_sheet.order_sheet_form_group_info', [
                'oopIdx' => (int)($request->input('idx') ?? 0),
                'products' => [],
                'brandForSelect' => [],
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 주문서폼 그룹 상품 목록 저장
     */
    public function saveFormGroupProducts(Request $request)
    {
        try {
            $requestData = $request->all();
            foreach (['idx', 'oop_idx', 'prd_idx', 'ps_idx', 'ordermemo', 'state'] as $key) {
                if (array_key_exists($key, $_POST)) {
                    $requestData[$key] = $_POST[$key];
                }
            }

            $orderGroupService = new OrderGroupService();
            $result = $orderGroupService->saveFormGroupProducts($requestData);

            return response()->json([
                'success' => true,
                'msg' => '완료',
                'message' => '완료',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 주문서폼 그룹 추가 상품 검색
     */
    public function searchFormGroupProducts(Request $request)
    {
        try {
            $orderGroupService = new OrderGroupService();
            $result = $orderGroupService->searchFormGroupProducts($request->all());

            return response()->json([
                'success' => true,
                'msg' => '완료',
                'message' => '완료',
                'count' => $result['count'] ?? 0,
                'prd_data' => $result['prd_data'] ?? [],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
                'message' => $e->getMessage(),
                'count' => 0,
                'prd_data' => [],
            ], 400);
        }
    }

}