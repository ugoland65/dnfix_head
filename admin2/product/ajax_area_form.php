<?
$pageGroup = "product";
$pageName = "area_form";

include "../lib/inc_common.php";

	$_wmode = securityVal($wmode);
	$_idx = securityVal($idx);
	$_isocode = securityVal($isocode);


	if( $_wmode == "view" && $_idx ){
		$area_data = wepix_fetch_array(wepix_query_error("select * from "._DB_AREA." where AREA_IDX = '".$_idx."' "));

		$view_area_name = $area_data[AREA_NAME];
		$view_area_code = $area_data[AREA_CODE];
		$show_area_view = $area_data[AREA_VIEW];

		$_action_mode = "area_modify";
		$title_name = "등록지역 관리";
		$submit_btn_text = "수정";

		$nation_data = wepix_fetch_array(wepix_query_error("select  AREA_NAME, AREA_CODE from AREA where AREA_NATION_ISO = '".$area_data[AREA_NATION_ISO]."' and AREA_KIND = 'N' "));

	}else{

		$_action_mode = "area_new";
		$title_name = "신규지역 등록";
		$submit_btn_text = "등록";

		$nation_data = wepix_fetch_array(wepix_query_error("select AREA_NAME, AREA_CODE from AREA where AREA_NATION_ISO = '".$_isocode."' and AREA_KIND = 'N' "));
	}

	$view_nation_name = $nation_data[AREA_NAME];
	$view_nation_code = $nation_data[AREA_CODE];
?>

<div class="ajax-page-title"><?=$title_name?></div>
<div class="table-wrap">

	<form name='form1' action='<?=_A_PATH_PRODUCT_AREA_OK?>' method='post'>
	<input type='hidden' name='a_mode' value='<?=$_action_mode?>'>
	<input type='hidden' name='area_nation_iso' value='<?=$view_nation_code?>'>
	<input type='hidden' name='modify_key' value='<?=$area_data[AREA_IDX]?>'>

	<table cellspacing="1" cellpadding="0" class="table-style2 basic-form">
		<tr>
			<th>국가</th>
			<td><?=$view_nation_name?> (<?=$view_nation_code?>)</td>
		</tr>
		<tr>
			<th>지역명</th>
			<td><input type='text' name='area_name' id='area_name' style="width:200px" value="<?=$view_area_name?>"> </td>
		</tr>
		<tr>
			<th>지역코드</th>
			<td>
				<? if( $area_data[AREA_CODE] ){ ?>
					<?=$view_area_code?>
				<? }else{ ?>
					<input type='text' name='area_code' id='area_code' style="width:100px"  value="<?=$view_area_code?>">
				<? } ?>
			</td>
		</tr>
		<tr>
			<th>속성</th>
			<td>
				<label><input type="checkbox" name="area_view" value="N" <? if( $show_area_view == "N" ) echo "checked"; ?> > 감춤</label>
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

function goDel(idx){
	if(confirm('해당 지역을 삭제합니다\n정말 삭제하시겠습니까?')){
		$.ajax({
			type: "post",
			url : "<?=_A_PATH_PRODUCT_AREA_OK?>",
			data : { 
				a_mode : "area_del",
				Idx : idx
			},
			success: function(getdata) {
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					alert("삭제 되었습니다.");
					//location.reload();
					location.href='<?=_A_PATH_PRODUCT_AREA_LIST?>';
				}
			}
		});
	}
}
//--> 
</script> 

<?
exit;
?>