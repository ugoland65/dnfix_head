<?
	include "../lib/inc_common.php";

	//넘어온 변수 전체 검열
	while(list($key,$value)= each($_POST)){
		${"_".$key} = securityVal($value);
	}

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 컨텐츠 관리
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_a_mode == "prdContents" ){

	$query = "update prd_contents set 
		c19 = '".$_c19."'
		WHERE pc_idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array(
		'success' => true,
		'msg' => "완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

}
?>