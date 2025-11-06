<?

	if( $_s_text ){

		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		if (preg_match("/[a-zA-Z]/", $_s_text)){
			$_where .= " ( 
				INSTR(LOWER(CD_NAME), LOWER('".$_s_text."')) or 
				INSTR(replace(CD_NAME,' ',''), LOWER('".$_s_text."')) or 
				INSTR(LOWER(CD_NAME_OG), LOWER('".$_s_text."')) or 
				INSTR(LOWER(CD_SEARCH_TERM), LOWER('".$_s_text."')) or 
				INSTR(cd_code_fn, '".$_s_text."')
				) ";
		}else{
			$_where .= " ( 
				INSTR(CD_NAME, '".$_s_text."') or 
				INSTR(replace(CD_NAME,' ',''), '".$_s_text."') or 
				INSTR(CD_NAME_OG, '".$_s_text."') or 
				INSTR(CD_SEARCH_TERM, '".$_s_text."') or 
				INSTR(cd_code_fn, '".$_s_text."')
			) ";
		}

	}



	if( $_pn == "" ) $_pn = 1;

	$total_count = sql_counter("day_end", $_where);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "brandLlist.list", "");

	if( $_sort_kind == "stock" ){
		$_sort = " D.ps_stock DESC ";
	}elseif( $_sort_kind == "stock_asc" ){
		$_sort = " D.ps_stock ASC ";
	}elseif( $_sort_kind == "idx" ){
		$_sort = " A.idx DESC ";
	}elseif( $_sort_kind == "rack_code" ){
		$_sort = " D.ps_rack_code ASC ";
	}elseif( $_sort_kind == "soldout" ){
		$_sort = " ( CASE WHEN D.ps_stock < 1  THEN 0 WHEN D.ps_stock > 0  THEN 1 END ), D.ps_soldout_date DESC ";

	}

	$_sort = " idx DESC ";

	$_limit = " limit ".$from_record.", ".$list_num;
	if( $_open_mode == "popup" ){
		$_limit = "";
	}

	$_query = "select * from day_end
		".$_where." ORDER BY ".$_sort." ".$_limit;
	$_result = sql_query_error($_query);

?>
<style type="text/css">
.no-image{ display:inline-block; width:50px; height:50px; line-height:120%; box-sizing:border-box; border:1px solid #ddd; background-color:#eee; 
	color:#999; font-size:11px; padding-top:10px;
}
.table-style tr.tr-no-stock td{ background-color:#eee !important; }
.table-style tr.tr-normal td{ background-color:#fff !important; }
</style>

<? if( $_s_text ){ ?>
	<div class="search-title">	
		검색어 ( <b style='color:red;'><?=$_s_text?></b> ) 검색결과 : <b><?=$total_count?></b>건 검색되었습니다.
		<button type="button" class="search-reset-btn btn btnstyle1 btnstyle1-inverse btnstyle1-sm" id="search_reset">검색 초기화</button>
	</div>
<? }else{ ?>
	<div class="total">Total : <span><b><?=number_format($total_count)?></b></span> &nbsp; | &nbsp;  <span><b><?=$_pn?></b></span> / <?=$total_page?> page</div>
<? } ?>

<table class="table-style m-t-5">	
	<tr class="list">
		<th class="list-idx">IDX</th>
		<th class="">날짜</th>
		<th class="">원가책정</th>
		<th class="">원가미책정</th>
		<th class="">원가합계</th>
		<th class="">판매가합계</th>
		<th class="">등록인</th>
		<th class="">등록일</th>
	</tr>
	<?

	$_total_sum1 = 0;
	$_total_sum2 = 0;

	while($list = sql_fetch_array($_result)){

		$_reg = json_decode($list['reg'], true);

	?>
	<tr align="center" id="trid_<?=$list['idx']?>" class="<?=$_tr_class?>">
		<td class="list-idx"><?=$list['idx']?></td>
		<td class="text-center"><B><?=$list['day_code']?></B></td>
		<td class="text-center"><?=number_format($list['count1'])?></td>
		<td class="text-center"><?=number_format($list['count2'])?></td>
		<td class="text-right"><?=number_format($list['cost_price'])?></td>
		<td class="text-right"><?=number_format($list['sale_price'])?></td>
		<td class="text-center"><?=$_reg['name']?></td>
		<td class="text-center"><?=date('y.m.d H:i',strtotime($_reg['date']))?></td>
	<tr>
	<? } ?>

</table>

<div id="hidden_pageing_ajax_data" style="display:none;"><?=$view_page?></div>
<script type="text/javascript"> 
pageingAjaxShow();

$('#search_reset').click(function(){
	$("#s_text").val("");
	prdMain.list();
});
</script>