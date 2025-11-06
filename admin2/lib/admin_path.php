<?

//################################################################################
// 상수 선언
//################################################################################
// 경로
define('_A_FOLDER', '/admin2');
define('_A_FOLDER_MAIN', 'main'); //메인
define('_A_FOLDER_MEMBER', 'member'); //회원
define('_A_FOLDER_BOOKING', 'booking'); //부킹
define('_A_FOLDER_PARTNER', 'partner'); //파트너
define('_A_FOLDER_PD', 'product2'); //일반상품
define('_A_FOLDER_PRODUCT', 'product'); //여행상품
define('_A_FOLDER_DASHBOARD', 'dashboard'); //통계,현황
define('_A_FOLDER_BOARD', 'board'); //게시물
define('_A_FOLDER_CONFIG', 'config'); //설정
define('_A_FOLDER_LOGIN', 'login'); //로그인
define('_A_FOLDER_CALCULATE', 'calculate'); //정산


define("_A_PATH_MAIN", "/admin2/?pageN=main"); // 메인
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
define("_A_PATH_BOOKING_MODIFY_POPUP2", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_modify_popup2.php"); //부킹수정
define("_A_PATH_BOOKING_OK", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_ok.php"); //부킹수정
define("_A_PATH_BOOKING_WENTED_POPUP", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/booking_wented_paper_popup.php"); //부킹수정
define("_A_PATH_PERSONAL_PAYMENT_LIST", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/personal_payment_list.php"); // 개인결제 list
define("_A_PATH_PERSONAL_PAYMENT_REG", ""._A_FOLDER."/"._A_FOLDER_BOOKING."/personal_payment_reg.php"); // 개인결제 reg

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

define("_A_PATH_PD_LIST", ""._A_FOLDER."/"._A_FOLDER_PD."/pd_list.php"); //일반상품 목록
define("_A_PATH_PD_OK", ""._A_FOLDER."/"._A_FOLDER_PD."/pd_ok.php"); //일반상품 목록

define("_A_PATH_PRODUCT_LIST", ""._A_FOLDER."/"._A_FOLDER_PRODUCT."/product_list.php"); //여행상품 목록
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
define("_A_PATH_VISIT_VIEW", ""._A_FOLDER."/"._A_FOLDER_DASHBOARD."/visit_view.php"); // 표형 에이전시 현황표
//define("_A_PATH_SEARCH_VIEW", ""._A_FOLDER."/"._A_FOLDER_DASHBOARD."/search_view.php"); // 표형 에이전시 현황표

define("_A_PATH_DASHBOARD_STICS_AGENCY_BRANCH", ""._A_FOLDER."/"._A_FOLDER_DASHBOARD."/ajax_statistics_agency_branch.php"); // 표형 에이전시 지사 현황표

define("_A_PATH_BOARD_MAIN", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_main.php"); // 게시물 관리 메인
define("_A_PATH_BOARD_LIST", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_list.php"); // 게시물 목록
define("_A_PATH_BOARD_REG", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_reg.php"); // 게시물 등록,수정
define("_A_PATH_BOARD_VIEW", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_view.php"); // 게시물 보기
define("_A_PATH_BOARD_CONFIG", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_config.php"); // 게시물 보기
define("_A_PATH_BOARD_OK", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_ok.php"); // 게시물 처리페이지
define("_A_PATH_BOARD_OK_NEW", ""._A_FOLDER."/"._A_FOLDER_BOARD."/processing.board.php"); // 게시물 처리페이지
define("_A_PATH_BOARD_PD_REVIEW", ""._A_FOLDER."/"._A_FOLDER_BOARD."/board_product_review.php"); // 상품리뷰 게시판


define("_A_PATH_CONFIG_OPEN_GRAPH", ""._A_FOLDER."/"._A_FOLDER_CONFIG."/config_open_graph.php"); // 오픈그래프 설정
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

// DG +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
define('_A_FOLDER_COMPARISON', 'comparison'); //가격비교

define("_A_PATH_COMPARISON_REG", ""._A_FOLDER."/"._A_FOLDER_COMPARISON."/comparison_reg.php"); // 가격비교 등록
define("_A_PATH_COMPARISON_LIST", ""._A_FOLDER."/"._A_FOLDER_COMPARISON."/comparison_list.php"); // 가격비교 리스트
define("_A_PATH_COMPARISON_LIST2", ""._A_FOLDER."/"._A_FOLDER_COMPARISON."/comparison_list2.php"); // 가격비교 리스트
define("_A_PATH_COMPARISON_OK", ""._A_FOLDER."/"._A_FOLDER_COMPARISON."/comparison_ok.php"); // 가격비교 처리페이지
define("_A_PATH_SITE_REG", ""._A_FOLDER."/"._A_FOLDER_COMPARISON."/site_reg.php"); // 사이트 등록
define("_A_PATH_SITE_LIST", ""._A_FOLDER."/"._A_FOLDER_COMPARISON."/site_list.php"); // 싸이트 리스트
define("_A_PATH_BRAND_REG", ""._A_FOLDER."/"._A_FOLDER_PD."/brand_reg.php"); // 브랜드 등록
define("_A_PATH_BRAND_LIST", ""._A_FOLDER."/"._A_FOLDER_PD."/brand_list.php"); // 브랜드 리스트
define("_A_PATH_MAKER_REG", ""._A_FOLDER."/"._A_FOLDER_PD."/maker_reg.php"); // 제조사 등록
define("_A_PATH_MAKER_LIST", ""._A_FOLDER."/"._A_FOLDER_PD."/maker_list.php"); // 제조사 리스트
define("_A_PATH_STRUCTURE_LIST", ""._A_FOLDER."/"._A_FOLDER_PD."/structure_list.php"); // 구조 리스트
define("_A_PATH_KEYWORD", ""._A_FOLDER."/"._A_FOLDER_PD."/keyword_view.php"); // 구조 리스트
define("_A_PATH_STRUCTURE_REG", ""._A_FOLDER."/"._A_FOLDER_PD."/structure_reg.php"); // 구조 리스트
define("_A_PATH_PRODUCT_CATE_LIST2", ""._A_FOLDER."/"._A_FOLDER_PD."/category_list.php"); //분류 리스트
define("_A_PATH_COMPARISON_SORT", ""._A_FOLDER."/"._A_FOLDER_COMPARISON."/comparison_sort.php"); // 표형 에이전시 지사 현황표
// DG +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// OSI +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
define('_A_FOLDER_OSI', 'osi');
define("_A_PATH_OSI_SUMMONER", ""._A_FOLDER."/"._A_FOLDER_OSI."/osi_summoner.php"); //osi 소환사 관리
define("_A_PATH_OSI_COMMENT_LIST", ""._A_FOLDER."/"._A_FOLDER_OSI."/osi_comment_list.php"); //osi 코멘트 리스트
define("_A_PATH_OSI_OK", ""._A_FOLDER."/"._A_FOLDER_OSI."/processing.osi.php"); //osi 처리페이지
// OSI +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// 설정
define("_A_SYSTEM_SMARTEDITOR", "smarteditor2-2.8.2.3"); // 네이버 스마트 에디터 버젼

?>