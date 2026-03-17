<?

use App\Services\ProductPartnerService;

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 변수 초기화
$_prd_idx = $_get1 ?? "";
$prd_data = [];
$img_path = "";

$prd_mode = $_GET['prd_mode'] ?? "basic";

// 디버깅: $_prd_idx 값 확인
if (!$_prd_idx) {
	echo "오류: 상품 IDX가 없습니다. _get1 = " . ($_get1 ?? 'undefined');
	exit;
}

if ($_prd_idx) {

	$_colum = "A.CD_IDX, A.CD_IMG, A.CD_NAME, A.CD_MEMO, comment_count, A.cd_godo_code, A.cd_national, A.img_mode,
		A.cd_reg_time, A.cd_reg, A.supplier_prd_idx";

	$_colum .= ",B.ps_idx, B.ps_stock, B.ps_stock_hold, B.ps_rack_code, B.is_sale_month, B.is_sale_special";
	$_colum .= ", C.BD_NAME";

	if ($prd_mode == "basic") {

		$_query = "select " . $_colum . " from " . _DB_COMPARISON . " A
			left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX) 
			left join " . _DB_BRAND . " C ON (C.BD_IDX = A.CD_BRAND_IDX AND A.CD_BRAND_IDX > 0) 
			where A.CD_IDX = '" . $_prd_idx . "' ";
	} else {

		$_query = "select " . $_colum . " from  prd_stock B
			left join " . _DB_COMPARISON . " A ON (B.ps_prd_idx = A.CD_IDX) 
			left join " . _DB_BRAND . " C ON (C.BD_IDX = A.CD_BRAND_IDX AND A.CD_BRAND_IDX > 0) 
			where B.ps_idx = '" . $_prd_idx . "' ";
	}

	/*
	$_query = "select ".$_colum." from "._DB_COMPARISON." A
		left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX) 
		left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND_IDX AND A.CD_BRAND_IDX > 0) 
		where A.CD_IDX = '".$_prd_idx."' ";
	*/

	// 디버깅: 쿼리 확인
	// echo "<pre>쿼리: " . $_query . "</pre>";

	$result = sql_query_error($_query);

	// 디버깅: 쿼리 결과 확인
	if (!$result) {
		echo "쿼리 실행 실패";
		exit;
	}

	$prd_data = sql_fetch_array($result);

	$reg_data = json_decode((string)($prd_data['cd_reg'] ?? ''), true);
	$latest_modify_date = '';
	if (is_array($reg_data) && !empty($reg_data['modify']) && is_array($reg_data['modify'])) {
		foreach ($reg_data['modify'] as $modifyUnit) {
			if (!is_array($modifyUnit)) {
				continue;
			}
			$modifyDate = (string)($modifyUnit['date'] ?? '');
			if ($modifyDate !== '' && ($latest_modify_date === '' || $modifyDate > $latest_modify_date)) {
				$latest_modify_date = $modifyDate;
			}
		}
	}

	//$reg_date = (string)($reg_data['reg']['info']['date'] ?? '');
	$reg_date = $prd_data['cd_reg_time'] ?? '';

	$latest_modify_date = '';
	if (is_array($reg_data) && !empty($reg_data['modify']) && is_array($reg_data['modify'])) {
		foreach ($reg_data['modify'] as $modifyUnit) {
			if (!is_array($modifyUnit)) {
				continue;
			}
			$modifyDate = (string)($modifyUnit['date'] ?? '');
			if ($modifyDate !== '' && ($latest_modify_date === '' || $modifyDate > $latest_modify_date)) {
				$latest_modify_date = $modifyDate;
			}
		}
	}


	// 배열 검증
	if (!is_array($prd_data) || empty($prd_data)) {
		echo "<pre>오류: 상품 데이터를 찾을 수 없습니다.<br>";
		echo "상품 IDX: " . $_prd_idx . "<br>";
		echo "쿼리: " . $_query . "</pre>";
		$prd_data = [];
	}

	// 디버깅: 데이터 확인
	// echo "<pre>prd_data: "; print_r($prd_data); echo "</pre>";

	if( $prd_data['img_mode'] == 'out' ){
		if (!empty($prd_data['CD_IMG'])) {
			$img_path = $prd_data['CD_IMG'];
		}
	}else{
		if (!empty($prd_data['CD_IMG'])) {
			$img_path = '/data/comparion/' . $prd_data['CD_IMG'];
		}
	}

	//매입 방식 라벨
	$cd_national_label = "";
	if ($prd_data['cd_national'] == "jp") {
		$cd_national_label = "일본수입";
	} else if ($prd_data['cd_national'] == "cn") {
		$cd_national_label = "중국수입";
	} else if ($prd_data['cd_national'] == "kr") {
		$cd_national_label = "한국사입";
	} else if ($prd_data['cd_national'] == "dollar") {
		$cd_national_label = "달러";
	}

	$popup_browser_title = "(" . $prd_data['BD_NAME'] . ") " . $prd_data['CD_NAME'] ?? '';

	if( !empty($prd_data['supplier_prd_idx']) ){
		$supplier_prd_idx = $prd_data['supplier_prd_idx'];

		$ProductPartnerService = new ProductPartnerService();
		$supplier_data = $ProductPartnerService->getProductPartnerInfo($supplier_prd_idx);

		//dump($supplier_data);
	}
}

