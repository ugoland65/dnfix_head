<?
$_session_save_path = "session";
include("lib/inc_common.php");

	/* ---- v2 버전 OOP 화 --- */
	require_once __DIR__.'/autoloader.php';

	$_pageN = securityVal($pageN);
	$_pageGroup = securityVal($pgroup);
	$_get1 = securityVal($get1);
	$_get2 = securityVal($get2);

	//echo"( _page : ".$_page.") / ( _pageGroup : ".$_pageGroup." ) / (  _get1 : ".$_get1." ) / (  _get2 : ".$_get2." ) ";

	foreach($_GET as $key => $value){
		${"_".$key} = securityVal($value);
	}

	if (strpos($_get1,":")){
		$_get_arr = explode(":", $_get1);
		for ($i=0; $i<count($_get_arr); $i++){
			$_this_get = explode("=", $_get_arr[$i]);
			$_this_key = $_this_get[0];
			$_this_val = $_this_get[1];
			${"_get_".$_this_key} = $_this_val;
		}
	}

	$_dir = "skin";

include "layout/header.php";
include $_dir."/skin.".$_pageN.".php";
include "layout/footer.php";

exit;
?>