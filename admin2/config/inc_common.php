<?
header("Content-type: text/html; charset=utf-8");

ini_set("session.use_trans_sid", 0);
ini_set("url_rewriter.tags","");
//ini_set("session.cache_expire", "3600");
//ini_set("session.gc_maxlifetime", "3600");
//ini_set("session.cookie_domain", ".");
session_save_path("../session");
session_start();

$docRoot = $_SERVER['DOCUMENT_ROOT'];
$_sess_id = $_SESSION["sess_id"];

include $docRoot."/library/globalConfig.php";
include $docRoot."/library/mysql.php";

//################################################################################
// 파일변수 - 어드민 전역
//################################################################################
$cff_ad_glob_file_src = $docRoot."/config_file/cff_ad_glob.php";
if( is_file($cff_ad_glob_file_src) == true ){

	$cff_ad_glob_setting = parse_ini_file($cff_ad_glob_file_src);
	extract($cff_ad_glob_setting);

	$cf_ad_glob_sitename = filevalue_settings($cf_salt_ad_glob_sitename);
	$cf_ad_glob_logofile = filevalue_settings($cf_salt_ad_glob_logofile);
	$cf_ad_glob_logofile_login = filevalue_settings($cf_salt_ad_glob_logofile_login);
	$cf_ad_glob_copyright = filevalue_settings($cf_salt_ad_glob_copyright);
	$cf_ad_glob_browser_title = filevalue_settings($cf_salt_ad_glob_browser_title);

	$cf_ad_glob_gnb_active_booking = filevalue_settings($cf_salt_ad_glob_gnb_active_booking);
	$cf_ad_glob_gnb_active_member_guide = filevalue_settings($cf_salt_ad_glob_gnb_active_member_guide);
	$cf_ad_glob_gnb_active_partner = filevalue_settings($cf_salt_ad_glob_gnb_active_partner);
	$cf_ad_glob_gnb_active_product2 = filevalue_settings($cf_salt_ad_glob_gnb_active_product2);
	$cf_ad_glob_gnb_active_product = filevalue_settings($cf_salt_ad_glob_gnb_active_product);
	$cf_ad_glob_gnb_dir_config = filevalue_settings($cf_salt_ad_glob_gnb_dir_config);
	$cf_ad_glob_gnb_active_osi = filevalue_settings($cf_salt_ad_glob_gnb_active_osi);

	$cf_ad_glob_d_capacity_total = filevalue_settings($cf_salt_ad_glob_d_capacity_total);
	$cf_ad_glob_d_capacity_text = filevalue_settings($cf_salt_ad_glob_d_capacity_text);

}

	if(!$cf_ad_glob_sitename) $cf_ad_glob_sitename = "WEPIX";
	if(!$cf_ad_glob_logofile) $cf_ad_glob_logofile = "logo.png";
	if(!$cf_ad_glob_logofile_login) $cf_ad_glob_logofile_login = "login_logo.png";
	if(!$cf_ad_glob_copyright) $cf_ad_glob_copyright = "operation management by WEPIX";
	if(!$cf_ad_glob_browser_title) $cf_ad_glob_browser_title = "Wepix Admin Station";
	if(!$cf_ad_glob_gnb_dir_config) $cf_ad_glob_gnb_dir_config = "_A_PATH_CONFIG_SYSTEM";

	define('_A_GLOB_SITENAME', $cf_ad_glob_sitename);
	define('_A_GLOB_LOGOFILE', $cf_ad_glob_logofile);
	define('_A_GLOB_LOGOFILE_LOGIN', $cf_ad_glob_logofile_login);
	define('_A_GLOB_COPYRIGHT', $cf_ad_glob_copyright);
	define('_A_GLOB_BROWSER_TITEL', $cf_ad_glob_browser_title);
	define('_A_GLOB_GNB_ACTIVE_BOOKING', $cf_ad_glob_gnb_active_booking);
	define('_A_GLOB_GNB_ACTIVE_MEMBER_GUIDE', $cf_ad_glob_gnb_active_member_guide);
	define('_A_GLOB_GNB_ACTIVE_PARTNER', $cf_ad_glob_gnb_active_partner);
	define('_A_GLOB_GNB_ACTIVE_PRODUCT2', $cf_ad_glob_gnb_active_product2);
	define('_A_GLOB_GNB_ACTIVE_PRODUCT', $cf_ad_glob_gnb_active_product);
	define('_A_GLOB_GNB_DIR_CONFIG', $cf_ad_glob_gnb_dir_config);
	define('_A_GLOB_GNB_ACTIVE_OSI', $cf_ad_glob_gnb_active_osi);
	define('_A_GLOB_D_CAPACITY_TOTAL', $cf_ad_glob_d_capacity_total);
	define('_A_GLOB_D_CAPACITY_TEXT', $cf_ad_glob_d_capacity_text);

