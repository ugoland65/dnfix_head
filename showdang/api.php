<?
$docRoot = $_SERVER['DOCUMENT_ROOT'];

include $docRoot."/library/globalConfig.php";
include $docRoot."/library/mysql.php";

	$_type = securityVal($type);

if( $_type == "brandLink" ){

	$query = "select * from brand_link ".$_serch_query." order by bl_idx desc";
	$result = wepix_query_error($query);
	while($list = wepix_fetch_array($result)){
		
		$_data[$list['bl_keyword']] = array(
			'link' => $list['bl_link']
		);

	}

	$response = $_data;

	header('Content-Type: application/json');
	echo "var brandLinkApiData = ".json_encode($response, JSON_UNESCAPED_UNICODE); 
	//echo json_encode($response, JSON_UNESCAPED_UNICODE); 
	exit;

}


?>