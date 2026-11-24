<?php
$rackGroups = $rackGroups ?? [];
$productGroups = $productGroups ?? [];
$group = (string)($group ?? '');
$searchValue = (string)($searchValue ?? '');
$showProducts = $group !== '' || $searchValue !== '';

$inspections = is_array($inspections ?? null) ? $inspections : [];
$activeInspection = is_array($activeInspection ?? null) ? $activeInspection : null;
$inspectionChecks = is_array($inspectionChecks ?? null) ? $inspectionChecks : [];
$formatInspectionTime = static function ($value): string {
    $value = trim((string)$value);
    $timestamp = $value !== '' ? strtotime($value) : false;
    return $timestamp ? date('Y-m-d H:i', $timestamp) : ($value !== '' ? $value : '-');
};

$currentParams = [];
if ($group !== '') {
    $currentParams['group'] = $group;
}
if ($activeInspection) {
    $currentParams['inspection'] = (int)$activeInspection['idx'];
}
if ($searchValue !== '') {
    $currentParams['search_value'] = $searchValue;
}
$currentQuery = http_build_query($currentParams);
$currentUrl = '/admobile/product/rack' . ($currentQuery !== '' ? '?' . $currentQuery : '');
$inspectionExitParams = [];
if ($group !== '') {
    $inspectionExitParams['group'] = $group;
}
if ($searchValue !== '') {
    $inspectionExitParams['search_value'] = $searchValue;
}
$inspectionExitQuery = http_build_query($inspectionExitParams);
$inspectionExitUrl = '/admobile/product/rack' . ($inspectionExitQuery !== '' ? '?' . $inspectionExitQuery : '');
$backUrl = $activeInspection ? $inspectionExitUrl : ($showProducts ? '/admobile/product/rack' : '/admobile/main');
$pageHeading = $activeInspection
    ? (string)$activeInspection['title']
    : ($group !== '' ? $group : '랙별 상품 재고관리');
