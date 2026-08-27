<?php

namespace App\Controllers\Api;

use App\Core\AuthAdmin;
use App\Models\AdminModel;
use App\Services\FirebaseRealtimeService;
use Throwable;

class FirebaseController
{
    /**
     * 현재 로그인한 인트라넷 관리자의 Firebase Custom Token을 발급한다.
     */
    public function token()
    {
        header('Cache-Control: no-store, private');
        header('Pragma: no-cache');
        header('Vary: Cookie');

        $adminIdx = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $adminId = trim((string)(AuthAdmin::getSession('sess_id') ?? ''));

        if ($adminIdx <= 0 || $adminId === '') {
            return response()->json([
                'success' => false,
                'message' => '인트라넷 로그인이 필요합니다.',
            ], 401);
        }

        try {
            $admin = AdminModel::find($adminIdx);
        } catch (Throwable $e) {
            error_log(json_encode([
                'event' => 'firebase_admin_lookup_failed',
                'admin_idx' => $adminIdx,
                'error' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return response()->json([
                'success' => false,
                'message' => '관리자 정보를 확인하지 못했습니다.',
            ], 500);
        }

        if (!$admin || (string)($admin->ad_id ?? '') !== $adminId) {
            return response()->json([
                'success' => false,
                'message' => '유효한 관리자 계정을 확인할 수 없습니다.',
            ], 403);
        }

        $adminName = trim((string)($admin->ad_name ?? ''));
        $adminRole = trim((string)($admin->ad_role ?? ''));
        if ($adminRole === '') {
            $adminRole = (string)($admin->ad_level ?? '');
        }

        try {
            $token = (new FirebaseRealtimeService())->createCustomToken([
                'idx' => $adminIdx,
                'name' => $adminName,
                'role' => $adminRole,
            ]);
        } catch (Throwable $e) {
            error_log(json_encode([
                'event' => 'firebase_custom_token_failed',
                'admin_idx' => $adminIdx,
                'error' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return response()->json([
                'success' => false,
                'message' => 'Firebase 인증 설정을 확인해 주세요.',
            ], 503);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'uid' => 'admin_' . $adminIdx,
        ]);
    }
}
