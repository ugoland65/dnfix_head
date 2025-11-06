<?
	include "../lib/inc_common.php";
	include 'board_inc.php';

	$_prd_idx = securityVal($prd_idx);

	$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx from prd_stock where ps_prd_idx = '".$_prd_idx."' "));
	$_ps_idx = $stock_data[ps_idx];

?>
<STYLE TYPE="text/css">
.table-list{ background-color:#fff; }
</STYLE>

	<table class="table-list">
	<?
	$_count = 0;
	$query = "select * from prd_stock_unit where psu_stock_idx = '".$_ps_idx."' order by psu_date desc";
	$result = wepix_query_error($query);

	while($list = wepix_fetch_array($result)){
		if( $list[psu_mode]=="plus" ){
			$_mode_icon = "▲";
			$_mode_color = "#1a02ff";
		}elseif( $list[psu_mode]=="minus" ){
			$_mode_icon = "▼";
			$_mode_color = "#ff0202";
		}

	?>
		<tr <?=$_tr_color?>>
			<td><?=$list[psu_idx]?></td>
			<td><?=$list[psu_day]?></td>
			<td style="color:<?=$_mode_color?>;"><?=$_mode_icon?> <b><?=$list[psu_qry]?></b></td>
			<td style="color:<?=$_mode_color?>;"><b><?=$list[psu_stock]?></b></td>
			<td><?=$list[psu_kind]?></td>
			<td><?=$list[psu_memo]?></td>
			<td><?=$list[psu_id]?></td>
			<td><?=date("Y-m-d H:i:s", $list[psu_date])?></td>
		</tr>

	<? 
		$_count++;	
	} ?>
	</table>