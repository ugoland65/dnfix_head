<?

?>
<div id="contents_head">
	<h1>상품 그룹핑</h1>

    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="prdGrouping.reg()" > 
			<i class="fas fa-plus-circle"></i>
			신규 상품 그룹핑 생성
		</button>
	</div>

	<div class="head-right-fixed-wrap">

		<div>
			<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
		</div>
		<div class="m-t-7">
			<ul class="m-t-5">
				<input type='text' name='s_text' id='s_text' value="<?=$_s_text ?>" class="width-full" placeholder="검색어" >
			</ul>
			<ul class="m-t-5">
				<select name="s_mode" id="s_mode" >
					<option value="">모드</option>
					<option value="sale" >데이할인</option>
					<option value="period" >기간할인</option>
					<option value="event" >기획전</option>
					<option value="qty" >수량 체크</option>
				</select>
			</ul>
			<? /*
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
			*/ ?>
			<ul class="m-t-15">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="prdGrouping.list();" > 
					<i class="fas fa-search"></i> 검색
				</button>
			</ul>
		</div>

	</div>

</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div id="list_wrap"></div>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script type="text/javascript">
<!-- 
var prdGrouping = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		list: function( pn ) {

			var sValue = $("#s_text").val();
			var sMode = $("#s_mode").val();

			$.ajax({
				url: "/ad/ajax/prd_grouping_list",
				data: { "pn":pn, "s_text":sValue, "s_mode":sMode },
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

		reg : function(obj) {

			var width = "600px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "신규 상품 그룹핑 생성",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/prd_grouping_reg',
						data: { "pmode":"newReg" },
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

		//상품 추가/순서변경
		prdView : function( idx ) {
		
			var width = "1400px";

			prdWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "상품 추가/순서변경",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/prd_grouping_prd',
						data: { "idx":idx },
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

prdGrouping.list();

$(function(){

	$("#s_text").bind("keydown", function(e){
		if(e.which=="13"){
			prdGrouping.list();
		}
	});

});
//--> 
</script> 