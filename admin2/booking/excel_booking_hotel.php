<?
	include "../lib/inc_common.php";


	$_search_mode = securityVal($search_mode);
	$_search_kind = securityVal($search_kind); 
	//$_search_st = securityVal($search_st);
	//$_search_et = securityVal($search_et);
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
    
    $now_time = date("d-M-y", $wepix_now_time);
	$export_file = date("Ymd_Hi",time());
	$export_file = $export_file."_hotel.xls";


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


<tr><td colspan='17' style='border: 0px solid #444444;'><span style='font-size:14px;'><b>Dear Reservation,</b></span></td></tr>
<tr><td colspan='17' style='border: 0px solid #444444;'><span style='font-size:14px;'><b>Please make reservations as below.</b></span></td></tr>
<tr>
<th class="xl65">No</th>
<th class="xl65">Maching <br style ='mso-data-placement:same-cell;'> Code</th>
<th class="xl65">Category</th>
<th class="xl65">Status</th>
<th class="xl65">HOTEL<br style='mso-data-placement:same-cell;'>IN/OUT</th>
<th class="xl65">FLIGHT<br style='mso-data-placement:same-cell;'>IN</th>
<th class="xl65">HOTEL</th>
<th class="xl65">Room<br style='mso-data-placement:same-cell;'>type</th>
<th class="xl65">Bed<br style='mso-data-placement:same-cell;'>type</th>
<th class="xl65" style='width:50px;'>Room<br style='mso-data-placement:same-cell;'>Night</th>
<th class="xl65" style='width:50px;'>No of <br style='mso-data-placement:same-cell;'>Rooms</th>
<th class="xl65">Room<br style='mso-data-placement:same-cell;'>Rate</th>
<th class="xl65">Total<br style='mso-data-placement:same-cell;'>Rm Rate</th>
<th class="xl65">Option<br style='mso-data-placement:same-cell;'>Rate</th>
<th class="xl65">Total<br style='mso-data-placement:same-cell;'>Op Rate</th>
<th class="xl65">Total</th>
<th class="xl65">Adult</th>
<th class="xl65">Child</th>
<th class="xl65">Name</th>
<th class="xl65">Confirmation No</th>
<th class="xl65">First<br style='mso-data-placement:same-cell;'>Booking Day</th>
<th class="xl65">Change<br style='mso-data-placement:same-cell;'>Booking Day</th>
<th class="xl65">Etc</th>

