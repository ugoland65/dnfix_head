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
                    <select name="cd_kind_code">
                        <option value=''>상품 구분 선택</option>
                        <?php foreach ($prd_kind_name as $key => $kind) { ?>
                            <option value="<?= $key ?>" <?php if ($productData['CD_KIND_CODE'] == $key) echo "selected"; ?>><?= $kind ?></option>
                        <?php } ?>
                    </select>
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
                                    $img_path = '/data/comparion/' . $productData['CD_IMG'];
                                ?>
                                    <div class="m-b-15">
                                        <img src="<?= $img_path ?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
                                    </div>
                                <?php } ?>

                                <input type='file' name='cd_img'><br>
                                <input type='text' name='out_img' value="" placeholder="URL로 저장"><br>

                                <?php if ($productData['CD_IMG'] ?? '') { ?>
                                    <div class="m-t-10"><?= $productData['CD_IMG'] ?></div>
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
                                        <img src="<?= $img_add1 ?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
                                    </div>
                                <?php } ?>

                                <input type='file' name='cd_add1'>

                                <?php if ($productData['cd_add_img']['add1']['filename'] ?? '') { ?>
                                    <div class="m-t-10"><?= $productData['cd_add_img']['add1']['filename'] ?></div>
                                <?php } ?>
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
                    </td>
                </tr>
            <?php } ?>

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
                    상품중량 : <input type='text' name='cd_weight_1' style='width:80px;' value="<?= $productData['cd_weight_fn']['1'] ?? '' ?>">
                    전체중량 : <input type='text' name='cd_weight_2' style='width:80px;' value="<?= $productData['cd_weight_fn']['2'] ?? '' ?>">
                    실측중량 : <input type='text' name='cd_weight_3' style='width:80px;' value="<?= $productData['cd_weight_fn']['3'] ?? '' ?>">
                    <div class="admin-guide-text">
                        - 단위 g (숫자만 등록할것)
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
         * 상품 베이직 저장
         */
        function save() {

            const form = document.getElementById('prd_form');
            const weightFields = [
                { name: 'cd_weight_1', label: '상품중량' },
                { name: 'cd_weight_2', label: '전체중량' },
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
        }

    }();

    $(function() {

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
            const weightText = String($weight3.val() || '').trim();
            const percentText = String($importPlastic.val() || '').trim();
            const weightValue = parseFloat(weightText);
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

        $hbtiTargetCheckbox.on('change', toggleHbtiConfigRow);
        toggleHbtiConfigRow();

    });
</script>