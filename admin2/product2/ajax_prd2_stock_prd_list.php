<?
$pageGroup = "product";
$pageName = "category_form";

include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	if( !$_mode ) $_mode = "all";

	if( $_mode == "all" ){
		$_serch_query = " where CD_IDX > 0 ";
	}else{
		$_serch_query = " where prd.CD_KIND_CODE = '".$_mode."' ";
	}


	//검색이 있을경우
	if( $_s_active == "on" AND $_s_text != "" ){

		if (preg_match("/[a-zA-Z]/", $_s_text)){
			$_serch_query .= " AND INSTR(LOWER(CD_NAME), LOWER('".$_s_text."')) ";
		}else{
			$_serch_query .= " AND INSTR(LOWER(CD_NAME), '".$_s_text."') ";
		}
	}


	$total_count = wepix_counter(_DB_COMPARISON, $_serch_query);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	if( $_sort_kind == "hit" ) {
		$_sort_text = "CD_HIT desc";
	}else{
		$_sort_text = "CD_IDX desc";
	}

	$query = "select * from prd_stock stock inner join "._DB_COMPARISON." prd ON (stock.ps_prd_idx = prd.CD_IDX ) ".$_serch_query." AND stock.ps_mode = 'basic'  order by stock.ps_stock desc, stock.ps_idx desc";
	$result = wepix_query_error($query);

	$page_link_text = _A_PATH_COMPARISON_LIST."?s_active=".$_s_active."&s_text=".$_s_text."&sort_kind=".$_sort_kind."&pn=";
	$_view_paging = publicPaging($pn, $total_page, $list_num, $page_num, $page_link_text);

?>
	<table class="table-list">
<?
	$query_set = "select * from prd_stock WHERE ps_mode = 'set'  order by ps_idx desc";
	$result_set = wepix_query_error($query_set);
	while($list_set = wepix_fetch_array($result_set)){
?>
		<tr bgcolor='#ddf7e9'>
			<td><?=$list_set[ps_idx]?></td>
			<td>세트상품</td>
			<td style="text-align:left"><span id="ps_prd_name_<?=$list_set[ps_idx]?>" style="color:#000; font-size:13px"><?=$list_set[ps_name]?></span></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="prd2_stock_cart_set('<?=$list_set[ps_idx]?>', '<?=$list_set[ps_set_value]?>');"><i class="fa fa-minus-circle" ></i></button></td>
		</tr>
<? } ?>

<?
	while($list = wepix_fetch_array($result)){
		$brand_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$list[CD_BRAND_IDX]."' "));
		$_view_brand_name = $brand_data[BD_NAME];

		$_tr_color = "";
		if( $list[ps_stock] == 0 ){
			$_tr_color = "bgcolor='#eee' ";
		}
?>
		<tr <?=$_tr_color?>>
			<td><?=$list[ps_idx]?></td>
			<td><span id="ps_brand_name_<?=$list[ps_idx]?>"><?=$_view_brand_name?></span></td>
			<td style="text-align:left">
				<span id="ps_prd_name_<?=$list[ps_idx]?>" style="color:#000; font-size:13px"><?=$list[CD_NAME]?></span>
				<?if( $list[CD_NAME_EN]){ ?><br><span style="color:#999; font-size:11px">(<?=$list[CD_NAME_EN]?>)</span><? }?>
			</td>
			<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="stockAlarm('<?=$list[ps_idx]?>');"><i class="far fa-clock"></i></button></td>
			<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="stockHistory('<?=$list[ps_idx]?>');"><i class="far fa-calendar-alt"></i></button></td>
			<td><?=number_format($list[ps_stock_all])?> | <?=number_format($list[ps_stock_all]-$list[ps_stock])?></td>
			<td><b style="color:#000; font-size:13px"><?=$list[ps_stock]?></b></td>
			<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prd2_stock_cart('<?=$list[ps_idx]?>','plus');"><i class="fa fa-plus-circle" ></i></button></td>
			<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="prd2_stock_cart('<?=$list[ps_idx]?>','minus');"><i class="fa fa-minus-circle" ></i></button></td>
		</tr>
	<? } ?>
	</table>


<?
exit;
?>