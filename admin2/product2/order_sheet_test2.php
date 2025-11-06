<?
$pageGroup = "product2";
$pageName = "order_sheet2";

include "../lib/inc_common.php";
include "../layout/header.php";

$_get_oog_code = securityVal($get_oog_code);
$_oo_idx = securityVal($oo_idx);

if($_get_oog_code){
	$_oog_code = $_get_oog_code;
}

$_oo_token = make_token(8,"order");

	$_koedge_order_state_text[1] = "작성중";
	$_koedge_order_state_text[2] = "주문전송";
	$_koedge_order_state_text[4] = "입금완료";
	$_koedge_order_state_text[5] = "입고완료";
	$_koedge_order_state_text[7] = "주문종료";

	$btn_text_submit = "주문서 생성";

//이값이 있다는것은 수정임
if($_oo_idx){

	$btn_text_submit = "주문서 수정";

	$ona_order_data = wepix_fetch_array(wepix_query_error("select * from ona_order where oo_idx = '".$_oo_idx."' "));
	$_oo_state = $ona_order_data[oo_state];
	$_oo_token = $ona_order_data[oo_token];
	$_oog_code = $ona_order_data[oo_code];


	$show_oo_name = " | ( ".$ona_order_data[oo_name]." )";
	$_ary_save_data_oo_c_idx = explode(",", $ona_order_data[oo_c_idx]);
	$_ary_save_data_oo_memo = explode("★", $ona_order_data[oo_memo]);
	$_ary_save_data_oo_qty = explode(",", $ona_order_data[oo_qty]);
	$_ary_save_data_oo_unit_state = explode(",", $ona_order_data[oo_unit_state]);

	for ($i=0; $i<count($_ary_save_data_oo_c_idx); $i++){
		$save_id = $_ary_save_data_oo_c_idx[$i];
		${"_save_data_".$save_id} = "ok";
		$save_memo[$save_id] = $_ary_save_data_oo_memo[$i];
		$save_qty[$save_id] = $_ary_save_data_oo_qty[$i];
		$save_unit_state[$save_id] = $_ary_save_data_oo_unit_state[$i];
		if( $_ary_save_data_oo_unit_state[$i] == "c" ){
			$_ary_unit_state_false[] = $_ary_save_data_oo_c_idx[$i];
		}
	}

	//주문 실패수 구하기
	$nunitState = array_count_values($_ary_save_data_oo_unit_state);
	$nunitStateFalseCount = ( $nunitState['c'] )? $nunitState['c'] : 0;
}

$form_view = "show";
if( $_oo_state == 4 || $_oo_state == 5 || $_oo_state == 7 ){ 
	$form_view = "hidden";
}


$oog_data = wepix_fetch_array(wepix_query_error("select * from ona_order_group where oog_code = '".$_oog_code."' "));

// -------------------------------------------------------------------------------------
$_oog_idx = $oog_data[oog_idx];
$_oprice_mode = $oog_data[oog_code];
$_ary_or_cg_data = $oog_data[oog_data];
$_ary_or_cg_all = explode("|", $_ary_or_cg_data);
// -------------------------------------------------------------------------------------

