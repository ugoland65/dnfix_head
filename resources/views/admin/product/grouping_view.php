<style>
    .grouping-view-form-wrap {
        padding: 20px 30px;
        display: flex;
        flex-direction: column;
        gap: 10px;

        input[type="text"] {
            width: 100%;
        }
    }

    .pg-memo {
        width: 200px;
        height: 80px;
    }

    #form_prdGroupingSave {
        width: 100%;
        height: 100%;
    }

    .prd-name{
        width: 300px;
        max-width: 300px;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
    }
    .prd-memo {
        color: #ff0000;
        margin-top: 5px;
        display: block;
        max-width: 300px;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .pg-qty {
        width: 40px !important;
        height: 28px !important;
    }

    .pg-sale-price {
        width: 80px !important;
        height: 28px !important;
    }
    .row-dis-sale-price{ color:#ff0000; }
</style>
<div id="contents_head">
    <h1>상품 그룹핑 - <?= $productGrouping['pg_subject'] ?? '' ?> (<?= $productGrouping['prd_count'] ?> 개)</h1>
</div>
<div id="contents_body" class="partition-body">
    <div id="contents_body_wrap">

        <form id="form_prdGroupingSave" method="post" action="/admin/product/grouping_save">
            <input type="hidden" name="idx" value="<?= $productGrouping['idx'] ?? '' ?>">
            <input type="hidden" name="pg_mode" value="<?= $productGrouping['pg_mode'] ?? '' ?>">

            <div class="partition-wrap">
                <ul class="partition-body">

                    <?php
                    // 공급사 상품일 경우
                    if ($productGrouping['prd_mode'] == 'provider') {
                    ?>
                        <div class="table-wrap5">
                            <div class="scroll-wrap">

                                <table class="table-st1">
                                    <thead>
                                        <tr>
                                            <th>순서</th>
                                            <th class="list-idx">고유번호</th>
                                            <th class="">등록상태</th>
                                            <th class="" style="width:80px;">이미지</th>
                                            <th class="" style="width:50px;">분류</th>
                                            <th class="prd-name">이름</th>
                                            <th class="">브랜드</th>
                                            <th class="">공급사</th>
                                            <th class="">코드</th>
                                            <th class="">고도몰<br>상품코드</th>
                                            <th class="">고도몰<br>판매가</th>
                                            <th class="">상품원가<br>/주문가</th>
                                            <th class="">공급사<br>이미지</th>
                                            <th class="prd-name">공급사<br>상품명</th>
                                            <th class="">공급사<br>상품코드</th>
                                            <th class="">공급사<br>판매상태</th>
                                            <th class="">공급 2차</th>
                                            <th class="">수정일<br>등록일</th>
                                            <th class="">메모</th>

                                            <?php if( $productGrouping['pg_state'] != '마감' ){ ?>
                                            <th>삭제</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody id="prd_search_add_prd_list_table">
                                        <?php
                                        foreach ($productGrouping['data'] as $row) {

                                            $item = $row['prd_data'];
                                        ?>
                                            <tr>
                                                <td class="text-center"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></td>
                                                <td class="text-center">
                                                    <input type="hidden" name="prd_idx[]" value="<?= $item['idx'] ?>">
                                                    <?= $item['idx'] ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= $item['status'] ?>
                                                    <?php if ($item['status'] == '품절') { ?>
                                                        <br><span class="text-red"><?= date('Y.m.d', strtotime($item['sold_out_date'])) ?? '' ?></span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <img src="<?= $item['img_src'] ?>" style="height:70px; border:1px solid #eee !important;">
                                                </td>
                                                <td class="text-center"><?= $item['kind'] ?></td>
                                                <td>
                                                    <div>
                                                        <ul class="prd-name"><a href="javascript:prdProviderQuick(<?= $item['idx'] ?>);"><?= $item['name'] ?></a></ul>
                                                        <?php if (!empty($item['memo'])) { ?>
                                                            <ul class="m-t-3">
                                                                <span class="prd-memo"><?= $item['memo'] ?></span>
                                                            </ul>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (!empty($item['brand_idx'])) { ?>
                                                        <?= $item['brand_name'] ?>
                                                    <?php } else { ?>
                                                        <span class="text-red">미등록</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center"><?= $item['partner_name'] ?></td>
                                                <td class="text-center"><?= $item['code'] ?></td>
                                                <td class="text-center">
                                                    <?php if (!empty($item['godo_goodsNo'])) { ?>
                                                        <div style="font-size: 12px;">
                                                            #<?= $item['godo_goodsNo'] ?>
                                                        </div>
                                                        <div class="m-t-3">
                                                            <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall(<?= $item['godo_goodsNo'] ?>);">쑈당몰 상품보기</button>
                                                        </div>
                                                        <div class="m-t-5">
                                                            <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin(<?= $item['godo_goodsNo'] ?>);">관리자 상품보기</button>
                                                        </div>
                                                    <?php } else { ?>
                                                        <span class="text-red">미등록</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right"><?= number_format($item['sale_price']) ?></td>
                                                <td class="text-right">
                                                    <?= number_format($item['cost_price']) ?>
                                                    <br><b><?= number_format($item['order_price']) ?></b>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    if (!empty($item['supplier_img_src'])) {
                                                    ?>
                                                        <img src="<?= $item['supplier_img_src'] ?>" style="height:70px; border:1px solid #eee !important;">
                                                    <?php } else { ?>
                                                        -
                                                    <?php } ?>
                                                </td>

                                                <!-- 공급사 상품명 -->
                                                <td>
                                                    <div>
                                                        <ul class="prd-name"><a href="javascript:goSupplierProductEdit('<?= $item['supplier_prd_idx'] ?>');"><?= $item['name_p'] ?? '-' ?></a></ul>
                                                        <?php if (!empty($item['matching_option'])) { ?>
                                                            <ul class="m-t-3">
                                                                ( 옵션 : <?= $item['matching_option'] ?? '-' ?>)
                                                            </ul>
                                                        <?php } ?>
                                                    </div>
                                                </td>

                                                <!-- 공급사 상품코드 -->
                                                <td class="text-center">
                                                    <?php
                                                    if (!empty($item['supplier_prd_idx'])) {
                                                    ?>
                                                        <div style="font-size: 12px;">
                                                            #<?= $item['supplier_prd_pk'] ?>
                                                        </div>
                                                        <div class="m-t-3">
                                                            <button type="button" class="btnstyle1 btnstyle1-xs"
                                                                onclick="goSupplierProduct('<?= $item['supplier_site'] ?>', '<?= $item['supplier_prd_pk'] ?>');">공급사 사이트</button>
                                                        </div>
                                                    <?php } else { ?>
                                                        -
                                                    <?php } ?>
                                                </td>

                                                <!-- 공급사 판매상태 -->
                                                <td class="text-center">
                                                    <?php
                                                    if (!empty($item['supplier_prd_idx'])) {
                                                    ?>
                                                        <?= $supplierProductMap[$item['supplier_prd_idx']]['status'] ?? '-' ?>
                                                        <?php if (($supplierProductMap[$item['supplier_prd_idx']]['status'] ?: '') == '품절') { ?>
                                                            <br><span class="text-red"><?= date('Y.m.d', strtotime($supplierProductMap[$item['supplier_prd_idx']]['sold_out_date'])) ?? '' ?></span>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        -
                                                    <?php } ?>
                                                </td>

                                                <td class="text-center"><?= $item['supplier_2nd_name'] ?? '-' ?></td>

                                                <td class="text-center">
                                                    <?= date('Y.m.d H:i', strtotime($item['updated_at'])) ?? '-' ?><br>
                                                    <?= date('Y.m.d H:i', strtotime($item['created_at'])) ?? '-' ?>
                                                </td>

                                                <td>
                                                    <textarea name="pg_prd_memo[]" class="pg-memo"><?= $item['memo_work'] ?? '' ?></textarea>
                                                </td>

                                                <?php if( $productGrouping['pg_state'] != '마감' ){ ?>
                                                <td>
                                                    <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs prd-list-del-btn" data-idx="<?= $item['CD_IDX'] ?>"><i class="fas fa-trash-alt"></i></button>
                                                </td>
                                                <?php } ?>

                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php
                        // 상품 DB일 경우
                    } else if ($productGrouping['prd_mode'] == 'prdDB') {

                        $pgMode = (string)($productGrouping['pg_mode'] ?? '');
                        $pgState = (string)($productGrouping['pg_state'] ?? '');
                        $isDiscountEditable = $pgMode === 'event' || $pgMode === 'sale' || ($pgMode === 'period' && $pgState !== '마감');

                    ?>
                        <div class="table-wrap5">
                            <div class="scroll-wrap">

                                <table class="table-st1">
                                    <thead>
                                        <tr>
                                            <th>순서</th>
                                            <th class="list-idx">고유번호</th>
                                            <th class="" style="width:80px;">이미지</th>
                                            <th class="" style="width:50px;">분류</th>
                                            <th class="prd-name">이름</th>
                                            <th>최근할인일</th>
                                            <th>판매가<br>원가</th>
                                            <th>재고</th>

                                            <?php if ($isDiscountEditable) { ?>
                                            <th>할인률 선택</th>
                                            <th>할인률</th>
                                            <?php } ?>

                                            <th class="">메모</th>

                                            <?php if( $productGrouping['pg_state'] != '마감' ){ ?>
                                            <th>삭제</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody id="prd_search_add_prd_list_table">
                                        <?php
                                        foreach ($productGrouping['data'] as $row) {

                                            $item = $row['prd_data'];

                                            /*
                                            if (!empty($item['CD_IMG'])) {
                                                $img_path = '/data/comparion/' . $item['CD_IMG'];
                                            }
                                            */

                                            if( $item['img_mode'] == 'out' ){
                                                if (!empty($item['CD_IMG'])) {
                                                    $img_path = $item['CD_IMG'];
                                                }
                                            }else{
                                                if (!empty($item['CD_IMG'])) {
                                                    $img_path = '/data/comparion/' . $item['CD_IMG'];
                                                }
                                            }
                                        

                                        ?>
                                            <tr>
                                                <td class="text-center"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></td>
                                                <td class="text-center">
                                                    <input type="hidden" name="prd_idx[]" value="<?= $item['CD_IDX'] ?>">
                                                    <?= $item['CD_IDX'] ?>
                                                </td>
                                                <td>
                                                    <img src="<?= $img_path ?>" style="height:70px; border:1px solid #eee !important; cursor:pointer;" 
                                                        onclick="onlyAD.prdView('<?= $item['CD_IDX'] ?? '' ?>','info');">
                                                </td>
                                                <td class="text-center"><?= $prd_kind_name[$item['CD_KIND_CODE']] ?? "미지정" ?></td>
                                                <td class="" style="max-width:300px;">

                                                    <?php if ($row['idx'] == "Instant") { ?>

                                                        <b><?= $row['pname'] ?></b>

                                                    <?php } else { ?>

                                                        <div>
                                                            <ul style="font-size:11px;"><?= $item['barcode'] ?></ul>
                                                            <ul class="m-t-3" style="font-size:11px;"><?= $item['brand_name'] ?></ul>
                                                            <ul class="prd-name m-t-3"><span onclick="onlyAD.prdView('<?= $item['CD_IDX'] ?? '' ?>','info');" style="cursor:pointer;"><b><?= $item['CD_NAME'] ?></b></span></ul>
                                                            <?php if (!empty($item['CD_MEMO'])) { ?>
                                                                <ul class="m-t-3">
                                                                    <span class="prd-memo"><?= $item['CD_MEMO'] ?></span>
                                                                </ul>
                                                            <?php } ?>
                                                        </div>

                                                    <?php } ?>

                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                        $saleDate = $item['ps_sale_date'] ?? null;
                                                        if (
                                                            !empty($saleDate) &&
                                                            $saleDate !== '0000-00-00 00:00:00' &&
                                                            $saleDate !== '0000-00-00' &&
                                                            ($ts = strtotime($saleDate)) // strtotime 실패하면 false
                                                        ) {
                                                    ?>
                                                        <div>
                                                            <ul class="text-center"><?=date('y.m.d', $ts)?></ul>
                                                            <ul class="text-center m-t-5" style="font-size:12px;">총 할인수 : <?=count($item['ps_sale_log']) ?? 0?></ul>
                                                            <ul class="text-center" style="font-size:11px;"><?=$item['ps_sale_log'][0]['pg_subject'] ?? ''?></ul>
                                                            <ul class="text-center"><?=$item['ps_sale_log'][0]['sale_per'] ?? 0?>%</ul>
                                                        </div>
                                                    <?php
                                                        } else {
                                                            echo '-';
                                                        }
                                                    ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php
                                                    $_margin = 0;
                                                    $_margin_pre = 0;
                                                    $_cd_sale_price = (int)($item['cd_sale_price'] ?? 0);
                                                    $_cd_cost_price = (int)($item['cd_cost_price'] ?? 0);

                                                    if ($_cd_sale_price > 0 && $_cd_cost_price > 0) {
                                                        $_margin = $_cd_sale_price - $_cd_cost_price;
                                                        $_margin_pre = round(($_cd_sale_price - $_cd_cost_price) / $_cd_sale_price * 100, 2);
                                                    }
                                                    ?>
                                                    <div class="p-l-10 p-r-10">
                                                        <ul>판매 : <span id="row_sale_price_<?= $item['CD_IDX'] ?>" data-saleprice="<?= $_cd_sale_price ?>"><?= number_format($_cd_sale_price) ?></span></ul>
                                                        <ul class="m-t-4">원가 : <span id="row_cost_price_<?= $item['CD_IDX'] ?>" data-costprice="<?= $_cd_cost_price ?>"><?= number_format($_cd_cost_price) ?></span></ul>
                                                        <? if ($_cd_sale_price > 0 && $_cd_cost_price > 0) { ?>
                                                            <ul class="m-t-4">마진 : <?= number_format($_margin) ?><br>( <b><?= $_margin_pre ?></b> %) </ul>
                                                        <? } ?>
                                                    </div>

                                                </td>
                                                <td class="text-right">
                                                    <?php if (($item['ps_stock'] ?? 0) == 0) { ?>
                                                        <span style="color:#ff0000;">재고<br>없음</span>
                                                    <?php } else { ?>
                                                        <b style="font-size:15px; color:#5e41ff;"><?= number_format($item['ps_stock'] ?? 0) ?></b>
                                                    <?php } ?>
                                                </td>

                                                <?php
                                                    if ($isDiscountEditable) {
                                                        $_mode_data = (isset($row['mode_data']) && is_array($row['mode_data'])) ? $row['mode_data'] : [];
                                                        $_mode_data_per = (int)($_mode_data['per'] ?? 0);
                                                        $_margin_ex_per = [10, 15, 20, 25, 30];
                                                ?>
                                                    <td>
                                                        <div>
                                                            <?php foreach ($_margin_ex_per as $discountPer): ?>
                                                                <?php
                                                                $dis_sale_price = $_cd_sale_price * ((100 - $discountPer) / 100);
                                                                $dis_margin = $dis_sale_price - $_cd_cost_price;
                                                                $dis_margin_per = ($dis_sale_price > 0)
                                                                    ? round(($dis_sale_price - $_cd_cost_price) / $dis_sale_price * 100, 2)
                                                                    : 0;
                                                                ?>
                                                                <ul class="p-2" style="font-size:12px;">
                                                                    <label onclick="prdGroupingView.exChoise('<?= $item['CD_IDX'] ?>', <?= $discountPer ?>);">
                                                                        <input type="radio" name="row_ex_<?= $item['CD_IDX'] ?>" <?php if ($_mode_data_per === $discountPer) echo "checked"; ?>>
                                                                        <b><?= $discountPer ?></b>% | <?= number_format($dis_sale_price) ?> | <b><?= number_format($dis_margin) ?></b> |
                                                                        <?= $dis_margin_per ?>%
                                                                    </label>
                                                                </ul>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-left">

                                                        <div class="p-l-10 p-r-10">
                                                            <ul>
                                                                할인률 :
                                                                <input type='text' name="pg_prd_per[]" id='pg_prd_per_<?= $item['CD_IDX'] ?>' value="<?= $_mode_data_per ?>" autocomplete="off" class="pg-qty" onkeyUP="prdGroupingView.marginAutoCalculation( '<?= $item['CD_IDX'] ?>', this.value );">
                                                            </ul>
                                                            <ul class="m-t-4">
                                                                판매가 :
                                                                <span id="row_dis_sale_price_<?= $item['CD_IDX'] ?>" class="row-dis-sale-price"><?= number_format($row['mode_data']['sale_price'] ?? 0) ?></span>
                                                            </ul>
                                                            <ul class="m-t-4">마진금 : <span id="row_dis_margin_price_<?= $item['CD_IDX'] ?>"><?= number_format($row['mode_data']['margin_price'] ?? 0) ?></span></ul>
                                                            <ul class="m-t-4">마진율 : <span id="row_dis_margin_per_<?= $item['CD_IDX'] ?>"><?= $row['mode_data']['margin_per'] ?? 0 ?>%</span></ul>
                                                        </div>

                                                        <input type="hidden" name="original_sale_price[]" id="row_original_sale_price_input_<?= $item['CD_IDX'] ?>" value="<?= $item['cd_sale_price'] ?>">
                                                        <input type="hidden" name="dis_sale_price[]" id="row_dis_sale_price_input_<?= $item['CD_IDX'] ?>" value="<?= $item['mode_data']['sale_price'] ?? 0 ?>">
                                                        <input type="hidden" name="dis_margin_price[]" id="row_dis_margin_price_input_<?= $item['CD_IDX'] ?>" value="<?= $item['mode_data']['margin_price'] ?? 0 ?>">
                                                        <input type="hidden" name="dis_margin_per[]" id="row_dis_margin_per_input_<?= $item['CD_IDX'] ?>" value="<?= $item['mode_data']['margin_per'] ?? 0 ?>">

                                                    </td>

                                                <?php } ?>

                                                <td>
                                                    <textarea name="pg_prd_memo[]" class="pg-memo"><?= $item['memo_work'] ?? '' ?></textarea>
                                                </td>

                                                <?php if( $productGrouping['pg_state'] != '마감' ){ ?>
                                                <td>
                                                    <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs prd-list-del-btn" data-idx="<?= $item['CD_IDX'] ?>"><i class="fas fa-trash-alt"></i></button>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    <?php } else { ?>

                    <?php } ?>

                </ul>
                <ul class="partition-right ">

                    <div class="grouping-view-form-wrap">
                        <ul>
                            그룹핑 모드 : <b><?= $productGrouping['pg_mode_text'] ?? '' ?></b>
                        </ul>
                        <ul>
                            그룹핑 상품모드 : <b><?= $productGrouping['prd_mode_text'] ?? '' ?></b>
                        </ul>
                        <ul>
                            그룹핑 상품갯수 : <b><?= $productGrouping['prd_count'] ?></b> 개
                        </ul>
                        <ul>
                            공개여부 :
                            <select name="public" id="public">
                                <option value="공개" <?php if ($productGrouping['public'] == "공개") echo "selected"; ?>>공개</option>
                                <option value="개인" <?php if ($productGrouping['public'] == "개인") echo "selected"; ?>>개인</option>
                            </select>
                        </ul>
                        <ul>
                            <input type='text' name='pg_subject' id='pg_subject' value="<?= $productGrouping['pg_subject'] ?? '' ?>" placeholder="그룹핑 제목">
                        </ul>
                        <ul>
                            진행상태 :
                            <select name="pg_state" id="pg_state">
                                <option value="진행" <?php if (($productGrouping['pg_state'] ?? "") == "진행") echo "selected"; ?>>진행</option>
                                <option value="마감" <?php if (($productGrouping['pg_state'] ?? "") == "마감") echo "selected"; ?>>마감</option>
                                <option value="취소" <?php if (($productGrouping['pg_state'] ?? "") == "취소") echo "selected"; ?>>취소</option>
                            </select>
                        </ul>

                        <?php
                        if ($productGrouping['pg_mode'] == "period" || $productGrouping['pg_mode'] == "sale" || $productGrouping['pg_mode'] == "event") {
                        ?>
                            <ul>
                                진행일
                                <?php if (($productGrouping['pg_mode'] ?? "") == "period") { ?>
                                    <div class="calendar-input" style="display:inline-block; width:105px;" id="pg_sday_wrap">
                                        <input type="text" name="pg_sday" id="pg_sday" value="<?= $productGrouping['pg_sday'] ?? '' ?>" style="width:90px;" placeholder="시작일" autocomplete="off"> ~
                                    </div>
                                <?php } ?>
                                <div class="calendar-input" style="display:inline-block;">
                                    <input type="text" name="pg_day" id="pg_day" value="<?= $productGrouping['pg_day'] ?? '' ?>" style="width:90px;" autocomplete="off">
                                </div>
                            </ul>
                        <?php } ?>

                        <ul>
                            메모
                            <textarea name="pg_memo" id="pg_memo"><?= $productGrouping['pg_memo'] ?? '' ?></textarea>
                        </ul>
                        <ul>
                            <button type="submit" id="save_btn" class="btnstyle1 btnstyle1-primary btnstyle1-lg width-full">
                                저장
                            </button>
                        </ul>

                    </div>


                </ul>
            </div>
        </form>

    </div>
</div>
<div id="contents_bottom">
    <button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="location.href='/admin/product/grouping'">
        <i class="fas fa-arrow-left"></i> 목록
    </button>
</div>

<script>
    const prdGroupingView = (function() {

        /**
         * 할인율 퍼센트로 자동계산
         * @param string idx
         * @param string per
         */
        function marginAutoCalculation(idx, per) {
            const salePrice = Number($("#row_sale_price_" + idx).data("saleprice")) || 0;
            const costPrice = Number($("#row_cost_price_" + idx).data("costprice")) || 0;
            const discountPer = Number(per) || 0;

            const disSalePrice = Math.round(salePrice * ((100 - discountPer) / 100));
            const disMarginPrice = disSalePrice - costPrice;
            const disMarginPer = disSalePrice > 0
                ? ((disMarginPrice / disSalePrice) * 100).toFixed(2)
                : "0.00";

            $("#row_dis_sale_price_" + idx).html(GC.comma(disSalePrice));
            $("#row_dis_margin_price_" + idx).html(GC.comma(disMarginPrice));
            $("#row_dis_margin_per_" + idx).html(disMarginPer + "%");

            $("#row_dis_sale_price_input_" + idx).val(disSalePrice);
            $("#row_dis_margin_price_input_" + idx).val(disMarginPrice);
            $("#row_dis_margin_per_input_" + idx).val(disMarginPer);
        }

        /**
         * 할인률 선택
         * @param string idx
         * @param string per
         */
        function exChoise(idx, per) {
            $("#pg_prd_per_" + idx).val(per);
            marginAutoCalculation(idx, per);
        }

        /**
         * 그룹핑 목록에서 상품 행 삭제(저장 전 DOM 제거)
         * @param {HTMLElement} btn
         */
        function prdListDel(btn) {
            const $row = $(btn).closest('tr');
            if ($row.length === 0) return;

            dnConfirm(
                '상품 삭제',
                '해당 상품을 그룹핑 목록에서 삭제합니다.<br/>저장을 눌러야 최종적용되며 삭제된 상품은 다시 추가해야 합니다.<br/>삭제하시겠습니까?',
                function() {
                    $row
                        .css('background-color', '#ffe9e9')
                        .animate({ opacity: 0.25 }, 120)
                        .slideUp(180, function() {
                            $(this).remove();
                        });
                },
                'fas fa-trash',
                'red',
                '삭제',
                'btn-red',
                '취소'
            );
        }

        return {
            marginAutoCalculation,
            exChoise,
            prdListDel,
        }
    
    })();

    $(function(){

        $( "#prd_search_add_prd_list_table" ).sortable({
            axis: "y",
            cursor: "move"
        });

        $(document).on('click', '.prd-list-del-btn', function(){
            prdGroupingView.prdListDel(this);
        });

    });
</script>