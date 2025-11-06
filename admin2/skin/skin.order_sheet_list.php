<?
	if( $_mode == "연관" ){
		$_where = " WHERE oo_form_idx = '".$_code."' ";
	}elseif( $_mode == "수입" ){
		$_where = " WHERE oo_import IN ('수입', '구매대행') ";
	}else{
		$_where = " WHERE oo_import = '국내' ";
	}
	

	$total_count = wepix_counter("ona_order", $_where);
	
	$list_num = 22;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$_query = "select * from ona_order ".$_where." ORDER BY oo_sort desc limit ".$from_record.", ".$list_num;
	$_result = sql_query_error($_query);

?>

<style type="text/css">
.opltb{ width:100%; }	
.opltb tr{}	
.opltb tr td{ padding:5px 3px; border:1px solid #ddd; }	
</style>

<table class="opltb m-t-5">
<?
$_order_sheet_state_text[1] = "작성중";
$_order_sheet_state_text[2] = "주문전송";
$_order_sheet_state_text[4] = "입금완료";
$_order_sheet_state_text[5] = "입고완료";
$_order_sheet_state_text[7] = "주문종료";

while($list = sql_fetch_array($_result)){

	if( $list['oo_state'] == "2" ){
		$_tr_color = "#c1ebff";
	}elseif( $list['oo_state'] == "4" ){
		$_tr_color = "#f6f0ac";
	}elseif( $list['oo_state'] == "7" ){
		$_tr_color = "#eaeaea";
	}else{
		$_tr_color = "#ffffff";
	}

	if( $list['oo_r_mode'] == "V3" ){
		$_oo_r_mode = "<i class='fas fa-star'></i> ";
	}else{
		$_oo_r_mode = "";
	}

?>
	<tr bgcolor="<?=$_tr_color?>">
		<td style="width:60px; text-align:center;"><?=$_order_sheet_state_text[$list['oo_state']]?></td>
		<td style="width:65px; text-align:right;"><?=number_format($list['oo_sum_price'])?></td>
		<td>
			<a href="/ad/order/order_sheet/<?=$list['oo_idx']?>"><?=$_oo_r_mode?><?=$list['oo_name']?></a>
			<!-- <a onclick="orderSheet.Detail('<?=$list['oo_idx']?>')"><?=$list['oo_name']?></a> -->
		</td>
	</tr>
<? } ?>
</table>