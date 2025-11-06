<?
$pageGroup = "dashboard";
$pageName = "product";

include "../lib/inc_common.php";
include "../layout/header.php";

	if(!$s_day){
		$startDayDate =  "01";
		$todayDayDate =  date("d");

		$s_day = date("Y-m-").$startDayDate;
		$e_day = date("Y-m-").$todayDayDate;
	}
	 $chartSearchDayDateSt = str_replace("-","",$s_day);
	 $chartSearchDayDateEd = str_replace("-","",$e_day);

	$productIdxResult = wepix_query_error("SELECT hp_ex_idx AS idx FROM hit_post WHERE hp_date_code BETWEEN ".$chartSearchDayDateSt." AND ".$chartSearchDayDateEd." GROUP BY hp_ex_idx order by sum(hp_hit) desc");

?>

<div id="contents_head">
	<h1>기간별 상품 Views</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list">
				<span class="count"><b>검색기간 : <?=$s_day?> ~  <?=$e_day?></b></span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">
					<table class="table-list" style='width:600px !important ; float:left; margin-right:60px;'>
					<tr>
						<th>제품명</th>
						<th>Total</th>
						<th>PC</th>
						<th>Mobile</th>
					</tr>
						<?
							while($productIdxArray = wepix_fetch_array($productIdxResult) ){
								$today_visit = wepix_fetch_array(wepix_query_error("
									SELECT hp_ex_idx AS idx ,
										SUM(hp_hit) AS total, 
										SUM(hp_pc) AS pc, 
										SUM(hp_mobile) AS mobile 
									FROM hit_post WHERE hp_date_code BETWEEN ".$chartSearchDayDateSt." AND ".$chartSearchDayDateEd." AND hp_ex_idx = ".$productIdxArray[idx]." "
								));
								$comparison_data = wepix_fetch_array(wepix_query_error("select CD_NAME,CD_IDX from "._DB_COMPARISON." where CD_IDX = '".$today_visit[idx]."' "));
						 ?>
					 <tr>
						<td><?=$comparison_data[CD_NAME]?></td>
						<td><?=$today_visit[total]?></td>
						<td><?=$today_visit[pc]?></td>
						<td><?=$today_visit[mobile]?></td>
					 </tr>
					 	<?
						 }
					    ?>
					</table>
					<div id="visit_chart">
						<div class="demo-section k-content wide">
							<div id="chart"></div>
						</div>
					</div>
					

				</div><!-- #list_box2 -->
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
					<form name='search_form' method='post' action="/admin2/dashboard/product_statistics.php?mode=search">
					<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
					
					<ul class="filter-from-ui m-t-5">
						<input type="text" id="s_day" name="s_day" value="<?=$s_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="시작일" readonly /> ~
						<input type="text" id="e_day" name="e_day" value="<?=$e_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="종료일" readonly />
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
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2020.2.617/styles/kendo.default-v2.min.css" />
<script src="https://kendo.cdn.telerik.com/2020.2.617/js/jquery.min.js"></script>
<script src="https://kendo.cdn.telerik.com/2020.2.617/js/kendo.all.min.js"></script>
<link rel="stylesheet" href="/admin2/css/daterangepicker.css" />
<script src="/admin2/js/moment.min.js"></script>
<script src="/admin2/js/jquery.daterangepicker.js"></script>

<script type="text/javascript"> 
<!-- 

function goSerch(){

	var form1 = document.search_form;
	form1.submit();
}

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
exit;
?>
