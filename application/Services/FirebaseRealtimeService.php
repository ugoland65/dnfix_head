<?php

namespace App\Services;

use JsonException;
use RuntimeException;

class FirebaseRealtimeService
{
    private const CUSTOM_TOKEN_AUDIENCE =
        'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit';
    private const CUSTOM_TOKEN_TTL_SECONDS = 3600;

    private string $serviceAccountPath;

    public function __construct(?string $serviceAccountPath = null)
    {
        $this->serviceAccountPath = $serviceAccountPath
            ?? dirname(__DIR__, 2)
                . '/private/dnfix-intranet-realtime-firebase-adminsdk-fbsvc-069511dba5.json';
    }

    /**
     * 인트라넷 관리자 PK를 UID로 사용하는 Firebase Custom Token을 발급한다.
     */
    public function createCustomToken(array $admin): string
    {
        $adminIdx = (int)($admin['idx'] ?? 0);
        if ($adminIdx <= 0) {
            throw new RuntimeException('Firebase 토큰에 사용할 관리자 PK가 없습니다.');
        }

        $serviceAccount = $this->loadServiceAccount();
        $clientEmail = $serviceAccount['client_email'];
        $issuedAt = time();

        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];
        $payload = [
            'iss' => $clientEmail,
            'sub' => $clientEmail,
            'aud' => self::CUSTOM_TOKEN_AUDIENCE,
            'iat' => $issuedAt,
            'exp' => $issuedAt + self::CUSTOM_TOKEN_TTL_SECONDS,
            'uid' => 'admin_' . $adminIdx,
            'claims' => [
                'intranet' => true,
                'admin_idx' => $adminIdx,
                'admin_name' => (string)($admin['name'] ?? ''),
                'admin_role' => (string)($admin['role'] ?? ''),
            ],
        ];

        try {
            $unsignedToken = $this->base64UrlEncode(
                json_encode($header, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)
            ) . '.' . $this->base64UrlEncode(
                json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        } catch (JsonException $e) {
            throw new RuntimeException('Firebase Custom Token 데이터를 생성하지 못했습니다.', 0, $e);
        }

        $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
        if ($privateKey === false) {
            throw new RuntimeException('Firebase 서비스 계정 비공개 키를 읽지 못했습니다.');
        }

        $signature = '';
        if (!openssl_sign($unsignedToken, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Firebase Custom Token 서명에 실패했습니다.');
        }

        return $unsignedToken . '.' . $this->base64UrlEncode($signature);
    }

    private function loadServiceAccount(): array
    {
        if (!is_readable($this->serviceAccountPath)) {
            throw new RuntimeException('Firebase 서비스 계정 파일을 읽을 수 없습니다.');
        }

        $json = file_get_contents($this->serviceAccountPath);
        if ($json === false) {
            throw new RuntimeException('Firebase 서비스 계정 파일을 불러오지 못했습니다.');
        }

        try {
            $serviceAccount = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Firebase 서비스 계정 JSON 형식이 올바르지 않습니다.', 0, $e);
        }

        $requiredKeys = ['type', 'project_id', 'private_key', 'client_email'];
        foreach ($requiredKeys as $requiredKey) {
            if (empty($serviceAccount[$requiredKey])) {
                throw new RuntimeException(
                    "Firebase 서비스 계정에 {$requiredKey} 값이 없습니다."
                );
            }
        }

        if ($serviceAccount['type'] !== 'service_account') {
            throw new RuntimeException('Firebase 서비스 계정 파일 형식이 아닙니다.');
        }

        return $serviceAccount;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
