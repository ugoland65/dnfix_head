<?php

namespace App\Controllers\Admobile;

use App\Auth\AdmobileSession;
use App\Classes\Request;
use App\Core\BaseClass;

class MobileController extends BaseClass
{
    /**
     * 모바일 관리자 기본 경로.
     */
    public function index(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        return redirect('/admobile/main');
    }

    /**
     * 모바일 관리자 시작 화면.
     */
    public function main(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        return view('admobile.main.index', [
            'pageTitle' => '모바일 관리자',
        ])->extends('admobile.layout.layout');
    }
}
