<?php

// 허용할 도메인 목록
$allowed_origins = [
    "https://www.showdang.co.kr",
    "https://showdang.co.kr",
    "https://m.showdang.co.kr",
	"https://dnfixhead.mycafe24.com"
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// OPTIONS 요청 처리 (Preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

	require_once '../application/Core/Autoloader.php';

	use App\Core\Autoloader;

	Autoloader::register(); // Autoloader 등록

	use App\Classes\RequestHandler;
	use App\Controllers\Api\ShowdangApi;

	$requestHandler = new RequestHandler();

	$requestData = $requestHandler->allInput();

	$apiMode =  $requestHandler->input('mode');
	$brandCode =  $requestHandler->input('code');

	$ShowdangApi = new ShowdangApi();
	
	header('Content-Type: application/json');

	$response = [];

	if( $apiMode == "brandList" ){
	
		$response = $ShowdangApi->brandList();

	}elseif( $apiMode == "brandInfo" && $brandCode ){

		$response = $ShowdangApi->brandInfo($requestData);

	}else{
		$response = [
			'success' => false,
			'message' => '잘못된 요청입니다.',
		];
	}

	echo json_encode($response, JSON_UNESCAPED_UNICODE);