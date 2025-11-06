<?
include "../lib/inc_common.php";


    $a_mode = securityVal($action_mode);
	$_mokey =securityVal($mokey);
	$_key = securityVal($key);

	$_hotel_name = securityVal($hotelName);
	$_hotel_area = securityVal($hotelarea);
    $_hot_full_name = securityVal($hot_full_name);
    $Inclusive= securityVal($Inclusive);

    $all_in_food_price= securityVal($all_in_food_price);
    $all_in_drink_price= securityVal($all_in_drink_price);
    $full_board_price= securityVal($full_board_price);
    $half_board_price=securityVal($half_board_price);
    $extra_bed_price= securityVal($extra_bed_price);
    $extra_person_price= securityVal($extra_person_price);
    $gala_dinner_price= securityVal($gala_dinner_price);
    $late_1600_price= securityVal($late_1600_price);
    $late_1800_price= securityVal($late_1800_price);
    $late_after_1800_price= securityVal($late_after_1800_price);
    
	
	$_hotel_view =securityVal($hotelView);
	$_mokey =securityVal($mokey);


	$_room_type  = securityVal($room_type);
	$_room_full_name  =  securityVal($room_full_name);

	for( $i=0; $i<count($_room_type); $i++ ){
		$room_array[] = $_room_type[$i];
		$room_full_array[] = $_room_full_name[$i];
    }
    
if( $a_mode=="new"){
       $query = "insert into  HOTEL_DB set
			HOT_NAME = '".$_hotel_name."',
            HOT_FULL_NAME =  '".$_hot_full_name."',
            HOT_INCLUSIVE =  '".$Inclusive."',
            HOT_All_IN_FOOD_PRICE =  '".$all_in_food_price."',
            HOT_All_IN_DRINK_PRICE =  '".$all_in_drink_price."',
            HOT_FUll_BOARD_PRICE =  '".$full_board_price."',
            HOT_HALF_BOARD_PRICE =  '".$half_board_price."',
            HOT_EXTRA_BED_PRICE =  '".$extra_bed_price."',
            HOT_EXTRA_PERSON_PRICE =  '".$extra_person_price."',
            HOT_GALA_DINNER_PRICE =  '".$gala_dinner_price."',
            HOT_LATE_1600_PRICE =  '".$late_1600_price."',
            HOT_LATE_1800_PRICE =  '".$late_1800_price."',
            HOT_LATE_AFTER_1800_PRICE =  '".$late_after_1800_price."',
			HOT_AREA =  '".$_hotel_area."',
			HOT_VIEW = '".$_hotel_view."'";
		$result = wepix_query_error($query);
			msg("등록 완료!","hotel_list.php");

}else if($a_mode=="modify"){

		$query = "update  HOTEL_DB  set 
		HOT_NAME = '".$_hotel_name."',
            HOT_FULL_NAME =  '".$_hot_full_name."',
            HOT_INCLUSIVE =  '".$Inclusive."',
            HOT_All_IN_FOOD_PRICE =  '".$all_in_food_price."',
            HOT_All_IN_DRINK_PRICE =  '".$all_in_drink_price."',
            HOT_FUll_BOARD_PRICE =  '".$full_board_price."',
            HOT_HALF_BOARD_PRICE =  '".$half_board_price."',
            HOT_EXTRA_BED_PRICE =  '".$extra_bed_price."',
            HOT_EXTRA_PERSON_PRICE =  '".$extra_person_price."',
            HOT_GALA_DINNER_PRICE =  '".$gala_dinner_price."',
            HOT_LATE_1600_PRICE =  '".$late_1600_price."',
            HOT_LATE_1800_PRICE =  '".$late_1800_price."',
            HOT_LATE_AFTER_1800_PRICE =  '".$late_after_1800_price."',
			HOT_VIEW = '".$_hotel_view."'
			where HOT_IDX = '".$_mokey."'";
		$result = wepix_query_error($query);


  for( $i=0; $i<count($_room_type); $i++ ){
	     if($room_array[$i] != ''){
			 $query = "insert into  ".$db_t_ROOM_TYPE_DB." set
				ROC_NAME = '".$room_array[$i]."',
				ROC_FULL_NAME = '".$room_full_array[$i]."',
				ROC_VIEW = 'Y',
				ROC_HOT_IDX = '".$_mokey."'";
			$result = wepix_query_error($query);
		 }
  }

	msg("수정 완료!","hotel_list.php");

}elseif($a_mode=="del" AND $_mokey ){
			$query = "delete from HOTEL_DB 
			where HOTEL_DB.HOT_IDX = ".$_mokey."";
		$result = wepix_query_error($query);

			msg("삭제 완료!","hotel_list.php");
}elseif($a_mode=="room_modify"){
		
		$roomName = "room_name_".$_key;
		$room_full_name = "room_full_name".$_key;
		$_room_name = $_POST[$roomName];
  	    $_room_full_name = securityVal($_POST[$room_full_name]);

		$query = "update  ".$db_t_ROOM_TYPE_DB."  set 
			ROC_NAME = '".$_room_name."',
			ROC_FULL_NAME = '".$_room_full_name."'
			where ROC_IDX = '".$_key."'";

		$result = wepix_query_error($query);
		msg("수정 완료!","hotel_reg.php?mode=modify&key=".$_mokey."");

}elseif($a_mode=="room_Del" AND $_mokey ){

		$query = "delete from ".$db_t_ROOM_TYPE_DB." 
			where ".$db_t_ROOM_TYPE_DB.".ROC_IDX = ".$_key."";
			
		$result = wepix_query_error($query);
		msg("삭제 완료!","hotel_reg.php?mode=modify&key=".$_mokey."");

}




exit;
?>
