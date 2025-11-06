<?
	include "../lib/inc_common.php";
	include 'board_inc.php';

	//넘어온 변수 전체 검열
	while(list($key,$val)= each($_POST)){
		${"_".$key} = securityVal($val);
	}

////////////////////////////////////////////////////////////////////////////////////////////////
// 신규재고 상품등록
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_a_mode == "new_stock_prd" ){

	/*
	상품 관리 v.2 > 재고관리
	곧 폐기될 페이지

	$_passkey = securityVal($passkey);
	$_ary_passkey = explode("/", $_passkey);

	for ($i=0; $i<count($_ary_passkey); $i++){
		
		$_ps_prd_idx = $_ary_passkey[$i];
		$_ps_update_date = date("Y-m-d h:i:s");

		//재고테이블에 해당상품 있는지 확인
		$stock_data = sql_fetch_array(sql_query_error("select ps_idx from prd_stock WHERE ps_prd_idx = '".$_ps_prd_idx."' "));
		if( !$stock_data[ps_idx] ){ //없다면 등록

			$prd_data = sql_fetch_array(sql_query_error("select CD_KIND_CODE from "._DB_COMPARISON." WHERE CD_IDX = '".$_ps_prd_idx."' "));

			$query = "insert prd_stock set
				ps_kind = '".$prd_data[CD_KIND_CODE]."',
				ps_prd_idx = '".$_ps_prd_idx."',
				ps_update_date = '".$_ps_update_date."' ";
			sql_query_error($query);

			$_return_key[] = $_ps_prd_idx;
		}

	}

    if( $_ajax_mode == "on" ){
		echo "|Processing_Complete|등록완료|".count($_return_key)."|".implode("/",$_return_key)."|";
    }else{
	}
	exit;
	*/

////////////////////////////////////////////////////////////////////////////////////////////////
// 상품 퀵창에서 [재고 코드 생성] 생성
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "new_stock_prd_one" ){

	$_passkey = securityVal($passkey);
	$_ps_update_date = date("Y-m-d h:i:s");

	//재고테이블에 해당상품 있는지 확인
	$stock_data = sql_fetch_array(sql_query_error("select ps_idx from prd_stock WHERE ps_prd_idx = '".$_passkey."' "));
	if( !$stock_data[ps_idx] ){ //없다면 등록

		$prd_data = sql_fetch_array(sql_query_error("select CD_KIND_CODE from "._DB_COMPARISON." WHERE CD_IDX = '".$_passkey."' "));

		$query = "insert prd_stock set
			ps_kind = '".$prd_data[CD_KIND_CODE]."',
			ps_prd_idx = '".$_passkey."',
			ps_update_date = '".$_ps_update_date."' ";
		sql_query_error($query);

	}

    if( $_ajax_mode == "on" ){
		echo "|Processing_Complete|등록완료|";
    }else{
	}
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 일일 재고 등록
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "day_stock" ){

	$_stock_token = make_token(8,"stock");
	$_stock_update_date = date("Y-m-d H:i:s",$check_time);

	$talk_massage = " ※재고알림 메세지※ \n";
	$talk_yn = "N";

	for ($i=0; $i<count($_stock_key); $i++){

		$_psu_stock_idx = $_stock_key[$i];
		$_psu_mode = $_stock_mode[$i];
		$_psu_qry = $_stock_qry[$i];
		$_psu_kind = $_stock_kind[$i];
		$_psu_memo = $_stock_memo[$i];
		
		//수량이 있을경우
		if( $_psu_qry > 0 ){

			$prd_stock_data = sql_fetch_array(sql_query_error("SELECT ps_stock,ps_prd_idx,ps_alarm_yn,ps_alarm_count,ps_alarm_message FROM prd_stock WHERE ps_idx = '".$_psu_stock_idx."' " ));
			$prd_data = sql_fetch_array(sql_query_error("select CD_NAME from "._DB_COMPARISON." WHERE CD_IDX = '".$prd_stock_data[ps_prd_idx]."' "));

			if( $_psu_mode == "plus" ){
				$_prd_stock_count = $prd_stock_data[ps_stock] + $_psu_qry;
			
			}elseif($_psu_mode == "minus"){
				
				$_prd_stock_count = $prd_stock_data[ps_stock] - $_psu_qry;

				if( $_prd_stock_count == 0 || $_prd_stock_count == 1 ){
					$talk_massage = $talk_massage. " [".$prd_data[CD_NAME]."] x ".$_prd_stock_count." \n";
					$talk_yn = "Y";
				}

				
					if($prd_stock_data[ps_alarm_yn] == 'Y'){
						$_ary_alarm_count = explode(",", $prd_stock_data[ps_alarm_count]);
						$_ary_alarm_message = explode(",", $prd_stock_data[ps_alarm_message]);
						if($_ary_alarm_message[$j] != ''){
							$alarmSet = "N";
							$talk_massage = " ※재고알림 메세지※ \n";
							   for($j=0;$j<6;$j++){
									if( $_prd_stock_count == $_ary_alarm_count[$j] ){
											$talk_massage = $talk_massage. " [".$prd_data[CD_NAME]."] x ".$_prd_stock_count." \n";
											$talk_massage = $talk_massage." ".$_ary_alarm_message[$j];
											$alarmSet = "Y";
									}
								}

								if($alarmSet == "N"){
									if( $_prd_stock_count == 0 || $_prd_stock_count == 1 ){
										$talk_massage = $talk_massage. " [".$prd_data[CD_NAME]."] x ".$_prd_stock_count." \n";
										
									}
								}
							$talk_yn = "Y";
						}
					}else{
					  $talk_yn = 'N';
					}
				
			}


			if( $_psu_mode == "plus" ){

				$update = "update prd_stock set ps_stock = ps_stock + ".$_psu_qry.", ";			

				if( $_psu_kind == "신규입고" ){
					$update .= " ps_stock_all = ps_stock_all + ".$_psu_qry.", ";
				}

				$update .= " ps_update_date = '".$_stock_update_date."' where ps_idx = '".$_psu_stock_idx."' ";

				//sql_query_error("update prd_stock set ps_stock = ps_stock + ".$_psu_qry.", ps_update_date = '".$_stock_update_date."' where ps_idx = '".$_psu_stock_idx."' " );
				sql_query_error($update);
			
			}else{

				if( $_prd_stock_count == 0 ){
					$_ps_soldout_date = $action_time;
				}else{
					$_ps_soldout_date = 0;
				}

				sql_query_error("UPDATE prd_stock set 
					ps_stock = ps_stock - ".$_psu_qry.", 
					ps_update_date = '".$_stock_update_date."',
					ps_last_date = '".$action_time."',
					ps_soldout_date = '".$_ps_soldout_date."'
					where ps_idx = '".$_psu_stock_idx."' " );

			}



			$query = "insert prd_stock_unit set
				psu_stock_idx = '".$_psu_stock_idx."',
				psu_day = '".$_stock_day."',
				psu_mode = '".$_psu_mode."',
				psu_qry = '".$_psu_qry."',
				psu_stock = '".$_prd_stock_count."',
				psu_kind = '".$_psu_kind."',
				psu_memo = '".$_psu_memo."',
				psu_token = '".$_stock_token."',
				psu_id = '".$_ad_id."',
				psu_date = '".$check_time ."' ";
			sql_query_error($query);
		}
	}//for END
	
	if($talk_yn == "Y"){

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
// 퀵창에서 재고등록 상품1개
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="quick_stock" ){

	$_idx = securityVal($idx);

	$_psu_qry = securityVal($stock_qry);
	$_psu_mode = securityVal($stock_mode);
	$_psu_kind = securityVal($stock_kind);
	$_psu_kind = securityVal($stock_kind);
	$_stock_update_date = date("Y-m-d H:i:s",$check_time);
	$_psu_stock_idx = securityVal($stock_idx);
	$_stock_day = securityVal($stock_day);
	$_psu_memo = securityVal($stock_memo);


	if( $_psu_qry > 0 ){

		$prd_stock_data = sql_fetch_array(sql_query_error("SELECT * FROM prd_stock WHERE ps_idx = '".$_psu_stock_idx."' " ));

		if( $_psu_mode == "plus" ){

			$_prd_stock_count = $prd_stock_data[ps_stock] + $_psu_qry;

			$update = "update prd_stock set ps_stock = ps_stock + ".$_psu_qry.", ";			

			if( $_psu_kind == "신규입고" ){
				$update .= " ps_stock_all = ps_stock_all + ".$_psu_qry.", ";
			}

			$update .= " ps_update_date = '".$_stock_update_date."' where ps_idx = '".$_psu_stock_idx."' ";
			//sql_query_error("update prd_stock set ps_stock = ps_stock + ".$_psu_qry.", ps_update_date = '".$_stock_update_date."' where ps_idx = '".$_psu_stock_idx."' " );
			sql_query_error($update);
		
		}else{
			$_prd_stock_count = $prd_stock_data[ps_stock] - $_psu_qry;
			sql_query_error("update prd_stock set ps_stock = ps_stock - ".$_psu_qry.", ps_update_date = '".$_stock_update_date."'  where ps_idx = '".$_psu_stock_idx."' " );
			
		}

		$query = "insert prd_stock_unit set
			psu_stock_idx = '".$_psu_stock_idx."',
			psu_day = '".$_stock_day."',
			psu_mode = '".$_psu_mode."',
			psu_qry = '".$_psu_qry."',
			psu_stock = '".$_prd_stock_count."',
			psu_kind = '".$_psu_kind."',
			psu_memo = '".$_psu_memo."',
			psu_id = '".$_ad_id."',
			psu_date = '".$check_time ."' ";
		sql_query_error($query);
	
	}

	msg("","/admin2/comparison/popup.comparison_modify.php?idx=".$_idx."&vmode=stock");

}elseif( $_a_mode=="stockAlarmSet" ){

	$_prd_key = securityVal($prd_key);
	$_ps_alarm_yn = securityVal($alarmYN);
	$_ps_alarm_count = securityVal($alarmCount);
	$_ps_alarm_message = securityVal($alarmMassage);
	
	$_ps_alarm_count_text = implode(",",$_ps_alarm_count);
	$_ps_alarm_message_text = implode(",",$_ps_alarm_message);

	$updateQuery = "
		UPDATE prd_stock SET 
			ps_alarm_yn  = '".$_ps_alarm_yn."', 
			ps_alarm_count  = '".$_ps_alarm_count_text."', 
			ps_alarm_message  = '".$_ps_alarm_message_text."'
		WHERE ps_idx = ".$_prd_key."
	   ";	

	   sql_query_error($updateQuery);

	   msg("","popup_prd2_stock_alarm.php?idx=".$_prd_key);

////////////////////////////////////////////////////////////////////////////////////////////////
// 브랜드 재고 등록
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "brand_stock_reg" ){

/*
	사용안함

	$json_url = "../../config_file/brand_stock_group.json";
	$json_string = file_get_contents($json_url);
	$json_data = json_decode($json_string, true);

	$add_data = array(
		'name' => $_brand_group_name,
		'idx' => $_brand_idx
	);

	$final_json_data = array();

	if( is_array($json_data) ){
		//array_push($json_data, $add_data);
		array_push($final_json_data, $json_data);
		array_push($final_json_data, $add_data);
	}else{
		//$json_data = $add_data;
		$final_json_data = $add_data;
	}

	$json = json_encode($final_json_data, JSON_UNESCAPED_UNICODE);
	$bytes = file_put_contents($json_url, $json);

	$response = array(
		'success' => true,
		'msg' => "완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;
*/

////////////////////////////////////////////////////////////////////////////////////////////////
// 브랜드 재고 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "brand_stock_modify" ){

	for ($i=0; $i<count($_brand_group_name); $i++){
		
		$json_data[] = array(
			'name' => $_brand_group_name[$i],
			'idx' => $_brand_idx[$i]
		);

	}

	$json_url = "../../config_file/brand_stock_group.json";
	$json = json_encode($json_data, JSON_UNESCAPED_UNICODE);
	$bytes = file_put_contents($json_url, $json);

	$response = array(
		'success' => true,
		'msg' => "완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 일일재고 엑셀 임시 등록하기 -> 신버전 사용으로 이전함
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="stock_excel" ){

	setlocale(LC_CTYPE, 'ko_KR.eucKR'); 
	extract($_FILES['userfile']); 

	$_file_name = $name;

	/*
	$data = sql_fetch_array(sql_query_error("select uid from prd_stock_history where file_name = '".$_file_name."' "));
	if( $data['uid'] ){
		msg("이미 등록된 파일입니다.", "prd2_stock_excel.php");
		exit;
	}
	*/

	$fp = fopen($tmp_name, 'r'); 
	$count = 0;
	$count2 = 0;

	//while ($row = fgetcsv($fp, 100000, ',')) { 
	while ($row = fgetcsv($fp, 2000, ',')) { 

		$count++;

		$_name = iconv("euc-kr","utf-8",$row[0]);
		$_phone = iconv("euc-kr","utf-8",$row[1]);
		$_prdCode = $row[4];
		$_prdCodeSub = $row[5];
		$_orderNum = $row[8];
		$_qty = $row[6]*1;
		$_option = iconv("euc-kr","utf-8",$row[12]);

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
					
					if( $stock_data[ps_idx] ){
						$stock_code[] = $_prdCode;
						${'stock_'.$_prdCode} = $_qty;
					}else{
						$_error[] = "[".$count."] ".$_name." / ".$_phone." |  (".$_prdCode.")  재고 상품 데이터 없음";
					}
				}

				if (strpos($_option,"패키지 제거") !== false ){
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

								if (strpos($_option,"패키지 제거") !== false ){
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
								if( $stock_data[ps_idx] ){
									$stock_set_code[] = $_set_prdCode;
									${'stock_set_'.$_set_prdCode} = $_qty;
								}else{
									$_error[] = "[".$count."] ".$_name." / ".$_phone." |  (".$_set_prdCode.")  세트 재고 상품 데이터 없음";
								}
							}

							if (strpos($_option,"패키지 제거") !== false ){
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
							if( $stock_data[ps_idx] ){
								$stock_set_code[] = $_set_prdCode;
								${'stock_set_'.$_set_prdCode} = $_this_qty;
							}else{
								$_error[] = "[".$count."] ".$_name." / ".$_phone." |  (".$_set_prdCode.")  세트 재고 상품 데이터 없음";
							}
						}

						if (strpos($_option,"패키지 제거") !== false ){
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

	if ( $stock_code && $stock_set_code ){
		$stock_all_code = array_merge($stock_code, $stock_set_code);
		$stock_all_code = array_values(array_unique($stock_all_code));
	}elseif ( !$stock_code && $stock_set_code ){
		$stock_all_code = $stock_set_code;
	}else{
		$stock_all_code = $stock_code;
	}	

	$order_num = array_values(array_unique($order_num));


	for ($i=0; $i<count($stock_all_code); $i++){

		$_ps_idx = $stock_all_code[$i];
		$prd_data = sql_fetch_array(sql_query_error("select 
			A.ps_idx, 
			B.CD_IDX, B.CD_NAME, B.CD_BRAND_IDX,
			C.BD_NAME 
			from prd_stock A 
			left join "._DB_COMPARISON." B ON (A.ps_prd_idx = B.CD_IDX ) 
			left join "._DB_BRAND." C  ON (B.CD_BRAND_IDX = C.BD_IDX ) 
			where ps_idx = '".$_ps_idx."' "));

		$_kind_one = array(
			'name'=>'단일',
			'qty' => ${'stock_'.$_ps_idx},
			'order_num' => ${'order_num_'.$_ps_idx}
		);

		$_kind_set = array(
			'name'=>'세트',
			'qty' => ${'stock_set_'.$_ps_idx},
			'order_num' => ${'order_set_num_'.$_ps_idx}
		);
		
		$_sum_qty = ${'stock_'.$_ps_idx} + ${'stock_set_'.$_ps_idx};
		$_package_out = ${'packageOut_'.$_ps_idx} + ${'packageOut_set_'.$_ps_idx};

		$_ary_st_show[] = array(
			'o_ps_idx' => $_ps_idx,
			'name' => $prd_data[CD_NAME],
			'brand_name' => $prd_data[BD_NAME],
			'brand_idx' => $prd_data[CD_BRAND_IDX],
			'cd_idx' => $prd_data[CD_IDX],
			'ps_idx' => $prd_data[ps_idx],
			'qty' => $_sum_qty,
			'one' => $_kind_one,
			'set' => $_kind_set,
			'packageOut' => $_package_out
		);

		$_ary_st[] = array(
			'brand_idx' => $prd_data[CD_BRAND_IDX],
			'cd_idx' => $prd_data[CD_IDX],
			'ps_idx' => $prd_data[ps_idx],
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

	msg("","prd2_stock_excel.php?idx=".$_key);

////////////////////////////////////////////////////////////////////////////////////////////////
// 일일재고 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode=="day_stock_del" ){

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