include($docRoot . "/admin2/layout/header_popup.php");
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
		align-items: flex-start;
	}
	.supplier-match-name {
		font-size: 12px;
		color: #1f2937;
		font-weight: 600;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 250px;
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
</style>
<div class="prd-quick-left">

	<?php if ($prd_data['is_sale_month'] || $prd_data['is_sale_special']) { ?>
		<div class="on_sale_label_wrap">
			<?php if ($prd_data['is_sale_month']) { ?>
				<label class="on_sale_label xs monthly">월간할인</label>
			<?php } ?>
			<?php if ($prd_data['is_sale_special']) { ?>
				<label class="on_sale_label xs special">특가할인</label>
			<?php } ?>
		</div>
	<?php } ?>

	<div class="prd-img">
		<? if ($img_path) { ?>
			<img src="<?= $img_path ?>" style="height:150px; border:1px solid #eee !important;">
		<? } else { ?>
			<div style="width:150px; height:150px; border:1px solid #eee; display:flex; align-items:center; justify-content:center; color:#999;">이미지 없음</div>
		<? } ?>
	</div>

	<div class="prd-quick-info">
		<ul class="prd-brand-name"><?= $prd_data['BD_NAME'] ?? '' ?></ul>
		<ul class="prd-name"><b><?= $prd_data['CD_NAME'] ?? '' ?></b></ul>
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

				<p>매칭된 공급사 상품</p>
				<div class="supplier-match-card" onclick="prdProviderQuick('<?= $prd_data['supplier_prd_idx'] ?>');" >
					
					<?php if ($supplierImage !== '') { ?>
						<img src="<?= $supplierImage ?>" alt="supplier" class="supplier-match-avatar">
					<?php } else { ?>
						<div class="supplier-match-avatar-placeholder">IMG</div>
					<?php } ?>

					<div class="supplier-match-text">
						<div class="supplier-match-name">
							<?= $supplierName !== '' ? $supplierName : '공급사 상품명 없음' ?>
						</div>
						<div class="supplier-match-meta">
							고유번호: <b>#<?= $supplierIdxText ?></b></br>
							<?php if ($supplierStatus !== '') { ?>
								상태: <b><?= $supplierStatus ?></b></br>
							<?php } ?>
							<?php if ($supplierStatus === '품절' && $supplierSoldOutDate !== '') { ?>
								| 품절일: <span class="text-red"><?= date('Y.m.d', strtotime($supplierSoldOutDate)) ?></span>
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
		<ul id="crm_menu_price" class="" onclick="prdInfo.mode('', 'price')">매입정보</ul>
		<ul id="crm_menu_saleLog" class="" onclick="prdInfo.mode('', 'saleLog')">할인 로그</ul>
		<ul id="crm_menu_stock_chart" class="" onclick="prdInfo.mode('', 'stock_chart')">재고/판매량 요약</ul>
		<ul id="crm_menu_stock" class="" onclick="prdInfo.mode('', 'stock')">재고/판매 리스트</ul>
		<ul id="crm_menu_onadb_config" class="" onclick="prdInfo.mode('', 'onadb_config')">오나DB 설정</ul>
		<ul id="crm_menu_onadb_comment" class="" onclick="prdInfo.mode('', 'onadb_comment')">오나DB 한줄평</ul>
		<ul id="crm_menu_log" class="" onclick="prdInfo.mode('', 'log')">수정로그</ul>
	</div>

	<?php 
		if( !empty($prd_data['ps_idx']) ){ ?>
		<div class="stock-write-box">

			<?php /*
			<ul>현재 재고 : <b id="now_stock"><?=$prd_data['ps_stock'] ?? 0?></b></ul>
			<ul class="m-t-7">보류 재고 : <b id="now_stock_hold" style="color:#999;"><?=$prd_data['ps_stock_hold'] ?? 0?></b></ul>
			*/ ?>

			<ul class="m-t-7"><button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm btnstyle1-search-full" onclick="prdInfo.stockModify()" >재고 변경등록</button></ul>
		</div>
		<?php } 

	?>

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

			<?php if (!empty($prd_data['ps_idx'])) { ?>
				<?php if (!empty($prd_data['cd_national'])) { ?>
					<ul>
						<dl>
							<dt>매입 방식</dt>
							<dd><b><?= $cd_national_label ?? '' ?></b></dd>
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
						<dd><b><?= $prd_data['ps_idx'] ?></b></dd>
					</dl>
				<?php } else { ?>
					<dl>
						<dt>재고코드가 생성되지 않았습니다.</dt>
						<dd>
							<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdInfo.makePsIdx()"> <i class="fas fa-plus-circle"></i> 재고 코드 생성</button>
						</dd>
					</dl>
				<?php } ?>
			</ul>

			<?php if (!empty($prd_data['ps_rack_code'])) { ?>
				<ul>
					<dl>
						<dt>랙코드</dt>
						<dd><b><?= $prd_data['ps_rack_code'] ?></b></dd>
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

				<?php if (!empty($prd_data['cd_godo_code'])) { ?>
					<ul>
						<dl>
							<dt>쑈당몰 상품보기</dt>
							<dd><button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall('<?= $prd_data['cd_godo_code'] ?? '' ?>');">#<?= $prd_data['cd_godo_code'] ?? 0 ?></button></dd>
						</dl>
					</ul>
					<ul>
						<dl>
							<dt>고도몰 상품관리</dt>
							<dd><button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin('<?= $prd_data['cd_godo_code'] ?? '' ?>');">#<?= $prd_data['cd_godo_code'] ?? 0 ?></button></dd>
						</dl>
					</ul>
				<?php } else { ?>
					<ul class="warning-text">
						<i class="fas fa-exclamation-triangle"></i>
						<p>아직 고도몰 상품번호가 등록되지 않았습니다.</p>
						<p>고도몰 상품번호 등록해주세요.</p>
					</ul>
				<?php } ?>

			<?php } ?>

			<ul class="right">
				<dl>
					<dt>댓글</dt>
					<dd>
						<button type="button" id="" class="btnstyle1 btnstyle1-xs" onclick="footerGlobal.comment('prd','<?= $_prd_idx ?>')">
							댓글
							<? if (($prd_data['comment_count'] ?? 0) > 0) { ?> : <b><?= $prd_data['comment_count'] ?></b><? } ?>
						</button>
					</dd>
				</dl>
			</ul>
			<ul>
				수정일 : <?= $latest_modify_date ?: '-' ?><br>
				등록일 : <?= $reg_date ?: '-' ?>
			</ul>


		</div>
		<div id="crm_body">

		</div>
	</ul>
</div>

<script>
	const prdInfo = (function() {

		var prd_idx = "<?= $prd_data['CD_IDX'] ?? '' ?>";
		var ps_idx = "<?= $prd_data['ps_idx'] ?? '' ?>";
		var stockModifyWindow;

		/**
		 * 메뉴 클릭
		 */
		function mode(pn, mode) {


			$(".crm-menu ul").removeClass('active');
			$("#crm_menu_" + mode).addClass('active');

			var _search_date_s = "";
			var _search_date_e = "";

			if ($(".list-search-date-box").length) {
				_search_date_s = $("#search_date_s").val();
				_search_date_e = $("#search_date_e").val();
			}

			var data = {
				"prd_idx": prd_idx
			};
			var ajaxMethod = "POST";
			var ajaxUrl = "/ad/ajax/prd_reg_form";

			// @deprecated
			if (mode == "info2") {
				ajaxUrl = "/ad/ajax/prd_reg_form";

			} else if (mode == "info") {
				ajaxMethod = "GET";
				ajaxUrl = "/admin/product/detail_basic";
				data = {
					"prd_idx": prd_idx
				};

			} else if (mode == "price") {
				ajaxMethod = "GET";
				ajaxUrl = "/admin/product/detail_price";
				data = {
					"prd_idx": prd_idx
				};

			} else if (mode == "price2") {
				ajaxUrl = "/ad/ajax/prd_info_price";

			} else if (mode == "saleLog") { //할인 로그
				ajaxUrl = "/ad/ajax/prd_info_salelog";

			} else if (mode == "stock_chart") { //재고 챠트
				ajaxUrl = "/ad/ajax/prd_info_stock_chart";
				data = {
					"prd_idx": prd_idx,
					"ps_idx": ps_idx
				};

			} else if (mode == "stock") { //재고 챠트
				ajaxUrl = "/ad/ajax/prd_info_stock";
				data = {
					"prd_idx": prd_idx,
					"ps_idx": ps_idx,
					"pn": pn,
					"sdate": _search_date_s,
					"edate": _search_date_e
				};

			} else if (mode == "onadb_config") { //오나DB 설정
				ajaxUrl = "/ad/ajax/prd_info_onadb_config";

			} else if (mode == "onadb_comment") { //오나DB 한줄평
				ajaxUrl = "/ad/ajax/onadb_prd_comment_list";
				data = {
					"prd_idx": prd_idx,
					"pn": pn,
					"load_page": "prdInfo"
				};

			//수정로그
			} else if (mode == "log") { 
				ajaxMethod = "GET";
				ajaxUrl = "/admin/admin_action_log/list";
				data = {
					"prd_idx": prd_idx,
					"target_type": "product",
				};
			}

			$.ajax({
				url: ajaxUrl,
				data: data,
				type: ajaxMethod,
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

		return {

			mode,
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

		}

	})();


	$(function() {

		prdInfo.mode('', 'info');

	});
</script>
<?
include($docRoot . "/admin2/layout/footer_popup.php");
exit;
?>