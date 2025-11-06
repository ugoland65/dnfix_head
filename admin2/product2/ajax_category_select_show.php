<?
include "../lib/inc_common.php";

	$_ct_id = securityVal($ct_id);
	$_depth_id = securityVal($depth_id);
	$_depth = securityVal($depth);

	$cateLevel = category_level($_depth_id);
	$cateCut =  category_status($_depth_id);

	$where = " where PDC_DEPTH = '".$_depth."' and PDC_VIEW = 'Y' ";

	//1차
	if( $_depth == "0" ){

		$where .= " and PDC_NEW_KIND ='G' ";
		$_select_id = category_depth_code($_ct_id,'1');

	//2차
	}elseif( $_depth == "1" ){

		$where .= " and substring(PDC_ID,1,2) = '".$cateCut."' ";
		$_select_id = category_depth_code($_ct_id,'2');
		$frist_option = "<option value=''>=== 2차 분류 ===</option>";

	//3차
	}elseif( $_depth == "2" ){

		$where .= " and substring(PDC_ID,1,4) = '".$cateCut."' ";
		$_select_id = category_depth_code($_ct_id,'3');
		$frist_option = "<option value=''>=== 3차 분류 ===</option>";

	//4차
	}elseif( $_depth == "3" ){

		$where .= " and substring(PDC_ID,1,6) = '".$cateCut."' ";
		$_select_id = $_ct_id;
		$frist_option = "<option value=''>=== 4차 분류 ===</option>";

	}

		//echo "<option>".$_depth."│_depth_id = ".$_depth_id."│_select_id = ".$_select_id."│cateCut = ".$cateCut."│".$pdc_depth_1."</option>";

	echo $frist_option;

	$query = "select PDC_ID, PDC_NAME from "._DB_PRODUCT_CATAGORY_TRAVEL." ".$where." ";
	//echo "<option>".$query."</option>";
	$result = wepix_query_error($query);
	while( $list = wepix_fetch_array($result)) {
		if( $list[PDC_ID] ==  $_select_id ){
			$selectd = "selected";
		}else{
			$selectd = "";
		}
		echo "<option value='".$list[PDC_ID]."' ".$selectd.">".$list[PDC_NAME]."</option>";
	}

exit;
?>