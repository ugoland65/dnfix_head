<?
	// 변수 초기화
	$_pn = $_pn ?? 1;

	if( $_oo_import == "all" ){
		$_where = "";
	}elseif( $_oo_import == "수입" ){
		$_where = " WHERE oo_import IN ('수입', '구매대행') ";
	}elseif( $_oo_import == "국내" ){
		$_where = " WHERE oo_import = '국내' ";
	}

	if( $_search_value ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " INSTR( oo_name, '".$_search_value."' ) ";
		$_where .= " OR INSTR( oo_express_data, '".$_search_value."' ) ";
		$_where .= " OR INSTR( oo_tex_data, '".$_search_value."' ) ";
	}
	
	if( $_oo_form_idx ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " oo_form_idx = '".$_oo_form_idx."'  ";
	}

	if( $_pn == "" ) $_pn = 1;

	$total_count = wepix_counter("ona_order", $_where);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "orderSheetMain.list", "");

	$_query = "select 
		A.*, B.oog_name
		from ona_order A
		left join ona_order_group B ON (B.oog_idx = A.oo_form_idx  ) 
		".$_where." ORDER BY oo_sort desc limit ".$from_record.", ".$list_num;

	$_result = sql_query_error($_query);

	$_order_sheet_state_text[1] = "작성중";
	$_order_sheet_state_text[2] = "주문전송";
	$_order_sheet_state_text[4] = "입금완료";
	$_order_sheet_state_text[5] = "입고완료";
	$_order_sheet_state_text[7] = "주문종료";

?>

<style type="text/css">
.search-title{ font-size:16px; padding-bottom:10px; }
.search-title b{ color:#d40202; }
</style>

<? if( $_search_value ){ ?>
<div class="search-title">	
	검색어 ( <b><?=$_search_value?></b> ) 검색결과 : <b><?=$total_count?></b>건 검색되었습니다.
	<button type="button" class="search-reset-btn btn btnstyle1 btnstyle1-inverse btnstyle1-sm" id="search_reset">검색 초기화</button>
</div>
<? } ?>

<div id="" class="table-wrap5">
	<div class=" scroll-wrap">

		<table class="table-st1">
			<thead>
			<tr>
				<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
				<th class="list-idx">고유번호</th>
				<!-- 
				<th class="">ver</th>
				 -->
				<th class="width-80">수입구분</th>
				<th class="width-80">상태</th>
				<th class="">이름</th>
				<th>주문금액</th>
				<th>결제금액</th>
				<th>주문서폼</th>
				<th>관리</th>
				<th>등록일</th>
				<th>메모</th>
			</tr>
			</thead>
			<tbody>
			<?
			while($list = sql_fetch_array($_result)){

				if( $list['oo_import'] == "수입" ){
					$_where = " WHERE oo_import = '수입' ";
					//$_tr_class = "import";
				}else{
					$_where = " WHERE oo_import = '국내' ";
					//$_tr_class = "ko";
				}

				if( $list['oo_state'] == 7 ){
					$_tr_class = "end";
				}

				if( $list['oo_state'] == "2" ){
					$_tr_class = "tr-2";
				}elseif( $list['oo_state'] == "4" ){
					$_tr_class = "tr-4";
				}elseif( $list['oo_state'] == "7" ){
					$_tr_class = "status_end";
				}else{
					$_tr_class = "tr-normal";
				}
				
				$_show_date = "";

				$_reg = json_decode($list['reg'], true);

				if( $_reg['reg']['date'] ){
					$_show_date = date('y.m.d H:i',strtotime($_reg['reg']['date']))."<br>(".$_reg['reg']['name'].")";
				}else{
					if( $list['oo_date'] > 0 ) $_show_date = date("<b>Y.m.d</b> H:i", $list['oo_date']);
				}

				$_oo_price_data = json_decode($list['oo_price_data'], true);
				if (!is_array($_oo_price_data)) {
					$_oo_price_data = [];
				}
			?>
			<tr align="center" id="trid_<?=$list['oo_idx']?>" class="<?=$_tr_class?>">
				<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list['oo_idx']?>" ></td>	
				<td class="list-idx"><?=$list['oo_idx']?></td>
				<!-- 
				<td class=""><?=$list['oo_r_mode']?></td>
				-->
				<td class=""><?=$list['oo_import']?></td>
				<td class=""><?=$_order_sheet_state_text[$list['oo_state']] ?? ''?></td>
				<td class="text-left"><a href="/ad/order/order_sheet/?idx=<?=$list['oo_idx']?>"><b><?=$list['oo_name']?></b></a></td>
				<td class="text-right"><?=number_format($list['oo_sum_price'])?> <?=$_oo_price_data['currency'] ?? ''?></td>
				<td class="text-right"><?=number_format($list['oo_price_kr'])?> 원</td>
				<td class=""><a href="/ad/order/order_sheet_main?form_idx=<?=$list['oo_form_idx']?>"><?=$list['oog_name']?></a></td>
				<td>
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-sm" onclick="orderSheet.osView(this, '<?=$list['oo_idx']?>','main')" >상세내용</button>
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-sm" onclick="location.href='/ad/order/order_sheet/?idx=<?=$list['oo_idx']?>'" >주문내역</button>
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-sm" onclick="orderSheetMainList.payment('<?=$list['oo_idx']?>')" >결제요청</button>
				</td>
				<td class="" style="font-size:12px;"><?=$_show_date?></td>
				<td class="text-left">
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-sm" onclick="footerGlobal.comment('orderSheet','<?=$list['oo_idx']?>')" >
						댓글
						<? if( $list['comment_count'] > 0 ) { ?> : <b><?=$list['comment_count']?></b><? } ?>
					</button>
					<?=$list['oo_memo']?>
				</td>
			</tr>
			<? } ?>
			</tbody>
		</table>

	</div>
</div>

<div id="hidden_pageing_ajax_data" style="display:none;"><?=$view_page?></div>
<script type="text/javascript"> 
pageingAjaxShow();

	$('#search_reset').click(function(){
		$("#search_value").val("");
		orderSheetMain.list();
	});

const orderSheetMainList = (function() {

	const API_ENDPOINTS = {
		wishListDel: "/user2/proc/WishList/delWishlist",
	};

	return {

		// 초기화
		init() {
			console.log('orderSheetMainList module initialized.');
		},

		payment(idx) {
			var width = "1000px";
			openDialog("/ad/ajax/payment_reg",{ idx, "mode":"orderSheet"  },"결제요청",width); 
		}

	}

})();	

</script> 