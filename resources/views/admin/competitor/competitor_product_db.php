<div id="contents_head">
	<h1>경쟁사 상품DB</h1>
    <h3>경쟁사 사이트에서 크롤링(수집)된 상품입니다.</h3>
    <div class="m-l-10">
<!--
        <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="competitorProductSyncBtn">상품 동기화</button>
-->
    </div>
    <div id="head_write_btn">
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

        <div class="supplier-site-selector-wrap">
            <div class="supplier-site-selector-title">경쟁사 사이트 선택</div>
            <input type="hidden" name="s_site" id="s_site" value="<?= htmlspecialchars((string)($site ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <div class="supplier-site-selector">
                <?php foreach($competitor_data as $key => $value){ ?>
                    <button
                        type="button"
                        class="supplier-site-btn <?=$site == $key ? 'active' : ''?>"
                        data-site="<?= htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8') ?>">
                        <?=$value['name']?> (<?=$value['code']?>)
                    </button>
                <?php } ?>
            </div>
        </div>

		<div id="list_new_wrap">

            <div class="table-top">
				<ul class="total">
					Total : <b><?=number_format($pagination_total)?></b>
				</ul>
                <ul>
					<select name="s_status" id="s_status" >
						<option value="" >판매상태</option>
                        <option value="판매중" <?=$s_status == '판매중' ? 'selected' : ''?>>판매중</option>
                        <option value="일시품절" <?=$s_status == '일시품절' ? 'selected' : ''?>>일시품절</option>
                        <option value="품절" <?=$s_status == '품절' ? 'selected' : ''?>>품절</option>
                        <option value="판매중단" <?=$s_status == '판매중단' ? 'selected' : ''?>>판매중단</option>
                        <option value="수집실패" <?=$s_status == '수집실패' ? 'selected' : ''?>>수집실패</option>
					</select>
				</ul>
                <ul>
					<select name="s_match_status" id="s_match_status" >
						<option value="all_match" <?=$s_match_status == 'all_match' ? 'selected' : ''?>>전체매칭</option>
                        <option value="matched" <?=$s_match_status == 'matched' ? 'selected' : ''?>>매칭완료</option>
                        <option value="unmatched" <?=$s_match_status == 'unmatched' ? 'selected' : ''?>>매칭안됨</option>
                        <option value="match_excluded" <?=$s_match_status == 'match_excluded' ? 'selected' : ''?>>매칭제외</option>
					</select>
				</ul>
                <ul class="m-l-10">
					<select name="s_keyword_mode" id="s_keyword_mode" >
                        <option value="name" <?=$s_keyword_mode == 'name' ? 'selected' : ''?>>상품명</option>
                        <option value="category" <?=$s_keyword_mode == 'category' ? 'selected' : ''?>>카테고리 명</option>
                        <option value="brand_name" <?=$s_keyword_mode == 'brand_name' ? 'selected' : ''?>>브랜드명</option>
					</select>
				</ul>
                <ul>
					<input type="text" name="s_keyword" id="s_keyword" placeholder="검색어" value="<?= $s_keyword ?? '' ?>">
				</ul>
                <ul>
					<button type="button" id="searchBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm"  > 
						<i class="fas fa-search"></i> 검색
					</button>
				</ul>
                <ul>
					<button type="button" id="resetBtn" class="btnstyle1 btnstyle1-sm"  > 
						<i class="fas fa-undo"></i> 초기화
					</button>
				</ul>
                <ul class="right">
                    목록수 :
                    <select name="s_limit" id="s_limit">
                        <option value="100" <?= (int)($s_limit ?? 100) === 100 ? 'selected' : '' ?>>100개씩</option>
                        <option value="200" <?= (int)($s_limit ?? 100) === 200 ? 'selected' : '' ?>>200개씩</option>
                        <option value="300" <?= (int)($s_limit ?? 100) === 300 ? 'selected' : '' ?>>300개씩</option>
                        <option value="500" <?= (int)($s_limit ?? 100) === 500 ? 'selected' : '' ?>>500개씩</option>
                    </select>
                    &nbsp;&nbsp;
                    정렬 : 
                    <select name="s_sort_mode" id="s_sort_mode" >
                        <option value="code" <?=$s_sort_mode == 'code' ? 'selected' : ''?>>상품코드순</option>
                        <option value="updated_at" <?=$s_sort_mode == 'updated_at' ? 'selected' : ''?>>수정일</option>
                        <option value="created_at" <?=$s_sort_mode == 'created_at' ? 'selected' : ''?>>등록일</option>
                        <option value="last_price_changed_at" <?=$s_sort_mode == 'last_price_changed_at' ? 'selected' : ''?>>판매가<br>변경일</option>
                        <option value="last_status_changed_at" <?=$s_sort_mode == 'last_status_changed_at' ? 'selected' : ''?>>판매상태<br>변경일</option>
                        <option value="last_changed_at" <?=$s_sort_mode == 'last_changed_at' ? 'selected' : ''?>>정보<br>변경일</option>
                    </select>
                </ul>
            </div> 

            <div class="table-wrap5 m-t-5">
                <div class="scroll-wrap">

                    <table class="table-st1">
                        <thead>
                        <tr>
                            <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                            <th class="">공급사<br>사이트</th>
                            <th class="">판매상태</th>
                            <th class="list-idx">사이트<br>고유번호</th>
                            <th class="">이미지</th>
                            <th class="">사이트<br>카테고리</th>
                            <th style="width:100px; min-width:100px; max-width:100px;">브랜드</th>
                            <th class="" style="width:300px;">상품명</th>
                            
                            <th>판매가</th>
                            <th>수정일<br>등록일</th>
                            <th>판매가<br>변경일</th>
                            <th>판매상태<br>변경일</th>
                            <th>정보<br>변경일</th>

                            <th>매칭</th>
                            <th>매칭상품</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                            foreach ( $CompetitorProductApiData['data']['competitorProducts'] ?? [] as $row ){
                        ?>
                        <tr id="trid_<?=$row['idx']?>" >
                            <td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$row['idx']?>" ></td>	
                            <td class=""><?=$row['site']?></td>
                            <td class="text-center"><?=$row['sale_status'] ?? ''?></td>
                            <td class="list-idx">
                                <div style="font-size: 12px;">
                                    #<?= $row['prd_pk'] ?>
                                </div>
                                <?php
                                /*
                                <div class="m-t-3">
                                    <button type="button" class="btnstyle1 btnstyle1-xs"
                                        onclick="goSupplierProduct('<?= $row['site'] ?>', '<?= $row['prd_pk'] ?>');">공급사 사이트</button>
                                </div>
                                */ ?>
                            </td>
                            <td >
                                <img src="<?=$row['image_url']?>" style="height:70px; border:1px solid #eee !important;">
                            </td>
                            <td class="text-left"><?=$row['category'] ?? ''?></td>
                            <td class="text-center" style="width:100px; min-width:100px; max-width:100px; white-space: normal !important;"><?=$row['brand_name']?></td>
                            <td class="text-left" style="white-space: normal !important;">
                                <b><?=$row['name']?></b>
                            </td>



                            <td class="text-right"><b><?=number_format($row['price'])?></b></td>

                            <td class="text-center">
                                <?=date('Y.m.d H:i', strtotime($row['updated_at']))?><br>
                                <?=date('Y.m.d H:i', strtotime($row['created_at']))?>
                            </td>
                            <td class="text-center">
                                <?php if( $row['last_price_changed_at'] ): ?>
                                    <?=date('Y-m-d', strtotime($row['last_price_changed_at']))?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if( ($row['sale_status'] ?? '') === '품절' ): ?>
                                    <span style="color:#dc2626; font-weight:700;">품절</span><br><br>
                                <?php endif; ?>
                                <?php if( $row['last_status_changed_at'] ): ?>
                                    <?=date('Y-m-d', strtotime($row['last_status_changed_at']))?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if( $row['last_changed_at'] ): ?>
                                    <?=date('Y-m-d', strtotime($row['last_changed_at']))?>
                                <?php endif; ?>
                            </td>
                            <td class="text-left">

                            <?php if( empty($row['match_idx']) ){ ?>
                                <button
                                    type="button"
                                    class="btnstyle1 btnstyle1-info btnstyle1-sm competitor-product-match-btn"
                                    data-competitor-idx="<?= (int)($row['idx'] ?? 0) ?>"
                                    data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>"
                                    data-competitor-image="<?= htmlspecialchars((string)($row['image_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-competitor-name="<?= htmlspecialchars((string)($row['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    매칭
                                </button>
                            <?php }else{ ?>
                                <span class="text-green">매칭완료</span>
                            <?php } ?>

                            </td>

                            <td class="text-left">
                                <?php
                                    $matchedIdx = (int)($row['match_idx'] ?? 0);
                                    $matchedProduct = $matchedProductMap[$matchedIdx] ?? null;
                                ?>
                                <?php if( !empty($matchedProduct) ): ?>
                                    <div style="display:flex; align-items:flex-start; gap:10px;">
                                        <div style="width:56px; min-width:56px;">
                                            <?php if( !empty($matchedProduct['img_path']) ){ ?>
                                                <img src="<?= htmlspecialchars((string)($matchedProduct['img_path'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:56px; height:56px; object-fit:cover; border:1px solid #eee !important;">
                                            <?php } ?>
                                        </div>
                                        <div style="flex:1; min-width:0;">
                                            <div><b>#<?=$matchedProduct['CD_IDX'] ?? ''?></b></div>
                                            <div class="m-t-3" style="font-size:12px; white-space:normal;">
                                                <?= htmlspecialchars((string)($matchedProduct['CD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="m-t-3" style="font-size:12px; color:#6b7280;">
                                                브랜드: <?= htmlspecialchars((string)($matchedProduct['brand_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="m-t-3" style="font-size:12px;">
                                                판매가: <b><?= number_format((int)($matchedProduct['cd_sale_price'] ?? 0)) ?></b>
                                            </div>
                                            <div class="m-t-3">
                                                <button type="button" class="btnstyle1 btnstyle1-xs"
                                                    onclick="onlyAD.prdView('<?= (int)($matchedProduct['CD_IDX'] ?? 0) ?>','info');">매칭된 상품보기</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>

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
	<div class="pageing-wrap"><?=$paginationHtml ?? ''?></div>
    <div class="m-l-20">
        선택된 상품 <span id="selected_product_count">0</span>
        <!--
        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" id="supplierProductRegBtn">공급사상품 등록대기로 등록</button>
        -->
    </div>
</div>
<style>
    .match-layer-overlay {
        display: none;
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(17, 24, 39, 0.4);
        z-index: 90000001;
    }
    .match-layer-overlay.active {
        display: block;
    }
    .match-layer-box {
        width: 920px;
        max-width: calc(100% - 40px);
        max-height: calc(100% - 60px);
        overflow: hidden;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 20px 48px rgba(15, 23, 42, 0.3);
        margin: 30px auto;
        display: flex;
        flex-direction: column;
    }
    .match-layer-head {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .match-layer-target {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .match-layer-target-thumb {
        width: 48px;
        height: 48px;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        object-fit: cover;
        background: #f3f4f6;
        flex: 0 0 48px;
    }
    .match-layer-body {
        padding: 14px 16px;
        overflow: auto;
    }
    .match-layer-search {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
    }
    .match-layer-search input[type="text"] {
        flex: 1;
        min-width: 200px;
    }
    .match-result-table {
        width: 100%;
        border-collapse: collapse;
    }
    .match-result-table th,
    .match-result-table td {
        border: 1px solid #e5e7eb;
        padding: 8px;
        vertical-align: middle;
    }
    .match-result-table th {
        background: #f8fafc;
    }
    .match-result-table tr.is-selected {
        background: #ecfdf3;
    }
    .match-thumbnail {
        width: 58px;
        height: 58px;
        border: 1px solid #e5e7eb;
        object-fit: cover;
        background: #f3f4f6;
    }
    .match-layer-foot {
        padding: 10px 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
<div id="competitorProductMatchLayer" class="match-layer-overlay" aria-hidden="true">
    <div class="match-layer-box">
        <div class="match-layer-head">
            <div class="match-layer-target">
                <img id="match_layer_competitor_image" class="match-layer-target-thumb" src="" alt="매칭 대상 상품 이미지" style="display:none;">
                <div>
                    <b>상품 매칭</b>
                    <div id="match_layer_competitor_name" style="margin-top:3px; color:#4b5563; font-size:12px;"></div>
                </div>
            </div>
            <button type="button" class="btnstyle1 btnstyle1-sm" id="matchLayerCloseBtn">닫기</button>
        </div>
        <div class="match-layer-body">
            <input type="hidden" id="match_competitor_idx" value="">
            <div class="match-layer-search">
                <input type="text" id="match_keyword" placeholder="상품명(CD_NAME) 검색">
                <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="matchSearchBtn">검색</button>
            </div>
            <div id="match_result_status" style="margin-bottom:8px; color:#6b7280; font-size:12px;">검색어를 입력하고 검색해주세요.</div>
            <table class="match-result-table">
                <colgroup>
                    <col width="90px">
                    <col width="120px">
                    <col width="*">
                    <col width="180px">
                    <col width="90px">
                </colgroup>
                <thead>
                    <tr>
                        <th>썸네일</th>
                        <th>CD_IDX</th>
                        <th>상품명</th>
                        <th>브랜드</th>
                        <th>선택</th>
                    </tr>
                </thead>
                <tbody id="match_result_list">
                    <tr>
                        <td colspan="5" class="text-center">검색 결과가 없습니다.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="match-layer-foot">
            <div id="match_selected_info">선택된 상품 없음</div>
            <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="matchSelectDoneBtn">선택완료</button>
        </div>
    </div>
</div>

<script>

function select_all() {
    var checkboxes = document.getElementsByName('key_check[]');
    var selectAll = event.target.checked;

    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = selectAll;
        if (selectAll) {
            $(checkboxes[i]).closest('tr').addClass('selected-row');
        } else {
            $(checkboxes[i]).closest('tr').removeClass('selected-row');
        }
    }
    updateSelectedCount();
}

$(function(){
    var $matchLayer = $("#competitorProductMatchLayer");
    var selectedMatchProduct = null;
    var currentMatchTarget = null;
    var isMatchProcessing = false;
    var MATCH_SCROLL_STORAGE_KEY = 'competitor_product_db_scroll_top';

    function saveScrollWrapPosition() {
        try {
            var $scrollWrap = $('.scroll-wrap').first();
            if (!$scrollWrap.length) {
                return;
            }
            sessionStorage.setItem(MATCH_SCROLL_STORAGE_KEY, String($scrollWrap.scrollTop() || 0));
        } catch (e) {}
    }

    function restoreScrollWrapPosition() {
        try {
            var savedTop = sessionStorage.getItem(MATCH_SCROLL_STORAGE_KEY);
            if (savedTop === null) {
                return;
            }
            var $scrollWrap = $('.scroll-wrap').first();
            if (!$scrollWrap.length) {
                return;
            }
            var scrollTop = parseInt(savedTop, 10);
            if (!isNaN(scrollTop) && scrollTop > 0) {
                $scrollWrap.scrollTop(scrollTop);
            }
            sessionStorage.removeItem(MATCH_SCROLL_STORAGE_KEY);
        } catch (e) {}
    }

    restoreScrollWrapPosition();

    $(document).on('click', '.supplier-site-btn', function() {
        var site = String($(this).data('site') || '');
        $('#s_site').val(site);
        $('.supplier-site-btn').removeClass('active');
        $(this).addClass('active');
        $("#searchBtn").trigger('click');
    });

    function escapeHtml(text) {
        return $("<div>").text(String(text || "")).html();
    }

    function closeMatchLayer() {
        $matchLayer.removeClass("active").attr("aria-hidden", "true");
    }

    function setSelectedMatchProduct(item) {
        selectedMatchProduct = item || null;
        if (!selectedMatchProduct) {
            $("#match_selected_info").html("선택된 상품 없음");
            return;
        }
        $("#match_selected_info").html(
            "선택됨: <b>#" + escapeHtml(selectedMatchProduct.cd_idx) + "</b> " + escapeHtml(selectedMatchProduct.cd_name)
        );
    }

    function renderMatchResults(items) {
        var $list = $("#match_result_list");
        if (!items || !items.length) {
            $list.html('<tr><td colspan="5" class="text-center">검색 결과가 없습니다.</td></tr>');
            return;
        }

        var html = "";
        for (var i = 0; i < items.length; i++) {
            var item = items[i] || {};
            var thumb = item.thumbnail_url
                ? '<img src="' + escapeHtml(item.thumbnail_url) + '" class="match-thumbnail">'
                : '<div class="match-thumbnail" style="display:flex;align-items:center;justify-content:center;font-size:11px;color:#9ca3af;">NO IMG</div>';
            html += ''
                + '<tr class="match-result-row" data-cd-idx="' + escapeHtml(item.cd_idx) + '" data-cd-name="' + escapeHtml(item.cd_name) + '" data-brand-name="' + escapeHtml(item.brand_name) + '">'
                + '  <td class="text-center">' + thumb + '</td>'
                + '  <td class="text-center"><b>#' + escapeHtml(item.cd_idx) + '</b></td>'
                + '  <td>' + escapeHtml(item.cd_name) + '</td>'
                + '  <td>' + escapeHtml(item.brand_name || '-') + '</td>'
                + '  <td class="text-center"><button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs match-select-btn">선택</button></td>'
                + '</tr>';
        }
        $list.html(html);
    }

    function submitCompetitorMatch(selectedProduct) {
        if (isMatchProcessing) {
            return;
        }
        if (!currentMatchTarget || !currentMatchTarget.site || !currentMatchTarget.prd_pk) {
            alert("매칭 대상 상품 정보가 없습니다.");
            return;
        }
        if (!selectedProduct || !selectedProduct.cd_idx) {
            alert("매칭할 상품을 선택해주세요.");
            return;
        }

        isMatchProcessing = true;
        $("#match_result_status").text("매칭 처리 중...");

        ajaxRequest('/admin/competitor/match', {
            site: currentMatchTarget.site,
            prd_pk: currentMatchTarget.prd_pk,
            match_idx: selectedProduct.cd_idx
        })
            .done(function(res) {
                if (!(res && (res.success || res.status === 'success'))) {
                    alert(res && res.message ? res.message : '매칭 처리에 실패했습니다.');
                    return;
                }
                if (typeof toast2 === 'function') {
                    toast2('success', '경쟁사 매칭', '매칭이 성공적으로 완료되었습니다.');
                }
                $("#match_result_status").text("매칭 처리 완료");
                closeMatchLayer();
                saveScrollWrapPosition();
                location.reload();
            })
            .fail(function(res) {
                alert(res && res.message ? res.message : '매칭 처리 중 오류가 발생했습니다.');
            })
            .always(function() {
                isMatchProcessing = false;
            });
    }

    function searchMatchProducts() {
        var keyword = $.trim($("#match_keyword").val() || "");
        if (!keyword) {
            alert("검색어를 입력해주세요.");
            $("#match_keyword").focus();
            return;
        }

        $("#match_result_status").text("검색 중...");
        ajaxRequest('/admin/competitor/search_product', {
            keyword: keyword,
            limit: 50
        })
            .done(function(res) {
                var items = (res && res.data && res.data.items) ? res.data.items : [];
                renderMatchResults(items);
                $("#match_result_status").text("검색 결과 " + items.length + "건");
            })
            .fail(function(res) {
                $("#match_result_status").text("검색 실패");
                alert(res && res.message ? res.message : "검색 중 오류가 발생했습니다.");
            });
    }

    // 개별 체크박스 선택 시 행 배경색 변경
    $(document).on('change', 'input[name="key_check[]"]', function() {
        if ($(this).is(':checked')) {
            $(this).closest('tr').addClass('selected-row');
        } else {
            $(this).closest('tr').removeClass('selected-row');
        }
        updateSelectedCount();
    });

    // 선택된 상품 등록
    $("#supplierProductRegBtn").on('click', function() {
        var selectedItems = [];
        $('input[name="key_check[]"]:checked').each(function() {
            selectedItems.push($(this).val());
        });

        if (selectedItems.length === 0) {
            alert('등록할 상품을 선택해주세요.');
            return;
        }

        if (!confirm(selectedItems.length + '개 상품을 등록대기로 처리할까요?\n선택한 상품이 이미 공급사 상품으로 등록되있는지 확인을 꼼꼼하게 해주세요.')) {
            return;
        }

        var payload = {
            action_mode: 'product_standby_register',
            partner_idx: '<?= $supplier_code_data[$site]['idx'] ?? '' ?>',
            pks: selectedItems
        };

        ajaxRequest('/admin/provider_product/action', payload)
            .done(function(res) {
                if (res && (res.success || res.status === 'success')) {
                    alert(res.message || '등록대기 처리되었습니다.');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '처리 실패');
                }
            })
            .fail(function(res) {
                alert(res && res.message ? res.message : '에러');
            });
    });

    $(document).on("click", ".competitor-product-match-btn", function() {
        var $btn = $(this);
        var competitorIdx = Number($btn.data("competitor-idx") || 0);
        var competitorName = String($btn.data("competitor-name") || "").trim();
        var competitorSite = String($btn.data("competitor-site") || "").trim();
        var competitorPrdPk = Number($btn.data("competitor-prd-pk") || 0);
        var competitorImage = String($btn.data("competitor-image") || "").trim();

        $("#match_competitor_idx").val(competitorIdx);
        $("#match_layer_competitor_name").text(competitorName);
        if (competitorImage) {
            $("#match_layer_competitor_image").attr("src", competitorImage).show();
        } else {
            $("#match_layer_competitor_image").attr("src", "").hide();
        }
        $("#match_keyword").val(competitorName);
        setSelectedMatchProduct(null);
        currentMatchTarget = {
            site: competitorSite,
            prd_pk: competitorPrdPk,
            name: competitorName
        };
        $matchLayer.addClass("active").attr("aria-hidden", "false");

        searchMatchProducts();
        $("#match_keyword").focus().select();
    });

    $("#matchLayerCloseBtn").on("click", function() {
        closeMatchLayer();
    });

    $("#matchSearchBtn").on("click", function() {
        searchMatchProducts();
    });

    $("#match_keyword").on("keydown", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            searchMatchProducts();
        }
    });

    $(document).on("click", ".match-select-btn", function() {
        var $row = $(this).closest(".match-result-row");
        $(".match-result-row").removeClass("is-selected");
        $row.addClass("is-selected");

        setSelectedMatchProduct({
            cd_idx: Number($row.data("cd-idx") || 0),
            cd_name: String($row.data("cd-name") || ""),
            brand_name: String($row.data("brand-name") || "")
        });

        // 선택 즉시 매칭 처리
        submitCompetitorMatch(selectedMatchProduct);
    });

    $("#matchSelectDoneBtn").on("click", function() {
        submitCompetitorMatch(selectedMatchProduct);
    });

    $(document).on("keydown", function(e) {
        if (e.key === "Escape" && $matchLayer.hasClass("active")) {
            closeMatchLayer();
        }
    });

    updateSelectedCount();

    $("#searchBtn").on('click',function(){

        let s_site = $("#s_site").val();

        if( !s_site ){
            alert('공급사 사이트를 선택해주세요.');
            return false;
        }

        // 검색 파라미터 수집
        var params = {};

        // URL에서 viewMode 파라미터 가져오기
        var urlParams = new URLSearchParams(window.location.search);

        // 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
        var fields = {
            's_site': $("#s_site").val(),
            's_match_status': $("#s_match_status").val(),
            's_keyword_mode': $("#s_keyword_mode").val(),
            's_keyword': $("#s_keyword").val(),
            's_status': $("#s_status").val(),
            's_limit': $("#s_limit").val(),
            's_sort_mode': $("#s_sort_mode").val(),
        };

        // 유효한 값만 params에 추가
        for (var key in fields) {
            if (fields[key] !== undefined && fields[key] !== null && fields[key] !== '') {
                params[key] = fields[key];
            }
        }

        // URL 쿼리 문자열 생성
        var queryString = Object.keys(params)
            .map(function(key) {
                return key + '=' + encodeURIComponent(params[key]);
            })
            .join('&');

        // 페이지 이동
        location.href = '/admin/competitor/competitor_product_db' + (queryString ? '?' + queryString : '');
    });

    $("#resetBtn").on('click',function(){

        let s_site = $("#s_site").val();
        let s_match_status = $("#s_match_status").val();

        location.href = '/admin/competitor/competitor_product_db?s_site=' + s_site + '&s_match_status=' + s_match_status + '&s_limit=100';
    });

    $("#s_limit").on('change', function() {
        $("#searchBtn").trigger('click');
    });

    $("#s_sort_mode").on('change', function() {
        $("#searchBtn").trigger('click');
    });



});

function updateSelectedCount() {
    var count = $('input[name="key_check[]"]:checked').length;
    $("#selected_product_count").text(count);
}
</script>   