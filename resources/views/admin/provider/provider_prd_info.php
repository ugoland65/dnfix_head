<?php
$prd_idx = (int)($prd_idx ?? 0);
$vmode = (string)($vmode ?? 'info');
$prd_data = (isset($prd_data) && is_array($prd_data)) ? $prd_data : [];
$img_path = $prd_data['img_src'] ?? '';
$soldOutDate = '';
if (($prd_data['status'] ?? '') === '품절' && !empty($prd_data['sold_out_date'])) {
    $soldOutTs = strtotime((string)$prd_data['sold_out_date']);
    $soldOutDate = $soldOutTs ? date('y.m.d H:i', $soldOutTs) : '';
}

$salePrice = (float)($prd_data['sale_price'] ?? 0);
$orderPrice = (float)($prd_data['order_price'] ?? 0);
$grade = '';
if ($salePrice > 0 && $orderPrice > 0) {
    $marginRate = (($salePrice - $orderPrice) / $salePrice) * 100;
    if ($marginRate > 39) {
        $grade = 'A';
    } elseif ($marginRate >= 35) {
        $grade = 'B';
    } elseif ($marginRate >= 30) {
        $grade = 'C';
    } elseif ($marginRate >= 25) {
        $grade = 'D';
    } elseif ($marginRate >= 20) {
        $grade = 'E';
    } elseif ($marginRate >= 15) {
        $grade = 'F';
    } elseif ($marginRate >= 10) {
        $grade = 'G';
    } elseif ($marginRate >= 5) {
        $grade = 'H';
    } elseif ($marginRate > 0) {
        $grade = 'I';
    }
}

$updatedAt = '';
if (!empty($prd_data['updated_at'])) {
    $updatedTs = strtotime((string)$prd_data['updated_at']);
    $updatedAt = $updatedTs ? date('Y.m.d H:i', $updatedTs) : '';
}
$createdAt = '';
if (!empty($prd_data['created_at'])) {
    $createdTs = strtotime((string)$prd_data['created_at']);
    $createdAt = $createdTs ? date('Y.m.d H:i', $createdTs) : '';
}
$imageStoragePath = trim((string)($prd_data['image_storage_path'] ?? ''));
$recommendedImageStoragePath = '/goods/provider/' . $prd_idx . '/';
?>

<style>
#popup { position:relative; min-width:400px; box-sizing:border-box; }
.prd-quick-left{ width:200px; height:100%; background-color:#fff; border-right:1px solid #9c9fae;
	position:fixed; padding-top:20px; z-index:99; }
