<?php

namespace App\Utils;

use App\Core\Config;

class ConfigHelper
{
    /**
     * 설정값을 가져오는 헬퍼 메서드
     * 
     * @param string|null $key 설정 키
     * @param mixed $default 기본값
     * @return mixed
     */
    public static function get($key = null, $default = null)
    {
        if ($key === null) {
            return Config::all();
        }
        return Config::get($key, $default);
    }

    /**
     * 공급사 설정을 가져오는 메서드
     * 
     * @return array
     */
    public static function getSuppliers()
    {
        return self::get('supplier', []);
    }

    /**
     * 특정 공급사 정보를 가져오는 메서드
     * 
     * @param string $site 사이트 키 (mobe, byedam 등)
     * @return array|null
     */
    public static function getSupplier($site)
    {
        $suppliers = self::getSuppliers();
        return $suppliers[$site] ?? null;
    }

    /**
     * 공급사의 파트너 IDX를 가져오는 메서드
     * 
     * @param string $site 사이트 키
     * @param int $default 기본값
     * @return int
     */
    public static function getSupplierIdx($site, $default = 3)
    {
        $supplier = self::getSupplier($site);
        return $supplier['idx'] ?? $default;
    }

    /**
     * 공급사 이름을 가져오는 메서드
     * 
     * @param string $site 사이트 키
     * @return string
     */
    public static function getSupplierName($site)
    {
        $supplier = self::getSupplier($site);
        return $supplier['name'] ?? $site;
    }

    /**
     * 공급사 API 설정을 가져오는 메서드
     * 
     * @return array
     */
    public static function getSupplierApi()
    {
        return self::get('supplier_api', []);
    }

    /**
     * 설정 초기화 메서드 (스킨 파일에서 사용)
     * 
     * @param string $configDir 설정 디렉토리 경로
     * @return void
     */
    public static function initialize($configDir = null)
    {
        if ($configDir === null) {
            $configDir = __DIR__ . '/../../application/config/';
        }

        $configs = [];

        if (is_dir($configDir)) {
            $files = glob($configDir . '*.php');
            foreach ($files as $file) {
                $filename = basename($file, '.php');
                $configs[$filename] = require $file;
            }
        }

        // application을 app으로 단축키 설정
        if (isset($configs['config'])) {
            $configs['app'] = $configs['config'];
        }

        Config::load($configs);
    }
}
