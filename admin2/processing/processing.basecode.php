<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

use App\Classes\RequestHandler;
use App\Controllers\Basecode;

// 변수 초기화
$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";

$requestHandler = new RequestHandler();

$postData = $requestHandler->getAllPost();

	// echo "<pre>";
	// print_r($postData);
	// echo "</pre>";

// exit;

$basecode = new Basecode(); 

////////////////////////////////////////////////////////////////////////////////////////////////
// 베이스 코드 만들기
if( $_a_mode == "make_basecode" ){

	$cate = $_POST['cate'];
	$rows = $_POST['rows'];

	try {
		// 조건을 설정하여 최대값 조회
		$conditions = [
			'cate' => $cate
		];

		$maxSortOrder = $db->getMaxValue('basecode', 'sort_order', $conditions);

		/*
		foreach ( $_POST['rows'] as $row) {
			$maxSortOrder++;
			
			$dataArray[] = [
				'code' => $_POST['code'],
				'code' => $row['code'],
				'name' => $row['name'],
				'sort_order' => $maxSortOrder,
			];
		}
		*/
		$dataArray = array_map(function ($row) use (&$maxSortOrder, $cate) {
			return [
				'cate' => $cate,
				'code' => $row['code'],
				'name' => $row['name'],
				'sort_order' => ++$maxSortOrder,
			];
		}, $rows);

		$db->insertMultiple('basecode', $dataArray);

		echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully']);

	} catch (Exception $e) {

		echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);

	}

}

?>