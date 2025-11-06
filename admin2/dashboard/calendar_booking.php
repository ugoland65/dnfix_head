<?
$pageGroup = "dashboard";
$pageName = "calendar_booking";

include "../lib/inc_common.php";

	$_cmode = securityVal($cmode);
	if( $_cmode == "reg" ){
		$_page_title_name = "부킹 등록현황";
		$_date_colum_name = "BKP_BOOKING_DATE";
	}else{
		$_page_title_name = "부킹 현황";
		$_date_colum_name = "BKP_START_DATE";
	}

include "../layout/header.php";
?>
<div id="contents_head">
	<h1><?=$_page_title_name?></h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
<link href="https://fonts.googleapis.com/css?family=Fredoka+One" rel="stylesheet">
<STYLE TYPE="text/css">
.calendal-sum{ width:90%; margin:10px auto 0; text-align:right; }

#calendal_head{ width:90%; margin:0 auto;  }
#calendal_head select{ height:40px; background-color:#dddddd; font-family: 'Fredoka One', cursive; text-align:center; font-size:26px; }

#calendal_head_btn_wrap{ width:100%; display:table; }
#calendal_head_btn_left{ display:table-cell; }
#calendal_head_btn_right{ display:table-cell; text-align:right; }

#calendal_wrap{ width:90%; margin:0 auto; border-top:1px solid #444444; border-left:1px solid #444444; overflow:hidden; }
#calendal_wrap div.box{ height:130px; float:left; box-sizing:border-box; border-bottom:1px solid #444444; border-right:1px solid #444444;  }

