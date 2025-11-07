<?
$_session_save_path = "session";
include("lib/inc_common.php");

	/* ---- v2 버전 OOP 화 --- */
	require_once __DIR__.'/autoloader.php';

	$_processing_mode = securityVal($_GET['processing_mode']);

	foreach($_POST as $key => $value){
		${"_".$key} = securityVal($value);
	}

	$_reg_d = array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );

	if( $_processing_mode ){

		include("processing/processing.".$_processing_mode.".php");

		exit;
	}



////////////////////////////////////////////////////////////////////////////////////////////////
// 월차,반차 신청
if( $_a_mode == "staff_holiday_reg" ){
/*
	$_target = wepix_fetch_array(wepix_query_error("select * from admin where idx = '".$_tidx."' "));

	$_ary_data = array(
		"reg" => array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain ),
		"target" => array(
			"id" => $_target['ad_id'],
			"name" => $_target['ad_name']
		)
	);

	$_data = json_encode($_ary_data, JSON_UNESCAPED_UNICODE);

	if( $_date_s && !$_date_e ) $_date_e = $_date_s;

	$query = "insert schedule_sttaf set
				tidx = '".$_tidx."',
				mode = '".$_mode."',
				date_s = '".$_date_s."',
				date_e = '".$_date_e."',
				data = '".$_data."',
				memo = '".$_memo."' ";
	sql_query_error($query);

	$_msg = "완료";
	$response = array('success' => true, 'msg' => $_msg );
*/
////////////////////////////////////////////////////////////////////////////////////////////////
// 월차,반차 수정
}elseif( $_a_mode == "staff_holiday_modify" ){

/*
	$data = sql_fetch_array(sql_query_error("select tidx, data from schedule_sttaf WHERE idx = '".$_idx."' "));

	$_data_json = json_decode($data['data'], true);

	//신청인이 변경되었을경우
	if( $data['tidx'] != $_tidx ){

		$_target = wepix_fetch_array(wepix_query_error("select * from admin where idx = '".$_tidx."' "));

		$_data_json['target'] = array(
			"id" => $_target['ad_id'],
			"name" => $_target['ad_name']
		);

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
*/
////////////////////////////////////////////////////////////////////////////////////////////////
// 거래처 등록
}elseif( $_a_mode == "partners_reg" ){

	$_ary_info = array(
		"nation" => $_nation,
		"hp" => array(
			"url" => $_hp_url,
			"id" => $_hp_id,
			"pw" => $_hp_pw
		),
		"info" => array(
			"tel" => $_tel,
			"email" => $_email
		),
		"keeper" => array(
			"name" => $_keeper_name,
			"rank" => $_keeper_rank,
			"tel" => $_keeper_tel
		)
	);

	$_info = json_encode($_ary_info, JSON_UNESCAPED_UNICODE);
	
	$_ary_reg = array(
		"reg" => array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain )
	);

	$_reg = json_encode($_ary_reg, JSON_UNESCAPED_UNICODE);

	$query = "INSERT partners SET
		name = '".$_name."',
		category = '".$_category."',
		info = '".$_info."',
		memo = '".$_memo."',
		reg = '".$_reg."' ";
	sql_query_error($query);

	$_msg = "완료";
	$response = array('success' => true, 'msg' => $_msg );

////////////////////////////////////////////////////////////////////////////////////////////////
// 거래처 수정
}elseif( $_a_mode == "partners_modify" ){

	$data = wepix_fetch_array(wepix_query_error("select * from partners WHERE idx = '".$_idx."' "));

	$_ary_info = array(
		"nation" => $_nation,
		"hp" => array(
			"url" => $_hp_url,
			"id" => $_hp_id,
			"pw" => $_hp_pw
		),
		"info" => array(
			"tel" => $_tel,
			"email" => $_email
		),
		"keeper" => array(
			"name" => $_keeper_name,
			"rank" => $_keeper_rank,
			"tel" => $_keeper_tel
		)
	);

	$_info = json_encode($_ary_info, JSON_UNESCAPED_UNICODE);

	$_reg_json = json_decode($data['reg'], true);

	$_reg_json['mod'][] = array(
		"date" => $action_time,
		"id" => $_sess_id,
		"name" => $_ad_name,
		"ip" => $check_ip,
		"domain" => $check_domain
	);

	$_reg = json_encode($_reg_json, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE partners SET 
		name = '".$_name."',
		category = '".$_category."',
		info = '".$_info."',
		memo = '".$_memo."',
		reg = '".$_reg."'
		WHERE idx = '".$_idx."' ";
	sql_query_error($query);

	$_msg = "완료";
	$response = array('success' => true, 'msg' => $_msg );

}


	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;
?>