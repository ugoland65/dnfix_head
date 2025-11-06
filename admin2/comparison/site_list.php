<?
$pageGroup = "comparison";
$pageName = "site_list";

include "../lib/inc_common.php";

	$_serch_query = " ";

	$total_count = wepix_counter(_DB_SITE, $_serch_query);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

/*
SD_IDX desc
*/

	$query = "select * from "._DB_SITE." ".$_serch_query." order by SD_CLINK_COUNT desc limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$page_link_text = _A_PATH_SITE_LIST."?pn=";
	$_view_paging = publicPaging($pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";
?>

<STYLE TYPE="text/css">
.site-logo{ width:50px; }
.site-logo img{ width:100%; }
.ag-name{ width:200px !important; }
.ag-sub-count{ width:40px !important; }
.ag-sub-view{ width:50px !important; }
</STYLE>

<script type='text/javascript'>
	function goDel(idx){
		if(confirm("삭제하시겠습니까?")){
			$.ajax({
				type: "post",
				url : "<?=_A_PATH_COMPARISON_OK?>",
				data : { 
					a_mode : "siteDel",
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
	}
</script>

<div id="contents_head">
	<h1>판매몰 목록</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_SITE_REG?>'" > 
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
						<th >고유번호</th>
						<th ></th>
						<th >이름</th>
						<th >랭킹</th>
						<th >종류</th>

						<th >로고</th>
						<th >도메인</th>
						<th >노출</th>
						<th >리스트</th>
						<th >연동상품</th>
						<th >관리</th>
					</tr>
<?
while($list = wepix_fetch_array($result)){
?>
					<tr>
						<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list[SD_IDX]?>" ></td>
						<td ><?=$list[SD_IDX]?></td>
						<td class="site-logo"><img src="/data/site_logo/<?=$list[SD_LOGO]?>" alt=""></td>
						<td >
							<b><?=$list[SD_NAME]?></b>
						</td>
						<td ><?=$list[SD_RANK]?></td>
						<td ><?=$bva_site_kind[$list[SD_KIND]]?></td>

						<td ><?=$list[SD_LOGO]?></td>
						<td ><?=$list[SD_DOMAIN]?></td>
						<td ><?=$list[SD_ACTIVE]?></td>
						<td ><?=$list[SD_LIST_ACTIVE]?></td>
						<td ><?=$list[SD_CLINK_COUNT]?></td>
						<td >
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="location.href='<?=_A_PATH_SITE_REG?>?mode=modify&key=<?=$list[SD_IDX]?>'">Modify</button>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-info btnstyle1-xs" onclick="memoPopup('site',<?=$list[SD_IDX]?>)"><i class="fas fa-sticky-note"></i> 메모</button>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="goDel('<?=$list[SD_IDX]?>');"><i class="far fa-trash-alt"></i> DEL</button>
						</td>
					</tr>
				<? } ?>
			</table>
		</div>
		<div class="footer-padding"><?=$_view_paging?></div>
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