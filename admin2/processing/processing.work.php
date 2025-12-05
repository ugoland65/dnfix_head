<?
ini_set('display_errors', 1);
error_reporting(E_ALL);

	// 변수 초기화
	$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";
	$response = array('success' => false, 'msg' => '잘못된 요청입니다.');

	$_reg_d = array( "date" => $action_time, "id" => $_sess_id, "idx" => $_ad_idx,  "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );

////////////////////////////////////////////////////////////////////////////////////////////////
// 업무 매뉴얼 등록
if( $_a_mode == "work_manual_reg" ){

	$_subject = $_POST['subject'] ?? "";
	$body = $_POST['body'] ?? "";
	$_category = $_POST['category'] ?? "";

	$_ary_reg = array(
		"reg" => array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain )
	);

	$_reg = json_encode($_ary_reg, JSON_UNESCAPED_UNICODE);


	// 파일코드 만들기
	$_file_code = $check_time."_".$_ad_idx;
	$_uploads_dir = "../data/work_manual";

	$_uploadfile = $_FILES['work_manual_file'] ?? [];
	$_uploadfileName = $_uploadfile['name'] ?? "";
	$_ufilename = [];

	if (isset($_FILES["work_manual_file"]["error"]) && is_array($_FILES["work_manual_file"]["error"])) {
		foreach ($_FILES["work_manual_file"]["error"] as $key => $error) {
			if ($error == UPLOAD_ERR_OK) {
				$tmp_name = $_FILES["work_manual_file"]["tmp_name"][$key] ?? "";

				$name = $_file_code."_".basename($_FILES["work_manual_file"]["name"][$key] ?? "");
				move_uploaded_file($tmp_name, "$_uploads_dir/$name");

				$_ufilename[] = $name;

			}
		}
	}


	$_ary_file = array(
		"file_code" => $_file_code,
		"file_name" => $_ufilename
	);

	$_file = json_encode($_ary_file, JSON_UNESCAPED_UNICODE);

	$query = "insert work_manual set
		subject = '".$_subject."',
		body = '".$body."',
		category = '".$_category."',
		file = '".$_file."',
		reg = '".$_reg."',
		reg_date = '".$action_time."' ";
	sql_query_error($query);

	$_key = mysqli_insert_id($connect);

	$response = array('success' => true, 'msg' => '완료', 'key' => $_key );

////////////////////////////////////////////////////////////////////////////////////////////////
// 업무 매뉴얼 수정
}elseif( $_a_mode == "work_manual_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_subject = $_POST['subject'] ?? "";
	$body = $_POST['body'] ?? "";
	$_category = $_POST['category'] ?? "";
	$old_work_manual_file = $_POST['old_work_manual_file'] ?? [];

	// 배열 검증
	if (!is_array($old_work_manual_file)) {
		$old_work_manual_file = [];
	}

	$data = sql_fetch_array(sql_query_error("select file, reg from work_manual WHERE idx = '".$_idx."' "));
	
	// 배열 검증
	if (!is_array($data) || empty($data)) {
		$data = ['file' => '{}', 'reg' => '{}'];
	}

	$_file_data = json_decode($data['file'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_file_data)) {
		$_file_data = ['file_code' => '', 'file_name' => []];
	}

	// 파일코드 만들기
	$_file_code = $_file_data['file_code'] ?? "";
	$_uploads_dir = "../data/work_manual";

	$new_file_count = 0;
	$_ufilename = [];

	if (isset($_FILES["work_manual_file"]["error"]) && is_array($_FILES["work_manual_file"]["error"])) {
		foreach ($_FILES["work_manual_file"]["error"] as $key => $error) {
			if ($error == UPLOAD_ERR_OK) {
				$tmp_name = $_FILES["work_manual_file"]["tmp_name"][$key] ?? "";

				$name = $_file_code."_".basename($_FILES["work_manual_file"]["name"][$key] ?? "");
				move_uploaded_file($tmp_name, "$_uploads_dir/$name");

				$_ufilename[] = $name;
				$new_file_count++;
			}
		}
	}

	//새로운 파일이 등록되었을경우
	if( count($_ufilename) > 0 ){
		$_file_data['file_name'] = array_merge($old_work_manual_file, $_ufilename);
	}else{
		$_file_data['file_name'] = $old_work_manual_file;
	}

	$_file = json_encode($_file_data, JSON_UNESCAPED_UNICODE);

	$_reg_json = json_decode($data['reg'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_reg_json)) {
		$_reg_json = [];
	}

	$_reg_json['mod'] = array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );
	$_reg_json['mod_log'][] = array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );

	$_reg = json_encode($_reg_json, JSON_UNESCAPED_UNICODE);

	$query = "update work_manual set
		subject = '".$_subject."',
		body = '".$body."',
		category = '".$_category."',
		file = '".$_file."',
		reg = '".$_reg."'
		where idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료', 'key' => $_idx );


