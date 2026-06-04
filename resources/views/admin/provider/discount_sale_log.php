<div style="margin-bottom:10px; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
    <span>최근 할인일 :</span>
    <span class="calendar-input">
        <input type="text" id="last_sale_date" value="<?= htmlspecialchars((string)($recentSaleLog['last_sale_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
    </span>
    <button type="button" class="btnstyle1 btnstyle1-sm" onclick="saveProviderRecentSaleDate()">저장</button>
    <?php if (!empty($providerName)) { ?>
        <span style="font-size:12px; color:#6b7280;">상품명 : <b><?= htmlspecialchars((string)$providerName, ENT_QUOTES, 'UTF-8') ?></b></span>
    <?php } ?>
</div>

<table class="table-style border01 width-full">
    <tr>
        <th class="text-center">할인모드</th>
        <th class="text-center" style="width:120px;">할인일</th>
        <th class="text-center">판매가</th>
        <th class="text-center">원가</th>
        <th class="text-center">마진율</th>
        <th class="text-center">할인율</th>
        <th class="text-center">할인 판매가</th>
        <th class="text-center">할인판매 마진</th>
        <th class="text-center">할인판매 마진율</th>
        <th class="text-center" style="width:120px;">등록일</th>
        <th class="text-center" style="width:90px;">이력번호</th>
    </tr>
    <?php if (!empty($saleLogRows) && is_array($saleLogRows)) { ?>
        <?php foreach ($saleLogRows as $row) { ?>
            <tr>
                <td class="text-center"><?= htmlspecialchars((string)($row['sale_mode_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-center"><?= $row['display_day'] ?? '' ?></td>
                <td class="text-right"><?= number_format((int)($row['original_price'] ?? 0)) ?></td>
                <td class="text-right"><?= number_format((int)($row['cost_price'] ?? 0)) ?></td>
                <td class="text-center"><?= htmlspecialchars((string)($row['margin_pre'] ?? 0), ENT_QUOTES, 'UTF-8') ?>%</td>
                <td class="text-center"><b style="color:#ff0000"><?= htmlspecialchars((string)($row['sale_per'] ?? 0), ENT_QUOTES, 'UTF-8') ?></b>%</td>
                <td class="text-right"><?= number_format((int)($row['sale_price'] ?? 0)) ?></td>
                <td class="text-right"><?= number_format((int)($row['margin_price'] ?? 0)) ?></td>
                <td class="text-center"><?= htmlspecialchars((string)($row['margin_per'] ?? 0), ENT_QUOTES, 'UTF-8') ?>%</td>
                <td class="text-center"><?= htmlspecialchars((string)($row['reg_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-center">#<?= (int)($row['sale_history_seq'] ?? 0) ?></td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="11" class="text-center" style="padding:30px;">
                할인 이력이 없습니다.
            </td>
        </tr>
    <?php } ?>
</table>

<script>
    $(function () {
        if ($(".calendar-input input").length) {
            $(".calendar-input input").datepicker(clareCalendar);
        }
    });

    function saveProviderRecentSaleDate() {
        const lastSaleDate = String($('#last_sale_date').val() || '').trim();
        const prdIdx = "<?= (int)($prd_idx ?? 0) ?>";

        if (!lastSaleDate) {
            alert('최근 할인일을 입력해주세요.');
            return;
        }

        fetch('/admin/provider_product/action', {
            method: 'POST',
            body: new URLSearchParams({
                action_mode: 'update_recent_sale_date',
                prd_idx: prdIdx,
                last_sale_date: lastSaleDate
            })
        })
        .then(async (response) => {
            const data = await response.json();
            if (!response.ok || data.status !== 'success') {
                throw new Error(data.message || '저장 실패');
            }
            alert(data.message || '저장되었습니다.');
        })
        .catch((error) => {
            alert(error.message || '저장 중 오류가 발생했습니다.');
        });
    }
</script>
