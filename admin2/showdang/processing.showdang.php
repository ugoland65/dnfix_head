<?
	include "../lib/inc_common.php";

	//넘어온 변수 전체 검열
	while(list($key,$val)= each($_POST)){
		${"_".$key} = securityVal($val);
	}

////////////////////////////////////////////////////////////////////////////////////////////////
// 브랜드 링크 신규 등록
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_a_mode == "brandLinkNewReq" ){

	$query = "insert brand_link set
		bl_keyword = '".$_bl_keyword."',
		bl_link = '".$_bl_link."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => '완료'
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 브랜드 링크 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "brandLinkDel" ){

	wepix_query_error("delete from brand_link where bl_idx = '".$_idx."' ");

	$response = array(
		'success' => true,
		'msg' => '완료'
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

}

?>