<?
include "../lib/inc_common.php";

	$_action_mode = securityVal($a_mode);
	$_idx = securityVal($idx);

// ******************************************************************************************************************
// 샵 매출 등록
// ******************************************************************************************************************
if( $_action_mode == "shopSalesNew" ){
			

			
			$ss_date = securityVal($search_st);
			$_ss_date = str_replace("-","",$ss_date);
			$_ss_shop_name = securityVal($ss_shop_name);
			$_ss_product_kind = securityVal($ss_product_kind);
			$_ss_gd_name = securityVal($ss_gd_name);
			$_ss_group_name = securityVal($ss_group_name);
			$_ss_sale_name = securityVal($ss_sale_name);
			$_ss_kind_hp = securityVal($ss_kind_hp);
			$_ss_personel = securityVal($ss_personel);
			$_ss_dis_count = securityVal($ss_dis_count);
			$_ss_sale_price = securityVal($ss_sale_price);
			$_ss_report_price = securityVal($ss_report_price);
			$_ss_cash = securityVal($ss_cash);
			$_ss_credit_price = securityVal($ss_credit_price);
			$_ss_credit_deposit = securityVal($ss_credit_deposit);
			$_ss_memo = securityVal($ss_memo);



			$_ex_data = wepix_fetch_array(wepix_query_error("select ER_DOLLAR_MONEY from "._DB_EXCHANGE_RATE." where ER_KIND ='get' order by ER_IDX desc limit 0, 1"));
			$_ex_rate = $_ex_data[ER_DOLLAR_MONEY];

	for($i=0;$i<count($_ss_gd_name); $i++){
			
			if($_ss_group_name[$i] == 0){
				$guide_data =  wepix_fetch_array(wepix_query_error("select GD_ID from "._DB_GUIDE." where GD_NAME = '".$_ss_gd_name[$i]."' "));
				$_bkg_maching_date = strtotime($_ss_date);

				$bkg_date =  wepix_fetch_array(wepix_query_error("select BKG_IDX from "._DB_BOOKING_GROUP." where BKG_GID_ID ='".$guide_data[GD_ID]."' and BKG_START_DATE <= ".$_bkg_maching_date." and 
				BKG_END_DATE >= ".$_bkg_maching_date.""));
				if($bkg_date[BKG_IDX]){
					$_bkg_idx =	$bkg_date[BKG_IDX];
				}else{
					$_bkg_idx = '';
				}
			}else{
				$_bkg_idx = $_ss_group_name[$i];
			}

		$query = "insert into  "._DB_SHOP_SALES." set
            SS_NAME = '".$_ss_shop_name."',
			SS_SALE_DATE ='".$_ss_date."',
			SS_BKG_IDX = '".$_bkg_idx."',
			SS_GUIDE_NAME = '".$_ss_gd_name[$i]."' ,
			SS_SALE_NAME = '".$_ss_sale_name[$i]."',
			SS_KIND_HP = '".$_ss_kind_hp[$i]."',
			SS_PERSONEL =  '".$_ss_personel[$i]."',
			SS_DC_COUNT	 = '".$_ss_dis_count[$i]."',
			SS_PRODUCT_KIND = '".$_ss_product_kind."' ,
			SS_EXCHANGE_RATE = '".$_ex_rate."',
			SS_SALE_PRICE = '".$_ss_sale_price[$i]."' ,
			SS_REPORT_PRICE = '".$_ss_report_price[$i]."',
			SS_CASH = '".$_ss_cash[$i]."',
			SS_CREDIT_PRICE	 = '".$_ss_credit_price[$i]."',
			SS_CREDIT_DEPOSIT = '".$_ss_credit_deposit[$i]."' ,
			SS_MEMO = '".$_ss_memo[$i]."' ,
			SS_REG_DATE = '".$wepix_now_time."' ,
			SS_REQ_ACTIVE = 'Y',
			SS_REG_ID = '".$_ad_id."'";

					
		$result = wepix_query_error($query);
	}
	
 msg("등록 완료!",_A_PATH_SHOP_LIST);
// ******************************************************************************************************************
// 샵 매출 삭제 (ajax)
// ******************************************************************************************************************
}elseif( $_action_mode == "delShop" ){
	
		$query = "delete from  "._DB_SHOP_SALES."
			where SS_IDX = ".$_idx;

		$result = wepix_query_error($query);

		 msg("삭제 완료!",_A_PATH_SHOP_LIST);
// ******************************************************************************************************************
// 샵 수정
// ******************************************************************************************************************
}elseif( $_action_mode == "shopSalesModify" ){


			$_ss_idx = securityVal($ss_idx);

			$ss_date = securityVal($search_st);
			$_ss_date = str_replace("-","",$ss_date);
			$_ss_shop_name = securityVal($ss_shop_name);
			$_ss_product_kind = securityVal($ss_product_kind);
			$_ss_gd_name = securityVal($ss_gd_name);
			$_ss_group_name = securityVal($ss_group_name);
			$_ss_sale_name = securityVal($ss_sale_name);
			$_ss_kind_hp = securityVal($ss_kind_hp);
			$_ss_personel = securityVal($ss_personel);
			$_ss_dis_count = securityVal($ss_dis_count);
			$_ss_sale_price = securityVal($ss_sale_price);
			$_ss_report_price = securityVal($ss_report_price);
			$_ss_cash = securityVal($ss_cash);
			$_ss_credit_price = securityVal($ss_credit_price);
			$_ss_credit_deposit = securityVal($ss_credit_deposit);
			$_ss_memo = securityVal($ss_memo);
			


			$_ex_data = wepix_fetch_array(wepix_query_error("select ER_DOLLAR_MONEY from "._DB_EXCHANGE_RATE." where ER_KIND ='get' order by ER_IDX desc limit 0, 1"));
			$_ex_rate = $_ex_data[ER_DOLLAR_MONEY];

	for($i=0;$i<count($_ss_gd_name); $i++){

			if($_ss_group_name[$i] == 0){
				$guide_data =  wepix_fetch_array(wepix_query_error("select GD_ID from "._DB_GUIDE." where GD_NAME = '".$_ss_gd_name[$i]."' "));
				$_bkg_maching_date = strtotime($_ss_date);
				$bkg_date =  wepix_fetch_array(wepix_query_error("select BKG_IDX from "._DB_BOOKING_GROUP." where BKG_GID_ID ='".$guide_data[GD_ID]."' and BKG_START_DATE <= ".$_bkg_maching_date." and 
				BKG_END_DATE >= ".$_bkg_maching_date.""));
				if($bkg_date[BKG_IDX]){
					$_bkg_idx =	$bkg_date[BKG_IDX];
				}else{
					$_bkg_idx = '';
				}
			}else{
				$_bkg_idx = $_ss_group_name[$i];
			}


		$query = "update  "._DB_SHOP_SALES." set
				SS_NAME = '".$_ss_shop_name."',
				SS_SALE_DATE ='".$_ss_date."',
				SS_BKG_IDX = '".$_bkg_idx."',
				SS_GUIDE_NAME = '".$_ss_gd_name[$i]."' ,
				SS_SALE_NAME = '".$_ss_sale_name[$i]."',
				SS_KIND_HP = '".$_ss_kind_hp[$i]."',
				SS_PERSONEL =  '".$_ss_personel[$i]."',
				SS_DC_COUNT	 = '".$_ss_dis_count[$i]."',
				SS_PRODUCT_KIND = '".$_ss_product_kind."' ,
				SS_EXCHANGE_RATE = '".$_ex_rate."',
				SS_SALE_PRICE = '".$_ss_sale_price[$i]."' ,
				SS_REPORT_PRICE = '".$_ss_report_price[$i]."',
				SS_CASH = '".$_ss_cash[$i]."',
				SS_CREDIT_PRICE	 = '".$_ss_credit_price[$i]."',
				SS_CREDIT_DEPOSIT = '".$_ss_credit_deposit[$i]."' ,
				SS_MEMO = '".$_ss_memo[$i]."' ,
				SS_REG_DATE = '".$wepix_now_time."' ,
				SS_REG_ID = '".$_ad_id."'
			where SS_IDX = ".$_ss_idx."";

					
		$result = wepix_query_error($query);
	}
	msg("수정 완료!",_A_PATH_SHOP_REG.'?mode=modify&key='.$_ss_idx);
}

exit;
?>