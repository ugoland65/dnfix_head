<div id="contents_head">
	<h1>브랜드 목록</h1>
	
	<!-- 
	<div class="head-btn-wrap m-l-10">
		<button type="button" class="btnstyle1 btnstyle1-sm" onclick="" >주문처 관리</button>
	</div>
	-->

    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="brandLlist.reg()" > 
			<i class="fas fa-plus-circle"></i>
			신규 브랜드 생성
		</button>
	</div>


	<div class="head-right-fixed-wrap">

		<div>
			<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
		</div>
		<div class="m-t-7">
			<ul class="m-t-5">
				<input type='text' name='s_text' id='s_text' placeholder="검색어" style="width:100%;">
			</ul>
			<ul class="m-t-5">
				<select name="s_kind" id="s_kind" >
					<option value="">전체 종류</option>
					<?
					for($t=0; $t<count($koedge_prd_kind_array); $t++){
					?>
					<option value="<?=$koedge_prd_kind_array[$t]['code']?>" <? if( $s_kind_code == $koedge_prd_kind_array[$t]['code'] ) echo "selected";?>><?=$koedge_prd_kind_array[$t]['name']?></option>
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
				</select>
			</ul>
			<ul class="m-t-15">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="brandLlist.list();" > 
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
var brandLlist = function() {

	var brandInfoWindow;

	return {

		init : function() {

		},

		list: function( pn ) {

			var s_text = $("#s_text").val();
			var s_kind = $("#s_kind").val();

			$.ajax({
				url: "/ad/ajax/brand_list",
				data: { "pn":pn, "s_text":s_text, "s_kind":s_kind },
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

			var width = "800px";

			brandInfoWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "브랜드 신규생성",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/brand_info',
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

		view : function( idx ) {

			var width = "800px";

			brandInfoWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "브랜드 정보",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/brand_info',
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

		del : function( idx ) {

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제하시겠습니까?',
				content: '삭제하시면 데이터는 복구하지 못합니다.',
				autoClose: 'cencle|9000',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '삭제',
						btnClass: 'btn-red',
						action: function(){
							
							$.ajax({
								url: "/ad/processing/brand",
								data: { "a_mode":"brand_del", "idx": idx },
								type: "POST",
								dataType: "json",
								success: function(res){
									if (res.success == true ){
										$("#trid_"+ idx).remove();
									}else{
										showAlert("Error", res.msg, "dialog" );
										return false;
									}
								},
								error: function(){
									showAlert("Error", "에러", "dialog" );
									return false;
								},
								complete: function() {
								}
							});

						}
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});

		},

		infoClose : function( idx ) {
			brandInfoWindow.close();
		}
	};

}();

brandLlist.list();

$(function(){

	$("#s_text").bind("keydown", function(e){
		if(e.which=="13"){
			brandLlist.list();
		}
	});

});

//--> 
</script>