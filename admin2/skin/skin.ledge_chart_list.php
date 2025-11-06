<?
	$_category_out_array = array("운영고정","인건비","운영비","세금","대출","환불","수입매입","국내매입","부자재","매입부대비용");

	$_query = "select * from ledge_category ".$_where." ORDER BY idx DESC";
	$_result = sql_query_error($_query);
	while($list = sql_fetch_array($_result)){

		if( $list['lc_mode'] == "수입" ){
			$_lc_mode = "in";
		}elseif( $list['lc_mode'] == "지출" ){
			$_lc_mode = "out";
		}

		$_ledge_category[$_lc_mode][$list['lc_depth']][] = array(
			"idx" => $list['idx'],
			"name" => $list['lc_name'],
			"approval" => $list['lc_approval']
		);

		$_ledge_unit[$_lc_mode][$list['lc_category']][] = array(
			"idx" => $list['idx'],
			"name" => $list['lc_name'],
			"approval" => $list['lc_approval']
		);

	}

	$_ledge_category_in_1 = $_ledge_category['in'][1];
	$_ledge_category_out_1 = $_ledge_category['out'][1];

	$_ledge_unit_out = $_ledge_unit['out'];

	$_ary_smonth = explode("-", $_ym);

	$cur_y = $_ary_smonth[0];
	$cur_m = $_ary_smonth[1];

	//해당월의 총날짜수를 구한다.
	function getTotaldays($y, $m) {
		$d = 1;
		while(checkdate($m, $d, $y)) {
			$d++;
		}
		$d = $d - 1;
		return $d;
	}

	$tot_days = getTotaldays($cur_y, $cur_m);

	$_s_date = $_ym."-01 00:00:00";
	$_e_date = $cur_y."-".$cur_m."-".$tot_days." 23:59:59";

?>
<?=$_s_date?> ~ <?=$_e_date?>


<?
				$_query = "select 
					COUNT( CASE WHEN ledge_cate_idx = '0' AND state = 'N' AND bs_mode = 'plus' THEN 1 END ) as in_n_count,
					COUNT( CASE WHEN ledge_cate_idx > 0 AND state = 'Y' AND bs_mode = 'plus' THEN 1 END ) as in_y_count,
					COUNT( CASE WHEN ledge_cate_idx = '0' AND state = 'N' AND bs_mode = 'minus' THEN 1 END ) as out_n_count,
					COUNT( CASE WHEN ledge_cate_idx > 0 AND state = 'Y' AND bs_mode = 'minus' THEN 1 END ) as out_y_count,
					SUM( CASE WHEN ledge_cate_idx = '0' AND state = 'N' AND bs_mode = 'plus' THEN in_money END ) as in_n_sum_money,
					SUM( CASE WHEN ledge_cate_idx > 0 AND state = 'Y' AND bs_mode = 'plus' THEN in_money END ) as in_y_sum_money,
					SUM( CASE WHEN ledge_cate_idx = '0' AND state = 'N' AND bs_mode = 'minus' THEN out_money END ) as out_n_sum_money,
					SUM( CASE WHEN ledge_cate_idx > 0 AND state = 'Y' AND bs_mode = 'minus' THEN out_money END ) as out_y_sum_money
					from bank_statement WHERE date >= '".$_s_date."' AND date <= '".$_e_date."' ";
				$_sum_all_sum = sql_fetch_array(sql_query_error($_query));
?>

