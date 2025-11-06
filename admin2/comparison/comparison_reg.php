<?
$pageGroup = "comparison";
$pageName = "comparison_reg";
include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	$_idx = securityVal($key);
	$_return_query_string_list = securityVal($return_query_string_list);
	$_s_brand = securityVal($s_brand);

	if( $_mode == "modify" ){

		$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));

		$page_title_text = "가격비교 수정";
		$submit_btn_text = "가격비교 수정";

		$com_tag_result = wepix_query_error("select CT_TG_IDX from "._DB_COMPARISON_TAG." where CT_CD_IDX = '".$_idx."' ");
		while($com_tag_list = wepix_fetch_array($com_tag_result)){
			$_ary_com_tag[] = $com_tag_list[CT_TG_IDX];
		}

		$query = "select * from "._DB_COMPARISON." com inner join "._DB_COMPARISON_TAG." com_tag ".$_where_sql." group by com.CD_IDX";
		$result = wepix_query_error($query);

	}else{

		$page_title_text = "가격비교 등록";
		$submit_btn_text = "가격비교 등록";

	}

	$st_query = "select SD_IDX,SD_NAME from "._DB_SITE." where SD_ACTIVE = 'Y'";

	if( $comparison_data[CD_KIND_CODE] ){
		$_tg_code = $comparison_data[CD_KIND_CODE];
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

	$brand_query = "select BD_IDX,BD_NAME from "._DB_BRAND." order by BD_NAME DESC";
	$brand_result = wepix_query_error($brand_query); 
	while($brand_list = wepix_fetch_array($brand_result)){
		$_ary_brand_key[] = $brand_list[BD_IDX];
		$_ary_brand_name[] = $brand_list[BD_NAME];
	}

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
.price-min-best-active{ color:#0075ff; }
</STYLE>

<script type='text/javascript'>
var siteCount = 0;
var site_Data = "";
<?
$st_result = wepix_query_error($st_query); 
while($st_list = wepix_fetch_array($st_result)) {
	$_ary_sd_idx[] = $st_list[SD_IDX];
	$_ary_sd_name[] = $st_list[SD_NAME];
?>
	site_Data += "<option value='<?=$st_list[SD_IDX]?>'><?=$st_list[SD_NAME]?></option>";
<? } ?>

var siteAdd=function(){
	
	siteCount++;

	var showHtml2 =""
	+"<tr id='option_tr_"+ siteCount +"'>"
	+"<td class='tds2'>"
	+"<label onclick='priceMinBest(this)'><input type='checkbox' name='price_min_best_checkbox'> 최저가 지정<input type='hidden' name='price_min_best[]'></label>"
	+"<select name='cl_site[]'>"
	+"<option value=''>Select Site</option>"
	+site_Data
	+"</select>"
	+"</td>"
	+"<input type='hidden' name='cl_idx[]' value='0'>"
	+"<td class='tds2'><input type='text' name='cl_sort[]'></td>"
	+"<td class='tds2'>"
	+"<input type='text' name='cl_price[]'  style='width:80px !important;'> "
	+"<input type='text' name='cl_delivery[]'   style='width:80px !important;'> "
	+"<input type='text' name='cl_memo[]' style='width:200px !important;'> "
	+"<select name='cl_kind[]'>"
	+"<option value='' selected>구분없음</option>"
	+"<option value='qodp'>해외구매대행(빠른직구)</option>"
	+"<option value='odp-jpy'>해외직구(일본)</option>"
	+"<option value='nobenefit'>무혜택</option>"
	+"</select><br>"
	+"<textarea name='cl_path[]' style='margin-top:5px; height:60px; background-color:#f7f7f7; border:1px solid #999;'></textarea>"
	+"</td>"
	+"<td class='tds2'><button type=\"button\" class=\"btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3 \" onclick=\"siteDel('"+siteCount+"');\"><i class=\"far fa-trash-alt\"></i></button></td>"
	+"</tr>";

	$("#site_table").append(showHtml2);
};

var siteDel = function(key){
    $('#option_tr_'+key).remove();
};

function sitedataDel(idx){
	alert(idx);
	$.ajax({
			type: "post",
			url : "<?=_A_PATH_COMPARISON_OK?>",
			data : { 
				a_mode : "comparisonLinkDel",
				idx : idx ,
			},
			success: function(getdata) {
				alert(getdata);
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					location.reload();
				}else if(ckcode=="Value_null"){

				}
			}
		});
}

function goSubmit(){
	var form = document.form1;
	form.submit();
}

function changeTag(num,idx,type){
		var id = "tg_"+type+"_"+num;
		var checked_yn = $("input:checkbox[id='"+id+"']").is(":checked");
	if(checked_yn == false){

		var checked_value = $("input:checkbox[id='"+id+"']").val();

		$.ajax({
			type: "post",
			url : "<?=_A_PATH_COMPARISON_OK?>",
			data : { 
				a_mode : "TagDel",
				cd_idx : idx ,
				tg_idx : checked_value
			},
			success: function(getdata) {
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					alert('삭제완료');

				}else if(ckcode=="Value_null"){

				}
			}
		});

	}else{

	}


}

