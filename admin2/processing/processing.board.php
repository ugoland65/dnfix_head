<?

	// 변수 초기화
	$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";

	$_reg_d = array( "date" => $action_time, "id" => $_sess_id, "idx" => $_ad_idx,  "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );

////////////////////////////////////////////////////////////////////////////////////////////////
// 게시판 등록
if( $_a_mode == "board_reg" ){

	$_kind = $_POST['kind'] ?? "";
	$_subject = $_POST['subject'] ?? "";
	$body = $_POST['body'] ?? "";

	$_ary_reg = array(
		"reg" => $_reg_d
	);

	$_reg = json_encode($_ary_reg, JSON_UNESCAPED_UNICODE);

	$query = "insert board set
		site = 'onadb',
		kind = '".$_kind."',
		subject = '".$_subject."',
		body = '".$body."',
		reg = '".$_reg."',
		reg_date = '".$action_time."' ";
	sql_query_error($query);

	$_key = mysqli_insert_id($connect);

	$response = array('success' => true, 'msg' => '완료', 'key' => $_key );

////////////////////////////////////////////////////////////////////////////////////////////////
// 게시판 수정
}elseif( $_a_mode == "board_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_kind = $_POST['kind'] ?? "";
	$_subject = $_POST['subject'] ?? "";
	$body = $_POST['body'] ?? "";

	$data = sql_fetch_array(sql_query_error("select * from board WHERE idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['reg' => '{}'];
	}

	$_reg_json = json_decode($data['reg'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_reg_json)) {
		$_reg_json = [];
	}

	$_reg_json['mod'] = $_reg_d;
	$_reg_json['mod_log'][] = $_reg_d;

	$_reg = json_encode($_reg_json, JSON_UNESCAPED_UNICODE);

	$query = "update board set
		site = 'onadb',
		kind = '".$_kind."',
		subject = '".$_subject."',
		body = '".$body."',
		reg = '".$_reg."'
		where idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료', 'key' => $_idx );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>