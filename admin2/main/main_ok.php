<?
include "../lib/inc_common.php";

	$_action_mode = securityVal($a_mode);

// ******************************************************************************************************************
// gnb 언어변경
// ******************************************************************************************************************
if( $_action_mode == "language_change" ){

	$_ad_lang = securityVal($value);

    $query = "update "._DB_ADMIN." set
		AD_LANG = '".$_ad_lang."'
		where AD_ID = '".$_sess_id."'";
	wepix_query_error($query);

	echo "|Processing_Complete|처리완료|".$_value;
	exit;

}
exit;
?>