<?php

namespace App\Controllers\Admin;

use Throwable;
use Exception;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\ProductStockService;

class ProductStockController extends BaseClass
{

    /**
     * 상품 재고 처리 액션
     * 
     * @param Request $request
     * @return view
     */
    public function productStockAction(Request $request)
    {
        try{

            $requestData = $request->all();
            $actionMode = $requestData['action_mode'] ?? null;
            $productStockService = new ProductStockService();

            switch ($actionMode) {

                // 재고코드 생성
                case 'create_stock_code':
                    $result = $productStockService->createStockCode($requestData);
                    break;

                // 상품 세일 설정
                case 'set_product_sale':
                    $result = $productStockService->setProductSale($requestData);
                    break;

                // 상품 세일 해제
                case 'unset_product_sale':
                    $result = $productStockService->unsetProductSale($requestData);
                    break;
            }

            $message = (is_array($result) && isset($result['message'])) ? $result['message'] : '처리 완료';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result,
            ]);

        }
        catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}