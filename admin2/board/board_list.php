<?
$pageGroup = "board";
$pageName = "board_list";

	include "../lib/inc_common.php";
	include 'board_inc.php';

	$_pn = securityVal($pn);
	$_s_active = securityVal($s_active);
	$_s_text = securityVal($s_text);
	$_s_kind = securityVal($s_kind);

	//$bo_where = " WHERE UID > 0 ";
	$bo_where = " WHERE BOARD_MODE != 'NT' ";

	//검색이 있을경우
	if( $_s_active == "on" AND $_s_text != "" ){
		if( $_s_kind == "subject_body" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$bo_where .= " AND INSTR(LOWER(BOARD_SUBJECT), LOWER('".$_s_text."')) OR INSTR(LOWER(BOARD_BODY), LOWER('".$_s_text."')) ";
			}else{
				$bo_where .= " AND INSTR(LOWER(BOARD_SUBJECT), '".$_s_text."') OR INSTR(LOWER(BOARD_BODY), LOWER('".$_s_text."')) ";
			}
		}elseif( $_s_kind == "subject" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$bo_where .= " AND INSTR(LOWER(BOARD_SUBJECT), LOWER('".$_s_text."')) ";
			}else{
				$bo_where .= " AND INSTR(LOWER(BOARD_SUBJECT), '".$_s_text."') ";
			}
		}elseif( $_s_kind == "writer_name" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$bo_where .= " AND INSTR(LOWER(BOARD_WITER_NAME), LOWER('".$_s_text."')) ";
			}else{
				$bo_where .= " AND INSTR(LOWER(BOARD_WITER_NAME), '".$_s_text."') ";
			}
		}elseif( $_s_kind == "product_name" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$bo_where .= " AND INSTR(LOWER(BOARD_PD_NAME), LOWER('".$_s_text."')) ";
			}else{
				$bo_where .= " AND INSTR(LOWER(BOARD_PD_NAME), '".$_s_text."') ";
			}
		}
	}


	$total_count = wepix_counter(_DB_BOARD, $bo_where);
		
	// 페이지당 목록수
	$list_num = 20;
	$page_num = 10;

	// 전체 페이지 계산
	$total_page = ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
	$counter = $total_count - (($_pn - 1) * $list_num);

	$bo_query = "select * from "._DB_BOARD." ".$bo_where." order by UID DESC limit ".$from_record.", ".$list_num;
	$bo_result = wepix_query_error($bo_query);

	//공지사항 쿼리
	$bo_query_notice = "select * from "._DB_BOARD." WHERE BOARD_MODE = 'NT' order by BOARD_ADMIN = 'Y' desc, HEADNUM asc ";
	$bo_result_notice = wepix_query_error($bo_query_notice);

	$paging_url = "board_list.php?b_code=".$_b_code."&ct=".$_show_b_category."&s_active=".$_s_active."&s_kind=".$_s_kind."&s_text=".$_s_text."&pn=";
	$_view_paging = publicPaging($_pn, $total_page, $list_num, $page_num, $paging_url);

include "../layout/header.php";
?>


