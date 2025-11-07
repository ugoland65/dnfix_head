<?
header("Content-type: text/html; charset=utf-8");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
@deprecated
if( !$_session_save_path ) $_session_save_path = "../session";

ini_set("session.use_trans_sid", 0);
ini_set("url_rewriter.tags","");
//ini_set("session.cache_expire", "3600");
//ini_set("session.gc_maxlifetime", "3600");
//ini_set("session.cookie_domain", ".");
session_save_path($_session_save_path);
session_start();
*/

$docRoot = $_SERVER['DOCUMENT_ROOT'];

if (session_status() === PHP_SESSION_NONE) {

	// 세션 저장 경로 설정
	// DOCUMENT_ROOT가 /계정/www 이면
	// 한 단계 위인 /계정/session 으로 설정
	$docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
	$parentDir = dirname($docRoot);
	$sessionPath = $parentDir . '/session';
	
	// 디렉토리가 없으면 생성
	/*
	if (!is_dir($sessionPath)) {
		mkdir($sessionPath, 0755, true);
	}
	*/
	
	session_save_path($sessionPath);
	session_start();
}



$_sess_id = $_SESSION["sess_id"] ?? null;

include $docRoot."/library/globalConfig.php";
include $docRoot."/library/mysql.php";


//################################################################################
// 파일변수 - 어드민 전역
//################################################################################
// 변수 초기화
$cf_ad_glob_sitename = "";
$cf_ad_glob_logofile = "";
$cf_ad_glob_logofile_login = "";
$cf_ad_glob_copyright = "";
$cf_ad_glob_browser_title = "";
$cf_ad_glob_gnb_active_booking = "";
$cf_ad_glob_gnb_active_member_guide = "";
$cf_ad_glob_gnb_active_partner = "";
$cf_ad_glob_gnb_active_comparison = "";
$cf_ad_glob_gnb_active_product2 = "";
$cf_ad_glob_gnb_active_product = "";
$cf_ad_glob_gnb_dir_config = "";
$cf_ad_glob_gnb_active_osi = "";
$cf_ad_glob_d_capacity_total = "";
$cf_ad_glob_d_capacity_text = "";

