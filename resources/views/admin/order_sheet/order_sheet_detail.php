<?php
$orderSheetMain = (isset($orderSheetMain) && is_array($orderSheetMain)) ? $orderSheetMain : [];
$groupSideRows = (isset($groupSideRows) && is_array($groupSideRows)) ? $groupSideRows : [];
$idx = isset($idx) ? (int)$idx : 0;
$open_oop_idx = isset($open_oop_idx) ? (string)$open_oop_idx : '';
$form_view = isset($form_view) ? (string)$form_view : 'show';
?>

<script type="text/javascript">
var oogBrand = <?= json_encode($groupSideRows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<style>
.order-sheet-product-loading {
    position: absolute;
    inset: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.86);
}

.order-sheet-product-loading[hidden] {
    display: none !important;
}

.order-sheet-product-loading__box {
    min-width: 240px;
    padding: 24px 28px;
    border: 1px solid #d7dce5;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    color: #333;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
}

.order-sheet-product-loading__spinner {
    width: 28px;
    height: 28px;
    margin: 0 auto 12px;
    border: 3px solid #d9e2f3;
    border-top-color: #1769d2;
    border-radius: 50%;
    animation: order-sheet-product-loading-spin 0.8s linear infinite;
}

@keyframes order-sheet-product-loading-spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
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

    .row-order-cell {
        text-align: center;
        vertical-align: middle;
        cursor: ns-resize;
        user-select: none;
        touch-action: pan-y;
    }

    .row-order-handle {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 52px;
        border: 1px solid #d8deea;
        border-radius: 4px;
        background: #f7f9fc;
        color: #60708f;
    }

    .row-order-handle::before,
    .row-order-handle::after {
        content: "";
        position: absolute;
        top: 8px;
        bottom: 8px;
        width: 2px;
        border-radius: 2px;
        background: repeating-linear-gradient(
            to bottom,
            #9ba8c2 0,
            #9ba8c2 2px,
            transparent 2px,
            transparent 5px
        );
    }

    .row-order-handle::before {
        left: 5px;
    }

    .row-order-handle::after {
        right: 5px;
    }

    .row-order-icon {
        position: relative;
        z-index: 1;
        font-size: 11px;
        line-height: 1;
    }

    .row-sort-active {
        box-shadow: inset 0 0 0 2px #6f89ff;
    }

    .row-sort-placeholder td {
        background: #eef3ff !important;
        border-top: 1px dashed #91a7e8;
        border-bottom: 1px dashed #91a7e8;
        height: 44px;
    }

    .row-context-selected {
        outline: 2px solid #5e78f0;
        outline-offset: -2px;
    }

    .row-context-menu {
        position: fixed;
        z-index: 9999;
        min-width: 150px;
        background: #fff;
        border: 1px solid #cdd5e5;
        border-radius: 6px;
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.14);
        padding: 4px 0;
    }

    .row-context-menu[hidden] {
        display: none;
    }

    .row-context-menu button {
        display: flex;
        align-items: center;
        gap: 7px;
        width: 100%;
        border: 0;
        background: transparent;
        text-align: left;
        padding: 8px 12px;
        font-size: 12px;
        color: #1f2c47;
        cursor: pointer;
    }

    .row-context-menu button:hover {
        background: #edf3ff;
    }

    .row-context-menu button.active {
        background: #e7efff;
        color: #1a397a;
        font-weight: 600;
    }

    .row-context-menu button + button {
        border-top: 1px solid #eef1f6;
    }

    #orderSheetRowMoveGroupList {
        max-height: 252px; /* 약 7개 항목 */
        overflow-y: auto;
    }

    #orderSheetRowMoveGroupList:not(:empty) {
        border-top: 1px solid #eef1f6;
    }

    .row-context-menu-icon {
        display: inline-flex;
        width: 14px;
        color: #4c5f86;
        justify-content: center;
        font-size: 12px;
        line-height: 1;
    }

    .other-form-register-modal {
        position: fixed;
        inset: 0;
        z-index: 10020;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(20, 29, 48, 0.48);
    }

    .other-form-register-modal[hidden] {
        display: none;
    }

    .other-form-register-dialog {
        width: min(520px, 100%);
        max-height: min(680px, calc(100vh - 40px));
        overflow: hidden;
        background: #fff;
        border-radius: 9px;
        box-shadow: 0 18px 48px rgba(0, 0, 0, 0.24);
    }

    .other-form-register-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-bottom: 1px solid #e4e8f0;
    }

    .other-form-register-header h3 {
        margin: 0;
        font-size: 16px;
        color: #17233b;
    }

    .other-form-register-close {
        border: 0;
        background: transparent;
        color: #68758e;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
    }

    .other-form-register-body {
        max-height: calc(min(680px, 100vh - 40px) - 58px);
        overflow-y: auto;
        padding: 16px;
    }

    .other-form-register-guide {
        margin: 0 0 12px;
        color: #55627a;
        font-size: 12px;
    }

    .other-form-register-list {
        display: grid;
        gap: 7px;
    }

    .other-form-register-list button {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #dce2ed;
        border-radius: 6px;
        background: #fff;
        color: #24324d;
        text-align: left;
        cursor: pointer;
    }

    .other-form-register-list button:hover {
        border-color: #7189e8;
        background: #f4f7ff;
    }

    .other-form-register-list button:disabled {
        cursor: not-allowed;
        opacity: 0.62;
    }

    .other-form-register-count {
        flex: none;
        margin-left: 12px;
        color: #74819a;
        font-size: 11px;
    }

    .other-form-register-back {
        margin: 0 0 12px;
        border: 0;
        background: transparent;
        color: #4863c6;
        cursor: pointer;
    }

    .other-form-register-selected {
        margin: 0 0 12px;
        font-size: 14px;
        color: #17233b;
    }

    .other-form-register-duplicate {
        padding: 12px;
        border: 1px solid #f0c6c6;
        border-radius: 6px;
        background: #fff4f4;
        color: #b32626;
        font-size: 12px;
    }

    .other-form-register-new {
        display: flex;
        gap: 7px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #e7eaf0;
    }

    .other-form-register-new input {
        min-width: 0;
        flex: 1;
        height: 34px;
        padding: 0 10px;
        border: 1px solid #cfd6e3;
        border-radius: 5px;
    }

    .other-form-register-new button {
        flex: none;
    }

    .other-form-register-empty,
    .other-form-register-loading {
        padding: 26px 12px;
        color: #6b7890;
        text-align: center;
        font-size: 12px;
    }

    .weight-sum-wrap{
        ul{
            font-size: 11px;
        }
    }

    .stock-zero-filter.is-active {
        background: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }

    .ospl-prd-wrap .table-st1 tbody > tr.stock-zero-filter-hidden {
        display: none !important;
    }

    .order-product-memo-cell {
        position: relative;
        min-width: 150px;
        padding: 0 !important;
        vertical-align: top;
    }

    .order-product-memo-display,
    .order-product-memo-editor {
        width: 100%;
        box-sizing: border-box;
        padding: 6px;
        margin: 0;
        color: #ff0000;
        background: transparent;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .order-product-memo-display {
        cursor: text;
    }

    .order-product-memo-display.is-empty {
        color: #bbb;
        font-size: 11px;
        font-weight: 400;
    }

    .order-product-memo-editor {
        position: absolute;
        inset: 0;
        z-index: 1;
        display: block;
        height: 100%;
        min-height: 0;
        border: 1px solid #7892d4 !important;
        outline: none;
        resize: vertical;
        visibility: hidden;
        opacity: 0;
        pointer-events: none;
    }

    .order-product-memo-cell.is-editing .order-product-memo-display {
        visibility: hidden;
    }

    .order-product-memo-cell.is-editing .order-product-memo-editor {
        visibility: visible;
        opacity: 1;
        pointer-events: auto;
    }

    .order-product-memo-status {
        position: absolute;
        z-index: 2;
        right: 5px;
        bottom: 3px;
        color: #777;
        font-size: 10px;
        pointer-events: none;
    }

    .order-product-memo-status.is-error {
        color: #d60000;
    }

    .order-sheet-restock-overlay {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(20, 28, 45, 0.58);
        color: #fff;
    }

    .order-sheet-restock-overlay[hidden] {
        display: none;
    }

    .order-sheet-restock-overlay__message {
        min-width: 280px;
        padding: 28px 32px;
        border-radius: 8px;
        background: #1f2c47;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        font-size: 14px;
        font-weight: 600;
        text-align: center;
    }
</style>
<div class="order_sheet_detail">
    <ul class="left">
        <div class="overflow-y">
            <?php foreach ($groupSideRows as $groupRow) { ?>
                <div class="ost-big <?= !empty($groupRow['has_order']) ? 'inorder' : '' ?>" id="group_side_<?= $groupRow['oop_idx'] ?? '' ?>" onclick="orderSheetDetail.PrdList('<?= $idx ?>', '<?= $groupRow['oop_idx'] ?? '' ?>')">
                    <ul><b><?= $groupRow['name'] ?? '' ?></b></ul>
                    <ul class="m-t-3">
                        <b><?= (int)($groupRow['oop_total_count'] ?? 0) ?></b> /
                        <span class="oprice-sum-goods-cate" id="oprice_sum_goods_<?= $groupRow['oop_idx'] ?? '' ?>"><?= (int)($groupRow['item'] ?? 0) ?></span>
                        <?php if ((int)($groupRow['false'] ?? 0) > 0) { ?>
                            <span>실패 : <?= (int)($groupRow['false'] ?? 0) ?></span>
                        <?php } ?>
                    </ul>
                    <ul>
                        <span class="oprice-sum-qty" id="group_side_sum_qty_<?= $groupRow['oop_idx'] ?? '' ?>" data-value="<?= number_format((float)($groupRow['qty'] ?? 0)) ?>"><?= number_format((float)($groupRow['qty'] ?? 0)) ?></span> /
                        <span class="oprice-sum-weight" id="oprice_sum_weight_<?= $groupRow['oop_idx'] ?? '' ?>"><?= $groupRow['show_weight'] ?? '' ?></span>
                    </ul>
                    <ul><span class="group-side-allsum-price" id="oprice_allsum_<?= $groupRow['oop_idx'] ?? '' ?>"><?= number_format((float)($groupRow['price'] ?? 0), 2) ?></span></ul>
                </div>
            <?php } ?>
        </div>
    </ul>
    <ul class="right">
        <div id="order_sheet_detail_prd_list" class="order-sheet-detail-prd-list">
            <div id="order_sheet_product_loading" class="order-sheet-product-loading" hidden>
                <div class="order-sheet-product-loading__box" role="status" aria-live="polite">
                    <div class="order-sheet-product-loading__spinner" aria-hidden="true"></div>
                    상품 데이터를 불러오는 중입니다.
                </div>
            </div>
            <div id="order_sheet_detail_prd_content"></div>
        </div>
    </ul>
</div>

<script>

var orderSheetDetail = function() {

	var detailDisplay;
	var normalFormView = "<?= $form_view ?>";
	var gState = "normal";
	var open_idx = "<?= $idx ?>";
	var open_oop_idx = "";
	var productListRequest = null;
	var productListRequestSeq = 0;
	var productListLoadingTimer = null;

	var ckTr = function( id, mode ) {

		if( mode == "on" ){
			$("#tr_"+ id +" td").css({'background':'#ffcbcb' }); 
			$("#checkbox_"+ id).prop("checked", true);
		}else{
			var beforetrcolor = $("#tr_"+ id).attr("bgcolor");
			$("#tr_"+ id +" td").css({'background':beforetrcolor }); 
			$("#checkbox_"+ id).prop("checked", false);
		}

	};

	//열려있는 그룹 합 재계산
	var groupSum = function (oop_idx) {

		var oprice_allsum = 0;
		var oprice_sum_qty = 0;
		var oprice_sum_goods = 0;
		var oprice_sum_weight = 0;
			var toNumber = function(value){
				var normalized = String(value ?? '').replace(/,/g, '').trim();
				var n = parseFloat(normalized);
				return isFinite(n) ? n : 0;
			};
			var formatDecimalForView = function(value, precision){
				var p = (typeof precision === 'number') ? precision : 2;
				var rounded = Math.round((toNumber(value) + Number.EPSILON) * Math.pow(10, p)) / Math.pow(10, p);
				var text = rounded.toFixed(p);
				if (p > 0 && text.slice(-(p + 1)) === '.' + '0'.repeat(p)) {
					text = rounded.toFixed(0);
				}
				var parts = text.split('.');
				parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
				return parts.length > 1 ? parts[0] + '.' + parts[1] : parts[0];
			};

		$(".checkSelect:checked").each(function(){

			var checkbox_id = $(this).val();

			if( $("#unit_qty_" + checkbox_id).val() == "" ){
				var plus_oprice_sum_qty = 1;
			}else{
				var plus_oprice_sum_qty = toNumber($("#unit_qty_" + checkbox_id).val());
			}

			oprice_allsum = oprice_allsum + toNumber($("#unit_price_sum_" + checkbox_id).val());
			oprice_sum_goods++;
			oprice_sum_qty = oprice_sum_qty + plus_oprice_sum_qty;
			oprice_sum_weight = oprice_sum_weight + (toNumber($("#weight_"+checkbox_id).data('weight')) * plus_oprice_sum_qty);

		});


		$("#oprice_allsum_" + oop_idx).html(formatDecimalForView(oprice_allsum, 2));
		$("#oprice_allsum_data_" + oop_idx).val(oprice_allsum);

		//선택상품
		$("#oprice_sum_goods_" + oop_idx ).html(GC.comma(oprice_sum_goods));
		$("#oprice_sum_goods_data_" + oop_idx ).val(oprice_sum_goods);
		if( $("#group_body_sum_goods_" + oop_idx).length ){ $("#group_body_sum_goods_" + oop_idx).html(GC.comma(oprice_sum_goods)); }


		//총 수량
		$("#group_side_sum_qty_" + oop_idx).html(GC.comma(oprice_sum_qty));
		$("#oprice_sum_qty_data_" + oop_idx).val(oprice_sum_qty);
		if( $("#group_body_sum_qty_" + oop_idx).length ){ $("#group_body_sum_qty_" + oop_idx).html(GC.comma(oprice_sum_qty));  }


		//총 무게
		if( oprice_sum_weight > 1000 ){
			var show_oprice_sum_weight = GC.comma(Math.round((oprice_sum_weight*0.001) * 100) / 100 )+"kg";
		}else{
			var show_oprice_sum_weight = GC.comma(Math.round(oprice_sum_weight))+"g";
		}

		$("#oprice_sum_weight_" + oop_idx).html(show_oprice_sum_weight);
		$("#oprice_sum_weight_data_" + oop_idx).val(oprice_sum_weight);
		if( $("#group_body_sum_weight_" + oop_idx).length ){ $("#group_body_sum_weight_" + oop_idx).html(show_oprice_sum_weight);  }

		// 하단 footer 총합(총수량/합가격) 동기화: 체크항목이 아닌 "현재 전체 라인" 기준으로 계산
		if (
			$("#order_sheet_total_qty").length ||
			$("#order_sheet_total_sum_price").length ||
			$("#order_sheet_pay_total_sum_price").length ||
			$("#order_sheet_total_weight_sum_kg").length
		) {
			var footerTotalQty = 0;
			var footerTotalSum = 0;
			var footerPayTotalSum = 0;
			var footerTotalWeightSumKg = 0;
			var isFalseByRowId = function(rowId){
				return String($("#unit_qty_" + rowId).data('is-false') || '0') === '1';
			};

			$(".qty-input").each(function(){
				if (String($(this).data('is-false') || '0') === '1') {
					return;
				}
				var qty = toNumber($(this).val());
				footerTotalQty += qty;

				var inputId = String($(this).attr('id') || '');
				var rowId = inputId.replace('unit_qty_', '');
				if (rowId !== '') {
					var unitWeight = toNumber($("#weight_" + rowId).data('weight'));
					if (unitWeight > 0 && qty > 0) {
						footerTotalWeightSumKg += (unitWeight * qty) / 1000;
					}
				}
			});

			$(".unit-price-sum-data").each(function(){
				var thisId = String($(this).attr('id') || '');
				var rowId = thisId.replace('unit_price_sum_', '');
				if (rowId !== '' && isFalseByRowId(rowId)) {
					return;
				}
				footerTotalSum += toNumber($(this).val());
			});
			$(".pay-unit-price-sum-data").each(function(){
				var thisId = String($(this).attr('id') || '');
				var rowId = thisId.replace('pay_unit_price_sum_', '');
				if (rowId !== '' && isFalseByRowId(rowId)) {
					return;
				}
				footerPayTotalSum += toNumber($(this).val());
			});

			if ($("#order_sheet_total_qty").length) {
				$("#order_sheet_total_qty").html(GC.comma(footerTotalQty));
			}
			if ($("#order_sheet_total_sum_price").length) {
				var totalSumRounded = Math.round((footerTotalSum + Number.EPSILON) * 100) / 100;
				var totalSumText = totalSumRounded.toFixed(2);
				if (totalSumText.slice(-3) === '.00') {
					totalSumText = totalSumRounded.toFixed(0);
				}
				$("#order_sheet_total_sum_price").html(GC.comma(totalSumText));

				var $totalWonEl = $("#order_sheet_total_sum_price_won");
				if ($totalWonEl.length > 0) {
					var footerExchangeRateRaw = String($totalWonEl.data('exchangerate') || '0');
					var footerCurrency = String($totalWonEl.data('currency') || '');
					var footerExchangeRate = parseFloat(footerExchangeRateRaw.replace(/,/g, "")) || 0;
					if (footerExchangeRate > 0) {
						var footerWonRaw = totalSumRounded * footerExchangeRate;
						if (footerCurrency === '엔' || footerCurrency.toUpperCase() === 'JPY') {
							footerWonRaw = footerWonRaw / 100;
						}
						$totalWonEl.html(GC.comma(Math.round(footerWonRaw)));
					} else {
						$totalWonEl.html("");
					}
				}
			}
			if ($("#order_sheet_pay_total_sum_price").length) {
				var payTotalSumRounded = Math.round((footerPayTotalSum + Number.EPSILON) * 100) / 100;
				var payTotalSumText = payTotalSumRounded.toFixed(2);
				if (payTotalSumText.slice(-3) === '.00') {
					payTotalSumText = payTotalSumRounded.toFixed(0);
				}
				$("#order_sheet_pay_total_sum_price").html(GC.comma(payTotalSumText));

				var $payTotalWonEl = $("#order_sheet_pay_total_sum_price_won");
				if ($payTotalWonEl.length > 0) {
					var payFooterExchangeRateRaw = String($payTotalWonEl.data('exchangerate') || '0');
					var payFooterCurrency = String($payTotalWonEl.data('currency') || '');
					var payFooterExchangeRate = parseFloat(payFooterExchangeRateRaw.replace(/,/g, "")) || 0;
					if (payFooterExchangeRate > 0) {
						var payFooterWonRaw = payTotalSumRounded * payFooterExchangeRate;
						if (payFooterCurrency === '엔' || payFooterCurrency.toUpperCase() === 'JPY') {
							payFooterWonRaw = payFooterWonRaw / 100;
						}
						$payTotalWonEl.html(GC.comma(Math.round(payFooterWonRaw)));
					} else {
						$payTotalWonEl.html("");
					}
				}
			}
			if ($("#order_sheet_total_weight_sum_kg").length) {
				var totalWeightSumKgRounded = Math.round((footerTotalWeightSumKg + Number.EPSILON) * 1000) / 1000;
				var totalWeightSumKgText = totalWeightSumKgRounded.toFixed(3).replace(/\.?0+$/, '');
				$("#order_sheet_total_weight_sum_kg").html(GC.comma(totalWeightSumKgText));
			}
		}

		var bw1 = $("#group_side_sum_qty_"+ detailDisplay).html() * 1;
		var bw2 = $("#group_side_sum_qty_"+ detailDisplay).data('value') * 1;

		//변경된 값이 있는지 체크
		if(  bw1 != bw2 ){
			orderSheetDetail.groupState("ing");
		}else if(  bw1 == bw2 ){
			orderSheetDetail.groupState("normal");
		}

	}


	var showPrdList = function ( oo_idx, oop_idx, form_view ) {

		if( !form_view ) form_view = normalFormView;

		$(".ost-big").removeClass('active');
		$("#group_side_" + oop_idx).addClass('active');
		if (
			window.orderSheetRealtime
			&& typeof window.orderSheetRealtime.setActiveFormGroup === "function"
		) {
			window.orderSheetRealtime.setActiveFormGroup(oo_idx, oop_idx);
		}

		productListRequestSeq++;
		var requestSeq = productListRequestSeq;
		var $loadingLayer = $("#order_sheet_product_loading");

		if (productListLoadingTimer) {
			clearTimeout(productListLoadingTimer);
			productListLoadingTimer = null;
		}
		if (productListRequest && productListRequest.readyState !== 4) {
			productListRequest.abort();
		}

		// 빠른 응답에는 로딩 레이어가 깜빡이지 않도록 잠시 지연해서 표시한다.
		productListLoadingTimer = setTimeout(function() {
			if (requestSeq === productListRequestSeq) {
				$loadingLayer.prop("hidden", false);
			}
		}, 250);

		productListRequest = $.ajax({
			url: "/admin/order/sheet/detail_product",
			data : { "oo_idx" : oo_idx, "oop_idx" : oop_idx, "form_view" : form_view  },
			type: "POST",
			dataType: "html",
			success: function(html){
				if (requestSeq === productListRequestSeq) {
					$("#order_sheet_detail_prd_content").html(html);
				}
			},
			error: function(request, status, error){
				if (status === "abort" || requestSeq !== productListRequestSeq) {
					return;
				}
				console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				showAlert("Error", "에러", "alert2" );
				return false;
			},
			complete: function(){
				if (requestSeq !== productListRequestSeq) {
					return;
				}
				if (productListLoadingTimer) {
					clearTimeout(productListLoadingTimer);
					productListLoadingTimer = null;
				}
				$loadingLayer.prop("hidden", true);
				productListRequest = null;
			}
		});

	}


	return {

		init : function() {

		},

		// 디테일 상품리스트
		PrdList: function( oo_idx, oop_idx ) {
			
			open_oop_idx = oop_idx;
			var showPrdListAction = true;

			if( detailDisplay ){
				if( detailDisplay == oop_idx ){
					return false;
				
				}else if( detailDisplay != oop_idx ){
				
					if( gState == "ing" ){
						
						showPrdListAction = false;
						var save_oop_idx = detailDisplay;

						$.confirm({
							icon: 'fas fa-exclamation-triangle',
							title: '그룹 변경된 내용이 있습니다.',
							content: '( 그룹 idx : '+ save_oop_idx +' )<br>변경된 내용을 저장후 그룹이동을 하시겠습니까?',
							type: 'red',
							typeAnimated: true,
							closeIcon: true,
							buttons: {
								somethingElse: {
									text: '저장',
									btnClass: 'btn-red',
									action: function(){
										orderSheetDetailPrd.groupOrder(oo_idx, save_oop_idx);
										showPrdList(oo_idx, oop_idx);
									}
								},
								cencle: {
									text: '취소',
									action: function(){
										showPrdList(oo_idx, oop_idx);
										gState = "normal";
									}
								}
							}
						});

					}else{
						gState = "normal";
					}

				}
			}

			if( showPrdListAction == true ){
				showPrdList(oo_idx, oop_idx);
			}

			detailDisplay = oop_idx;
			orderSheetDetail.groupState("normal");

		},

		PrdListReload: function( ) {
			showPrdList(open_idx, open_oop_idx);
		},

		// 디테일 상품리스트 따로 호출
		prdListShow: function( oo_idx, oop_idx, form_view ) {
			showPrdList(oo_idx, oop_idx, form_view);
		},

		quantityEditing: function( oop_idx ) {
			if (
				window.orderSheetRealtime
				&& typeof window.orderSheetRealtime.markQuantityEditing === "function"
			) {
				window.orderSheetRealtime.markQuantityEditing(open_idx, oop_idx);
			}
		},

		//수량 변경
		qtyGogo : function( id, oop_idx ) {

			var oprice = $("#unit_price_"+ id).val();
			var payOprice = $("#pay_unit_price_"+ id).val();
			var $qtyInput = $("#unit_qty_"+ id);
			var v = $qtyInput.val();
			var isFalseRow = String($qtyInput.data('is-false') || '0') === '1';
			var unitWeight = parseFloat(String($("#weight_" + id).data('weight') || 0).replace(/,/g, "")) || 0;
			oprice = parseFloat(String(oprice).replace(/,/g, "")) || 0;
			payOprice = parseFloat(String(payOprice).replace(/,/g, "")) || 0;
			v = parseFloat(String(v).replace(/,/g, "")) || 0;

			if (isFalseRow) {
				groupSum(oop_idx);
				return;
			}

			if(v=="") v=0;
			if( oprice > 0 && v > 0 ) {	
				var oprice_sum = Math.round(((oprice * v) + Number.EPSILON) * 100) / 100;
				if( oprice_sum > 0 ){
					var oprice_sum_text = oprice_sum.toFixed(2);
					if (oprice_sum_text.slice(-3) === '.00') {
						oprice_sum_text = oprice_sum.toFixed(0);
					}
					$("#unit_price_sum_"+ id).val(oprice_sum_text);
					$("#order_qty_sum_"+ id).html(GC.comma(oprice_sum_text));

					var $sumWonEl = $("#order_qty_sum_won_" + id);
					if ($sumWonEl.length > 0) {
						var exchangeRateRaw = String($sumWonEl.data('exchangerate') || '0');
						var currency = String($sumWonEl.data('currency') || '');
						var exchangeRate = parseFloat(exchangeRateRaw.replace(/,/g, "")) || 0;
						if (exchangeRate > 0) {
							var wonSumRaw = oprice_sum * exchangeRate;
							if (currency === '엔' || currency.toUpperCase() === 'JPY') {
								wonSumRaw = wonSumRaw / 100;
							}
							$sumWonEl.html(GC.comma(Math.round(wonSumRaw)));
						} else {
							$sumWonEl.html("");
						}
					}

					ckTr(id,"on");
				}
			}else{
				$("#unit_price_sum_"+ id).val("");
				$("#order_qty_sum_"+ id).html("");
				var $sumWonEl = $("#order_qty_sum_won_" + id);
				if ($sumWonEl.length > 0) {
					$sumWonEl.html("");
				}
				ckTr(id,"off");
			}

			if ($("#weight_sum_" + id).length > 0 || $("#weight_sum_kg_" + id).length > 0) {
				var weightSum = 0;
				var weightSumKg = 0;
				if (unitWeight > 0 && v > 0) {
					weightSum = Math.round((unitWeight * v) * 100) / 100;
					weightSumKg = Math.round(((weightSum / 1000) + Number.EPSILON) * 100) / 100;
				}
				if ($("#weight_sum_" + id).length > 0) {
					$("#weight_sum_" + id).html(GC.comma(weightSum));
				}
				if ($("#weight_sum_kg_" + id).length > 0) {
					$("#weight_sum_kg_" + id).html(GC.comma(weightSumKg));
				}
			}

			var hasPayPriceTargets =
				$("#pay_unit_price_sum_" + id).length > 0 ||
				$("#pay_order_qty_sum_" + id).length > 0 ||
				$("#pay_order_qty_sum_won_" + id).length > 0;
			if (hasPayPriceTargets) {
				if (payOprice > 0 && v > 0) {
					var payOpriceSum = Math.round(((payOprice * v) + Number.EPSILON) * 100) / 100;
					if (payOpriceSum > 0) {
						var payOpriceSumText = payOpriceSum.toFixed(2);
						if (payOpriceSumText.slice(-3) === '.00') {
							payOpriceSumText = payOpriceSum.toFixed(0);
						}
						$("#pay_unit_price_sum_" + id).val(payOpriceSumText);
						$("#pay_order_qty_sum_" + id).html(GC.comma(payOpriceSumText));

						var $paySumWonEl = $("#pay_order_qty_sum_won_" + id);
						if ($paySumWonEl.length > 0) {
							var payExchangeRateRaw = String($paySumWonEl.data('exchangerate') || '0');
							var payCurrency = String($paySumWonEl.data('currency') || '');
							var payExchangeRate = parseFloat(payExchangeRateRaw.replace(/,/g, "")) || 0;
							if (payExchangeRate > 0) {
								var payWonSumRaw = payOpriceSum * payExchangeRate;
								if (payCurrency === '엔' || payCurrency.toUpperCase() === 'JPY') {
									payWonSumRaw = payWonSumRaw / 100;
								}
								$paySumWonEl.html(GC.comma(Math.round(payWonSumRaw)));
							} else {
								$paySumWonEl.html("");
							}
						}
					}
				} else {
					$("#pay_unit_price_sum_" + id).val("");
					$("#pay_order_qty_sum_" + id).html("");
					var $paySumWonEl = $("#pay_order_qty_sum_won_" + id);
					if ($paySumWonEl.length > 0) {
						$paySumWonEl.html("");
					}
				}
			}

			groupSum(oop_idx);
		},

		groupState : function( mode ) {
			
			gState = mode;
			
			if( mode == "normal" ){
				$("#group_state").removeClass("ing").removeClass("end").addClass("normal").html("state : 보기중");
			}else if( mode == "ing" ){
				$("#group_state").removeClass("normal").removeClass("end").addClass("ing").html("state : 저장중");
			}else if( mode == "end" ){
				$("#group_state").removeClass("normal").removeClass("ing").addClass("end").html("state : 저장완료");
			}

		}

	};

}();

<?php if ($open_oop_idx !== '') { ?>
	orderSheetDetail.PrdList('<?= $idx ?>', '<?= $open_oop_idx ?>');
<?php } ?>

<?php if ((int)($orderSheetMain['oo_form_idx'] ?? 0) === 0) { ?>
	showAlert("Error", "이 주문서에 [주문서 폼]이 지정되어있지 않습니다.<br>(주문서 상세정보)에서 주문서 폼을 지정해주세요.", "alert2" );
<?php } ?>
</script>
