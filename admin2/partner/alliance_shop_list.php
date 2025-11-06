<?
$pageGroup = "partner";
$pageName = "partner_shop_list";

include "../lib/inc_common.php";

	$alliance_where = " ";

	$total_count = wepix_counter(_DB_ALLIANCE_SHOP, $alliance_where);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$alliance_query = "select * from "._DB_ALLIANCE_SHOP." ".$alliance_where." order by AS_NAME asc limit ".$from_record.", ".$list_num;
	$alliance_result = wepix_query_error($alliance_query);

	$page_link_text = _A_PATH_PARTNER_ALLIANCE_LIST."?pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.ag-kind{ width:50px !important; }
.ag-name{ width:200px !important; }
.ag-sub-count{ width:40px !important; }
.ag-sub-view{ width:50px !important; }
</STYLE>
<div id="contents_head">
	<h1>제휴샵 목록</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_PARTNER_ALLIANCE_REG?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total : <b><?=$total_count?></b></span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>
		<div class="table-wrap">
			<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
				<tr>
					<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
					<th class="list-idx">고유번호</th>
					<th class="ag-kind">분류</th>
					<th class="ag-name">회사명</th>
					<th class="list-active">노출</th>
					<th class="ag-btn">관리</th>
				</tr>
<?
while($alliance_list = wepix_fetch_array($alliance_result)){

	$_view_as_view = ($alliance_list[AS_VIEW]=="Y") ? "노출" : "비노출";
	$trcolor = "#fff";
	if( $alliance_list[AG_VIEW] == "N" ){
		$trcolor = "#eee";
	}
?>
				<tr bgcolor="<?=$trcolor?>">
					<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$alliance_list[AS_IDX]?>" ></td>
					<td class="list-idx"><?=$alliance_list[AS_IDX]?></td>
					<td class="ag-kind"></td>
					<td class="ag-name" style="text-align:left !important;"><B><?=$alliance_list[AS_NAME]?><B/></td>
					<td class="list-active"><?=$_view_as_view?></td>
					<td class="ag-btn">
						<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="location.href='<?=_A_PATH_PARTNER_ALLIANCE_REG?>?mode=modify&key=<?=$alliance_list[AS_IDX]?>'">Modify</button>
						<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="agencyDel('A','<?=$alliance_list[AS_IDX]?>');"><i class="far fa-trash-alt"></i> DEL</button>
					</td>
				</tr>
<? } ?>
			</table>
		</div>
	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>