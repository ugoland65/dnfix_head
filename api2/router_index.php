<?php
require_once __DIR__ . '/autoloader.php';

use App\Core\Router;
use App\Controllers\Api\ProductApi;


try {

    $router = new Router('/api2');  // 라우터 인스턴스 생성

    $router->get('/product/product-stock', ProductApi::class, 'productStockApi'); //상품재고목록


    // 라우트 처리
    $router->dispatch();
    
} catch (Exception $e) {

    // 에러 응답
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server Error: ' . $e->getMessage(),
        'status' => 500
    ]);

}
