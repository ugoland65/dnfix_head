<?
include "../lib/inc_common.php";

	$_oop_idx = securityVal($idx);
	$_oog_code = securityVal($oog_code);
	$_oo_idx = securityVal($oo_idx);
	$_form_view = securityVal($form_view);

	$oo_data = wepix_fetch_array(wepix_query_error("select * from ona_order where oo_idx = '".$_oo_idx."' "));
	$oog_data = wepix_fetch_array(wepix_query_error("select * from ona_order_group where oog_code = '".$_oog_code."' "));
	$oop_data = wepix_fetch_array(wepix_query_error("select * from ona_order_prd where oop_idx = '".$_oop_idx."' "));

	$_oo_state = $oo_data[oo_state];
	//$_oo_state = 4;
/*
	$form_view = "show";
	if( $_oo_state == 4 || $_oo_state == 5 || $_oo_state == 7 ){ 
		$form_view = "hidden";
	}
*/

	if( $oop_data[oop_code] == "" && $_oog_code ){
		$query = "update ona_order_prd set
			oop_code = '".$_oog_code."'
			where oop_idx = '".$_oop_idx."' ";
		wepix_query_error($query);
	}



	$form_view = $_form_view;

	$_price_colum = $oog_data[price_colum];

	$_price_colum_name['CD_SUPPLY_PRICE_2'] = "토이즈하트";
	$_price_colum_name['CD_SUPPLY_PRICE_6'] = "TIS";
	$_price_colum_name['CD_SUPPLY_PRICE_9'] = "N.P.G";
	$_price_colum_name['CD_SUPPLY_PRICE_1'] = "렌즈";

	$_price_colum_name['CD_SUPPLY_PRICE_7'] = "브랜드 A";
	$_price_colum_name['CD_SUPPLY_PRICE_8'] = "브랜드 B";

	$_price_colum_name['CD_SUPPLY_PRICE_3'] = "(원) 기타1";
	$_price_colum_name['CD_SUPPLY_PRICE_4'] = "(원) 기타2";
	$_price_colum_name['CD_SUPPLY_PRICE_5'] = "(원) 기타3";

	$_oop_json_check_data = substr($oop_data[oop_data], 0,1);
	if( $_oop_json_check_data == "[" ){
		$_oop_json = $oop_data[oop_data];
	}else{
		$_oop_json = '['.$oop_data[oop_data].']';
	}
	
	$_oop_jsondata = json_decode($_oop_json,true);

	$_select_json3 = $oo_data[oo_json];
	$_select_json4 = json_decode($_select_json3,true);

	for ($z=0; $z<count($_select_json4); $z++){
		//echo $_select_json4[$z]['bidx']."<br>";
		if( $_select_json4[$z]['bidx'] == $_oop_idx ){
			$_select_json = $_select_json4[$z]['selpd'];
			break;
		}
	}

	for ($i=0; $i<count($_select_json); $i++){ 

		$save_id = $_select_json[$i]['pidx'];

		${"_save_data_".$save_id} = "ok";
		//${"_save_data_memo_".$save_id} = str_replace('<br/>', '\r\n', $_select_json[$i]['memo']);
		${"_save_data_memo_".$save_id} = $_select_json[$i]['memo'];
/*
		if( $_select_json[$i]['false'] == true ){
			${"_false_idx_".$save_id} = "ok";
			${"_false_qty_".$save_id} = $_select_json[$i]['qty'];
			${"_false_memo_".$save_id} =  $_select_json[$i]['memo'];
		}

		$save_qty[$save_id] = $_select_json[$i]['qty'];
*/
	}


	$_false_data = "[".$oo_data[oo_false]."]";
	$_false_json = json_decode($_false_data,true);

	for ($z=0; $z<count($_false_json); $z++){
		${"_false_idx_".$_false_json[$z]['pidx']} = "ok";
		${"_false_qty_".$_false_json[$z]['pidx']} = $_false_json[$z]['qty'];
		${"_false_memo_".$_false_json[$z]['pidx']} = $_false_json[$z]['memo'];
		$save_qty[$_false_json[$z]['pidx']] = $_false_json[$z]['qty'];
	}


