<?php
    $rowSiteCode = (string)($row['site'] ?? '');
    $rowSiteInfo = $competitor_data[$rowSiteCode] ?? null;
    $rowSiteName = is_array($rowSiteInfo) ? (string)($rowSiteInfo['name'] ?? '') : '';
    $matchCellSalePrice = (int)($row['price'] ?? 0);
    $matchCellShippingMethod = is_array($rowSiteInfo) ? (string)($rowSiteInfo['shipping_method'] ?? '') : '';
    $matchCellShippingFee = is_array($rowSiteInfo) ? (int)($rowSiteInfo['shipping_fee'] ?? 0) : 0;
    $matchCellFreeShippingThreshold = is_array($rowSiteInfo) ? (int)($rowSiteInfo['free_shipping_threshold'] ?? 0) : 0;
    $matchCellIsFreeShipping = $matchCellShippingMethod === '금액별무료배송'
        && $matchCellFreeShippingThreshold > 0
        && $matchCellSalePrice >= $matchCellFreeShippingThreshold;
    $finalPrice = $matchCellIsFreeShipping
        ? $matchCellSalePrice
        : $matchCellSalePrice + $matchCellShippingFee;
?>
                            <?php
                                $rowPrimaryMatchIdx = (int)($row['primary_match_idx'] ?? ($row['match_idx'] ?? 0));
                                $rowMatchedItems = $row['matched_items'] ?? [];
                                if (is_string($rowMatchedItems)) {
                                    $decodedMatchedItems = json_decode($rowMatchedItems, true);
                                    $rowMatchedItems = is_array($decodedMatchedItems) ? $decodedMatchedItems : [];
                                }
                                if (!is_array($rowMatchedItems)) {
                                    $rowMatchedItems = [];
                                }
                                $rowMatchedCdIdxList = [];
                                foreach ($rowMatchedItems as $rowMatchedItem) {
                                    if (!is_array($rowMatchedItem)) {
                                        continue;
                                    }
                                    $matchedCdIdx = (int)($rowMatchedItem['cd_idx'] ?? 0);
                                    if ($matchedCdIdx > 0) {
                                        $rowMatchedCdIdxList[] = $matchedCdIdx;
                                    }
                                }
                                if ($rowPrimaryMatchIdx > 0 && !in_array($rowPrimaryMatchIdx, $rowMatchedCdIdxList, true)) {
                                    $rowMatchedCdIdxList[] = $rowPrimaryMatchIdx;
                                }
                                $rowMatchedCdIdxList = array_values(array_unique($rowMatchedCdIdxList));
                                $rowMatchedCount = count($rowMatchedCdIdxList);
                                $rowMatchedCdIdxCsv = implode(',', $rowMatchedCdIdxList);
                                $rowMatchSourceList = [];
                                $rowMatchSource = strtolower(trim((string)($row['match_source'] ?? '')));
                                if ($rowMatchSource !== '') {
                                    $rowMatchSourceList[] = $rowMatchSource;
                                }
                                foreach ($rowMatchedItems as $rowMatchedItem) {
                                    if (!is_array($rowMatchedItem)) {
                                        continue;
                                    }
                                    $itemMatchSource = strtolower(trim((string)($rowMatchedItem['match_source'] ?? '')));
                                    if ($itemMatchSource !== '') {
                                        $rowMatchSourceList[] = $itemMatchSource;
                                    }
                                }
                                $rowMatchSourceList = array_values(array_unique($rowMatchSourceList));
                            ?>
                            <td class="text-left competitor-match-cell">
                                <button
                                    type="button"
                                    class="btnstyle1 btnstyle1-info btnstyle1-sm competitor-product-match-btn"
                                    data-competitor-idx="<?= (int)($row['idx'] ?? 0) ?>"
                                    data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>"
                                    data-competitor-image="<?= htmlspecialchars((string)($row['image_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-competitor-name="<?= htmlspecialchars((string)($row['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-competitor-brand-name="<?= htmlspecialchars((string)($row['brand_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-existing-match-idx-list="<?= htmlspecialchars($rowMatchedCdIdxCsv, ENT_QUOTES, 'UTF-8') ?>"
                                    data-primary-match-idx="<?= $rowPrimaryMatchIdx ?>"
                                    data-competitor-price="<?= (int)($row['price'] ?? 0) ?>"
                                    data-competitor-detail-url="<?= htmlspecialchars((string)($row['detail_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <?= $rowMatchedCount > 0 ? ('매칭관리(' . number_format($rowMatchedCount) . ')') : '매칭' ?>
                                </button>
                                <?php if ($rowMatchedCount > 0) { ?>
                                    <div class="m-t-3">
                                        <span class="text-green">매칭완료</span>
                                    </div>
                                <?php } ?>
                                <?php if (in_array('auto', $rowMatchSourceList, true)) { ?>
                                    <div class="m-t-3">
                                        <span style="display:inline-block; padding:1px 6px; border-radius:3px; background:#d4380d; color:#fff; font-size:11px; font-weight:700;">자동매칭</span>
                                    </div>
                                <?php } ?>
                                <?php if (in_array('manual', $rowMatchSourceList, true)) { ?>
                                    <div class="m-t-3" style="font-size:12px; color:#98a2b3;">직접매칭</div>
                                <?php } ?>
                                <?php if (in_array('skip', $rowMatchSourceList, true)) { ?>
                                    <div class="m-t-3" style="font-size:12px; color:#98a2b3;">skip</div>
                                <?php } ?>
                            </td>

                            <td class="text-left competitor-matched-product-cell">
                                <?php
                                    $displayMatchedCdIdxList = $rowMatchedCdIdxList;
                                    if (empty($displayMatchedCdIdxList) && $rowPrimaryMatchIdx > 0) {
                                        $displayMatchedCdIdxList[] = $rowPrimaryMatchIdx;
                                    }
                                    $displayMatchedCdIdxList = array_values(array_unique(array_map('intval', $displayMatchedCdIdxList)));
                                ?>
                                <?php if( !empty($displayMatchedCdIdxList) ): ?>
                                    <?php if (count($displayMatchedCdIdxList) > 1) { ?>
                                        <div class="m-b-4">
                                            <button
                                                type="button"
                                                class="btnstyle1 btnstyle1-danger btnstyle1-xs competitor-product-unmatch-all-btn"
                                                data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>">
                                                전체 해지
                                            </button>
                                        </div>
                                    <?php } ?>
                                    <?php foreach ($displayMatchedCdIdxList as $displayIdx => $matchedIdx) { ?>
                                        <?php
                                            $matchedProduct = $matchedProductMap[(int)$matchedIdx] ?? null;
                                            if (empty($matchedProduct)) {
                                                continue;
                                            }
                                            $isPrimary = ((int)$matchedIdx === (int)$rowPrimaryMatchIdx);
                                            if ((int)$rowPrimaryMatchIdx <= 0 && $displayIdx === 0) {
                                                $isPrimary = true;
                                            }
                                            $ourSalePrice = (int)($matchedProduct['cd_sale_price'] ?? 0);
                                            $ourCostPrice = (int)($matchedProduct['cd_cost_price'] ?? 0);
                                            $ourMarginGrade = trim((string)($matchedProduct['margin_grade'] ?? ''));
                                            $competitorPrice = (int)($row['price'] ?? 0);
                                            $competitorFinalPrice = (int)$finalPrice;
                                            $ourFinalPrice = $ourSalePrice > 30000
                                                ? $ourSalePrice
                                                : $ourSalePrice + 2500;
                                            $adjustedSalePrice = $competitorFinalPrice > 30000
                                                ? $competitorFinalPrice
                                                : max(0, $competitorFinalPrice - 2500);

                                            $deliveryType = trim((string)($matchedProduct['delivery_type'] ?? 'small'));
                                            if ($deliveryType === 'tiny_80') {
                                                $deliveryType = 'tiny';
                                            }
                                            $deliveryFeeMap = [
                                                'tiny' => 2300,
                                                'small' => 2800,
                                                'medium' => 3300,
                                                'large' => 5000,
                                                'xlarge' => 5400,
                                            ];
                                            $deliveryFee = (int)($deliveryFeeMap[$deliveryType] ?? 2800);

                                            $calculateMarginInfo = function ($salePrice, $costPrice, $shippingFee, $useShippingDeduction = null) {
                                                $salePrice = (int)$salePrice;
                                                $costPrice = (int)$costPrice;
                                                $shippingFee = (int)$shippingFee;
                                                $shouldDeductShipping = is_bool($useShippingDeduction)
                                                    ? $useShippingDeduction
                                                    : ($salePrice > 29999);

                                                $marginAmount = $salePrice - $costPrice;
                                                if ($shouldDeductShipping) {
                                                    $marginAmount = $salePrice - ($costPrice + $shippingFee);
                                                }
                                                $marginRate = 0;
                                                if ($salePrice > 0) {
                                                    $marginRate = round(($marginAmount / $salePrice) * 100, 2);
                                                }

                                                $grade = '';
                                                if ($marginRate > 39) $grade = 'A';
                                                else if ($marginRate >= 35) $grade = 'B';
                                                else if ($marginRate >= 30) $grade = 'C';
                                                else if ($marginRate >= 25) $grade = 'D';
                                                else if ($marginRate >= 20) $grade = 'E';
                                                else if ($marginRate >= 15) $grade = 'F';
                                                else if ($marginRate >= 10) $grade = 'G';
                                                else if ($marginRate >= 5) $grade = 'H';
                                                else if ($marginRate > 0) $grade = 'I';

                                                return [
                                                    'margin_amount' => $marginAmount,
                                                    'margin_rate' => $marginRate,
                                                    'grade' => $grade,
                                                ];
                                            };

                                            // 비교 구간에서는 배송비 차감 기준을 현재 판매가 기준으로 고정해
                                            // 가격을 내렸을 때 마진이 역전 증가하는 케이스를 방지한다.
                                            $baseUseShippingDeduction = ($ourSalePrice > 29999);
                                            $currentMarginInfo = $calculateMarginInfo($ourSalePrice, $ourCostPrice, $deliveryFee, $baseUseShippingDeduction);
                                            $adjustedMarginInfo = $calculateMarginInfo($adjustedSalePrice, $ourCostPrice, $deliveryFee, $baseUseShippingDeduction);

                                            $priceDiff = $ourFinalPrice - $competitorFinalPrice;
                                            $priceDiffColor = '#111827';
                                            $priceDiffText = '동일';
                                            if ($priceDiff > 0) {
                                                $priceDiffColor = '#dc2626';
                                                $priceDiffText = '높음';
                                            } else if ($priceDiff < 0) {
                                                $priceDiffColor = '#2563eb';
                                                $priceDiffText = '낮음';
                                            }

                                            $adjustmentAmount = $adjustedSalePrice - $ourSalePrice;
                                            $adjustmentColor = '#111827';
                                            if ($adjustmentAmount < 0) {
                                                $adjustmentColor = '#dc2626';
                                            } else if ($adjustmentAmount > 0) {
                                                $adjustmentColor = '#2563eb';
                                            }
                                        ?>
                                        <div style="display:flex; align-items:flex-start; gap:7px; margin-top:<?= $displayIdx > 0 ? '10px' : '0' ?>; padding-top:<?= $displayIdx > 0 ? '10px' : '0' ?>; border-top:<?= $displayIdx > 0 ? '1px dashed #e5e7eb' : '0' ?>;">
                                            <div style="width:60px; min-width:60px;">
                                                <?php if( !empty($matchedProduct['img_path']) ){ ?>
                                                    <img src="<?= htmlspecialchars((string)($matchedProduct['img_path'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:60px; height:60px; object-fit:cover; border:1px solid #eee !important;">
                                                <?php } ?>
                                            </div>
                                            <div style="width:178px; min-width:178px; max-width:178px;">
                                                <div style="display:flex; align-items:center; justify-content:space-between; gap:6px;">
                                                    <span style="color:#6b7280;">브랜드 : <?= htmlspecialchars((string)($matchedProduct['brand_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span>
                                                    <?php if ($rowMatchedCount > 1 && $isPrimary) { ?><b style="color:#2563eb; font-size:11px;">대표</b><?php } ?>
                                                </div>
                                                <div class="m-t-3" style="font-size:12px; white-space:normal;">
                                                    <b><?= htmlspecialchars(str_replace(['&middot;', '&#183;', '&#xB7;', '·'], '', (string)($matchedProduct['CD_NAME'] ?? '')), ENT_QUOTES, 'UTF-8') ?></b>
                                                </div>
                                                <div class="m-t-3" style="font-size:12px; display:flex; align-items:center; justify-content:space-between; gap:6px;">
                                                    <span style="display:inline-flex; align-items:center; gap:10px; white-space:nowrap;">
                                                        <span>판매가 : <b><?= number_format((int)($matchedProduct['cd_sale_price'] ?? 0)) ?></b></span>
                                                        <?php if ((int)($matchedProduct['cd_sale_price'] ?? 0) > 30000) { ?>
                                                            <span style="display:inline-block; padding:1px 4px; border-radius:3px; background:#16a34a; color:#fff; font-size:11px;">무배</span>
                                                        <?php } else { ?>
                                                            <span style="color:#6b7280;">최종가 : <b><?= number_format((int)($matchedProduct['cd_sale_price'] ?? 0) + 2500) ?></b></span>
                                                        <?php } ?>
                                                    </span>
                                                </div>
                                                <div class="m-t-3">
                                                    <span style="color:<?= (int)($matchedProduct['stock_qty'] ?? 0) !== 0 ? '#2563eb' : '#6b7280' ?>;">재고 : <b><?= number_format((int)($matchedProduct['stock_qty'] ?? 0)) ?></b></span>
                                                </div>
                                                <div class="m-t-3" style="display:flex; align-items:center; gap:4px; flex-wrap:wrap;">
                                                    <button type="button" class="btnstyle1 btnstyle1-xs"
                                                        onclick="onlyAD.prdView('<?= (int)($matchedProduct['CD_IDX'] ?? 0) ?>','info');">#<?=$matchedProduct['CD_IDX'] ?? ''?> 상품보기</button>
                                                    <?php if (!$isPrimary) { ?>
                                                        <button
                                                            type="button"
                                                            class="btnstyle1 btnstyle1-xs competitor-product-set-primary-btn"
                                                            data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                            data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>"
                                                            data-match-idx="<?= (int)$matchedIdx ?>">
                                                            대표지정
                                                        </button>
                                                    <?php } ?>
                                                    <button
                                                        type="button"
                                                        class="btnstyle1 btnstyle1-danger btnstyle1-xs competitor-product-unmatch-one-btn"
                                                        data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                        data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>"
                                                        data-match-idx="<?= (int)$matchedIdx ?>">
                                                        해지
                                                    </button>
                                                </div>
                                            </div>
                                            <div style="border-left:1px solid #e5e7eb; padding-left:10px; margin-left:3px;">
                                                원가 : <?php if ($ourCostPrice > 0) { ?><b><?= number_format($ourCostPrice) ?></b><?php } else { ?>원가정보가 없습니다<?php } ?><br>
                                                <?php if ($ourCostPrice > 0) { ?>
                                                    마진그룹 : 마진율 <b><?= number_format((float)($currentMarginInfo['margin_rate'] ?? 0), 2) ?>%</b> / 그룹 <b><?= htmlspecialchars(($ourMarginGrade !== '' ? $ourMarginGrade : '-'), ENT_QUOTES, 'UTF-8') ?></b><br>
                                                    <?php if ($competitorFinalPrice > 0) { ?>
                                                        최종가격차이 :
                                                        <span style="color:<?= $priceDiffColor ?>;">
                                                            <b>
                                                                <?php if ($priceDiff > 0) { ?>+<?php } ?>
                                                                <?= number_format($priceDiff) ?>
                                                            </b>
                                                        </span>
                                                        <?= $priceDiffText ?><br>
                                                        <?php if ($priceDiff !== 0) { ?>

                                                            <div>
                                                                조정가 :
                                                                <b style="color:<?= $adjustmentColor ?>;"><?= number_format($adjustedSalePrice) ?></b>
                                                                (<span><?php if ($adjustmentAmount > 0) { ?>+<?php } ?><?= number_format($adjustmentAmount) ?></span>)
                                                                <?php if( $matchedProduct['cd_godo_code'] !== '' ){ ?>
                                                                    <button
                                                                        type="button"
                                                                        class="btnstyle1 btnstyle1-danger btnstyle1-xs competitor-product-adjust-price-btn"
                                                                        data-prd-idx="<?= (int)($matchedProduct['CD_IDX'] ?? 0) ?>"
                                                                        data-adjusted-sale-price="<?= (int)$adjustedSalePrice ?>"
                                                                        data-cost-price="<?= (int)$ourCostPrice ?>"
                                                                        data-godo-code="<?= htmlspecialchars((string)($matchedProduct['cd_godo_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                                        data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                                        data-competitor-site-name="<?= htmlspecialchars($rowSiteName, ENT_QUOTES, 'UTF-8') ?>"
                                                                        data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>"
                                                                        data-competitor-name="<?= htmlspecialchars((string)($row['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                                        data-competitor-price="<?= (int)$competitorPrice ?>"
                                                                        data-competitor-final-price="<?= (int)$competitorFinalPrice ?>"
                                                                        data-competitor-detail-url="<?= htmlspecialchars((string)($row['detail_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                                                        이가격으로 조정
                                                                    </button>
                                                                <?php } ?>
                                                            </div>


                                                            마진금 <b><?= number_format((int)$currentMarginInfo['margin_amount']) ?></b> → <b><?= number_format((int)$adjustedMarginInfo['margin_amount']) ?></b> <br>
                                                            마진율 <b><?= number_format((float)($currentMarginInfo['margin_rate'] ?? 0), 2) ?>% → <?= number_format((float)($adjustedMarginInfo['margin_rate'] ?? 0), 2) ?>%</b> / 그룹 <b><?= htmlspecialchars(($ourMarginGrade !== '' ? $ourMarginGrade : '-'), ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars(($adjustedMarginInfo['grade'] !== '' ? $adjustedMarginInfo['grade'] : '-'), ENT_QUOTES, 'UTF-8') ?></b>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
