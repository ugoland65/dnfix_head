<?
include "../lib/inc_common.php";
include('../../class/image.php'); 


	$_action_mode = securityVal($a_mode);
	$_key = securityVal($key);

	$a_mode = $_POST['action_mode'];
	$_hotel_name = $_POST['hotelName'];
	$_hotel_view = $_POST['hotelView'];
	$_mokey = $_POST['key'];
	$_bkp_use_Id = $_POST['bkpUseId'];
    $page_set = $_POST['page_set'];
	$_bkp_kind =  cleanVariable($_POST['bkp_kind']);
	$_bkp_area =  cleanVariable($_POST['bkp_area']);
	$_bkp_type =  cleanVariable($_POST['bkp_type']);
	$_bkp_guide_id =  cleanVariable($_POST['bkp_guide_id']);
	$_date_start =  cleanVariable($_POST['date_start']);
    $_date_end =  cleanVariable($_POST['date_end']);
    $_date_start2 =  cleanVariable($_POST['date_start2']);
	$_date_end2 =  cleanVariable($_POST['date_end2']);
	$_bkp_start_flight =  cleanVariable($_POST['bkp_start_flight']);
    $_bkp_arrive_flight =  cleanVariable($_POST['bkp_arrive_flight']);
    $_bkp_start_flight2 =  cleanVariable($_POST['bkp_start_flight2']);
    $_bkp_arrive_flight2 =  cleanVariable($_POST['bkp_arrive_flight2']);
    $bkp_optionN = $_POST['bkp_option'];
    $_bkp_ft_yn = $_POST['ft_yn'];
    $_booking_date =  cleanVariable($_POST['booking_date']);
    $_booking_date2 =  cleanVariable($_POST['booking_date2']);
	$_booking_mo_date =  cleanVariable($_POST['booking_mo_date']);
    $_bkp_agency =  cleanVariable($_POST['ageny_id']);
    $bkp_agency_1 = cleanVariable($_POST['bkp_agency_1']);
    $bkp_agency_2 = cleanVariable($_POST['bkp_agency_2']);
  
    $_bkp_agency_1 = wepix_fetch_array(wepix_query_error("select AG_IDX from AGENCY where AG_KIND = 'A' and AG_COMPANY='".$bkp_agency_1."'"));
    $_bkp_agency_2 = wepix_fetch_array(wepix_query_error("select AG_IDX from AGENCY where AG_KIND = 'B' and AG_COMPANY='".$bkp_agency_2."'"));
 
    $_bkp_memo =  cleanVariable($_POST['bkp_memo']);
    $_memo_admin =  cleanVariable($_POST['bkp_memo_admin']);
    
	$_bkp_money_first = cleanVariable($_POST['bkp_money_first']);
	$_bkp_money_now = cleanVariable($_POST['bkp_money_now']);
	$_bkp_land_fee =(int)str_replace(',','',$_POST['bkp_landfee']);
	$_bkp_minus_system = cleanVariable($_POST['bkp_minus_system']);
    $_bkp_payment_method = cleanVariable($_POST['bkp_payment_method']);
    $_bkp_business = cleanVariable($_POST['bkp_business']);
    
	$_bkp_money_first = (int)str_replace(',','',$_bkp_money_first); //베케이션 머니

    $_agency_confirm_yn =$_POST['agency_confirm_yn'];
	$_bkp_hot_check_in = $_POST['bkp_hot_check_in'];
	$_bkp_hot_check_out = $_POST['bkp_hot_check_out'];
    $_hotelN = $_POST['hotelN'];
    $bed_type  = $_POST['bed_type'];
    $hotel_chbox = $_POST['hotel_chbox'];
	$_sdN = $_POST['sdN'];
    $_rtN = $_POST['rtN'];
    $hot_qtyN = $_POST['hot_qty'];

    $hot_head = $_POST['hot_head'];
	$hot_head_c = $_POST['hot_head_c'];
	$_priceN = $_POST['priceN'];

	

    $_htkindN = $_POST['htkindN'];
    
    $_ges_kind =  $_POST['ges_kind'];
	$_ges_ko =  $_POST['ges_ko'];
	$_ges_en =  $_POST['ges_en'];
    $_ges_id  =  $_POST['ges_Id'];
    $ft_yn_kind_y =  $_POST['ft_yn_kind_y'];
    $ft_yn_kind_n =  $_POST['ft_yn_kind_n'];

    
    $ges_age  =  $_POST['ges_age'];
    $ges_birth  =  $_POST['ges_birth'];
    $ges_passport_num  =  $_POST['ges_passport_num'];
    $ges_passport_date  =  $_POST['ges_passport_date'];

    $_ges_age= implode("│",$ges_age);
	$_ges_birth_y = securityVal($ges_birth_y);
	$_ges_birth_m = securityVal($ges_birth_m);
	$_ges_birth_d = securityVal($ges_birth_d);
	for($birth=0;$birth<count($_ges_birth_y);$birth++){
		$ges_birth[] = $_ges_birth_y[$birth].$_ges_birth_m[$birth].$_ges_birth_d[$birth];
		
	}
	
	
    $_ges_birth= implode("│",$ges_birth);
    $_ges_passport_num= implode("│",$ges_passport_num);
    $_ges_passport_date = implode("│",$ges_passport_date);
	$_bkp_room_number = implode("│",$roomNum);
	$bkp_similan_ck = securityVal($bkp_similan_ck);
	//echo $bkp_similan_ck;


    $golf_name =  $_POST['golf_name'];
    $caddie_yn = $_POST['caddie_yn'];
    $t_up_time =  $_POST['t_up_time'];
    $t_up_date=  $_POST['t_up_date'];
	$gf_haed_ct =  $_POST['gf_haed_ct'];
    $holl_count  =  $_POST['holl_count'];
    $gf_am_pm =  $_POST['gf_am_pm'];
    $gf_cart =  $_POST['gf_cart'];

    $va_no =  $_POST['va_no'];
    $flight_detail =  $_POST['flight_detail'];
    $contract_person =  $_POST['contract_person'];
    $remark =  $_POST['remark'];

    $mo_golf =  $_POST['mo_golf'];
    $mo_golf_name =  $_POST['mo_golf_name'];
    $mo_caddie_yn = $_POST['mo_caddie_yn'];
    $mo_t_up_time =  $_POST['mo_t_up_time'];
    $mo_t_up_date=  $_POST['mo_t_up_date'];
	$mo_gf_haed_ct =  $_POST['mo_gf_haed_ct'];
    $mo_holl_count  =  $_POST['mo_holl_count'];
    
    $mo_gf_am_pm =  $_POST['mo_gf_am_pm'];
    $mo_gf_cart =  $_POST['mo_gf_cart'];
    
    $dstart = explode("-",$_date_start);
    $dend = explode("-",$_date_end);
    $dstart2 = explode("-",$_date_start2);
    $dend2 = explode("-",$_date_end2);
    
	$_bkp_start_date = mktime(0,0,0,$dstart[1],$dstart[2],$dstart[0]);
    $_bkp_arrive_date = mktime(23,59,59,$dend[1],$dend[2],$dend[0]);
    $_bkp_start_date2 = mktime(0,0,0,$dstart2[1],$dstart2[2],$dstart2[0]);
    $_bkp_arrive_date2 = mktime(23,59,59,$dend2[1],$dend2[2],$dend2[0]);


	$booking_date_array = explode("-",$_booking_date);
    $_bkp_booking_date = mktime(0,0,0,$booking_date_array[1],$booking_date_array[2],$booking_date_array[0]);
    $booking_date_array2 = explode("-",$_booking_date2);
    $_bkp_booking_date2 = mktime(0,0,0,$booking_date_array2[1],$booking_date_array2[2],$booking_date_array2[0]);

	$booking_mo_date_array = explode("-",$_booking_mo_date);
	$_bkp_booking_mo_date = mktime(0,0,0,$booking_mo_date_array[1],$booking_mo_date_array[2],$booking_mo_date_array[0]);

	$hot_total_price =0;

    $_bkp_optionN = implode(",", $bkp_optionN);

   
    $next_bkp_idx = wepix_fetch_array(wepix_query_error("select BKP_IDX from "._DB_BOOKING_PARENT." order by BKP_IDX desc limit 1 "));
    $next_idx = $next_bkp_idx[BKP_IDX]+1;
    $randomNum = mt_rand(10, 99);
    
   

    $hot_total_price = array();

	$bkp_landfee_name  =  $_POST['bkp_landfee_name'];
	$bkp_landfee  =  $_POST['bkp_landfee'];
	$bkp_landfee_people  =  $_POST['bkp_landfee_people'];
	$bkp_landfee_sn  =  $_POST['bkp_landfee_sn'];

	$_view_land_fee = 0;
	for( $j =0; $j<count($bkp_landfee_name); $j++ ){
		$landfee_array[] = $bkp_landfee_name[$j]."/".$bkp_landfee[$j]."/".$bkp_landfee_people[$j]."/".$bkp_landfee_sn[$j];

		if($bkp_landfee_people[$j] != 0 && $bkp_landfee_sn[$j] != 0){
			$_view_land_fee += $bkp_landfee_people[$j] *($bkp_landfee_sn[$j] * $bkp_landfee[$j]);
		}elseif($bkp_landfee_people[$j] != 0 && $bkp_landfee_sn[$j] == 0){
			$_view_land_fee += $bkp_landfee[$j] * $bkp_landfee_people[$j];
		}elseif($bkp_landfee_people[$j] == 0 && $bkp_landfee_sn[$j] != 0){
			$_view_land_fee += $bkp_landfee[$j] * $bkp_landfee_sn[$j];
		}
	}


	

		


	for( $j =0; $j<count($_hotelN); $j++ ){
		if($_action_mode == "bookingNew"){
			$box_num = $j+2;
		}else{
			$box_num = $j;
		}

		$chk_in[] = $_bkp_hot_check_in[$j];
        $chk_out[] =$_bkp_hot_check_out[$j];
        $_bed_type[]  = $bed_type[$j];

	

        $_hotel_chbox[] = implode(",",$_POST['hotel_chbox_'.$box_num]);
        $hotel_chbox_price[] = implode(",",$_POST['hotel_chbox_price_'.$box_num]);

		$hotel_chbox_price2 = explode(",",$hotel_chbox_price[$j]);
        $hot_op_price = 0;
        for($p=0;$p<count($hotel_chbox_price2);$p++){
         $hot_op_price += $hotel_chbox_price2[$p];
        }
        $hot_price = (int)str_replace(',','',$_priceN[$j]);
     
       
       

        $hot_total_price[] = (($hot_price *$_sdN[$j])*$hot_qtyN[$j]) + $hot_op_price; 

       if($_hotelN[$j] != '0:none'){
		$hotel_array[] = $_hotelN[$j].":".$_rtN[$j].":".$hot_qtyN[$j];
        $day_money_array[] = $_sdN[$j]."/".(int)str_replace(',','',$_priceN[$j]);
	   }
        $hot_data = wepix_fetch_array(wepix_query_error("select HOT_NAME from ".$db_t_HOTEL_DB." where HOT_IDX = '".$_hotelN[$j]."' "));
        
        $hotel_array_text = $hot_data[HOT_NAME].":".$_rtN[$j];
    }

     $ag_data = wepix_fetch_array(wepix_query_error("select AG_COMPANY from ".$db_t_AGENCY." where AG_IDX = '".$_bkp_agency_1[AG_IDX]."' "));
     $ag_data2 = wepix_fetch_array(wepix_query_error("select AG_COMPANY from ".$db_t_AGENCY." where AG_IDX = '".$_bkp_agency_2[AG_IDX]."' "));
     $_bkp_agncy_text = $ag_data[AG_COMPANY]."-".$ag_data2[AG_COMPANY];

    $_chk_in= implode("│",$chk_in);
    $_chk_out= implode("│",$chk_out);
    $_bed_type_array  = implode("│",$_bed_type);


    
    $_hotel_chbox_array = implode("│",$_hotel_chbox);
    $_hotel_chbox_price_array = implode("│",$hotel_chbox_price);

	

	$bkp_hotel_conf_num = securityVal($bkp_hotel_conf_num);
	$bkp_hotel_memo = securityVal($bkp_hotel_memo);

	for( $j =0; $j<count($hot_option_rate); $j++ ){
		$_hot_total_price_array[] = $hot_option_rate[$j]."/".$hot_room_rate[$j]."/".$hot_total_price[$j];
	}

	


    $_hot_total_price = implode("│",$_hot_total_price_array);

    $_bkp_hoter = implode("│",$hotel_array);
    $_bkp_landfee_text = implode("│",$landfee_array);
	
    $_bkp_hotel_text =implode("│",$hotel_array_text);
	$_bkp_schedule_day= implode("│",$day_money_array);
    $_bkp_hotel_kind= implode("│",$_htkindN);
    $_bkp_head_count= implode("│",$hot_head);
    $_bkp_head_count_c= implode("│",$hot_head_c);

	$_bkp_hotel_conf_num= implode("│",$bkp_hotel_conf_num);
    $_bkp_hotel_memo= implode("│",$bkp_hotel_memo);
    


    
    $bkp_now_time = $wepix_now_time;

        if(count($_ges_ko) == 2){
            $_bkp_team_name = $_ges_ko[0].",".$_ges_ko[1];
        }elseif(count($_ges_ko) > 2){
            $count_subtraction = count($_ges_ko) - 2;
            $_bkp_team_name = $_ges_ko[0].",".$_ges_ko[1]." 외".$count_subtraction."명";
        }elseif(count($_ges_ko) == 1){
            $_bkp_team_name = $_ges_ko[0];
        }

	for( $i=0; $i<count($_ges_kind); $i++ ){
        $guest_array[] = $_ges_kind[$i]."/".$_ges_ko[$i]."/".$_ges_en[$i]."/".$_ges_id[$i];
        if($_ges_ko[$i] != ''){
            $ges_ko_name = trim($_ges_ko[$i]);
            $double_booking_query .= " and BKP_GUEST like '%".$ges_ko_name ."%' ";
        }else{
            $double_booking_query .= " and BKP_GUEST like 'none' ";
        }
        
	}

	

    $_bkp_guest_instant = implode("│",$guest_array);