$_brand_json = '['.$oog_data[oog_brand].']';
$_brand_json_data = json_decode($_brand_json,true);
?>
<STYLE TYPE="text/css">
.no-weight{ color:#ff0000; }
.table-list tr td{ padding:1px 2px !important; }
.unit-price-sum{ font-size:13px; color:#2525fa; font-weight:bold; }

<?
/*
if( $_oo_state == 4 || $_oo_state == 5 || $_oo_state == 7 ){ 
?>
.order-sheet-brand-name,
.order-sheet-brand-sum,
.order-sheet-brand-sum2{ display:none; }
<? } 
*/
?>

</STYLE>

<script type="text/javascript"> 
<!-- 
var oo_token = "<?=$_oo_token?>";

var oo_sum_price = 0;
var oo_sum_goods = 0;
var oo_sum_qty = 0;
var oo_sum_weight = 0;

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

function qtyGogo( v, id ) {
/*
	var oprice =  eval("oprice_"+ id);
	if( oprice == 0 ) {
		oprice = $("#unit_price_"+ id).val();
	}
*/
	var oprice = $("#unit_price_"+ id).data("price");

	if(v=="") v=0;
	var oprice_sum = oprice*v;
	if( oprice_sum > 0 ){
		$("#unit_price_sum_"+ id).val(oprice_sum);
		$("#order_qty_sum_"+ id).html(Comma_int(oprice_sum));
		//alert("켜져");
		ckTr(id,"on");
	}else{
		$("#unit_price_sum_"+ id).val("");
		$("#order_qty_sum_"+ id).html("");
		//alert("꺼져");
		ckTr(id,"off");
	}
	//alert(oprice_sum);
	allSum();
}

function newOS(code) {
	location.href='order_sheet_test2.php?get_oog_code='+code;
}
//--> 
</script> 
<div id="contents_head">
	<h1>주문서<?=$show_oo_name?></h1>
    <div id="head_write_btn">
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total display-none">
			</ul>
			<ul class="list-top-btn text-left">

				<select name="" onchange="newOS(this.value);">
					<option value="" >주문서 선택</option>
<?
$query = "select * from ona_order_group order by oog_idx desc";
$result = wepix_query_error($query);
while($list = wepix_fetch_array($result)){
?>
					<option value="<?=$list[oog_code]?>" <? if( $_get_oog_code == $list[oog_code] ) echo "selected";?> ><?=$list[oog_name]?></option>
<? } ?>
				</select>

	<? if( $_oog_idx ){ ?>
		<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="groupConfig();">그룹관리</button>
	<? } ?>

	<? if($_oo_idx){ ?>
		<!-- <button type="button" id="show_type_all" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="orderSGroup('<?=$_oprice_mode?>');">주문폼 수정</button> -->
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="orderPrint('<?=$_oo_idx?>', '<?=$_oprice_mode?>');">주문서 출력</button>

		선택 : 
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="unitAction('false')">주문실패</button>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="">실패복귀</button>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="">단종처리</button>
	<? } ?>

	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm m-l-20" onclick="oderState('<?=($_oo_state*1)-1?>')">이전단계</button>
<? if( $_oo_state == 1 ){ ?>
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm " onclick="oderState('2')">주문전송 처리</button>
<? }elseif( $_oo_state == 2 ){ ?>
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm " onclick="oderState('4')">입금완료 처리</button>
<? }elseif( $_oo_state == 4 ){ ?>
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-gary btnstyle1-sm " onclick="">입금완료 처리</button>
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="oderState('5')">입고완료 처리</button>
<? }elseif( $_oo_state == 5 ){ ?>
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-gary btnstyle1-sm " onclick="">입금완료 처리</button>
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-gary btnstyle1-sm " onclick="">입고완료 처리</button>

	<input type='text' name='oo_name' id='stock_day' style="width:80px" value="<?=date("Y-m-d")?>" >
	<input type='text' name='oo_name' id='stock_all_memo' style="width:100px" value="<?=$ona_order_data[oo_name]?> 입고"  >
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm " onclick="stockWrite()">재고등록</button>
<? } ?>

(<?=$_oo_state?>)

			</ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">

					<table class="table-list">
<!-- 
						<tr>
							<th class="tl-check"><input type="checkbox" name="check_box_all" class="check_box_all" onclick="select_all()"></th>
							<th style="width:45px;">IDX</th>
						</tr>
 -->
<?
//for i
for ($i=0; $i<count($_brand_json_data); $i++){

	$_show_brand_idx = $_brand_json_data[$i]['brand'];
	$_show_brand_name = $_brand_json_data[$i]['name'];
	$_oop_idx = $_brand_json_data[$i]['oop_idx'];

	$oop_data = wepix_fetch_array(wepix_query_error("select * from ona_order_prd where oop_idx = '".$_oop_idx."' "));

	$_oop_json = '['.$oop_data[oop_data].']';
	$_oop_jsondata = json_decode($_oop_json,true);

	
?>
<tr>
	<th colspan="20" class="order-sheet-brand-name" id="osbn_<?=$_show_brand_idx?>"  style="height:40px;">
		( <?=$_oop_idx?> )
		<b style="font-size:15px"><?=$_show_brand_name?></b>
		( <?=count($_oop_jsondata)?> 개 )
		<? if( $_oo_state < 4 ){ ?>
		<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="addGoods('<?=$_oop_idx?>', '<?=$_oog_code?>', '<?=$i?>')">그룹수정</button>
		<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="groupInfoReload('<?=$_oop_idx?>')">정보갱신</button>
		<? } ?>

	</th>
</tr>

<?
// for z
for ($z=0; $z<count($_oop_jsondata); $z++){

	$_idx = $_oop_jsondata[$z]['idx'];
	$_ps_idx = $_oop_jsondata[$z]['stockidx'];
	$_code = $_oop_jsondata[$z]['code'];
	$_jan = $_oop_jsondata[$z]['jan'];
	$_kind = $_oop_jsondata[$z]['kind'];
	$_pname = $_oop_jsondata[$z]['pname'];
	$_om = $_oop_jsondata[$z]['om'];
	$_price = $_oop_jsondata[$z]['price']*1;
	$_show_price = number_format($_price,1);
	$_weight = $_oop_jsondata[$z]['weight'];
	$_last = $_oop_jsondata[$z]['last'];
	//$_show_weight = number_format($_weight);
	$_state = $_oop_jsondata[$z]['state'];

	$_this_line_show = "show";
	if( ${"_save_data_".$_idx} != "ok" ){
		if( $form_view == "hidden" ) $_this_line_show = "hidden";
	}

	if( $_this_line_show != "hidden" ){

		$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx,ps_stock from prd_stock where ps_prd_idx = '".$_idx."' "));

			if( ${"_save_data_".$_idx} == "ok" ){
				$save_unit_price = number_format($_price*$save_qty[$_idx],1);
				//주문 실패 상태일경우
				if( $save_unit_state[$_idx] == "c"){
					$unit_price_sum = 0;
				}else{
					$unit_price_sum = $_price*$save_qty[$_idx];
					$_sum1 += $save_qty[$_idx];
					$_sum2 += $unit_price_sum;
				}
			}else{
				$save_goods = "";
				$save_unit_price = "";
				$unit_price_sum = 0;
			}

			//주문 실패 상태일경우
			if( $save_unit_state[$_idx] == "c"){
				$_tr_color= "#adadad";
			}else{
				if( $stock_data[ps_stock] == 0 ){
					$_tr_color= "#eee";
				}else{
					$_tr_color= "#fff";
				}
			}
		
		if( $_state == "out" ){
			$_tr_color= "#a3c1ab";
		}
?>

<script type="text/javascript"> 
<!-- 
	var oprice_<?=$_idx?> = "<?=$_oprice?>";
//--> 
</script> 
<input type="hidden" name="" id="unit_state_<?=$_idx?>" value="<?=$save_unit_state[$_idx]?>">
<input type="hidden" name="" id="unit_stock_idx_<?=$_idx?>" value="<?=$stock_data[ps_idx]?>">


<tr bgcolor = "<?=$_tr_color?>" id="tr_<?=$_idx?>">
	<td>
<?
	//주문 실패 상태일경우
	if( $save_unit_state[$_idx] == "c" OR $_state == "out" ){
?>
<? }else{ ?>
		<input type="checkbox" name="key_check[]"  id="checkbox_<?=$_idx?>" class="checkSelect" value="<?=$_idx?>" >
<? } ?>

	</td>
	<td><?=$_idx?></td>
	<td><b style="font-size:13px; color:#2525fa;"><?=$_ps_idx?></b></td>
	<td><?=$_code?></td>
	<td style="width:100px"><?=$_jan?></td>
	<td>
		<? if( $_state == "on" ){ ?>
			<input type="checkbox" name="key_check2[]"  id="checkbox2_<?=$_idx?>" class="checkSelect2" value="<?=$_idx?>" >
		<? } ?>
	</td>
	<td style="width:60px"><?=$_kind?></td>
	<td class="text-left" style="width:180px;">
		<? if( $_state == "on" ){ ?>
			<input type='text' name='memo' id="memo_<?=$_idx?>" value="<?=$save_memo[$_idx]?>" style="width:100%; height:20px;">
		<? } ?>
	</td>
	<td class="text-left">
		<div class="p-t-5 p-b-5 p-l-3">
			<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="comparisonQuick('<?=$_idx?>','info');"">보기</button> <b><?=$_pname?></b>
			<? if( $_om ){ ?><br><span style="color:#ff0000; display:inline-block; margin-top:3px; font-size:11px;"><?=$_om?></span><? } ?>
		</div>
	</td>
	<td class="text-right"><b id="unit_price_<?=$_idx?>" data-price="<?=$_price?>"><?=$_show_price?></b></td>

	<? if( $_oo_state == 5 ){ ?>

	<td style="width:40px; font-size:15px; font-weight:bold; color:<? if( $save_unit_state[$_idx] == "c"){ echo "#777"; }else{ echo "#021aff"; } ?>;">
		<input type="hidden" name="" id="unit_qty_<?=$_idx?>" value="<?=$save_qty[$_idx]?>" >
		<?=$save_qty[$_idx]?>
	</td>
	<? if( $save_unit_state[$_idx] == "c"){ ?>
	<td>
	</td>
	<td>
	</td>
	<? }else{ ?>

	<td style="width:40px;">
		<input type='text' name='cd_code2' id="unit_stock_<?=$_idx?>" style="width:100%; font-size:15px; font-weight:bold; color:#021aff;" >
	</td>
	<td class="text-left" style="width:150px;">
		<input type='text' name='memo' id="unit_stock_memo_<?=$_idx?>" value="" style="width:100%; height:20px;">
	</td>
	<? } ?>

<? }else{ ?>

	<td style="width:40px;">
		<? if( $_state == "on" ){ ?>
			<input type='text' name='cd_code2' id="unit_qty_<?=$_idx?>" style="width:100%; font-size:15px; font-weight:bold; color:<? if( $save_unit_state[$_idx] == "c"){ echo "#999"; }else{ echo "#021aff"; } ?>;" value="<?=$save_qty[$_idx]?>" onkeyUP="qtyGogo( this.value, '<?=$_idx?>' );">
		<? }else{ ?>
			<input type="hidden" name="" id="unit_qty_<?=$_idx?>"  value="">
			단종
		<? } ?>
	</td>

<? } ?>

	<td class="text-right width-60">
		<input type="hidden" name="" id="unit_price_sum_<?=$_idx?>" class="unit-price-sum-data" value="<?=$unit_price_sum?>">
		<? if( $_state == "on" ){ ?>
		<span id="order_qty_sum_<?=$_idx?>" class="unit-price-sum" ><?=$save_unit_price?></span>
		<? }else{ ?>
			단종
		<? } ?>
	</td>

	<td style="width:30px;">
		<b onclick="comparisonQuick('<?=$_idx?>','stock');" style="cursor:pointer; <? if( $stock_data[ps_stock] == 0 ) echo "color:#aaa;"; ?>"><?=$stock_data[ps_stock]?></b>
	</td>

	<td class="text-left" style="width:100px; font-size:11px;">
		<?=$_last?>
	</td>

	<td class="text-right "><span id="weight_<?=$_idx?>" class="unit-weight <? if($_weight_mode=="2") echo "no-weight"; ?>"><?=number_format($_weight)?></span>g</td>
</tr>
<?

	} //if( $_this_line_show != "hidden" ){

}  // for z END
?>

<tr>
	<th colspan="20">

<!-- <?=$oop_data[oop_data]?> -->
<?
/*
	echo "<pre>";
	print_r($_oop_jsondata);
	echo "</pre>";
*/
?>

	</th>
</tr>
<? 
} // for ($i=0; $i<count($_brand_json_data); $i++){
?>
					</table>

				</div>
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
					<ul class="filter-menu-title"><i class="fas fa-filter"></i> 합계</ul>

					<ul class="filter-from-ui m-t-5">
						전체 주문 금액 : <b style="font-size:15px"><span id="oprice_allsum"><?=number_format($ona_order_data[oo_sum_price],1)?></span></b>
					</ul>
					<ul class="filter-from-ui m-t-5">
						전체 주문 상품 : <b style="font-size:15px"><span id="oprice_sum_goods"><?=number_format($ona_order_data[oo_sum_goods])?></span></b>
					</ul>
					<ul class="filter-from-ui m-t-5">
						전체 주문 수량 : <b style="font-size:15px"><span id="oprice_sum_qty"><?=number_format($ona_order_data[oo_sum_qty])?></span></b>
					</ul>
					<ul class="filter-from-ui m-t-5">
						전체 주문 무게 : <b style="font-size:15px">
						<span id="oprice_sum_weight">
							<?=number_format($ona_order_data[oo_sum_weight])?>g / <?=number_format($ona_order_data[oo_sum_weight]*0.001)?>kg
						</span>
						</b>
					</ul>
				
					<ul class="filter-from-ui m-t-10">
						<input type='text' name='oo_name' id='oo_name' size='20' value="<?=$ona_order_data[oo_name]?>" placeholder="주문서 이름">
					</ul>

				<? if($_oo_idx){ ?>
					<ul class="filter-from-ui m-t-10">
						등록 : <?=date("y.m.d H:i",$ona_order_data[oo_date]);?> | 수정 : <?=date("y.m.d H:i",$ona_order_data[oo_date_modify]);?>
					</ul>
				<? } ?>

				<? if( $_oo_state != 5 && $_oo_state != 7 ){ ?>
					<ul class="filter-from-ui m-t-5">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="makeOrder();" > 
							<?=$btn_text_submit?>
						</button>
					</ul>
				<? } ?>
				
					<ul class="filter-menu-title m-t-20"><i class="fas fa-filter"></i> 목록</ul>

					<ul class="filter-from-ui m-t-5">

<STYLE TYPE="text/css">
.opltb{ width:100%; }	
.opltb tr{}	
.opltb tr td{ padding:5px 3px; border:1px solid #ddd; }	
</STYLE>

<table class="opltb">
<?
$query = "select * from ona_order order by oo_sort desc limit 0, 25";
$result = wepix_query_error($query);
while($list = wepix_fetch_array($result)){

	if( $list[oo_state] == "2" ){
		$_tr_color = "#c1ebff";
	}elseif( $list[oo_state] == "4" ){
		$_tr_color = "#f6f0ac";
	}elseif( $list[oo_state] == "7" ){
		$_tr_color = "#eaeaea";
	}else{
		$_tr_color = "#ffffff";
	}

	if( $list[oo_r_mode] == "V3" ){
		$_oo_r_mode = "<i class='fas fa-star'></i> ";
		$_link_url = "order_sheet_test3.php";
	}else{
		$_oo_r_mode = "";
		$_link_url = "order_sheet_test2.php";
	}

?>
	<tr bgcolor="<?=$_tr_color?>">
		<td style="width:60px; text-align:center;"><?=$_koedge_order_state_text[$list[oo_state]]?></td>
		<td style="width:65px; text-align:right;"><?=number_format($list[oo_sum_price])?></td>
		<td><a href="<?=$_link_url?>?oo_idx=<?=$list[oo_idx]?>"><?=$_oo_r_mode?><?=$list[oo_name]?></a></td>
	</tr>
<? } ?>
</table>

					</ul>


				</div>
			</ul>
		</div>

	</div>
</div>

<script type="text/javascript"> 
<!-- 
<?
if($_oo_idx){
?>
	//var nunitFalseCount = <?=$nunitState['c']?>;
$(function(){

<?
	for ($i=0; $i<count($_ary_save_data_oo_c_idx); $i++){
?>
	if( $("#unit_state_<?=$_ary_save_data_oo_c_idx[$i]?>").val() != "c" ){
		$("#tr_<?=$_ary_save_data_oo_c_idx[$i]?> td").css({'background':'#ffcbcb' }); 
		$("#checkbox_<?=$_ary_save_data_oo_c_idx[$i]?>").attr("checked", true);
	}
<? } ?>
	allSum();
});
<? } ?>

function allSum() {

	var oprice_allsum = 0;
	var oprice_sum_qty = 0;
	var oprice_sum_goods = 0;
	var oprice_sum_weight = 0;
/*
	$(".unit-price-sum-data").each(function(){
		//oprice_allsum = oprice_allsum + unComma_int($(this).text());
		oprice_allsum = oprice_allsum + ($(this).val()*1);
	});
*/
	//alert($(".checkSelect:checked").length);
	$(".checkSelect:checked").each(function(){
		var checkbox_id = $(this).val();

		if( $("#unit_qty_" + checkbox_id).val() == "" ){
			var plus_oprice_sum_qty = 1;
		}else{
			var plus_oprice_sum_qty = ($("#unit_qty_" + checkbox_id).val() * 1);
			//var plus_oprice_sum_qty = ( $("#unit_price_"+ checkbox_id).data("price") * 1 );
		}
		
		oprice_allsum = oprice_allsum + ($("#unit_price_sum_" + checkbox_id).val() * 1);
		oprice_sum_goods++;
		
		oprice_sum_qty = oprice_sum_qty + plus_oprice_sum_qty;

		oprice_sum_weight = oprice_sum_weight + (unComma_int($("#weight_"+checkbox_id).text()) * ($("#unit_qty_"+checkbox_id).val() * 1));
	});

	$("#oprice_allsum").html(Comma_int(oprice_allsum));
	$("#oprice_sum_goods").html(Comma_int(oprice_sum_goods));
	$("#oprice_sum_qty").html(Comma_int(oprice_sum_qty));
	$("#oprice_sum_weight").html(Comma_int(oprice_sum_weight)+"g / "+Comma_int(oprice_sum_weight*0.001)+"kg" );
//unit-qty$(this).val()

	//console.log(oprice_allsum);
	oo_sum_price = oprice_allsum;
	oo_sum_goods =  oprice_sum_goods;
	oo_sum_qty = oprice_sum_qty;
	oo_sum_weight = oprice_sum_weight;
}


function stockWrite(mode){

	var stock_all_memo = $("#stock_all_memo").val();
	var stock_day = $("#stock_day").val();

	var send_array2 = "";
	var qty_array = "";
	var unit_stock_idx = "";
	var unit_stock_array = "";
	var unit_stock_memo_array = "";

	$(".checkSelect:checked").each(function(index){
		var checkbox_id = $(this).val();
		if(index!=0){ 
			send_array2 += ",";
			qty_array += ",";
			unit_stock_idx += ",";
			unit_stock_array += ",";
			unit_stock_memo_array += ",";
		}
		send_array2 += $(this).val();
		qty_array += $("#unit_qty_"+checkbox_id).val();
		unit_stock_idx += $("#unit_stock_idx_"+checkbox_id).val();
		if( $("#unit_stock_"+checkbox_id).val() == "" ){
			unit_stock_array += $("#unit_qty_"+checkbox_id).val();
		}else{
			unit_stock_array += $("#unit_stock_"+checkbox_id).val();
		}
		unit_stock_memo_array += $("#unit_stock_memo_"+checkbox_id).val();
	});

	//alert(qty_array);
	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : { 
			a_mode : "stockWrite",
			modify_idx : "<?=$_oo_idx?>",
			oo_token : oo_token,
			stock_all_memo : stock_all_memo,
			stock_day : stock_day,
			send_array2 : send_array2,
			qty_array : qty_array,
			unit_stock_idx : unit_stock_idx,
			unit_stock_array : unit_stock_array,
			unit_stock_memo_array : unit_stock_memo_array
		},
		success: function(getdata) {
			makedata = getdata.split('|');
			ckcode = makedata[1];
			msg = makedata[3];
			if(ckcode=="Processing_Complete"){
				location.reload();
				//alert(msg);

			}else if(ckcode=="Value_null"){

			}
		}
	});

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


function unitAction(mode){

	if( $(".checkSelect2:checked").length == 0 ){
		alert('상품을 선택해주세요.');
		return false;
	}

	if ( mode == "false"){
		var a_mode = "nunitFalse";
	}

	var target_idx = "";

	$(".checkSelect2:checked").each(function(index){
		var checkbox_id = $(this).val();
		if(index!=0){ 
			target_idx += ",";
		}
		target_idx += $(this).val();
	});

//alert(a_mode+"/"+target_idx);

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : { 
			a_mode : a_mode,
			target_idx : target_idx,
			modify_idx : "<?=$_oo_idx?>"
		},
		success: function(getdata) {
			makedata = getdata.split('|');
			ckcode = makedata[1];
			msg = makedata[3];
			if(ckcode=="Processing_Complete"){
				//alert(msg);
				location.reload();
			}else if(ckcode=="Value_null"){

			}
		}
	});
}

