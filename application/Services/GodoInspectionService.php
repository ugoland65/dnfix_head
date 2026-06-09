<?php

namespace App\Services;

class GodoInspectionService
{
    public const INSPECTION_VERSION = '20260609_v1';

    private const BRAND1_CATEGORY_LIST = [
        '049' => '중국수입 브랜드',
        '003' => '일본수입 본사발주',
        '054' => '일본수입 유통발주',
        '058' => '기타수입 브랜드',
        '053' => '국내공급사 브랜드 A',
        '018' => '국내공급사 브랜드 B',
        '055' => '국내공급사 브랜드 C',
        '052' => '국내공급사 브랜드 D',
        '045' => '콘돔 브랜드',
        '056' => '속옷 브랜드',
        '057' => '윤활젤 브랜드',
        '059' => '국내사입 비성인용품',
        '032' => '자체브랜드',
    ];

    private const ONAHOLE_WEIGHT_CATEGORIES = [
        ['cateNm' => '200g 미만', 'cateCd' => '026001006', 'min' => 0, 'max' => 199],
        ['cateNm' => '200g ~ 299g', 'cateCd' => '026001002', 'min' => 200, 'max' => 299],
        ['cateNm' => '300g ~ 399g', 'cateCd' => '026001005', 'min' => 300, 'max' => 399],
        ['cateNm' => '400g ~ 499g', 'cateCd' => '026001007', 'min' => 400, 'max' => 499],
        ['cateNm' => '500g ~ 599g', 'cateCd' => '026001008', 'min' => 500, 'max' => 599],
        ['cateNm' => '600g ~ 799g', 'cateCd' => '026001001', 'min' => 600, 'max' => 799],
        ['cateNm' => '800g ~ 999g', 'cateCd' => '026001009', 'min' => 800, 'max' => 999],
        ['cateNm' => '1kg ~ 2.99kg', 'cateCd' => '026001004', 'min' => 1000, 'max' => 2999],
        ['cateNm' => '3kg ~ 4.99kg', 'cateCd' => '026001003', 'min' => 3000, 'max' => 4999],
        ['cateNm' => '5kg 이상', 'cateCd' => '026001010', 'min' => 5000, 'max' => null],
    ];

    private const ONAHOLE_PRICE_CATEGORIES = [
        ['cateNm' => '2만원 이하', 'cateCd' => '026003001', 'min' => 0, 'max' => 19999],
        ['cateNm' => '2만원~3만원', 'cateCd' => '026003006', 'min' => 20000, 'max' => 29999],
        ['cateNm' => '3만원~4만원', 'cateCd' => '026003007', 'min' => 30000, 'max' => 39999],
        ['cateNm' => '4만원~5만원', 'cateCd' => '026003003', 'min' => 40000, 'max' => 49999],
        ['cateNm' => '5만원~7만원', 'cateCd' => '026003004', 'min' => 50000, 'max' => 69999],
        ['cateNm' => '7만원~10만원', 'cateCd' => '026003005', 'min' => 70000, 'max' => 99999],
        ['cateNm' => '10만원~', 'cateCd' => '026003002', 'min' => 100000, 'max' => null],
    ];

    private const ONAHOLE_HBTI_CATEGORIES = [
        ['hbti' => 'SRJT', 'cateNm' => 'SRJT-리얼 전략가', 'cateCd' => '026010001'],
        ['hbti' => 'SRJE', 'cateNm' => 'SRJE-감성 기획자', 'cateCd' => '026010002'],
        ['hbti' => 'SRPT', 'cateNm' => 'SRPT-자유 분석러', 'cateCd' => '026010003'],
        ['hbti' => 'SRPE', 'cateNm' => 'SRPE-감성 탐험가', 'cateCd' => '026010004'],
        ['hbti' => 'SFJT', 'cateNm' => 'SFJT-체계 몽상가', 'cateCd' => '026010005'],
        ['hbti' => 'SFJE', 'cateNm' => 'SFJE-감성 큐레이터', 'cateCd' => '026010006'],
        ['hbti' => 'SFPT', 'cateNm' => 'SFPT-판타지 러버', 'cateCd' => '026010007'],
        ['hbti' => 'SFPE', 'cateNm' => 'SFPE-감성 몽상가', 'cateCd' => '026010008'],
        ['hbti' => 'HRJT', 'cateNm' => 'HRJT-강한 현실주의자', 'cateCd' => '026010009'],
        ['hbti' => 'HRJE', 'cateNm' => 'HRJE-크리에이터', 'cateCd' => '026010010'],
        ['hbti' => 'HRPT', 'cateNm' => 'HRPT-테크 관찰자', 'cateCd' => '026010011'],
        ['hbti' => 'HRPE', 'cateNm' => 'HRPE-아티스트', 'cateCd' => '026010012'],
        ['hbti' => 'HFJT', 'cateNm' => 'HFJT-전략 마법사', 'cateCd' => '026010013'],
        ['hbti' => 'HFPT', 'cateNm' => 'HFPT-즉흥 드리머', 'cateCd' => '026010014'],
        ['hbti' => 'HFPE', 'cateNm' => 'HFPE-낭만 예술가', 'cateCd' => '026010015'],
        ['hbti' => 'HFJE', 'cateNm' => 'HFJE-예술 전사', 'cateCd' => '026010016'],
    ];

