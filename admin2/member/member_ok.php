<?
include "../lib/inc_common.php";

	$_action_mode = securityVal($a_mode);
	$_ajax_mode = securityVal($ajax_mode);

	$_user_idx = securityVal($user_idx);
	$_user_pw = securityVal($user_pw);

// ******************************************************************************************************************
// 가이드 등록
// ******************************************************************************************************************
if( $_action_mode == "guideNew" ){

	$_gd_name = securityVal($gd_name);
	$_gd_nick = securityVal($gd_nick);
	$_gd_birth1 = securityVal($gd_birth1);
	$_gd_birth2 = securityVal($gd_birth2);
	$_gd_birth3 = securityVal($gd_birth3);
	$_gd_phone1 = securityVal($gd_phone1);
	$_gd_phone2 = securityVal($gd_phone2);
	$_gd_phone3 = securityVal($gd_phone3);
	$_gd_mail = securityVal($gd_mail);
	$_gd_kakao = securityVal($gd_kakao);
	$_gd_line = securityVal($gd_line);
	$_gd_super = securityVal($gd_super);
	$_gd_memo = securityVal($gd_memo);
    $_gd_state = securityVal($gd_state);
    $_gd_chat_id = securityVal($gd_chat_id);
	$_gd_active  = securityVal($gd_active);
	$_gd_birth = $_gd_birth1."-".$_gd_birth2."-".$_gd_birth3;
	$_gd_phone = $_gd_phone1."-".$_gd_phone2."-".$_gd_phone3;

	$_g_pw = securityVal($g_pw);
	$_gd_pw = wepix_pw($_g_pw);

	if($_gd_state == 1){
		$_gd_state_id = $_ad_id;
	}

	$query = "insert into "._DB_GUIDE." set
		GD_NAME = '".$_gd_name."',
		GD_NICK = '".$_gd_nick."',
		GD_BIRTH = '".$_gd_birth."',
		GD_PHONE = '".$_gd_phone."',
		GD_MAIL = '".$_gd_mail."',
		GD_KAKAO = '".$_gd_kakao."',
		GD_LINE = '".$_gd_line."',
        GD_MEMO = '".$_gd_memo."',
        GD_CHAT_ID = '".$_gd_chat_id."',
		GD_PW = '".$_gd_pw."',
		GD_STATE = '".$_gd_state."',
		GD_STATE_ID = '".$_gd_state_id."',
		GD_SUPER = '".$_gd_super."',
		GD_VIEW_YN = '".$_gd_active."' ,
		GD_REG_DATE = '".$wepix_now_time."',
		GD_REG_MODE = 'A' ";
	wepix_query_error($query);

	msg("등록완료!", _A_PATH_MEMBER_G_LIST);

// ******************************************************************************************************************
// 가이드 수정
// ******************************************************************************************************************
}elseif( $_action_mode == "guideModify" ){

	$_gd_idx = securityVal($gd_idx);
	$_gd_name = securityVal($gd_name);
	$_gd_nick = securityVal($gd_nick);
	$_gd_birth1 = securityVal($gd_birth1);
	$_gd_birth2 = securityVal($gd_birth2);
	$_gd_birth3 = securityVal($gd_birth3);
	$_gd_phone1 = securityVal($gd_phone1);
	$_gd_phone2 = securityVal($gd_phone2);
	$_gd_phone3 = securityVal($gd_phone3);
	$_gd_mail = securityVal($gd_mail);
	$_gd_kakao = securityVal($gd_kakao);
	$_gd_line = securityVal($gd_line);
	$_gd_super = securityVal($gd_super);
	$_gd_memo = securityVal($gd_memo);
	$_gd_state = securityVal($gd_state);
	$_new_pw = securityVal($new_pw);
	$_g_pw = securityVal($g_pw);
    $_gd_chat_id = securityVal($gd_chat_id);
	$_gd_active  = securityVal($gd_active);

	$guide_data = wepix_fetch_array(wepix_query_error("select * from "._DB_GUIDE." where GD_IDX = '".$_gd_idx."' "));

	if( $_new_pw == "Y" ){
		$_gd_pw = wepix_pw($_g_pw);
	}else{
		$_gd_pw = $guide_data[GD_PW];
	}

	if($_gd_state == 1 && $guide_data[GD_STATE] == 0){
		$_gd_state_id = $_ad_id;
	}else{
		$_gd_state_id = $guide_data[GD_STATE_ID];
	}

	$_gd_birth = $_gd_birth1."-".$_gd_birth2."-".$_gd_birth3;
	$_gd_phone = $_gd_phone1."-".$_gd_phone2."-".$_gd_phone3;

	$query = "update "._DB_GUIDE." set 
		GD_NAME = '".$_gd_name."',
		GD_NICK = '".$_gd_nick."',
		GD_BIRTH = '".$_gd_birth."',
        GD_PHONE = '".$_gd_phone."',
        GD_CHAT_ID = '".$_gd_chat_id."',
		GD_MAIL = '".$_gd_mail."',
		GD_KAKAO = '".$_gd_kakao."',
		GD_LINE = '".$_gd_line."',
		GD_MEMO = '".$_gd_memo."',
		GD_PW = '".$_gd_pw."',
		GD_STATE = '".$_gd_state."',
		GD_STATE_ID = '".$_gd_state_id."',
		GD_SUPER = '".$_gd_super."',
		GD_VIEW_YN = '".$_gd_active."' ,
		GD_UP_DATE = '".$wepix_now_time."'
		where GD_IDX = '".$_gd_idx."' ";
	wepix_query_error($query);

	msg("수정 완료!", _A_PATH_MEMBER_G_REG."?mode=modify&key=".$_gd_idx);

// ******************************************************************************************************************
// 운영자 등록
// ******************************************************************************************************************
}elseif( $_action_mode == "adminNew" ){


    $_ad_id = securityVal($ad_id);
	$_ad_name = securityVal($ad_name);
	$_ad_name_eg = securityVal($ad_name_eg);
	$_ad_nick = securityVal($ad_nick);
	$_ad_birth1 = securityVal($ad_birth1);
	$_ad_birth2 = securityVal($ad_birth2);
	$_ad_birth3 = securityVal($ad_birth3);
	$_ad_phone1 = securityVal($ad_phone1);
	$_ad_phone2 = securityVal($ad_phone2);
	$_ad_phone3 = securityVal($ad_phone3);
	$_ad_mail = securityVal($ad_mail);
	$_ad_kakao = securityVal($ad_kakao);
	$_ad_line = securityVal($ad_line);
	$_ad_level = securityVal($ad_level);
	$_ad_memo = securityVal($ad_memo);
	$_ad_lang = securityVal($ad_lang);

	
	$_ad_birth = $_ad_birth1."-".$_ad_birth2."-".$_ad_birth3;
	$_ad_phone = $_ad_phone1."-".$_ad_phone2."-".$_ad_phone3;
	
	$_a_pw = securityVal($a_pw);
	$_ad_pw = wepix_pw($_a_pw);

	$query = "insert into "._DB_ADMIN." set
		AD_ID = '".$_ad_id."',
		AD_NAME = '".$_ad_name."',
		AD_NAME_EG = '".$_ad_name_eg."',
		AD_NICK = '".$_ad_nick."',
		AD_BIRTH = '".$_ad_birth."',
		AD_PHONE = '".$_ad_phone."',
		AD_MAIL = '".$_ad_mail."',
		AD_KAKAO = '".$_ad_kakao."',
		AD_LINE = '".$_ad_line."',
		AD_MEMO = '".$_ad_memo."',
		AD_LANG ='".$_ad_lang."',
		AD_PW = '".$_ad_pw."',
		AD_LEVEL = '".$_ad_level."',
		AD_REG_DATE = '".$wepix_now_time."' ";
	wepix_query_error($query);

	msg("등록완료!", _A_PATH_MEMBER_A_LIST);

// ******************************************************************************************************************
// 운영자 수정
// ******************************************************************************************************************
}elseif( $_action_mode == "adminModify" ){

	$_ad_idx = securityVal($ad_idx);
	$_ad_name = securityVal($ad_name);
	$_ad_name_eg = securityVal($ad_name_eg);
	$_ad_nick = securityVal($ad_nick);
	$_ad_birth1 = securityVal($ad_birth1);
	$_ad_birth2 = securityVal($ad_birth2);
	$_ad_birth3 = securityVal($ad_birth3);
	$_ad_phone1 = securityVal($ad_phone1);
	$_ad_phone2 = securityVal($ad_phone2);
	$_ad_phone3 = securityVal($ad_phone3);
	$_ad_mail = securityVal($ad_mail);
	$_ad_kakao = securityVal($ad_kakao);
	$_ad_line = securityVal($ad_line);
	$_ad_level = securityVal($ad_level);
	$_ad_memo = securityVal($ad_memo);
    $_ad_lang = securityVal($ad_lang);

	$_new_pw = securityVal($new_pw);
	$_a_pw = securityVal($a_pw);

	$admin_data = wepix_fetch_array(wepix_query_error("select AD_PW from "._DB_ADMIN." where AD_IDX = '".$_ad_idx."' "));



	if( $_new_pw == "Y" ){
		$_ad_pw = wepix_pw($_a_pw);
	}else{
		$_ad_pw = $admin_data[AD_PW];
	}

	$_ad_birth = $_ad_birth1."-".$_ad_birth2."-".$_ad_birth3;
	$_ad_phone = $_ad_phone1."-".$_ad_phone2."-".$_ad_phone3;

	$query = "update "._DB_ADMIN." set 
		AD_NAME = '".$_ad_name."',
		AD_NAME_EG = '".$_ad_name_eg."',
		AD_NICK = '".$_ad_nick."',
		AD_BIRTH = '".$_ad_birth."',
		AD_PHONE = '".$_ad_phone."',
		AD_MAIL = '".$_ad_mail."',
		AD_KAKAO = '".$_ad_kakao."',
		AD_LINE = '".$_ad_line."',
		AD_MEMO = '".$_ad_memo."',
		AD_PW = '".$_ad_pw."',
		AD_LEVEL = '".$_ad_level."',
		AD_LANG ='".$_ad_lang."',
		AD_UP_DATE = '".$wepix_now_time."'
		where AD_IDX = '".$_ad_idx."' ";
	wepix_query_error($query);

	msg("수정 완료!", _A_PATH_MEMBER_A_REG."?mode=modify&key=".$_ad_idx);
// ******************************************************************************************************************
// 유저 등록
// ******************************************************************************************************************
}elseif( $_action_mode == "userNew" ){
	$_use_id  = securityVal($use_id);
	$_use_name = securityVal($use_name);
	$_use_name_en_l = securityVal($use_name_en_l);
	$_use_name_en_f = securityVal($use_name_en_f);
	$_use_birth1 = securityVal($use_birth1);
	$_use_birth2 = securityVal($use_birth2);
	$_use_birth3 = securityVal($use_birth3);
	$_use_phone1 = securityVal($use_phone1);
	$_use_phone2 = securityVal($use_phone2);
	$_use_phone3 = securityVal($use_phone3);
	$_use_mail = securityVal($use_mail);
	$_use_kakao = securityVal($use_kakao);
	$_use_line = securityVal($use_line);
	$_use_memo = securityVal($use_memo);


	$_use_birth = $_use_birth1."-".$_use_birth2."-".$_use_birth3;
	$_use_phone = $_use_phone1."-".$_use_phone2."-".$_use_phone3;



	$query = "insert into "._DB_MEMBER." set
		USE_ID = '".$_use_id."',
		USE_NAME = '".$_use_name."',
		USE_NAME_EG_L = '".$_use_name_en_l."',
		USE_NAME_EG_F = '".$_use_name_en_f."',
        USE_BIRTH = '".$_use_birth."',
        USE_PHONE = '".$_use_phone."',
		USE_KAKAO = '".$_use_kakao."',
		USE_LINE = '".$_use_line."',
		USE_MEMO = '".$_use_memo."',
		USE_MAIL = '".$_use_mail."',
		USE_REQ_DATE = '".$wepix_now_time."'";


	wepix_query_error($query);

	msg("등록완료!", _A_PATH_MEMBER_LIST);

// ******************************************************************************************************************
// 유저 팝업수정
// ******************************************************************************************************************
}elseif( $_action_mode == "userModifyPopup" ){

	$_user_idx = securityVal($user_idx);
	$_user_id = securityVal($user_id);
	$_user_name = securityVal($user_name);
	$_user_nickname = securityVal($user_nickname);
	$_user_level = securityVal($user_level);
	$_user_state = securityVal($user_state);

	$query = "update "._DB_MEMBER." set 
		USE_NAME = '".$_user_name."',
		USE_NICKNAME = '".$_user_nickname."',
		USE_LEVEL = '".$_user_level."',
        USE_STATE = '".$_user_state."',
		USE_MOD_DATE = '".$check_time."'
		where USE_IDX = '".$_user_idx."' ";
	wepix_query_error($query);

	msg("수정 완료!", _A_PATH_MEMBER_INFO_POPUP."?id=".$_user_id);


// ******************************************************************************************************************
// 유저 수정
// ******************************************************************************************************************
}elseif( $_action_mode == "userModify" ){

	$_use_idx = securityVal($use_idx);
	$_use_name = securityVal($use_name);
	$_use_name_en_l = securityVal($use_name_en_l);
	$_use_name_en_f = securityVal($use_name_en_f);
	$_use_birth1 = securityVal($use_birth1);
	$_use_birth2 = securityVal($use_birth2);
	$_use_birth3 = securityVal($use_birth3);
	$_use_phone1 = securityVal($use_phone1);
	$_use_phone2 = securityVal($use_phone2);
	$_use_phone3 = securityVal($use_phone3);
	$_use_mail = securityVal($use_mail);
	$_use_kakao = securityVal($use_kakao);
	$_use_line = securityVal($use_line);
	$_use_memo = securityVal($use_memo);


	$_use_birth = $_use_birth1."-".$_use_birth2."-".$_use_birth3;
	$_use_phone = $_use_phone1."-".$_use_phone2."-".$_use_phone3;

	$query = "update "._DB_MEMBER." set 
		USE_NAME = '".$_use_name."',
		USE_NAME_EG_L = '".$_use_name_en_l."',
		USE_NAME_EG_F = '".$_use_name_en_f."',
        USE_BIRTH = '".$_use_birth."',
        USE_PHONE = '".$_use_phone."',
		USE_KAKAO = '".$_use_kakao."',
		USE_LINE = '".$_use_line."',
		USE_MEMO = '".$_use_memo."',
		USE_MAIL = '".$_use_mail."',
		USE_MOD_DATE = '".$wepix_now_time."' 
		where USE_IDX = '".$_use_idx."' ";
	wepix_query_error($query);

	msg("수정 완료!", _A_PATH_MEMBER_REG."?mode=modify&key=".$_use_idx);



// ******************************************************************************************************************
// CRM 패스워드 수정
// ******************************************************************************************************************
}elseif( $_action_mode == "userCrmPwModify" ){
	
	$_pw = wepix_pw($_user_pw);

	$query = "update "._DB_MEMBER." set 
		USE_PW = '".$_pw."'
		where USE_IDX = '".$_user_idx."' ";
	wepix_query_error($query);

	if($_ajax_mode=="on"){
		echo "|Processing_Complete|비밀번호 수정완료|";
		exit;
	}else{
		msg();
		exit;
	}

// ******************************************************************************************************************
// CRM에서 회원탈퇴
// ******************************************************************************************************************
}elseif( $_action_mode == "userWithdraw" ){
	
	$_pw = wepix_pw($_user_pw);

	$query = "update "._DB_MEMBER." set 
		USE_STATE = '21',
		USE_WITHDRAW_DATE = '".$action_time."'
		where USE_IDX = '".$_user_idx."' ";
	wepix_query_error($query);

	if($_ajax_mode=="on"){
		echo "|Processing_Complete|탈퇴 처리완료|";
		exit;
	}else{
		msg();
		exit;
	}

}

exit;
?>