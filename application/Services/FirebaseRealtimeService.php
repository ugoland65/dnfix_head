<?php

namespace App\Services;

use JsonException;
use RuntimeException;

class FirebaseRealtimeService
{
    private const CUSTOM_TOKEN_AUDIENCE =
        'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit';
    private const CUSTOM_TOKEN_TTL_SECONDS = 3600;
    private const GOOGLE_TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const DATABASE_SCOPE =
        'https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email';

    private string $serviceAccountPath;
    private string $webConfigPath;
    private ?string $accessToken = null;
    private int $accessTokenExpiresAt = 0;

    public function __construct(?string $serviceAccountPath = null, ?string $webConfigPath = null)
    {
        $privateDir = dirname(__DIR__, 2) . '/private';
        $this->serviceAccountPath = $serviceAccountPath
            ?? $privateDir . '/dnfix-intranet-realtime-firebase-adminsdk-fbsvc-069511dba5.json';
        $this->webConfigPath = $webConfigPath
            ?? $privateDir . '/firebase-web-config.json';
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

        return $this->signJwt(
            ['alg' => 'RS256', 'typ' => 'JWT'],
            [
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
            ],
            $serviceAccount
        );
    }

    /**
     * DNFIX006컴 수집기가 감시하는 /remote_jobs 에 작업을 넣고 완료까지 기다린다.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function runRemoteJob(string $type, array $payload, int $timeoutSec = 150): array
    {
        $jobId = $this->enqueueRemoteJob($type, $payload);
        return $this->waitForRemoteJob($jobId, $timeoutSec);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function enqueueRemoteJob(string $type, array $payload): string
    {
        $now = date('Y-m-d H:i:s');
        $result = \App\Utils\HttpClient::postDataWithMeta(
            $this->databaseUrl() . '/remote_jobs.json',
            [
                'type' => $type,
                'payload' => $payload,
                'status' => 'queued',
                'source' => 'intranet',
                'createdAt' => $now,
                'updatedAt' => $now,
            ],
            $this->authorizedJsonHeaders(),
            20
        );
        $responseData = json_decode((string)($result['response'] ?? ''), true);
        $jobId = trim((string)($responseData['name'] ?? ''));
        if ($jobId === '') {
            throw new RuntimeException(
                'Firebase 요청 생성에 실패했습니다. HTTP ' . (int)($result['http_code'] ?? 0)
                . ($result['response'] ? ' | ' . trim((string)$result['response']) : '')
            );
        }
        return $jobId;
    }

    /**
     * @return array<string, mixed>
     */
    public function waitForRemoteJob(string $jobId, int $timeoutSec = 150, int $intervalSec = 2): array
    {
        $deadline = time() + max(10, $timeoutSec);
        $intervalSec = max(1, $intervalSec);
        while (time() <= $deadline) {
            $job = $this->getRemoteJob($jobId);
            $status = strtolower(trim((string)($job['status'] ?? '')));
            if ($status === 'done') {
                $result = $job['result'] ?? [];
                if (!is_array($result)) {
                    $result = [];
                }
                if (isset($result['ok']) && !$result['ok']) {
                    throw new RuntimeException((string)($result['error'] ?? $result['message'] ?? 'TIS 수집에 실패했습니다.'));
                }
                $result['job_id'] = $jobId;
                return $result;
            }
            if (in_array($status, ['failed', 'fail'], true)) {
                $result = is_array($job['result'] ?? null) ? $job['result'] : [];
                throw new RuntimeException(
                    (string)($job['error'] ?? $result['error'] ?? $result['message'] ?? 'TIS 수집에 실패했습니다.')
                );
            }
            if ($status === 'cancelled') {
                throw new RuntimeException((string)($job['error'] ?? '수집이 취소되었습니다.'));
            }
            sleep($intervalSec);
        }
        throw new RuntimeException('DNFIX006컴 응답 대기 시간이 초과되었습니다. 수집기 앱이 켜져 있는지 확인하세요.');
    }

    /**
     * @return array<string, mixed>
     */
    public function getRemoteJob(string $jobId): array
    {
        $encodedId = rawurlencode($jobId);
        $result = \App\Utils\HttpClient::getDataWithMeta(
            $this->databaseUrl() . '/remote_jobs/' . $encodedId . '.json',
            $this->authorizedJsonHeaders()
        );
        $job = json_decode((string)($result['response'] ?? ''), true);
        if (!is_array($job)) {
            throw new RuntimeException('Firebase 작업 상태를 읽을 수 없습니다.');
        }
        return $job;
    }

