<?php
$idx = $idx ?? '';
?>
<div id="contents_head">
	<h1>일일 재고관리 (엑셀) </h1>
	<div class="btn-group-wrap m-l-10">
		<form action="/admin/stock/upload_stock_excel" method="post" enctype="multipart/form-data" onsubmit="return stockExcel.excelSubmitCheck()">
			<table>
				<tr>
					<td>엑셀파일 : &nbsp;</td>
					<td><input name="userfile" id="excel_file" type="file"></td>
					<td><input type="submit" value=" 재고 엑셀 올리기 " class="btnstyle1 btnstyle1-success btnstyle1-sm"></td>
				</tr>
			</table>
		</form>
	</div>
	<div class="btn-group-wrap m-l-10">
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

	.stock-list tr.active td {
		background: #fff3cd !important;
		border: 1px solid #f5c16c !important;
	}
</style>

<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="division-layout-wrap">
			<div class="division-1">

				<div class="division-top">
					<div class="calendar-input">
						<input type="text" name="s_date" id="s_date" value="<?= date("Y-m-d") ?>">
					</div>
					<div>~</div>
					<div class="calendar-input">
						<input type="text" name="e_date" id="e_date" value="<?= date("Y-m-d") ?>">
					</div>
					<div>
						<button type="button" class="btnstyle1 btnstyle1-inverse3 btnstyle1-sm" onclick="stockExcel.list()">기간검색</button>
					</div>
				</div>

				<div class="division-body scroll-wrap">
					<table class="table-st1 stock-list">
						<thead>
							<tr>
								<th>번호</th>
								<th>모드</th>
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
								<th colspan="4">합계</th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>

			</div>
			<div class="division-2" id="stock_excel_view">

				<div class="division-top" id="sort_wrap" data-idx="" data-sort="qty">
					<div class="float-right">
						<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" disabled="disabled" onclick="stockExcel.excelDown()">엑셀 다운로드</button>
						<iframe id="excelDown_iframe" src="" style="display:none;"></iframe>
						<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm m-l-5" disabled="disabled" onclick="stockExcel.swindow()">새창열기</button>
						<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm m-l-20" disabled="disabled" onclick="stockExcel.del(this)"><i class="fas fa-minus-circle"></i> 삭제</button>
					</div>
				</div>

				<div class="division-body scroll-wrap">
				</div>

				<div class="division-bottom text-center">
					재고 처리 날짜 :
					<div class="calendar-input" style="display:inline-block;"><input type="text" name="stock_day" id="stock_day" value="<?= date("Y-m-d") ?>"></div>
					<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-lg m-l-10" disabled="disabled" onclick="stockExcel.dayStock(this)">재고 입출고 등록하기</button>
				</div>

			</div>
		</div>

	</div>
</div>

<div id="contents_bottom">
	<div class="pageing-wrap"></div>
</div>

<script type="text/template" id="stockExcelListTemplate">
	<tr id="tr_<%= uid %>" onclick="stockExcel.view('<%= uid %>', 'qty')" style="cursor:pointer;"
		data-step="<%= step %>" data-name="<%- data_name %>">
		<td class="text-center"><%= uid %></td>
		<td class="text-center"><%- source_type_name %></td>
		<td class="text-left"><% if (is_temp) { %><span style="color:#ff0000">[임시저장]</span> <% } %><b><%- file_name %></b></td>
		<td>
			등록 : <%- reg_time %>
			<% if (end_time && end_time !== '0000-00-00 00:00:00') { %><br>처리 : <%- end_time %><% } %>
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
			<%- reg_id %><br/>(<%- reg_name %>)
		</td>
	</tr>
</script>

<script type="text/javascript">
	var stockExcel = (function() {

		function list() {
			var s_date = $("#s_date").val();
			var e_date = $("#e_date").val();

			ajaxRequest("/admin/stock_history/list", {
					s_date,
					e_date
				}, {
					method: "GET"
				})
				.then(res => {
					if (res.status === "success") {
						$("#stock_excel_list").empty();
						const rows = res.data.productStockHistoryList || [];
						const template = _.template($('#stockExcelListTemplate').html());

						let total_order_count = 0;
						let total_pd_count = 0;
						let total_package_out = 0;
						let total_error_count = 0;

						rows.forEach(row => {
							const info = row.info || {};
							const error = row.error || {};
							const orderCount = Number(info.order_count || 0);
							const pdCount = Number(info.pd_count || 0);
							const packageOut = Number(info.package_out || 0);
							const errorCount = Number(error.count || 0);
							const isTemp = !!row.is_temp || String(row.step) === '1';
							const fileName = row.file_name || '';
							const sourceTypeName = row.source_type_name || (row.source_type === 'fetch' ? 'API' : '엑셀');

							var renderedHTML = template({
								uid: row.uid,
								file_name: fileName,
								reg_time: row.reg_time || '',
								end_time: row.end_time || '',
								reg_id: row.reg_id || '',
								reg_name: row.reg_name || '',
								step: row.step,
								is_temp: isTemp,
								order_count: orderCount,
								pd_count: pdCount,
								package_out: packageOut,
								error_count: errorCount,
								source_type_name: sourceTypeName,
								data_name: (isTemp ? '[임시저장] ' : '') + fileName + ' | 주문 : ' + orderCount + ' | 상품 : ' + pdCount
							});
							$("#stock_excel_list").append(renderedHTML);

							total_order_count += orderCount;
							total_pd_count += pdCount;
							total_package_out += packageOut;
							total_error_count += errorCount;
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
				url: "/admin/stock/stock_excel_view",
				data: {
					"idx": idx,
					"sort": sort
				},
				type: "GET",
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
				}
			});
		}

		function excelSubmitCheck() {
			var fileCheck = document.getElementById("excel_file");
			if (!fileCheck || !fileCheck.value) {
				alert("파일을 첨부해 주세요");
				return false;
			}
			return true;
		}

		function godoOrderPrint(mode) {
			if (mode == 'new') {
				window.open("/admin/sales/packing_list", "bb", "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
			} else {
				window.open("/ad/ajax/godo_order_print", "aa", "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
			}
		}

		return {
			list: list,
			view: view,
			excelSubmitCheck: excelSubmitCheck,
			godoOrderPrint: godoOrderPrint
		};

	})();

	if (typeof window !== 'undefined') {
		window.stockExcel = stockExcel;
	}

	$(function() {
		if ($(".calendar-input input").length) {
			$(".calendar-input input").datepicker(clareCalendar);
		}
		<?php if (!empty($idx)) { ?>
			stockExcel.view(<?= json_encode((string)$idx) ?>, 'qty');
		<?php } ?>
		stockExcel.list();
	});
</script>
