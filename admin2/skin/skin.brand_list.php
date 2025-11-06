<?

	if( $_s_text ){

		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		if (preg_match("/[a-zA-Z]/", $_s_text)){
			$_where .= " ( 
				INSTR(LOWER(BD_NAME), LOWER('".$_s_text."')) or 
				INSTR(replace(BD_NAME,' ',''), LOWER('".$_s_text."')) or 
				INSTR(LOWER(BD_NAME_EN), LOWER('".$_s_text."'))
				) ";
		}else{
			$_where .= " ( 
				INSTR(BD_NAME, '".$_s_text."') or 
				INSTR(replace(BD_NAME,' ',''), '".$_s_text."') or 
				INSTR(BD_NAME_EN, '".$_s_text."') 
			) ";
		}

	}



	if( $_pn == "" ) $_pn = 1;

	$total_count = sql_counter(_DB_BRAND, $_where);
	
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
		$_sort = " A.CD_IDX DESC ";
	}elseif( $_sort_kind == "rack_code" ){
		$_sort = " D.ps_rack_code ASC ";
	}elseif( $_sort_kind == "soldout" ){
		$_sort = " ( CASE WHEN D.ps_stock < 1  THEN 0 WHEN D.ps_stock > 0  THEN 1 END ), D.ps_soldout_date DESC ";

	}

	$_sort = " BD_NAME asc ";

	$_limit = " limit ".$from_record.", ".$list_num;
	if( $_open_mode == "popup" ){
		$_limit = "";
	}

	$_query = "select * from "._DB_BRAND."
		".$_where." ORDER BY ".$_sort." ".$_limit;
	$_result = sql_query_error($_query);

	$_national_text['jp'] = "일본";
	$_national_text['cn'] = "중국";
	$_national_text['kr'] = "한국";


	if( $_open_mode == "popup" ){
		include($docRoot."/admin2/layout/header_popup.php");
	}
?>
<style type="text/css">
.no-image{ display:inline-block; width:50px; height:50px; line-height:120%; box-sizing:border-box; border:1px solid #ddd; background-color:#eee; 
	color:#999; font-size:11px; padding-top:10px;
}
.table-style tr.tr-no-stock td{ background-color:#eee !important; }
.table-style tr.tr-normal td{ background-color:#fff !important; }

<? if( $_open_mode == "popup" ){ ?>
.table-style tr td{ word-break:break-all; }
<? } ?>

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
		
		<? if( $_open_mode != "popup" ){ ?>
		<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
		<? } ?>

		<th class="list-idx">IDX</th>
		<th class="" style="width:60px;">이미지</th>
		<th class="">활성</th>
		<th class="">이름(국문)</th>
		<th class="">이름(영문)</th>
		<th class="">노출</th>
		<th class="">상품수</th>
		<th class="">보유상품</th>
		<th class="">상세내용</th>
		<th class="">삭제</th>
	</tr>
	<?

	$_total_sum1 = 0;
	$_total_sum2 = 0;

	while($list = sql_fetch_array($_result)){

		if( $list['BD_LOGO'] ){
			$img_path = '/data/brand_logo/'.$list['BD_LOGO'];
		}

		$_showdang_active_text = "";
		if( $list['bd_showdang_active'] == "N" ){
			$_tr_class = "tr-no-stock";
		}elseif( $list['bd_showdang_active'] == "Y" ){
			$_tr_class = "tr-normal";
			$_showdang_active_text = '<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="koegAd.brandModify(\''.$list['BD_IDX'].'\')" >쑈당몰 노출 </button>';
		}

		$_list_active_text = "";
		if( $list['BD_LIST_ACTIVE'] == "N" ){
			$_list_active_text = "검색 제외";
		}

	
		$query_count = " select 
			count( A.CD_IDX ) AS prdcount,
			COUNT( D.ps_idx ) as stock_count,
			COUNT( CASE WHEN D.ps_stock > 0  THEN 0 END ) as have_stock_count
			from "._DB_COMPARISON." A
			left join prd_stock D ON (D.ps_prd_idx = A.CD_IDX  ) where CD_BRAND_IDX = '".$list['BD_IDX']."' ";

		$result_count = mysqli_query($connect, $query_count);
		$_prd_brand_count = sql_fetch_array($result_count);


	?>
	<tr align="center" id="trid_<?=$list['BD_IDX']?>" class="<?=$_tr_class?>">
		
		<? if( $_open_mode != "popup" ){ ?>
		<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list['CD_IDX']?>" ></td>
		<? } ?>

		<td class="list-idx"><?=$list['BD_IDX']?></td>
		<td >
			<? if( $list['BD_LOGO'] ){ ?>
				<img src="<?=$img_path?>" style="height:50px; border:1px solid #eee !important;">
			<? }else{ ?>
				<div class="no-image 50">No<br>image</div>
			<? } ?>
		</td>
		<td class="">

		</td>
		<td class="text-left"><B><?=$list['BD_NAME']?></B></td>
		<td class="text-left"><?=$list['BD_NAME_EN']?></td>
		<td class="">
			<div>

			<? if( $list['bd_showdang_active'] == "Y" ){ ?>
				<ul><?=$_showdang_active_text?></ul>
			<? } ?>

			<? if( $list['bd_onadb_active'] == "Y" ){ ?>
				<ul>오나DB 노출</ul>
			<? } ?>

			<? if( $list['BD_LIST_ACTIVE'] == "N" ){ ?>
				<ul>검색 제외</ul>
			<? } ?>


			</div>
		</td>
		<td class="">
			<a href="/ad/prd/prd_db/brand_idx=<?=$list['BD_IDX']?>:"><? if( $_prd_brand_count['prdcount'] > 0 )  echo $_prd_brand_count['prdcount']; ?></a>
		</td>
		<td class="">
			<? if( $_prd_brand_count['stock_count'] > 0 ){ ?>
				<a href="/ad/prd/prd_main/brand_idx=<?=$list['BD_IDX']?>:">
				<?=$_prd_brand_count['stock_count']?> / 
				<b><?=$_prd_brand_count['have_stock_count']?></b>
				</a>
			<? } ?>
		</td>
		<td>
			<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="brandLlist.view('<?=$list['BD_IDX']?>')" > 상세내용 </button>
		</td>
		<td>
			<? if( $list['bd_showdang_active'] == "N" && $_prd_brand_count['prdcount'] == 0 ){ ?>
			<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="brandLlist.del('<?=$list['BD_IDX']?>')" ><i class="fas fa-trash-alt"></i></button>
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