</script>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
    <div id="head_write_btn">

	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		
		<div class="display-table">
			<ul class="display-table-cell">


			<div class="table-wrap">
			<form name='form1' action='<?=_A_PATH_COMPARISON_OK?>' method='post' enctype="multipart/form-data" autocomplete="off">
			
			<? if( $_mode == "modify" ){ ?>
				<input type="hidden" name="a_mode" value="comparisonModify">
				<input type="hidden" name="idx" value="<?=$comparison_data[CD_IDX]?>">
				<input type="hidden" name="cd_maching_code" value="<?=$comparison_data[CD_MACHING_CODE]?>">
				<input type='hidden' name="img_name" id="img_name" value="<?=$comparison_data[CD_IMG]?>" >
			<? }else{ ?>
				<input type="hidden" name="a_mode" value="comparisonNew">
			<? } ?>
				<input type="hidden" name="return_query_string_list" value="<?=$_return_query_string_list?>">


				<table cellspacing="1" cellpadding="0" class="table-style">
					<tr>
						<th class="tds1">구분</th>
						<td class="tds2">
<?
for($t=0; $t<count($koedge_prd_kind_array); $t++){
?>
							<label><input type="radio" name="cd_kind_code" value="<?=$koedge_prd_kind_array[$t]['code']?>" <? if($comparison_data[CD_KIND_CODE] == $koedge_prd_kind_array[$t]['code'] ) echo "checked"; ?>> <?=$koedge_prd_kind_array[$t]['name']?></label>
<? } ?>
<!-- 
							<label><input type="radio" name="cd_kind_code" value="ONAHOLE" <? if($comparison_data[CD_KIND_CODE] == "ONAHOLE" || !$comparison_data[CD_KIND_CODE] ) echo "checked"; ?>> 오나홀</label>
							<label><input type="radio" name="cd_kind_code" value="REALDOLL" <? if($comparison_data[CD_KIND_CODE] == "REALDOLL" ) echo "checked"; ?>> 리얼돌</label>
							<label><input type="radio" name="cd_kind_code" value="WOMAN" <? if($comparison_data[CD_KIND_CODE] == "WOMAN" ) echo "checked"; ?>> 여성용품</label>
							<label><input type="radio" name="cd_kind_code" value="SIDE" <? if($comparison_data[CD_KIND_CODE] == "SIDE" ) echo "checked"; ?>> 보조용품</label>
							<label><input type="radio" name="cd_kind_code" value="GEL" <? if($comparison_data[CD_KIND_CODE] == "GEL" ) echo "checked"; ?>> 윤활젤</label>
							<label><input type="radio" name="cd_kind_code" value="CONDOM" <? if($comparison_data[CD_KIND_CODE] == "CONDOM" ) echo "checked"; ?>> 콘돔</label>
 -->
						</td>
					</tr>
				 <tr>
					 <th class="tds1">브랜드</th>
					 <td class="tds2">

