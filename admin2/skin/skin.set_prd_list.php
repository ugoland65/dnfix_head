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

	if ( $_s_brand == "no" ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " CD_BRAND_IDX = '0' ";
	}elseif ( $_s_brand ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " ( CD_BRAND_IDX = '".$_s_brand."' OR CD_BRAND2_IDX = '".$_s_brand."' ) ";
	}

	if( $_s_kind_code ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " CD_KIND_CODE = '".$_s_kind_code."' ";
	}

	if( $_s_national ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " cd_national = '".$_s_national."' ";
	}

	if( $_s_rack_code_group ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " D.ps_rack_code LIKE '".$_s_rack_code_group."%' ";
	}


	$query_count = " select count(*)
		from prd_set A
		inner join prd_stock D ON (D.ps_prd_idx = A.pset_idx  ) ".$_where;

	$result_count = mysqli_query($connect, $query_count);
	$count_row = mysqli_fetch_row($result_count);
	$total_count = $count_row[0];


	if( $_pn == "" ) $_pn = 1;

	//$total_count = sql_counter(_DB_COMPARISON, $_where);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "prdMain.list", "");

	$_as_colum = "";
	
	$_sort = " pset_idx DESC ";

	$_limit = " limit ".$from_record.", ".$list_num;

	$_query = "select * from prd_set ".$_where." ORDER BY ".$_sort." ".$_limit;
	$_result = sql_query_error($_query);

?>
<style type="text/css">
.no-image{ display:inline-block; width:70px; height:70px; line-height:70px; box-sizing:border-box; border:1px solid #ddd; background-color:#eee; 
	color:#999; font-size:11px;
}
.table-style tr.tr-no-stock td{ background-color:#eee !important; }
.table-style tr.tr-normal td{ background-color:#fff !important; }

<? if( $_open_mode == "popup" ){ ?>
.table-style tr td{ word-break:break-all; }
<? } ?>

.prd-memo{ color:#6b49ff; }
</style>

<!-- <?=$_as_colum?> -->
<!-- <?=$_query?> -->

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
		
		<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
		<th class="list-idx">IDX</th>
		<th class="">세트상품 코드</th>
		<th class="">관리</th>
		<th class="">이름</th>
		<th class="">상품수</th>
		<th class="">재고</th>
	</tr>
	<?

	$_total_sum1 = 0;
	$_total_sum2 = 0;

	while($list = sql_fetch_array($_result)){

		if( $list['CD_IMG'] ){
			$img_path = '/data/comparion/'.$list['CD_IMG'];
		}

		if( $list['ps_stock'] == 0 ){
			$_tr_class = "tr-no-stock";
		}else{
			$_tr_class = "tr-normal";
		}

		$_cd_weight_data = json_decode($list['cd_weight_fn'], true);
		$_cd_weight_1 = $_cd_weight_data['1'];
		$_cd_weight_2 = $_cd_weight_data['2'];
		$_cd_weight_3 = $_cd_weight_data['3'];

		if( !$_cd_weight_2 && !$_cd_weight_3 ){
			$_weight = "<span>무게없음</span>";
		}elseif( $_cd_weight_3 ){
			$_weight = $_cd_weight_3."g";
		}elseif( $_cd_weight_2 && !$_cd_weight_3 ){
			$_weight = "<span style='color:#f95477;'><b>".$_cd_weight_2."</b>g</span>";
		}

		$_cd_code_data = json_decode($list['cd_code_fn'], true);

		$_name = $list['CD_NAME'];
		$_jancode = $_cd_code_data['jan'];

		if( $_s_text ){
			$_name = preg_replace("/".$_s_text."/","<b style='color:red;'>".$_s_text."</b>",$_name);
			$_jancode = preg_replace("/".$_s_text."/","<b style='color:red;'>".$_s_text."</b>",$_jancode);
		}

		/*
		$_sale_count = "";
		if( $_sort_kind == "new_dis_date" || $_sort_kind == "old_dis_date" ){
			$ps_sale_log_data = json_decode($list['ps_sale_log'], true);
			$_sale_count = count($ps_sale_log_data);
		}
		*/
		$ps_sale_log_data = "";
		$ps_sale_log_data = json_decode($list['ps_sale_log'], true);
		$_sale_count = count($ps_sale_log_data);

	?>
	<tr align="center" id="trid_<?=$list['pset_idx']?>" class="<?=$_tr_class?>">
		<td class="list-checkbox"><input type="checkbox" name="key_check[]" class="checkSelect" value="<?=$list['pset_idx']?>" ></td>
		<td class="list-idx"><?=$list['pset_idx']?></td>
		<td>NSET-<?=$list['pset_idx']?></td>
		<td><button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="setPrd.view(this, <?=$list['pset_idx']?>);" >관리</button></td>
		<td class="text-left"><?=$list['pset_name']?></td>
		<td class=""><?=$list['pset_count']?></td>
		<td class="text-right">
			<? if( $list['pset_stock'] > 0 ){ ?>
			<b><?=$list['pset_stock']?></b>
			<? } ?>
		</td>
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