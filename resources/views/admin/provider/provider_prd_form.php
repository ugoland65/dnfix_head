<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        display: none;
    }

    .loading-overlay.active {
        display: flex;
    }

    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .loading-text {
        margin-left: 15px;
        font-size: 16px;
        color: #333;
        font-weight: bold;
    }

    .supplier-detail-img-title{
        padding:10px 0 10px 0;
        font-size:16px;
        font-weight:bold;
    }

    .provider-section-nav {
        position: fixed;
        top: 75px;
        left: calc(50% + 100px);
        z-index: 1001;
        max-width: calc(100vw - 32px);
        transform: translateX(-50%);
        padding: 5px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
    }

    .provider-section-nav-list {
        display: flex;
        gap: 4px;
        overflow-x: auto;
        scrollbar-width: thin;
    }

    .provider-section-nav-button {
        flex: 0 0 auto;
        padding: 6px 10px;
        border: 0;
        border-radius: 5px;
        background: transparent;
        color: #4b5563;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
    }

    .provider-section-nav-button:hover,
    .provider-section-nav-button.is-active {
        background: #2563eb;
        color: #fff;
    }
    .additional-category-list { display: grid; gap: 8px; }
    .additional-category-row, .additional-category-selects { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .additional-category-message { color: #dc2626; font-size: 12px; }
    .additional-category-help { margin: 8px 0 0; color: #64748b; font-size: 12px; }
    .preference-tag-list { display: flex; align-items: center; gap: 8px 12px; flex-wrap: wrap; }
    .preference-tag-item { display: inline-flex; align-items: center; gap: 3px; }
    .preference-tag-chip { display: inline-flex; align-items: center; gap: 5px; padding: 6px 9px; border: 1px solid #cbd5e1; border-radius: 999px; background: #fff; color: #334155; font-size: 12px; cursor: pointer; }
    .preference-tag-chip:has(input:checked) { border-color: #2563eb; background: #eff6ff; color: #1d4ed8; }
    .preference-tag-chip input { margin: 0; }
    .preference-tag-info-button { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; padding: 0; border: 0; background: transparent; color: #64748b; cursor: pointer; }
    .preference-tag-info-button:hover { color: #2563eb; }
    .preference-tag-info-button:hover::after, .preference-tag-info-button:focus-visible::after { position: absolute; z-index: 30; left: 50%; bottom: calc(100% + 7px); width: max-content; max-width: 320px; padding: 7px 9px; border-radius: 5px; background: #1e293b; color: #fff; font-size: 11px; line-height: 1.4; white-space: normal; content: attr(data-tooltip); transform: translateX(-50%); box-shadow: 0 4px 12px rgba(15, 23, 42, .22); pointer-events: none; }
    .provider-tag-modal { position: fixed; inset: 0; z-index: 10020; display: none; align-items: center; justify-content: center; padding: 24px; background: rgba(15, 23, 42, .62); }
    .provider-tag-modal.is-open { display: flex; }
    .provider-tag-modal__panel { display: flex; flex-direction: column; width: min(760px, 100%); max-height: calc(100vh - 48px); border-radius: 10px; background: #fff; box-shadow: 0 20px 50px rgba(15, 23, 42, .28); overflow: hidden; }
    .provider-tag-modal__header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; }
    .provider-tag-modal__header h2 { margin: 0; color: #1f2937; font-size: 18px; }
    .provider-tag-modal__close { padding: 4px 8px; border: 0; background: transparent; color: #64748b; font-size: 24px; line-height: 1; cursor: pointer; }
    .provider-tag-modal__body { min-height: 0; padding: 16px 20px 20px; overflow-y: auto; }
    .preference-tag-detail-code { margin-left: 6px; color: #64748b; font-size: 12px; font-weight: 400; }
    .preference-tag-detail-section { margin-bottom: 14px; padding: 13px 15px; border: 1px solid #e2e8f0; border-radius: 7px; }
    .preference-tag-detail-section:last-child { margin-bottom: 0; }
    .preference-tag-detail-section h3 { margin: 0 0 7px; color: #334155; font-size: 13px; }
    .preference-tag-detail-section p { margin: 0; color: #475569; font-size: 13px; line-height: 1.6; white-space: pre-wrap; }
</style>

<?php if( empty($prd_data['godo_goodsNo']) ){ ?>
    <div class="alert alert-danger">
        <h3>아직 고도몰 매칭이 되지 않은 상품입니다.</h3>
        <p>고도몰 상품번호를 입력해주세요.</p>
    </div>
<?php } ?>

<div id="update_supplier_product_detail_loading" class="loading-overlay">
    <div class="loading-spinner"></div>
    <div class="loading-text">데이터 수집중입니다. 잠시만 기다려주세요...</div>
</div>

<nav id="provider_section_nav" class="provider-section-nav" aria-label="공급사 상품 상세 섹션 바로가기">
    <div class="provider-section-nav-list">
        <button type="button" class="provider-section-nav-button is-active" data-section-target="provider-basic-section">기본정보</button>

        <!--
        <button type="button" class="provider-section-nav-button" data-section-target="provider-hbti-section">HBTI</button>
        -->

        <button type="button" class="provider-section-nav-button" data-section-target="provider-godo-section">고도몰</button>
        <button type="button" class="provider-section-nav-button" data-section-target="provider-spec-section">상품 상세스펙</button>
        <button type="button" class="provider-section-nav-button" data-section-target="provider-supplier-section">공급사</button>
    </div>
</nav>

<form id="prd_provider_info_form">
    <input type="hidden" name="prd_idx" value="<?= $prd_data['idx'] ?>">
    <table class="table-style ">
        <colgroup>
            <col width="170px" />
            <col />
        </colgroup>
        <tr>
            <td colspan="2" class="none-bg title">
                <h1 id="provider-basic-section">위탁상품 기본정보</h1>
            </td>
        </tr>
        <tbody>
            <tr>
                <th>공급사 상품 고유번호</th>
                <td>
                    <b style="font-size:16px;"><?= $prd_data['idx'] ?></b>
                </td>
            </tr>

            <!-- 상품 구분 -->
            <tr>
                <th>상품 구분</th>
                <td>
                    <?php
                        $categoryRows = (isset($categories) && is_array($categories)) ? $categories : [];
                        $kindCodeByName = [];
                        foreach (($prd_kind_name ?? []) as $kindCode => $kindName) {
                            $kindCodeByName[(string)$kindName] = (string)$kindCode;
                        }
                        $categoryCodeByKind = [];
                        $categoryChildrenByKind = [];
                        foreach ($categoryRows as $categoryRow) {
                            if (!is_array($categoryRow)) {
                                continue;
                            }
                            $parentKey = trim((string)($categoryRow['key'] ?? ''));
                            $parentCode = trim((string)($categoryRow['code'] ?? ''));
                            $children = (isset($categoryRow['children']) && is_array($categoryRow['children'])) ? $categoryRow['children'] : [];
                            if ($parentKey !== '' && $parentCode !== '') {
                                $categoryCodeByKind[$parentKey] = $parentCode;
                            }
                            $childOptions = [];
                            foreach ($children as $childRow) {
                                if (!is_array($childRow)) {
                                    continue;
                                }
                                $childKey = trim((string)($childRow['key'] ?? ''));
                                $childCode = trim((string)($childRow['code'] ?? ''));
                                $childName = trim((string)($childRow['name'] ?? ''));
                                if ($childKey === '' || $childCode === '') {
                                    continue;
                                }
                                $categoryCodeByKind[$childKey] = $childCode;
                                $grandchildOptions = [];
                                $grandchildren = (isset($childRow['children']) && is_array($childRow['children'])) ? $childRow['children'] : [];
                                foreach ($grandchildren as $grandchildRow) {
                                    if (!is_array($grandchildRow)) {
                                        continue;
                                    }
                                    $grandchildKey = trim((string)($grandchildRow['key'] ?? ''));
                                    $grandchildCode = trim((string)($grandchildRow['code'] ?? ''));
                                    $grandchildName = trim((string)($grandchildRow['name'] ?? ''));
                                    if ($grandchildKey === '' || $grandchildCode === '') {
                                        continue;
                                    }
                                    $categoryCodeByKind[$grandchildKey] = $grandchildCode;
                                    $grandchildOptions[] = [
                                        'key' => $grandchildKey,
                                        'code' => $grandchildCode,
                                        'name' => $grandchildName !== '' ? $grandchildName : $grandchildKey,
                                    ];
                                }
                                $childOptions[] = [
                                    'key' => $childKey,
                                    'code' => $childCode,
                                    'name' => $childName !== '' ? $childName : $childKey,
                                    'children' => $grandchildOptions,
                                ];
                            }
                            if ($parentKey !== '' && !empty($childOptions)) {
                                $categoryChildrenByKind[$parentKey] = $childOptions;
                            }
                        }
                        $normalizeProviderAdditionalCategories = static function (array $rows, int $depth = 1) use (&$normalizeProviderAdditionalCategories): array {
                            if ($depth > 4) {
                                return [];
                            }
                            $normalizedRows = [];
                            foreach ($rows as $row) {
                                if (!is_array($row)) {
                                    continue;
                                }
                                $code = trim((string)($row['code'] ?? ''));
                                if ($code === '') {
                                    continue;
                                }
                                $key = trim((string)($row['key'] ?? ''));
                                $name = trim((string)($row['name'] ?? ''));
                                $children = (isset($row['children']) && is_array($row['children']))
                                    ? $normalizeProviderAdditionalCategories($row['children'], $depth + 1)
                                    : [];
                                $normalizedRows[] = [
                                    'key' => $key,
                                    'code' => $code,
                                    'name' => $name !== '' ? $name : ($key !== '' ? $key : $code),
                                    'children' => $children,
                                ];
                            }
                            return $normalizedRows;
                        };
                        $providerAdditionalCategoryTree = $normalizeProviderAdditionalCategories($categoryRows);

                        $selectedKindRaw = trim((string)($prd_data['kind'] ?? ''));
                        $selectedKindCode = $selectedKindRaw;
                        if ($selectedKindCode !== '' && !isset($prd_kind_name[$selectedKindCode])) {
                            $selectedKindCode = (string)($kindCodeByName[$selectedKindCode] ?? $selectedKindCode);
                        }
                        $selectedCategoryCode = trim((string)($prd_data['category_code'] ?? ''));
                        if ($selectedCategoryCode === '' && isset($categoryCodeByKind[$selectedKindCode])) {
                            $selectedCategoryCode = (string)$categoryCodeByKind[$selectedKindCode];
                        }
                        $selectedSecondKindKey = '';
                        $selectedThirdKindKey = '';
                        $selectedKindChildren = $categoryChildrenByKind[$selectedKindCode] ?? [];
                        if (!empty($selectedKindChildren)) {
                            foreach ($selectedKindChildren as $childOption) {
                                if ((string)($childOption['code'] ?? '') === $selectedCategoryCode) {
                                    $selectedSecondKindKey = (string)($childOption['key'] ?? '');
                                    break;
                                }
                                foreach (($childOption['children'] ?? []) as $grandchildOption) {
                                    if ((string)($grandchildOption['code'] ?? '') === $selectedCategoryCode) {
                                        $selectedSecondKindKey = (string)($childOption['key'] ?? '');
                                        $selectedThirdKindKey = (string)($grandchildOption['key'] ?? '');
                                        break 2;
                                    }
                                }
                            }
                        }
                        $providerAdditionalCategoryCodes = (isset($prd_data['additional_category_codes']) && is_array($prd_data['additional_category_codes']))
                            ? array_values(array_unique(array_filter(array_map('strval', $prd_data['additional_category_codes']))))
                            : [];
                    ?>
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <select name="kind">
                            <option value=''>상품 구분 선택</option>
                            <? foreach ($prd_kind_name as $kindCode => $kindName) { ?>
                                <option value="<?= $kindCode ?>" <? if ($selectedKindCode == $kindCode) echo "selected"; ?>><?= $kindName ?></option>
                            <? } ?>
                        </select>
                        <input type="hidden" name="category_code" id="provider_category_code" value="<?= htmlspecialchars($selectedCategoryCode, ENT_QUOTES, 'UTF-8') ?>">
                        <div id="provider_kind_second_wrap" style="display:none;">
                            <select name="kind_second" id="provider_kind_second">
                                <option value="">2차 카테고리 선택</option>
                            </select>
                        </div>
                        <div id="provider_kind_third_wrap" style="display:none;">
                            <select name="kind_third" id="provider_kind_third">
                                <option value="">3차 카테고리 선택</option>
                            </select>
                        </div>
                    </div>

                    <?php if ($selectedKindCode === '') { ?>
                        <p class="text-danger">
                            상품 구분이 지정되지 않았습니다. 상품 구분을 지정해주세요.
                        </p>
                    <?php } ?>

                    <?php
                    $categoryGuidePrefix = 'provider-';
                    include dirname(__DIR__) . '/product/partials/category_guide.php';
                    ?>

                </td>
            </tr>

            <tr>
                <th>추가 구분</th>
                <td>
                    <input type="hidden" name="cd_additional_category_codes[]" value="">
                    <div id="provider_additional_category_list" class="additional-category-list"></div>
                    <button type="button" id="provider_add_additional_category_btn" class="btnstyle1 btnstyle1-xs">+ 카테고리 추가</button>
                    <p class="additional-category-help">대표 카테고리를 제외한 카테고리를 여러 개 지정할 수 있습니다. 최대 4차 카테고리까지 선택할 수 있습니다.</p>
                </td>
            </tr>

            <tr>
                <th>취향/태그</th>
                <td>
                    <?php
                    $providerProductConfig = config('admin.product');
                    $providerPreferenceTags = (isset($providerProductConfig['preference_tags']) && is_array($providerProductConfig['preference_tags']))
                        ? $providerProductConfig['preference_tags']
                        : [];
                    $selectedProviderPreferenceTagCodes = (isset($prd_data['preference_tag_codes']) && is_array($prd_data['preference_tag_codes']))
                        ? array_fill_keys($prd_data['preference_tag_codes'], true)
                        : [];
                    ?>
                    <input type="hidden" name="preference_tag_codes[]" value="">
                    <div class="preference-tag-list">
                        <?php foreach ($providerPreferenceTags as $preferenceTag) {
                            if (!is_array($preferenceTag) || empty($preferenceTag['is_active'])) {
                                continue;
                            }
                            $tagCode = trim((string)($preferenceTag['code'] ?? ''));
                            $tagName = trim((string)($preferenceTag['name'] ?? ''));
                            $tagDescription = trim((string)($preferenceTag['description'] ?? ''));
                            $tagAdminDescription = trim((string)($preferenceTag['admin_description'] ?? ''));
                            if ($tagCode === '' || $tagName === '') {
                                continue;
                            }
                        ?>
                            <span class="preference-tag-item">
                                <label class="preference-tag-chip">
                                    <input type="checkbox" name="preference_tag_codes[]" value="<?= htmlspecialchars($tagCode, ENT_QUOTES, 'UTF-8') ?>" <?= isset($selectedProviderPreferenceTagCodes[$tagCode]) ? 'checked' : '' ?>>
                                    <span>#<?= htmlspecialchars($tagName, ENT_QUOTES, 'UTF-8') ?></span>
                                </label>
                                <button type="button"
                                    class="preference-tag-info-button"
                                    aria-label="<?= htmlspecialchars($tagName, ENT_QUOTES, 'UTF-8') ?> 태그 안내"
                                    data-code="<?= htmlspecialchars($tagCode, ENT_QUOTES, 'UTF-8') ?>"
                                    data-name="<?= htmlspecialchars($tagName, ENT_QUOTES, 'UTF-8') ?>"
                                    data-description="<?= htmlspecialchars($tagDescription, ENT_QUOTES, 'UTF-8') ?>"
                                    data-admin-description="<?= htmlspecialchars($tagAdminDescription, ENT_QUOTES, 'UTF-8') ?>"
                                    data-tooltip="<?= htmlspecialchars($tagAdminDescription, ENT_QUOTES, 'UTF-8') ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" fill="currentColor" viewBox="0 0 480 480" aria-hidden="true"><path d="M240 209c8.836 0 16 7.164 16 16v81c0 8.836-7.164 16-16 16s-16-7.163-16-16v-81c0-8.837 7.163-16 16-16M240 157c9.941 0 18 8.059 18 18s-8.059 18-18 18-18-8.059-18-18 8.059-18 18-18"></path><path fill-rule="evenodd" d="M240 47c106.591 0 193 86.409 193 193s-86.409 193-193 193S47 346.591 47 240 133.409 47 240 47m0 32c-88.918 0-161 72.082-161 161s72.082 161 161 161 161-72.082 161-161S328.918 79 240 79" clip-rule="evenodd"></path></svg>
                                </button>
                            </span>
                        <?php } ?>
                    </div>

                    <div id="provider_preference_tag_modal" class="provider-tag-modal" aria-hidden="true">
                        <div class="provider-tag-modal__panel" role="dialog" aria-modal="true" aria-labelledby="provider_preference_tag_modal_title">
                            <div class="provider-tag-modal__header">
                                <h2 id="provider_preference_tag_modal_title"><span class="preference-tag-detail-name"></span><span class="preference-tag-detail-code"></span></h2>
                                <button type="button" class="provider-tag-modal__close" aria-label="취향/태그 안내 닫기">&times;</button>
                            </div>
                            <div class="provider-tag-modal__body">
                                <div class="preference-tag-detail-section">
                                    <h3>고객 안내</h3>
                                    <p class="preference-tag-detail-description"></p>
                                </div>
                                <div class="preference-tag-detail-section">
                                    <h3>운영자 분류 기준</h3>
                                    <p class="preference-tag-detail-admin-description"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <th>브랜드</th>
                <td>
                    <select name="brand_idx" class="dn-select2">
                        <option value=''>브랜드 선택</option>
                        <?
                        foreach ($brandForSelect as $brand) {
                        ?>
                            <option value='<?= $brand['BD_IDX'] ?>' <? if ($brand['BD_IDX'] == $prd_data['brand_idx']) echo "selected"; ?>><?= $brand['BD_NAME'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>공급사/<?= $prd_data['partner_idx'] ?></th>
                <td>
                    <select name="partner_idx">
                        <option value=''>공급사 선택</option>
                        <?
                        foreach ($partnerForSelect as $partner) {
                        ?>
                            <option value='<?= $partner['idx'] ?>' <? if ($partner['idx'] == $prd_data['partner_idx']) echo "selected"; ?>><?= $partner['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>관리 상품코드</th>
                <td><input type='text' name='code' size='40' value="<?= $prd_data['code'] ?>" style="width:150px;"></td>
            </tr>
            <tr>
                <th>판매 상품명</th>
                <td>
                    <input type='text' name='name' id='name' size='40' value="<?= $prd_data['name'] ?>">
                    <button type="button" class="btnstyle1 btnstyle1-xs m-t-5" onclick="copyInputValue('name', '판매 상품명')">상품명 복사</button>
                    <div class="admin-guide-text">
                        브랜드에서 명칭한 정식 상품명이 특정이 될 경우 공급사에서 제공한 상품명을 무시하고 브랜드에서 명칭한 상품명을 사용합니다.
                    </div>
                </td>
            </tr>
            <tr>
                <th>원(영문,일어,중국어) 상품명</th>
                <td>
                    <input type='text' name='name_ori' id='name_ori' size='40' value="<?= $prd_data['name_ori'] ?>">
                    <button type="button" class="btnstyle1 btnstyle1-xs m-t-5" onclick="copyInputValue('name_ori', '원(영문,일어,중국어) 상품명')">상품명 복사</button>
                </td>
            </tr>

            <tr>
                <th>한줄 간략설명 </th>
                <td>
                    <input type='text' name='short_desc' id='short_desc' size='80' maxlength='255' value="<?= $prd_data['short_desc'] ?? '' ?>" placeholder="예: 부드러운 촉감의 입문용 제품">
                    <button type="button" class="btnstyle1 btnstyle1-xs m-t-5" onclick="copyInputValue('short_desc', '한줄 간략설명')">상품명 복사</button>
                </td>
            </tr>


            <tr>
                <th>공급사 상품명</th>
                <td><?= $prd_data['name_p'] ?? '' ?></td>
            </tr>
            <tr>
                <th>판매가</th>
                <td><?= number_format($prd_data['sale_price']) ?></td>
            </tr>
            <tr>
                <th>상품원가</th>
                <td>
                    <?= number_format($prd_data['cost_price']) ?><br>
                    <?php
                    if (!empty($prd_data['sale_price']) && !empty($prd_data['cost_price'])) {
                        $margin = $prd_data['sale_price'] - $prd_data['cost_price'];
                        $margin_rate = $margin / $prd_data['sale_price'] * 100;
                    ?>
                        마진 : <?= number_format($margin) ?>원 / 마진율 : <?= number_format($margin_rate, 2) ?>%
                    <?php
                    }
                    ?>
                    <?php
                    if (!empty($prd_data['min_sale_price']) && !empty($prd_data['cost_price'])) {
                        $min_margin = $prd_data['min_sale_price'] - $prd_data['cost_price'];
                        $min_margin_rate = $min_margin / $prd_data['min_sale_price'] * 100;
                    ?>
                        <br>최저판매가 기준 마진 : <b><?= number_format($min_margin) ?>원</b> / 마진율 : <b><?= number_format($min_margin_rate, 2) ?>%</b>
                    <?php
                    }
                    ?>
                </td>
            </tr>

            <tr>
                <th>주문가</th>
                <td>
                    <p><?= number_format($prd_data['order_price']) ?></p>
                    <?php
                        if (!empty($prd_data['sale_price']) && !empty($prd_data['order_price'])) {
                            $margin = $prd_data['sale_price'] - $prd_data['order_price'];
                            $margin_rate = $margin / $prd_data['sale_price'] * 100;

                            $grade = '';
                            $gradeColor = '';
                            if ($margin_rate > 39) {
                                $grade = 'A';
                                $gradeColor = '#28a745'; // 초록색
                            } elseif ($margin_rate >= 35) {
                                $grade = 'B';
                                $gradeColor = '#20c997'; // 연두색
                            } elseif ($margin_rate >= 30) {
                                $grade = 'C';
                                $gradeColor = '#17a2b8'; // 청록색
                            } elseif ($margin_rate >= 25) {
                                $grade = 'D';
                                $gradeColor = '#0dcaf0'; // 하늘색
                            } elseif ($margin_rate >= 20) {
                                $grade = 'E';
                                $gradeColor = '#ffc107'; // 노란색
                            } elseif ($margin_rate >= 15) {
                                $grade = 'F';
                                $gradeColor = '#fd7e14'; // 오렌지색
                            } elseif ($margin_rate >= 10) {
                                $grade = 'G';
                                $gradeColor = '#dc3545'; // 빨간색
                            } elseif ($margin_rate >= 5) {
                                $grade = 'H';
                                $gradeColor = '#d63384'; // 진한 빨강
                            } elseif ($margin_rate > 0) {
                                $grade = 'I';
                                $gradeColor = '#6c757d'; // 회색
                            }
                    ?>
                        마진 : <?= number_format($margin) ?>원 / 
                        마진율 : <b><?= number_format($margin_rate, 2) ?></b>%
                        마진등급 : 
                        <span class="grade-badge grade-<?=$grade?>">
                            <?=$grade?>
                        </span>
                    <?php
                        }
                    ?>
                </td>
            </tr>

            <tr>
                <th>부가세</th>
                <td>
                    <?php
                        $priceData = (isset($prd_data['price_data']) && is_array($prd_data['price_data']))
                            ? $prd_data['price_data']
                            : [];
                        $isVat = (string)($priceData['is_vat'] ?? '');
                    ?>
                    <?php if ($isVat === 'Y') { ?>
                        포함
                    <?php }elseif ($isVat === 'N') { ?>
                        미포함
                    <?php } ?>
                </td>
            </tr>

            <tr>
                <th>단종/취급중단</th>
                <td>
                    <?php
                        $isDiscontinued = !empty($prd_data['is_discontinued']);
                        $isHandlingStopped = !empty($prd_data['is_handling_stopped']);
                        $prdIdx = (int)($prd_data['idx'] ?? 0);
                    ?>
                    <?php if ($isDiscontinued) { ?>
                        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="unsetProductDiscontinued('<?= $prdIdx ?>')">단종 해제</button>
                    <?php } else { ?>
                        <button type="button" class="btnstyle1 btnstyle1-sm" onclick="setProductDiscontinued('<?= $prdIdx ?>', <?= $isHandlingStopped ? 'true' : 'false' ?>)">단종 처리</button>
                    <?php } ?>

                    <?php if ($isHandlingStopped) { ?>
                        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="unsetProductHandlingStopped('<?= $prdIdx ?>')">취급중단 해제</button>
                    <?php } else { ?>
                        <button type="button" class="btnstyle1 btnstyle1-sm" onclick="setProductHandlingStopped('<?= $prdIdx ?>', <?= $isDiscontinued ? 'true' : 'false' ?>)">취급중단 처리</button>
                    <?php } ?>
                    <div class="admin-guide-text">
                        - 단종: 더 이상 판매하지 않음 / 취급중단: 더 이상 사입하지 않음 (남은 재고는 판매 가능)
                        <br>- 두 상태는 동시에 지정할 수 없습니다.
                    </div>
                </td>
            </tr>

            <tr>
                <th>리스트 메모</th>
                <td>
                    <input type='text' name='memo' id='memo' value="<?= $prd_data['memo'] ?? '' ?>">
                    <div class="m-t-6">
                        <select id="quickMemoSelect" style="width:260px;" onchange="applyQuickMemo(this.value)">
                            <option value="">자주 쓰는 메모 선택</option>
                            <option value="입고예정후 판매전환 예정">입고예정후 판매전환 예정</option>
                        </select>
                    </div>
                </td>
            </tr>

            <tr>
                <th>작업지시 메모</th>
                <td>
                    <textarea name='memo_work' rows='5'><?= $prd_data['memo_work'] ?? '' ?></textarea>
                </td>
            </tr>

            <tr>
                <th>상세분류</th>
                <td>
                    <?php
                        $selectedDepth1 = $prd_data['godo_cate_depth1'] ?? ($prd_data['godo_cate1'] ?? '');
                        $selectedDepth2 = $prd_data['godo_cate_depth2'] ?? ($prd_data['godo_cate2'] ?? '');
                        $selectedDepth3 = $prd_data['godo_cate_depth3'] ?? ($prd_data['godo_cate3'] ?? '');
                        $initialSelectedCategoryItems = $prd_data['cate_json'] ?? [];
                        if (!is_array($initialSelectedCategoryItems)) {
                            $initialSelectedCategoryItems = [];
                        }
                    ?>
                    <div id="godoCateSelectorWrap" style="display:none;">
                        <select name="godo_cate_depth1" id="godo_cate_depth1" style="width:180px;">
                            <option value="">1차 분류 선택</option>
                        </select>
                        <select name="godo_cate_depth2" id="godo_cate_depth2" style="width:180px; margin-left:4px;" disabled>
                            <option value="">2차 분류 선택</option>
                        </select>
                        <select name="godo_cate_depth3" id="godo_cate_depth3" style="width:180px; margin-left:4px;" disabled>
                            <option value="">3차 분류 선택</option>
                        </select>

                        <button type="button" id="addGodoCategoryBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm">
                            분류선택
                        </button>

                        <div id="selectedGodoCategoryList" style="margin-top:8px;"></div>
                        <div id="selectedGodoCategoryInputs">
                            <input type="hidden" name="cate_json" id="cate_json" value="">
                        </div>
                    </div>
                    <div id="godoCateSelectorGuide" class="admin-guide-text">
                        상품 구분이 BDSM일 때 상세분류를 선택할 수 있습니다.
                    </div>
                </td>
            </tr>

        <tbody>

        <?php if (($selectedKindCode ?? '') === "ONAHOLE") { ?>
        <tbody>
            <tr>
                <td colspan="2" class="none-bg" style="height:10px;"></td>
            </tr>
            <tr>
                <td colspan="2" class="none-bg title">
                    <h1 id="provider-hbti-section">HBTI</h1>
                </td>
            </tr>
        </tbody>

        <tbody>
            <tr>
                <th>HBTI</th>
                <td>
                    <select name="hbti_type" id="hbti_type">
                        <option value="">HBTI 선택</option>
                        <?
                        foreach ($hbtiTypes as $hbtiType) {
                        ?>
                            <option value="<?= $hbtiType ?>" <? if (($prd_data['hbti_type'] ?? '') == $hbtiType) echo "selected"; ?>><?= $hbtiType ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
        </tbody>
    <?php } ?>


    <tbody>
        <tr>
            <td colspan="2" class="none-bg" style="height:30px;"></td>
        </tr>
        <tr>
            <td colspan="2" class="none-bg title">
                <div>
                    <ul>
                        <h1 id="provider-godo-section">고도몰</h1>
                    </ul>

                    <?php if (!empty($prd_data['godo_goodsNo'])) { ?>
                        <ul class="right">
                            <button type="button" class="btnstyle1 btnstyle1-sm"
                                onclick="goGodoMall(<?= $prd_data['godo_goodsNo'] ?>);">쑈당몰 상품보기</button>

                            <button type="button" class="btnstyle1 btnstyle1-sm" onclick="goGodoMallAdmin(<?= $prd_data['godo_goodsNo'] ?>);">관리자 상품보기</button>

                            <?php if (!empty($prd_data['godo_loaded_at'])) { ?>
                                ( 최근 고도몰 로드일 : <?= $prd_data['godo_loaded_at'] ?> )
                            <?php } ?>

                            <button type="button" id="loadGodoGoodsInfoBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm"
                                data-prd-idx="<?= $prd_data['idx'] ?>"
                                data-godo-goods-no="<?= $prd_data['godo_goodsNo'] ?>">
                                고도몰 데이터 로드
                                <i class="fas fa-download"></i>
                            </button>
                        </ul>
                    <?php } ?>
                </div>
            </td>
        </tr>
    </tbody>


    <tbody>
        <tr>
            <th>고도몰 상품번호</th>
            <td>
                <input type='text' name='godo_goodsNo' id="godo_goodsNo" value="<?= $prd_data['godo_goodsNo'] ?>" style="width:150px;">
                <?php if( empty($prd_data['godo_goodsNo']) ){ ?>
                <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="matchGodoGoods();">
                    고도몰 매칭하고 등록완료로 변경
                </button>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th>고도몰 등록상태</th>
            <td>

                <select name="status">
                    <option value="등록대기" <? if ($prd_data['status'] == '등록대기') echo "selected"; ?>>등록대기</option>
                    <option value="등록완료" <? if ($prd_data['status'] == '등록완료') echo "selected"; ?>>등록완료</option>
                    <option value="등록보류" <? if ($prd_data['status'] == '등록보류') echo "selected"; ?>>등록보류</option>
                    <option value="등록취소" <? if ($prd_data['status'] == '등록취소') echo "selected"; ?>>등록취소</option>
                    <option value="품절" <? if ($prd_data['status'] == '품절') echo "selected"; ?>>품절</option>
                    <option value="사입전용" <? if ($prd_data['status'] == '사입전용') echo "selected"; ?>>사입전용</option>
                </select>

                <?php if ($prd_data['status'] == '품절') { ?>
                    <br><span class="text-danger">품절 처리일 : <?= $prd_data['sold_out_date'] ?? '' ?></span>
                <?php } ?>

            </td>
        </tr>
        <tr>
            <th>공급사 매칭코드</th>
            <td><input type='text' name='matching_code' value="<?= $prd_data['matching_code'] ?>" style="width:150px;"></td>
        </tr>
        <tr>
            <th>판매가 ( 고도몰 등록가격)</th>
            <td>
                <input type='text' name='sale_price' size='40' value="<?= number_format($prd_data['sale_price']) ?>" style="width:150px;" class="comma-input">

                <?php if ($prd_data['sale_price'] == 0) { ?>
                    <p class="text-danger">
                        판매가가 지정되지 않았습니다. 판매가를 지정해주세요.
                    </p>
                <?php } ?>

                <?php if ($prd_data['cost_price'] > 0) { ?>
                    <?php
                    $_margin_ex_per = [10, 15, 20, 25, 30, 35, 40, 45, 50];
                    $_cost_price_for_margin = (float)($prd_data['cost_price'] ?? 0);
                    $_order_price_for_margin = (float)($prd_data['order_price'] ?? 0);
                    $_min_sale_price_for_margin = (float)($prd_data['min_sale_price'] ?? 0);
                    ?>
                    <div class="m-t-8" style="font-size:12px; display:flex; gap:24px; align-items:flex-start;">
                        <div>
                            <div><b>원가 기준 마진별 판매가 예시</b></div>
                            <div>원가 : <b><?= number_format($_cost_price_for_margin) ?></b>원</div>
                            <?php foreach ($_margin_ex_per as $_margin_per) { ?>
                                <?php
                                $_example_sale_price = 0;
                                if ($_cost_price_for_margin > 0 && (100 - $_margin_per) > 0) {
                                    $_example_sale_price = ceil($_cost_price_for_margin / ((100 - $_margin_per) / 100));
                                }
                                ?>
                                <div class="m-t-3">
                                    <?= $_margin_per ?>% 마진 판매가 : <b><?= number_format($_example_sale_price) ?></b>원
                                </div>
                            <?php } ?>
                        </div>
                        <div>
                            <div><b>주문가 기준 마진별 판매가 예시</b></div>
                            <div>주문가 : <b><?= number_format($_order_price_for_margin) ?></b>원</div>
                            <?php foreach ($_margin_ex_per as $_margin_per) { ?>
                                <?php
                                $_example_sale_price_by_order = 0;
                                if ($_order_price_for_margin > 0 && (100 - $_margin_per) > 0) {
                                    $_example_sale_price_by_order = ceil($_order_price_for_margin / ((100 - $_margin_per) / 100));
                                }
                                ?>
                                <div class="m-t-3">
                                    <?= $_margin_per ?>% 마진 판매가 : <b><?= number_format($_example_sale_price_by_order) ?></b>원
                                </div>
                            <?php } ?>
                        </div>
                        <?php if ($_min_sale_price_for_margin > 0) { ?>
                        <div>
                            <div><b>최저판매가 기준 마진율</b></div>
                            <div>최저판매가 : <b><?= number_format($_min_sale_price_for_margin) ?></b>원</div>
                            <?php
                            $_min_margin_by_cost = $_min_sale_price_for_margin - $_cost_price_for_margin;
                            $_min_margin_rate_by_cost = ($_cost_price_for_margin > 0)
                                ? (($_min_margin_by_cost / $_min_sale_price_for_margin) * 100)
                                : null;
                            $_min_margin_by_order = $_min_sale_price_for_margin - $_order_price_for_margin;
                            $_min_margin_rate_by_order = ($_order_price_for_margin > 0)
                                ? (($_min_margin_by_order / $_min_sale_price_for_margin) * 100)
                                : null;
                            ?>
                            <div class="m-t-3">
                                원가 기준: 
                                <?php if ($_min_margin_rate_by_cost !== null) { ?>
                                    <b><?= number_format($_min_margin_rate_by_cost, 2) ?>%</b>
                                <?php } else { ?>
                                    <b>-</b>
                                <?php } ?>
                            </div>
                            <div class="m-t-3">
                                주문가 기준: 
                                <?php if ($_min_margin_rate_by_order !== null) { ?>
                                    <b><?= number_format($_min_margin_rate_by_order, 2) ?>%</b>
                                <?php } else { ?>
                                    <b>-</b>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>


            </td>
        </tr>


        <?php

            if( !empty($prd_data['godo_goodsNo']) ){ 
                
                $godoOptionNames = $prd_data['godo_option']['name'] ?? [];
                $godoOptionItems = $prd_data['godo_option']['items'] ?? [];
                if (!empty($prd_data['godo_is_option']) && is_array($godoOptionNames) && is_array($godoOptionItems)) {
        ?>
            <tr>
                <th>고도몰 옵션</th>
                <td>
                    <table class="table-style ">
                        <colgroup>
                            <col width="100px" />
                            <col width="150px" />
                            <col width="100px" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th>옵션번호</th>
                                <?php foreach ($godoOptionNames as $optionName) { ?>
                                    <th><?= $optionName ?></th>
                                <?php } ?>
                                <th>옵션가격</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($godoOptionItems as $i => $option) { ?>
                                <tr>
                                    <td><?= $option['optionNo'] ?></td>
                                    <?php
                                    $valueNum = 0;
                                    foreach ($godoOptionNames as $optionName) {
                                        $valueNum++;
                                        $keyName = 'optionValue' . $valueNum;
                                    ?>
                                        <td><?= $option[$keyName] ?? '' ?></td>
                                    <?php
                                    }
                                    ?>
                                    <td class="text-right"><?= number_format($option['optionPrice'] ?? 0) ?></td>
                                    <td></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php
        }
        ?>

        <tr>
            <th>고도몰 카테고리</th>
            <td>
                <?php
                    $godoCateRows = $prd_data['godo_cate_json'] ?? [];
                    if (is_string($godoCateRows)) {
                        $decodedGodoCateRows = json_decode($godoCateRows, true);
                        $godoCateRows = is_array($decodedGodoCateRows) ? $decodedGodoCateRows : [];
                    } elseif (!is_array($godoCateRows)) {
                        $godoCateRows = [];
                    }
                    usort($godoCateRows, function ($a, $b) {
                        $codeA = (string)($a['cateCd'] ?? ($a['code'] ?? ''));
                        $codeB = (string)($b['cateCd'] ?? ($b['code'] ?? ''));
                        return strcmp($codeA, $codeB);
                    });
                ?>
                <table class="table-style ">
                    <colgroup>
                        <col />
                        <col width="100px" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>카테고리명</th>
                            <th>카테고리코드</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($godoCateRows as $cate) { ?>
                            <tr>
                                <td>
                                    <?php
                                        $pathParts = [];
                                        foreach (($cate['path'] ?? []) as $pathNode) {
                                            $pathName = trim((string)($pathNode['cateNm'] ?? ''));
                                            if ($pathName !== '') {
                                                $pathParts[] = $pathName;
                                            }
                                        }
                                        $cateName = trim((string)($cate['cateNm'] ?? ($cate['name'] ?? '')));
                                        if (empty($pathParts) && $cateName !== '') {
                                            $pathParts[] = $cateName;
                                        }
                                        echo implode(' > ', $pathParts);
                                    ?>
                                </td>
                                <td><?= $cate['cateCd'] ?? ($cate['code'] ?? '') ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </td>
        </tr>

        <?php } ?>


        <tr>
            <th>이미지 모드</th>
            <td>
                <label><input type="radio" name="img_mode" value="out" <? if ($prd_data['img_mode'] == 'out') echo "checked"; ?>> 외부 이미지</label>
                <label><input type="radio" name="img_mode" value="this" <? if ($prd_data['img_mode'] == 'this') echo "checked"; ?>> 서버에 등록</label>
            </td>
        </tr>
        <tr>
            <th>이미지 URL</th>
            <td><input type='text' name='img_src' size='40' value="<?= $prd_data['img_src'] ?>"></td>
        </tr>
    </tbody>


    <tbody>
        <tr>
            <td colspan="2" class="none-bg" style="height:30px;"></td>
        </tr>
        <tr>
            <td colspan="2" class="none-bg title">
                <div>
                    <ul>
                        <h1 id="provider-supplier-section">상품 상세정보</h1>
                    </ul>
                </div>
            </td>
        </tr>
    </tbody>

    <tbody>
        <tr>
            <th id="provider-spec-section">상품 상세스펙</th>
            <td>
                <?php
                $specCategoryCode = (string)($selectedCategoryCode ?? ($prd_data['category_code'] ?? ''));
                $specData = (isset($prd_data['spec_data']) && is_array($prd_data['spec_data'])) ? $prd_data['spec_data'] : [];
                $specInputPrefix = 'spec';
                include dirname(__DIR__) . '/product/partials/spec_form.php';
                ?>
            </td>
        </tr>
    </tbody>



    <tbody>
        <tr>
            <td colspan="2" class="none-bg" style="height:30px;"></td>
        </tr>
        <tr>
            <td colspan="2" class="none-bg title">

                <div>
                    <ul>
                        <h1 id="provider-supplier-section">공급사</h1>
                    </ul>

                    <?php if (!empty($prd_data['supplier_prd_pk']) && 
                    ( $prd_data['partner_idx'] == 3 || $prd_data['partner_idx'] == 6 || $prd_data['partner_idx'] == 7 || $prd_data['partner_idx'] == 8 || $prd_data['partner_idx'] == 10 ) ) { ?>
                        <ul class="right">
                            업데이트후 새로고침 됩니다. 저장후 이용바랍니다.

                            <?php if (!empty($prd_data['detail_crawler_date'])) { ?>
                                ( 최근 크롤링 일자 : <?= $prd_data['detail_crawler_date'] ?> )
                            <?php } ?>

                            <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="updateSupplierProductDetail();">
                                공급사 사이트 디테일 크롤링후 업데이트
                            </button>
                        </ul>
                    <?php } ?>
                </div>

            </td>
        </tr>
    </tbody>

    <tbody>
        <tr>
            <th>공급사 사이트</th>
            <td><input type='text' name='supplier_site' value="<?= $prd_data['supplier_site'] ?>" style="width:150px;"></td>
        </tr>
        <tr>
            <th>공급 2차</th>
            <td><input type='text' name='supplier_2nd_name' value="<?= $prd_data['supplier_2nd_name'] ?>" style="width:150px;"></td>
        </tr>
        <tr>
            <th>공급사 상품명</th>
            <td><input type='text' name='name_p' size='40' value="<?= $prd_data['name_p'] ?>"></td>
        </tr>
        <tr>
            <th>공급사 사이트 고유번호</th>
            <td>
                <input type='text' name='supplier_prd_pk' value="<?= $prd_data['supplier_prd_pk'] ?>" style="width:150px;">
                <button type="button" class="btnstyle1 btnstyle1-sm" onclick="goSupplierProduct('<?= $prd_data['supplier_site'] ?? '' ?>', '<?= $prd_data['supplier_prd_pk'] ?? '' ?>');">공급사 사이트 상품보기</button>
            </td>
        </tr>
        <tr>
            <th>공급사 상품DB 매칭 번호</th>
            <td>
                <input type='text' name='supplier_prd_idx' value="<?= $prd_data['supplier_prd_idx'] ?>" style="width:150px;">
                <button type="button" class="btnstyle1 btnstyle1-sm" onclick="goSupplierProductEdit('<?= $prd_data['supplier_prd_idx'] ?? '' ?>');">공급사 상품DB 상품보기</button>
            </td>
        </tr>

        <tr>
            <th>부가세</th>
            <td>
                <?php
                    $priceDataForVat = (isset($prd_data['price_data']) && is_array($prd_data['price_data']))
                        ? $prd_data['price_data']
                        : [];
                    $isVatForForm = (string)($priceDataForVat['is_vat'] ?? '');
                ?>
                <label><input type="radio" name="is_vat" value="Y" <? if ($isVatForForm === 'Y') echo "checked"; ?>> 포함</label>
                <label><input type="radio" name="is_vat" value="N" <? if ($isVatForForm === 'N') echo "checked"; ?>> 미포함</label>
            </td>
        </tr>
        <tr>
            <th>최저판매가</th>
            <td><input type='text' name='min_sale_price' size='40' value="<?= number_format($prd_data['min_sale_price'] ?? 0) ?>" style="width:150px;" class="comma-input"></td>
        </tr>
        <tr>
            <th>상품원가 (공급사 제공가격)</th>
            <td><input type='text' name='cost_price' size='40' value="<?= number_format($prd_data['cost_price']) ?>" style="width:150px;" class="comma-input"></td>
        </tr>
        <tr>
            <th>대리배송 배송비</th>
            <td>
                <input type='text' name='delivery_fee' size='40' value="<?= number_format($prd_data['price_data']['delivery_fee'] ?? 0) ?>" style="width:150px;" class="comma-input">
                <?= $prd_data['price_data']['delivery_com'] ?? '' ?>
                <?= $prd_data['price_data']['delivery_time'] ?? '' ?>
                <input type='hidden' name='delivery_com' value="<?= $prd_data['price_data']['delivery_com'] ?? '' ?>">
                <input type='hidden' name='delivery_time' value="<?= $prd_data['price_data']['delivery_time'] ?? '' ?>">
            </td>
        </tr>
        <tr>
            <th>주문가 (대리배송 가격)</th>
            <td>
                <input type='text' name='order_price' size='40' value="<?= number_format($prd_data['order_price']) ?>" style="width:150px;" class="comma-input">
            </td>
        </tr>

        <?php if ($prd_data['supplier_is_option'] == 'Y') { ?>
            <tr>
                <th>공급사 옵션</th>
                <td>

                    <table class="table-style ">
                        <thead>
                            <tr>
                                <th class="width-150">옵션명</th>
                                <th>옵션값</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prd_data['supplier_option_data'] as $option) { ?>
                                <tr>
                                    <td class="text-center"><?= $option['name'] ?></td>
                                    <td>
                                        <?php foreach ($option['items'] as $item) { ?>
                                            <div>
                                                <?= $item['value'] ?>
                                                <?php if ($item['price_adjustment'] > 0) { ?>
                                                    + <?= number_format($item['price_adjustment'] ?? 0) ?>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php } ?>

        <?php if (!empty($prd_data['matching_option'])) { ?>
            <tr>
                <th>공급사 매칭 옵션</th>
                <td><input type='text' name='matching_option' value="<?= $prd_data['matching_option'] ?>" style="width:300px;"></td>
            </tr>
        <?php } ?>

        <tr>
            <th>공급사 판매 상태</th>
            <td>

                <select name="supplier_status">
                    <option value="판매중" <? if ($prd_data['supplier_status'] == '판매중') echo "selected"; ?>>판매중</option>
                    <option value="품절" <? if ($prd_data['supplier_status'] == '품절') echo "selected"; ?>>품절</option>
                    <option value="판매중단" <? if ($prd_data['supplier_status'] == '판매중단') echo "selected"; ?>>판매중단</option>
                </select>

                <?php if ($prd_data['supplier_status'] == '품절') { ?>
                    <br><span class="text-danger">품절 처리일 : <?= $prd_data['supplier_status_date'] ?? '' ?></span>
                <?php } ?>

                <?php if ($prd_data['supplier_status'] == '판매중단') { ?>
                    <br><span class="text-danger">판매중단 처리일 : <?= $prd_data['supplier_status_date'] ?? '' ?></span>
                <?php } ?>

            </td>
        </tr>

        <tr>
            <th>공급사 이미지 모드</th>
            <td>
                <label><input type="radio" name="supplier_img_mode" value="out" <? if ($prd_data['supplier_img_mode'] == 'out') echo "checked"; ?>> 외부 이미지</label>
                <label><input type="radio" name="supplier_img_mode" value="this" <? if ($prd_data['supplier_img_mode'] == 'this') echo "checked"; ?>> 서버에 등록</label>
            </td>
        </tr>
        <tr>
            <th>공급사 이미지 URL</th>
            <td><input type='text' name='supplier_img_src' size='40' value="<?= $prd_data['supplier_img_src'] ?>"></td>
        </tr>


        <?php
        if (!empty($prd_data['supplier_detail_img'])) {
            $detailImgHtmlLines = [];
            foreach ($prd_data['supplier_detail_img'] as $imgSrc) {
                $detailImgHtmlLines[] = '<img src="' . htmlspecialchars((string)$imgSrc, ENT_QUOTES, 'UTF-8') . '">';
            }
            $detailImgHtmlCode = implode("\n", $detailImgHtmlLines);
        ?>
            <tr>
                <td colspan="2">
                    <div style="margin-bottom:10px;">
                        <b>상세이미지 HTML 코드</b>
                        <button type="button" class="btnstyle1 btnstyle1-xs m-l-5" onclick="copySupplierDetailImgHtml()">복사</button>
                    </div>
                    <textarea id="supplier_detail_img_html_code" readonly style="width:100%; height:90px; resize:vertical;"><?= htmlspecialchars($detailImgHtmlCode, ENT_QUOTES, 'UTF-8') ?></textarea>
                </td>
            </tr>

            <tr>
                <td colspan="2">

                    <div>
                        <h3 class="supplier-detail-img-title">공급사 제공 상세이미지</h3>
                    <?php
                    foreach ($prd_data['supplier_detail_img'] as $img) {
                    ?>
                        <img src="<?= $img ?>" style="max-width:990px; width:100%; height:auto;">
                    <?php
                    }
                    ?>
                    </div>

                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>


    </table>
</form>

<? if (!empty($prd_data['idx'])) { ?>
    <div class="button-wrap-back">
    </div>
    <div class="button-wrap">
        <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdProviderInfo.save()">상품수정</button>
    </div>
<? } ?>



<script>
    $(document).ready(function() {
        $('.dn-select2').select2();
        initProviderCategorySelector();
        initProviderAdditionalCategories();
        initProviderPreferenceTags();
        initGodoCategorySelector();

        var $providerSectionNav = $('#provider_section_nav');
        var $providerSectionNavButtons = $providerSectionNav.find('.provider-section-nav-button');
        var updateProviderSectionNav = function() {
            var scrollPosition = $(window).scrollTop() + $providerSectionNav.outerHeight() + 24;
            var $activeButton = null;

            $providerSectionNavButtons.each(function() {
                var $button = $(this);
                var $section = $('#' + $button.data('section-target'));
                var isVisible = $section.length && $section.is(':visible');
                $button.toggle(isVisible);

                if (isVisible && $section.offset().top <= scrollPosition) {
                    $activeButton = $button;
                }
            });

            if ($activeButton) {
                $providerSectionNavButtons.removeClass('is-active').removeAttr('aria-current');
                $activeButton.addClass('is-active').attr('aria-current', 'page');
            }
        };

        $providerSectionNavButtons.on('click', function() {
            var $section = $('#' + $(this).data('section-target'));
            if (!$section.length || !$section.is(':visible')) {
                return;
            }

            $('html, body').stop(true).animate({
                scrollTop: Math.max(0, $section.offset().top - $providerSectionNav.outerHeight() - 40)
            }, 250);
        });

        $(window).off('scroll.providerSectionNav resize.providerSectionNav')
            .on('scroll.providerSectionNav resize.providerSectionNav', updateProviderSectionNav);
        updateProviderSectionNav();
    });

    const providerCategoryCodeByKind = <?= json_encode($categoryCodeByKind ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const providerCategoryChildrenByKind = <?= json_encode($categoryChildrenByKind ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const providerKindNameByCode = <?= json_encode($prd_kind_name ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const initialProviderSecondKindKey = <?= json_encode($selectedSecondKindKey ?? '', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const initialProviderThirdKindKey = <?= json_encode($selectedThirdKindKey ?? '', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const providerAdditionalCategoryTree = <?= json_encode($providerAdditionalCategoryTree ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const initialProviderAdditionalCategoryCodes = <?= json_encode($providerAdditionalCategoryCodes ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    let hasAppliedInitialProviderSecondCategory = false;
    let hasAppliedInitialProviderThirdCategory = false;
    const godoCateTree = <?= json_encode($godo_cate ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const selectedGodoCate = <?= json_encode([
        'depth1' => $selectedDepth1,
        'depth2' => $selectedDepth2,
        'depth3' => $selectedDepth3,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const initialSelectedGodoCategoryItems = <?= json_encode($initialSelectedCategoryItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    let hasAppliedInitialGodoCate = false;
    const selectedGodoCategoryItems = Array.isArray(initialSelectedGodoCategoryItems) ? initialSelectedGodoCategoryItems.slice() : [];

    function resolveProviderCategoryCodeByKind(kindKey) {
        const key = String(kindKey || '').trim();
        if (!key || typeof providerCategoryCodeByKind !== 'object' || providerCategoryCodeByKind === null) {
            return '';
        }
        return String(providerCategoryCodeByKind[key] || '').trim();
    }

    function findProviderAdditionalCategoryPath(categoryCode, rows, path) {
        const targetCode = String(categoryCode || '').trim();
        const currentRows = Array.isArray(rows) ? rows : [];
        const currentPath = Array.isArray(path) ? path : [];
        for (let i = 0; i < currentRows.length; i++) {
            const row = currentRows[i] || {};
            const rowCode = String(row.code || '').trim();
            const nextPath = currentPath.concat(rowCode);
            if (rowCode === targetCode) {
                return nextPath;
            }
            const childPath = findProviderAdditionalCategoryPath(targetCode, row.children, nextPath);
            if (childPath.length > 0) {
                return childPath;
            }
        }
        return [];
    }

    function getProviderAdditionalRows(path, depth) {
        let rows = Array.isArray(providerAdditionalCategoryTree) ? providerAdditionalCategoryTree : [];
        for (let i = 0; i < depth; i++) {
            const selectedCode = String(path[i] || '').trim();
            const selectedRow = rows.find(function(row) {
                return String((row || {}).code || '').trim() === selectedCode;
            });
            rows = selectedRow && Array.isArray(selectedRow.children) ? selectedRow.children : [];
        }
        return rows;
    }

    function updateProviderAdditionalCategoryRow($row) {
        const path = [];
        $row.find('.provider-additional-category-select').each(function() {
            const code = String($(this).val() || '').trim();
            if (code) {
                path.push(code);
            }
        });
        const categoryCode = path.length ? path[path.length - 1] : '';
        const primaryCategoryCode = String($('#provider_category_code').val() || '').trim();
        let submittedCode = categoryCode;
        let message = '';

        if (categoryCode && categoryCode === primaryCategoryCode) {
            submittedCode = '';
            message = '대표 카테고리와 같은 카테고리는 추가할 수 없습니다.';
        } else if (categoryCode) {
            $('#provider_additional_category_list .provider-additional-category-code')
                .not($row.find('.provider-additional-category-code'))
                .each(function() {
                    if (String($(this).val() || '').trim() === categoryCode) {
                        submittedCode = '';
                        message = '이미 추가한 카테고리입니다.';
                        return false;
                    }
                });
        }
        $row.find('.provider-additional-category-code').val(submittedCode);
        $row.find('.additional-category-message').text(message);
    }

    function renderProviderAdditionalCategoryRow($row, selectedPath) {
        const path = Array.isArray(selectedPath) ? selectedPath : [];
        const $selects = $row.find('.additional-category-selects').empty();
        for (let depth = 0; depth < 4; depth++) {
            const rows = getProviderAdditionalRows(path, depth);
            if (!rows.length) {
                break;
            }
            const $select = $('<select>', {
                class: 'provider-additional-category-select',
                'data-depth': depth
            }).append($('<option>', {
                value: '',
                text: (depth + 1) + '차 카테고리 선택'
            }));
            rows.forEach(function(row) {
                $select.append($('<option>', {
                    value: String((row || {}).code || '').trim(),
                    text: String((row || {}).name || (row || {}).code || '').trim()
                }));
            });
            $select.val(String(path[depth] || '').trim());
            $selects.append($select);
            if (!path[depth]) {
                break;
            }
        }
        updateProviderAdditionalCategoryRow($row);
    }

    function addProviderAdditionalCategoryRow(categoryCode) {
        const selectedPath = categoryCode
            ? findProviderAdditionalCategoryPath(categoryCode, providerAdditionalCategoryTree, [])
            : [];
        const $row = $('<div>', { class: 'additional-category-row' });
        $row.append($('<div>', { class: 'additional-category-selects' }));
        $row.append($('<input>', {
            type: 'hidden',
            name: 'cd_additional_category_codes[]',
            class: 'provider-additional-category-code'
        }));
        $row.append($('<button>', {
            type: 'button',
            class: 'btnstyle1 btnstyle1-danger btnstyle1-xs provider-remove-additional-category-btn',
            text: '삭제'
        }));
        $row.append($('<span>', { class: 'additional-category-message' }));
        $('#provider_additional_category_list').append($row);
        renderProviderAdditionalCategoryRow($row, selectedPath);
    }

    function revalidateProviderAdditionalCategories() {
        $('#provider_additional_category_list .additional-category-row').each(function() {
            updateProviderAdditionalCategoryRow($(this));
        });
    }

    function initProviderAdditionalCategories() {
        $('#provider_add_additional_category_btn').on('click', function() {
            addProviderAdditionalCategoryRow('');
        });
        $(document).on('change', '.provider-additional-category-select', function() {
            const $select = $(this);
            const $row = $select.closest('.additional-category-row');
            const changedDepth = Number($select.attr('data-depth') || 0);
            const selectedPath = [];
            $row.find('.provider-additional-category-select').each(function() {
                if (Number($(this).attr('data-depth') || 0) > changedDepth) {
                    return false;
                }
                const code = String($(this).val() || '').trim();
                if (code) {
                    selectedPath.push(code);
                }
            });
            renderProviderAdditionalCategoryRow($row, selectedPath);
        });
        $(document).on('click', '.provider-remove-additional-category-btn', function() {
            $(this).closest('.additional-category-row').remove();
        });
        if (Array.isArray(initialProviderAdditionalCategoryCodes) && initialProviderAdditionalCategoryCodes.length) {
            initialProviderAdditionalCategoryCodes.forEach(addProviderAdditionalCategoryRow);
        } else {
            addProviderAdditionalCategoryRow('');
        }
    }

    function initProviderPreferenceTags() {
        const $modal = $('#provider_preference_tag_modal');
        let previousBodyOverflow = '';
        let $lastTrigger = $();
        const closeModal = function() {
            if (!$modal.hasClass('is-open')) {
                return;
            }
            $modal.removeClass('is-open').attr('aria-hidden', 'true');
            document.body.style.overflow = previousBodyOverflow;
            if ($lastTrigger.length) {
                $lastTrigger.trigger('focus');
            }
        };
        $(document).on('click', '.preference-tag-info-button', function() {
            $lastTrigger = $(this);
            $modal.find('.preference-tag-detail-name').text('#' + String($lastTrigger.attr('data-name') || ''));
            $modal.find('.preference-tag-detail-code').text(String($lastTrigger.attr('data-code') || ''));
            $modal.find('.preference-tag-detail-description').text(String($lastTrigger.attr('data-description') || '등록된 고객 안내가 없습니다.'));
            $modal.find('.preference-tag-detail-admin-description').text(String($lastTrigger.attr('data-admin-description') || '등록된 운영자 분류 기준이 없습니다.'));
            previousBodyOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            $modal.addClass('is-open').attr('aria-hidden', 'false');
            $modal.find('.provider-tag-modal__close').trigger('focus');
        });
        $modal.on('click', '.provider-tag-modal__close', closeModal);
        $modal.on('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });
        $(document).on('keydown.providerPreferenceTagModal', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    }

    function updateProviderCategoryCodeInput() {
        const primaryKind = String($('select[name="kind"]').val() || '').trim();
        const secondKind = String($('#provider_kind_second').val() || '').trim();
        const thirdKind = String($('#provider_kind_third').val() || '').trim();
        let categoryCode = '';
        if (thirdKind !== '') {
            categoryCode = resolveProviderCategoryCodeByKind(thirdKind);
        }
        if (categoryCode === '' && secondKind !== '') {
            categoryCode = resolveProviderCategoryCodeByKind(secondKind);
        }
        if (categoryCode === '' && primaryKind !== '') {
            categoryCode = resolveProviderCategoryCodeByKind(primaryKind);
        }
        $('#provider_category_code').val(categoryCode);
        if (typeof window.toggleSharedProductSpec === 'function') {
            window.toggleSharedProductSpec(categoryCode, 'spec');
        }
        $('#provider-onahole-second-category-guide').toggle(primaryKind === 'ONAHOLE' && secondKind === '');
        $('#provider-torso-third-category-guide').toggle(primaryKind === 'TORSO' && secondKind === 'TORSO' && thirdKind === '');
        revalidateProviderAdditionalCategories();
    }

    function renderProviderSecondCategorySelect(resetSelection) {
        const primaryKind = String($('select[name="kind"]').val() || '').trim();
        const childCategories = Array.isArray(providerCategoryChildrenByKind[primaryKind]) ? providerCategoryChildrenByKind[primaryKind] : [];
        const $secondWrap = $('#provider_kind_second_wrap');
        const $secondSelect = $('#provider_kind_second');

        $secondSelect.empty();
        $secondSelect.append('<option value="">2차 카테고리 선택</option>');

        if (childCategories.length === 0) {
            $secondWrap.hide();
            $('#provider_kind_third_wrap').hide();
            updateProviderCategoryCodeInput();
            return;
        }

        for (let i = 0; i < childCategories.length; i++) {
            const child = childCategories[i] || {};
            const childKey = String(child.key || '').trim();
            const childName = String(child.name || childKey).trim();
            if (!childKey) {
                continue;
            }
            $secondSelect.append(
                $('<option>', {
                    value: childKey,
                    text: childName
                })
            );
        }

        if (!resetSelection && !hasAppliedInitialProviderSecondCategory && initialProviderSecondKindKey) {
            $secondSelect.val(initialProviderSecondKindKey);
            hasAppliedInitialProviderSecondCategory = true;
        } else {
            $secondSelect.val('');
        }

        $secondWrap.show();
        renderProviderThirdCategorySelect(resetSelection);
    }

    function renderProviderThirdCategorySelect(resetSelection) {
        const secondKind = String($('#provider_kind_second').val() || '').trim();
        const primaryKind = String($('select[name="kind"]').val() || '').trim();
        const childCategories = Array.isArray(providerCategoryChildrenByKind[primaryKind]) ? providerCategoryChildrenByKind[primaryKind] : [];
        const selectedChild = childCategories.find(function(child) {
            return String((child || {}).key || '').trim() === secondKind;
        }) || {};
        const grandchildCategories = Array.isArray(selectedChild.children) ? selectedChild.children : [];
        const $thirdWrap = $('#provider_kind_third_wrap');
        const $thirdSelect = $('#provider_kind_third');

        $thirdSelect.empty().append('<option value="">3차 카테고리 선택</option>');
        if (grandchildCategories.length === 0) {
            $thirdWrap.hide();
            updateProviderCategoryCodeInput();
            return;
        }

        grandchildCategories.forEach(function(grandchild) {
            const grandchildKey = String((grandchild || {}).key || '').trim();
            if (grandchildKey !== '') {
                $thirdSelect.append($('<option>', {
                    value: grandchildKey,
                    text: String(grandchild.name || grandchildKey)
                }));
            }
        });

        if (!resetSelection && !hasAppliedInitialProviderThirdCategory && initialProviderThirdKindKey) {
            $thirdSelect.val(initialProviderThirdKindKey);
            hasAppliedInitialProviderThirdCategory = true;
        } else {
            $thirdSelect.val('');
        }
        $thirdWrap.show();
        updateProviderCategoryCodeInput();
    }

    function initProviderCategorySelector() {
        $('select[name="kind"]').on('change', function() {
            renderProviderSecondCategorySelect(true);
        });
        $('#provider_kind_second').on('change', function() {
            renderProviderThirdCategorySelect(true);
        });
        $('#provider_kind_third').on('change', function() {
            updateProviderCategoryCodeInput();
        });
        renderProviderSecondCategorySelect(false);
    }

    function normalizeGodoCategoryItems(items) {
        if (!Array.isArray(items)) {
            return [];
        }

        const normalizedItems = [];
        const dedupMap = {};

        items.forEach(function(item) {
            if (!item || typeof item !== 'object') {
                return;
            }

            const depth1Code = String(item.depth1Code || '').trim();
            const depth2Code = String(item.depth2Code || '').trim();
            const depth3Code = String(item.depth3Code || '').trim();
            const selectedCode = String(item.selectedCode || item.key || depth3Code || depth2Code || depth1Code).trim();
            const pathLabel = String(item.pathLabel || '').trim();

            if (!selectedCode || dedupMap[selectedCode]) {
                return;
            }

            dedupMap[selectedCode] = true;
            normalizedItems.push({
                key: String(item.key || selectedCode).trim(),
                depth1Code: depth1Code,
                depth2Code: depth2Code,
                depth3Code: depth3Code,
                selectedCode: selectedCode,
                pathLabel: pathLabel
            });
        });

        return normalizedItems;
    }

    function getDepth1CategoryMapByKind(kindName) {
        const targetKind = String(kindName || '').trim();
        if (!targetKind) {
            return {};
        }
        const targetKindName = String(providerKindNameByCode[targetKind] || targetKind).trim();

        const depth1Map = {};
        Object.entries(godoCateTree || {}).forEach(function(entry) {
            const code = entry[0];
            const node = entry[1] || {};
            if (String(node.name || '').trim() === targetKindName) {
                depth1Map[code] = node;
            }
        });

        return depth1Map;
    }

    function resetSelectOptions($select, placeholderText) {
        $select.empty();
        $select.append($('<option>', {
            value: '',
            text: placeholderText
        }));
    }

    function fillSelectOptions($select, options, placeholderText, selectedValue) {
        resetSelectOptions($select, placeholderText);

        const optionEntries = Object.entries(options || {});
        optionEntries.forEach(function(entry) {
            const code = entry[0];
            const node = entry[1] || {};
            $select.append($('<option>', {
                value: code,
                text: node.name || code
            }));
        });

        if (selectedValue && options && options[selectedValue]) {
            $select.val(selectedValue);
        } else if (optionEntries.length === 1) {
            $select.val(optionEntries[0][0]);
        } else {
            $select.val('');
        }

        $select.prop('disabled', optionEntries.length === 0);
    }

    function updateDepth2AndDepth3(depth2Code, depth3Code) {
        const depth1Code = $('#godo_cate_depth1').val();
        const kindName = $('select[name="kind"]').val();
        const depth1Map = getDepth1CategoryMapByKind(kindName);
        const depth1Node = (depth1Map && depth1Map[depth1Code]) ? depth1Map[depth1Code] : null;
        const depth2Map = depth1Node && depth1Node.children ? depth1Node.children : {};

        fillSelectOptions($('#godo_cate_depth2'), depth2Map, '2차 분류 선택', depth2Code);

        const selectedDepth2Code = $('#godo_cate_depth2').val();
        const depth2Node = (depth2Map && depth2Map[selectedDepth2Code]) ? depth2Map[selectedDepth2Code] : null;
        const depth3Map = depth2Node && depth2Node.children ? depth2Node.children : {};

        fillSelectOptions($('#godo_cate_depth3'), depth3Map, '3차 분류 선택', depth3Code);
    }

    function updateGodoCategorySelector() {
        const kindName = $('select[name="kind"]').val();
        const depth1Map = getDepth1CategoryMapByKind(kindName);
        const hasCategory = Object.keys(depth1Map).length > 0;

        if (!hasCategory) {
            $('#godoCateSelectorWrap').hide();
            $('#godoCateSelectorGuide').show();
            resetSelectOptions($('#godo_cate_depth1'), '1차 분류 선택');
            resetSelectOptions($('#godo_cate_depth2'), '2차 분류 선택');
            resetSelectOptions($('#godo_cate_depth3'), '3차 분류 선택');
            $('#godo_cate_depth1, #godo_cate_depth2, #godo_cate_depth3').prop('disabled', true);
            selectedGodoCategoryItems.length = 0;
            renderSelectedGodoCategoryItems();
            return;
        }

        $('#godoCateSelectorWrap').show();
        $('#godoCateSelectorGuide').hide();

        const initDepth1 = (!hasAppliedInitialGodoCate ? selectedGodoCate.depth1 : '');
        const initDepth2 = (!hasAppliedInitialGodoCate ? selectedGodoCate.depth2 : '');
        const initDepth3 = (!hasAppliedInitialGodoCate ? selectedGodoCate.depth3 : '');

        fillSelectOptions($('#godo_cate_depth1'), depth1Map || {}, '1차 분류 선택', initDepth1);
        updateDepth2AndDepth3(initDepth2, initDepth3);

        hasAppliedInitialGodoCate = true;
    }

    function getCurrentGodoCategorySelection() {
        const depth1Code = String($('#godo_cate_depth1').val() || '').trim();
        const depth2Code = String($('#godo_cate_depth2').val() || '').trim();
        const depth3Code = String($('#godo_cate_depth3').val() || '').trim();

        if (!depth1Code) {
            return null;
        }

        const depth1Name = String($('#godo_cate_depth1 option:selected').text() || '').trim();
        const depth2Name = depth2Code ? String($('#godo_cate_depth2 option:selected').text() || '').trim() : '';
        const depth3Name = depth3Code ? String($('#godo_cate_depth3 option:selected').text() || '').trim() : '';

        const key = depth3Code || depth2Code || depth1Code;
        const pathNames = [depth1Name, depth2Name, depth3Name].filter(Boolean);
        const pathLabel = pathNames.join(' > ');

        return {
            key: key,
            depth1Code: depth1Code,
            depth2Code: depth2Code,
            depth3Code: depth3Code,
            depth1Name: depth1Name,
            depth2Name: depth2Name,
            depth3Name: depth3Name,
            selectedCode: key,
            pathLabel: pathLabel
        };
    }

    function getGodoCategoryItemsToAdd(selectedItem) {
        const items = [];

        if (!selectedItem || !selectedItem.depth1Code) {
            return items;
        }

        items.push({
            key: selectedItem.depth1Code,
            depth1Code: selectedItem.depth1Code,
            depth2Code: '',
            depth3Code: '',
            selectedCode: selectedItem.depth1Code,
            pathLabel: selectedItem.depth1Name || selectedItem.depth1Code
        });

        if (selectedItem.depth2Code) {
            items.push({
                key: selectedItem.depth2Code,
                depth1Code: selectedItem.depth1Code,
                depth2Code: selectedItem.depth2Code,
                depth3Code: '',
                selectedCode: selectedItem.depth2Code,
                pathLabel: [selectedItem.depth1Name, selectedItem.depth2Name].filter(Boolean).join(' > ')
            });
        }

        if (selectedItem.depth3Code) {
            items.push({
                key: selectedItem.depth3Code,
                depth1Code: selectedItem.depth1Code,
                depth2Code: selectedItem.depth2Code,
                depth3Code: selectedItem.depth3Code,
                selectedCode: selectedItem.depth3Code,
                pathLabel: [selectedItem.depth1Name, selectedItem.depth2Name, selectedItem.depth3Name].filter(Boolean).join(' > ')
            });
        }

        return items;
    }

    function renderSelectedGodoCategoryItems() {
        const $list = $('#selectedGodoCategoryList');
        const $inputs = $('#selectedGodoCategoryInputs');
        $list.empty();
        $inputs.empty();
        $inputs.append('<input type="hidden" name="cate_json" id="cate_json" value="">');

        if (selectedGodoCategoryItems.length === 0) {
            $list.append('<div class="admin-guide-text">선택된 분류가 없습니다.</div>');
            return;
        }

        selectedGodoCategoryItems.forEach(function(item, index) {
            const displayLabel = '[' + String(item.selectedCode || item.key || '') + '] ' + String(item.pathLabel || '');
            const itemHtml = ''
                + '<div style="display:flex; align-items:center; gap:6px; margin-top:6px;">'
                + '  <span style="display:inline-block; padding:4px 8px; background:#f5f5f5; border:1px solid #ddd; border-radius:3px;">'
                +      $('<div>').text(displayLabel).html()
                + '  </span>'
                + '  <button type="button" class="btnstyle1 btnstyle1-default btnstyle1-xs remove-godo-cate-btn" data-index="' + index + '">삭제</button>'
                + '</div>';
            $list.append(itemHtml);

            $inputs.append('<input type="hidden" name="godo_cate_selected[]" value="' + $('<div>').text(item.selectedCode || '').html() + '">');
            $inputs.append('<input type="hidden" name="godo_cate_selected_depth1[]" value="' + $('<div>').text(item.depth1Code || '').html() + '">');
            $inputs.append('<input type="hidden" name="godo_cate_selected_depth2[]" value="' + $('<div>').text(item.depth2Code || '').html() + '">');
            $inputs.append('<input type="hidden" name="godo_cate_selected_depth3[]" value="' + $('<div>').text(item.depth3Code || '').html() + '">');
        });

        $('#cate_json').val(JSON.stringify(selectedGodoCategoryItems));
    }

    function addSelectedGodoCategory() {
        const selectedItem = getCurrentGodoCategorySelection();
        if (!selectedItem) {
            alert('1차 분류를 선택해주세요.');
            return;
        }

        const candidates = getGodoCategoryItemsToAdd(selectedItem);
        let addedCount = 0;

        candidates.forEach(function(candidate) {
            const exists = selectedGodoCategoryItems.some(function(item) {
                return item.key === candidate.key;
            });
            if (!exists) {
                selectedGodoCategoryItems.push(candidate);
                addedCount += 1;
            }
        });

        if (addedCount === 0) {
            alert('이미 추가된 분류입니다.');
            return;
        }

        renderSelectedGodoCategoryItems();
    }

    function initGodoCategorySelector() {
        const normalized = normalizeGodoCategoryItems(selectedGodoCategoryItems);
        selectedGodoCategoryItems.length = 0;
        normalized.forEach(function(item) {
            selectedGodoCategoryItems.push(item);
        });

        $('select[name="kind"]').on('change', function() {
            updateGodoCategorySelector();
        });

        $('#godo_cate_depth1').on('change', function() {
            updateDepth2AndDepth3('', '');
        });

        $('#godo_cate_depth2').on('change', function() {
            const kindName = $('select[name="kind"]').val();
            const depth1Map = getDepth1CategoryMapByKind(kindName);
            const depth1Code = $('#godo_cate_depth1').val();
            const depth2Code = $('#godo_cate_depth2').val();
            const depth1Node = (depth1Map && depth1Map[depth1Code]) ? depth1Map[depth1Code] : null;
            const depth2Map = depth1Node && depth1Node.children ? depth1Node.children : {};
            const depth2Node = (depth2Map && depth2Map[depth2Code]) ? depth2Map[depth2Code] : null;
            const depth3Map = depth2Node && depth2Node.children ? depth2Node.children : {};
            fillSelectOptions($('#godo_cate_depth3'), depth3Map, '3차 분류 선택', '');
        });

        $('#addGodoCategoryBtn').on('click', function() {
            addSelectedGodoCategory();
        });

        $(document).on('click', '.remove-godo-cate-btn', function() {
            const index = Number($(this).data('index'));
            if (Number.isNaN(index) || index < 0 || index >= selectedGodoCategoryItems.length) {
                return;
            }
            selectedGodoCategoryItems.splice(index, 1);
            renderSelectedGodoCategoryItems();
        });

        renderSelectedGodoCategoryItems();
        updateGodoCategorySelector();
    }

    function requestProviderSaleStop(actionMode, prdIdx) {
        if (!prdIdx) {
            alert('위탁상품 고유번호가 없습니다.');
            return;
        }

        ajaxRequest('/admin/provider_product/action', {
            action_mode: actionMode,
            prd_idx: prdIdx,
            action_url: window.location.pathname + window.location.search
        })
            .done(function(res) {
                if (res && (res.success || res.status === 'success')) {
                    alert(res.message || '처리가 완료되었습니다.');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '처리 실패');
                }
            })
            .fail(function(res) {
                alert(res && res.message ? res.message : '에러');
            });
    }

    function setProductDiscontinued(prdIdx, switchFromHandlingStopped) {
        if (switchFromHandlingStopped && !confirm('취급중단이 해제되고 단종으로 변경됩니다. 진행할까요?')) {
            return;
        }
        requestProviderSaleStop('set_product_discontinued', prdIdx);
    }

    function unsetProductDiscontinued(prdIdx) {
        requestProviderSaleStop('unset_product_discontinued', prdIdx);
    }

    function setProductHandlingStopped(prdIdx, switchFromDiscontinued) {
        if (switchFromDiscontinued && !confirm('단종이 해제되고 취급중단으로 변경됩니다. 진행할까요?')) {
            return;
        }
        requestProviderSaleStop('set_product_handling_stopped', prdIdx);
    }

    function unsetProductHandlingStopped(prdIdx) {
        requestProviderSaleStop('unset_product_handling_stopped', prdIdx);
    }

    /**
     * 공급사 사이트 디테일 크롤링후 업데이트
     */
    function updateSupplierProductDetail() {

        // 로딩 시작 시 화면 최상단으로 이동 후 스크롤 잠금
        window.scrollTo(0, 0);
        $('html, body').scrollTop(0).css('overflow', 'hidden');
        $('#update_supplier_product_detail_loading').addClass('active');


        var payload = {
            action_mode: 'update_supplier_product_detail',
            prd_idx: '<?= $prd_data['idx'] ?>',
            supplier_prd_pk: '<?= $prd_data['supplier_prd_pk'] ?>',
        };

        ajaxRequest('/admin/provider_product/action', payload)
            .done(function(res) {
                if (res && (res.success || res.status === 'success')) {
                    alert(res.message || '등록대기 처리되었습니다.');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '처리 실패');
                }
            })
            .fail(function(res) {
                alert(res && res.message ? res.message : '에러');
            });

    }

    /**
     * 고도몰 매칭하고 등록완료로 변경
     */
    function matchGodoGoods() {

        const godo_goodsNo = ($('#godo_goodsNo').val() || '').toString().trim();

        if (!godo_goodsNo) {
            alert('고도몰 상품번호를 입력해주세요.');
            return;
        }

        prdProviderInfo.loadGodoGoodsInfo('<?= $prd_data['idx'] ?>', godo_goodsNo);

    }

    function copySupplierDetailImgHtml() {
        var $target = $('#supplier_detail_img_html_code');
        if ($target.length === 0) {
            alert('복사할 HTML 코드가 없습니다.');
            return;
        }

        var text = String($target.val() || '');
        if (!text) {
            alert('복사할 HTML 코드가 없습니다.');
            return;
        }

        var onSuccess = function() {
            if (typeof showToast === 'function') {
                showToast('상세이미지 HTML 코드가 복사되었습니다.', new Date().toLocaleTimeString());
            } else {
                alert('상세이미지 HTML 코드가 복사되었습니다.');
            }
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(onSuccess).catch(function() {
                $target.trigger('focus').trigger('select');
                document.execCommand('copy');
                onSuccess();
            });
            return;
        }

        $target.trigger('focus').trigger('select');
        document.execCommand('copy');
        onSuccess();
    }

    function copyInputValue(inputId, fieldName) {
        var $target = $('#' + inputId);
        if ($target.length === 0) {
            alert('복사할 입력값을 찾을 수 없습니다.');
            return;
        }

        var text = String($target.val() || '');
        if (!text) {
            alert('복사할 값이 없습니다.');
            return;
        }

        var successMessage = (fieldName || '입력값') + '이(가) 복사되었습니다.';
        var onSuccess = function() {
            if (typeof showToast === 'function') {
                showToast(successMessage, new Date().toLocaleTimeString());
            } else {
                alert(successMessage);
            }
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(onSuccess).catch(function() {
                $target.trigger('focus').trigger('select');
                document.execCommand('copy');
                onSuccess();
            });
            return;
        }

        $target.trigger('focus').trigger('select');
        document.execCommand('copy');
        onSuccess();
    }

    function applyQuickMemo(memoText) {
        var $memo = $('input[name="memo"]');
        if ($memo.length === 0 || !memoText) {
            return;
        }

        $memo.val(String(memoText));
        $memo.trigger('focus');
    }

</script>