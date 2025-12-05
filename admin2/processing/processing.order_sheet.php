<?
ini_set('display_errors', 1);
error_reporting(E_ALL);

	$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";
	$_reg_d = array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );
	$response = array('success' => false, 'msg' => '잘못된 요청입니다.');

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 신규 등록
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_a_mode == "orderSheet_reg" ){

	$_oo_name = $_POST['oo_name'] ?? "";
	$_oo_import = $_POST['oo_import'] ?? "";
	$_oo_form_idx = (int)($_POST['oo_form_idx'] ?? 0);
	$_sum_currency = $_POST['sum_currency'] ?? "";
	$_sum_exchange_rate = (double)($_POST['sum_exchange_rate'] ?? 0);
	$_oo_memo = $_POST['oo_memo'] ?? "";

	//주문서 소트 최대값 구하기
	$data = sql_fetch_array(sql_query_error("select MAX(oo_sort) as max from ona_order limit 1 "));
	
	// 배열 검증
	if (!is_array($data)) {
		$data = ['max' => 0];
	}

	$_oo_sort = ($data['max'] ?? 0) + 1;

	$_ary_reg = array(
		"reg" => array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain )
	);

	$_reg = json_encode($_ary_reg, JSON_UNESCAPED_UNICODE);

	$query = "insert ona_order set
		oo_name = '".$_oo_name."',
		oo_import = '".$_oo_import."',
		oo_form_idx = '".$_oo_form_idx."',
		oo_sort = '".$_oo_sort."',
		oo_sum_currency = '".$_sum_currency."',
		oo_sum_exchange_rate = '".$_sum_exchange_rate."',
		oo_memo = '".$_oo_memo."',
		oo_json = '[]',
		oo_price_data = '[]',
		oo_false = '[]',
		oo_sum_goods = '0',
		oo_sum_qty = '0',
		oo_sum_weight = '0',
		oo_sum_price = '0',
		oo_sum_cbm = '0',
		oo_state = '0',
		oo_date_data = '{}',
		oo_stock = '{}',
		oo_upload_file = '[]',
		oo_fn_price = '0',
		oo_express_data = '{}',
		oo_tex_data = '{}',
		oo_po_name = '',
		oo_approval_date = '0000-00-00',
		oo_date = '0',
		oo_code = '',
		oo_token = '',
		oo_date_modify = '0',
		oo_c_idx = '[]',
		oo_price = '[]',
		oo_qty = '[]',
		oo_unit_state = '[]',
		oo_price_jp = '0',
		oo_price_kr = '0',
		oo_price_date = '0000-00-00',
		oo_in_date = '0000-00-00',
		oo_reported_price = '0',
		oo_duty_price = '0',
		oo_duty_due_date = '0000-00-00',
		oo_duty_settlement_date = '0000-00-00',
		oo_memo2 = '',
		oo_express = 'FEDEX',
		oo_express_number = '',
		oo_box = '0',
		oo_box_weight = '0',
		oo_box_weight_fix = '0',
		oo_express_price = '0',
		oo_express_price_date = '0000-00-00',
		oo_express_price_settlement_date = '0000-00-00',
		oo_import_declaration = '',
		oo_json_date = '{}',
		comment_count = '0',
		reg = '".$_reg."',
		oo_r_mode = 'V4' ";
	sql_query_error($query);

	$_key = mysqli_insert_id($connect);

	$response = array('success' => true, 'msg' => '완료', 'key' => $_key );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "orderSheet_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_oo_state = $_POST['oo_state'] ?? "";
	$_oo_import = $_POST['oo_import'] ?? "";
	$_oo_name = $_POST['oo_name'] ?? "";
	$_oo_po_name = $_POST['oo_po_name'] ?? "";
	$_oo_form_idx = $_POST['oo_form_idx'] ?? "";
	$_oo_sum_price = $_POST['oo_sum_price'] ?? "";
	$_currency = $_POST['currency'] ?? "";
	$_oo_fn_price = $_POST['oo_fn_price'] ?? "";
	$_pay_fee = $_POST['pay_fee'] ?? "";
	$_oo_price_kr = $_POST['oo_price_kr'] ?? "";
	$_change_price_mode = $_POST['change_price_mode'] ?? [];
	$_change_price_body = $_POST['change_price_body'] ?? [];
	$_change_price_price = $_POST['change_price_price'] ?? [];
	$_pay_mode = $_POST['pay_mode'] ?? [];
	$_pay_price = $_POST['pay_price'] ?? [];
	$_pay_date = $_POST['pay_date'] ?? [];
	$_pay_memo = $_POST['pay_memo'] ?? [];
	$_express_mode = $_POST['express_mode'] ?? "";
	$_express_name = $_POST['express_name'] ?? "";
	$_express_number = $_POST['express_number'] ?? "";
	$_express_report_weight = $_POST['express_report_weight'] ?? "";
	$_express_weight = $_POST['express_weight'] ?? "";
	$_express_cbm = $_POST['express_cbm'] ?? "";
	$_express_box = $_POST['express_box'] ?? "";
	$_express_price = $_POST['express_price'] ?? "";
	$_express_price_add = $_POST['express_price_add'] ?? "";
	$_tex_num = $_POST['tex_num'] ?? "";
	$_tex_report_price = $_POST['tex_report_price'] ?? "";
	$_tex_duty_price = $_POST['tex_duty_price'] ?? "";
	$_tex_vat_price = $_POST['tex_vat_price'] ?? "";
	$_tex_commission = $_POST['tex_commission'] ?? "";
	$_order_send_date = $_POST['order_send_date'] ?? "";
	$_in_date = $_POST['in_date'] ?? "";
	$_oo_memo = $_POST['oo_memo'] ?? "";
	$_sum_currency = $_POST['sum_currency'] ?? "";
	$_sum_exchange_rate = $_POST['sum_exchange_rate'] ?? "";

	// 배열 검증
	if (!is_array($_change_price_mode)) $_change_price_mode = [];
	if (!is_array($_change_price_body)) $_change_price_body = [];
	if (!is_array($_change_price_price)) $_change_price_price = [];
	if (!is_array($_pay_mode)) $_pay_mode = [];
	if (!is_array($_pay_price)) $_pay_price = [];
	if (!is_array($_pay_date)) $_pay_date = [];
	if (!is_array($_pay_memo)) $_pay_memo = [];

	$data = sql_fetch_array(sql_query_error("SELECT oo_state, oo_express_data, oo_date_data, reg FROM ona_order WHERE oo_idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['oo_state' => '', 'oo_express_data' => '{}', 'oo_date_data' => '{}', 'reg' => '{}'];
	}

	$_oo_date_data = json_decode($data['oo_date_data'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_oo_date_data)) {
		$_oo_date_data = [];
	}

	//상태가 변경될경우
	if( $data['oo_state'] != $_oo_state ){
		//$_oo_date_data['state'][] = array( "state_before" => $data['oo_state'], "state_after" => $_oo_state, "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name );
	}

	$_pay_fee = (int)str_replace(',','', $_pay_fee);
	$_oo_price_kr = (int)str_replace(',','', $_oo_price_kr);
	$_oo_fn_price = (double)str_replace(',','', $_oo_fn_price);

	$_change_price = [];
	$_add_pay_list = [];

	for ($i=0; $i<count($_change_price_mode); $i++){

		$_mode = $_change_price_mode[$i] ?? "";
		$_body = $_change_price_body[$i] ?? "";
		$_price = (double)str_replace(',','', $_change_price_price[$i] ?? 0);

		$_change_price[] = array(
			'mode' => $_mode,
			'body' => $_body,
			'price' => $_price
		);
	}

	for ($i=0; $i<count($_pay_mode); $i++){

		$_p_price = (int)str_replace(',','', $_pay_price[$i] ?? 0);

		$_add_pay_list[] = array(
			'pay_mode' => $_pay_mode[$i] ?? "",
			'pay_price' => $_p_price,
			'pay_date' => $_pay_date[$i] ?? "",
			'pay_memo' => $_pay_memo[$i] ?? ""
		);

	}

	$_ary_price_data = array(
		'price' => $_oo_sum_price ?? 0,
		'currency' => $_currency,
		'change_price' => $_change_price,
		'pay_fee' => $_pay_fee,
		'pay_price' => $_oo_price_kr,
		'pay_list' => $_add_pay_list
	);



	$_express_price = (int)str_replace(',','', $_express_price);
	$_express_price_add = (double)str_replace(',','', $_express_price_add);

	$_ary_express_data = array(
		'mode' => $_express_mode,
		'name' => $_express_name,
		'number' => $_express_number,
		'report_weight' => $_express_report_weight,
		'weight' => $_express_weight,
		'cbm' => $_express_cbm,
		'box' => $_express_box,
		'price' => $_express_price,
		'price_add' => $_express_price_add
	);



	$_tex_report_price = (double)str_replace(',','', $_tex_report_price);
	$_tex_duty_price = (double)str_replace(',','', $_tex_duty_price);
	$_tex_vat_price = (double)str_replace(',','', $_tex_vat_price);
	$_tex_commission = (double)str_replace(',','', $_tex_commission);

	$_ary_tex_data = array(
		'num' => $_tex_num,
		'report_price' => $_tex_report_price,
		'duty_price' => $_tex_duty_price,
		'vat_price' => $_tex_vat_price,
		'commission' => $_tex_commission
	);

	$_oo_date_data['order_send_date'] = $_order_send_date;
	$_oo_date_data['in_date'] = $_in_date;

	$_oo_price_data = json_encode($_ary_price_data, JSON_UNESCAPED_UNICODE);
	$_oo_express_data = json_encode($_ary_express_data, JSON_UNESCAPED_UNICODE);
	$_oo_tex_data = json_encode($_ary_tex_data, JSON_UNESCAPED_UNICODE);
	$_date_data = json_encode($_oo_date_data, JSON_UNESCAPED_UNICODE);

	$_reg_json = json_decode($data['reg'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_reg_json)) {
		$_reg_json = [];
	}
	if (!isset($_reg_json['mod']) || !is_array($_reg_json['mod'])) {
		$_reg_json['mod'] = [];
	}

	$_reg_json['mod'][] = array(
		"date" => $action_time,
		"id" => $_sess_id,
		"name" => $_ad_name,
		"ip" => $check_ip,
		"domain" => $check_domain
	);

	$_reg = json_encode($_reg_json, JSON_UNESCAPED_UNICODE);

	/*
	oo_state = '".$_oo_state."',
	*/
	$query = "update ona_order set
		oo_name = '".$_oo_name."',
		oo_po_name = '".$_oo_po_name."',
		oo_import = '".$_oo_import."',
		oo_form_idx = '".$_oo_form_idx."',
		oo_memo = '".$_oo_memo."',
		oo_price_data = '".$_oo_price_data."',
		oo_fn_price = '".$_oo_fn_price."',
		oo_express_data = '".$_oo_express_data."',
		oo_tex_data = '".$_oo_tex_data."',
		oo_date_data = '".$_date_data."',
		oo_price_kr = '".$_oo_price_kr."',
		reg = '".$_reg."' ,
		oo_sum_currency = '".$_sum_currency."',
		oo_sum_exchange_rate = '".$_sum_exchange_rate."'
		where oo_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' , 'key' => $_idx );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문 상태변경
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "os_State" ){

	$_idx = $_POST['idx'] ?? "";
	$_state = $_POST['state'] ?? "";
	$_in_date = $_POST['in_date'] ?? "";

	$data = sql_fetch_array(sql_query_error("SELECT oo_state, oo_date_data FROM ona_order WHERE oo_idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['oo_state' => '', 'oo_date_data' => '{}'];
	}

	$_oo_date_data = json_decode($data['oo_date_data'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_oo_date_data)) {
		$_oo_date_data = [];
	}
	if (!isset($_oo_date_data['state']) || !is_array($_oo_date_data['state'])) {
		$_oo_date_data['state'] = [];
	}

	//상태가 변경될경우
	if( $data['oo_state'] != $_state ){
		$_oo_date_data['in_date'] = $_in_date;
		$_oo_date_data['state'][] = array( "state_before" => $data['oo_state'], "state_after" => $_state, "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name );
	}

	$_date_data = json_encode($_oo_date_data, JSON_UNESCAPED_UNICODE);

	$query = "update ona_order set
		oo_state = '".$_state."',
		oo_date_data = '".$_date_data."'
		where oo_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 파일등록
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "orderSheetFile" ){

	$_idx = $_POST['idx'] ?? "";
	$_smode = $_POST['smode'] ?? "";
	$_view_name = $_POST['view_name'] ?? "";

	$_uploads_dir = "../data/uploads";

	//주문서 송금확인증
	if( $_smode == "pay" ){
		$_save_file_name = "pay_file_".$_idx."_".time();

	//수입면장
	}elseif( $_smode == "import_declaration" ){
		$_save_file_name = "import_declaration_".$_idx."_".time();

	//인보이스
	}elseif( $_smode == "invoice" ){
		$_save_file_name = "invoice_".$_idx."_".time();


	}


	$_imgfile = $_FILES['fileObj']['name'];
	$_tmpfile = $_FILES['fileObj']['tmp_name'];

	if ( $_imgfile ) {
		if (!$_FILES['fileObj']['error']) {

			//확장자
			$extension = pathinfo($_imgfile, PATHINFO_EXTENSION);

			$_save_filename = $_save_file_name.".".$extension;
			$_destination = $_uploads_dir."/".$_save_filename;
			move_uploaded_file($_tmpfile, $_destination);

			$data = sql_fetch_array(sql_query_error("SELECT oo_upload_file FROM ona_order WHERE oo_idx = '".$_idx."' "));

			// 배열 검증
			if (!is_array($data)) {
				$data = ['oo_upload_file' => '{}'];
			}

			$_ary_upload_file = json_decode($data['oo_upload_file'] ?? '{}', true);

			// 배열 검증
			if (!is_array($_ary_upload_file)) {
				$_ary_upload_file = [];
			}
			
			//주문서 송금확인증
			if( $_smode == "pay" ){

				if (!isset($_ary_upload_file['pay_file']) || !is_array($_ary_upload_file['pay_file'])) {
					$_ary_upload_file['pay_file'] = [];
				}
				
				$_ary_upload_file['pay_file'][] = array(
					"name" => $_save_filename,
					"view_name" => $_view_name,
					"date" => $action_time,
					"id" => $_sess_id
				);

			//수입면장
			}elseif( $_smode == "import_declaration" ){

				if (!isset($_ary_upload_file['import_declaration']) || !is_array($_ary_upload_file['import_declaration'])) {
					$_ary_upload_file['import_declaration'] = [];
				}
				
				$_ary_upload_file['import_declaration'][] = array(
					"name" => $_save_filename,
					"view_name" => $_view_name,
					"date" => $action_time,
					"id" => $_sess_id
				);

			//인보이스
			}elseif( $_smode == "invoice" ){

				if (!isset($_ary_upload_file['invoice']) || !is_array($_ary_upload_file['invoice'])) {
					$_ary_upload_file['invoice'] = [];
				}
				
				$_ary_upload_file['invoice'][] = array(
					"name" => $_save_filename,
					"view_name" => $_view_name,
					"date" => $action_time,
					"id" => $_sess_id
				);
			}


			$_oo_upload_file = json_encode($_ary_upload_file, JSON_UNESCAPED_UNICODE);

			$query = "update ona_order set
				oo_upload_file = '".$_oo_upload_file."'
				where oo_idx = '".$_idx."' ";
			sql_query_error($query);

		}
	}

	$response = array('success' => true, 'msg' => '완료', 'idx' => $_idx, 'filename' => $_save_filename, 'reg_id' => $_sess_id, 'reg_date' => $action_time );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 송금확인증 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "orderSheet_pay_file_del" ){

	$_idx = $_POST['idx'] ?? "";
	$_smode = $_POST['smode'] ?? "";
	$_filename = $_POST['filename'] ?? "";

	$_uploads_dir = "../data/uploads";
	$_old_img = $_uploads_dir."/".$_filename;

	$is_file = file_exists($_old_img);

	//기존 파일이 있을경우 파일삭제
	if ( $is_file ) {
		@unlink($_old_img); //파일 삭제
	}

	$data = sql_fetch_array(sql_query_error("SELECT oo_upload_file FROM ona_order WHERE oo_idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['oo_upload_file' => '{}'];
	}

	$_json_data = json_decode($data['oo_upload_file'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_json_data)) {
		$_json_data = [];
	}
	if (!isset($_json_data[$_smode]) || !is_array($_json_data[$_smode])) {
		$_json_data[$_smode] = [];
	}

	$_ary_upload_file = [];

	//주문서 송금확인증
	if( $_smode == "pay" ){
	//수입면장
	}elseif( $_smode == "import_declaration" ){
	//인보이스
	}elseif( $_smode == "invoice" ){ 
	}

	if (!isset($_ary_upload_file[$_smode]) || !is_array($_ary_upload_file[$_smode])) {
		$_ary_upload_file[$_smode] = [];
	}

	for ( $i=0; $i<count($_json_data[$_smode]); $i++ ){ 
	
		if (!isset($_json_data[$_smode][$i]) || !is_array($_json_data[$_smode][$i])) continue;

		if( ($_json_data[$_smode][$i]['name'] ?? "") == $_filename ){
		
		}else{
			$_ary_upload_file[$_smode][] = array(
				"name" => $_json_data[$_smode][$i]['name'] ?? "",
				"view_name" => $_json_data[$_smode][$i]['view_name'] ?? "",
				"date" => $_json_data[$_smode][$i]['date'] ?? "",
				"id" => $_json_data[$_smode][$i]['id'] ?? ""
			);
		}
	}

	$_oo_upload_file = json_encode($_ary_upload_file, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE ona_order SET
		oo_upload_file = '".$_oo_upload_file."'
		WHERE oo_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
//결제기한
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "ApprovalPayment" ){

	$_idx = $_POST['idx'] ?? "";
	$_price = $_POST['price'] ?? "0";
	$_ap_mode = $_POST['ap_mode'] ?? "";
	$_date = $_POST['date'] ?? "";
	$_memo = $_POST['memo'] ?? "";
	$_calendar_idx = $_POST['calendar_idx'] ?? "";

	$_price = (int)str_replace(',','', $_price);

	$data = sql_fetch_array(sql_query_error("SELECT oo_name, oo_express_data, oo_approval_date FROM ona_order WHERE oo_idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['oo_name' => '', 'oo_express_data' => '{}', 'oo_approval_date' => '{}'];
	}

	$_express_data = json_decode($data['oo_express_data'] ?? '{}', true);
	$_approval_date = json_decode($data['oo_approval_date'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_express_data)) {
		$_express_data = [];
	}
	if (!is_array($_approval_date)) {
		$_approval_date = [];
	}

	$_subject = "";
	$_mode = "";
	$_kind = "";

	if( $_ap_mode == "express" ){
		$_subject = ($data['oo_name'] ?? "")." - 배송비 결제기한";
		$_mode = "결제기한";
		$_kind = "배송비";

		if( (empty($_express_data['price'] ?? '') || ($_express_data['price'] ?? 0) == 0) && $_price > 0 ){
			$_express_data['price'] = $_price;
		}

	}elseif( $_ap_mode == "tax" ){
		$_subject = ($data['oo_name'] ?? "")." - 관/부가세 결제기한";
		$_mode = "결제기한";
		$_kind = "관/부가세";
	}

	$_data_json = array(
		"oo_idx" => $_idx,
		"price" => $_price
	);

	$_data = json_encode($_data_json, JSON_UNESCAPED_UNICODE);

	$_ary_reg = array(
		"reg" => array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain )
	);

	$_reg = json_encode($_ary_reg, JSON_UNESCAPED_UNICODE);

	//if( $_calendar_a_mode == "modify" && $calendar_idx ){
	if(  $_calendar_idx ){

		$query = "update calendar set
			subject = '".$_subject."',
			kind = '".$_kind."',
			mode = '".$_mode."',
			date_s = '".$_date."',
			date_e = '".$_date."',
			data = '".$_data."',
			targrt_idx = '".$_idx."',
			memo = '".$_memo."',
			reg = '".$_reg."'
			where idx = '".$_calendar_idx."' ";
		sql_query_error($query);

	}else{

		$query = "insert calendar set
			subject = '".$_subject."',
			kind = '".$_kind."',
			mode = '".$_mode."',
			date_s = '".$_date."',
			date_e = '".$_date."',
			data = '".$_data."',
			targrt_idx = '".$_idx."',
			memo = '".$_memo."',
			comment_count = '0',
			reg = '".$_reg."' ";
		sql_query_error($query);

		$_calendar_idx = mysqli_insert_id($connect);

	}





	if (!isset($_approval_date[$_ap_mode]) || !is_array($_approval_date[$_ap_mode])) {
		$_approval_date[$_ap_mode] = [];
	}
	
	$_approval_date[$_ap_mode]['price'] = $_price;
	$_approval_date[$_ap_mode]['approval'] = array(
		"date" => $_date,
		"calendar_idx" => $_calendar_idx,
		"reg" => array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain )
	);


	$_oo_express_data = json_encode($_express_data, JSON_UNESCAPED_UNICODE);
	$_oo_approval_date = json_encode($_approval_date, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE ona_order SET
		oo_express_data = '".$_oo_express_data."',
		oo_approval_date = '".$_oo_approval_date."'
		WHERE oo_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 연결된 캘린더 완료처리
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "calendarOk" ){

	$_idx = $_POST['idx'] ?? "";
	$_calendar_idx = $_POST['calendar_idx'] ?? "";
	$_ap_mode = $_POST['ap_mode'] ?? "";

	$data = sql_fetch_array(sql_query_error("select reg from calendar WHERE idx = '".$_calendar_idx."' "));
	
	// 배열 검증
	if (!is_array($data)) {
		$data = ['reg' => '{}'];
	}

	$_reg_json = json_decode($data['reg'] ?? '{}', true);
	
	// 배열 검증
	if (!is_array($_reg_json)) {
		$_reg_json = [];
	}

	$_reg_json['mod'][] = $_reg_d;
	$_reg = json_encode($_reg_json, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE calendar SET state = 'E', reg = '".$_reg."' WHERE idx = '".$_calendar_idx."' ";
	sql_query_error($query);

	$data = sql_fetch_array(sql_query_error("SELECT oo_approval_date FROM ona_order WHERE oo_idx = '".$_idx."' "));
	
	// 배열 검증
	if (!is_array($data)) {
		$data = ['oo_approval_date' => '{}'];
	}

	$_approval_date = json_decode($data['oo_approval_date'] ?? '{}', true);
	
	// 배열 검증
	if (!is_array($_approval_date)) {
		$_approval_date = [];
	}
	if (!isset($_approval_date[$_ap_mode]) || !is_array($_approval_date[$_ap_mode])) {
		$_approval_date[$_ap_mode] = ['approval' => []];
	}
	if (!isset($_approval_date[$_ap_mode]['approval']) || !is_array($_approval_date[$_ap_mode]['approval'])) {
		$_approval_date[$_ap_mode]['approval'] = [];
	}

	$_approval_date[$_ap_mode]['approval']['calendar_state'] = "E";
	$_approval_date[$_ap_mode]['approval']['calendar_reg'] = $_reg_d;

	$_oo_approval_date = json_encode($_approval_date, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE ona_order SET
		oo_approval_date = '".$_oo_approval_date."'
		WHERE oo_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 연결된 캘린더 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "calendarDel" ){

	$_idx = $_POST['idx'] ?? "";
	$_ap_mode = $_POST['ap_mode'] ?? "";
	$_calendar_idx = $_POST['calendar_idx'] ?? "";

	$data = sql_fetch_array(sql_query_error("SELECT oo_approval_date FROM ona_order WHERE oo_idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['oo_approval_date' => '{}'];
	}

	$_approval_date = json_decode($data['oo_approval_date'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_approval_date)) {
		$_approval_date = [];
	}
	if (!isset($_approval_date[$_ap_mode]) || !is_array($_approval_date[$_ap_mode])) {
		$_approval_date[$_ap_mode] = ['approval' => []];
	}
	if (!isset($_approval_date[$_ap_mode]['approval']) || !is_array($_approval_date[$_ap_mode]['approval'])) {
		$_approval_date[$_ap_mode]['approval'] = [];
	}

	$_approval_date[$_ap_mode]['approval']['calendar_idx'] = "";

	$_oo_approval_date = json_encode($_approval_date, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE ona_order SET
		oo_approval_date = '".$_oo_approval_date."'
		WHERE oo_idx = '".$_idx."' ";
	sql_query_error($query);

	$query = "DELETE FROM calendar WHERE	idx =".$_calendar_idx;
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "orderSheet_del" ){

	$_idx = $_POST['idx'] ?? "";

	$query = "DELETE FROM ona_order WHERE	oo_idx =".$_idx;
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 가격 신규등록
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "orderSheet_price_new" ){

	$_idx = $_POST['idx'] ?? "";
	$price = $_POST['price'] ?? "0";
	$_reg_mode = $_POST['reg_mode'] ?? "";
	$_oop_code = $_POST['oop_code'] ?? "";

	$data = sql_fetch_array(sql_query_error("select cd_price_fn, cd_price_history from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
	
	// 배열 검증
	if (!is_array($data)) {
		$data = ['cd_price_fn' => '{}', 'cd_price_history' => '[]'];
	}

	$_cd_price_data = json_decode($data['cd_price_fn'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_cd_price_data)) {
		$_cd_price_data = [];
	}

	$_price = (double)str_replace(',','', $price);
	//$_price = str_replace(',','', $price);

	$_ary_price_hidtory = [];
	$_ary_price_hidtory[] = array(
		"reg_mode" => $_reg_mode,
		"oop_code" => $_oop_code,
		"price" => $_price,
		"date" => $action_time,
		"id" => $_sess_id,
		"ip" => $check_ip
	);

	//주문가
	if( $_reg_mode == "newprice" ){
		$_cd_price_data[$_oop_code] = $_price;

	//인보이스가
	}elseif( $_reg_mode == "newinvoiceprice" ){
		if (!isset($_cd_price_data['invoice']) || !is_array($_cd_price_data['invoice'])) {
			$_cd_price_data['invoice'] = [];
		}
		$_cd_price_data['invoice'][$_oop_code] = $_price;
	}

	$_cd_price_fn = json_encode($_cd_price_data, JSON_UNESCAPED_UNICODE);
	$_cd_price_history = json_encode($_ary_price_hidtory, JSON_UNESCAPED_UNICODE);

	sql_query_error("update "._DB_COMPARISON." set 
		cd_price_fn = '".$_cd_price_fn."',
		cd_price_history = '".$_cd_price_history."' 
		WHERE CD_IDX = ".$_idx." ");

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 가격수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "orderSheet_price_modify" ){

	$_cd_idx = $_POST['cd_idx'] ?? "";
	$_value = $_POST['value'] ?? "0";
	$_mod_mode = $_POST['mod_mode'] ?? "";
	$_oop_code = $_POST['oop_code'] ?? "";

	$data = sql_fetch_array(sql_query_error("select cd_price_fn, cd_price_history from "._DB_COMPARISON." where CD_IDX = '".$_cd_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['cd_price_fn' => '{}', 'cd_price_history' => '[]'];
	}

	$_cd_price_data = json_decode($data['cd_price_fn'] ?? '{}', true);
	$_ary_price_hidtory = json_decode($data['cd_price_history'] ?? '[]', true);

	// 배열 검증
	if (!is_array($_cd_price_data)) {
		$_cd_price_data = [];
	}
	if (!is_array($_ary_price_hidtory)) {
		$_ary_price_hidtory = [];
	}

	$_price = (double)str_replace(',','', $_value);
	//$_price = str_replace(',','', $_value);

	$_ord_price = 0;
	if( $_mod_mode == "price" ){
		$_ord_price = $_cd_price_data[$_oop_code] ?? 0;
	}elseif( $_mod_mode == "invoicePrice" ){
		$_ord_price = isset($_cd_price_data['invoice'][$_oop_code]) ? $_cd_price_data['invoice'][$_oop_code] : 0;
	}

	$_ary_price_hidtory[] = array(
		"mod_mode" => $_mod_mode,
		"oop_code" => $_oop_code,
		"price" => $_price,
		"ord_price" => $_ord_price,
		"date" => $action_time,
		"id" => $_sess_id,
		"ip" => $check_ip
	);

	if( $_mod_mode == "price" ){
		$_cd_price_data[$_oop_code] = $_price;
	}elseif( $_mod_mode == "invoicePrice" ){
		$_cd_price_data['invoice'][$_oop_code] = $_price;
	}
	

	$_cd_price_fn = json_encode($_cd_price_data, JSON_UNESCAPED_UNICODE);
	$_cd_price_history = json_encode($_ary_price_hidtory, JSON_UNESCAPED_UNICODE);

	sql_query_error("update "._DB_COMPARISON." set 
		cd_price_fn = '".$_cd_price_fn."',
		cd_price_history = '".$_cd_price_history."' 
		WHERE CD_IDX = ".$_cd_idx." ");

	$response = array('success' => true, 'msg' => '완료', 'uprice' => $_price );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 재고일괄 등록
}elseif( $_a_mode == "os_allStock" ){

	$_os_idx = $_POST['os_idx'] ?? "";
	$_ps_idx = $_POST['ps_idx'] ?? [];
	$_s_qty = $_POST['s_qty'] ?? [];
	$_s_memo = $_POST['s_memo'] ?? [];
	$stock_all_memo = $_POST['stock_all_memo'] ?? "";
	$_stock_day = $_POST['stock_day'] ?? "";

	// 배열 검증
	if (!is_array($_ps_idx)) $_ps_idx = [];
	if (!is_array($_s_qty)) $_s_qty = [];
	if (!is_array($_s_memo)) $_s_memo = [];

	$_reg = array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );

	for ($i=0; $i<count($_ps_idx); $i++){

		$_this_ps_idx = $_ps_idx[$i] ?? "";
		$_this_s_qty = (int)($_s_qty[$i] ?? 0);
		
		if( !empty($_s_memo[$i] ?? '') ){
			$_this_s_memo = $stock_all_memo." - ".($_s_memo[$i] ?? "");
		}else{
			$_this_s_memo = $stock_all_memo;
		}

		$_ary_ps_income = array(
			"os_idx" => $_os_idx,
			"qty" => $_this_s_qty,
			"reg" => $_reg
		);

		$_ps_income = json_encode($_ary_ps_income, JSON_UNESCAPED_UNICODE);

		$_last = "( ".$_this_s_qty." ) ".$_this_s_memo;

		$ps_data = sql_fetch_array(sql_query_error("SELECT ps_stock FROM prd_stock WHERE ps_idx = '".$_this_ps_idx."' " ));
		
		// 배열 검증
		if (!is_array($ps_data)) {
			$ps_data = ['ps_stock' => 0];
		}

		$_psu_stock = ($ps_data['ps_stock'] ?? 0) + $_this_s_qty;
		
		//$_psu_day = $data['psu_day']." 00:00:00";

		sql_query_error("update prd_stock set 
			ps_stock = ps_stock + ".$_this_s_qty.",
			ps_stock_all = ps_stock_all + ".$_this_s_qty.",
			ps_income = '".$_ps_income."',
			ps_last_in = '".$_last."',
			ps_update_date = '".$action_time."',
			ps_in_date = '".$action_time."' 
			WHERE ps_idx = ".$_this_ps_idx." ");

		$query = "insert prd_stock_unit set
			psu_stock_idx = '".$_this_ps_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'plus',
			psu_qry = '".$_this_s_qty."',
			psu_stock = '".$_psu_stock."',
			psu_kind = '신규입고',
			psu_memo = '".$_this_s_memo."',
			psu_token = '',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."',
			reg = '' ";
		sql_query_error($query);

	}

	$_ary_oo_stock = array(
		"state" => "in",
		"reg" => $_reg
	);

	$_oo_stock = json_encode($_ary_oo_stock, JSON_UNESCAPED_UNICODE);

	$data = sql_fetch_array(sql_query_error("SELECT oo_date_data, oo_stock FROM ona_order WHERE oo_idx = '".$_os_idx."' "));

	// 배열 검증
	if (!is_array($data) || empty($data)) {
		$data = ['oo_date_data' => '{}', 'oo_stock' => '{}'];
	}

	$_json_oo_date_data = json_decode($data['oo_date_data'] ?? '{}', true);
	$_json_oo_stock = json_decode($data['oo_stock'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_json_oo_date_data)) {
		$_json_oo_date_data = [];
	}
	if (!is_array($_json_oo_stock)) {
		$_json_oo_stock = [];
	}
	if (!isset($_json_oo_date_data['stock_state']) || !is_array($_json_oo_date_data['stock_state'])) {
		$_json_oo_date_data['stock_state'] = [];
	}

	if( isset($_json_oo_stock['state']) && !empty($_json_oo_stock['state']) ){
		$_stock_state_before = $_json_oo_stock['state'];
	}else{
		$_stock_state_before = "첫등록";
	}

	$_json_oo_date_data['stock_state'][] = array( "state_before" => $_stock_state_before, "state_after" => "in", "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name );

	$_date_data = json_encode($_json_oo_date_data, JSON_UNESCAPED_UNICODE);

	$query = "update ona_order set
		oo_date_data = '".$_date_data."',
		oo_stock = '".$_oo_stock."'
		where oo_idx = '".$_os_idx."' ";
	sql_query_error($query);
	

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 그룹 상품담기
}elseif( $_a_mode == "orderSheet_groupOrder" ){

	$_oo_idx = $_POST['oo_idx'] ?? "";
	$_oop_idx = $_POST['oop_idx'] ?? "";
	$_send_idx = $_POST['send_idx'] ?? [];
	$_send_price = $_POST['send_price'] ?? [];
	$_send_qty = $_POST['send_qty'] ?? [];
	$_send_memo = $_POST['send_memo'] ?? [];
	$_item = $_POST['item'] ?? 0;
	$_total_qty = $_POST['total_qty'] ?? 0;
	$_total_price = $_POST['total_price'] ?? 0;
	$_total_weight = $_POST['total_weight'] ?? 0;
	$_total_cbm = $_POST['total_cbm'] ?? 0;

	// 배열 검증
	if (!is_array($_send_idx)) $_send_idx = [];
	if (!is_array($_send_price)) $_send_price = [];
	if (!is_array($_send_qty)) $_send_qty = [];
	if (!is_array($_send_memo)) $_send_memo = [];

	$oo_data =sql_fetch_array(sql_query_error("SELECT oo_json, oo_sum_goods, oo_sum_qty, oo_sum_weight, oo_sum_price FROM ona_order WHERE oo_idx = '".$_oo_idx."' "));

	// 배열 검증
	if (!is_array($oo_data)) {
		$oo_data = ['oo_json' => '[]', 'oo_sum_goods' => 0, 'oo_sum_qty' => 0, 'oo_sum_weight' => 0, 'oo_sum_price' => 0];
	}

	$_oo_json = json_decode($oo_data['oo_json'] ?? '[]', true);

	// 배열 검증
	if (!is_array($_oo_json)) {
		$_oo_json = [];
	}

	$_inst_selpd = [];
	for ( $i=0; $i<count($_send_idx); $i++ ){
		
		$_inst_selpd[] = array(
			"pidx" => $_send_idx[$i] ?? "",
			"price" => $_send_price[$i] ?? 0,
			"qty" => $_send_qty[$i] ?? 0,
			"memo" => $_send_memo[$i] ?? ""
		);

	} // for END

	$_save_data = array(
		"bidx" => $_oop_idx,
		"item" => $_item,
		"qty" => $_total_qty,
		"price" => $_total_price,
		"weight" => $_total_weight,
		"cbm" => $_total_cbm,
		"selpd" => $_inst_selpd
	);

	$_oo_sum_goods = 0;
	$_oo_sum_qty = 0;
	$_oo_sum_weight = 0;
	$_oo_sum_price = 0;
	$_oo_sum_cbm = 0;

	$_for_in_bidx = 0;
	$_inst_json_salt = [];

	for ($z=0; $z<count($_oo_json); $z++){
	
		if (!isset($_oo_json[$z]) || !is_array($_oo_json[$z])) continue;

		if( ($_oo_json[$z]['bidx'] ?? "") == $_oop_idx ){
			
			$_inst_json_salt[] = $_save_data;
			
			$_oo_sum_goods = $_oo_sum_goods + $_item; //주문 아이템
			$_oo_sum_qty = $_oo_sum_qty + $_total_qty; //주문 수량
			$_oo_sum_weight = $_oo_sum_weight + $_total_weight; //주문 무게
			$_oo_sum_price = $_oo_sum_price + $_total_price; //주문 금액
			$_oo_sum_cbm = $_oo_sum_cbm + $_total_cbm; // CBM

			$_for_in_bidx++;

		}else{
			
			$_inst_json_salt[] = $_oo_json[$z];
			
			$_oo_sum_goods = $_oo_sum_goods + ($_oo_json[$z]['item'] ?? 0); //주문 아이템
			$_oo_sum_qty = $_oo_sum_qty + ($_oo_json[$z]['qty'] ?? 0); //주문 수량
			$_oo_sum_weight = $_oo_sum_weight + ($_oo_json[$z]['weight'] ?? 0); //주문 무게
			$_oo_sum_price = $_oo_sum_price + ($_oo_json[$z]['price'] ?? 0); //주문 금액
			$_oo_sum_cbm = $_oo_sum_cbm + ($_oo_json[$z]['cbm'] ?? 0); // CBM

			if( ($_oo_json[$z]['false'] ?? 0) > 0 ){
				$_oo_sum_goods = $_oo_sum_goods - ($_oo_json[$z]['false'] ?? 0);
				$_oo_sum_qty = $_oo_sum_qty - ($_oo_json[$z]['false_sum_qty'] ?? 0);
				$_oo_sum_weight = $_oo_sum_weight - ($_oo_json[$z]['false_sum_weight'] ?? 0);
				$_oo_sum_price = $_oo_sum_price - ($_oo_json[$z]['false_sum_price'] ?? 0);
				$_oo_sum_cbm = $_oo_sum_cbm - ($_oo_json[$z]['false_sum_cbm'] ?? 0);
			}

		}

	} // for END


	if( $_for_in_bidx == 0 ){
		
		$_inst_json_salt[] = $_save_data;

		$_oo_sum_goods = $_oo_sum_goods + $_item; //주문 아이템
		$_oo_sum_qty = $_oo_sum_qty + $_total_qty; //주문 수량
		$_oo_sum_weight = $_oo_sum_weight + $_total_weight; //주문 무게
		$_oo_sum_price = $_oo_sum_price + $_total_price; //주문 금액
		$_oo_sum_cbm = $_oo_sum_cbm + $_total_cbm; // CBM

	}


	$_oo_json = json_encode($_inst_json_salt, JSON_UNESCAPED_UNICODE);

	$query = "update ona_order set
		oo_json = '".$_oo_json."',
		oo_sum_goods = '".$_oo_sum_goods."',
		oo_sum_qty = '".$_oo_sum_qty."',
		oo_sum_weight = '".$_oo_sum_weight."',
		oo_sum_price = '".$_oo_sum_price."',
		oo_sum_cbm = '".$_oo_sum_cbm."'
		where oo_idx = '".$_oo_idx."' ";
	sql_query_error($query);

	$response = array(
		'success' => true, 'msg' => '완료', 
		'group_sum_goods' => $_item, 'group_sum_qty' => $_total_qty, 'group_sum_weight' => $_total_weight, 'group_sum_price' => $_total_price,
		'oo_sum_goods' => $_oo_sum_goods, 'oo_sum_qty' => $_oo_sum_qty, 'oo_sum_weight' => $_oo_sum_weight, 'oo_sum_price' => $_oo_sum_price, 'oo_sum_cbm' => $_oo_sum_cbm
	);


////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 단종처리
}elseif( $_a_mode == "orderSheet_soldOut" ){

	$_oop_idx = $_POST['oop_idx'] ?? "";
	$_soldoutmode = $_POST['soldoutmode'] ?? "";
	$_num = $_POST['num'] ?? 0;

	$oop_data = sql_fetch_array(sql_query_error("select oop_data from ona_order_prd where oop_idx = '".$_oop_idx."' "));

	// 배열 검증
	if (!is_array($oop_data) || empty($oop_data)) {
		$oop_data = ['oop_data' => '[]'];
	}

	$_oop_json_check_data = substr($oop_data['oop_data'] ?? '', 0,1);
	
	if( $_oop_json_check_data == "[" ){
		$_oop_json = $oop_data['oop_data'] ?? '[]';
	}else{
		$_oop_json = '['.($oop_data['oop_data'] ?? '').']';
	}

	$_oop_jsondata = json_decode($_oop_json, true);

	// 배열 검증
	if (!is_array($_oop_jsondata)) {
		$_oop_jsondata = [];
	}

	$_cd_sale_state = "Y";
	$_cd_idx = "";

	if( isset($_oop_jsondata[$_num]) && is_array($_oop_jsondata[$_num]) ){
		if( $_soldoutmode == "out" ){
			$_oop_jsondata[$_num]['state'] = "out";
			$_cd_sale_state = "N";
		}elseif( $_soldoutmode == "on" ){
			$_oop_jsondata[$_num]['state'] = "on";
			$_cd_sale_state = "Y";
		}

		$_cd_idx = $_oop_jsondata[$_num]['idx'] ?? "";
	}

	$_oop_data = json_encode($_oop_jsondata, JSON_UNESCAPED_UNICODE);

	$query = "update ona_order_prd set
		oop_data = '".$_oop_data."'
		where oop_idx = '".$_oop_idx."' ";
	sql_query_error($query);

	if( $_cd_idx ){
		//sql_query_error("update "._DB_COMPARISON." set CD_SALE_STATE = '".$_cd_sale_state."' where CD_IDX = '".$_cd_idx."' ");
	}

	$response = array('success' => true, 'msg' => '완료' );


////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 주문 실패처리
}elseif( $_a_mode == "orderSheet_unitFalse" ){

	$_oo_idx = $_POST['oo_idx'] ?? "";
	$_oop_idx = $_POST['oop_idx'] ?? "";
	$_pidx = $_POST['pidx'] ?? "";
	$_unit_false_mode = $_POST['unit_false_mode'] ?? "";
	$_pidx_memo = $_POST['pidx_memo'] ?? "";

	$oo_data =sql_fetch_array(sql_query_error("SELECT oo_json, oo_false, oo_sum_goods, oo_sum_qty, oo_sum_weight, oo_sum_price FROM ona_order WHERE oo_idx = '".$_oo_idx."' "));

	// 배열 검증
	if (!is_array($oo_data) || empty($oo_data)) {
		$oo_data = ['oo_json' => '[]', 'oo_false' => '[]', 'oo_sum_goods' => 0, 'oo_sum_qty' => 0, 'oo_sum_weight' => 0, 'oo_sum_price' => 0];
	}

	$_false_check_data = substr($oo_data['oo_false'] ?? '', 0,1);
	if( $_false_check_data == "[" ){
		$_false_data = $oo_data['oo_false'] ?? '[]';
	}else{
		$_false_data = '['.($oo_data['oo_false'] ?? '').']';
	}
	$_false_json = json_decode($_false_data, true);

	// 배열 검증
	if (!is_array($_false_json)) {
		$_false_json = [];
	}

	$_oo_false = [];

	for ($z=0; $z<count($_false_json); $z++){
		
		if (!is_array($_false_json[$z])) continue;
		
		if( $_unit_false_mode == "out" ){
			
			//주문 실패 처리이고 해당값이 있는지 체크
			if( ($_false_json[$z]['pidx'] ?? "") == $_pidx ){
				$response = array( 'success' => false, 'msg' => '이미 실패처리된 상품입니다.' );
				header('Content-Type: application/json');
				echo json_encode($response); 
				exit;
			}
			$_oo_false[] = $_false_json[$z];
		
		}elseif( $_unit_false_mode == "on" ){
			
			if( ($_false_json[$z]['pidx'] ?? "") == $_pidx ){
			}else{
				$_oo_false[] = $_false_json[$z];
			}
		}

	}

	

	$_oo_json = json_decode($oo_data['oo_json'] ?? '[]', true);

	// 배열 검증
	if (!is_array($_oo_json)) {
		$_oo_json = [];
	}

	$_bidx_json_key = "";

	foreach($_oo_json as $key => $val) {
		if (!is_array($val)) continue;
		if( ($_oo_json[$key]['bidx'] ?? "") == $_oop_idx ){
			$_bidx_json_key = $key;
			break;
		}
	}

	$_selpd = [];
	if( $_bidx_json_key !== "" && isset($_oo_json[$_bidx_json_key]['selpd']) && is_array($_oo_json[$_bidx_json_key]['selpd']) ){
		$_selpd = $_oo_json[$_bidx_json_key]['selpd'];
	}


	$_unit_qty = 0;
	$_unit_price = 0;
	$_unit_sum_price = 0;
	//$_unit_memo = "";
	$_unit_memo = $_pidx_memo;

	foreach($_selpd as $key => $val) {
		if (!is_array($val)) continue;
		if( ($_selpd[$key]['pidx'] ?? "") == $_pidx ){

			if( $_unit_false_mode == "out" ){ // 주문실패시
				$_oo_json[$_bidx_json_key]['selpd'][$key]['false'] = true;
			
			}elseif( $_unit_false_mode == "on" ){ // 주문실패 복원
				$_oo_json[$_bidx_json_key]['selpd'][$key]['false'] = false;
			}

			$_unit_qty = $_oo_json[$_bidx_json_key]['selpd'][$key]['qty'] ?? 0;
			$_unit_price = $_oo_json[$_bidx_json_key]['selpd'][$key]['price'] ?? 0;
			$_unit_sum_price = $_unit_price * $_unit_qty;
			//$_unit_memo = $_oo_json[$_bidx_json_key]['selpd'][$key]['memo'];

			break;
		}
	}

	//상품무게 알아내기
	$prd_data = sql_fetch_array(sql_query_error("select cd_weight_fn from "._DB_COMPARISON." where CD_IDX = '".$_pidx."' "));

	// 배열 검증
	if (!is_array($prd_data) || empty($prd_data)) {
		$prd_data = ['cd_weight_fn' => '{}'];
	}

	$_cd_weight_data = json_decode($prd_data['cd_weight_fn'] ?? '{}', true);
	
	// 배열 검증
	if (!is_array($_cd_weight_data)) {
		$_cd_weight_data = [];
	}
	
	$_cd_weight_1 = $_cd_weight_data['1'] ?? 0;
	$_cd_weight_2 = $_cd_weight_data['2'] ?? 0;
	$_cd_weight_3 = $_cd_weight_data['3'] ?? 0;

	if( $_cd_weight_3 ){
		$_weight = $_cd_weight_3;
	}else{
		$_weight = max($_cd_weight_1, $_cd_weight_2);
	}

	$_unit_sum_weight = $_weight * $_unit_qty;

	$_old_false = $_oo_json[$_bidx_json_key]['false'] ?? 0;
	$_old_false_sum_qty = $_oo_json[$_bidx_json_key]['false_sum_qty'] ?? 0;
	$_old_false_sum_price = $_oo_json[$_bidx_json_key]['false_sum_price'] ?? 0;
	$_old_false_sum_weight = $_oo_json[$_bidx_json_key]['false_sum_weight'] ?? 0;




	// 주문실패시
	if( $_unit_false_mode == "out" ){

		//실패 데이터 남기기
		$_oo_false[] = array(
			"pidx" => $_pidx,
			"price" =>  $_unit_price,
			"qty" => $_unit_qty,
			"memo" => $_unit_memo
		);

		$_oo_sum_goods = $oo_data['oo_sum_goods'] - 1;
		$_oo_sum_qty = $oo_data['oo_sum_qty'] - (int)$_unit_qty;
		$_oo_sum_weight = $oo_data['oo_sum_weight'] - (double)$_unit_sum_weight;
		$_oo_sum_price = $oo_data['oo_sum_price'] - (double)$_unit_sum_price;



		if( $_old_false > 0 ){
			$_oo_json[$_bidx_json_key]['false'] = (int)$_old_false + 1;
		}else{
			$_oo_json[$_bidx_json_key]['false'] = 1;
		}

		if( $_old_false_sum_qty > 0 ){
			$_oo_json[$_bidx_json_key]['false_sum_qty'] = (int)$_old_false_sum_qty + (int)$_unit_qty;
		}else{
			$_oo_json[$_bidx_json_key]['false_sum_qty'] = (int)$_unit_qty;
		}

		if( $_old_false_sum_price > 0 ){
			$_oo_json[$_bidx_json_key]['false_sum_price'] = (double)$_old_false_sum_price + (double)$_unit_sum_price;
		}else{
			$_oo_json[$_bidx_json_key]['false_sum_price'] = (double)$_unit_sum_price;
		}

		if( $_old_false_sum_weight > 0 ){
			$_oo_json[$_bidx_json_key]['false_sum_weight'] = (double)$_old_false_sum_weight + (double)$_unit_sum_weight;
		}else{
			$_oo_json[$_bidx_json_key]['false_sum_weight'] = (double)$_unit_sum_weight;
		}

	// 주문실패 복원
	}elseif( $_unit_false_mode == "on" ){

		$_oo_sum_goods = $oo_data['oo_sum_goods'] + 1;
		$_oo_sum_qty = $oo_data['oo_sum_qty'] + (int)$_unit_qty;
		$_oo_sum_weight = $oo_data['oo_sum_weight'] + (double)$_unit_sum_weight;
		$_oo_sum_price = $oo_data['oo_sum_price'] + (double)$_unit_sum_price;

		if( $_old_false > 0 ){
			$_oo_json[$_bidx_json_key]['false'] = (int)$_old_false - 1;
		}else{
			$_oo_json[$_bidx_json_key]['false'] = 0;
		}

		if( $_old_false_sum_qty > 0 ){
			$_oo_json[$_bidx_json_key]['false_sum_qty'] = (int)$_old_false_sum_qty - (int)$_unit_qty;
		}else{
			$_oo_json[$_bidx_json_key]['false_sum_qty'] = 0;
		}

		if( $_old_false_sum_price > 0 ){
			$_oo_json[$_bidx_json_key]['false_sum_price'] = (double)$_old_false_sum_price - (double)$_unit_sum_price;
		}else{
			$_oo_json[$_bidx_json_key]['false_sum_price'] = 0;
		}

		if( $_old_false_sum_weight > 0 ){
			$_oo_json[$_bidx_json_key]['false_sum_weight'] = (double)$_old_false_sum_weight - (double)$_unit_sum_weight;
		}else{
			$_oo_json[$_bidx_json_key]['false_sum_weight'] = 0;
		}

	}


	$_oo_json_fn = json_encode($_oo_json, JSON_UNESCAPED_UNICODE);

	if( count($_oo_false) > 0 ){
		$_oo_false_fn = json_encode($_oo_false, JSON_UNESCAPED_UNICODE);
	}else{
		$_oo_false_fn = "";
	}
	//if( $_oo_false_fn == null ) $_oo_false_fn = "";

	$query = "update ona_order set
		oo_json = '".$_oo_json_fn."',
		oo_false = '".$_oo_false_fn."',
		oo_sum_goods = '".$_oo_sum_goods."',
		oo_sum_qty = '".$_oo_sum_qty."',
		oo_sum_weight = '".$_oo_sum_weight."',
		oo_sum_price = '".$_oo_sum_price."'
		where oo_idx = '".$_oo_idx."' ";
	sql_query_error($query);

	$response = array( 'success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서폼 등록
}elseif( $_a_mode == "orderSheetForm_reg" ){

	$_oog_name = $_POST['oog_name'] ?? "";
	$_oog_import = $_POST['oog_import'] ?? "";
	$_oog_code = $_POST['oog_code'] ?? "";
	$_oog_group = $_POST['oog_group'] ?? "";
	$_memo = $_POST['memo'] ?? "";

	$data = sql_fetch_array(sql_query_error("SELECT oog_idx FROM ona_order_group WHERE oog_code = '".$_oog_code."' "));

	if( !empty($data['oog_idx'] ?? '') ){

		$response = array('success' => false, 'msg' => '이미 존재하는 가격코드' );
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;

	}

	$query = "INSERT ona_order_group SET
		oog_name = '".$_oog_name."',
		oog_import = '".$_oog_import."',
		oog_code = '".$_oog_code."',
		oog_group = '".$_oog_group."',
		memo = '".$_memo."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서폼 수정
}elseif( $_a_mode == "orderSheetForm_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_oog_name = $_POST['oog_name'] ?? "";
	$_oog_import = $_POST['oog_import'] ?? "";
	$_oog_code = $_POST['oog_code'] ?? "";
	$_oog_group = $_POST['oog_group'] ?? "";
	$_memo = $_POST['memo'] ?? "";

	$query = "UPDATE ona_order_group SET 
		oog_name = '".$_oog_name."',
		oog_import = '".$_oog_import."',
		oog_code = '".$_oog_code."',
		oog_group = '".$_oog_group."',
		memo = '".$_memo."'
		WHERE oog_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서폼 그룹 수정
}elseif( $_a_mode == "orderSheetForm_group" ){

	$_idx = $_POST['idx'] ?? "";
	$_name = $_POST['name'] ?? [];
	$_oop_idx = $_POST['oop_idx'] ?? [];
	$_active = $_POST['active'] ?? [];
	$_oop_code = $_POST['oop_code'] ?? "";

	// 배열 검증
	if (!is_array($_name)) $_name = [];
	if (!is_array($_oop_idx)) $_oop_idx = [];
	if (!is_array($_active)) $_active = [];

	$_data_ary = [];
	for ($i=0; $i<count($_name); $i++){
		
		$_this_name = $_name[$i] ?? "";
		$_this_oop_idx = $_oop_idx[$i] ?? "";

		if( $_this_name ){

			if( !$_this_oop_idx ){
				sql_query_error(" INSERT ona_order_prd SET oop_name = '".$_this_name."', oop_code = '".$_oop_code."' ");
				$_this_oop_idx = mysqli_insert_id($connect);
			}

			$_data_ary[] = array(
				"name" => $_this_name,
				"active" => $_active[$i] ?? "",
				"oop_idx" => $_this_oop_idx
			);

		} //if( $_this_name ){
	} // for END

	$_oog_brand = json_encode($_data_ary, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE ona_order_group SET 
		oog_brand = '".$_oog_brand."'
		WHERE oog_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서폼 그룹 상품 인아웃
}elseif( $_a_mode == "orderSheetForm_group_prd_inout" ){

	$_idx = $_POST['idx'] ?? "";
	$_prd_idx = $_POST['prd_idx'] ?? [];
	$_ps_idx = $_POST['ps_idx'] ?? [];
	$_ordermemo = $_POST['ordermemo'] ?? [];
	$_state = $_POST['state'] ?? [];

	// 배열 검증
	if (!is_array($_prd_idx)) $_prd_idx = [];
	if (!is_array($_ps_idx)) $_ps_idx = [];
	if (!is_array($_ordermemo)) $_ordermemo = [];
	if (!is_array($_state)) $_state = [];

	$_data_array = [];
	for ( $i=0; $i<count($_prd_idx); $i++ ){
		
		$_this_idx = $_prd_idx[$i] ?? "";
		$_this_ps_idx = $_ps_idx[$i] ?? "";
		$_this_om = $_ordermemo[$i] ?? "";
		$_this_state = $_state[$i] ?? "";

		$_last = "";
		$_last_data = "";

		if( $_this_ps_idx ){
		
			$last_in_data = sql_fetch_array(sql_query_error("select psu_idx, psu_qry, psu_memo  from prd_stock_unit where psu_stock_idx = '".$_this_ps_idx."' 
				and psu_mode = 'plus' and psu_kind = '신규입고' order by psu_date desc"));

			// 배열 검증
			if (!is_array($last_in_data)) {
				$last_in_data = ['psu_idx' => '', 'psu_qry' => 0, 'psu_memo' => ''];
			}

			if( !empty($last_in_data['psu_idx'] ?? '') ){
				$_last = "( ".($last_in_data['psu_qry'] ?? 0)." ) ".($last_in_data['psu_memo'] ?? ""); //지워야 함
				$_last_data = array(
					"idx" => $last_in_data['psu_idx'] ?? "",
					"qty" => $last_in_data['psu_qry'] ?? 0,
					"memo" => $last_in_data['psu_memo'] ?? ""
				);
			}

		} // if( $_this_ps_idx ){

		$_data_array[] = array(
			"idx" => $_this_idx,
			"stockidx" => $_this_ps_idx,
			"om" => $_this_om,
			"last" => $_last,
			"last_data" => $_last_data,
			"state" => $_this_state
		);

	} //for END

	$_oop_data = json_encode($_data_array, JSON_UNESCAPED_UNICODE);

	$query = "update ona_order_prd set
		oop_data = '".$_oop_data."'
		where oop_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서폼 그룹 추가 상품 검색
}elseif( $_a_mode == "orderSheetForm_group_prd_search" ){

	$_keyword = $_POST['keyword'] ?? "";

	$_search_query = "";

	if ( preg_match("/[a-zA-Z]/", $_keyword) ){
		
		$_search_query .= " ( ";
		$_search_query .= " INSTR(LOWER(CD_NAME), LOWER('".$_keyword."')) ";
		$_search_query .= " or INSTR(replace(CD_NAME,' ',''), LOWER('".$_keyword."')) ";
		$_search_query .= " or INSTR(LOWER(CD_SEARCH_TERM), LOWER('".$_keyword."')) ";
		$_search_query .= " or INSTR(LOWER(CD_NAME_OG), LOWER('".$_keyword."')) ";
		$_search_query .= " or INSTR(cd_code_fn, '".$_keyword."') ";
		$_search_query .= " ) ";

	}else{

		$_search_query .= " ( ";
		$_search_query .= " INSTR(CD_NAME, '".$_keyword."') ";
		$_search_query .= " or INSTR(replace(CD_NAME,' ',''), '".$_keyword."') ";
		$_search_query .= " or INSTR(CD_SEARCH_TERM, '".$_keyword."') ";
		$_search_query .= " or INSTR(CD_NAME_OG, '".$_keyword."') ";
		$_search_query .= " or INSTR(cd_code_fn, '".$_keyword."') ";
		$_search_query .= " ) ";

	}

	$_search_query .= " or CD_IDX = '".$_keyword."' ";

	$_search_count = 0;
	$_prd_data = [];

	$query = "select CD_IDX, CD_NAME, CD_IMG, cd_code_fn from "._DB_COMPARISON." where ".$_search_query." order by CD_IDX DESC";
	$result = sql_query_error($query);
	while($list = sql_fetch_array($result)){
		
		if (!is_array($list)) continue;

		$stock_data = sql_fetch_array(sql_query_error("SELECT ps_idx, ps_rack_code, ps_in_sale_s, ps_in_sale_e, ps_in_sale_data FROM prd_stock WHERE ps_prd_idx = '".($list['CD_IDX'] ?? "")."' "));

		// 배열 검증
		if (!is_array($stock_data)) {
			$stock_data = ['ps_idx' => '', 'ps_rack_code' => '', 'ps_in_sale_s' => '', 'ps_in_sale_e' => '', 'ps_in_sale_data' => ''];
		}

		$_cd_code_data = json_decode($list['cd_code_fn'] ?? '{}', true);

		// 배열 검증
		if (!is_array($_cd_code_data)) {
			$_cd_code_data = ['jan' => ''];
		}

		$_jancode = $_cd_code_data['jan'] ?? "";

		$_in_sale = "";
		if( ($stock_data['ps_in_sale_s'] ?? '') <= $action_time && ($stock_data['ps_in_sale_e'] ?? '') >= $action_time ){
			
			//$_ps_in_sale_data = json_decode($stock_data['ps_in_sale_data'], true);
			
			$_in_sale = in_sale_icon($stock_data['ps_in_sale_s'] ?? "", $stock_data['ps_in_sale_e'] ?? "", $stock_data['ps_in_sale_data'] ?? "");

		}

		$_prd_data[] = array(
			"idx" => $list['CD_IDX'] ?? "",
			"ps_idx" => $stock_data['ps_idx'] ?? "",
			"jancode" => $_jancode,
			"ps_rack_code" => $stock_data['ps_rack_code'] ?? "",
			"name" => $list['CD_NAME'] ?? "",
			"img" => $list['CD_IMG'] ?? "",
			"in_sale_icon" => $_in_sale
		);

		$_search_count++;

	}

	$response = array( 'success' => true, 'msg' => '완료', 'count' => $_search_count, 'prd_data' => $_prd_data );



}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>