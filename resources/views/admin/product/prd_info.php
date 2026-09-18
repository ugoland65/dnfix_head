<?php
$prd_idx = (int)($prd_idx ?? 0);
$vmode = (string)($vmode ?? 'info');
$prd_data = (isset($prd_data) && is_array($prd_data)) ? $prd_data : [];
$supplier_data = (isset($supplier_data) && is_array($supplier_data)) ? $supplier_data : [];
$seriesNames = (isset($seriesNames) && is_array($seriesNames)) ? $seriesNames : [];
$img_path = (string)($img_path ?? '');
$cd_national_label = (string)($cd_national_label ?? '');
$grade = (string)($grade ?? '');
$reg_date = (string)($reg_date ?? '');
$latest_modify_date = (string)($latest_modify_date ?? '');
$h = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<style>
	.supplier-match-wrap {
		margin-top: 10px;
		padding: 15px 10px 10px 10px !important;
	}
	.supplier-match-card {
		display: flex;
		align-items: center;
		gap: 8px;
		padding: 5px 5px;
		margin-top: 3px;
		border: 1px solid #e4e7ee;
		border-radius: 6px;
		background: #f8faff;
		cursor: pointer;
	}
	.supplier-match-avatar {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		object-fit: cover;
		border: 1px solid #dde3f0;
	}
	.supplier-match-avatar-placeholder {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		background: #e9edf5;
		color: #7a8599;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 11px;
	}
	.supplier-match-text {
		display: flex;
		flex-direction: column;
		gap: 2px;
		min-width: 0;
		width: 0;
		flex: 1;
		align-items: flex-start;
	}
	.supplier-match-name {
		display: block;
		font-size: 12px;
		color: #1f2937;
		font-weight: 600;
		width: 100%;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.supplier-match-meta {
		font-size: 11px;
		color: #6b7280;
		text-align: left;
	}
	.supplier-unmatched-badge {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 4px 10px;
		border-radius: 12px;
		background: #f7f7f8;
		color: #666;
		font-size: 12px;
		border: 1px solid #e1e3e8;
	}

	.left-btn-wrap {
		padding: 5px 15px 0 15px;
	}
	.prd-series {
		margin-top: 8px;
		color: #4b5563;
		font-size: 12px;
		line-height: 1.45;
	}
	.prd-series b { color: #111827; }
	.prd-series-empty { color: #9ca3af; font-weight: 400; }

	.prd-settings-wrap {
		position: relative;
		align-self: center;
	}
	.prd-settings-btn {
		width: 32px;
		height: 32px;
		border: 1px solid #d9dce7;
		border-radius: 6px;
		background: #fff;
		color: #4b5563;
		cursor: pointer;
		line-height: 1;
	}
	.prd-settings-btn:hover,
	.prd-settings-btn[aria-expanded="true"] {
		background: #f3f4f6;
		color: #111827;
	}
	.prd-settings-layer {
		position: absolute;
		top: calc(100% + 6px);
		right: 0;
		min-width: 148px;
		padding: 6px;
		background: #fff;
		border: 1px solid #d9dce7;
		border-radius: 8px;
		box-shadow: 0 8px 24px rgba(15, 23, 42, .16);
		z-index: 120;
	}
	.prd-settings-layer[hidden] {
		display: none !important;
	}
	.prd-settings-item {
		display: block;
		width: 100%;
		padding: 8px 10px;
		border: 0;
		border-radius: 6px;
		background: none;
		text-align: left;
		font-size: 13px;
		color: #111827;
		cursor: pointer;
	}
	.prd-settings-item:hover {
		background: #f3f4f6;
	}
</style>
<div class="prd-quick-left">

	<?php if (!empty($prd_data['is_sale_month']) || !empty($prd_data['is_sale_special']) || !empty($prd_data['is_discontinued']) || !empty($prd_data['is_handling_stopped'])) { ?>
		<div class="on_sale_label_wrap">
			<?php if (!empty($prd_data['is_sale_month'])) { ?>
				<label class="on_sale_label xs monthly">월간할인</label>
			<?php } ?>
			<?php if (!empty($prd_data['is_sale_special'])) { ?>
				<label class="on_sale_label xs special">특가할인</label>
			<?php } ?>
			<?php if (!empty($prd_data['is_discontinued'])) { ?>
				<label class="on_sale_label xs discontinued">단종</label>
			<?php } ?>
			<?php if (!empty($prd_data['is_handling_stopped'])) { ?>
				<label class="on_sale_label xs handling-stopped">취급중단</label>
			<?php } ?>
		</div>
	<?php } ?>

	<div class="prd-img">
		<?php if ($img_path !== '') { ?>
			<img src="<?= $h($img_path) ?>" referrerpolicy="no-referrer" style="height:150px; border:1px solid #eee !important;">
		<?php } else { ?>
			<div style="width:150px; height:150px; border:1px solid #eee; display:flex; align-items:center; justify-content:center; color:#999;">이미지 없음</div>
		<?php } ?>
	</div>

	<div class="prd-quick-info">
		<ul class="prd-brand-name"><?= $h($prd_data['BD_NAME'] ?? '') ?></ul>
		<ul class="prd-name"><b><?= $h($prd_data['CD_NAME'] ?? '') ?></b></ul>
		<!-- <ul class="prd-name-en"><?= $prd_data['CD_NAME_OG'] ?? '' ?></ul> -->

		<?php if( !empty($prd_data['supplier_prd_idx']) ){ ?>
			
			<?php
				$supplierName = trim((string)($supplier_data['name_p'] ?? ''));
				$supplierImage = trim((string)($supplier_data['supplier_img_src'] ?? ''));
				$supplierStatus = trim((string)($supplier_data['status'] ?? ''));
				$supplierSoldOutDate = trim((string)($supplier_data['sold_out_date'] ?? ''));
				$supplierIdxText = (string)($prd_data['supplier_prd_idx'] ?? '');
			?>
			<ul class="supplier-match-wrap">

				<p>매칭된 위탁 상품</p>
				<div class="supplier-match-card" onclick="prdProviderQuick('<?= $h($prd_data['supplier_prd_idx'] ?? '') ?>');" >
					
					<?php if ($supplierImage !== '') { ?>
						<img src="<?= $h($supplierImage) ?>" alt="supplier" class="supplier-match-avatar" referrerpolicy="no-referrer">
					<?php } else { ?>
						<div class="supplier-match-avatar-placeholder">IMG</div>
					<?php } ?>

					<div class="supplier-match-text">
						<div class="supplier-match-name">
							<?= $supplierName !== '' ? $h($supplierName) : '위탁 상품명 없음' ?>
						</div>
						<div class="supplier-match-meta">
							고유번호: <b>#<?= $h($supplierIdxText) ?></b><br>
							<?php if ($supplierStatus !== '') { ?>
								상태: <b><?= $h($supplierStatus) ?></b><br>
							<?php } ?>
							<?php if ($supplierStatus === '품절' && $supplierSoldOutDate !== '') { ?>
								| 품절일: <span class="text-red"><?= $h(date('Y.m.d', strtotime($supplierSoldOutDate))) ?></span>
							<?php } ?>
						</div>
					</div>

				</div>
			</ul>
		<?php } else { ?>
			<ul class="m-t-10">
				<div class="supplier-unmatched-badge">
					<i class="fas fa-unlink" style="color:#9aa0a6;"></i>
					공급사 상품 연동되지 않음
				</div>
			</ul>
		<?php } ?>

		<ul id="prd-header-series" class="prd-series">
			시리즈 :
			<?php if (!empty($seriesNames)) { ?>
				<b><?= htmlspecialchars(implode(', ', $seriesNames), ENT_QUOTES, 'UTF-8') ?></b>
			<?php } else { ?>
				<span class="prd-series-empty">미설정</span>
			<?php } ?>
		</ul>

		<?php 
		/* if( !empty($prd_data['ps_idx']) ){ ?>
			<ul class="prd-stock-code">
				<b><?=$prd_data['ps_idx']?></b>
			</ul>

			<?php if( !empty($prd_data['ps_rack_code']) ){ ?>
				<ul>
					( <b><?=$prd_data['ps_rack_code']?></b> )
				</ul>
			<?php } ?>

		<? }else{ ?>
			<ul class="prd-stock-code-make"><button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdInfo.makePsIdx()"> <i class="fas fa-plus-circle"></i> 재고 코드 생성</button></ul>
		<?php } ?>

		<ul>
			<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-xs" onclick="footerGlobal.comment('prd','<?=$_prd_idx?>')" >
				댓글
				<? if( ($prd_data['comment_count'] ?? 0) > 0 ) { ?> : <b><?=$prd_data['comment_count']?></b><? } ?>
			</button>
		</ul>
		<?php 
		*/ ?>

	</div>

	<div class="crm-menu m-t-10">

		<?php 
			/*
				<ul id="crm_menu_info2" class="active" onclick="prdInfo.mode('', 'info2')">상품정보(구)</ul>
				<ul id="crm_menu_price2" class="" onclick="prdInfo.mode('', 'price2')">매입정보 (구)</ul>
			*/ 
		?>
		<ul id="crm_menu_info" class="active" onclick="prdInfo.mode('', 'info')">상품정보</ul>
		<ul id="crm_menu_price" class="" onclick="prdInfo.mode('', 'price')">매입/판매 정보</ul>
		<ul id="crm_menu_saleLog" class="" onclick="prdInfo.mode('', 'saleLog')">할인 로그</ul>

		<?php if (!empty($prd_data['ps_idx'])) { ?>
			<ul id="crm_menu_stock_chart" class="" onclick="prdInfo.mode('', 'stock_chart')"><b>판매량/발주 요약</b></ul>
			<ul id="crm_menu_stock" class="" onclick="prdInfo.mode('', 'stock')">재고 변경 이력</ul>
		<?php } ?>

		<ul id="crm_menu_competitor_product" class="" onclick="prdInfo.mode('', 'competitor_product')">경쟁사 판매현황</ul>
		<ul id="crm_menu_godo_inspection" class="" onclick="prdInfo.mode('', 'godo_inspection')">고도몰 검수 처리</ul>
		<ul id="crm_menu_relation_group" class="" onclick="prdInfo.mode('', 'relation_group')">시리즈/연관그룹 관리</ul>
		<ul id="crm_menu_info_collection" class="" onclick="prdInfo.mode('', 'info_collection')">상품 정보수집</ul>
		<ul id="crm_menu_spec_info" class="" onclick="prdInfo.mode('', 'spec_info')">상품 스팩정보</ul>
		<ul id="crm_menu_content" class="" onclick="prdInfo.mode('', 'content')"><b>상품 컨텐츠 관리</b></ul>

		<?php if (!empty($prd_data['cd_site_show']) && $prd_data['cd_site_show'] == 'Y') { ?>
			<ul id="crm_menu_onadb_config" class="" onclick="prdInfo.mode('', 'onadb_config')">오나DB 설정</ul>
			<ul id="crm_menu_onadb_comment" class="" onclick="prdInfo.mode('', 'onadb_comment')">오나DB 한줄평</ul>
		<?php } ?>

		<ul id="crm_menu_log" class="" onclick="prdInfo.mode('', 'log')">수정로그</ul>
	</div>

	<?php 
		if( !empty($prd_data['ps_idx']) ){ ?>
		<div class="stock-write-box">

			<?php 
				/*
				<ul>현재 재고 : <b id="now_stock"><?=$prd_data['ps_stock'] ?? 0?></b></ul>
				<ul class="m-t-7">보류 재고 : <b id="now_stock_hold" style="color:#999;"><?=$prd_data['ps_stock_hold'] ?? 0?></b></ul>
				*/ 
			?>

			<ul class="m-t-7"><button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm btnstyle1-search-full" onclick="prdInfo.stockModify()" >재고 변경등록</button></ul>
		</div>
		<?php } ?>
	<div class="left-btn-wrap">
		<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm btnstyle1-search-full" onclick="prdInfo.prdGroupingAdd()" >이상품 그룹핑 추가</button>
	</div>

</div>

<div class="crm-wrap">
	<ul class="crm-menu-wrap"></ul>
	<ul class="crm-gap "></ul>
	<ul class="crm-body">
		<div class="crm-top-menu-wrap">

			<?php
			/*
			<ul>
				<div>
					<ul><?=$prd_data['BD_NAME'] ?? ''?></ul>
					<ul><b><?=$prd_data['CD_NAME'] ?? ''?></b></ul>
				</div>
			</ul>
			*/
			?>
			<ul>
				<dl>
					<dt>고유번호</dt>
					<dd><b class="text-blue"><?= $h($prd_data['CD_IDX'] ?? '') ?></b></dd>
				</dl>
			</ul>
			<?php if (!empty($prd_data['ps_idx'])) { ?>
				<?php if (!empty($prd_data['cd_national'])) { ?>
					<ul>
						<dl>
							<dt>매입 방식</dt>
							<dd><b><?= $h($cd_national_label) ?></b></dd>
						</dl>
					</ul>
				<?php }else{ ?>
					<ul class="warning-text">
						<i class="fas fa-exclamation-triangle"></i>
						<p>매입방식 미등록</p>
						<p>매입방식 등록해주세요.</p>
					</ul>
				<?php } ?>
			<?php } ?>

			<ul>
				<?php if (!empty($prd_data['ps_idx'])) { ?>
					<dl>
						<dt>재고코드</dt>
						<dd><b><?= $h($prd_data['ps_idx'] ?? '') ?></b></dd>
					</dl>
				<?php } else { ?>
					<dl>
						<dt>재고코드가 생성되지 않았습니다.</dt>
						<dd>
							<button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdInfo.makePsIdx()"> <i class="fas fa-plus-circle"></i> 재고 코드 생성</button>
						</dd>
					</dl>
				<?php } ?>
			</ul>

			<?php if (!empty($prd_data['ps_rack_code'])) { ?>
				<ul>
					<dl>
						<dt>랙코드</dt>
						<dd><b><?= $h($prd_data['ps_rack_code'] ?? '') ?></b></dd>
					</dl>
				</ul>
			<?php } ?>

			<?php if (!empty($prd_data['ps_idx'])) { ?>

				<ul>
					<dl>
						<dt>현재 재고</dt>
						<dd><b id="now_stock" onclick="prdInfo.stockModify()"><?= $prd_data['ps_stock'] ?? 0 ?></b></dd>
					</dl>
				</ul>
				<ul>
					<dl>
						<dt>보류 재고</dt>
						<dd><b id="now_stock_hold" style="color:#999;"><?= $prd_data['ps_stock_hold'] ?? 0 ?></b></dd>
					</dl>
				</ul>

				<?php if (!empty($grade)) { ?>
					<ul>
						<dl>
							<dt>마진등급</dt>
							<dd>
								<span class="grade-badge grade-<?= $h($grade) ?>">
									<?= $h($grade) ?>
								</span>
							</dd>
						</dl>
					</ul>
				<?php } ?>
			<?php } ?>

			<?php if (!empty($prd_data['cd_godo_code'])) { ?>
				<ul>
					<dl>
						<dt>쑈당몰 보기</dt>
						<dd><button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall('<?= $h($prd_data['cd_godo_code'] ?? '') ?>');">#<?= $h($prd_data['cd_godo_code'] ?? 0) ?></button></dd>
					</dl>
				</ul>
				<ul>
					<dl>
						<dt>고도몰 관리</dt>
						<dd><button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin('<?= $h($prd_data['cd_godo_code'] ?? '') ?>');">#<?= $h($prd_data['cd_godo_code'] ?? 0) ?></button></dd>
					</dl>
				</ul>
			<?php } else { ?>
				<ul class="warning-text">
					<i class="fas fa-exclamation-triangle"></i>
					<p>아직 고도몰 상품번호가 등록되지 않았습니다.</p>
					<p>고도몰 상품번호 등록해주세요.</p>
				</ul>
			<?php } ?>

			<ul class="right">
				<dl>
					<dt>댓글</dt>
					<dd>
						<button type="button" class="btnstyle1 btnstyle1-xs" onclick="footerGlobal.comment('prd','<?= $h($prd_idx) ?>')">
							댓글
							<?php if ((int)($prd_data['comment_count'] ?? 0) > 0) { ?> : <b><?= (int)$prd_data['comment_count'] ?></b><?php } ?>
						</button>
					</dd>
				</dl>
			</ul>
			<ul>
				수정 : <?= $latest_modify_date !== '' ? $h($latest_modify_date) : '-' ?><br>
				등록 : <?= $reg_date !== '' ? $h($reg_date) : '-' ?>
			</ul>

			<!-- 설정 -->
			<ul class="prd-settings-wrap">
				<button type="button" class="prd-settings-btn" id="prd_settings_btn" aria-haspopup="true" aria-expanded="false" title="설정">
					<i class="fas fa-cog"></i>
				</button>
				<div class="prd-settings-layer" id="prd_settings_layer" hidden>
					<button type="button" class="prd-settings-item" data-prd-settings="copy-window">새창 복사</button>
				</div>
			</ul>

		</div>
		<div id="crm_body">

		</div>
	</ul>
</div>

<script>
	const prdInfo = (function() {

		var prd_idx = "<?= $h($prd_data['CD_IDX'] ?? '') ?>";
		var ps_idx = "<?= $h($prd_data['ps_idx'] ?? '') ?>";
		var stockModifyWindow;
		var activeModeStorageKey = 'prd_info_active_mode:' + prd_idx + ':' + ps_idx;

		function resolveMode(modeKey) {
			modeKey = String(modeKey || '').trim();
			return modeKey && $("#crm_menu_" + modeKey).length ? modeKey : '';
		}

		function getQueryVmode() {
			try {
				var params = new URLSearchParams(window.location.search);
				return String(params.get('vmode') || '').trim();
			} catch (e) {
				return '';
			}
		}

		function getSavedMode() {
			try {
				return resolveMode(window.sessionStorage.getItem(activeModeStorageKey)) || 'info';
			} catch (e) {
				return 'info';
			}
		}

		function getInitialMode() {
			return resolveMode(getQueryVmode()) || getSavedMode();
		}

		function syncUrlVmode(modeKey) {
			try {
				var url = new URL(window.location.href);
				if (url.searchParams.get('vmode') === modeKey) {
					return;
				}
				url.searchParams.set('vmode', modeKey);
				window.history.replaceState(null, '', url.pathname + url.search + url.hash);
			} catch (e) {
				// history API를 쓸 수 없으면 현재 URL을 유지한다.
			}
		}

		/**
		 * 메뉴 클릭
		 */
		function resetCrmBodyTopPadding() {
			var crmBody = document.querySelector('.crm-body');
			if (crmBody) {
				crmBody.style.removeProperty('--crm-body-top-padding');
			}
		}

		function mode(pn, modeKey) {

			$(".crm-menu ul").removeClass('active');
			$("#crm_menu_" + modeKey).addClass('active');
			resetCrmBodyTopPadding();
			try {
				window.sessionStorage.setItem(activeModeStorageKey, modeKey);
			} catch (e) {
				// sessionStorage를 사용할 수 없는 환경에서는 기본 탭으로 동작한다.
			}
			syncUrlVmode(modeKey);

			var searchDateStart = "";
			var searchDateEnd = "";
			if ($(".list-search-date-box").length) {
				searchDateStart = $("#search_date_s").val();
				searchDateEnd = $("#search_date_e").val();
			}

			var requestConfig = {
				method: "POST",
				url: "/ad/ajax/prd_reg_form",
				data: { prd_idx: prd_idx }
			};

			switch (modeKey) {
				case "info2": // @deprecated
					requestConfig = {
						method: "POST",
						url: "/ad/ajax/prd_reg_form",
						data: { prd_idx: prd_idx }
					};
					break;
				case "info":
					requestConfig = {
						method: "GET",
						url: "/admin/product/detail_basic",
						data: { prd_idx: prd_idx }
					};
					break;
				case "price":
					requestConfig = {
						method: "GET",
						url: "/admin/product/detail_price",
						data: { prd_idx: prd_idx }
					};
					break;
				case "price2": // @deprecated
					requestConfig = {
						method: "POST",
						url: "/ad/ajax/prd_info_price",
						data: { prd_idx: prd_idx }
					};
					break;
				case "saleLog": // 할인 로그
					requestConfig = {
						method: "GET",
						url: "/admin/product/detail_sale_log",
						data: {
							prd_idx: prd_idx,
							prd_mode: "prdDB"
						}
					};
					break;
				case "stock_chart": // 판매량/발주 요약
					requestConfig = {
						method: "GET",
						url: "/admin/product/detail_stock_chart",
						data: {
							prd_idx: prd_idx,
							ps_idx: ps_idx
						}
					};
					break;
				case "stock": // 재고/판매 리스트
					requestConfig = {
						method: "POST",
						url: "/ad/ajax/prd_info_stock",
						data: {
							prd_idx: prd_idx,
							ps_idx: ps_idx,
							pn: pn,
							sdate: searchDateStart,
							edate: searchDateEnd
						}
					};
					break;
				case "competitor_product": // 경쟁사 판매현황
					requestConfig = {
						method: "GET",
						url: "/admin/product/detail_competitor_product",
						data: { prd_idx: prd_idx }
					};
					break;
				case "godo_inspection": // 고도몰 검수 처리
					requestConfig = {
						method: "POST",
						url: "/admin/product/detail_godo_inspection",
						data: {
							prd_idx: prd_idx,
							ps_idx: ps_idx
						}
					};
					break;
				case "relation_group": // 시리즈/연관그룹 관리
					requestConfig = {
						method: "GET",
						url: "/admin/product/detail_relation_group",
						data: { prd_idx: prd_idx }
					};
					break;
				case "info_collection": // 상품 정보수집
					requestConfig = {
						method: "GET",
						url: "/admin/product/info_collect",
						data: { prd_idx: prd_idx }
					};
					break;
				case "spec_info": // 상품 스펙정보
					requestConfig = {
						method: "GET",
						url: "/admin/product/detail_spec_info",
						data: { prd_idx: prd_idx }
					};
					break;
				case "content": // 상품 컨텐츠 관리
					requestConfig = {
						method: "GET",
						url: "/admin/product/detail_content",
						data: { prd_idx: prd_idx }
					};
					break;
				case "onadb_config": // 오나DB 설정
					requestConfig = {
						method: "POST",
						url: "/ad/ajax/prd_info_onadb_config",
						data: { prd_idx: prd_idx }
					};
					break;
				case "onadb_comment": // 오나DB 한줄평
					requestConfig = {
						method: "POST",
						url: "/ad/ajax/onadb_prd_comment_list",
						data: {
							prd_idx: prd_idx,
							pn: pn,
							load_page: "prdInfo"
						}
					};
					break;
				case "log": // 수정로그
					requestConfig = {
						method: "GET",
						url: "/admin/admin_action_log/list",
						data: {
							prd_idx: prd_idx,
							target_type: "product"
						}
					};
					break;
			}

			$.ajax({
				url: requestConfig.url,
				data: requestConfig.data,
				type: requestConfig.method,
				dataType: "text",
				success: function(getHtml) {
					if (getHtml) {
						$("#crm_body").html(getHtml);
					}
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

		/**
		 * 이상품 그룹핑 추가
		 */
		function prdGroupingAdd() {
			onlyAD.prdGrouping('product_db', prd_idx);
		}

		function escapeSeriesHtml(value) {
			return String(value || '')
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;');
		}

		function updateSeriesLabel(seriesNames) {
			var names = Array.isArray(seriesNames)
				? seriesNames.map(function (name) { return String(name || '').trim(); }).filter(Boolean)
				: [];
			var $el = $('#prd-header-series');
			if (!$el.length) {
				return;
			}
			if (!names.length) {
				$el.html('시리즈 : <span class="prd-series-empty">미설정</span>');
				return;
			}
			$el.html('시리즈 : <b>' + names.map(escapeSeriesHtml).join(', ') + '</b>');
		}

		return {

			mode,
			restoreActiveMode: function() {
				mode('', getInitialMode());
			},
			updateSeriesLabel,
			prdGroupingAdd,
			makePsIdx: function() {

				var payload = {
					action_mode: 'create_stock_code',
					prd_idx: prd_idx
				};

				ajaxRequest('/admin/product/stock/action', payload)
					.done(function(res) {
						if (res && res.success) {
							alert(res.message || '처리가 완료되었습니다.');
							location.reload();
						} else {
							alert(res && res.message ? res.message : '처리 실패');
						}
					})
					.fail(function(res) {
						alert(res && res.message ? res.message : '에러');
					});


				/*
				@deprecated
				$.ajax({
					url: "/ad/processing/prd",
					data: { "a_mode":"new_stock_psidx", "prd_idx":prd_idx },
					type: "POST",
					dataType: "json",
					success: function(res){
						if (res.success == true ){
							location.reload();
						}else{
							showAlert("Error", res.msg, "alert2" );
							return false;
						}
					},
					error: function(request, status, error){
						console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
						showAlert("Error", "에러", "alert2" );
						return false;
					},
					complete: function() {
						//$(obj).attr('disabled', false);
					}
				});
				*/

			},

			stockModify: function() {

				var width = "600px";

				stockModifyWindow = $.alert({
					boxWidth: width,
					useBootstrap: false,
					title: "재고 변경등록",
					backgroundDismiss: true,
					closeIcon: true,
					closeIconClass: 'fas fa-times',
					content: function() {
						var self = this;
						return $.ajax({
							url: '/ad/ajax/prd_stock_reg',
							data: {
								"prd_idx": prd_idx,
								"ps_idx": ps_idx
							},
							dataType: 'html',
							method: 'POST'
						}).done(function(response) {
							self.setContent(response);
						}).fail(function() {
							self.setContent('에러');
						});
					},
					buttons: {
						cancel: {
							text: '닫기',
							action: function() {

							}
						},
					}
				});

			},

			stockUnitModify: function(idx, pn) {

				var width = "600px";

				stockModifyWindow = $.alert({
					boxWidth: width,
					useBootstrap: false,
					title: "재고 유닛 수정",
					backgroundDismiss: true,
					closeIcon: true,
					closeIconClass: 'fas fa-times',
					content: function() {
						var self = this;
						return $.ajax({
							url: '/ad/ajax/prd_stock_reg',
							data: {
								"prd_idx": prd_idx,
								"ps_idx": ps_idx,
								"idx": idx,
								"pn": pn
							},
							dataType: 'html',
							method: 'POST'
						}).done(function(response) {
							self.setContent(response);
						}).fail(function() {
							self.setContent('에러');
						});
					},
					buttons: {
						cancel: {
							text: '닫기',
							action: function() {

							}
						},
					}
				});

			},

			stockModifyClose: function() {
				stockModifyWindow.close();
			},

			bindSettingsMenu: function() {
				var $btn = $('#prd_settings_btn');
				var $layer = $('#prd_settings_layer');
				if (!$btn.length || !$layer.length) {
					return;
				}

				function closeLayer() {
					$layer.prop('hidden', true);
					$btn.attr('aria-expanded', 'false');
				}

				function openLayer() {
					$layer.prop('hidden', false);
					$btn.attr('aria-expanded', 'true');
				}

				$btn.off('click.prdSettings').on('click.prdSettings', function(e) {
					e.preventDefault();
					e.stopPropagation();
					if ($layer.prop('hidden')) {
						openLayer();
					} else {
						closeLayer();
					}
				});

				$layer.off('click.prdSettings').on('click.prdSettings', '[data-prd-settings]', function(e) {
					e.preventDefault();
					e.stopPropagation();
					var action = String($(this).attr('data-prd-settings') || '');
					closeLayer();
					if (action === 'copy-window') {
						window.open(window.location.href, '_blank');
					}
				});

				$(document).off('click.prdSettings').on('click.prdSettings', function(e) {
					if (!$(e.target).closest('.prd-settings-wrap').length) {
						closeLayer();
					}
				});

				$(document).off('keydown.prdSettings').on('keydown.prdSettings', function(e) {
					if (e.key === 'Escape') {
						closeLayer();
					}
				});
			},

		}

	})();


	$(function() {

		prdInfo.restoreActiveMode();
		prdInfo.bindSettingsMenu();
		$('.crm-body').each(function(){
			$(this).toggleClass('has-top-menu', $(this).find('.crm-top-menu-wrap').length > 0);
		});

	});
</script>