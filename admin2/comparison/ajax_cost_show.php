<?
$pageGroup = "product";
$pageName = "category_form";

include "../lib/inc_common.php";

	$_idx = securityVal($idx);
	//$_yen = securityVal($yen);
	$_kg_p = securityVal($kg_p);

	$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));

	$_cd_price_data = json_decode($comparison_data[cd_price_fn], true);

	$_cd_weight_data = json_decode($comparison_data[cd_weight_fn], true);
	$_cd_weight_1 = $_cd_weight_data['1'];
	$_cd_weight_2 = $_cd_weight_data['2'];
	$_cd_weight_3 = $_cd_weight_data['3'];

	$_sale_price = $comparison_data[CD_SALE_PRICE];

/*
	if( $comparison_data[cd_national] == "jp" ){
		$_yen = $yen;
*/
	if( $comparison_data[cd_national] == "cn" ){
		$_yen = $yen_cn;
	}else{
		$_yen = $yen;
	}

	//쑈당몰 판매가 찾기
	if( $comparison_data[CD_SALE_PRICE] == 0 ){
		$find = wepix_fetch_array(wepix_query_error("select CL_IDX, CL_PRICE from "._DB_COMPARISON_LINK." where CL_SD_IDX = '7' AND CL_CD_IDX = '".$_idx."' "));
		if( $find[CL_IDX] && $find[CL_PRICE] ){
			$query = "update  "._DB_COMPARISON." set CD_SALE_PRICE = '".$find[CL_PRICE]."' where CD_IDX = '".$_idx."' ";
			wepix_query_error($query);
			$_sale_price = $find[CL_PRICE];
		}
	}

	//if( $comparison_data[CD_WEIGHT2] > 0 ) { $_weight = $comparison_data[CD_WEIGHT2]; }else{ $_weight = $comparison_data[CD_WEIGHT]; }

	if( $_cd_weight_3 > 0 ) { 
		$_weight = $_cd_weight_3;
	}else{
		if( $_cd_weight_2 > 0 ) { $_weight = $_cd_weight_2; }else{ $_weight = $_cd_weight_1; }
	}

?>

<? 
if( $_cd_price_data['mg'] > 0 && $_weight > 0 ){ 
	if( $_sale_price > 0 ){ $_ary_margin[] = makeKoedgeMargin($_cd_price_data['mg'], $_weight, $_yen, $_kg_p, $_sale_price); }
?>
	<div class="show_cost">
		<ul class="sc-title">매직아이즈 : 환율1070 배송비 (6,000/kg)</ul>
		<ul><?=makeKoedgeCost($_cd_price_data['mg'], $_weight, $_yen, $_kg_p, $_sale_price, $comparison_data[cd_national])?></ul>
	</div>
<? } ?>

<? 
if( $_cd_price_data['tis'] > 0 && $_weight > 0 ){ 
	if( $_sale_price > 0 ){ $_ary_margin[] = makeKoedgeMargin($_cd_price_data['tis'], $_weight, $_yen, $_kg_p, $_sale_price); }
?>
	<div class="show_cost">
		<ul class="sc-title">TIS (아웃비젼) : 환율1070 배송비 (6,000/kg)</ul>
		<ul><?=makeKoedgeCost($_cd_price_data['tis'], $_weight, $_yen, $_kg_p, $_sale_price, $comparison_data[cd_national])?></ul>
	</div>
<? } ?>

<? 
if( $_cd_price_data['npg'] > 0 && $_weight > 0 ){ 
	if( $_sale_price > 0 ){ $_ary_margin[] = makeKoedgeMargin($_cd_price_data['npg'], $_weight, $_yen, $_kg_p, $_sale_price); }
?>
	<div class="show_cost">
		<ul class="sc-title">N.P.G</ul>
		<ul><?=makeKoedgeCost($_cd_price_data['npg'], $_weight, $_yen, $_kg_p, $_sale_price, $comparison_data[cd_national])?></ul>
	</div>
<? } ?>

<? 
if( $_cd_price_data['tma'] > 0 && $_weight > 0 ){ 
	if( $_sale_price > 0 ){ $_ary_margin[] = makeKoedgeMargin($_cd_price_data['tma'], $_weight, $_yen, $_kg_p, $_sale_price); }
?>
	<div class="show_cost">
		<ul class="sc-title">타마토이즈</ul>
		<ul><?=makeKoedgeCost($_cd_price_data['tma'], $_weight, $_yen, $_kg_p, $_sale_price, $comparison_data[cd_national])?></ul>
	</div>
<? } ?>