?>
<div class="ospl-wrap">
	<div class="ospl-top">
		<ul>

			<div>
				<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="addGoods('<?=$_oop_idx?>', '<?=$_oog_code?>', '<?=$i?>')">그룹수정</button>
				<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="orderSheet.lastInfoReset(this, '<?=$_oop_idx?>')">정보갱신</button>

				<? if( $form_view == "hidden" ){ ?>
					<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="orderSheetPrdListShow('<?=$_oop_idx?>', '<?=$_oog_code?>', '<?=$_oo_idx?>', 'show');"">전체 상품보기</button>
				<? }else{ ?>
					<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="orderSheetPrdListShow('<?=$_oop_idx?>', '<?=$_oog_code?>', '<?=$_oo_idx?>', 'hidden');">주문 상품만보기</button>
				<? } ?>

				<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs m-l-15" onclick="thisCateDel();"">이분류 상품 전부 삭제</button>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="unitAction('false')">선택 주문실패</button>
				<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="unitFalseReturn();">선택 실패복귀</button>
			</div>

			<div class="m-t-10">
				oop_idx : <?=$_oop_idx?> | 
				<? if( !$oog_data[price_colum] ){ ?>
					※가격 그룹이 설정되지 않았습니다.
				<? }else{ ?>
					가격그룹 : <b><?=$_price_colum_name[$oog_data[price_colum]]?></b> ( <?=$oog_data[price_colum]?> )
				<? } ?>
				| oop_code : <b><?=$oop_data[oop_code]?></b>
			</div>

		</ul>
		<ul class="btn">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="addOrder();" > 
				상품담기
			</button>
		</ul>
	</div>
</div>

<!-- <?=$oo_data[oo_false]?> -->
<!-- <?=$oo_data[oo_json]?> -->

<table class="table-list">
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

	$_price_json = $_oop_jsondata[$z]['price']*1;
	$_show_price_json = number_format($_price_json,1);

	$_weight = $_oop_jsondata[$z]['weight'];
	$_last = $_oop_jsondata[$z]['last'];
	//$_show_weight = number_format($_weight);
	$_state = $_oop_jsondata[$z]['state'];
	

	$_this_line_show = "show";
	if( ${"_save_data_".$_idx} != "ok" && ${"_false_idx_".$_idx} != "ok" ){
		if( $form_view == "hidden" ) $_this_line_show = "hidden";
	}

/* ********************************************************************************* */
	if( $_this_line_show != "hidden" ){

		$_colum = "CD_CODE, CD_CODE2, CD_CODE3, CD_NAME, 
			CD_SUPPLY_PRICE_1, CD_SUPPLY_PRICE_2, CD_SUPPLY_PRICE_3, CD_SUPPLY_PRICE_4, CD_SUPPLY_PRICE_5, CD_SUPPLY_PRICE_6, CD_SUPPLY_PRICE_7, CD_SUPPLY_PRICE_8, CD_SUPPLY_PRICE_9, 
			CD_WEIGHT, CD_WEIGHT2, CD_WEIGHT3, cd_code_fn, cd_weight_fn, cd_price_fn";

		$_query = "select 
			A.*,
			B.ps_idx, B.ps_stock, B.ps_cafe24_sms
			from "._DB_COMPARISON." A
			left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX  ) 
			where CD_IDX = '".$_idx."' ";

		$comparison_data = wepix_fetch_array(wepix_query_error($_query));
		
		if($comparison_data[CD_IMG] ){
			$img_path = '../../data/comparion/'.$comparison_data[CD_IMG];
		}

		//$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx,ps_stock from prd_stock where ps_prd_idx = '".$_idx."' "));


		$_cd_code_data = json_decode($comparison_data[cd_code_fn], true);
		$_cd_price_data = json_decode($comparison_data[cd_price_fn], true);

		$_jan = $comparison_data[CD_CODE];
		$_code2 = $comparison_data[CD_CODE2];
		$_code3 = $comparison_data[CD_CODE3];

		if( $_cd_code_data[$oop_data[oop_code]] ){
			$_code2 = $_cd_code_data[$oop_data[oop_code]];
		}

		$_pname = $comparison_data[CD_NAME];
		if( $_price_colum ){
			$_price = $comparison_data[$_price_colum];
			$_show_price = number_format($_price,2);
		}

		if( $oop_data[oop_code] && $_cd_price_data[$oop_data[oop_code]] == "" && $_price > 0 ){

			//array_push($_cd_price_data, $oop_data[oop_code] => $_price);
			//$_cd_price_data = array($oop_data[oop_code] => $_price);
			$_cd_price_data[$oop_data[oop_code]] = $_price;

			$_cd_price_fn = json_encode($_cd_price_data);
			$query = "update "._DB_COMPARISON." set cd_price_fn = '".$_cd_price_fn."' where CD_IDX = '".$_idx."'";
			wepix_query_error($query);
		
		}

//echo $_cd_price_data[$oop_data[oop_code]];

		if( $_cd_price_data[$oop_data[oop_code]] ){
			$_price = $_cd_price_data[$oop_data[oop_code]];
			$_show_price = number_format($_cd_price_data[$oop_data[oop_code]],2);
		}

		$_cd_weight_data = json_decode($comparison_data[cd_weight_fn], true);
		$_cd_weight_1 = $_cd_weight_data['1'];
		$_cd_weight_2 = $_cd_weight_data['2'];
		$_cd_weight_3 = $_cd_weight_data['3'];

		if( $_cd_weight_3 ){
			$_weight = $_cd_weight_3;
		}else{
			$_weight = max($_cd_weight_1, $_cd_weight_2);
		}
