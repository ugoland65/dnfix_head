<?php

namespace App\Core;

/**
 * Laravel 스타일 뷰 시스템
 */
class View
{
    // PHP 7.3 호환: typed properties 제거
    private static $viewPath = '';
    private $data = [];
    private $template = '';

    private $layout = null;
    private $layoutData = [];

    public function __construct(string $template, array $data = [])
    {
        $this->template = $template;
        $this->data = $data;
        self::$viewPath = dirname(__DIR__, 2) . '/resources/views/';
    }

    /**
     * 뷰 생성 (Laravel의 view() 헬퍼와 유사)
     */
    public static function make(string $template, array $data = []): self
    {
        return new self($template, $data);
    }

    /**
     * 데이터 추가 (Laravel의 with() 메서드와 유사)
     *
     * @param string|array $key
     * @param mixed $value
     */
    public function with($key, $value = null): self
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        
        return $this;
    }

    /**
     * 레이아웃 지정 (Blade의 @extends 대체)
     */
    public function extends(string $layout, array $layoutData = []): self
    {
        $this->layout = $layout;
        $this->layoutData = $layoutData;
        return $this;
    }

    /**
     * 뷰 렌더링
     */
    public function render(): string
    {
        // 세션 시작 (플래시 메시지 읽기 위해)
        $this->ensureSessionStarted();
        
        $templatePath = $this->getTemplatePath();

        if (!file_exists($templatePath)) {
            throw new \Exception("View [{$this->template}] not found at {$templatePath}");
        }

        // 현재 뷰 객체를 전역으로 등록 (extends_layout에서 접근 가능)
        $GLOBALS['__current_view__'] = $this;

        extract($this->data, EXTR_SKIP);

        ob_start();
        include $templatePath;
        $content = ob_get_clean();

        // 뷰 렌더링 끝나면 글로벌 변수 제거
        unset($GLOBALS['__current_view__']);

        // 레이아웃 처리...
        if ($this->layout) {
            $layoutPath = self::$viewPath . str_replace('.', DIRECTORY_SEPARATOR, $this->layout) . '.php';

            if (!file_exists($layoutPath)) {
                throw new \Exception("Layout [{$this->layout}] not found at {$layoutPath}");
            }

            $layoutData = array_merge($this->layoutData, $this->data, ['content' => $content]);
            extract($layoutData, EXTR_SKIP);

            ob_start();
            include $layoutPath;
            $content = ob_get_clean();
        }

        // 플래시 메시지 소멸 (렌더링 후)
        if (isset($_SESSION['_flash'])) {
            unset($_SESSION['_flash']);
        }

        return $content;
    }



    /**
     * 뷰를 HTTP 응답으로 반환
     */
    public function response(int $statusCode = 200, array $headers = []): void
    {
        // 기본 헤더 설정
        $defaultHeaders = [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $headers = array_merge($defaultHeaders, $headers);

        // HTTP 상태 코드 설정
        http_response_code($statusCode);

        // 헤더 설정
        foreach ($headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // 뷰 출력
        echo $this->render();
    }

    /**
     * JSON 응답으로 변환 (AJAX 요청용)
     */
    public function toJson(int $statusCode = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        
        echo json_encode([
            'success' => true,
            'html' => $this->render(),
            'data' => $this->data,
            'template' => $this->template
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /**
     * 템플릿 파일 경로 생성
     */
    private function getTemplatePath(): string
    {
        // 점 표기법을 디렉토리 구분자로 변환 (예: 'user.profile' -> 'user/profile')
        $path = str_replace('.', DIRECTORY_SEPARATOR, $this->template);
        
        // PHP 7.3 호환: str_ends_with 대신 substr 사용
        if (substr($path, -4) !== '.php') {
            $path .= '.php';
        }
        
        return self::$viewPath . $path;
    }

    /**
     * 뷰 존재 여부 확인
     */
    public static function exists(string $template): bool
    {
        $viewPath = dirname(__DIR__, 2) . '/resources/views/';
        $path = str_replace('.', DIRECTORY_SEPARATOR, $template);
        
        // PHP 7.3 호환: str_ends_with 대신 substr 사용
        if (substr($path, -4) !== '.php') {
            $path .= '.php';
        }
        
        return file_exists($viewPath . $path);
    }

    /**
     * 뷰 디렉토리 경로 설정
     */
    public static function setViewPath(string $path): void
    {
        self::$viewPath = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    /**
     * 현재 뷰 디렉토리 경로 반환
     */
    public static function getViewPath(): string
    {
        return self::$viewPath;
    }

    /**
     * 매직 메서드: 문자열로 변환 시 렌더링
     */
    public function __toString(): string
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return "View Error: " . $e->getMessage();
        }
    }

    /**
     * 세션이 시작되지 않았다면 시작
     */
    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // 관리자 영역인지 확인하여 세션 경로 설정
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            
            if (strpos($requestUri, '/admin') === 0 || strpos($requestUri, '/admin2') === 0) {
                // 관리자 세션 경로
                $sessionPath = $_SERVER['DOCUMENT_ROOT'] . "/admin2/session";
            } else {
                // 일반 사용자 세션 경로
                $sessionPath = $_SERVER['DOCUMENT_ROOT'] . "/user/session";
            }
            
            // 세션 설정
            ini_set("session.use_trans_sid", 0);
            ini_set("url_rewriter.tags", "");
            ini_set("session.cookie_httponly", 1);
            ini_set("session.use_only_cookies", 1);
            
            if (is_dir($sessionPath) && is_writable($sessionPath)) {
                session_save_path($sessionPath);
            }
            
            session_start();
        }
    }

}
