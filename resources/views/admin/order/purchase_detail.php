<div id="contents_head">
    <h1>구매대행 발주서 상세</h1>
    <h3>발주 상품 상세 목록</h3>
    <div class="right">
        <button type="button" class="btnstyle1 btnstyle1-sm" onclick="location.href='/admin/order/purchase/list'">리스트로</button>
        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="location.href='/admin/order/godo_order_purchase/excel?purchase_order_idx=<?= (int)($purchaseOrder['idx'] ?? 0) ?>'">엑셀 다운로드</button>
        <button type="button" id="purchase-delete-btn" class="btnstyle1 btnstyle1-danger btnstyle1-sm" data-idx="<?= (int)($purchaseOrder['idx'] ?? 0) ?>">발주서 삭제</button>
    </div>
</div>

<div id="contents_body">
    <div id="contents_body_wrap">
        <table class="table-style border01 width-full">
            <tr>
                <th style="width:140px;">발주서 번호</th>
                <td><?= (int)($purchaseOrder['idx'] ?? 0) ?></td>
                <th style="width:140px;">PO CODE</th>
                <td><?= htmlspecialchars((string)($purchaseOrder['po_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th>발주서명</th>
                <td><?= htmlspecialchars((string)($purchaseOrder['order_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <th>공급사</th>
                <td><?= htmlspecialchars((string)($purchaseOrder['supplier_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <th>주문수</th>
                <td><?= number_format((int)($summary['order_count'] ?? 0)) ?> 건</td>
                <th>총수량 / 총금액</th>
                <td><?= number_format((int)($summary['total_quantity'] ?? 0)) ?> / <?= number_format((float)($summary['total_amount'] ?? 0), 2) ?> 원</td>
            </tr>
        </table>

        <div class="m-t-10 table-wrap5">
            <div class="scroll-wrap">
                <table class="table-st1">
                    <thead>
                        <tr class="list">
                            <th class="list-idx">번호</th>
                            <th>주문번호</th>
                            <th>주문상품번호</th>
                            <th>상품이미지</th>
                            <th>상품명</th>
                            <th>옵션</th>
                            <th>수량</th>
                            <th>단가</th>
                            <th>합계</th>
                            <th>수령자</th>
                            <th>연락처</th>
                            <th>주소</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($purchaseOrderItems)) { ?>
                            <?php foreach ($purchaseOrderItems as $item) { ?>
                                <tr>
                                    <td class="text-center"><?= (int)($item['idx'] ?? 0) ?></td>
                                    <td class="text-center"><?= htmlspecialchars((string)($item['order_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-center"><?= htmlspecialchars((string)($item['order_goods_sno'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-center">
                                        <?php $thumbImageUrl = trim((string)($item['thumb_image_url'] ?? '')); ?>
                                        <?php if ($thumbImageUrl !== '') { ?>
                                            <img src="<?= htmlspecialchars($thumbImageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="상품이미지" style="width:48px; height:48px; object-fit:cover;">
                                        <?php } else { ?>
                                            -
                                        <?php } ?>
                                    </td>
                                    <td><?= htmlspecialchars((string)($item['goods_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($item['option_info_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-right"><?= number_format((int)($item['goods_count'] ?? 0)) ?></td>
                                    <td class="text-right"><?= number_format((float)($item['goods_price'] ?? 0), 2) ?></td>
                                    <td class="text-right"><?= number_format((float)($item['goods_total_price'] ?? 0), 2) ?></td>
                                    <td class="text-center"><?= htmlspecialchars((string)($item['receiver_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-center"><?= htmlspecialchars((string)($item['receiver_cell_phone'] ?? ($item['receiver_phone'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?= htmlspecialchars((string)($item['receiver_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                        <?php if (!empty($item['receiver_address_sub'])) { ?>
                                            <?= ' ' . htmlspecialchars((string)$item['receiver_address_sub'], ENT_QUOTES, 'UTF-8') ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="12" class="text-center">주문상품 데이터가 없습니다.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="contents_bottom">
</div>

<script>
    $(document).on('click', '#purchase-delete-btn', function() {
        var purchaseOrderIdx = parseInt($(this).data('idx'), 10) || 0;
        if (purchaseOrderIdx < 1) {
            alert('삭제할 발주서 번호를 확인할 수 없습니다.');
            return;
        }

        var confirmed = confirm(
            '정말 삭제하시겠습니까? 삭제하면 다시 복구되지 않습니다.\n'
            + '삭제된 상품은 다시 담기가 가능합니다.'
        );
        if (!confirmed) {
            return;
        }

        var $button = $(this);
        $button.prop('disabled', true);

        $.ajax({
            url: '/admin/order/purchase/delete',
            type: 'POST',
            dataType: 'json',
            data: {
                idx: purchaseOrderIdx
            },
            success: function(res) {
                if (!res || res.success !== true) {
                    alert((res && res.message) ? res.message : '발주서 삭제에 실패했습니다.');
                    return;
                }
                alert((res.message || '발주서가 삭제되었습니다.'));
                location.href = '/admin/order/purchase/list';
            },
            error: function(request) {
                alert((request && request.responseText) ? request.responseText : '발주서 삭제 중 오류가 발생했습니다.');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
</script>