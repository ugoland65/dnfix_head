<?
$pageGroup = "product";
$pageName = "category_form";

include "../lib/inc_common.php";

	$_wmode = securityVal($wmode);
	$_idx = securityVal($idx);

	if( $_wmode == "view" && $_idx ){
		$pd_ct_data = wepix_fetch_array(wepix_query_error("select * from "._DB_PRODUCT_CATAGORY_TRAVEL." where PDC_IDX = '".$_idx."' "));
		$_view_pdc_id = $pd_ct_data[PDC_ID];
		$_view_pdc_name = $pd_ct_data[PDC_NAME];
		$_view_pdc_skin = $pd_ct_data[PDC_SKIN];
		$_view_pdc_skin_mo = $pd_ct_data[PDC_SKIN_MO];

		$title_name = "분류 관리";
		$submit_btn_text = "수정";
		$_action_mode = "category_modify";
	}else{
		$title_name = "분류 관리";
		$submit_btn_text = "등록";
		$_action_mode = "category_new";
	}
?>
<div class="ajax-page-title"><?=$title_name?></div>
<div class="table-wrap">

	<form name='form1' action='<?=_A_PATH_PD_OK?>' method='post'>
	<input type='hidden' name='a_mode' value='<?=$_action_mode?>'>
	<input type='hidden' name='modify_key' value='<?=$pd_ct_data[PDC_IDX]?>'>

	<table cellspacing="1" cellpadding="0" class="table-style2 basic-form">
		<tr>
			<th>분류 코드</th>
			<td><?=$_view_pdc_id?></td>
		</tr>
		<tr>
			<th>분류 이름</th>
			<td><input type='text' name='pdc_name' id='pdc_name' style="width:200px" value="<?=$_view_pdc_name?>"></td>
		</tr>
		<tr>
			<th>카테고리 스킨</th>
			<td>
				<div>
					PC : <input type='text' name='pdc_skin' id='pdc_skin' style="width:200px" value="<?=$_view_pdc_skin?>">
				</div>
				<div>
					모바일 : <input type='text' name='pdc_skin_mo' id='pdc_skin_mo' style="width:200px" value="<?=$_view_pdc_skin_mo?>">
				</div>
			</td>
		</tr>
	</table>

	</form>

</div>
<div class="submitBtnWrap">

	<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-lg" onclick="goSave();" > 
		<i class="far fa-check-circle"></i>
		<?=$submit_btn_text?>
	</button>

<? if( $area_data[AREA_IDX] ){ ?>
	<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="goDel('<?=$area_data[AREA_IDX]?>');" > 
		<i class="fas fa-trash-alt"></i>
		삭제
	</button>
<? } ?>

</div>
<script type="text/javascript"> 
<!-- 
function goSave(){
	var form = document.form1;
	form.submit();
}

//--> 
</script> 
<?
exit;
?>