    private const ONAHOLE_INNER_LENGTH_CATEGORIES = [
        ['cateNm' => '10cm 미만', 'cateCd' => '026006007', 'min' => 0, 'max' => 9.99],
        ['cateNm' => '10cm ~ 11.9cm', 'cateCd' => '026006002', 'min' => 10, 'max' => 11.9],
        ['cateNm' => '12cm ~ 12.9cm', 'cateCd' => '026006003', 'min' => 12, 'max' => 12.9],
        ['cateNm' => '13cm ~ 13.9cm', 'cateCd' => '026006006', 'min' => 13, 'max' => 13.9],
        ['cateNm' => '14cm ~ 14.9cm', 'cateCd' => '026006005', 'min' => 14, 'max' => 14.9],
        ['cateNm' => '15cm ~ 15.9cm', 'cateCd' => '026006001', 'min' => 15, 'max' => 15.9],
        ['cateNm' => '16cm 이상', 'cateCd' => '026006004', 'min' => 16, 'max' => null],
    ];

    private const MARGIN_GRADE_CATEGORIES = [
        ['cateNm' => 'A', 'cateCd' => '046001001'],
        ['cateNm' => 'B', 'cateCd' => '046001002'],
        ['cateNm' => 'C', 'cateCd' => '046001003'],
        ['cateNm' => 'D', 'cateCd' => '046001004'],
        ['cateNm' => 'E', 'cateCd' => '046001005'],
        ['cateNm' => 'F', 'cateCd' => '046001006'],
        ['cateNm' => 'G', 'cateCd' => '046001007'],
        ['cateNm' => 'H', 'cateCd' => '046001008'],
        ['cateNm' => 'I', 'cateCd' => '046001009'],
    ];