// ******************************************************************************************************************
// 부킹 등록
// ******************************************************************************************************************
if( $_action_mode == "bookingNew" ){
	$bkp_maching_code = $_bkp_agency_1[AG_IDX].$randomNum.$next_idx;
	$data2_count = wepix_counter(_DB_BOOKING_PARENT,'where 1=1 '.$double_booking_query);
    
    if($data2_count != 0){
        echo "<script type='text/javascript'>";
        echo "if(confirm('같은 손님이름 의 부킹이 ".$data2_count."건 존재합니다. 부킹을 계속 등록 하시겠습니까?') == false){";
        echo "location.href='/admin/booking/booking_form.php?mode=new';";
        echo "}else{";
        echo "}";
        echo "</script>";
        $bkp_data2 = wepix_query_error("select * from "._DB_BOOKING_PARENT." where 1=1 ".$double_booking_query."");
        
        while($data2_list = wepix_fetch_array($bkp_data2)){
            if($data2_list[BKP_KIND] != 'CANCEL'){
                $query = "update  "._DB_BOOKING_PARENT."  set 
                    BKP_KIND = 'DUPE'
                where BKP_IDX = '".$data2_list[BKP_IDX]."'";
            $result = wepix_query_error($query);
            $_bkp_kind = "DUPE";
            }
        }
        
       
    }

    $query = "insert into "._DB_BOOKING_PARENT." set
		BKP_KIND = '".$_bkp_kind."',
		BKP_TYPE = '".$_bkp_type."',
		BKP_AREA = '".$_bkp_area."',
		BKP_STATE = '0',
		BKP_STATE_DATE = '".$wepix_now_time."',
		BKP_START_DATE = '".$_bkp_start_date."',
        BKP_ARRIVE_DATE = '".$_bkp_arrive_date."',
		BKP_SIMILAN = '".$bkp_similan_ck."' ,
        BKP_START_DATE2 = '".$_bkp_start_date2."',
		BKP_ARRIVE_DATE2 = '".$_bkp_arrive_date2."',
		BKP_START_FLIGHT = '".$_bkp_start_flight."',
        BKP_ARRIVE_FLIGHT = '".$_bkp_arrive_flight."',
        BKP_START_FLIGHT2 = '".$_bkp_start_flight2."',
		BKP_ARRIVE_FLIGHT2 = '".$_bkp_arrive_flight2."',
		BKP_BOOKING_DATE = '".$_bkp_booking_date."',
        BKP_BOOKING_MO_DATE = '".$wepix_now_time."',
        BKP_OPTON = '".$_bkp_ft_yn."', 
        BKP_TEAM_NAME = '".$_bkp_team_name."',
		BKP_HOT_CHECK_IN = '".$_chk_in."',
        BKP_HOT_CHECK_OUT = '".$_chk_out."',
        BKP_HOT_BED_TYPE = '".$_bed_type_array."',
        BKP_MACHING_CODE = '".$bkp_maching_code."',
        BKP_HOT_HEAD_COUNT = '".$_bkp_head_count."',
		BKP_ROOM_NUMBER = '".$_bkp_room_number."',
        BKP_HOT_ALLIN_YN = '".$_hotel_chbox_array."',
        BKP_HOT_ALLIN_PRICE = '".$_hotel_chbox_price_array."',
		BKP_HOTEL = '".$_bkp_hoter."',
		BKP_HOT_TOTAL_PRICE = '".$_hot_total_price."',
		BKP_SCHEDULE_DAY = '".$_bkp_schedule_day."',
		BKP_HOT_BOOKING_STATE = '".$_bkp_hotel_kind."',
        BKP_GUEST = '".$_bkp_guest_instant."',
		BKP_HOT_HEAD_COUNT_CHILD = '".$_bkp_head_count_c."',
		BKP_HOT_CONFIRM_NUM = '".$_bkp_hotel_conf_num."',
		BKP_HOT_MEMO = '".$_bkp_hotel_memo."',  

        BKP_GUEST_AGE = '".$_ges_age."',
        BKP_GUEST_BIRTH = '".$_ges_birth."',
        BKP_GUEST_PASSPORT_NUM = '".$_ges_passport_num."',
        BKP_GUEST_PASSPORT_DATE = '".$_ges_passport_date."',
        BKP_AGENCY_CONFIRM_YN = 'N',
        BKP_MEMO = '".$_bkp_memo."',
        BKP_MOD_LOG = '".$_bkp_memo_admin."',
        BKP_MEMO_ADMIN = '".$_memo_admin."',
		BKP_AGENCY = '".$_bkp_agency_1[AG_IDX]."',
		BKP_FIRST_MONEY = '".$_bkp_money_first."',
		BKP_NOW_MONEY = '".$_bkp_money_first."',
		BKP_LAND_FEE = '".$_view_land_fee."',
		BKP_LAND_FEE_TEXT = '".$_bkp_landfee_text."',
		BKP_MINUS_SYSTEM = 'hand',
		BKP_PAYMENT_METHOD ='Y',
        BKP_REQ_ID = '".$ad_id."',
        BKP_BUSINESS = '".$_bkp_agency_2[AG_IDX]."',
        BKP_AGNCY_TEXT = '".$_bkp_agncy_text."',
        BKP_RESERVER = '".$_ad_name."',
        BKP_HOTEL_TEXT = '".$_bkp_hotel_text."',
		BKP_REQ_DATE = '".$bkp_now_time."' ";
   $result = wepix_query_error($query);



   
   
    
    $bkp_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING_PARENT." where BKP_REQ_DATE='".$bkp_now_time."'"));
    $bkp_guest =  explode("│",$bkp_data[BKP_GUEST]);

    $hotel_data = explode("│",$_bkp_hoter);
    $bkp_hot_check_in = explode("│",$_chk_in);
    $bkp_hot_check_out = explode("│",$_chk_out);
    $schedule_day_array = explode("│",$_bkp_schedule_day);
        

           
            for( $i=0; $i<count($hotel_data); $i++ ){
              $hotel_data2 = explode(":",$hotel_data[$i]);
              $schedule_day = explode("/", $schedule_day_array[$i]);
              $hotel_in_date_array = explode("-",$bkp_hot_check_in[$i]);
              $_bkp_hotel_in_date = mktime(0,0,0,$hotel_in_date_array[1],$hotel_in_date_array[2],$hotel_in_date_array[0]);
              $hotel_out_date_array = explode("-",$bkp_hot_check_out[$i]);
              $_bkp_hotel_out_date = mktime(23,59,59,$hotel_out_date_array[1],$hotel_out_date_array[2],$hotel_out_date_array[0]);
              $sth_n = $schedule_day[0] * $hotel_data2[4];
              if($hotel_data2[1] != 'none'){
                $query = "insert into ".$db_t_STATISTICS_HOTEL." set
                        STH_NAME = '".$hotel_data2[1]."',
                        STH_BKP_IDX = '".$bkp_data[BKP_IDX]."',
                        STH_BKP_KIND = '".$bkp_data[BKP_KIND]."',
                        STH_HOT_IDX = '".$hotel_data2[0]."',
                        STH_IN = '".$_bkp_hotel_in_date."',
                        STH_OUT = '".$_bkp_hotel_out_date."',
                        STH_N = '".$sth_n."'";
                wepix_query_error($query);
                }
             
            }
        

    if($_bkp_type == 'GF'){
		for($i=0;$i<count($golf_name);$i++){
            $golf_data = wepix_fetch_array(wepix_query_error("select * from "._DB_GOLF." where GF_IDX=".$golf_name[$i]." "));

            $add_num = $i+2;
            $add_holl =  $_POST['add_holl_'.$add_num];
            $coupon  =  $_POST['coupon_'.$add_num];
            $caddie_yn = $_POST['mo_caddie_yn_'.$add_num];

            if($coupon == 'N'){
                 if($holl_count[$i] == '9'){
                    $green_fee = $golf_data[GF_GREEN_9_FEE] * $gf_haed_ct[$i];
                }elseif($holl_count[$i] == '18'){
                    $green_fee = $golf_data[GF_GREEN_18_FEE] * $gf_haed_ct[$i];
                }elseif($holl_count[$i] == '36'){
                    $green_fee = $golf_data[GF_GREEN_36_FEE] * $gf_haed_ct[$i];
                }
            }else{
                if($coupon == '9'){
                    $green_fee = $golf_data[GF_GREEN_9_COUPON_FEE] * $gf_haed_ct[$i];
                }elseif($coupon == '18'){
                    $green_fee = $golf_data[GF_GREEN_18_COUPON_FEE] * $gf_haed_ct[$i];
                }elseif($coupon == 'Set'){
                    $green_fee = $golf_data[GF_GREEN_set_COUPON_FEE] * $gf_haed_ct[$i];
                }
            }   

            if($gf_cart[$i] == 'double'){
                $rest = $gf_haed_ct[$i]%2;
                $process = $gf_haed_ct[$i]/2;
                $cart_fee = ($golf_data[GF_DOUBLE_CART_FEE] * $process) + ($golf_data[GF_CART_FEE] * $rest);
            }elseif($gf_cart[$i] == 'single'){
                $cart_fee = $golf_data[GF_CART_FEE] * $gf_haed_ct[$i];
            }
            
            if($caddie_yn == 'Y'){
                $caddie_fee =  $golf_data[GF_CADDIE_FEE] * $gf_haed_ct[$i];
            }elseif($caddie_yn == 'N'){
                $caddie_fee =  0;
            }

            if($coupon != 'Set'){
                $golf_total_price = $green_fee + $caddie_fee + $cart_fee;
            }else{
                $golf_total_price = $green_fee;
            }
            
            $query = "insert into "._DB_BOOKING_GOLF." set
                BG_BKP_IDX = '".$bkp_data[BKP_IDX]."',
                BG_GF_IDX = '".$golf_name[$i]."',
                BG_ST_TIME = '".$t_up_time[$i]."',
                BG_ST_DATE = '".$t_up_date[$i]."',
                BG_NAME = '".$golf_data[GF_NAME]."',
                BG_HOLL_ADD_YN = '".$add_holl."',
                BG_COUPON_YN = '".$coupon."',
                BG_TOTAL_PRICE = '".$golf_total_price."',
                BG_HEAD_CT = '".$gf_haed_ct[$i]."',
                BG_CADDIE_YN = '".$caddie_yn."',
                BG_CART = '".$gf_cart[$i]."',
                BG_HOLL_CT = '".$holl_count[$i]."',
                BG_TIME = '".$gf_am_pm[$i]."',
                BG_REQ_DATE = '".$wepix_now_time."',
                BG_REQ_ID = '".$ad_id."'";
            $result = wepix_query_error($query);

			//### 부킹 수배서
			if($_FILES['upload']['name']){
				// 설정
				$uploads_dir = '../../data/booking/wanted';
				$allowed_ext = array('jpg','jpeg','png','gif');
				 
				// 변수 정리
				$error = $_FILES['upload']['error'];
				$name = $_FILES['upload']['name'];
				
				$ext = array_pop(explode('.', $name));
				$data_name = "wanted_".$_mokey."_".$wepix_now_time.".".$ext;
				// 오류 확인
				if( $error != UPLOAD_ERR_OK ) {
					switch( $error ) {
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							echo "파일이 너무 큽니다. ($error)";
							break;
						case UPLOAD_ERR_NO_FILE:
							echo "파일이 첨부되지 않았습니다. ($error)";
							break;
						default:
							echo "파일이 제대로 업로드되지 않았습니다. ($error)";
					}
					exit;
				}
				 
				// 파일 이동
				move_uploaded_file( $_FILES['upload']['tmp_name'], "$uploads_dir/$data_name");

				echo "<h2>파일 정보</h2>
				<ul>
					<li>파일명: $name</li>
					<li>확장자: $ext</li>
					<li>파일형식: {$_FILES['myfile']['type']}</li>
					<li>파일크기: {$_FILES['myfile']['size']} 바이트</li>
				</ul>";

				$query = "insert into "._DB_WANTED." set
						WP_BKP_MACHING_CODE = '".$modify_maching_code."',
						WP_IMG_DATA = '".$data_name."',
						WP_KIND = 'WANTED',
						WP_REG_ID = '".$_ad_id."',
						WP_REG_DATE = '".$wepix_now_time."'";
				wepix_query_error($query);
			}

			if($_FILES['upload2']['name']){

				
				// 설정
				$uploads_dir = '../../data/booking/confirm';
				$allowed_ext = array('jpg','jpeg','png','gif');
				 
				// 변수 정리
				$error = $_FILES['upload2']['error'];
				$name = $_FILES['upload2']['name'];
				$ext = array_pop(explode('.', $name));
			    $data_name = "confirm_".$_mokey."_".$wepix_now_time.".".$ext;
				// 오류 확인
				if( $error != UPLOAD_ERR_OK ) {
					switch( $error ) {
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							echo "파일이 너무 큽니다. ($error)";
							break;
						case UPLOAD_ERR_NO_FILE:
							echo "파일이 첨부되지 않았습니다. ($error)";
							break;
						default:
							echo "파일이 제대로 업로드되지 않았습니다. ($error)";
					}
					exit;
				}
				 
				// 파일 이동
				move_uploaded_file( $_FILES['upload2']['tmp_name'], "$uploads_dir/$data_name");


				$query = "insert into "._DB_WANTED." set
						WP_BKP_MACHING_CODE = '".$bkp_maching_code."',
						WP_IMG_DATA = '".$data_name."',
						WP_KIND = 'CONFIRM',
						WP_REG_ID = '".$_ad_id."',
						WP_REG_DATE = '".$wepix_now_time."'";
				wepix_query_error($query);
			}

            
            $bk_golf_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING_GOLF." where BG_REQ_DATE='".$bkp_now_time."' and BG_ST_DATE = '".$t_up_date[$i]."'"));
            if($coupon != 'N'){
                for($go_cp=0;$go_cp<$gf_haed_ct[$i];$go_cp++){
                    $query = "update ".$db_t_COUPON_DB." set
                                CP_YN	 = 'Y',
                                CP_DATE  = '".$wepix_now_time."',
                                CP_BKP_IDX = '".$bkp_data[BKP_IDX]."',
                                CP_COMMON_IDX = '".$bk_golf_data[BG_IDX]."'
                        where CP_CODE ='".$golf_data[GF_COUPON_CODE]."' and CP_YN = 'N'  ORDER BY CP_NUM ASC limit 1";
                    $result = wepix_query_error($query);

                }
            }

        }
      
    }

	


	msg("등록완료!","booking_list.php?today=on");

	


