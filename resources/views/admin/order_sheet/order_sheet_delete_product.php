<style type="text/css">
    .number-point {
        color: #ff0000;
    }

    .unit-price-sum {
        font-size: 14px;
        font-weight: 600;
        color: #021aff;
    }

    .notice-box {
        text-align: center;
        font-size: 10px;
    }

    .notice-box i {
        font-size: 16px;
    }

    .group-state {
        display: inline-block;
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .group-state.normal {
        background-color: #eee;
        border: 1px solid #ddd;
    }

    .group-state.ing {
        background-color: #95f4ff;
        border: 1px solid #0ed1e8;
    }

    .group-state.end {
        background-color: #ffcbcb;
        border: 1px solid #f88080;
    }

    .qty-control-wrap {
        display: flex;
        align-items: stretch;
        gap: 2px;
    }

    .qty-control-wrap .qty-input {
        width: 100%;
    }

    .qty-step-wrap {
        display: flex;
        flex-direction: column;
        gap: 1px;
        width: 16px;
    }

    .qty-step-btn {
        width: 16px;
        height: 13px;
        line-height: 11px;
        border: 1px solid #bfc7d9;
        background: #f5f7fb;
        color: #2f3b59;
        font-size: 9px;
        cursor: pointer;
        padding: 0;
        user-select: none;
    }

    .price-input-wrap{
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .unit-price-text{
        font-size: 14px;
    }
    .krw-price{
        color: #777;
        font-size: 11px;
    }

    .en-title{
        color: #777;
        font-size: 10px !important;
    }

    .en-title-currency-code{
        color: #777;
        font-size: 10px !important;
    }

    .weight-sum-wrap{
        ul{
            font-size: 11px;
        }
    }
</style>
<div class="ospl-wrap">
    <div class="ospl-top">
        <ul>
            <div>
                Group : <?= $oop_idx ?? '' ?> | <b><?= $orderGroupProduct['oop_code'] ?? '' ?></b> <button type="button" id="" class="btnstyle1 btnstyle1-sm m-r-20" onclick="orderSheetForm.groupView('<?= $oop_idx ?? '' ?>')">폼그룹 상품관리</button>

                <?php if ($form_view == "hidden") { ?>
                    <button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="orderSheetDetail.prdListShow('<?= $oo_idx ?? '' ?>','<?= $oop_idx ?? '' ?>','show');">전체 상품보기</button>
                <?php } else { ?>
                    <button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="orderSheetDetail.prdListShow('<?= $oo_idx ?? '' ?>','<?= $oop_idx ?? '' ?>','hidden');">주문 상품만보기</button>
                <?php } ?>

                <div id="group_state" class="m-l-20 group-state normal">state : 보기중</div>
                <!-- 
				<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="orderSheet.lastInfoReset(this, '<?= $oop_idx ?>')">정보갱신</button>
				<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-xs m-l-15" onclick="thisCateDel();"">이분류 상품 전부 삭제</button>
				<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="unitAction('false')">선택 결품</button>
				<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="unitFalseReturn();">선택 실패복귀</button>
				-->
            </div>

            <div class="m-t-10">

                <span>
                    <?php
                    $_group_weight_raw = (float)str_replace(',', '', (string)($orderGroup['weight'] ?? 0));
                    if ($_group_weight_raw >= 1000) {
                        $_group_weight_text = number_format($_group_weight_raw / 1000, 2) . 'kg';
                    } else {
                        $_group_weight_text = number_format($_group_weight_raw, ($_group_weight_raw == floor($_group_weight_raw)) ? 0 : 2) . 'g';
                    }
                    ?>
                    총 : <b><?= $orderGroupProduct['oop_data_total_count'] ?? 0 ?></b> |
                    선택 : <b id="group_body_sum_goods_<?= $oop_idx ?? '' ?>" class="number-point"><?= $orderGroup['item'] ?? 0 ?></b>
                    (
                    총수량 : <b id="group_body_sum_qty_<?= $oop_idx ?? '' ?>" class="number-point"><?= number_format($orderGroup['qty'] ?? 0) ?></b>
                    
                    <?php
                        if( $orderSheet['oo_import'] !== '국내' ) {
                    ?>
                        , 총무게 : <b id="group_body_sum_weight_<?= $oop_idx ?? '' ?>" class="number-point"><?= $_group_weight_text ?></b>
                    <?php } ?>

                    )
                </span>

                <?php
                if( $orderSheet['oo_import'] !== '국내' ) {
                    $_prdCurrency = trim((string)($orderSheet['oo_prd_currency'] ?? ''));
                    $_exchangeRate = (float)($orderSheet['oo_prd_exchange_rate'] ?? 0);
                    $_needExchangeWarning = ($_prdCurrency !== '원' && $_exchangeRate <= 0);
                ?>
                    <span class="m-l-20">
                        상품통화 : <b><?= $orderSheet['oo_prd_currency'] ?? '' ?></b> (<?= $orderSheet['oo_prd_currency_code'] ?? '' ?>)
                    </span>
                    <span class="m-l-6">
                        적용환율 : <b><?= $orderSheet['oo_prd_exchange_rate'] ?? 0 ?></b>
                    </span>
                    <?php if ($_needExchangeWarning) { ?>
                        <span class="m-l-5" style="color:#ff0000; font-weight:600;">
                            ( ※ 수입주문은 적용환율을 적용해주세요. )
                        </span>
                    <?php } ?>

                    <?php if( $orderSheet['is_currency_mismatch'] ) { ?>
                        <span class="m-l-5"> / </span>
                        <span class="m-l-5">
                            결제 통화 : <b><?= $orderSheet['oo_sum_currency'] ?? '' ?></b> (<?= $orderSheet['oo_sum_currency_code'] ?? '' ?>)
                        </span>
                        <span class="m-l-6">
                            적용환율 : <b><?= $orderSheet['oo_sum_exchange_rate'] ?? 0 ?></b>
                        </span>
                    <?php } ?>

                    <?php if( $orderSheet['oo_prd_to_pay_exchange_rate'] > 0 ) { ?>
                        <span class="m-l-5"> / </span>
                        <span class="m-l-5">
                            통화 환산 환율 : <b><?= $orderSheet['oo_prd_to_pay_exchange_rate'] ?? 0 ?></b>
                        </span>
                    <?php } ?>

                <?php } ?>

            </div>

        </ul>
        <ul class="btn">
            <button type="button" id="" class="btnstyle1  btnstyle1-sm" onclick="orderSheetDetail.PrdListReload()">
                새로고침
            </button>
            <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="orderSheetDetailPrd.groupSave('<?= $oo_idx ?? '' ?>','<?= $oop_idx ?? '' ?>','<?= $form_view ?? '' ?>');">
                그룹상품<br>저장
            </button>
        </ul>
    </div>
</div>

<div class="ospl-prd-wrap">
    <?//=dump($orderSheet) ?>
    <?//=dump($orderGroup) ?>
    <?//=dump($orderGroupProduct) ?>
    <table class="table-st1">
        <thead>
            <tr>
                <th style="width:40px;"></th>
                <th style="width:60px;">IDX<br>재고코드</th>
                <th style="width:86px;">주문코드</th>
                <th>이미지</th>
                <th>상품명</th>
                <th>상품처리</th>
                <th style="width:100px;">메모</th>
                <th>주문수량</th>
                <th>
                    주문가격
                    <?php
                        if( $orderSheet['oo_import'] !== '국내' ) {
                    ?>
                    <br><span class="en-title">Unit Price</span>
                    <br><span class="en-title-currency-code">( <?= $orderSheet['oo_prd_currency_code'] ?? '' ?> )</span>
                    <?php } ?>
                </th>
                <th>
                    합가격
                    <?php
                        if( $orderSheet['oo_import'] !== '국내' ) {
                    ?>
                    <br><span class="en-title">Total Amount</span>
                    <br><span class="en-title-currency-code">( <?= $orderSheet['oo_prd_currency_code'] ?? '' ?> )</span>
                    <?php } ?>
                </th>

                <?php if( $orderSheet['is_currency_mismatch'] ) { ?>
                    <th>
                        주문가격
                        <br><span class="en-title">Unit Price</span>
                        <br><span class="en-title-currency-code">( <?= $orderSheet['oo_sum_currency_code'] ?? '' ?> )</span>
                    </th>
                    <th>
                        합가격
                        <br><span class="en-title">Total Amount</span>
                        <br><span class="en-title-currency-code">( <?= $orderSheet['oo_sum_currency_code'] ?? '' ?> )</span>
                    </th>
                <?php } ?>

                <?php if( $orderSheet['oo_import'] !== '국내' ) { ?>
                    <th>수입신고가</th>
                <?php } ?>

                <th>현재고</th>
                <th>최근 입/출고</th>
                <th>비고</th>
                <th>무게</th>
                <th>CBM</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $total_qty = 0;
            $total_sum_price = 0;
            $total_sum_price_cents = 0;
            $total_pay_sum_price = 0;
            $total_pay_sum_price_cents = 0;

            foreach ($orderGroupProduct['oop_data'] as $item) {

                $total_qty += $item['selpd']['qty'] ?? 0;
                $item_sum_price = (float)($item['selpd']['sum_price'] ?? 0);
                $item_sum_price_cents = (int)round($item_sum_price * 100);
                $total_sum_price_cents += $item_sum_price_cents;
                $total_sum_price = $total_sum_price_cents / 100;
                $item_pay_sum_price = (float)($item['product']['pay_unit_price'] ?? 0) * (float)($item['selpd']['qty'] ?? 0);
                $item_pay_sum_price_cents = (int)round($item_pay_sum_price * 100);
                $total_pay_sum_price_cents += $item_pay_sum_price_cents;
                $total_pay_sum_price = $total_pay_sum_price_cents / 100;

                $img_path = '';
                if ($item['product']['img_mode'] == 'out') {
                    if (!empty($item['product']['CD_IMG'])) {
                        $img_path = $item['product']['CD_IMG'];
                    }
                } else {
                    if (!empty($item['product']['CD_IMG'])) {
                        $img_path = '/data/comparion/' . $item['product']['CD_IMG'];
                    }
                }

                $_tr_class = "";

                //주문서 수량이 있는 경우 색상 변경
                if (!empty($item['selpd']) && $item['selpd']['qty'] > 0) {
                    //$_tr_color = "#ffcbcb";
                    $_tr_class = "red";
                } else {
                    if (($item['product']['ps_stock'] ?? 0) == 0) {
                        //$_tr_color = "#eee";
                        $_tr_class = "status_clx";
                    } else {
                        //$_tr_color = "#fff";
                    }
                }

                //결품 상태일경우
                if (isset($item['is_false']) && $item['is_false'] == "ok") {
                    //$_tr_color = "#adadad";
                    $_tr_class = "status_clx2";
                }

            ?>
                <tr id="tr_<?= $item['idx'] ?? '' ?>" class="<?= $_tr_class ?? '' ?>">

                    <!-- 체크 -->
                    <td id="checkbox_td_<?= $item['idx'] ?? '' ?>">
                        <input type="checkbox" name="key_check[]" id="checkbox_<?= $item['idx'] ?? '' ?>" class="checkSelect" value="<?= $item['idx'] ?? '' ?>" 
                        <?php if (!empty($item['selpd']) && $item['selpd']['qty'] > 0) { ?>
                            checked
                        <?php } ?>
                        style="<?php if (isset($item['is_false']) && $item['is_false'] == "ok") { ?>display:none;<?php } ?>">
                    </td>

                    <!-- 상품 고유번호 -->
                    <td class="text-center">
                        <?= $item['idx'] ?? '' ?>
                        <?php if (!empty($item['stockidx'])) { ?>
                            <br><b style="color:#2525fa;"><?= $item['stockidx'] ?? '' ?></b>
                        <?php } ?>
                    </td>

                    <!-- 상품 코드 -->
                    <td class="text-center">
                        <b><?= $item['product']['CD_CODE2'] ?? '' ?></b>
                        <?php if (!empty($item['product']['CD_CODE3'])) { ?><br><?= $item['product']['CD_CODE3'] ?><?php } ?>

                        <?php if (($orderSheet['oo_state'] ?? 0) > 1 && ($item['selpd']['qty'] ?? 0) > 1) { ?>
                            <div class="m-t-5">
                                <?php if (isset($item['is_false']) && $item['is_false'] == "ok") { ?>
                                    <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="orderSheetDetailPrd.unitFalse(this,'<?= $item['idx'] ?>', '<?= $oo_idx ?? '' ?>', '<?= $oop_idx ?? '' ?>','on','<?= $form_view ?? '' ?>')">결품복원</button>
                                <?php } else { ?>
                                    <button type="button" class="btnstyle1 btnstyle1-xs" onclick="orderSheetDetailPrd.unitFalse(this,'<?= $item['idx'] ?>', '<?= $oo_idx ?? '' ?>', '<?= $oop_idx ?? '' ?>','out','<?= $form_view ?? '' ?>')">결품처리</button>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </td>

                    <!-- 상품 이미지 -->
                    <td style="width:70px;">
                        <img src="<?= $img_path ?>" style="height:60px; border:1px solid #eee !important; cursor:pointer;" onclick="onlyAD.prdView('<?= $item['idx'] ?? '' ?>','info');">
                    </td>

                    <!-- 상품 명 -->
                    <td class="text-left">

                        <!-- 상품 바코드 -->
                        <?php if (!empty($item['product']['CD_CODE'])) { ?><div><?= $item['product']['CD_CODE'] ?></div><?php } ?>
                        <div class="p-t-5 p-b-5" onclick="onlyAD.prdView('<?= $item['idx'] ?? '' ?>','info');" style="cursor:pointer;">
                            <?php /*
                            <button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?= $item['idx'] ?? '' ?>','info');">보기</button> 
                            <?php */ ?>
                            <b><?= $item['product']['CD_NAME'] ?? '' ?></b>
                            <?php if (!empty($item['om'])) { ?><br><span style="color:#ff0000; display:inline-block; margin-top:3px; font-size:11px;"><?= $item['om'] ?? '' ?></span><?php } ?>
                        </div>

                        <?php if (!empty($prd_data['cd_memo3'])) { ?>
                            <div class="p-b-5"><span style="color:#ff0000; font-size:13px;"><?= $prd_data['cd_memo3'] ?></span></div>
                        <?php } ?>

                    </td>

                    <!-- 상품처리 -->
                    <td>
                        <div>
                            <?php if ($item['product']['is_discontinued'] == 0) { ?>
                                <button type="button" id="soldOut_out_<?= $item['idx'] ?? '' ?>" class="btnstyle1 btnstyle1-xs" onclick="orderSheetDetailPrd.soldOut(this, '<?= $oo_idx ?? '' ?>', '<?= $oop_idx ?? '' ?>', '<?= $item['product']['CD_IDX'] ?? '' ?>','out')">단종처리</button>
                            <?php } else { ?>
                                <button type="button" id="soldOut_on_<?= $item['idx'] ?? '' ?>" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetDetailPrd.soldOut(this, '<?= $oo_idx ?? '' ?>', '<?= $oop_idx ?? '' ?>', '<?= $item['product']['CD_IDX'] ?? '' ?>','on')">단종해제</button>
                            <?php } ?>
                        </div>
                    </td>

                    <!-- 주문메모 -->
                    <td style="min-width:150px; padding:0 !important; ">
                        <textarea name="memo" id="memo_<?= $item['idx'] ?? '' ?>" class="memo-auto-fit" style="width:100%; background-color:transparent; border:none !important; resize: none; padding:5px; margin:0 !important; box-sizing:border-box; color:#ff0000;"><?= $item['selpd']['memo'] ?? '' ?></textarea>
                    </td>

                    <!-- 주문수량 -->
                    <?php
                    $_is_false_row = (isset($item['is_false']) && $item['is_false'] == "ok");
                    if (isset($item['is_false']) && $item['is_false'] == "ok") {
                        $_color = "#999";
                    } else {
                        $_color = "#021aff";
                    }
                    ?>
                    <td style="width:80px;">
                        <div class="qty-control-wrap">
                            <input type='text' name='cd_code2' id="unit_qty_<?= $item['idx'] ?? '' ?>" class="qty-input" data-is-false="<?= $_is_false_row ? '1' : '0' ?>" style="width:40px; font-size:15px; font-weight:bold; color:<?= $_color ?>;" value="<?= $item['selpd']['qty'] ?? 0 ?>" onkeyUP="orderSheetDetail.qtyGogo('<?= $item['idx'] ?? '' ?>', '<?= $oop_idx ?? '' ?>');" <?= $_is_false_row ? 'disabled' : '' ?>>
                            <div class="qty-step-wrap">
                                <button type="button" class="qty-step-btn" data-step="1" data-idx="<?= $item['idx'] ?? '' ?>" data-oopidx="<?= $oop_idx ?? '' ?>" <?= $_is_false_row ? 'disabled' : '' ?>>▲</button>
                                <button type="button" class="qty-step-btn" data-step="-1" data-idx="<?= $item['idx'] ?? '' ?>" data-oopidx="<?= $oop_idx ?? '' ?>" <?= $_is_false_row ? 'disabled' : '' ?>>▼</button>
                            </div>
                        </div>
                    </td>

                    <!-- 상품가격 -->
                    <td class="text-right" id="unit_price_td_<?= $item['idx'] ?? '' ?>" data-price="<?= $item['product']['unit_price'] ?? 0 ?>" style="width:100px;">

                        <?php if ($item['product']['unit_price'] == 0) { ?>
                            <div class="price-input-wrap">
                                <ul>
                                    <input type='text' name='' id="unit_price_<?= $item['idx'] ?? '' ?>" style="width:72px;" value="" onkeyUP=" orderSheetDetail.qtyGogo('<?= $item['idx'] ?? '' ?>', '<?= $oop_idx ?? '' ?>');">
                                </ul>
                                <ul>
                                    <button type="button" id="" class="btnstyle1 btnstyle1-xs m-l-2" onclick="orderSheetDetailPrd.newPrice(this, '<?= $item['idx'] ?? '' ?>', '<?= $orderGroupProduct['oop_code'] ?? '' ?>', '<?= $oop_idx ?? '' ?>')">
                                        주문가격 등록
                                    </button>
                                </ul>
                            <div>
                        <?php } else { ?>
                            <input type='hidden' name='' id="unit_price_<?= $item['idx'] ?? '' ?>" value="<?= $item['product']['unit_price'] ?? 0 ?>">
                            <span>
                                <?php
                                $_unit_price = (float)($item['product']['unit_price'] ?? 0);
                                $_unit_price_text = number_format($_unit_price, ($_unit_price == floor($_unit_price)) ? 0 : 2);
                                ?>
                                <a href="#" class="editable-cd-price editable-click"
                                    data-pk="<?= $item['product']['unit_price'] ?? 0 ?>"
                                    data-cdidx="<?= $item['idx'] ?? '' ?>"
                                    data-oopcode="<?= $orderGroupProduct['oop_code'] ?? '' ?>"
                                    data-oopidx="<?= $_oop_idx ?? '' ?>"
                                    data-exchangerate="<?= (float)($orderSheet['oo_prd_exchange_rate'] ?? 0) ?>"
                                    data-currency="<?= $orderSheet['oo_prd_currency'] ?? '' ?>">
                                    <b class="unit-price-text"><?= $_unit_price_text ?></b>
                                </a>
                            </span>

                            <?php
                            $_this_won_price = "";
                            if (($orderSheet['oo_prd_exchange_rate'] ?? 0) > 0) {
                                $_exchange_rate = (float)($orderSheet['oo_prd_exchange_rate'] ?? 0);
                                $_currency = (string)($orderSheet['oo_prd_currency'] ?? '');
                                $_won_price_raw = (float)($item['product']['unit_price'] ?? 0) * $_exchange_rate;

                                // 일본 환율은 보통 100엔 기준(예: 944.97)으로 저장되므로 원화 환산 시 100으로 나눠 보정
                                if ($_currency === '엔' || strtoupper($_currency) === 'JPY') {
                                    $_won_price_raw = $_won_price_raw / 100;
                                }

                                $_this_won_price = number_format(round($_won_price_raw), 0);
                            ?>
                                <div class="m-t-5 krw-price">₩ <span id="unit_won_price_<?= $item['idx'] ?? '' ?>"><?= $_this_won_price ?></span></div>
                            <?php } ?>

                        <?php } ?>
                    </td>

                    <!-- 상품 합가격 -->
                    <td class="text-right">
                        <?php
                        $_sum_price_val = (float)($item['selpd']['sum_price'] ?? 0);
                        $_sum_price_text = number_format($_sum_price_val, ($_sum_price_val == floor($_sum_price_val)) ? 0 : 2);
                        $_sum_won_price_text = '';
                        if (($orderSheet['oo_prd_exchange_rate'] ?? 0) > 0) {
                            $_sum_exchange_rate = (float)($orderSheet['oo_prd_exchange_rate'] ?? 0);
                            $_sum_currency = (string)($orderSheet['oo_prd_currency'] ?? '');
                            $_sum_won_price_raw = $_sum_price_val * $_sum_exchange_rate;
                            if ($_sum_currency === '엔' || strtoupper($_sum_currency) === 'JPY') {
                                $_sum_won_price_raw = $_sum_won_price_raw / 100;
                            }
                            $_sum_won_price_text = number_format(round($_sum_won_price_raw), 0);
                        }
                        ?>
                        <input type="hidden" name="" id="unit_price_sum_<?= $item['idx'] ?? '' ?>" class="unit-price-sum-data" value="<?= $_sum_price_text ?>">

                        <span id="order_qty_sum_<?= $item['idx'] ?? '' ?>" class="unit-price-sum"><?= $_sum_price_text ?></span>
                        <?php if ($_sum_won_price_text !== '') { ?>
                            <div class="m-t-3 krw-price">₩ <span id="order_qty_sum_won_<?= $item['idx'] ?? '' ?>" data-exchangerate="<?= $_sum_exchange_rate ?>" data-currency="<?= $_sum_currency ?>"><?= $_sum_won_price_text ?></span></div>
                        <?php } ?>

                    </td>

                    <?php if (!empty($orderSheet['is_currency_mismatch'])) { ?>
                        <td class="text-right" id="pay_unit_price_td_<?= $item['idx'] ?? '' ?>" data-price="<?= $item['product']['pay_unit_price'] ?? 0 ?>">

                            <?php if ($item['product']['pay_unit_price'] == 0) { ?>
                                <div class="price-input-wrap">
                                    <ul>
                                        <input type='text' name='' id="pay_unit_price_<?= $item['idx'] ?? '' ?>" style="width:72px;" value="" onkeyUP=" orderSheetDetail.qtyGogo('<?= $item['idx'] ?? '' ?>', '<?= $oop_idx ?? '' ?>');">
                                    </ul>
                                    <ul>
                                        <button type="button" id="" class="btnstyle1 btnstyle1-xs m-l-2" onclick="orderSheetDetailPrd.newPayPrice(this, '<?= $item['idx'] ?? '' ?>', '<?= $orderGroupProduct['oop_code'] ?? '' ?>', '<?= $oop_idx ?? '' ?>')">
                                            <?= $orderSheet['oo_sum_currency_code'] ?? '' ?> 가격등록
                                        </button>
                                    </ul>
                                <div>
                            <?php } else { ?>
                                <input type='hidden' name='' id="pay_unit_price_<?= $item['idx'] ?? '' ?>" value="<?= $item['product']['pay_unit_price'] ?? 0 ?>">
                                <span>
                                    <?php
                                    $_pay_unit_price = (float)($item['product']['pay_unit_price'] ?? 0);
                                    $_pay_unit_price_text = number_format($_pay_unit_price, ($_pay_unit_price == floor($_pay_unit_price)) ? 0 : 2);
                                    ?>
                                    <?php if ((float)($orderSheet['oo_prd_to_pay_exchange_rate'] ?? 0) > 0) { ?>
                                        <b class="unit-price-text"><?= $_pay_unit_price_text ?></b>
                                    <?php } else { ?>
                                        <a href="#" class="editable-cd-pay-price editable-click"
                                            data-pk="<?= $item['product']['pay_unit_price'] ?? 0 ?>"
                                            data-cdidx="<?= $item['idx'] ?? '' ?>"
                                            data-oopcode="<?= $orderGroupProduct['oop_code'] ?? '' ?>"
                                            data-oopidx="<?= $_oop_idx ?? '' ?>"
                                            data-exchangerate="<?= (float)($orderSheet['oo_sum_exchange_rate'] ?? 0) ?>"
                                            data-currency="<?= $orderSheet['oo_sum_currency_code'] ?? '' ?>"
                                            data-currencycode="<?= $orderSheet['oo_sum_currency_code'] ?? '' ?>">
                                            <b class="unit-price-text"><?= $_pay_unit_price_text ?></b>
                                        </a>
                                    <?php } ?>
                                </span>

                                <?php
                                $_this_won_price = "";
                                if (($orderSheet['oo_sum_exchange_rate'] ?? 0) > 0) {
                                    $_exchange_rate = (float)($orderSheet['oo_sum_exchange_rate'] ?? 0);
                                    $_currency = (string)($orderSheet['oo_sum_currency_code'] ?? '');
                                    $_won_price_raw = (float)($item['product']['pay_unit_price'] ?? 0) * $_exchange_rate;

                                    // 일본 환율은 보통 100엔 기준(예: 944.97)으로 저장되므로 원화 환산 시 100으로 나눠 보정
                                    if ($_currency === '엔' || strtoupper($_currency) === 'JPY') {
                                        $_won_price_raw = $_won_price_raw / 100;
                                    }

                                    $_this_won_price = number_format(round($_won_price_raw), 0);
                                ?>
                                    <div class="m-t-5 krw-price">₩ <span id="pay_unit_won_price_<?= $item['idx'] ?? '' ?>"><?= $_this_won_price ?></span></div>
                                <?php } ?>

                            <?php } ?>

                        </td>

                        <!-- 상품 합가격 -->
                        <td class="text-right">
                            <?php
                            $_pay_sum_price_val = (float)($item['product']['pay_unit_price'] ?? 0) * (float)($item['selpd']['qty'] ?? 0);
                            $_pay_sum_price_text = number_format($_pay_sum_price_val, ($_pay_sum_price_val == floor($_pay_sum_price_val)) ? 0 : 2);
                            $_pay_sum_won_price_text = '';
                            if (($orderSheet['oo_sum_exchange_rate'] ?? 0) > 0) {
                                $_pay_sum_exchange_rate = (float)($orderSheet['oo_sum_exchange_rate'] ?? 0);
                                $_pay_sum_currency = (string)($orderSheet['oo_sum_currency_code'] ?? '');
                                $_pay_sum_won_price_raw = $_pay_sum_price_val * $_pay_sum_exchange_rate;
                                if ($_pay_sum_currency === '엔' || strtoupper($_pay_sum_currency) === 'JPY') {
                                    $_pay_sum_won_price_raw = $_pay_sum_won_price_raw / 100;
                                }
                                $_pay_sum_won_price_text = number_format(round($_pay_sum_won_price_raw), 0);
                            }
                            ?>
                            <input type="hidden" name="" id="pay_unit_price_sum_<?= $item['idx'] ?? '' ?>" class="pay-unit-price-sum-data" value="<?= $_pay_sum_price_text ?>">

                            <span id="pay_order_qty_sum_<?= $item['idx'] ?? '' ?>" class="unit-price-sum"><?= $_pay_sum_price_text ?></span>
                            <?php if ($_pay_sum_won_price_text !== '') { ?>
                                <div class="m-t-3 krw-price">₩ <span id="pay_order_qty_sum_won_<?= $item['idx'] ?? '' ?>" data-exchangerate="<?= $_pay_sum_exchange_rate ?>" data-currency="<?= $_pay_sum_currency ?>"><?= $_pay_sum_won_price_text ?></span></div>
                            <?php } ?>

                        </td>
                    <?php } ?>

                    <?php if( $orderSheet['oo_import'] !== '국내' ) { ?>
                    <!-- 수입신고가 -->
					<td class="text-right" id="unit_iv_price_td_<?= $item['idx'] ?? '' ?>" data-price="<?=$item['product']['invoice_price'] ?? 0 ?>" style="width:70px;">

						<?php if ($item['product']['invoice_price'] == 0) { ?>
                            <div class="price-input-wrap">
                                <ul>
							        <input type='text' name='' id="unit_iv_price_<?= $item['idx'] ?? '' ?>" style="width:60px;" value="">
                                </ul>
                                <ul>
                                    <button type="button" id="" class="btnstyle1 btnstyle1-xs m-l-2" onclick="orderSheetDetailPrd.newInvoicePrice(this, '<?= $item['idx'] ?? '' ?>', '<?= $orderGroupProduct['oop_code'] ?? '' ?>', '<?= $oop_idx ?? '' ?>')">
                                        신고가 등록
                                    </button>
                                </ul>
                            <div>
                        <?php } else { ?>
							<input type='hidden' name='' id="unit_iv_price_<?= $item['idx'] ?? '' ?>" value="<?=$item['product']['invoice_price'] ?? 0 ?>">
							<span>
                                <?php
                                    $_invoice_price = (float)($item['product']['invoice_price'] ?? 0);
                                    $_invoice_price_text = number_format($_invoice_price, ($_invoice_price == floor($_invoice_price)) ? 0 : 2);
                                ?>
								<a href="#" class="editable-cd-iv-price editable-click"
									data-pk="<?=$item['product']['invoice_price'] ?? 0 ?>"
									data-cdidx="<?= $item['idx'] ?? '' ?>"
									data-oopcode="<?= $orderGroupProduct['oop_code'] ?? '' ?>"
									data-oopidx="<?= $_oop_idx ?? '' ?>">
									<b class="unit-iv-price-text"><?= $_invoice_price_text ?></b>
								</a>
							</span>
						<?php } ?>

					</td>
                    <?php } ?>

                    <!-- 상품재고 -->
                    <td class="text-center" style="width:30px;">
                        <b onclick="onlyAD.prdView('<?= $item['idx'] ?? '' ?>','stock');" style="cursor:pointer; <?php if (($item['product']['ps_stock'] ?? 0) == 0) echo "color:#aaa;"; ?>"><?= $item['product']['ps_stock'] ?? 0 ?></b>
                    </td>

                    <!--  -->
                    <td>
                        <?php if ($item['product']['ps_in_date']  && $item['product']['ps_in_date']  !== '0000-00-00 00:00:00') { ?>
                            <div>입고 : <?= date("y.m.d", strtotime($item['product']['ps_in_date'])) ?></div>
                        <?php } ?>
                        <?php if ($item['product']['ps_last_date'] && $item['product']['ps_last_date'] !== '0000-00-00 00:00:00') { ?>
                            <div>출고 : <?= date("y.m.d", strtotime($item['product']['ps_last_date'])) ?></div>
                        <?php } ?>
                    </td>

                    <!-- 마지막 입고일 -->
                    <td class="text-left" style="width:100px; font-size:11px;">
                        <?php
                        /*
                        $_ps_cafe24_sms_data = json_decode($prd_data['ps_cafe24_sms'] ?? '{}', true);
                        if (!is_array($_ps_cafe24_sms_data)) {
                            $_ps_cafe24_sms_data = [];
                        }
                        if (($_ps_cafe24_sms_data['count'] ?? 0) > 0) { ?>
                            <div style="background-color:#ffb5b5; padding:4px; margin-bottom:3px; border-radius:5px; border:1px solid #cf7979;">
                                <ul>입고알림 : <b><?= $_ps_cafe24_sms_data['count'] ?? 0 ?></b></ul>
                                <ul class="m-t-2" style="font-size:10px;"><?= !empty($_ps_cafe24_sms_data['date']) ? date("m.d H:i:s", strtotime($_ps_cafe24_sms_data['date'])) : '' ?></ul>
                            </div>
                        <?php } 
                        */
                        ?>
                        <div style="font-size:12px;"><?= $item['last'] ?? '' ?></div>
                    </td>

                    <!-- 무게 -->
                    <td class="text-center" style="width:55px;">

                        <span id="weight_<?= $item['idx'] ?? '' ?>" class="unit-weight <?php if ($_weight_mode == "2") echo "no-weight"; ?>" data-weight="<?= $item['product']['weight'] ?? 0 ?>">
                            <?php
                            if ($item['product']['weight'] > 0) {
                                if ($item['product']['weight'] > 1000) {
                                    $_this_weight = number_format($item['product']['weight'] / 1000, 2) . "kg";
                                } else {
                                    $_this_weight = number_format($item['product']['weight']) . "g";
                                }
                            ?>
                                <?= $_this_weight ?>
                            <?php } else { ?>
                                <div class="notice-box">
                                    <!-- <i class="fas fa-exclamation-circle"></i><br> -->무게정보 없습니다.
                                </div>
                            <?php } ?>
                        </span>

                        <!-- 중량 합계 -->
                         <?php if( $item['selpd']['qty'] > 0 ) { ?>
                            <div class="weight-sum-wrap m-t-5">
                                <ul>
                                    총수량(g) : <b class="weight-sum" id="weight_sum_<?= $item['idx'] ?? '' ?>"><?= $item['weight_sum'] ?? 0 ?></b>g
                                </ul>
                                <ul>
                                    총중량(kg) : <b class="weight-sum-kg" id="weight_sum_kg_<?= $item['idx'] ?? '' ?>"><?= $item['weight_sum_kg'] ?? 0 ?></b>kg
                                </ul>
                            </div>
                        <?php } ?>

                    </td>

                    <!-- CBM -->
                    <td>
                        <span id="cbm_<?= $item['idx'] ?? '' ?>" class="unit-cbm" data-cbm="<?= $item['product']['cbm'] ?? 0 ?>">
                            <?php if ($item['product']['cbm'] > 0) { ?>
                                cbm : <b><?= $item['product']['cbm'] ?></b>
                            <?php } else { ?>
                                <div class="notice-box">
                                    <!-- <i class="fas fa-exclamation-circle"></i><br> -->CBM 정보 없습니다.
                                </div>
                            <?php } ?>
                        </span>
                    </td>

                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">
                </th>
                <th class="text-center">
                    <b id="order_sheet_total_qty"><?= number_format($total_qty) ?></b>
                </th>
                <th class="text-right">
                </th>
                <th class="text-right" style="font-size:14px;">
                    <?php
                        $_total_sum_price_rounded = round((float)$orderGroupProduct['oop_data_total_sum_price'], 2);
                        $_total_sum_price_text = number_format($_total_sum_price_rounded, ($_total_sum_price_rounded == floor($_total_sum_price_rounded)) ? 0 : 2);
                        $_total_sum_won_price_text = '';
                        $_total_sum_exchange_rate = (float)($orderSheet['oo_prd_exchange_rate'] ?? 0);
                        $_total_sum_currency = (string)($orderSheet['oo_prd_currency'] ?? '');
                        if ($_total_sum_exchange_rate > 0) {
                            $_total_sum_won_price_raw = $_total_sum_price_rounded * $_total_sum_exchange_rate;
                            if ($_total_sum_currency === '엔' || strtoupper($_total_sum_currency) === 'JPY') {
                                $_total_sum_won_price_raw = $_total_sum_won_price_raw / 100;
                            }
                            $_total_sum_won_price_text = number_format(round($_total_sum_won_price_raw), 0);
                        }
                    ?>
                    <b id="order_sheet_total_sum_price"><?= $_total_sum_price_text ?></b>
                    <?php if (($orderSheet['oo_import'] ?? '') !== '국내') { ?>
                        <div class="m-t-3 krw-price">
                            ₩ <span id="order_sheet_total_sum_price_won" data-exchangerate="<?= $_total_sum_exchange_rate ?>" data-currency="<?= $_total_sum_currency ?>"><?= $_total_sum_won_price_text ?></span>
                        </div>
                    <?php } ?>
                </th>

                <?php if (!empty($orderSheet['is_currency_mismatch'])) { ?>
                <th class="text-right">
                </th>
                <th class="text-right">
                    <?php
                    $_total_pay_sum_price_rounded = round((float)$total_pay_sum_price, 2);
                    $_total_pay_sum_price_text = number_format($_total_pay_sum_price_rounded, ($_total_pay_sum_price_rounded == floor($_total_pay_sum_price_rounded)) ? 0 : 2);
                    $_total_pay_sum_won_price_text = '';
                    $_total_pay_sum_exchange_rate = (float)($orderSheet['oo_sum_exchange_rate'] ?? 0);
                    $_total_pay_sum_currency = (string)($orderSheet['oo_sum_currency_code'] ?? '');
                    if ($_total_pay_sum_exchange_rate > 0) {
                        $_total_pay_sum_won_price_raw = $_total_pay_sum_price_rounded * $_total_pay_sum_exchange_rate;
                        if ($_total_pay_sum_currency === '엔' || strtoupper($_total_pay_sum_currency) === 'JPY') {
                            $_total_pay_sum_won_price_raw = $_total_pay_sum_won_price_raw / 100;
                        }
                        $_total_pay_sum_won_price_text = number_format(round($_total_pay_sum_won_price_raw), 0);
                    }
                    ?>
                    <b id="order_sheet_pay_total_sum_price"><?= $_total_pay_sum_price_text ?></b>
                    <?php if (($orderSheet['oo_import'] ?? '') !== '국내') { ?>
                        <div class="m-t-3 krw-price">
                            ₩ <span id="order_sheet_pay_total_sum_price_won" data-exchangerate="<?= $_total_pay_sum_exchange_rate ?>" data-currency="<?= $_total_pay_sum_currency ?>"><?= $_total_pay_sum_won_price_text ?></span>
                        </div>
                    <?php } ?>
                </th>
                <?php } ?>
                
                <?php if( $orderSheet['oo_import'] !== '국내' ) { ?>
                <th class="text-right">
                </th>
                <?php } ?>
                
                <th class="text-right">
                </th>
                <th class="text-right">
                </th>
                <th class="text-right">
                </th>
                <th class="text-right">
                    <b id="order_sheet_total_weight_sum_kg"><?= $orderGroupProduct['oop_data_total_weight_sum_kg'] ?? 0 ?></b>kg
                </th>
                <th colspan="100%">
                </th>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    var orderSheetDetailPrd = function() {

        /**
         * 주문서 상품 결룸 처리
         */
        function unitFalse(obj, pidx, oo_idx, oop_idx, mode, form_view) 
        {

            var pidx_memo = $("#memo_" + pidx).val();

            $(obj).attr('disabled', true);
            $.ajax({
                url: "/admin/order/sheet/action",
                data: {
                    "action_mode": "orderSheetProductUnitFalse",
                    "pidx": pidx,
                    "oo_idx": oo_idx,
                    "oop_idx": oop_idx,
                    "unit_false_mode": mode,
                    "pidx_memo": pidx_memo
                },
                type: "POST",
                dataType: "json",
                success: function(res) {
                    if (res.success == true) {
                        var moveUrl = '/ad/order/order_sheet/' + oo_idx + '?oop_idx=' + oop_idx;
                        if (form_view !== undefined && form_view !== null && String(form_view).trim() !== '') {
                            moveUrl += '&form_view=' + encodeURIComponent(String(form_view).trim());
                        }
                        location.href = moveUrl;
                    } else {
                        showAlert("Error", res.msg, "alert2");
                        return false;
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    showAlert("Error", "에러", "alert2");
                    return false;
                },
                complete: function() {
                    $(obj).attr('disabled', false);
                }
            });


        }

        /**
         * 주문서 상품 단종처리
         */
        function soldOut(obj, oo_idx, oop_idx, pidx, mode) 
        {
            var isSoldOut = (mode == "out");
            var confirmTitle = isSoldOut ? "단종처리" : "단종해제";
            var confirmContent = isSoldOut ? "해당 상품을 단종 처리하시겠습니까?" : "해당 상품의 단종을 해제하시겠습니까?";
            var confirmBtnText = isSoldOut ? "단종처리" : "단종해제";

            confirmContent += "<br/>" + confirmBtnText + "시 상품 DB에도 " + confirmBtnText + "가 됩니다";
            
            dnConfirm(confirmTitle, confirmContent, function() {
                $(obj).attr('disabled', true);
                $.ajax({
                    url: "/admin/order/sheet/action",
                    data: {
                        "action_mode": "orderSheetProductSoldOut",
                        "oop_idx": oop_idx,
                        "soldoutmode": mode,
                        "pidx": pidx
                    },
                    type: "POST",
                    dataType: "json",
                    success: function(res) {
                        if (res.success == true) {
                            if (isSoldOut) {
                                showToast("단종 처리 완료되었습니다.", new Date().toLocaleTimeString());
                            } else {
                                showToast("단종 해제 처리 완료되었습니다.", new Date().toLocaleTimeString());
                            }
                            location.href = '/ad/order/order_sheet/' + oo_idx + '?oop_idx=' + oop_idx;
                        } else {
                            showAlert("Error", res.msg, "alert2");
                            return false;
                        }
                    },
                    error: function(request, status, error) {
                        console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                        showAlert("Error", "에러", "alert2");
                        return false;
                    },
                    complete: function() {
                        $(obj).attr('disabled', false);
                    }
                });
            }, null, "default", confirmBtnText, "btn-red", "취소");

        }

        /**
         * 가격 형식 변환
         */
        function formatPriceForView(value) 
        {

            // 문자열/콤마 입력값을 숫자로 정규화한다. (예: "1,234.5" -> 1234.5)
            var numeric = parseFloat(String(value).replace(/,/g, ''));
            // 숫자로 해석할 수 없는 값은 0으로 표시한다.
            if (!isFinite(numeric)) {
                return '0';
            }
            // 부동소수점 오차를 줄이기 위해 EPSILON 보정 후 소수점 2자리 반올림
            var rounded = Math.round((numeric + Number.EPSILON) * 100) / 100;
            // 항상 소수점 2자리 문자열로 만든 뒤 표시 규칙에 맞게 가공한다.
            var fixed = rounded.toFixed(2);
            var parts = fixed.split('.');
            // 정수부에 천 단위 콤마를 적용한다.
            var intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            // 소수부가 .00 이면 숨기고 정수부만 반환한다. (예: 1,234.00 -> 1,234)
            if (parts[1] === '00') {
                return intPart;
            }
            // .00 이 아닐 때만 소수부를 포함해 반환한다. (예: 1,234.50)
            return intPart + '.' + parts[1];
        }

        function formatWonPriceForView(value) 
        {
            var numeric = parseFloat(String(value).replace(/,/g, ''));
            if (!isFinite(numeric)) {
                return '0';
            }
            var rounded = Math.round(numeric);
            return String(rounded).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        /**
         * 상품 가격 생성
         */
        function newPrice(obj, cd_idx, oop_code, oop_idx) 
        {
            
            var idx = String(cd_idx || '').trim();
            var oopCode = String(oop_code || '').trim();
            var rawPrice = String($("#unit_price_" + idx).val() || '');
            var nprice = rawPrice.replace(/,/g, '').trim();
            var exchangeRate = <?= (float)($orderSheet['oo_prd_exchange_rate'] ?? 0) ?>;
            var currency = "<?= addslashes((string)($orderSheet['oo_prd_currency'] ?? '')) ?>";

            if (idx === '' || oopCode === '') {
                showAlert("Error", "필수 값이 누락되었습니다.", "alert2");
                return false;
            }

            if (!nprice || !isFinite(Number(nprice)) || Number(nprice) <= 0) {
                $("#unit_price_" + idx).focus();
                showAlert("Error", "가격을 입력해주세요.", "alert2");
                return false;
            }

            $(obj).attr('disabled', true);
            $.ajax({
                url: "/admin/order/sheet/action",
                data: {
                    "action_mode": "orderSheetProductNewPrice",
                    "cd_idx": idx,
                    "oop_code": oopCode,
                    "price": nprice,
                    "reg_mode": "newprice"
                },
                type: "POST",
                dataType: "json",
                success: function(res) {
                    if (res.success == true) {

                        showToast("가격이 저장되었습니다.", new Date().toLocaleTimeString());

                        var savedPrice = (res.uprice !== undefined) ? res.uprice : nprice;
                        var html = "<input type='hidden' id='unit_price_" + idx + "' value='" + savedPrice + "'>" +
                            "<b>" + formatPriceForView(savedPrice) + "</b> ";
                        if (exchangeRate > 0) {
                            var wonPriceRaw = (parseFloat(savedPrice) || 0) * exchangeRate;
                            if (currency === '엔' || currency.toUpperCase() === 'JPY') {
                                wonPriceRaw = wonPriceRaw / 100;
                            }
                            html += "<div class='m-t-5'>₩ <span id='unit_won_price_" + idx + "'>" + formatWonPriceForView(wonPriceRaw) + "</span></div>";
                        }

                        $("#unit_price_td_" + idx).html(html).data("price", savedPrice);
                        orderSheetDetail.qtyGogo(idx, oop_idx);
                    } else {
                        showAlert("Error", res.msg, "alert2");
                        return false;
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    showAlert("Error", "에러", "alert2");
                    return false;
                },
                complete: function() {
                    $(obj).attr('disabled', false);
                }
            });
        }

        /**
         * 신고가 생성
         */
        function newInvoicePrice(obj, cd_idx, oop_code, oop_idx) 
        {
            var idx = String(cd_idx || '').trim();
            var oopCode = String(oop_code || '').trim();
            var rawPrice = String($("#unit_iv_price_" + idx).val() || '');
            var nprice = rawPrice.replace(/,/g, '').trim();

            if (idx === '' || oopCode === '') {
                showAlert("Error", "필수 값이 누락되었습니다.", "alert2");
                return false;
            }

            if (!nprice || !isFinite(Number(nprice)) || Number(nprice) <= 0) {
                $("#unit_iv_price_" + idx).focus();
                showAlert("Error", "신고가를 입력해주세요.", "alert2");
                return false;
            }

            $(obj).attr('disabled', true);
            $.ajax({
                url: "/admin/order/sheet/action",
                data: {
                    "action_mode": "orderSheetProductNewPrice",
                    "cd_idx": idx,
                    "oop_code": oopCode,
                    "price": nprice,
                    "reg_mode": "newinvoiceprice"
                },
                type: "POST",
                dataType: "json",
                success: function(res) {
                    if (res.success == true) {
                        showToast("신고가가 저장되었습니다.", new Date().toLocaleTimeString());

                        var savedPrice = (res.uprice !== undefined) ? res.uprice : nprice;
                        var html = "<input type='hidden' id='unit_iv_price_" + idx + "' value='" + savedPrice + "'>" +
                            "<b>" + formatPriceForView(savedPrice) + "</b> ";

                        $("#unit_iv_price_td_" + idx).html(html).data("price", savedPrice);
                        //orderSheetDetail.qtyGogo(idx, oop_idx);
                    } else {
                        showAlert("Error", res.msg, "alert2");
                        return false;
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    showAlert("Error", "에러", "alert2");
                    return false;
                },
                complete: function() {
                    $(obj).attr('disabled', false);
                }
            });
        }

        /**
         * 결제 통화 가격 생성
         */
        function newPayPrice(obj, cd_idx, oop_code, oop_idx)
        {

            var idx = String(cd_idx || '').trim();
            var oopCode = String(oop_code || '').trim();
            var currencyCode = "<?= addslashes((string)($orderSheet['oo_sum_currency_code'] ?? '')) ?>";
            var exchangeRate = <?= (float)($orderSheet['oo_sum_exchange_rate'] ?? 0) ?>;
            var currency = "<?= addslashes((string)($orderSheet['oo_sum_currency_code'] ?? '')) ?>";
            var isAutoPayUnitPrice = <?= ((float)($orderSheet['oo_prd_to_pay_exchange_rate'] ?? 0) > 0) ? 'true' : 'false' ?>;
            var rawPrice = String($("#pay_unit_price_" + idx).val() || '');
            var nprice = rawPrice.replace(/,/g, '').trim();

            if (idx === '' || oopCode === '' || currencyCode === '') {
                showAlert("Error", "필수 값이 누락되었습니다.", "alert2");
                return false;
            }

            if (!nprice || !isFinite(Number(nprice)) || Number(nprice) <= 0) {
                $("#pay_unit_price_" + idx).focus();
                showAlert("Error", "결제 상품가격을 입력해주세요.", "alert2");
                return false;
            }

            $(obj).attr('disabled', true);
            $.ajax({
                url: "/admin/order/sheet/action",
                data: {
                    "action_mode": "orderSheetProductNewPrice",
                    "cd_idx": idx,
                    "oop_code": oopCode,
                    "price": nprice,
                    "reg_mode": "newpayprice",
                    "currency_code": currencyCode
                },
                type: "POST",
                dataType: "json",
                success: function(res) {
                    if (res.success == true) {
                        showToast("결제 상품가격이 저장되었습니다.", new Date().toLocaleTimeString());

                        var savedPrice = (res.uprice !== undefined) ? res.uprice : nprice;
                        var html = "<input type='hidden' id='pay_unit_price_" + idx + "' value='" + savedPrice + "'>" + "<span>";
                        if (isAutoPayUnitPrice) {
                            html += "<b class='unit-price-text'>" + formatPriceForView(savedPrice) + "</b>";
                        } else {
                            html += "<a href='#' class='editable-cd-pay-price editable-click' " +
                                "data-pk='" + savedPrice + "' " +
                                "data-cdidx='" + idx + "' " +
                                "data-oopcode='" + oopCode + "' " +
                                "data-oopidx='" + oop_idx + "' " +
                                "data-exchangerate='" + exchangeRate + "' " +
                                "data-currency='" + currency + "' " +
                                "data-currencycode='" + currencyCode + "'>" +
                                "<b class='unit-price-text'>" + formatPriceForView(savedPrice) + "</b>" +
                                "</a>";
                        }
                        html += "</span>";
                        if (exchangeRate > 0) {
                            var wonPriceRaw = (parseFloat(savedPrice) || 0) * exchangeRate;
                            if (currency === '엔' || currency.toUpperCase() === 'JPY') {
                                wonPriceRaw = wonPriceRaw / 100;
                            }
                            html += "<div class='m-t-5 krw-price'>₩ <span id='pay_unit_won_price_" + idx + "'>" + formatWonPriceForView(wonPriceRaw) + "</span></div>";
                        }
                        $("#pay_unit_price_td_" + idx).html(html).data("price", savedPrice);
                        if (!isAutoPayUnitPrice) {
                            bindEditableCdPayPrice($("#pay_unit_price_td_" + idx + " .editable-cd-pay-price"));
                        }
                        orderSheetDetail.qtyGogo(idx, oop_idx);
                    } else {
                        showAlert("Error", res.msg, "alert2");
                        return false;
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    showAlert("Error", "에러", "alert2");
                    return false;
                },
                complete: function() {
                    $(obj).attr('disabled', false);
                }
            });
        }

        /**
         * 주문서 주문그룹 상품 저장
         */
        function groupSave(oo_idx, oop_idx, form_view) 
        {
            function toNumber(value, defaultValue) {
                var fallback = (defaultValue === undefined) ? 0 : defaultValue;
                if (value === null || value === undefined) {
                    return fallback;
                }
                var normalized = String(value).replace(/,/g, '').trim();
                if (normalized === '') {
                    return fallback;
                }
                var parsed = parseFloat(normalized);
                return isFinite(parsed) ? parsed : fallback;
            }

            var item = $(".checkSelect:checked").length;
            var send_idx = [];
            var send_price = [];
            var send_qty = [];
            var send_memo = [];

            var total_qty = 0;
            var total_price = 0;
            var total_weight = 0;
            var total_cbm = 0;

            $(".checkSelect:checked").each(function() {
                var checkbox_id = $(this).val();

                send_idx.push(checkbox_id);
                send_price.push($("#unit_price_" + checkbox_id).val());
                send_qty.push($("#unit_qty_" + checkbox_id).val());
                send_memo.push($("#memo_" + checkbox_id).val());

                var unitQtyRaw = $("#unit_qty_" + checkbox_id).val();
                var unitQty = toNumber(unitQtyRaw, 0);
                var plus_qty = (String(unitQtyRaw || '').trim() === '') ? 1 : unitQty;

                total_qty += unitQty;
                total_price += toNumber($("#unit_price_sum_" + checkbox_id).val(), 0);
                total_weight += toNumber($("#weight_" + checkbox_id).data('weight'), 0) * plus_qty;
                total_cbm += toNumber($("#cbm_" + checkbox_id).data('cbm'), 0) * plus_qty;
            });

            $.ajax({
                url: "/admin/order/sheet/save_group_product",
                data: {
                    "oo_idx": oo_idx,
                    "oop_idx": oop_idx,
                    "item": item,
                    "total_qty": total_qty,
                    "total_price": total_price,
                    "total_weight": total_weight,
                    "total_cbm": total_cbm,
                    "send_idx": send_idx,
                    "send_price": send_price,
                    "send_qty": send_qty,
                    "send_memo": send_memo
                },
                type: "POST",
                dataType: "json",
                success: function(res) {
                    if (res.success === true) {
                        showToast("설정이 저장되었습니다.", new Date().toLocaleTimeString());
                        if (window.orderSheetDetail && typeof window.orderSheetDetail.groupState === 'function') {
                            window.orderSheetDetail.groupState("end");
                        }
                        $("#group_side_sum_qty_" + oop_idx).data('value', total_qty);

                        $("#oprice_allsum").html(GC.comma(res.oo_sum_price ?? 0));
                        $("#oprice_sum_goods").html(GC.comma(res.oo_sum_goods ?? 0));
                        $("#oprice_sum_qty").html(GC.comma(res.oo_sum_qty ?? 0));
                        $("#oprice_sum_weight").html(GC.comma(res.oo_sum_weight ?? 0) + "g");
                        $("#oprice_sum_goods_" + oop_idx).html(GC.comma(res.group_sum_goods ?? 0));
                    } else {
                        showAlert("Error", (res.msg || res.message || "저장 중 오류가 발생했습니다."), "alert2");
                        return false;
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    showAlert("Error", "에러", "alert2");
                    return false;
                }
            });
        }

        return {
            unitFalse,
            soldOut,
            newPrice,
            newInvoicePrice,
            newPayPrice,
            groupSave,
        };

    }();

    (function() {

        function formatPriceForView(value) {
            var numeric = parseFloat(String(value).replace(/,/g, ''));
            if (!isFinite(numeric)) {
                return '0';
            }
            var rounded = Math.round((numeric + Number.EPSILON) * 100) / 100;
            var fixed = rounded.toFixed(2);
            var parts = fixed.split('.');
            var intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            if (parts[1] === '00') {
                return intPart;
            }
            return intPart + '.' + parts[1];
        }

        function formatWonPrice(value) {
            var numeric = parseFloat(String(value).replace(/,/g, ''));
            if (!isFinite(numeric)) {
                return '0';
            }
            var rounded = Math.round(numeric);
            return String(rounded).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        function fitMemoTextareas() {
            var textareas = document.querySelectorAll('.ospl-prd-wrap textarea.memo-auto-fit');
            var minHeight = 56;
            var maxHeight = 71;
            textareas.forEach(function(textarea) {
                var td = textarea.closest('td');
                if (!td) {
                    return;
                }
                var tdHeight = td.clientHeight;
                if (tdHeight > 0) {
                    var targetHeight = Math.max(minHeight, Math.min(maxHeight, tdHeight));
                    textarea.style.height = targetHeight + 'px';
                }
            });
        }

        function adjustQtyByStep(idx, oopIdx, step) {
            var $input = $("#unit_qty_" + idx);
            if ($input.length === 0) {
                return;
            }
            var current = parseInt(String($input.val() || '0').replace(/,/g, ''), 10);
            if (!isFinite(current) || isNaN(current)) {
                current = 0;
            }
            var next = current + step;
            if (next < 0) {
                next = 0;
            }
            $input.val(next);
            orderSheetDetail.qtyGogo(idx, oopIdx);
        }

        fitMemoTextareas();
        setTimeout(fitMemoTextareas, 0);
        window.addEventListener('resize', fitMemoTextareas);


        // 가격변경
        $('.editable-cd-price').editable({
            type: 'text',
            url: '/admin/order/sheet/action',
            title: '가격 변경',
            inputclass: 'testinput',
            params: function(params) {
                params.action_mode = 'orderSheetProductPriceChange';
                params.cd_idx = $(this).data('cdidx');
                params.oop_code = $(this).data('oopcode');
                params.mod_mode = "price";
                return params;
            },
            ajaxOptions: {
                type: 'POST',
                dataType: 'json'
            },
            display: function(value, response) {
                return false;
            },
            success: function(res) {
                if (res.success === true) {
                    $(this).html("<b>" + formatPriceForView(res.uprice) + "</b>");
                    var cdidx = $(this).data('cdidx');
                    $("#unit_price_" + cdidx).val(res.uprice);

                    var exchangeRate = parseFloat($(this).data('exchangerate') || 0);
                    var currency = String($(this).data('currency') || '').trim();
                    if (exchangeRate > 0) {
                        var wonPriceRaw = (parseFloat(res.uprice) || 0) * exchangeRate;
                        if (currency === '엔' || currency.toUpperCase() === 'JPY') {
                            wonPriceRaw = wonPriceRaw / 100;
                        }
                        $("#unit_won_price_" + cdidx).text(formatWonPrice(wonPriceRaw));
                    }

                    orderSheetDetail.qtyGogo(cdidx, $(this).data('oopidx'));
                } else {
                    showAlert("Error", res.msg, "alert2");
                    return false;
                }
            },
            error: function(request, status, error) {
                console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                showAlert("Error", "에러", "alert2");
                return false;
            },
            validate: function(value) {
                if ($.trim(value) == '') {
                    return '빈 값은 입력할 수 없습니다.';
                }
            }
        });

        // 수입신고가 가격변경
        $('.editable-cd-iv-price').editable({
            type: 'text',
            url: '/admin/order/sheet/action',
            title: '수입신고가 가격변경',
            inputclass: 'testinput',
            params: function(params) {
                params.action_mode = 'orderSheetProductPriceChange';
                params.cd_idx = $(this).data('cdidx');
                params.oop_code = $(this).data('oopcode');
                params.mod_mode = "invoicePrice";
                return params;
            },
            ajaxOptions: {
                type: 'POST',
                dataType: 'json'
            },
            display: function(value, response) {
                return false;
            },
            success: function(res) {
                if (res.success === true) {
                    $(this).html("<b>" + formatPriceForView(res.uprice) + "</b>");
                    var cdidx = $(this).data('cdidx');
                    $("#unit_iv_price_" + cdidx).val(res.uprice);
                    orderSheetDetail.qtyGogo(cdidx, $(this).data('oopidx'));
                } else {
                    showAlert("Error", res.msg, "alert2");
                    return false;
                }
            },
            error: function(request, status, error) {
                console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                showAlert("Error", "에러", "alert2");
                return false;
            },
            validate: function(value) {
                if ($.trim(value) == '') {
                    return '빈 값은 입력할 수 없습니다.';
                }
            }
        });

        // 결제통화 가격변경
        function bindEditableCdPayPrice($targets) {
            $targets.editable({
                type: 'text',
                url: '/admin/order/sheet/action',
                title: '결제통화 가격변경',
                inputclass: 'testinput',
                params: function(params) {
                    params.action_mode = 'orderSheetProductPriceChange';
                    params.cd_idx = $(this).data('cdidx');
                    params.oop_code = $(this).data('oopcode');
                    params.mod_mode = "payPrice";
                    params.currency_code = $(this).data('currencycode');
                    return params;
                },
                ajaxOptions: {
                    type: 'POST',
                    dataType: 'json'
                },
                display: function(value, response) {
                    return false;
                },
                success: function(res) {
                    if (res.success === true) {
                        $(this).html("<b>" + formatPriceForView(res.uprice) + "</b>");
                        var cdidx = $(this).data('cdidx');
                        $("#pay_unit_price_" + cdidx).val(res.uprice);

                        var exchangeRate = parseFloat($(this).data('exchangerate') || 0);
                        var currency = String($(this).data('currency') || '').trim();
                        if (exchangeRate > 0) {
                            var wonPriceRaw = (parseFloat(res.uprice) || 0) * exchangeRate;
                            if (currency === '엔' || currency.toUpperCase() === 'JPY') {
                                wonPriceRaw = wonPriceRaw / 100;
                            }
                            $("#pay_unit_won_price_" + cdidx).text(formatWonPrice(wonPriceRaw));
                        }

                        orderSheetDetail.qtyGogo(cdidx, $(this).data('oopidx'));
                    } else {
                        showAlert("Error", res.msg, "alert2");
                        return false;
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    showAlert("Error", "에러", "alert2");
                    return false;
                },
                validate: function(value) {
                    if ($.trim(value) == '') {
                        return '빈 값은 입력할 수 없습니다.';
                    }
                }
            });
        }
        bindEditableCdPayPrice($('.editable-cd-pay-price'));

        // AJAX 재렌더링으로 스크립트가 다시 실행되어도 클릭 핸들러가 중복 등록되지 않도록 네임스페이스로 재바인딩
        $(document)
            .off('click.orderSheetQtyStep', '.qty-step-btn')
            .on('click.orderSheetQtyStep', '.qty-step-btn', function(e) {
                e.preventDefault();
                var idx = $(this).data('idx');
                var oopIdx = $(this).data('oopidx');
                var step = parseInt($(this).data('step'), 10) || 0;
                if (!idx || !oopIdx || step === 0) {
                    return;
                }
                adjustQtyByStep(idx, oopIdx, step);
            });

    })();

</script>