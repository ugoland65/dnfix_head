<?

	// 변수 초기화
	$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";

	$_reg_d = array( "date" => $action_time, "idx" => $_ad_idx, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );

////////////////////////////////////////////////////////////////////////////////////////////////
// 브랜드 등록
if( $_a_mode == "brand_reg" ){

	$_bd_name = $_POST['bd_name'] ?? "";
	$_bd_name_en = $_POST['bd_name_en'] ?? "";
	$_bd_showdang_active = $_POST['bd_showdang_active'] ?? "N";
	$_bd_onadb_active = $_POST['bd_onadb_active'] ?? "N";
	$_bd_active = $_POST['bd_active'] ?? "N";
	$_bd_list_active = $_POST['bd_list_active'] ?? "N";
	$_bd_domain = $_POST['bd_domain'] ?? "";

	$query = "insert "._DB_BRAND." set
		BD_NAME = '".$_bd_name."',
		BD_NAME_EN = '".$_bd_name_en."',
		bd_showdang_active = '".$_bd_showdang_active."',
		bd_onadb_active = '".$_bd_onadb_active."',
		BD_ACTIVE = '".$_bd_active."',
		BD_LIST_ACTIVE = '".$_bd_list_active."',
		BD_DOMAIN = '".$_bd_domain."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 브랜드 수정
}elseif( $_a_mode == "brand_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_bd_name = $_POST['bd_name'] ?? "";
	$_bd_name_en = $_POST['bd_name_en'] ?? "";
	$_bd_logo = $_POST['bd_logo'] ?? "";
	$_bd_showdang_active = $_POST['bd_showdang_active'] ?? "N";
	$_bd_onadb_active = $_POST['bd_onadb_active'] ?? "N";
	$_bd_active = $_POST['bd_active'] ?? "N";
	$_bd_list_active = $_POST['bd_list_active'] ?? "N";
	$_bd_domain = $_POST['bd_domain'] ?? "";

	$_uploads_dir = "../data/brand_logo";
	$_save_file_name = "brand_logo_".$_ad_idx."_".time();

	// 파일이 있을경우 ---------------------
	if ( !empty($_FILES['logo_file']['name'] ?? '') ) {
		$_bd_logo = imgUpload($_FILES['logo_file'], $_uploads_dir, $_save_file_name, "", "");
	}

	$query = "UPDATE "._DB_BRAND." SET 
		BD_NAME = '".$_bd_name."',
		BD_NAME_EN = '".$_bd_name_en."',
		BD_LOGO = '".$_bd_logo."',
		bd_showdang_active = '".$_bd_showdang_active."',
		bd_onadb_active = '".$_bd_onadb_active."',
		BD_ACTIVE = '".$_bd_active."',
		BD_LIST_ACTIVE = '".$_bd_list_active."',
		BD_DOMAIN = '".$_bd_domain."'
		WHERE BD_IDX = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 브랜드 삭제
}elseif( $_a_mode == "brand_del" ){

	$_idx = $_POST['idx'] ?? "";

	$total_count = sql_counter(_DB_BRAND, " where CD_BRAND_IDX = '".$_idx."' ");

	if( $total_count > 0 ){
		
		$response = array('success' => false, 'msg' => '등록된 상품이 있을경우 삭제불가!' );
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;

	}

	$data = sql_fetch_array(sql_query_error("select bd_showdang_active from "._DB_BRAND." WHERE BD_IDX = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = ['bd_showdang_active' => ''];
	}

	if( ($data['bd_showdang_active'] ?? '') == "Y" ){

		$response = array('success' => false, 'msg' => '쑈당몰 노출상태인 브랜드는 삭제불가!' );
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;

	}

	sql_query_error("delete from "._DB_BRAND." where BD_IDX = '".$_idx."' ");
	
	$response = array('success' => true, 'msg' => '완료' );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>