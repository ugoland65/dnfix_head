<div style="margin-bottom:10px; display:flex; align-items:center; gap:8px;">
    <span>최근 할인일 :</span>
    <span class="calendar-input">
        <input type="text" id="ps_sale_date" value="<?= $recentSaleLog['ps_sale_date'] ?? '' ?>"  autocomplete="off">
    </span>
    <button type="button" class="btnstyle1 btnstyle1-sm" onclick="saveRecentSaleDate()">저장</button>
</div>

<table class="table-style border01 width-full">
    <tr>
        <th class="text-center">할인모드</th>
        <th class="text-center" style="width:110px">할인일</th>
        <th class="text-center">판매가</th>
        <th class="text-center">원가</th>
        <th class="text-center">마진율</th>
        <th class="text-center">할인율</th>
        <th class="text-center">할인 판매가</th>
        <th class="text-center">할인판매 마진</th>
        <th class="text-center">할인판매 마진율</th>
        <th class="text-center" style="width:150px">실적</th>
    </tr>
    <?php if (!empty($saleLogRows) && is_array($saleLogRows)) { ?>
        <?php foreach ($saleLogRows as $row) { ?>
            <tr>
                <td class="text-center"><?= $row['sale_mode_text'] ?? '' ?></td>
                <td class="text-center"><?= $row['display_day'] ?? '' ?></td>
                <td class="text-right"><?= number_format((int)($row['original_price'] ?? 0)) ?></td>
                <td class="text-right"><?= number_format((int)($row['cost_price'] ?? 0)) ?></td>
                <td class="text-center"><?= $row['margin_pre'] ?? 0 ?>%</td>
                <td class="text-center"><b style="color:#ff0000"><?= $row['sale_per'] ?? 0 ?></b>%</td>
                <td class="text-right"><?= number_format((int)($row['sale_price'] ?? 0)) ?></td>
                <td class="text-right"><?= number_format((int)($row['margin_price'] ?? 0)) ?></td>
                <td class="text-center"><?= $row['margin_per'] ?? 0 ?>%</td>
                <td class="text-center">
                    <div>
                        <?php if (!empty($row['qty_day_text'])) { ?>
                            <ul><?= $row['qty_day_text'] ?></ul>
                        <?php } ?>
                        <?php if ((int)($row['qty_sum'] ?? 0) > 0) { ?>
                            <ul class="m-t-5">판매 : <b style="color:#ff0000"><?= (int)($row['qty_sum'] ?? 0) ?></b>건</ul>
                            <ul class="m-t-5">판매가 : <span style="color:#ff0000"><?= number_format((int)($row['sale_amount'] ?? 0)) ?></span></ul>
                            <ul class="m-t-5">수익 : <span style="color:#ff0000"><?= number_format((int)($row['profit_amount'] ?? 0)) ?></span></ul>
                        <?php } else { ?>
                            <ul class="m-t-5">판매없음</ul>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="10" class="text-center" style="padding:30px;">
                할인 이력이 없습니다.
            </td>
        </tr>
    <?php } ?>
</table>

<script>
    $(function() {
        if ($(".calendar-input input").length) {
            $(".calendar-input input").datepicker(clareCalendar);
        }
    });

    function saveRecentSaleDate() {
        const psSaleDate = String($('#ps_sale_date').val() || '').trim();
        const prdIdx = "<?= (int)($prd_idx ?? 0) ?>";
        const prdMode = "<?= htmlspecialchars((string)($prd_mode ?? 'prdDB'), ENT_QUOTES, 'UTF-8') ?>";

        if (!psSaleDate) {
            alert('최근 할인일을 입력해주세요.');
            return;
        }

        fetch('/admin/product/detail_sale_log/save', {
            method: 'POST',
            body: new URLSearchParams({
                prd_idx: prdIdx,
                prd_mode: prdMode,
                ps_sale_date: psSaleDate
            })
        })
        .then(async (response) => {
            const data = await response.json();
            if (!response.ok || data.success !== true) {
                throw new Error(data.message || '저장 실패');
            }
            alert(data.message || '저장되었습니다.');
        })
        .catch((error) => {
            alert(error.message || '저장 중 오류가 발생했습니다.');
        });
    }
</script>