    /**
     * @return array<string, mixed>
     */
    public function describeRemoteJob(string $jobId): array
    {
        $job = $this->getRemoteJob($jobId);
        $status = strtolower(trim((string)($job['status'] ?? '')));
        $result = is_array($job['result'] ?? null) ? $job['result'] : [];
        $message = trim((string)($result['message'] ?? $job['error'] ?? ''));
        if ($message === '') {
            if ($status === 'queued') {
                $message = 'DNFIX006컴 응답 대기 중';
            } elseif ($status === 'running') {
                $message = 'DNFIX006컴에서 수집 실행 중';
            } elseif ($status === 'done') {
                $message = '수집이 완료되었습니다.';
            } elseif (in_array($status, ['failed', 'fail'], true)) {
                $message = '수집에 실패했습니다.';
            } elseif ($status === 'cancelled') {
                $message = '수집이 취소되었습니다.';
            }
        }
        return [
            'success' => true,
            'job_id' => $jobId,
            'status' => $status !== '' ? $status : 'queued',
            'waiting' => in_array($status, ['queued', 'running', ''], true),
            'message' => $message,
            'result' => $result,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function cancelRemoteJob(string $jobId): array
    {
        $job = $this->getRemoteJob($jobId);
        $status = strtolower(trim((string)($job['status'] ?? '')));
        if (in_array($status, ['done', 'failed', 'fail'], true)) {
            return [
                'success' => true,
                'already_finished' => true,
                'status' => $status,
            ];
        }
        $this->patchRemoteJob($jobId, [
            'status' => 'cancelled',
            'error' => '요청자가 취소했습니다.',
            'updatedAt' => date('Y-m-d H:i:s'),
        ]);
        return [
            'success' => true,
            'status' => 'cancelled',
        ];
    }

    /**
     * @param array<string, mixed> $fields
     */
    public function patchRemoteJob(string $jobId, array $fields): void
    {
        $encodedId = rawurlencode($jobId);
        $result = \App\Utils\HttpClient::patchDataWithMeta(
            $this->databaseUrl() . '/remote_jobs/' . $encodedId . '.json',
            $fields,
            $this->authorizedJsonHeaders(),
            20
        );
        $httpCode = (int)($result['http_code'] ?? 0);
        if ($httpCode >= 400) {
            throw new RuntimeException('Firebase 작업 상태 변경에 실패했습니다. HTTP ' . $httpCode);
        }
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

    /**
     * @return array<int, string>
     */
    private function authorizedJsonHeaders(): array
    {
        return [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->createGoogleAccessToken(),
        ];
    }

    private function createGoogleAccessToken(): string
    {
        if ($this->accessToken && $this->accessTokenExpiresAt > time() + 60) {
            return $this->accessToken;
        }

        $serviceAccount = $this->loadServiceAccount();
        $issuedAt = time();
        $assertion = $this->signJwt(
            ['alg' => 'RS256', 'typ' => 'JWT'],
            [
                'iss' => $serviceAccount['client_email'],
                'scope' => self::DATABASE_SCOPE,
                'aud' => self::GOOGLE_TOKEN_URL,
                'iat' => $issuedAt,
                'exp' => $issuedAt + 3600,
            ],
            $serviceAccount
        );
        $result = \App\Utils\HttpClient::postDataWithMeta(
            self::GOOGLE_TOKEN_URL,
            [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $assertion,
            ],
            [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ],
            20
        );
        $tokenData = json_decode((string)($result['response'] ?? ''), true);
        $accessToken = trim((string)($tokenData['access_token'] ?? ''));
        if ($accessToken === '') {
            throw new RuntimeException('Firebase 액세스 토큰을 발급하지 못했습니다.');
        }
        $this->accessToken = $accessToken;
        $this->accessTokenExpiresAt = $issuedAt + (int)($tokenData['expires_in'] ?? 3500);
        return $accessToken;
    }

    private function databaseUrl(): string
    {
        if (!is_readable($this->webConfigPath)) {
            throw new RuntimeException('Firebase 웹 설정 파일을 읽을 수 없습니다.');
        }
        $config = json_decode((string)file_get_contents($this->webConfigPath), true);
        $databaseUrl = rtrim((string)($config['databaseURL'] ?? ''), '/');
        if ($databaseUrl === '') {
            throw new RuntimeException('Firebase databaseURL이 없습니다.');
        }
        return $databaseUrl;
    }

    /**
     * @param array<string, mixed> $header
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $serviceAccount
     */
    private function signJwt(array $header, array $payload, array $serviceAccount): string
    {
        try {
            $unsignedToken = $this->base64UrlEncode(
                json_encode($header, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)
            ) . '.' . $this->base64UrlEncode(
                json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        } catch (JsonException $e) {
            throw new RuntimeException('Firebase JWT 데이터를 생성하지 못했습니다.', 0, $e);
        }

        $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
        if ($privateKey === false) {
            throw new RuntimeException('Firebase 서비스 계정 비공개 키를 읽지 못했습니다.');
        }

        $signature = '';
        if (!openssl_sign($unsignedToken, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Firebase JWT 서명에 실패했습니다.');
        }

        return $unsignedToken . '.' . $this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
