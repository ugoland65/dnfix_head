<?
include "../lib/inc_common.php";

	//넘어온 변수 전체 검열
	foreach($_POST as $key => $val){
		${"_".$key} = securityVal($val);
	}

////////////////////////////////////////////////////////////////////////////////////////////////
// 세트상품 상품 불러오기
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_a_mode == "prd2SetRegAddPrd" ){

	$colum = "A.CD_IDX, A.CD_NAME , A.CD_IMG";
	$colum .= ", B.ps_idx, B.ps_stock";
	$colum .= ", C.BD_NAME";

	$query = "select ".$colum."
		from "._DB_COMPARISON." A 
		left join prd_stock B ON ( B.ps_prd_idx = A.CD_IDX )
		left join "._DB_BRAND." C ON ( C.BD_IDX = A.CD_BRAND_IDX )
		where CD_IDX = '".$_addGoods_idx."' ";


	$_data = wepix_fetch_array(wepix_query_error($query));

	if( !$_data['CD_IDX'] ){
	
		$response = array(
			'success' => false,
			'msg' => '상품 IDX가 검색되지 않습니다.'
		);

		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;

	}


	$response = array(
		'success' => true,
		'msg' => '',
		'stock_idx' => $_data['ps_idx'],
		'stock' => $_data['ps_stock'],
		'name' => $_data['CD_NAME'],
		'brand' => $_data['BD_NAME'],
		'img' => $_data['CD_IMG']
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

}
?>