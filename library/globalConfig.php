<?
header("Content-type: text/html; charset=utf-8");

//################################################################################
// 환경 변수
//################################################################################
@extract($_GET);
@extract($_POST);
@extract($_SERVER);

//IP확인
$_SERVER['REMOTE_ADDR'] = ( isset($_SERVER['HTTP_CF_CONNECTING_IP']) && $_SERVER['HTTP_CF_CONNECTING_IP'] != NULL) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
$check_ip = $_SERVER['REMOTE_ADDR'];

//도메인 확인
$check_domain = $_SERVER['HTTP_HOST'];
$check_domain = str_replace("www.", "", $check_domain);

$check_time = time(); #현재시간
$action_time = date("Y-m-d H:i:s");
$action_time_ymd = date("Y-m-d");
$check_request_uri  = $_SERVER['REQUEST_URI']; //현재페이지
$check_request_uri_urlencode  = urlencode($_SERVER['REQUEST_URI']); //현재페이지 인코딩

$check_query_string  = $_SERVER['QUERY_STRING']; //쿼리스트링
$check_query_string_urlencode  = urlencode($_SERVER['QUERY_STRING']); //쿼리스트링 인코딩

$check_user_agent = $_SERVER['HTTP_USER_AGENT'];

$docRoot = $_SERVER['DOCUMENT_ROOT'];


//버젼 정보
//include ('version.php');

//모바일 접속 체크
if( !preg_match('/(iPad)/i', $_SERVER['HTTP_USER_AGENT']) && preg_match('/(iPhone|Mobile|UP.Browser|Android|BlackBerry|Windows CE|Nokia|webOS|Opera Mini|SonyEricsson|opera mobi|Windows Phone|IEMobile|POLARIS)/i', $_SERVER['HTTP_USER_AGENT']) ) {
	//define('_GLOB_MOBILE', 'Y');
	$check_mobile = "Y";
	$check_device = "mobile";
	define('IS_MOBILE', true);
}else{
	$check_device = "pc";
	define('IS_MOBILE', false);
}


//################################################################################
// 파일변수 - 홈페이지 전역
//################################################################################

//파일변수 가공 함수
function filevalue_settings($str) {
    $str = stripslashes($str);
    $str = str_replace("<br>", "\n", $str);
    $str = str_replace("&quot;", "\"", $str);
    return $str;
}

// 변수 초기화
$cf_all_glob_ws_mode = "";
$cf_all_glob_ws_code = "";
$cf_all_glob_index_path = "";
$cf_all_glob_index_path_mobile = "";
$cf_all_glob_skin_name = "";
$cf_all_glob_skin_name_mobile = "";
$cf_all_glob_sys_user_email_id = "";
$cf_all_glob_sys_real_name = "";
$cf_all_glob_sys_individual_function = "";
$cf_all_glob_sys_individual_variable = "";
$cf_all_glob_sys_folder_dir_admin = "";
$cf_all_glob_sys_folder_dir_pc = "";
$cf_all_glob_sys_folder_dir_mobile = "";

