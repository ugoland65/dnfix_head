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
	background-color:#2070db;
	background: linear-gradient(180deg, #0088cc, #0044cc);
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
			<img src="<?= htmlspecialchars((string)$img_path, ENT_QUOTES, 'UTF-8') ?>" style="height:150px; border:1px solid #eee !important;">
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
		<img src="<?= htmlspecialchars((string)$prd_data['supplier_img_src'], ENT_QUOTES, 'UTF-8') ?>" style="height:150px; border:1px solid #eee !important;">
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
			<ul class="right">
				수정일 : <?= $updatedAt !== '' ? htmlspecialchars($updatedAt, ENT_QUOTES, 'UTF-8') : '-' ?><br>
				등록일 : <?= $createdAt !== '' ? htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8') : '-' ?>
			</ul>
		</div>
		<div id="crm_body"></div>
	</ul>
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
        log: '/admin/admin_action_log/list',
    };

    function view(mode){
        var endPoint = '';
        var payload = {};

        if (mode == 'info') {
            endPoint = API_ENDPOINTS.info;
            payload = { prd_idx : prd_idx };
        } else if (mode == 'discount_sale_log') {
            endPoint = API_ENDPOINTS.discount_sale_log;
            payload = { prd_idx : prd_idx };
        } else if (mode == 'match') {
            endPoint = API_ENDPOINTS.match;
            payload = { prd_idx : prd_idx };
        } else if (mode == 'log') {
            endPoint = API_ENDPOINTS.log;
            payload = { prd_idx : prd_idx, target_type : 'prd_partner' };
        }

        ajaxRequest(endPoint, payload, {  method: 'GET', dataType: 'html' })
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
                } else {
                    alert('저장에 실패했습니다: ' + (res.message || '알 수 없는 오류'));
                }
            })
            .catch(function(error){
                console.error('Error:', error);
                alert('저장 중 오류가 발생했습니다.');
            });
    }

    return {
        view,
        cancelMatchProviderProduct,
        loadGodoGoodsInfo,
        save
    }

})();


$(function(){
    $('.crm-body').each(function(){
        $(this).toggleClass('has-top-menu', $(this).find('.crm-top-menu-wrap').length > 0);
    });

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
