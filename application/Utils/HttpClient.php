<?php

namespace App\Utils;

class HttpClient
{   
    // 기본 헤더 설정
    private static function setHeaders($header)
    {
        return empty($header) ? ['Content-Type: application/json'] : $header;
    }

    // POST 요청
    public static function postData($url, $data, $header = '')
    {
        return self::sendRequest($url, 'POST', $data, $header);
    }

    // GET 요청
    // - 기존 시그니처(getData($url, $header))와 호환
    // - getData($url, $data, $header) 형태도 지원
    public static function getData($url, $arg1 = '', $arg2 = '')
    {
        $data = null;
        $header = '';

        if (is_array($arg1)) {
            if (self::isHeaderArray($arg1) && empty($arg2)) {
                $header = $arg1;
            } else {
                $data = $arg1;
                $header = $arg2;
            }
        } else {
            $header = $arg1;
            $data = $arg2;
        }

        return self::sendRequest($url, 'GET', $data, $header);
    }

    private static function isHeaderArray($value)
    {
        if (!is_array($value)) {
            return false;
        }
        foreach ($value as $item) {
            if (!is_string($item)) {
                return false;
            }
            if (strpos($item, ':') === false) {
                return false;
            }
        }
        return true;
    }

    // 공통 요청 처리
    private static function sendRequest($url, $method, $data = null, $header = '')
    {
        try {
            if ($method === 'GET' && !empty($data)) {
                if (is_array($data)) {
                    $query = http_build_query($data);
                } else {
                    $query = ltrim((string)$data, '?');
                }
                $url .= (strpos($url, '?') === false ? '?' : '&') . $query;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            if ($method !== 'GET' && !empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $data);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, self::setHeaders($header));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode >= 400) { // HTTP 에러 코드 처리
                error_log("HTTP Error: $httpCode - $response");
                return '';
            }

            curl_close($ch);
            return $response;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return '';
        }
    }
}
