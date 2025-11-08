<?

	// 변수 초기화
	$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";

	$_reg_d = array( "date" => $action_time, "idx" => $_ad_idx, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 등록
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_a_mode == "prd_reg" ){

	$_cd_kind_code = $_POST['cd_kind_code'] ?? "";
	$_cd_brand_idx = $_POST['cd_brand_idx'] ?? "";
	$_cd_brand2_idx = $_POST['cd_brand2_idx'] ?? "";
	$_cd_name = $_POST['cd_name'] ?? "";
	$_cd_name_og = $_POST['cd_name_og'] ?? "";
	$_cd_name_en = $_POST['cd_name_en'] ?? "";
	$_cd_cont = $_POST['cd_cont'] ?? "";
	$_cd_memo = $_POST['cd_memo'] ?? "";
	$_cd_memo2 = $_POST['cd_memo2'] ?? "";
	$_cd_memo3 = $_POST['cd_memo3'] ?? "";
	$_cd_search_term = $_POST['cd_search_term'] ?? "";
	$_cd_release_date = $_POST['cd_release_date'] ?? "";
	$_cd_size_w = $_POST['cd_size_w'] ?? "";
	$_cd_size_h = $_POST['cd_size_h'] ?? "";
	$_cd_size_d = $_POST['cd_size_d'] ?? "";
	$_cd_size2 = $_POST['cd_size2'] ?? "";
	$_cd_weight_1 = $_POST['cd_weight_1'] ?? "";
	$_cd_weight_2 = $_POST['cd_weight_2'] ?? "";
	$_cd_weight_3 = $_POST['cd_weight_3'] ?? "";
	$_cd_code = $_POST['cd_code'] ?? "";
	$_cd_code2 = $_POST['cd_code2'] ?? "";
	$_cd_godo_code = $_POST['cd_godo_code'] ?? "";
	$_hbti_1 = $_POST['hbti_1'] ?? null;
	$_hbti_2 = $_POST['hbti_2'] ?? null;
	$_hbti_3 = $_POST['hbti_3'] ?? null;
	$_hbti_4 = $_POST['hbti_4'] ?? null;
	$_hbti_target = $_POST['hbti_target'] ?? "N";
	$_cd_site_show = $_POST['cd_site_show'] ?? "N";
	$_out_img = $_POST['out_img'] ?? "";

	include($docRoot.'/class/image.php'); //이미지 처리 클래스

	$_uploads_dir = "../data/comparion";
	$_save_file_name = "prd_".$_ad_idx."_".time();

	$_img_name = "";
	$_img_name2 = "";

	// 파일이 있을경우 ---------------------
	if ( !empty($_FILES['cd_img']['name'] ?? '') ) {
		$_img_name = imgUpload($_FILES['cd_img'], $_uploads_dir, $_save_file_name, "302", "302");

	// 외부 이미지 일경우 ---------------------
	}elseif( $_out_img ){
		$_img_name = outImgUpload($_out_img, $_uploads_dir, $_save_file_name, "302", "302");
	}


	// 파일이 있을경우
	if ( !empty($_FILES['cd_img2']['name'] ?? '') ) {
		$_save_file_name = "prd_icon_".$_ad_idx."_".time();
		$_img_name2 = imgUpload($_FILES['cd_img2'], $_uploads_dir, $_save_file_name, "100", "100");
	}
	
	// 인보이스 이미지
	$_img_add1 = "";
	if ( !empty($_FILES['cd_add1']['name'] ?? '') ) {
		$_save_file_name = "prd_invoice_".$_ad_idx."_".time();
		$_img_add1 = imgUpload($_FILES['cd_add1'], $_uploads_dir, $_save_file_name, "", "");
	}

	// 19금 대체 이미지
	$_img_add2 = "";
	if ( !empty($_FILES['cd_add2']['name'] ?? '') ) {
		$_save_file_name = "prd_c19_".$_ad_idx."_".time();
		$_img_add2 = imgUpload($_FILES['cd_add2'], $_uploads_dir, $_save_file_name, "302", "302");
	}

	//추가이미지
	$_cd_add_img_data = array(
		'add1' => array(
			'name' => '인보이스이미지',
			'filename' => $_img_add1
		),
		'add2' => array(
			'name' => '19금대체이미지',
			'filename' => $_img_add2
		)
	);
	$_cd_add_img = json_encode($_cd_add_img_data, JSON_UNESCAPED_UNICODE);


	//패키지 사이즈
	$_cd_size_data = array(
		'W' => $_cd_size_w,
		'H' => $_cd_size_h,
		'D' => $_cd_size_d
	);
	$_cd_size = json_encode($_cd_size_data);

	//중량
	$_cd_weight_data = array(
		'1' => $_cd_weight_1,
		'2' => $_cd_weight_2,
		'3' => $_cd_weight_3
	);
	$_cd_weight_fn = json_encode($_cd_weight_data);

	/*
	$response = array('success' => false, 'msg' => $_img_name );
	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;
	*/
	$_reg_data = array(
		"reg" => array(
			"mode" => "v3",
			"info" => $_reg_d
		)
	);

	$_cd_reg = json_encode($_reg_data, JSON_UNESCAPED_UNICODE);

	// HBTI 개별 요소: 존재하지 않으면 null 처리
	$_hbti_1 = $_hbti_1 ?? null;
	$_hbti_2 = $_hbti_2 ?? null;
	$_hbti_3 = $_hbti_3 ?? null;
	$_hbti_4 = $_hbti_4 ?? null;

	// 1. JSON 배열로 인코딩 (null 포함)
	$_hbti_data = [$_hbti_1, $_hbti_2, $_hbti_3, $_hbti_4];
	$_cd_hbti_data = json_encode($_hbti_data, JSON_UNESCAPED_UNICODE); // 예: ["S",null,null,"E"]

	// 2. 값 있는 것만 붙이기
	$_cd_hbt = '';
	foreach ($_hbti_data as $val) {
		if (!is_null($val) && $val !== '') {
			$_cd_hbt .= $val;
		}
	}

	if( $_hbti_target == "N" ){
		$_cd_hbti_data = null;
		$_cd_hbt = null;
	}

	$query = "insert into  "._DB_COMPARISON." set
		CD_KIND_CODE = '".$_cd_kind_code."',
		CD_BRAND_IDX = '".$_cd_brand_idx."',
		CD_BRAND2_IDX = '".$_cd_brand2_idx."',
		CD_NAME = '".$_cd_name."',
		CD_NAME_OG = '".$_cd_name_og."',
		CD_NAME_EN = '".$_cd_name_en."',
		CD_CONT = '".$_cd_cont."',
		CD_MEMO = '".$_cd_memo."',
		cd_memo2 = '".$_cd_memo2."',
		cd_memo3 = '".$_cd_memo3."',
		CD_SEARCH_TERM = '".$_cd_search_term."',
		CD_RELEASE_DATE = '".$_cd_release_date."',
		CD_IMG = '".$_img_name."',
		CD_IMG2 = '".$_img_name2."',
		cd_add_img = '".$_cd_add_img."',
		CD_SIZE = '".$_cd_size."',
		CD_SIZE2 = '".$_cd_size2."',
		cd_weight_fn = '".$_cd_weight_fn."',
		CD_CODE = '".$_cd_code."',
		CD_CODE2 = '".$_cd_code2."',
		cd_godo_code = '".$_cd_godo_code."',
		cd_hbti_data = '".$_cd_hbti_data."',
		cd_hbti = '".$_cd_hbt."',
		cd_reg_time = '".$action_time."',
		cd_reg = '".$_cd_reg."',
		cd_site_show = '".$_cd_site_show."' ";
	sql_query_error($query);

	$_key = mysqli_insert_id($connect);

	$response = array('success' => true, 'msg' => '완료', 'a_mode' => $_a_mode, 'idx' => $_key );

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "prd_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_cd_kind_code = $_POST['cd_kind_code'] ?? "";
	$_cd_brand_idx = $_POST['cd_brand_idx'] ?? "";
	$_cd_brand2_idx = $_POST['cd_brand2_idx'] ?? "";
	$_cd_name = $_POST['cd_name'] ?? "";
	$_cd_name_og = $_POST['cd_name_og'] ?? "";
	$_cd_name_en = $_POST['cd_name_en'] ?? "";
	$_cd_cont = $_POST['cd_cont'] ?? "";
	$_cd_memo = $_POST['cd_memo'] ?? "";
	$_cd_memo2 = $_POST['cd_memo2'] ?? "";
	$_cd_memo3 = $_POST['cd_memo3'] ?? "";
	$_cd_search_term = $_POST['cd_search_term'] ?? "";
	$_cd_release_date = $_POST['cd_release_date'] ?? "";
	$_cd_size_w = $_POST['cd_size_w'] ?? "";
	$_cd_size_h = $_POST['cd_size_h'] ?? "";
	$_cd_size_d = $_POST['cd_size_d'] ?? "";
	$_cd_size2 = $_POST['cd_size2'] ?? "";
	$_cd_weight_1 = $_POST['cd_weight_1'] ?? "";
	$_cd_weight_2 = $_POST['cd_weight_2'] ?? "";
	$_cd_weight_3 = $_POST['cd_weight_3'] ?? "";
	$_cd_code = $_POST['cd_code'] ?? "";
	$_cd_code2 = $_POST['cd_code2'] ?? "";
	$_cd_national = $_POST['cd_national'] ?? "";
	$_cd_inv_name1 = $_POST['cd_inv_name1'] ?? "";
	$_cd_inv_name2 = $_POST['cd_inv_name2'] ?? "";
	$_cd_inv_material = $_POST['cd_inv_material'] ?? "";
	$_cd_coo = $_POST['cd_coo'] ?? "";
	$_cd_godo_code = $_POST['cd_godo_code'] ?? "";
	$_hbti_1 = $_POST['hbti_1'] ?? null;
	$_hbti_2 = $_POST['hbti_2'] ?? null;
	$_hbti_3 = $_POST['hbti_3'] ?? null;
	$_hbti_4 = $_POST['hbti_4'] ?? null;
	$_hbti_target = $_POST['hbti_target'] ?? "N";
	$_cd_site_show = $_POST['cd_site_show'] ?? "N";
	$_ps_idx = $_POST['ps_idx'] ?? "";
	$_ps_rack_code = $_POST['ps_rack_code'] ?? "";
	$_ps_stock_object = $_POST['ps_stock_object'] ?? "";
	$_ps_alarm_count = $_POST['ps_alarm_count'] ?? "";
	$_out_img = $_POST['out_img'] ?? "";
	$_invoice_size_w = $_POST['invoice_size_w'] ?? "";
	$_invoice_size_h = $_POST['invoice_size_h'] ?? "";
	$_invoice_size_d = $_POST['invoice_size_d'] ?? "";
	$_invoice_size_cbm = $_POST['invoice_size_cbm'] ?? "";
	$_invoice_size_cbm_mode = $_POST['invoice_size_cbm_mode'] ?? "";
	$_import_plastic = $_POST['import_plastic'] ?? "";
	$_import_hscode = $_POST['import_hscode'] ?? "";
	$_import_hscode1 = $_POST['import_hscode1'] ?? "";
	$_import_hscode2 = $_POST['import_hscode2'] ?? "";

	include($docRoot.'/class/image.php'); //이미지 처리 클래스

	$_uploads_dir = "../data/comparion";
	$_save_file_name = "prd_".$_ad_idx."_".time();

	$data = sql_fetch_array(sql_query_error("select cd_reg, CD_IMG, CD_IMG2, cd_add_img  from "._DB_COMPARISON." WHERE CD_IDX = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['cd_reg' => '{}', 'CD_IMG' => '', 'CD_IMG2' => '', 'cd_add_img' => '{}'];
	}

	$_cd_add_img_data = json_decode($data['cd_add_img'] ?? '{}', true);
	$_cd_code_data = json_decode($data['cd_code_fn'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_cd_add_img_data)) {
		$_cd_add_img_data = ['add1' => ['filename' => ''], 'add2' => ['filename' => '']];
	}
	if (!is_array($_cd_code_data)) {
		$_cd_code_data = ['jan' => '', 'pcode' => ''];
	}

	$_cd_code_data['jan'] = $_cd_code;
    $_cd_code_data['pcode'] = $_cd_code2;

	$_img_name = "";
	$_img_name2 = "";
	$_img_add1 = "";
	$_img_add2 = "";

	// 파일이 있을경우 ---------------------
	if ( !empty($_FILES['cd_img']['name'] ?? '') ) {
		$_img_name = imgUpload($_FILES['cd_img'], $_uploads_dir, $_save_file_name, "302", "302");

		$is_file_dir = $_uploads_dir."/".($data['CD_IMG'] ?? "");
		$is_file_exist = file_exists($is_file_dir);
		//파일이 있을경우 파일삭제
		if ($is_file_exist) {
			@unlink($is_file_dir);
		}

	// 외부 이미지 일경우 ---------------------
	}elseif( $_out_img ){
		$_img_name = outImgUpload($_out_img, $_uploads_dir, $_save_file_name, "302", "302");

		$is_file_dir = $_uploads_dir."/".($data['CD_IMG'] ?? "");
		$is_file_exist = file_exists($is_file_dir);
		//파일이 있을경우 파일삭제
		if ($is_file_exist) {
			@unlink($is_file_dir);
		}

	}


	// 파일이 있을경우
	if ( !empty($_FILES['cd_img2']['name'] ?? '') ) {
		$_save_file_name = "prd_icon_".$_ad_idx."_".time();
		$_img_name2 = imgUpload($_FILES['cd_img2'], $_uploads_dir, $_save_file_name, "100", "100");

		$is_file_dir = $_uploads_dir."/".($data['CD_IMG2'] ?? "");
		$is_file_exist = file_exists($is_file_dir);
		//파일이 있을경우 파일삭제
		if ($is_file_exist) {
			@unlink($is_file_dir);
		}

	}
	
	// 인보이스 이미지
	if ( !empty($_FILES['cd_add1']['name'] ?? '') ) {
		
		$_old_img = isset($_cd_add_img_data['add1']['filename']) ? $_cd_add_img_data['add1']['filename'] : "";
		
		$_save_file_name = "prd_invoice_".$_ad_idx."_".time();
		$_img_add1 = imgUpload($_FILES['cd_add1'], $_uploads_dir, $_save_file_name, "", "");

		$is_file_dir = $_uploads_dir."/".$_old_img;
		$is_file_exist = file_exists($is_file_dir);
		//파일이 있을경우 파일삭제
		if ($is_file_exist) {
			@unlink($is_file_dir);
		}

	}

	// 19금 대체 이미지
	if ( !empty($_FILES['cd_add2']['name'] ?? '') ) {
		
		$_old_img = isset($_cd_add_img_data['add2']['filename']) ? $_cd_add_img_data['add2']['filename'] : "";
		
		$_save_file_name = "prd_c19_".$_ad_idx."_".time();
		$_img_add2 = imgUpload($_FILES['cd_add2'], $_uploads_dir, $_save_file_name, "302", "302");

		$is_file_dir = $_uploads_dir."/".$_old_img;
		$is_file_exist = file_exists($is_file_dir);
		//파일이 있을경우 파일삭제
		if ($is_file_exist) {
			@unlink($is_file_dir);
		}

	}

	// 추가이미지
	$_cd_add_img_data = array(
		'add1' => array(
			'name' => '인보이스이미지',
			'filename' => $_img_add1
		),
		'add2' => array(
			'name' => '19금대체이미지',
			'filename' => $_img_add2
		)
	);
	$_cd_add_img = json_encode($_cd_add_img_data, JSON_UNESCAPED_UNICODE);

	// 패키지 사이즈
	$_cd_size_data = array(
		'W' => $_cd_size_w,
		'H' => $_cd_size_h,
		'D' => $_cd_size_d
	);
	$_cd_size = json_encode($_cd_size_data);


	// 팩킹 사이즈
	if( $_invoice_size_cbm_mode != "hand" ) $_invoice_size_cbm_mode = "auto";

	// cbm 계산기
	if( $_invoice_size_cbm_mode == "auto" && $_invoice_size_d > 0 && $_invoice_size_h > 0 && $_invoice_size_w > 0 ){
		$_cbm = round(($_invoice_size_d/1000) * ($_invoice_size_h/1000) * ($_invoice_size_w/1000),3);
	}else{
		$_cbm = $_invoice_size_cbm;
	}


	$_invoice_size_data = array(
		'W' => $_invoice_size_w,
		'H' => $_invoice_size_h,
		'D' => $_invoice_size_d,
		'cbm' => $_cbm,
		'cbm_mode' => $_invoice_size_cbm_mode
	);

	$_import_information_data = array(
		'plastic' => $_import_plastic,
		'hscode' => $_import_hscode,
		'hscode1' => $_import_hscode1,
		'hscode2' => $_import_hscode2
	);

	$_cd_size_fn_data = array(
		'package' => $_cd_size_data,
		'invoice' => $_invoice_size_data,
		'import' => $_import_information_data
	);

	$_cd_size_fn = json_encode($_cd_size_fn_data);

	// 중량
	$_cd_weight_data = array(
		'1' => $_cd_weight_1,
		'2' => $_cd_weight_2,
		'3' => $_cd_weight_3
	);
	$_cd_weight_fn = json_encode($_cd_weight_data);

	$_cd_reg_data = json_decode($data['cd_reg'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_cd_reg_data)) {
		$_cd_reg_data = [];
	}

	if( empty($_cd_reg_data['modify'] ?? '') ){
		$_cd_reg_data['modify'] = array( $_reg_d );
	}else{
		array_unshift($_cd_reg_data['modify'], $_reg_d);
	}

	$_cd_reg = json_encode($_cd_reg_data, JSON_UNESCAPED_UNICODE);

	$_cd_code_fn = json_encode($_cd_code_data, JSON_UNESCAPED_UNICODE);

	// HBTI 개별 요소: 존재하지 않으면 null 처리
	$_hbti_1 = $_hbti_1 ?? null;
	$_hbti_2 = $_hbti_2 ?? null;
	$_hbti_3 = $_hbti_3 ?? null;
	$_hbti_4 = $_hbti_4 ?? null;

	// 1. JSON 배열로 인코딩 (null 포함)
	$_hbti_data = [$_hbti_1, $_hbti_2, $_hbti_3, $_hbti_4];
	$_cd_hbti_data = json_encode($_hbti_data, JSON_UNESCAPED_UNICODE); // 예: ["S",null,null,"E"]

	// 2. 값 있는 것만 붙이기
	$_cd_hbt = '';
	foreach ($_hbti_data as $val) {
		if (!is_null($val) && $val !== '') {
			$_cd_hbt .= $val;
		}
	}

	if( $_hbti_target == "N" ){
		$_cd_hbti_data = null;
		$_cd_hbt = null;
	}


	$query = "update "._DB_COMPARISON." set
		CD_KIND_CODE = '".$_cd_kind_code."',
		CD_BRAND_IDX = '".$_cd_brand_idx."',
		CD_BRAND2_IDX = '".$_cd_brand2_idx."',
		CD_NAME = '".$_cd_name."',
		CD_NAME_OG = '".$_cd_name_og."',
		CD_NAME_EN = '".$_cd_name_en."',
		CD_CONT = '".$_cd_cont."',
		CD_MEMO = '".$_cd_memo."',
		cd_memo2 = '".$_cd_memo2."',
		cd_memo3 = '".$_cd_memo3."',
		CD_SEARCH_TERM = '".$_cd_search_term."',
		CD_RELEASE_DATE = '".$_cd_release_date."',
		CD_IMG = '".$_img_name."',
		CD_IMG2 = '".$_img_name2."',
		cd_add_img = '".$_cd_add_img."',
		CD_SIZE = '".$_cd_size."',
		CD_SIZE2 = '".$_cd_size2."',
		cd_size_fn = '".$_cd_size_fn."',
		cd_weight_fn = '".$_cd_weight_fn."',
		CD_CODE = '".$_cd_code."',
		CD_CODE2 = '".$_cd_code2."',
		cd_reg = '".$_cd_reg."',
		cd_national = '".$_cd_national."',
		CD_INV_NAME1 = '".$_cd_inv_name1."',
		CD_INV_NAME2 = '".$_cd_inv_name2."',
		CD_INV_MATERIAL = '".$_cd_inv_material."',
		CD_COO = '".$_cd_coo."',
		cd_code_fn = '".$_cd_code_fn."',
		cd_godo_code = '".$_cd_godo_code."',
		cd_hbti_data = '".$_cd_hbti_data."',
		cd_hbti = '".$_cd_hbt."',
		cd_site_show = '".$_cd_site_show."'
		where CD_IDX = '".$_idx."' ";
	sql_query_error($query);


	if( $_ps_idx ){

		$query = "UPDATE prd_stock SET
			ps_rack_code = '".$_ps_rack_code."',
			ps_stock_object = '".$_ps_stock_object."',
			ps_alarm_count = '".$_ps_alarm_count."'
			where ps_idx = '".$_ps_idx."' ";
		sql_query_error($query);

	}

	$response = array('success' => true, 'msg' => '완료', 'a_mode' => $_a_mode, 'idx' => $_idx );

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 복사 등록
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "prd_copy" ){

	$_idx = $_POST['idx'] ?? "";

	$data = sql_fetch_array(sql_query_error("select * from "._DB_COMPARISON." WHERE CD_IDX = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$response = array('success' => false, 'msg' => '상품을 찾을 수 없습니다.' );
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;
	}

	$_cd_kind_code = $data['CD_KIND_CODE'];
	$_cd_brand_idx = $data['CD_BRAND_IDX'];
	$_cd_brand2_idx = $data['CD_BRAND2_IDX'];
	$_cd_name = $data['CD_NAME'];
	$_cd_name_og = $data['CD_NAME_OG'];
	$_cd_name_en = $data['CD_NAME_EN'];
	$_cd_cont = $data['CD_CONT'];
	$_cd_memo = $data['CD_MEMO'];
	$_cd_search_term = $data['CD_SEARCH_TERM'];
	$_cd_search_term = $data['CD_SEARCH_TERM'];
	$_cd_release_date = $data['CD_RELEASE_DATE'];
	$_img_name = $data['CD_IMG'];
	$_img_name2 = $data['CD_IMG2'];
	$_cd_add_img = $data['cd_add_img'];
	$_cd_size = $data['CD_SIZE'];
	$_cd_size2 = $data['CD_SIZE2'];
	$_cd_weight_fn = $data['cd_weight_fn'];
	$_cd_code = $data['CD_CODE'];
	$_cd_code2 = $data['CD_CODE2'];
	$_cd_site_show = $data['cd_site_show'];

	$_reg_data = array(
		"reg" => array(
			"mode" => "v3",
			"copy_idx" => $_idx,
			"copy" => "(".$_idx.") 복사등록",
			"info" => $_reg_d
		)
	);

	$_cd_reg = json_encode($_reg_data, JSON_UNESCAPED_UNICODE);

	/*
		CD_IMG = '".$_img_name."',
	*/
	$query = "insert into  "._DB_COMPARISON." set
		CD_KIND_CODE = '".$_cd_kind_code."',
		CD_BRAND_IDX = '".$_cd_brand_idx."',
		CD_BRAND2_IDX = '".$_cd_brand2_idx."',
		CD_NAME = '".$_cd_name."',
		CD_NAME_OG = '".$_cd_name_og."',
		CD_NAME_EN = '".$_cd_name_en."',
		CD_CONT = '".$_cd_cont."',
		CD_MEMO = '".$_cd_memo."',
		CD_SEARCH_TERM = '".$_cd_search_term."',
		CD_RELEASE_DATE = '".$_cd_release_date."',
		CD_IMG2 = '".$_img_name2."',
		cd_add_img = '".$_cd_add_img."',
		CD_SIZE = '".$_cd_size."',
		CD_SIZE2 = '".$_cd_size2."',
		cd_weight_fn = '".$_cd_weight_fn."',
		CD_CODE = '".$_cd_code."',
		CD_CODE2 = '".$_cd_code2."',
		cd_site_show = '".$_cd_site_show."',
		cd_reg_time = '".$action_time."',
		cd_reg = '".$_cd_reg."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 가격정보 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "costCalculationSave" ){

	$_idx = $_POST['idx'] ?? "";
	$_invoice_size_w = $_POST['invoice_size_w'] ?? "";
	$_invoice_size_h = $_POST['invoice_size_h'] ?? "";
	$_invoice_size_d = $_POST['invoice_size_d'] ?? "";
	$_invoice_size_cbm = $_POST['invoice_size_cbm'] ?? "";
	$_invoice_size_cbm_mode = $_POST['invoice_size_cbm_mode'] ?? "";
	$_cd_weight_1 = $_POST['cd_weight_1'] ?? "";
	$_cd_weight_2 = $_POST['cd_weight_2'] ?? "";
	$_cd_weight_3 = $_POST['cd_weight_3'] ?? "";
	$_cd_sale_price = $_POST['cd_sale_price'] ?? "0";
	$_cost_cal_kind = $_POST['cost_cal_kind'] ?? "";
	$_cd_national = $_POST['cd_national'] ?? "";
	$_cost_cal_price = $_POST['cost_cal_price'] ?? "";
	$_cost_cal_exchange_rate = $_POST['cost_cal_exchange_rate'] ?? "";
	$_cost_cal_delivery = $_POST['cost_cal_delivery'] ?? "";
	$_cost_cal_tariff = $_POST['cost_cal_tariff'] ?? "";
	$_cost_cal_incidental_cost = $_POST['cost_cal_incidental_cost'] ?? "";
	$_ex_rate = $_POST['ex_rate'] ?? "";
	$_oprice = $_POST['oprice'] ?? "";
	$_oprice_key = $_POST['oprice_key'] ?? "";
	$_kg_p = $_POST['kg_p'] ?? "";
	$_weight_mode = $_POST['weight_mode'] ?? "";
	$_weight = $_POST['weight'] ?? "";
	$_tax = $_POST['tax'] ?? "";
	$_vat = $_POST['vat'] ?? "";
	$_delivery = $_POST['delivery'] ?? "";
	$_op_won = $_POST['op_won'] ?? "";
	$_cost_p = $_POST['cost_p'] ?? "";

	$prd_data = sql_fetch_array(sql_query_error("select cd_size_fn  from "._DB_COMPARISON." WHERE CD_IDX = '".$_idx."' "));

	// 배열 검증
	if (!is_array($prd_data)) {
		$prd_data = ['cd_size_fn' => '{}'];
	}

	$_cd_size_fn_data = json_decode($prd_data['cd_size_fn'] ?? '{}', true);

	// 배열 검증
	if (!is_array($_cd_size_fn_data)) {
		$_cd_size_fn_data = [];
	}

	// 팩킹 사이즈
	if( $_invoice_size_cbm_mode != "hand" ) $_invoice_size_cbm_mode = "auto";

	// cbm 계산기
	if( $_invoice_size_cbm_mode == "auto" && $_invoice_size_d > 0 && $_invoice_size_h > 0 && $_invoice_size_w > 0 ){
		$_cbm = round(($_invoice_size_d/1000) * ($_invoice_size_h/1000) * ($_invoice_size_w/1000),3);
	}else{
		$_cbm = $_invoice_size_cbm;
	}

	$_invoice_size_data = array(
		'W' => $_invoice_size_w,
		'H' => $_invoice_size_h,
		'D' => $_invoice_size_d,
		'cbm' => $_cbm,
		'cbm_mode' => $_invoice_size_cbm_mode
	);

	$_cd_size_fn_data['invoice'] = $_invoice_size_data;
	$_cd_size_fn = json_encode($_cd_size_fn_data);

	$_cd_weight_data = array(
		'1' => $_cd_weight_1,
		'2' => $_cd_weight_2,
		'3' => $_cd_weight_3
	);
	$_cd_weight_fn = json_encode($_cd_weight_data);

	$_cd_sale_price = (int)str_replace(',','', $_cd_sale_price);

	if( $_cost_cal_kind == "중국주문" ){

		$_cd_cost_price_info_data = array(
			"주문종류" => "중국주문",
			"국가" => $_cd_national,
			"주문가" => $_cost_cal_price,
			"적용환율" => $_cost_cal_exchange_rate,
			"배송비" => $_cost_cal_delivery,
			"관세율" => $_cost_cal_tariff,
			"부대비용" => $_cost_cal_incidental_cost,
			"reg" => $_reg_d
		);

	}else{

		$_cd_cost_price_info_data = array(
			"VAT" => "포함",
			"국가" => $_cd_national,
			"환율" => $_ex_rate,
			"기준주문가" => $_oprice,
			"기준주문가코드" => $_oprice_key,
			"1kg배송비" => $_kg_p,
			"중량종류" => $_weight_mode,
			"중량" => $_weight,
			"관세" => $_tax,
			"부가세" => $_vat,
			"배송비" => $_delivery,
			"원전환" => $_op_won,
			"원가" => $_cost_p,
			"reg" => $_reg_d
		);

	}

	$_cd_cost_price_info = json_encode($_cd_cost_price_info_data, JSON_UNESCAPED_UNICODE);


	$query = "UPDATE "._DB_COMPARISON." SET 
		cd_national = '".$_cd_national."',
		cd_size_fn = '".$_cd_size_fn."',
		cd_weight_fn = '".$_cd_weight_fn."',
		cd_sale_price = '".$_cd_sale_price."',
		cd_cost_price = '".$_cost_p."',
		cd_cost_price_info = '".$_cd_cost_price_info."'
		WHERE CD_IDX = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 계산 원가 수정2
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "costSave" ){

/*
	$response = array('success' => false, 'msg' => '/'.$_cost_cal_kind.'/' );
	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;
*/

	$prd_data = sql_fetch_array(sql_query_error("select cd_size_fn  from "._DB_COMPARISON." WHERE CD_IDX = '".$_idx."' "));

	$_cd_size_fn_data = json_decode($prd_data['cd_size_fn'], true);

	// 팩킹 사이즈
	if( $_invoice_size_cbm_mode != "hand" ) $_invoice_size_cbm_mode = "auto";

	// cbm 계산기
	if( $_invoice_size_cbm_mode == "auto" && $_invoice_size_d > 0 && $_invoice_size_h > 0 && $_invoice_size_w > 0 ){
		$_cbm = round(($_invoice_size_d/1000) * ($_invoice_size_h/1000) * ($_invoice_size_w/1000),3);
	}else{
		$_cbm = $_invoice_size_cbm;
	}

	$_invoice_size_data = array(
		'W' => $_invoice_size_w,
		'H' => $_invoice_size_h,
		'D' => $_invoice_size_d,
		'cbm' => $_cbm,
		'cbm_mode' => $_invoice_size_cbm_mode
	);

	$_cd_size_fn_data['invoice'] = $_invoice_size_data;
	$_cd_size_fn = json_encode($_cd_size_fn_data);

	$_cd_weight_data = array(
		'1' => $_cd_weight_1,
		'2' => $_cd_weight_2,
		'3' => $_cd_weight_3
	);
	$_cd_weight_fn = json_encode($_cd_weight_data);

	$_cd_sale_price = (int)str_replace(',','', $_cd_sale_price);
	$_cd_cost_price = (int)str_replace(',','', $_cd_cost_price);

	if( $_cost_cal_kind == "중국주문" ){

		$_cd_cost_price_info_data = array(
			"VAT" => $_cd_cost_price_vat,
			"주문종류" => "중국주문",
			"국가" => $_cd_national,
			"주문가" => $_cost_cal_price,
			"기준주문가코드" => $_order_price_code,
			"적용환율" => $_cost_cal_exchange_rate,
			"배송비" => $_cost_cal_delivery,
			"관세율" => $_cost_cal_tariff,
			"부대비용" => $_cost_cal_incidental_cost,
			"reg" => $_reg_d
		);

	}elseif( $_cost_cal_kind == "일본주문" ){

		$_cd_cost_price_info_data = array(
			"VAT" => $_cd_cost_price_vat,
			"주문종류" => "일본주문",
			"국가" => $_cd_national,
			"주문가" => $_cost_cal_price,
			"기준주문가코드" => $_order_price_code,
			"중량종류" => $_weight_mode,
			"중량" => $_weight,
			"적용환율" => $_cost_cal_exchange_rate,
			"배송비" => $_cost_cal_delivery,
			"관세율" => $_cost_cal_tariff,
			"부대비용" => $_cost_cal_incidental_cost,
			"reg" => $_reg_d
		);
	}else{

		$_cd_cost_price_info_data = array(
			"VAT" => "포함",
			"국가" => $_cd_national,
			"환율" => $_ex_rate,
			"기준주문가" => $_oprice,
			"기준주문가코드" => $_oprice_key,
			"1kg배송비" => $_kg_p,
			"중량종류" => $_weight_mode,
			"중량" => $_weight,
			"관세" => $_tax,
			"부가세" => $_vat,
			"배송비" => $_delivery,
			"원전환" => $_op_won,
			"원가" => $_cost_p,
			"reg" => $_reg_d
		);

	}

	$_cd_cost_price_info = json_encode($_cd_cost_price_info_data, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE "._DB_COMPARISON." SET 
		cd_national = '".$_cd_national."',
		cd_size_fn = '".$_cd_size_fn."',
		cd_weight_fn = '".$_cd_weight_fn."',
		cd_sale_price = '".$_cd_sale_price."',
		cd_cost_price = '".$_cd_cost_price."',
		cd_cost_price_info = '".$_cd_cost_price_info."',
		cd_cost_price_memo = '".$_cd_cost_price_memo."'
		WHERE CD_IDX = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "prdDel" ){

	$_idx = $_POST['idx'] ?? "";

	$data = sql_fetch_array(sql_query_error("select CD_IMG from "._DB_COMPARISON." WHERE CD_IDX = '".$_idx."' "));
	$stock_data = sql_fetch_array(sql_query_error("select ps_idx from prd_stock where ps_prd_idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($stock_data)) {
		$stock_data = ['ps_idx' => ''];
	}

	if( !empty($stock_data['ps_idx'] ?? '') ){
		$response = array('success' => false, 'msg' => '재고코드가 생성되어 있어 삭제가 불가능 합니다.' );
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;
	}

	// 배열 검증
	if (!is_array($data)) {
		$data = ['CD_IMG' => ''];
	}

	$img_path = '/data/comparion/'.($data['CD_IMG'] ?? '');

	$is_file_exist = file_exists($docRoot.$img_path);

	if ($is_file_exist) {
		@unlink($docRoot.$img_path); //파일 삭제
	}

	sql_query_error("delete from "._DB_COMPARISON." where CD_IDX = '".$_idx."'");

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 세트상품 재고변경
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "prd3SetStock" ){

	$_idx = $_POST['idx'] ?? "";
	$_psu_mode = $_POST['psu_mode'] ?? "";
	$_psu_stock = $_POST['psu_stock'] ?? 0;
	$_psu_memo = $_POST['psu_memo'] ?? "";

	$data = sql_fetch_array(sql_query_error("select * from prd_set WHERE pset_idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['pset_goods' => '[]', 'pset_name' => '', 'pset_stock' => 0];
	}

	$_pset_goods = json_decode($data['pset_goods'] ?? '[]', true);

	// 배열 검증
	if (!is_array($_pset_goods)) {
		$_pset_goods = [];
	}

	/*
				<label><input type="radio" name="psu_mode" value="ibgo_plus"> 입고증가</label>
				<label><input type="radio" name="psu_mode" value="ibgo_minus"> 입고감소</label>
				&nbsp;|&nbsp;
				<label><input type="radio" name="psu_mode" value="plus" checked> 증가</label>
				<label><input type="radio" name="psu_mode" value="minus"> 감소</label>


	[{"idx":"2385","stock_idx":"1819"},{"idx":"1","stock_idx":"18"},{"idx":"50","stock_idx":"52"}]

	*/

	$_reg_data = array(
		"reg" => array(
			"mode" => "set_prd",
			"set_idx" => $_idx,
			"info" => $_reg_d
		)
	);

	$_reg = json_encode($_reg_data, JSON_UNESCAPED_UNICODE);


	//입고증가, 입고감소
	if( $_psu_mode == "ibgo_plus" || $_psu_mode == "ibgo_minus" ){
	
		foreach ( $_pset_goods as $key => $val ){

			$_ps_idx = $val['stock_idx'];

			$stock_data = sql_fetch_array(sql_query_error("select ps_stock from prd_stock where ps_idx = '".$_ps_idx."' "));

			//입고증가면 재고 감소 !!!!
			if( $_psu_mode == "ibgo_plus" ){

				$_prd_stock_count = $stock_data['ps_stock'] - $_psu_stock;
				$_query_ps_stock_text = " ps_stock = ps_stock - ".$_psu_stock.", ";
				$_psu_kind = "세트 입고증가";
				$_psu_memo = "세트 (".$data['pset_name'].") 입고증가";

			}elseif( $_psu_mode == "ibgo_minus" ){

				$_prd_stock_count = $stock_data['ps_stock'] + $_psu_stock;
				$_query_ps_stock_text = " ps_stock = ps_stock + ".$_psu_stock.", ";
				$_psu_kind = "세트 입고감소";
				$_psu_memo = "세트 (".$data['pset_name'].") 입고감소";
			}


			sql_query_error("UPDATE prd_stock set 
				".$_query_ps_stock_text." 
				ps_update_date = '".$action_time."'  
				WHERE ps_idx = '".$_ps_idx."' " );

			if( $_psu_mode == "ibgo_plus" ){
				$_psu_mode_text = "minus";
			}elseif( $_psu_mode == "ibgo_minus" ){
				$_psu_mode_text = "plus";
			}

			$query = "INSERT prd_stock_unit SET
				psu_stock_idx = '".$_ps_idx."',
				psu_day = '".$action_time_ymd."',
				psu_mode = '".$_psu_mode_text."',
				psu_qry = '".$_psu_stock."',
				psu_stock = '".$_prd_stock_count."',
				psu_kind = '".$_psu_kind."',
				psu_memo = '".$_psu_memo."',
				psu_id = '".$_ad_id."',
				psu_date = '".$check_time ."',
				reg = '".$_reg."' ";
			sql_query_error($query);

		} //foreach END

	}


	if( $_psu_mode == "plus" || $_psu_mode == "ibgo_plus" ){
		$_query1 = " pset_stock = pset_stock + ".$_psu_stock." ";
		$_all_psu_stock = $data['pset_stock'] + $_psu_stock;
		if( !$_psu_memo ){
			$_psu_memo = "세트상품 수정 증가";
		}

	}elseif( $_psu_mode == "minus" || $_psu_mode == "ibgo_minus" ){
		$_query1 = " pset_stock = pset_stock - ".$_psu_stock." ";
		$_all_psu_stock = $data['pset_stock'] - $_psu_stock;
		if( !$_psu_memo ){
			$_psu_memo = "세트상품 수정 감소";
		}
	}

	$query = "UPDATE prd_set set
		".$_query1."
		WHERE pset_idx = '".$_idx."' ";
	sql_query_error($query);

	if( $_psu_mode == "ibgo_plus" ){
		$_psu_mode = "plus";
	}elseif( $_psu_mode == "ibgo_minus" ){
		$_psu_mode = "minus";
	}

	$query = "insert prd_stock_unit set
		psu_rmode = 'set',
		psu_stock_idx = '".$_idx."',
		psu_day = '".$action_time_ymd."',
		psu_mode = '".$_psu_mode."',
		psu_qry = '".$_psu_stock."',
		psu_stock = '".$_all_psu_stock."',
		psu_memo = '".$_psu_memo."',
		psu_id = '".$_sess_id."',
		psu_date = '".$check_time."' ";
	sql_query_error($query);


	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 재고변경 등록
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "stock_info_reg" ){

	$_ps_idx = $_POST['ps_idx'] ?? "";
	$_stock_mode = $_POST['stock_mode'] ?? "";
	$_stock_qty = $_POST['stock_qty'] ?? 0;
	$_stock_day = $_POST['stock_day'] ?? "";
	$_stock_kind = $_POST['stock_kind'] ?? "";
	$_stock_memo = $_POST['stock_memo'] ?? "";

	if( $_stock_qty < 0 ){

		$response = array('success' => false, 'msg' => '수량을 입력해 주세요.' );
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;

	}
	
	$stock_data = sql_fetch_array(sql_query_error("select * from prd_stock where ps_idx = '".$_ps_idx."' "));

	// 배열 검증
	if (!is_array($stock_data)) {
		$stock_data = ['ps_stock' => 0, 'ps_stock_hold' => 0];
	}

	$_prd_stock_count = $stock_data['ps_stock'] ?? 0;
	$_prd_stock_hold_count = $stock_data['ps_stock_hold'] ?? 0;

	$_reg_data = array(
		"reg" => array(
			"mode" => "prd_info",
			"info" => $_reg_d
		)
	);
	$_reg = json_encode($_reg_data, JSON_UNESCAPED_UNICODE);


	//--------------------------------------------------------------------------------------------------------------------
	// 입고
	if( $_stock_mode == "plus" ){

		$_prd_stock_count = $stock_data['ps_stock'] + $_stock_qty;

		$update = "UPDATE prd_stock set ps_stock = ps_stock + ".$_stock_qty.", ";			

		if( $_stock_kind == "신규입고" ){
			$update .= " ps_stock_all = ps_stock_all + ".$_stock_qty.", ";
		}

		$update .= " ps_update_date = '".$action_time."' WHERE ps_idx = '".$_ps_idx."' ";
		sql_query_error($update);

		$query = "INSERT prd_stock_unit SET
			psu_stock_idx = '".$_ps_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'plus',
			psu_qry = '".$_stock_qty."',
			psu_stock = '".$_prd_stock_count."',
			psu_kind = '".$_stock_kind."',
			psu_memo = '".$_stock_memo."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."',
			reg = '".$_reg."' ";
		sql_query_error($query);

	//--------------------------------------------------------------------------------------------------------------------
	// 출고
	}elseif( $_stock_mode == "minus" ){

		$_prd_stock_count = $stock_data['ps_stock'] - $_stock_qty;
		sql_query_error("UPDATE prd_stock set ps_stock = ps_stock - ".$_stock_qty.", ps_update_date = '".$action_time."'  WHERE ps_idx = '".$_ps_idx."' " );

		$query = "INSERT prd_stock_unit SET
			psu_stock_idx = '".$_ps_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'minus',
			psu_qry = '".$_stock_qty."',
			psu_stock = '".$_prd_stock_count."',
			psu_kind = '".$_stock_kind."',
			psu_memo = '".$_stock_memo."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."',
			reg = '".$_reg."' ";
		sql_query_error($query);

	//--------------------------------------------------------------------------------------------------------------------
	// 보류 전환
	}elseif( $_stock_mode == "to_hold" ){

		$_prd_stock_count = $stock_data['ps_stock'] - $_stock_qty;

		sql_query_error("UPDATE prd_stock set 
			ps_stock = ps_stock - ".$_stock_qty.", 
			ps_stock_hold = ps_stock_hold + ".$_stock_qty.", 
			ps_update_date = '".$action_time."'  WHERE ps_idx = '".$_ps_idx."' " );

		//현재 재고 빼기 기록 
		$query = "INSERT prd_stock_unit SET
			psu_stock_idx = '".$_ps_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'minus_to_hold',
			psu_qry = '".$_stock_qty."',
			psu_stock = '".$_prd_stock_count."',
			psu_kind = '".$_stock_kind."',
			psu_memo = '".$_stock_memo."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."',
			reg = '".$_reg."' ";
		sql_query_error($query);

		//보류 재고 더하기 기록 
		$_prd_stock_hold_count = $stock_data['ps_stock_hold'] + $_stock_qty;

		$query = "INSERT prd_stock_unit SET
			psu_stock_idx = '".$_ps_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'to_hold',
			psu_qry = '".$_stock_qty."',
			psu_stock = '".$_prd_stock_hold_count."',
			psu_kind = '".$_stock_kind."',
			psu_memo = '".$_stock_memo."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."',
			reg = '".$_reg."' ";
		sql_query_error($query);

	//--------------------------------------------------------------------------------------------------------------------
	// 재고 전환
	}elseif( $_stock_mode == "to_stock" ){

		$_prd_stock_count = $stock_data['ps_stock'] + $_stock_qty;

		sql_query_error("UPDATE prd_stock set 
			ps_stock = ps_stock + ".$_stock_qty.", 
			ps_stock_hold = ps_stock_hold - ".$_stock_qty.", 
			ps_update_date = '".$action_time."'  WHERE ps_idx = '".$_ps_idx."' " );

		//현재 재고 더하기 기록 
		$query = "INSERT prd_stock_unit SET
			psu_stock_idx = '".$_ps_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'plus_to_stock',
			psu_qry = '".$_stock_qty."',
			psu_stock = '".$_prd_stock_count."',
			psu_kind = '".$_stock_kind."',
			psu_memo = '".$_stock_memo."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."',
			reg = '".$_reg."' ";
		sql_query_error($query);

		//보류 재고 빼기 기록 
		$_prd_stock_hold_count = $stock_data['ps_stock_hold'] - $_stock_qty;

		$query = "INSERT prd_stock_unit SET
			psu_stock_idx = '".$_ps_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'to_stock',
			psu_qry = '".$_stock_qty."',
			psu_stock = '".$_prd_stock_hold_count."',
			psu_kind = '".$_stock_kind."',
			psu_memo = '".$_stock_memo."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."',
			reg = '".$_reg."' ";
		sql_query_error($query);

	//--------------------------------------------------------------------------------------------------------------------
	// 보류재고 입고
	}elseif( $_stock_mode == "plus_hold" ){
	
		$_prd_stock_hold_count = $stock_data['ps_stock_hold'] + $_stock_qty;
		sql_query_error("UPDATE prd_stock set ps_stock_hold = ps_stock_hold + ".$_stock_qty.", ps_update_date = '".$action_time."'  WHERE ps_idx = '".$_ps_idx."' " );

		$query = "INSERT prd_stock_unit SET
			psu_stock_idx = '".$_ps_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'plus_hold',
			psu_qry = '".$_stock_qty."',
			psu_stock = '".$_prd_stock_hold_count."',
			psu_kind = '".$_stock_kind."',
			psu_memo = '".$_stock_memo."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."',
			reg = '".$_reg."' ";
		sql_query_error($query);

	//--------------------------------------------------------------------------------------------------------------------
	// 보류재고 출고
	}elseif( $_stock_mode == "minus_hold" ){

		$_prd_stock_hold_count = $stock_data['ps_stock_hold'] - $_stock_qty;
		sql_query_error("UPDATE prd_stock set ps_stock_hold = ps_stock_hold - ".$_stock_qty.", ps_update_date = '".$action_time."'  WHERE ps_idx = '".$_ps_idx."' " );

		$query = "INSERT prd_stock_unit SET
			psu_stock_idx = '".$_ps_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'minus_hold',
			psu_qry = '".$_stock_qty."',
			psu_stock = '".$_prd_stock_hold_count."',
			psu_kind = '".$_stock_kind."',
			psu_memo = '".$_stock_memo."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."',
			reg = '".$_reg."' ";
		sql_query_error($query);

	}

/*
	$query = "INSERT prd_stock_unit SET
		psu_stock_idx = '".$_ps_idx."',
		psu_day = '".$_stock_day."',
		psu_mode = '".$_stock_mode."',
		psu_qry = '".$_stock_qty."',
		psu_stock = '".$_prd_stock_count."',
		psu_kind = '".$_stock_kind."',
		psu_memo = '".$_stock_memo."',
		psu_id = '".$_ad_id."',
		psu_date = '".$check_time ."',
		reg = '".$_reg."' ";
	sql_query_error($query);
*/

	$response = array('success' => true, 'msg' => '완료', 'stock' => $_prd_stock_count, 'stock_hold' => $_prd_stock_hold_count );
	
////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 유닛 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "stock_info_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_stock_kind = $_POST['stock_kind'] ?? "";
	$_stock_memo = $_POST['stock_memo'] ?? "";

	$query = "UPDATE prd_stock_unit SET 
		psu_kind = '".$_stock_kind."',
		psu_memo = '".$_stock_memo."'
		WHERE psu_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 재고코드 생성
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "new_stock_psidx" ){

	$_prd_idx = $_POST['prd_idx'] ?? "";

	//재고테이블에 해당상품 있는지 확인
	$stock_data = sql_fetch_array(sql_query_error("select ps_idx from prd_stock WHERE ps_prd_idx = '".$_prd_idx."' "));

	// 배열 검증
	if (!is_array($stock_data)) {
		$stock_data = ['ps_idx' => ''];
	}

	if( !empty($stock_data['ps_idx'] ?? '') ){

		$response = array('success' => false, 'msg' => '이미 재고코드가 있습니다.' );
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;

	}else{ //없다면 등록

		$query = "insert prd_stock set
			ps_prd_idx = '".$_prd_idx."',
			ps_update_date = '".$action_time."' ";
		sql_query_error($query);

	}

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 오나DB 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "onadb_prd_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_cd_site_show = $_POST['cd_site_show'] ?? "";
	$_cd_tier = $_POST['cd_tier'] ?? "";
	$_c19 = $_POST['c19'] ?? "";
	$_c19_package = $_POST['c19_package'] ?? "";
	$_ps_idx = $_POST['ps_idx'] ?? "";
	$_ps_grade = $_POST['ps_grade'] ?? "";

	$query = "update "._DB_COMPARISON." set
		cd_site_show = '".$_cd_site_show."',
		cd_tier = '".$_cd_tier."'
		where CD_IDX = '".$_idx."' ";
	sql_query_error($query);

	$query = "update prd_contents set
		c19 = '".$_c19."',
		c19_package = '".$_c19_package."'
		where cd_idx = '".$_idx."' ";
	sql_query_error($query);

	$prd_score = sql_fetch_array(sql_query_error("select * from prd_score WHERE ps_idx = '".$_ps_idx."'  "));

	// 배열 검증
	if (!is_array($prd_score)) {
		$prd_score = ['ps_grade' => '', 'ps_grade_data' => '{}'];
	}

	//일단 기존 평점과 변경평점이 다를경우 실행하자
	if( ($prd_score['ps_grade'] ?? '') != $_ps_grade ){
	
		$_ps_grade_data_arr = json_decode($prd_score['ps_grade_data'] ?? '{}', true);
		
		// 배열 검증
		if (!is_array($_ps_grade_data_arr)) {
			$_ps_grade_data_arr = [];
		}

		$_ps_grade_data_arr['last_modify_mode'] = "ad_modify";
		$_ps_grade_data_arr['ad_modify'] = array(
			"before" => $prd_score['ps_grade'] ?? "",
			"after" => $_ps_grade,
			"reg" => $_reg_d
		);
		
		$_ps_grade_data = json_encode($_ps_grade_data_arr, JSON_UNESCAPED_UNICODE);

		$query = "update prd_score set
			ps_grade = '".$_ps_grade."',
			ps_grade_data = '".$_ps_grade_data."'
			where ps_idx = '".$_ps_idx."' ";
		sql_query_error($query);

	}


	$response = array('success' => true, 'msg' => '완료', 'a_mode' => $_a_mode, 'idx' => $_idx );

////////////////////////////////////////////////////////////////////////////////////////////////
// 오나DB 개인평점 리셋
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "onadb_gradeReset" ){

	$_prd_idx = $_POST['prd_idx'] ?? "";

	$query = "select 
		COUNT( * ) as grade_count,
		AVG( pc_grade ) as grade_avg,
		SUM( pc_grade ) as grade_sum
		from prd_comment WHERE pc_pd_idx = '".$_prd_idx."' ";
	$sum = sql_fetch_array(sql_query_error($query));

	// 배열 검증
	if (!is_array($sum)) {
		$sum = ['grade_count' => 0, 'grade_avg' => 0, 'grade_sum' => 0];
	}

	$prd_score = sql_fetch_array(sql_query_error("select * from prd_score WHERE ps_pd_idx = '".$_prd_idx."' AND ps_mode = 'total'  "));

	// 배열 검증
	if (!is_array($prd_score)) {
		$prd_score = ['ps_idx' => '', 'ps_grade' => 0, 'ps_grade_data' => '{}'];
	}

	if( !$prd_score['ps_idx'] ){
		$query = "insert prd_score set
			ps_pd_idx = '".$_prd_idx."',
			ps_mode = 'total' ";
		sql_query_error($query);
		$prd_score = sql_fetch_array(sql_query_error("select * from prd_score WHERE ps_pd_idx = '".$_prd_idx."' AND ps_mode = 'total'  "));
	}

	if( !$prd_score['ps_grade'] ) $prd_score['ps_grade'] = 0;
	//$_msg = $prd_score['ps_grade']."/".$sum['grade_avg'];

	//일단 기존 평점과 변경평점이 다를경우 실행하자
	if( $prd_score['ps_grade'] != $sum['grade_avg'] ){
		
		/*
		$response = array('success' => false, 'msg' => $_msg );
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;
		*/

		$_ps_grade_data_arr = json_decode($prd_score['ps_grade_data'], true);
		
		$_ps_grade_data_arr['last_modify_mode'] = "gradeReset";
		$_ps_grade_data_arr['gradeReset'] = array(
			"before" => $prd_score['ps_grade'],
			"after" => $sum['grade_avg'],
			"reg" => $_reg_d
		);
		
		$_ps_grade_data = json_encode($_ps_grade_data_arr, JSON_UNESCAPED_UNICODE);

		$query = "UPDATE prd_score SET 
			ps_grade = '".$sum['grade_avg']."',
			ps_grade_count = '".$sum['grade_count']."',
			ps_grade_total = '".$sum['grade_sum']."',
			ps_grade_data = '".$_ps_grade_data."'
			WHERE ps_pd_idx = '".$_prd_idx."' AND ps_mode = 'total' ";
		sql_query_error($query);

	}

	$response = array('success' => true, 'msg' => '완료'  );

////////////////////////////////////////////////////////////////////////////////////////////////
// 오나DB 한줄평 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "onadb_commModify" ){

	$_idx = $_POST['idx'] ?? "";
	$_pc_score_mode = $_POST['pc_score_mode'] ?? "";
	$_pc_grade = $_POST['pc_grade'] ?? "";

	$query = "UPDATE prd_comment SET 
		pc_score_mode = '".$_pc_score_mode."',
		pc_grade = '".$_pc_grade."'
		WHERE pc_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 오나DB 한줄평
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "onadb_commWrite" ){

	$_pd_idx = $_POST['pd_idx'] ?? "";
	$_pc_grade = $_POST['pc_grade'] ?? 0;
	$_pc_score_mode = $_POST['pc_score_mode'] ?? "";
	$_ps_grade = $_POST['ps_grade'] ?? 0;
	$body = $_POST['body'] ?? "";
	$_name = $_POST['name'] ?? "";
	$_pw = $_POST['pw'] ?? "";

	$_ps_ym = date("Y-m");

	$data_total = sql_fetch_array(sql_query_error("select * from prd_score where ps_pd_idx = '".$_pd_idx."' AND ps_mode = 'total' "));
	$data_month = sql_fetch_array(sql_query_error("select * from prd_score where ps_pd_idx = '".$_pd_idx."' AND ps_mode = 'month' AND ps_ym = '".$_ps_ym."'  "));

	// 배열 검증
	if (!is_array($data_total)) {
		$data_total = ['ps_idx' => '', 'ps_grade_count' => 0, 'ps_grade_total' => 0, 'ps_score' => '{}', 'ps_grade' => 0];
	}
	if (!is_array($data_month)) {
		$data_month = ['ps_idx' => '', 'ps_grade_count' => 0, 'ps_grade_total' => 0, 'ps_score' => '{}', 'ps_grade' => 0];
	}

	//전체평점
	if( !$data_total['ps_idx'] ){

		sql_query_error("insert prd_score set ps_pd_idx = '".$_pd_idx."',  ps_mode = 'total' ");
		$_ps_idx = mysqli_insert_id($connect);

		$_ps_grade_count = 1;
		$_ps_grade_total = $_pc_grade;
		$_ps_grade = $_pc_grade;

		for ($i=0; $i<count($_gva_koedge_onadb_score_option); $i++){ 
			$i2 = $i + 1;
			${'_ps_score_'.$i2.'_count'} = 0;
			${'_ps_score_'.$i2.'_score_sum'} = 0;
		}

	}else{

		$_ps_idx = $data_total['ps_idx'];
		$_ps_grade_count = $data_total['ps_grade_count'] + 1;
		$_ps_grade_total = $data_total['ps_grade_total'] + $_pc_grade;
		$_ps_grade = round(($_ps_grade_total/$_ps_grade_count),1);

		$_ps_score_data = json_decode($data_total['ps_score'], true);

		for ($i=0; $i<count($_gva_koedge_onadb_score_option); $i++){ 
			$i2 = $i + 1;
			${'_ps_score_'.$i2.'_count'} = $_ps_score_data['score'][$i2]['count'];
			${'_ps_score_'.$i2.'_score_sum'} = $_ps_score_data['score'][$i2]['score_sum'];
		}

	}


	//이번달 평점
	if( !$data_month['ps_idx'] ){

		sql_query_error("insert prd_score set ps_pd_idx = '".$_pd_idx."',  ps_mode = 'month',  ps_ym = '".$_ps_ym."'  ");
		$_ps_idx_month = mysqli_insert_id($connect);

		$_ps_grade_count_month = 1;
		$_ps_grade_total_month = $_pc_grade;
		$_ps_grade_month = $_pc_grade;

		for ($i=0; $i<count($_gva_koedge_onadb_score_option); $i++){ 
			$i2 = $i + 1;
			${'_ps_month_score_'.$i2.'_count'} = 0;
			${'_ps_month_score_'.$i2.'_score_sum'} = 0;
		}

	}else{

		$_ps_idx_month = $data_month['ps_idx'];
		$_ps_grade_count_month = $data_month['ps_grade_count'] + 1;
		$_ps_grade_total_month = $data_month['ps_grade_total'] + $_pc_grade;
		$_ps_grade_month = round(($_ps_grade_total_month/$_ps_grade_count_month),1);

		$_ps_score_data = json_decode($data_month['ps_score'], true);

		for ($i=0; $i<count($_gva_koedge_onadb_score_option); $i++){ 
			$i2 = $i + 1;
			${'_ps_month_score_'.$i2.'_count'} = $_ps_score_data['score'][$i2]['count'];
			${'_ps_month_score_'.$i2.'_score_sum'} = $_ps_score_data['score'][$i2]['score_sum'];
		}

	}


	if ( substr_count($body, '&#') > 50 ) {
		$response = array(
			'success' => false,
			'msg' => '내용에 올바르지 않은 코드가 다수 포함되어 있습니다.'
		);
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;
	}

	$_pc_body = mysqli_real_escape_string($connect, $body);

	if( !$_name) $_name = "익명";

	//--------------------------------------------------------------------------------------
	// 사용자
	if( $_pc_score_mode == "after" ){
	
		$_ps_total_score_avg = 0;
		$_ps_month_total_score_avg = 0;
		$_pc_score_sum = 0;

		for ($i=1; $i<=count($_gva_koedge_onadb_score_option); $i++){ 
			
			$im = $i - 1;
			$_option_name = $_gva_koedge_onadb_score_option[$im];

			// pc
			$_pc_ary[$i] = array(
				"name" => $_option_name,
				"score" => ${'_pc_score_'.$i}
			);

			$_pc_score_sum = $_pc_score_sum + ${'_pc_score_'.$i};

			//--------------------------------------------------------------------------------------
			$_this_count = (${'_ps_score_'.$i.'_count'} + 1);
			$_this_score_sum = (${'_ps_score_'.$i.'_score_sum'} + ${'_pc_score_'.$i});
			$_this_score_avg = round(($_this_score_sum / $_this_count),1);

			//$_ps_total_score_sum = $_ps_total_score_sum + $_this_score_sum;
			$_ps_total_score_avg = $_ps_total_score_avg + $_this_score_avg;

			// ps
			$_ps_ary[$i] = array(
				"name" => $_option_name,
				"count" => $_this_count,
				"score_sum" => $_this_score_sum,
				"score_avg" => $_this_score_avg
			);

			//--------------------------------------------------------------------------------------
			$_this_count = (${'_ps_month_score_'.$i.'_count'} + 1);
			$_this_score_sum = (${'_ps_month_score_'.$i.'_score_sum'} + ${'_pc_score_'.$i});
			$_this_score_avg = round(($_this_score_sum / $_this_count),1);

			//$_ps_month_total_score_sum = $_ps_month_total_score_sum + $_this_score_sum;
			$_ps_month_total_score_avg = $_ps_month_total_score_avg + $_this_score_avg;

			// ps_month
			$_ps_month_ary[$i] = array(
				"name" => $_option_name,
				"count" => $_this_count,
				"score_sum" => $_this_score_sum,
				"score_avg" => $_this_score_avg
			);

		} //for END

		$_pc_total = round(( $_pc_score_sum / count($_gva_koedge_onadb_score_option) ),1);
		$_ps_total = round(( $_ps_total_score_avg / count($_gva_koedge_onadb_score_option) ),1);
		$_ps_month_total = round(( $_ps_month_total_score_avg / count($_gva_koedge_onadb_score_option) ),1);

		$_pc_score_data = array(
			"score" => $_pc_ary,
			"score_sum" => $_pc_score_sum,
			"score_avg" => $_pc_total
		);
		$_pc_score = json_encode($_pc_score_data, JSON_UNESCAPED_UNICODE);

		$_ps_score_data = array(
			"score" => $_ps_ary,
			"total" => $_ps_total
		);
		$_ps_score = json_encode($_ps_score_data, JSON_UNESCAPED_UNICODE);
	
		$_ps_score_month_data = array(
			"score" => $_ps_month_ary,
			"total" => $_ps_month_total
		);
		$_ps_score_month = json_encode($_ps_score_month_data, JSON_UNESCAPED_UNICODE);


	}else{

		$_ps_score = $data_total['ps_score'];
		$_ps_score_month = $_ps_month_total['ps_score'];

	}


	//----------------------------------------------------------------------------------------------------------------
	$_ps_grade_data_arr['last_modify_mode'] = "admin_reg";
	$_ps_grade_data_arr['admin_reg'] = array(
		"before" => $data_total['ps_grade'],
		"after" => $_ps_grade,
		"reg" => $_reg_d
	);
	
	$_ps_grade_data = json_encode($_ps_grade_data_arr, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE prd_score SET 
		ps_score = '".$_ps_score."',
		ps_count = ps_count + 1,
		ps_grade = '".$_ps_grade."',
		ps_grade_count = '".$_ps_grade_count."',
		ps_grade_total = '".$_ps_grade_total."',
		ps_grade_data = '".$_ps_grade_data."'
		WHERE ps_idx = '".$_ps_idx."' ";
	sql_query_error($query);

	//----------------------------------------------------------------------------------------------------------------
	$_ps_grade_data_arr['last_modify_mode'] = "admin_reg";
	$_ps_grade_data_arr['admin_reg'] = array(
		"before" => $data_month['ps_grade'],
		"after" => $_ps_grade,
		"reg" => $_reg_d
	);
	
	$_ps_grade_data = json_encode($_ps_grade_data_arr, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE prd_score SET 
		ps_score = '".$_ps_score_month."',
		ps_count = ps_count + 1,
		ps_grade = '".$_ps_grade_month."',
		ps_grade_count = '".$_ps_grade_count_month."',
		ps_grade_total = '".$_ps_grade_total_month."',
		ps_grade_data = '".$_ps_grade_data."'
		WHERE ps_idx = '".$_ps_idx_month."' ";
	sql_query_error($query);

	$_pc_reg_info_data = array(
		"name" => $_name,
		"pw" => $_pw,
		"ip" => $check_ip,
		"domain" => $check_domain,
		"device" => $check_device
	);

	$_pc_reg_info = json_encode($_pc_reg_info_data, JSON_UNESCAPED_UNICODE);

	$_pc_reg_mode = "AD";

	$query = "insert prd_comment set
		pc_kind = 'onadb',
		pc_pd_idx = '".$_pd_idx."',
		pc_user_idx = '',
		pc_reg_info = '".$_pc_reg_info."',
		pc_score = '".$_pc_score."',
		pc_score_mode = '".$_pc_score_mode."',
		pc_grade = '".$_pc_grade."',
		pc_body = '".$_pc_body."',
		pc_category = 'ONAHOLE',
		pc_reg_date = '".$action_time."',
		pc_reg_mode = '".$_pc_reg_mode."',
		pc_ip = '".$check_ip."' ";
	sql_query_error($query);


	$response = array('success' => true, 'msg' => '완료'  );

////////////////////////////////////////////////////////////////////////////////////////////////
// 오나DB 코멘트 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "onadb_commentDel" ){

	$_idx = $_POST['idx'] ?? "";

	sql_query_error("delete from prd_comment where pc_idx = '".$_idx."'");
	$response = array('success' => true, 'msg' => '완료' );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>