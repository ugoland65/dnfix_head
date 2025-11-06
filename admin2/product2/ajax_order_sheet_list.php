<?
include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	$_code = securityVal($code);

	$_koedge_order_state_text[1] = "작성중";
	$_koedge_order_state_text[2] = "주문전송";
	$_koedge_order_state_text[4] = "입금완료";
	$_koedge_order_state_text[5] = "입고완료";
	$_koedge_order_state_text[7] = "주문종료";

	$_ko_code = "'koetc','roma','lust','ko5','ko4','ko3','ko2','ko1','tenga'";

	if( $_mode == "ko" ){
		$_where = " WHERE oo_code IN (".$_ko_code.") ";
		$_where2 = " WHERE oog_code IN (".$_ko_code.") ";
	}else{
		$_where = " WHERE oo_code NOT IN (".$_ko_code.") ";
		$_where2 = " WHERE oog_code NOT IN (".$_ko_code.") ";
	}

	if( $_code && $_code != "all" ){
		$_where = " WHERE oo_code = '".$_code."' ";
	}

?>

<STYLE TYPE="text/css">
.opltb{ width:100%; }	
.opltb tr{}	
.opltb tr td{ padding:5px 3px; border:1px solid #ddd; }	
</STYLE>

	<select name="ona_order_group_list" id="ona_order_group_list" data-mode="<?=$_mode?>">
		<option value="" >주문처 선택</option>
		<option value="all"  <? if( $_code == "all" ) echo "selected";?>>전체보기</option>
<?
$query = "select * from ona_order_group ".$_where2." order by oog_idx desc";
$result = wepix_query_error($query);
while($list = wepix_fetch_array($result)){
?>
		<option value="<?=$list[oog_code]?>" <? if( $_code == $list[oog_code] ) echo "selected";?> ><?=$list[oog_name]?></option>
<? } ?>
	</select>

<table class="opltb m-t-5">
<?
$query = "select * from ona_order ".$_where." order by oo_sort desc limit 0, 25";
$result = wepix_query_error($query);
while($list = wepix_fetch_array($result)){

	if( $list[oo_state] == "2" ){
		$_tr_color = "#c1ebff";
	}elseif( $list[oo_state] == "4" ){
		$_tr_color = "#f6f0ac";
	}elseif( $list[oo_state] == "7" ){
		$_tr_color = "#eaeaea";
	}else{
		$_tr_color = "#ffffff";
	}

	if( $list[oo_r_mode] == "V3" ){
		$_oo_r_mode = "<i class='fas fa-star'></i> ";
	}else{
		$_oo_r_mode = "";
	}
?>
	<tr bgcolor="<?=$_tr_color?>">
		<td style="width:60px; text-align:center;"><?=$_koedge_order_state_text[$list[oo_state]]?></td>
		<td style="width:65px; text-align:right;"><?=number_format($list[oo_sum_price])?></td>
		<td><a href="order_sheet_test3.php?oo_idx=<?=$list[oo_idx]?>"><?=$_oo_r_mode?><?=$list[oo_name]?></a></td>
	</tr>
<? } ?>
</table>

<script type="text/javascript"> 
<!-- 
$(function(){

	$("#ona_order_group_list").change(function(){
		orderSheet.List($(this).data('mode'), $(this).val());
	});

});
//--> 
</script> 