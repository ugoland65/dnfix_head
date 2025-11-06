<?
include "../lib/inc_common.php";

	$_a_mode = securityVal($action_mode);
	$_tr_key = securityVal($tr_key);
	$_bkp_key = securityVal($bkp_key);
	$_tp_code = securityVal($tp_code);
	$_key = securityVal($key);

    $_tp_area = securityVal($tp_area);
    $_tp_title =securityVal($tp_title);
    $_tp_price_text = securityVal($tp_price_text);
    $_tp_inclusion =securityVal($tp_inclusion);
    $_tp_not_inclusion = securityVal($tp_not_inclusion);
    $_tp_hotel_contact =securityVal($tp_hotel_contact);
    $_tp_local_contact = securityVal($tp_local_contact);
    $_tp_meeting =securityVal($tp_meeting);
    $_tp_memo =securityVal($tp_memo);
	
    $_tpg_area = securityVal($tpg_area);
    $_tpg_traffic =securityVal($tpg_traffic);
    $_tpg_paln_text= securityVal($tpg_paln_text);
    $_tpg_hotel =securityVal($tpg_hotel);
    $_tpg_food1 = securityVal($tpg_food1);
    $_tpg_food2 =securityVal($tpg_food2);
    $_tpg_food3 = securityVal($tpg_food3);


