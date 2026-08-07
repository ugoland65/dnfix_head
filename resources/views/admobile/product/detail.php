<?php
$product = $productData ?? [];
$prdKindNames = $prdKindNames ?? [];
$returnTo = (string)($returnTo ?? '/admobile/product/list');
$image = trim((string)($product['CD_IMG'] ?? ''));
if ($image !== '' && ($product['img_mode'] ?? '') !== 'out' && strpos($image, '/') !== 0) {
    $image = '/data/comparion/' . $image;
}
$packageSize = $product['cd_size_fn']['package'] ?? [];
$packageDimensions = array_filter([
    $packageSize['W'] ?? '',
    $packageSize['H'] ?? '',
    $packageSize['D'] ?? '',
], static function ($value) {
    return $value !== '';
});
$labels = $product['product_label_mappings'] ?? [];
$referenceLinks = $product['cd_reference_links'] ?? [];
$productKind = (string)($prdKindNames[$product['CD_KIND_CODE'] ?? ''] ?? ($product['CD_KIND_CODE'] ?? '-'));
$measuredProductWeight = (string)($product['cd_weight_fn']['4'] ?? '');
$measuredTotalWeight = (string)($product['cd_weight_fn']['3'] ?? '');
$weightImage = trim((string)($product['cd_add_img']['add1']['filename'] ?? ''));
$shippingImage = trim((string)($product['cd_add_img']['add3']['filename'] ?? ''));
if ($weightImage !== '') {
    $weightImage = '/data/comparion/' . $weightImage;
}
if ($shippingImage !== '') {
    $shippingImage = '/data/comparion/' . $shippingImage;
}
?>
<section class="admobile-product-detail">

