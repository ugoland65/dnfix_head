<style>
    .partition-header-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px;
        margin-bottom: 10px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
    }
    .partition-header-tabs .tab-item {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 8px;
        border: 1px solid transparent;
        background: transparent;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s ease;
    }
    .partition-header-tabs .tab-item:hover {
        color: #0f172a;
        background: #eef2ff;
    }
    .partition-header-tabs .tab-item.active {
        color: #1d4ed8;
        background: #ffffff;
        border-color: #c7d2fe;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
    }
    .partition-header-tabs .tab-item .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        height: 20px;
        margin-left: 6px;
        padding: 0 7px;
        border-radius: 999px;
        background: #e5e7eb;
        color: #374151;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
    }

    .table-wrap5 {
        height: calc(100% - 80px);
    }

    .action-wrap{
        padding: 0 0 0 20px;
    }
</style>
<div id="contents_head">
    <h1>월간할인관리</h1>
</div>

<div id="contents_body">
    <div id="contents_body_wrap">

        <div class="partition-wrap">
            <ul class="partition-body">

                <div class="partition-header-tabs">
                    <div class="tab-item active" data-tab="godo" data-filter="all">현재 고도몰 진열상품 <span class="tab-count"><?= number_format(count($godoGoods)) ?></span></div>
                    <div class="tab-item" data-tab="godo" data-filter="soldout">품절 처리해야할 상품 <span class="tab-count"><?= number_format((int)($soldOutProductCount ?? 0)) ?></span></div>
                    <div class="tab-item" data-tab="missing">월간할인 아닌 라벨있는 상품 <span class="tab-count"><?= number_format(count($missingMonthlyProducts)) ?></span></div>
                </div>

                <div class="table-wrap5" id="godo_monthly_discount_management_table" style="display:none;">
                    <div class="scroll-wrap">
                        <table class="table-st1">
                            <thead>
                                <tr class="list">
                                    <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                                    <th class="list-idx">상품번호</th>
                                    <th>판매상태</th>
                                    <th>이미지</th>
                                    <th>상품명</th>
                                    <th>원가</th>
                                    <th>정가</th>
                                    <th>판매가</th>
                                    <th>표기할인율</th>
                                    <th>재고량</th>
                                    <th>매칭상품</th>
                                    <th>할인해제</th>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach ($godoGoods as $godoGood) { ?>
                                    <?php
                                        $stockFl = strtolower(trim((string)($godoGood['stockFl'] ?? '')));
                                        $totalStock = (int)($godoGood['totalStock'] ?? 0);
                                        $isSoldOut = ($stockFl === 'y' && $totalStock === 0);
                                    ?>
                                    <tr style="<?= $isSoldOut ? 'background:#f3f4f6;' : '' ?>" data-soldout="<?= $isSoldOut ? '1' : '0' ?>">
                                        <td><input type="checkbox" name="check_idx[]" value="<?= $godoGood['goodsNo'] ?>"></td>
                                        <td><?= $godoGood['goodsNo'] ?></td>
                                        <td class="text-center">
                                            <?php if ($isSoldOut) { ?>
                                                <span style="color:#dc2626; font-weight:700;">품절</span>
                                            <?php } else { ?>
                                                판매중
                                            <?php } ?>
                                        </td>
                                        <td class="p-5">
                                            <p style="cursor:pointer;" ><img src="<?=$godoGood['thumbImageUrl']?>" style="height:70px; border:1px solid #eee !important;"></p>
                                        </td>
                                        <td><?= $godoGood['goodsNm'] ?></td>
                                        <td class="text-right"><?= number_format(($godoGood['costPrice'] ?? 0)) ?></td>
                                        <td class="text-right"><?= number_format(($godoGood['fixedPrice'] ?? 0)) ?></td>
                                        <td class="text-right"><b><?= number_format(($godoGood['goodsPrice'] ?? 0)) ?></b></td>
                                        <td class="text-right">
                                            <?php
                                                $fixedPrice = (float)($godoGood['fixedPrice'] ?? 0);
                                                $goodsPrice = (float)($godoGood['goodsPrice'] ?? 0);
                                                $discountRate = 0;
                                                if ($fixedPrice > 0 && $goodsPrice >= 0) {
                                                    $discountRate = (($fixedPrice - $goodsPrice) / $fixedPrice) * 100;
                                                    if ($discountRate < 0) {
                                                        $discountRate = 0;
                                                    }
                                                }
                                            ?>
                                            <?= number_format(round($discountRate), 0) ?>%
                                        </td>
                                        <td class="text-right">
                                            <p><?= $godoGood['stockFl'] == 'y' ? '' : '재고관리안함' ?></p>
                                            <?= number_format(($godoGood['totalStock'] ?? 0)) ?>
                                        </td>
                                        <td>
                                            <?php $productData = $godoGood['product_data'] ?? []; ?>
                                            <?php if (!empty($productData)) { ?>
                                                <?php if( $productData['is_sale_month'] ){ ?>
                                                    <label class="on_sale_label xs monthly">월간할인</label>
                                                <?php } ?>
                                                <div style="font-size:11px; color:#6b7280;" class="m-t-3">재고코드: <b><?= htmlspecialchars((string)($productData['ps_idx'] ?? ''), ENT_QUOTES, 'UTF-8') ?></b></div>
                                                <div style="margin-top:2px;">
                                                    <a href="javascript:onlyAD.prdView('<?= (int)($productData['CD_IDX'] ?? 0) ?>','info');">
                                                        <b><?= htmlspecialchars((string)($productData['CD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?></b>
                                                    </a>
                                                </div>
                                                <div style="font-size:11px; color:#6b7280; margin-top:2px;">
                                                    상품번호: #<?= (int)($productData['CD_IDX'] ?? 0) ?> / 재고: <?= number_format((int)($productData['ps_stock'] ?? 0)) ?>
                                                </div>
                                            <?php } else { ?>
                                                <span style="color:#9ca3af;">미매칭</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btn btnstyle1 btnstyle1-sm monthly-discount-release-btn"
                                                data-goods-no="<?= htmlspecialchars((string)($godoGood['goodsNo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                data-prd-idx="<?= (int)($productData['CD_IDX'] ?? 0) ?>"
                                                data-prd-stock-idx="<?= (int)($productData['ps_idx'] ?? 0) ?>"
                                                data-fixed-price="<?= (float)($godoGood['fixedPrice'] ?? 0) ?>"
                                                data-goods-price="<?= (float)($godoGood['goodsPrice'] ?? 0) ?>"
                                                >
                                                할인해제
                                            </button>
                                        </td>
                                    </tr>
                                <? } ?>
                            </tbody>
                        </table>

                    </div>
                </div>

                <div class="table-wrap5" id="missing_monthly_products_table">
                    <div class="scroll-wrap">
                        <table class="table-st1">
                            <thead>
                                <tr class="list">
                                    <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                                    <th class="list-idx">고유번호</th>
                                    <th>이미지</th>
                                    <th>재고코드</th>
                                    <th>분류</th>
                                    <th>상품명</th>
                                    <th>브랜드</th>
                                    <th>판매가</th>
                                    <th>원가</th>
                                    <th>시스템<br>재고량</th>
                                    <th>연결한<br>고도몰 상품번호</th>
                                    <th>고도몰<br>판매상태</th>
                                    <th>고도몰<br>판매가</th>
                                    <th>고도몰<br>원가</th>
                                    <th>고도몰<br>재고량</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($missingMonthlyProducts)) { ?>
                                    <?php foreach ($missingMonthlyProducts as $product) { ?>
                                        <?php
                                            $imgPath = '';
                                            if (($product['img_mode'] ?? '') === 'out') {
                                                $imgPath = (string)($product['CD_IMG'] ?? '');
                                            } else {
                                                $imgFile = trim((string)($product['CD_IMG'] ?? ''));
                                                if ($imgFile !== '') {
                                                    $imgPath = '/data/comparion/' . $imgFile;
                                                }
                                            }
                                        ?>
                                        <tr>
                                            <td><input type="checkbox" name="missing_check_idx[]" value="<?= (int)($product['CD_IDX'] ?? 0) ?>"></td>
                                            <td class="text-center"><?= (int)($product['CD_IDX'] ?? 0) ?></td>
                                            <td class="p-5">
                                                <?php if ($imgPath !== '') { ?>
                                                    <p onclick="onlyAD.prdView('<?= (int)($product['CD_IDX'] ?? 0) ?>','info');" style="cursor:pointer;">
                                                        <img src="<?= htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8') ?>" style="height:70px; border:1px solid #eee !important;">
                                                    </p>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center"><?= htmlspecialchars((string)($product['ps_idx'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="text-center"><?= htmlspecialchars((string)($product['prd_kind_name'] ?? '미지정'), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <label class="on_sale_label xs monthly">월간할인</label>
                                                <p onclick="onlyAD.prdView('<?= (int)($product['CD_IDX'] ?? 0) ?>','info');" style="cursor:pointer;">
                                                    <b><?= $product['CD_NAME'] ?? '' ?></b>
                                                </p>
                                                <p><?= htmlspecialchars((string)($product['godo_goods_data']['goodsNm'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                            </td>
                                            <td class="text-center"><?= htmlspecialchars((string)($product['brand_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="text-right">
                                                <?php
                                                    $ourSalePrice = (int)($product['cd_sale_price'] ?? 0);
                                                    $godoSalePrice = (int)($product['godo_goods_data']['goodsPrice'] ?? 0);
                                                    $ourSalePriceStyle = '';
                                                    if ($ourSalePrice < $godoSalePrice) {
                                                        $ourSalePriceStyle = 'background:#2563eb; color:#fff; border-radius:4px; padding:2px 6px;';
                                                    } elseif ($ourSalePrice > $godoSalePrice) {
                                                        $ourSalePriceStyle = 'background:#dc2626; color:#fff; border-radius:4px; padding:2px 6px;';
                                                    }
                                                ?>
                                                <b style="<?= $ourSalePriceStyle ?>"><?= number_format($ourSalePrice) ?></b>
                                            </td>
                                            <td class="text-right">
                                                <?php
                                                    $ourCostPrice = (int)($product['cd_cost_price'] ?? 0);
                                                    $godoCostPrice = (int)($product['godo_goods_data']['costPrice'] ?? 0);
                                                ?>
                                                <b><?= number_format($ourCostPrice) ?></b>
                                            </td>
                                            <td class="text-right"><?= number_format((int)($product['ps_stock'] ?? 0)) ?></td>
                                            <td class="text-center"><?= $product['cd_godo_code'] ?? '' ?></td>
                                            <td class="text-center">
                                                <?php
                                                    $godoStockFl = strtolower(trim((string)($product['godo_goods_data']['stockFl'] ?? '')));
                                                    $godoTotalStock = (int)($product['godo_goods_data']['totalStock'] ?? 0);
                                                    $isGodoSoldOut = ($godoStockFl === 'y' && $godoTotalStock === 0);
                                                ?>
                                                <?php if ($isGodoSoldOut) { ?>
                                                    <span style="color:#dc2626; font-weight:700;">품절</span>
                                                <?php } else { ?>
                                                    판매중
                                                <?php } ?>
                                            </td>
                                            <td class="text-right"><?= number_format((int)($product['godo_goods_data']['goodsPrice'] ?? 0)) ?></td>
                                            <td class="text-right">
                                                <?php
                                                    $godoCostPriceStyle = '';
                                                    if ($godoCostPrice < $ourCostPrice) {
                                                        $godoCostPriceStyle = 'background:#2563eb; color:#fff; border-radius:4px; padding:2px 6px;';
                                                    } elseif ($godoCostPrice > $ourCostPrice) {
                                                        $godoCostPriceStyle = 'background:#dc2626; color:#fff; border-radius:4px; padding:2px 6px;';
                                                    }
                                                ?>
                                                <b style="<?= $godoCostPriceStyle ?>"><?= number_format($godoCostPrice) ?></b>
                                            </td>
                                            <td class="text-right">
                                                <p><?= $product['godo_goods_data']['stockFl'] == 'y' ? '' : '재고관리안함' ?></p>
                                                <?= number_format((int)($product['godo_goods_data']['totalStock'] ?? 0)) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="9" class="text-center">월간할인이 아닌 라벨 상품이 없습니다.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </ul>
            <ul class="partition-right ">
                <div class="action-wrap">

                    <p>품절 처리해야할 상품 (<?= number_format((int)($soldOutProductCount ?? 0)) ?>개)</p>
                    <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm">품절상품 월간할인 제외 처리</button>

                </div>
            </ul>

        </div>

    </div>
</div>

<script>
    (function() {
        var tabItems = document.querySelectorAll('.partition-header-tabs .tab-item');
        var godoTable = document.getElementById('godo_monthly_discount_management_table');
        var missingTable = document.getElementById('missing_monthly_products_table');
        var rows = document.querySelectorAll('#godo_monthly_discount_management_table tbody tr[data-soldout]');
        if (!tabItems.length || !godoTable || !missingTable) {
            return;
        }

        function applyFilter(filterMode) {
            for (var i = 0; i < rows.length; i++) {
                var isSoldOut = rows[i].getAttribute('data-soldout') === '1';
                if (filterMode === 'soldout') {
                    rows[i].style.display = isSoldOut ? '' : 'none';
                } else {
                    rows[i].style.display = '';
                }
            }
        }

        function showTable(tabMode, filterMode) {
            if (tabMode === 'missing') {
                godoTable.style.display = 'none';
                missingTable.style.display = '';
                return;
            }
            missingTable.style.display = 'none';
            godoTable.style.display = '';
            applyFilter(filterMode || 'all');
        }

        for (var i = 0; i < tabItems.length; i++) {
            tabItems[i].addEventListener('click', function() {
                for (var j = 0; j < tabItems.length; j++) {
                    tabItems[j].classList.remove('active');
                }
                this.classList.add('active');
                showTable(this.getAttribute('data-tab') || 'godo', this.getAttribute('data-filter') || 'all');
            });
        }
        showTable('godo', 'all');

        $(document).on('click', '.monthly-discount-release-btn', function() {

            var goodsNo = String($(this).data('goods-no') || '').trim();
            var prdIdx = String($(this).data('prd-idx') || '').trim();
            var prdStockIdx = String($(this).data('prd-stock-idx') || '').trim();
            var fixedPrice = String($(this).data('fixed-price') || '').trim();
            var goodsPrice = String($(this).data('goods-price') || '').trim();

            if (!goodsNo) {
                alert('상품번호가 없습니다.');
                return;
            }
            if (!prdIdx) {
                alert('상품번호가 없습니다.');
                return;
            }
            if (!prdStockIdx) {
                alert('재고코드가 없습니다.');
                return;
            }
            if (!fixedPrice) {
                alert('정가가 없습니다.');
                return;
            }
            if (!goodsPrice) {
                alert('판매가가 없습니다.');
                return;
            }
            if (!confirm('할인해제시 월간할인 카테고리에서 제외시키고 정가를 판매가로 수정합니다! 해제 하시겠습니까?')) {
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true);

            ajaxRequest('/admin/product/action', {
                action_mode: 'prd_release_monthly_discount',
                action_source: 'monthly_discount_management',
                goods_no: goodsNo,
                prd_idx: prdIdx,
                prd_stock_idx: prdStockIdx,
                fixed_price: fixedPrice,
                goods_price: goodsPrice
            })
                .done(function(res) {
                    if (!(res && (res.success || res.status === 'success'))) {
                        alert(res && res.message ? res.message : '할인해제 처리에 실패했습니다.');
                        return;
                    }
                    if (typeof toast2 === 'function') {
                        toast2('success', '월간할인관리', '할인해제가 완료되었습니다.');
                    }
                    location.reload();
                })
                .fail(function(res) {
                    alert(res && res.message ? res.message : '할인해제 처리 중 오류가 발생했습니다.');
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
        });
    })();
</script>
