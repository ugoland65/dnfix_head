<?
include "../lib/inc_common.php";

	$_action_mode = securityVal($a_mode);

	$_b_code = securityVal($b_code);

	$_bac_name = securityVal($bac_name);
	$_bac_name_show = securityVal($bac_name_show);
	$_bac_kind = securityVal($bac_kind);

	$_bac_category = securityVal($bac_category);
	$_bac_category_active = securityVal($bac_category_active);
	$_bac_list_num = securityVal($bac_list_num);
	$_bac_view_check_active = securityVal($bac_view_check_active);

	$_bac_image_active = securityVal($bac_image_active);
	if( $_bac_image_active != "Y" ) $_bac_image_active = "N";

	$_bac_thumbnail_active = securityVal($bac_thumbnail_active);
	if( $_bac_thumbnail_active != "Y" ) $_bac_thumbnail_active = "N";

	$_bac_thumbnail_auto_active = securityVal($bac_thumbnail_auto_active);
	if( $_bac_thumbnail_auto_active != "Y" ) $_bac_thumbnail_auto_active = "N";

	$_bac_image_size = securityVal($bac_image_size);
	$_bac_thumbnail_w = securityVal($bac_thumbnail_w);
	$_bac_thumbnail_h = securityVal($bac_thumbnail_h);

	$_bac_product_active = securityVal($bac_product_active);
	$_bac_product_mode = securityVal($bac_product_mode);
	$_bac_grade_active = securityVal($bac_grade_active);
	$_bac_link_active = securityVal($bac_link_active);
	$_bac_recom_active = securityVal($bac_recom_active);

	$_bac_layout_skin = securityVal($bac_layout_skin);
	$_bac_skin = securityVal($bac_skin);
	$_bac_skin_mo = securityVal($bac_skin_mo);

	$_bac_access_list_mode = securityVal($bac_access_list_mode);
	$_bac_access_list_level = securityVal($bac_access_list_level);
	$_bac_access_view_mode = securityVal($bac_access_view_mode);
	$_bac_access_view_level = securityVal($bac_access_view_level);
	$_bac_access_write_mode = securityVal($bac_access_write_mode);
	$_bac_access_write_level = securityVal($bac_access_write_level);
	$_bac_access_comment_mode = securityVal($bac_access_comment_mode);
	$_bac_access_comment_level = securityVal($bac_access_comment_level);
	$_bac_access_reply_mode = securityVal($bac_access_reply_mode);
	$_bac_access_reply_level = securityVal($bac_access_reply_level);

	$_bac_hit_duplicate_active = securityVal($bac_hit_duplicate_active);
	if( $_bac_hit_duplicate_active != "Y" ) $_bac_hit_duplicate_active = "N";

	$_bac_manager_idx = securityVal($bac_manager_idx);
	$_bac_block_ip = securityVal($bac_block_ip);
	$_bac_filter = securityVal($bac_filter);