    public function buildInspectionContext(array $item): array
    {
        $psIdx = (int)($item['ps_idx'] ?? 0);
        $goodsNo = trim((string)($item['godo_goods_no'] ?? ''));
        $cdGodoCode = trim((string)($item['cd_godo_code'] ?? ''));
        $hasCdGodoCode = ($cdGodoCode !== '' && $cdGodoCode !== '0');
        $isMatchedByGoodsNo = ($goodsNo !== '');
        $onlyAdultFl = strtolower(trim((string)($item['godo_only_adult_fl'] ?? '')));
        $godoGoodsModelNo = trim((string)($item['godo_goods_model_no'] ?? ''));
        $godoCostPriceRaw = (string)($item['godo_cost_price'] ?? '');
        $intranetCostPriceRaw = (string)($item['cost_price'] ?? '');
        $godoGoodsPriceRaw = (string)($item['godo_goods_price'] ?? '');
        $intranetGoodsPriceRaw = (string)($item['goods_price'] ?? '');
        $goodsWeightRaw = (string)($item['goods_weight'] ?? '');
        $innerLengthRaw = (string)($item['inner_length'] ?? '');
        $intranetBarcode = trim((string)($item['barcode'] ?? ''));
        $godoCategoryLines = (isset($item['godo_category_lines']) && is_array($item['godo_category_lines'])) ? $item['godo_category_lines'] : [];
        $marginGrade = strtoupper(trim((string)($item['margin_grade'] ?? '')));
        $cdHbti = strtoupper(trim((string)($item['cd_hbti'] ?? '')));
        $cdKindCode = strtoupper(trim((string)($item['cd_kind_code'] ?? '')));

        $normalizePlain = static function (string $value): string {
            $value = str_replace(',', '', trim($value));
            $value = preg_replace('/\s+/', '', $value);
            return is_string($value) ? $value : '';
        };

        $godoCostPriceNormalized = $normalizePlain($godoCostPriceRaw);
        $intranetCostPriceNormalized = $normalizePlain($intranetCostPriceRaw);
        $godoGoodsPriceNormalized = $normalizePlain($godoGoodsPriceRaw);
        $intranetGoodsPriceNormalized = $normalizePlain($intranetGoodsPriceRaw);
        $goodsWeightNormalized = $normalizePlain($goodsWeightRaw);
        $innerLengthNormalized = $normalizePlain($innerLengthRaw);
        $normalizedIntranetBarcode = preg_replace('/\s+/', '', $intranetBarcode);
        $normalizedGodoGoodsModelNo = preg_replace('/\s+/', '', $godoGoodsModelNo);
        $normalizedIntranetBarcode = is_string($normalizedIntranetBarcode) ? $normalizedIntranetBarcode : '';
        $normalizedGodoGoodsModelNo = is_string($normalizedGodoGoodsModelNo) ? $normalizedGodoGoodsModelNo : '';

        $onaholeWeightCategoryMap = $this->buildCategoryMap(self::ONAHOLE_WEIGHT_CATEGORIES);
        $onaholePriceCategoryMap = $this->buildCategoryMap(self::ONAHOLE_PRICE_CATEGORIES);
        $onaholeInnerLengthCategoryMap = $this->buildCategoryMap(self::ONAHOLE_INNER_LENGTH_CATEGORIES);
        $marginGradeCategoryMap = $this->buildMarginGradeCategoryMap(self::MARGIN_GRADE_CATEGORIES);
        $onaholeHbtiCategoryMap = $this->buildHbtiCategoryMap(self::ONAHOLE_HBTI_CATEGORIES);

        $brand1Codes = array_keys(self::BRAND1_CATEGORY_LIST);
        $brand1CodeMap = array_fill_keys($brand1Codes, true);
        $categoryCodeList = [];
        foreach ($godoCategoryLines as $categoryRow) {
            if (!is_array($categoryRow)) {
                continue;
            }
            $cateCd = trim((string)($categoryRow['cateCd'] ?? ''));
            if ($cateCd !== '') {
                $categoryCodeList[] = $cateCd;
            }
        }
        $categoryCodeList = array_values(array_unique($categoryCodeList));

        $matchedBrand1Codes = [];
        $matchedBrand2Codes = [];
        $matchedBrand2Rows = [];
        foreach ($categoryCodeList as $cateCd) {
            if (isset($brand1CodeMap[$cateCd]) && strlen($cateCd) === 3) {
                $matchedBrand1Codes[] = $cateCd;
            }
            if (strlen($cateCd) === 6) {
                $matchedBrand2Codes[] = $cateCd;
            }
        }
        foreach ($godoCategoryLines as $categoryRow) {
            if (!is_array($categoryRow)) {
                continue;
            }
            $cateCd = trim((string)($categoryRow['cateCd'] ?? ''));
            if (strlen($cateCd) !== 6) {
                continue;
            }
            $matchedBrand2Rows[] = [
                'cateCd' => $cateCd,
                'line' => trim((string)($categoryRow['line'] ?? '')),
            ];
        }
        $matchedBrand1Codes = array_values(array_unique($matchedBrand1Codes));
        $matchedBrand2Codes = array_values(array_unique($matchedBrand2Codes));
        $hasBrand1Category = !empty($matchedBrand1Codes);
        $hasBrand2Category = false;
        foreach ($matchedBrand2Codes as $brand2Code) {
            if (isset($brand1CodeMap[substr($brand2Code, 0, 3)])) {
                $hasBrand2Category = true;
                break;
            }
        }

        $isBrandHierarchyMismatch = false;
        $mismatchBrand1Code = '';
        $mismatchBrand2Code = '';
        $mismatchBrand2Line = '';
        if ($hasBrand1Category && $hasBrand2Category) {
            $hasAlignedBrand12 = false;
            foreach ($matchedBrand1Codes as $brand1Code) {
                foreach ($matchedBrand2Codes as $brand2Code) {
                    if (strpos($brand2Code, $brand1Code) === 0) {
                        $hasAlignedBrand12 = true;
                        break 2;
                    }
                }
            }
            if (!$hasAlignedBrand12) {
                $isBrandHierarchyMismatch = true;
                $mismatchBrand1Code = (string)($matchedBrand1Codes[0] ?? '');
                foreach ($matchedBrand2Rows as $brand2Row) {
                    $brand2Code = (string)($brand2Row['cateCd'] ?? '');
                    if ($brand2Code === '' || strlen($brand2Code) !== 6) {
                        continue;
                    }
                    if (!isset($brand1CodeMap[substr($brand2Code, 0, 3)])) {
                        continue;
                    }
                    $mismatchBrand2Code = $brand2Code;
                    $mismatchBrand2Line = (string)($brand2Row['line'] ?? '');
                    break;
                }
                if ($mismatchBrand2Code === '' && !empty($matchedBrand2Codes)) {
                    $mismatchBrand2Code = (string)$matchedBrand2Codes[0];
                }
            }
        }

        $categoryAddQueue = [];
        $categoryDeleteQueue = [];
        $queueAddCategory = static function (array &$targetQueue, string $cateCd, string $cateNm): void {
            if ($cateCd === '') {
                return;
            }
            foreach ($targetQueue as $queueRow) {
                if ((string)($queueRow['cateCd'] ?? '') === $cateCd) {
                    return;
                }
            }
            $targetQueue[] = [
                'cateCd' => $cateCd,
                'cateNm' => $cateNm,
            ];
        };

        $currentOnaholeWeightCategories = [];
        $currentOnaholePriceCategories = [];
        $currentOnaholeTypeCategories = [];
        $currentOnaholeHbtiCategories = [];
        $currentOnaholeInnerLengthCategories = [];
        $currentMarginGradeCategories = [];
        foreach ($godoCategoryLines as $categoryRow) {
            if (!is_array($categoryRow)) {
                continue;
            }
            $cateCd = trim((string)($categoryRow['cateCd'] ?? ''));
            $line = trim((string)($categoryRow['line'] ?? ''));
            if (strlen($cateCd) === 9 && strpos($cateCd, '026001') === 0) {
                $currentOnaholeWeightCategories[] = ['cateCd' => $cateCd, 'line' => $line];
            }
            if (strlen($cateCd) === 9 && strpos($cateCd, '026003') === 0) {
                $currentOnaholePriceCategories[] = ['cateCd' => $cateCd, 'line' => $line];
            }
            if (strlen($cateCd) === 9 && strpos($cateCd, '026005') === 0) {
                $currentOnaholeTypeCategories[] = ['cateCd' => $cateCd, 'line' => $line];
            }
            if (strlen($cateCd) === 9 && strpos($cateCd, '026010') === 0) {
                $currentOnaholeHbtiCategories[] = ['cateCd' => $cateCd, 'line' => $line];
            }
            if (strlen($cateCd) === 9 && strpos($cateCd, '026006') === 0) {
                $currentOnaholeInnerLengthCategories[] = ['cateCd' => $cateCd, 'line' => $line];
            }
            if (strlen($cateCd) === 9 && strpos($cateCd, '046001') === 0) {
                $currentMarginGradeCategories[] = ['cateCd' => $cateCd, 'line' => $line];
            }
        }

        $targetOnaholeWeightCategory = $this->findTargetCategoryByValue(self::ONAHOLE_WEIGHT_CATEGORIES, $goodsWeightNormalized);
        $targetOnaholeInnerLengthCategory = $this->findTargetCategoryByValue(self::ONAHOLE_INNER_LENGTH_CATEGORIES, $innerLengthNormalized);
        $targetMarginGradeCategory = $marginGradeCategoryMap[$marginGrade] ?? null;
        $targetOnaholePriceCategory = null;

        $inspectionIssues = [];

        if (!$isMatchedByGoodsNo && $psIdx <= 0) {
            $inspectionIssues[] = ['required' => '필수', 'issue' => '재고코드 미생성', 'solution' => '<span>재고코드를 입력해주세요.</span>'];
        }
        if (!$isMatchedByGoodsNo && $psIdx > 0) {
            $inspectionIssues[] = ['required' => '필수', 'issue' => '재고코드는 있으나 매칭된 고도몰 상품번호가 없음', 'solution' => '<span>고도몰에 재고코드가 등록된 상품이 없습니다.</span>'];
        }
        if ($isMatchedByGoodsNo && $hasCdGodoCode && $goodsNo !== $cdGodoCode) {
            $inspectionIssues[] = [
                'required' => '필수',
                'issue' => '상품번호 불일치',
                'solution' => "<span>고도몰상품에 매칭된 상품 번호와 인트라넷 등록된 번호가 일치하지 않습니다.</span>\n매칭된 고도몰 상품번호 : <b>{$goodsNo}</b>\n인트라넷 등록된 상품번호 : <b>{$cdGodoCode}</b>",
            ];
        }
        if ($isMatchedByGoodsNo && $onlyAdultFl !== 'y') {
            $inspectionIssues[] = ['required' => '참고', 'issue' => '성인인증', 'solution' => '<span>성인인증 사용이 체크되어 있지 않습니다.</span>'];
        }

        if ($isMatchedByGoodsNo && $cdKindCode === 'ONAHOLE') {
            if ($goodsWeightNormalized === '' || !is_numeric($goodsWeightNormalized) || (float)$goodsWeightNormalized <= 0) {
                $inspectionIssues[] = ['required' => '필수', 'issue' => '상품중량 미입력', 'solution' => '<span>인트라넷에 상줌중량 정보가 없습니다.</span>'];
            }
            if ($innerLengthNormalized === '' || !is_numeric($innerLengthNormalized) || (float)$innerLengthNormalized <= 0) {
                $inspectionIssues[] = ['required' => '필수', 'issue' => '내부길이 미입력', 'solution' => '<span>인트라넷에 내부길이 정보가 없습니다.</span>'];
            }
            if (empty($currentOnaholeTypeCategories)) {
                $inspectionIssues[] = ['required' => '참고', 'issue' => '유형별 카테고리 미지정', 'solution' => '<span>오나홀 유형별 카테고리(026005???)가 지정되어 있지 않습니다.</span>'];
            }
            if (empty($currentOnaholeHbtiCategories)) {
                $inspectionIssues[] = ['required' => '참고', 'issue' => 'HBTI 카테고리 미지정', 'solution' => '<span>오나홀 HBTI 카테고리(026010???)가 지정되어 있지 않습니다.</span>'];
            }
        }

        if ($isMatchedByGoodsNo && $cdKindCode === 'ONAHOLE' && $targetOnaholeInnerLengthCategory !== null) {
            $this->appendCategoryMismatchIssue(
                $inspectionIssues,
                $categoryAddQueue,
                $categoryDeleteQueue,
                $queueAddCategory,
                $currentOnaholeInnerLengthCategories,
                (string)($targetOnaholeInnerLengthCategory['cateCd'] ?? ''),
                (string)($targetOnaholeInnerLengthCategory['cateNm'] ?? ''),
                '내부길이 카테고리 미지정',
                '내부길이 카테고리 오류',
                "<span>오나홀 > 내부길이 카테고리가 미지정되어 있습니다.</span>",
                "<span>오나홀 > 내부길이 카테고리 오분류</span>\n현재 설정된 내부길이 : <b>{$innerLengthRaw}</b>",
                $onaholeInnerLengthCategoryMap
            );
        }

        if ($isMatchedByGoodsNo && $cdKindCode === 'ONAHOLE' && $cdHbti !== '') {
            $targetHbtiCategory = $onaholeHbtiCategoryMap[$cdHbti] ?? null;
            if ($targetHbtiCategory !== null) {
                $targetHbtiCateCd = (string)($targetHbtiCategory['cateCd'] ?? '');
                $targetHbtiCateNm = (string)($targetHbtiCategory['cateNm'] ?? '');
                $hasExpectedHbtiCategory = false;
                $wrongHbtiCategoryLines = [];
                foreach ($currentOnaholeHbtiCategories as $hbtiCategoryRow) {
                    $currentCateCd = (string)($hbtiCategoryRow['cateCd'] ?? '');
                    $currentLine = (string)($hbtiCategoryRow['line'] ?? '');
                    if ($currentCateCd === $targetHbtiCateCd) {
                        $hasExpectedHbtiCategory = true;
                        continue;
                    }
                    $lineLabel = $currentLine !== '' ? $currentLine : $currentCateCd;
                    $wrongHbtiCategoryLines[] = $lineLabel . ' ( ' . $currentCateCd . ' ) - 삭제';
                    $queueAddCategory($categoryDeleteQueue, $currentCateCd, '');
                }
                if (!$hasExpectedHbtiCategory) {
                    $queueAddCategory($categoryAddQueue, $targetHbtiCateCd, $targetHbtiCateNm);
                }
                if (!empty($wrongHbtiCategoryLines) || !$hasExpectedHbtiCategory) {
                    $wrongLineText = !empty($wrongHbtiCategoryLines) ? implode("\n", $wrongHbtiCategoryLines) : '-';
                    $inspectionIssues[] = [
                        'required' => '참고',
                        'issue' => 'HBTI 카테고리 오류',
                        'solution' => "<span>HBTI 값과 카테고리 매핑이 일치하지 않습니다.</span>\ncd_hbti : <b>{$cdHbti}</b>\n오분류 카테고리 : {$wrongLineText}\n알맞은 카테고리 : <b>{$targetHbtiCateNm}</b> ( {$targetHbtiCateCd} ) - 추가",
                    ];
                }
            }
        }

        if ($isMatchedByGoodsNo && $cdKindCode === 'ONAHOLE' && $targetOnaholeWeightCategory !== null) {
            $this->appendCategoryMismatchIssue(
                $inspectionIssues,
                $categoryAddQueue,
                $categoryDeleteQueue,
                $queueAddCategory,
                $currentOnaholeWeightCategories,
                (string)($targetOnaholeWeightCategory['cateCd'] ?? ''),
                (string)($targetOnaholeWeightCategory['cateNm'] ?? ''),
                '카테고리 미지정',
                '카테고리 오류',
                "<span>오나홀 > 중량별 카테고리가 미지정되어 있습니다.</span>",
                "<span>오나홀 > 중량별 카테고리 오분류</span>\n현재 설정된 중량 : <b>{$goodsWeightRaw}</b>",
                $onaholeWeightCategoryMap
            );
        }

        if ($isMatchedByGoodsNo) {
            if ($targetMarginGradeCategory === null) {
                $inspectionIssues[] = ['required' => '필수', 'issue' => '마진그룹 미산출', 'solution' => '<span>판매가/원가 기준 마진그룹이 산출되지 않습니다.</span>'];
            } else {
                $this->appendCategoryMismatchIssue(
                    $inspectionIssues,
                    $categoryAddQueue,
                    $categoryDeleteQueue,
                    $queueAddCategory,
                    $currentMarginGradeCategories,
                    (string)($targetMarginGradeCategory['cateCd'] ?? ''),
                    (string)($targetMarginGradeCategory['cateNm'] ?? ''),
                    '마진그룹 카테고리 미지정',
                    '마진그룹 카테고리 오류',
                    "<span>마진그룹 카테고리가 지정되어 있지 않습니다.</span>",
                    "<span>마진그룹 카테고리 오분류</span>",
                    []
                );
            }
        }

        if ($isMatchedByGoodsNo && $hasBrand1Category && !$hasBrand2Category) {
            $inspectionIssues[] = ['required' => '필수', 'issue' => '브랜드 2차 카테고리 미지정', 'solution' => '<span>브랜드 2차 카테고리가 지정되어 있지 않습니다.</span>'];
        }
        if ($isMatchedByGoodsNo && $isBrandHierarchyMismatch) {
            $brand1Name = (string)(self::BRAND1_CATEGORY_LIST[$mismatchBrand1Code] ?? '-');
            $brand2Line = $mismatchBrand2Line !== '' ? $mismatchBrand2Line : '-';
            $inspectionIssues[] = [
                'required' => '참고',
                'issue' => '브랜드 1차/2차 불일치',
                'solution' => "<span>브랜드 1차와 2차카테고리가 알맞지 않습니다.</span>\n1차 카테고리 : {$brand1Name} ( {$mismatchBrand1Code} )\n2차 카테고리 : {$brand2Line} ( {$mismatchBrand2Code} )",
            ];
        }
        if ($isMatchedByGoodsNo && $godoGoodsModelNo === '') {
            $inspectionIssues[] = ['required' => '필수', 'issue' => '바코드 미입력', 'solution' => '<span>고도몰에 바코드가 입력되있지 않습니다.</span>'];
        }
        if ($isMatchedByGoodsNo && $godoGoodsModelNo !== '' && $normalizedIntranetBarcode !== $normalizedGodoGoodsModelNo) {
            $inspectionIssues[] = [
                'required' => '필수',
                'issue' => '바코드 불일치',
                'solution' => "<span>인트라넷 바코드와 고도몰 바코드가 일치 하지않습니다.</span>\n인트라넷 : <b>{$intranetBarcode}</b>\n고도몰 : <b>{$godoGoodsModelNo}</b>",
            ];
        }

        $normalizePriceValue = static function (string $value): ?string {
            if ($value === '' || !is_numeric($value)) {
                return null;
            }
            if (strpos($value, '.') === false) {
                return ltrim($value, '0') === '' ? '0' : ltrim($value, '0');
            }
            $trimmed = rtrim(rtrim($value, '0'), '.');
            if ($trimmed === '' || $trimmed === '-0') {
                return '0';
            }
            if (strpos($trimmed, '.') === false) {
                return ltrim($trimmed, '0') === '' ? '0' : ltrim($trimmed, '0');
            }
            [$intPart, $decimalPart] = explode('.', $trimmed, 2);
            $intPart = ltrim($intPart, '0');
            if ($intPart === '' || $intPart === '-') {
                $intPart = '0';
            }
            return $intPart . '.' . $decimalPart;
        };
        $formatPriceDisplay = static function (string $rawValue): string {
            $value = str_replace(',', '', trim($rawValue));
            $value = preg_replace('/\s+/', '', $value);
            $value = is_string($value) ? $value : '';
            if ($value === '' || !is_numeric($value)) {
                return $rawValue;
            }
            $decimals = (strpos($value, '.') !== false) ? 2 : 0;
            $formatted = number_format((float)$value, $decimals, '.', ',');
            if (substr($formatted, -3) === '.00') {
                $formatted = substr($formatted, 0, -3);
            }
            return $formatted;
        };

        $normalizedIntranetCostCompare = $normalizePriceValue($intranetCostPriceNormalized);
        $normalizedGodoCostCompare = $normalizePriceValue($godoCostPriceNormalized);
        $hasIntranetCost = ($normalizedIntranetCostCompare !== null && (float)$normalizedIntranetCostCompare > 0);
        if ($isMatchedByGoodsNo && !$hasIntranetCost) {
            $inspectionIssues[] = ['required' => '필수', 'issue' => '원가정보 없음', 'solution' => '<span>인트라넷에 책정원가가 없습니다.</span>'];
        }
        if ($isMatchedByGoodsNo && $hasIntranetCost && ($godoCostPriceNormalized === '' || !is_numeric($godoCostPriceNormalized) || (float)$godoCostPriceNormalized <= 0)) {
            $inspectionIssues[] = [
                'required' => '필수',
                'issue' => '원가 미입력',
                'solution' => "<span>고도몰에 원가정보가 미입력되어 있습니다.</span>\n인트라넷 책정원가 : <b>{$formatPriceDisplay($intranetCostPriceRaw)}</b>",
            ];
        }
        if ($isMatchedByGoodsNo && $hasIntranetCost && $normalizedGodoCostCompare !== null && (float)$normalizedGodoCostCompare > 0) {
            $isCostMismatch = ($normalizedIntranetCostCompare === null || $normalizedIntranetCostCompare !== $normalizedGodoCostCompare);
            if ($isCostMismatch) {
                $inspectionIssues[] = [
                    'required' => '필수',
                    'issue' => '원가 불일치',
                    'solution' => "<span>인트라넷 책정원가와 고도몰 원가값이 틀립니다.</span>\n인트라넷 : <b>{$formatPriceDisplay($intranetCostPriceRaw)}</b>\n고도몰 : <b>{$formatPriceDisplay($godoCostPriceRaw)}</b>",
                ];
            }
        }

        $normalizedIntranetGoodsPriceCompare = $normalizePriceValue($intranetGoodsPriceNormalized);
        $normalizedGodoGoodsPriceCompare = $normalizePriceValue($godoGoodsPriceNormalized);
        if ($isMatchedByGoodsNo && $normalizedIntranetGoodsPriceCompare !== $normalizedGodoGoodsPriceCompare) {
            $inspectionIssues[] = [
                'required' => '필수',
                'issue' => '판매가 불일치',
                'solution' => "<span>판매가가 일치하지 않습니다.</span>\n인트라넷 : <b>{$formatPriceDisplay($intranetGoodsPriceRaw)}</b>\n고도몰 : <b>{$formatPriceDisplay($godoGoodsPriceRaw)}</b>",
            ];
        }

        if ($isMatchedByGoodsNo && $cdKindCode === 'ONAHOLE' && $normalizedIntranetGoodsPriceCompare !== null && $normalizedIntranetGoodsPriceCompare === $normalizedGodoGoodsPriceCompare) {
            $targetOnaholePriceCategory = $this->findTargetCategoryByValue(self::ONAHOLE_PRICE_CATEGORIES, (string)$normalizedIntranetGoodsPriceCompare);
            if ($targetOnaholePriceCategory !== null) {
                $this->appendCategoryMismatchIssue(
                    $inspectionIssues,
                    $categoryAddQueue,
                    $categoryDeleteQueue,
                    $queueAddCategory,
                    $currentOnaholePriceCategories,
                    (string)($targetOnaholePriceCategory['cateCd'] ?? ''),
                    (string)($targetOnaholePriceCategory['cateNm'] ?? ''),
                    '가격별 카테고리 미지정',
                    '가격별 카테고리 오류',
                    "<span>오나홀 > 가격별 카테고리가 미지정되어 있습니다.</span>\n현재 판매 가격 : <b>{$formatPriceDisplay($intranetGoodsPriceRaw)}</b>",
                    "<span>오나홀 > 가격별 카테고리 오분류</span>\n현재 판매 가격 : <b>{$formatPriceDisplay($intranetGoodsPriceRaw)}</b>",
                    $onaholePriceCategoryMap
                );
            }
        }

        $categoryAddCodeList = [];
        foreach ($categoryAddQueue as $row) {
            $cd = trim((string)($row['cateCd'] ?? ''));
            if ($cd !== '') {
                $categoryAddCodeList[] = $cd;
            }
        }
        $categoryDeleteCodeList = [];
        foreach ($categoryDeleteQueue as $row) {
            $cd = trim((string)($row['cateCd'] ?? ''));
            if ($cd !== '') {
                $categoryDeleteCodeList[] = $cd;
            }
        }

        return [
            'is_matched_by_goods_no' => $isMatchedByGoodsNo,
            'inspection_issues' => $inspectionIssues,
            'category_add_codes_for_sync' => implode(',', array_values(array_unique($categoryAddCodeList))),
            'category_delete_codes_for_sync' => implode(',', array_values(array_unique($categoryDeleteCodeList))),
            'intranet_barcode' => $intranetBarcode,
            'intranet_cost_price_raw' => $intranetCostPriceRaw,
            'intranet_goods_price_raw' => $intranetGoodsPriceRaw,
            'godo_category_lines' => $godoCategoryLines,
        ];
    }

