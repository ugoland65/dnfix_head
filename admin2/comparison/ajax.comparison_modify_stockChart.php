<?
include "../lib/inc_common.php";

	
	$today_date = date("Y-m-d");

	if(!$s_day){
		$s_day = $today_date;
		$e_day = $today_date;
	}

	$_prd_idx = securityVal($prd_idx);

	$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx from prd_stock where ps_prd_idx = '".$_prd_idx."' "));
	$_ps_idx = $stock_data[ps_idx];

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


	if($mode != 'search'){
		$startDayDate =  1;
		$todayDayDate =  1*date("d");

		for($startDayDate;$startDayDate <= $todayDayDate;$startDayDate++){
			if($startDayDate < 10){
				$startDayDate = "0".$startDayDate;
			}
			$chartSearchDayDate = date("Y-m")."-".$startDayDate;
			$chartSearchDayDate2 = date("m").".".$startDayDate;

			$_stockMinusData = wepix_fetch_array(wepix_query_error("
				SELECT SUM(psu_qry) as psu_qry FROM prd_stock_unit 
					WHERE psu_stock_idx = '".$_ps_idx."' AND psu_mode = 'minus' AND psu_day = '".$chartSearchDayDate."' 
			"));

			$_stockPlusData = wepix_fetch_array(wepix_query_error("
				SELECT SUM(psu_qry) as psu_qry FROM prd_stock_unit 
					WHERE psu_stock_idx = '".$_ps_idx."' AND psu_mode = 'plus' AND psu_day = '".$chartSearchDayDate."' 
			"));

			$_stockDayData = wepix_fetch_array(wepix_query_error("
				SELECT psu_stock FROM prd_stock_unit 
					WHERE psu_stock_idx = '".$_ps_idx."' AND psu_day = '".$chartSearchDayDate."' ORDER BY psu_idx DESC LIMIT 0,1
			"));

			$stockPlusData[] = $_stockPlusData[psu_qry];
			$stockMinusData[] = $_stockMinusData[psu_qry];
			$stockDayData[] = $_stockDayData[psu_stock];

			$chartDayDateArray[] = $chartSearchDayDate2;
			
		}
		$chartDayTitle =   date("Y.m").".01 ~ ".date("d");
	}else{
		$chartSearchDayDate = dateGap($s_day,$e_day);
		$chartSearchDayDate2 = dateGap2($s_day,$e_day);
		
		for($i=0;$i<count($chartSearchDayDate);$i++){

			$_stockMinusData = wepix_fetch_array(wepix_query_error("
				SELECT SUM(psu_qry) as psu_qry FROM prd_stock_unit 
					WHERE psu_stock_idx = '".$_ps_idx."' AND psu_mode = 'minus' AND psu_day = '".$chartSearchDayDate[$i]."' 
			"));

			$_stockPlusData = wepix_fetch_array(wepix_query_error("
				SELECT SUM(psu_qry) as psu_qry FROM prd_stock_unit 
					WHERE psu_stock_idx = '".$_ps_idx."' AND psu_mode = 'plus' AND psu_day = '".$chartSearchDayDate[$i]."' 
			"));

			$_stockDayData = wepix_fetch_array(wepix_query_error("
				SELECT psu_stock FROM prd_stock_unit 
					WHERE psu_stock_idx = '".$_ps_idx."' AND psu_day = '".$chartSearchDayDate[$i]."' ORDER BY psu_idx DESC LIMIT 0,1
			"));

			$stockPlusData[] = $_stockPlusData[psu_qry];
			$stockMinusData[] = $_stockMinusData[psu_qry];
			$stockDayData[] = $_stockDayData[psu_stock];

			$chartDayDateArray[] = $chartSearchDayDate2[$i];
		}

		$chartDayTitle = str_replace("-", ".", $s_day)." ~ ". str_replace("-", ".", $e_day);
	}

	$_idx = $_prd_idx;

	$query = "select * from prd_stock where ps_prd_idx = '".$_prd_idx."'";
	$result = wepix_query_error($query);

	$alarm_data = wepix_fetch_array($result);

	$cd_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$alarm_data[ps_prd_idx]."'"));

	$_ary_alarm_count = explode(",", $alarm_data[ps_alarm_count]);
	$_ary_alarm_message = explode(",", $alarm_data[ps_alarm_message]);
	$_ps_alarm_yn = $alarm_data[ps_alarm_yn];


?>
<STYLE TYPE="text/css">
.table-list{ background-color:#fff; }
</STYLE>

<ul class="filter-from-ui m-t-5">
	<input type="text" id="s_day" name="s_day" value="<?=$s_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="시작일" readonly /> ~
	<input type="text" id="e_day" name="e_day" value="<?=$e_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="종료일" readonly />
	<button type="button" id="" style='width:80px;'  onclick="prdShow('stockChartSearch');" > 
		<i class="fas fa-search"></i> 검색
	</button>
</ul>
<div id="visit_chart">
	<div class="demo-section k-content wide">
		<div id="chart"></div>
	</div>
</div>
<br/>
<form name='alarm_stock_form'>
		<input type="hidden" name="a_mode" value="stockAlarmSet">
		<input type="hidden" name="prd_key" value="<?=$_idx?>">
		<table class="table-list">
			<tr>
				<th>상품 이름</th>
				<td><?=$cd_data[CD_NAME]?></td>
			</tr>
			<tr>
				<th>알림 설정</th>
				<td>
				<label><input type='radio' value='Y' name='alarmYN' <?if($_ps_alarm_yn == 'Y' || $_ps_alarm_yn == '' ){ echo "checked"; } ?> > Y </label>
				<label><input type='radio' value='N'name='alarmYN'  <?if($_ps_alarm_yn == 'N' ){ echo "checked"; } ?> > N </label>
				</td>
			</tr>
			<tr>
				<th>알림 카운트</th>
				<th>알림 메세지</th>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[0]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [0]?>'></td>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[1]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [1]?>'></td>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[2]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [2]?>'></td>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[3]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [3]?>'></td>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[4]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [4]?>'></td>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[5]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [5]?>'></td>
			</tr>
			<tr>
				<td colspan='2'><input type='button' value="설정 완료" onclick="doAlarmSet('<?=$_idx?>');"></td>
			</tr>
		</table>
</form>
	



<script>
	function createChart() {

		//var stockPlusData =  <?php echo json_encode($stockPlusData); ?>;
		var stockMinusData =  <?php echo json_encode($stockMinusData); ?>;
		var stockDayData = <?php echo json_encode($stockDayData); ?>;
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
				data: stockMinusData,
				name: "Minus",
				color:"#FFB4B9"

			},{
				type: "line",
				data: stockDayData,
				name: "Stock",
				color:"#FACD87"

			}],
			
			categoryAxis: {
				categories: dayDate,
				line: {
					visible: true
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

function doAlarmSet(){

	var queryString = $("form[name=alarm_stock_form]").serialize() ;

	$.ajax({
		url: "/admin2/comparison/comparison_ok.php",
		data : queryString,
		type: "POST",
		success: function(getHtml){
			location.reload();
		},
		error: function(request,status,error){
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

</script>