<?php if (strpos($returnTo, '/admobile/order/sheet/stock?') === 0) { ?>
            <a class="admobile-product-detail__return-link" href="<?= htmlspecialchars($returnTo, ENT_QUOTES, 'UTF-8') ?>">검수 목록으로 돌아가기</a>
        <?php } ?>

    <div class="admobile-product-detail__heading">
        <a href="<?= htmlspecialchars($returnTo, ENT_QUOTES, 'UTF-8') ?>" aria-label="이전 페이지로 돌아가기">‹</a>
        <div>
            <p>상품정보</p>
            <h2><?= htmlspecialchars((string)($product['CD_NAME'] ?? '상품명 없음'), ENT_QUOTES, 'UTF-8') ?></h2>
        </div>
    </div>

    <article class="admobile-product-detail__hero">
        <div class="admobile-product-detail__image">
            <?php if ($image !== '') { ?>
                <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string)($product['CD_NAME'] ?? '상품 이미지'), ENT_QUOTES, 'UTF-8') ?>">
            <?php } else { ?>
                <span>이미지 없음</span>
            <?php } ?>
        </div>
        <div class="admobile-product-detail__badges">
            <?php if (!empty($product['sale_status'])) { ?><span><?= htmlspecialchars((string)$product['sale_status'], ENT_QUOTES, 'UTF-8') ?></span><?php } ?>
            <?php if (!empty($product['is_sale_month'])) { ?><span>월간할인</span><?php } ?>
            <?php if (!empty($product['is_sale_special'])) { ?><span>특가할인</span><?php } ?>
            <?php if (!empty($product['is_discontinued'])) { ?><span class="is-muted">단종</span><?php } ?>
        </div>
        <?php if (!empty($product['BD_NAME'])) { ?><p class="admobile-product-detail__brand"><?= htmlspecialchars((string)$product['BD_NAME'], ENT_QUOTES, 'UTF-8') ?></p><?php } ?>
        <h1><?= htmlspecialchars((string)($product['CD_NAME'] ?? '상품명 없음'), ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($product['CD_NAME_OG'])) { ?><p class="admobile-product-detail__original-name"><?= htmlspecialchars((string)$product['CD_NAME_OG'], ENT_QUOTES, 'UTF-8') ?></p><?php } ?>
        <?php if (!empty($labels)) { ?>
            <div class="admobile-product-detail__labels">
                <?php foreach ($labels as $label) { ?>
                    <?php if (!empty($label['label_name'])) { ?><span><?= htmlspecialchars((string)$label['label_name'], ENT_QUOTES, 'UTF-8') ?></span><?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
    </article>

    <section class="admobile-product-detail__section">
        <h2>검수 · 출고 이미지</h2>
        <div class="admobile-product-detail__inspection-images">
            <article>
                <h3>중량 실사 이미지</h3>
                <p>플라스틱 함유량 확인용</p>
                <?php if ($weightImage !== '') { ?>
                    <button type="button" class="admobile-product-detail__inspection-image" data-image-upload="add1" aria-label="중량 실사 이미지 촬영 또는 첨부">
                        <img src="<?= htmlspecialchars($weightImage, ENT_QUOTES, 'UTF-8') ?>" alt="중량 실사 이미지">
                        <span>사진 변경</span>
                    </button>
                    <button type="button" class="admobile-product-detail__image-preview" data-image-preview="<?= htmlspecialchars($weightImage, ENT_QUOTES, 'UTF-8') ?>" data-image-name="중량 실사 이미지">확대 보기</button>
                <?php } else { ?>
                    <button type="button" class="admobile-product-detail__inspection-image is-empty" data-image-upload="add1">등록안됨<br><span>사진 촬영 또는 첨부</span></button>
                <?php } ?>
            </article>
            <article>
                <h3>출고 이미지</h3>
                <p>출고 시 확인용 실사 사진</p>
                <?php if ($shippingImage !== '') { ?>
                    <button type="button" class="admobile-product-detail__inspection-image" data-image-upload="add3" aria-label="출고 이미지 촬영 또는 첨부">
                        <img src="<?= htmlspecialchars($shippingImage, ENT_QUOTES, 'UTF-8') ?>" alt="출고 이미지">
                        <span>사진 변경</span>
                    </button>
                    <button type="button" class="admobile-product-detail__image-preview" data-image-preview="<?= htmlspecialchars($shippingImage, ENT_QUOTES, 'UTF-8') ?>" data-image-name="출고 이미지">확대 보기</button>
                <?php } else { ?>
                    <button type="button" class="admobile-product-detail__inspection-image is-empty" data-image-upload="add3">등록안됨<br><span>사진 촬영 또는 첨부</span></button>
                <?php } ?>
            </article>
        </div>
        <input id="admobile-product-image-upload" type="file" accept="image/jpeg,image/png,image/gif,image/webp" hidden>
        <p id="admobile-product-image-upload-message" class="admobile-product-detail__upload-message" aria-live="polite"></p>
    </section>

    <section class="admobile-product-detail__section">
        <h2>기본 정보</h2>
        <dl class="admobile-product-detail__info">
            <div><dt>상품번호</dt><dd><?= number_format((int)($product['CD_IDX'] ?? 0)) ?></dd></div>
            <div><dt>상품 구분</dt><dd><?= htmlspecialchars($productKind, ENT_QUOTES, 'UTF-8') ?></dd></div>
            <div><dt>바코드</dt><dd><?= htmlspecialchars((string)($product['cd_code_fn']['jan'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd></div>
            <div><dt>고도몰 상품코드</dt><dd><?= htmlspecialchars((string)($product['cd_godo_code'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd></div>
            <div><dt>출시일</dt><dd><?= htmlspecialchars((string)($product['CD_RELEASE_DATE'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd></div>
            <div><dt>원산지</dt><dd><?= htmlspecialchars((string)($product['cd_origin'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd></div>
        </dl>
    </section>

    <section class="admobile-product-detail__section">
        <h2>재고 · 주문</h2>
        <dl class="admobile-product-detail__info">
            <div><dt>현재고</dt><dd class="is-stock"><?= number_format((int)($product['ps_stock'] ?? 0)) ?>개</dd></div>
            <div class="admobile-product-detail__rack">
                <dt>랙 코드</dt>
                <dd>
                    <strong id="admobile-product-rack-code"><?= htmlspecialchars((string)($product['ps_rack_code'] ?? '-') ?: '-', ENT_QUOTES, 'UTF-8') ?></strong>
                    <button type="button" id="admobile-product-rack-edit" data-rack-code="<?= htmlspecialchars((string)($product['ps_rack_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">변경</button>
                </dd>
            </div>
            <div><dt>재고 알림 수량</dt><dd><?= number_format((int)($product['ps_alarm_count'] ?? 0)) ?>개</dd></div>
            <div><dt>주문서 메모</dt><dd><?= htmlspecialchars((string)($product['cd_memo3'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd></div>
        </dl>
    </section>

    <section class="admobile-product-detail__section">
        <h2>상품 · 포장 정보</h2>
        <dl class="admobile-product-detail__info">
            <div class="admobile-product-detail__weight">
                <dt>실측 상품중량</dt>
                <dd><strong id="admobile-product-weight-product"><?= $measuredProductWeight !== '' ? htmlspecialchars($measuredProductWeight, ENT_QUOTES, 'UTF-8') . 'g' : '-' ?></strong></dd>
            </div>
            <div class="admobile-product-detail__weight">
                <dt>실측 전체중량</dt>
                <dd>
                    <strong id="admobile-product-weight-total"><?= $measuredTotalWeight !== '' ? htmlspecialchars($measuredTotalWeight, ENT_QUOTES, 'UTF-8') . 'g' : '-' ?></strong>
                    <button type="button" id="admobile-product-weight-edit" data-product-weight="<?= htmlspecialchars($measuredProductWeight, ENT_QUOTES, 'UTF-8') ?>" data-total-weight="<?= htmlspecialchars($measuredTotalWeight, ENT_QUOTES, 'UTF-8') ?>">변경</button>
                </dd>
            </div>
            <div><dt>패키지 크기</dt><dd><?= !empty($packageDimensions) ? htmlspecialchars(implode(' × ', $packageDimensions) . ' cm', ENT_QUOTES, 'UTF-8') : '-' ?></dd></div>
            <div><dt>패키지 부피</dt><dd><?= !empty($product['package_volume_m3']) ? htmlspecialchars((string)$product['package_volume_m3'], ENT_QUOTES, 'UTF-8') . 'm³' : '-' ?></dd></div>
            <div><dt>플라스틱 함유량</dt><dd><?= !empty($product['cd_size_fn']['import']['plastic']) ? htmlspecialchars((string)$product['cd_size_fn']['import']['plastic'], ENT_QUOTES, 'UTF-8') . '%' : '-' ?></dd></div>
        </dl>
    </section>

    <?php if (!empty($product['cd_memo2']) || !empty($referenceLinks)) { ?>
        <section class="admobile-product-detail__section">
            <h2>참고 정보</h2>
            <?php if (!empty($product['cd_memo2'])) { ?><p class="admobile-product-detail__memo"><?= nl2br(htmlspecialchars((string)$product['cd_memo2'], ENT_QUOTES, 'UTF-8')) ?></p><?php } ?>
            <?php if (!empty($referenceLinks)) { ?>
                <ul class="admobile-product-detail__links">
                    <?php foreach ($referenceLinks as $referenceLink) { ?>
                        <?php $url = trim((string)($referenceLink['url'] ?? '')); ?>
                        <?php if ($url !== '') { ?><li><a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars((string)($referenceLink['title'] ?? $url), ENT_QUOTES, 'UTF-8') ?></a></li><?php } ?>
                    <?php } ?>
                </ul>
            <?php } ?>
        </section>
    <?php } ?>
</section>

<div id="admobile-product-image-modal" class="admobile-product-detail-modal" hidden>
    <div class="admobile-product-detail-modal__backdrop" data-action="close"></div>
    <section class="admobile-product-detail-modal__content" role="dialog" aria-modal="true" aria-labelledby="admobile-product-image-modal-title">
        <button type="button" class="admobile-product-detail-modal__close" data-action="close" aria-label="이미지 닫기">닫기</button>
        <img id="admobile-product-image-modal-image" src="" alt="">
        <p id="admobile-product-image-modal-title"></p>
    </section>
</div>

<div id="admobile-product-rack-modal" class="admobile-product-rack-modal" hidden>
    <div class="admobile-product-rack-modal__backdrop" data-action="close"></div>
    <section class="admobile-product-rack-modal__content" role="dialog" aria-modal="true" aria-labelledby="admobile-product-rack-title">
        <form id="admobile-product-rack-form">
            <div class="admobile-product-rack-modal__heading">
                <div>
                    <p>재고 · 주문</p>
                    <h2 id="admobile-product-rack-title">랙 코드 변경</h2>
                </div>
                <button type="button" data-action="close" aria-label="랙 코드 변경 닫기">×</button>
            </div>
            <label for="admobile-product-rack-input">새 랙 코드</label>
            <input id="admobile-product-rack-input" type="text" maxlength="100" autocomplete="off" placeholder="예) A-01-02">
            <p id="admobile-product-rack-message" class="admobile-product-rack-modal__message" aria-live="polite"></p>
            <div class="admobile-product-rack-modal__actions">
                <button type="button" data-action="close">취소</button>
                <button type="submit">저장</button>
            </div>
        </form>
    </section>
</div>

<div id="admobile-product-weight-modal" class="admobile-product-weight-modal" hidden>
    <div class="admobile-product-weight-modal__backdrop" data-action="close"></div>
    <section class="admobile-product-weight-modal__content" role="dialog" aria-modal="true" aria-labelledby="admobile-product-weight-title">
        <form id="admobile-product-weight-form">
            <div class="admobile-product-weight-modal__heading">
                <div>
                    <p>상품 · 포장 정보</p>
                    <h2 id="admobile-product-weight-title">실측 중량 변경</h2>
                </div>
                <button type="button" data-action="close" aria-label="실측 중량 변경 닫기">×</button>
            </div>
            <label for="admobile-product-weight-product-input">실측 상품중량 <span>패키지 제외</span></label>
            <div class="admobile-product-weight-modal__input"><input id="admobile-product-weight-product-input" type="number" min="0" max="1000000" step="0.1" inputmode="decimal" placeholder="0"><span>g</span></div>
            <label for="admobile-product-weight-total-input">실측 전체중량 <span>패키지 포함</span></label>
            <div class="admobile-product-weight-modal__input"><input id="admobile-product-weight-total-input" type="number" min="0" max="1000000" step="0.1" inputmode="decimal" placeholder="0"><span>g</span></div>
            <p id="admobile-product-weight-message" class="admobile-product-weight-modal__message" aria-live="polite"></p>
            <div class="admobile-product-weight-modal__actions">
                <button type="button" data-action="close">취소</button>
                <button type="submit">저장</button>
            </div>
        </form>
    </section>
</div>

<style>
    .admobile-product-detail { padding-bottom: 28px; }
    .admobile-product-detail__heading { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
    .admobile-product-detail__heading > a { color: #344054; font-size: 30px; line-height: 24px; text-decoration: none; }
    .admobile-product-detail__heading p { margin: 0 0 3px; color: #667085; font-size: 12px; font-weight: 700; }
    .admobile-product-detail__heading h2 { margin: 0; font-size: 18px; }
    .admobile-product-detail__return-link { display: block; width: 100%; margin: 0 0 12px; padding: 12px; border: 1px solid #8fa5d3; border-radius: 8px; background: #f7f9ff; color: #2c4d92; font-size: 14px; font-weight: 700; text-align: center; text-decoration: none; }
    .admobile-product-detail__hero, .admobile-product-detail__section { margin-bottom: 12px; padding: 15px; border: 1px solid #e3e7ee; border-radius: 12px; background: #fff; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
    .admobile-product-detail__image { display: flex; align-items: center; justify-content: center; width: min(100%, 280px); height: 280px; margin: 0 auto 14px; overflow: hidden; border: 1px solid #edf0f4; border-radius: 10px; background: #f8fafc; color: #98a2b3; font-size: 12px; }
    .admobile-product-detail__image img { width: 100%; height: 100%; object-fit: contain; }
    .admobile-product-detail__badges, .admobile-product-detail__labels { display: flex; flex-wrap: wrap; gap: 5px; }
    .admobile-product-detail__badges span, .admobile-product-detail__labels span { padding: 3px 7px; border-radius: 999px; background: #e4ebfb; color: #2c4d92; font-size: 11px; font-weight: 700; }
    .admobile-product-detail__badges .is-muted { background: #eef1f5; color: #667085; }
    .admobile-product-detail__brand { margin: 10px 0 4px; color: #667085; font-size: 13px; }
    .admobile-product-detail__hero h1 { margin: 0; color: #172033; font-size: 20px; line-height: 1.45; }
    .admobile-product-detail__original-name { margin: 7px 0 0; color: #667085; font-size: 13px; }
    .admobile-product-detail__labels { margin-top: 11px; }
    .admobile-product-detail__labels span { background: #f2f4f7; color: #475467; }
    .admobile-product-detail__section h2 { margin: 0 0 12px; color: #172033; font-size: 15px; }
    .admobile-product-detail__inspection-images { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px; }
    .admobile-product-detail__inspection-images article { min-width: 0; }
    .admobile-product-detail__inspection-images h3 { margin: 0; color: #344054; font-size: 13px; }
    .admobile-product-detail__inspection-images p { min-height: 30px; margin: 4px 0 7px; color: #98a2b3; font-size: 10px; line-height: 1.4; }
    .admobile-product-detail__inspection-image { position: relative; display: flex; align-items: center; justify-content: center; width: 100%; aspect-ratio: 1; overflow: hidden; padding: 0; border: 1px solid #d9e1ef; border-radius: 8px; background: #f8fafc; color: #98a2b3; font: inherit; font-size: 12px; }
    .admobile-product-detail__inspection-image:not(.is-empty) { cursor: pointer; }
    .admobile-product-detail__inspection-image img { width: 100%; height: 100%; object-fit: cover; }
    .admobile-product-detail__inspection-image span { position: absolute; right: 6px; bottom: 6px; padding: 3px 5px; border-radius: 4px; background: rgba(23, 43, 77, .78); color: #fff; font-size: 10px; }
    .admobile-product-detail__inspection-image.is-empty { border-style: dashed; color: #98a2b3; }
    .admobile-product-detail__inspection-image.is-empty span { position: static; margin-top: 5px; padding: 0; background: none; color: #667085; font-size: 10px; }
    .admobile-product-detail__image-preview { display: block; width: 100%; margin-top: 6px; padding: 6px; border: 1px solid #d9e1ef; border-radius: 6px; background: #fff; color: #475467; font: inherit; font-size: 11px; font-weight: 700; }
    .admobile-product-detail__upload-message { min-height: 16px; margin: 9px 0 0; color: #067647; font-size: 12px; text-align: center; }
    .admobile-product-detail__info { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0; margin: 0; border: 1px solid #edf0f4; border-radius: 8px; overflow: hidden; }
    .admobile-product-detail__info > div { min-width: 0; padding: 10px; border-right: 1px solid #edf0f4; border-bottom: 1px solid #edf0f4; }
    .admobile-product-detail__info > div:nth-child(2n) { border-right: 0; }
    .admobile-product-detail__info > div:nth-last-child(-n+2) { border-bottom: 0; }
    .admobile-product-detail__info dt { margin-bottom: 4px; color: #98a2b3; font-size: 10px; }
    .admobile-product-detail__info dd { overflow-wrap: anywhere; margin: 0; color: #344054; font-size: 13px; line-height: 1.4; }
    .admobile-product-detail__info .is-stock { color: #2450a6; font-size: 16px; font-weight: 800; }
    .admobile-product-detail__rack { background: #f1f5ff; }
    .admobile-product-detail__rack dt { color: #2c4d92; font-weight: 700; }
    .admobile-product-detail__rack dd { display: flex; align-items: center; justify-content: space-between; gap: 7px; }
    .admobile-product-detail__rack strong { overflow-wrap: anywhere; color: #173d8f; font-family: monospace; font-size: 19px; font-weight: 800; letter-spacing: .5px; }
    .admobile-product-detail__rack button { flex: 0 0 auto; padding: 5px 7px; border: 1px solid #8fa5d3; border-radius: 5px; background: #fff; color: #2c4d92; font: inherit; font-size: 11px; font-weight: 700; }
    .admobile-product-detail__weight { background: #fbfcff; }
    .admobile-product-detail__weight dt { color: #667085; font-weight: 700; }
    .admobile-product-detail__weight dd { display: flex; align-items: center; justify-content: space-between; gap: 7px; }
    .admobile-product-detail__weight strong { color: #2450a6; font-size: 16px; font-weight: 800; }
    .admobile-product-detail__weight button { flex: 0 0 auto; padding: 5px 7px; border: 1px solid #aab9d6; border-radius: 5px; background: #fff; color: #2c4d92; font: inherit; font-size: 11px; font-weight: 700; }
    .admobile-product-detail__memo { margin: 0; padding: 10px; border-left: 3px solid #aab9d6; border-radius: 0 6px 6px 0; background: #f7f9fc; color: #475467; font-size: 13px; line-height: 1.5; }
    .admobile-product-detail__links { display: grid; gap: 7px; margin: 10px 0 0; padding: 0; list-style: none; }
    .admobile-product-detail__links a { display: block; overflow: hidden; padding: 9px; border: 1px solid #d9e1ef; border-radius: 7px; color: #2c4d92; font-size: 12px; text-decoration: none; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-product-detail-modal[hidden] { display: none; }
    .admobile-product-detail-modal { position: fixed; z-index: 1200; inset: 0; display: flex; align-items: center; justify-content: center; padding: 18px; }
    .admobile-product-detail-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .78); }
    .admobile-product-detail-modal__content { position: relative; width: min(100%, 640px); max-height: 90vh; padding: 42px 12px 12px; border-radius: 12px; background: #fff; text-align: center; }
    .admobile-product-detail-modal__content img { display: block; width: 100%; max-height: calc(90vh - 94px); object-fit: contain; }
    .admobile-product-detail-modal__content p { margin: 9px 0 0; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-product-detail-modal__close { position: absolute; top: 8px; right: 8px; padding: 7px 10px; border: 0; border-radius: 6px; background: #344054; color: #fff; font-size: 12px; font-weight: 700; }
    .admobile-product-rack-modal[hidden] { display: none; }
    .admobile-product-rack-modal { position: fixed; z-index: 1200; inset: 0; display: flex; align-items: flex-end; }
    .admobile-product-rack-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .58); }
    .admobile-product-rack-modal__content { position: relative; width: 100%; padding: 20px 16px calc(16px + env(safe-area-inset-bottom)); border-radius: 18px 18px 0 0; background: #fff; }
    .admobile-product-rack-modal__heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .admobile-product-rack-modal__heading p { margin: 0 0 3px; color: #667085; font-size: 12px; font-weight: 700; }
    .admobile-product-rack-modal__heading h2 { margin: 0; color: #172033; font-size: 19px; }
    .admobile-product-rack-modal__heading button { width: 34px; height: 34px; border: 0; border-radius: 50%; background: #f2f4f7; color: #344054; font-size: 23px; line-height: 1; }
    .admobile-product-rack-modal label { display: block; margin: 18px 0 7px; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-product-rack-modal input { width: 100%; padding: 13px; border: 1px solid #8fa5d3; border-radius: 8px; color: #172033; font: inherit; font-family: monospace; font-size: 20px; font-weight: 700; letter-spacing: .5px; outline-color: #3056a8; }
    .admobile-product-rack-modal__message { min-height: 16px; margin: 8px 0 0; color: #b42318; font-size: 12px; }
    .admobile-product-rack-modal__actions { display: grid; grid-template-columns: 88px 1fr; gap: 8px; margin-top: 10px; }
    .admobile-product-rack-modal__actions button { min-height: 46px; border: 0; border-radius: 8px; background: #eef1f5; color: #475467; font: inherit; font-size: 14px; font-weight: 700; }
    .admobile-product-rack-modal__actions button[type="submit"] { background: #3056a8; color: #fff; }
    .admobile-product-weight-modal[hidden] { display: none; }
    .admobile-product-weight-modal { position: fixed; z-index: 1200; inset: 0; display: flex; align-items: flex-end; }
    .admobile-product-weight-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .58); }
    .admobile-product-weight-modal__content { position: relative; width: 100%; padding: 20px 16px calc(16px + env(safe-area-inset-bottom)); border-radius: 18px 18px 0 0; background: #fff; }
    .admobile-product-weight-modal__heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .admobile-product-weight-modal__heading p { margin: 0 0 3px; color: #667085; font-size: 12px; font-weight: 700; }
    .admobile-product-weight-modal__heading h2 { margin: 0; color: #172033; font-size: 19px; }
    .admobile-product-weight-modal__heading button { width: 34px; height: 34px; border: 0; border-radius: 50%; background: #f2f4f7; color: #344054; font-size: 23px; line-height: 1; }
    .admobile-product-weight-modal label { display: block; margin: 16px 0 7px; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-product-weight-modal label span { margin-left: 4px; color: #98a2b3; font-size: 11px; font-weight: 400; }
    .admobile-product-weight-modal__input { display: flex; align-items: center; overflow: hidden; border: 1px solid #aab9d6; border-radius: 8px; }
    .admobile-product-weight-modal__input input { width: 100%; min-width: 0; padding: 12px; border: 0; color: #172033; font: inherit; font-size: 20px; font-weight: 700; outline-color: #3056a8; }
    .admobile-product-weight-modal__input span { padding: 0 12px; color: #667085; font-size: 13px; font-weight: 700; }
    .admobile-product-weight-modal__message { min-height: 16px; margin: 8px 0 0; color: #b42318; font-size: 12px; }
    .admobile-product-weight-modal__actions { display: grid; grid-template-columns: 88px 1fr; gap: 8px; margin-top: 10px; }
    .admobile-product-weight-modal__actions button { min-height: 46px; border: 0; border-radius: 8px; background: #eef1f5; color: #475467; font: inherit; font-size: 14px; font-weight: 700; }
    .admobile-product-weight-modal__actions button[type="submit"] { background: #3056a8; color: #fff; }
</style>

<script>
    (function() {
        var modal = document.getElementById('admobile-product-image-modal');
        var image = document.getElementById('admobile-product-image-modal-image');
        var title = document.getElementById('admobile-product-image-modal-title');

        document.querySelectorAll('[data-image-preview]').forEach(function(button) {
            button.addEventListener('click', function() {
                image.src = button.dataset.imagePreview || '';
                image.alt = button.dataset.imageName || '상품 이미지';
                title.textContent = button.dataset.imageName || '';
                modal.hidden = false;
            });
        });

        modal.addEventListener('click', function(event) {
            if (event.target.closest('[data-action="close"]')) {
                modal.hidden = true;
                image.src = '';
            }
        });
    })();
</script>

<script>
    (function() {
        var modal = document.getElementById('admobile-product-weight-modal');
        var form = document.getElementById('admobile-product-weight-form');
        var editButton = document.getElementById('admobile-product-weight-edit');
        var productInput = document.getElementById('admobile-product-weight-product-input');
        var totalInput = document.getElementById('admobile-product-weight-total-input');
        var message = document.getElementById('admobile-product-weight-message');
        var productWeight = document.getElementById('admobile-product-weight-product');
        var totalWeight = document.getElementById('admobile-product-weight-total');
        var prdIdx = <?= (int)($product['CD_IDX'] ?? 0) ?>;

        function closeModal() {
            modal.hidden = true;
        }

        function toDisplayWeight(value) {
            return value === '' ? '-' : value + 'g';
        }

        editButton.addEventListener('click', function() {
            productInput.value = editButton.dataset.productWeight || '';
            totalInput.value = editButton.dataset.totalWeight || '';
            message.textContent = '';
            modal.hidden = false;
            window.setTimeout(function() { productInput.focus(); }, 100);
        });

        modal.addEventListener('click', function(event) {
            if (event.target.closest('[data-action="close"]')) {
                closeModal();
            }
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            var saveButton = form.querySelector('[type="submit"]');
            saveButton.disabled = true;
            message.textContent = '';

            fetch('/admobile/product/action', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({
                    action_mode: 'measured_weight_update',
                    prd_idx: prdIdx,
                    product_weight: productInput.value,
                    total_weight: totalInput.value
                })
            })
                .then(function(response) { return response.json(); })
                .then(function(result) {
                    if (!result.success) {
                        throw new Error(result.message || '실측 중량 변경에 실패했습니다.');
                    }

                    var weights = result.weights || {};
                    var productValue = weights.product_weight || '';
                    var totalValue = weights.total_weight || '';
                    editButton.dataset.productWeight = productValue;
                    editButton.dataset.totalWeight = totalValue;
                    productWeight.textContent = toDisplayWeight(productValue);
                    totalWeight.textContent = toDisplayWeight(totalValue);
                    closeModal();
                })
                .catch(function(error) {
                    message.textContent = error.message;
                })
                .finally(function() {
                    saveButton.disabled = false;
                });
        });
    })();
</script>

<script>
    (function() {
        var modal = document.getElementById('admobile-product-rack-modal');
        var form = document.getElementById('admobile-product-rack-form');
        var editButton = document.getElementById('admobile-product-rack-edit');
        var input = document.getElementById('admobile-product-rack-input');
        var message = document.getElementById('admobile-product-rack-message');
        var rackCode = document.getElementById('admobile-product-rack-code');
        var prdIdx = <?= (int)($product['CD_IDX'] ?? 0) ?>;

        function closeModal() {
            modal.hidden = true;
        }

        editButton.addEventListener('click', function() {
            input.value = editButton.dataset.rackCode || '';
            message.textContent = '';
            modal.hidden = false;
            window.setTimeout(function() { input.focus(); }, 100);
        });

        modal.addEventListener('click', function(event) {
            if (event.target.closest('[data-action="close"]')) {
                closeModal();
            }
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            var saveButton = form.querySelector('[type="submit"]');
            saveButton.disabled = true;
            message.textContent = '';

            fetch('/admobile/product/action', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({
                    action_mode: 'rack_code_update',
                    prd_idx: prdIdx,
                    rack_code: input.value
                })
            })
                .then(function(response) { return response.json(); })
                .then(function(result) {
                    if (!result.success) {
                        throw new Error(result.message || '랙 코드 변경에 실패했습니다.');
                    }

                    var value = result.rack_code || '';
                    editButton.dataset.rackCode = value;
                    rackCode.textContent = value || '-';
                    closeModal();
                })
                .catch(function(error) {
                    message.textContent = error.message;
                })
                .finally(function() {
                    saveButton.disabled = false;
                });
        });
    })();
</script>

<script>
    (function() {
        var uploadInput = document.getElementById('admobile-product-image-upload');
        var uploadMessage = document.getElementById('admobile-product-image-upload-message');
        var imageType = '';
        var activeButton = null;
        var prdIdx = <?= (int)($product['CD_IDX'] ?? 0) ?>;

        document.querySelectorAll('[data-image-upload]').forEach(function(button) {
            button.addEventListener('click', function() {
                imageType = button.dataset.imageUpload || '';
                activeButton = button;
                uploadMessage.textContent = '';
                uploadInput.value = '';
                uploadInput.click();
            });
        });

        uploadInput.addEventListener('change', function() {
            var file = uploadInput.files[0];
            if (!file || !imageType) {
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                uploadMessage.textContent = '이미지는 10MB 이하만 업로드할 수 있습니다.';
                uploadMessage.style.color = '#b42318';
                return;
            }

            activeButton.disabled = true;
            uploadMessage.style.color = '#667085';
            uploadMessage.textContent = '이미지를 업로드하고 있습니다...';
            var formData = new FormData();
            formData.append('action_mode', 'inspection_image_upload');
            formData.append('prd_idx', prdIdx);
            formData.append('image_type', imageType);
            formData.append('image', file);

            fetch('/admobile/product/action', {
                method: 'POST',
                body: formData
            })
                .then(function(response) { return response.json(); })
                .then(function(result) {
                    if (!result.success) {
                        throw new Error(result.message || '이미지 업로드에 실패했습니다.');
                    }
                    uploadMessage.style.color = '#067647';
                    uploadMessage.textContent = result.message;
                    window.setTimeout(function() {
                        window.location.reload();
                    }, 400);
                })
                .catch(function(error) {
                    uploadMessage.style.color = '#b42318';
                    uploadMessage.textContent = error.message;
                    activeButton.disabled = false;
                });
        });
    })();
</script>
