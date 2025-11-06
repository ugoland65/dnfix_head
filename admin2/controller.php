<?php

	/* ---- v2 버전 OOP 화 --- */
	require_once __DIR__.'/autoloader.php';

	use App\Classes\RequestHandler;

	$requestHandler = new RequestHandler();

	// URL 파라미터 추출
	$get_folder = $requestHandler->getValue('folder'); // 폴더 이름
	$get_className = $requestHandler->getValue('class'); // 클래스 이름
	$get_methodName = $requestHandler->getValue('method'); // 메서드 이름

	/*
	// method 값이 없는 경우 처리
	if (empty($get_methodName)) {
		$get_methodName = $get_className; // 클래스 이름을 메서드 이름으로 이동
		$get_className = $get_folder; // 폴더 이름을 클래스 이름으로 이동
		$get_folder = ''; // 폴더는 없으므로 빈 값으로 설정
	}
	*/

	// POST 데이터 추출
	$postData = $requestHandler->getAllPost();

	// 폴더 경로 설정
	$namespaceBase = 'App\Controllers\\';
	$folderPath = $get_folder ? $get_folder . '\\' : ''; // 폴더가 있으면 추가

	/*
	클래스와 메서드 설정
	컨트롤러 클래스에는 무조건 Controller 접미사가 있어야 한다.
	*/

	$className = $namespaceBase . $folderPath . $get_className. 'Controller';
	$methodName = $get_methodName;

	header('Content-Type: application/json');

	/*
	
	개발 끝난후 보안 강화 

	$allowedClasses = ['Basecode', 'OtherController'];
	$allowedMethods = ['saveBasecode', 'updateBasecode'];

	if (!in_array($get_className, $allowedClasses) || !in_array($get_methodName, $allowedMethods)) {
		echo json_encode(['status' => 'error', 'message' => 'Unauthorized class or method']);
		exit;
	}
	*/

	try {

		if (class_exists($className) && method_exists($className, $methodName)) {
			
			$instance = new $className();
			//$response = $instance->$methodName($postData);  // POST 데이터 전달
			
			$response = $instance->$methodName(); 

			echo json_encode($response);

		} else {
			throw new Exception('Invalid class or method');
		}

	} catch (Exception $e) {
		echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
	}