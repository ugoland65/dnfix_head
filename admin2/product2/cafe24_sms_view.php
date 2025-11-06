<?
include "../lib/inc_common.php";

	$_idx = securityVal($idx);
	$data = wepix_fetch_array(wepix_query_error("select * from cafe24_sms where uid = '".$_idx."' "));

	$_json_data = json_decode($data['data'],true);
	$_json_data = arr_sort( $_json_data,'count', 'desc' );

?>

<table class="table-list" id="stock_prd_cart">
<?
$total_qty_sum = 0;

for ($i=0; $i<count($_json_data); $i++){

	$_show_code = $_json_data[$i]['code'];
	$_mycode = $_json_data[$i]['mycode'];
	$_brand = $_json_data[$i]['brand'];
	$_cdidx = $_json_data[$i]['cdidx'];
	$_prdname = $_json_data[$i]['prdname'];
	$_count = $_json_data[$i]['count'];
	$_mindate = $_json_data[$i]['mindate'];

	if( $_count == "1" ){
		$_bgcolor = "#ddd";
	}elseif( $_count == "2" ){
		$_bgcolor = "#eee";
	}elseif( $_count == "3" ){
		$_bgcolor = "#f5f5f5";
	}else{
		$_bgcolor = "#fff";
	}
?>
	<tr bgcolor="<?=$_bgcolor?>">
		<td><?=$_show_code?></td>
		<td><?=$_mycode?></td>
		<td><?=$_brand?></td>
		<td class="text-left">
			<? if( $_cdidx ) { ?>
			<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="comparisonQuick('<?=$_cdidx?>','info');"">보기</button>
			<? } ?>
			<?=$_prdname?>
		</td>
		<td><b><?=$_count?></b></td>
		<td><?=date('Y-m-d H:i:s', $_mindate)?></td>
	</tr>
<? } ?>
</table>