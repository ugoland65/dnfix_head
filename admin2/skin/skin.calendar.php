<?

	//$_calendar_view = json_decode($calendar_view, true);

	for ($i=0; $i<count($_calendar_view); $i++){
		$_cv[$_calendar_view[$i]] = "show";
	}

	//---- 오늘 날짜
	$thisyear = date('Y'); // 4자리 연도
	$thismonth = date('n'); // 0을 포함하지 않는 월
	$thismonth2 = date('m'); 
	$today = date('j'); // 0을 포함하지 않는 일

	//------ $year, $month 값이 없으면 현재 날짜
	$year = isset($_y) ? $_y : $thisyear;
	$month = isset($_m) ? $_m : $thismonth;
	$day = isset($_GET['day']) ? $_GET['day'] : $today;

	$prev_month = $month - 1;
	$next_month = $month + 1;
	$prev_year = $next_year = $year;

	if ( $month == 1 ) {
		$prev_month = 12;
		$prev_year = $year - 1;
	} else if ( $month == 12 ) {
		$next_month = 1;
		$next_year = $year + 1;
	}
	$preyear = $year - 1;
	$nextyear = $year + 1;

	$predate = date("Y-m-d", mktime(0, 0, 0, $month - 1, 1, $year));
	$nextdate = date("Y-m-d", mktime(0, 0, 0, $month + 1, 1, $year));

	// 총일수 구하기
	$max_day = date('t', mktime(0, 0, 0, $month, 1, $year)); // 해당월의 마지막 날짜

	// 2. 시작요일 구하기
	$start_week = date("w", mktime(0, 0, 0, $month, 1, $year)); // 일요일 0, 토요일 6

	// 3. 총 몇 주인지 구하기
	$total_week = ceil(($max_day + $start_week) / 7);

	// 4. 마지막 요일 구하기
	$last_week = date('w', mktime(0, 0, 0, $month, $max_day, $year));


	$_before_m_max_day = date('t', mktime(0, 0, 0, $month-1, 1, $year)); // 지난달 총 날짜
	$_before_m_start_day = ($_before_m_max_day - $start_week) + 1;

	$_after_m_day = 1;
	$_after_m_end_day = 6 - $last_week;

	/*
	$tot_days = getTotaldays2($year, $month); // 해당 년월의 총 날짜수를 구한다.
	$month_code = ( $month < 10 ) ? "0".$month : $month;

	$_sdate = $year."-".$month_code."-01";
	$_edate = $year."-".$month_code."-".$tot_days;
	*/

	$_sdate = $prev_year."-".$prev_month."-".$_before_m_start_day;
	$_edate = $next_year."-".$next_month."-".$_after_m_end_day;
	
	if( $_cv['holiday'] == "show" ){

		/*
		$_where = " WHERE mode IN( '월차','반차','유급휴가','조퇴','휴가' ) ";
		$_where .= " AND date_s >= '".$_sdate." 00:00:00' AND date_e <= '".$_edate." 23:59:59' ";
		*/
		$_where = " WHERE date_s >= '".$_sdate." 00:00:00' AND date_e <= '".$_edate." 23:59:59' ";

		//echo $_where;

		$_query = "select * from schedule_sttaf ".$_where." ORDER BY idx DESC";  //echo $_query;
		$_result = sql_query_error($_query);
		while($_list = sql_fetch_array($_result)){
			
			$_data = json_decode($_list['data'], true);
			$_reg_name = $_data['reg']['name'];
			$_reg_date = date("Y.m.d H:i", strtotime($_data['reg']['date']));
			$_target_name = $_data['target']['name'];

			$day_code = date("Ymd", strtotime($_list['date_s']));
			$_staff_holiday[$day_code][] = array(
				"idx" => $_list['idx'],
				"mode" => $_list['mode'],
				"target_name" => $_target_name
			);

		}//while
	}
	
	$_where = " WHERE date_s >= '".$_sdate." 00:00:00' AND date_e <= '".$_edate." 23:59:59' ";

	if( $_cv['delivery'] != "show" ){ $_where .= " AND kind != '배송비' "; }
	if( $_cv['tax'] != "show" ){ $_where .= " AND kind != '관/부가세' "; }
	if( $_cv['staff_meeting'] != "show" ){ $_where .= " AND kind != '회의' "; }
	if( $_cv['meeting'] != "show" ){ $_where .= " AND kind != '방문미팅' AND kind != '외부미팅' "; }
	if( $_cv['schedule'] != "show" ){ $_where .= " AND kind != '일정' "; }
	
	if( $_cv['individual'] == "show" ){
		$_where .= " AND target_idx IN(0, ".$_ad_idx.") ";
	}else{
		$_where .= " AND target_idx = '0' ";
	}

	$_commentDay = [];
	$_query = "select * from calendar ".$_where." ORDER BY idx DESC"; //echo $_query;
	$_result = sql_query_error($_query);
	while($_list = sql_fetch_array($_result)){
		
		$day_code = date("Ymd", strtotime($_list['date_s']));

		if( $_list['mode'] == "comment" ){
			$_commentDay[$day_code] = [
				"idx" => $_list['idx'],
				"comment_count" => $_list['comment_count'] ?? 0,
			];
		}else{
			$_calendar[$day_code][] = [
				"idx" => $_list['idx'],
				"mode" => $_list['mode'],
				"kind" => $_list['kind'],
				"state" => $_list['state'],
				"subject" => $_list['subject'],
				"data" => $_list['data']
			];
		}

	}//while


