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

    $ad_mem = wepix_fetch_array(wepix_query_error("select * from ".$db_t_ADMIN_MEMBER." where AD_ID = '".$_ad_id."' "));
    //$query = "select * from ".$db_t_BOOKING_PARENT." ".$search_sql." ".$defult_sql."  order by BKP_IDX desc ";

    $result = wepix_query_error($bk_query);

    
    $now_time = date("d-M-y", $wepix_now_time);
	$export_file = date("Ymd_Hi",time());
	$export_file = $export_file."_apis.xls";


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

<tr><td colspan='11' style='border: 0px solid #444444;'><span style='font-size:14px;'><b>Travel Agent : 더 너바나</b></span></td></tr>
<tr><td colspan='11' style='border: 0px solid #444444;'><span style='font-size:14px;'><b>Address : 경기도 의정부시 경의로 66-1, 정우빌딩 501호</b></span></td></tr>
<tr><td colspan='11' style='border: 0px solid #444444;'><span style='font-size:14px;'><b>Tel   :  010 3769 1614</b></span></td></tr>
<tr><th colspan='11' style='border-top: 5px solid #444444;'><span style='font-size:14px;'><b>PROFORMA INVOICE</b></span></th></tr>
<tr>
<th class="xl65" style='background-color:#DAEEF3;'>No</th>
<th class="xl65" style='background-color:#DAEEF3;'>HOTEL<br style='mso-data-placement:same-cell;'>IN</th>
<th class="xl65" style='background-color:#DAEEF3;'>HOTEL<br style='mso-data-placement:same-cell;'>OUT</th>
<th class="xl65" style='background-color:#DAEEF3;'>HOTEL</th>
<th class="xl65" style='background-color:#DAEEF3;'>Room<br style='mso-data-placement:same-cell;'>Night</th>
<th class="xl65" style='background-color:#DAEEF3;'>Adult</th>
<th class="xl65" style='background-color:#DAEEF3;'>Child</th>
<th class="xl65" style='background-color:#DAEEF3;'>Y.M.D</th>
<th class="xl65" style='background-color:#DAEEF3;'>Name</th>
<th class="xl65" style='background-color:#DAEEF3;'>Tour Cost</th>
<th class="xl65" style='background-color:#DAEEF3;'>환전 요청금액</th>

</tr>
</thead>
<tbody>
<?

