<?php

namespace App\Core;

interface Middleware
{
    /**
     * 미들웨어 처리
     * 
     * @return bool true면 계속 진행, false면 중단
     */
    public function handle(): bool;
}

