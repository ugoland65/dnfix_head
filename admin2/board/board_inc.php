<?
$_b_code = securityVal($b_code);
$_b_key = securityVal($b_key);
$_c_key = securityVal($c_key);

//게시판 코드가 있을때
if( $_b_code ) {
	
	$bo_c_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOARD_A_CONFIG." where BOARD_CODE = '".$_b_code."' "));

	$_view_bc_name = $bo_c_data[BAC_NAME]; //게시판 이름
	$_view_bc_name_show = $bo_c_data[BAC_NAME_SHOW]; //게시판 노출이름
	$_show_bc_kind = $bo_c_data[BAC_KIND]; //게시판 종류
	
	$_view_bc_category = $bo_c_data[BAC_CATEGORY]; //카테고리
	$_show_bc_category_active = $bo_c_data[BAC_CATEGORY_ACTIVE]; //카테고리 사용여부
	$_show_bc_category = "|".$bo_c_data[BAC_CATEGORY]; //카테고리
	$_ary_bc_category = explode("|", $_show_bc_category);

	$_show_bc_view_check_active = $bo_c_data[BAC_VIEW_CHECK_ACTIVE]; //읽음확인 기능
	$_show_bc_image_active = $bo_c_data[BAC_IMAGE_ACTIVE]; //이미지 첨부 기능
	$_show_bc_image_size = $bo_c_data[BAC_IMAGE_SIZE]; //이미지 업로드 제한

	$_show_bc_thumbnail_active = $bo_c_data[BAC_THUMBNAIL_ACTIVE]; //이미지 첨부 기능
	$_show_bc_thumbnail_auto_active = $bo_c_data[BAC_THUMBNAIL_AUTO_ACTIVE]; //오토 썸네일

	$_show_bc_thumbnail_w = $bo_c_data[BAC_THUMBNAIL_W]; //썸네일 가로사이즈
	$_show_bc_thumbnail_h = $bo_c_data[BAC_THUMBNAIL_H]; //썸네일 세로사이즈
	if( !$_show_bc_thumbnail_w ) $_show_bc_thumbnail_w = 150;
	if( !$_show_bc_thumbnail_h ) $_show_bc_thumbnail_h = 150;

	$_show_bc_product_active = $bo_c_data[BAC_PRODUCT_ACTIVE]; //상품연동
	$_show_bc_product_mode = $bo_c_data[BAC_PRODUCT_MODE]; //상품연동 그룹
	$_show_bc_grade_active = $bo_c_data[BAC_GRADE_ACTIVE]; //평점
	$_show_bc_link_active = $bo_c_data[BAC_LINK_ACTIVE]; //링크
	$_show_bc_recom_active = $bo_c_data[BAC_RECOM_ACTIVE]; //추천기능

	$_show_board_layout_skin = $bo_c_data[BOARD_LAYOUT_SKIN]; //게시판 스킨
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

	$_show_bc_hit_duplicate_active = $bo_c_data[BAC_HIT_DUPLICATE_ACTIVE]; //중복 조회수

	$_show_bc_manager_idx = $bo_c_data[BAC_MANAGER_IDX]; //게시판 매니져 IDX
	$_show_bc_block_ip = $bo_c_data[BAC_BLOCK_IP]; //
	$_show_bc_filter = $bo_c_data[BAC_FILTER]; //

	define("_DB_BOARD", "BOARD_".$_b_code); //게시판 테이블
	define("_DB_BOARD_COMMENT", "BOARD_".$_b_code."_COMMENT"); //코멘트 테이블

}

	//게시판 변수
/*
어드민 lib으로 이동했음
	$_bo_gv_mode['IG'] = "가상";
	$_bo_gv_mode['IG2'] = "가상(비)";
	$_bo_gv_mode['BS'] = "일반";
	$_bo_gv_mode['NT'] = "공지";
*/

/*
if( !$_b_code OR !$bo_c_data[BOARD_CODE] ){
	msg("게시판 오류","");
	exit;
}
*/
?>