$cff_all_glob_file_src = $docRoot."/config_file/cff_all_glob.php";
if( is_file($cff_all_glob_file_src) == true ){

	$cff_all_glob_setting = parse_ini_file($cff_all_glob_file_src);
	extract($cff_all_glob_setting);

	$cf_all_glob_ws_mode = filevalue_settings($cf_salt_all_glob_ws_mode ?? "");
	$cf_all_glob_ws_code = filevalue_settings($cf_salt_all_glob_ws_code ?? "");
	$cf_all_glob_index_path = filevalue_settings($cf_salt_all_glob_index_path ?? "");
	$cf_all_glob_index_path_mobile = filevalue_settings($cf_salt_all_glob_index_path_mobile ?? "");
	$cf_all_glob_skin_name = filevalue_settings($cf_salt_all_glob_skin_name ?? "");
	$cf_all_glob_skin_name_mobile = filevalue_settings($cf_salt_all_glob_skin_name_mobile ?? "");
	$cf_all_glob_sys_user_email_id = filevalue_settings($cf_salt_all_glob_sys_user_email_id ?? "");
	$cf_all_glob_sys_real_name = filevalue_settings($cf_salt_all_glob_sys_real_name ?? "");
	$cf_all_glob_sys_individual_function = filevalue_settings($cf_salt_all_glob_sys_individual_function ?? "");
	$cf_all_glob_sys_individual_variable = filevalue_settings($cf_salt_all_glob_sys_individual_variable ?? "");

	$cf_all_glob_sys_folder_dir_admin = filevalue_settings($cf_salt_all_glob_sys_folder_dir_admin ?? "");
	$cf_all_glob_sys_folder_dir_pc = filevalue_settings($cf_salt_all_glob_sys_folder_dir_pc ?? "");
	$cf_all_glob_sys_folder_dir_mobile = filevalue_settings($cf_salt_all_glob_sys_folder_dir_mobile ?? "");

	if(!$cf_all_glob_ws_mode) $cf_all_glob_ws_mode = "travel";
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//수동변수 - 후에 처리
	$cf_all_glob_sys_domain_by_skin_active = "active";

	if( $cf_all_glob_sys_domain_by_skin_active == "active" ){
		if( $check_domain == "onadb.net" ){
			//$cf_all_glob_skin_name = "onazone";
			$cf_all_glob_skin_name = "anadbs";
		}
	}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	define('_GLOB_WS_MODE', $cf_all_glob_ws_mode);
	define('_GLOB_WS_CODE', $cf_all_glob_ws_code);
	define('_GLOB_INDEX_PATH', $cf_all_glob_index_path);
	define('_GLOB_INDEX_PATH_MOBILE', $cf_all_glob_index_path_mobile);
	define('_GLOB_SKIN_NAME', $cf_all_glob_skin_name);
	define('_GLOB_SKIN_NAME_MOBILE', $cf_all_glob_skin_name_mobile);
	define('_GLOB_SITE_CODE', $cf_all_glob_skin_name);
	define('_GLOB_SYS_USER_EMAIL_ID', $cf_all_glob_sys_user_email_id); // 회원아이디를 이메일로 사용시 on
	define('_GLOB_SYS_REAL_NAME', $cf_all_glob_sys_real_name); //실명사용여부
	define('_GLOB_SYS_INDIVIDUAL_FUNCTION', $cf_all_glob_sys_individual_function); //개별함수
	define('_GLOB_SYS_INDIVIDUAL_VARIABLE', $cf_all_glob_sys_individual_variable); //개별변수
	define('_GLOB_SYS_FOLDER_DIR_ADMIN', $cf_all_glob_sys_folder_dir_admin);
	define('_GLOB_SYS_FOLDER_DIR_PC', $cf_all_glob_sys_folder_dir_pc);
	define('_GLOB_SYS_FOLDER_DIR_MOBILE', $cf_all_glob_sys_folder_dir_mobile);

//################################################################################
// html meta 변수 (오픈그래프)
//################################################################################
/*
$cff_open_graph_src = $docRoot."/config_file/cff_open_graph.php";
if( is_file($cff_open_graph_src) == true ){

	$cff_open_graph_setting = parse_ini_file($cff_open_graph_src);
	extract($cff_open_graph_setting);

	$cf_open_graph_title = filevalue_settings($cf_salt_open_graph_title);
	$cf_open_graph_subject = filevalue_settings($cf_salt_open_graph_subject);
	$cf_open_graph_description = filevalue_settings($cf_salt_open_graph_description);
	$cf_open_graph_keywords = filevalue_settings($cf_salt_open_graph_keywords);

	$cf_open_graph_og_site_name = filevalue_settings($cf_salt_open_graph_og_site_name);
	$cf_open_graph_og_type = filevalue_settings($cf_salt_open_graph_og_type);
	$cf_open_graph_og_title = filevalue_settings($cf_salt_open_graph_og_title);
	$cf_open_graph_og_description = filevalue_settings($cf_salt_open_graph_og_description);
	$cf_open_graph_og_img = filevalue_settings($cf_salt_open_graph_og_img);
	$cf_open_graph_og_url = filevalue_settings($cf_salt_open_graph_og_url);

	$cf_open_graph_tw_card = filevalue_settings($cf_salt_open_graph_tw_card);
	$cf_open_graph_tw_title = filevalue_settings($cf_salt_open_graph_tw_title);
	$cf_open_graph_tw_description = filevalue_settings($cf_salt_open_graph_tw_description);
	$cf_open_graph_tw_image = filevalue_settings($cf_salt_open_graph_tw_image);
	$cf_open_graph_tw_domain = filevalue_settings($cf_salt_open_graph_tw_domain);
			
}

	define('_OPEN_GRAPH_TITLE', $cf_open_graph_title);
	define('_OPEN_GRAPH_SUBJECT', $cf_open_graph_subject);
	define('_OPEN_GRAPH_DESCRIPTION', $cf_open_graph_description);
	define('_OPEN_GRAPH_KEYWORDS', $cf_open_graph_keywords);

	define('_OPEN_GRAPH_OG_SITE_NAME', $cf_open_graph_og_site_name);
	define('_OPEN_GRAPH_OG_TYPE', $cf_open_graph_og_type);
	define('_OPEN_GRAPH_OG_TITLE', $cf_open_graph_og_title);
	define('_OPEN_GRAPH_OG_DESCRIPTION', $cf_open_graph_og_description);
	define('_OPEN_GRAPH_OG_IMAGE', $cf_open_graph_og_img);
	define('_OPEN_GRAPH_OG_URL', $cf_open_graph_og_url);

	define('_OPEN_GRAPH_TW_CARD', $cf_open_graph_tw_card);
	define('_OPEN_GRAPH_TW_TITLE', $cf_open_graph_tw_title);
	define('_OPEN_GRAPH_TW_DESCRIPTION', $cf_open_graph_tw_description);
	define('_OPEN_GRAPH_TW_IMAGE', $cf_open_graph_tw_image);
	define('_OPEN_GRAPH_TW_DOMAIN', $cf_open_graph_tw_domain);
*/
//################################################################################
// 상수 선언
//################################################################################

include ('db_table.php'); // DB 테이블



//################################################################################
// 기본 변수
//################################################################################
$wepix_now_time  = time(); #현재시간
$wp_salt = "sjqksk"; //비밀번호 암호화 salt 값

$gva_email = array("naver.com","hanmail.net","gmail.com","yahoo.co.kr","nate.com");  // 회원가입 이메일
$gva_year = array("2017","2018","2019","2020");  //

$gva_nowtime_y  = date("Y",$wepix_now_time);
$gva_nowtime_m  = date("m",$wepix_now_time);
$gva_nowtime_d  = date("d",$wepix_now_time);
$gva_nowtime_h  = date("H",$wepix_now_time);
$gva_nowtime_i  = date("i",$wepix_now_time);
$gva_nowtime_t1  = date("Y-m-d",$wepix_now_time);
$gva_today_mktime_start  = mktime(0,0,0,$gva_nowtime_m,$gva_nowtime_d,$gva_nowtime_y);
$gva_today_mktime_end = mktime(23,59,59,$gva_nowtime_m,$gva_nowtime_d,$gva_nowtime_y);

$gv_date_code_ym  = date("ym",$check_time);
$gv_date_y_m_d  = date("Y-m-d",$check_time);

$gva_week_w = array('일','월','화','수','목','금','토');

//################################################################################
//전역 배열 변수
//################################################################################
$bva_gender['M'] = '남성';
$bva_gender['W'] = '여성';

$bva_user_state[1] = "인증대기";
$bva_user_state[2] = "인증완료-1단계";
$bva_user_state[3] = "인증완료";
$bva_user_state[21] = "탈퇴회원";

$bva_site_kind['online'] = '온라인몰';
$bva_site_kind['brand'] = '브랜드';
$bva_site_kind['openmarket'] = '오픈마켓';

//시스템 변수
$bva_system_language_ko['kor'] = "한국어";
$bva_system_language_ko['usa'] = "영어";
$bva_system_language_ko['tha'] = "태국어";
$bva_system_language_ko['vnm'] = "베트남어";

$gva_currency_simbol['USD'] = "$";
$gva_currency_simbol['THE'] = "฿";
$gva_currency_simbol['KRW'] = "￦";
$gva_currency_simbol['PHP'] = "₱";
$gva_currency_simbol['VND'] = "₫";



//################################################################################
//개별 함수,변수
//################################################################################
//개별 함수

if( is_file($docRoot."/library/"._GLOB_SYS_INDIVIDUAL_FUNCTION) == true ){
	include ($docRoot."/library/"._GLOB_SYS_INDIVIDUAL_FUNCTION);
}
//개별 변수
if( is_file($docRoot."/library/"._GLOB_SYS_INDIVIDUAL_VARIABLE) == true ){
	include ($docRoot."/library/"._GLOB_SYS_INDIVIDUAL_VARIABLE);
}





//################################################################################
// DB변수
//################################################################################
  $db_t_ADMIN_MEMBER ="ADMIN_MEMBER";
  $db_t_AGENCY ="AGENCY";
  $db_t_BOOKING_CHILD ="BOOKING_CHILD";
  $db_t_BOOKING_PARENT ="BOOKING_PARENT";
  $db_t_BOOKING_GROUP = "BOOKING_GROUP";
  $db_t_BOOKING_SETTING ="BOOKING_SETTING";
  $db_t_BOOKING_SETTING_A = "BOOKING_SETTING_A";
  $db_t_GUIDE_MEMBER ="GUIDE_MEMBER";
  $db_t_HOTEL_DB ="HOTEL_DB";
  $db_t_PRODUCT_DB ="PRODUCT_DB";
  $db_t_ROOM_TYPE_DB ="ROOM_TYPE_DB";
  $db_t_TRVAL_REPORT ="TRVAL_REPORT";
  $db_t_USER_MEMBER = "USER_MEMBER";
  $db_t_PRODUCT_CATAGORY = "PRODUCT_CATAGORY";
  $db_t_SCHEDULE = "SCHEDULE";
  $db_t_BUY_PRODUCT = "BUY_PRODUCT";
  $db_t_EXCHANGE_RATE = "EXCHANGE_RATE";
  $db_t_PAYMENT_UNPAID = "PAYMENT_UNPAID";
  $db_t_ALLIANCE_SHOP = "ALLIANCE_SHOP";
  $db_t_ALLIANCE_SHOP_CALCULATE ="ALLIANCE_SHOP_CALCULATE";
  $db_t_ALLIANCE_SHOP_SALES ="ALLIANCE_SHOP_SALES";
  $db_t_ORDER_PAY ="ORDER_PAY";
  $db_t_HOTEL_BOOKING = "HOTEL_BOOKING";
  $db_t_BOOKING_MACHING = "BOOKING_MACHING";
  $db_t_BOOKING_CALCULATE = "BOOKING_CALCULATE";
  $db_t_PRODUCT_ESTIMATE = "PRODUCT_ESTIMATE";
  $db_t_PRODUCT_REVIEW = "PRODUCT_REVIEW";
  $db_t_AREA = "AREA";
  $db_t_BUSINESS = "BUSINESS";
  $db_t_POPUP = "POPUP";
  $db_t_PRODUCT_RESERVATION = "PRODUCT_RESERVATION";
  $db_t_PAYMENT_INFO = "PAYMENT_INFO";
  $db_t_SETTING = "SETTING";
  $db_t_GOLF_DB = "GOLF_DB";
  $db_t_BOOKING_GOLF = "BOOKING_GOLF";
  $db_t_COUPON_DB = "COUPON_DB";
  $db_t_FAST_TRACK = "FAST_TRACK";
  $db_t_DEVELOPER_DB = "DEVELOPER_DB";
  $db_t_PAYMENT_DB = "PAYMENT_DB";
  $db_t_WISH_DB = "WISH_DB";
  $db_t_CART_DB = "CART_DB";
  $db_t_BOARD_FAQ = "BOARD_FAQ";
  $db_t_CUSTOMER_DB = "CUSTOMER_DB";
  $db_t_AREA_GUIDE = "AREA_GUIDE";
  $db_t_CONFIRMATION = "CONFIRMATION";
  $db_t_STATISTICS_HOTEL = "STATISTICS_HOTEL";
  $db_t_STATISTICS_BOOKING = "STATISTICS_BOOKING";
  $db_t_STATISTICS_AGENCY = "STATISTICS_AGENCY";
  $db_t_STATISTICS_GOLF = "STATISTICS_GOLF";
 
//가이드 프로필 경로
$gb_path_guide_profile ="/uploads/guide/member";


//################################################################################
// 공용함수
//################################################################################

//------------------------------------------------------------------------------------
//보안 함수
function securityVal($str){
    global $connect;

	// null 체크
	if($str === null) return "";

	//$str = htmlspecialchars($str);

	//배열일경우 배열원소마다 체크한다
    if (is_array($str)) {
		for ($i=0; $i<count($str); ++$i) {
			$str[$i] = mysqli_real_escape_string($connect, $str[$i] ?? "");
			$str[$i] = trim(strip_tags($str[$i]));
		}
		$result = $str;

	//배열이 아닐경우
    }else{
		$result = mysqli_real_escape_string($connect, $str);
		$result = trim(strip_tags(htmlspecialchars($result)));
	}

	return $result;

}

//------------------------------------------------------------------------------------
//보안 함수 게시물 body
function securityBoBodyVal($str){

	$str = addslashes($str);
/*
    global $connect;
	$result = mysqli_real_escape_string($connect,$str);
*/
	$result = $str;
	return $result;
}

//------------------------------------------------------------------------------------
//변수 크린 ??
function cleanVariable($val){
	$_val = trim(strip_tags($val));
	return $_val;
}

$wp_salt = "sjqksk"; //비밀번호 암호화 salt 값
//------------------------------------------------------------------------------------
//비밀번호 암호화 함수
function wepix_pw($val) {  
	global $wp_salt;
	$value = $val.$wp_salt;
	$row = wepix_fetch_array(wepix_query_error(" select password('".$value."') as pass "));
    return $row['pass'];
}
//------------------------------------------------------------------------------------
//패스워드 생성
function sql_password($value, $mode= "") {

	if( $mode == "A" ){
		$_return = MD5(@crypt($value, 'boom'));
	
	}elseif( $mode == "B" ){
		$_return = MD5('*' . strtoupper(hash('sha1', pack('H*', hash('sha1', $value)))));

	}else{
		$row = sql_fetch_array(sql_query_error(" select password('$value') as pass "));
		$_return = $row['pass'];
	}

    return $_return;
}

//------------------------------------------------------------------------------------
// 페이지 이동 스크립트
function move($url,$second = "0") {
    global $connect;

	if($connect) wepix_close($connect);
    echo"<meta http-equiv=\"refresh\" content=\"$second; url=$url\">";
    exit;
}

//------------------------------------------------------------------------------------
// 메세지후 페이지 이동 스크립트
function msg($str, $url= "") {
	global $connect;

    if($connect) wepix_close($connect);

    if ($url == "") { $url = "history.go(-1)";
    } elseif ($url == "close") { $url = "window.close()";
    } else { $url = "document.location.href = '$url'"; }

    if ($str != "") { echo "<script language='javascript'>alert('$str');$url;</script>"; }
    else { echo "<script language='javascript'>$url;</script>"; }
    exit;
}

//------------------------------------------------------------------------------------
// 배열 정렬
function arr_sort( $array, $key, $sort ){

	$keys = array();
	$vals = array();

	foreach( $array as $k=>$v ){
		$i = $v[$key].'.'.$k;
		$vals[$i] = $v;
		array_push($keys, $k);
	}

	unset($array);

	if( $sort=='asc' ){
		ksort($vals);
	}else{
		krsort($vals);
	}

	$ret = array_combine( $keys, $vals );

	unset($keys);
	unset($vals);

	return $ret;
}


//------------------------------------------------------------------------------------
// 공용 페이징
function publicPaging($current_page=null, $total_page=null, $list_num=null, $page_num=null, $url=null){

	// 기본값 설정
	if($current_page === null) $current_page = "1";
	if($total_page === null) $total_page = "0";
	if($list_num === null) $list_num = "15";
	if($page_num === null) $page_num = "10";

	// 변수 초기화
	$link_str = "";
	$link_str .= "<div class='paging'>";

	if( $total_page < 2 ){

		$link_str .= "<a class='side'><i class='fas fa-angle-double-left'></i></a>";
		$link_str .= "<a class='side'><i class='fas fa-angle-left'></i></a>";
		$link_str .= "<a class='now'>1</a>";
		$link_str .= "<a class='side'><i class='fas fa-angle-right'></i></a>";
		$link_str .= "<a class='side'><i class='fas fa-angle-double-right'></i></a>";

	}else{

		$start_page = @(((int)(($current_page-1)/$page_num))*$page_num)+1;
		$temp_pnum = $page_num - 1 ;
		$end_page = $start_page + $temp_pnum;
		if ($end_page >= $total_page) $end_page = $total_page;
        

		if ($current_page > 1) {
			$link_str .= "<a href='".$url."' class='side-on'><i class='fas fa-angle-double-left'></i></a>";
			$link_str .= "<a href='".$url.($current_page-1)."' class='side-on'><i class='fas fa-angle-left'></i></a>";
		}else{
			$link_str .= "<a class='side'><i class='fas fa-angle-double-left'></i></a>";
			$link_str .= "<a class='side'><i class='fas fa-angle-left'></i></a>";
		}

		for ($i=$start_page; $i<=$end_page; $i++) {
			if ($current_page != $i) {
                $link_str .= "<a href='".$url.$i."' class='rest'>".$i."</a>";
            } else {
                $link_str .= "<a class='now'>".$i."</a>";
            }
        }

		if ($current_page < $total_page) {
			$link_str .= "<a href='".$url.($current_page+1)."' class='side-on'><i class='fas fa-angle-right'></i></a>";
			$link_str .= "<a href='".$url.$total_page."' class='side-on'><i class='fas fa-angle-double-right'></i></a>";
		}else{
			$link_str .= "<a class='side'><i class='fas fa-angle-right'></i></a>";
			$link_str .= "<a class='side'><i class='fas fa-angle-double-right'></i></a>";
		}

	}

	$link_str .= "</div>";

    return $link_str;
}

//------------------------------------------------------------------------------------------------------------------
// 공용 AJAX 페이징
function publicAjaxPaging($current_page=null, $total_page=null, $list_num=null, $page_num=null, $function_name=null, $function_value=null){

	// 기본값 설정
	if($current_page === null) $current_page = "1";
	if($total_page === null) $total_page = "0";
	if($list_num === null) $list_num = "15";
	if($page_num === null) $page_num = "10";

	// 변수 초기화
	$link_str = "";
	$link_str .= "<div class='paging'>";

	if( $total_page < 1 ){

		$link_str .= "<a class='side'><i class='fas fa-angle-double-left'></i></a>";
		$link_str .= "<a class='side'><i class='fas fa-angle-left'></i></a>";
		$link_str .= "<a class='now'>1</a>";
		$link_str .= "<a class='side'><i class='fas fa-angle-right'></i></a>";
		$link_str .= "<a class='side'><i class='fas fa-angle-double-right'></i></a>";

	}else{

		$start_page = @(((int)(($current_page-1)/$page_num))*$page_num)+1;
		$temp_pnum = $page_num - 1 ;
		$end_page = $start_page + $temp_pnum;
		if ($end_page >= $total_page) $end_page = $total_page;
        

		if ($current_page > 1) {
			$link_str .= "<a class='side-on' href=\"javascript:".$function_name."('1'".$function_value.")\"><i class='fas fa-angle-double-left'></i></a>";
			$link_str .= "<a class='side-on' href=\"javascript:".$function_name."('".($current_page-1)."'".$function_value.")\"><i class='fas fa-angle-left'></i></a>";
		}else{
			$link_str .= "<a class='side'><i class='fas fa-angle-double-left'></i></a>";
			$link_str .= "<a class='side'><i class='fas fa-angle-left'></i></a>";
		}

		for ($i=$start_page; $i<=$end_page; $i++) {
			if ($current_page != $i) {
                $link_str .= "<a class='rest' href=\"javascript:".$function_name."('".$i."'".$function_value.")\">".$i."</a>";
            } else {
                $link_str .= "<a class='now'>".$i."</a>";
            }
        }

		if ($current_page < $total_page) {
			$link_str .= "<a class='side-on' href=\"javascript:".$function_name."('".($current_page+1)."'".$function_value.")\"><i class='fas fa-angle-right'></i></a>";
			$link_str .= "<a class='side-on' href=\"javascript:".$function_name."('".$total_page."'".$function_value.")\"><i class='fas fa-angle-double-right'></i></a>";
		}else{
			$link_str .= "<a class='side'><i class='fas fa-angle-right'></i></a>";
			$link_str .= "<a class='side'><i class='fas fa-angle-double-right'></i></a>";
		}

		
	}

	$link_str .= "</div>";

    return $link_str;
}

//------------------------------------------------------------------------------------
//상품 카테고리 ID 커팅
function category_status($str){

	$str_status = $str;
	$str_len = strlen($str_status);
	for($i=$str_len; $i>1; $i=$i-2){
		$j = $i-2;
		$temp_status = substr($str_status,$j,2);
		if($temp_status != "00"){ break; }
		$str_status = substr($str_status,0,$j);
	}
	return $str_status;
}

//------------------------------------------------------------------------------------
//상품 카테고리 level
function category_level($str){
	$level_status = 0;
	$str_len = strlen($str);
	for($i=0; $i<$str_len; $i=$i+2){
		$temp_status = substr($str,$i,2);
		if($temp_status == "00"){ break; }
		$str_level++;
	}
	return $str_level;
}

//------------------------------------------------------------------------------------
//상품 depth 코드 4차 한정
function category_depth_code($id, $depth){

	$cateLevel = category_level($id);

	if( $depth == '0' ){
		$value = $id;
	}elseif( $depth == '1' ){
		$navi = substr($id,0,2);
		$value = $navi."000000";
	}elseif( $depth == '2' ){
		$navi = substr($id,0,4);
		$value = $navi."0000";
	}elseif( $depth == '3' ){
		$navi = substr($id,0,6);
		$value = $navi."00";
	}else if( $depth == '4' ){
		$value = $id;
	}

	if( $cateLevel < $depth ) $value = "none";

	return $value;
}




//------------------------------------------------------------------------------------
function review_ran()
{
    $len = 3;
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrsyuvwxyz";

    srand((double)microtime()*1000000);

    $i = 0;
    $str = '';

    while ($i < $len) {
        $num = rand() % strlen($chars);
        $tmp = substr($chars, $num, 1);
        $str .= $tmp;
        $i++;
    }
    return $str;
}

//------------------------------------------------------------------------------------
function rmdirAll($dir) {
   $dirs = dir($dir);
   while(false !== ($entry = $dirs->read())) {
      if(($entry != '.') && ($entry != '..')) {
         if(is_dir($dir.'/'.$entry)) {
            rmdirAll($dir.'/'.$entry);
         } else {
            @unlink($dir.'/'.$entry);
         }
       }
    }
    $dirs->close();
    @rmdir($dir);
}

//------------------------------------------------------------------------------------
//해당월의 총날짜수를 구한다.
function getTotaldays2($y, $m) {
	$d = 1;
	while(checkdate($m, $d, $y)) {
		$d++;
	}
	$d = $d - 1;
	return $d;
}

//------------------------------------------------------------------------------------
//2차원 배열 SORT
//wepixArraySort($array, "name");
function wepixArraySort($arr, $dimension, $mode="sort") {
	
	if($dimension){
		for($i = 0; $i < sizeof($arr); $i++) {
			array_unshift($arr[$i], $arr[$i][$dimension]);
		}
		if( $mode == "rsort" ){
			@rsort($arr);
		}else{
			@sort($arr);
		}
		for($i = 0; $i < sizeof($arr); $i++) {
			array_shift($arr[$i]);
		}
	} else {
		if( $mode == "rsort" ){
			@rsort($arr);
		}else{
			@sort($arr);
		}
	}
 
	return $arr;
 }

//------------------------------------------------------------------------------------
// IP 노출변형
function get_ip_shield($str){

	$_ary_board_ip_show = explode(".",$str);
	$_board_ip_show = $_ary_board_ip_show[0].".".$_ary_board_ip_show[1];

	return $_board_ip_show;
}

//------------------------------------------------------------------------------------
// ipv6 체크 함수
function is_ipv6($ip) {
    //$ip = getRealIpAddr();
    if (!preg_match("/^([0-9a-f\.\/:]+)$/",strtolower($ip))) {
        return false;
    }
    if (substr_count($ip,":") < 2) {
        return false;
    }
    $part = preg_split("/[:\/]/", $ip);
    foreach ($part as $i) {
        if (strlen($i) > 4) {
            return false;
        }
    }
    return true;
}

//------------------------------------------------------------------------------------
// 도메인 추출
function getDomain($url)	{
	$value = strtolower(trim($url));
	$url_patten = '/^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?$/i';
	$domain_patten = '/([a-z\d\-]+(?:\.(?:asia|info|name|mobi|com|net|org|biz|tel|xxx|kr|co|so|me|eu|cc|or|pe|ne|re|tv|jp|tw)){1,2})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?$/i';
	if (preg_match($url_patten, $value))
	{
		preg_match($domain_patten, $value, $matches);
		$host = (!$matches[1]) ? $value : $matches[1];
	}
	return $host;
}

//------------------------------------------------------------------------------------
// ip자르기
function ipShortCut($ip) {
	if( is_ipv6($ip)==true ){
		$_ary_board_ip_show = explode(":",$ip);
		$_board_ip_show = $_ary_board_ip_show[2].":".$_ary_board_ip_show[3];
	}else{
		$_ary_board_ip_show = explode(".",$ip);
		$_board_ip_show = $_ary_board_ip_show[0].".".$_ary_board_ip_show[1];
	}
    return $_board_ip_show;
}


//------------------------------------------------------------------------------------
// 게시물 내용을 TEXT 형식으로 변환
function get_body_text($str){

	$str = strip_tags($str);
	$str = str_replace("\"", "'", $str);
	$str = str_replace("\n", " ", $str);
	$str = str_replace("\r", "", $str);
	$str = str_replace('"', '', $str);
	return $str;
}

//------------------------------------------------------------------------------------
// TEXT 형식으로 변환
function get_text($str, $html=0, $restore=false)
{
    $source[] = "<";
    $target[] = "&lt;";
    $source[] = ">";
    $target[] = "&gt;";
    $source[] = "\"";
    $target[] = "&#034;";
    $source[] = "\'";
    $target[] = "&#039;";

    if($restore)
        $str = str_replace($target, $source, $str);

    // 3.31
    // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
    if ($html == 0) {
        $str = html_symbol($str);
    }

    if ($html) {
        $source[] = "\n";
        $target[] = "<br/>";
    }

    return str_replace($source, $target, $str);
}

//------------------------------------------------------------------------------------
// HTML SYMBOL 변환
// &nbsp; &amp; &middot; 등을 정상으로 출력
function html_symbol($str)
{
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}

//------------------------------------------------------------------------------------
// 토큰생성
function make_token($num,$str){

	if(!$num) $num = 8; 
	$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
	shuffle($chars_array);
	$shuffle = implode('', $chars_array);

	$result = substr($shuffle,0,$num)."_".time()."_".$str;

	return $result;
}

//------------------------------------------------------------------------------------------------------------------
//이미지 체크함수
function file_image_check($file) {

	$_tmp_file = $file['tmp_name'];
	$_timg = @getimagesize($_tmp_file);

	//이미지인지 체크
	if(in_array($_timg[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))){
		return "true";
	}else{
		return "false";
	}
}

//------------------------------------------------------------------------------------------------------------------
//파일이름 변경
function file_change_name($file, $name) {

	$_ary_ext = explode('.', $file['name']); //확장자 분리
	$_ary_ext_index = count($_ary_ext) - 1; //파일명에 ( . ) 들어갔을경우 씨부랄
	$_ext = $_ary_ext[$_ary_ext_index];
	$_name = $name.".".$_ext;

    return $_name;
}

//################################################################################
// DB 쓰는함수
//################################################################################

function getBrowserInfo() {
  $userAgent = $_SERVER["HTTP_USER_AGENT"]; 
  if(preg_match('/MSIE/i',$userAgent) && !preg_match('/Opera/i',$u_agent)){
    $browser = 'Internet Explorer';
  }else if(preg_match('/Firefox/i',$userAgent)){
    $browser = 'Mozilla Firefox';
  }else if (preg_match('/Chrome/i',$userAgent)){
    $browser = 'Google Chrome';
  }else if(preg_match('/Safari/i',$userAgent)){
    $browser = 'Apple Safari';
  }elseif(preg_match('/Opera/i',$userAgent)){
    $browser = 'Opera';
  }elseif(preg_match('/Netscape/i',$userAgent)){
    $browser = 'Netscape';
  }else{
    $browser = "Other";
  }

  return $browser;
}

function getOsInfo(){
  $userAgent = $_SERVER["HTTP_USER_AGENT"]; 

    if(preg_match('/linux/i', $userAgent)){ 
		$os = 'linux';
    }elseif(preg_match('/macintosh|mac os x/i', $userAgent)){
	    $os = 'mac';
    }elseif (preg_match('/windows|win32/i', $userAgent)){
		$os = 'windows';
	}else {
	    $os = 'Other';
	}
	 return $os;
}

function rtn_mobile_chk() {
    // 모바일 기종(배열 순서 중요, 대소문자 구분 안함)
    $ary_m = array("iPhone","iPod","IPad","Android","Blackberry","SymbianOS|SCH-M\d+","Opera Mini","Windows CE","Nokia","Sony","Samsung","LGTelecom","SKT","Mobile","Phone");
    for($i=0; $i<count($ary_m); $i++){
        if(preg_match("/$ary_m[$i]/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return $ary_m[$i];
            break;
        }
    }
}

//------------------------------------------------------------------------------------------------------------------
// 프론트 검색어 저장 함수
function frontSearchSave($text, $result_count){
	global $connect, $check_ip, $check_domain, $check_time;

	$st_date = date("Y-m-d", $check_time);
	$st_time = date("h:i:s", $check_time);
	$st_device = rtn_mobile_chk();

	if(!$st_device){
		$_st_device = "PC";
	}else{
		$_st_device = "Mobile";
	}

	$query = "INSERT INTO  serch_text SET
		st_domain ='".$check_domain."',
		st_word ='".$text."',
		st_date ='".$st_date."',
		st_time ='".$st_time."',
		st_ip = '".$check_ip."',
		st_device = '".$_st_device."',
		st_result = '".$result_count."' ";
	wepix_query_error($query);

}

//------------------------------------------------------------------------------------------------------------------
// 방문자 기록함수
function userVisit(){
	global $connect, $check_ip, $check_domain, $check_time, $check_mobile;

	$vi_date = date("Y-m-d", $check_time);
	$vi_time = date("h:i:s", $check_time);
	$vi_agent = $_SERVER['HTTP_USER_AGENT'];
	$vi_referer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH); 
	$vi_brower = getBrowserInfo();
	$vi_os = getOsInfo();
	$vi_device = rtn_mobile_chk();

	$today_visit_sum = wepix_fetch_array(wepix_query_error("SELECT * FROM visit_sum WHERE vs_date = '".$vi_date."' AND vs_domain = '".$check_domain."' "));

	if( !$today_visit_sum['vs_idx'] ){
		wepix_query_error("INSERT INTO visit_sum SET vs_date ='".$vi_date."', vs_domain = '".$check_domain."' ");
		$today_visit_sum = wepix_fetch_array(wepix_query_error("SELECT * FROM visit_sum WHERE vs_date = '".$vi_date."' AND vs_domain = '".$check_domain."' "));
	}

	$vs_count = $today_visit_sum['vs_count'];
	$vs_pc = $today_visit_sum['vs_pc']; 
	$vs_mobile = $today_visit_sum['vs_mobile']; 

	$today_visit = wepix_fetch_array(wepix_query_error("SELECT vi_id FROM visit WHERE vi_domain = '".$check_domain."' AND vi_date = '".$vi_date."' AND vi_ip = '".$check_ip."' "));

	if( !$today_visit['vi_id'] ){
		$visit_query = "INSERT INTO visit SET
			vi_domain = '".$check_domain."',
			vi_ip = '".$check_ip."',
			vi_date ='".$vi_date."',
			vi_time ='".$vi_time."',
			vi_agent = '".$vi_agent."',
			vi_referer = '".$vi_referer."',
			vi_brower ='".$vi_brower."',
			vi_os = '".$vi_os."', 
			vi_device = '".$vi_device."'";
		wepix_query_error($visit_query);

		$vs_count = $vs_count + 1;
/*
		if($vs_device == ''){
			$vs_pc =  $vs_pc + 1;
		}else{
			$vs_mobile =  $vs_mobile + 1;
		}
*/
		if( $check_mobile == 'Y' ){
			$vs_mobile =  $vs_mobile + 1;
		}else{
			$vs_pc =  $vs_pc + 1;
		}

		$visit_sum_query = "UPDATE visit_sum SET
			vs_count = ".$vs_count.",
			vs_pc = ".$vs_pc.",
			vs_mobile = ".$vs_mobile."
			WHERE vs_idx ='".$today_visit_sum['vs_idx']."' ";
		wepix_query_error($visit_sum_query);

	}
}

//------------------------------------------------------------------------------------------------------------------
// 게시판 이미지 파일 DB 저장 함수
function fileReg( $dir, $name, $b_code, $b_token, $width, $height, $size, $kind, $mode ){ 
	global $connect, $_db_file, $check_time;
	$query = "insert into ".$_db_file." set
		FILE_KIND = '".$kind."',
		FILE_MODE = '".$mode."',
		FILE_B_CODE ='".$b_code."',
		FILE_B_TOKEN = '".$b_token."',
		FILE_DIR = '".$dir."',
		FILE_NAME = '".$name."',
		FILE_WIDTH = '".$width."',
		FILE_HEIGHT = '".$height."',
		FILE_SIZE = '".$size."',
		FILE_DATE = '".$check_time."' ";
	wepix_query_error($query);
}

//------------------------------------------------------------------------------------------------------------------
// 이미지업로드 함수
function imageUpload( $file, $mode, $dir, $addname, $existing_file, $file_b_code, $file_b_token, $thum_w, $thum_h, $not_del ){
	global $connect, $_db_file, $check_time;

	//기존파일 있을경우 삭제한다
	//if( $mode == "modify" && $existing_file ){
	if( $existing_file && $not_del != "not_del" ){
		$is_file_exist = file_exists($dir.$existing_file);
		//파일이 있을경우 파일삭제
		if ($is_file_exist) {
			wepix_query_error("delete from ".$_db_file." where FILE_B_CODE ='".$file_b_code."' and FILE_B_TOKEN = '".$file_b_token."' "); //파일 DB삭제
			@unlink($dir.$existing_file); // 사진파일 삭제
		}
	}

	$thumbnail_tmp_file = $file['tmp_name'];
	$thumbnail_timg = @getimagesize($thumbnail_tmp_file);
	$thumbnail_size = filesize($thumbnail_tmp_file);
	$_ary_thumbnail_ext = explode('.', $file['name']); //확장자 분리
	$_thumbnail_ext_index = count($_ary_thumbnail_ext) - 1; //파일명에 ( . ) 들어갔을경우

	//이미지인지 체크
	if(in_array($thumbnail_timg[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))){
	}else{
		msg("이미지만 등록 가능합니다.","");
		exit;
	}

	if($addname){
		$_img_name = $file_b_token."_".$addname.".".$_ary_thumbnail_ext[1];
	}else{
		$_img_name = $file_b_token.".".$_ary_thumbnail_ext[1];
	}

	$thumbnail_destination = $dir."/".$_img_name;
	move_uploaded_file($thumbnail_tmp_file, $thumbnail_destination);

	if($thum_w && $thum_h){
		//썸네일 리사이징
		if( $thumbnail_timg[0] > $thum_w or $thumbnail_timg[1] > $thum_h ){
			$image = new SimpleImage();
			$image->load($thumbnail_destination);
			$image->resize($thum_w, $thum_h);
			$image->save($thumbnail_destination);
		}
	}
	
	//썸네일이 저장되었으면 DB에 저장한다
	fileReg( $dir, $_img_name,$file_b_code, $file_b_token, $thumbnail_timg[0], $thumbnail_timg[1], $thumbnail_size, "", "img" );

	return $_img_name;
}

//------------------------------------------------------------------------------------------------------------------
// 이미지업로드 함수2
function imgUpload2($file_name, $file_tmp_name, $dir, $name, $thum_w, $thum_h, $option=""){

	$_imgfile = $file_name;
	$_tmpfile = $file_tmp_name;
	$_tmpinfo = @getimagesize($_tmpfile);

	//확장자
	$extension = pathinfo($_imgfile, PATHINFO_EXTENSION);

	$_save_filename = $name.".".$extension;
	$_destination = $dir."/".$_save_filename;
	move_uploaded_file($_tmpfile, $_destination);

	//썸네일 리사이징
	if( $thum_w && $thum_h == "resizeToWidth" ){
		if( $_tmpinfo[0] > $thum_w ){
			$image = new SimpleImage();
			$image->load($_destination);
			$image->resizeToWidth($thum_w);
			$image->save($_destination);
		}
	}elseif( $thum_w && $thum_h == "square" ){
		if( $_tmpinfo[0] > $thum_w ){
			$image = new SimpleImage();
			$image->load($_destination);
			$image->square($thum_w);
			$image->save($_destination);
		}
	}elseif( $thum_w && $thum_h && $thum_h != "resizeToWidth" && $thum_h != "square" ){
		if( $_tmpinfo[0] > $thum_w || $_tmpinfo[1] > $thum_h ){
			$image = new SimpleImage();
			$image->load($_destination);
			$image->resize($thum_w, $thum_h);
			$image->save($_destination);
		}
	}

	return $_save_filename;

}

//------------------------------------------------------------------------------------------------------------------
// 이미지업로드 함수
function imgUpload($file, $dir, $name, $thum_w, $thum_h){

	$_imgfile = $file['name'];
	$_tmpfile = $file['tmp_name'];
	$_tmpinfo = @getimagesize($_tmpfile);

	//확장자
	$extension = pathinfo($_imgfile, PATHINFO_EXTENSION);

	$_save_filename = $name.".".$extension;
	$_destination = $dir."/".$_save_filename;
	move_uploaded_file($_tmpfile, $_destination);

	//썸네일 리사이징
	if( $thum_w && $thum_h == "resizeToWidth" ){
		if( $_tmpinfo[0] > $thum_w ){
			$image = new SimpleImage();
			$image->load($_destination);
			$image->resizeToWidth($thum_w);
			$image->save($_destination);
		}
	}elseif( $thum_w && $thum_h && $thum_h != "resizeToWidth" ){
		if( $_tmpinfo[0] > $thum_w || $_tmpinfo[1] > $thum_h ){
			$image = new SimpleImage();
			$image->load($_destination);
			$image->resize($thum_w, $thum_h);
			$image->save($_destination);
		}
	}

	return $_save_filename;

}

//------------------------------------------------------------------------------------------------------------------
// 아웃 이미지업로드 함수
function outImgUpload($out_img, $dir, $name, $thum_w, $thum_h){

	// 확장자
	//$extension = strtolower(pathinfo($out_img, PATHINFO_EXTENSION));
	$extension = pathinfo($out_img, PATHINFO_EXTENSION);

	$_save_filename = $name.".".$extension;
	$_destination = $dir."/".$_save_filename;

	$fp = fopen($_destination, 'w'); // 저장할 이미지 위치 및 파일명

	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $out_img );
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	$contents = curl_exec($ch);
	curl_close($ch);

	fwrite($fp,$contents); // 가져올 외부이미지 주소
	fclose($fp);

	if($thum_w && $thum_h){
		//썸네일 리사이징
		if( $_tmpinfo[0] > $thum_w || $_tmpinfo[1] > $thum_h ){
			$image = new SimpleImage();
			$image->load($_destination);
			$image->resize($thum_w, $thum_h);
			$image->save($_destination);
		}
	}

	return $_save_filename;

}


