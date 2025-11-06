<?
$pageGroup = "booking";
$pageName = "plan_template_list";

include "../lib/inc_common.php";


	$total_count = wepix_counter(_DB_TRAVEL_PLAN_TEMPLATE,"");
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$tp_query = "select * from "._DB_TRAVEL_PLAN_TEMPLATE." order by TP_IDX desc limit ".$from_record.", ".$list_num;
	$tp_result = wepix_query_error($tp_query);

	$page_link_text = _A_PATH_PLAN_TEMPLATE_LIST."?pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>확정서 샘플 목록</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<form name='search' method='post' action="<?=_A_PATH_PLAN_TEMPLATE_LIST?>">
		<div class="search-wrap">
			<ul class="td search-img"></ul>
			<ul class="td search-body">
			</ul>
            <ul class="td search-button">
				<input type="submit" value="Searching">
			</ul>
		</div>
		</form>

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total : <b><?=$total_count?></b></span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list-box">
			<div class="table-wrap">
				<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
					<tr>
						<th width="25px"><input type="checkbox" name="" onclick="select_all()"></th>
						 <th width="10%">고유 번호</th>
						 <th width="*%">확정서</th>
						 <th width="15%">등록자</th>
						 <th width="15%">등록날짜</th>
						<th width="60px">관리 </th>
					</tr>
					<?
					while($tp_list = wepix_fetch_array($tp_result)){
						$_view2_reg_date = date("y-m-d", $tp_list[TP_REG_DATE]);
						
					?>
					<tr align="center" id="trid_<?=$tp_list[TP_IDX]?>" bgcolor="<?=$trcolor?>">
						<td><input type="checkbox" name="key_check[]" value="<?=$tp_list[TP_IDX]?>" ></td>	
						<td><?= $tp_list[TP_IDX]?></td>
						<td><?= $tp_list[TP_TITLE]?></td>
						<td><?= $tp_list[TP_REG_ID]?></td>
						<td><?= $_view2_reg_date?></td>
						<td>
						<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='<?=_A_PATH_PLAN_TEMPLATE_REG?>?mode=modify&key=<?=$tp_list[TP_IDX]?>'"> 수정 </button>
						</td>
					<tr>
					<? }?>

				</table>
			</div>
		</div><!-- #list-box -->
		<div class="paging-wrap"><?=$view_paging?></div>

	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>