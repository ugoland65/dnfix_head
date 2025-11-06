<div id="contents_head">
	<h1>일일 마감</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='/ad/accounting/work_end_reg'" > 
			<i class="fas fa-plus-circle"></i>
			일일마감 등록
		</button>
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
var workEndList = function() {

	return {

		init : function() {

		},

		list: function( pn ) {

			var search_value = $("#search_value").val();
			
			if( search_value ){
				oo_import = "all";
			}

			$.ajax({
				url: "/ad/ajax/work_end_list",
				data: { "pn":pn },
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

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "브랜드 신규생성",
				backgroundDismiss: true,
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

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "브랜드 정보",
				backgroundDismiss: true,
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

	};

}();

workEndList.list();

//--> 
</script>