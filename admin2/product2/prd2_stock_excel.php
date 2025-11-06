<?
$pageGroup = "product2";
$pageName = "prd2_stock_excel";

include "../lib/inc_common.php";

$_idx = securityVal($idx);

include "../layout/header.php";
?>
<div id="contents_head">

	<h1>일일 재고관리 (엑셀)</h1>

	<div class="btn-group m-l-60">
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick='window.open("/admin2/product2/popup.brand_stock.php", "brand_stock", "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");'>브랜드별 재고 파일</button>
	</div>

	<div class="btn-group-wrap m-l-10">
		<form action="processing.prd2_stock.php" method="post" enctype="multipart/form-data" onsubmit="return stockExcel.excelSubmitCheck()">
		<input type="hidden" name="a_mode" value="stock_excel">
		<table>
			<tr>
				<td>엑셀파일 : &nbsp;</td>
				<td><input name="userfile" id="excel_file" type="file" id="데이터 찾기"></td>
				<td><input type="submit" value=" 재고 엑셀 올리기 " class="btnstyle1 btnstyle1-success btnstyle1-sm"></td>
			</tr>
		</table>
		</form>
	</div>

</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div id="" class="list-box-layout3 display-table">
			<ul class="display-table-cell width-700 v-align-top">
				
				<div class="list-box-layout3-wrap" id="stock_prd_list">

<table class="table-list stock-list">

<?
$query = "select 
	uid, file_name, reg_time, step, info, error, end_time, reg_id
from prd_stock_history ".$_where." order by uid desc limit 0, 25";
$result = wepix_query_error($query);
while($list = wepix_fetch_array($result)){
	$_step_name = "";
	if( $list['step'] == "1" ){
		$_step_name = "<span style='color:#ff0000'>[임시저장]</span> ";
	}

	$_info_data = json_decode($list['info'],true);
	$_error_data = json_decode($list['error'],true);

?>
	<tr id="tr_<?=$list['uid']?>" onclick="stockExcel.view('<?=$list['uid']?>', 'qty')" style="cursor:pointer;" 
		data-step="<?=$list['step']?>" data-name="<?=$_step_name?><?=$list['file_name']?> | 주문 : <?=$_info_data['order_count']?> | 상품 : <?=$_info_data['pd_count']?>" >
		<td><?=$list['uid']?></td>
		<td class="text-left"><?=$_step_name?><b><?=$list['file_name']?></b></td>
		<td>
			등록 : <?=$list['reg_time']?>
			<? if( $list['end_time'] > 0){ ?><br>처리 : <?=$list['end_time']?><? } ?>
		</td>
		<td><?=$_info_data['order_count']?></td>
		<td><?=$_info_data['pd_count']?></td>
		<td><?=$_error_data['count']?></td>
		<td><?=$list['reg_id']?></td>
	</tr>
<? } ?>
</table>

				</div>

			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell v-align-top">
				
				<div style="height:35px;" id="sort_wrap" data-idx="" data-sort="qty">
					<button type="button" class="btnstyle1 btnstyle1-sm" disabled="disabled" onclick="stockExcel.sort('qty')" >수량 높은순</button>
					<button type="button" class="btnstyle1 btnstyle1-sm"disabled="disabled"  onclick="stockExcel.sort('brand')" >브랜드 순</button>
					<span id="sh_name"></span>
					<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm m-l-40" disabled="disabled" onclick="stockExcel.excelDown()" >엑셀 다운로드</button>
					<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm m-l-5" disabled="disabled" onclick="stockExcel.swindow()" >새창열기</button>
					<iframe id="excelDown_iframe" src='' style='display:none;'></iframe>
					<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm m-l-40" disabled="disabled" onclick="stockExcel.del(this)" ><i class="fas fa-minus-circle"></i> 삭제</button>
				</div>

				<div class="list-box-layout3-wrap" id="view">
				</div>

			</ul>
		</div>

		<div class="list-bottom-btn-wrap">
			<ul class="list-top-total">
				<span class="count"></span>
			</ul>
			<ul class="list-top-btn">
				재고 처리 날짜 : 
				<div class="calendar-input" style="display:inline-block;"><input type='text' name="stock_day" id="stock_day" value="<?=date("Y-m-d")?>" ></div>
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg m-l-10" disabled="disabled" onclick="stockExcel.dayStock(this)">재고 입출고 등록하기</button>
			</ul>
		</div>

	</div>
</div>