<?
if( $comparison_data[CD_BRAND_IDX] ){
	$_cl_brand = $comparison_data[CD_BRAND_IDX];
}else{
	$_cl_brand = $_s_brand;
}
?>
						<select name='cl_brand'>
							<option value=''>Select Brand</option>
							<?
							for ($i=0; $i<count($_ary_brand_name); $i++){
							?>
							<option value='<?=$_ary_brand_key[$i]?>'<? if( $_ary_brand_key[$i] == $_cl_brand ) echo "selected"; ?>><?=$_ary_brand_name[$i]?></option>
							<? } ?>
						</select>

						<select name='cl_brand2'>
							<option value=''>Select Brand</option>
							<?
							for ($i=0; $i<count($_ary_brand_name); $i++){
							?>
							<option value='<?=$_ary_brand_key[$i]?>'<? if( $_ary_brand_key[$i] == $comparison_data[CD_BRAND2_IDX] ) echo "selected"; ?>><?=$_ary_brand_name[$i]?></option>
							<? } ?>
						</select>

					</td>
				 </tr>

					<tr>
						<th class="tds1">가격비교 노출</th>
						<td class="tds2">
							<label><input type="radio" name="cd_comparison" value="Y" <? if($comparison_data[CD_COMPARISON] == "Y" || !$comparison_data[CD_COMPARISON] ) echo "checked"; ?>> 노출</label>
							<label><input type="radio" name="cd_comparison" value="N" <? if($comparison_data[CD_COMPARISON] == "N" ) echo "checked"; ?>> 비노출</label>
						</td>
					</tr>

			     <tr>
					 <th class="tds1">이름</th>
					 <td class="tds2"><input type='text' name='cd_name'  size='40' value="<?=$comparison_data[CD_NAME]?>" ></td>
				 </tr>
			     <tr>
					 <th class="tds1">해외 이름</th>
					 <td class="tds2"><input type='text' name='cd_name_og'  size='40' value="<?=$comparison_data[CD_NAME_OG]?>" ></td>
				 </tr>
			     <tr>
					 <th class="tds1">영문명</th>
					 <td class="tds2"><input type='text' name='cd_name_en'  size='40' value="<?=$comparison_data[CD_NAME_EN]?>" ></td>
				 </tr>
			    <tr>
					 <th class="tds1">간략 내용</th>
					 <td class="tds2"><input type='text' name='cd_cont'  value="<?=$comparison_data[CD_CONT]?>"></td>
			     </tr>

		<tr>
			<td colspan="2" style="background-color:#dddddd; border:none !important; padding: 0 !important; height:10px;"></td>
		</tr>

					<tr>
						<th class="tds1">검색어</th>
						<td class="tds2"><input type='text' name='cd_search_term' value="<?=$comparison_data[CD_SEARCH_TERM]?>" ></td>
					</tr>


			     <tr>
					 <th class="tds1">태그</th>
					 <td class="tds2">
						<table class="table-style">
						 <?
							for($a=0;$a<count($_ary_two_depth_idx);$a++){
						 ?>
						 <tr>
							 <th class="tds1"><?=$_ary_two_depth_name[$a]?></th>
							 <td class="tds2">
							<?
								for($i=0;$i<count(${"_ary_three_depth_".$_ary_two_depth_idx[$a]."_idx"});$i++){

								$_view_tag_idx = ${"_ary_three_depth_".$_ary_two_depth_idx[$a]."_idx"}[$i];
								$_view_tag_name = ${"_ary_three_depth_".$_ary_two_depth_idx[$a]."_name"}[$i];
							?>
								<label><input type='checkbox'  name='tg_structure[]' id='tg_structure_<?=$_view_tag_idx?>' 
								<?if(in_array($_view_tag_idx, $_ary_com_tag)){ echo "checked";}?> value='<?=$_view_tag_idx?>'
								onclick="changeTag('<?=$_view_tag_idx?>','<?=$_idx?>','structure')"><?=$_view_tag_name?> </label> 
								<?}?>
							 </td>
						 </tr>
						 <?}?>
						</table>
					 </td>
				 </tr>




				 <tr>
					 <th class="tds1">카테고리</th>
					 <td class="tds2"><input type='text' name='cd_category' value="<?=$comparison_data[CD_CATEGORY]?>"></td>
			     </tr>

		<tr>
			<td colspan="2" style="background-color:#dddddd; border:none !important; padding: 0 !important; height:10px;"></td>
		</tr>

				 <tr>
					 <th class="tds1">이미지</th>
					 <td class="tds2">
					 이미지 사이즈 : 302 x 302(px)<br>
					 <?
						if($comparison_data[CD_IMG] ){
							$img_path = '../../data/comparion/'.$comparison_data[CD_IMG];
						}
					?>
					 <input type='file' name='cd_img'>
						<img src="<?=$img_path?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
						(<?=$img_path?>)
			
					 </td>
			     </tr>
			    <tr>
					 <th class="tds1">외부이미지 URL 저장</th>
					 <td class="tds2"><input type='text' name='out_img'  value=""></td>
			     </tr>

