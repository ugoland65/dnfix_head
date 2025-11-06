<?
$_b_code = securityVal($b_code);
$_b_key = securityVal($b_key);
$_c_key = securityVal($c_key);

//게시판 코드가 있을때
if( $_b_code ) {
	
	$bo_c_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOARD_A_CONFIG." where BOARD_CODE = '".$_b_code."' "));

	$_show_b_code = $_b_code;
	$_view_bc_name = $bo_c_data[BAC_NAME]; //게시판 이름
	$_view_bc_name_show = $bo_c_data[BAC_NAME_SHOW]; //게시판 노출이름

	$_view_bc_category = $bo_c_data[BAC_CATEGORY]; //카테고리
	$_show_bc_category_active = $bo_c_data[BAC_CATEGORY_ACTIVE]; //카테고리 사용여부
	$_show_bc_category = "|".$bo_c_data[BAC_CATEGORY]; //카테고리
	$_ary_bc_category = explode("|", $_show_bc_category);

	$_show_board_skin = $bo_c_data[BOARD_SKIN]; //게시판 스킨
	$_show_board_skin_mo = $bo_c_data[BOARD_SKIN_MO]; //게시판 모바일 스킨

	$_show_bc_list_num = $bo_c_data[BAC_LIST_NUM]; //게시판 목록수

	$_show_bc_access_list_mode = $bo_c_data[BAC_ACCESS_LIST_MODE];
	$_show_bc_access_list_level = $bo_c_data[BAC_ACCESS_LIST_LEVEL];
	$_show_bc_access_view_mode = $bo_c_data[BAC_ACCESS_VIEW_MODE];
	$_show_bc_access_view_level = $bo_c_data[BAC_ACCESS_VIEW_LEVEL];
	$_show_bc_access_write_mode = $bo_c_data[BAC_ACCESS_WRITE_MODE];
	$_show_bc_access_write_level = $bo_c_data[BAC_ACCESS_WRITE_LEVEL];
	$_show_bc_access_comment_mode = $bo_c_data[BAC_ACCESS_COMMENT_MODE];
	$_show_bc_access_comment_level = $bo_c_data[BAC_ACCESS_COMMENT_LEVEL];
	$_show_bc_access_reply_mode = $bo_c_data[BAC_ACCESS_REPLY_MODE];
	$_show_bc_access_reply_level = $bo_c_data[BAC_ACCESS_REPLY_LEVEL];


	define("_DB_BOARD", "BOARD_".$_b_code); //게시판 테이블
	define("_DB_BOARD_COMMENT", "BOARD_".$_b_code."_COMMENT"); //코멘트 테이블

}


	if( !$_b_code ){
		msg("게시판 오류","");
		exit;
	}
?>