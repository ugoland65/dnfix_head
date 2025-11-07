<?
// 변수 초기화
$data = [];
$ps_data = [];
$_ps_idx = "";
$ps_sale_log_data = [];

if( $_prd_idx ){
	$data = sql_fetch_array(sql_query_error("select CD_IDX, cd_sale_price, cd_cost_price from "._DB_COMPARISON." where CD_IDX = '".$_prd_idx."' "));
	if (!is_array($data)) {
		$data = [];
	}
	
	$ps_data = sql_fetch_array(sql_query_error("select ps_idx, ps_sale_log from prd_stock WHERE ps_prd_idx = '".$_prd_idx."' "));
	if (!is_array($ps_data)) {
		$ps_data = [];
	}

	$_ps_idx = $ps_data['ps_idx'] ?? "";

	$ps_sale_log_data = json_decode($ps_data['ps_sale_log'] ?? '[]', true);
	if (!is_array($ps_sale_log_data)) {
		$ps_sale_log_data = [];
	}
}
?>

<?
/*
	echo "<pre>";
	print_r($ps_sale_log_data);
	echo "</pre>";
*/
?>

<table class="table-style border01 width-full">
	<tr>
		<th class="text-center">할인모드</th>
		<th class="text-center" style="width:110px">할인일</th>
		<th class="text-center">판매가</th>
		<th class="text-center">원가</th>
		<th class="text-center">마진율</th>
		<th class="text-center">할인율</th>
		<th class="text-center">할인 판매가</th>
		<th class="text-center">할인판매 마진</th>
		<th class="text-center">할인판매 마진율</th>
		<th class="text-center" style="width:150px">실적</th>
	</tr>
<?
for ($i=0; $i<count($ps_sale_log_data); $i++){
	
	// 변수 초기화
	$_sale_mode = "";
	$_pg_day = "";
	$_pg_day1 = "";
	$_pg_day2 = "";
	$_psu_day = "";
	$showings = [];
	
	// 배열 검증
	if (!is_array($ps_sale_log_data[$i])) {
		continue;
	}
	
	if( ($ps_sale_log_data[$i]['sale_mode'] ?? '') == "period" ){
		$_sale_mode = "기간할인";

		$_pg_day = ($ps_sale_log_data[$i]['pg_sday'] ?? '')." ~<br>".($ps_sale_log_data[$i]['pg_day'] ?? '');

		$_pg_sday = $ps_sale_log_data[$i]['pg_sday'] ?? '';
		$_pg_eday = $ps_sale_log_data[$i]['pg_day'] ?? '';
		
		if( !empty($_pg_sday) && !empty($_pg_eday) ){
			$_pg_day1 = date("Y-m-d",strtotime($_pg_sday." +1 days"));
			$_pg_day2 = date("Y-m-d",strtotime($_pg_eday." +1 days"));

			$_psu_day = "<span style='font-size:11px;'>".$_pg_day1." ~<br>".$_pg_day2."</span>";
			$showings = sql_fetch_array(sql_query_error("select 
			SUM(psu_qry) as qty_sum
			from prd_stock_unit WHERE psu_stock_idx = '".$_ps_idx."' AND psu_day >= '".$_pg_day1."' AND psu_day <= '".$_pg_day2."' AND psu_mode = 'minus' AND INSTR(psu_kind, '판매')  "));
			
			if (!is_array($showings)) {
				$showings = [];
			}
		}

	}else{
		$_sale_mode = "일일할인";

		$_pg_day = $ps_sale_log_data[$i]['pg_day'] ?? '';

		if( !empty($_pg_day) ){
			$_psu_day = date("Y-m-d",strtotime($_pg_day." +1 days"));
			$showings = sql_fetch_array(sql_query_error("select 
			SUM(psu_qry) as qty_sum
			from prd_stock_unit WHERE psu_stock_idx = '".$_ps_idx."' AND psu_day = '".$_psu_day."' AND psu_mode = 'minus' AND INSTR(psu_kind, '판매')  "));
			
			if (!is_array($showings)) {
				$showings = [];
			}
		}
	}

	if( ($ps_sale_log_data[$i]['original_price'] ?? 0) > 0 ){
		$_original_price = $ps_sale_log_data[$i]['original_price'];
	}else{
		$_original_price = $data['cd_sale_price'] ?? 0;
	}

	$_cost_price = $data['cd_cost_price'] ?? 0;
	
	if( $_original_price > 0 ){
		$_margin_pre = round(($_original_price - $_cost_price) / $_original_price * 100, 2);
	}else{
		$_margin_pre = 0;
	}



?>
	<tr>
		<td class="text-center"><?=$_sale_mode ?? ''?></td>
		<td class="text-center"><?=$_pg_day ?? ''?></td>
		<td class="text-right"><?=number_format($_original_price ?? 0)?></td>
		<td class="text-right"><?=number_format($_cost_price ?? 0)?></td>
		<td class="text-center"><?=$_margin_pre ?? 0?>%</td>
		<td class="text-center"><b style="color:#ff0000"><?=$ps_sale_log_data[$i]['sale_per'] ?? 0?></b>%</td>
		<td class="text-right"><?=number_format($ps_sale_log_data[$i]['sale_price'] ?? 0)?></td>
		<td class="text-right"><?=number_format($ps_sale_log_data[$i]['margin_price'] ?? 0)?></td>
		<td class="text-center"><?=$ps_sale_log_data[$i]['margin_per'] ?? 0?>%</td>
		<td class="text-center">

			<div>
				<ul><?=$_psu_day ?? ''?></ul>
				<?
					if( ($showings['qty_sum'] ?? 0) > 0 ){
				?>
					<ul class="m-t-5">판매 : <b style="color:#ff0000"><?=$showings['qty_sum']?></b>건</ul>
					<ul class="m-t-5">판매가 : <span style="color:#ff0000"><?=number_format(($ps_sale_log_data[$i]['sale_price'] ?? 0)*$showings['qty_sum'])?></span></ul>
					<ul class="m-t-5">수익 : <span style="color:#ff0000"><?=number_format(($ps_sale_log_data[$i]['margin_price'] ?? 0)*$showings['qty_sum'])?></span></ul>
				<? }else{ ?>
					<ul class="m-t-5">판매없음</ul>
				<? } ?>
			</div>

		</td>
	</tr>
<? } ?>
</table>