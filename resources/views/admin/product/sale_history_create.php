<style>
    .sale-item-label {
        display: block;
        padding: 4px 8px;
        border-radius: 3px;
        color: #fff;
        font-weight: 600;
        text-align: center;
    }

    .sale-item-label-have {
        background-color: #0d6efd;
    }

    .sale-item-label-provider {
        background-color: #fd7e14;
    }

    @keyframes new-row-blink {
        0% { background-color: #d9ff00; }
        50% { background-color: #f4ff9a; }
        100% { background-color: #d9ff00; }
    }

    .new-row-highlight td {
        animation: new-row-blink 0.7s ease-in-out 3;
    }

    .margin-grade-badge {
        display: inline-block;
        min-width: 22px;
        padding: 2px 8px;
        border-radius: 999px;
        color: #fff;
        font-weight: 700;
        line-height: 1.2;
    }

    .discount-rate-input {
        width: 40px;
        text-align: center;
        font-size: 15px;
        font-weight: 700;
    }

    .sale-history-condition-panel {
        border: 1px solid #d8deea;
        border-radius: 8px;
        background: #f8faff;
        padding: 10px;
    }

    .sale-history-condition-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .sale-history-condition-title {
        font-size: 14px;
        font-weight: 700;
        color: #2f3b52;
    }

    .sale-history-condition-body {
        display: block;
    }

    .sale-history-condition-body.is-collapsed {
        display: none;
    }

    .sale-history-condition-row {
        margin-top: 8px;
        line-height: 1.8;
    }

    .sale-history-field-row {
        display: flex;
        flex-wrap: wrap;
        gap: 3px 14px;
        align-items: center;
    }

    .sale-history-field {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .sale-history-inline-label {
        display: inline-block;
        min-width: 92px;
        font-weight: 600;
        color: #2f3b52;
    }

    .sale-history-number-input {
        width: 52px;
        min-width: 52px;
        max-width: 52px;
        text-align: right;
        padding-right: 6px;
    }

    .sale-history-unit {
        font-size: 12px;
        color: #5d6678;
        font-weight: 600;
    }

    .sale-history-guide {
        color: #1f6fd6;
        font-size: 12px;
        font-weight: 600;
        margin-top: 6px;
    }

    .sale-history-load-status {
        margin-top: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #1f6fd6;
        min-height: 18px;
    }

    .sale-history-load-status.is-error {
        color: #dc3545;
    }

    .sale-history-checkbox-wrap label {
        display: inline-block;
        margin-right: 8px;
        margin-bottom: 4px;
    }

    .inspect-godo-box {
        margin-top: 6px;
        padding: 8px 10px;
        border-radius: 6px;
        background: #333;
    }

    .inspect-godo-name {
        font-size: 11px;
        color: #fff;
        line-height: 1.4;
    }

    .inspect-weekly-discount-keyword {
        display: inline-block;
        padding: 0 4px;
        margin: 0 2px;
        border-radius: 3px;
        background: #ffde59;
        color: #111;
        font-weight: 700;
    }

    .inspect-godo-buttons {
        margin-top: 6px;
    }

    .provider-stock-warning {
        color: #dc3545;
        font-weight: 700;
    }

    #sale_history_processing_mask {
        display: none;
        position: fixed;
        z-index: 99999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        align-items: center;
        justify-content: center;
        text-align: center;
        white-space: pre-line;
    }

    .sale-history-create-panel-wrap {
        margin-top: 12px;
        padding: 0 30px;
    }
    .sale-history-create-panel {
        padding: 12px;
        border: 1px solid #d8deea;
        border-radius: 8px;
        background: #f8faff;
    }

    .sale-history-create-title {
        font-size: 14px;
        font-weight: 700;
        color: #2f3b52;
        margin-bottom: 8px;
    }

    .sale-history-create-row {
        display: flex;
        align-items: center;
        gap: 3px;
        margin-top: 8px;
    }

    .sale-history-create-label {
        min-width: 70px;
        font-weight: 600;
        color: #2f3b52;
    }

    .sale-history-date-input {
        width: 106px;
    }

    .sale-history-time-select {
        width: 50px;
        text-align: right;
    }

    .sale-history-time-sep {
        color: #5d6678;
        font-weight: 600;
        margin: 0 1px;
    }

    .sale-history-create-actions {
        margin-top: 12px;
        text-align: right;
    }
</style>

<div id="sale_history_processing_mask"><span class="sale-history-processing-message">데이터 처리중입니다.</span></div>

<div id="contents_head">
    <h1>상품 할인 생성</h1>
    <div class="head-count-wrap m-l-20">
        총할인상품 : <b id="display_total_count">0</b>건 보유상품 : <b id="display_have_count">0</b>건 위탁상품 : <b id="display_provider_count">0</b>건
    </div>
    <div class="head-btn-wrap m-l-20">
        <button type="button" id="godo_inspect_btn" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm" style="display:none;">
            고도몰 상품 검수
        </button>
        <button type="button" id="godo_bulk_cost_apply_btn" class="btn btnstyle1 btnstyle1-danger btnstyle1-sm m-l-5" style="display:none;">
            고도몰검수 일괄처리
        </button>
        <button type="button" id="refresh_current_btn" class="btn btnstyle1 btnstyle1-inverse btnstyle1-sm m-l-5" style="display:none;">
            현재 목록 새로고침
        </button>
        <button type="button" id="provider_data_inspect_bulk_btn" class="btn btnstyle1 btnstyle1-danger btnstyle1-sm m-l-5" style="display:none;">
            데이터검수 일괄처리
        </button>
    </div>
    <div class="head-btn-wrap m-l-20">
        상품삽입
        <select name="insert_product_mode" id="insert_product_mode">
            <option value="have">보유상품</option>
            <option value="provider">위탁상품</option>
        </select>
        <input type="text" name="insert_product_code" id="insert_product_code" value="" placeholder="재고코드/상품코드">
        <button type="button" id="insert_product_btn" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm">
            상품삽입
        </button>
    </div>
</div>
<div id="contents_body" class="partition-body">
    <div id="contents_body_wrap">

        <div class="partition-wrap">
            <ul class="partition-body">

                <div class="table-wrap5">
                    <div class="scroll-wrap">
                        <table class="table-st1">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="keep_all_checkbox" title="전체 선택"></th>
                                    <th>제외</th>
                                    <th>교체</th>
                                    <th>종류</th>
                                    <th>상품고유번호</th>

                                    <th>분류코드</th>
                                    <th>이미지</th>
                                    <th>상품명</th>
                                    <th>브랜드</th>
                                    <th>공급사</th>
                                    
                                    <th>재고</th>
                                    <th>마지막 할인일</th>
                                    <th>생성일</th>
                                    <th>판매가</th>
                                    <th>원가</th>
                                    <th>마진율</th>
                                    <th>고도몰검수</th>
                                    <th>데이터검수</th>
                                    <th>할인율</th>
                                    <th>할인판매가</th>
                                    <th>할인후마진</th>
                                </tr>
                            </thead>
                            <tbody id="random_product_tbody">
                                <tr>
                                    <td colspan="21" class="text-center" style="padding:20px;">랜덤 할인상품 불러오기를 실행해주세요.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </ul>
            <ul class="partition-right ">

                <div class="grouping-view-form-wrap">
                    <ul>
                        <button type="button" id="load_random_product_btn" class="btnstyle1 btnstyle1-primary btnstyle1-sm">
                            랜덤 할인상품 불러오기
                        </button>
                        <button type="button" id="one_touch_random_btn" class="btnstyle1 btnstyle1-danger btnstyle1-sm m-l-5">
                            원터치 랜덤 할인상품 불러오기
                        </button>
                        <div id="save_btn_status_text" class="sale-history-load-status">대기중</div>
                    </ul>
                    <div class="sale-history-condition-panel">
                        <div class="sale-history-condition-header">
                            <span class="sale-history-condition-title">조건 설정</span>
                            <button type="button" id="toggle_condition_btn" class="btnstyle1 btnstyle1-inverse btnstyle1-xs">접기 ▲</button>
                        </div>
                        <div id="sale_history_condition_body" class="sale-history-condition-body">
                            <div class="sale-history-condition-row">
                                <div class="sale-history-field-row">
                                    <div class="sale-history-field">
                                        <span class="sale-history-inline-label">보유상품 수량</span>
                                        <input type="text" name="have_product_qty" id="have_product_qty" value="10" class="sale-history-number-input">
                                    </div>
                                    <div class="sale-history-field">
                                        <span class="sale-history-inline-label">공급사 상품 수량</span>
                                        <input type="text" name="provider_product_qty" id="provider_product_qty" value="20" class="sale-history-number-input">
                                    </div>
                                </div>
                            </div>
                            <div class="sale-history-condition-row">
                                <div class="sale-history-field-row">
                                    <div class="sale-history-field">
                                        <span class="sale-history-inline-label">보유 최소 마진율</span>
                                        <input type="text" name="have_product_margin_per" id="have_product_margin_per" value="15" class="sale-history-number-input">
                                        <span class="sale-history-unit">%</span>
                                    </div>
                                    <div class="sale-history-field">
                                        <span class="sale-history-inline-label">공급사 최소 마진율</span>
                                        <input type="text" name="provider_product_margin_per" id="provider_product_margin_per" value="15" class="sale-history-number-input">
                                        <span class="sale-history-unit">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="sale-history-condition-row">
                                <div class="sale-history-field-row">
                                    <div class="sale-history-field">
                                        <span class="sale-history-inline-label">보유상품 최소재고</span>
                                        <input type="text" name="have_product_min_stock" id="have_product_min_stock" value="3" class="sale-history-number-input">
                                    </div>
                                </div>
                            </div>
                            <div class="sale-history-condition-row">
                                <div class="sale-history-field-row">
                                    <div class="sale-history-field">
                                        <span class="sale-history-inline-label">할인중복 기준</span>
                                        <select name="sale_duplicate_mode" id="sale_duplicate_mode">
                                            <option value="1month">최근할인 1달이전</option>
                                            <option value="3week">최근할인 3주이전</option>
                                            <option value="2week">최근할인 2주이전</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="sale-history-condition-row sale-history-checkbox-wrap">
                                <span class="sale-history-inline-label">분류</span>
                                <div>
                                    <?php 
                                        foreach ($prd_kind_name as $key => $kind) {
                                            $isChecked = !in_array((string)$key, ['SET', 'ONLYORDER'], true);
                                        ?>
                                        <label><input type="checkbox" name="selected_kind_codes[]" value="<?= $key ?>" <?= $isChecked ? 'checked' : '' ?>> <?= $kind ?></label>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="sale-history-condition-row sale-history-checkbox-wrap">
                                <span class="sale-history-inline-label">브랜드 제외</span>
                                <label><input type="checkbox" name="exclude_brand_idxs[]" value="34" checked> 텐가</label>
                                <label><input type="checkbox" name="exclude_brand_idxs[]" value="37" checked> 로마</label>
                            </div>
                            <div class="sale-history-condition-row">
                                <label><input type="checkbox" name="min_sale_price_exclude" id="min_sale_price_exclude" value="1"> 최저 판매가 존재시 제외</label>
                                <div class="sale-history-guide">마지막 할인일이 오래된 상품(또는 할인일 없는 상품) 우선으로 추천됩니다.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sale-history-create-panel-wrap">
                    <div class="sale-history-create-panel">
                        <div class="sale-history-create-title">할인 생성 설정</div>
                        <div class="sale-history-create-row">
                            <span class="sale-history-create-label">할인모드</span>
                            <select name="sale_mode" id="sale_mode">
                                <option value="day">데이할인</option>
                                <option value="week">주간할인</option>
                                <option value="month">월간할인</option>
                            </select>
                        </div>
                        <div class="sale-history-create-row">
                            <span class="sale-history-create-label">할인시작일</span>
                            <div class="calendar-input" style="display:inline-block; width:105px;">
                                <input type="text" name="sale_start_date" id="sale_start_date" value="" class="sale-history-date-input" style="width:90px;" placeholder="시작일" autocomplete="off">
                            </div>
                            <select id="sale_start_hour" class="sale-history-time-select"></select>
                            <span class="sale-history-time-sep">:</span>
                            <select id="sale_start_minute" class="sale-history-time-select"></select>
                        </div>
                        <div class="sale-history-create-row">
                            <span class="sale-history-create-label">할인종료일</span>
                            <div class="calendar-input" style="display:inline-block; width:105px;">
                                <input type="text" name="sale_end_date" id="sale_end_date" value="" class="sale-history-date-input" style="width:90px;" placeholder="종료일" autocomplete="off">
                            </div>
                            <select id="sale_end_hour" class="sale-history-time-select"></select>
                            <span class="sale-history-time-sep">:</span>
                            <select id="sale_end_minute" class="sale-history-time-select"></select>
                        </div>
                        <div class="sale-history-create-actions">
                            <button type="button" id="create_sale_history_btn" class="btnstyle1 btnstyle1-primary btnstyle1-md">
                                할인생성
                            </button>
                        </div>
                    </div>
                </div>

            </ul>
        </div>
    </div>
</div>
<div id="contents_bottom">
    <button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="location.href='/admin/product/grouping'">
        <i class="fas fa-arrow-left"></i> 목록
    </button>
</div>

<script>
    var pinnedItemsMap = {};
    var currentItemsMap = {};
    var latestGodoGoodsResult = { stock_codes: [], count: 0, items: [] };
    var latestGodoGoodsMap = {};
    var latestInspectRequestedKeyMap = {};
    var discountRateInputMap = {};
    var providerDataInspectBulkAttempted = false;

    // 숫자를 천단위 콤마 문자열로 변환한다.
    function numberWithComma(value) {
        var num = Number(value || 0);
        if (isNaN(num)) {
            return '0';
        }
        return num.toLocaleString('ko-KR');
    }

    // XSS 방지를 위해 HTML 특수문자를 이스케이프한다.
    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // 검수상품명에서 [주간할인] 키워드를 강조한다.
    function highlightWeeklyDiscountKeyword(value) {
        var safeText = escapeHtml(value);
        return safeText.replace(/\[주간할인\]/g, '<span class="inspect-weekly-discount-keyword">[주간할인]</span>');
    }

    // 마지막 할인일 표시값을 정규화한다.
    function formatLastSaleDate(value) {
        var text = String(value || '').trim();
        if (!text || text === '0000-00-00') {
            return '-';
        }
        return text;
    }

    // 날짜/시간 문자열에서 날짜(YYYY-MM-DD)만 추출한다.
    function formatDateOnly(value) {
        var text = String(value || '').trim();
        if (!text || text === '0000-00-00' || text === '0000-00-00 00:00:00') {
            return '-';
        }
        return text.length >= 10 ? text.substring(0, 10) : text;
    }

    // Date 객체를 YYYY-MM-DD HH:mm:ss 문자열로 변환한다.
    function formatDateTimeValue(dateObj) {
        var year = dateObj.getFullYear();
        var month = String(dateObj.getMonth() + 1).padStart(2, '0');
        var day = String(dateObj.getDate()).padStart(2, '0');
        var hour = String(dateObj.getHours()).padStart(2, '0');
        var minute = String(dateObj.getMinutes()).padStart(2, '0');
        var second = String(dateObj.getSeconds()).padStart(2, '0');
        return year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second;
    }

    // Date 객체를 YYYY-MM-DD 문자열로 변환한다.
    function formatDateOnlyValue(dateObj) {
        var year = dateObj.getFullYear();
        var month = String(dateObj.getMonth() + 1).padStart(2, '0');
        var day = String(dateObj.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    // 시간 선택 UI(시/분) 옵션을 초기화한다.
    function initSaleTimeSelectors() {
        var buildOptionHtml = function (max) {
            var html = '';
            for (var i = 0; i <= max; i++) {
                var value = String(i).padStart(2, '0');
                html += '<option value="' + value + '">' + value + '</option>';
            }
            return html;
        };

        var hourHtml = buildOptionHtml(23);
        var minuteHtml = buildOptionHtml(59);
        $('#sale_start_hour, #sale_end_hour').html(hourHtml);
        $('#sale_start_minute, #sale_end_minute').html(minuteHtml);

        $('#sale_start_hour').val('17');
        $('#sale_end_hour').val('17');
        $('#sale_start_minute').val('00');
        $('#sale_end_minute').val('00');
    }

    // 시/분 선택값을 가져온다.
    function getSelectedTimeByPrefix(prefix) {
        var hour = Number($('#' + prefix + '_hour').val() || 17);
        var minute = Number($('#' + prefix + '_minute').val() || 0);
        if (isNaN(hour) || hour < 0 || hour > 23) {
            hour = 17;
        }
        if (isNaN(minute) || minute < 0 || minute > 59) {
            minute = 0;
        }
        return { hour: hour, minute: minute };
    }

    // Date 객체 시간을 시/분 선택 UI에 반영한다.
    function setSelectedTimeByPrefix(prefix, dateObj) {
        $('#' + prefix + '_hour').val(String(dateObj.getHours()).padStart(2, '0'));
        $('#' + prefix + '_minute').val(String(dateObj.getMinutes()).padStart(2, '0'));
    }

    // 날짜 입력값을 YYYY-MM-DD 형태로 정규화한다.
    function normalizeSaleDateText(value) {
        var text = String(value || '').trim();
        var match = text.match(/^(\d{4})-(\d{2})-(\d{2})(?:\s+.*)?$/);
        if (!match) {
            return '';
        }

        var year = Number(match[1]);
        var month = Number(match[2]);
        var day = Number(match[3]);
        var date = new Date(year, month - 1, day);
        if (
            date.getFullYear() !== year ||
            date.getMonth() !== (month - 1) ||
            date.getDate() !== day
        ) {
            return '';
        }
        return formatDateOnlyValue(date);
    }

    // 날짜 입력값 + 시간 선택값으로 Date 객체를 만든다.
    function parseDateInputToDate(value, timePrefix) {
        var normalized = normalizeSaleDateText(value);
        if (!normalized) {
            return null;
        }

        var match = normalized.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (!match) {
            return null;
        }
        var year = Number(match[1]);
        var month = Number(match[2]);
        var day = Number(match[3]);
        var time = getSelectedTimeByPrefix(timePrefix || 'sale_start');
        var hour = Number(time.hour);
        var minute = Number(time.minute);
        var second = 0;
        var date = new Date(year, month - 1, day, hour, minute, second);
        if (
            date.getFullYear() !== year ||
            date.getMonth() !== (month - 1) ||
            date.getDate() !== day ||
            date.getHours() !== hour ||
            date.getMinutes() !== minute ||
            date.getSeconds() !== second
        ) {
            return null;
        }
        return date;
    }

    // 두 날짜의 일수 차이를 계산한다. (end - start)
    function calcDateDiffDays(startDate, endDate) {
        var msPerDay = 24 * 60 * 60 * 1000;
        return Math.round((endDate.getTime() - startDate.getTime()) / msPerDay);
    }

    // 할인모드/시작일/종료일 조합을 검증한다.
    function validateSaleCreateDateRange(showAlert) {
        var saleMode = String($('#sale_mode').val() || '').trim();
        var startDateText = String($('#sale_start_date').val() || '').trim();
        var endDateText = String($('#sale_end_date').val() || '').trim();

        if (!startDateText || !endDateText) {
            return { valid: true, code: 'skip_empty' };
        }

        var startDate = parseDateInputToDate(startDateText, 'sale_start');
        var endDate = parseDateInputToDate(endDateText, 'sale_end');
        if (!startDate || !endDate) {
            if (showAlert) {
                alert('할인 시작일/종료일 형식이 올바르지 않습니다. (YYYY-MM-DD)');
            }
            return { valid: false, code: 'invalid_format' };
        }

        var diffDays = calcDateDiffDays(startDate, endDate);
        if (diffDays < 0) {
            if (showAlert) {
                alert('할인 종료일은 할인 시작일보다 빠를 수 없습니다.');
            }
            return { valid: false, code: 'end_before_start' };
        }

        if (saleMode === 'day' && diffDays !== 1) {
            if (showAlert) {
                alert('데이할인은 시작일과 종료일의 차이가 정확히 1일이어야 합니다.');
            }
            return { valid: false, code: 'day_range_invalid' };
        }

        if (saleMode === 'week' && diffDays !== 7) {
            if (showAlert) {
                alert('주간할인은 시작일과 종료일의 차이가 정확히 1주(7일)여야 합니다.');
            }
            return { valid: false, code: 'week_range_invalid' };
        }

        return { valid: true, code: 'ok' };
    }

    // 날짜 입력값 정규화/자동 보정(데이할인 +1일/-1일)을 처리한다.
    function normalizeAndAutoFillSaleDates(changedFieldId) {
        var $startInput = $('#sale_start_date');
        var $endInput = $('#sale_end_date');
        var saleMode = String($('#sale_mode').val() || '').trim();

        var startDateObj = parseDateInputToDate($startInput.val(), 'sale_start');
        var endDateObj = parseDateInputToDate($endInput.val(), 'sale_end');
        if (startDateObj) {
            setSelectedTimeByPrefix('sale_start', startDateObj);
        }
        if (endDateObj) {
            setSelectedTimeByPrefix('sale_end', endDateObj);
        }

        var startNormalized = '';
        if (startDateObj) {
            var startTime = getSelectedTimeByPrefix('sale_start');
            startDateObj.setHours(startTime.hour, startTime.minute, 0, 0);
            startNormalized = formatDateOnlyValue(startDateObj);
        }
        var endNormalized = '';
        if (endDateObj) {
            var endTime = getSelectedTimeByPrefix('sale_end');
            endDateObj.setHours(endTime.hour, endTime.minute, 0, 0);
            endNormalized = formatDateOnlyValue(endDateObj);
        }
        if (startNormalized) {
            $startInput.val(startNormalized);
        }
        if (endNormalized) {
            $endInput.val(endNormalized);
        }

        if (saleMode !== 'day') {
            return;
        }

        if (changedFieldId === 'sale_start_date' && startNormalized) {
            var startDate = parseDateInputToDate(startNormalized, 'sale_start');
            if (startDate) {
                var nextDate = new Date(startDate.getTime());
                nextDate.setDate(nextDate.getDate() + 1);
                var startSelectedTime = getSelectedTimeByPrefix('sale_start');
                nextDate.setHours(startSelectedTime.hour, startSelectedTime.minute, 0, 0);
                $('#sale_end_hour').val(String(startSelectedTime.hour).padStart(2, '0'));
                $('#sale_end_minute').val(String(startSelectedTime.minute).padStart(2, '0'));
                $endInput.val(formatDateOnlyValue(nextDate));
            }
            return;
        }

        if (changedFieldId === 'sale_end_date' && endNormalized && !String($startInput.val() || '').trim()) {
            var endDate = parseDateInputToDate(endNormalized, 'sale_end');
            if (endDate) {
                var prevDate = new Date(endDate.getTime());
                prevDate.setDate(prevDate.getDate() - 1);
                var endSelectedTime = getSelectedTimeByPrefix('sale_end');
                prevDate.setHours(endSelectedTime.hour, endSelectedTime.minute, 0, 0);
                $('#sale_start_hour').val(String(endSelectedTime.hour).padStart(2, '0'));
                $('#sale_start_minute').val(String(endSelectedTime.minute).padStart(2, '0'));
                $startInput.val(formatDateOnlyValue(prevDate));
            }
        }
    }

    // null/zero-date를 제외한 유효 날짜 여부를 판별한다.
    function hasValidDateValue(value) {
        var text = String(value == null ? '' : value).trim();
        if (!text) {
            return false;
        }

        var lower = text.toLowerCase();
        if (lower === 'null' || lower === 'undefined' || lower === 'none' || lower === 'n/a' || lower === '-') {
            return false;
        }

        // zero-date variants (ex: 0000-00-00, 0000-00-00 00:00:00, 0000-00-00T00:00:00)
        if (/^0{4}-0{2}-0{2}(?:[\sT].*)?$/.test(text)) {
            return false;
        }

        return true;
    }

    // 금액 문자열을 숫자로 안전하게 변환한다.
    function parseMoney(value) {
        var num = Number(value || 0);
        if (isNaN(num)) {
            return 0;
        }
        return num;
    }

    // 할인율 입력값을 0~99 범위 숫자로 정규화한다.
    function normalizeDiscountRate(value) {
        var text = String(value == null ? '' : value).replace(/[^0-9.]/g, '');
        var rate = Number(text);
        if (isNaN(rate)) {
            return 0;
        }
        if (rate < 0) {
            return 0;
        }
        if (rate > 99) {
            return 99;
        }
        return Math.round(rate * 100) / 100;
    }

    // 상품 소스(보유/위탁)에 맞는 최소 마진 방어값을 가져온다.
    function getMarginDefenseBySource(itemSource) {
        var targetInput = (itemSource === 'provider') ? '#provider_product_margin_per' : '#have_product_margin_per';
        var minMargin = Number($(targetInput).val() || 0);
        if (isNaN(minMargin)) {
            return 0;
        }
        return minMargin;
    }

    // 할인율 적용 후 판매가를 계산한다.
    function getDiscountedSalePrice(salePrice, discountRate) {
        var sale = parseMoney(salePrice);
        var rate = normalizeDiscountRate(discountRate);
        var discounted = Math.round(sale * (100 - rate) / 100);
        if (discounted < 0) {
            return 0;
        }
        return discounted;
    }

    // 판매가/원가 기준 마진율(%)을 계산한다.
    function calcMarginPerByPrice(salePrice, costPrice) {
        var sale = parseMoney(salePrice);
        var cost = parseMoney(costPrice);
        if (sale <= 0) {
            return 0;
        }
        return ((sale - cost) / sale) * 100;
    }

    // 마진 방어 조건을 만족하는 추천 할인율(30/25/20/15/10)을 반환한다.
    function recommendDiscountRate(item, itemSource) {
        var candidates = [30, 25, 20, 15, 10];
        var salePrice = parseMoney(item.sale_price);
        var costPrice = parseMoney(item.cost_price);
        var minMargin = getMarginDefenseBySource(itemSource);

        for (var i = 0; i < candidates.length; i++) {
            var rate = candidates[i];
            var discountedSale = getDiscountedSalePrice(salePrice, rate);
            var discountedMargin = calcMarginPerByPrice(discountedSale, costPrice);
            if (discountedMargin >= minMargin) {
                return rate;
            }
        }

        return 10;
    }

    // 할인 관련 표시값(할인율/할인가/마진율/마진금액)을 계산한다.
    function getDiscountDisplayValues(item, discountRate) {
        var rate = normalizeDiscountRate(discountRate);
        var discountedSalePrice = getDiscountedSalePrice(item.sale_price, rate);
        var discountedMarginAmount = discountedSalePrice - parseMoney(item.cost_price);
        var discountedMarginPer = calcMarginPerByPrice(discountedSalePrice, item.cost_price);
        return {
            discount_rate: rate,
            discounted_sale_price: discountedSalePrice,
            discounted_margin_amount: discountedMarginAmount,
            discounted_margin_per: discountedMarginPer
        };
    }

    // 퍼센트 숫자를 보기 좋은 문자열로 포맷한다.
    function formatPercentValue(value) {
        var num = Number(value || 0);
        if (isNaN(num)) {
            num = 0;
        }
        return (Math.round(num * 100) / 100).toFixed(2).replace(/\.00$/, '') + '%';
    }

    // 랜덤 불러오기 버튼 아래 상태 메시지를 갱신한다.
    function setSaveButtonStatus(message, isError) {
        var text = String(message || '').trim();
        var $status = $('#save_btn_status_text');
        $status.text(text || '-');
        $status.toggleClass('is-error', !!isError);
    }

    // 현재 리스트 기준 총/보유/위탁 상품 카운터를 갱신한다.
    function updateDisplaySourceCounts(items) {
        var list = Array.isArray(items) ? items : [];
        var totalCount = list.length;
        var haveCount = 0;
        var providerCount = 0;

        for (var i = 0; i < list.length; i++) {
            var source = String((list[i] || {}).item_source || 'have');
            if (source === 'provider') {
                providerCount++;
            } else {
                haveCount++;
            }
        }

        $('#display_total_count').text(numberWithComma(totalCount));
        $('#display_have_count').text(numberWithComma(haveCount));
        $('#display_provider_count').text(numberWithComma(providerCount));
    }

    // 특정 행의 할인 입력/결과 셀을 재계산해 반영한다.
    function applyDiscountValuesToRow(itemKey) {
        var key = String(itemKey || '');
        if (!key || !currentItemsMap[key]) {
            return;
        }

        var item = currentItemsMap[key];
        var itemSource = String(item.item_source || 'have');
        var discountRate = discountRateInputMap.hasOwnProperty(key)
            ? normalizeDiscountRate(discountRateInputMap[key])
            : recommendDiscountRate(item, itemSource);
        var displayValues = getDiscountDisplayValues(item, discountRate);
        discountRateInputMap[key] = displayValues.discount_rate;

        var $row = $('#random_product_tbody').find('tr[data-item-key="' + key + '"]');
        if (!$row.length) {
            return;
        }

        $row.find('.discount-rate-input').val(displayValues.discount_rate);
        $row.find('.discount-sale-price-cell').text(numberWithComma(displayValues.discounted_sale_price));
        $row.find('.discount-margin-per-cell').text(formatPercentValue(displayValues.discounted_margin_per));
        $row.find('.discount-margin-amount-cell').text('마진금액 : ' + numberWithComma(displayValues.discounted_margin_amount));
    }

    // 마진율에 따른 등급(A~I)과 색상을 반환한다.
    function getMarginGradeInfo(marginRate) {
        var rate = Number(marginRate || 0);
        if (isNaN(rate) || rate <= 0) {
            return { grade: '', color: '' };
        }

        if (rate > 39) return { grade: 'A', color: '#28a745' };
        if (rate >= 35) return { grade: 'B', color: '#20c997' };
        if (rate >= 30) return { grade: 'C', color: '#17a2b8' };
        if (rate >= 25) return { grade: 'D', color: '#0dcaf0' };
        if (rate >= 20) return { grade: 'E', color: '#ffc107' };
        if (rate >= 15) return { grade: 'F', color: '#fd7e14' };
        if (rate >= 10) return { grade: 'G', color: '#dc3545' };
        if (rate >= 5) return { grade: 'H', color: '#d63384' };
        return { grade: 'I', color: '#6c757d' };
    }

    // 체크된 유지 상품을 pinned 맵으로 수집한다.
    function collectPinnedItemsFromCheckedRows() {
        var nextPinnedMap = {};
        $('input[name="keep_product_ps_idxs[]"]:checked').each(function () {
            var key = String($(this).val() || '');
            if (!key) {
                return;
            }
            if (currentItemsMap[key]) {
                nextPinnedMap[key] = currentItemsMap[key];
                return;
            }
            if (pinnedItemsMap[key]) {
                nextPinnedMap[key] = pinnedItemsMap[key];
            }
        });
        pinnedItemsMap = nextPinnedMap;
    }

    // 개별 체크 상태를 기반으로 전체선택 체크박스를 동기화한다.
    function syncKeepAllCheckbox() {
        var $all = $('#keep_all_checkbox');
        var $rows = $('input[name="keep_product_ps_idxs[]"]');
        var total = $rows.length;
        var checked = $rows.filter(':checked').length;

        if (!total) {
            $all.prop('checked', false);
            $all.prop('indeterminate', false);
            return;
        }

        $all.prop('checked', checked === total);
        $all.prop('indeterminate', checked > 0 && checked < total);
    }

    // 현재 스테이지의 보유상품 재고코드 목록을 수집한다.
    function getCurrentStockCodes() {
        var stockCodes = [];
        Object.keys(currentItemsMap).forEach(function (key) {
            var item = currentItemsMap[key] || {};
            if (String(item.item_source || 'have') !== 'have') {
                return;
            }
            var psIdx = String(item.ps_idx || '').trim();
            if (psIdx !== '') {
                stockCodes.push(psIdx);
            }
        });
        return stockCodes;
    }

    // 현재 스테이지의 위탁상품 고도몰 상품번호 목록을 수집한다.
    function getCurrentProviderGoodsNos() {
        var goodsNos = [];
        Object.keys(currentItemsMap).forEach(function (key) {
            var item = currentItemsMap[key] || {};
            if (String(item.item_source || '') !== 'provider') {
                return;
            }
            var goodsNo = String(item.godo_goods_no || '').trim();
            if (goodsNo !== '') {
                goodsNos.push(goodsNo);
            }
        });
        return goodsNos;
    }

    // 현재 행 기준 고도몰 검수 데이터를 조회하고 맵을 갱신한다.
    function loadGodoGoodsInfoForCurrentRows() {
        var stockCodes = getCurrentStockCodes();
        var providerGoodsNos = getCurrentProviderGoodsNos();
        latestInspectRequestedKeyMap = {};
        Object.keys(currentItemsMap).forEach(function (key) {
            if (key) {
                latestInspectRequestedKeyMap[key] = true;
            }
        });
        if (!stockCodes.length && !providerGoodsNos.length) {
            latestGodoGoodsResult = { stock_codes: [], goods_nos: [], count: 0, items: [], stock_items: [], goods_no_items: [] };
            latestGodoGoodsMap = {};
            return $.Deferred().resolve(latestGodoGoodsResult).promise();
        }

        return $.ajax({
            url: '/admin/sale/history/action',
            type: 'POST',
            dataType: 'json',
            data: {
                action_mode: 'load_godo_goods_info_by_stock_codes',
                stock_codes: stockCodes,
                goods_nos: providerGoodsNos
            },
            success: function (response) {
                if (!response || response.status !== 'success') {
                    return;
                }

                latestGodoGoodsResult = response.data || { stock_codes: stockCodes, goods_nos: providerGoodsNos, count: 0, items: [], stock_items: [], goods_no_items: [] };
                latestGodoGoodsMap = {};

                var stockItems = latestGodoGoodsResult.stock_items || [];
                for (var i = 0; i < stockItems.length; i++) {
                    var goods = stockItems[i] || {};
                    var goodsCdKey = String(goods.goodsCd || '').trim();
                    if (!goodsCdKey) {
                        continue;
                    }
                    latestGodoGoodsMap[goodsCdKey] = goods;
                }

                var goodsNoItemMap = {};
                var goodsNoItems = latestGodoGoodsResult.goods_no_items || [];
                for (var j = 0; j < goodsNoItems.length; j++) {
                    var goodsByNo = goodsNoItems[j] || {};
                    var goodsNoKey = String(goodsByNo.goodsNo || '').trim();
                    if (!goodsNoKey) {
                        continue;
                    }
                    goodsNoItemMap[goodsNoKey] = goodsByNo;
                }

                Object.keys(currentItemsMap).forEach(function (itemKey) {
                    var item = currentItemsMap[itemKey] || {};
                    if (String(item.item_source || '') !== 'provider') {
                        return;
                    }
                    var providerGoodsNo = String(item.godo_goods_no || '').trim();
                    if (!providerGoodsNo || !goodsNoItemMap[providerGoodsNo]) {
                        return;
                    }
                    latestGodoGoodsMap[itemKey] = goodsNoItemMap[providerGoodsNo];
                });
                console.log('Godo goods info loaded:', latestGodoGoodsResult);
            }
        });
    }

    // 스테이지 상태에 맞춰 상단 액션 버튼 노출 상태를 제어한다.
    function toggleGodoInspectButton() {
        var hasItems = Object.keys(currentItemsMap).length > 0;
        if (hasItems) {
            $('#godo_inspect_btn').show();
            $('#refresh_current_btn').show();
        } else {
            $('#godo_inspect_btn').hide();
            $('#refresh_current_btn').hide();
        }
        toggleGodoBulkCostApplyButton();
        toggleProviderDataInspectBulkButton();
    }

    // 현재 스테이지 상품들이 고도몰 검수를 진행했는지 확인한다.
    function hasCompletedGodoInspectionForCurrentItems() {
        var itemKeys = Object.keys(currentItemsMap);
        if (!itemKeys.length) {
            return false;
        }
        for (var i = 0; i < itemKeys.length; i++) {
            if (!latestInspectRequestedKeyMap[itemKeys[i]]) {
                return false;
            }
        }
        return true;
    }

    // 고도몰 검수 요청은 했지만 매칭되지 않은(매칭안됨) 항목 수를 반환한다.
    function countUnmatchedGodoInspectionItems() {
        var itemKeys = Object.keys(currentItemsMap);
        var unmatchedCount = 0;
        for (var i = 0; i < itemKeys.length; i++) {
            var key = itemKeys[i];
            if (!latestInspectRequestedKeyMap[key]) {
                continue;
            }
            if (!latestGodoGoodsMap[key]) {
                unmatchedCount++;
            }
        }
        return unmatchedCount;
    }

    // 할인생성 가능 재고 상태인지 확인하고, 미충족 건수를 반환한다.
    function countInvalidStockStateItems() {
        var itemKeys = Object.keys(currentItemsMap);
        var invalidCount = 0;

        for (var i = 0; i < itemKeys.length; i++) {
            var item = currentItemsMap[itemKeys[i]] || {};
            var itemSource = String(item.item_source || 'have');
            if (itemSource === 'provider') {
                var providerStatus = String(item.supplier_status || '').trim();
                if (providerStatus !== '판매중') {
                    invalidCount++;
                }
                continue;
            }

            var stockQty = Number(item.stock_qty || 0);
            if (isNaN(stockQty) || stockQty <= 0) {
                invalidCount++;
            }
        }

        return invalidCount;
    }

    // 모든 라인이 검수 통과 상태인지 확인하고, 미통과 건수를 반환한다.
    function countNotPassedGodoInspectionItems() {
        var itemKeys = Object.keys(currentItemsMap);
        var notPassedCount = 0;

        for (var i = 0; i < itemKeys.length; i++) {
            var key = itemKeys[i];
            var item = currentItemsMap[key] || {};
            var godoGoods = latestGodoGoodsMap[key] || null;
            if (!latestInspectRequestedKeyMap[key] || !godoGoods) {
                notPassedCount++;
                continue;
            }

            var localSalePriceCmp = parseMoney(item.sale_price);
            var godoSalePriceCmp = parseMoney(godoGoods.goodsPrice);
            var localCostPriceCmp = parseMoney(item.cost_price);
            var godoCostPriceCmp = parseMoney(godoGoods.costPrice);
            var saleMatched = Math.abs(localSalePriceCmp - godoSalePriceCmp) < 0.0001;
            var costMatched = Math.abs(localCostPriceCmp - godoCostPriceCmp) < 0.0001;
            if (!(saleMatched && costMatched)) {
                notPassedCount++;
            }
        }

        return notPassedCount;
    }

    // 검수 결과 불일치(판매가/원가/고도상품번호) 일괄처리 대상 목록을 만든다.
    function collectInspectionMismatchTargets() {
        var targets = [];

        Object.keys(currentItemsMap).forEach(function (key) {
            var item = currentItemsMap[key] || {};
            var godoGoods = latestGodoGoodsMap[key] || null;
            if (!godoGoods) {
                return;
            }

            var goodsNo = String(godoGoods.goodsNo || '').trim();
            var localGodoCode = String(item.godo_goods_no || item.godo_goodsNo || item.godoNo || '').trim();
            var itemSource = String(item.item_source || 'have');
            var prdIdx = String(item.prd_idx || '').trim();
            var localSalePriceCmp = parseMoney(item.sale_price);
            var godoSalePriceCmp = parseMoney(godoGoods.goodsPrice);
            var localCostPriceCmp = parseMoney(item.cost_price);
            var godoCostPriceCmp = parseMoney(godoGoods.costPrice);
            var saleMatched = Math.abs(localSalePriceCmp - godoSalePriceCmp) < 0.0001;
            var costMatched = Math.abs(localCostPriceCmp - godoCostPriceCmp) < 0.0001;
            var canApplySaleMismatch = (prdIdx !== '' && Number(prdIdx) > 0);
            var effectiveSaleMismatch = (!saleMatched && canApplySaleMismatch);
            var effectiveCostMismatch = !costMatched;
            var effectiveGodoCodeMismatch = (itemSource === 'have' && goodsNo !== '' && localGodoCode !== goodsNo);

            if (!effectiveSaleMismatch && !effectiveCostMismatch && !effectiveGodoCodeMismatch) {
                return;
            }

            targets.push({
                item_key: String(item.item_key || key),
                item_source: itemSource,
                ps_idx: String(item.ps_idx || key),
                prd_idx: prdIdx,
                goods_no: goodsNo,
                local_godo_code: localGodoCode,
                cost_price: String(localCostPriceCmp),
                godo_sale_price: String(godoSalePriceCmp),
                cost_mismatch: effectiveCostMismatch ? 'Y' : 'N',
                sale_mismatch: effectiveSaleMismatch ? 'Y' : 'N',
                godo_code_mismatch: effectiveGodoCodeMismatch ? 'Y' : 'N'
            });
        });

        return targets;
    }

    // 고도몰검수 일괄처리 버튼 노출 여부를 갱신한다.
    function toggleGodoBulkCostApplyButton() {
        if (collectInspectionMismatchTargets().length > 0) {
            $('#godo_bulk_cost_apply_btn').show();
        } else {
            $('#godo_bulk_cost_apply_btn').hide();
        }
    }

    // 위탁상품 데이터검수 미완료 항목 존재 여부를 확인한다.
    function hasPendingProviderDataInspection() {
        var hasPending = false;
        Object.keys(currentItemsMap).forEach(function (key) {
            if (hasPending) {
                return;
            }
            var item = currentItemsMap[key] || {};
            if (String(item.item_source || '') !== 'provider') {
                return;
            }

            var detailCrawlerDate = String(item.detail_crawler_date || '').trim();
            var godoLoadedAt = String(item.godo_loaded_at || '').trim();
            if (!hasValidDateValue(detailCrawlerDate) || !hasValidDateValue(godoLoadedAt)) {
                hasPending = true;
            }
        });
        return hasPending;
    }

    // 위탁상품 데이터검수(크롤링/고도몰로드) 필요 대상 목록을 만든다.
    function collectPendingProviderDataInspectItems() {
        var targets = [];
        Object.keys(currentItemsMap).forEach(function (key) {
            var item = currentItemsMap[key] || {};
            if (String(item.item_source || '') !== 'provider') {
                return;
            }

            var detailCrawlerDate = String(item.detail_crawler_date || '').trim();
            var godoLoadedAt = String(item.godo_loaded_at || '').trim();
            var needSupplierDetail = !hasValidDateValue(detailCrawlerDate);
            var needGodoLoad = !hasValidDateValue(godoLoadedAt);
            if (!needSupplierDetail && !needGodoLoad) {
                return;
            }

            targets.push({
                item_key: String(item.item_key || key || ''),
                prd_idx: String(item.ps_idx || '').trim(),
                supplier_prd_pk: String(item.supplier_prd_pk || '').trim(),
                godo_goods_no: String(item.godo_goods_no || item.godo_goodsNo || item.godoNo || '').trim(),
                need_supplier_detail: needSupplierDetail,
                need_godo_load: needGodoLoad
            });
        });
        return targets;
    }

    // 위탁상품 데이터검수 미완료 상태를 항목별 건수로 집계한다.
    function getPendingProviderInspectionStatusCounts() {
        var targets = collectPendingProviderDataInspectItems();
        var needSupplierDetailCount = 0;
        var needGodoLoadCount = 0;
        for (var i = 0; i < targets.length; i++) {
            var target = targets[i] || {};
            if (target.need_supplier_detail) {
                needSupplierDetailCount++;
            }
            if (target.need_godo_load) {
                needGodoLoadCount++;
            }
        }
        return {
            total_pending_count: targets.length,
            need_supplier_detail_count: needSupplierDetailCount,
            need_godo_load_count: needGodoLoadCount
        };
    }

    // 위탁상품 데이터검수 일괄처리 버튼 노출 여부를 갱신한다.
    function toggleProviderDataInspectBulkButton() {
        if (collectPendingProviderDataInspectItems().length > 0) {
            $('#provider_data_inspect_bulk_btn').show();
        } else {
            $('#provider_data_inspect_bulk_btn').hide();
        }
    }

    // 처리중 마스크의 안내 문구를 갱신한다.
    function setSaleHistoryProcessingMaskMessage(message) {
        var text = String(message || '데이터 처리중입니다.');
        var $mask = $('#sale_history_processing_mask');
        var $loadingBox = $mask.find('#loading');
        if (!$loadingBox.length) {
            $loadingBox = $('<div id="loading" class="loading-box" style="min-width:180px; max-width:360px; padding:20px 28px; border-radius:10px; text-align:center; background:rgba(34,34,34,0.92); color:#ffffff; box-shadow:0 8px 24px rgba(0,0,0,0.28);"><i class="fas fa-spinner fa-spin loading-icon" aria-hidden="true" style="display:block; font-size:30px; line-height:1; margin-bottom:10px; color:#4fa3ff;"></i><div class="loading-text" style="font-size:14px; font-weight:600; line-height:1.5; white-space:pre-line;"></div></div>');
            $mask.empty().append($loadingBox);
        }
        $loadingBox.find('.loading-text').text(text);
    }

    // 처리중 마스크 표시/숨김 상태를 제어한다.
    function setSaleHistoryProcessingMask(active, message) {
        if (active) {
            $('#sale_history_processing_mask').css('display', 'flex');
            setSaleHistoryProcessingMaskMessage(message || '데이터 처리중입니다.');
        } else {
            $('#sale_history_processing_mask').hide();
        }
    }

    // 위탁상품 상세 크롤링 API를 호출한다.
    function requestUpdateSupplierProductDetail(target) {
        var dfd = $.Deferred();
        if (!target.prd_idx || !target.supplier_prd_pk) {
            dfd.reject('공급사 크롤링 정보가 부족합니다.');
            return dfd.promise();
        }

        $.ajax({
            url: '/admin/provider_product/action',
            type: 'POST',
            dataType: 'json',
            data: {
                action_mode: 'update_supplier_product_detail',
                prd_idx: target.prd_idx,
                supplier_prd_pk: target.supplier_prd_pk
            }
        }).done(function (res) {
            if (res && res.status === 'success') {
                dfd.resolve(res);
            } else {
                dfd.reject((res && res.message) ? res.message : '공급사 크롤링 실패');
            }
        }).fail(function (xhr) {
            dfd.reject((xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '공급사 크롤링 통신 실패');
        });

        return dfd.promise();
    }

    // 위탁상품 고도몰 로드 API를 호출한다.
    function requestLoadGodoGoodsInfo(target) {
        var dfd = $.Deferred();
        if (!target.prd_idx || !target.godo_goods_no) {
            dfd.reject('고도몰 로드 정보가 부족합니다.');
            return dfd.promise();
        }

        $.ajax({
            url: '/router/loadGodoGoodsInfo/',
            type: 'POST',
            dataType: 'json',
            data: {
                prd_idx: target.prd_idx,
                godo_goodsNo: target.godo_goods_no
            }
        }).done(function (res) {
            if (res && res.status === 'success') {
                dfd.resolve(res);
            } else {
                dfd.reject((res && res.message) ? res.message : '고도몰 로드 실패');
            }
        }).fail(function (xhr) {
            dfd.reject((xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '고도몰 로드 통신 실패');
        });

        return dfd.promise();
    }

    // 화면에 표시할 목표 상품 수를 응답/입력값에서 계산한다.
    function getTargetDisplayCount(data) {
        var fromResponse = Number((((data || {}).condition || {}).total_product_qty) || 0);
        if (fromResponse > 0) {
            return fromResponse;
        }
        var haveQty = Number($('#have_product_qty').val() || 0);
        var providerQty = Number($('#provider_product_qty').val() || 0);
        var fromInput = haveQty + providerQty;
        if (fromInput <= 0) {
            fromInput = Number($('#total_product_qty').val() || 0);
        }
        return fromInput > 0 ? fromInput : 0;
    }

    // 화면/응답 기준 목표 source별 수량을 계산한다.
    function getTargetSourceCounts(data) {
        var condition = (data || {}).condition || {};
        var haveQty = Number(condition.have_product_qty || 0);
        var providerQty = Number(condition.provider_product_qty || 0);

        if (isNaN(haveQty) || haveQty < 0) {
            haveQty = 0;
        }
        if (isNaN(providerQty) || providerQty < 0) {
            providerQty = 0;
        }

        if (haveQty <= 0 && providerQty <= 0) {
            haveQty = Number($('#have_product_qty').val() || 0);
            providerQty = Number($('#provider_product_qty').val() || 0);
            if (isNaN(haveQty) || haveQty < 0) {
                haveQty = 0;
            }
            if (isNaN(providerQty) || providerQty < 0) {
                providerQty = 0;
            }
        }

        return {
            have: haveQty,
            provider: providerQty
        };
    }

    // 유지 상품과 신규 상품을 합쳐 최종 표시 리스트를 만든다.
    function mergePinnedAndNewItems(newItems, targetCount, targetSourceCounts) {
        var merged = [];
        var used = {};
        var sourceCounts = targetSourceCounts || { have: 0, provider: 0 };
        var mergedHaveCount = 0;
        var mergedProviderCount = 0;

        Object.keys(pinnedItemsMap).forEach(function (key) {
            if (!pinnedItemsMap[key]) {
                return;
            }
            var pinnedItem = pinnedItemsMap[key];
            var pinnedSource = String((pinnedItem || {}).item_source || 'have');
            merged.push(pinnedItem);
            used[key] = true;
            if (pinnedSource === 'provider') {
                mergedProviderCount++;
            } else {
                mergedHaveCount++;
            }
        });

        var needHave = Math.max(sourceCounts.have - mergedHaveCount, 0);
        var needProvider = Math.max(sourceCounts.provider - mergedProviderCount, 0);

        var appendBySource = function (targetSource, needCount) {
            if (needCount <= 0) {
                return 0;
            }
            var added = 0;
            for (var i = 0; i < newItems.length; i++) {
                var item = newItems[i] || {};
                var source = String(item.item_source || 'have');
                if (source !== targetSource) {
                    continue;
                }
                var key = String(item.item_key || item.ps_idx || '');
                if (!key || used[key]) {
                    continue;
                }
                merged.push(item);
                used[key] = true;
                added++;
                if (targetSource === 'provider') {
                    mergedProviderCount++;
                } else {
                    mergedHaveCount++;
                }
                if (added >= needCount) {
                    break;
                }
                if (targetCount > 0 && merged.length >= targetCount) {
                    break;
                }
            }
            return added;
        };

        // source별 목표 수량을 우선 채운다.
        appendBySource('have', needHave);
        appendBySource('provider', needProvider);

        // 목표 수량 미설정/후보부족 등 예외 상황에서는 남은 슬롯을 소스 무관 채운다.
        for (var i = 0; i < newItems.length; i++) {
            if (targetCount > 0 && merged.length >= targetCount) {
                break;
            }
            var item = newItems[i] || {};
            var key = String(item.item_key || item.ps_idx || '');
            if (!key || used[key]) {
                continue;
            }
            merged.push(item);
            used[key] = true;
        }

        return merged;
    }

    // currentItemsMap 기준으로 현재 스테이지를 다시 렌더링한다.
    function rerenderCurrentStageRows() {
        var remainItems = [];
        Object.keys(currentItemsMap).forEach(function (key) {
            if (currentItemsMap[key]) {
                remainItems.push(currentItemsMap[key]);
            }
        });

        renderRandomProducts({
            items: remainItems,
            total_count: remainItems.length,
            condition: {
                total_product_qty: remainItems.length
            }
        });
    }

    // DOM에 보이는 현재 행 순서대로 item key 목록을 가져온다.
    function getRenderedItemKeysInOrder() {
        var keys = [];
        $('#random_product_tbody tr').each(function () {
            var key = String($(this).find('input[name="keep_product_ps_idxs[]"]').val() || '').trim();
            if (key) {
                keys.push(key);
            }
        });
        return keys;
    }

    // 현재 조건 패널 입력값을 AJAX 요청용 payload로 구성한다.
    function getCurrentConditionPayload() {
        var excludeBrandIdxs = [];
        $('input[name="exclude_brand_idxs[]"]:checked').each(function () {
            excludeBrandIdxs.push($(this).val());
        });
        var selectedKindCodes = [];
        $('input[name="selected_kind_codes[]"]:checked').each(function () {
            selectedKindCodes.push($(this).val());
        });

        return {
            total_product_qty: $('#total_product_qty').val(),
            have_product_qty: $('#have_product_qty').val(),
            provider_product_qty: $('#provider_product_qty').val(),
            have_product_min_stock: $('#have_product_min_stock').val(),
            have_product_margin_per: $('#have_product_margin_per').val(),
            provider_product_margin_per: $('#provider_product_margin_per').val(),
            sale_duplicate_mode: $('#sale_duplicate_mode').val(),
            exclude_brand_idxs: excludeBrandIdxs,
            selected_kind_codes: selectedKindCodes
        };
    }

    // 현재 스테이지 목록을 서버 기준 최신 데이터로 새로고침한다.
    function refreshCurrentStageFromServer() {
        var dfd = $.Deferred();
        var havePsIdxs = [];
        var providerIdxs = [];
        Object.keys(currentItemsMap).forEach(function (key) {
            var item = currentItemsMap[key] || {};
            var itemSource = String(item.item_source || 'have');
            var idx = String(item.ps_idx || '').trim();
            if (!idx) {
                return;
            }
            if (itemSource === 'provider') {
                providerIdxs.push(idx);
            } else {
                havePsIdxs.push(idx);
            }
        });

        if (!havePsIdxs.length && !providerIdxs.length) {
            dfd.resolve();
            return dfd.promise();
        }

        $.ajax({
            url: '/admin/sale/history/action',
            type: 'POST',
            dataType: 'json',
            data: {
                action_mode: 'refresh_current_product_list',
                have_ps_idxs: havePsIdxs,
                provider_idxs: providerIdxs
            }
        }).done(function (response) {
            if (!response || response.status !== 'success') {
                dfd.reject((response && response.message) ? response.message : '현재 목록 새로고침 실패');
                return;
            }
            var refreshedData = response.data || {};
            renderRandomProducts({
                items: refreshedData.items || [],
                total_count: Number(refreshedData.total_count || 0),
                force_exact_list: true,
                condition: {
                    total_product_qty: Number(refreshedData.selected_count || 0)
                }
            });
            dfd.resolve(response);
        }).fail(function (xhr) {
            dfd.reject((xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '현재 목록 새로고침 통신 실패');
        });

        return dfd.promise();
    }

    // 랜덤 할인상품 불러오기를 Promise 형태로 실행한다.
    function requestLoadRandomProducts() {
        var dfd = $.Deferred();
        collectPinnedItemsFromCheckedRows();
        var conditionPayload = getCurrentConditionPayload();
        conditionPayload.action_mode = 'load_random_product';

        $.ajax({
            url: '/admin/sale/history/action',
            type: 'POST',
            dataType: 'json',
            data: conditionPayload
        }).done(function (response) {
            if (!response || response.status !== 'success') {
                dfd.reject((response && response.message) ? response.message : '랜덤 할인상품 불러오기 실패');
                return;
            }

            var responseData = response.data || {};
            renderRandomProducts(responseData);
            var items = responseData.items || [];
            var haveCandidateCount = Number(responseData.have_candidate_count || 0);
            var providerCandidateCount = Number(responseData.provider_candidate_count || 0);
            var totalCount = Number(responseData.total_count || 0);
            if ((haveCandidateCount + providerCandidateCount) <= 0 && totalCount > 0) {
                haveCandidateCount = totalCount;
                providerCandidateCount = 0;
            }
            setSaveButtonStatus(
                '작업 완료 - 후보 보유상품: ' + numberWithComma(haveCandidateCount) + '건 / 위탁상품: ' + numberWithComma(providerCandidateCount) + '건 / 표시: ' + numberWithComma(items.length) + '건',
                false
            );
            dfd.resolve(response);
        }).fail(function (xhr) {
            dfd.reject((xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '랜덤 할인상품 통신 실패');
        });

        return dfd.promise();
    }

    // 위탁상품 데이터검수 일괄처리를 Promise 형태로 실행한다.
    function runProviderDataInspectBulkProcess() {
        var dfd = $.Deferred();
        var targets = collectPendingProviderDataInspectItems();
        if (!targets.length) {
            dfd.resolve({ success_count: 0, fail_count: 0, target_count: 0 });
            return dfd.promise();
        }
        providerDataInspectBulkAttempted = true;

        var index = 0;
        var successCount = 0;
        var failCount = 0;

        var runNext = function () {
            if (index >= targets.length) {
                refreshCurrentStageFromServer().always(function () {
                    dfd.resolve({
                        success_count: successCount,
                        fail_count: failCount,
                        target_count: targets.length
                    });
                });
                return;
            }

            var target = targets[index++];
            setSaleHistoryProcessingMaskMessage(
                '원터치 처리중\n데이터검수 ' + index + '/' + targets.length + ' (성공 ' + successCount + ' / 실패 ' + failCount + ')'
            );

            var runGodoLoad = function () {
                if (!target.need_godo_load) {
                    successCount++;
                    setTimeout(runNext, 200);
                    return;
                }
                requestLoadGodoGoodsInfo(target)
                    .done(function () { successCount++; })
                    .fail(function () { failCount++; })
                    .always(function () { setTimeout(runNext, 200); });
            };

            if (!target.need_supplier_detail) {
                runGodoLoad();
                return;
            }

            requestUpdateSupplierProductDetail(target)
                .done(function () {
                    runGodoLoad();
                })
                .fail(function () {
                    failCount++;
                    runGodoLoad();
                });
        };

        runNext();
        return dfd.promise();
    }

    // 공급사 상품 중 판매중이 아닌 항목의 item_key 목록을 반환한다.
    function collectProviderNotSellingItemKeys() {
        var keys = [];
        Object.keys(currentItemsMap).forEach(function (key) {
            var item = currentItemsMap[key] || {};
            if (String(item.item_source || '') !== 'provider') {
                return;
            }
            if (String(item.supplier_status || '').trim() !== '판매중') {
                keys.push(key);
            }
        });
        return keys;
    }

    // 고도몰 검수 요청했지만 매칭안됨인 항목 item_key 목록을 반환한다.
    function collectUnmatchedGodoItemKeys() {
        var keys = [];
        Object.keys(currentItemsMap).forEach(function (key) {
            if (!latestInspectRequestedKeyMap[key]) {
                return;
            }
            if (!latestGodoGoodsMap[key]) {
                keys.push(key);
            }
        });
        return keys;
    }

    // 특정 항목 key를 같은 소스로 교체한다.
    function replaceItemByKey(itemKey) {
        var dfd = $.Deferred();
        var targetKey = String(itemKey || '').trim();
        if (!targetKey || !currentItemsMap[targetKey]) {
            dfd.resolve(false);
            return dfd.promise();
        }

        var targetItem = currentItemsMap[targetKey] || {};
        var itemSource = String(targetItem.item_source || 'have');
        var orderedKeys = getRenderedItemKeysInOrder();
        var removedIndex = orderedKeys.indexOf(targetKey);
        if (removedIndex < 0) {
            removedIndex = orderedKeys.length;
        }

        var orderedItems = [];
        var existingKeyMap = {};
        for (var i = 0; i < orderedKeys.length; i++) {
            var key = orderedKeys[i];
            if (!currentItemsMap[key]) {
                continue;
            }
            orderedItems.push(currentItemsMap[key]);
            if (key !== targetKey) {
                existingKeyMap[key] = true;
            }
        }

        loadReplacementItem(itemSource, existingKeyMap).done(function (replacementItem) {
            if (!replacementItem) {
                dfd.resolve(false);
                return;
            }

            var remainItems = [];
            for (var j = 0; j < orderedItems.length; j++) {
                var row = orderedItems[j] || {};
                var rowKey = String(row.item_key || row.ps_idx || '');
                if (rowKey === targetKey) {
                    continue;
                }
                remainItems.push(row);
            }

            var insertIndex = removedIndex;
            if (insertIndex < 0) {
                insertIndex = 0;
            }
            if (insertIndex > remainItems.length) {
                insertIndex = remainItems.length;
            }
            remainItems.splice(insertIndex, 0, replacementItem);

            delete pinnedItemsMap[targetKey];
            renderRandomProducts({
                items: remainItems,
                total_count: remainItems.length,
                force_exact_list: true,
                highlight_item_keys: [String(replacementItem.item_key || replacementItem.ps_idx || '')],
                condition: {
                    total_product_qty: remainItems.length
                }
            });
            dfd.resolve(true);
        }).fail(function () {
            dfd.resolve(false);
        });

        return dfd.promise();
    }

    // item_key 목록을 순차 교체한다.
    function replaceItemsSequential(itemKeys) {
        var dfd = $.Deferred();
        var keys = Array.isArray(itemKeys) ? itemKeys.slice() : [];
        var index = 0;
        var replaced = 0;

        var runNext = function () {
            if (index >= keys.length) {
                dfd.resolve({ requested: keys.length, replaced: replaced });
                return;
            }
            var key = keys[index++];
            replaceItemByKey(key).done(function (ok) {
                if (ok) {
                    replaced++;
                }
                setTimeout(runNext, 120);
            });
        };

        runNext();
        return dfd.promise();
    }

    // 고도몰 검수 실행
    function runGodoInspectProcess() {
        var dfd = $.Deferred();
        loadGodoGoodsInfoForCurrentRows()
            .done(function (response) {
                if (response && response.status === 'error') {
                    dfd.reject(response.message || '고도몰 상품 검수 실패');
                    return;
                }
                renderRandomProducts({
                    items: Object.keys(currentItemsMap).map(function (key) { return currentItemsMap[key]; }),
                    total_count: Object.keys(currentItemsMap).length,
                    condition: {
                        total_product_qty: Object.keys(currentItemsMap).length
                    }
                });
                dfd.resolve(response);
            })
            .fail(function (xhr) {
                dfd.reject((xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '고도몰 상품 검수 통신 실패');
            });
        return dfd.promise();
    }

    // 고도몰검수 일괄처리 실행
    function runGodoBulkApplyProcess() {
        var dfd = $.Deferred();
        var targets = collectInspectionMismatchTargets();
        if (!targets.length) {
            dfd.resolve({ success_count: 0, fail_count: 0, target_count: 0 });
            return dfd.promise();
        }

        var index = 0;
        var successCount = 0;
        var failCount = 0;

        var runNext = function () {
            if (index >= targets.length) {
                rerenderCurrentStageRows();
                dfd.resolve({
                    success_count: successCount,
                    fail_count: failCount,
                    target_count: targets.length
                });
                return;
            }

            var target = targets[index++];
            setSaleHistoryProcessingMaskMessage(
                '원터치 처리중\n고도몰검수 일괄처리 ' + index + '/' + targets.length + ' (성공 ' + successCount + ' / 실패 ' + failCount + ')'
            );

            $.ajax({
                url: '/admin/sale/history/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'update_godo_goods_cost_price',
                    ps_idx: String(target.ps_idx || ''),
                    prd_idx: String(target.prd_idx || ''),
                    goods_no: String(target.goods_no || ''),
                    local_godo_code: String(target.local_godo_code || ''),
                    cost_price: String(target.cost_price || ''),
                    godo_sale_price: String(target.godo_sale_price || ''),
                    cost_mismatch: String(target.cost_mismatch || 'N'),
                    sale_mismatch: String(target.sale_mismatch || 'N'),
                    godo_code_mismatch: String(target.godo_code_mismatch || 'N')
                }
            }).done(function (response) {
                if (!response || response.status !== 'success') {
                    failCount++;
                    return;
                }
                successCount++;
                var applied = response.data || {};
                var itemKey = String(target.item_key || '').trim();
                if (!itemKey) {
                    var fallbackPsIdx = String(target.ps_idx || '').trim();
                    itemKey = String(target.item_source || '') === 'provider' ? ('provider_' + fallbackPsIdx) : fallbackPsIdx;
                }
                if (latestGodoGoodsMap[itemKey] && applied.cost_price_updated === true) {
                    latestGodoGoodsMap[itemKey].costPrice = String(applied.cost_price || '');
                }
                if (currentItemsMap[itemKey] && applied.sale_price_updated === true) {
                    currentItemsMap[itemKey].sale_price = parseMoney(applied.godo_sale_price);
                }
                if (currentItemsMap[itemKey] && applied.godo_code_updated === true) {
                    currentItemsMap[itemKey].godo_goods_no = String(applied.goods_no || '');
                }
            }).fail(function () {
                failCount++;
            }).always(function () {
                setTimeout(runNext, 200);
            });
        };

        runNext();
        return dfd.promise();
    }

    // 원터치 자동 처리(랜덤불러오기 -> 데이터검수/교체 -> 고도몰검수/일괄처리)를 실행한다.
    function runOneTouchRandomProcess() {
        var dfd = $.Deferred();
        var MAX_PROVIDER_LOOP = 8;
        var MAX_GODO_LOOP = 8;

        var failWith = function (message) {
            dfd.reject(message || '원터치 처리 실패');
        };

        setSaleHistoryProcessingMask(true, '원터치 처리중\n1/5 랜덤 할인상품 불러오는 중...');
        setSaveButtonStatus('원터치 처리 진행중...', false);

        requestLoadRandomProducts()
            .done(function () {
                if (!Object.keys(currentItemsMap).length) {
                    failWith('랜덤 할인상품을 불러오지 못했습니다.');
                    return;
                }

                var providerLoop = 0;
                var runProviderLoop = function () {
                    providerLoop++;
                    if (providerLoop > MAX_PROVIDER_LOOP) {
                        failWith('데이터검수/교체 단계가 제한 횟수를 초과했습니다.');
                        return;
                    }

                    setSaleHistoryProcessingMaskMessage('원터치 처리중\n2/5 데이터검수 점검 (' + providerLoop + '/' + MAX_PROVIDER_LOOP + ')');
                    runProviderDataInspectBulkProcess().always(function () {
                        var pendingStatus = getPendingProviderInspectionStatusCounts();
                        var pendingKeys = [];
                        Object.keys(currentItemsMap).forEach(function (key) {
                            var item = currentItemsMap[key] || {};
                            if (String(item.item_source || '') !== 'provider') {
                                return;
                            }
                            var detailCrawlerDate = String(item.detail_crawler_date || '').trim();
                            var godoLoadedAt = String(item.godo_loaded_at || '').trim();
                            if (!hasValidDateValue(detailCrawlerDate) || !hasValidDateValue(godoLoadedAt)) {
                                pendingKeys.push(key);
                            }
                        });
                        var soldOutKeys = collectProviderNotSellingItemKeys();
                        var replaceTargets = pendingKeys.concat(soldOutKeys).filter(function (v, i, arr) {
                            return arr.indexOf(v) === i;
                        });

                        if (!replaceTargets.length && pendingStatus.total_pending_count === 0 && soldOutKeys.length === 0) {
                            runGodoLoopEntry();
                            return;
                        }

                        if (!replaceTargets.length && pendingStatus.total_pending_count > 0) {
                            failWith('데이터검수 미완료 항목이 남아 있습니다.');
                            return;
                        }

                        setSaleHistoryProcessingMaskMessage('원터치 처리중\n3/5 교체 처리중 (' + replaceTargets.length + '건)');
                        replaceItemsSequential(replaceTargets).always(function () {
                            refreshCurrentStageFromServer().always(function () {
                                runProviderLoop();
                            });
                        });
                    });
                };

                var godoLoop = 0;
                var runGodoLoopEntry = function () {
                    godoLoop++;
                    if (godoLoop > MAX_GODO_LOOP) {
                        failWith('고도몰 검수/반영 단계가 제한 횟수를 초과했습니다.');
                        return;
                    }

                    setSaleHistoryProcessingMaskMessage('원터치 처리중\n4/5 고도몰 검수중 (' + godoLoop + '/' + MAX_GODO_LOOP + ')');
                    runGodoInspectProcess()
                        .done(function () {
                            var unmatchedKeys = collectUnmatchedGodoItemKeys();
                            if (unmatchedKeys.length > 0) {
                                setSaleHistoryProcessingMaskMessage('원터치 처리중\n매칭안됨 교체중 (' + unmatchedKeys.length + '건)');
                                replaceItemsSequential(unmatchedKeys).always(function () {
                                    refreshCurrentStageFromServer().always(function () {
                                        runProviderDataInspectBulkProcess().always(function () {
                                            runGodoLoopEntry();
                                        });
                                    });
                                });
                                return;
                            }

                            var mismatchTargets = collectInspectionMismatchTargets();
                            if (mismatchTargets.length > 0) {
                                setSaleHistoryProcessingMaskMessage('원터치 처리중\n5/5 고도몰검수 일괄처리중 (' + mismatchTargets.length + '건)');
                                runGodoBulkApplyProcess().always(function () {
                                    runGodoLoopEntry();
                                });
                                return;
                            }

                            var pendingStatus = getPendingProviderInspectionStatusCounts();
                            var notPassedCount = countNotPassedGodoInspectionItems();
                            if (pendingStatus.total_pending_count > 0) {
                                runProviderLoop();
                                return;
                            }
                            if (notPassedCount > 0) {
                                runGodoLoopEntry();
                                return;
                            }

                            setSaleHistoryProcessingMask(false);
                            setSaveButtonStatus('원터치 처리 완료 - 모든 라인이 데이터검수 정상 / 고도몰 검수통과 상태입니다.', false);
                            showToast('원터치 처리 완료', new Date().toLocaleTimeString());
                            dfd.resolve({
                                completed: true
                            });
                        })
                        .fail(function (msg) {
                            failWith(msg || '고도몰 검수 처리 실패');
                        });
                };

                runProviderLoop();
            })
            .fail(function (message) {
                failWith(message || '랜덤 할인상품 불러오기 실패');
            });

        dfd.fail(function (message) {
            setSaleHistoryProcessingMask(false);
            setSaveButtonStatus('원터치 처리 실패: ' + String(message || '알 수 없는 오류'), true);
            showToast(String(message || '원터치 처리 실패'), new Date().toLocaleTimeString());
        });

        return dfd.promise();
    }

    // 현재 스테이지의 저장용 product_json payload를 만든다.
    function buildSaleHistoryProductPayload() {
        var orderedKeys = getRenderedItemKeysInOrder();
        var productItems = [];
        var pushedKeyMap = {};

        var appendItemByKey = function (key) {
            var itemKey = String(key || '').trim();
            if (!itemKey || pushedKeyMap[itemKey] || !currentItemsMap[itemKey]) {
                return;
            }
            var item = currentItemsMap[itemKey] || {};
            var source = String(item.item_source || 'have');
            var discountRate = normalizeDiscountRate(discountRateInputMap[itemKey]);
            var discountDisplayValues = getDiscountDisplayValues(item, discountRate);

            productItems.push({
                item_key: itemKey,
                item_source: source,
                ps_idx: String(item.ps_idx || ''),
                prd_idx: String(item.prd_idx || ''),
                godo_goods_no: String(item.godo_goods_no || item.godo_goodsNo || item.godoNo || ''),
                supplier_prd_pk: String(item.supplier_prd_pk || ''),
                cd_kind_code: String(item.cd_kind_code || ''),
                prd_name: String(item.prd_name || ''),
                brand_name: String(item.brand_name || ''),
                stock_qty: Number(item.stock_qty || 0),
                supplier_status: String(item.supplier_status || ''),
                sale_price: Number(item.sale_price || 0),
                cost_price: Number(item.cost_price || 0),
                margin_per: Number(item.margin_per || 0),
                discount_rate: Number(discountDisplayValues.discount_rate || 0),
                discount_sale_price: Number(discountDisplayValues.discounted_sale_price || 0),
                discount_margin_per: Number((Math.round((discountDisplayValues.discounted_margin_per || 0) * 100) / 100)),
                discount_margin_amount: Number(discountDisplayValues.discounted_margin_amount || 0),
                detail_crawler_date: String(item.detail_crawler_date || ''),
                godo_loaded_at: String(item.godo_loaded_at || '')
            });
            pushedKeyMap[itemKey] = true;
        };

        for (var i = 0; i < orderedKeys.length; i++) {
            appendItemByKey(orderedKeys[i]);
        }
        Object.keys(currentItemsMap).forEach(function (key) {
            appendItemByKey(key);
        });

        return productItems;
    }

    // 현재 조건/검수 상태의 저장용 meta_json payload를 만든다.
    function buildSaleHistoryMetaPayload(productItems) {
        var inspectSummary = {
            requested_count: Object.keys(latestInspectRequestedKeyMap).length,
            unmatched_count: countUnmatchedGodoInspectionItems(),
            mismatch_count: collectInspectionMismatchTargets().length,
            not_passed_count: countNotPassedGodoInspectionItems()
        };
        var pendingStatus = getPendingProviderInspectionStatusCounts();

        return {
            saved_at: formatDateTimeValue(new Date()),
            source_counts: {
                total: productItems.length,
                have: productItems.filter(function (row) { return String(row.item_source || '') === 'have'; }).length,
                provider: productItems.filter(function (row) { return String(row.item_source || '') === 'provider'; }).length
            },
            sale_setting: {
                sale_mode: String($('#sale_mode').val() || 'day'),
                sale_start_date: String($('#sale_start_date').val() || '').trim(),
                sale_end_date: String($('#sale_end_date').val() || '').trim(),
                sale_start_time: String($('#sale_start_hour').val() || '00') + ':' + String($('#sale_start_minute').val() || '00'),
                sale_end_time: String($('#sale_end_hour').val() || '00') + ':' + String($('#sale_end_minute').val() || '00')
            },
            random_condition: getCurrentConditionPayload(),
            inspection_summary: inspectSummary,
            provider_inspection_summary: pendingStatus,
            provider_bulk_inspect_attempted: !!providerDataInspectBulkAttempted
        };
    }

    // 동일 소스 기준으로 중복 없는 교체용 상품 1건을 조회한다.
    function loadReplacementItem(itemSource, existingKeyMap) {
        var deferred = $.Deferred();
        var conditionPayload = getCurrentConditionPayload();
        var candidateHaveQty = Number(conditionPayload.have_product_qty || 0);
        var candidateProviderQty = Number(conditionPayload.provider_product_qty || 0);
        if (isNaN(candidateHaveQty) || candidateHaveQty < 0) {
            candidateHaveQty = 0;
        }
        if (isNaN(candidateProviderQty) || candidateProviderQty < 0) {
            candidateProviderQty = 0;
        }
        conditionPayload.action_mode = 'load_random_product';
        conditionPayload.candidate_have_product_qty = candidateHaveQty;
        conditionPayload.candidate_provider_product_qty = candidateProviderQty;
        conditionPayload.have_product_qty = (itemSource === 'have') ? 1 : 0;
        conditionPayload.provider_product_qty = (itemSource === 'provider') ? 1 : 0;

        $.ajax({
            url: '/admin/sale/history/action',
            type: 'POST',
            dataType: 'json',
            data: conditionPayload,
            success: function (response) {
                if (!response || response.status !== 'success') {
                    deferred.resolve(null);
                    return;
                }

                var items = ((response.data || {}).items) || [];
                var replacement = null;
                for (var i = 0; i < items.length; i++) {
                    var item = items[i] || {};
                    var source = String(item.item_source || 'have');
                    var key = String(item.item_key || item.ps_idx || '');
                    if (source !== itemSource || !key || existingKeyMap[key]) {
                        continue;
                    }
                    replacement = item;
                    break;
                }
                deferred.resolve(replacement);
            },
            error: function () {
                deferred.resolve(null);
            }
        });

        return deferred.promise();
    }

    // 랜덤 추출/새로고침 결과를 테이블에 렌더링한다.
    function renderRandomProducts(data) {
        var newItems = (data && data.items) ? data.items : [];
        var highlightItemKeys = (data && data.highlight_item_keys && Array.isArray(data.highlight_item_keys)) ? data.highlight_item_keys : [];
        var highlightMap = {};
        for (var h = 0; h < highlightItemKeys.length; h++) {
            var highlightKey = String(highlightItemKeys[h] || '');
            if (highlightKey) {
                highlightMap[highlightKey] = true;
            }
        }
        var forceExactList = !!((data || {}).force_exact_list);
        var targetCount = getTargetDisplayCount(data);
        var targetSourceCounts = getTargetSourceCounts(data);
        var items = forceExactList ? newItems : mergePinnedAndNewItems(newItems, targetCount, targetSourceCounts);
        var html = '';

        currentItemsMap = {};
        for (var c = 0; c < items.length; c++) {
            var currentItem = items[c] || {};
            var currentKey = String(currentItem.item_key || currentItem.ps_idx || '');
            if (currentKey) {
                currentItemsMap[currentKey] = currentItem;
            }
        }
        var nextDiscountRateInputMap = {};
        for (var currentMapKey in currentItemsMap) {
            if (!currentItemsMap.hasOwnProperty(currentMapKey)) {
                continue;
            }
            if (discountRateInputMap.hasOwnProperty(currentMapKey)) {
                nextDiscountRateInputMap[currentMapKey] = normalizeDiscountRate(discountRateInputMap[currentMapKey]);
            }
        }
        discountRateInputMap = nextDiscountRateInputMap;

        if (!items.length) {
            html = '<tr><td colspan="21" class="text-center" style="padding:20px;">조건에 맞는 상품이 없습니다.</td></tr>';
            $('#random_product_tbody').html(html);
            $('#random_product_summary').text('후보 0건 / 선택 0건');
            updateDisplaySourceCounts([]);
            syncKeepAllCheckbox();
            return;
        }

        for (var i = 0; i < items.length; i++) {
            var item = items[i] || {};
            var itemSource = String(item.item_source || 'have');
            var itemKey = String(item.item_key || item.ps_idx || '');
            var isPinned = !!pinnedItemsMap[itemKey];
            var imgPath = String(item.img_path || '').trim();
            var prdIdx = String(item.prd_idx || '').trim();
            var providerPrdIdx = String(item.ps_idx || '').trim();
            var godoGoods = latestGodoGoodsMap[itemKey] || null;
            var imgHtml = '-';
            if (imgPath) {
                if (itemSource === 'provider' && providerPrdIdx) {
                    imgHtml = '<p onclick="prdProviderQuick(\'' + providerPrdIdx + '\');" style="cursor:pointer;"><img src="' + imgPath + '" style="height:50px; border:1px solid #eee !important;"></p>';
                } else if (prdIdx) {
                    imgHtml = '<p onclick="onlyAD.prdView(\'' + prdIdx + '\',\'info\');" style="cursor:pointer;"><img src="' + imgPath + '" style="height:50px; border:1px solid #eee !important;"></p>';
                } else {
                    imgHtml = '<p><img src="' + imgPath + '" style="height:50px; border:1px solid #eee !important;"></p>';
                }
            }

            var prdNameHtml = item.prd_name || '';
            if (itemSource === 'provider' && providerPrdIdx) {
                prdNameHtml = '<p onclick="prdProviderQuick(\'' + providerPrdIdx + '\');" style="cursor:pointer;"><b>' + (item.prd_name || '') + '</b></p>';
            } else if (prdIdx) {
                prdNameHtml = '<p onclick="onlyAD.prdView(\'' + prdIdx + '\',\'info\');" style="cursor:pointer;"><b>' + (item.prd_name || '') + '</b></p>';
            }
            var supplierHtml = '자체상품';
            if (itemSource === 'provider') {
                var supplierSite = escapeHtml(item.supplier_site || '-');
                var supplierSecondName = escapeHtml(item.supplier_2nd_name || '-');
                supplierHtml = supplierSite + '<br>' + supplierSecondName;
            }
            var nameMatched = null;
            var saleMatched = null;
            var costMatched = null;
            if (godoGoods) {
                var localName = String(item.prd_name || '').trim();
                var godoName = String(godoGoods.goodsNm || '').trim();
                if (godoName !== '') {
                    nameMatched = (localName === godoName);
                }

                var localSalePriceCmp = parseMoney(item.sale_price);
                var localCostPriceCmp = parseMoney(item.cost_price);
                var godoSalePriceCmp = parseMoney(godoGoods.goodsPrice);
                var godoCostPriceCmp = parseMoney(godoGoods.costPrice);
                saleMatched = Math.abs(localSalePriceCmp - godoSalePriceCmp) < 0.0001;
                costMatched = Math.abs(localCostPriceCmp - godoCostPriceCmp) < 0.0001;
            }
            var localGodoCode = String(item.godo_goods_no || item.godo_goodsNo || item.godoNo || '').trim();
            var inspectGoodsNo = godoGoods ? String(godoGoods.goodsNo || '').trim() : '';
            var godoCodeMismatch = (itemSource === 'have' && inspectGoodsNo !== '' && localGodoCode !== inspectGoodsNo);
            var inspectNameHtml = '';
            var inspectGoodsButtonsHtml = '';
            var inspectGoodsInfoHtml = '';
            if (godoGoods && godoGoods.goodsNm) {
                inspectNameHtml = '<div class="inspect-godo-name">검수상품 : ' + highlightWeeklyDiscountKeyword(godoGoods.goodsNm) + '</div>';
            }
            if (godoGoods && godoGoods.goodsNo !== undefined && godoGoods.goodsNo !== null && String(godoGoods.goodsNo).trim() !== '') {
                var godoGoodsNo = String(godoGoods.goodsNo).trim().replace(/'/g, "\\'");
                inspectGoodsButtonsHtml = '<div class="inspect-godo-buttons">'
                    + '<button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall(\'' + godoGoodsNo + '\');">쑈당몰 상품보기</button>'
                    + '<button type="button" class="btnstyle1 btnstyle1-xs m-l-5" onclick="goGodoMallAdmin(\'' + godoGoodsNo + '\');">관리자 상품보기</button>'
                    + '</div>';
            }
            if (inspectNameHtml || inspectGoodsButtonsHtml) {
                inspectGoodsInfoHtml = '<div class="inspect-godo-box">' + inspectNameHtml + inspectGoodsButtonsHtml + '</div>';
            }
            var inspectSalePriceHtml = '';
            if (godoGoods && godoGoods.goodsPrice !== undefined && godoGoods.goodsPrice !== null && godoGoods.goodsPrice !== '') {
                var inspectSaleColor = (saleMatched === false) ? '#dc3545' : '#0d6efd';
                inspectSalePriceHtml = '<div class="m-t-3" style="font-size:11px; color:' + inspectSaleColor + ';">검수 : ' + numberWithComma(godoGoods.goodsPrice) + '</div>';
            }
            var inspectCostPriceHtml = '';
            if (godoGoods && godoGoods.costPrice !== undefined && godoGoods.costPrice !== null && godoGoods.costPrice !== '') {
                var inspectCostColor = (costMatched === false) ? '#dc3545' : '#0d6efd';
                inspectCostPriceHtml = '<div class="m-t-3" style="font-size:11px; color:' + inspectCostColor + ';">검수 : ' + numberWithComma(godoGoods.costPrice) + '</div>';
            }
            var inspectResultHtml = '-';
            if (godoGoods) {
                var isMatched = (saleMatched === true) && (costMatched === true) && !godoCodeMismatch;
                if (isMatched) {
                    inspectResultHtml = '<span style="color:#198754; font-weight:bold;">검수 통과</span>';
                } else {
                    var goodsNo = String(godoGoods.goodsNo || '').trim();
                    var localCostPriceForApply = parseMoney(item.cost_price);
                    var godoSalePriceForApply = parseMoney(godoGoods.goodsPrice);
                    var godoCodeMismatchHtml = '';
                    if (godoCodeMismatch) {
                        godoCodeMismatchHtml = '<div class="m-t-4" style="font-size:11px; color:#dc3545;">goodsNo 불일치 (API: ' + escapeHtml(goodsNo || '-') + ' / DB: ' + escapeHtml(localGodoCode || '-') + ')</div>';
                    }
                    inspectResultHtml = '<span style="color:#dc3545; font-weight:bold;">검수 불합격</span>'
                        + godoCodeMismatchHtml
                        + '<div class="m-t-5"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs apply-godo-cost-btn"'
                        + ' data-ps-idx="' + itemKey + '"'
                        + ' data-prd-idx="' + String(item.prd_idx || '').trim() + '"'
                        + ' data-goods-no="' + goodsNo + '"'
                        + ' data-local-godo-code="' + localGodoCode + '"'
                        + ' data-cost-price="' + localCostPriceForApply + '"'
                        + ' data-godo-sale-price="' + godoSalePriceForApply + '"'
                        + ' data-cost-mismatch="' + ((costMatched === false) ? 'Y' : 'N') + '"'
                        + ' data-sale-mismatch="' + ((saleMatched === false) ? 'Y' : 'N') + '"'
                        + ' data-godo-code-mismatch="' + (godoCodeMismatch ? 'Y' : 'N') + '"'
                        + '>판매가/원가 반영</button></div>';
                }
            } else if (latestInspectRequestedKeyMap[itemKey]) {
                inspectResultHtml = '<span style="color:#dc3545; font-weight:bold;">매칭안됨</span>';
            }
            var dataInspectHtml = '<span style="color:#198754; font-weight:bold;">정상</span>';
            if (itemSource === 'provider') {
                var dataInspectMessages = [];
                var detailCrawlerDate = String(item.detail_crawler_date || '').trim();
                var godoLoadedAt = String(item.godo_loaded_at || '').trim();
                if (!hasValidDateValue(detailCrawlerDate)) {
                    dataInspectMessages.push('공급사 크롤링 안됨');
                }
                if (!hasValidDateValue(godoLoadedAt)) {
                    dataInspectMessages.push('고도몰 로드 안됨');
                }
                dataInspectHtml = dataInspectMessages.length
                    ? ('<span style="color:#dc3545; font-weight:bold;">' + dataInspectMessages.join('<br>') + '</span>')
                    : '<span style="color:#198754; font-weight:bold;">정상</span>';
            }
            var marginPerValue = Number(item.margin_per || 0);
            if (isNaN(marginPerValue)) {
                marginPerValue = 0;
            }
            var marginGradeInfo = getMarginGradeInfo(marginPerValue);
            var marginGradeHtml = '';
            if (marginGradeInfo.grade) {
                marginGradeHtml = '<div><span class="margin-grade-badge" style="background-color:' + marginGradeInfo.color + ';">' + marginGradeInfo.grade + '</span></div>';
            }
            var marginRateHtml = String(item.margin_per || 0) + '%';
            var initialDiscountRate = discountRateInputMap.hasOwnProperty(itemKey)
                ? normalizeDiscountRate(discountRateInputMap[itemKey])
                : recommendDiscountRate(item, itemSource);
            var discountDisplayValues = getDiscountDisplayValues(item, initialDiscountRate);
            discountRateInputMap[itemKey] = discountDisplayValues.discount_rate;
            var rowClass = highlightMap[itemKey] ? ' class="new-row-highlight"' : '';
            html += '<tr' + rowClass + ' data-item-key="' + itemKey + '">'
                + '<td class="text-center"><input type="checkbox" name="keep_product_ps_idxs[]" value="' + itemKey + '" ' + (isPinned ? 'checked' : '') + '></td>'
                + '<td class="text-center"><button type="button" class="btnstyle1 btnstyle1-xs exclude-row-btn" data-item-key="' + itemKey + '" data-item-source="' + itemSource + '" data-target-idx="' + String(item.ps_idx || '') + '">할인대상 제외</button></td>'
                + '<td class="text-center">'
                + '<button type="button" class="btnstyle1 btnstyle1-sm replace-row-btn" data-item-key="' + itemKey + '" data-item-source="' + itemSource + '" data-target-idx="' + String(item.ps_idx || '') + '"><i class="fas fa-sync-alt"></i> 교체</button>'
                + '<div class="m-t-5"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm remove-row-btn" data-item-key="' + itemKey + '">- 제거</button></div>'
                + '</td>'
                + '<td class="text-center"><span class="sale-item-label ' + (itemSource === 'provider' ? 'sale-item-label-provider' : 'sale-item-label-have') + '">' + (itemSource === 'provider' ? '위탁상품' : '보유상품') + '</span>' + (itemSource === 'have' ? '<div class="m-t-3">재고코드 : ' + String(item.ps_idx || '') + '</div>' : '') + '</td>'
                + '<td class="text-center">' + (itemSource === 'provider' ? String(item.ps_idx || '') : String(item.prd_idx || '')) + '</td>'
                
                + '<td class="text-center">' + (item.cd_kind_code || '-') + '</td>'
                + '<td class="text-center p-5">' + imgHtml + '</td>'
                + '<td>' + prdNameHtml + inspectGoodsInfoHtml + '</td>'
                + '<td class="text-center">' + (item.brand_name || '-') + '</td>'
                + '<td class="text-center">' + supplierHtml + '</td>'
                
                + '<td class="text-center">' + (itemSource === 'provider'
                    ? ((String(item.supplier_status || '').trim() === '판매중')
                        ? '판매중'
                        : ('<span class="provider-stock-warning">' + escapeHtml(item.supplier_status || '-') + '</span>'))
                    : numberWithComma(item.stock_qty)) + '</td>'
                + '<td class="text-center">' + formatLastSaleDate(item.last_sale_date) + '</td>'
                + '<td class="text-center">' + formatDateOnly(item.created_at) + '</td>'
                + '<td class="text-right">' + numberWithComma(item.sale_price) + inspectSalePriceHtml + '</td>'
                + '<td class="text-right">' + numberWithComma(item.cost_price) + inspectCostPriceHtml + '</td>'
                + '<td class="text-center">' + marginGradeHtml + (marginGradeHtml ? '<div class="m-t-3">' + marginRateHtml + '</div>' : marginRateHtml) + '</td>'
                + '<td class="text-center">' + inspectResultHtml + '</td>'
                + '<td class="text-center">' + dataInspectHtml + '</td>'
                + '<td class="text-center"><input type="text" class="discount-rate-input" data-item-key="' + itemKey + '" value="' + discountDisplayValues.discount_rate + '"> %</td>'
                + '<td class="text-right discount-sale-price-cell">' + numberWithComma(discountDisplayValues.discounted_sale_price) + '</td>'
                + '<td class="text-center"><div class="discount-margin-per-cell">' + formatPercentValue(discountDisplayValues.discounted_margin_per) + '</div><div class="m-t-3 discount-margin-amount-cell">마진금액 : ' + numberWithComma(discountDisplayValues.discounted_margin_amount) + '</div></td>'
                + '</tr>';
        }

        $('#random_product_tbody').html(html);
        var haveCandidateCount = Number((data && data.have_candidate_count) || 0);
        var providerCandidateCount = Number((data && data.provider_candidate_count) || 0);
        var totalCandidateCount = Number((data && data.total_count) || 0);
        if ((haveCandidateCount + providerCandidateCount) <= 0 && totalCandidateCount > 0) {
            haveCandidateCount = totalCandidateCount;
            providerCandidateCount = 0;
        }
        $('#random_product_summary').text(
            '후보 보유상품: ' + numberWithComma(haveCandidateCount) + '건 / 위탁상품: ' + numberWithComma(providerCandidateCount) + '건 / 표시: ' + numberWithComma(items.length) + '건 / 유지 ' + numberWithComma(Object.keys(pinnedItemsMap).length) + '건'
        );
        updateDisplaySourceCounts(items);
        syncKeepAllCheckbox();
        toggleGodoInspectButton();
    }

    $(function () {
        initSaleTimeSelectors();

        $('#sale_start_date, #sale_end_date').on('change', function () {
            normalizeAndAutoFillSaleDates(this.id);
            validateSaleCreateDateRange(true);
        });

        $('#sale_start_hour, #sale_start_minute').on('change', function () {
            normalizeAndAutoFillSaleDates('sale_start_date');
            validateSaleCreateDateRange(true);
        });

        $('#sale_end_hour, #sale_end_minute').on('change', function () {
            normalizeAndAutoFillSaleDates('sale_end_date');
            validateSaleCreateDateRange(true);
        });

        $('#sale_mode').on('change', function () {
            normalizeAndAutoFillSaleDates('');
            validateSaleCreateDateRange(true);
        });

        $('#toggle_condition_btn').on('click', function () {
            var $body = $('#sale_history_condition_body');
            var isCollapsed = $body.hasClass('is-collapsed');
            if (isCollapsed) {
                $body.removeClass('is-collapsed');
                $(this).text('접기 ▲');
            } else {
                $body.addClass('is-collapsed');
                $(this).text('펼치기 ▼');
            }
        });

        $('#create_sale_history_btn').on('click', function (e) {

            if (!Object.keys(currentItemsMap).length) {
                alert('할인생성 대상 상품이 없습니다. 먼저 랜덤 할인상품을 불러와주세요.');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }

            normalizeAndAutoFillSaleDates('');

            var invalidStockStateCount = countInvalidStockStateItems();
            if (invalidStockStateCount > 0) {
                alert('재고 라인에 재고수량 또는 판매중이 아닌 항목이 있습니다. 해당 항목을 먼저 처리해 주세요.');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }

            // 1) 데이터검수 체크 (공급사 크롤링 안됨 -> 고도몰 로드 안됨)
            var pendingStatus = getPendingProviderInspectionStatusCounts();
            if (pendingStatus.total_pending_count > 0) {
                if (!providerDataInspectBulkAttempted) {
                    alert('[데이터검수 일괄처리]를 진행해 주세요. 만약 데이터검수 일괄처리 이후에도 처리가 안 되는 건이 있다면 상품 상세 페이지를 열어서 개별 진행하면 불가 사유를 확인할 수 있습니다. 그래도 안 되면 담당자에게 보고해 주세요.');
                } else {
                    if (pendingStatus.need_supplier_detail_count > 0) {
                        alert('"공급사 크롤링 안됨" 항목이 ' + numberWithComma(pendingStatus.need_supplier_detail_count) + '건 있습니다. 먼저 처리해 주세요.');
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }
                    if (pendingStatus.need_godo_load_count > 0) {
                        alert('"고도몰 로드 안됨" 항목이 ' + numberWithComma(pendingStatus.need_godo_load_count) + '건 있습니다. 먼저 처리해 주세요.');
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }
                    alert('데이터검수 미완료 항목이 있습니다. 먼저 처리해 주세요.');
                }
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }

            // 2) 고도몰 상품 검수 여부 체크
            if (!hasCompletedGodoInspectionForCurrentItems()) {
                alert('할인생성 전에 [고도몰 상품 검수]를 먼저 진행해 주세요.');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }

            // 3) 고도몰 검수 불합격 항목 체크
            var inspectionMismatchTargets = collectInspectionMismatchTargets();
            if (inspectionMismatchTargets.length > 0) {
                alert('고도몰 상품 검수 결과 "검수 불합격" 항목이 있습니다. 불합격 항목을 먼저 처리해 주세요.\n[고도몰검수 일괄처리] 버튼으로 처리할 수 있습니다.');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }

            // 4) 고도몰 검수 매칭안됨 항목 체크
            var unmatchedGodoCount = countUnmatchedGodoInspectionItems();
            if (unmatchedGodoCount > 0) {
                alert(
                    '고도몰검수 중 매칭안됨건이 발견되었습니다. 매칭안됨 건이 있을 경우 할인생성이 불가능합니다. 매칭안됨은 고도몰 상품이 옵션이 존재하는 상품일 가능성이 매우 높습니다.\n\n해당 상품을 교체해서 제거해 주세요.'
                );
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }

            // 5) 모든 라인이 검수통과 상태인지 최종 확인
            var notPassedGodoCount = countNotPassedGodoInspectionItems();
            if (notPassedGodoCount > 0) {
                alert('모든 상품이 검수통과가 되야 합니다');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }

            var startDateText = String($('#sale_start_date').val() || '').trim();
            var endDateText = String($('#sale_end_date').val() || '').trim();
            if (!startDateText || !endDateText) {
                alert('할인 시작일과 할인 종료일을 입력해주세요.');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }

            var dateRangeValidation = validateSaleCreateDateRange(true);
            if (!dateRangeValidation.valid) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }

            var productPayload = buildSaleHistoryProductPayload();
            if (!productPayload.length) {
                alert('저장할 상품 데이터가 없습니다.');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
            var metaPayload = buildSaleHistoryMetaPayload(productPayload);
            var startDateObj = parseDateInputToDate(startDateText, 'sale_start');
            var endDateObj = parseDateInputToDate(endDateText, 'sale_end');
            var saveStartDateTime = startDateObj ? formatDateTimeValue(startDateObj) : startDateText;
            var saveEndDateTime = endDateObj ? formatDateTimeValue(endDateObj) : endDateText;

            var $btn = $(this);
            $btn.prop('disabled', true).text('저장중...');

            $.ajax({
                url: '/admin/sale/history/save',
                type: 'POST',
                dataType: 'json',
                data: {
                    mode: 'create',
                    sale_status: 'wait',
                    sale_mode: String($('#sale_mode').val() || 'day').trim(),
                    sale_start_date: saveStartDateTime,
                    sale_end_date: saveEndDateTime,
                    product_json: JSON.stringify(productPayload),
                    meta_json: JSON.stringify(metaPayload)
                },
                success: function (response) {
                    if (!response || response.status !== 'success') {
                        showToast((response && response.message) ? response.message : '할인생성 저장에 실패했습니다.', new Date().toLocaleTimeString());
                        return;
                    }
                    showToast((response && response.message) ? response.message : '할인생성 저장 완료', new Date().toLocaleTimeString());
                    setTimeout(function () {
                        location.href = '/admin/sale/history';
                    }, 500);
                },
                error: function (xhr) {
                    var message = '할인생성 저장에 실패했습니다.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(message, new Date().toLocaleTimeString());
                },
                complete: function () {
                    $btn.prop('disabled', false).text('할인생성');
                }
            });

            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });

        $(document).on('change', '#keep_all_checkbox', function () {
            var checked = $(this).is(':checked');
            $('input[name="keep_product_ps_idxs[]"]').prop('checked', checked);
            collectPinnedItemsFromCheckedRows();
            syncKeepAllCheckbox();
        });

        $(document).on('change', 'input[name="keep_product_ps_idxs[]"]', function () {
            collectPinnedItemsFromCheckedRows();
            syncKeepAllCheckbox();
        });

        $(document).on('input change', '.discount-rate-input', function () {
            var $input = $(this);
            var itemKey = String($input.data('item-key') || '');
            if (!itemKey || !currentItemsMap[itemKey]) {
                return;
            }
            discountRateInputMap[itemKey] = normalizeDiscountRate($input.val());
            applyDiscountValuesToRow(itemKey);
        });

        $('#insert_product_btn').on('click', function () {
            var insertMode = String($('#insert_product_mode').val() || 'have').trim();
            if (insertMode !== 'provider') {
                insertMode = 'have';
            }

            var insertCode = String($('#insert_product_code').val() || '').trim();
            if (!insertCode) {
                showToast('삽입할 재고코드/상품코드를 입력해주세요.', new Date().toLocaleTimeString());
                $('#insert_product_code').focus();
                return;
            }

            var existingItemKeys = Object.keys(currentItemsMap);
            var $btn = $(this);
            $btn.prop('disabled', true).text('삽입중...');

            $.ajax({
                url: '/admin/sale/history/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'insert_product_by_code',
                    insert_product_mode: insertMode,
                    insert_product_code: insertCode,
                    existing_item_keys: existingItemKeys
                },
                success: function (response) {
                    if (!response || response.status !== 'success') {
                        showToast((response && response.message) ? response.message : '상품 삽입에 실패했습니다.', new Date().toLocaleTimeString());
                        return;
                    }

                    var responseData = response.data || {};
                    var insertedItem = responseData.item || ((responseData.items && responseData.items[0]) ? responseData.items[0] : null);
                    if (!insertedItem) {
                        showToast('삽입할 상품 데이터를 찾지 못했습니다.', new Date().toLocaleTimeString());
                        return;
                    }

                    var insertedItemKey = String(insertedItem.item_key || insertedItem.ps_idx || '').trim();
                    if (!insertedItemKey) {
                        showToast('삽입 대상 상품 키 정보가 없습니다.', new Date().toLocaleTimeString());
                        return;
                    }

                    if (currentItemsMap[insertedItemKey]) {
                        showToast('이미 리스트에 존재하는 상품입니다.', new Date().toLocaleTimeString());
                        return;
                    }

                    var orderedKeys = getRenderedItemKeysInOrder();
                    var mergedItems = [];
                    var usedKeyMap = {};

                    if (orderedKeys.length > 0) {
                        for (var i = 0; i < orderedKeys.length; i++) {
                            var key = orderedKeys[i];
                            if (!key || !currentItemsMap[key] || usedKeyMap[key]) {
                                continue;
                            }
                            mergedItems.push(currentItemsMap[key]);
                            usedKeyMap[key] = true;
                        }
                    } else {
                        Object.keys(currentItemsMap).forEach(function (key) {
                            if (!key || !currentItemsMap[key] || usedKeyMap[key]) {
                                return;
                            }
                            mergedItems.push(currentItemsMap[key]);
                            usedKeyMap[key] = true;
                        });
                    }

                    mergedItems.push(insertedItem);
                    renderRandomProducts({
                        items: mergedItems,
                        total_count: mergedItems.length,
                        force_exact_list: true,
                        highlight_item_keys: [insertedItemKey],
                        condition: {
                            total_product_qty: mergedItems.length
                        }
                    });

                    $('#insert_product_code').val('').focus();
                    showToast('상품 삽입 완료', new Date().toLocaleTimeString());
                },
                error: function (xhr) {
                    var message = '상품 삽입에 실패했습니다.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(message, new Date().toLocaleTimeString());
                },
                complete: function () {
                    $btn.prop('disabled', false).text('상품삽입');
                }
            });
        });

        $('#insert_product_code').on('keypress', function (e) {
            if (Number(e.which || e.keyCode) === 13) {
                e.preventDefault();
                $('#insert_product_btn').trigger('click');
            }
        });

        $('#load_random_product_btn').on('click', function () {
            var $btn = $(this);
            $btn.prop('disabled', true).text('불러오는 중...');
            setSaveButtonStatus('랜덤 할인상품 불러오는 중...', false);
            collectPinnedItemsFromCheckedRows();
            var excludeBrandIdxs = [];
            $('input[name="exclude_brand_idxs[]"]:checked').each(function () {
                excludeBrandIdxs.push($(this).val());
            });
            var selectedKindCodes = [];
            $('input[name="selected_kind_codes[]"]:checked').each(function () {
                selectedKindCodes.push($(this).val());
            });

            $.ajax({
                url: '/admin/sale/history/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'load_random_product',
                    total_product_qty: $('#total_product_qty').val(),
                    have_product_qty: $('#have_product_qty').val(),
                    provider_product_qty: $('#provider_product_qty').val(),
                    have_product_min_stock: $('#have_product_min_stock').val(),
                    have_product_margin_per: $('#have_product_margin_per').val(),
                    provider_product_margin_per: $('#provider_product_margin_per').val(),
                    sale_duplicate_mode: $('#sale_duplicate_mode').val(),
                    exclude_brand_idxs: excludeBrandIdxs,
                    selected_kind_codes: selectedKindCodes
                },
                success: function (response) {
                    if (!response || response.status !== 'success') {
                        var failMessage = (response && response.message) ? response.message : '요청에 실패했습니다.';
                        setSaveButtonStatus('작업 실패: ' + failMessage, true);
                        alert(failMessage);
                        return;
                    }
                    var responseData = response.data || {};
                    renderRandomProducts(responseData);
                    var items = responseData.items || [];
                    var haveCandidateCount = Number(responseData.have_candidate_count || 0);
                    var providerCandidateCount = Number(responseData.provider_candidate_count || 0);
                    var totalCount = Number(responseData.total_count || 0);
                    if ((haveCandidateCount + providerCandidateCount) <= 0 && totalCount > 0) {
                        haveCandidateCount = totalCount;
                        providerCandidateCount = 0;
                    }
                    var doneMessage = '작업 완료 - 후보 보유상품: ' + numberWithComma(haveCandidateCount) + '건 / 위탁상품: ' + numberWithComma(providerCandidateCount) + '건 / 표시: ' + numberWithComma(items.length) + '건';
                    setSaveButtonStatus(doneMessage, false);
                },
                error: function (xhr) {
                    var message = '요청에 실패했습니다.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    setSaveButtonStatus('작업 실패: ' + message, true);
                    alert(message);
                },
                complete: function () {
                    $btn.prop('disabled', false).text('랜덤 할인상품 불러오기');
                }
            });
        });

        $('#one_touch_random_btn').on('click', function () {
            var $btn = $(this);
            if ($btn.prop('disabled')) {
                return;
            }
            var checkedKeepCount = $('input[name="keep_product_ps_idxs[]"]:checked').length;
            var confirmMessage = '';
            if (checkedKeepCount > 0) {
                confirmMessage = '[원터치 랜덤 할인상품 불러오기]를 하시면 전체가 전부 초기화됩니다. 선택된 상품도 교체 됩니다.\n진행하시겠습니까?';
            } else {
                confirmMessage = '원터치 자동 처리(랜덤불러오기 → 데이터검수/교체 → 고도몰검수/일괄반영)를 실행하시겠습니까?';
            }

            if (!confirm(confirmMessage)) {
                return;
            }

            // 원터치는 항상 처음부터 다시 시작하도록 유지/검수 상태를 초기화한다.
            pinnedItemsMap = {};
            discountRateInputMap = {};
            latestGodoGoodsResult = { stock_codes: [], count: 0, items: [] };
            latestGodoGoodsMap = {};
            latestInspectRequestedKeyMap = {};
            providerDataInspectBulkAttempted = false;
            $('input[name="keep_product_ps_idxs[]"]').prop('checked', false);
            $('#keep_all_checkbox').prop('checked', false).prop('indeterminate', false);
            syncKeepAllCheckbox();

            $btn.prop('disabled', true).text('원터치 처리중...');
            runOneTouchRandomProcess().always(function () {
                $btn.prop('disabled', false).text('원터치 랜덤 할인상품 불러오기');
            });
        });

        $('#refresh_current_btn').on('click', function () {
            if (!Object.keys(currentItemsMap).length) {
                alert('스테이지에 상품이 없습니다.');
                return;
            }

            var havePsIdxs = [];
            var providerIdxs = [];
            Object.keys(currentItemsMap).forEach(function (key) {
                var item = currentItemsMap[key] || {};
                var itemSource = String(item.item_source || 'have');
                var idx = String(item.ps_idx || '').trim();
                if (!idx) {
                    return;
                }
                if (itemSource === 'provider') {
                    providerIdxs.push(idx);
                } else {
                    havePsIdxs.push(idx);
                }
            });

            var $btn = $(this);
            $btn.prop('disabled', true).text('새로고침 중...');

            $.ajax({
                url: '/admin/sale/history/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'refresh_current_product_list',
                    have_ps_idxs: havePsIdxs,
                    provider_idxs: providerIdxs
                },
                success: function (response) {
                    if (!response || response.status !== 'success') {
                        alert((response && response.message) ? response.message : '새로고침에 실패했습니다.');
                        return;
                    }

                    var refreshedData = response.data || {};
                    renderRandomProducts({
                        items: refreshedData.items || [],
                        total_count: Number(refreshedData.total_count || 0),
                        force_exact_list: true,
                        condition: {
                            total_product_qty: Number(refreshedData.selected_count || 0)
                        }
                    });
                    alert((response && response.message) ? response.message : '현재 목록 새로고침 완료');
                },
                error: function (xhr) {
                    var message = '새로고침에 실패했습니다.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                },
                complete: function () {
                    $btn.prop('disabled', false).text('현재 목록 새로고침');
                }
            });
        });

        $('#provider_data_inspect_bulk_btn').on('click', function () {
            var targets = collectPendingProviderDataInspectItems();
            if (!targets.length) {
                showToast('데이터검수 대상 위탁상품이 없습니다.', new Date().toLocaleTimeString());
                return;
            }
            providerDataInspectBulkAttempted = true;

            var $btn = $(this);
            $btn.prop('disabled', true).text('처리중...');
            setSaleHistoryProcessingMask(true, '데이터 처리중입니다.\n총 ' + targets.length + '건 준비중...');

            var index = 0;
            var successCount = 0;
            var failCount = 0;

            var runNext = function () {
                if (index >= targets.length) {
                    setSaleHistoryProcessingMaskMessage(
                        '데이터 처리중입니다.\n완료 단계 진행중... (총 ' + targets.length + '건 / 성공 ' + successCount + '건 / 실패 ' + failCount + '건)'
                    );
                    var havePsIdxs = [];
                    var providerIdxs = [];
                    Object.keys(currentItemsMap).forEach(function (key) {
                        var item = currentItemsMap[key] || {};
                        var itemSource = String(item.item_source || 'have');
                        var idx = String(item.ps_idx || '').trim();
                        if (!idx) {
                            return;
                        }
                        if (itemSource === 'provider') {
                            providerIdxs.push(idx);
                        } else {
                            havePsIdxs.push(idx);
                        }
                    });

                    $.ajax({
                        url: '/admin/sale/history/action',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action_mode: 'refresh_current_product_list',
                            have_ps_idxs: havePsIdxs,
                            provider_idxs: providerIdxs
                        }
                    }).done(function (response) {
                        if (response && response.status === 'success') {
                            var refreshedData = response.data || {};
                            renderRandomProducts({
                                items: refreshedData.items || [],
                                total_count: Number(refreshedData.total_count || 0),
                                force_exact_list: true,
                                condition: {
                                    total_product_qty: Number(refreshedData.selected_count || 0)
                                }
                            });
                        }
                    }).always(function () {
                        setSaleHistoryProcessingMask(false);
                        $btn.prop('disabled', false).text('위탁상품 데이터검수 일괄처리');
                        showToast('위탁상품 데이터검수 처리 완료 (성공 ' + successCount + '건, 실패 ' + failCount + '건)', new Date().toLocaleTimeString());
                    });
                    return;
                }

                var target = targets[index++];
                setSaleHistoryProcessingMaskMessage(
                    '데이터 처리중입니다.\n' + index + '/' + targets.length + ' 진행중... (성공 ' + successCount + '건 / 실패 ' + failCount + '건)'
                );

                var runGodoLoad = function () {
                    if (!target.need_godo_load) {
                        successCount++;
                        setTimeout(runNext, 250);
                        return;
                    }
                    requestLoadGodoGoodsInfo(target)
                        .done(function () {
                            successCount++;
                        })
                        .fail(function () {
                            failCount++;
                        })
                        .always(function () {
                            setTimeout(runNext, 250);
                        });
                };

                if (!target.need_supplier_detail) {
                    runGodoLoad();
                    return;
                }

                requestUpdateSupplierProductDetail(target)
                    .done(function () {
                        runGodoLoad();
                    })
                    .fail(function () {
                        failCount++;
                        // 공급사 크롤링 실패여도 고도몰 로드는 독립적으로 시도
                        runGodoLoad();
                    });
            };

            runNext();
        });

        $(document).on('click', '.exclude-row-btn', function () {
            var itemKey = String($(this).data('item-key') || '').trim();
            var itemSource = String($(this).data('item-source') || 'have').trim();
            var targetIdx = String($(this).data('target-idx') || '').trim();
            if (!targetIdx) {
                showToast('제외할 상품 정보가 없습니다.', new Date().toLocaleTimeString());
                return;
            }

            if (!confirm('해당상품을 할인 대상에서 제외 시키겠습니까?\n앞으로 제외된 상품은 랜덤 할인상품 대상에서 제외 됩니다.\n상품 상세페이지에서 다시 수정이 가능합니다.')) {
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).text('처리중...');

            $.ajax({
                url: '/admin/sale/history/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'exclude_discount_product',
                    item_source: itemSource,
                    ps_idxs: itemSource === 'provider' ? [] : [targetIdx],
                    provider_idxs: itemSource === 'provider' ? [targetIdx] : []
                },
                success: function (response) {
                    if (!response || response.status !== 'success') {
                        showToast((response && response.message) ? response.message : '요청에 실패했습니다.', new Date().toLocaleTimeString());
                        return;
                    }

                    var orderedKeys = getRenderedItemKeysInOrder();
                    var removedIndex = orderedKeys.indexOf(itemKey);
                    if (removedIndex < 0) {
                        removedIndex = orderedKeys.length;
                    }

                    if (itemKey) {
                        delete pinnedItemsMap[itemKey];
                        delete currentItemsMap[itemKey];
                    }

                    var remainItems = [];
                    var existingKeyMap = {};
                    for (var i = 0; i < orderedKeys.length; i++) {
                        var key = orderedKeys[i];
                        if (key === itemKey) {
                            continue;
                        }
                        if (currentItemsMap[key]) {
                            remainItems.push(currentItemsMap[key]);
                            existingKeyMap[key] = true;
                        }
                    }

                    loadReplacementItem(itemSource, existingKeyMap).done(function (replacementItem) {
                        var highlightItemKeys = [];
                        if (replacementItem) {
                            var insertIndex = removedIndex;
                            if (insertIndex < 0) {
                                insertIndex = 0;
                            }
                            if (insertIndex > remainItems.length) {
                                insertIndex = remainItems.length;
                            }
                            remainItems.splice(insertIndex, 0, replacementItem);
                            highlightItemKeys.push(String(replacementItem.item_key || replacementItem.ps_idx || ''));
                        }

                        renderRandomProducts({
                            items: remainItems,
                            total_count: remainItems.length,
                            force_exact_list: true,
                            highlight_item_keys: highlightItemKeys,
                            condition: {
                                total_product_qty: remainItems.length
                            }
                        });

                        showToast('매칭제외 처리 완료', new Date().toLocaleTimeString());
                    });
                },
                error: function (xhr) {
                    var message = '요청에 실패했습니다.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(message, new Date().toLocaleTimeString());
                },
                complete: function () {
                    $btn.prop('disabled', false).text('제외');
                }
            });
        });

        $(document).on('click', '.replace-row-btn', function () {
            var itemKey = String($(this).data('item-key') || '').trim();
            var itemSource = String($(this).data('item-source') || 'have').trim();
            if (!itemKey) {
                showToast('교체할 상품 정보가 없습니다.', new Date().toLocaleTimeString());
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).text('교체중...');

            var orderedKeys = getRenderedItemKeysInOrder();
            var removedIndex = orderedKeys.indexOf(itemKey);
            if (removedIndex < 0) {
                removedIndex = orderedKeys.length;
            }

            var orderedItems = [];
            var existingKeyMap = {};
            for (var i = 0; i < orderedKeys.length; i++) {
                var key = orderedKeys[i];
                if (!currentItemsMap[key]) {
                    continue;
                }
                orderedItems.push(currentItemsMap[key]);
                if (key !== itemKey) {
                    existingKeyMap[key] = true;
                }
            }

            loadReplacementItem(itemSource, existingKeyMap).done(function (replacementItem) {
                if (!replacementItem) {
                    showToast('교체 가능한 상품이 없습니다.', new Date().toLocaleTimeString());
                    return;
                }

                var remainItems = [];
                for (var j = 0; j < orderedItems.length; j++) {
                    var row = orderedItems[j] || {};
                    var rowKey = String(row.item_key || row.ps_idx || '');
                    if (rowKey === itemKey) {
                        continue;
                    }
                    remainItems.push(row);
                }

                var insertIndex = removedIndex;
                if (insertIndex < 0) {
                    insertIndex = 0;
                }
                if (insertIndex > remainItems.length) {
                    insertIndex = remainItems.length;
                }
                remainItems.splice(insertIndex, 0, replacementItem);

                delete pinnedItemsMap[itemKey];

                renderRandomProducts({
                    items: remainItems,
                    total_count: remainItems.length,
                    force_exact_list: true,
                    highlight_item_keys: [String(replacementItem.item_key || replacementItem.ps_idx || '')],
                    condition: {
                        total_product_qty: remainItems.length
                    }
                });

                showToast('상품 교체 완료', new Date().toLocaleTimeString());
            }).always(function () {
                $btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> 교체');
            });
        });

        $(document).on('click', '.remove-row-btn', function () {
            var itemKey = String($(this).data('item-key') || '').trim();
            if (!itemKey) {
                showToast('제거할 상품 정보가 없습니다.', new Date().toLocaleTimeString());
                return;
            }

            if (!currentItemsMap[itemKey]) {
                showToast('이미 제거된 상품입니다.', new Date().toLocaleTimeString());
                return;
            }

            delete currentItemsMap[itemKey];
            delete pinnedItemsMap[itemKey];
            delete discountRateInputMap[itemKey];
            delete latestGodoGoodsMap[itemKey];
            delete latestInspectRequestedKeyMap[itemKey];

            var orderedKeys = getRenderedItemKeysInOrder();
            var remainItems = [];
            var used = {};
            for (var i = 0; i < orderedKeys.length; i++) {
                var key = String(orderedKeys[i] || '').trim();
                if (!key || key === itemKey || used[key] || !currentItemsMap[key]) {
                    continue;
                }
                remainItems.push(currentItemsMap[key]);
                used[key] = true;
            }
            Object.keys(currentItemsMap).forEach(function (key) {
                if (!key || used[key]) {
                    return;
                }
                remainItems.push(currentItemsMap[key]);
                used[key] = true;
            });

            renderRandomProducts({
                items: remainItems,
                total_count: remainItems.length,
                force_exact_list: true,
                condition: {
                    total_product_qty: remainItems.length
                }
            });

            showToast('상품 라인이 제거되었습니다.', new Date().toLocaleTimeString());
        });

        $('#godo_inspect_btn').on('click', function () {
            if (!Object.keys(currentItemsMap).length) {
                showToast('스테이지에 상품이 없습니다.', new Date().toLocaleTimeString());
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).text('검수 조회중...');

            loadGodoGoodsInfoForCurrentRows()
                .done(function (response) {
                    if (response && response.status === 'error') {
                        showToast(response.message || '고도몰 상품 정보 조회에 실패했습니다.', new Date().toLocaleTimeString());
                        return;
                    }
                    renderRandomProducts({
                        items: Object.keys(currentItemsMap).map(function (key) { return currentItemsMap[key]; }),
                        total_count: Object.keys(currentItemsMap).length,
                        condition: {
                            total_product_qty: Object.keys(currentItemsMap).length
                        }
                    });
                    showToast('고도몰 상품 검수 데이터 조회 완료', new Date().toLocaleTimeString());
                })
                .fail(function (xhr) {
                    var message = '고도몰 상품 정보 조회에 실패했습니다.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(message, new Date().toLocaleTimeString());
                })
                .always(function () {
                    $btn.prop('disabled', false).text('고도몰 상품 검수');
                });
        });

        $('#godo_bulk_cost_apply_btn').on('click', function () {
            if (hasPendingProviderDataInspection()) {
                alert('데이터 검수가 완료되지 않은 위탁상품이 존재합니다. 데이터 검수를 완료해주세요.');
                return;
            }

            var targets = collectInspectionMismatchTargets();
            if (!targets.length) {
                alert('고도몰검수 일괄처리 대상이 없습니다.');
                return;
            }

            if (!confirm('판매가 불일치는 우리 DB 판매가를 고도몰 값으로 변경하고, 원가 불일치는 고도몰 원가를 반영합니다.\n보유상품 goodsNo 불일치 건은 cd_godo_code를 API goodsNo로 보정합니다.\n총 ' + numberWithComma(targets.length) + '건 고도몰검수 일괄처리하시겠습니까?')) {
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).text('고도몰검수 일괄처리중...');
            setSaleHistoryProcessingMask(true, '데이터 처리중입니다.\n총 ' + targets.length + '건 준비중...');

            var index = 0;
            var successCount = 0;
            var failCount = 0;

            var runNextBulkApply = function () {
                if (index >= targets.length) {
                    rerenderCurrentStageRows();
                    setSaleHistoryProcessingMask(false);
                    $btn.prop('disabled', false).text('고도몰검수 일괄처리');
                    toggleGodoBulkCostApplyButton();

                    var doneMsg = '고도몰검수 일괄처리 완료 (성공 ' + numberWithComma(successCount) + '건';
                    if (failCount > 0) {
                        doneMsg += ', 실패 ' + numberWithComma(failCount) + '건';
                    }
                    doneMsg += ')';
                    showToast(doneMsg, new Date().toLocaleTimeString());
                    return;
                }

                var target = targets[index++];
                setSaleHistoryProcessingMaskMessage(
                    '데이터 처리중입니다.\n' + index + '/' + targets.length + ' 진행중... (성공 ' + successCount + '건 / 실패 ' + failCount + '건)'
                );

                $.ajax({
                    url: '/admin/sale/history/action',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action_mode: 'update_godo_goods_cost_price',
                        ps_idx: String(target.ps_idx || ''),
                        prd_idx: String(target.prd_idx || ''),
                        goods_no: String(target.goods_no || ''),
                        local_godo_code: String(target.local_godo_code || ''),
                        cost_price: String(target.cost_price || ''),
                        godo_sale_price: String(target.godo_sale_price || ''),
                        cost_mismatch: String(target.cost_mismatch || 'N'),
                        sale_mismatch: String(target.sale_mismatch || 'N'),
                        godo_code_mismatch: String(target.godo_code_mismatch || 'N')
                    }
                }).done(function (response) {
                    if (!response || response.status !== 'success') {
                        failCount++;
                        return;
                    }

                    successCount++;
                    var applied = response.data || {};
                    var itemKey = String(target.item_key || '').trim();
                    if (!itemKey) {
                        var fallbackPsIdx = String(target.ps_idx || '').trim();
                        itemKey = String(target.item_source || '') === 'provider' ? ('provider_' + fallbackPsIdx) : fallbackPsIdx;
                    }
                    if (latestGodoGoodsMap[itemKey] && applied.cost_price_updated === true) {
                        latestGodoGoodsMap[itemKey].costPrice = String(applied.cost_price || '');
                    }
                    if (currentItemsMap[itemKey] && applied.sale_price_updated === true) {
                        currentItemsMap[itemKey].sale_price = parseMoney(applied.godo_sale_price);
                    }
                    if (currentItemsMap[itemKey] && applied.godo_code_updated === true) {
                        currentItemsMap[itemKey].godo_goods_no = String(applied.goods_no || '');
                    }
                }).fail(function () {
                    failCount++;
                }).always(function () {
                    setTimeout(runNextBulkApply, 250);
                });
            };

            runNextBulkApply();
        });

        $(document).on('click', '.apply-godo-cost-btn', function () {
            var $btn = $(this);
            var psIdx = String($btn.data('ps-idx') || '').trim();
            var prdIdx = String($btn.data('prd-idx') || '').trim();
            var goodsNo = String($btn.data('goods-no') || '').trim();
            var localGodoCode = String($btn.data('local-godo-code') || '').trim();
            var costPrice = String($btn.data('cost-price') || '').trim();
            var godoSalePrice = String($btn.data('godo-sale-price') || '').trim();
            var isCostMismatch = String($btn.data('cost-mismatch') || 'N') === 'Y';
            var isSaleMismatch = String($btn.data('sale-mismatch') || 'N') === 'Y';
            var isGodoCodeMismatch = String($btn.data('godo-code-mismatch') || 'N') === 'Y';

            if (!isCostMismatch && !isSaleMismatch && !isGodoCodeMismatch) {
                alert('판매가/원가/goodsNo 불일치 항목이 아닙니다.');
                return;
            }

            if (isSaleMismatch && !prdIdx) {
                alert('상품 IDX가 없어 판매가 반영을 진행할 수 없습니다.');
                return;
            }

            if (isCostMismatch && !goodsNo) {
                alert('고도몰 상품번호가 없어 원가 반영을 진행할 수 없습니다.');
                return;
            }

            if (isGodoCodeMismatch && !goodsNo) {
                alert('고도몰 상품번호가 없어 cd_godo_code 보정을 진행할 수 없습니다.');
                return;
            }

            if (!confirm('판매가 불일치는 우리 DB 판매가를 고도몰 값으로 변경하고, 원가 불일치는 고도몰 원가를 반영합니다.\ngoodsNo 불일치 건은 cd_godo_code를 API goodsNo로 보정합니다.\n반영하시겠습니까?')) {
                return;
            }

            $btn.prop('disabled', true).text('반영중...');

            $.ajax({
                url: '/admin/sale/history/action',
                type: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'update_godo_goods_cost_price',
                    ps_idx: psIdx,
                    prd_idx: prdIdx,
                    goods_no: goodsNo,
                    local_godo_code: localGodoCode,
                    cost_price: costPrice,
                    godo_sale_price: godoSalePrice,
                    cost_mismatch: isCostMismatch ? 'Y' : 'N',
                    sale_mismatch: isSaleMismatch ? 'Y' : 'N',
                    godo_code_mismatch: isGodoCodeMismatch ? 'Y' : 'N'
                },
                success: function (response) {
                    if (!response || response.status !== 'success') {
                        alert((response && response.message) ? response.message : '반영에 실패했습니다.');
                        return;
                    }

                    if (latestGodoGoodsMap[psIdx] && isCostMismatch) {
                        latestGodoGoodsMap[psIdx].costPrice = costPrice;
                    }
                    if (currentItemsMap[psIdx] && isSaleMismatch) {
                        currentItemsMap[psIdx].sale_price = parseMoney(godoSalePrice);
                    }
                    if (currentItemsMap[psIdx] && response.data && response.data.godo_code_updated === true) {
                        currentItemsMap[psIdx].godo_goods_no = String(response.data.goods_no || goodsNo || '');
                    }
                    rerenderCurrentStageRows();
                    alert((response && response.message) ? response.message : '판매가/원가 반영 완료');
                },
                error: function (xhr) {
                    var message = '반영에 실패했습니다.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                },
                complete: function () {
                    $btn.prop('disabled', false).text('판매가/원가 반영');
                    toggleGodoBulkCostApplyButton();
                }
            });
        });

        toggleGodoInspectButton();
    });
</script>