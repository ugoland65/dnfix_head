<?
include "../lib/inc_common.php";

$pageGroup = "booking";
$pageName = "group_calculate_reg";

		$_bkg_idx = securityVal($idx);
		$_mode = securityVal($mode);
		
		$_submit_btn_color = 'btnstyle1-primary';
		$page_link_text = "?list=save";
		if($s_day){
			$page_link_text .= "&s_day=".$s_day;
		}
		if($e_day){
			$page_link_text .= "&e_day=".$e_day;
		}
		if($search_type){
			$page_link_text .= "&search_type=".$search_type;
		}
		if($search_guide){
			$page_link_text .= "&search_guide=".$search_guide;
		}


		$bkg_data =  sql_fetch_array(sql_query_error("select * from "._DB_BOOKING_GROUP." where BKG_IDX = ".$_bkg_idx));
		$cal_data = sql_fetch_array(sql_query_error("select * from "._DB_CALCULATE." where CAL_BKG_IDX = ".$_bkg_idx." order by CAL_IDX desc"));
		$guide_data =  sql_fetch_array(sql_query_error("select GD_NAME from "._DB_GUIDE." where GD_ID = '".$bkg_data['BKG_GID_ID']."' "));

		$_submit_btn_name = '정산서 저장';

		$_cal_mode = "N";
		if( $cal_data['CAL_IDX'] ){	
			$_cal_mode = "Y";
			$_view_cal_state = $cal_data['CAL_STATE'];
			$_view_cal_food_memo = $cal_data['CAL_FOOD_MEMO'];
			$_view_cal_tour_memo = $cal_data['CAL_TOUR_MEMO'];
			$_view_cal_bill_memo = $cal_data['CAL_UNPID_BILL_MEMO'];
			$_view_cal_memo = $cal_data['CAL_CALCU_MEMO'];
			$_view_cal_admin_memo = $cal_data['CAL_CALCU_ADMIN_MEMO'];
				
			if($_view_cal_state == 2){
				$_submit_btn_name = '정산서 수정';
				$_submit_btn_color = 'btnstyle1-success';
			}
		}


		$_view_group_idx = $bkg_data['BKG_IDX'];
		$_view_group_type = $bkg_data['BKG_TYPE'];
		$_view_group_name = $bkg_data['BKG_NAME'];
		$_view_group_state = $bkg_data['BKG_STATE'];
		$_view_group_head_count = $bkg_data['BKG_HEAD_COUNT'];
		$_view_group_start_date = date("d-M-y",$bkg_data['BKG_START_DATE']);
		$_view_group_end_date = date("d-M-y",$bkg_data['BKG_END_DATE']);
		$_view_group_tour_fee = number_format($bkg_data['BKG_TOTAL_TOUR_FEE']);
		$_ary_tour_fee_history =  explode("|",$bkg_data['BKG_TOUR_FEE']);
		$_ary_tour_fee_history_date =  explode("|",$bkg_data['BKG_TOUR_FEE_DATE']);
		$_ary_tour_fee_history_id =  explode("|",$bkg_data['BKG_TOUR_FEE_ID']);
		$_ary_bkp_idx = explode(",",$bkg_data['BKG_BKP_IDX']);


		for($i=0;$i<count($_ary_bkp_idx);$i++){
			$bkp_data = sql_fetch_array(sql_query_error("select * from "._DB_BOOKING." where BKP_IDX = ".$_ary_bkp_idx[$i]));
			$_ary_bkp_hotel[] = $bkp_data['BKP_HOTEL']; //호텔
		    $_ary_schedule_day[] = $bkp_data['BKP_SCHEDULE_DAY'];
			$_ary_bkp_start_date[] = date("d-M-y",$bkp_data['BKP_START_DATE']);
			$_ary_bkp_arrive_date[] = date("d-M-y",$bkp_data['BKP_ARRIVE_DATE']);
			$_ary_bkp_start_flight[] = str_replace(" ","",strtoupper($bkp_data['BKP_START_FLIGHT']));
			$_ary_bkp_arrive_flight[] = str_replace(" ","",strtoupper($bkp_data['BKP_ARRIVE_FLIGHT']));
			$_ary_bkp_team_name[] = $bkp_data['BKP_TEAM_NAME'];
			$_ary_bkp_team_name_idx[$_ary_bkp_idx[$i]] = $bkp_data['BKP_TEAM_NAME'];
			$_ary_bkp_first_money[] = number_format($bkp_data['BKP_FIRST_MONEY']);

			$buy_pd_sum = sql_fetch_array(sql_query_error("select sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BK_IDX = '".$_ary_bkp_idx[$i]."' "));
			$_show_total_buy_pd = $buy_pd_sum['total_price'];

			$_view_bkp_use_tm[] = number_format($_show_total_buy_pd); // 총사용 T.M
			$_view_bkp_over_tm[] = number_format($_show_total_buy_pd-$bkp_data['BKP_FIRST_MONEY']); //총 추가 T.M
			
			$_view_bkp_discount_rate[] = $bkp_data['BKP_DISCOUNT_RATE'];
			$_view_bkp_charge_tm[] = number_format(($_show_total_buy_pd-$bkp_data['BKP_FIRST_MONEY']) * (100 - $bkp_data['BKP_DISCOUNT_RATE']) / 100);

			if($_show_total_buy_pd > $bkp_data['BKP_FIRST_MONEY']){
				$_view_bkp_total_tm += ($_show_total_buy_pd-$bkp_data['BKP_FIRST_MONEY']) * (100 - $bkp_data['BKP_DISCOUNT_RATE']) / 100;
			}
			

		}
		$exchange_sell_data = sql_fetch_array( sql_query_error("select * from "._DB_EXCHANGE_RATE." where ER_KIND = 'sell' order by ER_IDX desc limit 0,1"));
		$exchange_get_data = sql_fetch_array( sql_query_error("select * from "._DB_EXCHANGE_RATE." where ER_KIND = 'get' order by ER_IDX desc limit 0,1"));

		$_ex_rate = $exchange_sell_data['ER_DOLLAR_MONEY'];
		$_ex_rate_get = $exchange_get_data['ER_DOLLAR_MONEY'];


		if($_cal_mode == "Y"){
			$_ex_rate_get = $cal_data['CAL_RATE'];
			$_ex_rate = $cal_data['CAL_SHOP_RATE'];
		}


		//Query
		$op_query = "select * from "._DB_GROUP_OTHER." where OP_BKG_IDX ='".$_view_group_idx."'";
		$op_result = sql_query_error($op_query);

//판매를 위한 for문


include "../layout/header_popup.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:100%; }
.table-style tr th { padding:5px 7px 4px !important; text-align:center; }

.this-team-num{ width:60px; }
.this-team-name{ width:200px; }
.this-team-name2{ width:440px; }
.this-team-name-s{ width:150px; }
.this-list-day-num{ width:75px; }
.this-qty-s{ width:40px; text-align:center; }
.this-discount-s{ width:170px; }
.this-payment-kind{ width:50px; text-align:center; }

