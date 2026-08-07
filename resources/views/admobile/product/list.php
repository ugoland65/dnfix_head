<?php
$productList = $productList ?? [];
$saleStatusOptions = $saleStatusOptions ?? [];
$totalCount = (int)($totalCount ?? 0);
$searchValue = (string)($searchValue ?? '');
$saleStatus = (string)($saleStatus ?? '');
$inStock = (string)($inStock ?? 'all');
?>
<section class="admobile-product-list">
    <div class="admobile-product-heading">
        <a href="/admobile/main" aria-label="메인으로 돌아가기">‹</a>
        <div>
            <h2>상품관리</h2>
            <p>상품 DB <strong><?= number_format($totalCount) ?></strong>개</p>
        </div>
    </div>

    <div id="product-search-sentinel" class="admobile-product-search-sentinel" aria-hidden="true"></div>
    <form id="product-search" class="admobile-product-search" method="get" action="/admobile/product/list">
        <label for="product-search-value">상품명 또는 바코드 검색</label>
        <div class="admobile-product-search__input">
            <input id="product-search-value" name="search_value" type="search" value="<?= htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8') ?>" placeholder="상품명, 바코드, 상품코드 입력">
            <button type="submit">검색</button>
        </div>
        <div class="admobile-product-search__filters">
            <select name="in_stock" aria-label="재고 상태">
                <option value="all" <?= $inStock === 'all' ? 'selected' : '' ?>>전체 재고</option>
                <option value="have" <?= $inStock === 'have' ? 'selected' : '' ?>>재고 보유</option>
                <option value="no" <?= $inStock === 'no' ? 'selected' : '' ?>>재고 없음</option>
            </select>
            <select name="s_sale_status" aria-label="판매 상태">
                <option value="">전체 상태</option>
                <?php foreach ($saleStatusOptions as $key => $option) { ?>
                    <?php
                    $value = is_array($option) ? (string)($option['value'] ?? '') : (string)$key;
                    $label = is_array($option) ? (string)($option['label'] ?? $value) : (string)$option;
                    if ($value === '') {
                        continue;
                    }
                    ?>
                    <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" <?= $saleStatus === $value ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                <?php } ?>
            </select>
        </div>
    </form>

    <div class="admobile-product-results">
        <?php if (empty($productList)) { ?>
            <p class="admobile-product-empty">검색 조건에 맞는 상품이 없습니다.</p>
        <?php } ?>

        <?php foreach ($productList as $product) { ?>
            <?php
            $image = trim((string)($product['CD_IMG'] ?? ''));
            if ($image !== '' && ($product['img_mode'] ?? '') !== 'out' && strpos($image, '/') !== 0) {
                $image = '/data/comparion/' . $image;
            }
            $stockQty = $product['ps_stock'] ?? null;
            $saleStatusText = trim((string)($product['sale_status'] ?? ''));
            ?>
            <article class="admobile-product-card">
                <?php if ($image !== '') { ?>
                    <button
                        type="button"
                        class="admobile-product-card__image admobile-product-card__image-button"
                        data-image="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
                        data-name="<?= htmlspecialchars((string)($product['CD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        aria-label="상품 이미지 크게 보기"
                    >
                        <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="">
                    </button>
                <?php } else { ?>
                    <div class="admobile-product-card__image">
                        <span>이미지 없음</span>
                    </div>
                <?php } ?>
                <div class="admobile-product-card__content">
                    <div class="admobile-product-card__topline">
                        <?php if ($saleStatusText !== '') { ?>
                            <span class="admobile-product-status"><?= htmlspecialchars($saleStatusText, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php } ?>
                        <?php if (!empty($product['is_discontinued'])) { ?>
                            <span class="admobile-product-status admobile-product-status--muted">단종</span>
                        <?php } ?>
                    </div>
                    <?php if (!empty($product['brand_name'])) { ?>
                        <p class="admobile-product-card__brand"><?= htmlspecialchars((string)$product['brand_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php } ?>
                    <h3><?= htmlspecialchars((string)($product['CD_NAME'] ?? '상품명 없음'), ENT_QUOTES, 'UTF-8') ?></h3>
                    <dl>
                        <div>
                            <dt>바코드</dt>
                            <dd><?= htmlspecialchars((string)($product['barcode'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div>
                            <dt>상품코드</dt>
                            <dd><?= htmlspecialchars((string)($product['cd_godo_code'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div>
                            <dt>랙</dt>
                            <dd><?= htmlspecialchars((string)($product['ps_rack_code'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                    </dl>
                    <p class="admobile-product-card__stock">현재고 <strong><?= $stockQty === null ? '-' : number_format((int)$stockQty) ?></strong>개</p>
                    <a class="admobile-product-card__detail-link" href="/admobile/product/detail?prd_idx=<?= (int)($product['CD_IDX'] ?? 0) ?>">상품정보 보기</a>
                </div>
            </article>
        <?php } ?>
    </div>

    <?php if (!empty($paginationHtml)) { ?>
        <div class="admobile-product-pagination"><?= $paginationHtml ?></div>
    <?php } ?>
</section>

<div id="admobile-product-list-image-modal" class="admobile-product-list-image-modal" hidden>
    <div class="admobile-product-list-image-modal__backdrop" data-action="close"></div>
    <section class="admobile-product-list-image-modal__content" role="dialog" aria-modal="true" aria-labelledby="admobile-product-list-image-title">
        <button type="button" class="admobile-product-list-image-modal__close" data-action="close" aria-label="이미지 닫기">닫기</button>
        <img id="admobile-product-list-image" src="" alt="">
        <p id="admobile-product-list-image-title"></p>
    </section>
</div>

<style>
    .admobile-product-heading { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
    .admobile-product-heading > a { color: #344054; font-size: 30px; line-height: 24px; text-decoration: none; }
    .admobile-product-heading h2 { margin: 0; font-size: 18px; }
    .admobile-product-heading p { margin: 4px 0 0; color: #667085; font-size: 13px; }
    .admobile-product-heading strong { color: #2450a6; }
    .admobile-product-search-sentinel { height: 1px; margin-top: -1px; }
    .admobile-product-search { position: sticky; z-index: 40; top: 0; margin: 0 -16px; padding: 13px 16px; border: 1px solid #cbd6ee; border-radius: 10px; background: #f7f9ff; transition: padding .18s ease, border-radius .18s ease, background .18s ease, box-shadow .18s ease; }
    .admobile-product-search > label { display: block; margin-bottom: 7px; color: #2c4d92; font-size: 13px; font-weight: 700; }
    .admobile-product-search__input { display: flex; overflow: hidden; border: 1px solid #aab9d6; border-radius: 7px; background: #fff; }
    .admobile-product-search__input input { width: 100%; min-width: 0; padding: 11px 10px; border: 0; color: #172033; font: inherit; font-size: 15px; outline: 0; }
    .admobile-product-search__input button { width: 58px; border: 0; border-left: 1px solid #e2e8f3; background: #3056a8; color: #fff; font: inherit; font-size: 13px; font-weight: 700; }
    .admobile-product-search__filters { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 7px; margin-top: 9px; }
    .admobile-product-search__filters select { width: 100%; min-width: 0; padding: 9px; border: 1px solid #cfd6e1; border-radius: 7px; background: #fff; color: #475467; font: inherit; font-size: 12px; }
    .admobile-product-search.is-stuck { padding-top: calc(8px + env(safe-area-inset-top)); padding-bottom: 8px; border-width: 0 0 1px; border-radius: 0; background: #172b4d; box-shadow: 0 4px 12px rgba(16, 24, 40, .18); }
    .admobile-product-search.is-stuck > label { color: #fff; font-size: 11px; }
    .admobile-product-search.is-stuck .admobile-product-search__filters { margin-top: 6px; }
    .admobile-product-search.is-stuck .admobile-product-search__filters select { padding-top: 7px; padding-bottom: 7px; }
    .admobile-product-results { display: grid; gap: 9px; margin-top: 12px; }
    .admobile-product-card { display: flex; gap: 12px; padding: 12px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
    .admobile-product-card__image { display: flex; flex: 0 0 82px; align-items: center; justify-content: center; width: 82px; height: 82px; overflow: hidden; border: 1px solid #edf0f4; border-radius: 8px; background: #f8fafc; color: #98a2b3; font-size: 10px; text-align: center; }
    .admobile-product-card__image img { width: 100%; height: 100%; object-fit: cover; }
    .admobile-product-card__image-button { padding: 0; cursor: zoom-in; }
    .admobile-product-card__content { min-width: 0; flex: 1; }
    .admobile-product-card__topline { display: flex; flex-wrap: wrap; gap: 4px; min-height: 2px; margin-bottom: 4px; }
    .admobile-product-status { padding: 2px 6px; border-radius: 999px; background: #e4ebfb; color: #2c4d92; font-size: 10px; font-weight: 700; }
    .admobile-product-status--muted { background: #eef1f5; color: #667085; }
    .admobile-product-card__brand { overflow: hidden; margin: 0 0 3px; color: #667085; font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-product-card h3 { display: -webkit-box; overflow: hidden; margin: 0; color: #172033; font-size: 14px; line-height: 1.4; line-clamp: 2; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
    .admobile-product-card dl { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 10px; margin: 8px 0 0; }
    .admobile-product-card dl > div:last-child { grid-column: 1 / -1; }
    .admobile-product-card dt { color: #98a2b3; font-size: 10px; }
    .admobile-product-card dd { overflow: hidden; margin: 1px 0 0; color: #475467; font-family: monospace; font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-product-card__stock { margin: 8px 0 0; color: #667085; font-size: 12px; }
    .admobile-product-card__stock strong { margin-left: 3px; color: #2450a6; font-size: 15px; }
    .admobile-product-card__detail-link { display: block; margin-top: 8px; padding: 8px; border: 1px solid #3056a8; border-radius: 6px; background: #3056a8; color: #fff; font-size: 12px; font-weight: 700; text-align: center; text-decoration: none; }
    .admobile-product-empty { margin: 0; padding: 32px 16px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; color: #667085; font-size: 14px; text-align: center; }
    .admobile-product-pagination { margin-top: 16px; overflow-x: auto; }
    .admobile-product-pagination .pagination { display: flex; justify-content: center; min-width: max-content; margin: 0; padding: 0; }
    .admobile-product-pagination .pagination ul { display: flex; gap: 4px; margin: 0; padding: 0; list-style: none; }
    .admobile-product-pagination .pagination a { display: block; min-width: 34px; padding: 9px 7px; border: 1px solid #d0d5dd; border-radius: 6px; color: #475467; font-size: 12px; text-align: center; text-decoration: none; }
    .admobile-product-pagination .pagination .active a { border-color: #3056a8; background: #3056a8; color: #fff; }
    .admobile-product-list-image-modal[hidden] { display: none; }
    .admobile-product-list-image-modal { position: fixed; z-index: 1200; inset: 0; display: flex; align-items: center; justify-content: center; padding: 18px; }
    .admobile-product-list-image-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .78); }
    .admobile-product-list-image-modal__content { position: relative; width: min(100%, 640px); max-height: 90vh; padding: 42px 12px 12px; border-radius: 12px; background: #fff; text-align: center; }
    .admobile-product-list-image-modal__content img { display: block; width: 100%; max-height: calc(90vh - 94px); object-fit: contain; }
    .admobile-product-list-image-modal__content p { margin: 9px 0 0; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-product-list-image-modal__close { position: absolute; top: 8px; right: 8px; padding: 7px 10px; border: 0; border-radius: 6px; background: #344054; color: #fff; font-size: 12px; font-weight: 700; }
</style>

<script>
    (function() {
        var search = document.getElementById('product-search');
        var sentinel = document.getElementById('product-search-sentinel');
        var modal = document.getElementById('admobile-product-list-image-modal');
        var image = document.getElementById('admobile-product-list-image');
        var title = document.getElementById('admobile-product-list-image-title');

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                search.classList.toggle('is-stuck', !entries[0].isIntersecting);
            }, { threshold: 0 }).observe(sentinel);
        }

        document.querySelectorAll('.admobile-product-card__image-button').forEach(function(button) {
            button.addEventListener('click', function() {
                image.src = button.dataset.image || '';
                image.alt = button.dataset.name || '상품 이미지';
                title.textContent = button.dataset.name || '';
                modal.hidden = false;
            });
        });

        modal.addEventListener('click', function(event) {
            if (event.target.closest('[data-action="close"]')) {
                modal.hidden = true;
                image.src = '';
            }
        });
    })();
</script>
