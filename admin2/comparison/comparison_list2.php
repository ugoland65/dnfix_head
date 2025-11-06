<?
$pageGroup = "comparison";
$pageName = "comparison_list2";

include "../lib/inc_common.php";

	$_pn = securityVal($pn);
	$_s_active = securityVal($s_active);
	$_s_text = securityVal($s_text);
	$_s_brand = securityVal($s_brand);
	$_s_kind_code = securityVal($s_kind_code);
	$_stock_view_mode = securityVal($stock_view_mode);

	if(!$_stock_view_mode) $_stock_view_mode = "on";

	$_sort_kind = securityVal($sort_kind);

	$_serch_query = " where CD_IDX > 0 ";

	//검색이 있을경우
	//if( $_s_active == "on" AND $_s_text != "" ){
	if( $_s_active == "on" ){

		if( $_s_text ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$_serch_query .= " AND INSTR(LOWER(CD_NAME), LOWER('".$_s_text."')) ";
			}else{
				$_serch_query .= " AND INSTR(LOWER(CD_NAME), '".$_s_text."') ";
			}
		}

		if ( $_s_brand ){
			$_serch_query .= " AND CD_BRAND_IDX = '".$_s_brand."' ";
			//$_sort_kind = "brand_rank";
		}

		if( $_s_kind_code){
			$_serch_query .= " AND CD_KIND_CODE = '".$_s_kind_code."' ";
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


	if( $_stock_view_mode == "on" ){

		$_join_sql = " PRD inner join prd_stock STOCK ON (PRD.CD_IDX = STOCK.ps_prd_idx) ";
		$query = "select count(*) from ( select * from "._DB_COMPARISON." PRD inner join prd_stock STOCK ON (PRD.CD_IDX = STOCK.ps_prd_idx) ".$_serch_query." ) T ";
		$result = mysqli_query($connect, $query);
		$fetch_row = mysqli_fetch_row($result);

		$total_count = $fetch_row[0];

		$_sort_kind = "stock";

	}else{

		$_join_sql = "";
		$total_count = wepix_counter(_DB_COMPARISON, $_serch_query);
	}


	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);


	if( $_sort_kind == "hit" ) {
		$_sort_text = "CD_HIT desc";

	}elseif( $_sort_kind == "brand_rank" ) {
		$_sort_text = "CD_BRAND_RANK asc";

	}elseif( $_sort_kind == "stock" ) {
		$_sort_text = "STOCK.ps_stock desc";
	}else{
		//$_sort_text = "CD_IDX desc";
		$_sort_text = "CD_HIT desc";
	}

	$query = "select * from "._DB_COMPARISON." ".$_join_sql." ".$_serch_query." order by ".$_sort_text." limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$page_link_text = "comparison_list2.php?s_active=".$_s_active."&s_text=".$_s_text."&s_brand=".$_s_brand."&stock_view_mode=".$_stock_view_mode."&sort_kind=".$_sort_kind."&pn=";
	//$page_link_text = "comparison_list2.php?".$check_query_string."&pn=";
	$_view_paging = publicPaging($pn, $total_page, $list_num, $page_num, $page_link_text);
	

include "../layout/header.php";
?>

<STYLE TYPE="text/css">
.ag-kind{ width:50px !important; }
.ag-name{ width:200px !important; }
.ag-sub-count{ width:40px !important; }
.ag-sub-view{ width:50px !important; }
.margin-box-wrap{ border:1px solid #ccc !important; background-color:#f7f7f7;  padding:5px; margin:2px 0 2px; border-radius: 5px; box-sizing:border-box; position:relative; text-align:left;  }
</STYLE>

<script type='text/javascript'>

function goDel(idx){
	if(confirm("삭제하시겠습니까?")){
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
				if(ckcode=="Processing_Complete"){
					alert('삭제완료');
					location.reload();
				}
			}
		});
	}
}

function listModify(){
	var send_array2 = "";
	$(".checkSelect:checked").each(function(index){
		if(index!=0){ send_array2 += ","; }
		send_array2 += $(this).val();
	});

	if(send_array2 == ''){
		alert('수정할 상품을 선택해주세요.');
	}else{
		var form = document.form1;
		form1.idxArrayText.value=send_array2;
		form.submit();
	}
}


