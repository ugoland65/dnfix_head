<?

	$_reg_d = array( "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 그룹핑 생성
if( $_a_mode == "prdGrouping_reg" ){

	if( !$_reg_mode ) $_reg_mode = "basic";
	if( $_reg_mode == "make_prouping" ){
	}

	$_reg_data = array(
		"reg_mode" => $_reg_mode,
		"d" => $_reg_d
	);
	$_reg = json_encode($_reg_data, JSON_UNESCAPED_UNICODE);

	$query = "insert prd_grouping set
		pg_subject = '".$_pg_subject."',
		pg_mode = '".$_pg_mode."',
		pg_sday = '".$_pg_sday."',
		pg_day = '".$_pg_day."',
		reg = '".$_reg."' ";
	sql_query_error($query);
	
	$_key = mysqli_insert_id($connect);

	$response = array('success' => true, 'msg' => '완료', 'key' => $_key );

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품/노출순서 저장
}elseif( $_a_mode == "prdGrouping_prd_inout" ){

	$data = sql_fetch_array(sql_query_error("select * from prd_grouping WHERE idx = '".$_idx."' "));

	for ( $i=0; $i<count($_prd_idx); $i++ ){
		
		$_this_idx = $_prd_idx[$i];
		$_this_ps_idx = $_ps_idx[$i];
		$_this_mode_data = json_decode($mode_data[$i], true);
		$_this_pname = $_prd_name[$i];
		$_this_memo = $_memo[$i];

		$_data_array[] = array(
			"idx" => $_this_idx,
			"stockidx" => $_this_ps_idx,
			"pname" => $_this_pname,
			"mode_data" => $_this_mode_data,
			"memo" => $_this_memo
		);

	}

	$_data = json_encode($_data_array, JSON_UNESCAPED_UNICODE);

	$query = "update prd_grouping set
		data = '".$_data."'
		where idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 그룹핑 수정
}elseif( $_a_mode == "prdGrouping_modify" ){

	$data = sql_fetch_array(sql_query_error("select * from prd_grouping WHERE idx = '".$_idx."' "));

	$_prd_jsondata = json_decode($data['data'], true);

	for ($z=0; $z<count($_prd_jsondata); $z++){

		if( $data['pg_mode'] == "qty" ){

			$_prd_jsondata[$z]['mode_data']['qty'] = $_pg_prd_qty[$z];
		
		}elseif( $data['pg_mode'] == "event" || $data['pg_mode'] == "sale" || $data['pg_mode'] == "period" ){

			$_prd_jsondata[$z]['mode_data'] = array(
				"per" => $_pg_prd_per[$z],
				"sale_price" => $_dis_sale_price[$z],
				"margin_price" => $_dis_margin_price[$z],
				"margin_per" => $_dis_margin_per[$z]
			);

			if( ( $data['pg_mode'] == "sale" || $data['pg_mode'] == "period" ) && $_save_mode == "end" ){
				
				if( $data['pg_mode'] == "period" && $_pg_sday == 0 ){

					$response = array('success' => false, 'msg' => '진행 시작일을 등록해 주세요.' );
					header('Content-Type: application/json');
					echo json_encode($response); 
					exit;

				}

				if( $_pg_day == 0 ){

					$response = array('success' => false, 'msg' => '진행일을 등록해 주세요.' );
					header('Content-Type: application/json');
					echo json_encode($response); 
					exit;

				}


				$_ps_idx = $_prd_jsondata[$z]['stockidx'];
				$ps_data = sql_fetch_array(sql_query_error("select ps_sale_date, ps_sale_log from prd_stock WHERE ps_idx = '".$_ps_idx."' "));

				$_ps_sale_log_data = json_decode($ps_data['ps_sale_log'], true);


				$_is_log = true;

				for ($i=0; $i<count($_ps_sale_log_data); $i++){
					
					//등록된 로그가 있는지?
					if( $_ps_sale_log_data[$i]['grouping_idx'] == $_idx && $_ps_sale_log_data[$i]['sale_per'] == $_pg_prd_per[$z] ){
						$_is_log = false;
						break;
					}

				}

				if( $ps_data['ps_sale_date'] > $_pg_day ){
					$_ps_sale_date = $ps_data['ps_sale_date'];
				}else{
					$_ps_sale_date = $_pg_day;
				}

				//일일할인 일경우
				if( $data['pg_mode'] == "sale" ){
					$_sale_mode = "day";

					$_ps_in_sale_s = date("Y-m-d",strtotime($_pg_day))." 17:00:00";
					$_ps_in_sale_e = date("Y-m-d",strtotime($_pg_day." +1 days"))." 17:00:00";

				//기간할인 일경우
				}elseif( $data['pg_mode'] == "period" ){
					$_sale_mode = "period";

					$_ps_in_sale_s = date("Y-m-d",strtotime($_pg_sday))." 17:00:00";
					$_ps_in_sale_e = date("Y-m-d",strtotime($_pg_day))." 17:00:00";
				}

				//기간 period 데이 day
				$_sale_log_unit = array(
					"sale_mode" => $_sale_mode,
					"grouping_idx" => $_idx,
					"pg_subject" => $data['pg_subject'],
					"pg_sday" => $_pg_sday,
					"pg_day" => $_pg_day,
					"sale_per" => $_pg_prd_per[$z],
					"original_price" => $_original_sale_price[$z],
					"sale_price" => $_dis_sale_price[$z],
					"margin_price" => $_dis_margin_price[$z],
					"margin_per" => $_dis_margin_per[$z],
					"d" => $_reg_d
				);

				if( $_is_log == true ){
					if(is_array($_ps_sale_log_data)){
						array_unshift($_ps_sale_log_data, $_sale_log_unit);
					}else{
						$_ps_sale_log_data = array($_sale_log_unit);
					}

				}

				$_ps_sale_log = json_encode($_ps_sale_log_data, JSON_UNESCAPED_UNICODE);
				$_ps_in_sale_data = json_encode($_sale_log_unit, JSON_UNESCAPED_UNICODE);

				$query = "update prd_stock set
					ps_sale_date = '".$_ps_sale_date."',
					ps_sale_log = '".$_ps_sale_log."',
					ps_in_sale_s = '".$_ps_in_sale_s."',
					ps_in_sale_e = '".$_ps_in_sale_e."',
					ps_in_sale_data = '".$_ps_in_sale_data."'
					where ps_idx = '".$_ps_idx."' ";
				sql_query_error($query);

			}

		}

		$_prd_jsondata[$z]['memo'] = $_pg_prd_memo[$z];

	}

	$_data = json_encode($_prd_jsondata, JSON_UNESCAPED_UNICODE);

	if( $_save_mode == "end" ){
		$_pg_state = "마감";
	}

	$query = "update prd_grouping set
		pg_subject = '".$_pg_subject."',
		pg_state = '".$_pg_state."',
		pg_sday = '".$_pg_sday."',
		pg_day = '".$_pg_day."',
		pg_memo = '".$_pg_memo."',
		data = '".$_data."'
		where idx = '".$_idx."' ";
	sql_query_error($query);

	$response = array('success' => true, 'msg' => '완료' );


////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 그룹핑 상품로드
}elseif( $_a_mode == "prdGrouping_prd_load" ){


	$_jungbok = 0;
	//-------------------------------------------------------------------------------------------------------------------------------
	if( $_load_mode == "saved_prd" ){

		$data = sql_fetch_array(sql_query_error("select * from prd_grouping WHERE idx = '".$_idx."' "));
		$_prd_jsondata = json_decode($data['data'], true);

		for ($z=0; $z<count($_prd_jsondata); $z++){

			$_is_jungbok = "no";

			$_prd_idx = $_prd_jsondata[$z]['idx'];
			$_ps_idx = $_prd_jsondata[$z]['stockidx'];
			$_mode_data = $_prd_jsondata[$z]['mode_data'];
			$_pname = $_prd_jsondata[$z]['pname'];

			//중복 여부 체크하기
			for ($i=0; $i<count($_new_add_array); $i++){
				if( $_new_add_array[$i] == $_prd_idx ){
					$_is_jungbok = "ok";
					$_jungbok++;
					break;
				}
			}
			
			//중복이라면 z for문 스킵하기
			if( $_is_jungbok == "ok" ){
				continue;
			}

			if( $_prd_idx == "Instant" ){
			}else{

				$_colum = "A.CD_IDX, A.CD_IMG, A.CD_CODE2, A.CD_CODE3, A.CD_NAME, A.CD_KIND_CODE, A.cd_code_fn, A.cd_sale_price, A.cd_cost_price";
				$_colum .= ", B.ps_idx";
				$_colum .= ", C.BD_NAME";

				$_query = "select ".$_colum." from "._DB_COMPARISON." A
					left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX ) 
					left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND_IDX  ) 
					where CD_IDX = '".$_prd_idx."' ";

				$prd_data = sql_fetch_array(sql_query_error($_query));

				if( $prd_data['CD_IMG'] ){
					$img_path = '/data/comparion/'.$prd_data['CD_IMG'];
				}

				$_code2 = $prd_data['CD_CODE2'];
				$_code3 = $prd_data['CD_CODE3'];

				$_pname = $prd_data['CD_NAME'];

				$_cd_code_data = json_decode($prd_data['cd_code_fn'], true);

				$_jancode = $_cd_code_data['jan'];

				if( $prd_data['cd_sale_price'] < 29999 ){
					$_margin_per =  round( ($prd_data['cd_sale_price'] - $prd_data['cd_cost_price'] ) / $prd_data['cd_sale_price'] * 100, 2);
				}else{
					$_margin_per =  round( ($prd_data['cd_sale_price'] - ($prd_data['cd_cost_price'] + 2500) ) / $prd_data['cd_sale_price'] * 100, 2);
				}

			}

			$_prd_data[] = array(
				"idx" => $_prd_idx,
				"ps_idx" => $prd_data['ps_idx'],
				"mode_data" => $_mode_data,
				"pname" => $_pname,
				"img_path" => $img_path,
				"code2" => $_code2,
				"code3" => $_code3,
				"jancode" => $_jancode,
				"brandname" => $prd_data['BD_NAME'],
				"sale_price" => $prd_data['cd_sale_price'],
				"cost_price" => $prd_data['cd_cost_price'],
				"margin_per" => $_margin_per
			);

		} // for END

	//-------------------------------------------------------------------------------------------------------------------------------
	}elseif( $_load_mode == "new_add" ){

/*
	$response = array('success' => false, 'msg' => count($_new_add_array) );
	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;
*/
		for ($z=0; $z<count($_new_add_array); $z++){

			$_prd_idx = $_new_add_array[$z];

			$_colum = "A.CD_IDX, A.CD_IMG, A.CD_CODE2, A.CD_CODE3, A.CD_NAME, A.CD_KIND_CODE, A.cd_code_fn, A.cd_sale_price, A.cd_cost_price";
			$_colum .= ", B.ps_idx";
			$_colum .= ", C.BD_NAME";

			$_query = "select ".$_colum." from "._DB_COMPARISON." A
				left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX ) 
				left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND_IDX  ) 
				where CD_IDX = '".$_prd_idx."' ";

			$prd_data = sql_fetch_array(sql_query_error($_query));

			if( $prd_data['CD_IMG'] ){
				$img_path = '/data/comparion/'.$prd_data['CD_IMG'];
			}

			$_code2 = $prd_data['CD_CODE2'];
			$_code3 = $prd_data['CD_CODE3'];

			$_pname = $prd_data['CD_NAME'];

			$_cd_code_data = json_decode($prd_data['cd_code_fn'], true);

			$_jancode = $_cd_code_data['jan'];

			if( $prd_data['cd_sale_price'] > 0 && $prd_data['cd_cost_price'] > 0 ){
				if( $prd_data['cd_sale_price'] < 29999 ){
					$_margin_per =  round( ($prd_data['cd_sale_price'] - $prd_data['cd_cost_price'] ) / $prd_data['cd_sale_price'] * 100, 2);
				}else{
					$_margin_per =  round( ($prd_data['cd_sale_price'] - ($prd_data['cd_cost_price'] + 2500) ) / $prd_data['cd_sale_price'] * 100, 2);
				}
			}

			$_prd_data[] = array(
				"idx" => $_prd_idx,
				"ps_idx" => $prd_data['ps_idx'],
				"mode_data" => $_mode_data,
				"pname" => $_pname,
				"img_path" => $img_path,
				"code2" => $_code2,
				"code3" => $_code3,
				"jancode" => $_jancode,
				"brandname" => $prd_data['BD_NAME'],
				"sale_price" => $prd_data['cd_sale_price'],
				"cost_price" => $prd_data['cd_cost_price'],
				"margin_per" => $_margin_per
			);

		}// for END

	}

	$response = array( 'success' => true, 'msg' => '완료', 'jungbok' => $_jungbok, 'prd_data' => $_prd_data );

}

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;
?>