<div id="contents_head">
	<h1>쿠팡 상품 목록</h1>
	<h3>쿠팡 상품 목록입니다.</h3>

    <button type="button" id="coupangProductSyncBtn" class="btnstyle1 btnstyle1-success btnstyle1-sm m-l-20" >
        쿠팡 상품 동기화
    </button>
    <button type="button" id="coupangProductDetailSyncSelectedBtn" class="btnstyle1 btnstyle1-success btnstyle1-sm m-l-10" >
        선택 데이터수집
    </button>
    <button type="button" id="coupangProductDetailSyncAllBtn" class="btnstyle1 btnstyle1-success btnstyle1-sm m-l-10" >
        전체 데이터수집
    </button>

</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div id="list_new_wrap">

            <div class="table-top">
                <ul class="total">
                    Total : <span><b><?= number_format($pagination['total']) ?></b></span> &nbsp; | &nbsp;
                    <span><b><?= $pagination['current_page'] ?></b></span> / <?= $pagination['last_page'] ?> page
                </ul>

            </div>

            <div class="table-wrap5 m-t-5">
                <div class="scroll-wrap">

                    <table class="table-st1">
						<thead>
							<tr>
								<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
								<th class="list-idx">고유번호</th>
                                <th class="">이미지</th>
                                <th class="">상품명</th>
                                <th class="">매칭<br>재고코드</th>
                                <th class="">일반상품</th>
                                <th class="">일반<br>설정재고</th>
                                <th class="">로켓상품</th>
                                <th class="">로켓<br>설정재고</th>
                                <th class="">등록상품ID</th>
								<th class="">판매/승인<br>상태</th>
                                <th class="">판매일/종료일</th>
                                <th class="">데이터수집</th>
                                <th class="">데이터수집일</th>
                                <th class="">현재고</th>
                                <th class="">책정원가</th>
                                <th class="">마진율</th>
							</tr>
						</thead>
						<tbody>
                            <?php
                                foreach ($coupangProductList as $row) {

                                    if($row['thumbnail']){ 
                                        $thumbnail = "https://image.coupangcdn.com/image/".$row['thumbnail'];
                                    }else{
                                        $thumbnail = null;
                                    }

                                    //수수료
                                    $commission = $row['marketplace_price'] * 0.1056;
                                    //수수료제외
                                    $commission_ex = $row['marketplace_price'] * 0.8944;

                                    $margin = null;
                                    $margin_per = null;

                                    if( $commission_ex > 0 && $row['cd_cost_price'] > 0 ){
                                        if( $commission_ex < 29999 ){
                                            $margin = $commission_ex - $row['cd_cost_price'];
                                            $margin_per =  round( $margin / $commission_ex * 100, 2);
                                        }else{
                                            $margin = $commission_ex - ($row['cd_cost_price'] + 2500);
                                            $margin_per =  round( $margin / $commission_ex * 100, 2);
                                        }
                                    }

                            ?>
                                <tr>
                                    <td><input type="checkbox" name="check_idx[]" value="<?=$row['idx']?>"></td>
                                    <td class="text-center"><?=$row['idx']?></td>
                                    <td>
                                        <?php if($thumbnail){ ?>
                                        <img src="<?=$thumbnail?>" alt="<?=$row['name']?>" style="width:60px; height:60px;">
                                        <?php } ?>
                                    </td>
                                    <td><?=$row['name']?></td>
                                    <td>
                                        <div>
                                            <?php if (!empty($row['ps_idx'])) { ?>
                                                <ul>
                                                    <b><?=$row['ps_idx']?></b>
                                                </ul>
                                                <ul class="m-t-3">
                                                    <button
                                                        type="button"
                                                        class="btnstyle1 btnstyle1-danger btnstyle1-xs matchingCancelBtn"
                                                        data-coupang-idx="<?=$row['idx']?>"
                                                        onclick="cancelMatchingProduct('<?=$row['idx']?>')"
                                                    >
                                                        매칭취소
                                                    </button>
                                                </ul>
                                            <?php } else { ?>
                                                <ul>
                                                    <input
                                                        type="text"
                                                        name="matching_idx"
                                                        class="matching-idx-input"
                                                        data-coupang-idx="<?=$row['idx']?>"
                                                        value=""
                                                        placeholder="ps_idx"
                                                        style="width: 60px;"
                                                    >
                                                </ul>
                                                <ul class="m-t-3">
                                                    <button
                                                        type="button"
                                                        class="btnstyle1 btnstyle1-success btnstyle1-xs matchingProductBtn"
                                                        data-coupang-idx="<?=$row['idx']?>"
                                                        onclick="matchingProduct('<?=$row['idx']?>')"
                                                    >
                                                        매칭
                                                    </button>
                                                </ul>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <?php
                                            /*
                                                "marketplaceItemData" => array:10 [
                                                "sellerProductItemId" => 33929149699
                                                "vendorItemId" => 91769263810
                                                "itemId" => 24760956231
                                                "bestPriceGuaranteed3P" => false
                                                "externalVendorSku" => string(0) ""
                                                "priceData" => array:3 [
                                                    "originalPrice" => 89000
                                                    "salePrice" => 87000
                                                    "supplyPrice" => 0
                                                ]
                                                "barcode" => string(0) ""
                                                "maximumBuyCount" => 0
                                                "modelNo" => string(0) ""
                                                "isAutoGenerated" => false
                                                ]
                                            */
                                        ?>
                                        <?php if( !empty($row['marketplace_price'] ) ){ ?>
                                            <b><?=number_format($row['marketplace_price'])?></b>
                                            <p>
                                                <?=number_format($commission)?>
                                            </p>
                                            <p>
                                                <?=number_format($commission_ex)?>
                                            </p>
                                        <?php } ?>

                                    </td>
                                    <td class="text-right">
                                        <?php if( !empty($row['stock_json'] ) ){ ?>
                                            <?php if( $row['stock_json']['data']['onSale'] ){ ?>
                                                <p class="text-danger">판매중</p>
                                            <?php } else { ?>
                                                <p class="text-muted">판매중지</p>
                                            <?php } ?>
                                            <?=number_format($row['stock_json']['data']['amountInStock'])?>
                                        <?php } ?>
                                    </td>

                                    <td class="text-right">
                                        <?php
                                            /*
                                                "rocketGrowthItemData" => array:10 [
                                                    "sellerProductItemId" => 33265027138
                                                    "vendorItemId" => 91172409178
                                                    "itemId" => 13958556137
                                                    "skuInfo" => array:22 [
                                                        "fragile" => false
                                                        "year" => null
                                                        "season" => string(10) "YEAR_ROUND"
                                                        "height" => 285
                                                        "length" => 165
                                                        "width" => 220
                                                        "weight" => 2200
                                                        "producedAtManaged" => false
                                                        "quantityPerBox" => 1
                                                        "standAlone" => false
                                                        "distributionPeriod" => 0
                                                        "expiredAtManaged" => false
                                                        "manufacturedAtManaged" => false
                                                        "netWeight" => null
                                                        "heatSensitive" => null
                                                        "hazardous" => null
                                                        "originalBarcode" => null
                                                        "inboundName" => string(11) "도원향님펫2홀듀얼구조"
                                                        "originalDimensionInputType" => null
                                                        "vendorFlexCode" => null
                                                        "heavyBulky" => null
                                                        "rocketInstallation" => null
                                                    ]
                                                    "externalVendorSku" => string(0) ""
                                                    "priceData" => array:3 [
                                                        "originalPrice" => 89000
                                                        "salePrice" => 87000
                                                        "supplyPrice" => 84129
                                                    ]
                                                    "barcode" => string(13) "4589431650481"
                                                    "maximumBuyCount" => 0
                                                    "modelNo" => string(0) ""
                                                    "isAutoGenerated" => false
                                                    ]
                                            */
                                        ?>
                                        <?php if( !empty($row['rocket_price'] ) ){ ?>
                                            <?=number_format($row['rocket_price'])?>
                                        <?php } ?>
                                    </td>
                                    <td class="text-right">
                                        <?php if( !empty($row['rocket_stock_json'] ) ){ ?>
                                            <?php if( $row['rocket_stock_json']['data']['onSale'] ){ ?>
                                                <p class="text-danger">판매중</p>
                                            <?php } else { ?>
                                                <p class="text-muted">판매중지</p>
                                            <?php } ?>
                                            <?=number_format($row['rocket_stock_json']['data']['amountInStock'])?>
                                        <?php } ?>
                                    </td>
                                    <td><?=$row['seller_product_id']?></td>
                                    <td class="text-center"><?=$row['status']?></td>
                                    <td>
                                        <p><?=$row['sale_started_at']?></p>
                                        <p><?=$row['sale_ended_at']?></p>
                                    </td>
                                    <td class="text-center">
                                        <button
                                            type="button"
                                            class="btnstyle1 btnstyle1-success btnstyle1-sm coupangProductDetailSyncBtn"
                                            data-seller-product-id="<?=$row['seller_product_id']?>"
                                            data-product-id="<?=$row['product_id'] ?? ''?>"
                                        >
                                            데이터수집
                                        </button>
                                    </td>
                                    <td><?=$row['detail_loaded_at']?></td>
                                    <td class="text-center">
                                        <?php if ((int)($row['ps_stock'] ?? 0) > 0) { ?>
                                            <b><?=number_format($row['ps_stock'])?></b>
                                        <?php } else { ?>
                                            <span style="color:#999;">재고없음</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-right">
                                        <?=number_format($row['cd_cost_price'])?>
                                    </td>
                                    <td class="text-right">

                                        <?php if( !empty($margin) ){ ?>
                                            <b><?=number_format($margin)?></b>
                                            <p>
                                                <?=number_format($margin_per)?>%
                                            </p>
                                        <?php } ?>

                                    </td>
                                </tr>
                            <?php
                                }
                            ?>

                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap"><?= $paginationHtml ?></div>
</div>

<script>
$(function () {
    window.matchingProduct = function (coupangIdx) {
        var idx = parseInt(coupangIdx, 10) || 0;
        if (!idx) {
            alert('쿠팡 상품 idx가 올바르지 않습니다.');
            return;
        }

        var $input = $('.matching-idx-input[data-coupang-idx="' + idx + '"]');
        var $btn = $('.matchingProductBtn[data-coupang-idx="' + idx + '"]');
        var psIdx = $.trim($input.val());

        if (!psIdx) {
            alert('매칭할 재고코드(ps_idx)를 입력해주세요.');
            $input.focus();
            return;
        }
        if (!/^\d+$/.test(psIdx)) {
            alert('재고코드(ps_idx)는 숫자만 입력 가능합니다.');
            $input.focus();
            return;
        }

        $btn.prop('disabled', true).text('매칭중...');

        ajaxRequest('/admin/coupang/action', {
            action_mode: 'product_matching',
            idx: idx,
            ps_idx: psIdx
        })
            .done(function (res) {
                if (res && res.success) {
                    alert(res.message || '상품 매칭이 완료되었습니다.');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '상품 매칭에 실패했습니다.');
                }
            })
            .fail(function () {
                alert('상품 매칭 요청 중 오류가 발생했습니다.');
            })
            .always(function () {
                $btn.prop('disabled', false).text('매칭');
            });
    };

    window.cancelMatchingProduct = function (coupangIdx) {
        var idx = parseInt(coupangIdx, 10) || 0;
        if (!idx) {
            alert('쿠팡 상품 idx가 올바르지 않습니다.');
            return;
        }
        if (!confirm('현재 매칭을 해제할까요?')) {
            return;
        }

        var $btn = $('.matchingCancelBtn[data-coupang-idx="' + idx + '"]');
        $btn.prop('disabled', true).text('해제중...');

        ajaxRequest('/admin/coupang/action', {
            action_mode: 'product_matching_cancel',
            idx: idx
        })
            .done(function (res) {
                if (res && res.success) {
                    alert(res.message || '상품 매칭이 해제되었습니다.');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '상품 매칭 해제에 실패했습니다.');
                }
            })
            .fail(function () {
                alert('상품 매칭 해제 요청 중 오류가 발생했습니다.');
            })
            .always(function () {
                $btn.prop('disabled', false).text('매칭취소');
            });
    };

    function syncProductDetail($btn, options) {
        var opts = $.extend({
            confirmBefore: true,
            reloadOnSuccess: true,
            showAlert: true,
            syncStock: true
        }, options || {});

        var sellerProductId = $btn.data('seller-product-id');
        var productId = $btn.data('product-id');

        var dfd = $.Deferred();

        if (!sellerProductId) {
            if (opts.showAlert) {
                alert('sellerProductId가 없어 데이터 수집을 진행할 수 없습니다.');
            }
            return dfd.reject({ message: 'sellerProductId missing' }).promise();
        }

        if (opts.confirmBefore && !confirm('해당 상품 상세 데이터를 수집할까요?\n(sellerProductId: ' + sellerProductId + ')')) {
            return dfd.reject({ message: 'cancelled' }).promise();
        }

        $btn.prop('disabled', true).text('수집중...');

        var payload = {
            action_mode: 'product_detail_sync',
            sellerProductId: sellerProductId,
            productId: productId,
            syncStock: opts.syncStock ? 'Y' : 'N'
        };

        ajaxRequest('/admin/coupang/action', payload)
            .done(function (res) {
                if (res && res.success) {
                    if (opts.showAlert) {
                        alert(res.message || '상품 상세 데이터 수집이 완료되었습니다.');
                    }
                    if (opts.reloadOnSuccess) {
                        location.reload();
                    }
                    dfd.resolve(res);
                } else {
                    if (opts.showAlert) {
                        alert(res && res.message ? res.message : '상품 상세 데이터 수집에 실패했습니다.');
                    }
                    dfd.reject(res || { message: 'failed' });
                }
            })
            .fail(function () {
                if (opts.showAlert) {
                    alert('상품 상세 데이터 수집 요청 중 오류가 발생했습니다.');
                }
                dfd.reject({ message: 'request failed' });
            })
            .always(function () {
                $btn.prop('disabled', false).text('데이터수집');
            });

        return dfd.promise();
    }

    function collectTargetButtons(mode) {
        if (mode === 'selected') {
            var $selectedRows = $('input[name="check_idx[]"]:checked').closest('tr');
            return $selectedRows.find('.coupangProductDetailSyncBtn');
        }
        return $('.coupangProductDetailSyncBtn');
    }

    function runBatchDetailSync(mode) {
        var $targets = collectTargetButtons(mode);
        var total = $targets.length;

        if (!total) {
            alert(mode === 'selected' ? '선택된 상품이 없습니다.' : '수집할 상품이 없습니다.');
            return;
        }

        if (!confirm(total + '개 상품의 상세 데이터를 순차 수집할까요?')) {
            return;
        }

        var $batchBtns = $('#coupangProductDetailSyncSelectedBtn, #coupangProductDetailSyncAllBtn');
        var originalTextMap = {};
        $batchBtns.each(function () {
            originalTextMap[this.id] = $(this).text();
        });
        $batchBtns.prop('disabled', true);

        var successCount = 0;
        var failCount = 0;
        var chain = $.Deferred().resolve();

        $targets.each(function (index) {
            var $itemBtn = $(this);
            chain = chain.then(function () {
                $('#coupangProductDetailSyncSelectedBtn').text('선택 데이터수집 (' + (index + 1) + '/' + total + ')');
                $('#coupangProductDetailSyncAllBtn').text('전체 데이터수집 (' + (index + 1) + '/' + total + ')');

                return syncProductDetail($itemBtn, {
                    confirmBefore: false,
                    reloadOnSuccess: false,
                    showAlert: false,
                    syncStock: true
                }).then(
                    function () { successCount++; },
                    function () { failCount++; }
                );
            });
        });

        chain.always(function () {
            $batchBtns.each(function () {
                var id = this.id;
                $(this).prop('disabled', false).text(originalTextMap[id]);
            });
            alert('일괄 수집 완료\n성공: ' + successCount + '건\n실패: ' + failCount + '건');
            location.reload();
        });
    }

    $('#coupangProductSyncBtn').on('click', function () {
        if (!confirm('쿠팡 상품 동기화를 시작할까요?')) {
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('동기화 진행중...');

        var payload = {
            action_mode: 'product_sync'
        };

        ajaxRequest('/admin/coupang/action', payload)
            .done(function (res) {
                if (res && res.success) {
                    alert(res.message || '상품 동기화가 완료되었습니다.');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '상품 동기화 처리에 실패했습니다.');
                }
            })
            .fail(function () {
                alert('상품 동기화 요청 중 오류가 발생했습니다.');
            })
            .always(function () {
                $btn.prop('disabled', false).text('쿠팡 상품 동기화');
            });
    });

    $('#coupangProductDetailSyncSelectedBtn').on('click', function () {
        runBatchDetailSync('selected');
    });

    $('#coupangProductDetailSyncAllBtn').on('click', function () {
        runBatchDetailSync('all');
    });

    $(document).on('click', '.coupangProductDetailSyncBtn', function () {
        syncProductDetail($(this));
    });
});
</script>