.this-list-num{ width:60px; }
.this-list-detail{  width:50px; }
.this-list-name{ width:400px; }
.this-list-price{ width:150px; }
.this-list-price-s{ width:60px; }
.this-list-count{ width:100px; }
.this-list-memo{  }
.this-total{ background-color:#f7e3fb; font-size:13px; font-weight:bold;  }
.this-total-text{ font-family: 'Godo', sans-serif; font-size:18px; font-weight:normal !important; box-sizing:border-box; padding-right:20px !important; }
.this-list-category{ width:500px; }
.this-shop-price{ /* width:6%; */ }
.this-total-price-1{ font-family: 'Godo', sans-serif; font-size:18px; font-weight:normal !important; }
.this-total-price-2{ font-family: 'Godo', sans-serif; font-size:18px; font-weight:normal !important; color:#ff0000; }
.buy-detail-wrap{ display:none; }
.buy-detail-wrap-td{ padding: 1px !important; background-color:#e4e4e4 !important; }
.table-style tr td{ line-height:140%; }
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
  } else {
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
	<h1>부킹그룹 정산</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="full-wrap" style="padding:5px 35px !important;">

		<form name='formCalculate' action='<?=_A_PATH_GROUP_CALCUATE_OK?>' method='post'>
		
		<input type='hidden' name='a_mode' value='newCalculate'>
		<input type='hidden' name='back_url' value='<?=$page_link_text?>'>
		<input type='hidden' name='cal_shop_rate' value='<?=$_ex_rate?>'>
		<input type='hidden' name='key' value='<?=$_bkg_idx?>'>
		<input type='hidden' name='cal_idx' value='<?=$cal_data['CAL_IDX']?>'>

		<!-- 그룹정보 -->
        <div class="section-title">
			<h2>그룹 정보</h2>
        </div>
		<table class="table-style">
			<tr align="center">
				<th class="text-center">그룹 번호</th>
				<th class="text-center">그룹 타입</th>
				<th class="text-center">그룹 이름</th>
				<th class="text-center">진행 현황</th>
				<th class="text-center">인원수</th>
				<th class="text-center">그룹 시작날짜</th>
				<th class="text-center">그룹 종료날짜</th>
				<th class="text-center">지급 투어피</th>
			</tr>
			<tr align="center">
				<td ><?=$_view_group_idx?> <?=$bkg_data['BKG_CODE']?></td>
				<td ><?=$_view_group_type?></td>
				<td ><?=$_view_group_name?></td>
				<td >END</td>
				<td ><?=$_view_group_head_count?></td>
				<td ><?=$_view_group_start_date?></td>
				<td ><?=$_view_group_end_date?></td>
				<td ><?=$_view_group_tour_fee?></td>
		  </tr>
		</table>


		<!--  팀정보시작 -->
        <div class="section-title">
			<h2>팀 정보</h2>
        </div>
        <table class="table-style">
			<tr>
				<th class="text-center this-team-num">팀번호</th>
				<th class="text-center this-team-name">Name</th>
				<th class="text-center">IN</th>
				<th class="text-center">OUT</th>
				<th class="text-center">Hotel</th>
				<th class="text-center">Basic TM / Total TM</th>
				<th class="text-center">Over TM / Receve TM</th>
				<th class="text-center">Info</th>
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
			<tr align="center">
				<td><?=$_ary_bkp_idx[$i]?> </td>
				<td ><?=$_ary_bkp_team_name[$i]?></td>
				<td ><?=$_ary_bkp_start_date[$i]?> (<?=$_ary_bkp_start_flight[$i]?>)</td>
				<td ><?=$_ary_bkp_arrive_date[$i]?> (<?=$_ary_bkp_arrive_flight[$i]?>)</td>
				<td ><?=$_view2_hot_name?></td>
				<td ><?=$_ary_bkp_first_money[$i]?> (<?=$_view_bkp_use_tm[$i]?>)</td>
				<td ><?=$_view2_bkp_over_tm?> (<?=$_view2_bkp_charge_tm?>)</td>
				<td ><button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="bookingModify2('<?=$_ary_bkp_idx[$i]?>')" >INFO</button></td>
			</tr>
			<?
				} //for END
			?>

		</table>


		<!--  식사정보시작 -->
        <div class="section-title">
			<h2>식사 판매</h2>
        </div>

<div class="display-table">
	<ul class="display-table-cell">
		
		<table class="table-style">
            <tr>
				<th class="text-center this-list-num">No</th>
				<th class="text-center">Product Name</th>
				<th class="text-center">Count</th>
				<th class="text-center">T/M</th>
				<th class="text-center">Cash</th>
				<th class="text-center">Credit</th>
				<th class="text-center this-list-detail">Detail</th>
			  </tr>
<?
	$food_span = 1;

	$search_sql = "where BP_PD_KIND ='10000000' and BP_BK_IDX in  (".$bkg_data['BKG_BKP_IDX'].")";
	$food_data_count = sql_counter2(_DB_BUY_PRODUCT_TRAVEL, $search_sql, "BP_PD_IDX");

if( $food_data_count == 0 ){
?>
			<tr>
				<td colspan="7" style="height:100px;">데이터가 없습니다.</td>
			 </tr>
<?
}else{

	$_row_num = 0;
	$buydata = sql_query_error("select * from "._DB_BUY_PRODUCT_TRAVEL."  ".$search_sql ." GROUP BY BP_PD_IDX");
	while($pdlist = sql_fetch_array($buydata)){
						
                $bp_sum = sql_fetch_array(sql_query_error("select count(BP_IDX) as idx_count, sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data['BKG_CODE']."' and BP_PD_IDX='".$pdlist['BP_PD_IDX']."'  and BP_BK_IDX in (".$bkg_data['BKG_BKP_IDX'].")"));

				$bp_sum_cash = sql_fetch_array(sql_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data['BKG_CODE']."' and BP_PAYMENT_KIND = 'cash' and BP_PD_IDX='".$pdlist['BP_PD_IDX']."'  and BP_BK_IDX in   (".$bkg_data['BKG_BKP_IDX'].")"));

				$bp_sum_credit = sql_fetch_array(sql_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data['BKG_CODE']."' and BP_PAYMENT_KIND = 'credit' and BP_PD_IDX='".$pdlist['BP_PD_IDX']."'  and BP_BK_IDX in   (".$bkg_data['BKG_BKP_IDX'].")"));

			   $_view_pd_name = $pdlist['BP_PD_NAME'];
               $_view_price = number_format($bp_sum['total_price']);
               $_view_cash_price = number_format($bp_sum_cash['total_price']);
               $_view_credit_price = number_format($bp_sum_credit['total_price']);
			   $_view_bp_count = $bp_sum['idx_count'];
			   $_view_food_total_price += $bp_sum['total_price'];
               $_view_food_total_cash_price += $bp_sum_cash['total_price'];
               $_view_food_total_credit_price  += $bp_sum_credit['total_price'];
			   $_view_total_bp_count += $bp_sum['idx_count'];
			   $_show_food_reate_cost = $bp_sum_cash['rate_cost'] + $bp_sum_credit['rate_cost'];
			   if($bp_sum['total_price'] > 0){
				   $_view_food_total_rate_cost += $_show_food_reate_cost;
			   }

	$_row_num++;
	$trcolor = "#ffffff";
	if($_row_num%2 == 0){
		$trcolor = "#eee";
	}
?>
<tr bgcolor="<?=$trcolor?>">
	<td class="this-list-num text-center"><?=$food_span?></td>
	<td class="this-list-name"><?=$_view_pd_name?></td>
	<td class="this-list-count text-center"><?=$_view_bp_count?></td>
	<td class="this-list-price text-right"><b><?=$_view_price ?></b></td>
	<td class="this-list-price text-right"><b><?=$_view_cash_price?></b></td>
	<td class="this-list-price text-right"><b><?=$_view_credit_price?></b></td>
	<td ><button type="button" id="food_<?=$food_span?>_btn" class="btnstyle1 btnstyle1-primary btnstyle1-sm"  onclick="buyDetailView('food','<?=$food_span?>')">▼</button></td>
</tr>
<tr class="buy-detail-wrap" id="food_<?=$food_span?>" state="off">
	<td colspan='7' class="buy-detail-wrap-td">

		<table class="table-style border01 width-full">
<?
$_depth2_row_num=0;

$buy_detail_query = sql_query_error("select * from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data['BKG_CODE']."' and BP_PD_IDX='".$pdlist['BP_PD_IDX']."' order by BP_SC_DAY_NUM asc ");
while($buy_detail_data = sql_fetch_array($buy_detail_query)){

	$_depth2_row_num++;
	if($_depth2_row_num%2 == 0){
		$_depth2_trcolor = "#fff";
	}else{
		$_depth2_trcolor = "#ebf3ff";
	}

	$_view_bp_pd_option_price = "";
	$_ary_bp_pd_option_price = explode("|", $buy_detail_data['BP_PD_OPTION_PRICE']);
	for ($z=0; $z<count($_ary_bp_pd_option_price); $z++){
		$_view_bp_pd_option_price .= number_format($_ary_bp_pd_option_price[$z]);
		if( ($z+1) != count($_ary_bp_pd_option_price)) $_view_bp_pd_option_price .= "<br>";
	}

	$_view_bp_pd_option = str_replace('|','<br>',$buy_detail_data['BP_PD_OPTION_NAME']);
	$_view_bp_qty = str_replace('|','<br>',$buy_detail_data['BP_QTY']);
	$pd_sale_price = $buy_detail_data['BP_TOTAL_PRICE'] + $buy_detail_data['BP_DISCOUNT_PRICE'] - $buy_detail_data['BP_ADD_SALE_PRICE'];
	
?>
			<tr bgcolor="<?=$_depth2_trcolor?>">
				<!-- <td><?=$buy_detail_data['BP_IDX']?></td> -->
				<td class="this-team-name-s"><?=$buy_detail_data['BP_BK_IDX']?> - <?=$_ary_bkp_team_name_idx[$buy_detail_data['BP_BK_IDX']]?></td>
				<td class="this-list-day-num text-center"><?=$buy_detail_data['BP_SC_DAY_NUM']?></td>
				<td><?=$_view_bp_pd_option?></td>
				<td class="this-qty-s"><?=$_view_bp_qty?></td>
				<td class="this-list-price-s text-right"><?=$_view_bp_pd_option_price?></td>
				<td class="this-list-price-s text-right"><?=number_format($pd_sale_price)?></td>
				<td class="this-discount-s">
				<? if($buy_detail_data['BP_DISCOUNT_PRICE']>0){?><span style="color:#ff0000">DC - <b><?=number_format($buy_detail_data['BP_DISCOUNT_PRICE'])?></b></span><br><?=$buy_detail_data['BP_DISCOUNT_MEMO']?><? } ?>
				<? if($buy_detail_data['BP_ADD_SALE_PRICE']>0){ ?>
					<div  style="color:#2956ff"><span>ADD + <b><?=number_format($buy_detail_data['BP_ADD_SALE_PRICE'])?></b></span><br><?=$buy_detail_data['BP_ADD_MEMO']?></div>
				<? } ?>
				</td>
				<td class="this-list-price-s text-right"><b><?=number_format($buy_detail_data['BP_TOTAL_PRICE'])?></b></td>
				<td class="this-payment-kind"><?=$buy_detail_data['BP_PAYMENT_KIND']?></td>
			</tr>

<? } ?>
		</table>

	</td>
</tr>
<?$food_span++;
}  }
?>
			<tr>
				<td colspan='7' style="padding:0; border:none; height:5px;"></td>
			</tr>
			<tr>
				<td colspan='2' rowspan='2' class="this-total text-right this-total-text">Total</td>
				<td rowspan='2' class="this-total this-list-count text-center"><?=$_view_total_bp_count?></td>
				<td rowspan='2' class="this-total this-list-price text-right"><?=number_format($_view_food_total_price) ?></td>
				<td class="this-total this-list-price text-right"><?=number_format($_view_food_total_cash_price) ?></td>
				<td class="this-total this-list-price text-right"><?=number_format($_view_food_total_credit_price) ?></td>
				<td rowspan='2' class="this-total "></td>
			</tr>
			<tr>
				<td colspan='2' class="this-total text-center"><?=number_format($_view_food_total_cash_price + $_view_food_total_credit_price) ?></td>
			</tr>
		</table>

	</ul>
	<ul class="display-table-cell p-l-15">
		
		<table class="table-style">
			<tr>
				<th>식사관련 특이사항</th>
			</tr>
			<tr>
				<td><textarea style='width:300px; height:150px;' name='calculate_food_memo'><?=$_view_cal_food_memo?></textarea></td>
			</tr>
		</table>

	</ul>
</div>





		<!--  투어 정보 시작 -->
        <div class="section-title">
			<h2>투어 판매</h2>
        </div>

<div class="display-table">
	<ul class="display-table-cell">

		<table class="table-style">
             <tr>
				<th class="text-center this-list-num">No</th>
				<th class="text-center">Product Name</th>
				<th class="text-center">Count</th>
				<th class="text-center">T/M</th>
				<th class="text-center">Cash</th>
				<th class="text-center">Credit</th>
				<th class="text-center this-list-detail">Detail</th>
			  </tr>
<?
	$tour_span = 1;

	$search_sql_tour = "where  (BP_PD_KIND ='07000000' and BP_BK_IDX in  (".$bkg_data['BKG_BKP_IDX'].")) or (BP_PD_KIND ='08000000'  and BP_BK_IDX in  (".$bkg_data['BKG_BKP_IDX']."))";
	$tour_data_count = sql_counter2(_DB_BUY_PRODUCT_TRAVEL, $search_sql_tour, "BP_PD_IDX");


if($tour_data_count == 0){
?>
			 <tr>
				<td colspan="7" style="height:100px;">데이터가 없습니다.</td>
			 </tr>
<?
}else{
    
	
	$_row_num = 0;
	$buydata = sql_query_error("select * from "._DB_BUY_PRODUCT_TRAVEL."  ".$search_sql_tour ." GROUP BY BP_PD_IDX");
			while($pdlist = sql_fetch_array($buydata)){
						
                $bp_sum = sql_fetch_array(sql_query_error("select count(BP_IDX) as idx_count , sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data['BKG_CODE']."'  and BP_PD_IDX='".$pdlist['BP_PD_IDX']."'  and BP_BK_IDX in   (".$bkg_data['BKG_BKP_IDX'].")"));
				//select count(BP_IDX) as idx_count , sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='ARI_1571194311'  and BP_PD_IDX='238'  and BP_BK_IDX in   (1558,1285,1267,1139,1093,1092,1257,1333,1107)

				$bp_sum_cash = sql_fetch_array(sql_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data['BKG_CODE']."' and BP_PAYMENT_KIND = 'cash' and BP_PD_IDX='".$pdlist['BP_PD_IDX']."'  and BP_BK_IDX in   (".$bkg_data['BKG_BKP_IDX'].")"));

				$bp_sum_credit = sql_fetch_array(sql_query_error("select sum((BP_TOTAL_COST_PRICE * BP_RATE_OF_COST) / 100) as rate_cost, sum(BP_TOTAL_COST_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data['BKG_CODE']."' and BP_PAYMENT_KIND = 'credit' and BP_PD_IDX='".$pdlist['BP_PD_IDX']."'  and BP_BK_IDX in   (".$bkg_data['BKG_BKP_IDX'].")"));

				
			   $_view_pd_name = $pdlist['BP_PD_NAME'];
               $_view_price = number_format($bp_sum['total_price']);
               $_view_cash_price = number_format($bp_sum_cash['total_price']);
               $_view_credit_price = number_format($bp_sum_credit['total_price']);
			   $_view_bp_count = $bp_sum['idx_count'];

			   $_view_total_price += $bp_sum['total_price'];
               $_view_total_cash_price += $bp_sum_cash['total_price'];
               $_view_total_credit_price  += $bp_sum_credit['total_price'];
			   $_view_total_count +=$bp_sum['idx_count'];

			   $_show_tour_reate_cost = $bp_sum_cash['rate_cost'] + $bp_sum_credit['rate_cost'];
			   if($bp_sum['total_price'] > 0){
				   $_view_tour_total_rate_cost += $_show_tour_reate_cost;
			   }

	$_row_num++;
	$trcolor = "#ffffff";
	if($_row_num%2 == 0){
		$trcolor = "#eee";
	}
?>
<tr bgcolor="<?=$trcolor?>">
	<td class="this-list-num text-center"><?=$tour_span?></td>
	<td class="this-list-name"><?=$_view_pd_name?></td>
	<td class="this-list-count text-center"><?=$_view_bp_count ?></td>
	<td class="this-list-price text-right"><b><?=$_view_price ?> </b></td>
	<td class="this-list-price text-right"><b><?=$_view_cash_price?></b></td>
	<td class="this-list-price text-right"><b><?=$_view_credit_price?></b></td>
	<td ><button type="button" id="tour_<?=$tour_span?>_btn" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="buyDetailView('tour','<?=$tour_span?>')">▼</button></td>
</tr>
<tr class="buy-detail-wrap"  id="tour_<?=$tour_span?>" state="off">
	<td colspan='7' class="buy-detail-wrap-td">

		<table class="table-style border01 width-full">
<?
$_depth2_row_num=0;

$buy_detail_query = sql_query_error("select * from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BKG_CODE='".$bkg_data['BKG_CODE']."' and BP_PD_IDX='".$pdlist['BP_PD_IDX']."' order by BP_SC_DAY_NUM asc");
while($buy_detail_data = sql_fetch_array($buy_detail_query)){

	$_depth2_row_num++;
	if($_depth2_row_num%2 == 0){
		$_depth2_trcolor = "#fff";
	}else{
		$_depth2_trcolor = "#ebf3ff";
	}

	$_view_bp_pd_option_price = "";
	$_ary_bp_pd_option_price = explode("|", $buy_detail_data['BP_PD_OPTION_PRICE']);
	for ($z=0; $z<count($_ary_bp_pd_option_price); $z++){
		$_view_bp_pd_option_price .= number_format($_ary_bp_pd_option_price[$z]);
		if( ($z+1) != count($_ary_bp_pd_option_price)) $_view_bp_pd_option_price .= "<br>";
	}

	$_view_bp_pd_option = str_replace('|','<br>',$buy_detail_data['BP_PD_OPTION_NAME']);
	$_view_bp_qty = str_replace('|','<br>',$buy_detail_data['BP_QTY']);

	$pd_sale_price = $buy_detail_data['BP_TOTAL_PRICE'] + $buy_detail_data['BP_DISCOUNT_PRICE'] - $buy_detail_data['BP_ADD_SALE_PRICE'];
?>
			<tr bgcolor="<?=$_depth2_trcolor?>">
				<!-- <td><?=$buy_detail_data['BP_IDX']?></td> -->
				<td class="this-team-name-s"><?=$buy_detail_data['BP_BK_IDX']?> - <?=$_ary_bkp_team_name_idx[$buy_detail_data['BP_BK_IDX']]?></td>
				<td class="this-list-day-num text-center"><?=$buy_detail_data['BP_SC_DAY_NUM']?></td>
				<td><?=$_view_bp_pd_option?></td>
				<td class="this-qty-s"><?=$_view_bp_qty?></td>
				<td class="this-list-price-s text-right"><?=$_view_bp_pd_option_price?></td>
				<td class="this-list-price-s text-right"><?=number_format($pd_sale_price)?></td>
				<td class="this-discount-s">
				<? if($buy_detail_data['BP_DISCOUNT_PRICE']>0){ ?>
					<div style="color:#ff0000"><span >DC - <b><?=number_format($buy_detail_data['BP_DISCOUNT_PRICE'])?></b></span><br><?=$buy_detail_data['BP_DISCOUNT_MEMO']?></div>
				<? } ?>
				<? if($buy_detail_data['BP_ADD_SALE_PRICE']>0){ ?>
					<div  style="color:#2956ff"><span>ADD + <b><?=number_format($buy_detail_data['BP_ADD_SALE_PRICE'])?></b></span><br><?=$buy_detail_data['BP_ADD_MEMO']?></div>
				<? } ?>
	</td>
				<td class="this-list-price-s text-right"><b><?=number_format($buy_detail_data['BP_TOTAL_PRICE'])?></b></td>
				<td class="this-payment-kind"><?=$buy_detail_data['BP_PAYMENT_KIND']?></td>
			</tr>

<? } ?>
		</table>

	</td>
</tr>
<?$tour_span++;
}  }
?>
			<tr>
				<td colspan='7' style="padding:0; border:none; height:5px;"></td>
			</tr>
			<tr>
				<td colspan='2' rowspan='2' class="this-total text-right this-total-text">Total</td>
				<td rowspan='2' class="this-total this-list-count text-center"><?=$_view_total_count?></td>
				<td rowspan='2' class="this-total this-list-price text-right"><?=number_format($_view_total_price) ?></td>
				<td class="this-total this-list-price text-right"><?=number_format($_view_total_cash_price) ?></td>
				<td class="this-total this-list-price text-right"><?=number_format($_view_total_credit_price) ?></td>
				<td rowspan='2' class="this-total"></td>
			</tr>
			<tr>
				<td colspan='2' class="this-total text-center"><?=number_format($_view_total_cash_price + $_view_total_credit_price) ?></td>
			</tr>
		</table>

	</ul>
	<ul class="display-table-cell p-l-15">

		<table class="table-style">
			<tr>
				<th>투어관련 특이사항</th>
			</tr>
			<tr>
				<td><textarea style='width:300px; height:200px;' name='calculate_tour_memo'><?=$_view_cal_tour_memo?></textarea></td>
			</tr>
		</table>

	</ul>
</div>

		<!-- 기타 정보시작 -->
        <div class="section-title"  style='margin-top:100px;'>
			<h2>기타 지출</h2>
        </div>
		
		<table class="table-style" >
             <tr>
				<th class="text-center this-list-num">No</th>
				<th class="text-center this-list-category">분류</th>
				<th class="this-list-price text-center">Credit</th>
				<th class="this-list-price text-center">Cash</th>
				<th class="this-list-price text-center">Tatal</th>
				<th class="text-center">Memo</th>
			  </tr>

<?
$num=0;
while($op_list = sql_fetch_array($op_result)){
			$num++;
			$_view_dapth_1 = $op_list['OP_DAPTH_1'];
			$_view_dapth_2 = ( $op_list['OP_DAPTH_2'] ) ? " > ".$op_list['OP_DAPTH_2'] : "";
			$_view_credit = number_format($op_list['OP_CREDIT']);
			$_view_cash =  number_format($op_list['OP_CASH']); 
			$_view_total =  number_format($op_list['OP_TOTAL']); 
			$_view_memo = $op_list['OP_MEMO'];
			$_view_credit_sum += $op_list['OP_CREDIT'];
			$_view_cash_sum += $op_list['OP_CASH']; 
			$_view_total_sum += $op_list['OP_TOTAL']; 
?>
<tr>
	<td class="text-center this-list-num"><?=$num?></td>
	<td class="text-left this-list-category"><?=$_view_dapth_1?><?=$_view_dapth_2?></td>
	<td class="this-list-price text-right"><b><?=$_view_credit?></b></td>
	<td class="this-list-price text-right"><b><?=$_view_cash?></b></td>
	<td class="this-list-price text-right"><b><?=$_view_total?></b></td>
	<td ><?=$_view_memo?></td>
</tr>
<? } ?>

			<tr>
				<td colspan='6' style="padding:0; border:none; height:5px;"></td>
			</tr>
			<tr>
				<td colspan='2' class="this-total text-right this-total-text">Total</td>
				<td class="this-total this-list-price text-right"><?=number_format($_view_credit_sum) ?></td>
				<td class="this-total this-list-price text-right"><?=number_format($_view_cash_sum) ?></td>
				<td class="this-total this-list-price text-right"><?=number_format($_view_total_sum) ?></td>
				<td class="this-total" ></td>
			</tr>
</table>

		<!-- 투어피 정보시작 -->
        <div class="section-title">
			<h2>투어피</h2>
        </div>
<?
	$_view_tour_fee_total = $bkg_data['BKG_TOTAL_TOUR_FEE']; // 지급받은 투어피
	$_view_tour_fee_cash = $_view_food_total_cash_price + $_view_total_cash_price + $_view_cash_sum; // 캐쉬 사용 투어피
	$_view_tour_fee_credit = $_view_food_total_credit_price + $_view_total_credit_price + $_view_credit_sum; //크레딧 사용 어피
	$_view_use_tour_fee = $_view_tour_fee_cash + $_view_tour_fee_credit; // 총사용 투어피
	$_view_balance_tour_fee = $_view_tour_fee_total - $_view_tour_fee_cash; // 투어피 발란스

	if( $_view_balance_tour_fee < 0 ){
		$_show_balance_color = 'blue';
	}else{
		$_show_balance_color = 'red';
	}
?>
<table class="table-style" >
	<tr>
		<th class="text-center">총 지급받은 투어피</th>
		<th class="text-center">총 사용 투어피</th>
		<th class="text-center">Cash</th>
		<th class="text-center">Credit</th>
		<th class="text-center">투어피 발란스</th>
		<th class="text-center">투어피 내역</th>
	</tr>
	<tr>
		<td class="text-center">฿ <?=number_format($_view_tour_fee_total)?></td>
		<td class="text-center">฿ <?=number_format($_view_use_tour_fee)?></td>
		<td class="text-center">฿ <?=number_format($_view_tour_fee_cash)?></td>
		<td class="text-center">฿ <?=number_format($_view_tour_fee_credit)?></td>
		<td class="text-center"><span style='color:<?=$_show_balance_color?>'>฿ <?=number_format($_view_balance_tour_fee)?></span></td>
		<td>
		<?for($t=0;$t<count($_ary_tour_fee_history);$t++){?>
			<?= number_format($_ary_tour_fee_history[$t])?> - ( <?=$_ary_tour_fee_history_id[$t]?> : <?=date("d-M-y",$_ary_tour_fee_history_date[$t])?> ) <br>
		<?}?>
		</td>
	</tr>
</table>


		<!-- 청구서 -->
        <div class="section-title">
			<h2>청구서</h2>
        </div>
			<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:150px; margin-bottom:5px;" onclick="pu_ok('<?=$_bkg_idx?>','bill_all_cf','<?=$_bkg_idx?>')"> 
			<i class="fas fa-plus-circle"></i> 전체 입금확인
			</button>

	<div class="bill-wrap">
		<table class="table-style" style="width:609px;">
			<tr>
				<th class="text-center this-team-num">팀번호</th>
				<th class="text-center ">Name</th>
				<th class="text-center ">건수</th>
				<th class="text-center this-list-price">입금</th>
				<th class="text-center this-list-detail">Detail</th>
			</tr>
<?
			$exchange_rate['D'] = "달러";
			$exchange_rate['W'] = "원화";
			$exchange_rate['G'] = "송금";
			$exchange_rate['B'] = "바트";

			$exchange_sb['D'] = "$";
			$exchange_sb['W'] = '₩';
			$exchange_sb['G'] = '₩';
			$exchange_sb['B'] = "฿";

for($b=0;$b<count($_ary_bkp_idx);$b++){

	$bill_count = sql_counter(_DB_BILL_TRAVEL, " where PU_BKP_IDX = '".$_ary_bkp_idx[$b]."' ");
	$bill_sum = sql_fetch_array(sql_query_error("select sum(PU_MONEY) as money_total from "._DB_BILL_TRAVEL." where PU_BKP_IDX = '".$_ary_bkp_idx[$b]."' " ));

	$bill_result = sql_query_error("select * from "._DB_BILL_TRAVEL." where PU_BKP_IDX = '".$_ary_bkp_idx[$b]."' order by PU_IDX desc");
?>
			<tr>
				<td class="text-center"><?=$_ary_bkp_idx[$b]?></td>
				<td class="text-center"><?=$_ary_bkp_team_name[$b]?></td>
				<td class="text-center"><?=$bill_count?></td>
				<td class="text-right"><b>฿ <?=number_format($bill_sum['money_total'])?></b></td>
				<td ><button type="button" id="food_<?=$_ary_bkp_idx[$b]?>_btn" class="btnstyle1 btnstyle1-primary btnstyle1-sm"  onclick="buyDetailView('bill','<?=$_ary_bkp_idx[$b]?>')">▼</button></td>
			</tr>
			<tr class="buy-detail-wrap" id="bill_<?=$_ary_bkp_idx[$b]?>" state="off">
				<td colspan='5' class="buy-detail-wrap-td">
					<table class="table-style border01 width-full">
<?
			$bill_count = 0;
			while($bill_list = sql_fetch_array($bill_result)){
				$bill_count++;
				if($bill_count%2 == 0){
					$_depth2_trcolor = "#fff";
				}else{
					$_depth2_trcolor = "#ebf3ff";
				}
				$_view2_bill_kind = $bill_list['PU_KIND'];
				$_view2_bill_exchange_rate = $bill_list['PU_EXCHANGE_RATE'];
				$_view2_bill_defult_money = number_format($bill_list['PU_DEFULT_MONEY']);
				$_view2_bill_money = number_format($bill_list['PU_MONEY']);
				$_view2_bill_total_money += $bill_list['PU_MONEY'];
				$_view2_bill_cf_yn = $bill_list['PU_ADMIN_CONFIRM'];
				$_view2_bill_cf_yn_id = $bill_list['PU_ADMIN_CONFIRM_ID'];
				$_view2_bill_cf_yn_date = date("d-M-y H:i",$bill_list['PU_ADMIN_CONFIRM_DATE']);
				$_view2_biil_total_money += $bill_list['PU_MONEY'];
				if($_view2_bill_kind == 'D'){
					$_view_dor_total +=  $bill_list['PU_DEFULT_MONEY'];
				}elseif($_view2_bill_kind == 'W'){
					$_view_won_total +=  $bill_list['PU_DEFULT_MONEY'];
				}elseif($_view2_bill_kind == 'G'){
					$_view_bnk_total +=  $bill_list['PU_DEFULT_MONEY'];
				}elseif($_view2_bill_kind == 'B'){
					$_view_thb_total +=  $bill_list['PU_DEFULT_MONEY'];
				}
?>
<tr bgcolor="<?=$_depth2_trcolor?>">
	<td class="text-center this-team-num"><?=$bill_list['PU_IDX']?></td>
	<td class="text-center"><?=$exchange_rate[$_view2_bill_kind]?></td>
	<td class="text-center"><?=$_view2_bill_exchange_rate?></td>
	<td class="text-right"> <?=$exchange_sb[$_view2_bill_kind]?> <b><?=$_view2_bill_defult_money?></b></td>
	<td class="text-right"><b>฿ <?=$_view2_bill_money?></b> </td>
	<td>
		<?if($_view2_bill_cf_yn == 'N'){?>
			<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs m-t-3 width-50" onclick="pu_ok('<?=$bill_list['PU_IDX']?>','bill_part_cf','<?=$_bkg_idx?>')" >
			<i class="far fa-check-square"></i> C/F</button>
		<?}else{?>
			완료 - <?=$_view2_bill_cf_yn_id?> (<?=$_view2_bill_cf_yn_date?>)
		<?}?>
	</td>
</tr>
<? } ?>
					</table>
				</td>
			</tr>
<? } ?>
		<tr>
			<th colspan='3'>Total</th>
			<td class="text-center" colspan='2'><b><span style='font-size:13px; color:red;'>฿ <?=number_format($_view2_biil_total_money)?></span></b></td>
		</tr>
		</table>


	<div class="bill-list-wrap">

	</div>
</div>
		<table class="table-style" >



<?		$bill_y_result = sql_query_error("select * from "._DB_BILL_TRAVEL." where PU_BKG_IDX = '".$_bkg_idx."' and PU_ADMIN_CONFIRM = 'Y' order by PU_IDX asc");
			
			while($bill_y_list = sql_fetch_array($bill_y_result)){

				$_view2_bill_y_kind = $bill_y_list['PU_KIND'];

				if($_view2_bill_y_kind == 'D'){
					$_view_dor_y_total +=  $bill_y_list['PU_DEFULT_MONEY'];
					$_view_dor_y_date = date("d-M-y H:i",$bill_y_list['PU_ADMIN_CONFIRM_DATE']);
				}elseif($_view2_bill_y_kind == 'W'){
					$_view_won_y_total +=  $bill_y_list['PU_DEFULT_MONEY'];
					$_view_won_y_date = date("d-M-y H:i",$bill_y_list['PU_ADMIN_CONFIRM_DATE']);
				}elseif($_view2_bill_y_kind == 'G'){
					$_view_bnk_y_total +=  $bill_y_list['PU_DEFULT_MONEY'];
					$_view_bnk_y_date = date("d-M-y H:i",$bill_y_list['PU_ADMIN_CONFIRM_DATE']);
				}elseif($_view2_bill_y_kind == 'B'){
					$_view_thb_y_total +=  $bill_y_list['PU_DEFULT_MONEY'];
					$_view_thb_y_date = date("d-M-y H:i",$bill_y_list['PU_ADMIN_CONFIRM_DATE']);
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

			if($_view_thb_y_total != 0)$_view_bill_thb_history = "฿".number_format($_view_thb_y_total)."\n (".$_view_thb_y_date.")";
			if($_view_won_y_total != 0)$_view_bill_won_history = "₩".number_format($_view_won_y_total)."\n (".$_view_won_y_date.")";
			if($_view_bnk_y_total != 0)$_view_bill_bnk_history = "₩".number_format($_view_bnk_y_total)."\n (".$_view_bnk_y_date.")";
			if($_view_dor_y_total != 0)$_view_bill_dor_history = "$".number_format($_view_dor_y_total)."\n (".$_view_dor_y_date.")";
?>
	</table>



	<table class="table-style m-t-10" style='float:left; margin-right:15px;'>
		<tr>
			<th class="text-center" rowspan='3'>입금 합계</th>
			<th class="text-center" style="width:18%;">바트 현금</th>
			<th class="text-center" style="width:18%;">바트 송금</th>
			<th class="text-center" style="width:18%;">원화 현금</th>
			<th class="text-center" style="width:18%;">원화 송금</th>
			<th class="text-center" style="width:18%;">달러 현금</th>
		</tr>
		<tr>
			<td class="text-right">฿ <span style='color:<?=$_show_thb_color?>'><?=number_format($_view_thb_total)?></span></td>
			<td></td>
			<td class="text-right">₩ <span style='color:<?=$_show_won_color?>'><?=number_format($_view_won_total)?></span></td>
			<td class="text-right">₩ <span style='color:<?=$_show_bnk_color?>'><?=number_format($_view_bnk_total)?></span></td>
			<td class="text-right">$ <span style='color:<?=$_show_dor_color?>'><?=number_format($_view_dor_total)?></span></td>
		</tr>
		<tr>
			<td class="text-center"><?=$_view_bill_thb_history?></span></td>
			<td class="text-center"></td>
			<td class="text-center"><?=$_view_bill_won_history?></td>
			<td class="text-center"><?=$_view_bill_bnk_history?></td>
			<td class="text-center"><?=$_view_bill_dor_history?></td>
		</tr>
	</table>

	<table class="table-style  m-t-10">
		<tr>
			<th class="text-center">입금관련<br>특이사항</th>
			<td><textarea name='unpid_bill_memo' style='width:300px; height:80px;'><?=$_view_cal_bill_memo?></textarea></td>
		</tr>
	</table>

		<!-- TM 정산 -->
        <div class="section-title">
			<h2>TM 정산</h2>
        </div>
<?

	
	if($_view_group_type == 'HM' || $_view_group_type == 'FA'){
	
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
	}else{ //허니문과 FA 가 아닐때.
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
		$_view_concede_sales_rate = 100; //인정맨출률

	
		if($_view_bkp_total_tm < 0){
			$_view_bkp_total_tm = 0;
		}
		if($_view_cost_deduct < 0){
			$_view_cost_deduct = 0;
		}
		if($_view_calculate_salas < 0){
			$_view_calculate_salas = 0;
		}

		$_view_bkp_total_tm_dollar = $_view_total_tm / $_ex_rate; //추가 T/M 달러
		$_view_cost_deduct_dollar = round($_view_total_rate_cost_price / $_ex_rate); // 원가공제 달러
		$_view_calculate_salas_dollar = round(($_view_bkp_total_tm - $_view_total_rate_cost_price) / $_ex_rate);  // 정산 매출 달러
		$_view_concede_sales_price_dollar = round($_view_bkp_total_tm_dollar * $_view_concede_sales_rate) / 100;
	}

?>
	<table class="table-style">
		<tr>
			<th class="text-center" >총 사용 T/M</th>
			<td style="width:18%;">฿ <b><?=number_format($_view_total_tm)?></b></td>
			<td style="padding:0; border:none; width:10px;"></td>
			<th class="text-center" style="width:18%;">추가 T/M</th>
			<th class="text-center" style="width:18%;">원가 공제</th>
			<th class="text-center" style="width:18%;">정산 매출</th>
			<th class="text-center" style="width:18%;">인정 매출</th>
		</tr>
		<tr>
			<th class="text-center" >총 인정 원가지출</th>
			<td>฿ <b><?=number_format($_view_total_rate_cost_price)?></b></td>
			<td style="padding:0; border:none;"></td>
			<td class="text-right" style="width:18%;">฿ <b><?=number_format($_view_bkp_total_tm)?></b></td>
			<td class="text-right" style="width:18%;">฿ <b><?=number_format($_view_cost_deduct)?></b></td>
			<td class="text-right" style="width:18%;">฿ <b><?=number_format($_view_calculate_salas)?></b></td>
			<td class="text-center" style="width:18%;"><?=$_view_concede_sales_rate?>%</td>
		</tr>
		<tr>
			<th class="text-center" >원가 지출률</th>
			<td><b><?=$_view_rate_cast?></b>%</td>
			<td style="padding:0; border:none;"></td>
			<td class="text-right" style="width:18%;">$ <b><?=number_format($_view_bkp_total_tm_dollar)?></b></td>
			<td class="text-right" style="width:18%;">$ <b><?=number_format($_view_cost_deduct_dollar)?></b></td>
			<td class="text-right" style="width:18%;">$ <b><?=number_format($_view_calculate_salas_dollar)?></b></td>
			<td class="text-right" style="width:18%;">$ <b><?=number_format($_view_concede_sales_price_dollar)?></b></td>
		</tr>
	</table>

		<!-- 샵 정보시작 -->
        <div class="section-title">
			<h2>쇼핑매출</h2>
        </div>
<?	
	
	$_shop_st_date = date("Ymd",$bkg_data['BKG_START_DATE']);
	$_shop_ed_date = date("Ymd",$bkg_data['BKG_END_DATE']);
	$_shop_gd_name = $guide_data['GD_NAME'];
	$_shop_sql = " and SS_BKG_IDX = '".$_bkg_idx."'  and SS_GUIDE_NAME = '".$_shop_gd_name."'";
	$gml_data   =  sql_fetch_array(sql_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'GML' ".$_shop_sql.""));
	$brd_a_data =  sql_fetch_array(sql_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'BRD' and SS_PRODUCT_KIND = 'A' ".$_shop_sql.""));
	$brd_b_data =  sql_fetch_array(sql_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'BRD' and SS_PRODUCT_KIND = 'B' ".$_shop_sql.""));
	$brd_c_data =  sql_fetch_array(sql_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'BRD' and SS_PRODUCT_KIND = 'C' ".$_shop_sql.""));
	
	$prl_data   =  sql_fetch_array(sql_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'PRL' ".$_shop_sql.""));
	$muk_data   =  sql_fetch_array(sql_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'MUK' ".$_shop_sql.""));
	$saw_data   =  sql_fetch_array(sql_query_error("select sum(SS_REPORT_PRICE)as report_price from "._DB_SHOP_SALES." where SS_NAME = 'SAW' ".$_shop_sql.""));
	
	$muk_dollar = $muk_data['report_price'] / $_ex_rate;
	$prl_dollar = $prl_data['report_price'] / $_ex_rate;


	$gml_report_pirce = $gml_data['report_price'];
	$brd_a_report_pirce = $brd_a_data['report_price'];
	$brd_b_report_pirce = $brd_b_data['report_price'];
	$brd_c_report_pirce = $brd_c_data['report_price'];

	
	$saw_report_pirce = $saw_data['report_price'];
	$muk_report_pirce = $muk_data['report_price'];
	$prl_report_pirce = $prl_data['report_price'];

	if($gml_data['report_price'] == ''){
		$gml_report_pirce = 0;
	}
	if($brd_a_data['report_price'] == ''){
		$brd_a_report_pirce = 0;
	}
	if($brd_b_data['report_price'] == ''){
		$brd_b_report_pirce = 0;
	}
	if($brd_c_data['report_price'] == ''){
		$brd_c_report_pirce = 0;
	}

	if($prl_data['report_price'] == ''){
		$prl_report_pirce = 0;
	}
	if($muk_data['report_price'] == ''){
		$muk_report_pirce = 0;
	}


	$total_con_dollar = $gml_data['report_price'] + $brd_a_data['report_price'] + $brd_b_data['report_price'] + $brd_c_data['report_price'] + $muk_dollar + $_view_concede_sales_price_dollar + $prl_dollar;
	
	
	if($_cal_mode == "Y"){
		$team_count = $cal_data['CAL_TEAM_COUNT'];
	}else{
		$team_count = count($_ary_bkp_idx); //부킹 팀수
	}
	$shop_cal_rate = $total_con_dollar/$team_count; //쌍당매출


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
	


	if($_cal_mode == "Y" || $cal_data['CAL_STATE'] == '2'){
		$_cal_rate = explode("|",$cal_data['CAL_SHOP_CAL_LATE']);
		$_gmd_calcu_rate = $_cal_rate[0];
		$_brd_a_calcu_rate = $_cal_rate[1];
		$_brd_b_calcu_rate = $_cal_rate[2];
		$_brd_c_calcu_rate = $_cal_rate[3];
		$_prl_calcu_rate = $_cal_rate[4];
		$_muk_calcu_rate = $_cal_rate[5];
		$_tm_calcu_rate = $_cal_rate[6];
	}else{
		if($_view_group_type == 'HM'){
			$cal_data_gml  = sql_fetch_array(sql_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='GML' and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
			$cal_data_prl  = sql_fetch_array(sql_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='PRL' and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
			$cal_data_muk  = sql_fetch_array(sql_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='MUK' and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
			$cal_data_saw  = sql_fetch_array(sql_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='SAW' and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
			$cal_data_brd_a = sql_fetch_array(sql_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='BRD' and ASC_SHOP_KIND = 'A'  and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
			$cal_data_brd_b = sql_fetch_array(sql_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='BRD' and ASC_SHOP_KIND = 'B' and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
			$cal_data_brd_c = sql_fetch_array(sql_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='BRD' and ASC_SHOP_KIND = 'C'  and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));
			$cal_data_tm = sql_fetch_array(sql_query_error("select ASC_CRITERIA_CALCULATE from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_CODE ='TM'  and ASC_CRITERIA_MONEY = '".$_select_criteria."'"));

			$_gmd_calcu_rate = $cal_data_gml['ASC_CRITERIA_CALCULATE'];
			$_prl_calcu_rate = $cal_data_prl['ASC_CRITERIA_CALCULATE'];
			$_muk_calcu_rate = $cal_data_muk['ASC_CRITERIA_CALCULATE'];
			$_saw_calcu_rate = $cal_data_saw['ASC_CRITERIA_CALCULATE'];
			$_brd_a_calcu_rate = $cal_data_brd_a['ASC_CRITERIA_CALCULATE'];
			$_brd_b_calcu_rate = $cal_data_brd_b['ASC_CRITERIA_CALCULATE'];
			$_brd_c_calcu_rate = $cal_data_brd_c['ASC_CRITERIA_CALCULATE'];
			$_brd_bkk_calcu_rate = 30;
			$_jol_calcu_rate = 30;
			$_tm_calcu_rate = $cal_data_tm['ASC_CRITERIA_CALCULATE'];
			
		}else{
			$_gmd_calcu_rate = 30;
			$_brd_a_calcu_rate = 30;
			$_brd_b_calcu_rate = 25;
			$_brd_c_calcu_rate = 15;
			$_brd_bkk_calcu_rate = 30;
			$_prl_calcu_rate = 25;
			$_muk_calcu_rate = 30;
			$_saw_calcu_rate = 15;
			$_tm_calcu_rate = 100;
			$_jol_calcu_rate = 30;
		}
	}
	
	$gml_calcu_price = (($gml_data['report_price'] * $_gmd_calcu_rate) * $_ex_rate_get ) / 100;
	$brd_a_calcu_price = (($brd_a_data['report_price'] * $_brd_a_calcu_rate) * $_ex_rate_get ) / 100;
	$brd_b_calcu_price = (($brd_b_data['report_price'] * $_brd_b_calcu_rate) * $_ex_rate_get ) / 100;
	$brd_c_calcu_price = (($brd_c_data['report_price'] * $_brd_c_calcu_rate) * $_ex_rate_get ) / 100;
	$brd_bkk_calcu_price = (($brd_bkk_data['report_price'] * $_brd_bkk_calcu_rate) * $_ex_rate_get ) / 100;
	$prl_calcu_price = ((($prl_data['report_price'] / $_ex_rate) * $_prl_calcu_rate) * $_ex_rate_get) / 100;
	$muk_calcu_price = ($muk_data['report_price'] * $_muk_calcu_rate)  / 100;
	$jol_calcu_price = ($jol_data['report_price'] * $_muk_calcu_rate)  / 100;

	$_calcu_price = (($_view_calculate_salas_dollar * $_tm_calcu_rate) * $_ex_rate_get)/ 100 ;
	$last_calcu_price = $gml_calcu_price + $brd_a_calcu_price + $brd_b_calcu_price + $brd_c_calcu_price + $prl_calcu_price + $muk_calcu_price + $_calcu_price;
	
?>

	<table class="table-style">
		<tr>
			<th class="text-center">쇼핑업체</th>
			<td style="padding:0; border:none; width:5px;"></td>
			<th class="text-center" colspan='2'>LATEX</th>
			<td style="padding:0; border:none; width:5px;"></td>
			<th class="text-center" colspan='8'>BRD</th>
			<td style="padding:0; border:none; width:5px;"></td>
			<th class="text-center" colspan='2'>P F</th>
			<td style="padding:0; border:none; width:5px;"></td>
			<th class="text-center" colspan='2'>MUKDARA SPA</th>
			<td style="padding:0; border:none; width:5px;"></td>
			<th class="text-center" colspan='2'>JOLIE</th>
			<td style="padding:0; border:none; width:5px;"></td>
			<th class="text-center" colspan='2'>T/M</th>
			<td style="padding:0; border:none; width:5px;"></td>
			<th class="text-center">TOTAL</th>
		</tr>
		<tr>
			<td >매출</td>
			<td style="padding:0; border:none;"></td>
			<td class="text-right" colspan='2'>$ <b><?=number_format($gml_data['report_price'])?></b></td>
			<td style="padding:0; border:none;"></td>
			<td class="text-center this-shop-price">A</td>
			<td class="this-shop-price text-right">$ <b><?=number_format($brd_a_data['report_price'])?></b></td>
			<td class="text-center this-shop-price">B</td>
			<td class="this-shop-price text-right">$ <b><?=number_format($brd_b_data['report_price'])?></b></td>
			<td class="text-center this-shop-price">C</td>
			<td class="this-shop-price text-right">$ <b><?=number_format($brd_c_data['report_price'])?></b></td>
			<td class="text-center this-shop-price">BKK</td>
			<td class="this-shop-price text-right">$ <b><?=number_format($brd_bkk_data['report_price'])?></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="this-shop-price text-right">฿ <b><?=number_format($prl_data['report_price'])?></b></td>
			<td class="this-shop-price text-right">$ <b><?=number_format($prl_data['report_price'] / $_ex_rate)?></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="this-shop-price text-right">฿ <b><?=number_format($muk_data['report_price'])?></b></td>
			<td class="this-shop-price text-right">$ <b><?=number_format($muk_data['report_price'] / $_ex_rate)?></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="this-shop-price text-right">฿ <b><?=number_format($jol_data['report_price'])?></b></td>
			<td class="this-shop-price text-right">$ <b><?=number_format($jol_data['report_price'] / $_ex_rate)?></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="this-shop-price text-right">$ <b><?=number_format($_view_calculate_salas_dollar)?></b></td>
			<td class="this-shop-price text-right">$ <b><?=number_format($_view_concede_sales_price_dollar)?></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="text-right">$  <b><?=number_format($total_con_dollar)?></b></td>
		</tr>
		<tr>
			<td >COM</td>
			<td style="padding:0; border:none;"></td>
			<td class="text-center this-shop-price"><input type='text' name='gmd_rate' id='gmd_rate' style='width:30px;' onchange="changeRate();" value='<?=$_gmd_calcu_rate?>'>%</td>
			<td class="this-shop-price text-right">฿ <b><span id='gml_calcu_price'><?=number_format($gml_calcu_price)?></span></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="text-center"><input type='text' name='brd_a_rate' id='brd_a_rate' style='width:30px;' onchange="changeRate();" value='<?=$_brd_a_calcu_rate?>'>%</td>
			<td class="text-right">฿ <b><span id='brd_a_calcu_price'><?=number_format($brd_a_calcu_price)?></span></b></td>
			<td class="text-center"><input type='text' name='brd_b_rate' id='brd_b_rate' style='width:30px;' onchange="changeRate();" value='<?=$_brd_b_calcu_rate?>'>%</td>
			<td class="text-right">฿ <b><span id='brd_b_calcu_price'><?=number_format($brd_b_calcu_price)?></span></b></td>
			<td class="text-center"><input type='text' name='brd_c_rate' id='brd_c_rate' style='width:30px;' onchange="changeRate();" value='<?=$_brd_c_calcu_rate?>'>%</td>
			<td class="text-right">฿ <b><span id='brd_c_calcu_price'><?=number_format($brd_c_calcu_price)?></span></b></td>
			<td class="text-center"><input type='text' style='width:30px;' onchange="changeRate();" value='<?=$_brd_bkk_calcu_rate?>'>%</td>
			<td class="text-right">฿ <b><span id='brd_bkk_calcu_price'><?=number_format($brd_bkk_calcu_price)?></span></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="text-center"><input type='text' name='prl_rate' id='prl_rate' style='width:30px;' onchange="changeRate();" value='<?=$_prl_calcu_rate?>'>%</td>
			<td class="text-right">฿ <b><span id='prl_calcu_price'><?=number_format($prl_calcu_price)?></span></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="text-center"><input type='text' name='muk_rate' id='muk_rate' style='width:30px;' onchange="changeRate();" value='<?=$_muk_calcu_rate?>'>%</td>
			<td class="text-right">฿ <b><span id='muk_calcu_price'><?=number_format($muk_calcu_price)?></span></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="text-center"><input type='text' style='width:30px;' onchange="changeRate();" value='<?=$_jol_calcu_rate?>'>%</td>
			<td class="text-right">฿ <b><span id='jol_calcu_price'><?=number_format($jol_calcu_price)?></span></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="text-center"><input type='text' name='tm_rate' id='tm_rate' style='width:30px;' onchange="changeRate();" value='<?=$_tm_calcu_rate?>'>%</td>
			<td class="text-right">฿ <b><span id='tm_calcu_price'><?=number_format($_calcu_price)?></span></b></td>
			<td style="padding:0; border:none;"></td>

			<td class="text-right">฿ <b><span id='last_calcu_price'><?=number_format($last_calcu_price)?></span></b></td>
		</tr>
	</table>


		<!-- 최종 정산 -->
        <div class="section-title">
			<h2>정산</h2>
        </div>
<?
	if($_cal_mode == "Y"){
		$_team_bouns = $cal_data['CAL_TEAM_BOUNS'];
	}else{

		if($shop_cal_rate > 2900  && $shop_cal_rate < 3501){
			$_team_bouns = 1000 * $team_count;
		}elseif($shop_cal_rate > 3500){
			$_team_bouns = 2000 * $team_count;
		}else{
			$_team_bouns = 0;
		}
	}

	

    $muk_bouns_rate =$muk_data['report_price'] / $team_count;

	if($muk_bouns_rate > 3999 && $muk_bouns_rate < 6000){
		$muk_bouns = 500 * $team_count;
	}elseif($muk_bouns_rate > 5999){
		$muk_bouns = 1000 * $team_count;
	}else{
		$muk_bouns = 0;
	}
	if($shop_cal_rate < 1101){
			$muk_bouns =0;
	}


	if($_cal_mode == "Y"){
		$car_calcu_price = $cal_data['CAL_CAR_DEDUCTION'];
		$deduction_price =  $cal_data['CAL_DEDUCTION_PRICE'];
		$deduction_memo = $cal_data['CAL_DEDUCTION_MEMO'];
		$balance_price = $cal_data['CAL_TOUR_BALANCE'];
		$balance_date =  $cal_data['CAL_TOUR_BALANCE_DATE'];
	}else{
		if($shop_cal_rate < 1101){
			$car_calcu_price =0;
		}else{
			$car_calcu_price = $team_count * 2000;
		}

		$deduction_price = 0;
		$balance_price = 0;
	}

	if($_view_group_type == 'HM' ){
		$guide_cal_price = $last_calcu_price - $_view_balance_tour_fee - $car_calcu_price - $deduction_price - $balance_price + $muk_bouns ;//실지급 총액
	}elseif($_view_group_type == 'FA' ){
		$guide_cal_price = ($last_calcu_price/2) - ($_view_balance_tour_fee + $balance_price) - $car_calcu_price - $deduction_price ;//실지급 총액
	}else{
		$guide_cal_price = $last_calcu_price - ($_view_balance_tour_fee + $balance_price) - $car_calcu_price - $deduction_price ;//실지급 총액
	}
	
	$_show_push_cont = $_view_group_name." 그룹의 정산이 완료되었습니다.";
?>
	<table class="table-style" style='float:left; margin-right:15px;'>
		<tr>
			<th>적용환율</th>
			<th>총인원</th>
			<th>쌍당매출</th>
			<th>묵다라 보너스</th>	
			<th>투어피발란스</th>
			<th>기타</th>
			<th>정산 총액</th>
		</tr>
		<tr>
			<td class="text-center"><input type='text' style='width:50px;' id='cal_rate' name='cal_rate' onchange="changeRate();" value='<?=$_ex_rate_get?>'></td>
			<td class="text-center"><input type='text' style='width:30px;' name='team_count' value='<?=$team_count?>'> 팀</td>
			<td class="text-right">$ <?=number_format($shop_cal_rate)?></td>
			<td class="text-right">฿ <?=number_format($muk_bouns)?></td>
			<td class="text-right"><span style='color:<?=$_show_balance_color?>'>฿ <?=number_format($_view_balance_tour_fee)?></span></td>
			<td><input type='text' name='deduction_memo' value='<?=$deduction_memo?>'></td>
			<td class="text-right">฿ <b class="this-total-price-1"><span id='last_calcu_price2'><?=number_format($last_calcu_price)?></span></b></td>
		</tr>
			<tr>
				<td colspan='7' style="padding:0; border:none; height:5px;"></td>
			</tr>
		<tr>
			<th colspan='2'>차량비공제</th>
			<td>฿ <input type='text' name='car_calcu_price' id='car_calcu_price' style='width:50px;' onchange="changeRate();" value='<?=$car_calcu_price?>'></td>
			<th colspan='2'>발란스 금액</th>

			<th>공제금액</th>
			<th>실지급 총액</th>
		</tr>
		<tr>
			<th colspan='2'>쌍당 보너스</th>
			<td>฿ <input type='text' style='width:50px;' id='team_bouns' name='team_bouns'  onchange="changeRate();" value='<?=$_team_bouns?>'></td>
			<td>  <input type='text' style='width:80px;'  id="search_st" name='balance_date' readonly value='<?=$balance_date?>'></td>
			<td>฿ <input type='text' style='width:50px;' name='balance_price' id='balance_text'  onchange="changeRate();" value='<?=$balance_price?>'></td>			
			<td>฿ <input type='text' style='width:70px;' name='deduction_price' id='deduction_price' value='<?=$deduction_price?>' onchange="changeRate();"></td>
			<td class="text-right">฿ <b class="this-total-price-2"><span id='guide_cal_price'><?=number_format($guide_cal_price)?></span></b></td>
		</tr>
	</table>
	<table class="table-style" style='float:left; margin-right:15px;'>
		<tr>
			<th class="text-center">정산관련<br>특이사항</th>
			<td><textarea style='width:300px; height:130px;' name='calculate_memo'><?=$_view_cal_memo?></textarea></td>
		</tr>
	</table>
	<table class="table-style">
		<tr>
			<th class="text-center">관리자용<br>특이사항</th>
			<td><textarea style='width:300px; height:130px;' name='calculate_admin_memo'><?=$_view_cal_admin_memo?></textarea></td>
		</tr>
	</table>



    
</form>

		</div>

			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_BOOKING_GROUP_LIST?><?=$page_link_text?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
				<?if($_view_cal_state <= 1){?>
					<button type="button" id="" style='background:#ff9e96; color:white;' class="btnstyle1 btnstyle1 btnstyle1-lg" onclick="doCalCuSubmit('<?=$_cal_mode?>','temporary','<?=$bkg_data['BKG_GID_ID']?>','<?=$_show_push_cont?>');" > 
						<i class="far fa-check-circle"></i>
						임시 저장
					</button>
				<?}?>
					<button type="button" id="" class="btnstyle1 <?=$_submit_btn_color?> btnstyle1-lg" onclick="doCalCuSubmit('<?=$_cal_mode?>','save','<?=$bkg_data['BKG_GID_ID']?>','<?=$_show_push_cont?>');" > 
						<i class="far fa-check-circle"></i>
						<?=$_submit_btn_name?>
					</button>
				</ul>
			</div>

		<div style="height:60px;"></div>
	</div>
</div>
<script src="/library/globalJavaScript.js"></script>
<script type="text/javascript"> 
<!-- 

function changeRate(){

/*
	var view_calculate_salas_dollar = <?= $_view_calculate_salas_dollar ?>;
	var view_balance_tour_fee = <?=$_view_balance_tour_fee?>;
	var ex_rate = <?= $_ex_rate ?>; 
	var ex_rate_get = $("#cal_rate").val();
	var bkg_type = "<?= $_view_group_type ?>"; 
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

	

	if(bkg_type == 'HM' ){
		var guide_cal_price =  1*(last_calcu_price) - 1*(view_balance_tour_fee) - 1*(car_calcu_price) - 1*(deduction_price) - 1*(balance_text) + 1*(muk_bouns);
	}else if(bkg_type == 'FA' ){
		var guide_cal_price =  1*(last_calcu_price)/2 - 1*(view_balance_tour_fee+ balance_text) - 1*(car_calcu_price) - 1*(deduction_price);
	}else{
		var guide_cal_price =  1*(last_calcu_price) - 1*(view_balance_tour_fee+ balance_text) - 1*(car_calcu_price) - 1*(deduction_price);
	}
	
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
*/
}

function pu_ok(key,kind,bak){
   location.href="<?=_A_PATH_BOOKING_OK?>?key="+key+"&a_mode="+kind+"&bak="+bak;
}
function bookingModify2(key){
	
	window.open("<?=_A_PATH_BOOKING_MODIFY_POPUP2?>?key="+key+"&mode=modify", "overlap_"+key, "width=1070,height=660,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
// Submit.


function doCalCuSubmit(cal_mode,mode,guide,cont){

	var form = document.formCalculate;

	var state = '<?=$_view_cal_state?>';
	if(state == 1){
		var question = " 저장된 임시 데이터가 있습니다. \n기존에 임시 저장된 데이터는 삭제됩니다. \n진행하시겠습니까?";
	}else if(state == 2){
		var question = " 정산서가 존재합니다. \n한번더 생성하시겠습니까?";
	}

	if(mode == 'save'){
		if(cal_mode == 'Y'){
			if(confirm(question)){
				//sandPushMassage("MEMBER",guide,cont,cont,"http://nirvana.wepix-hosting.co.kr/guide2/calculate/calculate_list.php");
				form.submit();
			}
		}else{
			if(confirm('정산서 저장시 부킹그룹 일정은 완료 처리됩니다. \n정산서를 저장 하시겠습니까?')){
				form.submit();
			}
		}
	}else{
		if(cal_mode == 'Y'){
			if(confirm(question)){
				form.a_mode.value = "temporaryCalculate";
				form.submit();
			}
		}else{
				form.a_mode.value = "temporaryCalculate";
				form.submit();
		}
	}
}


function bookingInfo(key){
	window.open("<?=_A_PATH_BOOKING_VIEW_POPUP?>?key="+key, "overlap_"+key, "width=900,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

function buyDetailView(mode, id){
	if($("#"+mode+"_"+id).attr("state")=="off"){
		$("#"+mode+"_"+id).show();
		$("#"+mode+"_"+id).attr("state","on");
		$("#"+mode+"_"+id+"_btn").attr("class","btnstyle1 btnstyle1-inverse btnstyle1-sm").html("▲");
	}else{
		$("#"+mode+"_"+id).hide();
		$("#"+mode+"_"+id).attr("state","off");
		$("#"+mode+"_"+id+"_btn").attr("class","btnstyle1 btnstyle1-primary btnstyle1-sm").html("▼");
	}
}
</script>
<?
include "../layout/footer_popup.php";
exit;
?>
 