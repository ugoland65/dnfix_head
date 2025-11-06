<?
$docRoot = $_SERVER['DOCUMENT_ROOT'];
/*
include $docRoot."/library/globalConfig.php";
include $docRoot."/library/mysql.php";
*/
//도메인 확인
$check_domain = $_SERVER['HTTP_HOST'];
$check_domain = str_replace("www.", "", $check_domain);

if( $check_domain == "jcyh.co.kr" ){
	
	exit;

//-----------------------------------------------------------------------------------------------------------------------
}elseif( $check_domain == "onadb.net" || $check_domain == "onadbs.com" ){
	
	include $docRoot."/f/inc_common.php";

	while(list($key,$value) = each($_POST)){
		${"_".$key} = securityVal($value);
	}

	$_page = securityVal($get1);
	
	if( !$_page ) $_page = "index";
	$_skin_code = $_page;


	/*
	if( IS_MOBILE ){
		$_dir = $docRoot."/f/skin/onadb_mobile/";
	}else{
		$_dir = $docRoot."/f/skin/onadb/";
	}
	*/
	$_dir = $docRoot."/f/skin/onadb/";

	//로그인 전용페이지
	if( !$_sess_key ){

		if( $_page == "mypage" || $_page == "reports" ){

			session_unset(); // 모든 세션변수를 언레지스터 시켜줌
			session_destroy(); // 세션해제함
			echo '<meta http-equiv="refresh" content="0; url=/login" >';
			exit;

		}

	}

	if( $_page == "logout" ){

		session_unset(); // 모든 세션변수를 언레지스터 시켜줌
		session_destroy(); // 세션해제함
		echo '<meta http-equiv="refresh" content="0; url=/" >';
		exit;
	
	}elseif( $_page == "index" ){
		//$_skin_code = "main";
		$_skin_code = "prd_list";

	/*
	}elseif( $_page == "mypage" ){
		$_skin_code = "mypage";
	*/

	}elseif( $_page == "u" ){
		$_nick = securityVal($get2);
		$_skin_code = "user_view";

	}elseif( $_page == "pl" ){
		$_category = securityVal($get2);
		$_pn = securityVal($get3);
		$_skin_code = "prd_list";

	}elseif( $_page == "pv" ){
		$_idx = securityVal($get2);
		$_skin_code = "prd_view";

	}elseif( $_page == "search" ){
		$_keyword = securityVal($get2);
		$_skin_code = "search";

	//게시판
	}elseif( $_page == "b" ){
		$_b_code = securityVal($get2);
		$_pn = securityVal($get3);
		$_skin_code = "board";

	}elseif( $_page == "bv" ){
		$_b_code = securityVal($get2);
		$_idx = securityVal($get3);
		$_skin_code = "board_view";

	}elseif( $_page == "login" ){
		$_layout = "no";
		$_skin_code = "login";
		//include ($docRoot."/f/login.php");
	}elseif( $_page == "join" ){
		$_layout = "no";
		$_skin_code = "join";
		//include ($docRoot."/f/join.php");


	}

	include ($docRoot."/f/skin.php");

	exit;

}






if (_GLOB_WS_CODE == "KOEDGE" ){
	if( ( !preg_match('/(iPad)/i', $_SERVER['HTTP_USER_AGENT']) && preg_match('/(iPhone|Mobile|UP.Browser|Android|BlackBerry|Windows CE|Nokia|webOS|Opera Mini|SonyEricsson|opera mobi|Windows Phone|IEMobile|POLARIS)/i', $_SERVER['HTTP_USER_AGENT']) ) AND $_GET['page'] != "join" AND $not_mobile != "ok" ) {

		$_index_path_mobile = _GLOB_INDEX_PATH_MOBILE;

		echo "<meta http-equiv=\"refresh\" content=\"0; url=".$_index_path_mobile."\">";
		exit;

	}
}

	$url = _GLOB_INDEX_PATH;
    echo "<meta http-equiv=\"refresh\" content=\"0; url=".$url."\">";
    exit;


/*
if( ( !preg_match('/(iPad)/i', $_SERVER['HTTP_USER_AGENT']) && preg_match('/(iPhone|Mobile|UP.Browser|Android|BlackBerry|Windows CE|Nokia|webOS|Opera Mini|SonyEricsson|opera mobi|Windows Phone|IEMobile|POLARIS)/i', $_SERVER['HTTP_USER_AGENT']) ) AND $_GET['page'] != "join" AND $not_mobile != "ok" ) {

	include "user/lib/inc_common.php";
	msg("","user/layout/main.php");
	//include "user/index.php";
	//include "user/layout/main.php";
	exit;

}else{

    include ("front/index.php");
    
	exit;

}
*/
?>