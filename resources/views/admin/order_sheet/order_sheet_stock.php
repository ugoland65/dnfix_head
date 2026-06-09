<?php
    $orderSheetIdx = isset($orderSheetIdx) ? (int)$orderSheetIdx : 0;
    $orderSheetName = isset($orderSheetName) ? (string)$orderSheetName : '';
    $orderSheetStockState = (isset($orderSheetStockState) && is_array($orderSheetStockState)) ? $orderSheetStockState : [];
    $stockItems = (isset($stockItems) && is_array($stockItems)) ? $stockItems : [];
    $godoApiErrorMessage = isset($godoApiErrorMessage) ? (string)$godoApiErrorMessage : '';
    $godoRestockApiErrorMessage = isset($godoRestockApiErrorMessage) ? (string)$godoRestockApiErrorMessage : '';
    $godoInfoLoadedAt = isset($godoInfoLoadedAt) ? (string)$godoInfoLoadedAt : date('Y-m-d H:i:s');
    $godoInfoLoadMs = isset($godoInfoLoadMs) ? (int)$godoInfoLoadMs : 0;
    $defaultStockDay = isset($defaultStockDay) ? (string)$defaultStockDay : date('Y-m-d');
    $defaultStockMemo = isset($defaultStockMemo) ? (string)$defaultStockMemo : '';
    $inspectionVersion = (new \App\Services\GodoInspectionService())->getInspectionVersion();
    $isOnFlag = static function ($value): bool {
        if (is_bool($value)) {
            return $value;
        }
        $value = trim((string)$value);
        return in_array($value, ['1', 'Y', 'y', 'true', 'TRUE'], true);
    };

    //브랜드 1차 카테고리 목록
    $brand1CategoryList = [
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

    //오나홀 중량별 카테고리 목록
    $onaholeWeightCategories = [
        [
            'cateNm' => '200g 미만',
            'cateCd' => '026001006',
            'min' => 0,
            'max' => 199,
        ],
        [
            'cateNm' => '200g ~ 299g',
            'cateCd' => '026001002',
            'min' => 200,
            'max' => 299,
        ],
        [
            'cateNm' => '300g ~ 399g',
            'cateCd' => '026001005',
            'min' => 300,
            'max' => 399,
        ],
        [
            'cateNm' => '400g ~ 499g',
            'cateCd' => '026001007',
            'min' => 400,
            'max' => 499,
        ],
        [
            'cateNm' => '500g ~ 599g',
            'cateCd' => '026001008',
            'min' => 500,
            'max' => 599,
        ],
        [
            'cateNm' => '600g ~ 799g',
            'cateCd' => '026001001',
            'min' => 600,
            'max' => 799,
        ],
        [
            'cateNm' => '800g ~ 999g',
            'cateCd' => '026001009',
            'min' => 800,
            'max' => 999,
        ],
        [
            'cateNm' => '1kg ~ 2.99kg',
            'cateCd' => '026001004',
            'min' => 1000,
            'max' => 2999,
        ],
        [
            'cateNm' => '3kg ~ 4.99kg',
            'cateCd' => '026001003',
            'min' => 3000,
            'max' => 4999,
        ],
        [
            'cateNm' => '5kg 이상',
            'cateCd' => '026001010',
            'min' => 5000,
            'max' => null,
        ],
    ];

    //오나홀 가격별 카테고리 목록
    $onaholePriceCategories = [
        [
            'cateNm' => '2만원 이하',
            'cateCd' => '026003001',
            'min' => 0,
            'max' => 19999,
        ],
        [
            'cateNm' => '2만원~3만원',
            'cateCd' => '026003006',
            'min' => 20000,
            'max' => 29999,
        ],
        [
            'cateNm' => '3만원~4만원',
            'cateCd' => '026003007',
            'min' => 30000,
            'max' => 39999,
        ],
        [
            'cateNm' => '4만원~5만원',
            'cateCd' => '026003003',
            'min' => 40000,
            'max' => 49999,
        ],
        [
            'cateNm' => '5만원~7만원',
            'cateCd' => '026003004',
            'min' => 50000,
            'max' => 69999,
        ],
        [
            'cateNm' => '7만원~10만원',
            'cateCd' => '026003005',
            'min' => 70000,
            'max' => 99999,
        ],
        [
            'cateNm' => '10만원~',
            'cateCd' => '026003002',
            'min' => 100000,
            'max' => null,
        ],
    ];

    //오나홀 HBTI 카테고리 목록
    $onaholeHbtiCategories = [
        [
            'hbti'   => 'SRJT',
            'cateNm' => 'SRJT-리얼 전략가',
            'cateCd' => '026010001',
        ],
        [
            'hbti'   => 'SRJE',
            'cateNm' => 'SRJE-감성 기획자',
            'cateCd' => '026010002',
        ],
        [
            'hbti'   => 'SRPT',
            'cateNm' => 'SRPT-자유 분석러',
            'cateCd' => '026010003',
        ],
        [
            'hbti'   => 'SRPE',
            'cateNm' => 'SRPE-감성 탐험가',
            'cateCd' => '026010004',
        ],
        [
            'hbti'   => 'SFJT',
            'cateNm' => 'SFJT-체계 몽상가',
            'cateCd' => '026010005',
        ],
        [
            'hbti'   => 'SFJE',
            'cateNm' => 'SFJE-감성 큐레이터',
            'cateCd' => '026010006',
        ],
        [
            'hbti'   => 'SFPT',
            'cateNm' => 'SFPT-판타지 러버',
            'cateCd' => '026010007',
        ],
        [
            'hbti'   => 'SFPE',
            'cateNm' => 'SFPE-감성 몽상가',
            'cateCd' => '026010008',
        ],
        [
            'hbti'   => 'HRJT',
            'cateNm' => 'HRJT-강한 현실주의자',
            'cateCd' => '026010009',
        ],
        [
            'hbti'   => 'HRJE',
            'cateNm' => 'HRJE-크리에이터',
            'cateCd' => '026010010',
        ],
        [
            'hbti'   => 'HRPT',
            'cateNm' => 'HRPT-테크 관찰자',
            'cateCd' => '026010011',
        ],
        [
            'hbti'   => 'HRPE',
            'cateNm' => 'HRPE-아티스트',
            'cateCd' => '026010012',
        ],
        [
            'hbti'   => 'HFJT',
            'cateNm' => 'HFJT-전략 마법사',
            'cateCd' => '026010013',
        ],
        [
            'hbti'   => 'HFPT',
            'cateNm' => 'HFPT-즉흥 드리머',
            'cateCd' => '026010014',
        ],
        [
            'hbti'   => 'HFPE',
            'cateNm' => 'HFPE-낭만 예술가',
            'cateCd' => '026010015',
        ],
        [
            'hbti'   => 'HFJE',
            'cateNm' => 'HFJE-예술 전사',
            'cateCd' => '026010016',
        ],
    ];

    //오나홀 내부길이 카테고리 목록
    $onaholeInnerLengthCategories = [
        [
            'cateNm' => '10cm 미만',
            'cateCd' => '026006007',
            'min' => 0,
            'max' => 9.99,
        ],
        [
            'cateNm' => '10cm ~ 11.9cm',
            'cateCd' => '026006002',
            'min' => 10,
            'max' => 11.9,
        ],
        [
            'cateNm' => '12cm ~ 12.9cm',
            'cateCd' => '026006003',
            'min' => 12,
            'max' => 12.9,
        ],
        [
            'cateNm' => '13cm ~ 13.9cm',
            'cateCd' => '026006006',
            'min' => 13,
            'max' => 13.9,
        ],
        [
            'cateNm' => '14cm ~ 14.9cm',
            'cateCd' => '026006005',
            'min' => 14,
            'max' => 14.9,
        ],
        [
            'cateNm' => '15cm ~ 15.9cm',
            'cateCd' => '026006001',
            'min' => 15,
            'max' => 15.9,
        ],
        [
            'cateNm' => '16cm 이상',
            'cateCd' => '026006004',
            'min' => 16,
            'max' => null,
        ],
    ];

    //마진그룹
    $marginGradeCategories = [
        [
            'cateNm' => 'A',
            'cateCd' => '046001001',
            'groupRootCateCd' => '046001',
        ],
        [
            'cateNm' => 'B',
            'cateCd' => '046001002',
            'groupRootCateCd' => '046001',
        ],
        [
            'cateNm' => 'C',
            'cateCd' => '046001003',
            'groupRootCateCd' => '046001',
        ],
        [
            'cateNm' => 'D',
            'cateCd' => '046001004',
            'groupRootCateCd' => '046001',
        ],
        [
            'cateNm' => 'E',
            'cateCd' => '046001005',
            'groupRootCateCd' => '046001',
        ],
        [
            'cateNm' => 'F',
            'cateCd' => '046001006',
            'groupRootCateCd' => '046001',
        ],
        [
            'cateNm' => 'G',
            'cateCd' => '046001007',
            'groupRootCateCd' => '046001',
        ],
        [
            'cateNm' => 'H',
            'cateCd' => '046001008',
            'groupRootCateCd' => '046001',
        ],
        [
            'cateNm' => 'I',
            'cateCd' => '046001009',
            'groupRootCateCd' => '046001',
        ],
    ];

?>
<style>
.stock-update-form {
    padding: 20px;
}
.inspection-checklist-table {
    width: 100%;
    margin-top: 6px;
    border-collapse: collapse;
    font-size: 11px;
    color: #374151;
}
.inspection-checklist-th {
    text-align: left;
    padding: 4px 6px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
}
.inspection-checklist-th-no {
    width: 42px;
    text-align: center;
}
.inspection-checklist-th-required {
    width: 58px;
    text-align: center;
}
.inspection-checklist-td {
    padding: 4px 6px;
    border: 1px solid #e5e7eb;
    line-height: 1.4;
    span{
        color: #dc3545;
    }
}
.inspection-checklist-td-center {
    text-align: center;
}
.inspection-checklist-issue {
    color: #dc3545;
}
.inspection-checklist-required {
    font-weight: 700;
}
.inspection-checklist-required-required {
    color: #dc3545;
}
.inspection-checklist-required-ref {
    color: #0d6efd;
}
</style>
<div class="stock-update-form">
    <form id="form_os_stock">
        <input type="hidden" name="action_mode" value="orderSheetAllStock">
        <input type="hidden" name="os_idx" value="<?= $orderSheetIdx ?>">
        <input type="hidden" name="godo_info_loaded_at" value="<?= htmlspecialchars($godoInfoLoadedAt, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="godo_info_load_ms" value="<?= $godoInfoLoadMs ?>">

        <?php if (($orderSheetStockState['state'] ?? '') === 'in') { ?>
            <div>
                재고 일괄 등록이 완료된 상태입니다.
                ( <?= !empty($orderSheetStockState['reg']['date']) ? date("y.m.d H:i:s", strtotime($orderSheetStockState['reg']['date'])) : '' ?>
                | <?= htmlspecialchars((string)($orderSheetStockState['reg']['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?> )
            </div>
        <?php } ?>

        <div class="m-t-8">
            재고 등록일 :
            <div class="calendar-input" style="display:inline-block;">
                <input type="text" name="stock_day" value="<?= htmlspecialchars($defaultStockDay, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <input
                type="text"
                name="stock_all_memo"
                id="stock_all_memo"
                style="width:220px"
                value="<?= htmlspecialchars($defaultStockMemo, ENT_QUOTES, 'UTF-8') ?>"
            >
            <button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetStockPopup.allStock()">재고등록</button>
        </div>
        <?php if ($godoApiErrorMessage !== '') { ?>
            <div class="m-t-8" style="color:#dc3545; font-size:12px;">
                고도몰 정보 조회 실패: <?= htmlspecialchars($godoApiErrorMessage, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php } ?>
        <?php if ($godoRestockApiErrorMessage !== '') { ?>
            <div class="m-t-8" style="color:#dc3545; font-size:12px;">
                고도몰 재입고 알림 조회 실패: <?= htmlspecialchars($godoRestockApiErrorMessage, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php } ?>

        <div class="m-t-8" style="font-size:12px; color:#374151;">
            고도몰 정보 조회시간 : <b><?= htmlspecialchars($godoInfoLoadedAt, ENT_QUOTES, 'UTF-8') ?></b>
            (로딩시간: <b><?= number_format($godoInfoLoadMs) ?>ms</b>)
        </div>

        <!-- 검수항목 -->
        <div class="m-t-8 m-b-8" style="font-size:12px; color:#374151; line-height:1.5; border:1px solid #e5e7eb; background:#f8fafc; padding:10px 12px;">
            <div style="font-weight:700; margin-bottom:4px;">검수항목 안내</div>
            <div><b>현재 검수버전:</b> <?= htmlspecialchars($inspectionVersion, ENT_QUOTES, 'UTF-8') ?></div>
            <div><b>[공통]</b> 매칭상태, 상품번호, 바코드, 원가(미입력/불일치), 판매가 불일치, 마진그룹 카테고리(046001), 브랜드 1차/2차 카테고리를 검사합니다.</div>
            <div><b>[오나홀 전용]</b> 상품중량, 내부길이(CD_SIZE2), 유형별(026005), HBTI(026010), 중량별(026001), 가격별(026003), 내부길이(026006) 카테고리를 추가 검사합니다.</div>
            <div style="color:#6b7280;">* 자동처리 가능: 성인인증, 바코드/원가 동기화, 카테고리 추가/삭제, 판매가(인트라넷) 동기화</div>
            <div style="color:#6b7280;">* 자동처리 불가: 재고코드 미생성, 매칭된 고도몰 상품번호 없음, 브랜드 1차/2차 카테고리 관련 항목</div>
            <div style="color:#6b7280;">* 원천 데이터 없음 항목: 원가정보 없음, 상품중량 미입력, 내부길이 미입력, (바코드 미입력 + 인트라넷 바코드 없음)</div>
            <div style="color:#6b7280;">* 산출 불가 항목: 마진그룹 미산출(판매가/원가 기준으로 마진그룹 계산 불가)</div>
            <div style="color:#6b7280;">* 각 항목은 필수/참고로 구분되며, 오분류 시 삭제/추가 대상 카테고리가 함께 표시됩니다.</div>
        </div>

        <table class="table-list m-t-10">
            <tr>
                <th>상품번호<br>재고코드</th>
                <th>이미지</th>
                <th>상품명</th>
                <th>바코드</th>
                <th>현재고</th>
                <th>고도몰<br/>현재고</th>
                <th>재입고알림<br/>요청수</th>
                <th>매칭/검수</th>
                <th>고도몰<br>카테고리</th>
                <th>주문수량</th>
                <th>실입고수량</th>
                <th>최종적용수량</th>
                <th>메모</th>
            </tr>
            <?php $godoInspectionService = new \App\Services\GodoInspectionService(); ?>
            <?php foreach ($stockItems as $rowIndex => $item) { ?>
                <?php
                $rowBg = !empty($item['is_false']) ? '#eee' : '#fff';
                $pidx = (int)($item['pidx'] ?? 0);
                $psIdx = (int)($item['ps_idx'] ?? 0);
                $qty = (int)($item['qty'] ?? 0);
                $currentStockQty = (int)($item['stock_qty'] ?? 0);
                $goodsNo = (string)($item['godo_goods_no'] ?? '');
                $cdGodoCode = (string)($item['cd_godo_code'] ?? '');
                $hasCdGodoCode = ($cdGodoCode !== '' && $cdGodoCode !== '0');
                $isMatchedByGoodsNo = ($goodsNo !== '');
                $intranetBarcode = (string)($item['barcode'] ?? '');
                $intranetCostPriceRaw = (string)($item['cost_price'] ?? '');
                $intranetGoodsPriceRaw = (string)($item['goods_price'] ?? '');
                $godoCategoryLines = (isset($item['godo_category_lines']) && is_array($item['godo_category_lines'])) ? $item['godo_category_lines'] : [];
                $categoryAddCodesForSync = '';
                $categoryDeleteCodesForSync = '';
                if (false) {
                $onlyAdultFl = strtolower(trim((string)($item['godo_only_adult_fl'] ?? '')));
                $godoGoodsModelNo = trim((string)($item['godo_goods_model_no'] ?? ''));
                $godoCostPriceRaw = (string)($item['godo_cost_price'] ?? '');
                $intranetCostPriceRaw = (string)($item['cost_price'] ?? '');
                $godoGoodsPriceRaw = (string)($item['godo_goods_price'] ?? '');
                $intranetGoodsPriceRaw = (string)($item['goods_price'] ?? '');
                $godoCostPriceNormalized = str_replace(',', '', trim($godoCostPriceRaw));
                $intranetCostPriceNormalized = str_replace(',', '', trim($intranetCostPriceRaw));
                $godoGoodsPriceNormalized = str_replace(',', '', trim($godoGoodsPriceRaw));
                $intranetGoodsPriceNormalized = str_replace(',', '', trim($intranetGoodsPriceRaw));
                $goodsWeightRaw = (string)($item['goods_weight'] ?? '');
                $goodsWeightNormalized = str_replace(',', '', trim($goodsWeightRaw));
                $innerLengthRaw = (string)($item['inner_length'] ?? '');
                $innerLengthNormalized = str_replace(',', '', trim($innerLengthRaw));
                $intranetBarcode = trim((string)($item['barcode'] ?? ''));
                $normalizedIntranetBarcode = preg_replace('/\s+/', '', $intranetBarcode);
                $normalizedGodoGoodsModelNo = preg_replace('/\s+/', '', $godoGoodsModelNo);
                $normalizedIntranetBarcode = is_string($normalizedIntranetBarcode) ? $normalizedIntranetBarcode : '';
                $normalizedGodoGoodsModelNo = is_string($normalizedGodoGoodsModelNo) ? $normalizedGodoGoodsModelNo : '';
                $godoCostPriceNormalized = preg_replace('/\s+/', '', $godoCostPriceNormalized);
                $intranetCostPriceNormalized = preg_replace('/\s+/', '', $intranetCostPriceNormalized);
                $godoGoodsPriceNormalized = preg_replace('/\s+/', '', $godoGoodsPriceNormalized);
                $intranetGoodsPriceNormalized = preg_replace('/\s+/', '', $intranetGoodsPriceNormalized);
                $godoCostPriceNormalized = is_string($godoCostPriceNormalized) ? $godoCostPriceNormalized : '';
                $intranetCostPriceNormalized = is_string($intranetCostPriceNormalized) ? $intranetCostPriceNormalized : '';
                $godoGoodsPriceNormalized = is_string($godoGoodsPriceNormalized) ? $godoGoodsPriceNormalized : '';
                $intranetGoodsPriceNormalized = is_string($intranetGoodsPriceNormalized) ? $intranetGoodsPriceNormalized : '';
                $goodsWeightNormalized = preg_replace('/\s+/', '', $goodsWeightNormalized);
                $goodsWeightNormalized = is_string($goodsWeightNormalized) ? $goodsWeightNormalized : '';
                $innerLengthNormalized = preg_replace('/\s+/', '', $innerLengthNormalized);
                $innerLengthNormalized = is_string($innerLengthNormalized) ? $innerLengthNormalized : '';
                $godoCategoryLines = (isset($item['godo_category_lines']) && is_array($item['godo_category_lines'])) ? $item['godo_category_lines'] : [];
                $marginGrade = strtoupper(trim((string)($item['margin_grade'] ?? '')));
                $cdHbti = strtoupper(trim((string)($item['cd_hbti'] ?? '')));
                $onaholeWeightCategoryMap = [];
                foreach ($onaholeWeightCategories as $weightCategoryRow) {
                    if (!is_array($weightCategoryRow)) {
                        continue;
                    }
                    $cateCd = trim((string)($weightCategoryRow['cateCd'] ?? ''));
                    if ($cateCd === '') {
                        continue;
                    }
                    $onaholeWeightCategoryMap[$cateCd] = [
                        'cateNm' => trim((string)($weightCategoryRow['cateNm'] ?? '')),
                        'cateCd' => $cateCd,
                        'min' => isset($weightCategoryRow['min']) ? (float)$weightCategoryRow['min'] : 0.0,
                        'max' => isset($weightCategoryRow['max']) ? (($weightCategoryRow['max'] === null) ? null : (float)$weightCategoryRow['max']) : null,
                    ];
                }
                $onaholePriceCategoryMap = [];
                foreach ($onaholePriceCategories as $priceCategoryRow) {
                    if (!is_array($priceCategoryRow)) {
                        continue;
                    }
                    $cateCd = trim((string)($priceCategoryRow['cateCd'] ?? ''));
                    if ($cateCd === '') {
                        continue;
                    }
                    $onaholePriceCategoryMap[$cateCd] = [
                        'cateNm' => trim((string)($priceCategoryRow['cateNm'] ?? '')),
                        'cateCd' => $cateCd,
                        'min' => isset($priceCategoryRow['min']) ? (float)$priceCategoryRow['min'] : 0.0,
                        'max' => isset($priceCategoryRow['max']) ? (($priceCategoryRow['max'] === null) ? null : (float)$priceCategoryRow['max']) : null,
                    ];
                }
                $onaholeInnerLengthCategoryMap = [];
                foreach ($onaholeInnerLengthCategories as $lengthCategoryRow) {
                    if (!is_array($lengthCategoryRow)) {
                        continue;
                    }
                    $cateCd = trim((string)($lengthCategoryRow['cateCd'] ?? ''));
                    if ($cateCd === '') {
                        continue;
                    }
                    $onaholeInnerLengthCategoryMap[$cateCd] = [
                        'cateNm' => trim((string)($lengthCategoryRow['cateNm'] ?? '')),
                        'cateCd' => $cateCd,
                        'min' => isset($lengthCategoryRow['min']) ? (float)$lengthCategoryRow['min'] : 0.0,
                        'max' => isset($lengthCategoryRow['max']) ? (($lengthCategoryRow['max'] === null) ? null : (float)$lengthCategoryRow['max']) : null,
                    ];
                }
                $marginGradeCategoryMap = [];
                foreach ($marginGradeCategories as $marginCategoryRow) {
                    if (!is_array($marginCategoryRow)) {
                        continue;
                    }
                    $cateNm = strtoupper(trim((string)($marginCategoryRow['cateNm'] ?? '')));
                    if ($cateNm === '') {
                        continue;
                    }
                    $marginGradeCategoryMap[$cateNm] = [
                        'cateNm' => $cateNm,
                        'cateCd' => trim((string)($marginCategoryRow['cateCd'] ?? '')),
                        'groupRootCateCd' => trim((string)($marginCategoryRow['groupRootCateCd'] ?? '046001')),
                    ];
                }
                $onaholeHbtiCategoryMap = [];
                foreach ($onaholeHbtiCategories as $hbtiCategoryRow) {
                    if (!is_array($hbtiCategoryRow)) {
                        continue;
                    }
                    $hbtiCode = strtoupper(trim((string)($hbtiCategoryRow['hbti'] ?? '')));
                    $cateCd = trim((string)($hbtiCategoryRow['cateCd'] ?? ''));
                    if ($hbtiCode === '' || $cateCd === '') {
                        continue;
                    }
                    $onaholeHbtiCategoryMap[$hbtiCode] = [
                        'hbti' => $hbtiCode,
                        'cateNm' => trim((string)($hbtiCategoryRow['cateNm'] ?? '')),
                        'cateCd' => $cateCd,
                    ];
                }
                $brand1Codes = array_keys($brand1CategoryList);
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
                if (!empty($matchedBrand2Codes)) {
                    foreach ($matchedBrand2Codes as $brand2Code) {
                        $brand2Prefix = substr($brand2Code, 0, 3);
                        if (isset($brand1CodeMap[$brand2Prefix])) {
                            $hasBrand2Category = true;
                            break;
                        }
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
                foreach ($godoCategoryLines as $categoryRow) {
                    if (!is_array($categoryRow)) {
                        continue;
                    }
                    $cateCd = trim((string)($categoryRow['cateCd'] ?? ''));
                    if (strlen($cateCd) === 9 && strpos($cateCd, '026001') === 0) {
                        $currentOnaholeWeightCategories[] = [
                            'cateCd' => $cateCd,
                            'line' => trim((string)($categoryRow['line'] ?? '')),
                        ];
                    }
                    if (strlen($cateCd) === 9 && strpos($cateCd, '026003') === 0) {
                        $currentOnaholePriceCategories[] = [
                            'cateCd' => $cateCd,
                            'line' => trim((string)($categoryRow['line'] ?? '')),
                        ];
                    }
                    if (strlen($cateCd) === 9 && strpos($cateCd, '026005') === 0) {
                        $currentOnaholeTypeCategories[] = [
                            'cateCd' => $cateCd,
                            'line' => trim((string)($categoryRow['line'] ?? '')),
                        ];
                    }
                    if (strlen($cateCd) === 9 && strpos($cateCd, '026010') === 0) {
                        $currentOnaholeHbtiCategories[] = [
                            'cateCd' => $cateCd,
                            'line' => trim((string)($categoryRow['line'] ?? '')),
                        ];
                    }
                    if (strlen($cateCd) === 9 && strpos($cateCd, '026006') === 0) {
                        $currentOnaholeInnerLengthCategories[] = [
                            'cateCd' => $cateCd,
                            'line' => trim((string)($categoryRow['line'] ?? '')),
                        ];
                    }
                }
                $targetOnaholeWeightCategory = null;
                if ($goodsWeightNormalized !== '' && is_numeric($goodsWeightNormalized) && (float)$goodsWeightNormalized > 0) {
                    $goodsWeightValue = (float)$goodsWeightNormalized;
                    foreach ($onaholeWeightCategories as $weightCategoryRow) {
                        if (!is_array($weightCategoryRow)) {
                            continue;
                        }
                        $min = isset($weightCategoryRow['min']) ? (float)$weightCategoryRow['min'] : 0.0;
                        $max = isset($weightCategoryRow['max']) && $weightCategoryRow['max'] !== null ? (float)$weightCategoryRow['max'] : null;
                        if ($goodsWeightValue < $min) {
                            continue;
                        }
                        if ($max !== null && $goodsWeightValue > $max) {
                            continue;
                        }
                        $targetCateCd = trim((string)($weightCategoryRow['cateCd'] ?? ''));
                        if ($targetCateCd === '') {
                            continue;
                        }
                        $targetOnaholeWeightCategory = [
                            'cateNm' => trim((string)($weightCategoryRow['cateNm'] ?? '')),
                            'cateCd' => $targetCateCd,
                        ];
                        break;
                    }
                }
                $targetOnaholePriceCategory = null;
                $targetOnaholeInnerLengthCategory = null;
                $targetMarginGradeCategory = $marginGradeCategoryMap[$marginGrade] ?? null;
                $currentMarginGradeCategories = [];
                foreach ($godoCategoryLines as $categoryRow) {
                    if (!is_array($categoryRow)) {
                        continue;
                    }
                    $cateCd = trim((string)($categoryRow['cateCd'] ?? ''));
                    if (strlen($cateCd) === 9 && strpos($cateCd, '046001') === 0) {
                        $currentMarginGradeCategories[] = [
                            'cateCd' => $cateCd,
                            'line' => trim((string)($categoryRow['line'] ?? '')),
                        ];
                    }
                }

                // 검수 문제점 리스트: 매칭 실패 케이스부터 순차적으로 확장한다.
                $inspectionIssues = [];
                // 매칭이 안된 경우 체크리스트
                if (!$isMatchedByGoodsNo && $psIdx <= 0) {
                    $inspectionIssues[] = [
                        'required' => '필수',
                        'issue' => '재고코드 미생성',
                        'solution' => '<span>재고코드를 입력해주세요.</span>',
                    ];
                }

                if (!$isMatchedByGoodsNo && $psIdx > 0) {
                    $inspectionIssues[] = [
                        'required' => '필수',
                        'issue' => '재고코드는 있으나 매칭된 고도몰 상품번호가 없음',
                        'solution' => '<span>고도몰에 재고코드가 등록된 상품이 없습니다.</span>',
                    ];
                }

                if ($isMatchedByGoodsNo && $hasCdGodoCode && $goodsNo !== $cdGodoCode) {
                    $inspectionIssues[] = [
                        'required' => '필수',
                        'issue' => '상품번호 불일치',
                        'solution' => "<span>고도몰상품에 매칭된 상품 번호와 인트라넷 등록된 번호가 일치하지 않습니다.</span>\n"
                            . '매칭된 고도몰 상품번호 : <b>' . $goodsNo . "</b>\n"
                            . '인트라넷 등록된 상품번호 : <b>' . $cdGodoCode . "</b>",
                    ];
                }

                if ($isMatchedByGoodsNo && $onlyAdultFl !== 'y') {
                    $inspectionIssues[] = [
                        'required' => '참고',
                        'issue' => '성인인증',
                        'solution' => '<span>성인인증 사용이 체크되어 있지 않습니다.</span>',
                    ];
                }

                if ($isMatchedByGoodsNo && strtoupper((string)($item['cd_kind_code'] ?? '')) === 'ONAHOLE') {
                    if ($goodsWeightNormalized === '' || !is_numeric($goodsWeightNormalized) || (float)$goodsWeightNormalized <= 0) {
                        $inspectionIssues[] = [
                            'required' => '필수',
                            'issue' => '상품중량 미입력',
                            'solution' => '<span>인트라넷에 상줌중량 정보가 없습니다.</span>',
                        ];
                    }
                    if ($innerLengthNormalized === '' || !is_numeric($innerLengthNormalized) || (float)$innerLengthNormalized <= 0) {
                        $inspectionIssues[] = [
                            'required' => '필수',
                            'issue' => '내부길이 미입력',
                            'solution' => '<span>인트라넷에 내부길이 정보가 없습니다.</span>',
                        ];
                    } else {
                        $innerLengthValue = (float)$innerLengthNormalized;
                        foreach ($onaholeInnerLengthCategories as $lengthCategoryRow) {
                            if (!is_array($lengthCategoryRow)) {
                                continue;
                            }
                            $min = isset($lengthCategoryRow['min']) ? (float)$lengthCategoryRow['min'] : 0.0;
                            $max = isset($lengthCategoryRow['max']) && $lengthCategoryRow['max'] !== null ? (float)$lengthCategoryRow['max'] : null;
                            if ($innerLengthValue < $min) {
                                continue;
                            }
                            if ($max !== null && $innerLengthValue > $max) {
                                continue;
                            }
                            $targetCateCd = trim((string)($lengthCategoryRow['cateCd'] ?? ''));
                            if ($targetCateCd === '') {
                                continue;
                            }
                            $targetOnaholeInnerLengthCategory = [
                                'cateNm' => trim((string)($lengthCategoryRow['cateNm'] ?? '')),
                                'cateCd' => $targetCateCd,
                            ];
                            break;
                        }
                    }
                    if (empty($currentOnaholeTypeCategories)) {
                        $inspectionIssues[] = [
                            'required' => '참고',
                            'issue' => '유형별 카테고리 미지정',
                            'solution' => '<span>오나홀 유형별 카테고리(026005???)가 지정되어 있지 않습니다.</span>',
                        ];
                    }
                    if (empty($currentOnaholeHbtiCategories)) {
                        $inspectionIssues[] = [
                            'required' => '참고',
                            'issue' => 'HBTI 카테고리 미지정',
                            'solution' => '<span>오나홀 HBTI 카테고리(026010???)가 지정되어 있지 않습니다.</span>',
                        ];
                    }
                }

                if ($isMatchedByGoodsNo && strtoupper((string)($item['cd_kind_code'] ?? '')) === 'ONAHOLE' && $targetOnaholeInnerLengthCategory !== null) {
                    $targetInnerLengthCateCd = (string)($targetOnaholeInnerLengthCategory['cateCd'] ?? '');
                    $targetInnerLengthCateNm = (string)($targetOnaholeInnerLengthCategory['cateNm'] ?? '');
                    if (empty($currentOnaholeInnerLengthCategories)) {
                        $queueAddCategory($categoryAddQueue, $targetInnerLengthCateCd, $targetInnerLengthCateNm);
                        $inspectionIssues[] = [
                            'required' => '필수',
                            'issue' => '내부길이 카테고리 미지정',
                            'solution' => "<span>오나홀 > 내부길이 카테고리가 미지정되어 있습니다.</span>\n"
                                . '추가 카테고리 : <b>' . $targetInnerLengthCateNm . '</b> ( ' . $targetInnerLengthCateCd . ' )',
                        ];
                    } else {
                        $hasExpectedInnerLengthCategory = false;
                        $wrongInnerLengthCategoryLines = [];
                        foreach ($currentOnaholeInnerLengthCategories as $innerLengthCategoryRow) {
                            $currentCateCd = (string)($innerLengthCategoryRow['cateCd'] ?? '');
                            $currentLine = (string)($innerLengthCategoryRow['line'] ?? '');
                            if ($currentCateCd === $targetInnerLengthCateCd) {
                                $hasExpectedInnerLengthCategory = true;
                                continue;
                            }
                            $currentCateNm = (string)($onaholeInnerLengthCategoryMap[$currentCateCd]['cateNm'] ?? '');
                            $lineLabel = $currentLine !== '' ? $currentLine : $currentCateNm;
                            $wrongInnerLengthCategoryLines[] = $lineLabel . ' ( ' . $currentCateCd . ' ) - 삭제';
                            $queueAddCategory($categoryDeleteQueue, $currentCateCd, $currentCateNm);
                        }
                        if (!empty($wrongInnerLengthCategoryLines) || !$hasExpectedInnerLengthCategory) {
                            if (!$hasExpectedInnerLengthCategory) {
                                $queueAddCategory($categoryAddQueue, $targetInnerLengthCateCd, $targetInnerLengthCateNm);
                            }
                            $wrongLineText = !empty($wrongInnerLengthCategoryLines) ? implode("\n", $wrongInnerLengthCategoryLines) : '-';
                            $inspectionIssues[] = [
                                'required' => '필수',
                                'issue' => '내부길이 카테고리 오류',
                                'solution' => "<span>오나홀 > 내부길이 카테고리 오분류</span>\n"
                                    . '현재 설정된 내부길이 : <b>' . $innerLengthRaw . "</b>\n"
                                    . '오분류 카테고리 : ' . $wrongLineText . "\n"
                                    . '알맞은 카테고리 : <b>' . $targetInnerLengthCateNm . '</b> ( ' . $targetInnerLengthCateCd . ' ) - 추가',
                            ];
                        }
                    }
                }

                if ($isMatchedByGoodsNo && strtoupper((string)($item['cd_kind_code'] ?? '')) === 'ONAHOLE' && $cdHbti !== '') {
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
                                'solution' => "<span>HBTI 값과 카테고리 매핑이 일치하지 않습니다.</span>\n"
                                    . 'cd_hbti : <b>' . $cdHbti . "</b>\n"
                                    . '오분류 카테고리 : ' . $wrongLineText . "\n"
                                    . '알맞은 카테고리 : <b>' . $targetHbtiCateNm . '</b> ( ' . $targetHbtiCateCd . ' ) - 추가',
                            ];
                        }
                    }
                }

                if ($isMatchedByGoodsNo && strtoupper((string)($item['cd_kind_code'] ?? '')) === 'ONAHOLE' && $targetOnaholeWeightCategory !== null) {
                    $targetCateCd = (string)($targetOnaholeWeightCategory['cateCd'] ?? '');
                    $targetCateNm = (string)($targetOnaholeWeightCategory['cateNm'] ?? '');
                    if (empty($currentOnaholeWeightCategories)) {
                        $categoryAddQueue[] = [
                            'cateCd' => $targetCateCd,
                            'cateNm' => $targetCateNm,
                        ];
                        $inspectionIssues[] = [
                            'required' => '필수',
                            'issue' => '카테고리 미지정',
                            'solution' => "<span>오나홀 > 중량별 카테고리가 미지정되어 있습니다.</span>\n"
                                . '추가 카테고리 : <b>' . $targetCateNm . '</b> ( ' . $targetCateCd . ' )',
                        ];
                    } else {
                        $hasExpectedOnaholeWeightCategory = false;
                        $wrongOnaholeWeightCategoryLines = [];
                        foreach ($currentOnaholeWeightCategories as $weightCategoryRow) {
                            $currentCateCd = (string)($weightCategoryRow['cateCd'] ?? '');
                            $currentLine = (string)($weightCategoryRow['line'] ?? '');
                            if ($currentCateCd === $targetCateCd) {
                                $hasExpectedOnaholeWeightCategory = true;
                                continue;
                            }
                            $currentCateNm = (string)($onaholeWeightCategoryMap[$currentCateCd]['cateNm'] ?? '');
                            $lineLabel = $currentLine !== '' ? $currentLine : $currentCateNm;
                            $wrongOnaholeWeightCategoryLines[] = $lineLabel . ' ( ' . $currentCateCd . ' ) - 삭제';
                            $categoryDeleteQueue[] = [
                                'cateCd' => $currentCateCd,
                                'cateNm' => $currentCateNm,
                            ];
                        }

                        if (!empty($wrongOnaholeWeightCategoryLines) || !$hasExpectedOnaholeWeightCategory) {
                            if (!$hasExpectedOnaholeWeightCategory) {
                                $categoryAddQueue[] = [
                                    'cateCd' => $targetCateCd,
                                    'cateNm' => $targetCateNm,
                                ];
                            }
                            $wrongLineText = !empty($wrongOnaholeWeightCategoryLines) ? implode("\n", $wrongOnaholeWeightCategoryLines) : '-';
                            $inspectionIssues[] = [
                                'required' => '필수',
                                'issue' => '카테고리 오류',
                                'solution' => "<span>오나홀 > 중량별 카테고리 오분류</span>\n"
                                    . '현재 설정된 중량 : <b>' . $goodsWeightRaw . "</b>\n"
                                    . '오분류 카테고리 : ' . $wrongLineText . "\n"
                                    . '알맞은 카테고리 : ' . $targetCateNm . ' ( ' . $targetCateCd . ' ) - 추가',
                            ];
                        }
                    }
                }

                if ($isMatchedByGoodsNo) {
                    if ($targetMarginGradeCategory === null) {
                        $inspectionIssues[] = [
                            'required' => '필수',
                            'issue' => '마진그룹 미산출',
                            'solution' => '<span>판매가/원가 기준 마진그룹이 산출되지 않습니다.</span>',
                        ];
                    } else {
                        $targetMarginCateCd = (string)($targetMarginGradeCategory['cateCd'] ?? '');
                        $targetMarginCateNm = (string)($targetMarginGradeCategory['cateNm'] ?? '');
                        if (empty($currentMarginGradeCategories)) {
                            $queueAddCategory($categoryAddQueue, $targetMarginCateCd, $targetMarginCateNm);
                            $inspectionIssues[] = [
                                'required' => '필수',
                                'issue' => '마진그룹 카테고리 미지정',
                                'solution' => "<span>마진그룹 카테고리가 지정되어 있지 않습니다.</span>\n"
                                    . '추가 카테고리 : <b>' . $targetMarginCateNm . '</b> ( ' . $targetMarginCateCd . ' )',
                            ];
                        } else {
                            $hasExpectedMarginCategory = false;
                            $wrongMarginCategoryLines = [];
                            foreach ($currentMarginGradeCategories as $marginCategoryRow) {
                                $currentCateCd = (string)($marginCategoryRow['cateCd'] ?? '');
                                $currentLine = (string)($marginCategoryRow['line'] ?? '');
                                if ($currentCateCd === $targetMarginCateCd) {
                                    $hasExpectedMarginCategory = true;
                                    continue;
                                }
                                $lineLabel = $currentLine !== '' ? $currentLine : $currentCateCd;
                                $wrongMarginCategoryLines[] = $lineLabel . ' ( ' . $currentCateCd . ' ) - 삭제';
                                $queueAddCategory($categoryDeleteQueue, $currentCateCd, '');
                            }

                            if (!empty($wrongMarginCategoryLines) || !$hasExpectedMarginCategory) {
                                if (!$hasExpectedMarginCategory) {
                                    $queueAddCategory($categoryAddQueue, $targetMarginCateCd, $targetMarginCateNm);
                                }
                                $wrongLineText = !empty($wrongMarginCategoryLines) ? implode("\n", $wrongMarginCategoryLines) : '-';
                                $inspectionIssues[] = [
                                    'required' => '필수',
                                    'issue' => '마진그룹 카테고리 오류',
                                    'solution' => "<span>마진그룹 카테고리 오분류</span>\n"
                                        . '오분류 카테고리 : ' . $wrongLineText . "\n"
                                        . '알맞은 카테고리 : <b>' . $targetMarginCateNm . '</b> ( ' . $targetMarginCateCd . ' ) - 추가',
                                ];
                            }
                        }
                    }
                }

                /*
                 * 임시 비활성화:
                 * 브랜드 1차 카테고리 미지정 체크는 현재 운영 정책상 제외한다.
                 */
                // if ($isMatchedByGoodsNo && !$hasBrand1Category) {
                //     $inspectionIssues[] = [
                //         'required' => '필수',
                //         'issue' => '브랜드 1차 카테고리 미지정',
                //         'solution' => '<span>브랜드 1차 카테고리가 지정되어있지 않습니다.</span>',
                //     ];
                // }

                if ($isMatchedByGoodsNo && $hasBrand1Category && !$hasBrand2Category) {
                    $inspectionIssues[] = [
                        'required' => '필수',
                        'issue' => '브랜드 2차 카테고리 미지정',
                        'solution' => '<span>브랜드 2차 카테고리가 지정되어 있지 않습니다.</span>',
                    ];
                }

                if ($isMatchedByGoodsNo && $isBrandHierarchyMismatch) {
                    $brand1Name = (string)($brand1CategoryList[$mismatchBrand1Code] ?? '-');
                    $brand2Line = $mismatchBrand2Line !== '' ? $mismatchBrand2Line : '-';
                    $inspectionIssues[] = [
                        'required' => '참고',
                        'issue' => '브랜드 1차/2차 불일치',
                        'solution' => "<span>브랜드 1차와 2차카테고리가 알맞지 않습니다.</span>\n"
                            . '1차 카테고리 : ' . $brand1Name . ' ( ' . $mismatchBrand1Code . " )\n"
                            . '2차 카테고리 : ' . $brand2Line . ' ( ' . $mismatchBrand2Code . ' )',
                    ];
                }

                if ($isMatchedByGoodsNo && $godoGoodsModelNo === '') {
                    $inspectionIssues[] = [
                        'required' => '필수',
                        'issue' => '바코드 미입력',
                        'solution' => '<span>고도몰에 바코드가 입력되있지 않습니다.</span>',
                    ];
                }

                if ($isMatchedByGoodsNo && $godoGoodsModelNo !== '' && $normalizedIntranetBarcode !== $normalizedGodoGoodsModelNo) {
                    $inspectionIssues[] = [
                        'required' => '필수',
                        'issue' => '바코드 불일치',
                        'solution' => "<span>인트라넷 바코드와 고도몰 바코드가 일치 하지않습니다.</span>\n"
                            . '인트라넷 : <b>' . $intranetBarcode . "</b>\n"
                            . '고도몰 : <b>' . $godoGoodsModelNo . "</b>",
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
                    $inspectionIssues[] = [
                        'required' => '필수',
                        'issue' => '원가정보 없음',
                        'solution' => '<span>인트라넷에 책정원가가 없습니다.</span>',
                    ];
                }

                if (
                    $isMatchedByGoodsNo
                    && $hasIntranetCost
                    && ($godoCostPriceNormalized === '' || !is_numeric($godoCostPriceNormalized) || (float)$godoCostPriceNormalized <= 0)
                ) {
                    $intranetCostPriceDisplay = $formatPriceDisplay($intranetCostPriceRaw);
                    $inspectionIssues[] = [
                        'required' => '필수',
                        'issue' => '원가 미입력',
                        'solution' => "<span>고도몰에 원가정보가 미입력되어 있습니다.</span>\n"
                            . '인트라넷 책정원가 : <b>' . $intranetCostPriceDisplay . "</b>",
                    ];
                }

                if (
                    $isMatchedByGoodsNo
                    && $hasIntranetCost
                    && $normalizedGodoCostCompare !== null
                    && (float)$normalizedGodoCostCompare > 0
                ) {
                    $isCostMismatch = ($normalizedIntranetCostCompare === null || $normalizedIntranetCostCompare !== $normalizedGodoCostCompare);
                    if ($isCostMismatch) {
                        $intranetCostPriceDisplay = $formatPriceDisplay($intranetCostPriceRaw);
                        $godoCostPriceDisplay = $formatPriceDisplay($godoCostPriceRaw);
                        $inspectionIssues[] = [
                            'required' => '필수',
                            'issue' => '원가 불일치',
                            'solution' => "<span>인트라넷 책정원가와 고도몰 원가값이 틀립니다.</span>\n"
                                . '인트라넷 : <b>' . $intranetCostPriceDisplay . "</b>\n"
                                . '고도몰 : <b>' . $godoCostPriceDisplay . "</b>",
                        ];
                    }
                }

                $normalizedIntranetGoodsPriceCompare = $normalizePriceValue($intranetGoodsPriceNormalized);
                $normalizedGodoGoodsPriceCompare = $normalizePriceValue($godoGoodsPriceNormalized);
                if ($isMatchedByGoodsNo) {
                    $isGoodsPriceMismatch = ($normalizedIntranetGoodsPriceCompare !== $normalizedGodoGoodsPriceCompare);
                    if ($isGoodsPriceMismatch) {
                        $intranetGoodsPriceDisplay = $formatPriceDisplay($intranetGoodsPriceRaw);
                        $godoGoodsPriceDisplay = $formatPriceDisplay($godoGoodsPriceRaw);
                        $inspectionIssues[] = [
                            'required' => '필수',
                            'issue' => '판매가 불일치',
                            'solution' => "<span>판매가가 일치하지 않습니다.</span>\n"
                                . '인트라넷 : <b>' . $intranetGoodsPriceDisplay . "</b>\n"
                                . '고도몰 : <b>' . $godoGoodsPriceDisplay . "</b>",
                        ];
                    }
                }

                if (
                    $isMatchedByGoodsNo
                    && strtoupper((string)($item['cd_kind_code'] ?? '')) === 'ONAHOLE'
                    && $normalizedIntranetGoodsPriceCompare !== null
                    && $normalizedIntranetGoodsPriceCompare === $normalizedGodoGoodsPriceCompare
                ) {
                    $intranetGoodsPriceValue = (float)$normalizedIntranetGoodsPriceCompare;
                    foreach ($onaholePriceCategories as $priceCategoryRow) {
                        if (!is_array($priceCategoryRow)) {
                            continue;
                        }
                        $min = isset($priceCategoryRow['min']) ? (float)$priceCategoryRow['min'] : 0.0;
                        $max = isset($priceCategoryRow['max']) && $priceCategoryRow['max'] !== null ? (float)$priceCategoryRow['max'] : null;
                        if ($intranetGoodsPriceValue < $min) {
                            continue;
                        }
                        if ($max !== null && $intranetGoodsPriceValue > $max) {
                            continue;
                        }
                        $targetCateCd = trim((string)($priceCategoryRow['cateCd'] ?? ''));
                        if ($targetCateCd === '') {
                            continue;
                        }
                        $targetOnaholePriceCategory = [
                            'cateNm' => trim((string)($priceCategoryRow['cateNm'] ?? '')),
                            'cateCd' => $targetCateCd,
                        ];
                        break;
                    }

                    if ($targetOnaholePriceCategory !== null) {
                        $targetPriceCateCd = (string)($targetOnaholePriceCategory['cateCd'] ?? '');
                        $targetPriceCateNm = (string)($targetOnaholePriceCategory['cateNm'] ?? '');
                        $currentMatchedPriceDisplay = $formatPriceDisplay($intranetGoodsPriceRaw);
                        if (empty($currentOnaholePriceCategories)) {
                            $queueAddCategory($categoryAddQueue, $targetPriceCateCd, $targetPriceCateNm);
                            $inspectionIssues[] = [
                                'required' => '필수',
                                'issue' => '가격별 카테고리 미지정',
                                'solution' => "<span>오나홀 > 가격별 카테고리가 미지정되어 있습니다.</span>\n"
                                    . '현재 판매 가격 : <b>' . $currentMatchedPriceDisplay . "</b>\n"
                                    . '추가 카테고리 : <b>' . $targetPriceCateNm . '</b> ( ' . $targetPriceCateCd . ' )',
                            ];
                        } else {
                            $hasExpectedOnaholePriceCategory = false;
                            $wrongOnaholePriceCategoryLines = [];
                            foreach ($currentOnaholePriceCategories as $priceCategoryRow) {
                                $currentCateCd = (string)($priceCategoryRow['cateCd'] ?? '');
                                $currentLine = (string)($priceCategoryRow['line'] ?? '');
                                if ($currentCateCd === $targetPriceCateCd) {
                                    $hasExpectedOnaholePriceCategory = true;
                                    continue;
                                }
                                $currentCateNm = (string)($onaholePriceCategoryMap[$currentCateCd]['cateNm'] ?? '');
                                $lineLabel = $currentLine !== '' ? $currentLine : $currentCateNm;
                                $wrongOnaholePriceCategoryLines[] = $lineLabel . ' ( ' . $currentCateCd . ' ) - 삭제';
                                $queueAddCategory($categoryDeleteQueue, $currentCateCd, $currentCateNm);
                            }
                            if (!empty($wrongOnaholePriceCategoryLines) || !$hasExpectedOnaholePriceCategory) {
                                if (!$hasExpectedOnaholePriceCategory) {
                                    $queueAddCategory($categoryAddQueue, $targetPriceCateCd, $targetPriceCateNm);
                                }
                                $wrongLineText = !empty($wrongOnaholePriceCategoryLines) ? implode("\n", $wrongOnaholePriceCategoryLines) : '-';
                                $inspectionIssues[] = [
                                    'required' => '필수',
                                    'issue' => '가격별 카테고리 오류',
                                    'solution' => "<span>오나홀 > 가격별 카테고리 오분류</span>\n"
                                        . '현재 판매 가격 : <b>' . $currentMatchedPriceDisplay . "</b>\n"
                                        . '오분류 카테고리 : ' . $wrongLineText . "\n"
                                        . '알맞은 카테고리 : <b>' . $targetPriceCateNm . '</b> ( ' . $targetPriceCateCd . ' ) - 추가',
                                ];
                            }
                        }
                    }
                }
                $categoryAddCodeList = [];
                foreach ($categoryAddQueue as $categoryQueueRow) {
                    $queueCateCd = trim((string)($categoryQueueRow['cateCd'] ?? ''));
                    if ($queueCateCd !== '') {
                        $categoryAddCodeList[] = $queueCateCd;
                    }
                }
                $categoryAddCodeList = array_values(array_unique($categoryAddCodeList));

                $categoryDeleteCodeList = [];
                foreach ($categoryDeleteQueue as $categoryQueueRow) {
                    $queueCateCd = trim((string)($categoryQueueRow['cateCd'] ?? ''));
                    if ($queueCateCd !== '') {
                        $categoryDeleteCodeList[] = $queueCateCd;
                    }
                }
                $categoryDeleteCodeList = array_values(array_unique($categoryDeleteCodeList));
                $categoryAddCodesForSync = implode(',', $categoryAddCodeList);
                $categoryDeleteCodesForSync = implode(',', $categoryDeleteCodeList);
                }

                // 공용 검수 로직(단일/다건 공통)을 기준으로 최종 컨텍스트를 덮어쓴다.
                $inspectionContext = $godoInspectionService->buildInspectionContext($item);
                $inspectionIssues = (isset($inspectionContext['inspection_issues']) && is_array($inspectionContext['inspection_issues']))
                    ? $inspectionContext['inspection_issues']
                    : [];
                $isMatchedByGoodsNo = !empty($inspectionContext['is_matched_by_goods_no']);
                $intranetBarcode = (string)($inspectionContext['intranet_barcode'] ?? $intranetBarcode);
                $intranetCostPriceRaw = (string)($inspectionContext['intranet_cost_price_raw'] ?? $intranetCostPriceRaw);
                $intranetGoodsPriceRaw = (string)($inspectionContext['intranet_goods_price_raw'] ?? $intranetGoodsPriceRaw);
                $godoCategoryLines = (isset($inspectionContext['godo_category_lines']) && is_array($inspectionContext['godo_category_lines']))
                    ? $inspectionContext['godo_category_lines']
                    : $godoCategoryLines;
                $categoryAddCodesForSync = (string)($inspectionContext['category_add_codes_for_sync'] ?? $categoryAddCodesForSync);
                $categoryDeleteCodesForSync = (string)($inspectionContext['category_delete_codes_for_sync'] ?? $categoryDeleteCodesForSync);

                
                ?>
                <tr bgcolor="<?= $rowBg ?>">
                    <td>
                        <?= $pidx ?><br>
                        <?php if ($psIdx > 0) { ?>
                            <b><?= $psIdx ?></b>
                        <?php } else { ?>
                            <span style="color:#dc3545; font-weight:700;">재고코드 미생성</span>
                        <?php } ?>
                    </td>
                    <td style="width:70px;">
                        <?php if (!empty($item['img_path'])) { ?>
                            <img src="<?= htmlspecialchars((string)$item['img_path'], ENT_QUOTES, 'UTF-8') ?>" style="height:60px; border:1px solid #eee !important;">
                        <?php } ?>
                    </td>
                    <td class="text-left">
                        <?php
                        $isSaleMonth = $isOnFlag($item['is_sale_month'] ?? null);
                        $isSaleSpecial = $isOnFlag($item['is_sale_special'] ?? null);
                        $isDiscontinued = $isOnFlag($item['is_discontinued'] ?? null);
                        ?>
                        <?php if ($isSaleMonth || $isSaleSpecial || $isDiscontinued) { ?>
                            <div class="on_sale_label_wrap">
                                <?php if ($isSaleMonth) { ?>
                                    <label class="on_sale_label xs monthly">월간할인</label>
                                <?php } ?>
                                <?php if ($isSaleSpecial) { ?>
                                    <label class="on_sale_label xs special">특가할인</label>
                                <?php } ?>
                                <?php if ($isDiscontinued) { ?>
                                    <label class="on_sale_label xs discontinued">단종</label>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div><?= htmlspecialchars((string)($item['brand_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                        <div>
                            <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?= $pidx ?>','info');">보기</button>
                            <?= htmlspecialchars((string)($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars((string)($item['barcode'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="width:70px;"><?= number_format($currentStockQty) ?></td>
                    <td style="width:90px;"><?= number_format((int)($item['godo_stock_qty'] ?? 0)) ?></td>
                    <?php $restockCount = (int)($item['restock_request_count'] ?? 0); ?>
                    <td style="width:90px; color:<?= $restockCount > 0 ? '#0d6efd' : '#9ca3af' ?>; font-weight:<?= $restockCount > 0 ? '700' : '400' ?>;">
                        <?= number_format($restockCount) ?>
                    </td>
                    <td>
                        <?php if ($isMatchedByGoodsNo) { ?>
                            <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                <button type="button" class="btnstyle1 btnstyle1-sm" onclick="goGodoMall('<?= htmlspecialchars($goodsNo, ENT_QUOTES, 'UTF-8') ?>');">쑈당몰 바로가기</button>
                                <button type="button" class="btnstyle1 btnstyle1-sm" onclick="goGodoMallAdmin('<?= htmlspecialchars($goodsNo, ENT_QUOTES, 'UTF-8') ?>');">관리자 바로가기</button>
                            </div>
                        <?php } else { ?>
                            <?php /*
                            <div>매칭된 고도몰 상품번호 : <b><?= htmlspecialchars($goodsNo !== '' ? $goodsNo : '-', ENT_QUOTES, 'UTF-8') ?></b></div>
                            <div>인트라넷 등록된 상품번호 : <b><?= htmlspecialchars($hasCdGodoCode ? $cdGodoCode : '-', ENT_QUOTES, 'UTF-8') ?></b></div>
                            */ ?>
                            <b>매칭실패</b>
                        <?php } ?>

                        <?php if (!empty($inspectionIssues)) { ?>
                            <table class="inspection-checklist-table">
                                <thead>
                                <tr>
                                    <th class="inspection-checklist-th inspection-checklist-th-no">순번</th>
                                    <th class="inspection-checklist-th">필수여부 / 문제점</th>
                                    <th class="inspection-checklist-th">내용</th>
                                    <th class="inspection-checklist-th">처리</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($inspectionIssues as $issueIdx => $issueRow) { ?>
                                    <?php $requiredClass = (($issueRow['required'] ?? '') === '참고') ? 'inspection-checklist-required-ref' : 'inspection-checklist-required-required'; ?>
                                    <?php
                                    $issueName = trim((string)($issueRow['issue'] ?? ''));
                                    $actionMeta = $godoInspectionService->resolveIssueActionMeta($issueName, $intranetBarcode);
                                    $actionTarget = (string)($actionMeta['target'] ?? '-');
                                    $actionState = (string)($actionMeta['state'] ?? '확인필요');
                                    $actionReason = (string)($actionMeta['reason'] ?? '처리 방식 확인 필요');
                                    $solutionEscaped = htmlspecialchars((string)($issueRow['solution'] ?? ''), ENT_QUOTES, 'UTF-8');
                                    $solutionEscaped = str_replace(
                                        ['&lt;b&gt;', '&lt;/b&gt;', '&lt;span&gt;', '&lt;/span&gt;'],
                                        ['<b>', '</b>', '<span>', '</span>'],
                                        $solutionEscaped
                                    );
                                    ?>
                                    <tr>
                                        <td class="inspection-checklist-td inspection-checklist-td-center"><?= (int)$issueIdx + 1 ?></td>
                                        <td class="inspection-checklist-td">
                                            <span class="inspection-checklist-required <?= $requiredClass ?>"><?= htmlspecialchars((string)($issueRow['required'] ?? '필수'), ENT_QUOTES, 'UTF-8') ?></span><br>
                                            <span class="inspection-checklist-issue"><?= htmlspecialchars((string)($issueRow['issue'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                        </td>
                                        <td class="inspection-checklist-td text-left"><?= nl2br($solutionEscaped, false) ?></td>
                                        <td class="inspection-checklist-td">
                                            <?php if ($actionState === '자동처리 가능') { ?>
                                                <label style="display:block; margin-bottom:4px;">
                                                    <input
                                                        type="checkbox"
                                                        name="auto_process_flags[<?= $pidx ?>][<?= $issueIdx ?>]"
                                                        value="1"
                                                        data-issue="<?= htmlspecialchars($issueName, ENT_QUOTES, 'UTF-8') ?>"
                                                        data-target="<?= htmlspecialchars($actionTarget, ENT_QUOTES, 'UTF-8') ?>"
                                                        data-state="<?= htmlspecialchars($actionState, ENT_QUOTES, 'UTF-8') ?>"
                                                        checked
                                                    >
                                                    재고등록시 자동처리
                                                </label>
                                            <?php } else { ?>
                                                <div style="margin-bottom:4px; color:#9ca3af;">재고등록시 자동처리 대상 아님</div>
                                            <?php } ?>
                                            <input type="hidden" name="auto_process_meta[<?= $pidx ?>][<?= $issueIdx ?>][pidx]" value="<?= htmlspecialchars((string)$pidx, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="auto_process_meta[<?= $pidx ?>][<?= $issueIdx ?>][ps_idx]" value="<?= htmlspecialchars((string)$psIdx, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="auto_process_meta[<?= $pidx ?>][<?= $issueIdx ?>][goods_no]" value="<?= htmlspecialchars((string)$goodsNo, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="auto_process_meta[<?= $pidx ?>][<?= $issueIdx ?>][issue]" value="<?= htmlspecialchars($issueName, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="auto_process_meta[<?= $pidx ?>][<?= $issueIdx ?>][target]" value="<?= htmlspecialchars($actionTarget, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="auto_process_meta[<?= $pidx ?>][<?= $issueIdx ?>][state]" value="<?= htmlspecialchars($actionState, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="auto_process_meta[<?= $pidx ?>][<?= $issueIdx ?>][reason]" value="<?= htmlspecialchars($actionReason, ENT_QUOTES, 'UTF-8') ?>">
                                            <div><b>대상:</b> <?= htmlspecialchars($actionTarget, ENT_QUOTES, 'UTF-8') ?></div>
                                            <div><b>자동:</b> <?= htmlspecialchars($actionState, ENT_QUOTES, 'UTF-8') ?></div>
                                            <div style="color:#6b7280;"><?= htmlspecialchars($actionReason, ENT_QUOTES, 'UTF-8') ?></div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } elseif ($isMatchedByGoodsNo) { ?>
                            <div style="margin-top:6px; color:#198754; font-size:11px;">검수 이슈 없음</div>
                        <?php } else { ?>
                            <div style="margin-top:6px; color:#6b7280; font-size:11px;">매칭 실패(추가 체크리스트 준비중)</div>
                        <?php } ?>

                        <button type="button" class="btnstyle1 btnstyle1-sm">체크항목 일괄 검수 처리</button>

                    </td>
                    <td>
                        <?php if (!empty($godoCategoryLines)) { ?>
                            <?php
                            $categoryToggleId = 'godo-category-' . (int)$rowIndex . '-' . $pidx . '-' . $psIdx;
                            $categoryCount = count($godoCategoryLines);
                            ?>
                            <button
                                type="button"
                                class="btnstyle1 btnstyle1-xs"
                                onclick="(function(btn){var box=document.getElementById('<?= htmlspecialchars($categoryToggleId, ENT_QUOTES, 'UTF-8') ?>');if(!box){return;}var isHidden=(box.style.display==='none'||box.style.display==='');box.style.display=isHidden?'block':'none';btn.textContent=isHidden?'카테고리 닫기 (<?= $categoryCount ?>)':'카테고리 보기 (<?= $categoryCount ?>)';})(this);"
                            >카테고리 보기 (<?= $categoryCount ?>)</button>
                            <div id="<?= htmlspecialchars($categoryToggleId, ENT_QUOTES, 'UTF-8') ?>" style="display:none;">
                                <table style="width:100%; margin-top:6px; border-collapse:collapse; font-size:11px; color:#374151;">
                                    <thead>
                                    <tr>
                                        <th style="text-align:left; padding:4px 6px; border:1px solid #e5e7eb; background:#f9fafb;">카테고리명</th>
                                        <th style="text-align:right; padding:4px 6px; border:1px solid #e5e7eb; background:#f9fafb; width:90px;">코드</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($godoCategoryLines as $categoryRow) { ?>
                                        <?php
                                        $categoryLine = is_array($categoryRow) ? (string)($categoryRow['line'] ?? '') : (string)$categoryRow;
                                        $categoryCode = is_array($categoryRow) ? (string)($categoryRow['cateCd'] ?? '') : '';
                                        ?>
                                        <tr>
                                            <td style="padding:4px 6px; border:1px solid #e5e7eb; text-align:left;"><?= htmlspecialchars($categoryLine, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td style="padding:4px 6px; border:1px solid #e5e7eb; text-align:right;"><?= htmlspecialchars($categoryCode !== '' ? $categoryCode : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <span style="color:#9ca3af;">-</span>
                        <?php } ?>
                    </td>
                    <td style="width:70px;"><?= number_format($qty) ?></td>
                    <td style="width:70px;">
                        <?php if (!empty($item['is_false'])) { ?>
                            주문실패
                        <?php } else { ?>
                            <input type="hidden" name="ps_idx[]" value="<?= $psIdx ?>">
                            <input
                                type="text"
                                name="s_qty[]"
                                class="stock-apply-qty-input"
                                data-current-stock="<?= $currentStockQty ?>"
                                style="width:100%; font-size:14px; font-weight:bold;"
                                value="<?= $qty ?>"
                            >
                        <?php } ?>
                    </td>
                    <td style="width:90px;" class="text-center">

                        <div>
                            <?php if (!empty($item['is_false'])) { ?>
                                -
                            <?php } else { ?>
                                <span class="final-apply-qty-text"><?= number_format($currentStockQty + $qty) ?></span>
                            <?php } ?>
                        </div>
                        <div class="m-t-4">
                            <button
                                type="button"
                                class="btnstyle1 btnstyle1-inverse btnstyle1-xs btn-godo-process"
                                data-pidx="<?= (int)$pidx ?>"
                                data-goods-no="<?= htmlspecialchars($goodsNo !== '' ? $goodsNo : $cdGodoCode, ENT_QUOTES, 'UTF-8') ?>"
                                data-intranet-barcode="<?= htmlspecialchars((string)$intranetBarcode, ENT_QUOTES, 'UTF-8') ?>"
                                data-intranet-cost-price="<?= htmlspecialchars((string)$intranetCostPriceRaw, ENT_QUOTES, 'UTF-8') ?>"
                                data-intranet-goods-price="<?= htmlspecialchars((string)$intranetGoodsPriceRaw, ENT_QUOTES, 'UTF-8') ?>"
                                data-godo-goods-price="<?= htmlspecialchars((string)($item['godo_goods_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                data-category-add-cds="<?= htmlspecialchars((string)$categoryAddCodesForSync, ENT_QUOTES, 'UTF-8') ?>"
                                data-category-delete-cds="<?= htmlspecialchars((string)$categoryDeleteCodesForSync, ENT_QUOTES, 'UTF-8') ?>"
                                onclick="orderSheetStockPopup.godoProcess(this)"
                            >
                                고도몰 재고+검수 처리
                            </button>
                        </div>
                        
                    </td>
                    <td style="width:150px;">
                        <?php if (empty($item['is_false'])) { ?>
                            <input type="text" name="s_memo[]" style="width:100%;" value="">
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </form>
</div>

<script>
var orderSheetStockPopup = function () {
    return {
        godoProcess: function (btn) {
            var $btn = $(btn);
            var goodsNo = String($btn.data('goodsNo') || '').trim();
            var pidx = Number($btn.data('pidx') || 0);
            var $row = $btn.closest('tr');
            var finalApplyQtyText = String($row.find('.final-apply-qty-text').text() || '').trim();
            var stockQty = Number(String(finalApplyQtyText).replace(/[^0-9\-]/g, ''));
            if (isNaN(stockQty)) {
                stockQty = 0;
            }
            if (!goodsNo && (!pidx || isNaN(pidx))) {
                alert('고도몰 상품번호가 없어 처리할 수 없습니다.');
                return;
            }

            var normalizeSimpleValue = function (value) {
                return String(value || '').replace(/,/g, '').trim();
            };
            var checkedIssues = [];
            var checkedAutoIssueMap = {};
            $row.find('input[name^="auto_process_flags["]:checked').each(function () {
                var $checkbox = $(this);
                var target = String($checkbox.data('target') || '').trim();
                var state = String($checkbox.data('state') || '').trim();
                var issue = String($checkbox.data('issue') || '').trim();
                if (state === '자동처리 가능' && issue) {
                    checkedAutoIssueMap[issue] = true;
                }
                if (target !== '고도몰' || state !== '자동처리 가능') {
                    return;
                }
                checkedIssues.push(issue);
            });
            var checkedIssueMap = {};
            for (var ci = 0; ci < checkedIssues.length; ci++) {
                var issueName = checkedIssues[ci];
                if (issueName) {
                    checkedIssueMap[issueName] = true;
                }
            }

            var intranetBarcode = normalizeSimpleValue($btn.data('intranetBarcode'));
            var intranetCostPrice = normalizeSimpleValue($btn.data('intranetCostPrice'));
            var godoGoodsPrice = normalizeSimpleValue($btn.data('godoGoodsPrice'));
            var addCategoryCds = String($btn.data('categoryAddCds') || '').trim();
            var deleteCategoryCds = String($btn.data('categoryDeleteCds') || '').trim();

            var columnUpdates = {};
            if (checkedIssueMap['바코드 미입력'] || checkedIssueMap['바코드 불일치']) {
                if (intranetBarcode !== '') {
                    columnUpdates.godo_goods_model_no = intranetBarcode;
                }
            }
            if (checkedIssueMap['원가 미입력'] || checkedIssueMap['원가 불일치']) {
                if (intranetCostPrice !== '') {
                    columnUpdates.godo_cost_price = intranetCostPrice;
                }
            }
            if (checkedIssueMap['성인인증']) {
                columnUpdates.godo_only_adult_fl = 'y';
            }
            var intranetSalePrice = '';
            if (checkedAutoIssueMap['판매가 불일치'] && godoGoodsPrice !== '') {
                intranetSalePrice = godoGoodsPrice;
            }

            var hasCategoryIssueChecked = false;
            for (var issueKey in checkedIssueMap) {
                if (!Object.prototype.hasOwnProperty.call(checkedIssueMap, issueKey)) {
                    continue;
                }
                if (issueKey.indexOf('카테고리') >= 0) {
                    hasCategoryIssueChecked = true;
                    break;
                }
            }

            var columnUpdatePairs = [];
            for (var columnName in columnUpdates) {
                if (!Object.prototype.hasOwnProperty.call(columnUpdates, columnName)) {
                    continue;
                }
                columnUpdatePairs.push(columnName + '=' + String(columnUpdates[columnName]));
            }
            var columnUpdateString = columnUpdatePairs.join(',');
            var addCategoryString = hasCategoryIssueChecked ? addCategoryCds : '';
            var deleteCategoryString = hasCategoryIssueChecked ? deleteCategoryCds : '';
            var godoApiPreviewUrl = 'https://showdang.co.kr/dnfix/api/goods_api.php'
                + '?mode=autoRestockWithCheck'
                + '&goodsNo=' + encodeURIComponent(goodsNo)
                + '&stockQty=' + encodeURIComponent(String(stockQty));
            if (columnUpdateString !== '') {
                godoApiPreviewUrl += '&updateColumns=' + encodeURIComponent(columnUpdateString);
            }
            if (addCategoryString !== '') {
                godoApiPreviewUrl += '&addCategoryCds=' + encodeURIComponent(addCategoryString);
            }
            if (deleteCategoryString !== '') {
                godoApiPreviewUrl += '&deleteCategoryCds=' + encodeURIComponent(deleteCategoryString);
            }

            alert('고도몰 API URL 미리보기\n' + godoApiPreviewUrl);

            if (!confirm('해당 상품을 고도몰 처리하시겠습니까?')) {
                return;
            }

            $btn.prop('disabled', true);
            $.ajax({
                url: '/admin/order/sheet/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'orderSheetSingleGodoInspection',
                    goods_no: goodsNo,
                    pidx: pidx,
                    stock_qty: stockQty,
                    intranet_sale_price: intranetSalePrice,
                    column_updates: columnUpdateString,
                    add_category_cds: addCategoryString,
                    delete_category_cds: deleteCategoryString
                },
                success: function (res) {
                    $btn.prop('disabled', false);
                    if (res && res.success === true) {
                        var doneGoodsNo = (res.goods_no || goodsNo || '').toString();
                        alert('고도몰 처리 완료' + (doneGoodsNo ? '\n상품번호: ' + doneGoodsNo : ''));
                        return;
                    }
                    var msg = (res && (res.message || res.msg)) ? (res.message || res.msg) : '고도몰 처리에 실패했습니다.';
                    alert(msg);
                },
                error: function (xhr) {
                    $btn.prop('disabled', false);
                    var msg = (xhr && xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.msg))
                        ? (xhr.responseJSON.message || xhr.responseJSON.msg)
                        : '에러';
                    alert(msg);
                }
            });
        },
        allStock: function () {
            var formData = $('#form_os_stock').serializeArray();
            $.ajax({
                url: '/admin/order/sheet/action',
                data: formData,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    if (res && res.success === true) {
                        alert('재고가 일괄 등록되었습니다.');
                        location.reload();
                        return;
                    }
                    var msg = (res && (res.message || res.msg)) ? (res.message || res.msg) : '처리에 실패했습니다.';
                    if (typeof showAlert === 'function') {
                        showAlert('Error', msg, 'alert2');
                    } else {
                        alert(msg);
                    }
                },
                error: function (xhr) {
                    var msg = (xhr && xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.msg))
                        ? (xhr.responseJSON.message || xhr.responseJSON.msg)
                        : '에러';
                    if (typeof showAlert === 'function') {
                        showAlert('Error', msg, 'alert2');
                    } else {
                        alert(msg);
                    }
                }
            });
        }
    };
}();

$(function () {
    if ($('.calendar-input input').length) {
        $('.calendar-input input').datepicker(clareCalendar);
    }

    var recalcFinalApplyQty = function ($input) {
        var currentStock = Number($input.data('current-stock') || 0);
        if (isNaN(currentStock)) {
            currentStock = 0;
        }
        var applyQty = Number(String($input.val() || '').replace(/[^0-9\-]/g, ''));
        if (isNaN(applyQty)) {
            applyQty = 0;
        }
        var finalQty = currentStock + applyQty;
        $input.closest('tr').find('.final-apply-qty-text').text(Number(finalQty).toLocaleString('ko-KR'));
    };

    $(document).on('input change keyup', '.stock-apply-qty-input', function () {
        recalcFinalApplyQty($(this));
    });
});
</script>
