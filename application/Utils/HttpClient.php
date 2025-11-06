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
    public static function getData($url, $header = '')
    {
        return self::sendRequest($url, 'GET', null, $header);
    }

    // 공통 요청 처리
    private static function sendRequest($url, $method, $data = null, $header = '')
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? json_encode($data) : $data);
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