if($ex_mode != 'all'){
$send_array = $_GET['send_array'];
$idx_array = explode(",",$send_array);
$number = 0;
for($a=0;$a<count($idx_array);$a++){

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
    $guest_birth = explode("│",$list[BKP_GUEST_BIRTH]);
    $bkp_hot_check_in = explode("│",$list[BKP_HOT_CHECK_IN]);
    $bkp_hot_check_out = explode("│",$list[BKP_HOT_CHECK_OUT]);
    $bkp_hot_state = explode("│",$list[BKP_HOT_BOOKING_STATE]);
    $bkp_hot_head_count = explode("│",$list[BKP_HOT_HEAD_COUNT]);
    
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
for($gut=0;$gut<count($bkp_guest_instant);$gut++){  
  
    $number++;
    $_bkp_hotel_array = explode(":",$bkp_hoter_array[0]);    
    $_bkp_schedule_array = explode("/",$bkp_schedule_day[0]); 
    if($_bkp_hotel_array[1] != "none"){
            $hotel_name=$_bkp_hotel_array[1];
            $room_type=$_bkp_hotel_array[3];
            $bed_type=$bkp_bed_type[$i];
            $sdn = $_bkp_schedule_array[0]."N";
            $qty = $_bkp_hotel_array[4];
            $room_price = $_bkp_schedule_array[1];
            $room_total_price = ($sdn*$room_price)*$qty;

            $_ch_in_time = date("d-M-y" ,strtotime($bkp_hot_check_in[0]));
            $_ch_out_time = date("d-M-y",strtotime($bkp_hot_check_out[0]));
        ?>
<tr bgcolor="<?=$trcolor?>">
<td class="xl65"><?=$number?></td>
<td class="xl65"><?=$_ch_in_time?></td>
<td class="xl65"><?=$_ch_out_time?></td> 
<td class="xl65"><?=$hotel_name?></td>
<td class="xl65"><?=$sdn?></td>
        <?
        $guest_p_count = 0;
        $guest_c_count = 0;
        $guest = explode("/",$bkp_guest_instant[$gut]); 
        if($guest[0] == 'Chd' || $guest[0] == 'Inf'){
            $guest_c_count++;
        }else{
            $guest_p_count++;
        }

        ?>
        <td class="xl65"><?=$bkp_hot_head_count[0]?></td>
        <td class="xl65"><?if($guest_c_count != 0){?><?=$guest_c_count?><?}?></td>
        <?for($p=0;$p<1;$p++){  
            $_bkp_guest_instant = explode("/",$bkp_guest_instant[$p]); 
        ?>
        <td class="xl65"><?=$guest_birth[$gut]?></td>
        <td class="xl65"><?=$guest[1]?></td>
        <td class='xl65'>Tour Cost</td>
        <td></td>
        <?}?>
    <?}?>
<?}
}?>
<?}else{

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
$guest_birth = explode("│",$list[BKP_GUEST_BIRTH]);
$bkp_hot_check_in = explode("│",$list[BKP_HOT_CHECK_IN]);
$bkp_hot_check_out = explode("│",$list[BKP_HOT_CHECK_OUT]);
$bkp_hot_state = explode("│",$list[BKP_HOT_BOOKING_STATE]);
$bkp_hot_head_count = explode("│",$list[BKP_HOT_HEAD_COUNT]);

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
for($gut=0;$gut<count($bkp_guest_instant);$gut++){  

$number++;
$_bkp_hotel_array = explode(":",$bkp_hoter_array[0]);    
$_bkp_schedule_array = explode("/",$bkp_schedule_day[0]); 
if($_bkp_hotel_array[1] != "none"){
        $hotel_name=$_bkp_hotel_array[1];
        $room_type=$_bkp_hotel_array[3];
        $bed_type=$bkp_bed_type[$i];
        $sdn = $_bkp_schedule_array[0]."N";
        $qty = $_bkp_hotel_array[4];
        $room_price = $_bkp_schedule_array[1];
        $room_total_price = ($sdn*$room_price)*$qty;

        $_ch_in_time = date("d-M-y" ,strtotime($bkp_hot_check_in[0]));
        $_ch_out_time = date("d-M-y",strtotime($bkp_hot_check_out[0]));
    ?>
<tr bgcolor="<?=$trcolor?>">
<td class="xl65"><?=$number?></td>
<td class="xl65"><?=$_ch_in_time?></td>
<td class="xl65"><?=$_ch_out_time?></td> 
<td class="xl65"><?=$hotel_name?></td>
<td class="xl65"><?=$sdn?></td>
    <?
    $guest_p_count = 0;
    $guest_c_count = 0;
    $guest = explode("/",$bkp_guest_instant[$gut]); 
    if($guest[0] == 'Chd' || $guest[0] == 'Inf'){
        $guest_c_count++;
    }else{
        $guest_p_count++;
    }

    ?>
    <td class="xl65"><?=$bkp_hot_head_count[0]?></td>
    <td class="xl65"><?if($guest_c_count != 0){?><?=$guest_c_count?><?}?></td>
    <?for($p=0;$p<1;$p++){  
        $_bkp_guest_instant = explode("/",$bkp_guest_instant[$p]); 
    ?>
    <td class="xl65"><?=$guest_birth[$gut]?></td>
    <td class="xl65"><?=$guest[1]?></td>
    <td class='xl65'>Tour Cost</td>
    <td></td>
    <?}?>
<?}?>
<?}
}?>

<?}?>
<tr><th colspan='11' style='border-bottom: 5px solid #444444;'><span style='font-size:14px;'><b>합 계 금 액</b></span></th></tr>
<tr><td colspan='11' style='border: 0px solid #444444;'><span style='font-size:14px;'><b>Payment should be made before the end of the month of presentation of statement </b></span></td></tr>
<tr><td colspan='11' style='border: 0px solid #444444;'><span style='font-size:14px;'><b>Please make your payment by crossed cheque or bank transfer to The Nirvana Co., LTD.</b></span></td></tr>
<tr><td colspan='11' style='border: 0px solid #444444;'><span style='font-size:14px;'><b>Kasikorn Bank PCL., </b></span></td></tr>
<tr><td colspan='11' style='border: 0px solid #444444;'><span style='font-size:14px;'><b>Account No. 008 - 1 - 116377     SWIFT CODE : KASITHBK</b></span></td></tr>
</tbody>
</table> 

</body>
</html>