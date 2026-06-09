<?php
$item = (isset($item) && is_array($item)) ? $item : [];
$inspectionContext = (isset($inspectionContext) && is_array($inspectionContext)) ? $inspectionContext : [];
$inspectionIssues = (isset($inspectionContext['inspection_issues']) && is_array($inspectionContext['inspection_issues'])) ? $inspectionContext['inspection_issues'] : [];
$godoCategoryLines = (isset($inspectionContext['godo_category_lines']) && is_array($inspectionContext['godo_category_lines']))
    ? $inspectionContext['godo_category_lines']
    : ((isset($item['godo_category_lines']) && is_array($item['godo_category_lines'])) ? $item['godo_category_lines'] : []);
$isMatchedByGoodsNo = !empty($inspectionContext['is_matched_by_goods_no']);
$intranetBarcode = (string)($inspectionContext['intranet_barcode'] ?? ($item['barcode'] ?? ''));
$goodsNo = trim((string)($item['godo_goods_no'] ?? ''));
$prdIdx = (int)($item['pidx'] ?? ($prd_idx ?? 0));
$psIdx = (int)($item['ps_idx'] ?? 0);
$inspectionHistoryRows = (isset($inspectionHistoryRows) && is_array($inspectionHistoryRows)) ? $inspectionHistoryRows : [];

$godoInspectionService = new \App\Services\GodoInspectionService();
$inspectionVersion = trim((string)($inspectionVersion ?? $godoInspectionService->getInspectionVersion()));
$godoCategoryLineByCode = [];
foreach ($godoCategoryLines as $godoCategoryRow) {
    if (!is_array($godoCategoryRow)) {
        continue;
    }
    $mapCateCd = trim((string)($godoCategoryRow['cateCd'] ?? ''));
    $mapLine = trim((string)($godoCategoryRow['line'] ?? ''));
    if ($mapCateCd !== '' && $mapLine !== '') {
        $godoCategoryLineByCode[$mapCateCd] = $mapLine;
    }
}
?>

