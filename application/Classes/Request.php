<?php

namespace App\Classes;

class Request extends RequestHandler
{
    
    /**
     * Request 클래스는 RequestHandler를 상속받아 
     * Laravel과 유사한 인터페이스를 제공
     */
    
    /**
     * 모든 입력 데이터 반환 (Laravel의 all() 메서드와 동일)
     */
    public function all($filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    {
        return parent::all($filter);
    }
    
    /**
     * 특정 키의 값 반환 (Laravel의 input() 메서드와 동일)
     */
    public function input($key = null, $default = null, $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    {
        $value = parent::input($key, $filter);
        return $value !== null ? $value : $default;
    }
    
    /**
     * 특정 키가 존재하는지 확인 (Laravel의 has() 메서드와 동일)
     */
    public function has($key)
    {
        return parent::has($key);
    }

    /**
     * 업로드된 단일 파일 반환 (Laravel file)
     */
    public function file($key)
    {
        return parent::file($key);
    }

    /**
     * 업로드된 파일 존재 여부 확인 (Laravel hasFile)
     */
    public function hasFile($key)
    {
        return parent::hasFile($key);
    }

    /**
     * 업로드된 모든 파일 반환 (Laravel allFiles 대응)
     */
    public function allFiles()
    {
        return parent::allFiles();
    }
    
    /**
     * 여러 키의 값을 배열로 반환 (Laravel의 only() 메서드와 유사)
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $result = [];
        
        foreach ($keys as $key) {
            if ($this->has($key)) {
                $result[$key] = $this->input($key);
            }
        }
        
        return $result;
    }
    
    /**
     * 특정 키들을 제외한 모든 값 반환 (Laravel의 except() 메서드와 유사)
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $all = $this->all();
        
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        
        return $all;
    }
    
    /**
     * 요청이 AJAX인지 확인
     */
    public function ajax()
    {
        return strtolower($this->header('X-Requested-With')) === 'xmlhttprequest';
    }
    
    /**
     * 요청이 JSON인지 확인
     */
    public function wantsJson()
    {
        $acceptable = $this->header('Accept');
        return $acceptable && strpos($acceptable, 'application/json') !== false;
    }
    
    /**
     * IP 주소 반환
     */
    public function ip()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * User Agent 반환
     */
    public function userAgent()
    {
        return $this->header('User-Agent');
    }
}
