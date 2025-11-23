<?php

require_once __DIR__ . '/autoloader.php';

use App\Core\Router;
use App\Core\MiddlewareManager;
use App\Controllers\Onadb\HomeController;
use App\Controllers\Onadb\AuthController;
use App\Controllers\Onadb\ProductController;
use App\Controllers\Onadb\MyPageController;
use App\Middleware\OnadbAuthMiddleware;

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

    // 미들웨어 별칭 등록
    MiddlewareManager::registerMany([
        'auth' => OnadbAuthMiddleware::class,
    ]);
    
    $router = new Router($basePath);  // 라우터 인스턴스 생성

    $router->get('/', HomeController::class, 'index'); //메인페이지

    $router->get('/login', AuthController::class, 'login'); //로그인페이지
    $router->post('/login', AuthController::class, 'loginProc'); //로그인처리
    $router->get('/logout', AuthController::class, 'logout'); //로그아웃
    $router->get('/join', AuthController::class, 'registerForm'); //회원가입페이지
    $router->post('/check-availability', AuthController::class, 'checkAvailability'); //회원 중복확인
    $router->post('/join-register', AuthController::class, 'register'); //회원가입

    $router->get('/pv/{idx}', ProductController::class, 'productDetail'); //상품상세페이지

    //인증 페이지 그룹
    $router->middleware('auth', function ($router) {
        $router->get('/mypage', MyPageController::class, 'mypage'); //마이페이지
        $router->post('/mypage', MyPageController::class, 'mypageProc'); //마이페이지처리
    });

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