<script type="text/javascript"> 
<!-- 
var stockExcel = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		view : function(idx, sort) {

			$.ajax({
				url: "prd2_stock_excel_view.php",
				data: { "idx":idx, "sort":sort },
				type: "POST",
				dataType: "html",
				success: function(html){
					$("#view").html(html);
					$("#sort_wrap").data('idx', idx).data('sort', sort);
					$("#sort_wrap button").attr('disabled', false);
					$(".stock-list tr").removeClass('active');
					$("#tr_"+idx).addClass('active');
					$("#sh_name").html($("#tr_"+idx).data('name'));
					
					if( $("#tr_"+idx).data('step') == "1" ){
						$(".list-top-btn button").attr('disabled', false);
					}

				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {

				}
			});
		},
		sort : function(mode) {
			stockExcel.view($("#sort_wrap").data('idx'), mode);
		},

		excelDown : function() {
			$("#excelDown_iframe").attr("src","prd2_stock_excel_view.php?mode=excelDown&idx="+ $("#sort_wrap").data('idx') +"&sort="+ $("#sort_wrap").data('sort'));
		},

		swindow : function() {
			window.open("prd2_stock_excel_view2.php?idx="+ $("#sort_wrap").data('idx') +"&sort="+ $("#sort_wrap").data('sort'), "excelDown", "width=1000,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
		},

		dayStock : function(obj) {

			var formData = $("#form2").serializeArray();
			var stock_day = $("#stock_day").val();
			var stock_history_idx = $("#sort_wrap").data("idx");

			formData.push({name: "stock_day", value: stock_day});
			formData.push({name: "stock_history_idx", value: stock_history_idx});

			$(obj).attr('disabled', true);
			$.ajax({
				url: "processing.prd2_stock.php",
				data : formData, 
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						GC.movePage("prd2_stock_excel.php?idx="+res.idx);
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		},
		del : function(obj) {

			var idx = $("#sort_wrap").data('idx');
			if( $("#tr_"+idx).data('step') == "2" ){
				showAlert("Error", "완료된건은 삭제 불가", "alert2" );
				return false;
			}

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

							$(obj).attr('disabled', true);
							$.ajax({
								url: "processing.prd2_stock.php",
								data: { "a_mode":"day_stock_del", "idx":idx },
								type: "POST",
								dataType: "json",
								success: function(res){
									if (res.success == true ){
										GC.movePage("prd2_stock_excel.php");
									}else{
										showAlert("Error", res.msg, "alert2" );
										return false;
									}
								},
								error: function(){
									showAlert("Error", "에러2", "alert2" );
									return false;
								},
								complete: function() {
									$(obj).attr('disabled', false);
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
		excelSubmitCheck : function(obj) { //엑셀등록시 폼 체크
			var fileCheck = document.getElementById("excel_file").value;
			if(!fileCheck){
				alert("파일을 첨부해 주세요");
				return false;
			}
		}
	};

}();

$(function(){

<? if( $_idx ){ ?>
	stockExcel.view('<?=$_idx?>', 'qty');
<? } ?>


	var content22 = '일일 재고관리 (엑셀) v.2은 곧 폐기될 예정입니다.<br><b>재고/발주</b> > 일일 재고관리 (엑셀) 를 사용해 주세요.'
		+ '<br>v.2 는 오류 발견으로 인하여 업무처리 문제시에만 사용해 주세요.'
		+ '<br>v.2 오류 발견 시 별도로 보고해 주세요.';


	$.confirm({
		boxWidth : "500px",
		useBootstrap : false,
		icon: 'fas fa-exclamation-triangle',
		title: '공지',
		content: content22,
		type: 'red',
		typeAnimated: true,
		closeIcon: true,
		buttons: {
			somethingElse: {
				text: '신규 일일 재고관리 (엑셀) 바로가기',
				btnClass: 'btn-red',
				action: function(){
					location.href='/ad/order/stock_excel';
				}
			},
			cencle: {
				text: 'v.2 사용',
				action: function(){
				}
			}
		}
	});

});

function stockModeAll(mode){

	if( mode == "plus" ){
		$(".stock-mode-value").each(function(){
			$(this).val('plus');
		});
		$(".stock-mode-text").each(function(){
			$(this).html('입고');
		});
	}else if( mode == "minus" ){
		$(".stock-mode-value").each(function(){
			$(this).val('minus');
		});
		$(".stock-mode-text").each(function(){
			$(this).html('출고');
		});
	}

}


function stockMemoAll(text){
	$(".stock-memo").each(function(){
		$(this).val(text);
	});
}
//--> 
</script> 

<script type="text/javascript"> 
<!-- 
function dayStock(){
	$("#stock_day").val($("#_stock_day").val());
	$("#day_stock_form").submit();
}
//--> 
</script> 

<?
include "../layout/footer.php";
exit;
?>