//################################################################################
// 상수 선언
//################################################################################
// 경로
define('_A_FOLDER', '/admin2');
define('_A_FOLDER_MAIN', 'main'); //메인
define('_A_FOLDER_MEMBER', 'member'); //회원
define('_A_FOLDER_BOOKING', 'booking'); //부킹
define('_A_FOLDER_PARTNER', 'partner'); //파트너
define('_A_FOLDER_PRODUCT', 'product'); //상품
define('_A_FOLDER_DASHBOARD', 'dashboard'); //통계,현황
define('_A_FOLDER_BOARD', 'board'); //게시물
define('_A_FOLDER_CONFIG', 'config'); //설정
define('_A_FOLDER_LOGIN', 'login'); //로그인
define('_A_FOLDER_CALCULATE', 'calculate'); //정산

//define("_A_PATH_MAIN", ""._A_FOLDER."/main/index.php"); //메인
define("_A_PATH_MAIN", ""._A_FOLDER."/"._A_FOLDER_DASHBOARD."/calendar_booking.php"); // 달력형 부킹 현황표
define("_A_PATH_MAIN_OK", ""._A_FOLDER."/"._A_FOLDER_MAIN."/main_ok.php"); //애매한 전역 처리 페이지

define("_A_PATH_MEMBER_LIST", ""._A_FOLDER."/"._A_FOLDER_MEMBER."/member_list.php"); //회원목록
define("_A_PATH_MEMBER_REG", ""._A_FOLDER."/"._A_FOLDER_MEMBER."/member_reg.php"); //회원 등록,수정
define("_A_PATH_MEMBER_INFO_POPUP", ""._A_FOLDER."/"._A_FOLDER_MEMBER."/member_info_popup.php"); //회원정보 팝업
define("_A_PATH_MEMBER_G_LIST", ""._A_FOLDER."/"._A_FOLDER_MEMBER."/guide_list.php"); //가이드 회원목록
define("_A_PATH_MEMBER_G_REG", ""._A_FOLDER."/"._A_FOLDER_MEMBER."/guide_reg.php"); //가이드 회원 등록,수정
define("_A_PATH_MEMBER_A_LIST", ""._A_FOLDER."/"._A_FOLDER_MEMBER."/admin_list.php"); //운영자 회원목록
define("_A_PATH_MEMBER_A_REG", ""._A_FOLDER."/"._A_FOLDER_MEMBER."/admin_reg.php"); //운영자 회원 등록,수정
define("_A_PATH_MEMBER_OK", ""._A_FOLDER."/"._A_FOLDER_MEMBER."/member_ok.php"); //처리 페이지

