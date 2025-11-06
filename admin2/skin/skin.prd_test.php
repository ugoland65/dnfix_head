<style type="text/css">
.prd-test{ box-sizing:border-box; padding:60px 0 0 20px; }
.prd-test > ul{ border-bottom:1px solid #444; }
</style>
<div class="prd-test">
<?

	$_where .= "where COMMENT_MODE = 'BS' ";

	$_query = "select * from COMPARISON_COMMENT ".$_where." order by COMMENT_IDX desc ";

	$_result = sql_query_error($_query);

	while($list = sql_fetch_array($_result)){
		
		/*
		{"name":"도트","level":"2","ip":"123.143.96.188","domain":"onadb.net","device":"pc"}
		{"name":"불멸의두두","pw":"102145","ip":"112.76.20.63","domain":"onadb.net","device":"pc"}
		*/

		$_pc_reg_info_data = array(
			"name" => $list['COMMENT_NAME'],
			"pw" =>  $list['COMMENT_PW'],
			"pw_mode" => "wepix_pw",
			"ip" => $list['COMMENT_IP'],
			"ad_memo" => "코엣지DB 오나디비로 옮김",
			"copy_date" => $action_time
		);

		$_pc_reg_info =json_encode($_pc_reg_info_data, JSON_UNESCAPED_UNICODE);
		
		$_pc_body = securityVal($list['COMMENT_BODY']);
		
		$_pc_reg_date = date("Y-m-d H:i:s", $list['COMMENT_DATE']);

/*
		$query = "insert prd_comment set
			pc_kind = 'onadb',
			pc_pd_idx = '".$list['PD_UID']."',
			pc_user_idx = '',
			pc_reg_info = '".$_pc_reg_info."',
			pc_score = '',
			pc_score_mode = 'before',
			pc_body = '".$_pc_body."',
			pc_category = 'ONAHOLE',
			pc_reg_date = '".$_pc_reg_date."',
			pc_reg_mode = 'BG',
			pc_ip = '".$list['COMMENT_IP']."' ";
		sql_query_error($query);
*/

	} //while END

?>

</div>