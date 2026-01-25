<style>
    .layout-style1 {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        box-sizing: border-box;

        >ul {

            width: 350px;
            height: 100%;
            padding: 0;

            .layout-style1-section-top {
                height: 30px;
            }

            .layout-style1-section {
                height: calc(100% - 30px);
                border: 1px solid #ccc;
                background-color: #fff;
            }

        }

        >ul:first-child {
            flex: 1;
        }

    }

    .order_goods_list{

        display: none;

        .table-st1{
            border-top: 1px solid #b4b4b4;
            border-left: 1px solid #b4b4b4;
            border-bottom: 1px solid #b4b4b4;
            thead tr {
                position:static;
            }
            tfoot > tr{
                position:static;
            }
        }
    }
</style>

<div id="contents_head">
    <h1>고도몰 주문 가져오기</h1>
    <div class="m-l-20">
        <select name="mode" id="mode">
            <option value="p" <?= $mode == 'p' ? 'selected' : '' ?>>결제완료</option>
            <option value="ds" <?= $mode == 'ds' ? 'selected' : '' ?>>배송완료</option>
        </select>
        <label class="calendar-input">
            <input type='text' name='start_date' id="start_date" value="<?= $start_date ?? date('Y-m-d') ?>">
        </label>
        ~
        <label class="calendar-input">
            <input type='text' name='end_date' id="end_date" value="<?= $end_date ?? date('Y-m-d') ?>">
        </label>
        <button type="button" id="search_btn" class="btnstyle1 btnstyle1-primary btnstyle1-sm ">조회</button>
    </div>
</div>