<STYLE TYPE="text/css">
	.table-style{ width:100% !important; }
	.table-style td{ height:28px !important; }
	.bo-list-comment{ width:17px; height:15px; line-height:15px; text-align:center; font-size:9px; border:1px solid #e9eff5;  border-radius:3px; box-sizing:border-box; display:inline-block; background-color:#148eff; color:#ffffff !important; padding:0 !important; }
</STYLE>

<script type="text/javascript"> 
<!-- 
function listDel(){

	var send_array2 = "";
	$(".checkSelect:checked").each(function(index){
		if(index!=0){ send_array2 += ","; }
		send_array2 += $(this).val();
	});

	if(send_array2 == ''){
		alert('삭제할 게시물을 선택해주세요.');
	}else{

		$('#modal_alert_title').html('<i class="fas fa-exclamation-circle"></i> 경고');
		$('#modal_alert_msg').html(
			"해당 게시물을 삭제합니다<br>"
			+ "삭제후 데이터는 복구 되지 않습니다.<br>"
			+ "댓글이 있을경우 댓글도 삭제됩니다.<br>"
			+ "<br>정말 삭제하시겠습니까?'"
		);
		$("#modal_footer_btn_wrap").addClass('display-none');
		$("#modal_footer_confirm_btn_wrap").removeClass('display-none');
		$('#modal-alert').modal({show: true,backdrop:'static'});

		$("#confirm-ok").on("click", function(){
			$('#a_mode').val("boardBatchDel");
			$('#idxArrayText').val(send_array2);
			$("#board_action").submit();
		});

	}
}
//--> 
</script> 


<div id="contents_head">
	<h1>게시판 목록 (<?=$_view_bc_name?>)</h1>
	<div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_BOARD_REG?>?b_mode=new&b_code=<?=$_b_code?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>

		<?if($_b_code == 'CREVIEW'){?>
			<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='/admin2/board/reg_test.php?b_mode=new&b_code=<?=$_b_code?>'" > 
			 <i class="fas fa-plus-circle"></i>
			 test등록
			</button>
		<?}?>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total" style="width:300px;">
				<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn">
				<!-- <button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="doGroup('new','600','500');">선택회원 삭제</button> -->
			</ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">
					<table cellspacing="1px" cellpadding="0" border="0" class="table-list">	
						<tr>
							<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
							<th class="tl-idx">고유번호</th>
							<th class="tl-bo-mode">모드</th>
<?
//카테고리 사용시
if( $_show_bc_category_active == "Y" ){
?>
							<th width="100px">분류</th>
<? } ?>
							<th>제목</th>
							<th class="tl-witer">작성자</th>
							<th class="tl-date">작성일</th>
							<th class="tl-hit">조회수</th>

<?
//추천기능 사용시
if( $_show_bc_recom_active == "Y" ){
?>

							<th class="tl-recom">추천</th>
<? } ?>
<?
//평점 기능 사용시
if( $_show_bc_grade_active == "Y" ){
?>
							<th class="tl-grade">평점</th>
<? } ?>
						</tr>

<!-- -----------------| 공지사항 |----------------- -->
<?
while($bo_list = wepix_fetch_array($bo_result_notice)){
	$board_date = date("Y-m-d H:i", $bo_list[BOARD_DATE]);

	$_view2_category = $_ary_bc_category[$bo_list[BOARD_CATEGORY]];
	$_view2_thumbnail = ( $bo_list[BOARD_THUMBNAIL] ) ? "<span class='bo-list-img'><i class='far fa-image'></i></span>" : "";
	$_view2_hit = $bo_list[BOARD_HIT];
	$_view2_mode = $_bo_gv_mode[$bo_list[BOARD_MODE]];
	$_view_comment = ( $bo_list[BOARD_COMMENT] > 0 ) ? "<span class='bo-list-comment'>".$bo_list[BOARD_COMMENT]."</span>" : "";
	//$_view_admin_icon = ( $bo_list[BOARD_ADMIN] == "Y" ) ? "<span class='bo-list-admin-icon'><i class='fas fa-crown'></i></span>" : "";
	$_view2_grade = $bo_list[BOARD_GRADE];
	$_view_like = $bo_list[BOARD_LIKE];
	$_view_bad = $bo_list[BOARD_BAD];

	if( $bo_list[BOARD_ADMIN] == "Y" ){
		$_view_micon = "<span class='bo-list-admin-icon'><i class='fas fa-crown'></i></span>";
		$_view_board_name = "<b>".$bo_list[BOARD_WITER_NAME]."</b>";
	}elseif( $bo_list[BOARD_ADMIN] == "M2" ){
		$_view_micon = "<span class='bo-list-admin-icon'><i class='fas fa-user-astronaut'></i></span>";
		$_view_board_name = "<b>".$bo_list[BOARD_WITER_NAME]."</b>";
	}else{
		$_view_micon = "";
		$_view_board_name = $bo_list[BOARD_WITER_NAME];
	}

	$trcolor = "#ffe7f1";

?>
<tr align="center" id="trid_<?=$bo_list[UID]?>" bgcolor="<?=$trcolor?>">
	<td class="tl-check"><input type="checkbox" name="key_check[]"  id="bo_idx_<?=$bo_list[UID]?>" class="checkSelect" value="<?=$bo_list[UID]?>" ></td>
	<td class="tl-idx"><?=$bo_list[UID]?></td>
	<td class="tl-bo-mode"><b><?=$_view2_mode?></b></td>
<?
if( $_show_bc_category_active == "Y" ){
?>
	<td><?=$_view2_category?></td>
<? } ?>
	<td class="tl-bo-subject text-left">
		<? if($bo_list[BOARD_PD_IDX]){ ?>
			<span class="tl-bo-pdname"><? if($bo_list[BOARD_PD_NAME]){ echo $bo_list[BOARD_PD_NAME]."<br>"; }else{ echo "상품명 등록안됨 IDX (".$bo_list[BOARD_PD_IDX].")<br>"; } ?></span>
		<? } ?>
		<b><a href="<?=_A_PATH_BOARD_VIEW?>?b_code=<?=$_b_code?>&b_key=<?=$bo_list[UID]?>&returnUrl=<?=$check_request_uri_urlencode?>"><?=$bo_list[BOARD_SUBJECT]?></a></b>
		<?=$_view_comment?>
		<?=$_view2_thumbnail?>
	</td>
	<td class="tl-witer">
		<?=$_view_micon?><?=$_view_board_name?><br>
		<?=$bo_list[BOARD_WITER_ID]?>
	</td>
	<td class="tl-date"><?=$board_date?></td>
	<td class="tl-hit"><?=$_view2_hit?></td>
<?
//추천기능 사용시
if( $_show_bc_recom_active == "Y" ){
?>
	<td class="tl-recom"><?=$_view_like?> / <?=$_view_bad?></td>
<? } ?>

<?
//평점 기능 사용시
if( $_show_bc_grade_active == "Y" ){
?>
	<td class="tl-grade"><?=$_view2_grade?></td>
<? } ?>
	
</tr>
<? } ?>
<!-- -----------------| 공지사항 |----------------- -->

<?
$_row_num = 0;
while($bo_list = wepix_fetch_array($bo_result)){
	$board_date = date("Y-m-d H:i", $bo_list[BOARD_DATE]);

	$_view2_category = $_ary_bc_category[$bo_list[BOARD_CATEGORY]];
	$_view2_thumbnail = ( $bo_list[BOARD_THUMBNAIL] ) ? "<span class='bo-list-img'><i class='far fa-image'></i></span>" : "";
	$_view2_hit = $bo_list[BOARD_HIT];
	$_view2_mode = $_bo_gv_mode[$bo_list[BOARD_MODE]];
	$_view_comment = ( $bo_list[BOARD_COMMENT] > 0 ) ? "<span class='bo-list-comment'>".$bo_list[BOARD_COMMENT]."</span>" : "";
	$_view_like = $bo_list[BOARD_LIKE];
	$_view_bad = $bo_list[BOARD_BAD];
	//$_view_admin_icon = ( $bo_list[BOARD_ADMIN] == "Y" ) ? "<span class='bo-list-admin-icon'><i class='fas fa-crown'></i></span>" : "";

	if( $bo_list[BOARD_ADMIN] == "Y" ){
		$_view_micon = "<span class='bo-list-admin-icon'><i class='fas fa-crown'></i></span>";
		$_view_board_name = "<b>".$bo_list[BOARD_WITER_NAME]."</b>";
	}elseif( $bo_list[BOARD_ADMIN] == "M2" ){
		$_view_micon = "<span class='bo-list-admin-icon'><i class='fas fa-user-astronaut'></i></span>";
		$_view_board_name = "<b>".$bo_list[BOARD_WITER_NAME]."</b>";
	}else{
		$_view_micon = "";
		$_view_board_name = $bo_list[BOARD_WITER_NAME];
	}

	$_view2_grade = $bo_list[BOARD_GRADE];
	$_row_num++;
	$trcolor = "#ffffff";
	if($_row_num%2 == 0){
		$trcolor = "#eee";
	}

	if( $bo_list[BOARD_IP_SHOW] ){
		$_view_ip = "(".$bo_list[BOARD_IP_SHOW].")";
	}else{
		$_view_ip = "";
	}
?>
<tr align="center" id="trid_<?=$bo_list[UID]?>" bgcolor="<?=$trcolor?>">
	<td class="tl-check"><input type="checkbox" name="key_check[]"  id="bo_idx_<?=$bo_list[UID]?>" class="checkSelect" value="<?=$bo_list[UID]?>"></td>
	<td class="tl-idx"><?=$bo_list[UID]?></td>
	<td class="tl-bo-mode"><?=$_view2_mode?></td>
<?
if( $_show_bc_category_active == "Y" ){
?>
	<td><?=$_view2_category?></td>
<? } ?>
	<td class="tl-bo-subject text-left">
		<? if($bo_list[BOARD_PD_IDX]){ ?>
			<span class="tl-bo-pdname"><? if($bo_list[BOARD_PD_NAME]){ echo $bo_list[BOARD_PD_NAME]."<br>"; }else{ echo "상품명 등록안됨 IDX (".$bo_list[BOARD_PD_IDX].")<br>"; } ?></span>
		<? } ?>
		<b><a href="<?=_A_PATH_BOARD_VIEW?>?b_code=<?=$_b_code?>&b_key=<?=$bo_list[UID]?>&returnUrl=<?=$check_request_uri_urlencode?>"><?=$bo_list[BOARD_SUBJECT]?></a></b>
		<?=$_view_comment?>
		<?=$_view2_thumbnail?>
	</td>
	<td class="tl-witer">
		<?=$_view_micon?><?=$_view_board_name?><br>
		<?=$bo_list[BOARD_WITER_ID]?>
		<?=$_view_ip?>
	</td>
	<td class="tl-date"><?=$board_date?></td>
	<td class="tl-hit"><?=$_view2_hit?></td>
<?
//추천기능 사용시
if( $_show_bc_recom_active == "Y" ){
?>
	<td class="tl-recom"><?=$_view_like?> / <?=$_view_bad?></td>
<? } ?>
<?
//평점 기능 사용시
if( $_show_bc_grade_active == "Y" ){
?>
	<td class="tl-grade"><?=$_view2_grade?></td>
<? } ?>
	
</tr>
<? } ?>


					</table>
				</div>
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">

				<div id="list_box_layout2_filter_wrap">
					<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
					<form name='search_form' id='search_form' method='post' action="board_list.php">
					<input type="hidden" name="b_code" value="<?=$_b_code?>">
					<input type="hidden" name="s_active" value="on">
					<ul class="filter-from-ui m-t-5">
						<select name="s_kind">
							<option value="" >검색 조건</option>
							<option value="subject_body" <? if( $_s_kind == "subject_body" ) echo "selected"; ?>>제목+내용</option>
							<option value="subject" <? if( $_s_kind == "subject" ) echo "selected"; ?>>제목</option>
							<option value="writer_name" <? if( $_s_kind == "writer_name" ) echo "selected"; ?>>글쓴이</option>
<?
//상품 기능 사용시
if( $_show_bc_product_active == "Y" ){
?>
							<option value="product_name" <? if( $_s_kind == "product_name" ) echo "selected"; ?>>상품명</option>
<? } ?>
						</select>
					</ul>
					<ul class="filter-from-ui m-t-5">
						<input type='text' name='s_text' id='s_text' size='20' value="<?=$_s_text?>" placeholder="검색어">
					</ul>
					</form>
					<ul class="filter-from-ui m-t-5">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="goSerch();" > 
							<i class="fas fa-search"></i> 검색
						</button>
					</ul>

					<ul class="filter-menu-title" style="margin-top:20px;"><i class="fas fa-wrench"></i> Action</ul>
					<ul class="filter-from-ui m-t-5">
						<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="listDel()" > <i class="fas fa-plus-circle"></i> 선택삭제</button>
					</ul>
				</div>

			</ul>
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>

	</div>
</div>

<form name='board_action' id='board_action' action="<?=_A_PATH_BOARD_OK_NEW?>" method="post" enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="a_mode" id="a_mode">
<input type="hidden" name="idxArrayText" id="idxArrayText">
<input type="hidden" name="b_code" id="b_code" value="<?=$_b_code?>">
<input type="hidden" name="returnUrl" id="returnUrl" value="<?=$check_request_uri?>">

</form>

<script type="text/javascript"> 
<!-- 
function goSerch(){
	$("#search_form").submit();
}
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>