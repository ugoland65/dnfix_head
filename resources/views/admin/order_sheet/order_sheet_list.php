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

		<div id="list_new_wrap">
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
								<th>결제금액</th>
								<th>주문서폼</th>
								<th>관리</th>
								<th>등록일</th>
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
							?>
								<tr class="<?= $tr_class ?>">
									<td><input type="checkbox" name="order_sheet_idx[]" value="<?= $orderSheet['oo_idx'] ?>"></td>
									<td class="text-center"><?= $orderSheet['oo_idx'] ?></td>
									<td class="text-center"><?= $orderSheet['oo_import'] ?></td>
									<td><?= $orderSheet['oo_state_text'] ?></td>
									<td><a href="/ad/order/order_sheet/?idx=<?=$orderSheet['oo_idx']?>"><b><?= $orderSheet['oo_name'] ?></b></a></td>
									<td class="text-right"><?= number_format($orderSheet['oo_sum_price']) ?> <?= $orderSheet['oo_price_data']['currency'] ?? '' ?></td>
									<td class="text-right"><?= number_format($orderSheet['oo_price_kr']) ?> 원</td>
									<td class="text-center"><a href="/admin/order/sheet/list?oo_state=all&oo_form_idx=<?= $orderSheet['oo_form_idx'] ?>"><?= $orderSheet['oog_name'] ?></a></td>
									<td class="text-center">
										<button type="button" class="btnstyle1 btnstyle1-sm" onclick="orderSheet.osView(this, '<?= $orderSheet['oo_idx'] ?>','main')">상세내용</button>
										<button type="button" class="btnstyle1 btnstyle1-sm" onclick="location.href='/ad/order/order_sheet/?idx=<?= $orderSheet['oo_idx'] ?>'">주문상품</button>

										<?php /*
										<button type="button" class="btnstyle1 btnstyle1-sm" onclick="orderSheetMainList.payment('<?=$orderSheet['oo_idx']?>')">결제요청</button>
										*/ ?>

									</td>
									<td class="text-center">
										<?= date('y.m.d H:i', strtotime($orderSheet['created_at'])) ?><br>
										( <?= $orderSheet['created_name'] ?> )
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

		$("#searchBtn").on('click', function() {
			var params = getSearchParams();
			navigateWithParams(params);
		});

		$("#search_reset").click(function() {
			var url = "?";
			window.location.href = url;
		});

	});
</script>