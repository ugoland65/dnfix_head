<style type="text/css">
    .layout-style1 {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        box-sizing: border-box;

        >ul {

            width: 350px;
            height: 100%;
            padding: 0;

            .layout-style1-section-top {
                height: 30px;
            }

            .layout-style1-section {
                height: calc(100% - 30px);
                border: 1px solid #ccc;
                background-color: #fff;
            }

        }

        >ul:first-child {
            flex: 1;
        }

    }

    .order_goods_list {

        display: none;

        .table-st1 {
            border-top: 1px solid #b4b4b4;
            border-left: 1px solid #b4b4b4;
            border-bottom: 1px solid #b4b4b4;

            thead tr {
                position: static;
            }

            tfoot>tr {
                position: static;
            }
        }
    }



    .order-handle-label {
        display: block;
        margin-right: 6px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.4;
    }

    .order-handle-label-refund {
        background: #fff4e5;
        color: #b25a00;
        border: 1px solid #ffd59a;
    }

    .order-handle-label-approved {
        background: #edf9f0;
        color: #1f7a35;
        border: 1px solid #b8e7c2;
    }

    .order-handle-label-rejected {
        background: #fff1f1;
        color: #b42318;
        border: 1px solid #f4c2c0;
    }

    .copy-cell-wrap {
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    .copy-btn {
        border: 1px solid #d0d7de;
        background: #f2f4f7;
        color: #57606a;
        border-radius: 6px;
        width: 20px;
        height: 20px;
        padding: 0;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .copy-btn:hover {
        background: #e9edf2;
    }

    .copy-btn.is-copied {
        border-color: #16a34a;
        background: #16a34a;
        color: #ffffff;
    }

    .copy-btn.is-copied .copy-icon {
        display: none;
    }

    .copy-btn.is-copied::before {
        content: '✓';
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
    }

    .copy-btn.is-copied::after {
        content: '복사됨';
        position: absolute;
        top: -22px;
        left: 50%;
        transform: translateX(-50%);
        background: #111827;
        color: #ffffff;
        font-size: 10px;
        line-height: 1;
        padding: 3px 6px;
        border-radius: 4px;
        white-space: nowrap;
        z-index: 2;
    }

    .copy-btn .copy-icon {
        width: 16px;
        height: 16px;
        display: block;
        color: currentColor;
    }

    .partner-match-card {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        width: 250px;
        padding: 5px 6px;
        border: 1px solid #e3e7ee;
        border-radius: 8px;
        background: #f5f7fa;
        cursor: pointer;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .partner-match-card:hover {
        background: #eaf1ff;
        border-color: #9db7ea;
    }

    .partner-match-thumb {
        width: 40px;
        height: 40px;
        border-radius: 4px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .partner-match-info {
        line-height: 1.5;
        color: #1f2937;
        min-width: 0;
        flex: 1;
    }

    .partner-match-name {
        display: block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<div id="contents_head">
    <h1>고도몰 공급사 주문 가져오기</h1>
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
</div>

<div id="contents_body">
    <div id="contents_body_wrap">

        <div class="layout-style1">
            <ul>
                <div class="scroll-wrap">

                    <table class="table-st1">
                        <thead>
                            <tr>
                                <th class="">No</th>
                                <th class="">주문번호</th>
                                <th class="">주문일</th>
                                <th class="">결제일</th>
                                <th class="">공급사</th>
                                <th class="">주문상품</th>
                                <th class="">주문상품명</th>
                                <th class="">주문수량</th>
                                <th class="">매칭상품</th>
                                <th class="">C/S 요청</th>
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
                            $no = 0;
                            foreach ($orderList['data'] as $order) {
                                if (!is_array($order) || !isset($order['orderNo'])) {
                                    continue;
                                }
                                $no++;
                            ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td>
                                        <?php if ($order['userHandleFl'] == 'r') { ?>
                                            <span class="order-handle-label order-handle-label-refund">환불요청중</span>
                                        <?php } elseif ($order['userHandleFl'] == 'y') { ?>
                                            <span class="order-handle-label order-handle-label-approved">승인</span>
                                        <?php } elseif ($order['userHandleFl'] == 'n') { ?>
                                            <span class="order-handle-label order-handle-label-rejected">거절</span>
                                        <?php } ?>
                                        <a href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= $order['orderNo'] ?>" target="_blank"><?= $order['orderNo'] ?></a>
                                    </td>
                                    <td><?= date('y.m.d H:i', strtotime($order['regDt'])) ?></td>
                                    <td><?= date('y.m.d H:i', strtotime($order['paymentDt'])) ?></td>
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
                                        <?php if (isset($order['ProductPartner']) && !empty($order['ProductPartner']['supplier_prd_pk'])) { ?>
                                            <div class="partner-match-card" onclick="goSupplierProduct('<?= $order['ProductPartner']['supplier_site'] ?>', '<?= $order['ProductPartner']['supplier_prd_pk'] ?>');">
                                                <img class="partner-match-thumb" src="<?= $order['ProductPartner']['supplier_img_src'] ?>">
                                                <div class="partner-match-info">
                                                    <span class="partner-match-name"><?= $order['ProductPartner']['name_p'] ?></span>
                                                    주문가 : <?= number_format($order['ProductPartner']['order_price']) ?> | 원가 : <?= number_format($order['ProductPartner']['cost_price']) ?></br>
                                                </div>
                                            </div>
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

                </div>
            </ul>
        </div>

    </div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script>

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
            var mode = $('#mode').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            location.href = "/admin/order/godo_order_goods_list?mode=" + mode + "&start_date=" + start_date + "&end_date=" + end_date;
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

    });
</script>