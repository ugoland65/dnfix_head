<?

	// 변수 초기화
	$_where = "";
	$_ledge_category = [];
	
	$_query = "select * from ledge_category ".$_where." ORDER BY idx DESC";
	$_result = sql_query_error($_query);
	while($list = sql_fetch_array($_result)){

		if( $list['lc_mode'] == "수입" ){
			$_lc_mode = "in";
		}elseif( $list['lc_mode'] == "지출" ){
			$_lc_mode = "out";
		}

		$_ledge_category[$_lc_mode][$list['lc_depth']][] = array(
			"idx" => $list['idx'],
			"name" => $list['lc_name'],
			"approval" => $list['lc_approval']
		);
	}

?>
<div id="contents_head">
	<h1>입출금 항목관리</h1>

	<!-- 
	<div class="head-btn-wrap m-l-10">
		<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="ledge.bankStatementExcelUpload();" >입출금 엑셀등록</button>
	</div>
	-->

	<!-- 
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="moneyPlan.reg(this)" > 
			<i class="fas fa-plus-circle"></i>
			신규 등록
		</button>
	</div>
	-->

	<div class="head-left-fixed-wrap">

		<div>
			<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
		</div>

		<form id="form1">
		<input type="hidden" name="a_mode" value="ledgeCategoryReg" >
		
		<div class="">
			<ul class="m-t-5">
				<input type='text' name='lc_name' id='lc_name' value="" placeholder="항목명" >
			</ul>
			<ul class="m-t-5">

			</ul>
			<ul class="m-t-5">
				<select name="lc_mode" id="lc_mode" >
					<option value="">종류</option>
					<option value="수입">수입</option>
					<option value="지출">지출</option>
				</select>
				<select name="lc_approval" id="lc_approval" >
					<option value="인정">인정</option>
					<option value="비인정">비인정</option>
				</select>
			</ul>

			<!-- 
			<ul class="m-t-5">
				SORT : 
				<select name="sort_kind" id="sort_kind" >
					<option value="stock" <? if( $_sort_kind == "stock" ) echo "selected";?>>재고 많은순</option>
					<option value="stock_asc" <? if( $_sort_kind == "stock_asc" ) echo "selected";?>>재고 적은순</option>
					<option value="idx" <? if( $_sort_kind == "idx" ) echo "selected";?> >상품 등록순</option>
					<option value="rack_code" <? if( $_sort_kind == "rack_code" ) echo "selected";?> >랙코드순</option>
				</select>
			</ul>
			-->

			<ul class="m-t-10">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="ledgeCategory.reg();" > 
					등 록
				</button>
			</ul>

		</div>
		</form>

	</div>

</div>
<div id="contents_body">
	<div id="contents_body_wrap" class="have-head-left-fixed">
<?

	$_ledge_category_in_1 = $_ledge_category['in'][1] ?? [];
	$_ledge_category_out_1 = $_ledge_category['out'][1] ?? [];

?>
		<table class="table-style">	
			<tr class="list">
				<th class="">수입항목</th>
				<th class="">지출항목</th>
			</tr>
			<tr class="list">
				<td class="" style="vertical-align:top;">
					
					<table class="table-style">	
						<tr class="list">
							<th class="">idx</th>
							<th class="">항목명</th>

						</tr>
					<?
					for ($i=0; $i<count($_ledge_category_in_1); $i++){
					?>
						<tr class="list">
							<td class=""><?=$_ledge_category_in_1[$i]['idx'] ?? ''?></td>
							<td class=""><?=$_ledge_category_in_1[$i]['name'] ?? ''?></td>
						</tr>
					<? } ?>
					</table>

				</td>
				<td class="" style="vertical-align:top;">
					
					<table class="table-style">	
						<tr class="list">
							<th class="">idx</th>
							<th class="">항목명</th>
							<th class="">계산여부</th>
						</tr>
					<?
					for ($i=0; $i<count($_ledge_category_out_1); $i++){
					?>
						<tr class="list">
							<td class=""><?=$_ledge_category_out_1[$i]['idx'] ?? ''?></td>
							<td class=""><?=$_ledge_category_out_1[$i]['name'] ?? ''?></td>
							<td class=""><?=$_ledge_category_out_1[$i]['approval'] ?? ''?></td>
						</tr>
					<? } ?>
					</table>

				</td>
			</tr>
		</table>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script type="text/javascript"> 
<!--
var ledgeCategory = function() {

	var B;

	var C = function() {
	};

	return {

		init : function() {

		},

		reg: function( pn ) {

			var _lc_name = $("#lc_name").val();
			var _lc_mode = $("#lc_mode").val();

			if( !_lc_name ){
				showAlert("Error", "항목명을 입력해 주세요", "alert2" );
				return false;
			}

			if( !_lc_mode ){
				showAlert("Error", "종류를 선택해 주세요", "alert2" );
				return false;
			}

			var formData = $("#form1").serializeArray();

			$.ajax({
				url: "/ad/processing/accounting",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						location.reload();
					}else{
						showAlert("Error", res.msg, "dialog" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {

				}
			});

		},

	};

}();

//--> 
</script> 