// ******************************************************************************************************************
// 부킹 수정
// ******************************************************************************************************************
}elseif($_action_mode=="bookingModify"){


    $bkp_data = wepix_fetch_array(wepix_query_error("select * from ".$db_t_BOOKING_PARENT." where BKP_IDX='".$_mokey."'"));
    
    if($_bkp_type == 'GF'){
        
        for($i=0;$i<count($mo_golf);$i++){
            $mo_golf_data = wepix_fetch_array(wepix_query_error("select * from ".$db_t_GOLF_DB." where GF_IDX=".$mo_golf_name[$i]." "));
            $add_num = $i;
            $mo_add_holl =  $_POST['mo_add_holl_'.$add_num];
            $caddie_yn = $_POST['mo_caddie_yn_'.$add_num];
            $coupon  =  $_POST['mo_coupon_'.$add_num];
      

            if($coupon == 'N'){
                if($mo_holl_count[$i] == '9'){
                   $green_fee = $mo_golf_data[GF_GREEN_9_FEE] * $mo_gf_haed_ct[$i];
               }elseif($mo_holl_count[$i] == '18'){
                   $green_fee = $mo_golf_data[GF_GREEN_18_FEE] * $mo_gf_haed_ct[$i];
               }elseif($mo_holl_count[$i] == '36'){
                   $green_fee = $mo_golf_data[GF_GREEN_36_FEE] * $mo_gf_haed_ct[$i];
               }
             }else{
               if($coupon == '9'){
                   $green_fee = $mo_golf_data[GF_GREEN_9_COUPON_FEE] * $mo_gf_haed_ct[$i];
               }elseif($coupon == '18'){
                   $green_fee = $mo_golf_data[GF_GREEN_18_COUPON_FEE] * $mo_gf_haed_ct[$i];
               }elseif($coupon == 'Set'){
                   $green_fee = $mo_golf_data[GF_GREEN_SET_COUPON_FEE] * $mo_gf_haed_ct[$i];
               }
            }   
                

            if($mo_gf_cart[$i] == 'double'){
                $rest = $mo_gf_haed_ct[$i]%2;
                $process = $mo_gf_haed_ct[$i]/2;
                $cart_fee = ($mo_golf_data[GF_DOUBLE_CART_FEE] * $process) + ($mo_golf_data[GF_CART_FEE] * $rest);
            }elseif($mo_gf_cart[$i] == 'single'){
                $cart_fee = $mo_golf_data[GF_CART_FEE] * $mo_gf_haed_ct[$i];
            }
            
            if($caddie_yn == 'Y'){
                $caddie_fee =  $mo_golf_data[GF_CADDIE_FEE] * $mo_gf_haed_ct[$i];
            }elseif($caddie_yn == 'N'){
                $caddie_fee =  0;
            }
            
            if($coupon != 'Set'){
                $golf_total_price = $green_fee + $caddie_fee + $cart_fee;
            }else{
                $golf_total_price = $green_fee;
            }
          
            $query = " update "._DB_BOOKING_GOLF." set
                BG_GF_IDX = '".$mo_golf_name[$i]."',
                BG_ST_TIME = '".$mo_t_up_time[$i]."',
                BG_ST_DATE = '".$mo_t_up_date[$i]."',
                BG_NAME = '".$mo_golf_data[GF_NAME]."',
                BG_HOLL_ADD_YN = '".$mo_add_holl."',
                BG_COUPON_YN = '".$coupon."',
                BG_TOTAL_PRICE = '".$golf_total_price."',
                BG_HEAD_CT = '".$mo_gf_haed_ct[$i]."',
                BG_CADDIE_YN = '".$caddie_yn."',
                BG_CART = '".$mo_gf_cart[$i]."',
                BG_HOLL_CT = '".$mo_holl_count[$i]."',
                BG_TIME = '".$mo_gf_am_pm[$i]."',
                BG_MOD_DATE = '".$wepix_now_time."',
                BG_MOD_ID = '".$_ad_id."' 
                where BG_IDX = ".$mo_golf[$i]."";
            $result = wepix_query_error($query);
          
        }
       
        
        for($i=0;$i<count($golf_name);$i++){
            $golf_data = wepix_fetch_array(wepix_query_error("select * from ".$db_t_GOLF_DB." where GF_IDX=".$golf_name[$i]." "));

            $add_num = $i+2;
            $add_holl =  $_POST['add_holl_'.$add_num];
            $coupon  =  $_POST['coupon_'.$add_num];
            $caddie_yn = $_POST['mo_caddie_yn_'.$add_num];

            if($coupon == 'N'){
                if($holl_count[$i] == '9'){
                   $green_fee = $golf_data[GF_GREEN_9_FEE] * $gf_haed_ct[$i];
               }elseif($holl_count[$i] == '18'){
                   $green_fee = $golf_data[GF_GREEN_18_FEE] * $gf_haed_ct[$i];
               }elseif($holl_count[$i] == '36'){
                   $green_fee = $golf_data[GF_GREEN_36_FEE] * $gf_haed_ct[$i];
               }
             }else{
               if($coupon == '9'){
                   $green_fee = $golf_data[GF_GREEN_9_COUPON_FEE] * $gf_haed_ct[$i];
               }elseif($coupon == '18'){
                   $green_fee = $golf_data[GF_GREEN_18_COUPON_FEE] * $gf_haed_ct[$i];
               }elseif($coupon == 'Set'){
                   $green_fee = $golf_data[GF_GREEN_set_COUPON_FEE] * $gf_haed_ct[$i];
               }
            }


            if($gf_cart[$i] == 'double'){
                $rest = $gf_haed_ct[$i]%2;
                $process = $gf_haed_ct[$i]/2;
                $cart_fee = ($golf_data[GF_DOUBLE_CART_FEE] * $process) + ($golf_data[GF_CART_FEE] * $rest);
            }elseif($gf_cart[$i] == 'single'){
                $cart_fee = $golf_data[GF_CART_FEE] * $gf_haed_ct[$i];
            }
            if($caddie_yn == 'Y'){
                $caddie_fee =  $golf_data[GF_CADDIE_FEE] * $gf_haed_ct[$i];
            }elseif($caddie_yn == 'N'){
                $caddie_fee =  0;
            }
            if($coupon != 'Set'){
                $golf_total_price = $green_fee + $caddie_fee + $cart_fee;
            }else{
                $golf_total_price = $green_fee;
            }
            
            
            $query = "insert into "._DB_BOOKING_GOLF." set
                    BG_BKP_IDX = '".$_mokey."',
                    BG_GF_IDX = '".$golf_name[$i]."',
                    BG_ST_TIME = '".$t_up_time[$i]."',
                    BG_ST_DATE = '".$t_up_date[$i]."',
                    BG_NAME = '".$golf_data[GF_NAME]."',
                    BG_HOLL_ADD_YN = '".$add_holl."',
                    BG_CADDIE_YN = '".$caddie_yn."',
                    BG_HEAD_CT = '".$gf_haed_ct[$i]."',
                    BG_HOLL_CT = '".$holl_count[$i]."',
                    BG_TIME = '".$gf_am_pm[$i]."',
                    BG_REQ_DATE = '".$wepix_now_time."',
                    BG_REQ_ID = '".$ad_id."'";
           $result = wepix_query_error($query);

            $bk_golf_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING_GOLF." where BG_REQ_DATE='".$bkp_now_time."' and BG_ST_DATE = '".$t_up_date[$i]."'"));
            if($coupon != 'N'){
                for($go_cp=0;$go_cp<$gf_haed_ct[$i];$go_cp++){
                    $query = "update ".$db_t_COUPON_DB." set
                                CP_YN	 = 'Y',
                                CP_DATE  = '".$wepix_now_time."',
                                CP_BKP_IDX = '".$_mokey."',
                                CP_COMMON_IDX = '".$bk_golf_data[BG_IDX]."'
                        where CP_CODE ='".$golf_data[GF_COUPON_CODE]."' and CP_YN = 'N'  ORDER BY CP_NUM ASC limit 1";
                    $result = wepix_query_error($query);

                }
            }
        }

    }
        if(count($va_no) != 0){
            for($ft=0;$ft<count($va_no);$ft++){
                if($ft_yn_kind_y == 'on' && $ft_yn_kind_y == 'on'){
                    $flight_kind = 'ALL';
                    $ft_date = $_bkp_start_date.":".$_bkp_arrive_date;
                }elseif($ft_yn_kind_y == 'on'){
                    $flight_kind = 'IN';
                    $ft_date = $_bkp_start_date;
                }elseif($ft_yn_kind_n == 'on'){
                    $flight_kind = 'OUT';
                    $ft_date = $_bkp_arrive_date;
                }

                $_flight_detail = $_bkp_start_flight."(".$flight_detail.") - ".$_bkp_arrive_flight."(".$contract_person.")";
                $person_count = count($_ges_en)-1;
                $_contract_person = $_ges_en[0]." 외 ".$person_count."명";

				 $query = "insert into ".$db_t_FAST_TRACK." set
					FT_BKP_IDX = '".$_mokey."',
					FT_NO = '".$va_no[$ft]."',
					FT_FLIGHT_DETAIL = '".$_flight_detail."',
					FT_FLIGHT_KIND = '".$flight_kind."',
					FT_SERVICE_DATE = '".$_bkp_start_date."',
					FT_FLIGHT_IN = '".$_bkp_start_flight."',
					FT_FLIGHT_OUT = '".$_bkp_arrive_flight."',
					FT_CONTRACT_PERSON = '".$_contract_person."',
					FT_NAME = '".$_ges_kind[$ft].". ".$_ges_en[$ft]."',
					FT_REMARK = '".$remark."',
					FT_REQ_DATE = '".$wepix_now_time."',
					FT_REQ_ID = '".$ad_id."'";
				$result = wepix_query_error($query);
				$bkp_start_date = date("y-m-d", $data[BKP_START_DATE]);
            }
        }

        $query = "delete from ".$db_t_STATISTICS_HOTEL." where	STH_BKP_IDX =".$_mokey;
        wepix_query_error($query);

        $hotel_data = explode("│",$_bkp_hoter);
        $bkp_hot_check_in = explode("│",$_chk_in);
        $bkp_hot_check_out = explode("│",$_chk_out);
        $schedule_day_array = explode("│",$_bkp_schedule_day);
        
    
               
		for( $i=0; $i<count($hotel_data); $i++ ){
		  $hotel_data2 = explode(":",$hotel_data[$i]);
		  $schedule_day = explode("/", $schedule_day_array[$i]);
		  $hotel_in_date_array = explode("-",$bkp_hot_check_in[$i]);
		  $_bkp_hotel_in_date = mktime(0,0,0,$hotel_in_date_array[1],$hotel_in_date_array[2],$hotel_in_date_array[0]);
		  $hotel_out_date_array = explode("-",$bkp_hot_check_out[$i]);
		  $_bkp_hotel_out_date = mktime(23,59,59,$hotel_out_date_array[1],$hotel_out_date_array[2],$hotel_out_date_array[0]);
		  $sth_n = $schedule_day[0] * $hotel_data2[4];
			
	   

		  if($hotel_data2[1] != 'none'){
			$query = "insert into ".$db_t_STATISTICS_HOTEL." set
					STH_NAME = '".$hotel_data2[1]."',
					STH_BKP_IDX = '".$_mokey."',
					STH_HOT_IDX = '".$hotel_data2[0]."',
					STH_IN = '".$_bkp_hotel_in_date."',
					STH_OUT = '".$_bkp_hotel_out_date."',
					STH_N = '".$sth_n."'";
			wepix_query_error($query);
			}
		 
		}
                

      
        //echo $_bkp_memo_admin;
       
        if($_bkp_kind == 'CANCEL'){
          //  $_bkp_re_date = $wepix_now_time;
            $_bkp_hotel_kind_ct = explode("│",$bkp_data[BKP_HOT_BOOKING_STATE]);
            for($ct=0;$ct<count($_bkp_hotel_kind_ct);$ct++){
                $_bkp_hotel_kind_ct[$ct] = '3';
            }
            $_bkp_hotel_kind= implode("│",$_bkp_hotel_kind_ct);
            
            
        }else{
            $_bkp_hotel_kind = $bkp_data[BKP_HOT_BOOKING_STATE];
        }

		$_bkp_memo_admin = $bkp_data[BKP_MOD_LOG];
        $_bkp_re_date = 0;

        $bkp_hot_check_in = explode("│",$bkp_data[BKP_HOT_CHECK_IN]);
		$bkp_hot_check_out = explode("│",$bkp_data[BKP_HOT_CHECK_OUT]);
        $bkp_hoter_array = explode("│",$bkp_data[BKP_HOTEL]);
        $bkp_schedule_day = explode("│",$bkp_data[BKP_SCHEDULE_DAY]);
        $bkp_allin = explode("│",$bkp_data[BKP_HOT_ALLIN_YN]);
        $bkp_allin_price = explode("│",$bkp_data[BKP_HOT_ALLIN_PRICE]);
        $bkp_bed_type = explode("│",$bkp_data[BKP_HOT_BED_TYPE]);
        $_bkp_hot_head_count = explode("│",$bkp_data[BKP_HOT_HEAD_COUNT]);

        $bkp_guest_instant = explode("│",$bkp_data[BKP_GUEST]);
		$bkp_guest_age = explode("│",$bkp_data[BKP_GUEST_AGE]);
		$bkp_guest_birth = explode("│",$bkp_data[BKP_GUEST_BIRTH]);
		$bkp_guest_passport_num = explode("│",$bkp_data[BKP_GUEST_PASSPORT_NUM]);
		$bkp_guest_passport_date = explode("│",$bkp_data[BKP_GUEST_PASSPORT_DATE]);
        
        if($_bkp_start_date != $bkp_data[BKP_START_DATE]){
            $start_date = date("d-M-y", $_bkp_start_date);
            $start_date2 = date("d-M-y", $bkp_data[BKP_START_DATE]);

            $_bkp_memo_admin .= " IN : ".$start_date2." -> ".$start_date."".nl2br("\n");
         }
        if($_bkp_start_date2 != $bkp_data[BKP_START_DATE2]){
            $start_date = date("d-M-y", $_bkp_start_date2);
            $start_date2 = date("d-M-y", $bkp_data[BKP_START_DATE2]);
            $_bkp_memo_admin .= " 경유IN : ".$start_date2." -> ".$start_date."".nl2br("\n");
        }
        if($_bkp_arrive_date != $bkp_data[BKP_ARRIVE_DATE]){
            $end_date = date("d-M-y", $_bkp_arrive_date);
            $end_date2 = date("d-M-y", $bkp_data[BKP_ARRIVE_DATE]);

            $_bkp_memo_admin .= " OUT : ".$end_date2." -> ".$end_date."".nl2br("\n");
        }
        if($_bkp_arrive_date2 != $bkp_data[BKP_ARRIVE_DATE2]){
            $end_date = date("d-M-y", $_bkp_arrive_date2);
            $end_date2 = date("d-M-y", $bkp_data[BKP_ARRIVE_DATE2]);
            $_bkp_memo_admin .= " 경유OUT : ".$end_date2." -> ".$end_date."".nl2br("\n");
        }
        if($_bkp_start_flight != $bkp_data[BKP_START_FLIGHT]){
            $_bkp_memo_admin .= " 항공IN : ".$bkp_data[BKP_START_FLIGHT]." -> ".$_bkp_start_flight."".nl2br("\n");
        }
        if($_bkp_start_flight2 != $bkp_data[BKP_START_FLIGHT2]){
            $_bkp_memo_admin .= " 경유한공IN : ".$bkp_data[BKP_START_FLIGHT2]." -> ".$_bkp_start_flight2."".nl2br("\n");
        }
        if($_bkp_arrive_flight != $bkp_data[BKP_ARRIVE_FLIGHT]){
            $_bkp_memo_admin .= " 한공OUT : ".$bkp_data[BKP_ARRIVE_FLIGHT]." -> ".$_bkp_arrive_flight."".nl2br("\n");
         }
        if($_bkp_arrive_flight2 != $bkp_data[BKP_ARRIVE_FLIGHT2]){
            $_bkp_memo_admin .= " 경유한공OUT : ".$bkp_data[BKP_ARRIVE_FLIGHT2]." -> ".$_bkp_arrive_flight2."".nl2br("\n");
        }
        if($_bkp_agency_1[AG_IDX] != $bkp_data[BKP_AGENCY]){
            $_bkp_memo_admin .= " 에이전시 : ".$bkp_data[AG_COMPANY]." -> ".$_bkp_agency_1[AG_IDX]."".nl2br("\n");
        }

  
        for($a=0;$a<count($hotelN);$a++){
            $htinfo   = explode(":",$bkp_hoter_array[$a]);
            $hitnfo2 = explode("/",$bkp_schedule_day[$a]);
            $bkp_allin_op = explode(",",$bkp_allin[$a]);
            $bkp_allin_ob = explode(",",$_hotel_chbox[$a]);
            $bkp_allin_op_price = explode(",",$bkp_allin_price[$a]);
            $bkp_allin_ob_price = explode(",",$hotel_chbox_price[$a]);
            
            if($_bkp_hot_check_in[$a] != $bkp_hot_check_in[$a]){
                $_bkp_memo_admin .= " 체크IN : ".$bkp_hot_check_in[$a]." -> ".$_bkp_hot_check_in[$a]."".nl2br("\n");
                $_bkp_re_date = $wepix_now_time;
             }
            if($_bkp_hot_check_out[$a] != $bkp_hot_check_out[$a]){
                $_bkp_memo_admin .= " 체크OUT : ".$bkp_hot_check_out[$a]." -> ".$_bkp_hot_check_out[$a]."".nl2br("\n");
                $_bkp_re_date = $wepix_now_time;
            }
            if($_hotelN[$a] != $htinfo[0].":".$htinfo[1]){
                $_hotel = explode(":",$_hotelN[$a]);
                $_bkp_memo_admin .= " 호텔 : ".$htinfo[1]." -> ".$_hotel[1]."".nl2br("\n");
                $_bkp_re_date = $wepix_now_time;
            }
            if($_rtN[$a] != $htinfo[2].":".$htinfo[3]){
                $rtN = explode(":",$_rtN[$a]);
                $_bkp_memo_admin .= " 룸타입 : ".$htinfo[3]." -> ".$rtN[1]."".nl2br("\n");
                $_bkp_re_date = $wepix_now_time;
             }
            if($_sdN[$a] != $hitnfo2[0]){
                $_bkp_memo_admin .= " 숙박일 : ".$hitnfo2[0]." -> ".$_sdN[$a]."".nl2br("\n");
                $_bkp_re_date = $wepix_now_time;
            }
            if($_bkp_hot_head_count[$a] != $bkp_hot_head_count2[$a]){
                  $_bkp_memo_admin .= " 인원수 : ".$_bkp_hot_head_count[$a]." -> ".$_bkp_hot_head_count[$a]."".nl2br("\n");
                  $_bkp_re_date = $wepix_now_time;
                  //echo   " 옵션 : ".$htinfo[4]." -> ".$hot_qtyN[$a];
            }
			if($hot_qtyN[$a] != $htinfo[4]){
                if($htinfo[4] != '' && $hot_qtyN[$a] != 'none'){
                  $_bkp_memo_admin .= " 객실수  : ".$htinfo[4]." -> ".$hot_qtyN[$a]."".nl2br("\n");
                  $_bkp_re_date = $wepix_now_time;
                  //echo   " 옵션 : ".$htinfo[4]." -> ".$hot_qtyN[$a];
                }
            }
            if((int)str_replace(',','',$_priceN[$a]) != $hitnfo2[1]){
                $_bkp_memo_admin .= " 객실가격 : ".$hitnfo2[1]." -> ".$_priceN[$a]."".nl2br("\n");
				$_bkp_re_date = $wepix_now_time;
            }
            
            for($hot_op=0;$hot_op<count($bkp_allin_op);$hot_op++){

                    if($bkp_allin_ob[$hot_op] != $bkp_allin_op[$hot_op]){
                        $_bkp_memo_admin .= " 옵션 : ".$bkp_allin_op[$hot_op]." -> ".$bkp_allin_ob[$hot_op]."".nl2br("\n");
                    }
                    if($bkp_allin_ob_price[$hot_op] != $bkp_allin_op_price[$hot_op]){
                         $_bkp_memo_admin .= " 옵션가격 : ".$bkp_allin_op_price[$hot_op]." -> ".$bkp_allin_ob_price[$hot_op]."".nl2br("\n");
                    }
            }
                       
        }
        if($_bkp_guest_instant != $bkp_data[BKP_GUEST]){
            $_bkp_memo_admin .= " 게스트 : ".$bkp_data[BKP_GUEST]." -> ".$_bkp_guest_instant."".nl2br("\n");
         //   $_bkp_re_date = $wepix_now_time;
          }
          if($_ges_age != $bkp_data[BKP_GUEST_AGE]){
            $_bkp_memo_admin .= " 게스트나이 : ".$bkp_data[BKP_GUEST_AGE]." -> ".$_ges_age."".nl2br("\n");
        //    $_bkp_re_date = $wepix_now_time;
          }
          if($_ges_birth != $bkp_data[BKP_GUEST_BIRTH]){
            $_bkp_memo_admin .= " 게스트 생년월일 : ".$bkp_data[BKP_GUEST_BIRTH]." -> ".$_ges_birth."".nl2br("\n");
         //   $_bkp_re_date = $wepix_now_time;
          }
          if($_ges_passport_num != $bkp_data[BKP_GUEST_PASSPORT_NUM]){
            $_bkp_memo_admin .= " 여권번호 : ".$bkp_data[BKP_GUEST_PASSPORT_NUM]." -> ".$_ges_passport_num."".nl2br("\n");
        //    $_bkp_re_date = $wepix_now_time;
          }
          if($_ges_passport_date != $bkp_data[BKP_GUEST_PASSPORT_DATE]){
            $_bkp_memo_admin .= " 여권만료기간 : ".$bkp_data[BKP_GUEST_PASSPORT_DATE]." -> ".$_ges_passport_date."".nl2br("\n");
        //    $_bkp_re_date = $wepix_now_time;
          }

        
        $memo_date = date("y년m월d일 H시", $wepix_now_time);
        $_bkp_memo_admin .= "---------------------- 수정시간 : ".$memo_date."/ 수정 관리자 : ".$ad_id." -----------------------------".nl2br("\n");
   
    
		$query = "update  "._DB_BOOKING_PARENT."  set 
			BKP_KIND = '".$_bkp_kind."',
			BKP_TYPE = '".$_bkp_type."',
			BKP_AREA = '".$_bkp_area."',
			BKP_START_DATE = '".$_bkp_start_date."',
            BKP_ARRIVE_DATE = '".$_bkp_arrive_date."',
            BKP_START_DATE2 = '".$_bkp_start_date2."',
            BKP_ARRIVE_DATE2 = '".$_bkp_arrive_date2."',
            BKP_START_FLIGHT = '".$_bkp_start_flight."',
            BKP_START_FLIGHT2 = '".$_bkp_start_flight2."',
		    BKP_ARRIVE_FLIGHT2 = '".$_bkp_arrive_flight2."',
			BKP_ARRIVE_FLIGHT = '".$_bkp_arrive_flight."',
			BKP_TEAM_NAME = '".$_bkp_team_name."',
			BKP_BOOKING_DATE = '".$_bkp_booking_date."',
            BKP_BOOKING_MO_DATE = '".$wepix_now_time."',
            BKP_HOT_BED_TYPE = '".$_bed_type_array."',
            BKP_HOT_ALLIN_YN = '".$_hotel_chbox_array."',
			BKP_ROOM_NUMBER = '".$_bkp_room_number."',
            BKP_HOT_ALLIN_PRICE = '".$_hotel_chbox_price_array."',
			BKP_HOT_CHECK_IN = '".$_chk_in."',
            BKP_HOT_CHECK_OUT = '".$_chk_out."',
            BKP_HOT_BOOKING_STATE = '".$_bkp_hotel_kind."',
            BKP_HOT_HEAD_COUNT = '".$_bkp_head_count."',
            BKP_OPTON = '".$_bkp_ft_yn."', 
			BKP_HOTEL = '".$_bkp_hoter."',
			BKP_HOT_TOTAL_PRICE = '".$_hot_total_price."',
			BKP_SCHEDULE_DAY = '".$_bkp_schedule_day."',
            BKP_GUEST = '".$_bkp_guest_instant."',
            BKP_GUEST_AGE = '".$_ges_age."',
            BKP_GUEST_BIRTH = '".$_ges_birth."',
            BKP_GUEST_PASSPORT_NUM = '".$_ges_passport_num."',
            BKP_GUEST_PASSPORT_DATE = '".$_ges_passport_date."',
            BKP_MEMO = '".$_bkp_memo."',
            BKP_MOD_LOG = '".$_bkp_memo_admin."',
            BKP_MEMO_ADMIN = '".$_memo_admin."',
			BKP_SIMILAN = '".$bkp_similan_ck."' ,
            BKP_AGENCY = '".$_bkp_agency_1[AG_IDX]."',
            BKP_FIRST_MONEY = '".$_bkp_money_first."',
            BKP_BUSINESS = '".$_bkp_agency_2[AG_IDX]."',
            BKP_AGNCY_TEXT = '".$_bkp_agncy_text."',
            BKP_HOTEL_TEXT = '".$_bkp_hotel_text."',

			BKP_HOT_HEAD_COUNT_CHILD = '".$_bkp_head_count_c."',
			BKP_HOT_CONFIRM_NUM = '".$_bkp_hotel_conf_num."',
			BKP_HOT_MEMO = '".$_bkp_hotel_memo."',            


			BKP_LAND_FEE = '".$_bkp_land_fee."',
			BKP_LAND_FEE_TEXT = '".$_bkp_landfee_text."',
			BKP_MOD_ID = '".$ad_id."',
            BKP_MOD_DATE = '".$wepix_now_time."',
            BKP_RE_DATE = '".$_bkp_re_date."'
		where BKP_IDX = '".$_mokey."'";
		$result = wepix_query_error($query);


			//### 부킹 수배서
			if($_FILES['upload']['name']){
				// 설정
				$uploads_dir = '../../data/booking/wanted';
				$allowed_ext = array('jpg','jpeg','png','gif');
				 
				// 변수 정리
				$error = $_FILES['upload']['error'];
				$name = $_FILES['upload']['name'];
				$ext = array_pop(explode('.', $name));
				$data_name = "wanted_".$_mokey."_".$wepix_now_time.".".$ext;
				// 오류 확인
				if( $error != UPLOAD_ERR_OK ) {
					switch( $error ) {
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							echo "파일이 너무 큽니다. ($error)";
							break;
						case UPLOAD_ERR_NO_FILE:
							echo "파일이 첨부되지 않았습니다. ($error)";
							break;
						default:
							echo "파일이 제대로 업로드되지 않았습니다. ($error)";
					}
					exit;
				}
				 
				// 파일 이동
				move_uploaded_file( $_FILES['upload']['tmp_name'], "$uploads_dir/$data_name");


				$query = "insert into "._DB_WANTED." set
						WP_BKP_MACHING_CODE = '".$modify_maching_code."',
						WP_IMG_DATA = '".$data_name."',
						WP_KIND = 'WANTED',
						WP_REG_ID = '".$_ad_id."',
						WP_REG_DATE = '".$wepix_now_time."'";
				wepix_query_error($query);
			}

			if($_FILES['upload2']['name']){

				
				// 설정
				$uploads_dir = '../../data/booking/confirm';
				$allowed_ext = array('jpg','jpeg','png','gif');
				 
				// 변수 정리
				$error = $_FILES['upload2']['error'];
				$name = $_FILES['upload2']['name'];
				$ext = array_pop(explode('.', $name));
			    $data_name = "confirm_".$_mokey."_".$wepix_now_time.".".$ext;
				// 오류 확인
				if( $error != UPLOAD_ERR_OK ) {
					switch( $error ) {
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							echo "파일이 너무 큽니다. ($error)";
							break;
						case UPLOAD_ERR_NO_FILE:
							echo "파일이 첨부되지 않았습니다. ($error)";
							break;
						default:
							echo "파일이 제대로 업로드되지 않았습니다. ($error)";
					}
					exit;
				}
				 
				// 파일 이동
				move_uploaded_file( $_FILES['upload2']['tmp_name'], "$uploads_dir/$data_name");


				$query = "insert into "._DB_WANTED." set
						WP_BKP_MACHING_CODE = '".$modify_maching_code."',
						WP_IMG_DATA = '".$data_name."',
						WP_KIND = 'CONFIRM',
						WP_REG_ID = '".$_ad_id."',
						WP_REG_DATE = '".$wepix_now_time."'";
				wepix_query_error($query);
			}
		

		msg("수정완료 !","booking_modify_popup2.php?key=".$_mokey."&mode=modify");
		