<tr>
	<th class="tds1">출시일</th>
	<td class="tds2">
		<div class="calendar-input">
			<input type='text' name='cd_release_date'  value="<?=$comparison_data[CD_RELEASE_DATE]?>" >
		</div>
	</td>
</tr>

<tr>
	<th class="tds1">패키지 사이즈</th>
	<td class="tds2">
		세로(H) : <input type='text' name='cd_size_h' value="" style="width:60px">
		가로(W) : <input type='text' name='cd_size_w' value="" style="width:60px">
		깊이(D) : <input type='text' name='cd_size_d' value="" style="width:60px">
		<div class="admin-guide-text">
			- 단위 mm (숫자만 등록할것)
		</div>
	</td>
</tr>

<tr>
	<th class="tds1">중량</th>
	<td class="tds2">
		상품중량 : <input type='text' name='cd_weight_1' style='width:80px;'  value="<?=$comparison_data[CD_WEIGHT]?>">
		전체중량 : <input type='text' name='cd_weight_2' style='width:80px;'  value="<?=$comparison_data[CD_WEIGHT2]?>">
		실측중량 : <input type='text' name='cd_weight_3' style='width:80px;'  value="<?=$comparison_data[CD_WEIGHT2]?>"> 
		<div class="admin-guide-text">
			- 단위 g (숫자만 등록할것)
		</div>
	</td>
</tr>

<tr>
	<th class="tds1">상품 코드</th>
	<td class="tds2">
		바코드 (JAN) : <input type='text' name='cd_code' style='width:200px;' value="<?=$comparison_data[CD_CODE]?>">
		상품 품번 : <input type='text' name='cd_code2' style='width:100px;' value="<?=$comparison_data[CD_CODE2]?>">
	</td>
</tr>

<tr>
	<th class="tds1">주문 코드</th>
	<td class="tds2">
		<div>
			N.P.G : <input type='text' name='cd_code_npg' style='width:80px;'  value="<?=$comparison_data[CD_WEIGHT]?>">		
			라이드재팬 : <input type='text' name='cd_code_rj' style='width:80px;'  value="<?=$comparison_data[CD_WEIGHT]?>">		
			매직아이즈 : <input type='text' name='cd_code_mg' style='width:80px;'  value="<?=$comparison_data[CD_WEIGHT]?>">		
		</div>
		<div class="m-t-5">
			핫파워즈 : <input type='text' name='cd_code_hp' style='width:80px;'  value="<?=$comparison_data[CD_WEIGHT]?>">
			데몬킹 : <input type='text' name='cd_code_dmw' style='width:80px;'  value="<?=$comparison_data[CD_WEIGHT]?>">		
		</div>
		<div class="admin-guide-text">
			- 타마토이즈 상품일경우 상품 품번만 넣어줘도 됨
		</div>
	</td>
