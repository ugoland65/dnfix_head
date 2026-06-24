<style>
.prd-image-preview-trigger {
    cursor: zoom-in;
}
.prd-image-preview-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0, 0, 0, 0.75);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.prd-image-preview-modal.is-open {
    display: flex;
}
.prd-image-preview-content {
    position: relative;
    max-width: 1000px;
    max-height: 1000px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.prd-image-preview-modal img {
    max-width: 1000px;
    max-height: 1000px;
    width: auto;
    height: auto;
    border: 1px solid #e5e7eb;
    background: #fff;
}
.prd-image-preview-close {
    position: fixed;
    top: 16px;
    right: 16px;
    z-index: 10000;
}
</style>
<form name='prd_form' id='prd_form' method='post' enctype="multipart/form-data" autocomplete="off">

    <input type="hidden" name="idx" value="<?= $productData['CD_IDX'] ?? '' ?>">

    <table class="table-style ">
        <colgroup>
            <col width="150px" />
            <col />
        </colgroup>
        <tr>
            <td colspan="2" class="none-bg title">
                <h1>상품 기본정보</h1>
            </td>
        </tr>

        <tbody>
            <tr>
                <th>상품 구분</th>
                <td>
                    <?php
                        $categoryRows = (isset($categories) && is_array($categories)) ? $categories : [];
                        $categoryCodeByKind = [];
                        $categoryChildrenByKind = [];
                        foreach ($categoryRows as $categoryRow) {
                            if (!is_array($categoryRow)) {
                                continue;
                            }
                            $parentKey = trim((string)($categoryRow['key'] ?? ''));
                            $parentCode = trim((string)($categoryRow['code'] ?? ''));
                            if ($parentKey !== '' && $parentCode !== '') {
                                $categoryCodeByKind[$parentKey] = $parentCode;
                            }

                            $children = (isset($categoryRow['children']) && is_array($categoryRow['children'])) ? $categoryRow['children'] : [];
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
                                $childOptions[] = [
                                    'key' => $childKey,
                                    'code' => $childCode,
                                    'name' => $childName !== '' ? $childName : $childKey,
                                ];
                            }
                            if ($parentKey !== '' && !empty($childOptions)) {
                                $categoryChildrenByKind[$parentKey] = $childOptions;
                            }
                        }

                        $selectedKindCode = trim((string)($productData['CD_KIND_CODE'] ?? ''));
                        $selectedCategoryCode = trim((string)($productData['CD_CATEGORY_CODE'] ?? ''));
                        if ($selectedCategoryCode === '' && isset($categoryCodeByKind[$selectedKindCode])) {
                            $selectedCategoryCode = (string)$categoryCodeByKind[$selectedKindCode];
                        }

                        $selectedSecondKindKey = '';
                        $selectedKindChildren = $categoryChildrenByKind[$selectedKindCode] ?? [];
                        if (!empty($selectedKindChildren)) {
                            foreach ($selectedKindChildren as $childOption) {
                                if ((string)($childOption['code'] ?? '') === $selectedCategoryCode) {
                                    $selectedSecondKindKey = (string)($childOption['key'] ?? '');
                                    break;
                                }
                            }
                        }
                    ?>
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <select name="cd_kind_code">
                            <option value=''>상품 구분 선택</option>
                            <?php foreach ($prd_kind_name as $key => $kind) { ?>
                                <option value="<?= $key ?>" <?php if ($productData['CD_KIND_CODE'] == $key) echo "selected"; ?>><?= $kind ?></option>
                            <?php } ?>
                        </select>
                        <input type="hidden" name="cd_category_code" id="cd_category_code" value="<?= htmlspecialchars($selectedCategoryCode, ENT_QUOTES, 'UTF-8') ?>">
                        <div id="cd_kind_code_second_wrap" style="display:none;">
                            <select name="cd_kind_code_second" id="cd_kind_code_second">
                                <option value="">2차 카테고리 선택</option>
                            </select>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>브랜드</th>
                <td>
                    <select name="cd_brand_idx" class="dn-select2">
                        <option value=''>브랜드 선택</option>
                        <?php
                        foreach ($brandForSelect as $brand) {
                            if (!is_array($brand)) continue;
                        ?>
                            <option value='<?= $brand['BD_IDX'] ?? '' ?>' <?php if (($brand['BD_IDX'] ?? '') == ($productData['CD_BRAND_IDX'] ?? '')) echo "selected"; ?>><?= $brand['BD_NAME'] ?? '' ?></option>
                        <?php } ?>
                    </select>
                    <select name="cd_brand2_idx" class="dn-select2">
                        <option value=''>브랜드2 선택</option>
                        <?php
                        foreach ($brandForSelect as $brand) {
                            if (!is_array($brand)) continue;
                        ?>
                            <option value='<?= $brand['BD_IDX'] ?? '' ?>' <?php if (($brand['BD_IDX'] ?? '') == ($productData['CD_BRAND2_IDX'] ?? '')) echo "selected"; ?>><?= $brand['BD_NAME'] ?? '' ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>상품명</th>
                <td><input type='text' name='cd_name' size='40' value="<?= $productData['CD_NAME'] ?? '' ?>"></td>
            </tr>
            <tr>
                <th>원 상품명</th>
                <td><input type='text' name='cd_name_og' size='40' value="<?= $productData['CD_NAME_OG'] ?? '' ?>"></td>
            </tr>
            <tr>
                <th>영문 상품명</th>
                <td><input type='text' name='cd_name_en' size='40' value="<?= $productData['CD_NAME_EN'] ?? '' ?>"></td>
            </tr>

            <tr>
                <th>운영 이미지</th>
                <td>

                    <div class="img-upload-wrap">
                        <ul>
                            <h3>기본 이미지</h3>
                            <div class="admin-guide-text">
                                302 x 302(px)
                            </div>
                            <div class="img-box">
                                <?php
                                if ($productData['CD_IMG'] ?? '') {
                                    //$img_path = '/data/comparion/' . $productData['CD_IMG'];

                                    if( $productData['img_mode'] == 'out' ){
                                        if (!empty($productData['CD_IMG'])) {
                                            $img_path = $productData['CD_IMG'];
                                        }
                                    }else{
                                        if (!empty($productData['CD_IMG'])) {
                                            $img_path = '/data/comparion/' . $productData['CD_IMG'];
                                        }
                                    }

                                ?>
                                    <div class="m-b-15">
                                        <img src="<?= $img_path ?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
                                    </div>
                                <?php } ?>

                                <div class="img-upload-file-wrap">
                                    <ul>
                                        <input type='file' name='cd_img'>
                                    </ul>
                                    <ul>
                                        <input type='text' name='out_img' value="" placeholder="URL로 저장">
                                    </ul>
                                    <ul>
                                        <label><input type="radio" name="img_mode" value="out" <?php if (($productData['img_mode'] ?? '') == "out") echo "checked"; ?>> 외부서버 이미지</label>
                                        <label><input type="radio" name="img_mode" value="this" <?php if (($productData['img_mode'] ?? '') == "this") echo "checked"; ?>> 내부서버 이미지</label>
                                    </ul>
                                </div>

                                <?php if ($productData['CD_IMG'] ?? '') { ?>
                                    <div class="m-t-10 cd-img-text-wrap"><?= $productData['CD_IMG'] ?></div>
                                <?php } ?>
                            </div>
                        </ul>

                        <ul>
                            <h3>중량 실사 이미지</h3>
                            <div class="admin-guide-text">
                                플라스틱 함유량 첨부 실사 이미지
                            </div>
                            <div class="img-box">
                                <?php
                                if ($productData['cd_add_img']['add1']['filename'] ?? '') {
                                    $img_add1 = '/data/comparion/' . $productData['cd_add_img']['add1']['filename'];
                                ?>
                                    <div class="m-b-15">
                                        <img
                                            src="<?= $img_add1 ?>"
                                            data-preview-src="<?= $img_add1 ?>"
                                            class="prd-image-preview-trigger"
                                            style="height:100px; margin-left:20px; border:1px solid #eee !important;"
                                        >
                                    </div>
                                <?php } ?>

                                <input type='file' name='cd_add1'>

                                <?php if ($productData['cd_add_img']['add1']['filename'] ?? '') { ?>
                                    <div class="m-t-10"><?= $productData['cd_add_img']['add1']['filename'] ?></div>
                                <?php } ?>

                                <p style="font-size:12px; color:#ff0000;">해당값을 [실측 상품중량]에 꼭 기입해주세요.</p>
                            </div>
                        </ul>

                        <ul>
                            <h3>출고 이미지</h3>
                            <div class="admin-guide-text">
                                출고 시 확인가능한 실세 사진
                            </div>
                            <div class="img-box">
                                <?php
                                if ($productData['cd_add_img']['add3']['filename'] ?? '') {
                                    $img_add3 = '/data/comparion/' . $productData['cd_add_img']['add3']['filename'];
                                ?>
                                    <div class="m-b-15">
                                        <img src="<?= $img_add3 ?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
                                    </div>
                                <?php } ?>

                                <input type='file' name='cd_add3'>

                                <?php if ($productData['cd_add_img']['add3']['filename'] ?? '') { ?>
                                    <div class="m-t-10"><?= $productData['cd_add_img']['add3']['filename'] ?></div>
                                <?php } ?>
                            </div>
                        </ul>

                    </div>

                </td>
            </tr>

            <tr>
                <th>오나DB 이미지</th>
                <td>
                    <div class="img-upload-wrap">
                        <ul>
                            <h3>아이콘 이미지</h3>
                            <div class="admin-guide-text">
                                100 x 100(px) 오나DB 목록을 위한 이미지
                            </div>
                            <div class="img-box">
                                <?php
                                if ($productData['CD_IMG2'] ?? '') {
                                    $img_path2 = '/data/comparion/' . $productData['CD_IMG2'];
                                ?>
                                    <div class="m-b-15">
                                        <img src="<?= $img_path2 ?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
                                    </div>
                                <?php } ?>

                                <input type='file' name='cd_img2'>

                                <?php if ($productData['CD_IMG2'] ?? '') { ?>
                                    <div class="m-t-10"><?= $productData['CD_IMG2'] ?></div>
                                <?php } ?>
                            </div>
                        </ul>
                        <ul>
                            <h3>19금 대체 이미지</h3>
                            <div class="admin-guide-text">
                                오나 DB에서 19금 대체로 노출되는 이미지
                            </div>
                            <div class="img-box">
                                <?php
                                if ($productData['cd_add_img']['add2']['filename'] ?? '') {
                                    $img_add2 = '/data/comparion/' . $productData['cd_add_img']['add2']['filename'];
                                ?>
                                    <div class="m-b-15">
                                        <img src="<?= $img_add2 ?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
                                    </div>
                                <?php } ?>

                                <input type='file' name='cd_add2'>

                                <?php if ($productData['cd_add_img']['add2']['filename'] ?? '') { ?>
                                    <div class="m-t-10"><?= $productData['cd_add_img']['add2']['filename'] ?></div>
                                <?php } ?>
                            </div>
                        </ul>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="none-bg" style="height:15px;"></td>
            </tr>

            <tr>
                <th>작업체크</th>
                <td>
                    <?php
                        $workCheckList = $productData['work_check_list'] ?? [];
                    ?>
                    <?php if (!empty($workCheckList)) { ?>

                        <!--
                        <div class="admin-guide-text m-b-8">
                            - 분류에 맞는 작업 체크리스트입니다. 항목 추가/수정은 `prd_work_check_item` 테이블에서 관리합니다.
                        </div>
                        -->

                        <div style="display:flex; flex-wrap:wrap; gap:10px;">
                            <?php foreach ($workCheckList as $task) { ?>
                                <?php
                                    $taskCode = (string)($task['task_code'] ?? '');
                                    $taskLabel = (string)($task['task_label'] ?? $taskCode);
                                    $isChecked = !empty($task['is_checked']);
                                    if ($taskCode === '') {
                                        continue;
                                    }
                                ?>
                                <label class="work-check-chip <?= $isChecked ? 'is-done' : 'is-todo' ?>" style="display:inline-flex; align-items:center; gap:8px; padding:8px 10px; border-radius:8px; border:1px solid <?= $isChecked ? '#22c55e' : '#d1d5db' ?>; background:<?= $isChecked ? '#f0fdf4' : '#f9fafb' ?>; min-width:220px;">
                                    <input type="hidden" name="work_task_codes[]" value="<?= htmlspecialchars($taskCode, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="checkbox" name="work_task_done[<?= htmlspecialchars($taskCode, ENT_QUOTES, 'UTF-8') ?>]" value="Y" <?= $isChecked ? 'checked' : '' ?>>
                                    <span style="font-weight:600; color:#111827;"><?= htmlspecialchars($taskLabel, ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="work-check-state" style="margin-left:auto; font-size:11px; font-weight:700; color:<?= $isChecked ? '#15803d' : '#6b7280' ?>;">
                                        <?= $isChecked ? '완료' : '미완료' ?>
                                    </span>
                                </label>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="admin-guide-text">
                            - 현재 분류에 연결된 작업체크 항목이 없습니다. `prd_work_check_item`에 항목을 추가해 주세요.
                        </div>
                    <?php } ?>
                </td>
            </tr>

            <?php if (!empty($productData['ps_idx'])) { ?>
                <tr>
                    <th>할인중 설정</th>
                    <td>
                        <?php if ($productData['is_sale_month']) { ?>
                            <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm " onclick="prdDetailBasicForm.unsetProductSale('<?= $productData['CD_IDX'] ?? '' ?>','<?= $productData['ps_idx'] ?? '' ?>', 'monthly')">월간할인 해제</button>
                        <?php } else { ?>
                            <button type="button" class="btnstyle1 btnstyle1-sm" onclick="prdDetailBasicForm.setProductSale('<?= $productData['CD_IDX'] ?? '' ?>','<?= $productData['ps_idx'] ?? '' ?>', 'monthly')">월간할인 지정</button>
                        <?php } ?>

                        <?php if ($productData['is_sale_special']) { ?>
                            <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm " onclick="prdDetailBasicForm.unsetProductSale('<?= $productData['CD_IDX'] ?? '' ?>','<?= $productData['ps_idx'] ?? '' ?>', 'special')">특가할인 해제</button>
                        <?php } else { ?>
                            <button type="button" class="btnstyle1 btnstyle1-sm" onclick="prdDetailBasicForm.setProductSale('<?= $productData['CD_IDX'] ?? '' ?>','<?= $productData['ps_idx'] ?? '' ?>', 'special')">특가할인 지정</button>
                        <?php } ?>

                        할인대상 :
                        <label><input type="radio" name="ps_discount_target_yn" value="Y" <?php if (($productData['ps_discount_target_yn'] ?? 'Y') === 'Y') echo "checked"; ?>> 해당대상</label>
                        <label><input type="radio" name="ps_discount_target_yn" value="N" <?php if (($productData['ps_discount_target_yn'] ?? 'Y') === 'N') echo "checked"; ?>> 할인대상 제외</label>


                    </td>
                </tr>
            <?php } ?>

            <tr>
                <th>단종 설정</th>
                <td>
                    <?php if ($productData['is_discontinued']) { ?>
                        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm " onclick="prdDetailBasicForm.unsetProductDiscontinued('<?= $productData['CD_IDX'] ?? '' ?>')">단종 해제</button>
                    <?php } else { ?>
                        <button type="button" class="btnstyle1 btnstyle1-sm" onclick="prdDetailBasicForm.setProductDiscontinued('<?= $productData['CD_IDX'] ?? '' ?>')">단종 처리</button>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th>리스트 메모</th>
                <td>
                    <input type='text' name='cd_memo2' value="<?= $productData['cd_memo2'] ?? '' ?>" />
                    <div class="admin-guide-text">
                        - 상품목록에 노출되는 메모입니다.
                    </div>
                </td>
            </tr>
            <tr>
                <th>메모</th>
                <td>
                    <?php /*<input type='text' name='cd_memo'  value="<?=$productData['CD_MEMO'] ?? ''?>"> */ ?>
                    <textarea name="cd_memo" rows="5"><?= $productData['CD_MEMO'] ?? '' ?></textarea>
                    <div class="admin-guide-text">
                        - 외부에 노출되지 않는 인트라넷 전용 메모
                    </div>
                </td>
            </tr>

            <tr>
                <th>상품 검색어</th>
                <td>
                    <input type='text' name='cd_search_term' value="<?= $productData['CD_SEARCH_TERM'] ?? '' ?>">
                    <div class="admin-guide-text">
                        - 인트라넷, 오나디비 검색시 가능한 추가 검색어
                    </div>
                </td>
            </tr>
        </tbody>

        <tbody>
            <tr>
                <td colspan="2" class="none-bg" style="height:10px;"></td>
            </tr>
            <tr>
                <td colspan="2" class="none-bg title">
                    <h1>참고자료</h1>
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <th>자료참고 링크</th>
                <td>
                    <?php
                        $referenceLinks = $productData['cd_reference_links'] ?? [];
                        if (!is_array($referenceLinks)) {
                            $referenceLinks = [];
                        }
                        if (empty($referenceLinks)) {
                            $referenceLinks = [['title' => '', 'url' => '']];
                        }
                    ?>
                    <div id="reference_links_wrap">
                        <table class="table-style border01 width-full">
                            <colgroup>
                                <col width="200px" />
                                <col />
                                <col width="95px" />
                                <col width="80px" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center">링크명</th>
                                    <th class="text-center">URL</th>
                                    <th class="text-center">바로가기</th>
                                    <th class="text-center">삭제</th>
                                </tr>
                            </thead>
                            <tbody id="reference_links_tbody">
                                <?php foreach ($referenceLinks as $link) { ?>
                                    <tr class="reference-link-row">
                                        <td>
                                            <input type="text" name="reference_link_title[]" value="<?= htmlspecialchars((string)($link['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="예: 공급사 상세페이지" style="width:100%;">
                                        </td>
                                        <td>
                                            <input type="text" name="reference_link_url[]" value="<?= htmlspecialchars((string)($link['url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="https://example.com" style="width:100%;">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btnstyle1 btnstyle1-xs reference-link-open-btn">바로가기</button>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs remove-reference-link-btn">삭제</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="m-t-8">
                        <button type="button" id="add_reference_link_btn" class="btnstyle1 btnstyle1-sm">링크 추가</button>
                    </div>
                    <div class="admin-guide-text">
                        - 여러 링크를 추가할 수 있으며 상품정보를 수집할 수 있는 참고자료 URL를 입력해주세요. 예) 엠즈,NLS등등
                    </div>
                </td>
            </tr>
        </tbody>

        <tbody>
            <tr>
                <td colspan="2" class="none-bg" style="height:10px;"></td>
            </tr>
            <tr>
                <td colspan="2" class="none-bg title">
                    <h1>HBTI</h1>
                </td>
            </tr>
        </tbody>

        <!-- HBTI 설정 -->
        <tbody>
            <tr>
                <th>HBTI 대상</th>
                <td>
                    <label><input type="checkbox" name="hbti_target" value="N" <?php if (($productData['hbti_target'] ?? '') == "N") echo "checked"; ?>> 비대상</label>
                    <div class="admin-guide-text">
                        - 비대상 체크후 저장하면 HBTI 설정값이 초기화 되고 기존 데이터는 삭제됩니다.
                    </div>
                </td>
            </tr>
            <tr id="hbti-config-row" style="<?php if (($productData['hbti_target'] ?? '') == 'N') echo 'display:none;'; ?>">
                <th>HBTI</th>
                <td>

                    <table class="table-style border01">
                        <colgroup>
                            <col width="250px" />
                            <col />
                        </colgroup>
                        <tr>
                            <th>촉감 분석 (S/H)<br>softness (부드러움 정도)</th>
                            <td>
                                <label><input type="radio" name="hbti_1" value="S" <?php if (($productData['cd_hbti_data'][0] ?? '') == "S") echo "checked"; ?>> S (Soft)</label>
                                <label><input type="radio" name="hbti_1" value="H" <?php if (($productData['cd_hbti_data'][0] ?? '') == "H") echo "checked"; ?>> H (Hard)</label>
                            </td>
                            <td>
                                <div style="font-size:11px;">
                                    softness >= 7 → 부드러움 선호<br>
                                    softness < 7 → 강한 자극 선호
                                        </div>
                            </td>
                        </tr>

                        <tr>
                            <th>디자인 스타일 (R/F)<br>realistic_design (현실적 디자인 여부)</th>
                            <td>
                                <label><input type="radio" name="hbti_2" value="R" <?php if (($productData['cd_hbti_data'][1] ?? '') == "R") echo "checked"; ?>> R (Realistic)</label>
                                <label><input type="radio" name="hbti_2" value="F" <?php if (($productData['cd_hbti_data'][1] ?? '') == "F") echo "checked"; ?>> F (Fantasy)</label>
                            </td>
                            <td>
                                <div style="font-size:11px;">
                                    realistic_design == true → 실제감 높은 제품<br>
                                    realistic_design == false → 판타지 스타일
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>세척 & 관리 난이도 (J/P)<br>easy_to_clean (세척 용이성)</th>
                            <td>
                                <label><input type="radio" name="hbti_3" value="J" <?php if (($productData['cd_hbti_data'][2] ?? '') == "J") echo "checked"; ?>> J (Judging)</label>
                                <label><input type="radio" name="hbti_3" value="P" <?php if (($productData['cd_hbti_data'][2] ?? '') == "P") echo "checked"; ?>> P (Perceiving)</label>
                            </td>
                            <td>
                                <div style="font-size:11px;">
                                    easy_to_clean == true → 세척이 쉬움<br>
                                    easy_to_clean == false → 세척이 어려움
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>기능성 여부 (T/E)<br>has_tech_features (기술 포함 여부)</th>
                            <td>
                                <label><input type="radio" name="hbti_4" value="T" <?php if (($productData['cd_hbti_data'][3] ?? '') == "T") echo "checked"; ?>> T (Technical)</label>
                                <label><input type="radio" name="hbti_4" value="E" <?php if (($productData['cd_hbti_data'][3] ?? '') == "E") echo "checked"; ?>> E (Emotional)</label>
                            </td>
                            <td>
                                <div style="font-size:11px;">
                                    has_tech_features == true → 온열, 진동, 자동 기능 포함<br>
                                    has_tech_features == false → 기능보다 감성적 요소가 중요
                                </div>
                            </td>
                        </tr>

                    </table>

                </td>
            </tr>
        </tbody>

        <tbody>
            <tr>
                <td colspan="2" class="none-bg" style="height:10px;"></td>
            </tr>
            <tr>
                <td colspan="2" class="none-bg title">
                    <h1>고도몰</h1>
                </td>
            </tr>
        </tbody>

        <tbody>
            <tr>
                <th>고도몰 상품번호</th>
                <td>
                    <input type='text' name='cd_godo_code' style='width:200px;' value="<?= $productData['cd_godo_code'] ?? '' ?>">
                    <div class="admin-guide-text">
                        - 상품코드 아니고 상품번호 입니다.!!!!
                    </div>
                </td>
            </tr>

            <?php if (!empty($productData['cd_sale_price'])) { ?>
            <tr>
                <th>고도몰 판매가</th>
                <td>

                    <table class="table-style border01">
                        <tr>
                            <th>쑈당몰 판매가</th>
                            <td>
                                <b><?= number_format($productData['cd_sale_price']) ?> 원</b>
                            </td>
                            <th>책정 원가</th>
                            <td>
                                <b><?= number_format($productData['cd_cost_price']) ?> 원</b>
                            </td>
                            <th>마진율</th>
                            <td>
                                <b><?= number_format($productData['margin_per']) ?> %</b>
                                <span class="grade-badge grade-<?=$productData['margin_grade']?>">
                                    <?=$productData['margin_grade']?>
                                </span>
                            </td>
      
                        </tr>
                    </table>

                </td>
            </tr>
            <?php } ?>

        </tbody>

        <tbody>
            <tr>
                <td colspan="2" class="none-bg" style="height:10px;"></td>
            </tr>
            <tr>
                <td colspan="2" class="none-bg title">
                    <h1>사이트 (오나디비)</h1>
                </td>
            </tr>
        </tbody>

        <tbody>
            <tr>
                <th>사이트 옵션</th>
                <td>

                    <table class="table-style border01">
                        <colgroup>
                            <col width="150px" />
                            <col />
                        </colgroup>
                        <tr>
                            <th>오나디비 노출</th>
                            <td>
                                <label><input type="radio" name="cd_site_show" value="Y" <?php if (($productData['cd_site_show'] ?? '') == "Y" || !($productData['cd_site_show'] ?? '')) echo "checked"; ?>> 노출</label>
                                <label><input type="radio" name="cd_site_show" value="N" <?php if (($productData['cd_site_show'] ?? '') == "N") echo "checked"; ?>> 비노출</label>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
            <tr>
                <th>분류</th>
                <td>티어 정보는 오나DB 설정으로 옮겨감
                </td>
            </tr>
        </tbody>

        <tbody>
            <tr>
                <td colspan="2" class="none-bg" style="height:10px;"></td>
            </tr>
            <tr>
                <td colspan="2" class="none-bg title">
                    <h1>상품 상세정보</h1>
                </td>
            </tr>
        </tbody>

        <tbody>
            <tr>
                <th>출시일</th>
                <td>
                    <div class="calendar-input">
                        <input type='text' name='cd_release_date' value="<?= $productData['CD_RELEASE_DATE'] ?? '' ?>">
                    </div>
                </td>
            </tr>

            <tr>
                <th>패키지 사이즈</th>
                <td>

                    세로(H) : <input type='text' name='cd_size_h' value="<?= $productData['CD_SIZE']['H'] ?? '' ?>" style="width:60px">
                    가로(W) : <input type='text' name='cd_size_w' value="<?= $productData['CD_SIZE']['W'] ?? '' ?>" style="width:60px">
                    깊이(D) : <input type='text' name='cd_size_d' value="<?= $productData['CD_SIZE']['D'] ?? '' ?>" style="width:60px">
                    <div class="admin-guide-text">
                        - 단위 mm (숫자만 등록할것)
                    </div>

                </td>
            </tr>

            <tr>
                <th>내부길이</th>
                <td>
                    <input type='text' name='cd_size2' style='width:100px;' value="<?= $productData['CD_SIZE2'] ?? '' ?>"> ( Cm )
                    <div class="admin-guide-text">
                        ※ 젤일때는 용량( ml )
                    </div>
                </td>
            </tr>

            <tr>
                <th>중량</th>
                <td>

                    <table class="table-style border01">
                        <colgroup>
                            <col width="150px" />
                            <col />
                        </colgroup>
                        <tr>
                            <th>상품중량</th>
                            <td>
                                <input type='text' name='cd_weight_1' style='width:80px;' value="<?= $productData['cd_weight_fn']['1'] ?? '' ?>"> g
                                ※ 제공된 상품 상세페이지에 기재된 상품중량 ( 패키지 미포함 )
                                <div class="admin-guide-text">
                                    - 쑈당몰 카테고리 지정시 브랜드 제공 중량으로 표기해야 고객이 혼선없음
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>전체중량</th>
                            <td>
                                <input type='text' name='cd_weight_2' style='width:80px;' value="<?= $productData['cd_weight_fn']['2'] ?? '' ?>"> g
                                ※ 제공된 상품 상세페이지에 기재된 패키지를 포함한 전체 중량 (없다면 생략 가능)
                            </td>
                        </tr>
                        <tr>
                            <th>실측 상품중량</th>
                            <td>
                                <input type='text' name='cd_weight_4' style='width:80px;' value="<?= $productData['cd_weight_fn']['4'] ?? '' ?>"> g
                                ※ 패키지를 제외한 상품만 실제 측정한 중량
                            </td>
                        </tr>
                        <tr>
                            <th>실측 전체중량</th>
                            <td>
                                <input type='text' name='cd_weight_3' style='width:80px;' value="<?= $productData['cd_weight_fn']['3'] ?? '' ?>"> g
                                ※ 패키지를 포함한 실제 측정한 중량
                            </td>
                        </tr>
                    </table>

                    <div class="admin-guide-text">
                        - 단위 g (숫자만 등록할것)<br>
                        - 실측 전체중량시 개체별 차이가 있으니 오차범위 있음 ( 10g 이내 )
                    </div>

                </td>
            </tr>
            <tr>
                <th>상품 코드</th>
                <td>
                    바코드 : <input type='text' name='cd_code' style='width:200px;' value="<?= $productData['CD_CODE'] ?? '' ?>">
                    상품 품번 : <input type='text' name='cd_code2' style='width:100px;' value="<?= $productData['CD_CODE2'] ?? '' ?>">
                </td>
            </tr>
        </tbody>



        <!-- 재고/주문 정보 -->
        <tbody>
            <tr>
                <td colspan="2" class="none-bg" style="height:10px;"></td>
            </tr>
            <tr>
                <td colspan="2" class="none-bg title">
                    <h1>재고/주문 정보</h1>
                </td>
            </tr>
        </tbody>
        <tbody>

            <tr>
                <th>주문서 메모</th>
                <td>
                    <input type='text' name='cd_memo3' value="<?= $productData['cd_memo3'] ?? '' ?>" />
                    <div class="admin-guide-text">
                        - 주문서 폼에 노출되는 메모입니다.
                    </div>
                </td>
            </tr>

            <?php if ($productData['ps_idx'] ?? '') { ?>
                <tr>
                    <th>재고</th>
                    <td>

                        <input type="hidden" name="ps_idx" value="<?= $productData['ps_idx'] ?? '' ?>">
                        <table class="">
                            <tr>
                                <th class="text-center" style="width:100px">재고코드</th>
                                <td>
                                    <b><?= $productData['ps_idx'] ?? '' ?></b>
                                </td>
                                <th class="text-center" style="width:100px">랙 코드</th>
                                <td>
                                    <input type='text' name='ps_rack_code' style='width:150px;' value="<?= $productData['ps_rack_code'] ?? '' ?>">
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
                <tr>
                    <th>재고관리</th>
                    <td>
                        <table class="">
                            <tr>
                                <th class="text-center" style="width:100px">재고관리</th>
                                <td>
                                    <label><input type="radio" name="ps_stock_object" value="Y" <?php if (($productData['ps_stock_object'] ?? '') == "Y") echo "checked"; ?>> 재고관리</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="ps_stock_object" value="N" <?php if (($productData['ps_stock_object'] ?? '') == "N") echo "checked"; ?>> 재고관리 안함</label>
                                </td>
                                <th class="text-center" style="width:100px">재고알림</th>
                                <td>
                                    <input type='text' name='ps_alarm_count' style='width:50px;' value="<?= $productData['ps_alarm_count'] ?? '' ?>"> 개
                                </td>
                            </tr>
                        </table>
                        <div class="admin-guide-text">
                            - 재고알림 예)3 재고가 3개 이하시 알람발생
                        </div>
                    </td>
                </tr>
            <?php } ?>

            <tr>
                <th>수입국가</th>
                <td>
                    <?php

                    $_arr_national = [
                        ["name" => "일본", "code" => "jp"],
                        ["name" => "중국", "code" => "cn"],
                        ["name" => "한국", "code" => "kr"],
                        ["name" => "달러", "code" => "dollar"]
                    ];

                    foreach ($_arr_national as $national) {
                    ?>
                        <label><input type="radio" name="cd_national" value="<?= $national['code'] ?? '' ?>" <?php if (($productData['cd_national'] ?? '') == ($national['code'] ?? '')) echo "checked"; ?>> <?= $national['name'] ?? '' ?>(<?= $national['code'] ?? '' ?>)</label>&nbsp;&nbsp;
                    <?php } ?>
                </td>
            </tr>

            <tr>
                <th>포장 사이즈</th>
                <td>
                    가로(W) : <input type='text' name='invoice_size_w' value="<?= $productData['cd_size_fn']['invoice']['W'] ?? '' ?>" style="width:60px">
                    세로(H) : <input type='text' name='invoice_size_h' value="<?= $productData['cd_size_fn']['invoice']['H'] ?? '' ?>" style="width:60px">
                    깊이(D) : <input type='text' name='invoice_size_d' value="<?= $productData['cd_size_fn']['invoice']['D'] ?? '' ?>" style="width:60px">
                    &nbsp;&nbsp;
                    CBM : <input type='text' name='invoice_size_cbm' value="<?= $productData['cd_size_fn']['invoice']['cbm'] ?? '' ?>" style="width:60px">
                    <input type="checkbox" name="invoice_size_cbm_mode" value="hand" <?php if (($productData['cd_size_fn']['invoice']['cbm_mode'] ?? '') == "hand") echo "checked"; ?>> CBM 수동입력
                    <div class="admin-guide-text">
                        - 단위 mm (숫자만 등록할것)
                    </div>
                </td>
            </tr>

            <tr>
                <th>인보이스 이름1 (일어)</th>
                <td><input type='text' name='cd_inv_name1' value="<?= $productData['CD_INV_NAME1'] ?? '' ?>"></td>
            </tr>
            <tr>
                <th>인보이스 이름2 (영어)</th>
                <td><input type='text' name='cd_inv_name2' value="<?= $productData['CD_INV_NAME2'] ?? '' ?>"></td>
            </tr>
            <tr>
                <th>인보이스 소재</th>
                <td><input type='text' name='cd_inv_material' value="<?= $productData['CD_INV_MATERIAL'] ?? '' ?>" style='width:250px;'></td>
            </tr>
            <tr>
                <th>원산지</th>
                <td><input type='text' name='cd_coo' value="<?= $productData['CD_COO'] ?? '' ?>" style='width:250px;'></td>
            </tr>
            <tr>
                <th>플라스틱 함유량</th>
                <td>
                    함유량 퍼센트(%) : <input type='text' name='import_plastic' value="<?= $productData['cd_size_fn']['import']['plastic'] ?? '' ?>" style='width:100px; margin-right:30px !important;'>
                    신재원료사용량(g) : <input type='text' name='import_plastic_amount' value="<?= $productData['cd_size_fn']['import']['plastic_amount'] ?? '' ?>" style='width:100px;'>
                    <div class="admin-guide-text">
                        - 퍼센트 입력시 %기호 넣지 말고 숫자만 넣어주세요.<br>
                        - 퍼센트 입력시 자동계산은 실측중량값이 존재할때만 자동계산됩니다.<br>
                        - 젤일때는 퍼센트 넣지 말고 신재원료사용량(g)만 넣어주세요.<br>
                        - 신재원료사용량(g)은 신고되는 최종 개당 플라스틱 함유량입니다.
                    </div>
                </td>
            </tr>
            <tr>
                <th>HS CODE</th>
                <td>
                    <input type='text' name='import_hscode' value="<?= $productData['cd_size_fn']['import']['hscode'] ?? '' ?>" style='width:250px;'><br>
                    <input type='text' name='import_hscode1' value="<?= $productData['cd_size_fn']['import']['hscode1'] ?? '' ?>" class="m-t-5" style='width:250px;'><br>
                    <input type='text' name='import_hscode2' value="<?= $productData['cd_size_fn']['import']['hscode2'] ?? '' ?>" class="m-t-5" style='width:250px;'><br>
                </td>
            </tr>

        </tbody>


    </table>

</form>

<?php if (!empty($productData['CD_IDX'])) { ?>
    <div class="button-wrap-back">
    </div>
    <div class="button-wrap">
        <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdDetailBasicForm.save()">상품수정</button>
    </div>
<?php } ?>

<div id="prd_image_preview_modal" class="prd-image-preview-modal">
    <div class="prd-image-preview-content">
        <button type="button" id="prd_image_preview_close" class="btnstyle1 btnstyle1-danger btnstyle1-md prd-image-preview-close">이미지 닫기</button>
        <img id="prd_image_preview_target" src="" alt="원본 이미지 미리보기">
    </div>
</div>

<script>
    var prdDetailBasicForm = function() {

        /**
         * 상품 세일 설정
         * 
         * @param int $prd_idx 상품 인덱스
         * @param int $ps_idx 재고 인덱스
         * @param string $mode 모드 (monthly, special)
         */
        function setProductSale(prd_idx, ps_idx, mode) {

            var payload = {
                action_mode: 'set_product_sale',
                prd_idx: prd_idx,
                ps_idx: ps_idx,
                mode: mode
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

        }

        /**
         * 상품 세일 해제
         * 
         * @param int $prd_idx 상품 인덱스
         * @param int $ps_idx 재고 인덱스
         * @param string $mode 모드 (monthly, special)
         */
        function unsetProductSale(prd_idx, ps_idx, mode) {

            var payload = {
                action_mode: 'unset_product_sale',
                prd_idx: prd_idx,
                ps_idx: ps_idx,
                mode: mode
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

        }

        /**
         * 상품 단종 설정
         */
        function setProductDiscontinued(prd_idx) {

            var payload = {
                action_mode: 'set_product_discontinued',
                prd_idx: prd_idx
            };

            ajaxRequest('/admin/product/action', payload)
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
        }

        /**
         * 상품 단종 해제
         */
        function unsetProductDiscontinued(prd_idx) {

            var payload = {
                action_mode: 'unset_product_discontinued',
                prd_idx: prd_idx
            };

            ajaxRequest('/admin/product/action', payload)
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
        }

        /**
         * 상품 베이직 저장
         */
        function save() {

            const form = document.getElementById('prd_form');
            const weightFields = [
                { name: 'cd_weight_1', label: '상품중량' },
                { name: 'cd_weight_2', label: '전체중량' },
                { name: 'cd_weight_4', label: '실측 상품중량' },
                { name: 'cd_weight_3', label: '실측중량' },
            ];

            for (const field of weightFields) {
                const input = form.querySelector('input[name="' + field.name + '"]');
                if (!input) {
                    continue;
                }
                const value = String(input.value || '').trim();
                if (value === '') {
                    continue;
                }
                if (!/^\d+$/.test(value)) {
                    alert(field.label + '은(는) 숫자만 입력할 수 있습니다.');
                    input.focus();
                    return;
                }
            }

            const importPlasticInput = form.querySelector('input[name="import_plastic"]');
            if (importPlasticInput) {
                const importPlasticValue = String(importPlasticInput.value || '').trim();
                if (importPlasticValue !== '' && !/^(?:\d+|\d+\.\d+|\.\d+)$/.test(importPlasticValue)) {
                    alert('플라스틱 함유량 퍼센트는 숫자만 입력할 수 있습니다.');
                    importPlasticInput.focus();
                    return;
                }
            }

            const formData = new FormData(form);
            fetch('/admin/product/saveProduct', {
                    method: 'POST',
                    body: formData,
                })
                .then(async (response) => {
                    const data = await response.json();
                    if (!response.ok || data.success !== true) {
                        throw new Error(data.message || '저장 실패');
                    }

                    alert(data.message || '저장 완료');
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || '저장 실패');
                });
        }

        return {
            save,
            setProductSale,
            unsetProductSale,
            setProductDiscontinued,
            unsetProductDiscontinued,
        }

    }();

    $(function() {
        const categoryCodeByKind = <?= json_encode($categoryCodeByKind ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const categoryChildrenByKind = <?= json_encode($categoryChildrenByKind ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const initialSecondKindKey = <?= json_encode($selectedSecondKindKey ?? '', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        let hasAppliedInitialSecondCategory = false;

        function resolveCategoryCodeByKind(kindKey) {
            const key = String(kindKey || '').trim();
            if (!key || typeof categoryCodeByKind !== 'object' || categoryCodeByKind === null) {
                return '';
            }
            return String(categoryCodeByKind[key] || '').trim();
        }

        function updateCategoryCodeInput() {
            const primaryKind = String($('select[name="cd_kind_code"]').val() || '').trim();
            const $secondSelect = $('#cd_kind_code_second');
            const secondKind = String($secondSelect.val() || '').trim();
            let categoryCode = '';

            if (secondKind !== '') {
                categoryCode = resolveCategoryCodeByKind(secondKind);
            }

            if (categoryCode === '' && primaryKind !== '') {
                categoryCode = resolveCategoryCodeByKind(primaryKind);
            }

            $('#cd_category_code').val(categoryCode);
        }

        function renderSecondCategorySelect(resetSelection) {
            const primaryKind = String($('select[name="cd_kind_code"]').val() || '').trim();
            const childCategories = Array.isArray(categoryChildrenByKind[primaryKind]) ? categoryChildrenByKind[primaryKind] : [];
            const $secondWrap = $('#cd_kind_code_second_wrap');
            const $secondSelect = $('#cd_kind_code_second');

            $secondSelect.empty();
            $secondSelect.append('<option value="">2차 카테고리 선택</option>');

            if (childCategories.length === 0) {
                $secondWrap.hide();
                updateCategoryCodeInput();
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

            if (!resetSelection && !hasAppliedInitialSecondCategory && initialSecondKindKey) {
                $secondSelect.val(initialSecondKindKey);
                hasAppliedInitialSecondCategory = true;
            } else {
                $secondSelect.val('');
            }

            $secondWrap.show();
            updateCategoryCodeInput();
        }

        $('select[name="cd_kind_code"]').on('change', function() {
            renderSecondCategorySelect(true);
        });
        $('#cd_kind_code_second').on('change', function() {
            updateCategoryCodeInput();
        });
        renderSecondCategorySelect(false);

        $(document).on('change', 'input[type="checkbox"][name^="work_task_done["]', function() {
            var $chip = $(this).closest('.work-check-chip');
            var isDone = $(this).is(':checked');
            $chip
                .toggleClass('is-done', isDone)
                .toggleClass('is-todo', !isDone)
                .css({
                    borderColor: isDone ? '#22c55e' : '#d1d5db',
                    background: isDone ? '#f0fdf4' : '#f9fafb'
                });
            $chip.find('.work-check-state')
                .text(isDone ? '완료' : '미완료')
                .css('color', isDone ? '#15803d' : '#6b7280');
        });

        function buildReferenceLinkRow(title, url) {
            var safeTitle = String(title || '').replace(/"/g, '&quot;');
            var safeUrl = String(url || '').replace(/"/g, '&quot;');
            return '' +
                '<tr class="reference-link-row">' +
                    '<td><input type="text" name="reference_link_title[]" value="' + safeTitle + '" placeholder="예: 공급사 상세페이지" style="width:100%;"></td>' +
                    '<td><input type="text" name="reference_link_url[]" value="' + safeUrl + '" placeholder="https://example.com" style="width:100%;"></td>' +
                    '<td class="text-center"><button type="button" class="btnstyle1 btnstyle1-xs reference-link-open-btn">바로가기</button></td>' +
                    '<td class="text-center"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs remove-reference-link-btn">삭제</button></td>' +
                '</tr>';
        }

        function normalizeReferenceUrl(urlText) {
            var url = String(urlText || '').trim();
            if (!url) {
                return '';
            }
            if (!/^https?:\/\//i.test(url)) {
                url = 'https://' + url;
            }
            return url;
        }

        function extractDomainLabel(urlText) {
            var normalized = normalizeReferenceUrl(urlText);
            if (!normalized) {
                return '';
            }
            try {
                var parsed = new URL(normalized);
                return parsed.hostname || normalized;
            } catch (e) {
                return normalized;
            }
        }

        $(".dn-select2").select2();

        if ($(".calendar-input input").length) {
            $(".calendar-input input").datepicker(clareCalendar);
        }

        const $hbtiTargetCheckbox = $('input[name="hbti_target"][value="N"]');
        const $hbtiConfigRow = $('#hbti-config-row');

        function toggleHbtiConfigRow() {
            if ($hbtiTargetCheckbox.is(':checked')) {
                $hbtiConfigRow.hide();
            } else {
                $hbtiConfigRow.show();
            }
        }

        const $weight3 = $('input[name="cd_weight_3"]');
        const $weight4 = $('input[name="cd_weight_4"]');
        const $importPlastic = $('input[name="import_plastic"]');
        const $importPlasticAmount = $('input[name="import_plastic_amount"]');

        function sanitizeDecimalInput(value) {
            let sanitized = String(value || '').replace(/[^0-9.]/g, '');
            const dotIndex = sanitized.indexOf('.');
            if (dotIndex !== -1) {
                sanitized = sanitized.slice(0, dotIndex + 1) + sanitized.slice(dotIndex + 1).replace(/\./g, '');
            }
            return sanitized;
        }

        function updateImportPlasticAmount() {
            const weight4Text = String($weight4.val() || '').trim();
            const weight3Text = String($weight3.val() || '').trim();
            const percentText = String($importPlastic.val() || '').trim();
            const weight4Value = parseFloat(weight4Text);
            const weight3Value = parseFloat(weight3Text);
            const weightValue = Number.isFinite(weight4Value) ? weight4Value : weight3Value;
            const percentValue = parseFloat(percentText);

            // 실측중량/퍼센트가 모두 숫자일 때만 자동 계산
            if (!Number.isFinite(weightValue) || !Number.isFinite(percentValue)) {
                return;
            }

            const calculatedAmount = (weightValue * percentValue) / 100;
            $importPlasticAmount.val(calculatedAmount.toFixed(2));
        }

        $importPlastic.on('input', function() {
            const sanitized = sanitizeDecimalInput($(this).val());
            if ($(this).val() !== sanitized) {
                $(this).val(sanitized);
            }
            updateImportPlasticAmount();
        });

        $weight3.on('input', function() {
            updateImportPlasticAmount();
        });

        $weight4.on('input', function() {
            updateImportPlasticAmount();
        });

        $hbtiTargetCheckbox.on('change', toggleHbtiConfigRow);
        toggleHbtiConfigRow();

        $('#add_reference_link_btn').on('click', function() {
            $('#reference_links_tbody').append(buildReferenceLinkRow('', ''));
        });

        $(document).on('click', '.remove-reference-link-btn', function() {
            $(this).closest('tr').remove();
        });

        $(document).on('click', '.reference-link-open-btn', function() {
            var $row = $(this).closest('tr');
            var $urlInput = $row.find('input[name="reference_link_url[]"]');
            var normalizedUrl = normalizeReferenceUrl($urlInput.val());
            if (!normalizedUrl) {
                alert('URL을 입력해주세요.');
                $urlInput.focus();
                return;
            }
            $urlInput.val(normalizedUrl);
            window.open(normalizedUrl, '_blank');
        });

        // 링크명이 비어있고 URL 입력 시, 링크명에 도메인 자동 채움
        $(document).on('blur', 'input[name="reference_link_url[]"]', function() {
            var $row = $(this).closest('tr');
            var $titleInput = $row.find('input[name="reference_link_title[]"]');
            if (String($titleInput.val() || '').trim() !== '') {
                return;
            }
            var domainLabel = extractDomainLabel($(this).val());
            if (domainLabel) {
                $titleInput.val(domainLabel);
            }
        });

        const $imagePreviewModal = $('#prd_image_preview_modal');
        const $imagePreviewTarget = $('#prd_image_preview_target');

        $(document).on('click', '.prd-image-preview-trigger', function() {
            const src = String($(this).data('preview-src') || $(this).attr('src') || '').trim();
            if (!src) {
                return;
            }
            $imagePreviewTarget.attr('src', src);
            $imagePreviewModal.addClass('is-open');
        });

        const closeImagePreviewModal = function() {
            $imagePreviewModal.removeClass('is-open');
            $imagePreviewTarget.attr('src', '');
        };

        $('#prd_image_preview_close').on('click', function(e) {
            e.stopPropagation();
            closeImagePreviewModal();
        });

        $imagePreviewTarget.on('click', function(e) {
            e.stopPropagation();
        });

        $imagePreviewModal.on('click', function() {
            closeImagePreviewModal();
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $imagePreviewModal.hasClass('is-open')) {
                closeImagePreviewModal();
            }
        });

    });
</script>