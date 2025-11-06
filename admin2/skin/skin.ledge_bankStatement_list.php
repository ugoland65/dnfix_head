<?

	if( $_s_text ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " INSTR( name, '".$_s_text."' ) or INSTR( memo, '".$_s_text."') ";



	}
	
	if( $_s_kind ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " bs_mode = '".$_s_kind."' ";
	}

	if( $_s_bank ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " INSTR( bank, '".$_s_bank."' ) ";
	}

	if( $_s_s_date && $_s_e_date ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " date >= '".$_s_s_date." 00:00:00' AND date <= '".$_s_e_date." 23:59:59' ";
	}

	if( $_s_state ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " state = '".$_s_state."' ";
	}

	if( $_pn == "" ) $_pn = 1;

	$total_count = wepix_counter("bank_statement", $_where);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "ledge.list", "");

	$_query = "select 
		A.*, B.lc_name
		from bank_statement A
		left join ledge_category B ON ( B.idx = A.ledge_cate_idx )
		".$_where." ORDER BY date DESC, idx DESC limit ".$from_record.", ".$list_num;
	$_result = sql_query_error($_query);

?>

<style type="text/css">
.search-title{ font-size:16px; padding-bottom:10px; }
.search-title b{ color:#d40202; }

.table-style tr.tr-n td{ background-color:#fffbc1 !important; }
.table-style tr.tr-y td{ background-color:#fff !important; }
</style>

<? if( $_s_text ){ ?>
	<div class="search-title">	
		검색어 ( <b style='color:red;'><?=$_s_text?></b> ) 검색결과 : <b><?=$total_count?></b>건 검색되었습니다.
		<button type="button" class="search-reset-btn btn btnstyle1 btnstyle1-inverse btnstyle1-sm" id="search_reset">검색 초기화</button>
	</div>
<? }else{ ?>
	<div class="total">Total : <span><b><?=number_format($total_count)?></b></span> &nbsp; | &nbsp;  <span><b><?=$_pn?></b></span> / <?=$total_page?> page</div>
<? } ?>

<table class="table-style">	
	<tr class="list">
		<th class="list-checkbox"><input type="checkbox" name="" class="check_box_all"></th>
		<th class="list-idx">고유번호</th>
		<th class="">확인</th>
		<th class="">은행</th>
		<th class="">날짜</th>
		<th class="">이름</th>
		<th class="">입금</th>
		<th class="">출금</th>
		<th class="">항목</th>
		<th class="">메모</th>
		<th class="">관리</th>
	</tr>
	
	<?
	$_state_text['N'] = "미확인";
	$_state_text['Y'] = "확인";

	while($list = sql_fetch_array($_result)){

		if( $list['state'] == "N" ){
			$_tr_class = "tr-n";
		}else{
			$_tr_class = "tr-y";
		}

		$_bank = json_decode($list['bank'], true);

	?>
	<tr align="center" id="trid_<?=$list['idx']?>" class="<?=$_tr_class?>">
		<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list['idx']?>" class="checkSelect"></td>	
		<td class="list-idx"><?=$list['idx']?></td>
		<td class=""><?=$_state_text[$list['state']]?></td>
		<td class=""><?=$_bank['bank']?></td>
		<td class=""><?=date ("Y-m-d", strtotime($list['date']))?></td>
		<td class=""><?=$list['name']?></td>
		<td class="text-right"><? if( $list['in_money'] > 0 ){ ?><span style="color:#167aff;"><?=number_format($list['in_money'])?></span><? } ?></td>
		<td class="text-right"><? if( $list['out_money'] > 0 ){ ?><span style="color:#ff1212;"><?=number_format($list['out_money'])?></span><? } ?></td>
		<td class=""><?=$list['lc_name']?></td>
		<td class=""><?=$list['memo']?></td>
		<td class=""><button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="ledge.bankStatementInfo('<?=$list['idx']?>');" />관리</td>
	<tr>
	<? } ?>
</table>

<div id="hidden_pageing_ajax_data" style="display:none;"><?=$view_page?></div>
<script type="text/javascript"> 
pageingAjaxShow();

$('#search_reset').click(function(){
	$("#search_value").val("");
	orderSheetMain.list();
});
</script> 