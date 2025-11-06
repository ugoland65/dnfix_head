<div id="contents_head">
	<h1>상품 등록</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div id="view_wrap"></div>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>
<script type="text/javascript"> 
<!--
var prdReg = function() {

	return {

		init : function() {

		},

		form_view: function( pn ) {

			var sValue = $("#s_text").val();
			var sBrand = $("#s_brand").val();
			var sKindCode = $("#s_kind_code").val();
			var sNational = $("#s_national").val();
			var sSortKind = $("#sort_kind").val();

			$.ajax({
				url: "/ad/ajax/prd_reg_form",
				data: {  },
				type: "POST",
				dataType: "html",
				success: function(getdata){
					$('#view_wrap').html(getdata);
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

prdReg.form_view();

$(function(){


});
//--> 
</script>