// ******************************************************************************************************************
// 호텔선택시 룸타입 검색 (Ajax)
// ******************************************************************************************************************
}elseif($_action_mode == 'selectRoom'){
	$_hot_idx = securityVal($key);
	$_hot_key = explode(":",$_hot_idx);
	$_selectd_key = securityVal($selectd_key);


	$roomtype_query = "select ROC_IDX,ROC_NAME from ".$db_t_ROOM_TYPE_DB." where ROC_VIEW = 'Y' AND  ROC_HOT_IDX = '".$_hot_key[0]."' order by ROC_NAME asc ";
	$roomtype_result = wepix_query_error($roomtype_query);
	while( $roomtype_list = wepix_fetch_array($roomtype_result)) {
		
		if( $roomtype_list[ROC_IDX] ==  $_selectd_key ){
			$selectd = "selectd";
		}else{
			$selectd = "";
		}
		echo "<option value='".$roomtype_list[ROC_IDX].":".$roomtype_list[ROC_NAME]."' ".$selectd.">".$roomtype_list[ROC_NAME]."</option>";

	}

// ******************************************************************************************************************
// 부킹그룹 투어피 추가
// ******************************************************************************************************************
}elseif($_action_mode == 'groupTourfeeAdd'){

			$_bkg_idx = securityVal($bkg_idx);
			$_add_value = securityVal($add_value);
			
			$data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING_GROUP." where BKG_IDX = '".$_bkg_idx."'"));

			$total_tour_fee = $data[BKG_TOTAL_TOUR_FEE] + $_add_value;

			if($data[BKG_TOUR_FEE_DATE] != ''){
				$tour_fee_history = $data[BKG_TOUR_FEE]."|".$_add_value;
				$tour_fee_history_date = $data[BKG_TOUR_FEE_DATE]."|".$wepix_now_time;
				$tour_fee_history_id = $data[BKG_TOUR_FEE_ID]."|".$_ad_id;
				
			}else{
				$tour_fee_history = $_add_value;
				$tour_fee_history_date = $wepix_now_time;
				$tour_fee_history_id = $_ad_id;
			}
			
			
			$query = "update "._DB_BOOKING_GROUP." set
					BKG_TOUR_FEE = '".$tour_fee_history."',
					BKG_TOUR_FEE_DATE = '".$tour_fee_history_date."',
					BKG_TOUR_FEE_ID = '".$tour_fee_history_id."',
					BKG_TOTAL_TOUR_FEE = ".$total_tour_fee."
					where BKG_IDX = ".$_bkg_idx."";
			wepix_query_error($query);


