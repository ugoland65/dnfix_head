<?
$pageGroup = "osi";
$pageName = "osi_summoner";

	include "../lib/inc_common.php";

	$_pn = securityVal($pn);

	$bo_where = "";
	$total_count = wepix_counter(_DB_OSI_SUMMONER, $bo_where);
		
	// 페이지당 목록수
	$list_num = 20;
	$page_num = 10;

	// 전체 페이지 계산
	$total_page = ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
	$counter = $total_count - (($_pn - 1) * $list_num);

	$bo_query = "select * from "._DB_OSI_SUMMONER." order by IDX DESC limit ".$from_record.", ".$list_num;
	$bo_result = wepix_query_error($bo_query);

	$paging_url = "?b_code=".$_b_code."&ct=".$_show_b_category."&s_active=".$_s_active."&s_kind=".$_s_kind."&s_text=".$_s_text."&pn=";
	$_view_paging = publicPaging($_pn, $total_page, $list_num, $page_num, $paging_url);

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
	.table-style{ width:100% !important; }
	.table-style td{ height:28px !important; }
	.bo-list-comment{ width:17px; height:15px; line-height:15px; text-align:center; font-size:9px; border:1px solid #e9eff5;  border-radius:3px; box-sizing:border-box; display:inline-block; background-color:#148eff; color:#ffffff !important; padding:0 !important; }
</STYLE>
<div id="contents_head">
	<h1>소환사 관리</h1>
	<div id="head_write_btn">
<!-- 
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_BOARD_REG?>?b_mode=new&b_code=<?=$_b_code?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
 -->
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn">
				<!-- <button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="doGroup('new','600','500');">선택회원 삭제</button> -->
			</ul>
		</div>

<STYLE TYPE="text/css">
.summoner-tier{ width:70px; }
.summoner-level{ width:50px; }
</STYLE>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">
					<table cellspacing="1px" cellpadding="0" border="0" class="table-list">	
						<tr>
							<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
							<th class="tl-idx">고유번호</th>
							<th class="summoner-tier">티어</th>
							<th class="tl-nick">소환사명</th>
							<th class="summoner-level">레벨</th>
							<th>내용</th>
							<th class="tl-date">업데이트</th>
							<th class="tl-hit">조회수</th>
						</tr>
<?
$_row_num = 0;
while($bo_list = wepix_fetch_array($bo_result)){

	$_view2_uid = $bo_list[IDX];
	$_view2_summoner_name = $bo_list[SUMMONER_NAME];
	$_view2_comment = nl2br($bo_list[EAT_EVALUATE]);
	$_view2_tier = nl2br($bo_list[TIER_IMG_NAME]);
	$_view2_update_date = date("Y-m-d H:i", $bo_list[UPDATE_DATE]);
	$_view2_hit = $bo_list[SUMMONER_HIT];
	$_view2_level = $bo_list[SUMMONER_LEVEL];

	$_row_num++;
	$trcolor = "#ffffff";
	if($_row_num%2 == 0){
		$trcolor = "#eee";
	}
?>
<tr align="center" id="trid_<?=$_view2_uid?>" bgcolor="<?=$trcolor?>">
	<td class="tl-check"><input type="checkbox" name="key_check[]"  id="bo_idx_<?=$_view2_uid?>" class="checkSelect" value="<?=$_view2_uid?>"></td>
	<td class="tl-idx"><?=$_view2_uid?></td>
	<td class="summoner-tier"><?=$_view2_tier?></td>
	<td class="tl-nick"><?=$_view2_summoner_name?></td>
	<td class="summoner-level"><?=$_view2_level?></td>
	<td></td>
	<td class="tl-date"><?=$_view2_update_date?></td>
	<td class="tl-hit"><?=$_view2_hit?></td>
</tr>
<? } ?>
					</table>
				</div>
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
				</div>
			</ul>
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>

	</div>
</div>

<?
include "../layout/footer.php";
exit;
?>