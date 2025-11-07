<?
	include "../lib/inc_common.php";

	//넘어온 변수 전체 검열
	foreach($_POST as $key => $val){
		${"_".$key} = securityVal($val);
	}

////////////////////////////////////////////////////////////////////////////////////////////////
// 신규세트 상품 등록
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_a_mode == "prd2setNew" ){

	for ($i=0; $i<count($_idx); $i++){
	
		$_ary_goods_data[] = array(
			'idx' => $_idx[$i],
			'stock_idx' => $stock_idx[$i]
		);

	}

	$_pset_goods_data = json_encode($_ary_goods_data, JSON_UNESCAPED_UNICODE);

	$query = "insert prd_set set
		pset_name = '".$_pset_name."',
		pset_goods = '".$_pset_goods_data."',
		pset_count = '".count($_idx)."' ";
	wepix_query_error($query);

	$_key = mysqli_insert_id($connect);

	$response = array(
		'success' => true,
		'msg' => '완료'
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 신규세트 상품 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "prd2setModify" ){

	for ($i=0; $i<count($_idx); $i++){
	
		$_ary_goods_data[] = array(
			'idx' => $_idx[$i],
			'stock_idx' => $stock_idx[$i]
		);

	}

	$_pset_goods_data = json_encode($_ary_goods_data, JSON_UNESCAPED_UNICODE);

	$query = "UPDATE prd_set set
		pset_name = '".$_pset_name."',
		pset_goods = '".$_pset_goods_data."',
		pset_count = '".count($_idx)."'
		WHERE pset_idx = '".$_moidx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => '완료'
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 세트재고
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "prd2setStock" ){

	$set_data = wepix_fetch_array(wepix_query_error("select pset_stock from prd_set where pset_idx = '".$_moidx."' "));

	if( $_psu_mode == "plus" ){
		$_query1 = " pset_stock = pset_stock + ".$_psu_stock." ";
		$_all_psu_stock = $set_data['pset_stock'] + $_psu_stock;
		if( !$_psu_memo ){
			$_psu_memo = "세트상품  수정 증가";
		}

	}elseif( $_psu_mode == "minus" ){
		$_query1 = " pset_stock = pset_stock - ".$_psu_stock." ";
		$_all_psu_stock = $set_data['pset_stock'] - $_psu_stock;
		if( !$_psu_memo ){
			$_psu_memo = "세트상품  수정 감소";
		}
	}

	$query = "UPDATE prd_set set
		".$_query1."
		WHERE pset_idx = '".$_moidx."' ";
	wepix_query_error($query);

	$query = "insert prd_stock_unit set
		psu_rmode = 'set',
		psu_stock_idx = '".$_moidx."',
		psu_day = '".$action_time_ymd."',
		psu_mode = '".$_psu_mode."',
		psu_qry = '".$_psu_stock."',
		psu_stock = '".$_all_psu_stock."',
		psu_memo = '".$_psu_memo."',
		psu_id = '".$_sess_id."',
		psu_date = '".$check_time."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => '완료'
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

}
?>