function calendarContents( $dcode ){ 
	global $_staff_holiday, $_calendar;
	
	$shtml = "";

	//월차 반차 정보
	$shtml .= "<div class='m-t-10'>";
	
	for ($z=0; $z<count($_staff_holiday[$dcode]); $z++){

		$shtml .= "
			<ul class='calendar-unit-ul'>
				<span style='cursor:pointer;' onclick='onlyAD.staffHolidayView(".$_staff_holiday[$dcode][$z]['idx'].")'>
					<i class='fas fa-user-alt-slash' style=' font-size:10px !important;'></i> ".$_staff_holiday[$dcode][$z]['mode']." - ".$_staff_holiday[$dcode][$z]['target_name']."
				</span>
			</ul>";

	} //for END

	$shtml .= "</div>";

	//캘린더
	$shtml .= "<div>";

	/*
	for ($z=0; $z<count($_calendar[$dcode]); $z++){ 
	
		if( $_calendar[$dcode][$z]['mode'] == "결제기한" && 
			( $_calendar[$dcode][$z]['kind'] == "배송비" || $_calendar[$dcode][$z]['kind'] == "관/부가세" )
		){
				$_icon = "";
			if( $_calendar[$dcode][$z]['kind'] == "배송비" ){
				$_icon = '<i class="fas fa-truck" style="font-size:10px !important;" ></i>(배) ';
			}elseif( $_calendar[$dcode][$z]['kind'] == "관/부가세" ){
				$_icon = '<i class="fas fa-receipt"></i>(관) ';
			}
		
			$_this_data = json_decode($_calendar[$dcode][$z]['data'], true); 
			$_this_data_oo_idx = $_this_data['oo_idx'];
			$_this_data_price = number_format($_this_data['price']);
			$_this_subject = $_icon." ".$_this_data_price;
			
			//일정 완료
			if( $_calendar[$dcode][$z]['state'] == "E" ){
				$_this_subject = "<s> <font class='calendar-approval-state-end'>".$_icon." ".$_this_data_price."</font> </s>";
			}

			$_onclick = "onlyAD.orderSheetView('".$_this_data_oo_idx."', 'global');";
		
		}else{
			
				$_icon = "";
			if( $_calendar[$dcode][$z]['kind'] == "회의" ){
				$_icon = '<i class="far fa-comment-dots"></i> ';
			}elseif( $_calendar[$dcode][$z]['kind'] == "방문미팅" || $_calendar[$dcode][$z]['kind'] == "외부미팅" ){
				$_icon = '<i class="far fa-handshake"></i> ';
			}elseif( $_calendar[$dcode][$z]['kind'] == "일정" ){
				$_icon = '<i class="fas fa-hiking"></i> ';
			}elseif( $_calendar[$dcode][$z]['kind'] == "체크" ){
				$_icon = '<i class="fas fa-calendar-check"></i> ';
			}elseif( $_calendar[$dcode][$z]['kind'] == "중요" ){
				$_icon = '<i class="fas fa-star"></i> ';
			}elseif( $_calendar[$dcode][$z]['kind'] == "행사" ){
				$_icon = '<i class="fas fa-democrat"></i> ';
			}elseif( $_calendar[$dcode][$z]['kind'] == "개인" ){
				$_icon = '<i class="fas fa-tag"></i> ';
			}

			//일정취소
			if( $_calendar[$dcode][$z]['state'] == "C" ){
				$_this_subject = "<s>[".$_calendar[$dcode][$z]['kind']."] ".$_calendar[$dcode][$z]['subject']."</s>";
			//일정 완료
			}elseif( $_calendar[$dcode][$z]['state'] == "E" ){
				$_this_subject = "<font class='calendar-approval-state-end'>".$_icon." ".$_calendar[$dcode][$z]['subject']."</font>";
			}else{
				$_this_subject = "".$_icon." ".$_calendar[$dcode][$z]['subject'];
			}

			$_onclick = "calendar.detail(this, '".$_calendar[$dcode][$z]['idx']."');";

		}

		$shtml .= '<ul class="calendar-unit-ul"><span style="cursor:pointer;" onclick="'.$_onclick.'">'.$_this_subject.'</span></ul>';
	
	} //for END
	*/

	foreach ($_calendar[$dcode] as $key => $val) {

		if ($val['mode'] == "결제기한" && 
			($val['kind'] == "배송비" || $val['kind'] == "관/부가세")) {

			$_icon = "";
			if ($val['kind'] == "배송비") {
				$_icon = '<i class="fas fa-truck" style="font-size:10px !important;"></i>(배) ';
			} elseif ($val['kind'] == "관/부가세") {
				$_icon = '<i class="fas fa-receipt"></i>(관) ';
			}

			$_this_data = json_decode($val['data'], true); 
			$_this_data_oo_idx = $_this_data['oo_idx'];
			$_this_data_price = number_format($_this_data['price']);
			$_this_subject = $_icon . " " . $_this_data_price;

			// 일정 완료
			if ($val['state'] == "E") {
				$_this_subject = "<s> <font class='calendar-approval-state-end'>".$_icon." ".$_this_data_price."</font> </s>";
			}

			$_onclick = "onlyAD.orderSheetView('".$_this_data_oo_idx."', 'global');";

		} else {
			$_icon = "";
			switch ($val['kind']) {
				case "회의":
					$_icon = '<i class="far fa-comment-dots"></i> ';
					break;
				case "방문미팅":
				case "외부미팅":
					$_icon = '<i class="far fa-handshake"></i> ';
					break;
				case "일정":
					$_icon = '<i class="fas fa-hiking"></i> ';
					break;
				case "체크":
					$_icon = '<i class="fas fa-calendar-check"></i> ';
					break;
				case "중요":
					$_icon = '<i class="fas fa-star"></i> ';
					break;
				case "행사":
					$_icon = '<i class="fas fa-democrat"></i> ';
					break;
				case "개인":
					$_icon = '<i class="fas fa-tag"></i> ';
					break;
			}

			// 일정 상태에 따른 처리
			if ($val['state'] == "C") {
				$_this_subject = "<s>[".$val['kind']."] ".$val['subject']."</s>";
			} elseif ($val['state'] == "E") {
				$_this_subject = "<font class='calendar-approval-state-end'>".$_icon." ".$val['subject']."</font>";
			} else {
				$_this_subject = $_icon . " " . $val['subject'];
			}

			$_onclick = "calendar.detail(this, '".$val['idx']."');";
		}

		$shtml .= '<ul class="calendar-unit-ul" title="'.$val['subject'].'"><span style="cursor:pointer;" onclick="'.$_onclick.'" >'.$_this_subject.'</span></ul>';
	}



	$shtml .= "</div>";


	return $shtml;
}





	/*
	echo $_query;

	echo "<pre>";
	print_r($_cv);
	echo "</pre>";
	*/

