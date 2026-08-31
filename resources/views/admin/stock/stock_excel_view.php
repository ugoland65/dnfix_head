<?php
$h = static function ($value): string {
	return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$idx = (int)($idx ?? 0);
$sort = $sort ?? 'qty';
$history = is_array($history ?? null) ? $history : [];
$metaData = is_array($meta_data ?? null) ? $meta_data : [];
$errorList = is_array($error_list ?? null) ? $error_list : [];
$rows = is_array($rows ?? null) ? $rows : [];
$prdCount = (int)($prd_count ?? 0);
$noticeShortageCount = (int)($notice_shortage_count ?? 0);
$noticeSoldoutCount = (int)($notice_soldout_count ?? 0);
$errorCount = (int)($error_count ?? count($errorList));
$totalPackageOut = $total_package_out ?? 0;
$totalOneQty = $total_one_qty ?? 0;
$totalSetQty = $total_set_qty ?? 0;
$isCompleted = !empty($is_completed);
$isTemp = !empty($is_temp);

$filtersModeMap = [
	'p' => '결제완료',
	'p2' => '결제완료 - 출고일 조정',
	'g' => '준비중 전체',
	'g1' => '상품준비중',
	'g5' => '배송준비중-핸디',
	'g6' => '배송준비중-공급사 주문대기',
	'g7' => '배송준비중- CS 처리 대기',
	'g8' => '배송준비중-CS 처리중',
	'g9' => '배송준비중-공급사 주문완료',
	'd' => '배송중',
	'ds' => '배송완료',
	's1' => '구매확정',
];
$filterMode = $metaData['filters']['mode'] ?? '';
$filterModeName = $filtersModeMap[$filterMode] ?? '';

$noticeHtml = [];
if ($errorCount > 0) {
	$noticeHtml[] = '- 에러항목이 ( <b>' . $errorCount . '</b> )건 있습니다.';
}
if ($noticeShortageCount > 0) {
	$noticeHtml[] = '- 재고 부족상품이 ( <b>' . $noticeShortageCount . '</b> )개 있습니다.';
}
if ($noticeSoldoutCount > 0) {
	$noticeHtml[] = '- 재고 등록후 품절될 상품이 ( <b>' . $noticeSoldoutCount . '</b> )개 있습니다.';
}
?>
<style type="text/css">
	.brand-name {
		color: #296abc;
	}

	.prd-name {
		width: 450px;
		text-overflow: ellipsis;
		white-space: nowrap;
		overflow: hidden;
	}

	#sh_name {
		height: 30px;
		line-height: 30px;
	}

	.copy-cell-wrap {
		display: inline-flex;
		align-items: center;
		gap: 4px;
		max-width: 100%;
	}

	.copy-target {
		min-width: 0;
	}

	.copy-btn {
		padding: 0;
		border: 0;
		background: transparent;
		color: #888;
		cursor: pointer;
		line-height: 1;
		display: inline-flex;
		align-items: center;
	}

	.copy-btn:hover {
		color: #296abc;
	}

	.copy-btn.is-copied {
		color: #1f9d55;
	}

	.copy-btn.is-copied .copy-icon {
		display: none;
	}

	.copy-btn.is-copied::after {
		content: '✓';
		font-size: 12px;
		font-weight: 700;
		line-height: 1;
	}

	.order_num_list {
		display: none;
	}

	.order_num_list .table-st1 {
		border-top: 1px solid #b4b4b4;
		border-left: 1px solid #b4b4b4;
		border-bottom: 1px solid #b4b4b4;
	}

	.order_num_list .table-st1 thead tr,
	.order_num_list .table-st1 tfoot>tr {
		position: static;
	}
</style>
<div class="division-top" id="sort_wrap" data-idx="<?= $h($idx) ?>" data-sort="<?= $h($sort) ?>">
	<span id="sh_name">
		[<?= $h($history['uid'] ?? $idx) ?>] <b><?= $h($history['file_name'] ?? '') ?></b> |
	</span>

	<?php if (($history['source_type'] ?? '') === 'fetch') { ?>
		<span class="text-red">API 등록</span>
		Filters :
		<?= $h($filterModeName) ?> (<?= $h($filterMode) ?>)
		<?= $h($metaData['filters']['start_date'] ?? '') ?> ~
		<?= $h($metaData['filters']['end_date'] ?? '') ?>
	<?php } ?>
	<div class="float-right">
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" disabled="disabled">엑셀 다운로드</button>
		<iframe id="excelDown_iframe" src="" style="display:none;"></iframe>
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm m-l-5" onclick="stockExcelView.swindow()">새창열기</button>
		<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm m-l-20" onclick="stockExcelView.del(this)"><i class="fas fa-minus-circle"></i> 삭제</button>
		<?php if ($isCompleted) { ?>
			<button type="button" class="btnstyle1 btnstyle1-warning btnstyle1-sm m-l-10" onclick="stockExcelView.revert(this)">되돌리기</button>
		<?php } ?>
	</div>
