<?php
$orderSheetMain = (isset($orderSheetMain) && is_array($orderSheetMain)) ? $orderSheetMain : [];
$orderSheetStockState = (isset($orderSheetStockState) && is_array($orderSheetStockState)) ? $orderSheetStockState : [];
$orderSheetStateTextMap = (isset($orderSheetStateTextMap) && is_array($orderSheetStateTextMap)) ? $orderSheetStateTextMap : [];
$idx = isset($idx) ? (int)$idx : 0;
$oop_idx = isset($oop_idx) ? (string)$oop_idx : '';
$form_view = isset($form_view) ? (string)$form_view : '';
$oo_state = (int)($orderSheetMain['oo_state'] ?? 0);
$oo_form_idx = (string)($orderSheetMain['oo_form_idx'] ?? '');
$ooSumPrice = (float)($orderSheetMain['oo_sum_price'] ?? 0);
$ooSumExchangeRate = (float)($orderSheetMain['oo_sum_exchange_rate'] ?? 0);
$ooSumCurrency = trim((string)($orderSheetMain['oo_sum_currency'] ?? ''));
$ooPrdExchangeRate = (float)($orderSheetMain['oo_prd_exchange_rate'] ?? 0);
$ooPrdCurrency = trim((string)($orderSheetMain['oo_prd_currency'] ?? ''));
$displayExchangeRate = $ooSumExchangeRate > 0 ? $ooSumExchangeRate : $ooPrdExchangeRate;
$displayCurrency = $ooSumExchangeRate > 0 ? $ooSumCurrency : $ooPrdCurrency;
$displayExchangeLabel = $ooSumExchangeRate > 0 ? '결제 환율' : '상품 환율';
$displayKrwLabel = $ooSumExchangeRate > 0 ? '결제 원화' : '예상 원화';
$isYenCurrency = in_array($displayCurrency, ['엔', 'JPY', 'jpy'], true);
$convertedKrw = $displayExchangeRate > 0
    ? ($isYenCurrency
        ? round($ooSumPrice * ($displayExchangeRate / 100), 0)  // 엔 환율은 100엔 기준값으로 저장됨
        : round($ooSumPrice * $displayExchangeRate, 0))
    : 0;
?>

<link rel="stylesheet" href="/admin2/css/order_sheet.css?ver=<?= time() ?>">

<div id="contents_head">
    <h1>주문서 v.4 - ( <?= $orderSheetStateTextMap[$oo_state] ?? '' ?> ) <?= $orderSheetMain['oo_name'] ?? '' ?> </h1>

    <div id="head_write_btn">
        <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="orderSheet.osReg()">
            <i class="fas fa-plus-circle"></i>
            신규주문서 생성
        </button>
    </div>
