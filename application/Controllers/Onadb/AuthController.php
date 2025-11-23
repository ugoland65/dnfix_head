<?php

namespace App\Controllers\Onadb;

use Throwable;
use Exception;
use App\Auth\AuthService;
use App\Auth\OnadbAuth;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Models\UserModel;
use App\Services\UserServices;

class AuthController extends BaseClass
{


    /**
     * 로그인 페이지
     */
    public function login(Request $request)
    {
        init_session();

        $requestData = $request->all();

        $returnUrl = $requestData['redirect'] ?? '/';

        // 이미 로그인되어 있으면 대시보드로 리다이렉트
        /*
        if (!empty($_SESSION['onadb'])) {
            header('Location: /onadb/dashboard');
            exit;
        }
        */

        $data = [
            'returnUrl' => $returnUrl,
        ];

        return view('onadb.auth.login', $data);
    }


    /**
     * 로그인 처리
     */
    public function loginProc(Request $request)
    {

        // 화이트리스트 처리
        $data = $request->only('user_id', 'user_pw', 'returnUrl');

        $userId  = trim((string)($data['user_id'] ?? ''));
        $password = (string)($data['user_pw'] ?? '');
        $returnUrl = (string)($data['returnUrl'] ?? '/');

        try{

            if ($userId === '' || $password === '') {
                throw new Exception('로그인정보를 제대로 입력해주세요.');
            }

            $hashedPassword = AuthService::getLegacyPassword($password);

            $user = UserModel::where('user_id', $userId)->first();

            if( !$user || $user->user_pw !== $hashedPassword ){
                throw new Exception('로그인정보가 일치하지 않습니다.');
            }

            OnadbAuth::login([
                'user_idx' => $user->user_idx,
                'user_id' => $user->user_id,
                'user_nick' => $user->user_nick,
                'user_email' => $user->user_email,
            ]);

            return redirect($returnUrl);
            
        } catch (Exception $e) {
            return redirect("/login?error=1")->with('error', $e->getMessage());
            //dd($e->getMessage());
        }
        
    }

    
    /**
     * 로그아웃 처리
     */
    public function logout(Request $request)
    {
        OnadbAuth::logout();
        return redirect('/');
    }


    /**
     * 회원가입 페이지
     */
    public function registerForm(Request $request)
    {
        return view('onadb.auth.join');
    }


    /**
     * 회원가입 페이지
     */
    public function register(Request $request)
    {
        try{
            $requestData = $request->all();

            $userServices = new UserServices();
            $result = $userServices->register($requestData);

            if( $result ){
                return response()->json([
                    'success' => true,
                    'message' => '회원가입 완료',
                    'user_id' => $result->user_id,
                ]);
            }else{
                throw new Exception('생성오류');
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '회원가입 실패: ' . $e->getMessage(),
                'result' => null,
            ]);
        }
    }

    
    /**
     * 중복확인
     */
    public function checkAvailability(Request $request)
    {

        $requestData = $request->all();

        $mode = $requestData['mode'];
        $value = $requestData['value'];

        $userServices = new UserServices();
        $result = $userServices->checkAvailability($mode, $value);

        return response()->json([
            'success' => true,
            'message' => '중복확인 완료',
            'result' => $result,
        ]);

    }

}