function listModify2(){

	var pass_kind_code = $("#pass_kind_code option:selected").val();
	//alert(pass_kind_code);

	var send_array2 = "";
	$(".checkSelect:checked").each(function(index){
		if(index!=0){ send_array2 += ","; }
		send_array2 += $(this).val();
	});

	if(send_array2 == ''){
		alert('수정할 상품을 선택해주세요.');
	}else{
		var form = document.form1;
		$("#a_mode").val("comparisonListModify2");
		form1.idxArrayText.value=send_array2;
		$("#batch_kind_code").val(pass_kind_code);
		form.submit();
	}

}

function checkC(obj){
	lineTr = obj.parentNode.parentNode;
	lineTr.style.backgroundColor = (obj.checked) ? "#eeeeee" : "#fff";
}

$( document ).ready( function() {

	$( '.check_box_all' ).click( function() {
		$( '.checkSelect' ).prop( 'checked', this.checked );
	});

});


function listRelated(good_mode){

	var checkedCount = $("input:checked[name='key_check[]']:checked").length;
	if( checkedCount == 0 ){

	}else{

		var send_array2 = "";
		$(".checkSelect:checked").each(function(index){
			if(index!=0){ send_array2 += "|"; }
			send_array2 += $(this).val();
		});
		//alert(send_array2);
		//return false;

		$.ajax({
			url: "<?=_A_PATH_COMPARISON_OK?>",
			data: {
				"a_mode":"comparisonListRelated",
				"ajax_mode":"on",
				"good_mode":good_mode,
				"idxArrayText":send_array2
			},
			type: "POST",
			dataType: "text",
			success: function(getdata){
				if (getdata){
					redatawa = getdata.split('|');
					ckcode = redatawa[1];
					ckmsg = redatawa[2];
					if(ckcode == "Processing_Complete"){
						alert("완료 되었습니다.");
					}else{
						alert(ckmsg);
						return false;
					}
				}
			},
			error: function(){
				$('#modal_alert_msg').html('에러');
				$('#modal-alert').modal({show: true,backdrop:'static'});
			}
		});

	}

}

</script>

<div id="contents_head">
	<h1>가격비교 목록 수정</h1>
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
			<ul class="list-top-btn text-left">
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="listModify()">선택 수정</button>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="listRelated('related')">선택 연관상품</button>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="listRelated('recommend')">선택 추천상품</button>

				<select name="pass_kind_code" id="pass_kind_code" style="margin-left:30px !important;">
					<option value="">전체 종류</option>
<?
for($t=0; $t<count($koedge_prd_kind_array); $t++){
?>
					<option value="<?=$koedge_prd_kind_array[$t]['code']?>"><?=$koedge_prd_kind_array[$t]['name']?></option>
<? } ?>
				</select>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="listModify2()">일괄 변경</button>

			</ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">
					<form name='form1' action="<?=_A_PATH_COMPARISON_OK?>" method="post">
					<input type='hidden' name="a_mode" id="a_mode" value="comparisonListModify">
					<input type='hidden' name="idxArrayText" value="">
					<input type='hidden' name="returnUrl" value="<?=$check_query_string?>">
					<input type='hidden' name="batch_kind_code" id="batch_kind_code" value="">
					<table class="table-list">
						<tr>
							<th class="tl-check"><input type="checkbox" name="check_box_all" class="check_box_all" onclick="select_all()"></th>
							<th style="width:45px;">IDX</th>
							<th style="width:55px;">이미지</th>
							<th>이름</th>
							<th>브랜드</th>
							<th style="width:45px;">재고</th>
							<th style="width:45px;">재고 IDX</th>
							<th>JAN 코드</th>
							<th>주문 코드</th>
							<th style="width:50px;">중량</th>
							<th>메모</th>
							<th style="width:130px;">가격</th>
							<th>노출</th>
						</tr>
						<?
						while($list = wepix_fetch_array($result)){
							$brand_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$list[CD_BRAND_IDX]."' "));

							if( $_stock_view_mode != "on" ){
								$stock_data = wepix_fetch_array(wepix_query_error("select ps_stock from prd_stock where ps_prd_idx = '".$list[CD_IDX]."' "));
								$_stock = $stock_data[ps_stock];
							}else{
								$_stock = $list[ps_stock];
							}

							$_view_brand_name = $brand_data[BD_NAME];

							$img_path = '../../data/comparion/'.$list[CD_IMG];
						?>
						<tr>
							<td class="tl-check"><input type="checkbox" name="key_check[]"  id="<?=$list[CD_IDX]?>" class="checkSelect" value="<?=$list[CD_IDX]?>" onclick="checkC(this)"></td>
							<td><?=$list[CD_IDX]?></td>
							<td>
								<? if($list[CD_COMPARISON] == "N" ){?>비노출<br><?}?>
								<? if( $list[CD_IMG]){?><img src="<?=$img_path?>" style="height:70px;"><? } ?>
								<br><b><?=$koedge_prd_kind_name[$list[CD_KIND_CODE]]?></b>
							</td>
							<td class="text-left" style="max-width:460px;">
								<div>
									<ul style="font-size:11px; margin-bottom:5px;"><?=$list[CD_RELEASE_DATE]?></ul>
									<ul><b onclick="comparisonQuick('<?=$list[CD_IDX]?>','info');" style="cursor:pointer;"><?=$list[CD_NAME]?></b></ul>
									<? if($list[CD_NAME_OG]){ ?><ul style="margin-top:5px;"><?=$list[CD_NAME_OG]?></ul><? } ?>
									<? if($list[CD_MEMO]){ ?><ul style="margin-top:5px;"><span style='color:red;'><?=nl2br($list[CD_MEMO])?></ul></span><? } ?>
								</div>
							</td>
							<td>
								<? if( $list[CD_BRAND_IDX] ){ ?>
									<?=$_view_brand_name?><br>
									<b><?=$list[CD_BRAND_RANK]?></b>위
								<? } ?>
							</td>
							<td><b onclick="comparisonQuick('<?=$list[CD_IDX]?>','stock');" style="cursor:pointer;"><?=$_stock?></b></td>
							<td><b style="font-size:15px;"><?=$list[ps_idx]?></b></td>
							<td>
								<input type='text' name='cd_code_<?=$list[CD_IDX]?>' value='<?=$list[CD_CODE]?>'>
