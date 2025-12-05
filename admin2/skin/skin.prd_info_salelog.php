<?
// 변수 초기화
$data = [];
$ps_data = [];
$_ps_idx = "";
$ps_sale_log_data = [];
$_prd_idx = $_prd_idx ?? $_get1 ?? "";

if( !empty($_prd_idx) ){
	$data = sql_fetch_array(sql_query_error("select CD_IDX, cd_sale_price, cd_cost_price from "._DB_COMPARISON." where CD_IDX = '".$_prd_idx."' "));
	if (!is_array($data) || empty($data)) {
		$data = ['CD_IDX' => '', 'cd_sale_price' => 0, 'cd_cost_price' => 0];
	}
	
	$ps_data = sql_fetch_array(sql_query_error("select ps_idx, ps_sale_log from prd_stock WHERE ps_prd_idx = '".$_prd_idx."' "));
	if (!is_array($ps_data) || empty($ps_data)) {
		$ps_data = ['ps_idx' => '', 'ps_sale_log' => '[]'];
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
if (!empty($ps_sale_log_data) && is_array($ps_sale_log_data)) {
for ($i=0; $i<count($ps_sale_log_data); $i++){
	
	// 변수 초기화
	$_sale_mode = "";
	$_pg_day = "";
	$_pg_day1 = "";
	$_pg_day2 = "";
	$_psu_day = "";
	$_original_price = 0;
	$_cost_price = 0;
	$_margin_pre = 0;
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
		
		if( !empty($_pg_sday) && !empty($_pg_eday) && !empty($_ps_idx) ){
			$_pg_day1 = date("Y-m-d",strtotime($_pg_sday." +1 days"));
			$_pg_day2 = date("Y-m-d",strtotime($_pg_eday." +1 days"));

			$_psu_day = "<span style='font-size:11px;'>".$_pg_day1." ~<br>".$_pg_day2."</span>";
			$showings = sql_fetch_array(sql_query_error("select 
			SUM(psu_qry) as qty_sum
			from prd_stock_unit WHERE psu_stock_idx = '".$_ps_idx."' AND psu_day >= '".$_pg_day1."' AND psu_day <= '".$_pg_day2."' AND psu_mode = 'minus' AND INSTR(psu_kind, '판매')  "));
			
			if (!is_array($showings)) {
				$showings = ['qty_sum' => 0];
			}
		}

	}else{
		$_sale_mode = "일일할인";

		$_pg_day = $ps_sale_log_data[$i]['pg_day'] ?? '';

		if( !empty($_pg_day) && !empty($_ps_idx) ){
			$_psu_day = date("Y-m-d",strtotime($_pg_day." +1 days"));
			$showings = sql_fetch_array(sql_query_error("select 
			SUM(psu_qry) as qty_sum
			from prd_stock_unit WHERE psu_stock_idx = '".$_ps_idx."' AND psu_day = '".$_psu_day."' AND psu_mode = 'minus' AND INSTR(psu_kind, '판매')  "));
			
			if (!is_array($showings)) {
				$showings = ['qty_sum' => 0];
			}
		}
	}

	if( ($ps_sale_log_data[$i]['original_price'] ?? 0) > 0 ){
		$_original_price = (int)($ps_sale_log_data[$i]['original_price'] ?? 0);
	}else{
		$_original_price = (int)($data['cd_sale_price'] ?? 0);
	}

	$_cost_price = (int)($data['cd_cost_price'] ?? 0);
	
	if( $_original_price > 0 ){
		$_margin_pre = round(($_original_price - $_cost_price) / $_original_price * 100, 2);
	}else{
		$_margin_pre = 0;
	}
	
	// 할인 판매가 및 마진 계산
	$_sale_price = (int)($ps_sale_log_data[$i]['sale_price'] ?? 0);
	$_margin_price = (int)($ps_sale_log_data[$i]['margin_price'] ?? 0);
	$_margin_per = $ps_sale_log_data[$i]['margin_per'] ?? 0;
	$_sale_per = $ps_sale_log_data[$i]['sale_per'] ?? 0;



?>
	<tr>
		<td class="text-center"><?=$_sale_mode?></td>
		<td class="text-center"><?=$_pg_day?></td>
		<td class="text-right"><?=number_format($_original_price)?></td>
		<td class="text-right"><?=number_format($_cost_price)?></td>
		<td class="text-center"><?=$_margin_pre?>%</td>
		<td class="text-center"><b style="color:#ff0000"><?=$_sale_per?></b>%</td>
		<td class="text-right"><?=number_format($_sale_price)?></td>
		<td class="text-right"><?=number_format($_margin_price)?></td>
		<td class="text-center"><?=$_margin_per?>%</td>
		<td class="text-center">

			<div>
				<? if( !empty($_psu_day) ){ ?>
					<ul><?=$_psu_day?></ul>
				<? } ?>
				<?
					$qty_sum = (int)($showings['qty_sum'] ?? 0);
					if( $qty_sum > 0 ){
				?>
					<ul class="m-t-5">판매 : <b style="color:#ff0000"><?=$qty_sum?></b>건</ul>
					<ul class="m-t-5">판매가 : <span style="color:#ff0000"><?=number_format($_sale_price * $qty_sum)?></span></ul>
					<ul class="m-t-5">수익 : <span style="color:#ff0000"><?=number_format($_margin_price * $qty_sum)?></span></ul>
				<? }else{ ?>
					<ul class="m-t-5">판매없음</ul>
				<? } ?>
			</div>

		</td>
	</tr>
<? } // for END
} else { // 할인 이력 없음
?>
	<tr>
		<td colspan="10" class="text-center" style="padding:30px;">
			할인 이력이 없습니다.
		</td>
	</tr>
<? } // if (!empty($ps_sale_log_data)) END
?>
</table>