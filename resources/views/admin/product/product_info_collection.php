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
$registrationDateRaw = $collectionItem['registration_date'] ?? $specifications['registration_date'] ?? '';
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
        $collectedImages[] = [
            'url' => $imageUrl,
            'alt' => $imageAlt,
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
];
$formatSiteCodeName = static function (string $siteCode) use ($siteCodeNames): string {
    return $siteCodeNames[strtolower(trim($siteCode))] ?? '';
};
$sourceUrl = trim((string)($collectionItem['source_url'] ?? ''));
$supplyPriceRaw = $collectionItem['supply_price'] ?? null;
$supplyCurrency = trim((string)($collectionItem['supply_currency'] ?? ''));
$supplyPriceText = ($supplyPriceRaw === null || $supplyPriceRaw === '')
    ? 'No Data'
    : number_format((float)$supplyPriceRaw) . ($supplyCurrency !== '' ? ' ' . $supplyCurrency : '');
$accessoriesText = trim((string)($specifications['accessories'] ?? ''));
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
                    </div>
                </div>
                <p class="product-info-collection-help">현재 수집가능한 사이트 <br>
                    1) 닛포리기프트 발주 사이트 ex) <a href="http://www.nipporigift.net" target="_blank" rel="noopener noreferrer">http://www.nipporigift.net/products/detail.php?product_id=31373</a><br>
                    2) 타마토이즈 ex) <a href="https://tamatoys.tma.co.jp" target="_blank" rel="noopener noreferrer">https://tamatoys.tma.co.jp/item/detail/TMT-1716</a><br>
                    3) 엠자카 ex) <a href="https://mzakka.com" target="_blank" rel="noopener noreferrer">https://mzakka.com/pc/detail/item.php?item_id=M12488&amp;category=1789</a><br>
                    4) 노부나가 ex) <a href="https://www.nobunaga-toys.com" target="_blank" rel="noopener noreferrer">https://www.nobunaga-toys.com/?pid=193204770</a><br>
                    5) NLS ex) <a href="https://www.e-nls.com" target="_blank" rel="noopener noreferrer">https://www.e-nls.com/pict1-68047?c2=new</a>
                </p>
                <div id="collectionUrlValidation" class="product-info-collection-validation" hidden aria-live="polite"></div>
            </form>

            <section id="collectionApiResult" class="product-info-collection-result" hidden>
                <h3>정보수집 반환 데이터 <small>임시 확인용</small></h3>
                <pre id="collectionApiResultData"></pre>
            </section>

            <div id="collectionLoadingOverlay" class="product-info-collection-loading" hidden aria-live="assertive" aria-busy="true">
                <div>
                    <span class="product-info-collection-spinner"></span>
                    <strong>데이터를 수집중입니다.</strong>
                    <p>완료될때까지 잠시만 기다려주세요.</p>
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
                <span>수집 <?= htmlspecialchars($formatCollectedDate($collectionItem['collected_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
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
                        <th>품번</th>
                        <td><?= $renderCollectedValue($formatCollectedText($collectionItem['product_code'] ?? null)) ?></td>
                        <th>바코드</th>
                        <td><?= $renderCollectedValue($formatCollectedText($collectionItem['barcode'] ?? null)) ?></td>
                    </tr>
                    <tr>
                        <th>상품명</th>
                        <td colspan="3"><?= $renderCollectedValue($formatCollectedText($collectionItem['product_name'] ?? null)) ?></td>
                    </tr>
                    <tr>
                        <th>공급가</th>
                        <td colspan="3"><?= $renderCollectedValue($supplyPriceText) ?></td>
                    </tr>
                    <tr>
                        <th>패키지 사이즈</th>
                        <td colspan="3"><?= $renderCollectedValue($formatCollectedSize($packageSize, '깊이(D)')) ?></td>
                    </tr>
                    <tr>
                        <th>패키지 중량</th>
                        <td colspan="3"><?= $renderCollectedValue($formatCollectedMeasure($packageWeight)) ?></td>
                    </tr>
                    <tr>
                        <th>상품 사이즈</th>
                        <td colspan="3"><?= $renderCollectedValue($formatCollectedSize($productSize, '길이(D)')) ?></td>
                    </tr>
                    <tr>
                        <th>상품 중량</th>
                        <td colspan="3"><?= $renderCollectedValue($formatCollectedMeasure($productWeight)) ?></td>
                    </tr>
                    <tr>
                        <th>내부 길이</th>
                        <td colspan="3">
                            질 길이: <?= $renderCollectedValue($formatCollectedMeasure($vaginalInternalLength)) ?>
                            &nbsp; / &nbsp;
                            애널 길이: <?= $renderCollectedValue($formatCollectedMeasure($analInternalLength)) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>소재</th>
                        <td colspan="3"><?= $renderCollectedValue($formatCollectedText($material !== '' ? $material : null)) ?></td>
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
                        <td colspan="3"><?= $renderCollectedValue($formatCollectedText($registrationDateText !== '' ? $registrationDateText : null)) ?></td>
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
                                <div class="collection-translation">번역: <?= htmlspecialchars($translatedAccessories, ENT_QUOTES, 'UTF-8') ?></div>
                                <?php if ($translationLogs['accessories'] !== null) { ?><div class="collection-action-log">번역 수정: <?= htmlspecialchars($formatActionLog($translationLogs['accessories']), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
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
                                <div class="collection-translation">번역:<br><?= nl2br(htmlspecialchars(html_entity_decode($translatedMakerComment, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8')) ?></div>
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
                                <div class="collection-translation">번역:<br><?= nl2br(htmlspecialchars(html_entity_decode($translatedSellerComment, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8')) ?></div>
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
                            ?>
                            <div class="collected-product-image-item">
                                <a class="collected-product-image-preview" href="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><img src="<?= htmlspecialchars($imageProxyUrl($imageUrl), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($imageAlt !== '' ? $imageAlt : '수집 상품 이미지', ENT_QUOTES, 'UTF-8') ?>" referrerpolicy="no-referrer" ></a>
                                <a class="collected-product-image-download" href="<?= htmlspecialchars($imageProxyUrl($imageUrl) . '&download=1', ENT_QUOTES, 'UTF-8') ?>">이미지 다운로드</a>
                                <?php if ($imageAlt !== '') { ?>
                                    <p class="collected-product-image-alt"><?= htmlspecialchars($imageAlt, ENT_QUOTES, 'UTF-8') ?></p>
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
.product-info-collection-heading h2{margin:0 0 6px;font-size:19px;color:#1f2937}.product-info-collection-heading p{margin:0;color:#6b7280;font-size:13px}.product-info-collection-product{flex:0 0 auto;padding:7px 10px;border-radius:5px;background:#f3f6fa;color:#64748b;font-size:12px}.product-info-collection-product strong{color:#334155}.product-info-collection label{display:block;margin-bottom:9px;font-size:13px;font-weight:700;color:#374151}.product-info-collection-input{display:flex;gap:8px}.product-info-collection-input input{box-sizing:border-box;flex:1;min-width:0;height:46px;padding:0 14px;border:2px solid #f0b429;border-radius:6px;background:#fff;color:#1f2937;font-size:15px;box-shadow:0 1px 2px rgba(146,64,14,.08)}.product-info-collection-input input::placeholder{color:#b45309;opacity:.72}.product-info-collection-input input:focus{outline:0;border-color:#d97706;box-shadow:0 0 0 3px rgba(245,158,11,.28)}.product-info-collection-input button{min-width:90px}.product-info-collection-help{margin:9px 0 0;color:#333;font-size:12px;line-height:1.5}.product-info-collection-help a{color:#333;text-decoration:underline}.product-info-collection-help code{padding:1px 4px;border-radius:3px;background:#f1f5f9;color:#475569}.product-info-collection-validation{margin-top:16px;padding:11px 13px;border-radius:5px;font-size:13px}.product-info-collection-validation.is-success{color:#166534;background:#f0fdf4;border:1px solid #bbf7d0}.product-info-collection-validation.is-error{color:#b91c1c;background:#fef2f2;border:1px solid #fecaca}.product-info-collection-result{margin-top:22px;padding-top:20px;border-top:1px solid #e5e7eb}.product-info-collection-result h3{margin:0 0 9px;font-size:14px;color:#374151}.product-info-collection-result h3 small{margin-left:5px;color:#94a3b8;font-weight:400}.product-info-collection-result pre{max-height:460px;margin:0;padding:14px;overflow:auto;border-radius:6px;background:#0f172a;color:#e2e8f0;white-space:pre-wrap;word-break:break-word;font:12px/1.55 Consolas,Monaco,monospace}.product-info-collection-loading{position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,.56);text-align:center}.product-info-collection-loading>div{min-width:280px;padding:28px 36px;border-radius:10px;background:#fff;box-shadow:0 18px 40px rgba(0,0,0,.22);color:#1f2937}.product-info-collection-loading strong{display:block;margin-top:14px;font-size:16px}.product-info-collection-loading p{margin:7px 0 0;color:#64748b;font-size:13px}.product-info-collection-spinner{display:inline-block;width:32px;height:32px;border:4px solid #dbeafe;border-top-color:#2563eb;border-radius:50%;animation:collection-spin .8s linear infinite}@keyframes collection-spin{to{transform:rotate(360deg)}}.collected-product-information{flex:1;min-width:0;max-width:900px;margin-top:20px;border:1px solid #dfe5ed;border-radius:10px;background:#fff;overflow:hidden}.collected-product-information-heading{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;background:#f8fafc;border-bottom:1px solid #e5e7eb}.collected-product-information-heading h2{margin:0 0 4px;font-size:17px;color:#1e293b}.collected-product-information-heading p,.collected-product-information-heading span{margin:0;color:#64748b;font-size:12px}.collected-product-table{width:100%;border-collapse:collapse}.collected-product-table th,.collected-product-table td{padding:11px 13px;border-bottom:1px solid #edf0f4;text-align:left;vertical-align:top;font-size:13px;line-height:1.55}.collected-product-table th{width:135px;background:#f8fafc;color:#475569;font-weight:600}.collected-product-table td a{color:#2563eb;word-break:break-all}.collected-no-data{color:#b0b8c4}.collected-product-comment{white-space:normal;color:#475569}.collected-product-images{padding:20px}.collected-product-images-title{display:flex;align-items:center;gap:8px;margin-bottom:10px}.collected-product-images-title h3{margin:0;font-size:14px;color:#334155}.collected-product-images-title span{padding:2px 6px;border-radius:10px;background:#eef2ff;color:#4f46e5;font-size:11px}.collected-product-image-html{box-sizing:border-box;width:100%;height:75px;margin-bottom:14px;padding:9px;border:1px solid #d7dee8;border-radius:5px;resize:vertical;font:11px/1.4 Consolas,monospace}.collected-product-image-list{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}.collected-product-image-list a{display:block;overflow:hidden;border:1px solid #e2e8f0;border-radius:5px;background:#f8fafc}.collected-product-image-list img{display:block;width:100%;aspect-ratio:1;object-fit:contain}@media(max-width:640px){.product-info-collection{padding:20px}.product-info-collection-heading{display:block}.product-info-collection-product{display:inline-block;margin-top:12px}.product-info-collection-input{display:block}.product-info-collection-input button{width:100%;margin-top:8px}.collected-product-information-heading{display:block}.collected-product-information-heading span{display:block;margin-top:6px}.collected-product-table th{width:100px}.collected-product-image-list{grid-template-columns:repeat(2,minmax(0,1fr))}}
.product-info-collection-loading[hidden]{display:none}
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
.collected-product-compare{display:block;width:max-content;max-width:100%;margin-top:6px;padding:3px 7px;border-radius:4px;font-size:11px;font-weight:600}.collected-product-compare-empty{color:#92400e;background:#fef3c7}.collected-product-compare-mismatch{color:#b91c1c;background:#fee2e2}
.collection-translation{margin-top:8px;padding:8px 10px;border-left:3px solid #60a5fa;background:#eff6ff;color:#1e3a8a}.collection-translation-button{margin-top:8px}.collection-translation-modal{position:fixed;inset:0;z-index:10001;display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,.55)}.collection-translation-modal[hidden]{display:none}.collection-translation-modal-card{width:min(560px,calc(100% - 32px));padding:20px;border-radius:9px;background:#fff;box-shadow:0 20px 50px rgba(0,0,0,.25)}.collection-translation-modal-heading{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}.collection-translation-modal-heading h3{margin:0;font-size:16px}.collection-translation-modal-heading button{border:0;background:transparent;color:#64748b;font-size:24px;cursor:pointer}.collection-translation-modal textarea{box-sizing:border-box;width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:5px;resize:vertical;line-height:1.5}.collection-translation-modal-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:14px}
.collection-source-block{padding:9px 10px;border:1px solid #e2e8f0;border-radius:5px;background:#fff;color:#334155}.collection-action-block{display:flex;gap:6px;margin-top:8px;padding:7px 8px;border:1px solid #e2e8f0;border-radius:5px;background:#f8fafc}.collection-action-block .collection-translation-button{margin-top:0}
.collection-action-log{margin-top:6px;color:#64748b;font-size:11px}.hosted-action-log{margin:0 0 10px}
.collected-product-image-item{overflow:hidden;border:1px solid #e2e8f0;border-radius:5px;background:#f8fafc}.collected-product-image-list .collected-product-image-preview{display:block;border:0;border-radius:0}.collected-product-image-alt{margin:0;padding:7px 8px;background:#fff;border-top:1px solid #edf0f4;color:#475569;font-size:11px;line-height:1.45;word-break:break-word}.collected-product-image-download{display:block;padding:7px;text-align:center;background:#fff;color:#2563eb!important;font-size:11px;text-decoration:none}
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
    var productIdx = <?= json_encode($prdIdx, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var collectionItemIdx = <?= json_encode($collectionItemIdx) ?>;
    var selectedCollectionIndex = <?= json_encode($selectedCollectionIndex) ?>;
    var collectionRecordButtons = document.querySelectorAll('.collection-record-button');
    var apiResult = document.getElementById('collectionApiResult');
    var apiResultData = document.getElementById('collectionApiResultData');
    var loadingOverlay = document.getElementById('collectionLoadingOverlay');
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

    function showMessage(text, isSuccess) {
        message.textContent = text;
        message.hidden = false;
        message.className = 'product-info-collection-validation ' + (isSuccess ? 'is-success' : 'is-error');
    }

    function setCollectionLoading(isLoading) {
        loadingOverlay.hidden = !isLoading;
        input.disabled = isLoading;
        submitButton.disabled = isLoading;
        submitButton.textContent = isLoading ? '수집 중...' : '검수 후 수집 요청';
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
            loadingOverlay.querySelector('strong').textContent = '수집 상세정보를 불러오는 중입니다.';
            loadingOverlay.querySelector('p').textContent = '잠시만 기다려주세요.';
            loadingOverlay.hidden = false;

            window.jQuery.ajax({
                url: '/admin/product/info_collect',
                type: 'GET',
                dataType: 'text',
                data: {
                    prd_idx: productIdx,
                    collection_index: collectionIndex
                },
                success: function (html) {
                    window.jQuery('#crm_body').html(html);
                },
                error: function () {
                    loadingOverlay.hidden = true;
                    Array.prototype.forEach.call(collectionRecordButtons, function (recordButton) {
                        recordButton.disabled = false;
                    });
                    window.alert('수집 상세정보를 불러오지 못했습니다.');
                }
            });
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
                translation: translation
            }).toString()
        })
        .then(function (response) { return response.json(); })
        .then(function (responseData) {
            if (!responseData.success) {
                throw new Error(responseData.message || '번역 저장에 실패했습니다.');
            }
            window.location.reload();
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
                    window.location.reload();
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
                window.location.reload();
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
            loadingOverlay.querySelector('strong').textContent = '이미지를 이미지 호스팅에 업로드중입니다.';
            loadingOverlay.querySelector('p').textContent = '완료될때까지 잠시만 기다려주세요.';
            loadingOverlay.hidden = false;

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
                window.location.reload();
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
        } else {
            showMessage('현재 수집을 지원하지 않는 사이트입니다.', false);
            return;
        }

        if (!productIdx) {
            showMessage('연결할 내부 상품 번호를 확인할 수 없습니다.', false);
            return;
        }

        setCollectionLoading(true);
        apiResult.hidden = true;
        fetch('/admin/product/info_collect/request', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams({
                collection_url: rawUrl,
                prd_idx: productIdx
            }).toString()
        })
        .then(function (response) {
            return response.json().catch(function () {
                throw new Error('정보수집 API 응답을 읽을 수 없습니다.');
            });
        })
        .then(function (responseData) {
            if (!responseData.success) {
                throw new Error(responseData.message || '정보수집 요청에 실패했습니다.');
            }
            window.location.reload();
        })
        .catch(function (error) {
            showMessage(error.message || '정보수집 요청 중 오류가 발생했습니다.', false);
            setCollectionLoading(false);
        })
    });
}());
</script>