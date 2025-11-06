<?
//DB 테이블
define("_DB_ADMIN", "ADMIN_MEMBER"); //운영자
define("_DB_AD_MEMO", "ADMIN_MEMO"); //운영자 메모

define("_DB_BRIDGE_LOG", "bridge_log"); //링크클릭 로그
define("_DB_COMMENT_ALL", "comment_all"); //코멘트 공용

define("_DB_WISH", "WISH_DB"); //위시리스트
define("_DB_PRODUCT_TRAVEL", "PRODUCT_DB"); //여행상품
define("_DB_PRODUCT_REVIEW_TRAVEL", "PRODUCT_REVIEW"); //상품리뷰
define("_DB_PRODUCT_CATAGORY_TRAVEL", "PRODUCT_CATAGORY"); //여행상품 분류

define("_DB_MEMBER", "USER_MEMBER"); //회원
define("_DB_GUIDE", "GUIDE_MEMBER"); //가이드


define("_DB_AGENCY", "AGENCY"); //에이전시
define("_DB_ALLIANCE_SHOP", "ALLIANCE_SHOP"); //제휴샵
define("_DB_ALLIANCE_SHOP_CALCULATE", "ALLIANCE_SHOP_CALCULATE"); //제휴샵 정산율

define("_DB_MAIN_PRODUCT_SHOW", "MAIN_PRODUCT_SHOW");
define("_DB_BOOKING_MACHING", "BOOKING_MACHING"); //부킹 매칭

define("_DB_BOOKING_PARENT", "BOOKING_PARENT"); //부킹 팀 <-- 점차 삭제
define("_DB_BOOKING", "BOOKING_PARENT"); //부킹 팀

define("_DB_BOOKING_GROUP", "BOOKING_GROUP"); //부킹 그룹
define("_DB_BOOKING_GOLF", "BOOKING_GOLF"); //부킹 그룹
define("_DB_BOOKING_SETTING", "BOOKING_SETTING"); //부킹 셋팅
define("_DB_SCHEDULE_TRAVEL", "SCHEDULE"); //여행일정
define("_DB_BUY_PRODUCT_TRAVEL", "BUY_PRODUCT"); //
define("_DB_PAYMENT_TRAVEL", "PAYMENT_DB"); //
define("_DB_PAYMENT_UNPAID_TRAVEL", "PAYMENT_UNPAID"); //
define("_DBPAYMENT_REMITTANCE_TRAVEL", "PAYMENT_REMITTANCE"); //
define("_DB_BILL_PARENT_TRAVEL", "PAYMENT_UNPAID_PARENT"); // 
define("_DB_BILL_TRAVEL", "PAYMENT_UNPAID"); // 
define("_DB_SCHEDULE", "SCHEDULE"); // 나의여행 일정
define("_DB_BOARD_CONFIG", "BOARD_CONFIG"); // 게시판 설정 (구작업 작업후 폐기)
define("_DB_BOARD_A_CONFIG", "BOARD_A_CONFIG"); // 게시판 설정 (신작업)
define("_DB_BOARD_VIEW_CHECK", "BOARD_VIEW_CHECK"); // 게시판 보기확인
define("_DB_EXCHANGE_RATE", "EXCHANGE_RATE"); // 환율정보
define("_DB_HOTEL", "HOTEL_DB"); //
define("_DB_HOTEL_ROOM_TYPE", "ROOM_TYPE_DB"); //
define("_DB_HOTEL_STATISTICS", "STATISTICS_HOTEL"); // 호텔 통계
define("_DB_AREA", "AREA"); //여행지역
define("_DB_GOLF", "GOLF_DB"); //골프장
define("_DB_SHOP_SALES", "SHOP_SALES"); // 샵매출 
define("_DB_WANTED", "WANTED_PAPER"); // 수배서
define("_DB_GROUP_OTHER", "OTHER_DB"); // 오더페이 (그룹지출)
define("_DB_TRAVEL_PLAN", "TRAVAL_PLAN"); // 확정서
define("_DB_TRAVEL_PLAN_TEMPLATE", "TRAVEL_PLAN_TEMPLATE"); // 확정서 샘플
define("_DB_TRAVEL_PLAN_TEMPLATE_GOODS", "TRAVEL_PLAN_TEMPLATE_GOODS"); // 확정서 상품 샘플
define("_DB_SETTING", "SETTING"); // 셋팅테이블
define("_DB_EMAIL_CERTIFY", "USER_EMAIL_CERTIFY"); // 이메일 인증 테이블
define("_DB_CALCULATE", "CALCULATE"); // 정산
define("_DB_FILE", "FILE"); // 파일 테이블
define("_DB_LOG", "LOGIN_LOG"); // 로그 테이블
define("_DB_PAYMENT_GATE", "PAYMENT_GATE"); // PG결제 기록 테이블

define("_DB_LIKE", "MY_LIKE"); //좋아요 테이블


// DG MALL 가격비교 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
define("_DB_BRAND", "BRAND_DB"); // 브랜드
define("_DB_TAG", "TAG"); // 구조설정
define("_DB_MAKER", "MAKER_DB"); // 제조사
define("_DB_COMPARISON_COMM", "COMPARISON_COMMENT"); // 가격비교 코멘트
define("_DB_COMPARISON", "COMPARISON_DB"); // 가격비교
define("_DB_COMPARISON_TAG", "COMPARISON_TAG"); // 가격비교
define("_DB_COMPARISON_LINK", "COMPARISON_LINK"); // 가격비교 링크
define("_DB_SITE", "SITE_DB"); // 싸이트

define("_DB_RANK", "prd_ranking"); // 랭킹

// OSI +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
define("_DB_OSI_SUMMONER", "OSI_SUMMONER"); // 소환사
define("_DB_OSI_SUMMONER_COMMENT", "OSI_SUMMONER_COMMENT"); // 소환사 댓글

//DB쓰기 함수를 위한 지역변수 만들기
$_db_file = _DB_FILE;
$_db_log = _DB_LOG;
$_db_like = _DB_LIKE;
$_db_comparison = _DB_COMPARISON;
$_db_comparison_comm = _DB_COMPARISON_COMM;
$_db_board_a_config = _DB_BOARD_A_CONFIG;
?>