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
		<button type="button" class="btnstyle1 btnstyle1-sm" onclick="stockExcel.godoOrderPrint()">고도몰 주문서 프린트 (구버전)</button>
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="stockExcel.godoOrderPrint('new')">고도몰 주문서 프린트 (신버전)</button>
	</div>
</div>

<style type="text/css">
	.division-layout-wrap {
		display: flex;
		gap: 15px;
		width: 100%;
		height: 100%;
		overflow: hidden;
	}

	.division-layout-wrap>ul {}

	.division-1 {
		width: 650px;
	}

	.division-2 {
		flex: 1;
		padding: 0 0 0 0;
	}

	.division-top {
		height: 30px;
		display: flex;
		align-items: center;
		gap: 5px;

	}

	.division-1 .division-body {
		height: calc(100% - 30px);
	}

	.division-2 .division-body {
		height: calc(100% - 80px);
	}

	.division-2 .division-bottom {
		padding-top: 10px;
		height: 50px;
	}

	.scroll-wrap::-webkit-scrollbar {
		width: 7px;
		height: 7px;
		border-left: solid 1px rgba(255, 255, 255, .1)
	}

	.scroll-wrap::-webkit-scrollbar-thumb {
		background: #aaa;
	}

	.division-layout-wrap .table-style {
		width: 100%;
	}

	.stock-list tr.active td{
		background: #fff3cd !important; /* 더 밝은 하이라이트 */
		border: 1px solid #f5c16c !important;
	}
</style>

<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="division-layout-wrap">
			<ul class="division-1">

				<div class="division-top">
					<ul class="calendar-input">
						<input type='text' name="s_date" id="s_date" value="<?= date("Y-m-d") ?>">
					</ul>
					<ul>~</ul>
					<ul class="calendar-input">
						<input type='text' name="e_date" id="e_date" value="<?= date("Y-m-d") ?>">
					</ul>
					<ul>
						<button type="button" id="" class="btnstyle1 btnstyle1-inverse3 btnstyle1-sm" onclick="stockExcel.list()">기간검색</button>
					</ul>
				</div>

				<div class="division-body scroll-wrap">
					<table class="table-st1 stock-list">
						<thead>
							<tr>
								<th>번호</th>
								<th>파일명</th>
								<th>등록/처리</th>
								<th>주문</th>
								<th>상품</th>
								<th>패킹<br>제거</th>
								<th>에러</th>
								<th>등록자</th>
							</tr>
						</thead>
						<tbody id="stock_excel_list">
						</tbody>
						<tfoot id="stock_excel_list_tfoot">
							<tr>
								<th colspan="3">합계</th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
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
						<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" disabled="disabled" onclick="stockExcel.excelDown()">엑셀 다운로드</button>
						<iframe id="excelDown_iframe" src='' style='display:none;'></iframe>
						<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm m-l-5" disabled="disabled" onclick="stockExcel.swindow()">새창열기</button>
						<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm m-l-20" disabled="disabled" onclick="stockExcel.del(this)"><i class="fas fa-minus-circle"></i> 삭제</button>
					</div>

				</div>

				<div class="division-body scroll-wrap">
				</div>

				<div class="division-bottom text-center">
					재고 처리 날짜 :
					<div class="calendar-input" style="display:inline-block;"><input type='text' name="stock_day" id="stock_day" value="<?= date("Y-m-d") ?>"></div>
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg m-l-10" disabled="disabled" onclick="stockExcel.dayStock(this)">재고 입출고 등록하기</button>
				</div>

			</ul>
		</div>

	</div>
</div>

<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<!-- 파일 미리보기 팝업 -->
<script type="text/template" id="stockExcelListTemplate">
	<tr id="tr_<%= uid %>" onclick="stockExcel.view('<%= uid %>', 'qty')" style="cursor:pointer;"
		data-step="<%= step %>" data-name="<%= step_name %><%= file_name %> | 주문 : <%= order_count %> | 상품 : <%= pd_count %>">
		<td class="text-center"><%= uid %></td>
		<td class="text-left"><%= step_name %><b><%= file_name %></b></td>
		<td>
			등록 : <%= reg_time %>
			<% if (end_time) { %><br>처리 : <%= end_time %><% } %>
		</td>
		<td class="text-center"><%= order_count %></td>
		<td class="text-center"><%= pd_count %></td>
		<td class="text-center">
			<% if (package_out > 0) { %>
				<%= package_out %>
			<% } %>
		</td>
		<td class="text-center">
			<% if (error_count > 0) { %><span style="color:red;"><%= error_count %></span><% } %>
		</td>
		<td>
			<%= reg_id %><br/>(<%= reg_name %>)
		</td>
	</tr>
</script>

<script type="text/javascript">
	// 전역 스코프에 stockExcel 객체 등록
	var stockExcel = (function() {

		/**
		 * 재고 엑셀 목록 조회
		 */
		function list() {

			var s_date = $("#s_date").val();
			var e_date = $("#e_date").val();

			ajaxRequest("/admin//stock_history/list", {
					s_date,
					e_date
				}, {
					method: "GET"
				})
				.then(res => {
					if (res.status === "success") {
	
						$("#stock_excel_list").empty();
						const rows = res.data.productStockHistoryList;
						const template = _.template($('#stockExcelListTemplate').html());
						
						let total_order_count = 0;
						let total_pd_count = 0;
						let total_package_out = 0;
						let total_error_count = 0;

						rows.forEach(row => {
							var renderedHTML = template({
								uid: row.uid,
								file_name: row.file_name,
								reg_time: row.reg_time,
								end_time: row.end_time,
								reg_id: row.reg_id,
								reg_name: row.reg_name,
								step: row.step,
								step_name: row.step_name,
								order_count: row.info.order_count,
								pd_count: row.info.pd_count,
								package_out: row.info.package_out || 0,
								error_count: row.error.count
							});
							$("#stock_excel_list").append(renderedHTML);

							total_order_count += row.info.order_count;
							total_pd_count += row.info.pd_count;
							total_package_out += row.info.package_out || 0;
							total_error_count += row.error.count;
						});

						$("#stock_excel_list_tfoot tr th").eq(1).text(total_order_count);
						$("#stock_excel_list_tfoot tr th").eq(2).text(total_pd_count);
						$("#stock_excel_list_tfoot tr th").eq(3).text(total_package_out);
						$("#stock_excel_list_tfoot tr th").eq(4).text(total_error_count);
						
					}
				})
				.catch(function(error) {
					console.error(error);
				});
		}

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
				data: {
					"idx": idx,
					"sort": sort
				},
				type: "POST",
				dataType: "html",
				success: function(html) {
					$(".stock-list tr").removeClass('active');
					$("#tr_" + idx).addClass('active');
					$("#stock_excel_view").html(html);
				},
				error: function(request, status, error) {
					console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
					showAlert("Error", "에러", "alert2");
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

			if (mode == 'new') {
				window.open("/admin/sales/packing_list", "bb", "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
			} else {
				window.open("/ad/ajax/godo_order_print", "aa", "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
			}

		}

		return {
			list,
			view: view,
			excelSubmitCheck: excelSubmitCheck,
			godoOrderPrint: godoOrderPrint
		};

	})();

	// 전역 스코프 확인
	if (typeof window !== 'undefined') {
		window.stockExcel = stockExcel;
	}

	$(function() {

		<? if (!empty($_idx)) { ?>
			stockExcel.view('<?= $_idx ?>', 'qty');
		<? } ?>

		stockExcel.list();

	});
	//--> 
</script>