/*
		if( $comparison_data[CD_WEIGHT3] ){
			$_weight = $comparison_data[CD_WEIGHT3];
		}else{
			$_weight = max($comparison_data[CD_WEIGHT], $comparison_data[CD_WEIGHT2]);
		}
*/


		//$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx,ps_stock from prd_stock where ps_prd_idx = '".$_idx."' "));

	/*
		$last_in = wepix_fetch_array(wepix_query_error("select * from prd_stock_unit where psu_stock_idx = '".$stock_data[ps_idx]."' 
				and psu_mode = 'plus' and psu_kind = '신규입고' order by psu_date desc"));
		$_last = "";
		if($last_in[psu_idx]){ $_last = "( ".$last_in[psu_qry]." ) - ".$last_in[psu_memo].""; }else{ $_last = "입고정보 없음"; }
	*/

		//주문 실패 상태일경우
		if( ${"_false_idx_".$_idx} == "ok" ){
			$_tr_color= "#adadad";
			$_memo = ${"_false_memo_".$_idx};
		}else{
			if( $comparison_data[ps_stock] == 0 ){
				$_tr_color= "#eee";
			}else{
				$_tr_color= "#fff";
			}
			$_memo = ${"_save_data_memo_".$_idx};
		}

		if( $_state == "out" ){
			$_tr_color= "#a3c1ab";
		}

		$_ps_cafe24_sms_data = json_decode($comparison_data[ps_cafe24_sms], true);

?>

<tr bgcolor = "<?=$_tr_color?>" id="tr_<?=$_idx?>" >
	<td id="checkbox_td_<?=$_idx?>">
		<input type="checkbox" name="key_check[]"  id="checkbox_<?=$_idx?>" class="checkSelect" value="<?=$_idx?>" style="<? if( ${"_false_idx_".$_idx} == "ok" ){?>display:none;<?}?>">
	</td>
	<td>
		<?=$_idx?><br>
		<b style="font-size:13px; color:#2525fa;"><?=$_ps_idx?></b>
	</td>
	<td>
		<b><?=$_code2?></b>
		<? if( $_code3 ){ ?><br><?=$_code3?><? } ?>
	</td>
	<td>
		<input type="checkbox" name="key_check2[]"  id="checkbox2_<?=$_idx?>" class="checkSelect2" value="<?=$_idx?>" >
	</td>
	<td style="width:70px;">
		<img src="<?=$img_path?>" style="height:60px; border:1px solid #eee !important;">
	</td>
	<td class="text-left">
		<!-- <div><?=$comparison_data[cd_price_fn]?></div> -->
		<div><?=$_jan?></div>
		<div class="p-t-5 p-b-5 p-l-3">
			<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="comparisonQuick('<?=$_idx?>','info');"">보기</button> <b><?=$_pname?></b>
			<? if( $_om ){ ?><br><span style="color:#ff0000; display:inline-block; margin-top:3px; font-size:11px;"><?=$_om?></span><? } ?>
		</div>
		<? if( $_state == "on" ){ ?>
			<div><button type="button" id="aa" class="btnstyle1 btnstyle1-xs" onclick="orderSheet.soldOut(this, '<?=$_oop_idx?>', '<?=$z?>','out')">단종처리</button></div>
		<? }else{ ?>
			<div><button type="button" id="aa" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheet.soldOut(this, '<?=$_oop_idx?>', '<?=$z?>','on')">단종해제</button></div>
		<? } ?>
		

	</td>

	<!-- 주문메모 -->
	<td style="width:90px; padding:0 !important; ">
		<textarea name="memo" id="memo_<?=$_idx?>" style="width:100%; height:56px; background-color:transparent;  border:none !important; resize: none; padding:5px; margin:0 !important; box-sizing:border-box;  color:#ff0000;"><?=$_memo?></textarea>
	</td>

	<!-- 상품가격 -->
	<td class="text-right" id="unit_price_td_<?=$_idx?>" data-price="<?=$_price?>" style="width:100px;">
		<?=$_show_price_json?><br>
		<? if( $_price == 0 ){ ?>
			<input type='text' name='' id="unit_price_<?=$_idx?>" style="width:60px;" value="" onkeyUP="qtyGogo('<?=$_idx?>', '<?=$_oop_idx?>');"><button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-xs m-l-2" onclick="orderSheet.newPrice('<?=$_idx?>', '<?=$oop_data[oop_code]?>')" ><i class="fas fa-save"></i></button>
		<? }else{ ?>

			<input type='hidden' name='' id="unit_price_<?=$_idx?>" value="<?=$_price?>">
			<span><a href="#" class="editable-cd-price editable-click" data-url="processing.order_sheet.php" data-pk="<?=$_show_price?>" data-cdidx="<?=$_idx?>" data-oopidx="<?=$_oop_idx?>" data-pricecolum="<?=$_price_colum?>" data-oopcode="<?=$oop_data[oop_code]?>" data-title="가격 수정"><b><?=$_show_price?></b></a></span>

		<? } ?>

	</td>
	
	<!-- 주문수량 -->
	<td style="width:55px;">
			<input type='text' name='cd_code2' id="unit_qty_<?=$_idx?>" style="width:100%; font-size:15px; font-weight:bold; color:<? if( ${"_false_idx_".$_idx} == "ok" ){ echo "#999"; }else{ echo "#021aff"; } ?>;" value="<?=$save_qty[$_idx]?>" onkeyUP="qtyGogo('<?=$_idx?>', '<?=$_oop_idx?>');">
	</td>

	<!-- 상품 주문 총 가격 -->
	<td class="text-right width-60">
		<input type="hidden" name="" id="unit_price_sum_<?=$_idx?>" class="unit-price-sum-data" value="<?=$unit_price_sum?>">
		<? if( $_state == "on" ){ ?>
		<span id="order_qty_sum_<?=$_idx?>" class="unit-price-sum" ><?=$save_unit_price?></span>
		<? }else{ ?>
			단종
		<? } ?>
	</td>

	<td style="width:30px;">
		<b onclick="comparisonQuick('<?=$_idx?>','stock');" style="cursor:pointer; <? if( $comparison_data[ps_stock] == 0 ) echo "color:#aaa;"; ?>"><?=$comparison_data[ps_stock]?></b>
	</td>
	<td class="text-left" style="width:100px; font-size:11px;">
		
		<? if( $_ps_cafe24_sms_data['count'] > 0 ){ ?>
		<div style="background-color:#ffb5b5; padding:4px; margin-bottom:3px; border-radius:5px; border:1px solid #cf7979;">
			<ul>입고알림 : <b><?=$_ps_cafe24_sms_data['count']?></b></ul>
			<ul class="m-t-2" style="font-size:10px;"><?=date("m.d H:i:s", strtotime($_ps_cafe24_sms_data['date']))?></ul>
		</div>
		<? } ?>

		<div><?=$_last?></div>
	</td>
	<td class="text-right " style="width:55px;"><span id="weight_<?=$_idx?>" class="unit-weight <? if($_weight_mode=="2") echo "no-weight"; ?>" data-weight="<?=$_weight?>"><?=number_format($_weight)?></span>g</td>
