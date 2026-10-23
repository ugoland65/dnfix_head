<?php
$readJsonArray = static function ($raw) {
    if (is_array($raw)) {
        return $raw;
    }
    if (!is_string($raw)) {
        return null;
    }
    $trimmed = trim($raw);
    if ($trimmed === '') {
        return null;
    }
    $decoded = json_decode($trimmed, true);
    return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : null;
};

$rackChangeOf = static function ($diff) use ($readJsonArray): ?array {
    if (!is_array($diff)) {
        return null;
    }

    if (isset($diff['ps_rack_code']) && is_array($diff['ps_rack_code'])) {
        $before = trim((string)($diff['ps_rack_code']['before'] ?? ''));
        $after = trim((string)($diff['ps_rack_code']['after'] ?? ''));
        if ($before !== $after) {
            return ['before' => $before, 'after' => $after];
        }
    }

    if (!isset($diff['prd_stock']) || !is_array($diff['prd_stock'])) {
        return null;
    }

    $beforeStock = $readJsonArray($diff['prd_stock']['before'] ?? null);
    $afterStock = $readJsonArray($diff['prd_stock']['after'] ?? null);
    $beforeHas = is_array($beforeStock) && array_key_exists('ps_rack_code', $beforeStock);
    $afterHas = is_array($afterStock) && array_key_exists('ps_rack_code', $afterStock);
    if (!$beforeHas && !$afterHas) {
        return null;
    }

    $before = $beforeHas ? trim((string)$beforeStock['ps_rack_code']) : '';
    $after = $afterHas ? trim((string)$afterStock['ps_rack_code']) : '';
    if ($before === $after) {
        return null;
    }

    return ['before' => $before, 'after' => $after];
};

$rackLogCount = 0;
foreach ($adminActionLogList as $rackLogItem) {
    if ($rackChangeOf($rackLogItem['diff_json'] ?? []) !== null) {
        $rackLogCount++;
    }
}
?>
<style>
    .admin-action-log-toolbar { display:flex; align-items:center; gap:8px; padding:10px 10px 0; }
    .admin-action-log .log-rack-row { display:none; }
    .admin-action-log.is-rack-only tr[data-rack-changed="0"] { display:none; }
    .admin-action-log.is-rack-only .log-field-row { display:none; }
    .admin-action-log.is-rack-only .log-rack-row { display:table-row; }
    .admin-action-log .log-rack-empty { display:none; padding:16px 10px; color:#667085; }
    .admin-action-log.is-rack-only .log-rack-empty { display:block; }
</style>
<div class="admin-action-log" style="background-color: #fff;">
    <div class="admin-action-log-toolbar">
        <button
            type="button"
            class="btnstyle1 btnstyle1-primary btnstyle1-sm"
            onclick="var root=this.closest('.admin-action-log'); var on=root.classList.toggle('is-rack-only'); this.textContent=on ? '전체 로그' : '랙코드 변경 로그 (<?= (int)$rackLogCount ?>)';"
        >랙코드 변경 로그 (<?= (int)$rackLogCount ?>)</button>
    </div>
    <?php if ($rackLogCount === 0) { ?>
        <div class="log-rack-empty">랙코드 변경 로그가 없습니다.</div>
    <?php } ?>
    <table class="table-st1">
        <thead>
            <tr>
                <th class="list-idx">UID</th>
                <th>수정일</th>
                <th>모드/요약</th>
                <th>수정내용</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $renderDiffValue = static function ($rawValue): void {
                if ($rawValue === null || $rawValue === '') {
                    echo '-';
                    return;
                }

                if (is_array($rawValue) || is_object($rawValue)) {
                    $json = json_encode($rawValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                    echo '<pre style="white-space:pre-wrap; word-break:break-word; margin:0;">' . htmlspecialchars((string)$json, ENT_QUOTES, 'UTF-8') . '</pre>';
                    return;
                }

                if (is_string($rawValue)) {
                    $trimmed = trim($rawValue);
                    if ($trimmed === '') {
                        echo '-';
                        return;
                    }
                    $decoded = json_decode($trimmed, true);
                    if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                        $json = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                        echo '<pre style="white-space:pre-wrap; word-break:break-word; margin:0;">' . htmlspecialchars((string)$json, ENT_QUOTES, 'UTF-8') . '</pre>';
                        return;
                    }
                }

                echo nl2br(htmlspecialchars((string)$rawValue, ENT_QUOTES, 'UTF-8'));
            };

            foreach ($adminActionLogList as $item) {
                $diffJson = $item['diff_json'] ?? [];
                if (!is_array($diffJson)) {
                    $diffJson = [];
                }
                $rackChange = $rackChangeOf($diffJson);
            ?>
                <tr data-rack-changed="<?= $rackChange ? '1' : '0' ?>">
                    <td><?= $item['idx'] ?></td>
                    <td>
                        <?= date('Y.m.d', strtotime($item['processed_at'])) ?><br>
                        <?= date('H:i:s', strtotime($item['processed_at'])) ?><br>
                        <?= $item['operator_name'] ?>
                    </td>
                    <td>
                        <?= $item['action_mode'] ?><br>
                        <?= $item['action_summary'] ?>
                    </td>

                    <td>

                        <table>
                            <thead>
                                <tr>
                                    <th>필드</th>
                                    <th>이전</th>
                                    <th>이후</th>
                                </tr>
                            </thead>
                            <?php if ($rackChange) { ?>
                                <tr class="log-rack-row">
                                    <td>랙코드</td>
                                    <td><?= $rackChange['before'] !== '' ? htmlspecialchars($rackChange['before'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                    <td><?= $rackChange['after'] !== '' ? htmlspecialchars($rackChange['after'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                </tr>
                            <?php } ?>
                            <?php
                                foreach ($diffJson as $key => $value) {
                            ?>
                                <tr class="log-field-row">
                                    <td><?= $key ?></td>
                                    <td>
                                        <?php if ($key === 'cd_reg') { ?>
                                            <details>
                                                <summary style="cursor:pointer;">접기/펼치기</summary>
                                                <?php
                                                $beforeValue = $value['before'] ?? null;
                                                $renderDiffValue($beforeValue);
                                                ?>
                                            </details>
                                        <?php } else { ?>
                                            <?php
                                            $beforeValue = $value['before'] ?? null;
                                            $renderDiffValue($beforeValue);
                                            ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($key === 'cd_reg') { ?>
                                            <details>
                                                <summary style="cursor:pointer;">접기/펼치기</summary>
                                                <?php
                                                $afterValue = $value['after'] ?? null;
                                                $renderDiffValue($afterValue);
                                                ?>
                                            </details>
                                        <?php } else { ?>
                                            <?php
                                            $afterValue = $value['after'] ?? null;
                                            $renderDiffValue($afterValue);
                                            ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>

                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>

<?//= dump($adminActionLogList) ?>