<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $basePath;

    public function __construct($basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * GET 라우트 등록
     */
    public function get($path, $controller, $method = 'index')
    {
        $this->addRoute('GET', $path, $controller, $method);
    }

    /**
     * POST 라우트 등록
     */
    public function post($path, $controller, $method = 'store')
    {
        $this->addRoute('POST', $path, $controller, $method);
    }

    /**
     * PUT 라우트 등록
     */
    public function put($path, $controller, $method = 'update')
    {
        $this->addRoute('PUT', $path, $controller, $method);
    }

    /**
     * DELETE 라우트 등록
     */
    public function delete($path, $controller, $method = 'destroy')
    {
        $this->addRoute('DELETE', $path, $controller, $method);
    }

    /**
     * 라우트 추가
     */
    private function addRoute($httpMethod, $path, $controller, $method)
    {
        $path = '/' . trim($path, '/');
        $this->routes[$httpMethod][$path] = [
            'controller' => $controller,
            'method' => $method
        ];
    }

    /**
     * 현재 요청 처리
     */
    public function dispatch()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // 쿼리 스트링 제거
        $path = parse_url($requestUri, PHP_URL_PATH);
        
        // 베이스 패스 제거
        if ($this->basePath && strpos($path, $this->basePath) === 0) {
            $path = substr($path, strlen($this->basePath));
        }
        
        $path = '/' . trim($path, '/');
        
        // 라우트 매칭
        if (isset($this->routes[$requestMethod][$path])) {
            $route = $this->routes[$requestMethod][$path];
            return $this->callController($route['controller'], $route['method']);
        }

        // 라우트를 찾지 못한 경우
        $this->send404();
    }

    /**
     * 컨트롤러 호출
     */
    private function callController($controllerName, $method)
    {
        // ::class 형태로 전달된 경우 그대로 사용
        $controllerClass = $controllerName;
        
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} not found");
        }

        $controller = new $controllerClass();
        
        if (!method_exists($controller, $method)) {
            throw new \Exception("Method {$method} not found in {$controllerClass}");
        }

        return $controller->$method();
    }

    /**
     * 404 응답
     */
    private function send404()
    {
        http_response_code(404);
        echo json_encode([
            'error' => 'Route not found',
            'status' => 404
        ]);
    }

    /**
     * 등록된 라우트 목록 반환 (디버깅용)
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