</tr>
<? 
		} 
	}
/* ********************************************************************************* */
?>
</table>

<script type="text/javascript"> 
<!-- 

function selectGo(){

<? 
if( count($_select_json) > 0 ){
	for ($i=0; $i<count($_select_json); $i++){ 
		if( ${"_false_idx_".$_select_json[$i]['pidx']} != "ok" ){
?>
/*
	if( $("#unit_state_<?=$_ary_save_data_oo_c_idx[$i]?>").val() != "c" ){
		$("#tr_<?=$_ary_save_data_oo_c_idx[$i]?> td").css({'background':'#ffcbcb' }); 
		$("#checkbox_<?=$_ary_save_data_oo_c_idx[$i]?>").attr("checked", true);
	}
*/
	//$("#tr_<?=$_select_json[$i]['pidx']?> td").css({'background':'#ffcbcb' }); 
	$("#checkbox_<?=$_select_json[$i]['pidx']?>").attr("checked", true);
	$("#unit_qty_<?=$_select_json[$i]['pidx']?>").val(<?=$_select_json[$i]['qty']?>);
	qtyGogo('<?=$_select_json[$i]['pidx']?>', '<?=$_oop_idx?>');

<? } } } ?>

}

selectGo();

$(document).ready(function(){

	// 가격변경
	$('.editable-cd-price').editable({
			type: 'text',
			inputclass:'testinput',
			params: function(params) {
				params.a_mode = 'newPrice';
				params.cd_idx = $(this).data('cdidx');
				params.price_colum = $(this).data('pricecolum');
				params.oop_code = $(this).data('oopcode');
				return params;
			},	
			success: function(res) {
				if(res.success === true) {
					$("#unit_price_" + $(this).data('cdidx')).val(res.uprice);
					qtyGogo($(this).data('cdidx'), $(this).data('oopidx'));
				}else{
					showAlert("Error", res.msg, "alert2" );
					return false;
				}
			},
			validate: function(value) {
				if($.trim(value) == '') {
					return '빈 값은 입력할 수 없습니다.';
				}

			}
	});

});
//--> 
</script> 