.on_sale_label_wrap{ text-align:center; padding:0 0 5px 0; }
.prd-img{ text-align:center; }
.prd-quick-info{ margin:0; padding:0; }
.prd-quick-info > ul{ margin:0; padding:3px 0; box-sizing:border-box;text-align:center; }
.prd-brand-name{ padding-top:10px !important; text-align:center;  }
.prd-name{ padding:5px 10px 0 !important; text-align:center; }
.prd-memo{ text-align:center; color:#ff0000; }
.crm-menu{ width:100%; border-top:1px solid #9c9fae; }
.crm-menu ul{  height:35px; line-height:35px; padding:0 0 0 15px; margin:0 !important; box-sizing:border-box; border-bottom:1px solid #9c9fae; cursor:pointer; background-color:#eee;  }
.crm-menu ul.active {
	color:#fff;
	font-weight:bold;
	background-color:#159a5a;
	background: linear-gradient(180deg, #22c55e, #15803d);
}
.crm-wrap{ width:100%; height:calc(100% - 30px); display:table; table-layout: fixed; }
.crm-wrap > ul{ display:table-cell; vertical-align:top; }
.crm-menu-wrap{ width:200px; border-right:1px solid #9c9fae; }
.crm-gap{ width:5px; border-right:1px solid #9c9fae; }
.crm-body{ padding:20px; box-sizing:border-box; background-color:#dddddd; position:relative; }
.crm-body.has-top-menu{ padding:90px 20px 20px; }
.crm-top-menu-wrap{
	width:calc(100% - 205px);
	background-color:#fff;
	height:70px;
	position:fixed;
	top:0;
	left:205px;
	right:0;
	z-index:101;
	border-bottom:1px solid #9c9fae;
	display:flex;
	align-items:center;
	gap:15px;
	padding:0 30px;
	box-sizing:border-box;
}
.crm-top-menu-wrap > ul dl dt{ font-size:12px; font-weight:500; color:#777; }
.crm-top-menu-wrap > ul dl dd b{ font-size:16px; font-weight:600; color:#000; }
.crm-top-menu-wrap > ul.warning-text{
	color:#ff0000;
	font-size:12px;
	font-weight:500;
}
.crm-top-menu-wrap > ul.right{ margin-left:auto; }
.crm-top-menu-wrap > ul + ul{
	border-left:1px solid #d9dce7;
	padding-left:15px;
}
.prd-image-storage-status { display: inline-flex; align-items: center; gap: 6px; min-width: 0; }
.prd-image-storage-badge { display: inline-flex; align-items: center; padding: 2px 7px; border: 0; border-radius: 999px; background: transparent; font-size: 11px; font-weight: 700; line-height: 1.4; cursor: default; }
.prd-image-storage-badge.is-empty { color: #92400e; background: #fef3c7; }
.prd-image-storage-badge.is-set { color: #166534; background: #dcfce7; }
.prd-image-storage-badge.is-link { cursor: pointer; }
.prd-image-storage-badge.is-link:hover { filter: brightness(.96); }
#prd_image_storage_open[hidden] { display: none; }
.prd-image-storage-modal[hidden] { display: none; }
.prd-image-storage-modal { position: fixed; z-index: 12000; inset: 0; display: flex; align-items: center; justify-content: center; padding: 18px; }
.prd-image-storage-modal__backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, .45); }
.prd-image-storage-modal__panel { position: relative; width: min(100%, 560px); padding: 18px 18px 16px; border-radius: 10px; background: #fff; box-shadow: 0 16px 40px rgba(15, 23, 42, .2); }
.prd-image-storage-modal__head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 8px; }
.prd-image-storage-modal__head h2 { margin: 0; font-size: 16px; color: #111827; }
.prd-image-storage-modal__panel > p { margin: 0; color: #6b7280; font-size: 12px; line-height: 1.5; }
.prd-image-storage-modal__form { display: flex; gap: 8px; margin-top: 14px; }
.prd-image-storage-modal__form input { box-sizing: border-box; flex: 1; min-width: 0; height: 38px; padding: 0 10px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 13px; }
.prd-image-storage-modal__help { margin-top: 8px !important; }
.prd-image-storage-modal__help code { padding: 1px 4px; border-radius: 3px; background: #f1f5f9; }
.prd-image-storage-modal__message { margin-top: 10px; padding: 9px 11px; border-radius: 5px; font-size: 12px; }
.prd-image-storage-modal__message[hidden] { display: none; }
.prd-image-storage-modal__message.is-success { color: #166534; background: #dcfce7; }
.prd-image-storage-modal__message.is-error { color: #b91c1c; background: #fee2e2; }
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
		<?php if ($img_path) { ?>
			<img src="<?= htmlspecialchars((string)$img_path, ENT_QUOTES, 'UTF-8') ?>" referrerpolicy="no-referrer" style="height:150px; border:1px solid #eee !important;">
		<?php } else { ?>
			<div style="width:150px; height:150px; border:1px solid #eee; display:flex; align-items:center; justify-content:center; color:#999;">이미지 없음</div>
		<?php } ?>
	</div>

	<div class="prd-quick-info">
		<ul class="prd-brand-name"><?= htmlspecialchars((string)($prd_data['brand_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></ul>
		<ul class="prd-name"><b><?= htmlspecialchars((string)($prd_data['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></b></ul>

        <ul class="m-t-10">
            <?= htmlspecialchars((string)($prd_data['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            <?php if (($prd_data['status'] ?? '') === '품절' && $soldOutDate !== '') { ?>
                <br><span class="text-danger">처리일 : <?= htmlspecialchars($soldOutDate, ENT_QUOTES, 'UTF-8') ?></span>
            <?php } ?>
        </ul>
        <?php if ((int)($prd_data['sale_price'] ?? 0) > 0) { ?>
        <ul class="prd-sale-price"><b><?= number_format((int)$prd_data['sale_price']) ?></b></ul>
        <?php } ?>

        <?php if (!empty($prd_data['memo'])) { ?>
        <ul class="prd-memo m-t-10"><?= htmlspecialchars((string)$prd_data['memo'], ENT_QUOTES, 'UTF-8') ?></ul>
        <?php } ?>

		<?php if (!empty($prd_data['godo_goodsNo'])) { ?>
		<ul>
			<button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall('<?= htmlspecialchars((string)$prd_data['godo_goodsNo'], ENT_QUOTES, 'UTF-8') ?>');">쑈당몰 상품보기</button>
		</ul>
		<?php } ?>

    </div>

    <?php if (!empty($prd_data['supplier_img_src'])) { ?>
    <div class="prd-img m-t-10">
		<img src="<?= htmlspecialchars((string)$prd_data['supplier_img_src'], ENT_QUOTES, 'UTF-8') ?>" referrerpolicy="no-referrer" style="height:150px; border:1px solid #eee !important;">
	</div>
    <?php } ?>

    <?php if (!empty($prd_data['supplier_site']) && !empty($prd_data['supplier_prd_pk'])) { ?>
    <div class="prd-quick-info">
		<ul class="prd-name"><b><?= htmlspecialchars((string)($prd_data['name_p'] ?? ''), ENT_QUOTES, 'UTF-8') ?></b></ul>
        <?php if (!empty($prd_data['matching_option'])) { ?>
        <ul class="">옵션 : <?= htmlspecialchars((string)$prd_data['matching_option'], ENT_QUOTES, 'UTF-8') ?></ul>
        <?php } ?>
        <ul>
			<button type="button" class="btnstyle1 btnstyle1-xs" onclick="goSupplierProduct('<?= htmlspecialchars((string)$prd_data['supplier_site'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars((string)$prd_data['supplier_prd_pk'], ENT_QUOTES, 'UTF-8') ?>');">공급사 사이트 상품보기</button>
		</ul>
        <ul>
            <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm cancel-match-btn"
                data-db1-idx="<?= $prd_idx ?>"
                data-db2-idx="<?= htmlspecialchars((string)($prd_data['supplier_prd_idx'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            >매칭취소</button>
        </ul>
    </div>
    <?php } ?>

	<div class="crm-menu m-t-10">
		<ul class="<?= $vmode === 'info' ? 'active' : '' ?>" data-mode="info">상품정보</ul>
        <ul class="<?= $vmode === 'discount_sale_log' ? 'active' : '' ?>" data-mode="discount_sale_log">할인내역</ul>
		<ul class="<?= $vmode === 'match' ? 'active' : '' ?>" data-mode="match">검색매칭</ul>
        <ul class="<?= $vmode === 'godo_inspection' ? 'active' : '' ?>" data-mode="godo_inspection">고도몰 검수 처리</ul>
        <ul class="<?= $vmode === 'content' ? 'active' : '' ?>" data-mode="content"><b>상품 컨텐츠 관리</b></ul>
        <ul class="<?= $vmode === 'log' ? 'active' : '' ?>" data-mode="log">수정로그</ul>
	</div>

</div>

<div class="crm-wrap">
	<ul class="crm-menu-wrap"></ul>
	<ul class="crm-gap "></ul>
	<ul class="crm-body">
		<div class="crm-top-menu-wrap">
			<ul>
				<dl>
					<dt>고유번호</dt>
					<dd><b class="text-blue"><?= $prd_idx ?></b></dd>
				</dl>
			</ul>
			<ul>
				<dl>
					<dt>공급사</dt>
					<dd>
						<?php if (!empty($prd_data['partner_name'])) { ?>
							<b><?= htmlspecialchars((string)$prd_data['partner_name'], ENT_QUOTES, 'UTF-8') ?></b>
						<?php } else { ?>
							<b class="text-danger">미등록</b>
						<?php } ?>
					</dd>
				</dl>
			</ul>
			<ul>
				<dl>
					<dt>브랜드</dt>
					<dd>
						<?php if (!empty($prd_data['brand_name'])) { ?>
							<b><?= htmlspecialchars((string)$prd_data['brand_name'], ENT_QUOTES, 'UTF-8') ?></b>
						<?php } else { ?>
							<b class="text-danger">미등록</b>
						<?php } ?>
					</dd>
				</dl>
			</ul>
			<ul>
				<dl>
					<dt>등록상태</dt>
					<dd><b><?= htmlspecialchars((string)($prd_data['status'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></b></dd>
				</dl>
			</ul>
			<?php if ($grade !== '') { ?>
			<ul>
				<dl>
					<dt>마진등급</dt>
					<dd>
						<span class="grade-badge grade-<?= htmlspecialchars($grade, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($grade, ENT_QUOTES, 'UTF-8') ?></span>
					</dd>
				</dl>
			</ul>
			<?php } ?>
			<?php if (!empty($prd_data['godo_goodsNo'])) { ?>
			<ul>
				<dl>
					<dt>쑈당몰 보기</dt>
					<dd>
						<button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall('<?= htmlspecialchars((string)$prd_data['godo_goodsNo'], ENT_QUOTES, 'UTF-8') ?>');">#<?= htmlspecialchars((string)$prd_data['godo_goodsNo'], ENT_QUOTES, 'UTF-8') ?></button>
					</dd>
				</dl>
			</ul>
			<ul>
				<dl>
					<dt>고도몰 관리</dt>
					<dd>
						<button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin('<?= htmlspecialchars((string)$prd_data['godo_goodsNo'], ENT_QUOTES, 'UTF-8') ?>');">#<?= htmlspecialchars((string)$prd_data['godo_goodsNo'], ENT_QUOTES, 'UTF-8') ?></button>
					</dd>
				</dl>
			</ul>
			<?php } else { ?>
			<ul class="warning-text">
				<i class="fas fa-exclamation-triangle"></i>
				<p>아직 고도몰 상품번호가 등록되지 않았습니다.</p>
				<p>고도몰 상품번호 등록해주세요.</p>
			</ul>
			<?php } ?>
			<?php if (empty($prd_data['supplier_prd_idx'])) { ?>
			<ul class="warning-text">
				<i class="fas fa-exclamation-triangle"></i>
				<p>공급사 상품이 매칭되지 않았습니다.</p>
			</ul>
			<?php } ?>

			<ul>
				<dl>
					<dt>이미지 저장소</dt>
					<dd>
						<span class="prd-image-storage-status">
							<button type="button" id="prd_image_storage_badge" class="prd-image-storage-badge <?= $imageStoragePath === '' ? 'is-empty' : 'is-set is-link' ?>">
								<?= $imageStoragePath === '' ? '미등록' : '생성완료' ?>
							</button>
							<button type="button" class="btnstyle1 btnstyle1-xs" id="prd_image_storage_open" <?= $imageStoragePath === '' ? '' : 'hidden' ?>>등록</button>
						</span>
					</dd>
				</dl>
			</ul>






			<ul class="right">
				수정일 : <?= $updatedAt !== '' ? htmlspecialchars($updatedAt, ENT_QUOTES, 'UTF-8') : '-' ?><br>
				등록일 : <?= $createdAt !== '' ? htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8') : '-' ?>
			</ul>
		</div>
		<div id="crm_body"></div>
	</ul>
</div>

<div id="prd_image_storage_modal" class="prd-image-storage-modal" hidden>
	<div class="prd-image-storage-modal__backdrop" data-image-storage-close="1"></div>
	<section class="prd-image-storage-modal__panel" role="dialog" aria-modal="true" aria-labelledby="prd_image_storage_modal_title">
		<div class="prd-image-storage-modal__head">
			<h2 id="prd_image_storage_modal_title">이미지 저장소 생성</h2>
			<button type="button" class="btnstyle1 btnstyle1-xs" data-image-storage-close="1">닫기</button>
		</div>
		<p>이 위탁상품의 이미지는 생성한 경로에만 업로드됩니다.</p>
		<form id="prd_image_storage_form" class="prd-image-storage-modal__form">
			<input type="text" id="prd_image_storage_input" value="<?= htmlspecialchars($imageStoragePath, ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" aria-label="이미지 저장소 경로">
			<button type="submit" class="btnstyle1 btnstyle1-primary">생성</button>
		</form>
		<p class="prd-image-storage-modal__help">경로는 <code>/</code>로 시작해야 하며, 상품마다 중복될 수 없습니다.</p>
		<p class="prd-image-storage-modal__help">/goods/provider/<code>상품 고유번호</code>/</p>
		<p class="prd-image-storage-modal__help">추천 저장소 : <b id="prd_image_storage_recommended"><?= htmlspecialchars($recommendedImageStoragePath, ENT_QUOTES, 'UTF-8') ?></b>
			<button type="button" class="btnstyle1 btnstyle1-xs" id="prd_image_storage_use_recommended">사용하기</button>
		</p>
		<div id="prd_image_storage_message" class="prd-image-storage-modal__message" hidden aria-live="polite"></div>
	</section>
</div>

<script>
const prd_idx = '<?= $prd_idx ?>';
const initialVmode = '<?= htmlspecialchars($vmode, ENT_QUOTES, 'UTF-8') ?>';

const prdProviderInfo = (function(){

    const API_ENDPOINTS = {
        procSave: '/admin/provider_product/save',
        info: '/admin/provider_product/detail',
        discount_sale_log: '/admin/provider_product/discount_sale_log',
        match: '/ad/ajax/prd_provider_info_match',
        cancelMatchProviderProduct: '/admin/provider_product/proc/cancel_match_provider_product/',
        loadGodoGoodsInfo: '/router/loadGodoGoodsInfo/',
        godo_inspection: '/admin/provider_product/detail_godo_inspection',
        log: '/admin/admin_action_log/list',
        content: '/admin/provider_product/detail_content',
    };
    var imageStoragePath = <?= json_encode($imageStoragePath, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var recommendedImageStoragePath = <?= json_encode($recommendedImageStoragePath, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    function view(mode){
        var endPoint = '';
        var payload = {};
        var requestMethod = 'GET';

        if (mode == 'info') {
            endPoint = API_ENDPOINTS.info;
            payload = { prd_idx : prd_idx };
        } else if (mode == 'discount_sale_log') {
            endPoint = API_ENDPOINTS.discount_sale_log;
            payload = { prd_idx : prd_idx };
        } else if (mode == 'match') {
            endPoint = API_ENDPOINTS.match;
            payload = { prd_idx : prd_idx };
        } else if (mode == 'godo_inspection') {
            endPoint = API_ENDPOINTS.godo_inspection;
            payload = { prd_idx : prd_idx };
            requestMethod = 'POST';
        } else if (mode == 'log') {
            endPoint = API_ENDPOINTS.log;
            payload = { prd_idx : prd_idx, target_type : 'prd_partner' };
        } else if (mode == 'content') {
            endPoint = API_ENDPOINTS.content;
            payload = { prd_idx : prd_idx };
        }

        ajaxRequest(endPoint, payload, {  method: requestMethod, dataType: 'html' })
            .then((getdata) => {
                $('#crm_body').html(getdata);
            })
            .catch((error) => {
                alert('뷰 변경 실패');
            });
    }

	function cancelMatchProviderProduct(db1_idx, db2_idx){
        ajaxRequest(API_ENDPOINTS.cancelMatchProviderProduct, {
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

    function loadGodoGoodsInfo(prd_idx, godo_goodsNo){
        $('html, body').scrollTop(0).css('overflow', 'hidden');
        $('#update_supplier_product_detail_loading').addClass('active');

        ajaxRequest(API_ENDPOINTS.loadGodoGoodsInfo, {
            prd_idx,
            godo_goodsNo,
        })
        .then(res => {
            if (res.status === 'success') {
                alert('고도몰 매칭 상품 정보 갱신 완료');
                location.reload();
            } else {
                alert(res.message);
            }
        })
        .catch(error => {
            console.error('AJAX 요청 실패:', error);
            alert('서버 통신에 실패했습니다.');
        })
        .always(() => {
            $('html, body').scrollTop(0).css('overflow', 'auto');
            $('#update_supplier_product_detail_loading').removeClass('active');
        });
    }

    function save(){
        var formData = new FormData(document.getElementById("prd_provider_info_form"));

        ajaxRequest(API_ENDPOINTS.procSave, formData, {
                processData: false,
                contentType: false
            })
            .done(res => {
                if (res.status == 'success') {
                    alert('저장되었습니다.');
                    location.reload();
                } else {
                    alert('저장에 실패했습니다: ' + (res.message || '알 수 없는 오류'));
                }
            })
            .catch(function(error){
                console.error('Error:', error);
                alert('저장 중 오류가 발생했습니다.');
            });
    }

    function bindImageStorage() {
        var $modal = $('#prd_image_storage_modal');
        var $input = $('#prd_image_storage_input');
        var $message = $('#prd_image_storage_message');
        if (!$modal.length) {
            return;
        }

        function showStorageMessage(text, isError) {
            $message.text(text || '')
                .removeClass('is-success is-error')
                .addClass(isError ? 'is-error' : 'is-success');
            if (text) {
                $message.removeAttr('hidden');
            } else {
                $message.attr('hidden', true);
            }
        }

        function closeStorageModal() {
            $modal.attr('hidden', true);
        }

        function openContent(tabName) {
            window.__providerContentOpenTab = tabName || 'top';
            $('.crm-menu ul').removeClass('active');
            $('.crm-menu ul[data-mode="content"]').addClass('active');
            view('content');
        }

        function renderStorageStatus(path) {
            imageStoragePath = path || '';
            var $badge = $('#prd_image_storage_badge');
            var $button = $('#prd_image_storage_open');
            if (imageStoragePath === '') {
                $badge.text('미등록').removeClass('is-set is-link').addClass('is-empty');
                $button.removeAttr('hidden');
                return;
            }
            $badge.text('생성완료').removeClass('is-empty').addClass('is-set is-link');
            $button.attr('hidden', true);
        }

        $(document).off('click.providerImageStorageOpen').on('click.providerImageStorageOpen', '#prd_image_storage_open', function() {
            $input.val(imageStoragePath || recommendedImageStoragePath);
            showStorageMessage('', false);
            $modal.removeAttr('hidden');
            $input.trigger('focus');
        });

        $(document).off('click.providerImageStorageManage').on('click.providerImageStorageManage', '#prd_image_storage_badge', function() {
            if (imageStoragePath === '') {
                return;
            }
            openContent('images');
        });

        $modal.off('click.providerImageStorage').on('click.providerImageStorage', '[data-image-storage-close]', function() {
            closeStorageModal();
        });

        $('#prd_image_storage_use_recommended').off('click.providerImageStorage').on('click.providerImageStorage', function() {
            $input.val(recommendedImageStoragePath).trigger('focus');
        });

        $(document).off('keydown.providerImageStorage').on('keydown.providerImageStorage', function(e) {
            if (e.key === 'Escape' && !$modal.attr('hidden')) {
                closeStorageModal();
            }
        });

        $('#prd_image_storage_form').off('submit.providerImageStorage').on('submit.providerImageStorage', function(event) {
            event.preventDefault();
            var storagePath = $.trim($input.val() || '').replace(/\/{2,}/g, '/');
            if (!/^\/[A-Za-z0-9._/-]*$/.test(storagePath)) {
                showStorageMessage('이미지 저장소 경로는 /로 시작하고 영문, 숫자, . _ - /만 사용할 수 있습니다.', true);
                return;
            }
            var $submit = $(this).find('button[type="submit"]');
            $submit.prop('disabled', true);
            ajaxRequest('/admin/provider_product/image_storage_path/save', {
                prd_idx: prd_idx,
                image_storage_path: storagePath
            }).done(function(res) {
                if (res && res.success) {
                    renderStorageStatus(res.image_storage_path || storagePath);
                    closeStorageModal();
                    openContent('images');
                    return;
                }
                showStorageMessage((res && (res.message || res.msg)) || '이미지 저장소를 생성하지 못했습니다.', true);
            }).fail(function(err) {
                var message = (err && err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : ((err && err.message) ? err.message : '이미지 저장소를 생성하지 못했습니다.');
                showStorageMessage(message, true);
            }).always(function() {
                $submit.prop('disabled', false);
            });
        });
    }

    return {
        view,
        cancelMatchProviderProduct,
        loadGodoGoodsInfo,
        save,
        bindImageStorage
    }

})();


$(function(){
    $('.crm-body').each(function(){
        $(this).toggleClass('has-top-menu', $(this).find('.crm-top-menu-wrap').length > 0);
    });

    prdProviderInfo.bindImageStorage();

    if (initialVmode) {
        prdProviderInfo.view(initialVmode);
    }

    $('.crm-menu ul').on('click', function(){
        const mode = $(this).data('mode');

        $(".crm-menu ul").removeClass('active');
        $(this).addClass('active');

        prdProviderInfo.view(mode);
    });

    $('.cancel-match-btn').on('click', function(){
        const db1_idx = $(this).data('db1-idx');
        const db2_idx = $(this).data('db2-idx');

		dnConfirm(
			'정말 매칭취소 하시겠습니까?',
			'취소하시면 데이터는 복구되지 않습니다.',
			() => {
                prdProviderInfo.cancelMatchProviderProduct(db1_idx, db2_idx);
            }
		);
    });

    $(document).on('click', '#loadGodoGoodsInfoBtn', function(){
        const prd_idx = $(this).data('prd-idx');
        const godo_goodsNo = $(this).data('godo-goods-no');
        prdProviderInfo.loadGodoGoodsInfo(prd_idx, godo_goodsNo);
    });
});
</script>
