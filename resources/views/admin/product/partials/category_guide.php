<?php
$categoryGuidePrefix = (string)($categoryGuidePrefix ?? '');
$selectedKindCode = (string)($selectedKindCode ?? '');
$selectedSecondKindKey = (string)($selectedSecondKindKey ?? '');
$selectedThirdKindKey = (string)($selectedThirdKindKey ?? '');
$isCategoryGuideModal = (($categoryGuideMode ?? '') === 'modal');
$guideId = static function (string $name) use ($categoryGuidePrefix): string {
    return $categoryGuidePrefix . $name;
};
$formatGuideHtml = static function (string $text): string {
    $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    return str_replace(
        ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;', '&lt;b&gt;', '&lt;/b&gt;', '&lt;strong&gt;', '&lt;/strong&gt;', '&lt;span&gt;', '&lt;/span&gt;'],
        ['<br>', '<br>', '<br>', '<b>', '</b>', '<strong>', '</strong>', '<span>', '</span>'],
        $escaped
    );
};
?>
<style>
    .torso-third-category-guide { max-width: 680px; margin-top: 12px; padding: 14px; border: 1px solid #dbe5f5; border-radius: 8px; background: #f8fbff; }
    .torso-third-category-guide h3 { margin: 0 0 10px; color: #1e3a5f; font-size: 14px; }
    .torso-third-category-guide__list { display: grid; gap: 8px; }
    .torso-third-category-guide__item { display: flex; align-items: center; gap: 12px; padding: 8px; border: 1px solid #e3eaf5; border-radius: 6px; background: #fff; }
    .torso-third-category-guide__item img,
    .torso-third-category-guide__image-placeholder { flex: 0 0 86px; width: 86px; height: 86px; border-radius: 4px; }
    .torso-third-category-guide__item img { display: block; object-fit: cover; background: #f8fafc; }
    .torso-third-category-guide__image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #cbd5e1;
        background: repeating-linear-gradient(-45deg, #f8fafc, #f8fafc 7px, #eef2f7 7px, #eef2f7 14px);
        color: #94a3b8;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.4;
        text-align: center;
    }
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
    class="torso-third-category-guide<?= $isCategoryGuideModal ? ' category-guide-accordion-section is-open' : '' ?>"
    data-category-kind="ONAHOLE"
    data-category-second=""
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

        <div class="category-guide-description">
            <ul>
                <li>가슴에 니플퍽 기능이 있어도 메인 카테고리는 토르소 > 가슴형 입니다.</li>
            </ul>
        </div>

        <?php
        $onaholeSecondCategoryGuides = [
            ['name' => '콤팩트', 'description' => '100g 이하', 'image' => ''],
            ['name' => '핸디형 S', 'description' => '100~299g', 'image' => ''],
            ['name' => '핸디형 M', 'description' => '300~650g', 'image' => ''],
            ['name' => '핸디형 L', 'description' => '650g 이상 ~ 1kg', 'image' => ''],
            ['name' => '중/대형', 'description' => '1kg 이상', 'image' => ''],
            ['name' => '전동/자동형', 'description' => '', 'image' => ''],
            ['name' => '페라형', 'description' => '만약 펠라와 홀이 같이 있는 제품일 경우 펠라형 우선!<br>펠라형이라고 이름이 붙어있어도 입, 입술 조형이 없으면 일반 오나홀로 분류', 'image' => ''],
            ['name' => '컵홀형', 'description' => '', 'image' => ''],
            ['name' => '바닥오나', 'description' => '', 'image' => ''],
            ['name' => '신체/페티쉬', 'description' => '', 'image' => ''],
            ['name' => '면타입', 'description' => '', 'image' => ''],
        ];
        ?>
        <div class="torso-third-category-guide__list">
            <?php foreach ($onaholeSecondCategoryGuides as $onaholeGuide) {
                $guideName = trim((string)($onaholeGuide['name'] ?? ''));
                $guideDescription = trim((string)($onaholeGuide['description'] ?? ''));
                $guideImage = trim((string)($onaholeGuide['image'] ?? ''));
                if ($guideName === '') {
                    continue;
                }
            ?>
            <div class="torso-third-category-guide__item">
                <?php if ($guideImage !== '') { ?>
                    <img src="<?= htmlspecialchars($guideImage, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($guideName, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" referrerpolicy="no-referrer">
                <?php } else { ?>
                    <div class="torso-third-category-guide__image-placeholder" aria-hidden="true">이미지<br>준비중</div>
                <?php } ?>
                <div>
                    <strong><?= htmlspecialchars($guideName, ENT_QUOTES, 'UTF-8') ?></strong>
                    <?php if ($guideDescription !== '') { ?>
                        <p><?= $formatGuideHtml($guideDescription) ?></p>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<div
    id="<?= htmlspecialchars($guideId('torso-second-category-guide'), ENT_QUOTES, 'UTF-8') ?>"
    class="torso-third-category-guide<?= $isCategoryGuideModal ? ' category-guide-accordion-section' : '' ?>"
    data-category-kind="TORSO"
    data-category-second=""
    style="<?= (!$isCategoryGuideModal && !($selectedKindCode === 'TORSO' && $selectedSecondKindKey === '')) ? 'display:none;' : '' ?>">
    <?php if ($isCategoryGuideModal) { ?>
        <button type="button" class="category-guide-accordion-title" aria-expanded="false">
            <span>토르소형 2차 카테고리 - 분류기준</span>
            <span class="category-guide-accordion-icon" aria-hidden="true">⌄</span>
        </button>
    <?php } else { ?>
        <h3>토르소형 2차 카테고리 - 분류기준</h3>
    <?php } ?>
    <div class="<?= $isCategoryGuideModal ? 'category-guide-accordion-content' : '' ?>">
        <div class="category-guide-description">
            <ul>
                <li>가슴과 엉덩이가 한 몸으로 이어져 있으면 토르소형입니다.</li>
                <li>퍼리·피규어 조형이면 형태보다 퍼리/피규어를 우선합니다. (얼굴있으면 피큐어)</li>
                <li>토르소형은 선택한 뒤 3차(미니/라이트/리얼)로 다시 나눕니다.</li>
            </ul>
        </div>
        <?php
        $torsoSecondCategoryGuides = [
            ['name' => '토르소형', 'description' => '가슴·허리·엉덩이가 한 몸으로 이어진 몸통 형태', 'image' => 'https://cdn-saas-web-203-195.cdn-nhncommerce.com/dnfix202439_godomall_com/data/category/022007002_cateOverImg_goods.jpg'],
            ['name' => '가슴장난감', 'description' => '가슴만 독립적으로 조형된 제품', 'image' => ''],
            ['name' => '엉덩이형', 'description' => '엉덩이(힙) 위주 조형. 다리가 거의 없거나 매우 짧은 경우', 'image' => ''],
            ['name' => '하반신형', 'description' => '허리 아래부터 허벅지·다리까지 이어진 하반신 형태', 'image' => ''],
            ['name' => '신체부위', 'description' => '손, 발, 허벅지 등 특정 부위만 있는 제품', 'image' => ''],
            ['name' => '리얼돌/전신형', 'description' => '머리부터 발까지 전신이거나 전신에 가까운 리얼돌', 'image' => ''],
            ['name' => '헤드', 'description' => '머리·얼굴만 있는 제품', 'image' => ''],
            ['name' => '퍼리/피규어', 'description' => '퍼리, 수인, 피규어 등 캐릭터 조형 제품', 'image' => ''],
        ];
        ?>
        <div class="torso-third-category-guide__list">
            <?php foreach ($torsoSecondCategoryGuides as $torsoSecondGuide) {
                $guideName = trim((string)($torsoSecondGuide['name'] ?? ''));
                $guideDescription = trim((string)($torsoSecondGuide['description'] ?? ''));
                $guideImage = trim((string)($torsoSecondGuide['image'] ?? ''));
                if ($guideName === '') {
                    continue;
                }
            ?>
            <div class="torso-third-category-guide__item">
                <?php if ($guideImage !== '') { ?>
                    <img src="<?= htmlspecialchars($guideImage, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($guideName, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" referrerpolicy="no-referrer">
                <?php } else { ?>
                    <div class="torso-third-category-guide__image-placeholder" aria-hidden="true">이미지<br>준비중</div>
                <?php } ?>
                <div>
                    <strong><?= htmlspecialchars($guideName, ENT_QUOTES, 'UTF-8') ?></strong>
                    <?php if ($guideDescription !== '') { ?>
                        <p><?= $formatGuideHtml($guideDescription) ?></p>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<div
    id="<?= htmlspecialchars($guideId('torso-third-category-guide'), ENT_QUOTES, 'UTF-8') ?>"
    class="torso-third-category-guide<?= $isCategoryGuideModal ? ' category-guide-accordion-section' : '' ?>"
    data-category-kind="TORSO"
    data-category-second="TORSO"
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