<!-- 
								<input type='text' name='cd_size[]' value='<?=$list[CD_SIZE]?>'>
 -->
							</td>
							<td>
								<input type='text' name='cd_code2_<?=$list[CD_IDX]?>' value='<?=$list[CD_CODE2]?>'>
							</td>
							<td>
								<input type='text' name='cd_weight[]' value='<?=$list[CD_WEIGHT]?>'><br>
								<input type='text' name='cd_weight2[]' value='<?=$list[CD_WEIGHT2]?>'>
							</td>
							<td><textarea name='cd_memo[]' style='height:70px;'><?=$list[CD_MEMO]?></textarea></td>
							<td>
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
	라이드A : <b><?=number_format($list[CD_SUPPLY_PRICE_7])?></b>￥
</div>
<? } ?>

<? 
	if( $list[CD_SUPPLY_PRICE_8] > 0 ){ 
?>
<div class="margin-box-wrap">
	라이드B : <b><?=number_format($list[CD_SUPPLY_PRICE_8])?></b>￥
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
							<td>
								<select name="cd_comparison[]">
									<option value="N" <? if($list[CD_COMPARISON] == "N" ){ echo "selected"; } ?>>비노출</option>
									<option value="Y" <? if($list[CD_COMPARISON] == "Y" ){ echo "selected"; } ?>>노출</option>
								</select>
							</td>
						</tr>
						<? } ?>
					</table>
					</form>
				</div><!-- #list_box2 -->
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
					<form name='search_form' id='search_form'  method='get' action="comparison_list2.php">
					<input type="hidden" name="s_active" value="on">
					<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>

					<ul class="filter-from-ui m-t-5">
						<input type='text' name='s_text' id='s_text' size='20' value="<?=$_s_text ?>" placeholder="상품이름">
					</ul>

					<ul class="filter-from-ui m-t-5">
						<select name="s_brand">
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
						<select name="stock_view_mode">
							<option value="off" <? if( $_stock_view_mode == "off" ) echo "selected";?>>재고 상관없음</option>
							<option value="on" <? if( $_stock_view_mode == "on" ) echo "selected";?>>재고 연동</option>
						</select>
					</ul>

					<ul class="filter-from-ui m-t-5">
						<select name="s_kind_code">
							<option value="">전체 종류</option>
<?
for($t=0; $t<count($koedge_prd_kind_array); $t++){
?>
							<option value="<?=$koedge_prd_kind_array[$t]['code']?>"><?=$koedge_prd_kind_array[$t]['name']?></option>
<?
}
?>
						</select>
					</ul>

					<ul class="filter-from-ui m-t-5">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="goSerch();" > 
							<i class="fas fa-search"></i> 검색
						</button>
					</ul>
					</form>
				</div>
			</ul>
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>
	</div>
</div>

<script type="text/javascript"> 
<!-- 
function goSerch(){
	$("#search_form").submit();
}
//--> 
</script> 

<?
include "../layout/footer.php";
exit;
?>

