<?php

namespace App\Services;

use App\Models\ProductStockModel;
use App\Models\RackStockInspectionItemModel;
use App\Models\RackStockInspectionModel;
use PDO;
use PDOException;

class RackStockInspectionService
{
    private static $schemaReady = false;

    /**
     * 랙 그룹의 재고검수 목록. 상품 체크 기록은 시작 시점에 만들지 않는다.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listByGroup(string $group): array
    {
        $group = $this->groupKey($group);
        if ($group === '') {
            return [];
        }

        $this->ensureSchema();
        $statement = $this->connection()->prepare("
            SELECT
                inspection.idx,
                inspection.rack_group,
                inspection.title,
                inspection.inspect_date,
                inspection.seq_no,
                inspection.created_by_idx,
                inspection.created_by_id,
                inspection.created_by_name,
                inspection.created_at,
                (
                    SELECT COUNT(*)
                    FROM prd_rack_stock_inspection_item item
                    WHERE item.inspection_idx = inspection.idx
                ) AS checked_count
            FROM prd_rack_stock_inspection inspection
            WHERE inspection.rack_group = ?
            ORDER BY inspection.idx DESC
            LIMIT 30
        ");
        $statement->execute([$group]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'inspectionRow'], is_array($rows) ? $rows : []);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findForGroup(int $idx, string $group): ?array
    {
        if ($idx < 1) {
            return null;
        }

        foreach ($this->listByGroup($group) as $inspection) {
            if ((int)$inspection['idx'] === $idx) {
                return $inspection;
            }
        }

        return null;
    }

    /**
     * @param array{idx?: int, id?: string, name?: string} $actor
     * @return array<string, mixed>
     */
    public function start(string $group, array $actor): array
    {
        $group = $this->groupKey($group);
        if ($group === '') {
            throw new \Exception('랙 그룹이 올바르지 않습니다.');
        }

        $this->ensureSchema();
        $connection = $this->connection();
        $date = date('Ymd');
        $now = date('Y-m-d H:i:s');
        $creator = $this->actor($actor);

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $ownsTransaction = !$connection->inTransaction();
            try {
                if ($ownsTransaction) {
                    $connection->beginTransaction();
                }

                $sequenceStatement = $connection->prepare("
                    SELECT seq_no
                    FROM prd_rack_stock_inspection
                    WHERE rack_group = ? AND inspect_date = ?
                    ORDER BY seq_no DESC
                    LIMIT 1
                    FOR UPDATE
                ");
                $sequenceStatement->execute([$group, $date]);
                $latest = $sequenceStatement->fetch(PDO::FETCH_ASSOC);
                $sequence = (int)($latest['seq_no'] ?? 0) + 1;

                $created = RackStockInspectionModel::create([
                    'rack_group' => $group,
                    'title' => $group . '-' . $date . '-' . $sequence,
                    'inspect_date' => $date,
                    'seq_no' => $sequence,
                    'created_by_idx' => $creator['idx'],
                    'created_by_id' => $creator['id'],
                    'created_by_name' => $creator['name'],
                    'created_at' => $now,
                ]);
                $idx = (int)($this->modelValue($created, 'idx') ?? 0);
                if ($idx < 1) {
                    throw new \Exception('재고검수를 생성하지 못했습니다.');
                }

                if ($ownsTransaction && $connection->inTransaction()) {
                    $connection->commit();
                }

                $inspection = $this->findForGroup($idx, $group);
                if ($inspection === null) {
                    throw new \Exception('생성한 재고검수를 찾지 못했습니다.');
                }

                return $inspection;
            } catch (\Throwable $e) {
                if ($ownsTransaction && $connection->inTransaction()) {
                    $connection->rollBack();
                }
                if ($attempt < 2 && $this->isDuplicateKey($e)) {
                    continue;
                }
                throw $e instanceof \Exception ? $e : new \Exception($e->getMessage());
            }
        }

        throw new \Exception('재고검수 번호를 만들지 못했습니다.');
    }

    /**
     * 검수 완료 기록. 시작 직후에는 항목이 없고, 완료한 상품만 저장한다.
     *
     * @return array<string, array<string, mixed>>
     */
    public function checksFor(int $inspectionIdx): array
    {
        if ($inspectionIdx < 1) {
            return [];
        }

        $this->ensureSchema();
        $rows = RackStockInspectionItemModel::query()
            ->where('inspection_idx', '=', $inspectionIdx)
            ->get()
            ->toArray();

        $checks = [];
        foreach ($rows as $row) {
            $item = $this->itemRow($this->asArray($row));
            $checks[$item['ps_idx'] . ':' . $item['prd_idx']] = $item;
        }

        return $checks;
    }

