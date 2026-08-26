<?php
$categoryGuidePrefix = (string)($categoryGuidePrefix ?? '');
$selectedKindCode = (string)($selectedKindCode ?? '');
$selectedSecondKindKey = (string)($selectedSecondKindKey ?? '');
$selectedThirdKindKey = (string)($selectedThirdKindKey ?? '');
$isCategoryGuideModal = (($categoryGuideMode ?? '') === 'modal');
$guideId = static function (string $name) use ($categoryGuidePrefix): string {
    return $categoryGuidePrefix . $name;
};
?>
<style>
    .torso-third-category-guide { max-width: 680px; margin-top: 12px; padding: 14px; border: 1px solid #dbe5f5; border-radius: 8px; background: #f8fbff; }
    .torso-third-category-guide h3 { margin: 0 0 10px; color: #1e3a5f; font-size: 14px; }
    .torso-third-category-guide__list { display: grid; gap: 8px; }
    .torso-third-category-guide__item { display: flex; align-items: center; gap: 12px; padding: 8px; border: 1px solid #e3eaf5; border-radius: 6px; background: #fff; }
    .torso-third-category-guide__item img { flex: 0 0 86px; width: 86px; height: 86px; object-fit: cover; border-radius: 4px; }
    .torso-third-category-guide__item strong { display: block; margin-bottom: 4px; color: #1f2937; font-size: 13px; }
    .torso-third-category-guide__item p { margin: 0; color: #64748b; font-size: 12px; line-height: 1.45; }
    .category-guide-description { margin-bottom: 10px; padding: 9px 12px; border: 1px solid #fde68a; border-radius: 6px; background: #fffbeb; color: #92400e; font-size: 12px; line-height: 1.5; }
    .category-guide-description ul { margin: 0; padding-left: 18px; }
    .category-guide-description li { margin: 0; }
    .category-guide-accordion-section { margin-bottom: 10px; border: 1px solid #dbe3ee; border-radius: 8px; background: #fff; overflow: hidden; }
    .category-guide-accordion-title { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 13px 15px; border: 0; background: #f8fafc; color: #1e3a5f; font-size: 14px; font-weight: 700; text-align: left; cursor: pointer; }
    .category-guide-accordion-title:hover { background: #f1f5f9; }
    .category-guide-accordion-icon { transition: transform .2s ease; }
    .category-guide-accordion-section.is-open .category-guide-accordion-icon { transform: rotate(180deg); }
    .category-guide-accordion-content { display: none; padding: 14px; }
    .category-guide-accordion-section.is-open .category-guide-accordion-content { display: block; }
    .category-guide-modal .torso-third-category-guide { max-width: none; margin-top: 0; padding: 0; border-color: #dbe3ee; background: #fff; }
    .category-guide-modal .torso-third-category-guide h3 { margin: 0; }
</style>
<div
    id="<?= htmlspecialchars($guideId('onahole-second-category-guide'), ENT_QUOTES, 'UTF-8') ?>"
    class="<?= $isCategoryGuideModal ? 'category-guide-accordion-section is-open' : '' ?>"
    data-category-kind="ONAHOLE"
    style="<?= (!$isCategoryGuideModal && !($selectedKindCode === 'ONAHOLE' && $selectedSecondKindKey === '')) ? 'display:none;' : '' ?>">
    <?php if ($isCategoryGuideModal) { ?>
        <button type="button" class="category-guide-accordion-title" aria-expanded="true">
            <span>오나홀 2차 카테고리</span>
            <span class="category-guide-accordion-icon" aria-hidden="true">⌄</span>
        </button>
    <?php } else { ?>
        <h3>오나홀 2차 카테고리</h3>
    <?php } ?>
    <div class="<?= $isCategoryGuideModal ? 'category-guide-accordion-content' : '' ?>">
        <ul>
            콤팩트 - 100g 이하<br>핸디형 S - (100~299g)<br>핸디형 M - (300~650g)<br>핸디형 L - (650g 이상 ~ 1kg)<br>중/대형 - (1kg 이상)<br>전동/자동형<br>페라형<br>컵홀형<br>바닥오나<br>신체/페티쉬<br>면타입<br>
        </ul>
    </div>
</div>
<div
    id="<?= htmlspecialchars($guideId('torso-third-category-guide'), ENT_QUOTES, 'UTF-8') ?>"
    class="torso-third-category-guide<?= $isCategoryGuideModal ? ' category-guide-accordion-section' : '' ?>"
    data-category-kind="TORSO"
    style="<?= (!$isCategoryGuideModal && !($selectedKindCode === 'TORSO' && $selectedSecondKindKey === 'TORSO' && $selectedThirdKindKey === '')) ? 'display:none;' : '' ?>">
    <?php if ($isCategoryGuideModal) { ?>
        <button type="button" class="category-guide-accordion-title" aria-expanded="false">
            <span>토르소형 3차 카테고리 - 분류기준</span>
            <span class="category-guide-accordion-icon" aria-hidden="true">⌄</span>
        </button>
    <?php } else { ?>
        <h3>토르소형 3차 카테고리 - 분류기준</h3>
    <?php } ?>
    <div class="<?= $isCategoryGuideModal ? 'category-guide-accordion-content' : '' ?>">
        <div class="category-guide-description">
            <ul>
                <li>만약 무게가 소수점일경우 반올림해서 기준을 맞춰주세요. 예) 9.5kg -> 10kg</li>
            </ul>
        </div>
        <div class="torso-third-category-guide__list">
            <div class="torso-third-category-guide__item">
                <img src="https://cdn-saas-web-203-195.cdn-nhncommerce.com/dnfix202439_godomall_com/data/category/022007003_cateOverImg_goods.jpg" alt="미니 토르소" loading="lazy">
                <div>
                    <strong>미니 토르소</strong>
                    <p>5kg 미만</p>
                </div>
            </div>
            <div class="torso-third-category-guide__item">
                <img src="https://cdn-saas-web-203-195.cdn-nhncommerce.com/dnfix202439_godomall_com/data/category/022007002_cateOverImg_goods.jpg" alt="스탠다드 토르소" loading="lazy">
                <div>
                    <strong>라이트 토르소</strong>
                    <p>5kg 이상 ~ 10kg 이하</p>
                </div>
            </div>
            <div class="torso-third-category-guide__item">
                <img src="https://cdn-saas-web-203-195.cdn-nhncommerce.com/dnfix202439_godomall_com/data/category/022007001_cateOverImg_goods.jpg" alt="대형 토르소" loading="lazy">
                <div>
                    <strong>리얼 토르소</strong>
                    <p>무게 10kg 초과, 신체비율과 1:1 비율이거나 커야함</p>
                </div>
            </div>
        </div>
    </div>
</div>
