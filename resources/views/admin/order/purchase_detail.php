<style>
    .purchase-goods-modal { display:none; position:fixed; inset:0; z-index:10020; background:rgba(0, 0, 0, .45); padding:40px 20px; box-sizing:border-box; }
    .purchase-goods-modal.is-open { display:flex; align-items:center; justify-content:center; }
    .purchase-goods-modal-panel { width:min(1100px, 100%); max-height:calc(100vh - 80px); display:flex; flex-direction:column; background:#fff; border-radius:8px; box-shadow:0 12px 40px rgba(0, 0, 0, .25); overflow:hidden; }
    .purchase-goods-modal-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #ddd; }
    .purchase-goods-modal-head h3 { margin:0; }
    .purchase-goods-modal-head .purchase-goods-modal-close { border:0; background:transparent; font-size:26px; line-height:1; cursor:pointer; }
    .purchase-goods-modal-body { min-height:260px; padding:20px; overflow:auto; }
    .purchase-goods-search { display:flex; gap:8px; margin-bottom:15px; }
    .purchase-goods-search input { flex:1; min-width:0; height:36px; padding:0 10px; border:1px solid #bbb; }
    .purchase-goods-result-message { margin:10px 0; color:#555; }
    .purchase-goods-result table { width:100%; border-collapse:collapse; }
    .purchase-goods-result th, .purchase-goods-result td { padding:8px; border:1px solid #ddd; vertical-align:middle; }
    .purchase-goods-result th { background:#f6f6f6; text-align:center; }
    .purchase-goods-result img { width:48px; height:48px; object-fit:cover; }
    .purchase-goods-modal-foot { display:flex; justify-content:space-between; align-items:center; padding:14px 20px; border-top:1px solid #ddd; }
    .purchase-goods-empty { padding:45px 10px; text-align:center; color:#777; }
    .purchase-amount-card { margin-top:18px; padding:18px; border:1px solid #d8dde5; border-radius:8px; background:#fff; }
    .purchase-amount-card h3 { margin:0 0 14px; }
    .purchase-amount-row { display:flex; align-items:center; gap:10px; margin-top:8px; }
    .purchase-amount-label { width:120px; flex:0 0 120px; font-weight:700; }
    .purchase-amount-input { width:180px; height:34px; padding:0 8px; text-align:right; box-sizing:border-box; }
    .purchase-additional-cost-row { display:flex; align-items:center; gap:8px; margin-top:8px; }
    .purchase-additional-cost-reason { width:320px; height:34px; padding:0 8px; box-sizing:border-box; }
    .purchase-additional-cost-amount { width:180px; height:34px; padding:0 8px; text-align:right; box-sizing:border-box; }
    .purchase-final-amount { font-size:20px; color:#d92d20; }
    .purchase-amount-actions { display:flex; justify-content:flex-end; gap:8px; margin:0 auto;}
</style>

<div id="contents_head">
    <h1>구매대행 발주서 상세</h1>
    <h3>발주 상품 상세 목록</h3>
    <div class="right">
        <button type="button" class="btnstyle1 btnstyle1-sm" onclick="location.href='/admin/order/purchase/list'">리스트로</button>
        <button type="button" id="purchase-goods-open-btn" class="btnstyle1 btnstyle1-sm btnstyle1-primary">발주상품 추가</button>
        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="location.href='/admin/order/godo_order_purchase/excel?purchase_order_idx=<?= (int)($purchaseOrder['idx'] ?? 0) ?>'">엑셀 다운로드</button>
        <button type="button" id="purchase-delete-btn" class="btnstyle1 btnstyle1-danger btnstyle1-sm" data-idx="<?= (int)($purchaseOrder['idx'] ?? 0) ?>">발주서 삭제</button>
    </div>
</div>

<div id="contents_body">
    <div id="contents_body_wrap">

        <table class="table-style border01 width-full">
            <tr>
                <th style="width:140px;">발주서 번호</th>
                <td><?= (int)($purchaseOrder['idx'] ?? 0) ?></td>
                <th style="width:140px;">PO CODE</th>
                <td><?= htmlspecialchars((string)($purchaseOrder['po_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th>발주서명</th>
                <td><?= htmlspecialchars((string)($purchaseOrder['order_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <th>공급사</th>
                <td><?= htmlspecialchars((string)($purchaseOrder['supplier_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th>주문수</th>
                <td>
                    <?= number_format((int)($summary['order_count'] ?? 0)) ?> 건
                    총수량 / 총금액 : <?= number_format((int)($summary['total_quantity'] ?? 0)) ?> / <?= number_format((float)($summary['total_amount'] ?? 0), 2) ?> 원
                </td>
                <th>발주서 상태</th>
                <td><?= htmlspecialchars((string)($purchaseOrder['status_text'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        </table>

        <div id="list_new_wrap" style="height: calc(100% - 300px); margin-top: 10px;">
            <div class="table-wrap5" >
                <div class="scroll-wrap">
                    <table class="table-st1">
                        <thead>
                            <tr class="list">
                                <th class="list-idx">번호</th>
                                <th>주문번호</th>
                                <th>주문<br>상품번호</th>
                                <th>상품이미지</th>
                                <th>상품명</th>
                                <th>옵션</th>
                                <th>수량</th>
                                <th>판매단가</th>
                                <th>판매단가<br>합계</th>
                                <th>원가</th>
                                <th>원가<br>합계</th>
                                <th>수령자</th>
                                <th>연락처</th>
                                <th>주소</th>
                                <th>매칭상품</th>
                                <th>상품삭제</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($purchaseOrderItems)) { ?>
                                <?php
                                    foreach ($purchaseOrderItems as $item) {
                                        $product = $item['Product'] ?? [];

                                        $costPriceSum = 0;
                                        if (isset($product['cd_cost_price']) && is_numeric($product['cd_cost_price'])) {
                                            $costPriceSum = $product['cd_cost_price'] * $item['goods_count'];
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= (int)($item['idx'] ?? 0) ?></td>
                                        <td class="text-center"><a href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= $item['order_no'] ?>" target="_blank"><?= htmlspecialchars((string)($item['order_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></a></td>
                                        <td class="text-center"><?= htmlspecialchars((string)($item['order_goods_sno'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center">
                                            <?php $thumbImageUrl = trim((string)($item['thumb_image_url'] ?? '')); ?>
                                            <?php if ($thumbImageUrl !== '') { ?>
                                                <img src="<?= htmlspecialchars($thumbImageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="상품이미지" style="width:48px; height:48px; object-fit:cover;">
                                            <?php } else { ?>
                                                -
                                            <?php } ?>
                                        </td>
                                        <td><?= htmlspecialchars((string)($item['goods_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <?= $item['option_info_text'] ?? ''?>
                                        </td>
                                        <td class="text-right"><?= number_format((int)($item['goods_count'] ?? 0)) ?></td>
                                        <td class="text-right">
                                            <div><?= number_format((float)($item['goods_price'] ?? 0)) ?></div>
                                            <?php if ((float)($item['option_additional_price'] ?? 0) !== 0.0) { ?>
                                                <div>
                                                    <?= (float)$item['option_additional_price'] > 0 ? '+' : '-' ?>
                                                    <?= number_format(abs((float)$item['option_additional_price'])) ?>
                                                </div>
                                                <div><b>= <?= number_format((float)($item['goods_price_with_option'] ?? 0)) ?></b></div>
                                            <?php } ?>
                                        </td>

                                        <!-- 판매단가 합계 -->
                                        <td class="text-right"><?= number_format((float)($item['goods_total_price_with_option'] ?? 0)) ?></td>

                                        <!-- 원가 -->
                                        <td class="text-right"><?= number_format((float)($product['cd_cost_price']?? 0)) ?></td>
                                        <td class="text-right"><b><?= number_format((float)($costPriceSum ?? 0)) ?></b></td>

                                        <td class="text-center"><?= htmlspecialchars((string)($item['receiver_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= htmlspecialchars((string)($item['receiver_cell_phone'] ?? ($item['receiver_phone'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <?= htmlspecialchars((string)($item['receiver_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                            <?php if (!empty($item['receiver_address_sub'])) { ?>
                                                <?= ' ' . htmlspecialchars((string)$item['receiver_address_sub'], ENT_QUOTES, 'UTF-8') ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if (isset($item['Product']) && is_array($item['Product'])) {
                                                $productImage = trim((string)($product['CD_IMG'] ?? ''));
                                                if ($productImage !== '' && (string)($product['img_mode'] ?? 'this') !== 'out') {
                                                    $productImage = '/data/comparion/' . $productImage;
                                                }
                                            ?>
                                                <div class="partner-match-card" onclick="onlyAD.prdView('<?= (int)($product['CD_IDX'] ?? 0) ?>','info');">
                                                    <img class="partner-match-thumb" src="<?= htmlspecialchars($productImage, ENT_QUOTES, 'UTF-8') ?>" alt="">
                                                    <div class="partner-match-info">
                                                        <span class="partner-match-name"><?= htmlspecialchars((string)($product['CD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                                        판매가 : <?= number_format((float)($product['cd_sale_price'] ?? 0)) ?> |
                                                        원가 : <?= number_format((float)($product['cd_cost_price'] ?? 0)) ?>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="text-center">매칭된 상품이 없습니다.</div>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btnstyle1 btnstyle1-sm btnstyle1-danger purchase-goods-delete-btn"
                                                data-item-idx="<?= (int)($item['idx'] ?? 0) ?>"
                                            >삭제</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="16" class="text-center">주문상품 데이터가 없습니다.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="8" class="text-right">
                                </th>
                                <th class="text-right"><b><?= number_format((float)($summary['total_amount'] ?? 0)) ?></b></th>
                                <th class="text-right"><b></b></th>
                                <th class="text-right"><b><?= number_format((float)($summary['total_cost_amount'] ?? 0)) ?></b></th>
                                <th colspan="5"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <?php
            $amountCalculation = $amountCalculation ?? [];
            $purchaseBaseAmount = (float)($amountCalculation['base_amount'] ?? ($summary['total_cost_amount'] ?? 0));
            $purchaseAdditionalCosts = $amountCalculation['additional_costs'] ?? [];
            $purchaseFinalAmount = (float)($amountCalculation['final_amount'] ?? $purchaseBaseAmount);
            $isPaymentRequested = (string)($purchaseOrder['status'] ?? '') === 'payment_requested';
        ?>
        <div class="purchase-amount-card">
            <h3>발주서 금액</h3>
            <div class="purchase-amount-row">
                <label for="purchase-base-amount" class="purchase-amount-label">발주금액</label>
                <input type="text" id="purchase-base-amount" class="purchase-amount-input" value="<?= number_format($purchaseBaseAmount) ?>" inputmode="decimal">
                <span>원</span>
            </div>

            <div class="purchase-amount-row">
                <span class="purchase-amount-label">추가비용</span>
                <button type="button" id="purchase-additional-cost-add" class="btnstyle1 btnstyle1-sm">+ 추가비용</button>
            </div>
            <div id="purchase-additional-cost-list">
                <?php foreach ($purchaseAdditionalCosts as $additionalCost) { ?>
                    <div class="purchase-additional-cost-row">
                        <span class="purchase-amount-label"></span>
                        <input type="text" class="purchase-additional-cost-reason" value="<?= htmlspecialchars((string)($additionalCost['reason'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="추가비용 사유">
                        <input type="text" class="purchase-additional-cost-amount" value="<?= number_format((float)($additionalCost['amount'] ?? 0)) ?>" placeholder="금액" inputmode="decimal">
                        <span>원</span>
                        <button type="button" class="btnstyle1 btnstyle1-sm btnstyle1-danger purchase-additional-cost-delete">삭제</button>
                    </div>
                <?php } ?>
            </div>

            <div class="purchase-amount-row">
                <span class="purchase-amount-label">최종금액</span>
                <strong id="purchase-final-amount" class="purchase-final-amount"><?= number_format($purchaseFinalAmount) ?></strong>
                <span>원</span>
            </div>

        </div>

    </div>
</div>

<div id="purchase-goods-modal" class="purchase-goods-modal" role="dialog" aria-modal="true" aria-labelledby="purchase-goods-modal-title" aria-hidden="true">
    <div class="purchase-goods-modal-panel">
        <div class="purchase-goods-modal-head">
            <h3 id="purchase-goods-modal-title">발주상품 추가</h3>
            <button type="button" class="purchase-goods-modal-close" aria-label="닫기">&times;</button>
        </div>
        <div class="purchase-goods-modal-body">
            <div class="purchase-goods-search">
                <input type="text" id="purchase-goods-order-no" placeholder="고도몰 주문번호를 입력해 주세요" autocomplete="off">
                <button type="button" id="purchase-goods-search-btn" class="btnstyle1 btnstyle1-primary">주문 조회</button>
            </div>
            <div id="purchase-goods-result-message" class="purchase-goods-result-message">
                주문번호를 입력한 후 조회해 주세요.
            </div>
            <div id="purchase-goods-result" class="purchase-goods-result"></div>
        </div>
        <div class="purchase-goods-modal-foot">
            <label>
                <input type="checkbox" id="purchase-goods-check-all" disabled>
                선택 가능한 상품 전체 선택
            </label>
            <div>
                <button type="button" class="btnstyle1 purchase-goods-modal-close">취소</button>
                <button type="button" id="purchase-goods-add-selected-btn" class="btnstyle1 btnstyle1-primary" disabled>선택 상품 추가</button>
            </div>
        </div>
    </div>
</div>

<div id="contents_bottom">
    <div class="purchase-amount-actions">
        <?php if ($isPaymentRequested) { ?>
            <span>결제요청 등록이 완료된 발주서입니다.</span>
        <?php } else { ?>
            <button type="button" id="purchase-amount-draft-btn" class="btnstyle1 btnstyle1-md">임시저장</button>
            <button type="button" id="purchase-amount-payment-btn" class="btnstyle1 btnstyle1-primary btnstyle1-md">저장/결제요청</button>
        <?php } ?>
    </div>
</div>

<script>
    (function() {
        var purchaseOrderIdx = <?= (int)($purchaseOrder['idx'] ?? 0) ?>;
        var godoOrderNos = <?= json_encode(array_values(array_unique(array_filter(array_map(static function ($item) {
            return trim((string)($item['order_no'] ?? ''));
        }, $purchaseOrderItems ?? [])))), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        var $baseAmount = $('#purchase-base-amount');
        var $additionalCostList = $('#purchase-additional-cost-list');
        var $finalAmount = $('#purchase-final-amount');

        function parsePurchaseAmount(value) {
            var normalized = String(value || '').replace(/,/g, '').trim();
            var parsed = parseFloat(normalized);
            return isFinite(parsed) ? parsed : 0;
        }

        function formatPurchaseAmount(value) {
            return Math.round(Number(value) || 0).toLocaleString();
        }

        function calculatePurchaseFinalAmount() {
            var finalAmount = parsePurchaseAmount($baseAmount.val());
            $additionalCostList.find('.purchase-additional-cost-amount').each(function() {
                finalAmount += parsePurchaseAmount($(this).val());
            });
            $finalAmount.text(formatPurchaseAmount(finalAmount));
            return finalAmount;
        }

        function appendAdditionalCostRow(reason, amount) {
            var $row = $('<div>').addClass('purchase-additional-cost-row')
                .append($('<span>').addClass('purchase-amount-label'))
                .append($('<input>', {
                    type: 'text',
                    class: 'purchase-additional-cost-reason',
                    value: reason || '',
                    placeholder: '추가비용 사유'
                }))
                .append($('<input>', {
                    type: 'text',
                    class: 'purchase-additional-cost-amount',
                    value: amount ? formatPurchaseAmount(amount) : '',
                    placeholder: '금액',
                    inputmode: 'decimal'
                }))
                .append($('<span>').text('원'))
                .append($('<button>', {
                    type: 'button',
                    class: 'btnstyle1 btnstyle1-sm btnstyle1-danger purchase-additional-cost-delete',
                    text: '삭제'
                }));
            $additionalCostList.append($row);
            $row.find('.purchase-additional-cost-reason').focus();
        }

        function collectAdditionalCosts() {
            var costs = [];
            $additionalCostList.find('.purchase-additional-cost-row').each(function() {
                costs.push({
                    reason: String($(this).find('.purchase-additional-cost-reason').val() || '').trim(),
                    amount: parsePurchaseAmount($(this).find('.purchase-additional-cost-amount').val())
                });
            });
            return costs;
        }

        function savePurchaseAmount(openPaymentRequest) {
            var $buttons = $('#purchase-amount-draft-btn, #purchase-amount-payment-btn');
            $buttons.prop('disabled', true);

            $.ajax({
                url: '/admin/order/purchase/amount_save',
                type: 'POST',
                dataType: 'json',
                data: {
                    purchase_order_idx: purchaseOrderIdx,
                    base_amount: parsePurchaseAmount($baseAmount.val()),
                    additional_costs: JSON.stringify(collectAdditionalCosts())
                },
                success: function(res) {
                    if (!res || res.success !== true) {
                        alert((res && res.message) ? res.message : '발주서 금액 저장에 실패했습니다.');
                        return;
                    }

                    $baseAmount.val(formatPurchaseAmount(res.base_amount || 0));
                    $finalAmount.text(formatPurchaseAmount(res.final_amount || 0));
                    if (!openPaymentRequest) {
                        alert(res.message || '발주서 금액을 임시저장했습니다.');
                        return;
                    }

                    openDialog('/admin/payment/payment_request_create', {
                        mode: 'create',
                        category: '위탁발주',
                        kind: 'purchase_order',
                        purchaseOrderIdx: purchaseOrderIdx,
                        godoOrderNos: godoOrderNos
                    }, '결제요청 생성', '800px', 'POST');
                },
                error: function() {
                    alert('발주서 금액 저장 중 오류가 발생했습니다.');
                },
                complete: function() {
                    $buttons.prop('disabled', false);
                }
            });
        }

        $('#purchase-additional-cost-add').on('click', function() {
            appendAdditionalCostRow('', 0);
        });
        $additionalCostList.on('click', '.purchase-additional-cost-delete', function() {
            $(this).closest('.purchase-additional-cost-row').remove();
            calculatePurchaseFinalAmount();
        });
        $(document).on('input', '#purchase-base-amount, .purchase-additional-cost-amount', calculatePurchaseFinalAmount);
        $(document).on('blur', '#purchase-base-amount, .purchase-additional-cost-amount', function() {
            $(this).val(formatPurchaseAmount(parsePurchaseAmount($(this).val())));
        });
        $('#purchase-amount-draft-btn').on('click', function() {
            savePurchaseAmount(false);
        });
        $('#purchase-amount-payment-btn').on('click', function() {
            savePurchaseAmount(true);
        });
    })();

    (function() {
        var purchaseOrderIdx = <?= (int)($purchaseOrder['idx'] ?? 0) ?>;
        var $modal = $('#purchase-goods-modal');
        var $orderNoInput = $('#purchase-goods-order-no');
        var $result = $('#purchase-goods-result');
        var $message = $('#purchase-goods-result-message');
        var $checkAll = $('#purchase-goods-check-all');
        var $addSelectedButton = $('#purchase-goods-add-selected-btn');

        function setPurchaseGoodsLoading(isLoading) {
            $('#purchase-goods-search-btn').prop('disabled', isLoading);
            $orderNoInput.prop('disabled', isLoading);
            if (isLoading) {
                $message.text('주문 정보를 확인하고 있습니다.');
                $result.empty();
                $checkAll.prop({ checked: false, disabled: true });
                $addSelectedButton.prop('disabled', true);
            }
        }

        function updatePurchaseGoodsSelectionState() {
            var selectableCount = $('.purchase-goods-checkbox:not(:disabled)').length;
            var selectedCount = $('.purchase-goods-checkbox:not(:disabled):checked').length;
            $checkAll.prop('disabled', selectableCount === 0);
            $checkAll.prop('checked', selectableCount > 0 && selectedCount === selectableCount);
            $addSelectedButton.prop('disabled', selectedCount === 0);
        }

        function renderPurchaseGoods(goods) {
            $result.empty();
            if (!Array.isArray(goods) || goods.length === 0) {
                $result.append($('<div>').addClass('purchase-goods-empty').text('해당 주문의 상품 데이터가 없습니다.'));
                updatePurchaseGoodsSelectionState();
                return;
            }

            var $table = $('<table>');
            var $headRow = $('<tr>')
                .append($('<th>').text('선택'))
                .append($('<th>').text('주문상품번호'))
                .append($('<th>').text('이미지'))
                .append($('<th>').text('상품명 / 옵션'))
                .append($('<th>').text('수량'))
                .append($('<th>').text('금액'))
                .append($('<th>').text('상태'))
                .append($('<th>').text('바로 추가'));
            $table.append($('<thead>').append($headRow));

            var $tbody = $('<tbody>');
            goods.forEach(function(goodsRow) {
                var goodsId = parseInt(goodsRow.idx, 10) || 0;
                var isAvailable = goodsRow.selection_state === 'available' && goodsId > 0;
                var $checkbox = $('<input>', {
                    type: 'checkbox',
                    class: 'purchase-goods-checkbox',
                    value: goodsId,
                    disabled: !isAvailable
                });
                var $imageCell = $('<td>').addClass('text-center');
                var imageUrl = String(goodsRow.thumb_image_url || '').trim();
                if (imageUrl) {
                    $imageCell.append($('<img>', { src: imageUrl, alt: '' }));
                } else {
                    $imageCell.text('-');
                }

                var $nameCell = $('<td>').append(
                    $('<div>').text(String(goodsRow.goods_name || ''))
                );
                if (goodsRow.option_info_text) {
                    $nameCell.append($('<small>').text(String(goodsRow.option_info_text)));
                }

                var statusText = isAvailable
                    ? '추가 가능'
                    : String(goodsRow.selection_message || '추가 불가');
                var $directButton = $('<button>', {
                    type: 'button',
                    class: 'btnstyle1 btnstyle1-sm purchase-goods-add-one',
                    text: '1개 선택',
                    disabled: !isAvailable
                }).attr({
                    'data-goods-id': goodsId,
                    'data-available': isAvailable ? '1' : '0'
                });

                $tbody.append(
                    $('<tr>')
                        .append($('<td>').addClass('text-center').append($checkbox))
                        .append($('<td>').addClass('text-center').text(String(goodsRow.order_goods_sno || '')))
                        .append($imageCell)
                        .append($nameCell)
                        .append($('<td>').addClass('text-right').text(Number(goodsRow.goods_count || 0).toLocaleString()))
                        .append($('<td>').addClass('text-right').text(Number(goodsRow.goods_total_price || 0).toLocaleString()))
                        .append($('<td>').addClass('text-center').text(statusText))
                        .append($('<td>').addClass('text-center').append($directButton))
                );
            });

            $table.append($tbody);
            $result.append($table);
            updatePurchaseGoodsSelectionState();
        }

        function lookupPurchaseGoods() {
            var orderNo = String($orderNoInput.val() || '').trim();
            $orderNoInput.val(orderNo);
            if (!orderNo) {
                alert('주문번호를 입력해 주세요.');
                $orderNoInput.focus();
                return;
            }

            setPurchaseGoodsLoading(true);
            $.ajax({
                url: '/admin/order/purchase/goods_lookup',
                type: 'POST',
                dataType: 'json',
                data: {
                    purchase_order_idx: purchaseOrderIdx,
                    order_no: orderNo
                },
                success: function(res) {
                    if (!res || res.success !== true) {
                        $message.text((res && res.message) ? res.message : '주문 조회에 실패했습니다.');
                        return;
                    }
                    $message.text(res.message || '주문상품을 선택해 주세요.');
                    renderPurchaseGoods(res.goods || []);
                },
                error: function() {
                    $message.text('주문 조회 중 오류가 발생했습니다.');
                },
                complete: function() {
                    setPurchaseGoodsLoading(false);
                    updatePurchaseGoodsSelectionState();
                }
            });
        }

        function addPurchaseGoods(goodsIds) {
            goodsIds = (goodsIds || []).filter(function(goodsId) {
                return parseInt(goodsId, 10) > 0;
            });
            if (goodsIds.length === 0) {
                alert('추가할 상품을 선택해 주세요.');
                return;
            }

            $addSelectedButton.prop('disabled', true);
            $('.purchase-goods-add-one').prop('disabled', true);
            $.ajax({
                url: '/admin/order/purchase/goods_add',
                type: 'POST',
                dataType: 'json',
                data: {
                    purchase_order_idx: purchaseOrderIdx,
                    goods_ids: goodsIds
                },
                success: function(res) {
                    if (!res || res.success !== true) {
                        alert((res && res.message) ? res.message : '상품 추가에 실패했습니다.');
                        return;
                    }
                    alert(res.message || '상품을 발주서에 추가했습니다.');
                    location.reload();
                },
                error: function() {
                    alert('상품 추가 중 오류가 발생했습니다.');
                },
                complete: function() {
                    updatePurchaseGoodsSelectionState();
                    $('.purchase-goods-add-one').each(function() {
                        $(this).prop('disabled', $(this).attr('data-available') !== '1');
                    });
                }
            });
        }

        $('#purchase-goods-open-btn').on('click', function() {
            $modal.addClass('is-open').attr('aria-hidden', 'false');
            $('body').css('overflow', 'hidden');
            setTimeout(function() {
                $orderNoInput.focus();
            }, 0);
        });

        $('.purchase-goods-modal-close').on('click', function() {
            $modal.removeClass('is-open').attr('aria-hidden', 'true');
            $('body').css('overflow', '');
        });

        $modal.on('click', function(event) {
            if (event.target === this) {
                $('.purchase-goods-modal-close').first().trigger('click');
            }
        });

        $(document).on('keydown', function(event) {
            if (event.key === 'Escape' && $modal.hasClass('is-open')) {
                $('.purchase-goods-modal-close').first().trigger('click');
            }
        });

        $('#purchase-goods-search-btn').on('click', lookupPurchaseGoods);
        $orderNoInput.on('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                lookupPurchaseGoods();
            }
        });

        $checkAll.on('change', function() {
            $('.purchase-goods-checkbox:not(:disabled)').prop('checked', this.checked);
            updatePurchaseGoodsSelectionState();
        });
        $result.on('change', '.purchase-goods-checkbox', updatePurchaseGoodsSelectionState);
        $result.on('click', '.purchase-goods-add-one', function() {
            addPurchaseGoods([$(this).data('goods-id')]);
        });
        $addSelectedButton.on('click', function() {
            var goodsIds = $('.purchase-goods-checkbox:not(:disabled):checked').map(function() {
                return $(this).val();
            }).get();
            addPurchaseGoods(goodsIds);
        });
    })();

    $(document).on('click', '.purchase-goods-delete-btn', function() {
        var purchaseOrderIdx = <?= (int)($purchaseOrder['idx'] ?? 0) ?>;
        var purchaseOrderItemIdx = parseInt($(this).data('item-idx'), 10) || 0;
        if (purchaseOrderIdx < 1 || purchaseOrderItemIdx < 1) {
            alert('삭제할 발주상품 정보를 확인할 수 없습니다.');
            return;
        }

        if (!confirm('선택한 상품을 발주서에서 삭제하시겠습니까?\n삭제한 상품은 다시 추가할 수 있습니다.')) {
            return;
        }

        var $button = $(this);
        $button.prop('disabled', true);

        $.ajax({
            url: '/admin/order/purchase/goods_delete',
            type: 'POST',
            dataType: 'json',
            data: {
                purchase_order_idx: purchaseOrderIdx,
                purchase_order_item_idx: purchaseOrderItemIdx
            },
            success: function(res) {
                if (!res || res.success !== true) {
                    alert((res && res.message) ? res.message : '발주상품 삭제에 실패했습니다.');
                    return;
                }
                alert(res.message || '발주상품을 삭제했습니다.');
                location.reload();
            },
            error: function() {
                alert('발주상품 삭제 중 오류가 발생했습니다.');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '#purchase-delete-btn', function() {
        var purchaseOrderIdx = parseInt($(this).data('idx'), 10) || 0;
        if (purchaseOrderIdx < 1) {
            alert('삭제할 발주서 번호를 확인할 수 없습니다.');
            return;
        }

        var confirmed = confirm(
            '정말 삭제하시겠습니까? 삭제하면 다시 복구되지 않습니다.\n'
            + '삭제된 상품은 다시 담기가 가능합니다.'
        );
        if (!confirmed) {
            return;
        }

        var $button = $(this);
        $button.prop('disabled', true);

        $.ajax({
            url: '/admin/order/purchase/delete',
            type: 'POST',
            dataType: 'json',
            data: {
                idx: purchaseOrderIdx
            },
            success: function(res) {
                if (!res || res.success !== true) {
                    alert((res && res.message) ? res.message : '발주서 삭제에 실패했습니다.');
                    return;
                }
                alert((res.message || '발주서가 삭제되었습니다.'));
                location.href = '/admin/order/purchase/list';
            },
            error: function(request) {
                alert((request && request.responseText) ? request.responseText : '발주서 삭제 중 오류가 발생했습니다.');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
</script>