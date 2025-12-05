<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 변수 초기화
$_s_text = $_s_text ?? "";
$_s_mode = $_s_mode ?? "";
$_where = "";
$_pn = $_pn ?? 1;

	if( $_s_text ){

		if( $_where ){ $_where .= " AND "; }else{ $_where .= " WHERE "; }
		if (preg_match("/[a-zA-Z]/", $_s_text)){
			$_where .= " INSTR(LOWER(pg_subject), LOWER('".$_s_text."')) ";
		}else{
			$_where .= " INSTR(pg_subject, '".$_s_text."') ";
		}

	}

	if( $_s_mode ){
		if( $_where ){ $_where .= " AND "; }else{ $_where .= " WHERE "; }
		$_where .= " pg_mode = '".$_s_mode."' ";
	}

	$total_count = wepix_counter("prd_grouping", $_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "prdGrouping.list", "");

	$_query = "select * from  prd_grouping ".$_where." ORDER BY idx DESC limit ".$from_record.", ".$list_num;
	$_result = sql_query_error($_query);

?>

<? if( !empty($_s_text) ){ ?>
	<div class="search-title">	
		검색어 ( <b style='color:red;'><?=htmlspecialchars($_s_text, ENT_QUOTES, 'UTF-8')?></b> ) 검색결과 : <b><?=number_format($total_count ?? 0)?></b>건 검색되었습니다.
		<button type="button" class="search-reset-btn btn btnstyle1 btnstyle1-inverse btnstyle1-sm" id="search_reset">검색 초기화</button>
	</div>
<? }else{ ?>
	<div class="total">Total : <span><b><?=number_format($total_count ?? 0)?></b></span> &nbsp; | &nbsp;  <span><b><?=$_pn?></b></span> / <?=$total_page ?? 1?> page</div>
<? } ?>

<table class="table-style">	
	<tr class="list">
		<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
		<th class="list-idx">고유번호</th>
		<th class="">모드</th>
		<th class="">제목</th>
		<th class="">상품수</th>
		<th>진행일</th>
		<th>상태</th>
		<th>등록일</th>
		<th>관리</th>
		<th>상품관리</th>
	</tr>
<?
$_mode_text = [
	'sale' => "데이할인",
	'period' => "기간할인",
	'qty' => "수량체크",
	'event' => "기획전"
];

while($_list = sql_fetch_array($_result)){
	
	// 배열 검증
	if (!is_array($_list)) {
		continue;
	}
	
	$_prd_jsondata = json_decode($_list['data'] ?? '[]', true);
	
	// 배열 검증
	if (!is_array($_prd_jsondata)) {
		$_prd_jsondata = [];
	}

	$_reg_data = json_decode($_list['reg'] ?? '{}', true);
	
	// 배열 검증
	if (!is_array($_reg_data)) {
		$_reg_data = [];
	}
	
	$_reg_date = "";
	if (isset($_reg_data['d']['date']) && !empty($_reg_data['d']['date'])) {
		$_reg_date = date("y.m.d H:i", strtotime($_reg_data['d']['date']));
	}

	if( ($_list['pg_mode'] ?? "") == "period" ){
		$_pg_day = ($_list['pg_sday'] ?? "")." ~ ".($_list['pg_day'] ?? "");
	}else{
		$_pg_day = ( ($_list['pg_day'] ?? 0) > 0 ) ? $_list['pg_day'] : "";
	}

?>
<tr align="center" id="trid_<?=$_list['idx'] ?? ''?>">
	<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$_list['idx'] ?? ''?>" ></td>	
	<td class="list-idx"><?=$_list['idx'] ?? ''?></td>
	<td class=""><?=$_mode_text[$_list['pg_mode'] ?? ''] ?? ''?></td>
	<td class="text-left"><?=$_list['pg_subject'] ?? ''?></td>
	<td><?=count($_prd_jsondata)?></td>
	<td><?=$_pg_day?></td>
	<td><?=$_list['pg_state'] ?? ''?></td>
	<td><?=$_reg_date?></td>
	<td>
		<button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/ad/prd/prd_grouping_view/<?=$_list['idx'] ?? ''?>'"> 관리 </button>
	</td>
	<td>
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="prdGrouping.prdView('<?=$_list['idx'] ?? ''?>')"> 상품 </button>
	</td>
<tr>
<? }?>

</table>
<div id="hidden_pageing_ajax_data" style="display:none;"><?=$view_page?></div>
<script type="text/javascript"> 
pageingAjaxShow();

$('#search_reset').click(function(){
	$("#s_text").val("");
	prdGrouping.list();
});
</script> 