<?
$pageGroup = "product2";
$pageName = "prd2_stock2";

include "../lib/inc_common.php";

	
	$onaholeTotalPriceJP = 0;
	$onaholeTotalPriceKR = 0;
	$onaholeJpPrice = array();

	$gelTotalPriceJP = 0;
	$gelPriceKR = 0;
	$gelJpPrice = array();

	$stockOnaHoleResult = wepix_query_error("SELECT ps_prd_idx,ps_idx,ps_stock FROM prd_stock WHERE ps_kind = 'ONAHOLE' AND ps_stock != 0 ");
	$stockGelResult = wepix_query_error("SELECT ps_idx,ps_stock FROM prd_stock WHERE ps_kind = 'GEL' ");

	

/*
	while($gellist = wepix_fetch_array($stockGelResult)){
			
			unset( $gelJpPrice );
			$gelData = wepix_fetch_array(wepix_query_error("SELECT * FROM COMPARISON_DB WHERE CD_IDX ='".$gellist[ps_idx]."'"));
			$gelJpPrice[] = $gelData[CD_SUPPLY_PRICE_2];
			$gelJpPrice[] = $gelData[CD_SUPPLY_PRICE_6];
			$gelJpPrice[] = $gelData[CD_SUPPLY_PRICE_9];
			$gelJpPrice[] = $gelData[CD_SUPPLY_PRICE_7];
			$gelJpPrice[] = $gelData[CD_SUPPLY_PRICE_8];
			$gelJpPrice[] = $gelData[CD_SUPPLY_PRICE_1];
			$supplyJpPriceFilter = array_filter($gelJpPrice);
			$miniPriceJp = min($supplyJpPriceFilter);
			$gelTotalPriceJP += ($miniPriceJp * $gellist[ps_stock]);
	}
*/



include "../layout/header.php";
?>

<script type="text/javascript"> 

</script> 

<div id="contents_head">
	<h1>ㅁㅁㅁ</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div id="" class="list-box-layout3 display-table">
			<ul class="display-table-cell width-50p v-align-top">


				<table class="table-list">
					<tr>
						<th>상품이름</th>
						<th>상품 최저가격</th>
						<th>상품 재고수</th>
						<th>상품 총가격</th>
					</tr>
<?
	while($list = wepix_fetch_array($stockOnaHoleResult)){
			$onaholeTotalPriceJP2 = 0;
			unset( $onaholeJpPrice );
			$onaholeData = wepix_fetch_array(wepix_query_error("SELECT * FROM COMPARISON_DB WHERE CD_IDX ='".$list[ps_prd_idx]."'"));
			$onaholeJpPrice[] = $onaholeData[CD_SUPPLY_PRICE_2];
			$onaholeJpPrice[] = $onaholeData[CD_SUPPLY_PRICE_6];
			$onaholeJpPrice[] = $onaholeData[CD_SUPPLY_PRICE_9];
			$onaholeJpPrice[] = $onaholeData[CD_SUPPLY_PRICE_7];
			$onaholeJpPrice[] = $onaholeData[CD_SUPPLY_PRICE_8];
			$onaholeJpPrice[] = $onaholeData[CD_SUPPLY_PRICE_1];
			$supplyJpPriceFilter = array_filter($onaholeJpPrice);
			$miniPriceJp = min($supplyJpPriceFilter);
			$onaholeTotalPriceJP += ($miniPriceJp * $list[ps_stock]);
			$onaholeTotalPriceJP2 += ($miniPriceJp * $list[ps_stock]);
	
?>
					<tr>
						<td><?=$onaholeData[CD_NAME]?></td>
						<td><?=number_format($miniPriceJp)?></td>
						<td><?=$list[ps_stock]?></td>
						<td><?=number_format($onaholeTotalPriceJP2)?></td>
					</tr>

<?}?>
				<TR>
					<th colspan='2'>Total</th>
					<th colspan='2'><?=number_format($onaholeTotalPriceJP)?></th>
				<tr>
				</table>

			</ul>
		</div>
	</div>
</div>





<?
include "../layout/footer.php";
exit;
?>