</tr>
</thead>
<tbody>
<?
if($ex_mode != 'all'){
$send_array = $_GET['send_array'];
$idx_array = explode(",",$send_array);
$number = 0;
for($a=0;$a<count($idx_array);$a++){

    
//while($list = wepix_fetch_array($result)){
    $query = "select * from ".$db_t_BOOKING_PARENT." where BKP_IDX = ".$idx_array[$a];
    $result = wepix_query_error($query);
    $list = wepix_fetch_array($result);

    $bkp_start_date = date("d-M-y", $list[BKP_START_DATE]);
    $bkp_arrive_date = date("d-M-y", $list[BKP_ARRIVE_DATE]);
    $bkp_booking_date = date("d-M-y", $list[BKP_BOOKING_DATE]);
    


    if($list[BKP_BOOKING_MO_DATE] == '01-Jan-70'){
        $bkp_booking_date_mo = '';
    }else{
        $bkp_booking_date_mo = date("d-M-y", $list[BKP_BOOKING_MO_DATE]);
    }

    $bkp_hoter_array = explode("│",$list[BKP_HOTEL]);
	$bkp_schedule_day = explode("│",$list[BKP_SCHEDULE_DAY]);
    $bkp_guest_instant = explode("│",$list[BKP_GUEST]);
    $hotel_data = explode("│",$list[BKP_HOTEL]);
    $bkp_hot_kind = explode("│",$list[BKP_HOT_BOOKING_STATE]);
    $bkp_hot_kind = explode("│",$list[BKP_HOT_BOOKING_STATE]);

    $bkp_hot_check_in = explode("│",$list[BKP_HOT_CHECK_IN]);
    $bkp_hot_check_out = explode("│",$list[BKP_HOT_CHECK_OUT]);
    $bkp_hot_option = explode("│",$list[BKP_HOT_ALLIN_YN]);
    $bkp_hot_option_price = explode("│",$list[BKP_HOT_ALLIN_PRICE]);
    $bkp_hot_state = explode("│",$list[BKP_HOT_BOOKING_STATE]);
    $bkp_hot_head_count = explode("│",$list[BKP_HOT_HEAD_COUNT]);
	$bkp_hot_head_count_c = explode("│",$list[BKP_HOT_HEAD_COUNT_CHILD]);
	$bkp_hot_guest_birth = explode("│",$list[BKP_GUEST_BIRTH]);
	$bkp_hot_memo = explode("│",$list[BKP_HOT_MEMO]);
    
    $bkp_min_check_time = min($bkp_hot_check_in);
    $bkp_max_check_time = max($bkp_hot_check_out);

    $bkp_bed_type = explode("│",$list[BKP_HOT_BED_TYPE]);
                
    $agency = wepix_fetch_array(wepix_query_error("select * from AGENCY  where AG_IDX ='".$list[BKP_AGENCY]."'"));
    $business = wepix_fetch_array(wepix_query_error("select * from AGENCY  where AG_IDX ='".$list[BKP_BUSINESS]."'"));
    
    if($list[BKP_START_FLIGHT2] != ''){
        $bkp_st_flight = $list[BKP_START_FLIGHT2]."/".$list[BKP_START_FLIGHT];
        $bkp_et_flight = $list[BKP_ARRIVE_FLIGHT]."/".$list[BKP_ARRIVE_FLIGHT2];
    }else{
        $bkp_st_flight = $list[BKP_START_FLIGHT];
        $bkp_et_flight = $list[BKP_ARRIVE_FLIGHT];
    }
    
  for($i=0;$i<count($bkp_hoter_array);$i++){
    $number++;
    $_bkp_hotel_array = explode(":",$bkp_hoter_array[$i]);    
    $_bkp_schedule_array = explode("/",$bkp_schedule_day[$i]); 
    if($_bkp_hotel_array[1] != "none"){
    $hotel_name=$_bkp_hotel_array[1];
    $room_type=$_bkp_hotel_array[3];
    $bed_type=$bkp_bed_type[$i];
    $sdn = $_bkp_schedule_array[0]."N";
	
    $qty = $_bkp_hotel_array[4];
    $room_price = $_bkp_schedule_array[1];
    $room_total_price = ($sdn*$room_price)*$qty;



    $_ch_in_time = date("d-M-y" ,strtotime($bkp_hot_check_in[$i]));
    $_ch_out_time = date("d-M-y",strtotime($bkp_hot_check_out[$i]));
    
        
    
?>
<tr bgcolor="<?=$trcolor?>">
<td class="xl65"><?=$number?></td>
<td class="xl65"><?=$list[BKP_MACHING_CODE]?></td>
<td class="xl65"><?=$bva_tr_booking_type_en[$list[BKP_TYPE]]?></td>
<td class="xl65"><?=$list[BKP_KIND]?></td>
<td class="xl65"><?=$_ch_in_time?> ~ <?=$_ch_out_time?></td>
<td class="xl65"><?=strtoupper($list[BKP_START_FLIGHT])?></td> 

<td class="xl65"><?=$hotel_name?></td>
<td class="xl65"><?=$room_type?></td>
<td class="xl65"><?=$bed_type?></td>
<td class="xl65"><?=$sdn?></td>
<td class="xl65"><?=$qty?></td>
<td class="xl65"><?=number_format($room_price)?> * <?=$sdn?> * <?=$qty?>RM</td>
<td class="xl65"><?=number_format($room_total_price)?></td>
<td class="xl65">
<?
		$bkp_hot_option_array = explode(",",$bkp_hot_option[$i]); 
		$bkp_hot_option_price_array = explode(",",$bkp_hot_option_price[$i]); 
		$_option_price = 0;
		for($ho=0;$ho<count($bkp_hot_option_array);$ho++){
			$_option_price += $bkp_hot_option_price_array[$ho];
			if($bkp_hot_option_array[$ho] != 'none' && $bkp_hot_option_array[$ho] != ''){?>
				<?=$bkp_hot_option_array[$ho]?> @<?=number_format($bkp_hot_option_price_array[$ho])?><br style='mso-data-placement:same-cell;'>
			<?}?>
		<?}?></td>
<td class="xl65"><?=number_format($_option_price)?></td>
<?
	$_hot_price = $room_total_price + $_option_price;
	$_total_room_pice += $room_total_price;
	$_total_option += $_option_price;
	$_total_hot_price += $_hot_price;
?>
<td class="xl65"><?=number_format($_hot_price)?></td>
<td class="xl65"><?=$bkp_hot_head_count[$i]?></td>
<td class="xl65"><?=$bkp_hot_head_count_c[$i]?></td>
<td class="xl65">
	<?
	for($p=0;$p<count($bkp_guest_instant);$p++){  
		$_bkp_guest_instant = explode("/",$bkp_guest_instant[$p]); 

		$_guest_birth_y = substr($bkp_hot_guest_birth[$p], 0, 4);
		$_guest_birth_m = substr($bkp_hot_guest_birth[$p], 4, 2);
		$_guest_birth_d = substr($bkp_hot_guest_birth[$p], 6, 2);
		$_guest_birth = '';	
		if($_guest_birth_y != ''){
			$_guest_birth = "(".$_guest_birth_d.".".$_guest_birth_m.".".$_guest_birth_y.")";
		}
		
	?>
	<?$num_guest_instant = count($bkp_guest_instant)-1 ;?>
		<?=strtoupper($_bkp_guest_instant[2])?> <?=$_guest_birth?><?if($num_guest_instant != $p){?><br style='mso-data-placement:same-cell;'><?}?>
	<?}?>
</td>
<td class="xl65"></td>
<td class="xl65"><?=$bkp_booking_date?></td>
<td class="xl65"><?=$bkp_booking_date_mo?></td>

<td class="xl65"><?=$bkp_hot_memo[$i]?></td>
</tr>
<?}else{
        $number--;
    }
}?>
   <?}?>
<?}else{
 $number = 0;
while($list = wepix_fetch_array($result)){
    
    $bkp_start_date = date("d-M-y", $list[BKP_START_DATE]);
    $bkp_arrive_date = date("d-M-y", $list[BKP_ARRIVE_DATE]);
    $bkp_booking_date = date("d-M-y", $list[BKP_BOOKING_DATE]);
    


    if($list[BKP_BOOKING_MO_DATE] == '01-Jan-70'){
        $bkp_booking_date_mo = '';
    }else{
        $bkp_booking_date_mo = date("d-M-y", $list[BKP_BOOKING_MO_DATE]);
    }

    $bkp_hoter_array = explode("│",$list[BKP_HOTEL]);
	$bkp_schedule_day = explode("│",$list[BKP_SCHEDULE_DAY]);
    $bkp_guest_instant = explode("│",$list[BKP_GUEST]);
    $hotel_data = explode("│",$list[BKP_HOTEL]);
    $bkp_hot_kind = explode("│",$list[BKP_HOT_BOOKING_STATE]);
    $bkp_hot_kind = explode("│",$list[BKP_HOT_BOOKING_STATE]);

    $bkp_hot_check_in = explode("│",$list[BKP_HOT_CHECK_IN]);
    $bkp_hot_check_out = explode("│",$list[BKP_HOT_CHECK_OUT]);
    $bkp_hot_option = explode("│",$list[BKP_HOT_ALLIN_YN]);
    $bkp_hot_option_price = explode("│",$list[BKP_HOT_ALLIN_PRICE]);
    $bkp_hot_state = explode("│",$list[BKP_HOT_BOOKING_STATE]);
    $bkp_hot_head_count = explode("│",$list[BKP_HOT_HEAD_COUNT]);
	$bkp_hot_head_count_c = explode("│",$list[BKP_HOT_HEAD_COUNT_CHILD]);
	$bkp_hot_guest_birth = explode("│",$list[BKP_GUEST_BIRTH]);
	$bkp_hot_memo = explode("│",$list[BKP_HOT_MEMO]);
    
    $bkp_min_check_time = min($bkp_hot_check_in);
    $bkp_max_check_time = max($bkp_hot_check_out);

    $bkp_bed_type = explode("│",$list[BKP_HOT_BED_TYPE]);
                
    $agency = wepix_fetch_array(wepix_query_error("select * from AGENCY  where AG_IDX ='".$list[BKP_AGENCY]."'"));
    $business = wepix_fetch_array(wepix_query_error("select * from AGENCY  where AG_IDX ='".$list[BKP_BUSINESS]."'"));
    
    if($list[BKP_START_FLIGHT2] != ''){
        $bkp_st_flight = $list[BKP_START_FLIGHT2]."/".$list[BKP_START_FLIGHT];
        $bkp_et_flight = $list[BKP_ARRIVE_FLIGHT]."/".$list[BKP_ARRIVE_FLIGHT2];
    }else{
        $bkp_st_flight = $list[BKP_START_FLIGHT];
        $bkp_et_flight = $list[BKP_ARRIVE_FLIGHT];
    }
    
  for($i=0;$i<count($bkp_hoter_array);$i++){
    $number++;
    $_bkp_hotel_array = explode(":",$bkp_hoter_array[$i]);    
    $_bkp_schedule_array = explode("/",$bkp_schedule_day[$i]); 
    if($_bkp_hotel_array[1] != "none"){
    $hotel_name=$_bkp_hotel_array[1];
    $room_type=$_bkp_hotel_array[3];
    $bed_type=$bkp_bed_type[$i];
    $sdn = $_bkp_schedule_array[0]."N";
	
    $qty = $_bkp_hotel_array[4];
    $room_price = $_bkp_schedule_array[1];
    $room_total_price = ($sdn*$room_price)*$qty;



    $_ch_in_time = date("d-M-y" ,strtotime($bkp_hot_check_in[$i]));
    $_ch_out_time = date("d-M-y",strtotime($bkp_hot_check_out[$i]));
    
        
    
?>
<tr bgcolor="<?=$trcolor?>">
<td class="xl65"><?=$number?></td>
<td class="xl65"><?=$list[BKP_MACHING_CODE]?></td>
<td class="xl65"><?=$bva_tr_booking_type_en[$list[BKP_TYPE]]?></td>
<td class="xl65"><?=$list[BKP_KIND]?></td>
<td class="xl65"><?=$_ch_in_time?> ~ <?=$_ch_out_time?></td>
<td class="xl65"><?=strtoupper($list[BKP_START_FLIGHT])?></td> 

<td class="xl65"><?=$hotel_name?></td>
<td class="xl65"><?=$room_type?></td>
<td class="xl65"><?=$bed_type?></td>
<td class="xl65"><?=$sdn?></td>
<td class="xl65"><?=$qty?></td>
<td class="xl65"><?=number_format($room_price)?> * <?=$sdn?> * <?=$qty?>RM</td>
<td class="xl65"><?=number_format($room_total_price)?></td>
<td class="xl65">
<?
		$bkp_hot_option_array = explode(",",$bkp_hot_option[$i]); 
		$bkp_hot_option_price_array = explode(",",$bkp_hot_option_price[$i]); 
		$_option_price = 0;
		for($ho=0;$ho<count($bkp_hot_option_array);$ho++){
			$_option_price += $bkp_hot_option_price_array[$ho];
			if($bkp_hot_option_array[$ho] != 'none' && $bkp_hot_option_array[$ho] != ''){?>
				<?=$bkp_hot_option_array[$ho]?> @<?=number_format($bkp_hot_option_price_array[$ho])?><br style='mso-data-placement:same-cell;'>
			<?}?>
		<?}?></td>
<td class="xl65"><?=number_format($_option_price)?></td>
<?
	$_hot_price = $room_total_price + $_option_price;
	$_total_room_pice += $room_total_price;
	$_total_option += $_option_price;
	$_total_hot_price += $_hot_price;
?>
<td class="xl65"><?=number_format($_hot_price)?></td>
<td class="xl65"><?=$bkp_hot_head_count[$i]?></td>
<td class="xl65"><?=$bkp_hot_head_count_c[$i]?></td>
<td class="xl65">
	<?
	for($p=0;$p<count($bkp_guest_instant);$p++){  
		$_bkp_guest_instant = explode("/",$bkp_guest_instant[$p]); 

		$_guest_birth_y = substr($bkp_hot_guest_birth[$p], 0, 4);
		$_guest_birth_m = substr($bkp_hot_guest_birth[$p], 4, 2);
		$_guest_birth_d = substr($bkp_hot_guest_birth[$p], 6, 2);
		
		if($_guest_birth_y != ''){
			$_guest_birth = "(".$_guest_birth_d.$_guest_birth_m.$_guest_birth_y.")";
		}

	?>
		<?$num_guest_instant = count($bkp_guest_instant)-1 ;?>
		<?=strtoupper($_bkp_guest_instant[2])?> <?=$_guest_birth?> <?if($num_guest_instant != $p){?><br style='mso-data-placement:same-cell;'><?}?>
	<?}?>
</td>
<td class="xl65"></td>
<td class="xl65"><?=$bkp_booking_date?></td>
<td class="xl65"><?=$bkp_booking_date_mo?></td>

<td class="xl65"><?=$bkp_hot_memo[$i]?></td>

</tr>
    <?}else{
        $number--;
      }
    }?>
    <?}?>

<?}?>
<? $ad_mem = wepix_fetch_array(wepix_query_error("select * from "._DB_ADMIN." where AD_ID = '".$_ad_id."' "));?>

