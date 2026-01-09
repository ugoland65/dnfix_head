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
                            $koreanInitials = ['', 'ㄱ', 'ㄴ', 'ㄷ', 'ㄹ', 'ㅁ', 'ㅂ', 'ㅅ', 'ㅇ', 'ㅈ', 'ㅊ', 'ㅋ', 'ㅌ', 'ㅍ', 'ㅎ'];
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
                            $englishInitials = array_merge([''], range('A', 'Z'));
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
        document.getElementById('brand_form').submit();
    }
</script>