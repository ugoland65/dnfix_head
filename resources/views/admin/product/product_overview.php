<div id="contents_head">
    <h1>상품관리 종합현황</h1>
</div>

<div id="contents">
    <style>
        .product-overview-grid { display:flex; gap:10px; align-items:flex-start; }
        .product-overview-panel { flex:1 1 0; min-width:0; border:1px solid #e1e5eb; background:#fff; }
        .product-overview-panel-head { display:flex; justify-content:space-between; align-items:center; gap:6px; padding:10px 12px; border-bottom:1px solid #e1e5eb; }
        .product-overview-panel-head h2 { margin:0; font-size:14px; }
        .product-overview-period { margin:4px 0 0; color:#6b7280; font-size:12px; }
        .product-overview-more { flex:0 0 auto; padding:5px 8px; border:1px solid #cbd5e1; border-radius:4px; color:#334155; font-size:12px; text-decoration:none; }
        .product-overview-list { margin:0; padding:0; list-style:none; }
        .product-overview-item { display:flex; gap:8px; align-items:center; padding:8px 10px; border-bottom:1px solid #f0f2f5; }
        .product-overview-item:last-child { border-bottom:0; }
        .product-overview-image { flex:0 0 44px; }
        .product-overview-image img { display:block; width:44px; height:44px; border:1px solid #e5e7eb; object-fit:cover; }
        .product-overview-idx { flex:0 0 auto; color:#94a3b8; font-size:12px; }
        .product-overview-info { flex:1 1 auto; min-width:0; }
        .product-overview-brand { margin-bottom:2px; color:#64748b; font-size:12px; }
        .product-overview-name { overflow:hidden; color:#1f2937; font-size:12px; text-overflow:ellipsis; white-space:nowrap; }
        .product-overview-price-change { flex:0 0 80px; color:#334155; font-size:12px; text-align:right; }
        .product-overview-price-after { display:block; margin-bottom:2px; color:#1f2937; font-size:12px; font-weight:700; }
        .product-overview-price-before { display:block; margin-bottom:2px; color:#334155; font-size:12px; font-weight:600; }
        .product-overview-price-decrease { color:#dc2626; }
        .product-overview-price-increase { color:#2563eb; }
        .product-overview-editor { display:block; margin-top:2px; color:#64748b; font-size:12px; }
        .product-overview-price { flex:0 0 auto; color:#334155; font-size:12px; font-weight:600; }
        .product-overview-date { flex:0 0 64px; color:#64748b; font-size:12px; text-align:right; }
        .product-overview-restore { margin-top:4px; padding:3px 5px; border:1px solid #94a3b8; border-radius:3px; background:#fff; color:#334155; font-size:12px; line-height:1.2; cursor:pointer; }
        .product-overview-restore:disabled { cursor:default; opacity:.6; }
        .product-overview-empty { padding:24px 12px; color:#94a3b8; font-size:12px; text-align:center; }
        @media (max-width: 1000px) { .product-overview-grid { flex-direction:column; } .product-overview-panel { width:100%; } }
    </style>

    <?php
        $salePriceChangeProducts = $recentSalePriceChanges['data'] ?? [];
        $soldoutProducts = $recentSoldoutProducts['data'] ?? [];
        $deletedProducts = $recentDeletedProducts['data'] ?? [];
        $salePriceChangeTotal = (int)($recentSalePriceChanges['total'] ?? 0);
        $soldoutTotal = (int)($recentSoldoutProducts['total'] ?? 0);
        $deletedTotal = (int)($recentDeletedProducts['total'] ?? 0);
        $weekStartText = date('Y.m.d', strtotime($weekStart ?? date('Y-m-d 00:00:00', strtotime('-6 days'))));
        $monthStartText = date('Y.m.d', strtotime($monthStart ?? date('Y-m-d 00:00:00', strtotime('-1 month'))));
        $todayText = date('Y.m.d');
    ?>

    <div class="product-overview-grid">
        <section class="product-overview-panel">
            <div class="product-overview-panel-head">
                <div>
                    <h2>최근 판매가 변경 상품 (<?= number_format($salePriceChangeTotal) ?>)</h2>
                    <p class="product-overview-period"><?= $weekStartText ?> ~ <?= $todayText ?></p>
                </div>
                <a class="product-overview-more" href="/admin/product/product_stock?in_stock=all&amp;sort_mode=sale_price_changed_at">더보기</a>
            </div>
            <ul class="product-overview-list">
                <?php foreach ($salePriceChangeProducts as $product) { ?>
                    <?php
                        $productImagePath = '';
                        if (($product['img_mode'] ?? '') === 'out') {
                            $productImagePath = (string)($product['CD_IMG'] ?? '');
                        } elseif (!empty($product['CD_IMG'])) {
                            $productImagePath = '/data/comparion/' . $product['CD_IMG'];
                        }
                        $salePriceChangeMeta = json_decode((string)($product['cd_sale_price_change_meta'] ?? '[]'), true);
                        $latestSalePriceChange = is_array($salePriceChangeMeta) ? end($salePriceChangeMeta) : null;
                        $beforeSalePrice = (int)($latestSalePriceChange['before_sale_price'] ?? 0);
                        $afterSalePrice = (int)($latestSalePriceChange['after_sale_price'] ?? 0);
                        $salePriceDifference = $afterSalePrice - $beforeSalePrice;
                        $changedByName = trim((string)($latestSalePriceChange['changed_by_name'] ?? ''));
                        $brandName = trim((string)($product['brand_name'] ?? ''));
                        $brand2Name = trim((string)($product['brand2_name'] ?? ''));
                    ?>
                    <li class="product-overview-item">
                        <span class="product-overview-idx">#<?= (int)($product['CD_IDX'] ?? 0) ?></span>
                        <a class="product-overview-image" href="#" onclick="onlyAD.prdView(<?= (int)($product['CD_IDX'] ?? 0) ?>, 'info'); return false;">
                            <?php if ($productImagePath !== '') { ?>
                                <img src="<?= htmlspecialchars($productImagePath, ENT_QUOTES, 'UTF-8') ?>" alt="">
                            <?php } ?>
                        </a>
                        <div class="product-overview-info">
                            <?php if ($brandName !== '' || $brand2Name !== '') { ?>
                                <div class="product-overview-brand"><?= htmlspecialchars($brandName . ($brandName !== '' && $brand2Name !== '' ? ' / ' : '') . $brand2Name, ENT_QUOTES, 'UTF-8') ?></div>
                            <?php } ?>
                            <div class="product-overview-name"><?= htmlspecialchars((string)($product['CD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <div class="product-overview-price-change">
                            <span class="product-overview-price-before"><?= number_format($beforeSalePrice) ?></span>
                            <?php if ($salePriceDifference < 0) { ?>
                                <span class="product-overview-price-decrease">▼ <?= number_format(abs($salePriceDifference)) ?></span>
                            <?php } elseif ($salePriceDifference > 0) { ?>
                                <span class="product-overview-price-increase">▲ <?= number_format($salePriceDifference) ?></span>
                            <?php } ?>
                            <span class="product-overview-price-after">최종 <?= number_format((int)($product['cd_sale_price'] ?? $afterSalePrice)) ?>원</span>
                        </div>
                        <span class="product-overview-date">
                            <?= date('y.m.d', strtotime((string)($product['cd_sale_price_changed_at'] ?? ''))) ?>
                            <?php if ($changedByName !== '') { ?>
                                <br><span class="product-overview-editor">수정: <?= htmlspecialchars($changedByName, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php } ?>
                        </span>
                    </li>
                <?php } ?>
                <?php if (empty($salePriceChangeProducts)) { ?>
                    <li class="product-overview-empty">최근 일주일간 판매가 변경 상품이 없습니다.</li>
                <?php } ?>
            </ul>
        </section>

        <section class="product-overview-panel">
            <div class="product-overview-panel-head">
                <div>
                    <h2>최근 품절 상품 (<?= number_format($soldoutTotal) ?>)</h2>
                    <p class="product-overview-period"><?= $weekStartText ?> ~ <?= $todayText ?></p>
                </div>
                <a class="product-overview-more" href="/admin/product/product_stock?in_stock=all&amp;sort_mode=soldout">더보기</a>
            </div>
            <ul class="product-overview-list">
                <?php foreach ($soldoutProducts as $product) { ?>
                    <?php
                        $productImagePath = '';
                        if (($product['img_mode'] ?? '') === 'out') {
                            $productImagePath = (string)($product['CD_IMG'] ?? '');
                        } elseif (!empty($product['CD_IMG'])) {
                            $productImagePath = '/data/comparion/' . $product['CD_IMG'];
                        }
                        $brandName = trim((string)($product['brand_name'] ?? ''));
                        $brand2Name = trim((string)($product['brand2_name'] ?? ''));
                    ?>
                    <li class="product-overview-item">
                        <span class="product-overview-idx">#<?= (int)($product['CD_IDX'] ?? 0) ?></span>
                        <a class="product-overview-image" href="#" onclick="onlyAD.prdView(<?= (int)($product['CD_IDX'] ?? 0) ?>, 'info'); return false;">
                            <?php if ($productImagePath !== '') { ?>
                                <img src="<?= htmlspecialchars($productImagePath, ENT_QUOTES, 'UTF-8') ?>" alt="">
                            <?php } ?>
                        </a>
                        <div class="product-overview-info">
                            <?php if ($brandName !== '' || $brand2Name !== '') { ?>
                                <div class="product-overview-brand"><?= htmlspecialchars($brandName . ($brandName !== '' && $brand2Name !== '' ? ' / ' : '') . $brand2Name, ENT_QUOTES, 'UTF-8') ?></div>
                            <?php } ?>
                            <div class="product-overview-name"><?= htmlspecialchars((string)($product['CD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <span class="product-overview-price">재고 <?= number_format((int)($product['ps_stock'] ?? 0)) ?></span>
                        <span class="product-overview-date"><?= date('y.m.d', strtotime((string)($product['ps_soldout_date'] ?? ''))) ?></span>
                    </li>
                <?php } ?>
                <?php if (empty($soldoutProducts)) { ?>
                    <li class="product-overview-empty">최근 일주일간 품절 상품이 없습니다.</li>
                <?php } ?>
            </ul>
        </section>

        <section class="product-overview-panel">
            <div class="product-overview-panel-head">
                <div>
                    <h2>최근 삭제 상품 (<?= number_format($deletedTotal) ?>)</h2>
                    <p class="product-overview-period"><?= $monthStartText ?> ~ <?= $todayText ?></p>
                </div>
            </div>
            <ul class="product-overview-list">
                <?php foreach ($deletedProducts as $product) { ?>
                    <?php
                        $productImagePath = '';
                        if (($product['img_mode'] ?? '') === 'out') {
                            $productImagePath = (string)($product['CD_IMG'] ?? '');
                        } elseif (!empty($product['CD_IMG'])) {
                            $productImagePath = '/data/comparion/' . $product['CD_IMG'];
                        }
                        $brandName = trim((string)($product['brand_name'] ?? ''));
                        $brand2Name = trim((string)($product['brand_name2'] ?? ''));
                        $deletedAdminName = trim((string)($product['CD_DELETED_ADMIN_NAME'] ?? ''));
                        $deletedAt = trim((string)($product['CD_DELETED_AT'] ?? ''));
                    ?>
                    <li class="product-overview-item">
                        <span class="product-overview-idx">#<?= (int)($product['CD_IDX'] ?? 0) ?></span>
                        <a class="product-overview-image" href="#" onclick="onlyAD.prdView(<?= (int)($product['CD_IDX'] ?? 0) ?>, 'info'); return false;">
                            <?php if ($productImagePath !== '') { ?>
                                <img src="<?= htmlspecialchars($productImagePath, ENT_QUOTES, 'UTF-8') ?>" alt="">
                            <?php } ?>
                        </a>
                        <div class="product-overview-info">
                            <?php if ($brandName !== '' || $brand2Name !== '') { ?>
                                <div class="product-overview-brand"><?= htmlspecialchars($brandName . ($brandName !== '' && $brand2Name !== '' ? ' / ' : '') . $brand2Name, ENT_QUOTES, 'UTF-8') ?></div>
                            <?php } ?>
                            <div class="product-overview-name"><?= htmlspecialchars((string)($product['CD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                            <?php if ($deletedAdminName !== '') { ?>
                                <div class="product-overview-editor">삭제: <?= htmlspecialchars($deletedAdminName, ENT_QUOTES, 'UTF-8') ?></div>
                            <?php } ?>
                        </div>
                        <span class="product-overview-date">
                            <?= $deletedAt !== '' ? date('y.m.d', strtotime($deletedAt)) : '-' ?><br>
                            <button type="button" class="product-overview-restore" data-prd-idx="<?= (int)($product['CD_IDX'] ?? 0) ?>">상품복원</button>
                        </span>
                    </li>
                <?php } ?>
                <?php if (empty($deletedProducts)) { ?>
                    <li class="product-overview-empty">최근 한달간 삭제된 상품이 없습니다.</li>
                <?php } ?>
            </ul>
        </section>
    </div>
</div>

<script>
$(document).on('click', '.product-overview-restore', function () {
    var $button = $(this);
    var prdIdx = Number($button.data('prd-idx') || 0);
    if (prdIdx <= 0) {
        alert('상품 정보가 올바르지 않습니다.');
        return;
    }
    if (!confirm('삭제된 상품을 복원하시겠습니까?')) {
        return;
    }

    $button.prop('disabled', true);
    $.ajax({
        url: '/admin/product/action',
        type: 'POST',
        dataType: 'json',
        data: {
            action_mode: 'restore_deleted_product',
            prd_idx: prdIdx
        },
        success: function (res) {
            if (res && res.success) {
                alert(res.message || '상품이 복원되었습니다.');
                location.reload();
                return;
            }
            $button.prop('disabled', false);
            alert((res && (res.message || res.msg)) ? (res.message || res.msg) : '상품 복원에 실패했습니다.');
        },
        error: function (xhr) {
            $button.prop('disabled', false);
            var message = xhr && xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.msg);
            alert(message || '상품 복원 요청 중 오류가 발생했습니다.');
        }
    });
});
</script>
