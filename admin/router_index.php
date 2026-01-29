<?php
require_once __DIR__ . '/autoloader.php';

use App\Core\Router;
use App\Controllers\Admin\StaffController;
use App\Controllers\Admin\RackController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\ProductProcController;
use App\Controllers\Admin\ProductPartnerController;
use App\Controllers\Admin\SalesController;
use App\Controllers\Admin\BrandController;
use App\Controllers\Admin\ProductStockHistoryController;
use App\Controllers\Admin\GodoApiController;
use App\Controllers\Admin\CsRequestController;
use App\Controllers\Admin\WorkController;

try {

    $router = new Router('/admin');  // 라우터 인스턴스 생성


    // 인사/업무
    $router->get('/work/TaskRequest', WorkController::class, 'taskRequest'); //업무 로그
    $router->get('/work/TaskRequestDetail/{idx}', WorkController::class, 'taskRequestDetail'); //업무 로그 상세
    $router->get('/work/TaskRequest/create', WorkController::class, 'taskRequestCreate'); //업무 로그 신규생성 페이지
    $router->get('/work/TaskRequest/modify/{idx}', WorkController::class, 'taskRequestModify'); //업무 수정페이지
    $router->post('/work/TaskRequest/save', WorkController::class, 'saveTaskRequest'); //업무 로그 신규생성 저장
    $router->post('/work/TaskRequest/action', WorkController::class, 'taskRequestAction'); //업무 로그 액션
    //$router->post('/work/TaskRequestCheck', WorkController::class, 'taskRequestCheck'); //업무 로그 체크

    //코멘트

    $router->get('/staff/list', StaffController::class, 'staffList'); //직원목록
    $router->get('/staff/reg', StaffController::class, 'staffReg'); //신규 직원등록
    $router->get('/staff/info', StaffController::class, 'staffInfo'); //직원 상세
    $router->post('/staff/create', StaffController::class, 'createStaff'); //직원 신규생성
    $router->post('/staff/update', StaffController::class, 'updateStaff'); //직원 수정

    // 브랜드
    $router->get('/brand/list', BrandController::class, 'brandList'); //브랜드 목록
    $router->get('/brand/detail/{idx}', BrandController::class, 'brandDetail'); //브랜드 상세
    $router->get('/brand/reg', BrandController::class, 'brandReg'); //브랜드 신규생성 페이지
    $router->post('/brand/create', BrandController::class, 'createBrand'); //브랜드 신규생성
    $router->post('/brand/save', BrandController::class, 'saveBrand'); //브랜드 수정

    $router->get('/product/product_stock', ProductController::class, 'productStock'); //상품재고목록
    $router->get('/sales/picking_list/{idx}', SalesController::class, 'pickingList'); //피킹리스트
    $router->get('/sales/packing_list', SalesController::class, 'packingList'); //패킹리스트
    $router->get('/sales/sales_ranking_by_period', SalesController::class, 'salesRankingByPeriod'); //매출 일별 집계 조회
    $router->get('/stock_history/list', ProductStockHistoryController::class, 'productStockHistoryListApi'); //일일재고 목록 API

    // 상품 처리
    $router->post('/product/proc/rack_change_batch', ProductProcController::class, 'rackChangeBatch'); //랙코드 일괄변경

    //공급사
    $router->get('/provider_product/list', ProductPartnerController::class, 'getProductPartnerList'); //공급사 상품 목록
    $router->post('/provider_product/proc/match_provider_product', ProductPartnerController::class, 'matchProviderProduct'); //공급사 상품 매칭
    $router->post('/provider_product/proc/cancel_match_provider_product', ProductPartnerController::class, 'cancelMatchProviderProduct'); //공급사 상품 매칭 취소

    // 재고/발주 관리
    $router->get('/order/godo_order_list', GodoApiController::class, 'godoOrderList'); //고도몰 주문서 조회
    $router->get('/stock/rack_list', RackController::class, 'rackList'); //랙목록
    $router->get('/stock/rack_info', RackController::class, 'rackCreate'); //랙신규등록
    $router->get('/stock/rack_info/{idx}', RackController::class, 'rackInfo'); //랙상세
    $router->post('/stock/save_rack', RackController::class, 'saveRack'); //랙등록
    $router->post('/stock/delete_rack', RackController::class, 'deleteRack'); //랙삭제
    $router->get('/stock/rack_change', RackController::class, 'rackChange'); //랙그룹변경 페이지
    $router->post('/stock/save_rack_change', RackController::class, 'saveRackChange'); //랙그룹변경 저장

    // C/S 관리
    $router->get('/cs/cs_list', CsRequestController::class, 'csList'); //C/S 목록
    $router->get('/cs/cs_detail/{idx}', CsRequestController::class, 'csDetail'); //C/S 상세
    $router->post('/cs/cs_request', CsRequestController::class, 'createCsRequest'); //C/S 처리 요청
    $router->post('/cs/update_cs_status', CsRequestController::class, 'updateCsStatus'); //C/S 상태변경

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
