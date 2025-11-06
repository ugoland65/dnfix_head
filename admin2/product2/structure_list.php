<?
$pageGroup = "product2";
$pageName = "structure_list";

include "../lib/inc_common.php";

	$_tg_code = securityVal($tg_code);

	if(!$_tg_code) $_tg_code = "ONAHOLE";

	$_serch_query = " ";

	$total_count = wepix_counter(_DB_TAG, $_serch_query);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	//$query = "select * from "._DB_TAG." ".$_serch_query." order by TG_CODE asc limit ".$from_record.", ".$list_num;
	//$result = wepix_query_error($query);

	$query = "select * from "._DB_TAG." where TG_ACTIVE = 'Y' and TG_CODE = '".$_tg_code."' order by TG_HEADER,TG_SORT_NUM asc";
	//$query = "select * from "._DB_TAG." where TG_ACTIVE = 'Y' order by TG_HEADER,TG_SORT_NUM asc";
	$result = wepix_query_error($query); 
	
	while($list = wepix_fetch_array($result)){
		if($list[TG_HEADER] == '1'){
			$_header_1_name = $list[TG_NAME];
			$_header_1_code = $list[TG_CODE];
		}elseif($list[TG_HEADER] == '2'){
			$_ary_two_depth_idx[] = $list[TG_IDX];
			${"_ary_depth_".$list[TG_IDX]."_idx"}[] = $list[TG_IDX];
			${"_ary_depth_".$list[TG_IDX]."_name"}[] = $list[TG_NAME];
			${"_ary_depth_".$list[TG_IDX]."_sort"}[] = $list[TG_SORT_NUM];
			${"_ary_depth_".$list[TG_IDX]."_active"}[] = $list[TG_ACTIVE];
			${"_ary_depth_".$list[TG_IDX]."_header"}[] = $list[TG_HEADER];
			${"_ary_depth_".$list[TG_IDX]."_memo"}[] = $list[TG_MEMO];
			${"_ary_depth_".$list[TG_IDX]."_rbg"}[] = $list[TG_RGB];
		}elseif($list[TG_HEADER] == '3'){	
			${"_ary_depth_".$list[TG_PARENT_IDX]."_idx"}[] = $list[TG_IDX];
			${"_ary_depth_".$list[TG_PARENT_IDX]."_name"}[] = $list[TG_NAME];
			${"_ary_depth_".$list[TG_PARENT_IDX]."_sort"}[] = $list[TG_SORT_NUM];
			${"_ary_depth_".$list[TG_PARENT_IDX]."_active"}[] = $list[TG_ACTIVE];
			${"_ary_depth_".$list[TG_PARENT_IDX]."_header"}[] = $list[TG_HEADER];
			${"_ary_depth_".$list[TG_PARENT_IDX]."_memo"}[] = $list[TG_MEMO];
			${"_ary_depth_".$list[TG_PARENT_IDX]."_rbg"}[] = $list[TG_RGB];
			
		}
	}

	$page_title_text = "구조 등록";
	$submit_btn_text = "구조 등록";
	
	$tag_max = wepix_fetch_array(wepix_query_error("select max(TG_HEADER) as tg_max from "._DB_TAG.""));
	$_show_header_count = $tag_max[tg_max];

	

	$tag_query = "select * from "._DB_TAG." where TG_ACTIVE = 'Y' and TG_CODE = '".$_tg_code."' order by TG_HEADER asc";
	$tag_result = wepix_query_error($tag_query); 
	
	while($tag_list = wepix_fetch_array($tag_result)){
		${"_ary_tag_".$tag_list[TG_HEADER]."_idx"}[] = $tag_list[TG_IDX];
		${"_ary_tag_".$tag_list[TG_HEADER]."_code"}[] = $tag_list[TG_NAME];
	}


	$page_link_text = _A_PATH_MAKER_LIST."?pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.ag-kind{ width:50px !important; }
.ag-name{ width:200px !important; }
.ag-sub-count{ width:40px !important; }
.ag-sub-view{ width:50px !important; }
</STYLE>
<script type='text/javascript'>

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


function goDel(idx){
	$.ajax({
		type: "post",
		url : "<?=_A_PATH_PD_OK?>",
		data : { 
			a_mode : "structureDel",
			idx : idx
		},
		success: function(getdata) {
			makedata = getdata.split('|');
			ckcode = makedata[1];
			if(ckcode=="Processing_Complete"){
				alert('삭제완료');
				location.reload();
			}
		}
	});
}


function changeSort(){
	var form = document.sortFrom;
	form.submit();
}

