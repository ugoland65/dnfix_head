<?
	include "../lib/inc_common.php";
	include 'board_inc.php';

	$_idx = securityVal($prd_idx);

	$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));

	$site_query = "select SD_IDX,SD_NAME from "._DB_SITE." where SD_ACTIVE = 'Y' ";

	$site_result = wepix_query_error($site_query); 
	while($site_list = wepix_fetch_array($site_result)){
		$_ary_sd_idx[] = $site_list[SD_IDX];
		$_ary_sd_name[] = $site_list[SD_NAME];
	}
?>
<STYLE TYPE="text/css">
.crm-detail-info{}
.price-min-best-active{ color:#0075ff; }
</STYLE>
<div class="crm-title">
	<h3>최저가 정보</h3>
</div> 

	<form name='form1' id='form1' action='<?=_A_PATH_COMPARISON_OK?>' method='post' enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="a_mode" value="comparisonModifyPopup">
	<input type="hidden" name="idx" value="<?=$_idx?>">

<div class="crm-detail-info">
	<table class="table-style">
		 <tr>
			 <th class="tds1">최저가 노출 가격</th>
			 <td class="tds2"><input type='text' name='cd_price' style='width:100px;'  value="<?=number_format($comparison_data[CD_PRICE])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></td>
		 </tr>
		 <tr>
			 <th class="tds1">최저가 바로가기 링크</th>
			 <td class="tds2"><input type='text' name='cd_link' value="<?=$comparison_data[CD_LINK]?>"></td>
		 </tr>
		 <tr>
			 <th class="tds1">최저가 지정 IDX</th>
			 <td class="tds2"><?=$comparison_data[CD_LINK_IDX]?></td>
		 </tr>
	</table>
</div> 

<div class="crm-title">
	<h3>가격비교</h3>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:150px; margin:5px; float:right;" onclick="siteAdd()"> <i class="fas fa-plus-circle"></i> 비교 싸이트 추가</button>
</div> 
<div class="crm-detail-info">
	
	<table class="table-style" id='site_table'>
		<tr>
			<th class="text-center" style='width:150px !important;'>싸이트</th>
			<th class="text-center"  style='width:50px !important;'>정렬 </th>
			<th class="text-center" >가격 / 배송비 / 부가 정보</th>
			<th class="text-center"  style='width:40px !important;'>삭제</th>
		</tr>
		<?
			$link_result = wepix_query_error("select * from "._DB_COMPARISON_LINK." where CL_CD_IDX = '".$_idx."' order by 
	case CL_KIND 
	when 'odp-jpy' then 9 
	when 'qodp' then 8 
	else 1 end,
	if(CL_PRICE=0,999999999999,CL_PRICE) asc,
	CL_SORT_NUM asc
			");	
			$link_num = 20;
			while($link_list = wepix_fetch_array($link_result)){
			$link_num++;	
		?>
		<tr id='option_tr_<?=$link_num?>'>
			<td >
				<label onclick='priceMinBest(this)' class="<? if( $comparison_data[CD_LINK_IDX] == $link_list[CL_IDX] ) echo "price-min-best-active"; ?>"><input type='checkbox' name='price_min_best_checkbox' <? if( $comparison_data[CD_LINK_IDX] == $link_list[CL_IDX] ) echo "checked"; ?>> 최저가 지정<input type='hidden' name='price_min_best[]' value="<? if( $comparison_data[CD_LINK_IDX] == $link_list[CL_IDX] ) echo "ok"; ?>"></label>
				<select name='cl_site[]'>
					<option value=''>Select Site</option>
	<?
	for ($i=0; $i<count($_ary_sd_idx); $i++){
	?>
					<option value='<?=$_ary_sd_idx[$i]?>' <? if( $_ary_sd_idx[$i] == $link_list[CL_SD_IDX] ) echo "selected"; ?>><?=$_ary_sd_name[$i]?></option>
	<? } ?>
				</select>
				<div><?=$link_list[CL_IDX]?></div>
			</td>
			<input type='hidden' name='cl_idx[]' value="<?=$link_list[CL_IDX]?>">
			<td class='tds2'><input type='text' name='cl_sort[]' value="<?=$link_list[CL_SORT_NUM]?>"></td>

			<td class='tds2'>
				<input type='text' name='cl_price[]' value="<?=number_format($link_list[CL_PRICE])?>"  style='width:80px !important;' onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"> 
				<input type='text' name='cl_delivery[]' value="<?=number_format($link_list[CL_DELIVERY_PRICE])?>"  style='width:80px !important;' onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
				<input type='text' name='cl_memo[]' value="<?=$link_list[CL_MEMO]?>"  style='width:200px !important;'>
				<select name="cl_kind[]">
					<option value="" <? if( $link_list[CL_KIND] == "" ) echo "selected";?> >구분없음</option>
					<option value="qodp" <? if( $link_list[CL_KIND] == "qodp" ) echo "selected";?> >해외구매대행(빠른직구)</option>
					<option value="odp-jpy" <? if( $link_list[CL_KIND] == "odp-jpy" ) echo "selected";?> >해외직구(일본)</option>
					<option value="nobenefit" <? if( $link_list[CL_KIND] == "nobenefit" ) echo "selected";?> >무혜택</option>
				</select>
				<a href="<?=$link_list[CL_PATH]?>" target="_blank"><button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" ><i class="fas fa-external-link-alt"></i> 링크이동</button></a>
				<br>
				<textarea name='cl_path[]' style="margin-top:5px; height:60px; background-color:#f7f7f7; border:1px solid #999;"><?=$link_list[CL_PATH]?></textarea>
				<?
				if( $link_list[CL_KIND] == "odp-jpy" ){
					$ex_yen= 1060;
					$ex_yen2= 1030;
					$_show_jp_price = ($link_list[CL_PRICE] + $link_list[CL_DELIVERY_PRICE]) * ($ex_yen/100);
					$_show_jp_price_1 = ($link_list[CL_PRICE]) * ($ex_yen/100);
					$_show_jp_price_2 = ($link_list[CL_DELIVERY_PRICE]) * ($ex_yen/100);

					$_show_jp_price2 = ($link_list[CL_PRICE] + $link_list[CL_DELIVERY_PRICE]) * ($ex_yen2/100);
					$_show_jp_price2_1 = ($link_list[CL_PRICE]) * ($ex_yen2/100);
					$_show_jp_price2_2 = ($link_list[CL_DELIVERY_PRICE]) * ($ex_yen2/100);
				?>
				<br>
				환율 <?=number_format($ex_yen)?> : <b><?=number_format($_show_jp_price)?></b>원 / <?=number_format($_show_jp_price_1)?> +  <?=number_format($_show_jp_price_2)?><br>
				환율 <?=number_format($ex_yen2)?> : <b><?=number_format($_show_jp_price2)?></b>원  / <?=number_format($_show_jp_price2_1)?> +  <?=number_format($_show_jp_price2_2)?>
				<? } ?>
			</td>
			<td class='tds2' >
				<!-- <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="sitedataDel('<?=$link_list[CL_IDX]?>');"><i class="far fa-trash-alt"></i> </button> -->
				<label><input type="checkbox" name="cl_site_del[]" value="<?=$link_list[CL_IDX]?>"> 삭제</label>
			</td>
		</tr>
		<? } ?>
	</table>

	</form> 

</div>
<div class="text-center m-t-10">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-submit" onclick="goSave();" > 
		<i class="far fa-check-circle"></i> 수정
	</button>
</div>

<script type="text/javascript"> 
<!-- 
function goSave(){
	$("#form1").submit();
}

var siteCount = 0;
var site_Data = "";
<?
$st_result = wepix_query_error($site_query); 
while($st_list = wepix_fetch_array($st_result)) {
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

function priceMinBest(obj){
	if ($('input[type="checkbox"]', obj).prop('checked')) {
		$('input[type="checkbox"][name="price_min_best_checkbox"]').prop('checked', false);
		$('input[type="hidden"][name="price_min_best[]"]').attr('value', '');
		$('input[type="checkbox"]', obj).prop('checked', true);
		$('input[type="hidden"]', obj).val('ok');
	}
}
//--> 
</script> 