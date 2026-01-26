<?php

namespace App\Services;

use Exception;  
use Throwable;
use App\Models\AdminActionLogModel;
use App\Auth\AdminAuth;

class AdminActionLogService
{

    /**
     * diff 생성 (before/after 배열 기준)
     */
    public function buildDiff(array $before, array $after): array
    {
        $diff = [];
        $keys = array_unique(array_merge(array_keys($before), array_keys($after)));

        foreach ($keys as $k) {
            $bv = $before[$k] ?? null;
            $av = $after[$k] ?? null;

            // 엄격 비교
            if ($bv !== $av) {
                $diff[$k] = [
                    'before' => $bv,
                    'after'  => $av,
                ];
            }
        }
        return $diff;
    }

    /**
     * 공용 로그 insert
     * - json 필드: 배열/객체로 들어오면 자동 인코딩
     * - processed_at 기본 세팅
     */
    public function log(array $payload): int
    {
        $payload = $this->normalize($payload);

        // QueryBuilder insert는 lastInsertId 반환 구조로 보임 (insertSingle에서 lastInsertId 리턴)
        return (int) AdminActionLogModel::query()->insert($payload);
    }

    /**
     * prd_partner 품절처리 로그를 빠르게 남기는 헬퍼
     * (컨트롤러에서 before/after만 넘기면 됨)
     */
    public function logSoldOutPrdPartner(array $before, array $after, array $operator, array $meta = []): int
    {
        $diff = $this->buildDiff($before, $after);

        return $this->log([
            'target_type'    => 'prd_partner',
            'target_table'   => 'prd_partner',
            'target_pk'      => (string)($before['idx'] ?? $after['idx'] ?? ''),

            'action_mode'    => 'sold_out',
            'action_summary' => '파트너 상품 품절처리',

            'before_json'    => $before,
            'after_json'     => $after,
            'diff_json'      => $diff,

            'action_url'     => $meta['action_url'] ?? ($_SERVER['REQUEST_URI'] ?? null),
            'source'         => $meta['source'] ?? 'admin',
            'request_id'     => $meta['request_id'] ?? null,
            'ip_address'     => $meta['ip_address'] ?? ($_SERVER['REMOTE_ADDR'] ?? null),
            'user_agent'     => $meta['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? null),

            'operator_pk'    => (int)($operator['pk'] ?? 0),
            'operator_id'    => $operator['id'] ?? null,
            'operator_name'  => $operator['name'] ?? null,

            'is_success'     => 1,
        ]);
    }

    /**
     * payload 기본값/JSON 인코딩 처리
     */
    protected function normalize(array $p): array
    {
        $admin = AdminAuth::user();
        $now = date('Y-m-d H:i:s');

        $p['target_type'] = $p['target_type'] ?? 'unknown';
        $p['target_table'] = $p['target_table'] ?? null;
        $p['target_pk'] = $p['target_pk'] ?? null;

        $p['action_mode'] = $p['action_mode'] ?? 'unknown';
        $p['action_summary'] = $p['action_summary'] ?? null;

        // json fields
        $p['target_pks_json'] = $this->jsonOrNull($p['target_pks_json'] ?? null);
        $p['before_json'] = $this->jsonOrNull($p['before_json'] ?? null);
        $p['after_json'] = $this->jsonOrNull($p['after_json'] ?? null);
        $p['diff_json'] = $this->jsonOrNull($p['diff_json'] ?? null);

        $p['action_url']  = $p['action_url'] ?? null;
        $p['source'] = $p['source'] ?? 'admin';
        $p['request_id']  = $p['request_id'] ?? $this->generateRequestId();
        $p['ip_address']  = $p['ip_address'] ?? null;
        $p['user_agent']  = $p['user_agent'] ?? null;

        $p['operator_pk']   = $p['operator_pk'] ??  $admin["sess_idx"];
        $p['operator_id']   = $p['operator_id'] ??  $admin["sess_id"];
        $p['operator_name'] = $p['operator_name'] ??  $admin["sess_name"];

        $p['is_success'] = isset($p['is_success']) ? (int)$p['is_success'] : 1;
        $p['error_message'] = $p['error_message'] ?? null;

        $p['processed_at'] = $p['processed_at'] ?? $now;

        return $p;
    }

    protected function jsonOrNull($v): ?string
    {
        if ($v === null || $v === '') return null;

        if (is_array($v) || is_object($v)) {
            return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        return (string)$v;
    }

    protected function generateRequestId(): string
    {
        try {
            return bin2hex(random_bytes(16));
        } catch (\Exception $e) {
            // random_bytes 실패 대비
            return uniqid('req_', true);
        }
    }

}