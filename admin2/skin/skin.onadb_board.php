<?
// 변수 초기화
$_get1 = $_GET['get1'] ?? $_get1 ?? "";
?>
<div id="contents_head">
	<h1>게시판</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='/ad/onadb/onadb_board_reg/<?=$_get1?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규 업무게시판 등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div id="list_wrap"></div>

		<div id="contents_body_bottom_padding"></div>
<!-- 
/<?=$_get1?>/
/<?=$_get2?>/
 -->
	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script type="text/javascript"> 
<!-- 
var b_code = "<?=$_get1?>";

var board = function() {

	var C = function() {
	};

	return {

		init : function() {

		},

		list: function( pn ) {

			var search_value = $("#search_value").val();
			
			if( search_value ){
				oo_import = "all";
			}

			$.ajax({
				url: "/ad/ajax/onadb_board_list",
				data: { "pn":pn, "b_code":b_code  },
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

		}
	};

}();

board.list();

$(function(){

    $('#bresult_btn_wrap button').click(function(){
		$("#bresult_btn_wrap button").removeClass('btnstyle1-primary');
		$(this).addClass('btnstyle1-primary');
		oo_import = $(this).data('import');
		board.list();
    });

	$("#search_value").bind("keydown", function(e){
		if(e.which=="13"){
			board.list();
		}
	});

});


//--> 
</script>