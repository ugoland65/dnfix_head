<?
// 변수 초기화
$_s_text = $_GET['s_text'] ?? $_POST['s_text'] ?? "";
$_get_brand_idx = $_GET['brand_idx'] ?? $_POST['brand_idx'] ?? "";
$s_kind_code = $_GET['s_kind_code'] ?? $_POST['s_kind_code'] ?? "";
$_sort_kind = $_GET['sort_kind'] ?? $_POST['sort_kind'] ?? "stock";

// 배열 초기화 (inc_common.php에서 정의되지 않은 경우 대비)
if (!isset($koedge_prd_kind_array)) {
    $koedge_prd_kind_array = [];
}
if (!isset($_arr_national)) {
    $_arr_national = [];
}
?>
<div id="contents_head">
	<h1>onaDB 상품 코멘트</h1>

<!-- 
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="" > 
			<i class="fas fa-plus-circle"></i>
			신규상품 등록
		</button>
	</div>
 -->
	<div class="head-right-fixed-wrap">

		<div>
			<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
		</div>
		<div class="m-t-7">
			<ul class="m-t-5">
				<input type='text' name='s_text' id='s_text' size='20' value="<?=$_s_text ?>" placeholder="검색어" >
			</ul>
			<ul class="m-t-5">
				<select name="s_brand" id="s_brand" >
					<option value="">전체 브랜드</option>
					<option value="no" >브랜드 미설정 상품</option>
					<?
					$brand_result = sql_query_error("select BD_IDX,BD_NAME from "._DB_BRAND." WHERE BD_LIST_ACTIVE = 'Y' order by BD_NAME asc ");
					while($brand_list = sql_fetch_array($brand_result)){
						if (!is_array($brand_list)) continue;
					?>
					<option value="<?=$brand_list['BD_IDX'] ?? ''?>" <? if( ($brand_list['BD_IDX'] ?? '') == $_get_brand_idx ) echo "selected";?> ><?=$brand_list['BD_NAME'] ?? ''?></option>
					<? } ?>
				</select>
			</ul>
			<ul class="m-t-5">
				<select name="s_kind_code" id="s_kind_code" >
					<option value="">전체 종류</option>
					<?
					for($t=0; $t<count($koedge_prd_kind_array); $t++){
					?>
					<option value="<?=$koedge_prd_kind_array[$t]['code'] ?? ''?>" <? if( $s_kind_code == ($koedge_prd_kind_array[$t]['code'] ?? '') ) echo "selected";?>><?=$koedge_prd_kind_array[$t]['name'] ?? ''?></option>
					<? } ?>
				</select>
				<select name="s_national" id="s_national" >
					<option value="">수입국</option>
					<?
					for ($i=0; $i<count($_arr_national); $i++){
					?>
					<option value="<?=$_arr_national[$i]['code'] ?? ''?>" ><?=$_arr_national[$i]['name'] ?? ''?></option>
					<? } ?>
				</select>
			</ul>
			<ul class="m-t-5">
				SORT : 
				<select name="sort_kind" id="sort_kind" >
					<option value="stock" <? if( $_sort_kind == "stock" ) echo "selected";?>>재고 많은순</option>
					<option value="stock_asc" <? if( $_sort_kind == "stock_asc" ) echo "selected";?>>재고 적은순</option>
					<option value="idx" <? if( $_sort_kind == "idx" ) echo "selected";?> >상품 등록순</option>
					<option value="rack_code" <? if( $_sort_kind == "rack_code" ) echo "selected";?> >랙코드순</option>
					<option value="soldout" <? if( $_sort_kind == "soldout" ) echo "selected";?> >품절일 최근순</option>
					<option value="margin" <? if( $_sort_kind == "margin" ) echo "selected";?> >마진율 높은순</option>
					<option value="release_date" <? if( $_sort_kind == "release_date" ) echo "selected";?> >출시일 최근순</option>
					<option value="old_sale_date" <? if( $_sort_kind == "old_sale_date" ) echo "selected";?> >판매일 오랜순</option>
					<option value="new_dis_date" <? if( $_sort_kind == "old_dis_date" ) echo "selected";?> >할인일 최근순</option>
					<option value="old_dis_date" <? if( $_sort_kind == "new_dis_date" ) echo "selected";?> >할인일 오랜순</option>
				</select>
			</ul>
			<ul class="m-t-15">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="prdMain.list();" > 
					<i class="fas fa-search"></i> 검색
				</button>
			</ul>
		</div>

	</div>

</div>
<div id="contents_body">
	<div id="contents_body_wrap" class="have-head-right-fixed">

		<div id="list_wrap"></div>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>
<script type="text/javascript"> 
<!--
var onaDBprdComment = function() {

	return {

		init : function() {

		},

		list: function( pn ) {

			var sValue = $("#s_text").val();
			var sBrand = $("#s_brand").val();
			var sKindCode = $("#s_kind_code").val();
			var sNational = $("#s_national").val();
			var sSortKind = $("#sort_kind").val();

			$.ajax({
				url: "/ad/ajax/onadb_prd_comment_list",
				data: { "pn":pn, "s_text":sValue, "s_brand":sBrand, "s_kind_code":sKindCode, "s_national":sNational, "sort_kind":sSortKind },
				type: "POST",
				dataType: "html",
				success: function(getdata){
					$('#list_wrap').html(getdata);
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {
					loading('off','white');
				}
			});

		},


	};

}();

onaDBprdComment.list();

$(function(){

/*
	$("#s_text").bind("keydown", function(e){
		if(e.which=="13"){
			prdMain.list();
		}
	});
*/

});
//--> 
</script>