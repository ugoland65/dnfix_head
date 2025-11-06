<?
include "../lib/inc_common.php";

echo $a_mode;
    $a_mode = securityVal($action_mode);
	$gf_name = securityVal($gf_name);
	$gf_area = securityVal($gf_area);
    $gf_view = securityVal($gf_view);

    $gf_code = securityVal($gf_code);
    $gf_green_fee_09 =securityVal($gf_green_fee_09);
    $gf_green_fee_18 = securityVal($gf_green_fee_18);
    $gf_green_fee_36 = securityVal($gf_green_fee_36);

    $gf_caddie_fee = securityVal($gf_caddie_fee);
    $gf_cart_fee_1 = securityVal($gf_cart_fee_1);
    $gf_cart_fee_2 = securityVal($gf_cart_fee_2);
    $gf_green_coupon_fee = securityVal($gf_green_coupon_fee);
    $gf_green_9_coupon_fee = securityVal($gf_green_9_coupon_fee);
    $gf_green_18_coupon_fee = securityVal($gf_green_18_coupon_fee);
    $gf_green_set_coupon_fee = securityVal($gf_green_set_coupon_fee);
    
    $_mokey = securityVal($mokey);

        


if( $a_mode=="new"){
       $query = "insert into  ".$db_t_GOLF_DB." set 
                GF_NAME = '".$gf_name."',
                GF_VIEW =  '".$gf_view."',
                GF_COUPON_CODE =  '".$gf_code."',
                GF_GREEN_9_FEE =  '".$gf_green_fee_09."',
                GF_GREEN_18_FEE =  '".$gf_green_fee_18."',
                GF_GREEN_36_FEE =  '".$gf_green_fee_36."',
                GF_GREEN_9_COUPON_FEE =  '".$gf_green_9_coupon_fee."',
                GF_GREEN_18_COUPON_FEE =  '".$gf_green_18_coupon_fee."',
                GF_GREEN_SET_COUPON_FEE =  '".$gf_green_set_coupon_fee."',
                GF_CADDIE_FEE =  '".$gf_caddie_fee."',
                GF_CART_FEE =  '".$gf_cart_fee_1."',
                GF_DOUBLE_CART_FEE =  '".$gf_cart_fee_2."',
                GF_AREA =  '".$gf_area."'";
		$result = wepix_query_error($query);
			msg("등록 완료!","golf_list.php");

}else if($a_mode=="modify"){

		$query = "update ".$db_t_GOLF_DB."  set 
                    GF_NAME = '".$gf_name."',
                    GF_VIEW =  '".$gf_view."',
                    GF_COUPON_CODE =  '".$gf_code."',
                    GF_GREEN_9_FEE =  '".$gf_green_fee_09."',
                    GF_GREEN_18_FEE =  '".$gf_green_fee_18."',
                    GF_GREEN_36_FEE =  '".$gf_green_fee_36."',
                    GF_GREEN_9_COUPON_FEE =  '".$gf_green_9_coupon_fee."',
                    GF_GREEN_18_COUPON_FEE =  '".$gf_green_18_coupon_fee."',
                    GF_GREEN_SET_COUPON_FEE =  '".$gf_green_set_coupon_fee."',
					GF_CADDIE_FEE =  '".$gf_caddie_fee."',
                    GF_CART_FEE =  '".$gf_cart_fee_1."',
                    GF_DOUBLE_CART_FEE =  '".$gf_cart_fee_2."',
                    GF_AREA =  '".$gf_area."'
			where GF_IDX = '".$_mokey."'";
		$result = wepix_query_error($query);

	msg("수정 완료!","golf_reg.php?mode=modify&key=".$_mokey);

}elseif($a_mode=="del" AND $_mokey ){
			$query = "delete from ".$db_t_GOLF_DB."
			where GF_IDX = ".$_mokey."";
		$result = wepix_query_error($query);

			msg("삭제 완료!","golf_list.php");
}


exit;
?>
