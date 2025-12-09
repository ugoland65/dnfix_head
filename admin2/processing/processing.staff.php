<?
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 변수 초기화
$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";
$response = array('success' => false, 'msg' => '잘못된 요청입니다.');

////////////////////////////////////////////////////////////////////////////////////////////////
// 직원 등록
if( $_a_mode == "ad_reg" ){

	$_ad_id = $_POST['ad_id'] ?? "";
	$_ad_name = $_POST['ad_name'] ?? "";
	$_ad_nick = $_POST['ad_nick'] ?? "";
	$_ad_pw = $_POST['ad_pw'] ?? "";
	$_ad_birth = $_POST['ad_birth'] ?? "";
	$_ad_joining = $_POST['ad_joining'] ?? "";
	$_ad_address = $_POST['ad_address'] ?? "";
	$_ad_tel = $_POST['ad_tel'] ?? "";
	$_ad_contact_name = $_POST['ad_contact_name'] ?? "";
	$_ad_contact_relationship = $_POST['ad_contact_relationship'] ?? "";
	$_ad_contact_tel = $_POST['ad_contact_tel'] ?? "";
	
	// 날짜 필드 처리 (빈 값일 경우 0000-00-00)
	if (empty($_ad_birth)) $_ad_birth = '0000-00-00';
	if (empty($_ad_joining)) $_ad_joining = '0000-00-00';

	$_ad_pw = wepix_pw($_ad_pw);
	
	// SQL Injection 방지를 위한 escape 처리
	$_ad_id = mysqli_real_escape_string($connect, $_ad_id);
	$_ad_name = mysqli_real_escape_string($connect, $_ad_name);
	$_ad_nick = mysqli_real_escape_string($connect, $_ad_nick);
	$_ad_pw = mysqli_real_escape_string($connect, $_ad_pw);
	$_ad_birth = mysqli_real_escape_string($connect, $_ad_birth);
	$_ad_joining = mysqli_real_escape_string($connect, $_ad_joining);

	$query = "insert into "._DB_ADMIN." set
		AD_ID = '".$_ad_id."',
		AD_NAME = '".$_ad_name."',
		AD_NAME_EG = '',
		AD_NICK = '".$_ad_nick."',
		AD_PW = '".$_ad_pw."',
		AD_LEVEL = '0',
		AD_LEVEL2 = '',
		AD_MEMO = '',
		AD_BIRTH = '',
		AD_PHONE = '',
		AD_KAKAO = '',
		AD_LINE = '',
		AD_MAIL = '',
		AD_UP_DATE = '0',
		AD_LANG = 'kor',
		AD_REG_DATE = '".$wepix_now_time."' ";
	sql_query_error($query);

	$_ad_data_arr = array(
		"address" => $_ad_address,
		"tel" => $_ad_tel,
		"contact" => array(
			"name" => $_ad_contact_name,
			"relationship" => $_ad_contact_relationship,
			"tel" => $_ad_contact_tel
		)
	);

	$_ad_data = json_encode($_ad_data_arr, JSON_UNESCAPED_UNICODE);
	
	// 날짜 필드 재확인 (escape 처리 직전)
	if (empty($_ad_birth) || $_ad_birth == '') $_ad_birth = '0000-00-00';
	if (empty($_ad_joining) || $_ad_joining == '') $_ad_joining = '0000-00-00';
	
	// JSON 데이터도 escape 처리
	$_ad_data = mysqli_real_escape_string($connect, $_ad_data);
	$_ad_birth = mysqli_real_escape_string($connect, $_ad_birth);
	$_ad_joining = mysqli_real_escape_string($connect, $_ad_joining);

	$query = "insert admin SET 
		ad_id = '".$_ad_id."',
		ad_pw = '".$_ad_pw."',
		ad_nick = '".$_ad_nick."',
		ad_name = '".$_ad_name."',
		ad_name_en = '',
		ad_birth = '".$_ad_birth."',
		ad_joining = '".$_ad_joining."',
		ad_data = '".$_ad_data."',
		ad_image = '',
		ad_telegram_token = '',
		ad_line_token = '',
		ad_level = '0',
		AD_REG_DATE = '".$wepix_now_time."',
		AD_UP_DATE = '0' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );


