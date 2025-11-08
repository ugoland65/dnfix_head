<?

// 변수 초기화
$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";

////////////////////////////////////////////////////////////////////////////////////////////////
// 캘린더 등록
if( $_a_mode == "calendar_reg" ){

	$_subject = $_POST['subject'] ?? "";
	$_open = $_POST['open'] ?? "";
	$_kind = $_POST['kind'] ?? "";
	$_mode = $_POST['mode'] ?? "";
	$_date_s = $_POST['date_s'] ?? "";
	$_date_e = $_POST['date_e'] ?? "";
	$_memo = $_POST['memo'] ?? "";

	$_target_idx = 0;
	if( $_open == "전체공개" ){
		$_target_idx = 0;
	}elseif( $_open == "개인" ){
		$_target_idx = $_ad_idx;
		$_kind = "개인";
	}

	$_ary_reg = array(
		"reg" => array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain )
	);

	$_reg = json_encode($_ary_reg, JSON_UNESCAPED_UNICODE);

	$query = "insert calendar set
		subject = '".$_subject."',
		open = '".$_open."',
		target_idx = '".$_target_idx."',
		kind = '".$_kind."',
		mode = '".$_mode."',
		date_s = '".$_date_s."',
		date_e = '".$_date_e."',
		memo = '".$_memo."',
		reg = '".$_reg."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료', 'key' => $_key );

////////////////////////////////////////////////////////////////////////////////////////////////
// 캘린더 수정
}elseif( $_a_mode == "calendar_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_subject = $_POST['subject'] ?? "";
	$_open = $_POST['open'] ?? "";
	$_kind = $_POST['kind'] ?? "";
	$_state = $_POST['state'] ?? "";
	$_mode = $_POST['mode'] ?? "";
	$_date_s = $_POST['date_s'] ?? "";
	$_date_e = $_POST['date_e'] ?? "";
	$_memo = $_POST['memo'] ?? "";
	$_target_mb_id = $_POST['target_mb_id'] ?? [];

	// 배열 검증
	if (!is_array($_target_mb_id)) {
		$_target_mb_id = [];
	}

	$data = sql_fetch_array(sql_query_error("select reg from calendar WHERE idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['reg' => '{}'];
	}

	$_reg_json = json_decode($data['reg'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_reg_json)) {
		$_reg_json = [];
	}

	$_target_idx = 0;
	if( $_open == "전체공개" ){
		$_target_idx = 0;
	}elseif( $_open == "개인" ){
		$_target_idx = $_ad_idx;
		$_kind = "개인";
	}

	$_target_mb = "";
	for ($i=0; $i<count($_target_mb_id); $i++){
		$_target_mb .= "@".($_target_mb_id[$i] ?? "");
	}


	$_reg_json['mod'][] = array(
		"date" => $action_time,
		"id" => $_sess_id,
		"name" => $_ad_name,
		"ip" => $check_ip,
		"domain" => $check_domain
	);

	$_reg = json_encode($_reg_json, JSON_UNESCAPED_UNICODE);

	$query = "update calendar set
		subject = '".$_subject."',
		open = '".$_open."',
		target_idx = '".$_target_idx."',
		kind = '".$_kind."',
		state = '".$_state."',
		mode = '".$_mode."',
		date_s = '".$_date_s."',
		date_e = '".$_date_e."',
		target_mb = '".$_target_mb."',
		memo = '".$_memo."',
		reg = '".$_reg."'
		where idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료', 'key' => $_key );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>