////////////////////////////////////////////////////////////////////////////////////////////////
// 업무 매뉴얼 삭제
}elseif( $_a_mode == "workManualDel" ){

	$_idx = $_POST['idx'] ?? "";

	//$_uploads_dir = "../data/".$_pmode;
	$_uploads_dir = "../data/work_manual";

	$data = sql_fetch_array(sql_query_error("select file from work_manual WHERE idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data) || empty($data)) {
		$data = ['file' => '{}'];
	}

	$_file_data = json_decode($data['file'] ?? '{}', true);
	
	// 배열 검증
	if (!is_array($_file_data)) {
		$_file_data = ['file_name' => []];
	}
	if (!isset($_file_data['file_name']) || !is_array($_file_data['file_name'])) {
		$_file_data['file_name'] = [];
	}

	//파일전부 삭제
	for ( $i=0; $i<count($_file_data['file_name']); $i++ ){ 

		$_old_img = $_uploads_dir."/".($_file_data['file_name'][$i] ?? "");
		$is_file = file_exists($_old_img);
		if ( $is_file ) {
			@unlink($_old_img); //파일 삭제
		}

	}

	sql_query_error("delete from work_manual WHERE idx = '".$_idx."' ");

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 업무 매뉴얼,일지 파일삭제
}elseif( $_a_mode == "work_file_del" ){

	$_idx = $_POST['idx'] ?? "";
	$_pmode = $_POST['pmode'] ?? "";
	$_delnum = $_POST['delnum'] ?? "";

	$_uploads_dir = "../data/".$_pmode;

	$data = sql_fetch_array(sql_query_error("select file from ".$_pmode." WHERE idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data) || empty($data)) {
		$data = ['file' => '{}'];
	}

	$_file_data = json_decode($data['file'] ?? '{}', true);
	
	// 배열 검증
	if (!is_array($_file_data)) {
		$_file_data = ['file_name' => []];
	}
	if (!isset($_file_data['file_name']) || !is_array($_file_data['file_name'])) {
		$_file_data['file_name'] = [];
	}

	$_new_file_data = [];
	for ( $i=0; $i<count($_file_data['file_name']); $i++ ){ 
		if(  $i == $_delnum ){
			//기존 파일이 있을경우 파일삭제			
			$_old_img = $_uploads_dir."/".($_file_data['file_name'][$i] ?? "");
			$is_file = file_exists($_old_img);
			if ( $is_file ) {
				@unlink($_old_img); //파일 삭제
			}
		}else{
			$_new_file_data[] = $_file_data['file_name'][$i] ?? "";
		}
	}

	$_file_data['file_name'] = $_new_file_data;

	$_file = json_encode($_file_data, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE ".$_pmode." SET
		file = '".$_file."'
		WHERE idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 업무 게시판 등록
}elseif( $_a_mode == "work_log_reg" ){

	$_subject = $_POST['subject'] ?? "";
	$_state = $_POST['state'] ?? "";
	$body = $_POST['body'] ?? "";
	$_category = $_POST['category'] ?? "";
	$_target_mb_idx = $_POST['target_mb_idx'] ?? [];

	// 배열 검증
	if (!is_array($_target_mb_idx)) {
		$_target_mb_idx = [];
	}

	$_ary_reg = array(
		"reg" => array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain )
	);

	$_reg = json_encode($_ary_reg, JSON_UNESCAPED_UNICODE);


	// 파일코드 만들기
	$_file_code = $check_time."_".$_ad_idx;
	$_uploads_dir = "../data/work_log";

	$_uploadfile = $_FILES['work_log_file'] ?? [];
	$_uploadfileName = isset($_uploadfile['name']) ? $_uploadfile['name'] : "";
	$_ufilename = [];

	if (isset($_FILES["work_log_file"]["error"]) && is_array($_FILES["work_log_file"]["error"])) {
		foreach ($_FILES["work_log_file"]["error"] as $key => $error) {
			if ($error == UPLOAD_ERR_OK) {
				$tmp_name = $_FILES["work_log_file"]["tmp_name"][$key] ?? "";

				$name = $_file_code."_".basename($_FILES["work_log_file"]["name"][$key] ?? "");
				move_uploaded_file($tmp_name, "$_uploads_dir/$name");

				$_ufilename[] = $name;

			}
		}
	}


	$_ary_file = array(
		"file_code" => $_file_code,
		"file_name" => $_ufilename
	);

	$_file = json_encode($_ary_file, JSON_UNESCAPED_UNICODE);

	if( $_state ){
	}else{
		if( $_category == "업무일지" ){
			$_state = "완료";
		}
	}

	$_target_mb = "";
	for ($i=0; $i<count($_target_mb_idx); $i++){
		$_this_idx = $_target_mb_idx[$i] ?? "";
		$_target_mb .= "@".$_this_idx;
	}

	$query = "insert work_log set
		subject = '".$_subject."',
		state = '".$_state."',
		body = '".$body."',
		category = '".$_category."',
		file = '".$_file."',
		reg = '".$_reg."',
		reg_idx = '".$_ad_idx."',
		target_mb = '".$_target_mb."',
		view_check = '0',
		cmt_s_count = '0',
		cmt_b_count = '0',
		reg_date = '".$action_time."' ";
	sql_query_error($query);

	$_key = mysqli_insert_id($connect);

	for ($i=0; $i<count($_target_mb_idx); $i++){
		
		$_this_idx = $_target_mb_idx[$i] ?? "";

		$admin_data = wepix_fetch_array(wepix_query_error("select * from admin WHERE idx = '".$_this_idx."' "));

		// 배열 검증
		if (!is_array($admin_data) || empty($admin_data)) {
			$admin_data = ['ad_line_token' => ''];
		}

		if( !empty($admin_data['ad_line_token'] ?? '') ){

			$talk_massage = $_key."[".$_category."]\"".$_subject."\" 에 멘션되었습니다.";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: Bearer '.$admin_data['ad_line_token'] , 'content-type: application/x-www-form-urlencoded' ));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT,30);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"message=".$talk_massage);
			$res = curl_exec($ch);
			curl_close($ch);

		}

	}


	$response = array('success' => true, 'msg' => '완료', 'key' => $_key );

////////////////////////////////////////////////////////////////////////////////////////////////
// 업무 게시판 수정
}elseif( $_a_mode == "work_log_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_subject = $_POST['subject'] ?? "";
	$_state = $_POST['state'] ?? "";
	$body = $_POST['body'] ?? "";
	$_category = $_POST['category'] ?? "";
	$_target_mb_idx = $_POST['target_mb_idx'] ?? [];
	$old_work_log_file = $_POST['old_work_log_file'] ?? [];

	// 배열 검증
	if (!is_array($_target_mb_idx)) {
		$_target_mb_idx = [];
	}
	if (!is_array($old_work_log_file)) {
		$old_work_log_file = [];
	}

	$data = sql_fetch_array(sql_query_error("select file, reg from work_log WHERE idx = '".$_idx."' "));
	
	// 배열 검증
	if (!is_array($data) || empty($data)) {
		$data = ['file' => '{}', 'reg' => '{}'];
	}

	$_file_data = json_decode($data['file'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_file_data)) {
		$_file_data = ['file_code' => '', 'file_name' => []];
	}

	// 파일코드 만들기
	$_file_code = $_file_data['file_code'] ?? "";
	$_uploads_dir = "../data/work_log";

	$new_file_count = 0;
	$_ufilename = [];

	if (isset($_FILES["work_log_file"]["error"]) && is_array($_FILES["work_log_file"]["error"])) {
		foreach ($_FILES["work_log_file"]["error"] as $key => $error) {
			if ($error == UPLOAD_ERR_OK) {
				$tmp_name = $_FILES["work_log_file"]["tmp_name"][$key] ?? "";

				$name = $_file_code."_".basename($_FILES["work_log_file"]["name"][$key] ?? "");
				move_uploaded_file($tmp_name, "$_uploads_dir/$name");

				$_ufilename[] = $name;
				$new_file_count++;
			}
		}
	}

	//새로운 파일이 등록되었을경우
	if( count($_ufilename) > 0 ){
		$_file_data['file_name'] = array_merge($old_work_log_file, $_ufilename);
	}else{
		$_file_data['file_name'] = $old_work_log_file;
	}

	$_file = json_encode($_file_data, JSON_UNESCAPED_UNICODE);

	$_reg_json = json_decode($data['reg'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_reg_json)) {
		$_reg_json = [];
	}

	$_reg_json['mod'] = array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );
	$_reg_json['mod_log'][] = array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );

	$_reg = json_encode($_reg_json, JSON_UNESCAPED_UNICODE);

	$_target_mb = "";
	for ($i=0; $i<count($_target_mb_idx); $i++){
		$_target_mb .= "@".($_target_mb_idx[$i] ?? "");
	}

	$query = "update work_log set
		subject = '".$_subject."',
		state = '".$_state."',
		body = '".$body."',
		category = '".$_category."',
		file = '".$_file."',
		reg = '".$_reg."',
		target_mb = '".$_target_mb."'
		where idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료', 'key' => $_idx );

////////////////////////////////////////////////////////////////////////////////////////////////
// 업무 상태처리
}elseif( $_a_mode == "work_state_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_state = $_POST['state'] ?? "";

	$data = sql_fetch_array(sql_query_error("select * from work_log WHERE idx = '".$_idx."' "));
	
	// 배열 검증
	if (!is_array($data) || empty($data)) {
		$data = ['subject' => '', 'state' => '', 'reg' => '{}', 'reg_idx' => '', 'target_mb' => ''];
	}

	$_subject = $data['subject'] ?? "";

	$_reg_data = json_decode($data['reg'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_reg_data)) {
		$_reg_data = [];
	}

	//저장된 상태와 변경상태가 다른경우 저장
	if( ($data['state'] ?? '') != $_state ){

		$_state_make = array("brfore" => $data['state'] ?? '', "after" => $_state, "reg" => $_reg_d );

		if( !isset($_reg_data['state']) || empty($_reg_data['state']) ){
			$_reg_data['state'] = array( $_state_make );
		}else{
			array_unshift($_reg_data['state'], $_state_make);
		}

		$_reg = json_encode($_reg_data, JSON_UNESCAPED_UNICODE);

		$query = "update work_log set
			state = '".$_state."',
			reg = '".$_reg."'
			where idx = '".$_idx."' ";
		sql_query_error($query);

		//작성자에게 메세지 보내기
		if( !empty($data['reg_idx'] ?? '') ){

			$reg_data = wepix_fetch_array(wepix_query_error("select ad_line_token from admin WHERE idx = '".($data['reg_idx'] ?? "")."' "));

			// 배열 검증
			if (!is_array($reg_data) || empty($reg_data)) {
				$reg_data = ['ad_line_token' => ''];
			}

			$talk_massage = "나의 업무요청 상태가 변경되었습니다. [".$_idx."]\"".$_subject."\" | ".($data['state'] ?? "")." => ".$_state;

			if( !empty($reg_data['ad_line_token'] ?? '') ){ 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: Bearer '.$reg_data['ad_line_token'] , 'content-type: application/x-www-form-urlencoded' ));
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_ENCODING, "");
				curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
				curl_setopt($ch, CURLOPT_TIMEOUT,30);
				curl_setopt($ch, CURLOPT_POSTFIELDS,"message=".$talk_massage);
				$res = curl_exec($ch);
				curl_close($ch);
			}

		}

		//멘션된 사람에게 메세지 보내기
		$_this_target_mb_idx = explode("@", $data['target_mb'] ?? "");
		for ($i=1; $i<count($_this_target_mb_idx); $i++){
		
			$reg_data = wepix_fetch_array(wepix_query_error("select ad_line_token from admin WHERE idx = '".($_this_target_mb_idx[$i] ?? "")."' "));

			// 배열 검증
			if (!is_array($reg_data) || empty($reg_data)) {
				$reg_data = ['ad_line_token' => ''];
			}

			$talk_massage = "내가  멘션된 업무요청 상태가 변경되었습니다. [".$_idx."]\"".$_subject."\" | ".($data['state'] ?? "")." => ".$_state;

			if( !empty($reg_data['ad_line_token'] ?? '') ){ 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: Bearer '.$reg_data['ad_line_token'] , 'content-type: application/x-www-form-urlencoded' ));
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_ENCODING, "");
				curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
				curl_setopt($ch, CURLOPT_TIMEOUT,30);
				curl_setopt($ch, CURLOPT_POSTFIELDS,"message=".$talk_massage);
				$res = curl_exec($ch);
				curl_close($ch);
			}

		}

	}

	$response = array('success' => true, 'msg' => '완료', 'key' => $_idx );