    public function resolveIssueActionMeta(string $issueName, string $intranetBarcode = ''): array
    {
        $actionTarget = '-';
        $actionState = '확인필요';
        $actionReason = '처리 방식 확인 필요';

        switch (trim($issueName)) {
            case '재고코드 미생성':
                $actionTarget = '인트라넷';
                $actionState = '자동처리 불가';
                $actionReason = '재고코드 정보 부족';
                break;
            case '재고코드는 있으나 매칭된 고도몰 상품번호가 없음':
                $actionTarget = '고도몰';
                $actionState = '자동처리 불가';
                $actionReason = '매칭된 고도몰 상품 없음';
                break;
            case '상품번호 불일치':
                $actionTarget = '인트라넷';
                $actionState = '자동처리 가능';
                $actionReason = 'cd_godo_code 동기화';
                break;
            case '성인인증':
                $actionTarget = '고도몰';
                $actionState = '자동처리 가능';
                $actionReason = '성인인증 사용으로 자동 설정';
                break;
            case '상품중량 미입력':
            case '내부길이 미입력':
            case '원가정보 없음':
                $actionTarget = '인트라넷';
                $actionState = '자동처리 불가';
                $actionReason = '원천 데이터 없음';
                break;
            case '원가 미입력':
            case '원가 불일치':
            case '바코드 불일치':
                $actionTarget = '고도몰';
                $actionState = '자동처리 가능';
                $actionReason = '인트라넷 값 기준 갱신';
                break;
            case '판매가 불일치':
                $actionTarget = '인트라넷';
                $actionState = '자동처리 가능';
                $actionReason = '고도몰 판매가로 인트라넷 판매가 동기화';
                break;
            case '바코드 미입력':
                $actionTarget = '고도몰';
                if (trim($intranetBarcode) !== '') {
                    $actionState = '자동처리 가능';
                    $actionReason = '인트라넷 바코드로 입력';
                } else {
                    $actionState = '자동처리 불가';
                    $actionReason = '인트라넷 바코드 없음';
                }
                break;
            case '브랜드 1차 카테고리 미지정':
            case '브랜드 2차 카테고리 미지정':
            case '브랜드 1차/2차 불일치':
                $actionTarget = '고도몰';
                $actionState = '자동처리 불가';
                $actionReason = '타겟 카테고리 정보 부족';
                break;
            case '유형별 카테고리 미지정':
            case 'HBTI 카테고리 오류':
            case 'HBTI 카테고리 미지정':
            case '내부길이 카테고리 미지정':
            case '내부길이 카테고리 오류':
            case '가격별 카테고리 미지정':
            case '가격별 카테고리 오류':
            case '카테고리 미지정':
            case '카테고리 오류':
            case '마진그룹 카테고리 미지정':
            case '마진그룹 카테고리 오류':
                $actionTarget = '고도몰';
                $actionState = '자동처리 가능';
                $actionReason = '카테고리 추가/삭제 큐 반영';
                break;
            case '마진그룹 미산출':
                $actionTarget = '인트라넷';
                $actionState = '자동처리 불가';
                $actionReason = '판매가/원가 기준 산출 불가';
                break;
        }

        return [
            'target' => $actionTarget,
            'state' => $actionState,
            'reason' => $actionReason,
        ];
    }

