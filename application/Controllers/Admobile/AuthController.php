<?php

namespace App\Controllers\Admobile;

use App\Auth\AdmobileSession;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Models\AdminModel;

class AuthController extends BaseClass
{
    /**
     * 모바일 관리자 로그인 화면.
     */
    public function login(Request $request)
    {
        if (AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/main');
        }

        $errorMessage = (string)($_SESSION['admobile_login_error'] ?? '');
        unset($_SESSION['admobile_login_error']);
        if (empty($_SESSION['admobile_login_csrf'])) {
            $_SESSION['admobile_login_csrf'] = bin2hex(random_bytes(32));
        }

        return view('admobile.auth.login', [
            'pageTitle' => '인트라넷 로그인',
            'errorMessage' => $errorMessage,
            'csrfToken' => $_SESSION['admobile_login_csrf'],
        ])->extends('admobile.layout.auth_layout');
    }

    /**
     * 기존 관리자 계정으로 모바일 관리자 로그인을 처리한다.
     */
    public function authenticate(Request $request)
    {
        AdmobileSession::start();

        $requestData = $request->all();
        $loginId = trim((string)($requestData['login_id'] ?? ''));
        $loginPassword = (string)($requestData['login_password'] ?? '');
        $csrfToken = (string)($requestData['csrf_token'] ?? '');

        if ($loginId === '' || $loginPassword === ''
            || !hash_equals((string)($_SESSION['admobile_login_csrf'] ?? ''), $csrfToken)) {
            return $this->redirectLoginWithError();
        }

        $adminRow = AdminModel::query()
            ->select(['idx', 'ad_id', 'ad_pw', 'ad_name'])
            ->where('ad_id', '=', $loginId)
            ->first();
        $admin = is_array($adminRow) ? $adminRow : ($adminRow ? $adminRow->toArray() : []);
        if (empty($admin) || !hash_equals((string)($admin['ad_pw'] ?? ''), $this->hashLegacyPassword($loginPassword))) {
            return $this->redirectLoginWithError();
        }

        AdmobileSession::login($admin);
        unset($_SESSION['admobile_login_csrf']);

        return redirect('/admobile/main');
    }

    /**
     * 모바일 관리자 로그아웃.
     */
    public function logout(Request $request)
    {
        AdmobileSession::logout();

        return redirect('/admobile/login');
    }

    private function redirectLoginWithError()
    {
        $_SESSION['admobile_login_error'] = '아이디 또는 비밀번호가 올바르지 않습니다.';

        return redirect('/admobile/login');
    }

    private function hashLegacyPassword(string $password): string
    {
        $statement = $this->db->prepare('SELECT PASSWORD(:password) AS password_hash');
        $statement->execute([
            ':password' => $password . 'sjqksk',
        ]);

        return (string)($statement->fetchColumn() ?: '');
    }
}
