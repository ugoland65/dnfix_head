<?php
$productGroupingList = $productGroupingList ?? [];
$pagination = $pagination ?? [];
$s_prd_mode = (string)($s_prd_mode ?? '');
$s_pg_mode = (string)($s_pg_mode ?? '');
$s_pg_state = (string)($s_pg_state ?? '진행');
$totalCount = (int)($pagination['total'] ?? count($productGroupingList));

$formatPeriod = static function (array $row): string {
    $pgMode = (string)($row['pg_mode'] ?? '');
    $startDay = trim((string)($row['pg_sday'] ?? ''));
    $endDay = trim((string)($row['pg_day'] ?? ''));
    $isEmpty = static function ($date): bool {
        return $date === '' || strpos($date, '0000-00-00') === 0;
    };

    if ($pgMode === 'period') {
        $startText = $isEmpty($startDay) ? '-' : $startDay;
        $endText = $isEmpty($endDay) ? '-' : $endDay;
        return $startText . ' ~ ' . $endText;
    }

    return $isEmpty($endDay) ? '-' : $endDay;
};
?>
<section class="admobile-grouping">
    <div class="admobile-page-heading">
        <a href="/admobile/main" aria-label="메뉴로 돌아가기">‹</a>
        <div>
            <h2>상품 그룹핑</h2>
            <p>총 <strong><?= number_format($totalCount) ?></strong>건</p>
        </div>
    </div>

    <?php
        $flashSuccess = trim((string)($flashSuccess ?? ''));
        $flashError = trim((string)($flashError ?? ''));
    ?>
    <?php if ($flashSuccess !== '') { ?>
        <p class="admobile-flash is-success"><?= h($flashSuccess, ENT_QUOTES, 'UTF-8') ?></p>
    <?php } ?>
    <?php if ($flashError !== '') { ?>
        <p class="admobile-flash is-error"><?= h($flashError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php } ?>

    <form class="admobile-grouping-filter" method="get" action="/admobile/product/grouping">
        <select name="s_prd_mode" aria-label="상품모드" onchange="this.form.submit()">
            <option value="all" <?= $s_prd_mode === '' ? 'selected' : '' ?>>상품모드 전체</option>
            <option value="prdDB" <?= $s_prd_mode === 'prdDB' ? 'selected' : '' ?>>상품DB</option>
            <option value="provider" <?= $s_prd_mode === 'provider' ? 'selected' : '' ?>>공급사 상품</option>
        </select>
        <select name="s_pg_mode" aria-label="그룹핑 모드" onchange="this.form.submit()">
            <option value="all" <?= $s_pg_mode === '' ? 'selected' : '' ?>>모드 전체</option>
            <option value="sale" <?= $s_pg_mode === 'sale' ? 'selected' : '' ?>>데이할인</option>
            <option value="period" <?= $s_pg_mode === 'period' ? 'selected' : '' ?>>기간할인</option>
            <option value="qty" <?= $s_pg_mode === 'qty' ? 'selected' : '' ?>>수량체크</option>
            <option value="event" <?= $s_pg_mode === 'event' ? 'selected' : '' ?>>기획전</option>
            <option value="op" <?= $s_pg_mode === 'op' ? 'selected' : '' ?>>운영</option>
        </select>
        <select name="s_pg_state" aria-label="진행상태" onchange="this.form.submit()">
            <option value="전체" <?= $s_pg_state === '전체' || $s_pg_state === '' ? 'selected' : '' ?>>상태 전체</option>
            <option value="진행" <?= $s_pg_state === '진행' ? 'selected' : '' ?>>진행</option>
            <option value="마감" <?= $s_pg_state === '마감' ? 'selected' : '' ?>>마감</option>
            <option value="취소" <?= $s_pg_state === '취소' ? 'selected' : '' ?>>취소</option>
        </select>
    </form>

    <div class="admobile-grouping-list">
        <?php if (empty($productGroupingList)) { ?>
            <p class="admobile-empty">검색 조건에 맞는 그룹핑이 없습니다.</p>
        <?php } ?>

        <?php foreach ($productGroupingList as $productGrouping) {
            $state = trim((string)($productGrouping['pg_state'] ?? ''));
            $stateClass = 'is-ing';
            if ($state === '마감') {
                $stateClass = 'is-end';
            } elseif ($state === '취소') {
                $stateClass = 'is-cancel';
            }
        ?>
            <article class="admobile-grouping-card <?= $stateClass ?>">
                <div class="admobile-grouping-card__topline">
                    <span class="admobile-grouping-badge"><?= h((string)($productGrouping['pg_mode_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="admobile-grouping-badge is-soft"><?= h((string)($productGrouping['prd_mode_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="admobile-grouping-state <?= $stateClass ?>"><?= h($state, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <h3><?= h((string)($productGrouping['pg_subject'] ?? '제목 없음'), ENT_QUOTES, 'UTF-8') ?></h3>
                <dl>
                    <div>
                        <dt>고유번호</dt>
                        <dd><?= (int)($productGrouping['idx'] ?? 0) ?></dd>
                    </div>
                    <div>
                        <dt>공개</dt>
                        <dd><?= h((string)($productGrouping['public'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt>상품수</dt>
                        <dd><?= number_format((int)($productGrouping['prd_count'] ?? 0)) ?>개</dd>
                    </div>
                    <div>
                        <dt>진행일</dt>
                        <dd><?= h($formatPeriod($productGrouping), ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt>등록자</dt>
                        <dd><?= h((string)($productGrouping['reg_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt>등록일</dt>
                        <dd><?= h((string)($productGrouping['reg_date'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                </dl>
                <a class="admobile-grouping-card__link" href="/admobile/product/grouping/view/<?= (int)($productGrouping['idx'] ?? 0) ?>">그룹핑 관리</a>
            </article>
        <?php } ?>
    </div>

    <?php if (!empty($paginationHtml)) { ?>
        <div class="admobile-pagination"><?= $paginationHtml ?></div>
    <?php } ?>
</section>

<style>
    .admobile-page-heading { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
    .admobile-page-heading a { color: #344054; font-size: 30px; line-height: 24px; text-decoration: none; }
    .admobile-page-heading h2 { margin: 0; font-size: 18px; }
    .admobile-page-heading p { margin: 4px 0 0; color: #667085; font-size: 13px; }
    .admobile-page-heading strong { color: #2450a6; }
    .admobile-flash { margin: 0 0 12px; padding: 10px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; }
    .admobile-flash.is-success { background: #ecfdf3; color: #027a48; }
    .admobile-flash.is-error { background: #fef3f2; color: #b42318; }
    .admobile-grouping-filter { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 7px; margin-bottom: 12px; }
    .admobile-grouping-filter select { width: 100%; min-width: 0; padding: 10px 8px; border: 1px solid #cfd6e1; border-radius: 7px; background: #fff; color: #344054; font: inherit; font-size: 12px; }
    .admobile-grouping-list { display: grid; gap: 8px; }
    .admobile-grouping-card { padding: 12px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
    .admobile-grouping-card.is-end { background: #f8f9fb; }
    .admobile-grouping-card__topline { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 8px; }
    .admobile-grouping-badge { padding: 2px 7px; border-radius: 999px; background: #e4ebfb; color: #2c4d92; font-size: 10px; font-weight: 700; }
    .admobile-grouping-badge.is-soft { background: #eef1f5; color: #667085; }
    .admobile-grouping-state { padding: 2px 7px; border-radius: 999px; font-size: 10px; font-weight: 700; }
    .admobile-grouping-state.is-ing { background: #e9efff; color: #2450a6; }
    .admobile-grouping-state.is-end { background: #eef1f5; color: #667085; }
    .admobile-grouping-state.is-cancel { background: #fde8e8; color: #b42318; }
    .admobile-grouping-card h3 { margin: 0 0 10px; color: #172033; font-size: 16px; line-height: 1.4; overflow-wrap: anywhere; }
    .admobile-grouping-card dl { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px 12px; margin: 0; }
    .admobile-grouping-card dt { margin-bottom: 2px; color: #667085; font-size: 10px; }
    .admobile-grouping-card dd { overflow: hidden; margin: 0; color: #172033; font-size: 13px; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-grouping-card__link { display: block; margin-top: 12px; padding: 10px 12px; border-radius: 6px; background: #3056a8; color: #fff; font-size: 13px; font-weight: 700; text-align: center; text-decoration: none; }
    .admobile-grouping-card__link:active { background: #244687; }
    .admobile-empty { margin: 0; padding: 32px 16px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; color: #667085; text-align: center; }
    .admobile-pagination { margin-top: 20px; }
    .admobile-pagination .pagination ul { display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin: 0; padding: 0; list-style: none; }
    .admobile-pagination .pagination a { display: block; min-width: 32px; padding: 7px 8px; border: 1px solid #d0d5dd; border-radius: 6px; color: #344054; font-size: 13px; text-align: center; text-decoration: none; }
    .admobile-pagination .pagination .active a { border-color: #3056a8; background: #3056a8; color: #fff; }
</style>
