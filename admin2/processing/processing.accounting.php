<?



////////////////////////////////////////////////////////////////////////////////////////////////
// 운영자금 계획 등록
if( $_a_mode == "moneyPlan_reg" ){

	$query = "insert money_plan set
		category = '".$_category."',
		name = '".$_name."',
		memo = '".$_memo."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 운영자금 계획 수정
}elseif( $_a_mode == "moneyPlan_modify" ){

	$query = "UPDATE money_plan SET 
		category = '".$_category."',
		name = '".$_name."',
		memo = '".$_memo."'
		WHERE idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 운영자금 금액변동 입력
}elseif( $_a_mode == "moneyPlan_history" ){

	$data = sql_fetch_array(sql_query_error("select * from money_plan WHERE idx = '".$_idx."' "));

	$_price = (int)str_replace(',','', $_price);

	if( $_mode == "plus" ){
		$_total = $data['balance'] + $_price;
	}elseif( $_mode == "minus"){
		$_total = $data['balance'] - $_price;
	}

	$_ary_reg = array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );
	$_reg = json_encode($_ary_reg, JSON_UNESCAPED_UNICODE);

	$query = "insert money_plan_history set
		tidx = '".$_idx."',
		mode = '".$_mode."',
		price = '".$_price."',
		total = '".$_total."',
		date = '".$_date."',
		reg = '".$_reg."',
		memo = '".$_memo."' ";
	sql_query_error($query);

	$query = "UPDATE money_plan SET 
		balance = '".$_total."',
		update_data = '".$_reg."'
		WHERE idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 입출금 엑셀등록
}elseif( $_a_mode == "bankStatementExcelUpload" ){

	$_imgfile = $_FILES['fileObj']['name'];
	$_tmpfile = $_FILES['fileObj']['tmp_name'];

	if ( $_imgfile ) {
		if (!$_FILES['fileObj']['error']) {

			//확장자
			$extension = pathinfo($_imgfile, PATHINFO_EXTENSION);

			if( $extension != "csv" ){

				$response = array('success' => false, 'msg' => 'csv 파일만 등록 가능합니다.' );
				header('Content-Type: application/json');
				echo json_encode($response); 
				exit;

			}

		}
	}

	setlocale(LC_CTYPE, 'ko_KR.eucKR'); 
	extract($_FILES['fileObj']);

	$fp = fopen($tmp_name, 'r'); 

	$count = 0;
	$count1 = 0;
	$count2 = 0;

/*
	$_file_check_val = "";
	$_text_count = 0;

	//파일검수
	while ($row = fgetcsv($fp, 50, ',')) { 
		$_text_count++;
		$_file_check_val = iconv("euc-kr","utf-8",$row[3]); // F
		//$_file_check_val .= '('.$_text_count.')'.iconv("euc-kr","utf-8",$row[3]).'/'; // F

		if( $_text_count == 5 ){
			break;
		}
	}

	if( $_file_check_val != "적요" ){

		$response = array('success' => false, 'msg' => '파일 양식이 잘못된거 같습니다.<br>파일은 [상세약식]이여야 합니다.' );
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;

	}
*/



	while ($row = fgetcsv($fp, 10000, ',')) { 
		$count++;

		$date = new DateTimeImmutable($row[0]); // A
		$_date = $date->format('Y-m-d H:i:s');

		$_bank_name = iconv("euc-kr","utf-8",$row[1]); // B
		
		//$_bank_num = '="'.$row[2].'"'; 
		//$_bank_num =sprintf("%.0f", $row[2]); // C
		$_bank_num = iconv("euc-kr","utf-8",$row[2]); // C

		$_name = iconv("euc-kr","utf-8",$row[3]);  // D

		$_in_money = $row[6];  // G
		$_in_money = str_replace('.','', $_in_money);
		$_in_money = (int)str_replace(',','', $_in_money);

		$_out_money = $row[7]; // H
		$_out_money = str_replace('.','', $_out_money);
		$_out_money = (int)str_replace(',','', $_out_money);

		$_balance_money = (int)str_replace(',','', $row[8]);

		if( $_in_money > 0 ){
			$_bs_mode = "plus";
		}elseif( $_out_money > 0 ){
			$_bs_mode = "minus";
		}

		$_bank_array = array(
			"bank" => $_bank_name,
			"num" => $_bank_num
		);

		$_bank = json_encode($_bank_array, JSON_UNESCAPED_UNICODE);

		$data = sql_fetch_array(sql_query_error("select idx from bank_statement WHERE 
			bank = '".$_bank."' AND name = '".$_name."' AND date = '".$_date."' AND in_money = '".$_in_money."' AND 
			out_money = '".$_out_money."' AND balance_money = '".$_balance_money."' AND bs_mode = '".$_bs_mode."' "));

		if( $data['idx'] ){
			
			$count1++;

		}else{
			
			//데이터가 존재할경우에만
			if( $_name ){
				$count2++;

				$_data_data = array(
					"bank_info" => array(
						"mode" => iconv("euc-kr","utf-8",$row[4]),
						"branch" => iconv("euc-kr","utf-8",$row[5])
					)
				);
				$_data = json_encode($_data_data, JSON_UNESCAPED_UNICODE);

				$_reg_data = array(
					"reg_mode" => "excel_cafe24",
					"d" => $_reg_d
				);
				$_reg = json_encode($_reg_data, JSON_UNESCAPED_UNICODE);
					
				$query = "insert bank_statement set
					bank = '".$_bank."',
					name = '".$_name."',
					date = '".$_date."',
					in_money = '".$_in_money."',
					out_money = '".$_out_money."',
					balance_money = '".$_balance_money."',
					bs_mode = '".$_bs_mode."',
					data = '".$_data."',
					reg = '".$_reg."' ";
				sql_query_error($query);
			}
		}

	}

	$response = array('success' => true, 'msg' => '완료', 'count' => $count, 'count1' => $count1, 'count2' => $count2 );
	//$response = array('success' => false, 'msg' => '완료-'.$count );

////////////////////////////////////////////////////////////////////////////////////////////////
// 입출금 상세처리
}elseif( $_a_mode == "bankStatement_modify" ){

	$query = "UPDATE bank_statement SET 
		ledge_cate_idx = '".$_ledge_cate."',
		state = '".$_state."',
		memo = '".$_memo."'
		WHERE idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 입출금 일괄 상세처리
}elseif( $_a_mode == "bankStatement_batch_process" ){

	$_log_data = array(
		"log_mode" => "batch_process",
		"d" => $_reg_d
	);
	$_log = json_encode($_log_data, JSON_UNESCAPED_UNICODE);

	for ($i=0; $i<count($_chk_idx); $i++){
		
		$_idx = $_chk_idx[$i];
		
		$query = "UPDATE bank_statement SET 
			ledge_cate_idx = '".$_batch_process_cate."',
			state = 'Y',
			memo = '".$_batch_process_memo."',
			log = '".$_log."'
			WHERE idx = '".$_idx."' ";
		sql_query_error($query);

	}

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 입출금 항목등록
}elseif( $_a_mode == "ledgeCategoryReg" ){

	$_lc_depth = "1";
	$_lc_ancestor = "";

	$query = "insert ledge_category set
		lc_mode = '".$_lc_mode."',
		lc_name = '".$_lc_name."',
		lc_approval = '".$_lc_approval."',
		lc_depth = '".$_lc_depth."',
		lc_ancestor = '".$_lc_ancestor."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 입출금 항목 불러오기
}elseif( $_a_mode == "ledgeCateLoad" ){

	$_where = "WHERE lc_mode = '".$_kind."' ";
	$_query = "select * from ledge_category ".$_where." ORDER BY idx DESC";
	$_result = sql_query_error($_query);
	while($list = sql_fetch_array($_result)){

		$_ledge_category[] = array(
			"idx" => $list['idx'],
			"name" => $list['lc_name']
		);

	}

	$response = array('success' => true, 'msg' => '완료', 'ledge_cate' => $_ledge_category );

////////////////////////////////////////////////////////////////////////////////////////////////
// 일일마감
}elseif( $_a_mode == "day_work_end" ){

	$data = sql_fetch_array(sql_query_error("select * from day_end WHERE day_code = '".$_day_code."' "));

	if( $data['idx'] ){

		$response = array('success' => false, 'msg' => '해당일에 이미 등록되어 있습니다.' );
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;

	}

	for ($i=0; $i<count($_brand_name); $i++){

		$_brand[] = array(
			"idx" => $_brand_idx[$i],
			"brand_name" => $_brand_name[$i],
			"cost_count1" => $_cost_count1[$i],
			"cost_count2" => $_cost_count2[$i],
			"cost_price_sum" => $_cost_price_sum[$i],
			"sale_price_sum" => $_sale_price_sum[$i]
		);

	}

	$_dd = array(
		"total_count_sum1" => $_total_count_sum1,
		"total_count_sum2" => $_total_count_sum2,
		"total_cost_price_sum" => $_total_cost_price_sum,
		"total_sale_price_sum" => $_total_sale_price_sum,
		"data" => $_brand
	);

	$_day_data = json_encode($_dd, JSON_UNESCAPED_UNICODE);
	$_reg = json_encode($_reg_d, JSON_UNESCAPED_UNICODE);

	$query = "insert day_end set
		day_code = '".$_day_code."',
		day_data = '".$_day_data."',
		count1 = '".$_total_count_sum1."',
		count2 = '".$_total_count_sum2."',
		cost_price = '".$_total_cost_price_sum."',
		sale_price = '".$_total_sale_price_sum."',
		memo = '".$_memo."',
		reg = '".$_reg."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

?>