<?
$pageGroup = "login";
$pageName = "login_ok";

include "../lib/inc_common.php";

	$_login_id = securityVal($login_id);
	$_login_pass = securityVal($login_pass);
	$_check_pw = wepix_pw($_login_pass);
	$_test_check_pw = wepix_pw("1q2w3e4r!");


	if( !$_login_id ){ msg("아이디를 입력하지 않았습니다.",""); }
	if( !$_login_pass ){ msg("비밀번호를 입력하지 않았습니다.",""); }

	//$admin_data = sql_fetch_array(sql_query_error("select AD_ID, AD_PW from "._DB_ADMIN." where AD_ID = '".$_login_id."' "));

	$data = wepix_fetch_array(wepix_query_error("select * from admin WHERE ad_id = '".$_login_id."' "));

/*
			msg($_test_check_pw."\\n".$_check_pw."\\n".$data['ad_pw'],"");
			exit;
*/
	if( $data['ad_id'] ){

		if( $_check_pw == $data['ad_pw'] ){

			$_SESSION["sess_id"] = $data['ad_id'];
			$_SESSION["sess_idx"] = $data['idx'];
			$_SESSION["sess_name"] = $data['ad_name'];

			insertLog($data['ad_id'], "AD", "Y"); //로그굽기
			msg("","/admin2/?pageN=main");
			exit;

		}else{

			insertLog($data['ad_id'], "AD", "N1", $_login_pass); //로그굽기
			msg("로그인 정보가 일치 하지 않습니다.2","");
			exit;
		
		}

	}else{

		insertLog($_login_id, "AD", "N2", $_login_pass); //로그굽기
		msg("로그인 정보가 일치 하지 않습니다.3","");
		exit;

	}

exit;
?>