//------------------------------------------------------------------------------------------------------------------
// 로그굽기 함수
function insertLog( $id, $mode, $state, $not="" ){ 
    global $connect, $_db_log , $check_ip, $check_domain, $check_time, $gv_date_y_m_d;
	$query = "insert into ".$_db_log." set
		LL_ID = '".$id."',
		LL_MODE = '".$mode."',
		LL_IP = '".$check_ip."',
		LL_DOMAIN = '".$check_domain."',
		LL_DATE = '".$check_time."',
		LL_DATE_CODE = '".$gv_date_y_m_d."',
		LL_STATE = '".$state."',
		LL_NOT = '".$not."' ";
	wepix_query_error($query);
}


//------------------------------------------------------------------------------------------------------------------
// 좋아요 함수
function contensLike( $mode, $idx, $selected, $id, $group ,$ip ){ 

    global $connect, $_db_like, $_db_comparison_comm;
/*	
	if( !$id ){
		$result = "|Processing_Failure|로그인 서비스";
		return $result;

	}
*/
	//가격비교 코멘트
	if( $mode == "comparison_comm" ){

		$_table = $_db_comparison_comm;
		$_culom_idx = "COMMENT_IDX";
		$_culom_good = "LIKE_GOOD";
		$_culom_bad = "LIKE_BAD";

		$_where = " where LIKE_IDX = '".$idx."' and LIKE_MODE = '".$mode."' and LIKE_ID = '".$id."' ";

	//게시판
	}elseif( $mode == "board" ){

		$_table = "BOARD_".$group;
		$_culom_idx = "UID";
		$_culom_good = "BOARD_LIKE";
		$_culom_bad = "BOARD_BAD";

		if( $id ) {
			$_where = " where LIKE_GROUP = '".$group."' and LIKE_IDX = '".$idx."' and LIKE_MODE = '".$mode."' and LIKE_ID = '".$id."' ";
		}else{
			$_where = " where LIKE_GROUP = '".$group."' and LIKE_IDX = '".$idx."' and LIKE_MODE = '".$mode."' and LIKE_IP = '".$ip."' ";
		}
	}

	$like_data = wepix_fetch_array(wepix_query_error("select LIKE_KEY, LIKE_SELECTED from ".$_db_like." ".$_where." "));

	if( $like_data['LIKE_KEY'] ){

		//삭제일 경우
		if( $selected == $like_data['LIKE_SELECTED'] ){
			wepix_query_error(" delete from ".$_db_like." where LIKE_KEY = '".$like_data['LIKE_KEY']."' ");

			if( $selected == "good"){
				wepix_query_error("update ".$_table." set ".$_culom_good." = ".$_culom_good." - 1 where ".$_culom_idx." = '".$idx."' " );
			}else{
				wepix_query_error("update ".$_table." set ".$_culom_bad." = ".$_culom_bad." - 1 where ".$_culom_idx." = '".$idx."' " );
			}

			$result = "DEL";
			return $result;
		//수정일 경우
		}else{
			wepix_query_error(" update ".$_db_like." set LIKE_SELECTED = '".$selected."' where LIKE_KEY = '".$like_data['LIKE_KEY']."' ");

			if( $selected == "good"){
				wepix_query_error("update ".$_table." set ".$_culom_good." = ".$_culom_good." + 1, ".$_culom_bad." = ".$_culom_bad." - 1 where ".$_culom_idx." = '".$idx."' " );
			}else{
				wepix_query_error("update ".$_table." set ".$_culom_good." = ".$_culom_good." - 1, ".$_culom_bad." = ".$_culom_bad." + 1 where ".$_culom_idx." = '".$idx."' " );
			}

			$result = "MODIFY";
			return $result;
		}

	}else{
		//신규일 경우
		$query = "insert ".$_db_like." set
					LIKE_GROUP = '".$group."',
					LIKE_IDX = '".$idx."',
					LIKE_ID = '".$id."',
					LIKE_SELECTED = '".$selected."',
					LIKE_MODE = '".$mode."',
					LIKE_DATE = '".time()."',
					LIKE_IP = '".$ip."' ";
		wepix_query_error($query);

		if( $selected == "good"){
			wepix_query_error("update ".$_table." set ".$_culom_good." = ".$_culom_good." + 1 where ".$_culom_idx." = '".$idx."' " );
		}else{
			wepix_query_error("update ".$_table." set ".$_culom_bad." = ".$_culom_bad." + 1 where ".$_culom_idx." = '".$idx."' " );
		}

		$result = "NEW";
		return $result;
	}

}


