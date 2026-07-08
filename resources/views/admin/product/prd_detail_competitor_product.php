<?php
    $ourSalePrice = (int)($productData['cd_sale_price'] ?? 0);
    $ourCostPrice = (int)($productData['cd_cost_price'] ?? 0);
    $ourMarginGrade = trim((string)($productData['margin_grade'] ?? ''));

    $deliveryType = trim((string)($productData['delivery_type'] ?? 'small'));
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

    $calculateMarginInfo = function ($salePrice, $costPrice, $shippingFee) {
        $salePrice = (int)$salePrice;
        $costPrice = (int)$costPrice;
        $shippingFee = (int)$shippingFee;

        $marginAmount = $salePrice - $costPrice;
        if ($salePrice > 29999) {
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

    $minPrice = null;
    $maxPrice = null;
    if (!empty($rows) && is_array($rows)) {
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $rowPrice = (int)($row['price'] ?? 0);
            if ($minPrice === null || $rowPrice < $minPrice) {
                $minPrice = $rowPrice;
            }
            if ($maxPrice === null || $rowPrice > $maxPrice) {
                $maxPrice = $rowPrice;
            }
        }
    }
    if ($minPrice === null) {
        $minPrice = 0;
    }
    if ($maxPrice === null) {
        $maxPrice = 0;
    }

    $currentMarginInfo = $calculateMarginInfo($ourSalePrice, $ourCostPrice, $deliveryFee);
    $adjustedSalePrice = $minPrice;
    $adjustedMarginInfo = $calculateMarginInfo($adjustedSalePrice, $ourCostPrice, $deliveryFee);

    $priceDiff = $ourSalePrice - $minPrice;
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

<style>
    .competitor-product-wrap {
        padding: 0;
        background-color: #fff;
    }
    .competitor-report-box {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        background: #fafafa;
        margin-bottom: 10px;
    }
    .competitor-report-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .competitor-report-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .competitor-report-chip {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: #fff;
        padding: 6px 8px;
        font-size: 12px;
    }
    .competitor-adjust-box {
        margin-top: 10px;
        border-top: 1px dashed #d1d5db;
        padding-top: 10px;
        font-size: 12px;
        line-height: 1.6;
    }
</style>

<div class="competitor-report-box">
    <div class="competitor-report-title">경쟁사 판매 데이터 리포트</div>
    <div class="competitor-report-summary">
        <div class="competitor-report-chip">쑈당몰 판매가 : <b><?= number_format($ourSalePrice) ?></b> 원</div>
        <div class="competitor-report-chip">책정원가 : <b><?= number_format($ourCostPrice) ?></b> 원</div>
        <div class="competitor-report-chip">마진율 : <b><?= number_format((float)($currentMarginInfo['margin_rate'] ?? 0), 2) ?></b> %</div>
        <div class="competitor-report-chip">마진그룹 : <b><?= htmlspecialchars(($ourMarginGrade !== '' ? $ourMarginGrade : '-'), ENT_QUOTES, 'UTF-8') ?></b></div>
        <div class="competitor-report-chip">최저가 : <b><?= number_format($minPrice) ?></b> 원</div>
        <div class="competitor-report-chip">최고가 : <b><?= number_format($maxPrice) ?></b> 원</div>
    </div>

    <div class="competitor-adjust-box">
        <b>가격조정 (최저가 기준)</b><br>
        가격차이 :
        <span style="color:<?= $priceDiffColor ?>;">
            <b><?php if ($priceDiff > 0) { ?>+<?php } ?><?= number_format($priceDiff) ?></b>
        </span>
        <?= $priceDiffText ?><br>
        <?php if ($priceDiff !== 0) { ?>
            조정가 :
            <b style="color:<?= $adjustmentColor ?>;"><?= number_format($adjustedSalePrice) ?></b>
            (<span><?php if ($adjustmentAmount > 0) { ?>+<?php } ?><?= number_format($adjustmentAmount) ?></span>)<br>
            마진금 <b><?= number_format((int)$currentMarginInfo['margin_amount']) ?></b> → <b><?= number_format((int)$adjustedMarginInfo['margin_amount']) ?></b><br>
            마진율 <b><?= number_format((float)($currentMarginInfo['margin_rate'] ?? 0), 2) ?>% → <?= number_format((float)($adjustedMarginInfo['margin_rate'] ?? 0), 2) ?>%</b>
            / 그룹 <b><?= htmlspecialchars(($ourMarginGrade !== '' ? $ourMarginGrade : '-'), ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars(($adjustedMarginInfo['grade'] !== '' ? $adjustedMarginInfo['grade'] : '-'), ENT_QUOTES, 'UTF-8') ?></b>
        <?php } ?>
    </div>
</div>

<div class="competitor-product-wrap">
    <div class="table-wrap5">
        <table class="table-st1">
            <thead>
            <tr>
                <th style="width:80px;">사이트</th>
                <th style="width:120px;">판매상태</th>
                <th style="width:70px;">이미지</th>
                <th>상품명</th>
                <th style="width:110px;">판매가</th>
                <th style="width:130px;">수정일</th>
                <th style="width:110px;">사이트 PK</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($rows)) { ?>
                <?php foreach ($rows as $row) { ?>
                    <tr>
                        <td class="text-center">
                            <?php
                                $rowSiteCode = (string)($row['site'] ?? '');
                                $rowSiteInfo = $competitor_data[$rowSiteCode] ?? null;
                                $rowSiteName = is_array($rowSiteInfo) ? (string)($rowSiteInfo['name'] ?? '') : '';
                            ?>
                            <div><?= htmlspecialchars($rowSiteCode, ENT_QUOTES, 'UTF-8') ?></div>
                            <?php if ($rowSiteName !== '') { ?>
                                <div style="font-size:11px; color:#6b7280;"><b><?= htmlspecialchars($rowSiteName, ENT_QUOTES, 'UTF-8') ?></b></div>
                            <?php } ?>
                        </td>
                        <td class="text-center">
                            <?php
                                $saleStatus = trim((string)($row['sale_status'] ?? ''));
                                $saleStatusStyle = ($saleStatus === '품절') ? 'color:#dc2626; font-weight:700;' : '';
                            ?>
                            <span style="<?= $saleStatusStyle ?>"><?= htmlspecialchars($saleStatus, ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td class="text-center">
                            <?php if (!empty($row['image_url'])) { ?>
                                <a href="javascript:goCompetitorProductEdit('<?= $row['site'] ?>', '<?= $row['prd_pk'] ?>');"><img src="<?= htmlspecialchars((string)$row['image_url'], ENT_QUOTES, 'UTF-8') ?>" style="width:56px; height:56px; object-fit:cover; border:1px solid #eee;"></a>
                            <?php } ?>
                        </td>
                        <td style="white-space:normal;"><b><a href="javascript:goCompetitorProductEdit('<?= $row['site'] ?>', '<?= $row['prd_pk'] ?>');"><?= htmlspecialchars((string)($row['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></a></b></td>
                        <td class="text-right">
                            <?php
                                $rowPrice = (int)($row['price'] ?? 0);
                                $isMinPrice = ($rowPrice === (int)$minPrice);
                                $isMaxPrice = ($rowPrice === (int)$maxPrice);
                                $isSameAsOurPrice = ($rowPrice === (int)$ourSalePrice);
                            ?>
                            <?php if ($isMinPrice || $isMaxPrice || $isSameAsOurPrice) { ?>
                                <div style="margin-bottom:2px;">
                                    <?php if ($isMinPrice) { ?>
                                        <span style="display:inline-block; font-size:11px; line-height:1.2; padding:1px 5px; border-radius:10px; background:#dc2626; color:#fff; margin-left:4px;">최저가</span>
                                    <?php } ?>
                                    <?php if ($isMaxPrice) { ?>
                                        <span style="display:inline-block; font-size:11px; line-height:1.2; padding:1px 5px; border-radius:10px; background:#16a34a; color:#fff; margin-left:4px;">최고가</span>
                                    <?php } ?>
                                    <?php if ($isSameAsOurPrice) { ?>
                                        <span style="display:inline-block; font-size:11px; line-height:1.2; padding:1px 5px; border-radius:10px; background:#6b7280; color:#fff; margin-left:4px;">동일가</span>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <b><?= number_format($rowPrice) ?></b>
                        </td>
                        <td class="text-center">
                            <?php if (!empty($row['updated_at'])) { ?>
                                <?= date('Y.m.d H:i', strtotime((string)$row['updated_at'])) ?>
                            <?php } ?>
                        </td>
                        <td class="text-center">#<?= (int)($row['prd_pk'] ?? 0) ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="7" class="text-center">매칭된 경쟁사 판매 데이터가 없습니다.</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>