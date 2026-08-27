<?php

require_once __DIR__ . '/autoloader.php';

use App\Controllers\Api\FirebaseController;
use App\Core\Router;

try {
    $router = new Router('/api');
    $router->get('/firebase/token', FirebaseController::class, 'token');
    $router->dispatch();
} catch (Throwable $e) {
    error_log(json_encode([
        'event' => 'api_router_failed',
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => '서버 요청 처리 중 오류가 발생했습니다.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
