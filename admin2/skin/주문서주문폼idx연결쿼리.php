<?
//$oo_data = sql_fetch_array(sql_query_error("select * from ona_order where oo_idx = '".$_idx."' "));

/
$query = "select oo_idx, oo_code from ona_order ".$_where." order by oo_idx desc";
$result = wepix_query_error($query);

$count = 0;
$count1 = 0;

while($list = wepix_fetch_array($result)){
	
	$count++;

	$_oog_code = $list['oo_code'];
	$_oo_idx = $list['oo_idx'];

	$oog_data = wepix_fetch_array(wepix_query_error("select oog_idx from ona_order_group where oog_code = '".$_oog_code."' "));
	

	$_oog_idx = $oog_data['oog_idx'];

	if( $_oog_idx ){
	
		$query = "UPDATE ona_order SET 
			oo_form_idx = '".$_oog_idx."'
			WHERE oo_idx = '".$_oo_idx."' ";
		sql_query_error($query);

		$count1++;

	}
}

?>

전체 : <?=$count?> | 수정 : <?=$count1?>
