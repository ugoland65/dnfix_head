<?
include "../lib/inc_common.php";
$pageGroup = "booking";
$pageName = "group_calculate_reg";

		$_bkg_idx = securityVal($idx);
		$_mode = securityVal($mode);

		$bkg_data =  wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING_GROUP." where BKG_IDX = ".$_bkg_idx));
		$cal_data = wepix_fetch_array(wepix_query_error("select * from "._DB_CALCULATE." where CAL_BKG_IDX = ".$_bkg_idx));
		
		if($cal_data[CAL_IDX]){	
			$_cal_mode = "Y";
		}else{
			$_cal_mode = "N";
		}

		$_view_group_idx = $bkg_data[BKG_IDX];
		$_view_group_type = $bkg_data[BKG_TYPE];
		$_view_group_name = $bkg_data[BKG_NAME];
		$_view_group_state = $bkg_data[BKG_STATE];
		$_view_group_head_count = $bkg_data[BKG_HEAD_COUNT];
		$_view_group_start_date = date("d-M-y",$bkg_data[BKG_START_DATE]);
		$_view_group_end_date = date("d-M-y",$bkg_data[BKG_END_DATE]);
		$_view_group_tour_fee = $bkg_data[BKG_TOUR_FEE];

		$_ary_bkp_idx = explode(",",$bkg_data[BKG_BKP_IDX]);

		

		for($i=0;$i<count($_ary_bkp_idx);$i++){
			$bkp_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING." where BKP_IDX = ".$_ary_bkp_idx[$i]));
			$_ary_bkp_hotel[] = $bkp_data[BKP_HOTEL]; //호텔
		    $_ary_schedule_day[] = $bkp_data[BKP_SCHEDULE_DAY];
			$_ary_bkp_start_date[] = date("d-M-y",$bkp_data[BKP_START_DATE]);
			$_ary_bkp_arrive_date[] = date("d-M-y",$bkp_data[BKP_ARRIVE_DATE]);
			$_ary_bkp_start_flight[] = str_replace(" ","",strtoupper($bkp_data[BKP_START_FLIGHT]));
			$_ary_bkp_arrive_flight[] = str_replace(" ","",strtoupper($bkp_data[BKP_ARRIVE_FLIGHT]));
			$_ary_bkp_team_name[] = $bkp_data[BKP_TEAM_NAME];
			$_ary_bkp_first_money[] = number_format($bkp_data[BKP_FIRST_MONEY]);

			$buy_pd_sum = wepix_fetch_array(wepix_query_error("select sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BK_IDX = '".$_ary_bkp_idx[$i]."' "));
			$_show_total_buy_pd = $buy_pd_sum[total_price];

			$_view_bkp_use_tm[] = number_format($_show_total_buy_pd); // 총사용 T.M
			$_view_bkp_over_tm[] = number_format($_show_total_buy_pd-$bkp_data[BKP_FIRST_MONEY]); //총 추가 T.M
			
			$_view_bkp_discount_rate[] = $bkp_data[BKP_DISCOUNT_RATE];
			$_view_bkp_charge_tm[] = number_format(($_show_total_buy_pd-$bkp_data[BKP_FIRST_MONEY]) * (100 - $bkp_data[BKP_DISCOUNT_RATE]) / 100);
			$_view_bkp_total_tm += ($_show_total_buy_pd-$bkp_data[BKP_FIRST_MONEY]) * (100 - $bkp_data[BKP_DISCOUNT_RATE]) / 100;
			

		}
		$exchange_sell_data = wepix_fetch_array( wepix_query_error("select * from "._DB_EXCHANGE_RATE." where ER_KIND = 'sell' order by ER_IDX desc limit 0,1"));
		$exchange_get_data = wepix_fetch_array( wepix_query_error("select * from "._DB_EXCHANGE_RATE." where ER_KIND = 'get' order by ER_IDX desc limit 0,1"));

		$_ex_rate = $exchange_sell_data[ER_DOLLAR_MONEY];
		$_ex_rate_get = $exchange_get_data[ER_DOLLAR_MONEY];


		if($_cal_mode == "Y"){
			$_ex_rate = $cal_data[CAL_RATE];
			$_ex_rate_get = $cal_data[CAL_SHOP_RATE];
		}


		//Query
		$op_query = "select * from "._DB_GROUP_OTHER." where OP_BKG_IDX ='".$_view_group_idx."'";
		$op_result = wepix_query_error($op_query);

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:100%; }
.table-style{ width:100%; }

</STYLE>
<script type='text/javascript'>

// 정수형 천단위 콤마 삽입
function Comma_int(input, obj) {
  input = unComma_int(input);

  var inputString = new String;
  var outputString = new String;
  var counter = 0;
  var decimalPoint = 0;
  var end = 0;
  var modval = 0;

  inputString = input.toString();
  outputString = '';
  decimalPoint = inputString.indexOf('.', 1);

  if(decimalPoint == -1) {
     end = inputString.length - (inputString.charAt(0)=='0' ? 1:0);
     for (counter=1;counter <=inputString.length; counter++)
     {
        var modval =counter - Math.floor(counter/3)*3;
        outputString = (modval==0 && counter <end ? ',' : '') + inputString.charAt(inputString.length - counter) + outputString;
     }
  }
  else {
     end = decimalPoint - ( inputString.charAt(0)=='-' ? 1 :0);
     for (counter=1; counter <= decimalPoint ; counter++)
     {
        outputString = (counter==0  && counter <end ? ',' : '') +  inputString.charAt(decimalPoint - counter) + outputString;
     }
     for (counter=decimalPoint; counter < decimalPoint+3; counter++)
     {
        outputString += inputString.charAt(counter);
     }
 }
    return (outputString);
}
</script>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">

<form name='formCalculate' action='<?=_A_PATH_GROUP_OK?>' method='post'>
		<input type='hidden' name='a_mode' value='newCalculate'>
		<input type='hidden' name='cal_rate' value='<?=$_ex_rate?>'>
		<input type='hidden' name='cal_shop_rate' value='<?=$_ex_rate_get?>'>
		<input type='hidden' name='key' value='<?=$_bkg_idx?>'>