////////////////////////////////////////////////////////////////////////////////////////////////
// 업무 확인처리
}elseif( $_a_mode == "work_view_check" ){

	$_work_mode = $_POST['work_mode'] ?? "";
	$_tidx = $_POST['tidx'] ?? "";

	$_reg = json_encode($_reg_d, JSON_UNESCAPED_UNICODE);

	$query = "insert work_view_check set
		mode = '".$_work_mode."',
		tidx = '".$_tidx."',
		mb_idx = '".$_ad_idx."',
		reg = '".$_reg."',
		reg_date = '".$action_time."' ";
	sql_query_error($query);

	if( $_work_mode == "comment" ){
		
		$data = sql_fetch_array(sql_query_error("select * from work_comment WHERE idx = '".$_tidx."' "));
		
		// 배열 검증
		if (!is_array($data) || empty($data)) {
			$data = ['mention_mb' => ''];
		}

		$_this_target_mb_idx = explode("@", $data['mention_mb'] ?? "");

		$comment_count = sql_counter("work_view_check", "WHERE mode = 'comment' AND tidx = '".$_tidx."' ");

		if( (count($_this_target_mb_idx) - 1) == $comment_count ){
		
			$query = "update work_comment set state = '완료' where idx = '".$_tidx."' ";
			sql_query_error($query);

		}

	}

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 업무항목 등록
}elseif( $_a_mode == "work_unit_reg" ){

	$_dept = $_POST['dept'] ?? "";
	$_subject = $_POST['subject'] ?? "";
	$body = $_POST['body'] ?? "";
	$_category = $_POST['category'] ?? "";
	$_work_detail_time = $_POST['work_detail_time'] ?? "";

	$_ary_data = array(
		"detail" => array(
			"time" => $_work_detail_time
		)
	);

	$_data = json_encode($_ary_data, JSON_UNESCAPED_UNICODE);

	$_ary_reg = array(
		"reg" => array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain )
	);

	$_reg = json_encode($_ary_reg, JSON_UNESCAPED_UNICODE);

	$query = "insert work_unit set
		dept = '".$_dept."',
		subject = '".$_subject."',
		body = '".$body."',
		category = '".$_category."',
		data = '".$_data."',
		reg = '".$_reg."',
		reg_date = '".$action_time."' ";
	sql_query_error($query);

	$_key = mysqli_insert_id($connect);

	$response = array('success' => true, 'msg' => '완료', 'key' => $_key );

