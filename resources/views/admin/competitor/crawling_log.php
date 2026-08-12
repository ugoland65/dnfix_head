<?php
$viewData = get_defined_vars();
$logs = is_array($viewData['logs'] ?? null) ? $viewData['logs'] : [];
$filters = is_array($viewData['filters'] ?? null) ? $viewData['filters'] : [];
$filterValue = static function (string $key) use ($filters): string {
    return (string)($filters[$key] ?? '');
};
$page = max(1, (int)($viewData['page'] ?? 1));
$limit = (int)($viewData['limit'] ?? 100);
$total = (int)($viewData['total'] ?? 0);
$hasNextPage = !empty($viewData['hasNextPage']);
$hasPrevPage = !empty($viewData['hasPrevPage']);
$apiError = (string)($viewData['apiError'] ?? '');
$queryParams = static function (int $targetPage) use ($filters, $limit): string {
    return http_build_query(array_filter(array_merge($filters, [
        'page' => $targetPage,
        'limit' => $limit,
    ]), static function ($value) {
        return $value !== '';
    }));
};
$summary = static function ($value): string {
    if (is_array($value) || is_object($value)) {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?: '';
    }
    return (string)$value;
};
?>
<div id="contents_head">
    <h1>크롤링 수집로그</h1>
    <h3>공급사·경쟁사 크롤러의 실행 결과와 수집 건수를 조회합니다.</h3>
