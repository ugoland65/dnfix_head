<?php
$saleHistory = $saleHistory ?? [];
$productList = $saleHistory['product_list'] ?? [];
if (!is_array($productList)) {
    $productList = [];
}
if (isset($productList['products']) && is_array($productList['products'])) {
    $productList = $productList['products'];
} elseif (isset($productList['data']) && is_array($productList['data'])) {
    $productList = $productList['data'];
}
$productList = array_values(array_filter($productList, static function ($item) {
    return is_array($item);
}));

$normalizeProductItem = static function ($item): array {
    if (is_object($item)) {
        $item = (array)$item;
    }
    if (!is_array($item)) {
        return [];
    }

    // 레거시/변형 저장구조 보정
    $candidates = ['item', 'product', 'row', 'data'];
    foreach ($candidates as $key) {
        if (isset($item[$key])) {
            $nested = $item[$key];
            if (is_object($nested)) {
                $nested = (array)$nested;
            }
            if (is_array($nested)) {
                $item = array_merge($nested, $item);
                break;
            }
        }
    }

    // 필드명 변형 통합
    if (!isset($item['godo_goods_no']) || trim((string)$item['godo_goods_no']) === '') {
        $item['godo_goods_no'] = (string)($item['godo_goodsNo'] ?? ($item['godoNo'] ?? ''));
    }
    if (!isset($item['prd_name']) || trim((string)$item['prd_name']) === '') {
        $item['prd_name'] = (string)($item['name'] ?? '');
    }
    if (!isset($item['img_path']) || trim((string)$item['img_path']) === '') {
        $item['img_path'] = (string)($item['img_src'] ?? ($item['CD_IMG'] ?? ''));
    }
    if (!isset($item['cd_kind_code']) || trim((string)$item['cd_kind_code']) === '') {
        $item['cd_kind_code'] = (string)($item['kind'] ?? ($item['CD_KIND_CODE'] ?? ''));
    }
    if (!isset($item['supplier_site'])) {
        $item['supplier_site'] = '';
    }
    if (!isset($item['supplier_2nd_name'])) {
        $item['supplier_2nd_name'] = '';
    }

    return $item;
};

$productList = array_values(array_filter(array_map($normalizeProductItem, $productList), static function ($item) {
    return is_array($item) && !empty($item);
}));

$metaData = [];
$metaRaw = $saleHistory['meta_json'] ?? '';
if (is_array($metaRaw)) {
    $metaData = $metaRaw;
} elseif (is_string($metaRaw) && trim($metaRaw) !== '') {
    $decoded = json_decode($metaRaw, true);
    if (is_array($decoded)) {
        $metaData = $decoded;
    }
}

$sourceCounts = $metaData['source_counts'] ?? [];
$totalCount = (int)($sourceCounts['total'] ?? count($productList));
$haveCount = (int)($sourceCounts['have'] ?? 0);
$providerCount = (int)($sourceCounts['provider'] ?? 0);
if ($haveCount === 0 && $providerCount === 0 && !empty($productList)) {
    foreach ($productList as $row) {
        if (($row['item_source'] ?? '') === 'provider') {
            $providerCount++;
        } else {
            $haveCount++;
        }
    }
}

$number = static function ($value): string {
    return number_format((float)$value);
};

$percent = static function ($value): string {
    $num = is_numeric($value) ? (float)$value : 0.0;
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.') . '%';
};

$text = static function ($value, string $fallback = '-'): string {
    $str = trim((string)$value);
    return $str === '' ? $fallback : $str;
};

$discountGroupedProducts = [];
foreach ($productList as $item) {
    $rawRate = $item['discount_rate'] ?? 0;
    $rateNum = is_numeric($rawRate) ? (float)$rawRate : 0.0;
    $rateKey = rtrim(rtrim(number_format($rateNum, 2, '.', ''), '0'), '.');
    if ($rateKey === '') {
        $rateKey = '0';
    }
    if (!isset($discountGroupedProducts[$rateKey])) {
        $discountGroupedProducts[$rateKey] = [];
    }
    $discountGroupedProducts[$rateKey][] = $item;
}
if (!empty($discountGroupedProducts)) {
    uksort($discountGroupedProducts, static function ($a, $b) {
        return (float)$b <=> (float)$a;
    });
}
$discountRateOptions = [];
foreach (array_keys($discountGroupedProducts) as $rateKey) {
    $discountRateOptions[(string)$rateKey] = (string)$rateKey;
}
foreach (['30', '25', '20', '15', '10', '5'] as $baseRate) {
    $discountRateOptions[$baseRate] = $baseRate;
}
uksort($discountRateOptions, static function ($a, $b) {
    return ((float)$b <=> (float)$a);
});

