<?
$pageGroup = "product2";
$pageName = "maker_list";
include "../lib/inc_common.php";


	$_mode = securityVal($mode);
	$_idx = securityVal($key);

	if( $_mode == "modify" ){
		$maker_data = wepix_fetch_array(wepix_query_error("select * from "._DB_MAKER." where MD_IDX = '".$_idx."' "));

		$page_title_text = "제조사 수정";
		$submit_btn_text = "제조사 수정";
	}else{
		$page_title_text = "제조사 등록";
		$submit_btn_text = "제조사 등록";
	}


include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
</STYLE>
<script language=javascript>


function goSubmit(){
	var form = document.form1;
	form.submit();
}
</script>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
    <div id="head_write_btn">

	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
	 <div class="table-wrap">
	<form name='form1' action='<?=_A_PATH_PD_OK?>' method='post'>
			<? if( $_mode == "modify" ){ ?>
				<input type="hidden" name="a_mode" value="makerModify">
				<input type="hidden" name="idx" value="<?=$maker_data[MD_IDX]?>">
			<? }else{ ?>
				<input type="hidden" name="a_mode" value="makerNew">
			<? } ?>


				<table cellspacing="1" cellpadding="0" class="table-style">

			     <tr>
					 <th class="tds1">이름</th>
					 <td class="tds2"><input type='text' name='md_name' size='40' value="<?=$maker_data[MD_NAME]?>" ></td>
				 </tr>
				 <tr>
					 <th class="tds1">COODE</th>
					 <td class="tds2"><input type='text' name='md_code' size='4' value="<?=$maker_data[MD_CODE]?>"></td>
			     </tr>
				
			
					
				</table>
				
			</form>
			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_MAKER_LIST?>'" > 
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
		</div>
	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>