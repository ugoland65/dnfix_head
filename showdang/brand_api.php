<?
$docRoot = $_SERVER['DOCUMENT_ROOT'];

include $docRoot."/library/globalConfig.php";
include $docRoot."/library/mysql.php";

	$_cate_no = securityVal($cate_no);

if( $_cate_no == "List" ){

	$_count = 0;
	$_search_query = " WHERE BD_NAME_GROUP != '' AND bd_showdang_active = 'Y' ";
	$query = "select * from "._DB_BRAND." ".$_search_query." order by BD_NAME desc";
	$result = wepix_query_error($query);
	while($list = wepix_fetch_array($result)){
		
		$_count++;

		$_bd_kind = json_decode($list['bd_kind'], true);
		
		$arycate = [];
		
		if( $_bd_kind['ona'] == "Y" ) $arycate[] = "ona";
		if( $_bd_kind['breast'] == "Y" ) $arycate[] = "breast";
		if( $_bd_kind['gel'] == "Y" ) $arycate[] = "gel";
		if( $_bd_kind['condom'] == "Y" ) $arycate[] = "condom";
		if( $_bd_kind['annal'] == "Y" ) $arycate[] = "annal";
		if( $_bd_kind['prostate'] == "Y" ) $arycate[] = "prostate";
		if( $_bd_kind['care'] == "Y" ) $arycate[] = "care";
		if( $_bd_kind['dildo'] == "Y" ) $arycate[] = "dildo";
		if( $_bd_kind['vibe'] == "Y" ) $arycate[] = "vibe";
		if( $_bd_kind['suction'] == "Y" ) $arycate[] = "suction";
		if( $_bd_kind['man'] == "Y" ) $arycate[] = "man";
		if( $_bd_kind['nipple'] == "Y" ) $arycate[] = "nipple";
		if( $_bd_kind['cos'] == "Y" ) $arycate[] = "cos";
		if( $_bd_kind['perfume'] == "Y" ) $arycate[] = "perfume";
		if( $_bd_kind['bdsm'] == "Y" ) $arycate[] = "bdsm";

		$_show_cate = implode(" ", $arycate);
		
		$arr_gp_ko[$list['BD_NAME_GROUP']][] = array( 
			'name' => $list['BD_NAME'],
			'name_en' => $list['BD_NAME_EN'],
			'cate_no' => $list['bd_cate_no'],
			'show_cate' => $_show_cate
		);

		$arr_gp_en[$list['BD_NAME_EN_GROUP']][] = array( 
			'name' => $list['BD_NAME'],
			'name_en' => $list['BD_NAME_EN'],
			'cate_no' => $list['bd_cate_no'],
			'show_cate' => $_show_cate
		);
	}

	$arr_ko_1st = array('ㄱ','ㄴ','ㄷ','ㄹ','ㅁ','ㅂ','ㅅ','ㅇ','ㅈ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ','#'); //초성
	$arr_en_1st = array('A', 'B', 'C', 'D', 'E', 'F','G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 'P', 'Q', 'R','S', 'T', 'U', 'V', 'W', 'X','Y', 'Z', '@');

	for ($i=0; $i<count($arr_ko_1st); $i++){ 
		$_chos_code = $arr_ko_1st[$i];
		sort($arr_gp_ko[$_chos_code]);
	}

	for ($i=0; $i<count($arr_en_1st); $i++){ 
		$_chos_code = $arr_en_1st[$i];
		sort($arr_gp_en[$_chos_code]);
	}

	$response = array(
		'result' => true,
		'count' => $_count,
		'brand_kr' => $arr_gp_ko,
		'brand_en' => $arr_gp_en
	);

}else{

	$brand_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BRAND." where bd_cate_no = '".$_cate_no."' "));
	$_bd_api_info = json_decode($brand_data['bd_api_info'], true);

	if( $brand_data[BD_IDX] ){

		$_active = "N";
		if( $_bd_api_info['active'] == "Y"){ 
			$_active = "Y";
		}

		$response = array(
			'result' => true,
			'active' => $_active,
			'bg_rgb' => $_bd_api_info['bg_rgb'],
			'bg' => $_bd_api_info['bg'],
			'bg_mobile' => $_bd_api_info['bg_mobile'],
			'info_class' => $_bd_api_info['info_class'],
			'logo' => $_bd_api_info['logo'],
			'logo_mobile' => $_bd_api_info['logo_mobile'],
			'name' => $_bd_api_info['name'],
			'name_en' => $_bd_api_info['name_en'],
			'introduce' => $brand_data['bd_api_introduce'],
			'msg' => '완료'
		);

	}else{

		$response = array(
			'result' => false,
			'msg' => '-'
		);

	}

}

		header('Content-Type: application/json');
		echo "var brandApiData = ".json_encode($response, JSON_UNESCAPED_UNICODE); 
		exit;

?>
