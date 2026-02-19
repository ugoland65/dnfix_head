<style>
    .grouping-view-form-wrap{
        padding: 20px 30px;
        display: flex;
        flex-direction: column;
        gap: 10px;

        input[type="text"]{
            width: 100%;
        }
    }

    .pg-memo{
        width:200px;
        height: 80px;
    }

    #form_prdGroupingSave{
        width: 100%;
        height:100%;
    }
</style>
<div id="contents_head">
	<h1>상품 그룹핑 - <?=$productGrouping['pg_subject'] ?? ''?> (<?= $productGrouping['prd_count'] ?> 개)</h1>
</div>
<div id="contents_body" class="partition-body">
	<div id="contents_body_wrap">

        <form id="form_prdGroupingSave" method="post" action="/admin/product/grouping_save">
        <input type="hidden" name="idx" value="<?= $productGrouping['idx'] ?? '' ?>">

        <div class="partition-wrap">
            <ul class="partition-body">

            <?php
                if( $productGrouping['prd_mode'] == 'provider' ){
            ?>
                <div class="table-wrap5">
                    <div class="scroll-wrap">

                        <table class="table-st1">
                            <thead>
                                <tr>
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach($productGrouping['prd_data'] as $item){
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $item['idx'] ?></td>
                                        <td class="text-center">
                                            <?= $item['status'] ?>
                                            <?php if( $item['status'] == '품절' ){ ?>
                                                <br><span class="text-red"><?=date('Y.m.d', strtotime($item['sold_out_date'])) ?? ''?></span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <img src="<?= $item['img_src'] ?>" style="height:70px; border:1px solid #eee !important;">
                                        </td>
                                        <td class="text-center"><?= $prd_kind_name[$item['kind']] ?? "미지정" ?></td>
                                        <td class="prd-name">
                                            <a href="javascript:prdProviderQuick(<?= $item['idx'] ?>);"><?= $item['name'] ?></a>
                                            <? if (!empty($item['memo'])) { ?>
                                                <br><span class="prd-memo"><?= $item['memo'] ?></span>
                                            <? } ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if( !empty($item['brand_idx']) ){ ?>
                                                <?= $item['brand_name'] ?>
                                            <?php } else { ?>
                                                <span class="text-red">미등록</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center"><?= $item['partner_name'] ?></td>
                                        <td class="text-center"><?= $item['code'] ?></td>
                                        <td class="text-center">
                                            <?php if( !empty($item['godo_goodsNo']) ){ ?>
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

                                        <td class="prd-name text-left">
                                            <a href="javascript:goSupplierProductEdit('<?= $item['supplier_prd_idx'] ?>');"><?= $item['name_p'] ?? '-' ?></a>
                                            <?php
                                            if (!empty($item['matching_option'])) {
                                            ?>
                                                <br>( 옵션 : <?= $item['matching_option'] ?? '-' ?>)
                                            <?php
                                            }
                                            ?>
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
                                                <?php if( ($supplierProductMap[$item['supplier_prd_idx']]['status'] ?: '') == '품절' ){ ?>
                                                    <br><span class="text-red"><?=date('Y.m.d', strtotime($supplierProductMap[$item['supplier_prd_idx']]['sold_out_date'])) ?? ''?></span>
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

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php }else{ ?>

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
    <button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="location.href='/admin/product/grouping'" > 
		<i class="fas fa-arrow-left"></i> 목록
	</button>
</div>