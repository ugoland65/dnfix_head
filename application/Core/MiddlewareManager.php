<?php

namespace App\Core;

/**
 * 미들웨어 관리자
 * Laravel의 Kernel 역할
 */
class MiddlewareManager
{
    /**
     * 미들웨어 별칭 등록
     * 
     * @var array
     */
    private static $aliases = [];

    /**
     * 미들웨어 별칭 등록
     * 
     * @param string $alias 별칭
     * @param string|callable $middleware 미들웨어 클래스명 또는 생성 함수
     */
    public static function register(string $alias, $middleware)
    {
        self::$aliases[$alias] = $middleware;
    }

    /**
     * 여러 미들웨어 별칭 일괄 등록
     * 
     * @param array $aliases ['auth' => AuthMiddleware::class, ...]
     */
    public static function registerMany(array $aliases)
    {
        foreach ($aliases as $alias => $middleware) {
            self::register($alias, $middleware);
        }
    }

    /**
     * 미들웨어 인스턴스 생성
     * 
     * @param string|object $middleware 별칭 또는 클래스명 또는 인스턴스
     * @return object|null
     */
    public static function resolve($middleware)
    {
        // 이미 인스턴스인 경우
        if (is_object($middleware)) {
            return $middleware;
        }

        // 별칭으로 등록된 경우
        if (is_string($middleware) && isset(self::$aliases[$middleware])) {
            $registered = self::$aliases[$middleware];

            // 클로저로 등록된 경우 (매개변수 전달 가능)
            if (is_callable($registered)) {
                return $registered();
            }

            // 클래스명으로 등록된 경우
            if (is_string($registered) && class_exists($registered)) {
                return new $registered();
            }

            // 이미 인스턴스로 등록된 경우
            if (is_object($registered)) {
                return $registered;
            }
        }

        // 클래스명으로 직접 사용
        if (is_string($middleware) && class_exists($middleware)) {
            return new $middleware();
        }

        return null;
    }

    /**
     * 등록된 모든 별칭 반환 (디버깅용)
     * 
     * @return array
     */
    public static function getAliases(): array
    {
        return self::$aliases;
    }
}

