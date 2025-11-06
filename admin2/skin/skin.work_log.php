<?

if( $_get1 ){
	$_work_log_cate_get = $_get1;
}else{
	$_work_log_cate_get = "all";
}

if( $_get_cate ){
	$_work_log_cate_get = $_get_cate;
}

if( $_get_call_mode ){
	$_call_mode = $_get_call_mode;
}else{
	$_call_mode = "all";
}

	$_check = "";
if( $_get_check ){
	$_check = $_get_check;
}

	$_state = "";
if( $_get_state ){
	$_state = $_get_state;
}

?>

<style type="text/css">
.table-style tr.import td{ background-color:#d2eeff !important; }
.table-style tr.ko td{ background-color:#fff !important; }
.table-style tr.end td{ background-color:#eee !important; }
</style>

<div id="contents_head">
	<h1>업무 게시판</h1>
	<!-- 
	<div class="head-btn-wrap m-l-10">
		<button type="button" class="btnstyle1 btnstyle1-sm" onclick="" >주문처 관리</button>
	</div>
	-->

	<div class="btn-group m-l-60" id="work_log_cate_btn_wrap">
		<button type="button" class="btn btnstyle1 <? if( $_work_log_cate_get == "all" ){ ?>btnstyle1-primary<? } ?> btnstyle1-sm"  data-cate="all">전체</button>
		<?
			for ($i=0; $i<count($_work_log_cate); $i++){
		?>
		<button type="button" class="btn btnstyle1 <? if( $_work_log_cate_get == $_work_log_cate[$i]['name'] ){ ?>btnstyle1-primary<? } ?> btnstyle1-sm" data-cate="<?=$_work_log_cate[$i]['name']?>"><?=$_work_log_cate[$i]['name']?></button>
		<? } ?>
	</div>

    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='/ad/staff/work_log_reg'" > 
			<i class="fas fa-plus-circle"></i>
			신규 업무게시판 등록
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
var _work_log_cate = "<?=$_work_log_cate_get?>";
var _call_mode = "<?=$_call_mode?>";
var _check = "<?=$_check?>";
var _state = "<?=$_state?>";

var workLogMain = function() {

	return {

		init : function() {

		},

		list: function( pn ) {

			$.ajax({
				url: "/ad/ajax/work_log_list",
				data: { "pn":pn, "work_log_cate":_work_log_cate, "call_mode":_call_mode, "check":_check, "state":_state },
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

workLogMain.list();

$(function(){

    $('#work_log_cate_btn_wrap button').click(function(){
		$("#work_log_cate_btn_wrap button").removeClass('btnstyle1-primary');
		$(this).addClass('btnstyle1-primary');
		_work_log_cate = $(this).data('cate');
		//location.href='/ad/staff/work_log/'+ _work_log_cate;
		location.href='/ad/staff/work_log/cate='+ _work_log_cate+':';
		//workLogMain.list();
    });

});
//--> 
</script>