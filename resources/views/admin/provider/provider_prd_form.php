<style>
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
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

<form id="prd_provider_info_form">
    <input type="hidden" name="prd_idx" value="<?= $prd_data['idx'] ?>">
    <table class="table-style ">
        <colgroup>
            <col width="170px" />
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
                    <select name="kind">
                        <option value=''>상품 구분 선택</option>
                        <? foreach ($prd_kind_name as $kind) { ?>
                            <option value="<?= $kind ?>" <? if ($prd_data['kind'] == $kind) echo "selected"; ?>><?= $kind ?></option>
                        <? } ?>
                    </select>

                    <?php if ($prd_data['kind'] == '') { ?>
                        <p class="text-danger">
                            상품 구분이 지정되지 않았습니다. 상품 구분을 지정해주세요.
                        </p>
                    <?php } ?>

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
                <td><input type='text' name='name' size='40' value="<?= $prd_data['name'] ?>"></td>
            </tr>
            <tr>
                <th>원(영문,일어,중국어) 상품명</th>
                <td><input type='text' name='name_ori' size='40' value="<?= $prd_data['name_ori'] ?>"></td>
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
                    ?>
                        마진 : <?= number_format($margin) ?>원 / 마진율 : <?= number_format($margin_rate, 2) ?>%
                    <?php
                    }
                    ?>
                </td>
            </tr>

            <tr>
                <th>부가세</th>
                <td>
                    <?php if ($prd_data['price_data']['is_vat'] == 'Y') { ?>
                        포함
                    <?php }elseif ($prd_data['price_data']['is_vat'] == 'N') { ?>
                        미포함
                    <?php } ?>
                </td>
            </tr>

            <tr>
                <th>리스트 메모</th>
                <td><input type='text' name='memo' value="<?= $prd_data['memo'] ?? '' ?>"></td>
            </tr>

            <tr>
                <th>작업지시 메모</th>
                <td>
                    <textarea name='memo_work' rows='5'><?= $prd_data['memo_work'] ?? '' ?></textarea>
                </td>
            </tr>
        <tbody>

            <?php
            if ($prd_data['kind'] == "ONAHOLE") {
            ?>
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
    <?php
            }
    ?>


    <tbody>
        <tr>
            <td colspan="2" class="none-bg" style="height:10px;"></td>
        </tr>
        <tr>
            <td colspan="2" class="none-bg title">
                <div>
                    <ul>
                        <h1>고도몰</h1>
                    </ul>

                    <?php if (!empty($prd_data['godo_goodsNo'])) { ?>
                        <ul class="right">
                            <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm"
                                onclick="goGodoMall(<?= $prd_data['godo_goodsNo'] ?>);">고도몰 상품보기</button>

                            <button type="button" id="loadGodoGoodsInfoBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm"
                                data-prd-idx="<?= $prd_data['idx'] ?>"
                                data-godo-goods-no="<?= $prd_data['godo_goodsNo'] ?>">
                                고도몰 정보로 반영
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
                    <option value="품절" <? if ($prd_data['status'] == '품절') echo "selected"; ?>>품절</option>
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
                    $_margin_ex_per = [10, 15, 20, 25, 30];
                    $_cost_price_for_margin = (float)($prd_data['cost_price'] ?? 0);
                    $_order_price_for_margin = (float)($prd_data['order_price'] ?? 0);
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
                                    <?= $_margin_per ?>% 마진: <b><?= number_format($_example_sale_price) ?></b>원
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
                                    <?= $_margin_per ?>% 마진: <b><?= number_format($_example_sale_price_by_order) ?></b>원
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>


            </td>
        </tr>


        <?php
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
                        <h1>공급사</h1>
                    </ul>

                    <?php if (!empty($prd_data['supplier_prd_pk']) && ( $prd_data['partner_idx'] == 3 || $prd_data['partner_idx'] == 6 ) ) { ?>
                        <ul class="right">
                            업데이트후 새로고침 됩니다. 저장후 이용바랍니다.
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
                <label><input type="radio" name="is_vat" value="Y" <? if ($prd_data['price_data']['is_vat'] == 'Y') echo "checked"; ?>> 포함</label>
                <label><input type="radio" name="is_vat" value="N" <? if ($prd_data['price_data']['is_vat'] == 'N') echo "checked"; ?>> 미포함</label>
            </td>
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
        ?>
            <tr>
                <td colspan="2">

                    <div>
                        <h3 class="supplier-detail-img-title">공급사 제공 상세이미지</h3>
                    <?php
                    foreach ($prd_data['supplier_detail_img'] as $img) {
                    ?>
                        <img src="<?= $img ?>">
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
    });

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

</script>