////////////////////////////////////////////////////////////////////////////////////////////////
// 직원 수정
}elseif( $_a_mode == "ad_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_ad_nick = $_POST['ad_nick'] ?? "";
	$_ad_name = $_POST['ad_name'] ?? "";
	$_ad_birth = $_POST['ad_birth'] ?? "";
	$_ad_joining = $_POST['ad_joining'] ?? "";
	$_ad_address = $_POST['ad_address'] ?? "";
	$_ad_tel = $_POST['ad_tel'] ?? "";
	$_ad_contact_name = $_POST['ad_contact_name'] ?? "";
	$_ad_contact_relationship = $_POST['ad_contact_relationship'] ?? "";
	$_ad_contact_tel = $_POST['ad_contact_tel'] ?? "";
	$_ad_telegram_token = $_POST['ad_telegram_token'] ?? "";
	$_ad_line_token = $_POST['ad_line_token'] ?? "";
	$_new_pw_change = $_POST['new_pw_change'] ?? "";
	$_new_ad_pw = $_POST['new_ad_pw'] ?? "";
	
	// 날짜 필드 처리 (빈 값일 경우 0000-00-00)
	if (empty($_ad_birth)) $_ad_birth = '0000-00-00';
	if (empty($_ad_joining)) $_ad_joining = '0000-00-00';
	
	// SQL Injection 방지를 위한 escape 처리
	$_idx = mysqli_real_escape_string($connect, $_idx);

	$data = wepix_fetch_array(wepix_query_error("select * from admin WHERE idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['ad_pw' => ''];
	}

	$_ad_pw = $data['ad_pw'] ?? "";

	if( $_new_pw_change == "ok" ){
		$_ad_pw = wepix_pw($_new_ad_pw);
	}

	$_ad_data_arr = array(
		"address" => $_ad_address,
		"tel" => $_ad_tel,
		"contact" => array(
			"name" => $_ad_contact_name,
			"relationship" => $_ad_contact_relationship,
			"tel" => $_ad_contact_tel
		)
	);

	$_ad_data = json_encode($_ad_data_arr, JSON_UNESCAPED_UNICODE);
	
	// 날짜 필드 재확인 (escape 처리 직전)
	if (empty($_ad_birth) || $_ad_birth == '') $_ad_birth = '0000-00-00';
	if (empty($_ad_joining) || $_ad_joining == '') $_ad_joining = '0000-00-00';
	
	// SQL Injection 방지를 위한 escape 처리
	$_ad_data = mysqli_real_escape_string($connect, $_ad_data);
	$_ad_pw = mysqli_real_escape_string($connect, $_ad_pw);
	$_ad_nick = mysqli_real_escape_string($connect, $_ad_nick);
	$_ad_name = mysqli_real_escape_string($connect, $_ad_name);
	$_ad_birth = mysqli_real_escape_string($connect, $_ad_birth);
	$_ad_joining = mysqli_real_escape_string($connect, $_ad_joining);
	$_ad_telegram_token = mysqli_real_escape_string($connect, $_ad_telegram_token);
	$_ad_line_token = mysqli_real_escape_string($connect, $_ad_line_token);

	$query = "UPDATE admin SET 
		ad_pw = '".$_ad_pw."',
		ad_nick = '".$_ad_nick."',
		ad_name = '".$_ad_name."',
		ad_birth = '".$_ad_birth."',
		ad_joining = '".$_ad_joining."',
		ad_data = '".$_ad_data."',
		ad_telegram_token = '".$_ad_telegram_token."',
		ad_line_token = '".$_ad_line_token."'
		WHERE idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 직원 프로필 수정
}elseif( $_a_mode == "adProfileFile" ){

	$_idx = $_POST['idx'] ?? "";
	
	// SQL Injection 방지를 위한 escape 처리
	$_idx = mysqli_real_escape_string($connect, $_idx);

	include($docRoot.'/class/image.php'); //이미지 처리 클래스

	$_uploads_dir = "../data/uploads";

	//$_save_file_name = "ad_profile_file_".$_idx."_".time();
	$_save_file_name = "ad_profile_file_".$_idx;

	$_imgfile = $_FILES['fileObj']['name'] ?? "";
	$_tmpfile = $_FILES['fileObj']['tmp_name'] ?? "";

	if ( $_imgfile ) {
		if (!($_FILES['fileObj']['error'] ?? 1)) {

			//확장자
			$extension = pathinfo($_imgfile, PATHINFO_EXTENSION);

			$_save_filename = $_save_file_name.".".$extension;
			$_destination = $_uploads_dir."/".$_save_filename;
			move_uploaded_file($_tmpfile, $_destination);

			$image = new SimpleImage();
			$image->load($_destination);
			$image->resize(200, 200);
			$image->save($_destination);
			
			// 파일명도 escape 처리
			$_save_filename = mysqli_real_escape_string($connect, $_save_filename);

			$query = "UPDATE admin SET 
				ad_image = '".$_save_filename."'
				WHERE idx = '".$_idx."' ";
			sql_query_error($query);

		}
	}

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 월차,반차 신청
}elseif( $_a_mode == "staff_holiday_reg" ){

	$_tidx = $_POST['tidx'] ?? "";
	$_mode = $_POST['mode'] ?? "";
	$_date_s = $_POST['date_s'] ?? "";
	$_date_e = $_POST['date_e'] ?? "";
	$_memo = $_POST['memo'] ?? "";
	
	// 날짜 필드 처리 (빈 값일 경우 0000-00-00)
	if (empty($_date_s)) $_date_s = '0000-00-00';
	if (empty($_date_e)) $_date_e = '0000-00-00';
	
	// SQL Injection 방지를 위한 escape 처리
	$_tidx = mysqli_real_escape_string($connect, $_tidx);

	$_target = wepix_fetch_array(wepix_query_error("select * from admin where idx = '".$_tidx."' "));

	// 배열 검증
	if (!is_array($_target)) {
		$_target = ['ad_id' => '', 'ad_name' => ''];
	}

	$_ary_data = array(
		"reg" => array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain ),
		"target" => array(
			"id" => $_target['ad_id'] ?? "",
			"name" => $_target['ad_name'] ?? ""
		)
	);

	$_data = json_encode($_ary_data, JSON_UNESCAPED_UNICODE);

	if( $_date_s && !$_date_e ) $_date_e = $_date_s;
	
	// 날짜 필드 재확인 (escape 처리 직전)
	if (empty($_date_s) || $_date_s == '') $_date_s = '0000-00-00';
	if (empty($_date_e) || $_date_e == '') $_date_e = '0000-00-00';
	
	// SQL Injection 방지를 위한 escape 처리
	$_data = mysqli_real_escape_string($connect, $_data);
	$_mode = mysqli_real_escape_string($connect, $_mode);
	$_date_s = mysqli_real_escape_string($connect, $_date_s);
	$_date_e = mysqli_real_escape_string($connect, $_date_e);
	$_memo = mysqli_real_escape_string($connect, $_memo);

	$query = "insert schedule_sttaf set
				tidx = '".$_tidx."',
				mode = '".$_mode."',
				date_s = '".$_date_s."',
				date_e = '".$_date_e."',
				data = '".$_data."',
				memo = '".$_memo."',
				state = '1',
				reg_date = '".$action_time."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 월차,반차 수정
}elseif( $_a_mode == "staff_holiday_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_tidx = $_POST['tidx'] ?? "";
	$_mode = $_POST['mode'] ?? "";
	$_date_s = $_POST['date_s'] ?? "";
	$_date_e = $_POST['date_e'] ?? "";
	$_memo = $_POST['memo'] ?? "";
	$_state = $_POST['state'] ?? "";
	
	// 날짜 필드 처리 (빈 값일 경우 0000-00-00)
	if (empty($_date_s)) $_date_s = '0000-00-00';
	if (empty($_date_e)) $_date_e = '0000-00-00';
	
	// SQL Injection 방지를 위한 escape 처리
	$_idx = mysqli_real_escape_string($connect, $_idx);

	$data = sql_fetch_array(sql_query_error("select tidx, data from schedule_sttaf WHERE idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['tidx' => '', 'data' => '{}'];
	}

	$_data_json = json_decode($data['data'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_data_json)) {
		$_data_json = [];
	}

	//신청인이 변경되었을경우
	if( ($data['tidx'] ?? '') != $_tidx ){
		
		// SQL Injection 방지
		$_tidx_escaped = mysqli_real_escape_string($connect, $_tidx);

		$_target = wepix_fetch_array(wepix_query_error("select * from admin where idx = '".$_tidx_escaped."' "));

		// 배열 검증
		if (!is_array($_target)) {
			$_target = ['ad_id' => '', 'ad_name' => ''];
		}

		$_data_json['target'] = array(
			"id" => $_target['ad_id'] ?? "",
			"name" => $_target['ad_name'] ?? ""
		);

	}
	
	if (!isset($_data_json['mod']) || !is_array($_data_json['mod'])) {
		$_data_json['mod'] = [];
	}

	$_data_json['mod'][] = array(
		"state" => $_state,
		"date" => $action_time,
		"id" => $_sess_id,
		"name" => $_ad_name,
		"ip" => $check_ip,
		"domain" => $check_domain
	);

	if ( $_state > 2 ){
		$_data_json['approval'] = array(
			"date" => $action_time,
			"id" => $_sess_id,
			"name" => $_ad_name,
			"ip" => $check_ip,
			"domain" => $check_domain
		);
	}

	$_data = json_encode($_data_json, JSON_UNESCAPED_UNICODE);

	if( $_date_s && !$_date_e ) $_date_e = $_date_s;
	
	// 날짜 필드 재확인 (escape 처리 직전)
	if (empty($_date_s) || $_date_s == '') $_date_s = '0000-00-00';
	if (empty($_date_e) || $_date_e == '') $_date_e = '0000-00-00';
	
	// SQL Injection 방지를 위한 escape 처리
	$_tidx = mysqli_real_escape_string($connect, $_tidx);
	$_mode = mysqli_real_escape_string($connect, $_mode);
	$_date_s = mysqli_real_escape_string($connect, $_date_s);
	$_date_e = mysqli_real_escape_string($connect, $_date_e);
	$_data = mysqli_real_escape_string($connect, $_data);
	$_memo = mysqli_real_escape_string($connect, $_memo);
	$_state = mysqli_real_escape_string($connect, $_state);

	$query = "UPDATE schedule_sttaf SET 
		tidx = '".$_tidx."',
		mode = '".$_mode."',
		date_s = '".$_date_s."',
		date_e = '".$_date_e."',
		data = '".$_data."',
		memo = '".$_memo."',
		state = '".$_state."'
		WHERE idx = '".$_idx."' ";
	sql_query_error($query);

	$_msg = "완료";

	$response = array('success' => true, 'msg' => $_msg );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>