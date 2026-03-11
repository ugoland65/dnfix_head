<?php

$_os_state_text[1] = "작성중";
$_os_state_text[2] = "주문전송";
$_os_state_text[4] = "입금완료";
$_os_state_text[5] = "입고완료";
$_os_state_text[7] = "주문종료";

$_os_pay_mode_list = ["계좌송금", "모인", "카드결제", "예치금"];
?>

<style type="text/css">
    .change-price {
        display: flex;
        flex-direction: column;
        gap: 3px;

        >ul {
            display: flex;
            gap: 3px;
        }
    }

    .notice-caution-box {
        margin-bottom: 8px;
        padding: 10px 12px;
        border: 1px solid #f3b2b2;
        border-left: 4px solid #d93025;
        border-radius: 6px;
        background-color: #fff3f3;
        color: #8b1e1e;
        line-height: 1.6;
    }

    .notice-caution-title {
        display: inline-block;
        margin-right: 8px;
        padding: 1px 8px;
        border-radius: 999px;
        background: #d93025;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        vertical-align: middle;
    }
</style>

<form id="orderSheetForm">
    <input type="hidden" name="mode" value="<?= $mode ?? '' ?>">
    <?php if ($mode == 'modify') { ?>
        <input type="hidden" name="idx" value="<?= $orderSheetInfo['oo_idx'] ?? '' ?>">
    <?php } ?>

    <table class="table-style border01 width-full">

        <?php if ($mode == 'modify') { ?>
            <!-- 주문서 번호 -->
            <tr>
                <th style="width:140px;">주문서 번호</th>
                <td>
                    <b><?= $orderSheetInfo['oo_idx'] ?? '' ?></b>
                </td>
            </tr>
        <?php } ?>

        <!-- 주문서 이름 -->
        <tr>
            <th style="width:140px;">주문서 이름</th>
            <td>
                <input type='text' name='oo_name' value="<?= $orderSheetInfo['oo_name'] ?? '' ?>" autocomplete="off" class="width-500">
            </td>
        </tr>

        <tr>
            <th>P/O CODE</th>
            <td>
                <input type='text' name='oo_po_name' value="<?= $orderSheetInfo['oo_po_name'] ?? '' ?>" autocomplete="off" class="width-200">
                PURCHASE ORDER Offer No ( 무역 서류 주문서 P/O 발송시 사내 고유넘버 )
            </td>
        </tr>

        <!-- 주문서폼 IDX -->
        <tr>
            <th>주문서폼 (상품그룹)</th>
            <td>
                <select name="oo_form_idx">
                    <option value="0">== 주문서 폼 선택 ==</option>
                    <?php
                    foreach ($onaOrderGroupList as $onaOrderGroup) {
                    ?>
                        <option value="<?= $onaOrderGroup['oog_idx'] ?? '' ?>" <?php if (($orderSheetInfo['oo_form_idx'] ?? 0) == ($onaOrderGroup['oog_idx'] ?? '')) echo "selected"; ?>><?= $onaOrderGroup['oog_name'] ?? '' ?></option>
                    <?php } ?>
                </select>
                <?php if ($mode == 'modify' && ($orderSheetInfo['oo_form_idx'] ?? 0) == 0) { ?>
                    <span style="color:#ff0000;">※ 주문서 폼 미지정!!!</span>
                <?php } ?>
            </td>
        </tr>

        <!-- 수입형태 -->
        <tr>
            <th>수입형태</th>
            <td>
                <label><input type="radio" name="oo_import" value="국내" <?php if (empty($orderSheetInfo['oo_import']) || ($orderSheetInfo['oo_import'] ?? '') == "국내") echo "checked"; ?>> 국내 주문</label>
                <label><input type="radio" name="oo_import" value="수입" <?php if (($orderSheetInfo['oo_import'] ?? '') == "수입") echo "checked"; ?>> 수입 주문</label>
                <label><input type="radio" name="oo_import" value="구매대행" <?php if (($orderSheetInfo['oo_import'] ?? '') == "구매대행") echo "checked"; ?>> 구매대행</label>
            </td>
        </tr>

        <tr>
            <th>주문 통화 설정</th>
            <td>
                <div id="order_currency_setting">

                    <table class="table-style width-full">
                        <tr>
                            <th style="width:120px;">상품</th>
                            <td>
                                통화
                                <select name="oo_prd_currency">
                                    <option value="원" <?php if (($orderSheetInfo['oo_prd_currency'] ?? '') == "원") echo "selected"; ?>>원</option>
                                    <option value="엔" <?php if (($orderSheetInfo['oo_prd_currency'] ?? '') == "엔") echo "selected"; ?>>엔</option>
                                    <option value="위안" <?php if (($orderSheetInfo['oo_prd_currency'] ?? '') == "위안") echo "selected"; ?>>위안</option>
                                    <option value="달러" <?php if (($orderSheetInfo['oo_prd_currency'] ?? '') == "달러") echo "selected"; ?>>달러</option>
                                </select>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                적용환율 :
                                <input type="text" name='oo_prd_exchange_rate' class="price" value="<?= $orderSheetInfo['oo_prd_exchange_rate'] ?? '' ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>결제 </th>
                            <td>
                                통화
                                <select name="oo_sum_currency">
                                    <option value="원" <?php if (($orderSheetInfo['oo_sum_currency'] ?? '') == "원") echo "selected"; ?>>원</option>
                                    <option value="엔" <?php if (($orderSheetInfo['oo_sum_currency'] ?? '') == "엔") echo "selected"; ?>>엔</option>
                                    <option value="위안" <?php if (($orderSheetInfo['oo_sum_currency'] ?? '') == "위안") echo "selected"; ?>>위안</option>
                                    <option value="달러" <?php if (($orderSheetInfo['oo_sum_currency'] ?? '') == "달러") echo "selected"; ?>>달러</option>
                                </select>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                적용환율 :
                                <input type="text" name='oo_sum_exchange_rate' class="price" value="<?= $orderSheetInfo['oo_sum_exchange_rate'] ?? '' ?>">
                            </td>
                        </tr>
                    </table>

                    <div class="admin-guide-text">
                        - 예) 중국 주문일경우 상품가격 통화는 위안, 결제는 달러로 해야할경우 결제 통화와, 환율을 설정해주세요.<br>
                        - 국내주문은 안써도 됨
                    </div>

                </div>
            </td>
        </tr>

        <tr>
            <th>메모</th>
            <td>
                <textarea name="oo_memo" style="height:70px"><?= $orderSheetInfo['oo_memo'] ?? '' ?></textarea>
            </td>
        </tr>

        <?php if ($mode == 'modify') { ?>

            <!-- 주문서 상태 -->
            <tr>
                <th>주문서 상태</th>
                <td>

                    <div class="os-state-btn-wrap">
                        <button type="button" id="" class="btnstyle1 <?php if (($orderSheetInfo['oo_state'] ?? '') == "1") echo "btnstyle1-info"; ?> btnstyle1-sm" onclick="orderSheetReg.orderSheetState(this, '1', '<?= $orderSheetInfo['oo_idx'] ?? '' ?>')" data-name="작성중">작성중</button>
                        <button type="button" id="" class="btnstyle1 <?php if (($orderSheetInfo['oo_state'] ?? '') == "2") echo "btnstyle1-info"; ?> btnstyle1-sm" onclick="orderSheetReg.orderSheetState(this, '2', '<?= $orderSheetInfo['oo_idx'] ?? '' ?>')" data-name="주문전송">주문전송</button>
                        <button type="button" id="" class="btnstyle1 <?php if (($orderSheetInfo['oo_state'] ?? '') == "4") echo "btnstyle1-info"; ?> btnstyle1-sm" onclick="orderSheetReg.orderSheetState(this, '4', '<?= $orderSheetInfo['oo_idx'] ?? '' ?>')" data-name="입금완료">입금완료</button>
                        <button type="button" id="" class="btnstyle1 <?php if (($orderSheetInfo['oo_state'] ?? '') == "5") echo "btnstyle1-info"; ?> btnstyle1-sm" onclick="orderSheetReg.orderSheetState(this, '5', '<?= $orderSheetInfo['oo_idx'] ?? '' ?>')" data-name="입고완료">입고완료</button>
                        <button type="button" id="" class="btnstyle1 <?php if (($orderSheetInfo['oo_state'] ?? '') == "7") echo "btnstyle1-info"; ?> btnstyle1-sm" onclick="orderSheetReg.orderSheetState(this, '7', '<?= $orderSheetInfo['oo_idx'] ?? '' ?>')" data-name="주문종료">주문종료</button>
                    </div>

                    <div class="m-t-5">
                        <?php
                        foreach ($orderSheetInfo['oo_date_data']['state'] ?? [] as $state) {
                        ?>
                            <ul>
                                <?= $_os_state_text[$state['state_before'] ?? ''] ?? '' ?> -> <?= $_os_state_text[$state['state_after'] ?? ''] ?? '' ?>
                                :: <?= $state['name'] ?? '' ?> ( <?= $state['date'] ?? '' ?> )
                            </ul>
                        <?php } ?>
                    </div>

                </td>
            </tr>

            <?php if( ($orderSheetInfo['oo_state'] ?? 0) > 4 ){ ?>
                <tr>
                    <th>재고 일괄등록</th>
                    <td>
                        
                        <? if( ($orderSheetInfo['oo_stock']['state'] ?? '') == "in" ){ ?>
                            <span style="">재고일괄등록 완료 ( <?=!empty($orderSheetInfo['oo_stock']['reg']['date']) ? date ("y.m.d <b>H:i</b>", strtotime($orderSheetInfo['oo_stock']['reg']['date'])) : ''?> ) | <?=$orderSheetInfo['oo_stock']['reg']['name'] ?? ''?></span>
                        <? }else{ ?>
                            <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheet.osStock('<?=$orderSheetInfo['oo_idx'] ?? ''?>');">재고 일괄등록</button>
                        <? } ?>
                    </td>
                </tr>
            <?php } ?>


            <!-- 주문정보 -->
            <tr>
                <th>주문정보</th>
                <td>

                    <table class="table-style width-full">

                        <tr>
                            <th style="width:120px;">상품 주문가격</th>
                            <td>
                                <input type='text' name='prd_sum_price' class="price" value="<?= number_format($orderSheetInfo['oo_price_data']['prd_sum_price'] ?? 0) ?>">
                                <?= $orderSheetInfo['oo_prd_currency'] ?? '' ?>
                                <div class="admin-guide-text">
                                    - 결제 통화와 다를 경우 입력해주세요.<br>
                                    - 예) 중국 주문일경우 상품가격 통화는 위안, 결제는 달러로 해야할경우
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>주문 결제 가격</th>
                            <td>
                                <input type='text' name='oo_sum_price' class="price" value="<?= number_format($orderSheetInfo['oo_sum_price'] ?? 0) ?>">
                                <?= $orderSheetInfo['oo_sum_currency'] ?? '' ?>
                            </td>
                        </tr>

                        <tr>
                            <th>주문서 발송일</th>
                            <td>
                                <div class="calendar-input"><input type='text' name='order_send_date' value="<?= $orderSheetInfo['oo_date_data']['order_send_date'] ?? '' ?>" autocomplete="off"></div>
                            </td>
                        </tr>

                        <!-- 가격변동사항 -->
                        <tr>
                            <th>가격변동사항</th>
                            <td>
                                <div id="change_price" class="change-price">
                                    <?php
                                    foreach ($orderSheetInfo['oo_price_data']['change_price'] ?? [] as $change_price) {
                                    ?>
                                        <ul>
                                            <li>
                                                <select name="change_price_mode[]">
                                                    <option value="할인" <? if ($change_price['mode'] == "할인") echo "selected"; ?>>할인</option>
                                                    <option value="추가" <? if ($change_price['mode'] == "추가") echo "selected"; ?>>추가</option>
                                                </select>
                                            <li>
                                            <li><input type="text" name='change_price_price[]' class="price" placeholder="금액" value="<?= number_format($change_price['price'] ?? 0, 2) ?>">
                                            <li>
                                            <li><input type="text" name='change_price_body[]' class='change-price-body' value="<?= $change_price['body'] ?? '' ?>" placeholder="사유">
                                            <li>
                                            <li><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="orderSheetReg.delChangePrice(this)"><i class="fas fa-minus-circle"></i> 삭제</button>
                                            <li>
                                        </ul>
                                    <?php } ?>
                                </div>
                                <div class="m-t-5"><button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="orderSheetReg.addChangePrice();"><i class="fas fa-plus-circle"></i> 주문 변동금액 라인 추가</button></div>
                            </td>
                        </tr>

                        <!-- 확정 주문 금액 -->
                        <tr>
                            <th>확정 주문 금액</th>
                            <td>
                                <input type='text' name='oo_fn_price' id='oo_fn_price' class="price price_point" value="<?= number_format($orderSheetInfo['oo_fn_price'] ?? 0, 2) ?>">
                                <?= $orderSheetInfo['oo_sum_currency'] ?? '' ?>
                            </td>
                        </tr>

                        <!-- 인보이스 -->
                        <tr>
                            <th>주문 관련 파일</th>
                            <td>

                                <div id="file_line_wrap_invoice">
                                    <?php
                                    foreach ($orderSheetInfo['oo_upload_file']['invoice'] ?? [] as $invoice) {

                                        if (!empty($invoice['view_name'])) {
                                            $_this_filename = $invoice['view_name'] . " ( " . ($invoice['name'] ?? '') . " )";
                                        } else {
                                            $_this_filename = $invoice['name'] ?? '';
                                        }
                                        $_size_text = !empty($invoice['size']) ? (' / ' . $invoice['size']) : '';

                                    ?>
                                        <div class="file-line m-t-5">
                                            <i class="far fa-save fa-flip-horizontal"></i>
                                            <a href="/data/uploads/<?= $invoice['name'] ?? '' ?>" target="_blank"><?= $_this_filename ?></a>
                                            :: <?= $invoice['reg_name'] ?? '' ?> ( <?= $invoice['date'] ?? '' ?> )<?= $_size_text ?>
                                            <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.fileDel(this, 'invoice', '<?= $orderSheetInfo['oo_idx'] ?? ($idx ?? '') ?>' ,'<?= $_this_filename ?>')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="m-t-5">
                                    <input type="text" name="upload_file_invoice_name" id="upload_file_invoice_name" style="width:200px;" placeholder="노출될 파일이름">
                                    <input name="upload_file_invoice" id="upload_file_invoice" type="file" class="m-t-5">
                                </div>

                                <div class="m-t-5">
                                    <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.fileUpload('invoice');">주문관련파일 업로드</button>
                                </div>

                            </td>
                        </tr>

                    </table>

                </td>
            </tr>

            <!-- 결제정보 -->
            <tr>
                <th>결제 (송금)정보</th>
                <td>

                    <table class="table-style width-full">

                        <tr>
                            <th style="width:120px;">결제처리</th>
                            <td>
                                <div id="add_pay_list" class="change-price">

                                    <?php
                                    foreach ($orderSheetInfo['oo_price_data']['pay_list'] ?? [] as $pay_list) {
                                    ?>
                                        <ul>
                                            <li>
                                                <select name="pay_mode[]">
                                                    <?
                                                    foreach ($_os_pay_mode_list as $pay_mode) {
                                                    ?>
                                                        <option value="<?= $pay_mode ?>" <? if ($pay_list['pay_mode'] == ($pay_mode ?? '')) echo "selected"; ?>><?= $pay_mode ?></option>
                                                    <? } ?>
                                                </select>
                                            </li>
                                            <li>
                                                결제금 : <input type="text" name="pay_price[]" class="price price_point" value="<?= number_format($pay_list['pay_price'] ?? 0) ?>" style="width:80px;"> 원
                                            </li>
                                            <li>
                                                결제일 : <div class="calendar-input" style="display:inline-block;"><input type="text" name="pay_date[]" value="<?= $pay_list['pay_date'] ?? '' ?>" style="width:80px;" autocomplete="off"></div>
                                            </li>
                                            <li>
                                                <input type="text" name="pay_memo[]" value="<?= $pay_list['pay_memo'] ?? '' ?>" style="width:250px;" placeholder="메모">
                                            </li>
                                            <li>
                                                <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.addPayListDel(this)"><i class="fas fa-trash-alt"></i></button>
                                            </li>
                                        </ul>
                                    <?php } ?>

                                </div>
                                <div class="m-t-5">
                                    <button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="orderSheetReg.addPayList();"><i class="fas fa-plus-circle"></i> 결제정보 라인 추가</button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>최종 결제수수료</th>
                            <td>
                                <input type='text' name='pay_fee' value="<?= number_format($orderSheetInfo['oo_price_data']['pay_fee'] ?? 0) ?>" style='width:100px;'> 원
                            </td>
                        </tr>

                        <tr>
                            <th>최종 합계 결제액</th>
                            <td>
                                <input type='text' name='oo_price_kr' id='oo_price_kr' class="price price_point" value="<?= number_format($orderSheetInfo['oo_price_kr'] ?? 0) ?>" style='width:100px;'> 원
                                ( ※ 예치금 결제도 결제액으로 포함 )
                            </td>
                        </tr>

                        <!-- 송금확인증 -->
                        <tr>
                            <th>결제 관련 파일</th>
                            <td>

                                <div id="file_line_wrap_pay">

                                    <?php
                                    foreach ($orderSheetInfo['oo_upload_file']['pay'] ?? [] as $pay) {
                                        if (!empty($pay['view_name'])) {
                                            $_this_filename = $pay['view_name'] . " ( " . ($pay['name'] ?? '') . " )";
                                        } else {
                                            $_this_filename = $pay['name'] ?? '';
                                        }
                                        $_size_text = !empty($pay['size']) ? (' / ' . $pay['size']) : '';
                                    ?>
                                        <div class="file-line m-t-5">
                                            <i class="far fa-save fa-flip-horizontal"></i>
                                            <a href="/data/uploads/<?= $pay['name'] ?? '' ?>" target="_blank"><?= $_this_filename ?></a>
                                            :: <?= $pay['reg_name'] ?? '' ?> ( <?= $pay['date'] ?? '' ?> )<?= $_size_text ?>
                                            <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.fileDel(this, 'pay', '<?= $orderSheetInfo['oo_idx'] ?? ($idx ?? '') ?>' ,'<?= $_this_filename ?>')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    <?php } ?>

                                </div>

                                <div class="m-t-5">
                                    <input type="text" name="upload_file_pay_name" id="upload_file_pay_name" style="width:200px;" placeholder="노출될 파일이름">
                                    <input name="upload_file" id="upload_file_pay" type="file" class="m-t-5">
                                </div>

                                <div class="m-t-5">
                                    <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.fileUpload('pay');">송금 확인증 업로드</button>
                                </div>

                            </td>
                        </tr>

                    </table>

                </td>
            </tr>

            <!-- 배송 -->
            <tr>
                <th>배송</th>
                <td>

                    <table class="table-style width-full">
                        <tr>
                            <th style="width:120px;">배송방식</th>
                            <td>
                                <label><input type="radio" name="express_mode" value="국내택배" <?php if ($orderSheetInfo['oo_express_data']['mode'] ?? '' == "국내택배") echo "checked"; ?>> 국내택배</label>
                                <label><input type="radio" name="express_mode" value="국내화물" <?php if ($orderSheetInfo['oo_express_data']['mode'] ?? '' == "국내화물") echo "checked"; ?>> 국내화물</label>
                                <label><input type="radio" name="express_mode" value="항공" <?php if ($orderSheetInfo['oo_express_data']['mode'] ?? '' == "항공") echo "checked"; ?>> 항공</label>
                                <label><input type="radio" name="express_mode" value="해운" <?php if ($orderSheetInfo['oo_express_data']['mode'] ?? '' == "해운") echo "checked"; ?>> 해운</label>
                            </td>
                        </tr>

                        <tr>
                            <th>배송사</th>
                            <td>
                                <select name="express_name">
                                    <option value="">배송사 선택</option>
                                    <option value="항공 FEDEX" <?php if (($orderSheetInfo['oo_express_data']['name'] ?? '') == "항공 FEDEX") echo "selected"; ?>>항공 FEDEX</option>
                                    <option value="항공 DHL" <?php if (($orderSheetInfo['oo_express_data']['name'] ?? '') == "항공 DHL") echo "selected"; ?>>항공 DHL</option>
                                    <option value="항공 UPS" <?php if (($orderSheetInfo['oo_express_data']['name'] ?? '') == "항공 UPS") echo "selected"; ?>>항공 UPS</option>
                                    <option value="중국 해운 이안로지스틱" <?php if (($orderSheetInfo['oo_express_data']['name'] ?? '') == "중국 해운 이안로지스틱") echo "selected"; ?>>중국 해운 이안로지스틱</option>
                                    <option value="중국 해운 구매대행" <?php if (($orderSheetInfo['oo_express_data']['name'] ?? '') == "중국 해운 구매대행") echo "selected"; ?>>중국 해운 구매대행</option>
                                    <option value="일본 해운 파테스" <?php if (($orderSheetInfo['oo_express_data']['name'] ?? '') == "일본 해운 파테스") echo "selected"; ?>>일본 해운 파테스</option>
                                    <option value="일본 해운 HTNS" <?php if (($orderSheetInfo['oo_express_data']['name'] ?? '') == "일본 해운 HTNS") echo "selected"; ?>>일본 해운 HTNS</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>송장번호</th>
                            <td><input type='text' name='express_number' value="<?= $orderSheetInfo['oo_express_data']['number'] ?? '' ?>" style='width:300px;'></td>
                        </tr>

                        <tr id="express_price_expected_row" style="<?php if (($orderSheetInfo['oo_express_data']['name'] ?? '') !== '항공 FEDEX') echo 'display:none;'; ?>">
                            <th>배송비 특가요청</th>
                            <td>

                                <div class="notice-caution-box">
                                    <span class="notice-caution-title">주의</span>
                                    배송사가 항공 FEDEX인 경우 특가요청을 진행할 수 있습니다.<br>
                                    송장번호가 발급되면 바로 특가요청을 진행해주세요.
                                </div>

                                특가요청일 : <div class="calendar-input m-r-10" style="display:inline-block;"><input type="text" name="express_price_expected_date" id="express_price_expected_date" value="<?= $orderSheetInfo['oo_express_data']['price_expected_date'] ?? '' ?>" autocomplete="off"></div>
                                예상 배송비 : <input type='text' name="express_price_expected" id="express_price_expected" class="price price_point" value="<?= number_format($orderSheetInfo['oo_express_data']['price_expected'] ?? 0) ?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;'>원
                            </td>
                        </tr>

                        <tr>
                            <th>중량 / CBM / 박스</th>
                            <td>
                                신고서 중량 : <input type='text' name='express_report_weight' value="<?= $orderSheetInfo['oo_express_data']['report_weight'] ?? '' ?>" style='width:80px;'> kg /
                                청구 중량 : <input type='text' name='express_weight' value="<?= $orderSheetInfo['oo_express_data']['weight'] ?? '' ?>" style='width:80px;'> kg /
                                CBM : <input type='text' name='express_cbm' value="<?= $orderSheetInfo['oo_express_data']['cbm'] ?? '' ?>" style='width:60px;'> /
                                박스수 : <input type='text' name='express_box' value="<?= $orderSheetInfo['oo_express_data']['box'] ?? '' ?>" style='width:60px;'> 박스
                            </td>
                        </tr>

                        <tr>
                            <th>배송비</th>
                            <td>
                                배송비 : <input type='text' name="express_price" id="express_price" class="price price_point" value="<?= number_format($orderSheetInfo['oo_express_data']['price'] ?? 0) ?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;'>원
                            </td>
                        </tr>

                        <!-- 배송비 결제기한 -->
                        <tr>
                            <th>배송비 결제기한</th>
                            <td>
                                <div class="calendar-input" style="display:inline-block;">
                                    <input type="text" name="expressApprovalPayment_date" id="expressApprovalPayment_date" value="<?= $orderSheetInfo['oo_approval_date']['express']['approval']['date'] ?? '' ?>" autocomplete="off">
                                </div>

                                <?php
                                    $_this_calendar_idx = $orderSheetInfo['oo_approval_date']['express']['approval']['calendar_idx'] ?? null;
                                    if (!empty($orderSheetInfo['oo_approval_date']['express']['approval']['date']) && !empty($_this_calendar_idx)) {
                                ?>
                                    <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm"
                                        onclick="orderSheetReg.approvalPayment('<?= $orderSheetInfo['oo_idx'] ?? '' ?>', 'express', 'modify', '<?= $_this_calendar_idx ?>');">
                                        배송비 결제기한 수정
                                    </button>

                                    <?php if (!empty($_this_calendar_idx)) { ?>
                                        <div class="m-t-10">
                                            캘린더 노출중 ( <?= $_this_calendar_idx ?> )

                                            <?php if (($orderSheetInfo['oo_approval_date']['express']['approval']['calendar_state'] ?? '') == "E") { ?>
                                                처리완료 ( <?= !empty($orderSheetInfo['oo_approval_date']['express']['approval']['calendar_reg']['date']) ? date("y.m.d <b>H:i</b>", strtotime($orderSheetInfo['oo_approval_date']['express']['approval']['calendar_reg']['date'])) : '' ?> :: <?= $orderSheetInfo['oo_approval_date']['express']['approval']['calendar_reg']['id'] ?? '' ?>)
                                            <?php } else { ?>
                                                <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs" onclick="orderSheetReg.calendarOk('<?= $orderSheetInfo['oo_idx'] ?? ($idx ?? '') ?>', 'express', '<?= $_this_calendar_idx ?>')">완료처리</button>
                                            <?php } ?>

                                            <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.calendarDel('<?= $orderSheetInfo['oo_idx'] ?? ($idx ?? '') ?>', 'express', '<?= $_this_calendar_idx ?>')"><i class="fas fa-trash-alt"></i> 캘린더 삭제</button>
                                        </div>
                                    <?php } ?>

                                <?php } else { ?>
                                    <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="orderSheetReg.approvalPayment('<?= $orderSheetInfo['oo_idx'] ?? ($idx ?? '') ?>', 'express', 'new');">배송비 결제기한 등록</button>
                                <?php } ?>

                            </td>
                        </tr>

                        <tr>
                            <th>추가 배송비</th>
                            <td>
                                추가배송비(용달등) : <input type='text' name='express_price_add' class="price price_point" value="<?= number_format($orderSheetInfo['oo_express_data']['price_add'] ?? 0) ?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;'>원
                                <?php /*
                                    <div class="m-t-5">
                                        <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.addPayList();" >추가 배송비</button>
                                    </div>
                                */ ?>
                            </td>
                        </tr>

                    </table>

                </td>
            </tr>

            <!-- 관부가세 -->
            <tr>
                <th>관부가세<br>(수입 전용)</th>
                <td>

                    <table class="table-style width-full">
                        <tr>
                            <th style="width:120px;">수입신고번호</th>
                            <td>
                                <input type='text' name='tex_num' value="<?= $orderSheetInfo['oo_tex_data']['num'] ?? '' ?>" style='width:300px;'>
                            </td>
                        </tr>

                        <tr>
                            <th>수입신고가격</th>
                            <td>
                                <input type='text' name='tex_report_price' class="price" value="<?= number_format($orderSheetInfo['oo_tex_data']['report_price'] ?? 0) ?>" style='width:100px;'>원
                            </td>
                        </tr>
                        <tr>
                            <th>관/부가세</th>
                            <td>
                                관세 : <input type='text' name='tex_duty_price' id='tex_duty_price' class="price price_point" value="<?= number_format($orderSheetInfo['oo_tex_data']['duty_price'] ?? 0) ?>" style='width:100px;'>원 /
                                부가세 : <input type='text' name='tex_vat_price' id='tex_vat_price' class="price price_point" value="<?= number_format($orderSheetInfo['oo_tex_data']['vat_price'] ?? 0) ?>" style='width:100px;'>원
                            </td>
                        </tr>

                        <!-- 관/부가세 결제기한 -->
                        <tr>
                            <th>관/부가세 결제기한</th>
                            <td>
                                <div class="calendar-input" style="display:inline-block;"><input type="text" name="texApprovalPayment_date" id="texApprovalPayment_date" value="<?= $orderSheetInfo['oo_approval_date']['tax']['approval']['date'] ?? '' ?>"></div>

                                <?php
                                if (!empty($orderSheetInfo['oo_approval_date']['tax']['approval']['date'])) {
                                    $_this_calendar_idx = $orderSheetInfo['oo_approval_date']['tax']['approval']['calendar_idx'] ?? '';
                                ?>
                                    <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.approvalPayment('<?= $orderSheetInfo['oo_idx'] ?? ($idx ?? '') ?>', 'tax', 'modify', '<?= $_this_calendar_idx ?>');">관/부가세 결제기한 수정</button>

                                    <?php if (!empty($orderSheetInfo['oo_approval_date']['tax']['approval']['calendar_idx'])) { ?>
                                        <div class="m-t-10">
                                            캘린더 노출중 ( <?= $_this_calendar_idx ?> )

                                            <? if (($orderSheetInfo['oo_approval_date']['tax']['approval']['calendar_state'] ?? '') == "E") { ?>
                                                처리완료 ( <?= !empty($orderSheetInfo['oo_approval_date']['tax']['approval']['calendar_reg']['date']) ? date("y.m.d <b>H:i</b>", strtotime($orderSheetInfo['oo_approval_date']['tax']['approval']['calendar_reg']['date'])) : '' ?> :: <?= $orderSheetInfo['oo_approval_date']['tax']['approval']['calendar_reg']['name'] ?? '' ?>)
                                            <? } else { ?>
                                                <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs" onclick="orderSheetReg.calendarOk('<?= $orderSheetInfo['oo_idx'] ?? ($idx ?? '') ?>', 'tax', '<?= $_this_calendar_idx ?>')">완료처리</button>
                                            <? } ?>

                                            <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.calendarDel('<?= $orderSheetInfo['oo_idx'] ?? ($idx ?? '') ?>', 'tax', '<?= $_this_calendar_idx ?>')"><i class="fas fa-trash-alt"></i> 캘린더에서 삭제</button>

                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.approvalPayment('<?= $orderSheetInfo['oo_idx'] ?? ($idx ?? '') ?>', 'tax', 'new');">관/부가세 결제기한 등록</button>
                                <?php } ?>

                            </td>
                        </tr>

                        <tr>
                            <th>관세사 수수료</th>
                            <td>
                                <input type='text' name='tex_commission' class="price price_point" value="<?= number_format($orderSheetInfo['oo_tex_data']['commission'] ?? 0) ?>" style='width:100px;'>원
                            </td>
                        </tr>

                        <tr>
                            <th>수입/세금 관련파일</th>
                            <td>

                                <div id="file_line_wrap_import_declaration">
                                    <?php
                                    foreach ($orderSheetInfo['oo_upload_file']['import_declaration'] ?? [] as $import_declaration) {
                                        if (!empty($import_declaration['view_name'])) {
                                            $_this_filename = $import_declaration['view_name'] . " ( " . ($import_declaration['name'] ?? '') . " )";
                                        } else {
                                            $_this_filename = $import_declaration['name'] ?? '';
                                        }
                                        $_size_text = !empty($import_declaration['size']) ? (' / ' . $import_declaration['size']) : '';
                                    ?>
                                        <div class="file-line m-t-5">
                                            <i class="far fa-save fa-flip-horizontal"></i>
                                            <a href="/data/uploads/<?= $import_declaration['name'] ?? '' ?>" target="_blank"><?= $_this_filename ?></a>
                                            :: <?= $import_declaration['reg_name'] ?? '' ?> ( <?= $import_declaration['date'] ?? '' ?> )<?= $_size_text ?>
                                            <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.fileDel(this, 'import_declaration', '<?= $orderSheetInfo['oo_idx'] ?? ($idx ?? '') ?>' ,'<?= $_this_filename ?>')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>

                                <div class="m-t-5">
                                    <input type="text" name="upload_file_import_declaration_name" id="upload_file_import_declaration_name" style="width:200px;" placeholder="노출될 파일이름">
                                    <input name="upload_file_import_declaration" id="upload_file_import_declaration" type="file" class="m-t-5">
                                </div>

                                <div class="m-t-5">
                                    <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="orderSheetReg.fileUpload('import_declaration');">수입신고필증 업로드</button>
                                </div>

                            </td>
                        </tr>

                    </table>

                </td>
            </tr>

            <tr>
                <th>입고</th>
                <td>
                    입고일 : <div class="calendar-input" style="display:inline-block;"><input type='text' name='in_date' id='in_date' value="<?= $orderSheetInfo['oo_in_date'] ?? '' ?>" autocomplete="off"></div>
                </td>
            </tr>

        <?php } ?>

    </table>
</form>

<div class="m-t-10 text-center">
    <?php if ($mode == 'modify') { ?>
        <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="orderSheetReg.orderSheetSave(this, 'stay');">저장후 남아있기</button>
    <?php } else { ?>
        <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="orderSheetReg.orderSheetSave(this);">신규주문서 생성</button>
    <?php } ?>
</div>


<?php if ($mode == 'modify') { ?>
    <!-- 파일등록 -->
    <form name='file_upload_form' id='file_upload_form' method='post' enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="action_mode" value="orderSheetFile">
        <input type="hidden" name="smode" id="file_upload_mode">
        <input type="hidden" name="sname" id="file_upload_name">
        <input type="hidden" name="idx" value="<?= $orderSheetInfo['oo_idx'] ?? '' ?>">
    </form>
<?php } ?>

<script>
    var orderSheetReg = (function() {

        const osPayModeList = <?= json_encode($_os_pay_mode_list ?? [], JSON_UNESCAPED_UNICODE); ?>;

        /**
         * 주문 변동금액 라인 추가
         * @return void
         */
        function addChangePrice() {

            var html = '<ul>' +
                '<li>' +
                '<select name="change_price_mode[]">' +
                '<option value="할인">할인</option>' +
                '<option value="추가">추가</option>' +
                '</select>' +
                '<li>' +
                '<li><input type="text" name="change_price_price[]" class="price" placeholder="금액"><li>' +
                '<li><input type="text" name="change_price_body[]" class= "change-price-body" placeholder="사유"><li>' +
                '<li><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="orderSheetReg.delChangePrice(this)" ><i class="fas fa-minus-circle"></i> 삭제</button><li>' +
                '</ul>';

            $("#change_price").append(html);

        }

        /**
         * 결제 리스트 라인 추가
         * @return void
         */
        function addPayList() {

            var html = '<ul>' +
                '<li>' +
                '<select name="pay_mode[]">'
            <? foreach ($_os_pay_mode_list as $pay_mode) { ?>
                    +
                    '<option value="<?= $pay_mode ?>"><?= $pay_mode ?></option>'
            <? } ?>
                +
                '</select>' +
                '</li>' +
                '<li>결제금 : <input type="text" name="pay_price[]" class="price price_point" value="" onkeyUP="GC.commaInput( this.value, this );" style="width:80px;" > 원</li>' +
                '<li>결제일 : <div class="calendar-input" style="display:inline-block;"><input type="text" name="pay_date[]"  value="" style="width:80px;" ></div></li>' +
                '<li><input type="text" name="pay_memo[]" value="" style="width:250px;" placeholder="메모" ></li>' +
                '<li><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.addPayListDel(this)" ><i class="fas fa-trash-alt"></i></button></li>' +
                '</ul>';

            $("#add_pay_list").append(html);
            $(".calendar-input input").datepicker(clareCalendar);

        }

        /**
         * 주문서 저장
         * @param object $obj
         * @param string $savemode
         * @return void
         */
        function orderSheetSave(obj, savemode) {

            var formData = $("#orderSheetForm").serialize();

            ajaxRequest('/admin/order/sheet/save', formData)
                .done(function(res) {
                    if (res.success == true) {

                        if( savemode == 'stay') {
                            showToast("주문서가 저장되었습니다.", new Date().toLocaleTimeString());
                            orderSheet.osViewReset('<?= $orderSheetInfo['oo_idx'] ?? '' ?>');
                        } else {
                            alert('주문서가 저장되었습니다.');
                            location.reload();
                        }

                    } else {
                        alert(res.message);
                    }
                })
                .fail(function(res) {
                    alert(res.message);
                });

        }

        /**
         * 주문서 상태 변경
         * - 입력값 유효성 체크 후 상태 변경 요청
         * 
         * @param object $obj
         * @param string $state
         * @param string $idx
         * @return void
         */
        function orderSheetState(obj, state, idx) {

            if (!idx) {
                alert('주문서 번호가 없습니다.');
                return false;
            }
            var currentState = '<?= $orderSheetInfo['oo_state'] ?? '' ?>';
            if (String(state) === String(currentState)) {
                alert('이미 동일한 상태입니다.');
                return false;
            }
            var oo_import = $(':input:radio[name=oo_import]:checked').val();
            var state_name = $(obj).data("name") || '';
            var content_msg = '주문상태를 (' + state_name + ')로 변경하시겠습니까?';
            var payload = {
                action_mode: 'order_sheet_state',
                idx: idx,
                state: state
            };

            var toNumberText = function(selector) {
                return String($(selector).val() || '').replace(/,/g, '');
            };

            var _oo_fn_price = toNumberText('#oo_fn_price');
            var _oo_price_kr = toNumberText('#oo_price_kr');
            var _tex_duty_price = toNumberText('#tex_duty_price');
            var _tex_vat_price = toNumberText('#tex_vat_price');

            // 상태가 입금완료 이상이면 확정금액/최종결제액 필수
            if (Number(state) > 2) {
                if (!_oo_fn_price || _oo_fn_price === "0") {
                    $('#oo_fn_price').addClass('input-point-ani').focus();
                    alert('(확정 주문 금액)을 입력해주세요.');
                    return false;
                }
                if (!_oo_price_kr || _oo_price_kr === "0") {
                    $('#oo_price_kr').addClass('input-point-ani').focus();
                    alert('(최종 합계 결제액)을 입력해주세요.');
                    return false;
                }
            }

            // 수입 주문 + 입고완료 이상이면 관세/부가세 필수
            if (oo_import === "수입" && Number(state) > 4) {
                if (!_tex_duty_price || _tex_duty_price === "0") {
                    $('#tex_duty_price').addClass('input-point-ani').focus();
                    alert('(관세)가 비어있습니다.');
                    return false;
                }
                if (!_tex_vat_price || _tex_vat_price === "0") {
                    $('#tex_vat_price').addClass('input-point-ani').focus();
                    alert('(부가세)가 비어있습니다.');
                    return false;
                }
            }

            // 입고완료 이상이면 입고일 필수
            if (Number(state) > 4) {
                var _in_date = $("#in_date").val();
                if (!_in_date) {
                    $("#in_date").addClass('input-point-ani').focus();
                    alert('(입고일)이 비어있습니다.');
                    return false;
                }
                content_msg = '주문상태를 ( 입고완료 )로 변경하시겠습니까?\n저장될 입고일은 (' + _in_date + ') 입니다.';
                payload.in_date = _in_date;
            }

            if (!confirm(content_msg)) {
                return false;
            }

            ajaxRequest('/admin/order/sheet/action', payload)
                .done(function(res) {
                    if (res && res.success) {
                        // 상태 버튼 활성 표시를 즉시 반영
                        var $wrap = $(obj).closest('.os-state-btn-wrap');
                        $wrap.find('button').removeClass('btnstyle1-info');
                        $(obj).addClass('btnstyle1-info');

                        showToast("주문상태가 변경되었습니다.", new Date().toLocaleTimeString());
                        orderSheet.osViewReset(idx);

                        //alert(res.message || '주문상태가 변경되었습니다.');
                        //location.reload();
                    } else {
                        alert(res && res.message ? res.message : '상태 변경 실패');
                    }
                })
                .fail(function(res) {
                    alert(res && res.message ? res.message : '에러');
                });
        }

        /**
         * 결제기한 등록/수정
         * @param string $idx
         * @param string $ap_mode
         * @param string $reg_mode
         * @param string $calendar_idx
         * @return void
         */
        function approvalPayment(idx, ap_mode, reg_mode, calendar_idx) {

            if (!idx) {
                alert('주문서 번호가 없습니다.');
                return false;
            }

            var uncomma = function(value) {
                return String(value || '').replace(/,/g, '');
            };

            var _date = '';
            var _price = 0;

            if (ap_mode === 'express') {

                if (!$("#express_price").val() || $("#express_price").val() === "0") {
                    $("#express_price").addClass('input-point-ani').focus();
                    alert("배송비를 입력해주세요.");
                    return false;
                }
                if (!$("#expressApprovalPayment_date").val()) {
                    $("#expressApprovalPayment_date").addClass('input-point-ani').focus();
                    alert("배송비 결제기한 날짜를 입력해주세요.");
                    return false;
                }
                _date = $("#expressApprovalPayment_date").val();
                _price = parseFloat(uncomma($("#express_price").val())) || 0;

            } else if (ap_mode === 'tax') {

                if (!$("#tex_duty_price").val() || $("#tex_duty_price").val() === "0") {
                    $("#tex_duty_price").addClass('input-point-ani').focus();
                    alert("관세를 입력해주세요.\n관/부가세를 모두 입력하셔야 합니다.");
                    return false;
                }
                if (!$("#tex_vat_price").val() || $("#tex_vat_price").val() === "0") {
                    $("#tex_vat_price").addClass('input-point-ani').focus();
                    alert("부가세를 입력해주세요.\n관/부가세를 모두 입력하셔야 합니다.");
                    return false;
                }
                if (!$("#texApprovalPayment_date").val()) {
                    $("#texApprovalPayment_date").addClass('input-point-ani').focus();
                    alert("관/부가세 결제기한 날짜를 입력해주세요.");
                    return false;
                }
                _date = $("#texApprovalPayment_date").val();
                _price = (parseFloat(uncomma($("#tex_duty_price").val())) || 0) + (parseFloat(uncomma($("#tex_vat_price").val())) || 0);

            } else {
                alert('결제기한 모드가 올바르지 않습니다.');
                return false;
            }

            var payload = {
                action_mode: 'ApprovalPayment',
                calendar_a_mode: reg_mode === 'modify' ? 'modify' : 'new',
                ap_mode: ap_mode,
                idx: idx,
                date: _date,
                price: _price
            };

            if (reg_mode === 'modify') {
                payload.calendar_idx = calendar_idx || '';
            }

            ajaxRequest('/admin/order/sheet/action', payload)
                .done(function(res) {
                    if (res && res.success) {
                        alert('결제기한이 저장되었습니다.');
                        location.reload();
                    } else {
                        alert(res && res.message ? res.message : '저장 실패');
                    }
                })
                .fail(function(res) {
                    alert(res && res.message ? res.message : '에러');
                });
        }

        /**
         * 캘린더 완료처리
         * @param string $idx
         * @param string $ap_mode
         * @param string $calendar_idx
         * @return void
         */
        function calendarOk(idx, ap_mode, calendar_idx) {
            if (!confirm('캘린더 완료처리 하시겠습니까?\n완료처리를 하면 페이지가 새로고침 되기때문에 기존에 작성중인 내용을 저장하지 않았다면 작성중인 내용이 사라질 수 있습니다.')) {
                return false;
            }

            var payload = {
                action_mode: 'calendarOk',
                idx: idx,
                ap_mode: ap_mode,
                calendar_idx: calendar_idx
            };

            ajaxRequest('/admin/order/sheet/action', payload)
                .done(function(res) {
                    if (res && res.success) {
                        alert('캘린더 완료처리되었습니다.');
                        location.reload();
                    } else {
                        alert(res && res.message ? res.message : '처리 실패');
                    }
                })
                .fail(function(res) {
                    alert(res && res.message ? res.message : '에러');
                });
        }

        /**
         * 결제기한 캘린더 삭제
         * @param string $idx
         * @param string $ap_mode
         * @param string $calendar_idx
         * @return void
         */
        function calendarDel(idx, ap_mode, calendar_idx) {
            if (!confirm('정말 삭제 하시겠습니까?\n캘린더에 등록된 내용만 삭제됩니다.')) {
                return false;
            }

            var payload = {
                action_mode: 'calendarDel',
                idx: idx,
                ap_mode: ap_mode,
                calendar_idx: calendar_idx
            };

            ajaxRequest('/admin/order/sheet/action', payload)
                .done(function(res) {
                    if (res && res.success) {
                        alert('캘린더가 삭제되었습니다.');
                        location.reload();
                    } else {
                        alert(res && res.message ? res.message : '삭제 실패');
                    }
                })
                .fail(function(res) {
                    alert(res && res.message ? res.message : '에러');
                });
        }

        /**
         * 주문서 파일 업로드
         * @param string $mode
         * @return void
         */
        function fileUpload(mode) {
            var fileCheck = document.getElementById("upload_file_" + mode).value;
            if (!fileCheck) {
                alert("파일을 첨부해 주세요");
                return false;
            }

            $("#file_upload_mode").val(mode);
            $("#file_upload_name").val($("#upload_file_" + mode + "_name").val());

            var form = $('#file_upload_form')[0];
            var imgData = new FormData(form);
            imgData.append("upload_file_" + mode, $("#upload_file_" + mode)[0].files[0]);

            $.ajax({
                url: "/admin/order/sheet/action",
                data: imgData,
                type: "POST",
                dataType: "text",
                contentType: false,
                processData: false,
                success: function(resText) {
                    var res = resText;
                    if (typeof resText === 'string') {
                        try {
                            res = JSON.parse(resText);
                        } catch (e) {
                            alert(resText || '에러');
                            return false;
                        }
                    }

                    if (res && res.success == true) {
                        var displayName = res.view_name ? (res.view_name + ' ( ' + res.filename + ' )') : res.filename;
                        var sizeText = res.size ? (' / ' + res.size) : '';
                        var html = '<div class="file-line m-t-5">' +
                            '<i class="far fa-save fa-flip-horizontal"></i>' +
                            ' <a href="/data/uploads/' + res.filename + '" target="_blank">' + displayName + '</a>' +
                            ' :: ' + res.reg_name + ' ( ' + res.reg_date + ' )' + sizeText +
                            ' <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.fileDel(this, \'' + mode + '\', \'' + res.idx + '\' ,\'' + displayName + '\')" >' +
                            '<i class="fas fa-trash-alt"></i>' +
                            '</button>' +
                            '</div>';

                        $("#file_line_wrap_" + mode).append(html);
                        $("#upload_file_" + mode).val("");
                    } else {
                        alert((res && (res.message || res.msg)) ? (res.message || res.msg) : '에러');
                        return false;
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    alert(request.responseText || "에러");
                    return false;
                }
            });
        }

        /**
         * 주문서 파일 삭제
         * @param object $obj
         * @param string $mode
         * @param string $idx
         * @param string $filename
         * @return void
         */
        function fileDel(obj, mode, idx, filename) {
            var displayName = filename || '';
            var realFilename = displayName;
            var match = displayName.match(/\(([^()]+)\)\s*$/);
            if (match && match[1]) {
                realFilename = match[1].trim();
            }

            if (!confirm('( ' + displayName + ' ) 파일을 삭제합니다.\n삭제시 복구되지 않습니다.')) {
                return false;
            }

            $.ajax({
                url: "/admin/order/sheet/action",
                data: {
                    "action_mode": "orderSheetFileDelete",
                    "idx": idx,
                    "smode": mode,
                    "filename": realFilename
                },
                type: "POST",
                dataType: "json",
                success: function(res) {
                    if (res && res.success == true) {
                        location.reload();
                    } else {
                        alert((res && (res.message || res.msg)) ? (res.message || res.msg) : '에러');
                        return false;
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    alert(request.responseText || "에러");
                    return false;
                },
                complete: function() {
                    $(obj).attr('disabled', false);
                }
            });
        }

        return {
            addChangePrice,
            delChangePrice: function(button) {
                $(button).closest('ul').remove();
            },
            addPayList,
            addPayListDel: function(button) {
                $(button).closest('ul').remove();
            },
            orderSheetSave,
            orderSheetState,
            approvalPayment,
            calendarOk,
            calendarDel,
            fileUpload,
            fileDel,
        }

    })();
</script>

<script>

        function toggleExpressPriceExpectedRow() {
            var expressName = $('select[name="express_name"]').val() || '';
            if (expressName === '항공 FEDEX') {
                $('#express_price_expected_row').show();
            } else {
                $('#express_price_expected_row').hide();
            }
        }

        function toggleOrderCurrencySetting() {
            var importValue = $('input[name="oo_import"]:checked').val();
            if (importValue === '국내') {
                $('#order_currency_setting').hide();
            } else {
                $('#order_currency_setting').show();
            }
        }

        function formatNumberWithComma(value) {
            var raw = String(value || '').replace(/,/g, '');
            if (raw === '') return '';
            var parts = raw.split('.');
            var intPart = parts[0].replace(/\D/g, '');
            var decPart = parts[1] ? parts[1].replace(/\D/g, '') : '';
            var formatted = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return decPart ? formatted + '.' + decPart : formatted;
        }


        $(function() {

            $('.calendar-input input').datepicker(clareCalendar);

            toggleOrderCurrencySetting();
            toggleExpressPriceExpectedRow();
            $('input[name="oo_import"]').on('change', toggleOrderCurrencySetting);
            $('select[name="express_name"]').on('change', toggleExpressPriceExpectedRow);

            $(document).on('input', 'input.price', function() {
                var value = $(this).val();
                if (typeof GC !== 'undefined' && typeof GC.commaInput === 'function') {
                    GC.commaInput(value, this);
                    return;
                }
                $(this).val(formatNumberWithComma(value));
            });

        });


</script>
