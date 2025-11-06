<?
include "../lib/inc_common.php";

	$_action_mode = securityVal($a_mode);
	$_bkg_idx = securityVal($key);

// ******************************************************************************************************************
// 샵 매출 등록
// ******************************************************************************************************************
if( $_action_mode == "newCalculate" ){
			
			wepix_query_error("delete from "._DB_CALCULATE." where CAL_STATE = '1' and CAL_BKG_IDX = ".$_bkg_idx."");
			$bkg_data =  wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING_GROUP." where BKG_IDX = ".$_bkg_idx));

			$op_query = "select * from "._DB_GROUP_OTHER." where OP_BKG_IDX ='".$_bkg_idx."'";
			$op_result = wepix_query_error($op_query);
			
			$_ary_bkp_idx = explode(",",$bkg_data[BKG_BKP_IDX]);
			for($i=0;$i<count($_ary_bkp_idx);$i++){
				$bkp_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING." where BKP_IDX = ".$_ary_bkp_idx[$i]));
				$buy_pd_sum = wepix_fetch_array(wepix_query_error("select sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BK_IDX = '".$_ary_bkp_idx[$i]."' "));
				$_show_total_buy_pd = $buy_pd_sum[total_price];
				$_view_bkp_discount_rate[] = $bkp_data[BKP_DISCOUNT_RATE];
				$_view_bkp_total_tm += ($_show_total_buy_pd-$bkp_data[BKP_FIRST_MONEY]) * (100 - $bkp_data[BKP_DISCOUNT_RATE]) / 100;
			}

			$search_sql = "where  BP_PD_KIND ='10000000'  and BP_BK_IDX in  (".$bkg_data[BKG_BKP_IDX].")";
			$food_data_count = wepix_counter2($db_t_BUY_PRODUCT, $search_sql, "BP_PD_IDX");

			$buydata = wepix_query_error("select * from ".$db_t_BUY_PRODUCT."  ".$search_sql ." GROUP BY BP_PD_IDX");
			while($pdlist = wepix_fetch_array($buydata)){
						
                $bp_sum = wepix_fetch_array(wepix_query_error("select count(BP_IDX) as idx_count, sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."'  and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_cash = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'cash' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_credit = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'credit' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));



			   $_view_food_total_price += $bp_sum[total_price];
               $_view_food_total_cash_price += $bp_sum_cash[total_price];
               $_view_food_total_credit_price  += $bp_sum_credit[total_price];
			   $_view_total_bp_count += $bp_sum[idx_count];

			   $_show_food_reate_cost = $bp_sum_cash[rate_cost] + $bp_sum_credit[rate_cost];
			   $_view_food_total_rate_cost += $_show_food_reate_cost;
			}

			$search_sql_tour = "where  (BP_PD_KIND ='07000000' and BP_BK_IDX in  (".$bkg_data[BKG_BKP_IDX].")) or (BP_PD_KIND ='08000000'  and BP_BK_IDX in  (".$bkg_data[BKG_BKP_IDX]."))";
			$tour_data_count = wepix_counter2($db_t_BUY_PRODUCT, $search_sql_tour, "BP_PD_IDX");

			$buydata = wepix_query_error("select * from ".$db_t_BUY_PRODUCT."  ".$search_sql_tour ." GROUP BY BP_PD_IDX");
			while($pdlist = wepix_fetch_array($buydata)){
						
                $bp_sum = wepix_fetch_array(wepix_query_error("select count(BP_IDX) as idx_count , sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."'  and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_cash = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'cash' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_credit = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'credit' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

			   $_view_total_price += $bp_sum[total_price];
               $_view_total_cash_price += $bp_sum_cash[total_price];
               $_view_total_credit_price  += $bp_sum_credit[total_price];
			   $_view_total_count +=$bp_sum[idx_count];

			   $_show_tour_reate_cost = $bp_sum_cash[rate_cost] + $bp_sum_credit[rate_cost];
			   $_view_tour_total_rate_cost += $_show_tour_reate_cost;
			}

			while($op_list = wepix_fetch_array($op_result)){

				$_view_credit_sum += $op_list[OP_CREDIT];
				$_view_cash_sum += $op_list[OP_CASH]; 
				$_view_total_sum += $op_list[OP_TOTAL]; 
			}

			$_view_tour_fee_total = $bkg_data[BKG_TOTAL_TOUR_FEE]; // 지급받은 투어피
			$_view_tour_fee_cash = $_view_food_total_cash_price + $_view_total_cash_price + $_view_cash_sum; // 캐쉬 사용 투어피
			$_view_tour_fee_credit = $_view_food_total_credit_price + $_view_total_credit_price + $_view_credit_sum; //크레딧 사용 어피
			$_view_use_tour_fee = $_view_tour_fee_cash + $_view_tour_fee_credit; // 총사용 투어피
			$_view_balance_tour_fee = $_view_tour_fee_total - $_view_tour_fee_cash; // 투어피 발란스

			$_ex_rate  = securityVal($cal_rate);
			$_ex_rate_get = $cal_shop_rate;

			$_view_total_tm = $_view_food_total_price + $_view_total_price; //총사용 TM
			$_view_total_rate_cost_price = $_view_food_total_rate_cost + $_view_tour_total_rate_cost;	//총 인정 원가 지출
			$_view_rate_cast = round(($_view_total_rate_cost_price / $_view_total_tm) * 100); // 원가 지출률

			if($_view_rate_cast > 35){  //원가 지출율이 35이상일때 계산 (원가지출률 * 추가 t/m) + ((추가 t/m) * (원가지출율 - 35))/100)
				$_over_rate_cost = $_view_rate_cast - 35;
				$_over_cost_deduct = ($_view_bkp_total_tm * $_over_rate_cost) / 100;
				$_view_cost_deduct = ($_view_rate_cast * $_view_bkp_total_tm) / 100 + $_over_cost_deduct;

			}else{ //원가 지출율이 35이하일때 계산 (원가지출률 * 추가 t/m) / 100
				$_view_cost_deduct = ($_view_rate_cast * $_view_bkp_total_tm) / 100;
			}
			$_view_calculate_salas = $_view_bkp_total_tm - $_view_cost_deduct ;//정산매출
			$_view_concede_sales_rate = 100 - ( $_view_rate_cast - 25 ); //인정맨출률

			if($_view_concede_sales_rate >100){
				$_view_concede_sales_rate = 100;
			}
			if($_view_bkp_total_tm < 0){
				$_view_bkp_total_tm = 0;
			}
			if($_view_cost_deduct < 0){
				$_view_cost_deduct = 0;
			}
			if($_view_calculate_salas < 0){
				$_view_calculate_salas = 0;
			}

			$_view_bkp_total_tm_dollar = round($_view_bkp_total_tm / $_ex_rate); //추가 T/M 달러
			$_view_cost_deduct_dollar = round($_view_cost_deduct / $_ex_rate); // 원가공제 달러
			$_view_calculate_salas_dollar = round($_view_calculate_salas / $_ex_rate);  // 정산 매출 달러
			$_view_concede_sales_price_dollar = round($_view_bkp_total_tm_dollar * $_view_concede_sales_rate) / 100;

			$guide_data =  wepix_fetch_array(wepix_query_error("select GD_NAME,GD_ID,GD_NICK from "._DB_GUIDE." where GD_ID = '".$bkg_data[BKG_GID_ID]."' "));
			$_shop_st_date = date("Ymd",$bkg_data[BKG_START_DATE]);
			$_shop_ed_date = date("Ymd",$bkg_data[BKG_END_DATE]);
			$_shop_gd_name = $guide_data[GD_NAME];
			$_shop_sql = " and SS_BKG_IDX = '".$_bkg_idx."'  and SS_GUIDE_NAME = '".$_shop_gd_name."'";
			$gml_data   =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'GML' ".$_shop_sql.""));
			$brd_a_data =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'BRD' and SS_PRODUCT_KIND = 'A' ".$_shop_sql.""));
			$brd_b_data =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'BRD' and SS_PRODUCT_KIND = 'B' ".$_shop_sql.""));
			$brd_c_data =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'BRD' and SS_PRODUCT_KIND = 'C' ".$_shop_sql.""));
			$prl_data   =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'PRL' ".$_shop_sql.""));
			$muk_data   =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'MUK' ".$_shop_sql.""));

			$muk_dollar = $muk_data[report_price] / $_ex_rate;
			$prl_dollar = $prl_data[report_price] / $_ex_rate;

			$total_con_dollar = $gml_data[report_price] + $brd_a_data[report_price] + $brd_b_data[report_price] + $brd_c_data[report_price] + $muk_dollar + $_view_concede_sales_price_dollar + $prl_dollar;
			
			$team_count = securityVal($team_count); //부킹 팀수
			$shop_cal_rate = $total_con_dollar/$team_count; //쌍당매ㅜㄹ

			$_gmd_calcu_rate  = securityVal($gmd_rate);
			$_brd_a_calcu_rate = securityVal($brd_a_rate);
			$_brd_b_calcu_rate = securityVal($brd_b_rate);
			$_brd_c_calcu_rate = securityVal($brd_c_rate);
			$_prl_calcu_rate  = securityVal($prl_rate);
			$_muk_calcu_rate = securityVal($muk_rate);
			$_tm_calcu_rate = securityVal($tm_rate);

			$gml_calcu_price = (($gml_data[report_price] * $_gmd_calcu_rate) * $_ex_rate_get ) / 100;
			$brd_a_calcu_price = (($brd_a_data[report_price] * $_brd_a_calcu_rate) * $_ex_rate_get ) / 100;
			$brd_b_calcu_price = (($brd_b_data[report_price] * $_brd_b_calcu_rate) * $_ex_rate_get ) / 100;
			$brd_c_calcu_price = (($brd_c_data[report_price] * $_brd_c_calcu_rate) * $_ex_rate_get ) / 100;
			$prl_calcu_price = ((($prl_data[report_price] / $_ex_rate) * $_prl_calcu_rate) * $_ex_rate_get) / 100;
			$muk_calcu_price = ($muk_data[report_price] * $_muk_calcu_rate)  / 100;

			$_calcu_price = (($_view_calculate_salas_dollar * $_tm_calcu_rate) * $_ex_rate_get)/ 100 ;
			$last_calcu_price = $gml_calcu_price + $brd_a_calcu_price + $brd_b_calcu_price + $brd_c_calcu_price + $prl_calcu_price + $muk_calcu_price + $_calcu_price;

			$_team_bouns = securityVal($team_bouns);

			$muk_bouns_rate =$muk_data[report_price] / $team_count;

			if($muk_bouns_rate > 4000 && $muk_bouns_rate < 6001){
				$muk_bouns = 500 * $team_count;
			}elseif($muk_bouns_rate > 6000){
				$muk_bouns = 1000 * $team_count;
			}else{
				$muk_bouns = 0;
			}
			
			$car_calcu_price = securityVal($car_calcu_price);
			$_balance_price = securityVal($balance_price);
			$_balance_date = securityVal($balance_date);
			$_deduction_price = securityVal($deduction_price);
			$_deduction_memo = securityVal($deduction_memo);

			$_calculate_food_memo = securityVal($calculate_food_memo);
			$_calculate_tour_memo = securityVal($calculate_tour_memo);
			$_unpid_bill_memo = securityVal($unpid_bill_memo);
			$_calculate_memo = securityVal($calculate_memo);
			$_calculate_admin_memo = securityVal($calculate_memo);

			$guide_cal_price = $last_calcu_price - $_view_balance_tour_fee - $car_calcu_price - $_deduction_price - $_balance_price + $muk_bouns + $_team_bouns;//실지급 총액
			
			$cal_shop_cal_late = $_gmd_calcu_rate."|".$_brd_a_calcu_rate."|".$_brd_b_calcu_rate."|".$_brd_c_calcu_rate."|".$_prl_calcu_rate."|".$_muk_calcu_rate."|".$_tm_calcu_rate;

			$_cal_code = $_ad_id."_".$wepix_now_time;

		$query = "insert into  "._DB_CALCULATE." set
			CAL_CODE = '".$_cal_code."',
            CAL_TYPE = '".$bkg_data[BKG_TYPE]."',
			CAL_STATE = '2' ,
			CAL_BKG_IDX ='".$_bkg_idx."',
			CAL_BKG_CODE = '".$bkg_data[BKG_CODE]."',
			CAL_BKP_IDX = '".$bkg_data[BKG_BKP_IDX]."' ,
			CAL_TEAM_COUNT = '".$team_count."',
			CAL_GD_ID = '".$guide_data[GD_ID]."',
			CAL_GD_NAME = '".$guide_data[GD_NAME]."',
			CAL_SHOP_CAL_LATE	 = '".$cal_shop_cal_late."',
			CAL_TOUR_BALANCE = '".$_balance_price."' ,
			CAL_TOUR_BALANCE_DATE = '".$_balance_date."',
			CAL_TEAM_BOUNS = '".$_team_bouns."' ,
			CAL_CAR_DEDUCTION = '".$car_calcu_price."',
			CAL_RATE	 = '".$_ex_rate."',
			CAL_SHOP_RATE = '".$_ex_rate_get."' ,
			CAL_DEDUCTION_PRICE = '".$_deduction_price."' ,
			CAL_DEDUCTION_MEMO = '".$_deduction_memo."' ,
			CAL_FOOD_MEMO = '".$_calculate_food_memo."' ,
			CAL_TOUR_MEMO = '".$_calculate_tour_memo."' ,
			CAL_UNPID_BILL_MEMO = '".$_unpid_bill_memo."' ,
			CAL_CALCU_MEMO =  '".$_calculate_memo."' ,
			CAL_CALCU_ADMIN_MEMO =  '".$_calculate_admin_memo."' ,
			
			CAL_REG_ID = '".$_ad_id."' ,
			CAL_REG_DATE = '".$wepix_now_time."'";

		$result = wepix_query_error($query);

		$_back_url = securityVal($back_url);

		// 부킹그룹 일정 종료
		$query = "update "._DB_BOOKING_GROUP." set 
					BKG_STATE = 'Y'
				where BKG_IDX = '".$_bkg_idx."' ";
		wepix_query_error($query);

		msg("등록 완료!",_A_PATH_GROUP_CALCUATE.$_back_url ."&idx=".$_bkg_idx);

		
// ******************************************************************************************************************
// 샵 매출 등록
// ******************************************************************************************************************

}elseif( $_action_mode == "temporaryCalculate" ){

			wepix_query_error("delete from "._DB_CALCULATE." where CAL_STATE = '1' and CAL_BKG_IDX = ".$_bkg_idx."");
			$bkg_data =  wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING_GROUP." where BKG_IDX = ".$_bkg_idx));

			$op_query = "select * from "._DB_GROUP_OTHER." where OP_BKG_IDX ='".$_bkg_idx."'";
			$op_result = wepix_query_error($op_query);
			
			$_ary_bkp_idx = explode(",",$bkg_data[BKG_BKP_IDX]);
			for($i=0;$i<count($_ary_bkp_idx);$i++){
				$bkp_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING." where BKP_IDX = ".$_ary_bkp_idx[$i]));
				$buy_pd_sum = wepix_fetch_array(wepix_query_error("select sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BK_IDX = '".$_ary_bkp_idx[$i]."' "));
				$_show_total_buy_pd = $buy_pd_sum[total_price];
				$_view_bkp_discount_rate[] = $bkp_data[BKP_DISCOUNT_RATE];
				$_view_bkp_total_tm += ($_show_total_buy_pd-$bkp_data[BKP_FIRST_MONEY]) * (100 - $bkp_data[BKP_DISCOUNT_RATE]) / 100;
			}

			$search_sql = "where  BP_PD_KIND ='10000000'  and BP_BK_IDX in  (".$bkg_data[BKG_BKP_IDX].")";
			$food_data_count = wepix_counter2($db_t_BUY_PRODUCT, $search_sql, "BP_PD_IDX");

			$buydata = wepix_query_error("select * from ".$db_t_BUY_PRODUCT."  ".$search_sql ." GROUP BY BP_PD_IDX");
			while($pdlist = wepix_fetch_array($buydata)){
						
                $bp_sum = wepix_fetch_array(wepix_query_error("select count(BP_IDX) as idx_count, sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."'  and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_cash = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'cash' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_credit = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'credit' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));



			   $_view_food_total_price += $bp_sum[total_price];
               $_view_food_total_cash_price += $bp_sum_cash[total_price];
               $_view_food_total_credit_price  += $bp_sum_credit[total_price];
			   $_view_total_bp_count += $bp_sum[idx_count];

			   $_show_food_reate_cost = $bp_sum_cash[rate_cost] + $bp_sum_credit[rate_cost];
			   $_view_food_total_rate_cost += $_show_food_reate_cost;
			}

			$search_sql_tour = "where  (BP_PD_KIND ='07000000' and BP_BK_IDX in  (".$bkg_data[BKG_BKP_IDX].")) or (BP_PD_KIND ='08000000'  and BP_BK_IDX in  (".$bkg_data[BKG_BKP_IDX]."))";
			$tour_data_count = wepix_counter2($db_t_BUY_PRODUCT, $search_sql_tour, "BP_PD_IDX");

			$buydata = wepix_query_error("select * from ".$db_t_BUY_PRODUCT."  ".$search_sql_tour ." GROUP BY BP_PD_IDX");
			while($pdlist = wepix_fetch_array($buydata)){
						
                $bp_sum = wepix_fetch_array(wepix_query_error("select count(BP_IDX) as idx_count , sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."'  and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_cash = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'cash' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_credit = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'credit' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

			   $_view_total_price += $bp_sum[total_price];
               $_view_total_cash_price += $bp_sum_cash[total_price];
               $_view_total_credit_price  += $bp_sum_credit[total_price];
			   $_view_total_count +=$bp_sum[idx_count];

			   $_show_tour_reate_cost = $bp_sum_cash[rate_cost] + $bp_sum_credit[rate_cost];
			   $_view_tour_total_rate_cost += $_show_tour_reate_cost;
			}

			while($op_list = wepix_fetch_array($op_result)){

				$_view_credit_sum += $op_list[OP_CREDIT];
				$_view_cash_sum += $op_list[OP_CASH]; 
				$_view_total_sum += $op_list[OP_TOTAL]; 
			}

			$_view_tour_fee_total = $bkg_data[BKG_TOTAL_TOUR_FEE]; // 지급받은 투어피
			$_view_tour_fee_cash = $_view_food_total_cash_price + $_view_total_cash_price + $_view_cash_sum; // 캐쉬 사용 투어피
			$_view_tour_fee_credit = $_view_food_total_credit_price + $_view_total_credit_price + $_view_credit_sum; //크레딧 사용 어피
			$_view_use_tour_fee = $_view_tour_fee_cash + $_view_tour_fee_credit; // 총사용 투어피
			$_view_balance_tour_fee = $_view_tour_fee_total - $_view_tour_fee_cash; // 투어피 발란스

			$_ex_rate  = securityVal($cal_rate);
			$_ex_rate_get = $cal_shop_rate;

			$_view_total_tm = $_view_food_total_price + $_view_total_price; //총사용 TM
			$_view_total_rate_cost_price = $_view_food_total_rate_cost + $_view_tour_total_rate_cost;	//총 인정 원가 지출
			$_view_rate_cast = round(($_view_total_rate_cost_price / $_view_total_tm) * 100); // 원가 지출률

			if($_view_rate_cast > 35){  //원가 지출율이 35이상일때 계산 (원가지출률 * 추가 t/m) + ((추가 t/m) * (원가지출율 - 35))/100)
				$_over_rate_cost = $_view_rate_cast - 35;
				$_over_cost_deduct = ($_view_bkp_total_tm * $_over_rate_cost) / 100;
				$_view_cost_deduct = ($_view_rate_cast * $_view_bkp_total_tm) / 100 + $_over_cost_deduct;

			}else{ //원가 지출율이 35이하일때 계산 (원가지출률 * 추가 t/m) / 100
				$_view_cost_deduct = ($_view_rate_cast * $_view_bkp_total_tm) / 100;
			}
			$_view_calculate_salas = $_view_bkp_total_tm - $_view_cost_deduct ;//정산매출
			$_view_concede_sales_rate = 100 - ( $_view_rate_cast - 25 ); //인정맨출률

			if($_view_concede_sales_rate >100){
				$_view_concede_sales_rate = 100;
			}
			if($_view_bkp_total_tm < 0){
				$_view_bkp_total_tm = 0;
			}
			if($_view_cost_deduct < 0){
				$_view_cost_deduct = 0;
			}
			if($_view_calculate_salas < 0){
				$_view_calculate_salas = 0;
			}

			$_view_bkp_total_tm_dollar = round($_view_bkp_total_tm / $_ex_rate); //추가 T/M 달러
			$_view_cost_deduct_dollar = round($_view_cost_deduct / $_ex_rate); // 원가공제 달러
			$_view_calculate_salas_dollar = round($_view_calculate_salas / $_ex_rate);  // 정산 매출 달러
			$_view_concede_sales_price_dollar = round($_view_bkp_total_tm_dollar * $_view_concede_sales_rate) / 100;

			$guide_data =  wepix_fetch_array(wepix_query_error("select GD_NAME,GD_ID,GD_NICK from "._DB_GUIDE." where GD_ID = '".$bkg_data[BKG_GID_ID]."' "));
			$_shop_st_date = date("Ymd",$bkg_data[BKG_START_DATE]);
			$_shop_ed_date = date("Ymd",$bkg_data[BKG_END_DATE]);
			$_shop_gd_name = $guide_data[GD_NAME];
			$_shop_sql = " and SS_BKG_IDX = '".$_bkg_idx."'  and SS_GUIDE_NAME = '".$_shop_gd_name."'";
			$gml_data   =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'GML' ".$_shop_sql.""));
			$brd_a_data =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'BRD' and SS_PRODUCT_KIND = 'A' ".$_shop_sql.""));
			$brd_b_data =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'BRD' and SS_PRODUCT_KIND = 'B' ".$_shop_sql.""));
			$brd_c_data =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'BRD' and SS_PRODUCT_KIND = 'C' ".$_shop_sql.""));
			$prl_data   =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'PRL' ".$_shop_sql.""));
			$muk_data   =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'MUK' ".$_shop_sql.""));

			$muk_dollar = $muk_data[report_price] / $_ex_rate;
			$prl_dollar = $prl_data[report_price] / $_ex_rate;

			$total_con_dollar = $gml_data[report_price] + $brd_a_data[report_price] + $brd_b_data[report_price] + $brd_c_data[report_price] + $muk_dollar + $_view_concede_sales_price_dollar + $prl_dollar;
			
			$team_count = securityVal($team_count); //부킹 팀수
			$shop_cal_rate = $total_con_dollar/$team_count; //쌍당매ㅜㄹ

			$_gmd_calcu_rate  = securityVal($gmd_rate);
			$_brd_a_calcu_rate = securityVal($brd_a_rate);
			$_brd_b_calcu_rate = securityVal($brd_b_rate);
			$_brd_c_calcu_rate = securityVal($brd_c_rate);
			$_prl_calcu_rate  = securityVal($prl_rate);
			$_muk_calcu_rate = securityVal($muk_rate);
			$_tm_calcu_rate = securityVal($tm_rate);

			$gml_calcu_price = (($gml_data[report_price] * $_gmd_calcu_rate) * $_ex_rate_get ) / 100;
			$brd_a_calcu_price = (($brd_a_data[report_price] * $_brd_a_calcu_rate) * $_ex_rate_get ) / 100;
			$brd_b_calcu_price = (($brd_b_data[report_price] * $_brd_b_calcu_rate) * $_ex_rate_get ) / 100;
			$brd_c_calcu_price = (($brd_c_data[report_price] * $_brd_c_calcu_rate) * $_ex_rate_get ) / 100;
			$prl_calcu_price = ((($prl_data[report_price] / $_ex_rate) * $_prl_calcu_rate) * $_ex_rate_get) / 100;
			$muk_calcu_price = ($muk_data[report_price] * $_muk_calcu_rate)  / 100;

			$_calcu_price = (($_view_calculate_salas_dollar * $_tm_calcu_rate) * $_ex_rate_get)/ 100 ;
			$last_calcu_price = $gml_calcu_price + $brd_a_calcu_price + $brd_b_calcu_price + $brd_c_calcu_price + $prl_calcu_price + $muk_calcu_price + $_calcu_price;

			$_team_bouns = securityVal($team_bouns);

			$muk_bouns_rate =$muk_data[report_price] / $team_count;

			if($muk_bouns_rate > 4000 && $muk_bouns_rate < 6001){
				$muk_bouns = 500 * $team_count;
			}elseif($muk_bouns_rate > 6000){
				$muk_bouns = 1000 * $team_count;
			}else{
				$muk_bouns = 0;
			}
			
			$car_calcu_price = securityVal($car_calcu_price);
			$_balance_price = securityVal($balance_price);
			$_balance_date = securityVal($balance_date);
			$_deduction_price = securityVal($deduction_price);
			$_deduction_memo = securityVal($deduction_memo);

			$_calculate_food_memo = securityVal($calculate_food_memo);
			$_calculate_tour_memo = securityVal($calculate_tour_memo);
			$_unpid_bill_memo = securityVal($unpid_bill_memo);
			$_calculate_memo = securityVal($calculate_memo);

			$guide_cal_price = $last_calcu_price - $_view_balance_tour_fee - $car_calcu_price - $_deduction_price - $_balance_price + $muk_bouns + $_team_bouns;//실지급 총액
			
			$cal_shop_cal_late = $_gmd_calcu_rate."|".$_brd_a_calcu_rate."|".$_brd_b_calcu_rate."|".$_brd_c_calcu_rate."|".$_prl_calcu_rate."|".$_muk_calcu_rate."|".$_tm_calcu_rate;

			$_cal_code = $_ad_id."_".$wepix_now_time;

		$query = "insert into  "._DB_CALCULATE." set
			CAL_CODE = '".$_cal_code."',
            CAL_TYPE = '".$bkg_data[BKG_TYPE]."',
			CAL_STATE = '1' ,
			CAL_BKG_IDX ='".$_bkg_idx."',
			CAL_BKG_CODE = '".$bkg_data[BKG_CODE]."',
			CAL_BKP_IDX = '".$bkg_data[BKG_BKP_IDX]."' ,
			CAL_TEAM_COUNT = '".$team_count."',
			CAL_GD_ID = '".$guide_data[GD_ID]."',
			CAL_GD_NAME = '".$guide_data[GD_NAME]."',
			CAL_SHOP_CAL_LATE	 = '".$cal_shop_cal_late."',
			CAL_TOUR_BALANCE = '".$_balance_price."' ,
			CAL_TOUR_BALANCE_DATE = '".$_balance_date."',
			CAL_TEAM_BOUNS = '".$_team_bouns."' ,
			CAL_CAR_DEDUCTION = '".$car_calcu_price."',
			CAL_RATE	 = '".$_ex_rate."',
			CAL_SHOP_RATE = '".$_ex_rate_get."' ,
			CAL_DEDUCTION_PRICE = '".$_deduction_price."' ,
			CAL_DEDUCTION_MEMO = '".$_deduction_memo."' ,
			CAL_FOOD_MEMO = '".$_calculate_food_memo."' ,
			CAL_TOUR_MEMO = '".$_calculate_tour_memo."' ,
			CAL_UNPID_BILL_MEMO = '".$_unpid_bill_memo."' ,
			CAL_CALCU_MEMO =  '".$_calculate_memo."' ,
			CAL_REG_ID = '".$_ad_id."' ,
			CAL_REG_DATE = '".$wepix_now_time."'";

		$result = wepix_query_error($query);
		$_back_url = securityVal($back_url);
		msg("임시저장 완료!",_A_PATH_GROUP_CALCUATE.$_back_url."&idx=".$_bkg_idx);
}

exit;
?>