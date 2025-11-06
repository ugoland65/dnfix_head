<?
	include "../lib/inc_common.php";


	$_search_mode = securityVal($search_mode);
	$_search_kind = securityVal($search_kind);
	$_search_st = securityVal($search_st);
	$_search_et = securityVal($search_et);
	$_search_date_kind = securityVal($search_date_kind); 
	$_search_text = securityVal($search_text);
	$_pn = securityVal($pn);
	$_sort_kind = securityVal($sort_kind);
	$_order_by = securityVal($order_by); 
	$_calendar_mode = securityVal($calendar_mode);
	

	$bk_where_sql = "where BKP_IDX > 0 ";
	$bk_sort_sql = " BKP_START_DATE asc ";

    if( $_search_st=="" AND $_search_et=="" ){

        $_search_st = date("Y-m-d",$wepix_now_time);
        $_search_et  = date("Y-m-d",strtotime ("+10 days"));
        $st_date = strtotime($_search_st);
        $dend2 = explode("-",$_search_et);
        $end_date = mktime(23,59,59,$dend2[1],$dend2[2],$dend2[0]);
		$bk_where_sql .= " and BKP_START_DATE >= ".$st_date." and BKP_START_DATE <= ".$end_date;

    }elseif( $_search_st > 0 ){

		$_show_start_date = strtotime($_search_st);
		if( $_search_et > 0 ){
			$_show_end_date = strtotime($_search_et);
		}else{
			$_show_end_date = $_show_start_date+((60*60*24)*10);
			$_search_et = date("Y-m-d",$_show_end_date);
		}
		$bk_where_sql .= " and BKP_START_DATE >= ".$_show_start_date." and BKP_START_DATE <= ".$_show_end_date;

		$bk_sort_sql = " BKP_START_DATE asc ";
	}

	$search_box = "";
	//........................................................................................................................................................
	//검색박스 - 부킹 수동상태
	$ary_scb_booking_kind = "";
	for( $i=0; $i<count($booking_kind_array); $i++ ){
		if( ${"search_check_box_".$booking_kind_array[$i]} == 'on' ){
			$ary_scb_booking_kind[] = $booking_kind_array[$i];
			$search_box .= "&search_check_box_".$booking_kind_array[$i]."=on";
		}
    }
	if( $ary_scb_booking_kind !="" ){
		$show_scb_booking_kind = implode("','", $ary_scb_booking_kind);
		$bk_where_sql .= " and BKP_KIND in ('".$show_scb_booking_kind."') ";
    }
	//검색박스 - 지역
	$ary_scb_booking_area = "";
	for( $i=0; $i<count($booking_area_array); $i++ ){
		if( ${"search_check_box_".$booking_area_array[$i]} == 'on' ){
			$ary_scb_booking_area[] = str_replace('K_P','K-P',$booking_area_array[$i]);
			$search_box .= "&search_check_box_".$booking_area_array[$i]."=on";
		}
	}
	if( $ary_scb_booking_area !="" ){
		$show_scb_booking_area = implode("','", $ary_scb_booking_area);
		$bk_where_sql .= " and BKP_AREA in ('".$show_scb_booking_area."') ";
    }
	//부킹종류
	$ary_scb_booking_type = "";
	for( $i=0; $i<count($booking_type_array2); $i++ ){
		if( ${"search_check_box_".$booking_type_array2[$i]} == 'on' ){
			$ary_scb_booking_type[] = $booking_type_array2[$i];
			$search_box .= "&search_check_box_".$booking_type_array2[$i]."=on";
		}
	}
	if( $ary_scb_booking_type !="" ){
		$show_scb_booking_type = implode("','", $ary_scb_booking_type);
		$bk_where_sql .= " and BKP_TYPE in ('".$show_scb_booking_type."') ";
    }
	//........................................................................................................................................................



	// 검색이 있을경우
	if( $search_mode == "ok" ){

	}

	if( $_search_kind && $_search_text != "" ){
        if($_search_kind == 'guest'){
            $bk_where_sql .= " and BKP_GUEST like '%".$_search_text."%'";
        }else if($_search_kind == 'hotel'){
            $bk_where_sql .= " and BKP_HOTEL like '%".$_search_text."%'";
        }else if($_search_kind == 'agency'){
            $bk_where_sql .= " and BKP_AGNCY_TEXT like '%".$_search_text."%'";
        }else if($_search_kind == 'business'){
            $bk_where_sql .= " and BKP_BUSINESS like '%".$_search_text."%'";
        }else if($_search_kind == 'manager'){
            $bk_where_sql .= " and BKP_RESERVER like '%".$_search_text."%'";
        }
	}


	if( $_sort_kind == 'kind' || $_sort_kind == 'loc' ){
		if( $_order_by == '' || $_order_by == 'desc' ){
			$_order_by ='asc';
		}else{
			$_order_by ='desc';
		}
	}else{
		if( $_order_by == '' || $_order_by == 'asc' ){
			$_order_by ='desc';
		}else{
			$_order_by ='asc';
		}
	}

	//IDX 정렬
	if( $_sort_kind == "no" ) {
		$bk_sort_sql = " BKP_IDX ".$_order_by;
		if($_order_by == 'desc'){
			$sort_no_icon='▲';
		}else{
			$sort_no_icon='▼';
		}
		$sort_kind_icon=''; $sort_loc_icon=''; $sort_cat_icon=''; $sort_in_icon=''; $sort_out_icon=''; $sort_gui_icon='';
	
	// STATUS 수동상태  정렬
	}elseif( $_sort_kind == "kind" ){
		$bk_sort_sql = " BKP_KIND ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_kind_icon='▲';
		}else{
			$sort_kind_icon='▼';
		}
		$sort_no_icon=''; $sort_loc_icon=''; $sort_cat_icon=''; $sort_in_icon=''; $sort_out_icon=''; $sort_gui_icon='';

	// Area 지역 정렬
	}elseif( $_sort_kind == 'loc' ){
		$bk_sort_sql = " BKP_AREA ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_loc_icon='▲';
		}else{
			$sort_loc_icon='▼';
		}
		$sort_kind_icon=''; $sort_no_icon=''; $sort_cat_icon=''; $sort_in_icon=''; $sort_out_icon=''; $sort_gui_icon='';

	// 부킹종류 정렬
	}elseif( $_sort_kind == 'cat' ){
		$bk_sort_sql = " BKP_TYPE ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_cat_icon='▲';
		}else{
			$sort_cat_icon='▼';
		}
		$sort_kind_icon=''; $sort_loc_icon=''; $sort_no_icon=''; $sort_in_icon=''; $sort_out_icon=''; $sort_gui_icon='';

	// 가이드 정렬
	}elseif( $_sort_kind == 'gui' ){
		$bk_sort_sql = " BKP_GUIDE_ID ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_gui_icon='▲';
		}else{
			$sort_gui_icon='▼';
		}
		$sort_kind_icon=''; $sort_loc_icon=''; $sort_cat_icon=''; $sort_in_icon=''; $sort_no_icon='';  $sort_out_icon='';

	// IN 정렬
	}elseif( $_sort_kind == 'in' ){
		$sort_sql = " BKP_START_DATE ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_in_icon='▲';
		}else{
			$sort_in_icon='▼';
		}
		$sort_kind_icon=''; $sort_loc_icon=''; $sort_cat_icon=''; $sort_no_icon=''; $sort_out_icon=''; $sort_gui_icon='';

	// OUT 정렬
	}elseif( $_sort_kind == 'out' ){
		$sort_sql = " BKP_ARRIVE_DATE ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_out_icon='▲';
		}else{
			$sort_out_icon='▼';
		}
		$sort_kind_icon=''; $sort_loc_icon=''; $sort_cat_icon=''; $sort_in_icon=''; $sort_no_icon=''; $sort_gui_icon='';

	}

	if($_calendar_mode == 'on'){

		$_st_date = securityVal($_ca_st_date);
		$_type = securityVal($_ca_type);
		$_kind = securityVal($_ca_kind);
		$_c_mode = securityVal($_ca_c_mode);

		if($_kind == 'day'){

			$st_date = mktime(0,0,0,substr($_st_date, 4, 2),substr($_st_date, 6, 2),substr($_st_date, 0, 4));
			$end_date = mktime(23,59,59,substr($_st_date, 4, 2),substr($_st_date, 6, 2),substr($_st_date, 0, 4));
			if($_c_mode != 'reg'){
				$bk_where_sql = " where BKP_START_DATE >= ".$st_date." and BKP_START_DATE <= ".$end_date." and BKP_TYPE = '".$_type."'";
			}else{
				$bk_where_sql = " where (BKP_BOOKING_DATE >= ".$st_date." and BKP_BOOKING_DATE <= ".$end_date.") or (BKP_RE_DATE >= ".$st_date." and BKP_RE_DATE <= ".$end_date.") or (BKP_BOOKING_MO_DATE >= ".$st_date." and BKP_BOOKING_MO_DATE <= ".$end_date.") or (BKP_MOD_DATE >= ".$st_date." and BKP_MOD_DATE <= ".$end_date.") and BKP_TYPE = '".$_type."'";
			}
		}else{
			$search_et = date("Ymd",$_st_date);
			
			$m_st_date = date("Ymd", strtotime($_st_date." -7 day"));
			$end_date = mktime(23,59,59,substr($_st_date, 4, 2),substr($_st_date, 6, 2),substr($_st_date, 0, 4));
			$st_date = mktime(0,0,0,substr($m_st_date, 4, 2),substr($m_st_date, 6, 2),substr($m_st_date, 0, 4));
			if($_c_mode != 'reg'){
				$bk_where_sql = " where BKP_START_DATE >= ".$st_date." and BKP_START_DATE <= ".$end_date." and BKP_TYPE = '".$_type."'";
			}else{
				$bk_where_sql = " where (BKP_BOOKING_DATE >= ".$st_date." and BKP_BOOKING_DATE <= ".$end_date.") or (BKP_RE_DATE >= ".$st_date." and BKP_RE_DATE <= ".$end_date.") or (BKP_BOOKING_MO_DATE >= ".$st_date." and BKP_BOOKING_MO_DATE <= ".$end_date.") or (BKP_MOD_DATE >= ".$st_date." and BKP_MOD_DATE <= ".$end_date.") and BKP_TYPE = '".$_type."'";
			}
			
		}

		
	}

	$total_count = wepix_counter(_DB_BOOKING, $bk_where_sql);

	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

    $bk_query = "select 
		@ROWNUM := @ROWNUM + 1 AS RNUM, "._DB_BOOKING.".
		* from "._DB_BOOKING."
		,(SELECT @ROWNUM:= 0) R
		".$bk_where_sql." order by ".$bk_sort_sql." limit ".$from_record.", ".$list_num;


    $result = wepix_query_error($bk_query);
    
	$export_file = date("Ymd_Hi",time());
	$export_file = $export_file."_booking.xls";

