<form name='prd_price_form' id='prd_price_form' method='post' enctype="multipart/form-data" autocomplete="off">

    <input type="hidden" name="idx" value="<?= $productData['CD_IDX'] ?? '' ?>">

    <table class="table-style ">
        <colgroup>
            <col width="150px" />
            <col />
        </colgroup>

        <tbody>
            <tr>
                <td colspan="2" class="none-bg title">
                    <h1>상품 매입정보</h1>
                </td>
            </tr>
            <tr>
                <th>매입 방식</th>
                <td>

                    <?php
                    /*
                    if (!isset($_arr_national)) {
                        $_arr_national = [];
                    }
                    for ($i=0; $i<count($_arr_national); $i++){
                        if (!is_array($_arr_national[$i])) continue;
                    ?>
                    <label><input type="radio" name="cd_national" value="<?=$_arr_national[$i]['code'] ?? ''?>" <?php if( ($productData['cd_national'] ?? '') == ($_arr_national[$i]['code'] ?? '') ) echo "checked"; ?> onclick="prdInfoPrice.costCalculationNew()"> <?=$_arr_national[$i]['name'] ?? ''?>(<?=$_arr_national[$i]['code'] ?? ''?>)</label>
                    <?php } 
                    */
                    ?>

                    <label><input type="radio" name="cd_national" value="jp" <?php if ($productData['cd_national'] == "jp") echo "checked"; ?> onclick="prdInfoPrice.costCalculationNew(); prdInfoPrice.toggleCostCalculatorVisibility();"> 일본수입</label>
                    <label class="m-l-10"><input type="radio" name="cd_national" value="cn" <?php if ($productData['cd_national'] == "cn") echo "checked"; ?> onclick="prdInfoPrice.costCalculationNew(); prdInfoPrice.toggleCostCalculatorVisibility();"> 중국수입</label>
                    <label class="m-l-10"><input type="radio" name="cd_national" value="dollar" <?php if ($productData['cd_national'] == "dollar") echo "checked"; ?> onclick="prdInfoPrice.costCalculationNew(); prdInfoPrice.toggleCostCalculatorVisibility();"> 달러</label>
                    <label class="m-l-10"><input type="radio" name="cd_national" value="kr" <?php if ($productData['cd_national'] == "kr") echo "checked"; ?> onclick="prdInfoPrice.costCalculationNew(); prdInfoPrice.toggleCostCalculatorVisibility();"> 한국사입</label>
                </td>
            </tr>
            <tr>
                <th>중량</th>
                <td>
                    상품중량 : <input type='text' name='cd_weight_1' id='cd_weight_1' style='width:80px;' value="<?= $productData['cd_weight_fn']['1'] ?? '' ?>">
                    전체중량 : <input type='text' name='cd_weight_2' id='cd_weight_2' style='width:80px;' value="<?= $productData['cd_weight_fn']['2'] ?? '' ?>" onkeyUP="prdInfoPrice.costCalculationNew()">
                    실측중량 : <input type='text' name='cd_weight_3' id='cd_weight_3' style='width:80px;' value="<?= $productData['cd_weight_fn']['3'] ?? '' ?>" onkeyUP="prdInfoPrice.costCalculationNew()">
                    <div class="admin-guide-text">
                        - 단위 g (숫자만 등록할것)
                    </div>
                </td>
            </tr>
            <tr>
                <th>포장 사이즈</th>
                <td>
                    가로(W) : <input type='text' name='invoice_size_w' value="<?= $productData['cd_size_fn']['invoice']['W'] ?? '' ?>" style="width:60px">
                    세로(H) : <input type='text' name='invoice_size_h' value="<?= $productData['cd_size_fn']['invoice']['H'] ?? '' ?>" style="width:60px">
                    깊이(D) : <input type='text' name='invoice_size_d' value="<?= $productData['cd_size_fn']['invoice']['D'] ?? '' ?>" style="width:60px">
                    &nbsp;&nbsp;
                    CBM : <input type='text' name='invoice_size_cbm' id='invoice_size_cbm' value="<?= $productData['cd_size_fn']['invoice']['cbm'] ?? '' ?>" style="width:60px">
                    <input type="checkbox" name="invoice_size_cbm_mode" value="hand" <?php if (($productData['cd_size_fn']['invoice']['cbm_mode'] ?? '') == "hand") echo "checked"; ?>> CBM 수동입력
                    <div class="admin-guide-text">
                        - 단위 mm (숫자만 등록할것)
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="none-bg" style="height:10px;"></td>
            </tr>

            <tr>
                <th>쑈당몰 판매가</th>
                <td>
                    <input type='text' name='cd_sale_price' value="<?= number_format($productData['cd_sale_price'] ?? 0) ?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;'> 원

                    <?php
                    if (($productData['cd_sale_price'] ?? 0) > 0 && ($productData['cd_cost_price'] ?? 0) > 0) {
                    ?>
                        | 마진 : <b><?= number_format($productData['cd_sale_price'] - $productData['cd_cost_price']) ?></b>
                        ( <b><?= round(($productData['cd_sale_price'] - $productData['cd_cost_price']) / $productData['cd_sale_price'] * 100, 2) ?></b> % )
                        <?php if ($productData['cd_sale_price'] > 29999) { ?>
                            | 3만 무배 마진 : <b><?= number_format($productData['cd_sale_price'] - ($productData['cd_cost_price'] + 2500)) ?></b>
                            ( <b style="color:#ff0000"><?= round(($productData['cd_sale_price'] - ($productData['cd_cost_price'] + 2500)) / $productData['cd_sale_price'] * 100, 2) ?></b> % )
                        <?php } ?>
                    <?php } ?>

                </td>
            </tr>

            <tr>
                <th>원가</th>
                <td>
                    <div>
                        <input type='text' name="cd_cost_price" id="cd_cost_price" value="<?= number_format($productData['cd_cost_price'] ?? 0) ?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;'> 원
                        <label class="m-l-20"><input type="radio" name="cd_cost_price_vat" value="포함" <?php if (($productData['cd_cost_price_info']['VAT'] ?? '') == "포함") echo "checked"; ?>> VAT 포함</label>
                        <label><input type="radio" name="cd_cost_price_vat" value="미포함" <?php if (($productData['cd_cost_price_info']['VAT'] ?? '') == "미포함") echo "checked"; ?>> VAT 미포함</label>
                    </div>
                    <div class="m-t-6">
                        <textarea name="cd_cost_price_memo" style="height:80px;" placeholder="원가 메모"><?= $productData['cd_cost_price_memo'] ?? '' ?></textarea>
                    </div>
                </td>
            </tr>

            <tr>
                <th>주문서 가격</th>
                <td>
                    <div>
                        <?php
                        foreach ($productData['cd_price_fn'] as $key => $val) {
                            if ($key) {
                        ?>
                                <ul>
                                    <label>
                                        <input type="radio" name="order_price_code" value="<?= $key ?>" <?php if ($key == ($productData['cd_cost_price_info']['기준주문가코드'] ?? '')) echo "checked"; ?> onclick="prdInfoPrice.orderPriceChange('<?= $val ?>')">
                                        <b><?= $key ?></b> : (<?= number_format($val, 2) ?>)
                                        <?php if ($productData['cd_price_fn']['invoice'][$key] ?? '') { ?>
                                            | invoice 다운 : <b><?= $productData['cd_price_fn']['invoice'][$key] ?></b>
                                        <?php } ?>
                                    </label>
                                </ul>
                        <?php }
                        } ?>
                    </div>
                </td>
            </tr>

            <?php
                $costInfo = $productData['cd_cost_price_info'] ?? [];
                $hasCostCalculatorValue = false;
                if (is_array($costInfo)) {
                    $costCalculatorKeys = ['주문종류', '주문가', '적용환율', '배송비', '관세율', '부대비용'];
                    foreach ($costCalculatorKeys as $ck) {
                        if (isset($costInfo[$ck]) && trim((string)$costInfo[$ck]) !== '') {
                            $hasCostCalculatorValue = true;
                            break;
                        }
                    }
                }
                $selectedNational = (string)($productData['cd_national'] ?? '');
                $showCostCalculator = in_array($selectedNational, ['jp', 'cn'], true) || $hasCostCalculatorValue;
            ?>
            <tr id="cost-calculator-row" style="<?php if (!$showCostCalculator) echo 'display:none;'; ?>">
                <th>원가 계산기</th>
                <td>

                    <div id="cost_cal_msg" style="display:none; color:#ff0000; font-size:17px;">[주문종류]를 선택해주세요.</div>

                    <?php if ($productData['cd_size_fn']['invoice']['cbm'] ?? '') { ?>
                        <div class="p-b-6">
                            CBM : <?= $productData['cd_size_fn']['invoice']['cbm'] ?> x 1.25 = (<?= ($productData['cd_size_fn']['invoice']['cbm'] * 1.25) ?>) /
                            <?= ($productData['cd_size_fn']['invoice']['cbm'] * 1.25) ?> x 88,000 = <b><?= number_format(($productData['cd_size_fn']['invoice']['cbm'] * 1.25) * 88000) ?></b>
                            (해운 예상 배송비)
                        </div>
                    <?php } ?>

                    <div>
                        <select name="cost_cal_kind" id="cost_cal_kind" class="m-r-10" onchange="prdInfoPrice.costCalKindChange(this.value)">
                            <option value="">주문종류</option>
                            <option value="중국주문" <?php if (($productData['cd_cost_price_info']['주문종류'] ?? '') == "중국주문") echo "selected"; ?>>중국주문</option>
                            <option value="일본주문" <?php if (($productData['cd_cost_price_info']['주문종류'] ?? '') == "일본주문") echo "selected"; ?>>일본주문</option>
                        </select>

                        주문가 : <input type="text" name="cost_cal_price" id="cost_cal_price" class="width-80 m-r-10" value="<?= $productData['cd_cost_price_info']['주문가'] ?? '' ?>" onkeyUP="prdInfoPrice.costCalculationNew()">
                        적용환율 : <input type="text" name="cost_cal_exchange_rate" id="cost_cal_exchange_rate" class="width-50 m-r-10" value="<?= $productData['cd_cost_price_info']['적용환율'] ?? '' ?>" onkeyUP="prdInfoPrice.costCalculationNew()">
                        <span id="cost_cal_kind_delivery_text">
                            <?php if ((($productData['cd_cost_price_info']['주문종류'] ?? '') == "중국주문")) { ?>
                                개당 배송비
                            <?php } elseif ((($productData['cd_cost_price_info']['주문종류'] ?? '') == "일본주문")) { ?>
                                kg당 배송비
                            <?php } else { ?>
                                배송비
                            <?php } ?>
                        </span> : <input type="text" name="cost_cal_delivery" id="cost_cal_delivery" class="width-80" value="<?= $productData['cd_cost_price_info']['1kg배송비'] ?? '' ?>" onkeyUP="prdInfoPrice.costCalculationNew()">

                        <!-- <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="alert('준비중');" >해외주문 기본값 설정</button> -->

                    </div>
                    <div class="m-t-6">
                        <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdInfoPrice.costCalculationNew()">원가 계산기 실행</button>
                    </div>

                    <div id="cost_calculation_info_new" class="cost-calculation-info m-t-7">
                        <div class="price-f-box">
                            <?php
                            if ($productData['cd_cost_price_info']['관세율'] ?? '') {
                                $_this_cost_cal_tariff = $productData['cd_cost_price_info']['관세율'];
                            } else {
                                $_this_cost_cal_tariff = "6.5";
                            }

                            if ($productData['cd_cost_price_info']['부대비용'] ?? '') {
                                $_this_cal_incidental_cost = $productData['cd_cost_price_info']['부대비용'];
                            } else {
                                $_this_cal_incidental_cost = "1000";
                            }
                            ?>
                            관세율 : <input type="text" name="cost_cal_tariff" id="cost_cal_tariff" class="width-50" value="<?= $_this_cost_cal_tariff ?>">%
                            <span id="incidental_cost_box" style="<?php if ((($productData['cd_cost_price_info']['주문종류'] ?? '') == "일본주문")) { ?>display:none;<?php } ?>">
                                &nbsp; | &nbsp;
                                부대비용 (B/L 문서, 통관수수료, 원산지증명) : <input type="text" name="cost_cal_incidental_cost" id="cost_cal_incidental_cost" class="width-50" value="<?= $_this_cal_incidental_cost ?>">원
                            </span>

                            <!-- 부가세율 :  <input type="text" name="cost_cal_vat" id="cost_cal_vat" class="width-50" value="11">% -->
                        </div>
                        <div class='calculation-show-box m-t-5' id="cost_calculation_detail">
                        </div>
                    </div>

                </td>
            </tr>

            <tr>
                <th>판매가 계산기</th>
                <td>
                    <div>
                        적용할 원가가격 : <input type="text" name="estimated_selling_price" id="estimated_selling_price" value="<?= number_format($productData['cd_cost_price'] ?? 0) ?>" onkeyUP="GC.commaInput( this.value, this ); prdInfoPrice.salePriceCalculation();" class="width-80 m-r-10">
                    </div>
                    <div class="m-t-6">
                        <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdInfoPrice.salePriceCalculation()">판매가 계산기 실행</button>
                    </div>
                    <div class="cost-calculation-info m-t-7">
                        <div id="estimated_selling_price_result" class="calculation-show-box">
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>

        <tbody>
            <tr>
                <td colspan="2" class="none-bg" style="height:20px;"></td>
            </tr>
            <tr>
                <td colspan="2" class="none-bg title">
                    <h1>위탁상품 정보</h1>
                </td>
            </tr>
        </tbody>

        <tbody>
            <tr>
                <th>위탁 상품 고유번호</th>
                <td>
                    <input type='text' name='supplier_prd_idx' style='width:200px;' value="<?= $productData['supplier_prd_idx'] ?? '' ?>">
                    <div class="admin-guide-text">
                        - 매칭할 위탁 상품 고유번호 입니다.
                    </div>
                </td>
            </tr>
        </tbody>

    </table>

