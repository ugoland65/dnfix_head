<?


	if( $_work_manual_cate == "all" ){
		$_where = "  ";
	}elseif( $_work_manual_cate ){
		$_where = " WHERE category = '".$_work_manual_cate."' ";
	}

	
	if( $_pn == "" ) $_pn = 1;

	$total_count = wepix_counter("work_manual", $_where);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "orderSheetMain.list", "");

	$_query = "select * from work_manual ".$_where." ORDER BY idx desc limit ".$from_record.", ".$list_num;
	$_result = sql_query_error($_query);

	$_order_sheet_state_text[1] = "작성중";
	$_order_sheet_state_text[2] = "주문전송";
	$_order_sheet_state_text[4] = "입금완료";
	$_order_sheet_state_text[5] = "입고완료";
	$_order_sheet_state_text[7] = "주문종료";

?>
<table class="table-style">	
	<tr class="list">
		<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
		<th class="list-idx">고유번호</th>
		<th class="">카테고리</th>
		<th class="">제목</th>
		<th class="">작성자</th>
		<th>작성일</th>

	</tr>
	<?
	while($list = sql_fetch_array($_result)){

		$_reg = json_decode($list['reg'], true);
	?>
	<tr align="center" id="trid_<?=$list['idx']?>" class="<?=$_tr_class?>">
		<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list['idx']?>" ></td>	
		<td class="list-idx"><?=$list['idx']?></td>
		<td class=""><?=$list['category']?></td>
		<td class="text-left" style="font-size:14px;"><a href="/ad/staff/work_manual_view/<?=$list['idx']?>"><?=$list['subject']?></a></td>
		<td class=""><?=$_reg['reg']['name']?></td>
		<td class=""><?=$list['reg_date']?></td>
	<tr>
	<? } ?>
</table>

<div id="hidden_pageing_ajax_data" style="display:none;"><?=$view_page?></div>
<script type="text/javascript"> 
pageingAjaxShow();
</script> 