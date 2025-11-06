<?
$pageGroup = "booking";
$pageName = "personal_list";

include "../lib/inc_common.php";

	$_pn = securityVal($pn);

	$search_sql = "where PG_IDX > 0 ";

	if($s_day){
		$_show_start_date = strtotime($s_day);
		$search_sql.= " and PG_DATE >= ".$_show_start_date;
		$page_link_text .= "&s_day=".$s_day;
	}
	if($e_day){
		$dend2 = explode("-",$e_day);
        $_show_end_date = mktime(23,59,59,$dend2[1],$dend2[2],$dend2[0]);

		$search_sql.= " and PG_DATE <= ".$_show_end_date;
		$page_link_text .= "&e_day=".$e_day;
	}
	if($search_type){
		$search_sql.= " and BKG_STATE =  '".$search_type."'";
		$page_link_text .= "&search_type=".$search_type;
	}
	if($search_text){
		$search_sql.= " and PG_NAME like '%".$search_text."%'";
		
	}

	$total_count = wepix_counter(_DB_PAYMENT_GATE, $search_sql);

	$list_num = 20;
	$page_num = 10;

	// 전체 페이지 계산
	$total_page  = @ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
	$counter = $total_count - (($_pn - 1) * $list_num);

	$query = "select * from "._DB_PAYMENT_GATE." ".$search_sql." order by PG_IDX desc limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$paging_url = _A_PATH_PERSONAL_PAYMENT_LIST.$page_link_text."&pn=";
	$_view_paging = publicPaging($_pn, $total_page, $list_num, $page_num, $paging_url);


include "../layout/header.php";
?>

<div id="contents_head">
	<h1>개인결제 리스트</h1>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">
				<table class="table-list">
					<tr>
						<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
						<th class="30px">고유번호</th>
						<th>상품명</th>
						<th>결제형태</th>
						<th>결제상황</th>
						<th>결제금액</th>
						<th>결제 요청일</th>
						<th>결제 완료일</th>
						<th width="140px">관리</th>
					</tr>
				<?	
				while($list = wepix_fetch_array($result)){ 

					$_view_reg_date = date("d-M-y",$list[PG_REG_DATE]);

					if($list[PG_DATE] == 0){
						$_view_pg_date = '미완료';
					}else{
						$_view_pg_date = date("d-M-y H:i",$list[PG_DATE]);
					}

				?>
					<tr  id="trid_<?=$list[PG_IDX]?>" bgcolor="<?=$trcolor?>">
						<td class="tl-check"><input type="checkbox" name="key_check[]"  class="checkSelect" value="<?=$list[PG_IDX]?>"></td>
						<td class="30px"><?=$list[PG_IDX]?></td>
						<td><?=$list[PG_NAME]?></td>
						<td><?=$list[PG_KIND]?></td>
						<td><?=$list[PG_STATE]?></td>
						<td><?=$gva_currency_simbol[$list[PG_PAYMENT_CURRENCY]]?><?=number_format($list[PG_TOTAL_PRICE])?></th>
						<td><?=$_view_reg_date?></td>
						<td><?=$_view_pg_date?></td>
						<td width="140px">
								<input type="button" value="상세보기 " style="margin-bottom:5px !important;" onclick="location.href='<?=_A_PATH_PERSONAL_PAYMENT_REG?>?idx=<?=$list[PG_IDX]?>'">
						</td>
					</tr>			

				<?}?>
				</table>

				</div><!-- #list_box2 -->
		
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
					<form name='search_form' method='post' action="<?=_A_PATH_PERSONAL_PAYMENT_LIST?>">
					<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
					<ul class="filter-from-ui m-t-5">
						<select name="search_type">
							<option value="" >타입선택</option>
							<option value="standby">standby</option>
							<option value="approval">approval</option>
							<option value="cancel" >cancel</option>
						</select>
					</ul>

					<ul class="filter-from-ui m-t-5">
						<input type="text" id="s_day" name="s_day" value="<?=$s_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="시작일" readonly /> ~
						<input type="text" id="e_day" name="e_day" value="<?=$e_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="종료일" readonly />
					</ul>
					<ul class="filter-from-ui m-t-5">
						<input type='text' name='search_text' id='board_subject' size='20' value="<?=$search_text?>" placeholder="상품이름">
					</ul>
					<ul class="filter-from-ui m-t-5">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="goSerch();" > 
							<i class="fas fa-search"></i> 검색
						</button>
					</ul>
				</div>
				</form>
			</ul>
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>
	</div>
 </div>
<link rel="stylesheet" href="/admin2/css/daterangepicker.css" />
<script src="/admin2/js/moment.min.js"></script>
<script src="/admin2/js/jquery.daterangepicker.js"></script>
<script type="text/javascript"> 
function CalculateModify(key){
	
	window.open("<?=_A_PATH_GROUP_CALCUATE?>?idx="+key, "overlap_"+key, "width=1280,height=980,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
function goSerch(){

	var form1 = document.search_form;
	form1.submit();
}
<!-- 
$(function(){
/*
	$("#s_day").datepicker();
	$("#e_day").datepicker();
*/
	$('#s_day').dateRangePicker(
	{
		separator : ' to ',
		getValue: function()
		{
			if ($('#date-range200').val() && $('#date-range201').val() )
				return $('#date-range200').val() + ' to ' + $('#date-range201').val();
			else
				return '';
		},
		setValue: function(s,s1,s2)
		{
			$('#s_day').val(s1);
			$('#e_day').val(s2);
		}
	});
});
//--> 
</script> 
<?
include "../layout/footer.php";
?>