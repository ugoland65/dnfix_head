<div id="contents_head">
    <h1>구매대행 발주서</h1>
    <h3>구매대행 전용 발주서 리스트</h3>

    <div id="head_write_btn">
		<button type="button" id="purchase-create-open-btn" class="btnstyle1 btnstyle1-danger btnstyle1-lg">
			<i class="fas fa-plus-circle"></i>
			발주서 생성
		</button>
	</div>

</div>

<div id="contents_body">
    <div id="contents_body_wrap">

        <?php
        $purchaseOrderSummary = $purchaseOrderSummary ?? [];
        $summaryTotal = $purchaseOrderSummary['total'] ?? ['count' => 0, 'amount' => 0];
        $summaryByStatus = $purchaseOrderSummary['status'] ?? [];
        ?>
        <style>
            .purchase-summary { display:flex; flex-wrap:wrap; gap:10px; margin:0 0 12px 0; }
            .purchase-summary .summary-card { min-width:220px; padding:12px 14px; border:1px solid #d8dde5; border-radius:8px; background:#fff; }
            .purchase-summary .summary-card-total { border-color:#8aa8ff; background:#f3f7ff; }
            .purchase-summary .summary-title { font-size:12px; color:#667085; margin-bottom:6px; }
            .purchase-summary .summary-count { font-size:18px; font-weight:700; color:#101828; line-height:1.2; }
            .purchase-summary .summary-amount { margin-top:3px; font-size:13px; color:#344054; }
            .purchase-create-modal { display:none; position:fixed; inset:0; z-index:10020; padding:40px 20px; box-sizing:border-box; background:rgba(0, 0, 0, .45); }
            .purchase-create-modal.is-open { display:flex; align-items:center; justify-content:center; }
            .purchase-create-panel { width:min(1100px, 100%); max-height:calc(100vh - 80px); display:flex; flex-direction:column; overflow:hidden; border-radius:8px; background:#fff; box-shadow:0 12px 40px rgba(0, 0, 0, .25); }
            .purchase-create-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #ddd; }
            .purchase-create-head h3 { margin:0; }
            .purchase-create-head .purchase-create-close { border:0; background:transparent; font-size:26px; line-height:1; cursor:pointer; }
            .purchase-create-body { min-height:300px; padding:20px; overflow:auto; }
            .purchase-create-fields { display:grid; grid-template-columns:220px minmax(240px, 1fr) 220px auto; gap:8px; align-items:center; }
            .purchase-create-fields select, .purchase-create-fields input { width:100%; height:36px; box-sizing:border-box; }
            .purchase-create-result-message { margin:12px 0; color:#555; }
            .purchase-create-result table { width:100%; border-collapse:collapse; }
            .purchase-create-result th, .purchase-create-result td { padding:8px; border:1px solid #ddd; vertical-align:middle; }
            .purchase-create-result th { background:#f6f6f6; text-align:center; }
            .purchase-create-result img { width:48px; height:48px; object-fit:cover; }
            .purchase-create-foot { display:flex; justify-content:space-between; align-items:center; padding:14px 20px; border-top:1px solid #ddd; }
            .purchase-create-empty { padding:45px 10px; text-align:center; color:#777; }
        </style>

        <div class="purchase-summary">
            <div class="summary-card summary-card-total">
                <div class="summary-title">총 발주서</div>
                <div class="summary-count"><?= number_format((int)($summaryTotal['count'] ?? 0)) ?>건</div>
                <div class="summary-amount"><?= number_format((float)($summaryTotal['amount'] ?? 0), 2) ?> 원</div>
            </div>
            <?php foreach ($summaryByStatus as $summaryStatus) { ?>
                <div class="summary-card">
                    <div class="summary-title"><?= $summaryStatus['label'] ?></div>
                    <div class="summary-count"><?= number_format((int)($summaryStatus['count'] ?? 0)) ?>건</div>
                    <div class="summary-amount"><?= number_format((float)($summaryStatus['amount'] ?? 0), 2) ?> 원</div>
                </div>
            <?php } ?>
        </div>

        <div class="top-search-wrap">
            <ul class="count-wrap">
                <span class="count">Total : <b><?= number_format((int)($pagination['total'] ?? 0)) ?></b></span>
                <span class="m-l-10"><b><?= (int)($pagination['current_page'] ?? 1) ?></b></span>
                <span>/</span>
                <span><b><?= (int)($pagination['last_page'] ?? 1) ?></b> page</span>
            </ul>
            <ul class="m-l-10">
                <select name="status" id="status">
                    <option value="all" <?= ($status ?? 'all') === 'all' ? 'selected' : '' ?>>상태 전체</option>
                    <option value="created" <?= ($status ?? '') === 'created' ? 'selected' : '' ?>>생성</option>
                    <option value="downloaded" <?= ($status ?? '') === 'downloaded' ? 'selected' : '' ?>>다운로드</option>
                    <option value="closed" <?= ($status ?? '') === 'closed' ? 'selected' : '' ?>>종료</option>
                </select>
            </ul>
            <ul>
                <input type="text" name="supplier_name" id="supplier_name" placeholder="공급사명" value="<?= htmlspecialchars((string)($supplier_name ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </ul>
            <ul>
                <input type="text" name="search_value" id="search_value" placeholder="발주서명/PO CODE/주문번호" value="<?= htmlspecialchars((string)($search_value ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </ul>
            <ul>
                <label style="display:inline-flex; align-items:center; gap:4px;">
                    선택: <b id="selected-order-count">0</b>건
                </label>
            </ul>
            <ul>
                <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm" id="mergeBtn">
                    선택 병합
                </button>
            </ul>
            <ul>
                <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm" id="searchBtn">
                    <i class="fas fa-search"></i> 검색
                </button>
                <button type="button" class="btnstyle1 btnstyle1-sm" id="search_reset">
                    <i class="far fa-trash-alt"></i> 초기화
                </button>
            </ul>
        </div>

        <div id="list_new_wrap" style="max-height: calc(100% - 180px);">
            <div class="table-wrap5">
                <div class="scroll-wrap">
                    <table class="table-st1">
                        <thead>
                            <tr class="list">
                                <th class="list-checkbox"><input type="checkbox" id="merge-check-all"></th>
                                <th class="list-idx">번호</th>
                                <th>발주서명</th>
                                <th>PO CODE</th>
                                <th>공급사</th>
                                <th>주문수</th>
                                <th>총수량</th>
                                <th>총금액</th>
                                <th>상태</th>
                                <th>등록자</th>
                                <th>등록일</th>
                                <th>발주</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($purchaseOrderList)) { ?>
                                <?php foreach ($purchaseOrderList as $purchaseOrder) { ?>
                                    <tr data-supplier-name="<?= htmlspecialchars((string)($purchaseOrder['supplier_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                        <td class="text-center"><input type="checkbox" class="merge-check-item" value="<?= (int)($purchaseOrder['idx'] ?? 0) ?>"></td>
                                        <td class="text-center"><?= (int)($purchaseOrder['idx'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars((string)($purchaseOrder['order_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= htmlspecialchars((string)($purchaseOrder['po_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= htmlspecialchars((string)($purchaseOrder['supplier_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-right"><?= number_format((int)($purchaseOrder['item_count'] ?? 0)) ?></td>
                                        <td class="text-right"><?= number_format((int)($purchaseOrder['total_quantity'] ?? 0)) ?></td>
                                        <td class="text-right"><?= number_format((float)($purchaseOrder['total_amount'] ?? 0), 2) ?></td>

                                        <!-- 상태 -->
                                        <td class="text-center">
                                            <?= htmlspecialchars((string)($purchaseOrder['status_text'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <td class="text-center"><?= htmlspecialchars((string)($purchaseOrder['created_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($purchaseOrder['created_at'])) { ?>
                                                <?= date('y.m.d H:i', strtotime($purchaseOrder['created_at'])) ?>
                                            <?php } ?>
                                        </td>

                                        <!-- 발주 -->
                                        <td class="text-center">
                                            <?php if (($purchaseOrder['status'] ?? '') !== 'payment_requested') { ?>
                                            <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" 
                                                data-purchase-order-idx="<?= (int)($purchaseOrder['idx'] ?? 0) ?>"
                                                data-godo-order-nos="<?= htmlspecialchars(json_encode($purchaseOrder['godo_order_nos'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>"
                                                onclick="purchaseOrderReg.paymentRequestCreate(this);">결제요청서 등록</button>
                                            <?php } ?>
                                        </td>

                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btnstyle1 btnstyle1-sm"
                                                onclick="location.href='/admin/order/purchase/detail?idx=<?= (int)($purchaseOrder['idx'] ?? 0) ?>'">
                                                주문상품
                                            </button>
                                            <button
                                                type="button"
                                                class="btnstyle1 btnstyle1-info btnstyle1-sm"
                                                onclick="location.href='/admin/order/godo_order_purchase/excel?purchase_order_idx=<?= (int)($purchaseOrder['idx'] ?? 0) ?>'">
                                                엑셀 다운로드
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="13" class="text-center">데이터가 없습니다.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="purchase-create-modal" class="purchase-create-modal" role="dialog" aria-modal="true" aria-labelledby="purchase-create-title" aria-hidden="true">
    <div class="purchase-create-panel">
        <div class="purchase-create-head">
            <h3 id="purchase-create-title">발주서 생성</h3>
            <button type="button" class="purchase-create-close" aria-label="닫기">&times;</button>
        </div>
        <div class="purchase-create-body">
            <div class="purchase-create-fields">
                <select id="purchase-create-supplier">
                    <option value="">공급사를 선택해 주세요</option>
                    <?php foreach (($supplierOptions ?? []) as $supplierOption) { ?>
                        <option value="<?= (int)($supplierOption['idx'] ?? 0) ?>">
                            <?= htmlspecialchars((string)($supplierOption['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php } ?>
                </select>
                <input type="text" id="purchase-create-order-no" placeholder="고도몰 주문번호를 입력해 주세요" autocomplete="off">
                <input type="text" id="purchase-create-order-name" value="<?= date('Ymd') ?>-" placeholder="발주서명">
                <button type="button" id="purchase-create-search-btn" class="btnstyle1 btnstyle1-primary">주문 조회</button>
            </div>
            <div id="purchase-create-result-message" class="purchase-create-result-message">
                공급사를 선택하고 주문번호를 조회해 주세요.
            </div>
            <div id="purchase-create-result" class="purchase-create-result"></div>
        </div>
        <div class="purchase-create-foot">
            <label>
                <input type="checkbox" id="purchase-create-check-all" disabled>
                선택 가능한 상품 전체 선택
            </label>
            <div>
                <button type="button" class="btnstyle1 purchase-create-close">취소</button>
                <button type="button" id="purchase-create-submit-btn" class="btnstyle1 btnstyle1-primary" disabled>선택 상품으로 발주서 생성</button>
            </div>
        </div>
    </div>
</div>

<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"><?= $paginationHtml ?? '' ?></div>
</div>

<script>
    (function() {
        var $modal = $('#purchase-create-modal');
        var $supplier = $('#purchase-create-supplier');
        var $orderNo = $('#purchase-create-order-no');
        var $orderName = $('#purchase-create-order-name');
        var $result = $('#purchase-create-result');
        var $message = $('#purchase-create-result-message');
        var $checkAll = $('#purchase-create-check-all');
        var $submitButton = $('#purchase-create-submit-btn');

        function updateCreateSelectionState() {
            var $selectable = $result.find('.purchase-create-checkbox:not(:disabled)');
            var selectedCount = $selectable.filter(':checked').length;
            $checkAll.prop('disabled', $selectable.length === 0);
            $checkAll.prop('checked', $selectable.length > 0 && selectedCount === $selectable.length);
            $submitButton.prop('disabled', selectedCount === 0);
        }

        function setCreateLoading(isLoading) {
            $('#purchase-create-search-btn').prop('disabled', isLoading);
            $orderNo.prop('disabled', isLoading);
            if (isLoading) {
                $message.text('주문 정보를 확인하고 있습니다.');
                $result.empty();
                $checkAll.prop({ checked: false, disabled: true });
                $submitButton.prop('disabled', true);
            }
        }

        function renderCreateGoods(goods) {
            $result.empty();
            if (!Array.isArray(goods) || goods.length === 0) {
                $result.append($('<div>').addClass('purchase-create-empty').text('해당 주문의 상품 데이터가 없습니다.'));
                updateCreateSelectionState();
                return;
            }

            var $table = $('<table>');
            $table.append(
                $('<thead>').append(
                    $('<tr>')
                        .append($('<th>').text('선택'))
                        .append($('<th>').text('주문상품번호'))
                        .append($('<th>').text('이미지'))
                        .append($('<th>').text('상품명 / 옵션'))
                        .append($('<th>').text('수량'))
                        .append($('<th>').text('금액'))
                        .append($('<th>').text('상태'))
                        .append($('<th>').text('바로 생성'))
                )
            );

            var $tbody = $('<tbody>');
            goods.forEach(function(goodsRow) {
                var orderGoodsSno = String(goodsRow.order_goods_sno || '').trim();
                var isAvailable = goodsRow.selection_state === 'available' && orderGoodsSno !== '';
                var $checkbox = $('<input>', {
                    type: 'checkbox',
                    class: 'purchase-create-checkbox',
                    value: orderGoodsSno,
                    disabled: !isAvailable
                });
                var $imageCell = $('<td>').addClass('text-center');
                var imageUrl = String(goodsRow.thumb_image_url || '').trim();
                if (imageUrl) {
                    $imageCell.append($('<img>', { src: imageUrl, alt: '' }));
                } else {
                    $imageCell.text('-');
                }

                var $nameCell = $('<td>').append($('<div>').text(String(goodsRow.goods_name || '')));
                if (goodsRow.option_info_text) {
                    $nameCell.append($('<small>').text(String(goodsRow.option_info_text)));
                }

                var $singleButton = $('<button>', {
                    type: 'button',
                    class: 'btnstyle1 btnstyle1-sm purchase-create-one',
                    text: '1개 선택',
                    disabled: !isAvailable
                }).attr({
                    'data-order-goods-sno': orderGoodsSno,
                    'data-available': isAvailable ? '1' : '0'
                });

                $tbody.append(
                    $('<tr>')
                        .append($('<td>').addClass('text-center').append($checkbox))
                        .append($('<td>').addClass('text-center').text(orderGoodsSno))
                        .append($imageCell)
                        .append($nameCell)
                        .append($('<td>').addClass('text-right').text(Number(goodsRow.goods_count || 0).toLocaleString()))
                        .append($('<td>').addClass('text-right').text(Number(goodsRow.goods_total_price || 0).toLocaleString()))
                        .append($('<td>').addClass('text-center').text(
                            isAvailable ? '생성 가능' : String(goodsRow.selection_message || '생성 불가')
                        ))
                        .append($('<td>').addClass('text-center').append($singleButton))
                );
            });

            $table.append($tbody);
            $result.append($table);
            updateCreateSelectionState();
        }

        function lookupOrderForCreate() {
            var orderNo = String($orderNo.val() || '').trim();
            $orderNo.val(orderNo);
            if (!orderNo) {
                alert('주문번호를 입력해 주세요.');
                $orderNo.focus();
                return;
            }

            setCreateLoading(true);
            $.ajax({
                url: '/admin/order/purchase/goods_lookup',
                type: 'POST',
                dataType: 'json',
                data: {
                    purchase_order_idx: 0,
                    order_no: orderNo
                },
                success: function(res) {
                    if (!res || res.success !== true) {
                        $message.text((res && res.message) ? res.message : '주문 조회에 실패했습니다.');
                        return;
                    }
                    $message.text(res.message || '발주서에 포함할 상품을 선택해 주세요.');
                    renderCreateGoods(res.goods || []);
                },
                error: function() {
                    $message.text('주문 조회 중 오류가 발생했습니다.');
                },
                complete: function() {
                    setCreateLoading(false);
                    updateCreateSelectionState();
                }
            });
        }

        function createPurchaseOrder(orderGoodsSnos) {
            var supplierPartnerIdx = parseInt($supplier.val(), 10) || 0;
            var orderName = String($orderName.val() || '').trim();
            orderGoodsSnos = (orderGoodsSnos || []).filter(function(value) {
                return String(value || '').trim() !== '';
            });

            if (supplierPartnerIdx < 1) {
                alert('공급사를 선택해 주세요.');
                $supplier.focus();
                return;
            }
            if (!orderName) {
                alert('발주서명을 입력해 주세요.');
                $orderName.focus();
                return;
            }
            if (orderGoodsSnos.length === 0) {
                alert('발주서에 포함할 상품을 선택해 주세요.');
                return;
            }

            $submitButton.prop('disabled', true);
            $result.find('.purchase-create-one').prop('disabled', true);
            $.ajax({
                url: '/admin/order/godo_order_purchase/create_sheet',
                type: 'POST',
                dataType: 'json',
                data: {
                    order_goods_snos: orderGoodsSnos,
                    order_name: orderName,
                    supplier_partner_idx: supplierPartnerIdx
                },
                success: function(res) {
                    if (!res || res.success !== true) {
                        alert((res && res.message) ? res.message : '발주서 생성에 실패했습니다.');
                        return;
                    }
                    alert((res.message || '발주서가 생성되었습니다.') + '\n엑셀 다운로드를 시작합니다.');
                    /*
                    if (res.download_url) {
                        window.location.href = res.download_url;
                    } else {
                        location.reload();
                    }
                        */
                        location.reload();
                },
                error: function() {
                    alert('발주서 생성 중 오류가 발생했습니다.');
                },
                complete: function() {
                    updateCreateSelectionState();
                    $result.find('.purchase-create-one').each(function() {
                        $(this).prop('disabled', $(this).attr('data-available') !== '1');
                    });
                }
            });
        }

        $('#purchase-create-open-btn').on('click', function() {
            $modal.addClass('is-open').attr('aria-hidden', 'false');
            $('body').css('overflow', 'hidden');
            setTimeout(function() {
                $supplier.focus();
            }, 0);
        });
        $('.purchase-create-close').on('click', function() {
            $modal.removeClass('is-open').attr('aria-hidden', 'true');
            $('body').css('overflow', '');
        });
        $modal.on('click', function(event) {
            if (event.target === this) {
                $('.purchase-create-close').first().trigger('click');
            }
        });
        $(document).on('keydown', function(event) {
            if (event.key === 'Escape' && $modal.hasClass('is-open')) {
                $('.purchase-create-close').first().trigger('click');
            }
        });
        $('#purchase-create-search-btn').on('click', lookupOrderForCreate);
        $orderNo.on('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                lookupOrderForCreate();
            }
        });
        $checkAll.on('change', function() {
            $result.find('.purchase-create-checkbox:not(:disabled)').prop('checked', this.checked);
            updateCreateSelectionState();
        });
        $result.on('change', '.purchase-create-checkbox', updateCreateSelectionState);
        $result.on('click', '.purchase-create-one', function() {
            createPurchaseOrder([$(this).attr('data-order-goods-sno')]);
        });
        $submitButton.on('click', function() {
            var orderGoodsSnos = $result.find('.purchase-create-checkbox:not(:disabled):checked').map(function() {
                return $(this).val();
            }).get();
            createPurchaseOrder(orderGoodsSnos);
        });
    })();

    var purchaseOrderReg = (function() {
        function paymentRequestCreate(button) {
            var purchaseOrderIdx = Number($(button).data('purchase-order-idx') || 0);
            if (purchaseOrderIdx <= 0) {
                alert('발주서 번호가 없습니다.');
                return;
            }
            var godoOrderNos = [];
            try {
                godoOrderNos = JSON.parse(String($(button).attr('data-godo-order-nos') || '[]'));
            } catch (e) {
                godoOrderNos = [];
            }

            openDialog('/admin/payment/payment_request_create', {
                mode: 'create',
                category: '위탁발주',
                kind: 'purchase_order',
                purchaseOrderIdx: purchaseOrderIdx,
                godoOrderNos: godoOrderNos
            }, '결제요청 생성', '800px', 'POST');
        }

        return {
            paymentRequestCreate: paymentRequestCreate
        };
    })();

    function getSearchParams() {
        var params = {};
        var fields = {
            status: $('#status').val(),
            supplier_name: $('#supplier_name').val(),
            search_value: $('#search_value').val()
        };

        for (var key in fields) {
            if (fields[key] !== undefined && fields[key] !== null && fields[key] !== '') {
                params[key] = fields[key];
            }
        }
        return params;
    }

    function navigateWithParams(params) {
        var queryString = Object.keys(params)
            .map(function(key) {
                return key + '=' + encodeURIComponent(params[key]);
            })
            .join('&');
        location.href = '/admin/order/purchase/list' + (queryString ? '?' + queryString : '');
    }

    function getMergeCheckboxes() {
        return $('.merge-check-item');
    }

    function normalizeSupplierName(rawName) {
        var supplierName = $.trim(String(rawName || ''));
        return supplierName !== '' ? supplierName : '(공급사 미지정)';
    }

    function getSelectedMergeRows() {
        return getMergeCheckboxes().filter(':checked').closest('tr');
    }

    function getSelectedMergeOrderIdxs() {
        return getMergeCheckboxes()
            .filter(':checked')
            .map(function() {
                return String($(this).val() || '').trim();
            })
            .get()
            .filter(function(value) {
                return value !== '';
            });
    }

    function updateMergeSelectedState() {
        var $checkboxes = getMergeCheckboxes();
        var selectedCount = $checkboxes.filter(':checked').length;
        $('#selected-order-count').text(selectedCount);
        var allChecked = $checkboxes.length > 0 && selectedCount === $checkboxes.length;
        $('#merge-check-all').prop('checked', allChecked);
    }

    function getSelectedSupplierForMerge(exceptCheckbox) {
        var selectedSupplier = '';
        getMergeCheckboxes().filter(':checked').each(function() {
            if (exceptCheckbox && this === exceptCheckbox.get(0)) {
                return;
            }
            var supplier = normalizeSupplierName($(this).closest('tr').data('supplier-name'));
            if (supplier !== '') {
                selectedSupplier = supplier;
                return false;
            }
        });
        return selectedSupplier;
    }

    function handleMergeSelectedOrders() {
        var selectedIdxs = getSelectedMergeOrderIdxs();
        if (selectedIdxs.length < 2) {
            alert('병합할 발주서를 2건 이상 선택해 주세요.');
            return;
        }

        if (!confirm('선택한 발주서를 병합하시겠습니까?')) {
            return;
        }

        var $button = $('#mergeBtn');
        $button.prop('disabled', true);

        $.ajax({
            url: '/admin/order/purchase/merge',
            type: 'POST',
            dataType: 'json',
            data: {
                purchase_order_idxs: selectedIdxs
            },
            success: function(res) {
                if (!res || res.success !== true) {
                    alert((res && res.message) ? res.message : '발주서 병합에 실패했습니다.');
                    return;
                }
                alert(res.message || '발주서 병합이 완료되었습니다.');
                location.reload();
            },
            error: function(request) {
                alert((request && request.responseText) ? request.responseText : '발주서 병합 중 오류가 발생했습니다.');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    }

    $(function() {
        $('#searchBtn').on('click', function() {
            navigateWithParams(getSearchParams());
        });

        $('#search_value').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#searchBtn').trigger('click');
            }
        });

        $('#search_reset').on('click', function() {
            location.href = '/admin/order/purchase/list';
        });

        $('#mergeBtn').on('click', function() {
            handleMergeSelectedOrders();
        });

        $('#merge-check-all').on('change', function() {
            var checked = $(this).is(':checked');
            var $checkboxes = getMergeCheckboxes();
            if (!checked) {
                $checkboxes.prop('checked', false);
                updateMergeSelectedState();
                return;
            }

            var targetSupplier = '';
            var skippedCount = 0;
            $checkboxes.each(function() {
                var $checkbox = $(this);
                var supplier = normalizeSupplierName($checkbox.closest('tr').data('supplier-name'));
                if (targetSupplier === '') {
                    targetSupplier = supplier;
                }
                if (supplier !== targetSupplier) {
                    skippedCount++;
                    $checkbox.prop('checked', false);
                    return;
                }
                $checkbox.prop('checked', true);
            });
            if (skippedCount > 0) {
                alert('같은 공급사 발주서만 병합할 수 있습니다.');
            }
            updateMergeSelectedState();
        });

        $(document).on('change', '.merge-check-item', function() {
            var $checkbox = $(this);
            if ($checkbox.is(':checked')) {
                var selectedSupplier = getSelectedSupplierForMerge($checkbox);
                var currentSupplier = normalizeSupplierName($checkbox.closest('tr').data('supplier-name'));
                if (selectedSupplier !== '' && currentSupplier !== selectedSupplier) {
                    alert('같은 공급사 발주서만 병합할 수 있습니다.');
                    $checkbox.prop('checked', false);
                }
            }
            updateMergeSelectedState();
        });

        updateMergeSelectedState();
    });
</script>