</div>
<div id="contents_body">
    <div id="contents_body_wrap">

        <div class="ost-head">

            <?php if (!empty($orderSheetMain['oo_idx'])) { ?>
                <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="orderSheet.osView(this, '<?= $orderSheetMain['oo_idx'] ?>')">주문서 상세정보</button>

                <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="orderSheet.osPrint('<?= $orderSheetMain['oo_idx'] ?>', '<?= $orderSheetMain['oog_code'] ?? '' ?>');">출력</button>
                <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="orderSheet.osWindowView('<?= $orderSheetMain['oo_idx'] ?>', '<?= $orderSheetMain['oog_code'] ?? '' ?>');">새창</button>

                <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="orderSheet.osDel(this, '<?= $orderSheetMain['oo_idx'] ?>', '<?= $orderSheetMain['oo_state'] ?? '' ?>');">주문서 삭제</button>

                <?php if (($orderSheetMain['oo_state'] ?? 0) > 4) { ?>
                    <button type="button" class="btnstyle1 btnstyle1-sm" onclick="orderSheet.osStock('<?= $orderSheetMain['oo_idx'] ?>');">재고 일괄등록</button>
                <?php } ?>

                <?php if (($orderSheetStockState['state'] ?? '') === 'in') { ?>
                    <span style="font-size:12px; font-weight:500;">재고일괄등록 완료 ( <?= date("y.m.d <b>H:i</b>", strtotime($orderSheetStockState['reg']['date'] ?? 'now')) ?> )</span>
                <?php } ?>

                <?php if (!empty($orderSheetMain['oo_form_idx'])) { ?>
                    <button type="button" class="btnstyle1 btnstyle1-sm" onclick="orderSheetForm.view('<?= $orderSheetMain['oo_form_idx'] ?>')">주문서 폼 : <?= $orderSheetMain['oog_name'] ?? '' ?></button>
                <?php } ?>
            <?php } ?>

        </div>

        <div class="ost-wrap">
            <ul class="ul2">
                <div id="order_sheet_detail"></div>
            </ul>
            <ul class="ul3">

                <div class="tabmenu-line">
                    <a id="info" href="#" onclick="orderSheet.List('info')" class="active"><span>정보</span></a>
                    <a id="relation" href="#" onclick="orderSheet.List('연관','<?= $oo_form_idx ?>')" class=""><span>연관</span></a>
                    <a id="import" href="#" onclick="orderSheet.List('수입')"><span>수입</span></a>
                    <a id="ko" href="#" onclick="orderSheet.List('국내')"><span>국내</span></a>
                </div>

                <?php if (!empty($orderSheetMain['oo_idx'])) { ?>
                    <div id="order_sheet_info">
                        <div class="order-sheet-info-wrap">
                            <ul class="name"><?= $orderSheetMain['oo_name'] ?? '' ?></ul>
                            <ul class="m-t-15">수입 형태 : <b><?= $orderSheetMain['oo_import'] ?? '' ?></b></ul>

                            <ul class="m-t-15">전체 금액 :
                                <b><span id="oprice_allsum"><?= number_format((float)($orderSheetMain['oo_sum_price'] ?? 0), 2) ?></span></b>
                                <?php if (!empty($orderSheetMain['oo_sum_currency'])) { ?><?= $orderSheetMain['oo_sum_currency'] ?><?php } ?>
                            </ul>

                            <?php if ($displayExchangeRate > 0) { ?>
                                <ul class="m-t-5"><?= $displayExchangeLabel ?> : <b><span><?= number_format($displayExchangeRate, 2) ?></span></b></ul>
                                <ul class="m-t-5"><?= $displayKrwLabel ?> : <b><span><?= number_format($convertedKrw, 0) ?></span></b></ul>
                            <?php } ?>
                            
                            <ul class="m-t-5">전체 상품 : <b><span id="oprice_sum_goods"><?= number_format((float)($orderSheetMain['oo_sum_goods'] ?? 0)) ?></span></b></ul>
                            <ul class="m-t-5">전체 수량 : <b><span id="oprice_sum_qty"><?= number_format((float)($orderSheetMain['oo_sum_qty'] ?? 0)) ?></span></b></ul>
                            <ul class="m-t-5">전체 무게 : <b><span id="oprice_sum_weight"><?= number_format((float)($orderSheetMain['oo_sum_weight'] ?? 0)) ?>g</span></b></ul>
                            <ul class="m-t-5">전체 CBM : <b><span id="oprice_sum_cbm"><?= number_format((float)($orderSheetMain['oo_sum_cbm'] ?? 0), 2) ?></span></b></ul>
                        </div>
                        <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full m-t-5" onclick="orderSheet.osView(this, '<?= $orderSheetMain['oo_idx'] ?>')">주문서 상세정보</button>
                    </div>
                <?php } else { ?>
                    주문서를 선택해주세요.
                <?php } ?>

                <div id="order_sheet_list"></div>

            </ul>
        </div>

    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/x-editable-bs5@1.5.8/dist/bootstrap5-editable/css/bootstrap-editable.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/x-editable-bs5@1.5.8/dist/bootstrap5-editable/js/bootstrap-editable.min.js"></script>

<script src="/admin2/js/order_sheet.js?ver=<?= time() ?>"></script>
<script type="text/javascript">
<?php if ($idx > 0) { ?>
orderSheet.Detail('<?= $idx ?>', '<?= $oop_idx ?>', '<?= $form_view ?>');
<?php } ?>
</script>
