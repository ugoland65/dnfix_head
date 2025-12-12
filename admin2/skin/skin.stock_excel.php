<div id="contents_head">
	<h1>일일 재고관리 (엑셀) </h1>
	<div class="btn-group-wrap m-l-10">
		<form action="/ad/processing/prd_stock" method="post" enctype="multipart/form-data" onsubmit="return stockExcel.excelSubmitCheck()">
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
	<div class="btn-group-wrap m-l-10">
		<button type="button" class="btnstyle1 btnstyle1-sm" onclick="stockExcel.godoOrderPrint()" >고도몰 주문서 프린트 (구버전)</button>
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="stockExcel.godoOrderPrint('new')" >고도몰 주문서 프린트 (신버전)</button>
	</div>
</div>

<style type="text/css">
.division-layout-wrap{ display:table; width:100%; height:100%; overflow:hidden; }
.division-layout-wrap > ul{ height:100%; display:table-cell; vertical-align:top; }
.division-1{ width:650px; height:calc(100% - 30px); }
.division-2{ padding:0 0 0 15px; }
.division-top{ height:30px; }
.division-1 .division-body{ height:calc(100% - 30px); }
.division-2 .division-body{ height:calc(100% - 80px); }
.division-2 .division-bottom{ padding-top:10px; height:50px; }
.scroll-wrap{ width:100%; height:100%; border:1px solid #555555; background:#ffffff; box-sizing:border-box; padding:0;  overflow-y:scroll;   }
.scroll-wrap::-webkit-scrollbar{ width:7px; height:7px; border-left:solid 1px rgba(255,255,255,.1)}
.scroll-wrap::-webkit-scrollbar-thumb{  background:#aaa;  }
.division-layout-wrap .table-style{ width:100%; }
</style>

<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="division-layout-wrap">
			<ul class="division-1">
				
				<div class="division-top">
					<div class="calendar-input" style="display:inline-block;"><input type='text' name="stock_day_s" id="stock_day_s" value="<?=date("Y-m-d")?>" ></div> ~
					<div class="calendar-input" style="display:inline-block;"><input type='text' name="stock_day_e" id="stock_day_e" value="<?=date("Y-m-d")?>" ></div>
				</div>

				<div class="division-body scroll-wrap">
					<table class="table-style border01 stock-list">
					<thead class="sticky">
						<tr class="sticky">
							<th>번호</th>
							<th>파일명</th>
							<th>등록/처리</th>
							<th>주문</th>
							<th>상품</th>
							<th>에러</th>
							<th>등록자</th>
						</tr>
					</thead>
					<?
					// 변수 초기화
					$_idx = $_GET['idx'] ?? $_POST['idx'] ?? "";
					$_where = "";
					
					$query = "select 
						uid, file_name, reg_time, step, info, error, end_time, reg_id
						from prd_stock_history ".$_where." order by uid desc limit 0, 25";
					$result = wepix_query_error($query);
					while($list = wepix_fetch_array($result)){
						
						// 배열 검증
						if (!is_array($list)) {
							continue;
						}

						$_step_name = "";
						if( ($list['step'] ?? '') == "1" ){
							$_step_name = "<span style='color:#ff0000'>[임시저장]</span> ";
						}

						$_info_data = json_decode($list['info'] ?? '{}', true);
						$_error_data = json_decode($list['error'] ?? '{}', true);

						// JSON 디코딩 결과 검증
						if (!is_array($_info_data)) {
							$_info_data = [];
						}
						if (!is_array($_error_data)) {
							$_error_data = [];
						}

					?>
						<tr id="tr_<?=$list['uid'] ?? ''?>" onclick="stockExcel.view('<?=$list['uid'] ?? ''?>', 'qty')" style="cursor:pointer;" 
							data-step="<?=$list['step'] ?? ''?>" data-name="<?=$_step_name?><?=$list['file_name'] ?? ''?> | 주문 : <?=$_info_data['order_count'] ?? 0?> | 상품 : <?=$_info_data['pd_count'] ?? 0?>" >
							<td class="text-center"><?=$list['uid'] ?? ''?></td>
							<td class="text-left"><?=$_step_name?><b><?=$list['file_name'] ?? ''?></b></td>
							<td>
								등록 : <?=$list['reg_time'] ?? ''?>
								<? if( ($list['end_time'] ?? 0) > 0){ ?><br>처리 : <?=$list['end_time']?><? } ?>
							</td>
							<td class="text-center"><?=$_info_data['order_count'] ?? 0?></td>
							<td class="text-center"><?=$_info_data['pd_count'] ?? 0?></td>
							<td class="text-center"><?=$_error_data['count'] ?? 0?></td>
							<td><?=$list['reg_id'] ?? ''?></td>
						</tr>
					<? } ?>
					</table>
				</div>

			</ul>
			<ul class="division-2" id="stock_excel_view">
				
				<div class="division-top" id="sort_wrap" data-idx="" data-sort="qty">
					
					<!-- 
					<button type="button" class="btnstyle1 btnstyle1-sm" disabled="disabled" onclick="stockExcel.sort('qty')" >수량 높은순</button>
					<button type="button" class="btnstyle1 btnstyle1-sm"disabled="disabled"  onclick="stockExcel.sort('brand')" >브랜드 순</button>
					<span id="sh_name"></span>
					-->
					<div class="float-right">
						<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" disabled="disabled" onclick="stockExcel.excelDown()" >엑셀 다운로드</button>
						<iframe id="excelDown_iframe" src='' style='display:none;'></iframe>
						<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm m-l-5" disabled="disabled" onclick="stockExcel.swindow()" >새창열기</button>
						<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm m-l-20" disabled="disabled" onclick="stockExcel.del(this)" ><i class="fas fa-minus-circle"></i> 삭제</button>
					</div>

				</div>

				<div class="division-body scroll-wrap" >
				</div>

				<div class="division-bottom text-center">
					재고 처리 날짜 : 
					<div class="calendar-input" style="display:inline-block;"><input type='text' name="stock_day" id="stock_day" value="<?=date("Y-m-d")?>" ></div>
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg m-l-10" disabled="disabled" onclick="stockExcel.dayStock(this)">재고 입출고 등록하기</button>
				</div>

			</ul>
		</div>

	</div>
</div>

<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script type="text/javascript"> 
<!-- 
// 전역 스코프에 stockExcel 객체 등록
var stockExcel = (function () {

	function view(idx, sort) { 
		if (!idx) {
			console.error('idx is required');
			return;
		}
		if (!sort) {
			sort = 'qty';
		}
		
		$.ajax({
			url: "/ad/ajax/stock_excel_view",
			data: { "idx":idx, "sort":sort },
			type: "POST",
			dataType: "html",
			success: function(html){
				$(".stock-list tr").removeClass('active');
				$("#tr_"+idx).addClass('active');
				$("#stock_excel_view").html(html);
			},
			error: function(request, status, error){
				console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				showAlert("Error", "에러", "alert2" );
				return false;
			},
			complete: function() {
				
			}
		});
	}

	function excelSubmitCheck(obj) {
        var fileCheck = document.getElementById("excel_file");
        if (!fileCheck || !fileCheck.value) {
            alert("파일을 첨부해 주세요");
            return false;
        }
        return true;
    }

	/**
	* 고도몰 주문서 프린트
	*/
	function godoOrderPrint(mode) {

		if( mode == 'new') {
			window.open("/admin/sales/packing_list", "bb", "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
		}else{
			window.open("/ad/ajax/godo_order_print", "aa", "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
		}
		
	}

	return {
		init: function() {},
		view: view,
		excelSubmitCheck: excelSubmitCheck,
		godoOrderPrint: godoOrderPrint
	};

})();

// 전역 스코프 확인
if (typeof window !== 'undefined') {
	window.stockExcel = stockExcel;
}

$(function(){

	<? if( !empty($_idx) ){ ?>
		stockExcel.view('<?=$_idx?>', 'qty');
	<? } ?>

});
//--> 
</script> 