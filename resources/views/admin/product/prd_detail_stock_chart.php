<style>
.stock-chart-section { margin-top: 28px; }
.stock-chart-section-title { font-size: 16px; font-weight: 700; margin: 0 0 10px; }
</style>
<form id="form_prd_info_stock_chart">
    <input type="hidden" name="prd_idx" value="<?= (int)($prd_idx ?? 0) ?>">
    <input type="hidden" name="ps_idx" value="<?= (int)($ps_idx ?? 0) ?>">
    <div>
        <select name="show_mode">
            <option value="연간통계" <?= ($show_mode ?? '') === '연간통계' ? 'selected' : '' ?>>연간통계</option>
            <option value="월간통계" <?= ($show_mode ?? '') === '월간통계' ? 'selected' : '' ?>>월간통계</option>
        </select>
        &nbsp;
        <input type="text" name="cur_y" value="<?= (int)($cur_y ?? date('Y')) ?>" class="width-50 m-r-3">년
        &nbsp;
        <select name="cur_m">
            <?php for ($i = 1; $i < 13; $i++) { ?>
                <option value="<?= $i ?>" <?= ((int)($cur_m ?? 0) === $i) ? 'selected' : '' ?>><?= $i ?>월</option>
            <?php } ?>
        </select>
        &nbsp;
        <button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdInfoStockChart.show()">적용하기</button>
    </div>
</form>

<?php
    $insight = (isset($insight) && is_array($insight)) ? $insight : [];
    $insightDaily90 = (float)($insight['daily_90'] ?? 0);
    $insightDaily28 = (float)($insight['daily_28'] ?? 0);
    $insightCoverDays = $insight['cover_days'] ?? null;
    $insightSoldOutAt = (string)($insight['soldout_at'] ?? '');
?>
<div class="stock-chart-section">
<div class="stock-chart-section-title">판매/발주 요약 (품절월 제외 월평균 <?= (int)($insight['sample_months'] ?? 0) ?>개월)</div>
<table class="table-style">
    <tr>
        <th>현재고</th>
        <th>월평균 판매</th>
        <th>월평균 일판매</th>
        <th>최근 <?= (int)($insight['recent_days'] ?? 28) ?>일 일판매</th>
        <th>재고 지속일</th>
        <th>품절 예상일</th>
        <th>추정 미판매</th>
        <th>월 1회 권장발주</th>
    </tr>
    <tr>
        <td class="text-center"><b><?= number_format((int)($insight['current_stock'] ?? 0)) ?></b></td>
        <td class="text-center"><b><?= $insight['monthly_avg'] ?? 0 ?></b> 건</td>
        <td class="text-center"><b><?= $insightDaily90 ?></b> 개/일</td>
        <td class="text-center">
            <b><?= $insightDaily28 ?></b> 개/일
            <?php if (!empty($insight['is_surge'])) { ?>
                <div><b style="color:#d4380d;">급판매</b></div>
            <?php } ?>
        </td>
        <td class="text-center">
            <?php if ($insightCoverDays !== null) { ?>
                <b><?= (int)$insightCoverDays ?></b> 일
            <?php } else { ?>
                -
            <?php } ?>
        </td>
        <td class="text-center" <?= !empty($insight['need_order_soon']) ? 'style="color:#d4380d;font-weight:700;"' : '' ?>>
            <?= $insightSoldOutAt !== '' ? htmlspecialchars($insightSoldOutAt, ENT_QUOTES, 'UTF-8') : '-' ?>
        </td>
        <td class="text-center"><?= number_format((int)($insight['lost_sale_90'] ?? 0)) ?> 개</td>
        <td class="text-center">
            <b><?= number_format((int)($insight['recommended_qty'] ?? 0)) ?></b> 개
            <?php if (!empty($insight['recommended_capped'])) { ?>
                <?php if ((int)($insight['typical_inbound'] ?? 0) > 0) { ?>
                    <div class="">최근 입고 상한 : <b><?= (int)$insight['typical_inbound'] ?>개</b></div>
                <?php } ?>
                <div class="">급판매 추정치 : <b><?= number_format((int)($insight['system_recommended_qty'] ?? 0)) ?>개</b></div>
            <?php } ?>
        </td>
    </tr>