.holiday_sun{ width:12%; height:30px!important; background-color:#DC143C; text-align:center; line-height:30px; font-size: 16px; }
.holiday_sat{ width:12%; height:30px!important; background-color:#1E90FF; text-align:center; line-height:30px;  font-size: 16px; }
.holiday_week{ width:12%; height:30px!important; background-color:#fff; text-align:center;   line-height:30px; font-size: 14px; }
.holiday_booking{ width:16%; height:30px!important; background-color:#333333; text-align:center;   line-height:30px; font-size: 14px; }

.holiday_ok{ width:12%; }
.holiday_no{ width:12%; }
.holiday_bk{ width:16%; }

.day-number{ width:30px; height:30px; line-height:30px; text-align:center; }

.daynum-sun{ color:#ff0000;}
.daynum-sat{ color:#3d38f4; }
.daynum-basic{ color:#000; }
.daynum-last{ color:#888; }

.now-day{ color:#fff; background-color:#FFFFCC; }
.past-day{ color:#999; background-color:#fff; }
.last-day{ color:#999; background-color:#f2f2f2; }
.booking-day{ color:#fff; background-color:#808080; padding:10px; }

.day-data-wrap{ width:100%; padding:2px 5px 0 5px; box-sizing:border-box;  }
.holiday_bk_date{ color:#fff; }
.show-count{ cursor:pointer; padding:3px 0 3px 15px; margin-top:3px; border-radius:3px; box-sizing:border-box; color:#666; position:relative; }
.show-count b{ color:#000 !important; }

.show-detail{ width:200px; background-color:#fff; border:1px solid #000;  border-radius:3px;  position:absolute; top:0px; right:-200px;  }

.show-count.hm{ background-color:#FFCC99; border:1px solid #f0a04f;  }
.show-count.fit{ background-color:#CCFFFF; border:1px solid #43c8c8;  }
.show-count.golf{ background-color:#ffcccc; border:1px solid #f28e8e;  }
.show-count.roomonly{ background-color:#ccccff; border:1px solid #8b8be7;  }

.show-count:hover{ background-color:#333; border:1px solid #111; color:#fff !important; }
.show-count:hover b{ color:#fff !important; }
</STYLE>
<script type='text/javascript'>
	var c_mode = "<?= $_cmode ?>";	
	function goList(type,st_date,kind){
		
		//alert("calendar_mode=on&type="+type+"&st_date="+st_date+"&kind="+kind+"&c_mode="+c_mode);
		location.href="<?=_A_PATH_BOOKING_LIST?>?calendar_mode=on&_ca_type="+type+"&_ca_st_date="+st_date+"&_ca_kind="+kind+"&_ca_c_mode="+c_mode;
	}	

</script>
<?
	//넘어오는 년월정보가 없으면 현재 년월을..
	if( !$cur_y ) $cur_y = date(Y); 
    if( !$cur_m ) $cur_m = date(m); 
    if($cur_m != 10){
		$cur_m_num = str_replace("0", "", $cur_m); 
	}
	$cur_d = date(d);

	$today_mktime = mktime(0,0,0,date(m),date(d),date(Y));

	//해당 년월의 총 날짜수를 구한다.
	$tot_days = getTotaldays2($cur_y, $cur_m);

	$tstamp = mktime(0,0,0,$cur_m,1,$cur_y); //해당 년월의 1일에 해당하는 timestamp값을 구한다.
	$tstamp_end = mktime(0,0,0,$cur_m,$tot_days,$cur_y); //해당 년월의 마지막 해당하는 timestamp값을 구한다.

	//timestamp값으로 날짜정보(요일)를 구한다.
	$tinfo = getdate($tstamp);
    $start_day = $tinfo["wday"];
	$tinfo_end = getdate($tstamp_end);
    $end_day = $tinfo_end["wday"];
    $plus_day = 6 - $end_day;

	$num = 0;
	$dayno = 0;
	$week_end = false;


	//달 부킹가져오기
	$_now_month_s_stamp = mktime(0,0,0,$cur_m,1,$cur_y);
	$_now_month_e_stamp = mktime(23,59,59,$cur_m,$tot_days+$plus_day,$cur_y);

	$bk_where = " where ".$_date_colum_name." >= ".$_now_month_s_stamp." and ".$_date_colum_name." <= ".$_now_month_e_stamp." and BKP_KIND !='CANCEL' ";
    $bk_query = "select BKP_IDX, ".$_date_colum_name.", BKP_TYPE, BKP_GUEST from "._DB_BOOKING." ".$bk_where." order by ".$_date_colum_name." asc";
	$bk_result = wepix_query_error($bk_query);
	$test_count = 0;
	while($bk_list = wepix_fetch_array($bk_result)){
		$test_count++;
		$bk_day_code = date('Y',$bk_list[$_date_colum_name]).date('m',$bk_list[$_date_colum_name]).date('d',$bk_list[$_date_colum_name]);
		${"_ary_".$bk_list[BKP_TYPE]}[$bk_day_code][] = $bk_list[BKP_IDX];
		${"_ary_".$bk_list[BKP_TYPE]."_head_count"}[$bk_day_code][] = count(explode("│",$bk_list[BKP_GUEST]));
	}
?>
		<div id='calendal_head'>
			<div id='calendal_head_btn_wrap'>
				<ul id='calendal_head_btn_left'>
					<!-- <i class="fas fa-chevron-circle-left"></i>  -->
					<select name="" id="calendar_year" onchange="calendal_ch()">
						<option value="2018" <? if($cur_y=="2018") echo "selected"; ?>>2018</option>
						<option value="2019" <? if($cur_y=="2019") echo "selected"; ?>>2019</option>
						<option value="2020" <? if($cur_y=="2020") echo "selected"; ?>>2020</option>
						<option value="2021" <? if($cur_y=="2021") echo "selected"; ?>>2021</option>
					</select>
					<select name="" id="calendar_month" onchange="calendal_ch()">
		<?
		for($i=1; $i<13; $i++){
		?>
						<option value="<?=$i?>" <? if($i==$cur_m) echo "selected"; ?>><?=$i?></option>
		<? } ?>
					</select>
		<!-- 
					<span class='year-name'>2019년</span>
					<span class='month-name'>7월</span>
		 -->
					<!-- <i class="fas fa-chevron-circle-right"></i> -->

					<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="height:30px; !important;" onclick="location.href='<?=_A_PATH_DASHBOARD_CDAL_BOOKING?>?cmode=<?=$_cmode?>&cur_y=<?=date(Y)?>&cur_m=<?=date(m)?>'" > <i class="far fa-calendar-alt"></i> 이번달</button>
				</ul>
				<ul id='calendal_head_btn_right'>
					<?  if( $_cmode == "reg" ) { ?>
						<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" style="height:30px; !important;" onclick="location.href='<?=_A_PATH_DASHBOARD_CDAL_BOOKING?>'" > <i class="far fa-calendar-alt"></i> 부킹 현황</button>
					<? }else{ ?>
						<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" style="height:30px; !important;" onclick="location.href='<?=_A_PATH_DASHBOARD_CDAL_BOOKING?>?cmode=reg'" > <i class="far fa-calendar-alt"></i> 부킹 등록현황</button>
					<? } ?>

				</ul>
			</div>
		</div>

		<div id="calendal_wrap">
			<div class="box holiday_sun"><span style='color:#fff'>Sun</span></div>
			<div class="box holiday_week">Mon</div>
			<div class="box holiday_week">The</div>
			<div class="box holiday_week">Wed</div>
			<div class="box holiday_week">Thu</div>
			<div class="box holiday_week">Fri</div>
			<div class="box holiday_sat"><span style='color:#fff'>Sat</span></div>
			<div class="box holiday_booking"><span style='color:#fff'>주 부킹현황</span></div>

<?
//----------------------------------------------------------------------------------------------------
//이전달 빈 블럭
$_last_month_day = $start_day - 1;

for($i=1; $i<=$start_day; $i++){
	$num++;

	if( $num == 1 || $num == 8 || $num == 15 || $num == 22 || $num == 29 || $num == 36 ){
		$holiday_css = "holiday_ok";
	}elseif( $num == 7 || $num == 14 || $num == 21 || $num == 28 || $num == 35 || $num == 42 ){
		$holiday_css = "holiday_ok";
	}else{
		$holiday_css = "holiday_no";
	}

    $day_mktime = mktime(0,0,0, date("m"), -$_last_month_day, date("Y"));
    $day_mktime_end  = mktime(23,59,59, date("m"), -$_last_month_day, date("Y"));
    $_last_month_day_no = date("d", $day_mktime);
?>
<div class="box <?=$holiday_css?> ">
	<ul class="day-number daynum-last"> <?=$_last_month_day_no?> </ul>
</div>
<? 
} 
//이전달 빈 블럭
//----------------------------------------------------------------------------------------------------
?>

<?
$zindex = 60;

	$HM_total_count = 0;
	$HM_total_head_count = 0;
	$FA_total_count = 0;
	$FA_total_head_count = 0;
	$GF_total_count = 0;
	$GF_total_head_count = 0;
	$RO_total_count = 0;
	$RO_total_head_count = 0;
//----------------------------------------------------------------------------------------------------
//이번달 시작
while(!$week_end) {

	$dayno++;
	$num++;

	$day_code = date("Ymd", mktime(0,0,0,$cur_m,$dayno,$cur_y));

    $day_mktime = mktime(0,0,0,$cur_m,$dayno,$cur_y);
    $day_mktime_end = mktime(23,59,59,$cur_m,$dayno,$cur_y);

	$w_tinfo = getdate($day_mktime);
	$w_day = $w_tinfo["wday"];

	if( $num == 1 || $num == 8 || $num == 15 || $num == 22 || $num == 29 || $num == 36 ){
		$holiday_css = "holiday_ok";
		$holiday_css2 = "daynum-sun";
	}elseif( $num == 7 || $num == 14 || $num == 21 || $num == 28 || $num == 35 || $num == 42 ){
		$holiday_css = "holiday_ok";
		$holiday_css2 = "daynum-sat";
	}else{
		$holiday_css = "holiday_no";
		$holiday_css2 = "daynum-basic";
    }

	if( $today_mktime == $day_mktime ){ 
		$day_class = "now-day"; 
	}elseif( $today_mktime > $day_mktime){
		$day_class = "last-day";
	}else{ 
		$day_class = "past-day";
	}

	$HM_count = count($_ary_HM[$day_code]);
	$HM_head_count = array_sum($_ary_HM_head_count[$day_code]);
	$FA_count = count($_ary_FA[$day_code]);
	$FA_head_count = array_sum($_ary_FA_head_count[$day_code]);
	$GF_count = count($_ary_GF[$day_code]);
	$GF_head_count = array_sum($_ary_GF_head_count[$day_code]);
	$RO_count = count($_ary_RO[$day_code]);
	$RO_head_count = array_sum($_ary_RO_head_count[$day_code]);
	
	$HM_total_count += $HM_count;
	$HM_total_head_count += $HM_head_count;
	$FA_total_count += $FA_count;
	$FA_total_head_count += $FA_head_count;
	$GF_total_count += $GF_count;
	$GF_total_head_count += $GF_head_count;
	$RO_total_count += $RO_count;
	$RO_total_head_count += $RO_head_count;

	$zindex--;
?>
	<div class="box <?=$holiday_css?> <?=$day_class?>">
		<ul class="day-number <?=$holiday_css2?>"><?=$dayno?></ul>
		<ul class="day-data-wrap">
			<!-- <?=$day_code?> -->
			<? if( $HM_count > 0 ){ ?><div onclick="goList('HM','<?=$day_code?>','day');" class="show-count hm" style="z-index:<?=$zindex?>"><b>HM</b> : <b><?=$HM_count?></b>ea <b><?=$HM_head_count?></b>p</div><? } ?>
			<? if( $FA_count > 0 ){ ?><div onclick="goList('FA','<?=$day_code?>','day');" class="show-count fit"><b>FIT</b> : <b><?=$FA_count?></b>ea <b><?=$FA_head_count?></b>p</div><? } ?>
			<? if( $GF_count > 0 ){ ?><div onclick="goList('GF','<?=$day_code?>','day');" class="show-count golf"><b>Golf</b> : <b><?=$GF_count?></b>ea <b><?=$GF_head_count?></b>p</div><? } ?>
			<? if( $RO_count > 0 ){ ?><div onclick="goList('RO','<?=$day_code?>','day');" class="show-count roomonly"><b>Room only</b> : <b><?=$RO_count?></b>ea <b><?=$RO_head_count?></b>p</div><? } ?>
		</ul>
	</div>

<?
//주 부킹현황
if($w_day == 6){
	
	$weeksum_day_no = $dayno;
	$weeksum_HM_count = 0;
	$weeksum_HM_head_count = 0;
	$weeksum_FA_count = 0;
	$weeksum_FA_head_count = 0;
	$weeksum_GF_count = 0;
	$weeksum_GF_head_count = 0;
	$weeksum_RO_count = 0;
	$weeksum_RO_head_count = 0;

 	for($i=0; $i<7; $i++){

		$weeksum_day_code = date("Ymd", mktime(0,0,0,$cur_m,$weeksum_day_no,$cur_y));

		$weeksum_HM_count += count($_ary_HM[$weeksum_day_code]);
		$weeksum_HM_head_count += array_sum($_ary_HM_head_count[$weeksum_day_code]);
		$weeksum_FA_count += count($_ary_FA[$weeksum_day_code]);
		$weeksum_FA_head_count += array_sum($_ary_FA_head_count[$weeksum_day_code]);
		$weeksum_GF_count += count($_ary_GF[$weeksum_day_code]);
		$weeksum_GF_head_count += array_sum($_ary_GF_head_count[$weeksum_day_code]);
		$weeksum_RO_count += count($_ary_RO[$weeksum_day_code]);
		$weeksum_RO_head_count += array_sum($_ary_RO_head_count[$weeksum_day_code]);

		$weeksum_day_no++;
	}

	$weeksum_start_day = date("y.m.d", mktime(0,0,0,$cur_m,$dayno,$cur_y));
	$weeksum_end_day = date("y.m.d", mktime(0,0,0,$cur_m,$weeksum_day_no-1,$cur_y));
?>
    <div class="box holiday_bk booking-day" >
		<div class="holiday_bk_date"><b><?=$weeksum_start_day?></b> ~ <b><?=$weeksum_end_day?></b></div>
		<? if( $weeksum_HM_count > 0 ){ ?><div onclick="goList('HM','<?=$weeksum_day_code?>','weeksum');" class="show-count hm"><b>HM</b> : <b><?=$weeksum_HM_count?></b>ea <b><?=$weeksum_HM_head_count?></b>p</div><? } ?>
		<? if( $weeksum_FA_count > 0 ){ ?><div onclick="goList('FA','<?=$weeksum_day_code?>','weeksum');" class="show-count fit"><b>FIT</b> : <b><?=$weeksum_FA_count?></b>ea <b><?=$weeksum_FA_head_count?></b>p</div><? } ?>
		<? if( $weeksum_GF_count > 0 ){ ?><div onclick="goList('GF','<?=$weeksum_day_code?>','weeksum');" class="show-count golf"><b>Golf</b> : <b><?=$weeksum_GF_count?></b>ea <b><?=$weeksum_GF_head_count?></b>p</div><? } ?>
		<? if( $weeksum_RO_count > 0 ){ ?><div onclick="goList('RO','<?=$weeksum_day_code?>','weeksum');"  class="show-count roomonly"><b>Room only</b> : <b><?=$weeksum_RO_count?></b>ea <b><?=$weeksum_RO_head_count?></b>p</div><? } ?>
	</div>
<? } ?>

<?
	if( $dayno >= $tot_days ){
		$week_end = true;
	}
}
//이번달 시작
//----------------------------------------------------------------------------------------------------
?>

<?
//----------------------------------------------------------------------------------------------------
//다음달 추가 블럭
$_next_month_day = 0;

for($i=1; $i<=$plus_day; $i++){
	$_next_month_day++;
	$_next_month_day_no = $_next_month_day;

	$plus_day_code = date("Ymd", mktime(0,0,0,$cur_m+1,$_next_month_day_no,$cur_y));

	$plus_HM_count = count($_ary_HM[$plus_day_code]);
	$plus_HM_head_count = array_sum($_ary_HM_head_count[$plus_day_code]);
	$plus_FA_count = count($_ary_FA[$plus_day_code]);
	$plus_FA_head_count = array_sum($_ary_FA_head_count[$plus_day_code]);
	$plus_GF_count = count($_ary_GF[$plus_day_code]);
	$plus_GF_head_count = array_sum($_ary_GF_head_count[$plus_day_code]);
	$plus_RO_count = count($_ary_RO[$plus_day_code]);
	$plus_RO_head_count = array_sum($_ary_RO_head_count[$plus_day_code]);
?>
	<div class="box <?=$holiday_css?> ">
		<ul class="day-number daynum-last"> <?=$_next_month_day_no?> </ul>
		<ul class="day-data-wrap">
			<? if( $plus_HM_count > 0 ){ ?><div onclick="goList('HM','<?=$plus_day_code?>','day');" class="show-count hm"><b>HM</b> : <b><?=$plus_HM_count?></b>ea <b><?=$plus_HM_head_count?></b>p</div><? } ?>
			<? if( $plus_FA_count > 0 ){ ?><div onclick="goList('FA','<?=$plus_day_code?>','day');" class="show-count fit"><b>FIT</b> : <b><?=$plus_FA_count?></b>ea <b><?=$plus_FA_head_count?></b>p</div><? } ?>
			<? if( $plus_GF_count > 0 ){ ?><div onclick="goList('GF','<?=$plus_day_code?>','day');" class="show-count golf"><b>Golf</b> : <b><?=$plus_GF_count?></b>ea <b><?=$plus_GF_head_count?></b>p</div><? } ?>
			<? if( $plus_RO_count > 0 ){ ?><div onclick="goList('RO','<?=$plus_day_code?>','day');" class="show-count roomonly"><b>Room only</b> : <b><?=$plus_RO_count?></b>ea <b><?=$plus_RO_head_count?></b>p</div><? } ?>
		</ul>
	</div>
<? 
}//for END 
//다음달 추가 블럭
//----------------------------------------------------------------------------------------------------
?>

<? if( $plus_day > 0 ){ ?>
    <div class="box holiday_bk booking-day" >
	</div>
<? } ?>
		</div><!-- #calendal_wrap -->
		<div class="calendal-sum">
			HM : <b><?=$HM_total_count?></b>ea <b><?=$HM_total_head_count?></b>p |
			FIT : <b><?=$FA_total_count?></b>ea <b><?=$FA_total_head_count?></b>p |
			Golf : <b><?=$GF_total_count?></b>ea <b><?=$GF_total_head_count?></b>p  |
			Room only : <b><?=$RO_total_count?></b>ea <b><?=$RO_total_head_count?></b>p
		</div>
	</div>
</div>


<script type='text/javascript'>
function calendal_ch(){
	//alert($("#calendar_year").val()+"/"+$("#calendar_month").val());
   location.href="<?=_A_PATH_DASHBOARD_CDAL_BOOKING?>?cmode=<?=$_cmode?>&cur_y="+ $("#calendar_year").val() +"&cur_m="+ $("#calendar_month").val();
}
</script>
<?
include "../layout/footer.php";
exit;
?>