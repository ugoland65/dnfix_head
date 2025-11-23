<?php

namespace App\Controllers\Onadb;

use Exception;
use App\Classes\Request;
use App\Core\JsonResponse;
use App\Core\BaseClass;
use App\Services\UserServices;


class MyPageController extends BaseClass
{

    /**
     * 마이페이지 페이지
     * 
     * @param Request $request
     * @return View
     */
    public function mypage(Request $request)
    {
        return view('onadb.mypage.mypage')
        ->extends('onadb.layout.layout');
    }

    /**
     * 마이페이지 수정 처리
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function mypageProc(Request $request)
    {

        try{

            $data = $request->only('id', 'nick', 'new_pw', 'old_pw');

            if( empty($data['old_pw']) ){
                throw new Exception('현재 패스워드를 입력해주세요.');
            }

            if ( empty($data['id']) ){
                throw new Exception('식별값이 없습니다.');
            }

            if ( empty($data['nick']) ){
                throw new Exception('닉네임이 없습니다.');
            }

            $payload = [
                'user_id' => $data['id'],
                'user_nick' => $data['nick'],
                'new_pw' => $data['new_pw'] ?? null,
                'old_pw' => $data['old_pw'] ?? null,
            ];

            $userServices = new UserServices();
            $result = $userServices->mypageModify($payload);

            return response()->json(['success' => true, 'msg' => '정보변경이 완료되었습니다.']);

        }catch(Exception $e){
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }

    }

}
