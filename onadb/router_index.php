<?php

require_once __DIR__ . '/autoloader.php';

use App\Core\Router;
use App\Controllers\Admin\StaffController;
use App\Controllers\Onadb\HomeController;

try {

    // 도메인에 따라 베이스 경로 설정
    $check_domain = $_SERVER['HTTP_HOST'] ?? '';
    $check_domain = str_replace("www.", "", $check_domain);
    
    // onadb.net, onadbs.com 도메인이면 최상위 경로 사용
    //if ($check_domain == "onadb.net" || $check_domain == "onadbs.com") {
    if ( $check_domain == "dnfixhead.mycafe24.com" ) {
        $basePath = '';
    } else {
        $basePath = '/onadb';
    }
    
    $router = new Router($basePath);  // 라우터 인스턴스 생성

    $router->get('/', HomeController::class, 'index'); //메인페이지

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
