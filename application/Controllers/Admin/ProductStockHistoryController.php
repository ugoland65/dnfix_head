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


}