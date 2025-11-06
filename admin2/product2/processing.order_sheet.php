<?
	include "../lib/inc_common.php";
	include 'board_inc.php';

	//넘어온 변수 전체 검열
	while(list($key,$val)= each($_POST)){
		${"_".$key} = securityVal($val);
	}

	$_oo_price_kr = (int)str_replace(',','', securityVal($oo_price_kr));
	$_oo_reported_price = (int)str_replace(',','', securityVal($oo_reported_price));
	$_oo_duty_price = (int)str_replace(',','', securityVal($oo_duty_price));
	$_oo_express_price= (int)str_replace(',','', securityVal($oo_express_price));

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 만들기 - makeOrderNew로 새로만듬
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_a_mode == "makeOrder" ){

	//주문서 소트 최대값 구하기
	$data = wepix_fetch_array(wepix_query_error("select MAX(oo_sort) as max from ona_order limit 1 "));
	$_oo_sort = $data['max'] + 1;

	$query = "insert ona_order set
		oo_name = '".$_oo_name."',
		oo_sort = '".$_oo_sort."',
		oo_code = '".$_oo_code."',
		oo_token = '".$_oo_token."',
		oo_state = '1',
		oo_date = '".$wepix_now_time."',
		oo_sum_price = '".$_oo_sum_price."',
		oo_sum_goods = '".$_oo_sum_goods."',
		oo_sum_qty = '".$_oo_sum_qty."',
		oo_sum_weight = '".$_oo_sum_weight."',
		oo_c_idx = '".$_send_array2."',
		oo_price = '".$_price_array."',
		oo_qty = '".$_qty_array."',
		oo_memo = '".$_memo_array."',
		oo_unit_state = '".$_unit_state_array."' ";
	wepix_query_error($query);

	$_ary_passkey = explode(",", $_send_array2);
	$_ary_code2 = explode(",", $_code2_array);
	$_ary_code = explode(",", $_code_array);
	$_ary_price = explode(",", $_price_array);
	//$_ary_memo = explode("★", $_memo_array);

	for ($i=0; $i<count($_ary_passkey); $i++){
		$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_ary_passkey[$i]."' "));
	
		if( $_oo_code == "npg"){
			if( (!$comparison_data[CD_CODE3] OR $comparison_data[CD_CODE3] == "U" ) && $_ary_code2[$i] ){
				wepix_query_error("update "._DB_COMPARISON." set CD_CODE3 = '".$_ary_code2[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
			}
		}else{
			if( (!$comparison_data[CD_CODE2] OR $comparison_data[CD_CODE2] == "U" ) && $_ary_code2[$i] ){
				wepix_query_error("update "._DB_COMPARISON." set CD_CODE2 = '".$_ary_code2[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
			}
		}
		if( !$comparison_data[CD_CODE] && $_ary_code[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_CODE = '".$_ary_code[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}

		if( $_oo_code == "th" && !$comparison_data[CD_SUPPLY_PRICE_2] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_2 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}
		if( $_oo_code == "npg" && !$comparison_data[CD_SUPPLY_PRICE_9] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_9 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}
		if( $_oo_code == "tis" && !$comparison_data[CD_SUPPLY_PRICE_6] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_6 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}

		//if( ($_oo_code == "tma" || $_oo_code == "mg" || $_oo_code == "kiteru" ) && !$comparison_data[CD_SUPPLY_PRICE_8] && $_ary_price[$i] ){
		if( ($_oo_code == "mg" || $_oo_code == "kiteru" ) && !$comparison_data[CD_SUPPLY_PRICE_8] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_8 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}

		//if( ($_oo_code == "hp" || $_oo_code == "rj") && !$comparison_data[CD_SUPPLY_PRICE_7] && $_ary_price[$i] ){
		if( ( $_oo_code == "tma" ||  $_oo_code == "hp" || $_oo_code == "rj") && !$comparison_data[CD_SUPPLY_PRICE_7] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_7 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}
		if( $_oo_code == "rends" && !$comparison_data[CD_SUPPLY_PRICE_1] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_1 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}

		if( ( $_oo_code == "ko3" || $_oo_code == "roma" ) && !$comparison_data[CD_SUPPLY_PRICE_5] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_5 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}

		if( $_oo_code == "ko2" && !$comparison_data[CD_SUPPLY_PRICE_4] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_4 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}

		if( $_oo_code == "ko1" && !$comparison_data[CD_SUPPLY_PRICE_3] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_3 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}

	}

	$data2 = wepix_fetch_array(wepix_query_error("select oo_idx from ona_order where oo_token ='".$_oo_token."' "));

    if( $_ajax_mode == "on" ){
		//echo "|Processing_Complete|등록완료|makeOrder|".$data[oo_dix]."|".implode("/",$_return_key)."|";
    }else{
	}

	echo "|Processing_Complete|등록완료|makeOrder|".$data2[oo_idx]."|".implode("/",$_return_key)."|";
	exit;


////////////////////////////////////////////////////////////////////////////////////////////////
// 해당분류 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "thisCateDel" ){

	$_oo_idx = securityVal($oo_idx);
	$_bidx = securityVal($selectBrand);

	$oo_data = wepix_fetch_array(wepix_query_error("select oo_json from ona_order where oo_idx = '".$_oo_idx."' "));

	$_select_json3 = $oo_data[oo_json];
	$_select_json4 = json_decode($_select_json3,true);

	$_save_json = '[';
	for ($z=0; $z<count($_select_json4); $z++){
		if( $_select_json4[$z]['bidx'] == $_bidx ){
		}else{
			$_inst_json_salt[] = json_encode($_select_json4[$z], JSON_UNESCAPED_UNICODE);
		}
	}
	$_save_json .= implode(",", $_inst_json_salt);
	$_save_json .= ']';

	$query = "update ona_order set
		oo_json = '".$_save_json."'
		where oo_idx = '".$_oo_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => $_save_json
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;


////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 수정 NEW
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="modifyOrderNew" ){

	$_oo_idx = securityVal($oo_idx);
	$_bidx = securityVal($selectBrand);
	$_item = securityVal($item);
	$_total_qty = securityVal($total_qty);
	$_total_price = securityVal($total_price);
	$_total_weight = securityVal($total_weight);
	$_memo_array = securityVal($memo_array);
	$_memo_array = str_replace('\n', '', $_memo_array);

	$oo_data = wepix_fetch_array(wepix_query_error("select oo_json from ona_order where oo_idx = '".$_oo_idx."' "));

	$_select_json3 = $oo_data[oo_json];
	$_select_json4 = json_decode($_select_json3,true);

	$_ary_passkey = explode(",", $_send_array2);
	$_ary_price = explode(",", $_price_array);
	$_ary_qty = explode(",", $_qty_array);
	$_ary_memo = explode(",", $_memo_array);

	for ($i=0; $i<count($_ary_passkey); $i++){
		$_inst_selpd[] = '{"pidx":"'.$_ary_passkey[$i].'","price":"'.$_ary_price[$i].'","qty":"'.$_ary_qty[$i].'","memo":"'.$_ary_memo[$i].'"}';
	}
	$_save_selpd = implode(",", $_inst_selpd);

	$_save_data = '{"bidx":"'.$_bidx.'", "item":"'.$_item.'", "qty":"'.$_total_qty.'", "price":"'.$_total_price.'", "weight":"'.$_total_weight.'", "selpd":['.$_save_selpd.']}';

	$_save_json = '[';
	
	$_for_in_bidx = 0;

	for ($z=0; $z<count($_select_json4); $z++){
		if( $_select_json4[$z]['bidx'] == $_bidx ){

			if( $_item > 0 ){
			 $_inst_json_salt[] = $_save_data;
			}
			$_for_in_bidx++;

		}else{
			$_inst_json_salt[] = json_encode($_select_json4[$z], JSON_UNESCAPED_UNICODE);
		}
	}

	if( $_for_in_bidx == 0 ){
		$_inst_json_salt[] = $_save_data;
	}

	$_save_json .= implode(",", $_inst_json_salt);
	$_save_json .= ']';

	$query = "update ona_order set
		oo_sum_price = '".$_oo_sum_price."',
		oo_sum_goods = '".$_oo_sum_goods."',
		oo_sum_qty = '".$_oo_sum_qty."',
		oo_json = '".$_save_json."',
		oo_r_mode = 'V3'
		where oo_idx = '".$_oo_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => $_save_json
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서에서 가격없는거 신규로 등록
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="newPrice" ){

/*
	$_cd_idx = securityVal($cd_idx);
	$_price_colum = securityVal($price_colum);
	$_oop_code = securityVal($oop_code);
	$_uprice = securityVal($uprice);
	$_value = securityVal($value);

	if( !$_uprice && $_value ){
		$_uprice = $_value;
	}
*/
	$_uprice = str_replace(',','', $_value);
	//$_uprice = $_value;

	$comparison_data = wepix_fetch_array(wepix_query_error("select cd_price_fn from "._DB_COMPARISON." where CD_IDX = '".$_cd_idx."' "));
	$_cd_price_data = json_decode($comparison_data[cd_price_fn], true);

	$_cd_price_data[$_oop_code] = $_uprice;

	$_cd_price_fn = json_encode($_cd_price_data);

	wepix_query_error("update "._DB_COMPARISON." set cd_price_fn = '".$_cd_price_fn."' WHERE CD_IDX = ".$_cd_idx." ");

	$response = array(
		'success' => true,
		'uprice' => $_uprice,
		'msg' => "완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="modifyOrderMain" ){

	$query = "update ona_order set
		oo_name = '".$_oo_name."',
		oo_r_mode = 'V3'
		where oo_idx = '".$_modify_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'insert_id' => $_modify_idx,
		'msg' => "수정완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="modifyOrder" ){

	$query = "update ona_order set
		oo_name = '".$_oo_name."',
		oo_code = '".$_oo_code."',
		oo_state = '".$_oo_state."',
		oo_date_modify = '".$wepix_now_time."',
		oo_sum_price = '".$_oo_sum_price."',
		oo_sum_goods = '".$_oo_sum_goods."',
		oo_sum_qty = '".$_oo_sum_qty."',
		oo_sum_weight = '".$_oo_sum_weight."',
		oo_c_idx = '".$_send_array2."',
		oo_price = '".$_price_array."',
		oo_qty = '".$_qty_array."',
		oo_memo = '".$_memo_array."',
		oo_unit_state = '".$_unit_state_array."'
		where oo_idx = '".$_modify_idx."' ";
	wepix_query_error($query);

	$_ary_passkey = explode(",", $_send_array2);
	$_ary_code2 = explode(",", $_code2_array);
	$_ary_code = explode(",", $_code_array);
	$_ary_price = explode(",", $_price_array);
	//$_ary_memo = explode("★", $_memo_array);

	for ($i=0; $i<count($_ary_passkey); $i++){
		$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_ary_passkey[$i]."' "));
		if( $_oo_code == "npg"){
			if( (!$comparison_data[CD_CODE3] OR $comparison_data[CD_CODE3] == "U" ) && $_ary_code2[$i] ){
				wepix_query_error("update "._DB_COMPARISON." set CD_CODE3 = '".$_ary_code2[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
			}
		}else{
			if( (!$comparison_data[CD_CODE2] OR $comparison_data[CD_CODE2] == "U" ) && $_ary_code2[$i] ){
				wepix_query_error("update "._DB_COMPARISON." set CD_CODE2 = '".$_ary_code2[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
			}
		}
		if( $_oo_code == "th" && !$comparison_data[CD_SUPPLY_PRICE_2] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_2 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}
		if( !$comparison_data[CD_CODE] && $_ary_code[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_CODE = '".$_ary_code[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}
		if( $_oo_code == "npg" && !$comparison_data[CD_SUPPLY_PRICE_9] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_9 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}
		if( $_oo_code == "tis" && !$comparison_data[CD_SUPPLY_PRICE_6] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_6 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}
		//if( ($_oo_code == "tma" || $_oo_code == "mg" || $_oo_code == "kiteru" ) && !$comparison_data[CD_SUPPLY_PRICE_8] && $_ary_price[$i] ){
		if( ($_oo_code == "mg" || $_oo_code == "kiteru" ) && !$comparison_data[CD_SUPPLY_PRICE_8] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_8 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}
		//if( ($_oo_code == "hp" || $_oo_code == "rj") && !$comparison_data[CD_SUPPLY_PRICE_7] && $_ary_price[$i] ){
		if( ($_oo_code == "tma" ||  $_oo_code == "hp" || $_oo_code == "rj") && !$comparison_data[CD_SUPPLY_PRICE_7] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_7 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}
		if( $_oo_code == "rends" && !$comparison_data[CD_SUPPLY_PRICE_1] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_1 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}

		if( ( $_oo_code == "ko3" || $_oo_code == "roma" ) && !$comparison_data[CD_SUPPLY_PRICE_5] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_5 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}

		if( $_oo_code == "ko2" && !$comparison_data[CD_SUPPLY_PRICE_4] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_4 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}

		if( $_oo_code == "ko1" && !$comparison_data[CD_SUPPLY_PRICE_3] && $_ary_price[$i] ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SUPPLY_PRICE_3 = '".$_ary_price[$i]."' WHERE CD_IDX = ".$_ary_passkey[$i]." ");
		}
	}

	echo "|Processing_Complete|등록완료|modifyOrder|".count($_return_key)."|".implode("/",$_return_key)."|";
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 재고등록 (신)
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="stockWriteNew" ){

	$_stock_all_memo = securityVal($stock_all_memo);
	$_stock_day = securityVal($stock_day);
	$_stock_update_date = date("Y-m-d H:i:s",$check_time);

	//$_msg = "";
	for ($i=0; $i<count($key_check); $i++){

		$_ps_idx = $key_check[$i];
		$_unit_stock = ${'unit_stock_'.$_ps_idx};
		$_unit_modify_stock = ${'unit_modify_stock_'.$_ps_idx};
		$_unit_stock_memo = ${'unit_stock_memo_'.$_ps_idx};

		$_psu_memo = $_stock_all_memo." ".$_unit_stock_memo;

		$_psu_qry = $_unit_stock;
		if( $_unit_modify_stock > 0 ) $_psu_qry = $_unit_modify_stock;

		$prd_stock_data = wepix_fetch_array(wepix_query_error("SELECT ps_stock FROM prd_stock WHERE ps_idx = '".$_ps_idx."' " ));
		$_prd_stock_count = $prd_stock_data[ps_stock] + $_psu_qry;

		$_last = "( ".$_psu_qry." ) ".$_psu_memo;

		$update = "update prd_stock set ps_stock = ps_stock + ".$_psu_qry.", ";			
		$update .= " ps_stock_all = ps_stock_all + ".$_psu_qry.", ";
		$update .= " ps_last_in = '".$_last."', ";
		$update .= " ps_update_date = '".$_stock_update_date."' where ps_idx = '".$_ps_idx."' ";
		wepix_query_error($update);

		$query = "insert prd_stock_unit set
			psu_stock_idx = '".$_ps_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'plus',
			psu_qry = '".$_psu_qry."',
			psu_stock = '".$_prd_stock_count."',
			psu_kind = '신규입고',
			psu_memo = '".$_psu_memo."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."' ";
		wepix_query_error($query);

		//$_msg .= $_ps_idx." / ".$_unit_stock." / ".$_unit_modify_stock." / ".$_unit_stock_memo."\n";
	}

	wepix_query_error("update ona_order set oo_state = '7' WHERE oo_idx = ".$_modify_idx." ");
	//$_msg = $_stock_all_memo."/".$_stock_day."/".$_stock_update_date;

	$response = array(
		'success' => true,
		'msg' => $_msg
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 재고등록
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="stockWrite" ){

	$_stock_all_memo = securityVal($stock_all_memo);
	$_stock_day = securityVal($stock_day);
	$_stock_update_date = date("Y-m-d H:i:s",$check_time);

	$_send_array2 = securityVal($send_array2);
	$_qty_array = securityVal($qty_array);
	$_unit_stock_idx = securityVal($unit_stock_idx);
	$_unit_stock_array = securityVal($unit_stock_array);
	$_unit_stock_memo_array = securityVal($unit_stock_memo_array);

	$_ary_passkey = explode(",", $_send_array2);
	$_ary_qty = explode(",", $_qty_array);
	$_ary_unit_stock_idx = explode(",", $_unit_stock_idx);
	$_ary_unit_stock_array = explode(",", $_unit_stock_array);
	$_ary_unit_stock_memo_array = explode(",", $_unit_stock_memo_array);

	for ($i=0; $i<count($_ary_passkey); $i++){

		$_psu_stock_idx = $_ary_unit_stock_idx [$i];
		$_psu_qry = $_ary_unit_stock_array [$i];
		$_psu_memo = $_stock_all_memo." ".$_ary_unit_stock_memo_array [$i];

		$prd_stock_data = wepix_fetch_array(wepix_query_error("SELECT * FROM prd_stock WHERE ps_idx = '".$_psu_stock_idx."' " ));
		$_prd_stock_count = $prd_stock_data[ps_stock] + $_psu_qry;

		$update = "update prd_stock set ps_stock = ps_stock + ".$_psu_qry.", ";			
		$update .= " ps_stock_all = ps_stock_all + ".$_psu_qry.", ";
		$update .= " ps_update_date = '".$_stock_update_date."' where ps_idx = '".$_psu_stock_idx."' ";
		wepix_query_error($update);

		$query = "insert prd_stock_unit set
			psu_stock_idx = '".$_psu_stock_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = 'plus',
			psu_qry = '".$_psu_qry."',
			psu_stock = '".$_prd_stock_count."',
			psu_kind = '신규입고',
			psu_memo = '".$_psu_memo."',
			psu_token = '".$_oo_token."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."' ";
		wepix_query_error($query);

	}

	wepix_query_error("update ona_order set oo_state = '7' WHERE oo_idx = ".$_modify_idx." ");

	echo "|Processing_Complete|등록완료|".$_unit_stock_memo_array."||";

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 만들기 - makeOrderNew로 새로만듬
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "makeOrderNew" ){

	//주문서 소트 최대값 구하기
	$data = wepix_fetch_array(wepix_query_error("select MAX(oo_sort) as max from ona_order limit 1 "));
	$_oo_sort = $data['max'] + 1;

	$query = "insert ona_order set
		oo_name = '".$_oo_name."',
		oo_sort = '".$_oo_sort."',
		oo_code = '".$_oo_code."',
		oo_token = '".$_oo_token."',
		oo_state = '1',
		oo_date = '".$wepix_now_time."',
		oo_sum_price = '".$_oo_sum_price."',
		oo_sum_goods = '".$_oo_sum_goods."',
		oo_sum_qty = '".$_oo_sum_qty."',
		oo_sum_weight = '".$_oo_sum_weight."'  ";
	wepix_query_error($query);

	$_insert_id = mysqli_insert_id($connect);

	$response = array(
		'success' => true,
		'insert_id' => $_insert_id,
		'msg' => "등록완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 입고완료 처리
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="oderState" ){

	$_step = securityVal($step);

	$ona_order_data = wepix_fetch_array(wepix_query_error("select oo_json_date from ona_order where oo_idx = '".$_modify_idx."' "));

	$_oo_json_date = $ona_order_data[oo_json_date].'{"step":"'.$_step.'", "date":"'.$action_time.'"},';

	//
	wepix_query_error("update ona_order set 
		oo_state = '".$_step."',
		oo_json_date = '".$_oo_json_date."'
		WHERE oo_idx = ".$_modify_idx." ");

	echo "|Processing_Complete|등록완료|||";
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 유닛 주문 복귀
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="unitFalseReturn" ){

	$_oo_idx = securityVal($oo_idx);
	$_target_idx = securityVal($target_idx);

	$oo_data = wepix_fetch_array(wepix_query_error("select oo_false from ona_order where oo_idx = '".$_oo_idx."' "));

	$_false_data = "[".$oo_data[oo_false]."]";
	$_false_json = json_decode($_false_data,true);

	$_ary_target_idx = explode(",", $_target_idx);
	for ($z=0; $z<count($_ary_target_idx); $z++){
		$_chk_idx[$_ary_target_idx[$z]] = "ok";
	}

	for ($z=0; $z<count($_false_json); $z++){
		//기존에 있음
		if( $_chk_idx[$_false_json[$z]['pidx']] == "ok" ){
		}else{
			$_false_add_data_ary[] = json_encode($_false_json[$z]);
		}
	}

	$_false_data = implode(",", $_false_add_data_ary);

	$query = "update ona_order set
		oo_false = '".$_false_data."'
		where oo_idx = '".$_oo_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => '완료'
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;


////////////////////////////////////////////////////////////////////////////////////////////////
// 유닛 주문실패 NEW
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="nunitFalseNew" ){

/*
	$_oo_idx = securityVal($oo_idx);
	$_target_idx = securityVal($target_idx);
	$_target_price= securityVal($target_price);
	$_qty_array = securityVal($qty_array);
	$_memo_array = securityVal($memo_array);
*/	
	$_bidx = securityVal($selectBrand);

	$_ary_target_idx = explode(",", $_target_idx);
	$_ary_target_price = explode(",", $_target_price);
	$_ary_target_price_sum = explode(",", $_target_price_sum);
	$_ary_qty_array = explode(",", $_qty_array);
	$_ary_target_weight_sum = explode(",", $_target_weight_sum);
	$_ary_memo_array = explode(",", $_memo_array);

	$oo_data = wepix_fetch_array(wepix_query_error("select oo_json, oo_false from ona_order where oo_idx = '".$_oo_idx."' "));

	$_order_sec_json2 = $oo_data[oo_json];
	$_order_sec_json = json_decode($_order_sec_json2,true);


	$_bidx_json_key = "";

	foreach($_order_sec_json as $key => $val) {
		if( $_order_sec_json[$key]['bidx'] == $_bidx ){
			$_bidx_json_key = $key;
			break;
		}
	}


	$_order_sec_json[$_bidx_json_key]['false'] = count($_ary_target_idx);
	$_order_sec_json[$_bidx_json_key]['false_sum_qty'] = array_sum($_ary_qty_array);
	$_order_sec_json[$_bidx_json_key]['false_sum_price'] = array_sum($_ary_target_price_sum);
	$_order_sec_json[$_bidx_json_key]['false_sum_weight'] = array_sum($_ary_target_weight_sum);

	$_selpd = $_order_sec_json[$_bidx_json_key]['selpd'];

	if( count($_ary_target_idx)  == 1 ){

		foreach($_selpd as $key => $val) {
			if( $_selpd[$key]['pidx'] ==$_target_idx ){
				$_order_sec_json[$_bidx_json_key]['selpd'][$key]['false'] = true;
				//break;
			}
		}

	}elseif( count($_ary_target_idx)  > 1 ){
		for ($z=0; $z<count($_ary_target_idx); $z++){
			foreach($_selpd as $key => $val) {
				if( $_selpd[$key]['pidx'] == $_ary_target_idx[$z] ){
					$_order_sec_json[$_bidx_json_key]['selpd'][$key]['false'] = true;
					//break;
				}
			}
		}
	}

	$_oo_json = json_encode($_order_sec_json, JSON_UNESCAPED_UNICODE);


	//$_false_data = '{"pidx":"209","qty":"2"}';
	$_false_data = "[".$oo_data[oo_false]."]";
	$_false_json = json_decode($_false_data,true);

	for ($z=0; $z<count($_false_json); $z++){
		$_chk_idx[$_false_json[$z]['pidx']] = "ok";
	}


	for ($z=0; $z<count($_ary_target_idx); $z++){
		//기존에 있음
		if( $_chk_idx[$_ary_target_idx[$z]] == "ok" ){
		}else{
			$_false_add_data_ary[] = '{"pidx":"'.$_ary_target_idx[$z].'","price":"'.$_ary_target_price[$z].'","qty":"'.$_ary_qty_array[$z].'","memo":"'.$_ary_memo_array[$z].'"}';
		}
	}

	$_false_add_data = implode(",", $_false_add_data_ary);

	if( $oo_data[oo_false] ){
		$_false_data = $oo_data[oo_false].",".$_false_add_data;
	}else{
		$_false_data = $_false_add_data;
	}
/*
	$_ary_false_data = explode(",", $_false_data);
	$_false_data2 = array_unique($_ary_false_data);
	$_false_data3 = implode(",", $_false_data2);
*/

	$query = "update ona_order set
		oo_json = '".$_oo_json."',
		oo_false = '".$_false_data."'
		where oo_idx = '".$_oo_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => '완료'
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 유닛 주문실패 nunitFalseNew로 새로만듬
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="nunitFalse" ){

	$_target_idx = securityVal($target_idx);

	$ona_order_data = wepix_fetch_array(wepix_query_error("select * from ona_order where oo_idx = '".$_modify_idx."' "));

	$_ary_target_idx = explode(",", $_target_idx);
	$_ary_save_data_oo_c_idx = explode(",", $ona_order_data[oo_c_idx]);
	$_ary_save_data_oo_unit_state = explode(",", $ona_order_data[oo_unit_state]);

	for ($i=0; $i<count($_ary_save_data_oo_c_idx); $i++){
		$__chk = "no";
		for ($z=0; $z<count($_ary_target_idx); $z++){
			if( $_ary_save_data_oo_c_idx[$i] == $_ary_target_idx[$z] ){
				$__chk = "ok"; 
			}
		}
		if( $__chk == "ok" ){
			$new_value[] = "c";
		}else{
			$new_value[] = $_ary_save_data_oo_unit_state[$i];
		}
	}

	$new_value_finish =  implode(",", $new_value);

	wepix_query_error("update ona_order set oo_unit_state = '".$new_value_finish."' WHERE oo_idx = ".$_modify_idx." ");

	echo "|Processing_Complete|등록완료|".$_modify_idx."/".$new_value_finish."||";
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 디테일 팝업수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="orderSheetDetail" ){

	$oo_data = wepix_fetch_array(wepix_query_error("select oo_date_data from ona_order where oo_idx = '".$_modify_idx."' "));

	$_oo_date_data = json_decode($oo_data[oo_date_data], true);

	$_oo_sum_price = (int)str_replace(',','', $_oo_sum_price);
	$_exchange_charge = (int)str_replace(',','', $_exchange_charge);
	$_oo_fn_price = (int)str_replace(',','', $_oo_fn_price);

	for ($i=0; $i<count($_change_price_mode); $i++){

		$_mode = $_change_price_mode[$i];
		$_body = $_change_price_body[$i];
		$_price = (int)str_replace(',','', $_change_price_price[$i]);

		$_change_price[] = array(
			'mode' => $_mode,
			'body' => $_body,
			'price' => $_price
		);
	}

	$_ary_price_data = array(
		'price' => $_price,
		'currency' => $_currency,
		'change_price' => $_change_price,
		'pay_mode' => $_pay_mode,
		'pay_price' => $_oo_price_kr,
		'pay_date' => $_pay_date,
		'exchange_rate' => $_exchange_rate,
		'exchange_charge' => $_exchange_charge
	);

	$_express_price = (int)str_replace(',','', $_express_price);
	$_express_price_add = (int)str_replace(',','', $_express_price_add);

	$_ary_express_data = array(
		'mode' => $_express_mode,
		'name' => $_express_name,
		'number' => $_express_number,
		'report_weight' => $_express_report_weight,
		'weight' => $_express_weight,
		'cbm' => $_express_cbm,
		'box' => $_express_box,
		'price' => $_express_price,
		'price_add' => $_express_price_add
	);

	$_tex_report_price = (int)str_replace(',','', $_tex_report_price);
	$_tex_duty_price = (int)str_replace(',','', $_tex_duty_price);
	$_tex_vat_price = (int)str_replace(',','', $_tex_vat_price);
	$_tex_commission = (int)str_replace(',','', $_tex_commission);

	$_ary_tex_data = array(
		'num' => $_tex_num,
		'report_price' => $_tex_report_price,
		'duty_price' => $_tex_duty_price,
		'vat_price' => $_tex_vat_price,
		'commission' => $_tex_commission
	);

	$_oo_date_data['in_date'] = $_in_date;

	$_oo_price_data = json_encode($_ary_price_data, JSON_UNESCAPED_UNICODE);
	$_oo_express_data = json_encode($_ary_express_data, JSON_UNESCAPED_UNICODE);
	$_oo_tex_data = json_encode($_ary_tex_data, JSON_UNESCAPED_UNICODE);
	$_date_data = json_encode($_oo_date_data);

	$query = "update ona_order set
		oo_name = '".$_oo_name."',
		oo_price_data = '".$_oo_price_data."',
		oo_fn_price = '".$_oo_fn_price."',
		oo_express_data = '".$_oo_express_data."',
		oo_tex_data = '".$_oo_tex_data."',
		oo_date_data = '".$_date_data."',
		oo_sum_price = '".$_oo_sum_price."',
		oo_price_kr = '".$_oo_price_kr."'
		where oo_idx = '".$_modify_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => "완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;
/*
	

	$query = "update ona_order set
		oo_name = '".$_oo_name."',
		
		
		oo_price_date = '".$_oo_price_date."', //지워도 될듯
		oo_in_date = '".$_oo_in_date."',
		oo_reported_price = '".$_oo_reported_price."',
		oo_duty_price = '".$_oo_duty_price."',
		oo_duty_due_date = '".$_oo_duty_due_date."',
		oo_duty_settlement_date = '".$_oo_duty_settlement_date."',
		oo_box = '".$_oo_box."',
		oo_box_weight = '".$_oo_box_weight."',
		oo_box_weight_fix = '".$_oo_box_weight_fix."',
		oo_express = '".$_oo_express."',
		oo_express_number = '".$_oo_express_number."', //지움
		oo_express_price = '".$_oo_express_price."', //지워도 될듯
		oo_express_price_date = '".$_oo_express_price_date."', //지워도 될듯
		oo_express_price_settlement_date = '".$_oo_express_price_settlement_date."', //지워도 될듯
		oo_import_declaration = '".$_oo_import_declaration."'
		where oo_idx = '".$_modify_idx."' ";
	wepix_query_error($query);

	msg("수정 완료!", "popup.order_sheet_view.php?idx=".$_modify_idx."&parent_reload=ok");
*/

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문표 순서변경
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="orderSort" ){

	$_ary_idx = securityVal($modify_idx);
	$_ary_sort = securityVal($modify_sort);


	for($i=0;$i<count($_ary_idx);$i++){
		$query = "update ona_order set
			oo_sort = '".$_ary_sort[$i]."'
			where oo_idx = '".$_ary_idx[$i]."'";
		wepix_query_error($query);
	}
	msg("수정완료", "order_sheet_list.php");

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 추가+수정하기
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="orderSheetProductModify" ){

	$_oog_code = securityVal($oog_code);
	$_oog_data = securityVal($oog_data);
	
	$query = "update ona_order_group set
		oog_data = '".$_oog_data."'
		where oog_code = '".$_oog_code."' ";
	wepix_query_error($query);

	echo "|Processing_Complete|등록완료||||";
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 등록하고 추가하기
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="ohAddBeforeReqPd" ){

	$_oog_code = securityVal($oog_code);
	$_cd_kind_code = securityVal($cd_kind_code);
	$_cl_brand = securityVal($cl_brand);
	$_cl_brand2 = securityVal($cl_brand2);
	$_cd_name = securityVal($cd_name);
	$_cd_name_og = securityVal($cd_name_og);
	$_cd_weight2 = securityVal($cd_weight2);
	$_cd_code = securityVal($cd_code);
	$_cd_code_npg = securityVal($cd_code_npg);
	//$_cd_code3 = securityVal($cd_code3);
	$_cd_price_mode = securityVal($cd_price_mode);

	if( $_oog_code == "npg" ){
		$_cd_code3 = $_cd_code_npg;
	}else{
		$_cd_code2 = $_cd_code_npg;
	}

	$_cd_supply_price = (int)str_replace(',','', securityVal($cd_supply_price)); //

	if( $_cd_price_mode == "TH" ){
		$_price_query_text = " CD_SUPPLY_PRICE_2 = '".$_cd_supply_price."', ";
	}elseif( $_cd_price_mode == "TIS" ){
		$_price_query_text = " CD_SUPPLY_PRICE_6 = '".$_cd_supply_price."', ";
	}elseif( $_cd_price_mode == "NPG" ){
		$_price_query_text = " CD_SUPPLY_PRICE_9 = '".$_cd_supply_price."', ";
	}elseif( $_cd_price_mode == "A" ){
		$_price_query_text = " CD_SUPPLY_PRICE_7 = '".$_cd_supply_price."', ";
	}elseif( $_cd_price_mode == "B" ){
		$_price_query_text = " CD_SUPPLY_PRICE_8 = '".$_cd_supply_price."', ";
	}elseif( $_cd_price_mode == "NLS" ){
		$_price_query_text = " CD_SUPPLY_PRICE_1 = '".$_cd_supply_price."', ";
	}elseif( $_cd_price_mode == "ETC1" ){
		$_price_query_text = " CD_SUPPLY_PRICE_5 = '".$_cd_supply_price."', ";
	}

	$query = "insert into  "._DB_COMPARISON." set
		CD_COMPARISON = 'N',
		CD_KIND_CODE = '".$_cd_kind_code."',
		CD_BRAND_IDX = '".$_cl_brand."',
		CD_BRAND2_IDX = '".$_cl_brand2."',
		CD_NAME = '".$_cd_name."',
		CD_NAME_OG = '".$_cd_name_og."',
		CD_WEIGHT2 = '".$_cd_weight2."',
		".$_price_query_text."
		CD_CODE = '".$_cd_code."',
		CD_CODE2 = '".$_cd_code2."',
		CD_CODE3 = '".$_cd_code3."' ";
	wepix_query_error($query);

	$_key = mysqli_insert_id($connect);

	echo "|Processing_Complete|등록완료|".$_key."|||";
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 IDX  추가
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="ohAddIdxPd" ){

	$_oog_code = securityVal($oog_code);
	$_add_idx = securityVal($add_idx);

	$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_add_idx."' "));
	$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx from prd_stock where ps_prd_idx = '".$_add_idx."' "));

	if( $_oog_code == "npg" ){
		$_cd_code_npg = $comparison_data[CD_CODE3];
		$_cd_supply_price = $comparison_data[CD_SUPPLY_PRICE_9];
	}else{
		$_cd_code_npg = $comparison_data[CD_CODE2];
	}

	$_cd_code = $comparison_data[CD_CODE];
	$_cd_kind_code = $koedge_prd_kind_name[$comparison_data[CD_KIND_CODE]];
	$_cd_name = $comparison_data[CD_NAME];
	$_cd_weight2 = $comparison_data[CD_WEIGHT2];

	echo "|Processing_Complete|등록완료|".$stock_data[ps_idx]."|".$_cd_code_npg."|".$_cd_code."|".$_cd_kind_code."|".$_cd_name."|".$_cd_supply_price."|".$_cd_weight2."|";
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 추가+수정하기2
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="orderSheetProductModifyNew" ){

	$_oop_idx = securityVal($oop_idx);

	$_idx = securityVal($idx);
	$_ps_idx = securityVal($ps_idx);
	$_code = securityVal($code);
	$_jan = securityVal($jan);
	$_kind = securityVal($kind);
	$_pname = securityVal($pname);
	$_om = securityVal($ordermemo);
	$_price = securityVal($price);
	$_weight = securityVal($weight);
	$_state = securityVal($state);

	$_data_array = array();

	for ($i=0; $i<count($_idx); $i++){

		if( $_ps_idx[$i] ){

			$last_in = wepix_fetch_array(wepix_query_error("select * from prd_stock_unit where psu_stock_idx = '".$_ps_idx[$i]."' 
				and psu_mode = 'plus' and psu_kind = '신규입고' order by psu_date desc"));

			if( $last_in[psu_idx] ){
				$_last = "( ".$last_in[psu_qry]." ) ".$last_in[psu_memo];
			}else{
				$_last = "";
			}
		}else{
			$_last = "";
		}

		if( $_state[$i] == "out" ){
			wepix_query_error("update "._DB_COMPARISON." set CD_SALE_STATE = 'N' where CD_IDX = '".$_idx[$i]."' ");
		}

		//$_price22 = (int)str_replace(',','', $_price[$i]);
		$_price22 = str_replace(',','', $_price[$i]);
		$_pname22 = securityVal($_pname[$i]);

		array_push($_data_array, array(
			"idx" => $_idx[$i],
			"stockidx" => $_ps_idx[$i],
			"code" => $_code[$i],
			"jan" => $_jan[$i],
			"kind" => $_kind[$i],
			"pname" => $_pname22,
			"price" => $_price22,
			"weight" => $_weight[$i],
			"om" => $_om[$i],
			"last" => $_last,
			"state" => $_state[$i]
		));

	}

	$_oop_data = json_encode($_data_array, JSON_UNESCAPED_UNICODE);

	$query = "update ona_order_prd set
		oop_data = '".$_oop_data."'
		where oop_idx = '".$_oop_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => "카운트".count($_idx)
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

/*
	echo "|Processing_Complete|등록완료||||";
	exit;
*/

////////////////////////////////////////////////////////////////////////////////////////////////
// 그룹관리
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="orderSheetGroupModify" ){

	$_oog_idx = securityVal($oog_idx);

	$_brand = securityVal($brand);
	$_name = securityVal($name);
	$_oop_idx = securityVal($oop_idx);
	$_active = securityVal($active);

	for ($i=0; $i<count($_brand); $i++){
		$data_ary[] = '{"brand":"'.$_brand[$i].'","name":"'.$_name[$i].'","oop_idx":"'.$_oop_idx[$i].'","active":"'.$_active[$i].'"}';
	}

	$data = implode(",", $data_ary);

	$query = "update ona_order_group set
		oog_brand = '".$data."'
		where oog_idx = '".$_oog_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => "처리완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 그룹명 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="ohGroupModify" ){

	$_oog_idx = securityVal($oog_idx);
	$_oog_arynum = securityVal($oog_arynum);
	$_group_name = securityVal($group_name);
	$_group_idx = securityVal($group_idx);

	$oog_data = wepix_fetch_array(wepix_query_error("select oog_brand from ona_order_group where oog_idx = '".$_oog_idx."' "));

	$_brand_json = '['.$oog_data[oog_brand].']';
	$_brand_json_data = json_decode($_brand_json,true);

	for ($i=0; $i<count($_brand_json_data); $i++){
		if( $i == $_oog_arynum ){
			$_ary_oog_brand[] = '{"brand":'.$_brand_json_data[$i]['brand'].',"name":"'.$_group_name.'","oop_idx":"'.$_group_idx.'"}';
		}else{
			$_ary_oog_brand[] = '{"brand":'.$_brand_json_data[$i]['brand'].',"name":"'.$_brand_json_data[$i]['name'].'","oop_idx":"'.$_brand_json_data[$i]['oop_idx'].'"}';
		}
	}

	$_oog_brand = implode(",", $_ary_oog_brand);

	$query = "update ona_order_group set
		oog_brand = '".$_oog_brand."'
		where oog_idx = '".$_oog_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => "aa/".$_oog_arynum
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

/*
{"brand":10,"name":"닛포리기프트","oop_idx":"1"},{"brand":75,"name":"SSI JAPAN ","oop_idx":"2"},{"brand":16,"name":"엔조이 토이즈","oop_idx":"3"},{"brand":11,"name":"에이원","oop_idx":"4"},{"brand":70,"name":"RENDS","oop_idx":"5"},{"brand":"63","name":"지프로젝트","oop_idx":"6"},{"brand":"71","name":"나카지마 화학","oop_idx":"7"},{"brand":"74","name":"후지 월드공예","oop_idx":"8"},{"brand":"30","name":"러브팩터","oop_idx":"9"},{"brand":"15","name":"토이쿠르 재팬","oop_idx":"10"},{"brand":"79","name":"Eve dolls","oop_idx":"11"},{"brand":"88","name":"프라임","oop_idx":"12"},{"brand":"19","name":"KISS ME LOVE","oop_idx":"13"},{"brand":"13","name":"키테루키테루","oop_idx":"14"},{"brand":"23","name":"MATE","oop_idx":"15"},{"brand":"7","name":"라이드 재팬","oop_idx":"16"},{"brand":"76","name":"Teppen","oop_idx":"17"},{"brand":"8","name":"매직 아이즈","oop_idx":"18"},{"brand":"21","name":"피치 토이즈","oop_idx":"19"},{"brand":"89","name":"세츠겐노 울프 완구","oop_idx":"20"},{"brand":"77","name":"판타스틱 베이비","oop_idx":"21"},{"brand":"67","name":"리그레 재팬","oop_idx":"22"}
*/

////////////////////////////////////////////////////////////////////////////////////////////////
// 정보갱신
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="groupInfoReload" ){

	$oop_data = wepix_fetch_array(wepix_query_error("select * from ona_order_prd where oop_idx = '".$_oop_idx."' "));

	$_oop_json = '['.$oop_data[oop_data].']';
	$_oop_jsondata = json_decode($_oop_json,true);

	for ($z=0; $z<count($_oop_jsondata); $z++){

		$_idx = $_oop_jsondata[$z]['idx'];
		$_ps_idx = $_oop_jsondata[$z]['stockidx'];
		$_code = $_oop_jsondata[$z]['code'];
		$_jan = $_oop_jsondata[$z]['jan'];
		$_kind = $_oop_jsondata[$z]['kind'];
		$_pname = $_oop_jsondata[$z]['pname'];
		$_price = $_oop_jsondata[$z]['price']*1;
		$_weight = $_oop_jsondata[$z]['weight'];
		$_om = $_oop_jsondata[$z]['om'];
		$_state = $_oop_jsondata[$z]['state'];

		$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
		$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx,ps_stock from prd_stock where ps_prd_idx = '".$_idx."' "));

		//재고코드 검사
		if( $_ps_idx != $stock_data[ps_idx] ){
			$_ps_idx = $stock_data[ps_idx];
		}

		//JAN코드 검사
		if( $_jan != $comparison_data[CD_CODE] ){
			$_jan = $comparison_data[CD_CODE];
		}

		//kind 검사
		if( $_kind != $koedge_prd_kind_name[$comparison_data[CD_KIND_CODE]] ){
			$_kind = $koedge_prd_kind_name[$comparison_data[CD_KIND_CODE]];
		}

		//상품명
		if( $_pname != $comparison_data[CD_NAME] ){
			$_pname = $comparison_data[CD_NAME];
		}

		if( $comparison_data[CD_WEIGHT2] > 0 ) { $_weight_og = $comparison_data[CD_WEIGHT2]; }else{  $_weight_og = $comparison_data[CD_WEIGHT]; }

		//무게정보
		if( $_weight != $_weight_og ){
			$_weight = $_weight_og;
		}

		if( $stock_data[ps_idx] ){
		
			$last_in = wepix_fetch_array(wepix_query_error("select * from prd_stock_unit where psu_stock_idx = '".$stock_data[ps_idx]."' 
				and psu_mode = 'plus' and psu_kind = '신규입고' order by psu_date desc"));

			if( $last_in[psu_idx] ){
				$_last = "( ".$last_in[psu_qry]." ) ".$last_in[psu_memo];
			}else{
				$_last = "";
			}
		
		}else{
			$_last = "";
		}

		//$_price22 = (int)str_replace(',','', $_price[$i]);
		$data_ary[] = '{"idx":"'.$_idx.'","stockidx":"'.$_ps_idx.'","code":"'.$_code.'","jan":"'.$_jan.'","kind":"'.$_kind.'","pname":"'.$_pname.'","price":"'.$_price.'","weight":"'.$_weight.'","om":"'.$_om.'","last":"'.$_last.'","state":"'.$_state.'"}';

	}

	$data = implode(",", $data_ary);

	$query = "update ona_order_prd set
		oop_data = '".$data."'
		where oop_idx = '".$_oop_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => "처리완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;


////////////////////////////////////////////////////////////////////////////////////////////////
// 정보갱신
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="ohPdGroupMake" ){

	$_group_name = securityVal($group_name);

	wepix_query_error(" insert into  ona_order_prd set oop_name = '".$_group_name."' ");

	$_return_key = mysqli_insert_id($connect);

	$response = array(
		'success' => true,
		'return_key' => $_return_key,
		'msg' => "처리완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;


////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="orderSheetDel" ){

	$_idx = securityVal($idx);

	wepix_query_error("delete from ona_order where oo_idx = '".$_idx."' ");

	$response = array(
		'success' => true,
		'msg' => "처리완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 주문처 생성
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="orderSheetShopMake" ){

	$_oog_name = securityVal($oog_name);
	$_oog_code = securityVal($oog_code);
	$_oog_group = securityVal($oog_group);

	$name_ck = wepix_fetch_array(wepix_query_error("select oog_idx from ona_order_group WHERE oog_name = '".$_oog_name."' "));
	if( $name_ck[oog_idx] ){
		$response = array(
			'success' => false,
			'msg' => "이름 중복!!"
		);

		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;
	}

	$code_ck = wepix_fetch_array(wepix_query_error("select oog_idx from ona_order_group WHERE oog_code = '".$_oog_code."' "));
	if( $code_ck[oog_idx] ){
		$response = array(
			'success' => false,
			'msg' => "코드 중복!!"
		);

		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;
	}



	wepix_query_error(" insert into  ona_order_group set oog_name = '".$_oog_name."', oog_code = '".$_oog_code."', oog_group = '".$_oog_group."' ");
	$_return_key = mysqli_insert_id($connect);

	$response = array(
		'success' => true,
		'return_key' => $_return_key,
		'msg' => "처리완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;


////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 단종처리
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="soldOut" ){


	$oop_data = wepix_fetch_array(wepix_query_error("select * from ona_order_prd where oop_idx = '".$_oop_idx."' "));
	
	$_oop_json_check_data = substr($oop_data[oop_data], 0,1);
	
	if( $_oop_json_check_data == "[" ){
		$_oop_json = $oop_data[oop_data];
	}else{
		$_oop_json = '['.$oop_data[oop_data].']';
	}

	$_oop_jsondata = json_decode($_oop_json,true);


	//$_oop_jsondata[$_num]['state'] = "out";
	if( $_soldoutmode == "out" ){
		$_oop_jsondata[$_num]['state'] = "out";
		$_cd_sale_state = "N";

	}elseif( $_soldoutmode == "on" ){
		$_oop_jsondata[$_num]['state'] = "on";
		$_cd_sale_state = "Y";
	}


	$_cd_idx = $_oop_jsondata[$_num]['idx'];

	$_data = json_encode($_oop_jsondata, JSON_UNESCAPED_UNICODE);

	$query = "update ona_order_prd set
		oop_data = '".$_data."'
		where oop_idx = '".$_oop_idx."' ";
	wepix_query_error($query);

	if( $_cd_idx ){
		//wepix_query_error("update "._DB_COMPARISON." set CD_SALE_STATE = '".$_cd_sale_state."' where CD_IDX = '".$_cd_idx."' ");
	}

	$response = array(
		'success' => true,
		'msg' => "처리완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;



////////////////////////////////////////////////////////////////////////////////////////////////
// 정보 갱신 완성본
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="lastInfoReset" ){

	$_oop_idx = securityVal($oop_idx);

	$oop_data = wepix_fetch_array(wepix_query_error("select * from ona_order_prd where oop_idx = '".$_oop_idx."' "));
	
	$_oop_json_check_data = substr($oop_data[oop_data], 0,1);
	
	if( $_oop_json_check_data == "[" ){
		$_oop_json = $oop_data[oop_data];
	}else{
		$_oop_json = '['.$oop_data[oop_data].']';
	}

	$_oop_jsondata = json_decode($_oop_json,true);

	for ($z=0; $z<count($_oop_jsondata); $z++){

		$_idx = $_oop_jsondata[$z]['idx'];
		$_ps_idx = $_oop_jsondata[$z]['stockidx'];
		
		//$comparison_data = wepix_fetch_array(wepix_query_error("select cd_weight_fn from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
		$comparison_data = wepix_fetch_array(wepix_query_error("select 
			A.*,
			B.ps_idx, B.ps_stock, B.ps_cafe24_sms
			from "._DB_COMPARISON." A
			left join prd_stock B ON (A.CD_IDX = B.ps_prd_idx ) 
			where CD_IDX = '".$_idx."' "));

		$_cd_weight_data = json_decode($comparison_data[cd_weight_fn], true);
		$_cd_weight_1 = $_cd_weight_data['1'];
		$_cd_weight_2 = $_cd_weight_data['2'];
		$_cd_weight_3 = $_cd_weight_data['3'];

		$_ps_cafe24_sms_data = json_decode($comparison_data[ps_cafe24_sms], true);

		if( $_cd_weight_3 ){ 
			$_oop_jsondata[$z]['weight'] = $_cd_weight_3; 
		}elseif( $_cd_weight_2 ){ 
			$_oop_jsondata[$z]['weight'] = $_cd_weight_2; 
		}elseif( $_cd_weight_1 ){ 
			$_oop_jsondata[$z]['weight'] = $_cd_weight_1; 
		}


		//$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx from prd_stock where ps_prd_idx = '".$_idx."' "));

		if(  $_oop_jsondata[$z]['stockidx'] == "" ){
			if( $comparison_data[ps_idx] ){
				$_oop_jsondata[$z]['stockidx'] = $comparison_data[ps_idx];
			}
		}

		//재고가 있을경우 sms 내용을 지워준다
		if( $comparison_data[ps_stock] > 0 ){
			if( $comparison_data[ps_cafe24_sms] ){
				wepix_query_error("update prd_stock set ps_cafe24_sms = '' where ps_idx = '".$comparison_data[ps_idx]."' ");
			}
		}


		if( $_ps_idx ){

			//$prd_stock_data = wepix_fetch_array(wepix_query_error("SELECT ps_stock FROM prd_stock WHERE ps_idx = '".$_ps_idx."' " ));
			$last_in = wepix_fetch_array(wepix_query_error("select * from prd_stock_unit where psu_stock_idx = '".$_ps_idx."' 
				and psu_mode = 'plus' and psu_kind = '신규입고' order by psu_date desc"));

			if( $last_in[psu_idx] ){
				$_last = "( ".$last_in[psu_qry]." ) ".$last_in[psu_memo];
			}else{
				$_last = "";
			}

		}else{
			$_last = "";
		}

		$_oop_jsondata[$z]['last'] = $_last;
	}

	$_data = json_encode($_oop_jsondata, JSON_UNESCAPED_UNICODE);

	$query = "update ona_order_prd set
		oop_data = '".$_data."'
		where oop_idx = '".$_oop_idx."' ";
	wepix_query_error($query);

	$response = array(
		'success' => true,
		'msg' => "처리완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 정보 갱신 완성본
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="cafe24Sms" ){

	setlocale(LC_CTYPE, 'ko_KR.eucKR'); 
	extract($_FILES['userfile']); 

	$fp = fopen($tmp_name, 'r'); 
	$count = 0;
	$count2 = 0;
	$_file_name = $name;

	$data = wepix_fetch_array(wepix_query_error("select uid from cafe24_sms where file_name = '".$_file_name."' "));
	if( $data['uid'] ){
		msg("이미 등록된 파일입니다.", "cafe24_sms.php");
		exit;
	}

	while ($row = fgetcsv($fp, 100000, ',')) {

		$count++;
		$_date = $row[0];
		if( $_date > 0 ){
			$_date_min_max_arry[] = strtotime($_date);
		}
		$_this_date = strtotime($_date);

		$_name = iconv("euc-kr","utf-8",$row[1]);
		$_phone = iconv("euc-kr","utf-8",$row[2]);
		$_prdCode = $row[3];
		$_prdCodeSub = $row[4];
		//$_prdName = iconv("euc-kr","utf-8",$row[5]);
		$_prdName = $row[5];

		$_make_code = $_prdCode."-".$_prdCodeSub;

		if( ${'ch_count_'.$_prdCode} ){
			${'ch_count_'.$_prdCode}++;
		}else{
			${'ch_count_'.$_prdCode} = 1;
			${'ch_mycode_'.$_prdCode} = $_prdCodeSub;
			${'ch_prdName_'.$_prdCode} = securityVal($_prdName);
			$sms_pd[] = $_prdCode;
		}

		if( ${'ch_min_date_'.$_prdCode} < $_this_date ){
			${'ch_min_date_'.$_prdCode} = $_this_date;
		}

	} //while END

	for ($i=1; $i<count($sms_pd); $i++){
		
		$_show_code = $sms_pd[$i];
		$_mycode = ${'ch_mycode_'.$_show_code};
		$_brand = ${'ch_brand_'.$_show_code};
		$_prdname = ${'ch_prdName_'.$_show_code};
		$_count = ${'ch_count_'.$_show_code};
		$_min_date = ${'ch_min_date_'.$_show_code};

		$prd_data = wepix_fetch_array(wepix_query_error("select 
			stock.ps_idx, 
			prd.CD_IDX,
			brand.BD_NAME 
			from prd_stock stock 
			left join "._DB_COMPARISON." prd ON (stock.ps_prd_idx = prd.CD_IDX ) 
			left join "._DB_BRAND." brand  ON (prd.CD_BRAND_IDX = brand.BD_IDX ) 
			where ps_idx = '".$_mycode."' "));

		//$_json_arry[] = '{"code":"'.$_show_code.'","mycode":"'.$_mycode.'","brand":"'.$prd_data[BD_NAME].'","cdidx":"'.$prd_data[CD_IDX].'","prdname":"'.$_prdname.'","count":"'.$_count.'","mindate":"'.$_min_date.'"}';
	
		$_json_arry[] = array(
			'code' => $_show_code,
			'mycode' => $_mycode,
			'brand' => $prd_data[BD_NAME],
			'cdidx' => $prd_data[CD_IDX],
			'prdname' => $_prdname,
			'count' => $_count,
			'mindate' => $_min_date
		);

		if( $prd_data[ps_idx] ){
			$_ps_cafe24_sms = json_encode(array('count'=>$_count, 'date'=>$action_time));
			wepix_query_error("update prd_stock set ps_cafe24_sms = '".$_ps_cafe24_sms."' WHERE ps_idx = ".$prd_data[ps_idx]." ");
		}
	}

	//$_count = count($sms_pd);
	$_count = $count;
	
	$_json_data_implode = json_encode($_json_arry, JSON_UNESCAPED_UNICODE);

	$_json_data = json_decode($_json_data_implode,true);
	//$_json_data = arr_sort( $_json_data,'count', 'desc' );

	$s_date = min($_date_min_max_arry);
	$e_date = max($_date_min_max_arry);

	$query = "insert cafe24_sms set
		data = '".$_json_data_implode."',
		reg_time = '".$action_time."',
		s_date = '".$s_date."',
		e_date = '".$e_date."',
		reg_id = '".$_ad_id."',
		count = '".$_count."',
		file_name = '".$_file_name."' ";
	wepix_query_error($query);

	$_key = mysqli_insert_id($connect);

	msg("등록 완료!", "cafe24_sms.php?idx=".$_key);

}
?>