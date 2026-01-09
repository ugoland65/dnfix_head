<?php

namespace App\Classes;

class RequestHandler
{
    private $inputData;

    public function __construct()
    {
        $this->inputData = array_merge($_GET, $_POST); // GET과 POST 데이터 병합
        //$this->startSessionIfNeeded(); // 세션 시작
    }

    // 특정 키 값 가져오기 (GET, POST 모두)
    public function __get($key)
    {
        return $this->input($key);
    }

    // 모든 입력값(GET, POST)에서 특정 키 값 가져오기 및 필터링
    public function input($key = null, $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS, $options = [])
    {
        if ($key === null) {
            return $this->filterArrayRecursive($this->inputData, $filter);
        }

        if (!isset($this->inputData[$key])) {
            return null;
        }

        $value = $this->inputData[$key];

        // 배열인 경우 재귀적으로 필터링
        if (is_array($value)) {
            return $this->filterArrayRecursive($value, $filter);
        }

        // 스칼라 값인 경우 filter_var 사용
        return filter_var($value, $filter, $options);
    }

    // 모든 입력값 반환 (GET, POST)
    public function allInput($filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    {
        return $this->filterArrayRecursive($this->inputData, $filter);
    }

    // 모든 입력값 반환 (GET, POST)
    public function all($filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    {
        return $this->filterArrayRecursive($this->inputData, $filter);
    }

    // 특정 키가 존재하는지 확인 (GET, POST)
    public function has($key)
    {
        return isset($this->inputData[$key]);
    }

    // GET 요청에서 특정 키 값 가져오기
    public function getValue($key, $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS, $options = [])
    {
        return filter_input(INPUT_GET, $key, $filter, $options) ?? null;
    }

    // POST 요청에서 특정 키 값 가져오기
    public function getPostValue($key, $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS, $options = [])
    {
        return filter_input(INPUT_POST, $key, $filter, $options) ?? null;
    }

    // 모든 GET 값 반환
    public function getAll($filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    {
        return $this->filterArrayRecursive($_GET, $filter);
    }

    // 모든 POST 값 반환
    public function getAllPost($filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    {
        return $this->filterArrayRecursive($_POST, $filter);
    }

    // 파일 업로드 처리
    public function file($key)
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * 업로드된 모든 파일 반환 (Laravel의 allFiles 유사)
     * 중첩된 다중파일도 PHP $_FILES 형태를 재구성하여 반환
     */
    public function allFiles()
    {
        return $this->normalizeFiles($_FILES);
    }

    public function hasFile($key)
    {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK;
    }

    // 요청 메서드 확인
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function isMethod($method)
    {
        return strcasecmp($this->method(), $method) === 0;
    }

    // 요청된 URI 가져오기
    public function uri()
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    // 요청된 URL 가져오기
    public function url()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return $protocol . $_SERVER['HTTP_HOST'] . $this->uri();
    }

    // 요청 헤더 가져오기
    public function header($key)
    {
        $headers = $this->allHeaders();
        return $headers[$key] ?? null;
    }

    public function allHeaders()
    {
        return getallheaders();
    }

    // CSRF 토큰 생성 및 검증
    public function generateCsrfToken()
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    public function validateCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // 재귀적 필터링
    private function filterArrayRecursive($data, $filter)
    {
        $filteredData = [];
        foreach ($data as $key => $value) {
            $filteredData[$key] = is_array($value) ? $this->filterArrayRecursive($value, $filter) : filter_var($value, $filter);
        }
        return $filteredData;
    }

    // 세션이 시작되지 않았으면 시작
    private function startSessionIfNeeded()
    {
		/*
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
		*/
    }

    /**
     * $_FILES 구조를 재귀적으로 Laravel과 유사한 형태로 정규화
     * @param array $files
     * @return array
     */
    private function normalizeFiles(array $files)
    {
        $normalized = [];

        foreach ($files as $key => $file) {
            if (!is_array($file) || !isset($file['name'])) {
                continue;
            }
            $normalized[$key] = $this->normalizeFileItem($file);
        }

        return $normalized;
    }

    /**
     * 단일/다중 파일 항목을 재귀적으로 정규화
     * @param array $file
     * @return array
     */
    private function normalizeFileItem(array $file)
    {
        if (is_array($file['name'])) {
            $items = [];
            foreach ($file['name'] as $idx => $name) {
                $items[$idx] = $this->normalizeFileItem([
                    'name'     => $name,
                    'type'     => $file['type'][$idx] ?? null,
                    'tmp_name' => $file['tmp_name'][$idx] ?? null,
                    'error'    => $file['error'][$idx] ?? UPLOAD_ERR_NO_FILE,
                    'size'     => $file['size'][$idx] ?? 0,
                ]);
            }
            return $items;
        }

        return [
            'name'     => $file['name'],
            'type'     => $file['type'] ?? null,
            'tmp_name' => $file['tmp_name'] ?? null,
            'error'    => $file['error'] ?? UPLOAD_ERR_NO_FILE,
            'size'     => $file['size'] ?? 0,
        ];
    }
}