<? 
if( $_cd_price_data['rj'] > 0 && $_weight > 0 ){ 
	if( $_sale_price > 0 ){ $_ary_margin[] = makeKoedgeMargin($_cd_price_data['rj'], $_weight, $_yen, $_kg_p, $_sale_price); }
?>
	<div class="show_cost">
		<ul class="sc-title">라이드 재팬</ul>
		<ul><?=makeKoedgeCost($_cd_price_data['rj'], $_weight, $_yen, $_kg_p, $_sale_price, $comparison_data[cd_national])?></ul>
	</div>
<? } ?>

<? 
if( $_cd_price_data['dmw'] > 0 && $_weight > 0 ){ 
	if( $_sale_price > 0 ){ $_ary_margin[] = makeKoedgeMargin($_cd_price_data['dmw'], $_weight, $_yen, $_kg_p, $_sale_price); }
?>
	<div class="show_cost">
		<ul class="sc-title">데몬킹</ul>
		<ul><?=makeKoedgeCost($_cd_price_data['dmw'], $_weight, $_yen, $_kg_p, $_sale_price, $comparison_data[cd_national])?></ul>
	</div>
<? } ?>

<? 
if( $comparison_data[CD_SUPPLY_PRICE_2] > 0 && $_weight > 0 ){ 
	if( $_sale_price > 0 ){ $_ary_margin[] = makeKoedgeMargin($comparison_data[CD_SUPPLY_PRICE_2], $_weight, $_yen, $_kg_p, $_sale_price); }
?>
	<div class="show_cost">
		<ul class="sc-title">토이즈하트</ul>
		<ul><?=makeKoedgeCost($comparison_data[CD_SUPPLY_PRICE_2], $_weight, $_yen, $_kg_p, $_sale_price, $comparison_data[cd_national])?></ul>
	</div>
<? } ?>



<? 
if( $comparison_data[CD_SUPPLY_PRICE_7] > 0  ){ 
	if( $_sale_price > 0 ){ $_ary_margin[] = makeKoedgeMargin($comparison_data[CD_SUPPLY_PRICE_7], $_weight, $_yen, $_kg_p, $_sale_price); }
?>
	<div class="show_cost">
		<ul class="sc-title">브랜드 A</ul>
		<ul><?=makeKoedgeCost($comparison_data[CD_SUPPLY_PRICE_7], $_weight, $_yen, $_kg_p, $_sale_price, $comparison_data[cd_national])?></ul>
	</div>
<? } ?>

<? 
if( $comparison_data[CD_SUPPLY_PRICE_8] > 0 && $_weight > 0 ){ 
	if( $_sale_price > 0 ){ $_ary_margin[] = makeKoedgeMargin($comparison_data[CD_SUPPLY_PRICE_8], $_weight, $_yen, $_kg_p, $_sale_price); }
?>
	<div class="show_cost">
		<ul class="sc-title">라이드재팬B</ul>
		<ul><?=makeKoedgeCost($comparison_data[CD_SUPPLY_PRICE_8], $_weight, $_yen, $_kg_p, $_sale_price, $comparison_data[cd_national])?></ul>
	</div>
<? } ?>

<? 
if( $comparison_data[CD_SUPPLY_PRICE_1] > 0 && $_weight > 0 ){ 
	if( $_sale_price > 0 ){ $_ary_margin[] = makeKoedgeMargin($comparison_data[CD_SUPPLY_PRICE_1], $_weight, $_yen, $_kg_p, $_sale_price); }
?>
	<div class="show_cost">
		<ul class="sc-title">NLS</ul>
		<ul><?=makeKoedgeCost($comparison_data[CD_SUPPLY_PRICE_1], $_weight, $_yen, $_kg_p, $_sale_price, $comparison_data[cd_national])?></ul>
	</div>
<? } ?>

<?
if( $_sale_price > 0 && max($_ary_margin) ){
	$query = "update  "._DB_COMPARISON." set CD_SALE_MARGIN_PER = '".max($_ary_margin)."' where CD_IDX = '".$_idx."' ";
	wepix_query_error($query);
?>
<div style="font-size:13px; margin-top:10px;">
적용 마진 : <b class='o-p'><?=max($_ary_margin);?>%</b>
</div>
<? } ?>

<?
exit;
?>