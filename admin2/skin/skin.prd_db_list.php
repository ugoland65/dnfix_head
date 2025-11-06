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


	if ( $_s_brand ){
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
	
	if( $_s_tier ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " cd_tier = '".$_s_tier."' ";
	}

	if( $_pn == "" ) $_pn = 1;

	$total_count = sql_counter(_DB_COMPARISON, $_where);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "prdMain.list", "");

	$_query = "select 
		A.*, 
		B.BD_NAME as brand_name1,
		C.BD_NAME as brand_name2,
		D.ps_idx, D.ps_in_sale_s, D.ps_in_sale_e, D.ps_in_sale_data
		from "._DB_COMPARISON." A
		left join "._DB_BRAND." B ON (B.BD_IDX = A.CD_BRAND_IDX  ) 
		left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND2_IDX  ) 
		left join prd_stock D ON (D.ps_prd_idx = A.CD_IDX  ) 
		".$_where." ORDER BY CD_IDX desc limit ".$from_record.", ".$list_num;
	$_result = sql_query_error($_query);


?>
<style type="text/css">
.no-image{ display:inline-block; width:70px; height:70px; line-height:70px; box-sizing:border-box; border:1px solid #ddd; background-color:#eee; 
	color:#999; font-size:11px;
}

.prd-memo{ color:#6b49ff; }
</style>

<? if( $_s_text ){ ?>
	<div class="search-title">	
		검색어 ( <b style='color:red;'><?=$_s_text?></b> ) 검색결과 : <b><?=$total_count?></b>건 검색되었습니다.
		<button type="button" class="search-reset-btn btn btnstyle1 btnstyle1-inverse btnstyle1-sm" id="search_reset">검색 초기화</button>
	</div>
<? }else{ ?>
	<div class="total">Total : <span><b><?=number_format($total_count)?></b></span> &nbsp; | &nbsp;  <span><b><?=$_pn?></b></span> / <?=$total_page?> page</div>
<? } ?>

<table class="table-style m-t-6">	
	<tr class="list">
		<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
		<th class="list-idx">고유번호</th>
		<th class="" style="width:80px;">이미지</th>
		<th class="" style="width:80px;">아이콘</th>
		<th class="">분류</th>
		<th class="">티어</th>
		<th class="" style="width:300px;">이름</th>
		<th class="">브랜드</th>
		<th class="">등록일</th>
		<th class="">관리</th>
		<th class="">비고</th>
	</tr>
	<?
	while($list = sql_fetch_array($_result)){

		if( $list['CD_IMG'] ){
			$img_path = '/data/comparion/'.$list['CD_IMG'];
		}

		if( $list['CD_IMG2'] ){
			$img_path2 = '/data/comparion/'.$list['CD_IMG2'];
		}

	?>
	<tr align="center" id="trid_<?=$list['CD_IDX']?>" class="<?=$_tr_class?>">
		<td class="list-checkbox"><input type="checkbox" name="key_check[]" class="checkSelect" value="<?=$list['CD_IDX']?>" ></td>	
		<td class="list-idx">
			<div>
				<ul><span style="font-size:12px;"><?=$list['CD_IDX']?></span></ul>
				<? if( $list['ps_idx'] ){ ?><ul>( <b style='color:#0093e9; font-size:14px;'><?=$list['ps_idx']?></b> )</ul><? } ?>
			</div>
		</td>
		<td onclick="onlyAD.prdView('<?=$list['CD_IDX']?>','info');" style="cursor:pointer;" >
			<? if( $list['CD_IMG'] ){ ?>
				<img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;">
			<? }else{ ?>
				<div class="no-image">No image</div>
			<? } ?>
		</td>
		<td >
			<? if( $list['CD_IMG2'] ){ ?>
				<img src="<?=$img_path2?>" style="height:70px; border:1px solid #eee !important;">
			<? }else{ ?>
				<div class="no-image">No image</div>
			<? } ?>
		</td>
		<td class="">
			<?=$koedge_prd_kind_name[$list['CD_KIND_CODE']]?>
		</td>
		<td class="">
			<?=$list['cd_tier']?>
		</td>
		<td class="text-left">
			<div>
				<? if( $list['CD_RELEASE_DATE'] > 0 ){ ?><ul class="m-b-5 f-s-11" style="color:#777">출시일 : <?=$list['CD_RELEASE_DATE']?></ul><? } ?>
				<ul>
					<b onclick="onlyAD.prdView('<?=$list['CD_IDX']?>','info');" style="cursor:pointer;" ><?=$list['CD_NAME']?></b>

					<? 
					/*
						if( $_ad_id == "admin" ){ ?>
					<button type="button" id="" class="btnstyle1 btnstyle1-xs" onclick="onlyAD.prdViewTest('<?=$list['CD_IDX']?>')" >구 상품정보</button>
					<? } 
					*/
					?>

				</ul>
				<? if( $list['CD_NAME_OG'] ){ ?><ul class="m-t-5"><?=$list['CD_NAME_OG']?></ul><? } ?>
				<? if( $list['cd_memo2'] ){ ?><ul class="m-t-3" style="word-break:break-all"><span class="prd-memo"><i class="fas fa-feather-alt"></i> <?=$list['cd_memo2']?></span></ul><? } ?>
				<ul ><?=in_sale_icon($list['ps_in_sale_s'], $list['ps_in_sale_e'], $list['ps_in_sale_data'])?></ul>
			</div>
		</td>
		<td class="">
			<div>
				<ul><a href="/ad/prd/prd_db/brand_idx=<?=$list['CD_BRAND_IDX']?>:"><?=$list['brand_name1']?></a></ul>
				<? if($list['brand_name2']){ ?><ul class="m-t-5"><a href="/ad/prd/prd_db/brand_idx=<?=$list['CD_BRAND2_IDX']?>:"><?=$list['brand_name2']?></a></ul><? } ?>
			</div>
		</td>
		<td class="">
			<span class="f-s-12"><?=date('y.m.d H:i',strtotime($list['cd_reg_time']))?></span>
		</td>
		<td class="">

			<? if( !$list['ps_idx'] ){ ?>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="prdDB.listDel('<?=$list['CD_IDX']?>');"><i class="far fa-trash-alt"></i></button>
			<? } ?>
				<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="prdDB.prdCopy('<?=$list['CD_IDX']?>');" >복사</button>

		</td>
		<td class="text-left">
			<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-sm" onclick="footerGlobal.comment('prd','<?=$list['CD_IDX']?>')" >
				댓글
				<? if( $list['comment_count'] > 0 ) { ?> : <b><?=$list['comment_count']?></b><? } ?>
			</button>
		</td>
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