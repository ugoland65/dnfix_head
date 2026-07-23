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
        align-items: center;
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

    .margin-grade-badge {
        display: inline-block;
        min-width: 22px;
        padding: 2px 8px;
        border-radius: 999px;
        color: #fff;
        font-weight: 700;
        line-height: 1.2;
    }



    .tap-order-goods-list-wrap{
        display:flex;
        height:35px;
        align-items:flex-end;
        gap:3px;
        padding:0 0;
        box-sizing:border-box;
    }

    .tap-order-goods-list-wrap > ul{
        display:flex;
        align-items:center;
        justify-content:center;
        min-width:96px;
        height:35px;
        padding:0 14px;
        margin:0;
        list-style:none;
        border:1px solid #c7d2e0;
        border-bottom:none;
        border-top-left-radius:8px;
        border-top-right-radius:8px;
        background:#eef2f7;
        color:#57606a;
        font-size:12px;
        font-weight:600;
        line-height:1;
        cursor:pointer;
        box-sizing:border-box;
        transition:background-color .15s ease, color .15s ease, border-color .15s ease;
    }

    .tap-order-goods-list-wrap > ul:hover{
        background:#e7eef8;
        color:#334155;
    }

    .tap-order-goods-list-wrap > ul.active{
        background:#ffffff;
        color:#1d4ed8;
        border-color:#8fb1ff;
        font-weight:700;
        position:relative;
        top:1px;
    }

    .table-st1 tbody tr.supplier-group-title-row td{
        background:#eef4ff;
        color:#1e3a8a;
        font-weight:700;
        border-top:2px solid #b9ceff;
    }

    .table-st1 tbody tr.supplier-group-subtotal-row td{
        background:#f8fbff;
        color:#1f2937;
        font-weight:700;
        text-align:left;
        border-bottom:1px solid #d9e4ff;
    }

    .tap-order-goods-list-wrap + .scroll-wrap{
        height:calc(100% - 35px);
        border:1px solid #d0d7de;
        border-top:none;
        border-bottom-left-radius:8px;
        border-bottom-right-radius:8px;
        background:#fff;
    }

</style>

