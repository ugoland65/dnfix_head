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
        $keys = explode('.', $key);
        $value = self::$items;

        foreach ($keys as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
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
