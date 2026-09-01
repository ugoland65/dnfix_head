<div id="contents_head">
	<h1>기간별 판매 조회</h1>
	<h3>기간별 판매 조회입니다.</h3>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

        <!-- 검색 영역 -->
        <div class="top-search-wrap">
            <ul class="count-wrap">
                <span class="count">상품 수 : <b><?= number_format((int)($sales_row_count ?? count($salesDaily ?? []))) ?></b></span>
                <span class="m-l-10 count">총 수량 : <b><?= number_format((int)($sold_qty_total ?? 0)) ?></b></span>
            </ul>
            <ul class="calendar-input">
                <input type='text' name="s_date" id="s_date" value="<?= $s_date ?>">
            </ul>
            <ul>~</ul>
            <ul class="calendar-input">
                <input type='text' name="e_date" id="e_date" value="<?= $e_date ?>">
            </ul>
            <ul>
                <select name="s_kind" id="s_kind" style="height:30px;">
                    <option value="">1차 분류</option>
                    <?php foreach (($prd_kind_name ?? []) as $kindCode => $kindName) { ?>
                        <option value="<?= htmlspecialchars((string)$kindCode, ENT_QUOTES, 'UTF-8') ?>" <?= ((string)($s_kind ?? '') === (string)$kindCode) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string)$kindName, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php } ?>
                </select>
            </ul>
            <ul id="s_kind_second_wrap" style="display:none;">
                <select name="s_kind_second" id="s_kind_second" style="height:30px;">
                    <option value="">2차 분류</option>
                </select>
            </ul>
            <ul id="s_kind_third_wrap" style="display:none;">
                <select name="s_kind_third" id="s_kind_third" style="height:30px;">
                    <option value="">3차 분류</option>
                </select>
            </ul>
            <ul id="s_kind_fourth_wrap" style="display:none;">
                <select name="s_kind_fourth" id="s_kind_fourth" style="height:30px;">
                    <option value="">4차 분류</option>
                </select>
            </ul>
            <ul>
                <button type="button" id="search_btn" class="btnstyle1 btnstyle1-inverse3 btnstyle1-sm" >기간검색</button>
            </ul>
        </div>

		<div id="list_new_wrap">
            <div class="table-wrap5 m-t-5">
                <div class="scroll-wrap">

                    <table class="table-st1">
                        <thead>
                            <tr class="list">
                                <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                                <th>순위</th>
                                <th class="list-idx">고유번호</th>
                                <th class="list-idx">재고코드</th>
                                <th>이미지</th>
                                <th>분류</th>
                                <th>상품명</th>
                                <th>브랜드</th>
                                <th>판매수량</th>
                                <th>현재고</th>
                                <th>마진율</th>
                                <th>현재<br>마진등급</th>
                                <th>최근 입고일</th>
                                <th>최근 판매일</th>
                                <th>최근 할인일</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $rank = 1;
                            foreach ($salesDaily as $row) {

                                $img_path = "";
                                if( $row['cd_img'] ){
                                    $img_path = '/data/comparion/'.$row['cd_img'];
                                }
                        ?>
                            <tr>
                                <td><input type="checkbox" name="check_idx[]" value="<?= $row['ps_idx'] ?>"></td>
                                <td class="text-center"><?= $rank ?></td>
                                <td class="list-idx"><?= $row['ps_idx'] ?></td>
                                <td class="list-idx"><?= $row['prd_idx'] ?></td>
                                <td class="p-5">
                                    <p onclick="onlyAD.prdView('<?=$row['cd_idx']?>','info');" style="cursor:pointer;" ><img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;"></p>
                                </td>
                                <td class="text-center"><?= htmlspecialchars((string)($row['prd_category_path'] ?? $row['prd_kind_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <p onclick="onlyAD.prdView('<?=$row['cd_idx']?>','info');" style="cursor:pointer;" ><b><?=$row['prd_name']?></b></p>
                                    <?php if( !empty($row['cd_memo2']) ){ ?>
                                        <div class="m-t-3" style="color:#ff0000"><span class="prd-memo">- <?=$row['cd_memo2']?></span></div>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?=$row['brand_name']?>
                                    <?php if( !empty($row['brand_name2']) ){ ?>
                                        <br>
                                        <?=$row['brand_name2']?>
                                    <?php } ?>
                                </td>
                                <td class="text-center"><?= $row['sold_qty'] ?></td>
                                <td class="text-center"><?= $row['ps_stock'] ?></td>
                                <td class="text-right"><b><?=$row['margin_per']?>%</b></td>
                                <td class="text-center">
                                    <?php if (!empty($row['margin_grade'])) { ?>
                                        <span class="grade-badge grade-<?=$row['margin_grade']?>">
                                            <?=$row['margin_grade']?>
                                        </span>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                                <td class="text-center"><?= date('Y-m-d', strtotime($row['ps_in_date'])) ?></td>
                                <td class="text-center"><?= date('Y-m-d', strtotime($row['ps_last_date'])) ?></td>
                                <td class="text-center">
                                    <?php
                                        $saleDate = $row['ps_sale_date'] ?? null;
                                        if (
                                            !empty($saleDate) &&
                                            $saleDate !== '0000-00-00 00:00:00' &&
                                            $saleDate !== '0000-00-00' &&
                                            ($ts = strtotime($saleDate)) // strtotime 실패하면 false
                                        ) {
                                    ?>
                                        <div>
                                            <ul class="text-center"><?=date('y.m.d', $ts)?></ul>
                                            <ul class="text-center m-t-5" style="font-size:12px;">총 할인수 : <?=$row['last_sale']['sale_count'] ?? 0?></ul>
                                            <ul class="text-center" style="font-size:11px;"><?=$row['last_sale']['sale_subject'] ?? ''?></ul>
                                            <ul class="text-center"><?=$row['last_sale']['sale_per'] ?? 0?>%</ul>
                                        </div>
                                    <?php
                                        } else {
                                            echo '-';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php
                                $rank++;
                            }
                        ?>
                        </tbody>
                        <!--
                        <tfoot>
                            <tr>
                                <td colspan="8" class="text-right"><b>합계</b></td>
                                <td class="text-center"><b><?= number_format((int)($sold_qty_total ?? 0)) ?></b></td>
                                <td colspan="5"></td>
                            </tr>
                        </tfoot>
                        -->
                    </table>

                </div>
            </div>
        </div>
	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap"><?= $paginationHtml ?? '' ?></div>
</div>

<script>

$(document).ready(function(){
    const categoryTree = <?= json_encode($categories ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const initialSecond = <?= json_encode((string)($s_kind_second ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const initialThird = <?= json_encode((string)($s_kind_third ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const initialFourth = <?= json_encode((string)($s_kind_fourth ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    function getCategoryRows(rows) {
        return Array.isArray(rows) ? rows : [];
    }

    function findCategoryNode(rows, matcher) {
        const categoryRows = getCategoryRows(rows);
        for (let i = 0; i < categoryRows.length; i++) {
            const row = categoryRows[i] || {};
            if (matcher(row)) {
                return row;
            }
            const found = findCategoryNode(row.children, matcher);
            if (found) {
                return found;
            }
        }
        return null;
    }

    function findFirstCategoryByKind(kindCode) {
        const kind = String(kindCode || '').trim();
        if (!kind) {
            return null;
        }
        return findCategoryNode(categoryTree, function(row) {
            return String((row || {}).key || '').trim() === kind;
        });
    }

    function fillCategorySelect($select, rows, placeholder, selectedCode) {
        const categoryRows = getCategoryRows(rows).filter(function(row) {
            return String((row || {}).code || '').trim() !== '';
        });
        $select.empty().append($('<option>', { value: '', text: placeholder }));
        categoryRows.forEach(function(row) {
            const code = String((row || {}).code || '').trim();
            const name = String((row || {}).name || (row || {}).key || code).trim();
            $select.append($('<option>', { value: code, text: name }));
        });
        if (selectedCode && categoryRows.some(function(row) {
            return String((row || {}).code || '').trim() === selectedCode;
        })) {
            $select.val(selectedCode);
        } else {
            $select.val('');
        }
        return categoryRows;
    }

    function renderSecondCategory(resetDeeper) {
        const kindCode = String($('#s_kind').val() || '').trim();
        const firstNode = findFirstCategoryByKind(kindCode);
        const children = firstNode ? getCategoryRows(firstNode.children) : [];
        const filled = fillCategorySelect($('#s_kind_second'), children, '2차 분류', resetDeeper ? '' : initialSecond);
        $('#s_kind_second_wrap').toggle(filled.length > 0);
        if (!filled.length) {
            $('#s_kind_second').val('');
        }
        renderThirdCategory(resetDeeper || !$('#s_kind_second').val());
    }

    function renderThirdCategory(resetDeeper) {
        const secondCode = String($('#s_kind_second').val() || '').trim();
        const secondNode = secondCode
            ? findCategoryNode(categoryTree, function(row) {
                return String((row || {}).code || '').trim() === secondCode;
            })
            : null;
        const children = secondNode ? getCategoryRows(secondNode.children) : [];
        const filled = fillCategorySelect($('#s_kind_third'), children, '3차 분류', resetDeeper ? '' : initialThird);
        $('#s_kind_third_wrap').toggle(filled.length > 0);
        if (!filled.length) {
            $('#s_kind_third').val('');
        }
        renderFourthCategory(resetDeeper || !$('#s_kind_third').val());
    }

    function renderFourthCategory(resetDeeper) {
        const thirdCode = String($('#s_kind_third').val() || '').trim();
        const thirdNode = thirdCode
            ? findCategoryNode(categoryTree, function(row) {
                return String((row || {}).code || '').trim() === thirdCode;
            })
            : null;
        const children = thirdNode ? getCategoryRows(thirdNode.children) : [];
        const filled = fillCategorySelect($('#s_kind_fourth'), children, '4차 분류', resetDeeper ? '' : initialFourth);
        $('#s_kind_fourth_wrap').toggle(filled.length > 0);
        if (!filled.length) {
            $('#s_kind_fourth').val('');
        }
    }

    $('#s_kind').on('change', function() {
        renderSecondCategory(true);
    });
    $('#s_kind_second').on('change', function() {
        renderThirdCategory(true);
    });
    $('#s_kind_third').on('change', function() {
        renderFourthCategory(true);
    });

    renderSecondCategory(false);

    $('#search_btn').click(function(){
        var s_date = $('#s_date').val();
        var e_date = $('#e_date').val();
        var s_kind = $('#s_kind').val();
        var s_kind_second = $('#s_kind_second').val();
        var s_kind_third = $('#s_kind_third').val();
        var s_kind_fourth = $('#s_kind_fourth').val();
        location.href = '/admin/sales/sales_ranking_by_period'
            + '?s_date=' + encodeURIComponent(s_date)
            + '&e_date=' + encodeURIComponent(e_date)
            + '&s_kind=' + encodeURIComponent(s_kind || '')
            + '&s_kind_second=' + encodeURIComponent(s_kind_second || '')
            + '&s_kind_third=' + encodeURIComponent(s_kind_third || '')
            + '&s_kind_fourth=' + encodeURIComponent(s_kind_fourth || '');
    });

});

</script>