//header("Content-type: application/vnd.ms-excel;");
//header("Content-Description: PHP5 Generated Data");

header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 
header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
header("Content-Disposition: attachment; filename=\"" . basename($export_file) . "\"" );


?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<table border="1">
<thead>
<tr>
<th class="xl65">No</th>
<th class="xl65">Maching<br style ='mso-data-placement:same-cell;'>Code</th>
<th class="xl65">Area</th>
<th class="xl65">Cate<br style ='mso-data-placement:same-cell;'>gory</th>
<th class="xl65">Status</th>
<th class="xl65">Guide</th>
<th class="xl65">FLIGHT<br style ='mso-data-placement:same-cell;'>IN/OUT</th>
<th class="xl65" style='width:170px;'>HOTEL<br style ='mso-data-placement:same-cell;'>IN/OUT</th>
<th class="xl65">HOTEL</th>
<th class="xl65">Room<br style ='mso-data-placement:same-cell;'>type</th>
<th class="xl65">Bed<br style ='mso-data-placement:same-cell;'>type</th>
<th class="xl65">Room<br style ='mso-data-placement:same-cell;'>Night</th>
<th class="xl65">RooM<br style ='mso-data-placement:same-cell;'>No</th>
<th class="xl65">Adult</th>
<th class="xl65">Child</th>
<th class="xl65">Name(M)</th>
<th class="xl65">Name(F)</th>
<th class="xl65">PassPort No</th>
<th class="xl65">similan</th>
<th class="xl65">Agency</th>
<th class="xl65">Agent</th>
<th class="xl65">Agent<br style ='mso-data-placement:same-cell;'>Confirm</th>
<th class="xl65">Booking Day</th>

