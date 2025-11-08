<?php
namespace App\Core;

class Config
{
    private static $items = [];

    /**
     * 설정 로드
     */
    public static function load(array $configs): void
    {
        self::$items = array_merge(self::$items, $configs);
    }

    /**
     * 설정 가져오기 / 저장하기
     *
     * 사용 예:
     * Config::get('app.name');
     * Config::set('app.debug', true);
     */
    public static function get(string $key, $default = null)
    {
        $segments = explode('.', $key);

        // 파일 경로는 모든 세그먼트를 사용 (config 폴더 기준)
        $filePath = __DIR__ . '/../../config/' . implode('/', $segments) . '.php';

        if (file_exists($filePath)) {
            $config = include $filePath;
            return $config;
        }

        return $default;
    }

    public static function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $array =& self::$items;

        foreach ($keys as $segment) {
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }
            $array =& $array[$segment];
        }

        $array = $value;
    }

    public static function all(): array
    {
        return self::$items;
    }
}