    /**
     * @param array{idx?: int, id?: string, name?: string} $actor
     * @return array<string, mixed>
     */
    public function completeItem(int $inspectionIdx, int $prdIdx, int $psIdx, array $actor): array
    {
        if ($inspectionIdx < 1 || $prdIdx < 1 || $psIdx < 1) {
            throw new \Exception('검수 대상이 올바르지 않습니다.');
        }

        $this->ensureSchema();
        $inspection = RackStockInspectionModel::query()
            ->where('idx', '=', $inspectionIdx)
            ->first();
        $inspectionRow = $this->asArray($inspection);
        if ($inspectionRow === []) {
            throw new \Exception('재고검수를 찾을 수 없습니다.');
        }

        $stock = ProductStockModel::query()
            ->select(['ps_idx', 'ps_prd_idx', 'ps_rack_code', 'ps_stock'])
            ->where('ps_idx', '=', $psIdx)
            ->where('ps_prd_idx', '=', $prdIdx)
            ->first();
        $stockRow = $this->asArray($stock);
        if ($stockRow === []) {
            throw new \Exception('랙 재고 상품을 찾을 수 없습니다.');
        }

        $group = $this->groupKey((string)($inspectionRow['rack_group'] ?? ''));
        $rackCode = trim((string)($stockRow['ps_rack_code'] ?? ''));
        if (!$this->rackCodeInGroup($rackCode, $group)) {
            throw new \Exception('이 랙 검수 대상 상품이 아닙니다.');
        }

        $existing = RackStockInspectionItemModel::query()
            ->where('inspection_idx', '=', $inspectionIdx)
            ->where('prd_idx', '=', $prdIdx)
            ->where('ps_idx', '=', $psIdx)
            ->first();
        $existingRow = $this->asArray($existing);
        if ($existingRow !== []) {
            return $this->itemRow($existingRow);
        }

        $checker = $this->actor($actor);
        try {
            $created = RackStockInspectionItemModel::create([
                'inspection_idx' => $inspectionIdx,
                'prd_idx' => $prdIdx,
                'ps_idx' => $psIdx,
                'rack_code' => $rackCode,
                'stock_qty' => (int)($stockRow['ps_stock'] ?? 0),
                'checked_at' => date('Y-m-d H:i:s'),
                'checked_by_idx' => $checker['idx'],
                'checked_by_id' => $checker['id'],
                'checked_by_name' => $checker['name'],
            ]);
        } catch (\Throwable $e) {
            if (!$this->isDuplicateKey($e)) {
                throw $e instanceof \Exception ? $e : new \Exception($e->getMessage());
            }
            $created = RackStockInspectionItemModel::query()
                ->where('inspection_idx', '=', $inspectionIdx)
                ->where('prd_idx', '=', $prdIdx)
                ->where('ps_idx', '=', $psIdx)
                ->first();
        }

        $createdRow = $this->asArray($created);
        if ($createdRow === []) {
            throw new \Exception('검수완료를 저장하지 못했습니다.');
        }

        return $this->itemRow($createdRow);
    }

    public function cancelItem(int $inspectionIdx, int $prdIdx, int $psIdx): void
    {
        if ($inspectionIdx < 1 || $prdIdx < 1 || $psIdx < 1) {
            throw new \Exception('검수 대상이 올바르지 않습니다.');
        }

        $this->ensureSchema();
        $inspection = RackStockInspectionModel::query()
            ->where('idx', '=', $inspectionIdx)
            ->first();
        if ($this->asArray($inspection) === []) {
            throw new \Exception('재고검수를 찾을 수 없습니다.');
        }

        RackStockInspectionItemModel::query()
            ->where('inspection_idx', '=', $inspectionIdx)
            ->where('prd_idx', '=', $prdIdx)
            ->where('ps_idx', '=', $psIdx)
            ->delete();
    }

