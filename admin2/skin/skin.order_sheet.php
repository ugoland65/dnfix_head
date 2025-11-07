<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\orderSheet; 

$orderSheet = new orderSheet(); 

$result = $orderSheet->orderSheetIndex();

/*
	echo "<pre>";
	print_r($result);
	echo "</pre>";
*/

// 변수 초기화
$oo_data = [];
$_json_oo_stock = [];
$_os_state_text = [];

$_os_state_text[1] = "작성중";
$_os_state_text[2] = "주문전송";
$_os_state_text[4] = "입금완료";
$_os_state_text[5] = "입고완료";
$_os_state_text[7] = "주문종료";

if( empty($_idx) && !empty($_get1) )	$_idx = $_get1;

if( !empty($_idx) ){


	//$oo_data = sql_fetch_array(sql_query_error("select oo_idx, oo_name, oo_state from ona_order WHERE oo_idx = '".$_idx."' "));

	$oo_data = sql_fetch_array(sql_query_error("select * from ona_order A 
		left join ona_order_group B ON ( B.oog_idx = A.oo_form_idx )  WHERE A.oo_idx = '".$_idx."' "));

	if (!is_array($oo_data)) {
		$oo_data = [];
	}

	$_json_oo_stock = json_decode($oo_data['oo_stock'] ?? '{}', true);
	if (!is_array($_json_oo_stock)) {
		$_json_oo_stock = [];
	}

}

$_oop_idx = "";
if( $_get2 ){ $_oop_idx = $_get2; }

?>
<style type="text/css">
#contents_body{   }
#contents_body_wrap{ padding: 0 15px 15px !important;  }
.ost-head{ height:40px; line-height:40px; font-size:18px; font-weight:600; padding-left:180px;  }
.ost-wrap{ display:table; width:100%; height:calc(100% - 40px); background-color:#fff; border-spacing:0; border-collapse:collapse; }
.ost-wrap > ul { display:table-cell; vertical-align:top; height:100%; box-sizing:border-box; border:1px solid #888; }
.ost-wrap > ul.ul2{ position:relative; }
.ost-wrap > ul.ul3{ width:310px; padding:10px; }

#order_sheet_detail{ width:100%; height:100%; }

.tabmenu-line{ height:42px;border-bottom:solid 2px #006edc; }
.tabmenu-line > *{ float:left; width:25%; }
.tabmenu-line > * span{ display:block; margin:0 0 0 -1px; height:40px; font-weight:600; color:#676767;text-align:center;line-height:40px;border:solid 1px #cdcdcd; border-bottom:0;background:#eee;box-sizing:border-box}
.tabmenu-line > *:first-child span{margin:0}
.tabmenu-line > .active span{position:relative;height:42px; color:#006edc; border-color:#006edc; background:#fff }

.order-sheet-info-wrap{ margin-top:10px; padding:15px; border:1px solid #ddd; }
.order-sheet-info-wrap > ul.name{ font-size:15px; }
</style>

<div id="contents_head">
	<h1>주문서 v.4 - ( <?=$_os_state_text[$oo_data['oo_state'] ?? ''] ?? ''?> ) <?=$oo_data['oo_name'] ?? ''?> </h1>
	<!-- 
	<div class="head-btn-wrap m-l-10">
		<button type="button" class="btnstyle1 btnstyle1-sm" onclick="" >주문처 관리</button>
	</div>
	-->

    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="orderSheet.osReg()" > 
			<i class="fas fa-plus-circle"></i>
			신규주문서 생성
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="ost-head">

			<? if( !empty($oo_data['oo_idx']) ){ ?>
				
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="orderSheet.osView(this, '<?=$oo_data['oo_idx']?>')" >주문서 상세정보</button>

				<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="orderSheet.osPrint('<?=$oo_data['oo_idx']?>', '<?=$_oog_code ?? ''?>');" >출력</button>
				<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="orderSheet.osWindowView('<?=$oo_data['oo_idx']?>', '<?=$_oog_code ?? ''?>');" >새창</button>

				<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="orderSheet.osDel(this, '<?=$oo_data['oo_idx']?>', '<?=$oo_data['oo_state'] ?? ''?>');">주문서 삭제</button>
				
				<? if( ($oo_data['oo_state'] ?? 0) > 4 ){ ?>
					<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="orderSheet.osStock('<?=$oo_data['oo_idx']?>');">재고 일괄등록</button>
				<? } ?>

				<? if( ($_json_oo_stock['state'] ?? '') == "in" ){ ?>
					<span style="font-size:12px; font-weight:500;">재고일괄등록 완료 ( <?=date ("y.m.d <b>H:i</b>", strtotime($_json_oo_stock['reg']['date'] ?? 'now'))?> )</span>
				<? } ?>

				<? if( !empty($oo_data['oo_form_idx']) ){ ?>
					<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="orderSheetForm.view('<?=$oo_data['oo_form_idx']?>')">주문서 폼 : <?=$oo_data['oog_name'] ?? ''?></button>
				<? } ?>

			<? }else{ ?>
			<? } ?>

		</div>

		<div class="ost-wrap">
			<ul class="ul2">
				<div id="order_sheet_detail">
				</div>
			</ul>
			<ul class="ul3">

				<div class="tabmenu-line">
					<a id="info" href="#" onclick="orderSheet.List('info')" class="active" ><span>정보</span></a>
					<a id="relation" href="#" onclick="orderSheet.List('연관','<?=$oo_data['oo_form_idx'] ?? ''?>')" class="" ><span>연관</span></a>
					<a id="import" href="#" onclick="orderSheet.List('수입')" ><span>수입</span></a>
					<a id="ko" href="#" onclick="orderSheet.List('국내')" ><span>국내</span></a>
				</div>
				
				<? 
					if( !empty($oo_data['oo_idx']) ){ 
				?>
				<div id="order_sheet_info">
					<div class="order-sheet-info-wrap">
						<ul class="name"><?=$oo_data['oo_name'] ?? ''?></ul>
						<ul class="m-t-15">수입 형태 : <b><?=$oo_data['oo_import'] ?? ''?></b></ul>
						
						<ul class="m-t-15">전체 금액 : 
							<b><span id="oprice_allsum"><?=number_format($oo_data['oo_sum_price'] ?? 0, 2)?></span></b>
							<? if( !empty($oo_data['oo_sum_currency']) ){ ?><?=$oo_data['oo_sum_currency']?><? } ?>
						</ul>

						<? 
							if( ($oo_data['oo_sum_exchange_rate'] ?? 0) > 0 ){ 
						?>
						<ul class="m-t-5">적용 환율 : <b><span id=""><?=$oo_data['oo_sum_exchange_rate']?></span></b></ul>
						<ul class="m-t-5">원화 전환 : <b><span id=""><?=number_format(($oo_data['oo_sum_price'] ?? 0) * ($oo_data['oo_sum_exchange_rate'] ?? 0), 2)?></span></b></ul>
						<? } ?>
						<ul class="m-t-5">전체 상품 : <b><span id="oprice_sum_goods"><?=number_format($oo_data['oo_sum_goods'] ?? 0)?></span></b></ul>
						<ul class="m-t-5">전체 수량 : <b><span id="oprice_sum_qty"><?=number_format($oo_data['oo_sum_qty'] ?? 0)?></span></b></ul>
						<ul class="m-t-5">전체 무게 : <b><span id="oprice_sum_weight"><?=number_format($oo_data['oo_sum_weight'] ?? 0)?>g</span></b></ul>
						<ul class="m-t-5">전체 CBM : <b><span id="oprice_sum_cbm"><?=number_format($oo_data['oo_sum_cbm'] ?? 0, 2)?></span></b></ul>
					</div>
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full m-t-5" onclick="orderSheet.osView(this, '<?=$oo_data['oo_idx']?>')" >주문서 상세정보</button>
				</div>
				<? }else{ ?>
					주문서를 선택해주세요.
				<? }?>

				<div id="order_sheet_list">
				</div>

			</ul>
		</div>

	</div>
</div>

<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<script src="/admin2/js/order_sheet.js?ver=<?=$wepix_now_time?>"></script>
<script type="text/javascript">
<!-- 
<? if( $_idx ){ ?>
	orderSheet.Detail('<?=$_idx?>', '<?=$_oop_idx?>');
<? } ?>
//--> 
</script>
