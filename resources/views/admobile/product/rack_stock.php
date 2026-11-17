<?php
$rackGroups = $rackGroups ?? [];
$productGroups = $productGroups ?? [];
$group = (string)($group ?? '');
$searchValue = (string)($searchValue ?? '');
$showProducts = $group !== '' || $searchValue !== '';

$currentParams = [];
if ($group !== '') {
    $currentParams['group'] = $group;
}
if ($searchValue !== '') {
    $currentParams['search_value'] = $searchValue;
}
$currentQuery = http_build_query($currentParams);
$currentUrl = '/admobile/product/rack' . ($currentQuery !== '' ? '?' . $currentQuery : '');
$backUrl = $showProducts ? '/admobile/product/rack' : '/admobile/main';
$pageHeading = $group !== '' ? $group : '랙별 상품 재고관리';
?>
<section class="admobile-rack-stock">
    <div class="admobile-rack-heading">
        <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" aria-label="이전으로 돌아가기">‹</a>
        <div>
            <h2><?= htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8') ?></h2>
            <?php if ($group !== '') { ?>
                <p><?= number_format((int)($rackGroups[$group]['product_count'] ?? 0)) ?>개 상품</p>
            <?php } else { ?>
                <p>랙 묶음 <?= number_format(count($rackGroups)) ?>개</p>
            <?php } ?>
        </div>
    </div>

    <form class="admobile-rack-search" method="get" action="/admobile/product/rack">
        <?php if ($group !== '') { ?>
            <input type="hidden" name="group" value="<?= htmlspecialchars($group, ENT_QUOTES, 'UTF-8') ?>">
        <?php } ?>
        <label for="rack-stock-search">바코드, 상품명, 랙코드 검색</label>
        <div class="admobile-rack-search__input">
            <input id="rack-stock-search" name="search_value" type="search" value="<?= htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8') ?>" placeholder="바코드, 상품명, 랙코드">
            <button type="submit">검색</button>
        </div>
    </form>

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
                    ?>
                    <article
                        class="admobile-rack-product<?= $isEmptyStock ? ' admobile-rack-product--empty' : '' ?>"
                        data-prd-idx="<?= (int)$product['prd_idx'] ?>"
                        data-ps-idx="<?= (int)($product['ps_idx'] ?? 0) ?>"
                        data-stock="<?= (int)$product['stock'] ?>"
                        data-rack-code="<?= htmlspecialchars((string)($product['sort_rack'] ?? $product['rack_code']), ENT_QUOTES, 'UTF-8') ?>"
                    >
                        <div class="admobile-rack-product__thumb">
                            <?php if ($image !== '') { ?>
                                <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="">
                            <?php } else { ?>
                                <span>없음</span>
                            <?php } ?>
                        </div>
                        <div class="admobile-rack-product__body">
                            <?php if ($detailUrl !== '') { ?>
                                <a class="admobile-rack-product__name" href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?></a>
                            <?php } else { ?>
                                <p class="admobile-rack-product__name"><?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php } ?>
                            <div class="admobile-rack-product__main">
                                <p class="admobile-rack-product__rack"><?= htmlspecialchars((string)$product['rack_code'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="admobile-rack-product__stock">현재고 <strong class="admobile-rack-product__stock-value"><?= number_format((int)$product['stock']) ?></strong></p>
                            </div>
                            <p class="admobile-rack-product__barcode">
                                <span>바코드</span>
                                <?= $barcode !== '' ? htmlspecialchars($barcode, ENT_QUOTES, 'UTF-8') : '-' ?>
                            </p>
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

<style>
    .admobile-rack-heading { display: flex; gap: 12px; align-items: center; margin-bottom: 12px; }
    .admobile-rack-heading a { display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: #fff; color: #172033; font-size: 28px; line-height: 1; text-decoration: none; }
    .admobile-rack-heading h2 { margin: 0; font-size: 20px; }
    .admobile-rack-heading p { margin: 2px 0 0; color: #667085; font-size: 13px; }
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
    .admobile-rack-product__thumb { display: flex; flex: 0 0 64px; align-items: center; justify-content: center; width: 64px; height: 64px; overflow: hidden; border: 1px solid #edf0f4; border-radius: 8px; background: #f8fafc; color: #98a2b3; font-size: 11px; }
    .admobile-rack-product__thumb img { width: 100%; height: 100%; object-fit: cover; }
    .admobile-rack-product__body { flex: 1; min-width: 0; }
    .admobile-rack-product__name { display: block; margin: 0 0 8px; color: #172033; font-size: 15px; font-weight: 700; line-height: 1.4; text-decoration: none; word-break: break-word; }
    .admobile-rack-product__main { display: flex; gap: 12px; align-items: baseline; justify-content: space-between; }
    .admobile-rack-product__rack, .admobile-rack-product__stock, .admobile-rack-product__barcode { margin: 0; }
    .admobile-rack-product__rack { color: #172033; font-size: 16px; font-weight: 800; letter-spacing: -.02em; }
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

        list.addEventListener('click', function(event) {
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