////////////////////////////////////////////////////////////////////////////////////////////////
// 업무항목 수정
}elseif( $_a_mode == "work_unit_modify" ){


////////////////////////////////////////////////////////////////////////////////////////////////
// 댓글입력
}elseif( $_a_mode == "work_comment_reg" ){

	$_work_comment_mode = $_POST['work_comment_mode'] ?? "";
	$_kind = $_POST['kind'] ?? "";
	$_tidx = $_POST['tidx'] ?? "";
	$comment = $_POST['comment'] ?? "";
	$_target_mb_idx = $_POST['target_mb_idx'] ?? [];

	// 배열 검증
	if (!is_array($_target_mb_idx)) {
		$_target_mb_idx = [];
	}

	$data = sql_fetch_array(sql_query_error("select * from work_log WHERE idx = '".$_tidx."' "));

	// 배열 검증
	if (!is_array($data) || empty($data)) {
		$data = ['category' => '', 'subject' => ''];
	}

	$_mention_mb = "";
	for ($i=0; $i<count($_target_mb_idx); $i++){
		
		$_mb_idx = $_target_mb_idx[$i] ?? "";
		$_mention_mb .= "@".$_mb_idx;

		$mb_data = wepix_fetch_array(wepix_query_error("select * from admin WHERE idx = '".$_mb_idx."' "));

		// 배열 검증
		if (!is_array($mb_data) || empty($mb_data)) {
			$mb_data = ['ad_line_token' => ''];
		}

		if( !empty($mb_data['ad_line_token'] ?? '') ){

			$talk_massage = "업무 게시판 (".($data['category'] ?? "").")\n제목: ".($data['subject'] ?? "")."\n 메션";
			$talk_massage .= "\n---------------------------\n\n";
			$talk_massage .= $comment;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: Bearer '.$mb_data['ad_line_token'] , 'content-type: application/x-www-form-urlencoded' ));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT,30);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"message=".$talk_massage);
			$res = curl_exec($ch);
			curl_close($ch);

		}
	}

	$_reg = json_encode($_reg_d, JSON_UNESCAPED_UNICODE);

	$_where = " WHERE mode = '".$_work_comment_mode."' AND kind = '".$_kind."' AND tidx = '".$_tidx."' ";

	$_grpno = "0";
	$_grpord = "0";
	$_depth = "0";
	$_ancestor = "";

	if( $_kind == "S" ){
		
		$max_grpno = sql_fetch_array(sql_query_error("select max(grpno) as max_grpno from work_comment ".$_where." "));
		
		// 배열 검증 및 값 추출
		if (!is_array($max_grpno) || empty($max_grpno)) {
			$_grpno = 1;
		}else{
			$_grpno = (int)($max_grpno['max_grpno'] ?? 0) + 1;
		}
		$_grpord = "0";
		$_depth = "1";
		$_ancestor = "";

	}

	$query = "insert work_comment set
		mode = '".$_work_comment_mode."',
		kind = '".$_kind."',
		tidx = '".$_tidx."',
		comment = '".$comment."',
		mb_idx = '".$_ad_idx."',
		reg = '".$_reg."',
		reg_date = '".$action_time."',
		mention_mb = '".$_mention_mb."',
		state = '대기',
		grpno = '".$_grpno."',
		grpord = '".$_grpord."',
		depth = '".$_depth."',
		ancestor = '".$_ancestor."'	";
	sql_query_error($query);

	$comment_count = sql_counter("work_comment", $_where);

	if( $_kind == "S" ){
		$query = "update work_log set cmt_s_count = '".$comment_count."' where idx = '".$_tidx."' ";
	}elseif( $_kind == "B" ){
		$query = "update work_log set cmt_b_count = '".$comment_count."' where idx = '".$_tidx."' ";
	}
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>