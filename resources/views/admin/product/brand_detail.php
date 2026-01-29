<?php

$brandEval = $brandInfo['brand_eval_json'] ?? [];
if (is_string($brandEval)) {
    $decoded = json_decode($brandEval, true);
    $brandEval = is_array($decoded) ? $decoded : [];
} elseif (!is_array($brandEval)) {
    $brandEval = [];
}

$ev = function ($key, $default = null) use ($brandEval) {
    return $brandEval[$key] ?? $default;
};

$isChecked = function ($key, $value) use ($ev) {
    return ((string)$ev($key, '') === (string)$value) ? 'checked' : '';
};

$isCheckedY = function ($key) use ($ev) {
    return ($ev($key, 'n') === 'y') ? 'checked' : '';
};

$hasFlag = function ($value) use ($ev) {
    $flags = $ev('hard_flags', []);
    if (!is_array($flags)) $flags = [];
    return in_array($value, $flags, true) ? 'checked' : '';
};

?>

<STYLE TYPE="text/css">
    .table-wrap {
        width: 800px;
        padding: 20px;
        margin: 0 auto;
    }

    .table-style {
        width: 100%;
    }

    .filsu {
        color: #ff0000;
    }
</style>

<div class="table-wrap">
    <form name='brand_form' id='brand_form' action='/admin/brand/save' method='post' enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="idx" value="<?= $brandInfo['BD_IDX'] ?>">
        <input type="hidden" name="modify_bd_logo" value="<?= $brandInfo['BD_LOGO'] ?? '' ?>">

        <table class="table-style">
            <tr>
                <th class="tds1">고유번호</th>
                <td class="tds2"><?= $brandInfo['BD_IDX'] ?></td>
            </tr>
            <tr>
                <th class="tds1">등록일</th>
                <td class="tds2"><?= $brandInfo['created_at'] ?? '' ?></td>
            </tr>
            <tr>
                <th class="tds1">수정일</th>
                <td class="tds2"><?= $brandInfo['updated_at'] ?? '' ?></td>
            </tr>
            <tr>
                <th class="tds1">이름(국문)<br><b class="filsu">*필수</b></th>
                <td class="tds2"><input type='text' name='bd_name' id='alliance_shop_name' size='40' value="<?= $brandInfo['BD_NAME'] ?? '' ?>"></td>
            </tr>
            <tr>
                <th class="tds1">이름(영문)<br><b class="filsu">*필수</b></th>
                <td class="tds2"><input type='text' name='bd_name_en' id='alliance_shop_name' size='40' value="<?= $brandInfo['BD_NAME_EN'] ?? '' ?>"></td>
            </tr>
            <tr>
                <th class="tds1">메모</th>
                <td class="tds2"><textarea name="bd_memo" rows="3" style="width:100%; height:80px;"><?= $brandInfo['bd_memo'] ?? '' ?></textarea></td>
            </tr>
            <tr>
                <th class="tds1">이름 그룹<br><b class="filsu">*필수</b></th>
                <td class="tds2">
                    <div>
                        한글초성 :
                        <select name="bd_name_group" id="bd_name_group" style="width:60px;">
                            <?php
                            // 초성: 단일 자음만 노출 (쌍자음 제외)
                            $koreanInitials = ['', 'ㄱ', 'ㄴ', 'ㄷ', 'ㄹ', 'ㅁ', 'ㅂ', 'ㅅ', 'ㅇ', 'ㅈ', 'ㅊ', 'ㅋ', 'ㅌ', 'ㅍ', 'ㅎ','#'];
                            foreach ($koreanInitials as $initial) {
                                $selected = ($brandInfo['BD_NAME_GROUP'] ?? '') === $initial ? 'selected' : '';
                                echo "<option value=\"{$initial}\" {$selected}>{$initial}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="m-t-5">
                        알파벳 초성 :
                        <select name="bd_name_en_group" id="bd_name_en_group" style="width:70px;">
                            <?php
                            $englishInitials = array_merge([''], range('A', 'Z'), ['@']);
                            foreach ($englishInitials as $initial) {
                                $selected = ($brandInfo['BD_NAME_EN_GROUP'] ?? '') === $initial ? 'selected' : '';
                                echo "<option value=\"{$initial}\" {$selected}>{$initial}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="tds1">활성</th>
                <td class="tds2">
                    <input type="radio" name='bd_active' value="N" <?php if ($brandInfo['BD_ACTIVE'] == "N") echo "checked"; ?>> 비활성
                    <input type="radio" name='bd_active' value="Y" <?php if ($brandInfo['BD_ACTIVE'] == "Y" or $brandInfo['BD_ACTIVE'] == "") echo "checked"; ?>> 활성
                </td>
            </tr>
            <tr>
                <th class="tds1">리스트 노출</th>
                <td class="tds2">
                    <input type="radio" name='bd_list_active' value="N" <?php if ($brandInfo['BD_LIST_ACTIVE'] == "N") echo "checked"; ?>> 비활성
                    <input type="radio" name='bd_list_active' value="Y" <?php if ($brandInfo['BD_LIST_ACTIVE'] == "Y" or $brandInfo['BD_LIST_ACTIVE'] == "") echo "checked"; ?>> 활성
                    <div class="admin-guide">
                        - 비활성 시 검색 리스트에 노출 안됨
                    </div>
                </td>
            </tr>
            <tr>
                <th class="tds1">로고</th>
                <td class="tds2">
                    <input type="file" id="bd_logo" name="bd_logo">
                    <?php if ($brandInfo['BD_LOGO']) { ?>
                        <div>
                            <img src="/data/brand_logo/<?= $brandInfo['BD_LOGO'] ?>" alt="">
                        </div>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th class="tds1">홈페이지</th>
                <td class="tds2"><input type='text' name='bd_domain' value="<?= $brandInfo['BD_DOMAIN'] ?? '' ?>" style="width:300px"></td>
            </tr>
            <tr>
                <th class="tds1">간략소개</th>
                <td class="tds2"><input type='text' name='bd_introduce' value="<?= $brandInfo['BD_INTRODUCE'] ?? '' ?>" class="w-100"></td>
            </tr>
            <tr>
                <th class="tds1">브랜드 코드</th>
                <td class="tds2">
                    <input type='text' name='bd_code' value="<?= $brandInfo['BD_CODE'] ?? '' ?>">
                    <div class="admin-guide">
                        - 현시점에서 중요하지 않음
                    </div>
                </td>
            </tr>
            <tr>
                <th class="tds1">구분코드</th>
                <td class="tds2">
                    <input type='text' name='bd_kind_code' value="<?= $brandInfo['BD_KIND_CODE'] ?? '' ?>">
                    <div class="admin-guide">
                        - 현시점에서 중요하지 않음
                    </div>
                </td>
            </tr>
            <tr>
                <th class="tds1">
                    브랜드 카테고리<br>
                    <b class="filsu">*필수</b>
                </th>
                <td class="tds2">
                    <label><input type="checkbox" name="bd_kind_ona" value="Y" <?php if ($brandInfo['bd_kind']['ona'] == "Y") echo "checked"; ?>>오나홀</label>
                    <label><input type="checkbox" name="bd_kind_breast" value="Y" <?php if ($brandInfo['bd_kind']['breast'] == "Y") echo "checked"; ?>>가슴형</label>
                    <label><input type="checkbox" name="bd_kind_gel" value="Y" <?php if ($brandInfo['bd_kind']['gel'] == "Y") echo "checked"; ?>>윤활제</label>
                    <label><input type="checkbox" name="bd_kind_condom" value="Y" <?php if ($brandInfo['bd_kind']['condom'] == "Y") echo "checked"; ?>>콘돔</label>
                    <label><input type="checkbox" name="bd_kind_annal" value="Y" <?php if ($brandInfo['bd_kind']['annal'] == "Y") echo "checked"; ?>>애널용품</label>
                    <label><input type="checkbox" name="bd_kind_prostate" value="Y" <?php if ($brandInfo['bd_kind']['prostate'] == "Y") echo "checked"; ?>>전립선자극</label>
                    <label><input type="checkbox" name="bd_kind_care" value="Y" <?php if ($brandInfo['bd_kind']['care'] == "Y") echo "checked"; ?>>관리/보조</label>
                    <label><input type="checkbox" name="bd_kind_dildo" value="Y" <?php if ($brandInfo['bd_kind']['dildo'] == "Y") echo "checked"; ?>>딜도</label>
                    <label><input type="checkbox" name="bd_kind_vibe" value="Y" <?php if ($brandInfo['bd_kind']['vibe'] == "Y") echo "checked"; ?>>바이브</label>
                    <label><input type="checkbox" name="bd_kind_suction" value="Y" <?php if ($brandInfo['bd_kind']['suction'] == "Y") echo "checked"; ?>>흡입토이</label>
                    <label><input type="checkbox" name="bd_kind_man" value="Y" <?php if ($brandInfo['bd_kind']['man'] == "Y") echo "checked"; ?>>남성보조</label>
                    <label><input type="checkbox" name="bd_kind_nipple" value="Y" <?php if ($brandInfo['bd_kind']['nipple'] == "Y") echo "checked"; ?>>니플/유두</label>
                    <label><input type="checkbox" name="bd_kind_cos" value="Y" <?php if ($brandInfo['bd_kind']['cos'] == "Y") echo "checked"; ?>>코스튬/속옷</label>
                    <label><input type="checkbox" name="bd_kind_perfume" value="Y" <?php if ($brandInfo['bd_kind']['perfume'] == "Y") echo "checked"; ?>>향수/목욕</label>
                    <label><input type="checkbox" name="bd_kind_bdsm" value="Y" <?php if ($brandInfo['bd_kind']['bdsm'] == "Y") echo "checked"; ?>>BDSM</label>
                </td>
            </tr>
            <tr>
                <th class="tds1">쑈당몰 디스플레이</th>
                <td class="tds2">

                    <table class="table-style">
                        <tr>
                            <th class="tds1">사용여부</th>
                            <td class="tds2">
                                <label><input type="radio" name="bd_api_active" value="Y" <? if ($brandInfo['bd_api_info']['active'] == "Y") echo "checked"; ?>>적용</label>
                                <label><input type="radio" name="bd_api_active" value="N" <? if (!$brandInfo['bd_api_info']['active'] || $brandInfo['bd_api_info']['active'] == "N") echo "checked"; ?>>비적용</label>
                            </td>
                        </tr>

                        <tr>
                            <th class="tds1">[카페24] 매칭 cate_no</th>
                            <td class="tds2">
                                <input type='text' name='bd_cate_no' value="<?= $brandInfo['bd_cate_no'] ?>" style="width:100px">
                                <div class="admin-guide">
                                    - 카페24 카테고리 넘버<br>
                                    - 이제 사용하지 않음
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th class="tds1">[고도몰] 매칭<br><b class="filsu">*필수</b></th>
                            <td class="tds2">

                                <table cellspacing="1" cellpadding="0" class="table-style">
                                    <tr>
                                        <th class="tds1">카테고리 코드</th>
                                        <td class="tds2">
                                            <input type='text' name='bd_matching_cate' value="<?= $brandInfo['bd_matching_cate'] ?>" style="width:100px">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="tds1">브랜드 코드</th>
                                        <td class="tds2">
                                            <input type='text' name='bd_matching_brand' value="<?= $brandInfo['bd_matching_brand'] ?>" style="width:100px">
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>


                        <tr>
                            <th class="tds1">노출 브랜드명 (국문)<br><b class="filsu">*필수</b></th>
                            <td class="tds2">
                                <input type='text' name='bd_api_name' value="<?= $brandInfo['bd_api_info']['name'] ?? '' ?>">
                            </td>
                        </tr>
                        <tr>
                            <th class="tds1">노출 브랜드명 (영문)<br><b class="filsu">*필수</b></th>
                            <td class="tds2">
                                <input type='text' name='bd_api_name_en' value="<?= $brandInfo['bd_api_info']['name_en'] ?? '' ?>">
                                <br><b class="filsu">' 따옴표 넣으면 에러남 ㅠㅠ 일단 넣지말길</b>
                            </td>
                        </tr>
                        <tr>
                            <th class="tds1">logo 파일위치<br><b class="filsu">*필수</b></th>
                            <td class="tds2">
                                <?php
                                if ($brandInfo['bd_api_info']['logo'] ?? '') {
                                ?>
                                    <div><img src="https://showdang.co.kr/data/<?= $brandInfo['bd_api_info']['logo'] ?>" style="width:150px"></div>
                                <?php } ?>
                                <input type='text' name='bd_api_logo' value="<?= $brandInfo['bd_api_info']['logo'] ?? '' ?>">
                                <div class="admin-guide">
                                    - 사이즈 300 x 300<br>
                                    - 디렉토리 : /dg_image/brand_image/<br>
                                    - ex) 파일명이 brand.jpg 일경우 => /dg_image/brand_image/brand.jpg<br>
                                    - 업로드 디렉토리 : /data/dg_image/brand_image/<br>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="tds1">배경 이미지 PC</th>
                            <td class="tds2">
                                <?php
                                if ($brandInfo['bd_api_info']['bg'] ?? '') {
                                ?>
                                    <div style="background-color:<?= $brandInfo['bd_api_info']['bg_rgb'] ?? '' ?>"><img src="https://showdang.co.kr/data/<?= $brandInfo['bd_api_info']['bg'] ?? '' ?>" style="width:500px"></div>
                                <?php } ?>
                                <input type='text' name='bd_api_bg' value="<?= $brandInfo['bd_api_info']['bg'] ?? '' ?>">
                                <div class="admin-guide">
                                    - 사이즈 1310 x 260<br>
                                    - 디렉토리 : /dg_image/brand_image/<br>
                                    - ex) 파일명이 brand_bg.jpg 일경우 => /dg_image/brand_image/brand_bg.jpg<br>
                                    - 업로드 디렉토리 : /data/dg_image/brand_image/<br>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="tds1">info_class PC</th>
                            <td class="tds2">
                                <input type='text' name='bd_api_info_class' value="<?= $brandInfo['bd_api_info']['info_class'] ?? '' ?>" style="width:100px">
                                <div class="admin-guide">
                                    - 별도로 클래스 줘서 디자인 다르게 해야할 경우 ( 건들지 말것 )
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="tds1">배경 컬러 PC</th>
                            <td class="tds2">
                                <input type='text' name='bd_api_bg_rgb' value="<?= $brandInfo['bd_api_info']['bg_rgb'] ?? '' ?>" style="width:100px">
                                <div class="admin-guide">
                                    - #포함한 RGB코드
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="tds1">모바일 top 로고</th>
                            <td class="tds2">

                                <?
                                if ($brandInfo['bd_api_info']['logo_mobile'] ?? '') {
                                ?>
                                <div style="background-color:#111; padding:10px;"><img src="https://showdang.kr/<?= $brandInfo['bd_api_info']['logo_mobile'] ?? '' ?>"></div>
                                <? } ?>

                                <input type='text' name='bd_api_logo_mobile' value="<?= $brandInfo['bd_api_info']['logo_mobile'] ?? '' ?>">
                                <div class="admin-guide">
                                    - 사이즈 세로 사이즈 50px / 흰색, 투명<br>
                                    - 디렉토리 : /dg_image/brand_image/<br>
                                    - ex) 파일명이 brand_mobile_logo.png 일경우 => /dg_image/brand_image/brand_mobile_logo.png<br>
                                    - 업로드 디렉토리 : /data/dg_image/brand_image/<br>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="tds1">배경 이미지 모바일</th>
                            <td class="tds2">

                                <?
                                if ($brandInfo['bd_api_info']['bg_mobile'] ?? '') {
                                ?>
                                <div style="background-color:<?= $brandInfo['bd_api_info']['bg_rgb'] ?? '' ?>"><img src="https://showdang.kr/<?= $brandInfo['bd_api_info']['bg_mobile'] ?? '' ?>" style="width:500px"></div>
                                <? } ?>

                                <input type='text' name='bd_api_bg_mobile' value="<?= $brandInfo['bd_api_info']['bg_mobile'] ?? '' ?>">
                                <div class="admin-guide">
                                    - 사이즈 800 x 490<br>
                                    - 디렉토리 : /dg_image/brand_image/<br>
                                    - ex) 파일명이 brand_bg_mobile.jpg 일경우 => /dg_image/brand_image/brand_bg_mobile.jpg<br>
                                    - 업로드 디렉토리 : /data/dg_image/brand_image/<br>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="tds1">브랜드 소개<br><b class="filsu">*필수</b></th>
                            <td class="tds2">
                                <textarea name="bd_api_introduce" rows="" cols=""><?= $brandInfo['bd_api_introduce'] ?></textarea>
                                <div class="admin-guide">
                                    - 줄바꿈시 &#60;br&#62; 사용
                                </div>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
            <tr>
                <th class="tds1">오나DB 디스플레이</th>
                <td class="tds2">

                    <table class="table-style">
                        <tr>
                            <th class="tds1">사용여부</th>
                            <td class="tds2">
                                <label><input type="radio" name="bd_onadb_active" value="Y" <? if ($brandInfo['bd_onadb_active'] == "Y") echo "checked"; ?>>적용</label>
                                <label><input type="radio" name="bd_onadb_active" value="N" <? if (!$brandInfo['bd_onadb_active'] || $brandInfo['bd_onadb_active'] == "N") echo "checked"; ?>>비적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th class="tds1">브랜드 노출 순서</th>
                            <td class="tds2">
                                <input type='text' name='bd_onadb_sort_num' value="<?= $brandInfo['bd_onadb_sort_num'] ?>" style="width:100px">
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>

            <tr>
    <th class="tds1">브랜드 내부평가표</th>
    <td class="tds2">

        <!-- (추가) 가격정책/공급라인 입력 -->
        <table class="table-style" style="margin-bottom:12px;">
            <tr>
                <th class="tds1">가격 정책(가격 억제력)</th>
                <td class="tds2">
                    <label><input type="radio" name="brand_eval[price_policy_level]" value="3" <?= $isChecked('price_policy_level', 3) ?>> (강함) 권장소비자가/최저가 정책 있고 덤핑 제재/유통 통제</label><br>
                    <label><input type="radio" name="brand_eval[price_policy_level]" value="2" <?= $isChecked('price_policy_level', 2) ?>> (중간) 정책은 있으나 관리가 약함(가끔 방치)</label><br>
                    <label><input type="radio" name="brand_eval[price_policy_level]" value="1" <?= $isChecked('price_policy_level', 1) ?>> (약함) 사실상 방임(가격이 제각각)</label><br>
                    <label><input type="radio" name="brand_eval[price_policy_level]" value="0" <?= $isChecked('price_policy_level', 0) ?>> (없음) 정책 없음/덤핑 상시</label>
                    <div style="margin-top:8px;">
                        <label style="margin-right:12px;"><input type="checkbox" name="brand_eval[msrp_exists]" value="y" <?= $isCheckedY('msrp_exists') ?>> 권장소비자가(MSRP) 있음</label>
                        <label><input type="checkbox" name="brand_eval[map_exists]" value="y" <?= $isCheckedY('map_exists') ?>> 최저가/최저광고가(MAP) 정책 있음</label>
                    </div>
                </td>
            </tr>

            <tr>
                <th class="tds1">공급 라인(제조사/총판)</th>
                <td class="tds2">
                    <label><input type="radio" name="brand_eval[supply_type]" value="manufacturer" <?= $isChecked('supply_type', 'manufacturer') ?>> 제조사 직거래</label>
                    <label style="margin-left:12px;"><input type="radio" name="brand_eval[supply_type]" value="distributor" <?= $isChecked('supply_type', 'distributor') ?>> 총판/도매</label>
                    <label style="margin-left:12px;"><input type="radio" name="brand_eval[supply_type]" value="mixed" <?= $isChecked('supply_type', 'mixed') ?>> 혼합</label>

                    <div style="margin-top:10px;">
                        <label style="display:inline-block; margin-right:14px;">
                            MPQ(제조사 연결 최소 수량)
                            <input type="number" name="brand_eval[mpq_threshold]" value="<?= htmlspecialchars((string)$ev('mpq_threshold',''), ENT_QUOTES, 'UTF-8') ?>" min="0" style="width:120px; margin-left:6px;">
                        </label>

                        <label style="display:inline-block; margin-right:14px;">
                            현재 평균 주문 수량(월/분기)
                            <input type="number" name="brand_eval[mpq_current]" value="<?= htmlspecialchars((string)$ev('mpq_current',''), ENT_QUOTES, 'UTF-8') ?>" min="0" style="width:120px; margin-left:6px;">
                        </label>

                        <label style="display:inline-block;">
                            <input type="checkbox" name="brand_eval[manufacturer_contactable]" value="y" <?= $isCheckedY('manufacturer_contactable') ?>>
                            MPQ 충족 시 제조사 직연결 가능
                        </label>
                    </div>
                </td>
            </tr>
        </table>

        <!-- 내부평가(축 5개) -->
        <table class="table-style">
            <!-- A. 수익성 (35점) -->
            <tr>
                <th class="tds1">A. 수익성 (35점)</th>
                <td class="tds2">
                    <label><input type="radio" name="brand_eval[profit_score]" value="5" <?= $isChecked('profit_score', 5) ?>> (5점) 기여이익률 매우 좋고(예: 25%↑) 할인해도 남음</label><br>
                    <label><input type="radio" name="brand_eval[profit_score]" value="4" <?= $isChecked('profit_score', 4) ?>> (4점) 안정적으로 남음(예: 18~25%)</label><br>
                    <label><input type="radio" name="brand_eval[profit_score]" value="3" <?= $isChecked('profit_score', 3) ?>> (3점) 간당간당(예: 12~18%)</label><br>
                    <label><input type="radio" name="brand_eval[profit_score]" value="2" <?= $isChecked('profit_score', 2) ?>> (2점) 낮음(예: 8~12%)</label><br>
                    <label><input type="radio" name="brand_eval[profit_score]" value="1" <?= $isChecked('profit_score', 1) ?>> (1점) 거의 안 남음(예: 0~8%)</label><br>
                    <label><input type="radio" name="brand_eval[profit_score]" value="0" <?= $isChecked('profit_score', 0) ?>> (0점) 손해/정산 오류 등으로 운영 부적합</label>
                </td>
            </tr>

            <!-- B. 상품력·차별성 (브랜드 인지도·선호도 포함) (20점) -->
            <tr>
                <th class="tds1">B. 상품력·차별성 (브랜드 인지도·선호도 포함) (20점)</th>
                <td class="tds2">
                    <div style="margin-bottom:6px; color:#666;">상품 경쟁력 + 고객 인지도/선호도(브랜드 파워) 포함</div>
                    <label><input type="radio" name="brand_eval[product_score]" value="5" <?= $isChecked('product_score', 5) ?>> (5점) 대표 히트 SKU 보유 + 대체 어려움 + 리뷰/재구매 강함 + 브랜드 지명도/팬층 확실</label><br>
                    <label><input type="radio" name="brand_eval[product_score]" value="4" <?= $isChecked('product_score', 4) ?>> (4점) 만족도 높고 라인업 안정적, 경쟁 대비 강점 뚜렷 + 브랜드 선호도 높은 편</label><br>
                    <label><input type="radio" name="brand_eval[product_score]" value="3" <?= $isChecked('product_score', 3) ?>> (3점) 무난한 상품력(평균 수준), 대체 가능 + 인지도 보통</label><br>
                    <label><input type="radio" name="brand_eval[product_score]" value="2" <?= $isChecked('product_score', 2) ?>> (2점) 차별성 약함, 성과가 특정 SKU에만 편중 + 인지도 낮아 설득 비용 큼</label><br>
                    <label><input type="radio" name="brand_eval[product_score]" value="1" <?= $isChecked('product_score', 1) ?>> (1점) 만족도 낮음/후기 불만 잦음/불량 체감 큼 + 선호도 낮음</label><br>
                    <label><input type="radio" name="brand_eval[product_score]" value="0" <?= $isChecked('product_score', 0) ?>> (0점) 상품 경쟁력 매우 낮아 유지 사유 없음 + 브랜드 가치 없음/부정 인식</label>
                </td>
            </tr>

            <!-- C. 고객 리스크 (15점) -->
            <tr>
                <th class="tds1">C. 고객 리스크 (15점)</th>
                <td class="tds2">
                    <div style="margin-bottom:6px; color:#666;">점수 높을수록 리스크 낮음 (반품/CS/분쟁 적음)</div>
                    <label><input type="radio" name="brand_eval[risk_score]" value="5" <?= $isChecked('risk_score', 5) ?>> (5점) 반품/CS 매우 낮고 분쟁 거의 없음</label><br>
                    <label><input type="radio" name="brand_eval[risk_score]" value="4" <?= $isChecked('risk_score', 4) ?>> (4점) 낮은 편(문제 발생 시 처리도 빠름)</label><br>
                    <label><input type="radio" name="brand_eval[risk_score]" value="3" <?= $isChecked('risk_score', 3) ?>> (3점) 평균 수준</label><br>
                    <label><input type="radio" name="brand_eval[risk_score]" value="2" <?= $isChecked('risk_score', 2) ?>> (2점) 반품/클레임 잦음, CS 부담 체감</label><br>
                    <label><input type="radio" name="brand_eval[risk_score]" value="1" <?= $isChecked('risk_score', 1) ?>> (1점) 분쟁/민원 리스크 높음(환불 분쟁, 후기 이슈 등)</label><br>
                    <label><input type="radio" name="brand_eval[risk_score]" value="0" <?= $isChecked('risk_score', 0) ?>> (0점) 플랫폼 제재/법적 리스크 등으로 운영 부적합</label>
                </td>
            </tr>

            <!-- D. 운영·공급 안정성 (20점) - 재고 포함 -->
            <tr>
                <th class="tds1">D. 운영·공급 안정성 (20점)</th>
                <td class="tds2">
                    <div style="margin-bottom:6px; color:#666;">납기/품절/커뮤니케이션/옵션 난이도 + 재고 적정성(커버리지 주수) 포함</div>
                    <label><input type="radio" name="brand_eval[ops_score]" value="5" <?= $isChecked('ops_score', 5) ?>> (5점) 매우 안정(재고 커버리지 2~6주, 품절 거의 없음, 납기/응대 우수)</label><br>
                    <label><input type="radio" name="brand_eval[ops_score]" value="4" <?= $isChecked('ops_score', 4) ?>> (4점) 안정(커버리지 1~2주 또는 6~8주, 이슈 발생해도 빠르게 해결)</label><br>
                    <label><input type="radio" name="brand_eval[ops_score]" value="3" <?= $isChecked('ops_score', 3) ?>> (3점) 보통(커버리지 0.5~1주 또는 8~12주, 가끔 품절/납기 흔들림)</label><br>
                    <label><input type="radio" name="brand_eval[ops_score]" value="2" <?= $isChecked('ops_score', 2) ?>> (2점) 불안(커버리지 0~0.5주 또는 12~20주, 품절/리드타임 문제 반복)</label><br>
                    <label><input type="radio" name="brand_eval[ops_score]" value="1" <?= $isChecked('ops_score', 1) ?>> (1점) 문제 큼(장기 품절 반복 또는 20주↑ 과재고, 운영 부담 큼)</label><br>
                    <label><input type="radio" name="brand_eval[ops_score]" value="0" <?= $isChecked('ops_score', 0) ?>> (0점) 장기 품절/장기 과재고 + 개선 없음(공급/운영 부적합)</label>
                </td>
            </tr>

            <!-- E. 파트너십·성장성 (10점) - 가격정책 포함 -->
            <tr>
                <th class="tds1">E. 파트너십·성장성 (10점)</th>
                <td class="tds2">
                    <div style="margin-bottom:6px; color:#666;">가격정책/가격 억제력(권장소비가·MAP·덤핑관리) + 협조/성장성 포함</div>
                    <label><input type="radio" name="brand_eval[growth_score]" value="5" <?= $isChecked('growth_score', 5) ?>> (5점) 가격정책 강함(권장소비가/MAP + 덤핑 제재/유통 통제) + 공동마케팅/단독런칭 가능</label><br>
                    <label><input type="radio" name="brand_eval[growth_score]" value="4" <?= $isChecked('growth_score', 4) ?>> (4점) 가격정책 있음(대체로 가격 안정) + 협조 양호/신제품·콘텐츠 꾸준</label><br>
                    <label><input type="radio" name="brand_eval[growth_score]" value="3" <?= $isChecked('growth_score', 3) ?>> (3점) 보통(정책은 있으나 느슨/상황 따라 다름) + 협조 평균</label><br>
                    <label><input type="radio" name="brand_eval[growth_score]" value="2" <?= $isChecked('growth_score', 2) ?>> (2점) 약함(가격 흔들림/덤핑 방치) + 지원 제한적</label><br>
                    <label><input type="radio" name="brand_eval[growth_score]" value="1" <?= $isChecked('growth_score', 1) ?>> (1점) 방임(가격 통제 거의 없음/가격붕괴 위험) + 파트너십 낮음</label><br>
                    <label><input type="radio" name="brand_eval[growth_score]" value="0" <?= $isChecked('growth_score', 0) ?>> (0점) 가격/유통 리스크 심각(지속 운영 어려움)</label>
                </td>
            </tr>

            <!-- 하드룰(선택) -->
            <tr>
                <th class="tds1">하드룰(선택)</th>
                <td class="tds2">
                    <label><input type="checkbox" name="brand_eval[hard_flags][]" value="stockout" <?= $hasFlag('stockout') ?>> 장기 품절/품절 반복</label><br>
                    <label><input type="checkbox" name="brand_eval[hard_flags][]" value="claim" <?= $hasFlag('claim') ?>> 분쟁/민원/패널티 리스크</label><br>
                    <label><input type="checkbox" name="brand_eval[hard_flags][]" value="settlement" <?= $hasFlag('settlement') ?>> 정산/계약 리스크</label><br>
                    <label><input type="checkbox" name="brand_eval[hard_flags][]" value="quality" <?= $hasFlag('quality') ?>> 불량률 과다</label>
                </td>
            </tr>
        </table>

    </td>
</tr>




        </table>
    </form>

    <div class="text-center m-t-20">
        <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="goSubmit();">
            <i class="far fa-check-circle"></i>
            수정하기
        </button>
    </div>

</div>

<script>
    function goSubmit() {
        const form = document.getElementById('brand_form');
        fetch(form.action, {
            method: form.method || 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(form),
        })
        .then(async (res) => {
            const contentType = res.headers.get('content-type') || '';
            if (res.ok && contentType.includes('application/json')) {
                return res.json();
            }
            // JSON이 아니거나 리다이렉트된 경우도 성공으로 간주
            if (res.ok) {
                return { success: true };
            }
            throw new Error('요청이 실패했습니다. (' + res.status + ')');
        })
        .then((data) => {
            if (data && data.success) {
                alert('브랜드 수정 완료');
                window.location.reload();
            } else {
                alert((data && data.message) ? data.message : '수정에 실패했습니다.');
            }
        })
        .catch((err) => {
            alert(err.message || '수정 요청 중 오류가 발생했습니다.');
        });
    }
</script>