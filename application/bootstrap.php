<?php

/**
 * Application Bootstrap
 * 
 * 애플리케이션 초기화 및 공통 설정을 담당합니다.
 * 모든 라우터에서 이 파일을 include하여 사용할 수 있습니다.
 */

// Autoloader 로드
require_once __DIR__ . '/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Providers\Onadb\ViewServiceProvider as OnadbViewServiceProvider;
use App\Providers\Admin\ViewServiceProvider as AdminViewServiceProvider;

// Autoloader 등록
Autoloader::register();

// Helper 함수 로드
require_once __DIR__ . '/helpers.php';

// View Service Provider 부트스트랩
// 사이트별로 필요한 Provider를 조건부로 부팅

// URL을 기반으로 자동 판단
$requestUri = $_SERVER['REQUEST_URI'] ?? '';

if (strpos($requestUri, '/onadb') === 0 || 
    (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], ['onadb.net', 'onadbs.com', 'dnfixhead.mycafe24.com']))) {
    // Onadb 사이트
    OnadbViewServiceProvider::boot();
} elseif (strpos($requestUri, '/admin') === 0 || strpos($requestUri, '/ad/') === 0) {
    // Admin 사이트
    AdminViewServiceProvider::boot();
}

// 기타 초기화 작업...
// - Config 로드
// - 데이터베이스 연결
// - 미들웨어 등록
// - 에러 핸들러 등록

