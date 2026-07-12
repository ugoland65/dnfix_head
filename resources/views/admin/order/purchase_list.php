<div id="contents_head">
    <h1>구매대행 발주서</h1>
    <h3>구매대행 전용 발주서 리스트</h3>
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
                                        <td class="text-center"><?= htmlspecialchars((string)($purchaseOrder['status_text'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= htmlspecialchars((string)($purchaseOrder['created_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($purchaseOrder['created_at'])) { ?>
                                                <?= date('y.m.d H:i', strtotime($purchaseOrder['created_at'])) ?>
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
                                    <td colspan="12" class="text-center">데이터가 없습니다.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"><?= $paginationHtml ?? '' ?></div>
</div>

<script>
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

