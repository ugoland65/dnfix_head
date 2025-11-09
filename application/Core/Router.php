<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $basePath;
    private $middlewareGroups = [];
    private $groupPrefix = '';

    public function __construct($basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * 미들웨어 그룹 설정 (Laravel 스타일 - 메서드 체이닝)
     * 
     * @param string|array $middlewares 미들웨어 별칭/클래스 (문자열 또는 배열)
     * @param callable $routes 라우트 정의 클로저
     * @return void
     */
    public function middleware($middlewares, $routes)
    {
        // 문자열인 경우 배열로 변환
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }
        
        if (!is_array($middlewares)) {
            $middlewares = [$middlewares];
        }

        $previousMiddlewares = $this->middlewareGroups;
        $this->middlewareGroups = array_merge($this->middlewareGroups, $middlewares);
        
        // 클로저 실행
        if (is_callable($routes)) {
            $routes($this);
        }
        
        $this->middlewareGroups = $previousMiddlewares;
    }

    /**
     * 라우트 그룹 설정 (Laravel 스타일 - 배열 옵션)
     * 
     * @param array $attributes 그룹 속성 (middleware, prefix, namespace 등)
     * @param callable $routes 라우트 정의 클로저
     * @return void
     * 
     * @example
     * $router->group(['middleware' => 'auth'], function($router) {
     *     $router->get('/dashboard', Controller::class, 'index');
     * });
     * 
     * $router->group(['middleware' => 'auth', 'prefix' => 'admin'], function($router) {
     *     $router->get('/users', AdminController::class, 'users');
     * });
     */
    public function group($attributes, $routes)
    {
        if (!is_array($attributes)) {
            $attributes = [];
        }
        
        $previousMiddlewares = $this->middlewareGroups;
        $previousPrefix = $this->groupPrefix ?? '';

        // 미들웨어 설정
        if (isset($attributes['middleware'])) {
            $middlewares = is_array($attributes['middleware']) 
                ? $attributes['middleware'] 
                : [$attributes['middleware']];
            $this->middlewareGroups = array_merge($this->middlewareGroups, $middlewares);
        }

        // prefix 설정 (향후 확장)
        if (isset($attributes['prefix'])) {
            $this->groupPrefix = $previousPrefix . '/' . trim($attributes['prefix'], '/');
        }

        // 클로저 실행
        if (is_callable($routes)) {
            $routes($this);
        }

        // 복원
        $this->middlewareGroups = $previousMiddlewares;
        $this->groupPrefix = $previousPrefix;
    }

    /**
     * GET 라우트 등록
     * 
     * @param string $path 경로
     * @param string|callable $controller 컨트롤러 클래스명 또는 클로저
     * @param string $method 메서드명 (컨트롤러인 경우)
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
     * 뷰 라우트 등록 (Laravel의 Route::view()와 유사)
     * 컨트롤러 없이 뷰만 반환
     */
    public function view($path, $viewPath, $data = [])
    {
        $path = '/' . trim($path, '/');
        $pattern = "#^" . $path . "$#";

        $this->routes['GET'][$path] = [
            'pattern' => $pattern,
            'view' => $viewPath,
            'data' => $data,
            'isView' => true,
            'middlewares' => $this->middlewareGroups,
        ];
    }

    /**
     * 라우트 추가
     * {param} 형태를 정규식 패턴으로 변환
     */
    private function addRoute($httpMethod, $path, $controller, $method)
    {
        $path = '/' . trim($path, '/');

        // {변수명} → 정규식 그룹 ([^/]+)
        $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $path);

        // 클로저인 경우 특별 처리
        $isClosure = is_callable($controller) && !is_string($controller);

        $this->routes[$httpMethod][$path] = [
            'pattern' => "#^" . $pattern . "$#",
            'controller' => $controller,
            'method' => $method,
            'isClosure' => $isClosure,
            'middlewares' => $this->middlewareGroups,
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

        // 등록된 라우트 확인
        if (!empty($this->routes[$requestMethod])) {
            foreach ($this->routes[$requestMethod] as $route) {
                if (preg_match($route['pattern'], $path, $matches)) {
                    array_shift($matches); // 전체 매칭 제거 → 파라미터만 남음

                    // 미들웨어 실행
                    if (!empty($route['middlewares'])) {
                        foreach ($route['middlewares'] as $middleware) {
                            // MiddlewareManager로 해결 (별칭, 클래스명, 인스턴스 모두 처리)
                            $middlewareInstance = \App\Core\MiddlewareManager::resolve($middleware);
                            
                            if ($middlewareInstance && !$middlewareInstance->handle()) {
                                return; // 미들웨어가 false 반환하면 중단
                            }
                        }
                    }

                    // 뷰 전용 라우트 처리
                    if (!empty($route['isView'])) {
                        $view = \App\Core\View::make($route['view'], $route['data']);
                        $view->response();
                        return;
                    }

                    // 클로저 라우트 처리
                    if (!empty($route['isClosure'])) {
                        $result = call_user_func_array($route['controller'], $matches);
                        
                        // 클로저에서 직접 출력했을 수 있으므로 null이면 종료
                        if ($result === null) {
                            return;
                        }
                    } else {
                        $result = $this->callController($route['controller'], $route['method'], $matches);
                    }

                    // 뷰 응답
                    if ($result instanceof \App\Core\View) {
                        $result->response();
                        return;
                    }

                    // 배열 응답 → JSON
                    if (is_array($result)) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($result, JSON_UNESCAPED_UNICODE);
                        return;
                    }

                    // 문자열 응답
                    if (is_string($result)) {
                        echo $result;
                        return;
                    }

                    return; // 기타 타입 무시
                }
            }
        }

        // 매칭 실패 → 404
        $this->send404();
    }

    /**
     * 컨트롤러 호출
     */
    private function callController($controllerName, $method, $params = [])
    {
        $controllerClass = $controllerName;

        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} not found");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new \Exception("Method {$method} not found in {$controllerClass}");
        }

        // 메서드의 파라미터 정보 가져오기
        $reflectionMethod = new \ReflectionMethod($controller, $method);
        $methodParams = $reflectionMethod->getParameters();
        
        $resolvedParams = [];
        
        // URL 파라미터 인덱스 추적
        $urlParamIndex = 0;
        
        // 각 파라미터에 대해 의존성 주입 처리
        foreach ($methodParams as $index => $param) {
            $paramType = $param->getType();
            
            if ($paramType && !$paramType->isBuiltin()) {
                $className = $paramType->getName();
                
                // Request 클래스 주입
                if ($className === 'App\Classes\Request' || $className === 'Request') {
                    $resolvedParams[] = new \App\Classes\Request();
                }
                // RequestHandler 클래스 주입
                elseif ($className === 'App\Classes\RequestHandler' || $className === 'RequestHandler') {
                    $resolvedParams[] = new \App\Classes\RequestHandler();
                }
                // 기타 클래스들은 기본 생성자로 생성
                else {
                    if (class_exists($className)) {
                        $resolvedParams[] = new $className();
                    } else {
                        throw new \Exception("Cannot resolve dependency: {$className}");
                    }
                }
            }
            // URL 파라미터가 있는 경우 추가
            elseif (isset($params[$urlParamIndex])) {
                $resolvedParams[] = $params[$urlParamIndex];
                $urlParamIndex++;
            }
            // 기본값이 있는 경우
            elseif ($param->isDefaultValueAvailable()) {
                $resolvedParams[] = $param->getDefaultValue();
            }
            // 필수 파라미터인데 값이 없는 경우
            else {
                throw new \Exception("Missing required parameter: {$param->getName()}");
            }
        }

        // 파라미터를 컨트롤러 메서드로 전달
        try {
            return call_user_func_array([$controller, $method], $resolvedParams);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage(), 'status' => 500]);
            exit;
        }
        
    }

    /**
     * 404 응답
     */
    private function send404()
    {
        echo "404";
        exit;
        
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