$cff_ad_glob_file_src = $docRoot."/config_file/cff_ad_glob.php";
if( is_file($cff_ad_glob_file_src) == true ){

	$cff_ad_glob_setting = parse_ini_file($cff_ad_glob_file_src);
	extract($cff_ad_glob_setting);

	$cf_ad_glob_sitename = filevalue_settings($cf_salt_ad_glob_sitename ?? "");
	$cf_ad_glob_logofile = filevalue_settings($cf_salt_ad_glob_logofile ?? "");
	$cf_ad_glob_logofile_login = filevalue_settings($cf_salt_ad_glob_logofile_login ?? "");
	$cf_ad_glob_copyright = filevalue_settings($cf_salt_ad_glob_copyright ?? "");
	$cf_ad_glob_browser_title = filevalue_settings($cf_salt_ad_glob_browser_title ?? "");

	$cf_ad_glob_gnb_active_booking = filevalue_settings($cf_salt_ad_glob_gnb_active_booking ?? "");
	$cf_ad_glob_gnb_active_member_guide = filevalue_settings($cf_salt_ad_glob_gnb_active_member_guide ?? "");
	$cf_ad_glob_gnb_active_partner = filevalue_settings($cf_salt_ad_glob_gnb_active_partner ?? "");
	$cf_ad_glob_gnb_active_comparison = filevalue_settings($cf_salt_ad_glob_gnb_active_comparison ?? "");
	$cf_ad_glob_gnb_active_product2 = filevalue_settings($cf_salt_ad_glob_gnb_active_product2 ?? "");
	$cf_ad_glob_gnb_active_product = filevalue_settings($cf_salt_ad_glob_gnb_active_product ?? "");
	$cf_ad_glob_gnb_dir_config = filevalue_settings($cf_salt_ad_glob_gnb_dir_config ?? "");
	$cf_ad_glob_gnb_active_osi = filevalue_settings($cf_salt_ad_glob_gnb_active_osi ?? "");

	$cf_ad_glob_d_capacity_total = filevalue_settings($cf_salt_ad_glob_d_capacity_total ?? "");
	$cf_ad_glob_d_capacity_text = filevalue_settings($cf_salt_ad_glob_d_capacity_text ?? "");

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
	define('_A_GLOB_GNB_ACTIVE_COMPARISON', $cf_ad_glob_gnb_active_comparison);
	define('_A_GLOB_GNB_ACTIVE_PRODUCT2', $cf_ad_glob_gnb_active_product2);
	define('_A_GLOB_GNB_ACTIVE_PRODUCT', $cf_ad_glob_gnb_active_product);
	define('_A_GLOB_GNB_DIR_CONFIG', $cf_ad_glob_gnb_dir_config);
	define('_A_GLOB_GNB_ACTIVE_OSI', $cf_ad_glob_gnb_active_osi);
	define('_A_GLOB_D_CAPACITY_TOTAL', $cf_ad_glob_d_capacity_total);
	define('_A_GLOB_D_CAPACITY_TEXT', $cf_ad_glob_d_capacity_text);


include $docRoot."/admin2/lib/admin_path.php";

// 변수 초기화
$pageGroup = $pageGroup ?? $pgroup ?? "";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 로그인 체크
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if( $pageGroup != "login" ){

	if( !empty($_sess_id) ){
		$_sess_admin_data = sql_fetch_array(sql_query_error("select * from admin where ad_id = '".$_sess_id."' "));
	}else{
		$_sess_admin_data = [];
	}
	
	if( !$_sess_admin_data['idx'] ){
		msg("로그인 해주세요.", _A_PATH_LOGIN);
		exit;
	}

	$_ad_idx = $_sess_admin_data['idx'];
	$_ad_id = $_sess_admin_data['ad_id'];
	$_ad_nick = $_sess_admin_data['ad_nick'];
	$_ad_name = $_sess_admin_data['ad_name'];
	$_ad_level = $_sess_admin_data['ad_level'];
	$_ad_image = $_sess_admin_data['ad_image'];
	$_ad_lang = $_sess_admin_data['ad_lang'] ?? "";
  
}else{
	$_ad_lang = "";
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 변수
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$_moneyplan_cate = array(
		array( "name" => "퇴직금" ),
		array( "name" => "대출금" )
	);

	$_partners_cate = [
		["name" => "성인용품공급" ],
		["name" => "성인용품도매" ],
		["name" => "성인용품해외" ],
		["name" => "운영제휴" ],
		["name" => "운영서비스" ],
		["name" => "부자재" ],
		["name" => "기타" ],
	];

	$_work_log_cate = array(
		array( "name" => "업무일지" ),
		array( "name" => "프로젝트" ),
		array( "name" => "기획안" ),
		array( "name" => "업무요청" ),
		array( "name" => "공지사항" )
	);

	$_work_manual_cate = array(
		array( "name" => "C/S 매뉴얼" ),
		array( "name" => "발주(수입)" ),
		array( "name" => "발주(국내)" ),
		array( "name" => "상품작업" ),
		array( "name" => "운영/지원" ),
		array( "name" => "배송관련" ),
		array( "name" => "오픈마켓" ),
		array( "name" => "인사/사칙" ),
		array( "name" => "회사생활" ),
		array( "name" => "인트라넷" ),
		array( "name" => "쑈당몰" ),
		array( "name" => "오나디비" ),
	);

	$_work_unit_cate = array(
		array( "name" => "디자인" ),
		array( "name" => "상품작업" ),
		array( "name" => "주문/발주" ),
		array( "name" => "배송" ),
		array( "name" => "재고관리" ),
		array( "name" => "생활" ),
	);

	$_arr_national = array(
		array( "name" => "일본", "code" => "jp" ),
		array( "name" => "중국", "code" => "cn" ),
		array( "name" => "한국", "code" => "kr" ),
		array( "name" => "달러", "code" => "dollar" )
	);



	//게시판 변수
	$_bo_gv_mode['IG'] = "가상";
	$_bo_gv_mode['IG2'] = "가상(비)";
	$_bo_gv_mode['BS'] = "일반";
	$_bo_gv_mode['NT'] = "공지";





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
// 어드민에만 사용하는 함수
//################################################################################

// 상품 in세일 아이콘
function in_sale_icon( $ps_in_sale_s, $ps_in_sale_e, $ps_in_sale_data ){
	global $action_time;

	if( $ps_in_sale_s <= $action_time && $ps_in_sale_e >= $action_time ){
		
		$_data = json_decode($ps_in_sale_data, true);

		if( $_data['sale_mode'] == "period" ){
			$_sale_name = "기간할인중 ";
		}else{
			$_sale_name = "일일할인중 ";
		}
		$shtml = "<div class='in-sale-icon-wrap'><span class='isi ".$_data['sale_mode']."'>".$_sale_name." <b>".$_data['sale_per']."</b>%</span> <span class='isi-date'>".date('y.m.d H:i',strtotime($ps_in_sale_s))." ~ ".date('y.m.d H:i',strtotime($ps_in_sale_e))."</span></div>";
	}

    return $shtml;

}


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