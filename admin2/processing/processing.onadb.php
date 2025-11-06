<?

////////////////////////////////////////////////////////////////////////////////////////////////
// 오나디비 브랜드 정렬
if( $_a_mode == "onadb_brand_sort" ){

	for ($i=0; $i<count($_brand_idx); $i++){
		$query = "UPDATE "._DB_BRAND." SET 
			bd_onadb_sort_num = '".$i."'
			WHERE BD_IDX = '".$_brand_idx[$i]."' ";
		sql_query_error($query);
	}

	$response = array('success' => true, 'msg' => '완료' );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>