?>

<style type="text/css">

.calendal{ width:100%; margin:0 auto; font-size:0; vertical-align:top; overflow:hidden; }
.week_name{ vertical-align:top; height:30px; background-color:#fff; text-align:center;  line-height:30px; font-size: 14px; display:inline-block; box-sizing:border-box; border-bottom:1px solid #444; border-right:1px solid #444; }
.calendal > div.box{ vertical-align:top; min-height:130px; display:inline-block; box-sizing:border-box; border-top:1px solid #444; border-right:1px solid #444; padding:5px 0 0 5px;  }
.holiday-ok{ width:12.5%; }
.holiday-no{ width:15%; }
.day.holiday-no{ background-color:#fff; }

.calendal-title{ text-align:center; }
.calendal-title .ym{ font-size:17px; font-weight:600;  }

.calendal-table{ width:100%; border-spacing:0; border-collapse:collapse; padding:0; margin:0; border:none; table-layout:fixed; box-sizing:border-box;  }
.calendal-table tr.info th{ height:28px; border:1px solid #666; line-height:30px;  text-align:center; }

.calendal-table tbody tr td { height:115px; border:1px solid #666; padding:5px; vertical-align:top; box-sizing:border-box;  }
.calendal-table tbody tr td.black { background-color:#fff; }
.calendal-table tbody tr td.today { background-color:#ffffd9; }
.calendal-table tbody tr td span{ font-size:12px; }

.calendal-table .day{ display:inline-block; width:25px; height:25px; margin:-3px 0 0 -3px; line-height:25px; text-align:center; font-size:13px; font-weight:600; 
	cursor:pointer;   border-radius:50%; }
.calendal-table .day-before { width:25px; height:25px; margin:-3px 0 0 -3px; line-height:25px; text-align:center; color:#888; font-size:13px; font-weight:500; }

.calendal-table .day.holy{ color:#ff407a; }
.calendal-table .day.blue{ color:#216eec; }

.calendal-table .day:hover{ background-color:#1b56ff; color:#fff; }

.calendar-approval-state-end{ color:#aaa; }

.calendar-unit-ul { padding:2px; margin-bottom:3px; background-color:#f5f5f5; border:1px solid #eeee; border-radius:4px; }
.calendar-unit-ul span{ font-size:12px !important;  
	display:block;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis; 
}
 .calendar-unit-ul span i{ font-size:12px !important;   width:16px; text-align:center; }

.calendar-unit-ul:hover{ background-color:#fff; border:1px solid #ff0000; }
.calendar-unit-ul:hover span{ color:#ff0000; }
</style>

<div>

	<div class="calendal-title">
		<button type="button" class="btnstyle1 btnstyle1-sm" onclick="calendar.view('<?=$prev_year?>', '<?=$prev_month?>')" ><i class="fas fa-chevron-circle-left"></i></button>
		<span class="ym"><?=$year?>년 <?=$month?>월</span>
		<button type="button" class="btnstyle1 btnstyle1-sm" onclick="calendar.view('<?=$next_year?>', '<?=$next_month?>')" ><i class="fas fa-chevron-circle-right"></i></button>
	</div>

<table class="calendal-table m-t-15">
<? /*
  <tr align="center" >
    <td>
        <a href=<?php echo 'index.php?year='.$preyear.'&month='.$month . '&day=1'; ?>>◀◀</a>
    </td>
    <td>
        <a href=<?php echo 'index.php?year='.$prev_year.'&month='.$prev_month . '&day=1'; ?>>◀</a>
    </td>
    <td height="50" bgcolor="#FFFFFF" colspan="3">
        <a href=<?php echo 'index.php?year=' . $thisyear . '&month=' . $thismonth . '&day=1'; ?>>
        <?php echo "&nbsp;&nbsp;" . $year . '년 ' . $month . '월 ' . "&nbsp;&nbsp;"; ?></a>
    </td>
    <td>
        <a href=<?php echo 'index.php?year='.$next_year.'&month='.$next_month.'&day=1'; ?>>▶</a>
    </td>
    <td>
        <a href=<?php echo 'index.php?year='.$nextyear.'&month='.$month.'&day=1'; ?>>▶▶</a>
    </td>
  </tr>
*/
?>
<tr class="info">
    <th><span style="color:#ff407a;">일</span></td>
    <th>월</th>
    <th>화</th>
    <th>수</th>
    <th>목</th>
    <th>금</th>
    <th><span style="color:#216eec;">토</span></th>
  </tr>
	
<tbody>
<?
    
$day=1; // 5. 화면에 표시할 화면의 초기값을 1로 설정

for($i=1; $i <= $total_week; $i++){ // 6. 총 주 수에 맞춰서 세로줄 만들기

	echo "<tr>";

	for ($j = 0; $j < 7; $j++) { // 7. 총 가로칸 만들기
	
	// 8. 첫번째 주이고 시작요일보다 $j가 작거나 마지막주이고 $j가 마지막 요일보다 크면 표시하지 않음
	//echo '<td height="50" valign="top">';
	
	if (!(($i == 1 && $j < $start_week) || ($i == $total_week && $j > $last_week))) {

		if ($j == 0) { //일요일
			$holy_style = "holy";
		} else if ($j == 6) { //토요일
			$holy_style = "blue";
		} else { //평일
			$holy_style = "black";
		}

		// 12. 오늘 날짜면 굵은 글씨
		if ($year == $thisyear && $month == $thismonth && $day == date("j")) {
			$today_style = "today";
		}else{
			$today_style = "";
		}

		$day_code = date("Ymd", mktime(0,0,0,$month,$day,$year));
		$day_code2 = date("Y-m-d", mktime(0,0,0,$month,$day,$year));

		$_calendar_idx = "";
		$_calendar_comment_count = "";
		if( !empty($_commentDay[$day_code]) ){
			$_calendar_idx = $_commentDay[$day_code]['idx'];
			$_calendar_comment_count = $_commentDay[$day_code]['comment_count'];
		}
?>
			<td class="<?=$holy_style?> <?=$today_style?>">

				<div>
					<div class="day <?=$holy_style?>" onclick="calendar.reg('<?=$year?>','<?=$month?>','<?=$day?>')"><?=$day?></div>
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-xs" onclick="footerGlobal.comment('calendar','<?=$_calendar_idx?>','<?=$day_code2?>')" >
						댓글
						<? if( $_calendar_comment_count > 0 ) { ?> : <b><?=$_calendar_comment_count?></b><? } ?>
						<!-- <?=$day_code2?> / <?=$_calendar_idx?> -->
					</button>
				</div>
				<?=calendarContents( $day_code )?>
<?
            $day++;

		}else{

			//지난달
			if( $i == 1 ){
				$_this_day = $_before_m_start_day;
				$day_code = date("Ymd", mktime(0,0,0,$prev_month,$_this_day,$prev_year));
				$_before_m_start_day++;

			//다음달
			}else{
				$_this_day = $_after_m_day;
				$day_code = date("Ymd", mktime(0,0,0,$next_month,$_this_day,$next_year));
				$_after_m_day++;

			}
?>
			<td class="">
				<div class="day-before"><?=$_this_day?></div>
				<?=calendarContents( $day_code )?>
<? 
		} 
        echo '</td>';
    }
?>
	</tr>
	<? } ?>
</tbody>

</table>
</div>

<script type="text/javascript"> 
<!-- 
$(document).ready(function() {
    $(".calendar-unit-ul").mouseover(function() {
        $(this).tooltip();
    });
	/*
    $(".element").mouseout(function() {
        $(this).removeClass("hovered");
    });
	*/
});
//--> 
</script> 