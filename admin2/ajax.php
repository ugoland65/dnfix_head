<?
$_session_save_path = "session";
include("lib/inc_common.php");

	/* ---- v2 버전 OOP 화 --- */
	require_once __DIR__.'/autoloader.php';

	foreach($_POST as $key => $value){
		${"_".$key} = securityVal($value);
	}

	$_pageN = securityVal($pageN ?? "");
	$_get1 = securityVal($get1 ?? "");
	$_get2 = securityVal($get2 ?? "");

	$_dir = "skin";

include $_dir."/skin.".$_pageN.".php";

exit;
?>