$groupUploadStatusMap = [];
$rawGroupUploadStatusMap = $metaData['godo_group_upload_status'] ?? [];
if (is_array($rawGroupUploadStatusMap)) {
    foreach ($rawGroupUploadStatusMap as $rateKey => $statusRow) {
        if (!is_array($statusRow)) {
            continue;
        }
        $normalizedRate = rtrim(rtrim(number_format((float)$rateKey, 2, '.', ''), '0'), '.');
        if ($normalizedRate === '') {
            $normalizedRate = rtrim(rtrim(number_format((float)($statusRow['discount_rate'] ?? 0), 2, '.', ''), '0'), '.');
        }
        if ($normalizedRate === '') {
            continue;
        }
        $groupUploadStatusMap[$normalizedRate] = $statusRow;
    }
}
$groupTotalCount = count($discountGroupedProducts);
$groupRegisteredCount = 0;
foreach (array_keys($discountGroupedProducts) as $rateKey) {
    $status = (string)(($groupUploadStatusMap[$rateKey]['status'] ?? ''));
    if ($status === 'success') {
        $groupRegisteredCount++;
    }
}
$groupRemainingCount = max($groupTotalCount - $groupRegisteredCount, 0);
?>

<style>
    .sale-detail-summary-box { background:#f8f9fb; border:1px solid #e5e7eb; border-radius:8px; padding:14px; margin-top:10px; }
    .sale-detail-summary-row { display:flex; flex-wrap:wrap; gap:10px 16px; align-items:center; }
    .sale-detail-badge { display:inline-block; padding:3px 8px; border-radius:999px; font-size:12px; font-weight:700; }
    .sale-detail-badge-have { background:#e7f1ff; color:#0d6efd; }
    .sale-detail-badge-provider { background:#e8f7ee; color:#198754; }
    .sale-detail-meta-grid { display:grid; grid-template-columns:repeat(2, minmax(260px, 1fr)); gap:10px; margin-top:12px; }
    .sale-detail-meta-card { border:1px solid #e5e7eb; border-radius:8px; background:#fff; padding:10px 12px; }
    .sale-detail-meta-title { font-weight:700; margin-bottom:8px; }
    .sale-detail-meta-line { font-size:12px; color:#374151; margin-bottom:4px; }
    .sale-detail-item-label { display:inline-block; padding:3px 7px; border-radius:4px; font-size:11px; font-weight:700; }
    .sale-detail-item-label-have { background:#e7f1ff; color:#0d6efd; }
    .sale-detail-item-label-provider { background:#e8f7ee; color:#198754; }
    .sale-detail-warning { color:#dc3545; font-weight:700; }
    .sale-detail-img-link { display:inline-block; cursor:pointer; }
    .sale-detail-img { height:50px; border:1px solid #eee !important; background:#fff; }
    .sale-detail-name-link { cursor:pointer; text-decoration:underline; text-underline-offset:2px; font-weight:700; }
    .sale-detail-action-wrap { display:flex; flex-wrap:wrap; gap:4px; justify-content:center; }
    .sale-detail-view-mode-wrap { margin-top:12px; display:flex; align-items:center; gap:8px; }
    .sale-detail-group-wrap { margin-top:10px; }
    .sale-detail-group-box { border:1px solid #e5e7eb; border-radius:8px; margin-bottom:14px; overflow:hidden; background:#fff; }
    .sale-detail-group-head { padding:10px 12px; background:#f3f4f6; border-bottom:1px solid #e5e7eb; font-weight:700; display:flex; justify-content:space-between; align-items:center; }
    .sale-detail-group-title { color:#111827; }
    .sale-detail-group-count { color:#374151; font-size:12px; }
    .sale-detail-group-right { display:flex; align-items:center; gap:8px; }
    .sale-detail-group-status { font-size:12px; color:#374151; }
    .sale-detail-group-status-ok { color:#198754; font-weight:700; }
    .sale-detail-group-status-fail { color:#dc3545; font-weight:700; }
    .sale-detail-group-meta { font-size:11px; color:#6b7280; }
</style>

<div id="contents_head">
    <h1>할인 이력 상세</h1>
    <div class="head-btn-wrap">
        <button type="button" class="btn btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="location.href='/admin/sale/history'">
            <i class="fas fa-list"></i> 목록
        </button>
        <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm m-l-5" onclick="location.href='/admin/sale/history/create'">
            <i class="fas fa-plus"></i> 신규 생성
        </button>
    </div>
</div>

<div id="contents_body">
    <div id="contents_body_wrap">

        <div class="sale-detail-summary-box">
            <div class="sale-detail-summary-row">
                <span><b>SEQ</b> : <?= (int)($saleHistory['seq'] ?? 0) ?></span>
                <span><b>상태</b> : <?= $text($saleHistory['sale_status_text'] ?? '', '-') ?></span>
                <span><b>모드</b> : <?= $text($saleHistory['sale_mode_text'] ?? '', '-') ?></span>
                <span><b>기간</b> : <?= $text(($saleHistory['sale_start_date'] ?? ''), '-') ?> ~ <?= $text(($saleHistory['sale_end_date'] ?? ''), '-') ?></span>
                <span><b>등록자</b> : <?= $text($saleHistory['created_by_name'] ?? '', '-') ?></span>
                <span><b>등록일</b> : <?= $text($saleHistory['created_at'] ?? '', '-') ?></span>
            </div>
            <div class="sale-detail-summary-row m-t-8">
                <span><b>총 상품</b> : <?= $number($totalCount) ?>건</span>
                <span class="sale-detail-badge sale-detail-badge-have">보유상품 <?= $number($haveCount) ?>건</span>
                <span class="sale-detail-badge sale-detail-badge-provider">위탁상품 <?= $number($providerCount) ?>건</span>
            </div>
        </div>

        <?php if ((string)($saleHistory['sale_status'] ?? '') === 'wait') { ?>
            <div class="sale-detail-view-mode-wrap">
                <button
                    type="button"
                    id="register_godo_time_sale_btn"
                    class="btn btnstyle1 btnstyle1-primary btnstyle1-sm"
                    data-seq="<?= (int)($saleHistory['seq'] ?? 0) ?>"
                    data-remaining-count="<?= (int)$groupRemainingCount ?>"
                >
                    전체 그룹 등록
                </button>
                <span style="font-size:12px; color:#6b7280;">
                    sale_status = wait 상태에서 할인율 그룹 등록을 수행합니다. (완료 그룹 자동 제외: <?= (int)$groupRegisteredCount ?>/<?= (int)$groupTotalCount ?>)
                </span>
            </div>
        <?php } ?>
        <?php if ((string)($saleHistory['sale_status'] ?? '') === 'upload' && !empty($saleHistory['uploaded_at'])) { ?>
            <div class="sale-detail-view-mode-wrap">
                <span style="font-size:12px; color:#2563eb;">
                    업로드 등록시간 : <b><?= htmlspecialchars($text($saleHistory['uploaded_at'] ?? '', '-'), ENT_QUOTES, 'UTF-8') ?></b>
                    / 등록자 : <b><?= htmlspecialchars($text($saleHistory['uploaded_by_name'] ?? ($saleHistory['uploaded_by'] ?? ''), '-'), ENT_QUOTES, 'UTF-8') ?></b>
                </span>
            </div>
        <?php } ?>


        <div class="sale-detail-meta-grid">
            <div class="sale-detail-meta-card">
                <div class="sale-detail-meta-title">저장된 할인 설정</div>
                <?php $saleSetting = $metaData['sale_setting'] ?? []; ?>
                <div class="sale-detail-meta-line">할인모드 : <?= $text($saleSetting['sale_mode'] ?? ($saleHistory['sale_mode'] ?? ''), '-') ?></div>
                <div class="sale-detail-meta-line">시작일시 : <?= $text($saleSetting['sale_start_date'] ?? ($saleHistory['sale_start_date'] ?? ''), '-') ?> <?= $text($saleSetting['sale_start_time'] ?? '', '') ?></div>
                <div class="sale-detail-meta-line">종료일시 : <?= $text($saleSetting['sale_end_date'] ?? ($saleHistory['sale_end_date'] ?? ''), '-') ?> <?= $text($saleSetting['sale_end_time'] ?? '', '') ?></div>
                <div class="sale-detail-meta-line">저장시각 : <?= $text($metaData['saved_at'] ?? '', '-') ?></div>
            </div>
            <div class="sale-detail-meta-card">
                <div class="sale-detail-meta-title">저장된 랜덤 조건</div>
                <?php $cond = $metaData['random_condition'] ?? []; ?>
                <div class="sale-detail-meta-line">보유/위탁 수량 : <?= $text($cond['have_product_qty'] ?? '', '0') ?> / <?= $text($cond['provider_product_qty'] ?? '', '0') ?></div>
                <div class="sale-detail-meta-line">보유 최소재고 : <?= $text($cond['have_product_min_stock'] ?? '', '-') ?></div>
                <div class="sale-detail-meta-line">보유 최소 마진율 : <?= $text($cond['have_product_margin_per'] ?? '', '-') ?>%</div>
                <div class="sale-detail-meta-line">위탁 최소 마진율 : <?= $text($cond['provider_product_margin_per'] ?? '', '-') ?>%</div>
                <div class="sale-detail-meta-line">할인중복 기준 : <?= $text($cond['sale_duplicate_mode'] ?? '', '-') ?></div>
            </div>
        </div>

        <div class="sale-detail-view-mode-wrap">
            <span><b>보기모드</b></span>
            <select id="sale_detail_view_mode">
                <option value="list">기본 목록 보기</option>
                <option value="discount_group" selected>할인율 그룹화 보기</option>
            </select>
        </div>

        <div class="table-wrap5 m-t-10" id="sale_detail_list_view" style="display:none;">
            <div class="scroll-wrap">
                <table class="table-st1">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>종류</th>
                        <th>상품고유번호</th>
                        <th>분류</th>
                        <th>이미지</th>
                        <th>상품명</th>
                        <th>브랜드</th>
                        <th>공급사</th>
                        <th>바로가기</th>
                        <th>재고/상태</th>
                        <th>마지막 할인일</th>
                        <th>판매가</th>
                        <th>원가</th>
                        <th>마진율</th>
                        <th>할인율</th>
                        <th>할인판매가</th>
                        <th>할인후마진</th>
                        <th>데이터검수</th>
                        <th>그룹이동</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($productList)) { ?>
                        <?php foreach ($productList as $idx => $item) { ?>
                            <?php
                            $itemSource = (string)($item['item_source'] ?? 'have');
                            $isProvider = $itemSource === 'provider';
                            $itemPsIdx = trim((string)($item['ps_idx'] ?? ''));
                            $itemPrdIdx = trim((string)($item['prd_idx'] ?? ''));
                            $itemKey = trim((string)($item['item_key'] ?? ''));
                            $itemName = (string)($item['prd_name'] ?? '');
                            $imgPath = trim((string)($item['img_path'] ?? ''));
                            $godoGoodsNo = trim((string)($item['godo_goods_no'] ?? ''));
                            $dataInspectLines = [];
                            if ($isProvider) {
                                if (trim((string)($item['detail_crawler_date'] ?? '')) === '') {
                                    $dataInspectLines[] = '공급사 크롤링 안됨';
                                }
                                if (trim((string)($item['godo_loaded_at'] ?? '')) === '') {
                                    $dataInspectLines[] = '고도몰 로드 안됨';
                                }
                            } else {
                                $dataInspectLines[] = '정상';
                            }
                            ?>
                            <tr>
                                <td class="text-center"><?= $idx + 1 ?></td>
                                <td class="text-center">
                                    <span class="sale-detail-item-label <?= $isProvider ? 'sale-detail-item-label-provider' : 'sale-detail-item-label-have' ?>">
                                        <?= $isProvider ? '위탁상품' : '보유상품' ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= $text($isProvider ? ($item['ps_idx'] ?? '') : ($item['prd_idx'] ?? ''), '-') ?></td>
                                <td class="text-center"><?= $text($item['cd_kind_code'] ?? '', '-') ?></td>
                                <td class="text-center p-5">
                                    <?php if ($imgPath !== '') { ?>
                                        <?php if ($isProvider && $itemPsIdx !== '') { ?>
                                            <span class="sale-detail-img-link" onclick="prdProviderQuick('<?= htmlspecialchars($itemPsIdx, ENT_QUOTES, 'UTF-8') ?>');">
                                                <img src="<?= htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8') ?>" class="sale-detail-img" alt="">
                                            </span>
                                        <?php } elseif (!$isProvider && $itemPrdIdx !== '') { ?>
                                            <span class="sale-detail-img-link" onclick="onlyAD.prdView('<?= htmlspecialchars($itemPrdIdx, ENT_QUOTES, 'UTF-8') ?>','info');">
                                                <img src="<?= htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8') ?>" class="sale-detail-img" alt="">
                                            </span>
                                        <?php } else { ?>
                                            <img src="<?= htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8') ?>" class="sale-detail-img" alt="">
                                        <?php } ?>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($isProvider && $itemPsIdx !== '') { ?>
                                        <span class="sale-detail-name-link" onclick="prdProviderQuick('<?= htmlspecialchars($itemPsIdx, ENT_QUOTES, 'UTF-8') ?>');">
                                            <?= htmlspecialchars($text($itemName, '-'), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php } elseif (!$isProvider && $itemPrdIdx !== '') { ?>
                                        <span class="sale-detail-name-link" onclick="onlyAD.prdView('<?= htmlspecialchars($itemPrdIdx, ENT_QUOTES, 'UTF-8') ?>','info');">
                                            <?= htmlspecialchars($text($itemName, '-'), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php } else { ?>
                                        <?= htmlspecialchars($text($itemName, '-'), ENT_QUOTES, 'UTF-8') ?>
                                    <?php } ?>
                                </td>
                                <td class="text-center"><?= $text($item['brand_name'] ?? '', '-') ?></td>
                                <td class="text-center">
                                    <?php if ($isProvider) { ?>
                                        <?= $text($item['supplier_site'] ?? '', '-') ?><br>
                                        <?= $text($item['supplier_2nd_name'] ?? '', '-') ?>
                                    <?php } else { ?>
                                        자체상품
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($godoGoodsNo !== '') { ?>
                                        <div class="sale-detail-action-wrap">
                                            <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall('<?= htmlspecialchars($godoGoodsNo, ENT_QUOTES, 'UTF-8') ?>');">쑈당몰 바로가기</button>
                                            <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin('<?= htmlspecialchars($godoGoodsNo, ENT_QUOTES, 'UTF-8') ?>');">관리자 바로가기</button>
                                        </div>
                                        <div class="m-t-3" style="font-size:11px;">#<?= htmlspecialchars($godoGoodsNo, ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($isProvider) { ?>
                                        <?php $supplierStatus = $text($item['supplier_status'] ?? '', '-'); ?>
                                        <?php if ($supplierStatus === '판매중') { ?>
                                            판매중
                                        <?php } else { ?>
                                            <span class="sale-detail-warning"><?= $supplierStatus ?></span>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?= $number($item['stock_qty'] ?? 0) ?>
                                    <?php } ?>
                                </td>
                                <td class="text-center"><?= $text($item['last_sale_date'] ?? '', '-') ?></td>
                                <td class="text-right"><?= $number($item['sale_price'] ?? 0) ?></td>
                                <td class="text-right"><?= $number($item['cost_price'] ?? 0) ?></td>
                                <td class="text-center"><?= $percent($item['margin_per'] ?? 0) ?></td>
                                <td class="text-center"><?= $percent($item['discount_rate'] ?? 0) ?></td>
                                <td class="text-right"><?= $number($item['discount_sale_price'] ?? 0) ?></td>
                                <td class="text-center">
                                    <?= $percent($item['discount_margin_per'] ?? 0) ?><br>
                                    <span style="font-size:11px;">마진금액 : <?= $number($item['discount_margin_amount'] ?? 0) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if (empty($dataInspectLines)) { ?>
                                        정상
                                    <?php } else { ?>
                                        <?= implode('<br>', array_map('htmlspecialchars', $dataInspectLines)) ?>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ((string)($saleHistory['sale_status'] ?? '') === 'wait') { ?>
                                        <div class="sale-detail-action-wrap">
                                            <select
                                                class="move-discount-group-select"
                                                data-item-key="<?= htmlspecialchars($itemKey, ENT_QUOTES, 'UTF-8') ?>"
                                                data-item-source="<?= htmlspecialchars($itemSource, ENT_QUOTES, 'UTF-8') ?>"
                                                data-ps-idx="<?= htmlspecialchars($itemPsIdx, ENT_QUOTES, 'UTF-8') ?>"
                                                data-prd-idx="<?= htmlspecialchars($itemPrdIdx, ENT_QUOTES, 'UTF-8') ?>"
                                            >
                                                <?php foreach ($discountRateOptions as $rateOption) { ?>
                                                    <?php
                                                    $optionValue = (string)$rateOption;
                                                    $currentRate = rtrim(rtrim(number_format((float)($item['discount_rate'] ?? 0), 2, '.', ''), '0'), '.');
                                                    $isSelected = ($currentRate === $optionValue);
                                                    ?>
                                                    <option value="<?= htmlspecialchars($optionValue, ENT_QUOTES, 'UTF-8') ?>" <?= $isSelected ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($optionValue, ENT_QUOTES, 'UTF-8') ?>%
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs move-discount-group-btn">이동저장</button>
                                        </div>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="19" class="text-center" style="padding:30px;">저장된 상품 데이터가 없습니다.</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sale-detail-group-wrap" id="sale_detail_group_view">
            <?php if (!empty($discountGroupedProducts)) { ?>
                <?php foreach ($discountGroupedProducts as $discountRate => $groupItems) { ?>
                    <?php
                    $groupStatus = $groupUploadStatusMap[$discountRate] ?? [];
                    $groupStatusCode = (string)($groupStatus['status'] ?? '');
                    $groupUploadedAt = $text($groupStatus['uploaded_at'] ?? '', '-');
                    $groupUploadedByName = $text($groupStatus['uploaded_by_name'] ?? '', '-');
                    $groupStatusMessage = $text($groupStatus['message'] ?? '', '');
                    ?>
                    <div class="sale-detail-group-box">
                        <div class="sale-detail-group-head">
                            <span class="sale-detail-group-title">할인율 <?= htmlspecialchars($discountRate, ENT_QUOTES, 'UTF-8') ?>% 그룹</span>
                            <div class="sale-detail-group-right">
                                <span class="sale-detail-group-count"><?= number_format(count($groupItems)) ?>건</span>
                                <?php if ($groupStatusCode === 'success') { ?>
                                    <span class="sale-detail-group-status sale-detail-group-status-ok">등록완료</span>
                                    <span class="sale-detail-group-meta">
                                        <?= htmlspecialchars($groupUploadedAt, ENT_QUOTES, 'UTF-8') ?> / <?= htmlspecialchars($groupUploadedByName, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php } elseif ($groupStatusCode === 'failed') { ?>
                                    <span class="sale-detail-group-status sale-detail-group-status-fail">등록실패</span>
                                    <?php if ($groupStatusMessage !== '') { ?>
                                        <span class="sale-detail-group-meta"><?= htmlspecialchars($groupStatusMessage, ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php } ?>
                                <?php } else { ?>
                                    <span class="sale-detail-group-status">미등록</span>
                                <?php } ?>
                                <?php if ((string)($saleHistory['sale_status'] ?? '') === 'wait') { ?>
                                    <?php if ($groupStatusCode !== 'success') { ?>
                                        <button
                                            type="button"
                                            class="btn btnstyle1 btnstyle1-primary btnstyle1-xs register_godo_group_btn"
                                            data-seq="<?= (int)($saleHistory['seq'] ?? 0) ?>"
                                            data-discount-rate="<?= htmlspecialchars($discountRate, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                            그룹 등록
                                        </button>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="table-wrap5">
                            <div class="">
                                <table class="table-st1">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>종류</th>
                                        <th>상품고유번호</th>
                                        <th>분류</th>
                                        <th>이미지</th>
                                        <th>상품명</th>
                                        <th>브랜드</th>
                                        <th>공급사</th>
                                        <th>바로가기</th>
                                        <th>재고/상태</th>
                                        <th>마지막 할인일</th>
                                        <th>판매가</th>
                                        <th>원가</th>
                                        <th>마진율</th>
                                        <th>할인율</th>
                                        <th>할인판매가</th>
                                        <th>할인후마진</th>
                                        <th>데이터검수</th>
                                        <th>그룹이동</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($groupItems as $idx => $item) { ?>
                                        <?php
                                        $item = $normalizeProductItem($item);
                                        if (empty($item)) {
                                            continue;
                                        }
                                        $itemSource = (string)($item['item_source'] ?? 'have');
                                        $isProvider = $itemSource === 'provider';
                                        $itemPsIdx = trim((string)($item['ps_idx'] ?? ''));
                                        $itemPrdIdx = trim((string)($item['prd_idx'] ?? ''));
                                        $itemKey = trim((string)($item['item_key'] ?? ''));
                                        $itemName = (string)($item['prd_name'] ?? '');
                                        $imgPath = trim((string)($item['img_path'] ?? ''));
                                        $godoGoodsNo = trim((string)($item['godo_goods_no'] ?? ''));
                                        $dataInspectLines = [];
                                        if ($isProvider) {
                                            if (trim((string)($item['detail_crawler_date'] ?? '')) === '') {
                                                $dataInspectLines[] = '공급사 크롤링 안됨';
                                            }
                                            if (trim((string)($item['godo_loaded_at'] ?? '')) === '') {
                                                $dataInspectLines[] = '고도몰 로드 안됨';
                                            }
                                        } else {
                                            $dataInspectLines[] = '정상';
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $idx + 1 ?></td>
                                            <td class="text-center">
                                                <span class="sale-detail-item-label <?= $isProvider ? 'sale-detail-item-label-provider' : 'sale-detail-item-label-have' ?>">
                                                    <?= $isProvider ? '위탁상품' : '보유상품' ?>
                                                </span>
                                            </td>
                                            <td class="text-center"><?= $text($isProvider ? ($item['ps_idx'] ?? '') : ($item['prd_idx'] ?? ''), '-') ?></td>
                                            <td class="text-center"><?= $text($item['cd_kind_code'] ?? '', '-') ?></td>
                                            <td class="text-center p-5">
                                                <?php if ($imgPath !== '') { ?>
                                                    <?php if ($isProvider && $itemPsIdx !== '') { ?>
                                                        <span class="sale-detail-img-link" onclick="prdProviderQuick('<?= htmlspecialchars($itemPsIdx, ENT_QUOTES, 'UTF-8') ?>');">
                                                            <img src="<?= htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8') ?>" class="sale-detail-img" alt="">
                                                        </span>
                                                    <?php } elseif (!$isProvider && $itemPrdIdx !== '') { ?>
                                                        <span class="sale-detail-img-link" onclick="onlyAD.prdView('<?= htmlspecialchars($itemPrdIdx, ENT_QUOTES, 'UTF-8') ?>','info');">
                                                            <img src="<?= htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8') ?>" class="sale-detail-img" alt="">
                                                        </span>
                                                    <?php } else { ?>
                                                        <img src="<?= htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8') ?>" class="sale-detail-img" alt="">
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    -
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($isProvider && $itemPsIdx !== '') { ?>
                                                    <span class="sale-detail-name-link" onclick="prdProviderQuick('<?= htmlspecialchars($itemPsIdx, ENT_QUOTES, 'UTF-8') ?>');">
                                                        <?= htmlspecialchars($text($itemName, '-'), ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                <?php } elseif (!$isProvider && $itemPrdIdx !== '') { ?>
                                                    <span class="sale-detail-name-link" onclick="onlyAD.prdView('<?= htmlspecialchars($itemPrdIdx, ENT_QUOTES, 'UTF-8') ?>','info');">
                                                        <?= htmlspecialchars($text($itemName, '-'), ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <?= htmlspecialchars($text($itemName, '-'), ENT_QUOTES, 'UTF-8') ?>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center"><?= $text($item['brand_name'] ?? '', '-') ?></td>
                                            <td class="text-center">
                                                <?php if ($isProvider) { ?>
                                                    <?= $text($item['supplier_site'] ?? '', '-') ?><br>
                                                    <?= $text($item['supplier_2nd_name'] ?? '', '-') ?>
                                                <?php } else { ?>
                                                    자체상품
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($godoGoodsNo !== '') { ?>
                                                    <div class="sale-detail-action-wrap">
                                                        <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall('<?= htmlspecialchars($godoGoodsNo, ENT_QUOTES, 'UTF-8') ?>');">쑈당몰 바로가기</button>
                                                        <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin('<?= htmlspecialchars($godoGoodsNo, ENT_QUOTES, 'UTF-8') ?>');">관리자 바로가기</button>
                                                    </div>
                                                    <div class="m-t-3" style="font-size:11px;">#<?= htmlspecialchars($godoGoodsNo, ENT_QUOTES, 'UTF-8') ?></div>
                                                <?php } else { ?>
                                                    -
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($isProvider) { ?>
                                                    <?php $supplierStatus = $text($item['supplier_status'] ?? '', '-'); ?>
                                                    <?php if ($supplierStatus === '판매중') { ?>
                                                        판매중
                                                    <?php } else { ?>
                                                        <span class="sale-detail-warning"><?= $supplierStatus ?></span>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?= $number($item['stock_qty'] ?? 0) ?>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center"><?= $text($item['last_sale_date'] ?? '', '-') ?></td>
                                            <td class="text-right"><?= $number($item['sale_price'] ?? 0) ?></td>
                                            <td class="text-right"><?= $number($item['cost_price'] ?? 0) ?></td>
                                            <td class="text-center"><?= $percent($item['margin_per'] ?? 0) ?></td>
                                            <td class="text-center"><?= $percent($item['discount_rate'] ?? 0) ?></td>
                                            <td class="text-right"><?= $number($item['discount_sale_price'] ?? 0) ?></td>
                                            <td class="text-center">
                                                <?= $percent($item['discount_margin_per'] ?? 0) ?><br>
                                                <span style="font-size:11px;">마진금액 : <?= $number($item['discount_margin_amount'] ?? 0) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if (empty($dataInspectLines)) { ?>
                                                    정상
                                                <?php } else { ?>
                                                    <?= implode('<br>', array_map('htmlspecialchars', $dataInspectLines)) ?>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ((string)($saleHistory['sale_status'] ?? '') === 'wait') { ?>
                                                    <div class="sale-detail-action-wrap">
                                                        <select
                                                            class="move-discount-group-select"
                                                            data-item-key="<?= htmlspecialchars($itemKey, ENT_QUOTES, 'UTF-8') ?>"
                                                            data-item-source="<?= htmlspecialchars($itemSource, ENT_QUOTES, 'UTF-8') ?>"
                                                            data-ps-idx="<?= htmlspecialchars($itemPsIdx, ENT_QUOTES, 'UTF-8') ?>"
                                                            data-prd-idx="<?= htmlspecialchars($itemPrdIdx, ENT_QUOTES, 'UTF-8') ?>"
                                                        >
                                                            <?php foreach ($discountRateOptions as $rateOption) { ?>
                                                                <?php
                                                                $optionValue = (string)$rateOption;
                                                                $currentRate = rtrim(rtrim(number_format((float)($item['discount_rate'] ?? 0), 2, '.', ''), '0'), '.');
                                                                $isSelected = ($currentRate === $optionValue);
                                                                ?>
                                                                <option value="<?= htmlspecialchars($optionValue, ENT_QUOTES, 'UTF-8') ?>" <?= $isSelected ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($optionValue, ENT_QUOTES, 'UTF-8') ?>%
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                        <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs move-discount-group-btn">이동저장</button>
                                                    </div>
                                                <?php } else { ?>
                                                    -
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php if (empty(array_filter($groupItems, 'is_array'))) { ?>
                                        <tr>
                                            <td colspan="19" class="text-center" style="padding:20px;">표시할 상품 데이터가 없습니다.</td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="table-wrap5">
                    <div class="scroll-wrap">
                        <table class="table-st1">
                            <tbody>
                            <tr>
                                <td class="text-center" style="padding:30px;">그룹화할 상품 데이터가 없습니다.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div id="contents_bottom">
    <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="location.href='/admin/sale/history'">
        <i class="fas fa-arrow-left"></i> 목록
    </button>
</div>

<script>
    (function () {
        var $mode = $('#sale_detail_view_mode');
        var $listView = $('#sale_detail_list_view');
        var $groupView = $('#sale_detail_group_view');
        var $registerBtn = $('#register_godo_time_sale_btn');
        var $groupRegisterBtns = $('.register_godo_group_btn');
        var saleHistorySeq = '<?= (int)($saleHistory['seq'] ?? 0) ?>';

        var applyViewMode = function () {
            var mode = String($mode.val() || 'discount_group');
            if (mode === 'discount_group') {
                $listView.hide();
                $groupView.show();
            } else {
                $groupView.hide();
                $listView.show();
            }
        };

        $mode.on('change', applyViewMode);
        applyViewMode();

        $registerBtn.on('click', function () {
            var seq = String($registerBtn.data('seq') || '').trim();
            if (!seq) {
                alert('할인 이력 번호가 없습니다.');
                return;
            }

            if (!confirm('미등록 할인율 그룹만 고도몰 타임세일로 전체 등록하시겠습니까?')) {
                return;
            }

            $registerBtn.prop('disabled', true).text('전체 그룹 등록중...');

            $.ajax({
                url: '/admin/sale/history/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'create_godo_time_sale_from_history',
                    seq: seq
                },
                success: function (response) {
                    if (!response || response.status !== 'success') {
                        var failMsg = (response && response.message) ? response.message : '고도몰 등록에 실패했습니다.';
                        if (typeof showToast === 'function') {
                            showToast(failMsg, new Date().toLocaleTimeString());
                        } else {
                            alert(failMsg);
                        }
                        return;
                    }

                    var result = response.data || {};
                    var skippedGroupCount = Number(result.skipped_group_count || 0);
                    var successGroupCount = Number(result.success_group_count || 0);
                    var failedGroupCount = Number(result.failed_group_count || 0);
                    var doneMessage = '전체 그룹 등록 완료 (성공 '
                        + successGroupCount
                        + '그룹 / 제외 '
                        + skippedGroupCount
                        + '그룹 / 실패 '
                        + failedGroupCount
                        + '그룹)';

                    if (failedGroupCount > 0) {
                        var failedGroups = Array.isArray(result.failed_groups) ? result.failed_groups : [];
                        var failLines = [];
                        for (var i = 0; i < failedGroups.length; i++) {
                            var row = failedGroups[i] || {};
                            var rate = String(row.discount_rate || '-');
                            var msg = String(row.message || '알 수 없는 오류');
                            failLines.push('- 할인율 ' + rate + '%: ' + msg);
                        }
                        var detailMessage = doneMessage
                            + '\n\n실패 원인'
                            + (failLines.length ? '\n' + failLines.join('\n') : '\n- 상세 오류 정보가 없습니다.');

                        if (typeof showToast === 'function') {
                            showToast(doneMessage, new Date().toLocaleTimeString());
                        }
                        alert(detailMessage);
                        return;
                    }

                    if (typeof showToast === 'function') {
                        showToast(doneMessage, new Date().toLocaleTimeString());
                    } else {
                        alert(doneMessage);
                    }
                    setTimeout(function () {
                        location.reload();
                    }, 300);
                },
                error: function (xhr) {
                    var message = '고도몰 등록에 실패했습니다.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (typeof showToast === 'function') {
                        showToast(message, new Date().toLocaleTimeString());
                    } else {
                        alert(message);
                    }
                },
                complete: function () {
                    $registerBtn.prop('disabled', false).text('전체 그룹 등록');
                }
            });
        });

        $groupRegisterBtns.on('click', function () {
            var $btn = $(this);
            var seq = String($btn.data('seq') || '').trim();
            var discountRate = String($btn.data('discount-rate') || '').trim();
            if (!seq || !discountRate) {
                alert('그룹 등록 정보가 부족합니다.');
                return;
            }

            if (!confirm('할인율 ' + discountRate + '% 그룹만 고도몰 타임세일로 등록하시겠습니까?')) {
                return;
            }

            $btn.prop('disabled', true).text('그룹 등록중...');

            $.ajax({
                url: '/admin/sale/history/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'create_godo_time_sale_group_from_history',
                    seq: seq,
                    discount_rate: discountRate
                },
                success: function (response) {
                    if (!response || response.status !== 'success') {
                        var failMsg = (response && response.message) ? response.message : '그룹 등록에 실패했습니다.';
                        if (typeof showToast === 'function') {
                            showToast(failMsg, new Date().toLocaleTimeString());
                        } else {
                            alert(failMsg);
                        }
                        return;
                    }

                    var result = response.data || {};
                    var alreadyRegistered = (result.already_registered === true);
                    var doneMessage = alreadyRegistered
                        ? ('할인율 ' + discountRate + '% 그룹은 이미 등록완료 상태입니다.')
                        : ('할인율 ' + discountRate + '% 그룹 등록 완료');

                    if (typeof showToast === 'function') {
                        showToast(doneMessage, new Date().toLocaleTimeString());
                    } else {
                        alert(doneMessage);
                    }

                    setTimeout(function () {
                        location.reload();
                    }, 250);
                },
                error: function (xhr) {
                    var message = '그룹 등록에 실패했습니다.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (typeof showToast === 'function') {
                        showToast(message, new Date().toLocaleTimeString());
                    } else {
                        alert(message);
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false).text('그룹 등록');
                }
            });
        });

        $(document).on('click', '.move-discount-group-btn', function () {
            var $btn = $(this);
            var $wrap = $btn.closest('.sale-detail-action-wrap');
            var $select = $wrap.find('.move-discount-group-select');
            if (!$select.length) {
                alert('그룹이동 대상 정보가 없습니다.');
                return;
            }

            var targetRate = String($select.val() || '').trim();
            var itemKey = String($select.data('item-key') || '').trim();
            var itemSource = String($select.data('item-source') || '').trim();
            var psIdx = String($select.data('ps-idx') || '').trim();
            var prdIdx = String($select.data('prd-idx') || '').trim();

            if (!saleHistorySeq) {
                alert('할인 이력 번호가 없습니다.');
                return;
            }
            if (!targetRate) {
                alert('이동할 할인율 그룹을 선택해 주세요.');
                return;
            }

            if (!confirm('선택한 상품을 할인율 ' + targetRate + '% 그룹으로 이동 저장하시겠습니까?')) {
                return;
            }

            $btn.prop('disabled', true).text('저장중...');
            $select.prop('disabled', true);

            $.ajax({
                url: '/admin/sale/history/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'move_sale_history_discount_group_item',
                    seq: saleHistorySeq,
                    item_key: itemKey,
                    item_source: itemSource,
                    ps_idx: psIdx,
                    prd_idx: prdIdx,
                    target_discount_rate: targetRate
                },
                success: function (response) {
                    if (!response || response.status !== 'success') {
                        var failMsg = (response && response.message) ? response.message : '그룹 이동 저장에 실패했습니다.';
                        if (typeof showToast === 'function') {
                            showToast(failMsg, new Date().toLocaleTimeString());
                        } else {
                            alert(failMsg);
                        }
                        return;
                    }

                    var doneMsg = (response && response.message) ? response.message : ('할인율 ' + targetRate + '% 그룹으로 이동 저장되었습니다.');
                    if (typeof showToast === 'function') {
                        showToast(doneMsg, new Date().toLocaleTimeString());
                    } else {
                        alert(doneMsg);
                    }

                    setTimeout(function () {
                        location.reload();
                    }, 200);
                },
                error: function (xhr) {
                    var message = '그룹 이동 저장에 실패했습니다.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (typeof showToast === 'function') {
                        showToast(message, new Date().toLocaleTimeString());
                    } else {
                        alert(message);
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false).text('이동저장');
                    $select.prop('disabled', false);
                }
            });
        });
    })();
</script>
