<?
	// 변수 초기화
	$_show_mode = $_show_mode ?? "연간통계";
	$_cur_y = $_cur_y ?? "";
	$_cur_m = $_cur_m ?? "";
	$_ps_idx = $_ps_idx ?? "";

	if( $_cur_y ){
		$cur_y = $_cur_y; 
	}else{ 
		$cur_y = date('Y'); 
	}

	if( $_cur_m ){
		$cur_m = $_cur_m; 
	}else{ 
		$cur_m = date('m');
	}

	if( $cur_y < date('Y') ){
		$_for_count = 12;
	}else{
		$_for_count = date('m')*1;
	}

function get_find_weeks_in_month( $date )// date format => Y-m-d  특정 month에 week 구하기
{
    if( empty($date) ) return [];
    
    $day = date('w', strtotime($date) );//xxxx년 xx월 1일에 대한 요일구함
    if( $day != 1 )//월요일이 아니면
        $date = date('Y-m-d', strtotime("next monday", strtotime($date)));// xxx년 xx월에 첫번째 월요일 구함.

    $start_week = date( "W", strtotime($date) );//첫번쨰 월요일이 몇번쨰 주인지.
    $year = date( "Y", strtotime( $date ) );//년도
    $temp_week = date( "Y-m-t", strtotime($date) );//xxxx년 xx월 마지막 날짜 구하고
    $last_week = date("W", strtotime($temp_week));// xxxx년 xx월 마지막 날짜가 년도기준 몇번째 주인지.

    $result = array();
    for( $i=$start_week; $i<=$last_week; $i++ )
    {
        $data = get_week($i,$year);
        $result[] = $data;
    }
    return $result;
}

function get_week( $week, $year )// week => xxxx년 기준 주차 year => xxxx
{
    $date_time = new DateTime();
    $result['start'] = $date_time->setISODate($year, $week, 1)->format('Y-m-d');//월요일
    $result['end'] = $date_time->setISODate($year, $week, 7)->format('Y-m-d');//일요일

    return $result;
}

$_ym01 = $_cur_y."-".$cur_m."-01";

$weeks = get_find_weeks_in_month(date($_ym01));
if (!is_array($weeks)) {
	$weeks = [];
}

/*
if( $_show_mode == "월간통계"){
	echo "<pre>";
	print_r($weeks);
	echo "</pre>";
}
*/
?>

<style type="text/css">
.table-style{}
.table-style th{ text-align:center; }
</style>


<form id="form_prd_info_stock_chart">
<input type="hidden" name="ps_idx" value="<?=$_ps_idx ?? ''?>">
<div>
	<select name="show_mode">
		<option value="연간통계" <? if( $_show_mode == "연간통계" ) echo "selected"; ?>>연간통계</option>
		<option value="월간통계" <? if( $_show_mode == "월간통계" ) echo "selected"; ?>>월간통계</option>
	</select>
	&nbsp;
	<input type='text' name='cur_y' value="<?=$cur_y ?? ''?>" class="width-50 m-r-3">년
	&nbsp;
	<select name="cur_m">
		<? for ($i=1; $i<13; $i++){ ?>
		<option value="<?=$i?>" <? if( ($cur_m*1) == $i ) echo "selected"; ?>><?=$i?>월</option>
		<? } ?>
	</select>
	&nbsp;
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdInfoStockChart.show()" >적용하기</button>
</div>
</form>

