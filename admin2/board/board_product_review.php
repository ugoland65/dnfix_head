<?
$pageGroup = "board";
$pageName = "board_product_review";

	include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	$_pn = securityVal($pn);

	$where = "";
	$total_count = wepix_counter("COMPARISON_COMMENT", $where);
		
	// 페이지당 목록수
	$list_num = 20;
	$page_num = 10;

	// 전체 페이지 계산
	$total_page = ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
	$counter = $total_count - (($_pn - 1) * $list_num);

	$query = "select * from COMPARISON_COMMENT order by COMMENT_IDX desc limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$paging_url = "board_product_review.php?pn=";
	$_view_paging = publicPaging($_pn, $total_page, $list_num, $page_num, $paging_url);

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
	.table-style{ width:100% !important; }
	.table-style td{ height:28px !important; }
	.bo-list-comment{ width:17px; height:15px; line-height:15px; text-align:center; font-size:9px; border:1px solid #e9eff5;  border-radius:3px; box-sizing:border-box; display:inline-block; background-color:#148eff; color:#ffffff !important; padding:0 !important; }
</STYLE>
<div id="contents_head">
	<h1>상품후기</h1>
	<div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_BOARD_REG?>?b_mode=new&b_code=<?=$_b_code?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total" style="width:300px;">
				<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">

					<table cellspacing="1px" cellpadding="0" border="0" class="table-list">	
						<tr>
							<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
							<th class="tl-idx">고유번호</th>
							<th class="tl-bo-mode">모드</th>
							<th class="tl-img-80">상품</th>
							<th class="tl-body">내용</th>
<?
if( $cf_all_glob_sys_domain_by_skin_active == "active" ){
?>
							<th class="tl-domain">도메인</th>
<? } ?>
							<th class="tl-witer">작성자</th>
							<th class="tl-date">작성일</th>
						</tr>
<?
$_row_num = 0;
while($list = wepix_fetch_array($result)){

	$_view2_mode = $_bo_gv_mode[$list[COMMENT_MODE]];

	$pd_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$list[PD_UID]."' "));
	$_rg_img_path = '../../data/comparion/'.$pd_data[CD_IMG];

	$comment_body = nl2br($list[COMMENT_BODY]);

	$_view_micon = "";
	$_view_board_name = $list[COMMENT_NAME];
	$_view_ip = $list[COMMENT_IP];
	$_view_ip_show = $list[COMMENT_IP_SHOW];

	$_view_domain = $list[DOMAIN];

	$board_date = date("Y-m-d H:i", $list[COMMENT_DATE]);
?>
<tr align="center" id="trid_<?=$list[COMMENT_IDX]?>" bgcolor="<?=$trcolor?>">
	<td class="tl-check"><input type="checkbox" name="key_check[]"  id="bo_idx_<?=$bo_list[UID]?>" class="checkSelect" value="<?=$list[COMMENT_IDX]?>"></td>
	<td class="tl-idx"><?=$list[COMMENT_IDX]?></td>
	<td class="tl-bo-mode"><?=$_view2_mode?></td>
	<td class="tl-img-80"><img src="<?=$_rg_img_path?>" alt=""></td>
	<td class="tl-body text-left">
		<p><b onclick="comparisonQuick('<?=$pd_data[CD_IDX]?>','comment');" style="cursor:pointer;"><?=$pd_data[CD_NAME]?></b></p>
		<p><?=$comment_body?></p>
	</td>
<?
if( $cf_all_glob_sys_domain_by_skin_active == "active" ){
?>
	<td class="tl-domain"><?=$_view_domain?></td>
<? } ?>
	<td class="tl-witer">
		<?=$_view_micon?><?=$_view_board_name?><br>
		<?=$bo_list[BOARD_WITER_ID]?>
		<?=$_view_ip?>
		<b><?=$_view_ip_show?></b>
	</td>
	<td class="tl-date"><?=$board_date?></td>
</tr>
<? } ?>
					</table>

				</div><!-- #list_box2 -->
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300">
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