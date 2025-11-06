<?
$pageGroup = "product2";
$pageName = "prd2_set_list";

include "../lib/inc_common.php";

	$_pn = securityVal($pn);

	$_s_active = securityVal($s_active);
	$_s_text = securityVal($s_text);
	$_s_brand = securityVal($s_brand);
	$_s_kind_code = securityVal($s_kind_code);

	$_sort_kind = securityVal($sort_kind);
	if( !$_sort_kind ) $_sort_kind = "idx";

	$search_sql = " where CD_IDX > 0 ";

	//검색이 있을경우
	//if( $_s_active == "on" AND $_s_text != "" ){
	if( $_s_active == "on"  ){

/*
		if (preg_match("/[a-zA-Z]/", $_s_text)){
			$search_sql .= " and INSTR(LOWER(CD_NAME), LOWER('".$_s_text."')) ";
		}else{
			$search_sql .= " and INSTR(LOWER(CD_NAME), '".$_s_text."') ";
		}
*/
		if (preg_match("/[a-zA-Z]/", $_s_text)){
			$search_sql .= " and ( ";
			$search_sql .= " INSTR(LOWER(CD_NAME), LOWER('".$_s_text."')) ";
			$search_sql .= " or INSTR(replace(CD_NAME,' ',''), LOWER('".$_s_text."')) ";
			$search_sql .= " or INSTR(LOWER(CD_SEARCH_TERM), LOWER('".$_s_text."')) ";
			$search_sql .= " or INSTR(LOWER(CD_NAME_OG), LOWER('".$_s_text."')) ";
			//$search_sql .= " or STOCK.ps_idx = '".$_s_text."' ";
			$search_sql .= " ) ";
		}else{
			//$search_sql .= " and (INSTR(LOWER(CD_NAME), '".$_s_text."') or INSTR(LOWER(CD_SEARCH_TERM),  '".$_s_text."') or INSTR(LOWER(CD_NAME_OG), '".$_s_text."') )";
			$search_sql .= " and ( ";
			$search_sql .= " INSTR(CD_NAME, '".$_s_text."') ";
			$search_sql .= " or INSTR(replace(CD_NAME,' ',''), '".$_s_text."') ";
			$search_sql .= " or INSTR(CD_SEARCH_TERM, '".$_s_text."') ";
			$search_sql .= " or INSTR(CD_NAME_OG, '".$_s_text."') ";
			//$search_sql .= " or STOCK.ps_idx = '".$_s_text."' ";
			$search_sql .= " ) ";
		}



		if ( $_s_brand ){
			$search_sql .= " and ( CD_BRAND_IDX = '".$_s_brand."' OR CD_BRAND2_IDX = '".$_s_brand."' ) ";
		}

		if( $_s_kind_code){
			$search_sql .= " and CD_KIND_CODE = '".$_s_kind_code."' ";
		}

/*
		if( $_s_kind == "subject_body" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$bo_where .= " AND INSTR(LOWER(BOARD_SUBJECT), LOWER('".$_s_text."')) OR INSTR(LOWER(BOARD_BODY), LOWER('".$_s_text."')) ";
			}else{
				$bo_where .= " AND INSTR(LOWER(BOARD_SUBJECT), '".$_s_text."') OR INSTR(LOWER(BOARD_BODY), LOWER('".$_s_text."')) ";
			}
		}elseif( $_s_kind == "subject" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$bo_where .= " AND INSTR(LOWER(BOARD_SUBJECT), LOWER('".$_s_text."')) ";
			}else{
				$bo_where .= " AND INSTR(LOWER(BOARD_SUBJECT), '".$_s_text."') ";
			}
		}elseif( $_s_kind == "writer_name" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$bo_where .= " AND INSTR(LOWER(BOARD_WITER_NAME), LOWER('".$_s_text."')) ";
			}else{
				$bo_where .= " AND INSTR(LOWER(BOARD_WITER_NAME), '".$_s_text."') ";
			}
		}elseif( $_s_kind == "product_name" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$bo_where .= " AND INSTR(LOWER(BOARD_PD_NAME), LOWER('".$_s_text."')) ";
			}else{
				$bo_where .= " AND INSTR(LOWER(BOARD_PD_NAME), '".$_s_text."') ";
			}
		}
*/
	}


	$total_count = wepix_counter(_DB_COMPARISON, $search_sql);
	
	$list_num = 60;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	if( $_sort_kind == "hit" ) {
		$sort_query = "CD_HIT desc";
	}elseif( $_sort_kind == "stock" ) {
		$sort_query = "CD_HIT desc";
	}elseif( $_sort_kind == "release_date" ) {
		$sort_query = "CD_RELEASE_DATE desc";
	}else{
		$sort_query = "CD_IDX desc";
	}

	$_join_sql = " PRD left join prd_stock STOCK ON (PRD.CD_IDX = STOCK.ps_prd_idx) ";

	$query_field = "A.CD_IDX, A.CD_SALE_STATE, A.CD_COMPARISON, A.CD_IMG, A.CD_KIND_CODE, A.CD_RELEASE_DATE, A.CD_NAME, A.CD_NAME_OG,
		A.CD_CODE, A.CD_CODE2, A.CD_MEMO,";
	$query_field .= "B.ps_idx,";
	$query_field .= "C.BD_NAME AS bd_name1,";
	$query_field .= "D.BD_NAME AS bd_name2";

	$query = "select ".$query_field."
		from "._DB_COMPARISON." A 
		left join prd_stock B ON (A.CD_IDX = B.ps_prd_idx)
		left join "._DB_BRAND." C ON (A.CD_BRAND_IDX = C.BD_IDX) 
		left join "._DB_BRAND." D ON (A.CD_BRAND2_IDX = D.BD_IDX)
		".$search_sql;

