<?php

require_once __DIR__ . '/autoloader.php';

use App\Core\Router;
use App\Controllers\Admin\StaffController;
use App\Controllers\Admin\RackController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\ProductProcController;
use App\Controllers\Admin\SalesController;

try {

    $router = new Router('/admin');  // 라우터 인스턴스 생성

    $router->get('/staff/list', StaffController::class, 'staffList'); //직원목록
    $router->get('/staff/reg', StaffController::class, 'staffReg'); //신규 직원등록
    $router->get('/staff/info', StaffController::class, 'staffInfo'); //직원 상세
    $router->post('/staff/create', StaffController::class, 'createStaff'); //직원 신규생성
    $router->post('/staff/update', StaffController::class, 'updateStaff'); //직원 수정

    $router->get('/product/product_stock', ProductController::class, 'productStock'); //상품재고목록
    $router->get('/sales/picking_list/{idx}', SalesController::class, 'pickingList'); //피킹리스트
    $router->get('/sales/packing_list', SalesController::class, 'packingList'); //패킹리스트

    $router->get('/stock/rack_list', RackController::class, 'rackList'); //랙목록
    $router->get('/stock/rack_info', RackController::class, 'rackCreate'); //랙신규등록
    $router->get('/stock/rack_info/{idx}', RackController::class, 'rackInfo'); //랙상세
    $router->post('/stock/save_rack', RackController::class, 'saveRack'); //랙등록
    $router->post('/stock/delete_rack', RackController::class, 'deleteRack'); //랙삭제
    $router->get('/stock/rack_change', RackController::class, 'rackChange'); //랙그룹변경 페이지
    $router->post('/stock/save_rack_change', RackController::class, 'saveRackChange'); //랙그룹변경 저장

    // 상품 처리
    $router->post('/product/proc/rack_change_batch', ProductProcController::class, 'rackChangeBatch'); //랙코드 일괄변경

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
