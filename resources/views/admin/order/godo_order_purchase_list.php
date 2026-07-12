<div id="contents_head">
    <h1>고도몰 주문 (개별주문)</h1>
    <div class="m-l-20">
        <select name="mode" id="mode">
            <option value="p" <?= $mode == 'p' ? 'selected' : '' ?>>결제완료</option>
            <option value="p2" <?= $mode == 'p2' ? 'selected' : '' ?>>결제완료 - 출고일 조정</option>
            <option value="g" <?= $mode == 'g' ? 'selected' : '' ?>>준비중</option>
            <option value="g5" <?= $mode == 'g5' ? 'selected' : '' ?>>배송준비중-핸디</option>
            <option value="g6" <?= $mode == 'g6' ? 'selected' : '' ?>>배송준비중-공급사 주문대기</option>
            <option value="g7" <?= $mode == 'g7' ? 'selected' : '' ?>>배송준비중- CS 처리 대기</option>
            <option value="g8" <?= $mode == 'g8' ? 'selected' : '' ?>>배송준비중-CS 처리중</option>
            <option value="g9" <?= $mode == 'g9' ? 'selected' : '' ?>>배송준비중-공급사 주문완료</option>
            <option value="d" <?= $mode == 'd' ? 'selected' : '' ?>>배송중</option>
            <option value="ds" <?= $mode == 'ds' ? 'selected' : '' ?>>배송완료</option>
            <option value="s1" <?= $mode == 's1' ? 'selected' : '' ?>>구매확정</option>
        </select>
        <label class="calendar-input">
            <input type='text' name='start_date' id="start_date" value="<?= $start_date ?? date('Y-m-d') ?>">
        </label>
        ~
        <label class="calendar-input">
            <input type='text' name='end_date' id="end_date" value="<?= $end_date ?? date('Y-m-d') ?>">
        </label>
        <button type="button" id="search_btn" class="btnstyle1 btnstyle1-primary btnstyle1-sm ">조회</button>
    </div>

    <?php /*
    <div class="right after-select-action-btn-wrap">
        <ul>
            선택 : <span id="selected-count">0</span>
        </ul>
        <ul>
            <button type="button" id="purchase-order-create-btn" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="handleOrderSheetReg()" >
                신규발주서 생성
            </button>
        </ul>
    </div>
    */ ?>

</div>

