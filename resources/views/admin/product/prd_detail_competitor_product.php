<div class="table-wrap5">
    <table class="table-st1">
        <thead>
        <tr>
            <th style="width:80px;">사이트</th>
            <th style="width:120px;">판매상태</th>
            <th style="width:70px;">이미지</th>
            <th>상품명</th>
            <th style="width:110px;">판매가</th>
            <th style="width:130px;">수정일</th>
            <th style="width:110px;">사이트 PK</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($rows)) { ?>
            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td class="text-center"><?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-center"><?= htmlspecialchars((string)($row['sale_status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-center">
                        <?php if (!empty($row['image_url'])) { ?>
                            <img src="<?= htmlspecialchars((string)$row['image_url'], ENT_QUOTES, 'UTF-8') ?>" style="width:56px; height:56px; object-fit:cover; border:1px solid #eee;">
                        <?php } ?>
                    </td>
                    <td style="white-space:normal;"><?= htmlspecialchars((string)($row['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-right"><b><?= number_format((int)($row['price'] ?? 0)) ?></b></td>
                    <td class="text-center">
                        <?php if (!empty($row['updated_at'])) { ?>
                            <?= date('Y.m.d H:i', strtotime((string)$row['updated_at'])) ?>
                        <?php } ?>
                    </td>
                    <td class="text-center">#<?= (int)($row['prd_pk'] ?? 0) ?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="7" class="text-center">매칭된 경쟁사 판매 데이터가 없습니다.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