<style>
.inspection-wrap { font-size: 12px; color: #374151; }
.inspection-summary { margin-bottom: 10px; padding: 10px 12px; border: 1px solid #e5e7eb; background: #f8fafc; line-height: 1.5; }
.inspection-checklist-table { width: 100%; margin-top: 8px; border-collapse: collapse; font-size: 12px; color: #111827; }
.inspection-checklist-th { text-align: left; padding: 4px 6px; border: 1px solid #e5e7eb; background: #f9fafb; }
.inspection-checklist-td { padding: 4px 6px; border: 1px solid #e5e7eb; line-height: 1.4; vertical-align: top; }
.inspection-checklist-td-center { text-align: center; }
.inspection-checklist-required-required { color: #dc3545; font-weight: 700; }
.inspection-checklist-required-ref { color: #0d6efd; font-weight: 700; }
.inspection-checklist-issue { color: #dc3545; }
.inspection-fold-summary { margin-top: 10px; cursor: pointer; font-weight: 700; color: #111827; }
.inspection-history-head { margin-top: 14px; font-size: 13px; font-weight: 700; color: #111827; }
</style>

<div class="inspection-wrap">
    <div class="inspection-summary">
        <div style="font-weight:700; margin-bottom:4px;">고도몰 단일상품 검수</div>
        <div>검수버전: <b><?= htmlspecialchars($inspectionVersion !== '' ? $inspectionVersion : '-', ENT_QUOTES, 'UTF-8') ?></b></div>
        <div>상품번호: <b><?= (int)($item['pidx'] ?? 0) ?></b> / 재고코드: <b><?= (int)($item['ps_idx'] ?? 0) ?></b></div>
        <div>고도몰 상품번호: <b><?= htmlspecialchars($goodsNo !== '' ? $goodsNo : '-', ENT_QUOTES, 'UTF-8') ?></b></div>
        <?php if (!empty($godoInfoLoadedAt)) { ?>
            <div>고도몰 정보 조회시간: <b><?= htmlspecialchars((string)$godoInfoLoadedAt, ENT_QUOTES, 'UTF-8') ?></b> (로딩시간: <b><?= number_format((int)($godoInfoLoadMs ?? 0)) ?>ms</b>)</div>
        <?php } ?>
    </div>

    <?php if (!empty($godoApiErrorMessage)) { ?>
        <div style="margin-bottom:8px; color:#dc3545;">고도몰 정보 조회 실패: <?= htmlspecialchars((string)$godoApiErrorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php } ?>

    <?php if ($isMatchedByGoodsNo) { ?>
        <div style="display:flex; gap:4px; flex-wrap:wrap; margin-bottom:8px;">
            <button type="button" class="btnstyle1 btnstyle1-sm" onclick="goGodoMall('<?= htmlspecialchars($goodsNo, ENT_QUOTES, 'UTF-8') ?>');">쑈당몰 바로가기</button>
            <button type="button" class="btnstyle1 btnstyle1-sm" onclick="goGodoMallAdmin('<?= htmlspecialchars($goodsNo, ENT_QUOTES, 'UTF-8') ?>');">관리자 바로가기</button>
        </div>
    <?php } else { ?>
        <div style="margin-bottom:8px;"><b>매칭실패</b></div>
    <?php } ?>

    <?php if (!empty($inspectionIssues)) { ?>
        <form id="single_godo_inspection_form">
        <input type="hidden" name="action_mode" value="process_single_godo_inspection">
        <input type="hidden" name="prd_idx" value="<?= $prdIdx ?>">
        <input type="hidden" name="ps_idx" value="<?= $psIdx ?>">
        <table class="inspection-checklist-table">
            <thead>
            <tr>
                <th class="inspection-checklist-th" style="width:42px; text-align:center;">순번</th>
                <th class="inspection-checklist-th">필수여부 / 문제점</th>
                <th class="inspection-checklist-th">내용</th>
                <th class="inspection-checklist-th">처리</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($inspectionIssues as $issueIdx => $issueRow) { ?>
                <?php
                $issueName = trim((string)($issueRow['issue'] ?? ''));
                $actionMeta = $godoInspectionService->resolveIssueActionMeta($issueName, $intranetBarcode);
                $actionTarget = (string)($actionMeta['target'] ?? '-');
                $actionState = (string)($actionMeta['state'] ?? '확인필요');
                $actionReason = (string)($actionMeta['reason'] ?? '');
                $isAutoProcessable = ($actionState === '자동처리 가능');
                $required = (string)($issueRow['required'] ?? '필수');
                $requiredClass = ($required === '참고') ? 'inspection-checklist-required-ref' : 'inspection-checklist-required-required';
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
                        <span class="<?= $requiredClass ?>"><?= htmlspecialchars($required, ENT_QUOTES, 'UTF-8') ?></span><br>
                        <span class="inspection-checklist-issue"><?= htmlspecialchars($issueName, ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td class="inspection-checklist-td"><?= nl2br($solutionEscaped, false) ?></td>
                    <td class="inspection-checklist-td">
                        <?php if ($isAutoProcessable) { ?>
                            <label style="display:block; margin-bottom:4px;">
                                <input type="checkbox" name="selected_issues[]" value="<?= htmlspecialchars($issueName, ENT_QUOTES, 'UTF-8') ?>" checked>
                                검수처리 대상
                            </label>
                        <?php } else { ?>
                            <div style="margin-bottom:4px; color:#9ca3af;">자동처리 대상 아님</div>
                        <?php } ?>
                        <div><b>대상:</b> <?= htmlspecialchars($actionTarget, ENT_QUOTES, 'UTF-8') ?></div>
                        <div><b>자동:</b> <?= htmlspecialchars($actionState, ENT_QUOTES, 'UTF-8') ?></div>
                        <div style="color:#6b7280;"><?= htmlspecialchars($actionReason, ENT_QUOTES, 'UTF-8') ?></div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <button type="button" class="btnstyle1 btnstyle1-md" onclick="singleGodoInspection.processSelected()">체크항목 일괄 검수 처리</button>
        
        </form>

    <?php } elseif ($isMatchedByGoodsNo) { ?>
        <div style="margin-top:6px; color:#198754;">검수 이슈 없음</div>
    <?php } else { ?>
        <div style="margin-top:6px; color:#6b7280;">매칭 실패(추가 체크리스트 준비중)</div>
    <?php } ?>

    <?php if (!empty($godoCategoryLines)) { ?>
        <details style="margin-top:10px;">
            <summary class="inspection-fold-summary">현재 고도몰에 설정된 카테고리 목록 (<?= count($godoCategoryLines) ?>)</summary>
            <table class="inspection-checklist-table" style="margin-top:8px;">
                <thead>
                <tr>
                    <th class="inspection-checklist-th">카테고리명</th>
                    <th class="inspection-checklist-th" style="width:100px; text-align:right;">코드</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($godoCategoryLines as $categoryRow) { ?>
                    <?php
                    $line = is_array($categoryRow) ? (string)($categoryRow['line'] ?? '') : (string)$categoryRow;
                    $cateCd = is_array($categoryRow) ? (string)($categoryRow['cateCd'] ?? '') : '';
                    ?>
                    <tr>
                        <td class="inspection-checklist-td"><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="inspection-checklist-td" style="text-align:right;"><?= htmlspecialchars($cateCd !== '' ? $cateCd : '-', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </details>
    <?php } ?>
    <div class="inspection-history-head">검수처리 리스트</div>
    <?php if (!empty($inspectionHistoryRows)) { ?>
        <table class="inspection-checklist-table" style="margin-top:6px;">
            <thead>
            <tr>
                <th class="inspection-checklist-th" style="width:54px; text-align:center;">No</th>
                <th class="inspection-checklist-th" style="width:180px;">실행일시 / 실행자</th>
                <th class="inspection-checklist-th" style="width:120px;">검수버전</th>
                <th class="inspection-checklist-th" style="width:150px;">위치</th>
                <th class="inspection-checklist-th">처리내용</th>
                <th class="inspection-checklist-th">수정이전값</th>
                <th class="inspection-checklist-th">수정값</th>
                <th class="inspection-checklist-th">결과</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($inspectionHistoryRows as $historyRow) { ?>
                <?php
                $historyNo = (int)($historyRow['ipl_idx'] ?? 0);
                $historyVersion = trim((string)($historyRow['inspection_version'] ?? ''));
                $locationCode = (string)($historyRow['location_code'] ?? '');
                $executedAt = (string)($historyRow['executed_at'] ?? '');
                $executorName = trim((string)($historyRow['executor_admin_name'] ?? ''));
                $executorId = trim((string)($historyRow['executor_admin_id'] ?? ''));
                $executorText = $executorName !== '' ? $executorName : '-';
                if ($executorId !== '') {
                    $executorText .= ' (' . $executorId . ')';
                }

                $locationText = $locationCode;
                if ($locationCode === 'product_single_godo_inspection') {
                    $locationText = '상품 개별 검수';
                } elseif ($locationCode === 'order_sheet_all_stock') {
                    $locationText = '재고 일괄등록';
                }

                $processContent = (isset($historyRow['process_content']) && is_array($historyRow['process_content']))
                    ? $historyRow['process_content']
                    : [];
                $resultContent = (isset($historyRow['result_content']) && is_array($historyRow['result_content']))
                    ? $historyRow['result_content']
                    : [];

                $processableIssues = (isset($processContent['processable_issues']) && is_array($processContent['processable_issues']))
                    ? $processContent['processable_issues']
                    : [];
                $processSummary = !empty($processableIssues) ? implode(', ', $processableIssues) : '-';

                $beforeValues = (isset($processContent['before_values']) && is_array($processContent['before_values']))
                    ? $processContent['before_values']
                    : [];
                $afterValues = (isset($processContent['after_values']) && is_array($processContent['after_values']))
                    ? $processContent['after_values']
                    : [];
                $changedValues = (isset($processContent['changed_values']) && is_array($processContent['changed_values']))
                    ? $processContent['changed_values']
                    : [];

                // before/after가 없고 changed_values만 있는 경우를 대비해 보강한다.
                if (empty($beforeValues) && !empty($changedValues)) {
                    foreach ($changedValues as $changedKey => $changedRow) {
                        if (!is_array($changedRow)) {
                            continue;
                        }
                        $beforeValues[$changedKey] = (string)($changedRow['before'] ?? '');
                    }
                }
                if (empty($afterValues) && !empty($changedValues)) {
                    foreach ($changedValues as $changedKey => $changedRow) {
                        if (!is_array($changedRow)) {
                            continue;
                        }
                        $afterValues[$changedKey] = (string)($changedRow['after'] ?? '');
                    }
                }

                $valueLabelMap = [
                    'cd_godo_code' => '인트라넷 고도몰상품번호',
                    'cd_sale_price' => '인트라넷 판매가',
                    'godo_only_adult_fl' => '성인인증',
                    'godo_goods_model_no' => '모델번호(바코드)',
                    'godo_cost_price' => '원가',
                    'godo_goods_price' => '판매가',
                    'category_add_codes_for_sync' => '추가 카테고리 코드',
                    'category_delete_codes_for_sync' => '삭제 카테고리 코드',
                ];

                $formatHistoryValue = function (string $valueKey, $rawValue) use ($godoInspectionService, $godoCategoryLineByCode): string {
                    $valueText = trim((string)$rawValue);
                    if ($valueText === '') {
                        return '';
                    }
                    if ($valueKey !== 'category_add_codes_for_sync' && $valueKey !== 'category_delete_codes_for_sync') {
                        return $valueText;
                    }
                    if (strpos($valueText, '(') !== false && strpos($valueText, ')') !== false) {
                        // 이미 "카테고리명(코드)" 형식으로 저장된 값은 그대로 사용
                        return $valueText;
                    }

                    $codes = array_values(array_unique(array_filter(array_map(static function ($v) {
                        return trim((string)$v);
                    }, explode(',', $valueText)), static function ($v) {
                        return $v !== '';
                    })));
                    if (empty($codes)) {
                        return $valueText;
                    }

                    $rows = [];
                    foreach ($codes as $cateCd) {
                        $cateName = $godoCategoryLineByCode[$cateCd] ?? $godoInspectionService->getCategoryNameByCode($cateCd);
                        if ($cateName === '') {
                            $rows[] = $cateCd;
                            continue;
                        }
                        $rows[] = $cateName . '(' . $cateCd . ')';
                    }

                    return implode(', ', $rows);
                };

                $beforeSummaryRows = [];
                foreach ($beforeValues as $valueKey => $valueText) {
                    $label = $valueLabelMap[$valueKey] ?? (string)$valueKey;
                    $beforeSummaryRows[] = $label . ': ' . $formatHistoryValue((string)$valueKey, $valueText);
                }
                $beforeSummary = !empty($beforeSummaryRows) ? implode("\n", $beforeSummaryRows) : '-';

                $afterSummaryRows = [];
                foreach ($afterValues as $valueKey => $valueText) {
                    $label = $valueLabelMap[$valueKey] ?? (string)$valueKey;
                    $afterSummaryRows[] = $label . ': ' . $formatHistoryValue((string)$valueKey, $valueText);
                }
                $afterSummary = !empty($afterSummaryRows) ? implode("\n", $afterSummaryRows) : '-';

                $resultMessage = trim((string)($resultContent['message'] ?? ''));
                if ($resultMessage === '') {
                    $resultMessage = '-';
                }
                ?>
                <tr>
                    <td class="inspection-checklist-td inspection-checklist-td-center"><?= $historyNo ?></td>
                    <td class="inspection-checklist-td">
                        <?= htmlspecialchars($executedAt !== '' ? $executedAt : '-', ENT_QUOTES, 'UTF-8') ?><br>
                        <span style="color:#6b7280;"><?= htmlspecialchars($executorText, ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td class="inspection-checklist-td"><?= htmlspecialchars($historyVersion !== '' ? $historyVersion : '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="inspection-checklist-td"><?= htmlspecialchars($locationText, ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="inspection-checklist-td"><?= htmlspecialchars($processSummary, ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="inspection-checklist-td"><?= nl2br(htmlspecialchars($beforeSummary, ENT_QUOTES, 'UTF-8'), false) ?></td>
                    <td class="inspection-checklist-td"><?= nl2br(htmlspecialchars($afterSummary, ENT_QUOTES, 'UTF-8'), false) ?></td>
                    <td class="inspection-checklist-td"><?= htmlspecialchars($resultMessage, ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div style="margin-top:6px; color:#6b7280;">검수처리 히스토리가 없습니다.</div>
    <?php } ?>
</div>

<script>
var singleGodoInspection = (function () {
    return {
        processSelected: function () {
            var $form = $('#single_godo_inspection_form');
            if (!$form.length) {
                alert('처리할 항목이 없습니다.');
                return;
            }
            if ($form.find('input[name="selected_issues[]"]').length < 1) {
                alert('실행할 것이 없습니다.');
                return;
            }
            if ($form.find('input[name="selected_issues[]"]:checked').length < 1) {
                alert('처리할 체크 항목을 선택해주세요.');
                return;
            }
            if (!confirm('선택한 체크 항목을 일괄 처리하시겠습니까?')) {
                return;
            }

            var $button = $form.find('button[onclick*="processSelected"]');
            $button.prop('disabled', true);
            $.ajax({
                url: '/admin/product/action',
                type: 'POST',
                dataType: 'json',
                data: $form.serializeArray(),
                success: function (res) {
                    $button.prop('disabled', false);
                    if (res && res.success === true) {
                        alert((res.message || '처리가 완료되었습니다.'));
                        if (typeof prdInfo !== 'undefined' && prdInfo && typeof prdInfo.mode === 'function') {
                            // 완료 후 동일한 "고도몰 검수 처리" 탭을 다시 로드한다.
                            prdInfo.mode(1, 'godo_inspection');
                        } else {
                            location.reload();
                        }
                        return;
                    }
                    alert((res && (res.message || res.msg)) ? (res.message || res.msg) : '처리에 실패했습니다.');
                },
                error: function (xhr) {
                    $button.prop('disabled', false);
                    var msg = (xhr && xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.msg))
                        ? (xhr.responseJSON.message || xhr.responseJSON.msg)
                        : '에러';
                    alert(msg);
                }
            });
        }
    };
}());
</script>