// ******************************************************************************************************************
// 확정서 샘플 등록
// ******************************************************************************************************************
if($_a_mode == "newTravelPlanTempalte"){

	$_tp_code = mt_rand(1,9999)."_".$wepix_now_time;

	for($i=0;$i<count($_tpg_area);$i++){
		$pd_num = $i+1;
		$tpg_food =  $_tpg_food1[$i]."/".$_tpg_food2[$i]."/".$_tpg_food3[$i];

		$tpg_pd_key = ${tpg_pd_key_.$pd_num};
			

		  $query = "insert into  "._DB_TRAVEL_PLAN_TEMPLATE_GOODS." set
            TPG_TP_CODE = '".$_tp_code."',
			TPG_DAY_NUM	 ='".$pd_num."',
			TPG_AREA = '".$_tpg_area[$i]."' ,
			TPG_TRAFFIC = '".$_tpg_traffic[$i]."' ,
			TPG_TIME = '".$_won_money."' ,
			TPG_PLAN_TEXT ='".$_tpg_paln_text[$i]."',
			TPG_HOTEL ='".$_tpg_hotel[$i]."',
			TPG_FOOD ='".$tpg_food."',
			TPG_PD_KEY ='".$tpg_pd_key."'";

		 $result = wepix_query_error($query);

	}




	 $query = "insert into  "._DB_TRAVEL_PLAN_TEMPLATE." set
            TP_CODE = '".$_tp_code."',
			TP_TITLE	 ='".$_tp_title."',
			TP_PRICE_TEXT = '".$_tp_price_text."' ,
			TP_INCLUSION = '".$_tp_inclusion."' ,
			TP_NOT_INCLUSION ='".$_tp_not_inclusion."' ,
			TP_HOTEL_CONTACT ='".$_tp_hotel_contact."',
			TP_LOCAL_CONTACT ='".$_tp_local_contact."',
			TP_MEETING ='".$_tp_meeting."',
			TP_MEMO ='".$_tp_memo."',
			TP_REG_ID ='".$_ad_id."',
			TP_REG_DATE ='".$wepix_now_time."'";

	 $result = wepix_query_error($query);
	 msg("등록 완료!",_A_PATH_PLAN_TEMPLATE_LIST);

	

}elseif($_a_mode == "modifyTravelPlanTempalte"){
// ******************************************************************************************************************
// 확정서 샘플 수정
// ******************************************************************************************************************


	for($i=0;$i<count($_tpg_area);$i++){
		$pd_num = $i+1;
		$tpg_food =  $_tpg_food1[$i]."/".$_tpg_food2[$i]."/".$_tpg_food3[$i];

		$tpg_pd_key = ${tpg_pd_key_.$pd_num};
			

		  $query = "update "._DB_TRAVEL_PLAN_TEMPLATE_GOODS." set
            
			TPG_DAY_NUM	 ='".$pd_num."',
			TPG_AREA = '".$_tpg_area[$i]."' ,
			TPG_TRAFFIC = '".$_tpg_traffic[$i]."' ,
			TPG_TIME = '".$_won_money."' ,
			TPG_PLAN_TEXT ='".$_tpg_paln_text[$i]."',
			TPG_HOTEL ='".$_tpg_hotel[$i]."',
			TPG_FOOD ='".$tpg_food."',
			TPG_PD_KEY ='".$tpg_pd_key."'
			where TPG_TP_CODE = '".$_tp_code."' and TPG_DAY_NUM = ".$pd_num;

		 $result = wepix_query_error($query);

	}

	 $query = "update "._DB_TRAVEL_PLAN_TEMPLATE." set
           
			TP_TITLE	 ='".$_tp_title."',
			TP_PRICE_TEXT = '".$_tp_price_text."' ,
			TP_INCLUSION = '".$_tp_inclusion."' ,
			TP_NOT_INCLUSION ='".$_tp_not_inclusion."' ,
			TP_HOTEL_CONTACT ='".$_tp_hotel_contact."',
			TP_LOCAL_CONTACT ='".$_tp_local_contact."',
			TP_MEETING ='".$_tp_meeting."',
			TP_MEMO ='".$_tp_memo."',
			TP_REG_ID ='".$_ad_id."',
			TP_REG_DATE ='".$wepix_now_time."'
			where TP_CODE = '".$_tp_code."'";

	 $result = wepix_query_error($query);
	 msg("수정 완료!",_A_PATH_PLAN_TEMPLATE_REG."?mode=modify&key=".$_key);
// ******************************************************************************************************************
// 확정서 등록
// ******************************************************************************************************************

}elseif($_a_mode == "newTravelPlan"){


	for($i=0;$i<count($_tpg_area);$i++){
		$pd_num = $i+1;
		$pd_day_num[] = $i+1;
		$tpg_food[] =  $_tpg_food1[$i]."/".$_tpg_food2[$i]."/".$_tpg_food3[$i];
		$tpg_pd_key[] = ${tpg_pd_key_.$pd_num};
	}

	
    $_ary_pd_num = implode("│",$pd_day_num);
    $_ary_tpg_area = implode("│",$_tpg_area);
    $_ary_tpg_traffic = implode("│",$_tpg_traffic);
	$_ary_tpg_time = implode("│",$_tpg_time);
    $_ary_tpg_paln_text = implode("│",$_tpg_paln_text);
    $_ary_tpg_hotel = implode("│",$_tpg_hotel);
	$_ary_tpg_food = implode("│",$tpg_food);
    $_ary_tpg_pd_key = implode("│",$tpg_pd_key);



	$query = "insert into  "._DB_TRAVEL_PLAN." set
				TR_BKP_IDX = '".$_bkp_key."',
				TR_TP_CODE	 ='".$_tp_code."',
				TR_TITLE = '".$_tp_title."' ,
				TR_PRICE_TEXT = '".$_tp_price_text."' ,
				TR_INCLUSION ='".$_tp_inclusion."' ,
				TR_NOT_INCLUSION ='".$_tp_not_inclusion."',
				TR_HOTEL_CONTACT ='".$_tp_hotel_contact."',
				TR_LACAL_CONTACT ='".$_tp_local_contact."',
				TR_MEETING ='".$_tp_meeting."',
				TR_MEMO ='".$_tp_memo."',
				TR_DAY_NUM ='".$_ary_pd_num."',
				TR_AREA ='".$_ary_tpg_area."',
				TR_TRAFFIC ='".$_ary_tpg_traffic."',
				TR_TIME ='".$_ary_tpg_time."',
				TR_PLAN_TEXT ='".$_ary_tpg_paln_text."',
				TR_HOTEL ='".$_ary_tpg_hotel."',
				TR_FOOD ='".$_ary_tpg_food."',
				TR_PD_KEY ='".$_ary_tpg_pd_key."' ,
				TR_REG_ID ='".$_ad_id."',
				TR_REG_DATE ='".$wepix_now_time."'";

	 $result = wepix_query_error($query);
	 msg("등록 완료!",_A_PATH_TRAVEL_PLAN_REG."?bkp_key=".$_bkp_key);

	
// ******************************************************************************************************************
// 확정서 수정
// ******************************************************************************************************************
}elseif($_a_mode == "modifyTravelPlan"){

	for($i=0;$i<count($_tpg_area);$i++){
		$pd_num = $i+1;
		$pd_day_num[] = $i+1;
		$tpg_food[] =  $_tpg_food1[$i]."/".$_tpg_food2[$i]."/".$_tpg_food3[$i];
		$tpg_pd_key[] = ${tpg_pd_key_.$pd_num};
	}

	
    $_ary_pd_num = implode("│",$pd_day_num);
    $_ary_tpg_area = implode("│",$_tpg_area);
    $_ary_tpg_traffic = implode("│",$_tpg_traffic);
	$_ary_tpg_time = implode("│",$_tpg_time);
    $_ary_tpg_paln_text = implode("│",$_tpg_paln_text);
    $_ary_tpg_hotel = implode("│",$_tpg_hotel);
	$_ary_tpg_food = implode("│",$tpg_food);
    $_ary_tpg_pd_key = implode("│",$tpg_pd_key);


	$query = "update  "._DB_TRAVEL_PLAN." set
				TR_TP_CODE	 ='".$_tp_code."',
				TR_TITLE = '".$_tp_title."' ,
				TR_PRICE_TEXT = '".$_tp_price_text."' ,
				TR_INCLUSION ='".$_tp_inclusion."' ,
				TR_NOT_INCLUSION ='".$_tp_not_inclusion."',
				TR_HOTEL_CONTACT ='".$_tp_hotel_contact."',
				TR_LACAL_CONTACT ='".$_tp_local_contact."',
				TR_MEETING ='".$_tp_meeting."',
				TR_MEMO ='".$_tp_memo."',
				TR_DAY_NUM ='".$_ary_pd_num."',
				TR_AREA ='".$_ary_tpg_area."',
				TR_TRAFFIC ='".$_ary_tpg_traffic."',
				TR_TIME ='".$_ary_tpg_time."',
				TR_PLAN_TEXT ='".$_ary_tpg_paln_text."',
				TR_HOTEL ='".$_ary_tpg_hotel."',
				TR_FOOD ='".$_ary_tpg_food."',
				TR_PD_KEY ='".$_ary_tpg_pd_key."' ,
				TR_REG_ID ='".$_ad_id."',
				TR_REG_DATE ='".$wepix_now_time."'
			where TR_IDX = '".$_tr_key."'";

	 $result = wepix_query_error($query);
	 msg("수정 완료!",_A_PATH_TRAVEL_PLAN_REG."?tr_key=".$_tr_key."&bkp_key=".$_bkp_key);


// ******************************************************************************************************************
// 확정서 템플릿 선택 (Ajax)
// ******************************************************************************************************************
}elseif($_a_mode == 'openPalnTemplate'){

	$_tr_key = securityVal($key);



	$tp_data = wepix_fetch_array(wepix_query_error("select * from "._DB_TRAVEL_PLAN_TEMPLATE." where TP_IDX = '".$_tr_key."' "));
	$tpg_query = "select * from "._DB_TRAVEL_PLAN_TEMPLATE_GOODS." where TPG_TP_CODE = '".$tp_data[TP_CODE]."' ";
	$tpg_result = wepix_query_error($tpg_query);



	$_ajax_tr_text = $tp_data[TP_TITLE]."#*#".$tp_data[TP_PRICE_TEXT]."#*#".$tp_data[TP_INCLUSION]."#*#".$tp_data[TP_NOT_INCLUSION]."#*#".$tp_data[TP_HOTEL_CONTACT]."#*#".$tp_data[TP_LOCAL_CONTACT]."#*#".$tp_data[TP_MEETING]."#*#".$tp_data[TP_MEMO];

	
	$tpg_count=0;
	while($tpg_list =  wepix_fetch_array($tpg_result)){
		$tpg_day_num[] = $tpg_list[TPG_DAY_NUM];
		$tpg_area[] = $tpg_list[TPG_AREA];
		$tpg_traffic[] = $tpg_list[TPG_TRAFFIC];
		$tpg_time[] = $tpg_list[TPG_TIME];
		$tpg_plan_text[] = $tpg_list[TPG_PLAN_TEXT];
		$tpg_hotel[] = $tpg_list[TPG_HOTEL];
		$tpg_food[] = $tpg_list[TPG_FOOD];
		$tpg_pd_key[] = $tpg_list[TPG_PD_KEY];
		$tpg_count++;
	}

	 $_tpg_day_num = implode("│",$tpg_day_num);
	 $_tpg_area = implode("│",$tpg_area);
	 $_tpg_traffic = implode("│",$tpg_traffic);
	 $_tpg_time = implode("│",$tpg_time);
	 $_tpg_plan_text = implode("│",$tpg_plan_text);
	 $_tpg_hotel = implode("│",$tpg_hotel);
	 $_tpg_food = implode("│",$tpg_food);
	 $_tpg_pd_key = implode("│",$tpg_pd_key);
	
	$_ajax_trg_text = $tpg_count."#*#".$_tpg_day_num."#*#".$_tpg_area."#*#".$_tpg_traffic."#*#".$_tpg_time."#*#".$_tpg_plan_text."#*#".$_tpg_hotel."#*#".$_tpg_food."#*#".$_tpg_pd_key;

	echo $_ajax_tr_text."@#$#@".$_ajax_trg_text;
	
}
     
        



exit;
?>
