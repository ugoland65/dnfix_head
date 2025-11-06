<?
	include "../lib/inc_common.php";

	$_idx = securityVal($prd_idx);

	$data = sql_fetch_array(sql_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));

	$_cd_weight_data = json_decode($data['cd_weight_fn'], true);
	$_cd_weight_1 = $_cd_weight_data['1'];
	$_cd_weight_2 = $_cd_weight_data['2'];
	$_cd_weight_3 = $_cd_weight_data['3'];

	$_cd_price_data = json_decode($data['cd_price_fn'], true);


/*
	if( $data[CD_KIND_CODE] ){
		$_tg_code = $data[CD_KIND_CODE];
	}else{
		$_tg_code = "ONAHOLE";
	}


	$tag_query = "select * from "._DB_TAG." where TG_ACTIVE = 'Y' and TG_CODE = '".$_tg_code."' order by TG_HEADER asc";
	$tag_result = wepix_query_error($tag_query); 
	while($tag_list = wepix_fetch_array($tag_result)){
		if($tag_list[TG_HEADER] == '2'){	
			$_ary_two_depth_idx[] = $tag_list[TG_IDX];
			$_ary_two_depth_name[] = $tag_list[TG_NAME];
		}elseif($tag_list[TG_HEADER] == '3'){	
			${"_ary_three_depth_".$tag_list[TG_PARENT_IDX]."_idx"}[] = $tag_list[TG_IDX];
			${"_ary_three_depth_".$tag_list[TG_PARENT_IDX]."_name"}[] = $tag_list[TG_NAME];
		}
	}

	$com_tag_result = wepix_query_error("select * from "._DB_COMPARISON_TAG." where CT_CD_IDX = '".$_idx."' ");
	while($com_tag_list = wepix_fetch_array($com_tag_result)){
		$_ary_com_tag[] = $com_tag_list[CT_TG_IDX];
	}
*/
?>
<STYLE TYPE="text/css">
.cost-show-wrap{ background-color:#222; padding:30px; box-sizing:border-box; color:#ddd !important;  }
.show_cost_result{}
.show_cost_result ul{ padding:3px; color:#ddd !important;  }
.cost-p{ color:#ffce0a; font-size:13px; }
.o-p{ color:#48efb6; font-size:13px; }
.show_cost{ margin-top:8px; }
.sc-title{ font-weight:bold; font-size:13px; color:#eee; }
</STYLE>

<div class="crm-title">
	<h3>가격 정보</h3>
</div> 

<?
	echo "<pre>";
	print_r($_cd_price_data);
	echo "</pre>";
?>

<form name='form1' id='form1' action='<?=_A_PATH_COMPARISON_OK?>' method='post' enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="a_mode" value="priceModifyPopup">
<input type="hidden" name="idx" value="<?=$data[CD_IDX]?>">

<div class="crm-detail-info">
	<table class="table-style">
		<tr>
			<th class="tds1">수입국가</th>
			<td class="tds2">
				<input type="radio" name="cd_national" value="jp" <? if( $data[cd_national] == "jp" ) echo "checked"; ?>> 일본
				<input type="radio" name="cd_national" value="cn" <? if( $data[cd_national] == "cn" ) echo "checked"; ?>> 중국
				<input type="radio" name="cd_national" value="kr" <? if( $data[cd_national] == "kr" ) echo "checked"; ?>>  한국
			</td>
		</tr>

		<tr>
			<th class="tds1">중량</th>
			<td class="tds2">
				상품중량 : <input type='text' name='cd_weight_1' style='width:80px;'  value="<?=$_cd_weight_1?>">
				전체중량 : <input type='text' name='cd_weight_2' style='width:80px;'  value="<?=$_cd_weight_2?>">
				실측중량 : <input type='text' name='cd_weight_3' style='width:80px;'  value="<?=$_cd_weight_3?>"> 
				<div class="admin-guide-text">
					- 단위 g (숫자만 등록할것)
				</div>
			</td>
		</tr>

		<tr>
			<th class="tds1">가격</th>
			<td class="tds2">

				<div>
					토이즈하트 : <input type='text' name='cd_price_th' style='width:70px;' value="<?=number_format($_cd_price_data['th'])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">엔 &nbsp; | &nbsp;

					TIS : <input type='text' name='cd_supply_price_6' style='width:70px;'  value="<?=number_format($_cd_price_data['tis'])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"> 엔&nbsp; | &nbsp;
					
					N.P.G : <input type='text' name='cd_price_npg' style='width:70px;'  value="<?=number_format($_cd_price_data['npg'])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"> 엔&nbsp; | &nbsp;

					라이드 : <input type='text' name='cd_price_rj' style='width:70px;'  value="<?=number_format($_cd_price_data['rj'])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"> 엔&nbsp; | &nbsp;

					렌즈 : <input type='text' name='cd_supply_price_1' style='width:70px;'  value="<?=number_format($data[CD_SUPPLY_PRICE_1])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">엔
				</div>

				<div style="margin-top:5px;">
					브랜드 A : <input type='text' name='cd_supply_price_7' style='width:70px;'  value="<?=number_format($data[CD_SUPPLY_PRICE_7])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">엔&nbsp; | &nbsp;
					브랜드 B : <input type='text' name='cd_supply_price_8' style='width:70px;' value="<?=number_format($data[CD_SUPPLY_PRICE_8])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">엔&nbsp; | &nbsp;
				</div>
				<div style="margin-top:5px;">
					(원) 기타1 : <input type='text' name='cd_supply_price_3' style='width:70px;'  value="<?=number_format($data[CD_SUPPLY_PRICE_3])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">원&nbsp; | &nbsp;
					(원) 기타2 : <input type='text' name='cd_supply_price_4' style='width:70px;'  value="<?=number_format($data[CD_SUPPLY_PRICE_4])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">원&nbsp; | &nbsp;
					(원) 기타3 : <input type='text' name='cd_supply_price_5' style='width:70px;'  value="<?=number_format($data[CD_SUPPLY_PRICE_5])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">원
				</div>
			</td>
		</tr>

		<tr>
			<th class="tds1">쑈당몰 판매가</th>
			<td class="tds2"><input type='text' name='cd_sale_price' style='width:100px;'  value="<?=number_format($data['cd_sale_price'])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></td>
		</tr>

		<tr>
			<th class="tds1">외부 판매가</th>
			<td class="tds2">
			외부1 <input type='text' name='cd_out_price_1' style='width:100px;'  value="<?=number_format($data[CD_OUT_PRICE_1])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"><br>
			지마켓/옥션 <input type='text' name='cd_out_price_2' style='width:100px;'  value="<?=number_format($data[CD_OUT_PRICE_2])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"><br>
			오픈마켓2 <input type='text' name='cd_out_price_3' style='width:100px;'  value="<?=number_format($data[CD_OUT_PRICE_3])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"><br>
			쿠팡 <input type='text' name='cd_out_price_4' style='width:100px;'  value="<?=number_format($data[CD_OUT_PRICE_4])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
			</td>
		</tr>
	</table>
</div> 

	</form> 

	<div class="cost-show-wrap">
		<div id="cost_show">
		</div>
	</div>

<!-- 
<div class="text-center m-t-10">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-submit" onclick=" goSave()"> 
		<i class="far fa-check-circle"></i> 수정
	</button>
</div>
 -->

<script type="text/javascript"> 
<!-- 
function goSave(){

	var formData = $("#form1").serializeArray();

	$.ajax({
		url: "<?=_A_PATH_COMPARISON_OK?>",
		data : formData, 
		type: "POST",
		dataType: "json",
		success: function(res){
			if (res.success == true ){
				toast2("success", "가격정보", "설정이 저장되었습니다.");
				prdShow('price');
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

	//$("#form1").submit();
}

function costShow(){
	$.ajax({
		url: "ajax_cost_show.php",
		data: {
			"idx":"<?=$_idx?>",
			"yen":"<?=$yen?>",
			"kg_p":"<?=$kg_p?>"
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$("#cost_show").html(getdata);
		},
		error: function(){
		}
	});
}

costShow();
//--> 
</script> 