<div id="contents_body">
    <div id="contents_body_wrap">

        <div class="layout-style1">
            <ul>
                <div class="scroll-wrap">

                    <table class="table-st1">
                        <thead>
                            <tr>
                                <th class="list-checkbox"><input type="checkbox" id="purchase-check-all" name="" onclick="select_all(this)"></th>
                                <th class="list-idx">No</th>
                                <th class="">주문번호</th>
                                <th class="">주문일</th>
                                <th class="">결제일</th>
                                <th class="">공급사</th>
                                <th class="">주문상품</th>
                                <th class="">주문상품명</th>
                                <th class="">주문<br>수량</th>
                                <th class="">매칭상품</th>

                                <th class="">저장상태</th>
                                <th class="">발주상태</th>

                                <th class="">C/S 요청</th>
                                <th class="">C/S 등록수</th>
                                <th class="">수령자명</th>
                                <th class="">수령자<br>전화번호</th>
                                <th class="">수령자<br>휴대폰번호</th>
                                <th class="">우편번호</th>
                                <th class="">주소</th>
                                <th class="">상세주소</th>
                                <th class="">주문메모</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $savedOrderGoodsMap = $savedOrderGoodsMap ?? [];
                            $purchaseStatusMap = $purchaseStatusMap ?? [];
                            $purchaseOrderIdxMap = $purchaseOrderIdxMap ?? [];
                            $no = 0;
                            foreach ($orderList['data'] as $order) {
                                if (!is_array($order) || !isset($order['orderNo'])) {
                                    continue;
                                }
                                $scmName = trim((string)($order['scmName'] ?? ''));
                                $isMatched = isset($order['Product']) ? '1' : '0';
                                $isRefundPending = ((string)($order['userHandleFl'] ?? '') === 'r');
                                $no++;
                            ?>
                                <tr data-scm-name="<?= htmlspecialchars($scmName, ENT_QUOTES, 'UTF-8') ?>" data-is-matched="<?= $isMatched ?>">
                                    <td><input type="checkbox" class="purchase-check-item" name="check_idx[]" value="<?= $order['orderGoodsSno'] ?>" <?= $isRefundPending ? 'disabled title="환불요청중 건은 선택할 수 없습니다."' : '' ?>></td>
                                    <td class="text-center"><?= $no ?></td>
                                    <td class="text-center">
                                        <?php if ($order['userHandleFl'] == 'r') { ?>
                                            <span class="order-handle-label order-handle-label-refund">환불요청중</span>
                                        <?php } elseif ($order['userHandleFl'] == 'y') { ?>
                                            <span class="order-handle-label order-handle-label-approved">환불승인</span>
                                        <?php } elseif ($order['userHandleFl'] == 'n') { ?>
                                            <span class="order-handle-label order-handle-label-rejected">환불거절</span>
                                        <?php } ?>
                                        <p><a href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= $order['orderNo'] ?>" target="_blank"><b>#<?= $order['orderNo'] ?></b></a></p>
                                        <p><?= $order['orderGoodsSno'] ?></p>
                                    </td>
                                    <td class="text-center">
                                        <p><?= date('y.m.d', strtotime($order['regDt'])) ?></p>
                                        <b><?= date('H:i', strtotime($order['regDt'])) ?></b>
                                    </td>
                                    <td class="text-center">
                                        <p><?= date('y.m.d', strtotime($order['paymentDt'])) ?></p>
                                        <b><?= date('H:i', strtotime($order['paymentDt'])) ?></b>
                                    </td>
                                    <td>
                                        <?= $order['scmName'] ?>
                                    </td>
                                    <td><img src="<?= $order['thumbImageUrl'] ?>" style="width:50px; height: 50px;"></td>
                                    <td>
                                        <b><?= $order['goodsNm'] ?></b>
                                        <?php if (!empty($order['optionInfo'])) { ?>
                                            <div class="option-info">
                                                <?php foreach ($order['optionInfo'] as $option) { ?>
                                                    <div class="option-info-item">
                                                        <?= $option[0] ?> : <?= $option[1] ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center"><?= $order['goodsCnt'] ?></td>
                                    <td>
                                        <?php 
                                            if (isset($order['Product'])){
                                                $img_path = "";

                                                if( $order['Product']['img_mode'] == 'out' ){
                                                    $img_path = $order['Product']['CD_IMG'];
                                                }else{
                                                    if( $order['Product']['CD_IMG'] ){
                                                        $img_path = '/data/comparion/'.$order['Product']['CD_IMG'];
                                                    }
                                                }
                                        ?>
                                            <div class="partner-match-card" onclick="onlyAD.prdView('<?= $order['Product']['CD_IDX'] ?>','info');">
                                                <img class="partner-match-thumb" src="<?=$img_path?>">
                                                <div class="partner-match-info">
                                                    <span class="partner-match-name"><?= $order['Product']['CD_NAME'] ?></span>
                                                    판매가 : <?= number_format($order['Product']['cd_sale_price']) ?> | 원가 : <?= number_format($order['Product']['cd_cost_price']) ?>
                                                </div>
                                            </div>
                                        <?php } else{ ?>
                                            <div class="text-center">
                                                매칭된 상품이 없습니다.
                                            </div>
                                        <?php } ?>

                                        <?php 
                                        /*
                                        if (isset($order['ProductPartner']) && !empty($order['ProductPartner']['supplier_prd_pk'])) { ?>
                                            <div class="partner-match-card" onclick="prdProviderQuick('<?= $order['ProductPartner']['idx'] ?>');">
                                                <img class="partner-match-thumb" src="<?= $order['ProductPartner']['supplier_img_src'] ?>">
                                                <div class="partner-match-info">
                                                    <span class="partner-match-name"><?= $order['ProductPartner']['name_p'] ?></span>
                                                    주문가 : <?= number_format($order['ProductPartner']['order_price']) ?> | 원가 : <?= number_format($order['ProductPartner']['cost_price']) ?></br>
                                                    <?php if ((int)($order['ProductPartner']['order_price'] ?? 0) <= 0 || (int)($order['ProductPartner']['cost_price'] ?? 0) <= 0) { ?>
                                                        <span style="color:#d9534f; font-weight:700;">경고: 주문가 또는 원가가 0원입니다.</span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } 
                                        */
                                        ?>
                                    </td>
                                    <?php
                                    $orderGoodsSnoRaw = trim((string)($order['orderGoodsSno'] ?? ''));
                                    $orderGoodsSno = null;
                                    if ($orderGoodsSnoRaw !== '' && preg_match('/^\d+$/', $orderGoodsSnoRaw)) {
                                        $orderGoodsSno = ltrim($orderGoodsSnoRaw, '0');
                                        if ($orderGoodsSno === '') {
                                            $orderGoodsSno = '0';
                                        }
                                    }
                                    $isSavedOrderGoods = $orderGoodsSno !== null && isset($savedOrderGoodsMap[$orderGoodsSno]);
                                    $purchaseStatus = $orderGoodsSno !== null
                                        ? trim((string)($purchaseStatusMap[$orderGoodsSno] ?? ''))
                                        : '';
                                    $purchaseOrderIdx = $orderGoodsSno !== null
                                        ? (int)($purchaseOrderIdxMap[$orderGoodsSno] ?? 0)
                                        : 0;
                                    ?>
                                    <td class="text-center">
                                        <?php if ($isSavedOrderGoods) { ?>
                                            <span class="order-save-label order-save-label-saved">저장됨</span>
                                        <?php } else { ?>
                                            <span class="order-save-label order-save-label-unsaved">미저장</span>
                                        <?php } ?>
                                    </td>

                                    <!-- 발주상태 -->
                                    <td class="text-center">
                                        <?= $purchaseStatus !== '' ? $purchaseStatus : ($isSavedOrderGoods ? '발주대기' : '-') ?>
                                        <?php if ($purchaseOrderIdx > 0) { ?>
                                            <br>발주서 번호 : <?= $purchaseOrderIdx ?>
                                            <br><button type="button" class="btnstyle1 btnstyle1-xs" onclick="window.open('/admin/order/purchase/detail?idx=<?= $purchaseOrderIdx ?>', '_blank');">발주서 확인</button>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <button type="button" class="btnstyle1 btnstyle1-xs" 

                                            data-order-no="<?= $order['orderNo'] ?>"
                                            data-order-date="<?= $order['regDt'] ?>"
                                            data-payment-dt="<?= $order['paymentDt'] ?>"
                                            data-mem-no="<?= $order['memNo'] ?>"
                                            data-mem-id="<?= $order['memId'] ?>"
                                            data-mem-nm="<?= $order['memNm'] ?>"
                                            data-mem-phone="<?= $order['cellPhone'] ?>"
                                            data-group-nm="<?= $order['groupNm'] ?>"
                                            data-receiver-name="<?= $order['receiverName'] ?>"
                                            data-receiver-phone="<?= $order['receiverCellPhone'] ?>"

                                            onclick="csCreate(this);">C/S 요청</button>
                                    </td>
                                    <td class="text-center">
                                        <?php $csRequestCount = (int)($csRequestCountMap[$order['orderNo']] ?? 0); ?>
                                        <?php if ($csRequestCount > 0) { ?>
                                            <?= $csRequestCount ?><br>
                                            <button type="button" class="btnstyle1 btnstyle1-xs" onclick="window.open('/admin/cs/cs_list?s_order_no=<?= urlencode((string)$order['orderNo']) ?>', '_blank');">C/S 확인</button>
                                        <?php } ?>
                                    </td>
                                    <?php
                                    $receiverName = trim((string)($order['receiverName'] ?? ''));
                                    $receiverPhone = trim((string)($order['receiverPhone'] ?? ''));
                                    $receiverCellPhone = trim((string)($order['receiverCellPhone'] ?? ''));
                                    $receiverZonecode = trim((string)($order['receiverZonecode'] ?? ''));
                                    $receiverAddress = trim((string)($order['receiverAddress'] ?? ''));
                                    $receiverAddressSub = trim((string)($order['receiverAddressSub'] ?? ''));
                                    $orderMemo = trim((string)($order['orderMemo'] ?? ''));
                                    ?>
                                    <td><span class="copy-cell-wrap"><span class="copy-target"><?= $receiverName ?></span><?php if ($receiverName !== '') { ?><button type="button" class="copy-btn" title="복사" aria-label="수령자명 복사" onclick="copyCellText(this)"><svg class="copy-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"></rect>
                                                        <path d="M5 15V7C5 5.9 5.9 5 7 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                    </svg></button><?php } ?></span></td>
                                    <td><span class="copy-cell-wrap"><span class="copy-target"><?= $receiverPhone ?></span><?php if ($receiverPhone !== '') { ?><button type="button" class="copy-btn" title="복사" aria-label="수령자 전화번호 복사" onclick="copyCellText(this)"><svg class="copy-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"></rect>
                                                        <path d="M5 15V7C5 5.9 5.9 5 7 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                    </svg></button><?php } ?></span></td>
                                    <td><span class="copy-cell-wrap"><span class="copy-target"><?= $receiverCellPhone ?></span><?php if ($receiverCellPhone !== '') { ?><button type="button" class="copy-btn" title="복사" aria-label="수령자 휴대폰번호 복사" onclick="copyCellText(this)"><svg class="copy-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"></rect>
                                                        <path d="M5 15V7C5 5.9 5.9 5 7 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                    </svg></button><?php } ?></span></td>
                                    <td><span class="copy-cell-wrap"><span class="copy-target"><?= $receiverZonecode ?></span><?php if ($receiverZonecode !== '') { ?><button type="button" class="copy-btn" title="복사" aria-label="우편번호 복사" onclick="copyCellText(this)"><svg class="copy-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"></rect>
                                                        <path d="M5 15V7C5 5.9 5.9 5 7 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                    </svg></button><?php } ?></span></td>
                                    <td><span class="copy-cell-wrap"><span class="copy-target"><?= $receiverAddress ?></span><?php if ($receiverAddress !== '') { ?><button type="button" class="copy-btn" title="복사" aria-label="주소 복사" onclick="copyCellText(this)"><svg class="copy-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"></rect>
                                                        <path d="M5 15V7C5 5.9 5.9 5 7 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                    </svg></button><?php } ?></span></td>
                                    <td><span class="copy-cell-wrap"><span class="copy-target"><?= $receiverAddressSub ?></span><?php if ($receiverAddressSub !== '') { ?><button type="button" class="copy-btn" title="복사" aria-label="상세주소 복사" onclick="copyCellText(this)"><svg class="copy-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"></rect>
                                                        <path d="M5 15V7C5 5.9 5.9 5 7 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                    </svg></button><?php } ?></span></td>
                                    <td><span class="copy-cell-wrap"><span class="copy-target"><?= $orderMemo ?></span><?php if ($orderMemo !== '') { ?><button type="button" class="copy-btn" title="복사" aria-label="주문메모 복사" onclick="copyCellText(this)"><svg class="copy-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"></rect>
                                                        <path d="M5 15V7C5 5.9 5.9 5 7 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                    </svg></button><?php } ?></span></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </ul>
            <ul>
                <div class="scroll-wrap">
                    <div class="p-10">
                        <ul>
                            선택 주문 : <span id="selected-order-count">0</span>건
                        </ul>
                        <ul>
                            발주처 : <span id="selected-supplier-name">-</span>
                        </ul>
                        <ul>
                            발주서명 : <input type="text" id="fileName" name="order_name" class="width-200" value="<?= date('Ymd') ?? '' ?>-">
                        </ul>
                        <ul class="m-t-5">
                            <button type="button" id="fileUploadBtn" class="btnstyle1 btnstyle1-primary ">신규발주서 생성</button>
                        </ul>
                    </div>
                </div>
            </ul>
        </div>

    </div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<div id="orderSyncLoadingModal" class="order-sync-loading-modal" aria-hidden="true">
    <div class="order-sync-loading-modal__box" role="alert" aria-live="assertive">
        <div class="order-sync-loading-modal__spinner"></div>
        <p class="order-sync-loading-modal__text">주문서를 api로 불러오고 저장중입니다. 잠시만 기다려주세요.</p>
    </div>
</div>

<script>
    function showOrderSyncLoadingModal() {
        var modal = document.getElementById('orderSyncLoadingModal');
        if (!modal) {
            return;
        }
        modal.classList.add('is-visible');
        modal.setAttribute('aria-hidden', 'false');
    }

    function copyTextWithFallback(text) {
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'fixed';
        textarea.style.top = '-9999px';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);

        textarea.focus();
        textarea.select();
        textarea.setSelectionRange(0, textarea.value.length);

        var copied = false;
        try {
            copied = document.execCommand('copy');
        } catch (e) {
            copied = false;
        }
        document.body.removeChild(textarea);
        return copied;
    }

    function setCopyButtonFeedback(button) {
        if (!button) return;
        button.classList.add('is-copied');
        button.title = '복사됨';
        setTimeout(function() {
            button.classList.remove('is-copied');
            button.title = '복사';
        }, 1200);
    }

    function copyCellText(button) {
        var textNode = button && button.parentNode ? button.parentNode.querySelector('.copy-target') : null;
        var text = textNode ? (textNode.textContent || '').trim() : '';
        if (text === '') {
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                setCopyButtonFeedback(button);
            }).catch(function() {
                if (copyTextWithFallback(text)) {
                    setCopyButtonFeedback(button);
                } else {
                    alert('복사에 실패했습니다.');
                }
            });
            return;
        }

        if (copyTextWithFallback(text)) {
            setCopyButtonFeedback(button);
        } else {
            alert('복사에 실패했습니다.');
        }
    }

    function getPurchaseCheckboxes() {
        return $('input[name="check_idx[]"]:not(:disabled)');
    }

    function getSelectedRows() {
        return getPurchaseCheckboxes().filter(':checked').closest('tr');
    }

    function getSelectedOrderGoodsSnos() {
        return getPurchaseCheckboxes()
            .filter(':checked')
            .map(function() {
                return String($(this).val() || '').trim();
            })
            .get()
            .filter(function(value) {
                return value !== '';
            });
    }

    function normalizeSupplierName(rawName) {
        var supplierName = $.trim(String(rawName || ''));
        return supplierName !== '' ? supplierName : '(공급사 미지정)';
    }

    function getRowSupplierName($row) {
        if (!$row || $row.length === 0) {
            return '';
        }
        return normalizeSupplierName($row.data('scm-name'));
    }

    function getSelectedSupplierName() {
        var supplierName = '';
        getSelectedRows().each(function() {
            var currentSupplier = getRowSupplierName($(this));
            if (currentSupplier === '') {
                return;
            }
            if (supplierName === '') {
                supplierName = currentSupplier;
            }
        });
        return supplierName;
    }

    function getTodayYmd() {
        var now = new Date();
        var year = now.getFullYear();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var day = String(now.getDate()).padStart(2, '0');
        return String(year) + month + day;
    }

    function buildAutoFileName(supplierName) {
        var today = getTodayYmd();
        var safeSupplier = $.trim(String(supplierName || ''));
        if (safeSupplier === '') {
            return today + '-';
        }
        return safeSupplier + '-' + today + '-';
    }

    function updateSelectedState() {
        var $checkboxes = getPurchaseCheckboxes();
        var selectedCount = 0;
        var selectedSupplier = '';

        $checkboxes.each(function() {
            var $row = $(this).closest('tr');
            if ($(this).is(':checked')) {
                selectedCount++;
                $row.addClass('is-selected-row');
                if (selectedSupplier === '') {
                    selectedSupplier = getRowSupplierName($row);
                }
            } else {
                $row.removeClass('is-selected-row');
            }
        });

        $('#selected-order-count').text(selectedCount);
        $('#selected-supplier-name').text(selectedSupplier !== '' ? selectedSupplier : '-');
        $('#fileName').val(buildAutoFileName(selectedSupplier));

        $('#selected-count').text(selectedCount);

        var allChecked = $checkboxes.length > 0 && selectedCount === $checkboxes.length;
        $('#purchase-check-all').prop('checked', allChecked);
    }

    function select_all(headerCheckbox) {
        var checked = $(headerCheckbox).is(':checked');
        var $checkboxes = getPurchaseCheckboxes();
        if (!checked) {
            $checkboxes.prop('checked', false);
            updateSelectedState();
            return;
        }

        var targetSupplier = getSelectedSupplierName();
        var skippedCount = 0;

        $checkboxes.each(function() {
            var $checkbox = $(this);
            var $row = $checkbox.closest('tr');
            var rowSupplier = getRowSupplierName($row);

            if (targetSupplier === '') {
                targetSupplier = rowSupplier;
            }

            if (rowSupplier !== targetSupplier) {
                $checkbox.prop('checked', false);
                skippedCount++;
                return;
            }

            $checkbox.prop('checked', true);
        });

        if (skippedCount > 0) {
            alert('같은 공급사 상품만 선택 가능합니다.');
        }

        updateSelectedState();
    }

    function validateOrderSheetReg() {
        var $selectedRows = getSelectedRows();
        if ($selectedRows.length === 0) {
            return true;
        }

        var supplierMap = {};
        var hasUnmatched = false;

        $selectedRows.each(function() {
            var scmName = $.trim(String($(this).data('scm-name') || ''));
            // 서버 플래그(data-is-matched)와 화면 상태가 어긋날 수 있어,
            // 실제 렌더링된 매칭 카드 존재 여부를 우선 기준으로 사용한다.
            var isMatched = $(this).find('.partner-match-card').length > 0;
            supplierMap[scmName] = true;

            if (!isMatched) {
                hasUnmatched = true;
                return false;
            }
        });

        if (Object.keys(supplierMap).length > 1) {
            alert('같은 공급사 상품만 발주서가 생성이 가능합니다');
            return false;
        }

        if (hasUnmatched) {
            alert('매칭되지 않은 상품이 포함되어 있습니다. 매칭우선 해주세요');
            return false;
        }

        return true;
    }

    function handleOrderSheetReg() {
        if (!validateOrderSheetReg()) {
            return false;
        }

        var selectedOrderGoodsSnos = getSelectedOrderGoodsSnos();
        if (selectedOrderGoodsSnos.length < 1) {
            alert('선택된 주문상품이 없습니다.');
            return false;
        }
        var orderName = $.trim(String($('#fileName').val() || ''));
        if (orderName === '') {
            alert('발주서명을 입력해 주세요.');
            $('#fileName').focus();
            return false;
        }

        var $button = $('#fileUploadBtn');
        $button.prop('disabled', true);

        $.ajax({
            url: '/admin/order/godo_order_purchase/create_sheet',
            type: 'POST',
            dataType: 'json',
            data: {
                order_goods_snos: selectedOrderGoodsSnos,
                order_name: orderName
            },
            success: function(res) {
                if (!res || res.success !== true) {
                    alert((res && res.message) ? res.message : '발주서 생성에 실패했습니다.');
                    return;
                }

                alert((res.message || '발주서가 생성되었습니다.') + '\n엑셀 다운로드를 시작합니다.');
                if (res.download_url) {
                    window.location.href = res.download_url;
                }
            },
            error: function(request) {
                alert((request && request.responseText) ? request.responseText : '발주서 생성 중 오류가 발생했습니다.');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });

        return false;
    }

    /**
     * C/S 요청
     * @param string $orderNo 주문번호
     * @param string $memNo 회원번호
     * @param string $memId 회원ID
     * @param string $memName 회원명
     * @param string $memPhone 회원전화
     * @param string $receiverName 수령자명
     * @param string $receiverPhone 수령자전화
     */
    function csCreate(button){

        var orderNo = $(button).data('order-no');
        var orderDate = $(button).data('order-date');
        var paymentDt = $(button).data('payment-dt');
        var memNo = $(button).data('mem-no');
        var memId = $(button).data('mem-id');
        var memName = $(button).data('mem-nm');
        var memPhone = $(button).data('mem-phone');
        var groupNm = $(button).data('group-nm');
        var receiverName = $(button).data('receiver-name');
        var receiverPhone = $(button).data('receiver-phone');

        var data = {
            mode: 'create',
            apiMode: 'none',
            category: '공급사주문',
            orderNo: orderNo,
            orderDate: orderDate,
            paymentDt: paymentDt,
            memNo: memNo,
            memId: memId,
            memName: memName,
            memPhone: memPhone,
            groupNm: groupNm,
            receiverName: receiverName,
            receiverPhone: receiverPhone
        };
        openDialog("/admin/cs/cs_create", data, "C/S 생성", "800px");

    }

    $(document).ready(function() {

        $('#search_btn').on('click', function() {
            if ($(this).prop('disabled')) {
                return;
            }

            $(this).prop('disabled', true);
            showOrderSyncLoadingModal();

            var mode = $('#mode').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            location.href = "/admin/order/godo_order_purchase_list?mode=" + mode + "&start_date=" + start_date + "&end_date=" + end_date;
        });

        // 상품보기 토글
        $(document).on('click', '.order-toggle', function() {
            var target = $(this).data('target');
            var $targetRow = $(target);
            if ($targetRow.length === 0) {
                return;
            }
            $targetRow.toggle();
            if ($targetRow.is(':visible')) {
                $(this).text('상품보기 ▲');
            } else {
                $(this).text('상품보기 ▼');
            }
        });

        $(document).on('change', 'input[name="check_idx[]"]', function() {
            var $checkbox = $(this);
            if ($checkbox.is(':checked')) {
                var selectedSupplier = '';
                getPurchaseCheckboxes().filter(':checked').not($checkbox).each(function() {
                    var supplier = getRowSupplierName($(this).closest('tr'));
                    if (supplier !== '') {
                        selectedSupplier = supplier;
                        return false;
                    }
                });
                var currentSupplier = getRowSupplierName($checkbox.closest('tr'));
                if (selectedSupplier !== '' && currentSupplier !== selectedSupplier) {
                    alert('같은 공급사 상품만 선택 가능합니다.');
                    $checkbox.prop('checked', false);
                }
            }
            updateSelectedState();
        });

        $('#fileUploadBtn').on('click', function() {
            handleOrderSheetReg();
        });

        updateSelectedState();

    });
</script>