</div>
<div id="contents_body">
    <div id="contents_body_wrap">
        <form method="get" action="/admin/competitor/crawling_log" class="crawl-log-search">
            <select name="kind">
                <option value="">전체 구분</option>
                <option value="공급사" <?= $filterValue('kind') === '공급사' ? 'selected' : '' ?>>공급사</option>
                <option value="경쟁사" <?= $filterValue('kind') === '경쟁사' ? 'selected' : '' ?>>경쟁사</option>
            </select>
            <input type="text" name="site_code" value="<?= htmlspecialchars($filterValue('site_code'), ENT_QUOTES, 'UTF-8') ?>" placeholder="사이트 코드">
            <input type="text" name="site_name" value="<?= htmlspecialchars($filterValue('site_name'), ENT_QUOTES, 'UTF-8') ?>" placeholder="사이트명">
            <input type="text" name="run_type" value="<?= htmlspecialchars($filterValue('run_type'), ENT_QUOTES, 'UTF-8') ?>" placeholder="실행 유형">
            <select name="status">
                <option value="">전체 상태</option>
                <?php foreach (['success' => '성공', 'fail' => '실패', 'partial' => '일부 실패'] as $value => $label) { ?>
                    <option value="<?= $value ?>" <?= $filterValue('status') === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php } ?>
            </select>
            <select name="limit">
                <?php foreach ([20, 50, 100, 200, 500] as $optionLimit) { ?>
                    <option value="<?= $optionLimit ?>" <?= $limit === $optionLimit ? 'selected' : '' ?>><?= $optionLimit ?>개</option>
                <?php } ?>
            </select>
            <button type="submit" class="btnstyle1 btnstyle1-primary">검색</button>
        </form>
        <form method="get" action="/admin/competitor/crawling_log" class="crawl-log-date-search">
            <?php foreach (['kind', 'site_code', 'site_name', 'run_type', 'status', 'limit'] as $key) { ?>
                <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($key === 'limit' ? (string)$limit : $filterValue($key), ENT_QUOTES, 'UTF-8') ?>">
            <?php } ?>
            <label>실행 시작일 <input type="datetime-local" name="started_from" value="<?= htmlspecialchars(str_replace(' ', 'T', $filterValue('started_from')), ENT_QUOTES, 'UTF-8') ?>"></label>
            <span>~</span>
            <label>실행 종료일 <input type="datetime-local" name="started_to" value="<?= htmlspecialchars(str_replace(' ', 'T', $filterValue('started_to')), ENT_QUOTES, 'UTF-8') ?>"></label>
            <button type="submit" class="btnstyle1">기간 적용</button>
            <a href="/admin/competitor/crawling_log" class="btnstyle1">초기화</a>
        </form>

        <?php if ($apiError !== '') { ?>
            <div class="crawl-log-error">크롤 실행 로그 API를 불러오지 못했습니다. <?= htmlspecialchars($apiError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php } ?>

        <div class="table-top crawl-log-summary">
            <ul class="total">Total : <b><?= number_format($total) ?></b></ul>
            <p>최신 실행 건부터 표시됩니다.</p>
        </div>
        <div class="crawl-log-table-wrap">
            <table class="crawl-log-table">
                <thead>
                    <tr>
                        <th>번호</th><th>구분 / 사이트</th><th>실행 유형</th><th>상태</th>
                        <th>수집 / 저장 / 오류</th><th>실행 시간</th><th>처리 시간</th><th>결과 요약</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)) { ?>
                        <tr><td colspan="8" class="crawl-log-empty">조회된 크롤 실행 로그가 없습니다.</td></tr>
                    <?php } ?>
                    <?php foreach ($logs as $log) { ?>
                        <tr>
                            <td><?= htmlspecialchars((string)($log['seq'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><strong><?= htmlspecialchars((string)($log['kind'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></strong><br><?= htmlspecialchars((string)($log['site_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?><small><?= htmlspecialchars((string)($log['site_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small></td>
                            <td><?= htmlspecialchars((string)($log['run_type'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="crawl-log-status crawl-log-status-<?= htmlspecialchars((string)($log['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)($log['status'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td><?= number_format((int)($log['collected_total_count'] ?? 0)) ?> / <?= number_format((int)($log['mysql_upsert_count'] ?? 0)) ?> / <span class="crawl-log-count-error"><?= number_format((int)($log['mysql_error_count'] ?? 0)) ?></span></td>
                            <td><?= htmlspecialchars((string)($log['started_at'] ?? '-'), ENT_QUOTES, 'UTF-8') ?><br><small><?= htmlspecialchars((string)($log['finished_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small></td>
                            <td><?= number_format((int)($log['duration_sec'] ?? 0)) ?>초</td>
                            <td class="crawl-log-message">
                                <?php foreach (['category_summary', 'sync_summary', 'message'] as $field) { ?>
                                    <?php if (!empty($log[$field])) { ?><details><summary><?= $field === 'message' ? '원문 로그' : ($field === 'sync_summary' ? '동기화 결과' : '카테고리 결과') ?></summary><pre><?= htmlspecialchars($summary($log[$field]), ENT_QUOTES, 'UTF-8') ?></pre></details><?php } ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <nav class="crawl-log-pagination">
            <?php if ($hasPrevPage) { ?><a href="/admin/competitor/crawling_log?<?= htmlspecialchars($queryParams($page - 1), ENT_QUOTES, 'UTF-8') ?>" class="btnstyle1">이전</a><?php } ?>
            <span><?= number_format($page) ?> 페이지</span>
            <?php if ($hasNextPage) { ?><a href="/admin/competitor/crawling_log?<?= htmlspecialchars($queryParams($page + 1), ENT_QUOTES, 'UTF-8') ?>" class="btnstyle1">다음</a><?php } ?>
        </nav>
    </div>
</div>
<style>
.crawl-log-search,.crawl-log-date-search{display:flex;gap:8px;align-items:center;margin-bottom:10px;flex-wrap:wrap}.crawl-log-search input,.crawl-log-search select,.crawl-log-date-search input{height:34px;padding:0 9px;border:1px solid #d5d9e0;border-radius:4px}.crawl-log-search input{width:145px}.crawl-log-date-search label{display:flex;gap:6px;align-items:center;font-size:12px}.crawl-log-error{margin:14px 0;padding:12px;color:#991b1b;background:#fef2f2;border:1px solid #fecaca;border-radius:4px}.crawl-log-summary{display:flex;justify-content:space-between;align-items:center}.crawl-log-summary p{color:#6b7280;font-size:12px}.crawl-log-table-wrap{overflow:auto;border:1px solid #dfe3e8}.crawl-log-table{width:100%;min-width:1150px;border-collapse:collapse}.crawl-log-table th{background:#f5f7fa;white-space:nowrap}.crawl-log-table th,.crawl-log-table td{padding:10px 12px;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;vertical-align:top;text-align:left;font-size:12px;line-height:1.45}.crawl-log-table small{display:block;margin-top:2px;color:#6b7280}.crawl-log-status{display:inline-block;padding:3px 7px;border-radius:12px;background:#e5e7eb;font-weight:600}.crawl-log-status-success{color:#166534;background:#dcfce7}.crawl-log-status-fail{color:#991b1b;background:#fee2e2}.crawl-log-status-partial{color:#92400e;background:#fef3c7}.crawl-log-count-error{color:#dc2626}.crawl-log-message{min-width:250px;max-width:420px}.crawl-log-message details{margin-bottom:4px}.crawl-log-message summary{cursor:pointer;color:#2563eb}.crawl-log-message pre{max-height:180px;overflow:auto;white-space:pre-wrap;word-break:break-word;font:11px/1.4 Consolas,monospace}.crawl-log-empty{padding:40px!important;text-align:center!important;color:#6b7280}.crawl-log-pagination{display:flex;justify-content:center;gap:10px;align-items:center;margin:20px 0}
</style>
