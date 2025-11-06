<?
	$_where = "";

	$total_count = wepix_counter("ona_order_group", $_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	//$_query = "select * from "._DB_ADMIN." ".$_where." order by AD_IDX desc limit ".$from_record.", ".$list_num;
	$_query = "select * from ona_order_group ".$_where." ORDER BY oog_idx DESC";
	$_result = sql_query_error($_query);

?>
<div id="contents_head">
	<h1>주문서폼 관리</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="orderSheetForm.reg()" > 
			<i class="fas fa-plus-circle"></i>
			신규 주문서폼 생성
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<table class="table-style">	
			<tr class="list">
				<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
				<th class="list-idx">고유번호</th>
				<th class="">폼이름</th>
				<th class="">수입형태</th>
				<th class="">국가</th>
				<th class="">가격코드</th>
				<th width="60px">관리</th>
			</tr>
		<?

		$_oog_nation_text['jp'] = "일본";
		$_oog_nation_text['ko'] = "한국";
		$_oog_nation_text['cn'] = "중국";
		$_oog_nation_text['dol'] = "달러국가";
		$_oog_nation_text['etc'] = "기타";

		while($_list = wepix_fetch_array($_result)){

		?>
		<tr align="center" id="trid_<?=$_list['oog_idx']?>" bgcolor="<?=$trcolor?>">
			<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$_list['oog_idx']?>" ></td>	
			<td class="list-idx"><?=$_list['oog_idx']?></td>
			<td class=""><b><?=$_list['oog_name']?></b></td>
			<td class=""><?=$_list['oog_import']?></td>
			<td class=""><?=$_oog_nation_text[$_list['oog_group']]?></td>
			<td class=""><?=$_list['oog_code']?></td>
			<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetForm.view('<?=$_list['oog_idx']?>')"> 수정 </button></td>
		<tr>
		<? }?>

	</table>

	</div>
</div>

<script type="text/javascript"> 
<!-- 
//--> 
</script>
<script src="/admin2/js/order_sheet.js?ver=<?=$wepix_now_time?>"></script>