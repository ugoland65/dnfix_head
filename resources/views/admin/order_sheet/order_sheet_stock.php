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

?>
<style>
.stock-update-form {
    padding: 20px;
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
            <thead>
                <tr>
                    <th>상품번호<br>재고코드</th>
                    <th>이미지</th>
                    <th>상품명</th>
                    <th>바코드</th>
                    <th style="width:70px;">현재고</th>
                    <th style="width:70px;">고도몰<br/>현재고</th>
                    <th style="width:70px;">재입고알림<br/>요청수</th>
                    <th>매칭/검수</th>
                    <th>고도몰<br>카테고리</th>
                    <th>주문수량</th>
                    <th>실입고수량</th>
                    <th>최종적용수량</th>
                    <th>메모</th>
                </tr>
            </thead>

            <tbody>
            <?php $godoInspectionService = new \App\Services\GodoInspectionService(); ?>
            <?php foreach ($stockItems as $rowIndex => $item) { ?>
                <?php
                $stockProcessed = (isset($item['stock_processed']) && is_array($item['stock_processed'])) ? $item['stock_processed'] : [];
                $rowBg = !empty($stockProcessed) ? '#fff9db' : (!empty($item['is_false']) ? '#eee' : '#fff');
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
                // 공용 검수 로직(단일/다건 공통)을 기준으로 최종 컨텍스트를 덮어쓴다.
                $inspectionContext = $godoInspectionService->buildInspectionContext(
                    $item,
                    \App\Services\GodoInspectionService::CONTEXT_ORDER_SHEET_STOCK
                );
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
                            <img src="<?= htmlspecialchars((string)$item['img_path'], ENT_QUOTES, 'UTF-8') ?>" onclick="onlyAD.prdView('<?= $pidx ?>','info');" style="height:60px; border:1px solid #eee !important; cursor:pointer;">
                        <?php } ?>
                    </td>
                    <td class="text-left">
                        <?php if (!empty($item['is_sale_month']) || !empty($item['is_sale_special']) || !empty($item['is_discontinued'])) { ?>
                            <div class="on_sale_label_wrap">
                                <?php if (!empty($item['is_sale_month'])) { ?>
                                    <label class="on_sale_label xs monthly">월간할인</label>
                                <?php } ?>
                                <?php if (!empty($item['is_sale_special'])) { ?>
                                    <label class="on_sale_label xs special">특가할인</label>
                                <?php } ?>
                                <?php if (!empty($item['is_discontinued'])) { ?>
                                    <label class="on_sale_label xs discontinued">단종</label>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div><?= htmlspecialchars((string)($item['brand_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                        <div>
                            <b onclick="onlyAD.prdView('<?= $pidx ?>','info');" style="cursor:pointer;"><?= htmlspecialchars((string)($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></b>
                        </div>
                    </td>
                    <td><?= htmlspecialchars((string)($item['barcode'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td ><?= number_format($currentStockQty) ?></td>
                    <td ><?= number_format((int)($item['godo_stock_qty'] ?? 0)) ?></td>
                    
                    <?php $restockCount = (int)($item['restock_request_count'] ?? 0); ?>
                    <td style="width:90px; color:<?= $restockCount > 0 ? '#0d6efd' : '#9ca3af' ?>; font-weight:<?= $restockCount > 0 ? '700' : '400' ?>;">
                        <div><?= number_format($restockCount) ?></div>
                        <?php if ($restockCount > 0 && $isMatchedByGoodsNo) { ?>
                            <div class="m-t-4">
                                <button
                                    type="button"
                                    class="btnstyle1 btnstyle1-sm"
                                    onclick="window.open('http://gdadmin.dnfix202439.godomall.com/goods/goods_restock.php?scmFl=all&key=goodsNo&keyword=<?= rawurlencode($goodsNo) ?>&pageNum=100', '_blank')"
                                >
                                    재입고 신청관리
                                </button>
                            </div>
                        <?php } ?>
                    </td>

                    
                    <td>
                        <?php if ($isMatchedByGoodsNo) { ?>
                            <div style="display:flex; gap:5px; align-items: center; flex-wrap:wrap;">
                                <div>매칭된 고도몰 상품번호 : <b><?= htmlspecialchars($goodsNo !== '' ? $goodsNo : '-', ENT_QUOTES, 'UTF-8') ?></b></div>
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
                                        $issueClass = (($issueRow['required'] ?? '') === '참고')
                                            ? 'inspection-checklist-issue-ref'
                                            : 'inspection-checklist-issue-required';
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
                                        <td class="inspection-checklist-td inspection-checklist-td-center" style="width:30px;"><?= (int)$issueIdx + 1 ?></td>
                                        <td class="inspection-checklist-td" style="width:120px;">
                                            <span class="inspection-checklist-required <?= $requiredClass ?>"><?= htmlspecialchars((string)($issueRow['required'] ?? '필수'), ENT_QUOTES, 'UTF-8') ?></span><br>
                                            <span class="<?= $issueClass ?>"><?= htmlspecialchars((string)($issueRow['issue'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                        </td>
                                        <td class="inspection-checklist-td text-left"><?= nl2br($solutionEscaped, false) ?></td>
                                        <td class="inspection-checklist-td text-left" style="width:150px;">

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
                                            <div><b>대상 :</b> <?= htmlspecialchars($actionTarget, ENT_QUOTES, 'UTF-8') ?></div>
                                            <div><b>자동 :</b> <?= htmlspecialchars($actionState, ENT_QUOTES, 'UTF-8') ?></div>
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

                        <?php if ($isMatchedByGoodsNo) { ?>
                            <div class="m-t-4">
                                <button
                                    type="button"
                                    class="btnstyle1 btnstyle1-info btnstyle1-sm"
                                    data-pidx="<?= (int)$pidx ?>"
                                    data-ps-idx="<?= (int)$psIdx ?>"
                                    data-brand-name="<?= htmlspecialchars((string)($item['brand_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-product-name="<?= htmlspecialchars((string)($item['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    onclick="orderSheetStockPopup.processChecklist(this)"
                                >
                                    체크항목 일괄 검수 처리
                                </button>
                            </div>
                        <?php } ?>

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
                                <span class="final-apply-qty-text"><?= number_format(!empty($stockProcessed) ? $currentStockQty : ($currentStockQty + $qty)) ?></span>
                            <?php } ?>
                        </div>
                        <?php if ($isMatchedByGoodsNo) { ?>
                            <div class="m-t-4">
                                <button
                                    type="button"
                                    class="btnstyle1 btnstyle1-inverse btnstyle1-xs btn-godo-process"
                                    data-pidx="<?= (int)$pidx ?>"
                                    data-ps-idx="<?= (int)$psIdx ?>"
                                    data-goods-no="<?= htmlspecialchars($goodsNo !== '' ? $goodsNo : $cdGodoCode, ENT_QUOTES, 'UTF-8') ?>"
                                    data-intranet-barcode="<?= htmlspecialchars((string)$intranetBarcode, ENT_QUOTES, 'UTF-8') ?>"
                                    data-intranet-cost-price="<?= htmlspecialchars((string)$intranetCostPriceRaw, ENT_QUOTES, 'UTF-8') ?>"
                                    data-intranet-goods-price="<?= htmlspecialchars((string)$intranetGoodsPriceRaw, ENT_QUOTES, 'UTF-8') ?>"
                                    data-godo-goods-price="<?= htmlspecialchars((string)($item['godo_goods_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-category-add-cds="<?= htmlspecialchars((string)$categoryAddCodesForSync, ENT_QUOTES, 'UTF-8') ?>"
                                    data-category-delete-cds="<?= htmlspecialchars((string)$categoryDeleteCodesForSync, ENT_QUOTES, 'UTF-8') ?>"
                                    onclick="orderSheetStockPopup.godoProcess(this)"
                                    <?= !empty($stockProcessed) ? 'disabled' : '' ?>
                                >
                                    <?= !empty($stockProcessed) ? '재고 반영완료' : '고도몰 재고+검수 처리' ?>
                                </button>
                                <?php if (!empty($stockProcessed)) { ?>
                                    <div class="m-t-4" style="font-size:11px; color:#15803d; line-height:1.5;">
                                        <div>재고 반영완료</div>
                                        <div><?= htmlspecialchars((string)($stockProcessed['processed_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?><?= !empty($stockProcessed['processed_by']) ? ' · ' . htmlspecialchars((string)$stockProcessed['processed_by'], ENT_QUOTES, 'UTF-8') : '' ?></div>
                                    </div>
                                <?php } ?>
                                <?php $godoProcessLog = (isset($item['godo_process_log']) && is_array($item['godo_process_log'])) ? $item['godo_process_log'] : []; ?>
                                <?php if (!empty($godoProcessLog)) { ?>
                                    <?php
                                        $processedBy = trim((string)($godoProcessLog['executor_name'] ?? ''));
                                        if ($processedBy === '') {
                                            $processedBy = trim((string)($godoProcessLog['executor_id'] ?? ''));
                                        }
                                    ?>
                                    <div class="m-t-4" style="font-size:11px; color:#15803d; line-height:1.5;">
                                        <div><?= htmlspecialchars((string)($godoProcessLog['status'] ?? '처리완료'), ENT_QUOTES, 'UTF-8') ?></div>
                                        <div><?= htmlspecialchars((string)($godoProcessLog['executed_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?><?= $processedBy !== '' ? ' · ' . htmlspecialchars($processedBy, ENT_QUOTES, 'UTF-8') : '' ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        
                    </td>
                    <td style="width:150px;">
                        <?php if (empty($item['is_false'])) { ?>
                            <input type="text" name="s_memo[]" style="width:100%;" value="">
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>
</div>

<script>
var orderSheetStockPopup = function () {
    return {
        processChecklist: function (btn) {
            var $btn = $(btn);
            var pidx = Number($btn.data('pidx') || 0);
            var psIdx = Number($btn.data('psIdx') || 0);
            var brandName = String($btn.data('brandName') || '').trim();
            var productName = String($btn.data('productName') || '').trim();
            var relationPk = Number($('#form_os_stock').find('input[name="os_idx"]').val() || 0);
            if (isNaN(relationPk) || relationPk < 1) {
                relationPk = 0;
            }
            var $row = $btn.closest('tr');
            var $checkboxes = $row.find('input[name^="auto_process_flags["]');
            if ($checkboxes.length < 1) {
                alert('실행할 것이 없습니다.');
                return;
            }

            var selectedIssues = [];
            $checkboxes.filter(':checked').each(function () {
                var issueName = String($(this).data('issue') || '').trim();
                if (issueName !== '') {
                    selectedIssues.push(issueName);
                }
            });

            if (selectedIssues.length < 1) {
                alert('처리할 체크 항목을 선택해주세요.');
                return;
            }

            var issueNameMap = {};
            for (var si = 0; si < selectedIssues.length; si++) {
                issueNameMap[selectedIssues[si]] = true;
            }
            var manualSoldOutIssueName = '현재 품절(수동) 상태';
            if (issueNameMap[manualSoldOutIssueName]) {
                var stockInputQtyRaw = String($row.find('.stock-apply-qty-input').val() || '').trim();
                var stockInputQty = Number(String(stockInputQtyRaw).replace(/[^0-9\-]/g, ''));
                if (isNaN(stockInputQty)) {
                    stockInputQty = 0;
                }
                if (stockInputQty <= 0) {
                    alert('재고수량이 미입력되어 품절(수동) 상태 해제 처리를 진행할 수 없습니다.');
                    return;
                }
            }

            var confirmRows = [];
            if (brandName !== '') {
                confirmRows.push(brandName);
            }
            if (productName !== '') {
                confirmRows.push(productName);
            }
            confirmRows.push('선택한 체크 항목을 일괄 검수 처리하시겠습니까?');
            if (!confirm(confirmRows.join('\n'))) {
                return;
            }

            $btn.prop('disabled', true);
            $.ajax({
                url: '/admin/product/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'process_single_godo_inspection',
                    prd_idx: pidx,
                    ps_idx: psIdx,
                    relation_pk: relationPk,
                    location_code: 'order_sheet_stock_single',
                    selected_issues: selectedIssues
                },
                success: function (res) {
                    $btn.prop('disabled', false);
                    if (res && res.success === true) {
                        alert((res.message || '처리가 완료되었습니다.'));
                        location.reload();
                        return;
                    }
                    alert((res && (res.message || res.msg)) ? (res.message || res.msg) : '처리에 실패했습니다.');
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
        godoProcess: function (btn) {
            var $btn = $(btn);
            var goodsNo = String($btn.data('goodsNo') || '').trim();
            var pidx = Number($btn.data('pidx') || 0);
            var psIdx = Number($btn.data('psIdx') || 0);
            var orderSheetIdx = Number($('#form_os_stock input[name="os_idx"]').val() || 0);
            var $row = $btn.closest('tr');
            var stockInputQtyRaw = String($row.find('.stock-apply-qty-input').val() || '').trim();
            var stockInputQty = Number(String(stockInputQtyRaw).replace(/[^0-9\-]/g, ''));
            if (isNaN(stockInputQty)) {
                stockInputQty = 0;
            }
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
            var checkedAutoIssues = [];
            for (var autoIssueName in checkedAutoIssueMap) {
                if (Object.prototype.hasOwnProperty.call(checkedAutoIssueMap, autoIssueName)) {
                    checkedAutoIssues.push(autoIssueName);
                }
            }
            var manualSoldOutIssueName = '현재 품절(수동) 상태';
            if (checkedIssueMap[manualSoldOutIssueName] && stockInputQty <= 0) {
                alert('재고수량이 미입력되어 품절(수동) 상태 해제 처리를 진행할 수 없습니다.');
                return;
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
            if (checkedIssueMap[manualSoldOutIssueName] && stockInputQty > 0) {
                columnUpdates.godo_sold_out_fl = 'n';
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

            //alert('고도몰 API URL 미리보기\n' + godoApiPreviewUrl);

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
                    os_idx: orderSheetIdx,
                    goods_no: goodsNo,
                    pidx: pidx,
                    ps_idx: psIdx,
                    stock_qty: stockQty,
                    stock_input_qty: stockInputQty > 0 ? String(stockInputQty) : '',
                    selected_auto_issues: checkedAutoIssues.join(','),
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
                        location.reload();
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