<!--  그룹정보 -->
	<table cellspacing="1" cellpadding="0" class="table-style">
	  <tr>
		<th>그룹 번호</th>
		<th>그룹 타입</th>
		<th>그룹 이름</th>
		<th>진행 현황</th>
		<th>인원수</th>
		<th>그룹 시작날짜</th>
		<th>그룹 종료날짜</th>
		<th>지급 투어피</th>
	  </tr>
	  <tr>
		<td class="tds2"><?=$_view_group_idx?></td>
		<td class="tds2"><?=$_view_group_type?></td>
		<td class="tds2"><?=$_view_group_name?></td>
		<td class="tds2">END</td>
		<td class="tds2"><?=$_view_group_head_count?></td>
		<td class="tds2"><?=$_view_group_start_date?></td>
		<td class="tds2"><?=$_view_group_end_date?></td>
		<td class="tds2"><?=$_view_group_tour_fee?></td>
	  </tr>
	</table>

<br/><br/><br/>
<!--  팀정보시작 -->
	<table cellspacing="1" cellpadding="0" class="table-style">
	   <tr>
		<th>팀번호</th>
		<th>IN</th>
		<th>OUT</th>
		<th>Name</th>
		<th>Hotel</th>
		<th>Basic TM / Total TM</th>
		<th>Over TM / Receve TM</th>
	   </tr>
	  <?
		for($i=0;$i<count($_ary_bkp_idx);$i++){
				$_ary2_bkp_hotel = explode("│",$_ary_bkp_hotel[$i]);
			    $_ary2_schedule_day = explode("│",$_ary_schedule_day[$i]); 
				$_ary_bkp_hotel_name = array();
			for($a=0;$a<count($_ary2_bkp_hotel);$a++){
				$_ary2_hotel_info = explode(":",$_ary2_bkp_hotel[$a]);
				$_ary2_schedule = explode("/",$_ary2_schedule_day[$a]);
				$_ary_bkp_hotel_name[] = $_ary2_hotel_info[1]." ( ".$_ary2_schedule[0]."N )";
			}
			 $_view2_hot_name = implode(",",$_ary_bkp_hotel_name);
			 if($_view_bkp_over_tm[$i] <= 0){
			   $_view2_bkp_over_tm = 0;
			   $_view2_bkp_charge_tm =0;
			 }else{
			   $_view2_bkp_over_tm = $_view_bkp_over_tm[$i];
			   $_view2_bkp_charge_tm = $_view_bkp_charge_tm[$i];
			 }

	  ?>
	   <tr>
		<td class="tds2"><?=$_ary_bkp_idx[$i]?></th>
		<td class="tds2"><?=$_ary_bkp_start_date[$i]?> (<?=$_ary_bkp_start_flight[$i]?>)</td>
		<td class="tds2"><?=$_ary_bkp_arrive_date[$i]?> (<?=$_ary_bkp_arrive_flight[$i]?>)</td>
		<td class="tds2"><?=$_ary_bkp_team_name[$i]?></td>
		<td class="tds2"><?=$_view2_hot_name?></td>
		<td class="tds2"><?=$_ary_bkp_first_money[$i]?> (<?=$_view_bkp_use_tm[$i]?>)</td>
		<td class="tds2"><?=$_view2_bkp_over_tm?> (<?=$_view2_bkp_charge_tm?>)</td>
	   </tr>

	   
	  <?
		 }
	  ?>

	</table>
<br/><br/><br/>
<!--  식사정보시작 -->
	<table cellspacing="1" cellpadding="0" class="table-style" >
             <tr>
				<th class="tds1" style='width:80px;' height="30">순번</th>
				<th class="tds1" style='width:150px;' >식사</th>
				<th class="tds1" style='width:120px;' >T/M</th>
				<th class="tds1" style='width:120px;' >Cash</th>
				<th class="tds1" style='width:120px;' >Credit</th>
				<th class="tds1" style='width:120px;' >Count</th>
				<th class="tds1" >Memo</th>
			  </tr>
			   <?

						$food_span = 1;

						$search_sql = "where  BP_PD_KIND ='10000000'  and BP_BK_IDX in  (".$bkg_data[BKG_BKP_IDX].")";
						$food_data_count = wepix_counter2($db_t_BUY_PRODUCT, $search_sql, "BP_PD_IDX");