    private function ensureSchema(): void
    {
        if (self::$schemaReady) {
            return;
        }

        $connection = $this->connection();
        $connection->exec("CREATE TABLE IF NOT EXISTS prd_rack_stock_inspection (
            idx INT UNSIGNED NOT NULL AUTO_INCREMENT,
            rack_group VARCHAR(32) NOT NULL,
            title VARCHAR(64) NOT NULL,
            inspect_date CHAR(8) NOT NULL,
            seq_no INT UNSIGNED NOT NULL,
            created_by_idx INT UNSIGNED NOT NULL DEFAULT 0,
            created_by_id VARCHAR(64) NOT NULL DEFAULT '',
            created_by_name VARCHAR(100) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL,
            PRIMARY KEY (idx),
            UNIQUE KEY uq_rack_stock_inspection_seq (rack_group, inspect_date, seq_no),
            KEY idx_rack_stock_inspection_group (rack_group, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $connection->exec("CREATE TABLE IF NOT EXISTS prd_rack_stock_inspection_item (
            idx INT UNSIGNED NOT NULL AUTO_INCREMENT,
            inspection_idx INT UNSIGNED NOT NULL,
            prd_idx INT UNSIGNED NOT NULL DEFAULT 0,
            ps_idx INT UNSIGNED NOT NULL DEFAULT 0,
            rack_code VARCHAR(64) NOT NULL DEFAULT '',
            stock_qty INT NULL,
            checked_at DATETIME NOT NULL,
            checked_by_idx INT UNSIGNED NOT NULL DEFAULT 0,
            checked_by_id VARCHAR(64) NOT NULL DEFAULT '',
            checked_by_name VARCHAR(100) NOT NULL DEFAULT '',
            PRIMARY KEY (idx),
            UNIQUE KEY uq_rack_stock_inspection_item (inspection_idx, prd_idx, ps_idx),
            KEY idx_rack_stock_inspection_item_inspection (inspection_idx)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $columns = $connection->query('SHOW COLUMNS FROM prd_rack_stock_inspection_item')->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = [];
        foreach ($columns as $column) {
            $name = (string)($column['Field'] ?? '');
            if ($name !== '') {
                $columnNames[$name] = true;
            }
        }
        if (empty($columnNames['stock_qty'])) {
            $connection->exec('ALTER TABLE prd_rack_stock_inspection_item ADD COLUMN stock_qty INT NULL AFTER rack_code');
        }

        self::$schemaReady = true;
    }

    private function connection(): PDO
    {
        $connection = app('db');
        if (!$connection instanceof PDO) {
            throw new \Exception('데이터베이스 연결을 확인할 수 없습니다.');
        }

        return $connection;
    }

    /**
     * @param array{idx?: int, id?: string, name?: string} $actor
     * @return array{idx: int, id: string, name: string}
     */
    private function actor(array $actor): array
    {
        $name = trim((string)($actor['name'] ?? ''));
        $id = trim((string)($actor['id'] ?? ''));
        if ($name === '') {
            $name = $id !== '' ? $id : '알 수 없음';
        }

        return [
            'idx' => (int)($actor['idx'] ?? 0),
            'id' => $id,
            'name' => $name,
        ];
    }

    private function groupKey(string $group): string
    {
        $group = strtoupper(trim($group));
        if (!preg_match('/^[A-Z0-9]{1,32}$/', $group)) {
            return '';
        }

        return $group;
    }

    private function rackCodeInGroup(string $rackCode, string $group): bool
    {
        $parts = preg_split('/\s*[\/／]\s*/u', trim($rackCode)) ?: [];
        foreach ($parts as $part) {
            $part = trim((string)$part);
            if ($part === '') {
                continue;
            }
            $key = strtoupper(trim((string)explode('-', $part, 2)[0]));
            if ($key === $group) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function inspectionRow(array $row): array
    {
        return [
            'idx' => (int)($row['idx'] ?? 0),
            'rack_group' => (string)($row['rack_group'] ?? ''),
            'title' => (string)($row['title'] ?? ''),
            'inspect_date' => (string)($row['inspect_date'] ?? ''),
            'seq_no' => (int)($row['seq_no'] ?? 0),
            'created_by_idx' => (int)($row['created_by_idx'] ?? 0),
            'created_by_id' => (string)($row['created_by_id'] ?? ''),
            'created_by_name' => (string)($row['created_by_name'] ?? ''),
            'created_at' => (string)($row['created_at'] ?? ''),
            'checked_count' => (int)($row['checked_count'] ?? 0),
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function itemRow(array $row): array
    {
        return [
            'idx' => (int)($row['idx'] ?? 0),
            'inspection_idx' => (int)($row['inspection_idx'] ?? 0),
            'prd_idx' => (int)($row['prd_idx'] ?? 0),
            'ps_idx' => (int)($row['ps_idx'] ?? 0),
            'rack_code' => (string)($row['rack_code'] ?? ''),
            'stock_qty' => ($row['stock_qty'] ?? null) === null || $row['stock_qty'] === '' ? null : (int)$row['stock_qty'],
            'checked_at' => (string)($row['checked_at'] ?? ''),
            'checked_by_idx' => (int)($row['checked_by_idx'] ?? 0),
            'checked_by_id' => (string)($row['checked_by_id'] ?? ''),
            'checked_by_name' => (string)($row['checked_by_name'] ?? ''),
        ];
    }

    /**
     * @param mixed $row
     * @return array<string, mixed>
     */
    private function asArray($row): array
    {
        if (is_array($row)) {
            return $row;
        }
        if (is_object($row) && method_exists($row, 'toArray')) {
            $data = $row->toArray();
            return is_array($data) ? $data : [];
        }

        return [];
    }

    /**
     * @param mixed $model
     * @return mixed
     */
    private function modelValue($model, string $key)
    {
        $row = $this->asArray($model);
        if (array_key_exists($key, $row)) {
            return $row[$key];
        }
        if (is_object($model) && isset($model->{$key})) {
            return $model->{$key};
        }

        return null;
    }

    private function isDuplicateKey(\Throwable $e): bool
    {
        $current = $e;
        while ($current instanceof \Throwable) {
            if ($current instanceof PDOException && (string)$current->getCode() === '23000') {
                return true;
            }
            $current = $current->getPrevious();
        }

        return strpos($e->getMessage(), '1062') !== false;
    }
}