</table>
<div class="admin-guide-text m-t-6">
    <?= htmlspecialchars((string)($insight['forecast_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
    권장발주 = 월평균 일판매 × (발주주기 <?= (int)($insight['cycle_days'] ?? 30) ?>일 + 입고리드 <?= (int)($insight['lead_days'] ?? 14) ?>일) − 현재고.
    급판매여도 최근 신규입고 수량(중앙값)을 넘지 않습니다. 입고 이력이 없으면 월평균 권장의 3배로 제한합니다.
    입고리드는 주문서 작성 1주 + 입고 1주(14일)로 고정합니다. 입고가 없던 품절월은 평균·미판매에서 제외합니다.
    <?php if (!empty($insight['need_order_soon'])) { ?>
        <b style="color:#d4380d;">재고 지속일이 리드일보다 짧아 이번 주기 발주가 필요합니다.</b>
    <?php } ?>
</div>
</div>

<?php if (($show_mode ?? '') === '연간통계') { ?>
<div class="stock-chart-section">
<div class="stock-chart-section-title">연간 판매/입고 통계</div>
    <table class="table-style">
        <tr>
            <th>년/월</th>
            <th>신규입고</th>
            <th>판매</th>
            <th>일판매</th>
            <th>소진율</th>
            <th>추정<br>미판매</th>
            <th>품절일</th>
            <th>품절기간</th>
        </tr>
        <?php foreach (($yearly_rows ?? []) as $row) { ?>
            <tr>
                <td>
                    <?= (int)($row['year'] ?? 0) ?>년 <?= (int)($row['month'] ?? 0) ?>월
                    <?php if (!empty($row['is_soldout_month'])) { ?>
                        <b style="color:#d4380d;">[품절월]</b>
                    <?php } ?>
                </td>
                <td>( <?= (int)($row['in_stock_count'] ?? 0) ?> 건) <?= (int)($row['in_stock'] ?? 0) ?></td>
                <td class="text-right"><b><?= (int)($row['sale_stock'] ?? 0) ?></b>건</td>
                <td class="text-center"><?= (float)($row['daily_sale'] ?? 0) ?></td>
                <td class="text-center"><?= isset($row['sell_through']) && $row['sell_through'] !== null ? ((float)$row['sell_through'] . '%') : '-' ?></td>
                <td class="text-center"><?= (int)($row['lost_sale'] ?? 0) > 0 ? (int)$row['lost_sale'] : '-' ?></td>
                <td class="text-center"><?= !empty($row['soldout_date_text']) ? nl2br(htmlspecialchars((string)$row['soldout_date_text'], ENT_QUOTES, 'UTF-8')) : '-' ?></td>
                <td><?= !empty($row['soldout_period_text']) ? nl2br(htmlspecialchars((string)$row['soldout_period_text'], ENT_QUOTES, 'UTF-8')) : '-' ?></td>
            </tr>
        <?php } ?>
    </table>
    <div class="m-t-6">
        월평균 : <b><?= $avg_all ?? 0 ?></b> 건
        &nbsp;/&nbsp;
        이번달(<?= (int)($current_month ?? date('n')) ?>월) 제외 월평균 : <b><?= $avg_exclude_current ?? 0 ?></b> 건
        <div class="admin-guide-text">품절월은 월평균·추정미판매에서 제외합니다. 추정미판매는 월평균 일판매 × 해당월 품절일수입니다.</div>
    </div>
</div>
<?php } elseif (($show_mode ?? '') === '월간통계') { ?>
<div class="stock-chart-section">
<div class="stock-chart-section-title">월간 주차별 판매</div>
    <?php if (!empty($month_soldout_info['is_soldout_month'])) { ?>
        <div class="m-t-6" style="font-weight:700; color:#d4380d;">[품절월] 해당월은 입고 없이 품절 기간입니다.</div>
    <?php } elseif (!empty($month_soldout_info['soldout_period_text'])) { ?>
        <div class="m-t-6">
            품절일 : <?= nl2br(htmlspecialchars((string)$month_soldout_info['soldout_date_text'], ENT_QUOTES, 'UTF-8')) ?>
            /
            품절기간 : <?= nl2br(htmlspecialchars((string)$month_soldout_info['soldout_period_text'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
    <?php } ?>
<table class="table-style m-t-6">
    <tr>
        <th>주차</th>
        <th>날짜</th>
        <th>판매</th>
        <th>전주대비</th>
    </tr>
    <?php foreach (($weekly_rows ?? []) as $row) { ?>
        <tr>
            <td><b><?= (int)($row['week_num'] ?? 0) ?></b>주차</td>
            <td>(월요일) <b><?= htmlspecialchars((string)($row['start'] ?? ''), ENT_QUOTES, 'UTF-8') ?></b> ~ <b><?= htmlspecialchars((string)($row['end'] ?? ''), ENT_QUOTES, 'UTF-8') ?></b> (일요일)</td>
            <td>
                <?php if ((int)($row['sale_stock'] ?? 0) > 0) { ?>
                    <b><?= (int)$row['sale_stock'] ?></b>건
                <?php } else { ?>
                    -
                <?php } ?>
            </td>
            <td class="text-center">
                <?php if (isset($row['wow']) && $row['wow'] !== null) { ?>
                    <?php $wow = (float)$row['wow']; ?>
                    <span style="color:<?= $wow >= 50 ? '#d4380d' : ($wow < 0 ? '#666' : '#111') ?>;"><?= ($wow > 0 ? '+' : '') . $wow ?>%</span>
                <?php } else { ?>
                    -
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>
</div>
<?php } ?>

<div class="stock-chart-section">
<div class="stock-chart-section-title">최근 주문(발주) 이력 (최근 5건) - 그룹상품 저장 또는 입금완료 시 반영됩니다.</div>
<table class="table-style">
    <tr>
        <th>주문서이름</th>
        <th>상태</th>
        <th>상태변경일</th>
        <th>입고일</th>
        <th>주문종료일</th>
        <th>주문수량</th>
        <th>입고간격</th>
        <th>주문메모</th>
        <th>바로가기</th>
    </tr>
    <?php if (!empty($order_rows) && is_array($order_rows)) { ?>
        <?php foreach ($order_rows as $row) { ?>
            <tr>
                <td><?= htmlspecialchars((string)($row['order_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-center"><?= htmlspecialchars((string)($row['order_state_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '-' ?></td>
                <td class="text-center">
                    <?php
                        $stateChangedAt = trim((string)($row['state_changed_at'] ?? ''));
                        $stateChangedName = trim((string)($row['state_changed_name'] ?? ''));
                    ?>
                    <?php if ($stateChangedAt !== '') { ?>
                        <?= htmlspecialchars($stateChangedAt, ENT_QUOTES, 'UTF-8') ?><br>
                        ( <?= htmlspecialchars($stateChangedName, ENT_QUOTES, 'UTF-8') ?> )
                    <?php } else { ?>
                        -
                    <?php } ?>
                </td>
                <td class="text-center"><?= htmlspecialchars((string)($row['in_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '-' ?></td>
                <td class="text-center"><?= htmlspecialchars((string)($row['end_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '-' ?></td>
                <td class="text-right"><?= number_format((int)($row['order_qty'] ?? 0)) ?></td>
                <td class="text-center"><?= (int)($row['inbound_gap_days'] ?? 0) > 0 ? ((int)$row['inbound_gap_days'] . '일') : '-' ?></td>
                <td><?= nl2br(htmlspecialchars((string)($row['order_memo'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></td>
                <td class="text-center">
                    <?php if (!empty($row['order_url'])) { ?>
                        <button type="button" class="btnstyle1 btnstyle1-sm" onclick="window.open('<?= htmlspecialchars((string)$row['order_url'], ENT_QUOTES, 'UTF-8') ?>', '_blank')">주문서 바로가기</button>
                    <?php } else { ?>
                        -
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="9" class="text-center" style="padding:20px;">발주 이력이 없습니다.</td>
        </tr>
    <?php } ?>
</table>
</div>

<div class="stock-chart-section">
<div class="stock-chart-section-title">최근 입고 사이클 (최근 10건)</div>
<table class="table-style">
    <colgroup>
        <col width="130px"/>
        <col />
        <col width="90px"/>
        <col />
        <col width="90px"/>
        <col width="80px"/>
        <col width="80px"/>
    </colgroup>
    <tr>
        <th>입고일</th>
        <th>비고</th>
        <th>입고수량</th>
        <th>기간</th>
        <th>판매</th>
        <th>일판매</th>
        <th>소진율</th>
    </tr>
    <?php if (!empty($inbound_rows) && is_array($inbound_rows)) { ?>
        <?php foreach ($inbound_rows as $row) { ?>
            <tr>
                <td class="text-center"><?= htmlspecialchars((string)($row['psu_day'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)($row['psu_memo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-right"><?= (int)($row['psu_qry'] ?? 0) ?></td>
                <td><?= htmlspecialchars((string)($row['period_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= (int)($row['period_days'] ?? 0) ?>일)</td>
                <td class="text-right"><b><?= (int)($row['sale_stock'] ?? 0) ?></b>건</td>
                <td class="text-center"><?= (float)($row['daily_sale'] ?? 0) ?></td>
                <td class="text-center"><?= (float)($row['sell_through'] ?? 0) ?>%</td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="7" class="text-center" style="padding:20px;">입고 이력이 없습니다.</td>
        </tr>
    <?php } ?>
</table>
</div>

<script type="text/javascript">
var prdInfoStockChart = function() {
    return {
        init: function() {
        },
        show: function() {
            var formData = $("#form_prd_info_stock_chart").serializeArray();

            $.ajax({
                url: "/admin/product/detail_stock_chart",
                data: formData,
                type: "GET",
                dataType: "text",
                success: function(getHtml) {
                    if (getHtml) {
                        $("#crm_body").html(getHtml);
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    showAlert("Error", "에러", "alert2");
                    return false;
                }
            });
        }
    };
}();
</script>
