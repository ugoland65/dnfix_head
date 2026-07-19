<?php
namespace App\Services;

use PDO;
use RuntimeException;

class CompetitorPopupTicketService
{
    private PDO $db;
    private array $config;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->config = config('admin.competitor_popup');
    }

    public function issue(int $userId, string $site, int $productId, string $requestId, string $ip): array
    {
        if ($userId <= 0 || $site === '' || mb_strlen($site) > 50 || $productId <= 0) {
            throw new RuntimeException('invalid_request');
        }

        $permissions = $this->permissionsFor($userId, $site, $productId);
        if (!in_array('competitor_product:view', $permissions, true) || !$this->resourceExists($site, $productId)) {
            throw new RuntimeException('forbidden');
        }

        $now = time();
        $jti = bin2hex(random_bytes(32));
        $payload = [
            'iss' => $this->requiredConfig('issuer'),
            'aud' => $this->requiredConfig('audience'),
            'iat' => $now,
            'exp' => $now + (int)$this->config['ttl_seconds'],
            'jti' => $jti,
            'user_id' => $userId,
            'site' => $site,
            'product_id' => $productId,
            'permissions' => $permissions,
        ];

        $ticket = $this->signRs256($payload);
        $launchUrl = $this->requiredConfig('launch_url');
        $this->audit($userId, $site, $productId, $permissions, $jti, $requestId, $ip);

        return [
            'popup_url' => $launchUrl . (str_contains($launchUrl, '?') ? '&' : '?') . 'ticket=' . rawurlencode($ticket),
            'expires_at' => $payload['exp'],
        ];
    }

    private function permissionsFor(int $userId, string $site, int $productId): array
    {
        // 임시 운영 정책: A 관리자 로그인 및 B 상품 존재 검증을 통과하면
        // 별도 권한 테이블 등록 없이 상세 팝업 조회를 허용한다.
        // 메모 저장도 모든 관리자에게 임시 허용한다.
        return ['competitor_product:view', 'competitor_product:memo:write'];

        /*
        $stmt = $this->db->prepare(
            'SELECT can_view, can_memo_write FROM competitor_popup_permissions
             WHERE admin_user_idx = :user_id AND site_code = :site
               AND product_id IN (0, :product_id) AND is_active = 1
             ORDER BY product_id DESC LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId, 'site' => $site, 'product_id' => $productId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !(int)$row['can_view']) {
            return [];
        }

        $permissions = ['competitor_product:view'];
        if ((int)$row['can_memo_write']) {
            $permissions[] = 'competitor_product:memo:write';
        }
        return $permissions;
        */
    }

    private function resourceExists(string $site, int $productId): bool
    {
        $competitorApiService = new CompetitorApiService();
        $response = $competitorApiService->getCompetitorProducts([
            'site' => $site,
            'product_id' => $productId,
            'limit' => 1,
        ]);
        $rows = $response['data']['competitorProducts'] ?? $response['data'] ?? [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            if ((string)($row['site'] ?? '') === $site && (int)($row['prd_pk'] ?? $row['product_id'] ?? 0) === $productId) {
                return true;
            }
        }
        return false;
    }

    private function signRs256(array $payload): string
    {
        if (($this->config['algorithm'] ?? '') !== 'RS256') {
            throw new RuntimeException('unsupported_algorithm');
        }
        $key = (string)($this->config['private_key'] ?? '');
        $path = (string)($this->config['private_key_path'] ?? '');
        if ($key === '' && $path !== '' && is_readable($path)) {
            $key = (string)file_get_contents($path);
        }
        if ($key === '') {
            throw new RuntimeException('signing_key_unavailable');
        }

        $header = $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_UNESCAPED_SLASHES));
        $body = $this->base64Url(json_encode($payload, JSON_UNESCAPED_SLASHES));
        if (!openssl_sign($header . '.' . $body, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('ticket_signing_failed');
        }
        return $header . '.' . $body . '.' . $this->base64Url($signature);
    }

    private function audit(int $userId, string $site, int $productId, array $permissions, string $jti, string $requestId, string $ip): void
    {
        error_log(json_encode([
            'event' => 'competitor_popup_ticket_issued', 'user_id' => $userId, 'site' => $site,
            'product_id' => $productId, 'permissions' => $permissions, 'issued_at' => date('c'),
            'jti_hash' => hash('sha256', $jti), 'request_id' => $requestId, 'ip' => $ip,
        ], JSON_UNESCAPED_SLASHES));
    }

    private function requiredConfig(string $key): string
    {
        $value = trim((string)($this->config[$key] ?? ''));
        if ($value === '') throw new RuntimeException('ticket_configuration_missing');
        return $value;
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