<? 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if( $_show_mode == "연간통계"){ 
?>
<table class="table-style m-t-6">
	<tr>
		<th>년/월</th>
		<th>신규입고</th>
		<th>판매</th>
	</tr>
<? 
$_avg_count = 0;
$_avg_sum = 0;

$_avg_this_m_count = 0;
$_avg_this_m_sum = 0;

	for ($i=0; $i<$_for_count; $i++){
		$_this_m = $_for_count - $i;
		$date = $cur_y."-".$_this_m."-01";
		$_this_last_day = date("t", strtotime($date));

		$_this_frist_timestemp = mktime(0, 0, 0, $_this_m, 1, $cur_y);
		$_this_last_timestemp = mktime(23, 59, 59, $_this_m, $_this_last_day, $cur_y);

		$stock_data = sql_fetch_array(sql_query_error("select 
			COUNT( CASE WHEN psu_mode = 'plus' AND psu_kind = '신규입고' THEN 1 END ) as in_stock_count,
			SUM( CASE WHEN psu_mode = 'plus' AND psu_kind = '신규입고' THEN psu_qry END ) as in_stock,
			SUM( 
				CASE 
					WHEN psu_mode = 'minus' AND psu_kind = '판매 (엑셀)' THEN psu_qry 
					WHEN psu_mode = 'minus' AND psu_kind = '판매' THEN psu_qry 
				END ) as sale_stock
			from prd_stock_unit WHERE psu_date >= '".$_this_frist_timestemp."' AND psu_date <= '".$_this_last_timestemp."' AND psu_stock_idx = '".$_ps_idx."' "));
		
		if (!is_array($stock_data)) {
			$stock_data = [];
		}
		
		$_avg_count++;
		$_avg_sum += ($stock_data['sale_stock'] ?? 0);

		// 이번달 제외
		if( $_this_m !=  date('m') ){

			$_avg_this_m_count++;
			$_avg_this_m_sum += ($stock_data['sale_stock'] ?? 0);

		}


?>
	<tr>
		<td><?=$cur_y ?? ''?>년 <?=$_this_m ?? ''?>월</td>
		<td>( <?=$stock_data['in_stock_count'] ?? 0?> 건) <?=$stock_data['in_stock'] ?? 0?></td>
		<td><b><?=$stock_data['sale_stock'] ?? 0?></b>건</td>
	</tr>
<? } ?>
	<tr>
		<th></th>
		<th></th>
		<th>
			월평균 : <b><?=$_avg_count > 0 ? round(($_avg_sum/$_avg_count),1) : 0?></b> 건<br>
			이번달(<?=date('m')?>월) 제외 월평균 : <b><?=$_avg_this_m_count > 0 ? round(($_avg_this_m_sum/$_avg_this_m_count),1) : 0?></b> 건
		</th>
	</tr>
</table>

<? 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_show_mode == "월간통계"){ 
?>
<table class="table-style m-t-6">
	<tr>
		<th>주차</th>
		<th>날짜</th>
		<th>판매</th>
	</tr>
	<?		
		$_week_num = 0;

		foreach ( $weeks as $key => $val ){

			// 배열 검증
			if (!is_array($val)) {
				continue;
			}

			$_week_num++;

			$_this_start = explode("-", $val['start'] ?? '');
			$_this_end = explode("-", $val['end'] ?? '');
			
			if (!is_array($_this_start) || count($_this_start) < 3) {
				$_this_start = [date('Y'), date('m'), date('d')];
			}
			if (!is_array($_this_end) || count($_this_end) < 3) {
				$_this_end = [date('Y'), date('m'), date('d')];
			}

			$_this_frist_timestemp = mktime(0, 0, 0, $_this_start[1], $_this_start[2], $_this_start[0]);
			$_this_last_timestemp = mktime(23, 59, 59, $_this_end[1], $_this_end[2], $_this_end[0]);

			$stock_data = sql_fetch_array(sql_query_error("select 
				SUM( 
					CASE 
						WHEN psu_mode = 'minus' AND psu_kind = '판매 (엑셀)' THEN psu_qry 
						WHEN psu_mode = 'minus' AND psu_kind = '판매' THEN psu_qry 
					END ) as sale_stock
				from prd_stock_unit WHERE psu_date >= '".$_this_frist_timestemp."' AND psu_date <= '".$_this_last_timestemp."' AND psu_stock_idx = '".$_ps_idx."' "));
			
			if (!is_array($stock_data)) {
				$stock_data = [];
			}

	?>
	<tr>
		<td><b><?=$_week_num?></b>주차</td>
		<td>(월요일) <b><?=$val['start'] ?? ''?></b> ~ <b><?=$val['end'] ?? ''?></b> (일요일)</td>
		<td><? if( ($stock_data['sale_stock'] ?? 0) > 0 ){ ?><b><?=$stock_data['sale_stock']?></b>건<? }else{ ?>-<? } ?></td>
	</tr>
	<? } ?>
</table>
<? } ?>

<table class="table-style m-t-10">
	<colgroup>
		<col width="130px"/>
		<col  />
		<col width="90px"/>
		<col  />
		<col width="90px"/>
	</colgroup>
	<tr>
		<th>입고일</th>
		<th>비고</th>
		<th>입고수량</th>
		<th>기간</th>
		<th>판매</th>
	</tr>
<?
	$in_s = "";
	$in_e = "";

	$_where = "where psu_kind = '신규입고' AND psu_mode = 'plus' AND psu_stock_idx = '".$_ps_idx."' ";
	$_query = "select * from prd_stock_unit  ".$_where." order by psu_idx DESC  limit 0, 10";
	$_result = sql_query_error($_query);
	while($list = sql_fetch_array($_result)){
	
		// 배열 검증
		if (!is_array($list)) {
			continue;
		}
		
		if( !$in_e ) $in_e = date("Y-m-d");

		$_show_date = ($list['psu_day'] ?? '')." ~ ".$in_e;
		
		$psu_day = $list['psu_day'] ?? date("Y-m-d");
		if( !empty($psu_day) ){
			$from = new DateTime($psu_day);
			$to = new DateTime($in_e);
			$_show_date_count = $from -> diff( $to ) -> days;
		}else{
			$_show_date_count = 0;
		}

		$_this_frist_day_arr = explode("-", $psu_day);
		$_this_last_day_arr = explode("-", $in_e);
		
		if (!is_array($_this_frist_day_arr) || count($_this_frist_day_arr) < 3) {
			$_this_frist_day_arr = [date('Y'), date('m'), date('d')];
		}
		if (!is_array($_this_last_day_arr) || count($_this_last_day_arr) < 3) {
			$_this_last_day_arr = [date('Y'), date('m'), date('d')];
		}

		$_this_frist_timestemp = mktime(0, 0, 0, $_this_frist_day_arr[1], $_this_frist_day_arr[2], $_this_frist_day_arr[0]);
		$_this_last_timestemp = mktime(23, 59, 59, $_this_last_day_arr[1], $_this_last_day_arr[2], $_this_last_day_arr[0]);

		$stock_data = sql_fetch_array(sql_query_error("select 
			COUNT( CASE WHEN psu_mode = 'plus' AND psu_kind = '신규입고' THEN 1 END ) as in_stock_count,
			SUM( CASE WHEN psu_mode = 'plus' AND psu_kind = '신규입고' THEN psu_qry END ) as in_stock,
			SUM( 
				CASE 
					WHEN psu_mode = 'minus' AND psu_kind = '판매 (엑셀)' THEN psu_qry 
					WHEN psu_mode = 'minus' AND psu_kind = '판매' THEN psu_qry 
				END ) as sale_stock
			from prd_stock_unit WHERE psu_date >= '".$_this_frist_timestemp."' AND psu_date <= '".$_this_last_timestemp."' AND psu_stock_idx = '".$_ps_idx."' "));
		
		if (!is_array($stock_data)) {
			$stock_data = [];
		}

?>
	<tr>
		<td class="text-center"><?=$list['psu_day'] ?? ''?></td>
		<td><?=$list['psu_memo'] ?? ''?></td>
		<td class="text-right"><?=$list['psu_qry'] ?? 0?></td>
		<td><?=$_show_date ?? ''?> (<?=$_show_date_count ?? 0?>일)</td>
		<td class="text-right"><b><?=$stock_data['sale_stock'] ?? 0?></b>건</td>
	</tr>
<?
	$in_e = $list['psu_day'] ?? date("Y-m-d");
} ?>
</table>

<script type="text/javascript"> 
<!-- 
var prdInfoStockChart = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		show : function() {

			ajaxUrl = "/ad/ajax/prd_info_stock_chart";
			var formData = $("#form_prd_info_stock_chart").serializeArray();

			$.ajax({
				url: ajaxUrl,
				data: formData,
				type: "POST",
				dataType: "text",
				success: function(getHtml){
					if (getHtml){
						$("#crm_body").html(getHtml);
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {

				}
			});

		}
	};

}();
//--> 
</script> 