<?

// 변수 초기화
$_where = "";
$_pn = $_pn ?? 1;
$_open_mode = $_open_mode ?? "";
$_s_text = $_s_text ?? "";

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
		from "._DB_COMPARISON." A
		inner join prd_stock D ON (D.ps_prd_idx = A.CD_IDX  ) ".$_where;

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

	if( $_sort_kind == "stock" ){
		$_sort = " D.ps_stock DESC ";
	}elseif( $_sort_kind == "stock_asc" ){
		$_sort = " D.ps_stock ASC ";
	}elseif( $_sort_kind == "idx" ){
		$_sort = " A.CD_IDX DESC ";
	}elseif( $_sort_kind == "rack_code" ){
		$_sort = " D.ps_rack_code ASC ";

	//품절일 최근순
	}elseif( $_sort_kind == "soldout" ){
		$_sort = " ( CASE WHEN D.ps_stock < 1  THEN 0 WHEN D.ps_stock > 0  THEN 1 END ), D.ps_soldout_date DESC ";
	
	//품절일 오랜순
	}elseif( $_sort_kind == "soldout_asc" ){

		$_sort = " ( CASE WHEN D.ps_stock < 1  THEN 0 WHEN D.ps_stock > 0  THEN 1 END ),  
			( CASE WHEN  D.ps_soldout_date = '0000-00-00 00:00:00' THEN 9999999999999 ELSE D.ps_soldout_date END ) ASC ";

	//판매가 높은순
	}elseif( $_sort_kind == "price_desc" ){
		$_sort = " A.cd_sale_price DESC ";

	//판매가 낮은순
	}elseif( $_sort_kind == "price_asc" ){
		$_sort = " ( CASE WHEN  A.cd_sale_price = 0 THEN 9999999999999 ELSE A.cd_sale_price END ) ASC ";


	//마진율 높은순
	}elseif( $_sort_kind == "margin" ){
		
		/*
		$_as_colum .= "SUM( cd_sale_price ) as margin_per,";
		*/

		$_as_colum .= "( CASE ";
		$_as_colum .= " WHEN A.cd_sale_price > 29999 THEN ( ( (A.cd_sale_price - A.cd_cost_price) + 2500) / A.cd_sale_price ) * 100 ";
		$_as_colum .= " WHEN A.cd_sale_price < 29999 THEN ( (A.cd_sale_price - A.cd_cost_price) / A.cd_sale_price ) * 100 ";
		$_as_colum .= " END ) as margin_per,";

		$_sort = " ( CASE WHEN A.cd_sale_price > 0 AND A.cd_cost_price > 0 THEN 0 ELSE 1 END ), margin_per DESC ";

	//출시일 최근순
	}elseif( $_sort_kind == "release_date" ){
		$_sort = " ( CASE WHEN D.ps_stock > 0 THEN 0 ELSE 1 END ),  A.CD_RELEASE_DATE DESC ";
	
	//출시일 오랜순
	}elseif( $_sort_kind == "old_release_date" ){
		//$_sort = " ( CASE WHEN D.ps_stock > 0 THEN 0 ELSE 1 END ),  A.CD_RELEASE_DATE ASC ";
		$_sort = " ( CASE WHEN D.ps_stock > 0 THEN 0 ELSE 1 END ),  
			( CASE WHEN A.CD_RELEASE_DATE  = 0 THEN 9999999999999 ELSE A.CD_RELEASE_DATE END ) ASC ";


	//판매일 오랜순
	}elseif( $_sort_kind == "old_sale_date" ){
		$_sort = " ( CASE WHEN D.ps_stock > 0 THEN 0 ELSE 1 END ), D.ps_last_date ASC, D.ps_in_date ASC, D.ps_sale_date ASC";

	//할인일 최근순
	}elseif( $_sort_kind == "new_dis_date" ){
		$_sort = " D.ps_sale_date DESC, D.ps_stock DESC ";

	//할인일 오랜순
	}elseif( $_sort_kind == "old_dis_date" ){
		$_sort = " D.ps_sale_date ASC, D.ps_stock DESC ";
	}



	$_limit = " limit ".$from_record.", ".$list_num;
	if( $_open_mode == "popup" ){
		$_limit = "";
	}

	$_query = "select 
		".$_as_colum."
		A.*,
		B.BD_NAME as brand_name1,
		C.BD_NAME as brand_name2,
		D.*
		from "._DB_COMPARISON." A
		left join "._DB_BRAND." B ON (B.BD_IDX = A.CD_BRAND_IDX ) 
		left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND2_IDX  ) 
		inner join prd_stock D ON (D.ps_prd_idx = A.CD_IDX  ) 
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
		
		<? if( $_open_mode != "popup" ){ ?>
		<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
		<? } ?>

		<th class="list-idx">재고코드</th>
		<th class="" style="width:80px;">이미지</th>
		<th class="">분류</th>
		<th class="" style="width:300px; max-width:300px;">이름</th>
		<th class="">브랜드</th>
		<th class="">코드</th>
		<th class="">재고</th>
		<th class="">랙코드</th>
		<th class="">수입국</th>
		<th class="">
			판매가<br>
			책정원가
		</th>
		<th class="">
			품절일<br>
			최근 판매일<br>
			최근 입고일
		</th>
		<th class="">
			최근 할인일
		</th>
		<th class="">비고</th>
	</tr>
	<?

	$_total_sum1 = 0;
	$_total_sum2 = 0;

	while($list = sql_fetch_array($_result)){

		$img_path = "";
		if( $list['CD_IMG'] ){
			$img_path = '/data/comparion/'.$list['CD_IMG'];
		}

		if( $list['ps_stock'] == 0 ){
			$_tr_class = "tr-no-stock";
		}else{
			$_tr_class = "tr-normal";
		}

		$_cd_weight_data = json_decode($list['cd_weight_fn'], true);
		if (!is_array($_cd_weight_data)) {
			$_cd_weight_data = [];
		}
		$_cd_weight_1 = $_cd_weight_data['1'] ?? null;
		$_cd_weight_2 = $_cd_weight_data['2'] ?? null;
		$_cd_weight_3 = $_cd_weight_data['3'] ?? null;

		$_weight = "";
		if( !$_cd_weight_2 && !$_cd_weight_3 ){
			$_weight = "<span>무게없음</span>";
		}elseif( $_cd_weight_3 ){
			$_weight = $_cd_weight_3."g";
		}elseif( $_cd_weight_2 && !$_cd_weight_3 ){
			$_weight = "<span style='color:#f95477;'><b>".$_cd_weight_2."</b>g</span>";
		}

		$_cd_code_data = json_decode($list['cd_code_fn'], true);
		if (!is_array($_cd_code_data)) {
			$_cd_code_data = [];
		}

		$_name = $list['CD_NAME'];
		$_jancode = $_cd_code_data['jan'] ?? '';

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
		$ps_sale_log_data = json_decode($list['ps_sale_log'], true);
		if (!is_array($ps_sale_log_data)) {
			$ps_sale_log_data = [];
		}
		$_sale_count = count($ps_sale_log_data);

	?>
	<tr align="center" id="trid_<?=$list['CD_IDX']?>" class="<?=$_tr_class?>">
		
		<? if( $_open_mode != "popup" ){ ?>
		<td class="list-checkbox"><input type="checkbox" name="key_check[]" class="checkSelect" value="<?=$list['CD_IDX']?>" ></td>
		<? } ?>

		<td class="list-idx">
			<div>
				<ul><span style="font-size:12px;"><?=$list['CD_IDX']?></span></ul>
				<ul>( <b style='color:#0093e9; font-size:14px;'><?=$list['ps_idx']?></b> )</ul>
			</div>
		</td>
		<td >
			<? if( $list['CD_IMG'] ){ ?>
				<img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;">
			<? }else{ ?>
				<div class="no-image">No image</div>
			<? } ?>
		</td>
		<td class="">
			<?=$koedge_prd_kind_name[$list['CD_KIND_CODE']] ?? ''?>
		</td>
		<td class="text-left">
			<div>
				<? if( $list['CD_RELEASE_DATE'] > 0 ){ ?><ul class="m-b-5 f-s-11" style="color:#777">출시일 : <?=$list['CD_RELEASE_DATE']?></ul><? } ?>
				<ul><b onclick="onlyAD.prdView('<?=$list['CD_IDX']?>','info');" style="cursor:pointer;" ><?=$_name?></b></ul>
				<? if($list['CD_NAME_OG']){ ?><ul class="m-t-5"><?=$list['CD_NAME_OG']?></ul><? } ?>
				<? if( $list['cd_memo2'] ){ ?><ul class="m-t-3" style="word-break:break-all"><span class="prd-memo"><i class="fas fa-feather-alt"></i> <?=$list['cd_memo2']?></span></ul><? } ?>
				<? if( $_weight ){ ?><ul class="m-t-4">무게 : <?=$_weight?></ul><? } ?>
				<ul ><?=in_sale_icon($list['ps_in_sale_s'], $list['ps_in_sale_e'], $list['ps_in_sale_data'])?></ul>
			</div>
		</td>
		<td class="">
			<div>
				<ul><a href="/ad/prd/prd_main/brand_idx=<?=$list['CD_BRAND_IDX']?>:"><?=$list['brand_name1']?></a></ul>
				<? if($list['brand_name2']){ ?><ul class="m-t-5"><a href="/ad/prd/prd_main/brand_idx=<?=$list['CD_BRAND2_IDX']?>:"><?=$list['brand_name2']?></a></ul><? } ?>
			</div>
		</td>
		<td class="">
			<?=$_jancode?>
		</td>
		<td class="">

			<? if( $list['ps_stock'] == 0 ){ ?>
				<span style="color:#ff0000;">재고없음</span>
			<? }else{ ?>
				<b style="font-size:15px; color:#5e41ff;"><?=number_format($list['ps_stock'])?></b>
			<? } ?>

			<? if( $list['ps_stock_hold'] > 0 ){ ?>
				<br><b style="font-size:14px; color:#999;"><?=number_format($list['ps_stock_hold'])?></b>
			<? } ?>

		</td>
		<td class="">
			<?=$list['ps_rack_code']?>
		</td>
		<td class="">
			<?=$_national_text[$list['cd_national']]?>
		</td>
		<td class="text-right">
			<div>
				<ul><b><?=number_format($list['cd_sale_price'])?></b></ul>
				<ul><?=number_format($list['cd_cost_price'])?></ul>

				<?
				if( $list['cd_sale_price'] > 0 && $list['cd_cost_price'] > 0 ){
					if( $_sort_kind == "margin" ){
						$_margin_per = round($list['margin_per'],2);
					}else{ 
						if( $list['cd_sale_price'] < 29999 ){
							$_margin_per =  round( ($list['cd_sale_price'] - $list['cd_cost_price'] ) / $list['cd_sale_price'] * 100, 2);
						}else{
							$_margin_per =  round( ($list['cd_sale_price'] - ($list['cd_cost_price'] + 2500) ) / $list['cd_sale_price'] * 100, 2);
						}
					}
				?>
				<ul class="m-t-3" style="font-size:12px;"><?=$_margin_per?>%</ul>
				<? } ?>
			</div>
		</td>
		<td class="text-center">
			<div>

				<? if( $list['ps_stock'] < 1 ){ ?>
				<ul class="m-b-3"><? if( $list['ps_soldout_date'] > 0 ){ ?><b style="color:#ff0000;"><?=date("y.m.d", strtotime($list['ps_soldout_date']))?></b><? } ?></ul>
				<? } ?>

				<ul class="m-b-3"><? if( $list['ps_last_date'] == 0 ){ ?>판매일 없음<?}else{ ?><?=date("y.m.d", strtotime($list['ps_last_date']))?><? } ?></ul>
				<ul ><? if( $list['ps_in_date'] > 0 ){ ?><?=date("y.m.d", strtotime($list['ps_in_date']))?><? } ?></ul>

			</div>
		</td>
		<td class="text-center">
			<div>
			<? if( $list['ps_sale_date'] > 0 ){ ?><ul><?=$list['ps_sale_date']?></ul><? } ?>
			<? if( $_sale_count && isset($ps_sale_log_data[0]) ){ ?>
				<ul class="text-center" style="font-size:12px;">할인수 : <?=$_sale_count?></ul>
				<ul class="text-center" style="font-size:11px;"><?=$ps_sale_log_data[0]['pg_subject'] ?? ''?></ul>
				<ul class="text-center"><?=$ps_sale_log_data[0]['sale_per'] ?? 0?>%</ul>
			<? } ?>
			</div>
		</td>
		<td class="text-left">
			<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-sm" onclick="footerGlobal.comment('prd','<?=$list['CD_IDX']?>')" >
				댓글
				<? if( $list['comment_count'] > 0 ) { ?> : <b><?=$list['comment_count']?></b><? } ?>
			</button>
		</td>
	<tr>

		<?
			$_total_sum1 += ($list['cd_sale_price']*$list['ps_stock']);
			$_total_sum2 += ($list['cd_cost_price']*$list['ps_stock']);
		?>

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