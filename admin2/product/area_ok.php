<?
include "../lib/inc_common.php";

	$_action_mode = securityVal($a_mode);

	$_area_nation_iso = securityVal($area_nation_iso);
	$_area_name = securityVal($area_name);
	$_area_code = securityVal($area_code);
	$_area_view = securityVal($area_view);
	if( $_area_view != "N" ) $_area_view = "Y";

// ******************************************************************************************************************
// 지역 신규등록
// ******************************************************************************************************************
if( $_action_mode == "area_new" ){

	//코드 중복 검사
	$check_data = wepix_fetch_array(wepix_query_error("select AREA_IDX from "._DB_AREA." where AREA_CODE = '".$_area_code."' "));
	if( $check_data[AREA_IDX] ){
		msg("코드는 중복 불가 입니다.","area_list.php");
	}

	$query = "insert "._DB_AREA." set
		AREA_KIND = 'L',
		AREA_NATION_ISO = '".$_area_nation_iso."',
		AREA_NAME = '".$_area_name."',
		AREA_CODE = '".$_area_code."',
		AREA_VIEW = '".$_area_view."' ";
    wepix_query_error($query);

	$pass_data = wepix_fetch_array(wepix_query_error("select AREA_IDX from "._DB_AREA." where AREA_KIND = 'L' and AREA_NATION_ISO = '".$_area_nation_iso."' and AREA_NAME = '".$_area_name."' and AREA_CODE = '".$_area_code."' "));
	msg("","area_list.php?key=".$pass_data[AREA_IDX]);

// ******************************************************************************************************************
// 지역 수정
// ******************************************************************************************************************
}elseif( $_action_mode == "area_modify" ){

	$_modify_key = securityVal($modify_key);

	//코드 중복 검사
/*
	$check_data = wepix_fetch_array(wepix_query_error("select AREA_IDX from "._DB_AREA." where AREA_CODE = '".$_area_code."' and AREA_IDX != '".$_modify_key."' "));
	if( $check_data[AREA_IDX] ){
		msg("코드는 중복 불가 입니다.","area_list.php?key=".$_modify_key);
	}
*/
    $query = "update "._DB_AREA." set 
		AREA_NAME = '".$_area_name."',
		AREA_VIEW = '".$_area_view."'
		where AREA_IDX = '".$_modify_key."'";
	wepix_query_error($query);

	msg("","area_list.php?key=".$_modify_key);

// ******************************************************************************************************************
// 지역 삭제
// ******************************************************************************************************************
}elseif( $_action_mode == "area_del" ){

	$_idx = securityVal($Idx);

	wepix_query_error("delete from "._DB_AREA." where AREA_IDX = '".$_idx."' ");

	echo "|Processing_Complete|처리완료|";
	exit;
}

exit;
?>