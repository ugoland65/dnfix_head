<?php
$unit = $unit ?? [];
$inspectionRecords = $inspectionRecords ?? [];
$checkedTotalQty = (int)($checkedTotalQty ?? 0);
$remainingQty = (int)($remainingQty ?? 0);
?>
<section class="admobile-unit-inspection">
    <div class="admobile-unit-heading">
        <a href="/admobile/order/sheet/stock?idx=<?= (int)($orderIdx ?? 0) ?>" aria-label="입고 수량검수 목록으로 돌아가기">‹</a>
        <h2>개별 수량검수</h2>
    </div>

    <article class="admobile-unit-card">
        <div class="admobile-unit-image">
            <?php if (!empty($unit['product_image'])) { ?>
                <img src="<?= htmlspecialchars((string)$unit['product_image'], ENT_QUOTES, 'UTF-8') ?>" alt="">
            <?php } else { ?>
                <span>이미지 없음</span>
            <?php } ?>
        </div>
        <?php if (!empty($unit['brand_name'])) { ?>
            <p class="admobile-unit-brand"><?= htmlspecialchars((string)$unit['brand_name'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php } ?>
        <h3><?= htmlspecialchars((string)($unit['product_name'] ?: '상품명 없음'), ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="admobile-unit-barcode"><?= htmlspecialchars((string)($unit['barcode'] ?: '-'), ENT_QUOTES, 'UTF-8') ?></p>
        <dl>
            <div>
                <dt>주문수량</dt>
                <dd><?= number_format((int)($unit['order_qty'] ?? 0)) ?>개</dd>
            </div>
            <div>
                <dt>누적 검수수량</dt>
                <dd><?= number_format($checkedTotalQty) ?>개</dd>
            </div>
            <div>
                <dt>남은 수량</dt>
                <dd class="<?= $remainingQty < 0 ? 'is-over' : '' ?>"><?= number_format($remainingQty) ?>개</dd>
            </div>
        </dl>
    </article>

    <section class="admobile-unit-memo">
        <div class="admobile-unit-memo__heading">
            <div>
                <h3>검수 메모</h3>
                <p>입고 시 확인할 내용을 공유합니다.</p>
            </div>
            <button
                type="button"
                data-memo-open
                data-order-idx="<?= (int)($orderIdx ?? 0) ?>"
                data-bidx="<?= (int)($unit['bidx'] ?? 0) ?>"
                data-pidx="<?= (int)($unit['pidx'] ?? 0) ?>"
                data-product-name="<?= htmlspecialchars((string)($unit['product_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                data-memo="<?= htmlspecialchars((string)($unit['stock_inspection_memo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            ><?= !empty($unit['stock_inspection_memo']) ? '수정' : '작성' ?></button>
        </div>
        <p class="admobile-unit-memo__content<?= empty($unit['stock_inspection_memo']) ? ' is-empty' : '' ?>" data-memo-preview><?= !empty($unit['stock_inspection_memo']) ? nl2br(htmlspecialchars((string)$unit['stock_inspection_memo'], ENT_QUOTES, 'UTF-8')) : '등록된 메모가 없습니다.' ?></p>
    </section>

    <form id="unit-inspection-form" class="admobile-unit-form">
        <input type="hidden" name="action_mode" value="inspection_add">
        <input type="hidden" name="idx" value="<?= (int)($orderIdx ?? 0) ?>">
        <input type="hidden" name="bidx" value="<?= (int)($unit['bidx'] ?? 0) ?>">
        <input type="hidden" name="pidx" value="<?= (int)($unit['pidx'] ?? 0) ?>">
        <label for="checked_qty">이번에 센 수량</label>
        <input id="checked_qty" name="checked_qty" type="number" min="1" inputmode="numeric" placeholder="이번에 센 수량 입력">
        <button type="submit">검수수량 추가 등록</button>
        <p id="unit-inspection-message" aria-live="polite"></p>
    </form>

    <section class="admobile-inspection-history">
        <h3>검수 등록 이력</h3>
        <?php if (empty($inspectionRecords)) { ?>
            <p>아직 등록된 검수수량이 없습니다.</p>
        <?php } ?>
        <?php foreach ($inspectionRecords as $record) { ?>
            <article class="admobile-inspection-record">
                <div>
                    <strong><?= number_format((int)($record['checked_qty'] ?? 0)) ?>개</strong>
                    <span><?= htmlspecialchars((string)($record['inspector_admin_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                    <time><?= htmlspecialchars((string)($record['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></time>
                    <?php if (!empty($record['inspection_memo'])) { ?>
                        <p class="admobile-inspection-record__memo" data-record-memo-preview><?= nl2br(htmlspecialchars((string)$record['inspection_memo'], ENT_QUOTES, 'UTF-8')) ?></p>
                    <?php } ?>
                </div>
                <?php if (!empty($record['is_owner'])) { ?>
                    <div class="admobile-inspection-record__actions">
                        <button type="button" data-action="edit" data-inspection-idx="<?= (int)($record['idx'] ?? 0) ?>" data-checked-qty="<?= (int)($record['checked_qty'] ?? 0) ?>">수정</button>
                        <button
                            type="button"
                            data-record-memo-open
                            data-inspection-idx="<?= (int)($record['idx'] ?? 0) ?>"
                            data-checked-qty="<?= (int)($record['checked_qty'] ?? 0) ?>"
                            data-memo="<?= htmlspecialchars((string)($record['inspection_memo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        ><?= !empty($record['inspection_memo']) ? '메모 수정' : '메모' ?></button>
                        <button type="button" data-action="delete" data-inspection-idx="<?= (int)($record['idx'] ?? 0) ?>">삭제</button>
                    </div>
                <?php } ?>
            </article>
        <?php } ?>
    </section>
</section>

<div id="inspection-memo-modal" class="admobile-unit-memo-modal" hidden>
    <div class="admobile-unit-memo-modal__backdrop" data-action="close"></div>
    <section class="admobile-unit-memo-modal__content" role="dialog" aria-modal="true" aria-labelledby="inspection-memo-title">
        <form id="inspection-memo-form">
            <input type="hidden" name="action_mode" value="inspection_memo_save">
            <input type="hidden" name="idx">
            <input type="hidden" name="bidx">
            <input type="hidden" name="pidx">
            <div class="admobile-unit-memo-modal__heading">
                <div>
                    <p>입고 수량검수</p>
                    <h2 id="inspection-memo-title">상품 메모</h2>
                </div>
                <button type="button" data-action="close" aria-label="메모 닫기">×</button>
            </div>
            <p id="inspection-memo-product" class="admobile-unit-memo-modal__product"></p>
            <label for="inspection-memo-text">검수 시 확인할 내용을 기록하세요</label>
            <textarea id="inspection-memo-text" name="memo" maxlength="1000" placeholder="예) 박스 훼손 여부 확인, 입고 시 유의사항"></textarea>
            <div class="admobile-unit-memo-modal__meta">
                <span>다른 관리자도 이 메모를 확인할 수 있습니다.</span>
                <strong><span id="inspection-memo-count">0</span>/1000</strong>
            </div>
            <p id="inspection-memo-message" class="admobile-unit-memo-modal__message" aria-live="polite"></p>
            <div class="admobile-unit-memo-modal__actions">
                <button type="button" class="admobile-unit-memo-modal__cancel" data-action="close">취소</button>
                <button type="submit" class="admobile-unit-memo-modal__save">저장</button>
            </div>
        </form>
    </section>
</div>

<div id="inspection-record-memo-modal" class="admobile-record-memo-modal" hidden>
    <div class="admobile-record-memo-modal__backdrop" data-action="close"></div>
    <section class="admobile-record-memo-modal__content" role="dialog" aria-modal="true" aria-labelledby="inspection-record-memo-title">
        <form id="inspection-record-memo-form">
            <input type="hidden" name="action_mode" value="inspection_record_memo_save">
            <input type="hidden" name="inspection_idx">
            <div class="admobile-record-memo-modal__heading">
                <div>
                    <p>검수 등록 이력</p>
                    <h2 id="inspection-record-memo-title">이력 메모</h2>
                </div>
                <button type="button" data-action="close" aria-label="메모 닫기">×</button>
            </div>
            <p id="inspection-record-memo-quantity" class="admobile-record-memo-modal__quantity"></p>
            <label for="inspection-record-memo-text">이 수량을 검수하면서 확인한 내용을 남기세요</label>
            <textarea id="inspection-record-memo-text" name="memo" maxlength="1000" placeholder="예) 외박스 일부 훼손, 3개 추가 확인 필요"></textarea>
            <div class="admobile-record-memo-modal__meta">
                <span>본인이 등록한 이력의 메모만 수정할 수 있습니다.</span>
                <strong><span id="inspection-record-memo-count">0</span>/1000</strong>
            </div>
            <p id="inspection-record-memo-message" class="admobile-record-memo-modal__message" aria-live="polite"></p>
            <div class="admobile-record-memo-modal__actions">
                <button type="button" class="admobile-record-memo-modal__cancel" data-action="close">취소</button>
                <button type="submit" class="admobile-record-memo-modal__save">저장</button>
            </div>
        </form>
    </section>
</div>

<style>
    .admobile-unit-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .admobile-unit-heading a { color: #344054; font-size: 30px; line-height: 24px; text-decoration: none; }
    .admobile-unit-heading h2 { margin: 0; font-size: 18px; }
    .admobile-unit-card { padding: 16px; border: 1px solid #e3e7ee; border-radius: 12px; background: #fff; text-align: center; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
    .admobile-unit-image { display: flex; align-items: center; justify-content: center; width: 180px; height: 180px; margin: 0 auto 14px; overflow: hidden; border-radius: 10px; background: #f8fafc; color: #98a2b3; font-size: 12px; }
    .admobile-unit-image img { width: 100%; height: 100%; object-fit: cover; }
    .admobile-unit-brand { margin: 0 0 4px; color: #667085; font-size: 13px; }
    .admobile-unit-card h3 { margin: 0; color: #172033; font-size: 19px; line-height: 1.45; }
    .admobile-unit-barcode { margin: 11px 0 16px; color: #172033; font-family: monospace; font-size: 18px; font-weight: 800; letter-spacing: 1px; word-break: break-all; }
    .admobile-unit-card dl { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin: 0; }
    .admobile-unit-card dl > div { padding: 12px; border-radius: 8px; background: #f7f9fc; }
    .admobile-unit-card dt { margin-bottom: 4px; color: #667085; font-size: 12px; }
    .admobile-unit-card dd { margin: 0; color: #172033; font-size: 20px; font-weight: 800; }
    .admobile-unit-card dl > div:first-child dd { color: #2450a6; }
    .admobile-unit-card .is-over { color: #b42318; }
    .admobile-unit-memo { margin-top: 14px; padding: 14px; border: 1px solid #d9e1ef; border-radius: 10px; background: #fff; }
    .admobile-unit-memo__heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .admobile-unit-memo__heading h3 { margin: 0; color: #172033; font-size: 15px; }
    .admobile-unit-memo__heading p { margin: 3px 0 0; color: #667085; font-size: 11px; }
    .admobile-unit-memo__heading button { padding: 7px 11px; border: 1px solid #aab9d6; border-radius: 6px; background: #f7f9ff; color: #2c4d92; font: inherit; font-size: 12px; font-weight: 700; }
    .admobile-unit-memo__content { margin: 12px 0 0; padding: 10px; border-left: 3px solid #3056a8; border-radius: 0 6px 6px 0; background: #f7f9fc; color: #344054; font-size: 13px; line-height: 1.5; white-space: pre-line; }
    .admobile-unit-memo__content.is-empty { border-left-color: #cfd6e1; color: #98a2b3; }
    .admobile-unit-form { display: grid; gap: 8px; margin-top: 14px; padding: 15px; border: 1px solid #cbd6ee; border-radius: 10px; background: #f7f9ff; }
    .admobile-unit-form label { color: #2c4d92; font-size: 13px; font-weight: 700; }
    .admobile-unit-form input { padding: 12px; border: 1px solid #aab9d6; border-radius: 7px; background: #fff; color: #172033; font-size: 20px; font-weight: 800; }
    .admobile-unit-form button { padding: 12px; border: 0; border-radius: 7px; background: #3056a8; color: #fff; font: inherit; font-size: 14px; font-weight: 700; }
    .admobile-unit-form p { min-height: 16px; margin: 0; color: #067647; font-size: 12px; text-align: center; }
    .admobile-inspection-history { margin-top: 14px; padding: 15px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; }
    .admobile-inspection-history h3 { margin: 0 0 10px; font-size: 15px; }
    .admobile-inspection-history > p { margin: 0; color: #667085; font-size: 13px; }
    .admobile-inspection-record { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 11px 0; border-top: 1px solid #eef1f5; }
    .admobile-inspection-record:first-of-type { border-top: 0; }
    .admobile-inspection-record strong { display: inline-block; margin-right: 7px; color: #2450a6; font-size: 17px; }
    .admobile-inspection-record span { color: #344054; font-size: 13px; }
    .admobile-inspection-record time { display: block; margin-top: 3px; color: #98a2b3; font-size: 11px; }
    .admobile-inspection-record__memo { margin: 7px 0 0; padding: 7px 8px; border-radius: 5px; background: #f7f9fc; color: #475467; font-size: 12px; line-height: 1.45; white-space: pre-line; }
    .admobile-inspection-record__actions { display: flex; gap: 5px; }
    .admobile-inspection-record__actions button { padding: 5px 7px; border: 1px solid #cfd6e1; border-radius: 5px; background: #fff; color: #475467; font-size: 11px; }
    .admobile-inspection-record__actions button[data-action="delete"] { border-color: #f5c2c7; color: #b42318; }
    .admobile-unit-memo-modal[hidden] { display: none; }
    .admobile-unit-memo-modal { position: fixed; z-index: 1200; inset: 0; display: flex; align-items: flex-end; }
    .admobile-unit-memo-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .58); }
    .admobile-unit-memo-modal__content { position: relative; width: 100%; padding: 20px 16px calc(16px + env(safe-area-inset-bottom)); border-radius: 18px 18px 0 0; background: #fff; box-shadow: 0 -8px 24px rgba(16, 24, 40, .16); }
    .admobile-unit-memo-modal__heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .admobile-unit-memo-modal__heading p { margin: 0 0 3px; color: #667085; font-size: 12px; font-weight: 700; }
    .admobile-unit-memo-modal__heading h2 { margin: 0; color: #172033; font-size: 19px; }
    .admobile-unit-memo-modal__heading button { width: 34px; height: 34px; border: 0; border-radius: 50%; background: #f2f4f7; color: #344054; font-size: 23px; line-height: 1; }
    .admobile-unit-memo-modal__product { overflow: hidden; margin: 14px 0 12px; color: #475467; font-size: 13px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-unit-memo-modal label { display: block; margin-bottom: 7px; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-unit-memo-modal textarea { display: block; width: 100%; min-height: 136px; padding: 12px; border: 1px solid #aab9d6; border-radius: 9px; color: #172033; font: inherit; font-size: 15px; line-height: 1.5; resize: vertical; outline-color: #3056a8; }
    .admobile-unit-memo-modal__meta { display: flex; justify-content: space-between; gap: 12px; margin-top: 7px; color: #667085; font-size: 11px; }
    .admobile-unit-memo-modal__meta strong { color: #475467; font-variant-numeric: tabular-nums; }
    .admobile-unit-memo-modal__message { min-height: 16px; margin: 8px 0 0; color: #b42318; font-size: 12px; }
    .admobile-unit-memo-modal__actions { display: grid; grid-template-columns: 88px 1fr; gap: 8px; margin-top: 10px; }
    .admobile-unit-memo-modal__actions button { min-height: 46px; border: 0; border-radius: 8px; font: inherit; font-size: 14px; font-weight: 700; }
    .admobile-unit-memo-modal__cancel { background: #eef1f5; color: #475467; }
    .admobile-unit-memo-modal__save { background: #3056a8; color: #fff; }
    .admobile-record-memo-modal[hidden] { display: none; }
    .admobile-record-memo-modal { position: fixed; z-index: 1210; inset: 0; display: flex; align-items: flex-end; }
    .admobile-record-memo-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .58); }
    .admobile-record-memo-modal__content { position: relative; width: 100%; padding: 20px 16px calc(16px + env(safe-area-inset-bottom)); border-radius: 18px 18px 0 0; background: #fff; box-shadow: 0 -8px 24px rgba(16, 24, 40, .16); }
    .admobile-record-memo-modal__heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .admobile-record-memo-modal__heading p { margin: 0 0 3px; color: #667085; font-size: 12px; font-weight: 700; }
    .admobile-record-memo-modal__heading h2 { margin: 0; color: #172033; font-size: 19px; }
    .admobile-record-memo-modal__heading button { width: 34px; height: 34px; border: 0; border-radius: 50%; background: #f2f4f7; color: #344054; font-size: 23px; line-height: 1; }
    .admobile-record-memo-modal__quantity { margin: 14px 0 12px; color: #2450a6; font-size: 14px; font-weight: 800; }
    .admobile-record-memo-modal label { display: block; margin-bottom: 7px; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-record-memo-modal textarea { display: block; width: 100%; min-height: 136px; padding: 12px; border: 1px solid #aab9d6; border-radius: 9px; color: #172033; font: inherit; font-size: 15px; line-height: 1.5; resize: vertical; outline-color: #3056a8; }
    .admobile-record-memo-modal__meta { display: flex; justify-content: space-between; gap: 12px; margin-top: 7px; color: #667085; font-size: 11px; }
    .admobile-record-memo-modal__meta strong { color: #475467; font-variant-numeric: tabular-nums; }
    .admobile-record-memo-modal__message { min-height: 16px; margin: 8px 0 0; color: #b42318; font-size: 12px; }
    .admobile-record-memo-modal__actions { display: grid; grid-template-columns: 88px 1fr; gap: 8px; margin-top: 10px; }
    .admobile-record-memo-modal__actions button { min-height: 46px; border: 0; border-radius: 8px; font: inherit; font-size: 14px; font-weight: 700; }
    .admobile-record-memo-modal__cancel { background: #eef1f5; color: #475467; }
    .admobile-record-memo-modal__save { background: #3056a8; color: #fff; }
</style>

<script>
    (function() {
        var modal = document.getElementById('inspection-record-memo-modal');
        var form = document.getElementById('inspection-record-memo-form');
        var textarea = document.getElementById('inspection-record-memo-text');
        var count = document.getElementById('inspection-record-memo-count');
        var message = document.getElementById('inspection-record-memo-message');
        var quantity = document.getElementById('inspection-record-memo-quantity');
        var activeButton = null;

        function closeModal() {
            modal.hidden = true;
            activeButton = null;
        }

        function updateCount() {
            count.textContent = textarea.value.length;
        }

        document.querySelectorAll('[data-record-memo-open]').forEach(function(button) {
            button.addEventListener('click', function() {
                activeButton = button;
                form.inspection_idx.value = button.dataset.inspectionIdx;
                textarea.value = button.dataset.memo || '';
                quantity.textContent = (button.dataset.checkedQty || '0') + '개 검수 이력';
                message.textContent = '';
                updateCount();
                modal.hidden = false;
                window.setTimeout(function() { textarea.focus(); }, 100);
            });
        });

        textarea.addEventListener('input', updateCount);

        modal.addEventListener('click', function(event) {
            if (event.target.closest('[data-action="close"]')) {
                closeModal();
            }
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            var saveButton = form.querySelector('[type="submit"]');
            saveButton.disabled = true;
            message.textContent = '';

            fetch('/admobile/order/sheet/action', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams(new FormData(form))
            })
                .then(function(response) { return response.json(); })
                .then(function(result) {
                    if (!result.success) {
                        throw new Error(result.message || '메모 저장에 실패했습니다.');
                    }

                    var memo = result.memo || '';
                    activeButton.dataset.memo = memo;
                    activeButton.textContent = memo ? '메모 수정' : '메모';
                    var record = activeButton.closest('.admobile-inspection-record');
                    var preview = record.querySelector('[data-record-memo-preview]');

                    if (memo && !preview) {
                        preview = document.createElement('p');
                        preview.className = 'admobile-inspection-record__memo';
                        preview.setAttribute('data-record-memo-preview', '');
                        record.firstElementChild.appendChild(preview);
                    }
                    if (preview) {
                        preview.textContent = memo;
                        if (!memo) {
                            preview.remove();
                        }
                    }
                    closeModal();
                })
                .catch(function(error) {
                    message.textContent = error.message;
                })
                .finally(function() {
                    saveButton.disabled = false;
                });
        });
    })();
</script>

<script>
    (function() {
        var modal = document.getElementById('inspection-memo-modal');
        var form = document.getElementById('inspection-memo-form');
        var textarea = document.getElementById('inspection-memo-text');
        var count = document.getElementById('inspection-memo-count');
        var message = document.getElementById('inspection-memo-message');
        var productName = document.getElementById('inspection-memo-product');
        var openButton = document.querySelector('[data-memo-open]');
        var preview = document.querySelector('[data-memo-preview]');

        function closeModal() {
            modal.hidden = true;
        }

        function updateCount() {
            count.textContent = textarea.value.length;
        }

        openButton.addEventListener('click', function() {
            form.idx.value = openButton.dataset.orderIdx;
            form.bidx.value = openButton.dataset.bidx;
            form.pidx.value = openButton.dataset.pidx;
            textarea.value = openButton.dataset.memo || '';
            productName.textContent = openButton.dataset.productName || '상품 메모';
            message.textContent = '';
            updateCount();
            modal.hidden = false;
            window.setTimeout(function() { textarea.focus(); }, 100);
        });

        textarea.addEventListener('input', updateCount);

        modal.addEventListener('click', function(event) {
            if (event.target.closest('[data-action="close"]')) {
                closeModal();
            }
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            var saveButton = form.querySelector('[type="submit"]');
            saveButton.disabled = true;
            message.textContent = '';

            fetch('/admobile/order/sheet/action', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams(new FormData(form))
            })
                .then(function(response) { return response.json(); })
                .then(function(result) {
                    if (!result.success) {
                        throw new Error(result.message || '메모 저장에 실패했습니다.');
                    }

                    var memo = result.memo || '';
                    openButton.dataset.memo = memo;
                    openButton.textContent = memo ? '수정' : '작성';
                    preview.textContent = memo || '등록된 메모가 없습니다.';
                    preview.classList.toggle('is-empty', !memo);
                    closeModal();
                })
                .catch(function(error) {
                    message.textContent = error.message;
                })
                .finally(function() {
                    saveButton.disabled = false;
                });
        });
    })();
</script>

<script>
    document.getElementById('unit-inspection-form').addEventListener('submit', function(event) {
        event.preventDefault();
        var form = event.currentTarget;
        var button = form.querySelector('button');
        var message = document.getElementById('unit-inspection-message');
        button.disabled = true;
        message.textContent = '';

        fetch('/admobile/order/sheet/action', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams(new FormData(form))
        })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (!result.success) {
                    throw new Error(result.message || '저장에 실패했습니다.');
                }
                message.style.color = '#067647';
                message.textContent = result.message;
                window.setTimeout(function() {
                    window.location.reload();
                }, 400);
            })
            .catch(function(error) {
                message.textContent = error.message;
                message.style.color = '#b42318';
            })
            .finally(function() {
                button.disabled = false;
            });
    });

    document.querySelector('.admobile-inspection-history').addEventListener('click', function(event) {
        var button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        var inspectionIdx = button.dataset.inspectionIdx;
        var action = button.dataset.action;
        var checkedQty = button.dataset.checkedQty || '';

        if (action === 'edit') {
            var newQty = window.prompt('수정할 검수수량을 입력하세요.', checkedQty);
            if (newQty === null) {
                return;
            }
            saveRecord({
                action_mode: 'inspection_record_update',
                inspection_idx: inspectionIdx,
                checked_qty: newQty
            });
            return;
        }

        if (action === 'delete' && window.confirm('이 검수 이력을 삭제하시겠습니까?')) {
            saveRecord({
                action_mode: 'inspection_record_delete',
                inspection_idx: inspectionIdx
            });
        }
    });

    function saveRecord(data) {
        fetch('/admobile/order/sheet/action', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams(data)
        })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (!result.success) {
                    throw new Error(result.message || '처리에 실패했습니다.');
                }
                window.location.reload();
            })
            .catch(function(error) {
                window.alert(error.message);
            });
    }
</script>
