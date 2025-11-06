<?
/*
	$_query = "select 
		COUNT( CASE WHEN betting_result = 'I'  THEN 1 END ) as ing_count,
		COUNT( CASE WHEN betting_result = 'Y' THEN 1 END ) as win_count,
		COUNT( CASE WHEN betting_result = 'N' THEN 1 END ) as lose_count,
		COUNT( CASE WHEN betting_result = 'C' THEN 1 END ) as cancel_count,
		SUM( CASE WHEN betting_result = 'I' THEN betting_money END )as ing_bet_money,
		SUM( CASE WHEN betting_result = 'I' THEN betting_win_money END )as ing_win_money,
		SUM( CASE WHEN betting_result = 'Y' THEN betting_win_money END )as win_win_money,
		SUM( CASE WHEN betting_result = 'N' THEN betting_money END )as lose_bet_money
		from "._DB_BETTING." WHERE betting_date >= '".$_sdate." 00:00:00' AND betting_date <= '".$_edate." 23:59:59' ";
	$_sum_all = sql_fetch_array(sql_query_error($_query));
*/

	$_query = "select 
		COUNT( CASE WHEN A.cd_cost_price > 0  THEN 0 END ) as have_cost_price_count,
		COUNT( CASE WHEN A.cd_cost_price = 0  THEN 0 END ) as nohave_cost_price_count,
		SUM( CASE WHEN A.cd_cost_price > 0 THEN A.cd_cost_price * D.ps_stock END )as cost_price_sum,
		SUM( CASE WHEN A.cd_sale_price > 0 THEN A.cd_sale_price * D.ps_stock END )as sale_price_sum,
		
		A.cd_cost_price, A.cd_sale_price, A.CD_BRAND_IDX, 
		A.CD_IDX, A.CD_KIND_CODE, A.cd_national, A.cd_weight_fn, A.cd_code_fn, A.CD_IMG, A.CD_NAME, A.CD_NAME_OG, 
		B.BD_NAME as brand_name1,
		D.ps_idx, D.ps_stock, D.ps_rack_code
		from "._DB_COMPARISON." A
		left join "._DB_BRAND." B ON (B.BD_IDX = A.CD_BRAND_IDX  ) 
		inner join prd_stock D ON (D.ps_prd_idx = A.CD_IDX  ) 
		WHERE D.ps_stock > 0 GROUP BY CD_BRAND_IDX ORDER BY cost_price_sum DESC ";
	$_result = sql_query_error($_query);

?>
<style type="text/css">
.table-style tr.ok td{ background-color:#eee; }
.table-style tr.nohave td{ background-color:#feffb6; }
</style>
<div id="contents_head">
	<h1>재고/원가 현황</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
	

	<table class="table-style m-t-5">	
		<tr class="list">
			<th class="">브랜드</th>
			<th class="">원가 O</th>
			<th class="">원가 X</th>
			<th class="">원가합계</th>
			<th class="">판매가합계</th>
			<th class="">마진율</th>
		</tr>
	<?

	$_total_count_sum1 = 0;
	$_total_count_sum2 = 0;

	$_total_sum1 = 0;
	$_total_sum2 = 0;

	while($list = sql_fetch_array($_result)){

		$_total_count_sum1 += $list['have_cost_price_count'];
		$_total_count_sum2 += $list['nohave_cost_price_count'];

		$_total_sum1 += $list['cost_price_sum'];
		$_total_sum2 += $list['sale_price_sum'];

		if( $list['nohave_cost_price_count'] > 0 ){
			$_tr_class= "nohave";
		}else{
			$_tr_class= "ok";
		}

		if( $list['CD_BRAND_IDX'] == 0 ){
			$_brand_name = "브랜드 미지정";
		}else{
			$_brand_name = $list['brand_name1'];
		}


	?>
		<tr class="<?=$_tr_class?>" >
			<td class="">
				(<?=$list['CD_BRAND_IDX']?>) <a href="/ad/prd/prd_main/brand_idx=<?=$list['CD_BRAND_IDX']?>:"><?=$_brand_name?></a>
			</td>
			<td class="text-right">
				<?=$list['have_cost_price_count']?>
			</td>
			<td class="text-right">
				<?=$list['nohave_cost_price_count']?>
			</td>
			<td class="text-right">
				<?=number_format($list['cost_price_sum'])?>
			</td>
			<td class="text-right">
				<?=number_format($list['sale_price_sum'])?>
			</td>
			<td class="text-right">
				<?=number_format($list['sale_price_sum'] - $list['cost_price_sum'])?>
				( <b><?=round( ($list['sale_price_sum'] - $list['cost_price_sum']) / $list['sale_price_sum'] * 100, 2)?></b> % )
			</td>
		</tr>
	<? } ?>

		<tr class="list">
			<th class=""></th>
			<th class="text-right"><?=number_format($_total_count_sum1)?></th>
			<th class="text-right"><?=number_format($_total_count_sum2)?></th>
			<th class="text-right"><b><?=number_format($_total_sum1)?></b></th>
			<th class="text-right"><b><?=number_format($_total_sum2)?></b></th>
			<td class="text-right">
				<?=number_format($_total_sum2 - $_total_sum1)?>
				( <b><?=round( ($_total_sum2 - $_total_sum1) / $_total_sum2 * 100, 2)?></b> % )
			</td>
		</tr>
	</table>


	</div>
</div>