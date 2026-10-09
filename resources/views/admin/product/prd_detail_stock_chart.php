<style>
.stock-chart-section { margin-top: 28px; }
.stock-chart-section-title { font-size: 16px; font-weight: 700; margin: 0 0 10px; }
.stock-chart-stock-applied { margin-top: 4px; font-size: 11px; color: #1769d2; font-weight: 700; }
.stock-chart-stock-applied-calc { margin-top: 2px; font-size: 11px; color: #5a6a85; font-weight: 400; }
.stock-chart-order-btns { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.stock-chart-expected-order { margin: 0 0 16px; padding-bottom: 12px; border-bottom: 1px solid #e6e8ee; }
.stock-chart-expected-result { margin-top: 8px; padding: 10px 12px; background: #f7f9fc; border: 1px solid #e4e7ee; }
.stock-chart-expected-qty { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
.stock-chart-expected-short { color: #d4380d; font-weight: 700; }
.stock-chart-expected-result table { margin-top: 0; }
.stock-chart-expected-applied { background: #f3f7ff; }
.stock-chart-expected-note { margin-top: 6px; }
</style>


<?php
    $insight = (isset($insight) && is_array($insight)) ? $insight : [];
    $insightDaily90 = (float)($insight['daily_90'] ?? 0);
    $insightDaily28 = (float)($insight['daily_28'] ?? 0);
    $insightCoverDays = $insight['cover_days'] ?? null;
    $insightSoldOutAt = (string)($insight['soldout_at'] ?? '');
    $lookbackMonths = (int)($lookback_months ?? ($insight['lookback_months'] ?? 6));
    if (!in_array($lookbackMonths, [3, 6, 12], true)) {
        $lookbackMonths = 6;
    }
    $expectedDateValue = trim((string)($expected_date ?? ''));
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expectedDateValue)) {
        $expectedDateValue = '';
    }
    $coverDateValue = trim((string)($cover_date ?? ''));
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $coverDateValue)) {
        $coverDateValue = '';
    }
    $coverDaysValue = trim((string)($cover_days ?? '30'));
    if (!preg_match('/^\d+$/', $coverDaysValue)) {
        $coverDaysValue = '30';
    }
?>
<div class="stock-chart-expected-order">
    <div>
        <select id="expected_lookback_months">
            <option value="3" <?= $lookbackMonths === 3 ? 'selected' : '' ?>>최근 3개월</option>
            <option value="6" <?= $lookbackMonths === 6 ? 'selected' : '' ?>>최근 6개월</option>
            <option value="12" <?= $lookbackMonths === 12 ? 'selected' : '' ?>>최근 1년</option>
        </select>
        입고예상일
        <input type="date" id="expected_inbound_date" value="<?= htmlspecialchars($expectedDateValue, ENT_QUOTES, 'UTF-8') ?>">
        커버종료일
        <input type="date" id="expected_cover_date" value="<?= htmlspecialchars($coverDateValue, ENT_QUOTES, 'UTF-8') ?>">
        <input type="number" id="expected_cover_days" value="<?= htmlspecialchars($coverDaysValue, ENT_QUOTES, 'UTF-8') ?>" min="0" max="365" class="width-50">일
        <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdInfoStockChart.calcExpectedOrder()">필요수량 계산</button>
    </div>
    <div id="expected_inbound_order_result"></div>
</div>
<div class="stock-chart-section">
<div class="stock-chart-section-title">판매/발주 요약 (최근 <?= $lookbackMonths ?>개월, 품절월·입고전 제외 <?= (int)($insight['sample_months'] ?? 0) ?>개월)</div>
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
            <?php if (!empty($insight['stock_applied'])) { ?>
                <div class="stock-chart-stock-applied">현재고 반영</div>
                <div class="stock-chart-stock-applied-calc">
                    필요 <?= number_format((int)($insight['demand_qty'] ?? 0)) ?>
                    − 현재고 <?= number_format((int)($insight['current_stock'] ?? 0)) ?>
                </div>
            <?php } ?>
        </td>
    </tr>
</table>
<div class="admin-guide-text m-t-6">
    <?= htmlspecialchars((string)($insight['forecast_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
    월평균은 선택한 최근 <?= $lookbackMonths ?>개월 중 품절월·입고전을 뺀 기간입니다.
    권장발주 = 월평균 일판매 × (발주주기 <?= (int)($insight['cycle_days'] ?? 30) ?>일 + 입고리드 <?= (int)($insight['lead_days'] ?? 14) ?>일) − 현재고.
    현재고가 1개 이하이면 급판매로 보지 않습니다.
    입고리드는 주문서 작성 1주 + 입고 1주(14일)로 고정합니다. 입고가 없던 품절월과 최초입고 이전 월은 평균·미판매에서 제외합니다.
    <?php if (!empty($insight['need_order_soon'])) { ?>
        <b style="color:#d4380d;">재고 지속일이 리드일보다 짧아 이번 주기 발주가 필요합니다.</b>
    <?php } ?>
</div>
</div>

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
                    <?php if (!empty($row['is_pre_inbound_month'])) { ?>
                        <b style="color:#888;">[입고전]</b>
                    <?php } elseif (!empty($row['is_soldout_month'])) { ?>
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
        <div class="admin-guide-text">
            품절월·최초입고 이전 월은 월평균·추정미판매에서 제외합니다.
            <?php if (!empty($first_inbound_day)) { ?>
                최초입고 <?= htmlspecialchars((string)$first_inbound_day, ENT_QUOTES, 'UTF-8') ?> 이전 월은 아직 취급하지 않은 기간입니다.
            <?php } ?>
            추정미판매는 월평균 일판매 × 해당월 품절일수입니다.
        </div>
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
        <th>주문일</th>
        <th>입금일</th>
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
                <td class="text-center"><?= htmlspecialchars((string)($row['order_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '-' ?></td>
                <td class="text-center"><?= nl2br(htmlspecialchars((string)($row['deposit_date'] ?? ''), ENT_QUOTES, 'UTF-8')) ?: '-' ?></td>
                <td class="text-center"><?= htmlspecialchars((string)($row['in_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '-' ?></td>
                <td class="text-center"><?= htmlspecialchars((string)($row['end_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '-' ?></td>
                <td class="text-right"><?= number_format((int)($row['order_qty'] ?? 0)) ?></td>
                <td class="text-center"><?= (int)($row['inbound_gap_days'] ?? 0) > 0 ? ((int)$row['inbound_gap_days'] . '일') : '-' ?></td>
                <td><?= nl2br(htmlspecialchars((string)($row['order_memo'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></td>
                <td class="text-center">
                    <?php if ((int)($row['order_idx'] ?? 0) > 0) { ?>
                        <div class="stock-chart-order-btns">
                            <?php if (!empty($row['order_url'])) { ?>
                                <button type="button" class="btnstyle1 btnstyle1-sm" onclick="window.open('<?= htmlspecialchars((string)$row['order_url'], ENT_QUOTES, 'UTF-8') ?>', '_blank')">주문서상품</button>
                            <?php } ?>
                            <button type="button" class="btnstyle1 btnstyle1-sm" onclick="orderSheet.osView(this, '<?= (int)$row['order_idx'] ?>','main')">주문서상세</button>
                        </div>
                    <?php } else { ?>
                        -
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="11" class="text-center" style="padding:20px;">발주 이력이 없습니다.</td>
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
        coverInputMode: "days",
        parseDate: function(value) {
            var match = /^(\d{4})-(\d{2})-(\d{2})$/.exec($.trim(value || ""));
            if (!match) {
                return null;
            }
            var year = Number(match[1]);
            var month = Number(match[2]);
            var day = Number(match[3]);
            var date = new Date(year, month - 1, day);
            if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) {
                return null;
            }
            return date;
        },
        formatDate: function(date) {
            var month = String(date.getMonth() + 1);
            var day = String(date.getDate());
            if (month.length < 2) {
                month = "0" + month;
            }
            if (day.length < 2) {
                day = "0" + day;
            }
            return date.getFullYear() + "-" + month + "-" + day;
        },
        diffDays: function(startDate, endDate) {
            var start = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
            var end = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate());
            return Math.round((end.getTime() - start.getTime()) / 86400000);
        },
        addDays: function(date, days) {
            var next = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            next.setDate(next.getDate() + days);
            return next;
        },
        syncCoverFromDate: function() {
            var inboundDate = this.parseDate($("#expected_inbound_date").val());
            var coverDate = this.parseDate($("#expected_cover_date").val());
            if (!coverDate) {
                return false;
            }
            if (!inboundDate) {
                showAlert("Error", "입고예상일을 먼저 입력해 주세요.", "alert2");
                $("#expected_cover_date").val("");
                return false;
            }
            var days = this.diffDays(inboundDate, coverDate);
            if (days < 0) {
                showAlert("Error", "커버종료일은 입고예상일 이후여야 합니다.", "alert2");
                $("#expected_cover_date").val("");
                return false;
            }
            if (days > 365) {
                showAlert("Error", "커버기간은 365일 이하로 선택해 주세요.", "alert2");
                $("#expected_cover_date").val("");
                return false;
            }
            $("#expected_cover_days").val(String(days));
            return true;
        },
        syncCoverFromDays: function() {
            var inboundDate = this.parseDate($("#expected_inbound_date").val());
            var daysText = $.trim($("#expected_cover_days").val());
            if (daysText === "") {
                $("#expected_cover_date").val("");
                return false;
            }
            if (!inboundDate || !/^\d+$/.test(daysText)) {
                return false;
            }
            var days = parseInt(daysText, 10);
            if (days > 365) {
                return false;
            }
            $("#expected_cover_date").val(this.formatDate(this.addDays(inboundDate, days)));
            return true;
        },
        bindCoverPeriod: function() {
            var self = this;
            $("#expected_inbound_date").on("change", function() {
                if (self.coverInputMode === "date" && $.trim($("#expected_cover_date").val()) !== "") {
                    self.syncCoverFromDate();
                } else {
                    self.syncCoverFromDays();
                }
            });
            $("#expected_cover_date").on("change", function() {
                self.coverInputMode = "date";
                self.syncCoverFromDate();
            });
            $("#expected_cover_days").on("input change", function() {
                self.coverInputMode = "days";
                self.syncCoverFromDays();
            });
            $("#expected_lookback_months").on("change", function() {
                self.show();
            });
        },
        calcExpectedOrder: function() {
            var psIdx = $("#form_prd_info_stock_chart input[name='ps_idx']").val();
            var expectedDate = $.trim($("#expected_inbound_date").val());
            var coverDays = $.trim($("#expected_cover_days").val());
            var lookbackMonths = $.trim($("#expected_lookback_months").val());
            var $result = $("#expected_inbound_order_result");

            if (!expectedDate) {
                showAlert("Error", "입고예상일을 입력해 주세요.", "alert2");
                return false;
            }
            if (coverDays === "" || !/^\d+$/.test(coverDays)) {
                showAlert("Error", "커버기간은 0 이상의 정수로 입력해 주세요.", "alert2");
                return false;
            }

            $result.html('<div class="stock-chart-expected-result">계산 중입니다.</div>');

            $.ajax({
                url: "/admin/product/detail_stock_chart/expected_order",
                data: {
                    ps_idx: psIdx,
                    expected_date: expectedDate,
                    cover_days: coverDays,
                    lookback_months: lookbackMonths
                },
                type: "GET",
                dataType: "json",
                success: function(res) {
                    if (!res || res.success !== true || !res.data) {
                        showAlert("Error", (res && res.message) ? res.message : "필요수량을 계산하지 못했습니다.", "alert2");
                        $result.empty();
                        return false;
                    }
                    $result.html(prdInfoStockChart.renderExpectedOrder(res.data));
                },
                error: function(request) {
                    var message = "필요수량을 계산하지 못했습니다.";
                    if (request.responseJSON && request.responseJSON.message) {
                        message = request.responseJSON.message;
                    }
                    showAlert("Error", message, "alert2");
                    $result.empty();
                    return false;
                }
            });
        },
        renderExpectedOrder: function(data) {
            var useSurge = data.applied_basis === "surge";
            var monthClass = useSurge ? "" : "stock-chart-expected-applied";
            var surgeClass = useSurge ? "stock-chart-expected-applied" : "";
            var basisLabel = useSurge ? "급판매" : "월평균";
            var monthNote = "최근 " + this.formatQty(data.completed_month_window) + "개월";
            if (Number(data.sample_months) !== Number(data.completed_month_window)) {
                monthNote += ", 정상월 " + this.formatQty(data.sample_months) + "개월";
            }
            var html = '';
            html += '<div class="stock-chart-expected-result">';
            html += '<div class="stock-chart-expected-qty">' + this.formatOrderWithSafety(data.order_qty, data.safety_qty) + ' <span style="font-size:13px;font-weight:400;">· ' + basisLabel + ' 기준</span></div>';
            html += '<table class="table-style">';
            html += '<tr>';
            html += '<th>항목</th>';
            html += '<th class="' + monthClass + '">월평균' + (useSurge ? '' : ' · 적용') + '</th>';
            html += '<th class="' + surgeClass + '">급판매' + (useSurge ? ' · 적용' : '') + '</th>';
            html += '</tr>';
            html += this.renderExpectedRow('일판매', this.formatQty(data.daily_month) + '개', this.formatQty(data.daily_recent) + '개', monthClass, surgeClass);
            html += this.renderExpectedRow('현재고', this.formatQty(data.current_stock) + '개', this.formatQty(data.current_stock) + '개', monthClass, surgeClass);
            html += this.renderExpectedRow(
                '입고까지 ' + this.formatQty(data.days_until) + '일 판매',
                this.formatQty(data.demand_month_until) + '개',
                this.formatQty(data.demand_surge_until) + '개',
                monthClass,
                surgeClass
            );
            html += this.renderExpectedRemainRow(data, monthClass, surgeClass);
            html += this.renderExpectedRow(
                '커버 ' + this.formatQty(data.cover_days) + '일 판매',
                this.formatQty(Math.max(0, Number(data.demand_month) - Number(data.demand_month_until))) + '개',
                this.formatQty(Math.max(0, Number(data.demand_surge) - Number(data.demand_surge_until))) + '개',
                monthClass,
                surgeClass
            );
            html += this.renderExpectedRow(
                '필요수량',
                this.formatOrderWithSafety(data.order_qty_month, data.safety_qty_month),
                this.formatOrderWithSafety(data.order_qty_surge, data.safety_qty_surge),
                monthClass,
                surgeClass,
                true
            );
            html += '</table>';
            html += '<div class="admin-guide-text stock-chart-expected-note">';
            html += monthNote + '. 필요수량 = 입고까지 판매 + 커버 판매 − 현재고. ';
            html += '안전재고는 커버가 끝난 뒤 ' + this.formatQty(data.safety_days) + '일치이며 필요수량과 따로 표기합니다.';
            if (useSurge) {
                html += ' 최근 ' + this.formatQty(data.recent_days) + '일 판매가 월평균의 1.5배 이상이라 급판매를 적용했습니다.';
            }
            html += '</div>';
            html += '</div>';
            return html;
        },
        formatOrderWithSafety: function(orderQty, safetyQty) {
            var order = Number(orderQty);
            var safety = Number(safetyQty);
            if (!isFinite(order)) {
                order = 0;
            }
            if (!isFinite(safety)) {
                safety = 0;
            }
            return '필요수량 ' + this.formatQty(order) + '개 + 안전재고 (' + this.formatQty(safety) + '개) = 합계 ' + this.formatQty(order + safety) + '개';
        },
        renderExpectedRow: function(label, monthText, surgeText, monthClass, surgeClass, emphasize) {
            var weight = emphasize ? ' style="font-weight:700;"' : '';
            return '<tr' + weight + '>'
                + '<td>' + label + '</td>'
                + '<td class="text-center ' + monthClass + '">' + monthText + '</td>'
                + '<td class="text-center ' + surgeClass + '">' + surgeText + '</td>'
                + '</tr>';
        },
        renderExpectedRemainRow: function(data, monthClass, surgeClass) {
            var monthRemain = parseInt(data.remain_qty_month, 10) || 0;
            var surgeRemain = parseInt(data.remain_qty_surge, 10) || 0;
            var monthText = this.formatQty(data.remain_qty_month) + '개';
            var surgeText = this.formatQty(data.remain_qty_surge) + '개';
            if (monthRemain < 0) {
                monthText = '<span class="stock-chart-expected-short">' + monthText + ' (입고 전 부족)</span>';
            }
            if (surgeRemain < 0) {
                surgeText = '<span class="stock-chart-expected-short">' + surgeText + ' (입고 전 부족)</span>';
            }
            return this.renderExpectedRow('입고예상일에 남는 재고', monthText, surgeText, monthClass, surgeClass);
        },
        formatQty: function(value) {
            var number = Number(value);
            if (!isFinite(number)) {
                return "0";
            }
            return number.toLocaleString("ko-KR");
        },
        show: function() {
            var formData = $("#form_prd_info_stock_chart").serializeArray();
            formData.push({ name: "lookback_months", value: $("#expected_lookback_months").val() });
            formData.push({ name: "expected_date", value: $("#expected_inbound_date").val() });
            formData.push({ name: "cover_date", value: $("#expected_cover_date").val() });
            formData.push({ name: "cover_days", value: $("#expected_cover_days").val() });

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
prdInfoStockChart.bindCoverPeriod();
if ($.trim($("#expected_inbound_date").val()) !== "") {
    prdInfoStockChart.calcExpectedOrder();
}
</script>
