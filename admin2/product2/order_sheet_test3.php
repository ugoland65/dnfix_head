<?
$pageGroup = "product2";
$pageName = "order_sheet3";

include "../lib/inc_common.php";

/*
상품 리스트
ajax_order_sheet_prd_list.php
*/

$_get_oog_code = securityVal($get_oog_code);
$_oo_idx = securityVal($oo_idx);
$_cate_num = securityVal($cate_num);

	$_koedge_order_state_text[1] = "작성중";
	$_koedge_order_state_text[2] = "주문전송";
	$_koedge_order_state_text[4] = "입금완료";
	$_koedge_order_state_text[5] = "입고완료";
	$_koedge_order_state_text[7] = "주문종료";

if($_get_oog_code){
	$_oog_code = $_get_oog_code;
}

		$btn_text_submit = "주문서 생성";

	//이값이 있다는것은 수정임
	if( $_oo_idx ){

		$btn_text_submit = "주문서 수정";

		$oo_data = wepix_fetch_array(wepix_query_error("select * from ona_order where oo_idx = '".$_oo_idx."' "));

		$_oog_code = $oo_data[oo_code];
		$_oo_state = $oo_data[oo_state];
		
		$_oo_sum_price = $oo_data[oo_sum_price];
		$_oo_sum_goods = $oo_data[oo_sum_goods];
		$_oo_sum_qty = $oo_data[oo_sum_qty];

		if( !$form_view ) $form_view = "show";
		if( $_oo_state == 4 || $_oo_state == 5 || $_oo_state == 7 ){ 
			$form_view = "hidden";
		}

		$show_oo_name = " | ".$oo_data[oo_name]." | ".$_koedge_order_state_text[$_oo_state];

	//{"brand":"","name":"텝펜","oop_idx":"68","active":"Y"},{"brand":"","name":"키테루","oop_idx":"63","active":"Y"}

		//$_order_sec_json2 = '[{"bidx":"27","selpd":[{"pidx":"1905","qty":"1"},{"pidx":"209","qty":"2"},{"pidx":"954","qty":"3"},{"pidx":"1906","qty":"4"}]},{"bidx":"28","selpd":[{"pidx":"475","qty":"4"},{"pidx":"1152","qty":"5"},{"pidx":"1168","qty":"7"}]}]';

		//$_order_sec_json2 = '[{"bidx":"63","item":"3","qty":"68","price":"36530"}, {"bidx":"27","item":"4","qty":"15","price":"36530"}]';
		$_order_sec_json2 = $oo_data[oo_json];
		$_order_sec_json = json_decode($_order_sec_json2,true);
		
		$_oo_sum_price2 = 0;
		$_oo_sum_goods2 = 0;
		$_oo_sum_qty2 = 0;
		$_oo_sum_weight2 = 0;

		for ($i=0; $i<count($_order_sec_json); $i++){
			$_os_data_idx = $_order_sec_json[$i]['bidx'];
			$_order_sec_data[$_os_data_idx]['item'] = $_order_sec_json[$i]['item'];
			$_order_sec_data[$_os_data_idx]['qty'] = $_order_sec_json[$i]['qty'];
			$_order_sec_data[$_os_data_idx]['price'] = $_order_sec_json[$i]['price'];
			$_order_sec_data[$_os_data_idx]['weight'] = $_order_sec_json[$i]['weight'];
			
			if( $_order_sec_json[$i]['false'] > 0 ){
				$_order_sec_data[$_os_data_idx]['item'] = (int)$_order_sec_json[$i]['item'] - (int)$_order_sec_json[$i]['false'];
				$_order_sec_data[$_os_data_idx]['qty'] = (int)$_order_sec_json[$i]['qty'] - (int)$_order_sec_json[$i]['false_sum_qty'];
				$_order_sec_data[$_os_data_idx]['price'] = (int)$_order_sec_json[$i]['price'] - (int)$_order_sec_json[$i]['false_sum_price'];
				$_order_sec_data[$_os_data_idx]['weight'] = (int)$_order_sec_json[$i]['weight'] - (int)$_order_sec_json[$i]['false_sum_weight'];
				$_order_sec_data[$_os_data_idx]['false'] = $_order_sec_json[$i]['false'];
				$_order_sec_data[$_os_data_idx]['false_sum'] = $_order_sec_json[$i]['false_sum'];
			}

			$_oo_sum_price2 += $_order_sec_data[$_os_data_idx]['price'];
			$_oo_sum_goods2 += $_order_sec_data[$_os_data_idx]['item'];
			$_oo_sum_qty2 += $_order_sec_data[$_os_data_idx]['qty'];
			$_oo_sum_weight2 += (int)str_replace(',','', $_order_sec_data[$_os_data_idx]['weight']);
			
		}

	/*
		echo "<pre>";
		print_r($_order_sec_json);
		echo "</pre>";

		echo "/".$_order_sec_data[$_oop_idx]['item']."/";
	*/

	}

	$oog_data = wepix_fetch_array(wepix_query_error("select * from ona_order_group where oog_code = '".$_oog_code."' "));
	
	$_oog_idx = $oog_data[oog_idx];

	$_price_colum = $oog_data[price_colum];

	//$_brand_json = '['.$oog_data[oog_brand].']';
	$_brand_json_data = json_decode($oog_data['oog_brand'],true);


