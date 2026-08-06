<?php

namespace App\Controllers\Admobile;

use Exception;
use App\Auth\AdmobileSession;
use App\Auth\AuthService;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Models\AdminModel;

class ProfileController extends BaseClass
{
    public function profile(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        $admin = $this->getCurrentAdmin();

        return view('admobile.profile.index', [
            'admin' => $admin,
        ])->extends('admobile.layout.layout', [
            'pageTitle' => '계정관리',
        ]);
    }

    public function update(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return response()->json(['success' => false, 'message' => '로그인이 필요합니다.'], 401);
        }

        try {
            $admin = $this->getCurrentAdmin();
            $data = $request->all();
            $newPassword = (string)($data['new_password'] ?? '');
            $passwordConfirm = (string)($data['password_confirm'] ?? '');

            if ($newPassword !== '' && $newPassword !== $passwordConfirm) {
                throw new Exception('새 비밀번호가 일치하지 않습니다.');
            }

            $adData = is_array($admin['ad_data'] ?? null) ? $admin['ad_data'] : [];
            $updateData = [
                'ad_nick' => trim((string)($data['ad_nick'] ?? '')),
                'ad_birth' => trim((string)($data['ad_birth'] ?? '')) ?: null,
                'ad_google' => trim((string)($data['ad_google'] ?? '')),
                'ad_data' => json_encode([
                    'address' => trim((string)($data['ad_address'] ?? '')),
                    'tel' => trim((string)($data['ad_tel'] ?? '')),
                    'contact' => [
                        'name' => trim((string)($data['ad_contact_name'] ?? '')),
                        'relationship' => trim((string)($data['ad_contact_relationship'] ?? '')),
                        'tel' => trim((string)($data['ad_contact_tel'] ?? '')),
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'AD_UP_DATE' => time(),
            ];

            if ($newPassword !== '') {
                $passwordHash = AuthService::getLegacyPassword($newPassword);
                if (!is_string($passwordHash) || $passwordHash === '') {
                    throw new Exception('비밀번호를 처리하지 못했습니다.');
                }
                $updateData['ad_pw'] = $passwordHash;
            }

            $imageName = $this->uploadProfileImage((int)$admin['idx']);
            if ($imageName !== null) {
                $updateData['ad_image'] = $imageName;
            }

            AdminModel::update(['idx' => (int)$admin['idx']], $updateData);

            AdmobileSession::start();
            $_SESSION['sess_name'] = (string)($admin['ad_name'] ?? $_SESSION['sess_name'] ?? '');

            return response()->json(['success' => true, 'message' => '내 정보를 수정했습니다.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function getCurrentAdmin(): array
    {
        AdmobileSession::start();
        $idx = (int)($_SESSION['sess_idx'] ?? 0);
        $row = AdminModel::find($idx);
        $admin = $row ? $row->toArray() : [];
        if (empty($admin)) {
            throw new Exception('관리자 정보를 찾을 수 없습니다.');
        }

        $admin['ad_data'] = json_decode((string)($admin['ad_data'] ?? '{}'), true);
        if (!is_array($admin['ad_data'])) {
            $admin['ad_data'] = [];
        }

        return $admin;
    }

    private function uploadProfileImage(int $adminIdx): ?string
    {
        $file = $_FILES['profile_image'] ?? null;
        if (!is_array($file) || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ((int)($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new Exception('프로필 이미지를 업로드하지 못했습니다.');
        }
        if ((int)($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new Exception('프로필 이미지는 5MB 이하만 가능합니다.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string)$file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
        if (!isset($extensions[$mime])) {
            throw new Exception('JPG, PNG, GIF 이미지 파일만 등록할 수 있습니다.');
        }

        $fileName = 'ad_profile_file_' . $adminIdx . '.' . $extensions[$mime];
        $uploadDirectory = dirname(__DIR__, 3) . '/data/uploads';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            throw new Exception('프로필 이미지 저장 경로를 만들 수 없습니다.');
        }
        if (!move_uploaded_file((string)$file['tmp_name'], $uploadDirectory . '/' . $fileName)) {
            throw new Exception('프로필 이미지를 저장하지 못했습니다.');
        }

        return $fileName;
    }
}
