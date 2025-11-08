<?php

// 변수 초기화
$_a_mode = $_POST['a_mode'] ?? $_GET['a_mode'] ?? "";

////////////////////////////////////////////////////////////////////////////////////////////////
// 일일 재고관리 (엑셀)
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_a_mode == "day_stock" ){

	$_stock_key = $_POST['stock_key'] ?? [];
	$_stock_mode = $_POST['stock_mode'] ?? [];
	$_stock_qry = $_POST['stock_qry'] ?? [];
	$_stock_kind = $_POST['stock_kind'] ?? [];
	$_stock_memo = $_POST['stock_memo'] ?? [];
	$_stock_day = $_POST['stock_day'] ?? "";
	$_stock_history_idx = $_POST['stock_history_idx'] ?? "";

	// 배열 검증
	if (!is_array($_stock_key)) $_stock_key = [];
	if (!is_array($_stock_mode)) $_stock_mode = [];
	if (!is_array($_stock_qry)) $_stock_qry = [];
	if (!is_array($_stock_kind)) $_stock_kind = [];
	if (!is_array($_stock_memo)) $_stock_memo = [];

	$_reg_data = array(
		"reg" => array(
			"mode" => "stock_excel",
			"info" => $_reg_d
		)
	);
	$_reg = json_encode($_reg_data, JSON_UNESCAPED_UNICODE);

	$_shortage = [];
	$_soldout = [];
	$_stock_alarm = [];

	for ($i=0; $i<count($_stock_key); $i++){
	
		//$_psu_stock_idx = $_stock_key[$i];
		$_ps_idx = $_stock_key[$i] ?? "";
		$_psu_mode = $_stock_mode[$i] ?? "";
		$_psu_qry = $_stock_qry[$i] ?? 0;
		$_psu_kind = $_stock_kind[$i] ?? "";
		$_psu_memo = $_stock_memo[$i] ?? "";

		//수량이 있을경우
		if( $_psu_qry > 0 ){

			$prd_data = sql_fetch_array(sql_query_error("select 
				A.ps_prd_idx, A.ps_rack_code, A.ps_stock, A.ps_stock_object, A.ps_alarm_count,
				B.CD_NAME, B.CD_IMG, B.CD_CODE, B.cd_national,
				C.BD_NAME 
				from prd_stock A 
				left join "._DB_COMPARISON." B ON (A.ps_prd_idx = B.CD_IDX ) 
				left join "._DB_BRAND." C  ON (B.CD_BRAND_IDX = C.BD_IDX ) 
				where ps_idx = '".$_ps_idx."' "));
		
			// 배열 검증
			if (!is_array($prd_data)) {
				$prd_data = ['ps_stock' => 0, 'ps_stock_object' => 'N', 'ps_alarm_count' => 0, 'CD_NAME' => '', 'cd_national' => ''];
			}

			$_prd_stock_count = 0;

			if( $_psu_mode == "plus" ){
				$_prd_stock_count = ($prd_data['ps_stock'] ?? 0) + $_psu_qry;
			
			}elseif($_psu_mode == "minus"){
				$_prd_stock_count = ($prd_data['ps_stock'] ?? 0) - $_psu_qry;

				//재고부족
				if( $_prd_stock_count < 0 && ($prd_data['ps_stock_object'] ?? 'N') == "Y" ){
				
					if( ($prd_data['cd_national'] ?? '') == "kr" ){
						$_shortage['domestic'][] = array(
							"name" => $prd_data['CD_NAME'] ?? "",
							"count" => $_prd_stock_count
						);
					}else{
						$_shortage['import'][] = array(
							"name" => $prd_data['CD_NAME'] ?? "",
							"count" => $_prd_stock_count
						);
					}
				
				//품절상황
				}elseif( $_prd_stock_count == 0 ){
				
					if( ($prd_data['cd_national'] ?? '') == "kr" ){
						$_soldout['domestic'][] = array(
							"name" => $prd_data['CD_NAME'] ?? "",
							"count" => $_prd_stock_count
						);
					}else{
						$_soldout['import'][] = array(
							"name" => $prd_data['CD_NAME'] ?? "",
							"count" => $_prd_stock_count
						);
					}

				}

				//재고관리 대상
				if( ($prd_data['ps_alarm_count'] ?? 0) > 0 && $_prd_stock_count < ($prd_data['ps_alarm_count'] ?? 0) && ($prd_data['ps_stock_object'] ?? 'N') == "Y" ){
					$_stock_alarm[] = array(
						"name" => $prd_data['CD_NAME'] ?? "",
						"count" => $_prd_stock_count
					);
				}


				if( $_prd_stock_count < 1 ){
					$_ps_soldout_date = $action_time;
				}else{
					$_ps_soldout_date = 0;
				}

				$query = "UPDATE prd_stock set 
					ps_stock = ps_stock - ".$_psu_qry.", 
					ps_update_date = '".$action_time."',
					ps_last_date = '".$action_time."',
					ps_soldout_date = '".$_ps_soldout_date."'
					where ps_idx = '".$_ps_idx."' ";
				sql_query_error($query);
				
			}

			$query = "insert prd_stock_unit set
				psu_stock_idx = '".$_ps_idx."',
				psu_day = '".$_stock_day."',
				psu_mode = '".$_psu_mode."',
				psu_qry = '".$_psu_qry."',
				psu_stock = '".$_prd_stock_count."',
				psu_kind = '".$_psu_kind."',
				psu_memo = '".$_psu_memo."',
				psu_id = '".$_ad_id."',
				psu_date = '".$check_time ."',
				reg = '".$_reg."' ";
			sql_query_error($query);

		} // if( $_psu_qry > 0 ){ --- 수량이 있을경우

	} // for END


	$talk_massage1 = "";

	if( is_array($_shortage['domestic']) ){
		$talk_massage1 .= "★★ 재고부족 - 국내 ★★\n\n";
		foreach ( $_shortage['domestic'] as $key => $val ){
			$talk_massage1 .= "(".$val['name'].") : ".$val['count']."\n";
		}
		$talk_massage1 .= "\n---------------------------\n\n";
	}

	if( is_array($_shortage['import']) ){
		$talk_massage1 .= "★★ 재고부족 - 수입 ★★\n\n";
		foreach ( $_shortage['import'] as $key => $val ){
			$talk_massage1 .= "(".$val['name'].") : ".$val['count']."\n";
		}
	}

	if( $talk_massage1 ){

		$talk_massage = "";
		$talk_massage .= "\n(".$_stock_day.") 재고부족\n";
		$talk_massage .= "————————————\n\n";
		$talk_massage .= $talk_massage1;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: Bearer oVmeDHRjttiAEZ6nQxIxokIN5uc6j5zaZL9gUin4lei' , 'content-type: application/x-www-form-urlencoded' ));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT,30);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"message=".$talk_massage);
		$res = curl_exec($ch);
		curl_close($ch);

	}


	$talk_massage2 = "";

	if( is_array($_soldout['domestic']) ){
		$talk_massage2 .= "★★ 품절 - 국내 ★★\n\n";
		foreach ( $_soldout['domestic'] as $key => $val ){
			$talk_massage2 .= "(".$val['name'].") : 품절\n";
		}
		$talk_massage2 .= "\n---------------------------\n\n";
	}

	if( is_array($_soldout['import']) ){
		$talk_massage2 .= "★★ 품절 - 수입 ★★\n\n";
		foreach ( $_soldout['import'] as $key => $val ){
			$talk_massage2 .= "(".$val['name'].") : 품절\n";
		}
	}

	if( $talk_massage2 ){

		$talk_massage = "";
		$talk_massage .= "\n(".$_stock_day.") 품절\n";
		$talk_massage .= "————————————\n\n";
		$talk_massage .= $talk_massage2;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: Bearer oVmeDHRjttiAEZ6nQxIxokIN5uc6j5zaZL9gUin4lei' , 'content-type: application/x-www-form-urlencoded' ));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT,30);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"message=".$talk_massage);
		$res = curl_exec($ch);
		curl_close($ch);

	}


	$talk_massage3 = "";

	if( is_array($_stock_alarm) ){
		$talk_massage3 .= "★★ 재고관리 대상  ★★\n\n";
		foreach ( $_stock_alarm as $key => $val ){
			$talk_massage3 .= "(".$val['name'].") : ".$val['count']."\n";
		}
		$talk_massage3 .= "\n---------------------------\n\n";
	}

	if( $talk_massage3 ){

		$talk_massage = "";
		$talk_massage .= "\n(".$_stock_day.") 재고관리 대상\n";
		$talk_massage .= "————————————\n\n";
		$talk_massage .= $talk_massage3;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: Bearer oVmeDHRjttiAEZ6nQxIxokIN5uc6j5zaZL9gUin4lei' , 'content-type: application/x-www-form-urlencoded' ));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT,30);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"message=".$talk_massage);
		$res = curl_exec($ch);
		curl_close($ch);

	}


	if( $_stock_history_idx ){
		$query = "UPDATE prd_stock_history set
			step = '2',
			end_time = '".$action_time."'
			WHERE uid = '".$_stock_history_idx."' ";
		sql_query_error($query);
	}

	$response = array(
		'success' => true,
		'idx' => $_stock_history_idx,
		'msg' => '완료'
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 일일재고 엑셀 임시 등록하기
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="stock_excel" ){

	setlocale(LC_CTYPE, 'ko_KR.eucKR'); 
	
	// extract($_FILES['userfile']); 대신 안전하게 접근
	$name = $_FILES['userfile']['name'] ?? "";
	$tmp_name = $_FILES['userfile']['tmp_name'] ?? "";

	$_file_name = $name;

	/*
	$data = sql_fetch_array(sql_query_error("select uid from prd_stock_history where file_name = '".$_file_name."' "));
	if( $data['uid'] ){
		msg("이미 등록된 파일입니다.", "prd2_stock_excel.php");
		exit;
	}
	*/

	/*
	$fp2 = file($tmp_name, FILE_SKIP_EMPTY_LINES);
	echo count($fp2)."<br>";
	*/

	if (!$tmp_name || !file_exists($tmp_name)) {
		$response = array('success' => false, 'msg' => '파일을 찾을 수 없습니다.');
		header('Content-Type: application/json');
		echo json_encode($response); 
		exit;
	}

	$fp = fopen($tmp_name, 'r'); 
	$count = 0;
	$count2 = 0;
	$_error = [];
	$order_num = [];
	$stock_code = [];
	$stock_set_code = [];
	$_ary_st_show = [];


	//while ($row = fgetcsv($fp, 2000, ',')) { 
	while ($row = fgetcsv($fp)) { 

		$count++;

/*
		$_name = iconv("euc-kr","utf-8",$row[0]); //수령인
		$_phone = iconv("euc-kr","utf-8",$row[1]); //수령인 휴대전화
		$_phone2 = iconv("euc-kr","utf-8",$row[2]); //수령인 전화번호
		$_add = iconv("euc-kr","utf-8",$row[3]); //수령인 주소(전체)
		$_prdCode =  iconv("euc-kr","utf-8",$row[4]);  //상품자체코드
		$_prdCodeSub =  iconv("euc-kr","utf-8",$row[5]);  //자체품목코드
		$_qty = ($row[6]*1); //수량
		$_d_msg = iconv("euc-kr","utf-8",$row[7]); //배송메시지
		$_orderNum = $row[8]; //주문번호

		$_option = iconv("euc-kr","utf-8",$row[12]);
*/
    // EUC-KR에서 UTF-8로 안전하게 변환
    $_name = mb_convert_encoding($row[0], "UTF-8", "EUC-KR"); // 수령인
    $_phone = mb_convert_encoding($row[1], "UTF-8", "EUC-KR"); // 수령인 휴대전화
    $_phone2 = mb_convert_encoding($row[2], "UTF-8", "EUC-KR"); // 수령인 전화번호
    $_add = mb_convert_encoding($row[3], "UTF-8", "EUC-KR"); // 수령인 주소(전체)
    $_prdCode = mb_convert_encoding($row[4], "UTF-8", "EUC-KR"); // 상품자체코드
    $_prdCodeSub = mb_convert_encoding($row[5], "UTF-8", "EUC-KR"); // 자체품목코드
    $_qty = ($row[6] * 1); // 수량
    $_d_msg = mb_convert_encoding($row[7], "UTF-8", "EUC-KR"); // 배송메시지
    $_orderNum = mb_convert_encoding($row[8], "UTF-8", "EUC-KR"); // 주문번호
    $_option = mb_convert_encoding($row[12], "UTF-8", "EUC-KR"); // 옵션

 		//첫줄제거
		if( $count > 1 && $_name != "" ){

			$order_num[] = $_orderNum;
			$count2++;

			//숫자로만 된 코드일경우( 단일상품 )
			if (is_numeric($_prdCode)) {
			
				${'order_num_'.$_prdCode}[] = array('num'=>$_orderNum, 'qty'=> $_qty);

				if( ${'stock_'.$_prdCode} > 0 ){
					${'stock_'.$_prdCode} = ${'stock_'.$_prdCode} + $_qty;
				}else{

					$stock_data = sql_fetch_array(sql_query_error("select ps_idx from prd_stock where ps_idx = '".$_prdCode."' "));
					
					if( $stock_data['ps_idx'] ){
						$stock_code[] = $_prdCode;
						${'stock_'.$_prdCode} = $_qty;
					}else{
						$_error[] = "[".$count."] ".$_name." / ".$_phone." |  (".$_prdCode.")  재고 상품 데이터 없음";
					}
				}

				if ( strpos($_option,"패키지 제거 여부 : 패키지 제거") !== false ){
					if( ${'packageOut_'.$_prdCode} > 0 ){
						${'packageOut_'.$_prdCode} = ${'packageOut_'.$_prdCode} + $_qty;
					}else{
						${'packageOut_'.$_prdCode} = $_qty;
					}
				}

			//코드값이 비어있는경우
			}elseif($_prdCode == ""){
			
				$_error[] = "[".$count."] ".$_name." / ".$_phone." |  상품자체코드 값 없음";

			}else{

				if(strpos($_prdCode, "set") !== false) {  
					if( $_prdCodeSub ){

						$_ary_prdCodeSub = explode("/", $_prdCodeSub);
						for ($i=0; $i<count($_ary_prdCodeSub); $i++){
							if( $_ary_prdCodeSub[$i] ){

								$_set_prdCode = $_ary_prdCodeSub[$i];

								${'order_set_num_'.$_set_prdCode}[] = array('num'=>$_orderNum, 'qty'=> $_qty);

								if( ${'stock_set_'.$_set_prdCode} > 0 ){
									${'stock_set_'.$_set_prdCode} = ${'stock_set_'.$_set_prdCode} + $_qty;
								}else{
									$stock_data = sql_fetch_array(sql_query_error("select ps_idx from prd_stock where ps_idx = '".$_set_prdCode."' "));
									if( $stock_data['ps_idx'] ){
										$stock_set_code[] = $_set_prdCode;
										${'stock_set_'.$_set_prdCode} = $_qty;
									}else{
										$_error[] = "[".$count."] ".$_name." / ".$_phone." |  (".$_set_prdCode.")  세트 재고 상품 데이터 없음";
									}
								}

								if (strpos($_option,"패키지 제거 여부 : 패키지 제거") !== false ){
									if( ${'packageOut_set_'.$_prdCode} > 0 ){
										${'packageOut_set_'.$_prdCode} = ${'packageOut_set_'.$_prdCode} + $_qty;
									}else{
										${'packageOut_set_'.$_prdCode} = $_qty;
									}
								}

							}
						}
					}

				}elseif(strpos($_prdCode, "one") !== false) {  

						$_ary_prdCode1 = explode("@", $_prdCode);
						$_ary_prdCode2 = explode("/", $_ary_prdCode1[1]);
						
						for ($i=0; $i<count($_ary_prdCode2); $i++){

							$_set_prdCode = $_ary_prdCode2[$i];
							
							${'order_set_num_'.$_set_prdCode}[] = array('num'=>$_orderNum, 'qty'=> $_qty);

							if( ${'stock_set_'.$_set_prdCode} > 0 ){
								${'stock_set_'.$_set_prdCode} = ${'stock_set_'.$_set_prdCode} + $_qty;
							}else{
								$stock_data = sql_fetch_array(sql_query_error("select ps_idx from prd_stock where ps_idx = '".$_set_prdCode."' "));
								if( $stock_data['ps_idx'] ){
									$stock_set_code[] = $_set_prdCode;
									${'stock_set_'.$_set_prdCode} = $_qty;
								}else{
									$_error[] = "[".$count."] ".$_name." / ".$_phone." |  (".$_set_prdCode.")  세트 재고 상품 데이터 없음";
								}
							}

							if (strpos($_option,"패키지 제거 여부 : 패키지 제거") !== false ){
								if( ${'packageOut_set_'.$_prdCode} > 0 ){
									${'packageOut_set_'.$_prdCode} = ${'packageOut_set_'.$_prdCode} + $_qty;
								}else{
									${'packageOut_set_'.$_prdCode} = $_qty;
								}
							}

						}

				}elseif(strpos($_prdCode, "qty") !== false) {  
				
					if( $_prdCodeSub ){

						$_ary_prdCodeSub = explode("@", $_prdCodeSub);
						$_this_code = $_ary_prdCodeSub[0]*1;
						$_this_qty = ($_ary_prdCodeSub[1]*1)*$_qty;

						$_set_prdCode = $_this_code;

						${'order_set_num_'.$_set_prdCode}[] = array('num'=>$_orderNum, 'qty'=> $_this_qty);

						if( ${'stock_set_'.$_set_prdCode} > 0 ){
							${'stock_set_'.$_set_prdCode} = ${'stock_set_'.$_set_prdCode} + $_this_qty;
						}else{
							$stock_data = sql_fetch_array(sql_query_error("select ps_idx from prd_stock where ps_idx = '".$_set_prdCode."' "));
							if( $stock_data['ps_idx'] ){
								$stock_set_code[] = $_set_prdCode;
								${'stock_set_'.$_set_prdCode} = $_this_qty;
							}else{
								$_error[] = "[".$count."] ".$_name." / ".$_phone." |  (".$_set_prdCode.")  세트 재고 상품 데이터 없음";
							}
						}

						if (strpos($_option,"패키지 제거 여부 : 패키지 제거") !== false ){
							if( ${'packageOut_set_'.$_prdCode} > 0 ){
								${'packageOut_set_'.$_prdCode} = ${'packageOut_set_'.$_prdCode} + $_qty;
							}else{
								${'packageOut_set_'.$_prdCode} = $_qty;
							}
						}

					}

				}else{
					$_error[] = "[".$count."] ".$_name." / ".$_phone." |  (".$_prdCode.") 코드 확인";
				}

			}

		}

	} //while END

	$stock_all_code = [];
	if ( count($stock_code) > 0 && count($stock_set_code) > 0 ){
		$stock_all_code = array_merge($stock_code, $stock_set_code);
		$stock_all_code = array_values(array_unique($stock_all_code));
	}elseif ( count($stock_code) == 0 && count($stock_set_code) > 0 ){
		$stock_all_code = $stock_set_code;
	}else{
		$stock_all_code = $stock_code;
	}	

	$order_num = array_values(array_unique($order_num));

	$_ary_st = [];
	for ($i=0; $i<count($stock_all_code); $i++){

		$_ps_idx = $stock_all_code[$i] ?? "";
		$prd_data = sql_fetch_array(sql_query_error("select 
			A.ps_idx, 
			B.CD_IDX, B.CD_NAME, B.CD_BRAND_IDX,
			C.BD_NAME 
			from prd_stock A 
			left join "._DB_COMPARISON." B ON (A.ps_prd_idx = B.CD_IDX ) 
			left join "._DB_BRAND." C  ON (B.CD_BRAND_IDX = C.BD_IDX ) 
			where ps_idx = '".$_ps_idx."' "));

		// 배열 검증
		if (!is_array($prd_data)) {
			$prd_data = ['CD_NAME' => '', 'BD_NAME' => '', 'CD_BRAND_IDX' => '', 'CD_IDX' => '', 'ps_idx' => ''];
		}

		// 동적 변수 안전 접근
		$_stock_qty = isset(${'stock_'.$_ps_idx}) ? ${'stock_'.$_ps_idx} : 0;
		$_stock_set_qty = isset(${'stock_set_'.$_ps_idx}) ? ${'stock_set_'.$_ps_idx} : 0;
		$_order_num = isset(${'order_num_'.$_ps_idx}) ? ${'order_num_'.$_ps_idx} : [];
		$_order_set_num = isset(${'order_set_num_'.$_ps_idx}) ? ${'order_set_num_'.$_ps_idx} : [];
		$_packageOut = isset(${'packageOut_'.$_ps_idx}) ? ${'packageOut_'.$_ps_idx} : 0;
		$_packageOut_set = isset(${'packageOut_set_'.$_ps_idx}) ? ${'packageOut_set_'.$_ps_idx} : 0;

		$_kind_one = array(
			'name'=>'단일',
			'qty' => $_stock_qty,
			'order_num' => $_order_num
		);

		$_kind_set = array(
			'name'=>'세트',
			'qty' => $_stock_set_qty,
			'order_num' => $_order_set_num
		);
		
		$_sum_qty = $_stock_qty + $_stock_set_qty;
		$_package_out = $_packageOut + $_packageOut_set;

		$_ary_st_show[] = array(
			'o_ps_idx' => $_ps_idx,
			'name' => $prd_data['CD_NAME'] ?? "",
			'brand_name' => $prd_data['BD_NAME'] ?? "",
			'brand_idx' => $prd_data['CD_BRAND_IDX'] ?? "",
			'cd_idx' => $prd_data['CD_IDX'] ?? "",
			'ps_idx' => $prd_data['ps_idx'] ?? "",
			'qty' => $_sum_qty,
			'one' => $_kind_one,
			'set' => $_kind_set,
			'packageOut' => $_package_out
		);

		$_ary_st[] = array(
			'brand_idx' => $prd_data['CD_BRAND_IDX'] ?? "",
			'cd_idx' => $prd_data['CD_IDX'] ?? "",
			'ps_idx' => $prd_data['ps_idx'] ?? "",
			'qty' => $_sum_qty,
			'one' => $_kind_one,
			'set' => $_kind_set,
			'packageOut' => $_package_out
		);

	}

	$_st_data = json_encode($_ary_st, JSON_UNESCAPED_UNICODE);

	$_ary_info = array(
		'order_count' => count($order_num),
		'pd_count' => count($stock_all_code)
	);

	$_info_data = json_encode($_ary_info, JSON_UNESCAPED_UNICODE);

	$_ary_error = array(
		'count' => count($_error),
		'result' => $_error
	);

	$_error_data = json_encode($_ary_error, JSON_UNESCAPED_UNICODE);

	$query = "insert prd_stock_history set
		file_name = '".$_file_name."',
		reg_time = '".$action_time."',
		reg_id = '".$_ad_id."',
		data = '".$_st_data."',
		step = '1',
		info = '".$_info_data."',
		error = '".$_error_data."' ";
	sql_query_error($query);

	$_key = mysqli_insert_id($connect);

	msg("","/ad/order/stock_excel?idx=".$_key);

////////////////////////////////////////////////////////////////////////////////////////////////
// 일일재고 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="day_stock_del" ){

	$_idx = $_POST['idx'] ?? "";

	sql_query_error("delete from prd_stock_history where uid = '".$_idx."' ");

	$response = array(
		'success' => true,
		'msg' => '완료'
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;
}

?>