<div id="contents_head">
    <h1>고도몰 주문 (위탁상품)</h1>
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
                <div class="tap-order-goods-list-wrap" id="order-goods-sort-tabs">
                    <ul data-sort-view="default">주문일시순</ul>
                    <ul class="active" data-sort-view="supplier">공급사별 주문정렬 보기</ul>
                </div>
                <div class="scroll-wrap">

                    <table class="table-st1">
                        <thead>
                            <tr>
                                <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                                <th class="list-idx">No</th>
                                <th class="">주문번호</th>
                                <th class="">주문일</th>
                                <th class="">결제일</th>
                                <th class="">공급사</th>
                                <th class="">주문상품</th>
                                <th class="">주문상품명</th>
                                <th class="">주문<br>수량</th>
                                <th class="">매칭상품</th>

                                <th class="">공급사링크</th>
                                <th class="">마진율</th>
                                <th class="">검수상태</th>
                                <th class="">저장상태</th>
                                <th class="">발주상태</th>

                                <th class="">연동데이터</th>
                                
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
                            $mobeOrderDataMap = $mobeOrderDataMap ?? [];
                            $hasValidDateValue = static function ($value) {
                                $text = trim((string)($value ?? ''));
                                if ($text === '') {
                                    return false;
                                }
                                if (in_array(strtolower($text), ['null', 'undefined', 'none', 'n/a', '-'], true)) {
                                    return false;
                                }
                                return preg_match('/^0{4}-0{2}-0{2}(?:[\sT].*)?$/', $text) !== 1;
                            };
                            $no = 0;
                            foreach ($orderList['data'] as $order) {
                                if (!is_array($order) || !isset($order['orderNo'])) {
                                    continue;
                                }
                                $productPartner = is_array($order['ProductPartner'] ?? null) ? $order['ProductPartner'] : [];
                                $scmNameRaw = trim((string)($order['scmName'] ?? ''));
                                $scmSubNameRaw = '';
                                if ($scmNameRaw === '모브') {
                                    $scmSubNameRaw = trim((string)($productPartner['supplier_2nd_name'] ?? ''));
                                }
                                $orderPriceRaw = (float)($productPartner['order_price'] ?? 0);
                                $salePrice = (float)($order['goodsPrice'] ?? 0);
                                $marginPer = $salePrice > 0 ? (($salePrice - $orderPriceRaw) / $salePrice) * 100 : 0;
                                $marginRateText = rtrim(rtrim(number_format($marginPer, 2, '.', ''), '0'), '.');
                                $marginGrade = '';
                                $marginGradeColor = '';
                                if ($marginPer > 39) {
                                    $marginGrade = 'A';
                                    $marginGradeColor = '#28a745';
                                } elseif ($marginPer >= 35) {
                                    $marginGrade = 'B';
                                    $marginGradeColor = '#20c997';
                                } elseif ($marginPer >= 30) {
                                    $marginGrade = 'C';
                                    $marginGradeColor = '#17a2b8';
                                } elseif ($marginPer >= 25) {
                                    $marginGrade = 'D';
                                    $marginGradeColor = '#0dcaf0';
                                } elseif ($marginPer >= 20) {
                                    $marginGrade = 'E';
                                    $marginGradeColor = '#ffc107';
                                } elseif ($marginPer >= 15) {
                                    $marginGrade = 'F';
                                    $marginGradeColor = '#fd7e14';
                                } elseif ($marginPer >= 10) {
                                    $marginGrade = 'G';
                                    $marginGradeColor = '#dc3545';
                                } elseif ($marginPer >= 5) {
                                    $marginGrade = 'H';
                                    $marginGradeColor = '#d63384';
                                } elseif ($marginPer > 0) {
                                    $marginGrade = 'I';
                                    $marginGradeColor = '#6c757d';
                                }
                                $supplierPrdPk = trim((string)($productPartner['supplier_prd_pk'] ?? ''));
                                $productPartnerIdx = (int)($productPartner['idx'] ?? 0);
                                $isMatchedProviderProduct = $productPartnerIdx > 0 && $supplierPrdPk !== '';
                                $dataInspectMessages = [];
                                if ($isMatchedProviderProduct) {
                                    if (!$hasValidDateValue($productPartner['detail_crawler_date'] ?? null)) {
                                        $dataInspectMessages[] = '공급사 크롤링 안됨';
                                    }
                                    if (!$hasValidDateValue($productPartner['godo_loaded_at'] ?? null)) {
                                        $dataInspectMessages[] = '고도몰 로드 안됨';
                                    }
                                }
                                $no++;
                            ?>
                                <tr
                                    data-scm-name="<?= htmlspecialchars($scmNameRaw, ENT_QUOTES, 'UTF-8') ?>"
                                    data-scm-sub-name="<?= htmlspecialchars($scmSubNameRaw, ENT_QUOTES, 'UTF-8') ?>"
                                    data-order-price="<?= htmlspecialchars((string)$orderPriceRaw, ENT_QUOTES, 'UTF-8') ?>"
                                    data-payment-dt="<?= htmlspecialchars((string)($order['paymentDt'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-order-goods-sno="<?= htmlspecialchars((string)($order['orderGoodsSno'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <td><input type="checkbox" name="check_idx[]" value="<?= $order['orderGoodsSno'] ?>"></td>
                                    <td class="text-center"><?= $no ?></td>
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
                                        <?php if ( $order['scmName'] == "모브" )  { ?>
                                            <p><?= htmlspecialchars((string)($order['ProductPartner']['supplier_2nd_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                        <?php } ?>
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

                                        <div class="m-t-3">
                                            판매가 : <b><?= number_format($order['goodsPrice']) ?></b>
                                        </div>

                                        <div class="m-t-3">
                                            <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall(<?= $order['goodsNo'] ?>);">쑈당몰 상품보기</button>
                                            <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin(<?= $order['goodsNo'] ?>);">관리자 상품보기</button>
                                        </div>
                                    </td>

                                    <td class="text-center"><?= $order['goodsCnt'] ?></td>
                                    <td>
                                        <?php if ($isMatchedProviderProduct) { ?>
                                            <div class="partner-match-card" onclick="prdProviderQuick('<?= $order['ProductPartner']['idx'] ?>');" 
                                                data-prd-idx="<?= htmlspecialchars((string)$productPartnerIdx, ENT_QUOTES, 'UTF-8') ?>"
                                                data-supplier-site="<?= htmlspecialchars((string)($productPartner['supplier_site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                data-supplier-prd-pk="<?= htmlspecialchars($supplierPrdPk, ENT_QUOTES, 'UTF-8') ?>"
                                                data-godo-goods-no="<?= htmlspecialchars((string)($order['goodsNo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                            >
                                                <img class="partner-match-thumb" src="<?= $productPartner['supplier_img_src'] ?>">
                                                <div class="partner-match-info">
                                                    <p><?= $productPartner['supplier_status'] ?></p>
                                                    <span class="partner-match-name"><?= $productPartner['name_p'] ?></span>
                                                    주문가 : <b><?= number_format($productPartner['order_price']) ?></b> | 원가 : <?= number_format($productPartner['cost_price']) ?></br>
                                                    <?php if ((int)($productPartner['order_price'] ?? 0) <= 0 || (int)($productPartner['cost_price'] ?? 0) <= 0) { ?>
                                                        <span style="color:#d9534f; font-weight:700;">경고: 주문가 또는 원가가 0원입니다.</span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <span style="color:#dc3545; font-weight:bold;">매칭 우선 해주세요</span>
                                        <?php } ?>
                                    </td>

                                    <!-- 공급사링크 -->
                                    <td>
                                        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="goSupplierProduct('<?= $order['ProductPartner']['supplier_site'] ?>', '<?= $order['ProductPartner']['supplier_prd_pk'] ?>');">공급사링크</button>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($isMatchedProviderProduct) { ?>
                                            <?php if ($marginGrade !== '') { ?>
                                                <div><span class="margin-grade-badge" style="background-color:<?= $marginGradeColor ?>;"><?= $marginGrade ?></span></div>
                                                <div class="m-t-3"><?= $marginRateText ?>%</div>
                                            <?php } else { ?>
                                                0%
                                            <?php } ?>
                                        <?php } else { ?>
                                            -
                                        <?php } ?>
                                    </td>
                                    <td class="text-center data-inspection-status" data-prd-idx="<?= htmlspecialchars((string)$productPartnerIdx, ENT_QUOTES, 'UTF-8') ?>">
                                        <?php if (!$isMatchedProviderProduct) { ?>
                                            -
                                        <?php } elseif (!empty($dataInspectMessages)) { ?>
                                            <span style="color:#dc3545; font-weight:bold;"><?= implode('<br>', $dataInspectMessages) ?></span>
                                        <?php } else { ?>
                                            <span style="color:#198754; font-weight:bold;">정상</span>
                                        <?php } ?>
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
                                    $purchaseStatus = $isSavedOrderGoods
                                        ? trim((string)($purchaseStatusMap[$orderGoodsSno] ?? ''))
                                        : '';
                                    $mobeOrderData = $isSavedOrderGoods && is_array($mobeOrderDataMap[$orderGoodsSno] ?? null)
                                        ? $mobeOrderDataMap[$orderGoodsSno]
                                        : [];
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
                                        <?= $purchaseStatus !== '' ? htmlspecialchars($purchaseStatus, ENT_QUOTES, 'UTF-8') : '-' ?>
                                    </td>
                                    
                                    <td class="text-left" style="font-size:11px; line-height:1.5;">
                                        <?php if (empty($mobeOrderData)) { ?>
                                            -
                                        <?php } else { ?>
                                            <div>주문번호: <?= htmlspecialchars((string)($mobeOrderData['order_number'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></div>
                                            <div>주문일: <?= htmlspecialchars((string)($mobeOrderData['ordered_at'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></div>
                                            <div>결제: <?= htmlspecialchars((string)($mobeOrderData['payment_method'] ?? '-'), ENT_QUOTES, 'UTF-8') ?> / <?= number_format((float)($mobeOrderData['payment_total'] ?? 0)) ?>원</div>
                                            <div>배송: <?= htmlspecialchars((string)($mobeOrderData['shipping_status'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></div>
                                            <div>배송사/송장: <?= htmlspecialchars((string)($mobeOrderData['carrier_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?> / <?= htmlspecialchars((string)($mobeOrderData['tracking_number'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></div>
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
                <div class="scroll-wrap p-10">

                    <div>
                        모브 남은 예치금 : <span id="mob-pay-balance">0</span>
                        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="getMobPayBalance();">예치금 조회</button>
                    </div>

                    <div class="m-t-5">
                        <button type="button" id="inspect-all-products-btn" class="btnstyle1 btnstyle1-sm">STEP 1 : 전체 상품 검수</button>
                    </div>

                    <div class="m-t-5">
                        <button type="button" id="sync-mobe-orders-btn" class="btnstyle1 btnstyle1-sm">STEP 2 : 모브 구매내역 데이터 갱신</button>
                    </div>

                    <div class="m-t-5">
                        <button type="button" id="match-mobe-orders-btn" class="btnstyle1 btnstyle1-sm">STEP 3 : 모브 구매내역 매칭하기</button>
                    </div>

                </div>
            </ul>
        </div>

    </div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script>
    function getMobPayBalance() {
        var $button = $('button[onclick="getMobPayBalance();"]');
        $button.prop('disabled', true).text('조회 중...');

        $.ajax({
            url: '/admin/order/mob_pay_balance',
            type: 'POST',
            dataType: 'json'
        }).done(function(response) {
            if (!(response && response.success)) {
                alert((response && response.message) || '모브 예치금 조회에 실패했습니다.');
                return;
            }
            $('#mob-pay-balance').text(Number(response.available_deposit || 0).toLocaleString('ko-KR') + '원');
        }).fail(function(xhr) {
            var response = xhr.responseJSON || {};
            alert(response.message || '모브 예치금 조회에 실패했습니다.');
        }).always(function() {
            $button.prop('disabled', false).text('예치금 조회');
        });
    }

    function syncMobeOrders() {
        var $button = $('#sync-mobe-orders-btn');
        $button.prop('disabled', true).text('모브 구매내역 갱신 중...');

        $.ajax({
            url: '/admin/order/mobe_orders/sync',
            type: 'POST',
            dataType: 'json',
            data: {
                max_pages: 2,
                refresh_recent_days: 30
            }
        }).done(function(response) {
            if (!(response && response.success)) {
                alert((response && response.message) || '모브 구매내역 데이터 갱신에 실패했습니다.');
                return;
            }
            alert(response.message || '모브 구매내역 데이터 갱신이 완료되었습니다.');
        }).fail(function(xhr) {
            var response = xhr.responseJSON || {};
            alert(response.message || '모브 구매내역 데이터 갱신에 실패했습니다.');
        }).always(function() {
            $button.prop('disabled', false).text('STEP 2 : 모브 구매내역 데이터 갱신');
        });
    }

    function matchMobeOrders() {
        var $button = $('#match-mobe-orders-btn');
        var orderGoodsSnos = [];
        var usedOrderGoodsSnoMap = {};
        var orderedAtStart = '';
        $('.table-st1 tbody').find('tr').each(function() {
            var $row = $(this);
            var paymentDateText = $.trim(String($row.data('payment-dt') || ''));
            var paymentDate = paymentDateText.slice(0, 10);
            if (/^\d{4}-\d{2}-\d{2}$/.test(paymentDate) && (orderedAtStart === '' || paymentDate < orderedAtStart)) {
                orderedAtStart = paymentDate;
            }
            if (!$row.find('.order-save-label-saved').length) {
                return;
            }
            var orderGoodsSno = $.trim(String($row.data('order-goods-sno') || ''));
            if (orderGoodsSno && !usedOrderGoodsSnoMap[orderGoodsSno]) {
                usedOrderGoodsSnoMap[orderGoodsSno] = true;
                orderGoodsSnos.push(orderGoodsSno);
            }
        });
        if (!orderGoodsSnos.length) {
            alert('저장된 주문상품이 없습니다.');
            return;
        }
        if (!orderedAtStart) {
            alert('조회 목록에서 결제일을 찾을 수 없습니다.');
            return;
        }

        $button.prop('disabled', true).text('모브 구매내역 매칭 중...');

        $.ajax({
            url: '/admin/order/mobe_orders/match',
            type: 'POST',
            dataType: 'json',
            data: {
                ordered_at_start: orderedAtStart,
                order_goods_snos: orderGoodsSnos
            }
        }).done(function(response) {
            if (!(response && response.success)) {
                alert((response && response.message) || '모브 구매내역 매칭에 실패했습니다.');
                return;
            }

            var result = response.data || {};
            alert(
                (response.message || '모브 구매내역 매칭이 완료되었습니다.')
                + '\n매칭: ' + Number(result.matched_count || 0) + '건'
                + '\n공급사배송완료 변경: ' + Number(result.shipping_completed_count || 0) + '건'
                + '\n미매칭: ' + Number(result.unmatched_count || 0) + '건'
            );
            location.reload();
        }).fail(function(xhr) {
            var response = xhr.responseJSON || {};
            alert(response.message || '모브 구매내역 매칭에 실패했습니다.');
        }).always(function() {
            $button.prop('disabled', false).text('STEP 3 : 모브 구매내역 매칭하기');
        });
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
        var $orderTableBody = $('.table-st1 tbody');
        var originalRows = $orderTableBody.find('tr').toArray();
        var tableColumnCount = $('.table-st1 thead th').length || 20;

        function collectInspectionTargets($scope) {
            var targets = [];
            var usedPrdIdxMap = {};

            $scope.find('.partner-match-card').each(function() {
                var $card = $(this);
                var prdIdx = $.trim(String($card.data('prd-idx') || ''));
                var supplierPrdPk = $.trim(String($card.data('supplier-prd-pk') || ''));
                var godoGoodsNo = $.trim(String($card.data('godo-goods-no') || ''));

                // 같은 위탁상품이 여러 주문에 포함된 경우 한 번만 갱신한다.
                if (!prdIdx || !supplierPrdPk || !godoGoodsNo || usedPrdIdxMap[prdIdx]) {
                    return;
                }

                usedPrdIdxMap[prdIdx] = true;
                targets.push({
                    prd_idx: prdIdx,
                    supplier_prd_pk: supplierPrdPk,
                    godo_goods_no: godoGoodsNo
                });
            });

            return targets;
        }

        function requestSupplierProductInspection(target) {
            return $.ajax({
                url: '/admin/provider_product/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'update_supplier_product_detail',
                    prd_idx: target.prd_idx,
                    supplier_prd_pk: target.supplier_prd_pk
                }
            }).then(function(response) {
                if (response && response.status === 'success') {
                    return response;
                }
                return $.Deferred().reject((response && response.message) || '공급사 상품 검수 실패').promise();
            });
        }

        function requestGodoGoodsInspection(target) {
            return $.ajax({
                url: '/router/loadGodoGoodsInfo/',
                type: 'POST',
                dataType: 'json',
                data: {
                    prd_idx: target.prd_idx,
                    godo_goodsNo: target.godo_goods_no
                }
            }).then(function(response) {
                if (response && response.status === 'success') {
                    return response;
                }
                return $.Deferred().reject((response && response.message) || '고도몰 상품 정보 갱신 실패').promise();
            });
        }

        function markInspectionPassed(prdIdx) {
            $('.data-inspection-status').filter(function() {
                return String($(this).data('prd-idx') || '') === String(prdIdx || '');
            }).html('<span style="color:#198754; font-weight:bold;">정상</span>');
        }

        function runProductInspection(targets, $button, buttonText) {
            if (!targets.length) {
                alert('검수할 매칭 위탁상품이 없습니다.');
                return;
            }

            var index = 0;
            var successCount = 0;
            var failCount = 0;
            $button.prop('disabled', true);

            var runNext = function() {
                if (index >= targets.length) {
                    $button.prop('disabled', false).text(buttonText);
                    alert('상품 검수 완료 (성공 ' + successCount + '건, 실패 ' + failCount + '건)');
                    return;
                }

                var target = targets[index++];
                $button.text('검수 중... ' + index + '/' + targets.length);

                // 할인상품 검수와 동일하게 크롤링 실패 후에도 고도몰 적재를 시도한다.
                requestSupplierProductInspection(target)
                    .done(function() {
                        requestGodoGoodsInspection(target)
                            .done(function() {
                                successCount++;
                                markInspectionPassed(target.prd_idx);
                            })
                            .fail(function() {
                                failCount++;
                            })
                            .always(function() {
                                setTimeout(runNext, 200);
                            });
                    })
                    .fail(function() {
                        failCount++;
                        requestGodoGoodsInspection(target).always(function() {
                            setTimeout(runNext, 200);
                        });
                    });
            };

            runNext();
        }

        function normalizeGroupLabel(rawText, fallbackText) {
            var value = $.trim(String(rawText || ''));
            return value !== '' ? value : fallbackText;
        }

        function getGroupInfo($row) {
            var scmName = normalizeGroupLabel($row.data('scm-name'), '공급사 미지정');
            var scmSubName = normalizeGroupLabel($row.data('scm-sub-name'), '');
            var groupLabel = scmName;
            if (scmName === '모브') {
                scmSubName = normalizeGroupLabel(scmSubName, '(2차 공급사 미지정)');
                groupLabel = scmName + ' / ' + scmSubName;
            }
            return {
                key: scmName === '모브' ? (scmName + '||' + scmSubName) : scmName,
                label: groupLabel
            };
        }

        function renderDefaultRows() {
            $orderTableBody.empty();
            $.each(originalRows, function(_, row) {
                $orderTableBody.append(row);
            });
        }

        function renderSupplierGroupedRows() {
            var groups = {};

            $.each(originalRows, function(_, row) {
                var $row = $(row);
                var groupInfo = getGroupInfo($row);
                var orderPrice = parseFloat($row.data('order-price')) || 0;
                if (!groups[groupInfo.key]) {
                    groups[groupInfo.key] = {
                        label: groupInfo.label,
                        rows: [],
                        subtotal: 0
                    };
                }
                groups[groupInfo.key].rows.push(row);
                groups[groupInfo.key].subtotal += orderPrice;
            });

            var sortedKeys = Object.keys(groups).sort(function(a, b) {
                return groups[a].label.localeCompare(groups[b].label, 'ko');
            });

            $orderTableBody.empty();
            $.each(sortedKeys, function(_, key) {
                var group = groups[key];
                var $titleRow = $('<tr class="supplier-group-title-row"><td colspan="' + tableColumnCount + '"></td></tr>');
                $titleRow.find('td').text(group.label);
                $orderTableBody.append($titleRow);

                $.each(group.rows, function(_, row) {
                    $orderTableBody.append(row);
                });

                var subtotalText = '주문가 : ' + group.subtotal.toLocaleString('ko-KR') + ' 원';
                var $subtotalRow = $('<tr class="supplier-group-subtotal-row"><td colspan="' + tableColumnCount + '"></td></tr>');
                $subtotalRow.find('td')
                    .append(document.createTextNode(subtotalText + ' '))
                    .append($('<button type="button" class="btnstyle1 btnstyle1-xs inspect-supplier-group-btn">이 공급사 상품 검수</button>'));
                $orderTableBody.append($subtotalRow);
            });
        }

        $('#order-goods-sort-tabs').on('click', 'ul', function() {
            var $tab = $(this);
            var viewMode = String($tab.data('sort-view') || 'default');

            $('#order-goods-sort-tabs > ul').removeClass('active');
            $tab.addClass('active');

            if (viewMode === 'supplier') {
                renderSupplierGroupedRows();
                return;
            }
            renderDefaultRows();
        });

        renderSupplierGroupedRows();

        $('#inspect-all-products-btn').on('click', function() {
            runProductInspection(
                collectInspectionTargets($orderTableBody),
                $(this),
                '전체 상품 검수'
            );
        });

        $('#sync-mobe-orders-btn').on('click', function() {
            syncMobeOrders();
        });

        $('#match-mobe-orders-btn').on('click', function() {
            matchMobeOrders();
        });

        $orderTableBody.on('click', '.inspect-supplier-group-btn', function() {
            var $button = $(this);
            var $groupRows = $button.closest('tr').prevUntil('.supplier-group-title-row').filter(function() {
                return !$(this).hasClass('supplier-group-subtotal-row');
            });
            runProductInspection(
                collectInspectionTargets($groupRows),
                $button,
                '이 공급사 상품 검수'
            );
        });

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