define("_A_PATH_BOOKING_LIST", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_list.php"); //부킹목록
define("_A_PATH_BOOKING_LAND_FEE_LIST", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_land_fee_list.php"); //랜드피
define("_A_PATH_BOOKING_GROUP_LIST", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_group_list.php"); //부킹 그룹 리스트
define("_A_PATH_BOOKING_GROUP_REG", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_group_reg.php"); //부킹 그룹 쓰기
define("_A_PATH_BOOKING_GROUP_FORM_POPUP", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_group_form_popup.php"); //부킹그룹 팀 배정

define("_A_PATH_BOOKING_VIEW_POPUP", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_view_popup.php"); //부킹보기
define("_A_PATH_BOOKING_MODIFY_POPUP", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_modify_popup.php"); //부킹수정
define("_A_PATH_BOOKING_OK", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_ok.php"); //부킹수정
define("_A_PATH_BOOKING_WENTED_POPUP", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_wented_paper_popup.php"); //부킹수정

define("_A_PATH_TRAVEL_PLAN_OK", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/travel_plan_ok.php"); //확정서 처리페이지
define("_A_PATH_TRAVEL_PLAN_REG", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/travel_plan_reg.php"); //확정서 등록,수정
define("_A_PATH_TRAVEL_PLAN_VIEW_POPUP", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/travel_plan_view_popup.php"); //확정서 보기
define("_A_PATH_TRAVEL_ESTIMATE_VIEW_POPUP", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/travel_estimate_view_popup.php"); //견적서 보기
define("_A_PATH_PLAN_TEMPLATE_LIST", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/travel_plan_template_list.php"); // 확정서 리스트
define("_A_PATH_PLAN_TEMPLATE_REG", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/travel_plan_template_reg.php"); // 확정서 샘플 등록

define("_A_PATH_PARTNER_AG_LIST", ""._A_FOLDER."/"._A_FOLDER_PARTNER."/agency_list.php"); //에이전시 목록
define("_A_PATH_PARTNER_AG_BRANCH_LIST", ""._A_FOLDER."/"._A_FOLDER_PARTNER."/ajax_agency_branch_list.php"); //에이전시 목록
define("_A_PATH_PARTNER_AG_REG", ""._A_FOLDER."/"._A_FOLDER_PARTNER."/agency_reg.php"); //에이전시 등록,수정
define("_A_PATH_PARTNER_OK", ""._A_FOLDER."/"._A_FOLDER_PARTNER."/partner_ok.php"); //처리 페이지
define("_A_PATH_PARTNER_ALLIANCE_LIST", ""._A_FOLDER."/"._A_FOLDER_PARTNER."/alliance_shop_list.php"); //제휴샵 목록
define("_A_PATH_PARTNER_ALLIANCE_REG", ""._A_FOLDER."/"._A_FOLDER_PARTNER."/alliance_shop_reg.php"); //제휴샵 등록,수정

define("_A_PATH_PRODUCT_LIST", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/product_list.php"); //상품 목록
define("_A_PATH_PRODUCT_REG", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/product_reg.php"); //신규상품 등록
define("_A_PATH_PRODUCT_OK", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/product_ok.php"); //여행상품 처리페이지
define("_A_PATH_PRODUCT_MOD_POPUP", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/product_modify_popup.php"); //상품 팝업수정
define("_A_PATH_PRODUCT_MAIN_SHOW", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/product_main_show.php"); //상품 진열
define("_A_PATH_PRODUCT_MAIN_SHOW_PD_LIST", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/ajax_product_main_show_pd_list.php"); //상품 진열
define("_A_PATH_PRODUCT_CHOICE", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/ajax_product_choice.php"); //상품 선택
define("_A_PATH_PRODUCT_CHOICE_LIST", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/ajax_product_choice_list.php"); //상품 선택
define("_A_PATH_PRODUCT_CATE_LIST", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/category_list.php"); //분류 리스트
define("_A_PATH_PRODUCT_CATE_FORM", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/ajax_category_form.php"); //분류 등록,수정
define("_A_PATH_PRODUCT_CATE_SELECT_SHOW", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/ajax_category_select_show.php"); //분류 셀렉트 폼
define("_A_PATH_PRODUCT_AREA_LIST", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/area_list.php"); //여행지역
define("_A_PATH_PRODUCT_AREA_FORM", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/ajax_area_form.php"); //여행지역 등록,수정
define("_A_PATH_PRODUCT_AREA_OK", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/area_ok.php"); //여행지역 처리페이지
define("_A_PATH_PRODUCT_HOTEL_LIST", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/hotel_list.php"); // 호텔목록
define("_A_PATH_PRODUCT_HOTEL_REG", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/hotel_reg.php"); // 호텔 등록,수정
define("_A_PATH_PRODUCT_GOLF_LIST", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/golf_list.php"); // 골프목록
define("_A_PATH_PRODUCT_GOLF_REG", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/golf_reg.php"); // 골프 등록,수정

define("_A_PATH_DASHBOARD_CDAL_BOOKING", ""._A_FOLDER."/"._A_FOLDER_DASHBOARD."/calendar_booking.php"); // 달력형 부킹 현황표
define("_A_PATH_DASHBOARD_BOOKING_YEAR", ""._A_FOLDER."/"._A_FOLDER_DASHBOARD."/calendar_booking_year.php"); // 년 목록 부킹 현황표
define("_A_PATH_DASHBOARD_STICS_HOTEL", ""._A_FOLDER."/"._A_FOLDER_DASHBOARD."/statistics_hotel.php"); // 표형 호텔 현황표
define("_A_PATH_DASHBOARD_STICS_AGENCY", ""._A_FOLDER."/"._A_FOLDER_DASHBOARD."/statistics_agency.php"); // 표형 에이전시 현황표
define("_A_PATH_DASHBOARD_STICS_AGENCY_BRANCH", ""._A_FOLDER."/"._A_FOLDER_DASHBOARD."/ajax_statistics_agency_branch.php"); // 표형 에이전시 지사 현황표

define("_A_PATH_BOARD_MAIN", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_main.php"); // 게시물 관리 메인
define("_A_PATH_BOARD_LIST", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_list.php"); // 게시물 목록
define("_A_PATH_BOARD_REG", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_reg.php"); // 게시물 등록,수정
define("_A_PATH_BOARD_VIEW", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_view.php"); // 게시물 보기
define("_A_PATH_BOARD_CONFIG", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_config.php"); // 게시물 보기
define("_A_PATH_BOARD_OK", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_ok.php"); // 게시물 처리페이지
define("_A_PATH_BOARD_OK_NEW", ""._A_FOLDER."/"._A_FOLDER_BOARD."/processing.board.php"); // 게시물 처리페이지
define("_A_PATH_BOARD_PD_REVIEW", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_product_review.php"); // 상품리뷰 게시판

define("_A_PATH_CONFIG_SYSTEM", ""._A_FOLDER."/"._A_FOLDER_CONFIG."/config_system.php"); // 시스템 설정
define("_A_PATH_CONFIG_EXCHANGE_RATE", ""._A_FOLDER."/"._A_FOLDER_CONFIG."/config_exchange_rate.php"); // 환율 설정
define("_A_PATH_CONFIG_PERSONAL", ""._A_FOLDER."/"._A_FOLDER_CONFIG."/config_personal.php"); // 개인 설정
define("_A_PATH_CONFIG_OK", ""._A_FOLDER."/"._A_FOLDER_CONFIG."/config_ok.php"); //설정 처리페이지

define("_A_PATH_CONFIG_DEVELOPER", ""._A_FOLDER."/"._A_FOLDER_CONFIG."/config_developer.php"); // 개발자 설정
define("_A_PATH_CONFIG_DEVELOPER_OK", ""._A_FOLDER."/"._A_FOLDER_CONFIG."/config_developer_ok.php"); // 개발자 설정 처리페이지

define("_A_PATH_LOGIN", ""._A_FOLDER."/"._A_FOLDER_LOGIN."/admin_login.php"); // 어드민 로그인
define("_A_PATH_LOGIN_OK", ""._A_FOLDER."/"._A_FOLDER_LOGIN."/admin_login_ok.php"); // 어드민 로그인 처리페이지
define("_A_PATH_LOGOUT", ""._A_FOLDER."/"._A_FOLDER_LOGIN."/admin_logout.php"); // 어드민 로그아웃

define("_A_PATH_SHOP_LIST", ""._A_FOLDER."/"._A_FOLDER_CALCULATE."/shop_sales_list.php"); // 쇼핑샵 리스트
define("_A_PATH_SHOP_REG", ""._A_FOLDER."/"._A_FOLDER_CALCULATE."/shop_sales_reg.php"); // 쇼핑샵 등록,수정
define("_A_PATH_SHOP_OK", ""._A_FOLDER."/"._A_FOLDER_CALCULATE."/shop_sales_ok.php"); // 쇼핑샵 처리 페이지
define("_A_PATH_GROUP_CALCUATE", ""._A_FOLDER."/"._A_FOLDER_CALCULATE."/group_calculate_reg_new.php"); // 그룹정산 페이지
define("_A_PATH_GROUP_CALCUATE_OK", ""._A_FOLDER."/"._A_FOLDER_CALCULATE."/group_calculate_ok.php"); // 그룹정산 처리페이지


// OSI +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
define('_A_FOLDER_OSI', 'osi');
define("_A_PATH_OSI_SUMMONER", ""._A_FOLDER."/"._A_FOLDER_OSI."/osi_summoner.php"); //osi 소환사 관리
define("_A_PATH_OSI_COMMENT_LIST", ""._A_FOLDER."/"._A_FOLDER_OSI."/osi_comment_list.php"); //osi 코멘트 리스트
// OSI +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// 설정
define("_A_SYSTEM_SMARTEDITOR", "smarteditor2-2.8.2.3"); // 네이버 스마트 에디터 버젼



//################################################################################
// 로그인 체크
//################################################################################
if( $pageGroup != "login" ){

	$_sess_admin_data = wepix_fetch_array(wepix_query_error("select * from "._DB_ADMIN." where AD_ID = '".$_sess_id."' "));
	if( !$_sess_admin_data[AD_IDX] ){
		msg("로그인 해주세요.", _A_PATH_LOGIN);
		exit;
	}

	$_ad_id = $_sess_admin_data[AD_ID];
	$_ad_nick = $_sess_admin_data[AD_NICK];
	$_ad_level = $_sess_admin_data[AD_LEVEL];
	$_ad_name = $_sess_admin_data[AD_NAME];
	$_ad_name_eg = $_sess_admin_data[AD_NAME_EG];
	$_ad_mail = $_sess_admin_data[AD_MAIL];
	$_ad_kakao = $_sess_admin_data[AD_KAKAO];
	$_ad_line = $_sess_admin_data[AD_LINE];
	$_ad_birth_day =  explode("-",$_sess_admin_data[AD_BIRTH]);
	$_ad_phone = explode("-",$_sess_admin_data[AD_PHONE]);
	$_ad_lang = $_sess_admin_data[AD_LANG];

	$_view_ad_birth_y = $_ad_birth_day[0];
	$_view_ad_birth_m = $_ad_birth_day[1];
	$_view_ad_birth_d = $_ad_birth_day[2];;
	$_view_ad_phone_1 = $_ad_phone[0];
	$_view_ad_phone_2 = $_ad_phone[1];
	$_view_ad_phone_3 = $_ad_phone[2];
  
}

//################################################################################
// 어드민 GNB 경로
//################################################################################
//설정관리
if( _A_GLOB_GNB_DIR_CONFIG ){
	define('_GNB_DIR_CONFIG', _A_FOLDER._A_GLOB_GNB_DIR_CONFIG); 
}else{
	define('_GNB_DIR_CONFIG', _A_FOLDER."config/config_system.php");
}
//################################################################################
// 언어셋
//################################################################################
if( $_ad_lang=="usa"){
	include $docRoot."/"._A_FOLDER."/lib/language_usa.php";
}else{
	include $docRoot."/"._A_FOLDER."/lib/language_kor.php";
}

//################################################################################
// 글로벌로 옮길 전체변수
//################################################################################
$bva_gender['M'] = '남성';
$bva_gender['W'] = '여성';

//################################################################################
// 어드민에만 사용하는 함수
//################################################################################
// 페이징
function paging( $current_page, $total_page, $list_num, $page_num, $url ){

	$link_str .= "<div class='paging'>";

	//페이지가 하나만 있을때
	if( $total_page == 1 OR $total_page == 0 ){
		$link_str .= "<a class='first_page'></a>";
		$link_str .= "<a class='post'></a>";
		$link_str .= "<a class='now'>1</a>";
		$link_str .= "<a class='next'></a>";
		$link_str .= "<a class='last_page'></a>";
	}else{
		$start_page = @(((int)(($current_page-1)/$page_num))*$page_num)+1;
		$temp_pnum = $page_num - 1 ;
		$end_page = $start_page + $temp_pnum;

		if ($end_page >= $total_page) $end_page = $total_page;

		if ($current_page > 1) {
			$link_str .= "<a href='".$url."' class='first_page'></a>";
			$link_str .= "<a href='".$url.($current_page-1)."' class='post'></a>";
		}else{
			$link_str .= "<a class='first_page'></a>";
			$link_str .= "<a class='post'></a>";
		}

		// 페이지 루프
		if ($total_page > 1) {
			for ($i=$start_page;$i<=$end_page;$i++) {
				if ($current_page != $i) {
					$link_str .= "<a href='$url$i' class='rest'>$i</a>";
				} else {
					$link_str .= "<a class='now'>$i</a>";
				}
			}
		}
		if ($current_page < $total_page) {
			$link_str .= "<a href='$url".($current_page+1)."' class='next'>".$next_page."</a>";
			$link_str .= "<a href='$url$total_page'>".$last_page."</a>";
		}else{
			$link_str .= "<a class='next'>".$next_page."</a>";
			$link_str .= "<a>".$last_page."</a>";
		}
	}
	$link_str .= "</div>";
    return $link_str;
}
?>