<?php

require_once __DIR__ . '/autoloader.php';

use App\Core\Router;
use App\Controllers\Admobile\AuthController;
use App\Controllers\Admobile\MobileController;
use App\Controllers\Admobile\OrderSheetController;
use App\Controllers\Admobile\ProfileController;

try {
    $router = new Router('/admobile');

    $router->get('/', MobileController::class, 'index');
    $router->get('/login', AuthController::class, 'login');
    $router->post('/login', AuthController::class, 'authenticate');
    $router->post('/logout', AuthController::class, 'logout');
    $router->get('/main', MobileController::class, 'main');
    $router->get('/profile', ProfileController::class, 'profile');
    $router->post('/profile', ProfileController::class, 'update');
    $router->get('/order/sheet/list', OrderSheetController::class, 'list');
    $router->get('/order/sheet/stock', OrderSheetController::class, 'stock');
    $router->get('/order/sheet/stock/unit', OrderSheetController::class, 'stockUnit');
    $router->post('/order/sheet/action', OrderSheetController::class, 'action');

    $router->dispatch();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server Error: ' . $e->getMessage(),
        'status' => 500,
    ]);
}
