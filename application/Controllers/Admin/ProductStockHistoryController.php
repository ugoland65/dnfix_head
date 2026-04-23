<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\ProductStockHistoryService;

class ProductStockHistoryController extends BaseClass
{

    /**
     * 상품 재고 이력 조회
     * 
     * @param int $idx
     * @return array
     */
    public function productStockHistoryListApi(Request $request)
    {

        try{

            $requestData = $request->all();

            $payload = [
                'start_date' => $requestData['s_date'] ?? date('Y-m-d'),
                'end_date' => $requestData['e_date'] ?? date('Y-m-d'),
            ];

            $productStockHistoryService = new ProductStockHistoryService();
            $productStockHistoryList = $productStockHistoryService->getProductStockHistoryList($payload);

            $data = [
                'productStockHistoryList' => $productStockHistoryList,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

    }


    /**
     * 일일재고 임시저장
     * 
     * @param Request $request
     * @return array
     */
    public function saveDailyStockTemp(Request $request)
    {
        try {
            $requestData = $request->all();

            $payload = [
                'file_name' => $requestData['file_name'] ?? '',
                'mode' => $requestData['mode'] ?? 'p',
                'start_date' => $requestData['start_date'] ?? date('Y-m-d'),
                'end_date' => $requestData['end_date'] ?? date('Y-m-d'),
            ];

            $productStockHistoryService = new ProductStockHistoryService();
            $result = $productStockHistoryService->saveDailyStockTemp($payload);

            return response()->json([
                'status' => 'success',
                'message' => '일일재고 임시저장이 완료되었습니다.',
                'data' => [
                    'uid' => (int)($result['uid'] ?? 0),
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}