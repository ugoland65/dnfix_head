<?
	include "../lib/inc_common.php";

	$_view_body = "";

	$bo_c_where = "  ";
	$bo_c_query = "select BAC_NAME, BOARD_CODE from "._DB_BOARD_A_CONFIG." ".$bo_c_where."order by UID desc ";
	$bo_c_result = wepix_query_error($bo_c_query);
	while($bo_c_list = wepix_fetch_array($bo_c_result)){

		//상품 idx 저장필드 검사
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."` LIKE 'BOARD_SUBJECT' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= "없음";
		}else{
			$_view_body .= "있음-------------------";
		}

	}

include "../layout/header.php";
?>
<?=$_view_body?>
<?
include "../layout/footer.php";
exit;
?>