    private function buildCategoryMap(array $rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $cateCd = trim((string)($row['cateCd'] ?? ''));
            if ($cateCd === '') {
                continue;
            }
            $map[$cateCd] = [
                'cateNm' => trim((string)($row['cateNm'] ?? '')),
                'cateCd' => $cateCd,
            ];
        }
        return $map;
    }

    private function buildMarginGradeCategoryMap(array $rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            $cateNm = strtoupper(trim((string)($row['cateNm'] ?? '')));
            if ($cateNm === '') {
                continue;
            }
            $map[$cateNm] = [
                'cateNm' => $cateNm,
                'cateCd' => trim((string)($row['cateCd'] ?? '')),
            ];
        }
        return $map;
    }

    private function buildHbtiCategoryMap(array $rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            $hbtiCode = strtoupper(trim((string)($row['hbti'] ?? '')));
            $cateCd = trim((string)($row['cateCd'] ?? ''));
            if ($hbtiCode === '' || $cateCd === '') {
                continue;
            }
            $map[$hbtiCode] = [
                'hbti' => $hbtiCode,
                'cateNm' => trim((string)($row['cateNm'] ?? '')),
                'cateCd' => $cateCd,
            ];
        }
        return $map;
    }

    private function findTargetCategoryByValue(array $rows, string $value): ?array
    {
        if ($value === '' || !is_numeric($value) || (float)$value <= 0) {
            return null;
        }
        $targetValue = (float)$value;
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $min = isset($row['min']) ? (float)$row['min'] : 0.0;
            $max = isset($row['max']) && $row['max'] !== null ? (float)$row['max'] : null;
            if ($targetValue < $min) {
                continue;
            }
            if ($max !== null && $targetValue > $max) {
                continue;
            }
            $targetCateCd = trim((string)($row['cateCd'] ?? ''));
            if ($targetCateCd === '') {
                continue;
            }
            return [
                'cateNm' => trim((string)($row['cateNm'] ?? '')),
                'cateCd' => $targetCateCd,
            ];
        }
        return null;
    }

    private function appendCategoryMismatchIssue(
        array &$inspectionIssues,
        array &$categoryAddQueue,
        array &$categoryDeleteQueue,
        callable $queueAddCategory,
        array $currentCategories,
        string $targetCateCd,
        string $targetCateNm,
        string $missingIssueName,
        string $wrongIssueName,
        string $missingPrefixText,
        string $wrongPrefixText,
        array $categoryNameMap
    ): void {
        if ($targetCateCd === '') {
            return;
        }
        if (empty($currentCategories)) {
            $queueAddCategory($categoryAddQueue, $targetCateCd, $targetCateNm);
            $inspectionIssues[] = [
                'required' => '필수',
                'issue' => $missingIssueName,
                'solution' => $missingPrefixText . "\n추가 카테고리 : <b>{$targetCateNm}</b> ( {$targetCateCd} )",
            ];
            return;
        }

        $hasExpected = false;
        $wrongLines = [];
        foreach ($currentCategories as $row) {
            $currentCateCd = (string)($row['cateCd'] ?? '');
            $currentLine = (string)($row['line'] ?? '');
            if ($currentCateCd === $targetCateCd) {
                $hasExpected = true;
                continue;
            }
            $currentCateNm = (string)($categoryNameMap[$currentCateCd]['cateNm'] ?? '');
            $lineLabel = $currentLine !== '' ? $currentLine : ($currentCateNm !== '' ? $currentCateNm : $currentCateCd);
            $wrongLines[] = $lineLabel . ' ( ' . $currentCateCd . ' ) - 삭제';
            $queueAddCategory($categoryDeleteQueue, $currentCateCd, $currentCateNm);
        }

        if (!empty($wrongLines) || !$hasExpected) {
            if (!$hasExpected) {
                $queueAddCategory($categoryAddQueue, $targetCateCd, $targetCateNm);
            }
            $wrongLineText = !empty($wrongLines) ? implode("\n", $wrongLines) : '-';
            $inspectionIssues[] = [
                'required' => '필수',
                'issue' => $wrongIssueName,
                'solution' => $wrongPrefixText . "\n오분류 카테고리 : {$wrongLineText}\n알맞은 카테고리 : <b>{$targetCateNm}</b> ( {$targetCateCd} ) - 추가",
            ];
        }
    }

    /**
     * 카테고리 코드로 기본 카테고리명을 조회한다.
     *
     * @param string $cateCd
     * @return string
     */
    public function getCategoryNameByCode(string $cateCd): string
    {
        $cateCd = trim($cateCd);
        if ($cateCd === '') {
            return '';
        }

        $maps = [
            self::ONAHOLE_WEIGHT_CATEGORIES,
            self::ONAHOLE_PRICE_CATEGORIES,
            self::ONAHOLE_HBTI_CATEGORIES,
            self::ONAHOLE_INNER_LENGTH_CATEGORIES,
            self::MARGIN_GRADE_CATEGORIES,
        ];

        foreach ($maps as $rows) {
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                if (trim((string)($row['cateCd'] ?? '')) !== $cateCd) {
                    continue;
                }
                return trim((string)($row['cateNm'] ?? ''));
            }
        }

        if (isset(self::BRAND1_CATEGORY_LIST[$cateCd])) {
            return (string)self::BRAND1_CATEGORY_LIST[$cateCd];
        }

        return '';
    }

    /**
     * 현재 검수 버전명
     *
     * @return string
     */
    public function getInspectionVersion(): string
    {
        return self::INSPECTION_VERSION;
    }
}

