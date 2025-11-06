<?
$pageGroup = "product2";
$pageName = "structure_list";
include "../lib/inc_common.php";


	$_mode = securityVal($mode);
	$_idx = securityVal($key);

	if( $_mode == "modify" ){
		$st_data = wepix_fetch_array(wepix_query_error("select * from "._DB_TAG." where TG_IDX = '".$_idx."' "));

		$page_title_text = "구조 수정";
		$submit_btn_text = "구조 수정";
	}else{
		$page_title_text = "구조 등록";
		$submit_btn_text = "구조 등록";
	}
	
	$tag_max = wepix_fetch_array(wepix_query_error("select max(TG_HEADER) as tg_max from "._DB_TAG.""));
	$_show_header_count = $tag_max[tg_max];

	

	$tag_query = "select * from "._DB_TAG." where TG_ACTIVE = 'Y' order by TG_HEADER asc";
	$tag_result = wepix_query_error($tag_query); 
	
	while($tag_list = wepix_fetch_array($tag_result)){
		${"_ary_tag_".$tag_list[TG_HEADER]."_idx"}[] = $tag_list[TG_IDX];
		${"_ary_tag_".$tag_list[TG_HEADER]."_code"}[] = $tag_list[TG_NAME];
	}


include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
</STYLE>
<script type='text/javascript'>

//var header_count = "<?=$_show_header_count?>";
	
function changeCode(num){

	if(num == 1){
		$("#st_code").remove();
		$("#code_td").append("<input type='text' id='st_code_text' name='st_code'>");
	}else{
		if(num == 2){
			var option_idx = <?php echo json_encode($_ary_tag_1_idx)?>; 
			var option_code = <?php echo json_encode($_ary_tag_1_code)?>; 
		}else if(num == 3){
			var option_idx = <?php echo json_encode($_ary_tag_2_idx)?>; 
			var option_code = <?php echo json_encode($_ary_tag_2_code)?>; 
		}else if(num == 4){
			var option_idx = <?php echo json_encode($_ary_tag_3_idx)?>; 
			var option_code = <?php echo json_encode($_ary_tag_3_code)?>; 
		}

		var optionData ="";
		for(var i=0; i<option_idx.length; i++){
			optionData += "<option value='"+option_idx[i]+"'>"+option_code[i]+"</option>";
		}

		$("#st_code_text").remove();
		$("#st_code").remove();
		var showHtml =""
			+"<select name='st_code' id='st_code'>"
			+"<option value=''>Select Code</option>"
			+optionData
			+"</select>";
		$("#code_td").append(showHtml);
	}
	
}

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
				<input type="hidden" name="a_mode" value="structureModify">
				<input type="hidden" name="idx" value="<?=$st_data[TG_IDX]?>">
			<? }else{ ?>
				<input type="hidden" name="a_mode" value="structureNew">
			<? } ?>


				<table cellspacing="1" cellpadding="0" class="table-style">

			     <tr>
					 <th class="tds1">이름</th>
					 <td class="tds2"><input type='text' name='st_name' size='40' value="<?=$st_data[TG_NAME]?>"></td>
				 </tr>
				 <tr>
					 <th class="tds1">Depth</th>
					 <td class="tds2">
					<?
					if($st_data[TG_HEADER] != ''){
						$checked_num = $st_data[TG_HEADER];
						$selected_num = $st_data[TG_HEADER] -1;
					}else{
						$checked_num = 2;
						$selected_num = 3;
					}
					
					for($i=1;$i <= $_show_header_count+1; $i++){?>
						 <label><input type='radio'  name='st_haed' value='<?=$i?>' <?if($i == $checked_num){ echo "checked";}?> onclick="changeCode('<?=$i?>');"><?=$i?></label>
					<?}?>
					 </td>
			     </tr>
				 <tr>
					 <th class="tds1">CODE</th>
					 <td class="tds2" id='code_td'>
					 <select name='st_code' id='st_code'>
							<option value=''>Select Code</option>
						<?for($i=0;$i<count(${"_ary_tag_".$selected_num."_idx"});$i++){?>
							<option value='<?=${"_ary_tag_".$selected_num."_idx"}[$i]?>' <?if($st_data[TG_PARENT_IDX] ==${"_ary_tag_".$selected_num."_idx"}[$i]){ echo "selected";}?>><?=${"_ary_tag_".$selected_num."_code"}[$i]?></option>
						<?}?>
					 </select>
					
			     </tr>
				 <tr>
					<th>색상 (rgb)</th>
					<th><input type='text' name='st_rgb' value='<?=$st_data[TG_RGB]?>'></td>
				 </tr>
				 <tr>
					<th>비고</th>
					<th><input type='text' name='st_memo' value='<?=$st_data[TG_MEMO]?>'></td>
				 </tr>
				 <tr>
					<th class="tds1">Sort Num</th>
					<td class="tds2"><input type='text' name='st_sort_num' value='<?=$st_data[TG_SORT_NUM]?>'></td>
				 </tr>
				 <tr>
					 <th class="tds1">View</th>
					 <td class="tds2">
						 <label><input type='radio' name='st_active' value='Y' checked>Y</label>
						 <label><input type='radio'  name='st_active' value='N' >N</label>
					 </td>
			     </tr>
				
			
					
				</table>
				
			</form>
			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_STRUCTURE_LIST?>'" > 
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