if($food_data_count == 0){
?>
			<tr>
				<td class="tds1" colspan="7" style="height:100px;">데이터가 없습니다.</td>
			 </tr>
<?
}else{

            
			$buydata = wepix_query_error("select * from ".$db_t_BUY_PRODUCT."  ".$search_sql ." GROUP BY BP_PD_IDX");
			while($pdlist = wepix_fetch_array($buydata)){
						
                $bp_sum = wepix_fetch_array(wepix_query_error("select count(BP_IDX) as idx_count, sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."'  and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_cash = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'cash' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_credit = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'credit' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

			   $_view_pd_name = $pdlist[BP_PD_NAME];
               $_view_price = number_format($bp_sum[total_price]);
               $_view_cash_price = number_format($bp_sum_cash[total_price]);
               $_view_credit_price = number_format($bp_sum_credit[total_price]);
			   $_view_bp_count = $bp_sum[idx_count];
			   $_view_food_total_price += $bp_sum[total_price];
               $_view_food_total_cash_price += $bp_sum_cash[total_price];
               $_view_food_total_credit_price  += $bp_sum_credit[total_price];
			   $_view_total_bp_count += $bp_sum[idx_count];
			   $_show_food_reate_cost = $bp_sum_cash[rate_cost] + $bp_sum_credit[rate_cost];
			   $_view_food_total_rate_cost += $_show_food_reate_cost;

?>
        

			<tr>
				<td class="tds1" height="22"><?=$food_span?></td>
				<td class="tds1"><?=$_view_pd_name?></td>
				<td class="tds1"><?=$_view_price ?> </td>
				<td class="tds1"><?=$_view_cash_price?></td>
				<td class="tds1"><?=$_view_credit_price?></td>
				<td class="tds1"><?=$_view_bp_count?></td>
				<td class="tds1"><?=$bp_sum[qty]?></td>
			 </tr>
<?$food_span++;
}  }
?>

			<tr>
				<th class="tds1" rowspan='3' style='width:250px;'>MEAL<br/>Total</th>
				<td class="tds2"  rowspan='3'></td>
				<th class="tds1" rowspan='2' style='width:150px;'>T/M</th>
				<th class="tds1" style='width:150px;'>Cash</th>
				<th class="tds1" style='width:150px;'> Credit</th>
				<th class="tds1" rowspan='2'  style='width:150px;'>Count</th>

			</tr>
			<tr>
				
				<td class="tds1">฿ <?=number_format($_view_food_total_cash_price) ?></td>
				<td class="tds1">฿ <?=number_format($_view_food_total_credit_price) ?></td>
			</tr>
			<tr>
				
				<td class="tds1">฿ <?=number_format($_view_food_total_price) ?></td>
				<td  class="tds1" colspan='2' style='text-align:center;'>฿ <?=number_format($_view_food_total_cash_price + $_view_food_total_credit_price) ?></td>
				<td class="tds1"><?=$_view_total_bp_count?></td>
			</tr>
		</table>
	<br/><br/><br/>
<!--  투어 정보 시작 -->
		<table cellspacing="1" cellpadding="0" class="table-style" >
             <tr>
				<th class="tds1" style='width:80px;' height="30">순번</th>
				<th class="tds1" style='width:150px;' >식사</th>
				<th class="tds1" style='width:120px;' >T/M</th>
				<th class="tds1" style='width:120px;' >Cash</th>
				<th class="tds1" style='width:120px;' >Credit</th>
				<th class="tds1" style='width:120px;' >Count</th>
				<th class="tds1" >Memo</th>
			  </tr>
			   <?

						$tour_span = 1;

						$search_sql_tour = "where  (BP_PD_KIND ='07000000' and BP_BK_IDX in  (".$bkg_data[BKG_BKP_IDX].")) or (BP_PD_KIND ='08000000'  and BP_BK_IDX in  (".$bkg_data[BKG_BKP_IDX]."))";
						$tour_data_count = wepix_counter2($db_t_BUY_PRODUCT, $search_sql_tour, "BP_PD_IDX");


if($tour_data_count == 0){
?>
			 <tr>
				<td class="tds1" colspan="7" style="height:100px;">데이터가 없습니다.</td>
			 </tr>
<?
}else{

            
			$buydata = wepix_query_error("select * from ".$db_t_BUY_PRODUCT."  ".$search_sql_tour ." GROUP BY BP_PD_IDX");
			while($pdlist = wepix_fetch_array($buydata)){
						
                $bp_sum = wepix_fetch_array(wepix_query_error("select count(BP_IDX) as idx_count , sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."'  and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_cash = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'cash' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				$bp_sum_credit = wepix_fetch_array(wepix_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data[BKG_CODE]."' and BP_PAYMENT_KIND = 'credit' and BP_PD_IDX='".$pdlist[BP_PD_IDX]."'  and BP_BK_IDX in   (".$bkg_data[BKG_BKP_IDX].")"));

				
			   $_view_pd_name = $pdlist[BP_PD_NAME];
               $_view_price = number_format($bp_sum[total_price]);
               $_view_cash_price = number_format($bp_sum_cash[total_price]);
               $_view_credit_price = number_format($bp_sum_credit[total_price]);
			   $_view_bp_count = $bp_sum[idx_count];

			   $_view_total_price += $bp_sum[total_price];
               $_view_total_cash_price += $bp_sum_cash[total_price];
               $_view_total_credit_price  += $bp_sum_credit[total_price];
			   $_view_total_count +=$bp_sum[idx_count];

			   $_show_tour_reate_cost = $bp_sum_cash[rate_cost] + $bp_sum_credit[rate_cost];
			   $_view_tour_total_rate_cost += $_show_tour_reate_cost;

?>
        

			<tr>
				<td class="tds1" height="22"><?=$tour_span?></td>
				<td class="tds1"><?=$_view_pd_name?></td>
				<td class="tds1"><?=$_view_price ?> </td>
				<td class="tds1"><?=$_view_cash_price?></td>
				<td class="tds1"><?=$_view_credit_price?></td>
				<td class="tds1"><?=$_view_bp_count ?></td>
				<td class="tds1"></td>
			 </tr>
<?$tour_span++;
}  }
?>

			<tr>
				<th class="tds1" rowspan='3' style='width:250px;'>TOUR<br/>Total</th>
				<td class="tds2"  rowspan='3'></td>
				<th class="tds1" rowspan='2' style='width:150px;'>T/M</th>
				<th class="tds1" style='width:150px;'>Cash</th>
				<th class="tds1" style='width:150px;'> Credit</th>
				<th class="tds1" rowspan='2'  style='width:150px;'>Count</th>

			</tr>
			<tr>
				
				<td class="tds1">฿ <?=number_format($_view_total_cash_price) ?></td>
				<td class="tds1">฿ <?=number_format($_view_total_credit_price) ?></td>
			</tr>
			<tr>
				
				<td class="tds1">฿ <?=number_format($_view_total_price) ?></td>
				<td class="tds1" colspan='2' style='text-align:center;'>฿ <?=number_format($_view_total_cash_price + $_view_total_credit_price) ?></td>
				<td class="tds1"><?=$_view_total_count?></td>
			</tr>
		</table>

				

<br/><br/><br/>
<!-- 기타 정보시작 -->
 <table cellspacing="1" cellpadding="0" class="table-style" >
             <tr>
				<th class="tds1" style='width:80px;' height="30">순번</th>
				<th class="tds1" style='width:150px;' >기타 대분류</th>
				<th class="tds1" style='width:120px;' >기타 중분류</th>
				<th class="tds1" style='width:120px;' >Credit</th>
				<th class="tds1" style='width:120px;' >Cash</th>
				<th class="tds1" style='width:120px;' >Tatal</th>
				<th class="tds1" >Memo</th>
			  </tr>

		<?
		$num=0;
		while($op_list = wepix_fetch_array($op_result)){
			$num++;
			$_view_dapth_1 = $op_list[OP_DAPTH_1];
			$_view_dapth_2 = $op_list[OP_DAPTH_2];
			$_view_credit = number_format($op_list[OP_CREDIT]);
			$_view_cash =  number_format($op_list[OP_CASH]); 
			$_view_total =  number_format($op_list[OP_TOTAL]); 
			$_view_memo = $op_list[OP_MEMO];
			$_view_credit_sum += $op_list[OP_CREDIT];
			$_view_cash_sum += $op_list[OP_CASH]; 
			$_view_total_sum += $op_list[OP_TOTAL]; 
			
		?>
		<tr>
			<td class="tds1"><?=$num?></td>
			<td class="tds1"><?=$_view_dapth_1?></td>
			<td class="tds1"><?=$_view_dapth_2?></td>
			<td class="tds1"><?=$_view_credit?></td>
			<td class="tds1"><?=$_view_cash?></td>
			<td class="tds1"><?=$_view_total?></td>
			<td class="tds1"><?=$_view_memo?></td>
		</tr>
	<?}?>
	
		<tr>
			<th class="tds1" rowspan='2'>기타 지출</th>
			<td class="tds1" rowspan='2' colspan='2'></td>
			<th class="tds1">Credit</th>
			<th class="tds1">Cash</th>
			<th class="tds1">Total</th>
		</tr>
		<tr>
			<td class="tds1">฿ <?=number_format($_view_credit_sum) ?></td>
			<td class="tds1">฿ <?=number_format($_view_cash_sum) ?></td>
			<td class="tds1">฿ <?=number_format($_view_total_sum) ?></td>
		</tr>
</table>

<br/><br/><br/>
<!-- 투어피 정보시작 -->


<?

	$_view_tour_fee_total = $bkg_data[BKG_TOUR_FEE]; // 지급받은 투어피
	$_view_tour_fee_cash = $_view_food_total_cash_price + $_view_total_cash_price + $_view_cash_sum; // 캐쉬 사용 투어피
	$_view_tour_fee_credit = $_view_food_total_credit_price + $_view_total_credit_price + $_view_credit_sum; //크레딧 사용 어피
	$_view_use_tour_fee = $_view_tour_fee_cash + $_view_tour_fee_credit; // 총사용 투어피
	$_view_balance_tour_fee = $_view_tour_fee_total - $_view_tour_fee_cash; // 투어피 발란스

	if($_view_balance_tour_fee < 0){
		$_show_balance_color = 'blue';
	}else{
		$_show_balance_color = 'red';
	}


 
?>
<table cellspacing="1" cellpadding="0" class="table-style" >
		<tr>
			<th class="tds1">총 지급받은 투어피</th>
			<th class="tds1">총 사용 투어피</th>
			<th class="tds1">Cash</th>
			<th class="tds1">Credit</th>
			<th class="tds1">투어피 발란스</th>
		</tr>
		<tr>
			<td class="tds2" style='text-align:center;'>฿ <?=number_format($_view_tour_fee_total)?></td>
			<td class="tds2" style='text-align:center;'>฿ <?=number_format($_view_use_tour_fee)?></td>
			<td class="tds2" style='text-align:center;'>฿ <?=number_format($_view_tour_fee_cash)?></td>
			<td class="tds2" style='text-align:center;'>฿ <?=number_format($_view_tour_fee_credit)?></td>
			<td class="tds2" style='text-align:center;'><span style='color:<?=$_show_balance_color?>'>฿ <?=number_format($_view_balance_tour_fee)?></span></td>
		</tr>
	</table>

<br/><br/><br/>

<!-- 청구서 -->


<?
		
	

 
?>

<table cellspacing="1" cellpadding="0" class="table-style" >

		<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:150px; margin:5px; float:right;" onclick="pu_ok('<?=$_bkg_idx?>','bill_all_cf','<?=$_bkg_idx?>')"> 
		<i class="fas fa-plus-circle"></i> 전체 입금확인
		</button>
		<tr>
			<th class="tds1">Team Name</th>
			<th class="tds1">화폐</th>
			<th class="tds1">환율</th>
			<th class="tds1">입금</th>
			<th class="tds1">환율 적용금</th>
			<th class="tds1">관리</th>
		</tr>
<?

		for($b=0;$b<count($_ary_bkp_idx);$b++){
			$bill_result = wepix_query_error("select * from "._DB_BILL_TRAVEL." where PU_BKP_IDX = '".$_ary_bkp_idx[$b]."' order by PU_IDX desc");

			$exchange_rate['D'] = "달러";
			$exchange_rate['W'] = "원화";
			$exchange_rate['G'] = "송금";
			$exchange_rate['B'] = "바트";

			$exchange_sb['D'] = "$";
			$exchange_sb['W'] = '₩';
			$exchange_sb['G'] = '₩';
			$exchange_sb['B'] = "฿";

			$bill_count = 0;
			while($bill_list = wepix_fetch_array($bill_result)){
				$bill_count++;
				$_view2_bill_kind = $bill_list[PU_KIND];
				$_view2_bill_exchange_rate = $bill_list[PU_EXCHANGE_RATE];
				$_view2_bill_defult_money = number_format($bill_list[PU_DEFULT_MONEY]);
				$_view2_bill_money = number_format($bill_list[PU_MONEY]);
				$_view2_bill_total_money += $bill_list[PU_MONEY];
				$_view2_bill_cf_yn = $bill_list[PU_ADMIN_CONFIRM];
				$_view2_bill_cf_yn_id = $bill_list[PU_ADMIN_CONFIRM_ID];
				$_view2_bill_cf_yn_date = date("d-M-y H:i",$bill_list[PU_ADMIN_CONFIRM_DATE]);

				if($_view2_bill_kind == 'D'){
					$_view_dor_total +=  $bill_list[PU_DEFULT_MONEY];
				}elseif($_view2_bill_kind == 'W'){
					$_view_won_total +=  $bill_list[PU_DEFULT_MONEY];
				}elseif($_view2_bill_kind == 'G'){
					$_view_bnk_total +=  $bill_list[PU_DEFULT_MONEY];
				}elseif($_view2_bill_kind == 'B'){
					$_view_thb_total +=  $bill_list[PU_DEFULT_MONEY];
				}
?>
				<tr>
					<td class="tds1"><?=$_ary_bkp_team_name[$b]?> - <?=$_ary_bkp_idx[$b]?></td>
					<td class="tds1"><?=$exchange_rate[$_view2_bill_kind]?></td>
					<td class="tds1"><?=$_view2_bill_exchange_rate?></td>
					<td class="tds1"> <?=$exchange_sb[$_view2_bill_kind]?> <b><?=$_view2_bill_defult_money?></b></td>
					<td class="tds1"><b>฿ <?=$_view2_bill_money?></b> </td>
					<td class="tds1">
					<?if($_view2_bill_cf_yn == 'N'){?>
						<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs m-t-3 width-50" onclick="pu_ok('<?=$bill_list[PU_IDX]?>','bill_part_cf','<?=$_bkg_idx?>')" >
						<i class="far fa-check-square"></i> C/F</button>
					<?}else{?>
						완료 - <?=$_view2_bill_cf_yn_id?> (<?=$_view2_bill_cf_yn_date?>)
					<?}?>
					</td>
				</tr>
<? } 

		}?>
<?		$bill_y_result = wepix_query_error("select * from "._DB_BILL_TRAVEL." where PU_BKG_IDX = '".$_bkg_idx."' and PU_ADMIN_CONFIRM = 'Y' order by PU_IDX asc");
			
			while($bill_y_list = wepix_fetch_array($bill_y_result)){

				$_view2_bill_y_kind = $bill_y_list[PU_KIND];

				if($_view2_bill_y_kind == 'D'){
					$_view_dor_y_total +=  $bill_y_list[PU_DEFULT_MONEY];
					$_view_dor_y_date = date("d-M-y H:i",$bill_y_list[PU_ADMIN_CONFIRM_DATE]);
				}elseif($_view2_bill_y_kind == 'W'){
					$_view_won_y_total +=  $bill_y_list[PU_DEFULT_MONEY];
					$_view_won_y_date = date("d-M-y H:i",$bill_y_list[PU_ADMIN_CONFIRM_DATE]);
				}elseif($_view2_bill_y_kind == 'G'){
					$_view_bnk_y_total +=  $bill_y_list[PU_DEFULT_MONEY];
					$_view_bnk_y_date = date("d-M-y H:i",$bill_y_list[PU_ADMIN_CONFIRM_DATE]);
				}elseif($_view2_bill_y_kind == 'B'){
					$_view_thb_y_total +=  $bill_y_list[PU_DEFULT_MONEY];
					$_view_thb_y_date = date("d-M-y H:i",$bill_y_list[PU_ADMIN_CONFIRM_DATE]);
				}
			}
			
			$_show_thb_color = 'blue';
			$_show_won_color = 'blue';
			$_show_bnk_color = 'blue';
			$_show_dor_color = 'blue';

			if($_view_thb_total > $_view_thb_y_total) $_show_thb_color = 'red';
			if($_view_won_total > $_view_won_y_total) $_show_won_color = 'red';
			if($_view_bnk_total > $_view_bnk_y_total) $_show_bnk_color = 'red';
			if($_view_dor_total > $_view_dor_y_total) $_show_dor_color = 'red';

			if($_view_thb_y_total != 0)$_view_bill_thb_history = "฿".number_format($_view_thb_y_total)." (".$_view_thb_y_date.")";
			if($_view_won_y_total != 0)$_view_bill_won_history = "₩".number_format($_view_won_y_total)." (".$_view_won_y_date.")";
			if($_view_bnk_y_total != 0)$_view_bill_bnk_history = "₩".number_format($_view_bnk_y_total)." (".$_view_bnk_y_date.")";
			if($_view_dor_y_total != 0)$_view_bill_dor_history = "$".number_format($_view_dor_y_total)." (".$_view_dor_y_date.")";
?>
	</table>
	<br/>
	<table cellspacing="1" cellpadding="0" class="table-style" >
		<tr>
			<th class="tds1" rowspan='2'>입금 금액 Total</th>
			<th class="tds1">바트 현금</th>
			<th class="tds1">바트 송금</th>
			<th class="tds1">원화 현금</th>
			<th class="tds1">원화 송금</th>
			<th class="tds1">달러 현금</th>
			<th class="tds1"></th>
		</tr>

		<tr>
			<td class="tds1">฿ <span style='color:<?=$_show_thb_color?>'><?=number_format($_view_thb_total)?></span></td>
			<td class="tds1"></td>
			<td class="tds1">₩ <span style='color:<?=$_show_won_color?>'><?=number_format($_view_won_total)?></span></td>
			<td class="tds1">₩ <span style='color:<?=$_show_bnk_color?>'><?=number_format($_view_bnk_total)?></span></td>
			<td class="tds1">$ <span style='color:<?=$_show_dor_color?>'><?=number_format($_view_dor_total)?></span></td>
		</tr>

		<tr>
			<td class="tds1">입금금액 (입금일)</td>
			<td class="tds1"><?=$_view_bill_thb_history?></span></td>
			<td class="tds1"></td>
			<td class="tds1"><?=$_view_bill_won_history?></td>
			<td class="tds1"><?=$_view_bill_bnk_history?></td>
			<td class="tds1"><?=$_view_bill_dor_history?></td>
		</tr>
	</table>

<br/><br/><br/>

<!-- TM 정산 -->
<?

	
	

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



?>
	<table cellspacing="1" cellpadding="0" class="table-style">
		<tr>
			<th class="tds1" rowspan='3'>T / M <br/> 정산</th>
			<td class="tds1">총 사용 T/M</td>
			<td class="tds1">฿ <?=number_format($_view_total_tm)?></td>
			<th class="tds1">추가 T/M</th>
			<th class="tds1">원가 공제</th>
			<th class="tds1">정산 매출</th>
			<th class="tds1">인정 매출</th>
		</tr>
		<tr>
			<td class="tds1">총 인정 원가지출</td>
			<td class="tds1">฿ <?=number_format($_view_total_rate_cost_price)?></td>
			<td class="tds1">฿ <?=number_format($_view_bkp_total_tm)?></td>
			<td class="tds1">฿ <?=number_format($_view_cost_deduct)?></td>
			<td class="tds1">฿ <?=number_format($_view_calculate_salas)?></td>
			<td class="tds1"><?=$_view_concede_sales_rate?>%</td>
		</tr>
		<tr>
			<td class="tds1">원가 지출률</td>
			<td class="tds1"><?=$_view_rate_cast?>%</td>
			<td class="tds1">$ <?=number_format($_view_bkp_total_tm_dollar)?></td>
			<td class="tds1">$ <?=number_format($_view_cost_deduct_dollar)?></td>
			<td class="tds1">$ <?=number_format($_view_calculate_salas_dollar)?></td>
			<td class="tds1">$ <?=number_format($_view_concede_sales_price_dollar)?></td>
		</tr>
	</table>


<br/><br/><br/>
<!-- 샵 정보시작 -->

<?	
	$guide_data =  wepix_fetch_array(wepix_query_error("select GD_NAME from "._DB_GUIDE." where GD_ID = '".$bkg_data[BKG_GID_ID]."' "));
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
	$saw_data   =  wepix_fetch_array(wepix_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'SAW' ".$_shop_sql.""));
	$muk_dollar = $muk_data[report_price] / $_ex_rate;
	$prl_dollar = $prl_data[report_price] / $_ex_rate;
	
	$gml_report_pirce = $gml_data[report_price];
	$brd_a_report_pirce = $brd_a_data[report_price];
	$brd_b_report_pirce = $brd_b_data[report_price];
	$brd_c_report_pirce = $brd_c_data[report_price];
	$saw_report_pirce = $saw_data[report_price];
	$muk_report_pirce = $muk_data[report_price];
	$prl_report_pirce = $prl_dollar[report_price];

	if($gml_data[report_price] == ''){
		$gml_report_pirce = 0;
	}
	if($brd_a_data[report_price] == ''){
		$brd_a_report_pirce = 0;
	}
	if($brd_b_data[report_price] == ''){
		$brd_b_report_pirce = 0;
	}
	if($brd_c_data[report_price] == ''){
		$brd_c_report_pirce = 0;
	}
	if($prl_dollar[report_price] == ''){
		$prl_report_pirce = 0;
	}
	if($muk_data[report_price] == ''){
		$muk_report_pirce = 0;
	}

	$total_con_dollar = $gml_data[report_price] + $brd_a_data[report_price] + $brd_b_data[report_price] + $brd_c_data[report_price] + $muk_dollar + $_view_concede_sales_price_dollar + $prl_dollar;
	
	$team_count = count($_ary_bkp_idx); //부킹 팀수
	$shop_cal_rate = $total_con_dollar/$team_count; //쌍당매ㅜㄹ


	$_max_calculate_kind = array(0,1101,1301,1501,1801,2301,2501,2701,2901,3401);
	for($i=0;$i<count($_max_calculate_kind);$i++){
		$_max_num = $i-1;
		if($_max_calculate_kind[$i] > $shop_cal_rate){
			$_select_criteria = $_max_calculate_kind[$_max_num];
			break;
		}elseif($shop_cal_rate > 3401){
			$_select_criteria = 3401;
		}

	}
	

	if($_cal_mode == "Y"){
		$_cal_rate = explode("|",$cal_data[CAL_SHOP_CAL_LATE]);
		$_gmd_calcu_rate = $_cal_rate[0];
		$_brd_a_calcu_rate = $_cal_rate[1];
		$_brd_b_calcu_rate = $_cal_rate[2];
		$_brd_c_calcu_rate = $_cal_rate[3];
		$_prl_calcu_rate = $_cal_rate[4];
		$_muk_calcu_rate = $_cal_rate[5];
		$_tm_calcu_rate = $_cal_rate[6];
	}else{
		$cal_data_gml  = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='GML' and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
		$cal_data_prl  = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='PRL' and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
		$cal_data_muk  = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='MUK' and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
		$cal_data_saw  = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='SAW' and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
		$cal_data_brd_a = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='BRD' and ASC_SHOP_KIND = 'A'  and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
		$cal_data_brd_b = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='BRD' and ASC_SHOP_KIND = 'B' and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
		$cal_data_brd_c = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='BRD' and ASC_SHOP_KIND = 'C'  and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
		$cal_data_tm = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='TM'  and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));

		$_gmd_calcu_rate = $cal_data_gml[ASC_CRITERIA_CALCULATE];
		$_prl_calcu_rate = $cal_data_prl[ASC_CRITERIA_CALCULATE];
		$_muk_calcu_rate = $cal_data_muk[ASC_CRITERIA_CALCULATE];
		$_saw_calcu_rate = $cal_data_saw[ASC_CRITERIA_CALCULATE];
		$_brd_a_calcu_rate = $cal_data_brd_a[ASC_CRITERIA_CALCULATE];
		$_brd_b_calcu_rate = $cal_data_brd_b[ASC_CRITERIA_CALCULATE];
		$_brd_c_calcu_rate = $cal_data_brd_c[ASC_CRITERIA_CALCULATE];
		$_tm_calcu_rate = $cal_data_tm[ASC_CRITERIA_CALCULATE];
	}
	
	$gml_calcu_price = (($gml_data[report_price] * $_gmd_calcu_rate) * $_ex_rate_get ) / 100;
	$brd_a_calcu_price = (($brd_a_data[report_price] * $_brd_a_calcu_rate) * $_ex_rate_get ) / 100;
	$brd_b_calcu_price = (($brd_b_data[report_price] * $_brd_b_calcu_rate) * $_ex_rate_get ) / 100;
	$brd_c_calcu_price = (($brd_c_data[report_price] * $_brd_c_calcu_rate) * $_ex_rate_get ) / 100;
	$prl_calcu_price = ((($prl_data[report_price] / $_ex_rate) * $_prl_calcu_rate) * $_ex_rate_get) / 100;
	$muk_calcu_price = ($muk_data[report_price] * $_muk_calcu_rate)  / 100;

	$_calcu_price = (($_view_calculate_salas_dollar * $_tm_calcu_rate) * $_ex_rate_get)/ 100 ;
	$last_calcu_price = $gml_calcu_price + $brd_a_calcu_price + $brd_b_calcu_price + $brd_c_calcu_price + $prl_calcu_price + $muk_calcu_price + $_calcu_price;
	
?>

		
	<table cellspacing="1" cellpadding="0" class="table-style">
		<tr>
			<th class="tds1">쇼핑업체</th>
			<th class="tds1" colspan='2'>LATEX</th>
			<th class="tds1" colspan='6'>BRD</th>
			<th class="tds1" colspan='2'>P F</th>
			<th class="tds1" colspan='2'>MUKDARA SPA</th>
			<th class="tds1" colspan='2'>T/M</th>
			<th>TOTAL</th>
		</tr>
		<tr>
			<td class="tds2">매출</td>
			<td class="tds2" colspan='2'>$<?=number_format($gml_data[report_price])?></td>
			<td class="tds2">A</td>
			<td class="tds2">$<?=number_format($brd_a_data[report_price])?></td>
			<td class="tds2">B</td>
			<td class="tds2">$<?=number_format($brd_b_data[report_price])?></td>
			<td class="tds2">C</td>
			<td class="tds2">$<?=number_format($brd_c_data[report_price])?></td>
			<td class="tds2">฿<?=number_format($prl_data[report_price])?></td>
			<td class="tds2">$<?=number_format($prl_data[report_price] / $_ex_rate)?></td>
			<td class="tds2">฿<?=number_format($muk_data[report_price])?></td>
			<td class="tds2">$<?=number_format($muk_data[report_price] / $_ex_rate)?></td>
			<td class="tds2">$<?=number_format($_view_calculate_salas_dollar)?> </td>
			<td class="tds2">$<?=number_format($_view_concede_sales_price_dollar)?> </td>
			<td class="tds2">$<?=number_format($total_con_dollar)?></td>
		</tr>
		<tr>
			<td class="tds2">COM</td>
			<td class="tds2"><input type='text' name='gmd_rate' id='gmd_rate' style='width:30px;' onchange="changeRate();" value='<?=$_gmd_calcu_rate?>'>%</td>
			<td class="tds2">฿ <span id='gml_calcu_price'><?=number_format($gml_calcu_price)?></span></td>
			<td class="tds2"><input type='text' name='brd_a_rate' id='brd_a_rate' style='width:30px;' onchange="changeRate();" value='<?=$_brd_a_calcu_rate?>'>%</td>
			<td class="tds2">฿ <span id='brd_a_calcu_price'><?=number_format($brd_a_calcu_price)?></span></td>
			<td class="tds2"><input type='text' name='brd_b_rate' id='brd_b_rate' style='width:30px;' onchange="changeRate();" value='<?=$_brd_b_calcu_rate?>'>%</td>
			<td class="tds2">฿ <span id='brd_b_calcu_price'><?=number_format($brd_b_calcu_price)?></span></td>
			<td class="tds2"><input type='text' name='brd_c_rate' id='brd_c_rate' style='width:30px;' onchange="changeRate();" value='<?=$_brd_c_calcu_rate?>'>%</td>
			<td class="tds2">฿ <span id='brd_c_calcu_price'><?=number_format($brd_c_calcu_price)?></span></td>
			<td class="tds2"><input type='text' name='prl_rate' id='prl_rate' style='width:30px;' onchange="changeRate();" value='<?=$_prl_calcu_rate?>'>%</td>
			<td class="tds2">฿ <span id='prl_calcu_price'><?=number_format($prl_calcu_price)?></span></td>
			<td class="tds2"><input type='text' name='muk_rate' id='muk_rate' style='width:30px;' onchange="changeRate();" value='<?=$_muk_calcu_rate?>'>%</td>
			<td class="tds2">฿ <span id='muk_calcu_price'><?=number_format($muk_calcu_price)?></span></td>
			<td class="tds2"><input type='text' name='tm_rate' id='tm_rate' style='width:30px;' onchange="changeRate();" value='<?=$_tm_calcu_rate?>'>%</td>
			<td class="tds2">฿ <span id='tm_calcu_price'><?=number_format($_calcu_price)?></span></td>
			<td class="tds2">฿ <span id='last_calcu_price'><?=number_format($last_calcu_price)?></span></td>
		</tr>
	</table>

<br/> <br/> <br/>
<!-- 최종 정산 -->

<?
	if($_cal_mode == "Y"){
		$_team_bouns = $cal_data[CAL_TEAM_BOUNS];
	}else{
		if($shop_cal_rate > 2900  && $shop_cal_rate < 3501){
			$_team_bouns = 1000 * $team_count;
		}elseif($shop_cal_rate > 3500){
			$_team_bouns = 2000 * $team_count;
		}else{
			$_team_bouns = 0;
		} 
	}

	

    $muk_bouns_rate =$muk_data[report_price] / $team_count;

	if($muk_bouns_rate > 3999 && $muk_bouns_rate < 6000){
		$muk_bouns = 500 * $team_count;
	}elseif($muk_bouns_rate > 5999){
		$muk_bouns = 1000 * $team_count;
	}else{
		$muk_bouns = 0;
	}

	if($_cal_mode == "Y"){
		$car_calcu_price = $cal_data[CAL_CAR_DEDUCTION];
		$deduction_price =  $cal_data[CAL_DEDUCTION_PRICE];
		$deduction_memo = $cal_data[CAL_DEDUCTION_MEMO];
		$balance_price = $cal_data[CAL_TOUR_BALANCE];
		$balance_date =  $cal_data[CAL_TOUR_BALANCE_DATE];

	}else{
		$car_calcu_price = $team_count * 2000;
		$deduction_price = 0;
		$balance_price = 0;
	}

	$guide_cal_price = $last_calcu_price - $_view_balance_tour_fee - $car_calcu_price - $deduction_price - $balance_price + $muk_bouns + $_team_bouns;//실지급 총액

?>
	<table style='float:left;'>
			
		<tr>
			<th rowspan='2' class="tds1">적용환율</th>
			<th class="tds1">총인원</th>
			<th class="tds1">쌍당매출</th>
			<th class="tds1">묵다라 보너스</th>	
			<th class="tds1">투어피발란스</th>
			<th class="tds1">기타</th>
			<th class="tds1">정산 총액</th>
		</tr>
		<tr>
			<td class="tds1"><?=$team_count?> 팀</td>
			<td class="tds1">$ <?=number_format($shop_cal_rate)?></td>
			<td class="tds1">฿ <?=number_format($muk_bouns)?></td>
			<td class="tds1"><span style='color:<?=$_show_balance_color?>'>฿ <?=number_format($_view_balance_tour_fee)?></span></td>
			<td class="tds1"><input type='text' name='deduction_memo' value='<?=$deduction_memo?>'></td>
			<th class="tds1">฿ <span id='last_calcu_price2'><?=number_format($last_calcu_price)?></span></th>
		</tr>
		<tr>
			<th rowspan='2' class="tds1"><?=$_ex_rate_get?></th>
			<th class="tds1">차량비공제</th>
			<td class="tds1">฿ <input type='text' name='car_calcu_price' id='car_calcu_price' style='width:50px;' onchange="changeRate();" value='<?=$car_calcu_price?>'></td>
			<th class="tds1" colspan='2'>발란스 금액</th>

			<th class="tds1">공제금액</th>
			<th class="tds1">실지급 총액</th>
		</tr>
		<tr>
			<th class="tds1">쌍당 보너스</th>
			<td class="tds1">฿ <input type='text' style='width:50px;' id='team_bouns' name='team_bouns'  onchange="changeRate();" value='<?=$_team_bouns?>'></td>
			<td class="tds1">  <input type='text' style='width:80px;'  id="search_st" name='balance_date' readonly value='<?=$balance_date?>'></td>
			<td class="tds1">  <input type='text' value='<?=$balance_price?>' style='width:50px;' name='balance_price' id='balance_text'  onchange="changeRate();"></td>			
			<td class="tds1">  <input type='text' style='width:70px;' name='deduction_price' id='deduction_price' value='<?=$deduction_price?>' onchange="changeRate();"></td>
			<td class="tds1">฿ <span id='guide_cal_price'><?=number_format($guide_cal_price)?></span></td>
		</tr>
	</table>


	<table style='float:left;'>
		<tr>
			<th style='width:50px;'>SALEL</th>
			<th style='width:50px;'>LATEX</th>
			<th style='width:50px;'>BRD A</th>
			<th style='width:50px;'>BRD B</th>
			<th style='width:50px;'>BRD C</th>
			<th style='width:50px;'>PFA</th>
			<th style='width:50px;'>MUK</th>
			<th style='width:50px;'>T/M</th>
		</tr>
		<?for($a=1;$a<count($_max_calculate_kind);$a++){
			
			$cal_data_gml  = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='GML' and ASC_CRITERIA_MONEY = '".$_max_calculate_kind[$a]."'"));
			$cal_data_prl  = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='PRL' and ASC_CRITERIA_MONEY = '".$_max_calculate_kind[$a]."'"));
			$cal_data_muk  = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='MUK' and ASC_CRITERIA_MONEY = '".$_max_calculate_kind[$a]."'"));
			$cal_data_saw  = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='SAW' and ASC_CRITERIA_MONEY = '".$_max_calculate_kind[$a]."'"));
			$cal_data_brd_a = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='BRD' and ASC_SHOP_KIND = 'A'  and ASC_CRITERIA_MONEY = '".$_max_calculate_kind[$a]."'"));
			$cal_data_brd_b = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='BRD' and ASC_SHOP_KIND = 'B' and ASC_CRITERIA_MONEY = '".$_max_calculate_kind[$a]."'"));
			$cal_data_brd_c = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='BRD' and ASC_SHOP_KIND = 'C'  and ASC_CRITERIA_MONEY = '".$_max_calculate_kind[$a]."'"));
			$cal_data_tm = wepix_fetch_array(wepix_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='TM'  and ASC_CRITERIA_MONEY = '".$_max_calculate_kind[$a]."'"));
			?>

		<tr>
			<th>$<?=number_format($_max_calculate_kind[$a])?></th>
			<th><?=number_format($cal_data_gml[ASC_CRITERIA_CALCULATE])?>%</th>
			<th><?=number_format($cal_data_brd_a[ASC_CRITERIA_CALCULATE])?>%</th>
			<th><?=number_format($cal_data_brd_b[ASC_CRITERIA_CALCULATE])?>%</th>
			<th><?=number_format($cal_data_brd_c[ASC_CRITERIA_CALCULATE])?>%</th>
			<th><?=number_format($cal_data_prl[ASC_CRITERIA_CALCULATE])?>%</th>
			<th><?=number_format($cal_data_muk[ASC_CRITERIA_CALCULATE])?>%</th>
			<th><?=number_format($cal_data_tm[ASC_CRITERIA_CALCULATE])?>%</th>
		</tr>	
		<?}?>
		
	</table>
</form>
			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_SHOP_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doCalCuSubmit('<?=$_cal_mode?>');" > 
						<i class="far fa-check-circle"></i>
						정산서 저장
					</button>
				</ul>
			</div>
		</div>
		<div style="height:60px;"></div>
	</div>
</div>

<script type="text/javascript"> 
<!-- 

function changeRate(){
	var view_calculate_salas_dollar = <?= $_view_calculate_salas_dollar ?>;
	var view_balance_tour_fee = <?=$_view_balance_tour_fee?>;
	var ex_rate = <?= $_ex_rate?>;
	var ex_rate_get = <?= $_ex_rate_get ?>;
	var gml_re_price = <?=$gml_report_pirce?>;
	var brd_a_re_price = <?=$brd_a_report_pirce?>;
	var brd_b_re_price = <?=$brd_b_report_pirce?>;
	var brd_c_re_price = <?=$brd_c_report_pirce?>;
	var prl_re_price = <?=$prl_report_pirce?>;
	var muk_re_price = <?=$muk_report_pirce?>;


	var muk_bouns = <?=$muk_bouns?>;

	var gmd_rate = $("#gmd_rate").val();
	var brd_a_rate = $("#brd_a_rate").val();
	var brd_b_rate = $("#brd_b_rate").val();
	var brd_c_rate = $("#brd_c_rate").val();
	var prl_rate = $("#prl_rate").val();
	var muk_rate = $("#muk_rate").val();
	var tm_rate = $("#tm_rate").val();

	

	var car_calcu_price = $("#car_calcu_price").val();
	var deduction_price = $("#deduction_price").val();
	var balance_text = 1*$("#balance_text").val();
	var team_bouns = $("#team_bouns").val();
	

	var gml_calcu_price = ((gml_re_price * gmd_rate) * ex_rate_get) / 100;
	var brd_a_calcu_price = ((brd_a_re_price * brd_a_rate) * ex_rate_get) / 100;
	var brd_b_calcu_price = ((brd_b_re_price * brd_b_rate) * ex_rate_get) / 100;
	var brd_c_calcu_price = ((brd_c_re_price * brd_c_rate) * ex_rate_get) / 100;
	var prl_calcu_price = (((prl_re_price / ex_rate) * prl_rate) * ex_rate_get) / 100;
	var muk_calcu_price = (muk_re_price * muk_rate) / 100;
	var calcu_price = ((view_calculate_salas_dollar * tm_rate) * ex_rate_get) / 100;
	var last_calcu_price = gml_calcu_price + brd_a_calcu_price + brd_b_calcu_price + brd_c_calcu_price + prl_calcu_price + muk_calcu_price + calcu_price ;

	var guide_cal_price =  1*(last_calcu_price) - 1*(view_balance_tour_fee) - 1*(car_calcu_price) - 1*(deduction_price) - 1*(balance_text) + 1*(team_bouns) + 1*(muk_bouns);
	
	//alert("last_calcu_price = "+last_calcu_price+" , view_balance_tour_fee = "+view_balance_tour_fee+" , car_calcu_price = "+car_calcu_price+" , deduction_price = "+deduction_price+" , balance_text = //"+balance_text+" ,team_bouns = "+team_bouns+" , "+" ,muk_bouns = "+muk_bouns+" , ");

	$("#gml_calcu_price").html(Comma_int(Math.round(gml_calcu_price)));
	$("#brd_a_calcu_price").html(Comma_int(Math.round(brd_a_calcu_price)));
	$("#brd_b_calcu_price").html(Comma_int(Math.round(brd_b_calcu_price)));
	$("#brd_c_calcu_price").html(Comma_int(Math.round(brd_c_calcu_price)));
	$("#prl_calcu_price").html(Comma_int(Math.round(prl_calcu_price)));
	$("#muk_calcu_price").html(Comma_int(Math.round(muk_calcu_price)));
	$("#tm_calcu_price").html(Comma_int(Math.round(calcu_price)));
	$("#last_calcu_price").html(Comma_int(Math.round(last_calcu_price)));
	$("#last_calcu_price2").html(Comma_int(Math.round(last_calcu_price)));
	$("#guide_cal_price").html(Comma_int(Math.round(guide_cal_price)));

}

function pu_ok(key,kind,bak){
   location.href="<?=_A_PATH_BOOKING_OK?>?key="+key+"&a_mode="+kind+"&bak="+bak;
}

// Submit.

function doCalCuSubmit(cal_mode){


	if(cal_mode == 'Y'){
		if( confirm( '정산서가 존재합니다. 한번더 생성하시겠습니까?')){
			var form = document.formCalculate;
			form.submit();
		}
	}else{
		var form = document.formCalculate;
		form.submit();
	}

}
</script>
<?
include "../layout/footer.php";
exit;
?>
 