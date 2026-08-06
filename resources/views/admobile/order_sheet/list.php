<?php
$orderSheetList = $orderSheetList ?? [];
$totalCount = (int)($totalCount ?? 0);
$ooState = (string)($ooState ?? '4');
?>
<section class="admobile-order-sheet">
    <div class="admobile-page-heading">
        <a href="/admobile/main" aria-label="메뉴로 돌아가기">‹</a>
        <h2>주문(발주) 리스트</h2>
    </div>

    <form class="admobile-order-filter" method="get" action="/admobile/order/sheet/list">
        <label for="oo_state">주문상태</label>
        <select name="oo_state" id="oo_state" onchange="this.form.submit()">
            <option value="all" <?= $ooState === 'all' ? 'selected' : '' ?>>전체</option>
            <option value="ing" <?= $ooState === 'ing' ? 'selected' : '' ?>>진행중</option>
            <option value="1" <?= $ooState === '1' ? 'selected' : '' ?>>작성중</option>
            <option value="2" <?= $ooState === '2' ? 'selected' : '' ?>>주문전송</option>
            <option value="4" <?= $ooState === '4' ? 'selected' : '' ?>>입금완료</option>
            <option value="5" <?= $ooState === '5' ? 'selected' : '' ?>>입고완료</option>
            <option value="7" <?= $ooState === '7' ? 'selected' : '' ?>>주문종료</option>
        </select>
        <span>총 <strong><?= number_format($totalCount) ?></strong>건</span>
    </form>

    <div class="admobile-order-list">
        <?php if (empty($orderSheetList)) { ?>
            <p class="admobile-empty">등록된 주문서가 없습니다.</p>
        <?php } ?>

        <?php foreach ($orderSheetList as $orderSheet) {
            $createdAt = trim((string)($orderSheet['created_at'] ?? ''));
            $createdAtText = $createdAt !== '' ? date('Y.m.d H:i', strtotime($createdAt)) : '-';
        ?>
            <article class="admobile-order-card">
                <h3><?= htmlspecialchars((string)($orderSheet['oo_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h3>
                <dl>
                    <div>
                        <dt>고유번호</dt>
                        <dd><?= htmlspecialchars((string)($orderSheet['oo_idx'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt>수입구분</dt>
                        <dd><?= htmlspecialchars((string)($orderSheet['oo_import'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt>상태</dt>
                        <dd><span class="admobile-state"><?= htmlspecialchars((string)($orderSheet['oo_state_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></dd>
                    </div>
                    <div>
                        <dt>주문서폼</dt>
                        <dd><?= htmlspecialchars((string)($orderSheet['oog_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt>등록일</dt>
                        <dd><?= htmlspecialchars($createdAtText, ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                </dl>
                <a class="admobile-stock-button" href="/admobile/order/sheet/stock?idx=<?= (int)($orderSheet['oo_idx'] ?? 0) ?>">입고 수량검수</a>
            </article>
        <?php } ?>
    </div>

    <?php if (!empty($paginationHtml)) { ?>
        <div class="admobile-pagination"><?= $paginationHtml ?></div>
    <?php } ?>
</section>

<style>
    .admobile-page-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .admobile-page-heading a { color: #344054; font-size: 30px; line-height: 24px; text-decoration: none; }
    .admobile-page-heading h2 { margin: 0; font-size: 18px; }
    .admobile-order-filter { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; padding: 10px 12px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; font-size: 13px; }
    .admobile-order-filter label { color: #667085; font-size: 12px; }
    .admobile-order-filter select { min-width: 88px; padding: 6px 22px 6px 8px; border: 1px solid #cfd6e1; border-radius: 5px; background: #fff; color: #344054; font: inherit; }
    .admobile-order-filter span { margin-left: auto; color: #667085; white-space: nowrap; }
    .admobile-order-filter strong { color: #172033; }
    .admobile-order-list { display: grid; gap: 8px; }
    .admobile-order-card { padding: 11px 12px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
    .admobile-order-card h3 { margin: 0 0 10px; color: #172033; font-size: 16px; line-height: 1.4; overflow-wrap: anywhere; }
    .admobile-order-card dl { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px 12px; margin: 0; }
    .admobile-order-card dl > div { min-width: 0; }
    .admobile-order-card dt { margin-bottom: 3px; color: #667085; font-size: 10px; }
    .admobile-order-card dd { overflow: hidden; margin: 0; color: #172033; font-size: 14px; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-state { display: inline-block; padding: 3px 7px; border-radius: 999px; background: #e9efff; color: #2450a6; font-size: 12px; }
    .admobile-stock-button { display: block; margin-top: 12px; padding: 9px 12px; border-radius: 6px; background: #3056a8; color: #fff; font-size: 13px; font-weight: 700; text-align: center; text-decoration: none; }
    .admobile-stock-button:active { background: #244687; }
    .admobile-empty { margin: 0; padding: 32px 16px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; color: #667085; text-align: center; }
    .admobile-pagination { margin-top: 20px; }
    .admobile-pagination .pagination ul { display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin: 0; padding: 0; list-style: none; }
    .admobile-pagination .pagination a { display: block; min-width: 32px; padding: 7px 8px; border: 1px solid #d0d5dd; border-radius: 6px; color: #344054; font-size: 13px; text-align: center; text-decoration: none; }
    .admobile-pagination .pagination .active a { border-color: #3056a8; background: #3056a8; color: #fff; }
</style>
