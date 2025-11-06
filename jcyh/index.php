<?php

	$docRoot = $_SERVER['DOCUMENT_ROOT'];

	include_once ($docRoot."/class/Helper.php");

	$host = "https://ree70e.cafe24api.com/api/v2/admin/boards/4/articles";

	$_client_id = "7ner5a2CPS6UvmrZL8UpxD";
	$_client_secret_key = "ZSSPDhIjAVMp9MEiMCSxmA";
	$access_token = $_client_id.":".$_client_secret_key;


	$api_version = "";

	$url = $host."/auth"; 
	$header = array('Content-Type: application/json', 'Authorization: Bearer '.$access_token); 
	$data = [];

	$helper = new Helper;
	$response = $helper->postData($url,$data,$header);
	
	$data = json_decode($response);

		echo "<pre>";
		print_r($data);
		echo "</pre>";
?>

asdasd