<?php
require_once __DIR__ . '/autoloader.php';

use App\Core\Router;
use App\Controllers\Api\ProductApi;
use App\Controllers\Api\AiRulebook;


try {

    $router = new Router('/api2');  // 라우터 인스턴스 생성

    $router->get('/product/product-stock', ProductApi::class, 'productStockApi'); //상품재고목록
    $router->get('/ai/rulebook/{code}', AiRulebook::class, 'aiRulebookApi'); //AI 룰북(code 기준) 조회
    $router->get('/ai/rulebook', AiRulebook::class, 'aiRulebookApi'); //AI 룰북(query code) 조회


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
