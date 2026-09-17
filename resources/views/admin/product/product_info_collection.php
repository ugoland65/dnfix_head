<?php
$viewData = get_defined_vars();
$prdIdx = (string)($viewData['prd_idx'] ?? '');
$productData = is_array($viewData['productData'] ?? null) ? $viewData['productData'] : [];
$imageStoragePath = trim((string)($productData['CD_IMAGE_STORAGE_PATH'] ?? ''));
$imageStorageNationalCodeMap = ['jp' => 'JP', 'cn' => 'CN', 'kr' => 'KR'];
$imageStorageNationalCode = $imageStorageNationalCodeMap[strtolower((string)($productData['cd_national'] ?? ''))] ?? 'ETC';
$brandEnglishName = trim((string)($productData['BD_NAME_EN'] ?? ''));
$brandEnglishName = html_entity_decode($brandEnglishName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$brandEnglishName = str_replace(["'", '’', '‘', '`'], '', $brandEnglishName);
$imageStorageBrandName = '브랜드영문이름';
if ($brandEnglishName !== '') {
    $brandWords = preg_split('/[^A-Za-z0-9]+/', $brandEnglishName, -1, PREG_SPLIT_NO_EMPTY);
    if (!empty($brandWords)) {
        $imageStorageBrandName = lcfirst(implode('', array_map(static function (string $word): string {
            return ucfirst(strtolower($word));
        }, $brandWords)));
    }
}
$recommendedImageStoragePath = '/goods/' . $imageStorageNationalCode . '/' . $imageStorageBrandName . '/' . $prdIdx . '/';
$hostingImageUrls = is_array($viewData['hostingImageUrls'] ?? null) ? $viewData['hostingImageUrls'] : [];
$hostingCollectionItemIdx = (int)($viewData['hostingCollectionItemIdx'] ?? 0);
$collectionItemData = is_array($viewData['collectionItemData'] ?? null) ? $viewData['collectionItemData'] : [];
$collectionItemIdx = (int)($collectionItemData['idx'] ?? 0);
$translatedAccessories = trim((string)($collectionItemData['translated_accessories'] ?? ''));
$translatedMakerComment = trim((string)($collectionItemData['translated_maker_comment'] ?? ''));
$translatedSellerComment = trim((string)($collectionItemData['translated_seller_comment'] ?? ''));
$translatedImageAlts = json_decode((string)($collectionItemData['translated_image_alts_json'] ?? '{}'), true);
if (!is_array($translatedImageAlts)) {
    $translatedImageAlts = [];
}
$collectionActionLogs = is_array($viewData['collectionActionLogs'] ?? null) ? $viewData['collectionActionLogs'] : [];
$translationLogs = ['accessories' => null, 'maker_comment' => null, 'seller_comment' => null];
$hostingUploadLog = null;
foreach ($collectionActionLogs as $collectionActionLog) {
    $actionMode = (string)($collectionActionLog['action_mode'] ?? '');
    if ($translationLogs['accessories'] === null && $actionMode === 'translation_accessories') {
        $translationLogs['accessories'] = $collectionActionLog;
    } elseif ($translationLogs['maker_comment'] === null && $actionMode === 'translation_maker_comment') {
        $translationLogs['maker_comment'] = $collectionActionLog;
    } elseif ($translationLogs['seller_comment'] === null && $actionMode === 'translation_seller_comment') {
        $translationLogs['seller_comment'] = $collectionActionLog;
    } elseif ($hostingUploadLog === null && $actionMode === 'image_hosting_upload') {
        $hostingUploadLog = $collectionActionLog;
    }
}
$normalizeCollectedDate = static function ($dateData): string {
    if ($dateData === null) {
        return '';
    }
    if (is_array($dateData)) {
        $dateData = $dateData['date'] ?? '';
    }
    $text = trim((string)$dateData);
    if ($text === '') {
        return '';
    }
    if (preg_match('/^(\d{4}-\d{2}-\d{2})[ T](\d{2}:\d{2}:\d{2})(?:\.\d+)?/', $text, $matches)) {
        return $matches[1] . ' ' . $matches[2];
    }
    return $text;
};
$formatActionLog = static function (?array $actionLog) use ($normalizeCollectedDate): string {
    if (empty($actionLog)) {
        return '';
    }
    $operator = trim((string)($actionLog['operator_name'] ?? $actionLog['operator_id'] ?? '알 수 없음'));
    $processedAt = $normalizeCollectedDate($actionLog['processed_at'] ?? '');
    return $operator . ($processedAt !== '' ? ' · ' . $processedAt : '');
};
$collectionData = $viewData['collectionData'] ?? [];
$collectionError = (string)($viewData['collectionError'] ?? '');
$collectionItems = is_array($collectionData['data']['items'] ?? null) ? $collectionData['data']['items'] : [];
$selectedCollectionIndex = max(0, (int)($viewData['selectedCollectionIndex'] ?? 0));
if (!isset($collectionItems[$selectedCollectionIndex])) {
    $selectedCollectionIndex = 0;
}
$collectionItem = is_array($collectionItems[$selectedCollectionIndex] ?? null) ? $collectionItems[$selectedCollectionIndex] : [];
$specifications = is_array($collectionItem['specifications'] ?? null) ? $collectionItem['specifications'] : [];
$packageSize = is_array($specifications['package_size'] ?? null) ? $specifications['package_size'] : [];
$packageWeight = is_array($specifications['package_weight'] ?? null) ? $specifications['package_weight'] : [];
$productSize = is_array($specifications['product_size'] ?? null) ? $specifications['product_size'] : [];
$productWeight = is_array($specifications['product_weight'] ?? null) ? $specifications['product_weight'] : [];
$internalLengthSource = $specifications['internal_length'] ?? $collectionItem['internal_length'] ?? null;
$internalLength = is_array($internalLengthSource) ? $internalLengthSource : [];
$vaginalInternalLength = is_array($internalLength['vaginal'] ?? null) ? $internalLength['vaginal'] : [];
$analInternalLength = is_array($internalLength['anal'] ?? null) ? $internalLength['anal'] : [];
$material = trim((string)($collectionItem['material'] ?? $specifications['material'] ?? ''));
$productType = trim((string)($collectionItem['product_type'] ?? $specifications['product_type'] ?? ''));
$countryOfOrigin = trim((string)($collectionItem['country_of_origin'] ?? $specifications['country_of_origin'] ?? ''));
$registrationDateRaw = $collectionItem['registration_date']
    ?? $collectionItem['release_date']
    ?? $specifications['registration_date']
    ?? $specifications['release_date']
    ?? '';
$registrationDateText = is_array($registrationDateRaw)
    ? trim((string)($registrationDateRaw['date'] ?? ''))
    : trim((string)$registrationDateRaw);
if ($registrationDateText !== '' && preg_match('/^(\d{4}-\d{2}-\d{2})/', $registrationDateText, $registrationDateMatch)) {
    $registrationDateText = $registrationDateMatch[1];
} elseif ($registrationDateText !== '') {
    $registrationTimestamp = strtotime($registrationDateText);
    $registrationDateText = $registrationTimestamp ? date('Y-m-d', $registrationTimestamp) : $registrationDateText;
}
$imageSources = is_array($collectionItem['image_sources'] ?? null) ? $collectionItem['image_sources'] : [];
$sourceUrl = trim((string)($collectionItem['source_url'] ?? ''));
$collectedImages = [];

foreach ($imageSources as $imageSource) {
    if (is_array($imageSource)) {
        $imageUrl = trim((string)($imageSource['full'] ?? $imageSource['src'] ?? ''));
        $imageAlt = trim((string)($imageSource['alt'] ?? ''));
    } else {
        $imageUrl = trim((string)$imageSource);
        $imageAlt = '';
    }
    if ($imageUrl !== '') {
        $imageUrl = \App\Services\ProductImageHostingService::resolveCollectedImageUrl($imageUrl, $sourceUrl);
        $imageKey = \App\Services\ProductImageHostingService::collectedImageTranslationKey($imageUrl);
        $collectedImages[] = [
            'url' => $imageUrl,
            'alt' => $imageAlt,
            'key' => $imageKey,
            'translated_alt' => \App\Services\ProductImageHostingService::collectedImageAltTranslation($translatedImageAlts, $imageUrl),
        ];
    }
}
$imageUrls = array_column($collectedImages, 'url');

$detailImageHtml = implode("\n", array_map(static function (string $imageUrl): string {
    return '<img src="' . $imageUrl . '" referrerpolicy="no-referrer"><br>';
}, $imageUrls));
$hostingImageHtml = implode("\n", array_map(static function (string $imageUrl): string {
    return '<img src="' . $imageUrl . '"><br>';
}, $hostingImageUrls));
$imageProxyUrl = static function (string $imageUrl): string {
    return '/admin/product/info_collect/image?' . http_build_query(['url' => $imageUrl]);
};
$formatCollectedDate = static function ($dateData) use ($normalizeCollectedDate): string {
    return $normalizeCollectedDate($dateData);
};
$formatCollectedText = static function ($value) use ($normalizeCollectedDate): string {
    if ($value === null) {
        return 'No Data';
    }
    $text = $normalizeCollectedDate($value);
    return $text === '' ? 'No Data' : $text;
};
$formatCollectedSize = static function (array $size, string $depthLabel): string {
    $width = trim((string)($size['width'] ?? ''));
    $height = trim((string)($size['height'] ?? ''));
    $depth = trim((string)($size['depth'] ?? ''));
    $unit = trim((string)($size['unit'] ?? ''));
    if ($width === '' && $height === '' && $depth === '') {
        return 'No Data';
    }
    $part = static function (string $value): string {
        return $value === '' ? 'No Data' : $value;
    };
    $text = '가로(W): ' . $part($width) . ' × 세로(H): ' . $part($height) . ' × ' . $depthLabel . ': ' . $part($depth);
    return $unit !== '' ? $text . ' ' . $unit : $text;
};
$formatCollectedMeasure = static function (array $measure): string {
    $value = trim((string)($measure['weight'] ?? $measure['length'] ?? ''));
    $unit = trim((string)($measure['unit'] ?? ''));
    if ($value === '') {
        return 'No Data';
    }
    return $unit !== '' ? $value . ' ' . $unit : $value;
};
$renderCollectedValue = static function (string $text): string {
    return str_replace('No Data', '<span class="collected-no-data">No Data</span>', htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
};
$siteCodeNames = [
    'npg' => 'NPG 주문사이트',
    'tamatoys' => '타마토이즈 본사사이트',
    'mzakka' => '엠자카',
    'nobunaga' => '노부나가',
    'nls' => 'NLS 사이트',
    'ms' => '엠즈',
    'tis' => 'TIS',
    'ridejapan' => '라이드재팬',
    'yelolab' => '옐로랩',
];
$formatSiteCodeName = static function (string $siteCode) use ($siteCodeNames): string {
    return $siteCodeNames[strtolower(trim($siteCode))] ?? '';
};
$normalizeCompareText = static function ($value): string {
    $text = html_entity_decode(trim((string)$value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text);
    return trim((string)$text);
};
$toHalfwidthAscii = static function (string $text): string {
    return $text === '' ? '' : mb_convert_kana($text, 'as', 'UTF-8');
};
$currentNameOg = $normalizeCompareText($productData['CD_NAME_OG'] ?? '');
$collectedProductName = $toHalfwidthAscii($normalizeCompareText($collectionItem['product_name'] ?? ''));
$canSyncNameOg = $collectedProductName !== '' && $currentNameOg !== $collectedProductName;
$normalizeSizeNumber = static function ($value): string {
    $text = trim((string)$value);
    $text = str_replace([',', ' '], '', $text);
    $text = preg_replace('/[^\d.\-]/', '', $text);
    if ($text === '' || !is_numeric($text)) {
        return '';
    }
    $number = (float)$text;
    if (abs($number - round($number)) < 0.0001) {
        return (string)(int)round($number);
    }
    return rtrim(rtrim(sprintf('%.4F', $number), '0'), '.');
};
$toMillimeter = static function (string $value, string $unit) use ($normalizeSizeNumber): string {
    if ($value === '') {
        return '';
    }
    $unit = strtolower(trim($unit));
    if ($unit === 'cm' || str_starts_with($unit, 'cm')) {
        $millimeter = (float)$value * 10;
        return $normalizeSizeNumber($millimeter);
    }
    return $value;
};
$currentPackageSize = $productData['CD_SIZE'] ?? [];
if (is_string($currentPackageSize)) {
    $decodedPackageSize = json_decode($currentPackageSize, true);
    $currentPackageSize = is_array($decodedPackageSize) ? $decodedPackageSize : [];
}
if (!is_array($currentPackageSize)) {
    $currentPackageSize = [];
}
$collectedPackageUnit = trim((string)($packageSize['unit'] ?? ''));
$collectedPackageSizeMm = [
    'W' => $toMillimeter($normalizeSizeNumber($packageSize['width'] ?? ''), $collectedPackageUnit),
    'H' => $toMillimeter($normalizeSizeNumber($packageSize['height'] ?? ''), $collectedPackageUnit),
    'D' => $toMillimeter($normalizeSizeNumber($packageSize['depth'] ?? ''), $collectedPackageUnit),
];
$currentPackageSizeMm = [
    'W' => $normalizeSizeNumber($currentPackageSize['W'] ?? ''),
    'H' => $normalizeSizeNumber($currentPackageSize['H'] ?? ''),
    'D' => $normalizeSizeNumber($currentPackageSize['D'] ?? ''),
];
$hasCollectedPackageSize = ($collectedPackageSizeMm['W'] !== '' || $collectedPackageSizeMm['H'] !== '' || $collectedPackageSizeMm['D'] !== '');
$canSyncPackageSize = $hasCollectedPackageSize && (
    $collectedPackageSizeMm['W'] !== $currentPackageSizeMm['W']
    || $collectedPackageSizeMm['H'] !== $currentPackageSizeMm['H']
    || $collectedPackageSizeMm['D'] !== $currentPackageSizeMm['D']
);
$currentPackageSizeLabel = ($currentPackageSizeMm['W'] === '' && $currentPackageSizeMm['H'] === '' && $currentPackageSizeMm['D'] === '')
    ? ''
    : ($currentPackageSizeMm['W'] !== '' ? $currentPackageSizeMm['W'] : '-')
        . ' × ' . ($currentPackageSizeMm['H'] !== '' ? $currentPackageSizeMm['H'] : '-')
        . ' × ' . ($currentPackageSizeMm['D'] !== '' ? $currentPackageSizeMm['D'] : '-')
        . ' mm';
$toGram = static function (string $value, string $unit) use ($normalizeSizeNumber): string {
    if ($value === '') {
        return '';
    }
    $unit = strtolower(trim($unit));
    if ($unit === 'kg' || str_starts_with($unit, 'kg')) {
        return $normalizeSizeNumber((float)$value * 1000);
    }
    return $value;
};
$currentWeightFn = $productData['cd_weight_fn'] ?? [];
if (is_string($currentWeightFn)) {
    $decodedWeightFn = json_decode($currentWeightFn, true);
    $currentWeightFn = is_array($decodedWeightFn) ? $decodedWeightFn : [];
}
if (!is_array($currentWeightFn)) {
    $currentWeightFn = [];
}
$collectedPackageWeightG = $toGram(
    $normalizeSizeNumber($packageWeight['weight'] ?? ''),
    (string)($packageWeight['unit'] ?? '')
);
$currentPackageWeightG = $normalizeSizeNumber($currentWeightFn['2'] ?? '');
$canSyncPackageWeight = $collectedPackageWeightG !== '' && $collectedPackageWeightG !== $currentPackageWeightG;
$collectedProductWeightG = $toGram(
    $normalizeSizeNumber($productWeight['weight'] ?? ''),
    (string)($productWeight['unit'] ?? '')
);
$currentProductWeightG = $normalizeSizeNumber($currentWeightFn['1'] ?? '');
$canSyncProductWeight = $collectedProductWeightG !== '' && $collectedProductWeightG !== $currentProductWeightG;
$hasSpecWeight = false;
$collectedSpecWeight = $collectedProductWeightG;
$currentSpecWeight = '';
$specWeightUnit = '';
$toCentimeter = static function (string $value, string $unit) use ($normalizeSizeNumber): string {
    if ($value === '') {
        return '';
    }
    $unit = strtolower(trim($unit));
    if ($unit === 'mm' || str_starts_with($unit, 'mm')) {
        return $normalizeSizeNumber((float)$value / 10);
    }
    return $value;
};
$currentSpec = $productData['cd_spec'] ?? [];
if (is_string($currentSpec)) {
    $decodedSpec = json_decode($currentSpec, true);
    $currentSpec = is_array($decodedSpec) ? $decodedSpec : [];
}
if (!is_array($currentSpec)) {
    $currentSpec = [];
}
$currentSpecVendor = (isset($currentSpec['vendor_size']) && is_array($currentSpec['vendor_size'])) ? $currentSpec['vendor_size'] : [];
$specService = new \App\Services\ProductSpecService();
$specSchema = $specService->getSchema((string)($productData['CD_CATEGORY_CODE'] ?? $productData['CD_KIND_CODE'] ?? ''));
$hasSpecProductSizeFields = isset($specSchema['fields']['length'], $specSchema['fields']['height'], $specSchema['fields']['width']);
$collectedProductUnit = trim((string)($productSize['unit'] ?? ''));
$collectedProductSizeCm = [
    'length' => $toCentimeter($normalizeSizeNumber($productSize['width'] ?? ''), $collectedProductUnit),
    'height' => $toCentimeter($normalizeSizeNumber($productSize['height'] ?? ''), $collectedProductUnit),
    'width' => $toCentimeter($normalizeSizeNumber($productSize['depth'] ?? ''), $collectedProductUnit),
];
$currentProductSizeCm = [
    'length' => $normalizeSizeNumber($currentSpecVendor['length'] ?? ''),
    'height' => $normalizeSizeNumber($currentSpecVendor['height'] ?? ''),
    'width' => $normalizeSizeNumber($currentSpecVendor['width'] ?? ''),
];
$hasCollectedProductSize = ($collectedProductSizeCm['length'] !== '' || $collectedProductSizeCm['height'] !== '' || $collectedProductSizeCm['width'] !== '');
$canSyncProductSize = $hasSpecProductSizeFields && $hasCollectedProductSize && (
    $collectedProductSizeCm['length'] !== $currentProductSizeCm['length']
    || $collectedProductSizeCm['height'] !== $currentProductSizeCm['height']
    || $collectedProductSizeCm['width'] !== $currentProductSizeCm['width']
);
$currentProductSizeLabel = ($currentProductSizeCm['length'] === '' && $currentProductSizeCm['height'] === '' && $currentProductSizeCm['width'] === '')
    ? ''
    : ($currentProductSizeCm['length'] !== '' ? $currentProductSizeCm['length'] : '-')
        . ' × ' . ($currentProductSizeCm['height'] !== '' ? $currentProductSizeCm['height'] : '-')
        . ' × ' . ($currentProductSizeCm['width'] !== '' ? $currentProductSizeCm['width'] : '-')
        . ' cm';
$specType = $specService->getSpecType((string)($productData['CD_CATEGORY_CODE'] ?? $productData['CD_KIND_CODE'] ?? ''));
$isOnaholeSpec = ($specType === '01000000');
$hasSpecWeight = isset($specSchema['fields']['weight']);
$specWeightUnit = strtolower(trim((string)($specSchema['fields']['weight'][1] ?? '')));
$currentSpecWeight = $normalizeSizeNumber($currentSpecVendor['weight'] ?? '');
$collectedSpecWeight = $collectedProductWeightG;
if ($collectedSpecWeight !== '' && ($specWeightUnit === 'kg' || str_starts_with($specWeightUnit, 'kg'))) {
    $collectedSpecWeight = $normalizeSizeNumber((float)$collectedSpecWeight / 1000);
}
$canSyncProductWeight = $collectedProductWeightG !== '' && (
    $collectedProductWeightG !== $currentProductWeightG
    || ($hasSpecWeight && $collectedSpecWeight !== $currentSpecWeight)
);
$hasSpecInnerLengthVagina = isset($specSchema['fields']['inner_length_vagina']);
$hasSpecInnerLengthAnal = isset($specSchema['fields']['inner_length_anal']);
$collectedVaginalLengthCm = $toCentimeter(
    $normalizeSizeNumber($vaginalInternalLength['length'] ?? $vaginalInternalLength['weight'] ?? ''),
    (string)($vaginalInternalLength['unit'] ?? '')
);
$collectedAnalLengthCm = $toCentimeter(
    $normalizeSizeNumber($analInternalLength['length'] ?? $analInternalLength['weight'] ?? ''),
    (string)($analInternalLength['unit'] ?? '')
);
$currentVaginalLengthCm = $normalizeSizeNumber($currentSpecVendor['inner_length_vagina'] ?? '');
$currentAnalLengthCm = $normalizeSizeNumber($currentSpecVendor['inner_length_anal'] ?? '');
$currentSize2Cm = $normalizeSizeNumber($productData['CD_SIZE2'] ?? '');
$canSyncVaginalLength = $collectedVaginalLengthCm !== '' && (
    ($hasSpecInnerLengthVagina && $collectedVaginalLengthCm !== $currentVaginalLengthCm)
    || ($isOnaholeSpec && $collectedVaginalLengthCm !== $currentSize2Cm)
);
$canSyncAnalLength = $hasSpecInnerLengthAnal && $collectedAnalLengthCm !== '' && $collectedAnalLengthCm !== $currentAnalLengthCm;
$currentVaginalLengthLabel = $currentVaginalLengthCm !== ''
    ? $currentVaginalLengthCm
    : ($isOnaholeSpec ? $currentSize2Cm : '');
$currentAnalLengthLabel = $currentAnalLengthCm;
$hasSpecMaterial = isset($specSchema['fields']['material']);
$resolvedMaterial = $specService->resolveCollectedMaterial($material);
$collectedMaterialRaw = $resolvedMaterial['raw'];
$collectedMaterialMapped = $resolvedMaterial['mapped'];
$collectedMaterial = $normalizeCompareText($resolvedMaterial['value']);
$currentMaterial = $normalizeCompareText($currentSpecVendor['material'] ?? '');
$canSyncMaterial = $hasSpecMaterial && $collectedMaterial !== '' && $collectedMaterial !== $currentMaterial;
$collectedReleaseDate = $registrationDateText;
if ($collectedReleaseDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $collectedReleaseDate)) {
    $collectedReleaseDate = '';
}
$currentReleaseDate = trim((string)($productData['CD_RELEASE_DATE'] ?? ''));
if ($currentReleaseDate === '0000-00-00') {
    $currentReleaseDate = '';
}
$canSyncReleaseDate = $collectedReleaseDate !== '' && $collectedReleaseDate !== $currentReleaseDate;
$currentBarcode = trim((string)($productData['CD_CODE'] ?? ''));
$collectedBarcode = trim((string)($collectionItem['barcode'] ?? ''));
$canSyncBarcode = $collectedBarcode !== '' && $currentBarcode !== $collectedBarcode;
$currentProductCode = $normalizeCompareText($productData['CD_CODE2'] ?? '');
$collectedProductCode = $normalizeCompareText($collectionItem['product_code'] ?? '');
$canSyncProductCode = $collectedProductCode !== '' && $currentProductCode !== $collectedProductCode;
$supplyPriceRaw = $collectionItem['supply_price'] ?? null;
$supplyCurrency = trim((string)($collectionItem['supply_currency'] ?? ''));
$supplyPriceText = ($supplyPriceRaw === null || $supplyPriceRaw === '')
    ? 'No Data'
    : number_format((float)$supplyPriceRaw) . ($supplyCurrency !== '' ? ' ' . $supplyCurrency : '');
$accessoriesText = trim((string)($specifications['accessories'] ?? ''));
$currentAccessories = $productData['cd_accessories'] ?? [];
if (is_string($currentAccessories)) {
    $decodedAccessories = json_decode($currentAccessories, true);
    $currentAccessories = is_array($decodedAccessories) ? $decodedAccessories : [];
}
if (!is_array($currentAccessories)) {
    $currentAccessories = [];
}
$hasProductAccessories = false;
foreach ($currentAccessories as $currentAccessory) {
    if (!is_array($currentAccessory)) {
        continue;
    }
    if (trim((string)($currentAccessory['text'] ?? '')) !== '') {
        $hasProductAccessories = true;
        break;
    }
}
$showAccessoryMissingWarning = $accessoriesText !== '' && !$hasProductAccessories;
$makerCommentText = trim((string)($collectionItem['maker_comment'] ?? ''));
$sellerCommentText = trim((string)($collectionItem['seller_comment'] ?? ''));
?>

        <section class="product-info-collection">
            <div class="product-info-collection-heading">
                <div>
                    <h2>상품 정보수집</h2>
                    <p>수입할 페이지의 URL을 입력해주세요.</p>
                </div>
                <?php if ($prdIdx !== '') { ?>
                    <span class="product-info-collection-product">대상 상품 <strong>#<?= htmlspecialchars($prdIdx, ENT_QUOTES, 'UTF-8') ?></strong></span>
                <?php } ?>
            </div>

            <?php if ($collectionError !== '') { ?>
                <div class="product-info-collection-validation is-error">수집정보 조회 실패: <?= htmlspecialchars($collectionError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php } ?>

            <form id="productInfoCollectionForm" novalidate>
                <div class="product-info-collection-search">
                    <span class="product-info-collection-search-label">수집 URL 검색</span>
                    <div class="product-info-collection-input">
                        <input type="url" id="collection_url" name="collection_url" placeholder="수집 대상 URL을 입력하세요" autocomplete="url">
                        <button type="submit" class="btnstyle1 btnstyle1-primary">수집시작</button>
                        <button type="button" id="firebaseWorkerPing" class="btnstyle1">DNFIX006컴 연결 확인</button>
                    </div>
                </div>

                <div class="product-info-collection-sites">
                    <h3>수집 가능한 사이트</h3>
                    <ul>
                        <li>
                            <label class="brand">브랜드</label> 타마토이즈 ex) <a href="https://tamatoys.tma.co.jp" target="_blank" rel="noopener noreferrer">https://tamatoys.tma.co.jp/item/detail/TMT-1716</a>
                        </li>
                        <li>
                            <label class="brand">브랜드</label>라이드재팬 ex) <a href="http://ridejapan.net" target="_blank" rel="noopener noreferrer">http://ridejapan.net/product_item/ftm/</a>
                        </li>
                        <li>
                            <label class="brand">브랜드</label>옐로랩 ex) <a href="https://yelolab.jp" target="_blank" rel="noopener noreferrer">https://yelolab.jp/products/hole/yelo-041</a>
                        </li>
                        <li>
                            <label class="local_supplier">현지 공급사</label>N.P.G ex) <a href="http://www.nipporigift.net" target="_blank" rel="noopener noreferrer">http://www.nipporigift.net/products/detail.php?product_id=31373</a>
                        </li>
                        <li>
                            <label class="local_supplier">현지 공급사</label>TIS (DNFIX006컴 수집) ex) <a href="https://bb-order.com/tisgoods_kr/shop/detail/TKR0003261" target="_blank" rel="noopener noreferrer">https://bb-order.com/tisgoods_kr/shop/detail/TKR0003261</a>
                        </li>
                        <li>
                            <label class="local_shopping_mall">현지 쇼핑몰</label>엠자카 ex) <a href="https://mzakka.com" target="_blank" rel="noopener noreferrer">https://mzakka.com/pc/detail/item.php?item_id=M12488&amp;category=1789</a>
                        </li>
                        <li>
                            <label class="local_shopping_mall">현지 쇼핑몰</label>노부나가 ex) <a href="https://www.nobunaga-toys.com" target="_blank" rel="noopener noreferrer">https://www.nobunaga-toys.com/?pid=193204770</a>
                        </li>
                        <li>
                            <label class="local_shopping_mall">현지 쇼핑몰</label>NLS ex) <a href="https://www.e-nls.com" target="_blank" rel="noopener noreferrer">https://www.e-nls.com/pict1-68047?c2=new</a>
                        </li>
                        <li>
                            <label class="local_shopping_mall">현지 쇼핑몰</label>엠즈 ex) <a href="https://www.ms-online.co.jp" target="_blank" rel="noopener noreferrer">https://www.ms-online.co.jp/onahole/punivirgin/UGPRO-011?pclass_id=13489</a>
                        </li>
                    </ul>
                </div>

                <div id="collectionUrlValidation" class="product-info-collection-validation" hidden aria-live="polite"></div>
            </form>

            <section id="collectionApiResult" class="product-info-collection-result" hidden>
                <h3>정보수집 반환 데이터 <small>임시 확인용</small></h3>
                <pre id="collectionApiResultData"></pre>
            </section>

            <div id="collectionLoadingOverlay" class="product-info-collection-loading" hidden aria-live="assertive" aria-busy="true">
                <div>
                    <span class="product-info-collection-spinner"></span>
                    <strong id="collectionLoadingTitle">데이터를 수집중입니다.</strong>
                    <p id="collectionLoadingDetail">완료될때까지 잠시만 기다려주세요.</p>
                    <button type="button" id="collectionLoadingCancel" class="product-info-collection-cancel" hidden>수집 중단</button>
                </div>
            </div>
        </section>

        <section class="product-image-storage-setting">
            <div>
                <h2>이미지 호스팅 저장소 설정</h2>
                <p>이 상품의 수집 이미지는 설정한 경로에만 업로드됩니다.</p>
            </div>

            <?php if ($imageStoragePath === '') { ?>
                <p class="product-image-storage-notice">이미지 저장소 설정을 완료해주세요.</p>
            <?php } else { ?>
                <p class="product-image-storage-current">저장된 이미지 저장소: <strong id="imageStoragePathCurrent"><?= htmlspecialchars($imageStoragePath, ENT_QUOTES, 'UTF-8') ?></strong> <button type="button" id="editImageStoragePath" class="btnstyle1 btnstyle1-xs">수정</button></p>
            <?php } ?>

            <form id="productImageStorageForm" class="product-image-storage-form" <?= $imageStoragePath !== '' ? 'hidden' : '' ?>>
                <input type="text" id="image_storage_path" value="<?= htmlspecialchars($imageStoragePath, ENT_QUOTES, 'UTF-8') ?>" placeholder="" autocomplete="off">
                <button type="submit" class="btnstyle1 btnstyle1-primary">저장</button>
            </form>

            <div id="productImageStorageHelp" <?= $imageStoragePath !== '' ? 'hidden' : '' ?>>
                <p class="product-image-storage-help">경로는 <code>/</code>로 시작해야 하며, 상품마다 중복될 수 없습니다.</p>
                <p class="product-image-storage-help">/goods/<code>국가코드</code>/<code>업체명(영문)</code>/<code>상품 고유번호</code>/</p>

                추천 저장소 : <b><?= htmlspecialchars($recommendedImageStoragePath, ENT_QUOTES, 'UTF-8') ?></b>
                <button type="button" id="useRecommendedImageStoragePath" class="btnstyle1 btnstyle1-xs">사용하기</button>
            </div>
            <div id="imageStoragePathMessage" class="product-image-storage-message" hidden aria-live="polite"></div>
        </section>

        <?php if (!empty($collectionItem)) { ?>
        <div class="product-collection-layout">
        <nav class="collection-record-list" aria-label="수집 데이터 목록">
            <div class="collection-record-list-heading">수집 사이트 (<?= count($collectionItems) ?>건)</div>
            <?php foreach ($collectionItems as $collectionIndex => $listItem) { ?>
                <?php
                $listItem = is_array($listItem) ? $listItem : [];
                $listSiteCode = trim((string)($listItem['site_code'] ?? ''));
                $listSiteName = $listSiteCode !== '' ? $formatSiteCodeName($listSiteCode) : '';
                $listSiteLabel = $listSiteName !== ''
                    ? $listSiteName . ' ( ' . $listSiteCode . ' )'
                    : ($listSiteCode !== '' ? $listSiteCode : '-');
                $listCollectedAt = $formatCollectedDate($listItem['collected_at'] ?? '');
                ?>
                <button
                    type="button"
                    class="collection-record-button<?= $collectionIndex === $selectedCollectionIndex ? ' is-active' : '' ?>"
                    data-collection-index="<?= (int)$collectionIndex ?>"
                    aria-pressed="<?= $collectionIndex === $selectedCollectionIndex ? 'true' : 'false' ?>"
                >
                    <strong><?= (int)$collectionIndex + 1 ?></strong>
                    <span><?= htmlspecialchars($listSiteLabel, ENT_QUOTES, 'UTF-8') ?><?php if ($listCollectedAt !== '') { ?><small><?= htmlspecialchars($listCollectedAt, ENT_QUOTES, 'UTF-8') ?></small><?php } ?></span>
                </button>
            <?php } ?>
        </nav>

        <section class="collected-product-information">
            <div class="collected-product-information-heading">
                <div>
                    <h2>수집된 상품 정보</h2>
                    <p>전체 <?= count($collectionItems) ?>건 중 <?= $selectedCollectionIndex + 1 ?>번 수집정보입니다.</p>
                </div>
                <div class="collected-product-information-actions">
                    <?php if ($canSyncNameOg || $canSyncProductCode || $canSyncBarcode || $canSyncPackageSize || $canSyncPackageWeight || $canSyncProductSize || $canSyncProductWeight || $canSyncVaginalLength || $canSyncAnalLength || $canSyncMaterial || $canSyncReleaseDate) { ?>
                        <button type="button" id="applyCollectedProductFields" class="btnstyle1 btnstyle1-primary btnstyle1-sm">선택 항목 일괄 업데이트</button>
                    <?php } ?>
                    <span>수집 <?= htmlspecialchars($formatCollectedDate($collectionItem['collected_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>

            <table class="collected-product-table">
                <tbody>
                    <tr>
                        <th>수집 URL</th>
                        <td colspan="3">
                            <?php if ($sourceUrl !== '') { ?>
                                <a href="<?= htmlspecialchars($sourceUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($sourceUrl, ENT_QUOTES, 'UTF-8') ?></a>
                            <?php } else { ?>
                                <span class="collected-no-data">No Data</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>수집일 / 수정일</th>
                        <td><?= $renderCollectedValue($formatCollectedText($collectionItem['collected_at'] ?? null)) ?><br><small><?= $renderCollectedValue($formatCollectedText($collectionItem['updated_at'] ?? null)) ?></small></td>
                        <th>사이트 코드</th>
                        <td>
                            <?= $renderCollectedValue($formatCollectedText($collectionItem['site_code'] ?? null)) ?>
                            <?php $siteCodeName = $formatSiteCodeName((string)($collectionItem['site_code'] ?? '')); ?>
                            <?php if ($siteCodeName !== '') { ?>
                                <br><small><?= htmlspecialchars($siteCodeName, ENT_QUOTES, 'UTF-8') ?></small>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>고유번호 / 품번</th>
                        <td>
                            <?= $renderCollectedValue($formatCollectedText($collectionItem['product_code'] ?? null)) ?>
                            <?php if ($canSyncProductCode) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_code2"
                                            data-field="cd_code2"
                                            data-label="상품 품번"
                                            data-value="<?= htmlspecialchars($collectedProductCode, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        상품 품번 수정
                                    </label>
                                    <span class="collected-product-compare <?= $currentProductCode === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?= $currentProductCode === ''
                                            ? '현재 상품 품번 없음'
                                            : '현재 상품 품번: ' . htmlspecialchars($currentProductCode, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </td>
                        <th>바코드</th>
                        <td>
                            <?= $renderCollectedValue($formatCollectedText($collectionItem['barcode'] ?? null)) ?>
                            <?php if ($canSyncBarcode) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_code"
                                            data-field="cd_code"
                                            data-label="바코드"
                                            data-value="<?= htmlspecialchars($collectedBarcode, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        바코드 수정
                                    </label>
                                    <span class="collected-product-compare <?= $currentBarcode === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?= $currentBarcode === ''
                                            ? '현재 바코드 없음'
                                            : '현재 바코드: ' . htmlspecialchars($currentBarcode, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>상품명</th>
                        <td colspan="3">
                            <?= $renderCollectedValue($collectedProductName === '' ? 'No Data' : $collectedProductName) ?>
                            <?php if ($canSyncNameOg) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_name_og"
                                            data-field="cd_name_og"
                                            data-label="원상품명"
                                            data-value="<?= htmlspecialchars($collectedProductName, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        원상품명 업데이트
                                    </label>
                                    <span class="collected-product-compare <?= $currentNameOg === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?= $currentNameOg === ''
                                            ? '현재 원상품명 없음'
                                            : '현재 원상품명: ' . htmlspecialchars($currentNameOg, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>공급가</th>
                        <td colspan="3"><?= $renderCollectedValue($supplyPriceText) ?></td>
                    </tr>
                    <tr>
                        <th>패키지 사이즈</th>
                        <td colspan="3">
                            <?= $renderCollectedValue($formatCollectedSize($packageSize, '깊이(D)')) ?>
                            <?php if ($canSyncPackageSize) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_size"
                                            data-field="cd_size"
                                            data-label="패키지 사이즈"
                                            data-value="<?= htmlspecialchars(json_encode($collectedPackageSizeMm, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        패키지 사이즈 수정
                                    </label>
                                    <span class="collected-product-compare <?= $currentPackageSizeLabel === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?= $currentPackageSizeLabel === ''
                                            ? '현재 패키지 사이즈 없음'
                                            : '현재 패키지 사이즈: ' . htmlspecialchars($currentPackageSizeLabel, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>패키지 중량</th>
                        <td colspan="3">
                            <?= $renderCollectedValue($formatCollectedMeasure($packageWeight)) ?>
                            <?php if ($canSyncPackageWeight) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_weight_2"
                                            data-field="cd_weight_2"
                                            data-label="전체중량"
                                            data-value="<?= htmlspecialchars($collectedPackageWeightG, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        전체중량 수정
                                    </label>
                                    <span class="collected-product-compare <?= $currentPackageWeightG === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?= $currentPackageWeightG === ''
                                            ? '현재 전체중량 없음'
                                            : '현재 전체중량: ' . htmlspecialchars($currentPackageWeightG, ENT_QUOTES, 'UTF-8') . ' g' ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>상품 사이즈</th>
                        <td colspan="3">
                            <?= $renderCollectedValue($formatCollectedSize($productSize, '길이(D)')) ?>
                            <?php if ($canSyncProductSize) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_spec_product_size"
                                            data-field="cd_spec_product_size"
                                            data-label="상품 사이즈"
                                            data-value="<?= htmlspecialchars(json_encode($collectedProductSizeCm, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        상품 사이즈 수정
                                    </label>
                                    <span class="collected-product-compare <?= $currentProductSizeLabel === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?= $currentProductSizeLabel === ''
                                            ? '현재 스펙 가로/세로/깊이 없음'
                                            : '현재 스펙 가로/세로/깊이: ' . htmlspecialchars($currentProductSizeLabel, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>상품 중량</th>
                        <td colspan="3">
                            <?= $renderCollectedValue($formatCollectedMeasure($productWeight)) ?>
                            <?php if ($canSyncProductWeight) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_weight_1"
                                            data-field="cd_weight_1"
                                            data-label="상품중량"
                                            data-value="<?= htmlspecialchars($collectedProductWeightG, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        <?= $hasSpecWeight ? '상품중량 · 스펙 상품중량 수정' : '상품중량 수정' ?>
                                    </label>
                                    <span class="collected-product-compare <?= $currentProductWeightG === '' && $currentSpecWeight === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?php
                                        $productWeightCompareParts = [];
                                        $productWeightCompareParts[] = $currentProductWeightG === ''
                                            ? '현재 상품중량 없음'
                                            : '현재 상품중량: ' . htmlspecialchars($currentProductWeightG, ENT_QUOTES, 'UTF-8') . ' g';
                                        if ($hasSpecWeight) {
                                            $specWeightUnitLabel = $specWeightUnit !== '' ? $specWeightUnit : 'g';
                                            $productWeightCompareParts[] = $currentSpecWeight === ''
                                                ? '스펙 상품중량 없음'
                                                : '스펙 상품중량: ' . htmlspecialchars($currentSpecWeight, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($specWeightUnitLabel, ENT_QUOTES, 'UTF-8');
                                        }
                                        echo implode(' / ', $productWeightCompareParts);
                                        ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>내부 길이</th>
                        <td colspan="3">
                            질 길이: <?= $renderCollectedValue($formatCollectedMeasure($vaginalInternalLength)) ?>
                            &nbsp; / &nbsp;
                            애널 길이: <?= $renderCollectedValue($formatCollectedMeasure($analInternalLength)) ?>
                            <?php if ($canSyncVaginalLength) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_spec_inner_length_vagina"
                                            data-field="cd_spec_inner_length_vagina"
                                            data-label="내부길이 (질)"
                                            data-value="<?= htmlspecialchars($collectedVaginalLengthCm, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        <?= $isOnaholeSpec ? '내부길이 (질) · 내부길이 수정' : '내부길이 (질) 수정' ?>
                                    </label>
                                    <span class="collected-product-compare <?= $currentVaginalLengthLabel === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?= $currentVaginalLengthLabel === ''
                                            ? '현재 내부길이 (질) 없음'
                                            : '현재 내부길이 (질): ' . htmlspecialchars($currentVaginalLengthLabel, ENT_QUOTES, 'UTF-8') . ' cm'
                                                . ($isOnaholeSpec && $currentSize2Cm !== '' && $currentSize2Cm !== $currentVaginalLengthCm
                                                    ? ' / 내부길이: ' . htmlspecialchars($currentSize2Cm, ENT_QUOTES, 'UTF-8') . ' cm'
                                                    : '') ?>
                                    </span>
                                </div>
                            <?php } ?>
                            <?php if ($canSyncAnalLength) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_spec_inner_length_anal"
                                            data-field="cd_spec_inner_length_anal"
                                            data-label="내부길이 (애널)"
                                            data-value="<?= htmlspecialchars($collectedAnalLengthCm, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        내부길이 (애널) 수정
                                    </label>
                                    <span class="collected-product-compare <?= $currentAnalLengthLabel === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?= $currentAnalLengthLabel === ''
                                            ? '현재 내부길이 (애널) 없음'
                                            : '현재 내부길이 (애널): ' . htmlspecialchars($currentAnalLengthLabel, ENT_QUOTES, 'UTF-8') . ' cm' ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>소재</th>
                        <td colspan="3">
                            <?php if ($collectedMaterial === '') { ?>
                                <?= $renderCollectedValue('No Data') ?>
                            <?php } elseif ($collectedMaterialMapped !== '') { ?>
                                <?= htmlspecialchars($collectedMaterialMapped, ENT_QUOTES, 'UTF-8') ?>
                                <br><small>원문: <?= htmlspecialchars($collectedMaterialRaw, ENT_QUOTES, 'UTF-8') ?></small>
                            <?php } else { ?>
                                <?= $renderCollectedValue($formatCollectedText($collectedMaterialRaw !== '' ? $collectedMaterialRaw : null)) ?>
                            <?php } ?>
                            <?php if ($canSyncMaterial) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_spec_material"
                                            data-field="cd_spec_material"
                                            data-label="소재"
                                            data-value="<?= htmlspecialchars($collectedMaterial, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        소재 수정
                                    </label>
                                    <span class="collected-product-compare <?= $currentMaterial === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?= $currentMaterial === ''
                                            ? '현재 소재 없음'
                                            : '현재 소재: ' . htmlspecialchars($currentMaterial, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>상품구분</th>
                        <td colspan="3"><?= $renderCollectedValue($formatCollectedText($productType !== '' ? $productType : null)) ?></td>
                    </tr>
                    <tr>
                        <th>제조국</th>
                        <td colspan="3"><?= $renderCollectedValue($formatCollectedText($countryOfOrigin !== '' ? $countryOfOrigin : null)) ?></td>
                    </tr>
                    <tr>
                        <th>발매일</th>
                        <td colspan="3">
                            <?= $renderCollectedValue($formatCollectedText($registrationDateText !== '' ? $registrationDateText : null)) ?>
                            <?php if ($canSyncReleaseDate) { ?>
                                <div>
                                    <label class="collected-product-sync">
                                        <input
                                            type="checkbox"
                                            class="collected-product-sync-check"
                                            name="collection_sync_fields[]"
                                            value="cd_release_date"
                                            data-field="cd_release_date"
                                            data-label="출시일"
                                            data-value="<?= htmlspecialchars($collectedReleaseDate, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        출시일 수정
                                    </label>
                                    <span class="collected-product-compare <?= $currentReleaseDate === '' ? 'collected-product-compare-empty' : 'collected-product-compare-mismatch' ?>">
                                        <?= $currentReleaseDate === ''
                                            ? '현재 출시일 없음'
                                            : '현재 출시일: ' . htmlspecialchars($currentReleaseDate, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <th>부속품</th>
                        <td colspan="3">
                            <div id="collectionAccessoriesOriginal" class="collection-source-block"><?= $renderCollectedValue($formatCollectedText($accessoriesText !== '' ? $accessoriesText : null)) ?></div>
                            <div class="collection-action-block">
                                <button type="button" class="btnstyle1 btnstyle1-xs collection-copy-button" data-copy-target="collectionAccessoriesOriginal">원문 복사</button>
                                <?php if ($collectionItemIdx > 0) { ?>
                                    <button type="button" class="btnstyle1 btnstyle1-xs collection-translation-button" data-field="accessories" data-label="부속품" data-value="<?= htmlspecialchars($translatedAccessories, ENT_QUOTES, 'UTF-8') ?>"><?= $translatedAccessories === '' ? '번역데이터 입력' : '번역 수정' ?></button>
                                <?php } ?>
                            </div>
                            <?php if ($translatedAccessories !== '') { ?>
                                <div class="collection-translation">
                                    <span class="collection-translation-label">번역</span>
                                    <div class="collection-translation-text"><?= htmlspecialchars($translatedAccessories, ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                                <?php if ($translationLogs['accessories'] !== null) { ?><div class="collection-action-log">번역 수정: <?= htmlspecialchars($formatActionLog($translationLogs['accessories']), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
                            <?php } ?>
                            <?php if ($showAccessoryMissingWarning) { ?>
                                <div class="collected-product-compare collected-product-compare-empty">부속품 정보가 상품 DB에 없습니다! 정보를 기입해주세요.</div>
                            <?php } ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>메이커 코멘트</th>
                        <td colspan="3" class="collected-product-comment">
                            <div id="collectionMakerCommentOriginal" class="collection-source-block"><?= $makerCommentText !== '' ? nl2br(htmlspecialchars($makerCommentText, ENT_QUOTES, 'UTF-8')) : '<span class="collected-no-data">No Data</span>' ?></div>
                            <div class="collection-action-block">
                                <button type="button" class="btnstyle1 btnstyle1-xs collection-copy-button" data-copy-target="collectionMakerCommentOriginal">원문 복사</button>
                                <?php if ($collectionItemIdx > 0) { ?>
                                    <button type="button" class="btnstyle1 btnstyle1-xs collection-translation-button" data-field="maker_comment" data-label="메이커 코멘트" data-value="<?= htmlspecialchars($translatedMakerComment, ENT_QUOTES, 'UTF-8') ?>"><?= $translatedMakerComment === '' ? '번역데이터 입력' : '번역 수정' ?></button>
                                <?php } ?>
                            </div>
                            <?php if ($translatedMakerComment !== '') { ?>
                                <div class="collection-translation">
                                    <span class="collection-translation-label">번역</span>
                                    <div class="collection-translation-text"><?= nl2br(htmlspecialchars(html_entity_decode($translatedMakerComment, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8')) ?></div>
                                </div>
                                <?php if ($translationLogs['maker_comment'] !== null) { ?><div class="collection-action-log">번역 수정: <?= htmlspecialchars($formatActionLog($translationLogs['maker_comment']), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>판매사 코멘트</th>
                        <td colspan="3" class="collected-product-comment">
                            <div id="collectionSellerCommentOriginal" class="collection-source-block"><?= $sellerCommentText !== '' ? nl2br(htmlspecialchars($sellerCommentText, ENT_QUOTES, 'UTF-8')) : '<span class="collected-no-data">No Data</span>' ?></div>
                            <div class="collection-action-block">
                                <button type="button" class="btnstyle1 btnstyle1-xs collection-copy-button" data-copy-target="collectionSellerCommentOriginal">원문 복사</button>
                                <?php if ($collectionItemIdx > 0) { ?>
                                    <button type="button" class="btnstyle1 btnstyle1-xs collection-translation-button" data-field="seller_comment" data-label="판매사 코멘트" data-value="<?= htmlspecialchars($translatedSellerComment, ENT_QUOTES, 'UTF-8') ?>"><?= $translatedSellerComment === '' ? '번역데이터 입력' : '번역 수정' ?></button>
                                <?php } ?>
                            </div>
                            <?php if ($translatedSellerComment !== '') { ?>
                                <div class="collection-translation">
                                    <span class="collection-translation-label">번역</span>
                                    <div class="collection-translation-text"><?= nl2br(htmlspecialchars(html_entity_decode($translatedSellerComment, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8')) ?></div>
                                </div>
                                <?php if ($translationLogs['seller_comment'] !== null) { ?><div class="collection-action-log">번역 수정: <?= htmlspecialchars($formatActionLog($translationLogs['seller_comment']), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php if (!empty($imageUrls)) { ?>
                <div class="collected-product-images">
                    <div class="collected-product-images-title">
                        <h3>수집 이미지</h3>
                        <span><?= count($imageUrls) ?>장</span>
                        <button type="button" id="copyCollectedImageHtml" class="btnstyle1 btnstyle1-sm">HTML 복사</button>
                        <a href="/admin/product/info_collect/images/download?<?= htmlspecialchars(http_build_query(['prd_idx' => $prdIdx, 'collection_index' => $selectedCollectionIndex]), ENT_QUOTES, 'UTF-8') ?>" class="btnstyle1 btnstyle1-sm">이미지 일괄 다운로드</a>
                        <?php if ($imageStoragePath !== '') { ?>
                            <button type="button" id="uploadCollectedImagesToHosting" class="btnstyle1 btnstyle1-primary btnstyle1-sm" data-collection-index="<?= $selectedCollectionIndex ?>">이미지 호스팅 업로드</button>
                        <?php } else { ?>
                            <span class="collected-product-upload-disabled">이미지 저장소 설정 후 업로드 가능</span>
                        <?php } ?>
                    </div>
                    <textarea id="collectedProductImageHtml" readonly class="collected-product-image-html"><?= htmlspecialchars($detailImageHtml, ENT_QUOTES, 'UTF-8') ?></textarea>
                    <div class="collected-product-image-list">
                        <?php foreach ($collectedImages as $collectedImage) { ?>
                            <?php
                            $imageUrl = (string)($collectedImage['url'] ?? '');
                            $imageAlt = trim((string)($collectedImage['alt'] ?? ''));
                            $imageKey = trim((string)($collectedImage['key'] ?? ''));
                            $translatedImageAlt = trim((string)($collectedImage['translated_alt'] ?? ''));
                            ?>
                            <div class="collected-product-image-item">
                                <a class="collected-product-image-preview" href="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><img src="<?= htmlspecialchars($imageProxyUrl($imageUrl), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($imageAlt !== '' ? $imageAlt : '수집 상품 이미지', ENT_QUOTES, 'UTF-8') ?>" referrerpolicy="no-referrer" ></a>
                                <a class="collected-product-image-download" href="<?= htmlspecialchars($imageProxyUrl($imageUrl) . '&download=1', ENT_QUOTES, 'UTF-8') ?>">이미지 다운로드</a>
                                <?php if ($imageAlt !== '' || $translatedImageAlt !== '') { ?>
                                    <div class="collected-product-image-alt-wrap">
                                        <?php if ($imageAlt !== '') { ?>
                                            <p class="collected-product-image-alt"><?= htmlspecialchars($imageAlt, ENT_QUOTES, 'UTF-8') ?></p>
                                        <?php } ?>
                                        <?php if ($collectionItemIdx > 0 && $imageKey !== '') { ?>
                                            <button
                                                type="button"
                                                class="btnstyle1 btnstyle1-xs collection-translation-button"
                                                data-field="image_alt"
                                                data-label="이미지 설명"
                                                data-image-key="<?= htmlspecialchars($imageKey, ENT_QUOTES, 'UTF-8') ?>"
                                                data-source-alt="<?= htmlspecialchars($imageAlt, ENT_QUOTES, 'UTF-8') ?>"
                                                data-value="<?= htmlspecialchars($translatedImageAlt, ENT_QUOTES, 'UTF-8') ?>"
                                            ><?= $translatedImageAlt === '' ? '번역데이터 입력' : '번역 수정' ?></button>
                                        <?php } ?>
                                        <?php if ($translatedImageAlt !== '') { ?>
                                            <div class="collection-translation">
                                                <span class="collection-translation-label">번역</span>
                                                <div class="collection-translation-text"><?= htmlspecialchars($translatedImageAlt, ENT_QUOTES, 'UTF-8') ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

        <?php if (!empty($hostingImageUrls)) { ?>
        <section class="hosted-product-images">
            <div class="hosted-product-images-title">
                <h2>이미지 호스팅 등록 이미지</h2>
                <span><?= count($hostingImageUrls) ?>장</span>
                <button type="button" id="copyHostedImageHtml" class="btnstyle1 btnstyle1-sm">HTML 복사</button>
            </div>
            <?php if ($hostingUploadLog !== null) { ?><p class="collection-action-log hosted-action-log">이미지 호스팅 업로드: <?= htmlspecialchars($formatActionLog($hostingUploadLog), ENT_QUOTES, 'UTF-8') ?></p><?php } ?>
            <textarea id="hostedProductImageHtml" readonly class="collected-product-image-html"><?= htmlspecialchars($hostingImageHtml, ENT_QUOTES, 'UTF-8') ?></textarea>
            <p class="hosted-product-images-help">이미지를 드래그하여 순서를 변경하면 HTML 코드와 저장 순서가 함께 갱신됩니다.</p>
            <div id="hostedProductImageList" class="hosted-product-image-list" data-collection-item-idx="<?= $hostingCollectionItemIdx ?>">
                <?php foreach ($hostingImageUrls as $hostingImageUrl) { ?>
                    <?php $hostingImageFilename = basename((string)(parse_url($hostingImageUrl, PHP_URL_PATH) ?? '')); ?>
                    <a href="<?= htmlspecialchars($hostingImageUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" draggable="true" data-hosting-image-url="<?= htmlspecialchars($hostingImageUrl, ENT_QUOTES, 'UTF-8') ?>">
                        <span class="hosted-product-image-labels"><span>이미지호스팅</span><strong><?= htmlspecialchars($hostingImageFilename, ENT_QUOTES, 'UTF-8') ?></strong></span>
                        <img src="<?= htmlspecialchars($hostingImageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="이미지 호스팅 등록 이미지">
                    </a>
                <?php } ?>
            </div>
        </section>
        <?php } ?>
        </section>
        </div>
        <?php } ?>


<div id="collectionTranslationModal" class="collection-translation-modal" hidden>
    <div class="collection-translation-modal-card">
        <div class="collection-translation-modal-heading">
            <h3 id="collectionTranslationModalTitle">번역데이터 입력</h3>
            <button type="button" id="closeCollectionTranslationModal" aria-label="닫기">×</button>
        </div>
        <textarea id="collectionTranslationInput" rows="8" placeholder="번역 내용을 입력하세요."></textarea>
        <div class="collection-translation-modal-actions">
            <button type="button" id="saveCollectionTranslation" class="btnstyle1 btnstyle1-primary">번역 저장</button>
            <button type="button" id="cancelCollectionTranslation" class="btnstyle1">취소</button>
        </div>
    </div>
</div>

<style>
.product-info-collection{position:relative;max-width:900px;padding:26px 28px;border:1px solid #e4e8ef;border-radius:10px;background:#fff;box-shadow:0 3px 12px rgba(15,23,42,.04)}
.product-info-collection-heading{display:flex;justify-content:space-between;gap:16px;align-items:flex-start; padding-bottom:10px;
}
.product-info-collection-search{margin:2px 0 12px;padding:14px;border:1px solid #f6d98a;border-radius:8px;background:linear-gradient(180deg,#fff8e1 0%,#ffefc2 100%);box-shadow:inset 0 1px 0 rgba(255,255,255,.8)}
.product-info-collection-search-label{display:inline-flex;align-items:center;margin-bottom:8px;padding:3px 8px;border-radius:999px;background:#f59e0b;color:#fff;font-size:11px;font-weight:700;letter-spacing:.02em}
.product-info-collection-sites{margin:4px 0 12px}
.product-info-collection-sites h3{margin:0 0 8px;font-size:14px;color:#374151}
.product-info-collection-sites ul{list-style:none;margin:0;padding:0;display:grid;gap:4px; }
.product-info-collection-sites li{display:flex;align-items:center;gap:6px;min-width:0;white-space:nowrap;font-size:12px;line-height:1.4;color:#475569}
.product-info-collection-sites li a{min-width:0;overflow:hidden;text-overflow:ellipsis;color:#2563eb}
.product-info-collection-sites label{display:inline-flex;align-items:center;flex:0 0 auto;margin:0;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;line-height:1.4;color:#fff}
.product-info-collection-sites label.brand{background:#2563eb}
.product-info-collection-sites label.local_supplier{background:#0d9488}
.product-info-collection-sites label.local_shopping_mall{background:#7c3aed}
.product-info-collection-heading h2{margin:0 0 6px;font-size:19px;color:#1f2937}.product-info-collection-heading p{margin:0;color:#6b7280;font-size:13px}.product-info-collection-product{flex:0 0 auto;padding:7px 10px;border-radius:5px;background:#f3f6fa;color:#64748b;font-size:12px}.product-info-collection-product strong{color:#334155}
.product-info-collection label{display:block; color:#fff; }
.product-info-collection-input{display:flex;gap:8px}.product-info-collection-input input{box-sizing:border-box;flex:1;min-width:0;height:46px;padding:0 14px;border:2px solid #f0b429;border-radius:6px;background:#fff;color:#1f2937;font-size:15px;box-shadow:0 1px 2px rgba(146,64,14,.08)}.product-info-collection-input input::placeholder{color:#b45309;opacity:.72}.product-info-collection-input input:focus{outline:0;border-color:#d97706;box-shadow:0 0 0 3px rgba(245,158,11,.28)}.product-info-collection-input button{min-width:90px}.product-info-collection-help{margin:9px 0 0;color:#333;font-size:12px;line-height:1.5}.product-info-collection-help a{color:#333;text-decoration:underline}.product-info-collection-help code{padding:1px 4px;border-radius:3px;background:#f1f5f9;color:#475569}.product-info-collection-validation{margin-top:16px;padding:11px 13px;border-radius:5px;font-size:13px}.product-info-collection-validation.is-success{color:#166534;background:#f0fdf4;border:1px solid #bbf7d0}.product-info-collection-validation.is-error{color:#b91c1c;background:#fef2f2;border:1px solid #fecaca}.product-info-collection-result{margin-top:22px;padding-top:20px;border-top:1px solid #e5e7eb}.product-info-collection-result h3{margin:0 0 9px;font-size:14px;color:#374151}.product-info-collection-result h3 small{margin-left:5px;color:#94a3b8;font-weight:400}.product-info-collection-result pre{max-height:460px;margin:0;padding:14px;overflow:auto;border-radius:6px;background:#0f172a;color:#e2e8f0;white-space:pre-wrap;word-break:break-word;font:12px/1.55 Consolas,Monaco,monospace}.product-info-collection-loading{position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,.56);text-align:center}.product-info-collection-loading>div{min-width:280px;padding:28px 36px;border-radius:10px;background:#fff;box-shadow:0 18px 40px rgba(0,0,0,.22);color:#1f2937}.product-info-collection-loading strong{display:block;margin-top:14px;font-size:16px}.product-info-collection-loading p{margin:7px 0 0;color:#64748b;font-size:13px}.product-info-collection-spinner{display:inline-block;width:32px;height:32px;border:4px solid #dbeafe;border-top-color:#2563eb;border-radius:50%;animation:collection-spin .8s linear infinite}@keyframes collection-spin{to{transform:rotate(360deg)}}.collected-product-information{flex:1;min-width:0;max-width:900px;margin-top:20px;border:1px solid #dfe5ed;border-radius:10px;background:#fff;overflow:hidden}.collected-product-information-heading{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;background:#f8fafc;border-bottom:1px solid #e5e7eb}.collected-product-information-heading h2{margin:0 0 4px;font-size:17px;color:#1e293b}.collected-product-information-heading p,.collected-product-information-heading span{margin:0;color:#64748b;font-size:12px}.collected-product-table{width:100%;border-collapse:collapse}.collected-product-table th,.collected-product-table td{padding:11px 13px;border-bottom:1px solid #edf0f4;text-align:left;vertical-align:top;font-size:13px;line-height:1.55}.collected-product-table th{width:135px;background:#f8fafc;color:#475569;font-weight:600}.collected-product-table td a{color:#2563eb;word-break:break-all}.collected-no-data{color:#b0b8c4}.collected-product-comment{white-space:normal;color:#475569}.collected-product-images{padding:20px}.collected-product-images-title{display:flex;align-items:center;gap:8px;margin-bottom:10px}.collected-product-images-title h3{margin:0;font-size:14px;color:#334155}.collected-product-images-title span{padding:2px 6px;border-radius:10px;background:#eef2ff;color:#4f46e5;font-size:11px}.collected-product-image-html{box-sizing:border-box;width:100%;height:75px;margin-bottom:14px;padding:9px;border:1px solid #d7dee8;border-radius:5px;resize:vertical;font:11px/1.4 Consolas,monospace}.collected-product-image-list{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}.collected-product-image-list a{display:block;overflow:hidden;border:1px solid #e2e8f0;border-radius:5px;background:#f8fafc}.collected-product-image-list img{display:block;width:100%;aspect-ratio:1;object-fit:contain}@media(max-width:640px){.product-info-collection{padding:20px}.product-info-collection-heading{display:block}.product-info-collection-product{display:inline-block;margin-top:12px}.product-info-collection-input{display:block}.product-info-collection-input button{width:100%;margin-top:8px}.collected-product-information-heading{display:block}.collected-product-information-heading span{display:block;margin-top:6px}.collected-product-table th{width:100px}.collected-product-image-list{grid-template-columns:repeat(2,minmax(0,1fr))}}
.product-info-collection-loading[hidden]{display:none}
.product-info-collection-cancel{margin-top:16px;min-width:120px;height:36px;padding:0 16px;border:1px solid #cbd5e1;border-radius:6px;background:#fff;color:#334155;font-size:13px;font-weight:700;cursor:pointer}
.product-info-collection-cancel:hover{background:#f8fafc;border-color:#94a3b8}
.product-info-collection-cancel[hidden]{display:none}
.product-collection-layout{display:flex;align-items:flex-start;gap:16px;max-width:1136px}
.collection-record-list{position:sticky; top:90px; z-index:20; box-sizing:border-box;order:2;display:grid;gap:6px;flex:0 0 220px;width:220px;max-width:220px;margin:20px 0 0;padding:10px;border:1px solid #dfe5ed;border-radius:10px;background:#f8fafc;max-height:calc(100vh - 32px);overflow:auto}
.collection-record-list-heading{padding:2px 2px 6px;color:#1e293b;font-size:13px;font-weight:700}
.collection-record-button{display:flex;align-items:flex-start;gap:8px;width:100%;padding:8px;border:1px solid #dce3ed;border-radius:6px;background:#fff;color:#475569;text-align:left;cursor:pointer}
.collection-record-button:hover{border-color:#93a8e8;background:#f5f7ff}
.collection-record-button strong{display:inline-flex;align-items:center;justify-content:center;flex:0 0 22px;width:22px;height:22px;border-radius:50%;background:#eef2f7;color:#64748b;font-size:11px}
.collection-record-button span{min-width:0;display:flex;flex-direction:column;gap:2px;font-size:12px;line-height:1.35;word-break:break-all}
.collection-record-button span small{color:#94a3b8;font-size:11px;font-weight:400}
.collection-record-button.is-active{border-color:#5975dd;background:#eef3ff;color:#1e3a8a;font-weight:700}
.collection-record-button.is-active strong{background:#5975dd;color:#fff}
.collection-record-button.is-active span small{color:#64748b}
.collection-record-button:disabled{cursor:wait;opacity:.65}
@media(max-width:640px){.product-collection-layout{display:block}.collection-record-list{position:static;order:0;width:100%;max-width:220px;margin:20px 0 0 auto}}
.collected-product-information-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.collected-product-sync{display:inline-flex;align-items:center;gap:6px;margin:8px 8px 0 0;color:#1e3a8a;font-size:12px;font-weight:700;cursor:pointer}
.collected-product-sync input{margin:0}
.collected-product-compare{display:inline-block;max-width:100%;margin-top:8px;padding:3px 7px;border-radius:4px;font-size:11px;font-weight:600;word-break:break-all}.collected-product-compare-empty{color:#92400e;background:#fef3c7}.collected-product-compare-mismatch{color:#b91c1c;background:#fee2e2}
.collection-translation{margin-top:8px;padding:8px 10px;border:1px solid #e5e7eb;border-left:3px solid #6b7280;background:#f4f4f5;color:#1f2937}
.collection-translation-button{margin-top:8px}
.collection-translation-label{display:inline-block;margin:0 0 6px;padding:2px 8px;border-radius:999px;background:#4b5563;color:#fff;font-size:11px;font-weight:700;line-height:1.3;letter-spacing:.02em}
.collection-translation-text{display:block;color:#1f2937;line-height:1.55;word-break:break-word}.collection-translation-modal{position:fixed;inset:0;z-index:10001;display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,.55)}.collection-translation-modal[hidden]{display:none}.collection-translation-modal-card{width:min(560px,calc(100% - 32px));padding:20px;border-radius:9px;background:#fff;box-shadow:0 20px 50px rgba(0,0,0,.25)}.collection-translation-modal-heading{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}.collection-translation-modal-heading h3{margin:0;font-size:16px}.collection-translation-modal-heading button{border:0;background:transparent;color:#64748b;font-size:24px;cursor:pointer}.collection-translation-modal textarea{box-sizing:border-box;width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:5px;resize:vertical;line-height:1.5}.collection-translation-modal-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:14px}
.collection-source-block{padding:9px 10px;border:1px solid #e2e8f0;border-radius:5px;background:#fff;color:#334155}.collection-action-block{display:flex;gap:6px;margin-top:8px;padding:7px 8px;border:1px solid #e2e8f0;border-radius:5px;background:#f8fafc}.collection-action-block .collection-translation-button{margin-top:0}
.collection-action-log{margin-top:6px;color:#64748b;font-size:11px}.hosted-action-log{margin:0 0 10px}
.collected-product-image-item{overflow:hidden;border:1px solid #e2e8f0;border-radius:5px;background:#f8fafc}.collected-product-image-list .collected-product-image-preview{display:block;border:0;border-radius:0}.collected-product-image-alt-wrap{padding:8px;background:#fff;border-top:1px solid #edf0f4}
.collected-product-image-alt{margin:0;color:#475569;font-size:11px;line-height:1.45;word-break:break-word}
.collected-product-image-alt-wrap .collection-translation-button{margin:6px 0 0}
.collected-product-image-alt-wrap .collection-translation{margin-top:6px}.collected-product-image-download{display:block;padding:7px;text-align:center;background:#fff;color:#2563eb!important;font-size:11px;text-decoration:none}
.collected-product-upload-disabled{color:#92400e!important;background:#fef3c7!important}
.hosted-product-images{
    max-width:900px;
    margin:20px;

    padding:20px 22px;
    border:1px solid #bbf7d0;border-radius:10px;background:#f0fdf4}.hosted-product-images-title{display:flex;align-items:center;gap:8px;margin-bottom:12px}.hosted-product-images-title h2{margin:0;font-size:16px;color:#166534}.hosted-product-images-title span{padding:2px 7px;border-radius:10px;background:#bbf7d0;color:#166534;font-size:11px}.hosted-product-image-list{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}.hosted-product-image-list a{display:block;overflow:hidden;border:1px solid #86efac;border-radius:5px;background:#fff}.hosted-product-image-list img{display:block;width:100%;aspect-ratio:1;object-fit:contain}@media(max-width:640px){.hosted-product-image-list{grid-template-columns:repeat(2,minmax(0,1fr))}}
.hosted-product-image-labels{display:flex;align-items:center;gap:4px;min-width:0;padding:6px 7px;border-bottom:1px solid #dcfce7;font-size:10px}.hosted-product-image-labels span{flex:0 0 auto;padding:2px 4px;border-radius:3px;background:#dcfce7;color:#166534;font-weight:700}.hosted-product-image-labels strong{min-width:0;overflow:hidden;color:#334155;text-overflow:ellipsis;white-space:nowrap;font-weight:600}
.hosted-product-images-help{margin:0 0 10px;color:#64748b;font-size:12px}.hosted-product-image-list a[draggable="true"]{cursor:grab}.hosted-product-image-list a.is-dragging{opacity:.4}.hosted-product-image-list a.is-drop-target{outline:2px dashed #16a34a;outline-offset:2px}
.product-image-storage-setting{max-width:900px;margin-top:20px;padding:20px 22px;border:1px solid #dfe5ed;border-radius:10px;background:#fff}.product-image-storage-setting h2{margin:0 0 4px;font-size:16px;color:#1e293b}.product-image-storage-setting p{margin:0;color:#64748b;font-size:12px}.product-image-storage-notice{margin-top:14px!important;padding:9px 11px;border-radius:5px;color:#92400e!important;background:#fef3c7}.product-image-storage-form{display:flex;gap:8px;margin-top:14px}.product-image-storage-form input{box-sizing:border-box;flex:1;height:38px;padding:0 10px;border:1px solid #cbd5e1;border-radius:5px;font-size:13px}.product-image-storage-help{margin-top:7px!important}.product-image-storage-help code{padding:1px 4px;border-radius:3px;background:#f1f5f9}.product-image-storage-message{margin-top:10px;padding:9px 11px;border-radius:5px;font-size:12px}.product-image-storage-message.is-success{color:#166534;background:#dcfce7}.product-image-storage-message.is-error{color:#b91c1c;background:#fee2e2}
.product-image-storage-current{margin-top:14px!important;padding:9px 11px;border-radius:5px;color:#166534!important;background:#dcfce7}.product-image-storage-current strong{font-family:Consolas,monospace}
.product-image-storage-form[hidden],#productImageStorageHelp[hidden]{display:none}
</style>
<script>
(function () {
    var form = document.getElementById('productInfoCollectionForm');
    var input = document.getElementById('collection_url');
    var message = document.getElementById('collectionUrlValidation');
    var submitButton = form.querySelector('button[type="submit"]');
    var pingButton = document.getElementById('firebaseWorkerPing');
    var productIdx = <?= json_encode($prdIdx, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var collectionItemIdx = <?= json_encode($collectionItemIdx) ?>;
    var selectedCollectionIndex = <?= json_encode($selectedCollectionIndex) ?>;
    var collectionSiteCode = <?= json_encode(trim((string)($collectionItem['site_code'] ?? '')), JSON_UNESCAPED_UNICODE) ?>;
    var collectionRecordButtons = document.querySelectorAll('.collection-record-button');
    var apiResult = document.getElementById('collectionApiResult');
    var apiResultData = document.getElementById('collectionApiResultData');
    var loadingOverlay = document.getElementById('collectionLoadingOverlay');
    var loadingTitle = document.getElementById('collectionLoadingTitle');
    var loadingDetail = document.getElementById('collectionLoadingDetail');
    var loadingCancelButton = document.getElementById('collectionLoadingCancel');
    var collectionWaitActive = false;
    var collectionRequestAbort = null;
    var collectionPollTimer = null;
    var collectionActiveJobId = '';
    var copyImageHtmlButton = document.getElementById('copyCollectedImageHtml');
    var imageHtmlTextarea = document.getElementById('collectedProductImageHtml');
    var copyHostedImageHtmlButton = document.getElementById('copyHostedImageHtml');
    var hostedImageHtmlTextarea = document.getElementById('hostedProductImageHtml');
    var hostedImageList = document.getElementById('hostedProductImageList');
    var uploadImagesButton = document.getElementById('uploadCollectedImagesToHosting');
    var imageStorageForm = document.getElementById('productImageStorageForm');
    var imageStorageInput = document.getElementById('image_storage_path');
    var imageStorageMessage = document.getElementById('imageStoragePathMessage');
    var editImageStorageButton = document.getElementById('editImageStoragePath');
    var imageStorageHelp = document.getElementById('productImageStorageHelp');
    var useRecommendedImageStoragePathButton = document.getElementById('useRecommendedImageStoragePath');
    var translationModal = document.getElementById('collectionTranslationModal');
    var translationModalTitle = document.getElementById('collectionTranslationModalTitle');
    var translationInput = document.getElementById('collectionTranslationInput');
    var translationField = '';
    var translationImageKey = '';
    var translationSourceAlt = '';

    function showMessage(text, isSuccess) {
        message.textContent = text;
        message.hidden = false;
        message.className = 'product-info-collection-validation ' + (isSuccess ? 'is-success' : 'is-error');
    }

    function setCollectionLoading(isLoading, options) {
        options = options || {};
        loadingOverlay.hidden = !isLoading;
        if (loadingCancelButton) {
            loadingCancelButton.hidden = !isLoading || !options.canCancel;
        }
        input.disabled = isLoading;
        submitButton.disabled = isLoading;
        submitButton.textContent = isLoading ? '수집 중...' : '수집시작';
        if (pingButton) {
            pingButton.disabled = isLoading;
        }
    }

    function setCollectionLoadingCopy(title, detail) {
        if (loadingTitle) {
            loadingTitle.textContent = title;
        }
        if (loadingDetail) {
            loadingDetail.textContent = detail;
        }
    }

    function stopCollectionWait() {
        collectionWaitActive = false;
        collectionActiveJobId = '';
        if (collectionPollTimer) {
            clearTimeout(collectionPollTimer);
            collectionPollTimer = null;
        }
        if (collectionRequestAbort) {
            collectionRequestAbort.abort();
            collectionRequestAbort = null;
        }
        setCollectionLoading(false);
    }

    function notifyCollectionJobCancel(jobId) {
        if (!jobId) {
            return;
        }
        fetch('/admin/product/info_collect/job/cancel', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams({ job_id: jobId }).toString()
        }).catch(function () {});
    }

    function cancelCollectionWait() {
        var jobId = collectionActiveJobId;
        stopCollectionWait();
        showMessage('수집을 중단했습니다.', false);
        notifyCollectionJobCancel(jobId);
    }

    function pollFirebaseCollectionJob(jobId, options) {
        options = options || {};
        collectionActiveJobId = jobId;
        var queuedAt = Date.now();
        var seenRunning = false;
        var deadlineAt = Date.now() + (options.timeoutMs || 150000);
        var noResponseMs = options.noResponseMs || 45000;

        function scheduleNext() {
            collectionPollTimer = setTimeout(tick, 2000);
        }

        function tick() {
            if (!collectionWaitActive || collectionActiveJobId !== jobId) {
                return;
            }
            if (!seenRunning && Date.now() - queuedAt > noResponseMs) {
                stopCollectionWait();
                showMessage('DNFIX006컴 응답이 없습니다. 수집기 앱이 켜져 있는지 확인하세요.', false);
                notifyCollectionJobCancel(jobId);
                return;
            }
            if (Date.now() > deadlineAt) {
                stopCollectionWait();
                showMessage('DNFIX006컴 수집 대기 시간이 초과되었습니다.', false);
                notifyCollectionJobCancel(jobId);
                return;
            }
            fetch('/admin/product/info_collect/job?job_id=' + encodeURIComponent(jobId), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function (response) {
                return response.json().catch(function () {
                    throw new Error('수집 상태를 읽을 수 없습니다.');
                });
            })
            .then(function (jobData) {
                if (!collectionWaitActive || collectionActiveJobId !== jobId) {
                    return;
                }
                if (!jobData.success) {
                    throw new Error(jobData.message || '수집 상태를 확인할 수 없습니다.');
                }
                var status = String(jobData.status || '').toLowerCase();
                var result = jobData.result || {};
                if (status === 'running') {
                    seenRunning = true;
                }
                if (status === 'done' && result.ok !== false) {
                    if (typeof options.onDone === 'function') {
                        stopCollectionWait();
                        options.onDone(jobData);
                        return;
                    }
                    window.location.reload();
                    return;
                }
                if (status === 'cancelled') {
                    stopCollectionWait();
                    showMessage(jobData.message || '수집이 취소되었습니다.', false);
                    return;
                }
                if (status === 'failed' || status === 'fail' || result.ok === false) {
                    stopCollectionWait();
                    showMessage(jobData.message || result.message || result.error || '수집에 실패했습니다.', false);
                    return;
                }
                setCollectionLoadingCopy(
                    status === 'running' ? 'DNFIX006컴에서 TIS 수집 중입니다.' : 'DNFIX006컴 응답을 기다리는 중',
                    jobData.message || (status === 'running' ? '수집기 앱이 작업을 실행 중입니다.' : '수집기 앱이 요청을 받을 때까지 대기합니다.')
                );
                scheduleNext();
            })
            .catch(function (error) {
                if (!collectionWaitActive || collectionActiveJobId !== jobId) {
                    return;
                }
                showMessage(error.message || '수집 상태 확인 중 오류가 발생했습니다.', false);
                scheduleNext();
            });
        }

        tick();
    }

    if (loadingCancelButton) {
        loadingCancelButton.addEventListener('click', cancelCollectionWait);
    }

    if (pingButton) {
        pingButton.addEventListener('click', function () {
            collectionWaitActive = true;
            collectionActiveJobId = '';
            collectionRequestAbort = (typeof AbortController === 'function') ? new AbortController() : null;
            setCollectionLoadingCopy('DNFIX006컴 연결을 확인하는 중', '핑 요청을 보냈습니다. 응답이 없으면 바로 중단할 수 있습니다.');
            setCollectionLoading(true, { canCancel: true });
            fetch('/admin/product/info_collect/ping', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                signal: collectionRequestAbort ? collectionRequestAbort.signal : undefined
            })
            .then(function (response) {
                return response.json().catch(function () {
                    throw new Error('연결 확인 응답을 읽을 수 없습니다.');
                });
            })
            .then(function (responseData) {
                if (!collectionWaitActive) {
                    if (responseData && responseData.data && responseData.data.job_id) {
                        notifyCollectionJobCancel(responseData.data.job_id);
                    }
                    return;
                }
                if (!responseData.success) {
                    throw new Error(responseData.message || '연결 확인 요청에 실패했습니다.');
                }
                var jobId = responseData.data && responseData.data.job_id ? String(responseData.data.job_id) : '';
                if (!jobId) {
                    throw new Error('연결 확인 작업 ID를 받지 못했습니다.');
                }
                setCollectionLoadingCopy('DNFIX006컴 응답을 기다리는 중', '수집기 앱이 핑을 받을 때까지 대기합니다.');
                pollFirebaseCollectionJob(jobId, {
                    timeoutMs: 20000,
                    noResponseMs: 15000,
                    onDone: function (jobData) {
                        var result = jobData.result || {};
                        var workerId = result.worker_id ? ' / ' + result.worker_id : '';
                        showMessage('DNFIX006컴 응답: ' + (result.message || 'pong') + workerId, true);
                    }
                });
            })
            .catch(function (error) {
                if (!collectionWaitActive || (error && error.name === 'AbortError')) {
                    return;
                }
                showMessage(error.message || '연결 확인 중 오류가 발생했습니다.', false);
                stopCollectionWait();
            });
        });
    }

    var applyCollectedFieldsButton = document.getElementById('applyCollectedProductFields');
    if (applyCollectedFieldsButton) {
        applyCollectedFieldsButton.addEventListener('click', function () {
            var selectedFields = [];
            Array.prototype.forEach.call(document.querySelectorAll('.collected-product-sync-check:checked'), function (checkbox) {
                var field = String(checkbox.dataset.field || '').trim();
                var value = String(checkbox.dataset.value || '').trim();
                if (field !== '' && value !== '') {
                    selectedFields.push({
                        field: field,
                        value: value
                    });
                }
            });
            if (!selectedFields.length) {
                window.alert('업데이트할 항목을 선택해 주세요.');
                return;
            }
            if (!window.confirm('선택한 ' + selectedFields.length + '개 항목을 상품에 반영할까요?')) {
                return;
            }

            fetch('/admin/product/info_collect/fields/apply', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({
                    prd_idx: productIdx,
                    site_code: collectionSiteCode,
                    fields: JSON.stringify(selectedFields)
                }).toString()
            })
            .then(function (response) { return response.json(); })
            .then(function (responseData) {
                if (!responseData.success) {
                    throw new Error(responseData.message || '일괄 업데이트에 실패했습니다.');
                }
                if (window.toast2) {
                    toast2('success', '상품 정보수집', responseData.message || '반영했습니다.');
                } else {
                    window.alert(responseData.message || '반영했습니다.');
                }
                loadCollectionView(selectedCollectionIndex);
            })
            .catch(function (error) {
                window.alert(error.message || '일괄 업데이트 중 오류가 발생했습니다.');
            });
        });
    }

    function loadCollectionView(collectionIndex) {
        var index = Number(collectionIndex);
        if (!Number.isInteger(index) || index < 0) {
            index = 0;
        }
        if (!window.jQuery) {
            window.location.reload();
            return;
        }

        loadingOverlay.querySelector('strong').textContent = '수집 상세정보를 불러오는 중입니다.';
        loadingOverlay.querySelector('p').textContent = '잠시만 기다려주세요.';
        loadingOverlay.hidden = false;

        window.jQuery.ajax({
            url: '/admin/product/info_collect',
            type: 'GET',
            dataType: 'text',
            data: {
                prd_idx: productIdx,
                collection_index: index
            },
            success: function (html) {
                window.jQuery('#crm_body').html(html);
            },
            error: function () {
                loadingOverlay.hidden = true;
                window.alert('수집 상세정보를 불러오지 못했습니다.');
            }
        });
    }

    Array.prototype.forEach.call(collectionRecordButtons, function (button) {
        button.addEventListener('click', function () {
            var collectionIndex = Number(button.dataset.collectionIndex);
            if (!Number.isInteger(collectionIndex) || collectionIndex < 0 || collectionIndex === selectedCollectionIndex) {
                return;
            }

            Array.prototype.forEach.call(collectionRecordButtons, function (recordButton) {
                recordButton.disabled = true;
            });
            loadCollectionView(collectionIndex);
        });
    });

    if (editImageStorageButton) {
        editImageStorageButton.addEventListener('click', function () {
            imageStorageForm.hidden = false;
            imageStorageHelp.hidden = false;
            imageStorageInput.focus();
        });
    }

    if (useRecommendedImageStoragePathButton) {
        useRecommendedImageStoragePathButton.addEventListener('click', function () {
            imageStorageInput.value = <?= json_encode($recommendedImageStoragePath, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
            imageStorageInput.focus();
        });
    }

    function closeTranslationModal() {
        translationModal.hidden = true;
        translationField = '';
        translationImageKey = '';
        translationSourceAlt = '';
        translationInput.value = '';
    }

    Array.prototype.forEach.call(document.querySelectorAll('.collection-copy-button'), function (button) {
        button.addEventListener('click', function () {
            var sourceElement = document.getElementById(button.dataset.copyTarget);
            if (!sourceElement) {
                return;
            }
            var text = sourceElement.innerText;
            var originalLabel = button.textContent;
            var complete = function () {
                button.textContent = '복사 완료';
                setTimeout(function () { button.textContent = originalLabel; }, 1500);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(complete);
                return;
            }
            var temporaryTextarea = document.createElement('textarea');
            temporaryTextarea.value = text;
            document.body.appendChild(temporaryTextarea);
            temporaryTextarea.select();
            document.execCommand('copy');
            temporaryTextarea.remove();
            complete();
        });
    });

    Array.prototype.forEach.call(document.querySelectorAll('.collection-translation-button'), function (button) {
        button.addEventListener('click', function () {
            translationField = button.dataset.field;
            translationImageKey = button.dataset.imageKey || '';
            translationSourceAlt = button.dataset.sourceAlt || '';
            translationModalTitle.textContent = button.dataset.label + ' 번역데이터';
            translationInput.value = button.dataset.value || '';
            translationModal.hidden = false;
            translationInput.focus();
        });
    });

    document.getElementById('closeCollectionTranslationModal').addEventListener('click', closeTranslationModal);
    document.getElementById('cancelCollectionTranslation').addEventListener('click', closeTranslationModal);
    document.getElementById('saveCollectionTranslation').addEventListener('click', function () {
        var translation = translationInput.value.trim();
        if (!translation || !translationField || !collectionItemIdx) {
            window.alert('번역 내용을 입력해 주세요.');
            return;
        }

        fetch('/admin/product/info_collect/translation/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams({
                prd_idx: productIdx,
                collection_item_idx: collectionItemIdx,
                field: translationField,
                translation: translation,
                image_key: translationImageKey,
                source_alt: translationSourceAlt
            }).toString()
        })
        .then(function (response) { return response.json(); })
        .then(function (responseData) {
            if (!responseData.success) {
                throw new Error(responseData.message || '번역 저장에 실패했습니다.');
            }
            loadCollectionView(selectedCollectionIndex);
        })
        .catch(function (error) {
            window.alert(error.message || '번역 저장 중 오류가 발생했습니다.');
        });
    });

    if (imageStorageForm) {
        imageStorageForm.addEventListener('submit', function (event) {
            event.preventDefault();
            var storagePath = imageStorageInput.value.trim().replace(/\/{2,}/g, '/');
            if (!/^\/[A-Za-z0-9._/-]*$/.test(storagePath)) {
                imageStorageMessage.textContent = '이미지 저장소 경로는 /로 시작하고 영문, 숫자, . _ - /만 사용할 수 있습니다.';
                imageStorageMessage.className = 'product-image-storage-message is-error';
                imageStorageMessage.hidden = false;
                return;
            }

            fetch('/admin/product/info_collect/image_storage_path/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({
                    prd_idx: productIdx,
                    image_storage_path: storagePath
                }).toString()
            })
            .then(function (response) { return response.json(); })
            .then(function (responseData) {
                if (!responseData.success) {
                    throw new Error(responseData.message || '이미지 저장소 경로 저장에 실패했습니다.');
                }
                imageStorageMessage.textContent = responseData.message;
                imageStorageMessage.className = 'product-image-storage-message is-success';
                imageStorageMessage.hidden = false;
                imageStorageInput.value = responseData.image_storage_path;
                setTimeout(function () {
                    loadCollectionView(selectedCollectionIndex);
                }, 400);
            })
            .catch(function (error) {
                imageStorageMessage.textContent = error.message || '이미지 저장소 경로 저장 중 오류가 발생했습니다.';
                imageStorageMessage.className = 'product-image-storage-message is-error';
                imageStorageMessage.hidden = false;
            });
        });
    }

    if (copyImageHtmlButton && imageHtmlTextarea) {
        copyImageHtmlButton.addEventListener('click', function () {
            var originalLabel = copyImageHtmlButton.textContent;
            var complete = function () {
                copyImageHtmlButton.textContent = '복사 완료';
                setTimeout(function () {
                    copyImageHtmlButton.textContent = originalLabel;
                }, 1500);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(imageHtmlTextarea.value).then(complete);
                return;
            }

            imageHtmlTextarea.focus();
            imageHtmlTextarea.select();
            document.execCommand('copy');
            complete();
        });
    }

    if (copyHostedImageHtmlButton && hostedImageHtmlTextarea) {
        copyHostedImageHtmlButton.addEventListener('click', function () {
            var originalLabel = copyHostedImageHtmlButton.textContent;
            var complete = function () {
                copyHostedImageHtmlButton.textContent = '복사 완료';
                setTimeout(function () {
                    copyHostedImageHtmlButton.textContent = originalLabel;
                }, 1500);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(hostedImageHtmlTextarea.value).then(complete);
                return;
            }

            hostedImageHtmlTextarea.focus();
            hostedImageHtmlTextarea.select();
            document.execCommand('copy');
            complete();
        });
    }

    if (hostedImageList && hostedImageHtmlTextarea) {
        var draggedHostedImage = null;

        function saveHostedImageOrder() {
            var orderedUrls = Array.prototype.map.call(
                hostedImageList.querySelectorAll('[data-hosting-image-url]'),
                function (element) { return element.dataset.hostingImageUrl; }
            );
            hostedImageHtmlTextarea.value = orderedUrls.map(function (url) {
                return '<img src="' + url + '"><br>';
            }).join('\n');

            fetch('/admin/product/info_collect/images/hosting_order/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({
                    prd_idx: productIdx,
                    collection_item_idx: hostedImageList.dataset.collectionItemIdx,
                    hosting_image_urls_json: JSON.stringify(orderedUrls)
                }).toString()
            })
            .then(function (response) { return response.json(); })
            .then(function (responseData) {
                if (!responseData.success) {
                    throw new Error(responseData.message || '이미지 순서 저장에 실패했습니다.');
                }
            })
            .catch(function (error) {
                window.alert(error.message || '이미지 순서 저장 중 오류가 발생했습니다.');
                loadCollectionView(selectedCollectionIndex);
            });
        }

        Array.prototype.forEach.call(hostedImageList.querySelectorAll('[data-hosting-image-url]'), function (imageElement) {
            imageElement.addEventListener('dragstart', function () {
                draggedHostedImage = imageElement;
                imageElement.classList.add('is-dragging');
            });
            imageElement.addEventListener('dragend', function () {
                imageElement.classList.remove('is-dragging');
                Array.prototype.forEach.call(hostedImageList.children, function (item) {
                    item.classList.remove('is-drop-target');
                });
            });
            imageElement.addEventListener('dragover', function (event) {
                event.preventDefault();
                if (draggedHostedImage && draggedHostedImage !== imageElement) {
                    imageElement.classList.add('is-drop-target');
                }
            });
            imageElement.addEventListener('dragleave', function () {
                imageElement.classList.remove('is-drop-target');
            });
            imageElement.addEventListener('drop', function (event) {
                event.preventDefault();
                imageElement.classList.remove('is-drop-target');
                if (!draggedHostedImage || draggedHostedImage === imageElement) {
                    return;
                }
                var insertAfter = event.clientY > imageElement.getBoundingClientRect().top + (imageElement.offsetHeight / 2);
                hostedImageList.insertBefore(draggedHostedImage, insertAfter ? imageElement.nextSibling : imageElement);
                saveHostedImageOrder();
            });
        });
    }

    if (uploadImagesButton) {
        uploadImagesButton.addEventListener('click', function () {
            if (!window.confirm('수집 이미지를 설정된 이미지 저장소로 일괄 업로드하시겠습니까?')) {
                return;
            }

            uploadImagesButton.disabled = true;
            setCollectionLoadingCopy('이미지를 이미지 호스팅에 업로드중입니다.', '완료될때까지 잠시만 기다려주세요.');
            loadingOverlay.hidden = false;
            if (loadingCancelButton) {
                loadingCancelButton.hidden = true;
            }

            fetch('/admin/product/info_collect/images/upload_hosting', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({
                    prd_idx: productIdx,
                    collection_index: uploadImagesButton.dataset.collectionIndex || selectedCollectionIndex
                }).toString()
            })
            .then(function (response) { return response.json(); })
            .then(function (responseData) {
                if (!responseData.success) {
                    throw new Error(responseData.message || '이미지 호스팅 업로드에 실패했습니다.');
                }
                window.alert(responseData.message || '이미지 호스팅 업로드가 완료되었습니다.');
                loadCollectionView(selectedCollectionIndex);
            })
            .catch(function (error) {
                window.alert(error.message || '이미지 호스팅 업로드 중 오류가 발생했습니다.');
                loadingOverlay.hidden = true;
                uploadImagesButton.disabled = false;
            });
        });
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var rawUrl = input.value.trim();
        var url;

        if (rawUrl === '') {
            showMessage('검수할 상품 URL을 입력해 주세요.', false);
            input.focus();
            return;
        }

        try {
            url = new URL(rawUrl);
        } catch (error) {
            showMessage('http:// 또는 https://를 포함한 올바른 URL을 입력해 주세요.', false);
            return;
        }

        var normalizedHost = url.hostname.toLowerCase().replace(/^www\./, '');
        if (normalizedHost === 'nipporigift.net') {
            var pageName = url.pathname.split('/').filter(Boolean).pop() || '';
            var productId = url.searchParams.get('product_id');

            if (pageName !== 'detail.php') {
                showMessage('닛포리기프트 URL은 detail.php 상품 상세 페이지여야 합니다.', false);
                return;
            }
            if (!productId || productId.trim() === '') {
                showMessage('닛포리기프트 URL에는 product_id 값이 반드시 포함되어야 합니다.', false);
                return;
            }
        } else if (normalizedHost === 'tamatoys.tma.co.jp') {
            var tamatoysMatch = url.pathname.match(/^\/item\/detail\/([A-Za-z0-9_-]+)$/);
            if (!tamatoysMatch) {
                showMessage('타마토이즈 URL은 /item/detail/상품코드 형식이어야 합니다.', false);
                return;
            }
        } else if (normalizedHost === 'mzakka.com') {
            var mzakkaItemId = url.searchParams.get('item_id');
            if (url.pathname !== '/pc/detail/item.php') {
                showMessage('엠자카 URL은 /pc/detail/item.php 상품 상세 페이지여야 합니다.', false);
                return;
            }
            if (!mzakkaItemId || !/^[A-Za-z0-9_-]+$/.test(mzakkaItemId)) {
                showMessage('엠자카 URL에는 유효한 item_id 값이 필요합니다.', false);
                return;
            }
        } else if (normalizedHost === 'nobunaga-toys.com') {
            var nobunagaProductId = url.searchParams.get('pid');
            if (url.pathname !== '/') {
                showMessage('노부나가 URL은 사이트 최상위 상품 페이지여야 합니다.', false);
                return;
            }
            if (!nobunagaProductId || !/^[1-9][0-9]*$/.test(nobunagaProductId)) {
                showMessage('노부나가 URL에는 유효한 pid 값이 필요합니다.', false);
                return;
            }
        } else if (normalizedHost === 'e-nls.com') {
            var nlsMatch = url.pathname.match(/^\/pict[0-9]+-([1-9][0-9]*)\/?$/);
            if (!nlsMatch) {
                showMessage('NLS URL은 /pict1-상품번호 형식이어야 합니다.', false);
                return;
            }
        } else if (normalizedHost === 'ms-online.co.jp') {
            var msProductId = url.searchParams.get('pclass_id');
            if (!msProductId || !/^[1-9][0-9]*$/.test(msProductId)) {
                showMessage('엠즈 URL에는 유효한 pclass_id 값이 필요합니다.', false);
                return;
            }
        } else if (normalizedHost === 'bb-order.com') {
            var tisMatch = url.pathname.match(/^\/tisgoods_kr\/shop\/detail\/([A-Za-z0-9_-]+)\/?$/);
            if (!tisMatch) {
                showMessage('TIS URL은 /tisgoods_kr/shop/detail/상품코드 형식이어야 합니다.', false);
                return;
            }
        } else if (normalizedHost === 'ridejapan.net') {
            var ridejapanMatch = url.pathname.match(/^\/product_item\/([A-Za-z0-9_-]+)\/?$/);
            if (!ridejapanMatch) {
                showMessage('라이드재팬 URL은 /product_item/상품코드 형식이어야 합니다.', false);
                return;
            }
        } else if (normalizedHost === 'yelolab.jp') {
            var yelolabMatch = url.pathname.match(/^\/products\/[^/]+\/([A-Za-z0-9_-]+)\/?$/);
            if (!yelolabMatch) {
                showMessage('옐로랩 URL은 /products/카테고리/상품코드 형식이어야 합니다.', false);
                return;
            }
        } else {
            showMessage('현재 수집을 지원하지 않는 사이트입니다.', false);
            return;
        }

        if (!productIdx) {
            showMessage('연결할 내부 상품 번호를 확인할 수 없습니다.', false);
            return;
        }

        if (normalizedHost === 'bb-order.com') {
            setCollectionLoadingCopy('DNFIX006컴에 수집 요청을 보내는 중', '응답이 없으면 바로 중단할 수 있습니다.');
        } else {
            setCollectionLoadingCopy('데이터를 수집중입니다.', '완료될때까지 잠시만 기다려주세요. 필요하면 바로 중단할 수 있습니다.');
        }
        collectionWaitActive = true;
        collectionActiveJobId = '';
        collectionRequestAbort = (typeof AbortController === 'function') ? new AbortController() : null;
        setCollectionLoading(true, { canCancel: true });
        apiResult.hidden = true;
        fetch('/admin/product/info_collect/request', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams({
                collection_url: rawUrl,
                prd_idx: productIdx
            }).toString(),
            signal: collectionRequestAbort ? collectionRequestAbort.signal : undefined
        })
        .then(function (response) {
            return response.json().catch(function () {
                throw new Error('정보수집 API 응답을 읽을 수 없습니다.');
            });
        })
        .then(function (responseData) {
            if (!collectionWaitActive) {
                if (responseData && responseData.data && responseData.data.job_id) {
                    notifyCollectionJobCancel(responseData.data.job_id);
                }
                return;
            }
            if (!responseData.success) {
                throw new Error(responseData.message || '정보수집 요청에 실패했습니다.');
            }
            var jobId = responseData.data && responseData.data.job_id ? String(responseData.data.job_id) : '';
            if ((responseData.async || (responseData.data && responseData.data.async)) && jobId) {
                setCollectionLoadingCopy('DNFIX006컴 응답을 기다리는 중', '수집기 앱이 요청을 받을 때까지 대기합니다. 응답이 없으면 중단하세요.');
                pollFirebaseCollectionJob(jobId);
                return;
            }
            window.location.reload();
        })
        .catch(function (error) {
            if (!collectionWaitActive || (error && error.name === 'AbortError')) {
                return;
            }
            showMessage(error.message || '정보수집 요청 중 오류가 발생했습니다.', false);
            stopCollectionWait();
        })
    });
}());
</script>