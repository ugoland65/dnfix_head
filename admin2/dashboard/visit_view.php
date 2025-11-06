<?
$pageGroup = "dashboard";
$pageName = "visit";

include "../lib/inc_common.php";
include "../layout/header.php";


	$today_date = date("Y-m-d");
	$yesterday_date =date("Y-m-d", strtotime("-1 day"));
	
	if(!$s_day){
		$s_day = $today_date;
		$e_day = $today_date;
	}

 function dateGap($sdate,$edate){

	 $sdate = str_replace("-","",$sdate);
	 $edate = str_replace("-","",$edate);
	 $dateNum = 0;
	 for($i=$sdate;$i<=$edate;$i++){
	  $year = substr($i,0,4);
	  $month = substr($i,4,2);
	  $day = substr($i,6,2);
     if(checkdate($month,$day,$year)){
		   $date[$dateNum] = $year."-".$month."-".$day;
		   $dateNum++;
	  }
	 }
	return $date;
}

 function dateGap2($sdate,$edate){

	 $sdate = str_replace("-","",$sdate);
	 $edate = str_replace("-","",$edate);
	 $dateNum = 0;
	 for($i=$sdate;$i<=$edate;$i++){
	  $year = substr($i,0,4);
	  $month = substr($i,4,2);
	  $day = substr($i,6,2);
     if(checkdate($month,$day,$year)){
		   $date[$dateNum] = $month.".".$day;
		   $dateNum++;
	  }
	 }
	return $date;
}

	$visit_result = wepix_query_error("SELECT * FROM visit_sum WHERE vs_date >= '".$s_day."' AND vs_date <= '".$e_day."'");

	$today_visit = wepix_fetch_array(wepix_query_error("SELECT vs_count as total ,vs_pc as pc ,vs_mobile as mobile FROM visit_sum WHERE vs_date = '".$today_date."' "));
	$yesterday_visit = wepix_fetch_array(wepix_query_error("SELECT vs_count as total ,vs_pc as pc ,vs_mobile as mobile FROM  visit_sum  WHERE vs_date = '".$yesterday_date."' "));
	$max_visit  = wepix_fetch_array(wepix_query_error("SELECT vs_count as total ,vs_pc as pc ,vs_mobile AS mobile FROM visit_sum ORDER BY vs_count DESC LIMIT 1;"));
	$total_visit  = wepix_fetch_array(wepix_query_error("SELECT SUM(vs_count) as total ,SUM(vs_pc) as pc ,SUM(vs_mobile) as mobile FROM visit_sum "));

	if($mode != 'search'){
		$startDayDate =  1;
		$todayDayDate =  1*date("d");

		for($startDayDate;$startDayDate <= $todayDayDate;$startDayDate++){
			if($startDayDate < 10){
				$startDayDate = "0".$startDayDate;
			}
			$chartSearchDayDate = date("Y-m")."-".$startDayDate;
			$chartSearchDayDate2 = date("m").".".$startDayDate;
			$today_visit = wepix_fetch_array(wepix_query_error("SELECT vs_count as total ,vs_pc as pc ,vs_mobile as mobile FROM visit_sum WHERE vs_date = '".$chartSearchDayDate."' "));

			$chartDayAllArray[] = $today_visit[total];
			$chartDayPcArray[] = $today_visit[pc];
			$chartDayMobileArray[] = $today_visit[mobile];
			$chartDayDateArray[] = $chartSearchDayDate2;
			
		}
		$chartDayTitle =   date("Y.m").".01 ~ ".date("d");
	}else{

		$chartSearchDayDate = dateGap($s_day,$e_day);
		$chartSearchDayDate2 = dateGap2($s_day,$e_day);
		
		for($i=0;$i<count($chartSearchDayDate);$i++){

			$today_visit = wepix_fetch_array(wepix_query_error("SELECT vs_count as total ,vs_pc as pc ,vs_mobile as mobile FROM visit_sum WHERE vs_date = '".$chartSearchDayDate[$i]."' "));

			$chartDayAllArray[] = $today_visit[total];
			$chartDayPcArray[] = $today_visit[pc];
			$chartDayMobileArray[] = $today_visit[mobile];
			$chartDayDateArray[] = $chartSearchDayDate2[$i];
		}

		$chartDayTitle =    str_replace("-", ".", $s_day)." ~ ". str_replace("-", ".", $e_day);

	}
	


?>

<div id="contents_head">
	<h1>접속 통계</h1>
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
				<table class="table-list" style='width:600px !important ; float:left; margin-right:60px;'>
						
					</table>
					<div id="visit_chart">
						<div class="demo-section k-content wide">
							<div id="chart"></div>
						</div>
					</div>
					<table class="table-list" style='width:300px !important ; margin-left:60px !important ; '>
						<tr>
							<th></th>
							<th>총 방문자</th>
							<th>PC</th>
							<th>Mobile</th>
						</tr>
						<tr>
							<th>오늘</th>
							<td><?=number_format($today_visit[total])?></td>
							<td><?=number_format($today_visit[pc])?></td>
							<td><?=number_format($today_visit[mobile])?></td>
						</tr>
						<tr>
							<th>어제</th>
							<td><?=number_format($yesterday_visit[total])?></td>
							<td><?=number_format($yesterday_visit[pc])?></td>
							<td><?=number_format($yesterday_visit[mobile])?></td>
						</tr>
						<tr>
							<th>최대</th>
							<td><?=number_format($max_visit[total])?></td>
							<td><?=number_format($max_visit[pc])?></td>
							<td><?=number_format($max_visit[mobile])?></td>
						</tr>
						<tr>
							<th>전체</th>
							<td><?=number_format($total_visit[total])?></td>
							<td><?=number_format($total_visit[pc])?></td>
							<td><?=number_format($total_visit[mobile])?></td>
						</tr>
					</table>

				</div><!-- #list_box2 -->
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
					<form name='search_form' method='post' action="<?=_A_PATH_VISIT_VIEW?>?mode=search">
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


<script>
        function createChart() {

			var dayTotal =  <?php echo json_encode($chartDayAllArray); ?>;
			var dayPc =  <?php echo json_encode($chartDayPcArray); ?>;
			var dayMobile = <?php echo json_encode($chartDayMobileArray); ?>;
			var dayDate = <?php echo json_encode($chartDayDateArray); ?>;
			var dayTitle = <?php echo json_encode($chartDayTitle); ?>;
			

            $("#chart").kendoChart({
                title: {
                    text: dayTitle
                },
                legend: {
                    position: "top"
                },

                series: [{
					type: "column",
                    data: dayTotal,
                    name: "Total",
					color:"#FFB4B9"

                },{
					type: "line",
                    data: dayPc,
                    name: "PC",
					color:"#78E6E6"
                },{
					type: "line",
                    data: dayMobile,
                    name: "Mobile",
					color:"#FACD87"

                }],
                
                categoryAxis: {
                    categories: dayDate,
                    line: {
                        visible: false
                    },
                    labels: {
                        padding: {top: 0}
                    }
                },
				 tooltip: {
                    visible: true,
                    format: "{0}",
                    template: "#= series.name #: #= value #"
                }
               
            });
        }

        $(document).ready(createChart);
        $(document).bind("kendo:skinChange", createChart);
    </script>
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
