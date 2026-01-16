<style>
	.prd-name {
		width: 300px;
		white-space: normal !important;
	}
</style>
<div id="contents_head">
	<h1>공급사 상품관리</h1>
	<h3>인트라넷에 등록된 공급사 상품입니다.</h3>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div id="list_new_wrap">

			<div class="table-top">
				<ul class="total">
					Total : <span><b><?= number_format($pagination['total']) ?></b></span> &nbsp; | &nbsp;
					<span><b><?= $pagination['current_page'] ?></b></span> / <?= $pagination['last_page'] ?> page
				</ul>
				<ul>
					<select name="s_partner" id="s_partner">
						<option value="">전체 공급사</option>
						<?
						foreach ($partnerForSelect as $partner) {
						?>
							<option value="<?= $partner['idx'] ?>" <? if ($partner['idx'] == ($_s_partner ?? '')) echo "selected"; ?>><?= $partner['name'] ?></option>
						<? } ?>
					</select>
				</ul>
				<ul>
					<select name="s_godo_match" id="s_godo_match">
						<option value="">고도몰 매칭</option>
						<option value="matched" <? if ($s_godo_match == 'matched') echo "selected"; ?>>매칭완료</option>
						<option value="unmatched" <? if ($s_godo_match == 'unmatched') echo "selected"; ?>>매칭안됨</option>
					</select>
				</ul>
				<ul>
					<select name="s_supplier_match" id="s_supplier_match">
						<option value="">공급사 매칭</option>
						<option value="matched" <? if ($s_supplier_match == 'matched') echo "selected"; ?>>매칭완료</option>
						<option value="unmatched" <? if ($s_supplier_match == 'unmatched') echo "selected"; ?>>매칭안됨</option>
					</select>
				</ul>
				<ul class="">
					<select name="s_brand" id="s_brand" class="dn-select2">
						<option value="">브랜드</option>
						<?
						foreach ($brandForSelect as $brand) {
						?>
							<option value="<?= $brand['BD_IDX'] ?>" <? if ($brand['BD_IDX'] == ($s_brand ?? '')) echo "selected"; ?>><?= $brand['BD_NAME'] ?></option>
						<? } ?>
					</select>
				</ul>
				<ul>
					<input type="text" name="s_keyword" id="s_keyword" placeholder="상품명 검색" value="<?= $s_keyword ?? '' ?>">
				</ul>
				<ul>
					<button type="button" id="searchBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm">
						<i class="fas fa-search"></i> 검색
					</button>
				</ul>
				<ul class="right">
					<select name="sort_kind" id="sort_kind">
						<option value="idx" <? if ($sort_mode == "idx") echo "selected"; ?>>등록순</option>
						<option value="updated_at" <? if ($sort_mode == "updated_at") echo "selected"; ?>>수정순</option>
					</select>
				</ul>
			</div>

			<div class="table-wrap5 m-t-5">
				<div class="scroll-wrap">

					<table class="table-st1">
						<thead>
							<tr>
								<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
								<th class="list-idx">고유번호</th>
								<th class="">상태</th>
								<th class="" style="width:80px;">이미지</th>
								<th class="" style="width:50px;">분류</th>
								<th class="prd-name">이름</th>
								<th class="">브랜드</th>
								<th class="">공급사</th>
								<th class="">코드</th>
								<th class="">고도몰<br>상품코드</th>
								<th class="">고도몰<br>판매가</th>
								<th class="">상품원가<br>/주문가</th>
								<th class="">마진</th>
								<th class="">마진율</th>
								<th class="">공급사<br>이미지</th>
								<th class="prd-name">공급사<br>상품명</th>
								<th class="">공급매칭</th>
								<th class="">공급<br>상품코드</th>
								<th class="">공급<br>사이트</th>
								<th class="">공급 2차</th>
								<th class="" style="width:80px;">매칭취소</th>
								<th class="">수정일<br>등록일</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($productPartnerList as $item) {
							?>
								<tr>
									<td><input type="checkbox" name="check_idx[]" value="<?= $item['idx'] ?>"></td>
									<td class="text-center"><?= $item['idx'] ?></td>
									<td class="text-center"><?= $item['status'] ?></td>
									<td>
										<img src="<?= $item['img_src'] ?>" style="height:70px; border:1px solid #eee !important;">
									</td>
									<td class="text-center"><?= $koedge_prd_kind_name[$item['kind']] ?? "미지정" ?></td>
									<td class="prd-name">
										<a href="javascript:prdProviderQuick(<?= $item['idx'] ?>);"><?= $item['name'] ?></a>
										<? if (!empty($item['memo'])) { ?>
											<br><span class="prd-memo"><?= $item['memo'] ?></span>
										<? } ?>
									</td>
									<td class="text-center"><?= $item['brand_name'] ?></td>
									<td class="text-center"><a href="/ad/prd/prd_provider/?s_partner=<?= $item['partner_idx'] ?>"><?= $item['partner_name'] ?></a></td>
									<td class="text-center"><?= $item['code'] ?></td>
									<td class="text-center">
										<div style="font-size: 12px;">
											#<?= $item['godo_goodsNo'] ?>
										</div>
										<div class="m-t-3">
											<button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall(<?= $item['godo_goodsNo'] ?>);">쑈당몰 상품보기</button>
										</div>
										<div class="m-t-5">
											<button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin(<?= $item['godo_goodsNo'] ?>);">관리자 상품보기</button>
										</div>
									</td>
									<td class="text-right"><?= number_format($item['sale_price']) ?></td>
									<td class="text-right">
										<?= number_format($item['cost_price']) ?>
										<br><b><?= number_format($item['order_price']) ?></b>
									</td>
									<td class="text-right">
										<?php
										$salePrice = (float)($item['sale_price'] ?? 0);
										$costPrice = (float)($item['cost_price'] ?? 0);
										$orderPrice = (float)($item['order_price'] ?? 0);
										$margin = $salePrice - $costPrice;
										$margin2 = $salePrice - $orderPrice;

										if ($salePrice > 0 && $costPrice > 0) {
											echo number_format($margin);
										} else {
											echo '-';
										}
										if ($salePrice > 0 && $orderPrice > 0) {
											echo "<br><b>" . number_format($margin2) . "</b>";
										} else {
											echo '-';
										}
										?>
									</td>
									<td class="text-right">
										<?php
										if ($salePrice > 0 && $costPrice > 0) {
											$marginRate = ($margin / $salePrice) * 100;
											echo number_format($marginRate, 1) . '%';
										} else {
											echo '-';
										}
										?>
										<?php
										if ($salePrice > 0 && $orderPrice > 0) {
											$marginRate2 = ($margin2 / $salePrice) * 100;
											echo "<br><b>" . number_format($marginRate2, 1) . '%</b>';
										} else {
											echo '-';
										}
										?>
									</td>

									<td class="text-center">
										<?php
										if (!empty($item['supplier_img_src'])) {
										?>
											<img src="<?= $item['supplier_img_src'] ?>" style="height:70px; border:1px solid #eee !important;">
										<?php } else { ?>
											-
										<?php } ?>
									</td>

									<td class="prd-name text-left">
										<a href="javascript:goSupplierProductEdit('<?= $item['supplier_prd_idx'] ?>');"><?= $item['name_p'] ?? '-' ?></a>
										<?php
										if (!empty($item['matching_option'])) {
										?>
											<br>( 옵션 : <?= $item['matching_option'] ?? '-' ?>)
										<?php
										}
										?>
									</td>

									<!-- 공급매칭 -->
									<td class="text-center">
										<?php
										if (!empty($item['supplier_prd_idx'])) {
										?>
											<?= $item['supplier_prd_idx'] ?>
										<?php } else { ?>
											비매칭
										<?php } ?>
									</td>

									<td class="text-center">
										<?php
											if (!empty($item['supplier_prd_idx'])) {
										?>
											<div style="font-size: 12px;">
												#<?= $item['supplier_prd_pk'] ?>
											</div>
											<div class="m-t-3">
												<button type="button" class="btnstyle1 btnstyle1-xs"
													onclick="goSupplierProduct('<?= $item['supplier_site'] ?>', '<?= $item['supplier_prd_pk'] ?>');">공급사 사이트</button>
											</div>
										<?php } else { ?>
											-
										<?php } ?>
									</td>
									<td class="text-center"><?= $item['supplier_site'] ?? '-' ?></td>
									<td class="text-center"><?= $item['supplier_2nd_name'] ?? '-' ?></td>
									<td class="text-center">
										<?php
											if (!empty($item['supplier_prd_idx'])) {
										?>
											<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs cancel-match-btn"
												data-db1-idx="<?= $item['idx'] ?>"
												data-db2-idx="<?= $item['supplier_prd_idx'] ?>">매칭취소</button>
										<?php } else { ?>
											-
										<?php } ?>
									</td>
									<td class="text-center">
										<?= date('Y.m.d H:i', strtotime($item['updated_at'])) ?? '-' ?><br>
										<?= date('Y.m.d H:i', strtotime($item['created_at'])) ?? '-' ?>
									</td>
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
	<div class="pageing-wrap"><?= $paginationHtml ?></div>
</div>
<script type="text/javascript">
	const prdProvider = (function() {

		const API_ENDPOINT = {
			cancelMatchProviderProduct: '/admin/provider_product/proc/cancel_match_provider_product',
		};

		/**
		 * 공급사 상품 매칭 취소
		 * @param {string} db1_idx - 상품 고유번호
		 * @param {string} db2_idx - 공급사 상품 고유번호
		 */
		function cancelMatchProviderProduct(db1_idx, db2_idx) {

			ajaxRequest(API_ENDPOINT.cancelMatchProviderProduct, {
					db1_idx,
					db2_idx,
				})
				.then(res => {
					if (res.status === 'success') {
						alert(res.message);
						location.reload();
					} else {
						alert(res.message);
					}
				})
				.catch(error => {
					console.error('AJAX 요청 실패:', error);
					alert('서버 통신에 실패했습니다.');
				});
		}

		return {
			cancelMatchProviderProduct
		};

	})();

	// 검색 파라미터 수집 공통 함수
	function getSearchParams(additionalParams) {
		var params = {};

		// 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
		var fields = {
			's_partner': $("#s_partner").val(),
			's_godo_match': $("#s_godo_match").val(),
			's_supplier_match': $("#s_supplier_match").val(),
			's_keyword': $("#s_keyword").val(),
			's_brand': $("#s_brand").val(),
			'sort_mode': $("#sort_kind").val(),
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
		location.href = '/admin/provider_product/list' + (queryString ? '?' + queryString : '');
	}


	$(function() {

		$(".dn-select2").select2();

		// 검색 인풋 엔터키 처리
		$('#s_keyword').on('keypress', function(e) {
			if (e.which === 13) {
				e.preventDefault();
				$("#searchBtn").click();
			}
		});

		$("#searchBtn").on('click', function() {
			// 검색 파라미터 수집
			var params = getSearchParams();

			// 페이지 이동
			navigateWithParams(params);
		});

		$("#sort_kind").change(function() {
			// 정렬 모드 추가하여 검색 파라미터 수집
			var params = getSearchParams({
				'sort_mode': $(this).val()
			});

			// 페이지 이동
			navigateWithParams(params);
		});

		// 매칭취소 버튼 클릭
		$('.cancel-match-btn').on('click', function() {

			const db1_idx = $(this).data('db1-idx');
			const db2_idx = $(this).data('db2-idx');

			dnConfirm(
				'정말 매칭취소 하시겠습니까?',
				'취소하시면 데이터는 복구되지 않습니다.',
				() => {
					prdProvider.cancelMatchProviderProduct(db1_idx, db2_idx);
				}
			);

		});


	});
</script>