function makeOrder(mode){
		
	var oo_name = $('#oo_name').val();

	if( oo_name == "" ){
		alert('주문서 이름!');
		return false;
	}

	if( $(".checkSelect:checked").length == 0 ){
		alert('상품을 선택해주세요.');
		return false;
	}

	var send_array2 = "";
	var code2_array = "";
	var code_array = "";
	var price_array = "";
	var qty_array = "";
	var memo_array = "";
	var unit_state_array = "";

	$(".checkSelect:checked").each(function(index){

		var checkbox_id = $(this).val();
		if(index!=0){ 
			send_array2 += ",";
			code2_array += ",";
			code_array += ",";
			price_array += ",";
			qty_array += ",";
			memo_array += "★";
			unit_state_array += ",";
		}

		send_array2 += $(this).val();
		code2_array += $("#code2_"+checkbox_id).val();
		code_array += $("#code_"+checkbox_id).val();
		price_array += $("#unit_price_"+checkbox_id).val();
		qty_array += $("#unit_qty_"+checkbox_id).val();
		memo_array += $("#memo_"+checkbox_id).val();
		if( $("#unit_state_"+checkbox_id).val() == "" ){
			unit_state_array += "i";
		}else{
			unit_state_array += $("#unit_state_"+checkbox_id).val();
		}

	});
	//alert(code2_array);

<? 
	if( $nunitStateFalseCount > 0 ){ 
		for ($i=0; $i<count($_ary_unit_state_false); $i++){
?>
		send_array2 += ",<?=$_ary_unit_state_false[$i]?>";
		code2_array += "," + $("#code2_<?=$_ary_unit_state_false[$i]?>").val();
		code_array += "," + $("#code_<?=$_ary_unit_state_false[$i]?>").val();
		price_array += "," + $("#unit_price_<?=$_ary_unit_state_false[$i]?>").val();
		qty_array += "," + $("#unit_qty_<?=$_ary_unit_state_false[$i]?>").val();
		memo_array += "★" + $("#memo_<?=$_ary_unit_state_false[$i]?>").val();
		unit_state_array += ",c";
<? 
	} //for END 
}
?>

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : { 
<?
if($_oo_idx){
?>
			a_mode : "modifyOrder",
			modify_idx : "<?=$_oo_idx?>",
			oo_state : "<?=$_oo_state?>",
<? } else{ ?>
			a_mode : "makeOrder",
<? } ?>
			oo_name : oo_name,
			oo_code : "<?=$_oprice_mode?>",
			oo_token : oo_token,
			oo_sum_price : oo_sum_price ,
			oo_sum_goods : oo_sum_goods,
			oo_sum_qty : oo_sum_qty,
			oo_sum_weight : oo_sum_weight,
			send_array2 : send_array2,
			code2_array : code2_array,
			code_array : code_array,
			price_array : price_array,
			qty_array : qty_array,
			memo_array : memo_array,
			unit_state_array : unit_state_array
		},
		success: function(getdata) {
			makedata = getdata.split('|');
			ckcode = makedata[1];
			processing_mode = makedata[3];
			processing_idx = makedata[4];
			//alert(ckcode+"/"+processing_mode);
			if(ckcode=="Processing_Complete"){
				//costShow( idx );
				if( processing_mode == "makeOrder" ){
					location.href='order_sheet_test2.php?oo_idx='+ processing_idx;
				}else{
					location.reload();
				}

			}else if(ckcode=="Value_null"){

			}
		}
	});

}