<div id="contents_body">
    <div id="contents_body_wrap">

        <div class="layout-style1">
            <ul>
                <div class="scroll-wrap">
                    <table class="table-st1">
                        <thead>
                            <tr>
                                <th class="">No</th>
                                <th class="">주문번호</th>
                                <th class="">주문상품</th>
                                <th class="">주문일</th>
                                <th class="">결제일</th>
                                <th class="">결제방법</th>
                                <th class="">상품</th>
                                <th class="">총상품금액</th>
                                <th class="">총할인금액</th>
                                <th class="">할인율</th>
                                <th class="">부가결제</th>
                                <th class="">부가<br>결제율</th>
                                <th class="">결제금액</th>
                                <th class="">결제율</th>
                                <th class="">환불금액</th>
                                <th class="">정산금액</th>
                                <th class="">총원가</th>
                                <th class="">총수익</th>
                                <th class="">수익률</th>
                                <th class="">대상</th>
                                <th class="">보유</th>
                                <th class="">비매품</th>
                                <th class="">미보유</th>
                                <th class="">원가상품</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $settleKind = [
                                'gb' => '무',
                                'fa' => '무',
                                'pb' => '계좌이체',
                                'fb' => '계좌이체',
                                'eb' => '계좌이체',
                                'pc' => '신',
                                'fc' => '신',
                                'ec' => '신',
                                'ph' => '휴대폰',
                                'fh' => '휴대폰',
                                'fv' => '가상계좌',
                                'ev' => '가상계좌',
                                'pv' => '가상계좌',
                                'gz' => '전액할인',
                                'gd' => '예치금',
                                'gm' => '마일리지',
                                'fp' => '포인트',
                                'pn' => '네이버페이',
                                'pk' => '카카오페이',
                                'gr' => '기타',
                            ];

                            $no = 0;

                            $totalGoodsCnt = 0;
                            $totalGoodsPrice = 0;
                            $totalDcPrice = 0;
                            $totalUseMileage = 0;
                            $totalSalePrice = 0;
                            $totalRefundPrice = 0;
                            $totalFinalPrice = 0;
                            $totalNonSale = 0;
                            $totalNotMy = 0;
                            $totalCost = 0;
                            $totalMargin = 0;
                            $totalMarginRate = 0;

                            foreach ($orderList['orderMargin'] as $order) {
                                $no++;

                                $totalGoodsCnt += $order['orderGoodsCnt'] ?? 0;
                                $totalGoodsPrice += $order['totalGoodsPrice'] ?? 0;
                                $totalSalePrice += $order['salePrice'] ?? 0;
                                $totalDcPrice += $order['totalDcPrice'] ?? 0;
                                $totalUseMileage += $order['useMileage'] ?? 0;
                                $totalRefundPrice += $order['refundPrice'] ?? 0;
                                $totalFinalPrice += $order['finalPrice'] ?? 0;
                                $totalCost += $order['totalCost'] ?? 0;
                                $totalNonSale += $order['non_sale'] ?? 0;
                                $totalNotMy += $order['scm_not_my'] ?? 0;
                                $totalMargin += $order['marginAmount'] ?? 0;
                                $totalMarginRate += $order['marginRate'] ?? 0;

                                if (($order['goodsCnt'] ?? 0) > (($order['costGoodsCount'] ?? 0) + ($order['non_sale'] ?? 0) )) {
                                    $trClass = "notice";
                                } else {
                                    $trClass = "";
                                }

                            ?>
                                <tr class="<?= $trClass ?>">
                                    <td class="text-center"><?= $no ?></td>
                                    <td>
                                        <a href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= $order['orderNo'] ?>" target="_blank"><?= $order['orderNo'] ?></a>
                                    </td>
                                    <td>
                                        <button type="button" class="btnstyle1 btnstyle1-xs order-toggle" data-target="#goods_list_<?= $order['orderNo'] ?>">상품보기 ▼</button>
                                    </td>
                                    <td>
                                        <?= date('y.m.d H:i', strtotime($order['orderDate'])) ?>
                                    </td>
                                    <td>
                                        <?= date('y.m.d H:i', strtotime($order['paymentDt'])) ?>
                                    </td>
                                    <td>
                                        <?= $order['settleKind'] ?>
                                        (<?= $settleKind[$order['settleKind']] ?>)
                                    </td>
                                    <td class="text-center"><?= $order['orderGoodsCnt'] ?></td>
                                    <td class="text-right"><?= number_format($order['totalGoodsPrice']) ?></td>
                                    <td class="text-right"><?= number_format($order['totalDcPrice']) ?></td>

                                    <!-- 할인율 -->
                                    <?php
                                        $dcRate = $order['totalDcPrice'] / $order['totalGoodsPrice'] * 100;
                                    ?>
                                    <td class="text-right"><?= number_format($dcRate) ?>%</td>

                                    <!-- 부가결제 -->
                                    <td class="text-right"><?= number_format($order['useMileage']) ?></td>
                                    <!-- 부가결제율 -->
                                    <?php
                                        $useMileageRate = $order['useMileage'] / $order['totalGoodsPrice'] * 100;
                                    ?>
                                    <td class="text-right"><?= number_format($useMileageRate) ?>%</td>

                                    <!-- 결제금액 -->
                                    <td class="text-right"><?= number_format($order['salePrice']) ?></td>
                                    <!-- 결제율 -->
                                    <?php
                                        $paymentRate = $order['paymentRate'] ?? 0;
                                        if ($paymentRate <= 15) {
                                            $rateClass = 'text-red text-bold';
                                        } elseif ($paymentRate <= 50) {
                                            $rateClass = 'text-blue text-bold';
                                        } elseif ($paymentRate <= 85) {
                                            $rateClass = 'text-green text-bold';
                                        } else {
                                            $rateClass = '';
                                        }
                                    ?>
                                    <td class="text-right <?= $rateClass ?>"><?= $paymentRate ?>%</td>
                                    
                                    <td class="text-right"><?= number_format($order['refundPrice']) ?></td>
                                    <td class="text-right"><?= number_format($order['finalPrice']) ?></td>
                                    <td class="text-right"><?= number_format($order['totalCost']) ?></td>
                                    <td class="text-right"><?= number_format($order['marginAmount']) ?></td>

                                    <!-- 수익률 -->
                                    <?php
                                        $marginRate = $order['marginRate'] ?? 0;
                                        if ($marginRate <= 0) {
                                            $marginClass = 'text-red text-bold';
                                        } elseif ($marginRate <= 20) {
                                            $marginClass = 'text-blue text-bold';
                                        } elseif ($marginRate <= 35) {
                                            $marginClass = 'text-green text-bold';
                                        } else {
                                            $marginClass = '';
                                        }
                                    ?>
                                    <td class="text-right <?= $marginClass ?>"><?= $marginRate ?>%</td>
                                    
                                    <td class="text-center"><?= $order['goodsCnt'] ?></td>
                                    <td class="text-center"><?= $order['scm_my'] ?></td>

                                    <!-- 비매품 -->
                                    <td class="text-center">
                                        <?php if (($order['non_sale'] ?? 0) > 0) { ?>
                                            <?= $order['non_sale'] ?>
                                        <?php } ?>
                                    </td>

                                    <!-- 미보유 -->
                                    <td class="text-center">
                                        <?php if (($order['scm_not_my'] ?? 0) > 0) { ?>
                                            <?= $order['scm_not_my'] ?>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center"><?= $order['costGoodsCount'] ?></td>
                                </tr>
                                <tr id="goods_list_<?= $order['orderNo'] ?>" class="order_goods_list">
                                    <td colspan="100%">
                                        <table class="table-st1">
                                            <thead>
                                                <tr>
                                                    <th class="">No</th>
                                                    <th class="">종류</th>
                                                    <th class="">보유</th>
                                                    <th class="">상품보기</th>
                                                    <th class="">상품명</th>
                                                    <th class="">원가</th>
                                                    <th class="">수량</th>
                                                    <th class="">원가합계</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                    $goods_count = 0;
                                                    $goods_total_qty = 0;
                                                    $goods_total_cost_price_sum = 0;

                                                    foreach ($order['goods'] as $goods) {
                                                        $goods_count++;
                                                        $goods_total_qty += $goods['qty'];
                                                        $goods_total_cost_price_sum += $goods['cost_price_sum'];
                                                ?>
                                                    <tr>
                                                        <td><?= $goods_count ?></td>
                                                        <td><?= $goods['item_type'] == 'option' ? '옵션상품' : '주문상품' ?></td>
                                                        <td><?= $goods['is_owned'] ? '보유' : '미보유' ?></td>
                                                        <td>
                                                            <?php
                                                                if( $goods['is_owned'] == true ){
                                                            ?>
                                                                <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
                                                                    onclick="onlyAD.prdView('<?=$goods['cd_idx'] ?? ''?>','price');" >상품보기</button>
                                                            <?php }else{ ?>
                                                                <button type="button" id="" class="btnstyle1 btnstyle1-warning btnstyle1-xs" 
                                                                    onclick="onlyAD.prdProviderQuick('<?=$goods['pp_idx'] ?? ''?>');" >공급사상품</button>
                                                            <?php } ?>
                                                        
                                                        <td><?= $goods['goods_name'] ?></td>
                                                        <td class="text-right"><?= number_format($goods['cost_price']) ?></td>
                                                        <td class="text-center"><?= $goods['qty'] ?></td>
                                                        <td class="text-right"><?= number_format($goods['cost_price_sum']) ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="text-center" colspan="6">합계</th>
                                                    <th class="text-center"><?= $goods_total_qty ?></th>
                                                    <th class="text-right"><?= number_format($goods_total_cost_price_sum) ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>

                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">Total : <?= $no ?>건</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="text-center"><?= number_format($totalGoodsCnt) ?></th><!-- 총상품개수 -->
                                <th class="text-right"><?= number_format($totalGoodsPrice) ?></th><!-- 총상품금액 -->
                                <th class="text-right"><?= number_format($totalDcPrice) ?></th><!-- 총할인금액 -->

                                <?php
                                    $totalDcRate = $totalDcPrice / $totalGoodsPrice * 100;
                                ?>
                                <th class="text-right"><?= number_format($totalDcRate) ?>%</th><!-- 총할인율 -->


                                <th class="text-right"><?= number_format($totalUseMileage) ?></th><!-- 총부가결제 -->
                                <?php
                                    $totalUseMileageRate = $totalUseMileage / $totalGoodsPrice * 100;
                                ?>
                                <th class="text-right"><?= number_format($totalUseMileageRate) ?>%</th><!-- 총부가결제율 -->

                                <th class="text-right"><?= number_format($totalSalePrice) ?></th><!-- 총결제금액 -->
                                <?php
                                // 총결제율: 총결제금액 / 총상품금액 * 100
                                $totalPaymentRate = 0;
                                if ($totalGoodsPrice > 0) {
                                    $totalPaymentRate = $totalSalePrice / $totalGoodsPrice * 100;
                                }
                                ?>
                                <th class="text-right"><?= number_format($totalPaymentRate, 2) ?>%</th><!-- 총결제율 -->
                                <th class="text-right"><?= number_format($totalRefundPrice) ?></th><!-- 총환불금액 -->
                                <th class="text-right"><?= number_format($totalFinalPrice) ?></th><!-- 총정산금액 -->
                                <th class="text-right"><?= number_format($totalCost) ?></th><!-- 총원가 -->
                                <th class="text-right"><?= number_format($totalMargin) ?></th><!-- 총수익 -->
                                <?php
                                // 총수익률: 총수익 / 총결제금액 * 100
                                $avgMarginRate = 0;
                                if ($totalSalePrice > 0) {
                                    $avgMarginRate = $totalMargin / $totalSalePrice * 100;
                                }
                                ?>
                                <th class="text-right"><?= number_format($avgMarginRate, 2) ?>%</th><!-- 총수익률 -->
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <!-- 비매품 -->
                                <th class="text-center"><?= $totalNonSale ?></th>
                                <!-- 미보유 -->
                                <th class="text-center"> <?= $totalNotMy ?></th>
                                <th class="text-center"></th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </ul>
            <ul>
                <div class="scroll-wrap">
                    <table class="table-st1">
                        <thead>
                            <tr>
                                <th class="list-idx text-center">No</th>
                                <th class="" style="width: 100px;">에러내용</th>
                                <th class="">에러상세</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                                $scmMapping = [
                                    0  => ['name' => '오류', 'partner_key' => null, 'display'=>'none' ],
                                    1  => ['name' => '주식회사 디엔픽스', 'partner_key' => null, 'display'=>'none' ],
                                    2  => ['name' => '모브X', 'partner_key' => null, 'display'=>'none' ],
                                    3  => ['name' => '모브', 'partner_key' => 3],
                                    4  => ['name' => '공급사사입', 'partner_key' => null, 'display'=>'none'],
                                    5  => ['name' => '바니컴퍼니', 'partner_key' => 8],
                                    6  => ['name' => '바이담', 'partner_key' => 10],
                                    7  => ['name' => '해외직구', 'partner_key' => null, 'display'=>'none'],
                                    8  => ['name' => '그린쉘프', 'partner_key' => 12],
                                    9  => ['name' => '울컨코리아', 'partner_key' => 7],
                                    10 => ['name' => '모노프로', 'partner_key' => 11],
                                    11 => ['name' => '핑크에그', 'partner_key' => 9],
                                    12 => ['name' => '리퍼브', 'partner_key' => null, 'display'=>'none'],
                                    13 => ['name' => 'MSHb2b', 'partner_key' => 5],
                                    14 => ['name' => 'JPDOLL', 'partner_key' => 14],
                                    15 => ['name' => '도라토이', 'partner_key' => 6],
                                    16 => ['name' => '대형', 'partner_key' => null, 'display'=>'none'],
                                    17 => ['name' => '리퍼브', 'partner_key' => null, 'display'=>'none'],
                                    18 => ['name' => '랜덤박스', 'partner_key' => null, 'display'=>'none'],
                                    19 => ['name' => '예비1', 'partner_key' => null, 'display'=>'none'],
                                    20 => ['name' => '예비2', 'partner_key' => null, 'display'=>'none'],
                                ];

                                $errorCodeMapping = [
                                    'scm_code_not_match' => '상품 코드 공급사 상품 매칭 실패',
                                    'cost_price_not_found' => '원가 정보 없음',
                                    'product_data_not_found' => '상품 데이터 없음',
                                ];

                                $i = 0;

                                foreach ($orderList['errorList'] as $error) {
                                    $i++;

										$errorText = "";
										$errorDetail = "";

										if( empty($error['error_code']) ){
											$errorText = "( ".($error['code'] ?? '')." )"." 에러코드 없음";
											
											$orderGoods = $error['orderGoods'] ?? [];
											if (!is_array($orderGoods)) {
												$orderGoods = [];
											}
											
											$scmNo = $orderGoods['scmNo'] ?? '';
											$scmName = isset($scmMapping[$scmNo]['name']) ? $scmMapping[$scmNo]['name'] : "공급사이름 없음";
											
											$errorDetail = "주문번호 : <a href='http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=" . ($error['orderNo'] ?? '') . "' target='_blank'>" . ($error['orderNo'] ?? '') . "</a>"
                                                . "<br>주문일 : " . ($error['orderDate'] ?? '')
												. "<br>상품명 : " . ($orderGoods['goodsNm'] ?? "상품명 없음")
												. "<br>상품코드 : " . ($orderGoods['goodsCd'] ?? "상품코드 없음")
												. "<br>공급사번호 : " . ($orderGoods['scmNo'] ?? "공급사번호 없음")
												. "<br>공급사이름 : " . $scmName;	
			
										
										}elseif( ($error['error_code'] ?? '') == 'scm_code_not_match' ){
											$errorText = "( ".($error['goodsNo'] ?? '')." )"."<br>".($errorCodeMapping[$error['error_code']] ?? '');
											
											$orderGoods = $error['orderGoods'] ?? [];
											if (!is_array($orderGoods)) {
												$orderGoods = [];
											}
											
											$scmNo = $orderGoods['scmNo'] ?? '';
											$scmName = isset($scmMapping[$scmNo]['name']) ? $scmMapping[$scmNo]['name'] : "공급사이름 없음";
											
											$errorDetail = "주문번호 : <a href='http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=" . ($error['orderNo'] ?? '') . "' target='_blank'>" . ($error['orderNo'] ?? '') . "</a>"
                                                . "<br>주문일 : " . ($error['orderDate'] ?? '')
												. "<br>상품명 : " . ($orderGoods['goodsNm'] ?? "상품명 없음")
												. "<br>상품코드 : " . ($orderGoods['goodsCd'] ?? "상품코드 없음")
												. "<br>공급사번호 : " . ($orderGoods['scmNo'] ?? "공급사번호 없음")
												. "<br>공급사이름 : " . $scmName;	
										
										}elseif( ($error['error_code'] ?? '') == 'cost_price_not_found' ){
											
											$errorText = "( ".($error['code'] ?? '')." )"."<br>".($errorCodeMapping[$error['error_code']] ?? '');
											
											$dbGoods = $error['dbGoods'] ?? [];
											if (!is_array($dbGoods)) {
												$dbGoods = [];
											}
											
											$goodsNm = $dbGoods['goods_name'] ?? "상품명 없음";
											$goodsCd = $error['code'] ?? "상품코드 없음";
											$scmNo = $error['scmNo'] ?? null;

											$errorDetail = "주문번호 : <a href='http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=" . ($error['orderNo'] ?? '') . "' target='_blank'>" . ($error['orderNo'] ?? '') . "</a>"
                                                . "<br>주문일 : " . ($error['orderDate'] ?? '')
												. "<br>상품명 : " . $goodsNm
												. "<br>재고코드 : " . $goodsCd;

											if( !empty($scmNo) ){
												$scmName = isset($scmMapping[$scmNo]['name']) ? $scmMapping[$scmNo]['name'] : "공급사이름 없음";
												$errorDetail .= "<br>공급사번호 : ".$scmNo."<br>공급사이름 : <b>".$scmName."</b>";
											}

										}
                            ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td style="width: 100px !important; white-space: normal !important;">
                                        <p>
                                            <?= $errorText ?>
                                        </p>
                                        <?php
											if( ($error['error_code'] ?? '') == 'cost_price_not_found' ){
										?>
										<div>
											<?php
												if( ($error['mode'] ?? '') == 'stock' ){
											?>
												<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
												onclick="onlyAD.prdView('<?=$error['idx'] ?? ''?>','price');" >상품정보</button>
											<?php }else{ ?>
												<button type="button" id="" class="btnstyle1 btnstyle1-warning btnstyle1-xs" 
												onclick="onlyAD.prdProviderQuick('<?=$error['idx'] ?? ''?>');" >공급사상품</button>
											<?php } ?>

										</div>
										<?php } ?>
                                    </td>
                                    <td><?= $errorDetail ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </ul>
        </div>

    </div>
</div>

<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script>
    $(document).ready(function() {

        $('#search_btn').on('click', function() {
            var mode = $('#mode').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            location.href = "/admin/order/godo_order_list?mode=" + mode + "&start_date=" + start_date + "&end_date=" + end_date;
        });

        // 상품보기 토글
        $(document).on('click', '.order-toggle', function() {
            var target = $(this).data('target');
            var $targetRow = $(target);
            if ($targetRow.length === 0) {
                return;
            }
            $targetRow.toggle();
            if ($targetRow.is(':visible')) {
                $(this).text('상품보기 ▲');
            } else {
                $(this).text('상품보기 ▼');
            }
        });

    });
</script>