<tr><td colspan='17' style='border: 0px solid #444444; '><span style='font-size:14px;'><b>Looking forward to your reply.</b></span></td></tr>
<tr><td colspan='17' style='border: 0px solid #444444; margin-top:5px;'><span style='font-size:14px;'><b>Thank you,</b></span></td></tr>
<tr><td colspan='17' style='border: 0px solid #444444; margin-top:5px;'><span style='font-size:14px;'><b>Best & Regards,</b></span></td></tr>
<tr><td colspan='17' style='border: 0px solid #444444; margin-top:5px;'><span style='font-size:14px;'><b><?=$ad_mem[AD_NAME_EG]?> (<?=$ad_mem[AD_NICK]?>)</b></span></td></tr>
<tr><td colspan='17' style='border: 0px solid #444444; margin-top:5px;'><span style='font-size:12px;'>Operation & Reservation Manager</span></td></tr>
<tr><td colspan='17' style='border: 0px solid #444444; margin-top:5px;'><span style='font-size:14px;'><b> THE NIRVANA</b></span></td></tr>
<tr><td colspan='17' style='border: 0px solid #444444; margin-top:5px;'><span style='font-size:12px;'>67/149  Moo5. Kukkak, Takuapa Phang Nga, Thailand, 82190 </span></td></tr>
<tr><td colspan='17' style='border: 0px solid #444444; margin-top:5px;'><span style='font-size:12px;'>Mobile :  <?=$ad_mem[AD_PHONE]?>  Tel : 076-410-540-1 Fax : 076-486450</span></td></tr>
<tr><th colspan='17' style='border: 0px solid #444444;  height:250px;'> <img src='http://nirvana.wepix-hosting.co.kr/admin/img/nirvana_logo.png'></th></tr>
</tbody>
</table> 

</body>
</html>