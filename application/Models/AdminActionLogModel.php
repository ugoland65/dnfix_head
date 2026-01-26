<?php
namespace App\Models;

use App\Core\BaseModel;

/**
 * 어드민 공용 액션 로그 모델
 *
 * 테이블: admin_action_log
 * PK: idx
 * 시간 컬럼: processed_at (created_at/updated_at 대신 사용)
 */
class AdminActionLogModel extends BaseModel
{
    protected $table = 'admin_action_log';
    protected $primaryKey = 'idx';

    // created_at / updated_at 자동 처리 안 쓰는 구조라면 false
    public $timestamps = false;

    // 저장 가능 필드 (fill)
    protected $fillable = [
        'target_type',
        'target_table',
        'target_pk',
        'target_pks_json',

        'action_mode',
        'action_summary',

        'before_json',
        'after_json',
        'diff_json',

        'action_url',
        'source',
        'request_id',
        'ip_address',
        'user_agent',

        'operator_pk',
        'operator_id',
        'operator_name',

        'is_success',
        'error_message',

        'processed_at',
    ];

    // JSON 컬럼 목록 (저장/조회 시 배열 <-> JSON 문자열 변환용)
    protected $jsonFields = [
        'target_pks_json',
        'before_json',
        'after_json',
        'diff_json',
    ];

    /**
     * 로그 1건 적재
     *
     * - 배열로 들어온 JSON 필드들은 자동으로 json_encode 처리
     * - processed_at 없으면 현재시각으로 채움
     */
    public static function write(array $data): int
    {
        $model = new static();

        // 기본값
        if (!isset($data['is_success'])) {
            $data['is_success'] = 1;
        }
        if (empty($data['processed_at'])) {
            $data['processed_at'] = date('Y-m-d H:i:s');
        }

        // JSON 필드 인코딩
        $data = $model->encodeJsonFields($data);

        // fillable만 남기기
        $payload = $model->onlyFillable($data);

        // insert 후 PK 리턴 (BaseModel/QueryBuilder에 맞춰 2가지 방식 제공)
        // 1) insertGetId 지원하는 경우
        if (method_exists($model, 'insertGetId')) {
            return (int) $model->insertGetId($payload);
        }

        // 2) QueryBuilder 스타일인 경우
        $qb = static::query()->from($model->table);
        $result = $qb->insert($payload);

        // insert가 id를 반환하는 구현이라면 그대로 사용
        if (is_numeric($result)) {
            return (int) $result;
        }

        // BaseModel에 lastInsertId 같은 헬퍼가 있으면 거기에 맞춰 수정
        return (int) static::getLastInsertIdSafe();
    }

    /**
     * target 단건 조회용: (target_type, target_pk)로 최근 로그 n개
     */
    public static function latestByTarget(string $targetType, string $targetPk, int $limit = 50): array
    {
        return static::query()
            ->where('target_type', '=', $targetType)
            ->where('target_pk', '=', $targetPk)
            ->orderByRaw('processed_at DESC')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * 조회 결과에서 JSON 필드 디코딩이 필요하면 사용
     * (BaseModel에 cast 기능이 없다면, get() 후 foreach로 decode해 쓰면 됨)
     */
    public function decodeRow(array $row): array
    {
        foreach ($this->jsonFields as $field) {
            if (!array_key_exists($field, $row)) {
                continue;
            }
            if ($row[$field] === null || $row[$field] === '') {
                continue;
            }

            // 문자열 JSON만 디코딩
            if (is_string($row[$field])) {
                $decoded = json_decode($row[$field], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $row[$field] = $decoded;
                }
            }
        }
        return $row;
    }

    /**
     * 내부: JSON 필드를 문자열로 인코딩
     */
    protected function encodeJsonFields(array $data): array
    {
        foreach ($this->jsonFields as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }
            if (is_array($data[$field]) || is_object($data[$field])) {
                $data[$field] = json_encode($data[$field], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
        return $data;
    }

    /**
     * 내부: fillable만 남김
     */
    protected function onlyFillable(array $data): array
    {
        $allowed = array_flip($this->fillable);
        return array_intersect_key($data, $allowed);
    }

    /**
     * 내부: lastInsertId 안전 호출 (프레임워크에 맞춰 수정)
     */
    protected static function getLastInsertIdSafe(): int
    {
        // 예시:
        // return (int) Database::connection()->lastInsertId();
        return 0;
    }
}