include "../layout/header.php";
?>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<style type="text/css">
.ost-wrap_wrap{ width:100%; height:100%; height : -webkit-calc(100% - 50px); height : -moz-calc(100% - 50px); height : calc(100% - 50px);  }
.ost-wrap{ display:table; width:100%;  height:100%;  }
.ost-wrap > ul { display:table-cell; vertical-align:top; height:100%; box-sizing:border-box; }
.ost-wrap > ul.ost-wrap-ul1{ width:180px; }
.ost-wrap > ul.ost-wrap-ul2{  position:relative; }
.ost-wrap > ul.ost-wrap-ul3{ width:310px;  }
.overflow-y { width:100%; height:100%; overflow-y:scroll;  box-sizing:border-box; }

.ost-big{ width:100%; border:1px solid #ccc; background-color:#eee; padding:8px 10px; margin-bottom:3px; border-radius:6px; box-sizing:border-box; cursor:pointer; }
.ost-big.inorder{ background-color:#fff; }
.ost-big.active{ background-color:#ffde00; }

.unit-price-sum{ font-size:13px; color:#2525fa; font-weight:bold; }
.oprice-sum-goods-cate{ color:#2525fa; font-weight:bold; }
.oprice-sum-qty{ color:#ff0000; font-weight:bold; }
.oprice-allsum-cate{ color:#ff0000; font-weight:bold; }

#order_sheet_prd_list{ padding-top:75px; }
.ospl-wrap{ position:absolute; top:2px; left:2px; width:calc(100% - 22px); height:70px; background-color:#fff; border-bottom:2px solid #222; box-sizing:border-box; }

.ospl-top{ width:100%; height:100%;  display:table; }
.ospl-top > ul{ display:table-cell; box-sizing:border-box;  padding:6px; }
.ospl-top > ul.btn{ width:100px; }
.ospl-top > ul.btn button{ width:100% !important; height:100% !important; }

.order-form-wrap{ width:100%; height:100%; background-color:#fff; border:1px solid #222; padding:6px; position:relative; }
</style>

<div id="contents_head">
	<h1>주문서<?=$show_oo_name?></h1>
    <div id="head_write_btn">
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

<div class="m-b-10">

	<select name="" onchange="newOS(this.value);">
		<option value="" >주문처 선택</option>
<?
$query = "select * from ona_order_group order by oog_idx desc";
$result = wepix_query_error($query);
while($list = wepix_fetch_array($result)){
?>
		<option value="<?=$list[oog_code]?>" <? if( $_get_oog_code == $list[oog_code] ) echo "selected";?> ><?=$list[oog_name]?></option>
<? } ?>
	</select>
	
	<? if( $_oog_idx ){ ?>
		<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="orderSheet.groupConfig();">그룹관리</button>
	<? } ?>
		<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="orderSheet.shopMake();" >주문처 생성</button>

<? if( $_oo_idx ){ ?>

		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="orderPrint('<?=$_oo_idx?>', '<?=$_oog_code?>');" >주문서 출력</button>
		<button type="button" id="order_sheet_del" class="btnstyle1 btnstyle1-danger btnstyle1-sm" data-idx="<?=$_oo_idx?>" >주문서 삭제</button>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm m-l-20" onclick="oderState('<?=($_oo_state*1)-1?>')">이전단계</button>

	<? if( $_oo_state == 1 ){ ?>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm " onclick="oderState('2')">주문전송 처리</button>
	<? }elseif( $_oo_state == 2 ){ ?>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm " onclick="oderState('4')">입금완료 처리</button>
	<? }elseif( $_oo_state == 4 ){ ?>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="oderState('5')">입고완료 처리</button>
	<? }elseif( $_oo_state == 5 ){ ?>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm " onclick="oderState('7')">재고 등록 없이 주문종료</button>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm " onclick="orderPrint('<?=$_oo_idx?>', '<?=$_oog_code?>', 'stock');" >재고등록</button>
	<? } ?>

		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm m-l-50" onclick="orderSheet.detail('<?=$_oo_idx?>')">주문서 상세정보</button>

<? } ?>

</div>

<div class="ost-wrap_wrap">
<div class="ost-wrap">
	<ul class="ost-wrap-ul1">
		<div class="overflow-y" style="border:1px solid #222; padding:6px; ">
<?
for ($i=0; $i<count($_brand_json_data); $i++){

	$_show_brand_idx = $_brand_json_data[$i]['brand'];
	$_show_brand_name = $_brand_json_data[$i]['name'];
	$_oop_idx = $_brand_json_data[$i]['oop_idx'];

	$oop_data = wepix_fetch_array(wepix_query_error("select * from ona_order_prd where oop_idx = '".$_oop_idx."' "));

	$_oop_json_check_data = substr($oop_data[oop_data], 0,1);
	if( $_oop_json_check_data == "[" ){
		$_oop_json = $oop_data[oop_data];
	}else{
		$_oop_json = '['.$oop_data[oop_data].']';
	}

	$_oop_jsondata = json_decode($_oop_json,true);

	$_item = 0;
	$_qty = 0;
	$_price = "";
	$_weight = "";
	$_show_weight = "";

	if( $_order_sec_data[$_oop_idx]['item'] ) $_item = $_order_sec_data[$_oop_idx]['item'];
	if( $_order_sec_data[$_oop_idx]['qty'] ) $_qty = $_order_sec_data[$_oop_idx]['qty'];
	if( $_order_sec_data[$_oop_idx]['price'] ) $_price = $_order_sec_data[$_oop_idx]['price'];
	if( $_order_sec_data[$_oop_idx]['weight'] ) $_weight = $_order_sec_data[$_oop_idx]['weight'];

	if( $_weight ){
		if( $_weight > 1000 ){
			$_show_weight = number_format($_weight*0.001)."kg";
		}else{
			$_show_weight = number_format($_weight)."g";
		}
	}

?>

<input type="hidden" name="" id="oprice_allsum_data_<?=$_oop_idx?>" value="<?=$_price?>" >
<input type="hidden" name="" id="oprice_sum_goods_data_<?=$_oop_idx?>" value="<?=$_item?>" >
<input type="hidden" name="" id="oprice_sum_qty_data_<?=$_oop_idx?>" value="<?=$_qty?>" >
<input type="hidden" name="" id="oprice_sum_weight_data_<?=$_oop_idx?>" value="<?=$_weight?>" >

<div class="ost-big <? if($_qty > 0) echo 'inorder';?>" data-idx="<?=$_oop_idx?>" id="ost_big_<?=$_oop_idx?>" onclick="orderSheetPrdList('<?=$_oop_idx?>', '<?=$_oog_code?>', '<?=$_oo_idx?>', '<?=$form_view?>')">
	<ul><b style="font-size:13px;"><?=$_show_brand_name?></b></ul>
	<ul class="m-t-3">
		<b><?=count($_oop_jsondata)?></b> / 
		<span class="oprice-sum-goods-cate" id="oprice_sum_goods_<?=$_oop_idx?>"><?=$_item?></span>
		<? if( $_order_sec_data[$_oop_idx]['false'] > 0 ){ ?>
		<span class="" id="">실패 : <?=$_order_sec_data[$_oop_idx]['false']?></span>
		<? } ?>
	</ul>
	<ul>
		<span class="oprice-sum-qty" id="oprice_sum_qty_<?=$_oop_idx?>"><?=$_qty?></span> /
		<span class="oprice-sum-weight" id="oprice_sum_weight_<?=$_oop_idx?>"><?=$_show_weight?></span>
	</ul>
	<ul><span class="oprice-allsum-cate" id="oprice_allsum_<?=$_oop_idx?>"><?=number_format($_price)?></span></ul>
</div>
<? } ?>

		</div>

	</ul>
	<ul class="ost-wrap-ul2">

		<div id="order_sheet_prd_list" class="overflow-y" style="border:2px solid #222;">
		</div>

	</ul>
	<ul class="ost-wrap-ul3">
		
<style type="text/css">
.tabmenu-line{ height:42px;border-bottom:solid 2px #006edc; }
.tabmenu-line > *{ float:left; width:33.33%; }
.tabmenu-line > * span{ display:block; margin:0 0 0 -1px; height:40px; font-family: 'Noto Sans KR', sans-serif; font-size:14px; font-weight:700; color:#676767;text-align:center;line-height:40px;border:solid 1px #cdcdcd; border-bottom:0;background:#eee;box-sizing:border-box}
.tabmenu-line > *:first-child span{margin:0}
.tabmenu-line > .active span{position:relative;height:42px; color:#006edc; border-color:#006edc; background:#fff}

#order_form_info{ padding-top:20px; }
#order_form_list{ padding-top:10px; display:none; }

</style>

		<div class="order-form-wrap">

			<div class="tabmenu-line">
				<a id="info" href="#" onclick="orderSheet.List('info')" class="active" ><span>주문정보</span></a>
				<a id="jp" href="#" onclick="orderSheet.List('jp')" ><span>수입</span></a>
				<a id="ko" href="#" onclick="orderSheet.List('ko')" ><span>국내</span></a>
			</div>

		<div id="order_form_list">
		</div>
<?
if( $get_oog_code || $oo_idx ){
?>
		<div id="order_form_info">
			<div>
				<ul class="filter-from-ui m-t-5">
					전체 주문 금액 : <b style="font-size:15px"><span id="oprice_allsum"><?=number_format($_oo_sum_price2,2)?></span></b>
					<? if( $_oo_sum_price != $_oo_sum_price2 ){ ?>가격 데이터 체크!<? } ?>
				</ul>
<?
if( $oog_data[oog_group] == "cn" ){
?>
				<ul class="filter-from-ui m-t-5">
					전체 위안(195) : <b style="font-size:15px"><span id="oprice_allsum_cn"></span>원</b>
				</ul>
<? } ?>
				<ul class="filter-from-ui m-t-5">
					전체 주문 상품 : <b style="font-size:15px"><span id="oprice_sum_goods"><?=number_format($_oo_sum_goods2)?></span></b>
					<? if( $_oo_sum_goods != $_oo_sum_goods2 ){ ?>상품수 데이터 체크!<? } ?>
				</ul>
				<ul class="filter-from-ui m-t-5">
					전체 주문 수량 : <b style="font-size:15px"><span id="oprice_sum_qty"><?=number_format($_oo_sum_qty2)?></span></b>
					<? if( $_oo_sum_qty != $_oo_sum_qty2 ){ ?>주문수량 데이터 체크!<? } ?>
				</ul>
				<ul class="filter-from-ui m-t-5">
					전체 주문 무게 : <b style="font-size:15px">
					<span id="oprice_sum_weight">
						<?=number_format($_oo_sum_weight2)?>g | <?=number_format($_oo_sum_weight2*0.001)?>kg
					</span>
					</b>
				</ul>

				<ul class="filter-from-ui m-t-10">
					<input type='text' name='oo_name' id='oo_name' size='20' value="<?=$oo_data[oo_name]?>" placeholder="주문서 이름">
				</ul>

				<ul class="filter-from-ui m-t-10">
					<table>
						<tr>
							<td>배송비</td>
							<td ><input type='text' name='oo_name' id='' value=""></td>
						</tr>
						<tr>
							<td colspan="2" style="height:5px"></td>
						</tr>
						<tr>
							<td>관부가세</td>
							<td><input type='text' name='oo_name' id='' value=""></td>
						</tr>
					</table>
				</ul>

				<ul class="filter-from-ui m-t-10">
					<div class="text-right m-b-5">
						<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-info btnstyle1-xs" onclick="" >(원) 추가금액 추가</button>
					</div>
					<div>
						<input type='text' name='oo_name' id='' size='' value="<?=$oo_data[oo_name]?>" style="width:207px;">
						<input type='text' name='oo_name' id='' size='' value="<?=$oo_data[oo_name]?>" style="width:85px;">
					</div>
				</ul>



			<? if($_oo_idx){ ?>
				<ul class="filter-from-ui m-t-10">
					등록 : <?=date("y.m.d H:i",$oo_data[oo_date]);?> | 수정 : <?=date("y.m.d H:i",$oo_data[oo_date_modify]);?>
				</ul>
			<? } ?>

			<? if(  $_oo_state != 5 && $_oo_state != 7 ){ ?>
				<ul class="filter-from-ui m-t-5">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="makeOrder();" > 
						<?=$btn_text_submit?>
					</button>
				</ul>
			<? } ?>


			</div>
		</div>

<? }else{ ?>

<? } ?>

	</div>

	</ul>
</div>
</div>

	</div>
</div>

<script type="text/javascript"> 
<!-- 
var brandViewState = "off";
var selectBrand = "";
var formView = "<?=$form_view?>";
var ooSumPrice = "";
var ooSumGoods = "";
var ooSumQty = "";

function orderPrint(idx, code, mode){
	window.open("/admin2/product2/popup.order_sheet_print2.php?idx="+idx+"&code="+ code+"&mode="+ mode, "orderSGroup_"+ code, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

function addGoods(oop_idx, oog_code, oog_arynum){
	
	$.ajax({
		type: "post",
		url : "ajax_order_sheet_add_product_new.php",
		data : { oop_idx : oop_idx, oog_code : oog_code, oog_arynum : oog_arynum, oog_idx : "<?=$_oog_idx?>", oo_idx : "<?=$_oo_idx?>" },
		success: function(getdata) {
			$("#popup_layer_body").html(getdata);
		}
	});

	showPopup('1300','800','ajax');
}


function newOS(code) {
	location.href='order_sheet_test3.php?get_oog_code='+code;
}


function oderState(step){

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : { 
			a_mode : "oderState",
			step : step,
			modify_idx : "<?=$_oo_idx?>"
		},
		success: function(getdata) {
			makedata = getdata.split('|');
			ckcode = makedata[1];
			msg = makedata[3];
			if(ckcode=="Processing_Complete"){
				location.reload();

			}else if(ckcode=="Value_null"){

			}
		}
	});

}


function orderListShow(mode){


}


function orderSheetPrdListShow(idx, oog_code, oo_idx, form_view){

	if( !form_view ) form_view = formView;

	$.ajax({
		type: "post",
		url : "ajax_order_sheet_prd_list.php",
		data : { 
			idx : idx, 
			oog_code : oog_code,
			oo_idx : oo_idx,
			form_view : form_view
		},
		success: function(html) {
			$("#order_sheet_prd_list").html(html);
			brandViewState = "on";
			selectBrand = idx;
		}
	});

}


function orderSheetPrdList(idx, oog_code, oo_idx, form_view){

	if( brandViewState == "on" ){
		addOrder('noReload');
	}

	$(".ost-big").each(function(){
		$(this).removeClass('active');
	});

	$("#ost_big_" + idx).addClass('active');
	
	 orderSheetPrdListShow(idx, oog_code, oo_idx, form_view);

}

function qtyGogo( id, opg ) {

/*
	var oprice = $("#unit_price_td_"+ id).data("price");
	if( oprice == 0 ) {
		oprice = $("#unit_price_"+ id).val();
	}
*/

	var oprice = $("#unit_price_"+ id).val();
	var v = $("#unit_qty_"+ id).val();

	console.log(id + ":" + oprice);

	if(v=="") v=0;
	if( oprice > 0 && v > 0 ) {	
		var oprice_sum = oprice*v;
		if( oprice_sum > 0 ){
			$("#unit_price_sum_"+ id).val(oprice_sum);
			$("#order_qty_sum_"+ id).html(Comma_int(oprice_sum));
			//alert("켜져");
			ckTr(id,"on");
		}
	}else{
		$("#unit_price_sum_"+ id).val("");
		$("#order_qty_sum_"+ id).html("");
		//alert("꺼져");
		ckTr(id,"off");
	}

	brandSum(opg);
	orderSheet.allSum();

}

function brandSum(opg) {

	var oprice_allsum = 0;
	var oprice_sum_qty = 0;
	var oprice_sum_goods = 0;
	var oprice_sum_weight = 0;

	$(".checkSelect:checked").each(function(){

		var checkbox_id = $(this).val();

		if( $("#unit_qty_" + checkbox_id).val() == "" ){
			var plus_oprice_sum_qty = 1;
		}else{
			var plus_oprice_sum_qty = ($("#unit_qty_" + checkbox_id).val() * 1);
		}

		//alert(plus_oprice_sum_qty);

		oprice_allsum = oprice_allsum + ($("#unit_price_sum_" + checkbox_id).val() * 1);
		oprice_sum_goods++;
		
		oprice_sum_qty = oprice_sum_qty + plus_oprice_sum_qty;

		oprice_sum_weight = oprice_sum_weight + ($("#weight_"+checkbox_id).data('weight') * plus_oprice_sum_qty);

	});


	$("#oprice_allsum_" + opg).html(Comma_int(oprice_allsum));
	$("#oprice_allsum_data_" + opg).val(oprice_allsum);

	$("#oprice_sum_goods_" + opg ).html(Comma_int(oprice_sum_goods));
	$("#oprice_sum_goods_data_" + opg ).val(oprice_sum_goods);

	//$("#oprice_sum_qty_" + opg).html("ㅁㅁㅁㅁㅁㅁㅁㅁ");
	$("#oprice_sum_qty_data_" + opg).val(oprice_sum_qty);

	if( oprice_sum_weight > 1000 ){
		var show_oprice_sum_weight = Comma_int(oprice_sum_weight*0.001)+"kg";
	}else{
		var show_oprice_sum_weight = Comma_int(oprice_sum_weight)+"g";
	}

	$("#oprice_sum_weight_" + opg).html(show_oprice_sum_weight);
	$("#oprice_sum_weight_data_" + opg).val(oprice_sum_weight);

}


function allSum() {

	var oprice_allsum = 0;
	var oprice_sum_goods = 0;
	var oprice_sum_qty = 0;
	var oprice_sum_weight = 0;

	$(".ost-big").each(function(){

		var checkbox_id = $(this).data("idx");

		oprice_allsum = oprice_allsum + ($("#oprice_allsum_data_" + checkbox_id).val() * 1);
		oprice_sum_goods = oprice_sum_goods + ($("#oprice_sum_goods_data_" + checkbox_id).val() * 1);
		oprice_sum_qty = oprice_sum_qty + ($("#oprice_sum_qty_data_" + checkbox_id).val() * 1);
		oprice_sum_weight = oprice_sum_weight + ($("#oprice_sum_weight_data_" + checkbox_id).val() * 1);

	});

	ooSumPrice = oprice_allsum;
	ooSumGoods = oprice_sum_goods;
	ooSumQty = oprice_sum_qty;

	$("#oprice_allsum").html(Comma_int(oprice_allsum));
	$("#oprice_allsum_cn").html(Comma_int(oprice_allsum*195));
	$("#oprice_sum_goods").html(Comma_int(oprice_sum_goods));
	$("#oprice_sum_qty").html(Comma_int(oprice_sum_qty));
	$("#oprice_sum_weight").html(Comma_int(oprice_sum_weight)+"g | "+ Comma_int(oprice_sum_weight*0.001)+"kg" );

}


function thisCateDel(){

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : {
			a_mode : "thisCateDel",
			oo_idx : "<?=$_oo_idx?>",
			selectBrand : selectBrand
		},
		success: function(res) {
			if(res.success === true) {
				location.reload();
			}
		}
	});

}


function unitFalseReturn(){

	if( $(".checkSelect2:checked").length == 0 ){
		alert('상품을 선택해주세요.');
		return false;
	}

	var target_idx = "";

	$(".checkSelect2:checked").each(function(index){
		var checkbox_id = $(this).val();
		if(index!=0){ 
			target_idx += ",";
		}
/*
		var checkbox = '<input type="checkbox" name="key_check[]"  id="checkbox_'+ checkbox_id +'" class="checkSelect" value="'+ checkbox_id +'" >';
		$("#checkbox_td_" + checkbox_id).html(checkbox);
*/
		target_idx += $(this).val();
		$("#checkbox_" + checkbox_id).show();
		$("#tr_"+ checkbox_id +" td").css({'background':'#ffcbcb'}); 
		qtyGogo(checkbox_id, selectBrand);

	});

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : {
			a_mode : "unitFalseReturn",
			oo_idx : "<?=$_oo_idx?>",
			target_idx : target_idx
		},
		success: function(res) {
			if(res.success === true) {
				//alert("실패 복귀완료");
				addOrder();
			}
		}
	});

}

//선택 주문실패
function unitAction(mode){

	if( $(".checkSelect2:checked").length == 0 ){
		alert('상품을 선택해주세요.');
		return false;
	}

	var target_idx = "";
	var target_price = "";
	var target_price_sum = "";
	var qty_array = "";
	var target_weight_sum = "";
	var memo_array = "";

	$(".checkSelect2:checked").each(function(index){
		var checkbox_id = $(this).val();
		if(index!=0){ 
			target_idx += ",";
			target_price += ",";
			target_price_sum += ",";
			qty_array += ",";
			target_weight_sum += ",";
			memo_array += ",";
		}
		target_idx += $(this).val();
		target_price += $("#unit_price_"+checkbox_id).val();
		target_price_sum += ($("#unit_price_"+checkbox_id).val()*$("#unit_qty_"+checkbox_id).val());
		qty_array += $("#unit_qty_"+checkbox_id).val();
		target_weight_sum += ($("#weight_"+checkbox_id).data('weight')*$("#unit_qty_"+checkbox_id).val());
		memo_array += $("#memo_"+checkbox_id).val();

		$("#tr_"+ checkbox_id +" td").css({'background':'#ffcbcb' }); 
	});

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : {
			a_mode : "nunitFalseNew",
			oo_idx : "<?=$_oo_idx?>",
			target_idx : target_idx,
			target_price : target_price,
			target_price_sum : target_price_sum,
			qty_array : qty_array,
			target_weight_sum : target_weight_sum,
			memo_array : memo_array,
			selectBrand : selectBrand
		},
		success: function(res) {
			if(res.success == true) {
				location.href='order_sheet_test3.php?oo_idx=<?=$_oo_idx?>&cate_num='+selectBrand+'&form_view=hidden';
/*
				if( mode == "noReload" ){
					//alert("상품담기 저장");
				}else{
					alert("실패 처리완료");
					//orderSheetPrdList(selectBrand, '<?=$_oog_code?>', '<?=$_oo_idx?>');
				}
*/
			}else{
				alert(res.msg);
			}
		}
	});

}

function addOrder(mode){

	if( $(".checkSelect:checked").length == 0 ){
		if( mode == "noReload" ){
		}else{
			alert('상품을 선택해주세요.');
		}
		return false;
	}

	var send_array2 = "";
	var qty_array = "";
	var price_array = "";
	var total_qty = 0;
	var total_price = 0;
	var total_weight = 0;
	var memo_array = "";

	$(".checkSelect:checked").each(function(index){
		var checkbox_id = $(this).val();
		if(index!=0){
			send_array2 += ",";
			qty_array += ",";
			price_array += ",";
			memo_array += ",";
		}

		if( $("#unit_qty_" + checkbox_id).val() == "" ){
			var plus_oprice_sum_qty = 1;
		}else{
			var plus_oprice_sum_qty = ($("#unit_qty_" + checkbox_id).val() * 1);
		}

		send_array2 += checkbox_id;
		qty_array += $("#unit_qty_"+checkbox_id).val();
		price_array += $("#unit_price_"+checkbox_id).val();
		total_qty = total_qty + ($("#unit_qty_"+checkbox_id).val()*1);
		total_price = total_price + ($("#unit_price_sum_" + checkbox_id).val() * 1);
		total_weight = total_weight + ($("#weight_"+checkbox_id).data('weight') * plus_oprice_sum_qty);
		memo_array += $("#memo_"+checkbox_id).val();
	});

	//alert(send_array2+"|"+price_array+"|"+qty_array);

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : {
			a_mode : "modifyOrderNew",
			oo_idx : "<?=$_oo_idx?>",
			oo_sum_price : ooSumPrice,
			oo_sum_goods : ooSumGoods,
			oo_sum_qty : ooSumQty,
			selectBrand : selectBrand,
			send_array2 : send_array2,
			price_array : price_array,
			qty_array : qty_array,
			item : $(".checkSelect:checked").length,
			total_qty : total_qty,
			total_price : total_price,
			total_weight : total_weight,
			memo_array : memo_array
		},
		success: function(res) {
			if(res.success === true) {
				//alert(res.msg);

				if( mode == "noReload" ){
					//alert("상품담기 저장");
				}else{
					alert("상품담기 완료");
					orderSheetPrdList(selectBrand, '<?=$_oog_code?>', '<?=$_oo_idx?>');
				}
			}
		}
	});

}

/*
function newPrice(idx){

	var uprice = $("#unit_price_"+ idx).val();
	if( uprice == "" ){
		alert("가격 넣어주세요");
		return false;
	}

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : {
			a_mode : "newPrice",
			cd_idx : idx,
			uprice : uprice,
			price_colum : "<?=$_price_colum?>"
		},
		success: function(res) {
			if(res.success === true) {

			var Html = "<input type='hidden' id='unit_price_"+ idx +"' value='"+ uprice +"'>"
			 + "<b>"+ Comma_int(uprice) +"</b> ";

			//var Html = "<input type='hidden' id='unit_price_"+ idx +"' value='"+ uprice +"'> <span><a href='#' class='editable-cd-price editable-click' data-url='processing.order_sheet.php' data-pk='"+ uprice +"' data-cdidx='"+ idx +"'data-value='"+ uprice +"' data-pricecolum='<?=$_price_colum?>' data-title='가격 수정'><b>"+ Comma_int(uprice) +"</b></a></span> ";


			 $("#unit_price_td_" + idx).html(Html);

			}
		}
	});

}
*/

function makeOrder(mode){

/*
	if( brandViewState == "on" ){
		alert("열려있다");
	}else{
		alert("닫혀있다");
	}
*/

	var oo_name = $('#oo_name').val();

	if( oo_name == "" ){
		alert('주문서 이름!');
		return false;
	}

/*
			oo_token : oo_token,
			oo_sum_price : oo_sum_price ,
			oo_sum_goods : oo_sum_goods,
			oo_sum_qty : oo_sum_qty,
			oo_sum_weight : oo_sum_weight
*/
	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : { 

<? if($_oo_idx){ ?>
			a_mode : "modifyOrderMain",
			modify_idx : "<?=$_oo_idx?>",
<? } else{ ?>
			a_mode : "makeOrderNew",
<? } ?>
			oo_name : oo_name,
			oo_code : "<?=$_oog_code?>"
		},
		success: function(res) {
			if(res.success === true) {
				location.href='order_sheet_test3.php?oo_idx='+ res.insert_id +'&cate_num='+ selectBrand;
			}
		}
	});
}


function ckTr(id, mode) {
	if( mode == "on" ){
		$("#tr_"+ id +" td").css({'background':'#ffcbcb' }); 
		$("#checkbox_"+ id).prop("checked", true);
	}else{
		var beforetrcolor = $("#tr_"+ id).attr("bgcolor");
		//alert(beforetrcolor);
		$("#tr_"+ id +" td").css({'background':beforetrcolor }); 
		$("#checkbox_"+ id).prop("checked", false);
	}
}

<? if( $_cate_num ){ ?>
orderSheetPrdList('<?=$_cate_num?>', '<?=$_oog_code?>', '<?=$_oo_idx?>', '<?=$form_view?>');
<? } ?>


var orderSheet = function() {

	return {

		// 그룹생성
		groupConfig: function(idx) {		
		
			$.ajax({
				type: "post",
				url : "ajax_order_sheet_group_config.php",
				data : { oog_idx : "<?=$_oog_idx?>" },
				success: function(getdata) {
					$("#popup_layer_body").html(getdata);
				}
			});

			showPopup('1300','800','ajax');

		},

		// 디테일
		detail: function(idx) {		
		
			$.alert({
				boxWidth : '1000px',
				useBootstrap : false,
				title : "주문서 상세정보",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				draggable: true,
				content:function () {
					var self = this;
					return $.ajax({
						url: 'popup.order_sheet_view.php',
						data: { "idx":idx, "ajax_mode":"true" },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},
				buttons: {
					cancel: {
						text: '닫기',
						action:function () {
							close();
						}
					},
				}
			});

		},

		// 전체합계
		allSum: function() {
		
			var _allsum = 0;
			var _sum_goods = 0;
			var _sum_qty = 0;
			var _sum_weight = 0;

			$(".ost-big").each(function(){
				var checkbox_id = $(this).data("idx");
				_allsum += ($("#oprice_allsum_data_" + checkbox_id).val() * 1);
				_sum_goods += ($("#oprice_sum_goods_data_" + checkbox_id).val() * 1);
				_sum_qty += ($("#oprice_sum_qty_data_" + checkbox_id).val() * 1);
				_sum_weight += GC.uncomma($("#oprice_sum_weight_data_" + checkbox_id).val())*1;
			});

			ooSumPrice = _allsum;
			ooSumGoods = _sum_goods;
			ooSumQty = _sum_qty;

			$("#oprice_allsum").html(GC.comma(_allsum));
			$("#oprice_allsum_cn").html(GC.comma(_allsum*195));
			$("#oprice_sum_goods").html(GC.comma(_sum_goods));
			$("#oprice_sum_qty").html(GC.comma(_sum_qty));
			$("#oprice_sum_weight").html(GC.comma(_sum_weight)+"g | "+ GC.comma(Math.floor(_sum_weight*0.001))+"kg" );
		
		},

		//정보갱신
		newPrice: function(idx, oop_code) {

			var uprice = $("#unit_price_"+ idx).val();
			if( uprice == "" ){
				alert("가격 넣어주세요");
				return false;
			}

			$.ajax({
				type: "post",
				url : "processing.order_sheet.php",
				data : {
					a_mode : "newPrice",
					cd_idx : idx,
					value : uprice,
					oop_code : oop_code
				},
				success: function(res) {
					if(res.success === true) {
						var Html = "<input type='hidden' id='unit_price_"+ idx +"' value='"+ uprice +"'>"
						+ "<b>"+ Comma_int(uprice) +"</b> ";
						$("#unit_price_td_" + idx).html(Html);
					}
				}
			});

		},

		//정보갱신
		lastInfoReset: function(obj, oop_idx) {
		
			$(obj).attr('disabled', true);
			$.ajax({
				url: "processing.order_sheet.php",
				data: { "a_mode":"lastInfoReset", "oop_idx":oop_idx  },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "정보갱신", "정보 처리 완료되었습니다.");
						orderSheetPrdListShow(oop_idx, '<?=$_oog_code?>', '<?=$_oo_idx?>');
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		},

		
		//단종처리- 해제
		// soldoutmode = out 단종처리, soldoutmode = on 단종해제, 
		soldOut: function(obj, oop_idx, num, soldoutmode ) {
		
			$(obj).attr('disabled', true);
			$.ajax({
				url: "processing.order_sheet.php",
				data: { "a_mode":"soldOut", "oop_idx":oop_idx, "num":num, "soldoutmode":soldoutmode  },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						if( soldoutmode == "out" ){
							toast2("success", "단종처리", "단종 처리 완료되었습니다.");
						}else{
							toast2("success", "단종해제", "단종 해제 처리 완료되었습니다.");
						}
						orderSheetPrdListShow(oop_idx, '<?=$_oog_code?>', '<?=$_oo_idx?>');
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		},


		//주문처 추가
		shopMake: function() {

			$.alert({
				boxWidth : '700px',
				useBootstrap : false,
				title : "주문처 추가",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: 'ajax_order_sheet_shop_make.php',
						data: {  },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},
				buttons: {
					cancel: {
						text: '닫기',
						action:function () {
						//close
						}
					},
				}
			});

		},

		//목록 조회
		List: function(mode, code) {

			$(".tabmenu-line a").each(function(){
				$(this).removeClass('active');
			});

			if( mode == "info" ){
				$("#info").addClass('active');
				$("#order_form_list").hide();
				$("#order_form_info").show();
			}else{
				if( mode == "ko" ){
					$("#ko").addClass('active');
				}else if( mode == "jp" ){
					$("#jp").addClass('active');
				}
				$("#order_form_list").show();
				$("#order_form_info").hide();
				$.ajax({
					type: "post",
					url : "ajax_order_sheet_list.php",
					data : { "mode" : mode, "code" : code },
					success: function(html) {
						$("#order_form_list").html(html);
					}
				});
			}

		},

		del: function(idx) {

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제하시겠습니까?',
				content: '삭제하시면 데이터는 복구하지 못합니다.',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '삭제',
						btnClass: 'btn-red',
						action: function(){

							$.ajax({
								url: "processing.order_sheet.php",
								data: { "a_mode":"orderSheetDel", "idx":idx },
								type: "POST",
								dataType: "json",
								success: function(res){
									if (res.success == true ){
										location.href='order_sheet_test3.php';
									}else{
										showAlert("Error", res.msg, "dialog" );
										return false;
									}
								},
								error: function(){
									showAlert("Error", "에러2", "dialog" );
									return false;
								},
								complete: function() {

								}
							});

						}
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});

		}

	};

}();


$(function(){

	$("#order_sheet_del").click(function(){
		orderSheet.del($(this).data('idx'));
	});


	var content22 = '주문서 v.3은 곧 폐기될 예정입니다.<br>재고/발주 > 주문서에서 주문서 v.4 (베타버전)를 사용해 주세요.'
		+ '<br>주문서 v.3 은 v.4에서 오류 발견으로 인하여 업무처리 문제시에만 사용해 주세요.'
		+ '<br>주문서 v.4 오류 발견 시 별도로 보고해 주세요.';


	$.confirm({
		boxWidth : "500px",
		useBootstrap : false,
		icon: 'fas fa-exclamation-triangle',
		title: '공지',
		content: content22,
		type: 'red',
		typeAnimated: true,
		closeIcon: true,
		buttons: {
			somethingElse: {
				text: '주문서 v.4 바로가기',
				btnClass: 'btn-red',
				action: function(){
					location.href='/ad/order/order_sheet_main';
				}
			},
			cencle: {
				text: '주문서 v.3 사용',
				action: function(){
				}
			}
		}
	});

});

//--> 
</script>

<?
include "../layout/footer.php";
exit;
?>