////////////////////////////////////////////////////////////////////////////////////////////////
// 게시판 새성
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_action_mode=="newBoard" ){

	if( !$_b_code ){
		msg("코드를 입력해 주세요.","");


		exit;
	}

	$board_a_config_data = wepix_fetch_array(wepix_query_error("select UID from "._DB_BOARD_A_CONFIG." WHERE BOARD_CODE = '".$_b_code."' "));
	if( $board_a_config_data[UID] ){
		msg("이미 코드가 있습니다.","");
		exit;
	}

	if( $_b_code == "temp" ){
		msg("사용 불가능 코드 입니다.","");
		exit;
	}

	//이미지 저장폴더 생성
	$file_dir_name = "../../data/board/".$_b_code;
	if(!is_dir($file_dir_name)){
/*
		mkdir($file_dir_name,0777,true);
*/
		@mkdir($file_dir_name, 0777);
		@chmod($file_dir_name, 0777);

	}

	//게시판 정보를 입력한다
	$query = "insert into "._DB_BOARD_A_CONFIG." set  
			BOARD_CODE = '".$_b_code."',
			BAC_NAME = '".$_bac_name."',
			BAC_NAME_SHOW = '".$_bac_name_show."',
			BAC_KIND = '".$_bac_kind."',
			BAC_CATEGORY_ACTIVE = '".$_bac_category_active."',
			BAC_CATEGORY = '".$_bac_category."',
			BAC_VIEW_CHECK_ACTIVE = '".$_bac_view_check_active."',
			BAC_IMAGE_ACTIVE = '".$_bac_image_active."',
			BAC_IMAGE_SIZE = '".$_bac_image_size."',
			BAC_THUMBNAIL_ACTIVE = '".$_bac_thumbnail_active."',
			BAC_THUMBNAIL_AUTO_ACTIVE = '".$_bac_thumbnail_auto_active."',
			BAC_THUMBNAIL_W = '".$_bac_thumbnail_w."',
			BAC_THUMBNAIL_H = '".$_bac_thumbnail_h."',
			BAC_PRODUCT_ACTIVE = '".$_bac_product_active."',
			BAC_PRODUCT_MODE = '".$_bac_product_mode."',
			BAC_GRADE_ACTIVE = '".$_bac_grade_active."',
			BAC_LINK_ACTIVE = '".$_bac_link_active."',
			BAC_RECOM_ACTIVE = '".$_bac_recom_active."',
			BOARD_LAYOUT_SKIN = '".$_bac_layout_skin."',
			BOARD_SKIN = '".$_bac_skin."',
			BOARD_SKIN_MO = '".$_bac_skin_mo."',
			BAC_LIST_NUM = '".$_bac_list_num."',
			BAC_ACCESS_LIST_MODE = '".$_bac_access_list_mode."',
			BAC_ACCESS_LIST_LEVEL = '".$_bac_access_list_level."',
			BAC_ACCESS_VIEW_MODE = '".$_bac_access_view_mode."',
			BAC_ACCESS_VIEW_LEVEL = '".$_bac_access_view_level."',
			BAC_ACCESS_WRITE_MODE = '".$_bac_access_write_mode."',
			BAC_ACCESS_WRITE_LEVEL = '".$_bac_access_write_level."',
			BAC_ACCESS_COMMENT_MODE = '".$_bac_access_comment_mode."',
			BAC_ACCESS_COMMENT_LEVEL = '".$_bac_access_comment_level."',
			BAC_ACCESS_REPLY_MODE = '".$_bac_access_reply_mode."',
			BAC_ACCESS_REPLY_LEVEL = '".$_bac_access_reply_level."',
			BAC_HIT_DUPLICATE_ACTIVE = '".$_bac_hit_duplicate_active."',
			BAC_MANAGER_IDX = '".$_bac_manager_idx."' ";
	wepix_query_error($query);

	$_board_teble_name = "BOARD_".$_b_code;
	$_board_comment_teble_name = "BOARD_".$_b_code."_COMMENT";

	$board_schema = "
		create table `".$_board_teble_name."` (
			`UID` int(11) unsigned not null auto_increment,
 			`HEADNUM` int(11) unsigned default '0' not null,
 			`DEPTH` int(11) unsigned default '0' not null,
 			`BOARD_MODE` ENUM('BS','NT','IG','IG2') NOT NULL DEFAULT 'BS',
 			`BOARD_SUBJECT` varchar(255) default '' not null,
 			`BOARD_WITER_NAME` varchar(50) default '' not null,
 			`BOARD_WITER_ID` varchar(50) default '' not null,
 			`BOARD_IP` varchar(100) default '' not null,
 			`BOARD_IP_SHOW` varchar(100) default '' not null,
 			`BOARD_LEVEL` tinyint(2) not null,
 			`BOARD_BODY` text NOT NULL,
 			`BOARD_CATEGORY` int(11) unsigned default '0' not null,
 			`BOARD_DATE` int(11) unsigned default '0' not null,
 			`BOARD_PASS` varchar(100) default '' not null,
 			`BOARD_HIT` int(11) unsigned default '0' not null,
 			`BOARD_LIKE` SMALLINT(6) unsigned default '0' not null,
 			`BOARD_BAD` SMALLINT(6) unsigned default '0' not null,
			`BOARD_COMMENT` int(11) unsigned default '0' not null,
 			`BOARD_TOKEN` varchar(50) default '' not null,
 			`BOARD_THUMBNAIL` varchar(255) default '' not null,
 			`BOARD_MODIFY_ID` varchar(50) default '' not null,
 			`BOARD_MODIFY_DATE` int(11) unsigned default '0' not null,
 			`BOARD_ADMIN` ENUM('Y','N','M1','M2') NOT NULL DEFAULT 'N',
			`BOARD_PD_IDX` int(11) unsigned default '0' not null,
 			`BOARD_PD_NAME` varchar(255) default '' not null,
 			`BOARD_GRADE` tinyint(2) not null,
 			`BOARD_LINK_NAME` varchar(100) default '' not null,
 			`BOARD_LINK_URL` varchar(255) default '' not null,

		primary key (UID),

		key HEADNUM (HEADNUM),
		key BOARD_CATEGORY (BOARD_CATEGORY),
		key BOARD_DATE (BOARD_DATE)

	)  ";
	wepix_query_error($board_schema);

	$board_comment_schema = "
		create table `".$_board_comment_teble_name."` (
			`COMMENT_IDX` int(11) unsigned not null auto_increment,
 			`HEADNUM` int(11) unsigned default '0' not null,
 			`DEPTH` int(11) unsigned default '0' not null,
 			`COMMENT_MODE` ENUM('BS','IG','IG2') NOT NULL DEFAULT 'BS',
 			`COMMENT_SHOW` ENUM('view', 'blind', 'del', 'delview') NOT NULL DEFAULT 'view',
 			`BOARD_UID` int(11) unsigned default '0' not null,
 			`COMMENT_ID` varchar(50) default '' not null,
 			`COMMENT_NAME` varchar(50) default '' not null,
 			`COMMENT_PASS` varchar(100) default '' not null,
 			`COMMENT_BODY` text NOT NULL,
 			`COMMENT_IP` varchar(100) default '' not null,
 			`COMMENT_IP_SHOW` varchar(100) default '' not null,
 			`COMMENT_DATE` int(11) default '0' not null,
 			`COMMENT_MO_ID` varchar(50) default '' not null,
			`COMMENT_MO_DATE` int(11) unsigned default '0' not null,
 			`COMMENT_ADMIN` ENUM('Y', 'N', 'M1', 'M2') NOT NULL DEFAULT 'N',
 			`COMMENT_REPLY` tinyint(2) not null,

		primary key (COMMENT_IDX),

		key HEADNUM (HEADNUM),
		key BOARD_UID (BOARD_UID),
		key COMMENT_DATE (COMMENT_DATE)

	)  ";
	wepix_query_error($board_comment_schema);


	msg("게시판 생성완료","board_main.php?b_code=".$_b_code);
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 게시판 설정수정 
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_action_mode == "boardConfigModify" ){

    $query = "update "._DB_BOARD_A_CONFIG." set 
			BAC_NAME = '".$_bac_name."',
			BAC_NAME_SHOW = '".$_bac_name_show."',
			BAC_KIND = '".$_bac_kind."',
			BAC_CATEGORY_ACTIVE = '".$_bac_category_active."',
			BAC_CATEGORY = '".$_bac_category."',
			BAC_VIEW_CHECK_ACTIVE = '".$_bac_view_check_active."',
			BAC_IMAGE_ACTIVE = '".$_bac_image_active."',
			BAC_IMAGE_SIZE = '".$_bac_image_size."',
			BAC_THUMBNAIL_ACTIVE = '".$_bac_thumbnail_active."',
			BAC_THUMBNAIL_AUTO_ACTIVE = '".$_bac_thumbnail_auto_active."',
			BAC_THUMBNAIL_W = '".$_bac_thumbnail_w."',
			BAC_THUMBNAIL_H = '".$_bac_thumbnail_h."',
			BAC_PRODUCT_ACTIVE = '".$_bac_product_active."',
			BAC_PRODUCT_MODE = '".$_bac_product_mode."',
			BAC_GRADE_ACTIVE = '".$_bac_grade_active."',
			BAC_LINK_ACTIVE = '".$_bac_link_active."',
			BAC_RECOM_ACTIVE = '".$_bac_recom_active."',
			BOARD_LAYOUT_SKIN = '".$_bac_layout_skin."',
			BOARD_SKIN = '".$_bac_skin."',
			BOARD_SKIN_MO = '".$_bac_skin_mo."',
			BAC_LIST_NUM = '".$_bac_list_num."',
			BAC_ACCESS_LIST_MODE = '".$_bac_access_list_mode."',
			BAC_ACCESS_LIST_LEVEL = '".$_bac_access_list_level."',
			BAC_ACCESS_VIEW_MODE = '".$_bac_access_view_mode."',
			BAC_ACCESS_VIEW_LEVEL = '".$_bac_access_view_level."',
			BAC_ACCESS_WRITE_MODE = '".$_bac_access_write_mode."',
			BAC_ACCESS_WRITE_LEVEL = '".$_bac_access_write_level."',
			BAC_ACCESS_COMMENT_MODE = '".$_bac_access_comment_mode."',
			BAC_ACCESS_COMMENT_LEVEL = '".$_bac_access_comment_level."',
			BAC_ACCESS_REPLY_MODE = '".$_bac_access_reply_mode."',
			BAC_ACCESS_REPLY_LEVEL = '".$_bac_access_reply_level."',
			BAC_HIT_DUPLICATE_ACTIVE = '".$_bac_hit_duplicate_active."',
			BAC_MANAGER_IDX = '".$_bac_manager_idx."',
			BAC_BLOCK_IP = '".$_bac_block_ip."',
			BAC_FILTER = '".$_bac_filter."'
			WHERE BOARD_CODE = '".$_b_code."' ";
    wepix_query_error($query);

	msg("게시판 수정완료","board_main.php?b_code=".$_b_code);
	exit;

}
?>