//################################################################################
// 뉴 어드민 들어가면 삭제 해야할 함수 
//################################################################################
//상품 카테고리 레벨 별 id 구하기
function category_level_name($str,$level){
	global $connect,$db_t_PRODUCT_CATAGORY;

	 if($level == '0'){
		  $navi = substr($str,0,2);
		  $value = $navi."000000";
	}else if($level == '1'){
		  $navi = substr($str,0,4);
		  $value = $navi."0000";
	}else if($level == '2'){
		  $navi = substr($str,0,6);
		  $value = $navi."00";
	}else if($level == '3'){
          $navi = substr($str,0,8);
		  $value = $navi."";
	 } 

   	      $query = "select PDC_ID from ".$db_t_PRODUCT_CATAGORY." where PDC_ID = '".$value."' ";
		  $result = wepix_fetch_array(wepix_query_error($query));
		  return $result['PDC_ID'];
}
//################################################################################
// 텔레그램 함수
//################################################################################
define('BOT_TOKEN', '784830158:AAHvP0HaSPwdoxZ-lAyCE5NgKVrHjqqS5tc');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
 
function telegramExecCurlRequest($handle) {
    $response = curl_exec($handle);
 
    if ($response === false) {
        $errno = curl_errno($handle);
        $error = curl_error($handle);
        error_log("Curl returned error $errno: $error\n");
        curl_close($handle);
        return false;
    }
 
    $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
    curl_close($handle);
 
    if ($http_code >= 500) {
        // do not wat to DDOS server if something goes wrong
        sleep(10);
        return false;
    } else if ($http_code != 200) {
        $response = json_decode($response, true);
        error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
        if ($http_code == 401) {
            throw new Exception('Invalid access token provided');
        }
        return false;
    } else {
        $response = json_decode($response, true);
        if (isset($response['description'])) {
            error_log("Request was successfull: {$response['description']}\n");
        }
        $response = $response['result'];
    }
    return $response;
}
 
function telegramApiRequest($method, $parameters) {
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }
 
    if (!$parameters) {
        $parameters = array();
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }
 
    foreach ($parameters as $key => &$val) {
        // encoding to JSON array parameters, for example reply_markup
        if (!is_numeric($val) && !is_string($val)) {
            $val = json_encode($val);
        }
    }
    $url = API_URL.$method.'?'.http_build_query($parameters);
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
 
    return telegramExecCurlRequest($handle);
}

function telegramSendMessage($chat_id,$message_cont){
    $_TELEGRAM_CHAT_ID = array($chat_id);
    foreach($_TELEGRAM_CHAT_ID AS $_TELEGRAM_CHAT_ID_STR) { 
		$_TELEGRAM_QUERY_STR    = array(
			'chat_id' => $_TELEGRAM_CHAT_ID_STR,
			'text'    => $message_cont,
		);
        telegramApiRequest("sendMessage",$_TELEGRAM_QUERY_STR);
    }
   
}
 
?>