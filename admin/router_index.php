<?php

require_once __DIR__ . '/autoloader.php';

use App\Core\Router;
use App\Controllers\Admin\StaffController;

try {

    $router = new Router('/admin');  // 라우터 인스턴스 생성

    $router->get('/staff/list', StaffController::class, 'staffList'); //직원목록

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
