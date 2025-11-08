<?

// 변수 초기화
$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";

////////////////////////////////////////////////////////////////////////////////////////////////
// 랙 등록
if( $_a_mode == "prdRack_reg" ){

	$_name = $_POST['name'] ?? "";
	$_code = $_POST['code'] ?? "";
	$_memo = $_POST['memo'] ?? "";

	$query = "insert prd_rack set
		name = '".$_name."',
		code = '".$_code."',
		memo = '".$_memo."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 랙 수정
}elseif( $_a_mode == "prdRack_modify" ){

	$_idx = $_POST['idx'] ?? "";
	$_name = $_POST['name'] ?? "";
	$_code = $_POST['code'] ?? "";
	$_memo = $_POST['memo'] ?? "";

	$query = "UPDATE prd_rack SET 
		name = '".$_name."',
		code = '".$_code."',
		memo = '".$_memo."'
		WHERE idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>