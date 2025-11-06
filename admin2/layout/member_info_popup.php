<?
$pageGroup = "member";
$pageName = "member_info_popup";

include "../lib/inc_common.php";

	$_id = securityVal($id);
	$user_data = wepix_fetch_array(wepix_query_error("select * from "._DB_MEMBER." where USE_ID = '".$_id."' "));

	define("_A_GLOB_BROWSER_TITEL", "ㅁㅁㅁㅁㅁㅁㅁㅁㅁㅁㅁㅁㅁㅁㅁㅁ"); //회원목록

include "../layout/header_popup.php";
?>
<div>
</div>
<div>
	<ul></ul>
	<ul></ul>
	<ul></ul>
</div>
<?
include "../layout/footer_popup.php";
exit;
?>