</tr>

				 <tr>
					 <th class="tds1">내부길이</th>
					 <td class="tds2">
						<input type='text' name='cd_size2' style='width:100px;'  value="<?=$comparison_data[CD_SIZE2]?>"> ( Cm )
						<br>※ 젤일때는 용량( ml )
					 </td>
			     </tr>

				 <tr>
					 <th class="tds1">색상</th>
					 <td class="tds2"><input type='text' name='cd_color' value="<?=$comparison_data[CD_COLOR]?>"></td>
			     </tr>
				<tr>
					 <th class="tds1">메모</th>
					 <td class="tds2"><textarea name='cd_memo' style='height:70px;' ><?=$comparison_data[CD_MEMO]?></textarea></td>
			     </tr>
				 <tr>
					 <th class="tds1">구성품 여부</th>
					 <td class="tds2">
					 <input type='radio' name='cd_supplement' <?if($comparison_data[CD_SUPPLEMENT] == "Y"){ echo "checked";}?> value="Y"> Y
					 <input type='radio' name='cd_supplement' <?if($comparison_data[CD_SUPPLEMENT] == "N" || !$comparison_data[CD_KIND_CODE] ) echo "checked"; ?> value="N"> N
					 </td>
			     </tr>

				 <tr>
					 <th class="tds1">공급가</th>
					 <td class="tds2">
						<div>
							토이즈하트 : <input type='text' name='cd_price_th' style='width:70px;' value="<?=number_format($comparison_data[CD_SUPPLY_PRICE_2])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">엔 &nbsp; | &nbsp;
							TIS : <input type='text' name='cd_supply_price_6' style='width:70px;'  value="<?=number_format($comparison_data[CD_SUPPLY_PRICE_6])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"> 엔&nbsp; | &nbsp;
							N.P.G : <input type='text' name='cd_price_npg' style='width:70px;'  value="<?=number_format($comparison_data[CD_SUPPLY_PRICE_9])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"> 엔
						</div>
						<div style="margin-top:5px;">
							브랜드 A : <input type='text' name='cd_supply_price_7' style='width:70px;'  value="<?=number_format($comparison_data[CD_SUPPLY_PRICE_7])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">엔 |
							브랜드 B : <input type='text' name='cd_supply_price_8' style='width:70px;' value="<?=number_format($comparison_data[CD_SUPPLY_PRICE_8])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">엔 
						</div>
						<div style="margin-top:5px;">
							NLS : <input type='text' name='cd_supply_price_1' style='width:70px;'  value="<?=number_format($comparison_data[CD_SUPPLY_PRICE_1])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">엔 |
							성원 : <input type='text' name='cd_supply_price_3' style='width:70px;'  value="<?=number_format($comparison_data[CD_SUPPLY_PRICE_3])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">원 |
							에스토이 : <input type='text' name='cd_supply_price_4' style='width:70px;'  value="<?=number_format($comparison_data[CD_SUPPLY_PRICE_4])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">원
						</div>
						<div style="margin-top:5px;">
							기타1 : <input type='text' name='cd_supply_price_5' style='width:70px;'  value="<?=number_format($comparison_data[CD_SUPPLY_PRICE_5])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"> |
							
						</div>
					 </td>
			     </tr>

				 <tr>
					 <th class="tds1">쑈당몰 판매가</th>
					 <td class="tds2"><input type='text' name='cd_sale_price' style='width:100px;'  value="<?=number_format($comparison_data[CD_SALE_PRICE])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></td>
			     </tr>

				 <tr>
					 <th class="tds1">외부 판매가</th>
					 <td class="tds2">
						외부1 <input type='text' name='cd_out_price_1' style='width:100px;'  value="<?=number_format($comparison_data[CD_OUT_PRICE_1])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"><br>
						지마켓/옥션 <input type='text' name='cd_out_price_2' style='width:100px;'  value="<?=number_format($comparison_data[CD_OUT_PRICE_2])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"><br>
						오픈마켓2 <input type='text' name='cd_out_price_3' style='width:100px;'  value="<?=number_format($comparison_data[CD_OUT_PRICE_3])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"><br>
						쿠팡 <input type='text' name='cd_out_price_4' style='width:100px;'  value="<?=number_format($comparison_data[CD_OUT_PRICE_4])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
					</td>
			     </tr>

				 <tr>
					 <th class="tds1">해쉬태그</th>
					 <td class="tds2"><input type='text' name='cd_hashtag' value="<?=$comparison_data[CD_HASH_TAG]?>"></td>
			     </tr>

				 <tr>
					<th class="tds1">기본 설정</th>
					<td class="tds2">
						평점 <input type='text' style='width:50px;' name='cd_score' value="<?=$comparison_data[CD_SCORE]?>">
						리뷰수 <input type='text' style='width:50px;' name='cd_review' value="<?=$comparison_data[CD_REVIEW]?>">
						찜하기 <input type='text' style='width:50px;' name='cd_keep' value="<?=$comparison_data[CD_KEEP]?>">
						갱신일 <input type='text' style='width:120px;' name='cd_update_date' id="search_st" readonly value="<?=date("Y-m-d",$comparison_data[CD_UPDATE_DATE])?>">
					</td>
				 </tr>

				 <tr>
					<th class="tds1">연관 상품</th>
					<td class="tds2">
						<input type='text' name='cd_related_goods' value="<?=$comparison_data[CD_RELATED_GOODS]?>">
						<div>구분자 "|"</div>
					</td>
			     </tr>

				 <tr>
					 <th class="tds1">추천 상품</th>
					 <td class="tds2"><input type='text' name='cd_recommend_goods' value="<?=$comparison_data[CD_RECOMMEND_GOODS]?>"></td>
			     </tr>
	
