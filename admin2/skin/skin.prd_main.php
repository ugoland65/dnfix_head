<div id="contents_head">
	<h1>상품 재고</h1>

	<div class="head-btn-wrap m-l-10">
		<button type="button" class="btnstyle1 btnstyle1-sm" onclick="prdMain.swindow()" >새창으로 열기</button>
		<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdMain.makeGrouping('');" >선택상품 그룹핑</button>
	</div>
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
					?>
					<option value="<?=$brand_list['BD_IDX']?>" <? if( $brand_list['BD_IDX'] == $_get_brand_idx ) echo "selected";?> ><?=$brand_list['BD_NAME']?></option>
					<? } ?>
				</select>
			</ul>
			<ul class="m-t-5">
				<select name="s_kind_code" id="s_kind_code" >
					<option value="">전체 종류</option>
					<?
					for($t=0; $t<count($koedge_prd_kind_array); $t++){
					?>
					<option value="<?=$koedge_prd_kind_array[$t]['code']?>" <? if( $s_kind_code == $koedge_prd_kind_array[$t]['code'] ) echo "selected";?>><?=$koedge_prd_kind_array[$t]['name']?></option>
					<? } ?>
				</select>
				<select name="s_national" id="s_national" >
					<option value="">수입국</option>
					<?
					for ($i=0; $i<count($_arr_national); $i++){
					?>
					<option value="<?=$_arr_national[$i]['code']?>" ><?=$_arr_national[$i]['name']?></option>
					<? } ?>
				</select>
				<select name="s_rack_code_group" id="s_rack_code_group" >
					<option value="">랙코드그룹</option>
					<?
						$_query = "select 
						* ,
						LEFT(code, 2) as code_group
						from prd_rack group by code_group ORDER BY code ASC";
						$_result = sql_query_error($_query);
						while($_list = wepix_fetch_array($_result)){
					?>
					<option value="<?=$_list['code_group']?>" ><?=$_list['code_group']?></option>
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
					<option value="soldout_asc" <? if( $_sort_kind == "soldout_asc" ) echo "selected";?> >품절일 오랜순</option>
					<option value="price_desc" <? if( $_sort_kind == "price_desc" ) echo "selected";?> >판매가 높은순</option>
					<option value="price_asc" <? if( $_sort_kind == "price_asc" ) echo "selected";?> >판매가 낮은순</option>
					<option value="margin" <? if( $_sort_kind == "margin" ) echo "selected";?> >마진율 높은순</option>
					<option value="release_date" <? if( $_sort_kind == "release_date" ) echo "selected";?> >출시일 최근순</option>
					<option value="old_release_date" <? if( $_sort_kind == "old_release_date" ) echo "selected";?> >출시일 오랜순</option>
					<option value="old_sale_date" <? if( $_sort_kind == "old_sale_date" ) echo "selected";?> >판매일 오랜순</option>
					<option value="new_dis_date" <? if( $_sort_kind == "old_dis_date" ) echo "selected";?> >할인일 최근</option>
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
var prdMain = function() {

	return {

		init : function() {

		},

		list: function( pn ) {

			var sValue = $("#s_text").val();
			var sBrand = $("#s_brand").val();
			var sKindCode = $("#s_kind_code").val();
			var sNational = $("#s_national").val();
			var sRackCodeGroup = $("#s_rack_code_group").val();
			var sSortKind = $("#sort_kind").val();

			$.ajax({
				url: "/ad/ajax/prd_list",
				data: { "pn":pn, "s_text":sValue, "s_brand":sBrand, "s_kind_code":sKindCode, "s_national":sNational, "s_rack_code_group":sRackCodeGroup, "sort_kind":sSortKind },
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

		swindow : function() {

			var sValue = $("#s_text").val();
			var sBrand = $("#s_brand").val();
			var sKindCode = $("#s_kind_code").val();
			var sNational = $("#s_national").val();
			var sRackCodeGroup = $("#s_rack_code_group").val();
			var sSortKind = $("#sort_kind").val();

			var pop_title = "popupOpener" ;
			
			window.open("", pop_title, "width=1200,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no") ;
			
			var new_form = $('<form></form>');
 
			new_form.attr("name", "test_form");
			new_form.attr("method", "post");
			new_form.attr("action", "/ad/ajax/prd_list");
			new_form.attr("target", pop_title);

			new_form.append($('<input/>', {type: 'hidden', name: 'open_mode', value: 'popup'}));
			new_form.append($('<input/>', {type: 'hidden', name: 's_text', value: sValue}));
			new_form.append($('<input/>', {type: 'hidden', name: 's_brand', value: sBrand}));
			new_form.append($('<input/>', {type: 'hidden', name: 's_kind_code', value: sKindCode}));	 
			new_form.append($('<input/>', {type: 'hidden', name: 's_national', value: sNational}));	 
			new_form.append($('<input/>', {type: 'hidden', name: 's_rack_code_group', value: sRackCodeGroup}));	 
			new_form.append($('<input/>', {type: 'hidden', name: 'sort_kind', value: sSortKind}));	 

			new_form.appendTo('body');

			new_form.submit();

		},

		//그룹핑 만들기
		makeGrouping : function( grouping_mode ) {

			var checkboxCount = $(".checkSelect:checked").length;
			if( checkboxCount == 0 ){
				showAlert("Error", "선택된 상품이 없습니다.", "dialog" );
				return false; 
			}

			var chkArray = new Array();

			$("input[name='key_check[]']:checked").each(function() { 
				var tmpVal = $(this).val(); 
				chkArray.push(tmpVal);
			});

			var width = "1200px";

			prdWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "선택상품 그룹핑",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/prd_make_grouping',
						data: { "grouping_mode":grouping_mode, "chkArray":chkArray },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},
				buttons: {
					cancel: {
						text: '닫기',
						action:function () {
							
						}
					},
				}
			});

		},

	};

}();

prdMain.list();

$(function(){

	$("#s_text").bind("keydown", function(e){
		if(e.which=="13"){
			prdMain.list();
		}
	});

});
//--> 
</script>