</form>

<style type="text/css">
	.button-wrap-back{ height:60px; }
	.button-wrap{ width:calc(100% - 205px); height:60px; line-height:60px; text-align:center; background:rgba(0,0,0,.4); border-top:1px solid #000; position:fixed; bottom:0; right:0;  }
</style>
<div class="button-wrap-back">
</div>
<div class="button-wrap">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdInfoPrice.costSave(this);" >수 정</button>
</div>

<script>

    var prdInfoPrice = function() {

        var yen = <?= $yen ?? 1000 ?>;
        var yen_cn = <?= $yen_cn ?? 193 ?>;
        var kg_p = <?= $kg_p ?? 6000 ?>;
        var cn_delivery_p = <?= $delivery_p_cn ?? 2800 ?>;

        var costCalMsg = function(msg) {
            $("#cost_cal_msg").show().html(msg);
        };

        /**
         * 원가 계산기
         * @return void
         */
        function costCalculationNew() {

            var isValidationOk = "ok";
            var validationMessage = "";

            var orderType = $("#cost_cal_kind").val();
            var foreignOrderAmount = $("#cost_cal_price").val();
            var exchangeRate = $("#cost_cal_exchange_rate").val();

            if (!orderType) {
                isValidationOk = "no";
                validationMessage += "[주문종류]를 선택해주세요.";
            }
            if (!foreignOrderAmount) {
                isValidationOk = "no";
                validationMessage += "<br>[주문가]를 입력해주세요.";
            }
            if (!exchangeRate) {
                isValidationOk = "no";
                validationMessage += "<br>[적용환율]를 입력해주세요.";
            }

            var customsDutyRate = $("#cost_cal_tariff").val();
            if (!customsDutyRate) {
                isValidationOk = "no";
                validationMessage += "<br>관세율 값을 입력해주세요.";
            }

            if (isValidationOk == "no") {
                costCalMsg(validationMessage);
            } else {
                $("#cost_cal_msg").hide();
            }

            var resultHtml = "";

            var normalizeMoney = function(value) {
                var num = Number(value);
                if (!isFinite(num)) {
                    return 0;
                }
                return Math.round(num * 100) / 100;
            };

            var formatMoney = function(value) {
                var normalized = normalizeMoney(value);
                if (Math.floor(normalized) === normalized) {
                    return GC.comma(normalized);
                }
                return normalized.toLocaleString("en-US", {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 2
                });
            };

            // 공통 계산: 실제 주문가를 KRW로 환산한 기준
            var purchaseCostKrw = normalizeMoney(foreignOrderAmount * exchangeRate);
            var normalDutyAmount = Math.round(purchaseCostKrw * (customsDutyRate / 100));
            var normalVatAmount = Math.round((purchaseCostKrw + normalDutyAmount) * 0.1);
            var normalTaxTotal = normalDutyAmount + normalVatAmount;

            var additionalCost = $("#cost_cal_incidental_cost").val() * 1;
            var deliveryCostInput = $("#cost_cal_delivery").val() * 1;

            var additionalCostDisplayText = "";
            var deliveryCostDisplayText = "";

            /*
            if( additionalCost > 0 ){
                normalTaxTotal = normalTaxTotal + additionalCost;
                reducedTaxTotal = reducedTaxTotal + additionalCost;
                additionalCostDisplayText = " | 부대비용 : <b>"+ GC.comma(additionalCost) +"</b>";
            }
            */

            // 중국주문 계산 흐름:
            // 1) 실제 매입가 기준 세금 계산 (normal)
            // 2) 세금 계산용 기준금액만 축소한 시뮬레이션 계산 (reduced)
            if (orderType == "중국주문") {

                var invoiceCbm = $("#invoice_size_cbm").val();
                if (!invoiceCbm) {
                    alert("포장 사이즈의 [CBM]값을 입력해주세요.");
                    return false;
                }

                /*
                var vatRate = $("#cost_cal_vat").val();
                if( !vatRate ){ alert("부가세율 값을 입력해주세요."); return false; }
                */

                // 실제 매입가를 줄이는 의미가 아니라, "세금 계산용 기준금액"만 축소한 내부 시뮬레이션
                var reducedDeclarePurchaseCostKrw = normalizeMoney((foreignOrderAmount * 0.65) * exchangeRate);
                var reducedDutyAmount = Math.round(reducedDeclarePurchaseCostKrw * (customsDutyRate / 100));
                var reducedVatAmount = Math.round((reducedDeclarePurchaseCostKrw + reducedDutyAmount) * 0.1);
                var reducedTaxTotal = reducedDutyAmount + reducedVatAmount;

                if (additionalCost > 0) {
                    normalTaxTotal = normalTaxTotal + additionalCost;
                    reducedTaxTotal = reducedTaxTotal + additionalCost;
                    additionalCostDisplayText = " | 부대비용 : <b>" + GC.comma(additionalCost) + "</b>";
                }

                if (deliveryCostInput > 0) {
                    normalTaxTotal = normalTaxTotal + deliveryCostInput;
                    reducedTaxTotal = reducedTaxTotal + deliveryCostInput;
                    deliveryCostDisplayText = " | 배송비 : <b>" + GC.comma(deliveryCostInput) + "</b>";
                }

                var normalLandedCost = normalizeMoney(purchaseCostKrw + normalTaxTotal);
                var reducedLandedCost = normalizeMoney(purchaseCostKrw + reducedTaxTotal);

                resultHtml += "<ul>(₩)원전환 : ￥<b>" + GC.comma(foreignOrderAmount) + "</b> -> <b class='point2'>" + formatMoney(purchaseCostKrw) + "</b>원 ( 적용환율 : <b>" + exchangeRate + "</b> )</ul>";
                resultHtml += "<ul>정상 원가 기준 | 관세 : <b>" + GC.comma(normalDutyAmount) + "</b> | 부가세 : <b>" + GC.comma(normalVatAmount) + "</b>" + additionalCostDisplayText + deliveryCostDisplayText + " = 합 : <b>" + GC.comma(normalTaxTotal) + "</b>";
                resultHtml += " | 총합 : <b class='point2'>" + formatMoney(normalLandedCost) + "</b>원</ul>";
                resultHtml += "<ul>축소신고 시뮬레이션 기준 | 관세 : <b>" + GC.comma(reducedDutyAmount) + "</b> | 부가세 : <b>" + GC.comma(reducedVatAmount) + "</b>" + additionalCostDisplayText + deliveryCostDisplayText + " = 합 : <b>" + GC.comma(reducedTaxTotal) + "</b>";
                resultHtml += " | 총합 : <b class='point2'>" + formatMoney(reducedLandedCost) + "</b>원</ul>";

            // 일본주문 계산 흐름 :
            // 1) 중량(실측 우선, 없으면 전체중량)으로 배송비 계산
            // 2) 관세/부가세/배송비를 합산하여 총원가 계산
            } else if (orderType == "일본주문") {

                if (!deliveryCostInput) {
                    $("#cost_cal_delivery").focus();
                    alert("배송비 값을 입력해주세요. 배송비값은 kg당 배송비 입니다.");
                    return false;
                }

                var totalWeightG = $("#cd_weight_2").val();
                var measuredWeightG = $("#cd_weight_3").val();

                /*
                if( !measuredWeightG ){ alert("실측중량 정보가 없습니다."); return false; }
                */
                var appliedWeightG = "";
                if (measuredWeightG == "" && totalWeightG > 0) {

                    appliedWeightG = totalWeightG;
                    resultHtml += "<input type='hidden' name='weight_mode' value='전체중량' >";
                    resultHtml += "<input type='hidden' name='weight' value='" + totalWeightG + "' >";

                } else if (measuredWeightG > 0) {

                    appliedWeightG = measuredWeightG;
                    resultHtml += "<input type='hidden' name='weight_mode' value='실측중량' >";
                    resultHtml += "<input type='hidden' name='weight' value='" + measuredWeightG + "' >";

                }

                if (deliveryCostInput > 0) {

                    var deliveryCostByWeight = Math.round(appliedWeightG * (deliveryCostInput * 0.001)); // 배송비

                    normalVatAmount = Math.round((purchaseCostKrw + normalDutyAmount + deliveryCostByWeight) * 0.1);
                    normalTaxTotal = normalDutyAmount + normalVatAmount + deliveryCostByWeight;
                    deliveryCostDisplayText = " | 배송비 : <b>" + GC.comma(deliveryCostByWeight) + "</b>( kg/" + GC.comma(deliveryCostInput) + " )";
                }


                var japanLandedCost = normalizeMoney(purchaseCostKrw + normalTaxTotal);

                resultHtml += "<ul>(₩)원전환 : ￥<b>" + GC.comma(foreignOrderAmount) + "</b> -> <b class='point2'>" + formatMoney(purchaseCostKrw) + "</b>원 ( 적용환율 : <b>" + exchangeRate + "</b> )</ul>";
                resultHtml += "<ul>정상 원가 기준 | 관세 : <b>" + GC.comma(normalDutyAmount) + "</b> | 부가세 : <b>" + GC.comma(normalVatAmount) + "</b>" + additionalCostDisplayText + deliveryCostDisplayText + " = 합 : <b>" + GC.comma(normalTaxTotal) + "</b></ul>";
                resultHtml += "<ul>총합 : <b class='point2'>" + formatMoney(japanLandedCost) + "</b>원</ul>";
                resultHtml += "<ul><button type='button' id='' class='btnstyle1 btnstyle1-xs' onclick='prdInfoPrice.goCostPrice(" + japanLandedCost + ")' >총합 원가로 입력</button></ul>";

            }

            $("#cost_calculation_detail").html(resultHtml);

        }

        /**
         * 판매가 계산기
         * @return void
         */
        function salePriceCalculation() {

            var estimatedSellingPrice = $("#estimated_selling_price").val();
            estimatedSellingPrice = GC.uncomma(estimatedSellingPrice);
            if (!estimatedSellingPrice) {
                $("#estimated_selling_price").focus();
                alert("판매할 원가를 입력해주세요.");
                return false;
            }

            var inst_arr = [50, 45, 40, 35, 30, 25, 20, 15, 10, 5];

            var _html = "";

            for (var i = 0; i < inst_arr.length; i++) {
                var inst_per = 1 - (inst_arr[i] / 100);
                var inst_value = Math.round(estimatedSellingPrice / inst_per);

                _html += "<ul>예상 판매가 ( " + inst_arr[i] + "% ) <b>" + GC.comma(inst_value) + "</b>원 | 마진 <b>" + GC.comma(inst_value - estimatedSellingPrice) + "</b>원";
                if (inst_value >= 30000) {
                    _html += " | 3만무배 <b>" + GC.comma((inst_value - estimatedSellingPrice) - 2500) + "</b>원";
                }
                _html += "</ul>";

            } //for END

            $("#estimated_selling_price_result").html(_html);

        }

        /**
         * 매입정보 저장
         * @return void
         */
        function costSave() {

            var purchaseType = $(':input:radio[name=cd_national]:checked').val();
            if (!purchaseType) {
                alert("매입 방식을 선택해주세요.");
                return false;
            }

            var salePrice = GC.uncomma($("input[name='cd_sale_price']").val() || "");
            if (!salePrice) {
                $("input[name='cd_sale_price']").focus();
                alert("쑈당몰 판매가를 입력해주세요.");
                return false;
            }

            var costPrice = GC.uncomma($("#cd_cost_price").val() || "");
            if (!costPrice) {
                $("#cd_cost_price").focus();
                alert("원가를 입력해주세요.");
                return false;
            }

            var vatType = $("input[name='cd_cost_price_vat']:checked").val();
            if (!vatType) {
                alert("VAT 포함/미포함을 선택해주세요.");
                return false;
            }

            var formData = $("#prd_price_form").serializeArray();

            $.ajax({
                url: "/admin/product/saveProductPrice",
                data: formData,
                type: "POST",
                dataType: "json",
                success: function(res) {
                    if (res.success == true) {
                        toast2("success", "상품가격정보", "상품가격정보 변경완료");
                        prdView.mode('price');
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
                    //$(obj).attr('disabled', false);
                }
            });

        }

        function toggleCostCalculatorVisibility() {

            var purchaseType = $(':input:radio[name=cd_national]:checked').val() || "";
            var isJapanOrChina = (purchaseType === "jp" || purchaseType === "cn");

            var hasSavedOrInputValue = false;
            var checkSelectors = [
                "#cost_cal_kind",
                "#cost_cal_price",
                "#cost_cal_exchange_rate",
                "#cost_cal_delivery",
                "#cost_cal_tariff",
                "#cost_cal_incidental_cost"
            ];
            for (var i = 0; i < checkSelectors.length; i++) {
                var checkValue = $(checkSelectors[i]).val();
                if (checkValue !== undefined && checkValue !== null && String(checkValue).trim() !== "") {
                    hasSavedOrInputValue = true;
                    break;
                }
            }

            // 매입방식을 선택한 경우: 선택값을 우선한다 (jp/cn만 노출)
            if (purchaseType !== "") {
                if (isJapanOrChina) {
                    $("#cost-calculator-row").show();
                } else {
                    $("#cost-calculator-row").hide();
                }
                return;
            }

            // 매입방식이 비어있는 초기 상태에서만 저장된 계산기 값 기준으로 노출
            if (hasSavedOrInputValue) {
                $("#cost-calculator-row").show();
            } else {
                $("#cost-calculator-row").hide();
            }
        }

        return {

            costCalculationNew,
            salePriceCalculation,
            costSave,
            toggleCostCalculatorVisibility,
            changeValue: function(mode, v) { //prdInfoPrice.changeValue
                if (mode == "yen_cn") {
                    yen_cn = v;
                } else if (mode == "cn_delivery_p") {
                    cn_delivery_p = GC.uncomma(v);
                }
                prdInfoPrice.costCalculationNew();
            },

            //주문서 가격 변경
            orderPriceChange: function(v) {
                $("#cost_cal_price").val(v);

                var purchaseType = $(':input:radio[name=cd_national]:checked').val() || "";
                if (purchaseType === "kr") {
                    var numericOrderPrice = GC.uncomma(v);
                    if (numericOrderPrice) {
                        $("#cd_cost_price").val(GC.comma(numericOrderPrice));
                    }
                }

                prdInfoPrice.costCalculationNew();
            },

            //원가로 입력
            goCostPrice: function(v) {

                $("#cd_cost_price").val(GC.comma(v));
                $("#estimated_selling_price").val(GC.comma(v));
                prdInfoPrice.salePriceCalculation();

            },

            //주문종류 체인지
            costCalKindChange: function(v) {

                if (v == "중국주문") {
                    $("#cost_cal_kind_delivery_text").html('개당 배송비');
                    $("#incidental_cost_box").show();
                } else if (v == "일본주문") {
                    $("#cost_cal_kind_delivery_text").html('kg당 배송비');
                    $("#incidental_cost_box").hide();
                } else {
                    $("#cost_cal_kind_delivery_text").html('배송비');
                    $("#incidental_cost_box").show();
                }

            },
            costCalculationSave: function() {

                var formData = $("#form1").serializeArray();
                formData.push({
                    name: "a_mode",
                    value: "costCalculationSave"
                });

                $.ajax({
                    url: "/ad/processing/prd",
                    data: formData,
                    type: "POST",
                    dataType: "json",
                    success: function(res) {
                        if (res.success == true) {
                            toast2("success", "상품가격정보", "상품가격정보 변경완료");
                            prdView.mode('price');
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
                        //$(obj).attr('disabled', false);
                    }
                });

            },

        };

    }();

    //prdInfoPrice.costCalculationNew();

    <?php
        if (($productData['cd_cost_price_info']['주문종류'] ?? '') && ($productData['cd_cost_price_info']['주문가'] ?? '') && ($productData['cd_cost_price_info']['적용환율'] ?? '')) {
    ?>
        prdInfoPrice.costCalculationNew();
    <?php } ?>

    $(function() {
        prdInfoPrice.toggleCostCalculatorVisibility();
        $(':input:radio[name=cd_national]').on('change', function() {
            prdInfoPrice.toggleCostCalculatorVisibility();
        });
    });
</script>