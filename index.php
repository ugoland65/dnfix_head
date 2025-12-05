<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

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
//}elseif ( $check_domain == "dnfixhead.mycafe24.com" ) {
	
	// onadb 라우터로 처리
	require_once $docRoot."/onadb/router_index.php";
	exit;

}


/*



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