</div>

<div class="division-body scroll-wrap">
	<form id="form2">
		<input type="hidden" name="stock_history_idx" value="<?= $h($idx) ?>">

		<table class="table-st1">
			<thead>
				<tr>
					<th>재고<br>코드</th>
					<th>상품명</th>
					<th>주문번호</th>
					<th>패킹<br>제거</th>
					<th>단일<br>상품</th>
					<th>세트<br>상품</th>
					<th>출고<br>총합</th>
					<th>현재<br>재고</th>
					<th>남는<br>재고</th>
					<th></th>
					<th style="width:150px;"></th>
				</tr>
			</thead>

			<?php if ($errorCount > 0) { ?>
				<tbody>
					<tr>
						<td colspan="100%">
							<div>※ 에러항목</div>
							<div>
								<?php foreach ($errorList as $errorItem) { ?>
									<ul><?= $h($errorItem) ?></ul>
								<?php } ?>
							</div>
						</td>
					</tr>
				</tbody>
			<?php } ?>

			<tbody>
				<?php foreach ($rows as $row) { ?>
					<?php
					$trClass = '';
					if (($row['mode'] ?? '') === 'stock_over') {
						$trClass = 'red';
					} elseif (($row['mode'] ?? '') === 'stock_zero') {
						$trClass = 'green';
					}
					$orderNums = is_array($row['order_num'] ?? null) ? $row['order_num'] : [];
					$cdIdx = $row['cd_idx'] ?? '';
					$prdName = (string)($row['prd_name'] ?? '');
					?>
					<input type="hidden" name="stock_key[]" value="<?= $h($row['ps_idx'] ?? '') ?>">
					<input type="hidden" name="stock_mode[]" class="stock-mode-value" value="minus">
					<input type="hidden" name="stock_kind[]" value="판매 (엑셀)">

					<tr class="<?= $h($trClass) ?>">
						<td class="text-center"><?= $h($row['ps_idx'] ?? '') ?></td>
						<td class="text-left">
							<?php if (($row['mode'] ?? '') === 'stock_over') { ?>
								<div style="color:#ff0000;" class="m-b-5">※ 재고 부족</div>
							<?php } ?>
							<?php if (($row['mode'] ?? '') === 'stock_zero') { ?>
								<div class="m-b-5">※ 재고 등록 후 품절</div>
							<?php } ?>
							<p class="prd-name">
								<button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?= $h($cdIdx) ?>','info');">보기</button>
								<?php if (!empty($row['brand_name'])) { ?>
									<span class="brand-name">[<?= $h($row['brand_name']) ?>] </span>
								<?php } ?>
								<span class="copy-cell-wrap">
									<span class="copy-target"><?= $h($prdName) ?></span>
									<?php if ($prdName !== '') { ?>
										<button type="button" class="copy-btn" title="복사" aria-label="상품명 복사" onclick="copyCellText(this)">
											<svg class="copy-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
												<rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"></rect>
												<path d="M5 15V7C5 5.9 5.9 5 7 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
											</svg>
										</button>
									<?php } ?>
								</span>
							</p>
						</td>
						<td class="text-center">
							<button type="button" class="btnstyle1 btnstyle1-xs order-toggle">▼</button>
						</td>
						<td class="text-center">
							<?php if ((int)($row['packageOut'] ?? 0) > 0) { ?><?= $h($row['packageOut']) ?><?php } ?>
						</td>
						<td class="text-center">
							<?php if ((float)($row['one_qty'] ?? 0) > 0) { ?><?= $h($row['one_qty']) ?><?php } ?>
						</td>
						<td class="text-center">
							<?php if ((float)($row['set_qty'] ?? 0) > 0) { ?><?= $h($row['set_qty']) ?><?php } ?>
						</td>
						<td><input type="text" name="stock_qry[]" style="width:40px; font-size:15px; font-weight:bold; color:#d00000;" placeholder="수량" value="<?= $h($row['qty'] ?? 0) ?>" /></td>
						<td class="text-center">
							<?php if (($row['ps_stock_object'] ?? '') === 'N') { ?>
								∞
							<?php } else { ?>
								<?= $h($row['ps_stock'] ?? 0) ?>
							<?php } ?>
						</td>
						<td class="text-center">
							<?php if (($row['ps_stock_object'] ?? '') === 'N') { ?>
								∞
							<?php } else { ?>
								<?= $h($row['stock_sum'] ?? 0) ?>
							<?php } ?>
						</td>
						<td class="stock-mode-text">출고</td>
						<td><input type="text" name="stock_memo[]" class="stock-memo" value="일일 재고관리" /></td>
					</tr>
					<tr class="order_num_list">
						<td colspan="100%">
							<table class="table-st1">
								<thead>
									<tr>
										<th>주문번호</th>
										<th>주문수량</th>
										<th>C/S 요청</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($orderNums as $orderItem) { ?>
										<?php $orderNo = $orderItem['num'] ?? ''; ?>
										<tr>
											<td>
												<a href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= $h($orderNo) ?>" target="_blank"><?= $h($orderNo) ?></a>
											</td>
											<td class="text-center"><?= $h($orderItem['qty'] ?? '') ?></td>
											<td>
												<button type="button" class="btnstyle1 btnstyle1-xs"
													data-order-no="<?= $h($orderNo) ?>"
													onclick="csCreate(this);">C/S 요청</button>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3" class="text-center">합계 - 상품 : <b><?= $h($prdCount) ?></b>개</th>
					<th><?= $h($totalPackageOut) ?></th>
					<th><?= $h($totalOneQty) ?></th>
					<th><?= $h($totalSetQty) ?></th>
					<th colspan="100%"></th>
				</tr>
			</tfoot>
		</table>
	</form>
</div>

<div class="division-bottom text-center">
	<?php if ($isCompleted) { ?>
		<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-lg" disabled>등록 완료</button>
		<button type="button" class="btnstyle1 btnstyle1-warning btnstyle1-lg m-l-10" onclick="stockExcelView.revert(this)">되돌리기</button>
	<?php } else { ?>
		재고 처리 날짜 :
		<div class="calendar-input" style="display:inline-block;"><input type="text" name="stock_day" id="stock_day" value="<?= date('Y-m-d') ?>"></div>
		<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-lg m-l-10" onclick="stockExcelView.dayStock(this)">재고 입출고 등록하기</button>
	<?php } ?>
</div>

<script>
	function csCreate(button) {
		var orderNo = $(button).data('order-no');
		openDialog("/admin/cs/cs_create", {
			mode: 'create',
			apiMode: 'none',
			category: '일일재고관리',
			orderNo: orderNo
		}, "C/S 생성", "800px");
	}

	function copyTextWithFallback(text) {
		var textarea = document.createElement('textarea');
		textarea.value = text;
		textarea.setAttribute('readonly', '');
		textarea.style.position = 'fixed';
		textarea.style.top = '-9999px';
		textarea.style.left = '-9999px';
		document.body.appendChild(textarea);
		textarea.focus();
		textarea.select();
		textarea.setSelectionRange(0, textarea.value.length);
		var copied = false;
		try {
			copied = document.execCommand('copy');
		} catch (e) {
			copied = false;
		}
		document.body.removeChild(textarea);
		return copied;
	}

	function setCopyButtonFeedback(button) {
		if (!button) return;
		button.classList.add('is-copied');
		button.title = '복사됨';
		setTimeout(function() {
			button.classList.remove('is-copied');
			button.title = '복사';
		}, 1200);
	}

	function copyCellText(button) {
		var textNode = button && button.parentNode ? button.parentNode.querySelector('.copy-target') : null;
		var text = textNode ? (textNode.textContent || '').trim() : '';
		if (text === '') return;
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(text).then(function() {
				setCopyButtonFeedback(button);
			}).catch(function() {
				if (copyTextWithFallback(text)) {
					setCopyButtonFeedback(button);
				}
			});
			return;
		}
		if (copyTextWithFallback(text)) {
			setCopyButtonFeedback(button);
		}
	}

	var stockExcelView = (function() {
		var stockHistoryIdx = <?= json_encode((string)$idx) ?>;
		var isCompleted = <?= $isCompleted ? 'true' : 'false' ?>;

		return {
			swindow: function() {
				window.open(
					"/admin/sales/picking_list/" + encodeURIComponent(stockHistoryIdx),
					"excelDown",
					"width=1000,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no"
				);
			},
			del: function(obj) {
				if (isCompleted) {
					showAlert("Error", "완료된건은 삭제 불가합니다.", "alert2");
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
							action: function() {
								$(obj).attr('disabled', true);
								$.ajax({
									url: "/admin/stock/delete_day_stock",
									data: { idx: stockHistoryIdx },
									type: "POST",
									dataType: "json",
									success: function(res) {
										if (res.status === "success" || res.success == true) {
											alert("삭제되었습니다.");
											location.href = "/admin/stock/stock_excel";
										} else {
											showAlert("Error", res.msg || res.message, "alert2");
											return false;
										}
									},
									error: function(request) {
										var msg = "에러";
										try {
											var res = JSON.parse(request.responseText || "{}");
											msg = res.message || res.msg || msg;
										} catch (e) {}
										showAlert("Error", msg, "alert2");
										return false;
									}
								});
							}
						},
						cencle: {
							text: '취소',
							action: function() {}
						}
					}
				});
			},
			revert: function(obj) {
				if (!isCompleted) {
					showAlert("Error", "등록 완료된 건만 되돌릴 수 있습니다.", "alert2");
					return false;
				}

				$.confirm({
					icon: 'fas fa-exclamation-triangle',
					title: '재고를 되돌리시겠습니까?',
					content: '차감된 재고 수량을 복구하고, 다시 입출고 등록 가능한 상태로 되돌립니다.',
					autoClose: 'cencle|9000',
					type: 'orange',
					typeAnimated: true,
					closeIcon: true,
					buttons: {
						somethingElse: {
							text: '되돌리기',
							btnClass: 'btn-orange',
							action: function() {
								$(obj).attr('disabled', true);
								$.ajax({
									url: "/admin/stock/revert_day_stock",
									data: { idx: stockHistoryIdx },
									type: "POST",
									dataType: "json",
									success: function(res) {
										if (res.status === "success" || res.success == true) {
											alert(res.message || "재고 수량을 되돌렸습니다.");
											if (typeof stockExcel !== "undefined" && stockHistoryIdx) {
												stockExcel.view(stockHistoryIdx, "qty");
												stockExcel.list();
											}
											return;
										}
										showAlert("Error", res.msg || res.message, "alert2");
										$(obj).attr("disabled", false);
									},
									error: function(request) {
										var msg = "에러";
										try {
											var res = JSON.parse(request.responseText || "{}");
											msg = res.message || res.msg || msg;
										} catch (e) {}
										showAlert("Error", msg, "alert2");
										$(obj).attr("disabled", false);
									}
								});
							}
						},
						cencle: {
							text: '취소',
							action: function() {}
						}
					}
				});
			},
			dayStock: function(obj) {
				var formData = $("#form2").serializeArray();
				formData.push({
					name: "stock_day",
					value: $("#stock_day").val()
				});

				$(obj).attr('disabled', true);
				$.ajax({
					url: "/admin/stock/register_day_stock",
					data: formData,
					type: "POST",
					dataType: "json",
					success: function(res) {
						if (res.status === "success" || res.success == true) {
							alert("완료");
							if (typeof stockExcel !== "undefined" && stockHistoryIdx) {
								stockExcel.view(stockHistoryIdx, "qty");
								stockExcel.list();
							}
							return;
						}
						showAlert("Error", res.msg || res.message, "alert2");
						if (typeof stockExcel !== "undefined" && stockHistoryIdx) {
							stockExcel.view(stockHistoryIdx, "qty");
						} else {
							$(obj).attr("disabled", false);
						}
					},
					error: function(request) {
						var msg = "에러";
						try {
							var res = JSON.parse(request.responseText || "{}");
							msg = res.message || res.msg || msg;
						} catch (e) {}
						showAlert("Error", msg, "alert2");
						if (typeof stockExcel !== "undefined" && stockHistoryIdx) {
							stockExcel.view(stockHistoryIdx, "qty");
						} else {
							$(obj).attr("disabled", false);
						}
					}
				});
			}
		};
	})();

	$(function() {
		if ($(".calendar-input input").length) {
			$(".calendar-input input").datepicker(clareCalendar);
		}

		$(document)
			.off('click.stockExcelOrderToggle', '.order-toggle')
			.on('click.stockExcelOrderToggle', '.order-toggle', function() {
				var $targetRow = $(this).closest('tr').next('.order_num_list');
				if ($targetRow.length === 0) {
					return;
				}
				$targetRow.toggle();
				$(this).text($targetRow.is(':visible') ? '▲' : '▼');
			});

		<?php if ($isTemp && !empty($noticeHtml)) { ?>
			$.confirm({
				boxWidth: "500px",
				useBootstrap: false,
				icon: 'fas fa-exclamation-triangle',
				title: '확인해주세요.',
				content: <?= json_encode(implode('<br>', $noticeHtml), JSON_UNESCAPED_UNICODE) ?>,
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					cencle: {
						text: '확인완료',
						action: function() {}
					}
				}
			});
		<?php } ?>
	});
</script>
