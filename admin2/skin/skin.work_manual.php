<?

if( !$_oo_import ) $_oo_import = "all";

?>

<style type="text/css">
.table-style tr.import td{ background-color:#d2eeff !important; }
.table-style tr.ko td{ background-color:#fff !important; }
.table-style tr.end td{ background-color:#eee !important; }
</style>
<div id="contents_head">
	<h1>업무 매뉴얼</h1>
	<!-- 
	<div class="head-btn-wrap m-l-10">
		<button type="button" class="btnstyle1 btnstyle1-sm" onclick="" >주문처 관리</button>
	</div>
	-->

	<div class="btn-group m-l-60" id="work_manual_cate_btn_wrap">
		<button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm"  data-cate="all">전체</button>
		<?
			for ($i=0; $i<count($_work_manual_cate); $i++){
		?>
		<button type="button" class="btn btnstyle1 btnstyle1-sm" data-cate="<?=$_work_manual_cate[$i]['name']?>"><?=$_work_manual_cate[$i]['name']?></button>
		<? } ?>
	</div>

    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='/ad/staff/work_manual_reg'" > 
			<i class="fas fa-plus-circle"></i>
			신규 매뉴얼 등록
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
<script src="/admin2/js/order_sheet.js?ver=<?=$wepix_now_time?>"></script>
<script type="text/javascript"> 
<!-- 
var _work_manual_cate = "all";

var workManualMain = function() {

	var C = function() {
	};

	return {

		init : function() {

		},

		list: function( pn ) {

			$.ajax({
				url: "/ad/ajax/work_manual_list",
				data: { "pn":pn, "work_manual_cate":_work_manual_cate },
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

workManualMain.list();

$(function(){

    $('#work_manual_cate_btn_wrap button').click(function(){
		$("#work_manual_cate_btn_wrap button").removeClass('btnstyle1-primary');
		$(this).addClass('btnstyle1-primary');
		_work_manual_cate = $(this).data('cate');
		workManualMain.list();
    });

});
//--> 
</script>