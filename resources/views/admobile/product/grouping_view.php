<?php
$productGrouping = $productGrouping ?? [];
$prd_kind_name = $prd_kind_name ?? [];
$flashSuccess = trim((string)($flashSuccess ?? ''));
$flashError = trim((string)($flashError ?? ''));
$pgMode = (string)($productGrouping['pg_mode'] ?? '');
$pgState = (string)($productGrouping['pg_state'] ?? '');
$prdMode = (string)($productGrouping['prd_mode'] ?? '');
$isClosed = ($pgState === '마감');
$isDiscountEditable = $pgMode === 'event' || $pgMode === 'sale' || ($pgMode === 'period' && !$isClosed);
$needsPeriodDate = in_array($pgMode, ['period', 'sale', 'event'], true);
$groupingIdx = (int)($productGrouping['idx'] ?? 0);
$viewReturnTo = '/admobile/product/grouping/view/' . $groupingIdx;

$dateInputValue = static function ($date): string {
    $date = trim((string)$date);
    if ($date === '' || strpos($date, '0000-00-00') === 0) {
        return '';
    }
    return substr($date, 0, 10);
};

$resolvePrdImage = static function (array $item, string $prdMode): string {
    if ($prdMode === 'provider') {
        return trim((string)($item['img_src'] ?? ''));
    }

    $image = trim((string)($item['CD_IMG'] ?? ''));
    if ($image === '') {
        return '';
    }
    if (($item['img_mode'] ?? '') === 'out' || strpos($image, '/') === 0 || strpos($image, 'http') === 0) {
        return $image;
    }
    return '/data/comparion/' . $image;
};
?>
<section class="admobile-grouping-view">
    <div class="admobile-page-heading">
        <a href="/admobile/product/grouping" aria-label="그룹핑 목록으로 돌아가기">‹</a>
        <div>
            <h2><?= h((string)($productGrouping['pg_subject'] ?? '상품 그룹핑'), ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= h((string)($productGrouping['pg_mode_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?> · <?= h((string)($productGrouping['prd_mode_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?> · <?= number_format((int)($productGrouping['prd_count'] ?? 0)) ?>개</p>
        </div>
    </div>

    <?php if ($flashSuccess !== '') { ?>
        <p class="admobile-flash is-success"><?= h($flashSuccess, ENT_QUOTES, 'UTF-8') ?></p>
    <?php } ?>
    <?php if ($flashError !== '') { ?>
        <p class="admobile-flash is-error"><?= h($flashError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php } ?>

    <form id="form_prdGroupingSave" method="post" action="/admobile/product/grouping/save">
        <input type="hidden" name="idx" value="<?= $groupingIdx ?>">
        <input type="hidden" name="pg_mode" value="<?= h($pgMode, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="prd_mode" value="<?= h($prdMode, ENT_QUOTES, 'UTF-8') ?>">

        <section class="admobile-grouping-settings">
            <h3>그룹핑 정보</h3>
            <label>
                <span>제목</span>
                <input type="text" name="pg_subject" id="pg_subject" value="<?= h((string)($productGrouping['pg_subject'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="그룹핑 제목" required>
            </label>
            <div class="admobile-grouping-settings__row">
                <label>
                    <span>공개여부</span>
                    <select name="public" id="public">
                        <option value="공개" <?= ($productGrouping['public'] ?? '') === '공개' ? 'selected' : '' ?>>공개</option>
                        <option value="개인" <?= ($productGrouping['public'] ?? '') === '개인' ? 'selected' : '' ?>>개인</option>
                    </select>
                </label>
                <label>
                    <span>진행상태</span>
                    <select name="pg_state" id="pg_state">
                        <option value="진행" <?= $pgState === '진행' ? 'selected' : '' ?>>진행</option>
                        <option value="마감" <?= $pgState === '마감' ? 'selected' : '' ?>>마감</option>
                        <option value="취소" <?= $pgState === '취소' ? 'selected' : '' ?>>취소</option>
                    </select>
                </label>
            </div>
            <?php if ($needsPeriodDate) { ?>
                <div class="admobile-grouping-settings__row<?= $pgMode === 'period' ? '' : ' is-single' ?>">
                    <?php if ($pgMode === 'period') { ?>
                        <label>
                            <span>시작일</span>
                            <input type="date" name="pg_sday" id="pg_sday" value="<?= h($dateInputValue($productGrouping['pg_sday'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </label>
                    <?php } ?>
                    <label>
                        <span><?= $pgMode === 'period' ? '종료일' : '진행일' ?></span>
                        <input type="date" name="pg_day" id="pg_day" value="<?= h($dateInputValue($productGrouping['pg_day'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </label>
                </div>
            <?php } ?>
            <label>
                <span>메모</span>
                <textarea name="pg_memo" id="pg_memo" rows="3"><?= h((string)($productGrouping['pg_memo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </label>
        </section>

        <div class="admobile-grouping-products">
            <input type="hidden" name="prd_idx[]" value="">
            <?php if (empty($productGrouping['data'])) { ?>
                <p class="admobile-empty">등록된 상품이 없습니다.</p>
            <?php } ?>

            <?php foreach (($productGrouping['data'] ?? []) as $row) {
                $item = (isset($row['prd_data']) && is_array($row['prd_data'])) ? $row['prd_data'] : [];
                $isInstant = ((string)($row['idx'] ?? '') === 'Instant');
                $prdIdx = $prdMode === 'provider'
                    ? (string)($item['idx'] ?? ($row['idx'] ?? ''))
                    : (string)($item['CD_IDX'] ?? ($row['idx'] ?? ''));
                $image = $resolvePrdImage($item, $prdMode);
                $productName = $isInstant
                    ? (string)($row['pname'] ?? '상품명 없음')
                    : (string)($prdMode === 'provider' ? ($item['name'] ?? '상품명 없음') : ($item['CD_NAME'] ?? '상품명 없음'));
                $modeData = (isset($row['mode_data']) && is_array($row['mode_data'])) ? $row['mode_data'] : [];
                $salePrice = (int)($item['cd_sale_price'] ?? ($item['sale_price'] ?? 0));
                $stockQty = (int)($item['ps_stock'] ?? 0);
                $productMemo = trim((string)($item['memo_work'] ?? ''));
            ?>
                <article class="admobile-grouping-product" data-prd-idx="<?= h($prdIdx, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="prd_idx[]" value="<?= h($prdIdx, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="admobile-grouping-product__head">
                        <?php if ($image !== '') { ?>
                            <button
                                type="button"
                                class="admobile-grouping-product__image"
                                data-image="<?= h($image, ENT_QUOTES, 'UTF-8') ?>"
                                data-name="<?= h($productName, ENT_QUOTES, 'UTF-8') ?>"
                            >
                                <img src="<?= h($image, ENT_QUOTES, 'UTF-8') ?>" alt="">
                            </button>
                        <?php } else { ?>
                            <div class="admobile-grouping-product__image is-empty"><span>이미지 없음</span></div>
                        <?php } ?>
                        <div class="admobile-grouping-product__meta">
                            <div class="admobile-grouping-product__topline">
                                <span>#<?= h($prdIdx !== '' ? $prdIdx : '-', ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if ($prdMode === 'provider' && !empty($item['status'])) { ?>
                                    <span class="admobile-grouping-badge <?= ($item['status'] ?? '') === '품절' ? 'is-danger' : '' ?>"><?= h((string)$item['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php } ?>
                                <?php if ($prdMode === 'prdDB') { ?>
                                    <span class="admobile-grouping-badge is-soft"><?= h((string)($prd_kind_name[$item['CD_KIND_CODE'] ?? ''] ?? '미지정'), ENT_QUOTES, 'UTF-8') ?></span>
                                <?php } ?>
                            </div>
                            <h3><?= h($productName, ENT_QUOTES, 'UTF-8') ?></h3>
                            <?php if ($prdMode === 'prdDB' && !empty($item['brand_name'])) { ?>
                                <p><?= h((string)$item['brand_name'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php } ?>
                            <?php if ($prdMode === 'provider') { ?>
                                <p><?= h((string)($item['brand_name'] ?? ($item['partner_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?></p>
                            <?php } ?>
                            <?php if ($prdMode === 'prdDB') { ?>
                                <dl class="admobile-grouping-product__info">
                                    <div>
                                        <dt>바코드</dt>
                                        <dd><?= h((string)($item['barcode'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                                    </div>
                                    <div>
                                        <dt>랙코드</dt>
                                        <dd><?= h((string)($item['ps_rack_code'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                                    </div>
                                    <div>
                                        <dt>재고</dt>
                                        <dd class="<?= $stockQty === 0 ? 'is-danger' : 'is-stock' ?>"><?= $stockQty === 0 ? '재고 없음' : number_format($stockQty) . '개' ?></dd>
                                    </div>
                                </dl>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if ($prdMode !== 'prdDB') { ?>
                        <dl>
                            <div>
                                <dt>공급사</dt>
                                <dd><?= h((string)($item['partner_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                            </div>
                            <div>
                                <dt>코드</dt>
                                <dd><?= h((string)($item['code'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                            </div>
                            <div>
                                <dt>고도몰</dt>
                                <dd><?= !empty($item['godo_goodsNo']) ? '#' . h((string)$item['godo_goodsNo'], ENT_QUOTES, 'UTF-8') : '미등록' ?></dd>
                            </div>
                            <div>
                                <dt>판매가</dt>
                                <dd><?= number_format((int)($item['sale_price'] ?? 0)) ?></dd>
                            </div>
                            <div>
                                <dt>원가 / 주문가</dt>
                                <dd><?= number_format((int)($item['cost_price'] ?? 0)) ?> / <?= number_format((int)($item['order_price'] ?? 0)) ?></dd>
                            </div>
                            <div>
                                <dt>공급사 상품명</dt>
                                <dd><?= h((string)($item['name_p'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></dd>
                            </div>
                        </dl>
                    <?php } ?>

                    <?php if ($prdMode === 'prdDB' && $isDiscountEditable && $prdIdx !== '') { ?>
                        <input type="hidden" name="pg_prd_per[]" value="<?= (int)($modeData['per'] ?? 0) ?>">
                        <input type="hidden" name="original_sale_price[]" value="<?= $salePrice ?>">
                        <input type="hidden" name="dis_sale_price[]" value="<?= (int)($modeData['sale_price'] ?? 0) ?>">
                        <input type="hidden" name="dis_margin_price[]" value="<?= (int)($modeData['margin_price'] ?? 0) ?>">
                        <input type="hidden" name="dis_margin_per[]" value="<?= h((string)($modeData['margin_per'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">
                    <?php } ?>

                    <input type="hidden" name="pg_prd_memo[]" class="admobile-grouping-product__memo-input" value="<?= h($productMemo, ENT_QUOTES, 'UTF-8') ?>">
                    <p class="admobile-grouping-product__memo-text"<?= $productMemo === '' ? ' hidden' : '' ?>><?= h($productMemo, ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="admobile-grouping-product__actions">
                        <?php if ($prdMode === 'prdDB' && $prdIdx !== '' && !$isInstant) { ?>
                            <a class="admobile-grouping-product__detail" href="/admobile/product/detail?prd_idx=<?= urlencode($prdIdx) ?>&return_to=<?= urlencode($viewReturnTo) ?>">상품정보 보기</a>
                        <?php } ?>
                        <button type="button" class="admobile-grouping-product__memo-btn" data-action="memo">
                            <?= $productMemo === '' ? '메모 추가' : '메모 수정' ?>
                        </button>
                        <?php if (!$isClosed) { ?>
                            <button type="button" class="admobile-grouping-product__delete" data-action="delete">삭제</button>
                        <?php } ?>
                    </div>
                </article>
            <?php } ?>
        </div>

        <div class="admobile-grouping-savebar">
            <button type="submit">저장</button>
        </div>
    </form>
</section>

<div id="admobile-grouping-image-modal" class="admobile-grouping-image-modal" hidden>
    <div class="admobile-grouping-image-modal__backdrop" data-action="close"></div>
    <section class="admobile-grouping-image-modal__content" role="dialog" aria-modal="true">
        <button type="button" class="admobile-grouping-image-modal__close" data-action="close">닫기</button>
        <img id="admobile-grouping-image" src="" alt="">
        <p id="admobile-grouping-image-title"></p>
    </section>
</div>

<div id="admobile-grouping-memo-modal" class="admobile-grouping-memo-modal" hidden>
    <div class="admobile-grouping-memo-modal__backdrop" data-action="memo-close"></div>
    <section class="admobile-grouping-memo-modal__content" role="dialog" aria-modal="true" aria-labelledby="admobile-grouping-memo-title">
        <h3 id="admobile-grouping-memo-title">상품 메모</h3>
        <p id="admobile-grouping-memo-product"></p>
        <textarea id="admobile-grouping-memo-input" rows="5" placeholder="메모를 입력하세요"></textarea>
        <div class="admobile-grouping-memo-modal__actions">
            <button type="button" data-action="memo-close">취소</button>
            <button type="button" data-action="memo-apply">적용</button>
        </div>
    </section>
</div>

<style>
    .admobile-page-heading { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
    .admobile-page-heading a { color: #344054; font-size: 30px; line-height: 24px; text-decoration: none; }
    .admobile-page-heading h2 { margin: 0; font-size: 18px; overflow-wrap: anywhere; }
    .admobile-page-heading p { margin: 4px 0 0; color: #667085; font-size: 13px; }
    .admobile-flash { margin: 0 0 12px; padding: 10px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; }
    .admobile-flash.is-success { background: #ecfdf3; color: #027a48; }
    .admobile-flash.is-error { background: #fef3f2; color: #b42318; }
    .admobile-grouping-settings { display: grid; gap: 10px; margin-bottom: 14px; padding: 14px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; }
    .admobile-grouping-settings h3 { margin: 0; font-size: 15px; }
    .admobile-grouping-settings label { display: grid; gap: 6px; }
    .admobile-grouping-settings span { color: #667085; font-size: 12px; font-weight: 700; }
    .admobile-grouping-settings input, .admobile-grouping-settings select, .admobile-grouping-settings textarea {
        width: 100%; padding: 10px; border: 1px solid #cfd6e1; border-radius: 7px; background: #fff; color: #172033; font: inherit; font-size: 14px;
    }
    .admobile-grouping-settings__row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
    .admobile-grouping-settings__row.is-single { grid-template-columns: 1fr; }
    .admobile-grouping-products { display: grid; gap: 6px; padding-bottom: 76px; }
    .admobile-grouping-product { padding: 8px; border: 1px solid #e3e7ee; border-radius: 8px; background: #fff; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
    .admobile-grouping-product__head { display: flex; gap: 8px; margin-bottom: 6px; }
    .admobile-grouping-product__image { display: flex; flex: 0 0 56px; align-items: center; justify-content: center; width: 56px; height: 56px; overflow: hidden; padding: 0; border: 1px solid #edf0f4; border-radius: 6px; background: #f8fafc; color: #98a2b3; font-size: 9px; }
    .admobile-grouping-product__image img { width: 100%; height: 100%; object-fit: cover; }
    .admobile-grouping-product__image.is-empty { cursor: default; }
    .admobile-grouping-product__meta { min-width: 0; flex: 1; }
    .admobile-grouping-product__topline { display: flex; flex-wrap: wrap; gap: 3px; margin-bottom: 2px; }
    .admobile-grouping-product__topline > span:first-child { color: #667085; font-size: 10px; font-weight: 700; }
    .admobile-grouping-badge { padding: 1px 5px; border-radius: 999px; background: #e4ebfb; color: #2c4d92; font-size: 9px; font-weight: 700; }
    .admobile-grouping-badge.is-soft { background: #eef1f5; color: #667085; }
    .admobile-grouping-badge.is-danger { background: #fde8e8; color: #b42318; }
    .admobile-grouping-product h3 { display: -webkit-box; overflow: hidden; margin: 0; color: #172033; font-size: 14px; line-height: 1.3; overflow-wrap: anywhere; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; }
    .admobile-grouping-product p { overflow: hidden; margin: 2px 0 0; color: #667085; font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-grouping-product dl { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px 8px; margin: 0 0 8px; }
    .admobile-grouping-product__info { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 2px 6px; margin: 5px 0 0; }
    .admobile-grouping-product dt { color: #667085; font-size: 10px; font-weight: 700; }
    .admobile-grouping-product dd { overflow: hidden; margin: 1px 0 0; color: #172033; font-size: 13px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-grouping-product dd.is-stock { color: #2450a6; }
    .admobile-grouping-product dd.is-danger { color: #b42318; }
    .admobile-grouping-product .admobile-grouping-product__memo-text { margin: 0 0 6px; padding: 6px 8px; border: 1px solid #f2d6a8; border-radius: 6px; background: #fff8eb; color: #7a4a00; font-size: 13px; line-height: 1.35; overflow-wrap: anywhere; white-space: pre-wrap; }
    .admobile-grouping-product__memo-text[hidden] { display: none; }
    .admobile-grouping-product__actions { display: flex; gap: 5px; }
    .admobile-grouping-product__actions > * { flex: 1; min-width: 0; padding: 7px 3px; border-radius: 5px; font: inherit; font-size: 11px; font-weight: 700; text-align: center; text-decoration: none; white-space: nowrap; }
    .admobile-grouping-product__detail { border: 1px solid #3056a8; background: #fff; color: #3056a8; }
    .admobile-grouping-product__memo-btn { border: 1px solid #cfd6e1; background: #fff; color: #344054; }
    .admobile-grouping-product__delete { border: 1px solid #fda29b; background: #fff; color: #b42318; }
    .admobile-grouping-memo-modal[hidden] { display: none; }
    .admobile-grouping-memo-modal { position: fixed; z-index: 1210; inset: 0; display: flex; align-items: flex-end; justify-content: center; padding: 16px; }
    .admobile-grouping-memo-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .5); }
    .admobile-grouping-memo-modal__content { position: relative; width: min(100%, 520px); padding: 18px 16px 16px; border-radius: 14px 14px 10px 10px; background: #fff; box-shadow: 0 -8px 24px rgba(16, 24, 40, .18); }
    .admobile-grouping-memo-modal__content h3 { margin: 0 0 6px; font-size: 17px; }
    .admobile-grouping-memo-modal__content p { margin: 0 0 10px; color: #667085; font-size: 13px; overflow-wrap: anywhere; }
    .admobile-grouping-memo-modal__content textarea { width: 100%; min-height: 120px; padding: 12px; border: 1px solid #cfd6e1; border-radius: 8px; color: #172033; font: inherit; font-size: 16px; resize: vertical; }
    .admobile-grouping-memo-modal__actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 12px; }
    .admobile-grouping-memo-modal__actions button { padding: 12px; border: 0; border-radius: 8px; font: inherit; font-size: 15px; font-weight: 700; }
    .admobile-grouping-memo-modal__actions [data-action="memo-close"] { background: #eef1f5; color: #344054; }
    .admobile-grouping-memo-modal__actions [data-action="memo-apply"] { background: #3056a8; color: #fff; }
    .admobile-grouping-savebar { position: sticky; bottom: 0; z-index: 20; margin: 0 -16px -16px; padding: 10px 16px calc(10px + env(safe-area-inset-bottom)); background: rgba(243, 245, 248, .96); border-top: 1px solid #e3e7ee; }
    .admobile-grouping-savebar button { width: 100%; padding: 13px; border: 0; border-radius: 8px; background: #3056a8; color: #fff; font: inherit; font-size: 15px; font-weight: 700; }
    .admobile-empty { margin: 0; padding: 32px 16px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; color: #667085; text-align: center; }
    .admobile-grouping-image-modal[hidden] { display: none; }
    .admobile-grouping-image-modal { position: fixed; z-index: 1200; inset: 0; display: flex; align-items: center; justify-content: center; padding: 18px; }
    .admobile-grouping-image-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .78); }
    .admobile-grouping-image-modal__content { position: relative; width: min(100%, 640px); max-height: 90vh; padding: 42px 12px 12px; border-radius: 12px; background: #fff; text-align: center; }
    .admobile-grouping-image-modal__content img { display: block; width: 100%; max-height: calc(90vh - 94px); object-fit: contain; }
    .admobile-grouping-image-modal__content p { margin: 9px 0 0; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-grouping-image-modal__close { position: absolute; top: 8px; right: 8px; padding: 7px 10px; border: 0; border-radius: 6px; background: #344054; color: #fff; font-size: 12px; font-weight: 700; }
</style>

<script>
    (function() {
        var modal = document.getElementById('admobile-grouping-image-modal');
        var image = document.getElementById('admobile-grouping-image');
        var title = document.getElementById('admobile-grouping-image-title');
        var memoModal = document.getElementById('admobile-grouping-memo-modal');
        var memoInput = document.getElementById('admobile-grouping-memo-input');
        var memoProduct = document.getElementById('admobile-grouping-memo-product');
        var activeCard = null;

        document.querySelectorAll('.admobile-grouping-product__image[data-image]').forEach(function(button) {
            button.addEventListener('click', function() {
                image.src = button.getAttribute('data-image') || '';
                image.alt = button.getAttribute('data-name') || '상품 이미지';
                title.textContent = button.getAttribute('data-name') || '';
                modal.hidden = false;
            });
        });

        modal.addEventListener('click', function(event) {
            if (event.target.closest('[data-action="close"]')) {
                modal.hidden = true;
                image.src = '';
            }
        });

        function closeMemoModal() {
            memoModal.hidden = true;
            memoInput.value = '';
            activeCard = null;
        }

        function applyMemo() {
            if (!activeCard) {
                return;
            }
            var memo = memoInput.value.trim();
            var hidden = activeCard.querySelector('.admobile-grouping-product__memo-input');
            var text = activeCard.querySelector('.admobile-grouping-product__memo-text');
            var button = activeCard.querySelector('[data-action="memo"]');
            if (hidden) {
                hidden.value = memo;
            }
            if (text) {
                text.textContent = memo;
                text.hidden = memo === '';
            }
            if (button) {
                button.textContent = memo === '' ? '메모 추가' : '메모 수정';
            }
            closeMemoModal();
        }

        document.querySelectorAll('[data-action="memo"]').forEach(function(button) {
            button.addEventListener('click', function() {
                activeCard = button.closest('.admobile-grouping-product');
                var hidden = activeCard ? activeCard.querySelector('.admobile-grouping-product__memo-input') : null;
                var name = activeCard ? activeCard.querySelector('h3') : null;
                memoProduct.textContent = name ? name.textContent : '';
                memoInput.value = hidden ? hidden.value : '';
                memoModal.hidden = false;
                memoInput.focus();
            });
        });

        memoModal.addEventListener('click', function(event) {
            if (event.target.closest('[data-action="memo-close"]')) {
                closeMemoModal();
            }
            if (event.target.closest('[data-action="memo-apply"]')) {
                applyMemo();
            }
        });

        document.querySelectorAll('[data-action="delete"]').forEach(function(button) {
            button.addEventListener('click', function() {
                if (!confirm('해당 상품을 그룹핑 목록에서 삭제합니다.\n저장을 눌러야 최종 적용됩니다. 삭제할까요?')) {
                    return;
                }
                var card = button.closest('.admobile-grouping-product');
                if (card) {
                    card.remove();
                }
            });
        });
    })();
</script>
