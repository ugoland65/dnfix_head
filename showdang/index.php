<?php
// 허용할 도메인 목록
$allowed_origins = [
    "https://www.showdang.co.kr",
    "https://showdang.co.kr",
    "https://m.showdang.co.kr"
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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

	if( $apiMode == "brandList" ){
	
		$response = $ShowdangApi->brandList();

	}elseif( $apiMode == "brandInfo" && $brandCode ){

		$response = $ShowdangApi->brandInfo($requestData);

	}

	echo json_encode($response, JSON_UNESCAPED_UNICODE);