/*
	$query = "select * from "._DB_COMPARISON." ".$_join_sql." ".$search_sql." order by ".$sort_query." limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);
*/

	$result = wepix_query_error($query." order by ".$sort_query." limit ".$from_record.", ".$list_num);

	$page_link_text = _A_PATH_COMPARISON_LIST."?s_active=".$_s_active."&s_text=".$_s_text."&s_brand=".$_s_brand."&sort_kind=".$_sort_kind."&pn=";
	$_view_paging = publicPaging($pn, $total_page, $list_num, $page_num, $page_link_text);
	
include "../layout/header.php";

?>
<STYLE TYPE="text/css">
img{ 
/*
image-rendering: pixelated; 
*/
image-rendering: -webkit-optimize-contrast;
}
.ag-kind{ width:50px !important; }
.ag-name{ width:200px !important; }
.ag-sub-count{ width:40px !important; }
.ag-sub-view{ width:50px !important; }

.margin-box{ border:1px solid #ccc !important; background-color:#f7f7f7;  padding:5px; margin-bottom:5px; border-radius: 5px; box-sizing:border-box; position:relative;  }
.margin-box-wrap{ border:1px solid #ccc !important; background-color:#f7f7f7;  padding:5px; margin:2px 0 2px; border-radius: 5px; box-sizing:border-box; position:relative; text-align:left;  }
.margin-box-calculation{ position:absolute; top:-40px; left:-405px; width:400px; border:1px solid #000 !important; z-index:9999999; background-color:#222; padding:10px; border-radius: 7px; color:#ddd; line-height:140%; display:none; }

.margin-box-wrap-new{ position:relative;  } 
.margin-box-calculation-new{ position:absolute; top:-40px; left:-505px; width:500px; text-align:left; border:1px solid #000 !important; z-index:9999999; background-color:#222; padding:10px; border-radius: 7px;  line-height:140%; display:none; }
.margin-box-calculation-new div{ color:#ddd !important;  } 
.margin-box-calculation-new div input{ color:#000 !important;  } 
.show_cost_result{}
.show_cost_result ul{ padding:3px;}
.cost-p{ color:#ffce0a; font-size:13px; }
.cost-p2{ color:#b8b28b; font-size:13px; }
.o-p{ color:#48efb6; font-size:13px; }
.show_cost{ margin-top:8px; }
.sc-title{ font-weight:bold; font-size:13px; color:#eee; }
</STYLE>


<script type='text/javascript'>
	var _yen = "<?=$yen?>";
	var _kg_p = "<?=$kg_p?>";
</script>

<div id="contents_head">
	<h1>상품 목록</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_COMPARISON_REG?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">
					<table class="table-list">
						<tr>
							<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
							<th style="width:45px;">IDX</th>
							<th style="width:55px;">이미지</th>
							<th>이름</th>
							<th>브랜드</th>
							<th>TAG</th>
							<th>최저가격</th>
							<th>쑈당몰 가격</th>
							<th>가격비교</th>
							<th style="width:150px;">공급가</th>
							<th>무게</th>
							<th><a href="?sort_kind=hit">조회</a></th>
							<th>재고</th>
							<th>관리</th>
						</tr>
<?
while($list = wepix_fetch_array($result)){

	$_view_brand_name = $list[bd_name1];
	$_view_brand2_name = $list[bd_name2];

	//$brand_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$list[CD_BRAND_IDX]."' "));
	//$brand2_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$list[CD_BRAND2_IDX]."' "));
	//$stock_data = wepix_fetch_array(wepix_query_error("select ps_stock from prd_stock where ps_prd_idx = '".$list[CD_IDX]."' "));

	$_stock_qty = "";
	if( $list[ps_stock] > 0 ){
		$_stock_qty = $list[ps_stock];
	}

							
							

							if( $list[CD_WEIGHT2] > 0 ) { $_weight = $list[CD_WEIGHT2]; }else{ $_weight = $list[CD_WEIGHT]; }

							$img_path = '../../data/comparion/'.$list[CD_IMG];

							if($list[CD_COMPARISON] == "N" ){
								$_trcolor = "#eee";
							}else{
								$_trcolor = "#fff";
							}
						?>

<tr bgcolor="<?=$_trcolor ?>">
	<td class="tl-check"><input type="checkbox" name="key_check[]" value="<?=$list[CD_IDX]?>" ></td>
	<td>
		<? if( $list[CD_SALE_STATE] == "N" ){ ?><b style='color:red; font-size:13px;'>단종</b><br><? } ?>
		<?=$list[CD_IDX]?><br>
		(<b style='color:red; font-size:14px;'><?=$list[ps_idx]?></b>)
	</td>
	<td>
		<? if($list[CD_COMPARISON] == "N" ){?>비노출<br><?}?>
		<? if( $list[CD_IMG]){?><img src="<?=$img_path?>" style="width:70px; "><? } ?>
		<br><b><?=$koedge_prd_kind_name[$list[CD_KIND_CODE]]?></b>
	</td>
	<td class="text-left" style="max-width:460px;">
		<div>
			<ul style="font-size:11px; margin-bottom:5px;"><?=$list[CD_RELEASE_DATE]?></ul>
			<ul ><b onclick="comparisonQuick('<?=$list[CD_IDX]?>','info');" style="cursor:pointer;"><?=$list[CD_NAME]?></b></ul>
			<? if($list[CD_NAME_OG]){ ?><ul style="margin-top:5px;"><?=$list[CD_NAME_OG]?></ul><? } ?>
			<ul style="margin-top:5px;">
				<?=$list[CD_CODE]?>
				<?if( $list[CD_CODE2] ){ ?>| <?=$list[CD_CODE2]?><? } ?>
			</ul>
			<? if($list[CD_MEMO]){ ?><ul style="margin-top:5px;"><span style='color:red; table-layout:fixed;word-break:break-all'><?=nl2br($list[CD_MEMO])?></ul></span><? } ?>
		</div>
	</td>
							<td>
								<a href="prd2_list.php?s_active=on&s_brand=<?=$list[CD_BRAND_IDX]?>"><?=$_view_brand_name?></a><br>
								<? if( $list[CD_BRAND2_IDX] ){ ?><a href="prd2_list.php?s_active=on&s_brand=<?=$list[CD_BRAND2_IDX]?>"><?=$_view_brand2_name?></a><br><? } ?>
								<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="location.href='<?=_A_PATH_COMPARISON_REG?>?s_brand=<?=$list[CD_BRAND_IDX]?>'"><?=$_view_brand_name?> 상품등록</button>
							</td>
							<td>
								<?
								$_ary_cd_tag = explode("│",$list[CD_HASH_TAG]);
								for($t=0;$t<count($_ary_cd_tag);$t++){
									$_ary2_cd_tag = explode("/",$_ary_cd_tag[$t]);
								?>
									<?=$_ary2_cd_tag[1]?></br>
								<? } ?>
							</td>

							<td><?=number_format($list[CD_PRICE])?></td>
							<td>
								<?=number_format($list[CD_SALE_PRICE])?>
								<? if( $list[CD_SALE_MARGIN_PER] > 0 ){ ?><b style="display:block; font-size:13px; margin-top:6px; color:#006cff;"><?=$list[CD_SALE_MARGIN_PER]?>%</b><? } ?>
							</td>
							<td>
								<span onclick="comparisonQuick('<?=$list[CD_IDX]?>');" style="cursor:pointer;"><?=$list[CD_LINK_COUNT]?></span>
<?
/*
	$total_link_count = wepix_counter(_DB_COMPARISON_LINK, "where CL_CD_IDX = '".$list[CD_IDX]."' ");
	if( $list[CD_LINK_COUNT] != $total_link_count ){
		wepix_query_error("update "._DB_COMPARISON." set CD_LINK_COUNT = '".$total_link_count."'  where CD_IDX = '".$list[CD_IDX]."'");
	}
*/
?>

							</td>
							<td>

<div class="margin-box-wrap-new">
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="calculationViewNew('<?=$list[CD_IDX]?>');"><i class="fas fa-calculator"></i> 원가계산</button>
	<div class="margin-box-calculation-new" id="margin_box_calculation_new_<?=$list[CD_IDX]?>">
		
						<div>
							<b style="color:#0feef1; font-size:13px;"><?=$list[CD_NAME]?></b>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs float-right" onclick="calculationViewDel('<?=$list[CD_IDX]?>');"><i class="fas fa-times"></i></button>
						</div>

						<div id="cost_show_<?=$list[CD_IDX]?>">
						</div>

						<div style="margin-top:10px; padding-top:10px; border-top:1px solid #ddd !important;">
							토이즈하트 : <input type='text' name='cd_supply_price_2' id='cd_supply_price_2_<?=$list[CD_IDX]?>' style='width:70px;' value="<?=number_format($list[CD_SUPPLY_PRICE_2])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">￥ | 
							TIS : <input type='text' name='cd_supply_price_6' id='cd_supply_price_6_<?=$list[CD_IDX]?>' style='width:70px;'  value="<?=number_format($list[CD_SUPPLY_PRICE_6])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">￥ | 
							NPG : <input type='text' name='cd_supply_price_9' id='cd_supply_price_9_<?=$list[CD_IDX]?>' style='width:70px;'  value="<?=number_format($list[CD_SUPPLY_PRICE_9])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">￥
						</div>

						<div style="margin-top:5px;">
							브랜드 A : <input type='text' name='cd_supply_price_7' id='cd_supply_price_7_<?=$list[CD_IDX]?>' style='width:70px;'  value="<?=number_format($list[CD_SUPPLY_PRICE_7])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">￥ |
							브랜드 B : <input type='text' name='cd_supply_price_8' id='cd_supply_price_8_<?=$list[CD_IDX]?>' style='width:70px;' value="<?=number_format($list[CD_SUPPLY_PRICE_8])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">￥ 
						</div>

						<div style="margin-top:5px;">
							NLS : <input type='text' name='cd_supply_price_1' id='cd_supply_price_1_<?=$list[CD_IDX]?>' style='width:70px;'  value="<?=number_format($list[CD_SUPPLY_PRICE_1])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">￥ |
							성원 : <input type='text' name='cd_supply_price_3' id='cd_supply_price_3_<?=$list[CD_IDX]?>' style='width:70px;'  value="<?=number_format($list[CD_SUPPLY_PRICE_3])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">원 |
							에스토이 : <input type='text' name='cd_supply_price_4' id='cd_supply_price_4_<?=$list[CD_IDX]?>' style='width:70px;'  value="<?=number_format($list[CD_SUPPLY_PRICE_4])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">원
						</div>
						<div style="margin-top:5px;">
							기타 : <input type='text' name='cd_supply_price_5' id='cd_supply_price_5_<?=$list[CD_IDX]?>' style='width:70px;'  value="<?=number_format($list[CD_SUPPLY_PRICE_5])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"> 
						</div>
						<div style="margin-top:5px;">
							중량 : <input type='text' name='cd_weight' id='cd_weight_<?=$list[CD_IDX]?>' style='width:70px;' value="<?=number_format($list[CD_WEIGHT])?>" onkeyUP="javascript:is_onlynumeric( this.value, this );">( g ) |
							전체중량 : <input type='text' name='cd_weight2' id='cd_weight2_<?=$list[CD_IDX]?>'  style='width:70px;' value="<?=number_format($list[CD_WEIGHT2])?>" onkeyUP="javascript:is_onlynumeric( this.value, this );">( g )
						</div>

						<div style="margin-top:10px;  padding-bottom:10px;">
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="quickPriceModify('<?=$list[CD_IDX]?>')">가격/중량 수정</button>
						</div>


	</div>
</div>

<? 
	if( $list[CD_SUPPLY_PRICE_2] > 0 ){ 
?>
<div class="margin-box-wrap">
	<b>T.H</b> : <b><?=number_format($list[CD_SUPPLY_PRICE_2])?></b>￥
</div>
<? } ?>

<? 
	if( $list[CD_SUPPLY_PRICE_6] > 0 ){ 
?>
<div class="margin-box-wrap">
	TIS : <b><?=number_format($list[CD_SUPPLY_PRICE_6])?></b>￥
</div>
<? } ?>

<? 
	if( $list[CD_SUPPLY_PRICE_9] > 0 ){ 
?>
<div class="margin-box-wrap">
	NPG : <b><?=number_format($list[CD_SUPPLY_PRICE_9])?></b>￥
</div>
<? } ?>

<? 
	if( $list[CD_SUPPLY_PRICE_7] > 0 ){ 
?>
<div class="margin-box-wrap">
	브랜드 A : <b><?=number_format($list[CD_SUPPLY_PRICE_7])?></b>￥
</div>
<? } ?>

<? 
	if( $list[CD_SUPPLY_PRICE_8] > 0 ){ 
?>
<div class="margin-box-wrap">
	브랜드 B : <b><?=number_format($list[CD_SUPPLY_PRICE_8])?></b>￥
</div>
<? } ?>

<?
if( $list[CD_SUPPLY_PRICE_1] > 0 ){ 
?>
<div class="margin-box-wrap">
	NLS : <b><?=number_format($list[CD_SUPPLY_PRICE_1])?></b>￥
</div>
<? } ?>

								<? if( $list[CD_SUPPLY_PRICE_3] > 0 ){ ?>성원 : <b><?=number_format($list[CD_SUPPLY_PRICE_3])?></b><br><? } ?>
								<? if( $list[CD_SUPPLY_PRICE_4] > 0 ){ ?>에토 : <b><?=number_format($list[CD_SUPPLY_PRICE_4])?></b><br><? } ?>
								<? if( $list[CD_SUPPLY_PRICE_5] > 0 ){ ?>기타1 : <b><?=number_format($list[CD_SUPPLY_PRICE_5])?></b><br><? } ?>

							</td>
							<td><?=number_format($_weight)?>g</td>
							<td><?=number_format($list[CD_HIT])?></td>
							<td><b id="prd_stock_<?=$list[CD_IDX]?>" onclick="comparisonQuick('<?=$list[CD_IDX]?>','stock');" style="cursor:pointer;"><?=$_stock_qty?></b></td>
							<td>

								<!-- <button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="location.href='<?=_A_PATH_COMPARISON_REG?>?mode=modify&key=<?=$list[CD_IDX]?>'">Modify</button> -->

								<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="goDel('<?=$list[CD_IDX]?>');"><i class="far fa-trash-alt"></i></button>
								<br><?=date("y-m-d",$list[CD_UPDATE_DATE])?>
							</td>
						</tr>
						<? } ?>
					</table>
				</div><!-- #list_box2 -->
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
					<form name='search_form' id='search_form'  method='get' action="prd2_list.php">
					<input type="hidden" name="s_active" value="on">
					<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
<!-- 
					<ul class="filter-from-ui m-t-5">
						<select name="search_type">
							<option value="" >타입선택</option>
							<option value="standby">standby</option>
							<option value="approval">approval</option>
							<option value="cancel" >cancel</option>
						</select>
					</ul>

					<ul class="filter-from-ui m-t-5">
						<input type="text" id="s_day" name="s_day" value="<?=$s_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="시작일" readonly /> ~
						<input type="text" id="e_day" name="e_day" value="<?=$e_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="종료일" readonly />
					</ul>
 -->
					<ul class="filter-from-ui m-t-5">
						<input type='text' name='s_text' id='s_text' size='20' value="<?=$_s_text ?>" placeholder="상품이름">
					</ul>

					<ul class="filter-from-ui m-t-5">
						<select name="s_brand" class="selectpicker">
							<option value="">전체 브랜드</option>
<?
	$brand_result = wepix_query_error("select BD_IDX,BD_NAME from "._DB_BRAND." order by BD_NAME asc ");
	while($brand_list = wepix_fetch_array($brand_result)){
?>
							<option value="<?=$brand_list[BD_IDX]?>" <? if( $_s_brand == $brand_list[BD_IDX] ) echo "selected";?> ><?=$brand_list[BD_NAME]?></option>
<? } ?>
						</select>
					</ul>

					<ul class="filter-from-ui m-t-5">
						<select name="s_kind_code">
							<option value="">전체 종류</option>
<?
for($t=0; $t<count($koedge_prd_kind_array); $t++){
?>
							<option value="<?=$koedge_prd_kind_array[$t]['code']?>" <? if( $s_kind_code == $koedge_prd_kind_array[$t]['code'] ) echo "selected";?>><?=$koedge_prd_kind_array[$t]['name']?></option>
<?
}
?>
						</select>
					</ul>


					<ul class="filter-from-ui m-t-5">
						SORT : 
						<select name="sort_kind">
							<option value="idx" <? if( $_sort_kind == "idx" ) echo "selected";?> >IDX</option>
							<option value="hit" <? if( $_sort_kind == "hit" ) echo "selected";?>>조회수 많은순</option>
							<option value="stock" <? if( $_sort_kind == "stock" ) echo "selected";?>>재고 많은순</option>
							<option value="release_date" <? if( $_sort_kind == "release_date" ) echo "selected";?>>출시일 최근순</option>
						</select>
					</ul>

					<ul class="filter-from-ui m-t-5">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="goSerch();" > 
							<i class="fas fa-search"></i> 검색
						</button>
					</ul>

					</form>

					<ul class="filter-menu-title" style="margin-top:20px;"><i class="fas fa-calculator"></i> 원가 계산기</ul>
					<ul class="filter-from-ui m-t-5">
						적용환율 <input type='text' name='' id='calculator_ex_yen' style="width:50px" value="1160" placeholder="적용환율">
						1kg 배송비 <input type='text' name='' id='calculator_kg_p' style="width:50px" value="6000" placeholder="1kg 배송비">
					</ul>
					<ul class="filter-from-ui m-t-5">
						상품가(엔) <input type='text' name='' id='calculator_o_p' style="width:150px" value="">
					</ul>
					<ul class="filter-from-ui m-t-5">
						상품무게(g) <input type='text' name='' id='calculator_weight' style="width:150px" value="">
					</ul>
					<ul class="filter-from-ui m-t-5">
						<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="calculatorShow();"><i class="fas fa-calculator"></i> 원가계산하기</button>
					</ul>
					<ul id="calculator_result">
					</ul>

					<ul class="filter-menu-title" style="margin-top:20px;"><i class="fas fa-calculator"></i> 판매가 계산기</ul>
					<ul class="filter-from-ui m-t-5">
						원가 <input type='text' name='' id='ma_o_p' style="width:100px" value="1160" placeholder="적용환율">
						퍼센트 <input type='text' name='' id='ma_per' style="width:50px" value="40" placeholder="1kg 배송비">
					</ul>
				</div>
			</ul>
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>
	</div>
</div>

<script type="text/javascript"> 
<!-- 

function calculatorShow(){

	var o_p = $("#calculator_o_p").val();
	var ex_yen = $("#calculator_ex_yen").val();
	var weight = $("#calculator_weight").val();
	var kg_p = $("#calculator_kg_p").val();

	var op_won = o_p * (ex_yen/100); //원가 원전환
	var delivery_p  = weight * (kg_p * 0.001); // 배송비
	var tariff_p = Math.round(op_won*0.08,0); //관세
	var tariff_vat_p = Math.round((op_won + tariff_p)*0.1,0); //부가세

	var tax_p = tariff_p + tariff_vat_p + delivery_p;
	var cost_p = op_won + tax_p;

	var str = "<div>";
	str += "<ul>원전환 : "+ Comma_int(o_p)+ "￥ -> "+ Comma_int(op_won) + "원</ul>";
	str += "<ul>관세(8%) : "+ Comma_int(tariff_p)+"원 / 부가세 : "+ Comma_int(tariff_vat_p)+"원 </ul>";
	str += "<ul>배송비 : "+Comma_int(delivery_p)+"원 = "+Comma_int(tax_p)+"원</ul>";
	str += "<ul>원가 : <b>"+Comma_int(cost_p)+"</b>원</ul>";
	str += "</div>";

	$("#calculator_result").html(str);
	
	//alert(str);

}
function calculatorShow2(){
	var ma_o_p = $("#ma_o_p").val();
	var ma_per = $("#ma_per").val();

17725
}

function goSerch(){
	$("#search_form").submit();
}

function calculationView( idx, mode, view ){
	if( view == "closed" ){
		$("#margin_box_calculation_"+ mode +"_"+ idx).hide();
	}else{

		$(".margin-box-calculation").each(function(){
			$(this).hide();
		});
		
		$("#margin_box_calculation_"+ mode +"_"+ idx).show();
	}
}




function calculationViewDel( idx ){
	$("#margin_box_calculation_new_"+ idx).hide();
}


function calculationViewNew( idx ){

		$(".margin-box-calculation-new").each(function(){
			$(this).hide();
		});
		
		costShow( idx );

		$("#margin_box_calculation_new_"+ idx).show();

}


function costShow( idx ){
	$.ajax({
		url: "ajax_cost_show.php",
		data: {
			"idx":idx,
			"yen":_yen,
			"kg_p":_kg_p
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$('#cost_show_'+idx).html(getdata);
		},
		error: function(){
		}
	});
}


function quickPriceModify( idx ){

	var cd_supply_price_1 = $("#cd_supply_price_1_"+idx).val();
	var cd_supply_price_2 = $("#cd_supply_price_2_"+idx).val();
	var cd_supply_price_3 = $("#cd_supply_price_3_"+idx).val();
	var cd_supply_price_4 = $("#cd_supply_price_4_"+idx).val();
	var cd_supply_price_5 = $("#cd_supply_price_5_"+idx).val();
	var cd_supply_price_6 = $("#cd_supply_price_6_"+idx).val();
	var cd_supply_price_7 = $("#cd_supply_price_7_"+idx).val();
	var cd_supply_price_8 = $("#cd_supply_price_8_"+idx).val();
	var cd_supply_price_9 = $("#cd_supply_price_9_"+idx).val();
	var cd_weight = $("#cd_weight_"+idx).val();
	var cd_weight2 = $("#cd_weight2_"+idx).val();

		$.ajax({
			type: "post",
			url : "<?=_A_PATH_COMPARISON_OK?>",
			data : { 
				a_mode : "quickPriceModify",
				idx : idx ,
				cd_supply_price_1 : cd_supply_price_1,
				cd_supply_price_2 : cd_supply_price_2,
				cd_supply_price_3 : cd_supply_price_3,
				cd_supply_price_4 : cd_supply_price_4,
				cd_supply_price_5 : cd_supply_price_5,
				cd_supply_price_6 : cd_supply_price_6,
				cd_supply_price_7 : cd_supply_price_7,
				cd_supply_price_8 : cd_supply_price_8,
				cd_supply_price_9 : cd_supply_price_9,
				cd_weight : cd_weight ,
				cd_weight2 : cd_weight2
			},
			success: function(getdata) {
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					costShow( idx );

				}else if(ckcode=="Value_null"){

				}
			}
		});

}

	function goDel(idx){

		var prd_stock = $("#prd_stock_"+idx).html()*1;
		var msg = "";
		
		if( prd_stock > 0 ){
			msg = "재고가 있습니다. 재고 일일 데이터가 있을경우 삭제되지 않습니다.";
		}else{
			msg = "삭제하시겠습니까?";
		}

		if(confirm(msg)){
			$.ajax({
				type: "post",
				url : "<?=_A_PATH_COMPARISON_OK?>",
				data : { 
					a_mode : "comparisonDel",
					idx : idx
				},
				success: function(getdata) {
					makedata = getdata.split('|');
					ckcode = makedata[1];
					ckmsg = makedata[2];

					if(ckcode=="Processing_Complete"){
						alert('삭제완료');
						location.reload();
					}else{
						alert(ckmsg);
						return false;
					}
				}
			});
		}

	}
//--> 
</script> 

<?
include "../layout/footer.php";
exit;
?>

