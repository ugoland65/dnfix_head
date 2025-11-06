<?
include "../lib/inc_common.php";

$_a_mode = securityVal($action_mode);

// ******************************************************************************************************************
// 시스템 설정
// ******************************************************************************************************************
if($_a_mode == "configSystem"){

	$_site_name = securityVal($site_name);
	$_domain = securityVal($domain);

	$_privacy_policy = securityVal($st_privacy_policy);
    $_terms_of_use = securityVal($st_terms_of_use);

	$_markieting = securityVal($st_markieting);
	$_email_certify_active = securityVal($email_certify_active);
	if(!$_email_certify_active) $_email_certify_active = "N";

	$_join_nick_indispensable = securityVal($join_nick_indispensable);
	if(!$_join_nick_indispensable) $_join_nick_indispensable = "N";

    $_email_host = securityVal($email_host);
    $_email_port = securityVal($email_port);
    $_email_account = securityVal($email_account);
    $_email_account_pw = securityVal($email_account_pw);
    $_email_name = securityVal($email_name);
    $_email_add = securityVal($email_add);

	$query = "update "._DB_SETTING." set
		SET_SITE_NAME = '".$_site_name."',
		SET_DOMAIN = '".$_domain."',
		SET_JOIN_PRIVACY_POLICY = '".$_privacy_policy."',
		SET_JOIN_TERMS_OF_USE = '".$_terms_of_use."',
		SET_JOIN_MARKETING  = '".$_markieting."',
		SET_JOIN_EMAIL_CERTIFY_ACTIVE = '".$_email_certify_active."',
		SET_JOIN_NICK_INDISPENSABLE = '".$_join_nick_indispensable."',
		SET_EMAIL_HOST = '".$_email_host."',
		SET_EMAIL_PORT = '".$_email_port."',
		SET_EMAIL_ACCOUNT = '".$_email_account."',
		SET_EMAIL_ACCOUNT_PW = '".$_email_account_pw."',
		SET_EMAIL_ADD = '".$_email_add."',
		SET_EMAIL_NAME = '".$_email_name."'
    where SET_CODE = '"._GLOB_SKIN_NAME."'";



    wepix_query_error($query);

	 msg("수정완료",_A_PATH_CONFIG_SYSTEM);

// ******************************************************************************************************************
// 환율설정
// ******************************************************************************************************************
}elseif($_a_mode == "exchangeSetting"){
    
	$_defult_symbol = securityVal($defult_symbol);
	$_defult_money = securityVal($defult_money);
    $_dollar_money = securityVal($dollar_money);
    $_won_money =securityVal($won_money);
    $_ex_kind = securityVal($ex_kind);

	 $query = "insert into  "._DB_EXCHANGE_RATE." set
            ER_KIND = '".$_ex_kind."',
			ER_DEFULT_SYMBOL	 ='".$_defult_symbol."',
			ER_DEFULT_MONEY =".$_defult_money." ,
			ER_DOLLAR_MONEY =".$_dollar_money." ,
			ER_WON_MONEY =".$_won_money." ,
			ER_REQ_DATE ='".$wepix_now_time."',
			ER_REQ_ID ='".$_ad_id."'";

	 $result = wepix_query_error($query);
	 msg("설정 완료!",_A_PATH_CONFIG_EXCHANGE_RATE);

// ******************************************************************************************************************
// 관리자 개인정보 수정
// ******************************************************************************************************************
}elseif($_a_mode == "adminModify"){
    
	$_a_pw = securityVal($a_pw);
	$_ad_name = securityVal($ad_name);
    $_ad_name_eg = securityVal($ad_name_eg);
    $_ad_nick =securityVal($ad_nick);
    $_ad_mail = securityVal($ad_mail); 
    $_ad_kakao = securityVal($ad_kakao);
    $_ad_line = securityVal($ad_line);
    $_ad_lang = securityVal($ad_lang);
    $_ad_phone1 = securityVal($ad_phone1);
    $_ad_phone2 = securityVal($ad_phone2);
    $_ad_phone3 = securityVal($ad_phone3);
    $_ad_birth1 = securityVal($ad_birth1);
    $_ad_birth2 = securityVal($ad_birth2);
    $_ad_birth3 = securityVal($ad_birth3);

    $_ad_phone = $_ad_phone1."-".$_ad_phone2."-".$_ad_phone3;
    $_ad_birth = $_ad_birth1."-".$_ad_birth2."-".$_ad_birth3;

	$pw = wepix_pw($_a_pw);

	 $query = "update "._DB_ADMIN." set
			AD_NAME = '".$_ad_name."',
            AD_PW = '".$pw."',
			AD_NICK	 = '".$_ad_nick."',
			AD_NAME_EG = '".$_ad_name_eg."' ,
			AD_BIRTH = '".$_ad_birth."' ,
			AD_PHONE = '".$_ad_phone."' ,
			AD_KAKAO = '".$_ad_kakao."',
			AD_MAIL = '".$_ad_mail."',
			AD_LINE ='".$_ad_line."',
			AD_LANG ='".$_ad_lang."',

			AD_MOD_DATE ='".$wepix_now_time."'
			where AD_ID ='".$_ad_id."'";

	 $result = wepix_query_error($query);
	 msg("수정완료","config_personal.php");


}
     
        



exit;
?>
