<?
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
	$_bd_onadb_sort_num = $_POST['bd_onadb_sort_num'] ?? 0;
	$_bd_active = $_POST['bd_active'] ?? "N";
	$_bd_list_active = $_POST['bd_list_active'] ?? "N";
	$_bd_domain = $_POST['bd_domain'] ?? "";
	$_bd_md_idx = $_POST['bd_md_idx'] ?? 0;
	$_bd_code = $_POST['bd_code'] ?? "";
	$_bd_name_group = $_POST['bd_name_group'] ?? "";
	$_bd_name_en_group = $_POST['bd_name_en_group'] ?? "";
	$_bd_introduce = $_POST['bd_introduce'] ?? "";
	$_bd_sort = $_POST['bd_sort'] ?? 0;
	$_bd_kind_code = $_POST['bd_kind_code'] ?? "";
	$_bd_token = $_POST['bd_token'] ?? "";
	$_bd_cate_no = $_POST['bd_cate_no'] ?? 0;
	$_bd_matching_cate = $_POST['bd_matching_cate'] ?? "";
	$_bd_matching_brand = $_POST['bd_matching_brand'] ?? "";
	$_bd_api_info = $_POST['bd_api_info'] ?? "";
	$_bd_api_introduce = $_POST['bd_api_introduce'] ?? "";
	$_bd_kind = $_POST['bd_kind'] ?? "";

	$query = "insert "._DB_BRAND." set
		BD_NAME = '".$_bd_name."',
		BD_NAME_EN = '".$_bd_name_en."',
		BD_LOGO = '',
		bd_showdang_active = '".$_bd_showdang_active."',
		bd_onadb_active = '".$_bd_onadb_active."',
		bd_onadb_sort_num = '".$_bd_onadb_sort_num."',
		BD_ACTIVE = '".$_bd_active."',
		BD_LIST_ACTIVE = '".$_bd_list_active."',
		BD_DOMAIN = '".$_bd_domain."',
		BD_MD_IDX = '".$_bd_md_idx."',
		BD_CODE = '".$_bd_code."',
		BD_NAME_GROUP = '".$_bd_name_group."',
		BD_NAME_EN_GROUP = '".$_bd_name_en_group."',
		BD_INTRODUCE = '".$_bd_introduce."',
		BD_SORT = '".$_bd_sort."',
		BD_KIND_CODE = '".$_bd_kind_code."',
		BD_TOKEN = '".$_bd_token."',
		bd_cate_no = '".$_bd_cate_no."',
		bd_matching_cate = '".$_bd_matching_cate."',
		bd_matching_brand = '".$_bd_matching_brand."',
		bd_api_info = '".$_bd_api_info."',
		bd_api_introduce = '".$_bd_api_introduce."',
		bd_kind = '".$_bd_kind."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 브랜드 수정
}elseif( $_a_mode == "brand_modify" ){

	$_idx = $_POST['idx'] ?? "";
	
	// 기존 데이터 조회
	$data = sql_fetch_array(sql_query_error("select BD_LOGO from "._DB_BRAND." WHERE BD_IDX = '".$_idx."' "));
	
	// 배열 검증
	if (!is_array($data)) {
		$data = ['BD_LOGO' => ''];
	}
	
	$_bd_name = $_POST['bd_name'] ?? "";
	$_bd_name_en = $_POST['bd_name_en'] ?? "";
	$_bd_logo = $data['BD_LOGO'] ?? "";
	$_bd_showdang_active = $_POST['bd_showdang_active'] ?? "N";
	$_bd_onadb_active = $_POST['bd_onadb_active'] ?? "N";
	$_bd_onadb_sort_num = $_POST['bd_onadb_sort_num'] ?? 0;
	$_bd_active = $_POST['bd_active'] ?? "N";
	$_bd_list_active = $_POST['bd_list_active'] ?? "N";
	$_bd_domain = $_POST['bd_domain'] ?? "";
	$_bd_md_idx = $_POST['bd_md_idx'] ?? 0;
	$_bd_code = $_POST['bd_code'] ?? "";
	$_bd_name_group = $_POST['bd_name_group'] ?? "";
	$_bd_name_en_group = $_POST['bd_name_en_group'] ?? "";
	$_bd_introduce = $_POST['bd_introduce'] ?? "";
	$_bd_sort = $_POST['bd_sort'] ?? 0;
	$_bd_kind_code = $_POST['bd_kind_code'] ?? "";
	$_bd_token = $_POST['bd_token'] ?? "";
	$_bd_cate_no = $_POST['bd_cate_no'] ?? 0;
	$_bd_matching_cate = $_POST['bd_matching_cate'] ?? "";
	$_bd_matching_brand = $_POST['bd_matching_brand'] ?? "";
	$_bd_api_info = $_POST['bd_api_info'] ?? "";
	$_bd_api_introduce = $_POST['bd_api_introduce'] ?? "";
	$_bd_kind = $_POST['bd_kind'] ?? "";

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
		bd_onadb_sort_num = '".$_bd_onadb_sort_num."',
		BD_ACTIVE = '".$_bd_active."',
		BD_LIST_ACTIVE = '".$_bd_list_active."',
		BD_DOMAIN = '".$_bd_domain."',
		BD_MD_IDX = '".$_bd_md_idx."',
		BD_CODE = '".$_bd_code."',
		BD_NAME_GROUP = '".$_bd_name_group."',
		BD_NAME_EN_GROUP = '".$_bd_name_en_group."',
		BD_INTRODUCE = '".$_bd_introduce."',
		BD_SORT = '".$_bd_sort."',
		BD_KIND_CODE = '".$_bd_kind_code."',
		BD_TOKEN = '".$_bd_token."',
		bd_cate_no = '".$_bd_cate_no."',
		bd_matching_cate = '".$_bd_matching_cate."',
		bd_matching_brand = '".$_bd_matching_brand."',
		bd_api_info = '".$_bd_api_info."',
		bd_api_introduce = '".$_bd_api_introduce."',
		bd_kind = '".$_bd_kind."'
		WHERE BD_IDX = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 브랜드 삭제
}elseif( $_a_mode == "brand_del" ){

	$_idx = $_POST['idx'] ?? "";

	$total_count = sql_counter(_DB_COMPARISON, " where CD_BRAND_IDX = '".$_idx."' ");

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