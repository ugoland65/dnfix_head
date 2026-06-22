<div id="contents_head">
	<h1>주문서 v.5</h1>
	<h3>상품발주 주문서 리스트</h3>
	<div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="orderSheet.osReg()" > 
			<i class="fas fa-plus-circle"></i>
			신규주문서 생성
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<?php
		$emptySplitSummary = [
			'domestic' => ['label' => '국내', 'count' => 0, 'pay_sum' => 0],
			'import' => ['label' => '수입', 'count' => 0, 'pay_sum' => 0],
		];
		$orderSheetSummary = $orderSheetSummary ?? [];
		$totalSummary = $orderSheetSummary['total'] ?? [
			'count' => 0,
			'pay_sum' => 0,
			'split' => $emptySplitSummary,
		];
		$stateSummaryMap = $orderSheetSummary['states'] ?? [
			'1' => ['label' => '작성중', 'count' => 0, 'pay_sum' => 0, 'split' => $emptySplitSummary],
			'2' => ['label' => '주문전송', 'count' => 0, 'pay_sum' => 0, 'split' => $emptySplitSummary],
			'4' => ['label' => '입금완료', 'count' => 0, 'pay_sum' => 0, 'split' => $emptySplitSummary],
			'5' => ['label' => '입고완료', 'count' => 0, 'pay_sum' => 0, 'split' => $emptySplitSummary],
		];
		?>
		<style>
			.order-sheet-summary { display:flex; flex-wrap:wrap; gap:10px; margin:0 0 12px 0; }
			.order-sheet-summary .summary-card { min-width:220px; padding:12px 14px; border:1px solid #d8dde5; border-radius:8px; background:#fff; }
			.order-sheet-summary .summary-card-total { border-color:#8aa8ff; background:#f3f7ff; }
			.order-sheet-summary .summary-clickable { cursor:pointer; transition:all .15s ease; }
			.order-sheet-summary .summary-clickable:hover { border-color:#a4b3cc; box-shadow:0 2px 8px rgba(16,24,40,.08); transform:translateY(-1px); }
			.order-sheet-summary .summary-title { font-size:12px; color:#667085; margin-bottom:6px; }
			.order-sheet-summary .summary-count { font-size:18px; font-weight:700; color:#101828; line-height:1.2; }
			.order-sheet-summary .summary-pay { margin-top:3px; font-size:13px; color:#344054; }
			.order-sheet-summary .summary-split { margin-top:8px; padding-top:8px; border-top:1px solid #eaecf0; }
			.order-sheet-summary .summary-split-row { display:flex; justify-content:space-between; font-size:12px; color:#475467; }
			.order-sheet-summary .summary-split-row + .summary-split-row { margin-top:4px; }
			.order-sheet-summary .summary-split-value { color:#1d2939; }
			.order-sheet-summary .summary-split-clickable { cursor:pointer; border-radius:4px; padding:2px 4px; margin:0 -4px; }
			.order-sheet-summary .summary-split-clickable:hover { background:#eef2f8; }
		</style>

		<div class="order-sheet-summary">
			<div class="summary-card summary-card-total summary-clickable" data-summary-filter="1" data-state="ing" data-import="all">
				<div class="summary-title">총 주문</div>
				<div class="summary-count"><?= number_format((int)($totalSummary['count'] ?? 0)) ?>건</div>
				<div class="summary-pay"><?= number_format((int)($totalSummary['pay_sum'] ?? 0)) ?>원</div>
				<div class="summary-split">
					<div class="summary-split-row summary-split-clickable" data-summary-filter="1" data-state="ing" data-import="국내">
						<span><?= $totalSummary['split']['domestic']['label'] ?? '국내' ?></span>
						<span class="summary-split-value"><?= number_format((int)($totalSummary['split']['domestic']['count'] ?? 0)) ?>건 / <?= number_format((int)($totalSummary['split']['domestic']['pay_sum'] ?? 0)) ?>원</span>
					</div>
					<div class="summary-split-row summary-split-clickable" data-summary-filter="1" data-state="ing" data-import="수입">
						<span><?= $totalSummary['split']['import']['label'] ?? '수입' ?></span>
						<span class="summary-split-value"><?= number_format((int)($totalSummary['split']['import']['count'] ?? 0)) ?>건 / <?= number_format((int)($totalSummary['split']['import']['pay_sum'] ?? 0)) ?>원</span>
					</div>
				</div>
			</div>
			<?php foreach ($stateSummaryMap as $summaryStateKey => $summaryState) { ?>
				<div class="summary-card summary-clickable" data-summary-filter="1" data-state="<?= $summaryStateKey ?>" data-import="all">
					<div class="summary-title"><?= $summaryState['label'] ?></div>
					<div class="summary-count"><?= number_format($summaryState['count']) ?>건</div>
					<div class="summary-pay"><?= number_format($summaryState['pay_sum']) ?>원</div>
					<div class="summary-split">
						<div class="summary-split-row summary-split-clickable" data-summary-filter="1" data-state="<?= $summaryStateKey ?>" data-import="국내">
							<span><?= $summaryState['split']['domestic']['label'] ?></span>
							<span class="summary-split-value"><?= number_format($summaryState['split']['domestic']['count']) ?>건 / <?= number_format($summaryState['split']['domestic']['pay_sum']) ?>원</span>
						</div>
						<div class="summary-split-row summary-split-clickable" data-summary-filter="1" data-state="<?= $summaryStateKey ?>" data-import="수입">
							<span><?= $summaryState['split']['import']['label'] ?></span>
							<span class="summary-split-value"><?= number_format($summaryState['split']['import']['count']) ?>건 / <?= number_format($summaryState['split']['import']['pay_sum']) ?>원</span>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

	
		<!-- 검색 영역 -->
		<div class="top-search-wrap">
			<ul class="count-wrap">
				<span class="count">Total : <b><?= number_format($pagination['total']) ?></b></span>
				<span class="m-l-10"><b><?= $pagination['current_page'] ?></b></span>
				<span>/</span>
				<span><b><?= $pagination['last_page'] ?></b> page</span>
			</ul>
			<ul class="m-l-10">
				<select name="oo_import" id="oo_import">
					<option value="all" <? if ($oo_import == 'all') echo "selected"; ?>>전체주문</option>
					<option value="수입" <? if ($oo_import == '수입') echo "selected"; ?>>수입주문</option>
					<option value="국내" <? if ($oo_import == '국내') echo "selected"; ?>>국내주문</option>
				</select>
			</ul>
			<ul>
				<select name="oo_state" id="oo_state">
					<option value="all" <? if ($oo_state == 'all') echo "selected"; ?>>주문상태</option>
					<option value="ing" <? if ($oo_state == 'ing') echo "selected"; ?>>진행중</option>
					<option value="1" <? if ($oo_state == '1') echo "selected"; ?>>작성중</option>
					<option value="2" <? if ($oo_state == '2') echo "selected"; ?>>주문전송</option>
					<option value="4" <? if ($oo_state == '4') echo "selected"; ?>>입금완료</option>
					<option value="5" <? if ($oo_state == '5') echo "selected"; ?>>입고완료</option>
					<option value="7" <? if ($oo_state == '7') echo "selected"; ?>>주문종료</option>
				</select>
			</ul>
			<ul>
				<select name="oo_form_idx" id="oo_form_idx">
					<option value="">주문서폼</option>
					<?php foreach ($onaOrderGroupList as $onaOrderGroup) { ?>
						<option value="<?= $onaOrderGroup['oog_idx'] ?>" <? if ($oo_form_idx == $onaOrderGroup['oog_idx']) echo "selected"; ?>>
							<?= $onaOrderGroup['oog_name'] ?>
						</option>
					<?php } ?>
				</select>
			</ul>
			<ul class="m-l-10">
				<input type="text" name="search_value" id="search_value" placeholder="검색어" value="<?= $search_value ?>">
			</ul>
			<ul>
				<button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm" id="searchBtn">
					<i class="fas fa-search"></i> 검색
				</button>
				<button type="button" class="btnstyle1 btnstyle1-sm" id="search_reset">
					<i class="far fa-trash-alt"></i> 초기화
				</button>
			</ul>
		</div>

		<div id="list_new_wrap" style="max-height: calc(100% - 180px);">
			<div class="table-wrap5">
				<div class="scroll-wrap">

					<table class="table-st1">
						<thead>
							<tr class="list">
								<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
								<th class="list-idx">고유번호</th>
								<th>수입구분</th>
								<th>상태</th>
								<th>이름</th>
								<th>주문금액</th>
								<th>예상금액</th>
								<th>결제금액</th>
								<th>주문서폼</th>
								<th>관리</th>
								<th>등록일</th>
								<th>입고처리</th>
								<th>댓글</th>
								<th>메모</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($orderSheetList as $orderSheet) {

								if ($orderSheet['oo_state'] == '7' ) {
									$tr_class = 'status_end2';
								}elseif( $orderSheet['oo_state'] == '4' ) {
									$tr_class = 'status_bl';
									/*
									}elseif( $row['cs_status'] == '요청' ){
											$tr_class = 'status_bl';
									*/
								} else {
									$tr_class = '';
								}

								$orderAmount = (float)($orderSheet['oo_sum_price'] ?? 0);
								$orderAmountCurrency = trim((string)($orderSheet['oo_price_data']['currency'] ?? ''));

								$expectedAmountText = '-';
								if ((string)($orderSheet['oo_import'] ?? '') === '국내') {
									$expectedAmountText = number_format($orderAmount);
									if ($orderAmountCurrency !== '') {
										$expectedAmountText .= ' ' . $orderAmountCurrency;
									}
								} else {
									$prdExchangeRate = (float)($orderSheet['oo_prd_exchange_rate'] ?? 0);
									$prdCurrency = trim((string)($orderSheet['oo_prd_currency'] ?? ''));
									$isYenCurrency = in_array($prdCurrency, ['엔', 'JPY', 'jpy'], true);
									$expectedAmount = $prdExchangeRate > 0
										? ($isYenCurrency
											? round($orderAmount * ($prdExchangeRate / 100), 0)
											: round($orderAmount * $prdExchangeRate, 0))
										: 0;
									$expectedAmountText = number_format($expectedAmount) . ' 원';
								}
							?>
								<tr class="<?= $tr_class ?>">
									<td><input type="checkbox" name="order_sheet_idx[]" value="<?= $orderSheet['oo_idx'] ?>"></td>
									<td class="text-center"><?= $orderSheet['oo_idx'] ?></td>
									<td class="text-center"><?= $orderSheet['oo_import'] ?></td>
									<td><?= $orderSheet['oo_state_text'] ?></td>
									<td><a href="/admin/order/sheet?idx=<?= $orderSheet['oo_idx'] ?>"><b><?= $orderSheet['oo_name'] ?></b></a></td>
									<td class="text-right"><?= number_format($orderAmount) ?> <?= $orderAmountCurrency ?></td>
									<td class="text-right"><?= $expectedAmountText ?></td>
									<td class="text-right"><?= ((float)($orderSheet['oo_price_kr'] ?? 0) > 0) ? number_format((float)$orderSheet['oo_price_kr']) . ' 원' : '-' ?></td>
									<td class="text-center"><a href="/admin/order/sheet/list?oo_state=all&oo_form_idx=<?= $orderSheet['oo_form_idx'] ?>"><?= $orderSheet['oog_name'] ?></a></td>
									<td class="text-center">
										<button type="button" class="btnstyle1 btnstyle1-sm" onclick="orderSheet.osView(this, '<?= $orderSheet['oo_idx'] ?>','main')">상세내용</button>
										<button type="button" class="btnstyle1 btnstyle1-sm" onclick="location.href='/admin/order/sheet?idx=<?= $orderSheet['oo_idx'] ?>'">주문상품</button>

										<?php /*
										<button type="button" class="btnstyle1 btnstyle1-sm" onclick="orderSheetMainList.payment('<?=$orderSheet['oo_idx']?>')">결제요청</button>
										*/ ?>

									</td>
									<td class="text-center">
										<?= date('y.m.d H:i', strtotime($orderSheet['created_at'])) ?><br>
										( <?= $orderSheet['created_name'] ?> )
									</td>
									<td class="text-center">
										<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="window.open('/admin/order/sheet/stock?idx=<?= $orderSheet['oo_idx'] ?>', '_blank')">검수+입고처리</button>
									</td>
									<td class="text-left">
										<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-sm" onclick="footerGlobal.comment('orderSheet','<?=$orderSheet['oo_idx']?>')" >
											댓글
											<?php if( $orderSheet['comment_count'] > 0 ) { ?> : <b><?=$orderSheet['comment_count']?></b><?php } ?>
										</button>
									</td>
									<td class="text-left"><?= $orderSheet['oo_memo'] ?></td>

								</tr>
							<?php } ?>
						</tbody>
					</table>
	
				</div>
			</div>
		</div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"><?= $paginationHtml ?></div>
	<div class="m-l-20">

	</div>
</div>
<script src="/admin2/js/order_sheet.js?ver=<?=time()?>"></script>
<script>
	// 검색 파라미터 수집 공통 함수
	function getSearchParams(additionalParams) {
		var params = {};

		// 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
		var fields = {
			'oo_import': $("#oo_import").val(),
			'oo_state': $("#oo_state").val(),
			'oo_form_idx': $("#oo_form_idx").val(),
			'search_value': $("#search_value").val(),
		};

		// 추가 파라미터가 있으면 병합
		if (additionalParams) {
			fields = Object.assign(fields, additionalParams);
		}

		// 유효한 값만 params에 추가
		for (var key in fields) {
			if (fields[key] !== undefined && fields[key] !== null && fields[key] !== '') {
				params[key] = fields[key];
			}
		}

		return params;
	}

	// 검색 파라미터로 페이지 이동
	function navigateWithParams(params) {
		// URL 쿼리 문자열 생성
		var queryString = Object.keys(params)
			.map(function(key) {
				return key + '=' + encodeURIComponent(params[key]);
			})
			.join('&');

		// 페이지 이동
		location.href = '/admin/order/sheet/list' + (queryString ? '?' + queryString : '');
	}

	$(function() {
		$("[data-summary-filter='1']").on("click", function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $target = $(this);
			var state = $target.data("state");
			var importType = $target.data("import");

			var params = getSearchParams({
				oo_state: state || 'ing',
				oo_import: importType || 'all',
			});

			navigateWithParams(params);
		});

		$("#searchBtn").on('click', function() {
			var params = getSearchParams();
			navigateWithParams(params);
		});

		$("#search_value").on('keydown', function(e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				$("#searchBtn").trigger('click');
			}
		});

		$("#search_reset").click(function() {
			var url = "?";
			window.location.href = url;
		});

	});
</script>