function goSubmit(){
	var form = document.regFrom;
	form.submit();
}
</script>
<div id="contents_head">
	<h1>제조사 목록</h1>
    <div id="head_write_btn">
		
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total : <b><?=$total_count?></b></span>
			</ul>
			<ul class="list-top-btn"></ul>
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" style="width:150px; margin:0px 0px 5px 25%;" onclick="changeSort()"><i class="fas fa-sort"></i> 전체 순서변경</button>
		</div>
		<div class="table-wrap">
		<form name='sortFrom' action='<?=_A_PATH_PD_OK?>' method='post' style= 'margin-left:15px;'>
			<input type="hidden" name="a_mode" value="changeSortStructure">
			
			<table cellspacing="1px" cellpadding="0" border="0" class="table-style" style='float:left; margin-right:15px;'>	
					<tr>
						<th class="tds1 list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
						<th class="tds1" style='width:50px;'>고유번호</th>
						<th class="tds1">1</th>
						<th class="tds1" style='width:120px;'>2</th>
						<th class="tds1" style='width:260px;'>3</th>
						<th class="tds1" style='width:100px;'>관리</th>
					</tr>
					<tr>
						<td class="tds2 list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list[TG_IDX]?>" ></td>
						<td class="tds2"></td>
						<td class="tds2"><?=$_header_1_name?> (<?=$_header_1_code?>)</td>
						<td class="tds2"></td>
						<td class="tds2"></td>
						<td class="tds2"></td>
					</tr>
				<?
				for($a=0;$a<count($_ary_two_depth_idx);$a++){
				?>
					<?
					for($i=0;$i<count(${"_ary_depth_".$_ary_two_depth_idx[$a]."_idx"});$i++){
						$_view_tag_idx = ${"_ary_depth_".$_ary_two_depth_idx[$a]."_idx"}[$i];
						$_view_tag_name = ${"_ary_depth_".$_ary_two_depth_idx[$a]."_name"}[$i];
						$_view_tag_sort = ${"_ary_depth_".$_ary_two_depth_idx[$a]."_sort"}[$i];
						$_view_tag_active = ${"_ary_depth_".$_ary_two_depth_idx[$a]."_active"}[$i];
						$_view_tag_header = ${"_ary_depth_".$_ary_two_depth_idx[$a]."_header"}[$i];
						$_view_tag_memo = ${"_ary_depth_".$_ary_two_depth_idx[$a]."_memo"}[$i];
						$_view_tag_rbg = ${"_ary_depth_".$_ary_two_depth_idx[$a]."_rbg"}[$i];
					?>
					<tr>
						<td class="tds2 list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$_view_tag_idx?>" ></td>
						<td class="tds2"><?=$_view_tag_idx?> </td>
						<td class="tds2"></td>
						<input type='hidden' name='tg_idx[]' value='<?=$_view_tag_idx?>'>
						<td class="tds2"> 
						<?if($_view_tag_header == '2'){?>
						<input type='text' name='tg_sort[]' style='width:20px;' value='<?=$_view_tag_sort?>'> - 
						<input type='text' name='tg_name[]' style='width:70px;' value='<?=$_view_tag_name?>'> 
						<?}?></td>
						<td class="tds2"> 
						<?if($_view_tag_header == '3'){?>
						<input type='text' name='tg_sort[]' style='width:30px;' value='<?=$_view_tag_sort?>'> - 
						<input type='text' name='tg_name[]' style='width:110px;' value='<?=$_view_tag_name?>'>
						<?if($_view_tag_rbg){?>- <input type='text' name='tg_rbg[]' style='width:70px;' value='<?=$_view_tag_rbg?>'> <?}?>
						<?}?></td>
						<td class="tds2">
						<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="location.href='<?=_A_PATH_STRUCTURE_REG?>?mode=modify&key=<?=$_view_tag_idx?>'">Modify</button>
						<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="goDel('<?=$_view_tag_idx?>');"><i class="far fa-trash-alt"></i></button>
					</tr>
					 <?}?>
				<?}?>
				<?/*
				while($list = wepix_fetch_array($result)){

				
				?>
					<tr>
						<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list[TG_IDX]?>" ></td>
						<td class="tds2"><?=$list[TG_IDX]?></td>
						<td class="tds2"><?=$list[TG_CODE]?></td>
						<td class="tds2" style="text-align:left !important;"><B><?=$list[TG_NAME]?><B/></td>
						<td class="tds2">
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="location.href='<?=_A_PATH_STRUCTURE_REG?>?mode=modify&key=<?=$list[TG_IDX]?>'">Modify</button>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="goDel('<?=$list[TG_IDX]?>');"><i class="far fa-trash-alt"></i> DEL</button>
						</td>
					</tr>
				<? } */?>
			</table>
			</form>

			<table cellspacing="1" cellpadding="0" class="table-style">
<?
	$query = "select * from "._DB_TAG." where TG_ACTIVE = 'Y' and TG_HEADER ='1' and TG_ACTIVE = 'Y' order by TG_HEADER,TG_SORT_NUM asc";
	$result = wepix_query_error($query); 
	while($list = wepix_fetch_array($result)){
?>
			     <tr>
					 <td><a href="structure_list.php?tg_code=<?=$list[TG_CODE]?>"><?=$list[TG_NAME]?> <?=$list[TG_CODE]?></a></td>
				 </tr>

<? } ?>
			</table>

			<form name='regFrom' action='<?=_A_PATH_PD_OK?>' method='post' style= 'margin-left:15px;'>
				<input type="hidden" name="a_mode" value="structureNew">
				<input type="hidden" name="tg_code" value="<?=$_tg_code?>">

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
						$checked_num = 3;
						$selected_num =2;
					}
					
					for($i=1;$i <= $_show_header_count+2; $i++){?>
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
								<option value='<?=${"_ary_tag_".$selected_num."_idx"}[$i]?>' <?if($st_data[TG_PARENT_IDX] ==${"_ary_tag_".$selected_num."_idx"}[$i]){ echo "selected";}?>>( <?=${"_ary_tag_".$selected_num."_idx"}[$i]?> ) <?=${"_ary_tag_".$selected_num."_code"}[$i]?></option>
							<?}?>
						</select>

			     </tr>
				 <tr>
					<th class="tds1">색상 (rgb)</th>
					<td  class="tds2"><input type='text' name='st_rgb'  value='<?=$st_data[TG_RGB]?>'></td>
				 </tr>
				 <tr>
					<th class="tds1">비고</th>
					<td class="tds2"><input type='text' name='st_memo' value='<?=$st_data[TG_MEMO]?>'></td>
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
				 <tr>
					 <th class="tds1" colspan='2'>
					 <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="goSubmit();" > 
						<i class="far fa-check-circle"></i>
						<?=$submit_btn_text?>
					</button></th>
			     </tr>
				</table>
				
			</form>
			
		</div>
		<div class="footer-padding"></div>
	</div>
</div>
<script type="text/javascript"> 
<!-- 

//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>