// ******************************************************************************************************************
// 부킹그룹 신규등록
// ******************************************************************************************************************
}elseif($_action_mode == 'newBookingGroup'){

		$a_mode = securityVal($action_mode);
		$_mokey = cleanVariable($mokey);
		$bkp_idx = securityVal($bkp_idx);
		$_bkg_name = securityVal($bkg_name);
		$_bkg_gid_id = securityVal($bkp_guide_id);
		$_bkg_bkp_count = securityVal($bkg_bkp_count);
		$_bkg_head_count = securityVal($bkg_head_count);
		$_bkp_type = securityVal($bkp_type);
		$bkg_start_date = securityVal($bkg_start_date);
		$bkg_end_date = securityVal($bkg_end_date);
		$_bkp_tour_fee = securityVal($bkp_tour_fee);
		$_bkg_total_tour_fee = (int)str_replace(',','',$_POST['bkg_total_tour_fee']);
		
		$_bkp_idx = explode(",",$bkp_idx);
		$_bkg_code = $_bkg_gid_id."_".$wepix_now_time;
		$dstart = explode("-",$bkg_start_date);
		$dend = explode("-",$bkg_end_date);
		$_bkg_start_date = mktime(0,0,0,$dstart[1],$dstart[2],$dstart[0]);
		$_bkg_end_date = mktime(23,59,59,$dend[1],$dend[2],$dend[0]);



	
			$query = "insert into "._DB_BOOKING_GROUP." set
                BKG_CODE = '".$_bkg_code."',
                BKG_TYPE = '".$_bkp_type."',
				BKG_KIND = 'N',
                BKG_NAME = '".$_bkg_name."',
                BKG_TOUR_FEE = '".$_bkp_tour_fee."',
                BKG_TOUR_FEE_DATE = '".$wepix_now_time."',
				BKG_TOTAL_TOUR_FEE = '".$_bkg_total_tour_fee."',
				BKG_GID_ID = '".$_bkg_gid_id."',
				BKG_START_DATE = '".$_bkg_start_date."',
				BKG_END_DATE = '".$_bkg_end_date."',
				BKG_BKP_IDX = '".$bkp_idx."',
				BKG_HEAD_COUNT = '".$_bkg_head_count."',
				BKG_BKP_COUNT = '".$_bkg_bkp_count."'";

		  wepix_query_error($query);


		  $bkg_idx_data = wepix_fetch_array(wepix_query_error("select BKG_IDX from "._DB_BOOKING_GROUP." where BKG_CODE = '".$_bkg_code."'"));


		for($i=0;$i < count($_bkp_idx); $i++){
		$query = "update  "._DB_BOOKING_PARENT."  set 
				BKP_BKG_CODE = '".$_bkg_code."',
				BKP_BKG_IDX = '".$bkg_idx_data[BKG_IDX]."',
				BKP_GUIDE_ID ='".$_bkg_gid_id."'
				
		where BKP_IDX = '".$_bkp_idx[$i]."'";

			wepix_query_error($query);
		}

		echo ("<script type='text/javascript'>parent.location.reload(); parent.closedPopup();</script>");


// ******************************************************************************************************************
// 부킹그룹 수정
// ******************************************************************************************************************
}elseif($_action_mode == 'modifyBookingGroup'){

	$_bkg_idx = str_replace(',','',$_POST['bkg_idx']);
	$_bkp_idx = explode(",",securityVal($bkp_idx));
	$bkg_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING_GROUP." where BKG_IDX = '".$_bkg_idx."' "));

	
	for($i=0;$i<count($_bkp_idx);$i++){
		$query = "update "._DB_BOOKING_PARENT." set 
                BKP_BKG_CODE = '".$bkg_data[BKG_CODE]."',
                BKP_BKG_IDX = '".$_bkg_idx."',
				BKP_GUIDE_ID ='".$bkg_data[BKG_GID_ID]."'
				where BKP_IDX = '".$_bkp_idx[$i]."' ";
		wepix_query_error($query);
	}



	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//부킹 그룹 정보 다시 갱신하기
	$search_sql = " where BKP_BKG_CODE = '".$bkg_data[BKG_CODE]."' ";
	$query = "select BKP_IDX,BKP_GUEST from "._DB_BOOKING_PARENT." ".$search_sql." order by BKP_IDX desc ";
	$result = wepix_query_error($query);

	$re_bkg_head_count = 0;
	while($list = wepix_fetch_array($result)){
		$re_bkg_bkp_idx_array[] = $list[BKP_IDX];
		$bkp_guest_array = explode("│",$list[BKP_GUEST]);
		$re_bkg_head_count += count($bkp_guest_array);
	}

	$re_bkg_bkp_idx = implode(",",$re_bkg_bkp_idx_array);
	$re_bkg_bkp_count = count($re_bkg_bkp_idx_array); 

	//시작일 끝일 다시 뽑기
	$redate = wepix_fetch_array(wepix_query_error("select 
		MIN(BKP_START_DATE) as valmin,
		MAX(BKP_ARRIVE_DATE) as valmax from "._DB_BOOKING_PARENT." WHERE BKP_IDX in (".$re_bkg_bkp_idx.") " ));

	$re_bkg_start_date = $redate[valmin];
	$re_bkg_end_date = $redate[valmax];

	//갱신된 부킹 그룹 정보 다시 저장하기
	$query = "update "._DB_BOOKING_GROUP." set 
				BKG_START_DATE = '".$re_bkg_start_date."',
				BKG_END_DATE = '".$re_bkg_end_date."',
				BKG_BKP_IDX = '".$re_bkg_bkp_idx."',
				BKG_HEAD_COUNT = '".$re_bkg_head_count."',
				BKG_BKP_COUNT ='".$re_bkg_bkp_count."'
				where BKG_IDX = '".$bkg_data[BKG_IDX]."' ";
	wepix_query_error($query);

	echo ("<script type='text/javascript'>parent.location.reload(); parent.closedPopup();</script>");
	//echo ("<script type='text/javascript'>alert('".$_bkg_idx."')</script>");


// ******************************************************************************************************************
// 부킹그룹 해제
// ******************************************************************************************************************
}elseif($_action_mode == 'outBookingGroup'){

	$query = "update  "._DB_BOOKING_PARENT."  set 
			BKP_BKG_CODE = '',
			BKP_GUIDE_ID =''
			where BKP_IDX = '".$out_idx."' ";
	wepix_query_error($query);

	$search_sql = " where BKP_BKG_CODE = '".$out_code."' ";
	$query = "select BKP_IDX,BKP_GUEST from "._DB_BOOKING_PARENT." ".$search_sql." order by BKP_IDX desc ";
	$result = wepix_query_error($query);

	$re_bkg_head_count = 0;
	while($list = wepix_fetch_array($result)){
		$re_bkg_bkp_idx_array[] = $list[BKP_IDX];
		$bkp_guest_array = explode("│",$list[BKP_GUEST]);
		$re_bkg_head_count += count($bkp_guest_array);
	}

	$re_bkg_bkp_idx = implode(",",$re_bkg_bkp_idx_array);
    $re_bkg_bkp_count = count($re_bkg_bkp_idx_array);
    
    if(count($re_bkg_bkp_idx_array) == 0){
        $query = "delete from ".$db_t_BOOKING_GROUP." 
            where BKG_CODE = '".$out_code."' ";
        wepix_query_error($query);
        msg("해제완료!","booking_list.php");
        exit;
    };
    
	$redate = wepix_fetch_array(wepix_query_error("select 
			MIN(BKP_START_DATE) as valmin,
			MAX(BKP_ARRIVE_DATE) as valmax 
		from "._DB_BOOKING_PARENT." WHERE BKP_IDX in (".$re_bkg_bkp_idx.") " ));

	$re_bkg_start_date = $redate[valmin];
	$re_bkg_end_date = $redate[valmax];

	$query = "update "._DB_BOOKING_GROUP." set 
				BKG_START_DATE = '".$re_bkg_start_date."',
				BKG_END_DATE = '".$re_bkg_end_date."',
				BKG_BKP_IDX = '".$re_bkg_bkp_idx."',
				BKG_HEAD_COUNT = '".$re_bkg_head_count."',
				BKG_BKP_COUNT ='".$re_bkg_bkp_count."'
				where BKG_CODE = '".$out_code."' ";
	wepix_query_error($query);


	msg("부킹그룹 해제 완료",_A_PATH_BOOKING_LIST);

// ******************************************************************************************************************
// 랜드피 입금관련
// ******************************************************************************************************************
}elseif($_action_mode == 'land_array'){

    $send_array = securityVal($send_array);
    $idx_array = explode(",",$send_array);
    $land_fee_kind = securityVal($land_kind);
	
    $page_get = securityVal($page_get);

	if($land_fee_kind != 'release' ){
	
    
    $serch_list = 'BKP_LAND_FEE,BKP_LAND_FEE_NOW,BKP_LAND_FEE_YN,BKP_LAND_FEE_KIND,BKP_LAND_FEE_CONFIRM_ID,BKP_LAND_FEE_CONFIRM_DATE,BKP_LAND_FEE_HISTORY,BKP_LAND_FEE_DATE_HISTORY,BKP_LAND_FEE_TEXT';

    for($a=0;$a<count($idx_array);$a++){
        $land_fee_now_="land_fee_now_".$idx_array[$a];
        $land_fee_date_="land_fee_date_".$idx_array[$a];
        $land_fee_now = (int)str_replace(',','',$_POST[$land_fee_now_]);  
        $land_fee_date = $_POST[$land_fee_date_];
        $data = wepix_fetch_array(wepix_query_error("select ".$serch_list." from "._DB_BOOKING_PARENT." where BKP_IDX = '".$idx_array[$a]."' "));
        $land_fee_yn = 'N';
        $dstart = explode("-",$land_fee_date);
        $_land_fee_date = mktime(0,0,0,$dstart[1],$dstart[2],$dstart[0]);

        $land_fee_history_array  = explode("|",$data[BKP_LAND_FEE_HISTORY]);
        $land_date_history_array  = explode("|",$data[BKP_LAND_FEE_DATE_HISTORY]);

        if($land_fee_now != 0){
            if($data[BKP_LAND_FEE_HISTORY] != ''){
                $land_fee_history = $data[BKP_LAND_FEE_HISTORY]."|".$land_fee_now;
                $land_fee_date_history = $data[BKP_LAND_FEE_DATE_HISTORY]."|".$_land_fee_date;
                $_land_fee_now = $land_fee_now+$data[BKP_LAND_FEE_NOW];
            }else{
                $land_fee_history = $land_fee_now;
                $land_fee_date_history = $_land_fee_date;
                $_land_fee_now = $land_fee_now;
            }
        }

		if($data[BKP_LAND_FEE] == 0 || $data[BKP_LAND_FEE] == 1){
			$_ary_bkp_land_fee_text = explode("│",$data[BKP_LAND_FEE_TEXT]);
			$_view_land_fee = 0;
			for($i=0;$i<count($_ary_bkp_land_fee_text);$i++){
				$_ary2_bkp_land_fee_text = explode("/",$_ary_bkp_land_fee_text[$i]);

				if($_ary2_bkp_land_fee_text[2] != 0 && $_ary2_bkp_land_fee_text[3] != 0){
					$_view_land_fee += $_ary2_bkp_land_fee_text[2] *($_ary2_bkp_land_fee_text[3] * $_ary2_bkp_land_fee_text[1]);
				}elseif($_ary2_bkp_land_fee_text[2] != 0 && $_ary2_bkp_land_fee_text[3] == 0){
					$_view_land_fee += $_ary2_bkp_land_fee_text[1] * $_ary2_bkp_land_fee_text[2];
				}elseif($_ary2_bkp_land_fee_text[2] == 0 && $_ary2_bkp_land_fee_text[3] != 0){
					$_view_land_fee += $_ary2_bkp_land_fee_text[1] * $_ary2_bkp_land_fee_text[3];
				}
			}
		}else{
			$_view_land_fee = $data[BKP_LAND_FEE];
		}



        if($_land_fee_now >= $_view_land_fee){
            $land_fee_yn = 'Y';
        }

            $query = "update "._DB_BOOKING_PARENT." set
                    BKP_LAND_FEE_NOW = '".$_land_fee_now."',
                    BKP_LAND_FEE_YN = '".$land_fee_yn."',
                    BKP_LAND_FEE_KIND = '".$land_fee_kind."',
                    BKP_LAND_FEE_HISTORY = '".$land_fee_history."',
                    BKP_LAND_FEE_DATE_HISTORY = '".$land_fee_date_history."',
                    BKP_LAND_FEE_CONFIRM_ID = '".$ad_id."',
                    BKP_LAND_FEE_CONFIRM_DATE = '".$_land_fee_date."'
            where BKP_IDX = ".$idx_array[$a]."";

        wepix_query_error($query);
        
    }


    msg("일괄등록 완료",_A_PATH_BOOKING_LAND_FEE_LIST."?".$page_get);

	}else{
		 for($a=0;$a<count($idx_array);$a++){
			
			$query = "update "._DB_BOOKING_PARENT." set
			  BKP_LAND_FEE_NOW = '0',
			  BKP_LAND_FEE_YN = 'N',
			  BKP_LAND_FEE_KIND = '',
			  BKP_LAND_FEE_HISTORY = '',
			  BKP_LAND_FEE_DATE_HISTORY = '',
			  BKP_LAND_FEE_CONFIRM_ID = '',
			  BKP_LAND_FEE_CONFIRM_DATE = ''
			where BKP_IDX = ".$idx_array[$a]."";

			wepix_query_error($query);
				
			}
	 msg("해제완료",_A_PATH_BOOKING_LAND_FEE_LIST."?".$page_get);
	}
// ******************************************************************************************************************
// 에이전시 컨펌
// ******************************************************************************************************************  
}elseif( $_action_mode == 'agency_cf' ){

	$_submit_mode = securityVal($submit_mode);
	$_state = securityVal($state);
	$_mokey = securityVal($key);
	
	if( $_state == "Y" ){
		$bkp_agency_confirm_yn = "N";
	}else{
		$bkp_agency_confirm_yn = "Y";
	}

	$query = "update "._DB_BOOKING_PARENT." set
            BKP_AGENCY_CONFIRM_YN = '".$bkp_agency_confirm_yn."',
            BKP_AGENCY_CONFIRM_DATE = '".$wepix_now_time."',
            BKP_AGENCY_CONFIRM_ID = '".$_ad_id."'
		where BKP_IDX = ".$_mokey;
	wepix_query_error($query);

	if( $_submit_mode == "bkt-list" ){
		echo "|completion|컨펌확인|".$bkp_agency_confirm_yn."|";
	}else{
		msg("컨펌확인","booking_form.php?mode=modify&mokey=".$_mokey);
	}
	exit;
// ******************************************************************************************************************
// 호텔 컨펌
// ******************************************************************************************************************  
}elseif($_action_mode == "htkindCh_list"){
	$_submit_mode = securityVal($submit_mode);
		 $_num = securityVal($num);
		 $_kind = securityVal($kind);
		 $_mokey = securityVal($mokey);

		 $data = wepix_fetch_array(wepix_query_error("select BKP_HOT_BOOKING_STATE from "._DB_BOOKING_PARENT." where BKP_IDX = '".$_mokey."' "));
		 $bkp_hot_kind = explode("│",$data[BKP_HOT_BOOKING_STATE]);

		 for($o=0; $o < count($bkp_hot_kind) ; $o++){
			 if($o == $_num){
				 if($_kind == '0'){
					 $bkp_hot_kind[$o] = '1';
				 }elseif($_kind == '1'){
					 $bkp_hot_kind[$o] = '2';
				 }elseif($_kind == '2'){
					  $bkp_hot_kind[$o] = '3';
				 }elseif($_kind == '3'){
					  $bkp_hot_kind[$o] = '0';
				 }

			 }

		 }
		  $_bkp_hotel_kind= implode("│",$bkp_hot_kind);

		  $query = "update  "._DB_BOOKING_PARENT."  set 
			BKP_HOT_BOOKING_STATE = '".$_bkp_hotel_kind."'
			where BKP_IDX = '".$_mokey."'";
		$result = wepix_query_error($query);
		
		if( $_submit_mode == "bkt-list" ){
			echo "|completion|컨펌확인|".$bkp_agency_confirm_yn."|";
		}else{
			msg("컨펌확인","booking_form.php?mode=modify&mokey=".$_mokey);
		}

// ******************************************************************************************************************
// 청구서 컨펌 (전체 컨펌)
// ******************************************************************************************************************
}elseif($_action_mode =='bill_all_cf'){

	$_bak_key = securityVal($bak);

    $bp_query = "update  "._DB_BILL_TRAVEL." set
    PU_ADMIN_CONFIRM = 'Y',
    PU_ADMIN_CONFIRM_ID = '".$_ad_id."',
    PU_ADMIN_CONFIRM_DATE = '".$wepix_now_time."'
    where PU_BKG_IDX = '".$_key."'";

    $bp_result = wepix_query_error($bp_query);  
    msg("",_A_PATH_GROUP_CALCUATE."?idx=".$_bak_key);
// ******************************************************************************************************************
// 청구서 컨펌 (개별 컨펌)
// ******************************************************************************************************************
}elseif($_action_mode =='bill_part_cf'){

	$_bak_key = securityVal($bak);

    $bp_query = "update  "._DB_BILL_TRAVEL." set
    PU_ADMIN_CONFIRM = 'Y',
    PU_ADMIN_CONFIRM_ID = '".$_ad_id."',
    PU_ADMIN_CONFIRM_DATE = '".$wepix_now_time."'
    where PU_IDX = '".$_key."'";

    $bp_result = wepix_query_error($bp_query);  
    msg("",_A_PATH_GROUP_CALCUATE."?idx=".$_bak_key);
// ******************************************************************************************************************
// 부킹삭제 (최종삭제)
// ******************************************************************************************************************
}elseif($_action_mode=="DelManagerBooking"){
		$query = "delete from "._DB_BOOKING_PARENT." 
			where BKP_IDX = ".$_key."";
        $result = wepix_query_error($query);
        
        $fastTrack_query = "delete from "._DB_FAST_TRACK." 
			where FT_BKP_IDX = ".$_key."";
        wepix_query_error($fastTrack_query);

        $golf_query = "delete from "._DB_BOOKING_GOLF." 
			where BG_BKP_IDX = ".$_key."";
        $result = wepix_query_error($golf_query);

        $query = "delete from "._DB_HOTEL_STATISTICS." 
                where STH_BKP_IDX =".$_key;
        wepix_query_error($query);

		msg("삭제완료!",_A_PATH_BOOKING_LIST);
// ******************************************************************************************************************
// 부킹삭제 
// ******************************************************************************************************************
}elseif($_action_mode=="DelBooking"){
		$query = "update  "._DB_BOOKING_PARENT." set
				BKP_DEL_YN = 'Y',
				BKP_DEL_DATE = '".$wepix_now_time."',
				BKP_DEL_ID = '".$_ad_id."'
			where BKP_IDX = ".$_key."";
        $result = wepix_query_error($query);

		msg("삭제완료!",_A_PATH_BOOKING_LIST);
// ******************************************************************************************************************
// 개인결제 환불
// ******************************************************************************************************************
}elseif($_action_mode=="doRefund"){

	$_code = securityVal($code);
	
	$operating_server = "https://api.paypal.com";

	$client_secret = "AeBFZDRdPLoDiXXMcpnopTc4sYgDd9eAPYFb5Qyj4lvjvX2xUadd3vhBq_TuUUyYABHFiBvMCwgmxf06:EOlnGPHWtV1XxabM70aTFUEd2DC0I6oW02OwZBjoD9jONXIEWb3zyte_bl7gmGaHO7HtmrgIQ2zd63Wj";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $operating_server."/v1/oauth2/token");
	curl_setopt($ch, CURLOPT_HTTPHEADER,'Content-Type: application/json');
	curl_setopt($ch, CURLOPT_HTTPHEADER,'Accept-Language: en_US');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_USERPWD ,$client_secret  );
	curl_setopt($ch, CURLOPT_POSTFIELDS,"grant_type=client_credentials");

	$result = curl_exec($ch);

	curl_close ($ch);
   
	$lines = explode(",", $result);
	$keyarray = array();
	for ($i=1; $i<count($lines);$i++){

		list($key,$val) = explode(":", $lines[$i]);
		$keyarray[str_replace('"','',urldecode($key))] = str_replace('"','',urldecode($val));
		
	}

	$_access_token = $keyarray['access_token'];
	
	$authorization = "Authorization: Bearer ".$_access_token."";

	

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.paypal.com/v1/payments/sale/".$_code."/refund");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"{}");
	$res = curl_exec($ch);
	curl_close($ch);
//	var_dump($res);//결과값 확인하기

	$lines = explode("\n", $res);
	$keyarray = array();

	
	for ($i=1; $i<count($lines);$i++){

		list($key,$val) = explode(":", $lines[$i]);
		$keyarray[str_replace('"','',urldecode($key))] = str_replace('"','',urldecode($val));
	}

	if(strpos($lines[0],"TRANSACTION_REFUSED") !== false){
		$query = "update "._DB_PAYMENT_GATE."  set 
				PG_STATE = 'cancel',
				PG_CANCEL_FEE = 0.3 ,
				PG_CANCEL_ID = '".$_ad_id."' ,
				PG_CANCEL_DATE = '".$wepix_now_time."'

			where PG_IDX = '".$_key."'";
		$result = wepix_query_error($query);

		msg("결제취소 완료",_A_PATH_BOOKING_LIST);
	}else{
		msg("취소 수수료 잔액부족",_A_PATH_BOOKING_LIST);
	}

	
	
	
}
exit;
?>