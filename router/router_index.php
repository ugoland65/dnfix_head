<?php

require_once __DIR__.'/autoloader.php';

use App\Core\Router;
use App\Controllers\Admin\ProductPartnerController;

try {

    // 라우터 인스턴스 생성
    $router = new Router('/router');

    // 라우트 등록
    $router->post('/matchProviderProduct', ProductPartnerController::class, 'matchProviderProduct'); // 공급사 상품 매칭
    $router->post('/cancelMatchProviderProduct', ProductPartnerController::class, 'cancelMatchProviderProduct'); // 공급사 상품 매칭 취소
    $router->post('/loadGodoGoodsInfo', ProductPartnerController::class, 'loadGodoGoodsInfo'); // 고도몰 매칭 상품 정보 갱신
    $router->get('/test-config', ProductPartnerController::class, 'matchProviderProduct');
    /*
    $router->post('/code', CodeController::class, 'store');
    $router->put('/code', CodeController::class, 'update');
    $router->delete('/code', CodeController::class, 'destroy');
    $router->get('/code/list', CodeController::class, 'list');
    */

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