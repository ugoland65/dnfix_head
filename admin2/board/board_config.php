<?
include "../lib/inc_common.php";
	$pageGroup = "board";
	$pageName = "board_config";


	$_b_code = securityVal($b_code);

	$bo_c_where = "  ";
	$bo_c_query = "select BOARD_NAME, BOARD_CODE from "._DB_BOARD_CONFIG." ".$bo_c_where."order by UID desc ";
	$bo_c_result = wepix_query_error($bo_c_query);




include "../layout/header.php";
?>
<script type='text/javascript'>
	function goSave(){
		var board_code  = "<?php echo $_ary_board_code;?>";
		var board_name  = "<?php echo $_ary_board_name;?>";
		var a  = document.getElementById("board_code").value;
		alert(board_code[2]);


		var form = document.form1;
		//form.submit();
	}
</script>
<div id="contents_head">
	<h1>게시판 설정</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<form method='post' name='form1' action='<?=_A_PATH_BOARD_OK?>'>
		<table cellspacing="0" cellpadding="0" border="0" class="table-style2 treewrap">	
			<tr>
				<td class="treewrap-menu">

					<div class="tree-left-wrap">
						<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='<?=_A_PATH_BOARD_CONFIG?>'" style="width:100%; height:28px !important;" > 
							<i class="fas fa-plus-circle"></i> 신규 게시판 추가
						</button>
						<?
						while($bo_c_list = wepix_fetch_array($bo_c_result)){
							$_inst2_ul_class = ($bo_c_list[BOARD_CODE] == $_b_code) ? "tree-big-menu2 active" : "tree-big-menu2-closed";
						?>
							<ul id="cate_<?=$bo_c_list[MPS_IDX]?>" class="<?=$_inst2_ul_class?>" onclick="location.href='<?=_A_PATH_BOARD_MAIN?>?b_code=<?=$bo_c_list[BOARD_CODE]?>'"><?=$bo_c_list[BOARD_NAME]?></ul>
						<? } ?>
					</div>

				</td>
				<td class="treewrap-margin" style="border:none;"></td>
				<td class="treewrap-body">

					<div class="ajax-page-title">신규게시판 추가</div>
					<div class="table-wrap">

						<table cellspacing="1" cellpadding="0" class="table-style2 basic-form">
							<tr>
								<th>게시판 코드</th>
								<td><input type='text' name='board_code' id='board_code' style="width:200px"></td>
							</tr>
							<tr>
								<th>게시판 이름</th>
								<td><input type='text' name='board_name' id='board_name' style="width:200px"></td>
							</tr>
							<tr>
								<th>게시판 노출 이름</th>
								<td><input type='text' name='board_name_show' id='board_name_show' style="width:200px" ></td>
							</tr>
							<tr>
								<th>게시판 스킨</th>
								<td>
									<div>
										PC : <input type='text' name='board_skin' id='board_skin' style="width:200px" >
									</div>
									<div>
										모바일 : <input type='text' name='board_skin_mo' id='board_skin_mo' style="width:200px">
									</div>
								</td>
							</tr>
							<tr>
								<th>카테고리</th>
								<td>
									<div>
										<label><input type="radio" name="show_cate" value="N" checked> 비사용</label>
										<label><input type="radio" name="show_cate" value="Y" > 사용</label>
									</div>
									<div style="margin-top:3px;">
										<input type='text' name='board_cate' id='board_cate'>
									</div>
								</td>
							</tr>
							<tr>
								<th>지역</th>
								<td>
									<label><input type="radio" name="show_area" value="N" checked> 비사용</label>
									<label><input type="radio" name="show_area" value="Y" > 사용(선택)</label>
									<label><input type="radio" name="show_area" value="I" > 사용(필수)</label>
								</td>
							</tr>
							<tr>
								<th>평점</th>
								<td>
									<label><input type="radio" name="show_grade" value="N" checked> 비사용</label>
									<label><input type="radio" name="show_grade" value="Y" > 사용(선택)</label>
									<label><input type="radio" name="show_grade" value="I" > 사용(필수)</label>
								</td>
							</tr>
							<tr>
								<th>이미지 첨부</th>
								<td>
									<label><input type="radio" name="show_image" value="N" checked> 비사용</label>
									<label><input type="radio" name="show_image" value="Y" > 사용(선택)</label>
									<label><input type="radio" name="show_image" value="I" > 사용(필수)</label>
								</td>
							</tr>
							<tr>
								<th>1:1 게시판</th>
								<td>
									<label><input type="radio" name="show_mtm" value="N" checked> 비사용</label>
									<label><input type="radio" name="show_mtm" value="Y" > 사용</label>
								</td>
							</tr>
							<tr>
								<th>답변 </th>
								<td>
									<label><input type="radio" name="show_answer" value="N" checked> 비사용</label>
									<label><input type="radio" name="show_answer" value="Y" > 사용</label>
								</td>
							</tr>
							<tr>
								<th>읽음 확인 </th>
								<td>
									<label><input type="radio" name="show_view_check" value="N" checked> 비사용</label>
									<label><input type="radio" name="show_view_check" value="Y"> 사용</label>
								</td>
							</tr>
							<tr>
							 <td colspan='2'>
								<button type="button" id="" style='margin-left:40%;' class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="goSave();" > 
									<i class="far fa-check-circle"></i> 신규 게시물 추가
								</button>
								</td>
							</tr>
						</table>
					</div>


				</td>
			</tr>
		 
		</table>
		</form>


	</div>
</div>


<?
include "../layout/footer.php";
exit;
?>