<th class="xl65">Change Day</th>
<th class="xl65">Manager</th>
<th class="xl65">Option</th>
<th class="xl65">Remark</th>
<th class="xl65">Tour Cost</th>
</tr>
</thead>
<tbody>
<?
if($ex_mode != 'all'){
$send_array = $_GET['send_array'];
$idx_array = explode(",",$send_array);

for($a=0;$a<count($idx_array);$a++){
    $number = $a+1;
    $query = "select * from ".$db_t_BOOKING_PARENT." where BKP_IDX = ".$idx_array[$a];
    $result = wepix_query_error($query);
    $list = wepix_fetch_array($result);

    $bkp_start_date = date("d-M", $list[BKP_START_DATE]);
    $bkp_arrive_date = date("d-M", $list[BKP_ARRIVE_DATE]);
    $bkp_booking_date = date("d-M-y", $list[BKP_BOOKING_DATE]);
    $_bkp_booking_date_mo = date("d-M-y", $list[BKP_BOOKING_MO_DATE]);
	$bkp_hot_head_count = explode("│",$list[BKP_HOT_HEAD_COUNT]);
	$bkp_hot_head_count_c = explode("│",$list[BKP_HOT_HEAD_COUNT_CHILD]);
	$bkp_passport_num = explode("│",$list[BKP_GUEST_PASSPORT_NUM]); 
	$bkp_similan = explode(",",$list[BKP_SIMILAN]);
 

    if($_bkp_booking_date_mo == '70-01-01'){
        $bkp_booking_date_mo = '';
    }else{
        $bkp_booking_date_mo = date("d-M-y", $list[BKP_BOOKING_MO_DATE]);
    }

    $bkp_hoter_array = explode("│",$list[BKP_HOTEL]);
	$bkp_schedule_day = explode("│",$list[BKP_SCHEDULE_DAY]);
    $bkp_guest_instant = explode("│",$list[BKP_GUEST]);

    $hotel_data = explode("│",$list[BKP_HOTEL]);
    $bkp_hot_kind = explode("│",$list[BKP_HOT_BOOKING_STATE]);

    $bkp_hot_check_in = explode("│",$list[BKP_HOT_CHECK_IN]);
    $bkp_hot_check_out = explode("│",$list[BKP_HOT_CHECK_OUT]);

    $_bkp_hotel_time = explode(":",$bkp_hoter_array[1]);
    
    if(count($bkp_hoter_array) >= 2 && $_bkp_hotel_time[1] != "none"){
		$_bkp_min_check_time = min($bkp_hot_check_in);
		$_bkp_max_check_time = max($bkp_hot_check_out);

        $dend = explode("-",$_bkp_min_check_time);
        $_st_date = mktime(0,0,0,$dend[1],$dend[2],$dend[0]);
        $dend2 = explode("-",$_search_et);
        $_ed_date = mktime(23,59,59,$dend2[1],$dend2[2],$dend2[0]);
		$bkp_min_check_time = date("d-M-y" ,$_st_date);
		$bkp_max_check_time = date("d-M-y",$_ed_date);
    }else{
		$dend = explode("-",$bkp_hot_check_in[0]);
        $_st_date = mktime(0,0,0,$dend[1],$dend[2],$dend[0]);
		$dend2 = explode("-",$bkp_hot_check_out[0]);
        $_ed_date = mktime(23,59,59,$dend2[1],$dend2[2],$dend2[0]);
		$bkp_min_check_time = date("d-M-y" ,$_st_date);
		$bkp_max_check_time = date("d-M-y",$_ed_date);
    }

    $bkp_bed_type = explode("│",$list[BKP_HOT_BED_TYPE]);
    $bkp_hot_option = explode("│",$list[BKP_HOT_ALLIN_YN]);
    $agency = wepix_fetch_array(wepix_query_error("select * from AGENCY  where AG_IDX ='".$list[BKP_AGENCY]."'"));
    $business = wepix_fetch_array(wepix_query_error("select * from AGENCY  where AG_IDX ='".$list[BKP_BUSINESS]."'"));
    
    if($list[BKP_START_FLIGHT2] != ''){
        $bkp_st_flight = $list[BKP_START_FLIGHT2]."/".$list[BKP_START_FLIGHT];
        $bkp_et_flight = $list[BKP_ARRIVE_FLIGHT]."/".$list[BKP_ARRIVE_FLIGHT2];
    }else{
        $bkp_st_flight = $list[BKP_START_FLIGHT];
        $bkp_et_flight = $list[BKP_ARRIVE_FLIGHT];
    }
	$_ary_land_fee_text = explode("│",$list[BKP_LAND_FEE_TEXT]);
	if($list[BKP_LAND_FEE] == 0 || $list[BKP_LAND_FEE] == 1){
		$_view_land_fee = 0;
		for($t=0;$t<count($_ary_land_fee_text);$t++){
			$_ary2_bkp_land_fee_text = explode("/",$_ary_land_fee_text[$t]);

			if($_ary2_bkp_land_fee_text[2] != 0 && $_ary2_bkp_land_fee_text[3] != 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[2] *($_ary2_bkp_land_fee_text[3] * $_ary2_bkp_land_fee_text[1]);
			}elseif($_ary2_bkp_land_fee_text[2] != 0 && $_ary2_bkp_land_fee_text[3] == 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[1] * $_ary2_bkp_land_fee_text[2];
			}elseif($_ary2_bkp_land_fee_text[2] == 0 && $_ary2_bkp_land_fee_text[3] != 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[1] * $_ary2_bkp_land_fee_text[3];
			}
		}
	}else{
		$_view_land_fee = $list[BKP_LAND_FEE];
	}
	$guide_data = wepix_fetch_array(wepix_query_error("select GD_NICK from "._DB_GUIDE." where GD_ID = '".$list[BKP_GUIDE_ID]."' "));
	$guide_nick = $guide_data[GD_NICK];
    
    ?>
<tr bgcolor="<?=$trcolor?>">
<td class="xl65"><?=$number?></td>
<td class="xl65"><?=$list[BKP_MACHING_CODE]?></td>
<td class="xl65"><?=$list[BKP_AREA]?></td>
<td class="xl65"><?=$list[BKP_TYPE]?></td>
<td class="xl65"><?=$list[BKP_KIND]?></td>
<td class="xl65"><?=$guide_nick?></td>
<td class="xl65"><?=strtoupper($bkp_st_flight)?><br style = 'mso-data-placement:same-cell;'>
                 <?=strtoupper($bkp_et_flight)?></td>
<td class="xl65">
<?for($hd=0;$hd<count($bkp_hoter_array);$hd++){
	$_ch_in_time = date("d-M-y" ,strtotime($bkp_hot_check_in[$hd]));
    $_ch_out_time = date("d-M-y",strtotime($bkp_hot_check_out[$hd]));
	if($_ch_in_time!= '01-Jan-70'){
?>
		<?=$_ch_in_time?> ~ <?=$_ch_out_time?> <br style = 'mso-data-placement:same-cell;'>
	<?}?>
<?}?>
</td>
<?		
$_hotel_name='';
$_room_type='';
$_bed_type='';
$_sdn = '';
$_qty = '';
$_head_count ='';
$_head_count_c ='';
for($hc=0;$hc<count($bkp_hoter_array);$hc++){
	$_bkp_hotel_array = explode(":",$bkp_hoter_array[$hc]);    
	$_bkp_schedule_array = explode("/",$bkp_schedule_day[$hc]);


	if($_bkp_hotel_array[1] == "none" || $_bkp_hotel_array[1] == ""){
		$hotel_name ='';
		$room_type ='';
		$bed_type ='';
		$sdn  = '';
		$qty = '';
	}else{
		$hotel_name=$_bkp_hotel_array[1];
		$room_type=$_bkp_hotel_array[3];
		$bed_type=$bkp_bed_type[0];
		$sdn = $_bkp_schedule_array[0]."N";
		$qty = $_bkp_hotel_array[4];
	}


	$_hotel_name .= $hotel_name."<br style='mso-data-placement:same-cell;'>";
	$_room_type .= $room_type."<br style='mso-data-placement:same-cell;'>";
	$_bed_type .= $bed_type."<br style='mso-data-placement:same-cell;'>";
	$_sdn .= $sdn."<br style='mso-data-placement:same-cell;'>";
	$_qty .= $qty."<br style='mso-data-placement:same-cell;'>";
	$_head_count .= $bkp_hot_head_count[$hc]."<br style='mso-data-placement:same-cell;'>";
	$_head_count_c .= $bkp_hot_head_count_c[$hc]."<br style='mso-data-placement:same-cell;'>";
}
?>
<td class="xl65"><?=$_hotel_name?></td>
<td class="xl65"><?=$_room_type?></td>
<td class="xl65"><?=$_bed_type?></td>
<td class="xl65"><?=$_sdn?></td>
<td class="xl65"><?=$_qty?></td>
<td class="xl65"><?=$_head_count?></td>
<td class="xl65"><?=$_head_count_c?></td>
<td class="xl65">
<?for($p=0;$p<count($bkp_guest_instant);$p++){  
 $_bkp_guest_instant = explode("/",$bkp_guest_instant[$p]); 
?>
<?=$_bkp_guest_instant[1]?><br style = 'mso-data-placement:same-cell;'>
<?}?>
</td>
<td class="xl65">
<?for($p=0;$p<count($bkp_guest_instant);$p++){  
 $_bkp_guest_instant = explode("/",$bkp_guest_instant[$p]); 
?>
<?=strtoupper($_bkp_guest_instant[2])?><br style = 'mso-data-placement:same-cell;'>
<?}?>
</td>
<td class="xl65">
<?for($p=0;$p<count($bkp_passport_num);$p++){  ?>
<?=$bkp_passport_num[$p]?><br style = 'mso-data-placement:same-cell;'>
<?}?>
</td>
<td class="xl65">
<?for($p=0;$p<count($bkp_similan);$p++){  
	if($bkp_similan[$p] == 'Y'){?>
	O <br style = 'mso-data-placement:same-cell;'>
<?}elseif($bkp_similan[$p] == 'N'){?>
	X <br style = 'mso-data-placement:same-cell;'>
<?}?>
<?}?>
</td>

<td class="xl65"><?=$agency[AG_COMPANY]?></td>
<td class="xl65"><?=$business[AG_COMPANY]?></td>
<td class="xl65"><?=$list[BKP_AGENCY_CONFIRM_YN]?></td>
<td class="xl65"><?=$bkp_booking_date?></td>

<td class="xl65"><?=$bkp_booking_date_mo?></td>
<td class="xl65"><?=$list[BKP_RESERVER]?></td>
<td class="xl65">
<?

for($ho=0;$ho<count($bkp_hot_option);$ho++){
    $bkp_hot_option_array = explode(",",$bkp_hot_option[$ho]);
    for($h2=0;$h2<count($bkp_hot_option_array);$h2++){
        if($bkp_hot_option_array[$h2] != 'none'){?>
            <?=$bkp_hot_option_array[$h2]?><br style = 'mso-data-placement:same-cell;'>
        <?}?>
    <?}?>
<?}?>

</td>
<td class="xl65"><?=$list[BKP_MEMO]?></td>
<td class="xl65"><?=number_format($_view_land_fee)?></td>
</tr>
<?}?>
<?}else{
    
$number=0;
while($list = wepix_fetch_array($result)){
$number++;
$bkp_start_date = date("d-M", $list[BKP_START_DATE]);
$bkp_arrive_date = date("d-M", $list[BKP_ARRIVE_DATE]);
$bkp_booking_date = date("d-M-y", $list[BKP_BOOKING_DATE]);
$_bkp_booking_date_mo = date("d-M-y", $list[BKP_BOOKING_MO_DATE]);



if($_bkp_booking_date_mo == '70-01-01'){
    $bkp_booking_date_mo = '';
}else{
    $bkp_booking_date_mo = date("d-M-y", $list[BKP_BOOKING_MO_DATE]);
}

$bkp_hoter_array = explode("│",$list[BKP_HOTEL]);
$bkp_schedule_day = explode("│",$list[BKP_SCHEDULE_DAY]);
$bkp_guest_instant = explode("│",$list[BKP_GUEST]);
$hotel_data = explode("│",$list[BKP_HOTEL]);
$bkp_hot_kind = explode("│",$list[BKP_HOT_BOOKING_STATE]);
$bkp_hot_head_count = explode("│",$list[BKP_HOT_HEAD_COUNT]);
$bkp_hot_head_count_c = explode("│",$list[BKP_HOT_HEAD_COUNT_CHILD]);

$bkp_hot_check_in = explode("│",$list[BKP_HOT_CHECK_IN]);
$bkp_hot_check_out = explode("│",$list[BKP_HOT_CHECK_OUT]);

$_bkp_hotel_time = explode(":",$bkp_hoter_array[1]);

if(count($bkp_hoter_array) >= 2 && $_bkp_hotel_time[1] != "none"){
    $_bkp_min_check_time = min($bkp_hot_check_in);
    $_bkp_max_check_time = max($bkp_hot_check_out);
	$bkp_min_check_time = date("dMy" ,strtotime($_bkp_min_check_time));
	$bkp_max_check_time = date("dMy",strtotime($_bkp_max_check_time));
}else{
     $bkp_min_check_time = date("dMy" ,strtotime($bkp_hot_check_in[0]));
	 $bkp_max_check_time = date("dMy",strtotime($bkp_hot_check_out[0]));
}
$bkp_bed_type = explode("│",$list[BKP_HOT_BED_TYPE]);
$bkp_hot_option = explode("│",$list[BKP_HOT_ALLIN_YN]);
$agency = wepix_fetch_array(wepix_query_error("select * from AGENCY  where AG_IDX ='".$list[BKP_AGENCY]."'"));
$business = wepix_fetch_array(wepix_query_error("select * from AGENCY  where AG_IDX ='".$list[BKP_BUSINESS]."'"));

if($list[BKP_START_FLIGHT2] != ''){
    $bkp_st_flight = $list[BKP_START_FLIGHT2]."/".$list[BKP_START_FLIGHT];
    $bkp_et_flight = $list[BKP_ARRIVE_FLIGHT]."/".$list[BKP_ARRIVE_FLIGHT2];
}else{
    $bkp_st_flight = $list[BKP_START_FLIGHT];
    $bkp_et_flight = $list[BKP_ARRIVE_FLIGHT];
}

	$_ary_land_fee_text = explode("│",$list[BKP_LAND_FEE_TEXT]);
	if($list[BKP_LAND_FEE] == 0 || $list[BKP_LAND_FEE] == 1){
		$_view_land_fee = 0;
		for($t=0;$t<count($_ary_land_fee_text);$t++){
			$_ary2_bkp_land_fee_text = explode("/",$_ary_land_fee_text[$t]);

			if($_ary2_bkp_land_fee_text[2] != 0 && $_ary2_bkp_land_fee_text[3] != 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[2] *($_ary2_bkp_land_fee_text[3] * $_ary2_bkp_land_fee_text[1]);
			}elseif($_ary2_bkp_land_fee_text[2] != 0 && $_ary2_bkp_land_fee_text[3] == 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[1] * $_ary2_bkp_land_fee_text[2];
			}elseif($_ary2_bkp_land_fee_text[2] == 0 && $_ary2_bkp_land_fee_text[3] != 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[1] * $_ary2_bkp_land_fee_text[3];
			}
		}
	}else{
		$_view_land_fee = $list[BKP_LAND_FEE];
	}
	$guide_data = wepix_fetch_array(wepix_query_error("select GD_NICK from "._DB_GUIDE." where GD_ID = '".$list[BKP_GUIDE_ID]."' "));
	$guide_nick = $guide_data[GD_NICK];

?>
<tr bgcolor="<?=$trcolor?>">
<td class="xl65"><?=$number?></td>
<td class="xl65"><?=$list[BKP_IDX]?></td>
<td class="xl65"><?=$list[BKP_AREA]?></td>
<td class="xl65"><?=$list[BKP_TYPE]?></td>
<td class="xl65"><?=$list[BKP_KIND]?></td>
<td class="xl65"><?=$guide_nick?></td>
<td class="xl65">20<?=$bkp_min_check_time?> <br style = 'mso-data-placement:same-cell;'>20<?=$bkp_max_check_time?></td>
<td class="xl65"><?=strtoupper($bkp_st_flight)?><br style = 'mso-data-placement:same-cell;'>
             <?=strtoupper($bkp_et_flight)?></td>


<?     
        $_bkp_hotel_array = explode(":",$bkp_hoter_array[0]);    
        $_bkp_schedule_array = explode("/",$bkp_schedule_day[0]);
        $_bkp_hotel_array2 = explode(":",$bkp_hoter_array[1]);    
        $_bkp_schedule_array2 = explode("/",$bkp_schedule_day[1]); 
        if($_bkp_hotel_array[1] == "none" || $_bkp_hotel_array[1] == ""){
            $hotel_name='';
            $room_type='';
            $bed_type='';
            $sdn = '';
            $qty = '';
        }else{
            $hotel_name=$_bkp_hotel_array[1];
            $room_type=$_bkp_hotel_array[3];
            $bed_type = $bkp_bed_type[0];
            $sdn = $_bkp_schedule_array[0];
            $qty = $_bkp_hotel_array[4];
        }

        if($_bkp_hotel_array2[1] == "none" || $_bkp_hotel_array2[1] == ""){
            $hotel_name2 ='';
            $room_type2 ='';
            $bed_type2 ='';
            $sdn2 = '';
            $qty2 = '';
        }else{
            $hotel_name2 =$_bkp_hotel_array2[1];
            $room_type2 =$_bkp_hotel_array2[3];
            $bed_type2 =$bkp_bed_type2[1];
            $sdn2 = $_bkp_schedule_array2[0];
            $qty2 = $_bkp_hotel_array2[4];
        }

		$_hotel_name = $hotel_name."<br  style='mso-data-placement:same-cell;'>".$hotel_name2;
		$_room_type = $room_type."<br style='mso-data-placement:same-cell;'>".$room_type2;
		$_bed_type = $bed_type."<br style='mso-data-placement:same-cell;'>".$bed_type2;
		$_sdn = $sdn."<br style='mso-data-placement:same-cell;'>".$sdn2;
		$_qty = $qty."<br style='mso-data-placement:same-cell;'>".$qty2;
?>
    <td class="xl65"><?=$_hotel_name?></td>
    <td class="xl65"><?=$_room_type?></td>
    <td class="xl65"><?=$_bed_type?></td>
    <td class="xl65"><?=$_sdn?></td>
    <td class="xl65"><?=$_qty?></td>


<?
$guest_p_count = 0;
$guest_c_count = 0;
for($hc=0;$hc<count($bkp_guest_instant);$hc++){  
$guest = explode("/",$bkp_guest_instant[$hc]); 

if($guest[0] == 'Chd' || $guest[0] == 'Inf'){
$guest_c_count++;
}else{
$guest_p_count++;
}

}
?>

<td class="xl65"><?=$_bkp_hot_head_count?></td>
<td class="xl65"></td>
<?for($p=0;$p<2;$p++){  
$_bkp_guest_instant = explode("/",$bkp_guest_instant[$p]); 
?>
<td class="xl65"><?=$_bkp_guest_instant[1]?></td>
<td class="xl65"><?=strtoupper($_bkp_guest_instant[2])?></td>
<td class="xl65"><?=$bkp_passport_num[$p]?></td>
<td class="xl65"><?=$bkp_similan[$p]?></td>
<?}?>

<td class="xl65"><?=$agency[AG_COMPANY]?></td>
<td class="xl65"><?=$business[AG_COMPANY]?></td>
<td class="xl65"><?=$list[BKP_AGENCY_CONFIRM_YN]?></td>
<td class="xl65"><?=$bkp_booking_date?></td>

<td class="xl65"><?=$bkp_booking_date_mo?></td>
<td class="xl65"><?=$list[BKP_RESERVER]?></td>
<td class="xl65">
<?

for($ho=0;$ho<count($bkp_hot_option);$ho++){
$bkp_hot_option_array = explode(",",$bkp_hot_option[$ho]);
for($h2=0;$h2<count($bkp_hot_option_array);$h2++){
    if($bkp_hot_option_array[$h2] != 'none'){?>
        <?=$bkp_hot_option_array[$h2]?><br style = 'mso-data-placement:same-cell;'>
    <?}?>
<?}?>
<?}?>

</td>
<td class="xl65"><?=$list[BKP_MEMO]?></td>
<td class="xl65"><?=number_format($_view_land_fee)?></td>
</tr>
<?}?>


<?}?>
</tbody>
</table> 

</body>
</html>