<table class="table-style">	
	<tr class="list">
		<th class="">수입항목</th>
		<th class="">지출항목</th>
		<th class="">수입/지출 합</th>
	</tr>
	<tr class="list">
		<td class="" style="vertical-align:top;">
			
			<div class="text-right">
				<ul>미확인 : (<?=$_sum_all_sum['in_n_count']?>)건 : <b><?=number_format($_sum_all_sum['in_n_sum_money'])?></b> 원</ul>
				<ul>확인 : (<?=$_sum_all_sum['in_y_count']?>)건 : <b><?=number_format($_sum_all_sum['in_y_sum_money'])?></b> 원</ul>
			</div>

			<table class="table-style m-t-10">	
				<tr class="list">
					<th class="">idx</th>
					<th class="">항목명</th>
					<th class="">건수</th>
					<th class="">계산</th>
					<th class="">합계</th>
				</tr>
			<?
			$_sum_count = 0;
			$_sum_money = 0;

			for ($i=0; $i<count($_ledge_category_in_1); $i++){

				$_query = "select 
					COUNT( CASE WHEN state = 'Y' THEN 1 END ) as count,
					SUM( CASE WHEN state = 'Y' THEN in_money END ) as sum_money
					from bank_statement WHERE ledge_cate_idx = '".$_ledge_category_in_1[$i]['idx']."' AND date >= '".$_s_date."' AND date <= '".$_e_date."' ";
				$_sum_all = sql_fetch_array(sql_query_error($_query));

				if( $_ledge_category_in_1[$i]['approval'] != "비인정" ){
					$_sum_count += $_sum_all['count'];
					$_sum_money += $_sum_all['sum_money'];
				}

			?>
				<tr class="list">
					<td class=""><?=$_ledge_category_in_1[$i]['idx']?></td>
					<td class=""><?=$_ledge_category_in_1[$i]['name']?></td>
					<td class="text-center"><?=$_ledge_category_in_1[$i]['approval']?></td>
					<td class="text-right"><?=$_sum_all['count']?></td>
					<td class="text-right"><?=number_format($_sum_all['sum_money'])?></td>
				</tr>
			<? } ?>
				<tr class="list">
					<th class="" colspan="3">인정 합계</th>
					<th class="text-right"><?=number_format($_sum_count)?></th>
					<th class="text-right"><?=number_format($_sum_money)?></th>
				</tr>
				<tr class="list">
					<th class="" colspan="3">인정 + 미확인 합계</th>
					<th class="text-right"><b><?=number_format($_sum_count + $_sum_all_sum['in_n_count'])?></b></th>
					<th class="text-right"><b><?=number_format($_sum_money + $_sum_all_sum['in_n_sum_money'])?></b></th>
				</tr>
			</table>
			<?
				$_in_sum = $_sum_money + $_sum_all_sum['in_n_sum_money'];
			?>

		</td>
		<td class="" style="vertical-align:top;">
			
			<div class="text-right">
				<ul>미확인 : (<?=$_sum_all_sum['out_n_count']?>)건 : <b><?=number_format($_sum_all_sum['out_n_sum_money'])?></b> 원</ul>
				<ul>확인 : (<?=$_sum_all_sum['out_y_count']?>)건 : <b><?=number_format($_sum_all_sum['out_y_sum_money'])?></b> 원</ul>
			</div>

			<table class="table-style m-t-10">	
			<? for ($z=0; $z<count($_category_out_array); $z++){ ?>
				<tr class="list">
					<th><?=$_category_out_array[$z]?></th>
					<td>
						
						<table class="table-style">	
							<?

								$_sum_count = 0;
								$_sum_money = 0;

								for ($i=0; $i<count($_ledge_unit_out[$_category_out_array[$z]]); $i++){
									
									$_this_unit = $_ledge_unit_out[$_category_out_array[$z]][$i];

									$_query = "select 
										COUNT( CASE WHEN state = 'Y' THEN 1 END ) as count,
										SUM( CASE WHEN state = 'Y' THEN out_money END ) as sum_money
										from bank_statement WHERE ledge_cate_idx = '".$_this_unit['idx']."' AND date >= '".$_s_date."' AND date <= '".$_e_date."' ";
									$_sum_all = sql_fetch_array(sql_query_error($_query));

									if( $_this_unit['approval'] != "비인정" ){
										$_sum_count += $_sum_all['count'];
										$_sum_money += $_sum_all['sum_money'];
									}

							?>
							<tr class="list">
								<td class=""><?=$_this_unit['idx']?></td>
								<td class=""><?=$_this_unit['name']?></td>
								<td class="text-center"><?=$_this_unit['approval']?></td>
								<td class="text-center"><?=$_sum_all['count']?></td>
								<td class="text-right"><?=number_format($_sum_all['sum_money'])?></td>
							</tr>
							<? } ?>

							<tr class="list">
								<th class="" colspan="3">합계</th>
								<th class="text-center"><?=number_format($_sum_count)?></th>
								<th class="text-right"><b><?=number_format($_sum_money)?></b></th>
							</tr>

						</table>

					</td>
				</tr>
			<? } ?>
			</table>

			<table class="table-style m-t-10">	
				<tr class="list">
					<th class="">idx</th>
					<th class="">항목명</th>
					<th class="">건수</th>
					<th class="">계산</th>
					<th class="">합계</th>
				</tr>
			<?
			$_sum_count = 0;
			$_sum_money = 0;

			for ($i=0; $i<count($_ledge_category_out_1); $i++){

				$_query = "select 
					COUNT( CASE WHEN state = 'Y' THEN 1 END ) as count,
					SUM( CASE WHEN state = 'Y' THEN out_money END ) as sum_money
					from bank_statement WHERE ledge_cate_idx = '".$_ledge_category_out_1[$i]['idx']."' AND date >= '".$_s_date."' AND date <= '".$_e_date."' ";
				$_sum_all = sql_fetch_array(sql_query_error($_query));

				if( $_ledge_category_out_1[$i]['approval'] != "비인정" ){
					$_sum_count += $_sum_all['count'];
					$_sum_money += $_sum_all['sum_money'];
				}
			?>
				<tr class="list">
					<td class=""><?=$_ledge_category_out_1[$i]['idx']?></td>
					<td class=""><?=$_ledge_category_out_1[$i]['name']?></td>
					<td class="text-center"><?=$_ledge_category_out_1[$i]['approval']?></td>
					<td class="text-right"><?=$_sum_all['count']?></td>
					<td class="text-right"><?=number_format($_sum_all['sum_money'])?></td>
				</tr>
			<? } ?>
				<tr class="list">
					<th class="" colspan="3">인정 합계</th>
					<th class="text-right"><?=number_format($_sum_count)?></th>
					<th class="text-right"><?=number_format($_sum_money)?></th>
				</tr>
				<tr class="list">
					<th class="" colspan="3">인정 + 미확인 합계</th>
					<th class="text-right"><b><?=number_format($_sum_count + $_sum_all_sum['out_n_count'])?></b></th>
					<th class="text-right"><b><?=number_format($_sum_money + $_sum_all_sum['out_n_sum_money'])?></b></th>
				</tr>
			</table>
			<?
				$_out_sum = $_sum_money + $_sum_all_sum['out_n_sum_money'];
			?>
		</td>
		<td style="vertical-align:top;">
			<b style="font-size:16px;"><?=number_format($_in_sum - $_out_sum)?></b>
		</td>
	</tr>
</table>