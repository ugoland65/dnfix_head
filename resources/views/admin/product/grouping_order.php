<style>
    .prd-memo{
        color: #ff0000;
    }
    #prd_search_add_prd_list_table .ui-sortable-helper{
        background-color: #e8f3ff;
        border: 1px solid #4d90fe;
    }
</style>
<div class="prd-search-add-wrap">
    <ul class="left">

        <?php /*
        <div>
            <input type="text" name="prdSearch" id="prdSearch" value="" autocomplete="off" placeholder="검색어">
        </div>
        <div class="m-t-5 m-b-10 text-center">
            <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdGroupingAdd.prdSearch(this);">상품검색</button>
        </div>
        <div id="prdSearch_result">
        </div>

        <div>
            <ul>수동 인스턴트 상품등록</ul>
            <ul><input type="text" name="prd_instant_name" id="prd_instant_name" value="" autocomplete="off" placeholder="상품명"></ul>
        </div>
        <div class="m-t-5 m-b-10 text-center">
            <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdGroupingAdd.prdInstantAdd(this)">수동 인스턴트 상품 추가</button>
        </div>
        */ ?>

    </ul>
    <ul class="right">

        <div>※ 추가된 상품은 저장을 눌러야 최종 적용됩니다. <span id="add_prd_count"></span></div>
        <div class="prd-search-add-prd-list-wrap">

            <form id="form_productGroupingProductOrderChangeSave" method="post" action="/admin/product/grouping_product_order_change_save">
                <input type="hidden" name="idx" value="<?= $idx ?>">

                <div id="prd_search_add_prd_list_table" class="prd-search-add-prd-list-table">
                    <?php
                    foreach ($productGrouping['data'] as $row) {
                        $item = $row['prd_data'];
                    ?>

                        <?php
                        // 공급사 상품일 경우
                        if ($productGrouping['prd_mode'] == 'provider') {
                        ?>
                        <?php
                            // 상품 DB일 경우
                        } else if ($productGrouping['prd_mode'] == 'prdDB') {

                            $prd_idx = $row['idx'];
                            if (!empty($item['CD_IMG'])) {
                                $img_path = '/data/comparion/' . $item['CD_IMG'];
                            }

                            if( $item['cd_sale_price'] < 29999 ){
                                $_margin_per =  round( ($item['cd_sale_price'] - $item['cd_cost_price'] ) / $item['cd_sale_price'] * 100, 2);
                            }else{
                                $_margin_per =  round( ($item['cd_sale_price'] - ($item['cd_cost_price'] + 2500) ) / $item['cd_sale_price'] * 100, 2);
                            }

                        ?>
                            <ul class="" data-prdidx="<?= $prd_idx ?>">

                                <input type="hidden" name="prd_idx[]" value="<?= $prd_idx ?>">
                                <li class="text-center" style="width:40px"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></li>
                                <li class="text-center" style="width:45px"><?=$prd_idx?></li>

                                <?php if ($prd_idx == "Instant") { ?>

                                    <li class="text-center" style="width:55px; height:45px; ">
                                    </li>
                                    <li class="text-center" style="width:80px">
                                    </li>
                                    <li>
                                        <input type="text" name="prd_name[]" value="<?= $_pname ?>">
                                    </li>

                                <?php } else { ?>

                                    <li class="text-center" style="width:55px"><img src="<?= $img_path ?>" style="height:45px; border:1px solid #ddd;"></li>
                                    <li class="text-center" style="width:80px; font-size:12px;">
                                        <?= $item['brand_name'] ?>
                                    </li>
                                    <li>
                                        <input type="hidden" name="prd_name[]" value="<?= $item['CD_NAME'] ?>">
                                        <div>
                                            <ul><span class="prd-code"><?= $item['cd_code_fn']['jan'] ?></span></ul>
                                            <ul>
                                                <?= $_pname ?>
                                                <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?= $_prd_idx ?>','info');"">보기</button>
                                            </ul>

                                            <?php if ($item['cd_sale_price'] > 0 && $item['cd_cost_price'] > 0) { ?>
                                                <ul class=" m-t-5" style="font-size:12px;">
                                                    <?= number_format($item['cd_sale_price']) ?> ( <?= number_format($item['cd_cost_price']) ?> ) <b><?= $_margin_per ?></b>%
                                                </ul>
                                            <?php } ?>

                                            <?php if ($item['CD_MEMO']) { ?>
                                                <ul class="m-t-3">
                                                    <span class="prd-memo"><?= $item['CD_MEMO'] ?></span>
                                                </ul>
                                            <?php } ?>

                                            <ul><?= admin_in_sale_icon($item['ps_in_sale_s'], $item['ps_in_sale_e'], $item['ps_in_sale_data']) ?></ul>
                                        </div>
                                    </li>

                                <?php } ?>
                            </ul>
                        <?php } ?>

                    <?php } ?>
                </div>
            </form>

        </div>

        <div class="m-t-5 text-center">
            <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdGroupingAdd.prdSave(this, '<?= $idx ?>');">상품/노출순서 저장</button>
        </div>

    </ul>
</div>

<script>
    $(function(){

        $( "#prd_search_add_prd_list_table" ).sortable({
            axis: "y",
            cursor: "move"
        });

        $("#prdSearch").bind("keydown", function(e){
            if(e.which=="13"){
                prdGroupingAdd.prdSearch();
            }
        });

    });
</script>