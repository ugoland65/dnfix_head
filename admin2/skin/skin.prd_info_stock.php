<?

	$_where = " WHERE psu_stock_idx = '".$_ps_idx."' ";

	if( $_sdate && $_edate ){
		$_where .= " AND psu_day >= '".$_sdate."' AND psu_day <= '".$_edate."' ";
	}

	if( $_pn == "" ) $_pn = 1;

	$total_count = sql_counter("prd_stock_unit", $_where);

	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "prdInfo.mode", ", 'stock'");

	$_count = 0;
	$query = "select * from prd_stock_unit ".$_where." ORDER BY psu_date DESC, psu_idx DESC limit ".$from_record.", ".$list_num;
	$result = sql_query_error($query);

?>
<style type="text/css">
.list-search-date-box{ padding-bottom:10px; }
.calendar-input{ display:inline-block; }
table.table-list thead { position: sticky; top: 0; }
table.table-list tbody tr td { background-color:#fff; }
</style>

<div class="list-search-date-box">
	<div class="calendar-input">
		<input type='text' name='search_date_s' id='search_date_s' value="<?=$_sdate?>" placeholder="시작일" autocomplete="off">
	</div>
	~
	<div class="calendar-input">
		<input type='text' name='search_date_e' id='search_date_e'  value="<?=$_edate?>" placeholder="끝일" autocomplete="off">
	</div>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdInfo.mode('1', 'stock')" >기간검색</button>
	<? if( $_sdate && $_edate ){ ?>
	<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="stockList.reset()" >검색초기화</button>
	<? } ?>
</div>

<div class="total">Total : <span><b><?=$total_count?></b></span> &nbsp; | &nbsp;  <span><b><?=$_pn?></b></span> / <?=$total_page?> page</div>

<table class="table-list">
	<thead>
		<tr>
			<th>idx</th>
			<th>날짜</th>
			<th>구분</th>
			<th>수량</th>
			<th>남은재고</th>
			<th>종류</th>
			<th>비고</th>
			<th>처리인</th>
			<th>처리시간</th>
			<th>수정</th>
		</tr>
	</thead>
	<tbody>
<?
while($list = sql_fetch_array($result)){
	
	if( $list['psu_mode']=="plus" || $list['psu_mode']=="to_hold" || $list['psu_mode']=="plus_to_stock" || $list['psu_mode']=="plus_hold" ){
		$_mode_icon = "▲";
		$_mode_color = "#1a02ff";
	}elseif( $list['psu_mode']=="minus" || $list['psu_mode']=="minus_to_hold" || $list['psu_mode']=="to_stock" || $list['psu_mode']=="minus_hold" ){
		$_mode_icon = "▼";
		$_mode_color = "#ff0202";
	}

	if( $list['psu_mode']=="plus" || $list['psu_mode']=="minus" || $list['psu_mode']=="minus_to_hold" || $list['psu_mode']=="plus_to_stock" ){
		$_event_name = "일반재고";
	}elseif( $list['psu_mode']=="to_hold" || $list['psu_mode']=="to_stock"  || $list['psu_mode']=="plus_hold" || $list['psu_mode']=="minus_hold" ){
		$_event_name = "<b style='color:#cd46dd;'>보류재고</b>";
	}


?>
	<tr <?=$_tr_color?>>
		<td><?=$list['psu_idx']?></td>
		<td><?=$list['psu_day']?></td>
		<td><?=$_event_name?></td>
		<td style="color:<?=$_mode_color?>;"><?=$_mode_icon?> <b><?=$list['psu_qry']?></b></td>
		<td style="color:<?=$_mode_color?>;"><b><?=$list['psu_stock']?></b></td>
		<td><?=$list['psu_kind']?></td>
		<td><?=$list['psu_memo']?></td>
		<td><?=$list['psu_id']?></td>
		<td><?=date("Y-m-d H:i:s", $list['psu_date'])?></td>
		<td><button type="button" id="" class="btnstyle1 btnstyle1-xs" onclick="prdInfo.stockUnitModify('<?=$list['psu_idx']?>', '<?=$_pn?>')" >수정</button></td>
	</tr>

<? 
	$_count++;	
} ?>
	</tbody>
</table>

<div class="pageing-unit-wrap"><?=$view_page?></div>

<script type="text/javascript"> 
<!-- 

var stockList = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		reset : function() {
			$("#search_date_s").val("");
			$("#search_date_e").val("");
			prdInfo.mode('1', 'stock');
		}
	};

}();

$(function(){

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}

});
//--> 
</script> 