<div id="contents_head">
    <h1>상품 할인 목록</h1>
    <div class="head-btn-wrap">
        <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="location.href='/admin/sale/history/create'">
            <i class="fas fa-plus"></i> 할인 생성
        </button>
    </div>
</div>
<div id="contents_body">
    <div id="contents_body_wrap">

        <div class="top-search-wrap">
            <ul class="count-wrap">
                <span class="count">Total : <b><?= number_format($pagination['total'] ?? 0) ?></b></span>
                <span class="m-l-10"><b><?= $pagination['current_page'] ?? 1 ?></b></span>
                <span>/</span>
                <span><b><?= $pagination['last_page'] ?? 1 ?></b> page</span>
            </ul>
            <ul class="m-l-10">
                <select name="s_sale_status" id="s_sale_status">
                    <option value="all" <? if (($s_sale_status ?? '') == '') echo "selected"; ?>>전체 상태</option>
                    <option value="wait" <? if (($s_sale_status ?? '') == 'wait') echo "selected"; ?>>대기</option>
                    <option value="start" <? if (($s_sale_status ?? '') == 'start') echo "selected"; ?>>진행</option>
                    <option value="end" <? if (($s_sale_status ?? '') == 'end') echo "selected"; ?>>종료</option>
                </select>
            </ul>
            <ul>
                <select name="s_sale_mode" id="s_sale_mode">
                    <option value="all" <? if (($s_sale_mode ?? '') == '') echo "selected"; ?>>전체 모드</option>
                    <option value="day" <? if (($s_sale_mode ?? '') == 'day') echo "selected"; ?>>일일할인</option>
                    <option value="period" <? if (($s_sale_mode ?? '') == 'period') echo "selected"; ?>>기간할인</option>
                    <option value="week" <? if (($s_sale_mode ?? '') == 'week') echo "selected"; ?>>주간할인</option>
                    <option value="month" <? if (($s_sale_mode ?? '') == 'month') echo "selected"; ?>>월간할인</option>
                    <option value="event" <? if (($s_sale_mode ?? '') == 'event') echo "selected"; ?>>기획전</option>
                </select>
            </ul>
            <ul>
                <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm" id="searchBtn">
                    <i class="fas fa-search"></i> 검색
                </button>
            </ul>
        </div>

        <div id="list_new_wrap">
            <div class="table-wrap5 m-t-5">
                <div class="scroll-wrap">
                    <table class="table-st1">
                        <thead>
                            <tr>
                                <th>번호</th>
                                <th>상태</th>
                                <th>모드</th>
                                <th>할인기간</th>
                                <th>상품수</th>
                                <th>등록자</th>
                                <th>등록일</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($saleHistoryList)) { ?>
                                <?php foreach ($saleHistoryList as $row) { ?>
                                    <tr>
                                        <td class="text-center"><?= $row['seq'] ?? '' ?></td>
                                        <td class="text-center"><?= $row['sale_status_text'] ?? '-' ?></td>
                                        <td class="text-center"><?= $row['sale_mode_text'] ?? '-' ?></td>
                                        <td class="text-center"><?= $row['sale_period_text'] ?? '-' ?></td>
                                        <td class="text-center"><?= number_format((int)($row['product_count'] ?? 0)) ?></td>
                                        <td class="text-center"><?= $row['created_by_name'] ?? '-' ?></td>
                                        <td class="text-center"><?= $row['created_at_text'] ?? '-' ?></td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btn btnstyle1 btnstyle1-success btnstyle1-sm"
                                                onclick="location.href='/admin/sale/history/detail/<?= (int)($row['seq'] ?? 0) ?>'"
                                            >
                                                상세
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="8" class="text-center" style="padding:30px;">조회된 할인 이력이 없습니다.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show">
        <?= $paginationHtml ?? '' ?>
    </div>
</div>

<script>
    function getSearchParams(additionalParams) {
        var params = {};
        var fields = {
            's_sale_status': $("#s_sale_status").val(),
            's_sale_mode': $("#s_sale_mode").val()
        };

        if (additionalParams) {
            fields = Object.assign(fields, additionalParams);
        }

        for (var key in fields) {
            if (fields[key] !== undefined && fields[key] !== null && fields[key] !== '' && fields[key] !== 'all') {
                params[key] = fields[key];
            }
        }

        return params;
    }

    function navigateWithParams(params) {
        var queryString = Object.keys(params)
            .map(function(key) {
                return key + '=' + encodeURIComponent(params[key]);
            })
            .join('&');

        location.href = '/admin/sale/history' + (queryString ? '?' + queryString : '');
    }

    $(function(){
        $("#searchBtn").on('click', function() {
            var params = getSearchParams();
            navigateWithParams(params);
        });

        $("#s_sale_status, #s_sale_mode").change(function() {
            var params = getSearchParams();
            navigateWithParams(params);
        });
    });
</script>