function orderPrint(idx, code){
	window.open("/admin2/product2/popup.order_sheet_print.php?idx="+idx+"&code="+ code, "orderSGroup_"+ code, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

function orderSGroup(code){
	window.open("/admin2/product2/popup.order_sheet_group.php?code="+ code, "orderSGroup_"+ code, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

function addGoods(oop_idx, oog_code, oog_arynum){

	$.ajax({
		type: "post",
		url : "ajax_order_sheet_add_product_new.php",
		data : { oop_idx : oop_idx, oog_code : oog_code, oog_arynum : oog_arynum, oog_idx : <?=$_oog_idx?> },
		success: function(getdata) {
			$("#popup_layer_body").html(getdata);
		}
	});

	showPopup('1300','800','ajax');
	
}

function groupConfig(){

	$.ajax({
		type: "post",
		url : "ajax_order_sheet_group_config.php",
		data : { oog_idx : <?=$_oog_idx?> },
		success: function(getdata) {
			$("#popup_layer_body").html(getdata);
		}
	});

	showPopup('1300','800','ajax');
	
}

function groupInfoReload(oop_idx){

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data: {
			a_mode : "groupInfoReload",
			oop_idx : oop_idx
		},
		success: function(res) {
			if(res.success === true) {
				//alert(res.msg);
				window.location.reload();
			}
		}
	});

}
//--> 
</script>

<?
include "../layout/footer.php";
exit;
?>