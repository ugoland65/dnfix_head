<?

// 변수 초기화
$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";

////////////////////////////////////////////////////////////////////////////////////////////////
// 오나디비 브랜드 정렬
if( $_a_mode == "onadb_brand_sort" ){

	$_brand_idx = $_POST['brand_idx'] ?? [];

	// 배열 검증
	if (!is_array($_brand_idx)) {
		$_brand_idx = [];
	}

	for ($i=0; $i<count($_brand_idx); $i++){
		$query = "UPDATE "._DB_BRAND." SET 
			bd_onadb_sort_num = '".$i."'
			WHERE BD_IDX = '".($_brand_idx[$i] ?? "")."' ";
		sql_query_error($query);
	}

	$response = array('success' => true, 'msg' => '완료' );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>