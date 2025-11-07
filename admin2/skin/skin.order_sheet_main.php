<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\orderSheet; 

$orderSheet = new orderSheet(); 

$result = $orderSheet->orderSheetMainIndex();

/*
	echo "<pre>";
	print_r($result);
	echo "</pre>";
*/

// 변수 초기화
$_oo_import = $_GET['oo_import'] ?? $_POST['oo_import'] ?? "all";
$_form_idx = $_GET['form_idx'] ?? $_POST['form_idx'] ?? "";
$_order_sheet_form = [];

	//주문서 폼
	$_where = "";
	$_query = "select * from ona_order_group ".$_where." ORDER BY oog_idx DESC";
	$_result = sql_query_error($_query);
	while($_list = wepix_fetch_array($_result)){
		
		// 배열 검증
		if (!is_array($_list)) {
			continue;
		}
		
		$_order_sheet_form[] = array(
			"idx" => $_list['oog_idx'] ?? '',
			"name" => $_list['oog_name'] ?? ''
		);

	}

?>

<style type="text/css">
.table-style tr.import td{ background-color:#d2eeff !important; }
.table-style tr.ko td{ background-color:#fff !important; }
.table-style tr.end td{ background-color:#eee !important; }

.table-style tr.tr-2 td{ background-color:#c1ebff !important; }
.table-style tr.tr-4 td{ background-color:#f6f0ac !important; }
.table-style tr.tr-7 td{ background-color:#ddd !important; }
.table-style tr.tr-normal td{ background-color:#fff !important; }

</style>
<div id="contents_head">
	<h1>주문서 v.4</h1>

	<div class="btn-group m-l-60" id="bresult_btn_wrap">
		<button type="button" class="btn btnstyle1 <? if( $_oo_import == "all" ){ ?>btnstyle1-primary<? } ?> btnstyle1-sm"  data-import="all">전체</button>
		<button type="button" class="btn btnstyle1 <? if( $_oo_import == "수입" ){ ?>btnstyle1-primary<? } ?> btnstyle1-sm" data-import="수입">수입주문</button>
		<button type="button" class="btn btnstyle1 <? if( $_oo_import == "국내" ){ ?>btnstyle1-primary<? } ?> btnstyle1-sm" data-import="국내">국내주문</button>
	</div>

	<div class="head-group m-l-30">
		<ul>
			<li>
				<select name="oo_form_idx" id="oo_form_idx">
					<option value="">==  주문서 폼 선택 ==</option>
					<? 
					// 배열 검증 후 count
					$_order_sheet_form_count = is_array($_order_sheet_form) ? count($_order_sheet_form) : 0;
					
					for ($i=0; $i<$_order_sheet_form_count; $i++){ 
						// 배열 요소 검증
						if (!isset($_order_sheet_form[$i]) || !is_array($_order_sheet_form[$i])) {
							continue;
						}
					?>
					<option value="<?=$_order_sheet_form[$i]["idx"] ?? ''?>" <? if( $_form_idx == ($_order_sheet_form[$i]["idx"] ?? '') ) echo "selected"; ?>><?=$_order_sheet_form[$i]["name"] ?? ''?></option>
					<? } ?>
				</select>
			</li>
			<li class="p-l-5"><input type='text' name='search_value' id='search_value' value="" style="width:200px"></li>
			<li class="p-l-3"><button type="button" class="btn btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetMain.list()">주문검색</button></li>
		</ul>
	</div>

    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="orderSheet.osReg()" > 
			<i class="fas fa-plus-circle"></i>
			신규주문서 생성
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div id="list_new_wrap"></div>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>
<script src="/admin2/js/order_sheet.js?ver=<?=$wepix_now_time?>"></script>
<script type="text/javascript"> 
<!-- 
var oo_import = "all";

var orderSheetMain = function() {

	var C = function() {
	};

	return {

		init : function() {

		},

		list: function( pn ) {

			var oo_form_idx = $("#oo_form_idx").val();
			var search_value = $("#search_value").val();
			
			if( search_value ){
				oo_import = "all";
			}

			$.ajax({
				url: "/ad/ajax/order_sheet_main_list",
				data: { "pn":pn, "oo_import":oo_import, "search_value":search_value, "oo_form_idx":oo_form_idx },
				type: "POST",
				dataType: "html",
				success: function(getdata){
					$('#list_new_wrap').html(getdata);
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

orderSheetMain.list();

$(function(){

    $('#bresult_btn_wrap button').click(function(){
		$("#bresult_btn_wrap button").removeClass('btnstyle1-primary');
		$(this).addClass('btnstyle1-primary');
		oo_import = $(this).data('import');
		orderSheetMain.list();
    });

	$("#search_value").bind("keydown", function(e){
		if(e.which=="13"){
			orderSheetMain.list();
		}
	});

});


//--> 
</script>