?>
<section class="admobile-rack-stock">
    <div class="admobile-rack-top<?= $activeInspection ? ' is-inspection' : '' ?>">
    <div class="admobile-rack-heading"<?php if ($group !== '') { ?> data-group="<?= htmlspecialchars($group, ENT_QUOTES, 'UTF-8') ?>" data-inspection-idx="<?= $activeInspection ? (int)$activeInspection['idx'] : 0 ?>"<?php } ?>>
        <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" aria-label="<?= $activeInspection ? '검수 전 화면으로 돌아가기' : '이전으로 돌아가기' ?>"><?php if ($activeInspection) { ?><span aria-hidden="true">‹</span><em>종료</em><?php } else { ?>‹<?php } ?></a>
        <div class="admobile-rack-heading__main">
            <?php if ($activeInspection) { ?>
                <p class="admobile-rack-mode">검수모드</p>
            <?php } ?>
            <h2><?= htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8') ?></h2>
            <?php if ($group !== '') { ?>
                <p><?= $activeInspection ? htmlspecialchars($group, ENT_QUOTES, 'UTF-8') . ' · ' : '' ?><?= number_format((int)($rackGroups[$group]['product_count'] ?? 0)) ?>개 상품</p>
                <?php if ($activeInspection) { ?>
                    <?php
                    $inspectionCreator = trim((string)($activeInspection['created_by_name'] ?? ''));
                    if ($inspectionCreator === '') {
                        $inspectionCreator = '알 수 없음';
                    }
                    ?>
                    <p>생성 <?= htmlspecialchars($inspectionCreator, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($formatInspectionTime($activeInspection['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                <?php } ?>
            <?php } else { ?>
                <p>랙 묶음 <?= number_format(count($rackGroups)) ?>개</p>
            <?php } ?>
        </div>
        <?php if ($group !== '') { ?>
            <button type="button" class="admobile-rack-log-open" data-inspection-log-open aria-label="검수로그">
                <span aria-hidden="true"></span>
                <em>검수로그</em>
            </button>
        <?php } ?>
    </div>

    <?php if ($group !== '' && !$activeInspection) { ?>
        <div class="admobile-rack-inspection">
            <button type="button" id="rack-inspection-start">재고검수시작</button>
            <p id="rack-inspection-message" aria-live="polite"></p>
        </div>
    <?php } elseif ($activeInspection) { ?>
        <!--
        <p class="admobile-rack-inspection__active">
            <?= htmlspecialchars((string)$activeInspection['title'], ENT_QUOTES, 'UTF-8') ?> 검수중
            <?php if ((int)$activeInspection['checked_count'] === 0) { ?>
                · 검수 기록은 아직 없습니다.
            <?php } ?>
        </p>
        -->
    <?php } ?>

    <form class="admobile-rack-search" method="get" action="/admobile/product/rack">
        <?php if ($group !== '') { ?>
            <input type="hidden" name="group" value="<?= htmlspecialchars($group, ENT_QUOTES, 'UTF-8') ?>">
        <?php } ?>
        <?php if ($activeInspection) { ?>
            <input type="hidden" name="inspection" value="<?= (int)$activeInspection['idx'] ?>">
        <?php } ?>
        <label for="rack-stock-search">바코드, 상품명, 랙코드 검색</label>
        <div class="admobile-rack-search__input">
            <input id="rack-stock-search" name="search_value" type="search" value="<?= htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8') ?>" placeholder="바코드, 상품명, 랙코드">
            <button type="submit">검색</button>
        </div>
    </form>
    </div>

    <?php if ($group !== '') { ?>
        <?php $recentInspections = array_slice($inspections, 0, 10); ?>
        <div id="admobile-rack-log-modal" class="admobile-rack-log-modal" hidden>
            <div class="admobile-rack-log-modal__backdrop" data-inspection-log-close></div>
            <section class="admobile-rack-log-modal__content" role="dialog" aria-modal="true" aria-labelledby="admobile-rack-log-title">
                <div class="admobile-rack-log-modal__heading">
                    <div>
                        <p><?= htmlspecialchars($group, ENT_QUOTES, 'UTF-8') ?></p>
                        <h2 id="admobile-rack-log-title">검수로그</h2>
                    </div>
                    <button type="button" data-inspection-log-close aria-label="검수로그 닫기">×</button>
                </div>
                <?php if ($recentInspections === []) { ?>
                    <p class="admobile-rack-log-modal__empty">검수 없음</p>
                <?php } else { ?>
                    <div class="admobile-rack-log-modal__list">
                        <?php foreach ($recentInspections as $inspection) { ?>
                            <?php
                            $inspectionParams = ['group' => $group, 'inspection' => (int)$inspection['idx']];
                            if ($searchValue !== '') {
                                $inspectionParams['search_value'] = $searchValue;
                            }
                            $isActiveInspection = $activeInspection && (int)$activeInspection['idx'] === (int)$inspection['idx'];
                            $creatorName = trim((string)($inspection['created_by_name'] ?? ''));
                            if ($creatorName === '') {
                                $creatorName = '알 수 없음';
                            }
                            ?>
                            <a class="admobile-rack-inspection__item<?= $isActiveInspection ? ' is-active' : '' ?>" href="/admobile/product/rack?<?= htmlspecialchars(http_build_query($inspectionParams), ENT_QUOTES, 'UTF-8') ?>">
                                <strong><?= htmlspecialchars((string)$inspection['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <span>생성 <?= htmlspecialchars($creatorName, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($formatInspectionTime($inspection['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                <em data-inspection-count="<?= (int)$inspection['checked_count'] ?>"><?= (int)$inspection['checked_count'] > 0 ? '검수 ' . number_format((int)$inspection['checked_count']) . '건' : '체크 전' ?></em>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </section>
        </div>
    <?php } ?>

    <?php if (!$showProducts) { ?>
        <?php if (empty($rackGroups)) { ?>
            <p class="admobile-rack-empty">등록된 랙 재고가 없습니다.</p>
        <?php } else { ?>
            <div class="admobile-rack-groups">
                <?php foreach ($rackGroups as $rackGroup) { ?>
                    <a class="admobile-rack-group" href="/admobile/product/rack?group=<?= rawurlencode((string)$rackGroup['group']) ?>">
                        <strong><?= htmlspecialchars((string)$rackGroup['group'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <span>상품 <?= number_format((int)$rackGroup['product_count']) ?> · 재고 <?= number_format((int)$rackGroup['stock_sum']) ?></span>
                        <em aria-hidden="true">›</em>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } elseif (empty($productGroups)) { ?>
        <p class="admobile-rack-empty">조건에 맞는 상품이 없습니다.</p>
    <?php } else { ?>
        <?php foreach ($productGroups as $groupKey => $products) { ?>
            <section class="admobile-rack-products">
                <?php if ($group === '') { ?>
                    <h3><?= htmlspecialchars((string)$groupKey, ENT_QUOTES, 'UTF-8') ?></h3>
                <?php } ?>
                <?php foreach ($products as $product) { ?>
                    <?php
                    $detailUrl = '';
                    if ((int)$product['prd_idx'] > 0) {
                        $detailUrl = '/admobile/product/detail?prd_idx=' . (int)$product['prd_idx']
                            . '&return_to=' . rawurlencode($currentUrl);
                    }
                    $barcode = trim((string)$product['barcode']);
                    $image = trim((string)($product['image'] ?? ''));
                    $productName = trim((string)($product['name'] ?? ''));
                    if ($productName === '') {
                        $productName = '상품명 없음';
                    }
                    $isEmptyStock = (int)$product['stock'] === 0;
                    $checkKey = (int)($product['ps_idx'] ?? 0) . ':' . (int)$product['prd_idx'];
                    $inspectionCheck = $activeInspection ? ($inspectionChecks[$checkKey] ?? null) : null;
                    $checkedByName = trim((string)($inspectionCheck['checked_by_name'] ?? ''));
                    if ($checkedByName === '') {
                        $checkedByName = '알 수 없음';
                    }
                    $isInspectionChecked = is_array($inspectionCheck);
                    $checkedStockText = '검수재고 -';
                    if ($isInspectionChecked && ($inspectionCheck['stock_qty'] ?? null) !== null) {
                        $checkedStockText = '검수재고 ' . number_format((int)$inspectionCheck['stock_qty']);
                    }
                    ?>
                    <article
                        class="admobile-rack-product<?= $isEmptyStock ? ' admobile-rack-product--empty' : '' ?>"
                        data-prd-idx="<?= (int)$product['prd_idx'] ?>"
                        data-ps-idx="<?= (int)($product['ps_idx'] ?? 0) ?>"
                        data-stock="<?= (int)$product['stock'] ?>"
                        data-rack-code="<?= htmlspecialchars((string)($product['sort_rack'] ?? $product['rack_code']), ENT_QUOTES, 'UTF-8') ?>"
                    >
                        <div class="admobile-rack-product__media">
                            <div class="admobile-rack-product__thumb">
                                <?php if ($image !== '') { ?>
                                    <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="">
                                <?php } else { ?>
                                    <span>없음</span>
                                <?php } ?>
                            </div>
                            <?php if ($isInspectionChecked) { ?>
                                <i class="admobile-rack-product__checked" aria-label="검수완료"><b>✓</b><span>검수완료</span></i>
                                <button type="button" class="admobile-rack-product__check-cancel" data-inspection-cancel>검수완료 취소</button>
                            <?php } ?>
                        </div>
                        <div class="admobile-rack-product__body">
                            <?php if ($detailUrl !== '') { ?>
                                <a class="admobile-rack-product__name" href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?></a>
                            <?php } else { ?>
                                <p class="admobile-rack-product__name"><?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php } ?>
                            <div class="admobile-rack-product__main">
                                <?php if ((int)$product['prd_idx'] > 0) { ?>
                                    <button type="button" class="admobile-rack-product__rack" data-rack-edit><?= htmlspecialchars((string)$product['rack_code'], ENT_QUOTES, 'UTF-8') ?></button>
                                <?php } else { ?>
                                    <p class="admobile-rack-product__rack"><?= htmlspecialchars((string)$product['rack_code'], ENT_QUOTES, 'UTF-8') ?></p>
                                <?php } ?>
                                <p class="admobile-rack-product__stock">현재고 <strong class="admobile-rack-product__stock-value"><?= number_format((int)$product['stock']) ?></strong></p>
                            </div>
                            <p class="admobile-rack-product__barcode">
                                <span>바코드</span>
                                <?= $barcode !== '' ? htmlspecialchars($barcode, ENT_QUOTES, 'UTF-8') : '-' ?>
                            </p>
                            <?php if ($activeInspection && (int)$product['prd_idx'] > 0 && (int)($product['ps_idx'] ?? 0) > 0) { ?>
                                <?php if ($isInspectionChecked) { ?>
                                    <div class="admobile-rack-product__check is-done">
                                        <strong><?= htmlspecialchars($checkedStockText, ENT_QUOTES, 'UTF-8') ?></strong>
                                        <span><?= htmlspecialchars($formatInspectionTime($inspectionCheck['checked_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($checkedByName, ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="admobile-rack-product__check">
                                        <button type="button" data-inspection-check>검수완료</button>
                                        <span class="admobile-rack-product__check-message" aria-live="polite"></span>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <?php if ((int)$product['prd_idx'] > 0) { ?>
                                <div class="admobile-rack-product__quick">
                                    <button type="button" data-stock-action="minus">출고</button>
                                    <input type="number" min="1" step="1" inputmode="numeric" value="1" aria-label="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?> 변경 수량">
                                    <button type="button" data-stock-action="plus">입고</button>
                                </div>
                                <p class="admobile-rack-product__quick-message" aria-live="polite"></p>
                            <?php } ?>
                        </div>
                    </article>
                <?php } ?>
            </section>
        <?php } ?>
    <?php } ?>
</section>

<div id="admobile-rack-code-modal" class="admobile-rack-code-modal" hidden>
    <div class="admobile-rack-code-modal__backdrop" data-rack-close></div>
    <section class="admobile-rack-code-modal__content" role="dialog" aria-modal="true" aria-labelledby="admobile-rack-code-title">
        <form id="admobile-rack-code-form">
            <div class="admobile-rack-code-modal__heading">
                <div>
                    <p id="admobile-rack-code-product">랙 코드</p>
                    <h2 id="admobile-rack-code-title">랙 코드 변경</h2>
                </div>
                <button type="button" data-rack-close aria-label="랙 코드 변경 닫기">×</button>
            </div>
            <label for="admobile-rack-code-input">새 랙 코드</label>
            <input id="admobile-rack-code-input" type="text" maxlength="100" autocomplete="off" placeholder="예) C01-2-1">
            <p id="admobile-rack-code-message" aria-live="polite"></p>
            <div class="admobile-rack-code-modal__actions">
                <button type="button" data-rack-close>취소</button>
                <button type="submit">저장</button>
            </div>
        </form>
    </section>
</div>

<style>
    .admobile-rack-heading { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 12px; }
    .admobile-rack-heading > a { display: flex; flex: 0 0 36px; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: #fff; color: #172033; font-size: 28px; line-height: 1; text-decoration: none; }
    .admobile-rack-heading > a em { font-style: normal; }
    .admobile-rack-heading__main { flex: 1; min-width: 0; }
    .admobile-rack-heading h2 { margin: 0; font-size: 20px; }
    .admobile-rack-heading__main p { margin: 2px 0 0; color: #667085; font-size: 13px; }
    .admobile-rack-mode { margin: 0 0 2px; color: #6ce9a6; font-size: 11px; font-weight: 800; letter-spacing: .04em; }
    .admobile-rack-top.is-inspection { margin: -16px -16px 16px; padding: 16px 16px 4px; background: #10281c; color: #f4fff8; }
    .admobile-rack-top.is-inspection .admobile-rack-heading > a { flex-basis: auto; width: auto; height: auto; min-height: 36px; padding: 0 10px; gap: 2px; background: #027a48; color: #fff; font-size: 20px; }
    .admobile-rack-top.is-inspection .admobile-rack-heading > a em { font-size: 12px; font-weight: 800; }
    .admobile-rack-top.is-inspection .admobile-rack-heading h2,
    .admobile-rack-top.is-inspection .admobile-rack-heading__main p,
    .admobile-rack-top.is-inspection .admobile-rack-inspection__active { color: #f4fff8; }
    .admobile-rack-top.is-inspection .admobile-rack-search { background: #0c2118; border-color: #1f5c3a; }
    .admobile-rack-top.is-inspection .admobile-rack-search label { color: #b7ebc9; }
    .admobile-rack-top.is-inspection .admobile-rack-search input[type="search"] { background: #fff; color: #172033; }
    .admobile-rack-log-open { display: flex; flex: 0 0 auto; gap: 6px; align-items: center; justify-content: center; height: 36px; padding: 0 10px; border: 0; border-radius: 8px; background: #fff; color: #172033; font: inherit; font-size: 12px; font-weight: 800; white-space: nowrap; }
    .admobile-rack-log-open em { font-style: normal; }
    .admobile-rack-log-open span { position: relative; display: block; width: 16px; height: 2px; border-radius: 1px; background: currentColor; }
    .admobile-rack-log-open span::before,
    .admobile-rack-log-open span::after { position: absolute; left: 0; width: 16px; height: 2px; border-radius: 1px; background: currentColor; content: ""; }
    .admobile-rack-log-open span::before { top: -5px; }
    .admobile-rack-log-open span::after { top: 5px; }
    .admobile-rack-top.is-inspection .admobile-rack-log-open { background: #027a48; color: #fff; }
    .admobile-rack-search { margin-bottom: 12px; padding: 12px; background: #fff; border: 1px solid #e3e7ee; border-radius: 12px; }
    .admobile-rack-search label { display: block; margin-bottom: 8px; color: #475467; font-size: 12px; font-weight: 700; }
    .admobile-rack-search__input { display: flex; gap: 8px; }
    .admobile-rack-search input[type="search"] { flex: 1; min-width: 0; height: 42px; padding: 0 12px; border: 1px solid #d0d5dd; border-radius: 8px; font: inherit; }
    .admobile-rack-search button { height: 42px; padding: 0 14px; border: 0; border-radius: 8px; background: #121b29; color: #fff; font: inherit; font-weight: 700; }
    .admobile-rack-empty { margin: 0; padding: 28px 16px; background: #fff; border: 1px solid #e3e7ee; border-radius: 12px; color: #667085; text-align: center; }
    .admobile-rack-groups, .admobile-rack-products { overflow: hidden; background: #fff; border: 1px solid #e3e7ee; border-radius: 12px; }
    .admobile-rack-products + .admobile-rack-products { margin-top: 12px; }
    .admobile-rack-group { display: grid; grid-template-columns: auto 1fr auto; gap: 8px; align-items: center; padding: 16px; color: #172033; text-decoration: none; }
    .admobile-rack-group + .admobile-rack-group { border-top: 1px solid #eef1f5; }
    .admobile-rack-group strong { font-size: 18px; }
    .admobile-rack-group span { color: #667085; font-size: 13px; text-align: right; }
    .admobile-rack-group em { color: #98a2b3; font-size: 24px; font-style: normal; line-height: 16px; }
    .admobile-rack-products h3 { margin: 0; padding: 14px 16px; border-bottom: 1px solid #e3e7ee; font-size: 16px; }
    .admobile-rack-product { display: flex; gap: 12px; align-items: flex-start; padding: 14px 16px; }
    .admobile-rack-product + .admobile-rack-product { border-top: 1px solid #eef1f5; }
    .admobile-rack-product__media { display: flex; flex: 0 0 76px; flex-direction: column; align-items: center; width: 76px; }
    .admobile-rack-product__thumb { display: flex; align-items: center; justify-content: center; width: 64px; height: 64px; overflow: hidden; border: 1px solid #edf0f4; border-radius: 8px; background: #f8fafc; color: #98a2b3; font-size: 11px; }
    .admobile-rack-product__checked { display: flex; gap: 2px; align-items: center; justify-content: center; width: 100%; margin-top: 6px; padding: 5px 0; border-radius: 8px; background: #027a48; color: #fff; font-style: normal; line-height: 1; }
    .admobile-rack-product__checked b { font-size: 18px; font-weight: 800; }
    .admobile-rack-product__checked span { font-size: 11px; font-weight: 800; }
    .admobile-rack-product__check-cancel { width: 100%; margin-top: 4px; padding: 6px 2px; border: 0; border-radius: 8px; background: #f2f4f7; color: #b42318; font: inherit; font-size: 11px; font-weight: 800; line-height: 1.2; }
    .admobile-rack-product__check-cancel:disabled { opacity: .45; }
    .admobile-rack-product__thumb img { width: 100%; height: 100%; object-fit: cover; }
    .admobile-rack-product__body { flex: 1; min-width: 0; }
    .admobile-rack-product__name { display: block; margin: 0 0 5px; color: #172033; font-size: 15px; font-weight: 700; line-height: 1.4; text-decoration: none; word-break: break-word; }
    .admobile-rack-product__main { display: flex; gap: 12px; align-items: baseline; justify-content: space-between; }
    .admobile-rack-product__rack, .admobile-rack-product__stock, .admobile-rack-product__barcode { margin: 0; }
    .admobile-rack-product__rack { margin: 0; padding: 0; border: 0; background: transparent; color: rgb(0, 45, 141); font: inherit; font-size: 18px; font-weight: 800; letter-spacing: -.01em; text-align: left; }
    button.admobile-rack-product__rack { cursor: pointer; text-decoration: underline; text-underline-offset: 3px; }
    .admobile-rack-product__stock { color: #475467; font-size: 13px; white-space: nowrap; }
    .admobile-rack-product__stock strong { color: #172033; font-size: 20px; }
    .admobile-rack-product__barcode { margin-top: 4px; color: #172033; font-size: 14px; font-weight: 700; }
    .admobile-rack-product__barcode span { margin-right: 6px; color: #667085; font-size: 12px; font-weight: 700; }
    .admobile-rack-product--empty { background: #f4f6f8; }
    .admobile-rack-product--empty .admobile-rack-product__thumb img { filter: grayscale(1); opacity: .4; }
    .admobile-rack-product--empty .admobile-rack-product__name,
    .admobile-rack-product--empty .admobile-rack-product__rack,
    .admobile-rack-product--empty .admobile-rack-product__stock,
    .admobile-rack-product--empty .admobile-rack-product__stock strong,
    .admobile-rack-product--empty .admobile-rack-product__barcode,
    .admobile-rack-product--empty .admobile-rack-product__barcode span { color: #98a2b3; }
    .admobile-rack-product__quick { display: flex; gap: 6px; align-items: center; margin-top: 8px; }
    .admobile-rack-product__quick button { flex: 1; height: 36px; border: 0; border-radius: 8px; font: inherit; font-size: 13px; font-weight: 800; }
    .admobile-rack-product__quick button[data-stock-action="minus"] { background: #eef1f5; color: #344054; }
    .admobile-rack-product__quick button[data-stock-action="plus"] { background: #173d8f; color: #fff; }
    .admobile-rack-product__quick input { width: 68px; height: 36px; border: 1px solid #d0d5dd; border-radius: 8px; background: #fff; color: #172033; font: inherit; font-size: 16px; font-weight: 800; text-align: center; }
    .admobile-rack-product__quick button:disabled,
    .admobile-rack-product__quick input:disabled { opacity: .45; }
    .admobile-rack-product__quick-message { margin: 4px 0 0; color: #b42318; font-size: 12px; }
    .admobile-rack-product__quick-message:empty { display: none; }
    .admobile-rack-inspection { margin-bottom: 12px; padding: 12px; background: #fff; border: 1px solid #e3e7ee; border-radius: 12px; }
    .admobile-rack-inspection > button { width: 100%; height: 44px; border: 0; border-radius: 8px; background: #173d8f; color: #fff; font: inherit; font-weight: 800; }
    .admobile-rack-inspection > button:disabled { opacity: .45; }
    #rack-inspection-message { margin: 8px 0 0; color: #b42318; font-size: 12px; }
    #rack-inspection-message:empty { display: none; }
    .admobile-rack-log-modal[hidden] { display: none; }
    .admobile-rack-log-modal { position: fixed; z-index: 1200; inset: 0; display: flex; align-items: flex-end; }
    .admobile-rack-log-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .58); }
    .admobile-rack-log-modal__content { position: relative; width: 100%; max-height: 78vh; padding: 20px 16px calc(16px + env(safe-area-inset-bottom)); overflow: auto; border-radius: 18px 18px 0 0; background: #fff; }
    .admobile-rack-log-modal__heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
    .admobile-rack-log-modal__heading p { margin: 0 0 3px; color: #667085; font-size: 12px; font-weight: 700; }
    .admobile-rack-log-modal__heading h2 { margin: 0; color: #172033; font-size: 19px; }
    .admobile-rack-log-modal__heading button { width: 34px; height: 34px; border: 0; border-radius: 50%; background: #f2f4f7; color: #344054; font-size: 23px; line-height: 1; }
    .admobile-rack-log-modal__empty { margin: 0; padding: 28px 8px; color: #667085; text-align: center; }
    .admobile-rack-log-modal__list { overflow: hidden; border: 1px solid #e3e7ee; border-radius: 12px; }
    .admobile-rack-log-modal .admobile-rack-inspection__item { display: grid; grid-template-columns: 1fr auto; gap: 2px 8px; padding: 12px; color: #172033; text-decoration: none; }
    .admobile-rack-log-modal .admobile-rack-inspection__item + .admobile-rack-inspection__item { border-top: 1px solid #eef1f5; }
    .admobile-rack-log-modal .admobile-rack-inspection__item strong { min-width: 0; overflow: hidden; font-size: 15px; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-rack-log-modal .admobile-rack-inspection__item span { grid-column: 1; color: #667085; font-size: 12px; }
    .admobile-rack-log-modal .admobile-rack-inspection__item em { grid-row: 1 / span 2; align-self: center; color: #667085; font-size: 12px; font-style: normal; font-weight: 700; }
    .admobile-rack-log-modal .admobile-rack-inspection__item.is-active { background: #f3f6fb; }
    .admobile-rack-log-modal .admobile-rack-inspection__item.is-active strong,
    .admobile-rack-log-modal .admobile-rack-inspection__item.is-active em { color: #173d8f; }
    .admobile-rack-inspection__active { margin: -4px 0 12px; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-rack-product__check { display: flex; gap: 8px; align-items: center; margin-top: 8px; }
    .admobile-rack-product__check button { height: 36px; padding: 0 12px; border: 0; border-radius: 8px; background: #027a48; color: #fff; font: inherit; font-size: 13px; font-weight: 800; }
    .admobile-rack-product__check button:disabled { opacity: .45; }
    .admobile-rack-product__check.is-done { flex-direction: column; align-items: flex-start; gap: 2px; }
    .admobile-rack-product__check.is-done strong { color: #027a48; font-size: 13px; }
    .admobile-rack-product__check.is-done span,
    .admobile-rack-product__check-message { color: #667085; font-size: 12px; }
    .admobile-rack-product__check-message:empty { display: none; }
    .admobile-rack-code-modal[hidden] { display: none; }
    .admobile-rack-code-modal { position: fixed; z-index: 1200; inset: 0; display: flex; align-items: flex-end; }
    .admobile-rack-code-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .58); }
    .admobile-rack-code-modal__content { position: relative; width: 100%; padding: 20px 16px calc(16px + env(safe-area-inset-bottom)); border-radius: 18px 18px 0 0; background: #fff; }
    .admobile-rack-code-modal__heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .admobile-rack-code-modal__heading p { margin: 0 0 3px; color: #667085; font-size: 12px; font-weight: 700; }
    .admobile-rack-code-modal__heading h2 { margin: 0; color: #172033; font-size: 19px; }
    .admobile-rack-code-modal__heading button { width: 34px; height: 34px; border: 0; border-radius: 50%; background: #f2f4f7; color: #344054; font-size: 23px; line-height: 1; }
    .admobile-rack-code-modal label { display: block; margin: 18px 0 7px; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-rack-code-modal input { width: 100%; padding: 13px; border: 1px solid #8fa5d3; border-radius: 8px; color: #172033; font: inherit; font-size: 20px; font-weight: 700; outline-color: #3056a8; }
    #admobile-rack-code-message { min-height: 16px; margin: 8px 0 0; color: #b42318; font-size: 12px; }
    .admobile-rack-code-modal__actions { display: grid; grid-template-columns: 88px 1fr; gap: 8px; margin-top: 10px; }
    .admobile-rack-code-modal__actions button { min-height: 46px; border: 0; border-radius: 8px; background: #eef1f5; color: #475467; font: inherit; font-size: 14px; font-weight: 700; }
    .admobile-rack-code-modal__actions button[type="submit"] { background: #3056a8; color: #fff; }
    .admobile-rack-code-modal__actions button:disabled { opacity: .45; }
</style>

<script>
    (function() {
        var list = document.querySelector('.admobile-rack-stock');
        if (!list) {
            return;
        }

        function todayText() {
            var today = new Date();
            var month = String(today.getMonth() + 1).padStart(2, '0');
            var day = String(today.getDate()).padStart(2, '0');
            return today.getFullYear() + '-' + month + '-' + day;
        }

        function formatCount(value) {
            return Number(value || 0).toLocaleString('ko-KR');
        }

        function sortSection(section) {
            var products = Array.prototype.slice.call(section.querySelectorAll('.admobile-rack-product'));
            products.sort(function(left, right) {
                var rackCompare = String(right.dataset.rackCode || '').localeCompare(String(left.dataset.rackCode || ''), undefined, {
                    numeric: true,
                    sensitivity: 'base'
                });
                if (rackCompare !== 0) {
                    return rackCompare;
                }
                return Number(right.dataset.stock || 0) - Number(left.dataset.stock || 0);
            });
            products.forEach(function(product) {
                section.appendChild(product);
            });
        }

        function setBusy(article, busy) {
            article.querySelectorAll('.admobile-rack-product__quick button, .admobile-rack-product__quick input').forEach(function(control) {
                control.disabled = busy;
            });
        }

        var logModal = document.getElementById('admobile-rack-log-modal');
        var logOpen = list.querySelector('[data-inspection-log-open]');
        if (logModal && logOpen) {
            logOpen.addEventListener('click', function() {
                logModal.hidden = false;
            });
            logModal.addEventListener('click', function(event) {
                if (event.target.closest('[data-inspection-log-close]')) {
                    logModal.hidden = true;
                }
            });
        }

        var rackModal = document.getElementById('admobile-rack-code-modal');
        var rackForm = document.getElementById('admobile-rack-code-form');
        var rackInput = document.getElementById('admobile-rack-code-input');
        var rackMessage = document.getElementById('admobile-rack-code-message');
        var rackProduct = document.getElementById('admobile-rack-code-product');
        var rackArticle = null;

        function closeRackModal() {
            if (rackModal) {
                rackModal.hidden = true;
            }
            rackArticle = null;
        }

        function rackBelongsToGroup(rackCode, group) {
            if (!group) {
                return true;
            }
            var parts = String(rackCode || '').split(/[\/／]/);
            return parts.some(function(part) {
                return String(part).trim().split('-')[0].trim().toUpperCase() === group;
            });
        }

        if (rackModal && rackForm) {
            list.addEventListener('click', function(event) {
                var rackButton = event.target.closest('[data-rack-edit]');
                if (!rackButton || !list.contains(rackButton)) {
                    return;
                }
                rackArticle = rackButton.closest('.admobile-rack-product');
                rackInput.value = rackButton.textContent.trim();
                rackProduct.textContent = rackArticle.querySelector('.admobile-rack-product__name').textContent.trim();
                rackMessage.textContent = '';
                rackModal.hidden = false;
                window.setTimeout(function() {
                    rackInput.focus();
                    rackInput.select();
                }, 100);
            });

            rackModal.addEventListener('click', function(event) {
                if (event.target.closest('[data-rack-close]')) {
                    closeRackModal();
                }
            });

            rackForm.addEventListener('submit', function(event) {
                event.preventDefault();
                if (!rackArticle) {
                    rackMessage.textContent = '상품 정보가 없습니다.';
                    return;
                }
                var saveButton = rackForm.querySelector('[type="submit"]');
                var article = rackArticle;
                saveButton.disabled = true;
                rackMessage.textContent = '';
                fetch('/admobile/product/action', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: new URLSearchParams({
                        action_mode: 'rack_code_update',
                        prd_idx: article.dataset.prdIdx || '0',
                        ps_idx: article.dataset.psIdx || '0',
                        rack_code: rackInput.value
                    })
                })
                    .then(function(response) {
                        return response.json().then(function(result) {
                            if (!response.ok || !result.success) {
                                throw new Error((result && result.message) || '랙 코드 변경에 실패했습니다.');
                            }
                            return result;
                        });
                    })
                    .then(function(result) {
                        var rackCode = result.rack_code || '';
                        var group = new URLSearchParams(window.location.search).get('group') || '';
                        if (group && !rackBelongsToGroup(rackCode, group.toUpperCase())) {
                            window.location.reload();
                            return;
                        }
                        var rackButton = article.querySelector('[data-rack-edit]');
                        rackButton.textContent = rackCode || '-';
                        article.dataset.rackCode = rackCode;
                        var section = article.closest('.admobile-rack-products');
                        if (section) {
                            sortSection(section);
                        }
                        closeRackModal();
                    })
                    .catch(function(error) {
                        rackMessage.textContent = error.message || '랙 코드 변경에 실패했습니다.';
                    })
                    .finally(function() {
                        saveButton.disabled = false;
                    });
            });
        }

        var inspectionBox = list.querySelector('.admobile-rack-heading[data-group]');
        var inspectionStart = document.getElementById('rack-inspection-start');
        if (inspectionBox && inspectionStart) {
            inspectionStart.addEventListener('click', function() {
                var message = document.getElementById('rack-inspection-message');
                message.textContent = '';
                inspectionStart.disabled = true;
                fetch('/admobile/product/action', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: new URLSearchParams({
                        action_mode: 'start_rack_stock_inspection',
                        group: inspectionBox.dataset.group || ''
                    })
                })
                    .then(function(response) {
                        return response.json().then(function(result) {
                            if (!response.ok || !result.success || !result.inspection) {
                                throw new Error((result && result.message) || '재고검수를 시작하지 못했습니다.');
                            }
                            return result;
                        });
                    })
                    .then(function(result) {
                        var params = new URLSearchParams(window.location.search);
                        params.set('group', inspectionBox.dataset.group || '');
                        params.set('inspection', String(result.inspection.idx || ''));
                        window.location.href = '/admobile/product/rack?' + params.toString();
                    })
                    .catch(function(error) {
                        message.textContent = error.message || '재고검수를 시작하지 못했습니다.';
                        inspectionStart.disabled = false;
                    });
            });
        }

        function inspectionCount(delta) {
            var countNode = list.querySelector('.admobile-rack-inspection__item.is-active em');
            if (!countNode) {
                return 0;
            }
            var nextCount = Math.max(0, Number(countNode.dataset.inspectionCount || 0) + delta);
            countNode.dataset.inspectionCount = String(nextCount);
            countNode.textContent = nextCount > 0 ? '검수 ' + nextCount.toLocaleString('ko-KR') + '건' : '체크 전';
            return nextCount;
        }

        list.addEventListener('click', function(event) {
            var cancelButton = event.target.closest('[data-inspection-cancel]');
            if (cancelButton && list.contains(cancelButton)) {
                var cancelArticle = cancelButton.closest('.admobile-rack-product');
                cancelButton.disabled = true;
                fetch('/admobile/product/action', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: new URLSearchParams({
                        action_mode: 'cancel_rack_stock_inspection',
                        inspection_idx: inspectionBox ? (inspectionBox.dataset.inspectionIdx || '0') : '0',
                        prd_idx: cancelArticle.dataset.prdIdx || '0',
                        ps_idx: cancelArticle.dataset.psIdx || '0'
                    })
                })
                    .then(function(response) {
                        return response.json().then(function(result) {
                            if (!response.ok || !result.success) {
                                throw new Error((result && result.message) || '검수완료 취소에 실패했습니다.');
                            }
                            return result;
                        });
                    })
                    .then(function() {
                        var checkedIcon = cancelArticle.querySelector('.admobile-rack-product__checked');
                        if (checkedIcon) {
                            checkedIcon.remove();
                        }
                        cancelButton.remove();
                        var checkWrap = cancelArticle.querySelector('.admobile-rack-product__check');
                        if (checkWrap) {
                            checkWrap.className = 'admobile-rack-product__check';
                            checkWrap.innerHTML = '<button type="button" data-inspection-check>검수완료</button><span class="admobile-rack-product__check-message" aria-live="polite"></span>';
                        }
                        if (inspectionCount(-1) === 0) {
                            var activeNote = list.querySelector('.admobile-rack-inspection__active');
                            if (activeNote && activeNote.textContent.indexOf('검수 기록은 아직 없습니다.') === -1) {
                                activeNote.textContent = activeNote.textContent.replace(/\s*$/, '') + ' · 검수 기록은 아직 없습니다.';
                            }
                        }
                    })
                    .catch(function(error) {
                        cancelButton.disabled = false;
                        window.alert(error.message || '검수완료 취소에 실패했습니다.');
                    });
                return;
            }

            var checkButton = event.target.closest('[data-inspection-check]');
            if (checkButton && list.contains(checkButton)) {
                var checkArticle = checkButton.closest('.admobile-rack-product');
                var checkWrap = checkButton.closest('.admobile-rack-product__check');
                var checkMessage = checkWrap.querySelector('.admobile-rack-product__check-message');
                checkMessage.textContent = '';
                checkButton.disabled = true;
                fetch('/admobile/product/action', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: new URLSearchParams({
                        action_mode: 'complete_rack_stock_inspection',
                        inspection_idx: inspectionBox ? (inspectionBox.dataset.inspectionIdx || '0') : '0',
                        prd_idx: checkArticle.dataset.prdIdx || '0',
                        ps_idx: checkArticle.dataset.psIdx || '0'
                    })
                })
                    .then(function(response) {
                        return response.json().then(function(result) {
                            if (!response.ok || !result.success) {
                                throw new Error((result && result.message) || '검수완료 처리에 실패했습니다.');
                            }
                            return result;
                        });
                    })
                    .then(function(result) {
                        checkWrap.classList.add('is-done');
                        var stockText = result.stock_qty === null || typeof result.stock_qty === 'undefined'
                            ? '검수재고 -'
                            : '검수재고 ' + Number(result.stock_qty).toLocaleString('ko-KR');
                        checkWrap.innerHTML = '<strong></strong><span></span>';
                        checkWrap.querySelector('strong').textContent = stockText;
                        checkWrap.querySelector('span').textContent = (result.checked_at_text || '') + ' · ' + (result.checked_by_name || '');
                        var media = checkArticle.querySelector('.admobile-rack-product__media');
                        if (media && !media.querySelector('.admobile-rack-product__checked')) {
                            var checkedIcon = document.createElement('i');
                            checkedIcon.className = 'admobile-rack-product__checked';
                            checkedIcon.setAttribute('aria-label', '검수완료');
                            checkedIcon.innerHTML = '<b>✓</b><span>검수완료</span>';
                            media.appendChild(checkedIcon);
                            var cancelButton = document.createElement('button');
                            cancelButton.type = 'button';
                            cancelButton.className = 'admobile-rack-product__check-cancel';
                            cancelButton.setAttribute('data-inspection-cancel', '');
                            cancelButton.textContent = '검수완료 취소';
                            media.appendChild(cancelButton);
                        }
                        var activeNote = list.querySelector('.admobile-rack-inspection__active');
                        if (activeNote) {
                            activeNote.textContent = activeNote.textContent.replace(' · 검수 기록은 아직 없습니다.', '');
                        }
                        inspectionCount(1);
                    })
                    .catch(function(error) {
                        checkMessage.textContent = error.message || '검수완료 처리에 실패했습니다.';
                        checkButton.disabled = false;
                    });
                return;
            }

            var button = event.target.closest('[data-stock-action]');
            if (!button || !list.contains(button)) {
                return;
            }

            var article = button.closest('.admobile-rack-product');
            var qtyInput = article.querySelector('.admobile-rack-product__quick input');
            var message = article.querySelector('.admobile-rack-product__quick-message');
            var qty = String(qtyInput.value || '').trim();
            message.textContent = '';
            if (!/^[1-9]\d*$/.test(qty)) {
                message.textContent = '수량은 1 이상의 숫자로 입력해 주세요.';
                qtyInput.focus();
                return;
            }

            setBusy(article, true);
            fetch('/admobile/product/action', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({
                    action_mode: 'register_stock_change',
                    prd_idx: article.dataset.prdIdx || '0',
                    ps_idx: article.dataset.psIdx || '0',
                    stock_day: todayText(),
                    stock_mode: button.dataset.stockAction,
                    stock_kind: '조정',
                    stock_qty: qty,
                    stock_memo: ''
                })
            })
                .then(function(response) {
                    return response.json().then(function(result) {
                        if (!response.ok || !result.success) {
                            throw new Error((result && result.message) || '재고 변경에 실패했습니다.');
                        }
                        return result;
                    });
                })
                .then(function(result) {
                    var stock = Number(result.stock || 0);
                    article.dataset.stock = String(stock);
                    if (result.ps_idx) {
                        article.dataset.psIdx = String(result.ps_idx);
                    }
                    article.querySelector('.admobile-rack-product__stock-value').textContent = formatCount(stock);
                    article.classList.toggle('admobile-rack-product--empty', stock === 0);
                    var section = article.closest('.admobile-rack-products');
                    if (section) {
                        sortSection(section);
                    }
                })
                .catch(function(error) {
                    message.textContent = error.message || '재고 변경에 실패했습니다.';
                })
                .finally(function() {
                    setBusy(article, false);
                });
        });
    })();
</script>
