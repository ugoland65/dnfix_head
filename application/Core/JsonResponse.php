<?php

namespace App\Core;

class JsonResponse
{
    private $data;
    private $statusCode;
    private $headers;

    public function __construct($data = [], int $statusCode = 200, array $headers = [])
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        
        header('Content-Type: application/json');
        
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
        
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