</table>
				
			</form>
			</div>

				<div class="page-btn-wrap">
					<ul class="page-btn-left">
						<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_COMPARISON_LIST?>?<?=$_return_query_string_list?>'" > 
							<i class="fas fa-arrow-left"></i>
							목록으로
						</button>
					</ul>
					<ul class="page-btn-right">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="goSubmit();" > 
							<i class="far fa-check-circle"></i>
							<?=$submit_btn_text?>
						</button>
					</ul>
				</div>

			</ul>
			<ul class="display-table-cell">

				<div id="comment">
				</div>

			</ul>
		</div>


		
	</div>
</div>

<script type="text/javascript"> 
<!-- 
function showComment(pn){
	if( pn== "" ) pn ="";
	$.ajax({
		url: "ajax.comparison_comment.php",
		data: {
			"key":"<?=$_idx?>",
			"pn":pn
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$('#comment').html(getdata);
		},
		error: function(){
		}
	});
}

showComment();

function priceMinBest(obj){
	if ($('input[type="checkbox"]', obj).prop('checked')) {
		$('input[type="checkbox"][name="price_min_best_checkbox"]').prop('checked', false);
		$('input[type="hidden"][name="price_min_best[]"]').attr('value', '');
		$('input[type="checkbox"]', obj).prop('checked', true);
		$('input[type="hidden"]', obj).val('ok');
	}
}


$(function(){

	var content22 = '이 페이지는 곧 폐기될 예정입니다.<br>상품관리 v.3를 이용해주세요.'
		+ '<br>상품등록도 v.3를 이용해주세요.';


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
			cencle: {
				text: '확인/닫기',
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