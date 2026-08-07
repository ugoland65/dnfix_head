<?php
$stockUnits = $stockUnits ?? [];
$totalUnitCount = (int)($totalUnitCount ?? 0);
$failedUnitCount = (int)($failedUnitCount ?? 0);
$completedUnitCount = count(array_filter($stockUnits, static function ($unit) {
    return !empty($unit['is_check_complete']);
}));
$pendingUnitCount = count($stockUnits) - $completedUnitCount;
?>
<section class="admobile-stock-inspection">
    <div class="admobile-stock-heading">
        <a href="/admobile/order/sheet/list" aria-label="주문 리스트로 돌아가기">‹</a>
        <div>
            <h2><?= htmlspecialchars((string)($orderName ?? ''), ENT_QUOTES, 'UTF-8') ?></h2>
            <p>입고 수량검수</p>
        </div>
    </div>

    <div id="barcode-search-sentinel" class="admobile-barcode-search-sentinel" aria-hidden="true"></div>
    <section id="barcode-search" class="admobile-barcode-search" aria-label="바코드 상품 찾기">
        <div class="admobile-barcode-search__heading">
            <label for="barcode-search-input">바코드 뒷자리 검색</label>
            <span>상품 찾기</span>
        </div>
        <div class="admobile-barcode-input-wrap">
            <input id="barcode-search-input" type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="off" placeholder="바코드 숫자를 입력하세요" autofocus>
            <button type="button" id="barcode-search-clear" aria-label="검색어 지우기">×</button>
            <button type="button" id="barcode-search-button">찾기</button>
        </div>
        <p id="barcode-search-message" class="admobile-barcode-search-message" aria-live="polite"></p>
    </section>

    <div class="admobile-stock-summary">
        <button type="button" class="is-active" data-stock-filter="all" aria-pressed="true">전체 <strong><?= number_format(count($stockUnits)) ?></strong>개</button>
        <button type="button" data-stock-filter="complete" aria-pressed="false">체크완료 <strong><?= number_format($completedUnitCount) ?></strong>개</button>
        <button type="button" data-stock-filter="pending" aria-pressed="false">미완료 <strong><?= number_format($pendingUnitCount) ?></strong>개</button>
       <!--
        <?php if ($failedUnitCount > 0) { ?>
            <span>주문실패 제외 <strong><?= number_format($failedUnitCount) ?></strong>개</span>
        <?php } ?>
        -->
        
    </div>

    <div class="admobile-stock-list">
        <?php if ($totalUnitCount === 0) { ?>
            <p class="admobile-stock-empty">주문상품 데이터가 없습니다.</p>
        <?php } elseif (empty($stockUnits)) { ?>
            <p class="admobile-stock-empty">표시할 주문상품이 없습니다. 주문실패 상품은 제외됩니다.</p>
        <?php } ?>

        <?php foreach ($stockUnits as $unit) { ?>
            <article class="admobile-stock-card" data-inspection-status="<?= !empty($unit['is_check_complete']) ? 'complete' : 'pending' ?>">
                <div class="admobile-stock-image-wrap">
                    <?php if (!empty($unit['sale_status'])) { ?>
                        <span class="admobile-stock-sale-status<?= ($unit['sale_status'] ?? '') === '신상주문' ? ' is-new-order' : (($unit['sale_status'] ?? '') === '구매대행' ? ' is-purchase-agent' : '') ?>"><?= htmlspecialchars((string)$unit['sale_status'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php } ?>
                    <?php if (!empty($unit['product_image'])) { ?>
                        <button
                            type="button"
                            class="admobile-stock-image admobile-stock-image-button"
                            data-image="<?= htmlspecialchars((string)$unit['product_image'], ENT_QUOTES, 'UTF-8') ?>"
                            data-name="<?= htmlspecialchars((string)($unit['product_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            aria-label="상품 이미지 크게 보기"
                        >
                            <img src="<?= htmlspecialchars((string)$unit['product_image'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                        </button>
                    <?php } else { ?>
                        <div class="admobile-stock-image">
                            <span>이미지 없음</span>
                        </div>
                    <?php } ?>
                    <a class="admobile-stock-product-edit" href="/admobile/product/detail?prd_idx=<?= (int)($unit['pidx'] ?? 0) ?>&amp;return_to=<?= rawurlencode('/admobile/order/sheet/stock?idx=' . (int)($orderIdx ?? 0)) ?>">상품수정</a>
                </div>
                <div class="admobile-stock-content">
                    <?php if (!empty($unit['brand_name'])) { ?>
                        <p class="admobile-stock-brand"><?= htmlspecialchars((string)$unit['brand_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php } ?>
                    <h3><?= htmlspecialchars((string)($unit['product_name'] ?: '상품명 없음'), ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="admobile-stock-barcode">바코드 <b><?= htmlspecialchars((string)($unit['barcode'] ?: '-'), ENT_QUOTES, 'UTF-8') ?></b></p>
                    <p class="admobile-stock-barcode">랙  <b><?= htmlspecialchars((string)($unit['ps_rack_code'] ?: '-'), ENT_QUOTES, 'UTF-8') ?></b></p>
                    <dl>
                        <div>
                            <dt>주문수량</dt>
                            <dd><?= number_format((int)($unit['order_qty'] ?? 0)) ?>개</dd>
                        </div>
                        <div>
                            <dt>체크수량</dt>
                            <dd><?= number_format((int)($unit['checked_total_qty'] ?? 0)) ?>개</dd>
                        </div>
                        <div>
                            <dt>현재고</dt>
                            <dd><?= number_format((int)($unit['stock_qty'] ?? 0)) ?>개</dd>
                        </div>
                    </dl>
                    <?php if (!empty($unit['is_check_complete'])) { ?>
                        <p class="admobile-check-complete">수량체크 완료</p>
                    <?php } ?>
                    <?php if (!empty($unit['stock_inspection_memo'])) { ?>
                        <p class="admobile-stock-memo" data-memo-preview><?= nl2br(htmlspecialchars((string)$unit['stock_inspection_memo'], ENT_QUOTES, 'UTF-8')) ?></p>
                    <?php } ?>

                    <nav class="admobile-unit-action-menu" aria-label="상품 작업 메뉴">
                        <button
                            type="button"
                            class="admobile-unit-action-menu__item"
                            data-complete-check
                            data-order-idx="<?= (int)($orderIdx ?? 0) ?>"
                            data-bidx="<?= (int)($unit['bidx'] ?? 0) ?>"
                            data-pidx="<?= (int)($unit['pidx'] ?? 0) ?>"
                            <?= !empty($unit['is_check_complete']) ? 'disabled' : '' ?>
                        ><?= !empty($unit['is_check_complete']) ? '체크완료' : '바로 완료체크' ?></button>
                        <a class="admobile-unit-action-menu__item admobile-unit-action-menu__item--primary" href="/admobile/order/sheet/stock/unit?idx=<?= (int)($orderIdx ?? 0) ?>&amp;bidx=<?= (int)($unit['bidx'] ?? 0) ?>&amp;pidx=<?= (int)($unit['pidx'] ?? 0) ?>">수량체크하기</a>
                        <button
                            type="button"
                            class="admobile-unit-action-menu__item"
                            data-memo-open
                            data-order-idx="<?= (int)($orderIdx ?? 0) ?>"
                            data-bidx="<?= (int)($unit['bidx'] ?? 0) ?>"
                            data-pidx="<?= (int)($unit['pidx'] ?? 0) ?>"
                            data-product-name="<?= htmlspecialchars((string)($unit['product_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            data-memo="<?= htmlspecialchars((string)($unit['stock_inspection_memo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        ><?= !empty($unit['stock_inspection_memo']) ? '메모 수정' : '메모 작성' ?></button>
                    </nav>

                </div>
            </article>
        <?php } ?>
        <p id="admobile-stock-filter-empty" class="admobile-stock-empty" hidden>해당 상태의 주문상품이 없습니다.</p>
    </div>
    <button type="button" class="admobile-stock-refresh" onclick="window.location.reload()">새로고침</button>
</section>

<div id="product-image-modal" class="admobile-product-image-modal" hidden>
    <div class="admobile-product-image-modal__backdrop" data-action="close"></div>
    <section class="admobile-product-image-modal__content" role="dialog" aria-modal="true" aria-label="상품 이미지 크게 보기">
        <button type="button" class="admobile-product-image-modal__close" data-action="close" aria-label="이미지 닫기">닫기</button>
        <img id="product-image-modal-image" src="" alt="">
        <p id="product-image-modal-name"></p>
    </section>
</div>

<div id="barcode-result-modal" class="admobile-barcode-modal" hidden>
    <div class="admobile-barcode-modal__backdrop" data-action="close"></div>
    <section class="admobile-barcode-modal__content" role="dialog" aria-modal="true" aria-labelledby="barcode-result-title">
        <div class="admobile-barcode-modal__heading">
            <h2 id="barcode-result-title">찾은 주문상품</h2>
            <button type="button" data-action="close" aria-label="결과 닫기">×</button>
        </div>
        <div id="barcode-result-list" class="admobile-barcode-result-list"></div>
    </section>
</div>

<div id="inspection-memo-modal" class="admobile-inspection-memo-modal" hidden>
    <div class="admobile-inspection-memo-modal__backdrop" data-action="close"></div>
    <section class="admobile-inspection-memo-modal__content" role="dialog" aria-modal="true" aria-labelledby="inspection-memo-title">
        <form id="inspection-memo-form">
            <input type="hidden" name="action_mode" value="inspection_memo_save">
            <input type="hidden" name="idx">
            <input type="hidden" name="bidx">
            <input type="hidden" name="pidx">
            <div class="admobile-inspection-memo-modal__heading">
                <div>
                    <p>입고 수량검수</p>
                    <h2 id="inspection-memo-title">상품 메모</h2>
                </div>
                <button type="button" data-action="close" aria-label="메모 닫기">×</button>
            </div>
            <p id="inspection-memo-product" class="admobile-inspection-memo-modal__product"></p>
            <label for="inspection-memo-text">검수 시 확인할 내용을 기록하세요</label>
            <textarea id="inspection-memo-text" name="memo" maxlength="1000" placeholder="예) 박스 훼손 여부 확인, 입고 시 유의사항"></textarea>
            <div class="admobile-inspection-memo-modal__meta">
                <span>다른 관리자도 이 메모를 확인할 수 있습니다.</span>
                <strong><span id="inspection-memo-count">0</span>/1000</strong>
            </div>
            <p id="inspection-memo-message" class="admobile-inspection-memo-modal__message" aria-live="polite"></p>
            <div class="admobile-inspection-memo-modal__actions">
                <button type="button" class="admobile-inspection-memo-modal__cancel" data-action="close">취소</button>
                <button type="submit" class="admobile-inspection-memo-modal__save">저장</button>
            </div>
        </form>
    </section>
</div>

<style>
    .admobile-stock-heading { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
    .admobile-stock-heading > a { color: #344054; font-size: 30px; line-height: 24px; text-decoration: none; }
    .admobile-stock-heading h2 { margin: 0; font-size: 18px; }
    .admobile-stock-heading p { overflow: hidden; margin: 4px 0 0; color: #667085; font-size: 13px; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-stock-inspection { padding-bottom: 64px; }
    .admobile-stock-refresh { position: fixed; z-index: 30; right: 0; bottom: 0; left: 0; width: 100%; padding: 15px 12px calc(15px + env(safe-area-inset-bottom)); border: 0; background: #3056a8; color: #fff; font: inherit; font-size: 15px; font-weight: 700; }
    .admobile-barcode-search-sentinel { height: 1px; margin-top: -1px; }
    .admobile-barcode-search { position: sticky; z-index: 40; top: 0; margin: 0 -16px 12px; padding: 13px 16px; border: 1px solid #cbd6ee; border-radius: 10px; background: #f7f9ff; transition: padding .18s ease, border-radius .18s ease, background .18s ease, box-shadow .18s ease; }
    .admobile-barcode-search__heading { display: flex; align-items: center; justify-content: space-between; margin-bottom: 7px; }
    .admobile-barcode-search__heading label { color: #2c4d92; font-size: 13px; font-weight: 700; }
    .admobile-barcode-search__heading span { padding: 3px 6px; border-radius: 999px; background: #e4ebfb; color: #526a9d; font-size: 10px; font-weight: 700; }
    .admobile-barcode-input-wrap { display: flex; overflow: hidden; border: 1px solid #aab9d6; border-radius: 7px; background: #fff; }
    .admobile-barcode-input-wrap input { width: 100%; min-width: 0; padding: 11px 10px; border: 0; color: #172033; font-size: 18px; font-weight: 700; letter-spacing: 1px; outline: 0; }
    .admobile-barcode-input-wrap button { border: 0; border-left: 1px solid #e2e8f3; background: #fff; color: #667085; }
    #barcode-search-clear { width: 42px; font-size: 22px; }
    #barcode-search-button { width: 56px; background: #3056a8; color: #fff; font-size: 13px; font-weight: 700; }
    .admobile-barcode-search-message { min-height: 16px; margin: 8px 0 0; color: #b42318; font-size: 12px; }
    .admobile-barcode-search.is-stuck { padding-top: calc(8px + env(safe-area-inset-top)); padding-bottom: 8px; border-width: 0 0 1px; border-radius: 0; background: #172b4d; box-shadow: 0 4px 12px rgba(16, 24, 40, .18); }
    .admobile-barcode-search.is-stuck .admobile-barcode-search__heading { margin-bottom: 4px; }
    .admobile-barcode-search.is-stuck .admobile-barcode-search__heading label { color: #fff; font-size: 11px; }
    .admobile-barcode-search.is-stuck .admobile-barcode-search__heading span { background: rgba(255, 255, 255, .15); color: #dce7ff; }
    .admobile-barcode-search.is-stuck .admobile-barcode-input-wrap input { padding-top: 9px; padding-bottom: 9px; font-size: 16px; }
    .admobile-barcode-search.is-stuck .admobile-barcode-search-message { min-height: 0; margin: 5px 0 0; color: #ffd1cc; }
    .admobile-stock-summary { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; color: #667085; font-size: 12px; }
    .admobile-stock-summary span, .admobile-stock-summary button { padding: 7px 9px; border: 1px solid #e3e7ee; border-radius: 999px; background: #fff; color: #667085; font: inherit; cursor: pointer; }
    .admobile-stock-summary button.is-active { border-color: #3056a8; background: #3056a8; color: #fff; font-weight: 700; }
    .admobile-stock-summary strong { color: #172033; }
    .admobile-stock-summary button.is-active strong { color: inherit; }
    .admobile-stock-list { display: grid; gap: 9px; }
    .admobile-stock-card { display: flex; gap: 12px; padding: 12px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
    .admobile-stock-card[hidden] { display: none; }
    .admobile-stock-image-wrap { flex: 0 0 72px; width: 72px; }
    .admobile-stock-image { display: flex; flex: 0 0 72px; align-items: center; justify-content: center; width: 72px; height: 72px; overflow: hidden; border: 1px solid #edf0f4; border-radius: 8px; background: #f8fafc; color: #98a2b3; font-size: 10px; text-align: center; }
    .admobile-stock-image img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .admobile-stock-image-button { padding: 0; cursor: zoom-in; }
    .admobile-stock-product-edit { display: block; margin-top: 5px; padding: 5px 2px; border: 1px solid #aab9d6; border-radius: 5px; background: #f7f9ff; color: #2c4d92; font-size: 10px; font-weight: 700; line-height: 1; text-align: center; text-decoration: none; }
    .admobile-stock-sale-status { display: block; overflow: hidden; margin-bottom: 4px; padding: 3px 5px; border-radius: 4px; background: #2c4d92; color: #fff; font-size: 9px; font-weight: 700; line-height: 1; text-align: center; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-stock-sale-status.is-new-order { background: #dc2626; }
    .admobile-stock-sale-status.is-purchase-agent { background: #555; }
    .admobile-stock-content { min-width: 0; flex: 1; }
    .admobile-stock-brand { overflow: hidden; margin: 0 0 3px; color: #667085; font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-stock-content h3 { display: -webkit-box; overflow: hidden; margin: 0; color: #172033; font-size: 14px; line-height: 1.4; line-clamp: 2; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
    .admobile-stock-barcode { overflow: hidden; margin: 6px 0 9px; color: #172033; font-family: monospace; font-size: 13px; font-weight: 500; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-stock-barcode b { font-weight: 700; letter-spacing: .5px; }
    .admobile-stock-content dl { display: flex; gap: 18px; margin: 0; }
    .admobile-stock-content dt { margin-bottom: 2px; color: #667085; font-size: 10px; }
    .admobile-stock-content dd { margin: 0; color: #172033; font-size: 16px; font-weight: 800; }
    .admobile-stock-content dl > div:first-child dd { color: #2450a6; }
    .admobile-stock-content dl > div:nth-child(3) dd { font-weight: 400; }
    .admobile-check-complete { margin: 9px 0 0; padding: 6px 8px; border-radius: 5px; background: #e7f8ef; color: #067647; font-size: 12px; font-weight: 800; text-align: center; }
    .admobile-stock-memo { display: -webkit-box; overflow: hidden; margin: 9px 0 0; padding: 8px 9px; border-left: 3px solid #aab9d6; border-radius: 0 6px 6px 0; background: #f7f9fc; color: #475467; font-size: 12px; line-height: 1.45; line-clamp: 2; -webkit-box-orient: vertical; -webkit-line-clamp: 2; white-space: pre-line; }
    .admobile-unit-action-menu { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 5px; margin-top: 10px; }
    .admobile-unit-action-menu__item { display: flex; align-items: center; justify-content: center; min-height: 36px; padding: 6px 4px; border: 1px solid #cfd6e1; border-radius: 6px; background: #fff; color: #475467; font: inherit; font-size: 11px; font-weight: 700; line-height: 1.25; text-align: center; text-decoration: none; }
    .admobile-unit-action-menu__item--primary { border-color: #3056a8; background: #3056a8; color: #fff; }
    .admobile-unit-action-menu__item:disabled { border-color: #b8e3ca; background: #e7f8ef; color: #067647; cursor: default; }
    .admobile-stock-empty { margin: 0; padding: 32px 16px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; color: #667085; font-size: 14px; text-align: center; }
    .admobile-product-image-modal[hidden] { display: none; }
    .admobile-product-image-modal { position: fixed; z-index: 1100; inset: 0; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .admobile-product-image-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .78); }
    .admobile-product-image-modal__content { position: relative; width: min(100%, 560px); max-height: 90vh; overflow: auto; padding: 42px 12px 12px; border-radius: 12px; background: #fff; text-align: center; }
    .admobile-product-image-modal__content img { display: block; width: 100%; max-height: calc(90vh - 100px); object-fit: contain; }
    .admobile-product-image-modal__content p { margin: 10px 4px 0; color: #344054; font-size: 14px; font-weight: 700; }
    .admobile-product-image-modal__close { position: absolute; top: 8px; right: 8px; padding: 7px 10px; border: 0; border-radius: 6px; background: #344054; color: #fff; font-size: 12px; font-weight: 700; }
    .admobile-barcode-modal[hidden] { display: none; }
    .admobile-barcode-modal { position: fixed; z-index: 1000; inset: 0; display: flex; align-items: flex-end; }
    .admobile-barcode-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .58); }
    .admobile-barcode-modal__content { position: relative; width: 100%; max-height: 82vh; overflow: auto; padding: 18px 16px calc(18px + env(safe-area-inset-bottom)); border-radius: 16px 16px 0 0; background: #fff; }
    .admobile-barcode-modal__heading { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .admobile-barcode-modal__heading h2 { margin: 0; font-size: 18px; }
    .admobile-barcode-modal__heading button { width: 32px; height: 32px; border: 0; border-radius: 50%; background: #f2f4f7; color: #344054; font-size: 22px; }
    .admobile-barcode-result-list { display: grid; gap: 10px; }
    .admobile-barcode-result { display: flex; gap: 13px; padding: 13px; border: 2px solid #3e68bf; border-radius: 10px; background: #f7f9ff; }
    .admobile-barcode-result__image-wrap { flex: 0 0 96px; width: 96px; }
    .admobile-barcode-result__image { display: flex; flex: 0 0 96px; align-items: center; justify-content: center; width: 96px; height: 96px; overflow: hidden; border-radius: 8px; background: #fff; color: #98a2b3; font-size: 10px; text-align: center; }
    .admobile-barcode-result__image img { width: 100%; height: 100%; object-fit: cover; }
    .admobile-barcode-result__content { min-width: 0; flex: 1; }
    .admobile-barcode-result__brand { margin: 0 0 4px; color: #667085; font-size: 12px; }
    .admobile-barcode-result__content h3 { margin: 0; color: #172033; font-size: 17px; line-height: 1.45; }
    .admobile-barcode-result__barcode { margin: 8px 0 0; color: #344054; font-size: 13px; word-break: break-all; }
    .admobile-barcode-result__rack { margin: 5px 0 0; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-barcode-result__qty { margin: 8px 0 0; color: #2450a6; font-size: 15px; font-weight: 700; }
    .admobile-inspection-memo-modal[hidden] { display: none; }
    .admobile-inspection-memo-modal { position: fixed; z-index: 1200; inset: 0; display: flex; align-items: flex-end; }
    .admobile-inspection-memo-modal__backdrop { position: absolute; inset: 0; background: rgba(16, 24, 40, .58); }
    .admobile-inspection-memo-modal__content { position: relative; width: 100%; padding: 20px 16px calc(16px + env(safe-area-inset-bottom)); border-radius: 18px 18px 0 0; background: #fff; box-shadow: 0 -8px 24px rgba(16, 24, 40, .16); }
    .admobile-inspection-memo-modal__heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .admobile-inspection-memo-modal__heading p { margin: 0 0 3px; color: #667085; font-size: 12px; font-weight: 700; }
    .admobile-inspection-memo-modal__heading h2 { margin: 0; color: #172033; font-size: 19px; }
    .admobile-inspection-memo-modal__heading button { width: 34px; height: 34px; border: 0; border-radius: 50%; background: #f2f4f7; color: #344054; font-size: 23px; line-height: 1; }
    .admobile-inspection-memo-modal__product { overflow: hidden; margin: 14px 0 12px; color: #475467; font-size: 13px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
    .admobile-inspection-memo-modal label { display: block; margin-bottom: 7px; color: #344054; font-size: 13px; font-weight: 700; }
    .admobile-inspection-memo-modal textarea { display: block; width: 100%; min-height: 136px; padding: 12px; border: 1px solid #aab9d6; border-radius: 9px; color: #172033; font: inherit; font-size: 15px; line-height: 1.5; resize: vertical; outline-color: #3056a8; }
    .admobile-inspection-memo-modal__meta { display: flex; justify-content: space-between; gap: 12px; margin-top: 7px; color: #667085; font-size: 11px; }
    .admobile-inspection-memo-modal__meta strong { color: #475467; font-variant-numeric: tabular-nums; }
    .admobile-inspection-memo-modal__message { min-height: 16px; margin: 8px 0 0; color: #b42318; font-size: 12px; }
    .admobile-inspection-memo-modal__actions { display: grid; grid-template-columns: 88px 1fr; gap: 8px; margin-top: 10px; }
    .admobile-inspection-memo-modal__actions button { min-height: 46px; border: 0; border-radius: 8px; font: inherit; font-size: 14px; font-weight: 700; }
    .admobile-inspection-memo-modal__cancel { background: #eef1f5; color: #475467; }
    .admobile-inspection-memo-modal__save { background: #3056a8; color: #fff; }
</style>

<script>
    (function() {
        var products = <?= json_encode(array_values($stockUnits), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        var orderIdx = <?= (int)($orderIdx ?? 0) ?>;
        var input = document.getElementById('barcode-search-input');
        var message = document.getElementById('barcode-search-message');
        var modal = document.getElementById('barcode-result-modal');
        var resultList = document.getElementById('barcode-result-list');
        var search = document.getElementById('barcode-search');
        var searchSentinel = document.getElementById('barcode-search-sentinel');
        var searchTimer;

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                search.classList.toggle('is-stuck', !entries[0].isIntersecting);
            }, { threshold: 0 }).observe(searchSentinel);
        }

        function normalizeBarcode(value) {
            return String(value || '')
                .replace(/\s+/g, '')
                .replace(/\D/g, '');
        }

        function focusInput() {
            input.focus();
        }

        function closeModal() {
            modal.hidden = true;
            focusInput();
        }

        function renderResult(product) {
            var image = product.product_image
                ? '<img src="' + escapeHtml(product.product_image) + '" alt="">'
                : '<span>이미지 없음</span>';
            var saleStatusClass = product.sale_status === '신상주문'
                ? ' is-new-order'
                : (product.sale_status === '구매대행' ? ' is-purchase-agent' : '');
            var saleStatus = product.sale_status
                ? '<span class="admobile-stock-sale-status' + saleStatusClass + '">' + escapeHtml(product.sale_status) + '</span>'
                : '';

            return '<article class="admobile-barcode-result">' +
                '<div class="admobile-barcode-result__image-wrap">' +
                    saleStatus +
                    '<div class="admobile-barcode-result__image">' + image + '</div>' +
                    '<a class="admobile-stock-product-edit" href="/admobile/product/detail?prd_idx=' + Number(product.pidx || 0) + '&return_to=' + encodeURIComponent('/admobile/order/sheet/stock?idx=' + orderIdx) + '">상품수정</a>' +
                '</div>' +
                '<div class="admobile-barcode-result__content">' +
                    (product.brand_name ? '<p class="admobile-barcode-result__brand">' + escapeHtml(product.brand_name) + '</p>' : '') +
                    '<h3>' + escapeHtml(product.product_name || '상품명 없음') + '</h3>' +
                    '<p class="admobile-barcode-result__barcode">바코드: ' + escapeHtml(product.barcode || '-') + '</p>' +
                    '<p class="admobile-barcode-result__rack">랙: ' + escapeHtml(product.ps_rack_code || '-') + '</p>' +
                    '<p class="admobile-barcode-result__qty">주문수량 ' + Number(product.order_qty || 0).toLocaleString() + '개</p>' +
                    '<nav class="admobile-unit-action-menu" aria-label="상품 작업 메뉴">' +
                        '<button type="button" class="admobile-unit-action-menu__item" data-complete-check data-order-idx="' + orderIdx + '" data-bidx="' + Number(product.bidx || 0) + '" data-pidx="' + Number(product.pidx || 0) + '"' + (product.is_check_complete ? ' disabled' : '') + '>' + (product.is_check_complete ? '체크완료' : '바로 완료체크') + '</button>' +
                        '<a class="admobile-unit-action-menu__item admobile-unit-action-menu__item--primary" href="/admobile/order/sheet/stock/unit?idx=' + orderIdx + '&bidx=' + Number(product.bidx || 0) + '&pidx=' + Number(product.pidx || 0) + '">수량체크하기</a>' +
                        '<button type="button" class="admobile-unit-action-menu__item" data-memo-open data-order-idx="' + orderIdx + '" data-bidx="' + Number(product.bidx || 0) + '" data-pidx="' + Number(product.pidx || 0) + '" data-product-name="' + escapeHtml(product.product_name || '') + '" data-memo="' + escapeHtml(product.stock_inspection_memo || '') + '">' + (product.stock_inspection_memo ? '메모 수정' : '메모 작성') + '</button>' +
                    '</nav>' +
                '</div>' +
            '</article>';
        }

        function escapeHtml(value) {
            var element = document.createElement('span');
            element.textContent = String(value || '');
            return element.innerHTML;
        }

        function searchBarcode() {
            var query = normalizeBarcode(input.value);
            input.value = query;
            message.textContent = '';

            if (query.length === 0) {
                message.textContent = '바코드 숫자를 입력해주세요.';
                return;
            }

            var matches = products.filter(function(product) {
                var barcode = normalizeBarcode(product.barcode);
                return barcode !== '' && barcode.endsWith(query);
            });

            if (matches.length === 0) {
                message.textContent = '일치하는 주문상품이 없습니다.';
                return;
            }

            resultList.innerHTML = matches.map(renderResult).join('');
            modal.hidden = false;
        }

        document.getElementById('barcode-search-clear').addEventListener('click', function() {
            input.value = '';
            message.textContent = '';
            focusInput();
        });

        document.getElementById('barcode-search-button').addEventListener('click', function() {
            input.blur();
            searchBarcode();
        });

        input.addEventListener('input', function() {
            input.value = normalizeBarcode(input.value);
            window.clearTimeout(searchTimer);
            searchTimer = window.setTimeout(function() {
                var query = normalizeBarcode(input.value);
                var exactMatch = products.some(function(product) {
                    return normalizeBarcode(product.barcode) === query;
                });
                if (query.length >= 8 && exactMatch) {
                    searchBarcode();
                }
            }, 250);
        });

        input.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === 'Tab') {
                event.preventDefault();
                searchBarcode();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (modal.hidden && document.activeElement !== input && /^[0-9]$/.test(event.key)) {
                input.value += event.key;
                focusInput();
                event.preventDefault();
            }
        });

        modal.addEventListener('click', function(event) {
            if (event.target.closest('[data-action="close"]')) {
                closeModal();
            }
        });
    })();
</script>

<script>
    (function() {
        var filterButtons = document.querySelectorAll('[data-stock-filter]');
        var cards = document.querySelectorAll('.admobile-stock-card');
        var emptyMessage = document.getElementById('admobile-stock-filter-empty');

        filterButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var filter = button.dataset.stockFilter;
                var visibleCount = 0;

                cards.forEach(function(card) {
                    var isVisible = filter === 'all' || card.dataset.inspectionStatus === filter;
                    card.hidden = !isVisible;
                    if (isVisible) {
                        visibleCount++;
                    }
                });

                filterButtons.forEach(function(filterButton) {
                    var isActive = filterButton === button;
                    filterButton.classList.toggle('is-active', isActive);
                    filterButton.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });
                emptyMessage.hidden = visibleCount > 0;
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
        var activeButton = null;

        function closeModal() {
            modal.hidden = true;
            activeButton = null;
        }

        function updateCount() {
            count.textContent = textarea.value.length;
        }

        document.addEventListener('click', function(event) {
            var button = event.target.closest('[data-memo-open]');
            if (!button) {
                return;
            }

            activeButton = button;
            form.idx.value = button.dataset.orderIdx;
            form.bidx.value = button.dataset.bidx;
            form.pidx.value = button.dataset.pidx;
            textarea.value = button.dataset.memo || '';
            productName.textContent = button.dataset.productName || '상품 메모';
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
                    activeButton.dataset.memo = memo;
                    activeButton.textContent = memo ? '메모 수정' : '메모 작성';
                    var card = activeButton.closest('.admobile-stock-card');
                    if (!card) {
                        closeModal();
                        return;
                    }
                    var preview = card.querySelector('[data-memo-preview]');

                    if (memo && !preview) {
                        preview = document.createElement('p');
                        preview.className = 'admobile-stock-memo';
                        preview.setAttribute('data-memo-preview', '');
                        card.querySelector('.admobile-unit-action-menu').before(preview);
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
        var modal = document.getElementById('product-image-modal');
        var modalImage = document.getElementById('product-image-modal-image');
        var modalName = document.getElementById('product-image-modal-name');

        document.querySelectorAll('.admobile-stock-image-button').forEach(function(button) {
            button.addEventListener('click', function() {
                modalImage.src = button.dataset.image || '';
                modalImage.alt = button.dataset.name || '상품 이미지';
                modalName.textContent = button.dataset.name || '';
                modal.hidden = false;
            });
        });

        modal.addEventListener('click', function(event) {
            if (event.target.closest('[data-action="close"]')) {
                modal.hidden = true;
                modalImage.src = '';
            }
        });
    })();
</script>

<script>
    document.addEventListener('click', function(event) {
        var button = event.target.closest('[data-complete-check]');
        if (!button || button.disabled) {
            return;
        }
        if (!window.confirm('남은 수량을 주문수량 기준으로 바로 체크완료 처리하시겠습니까?')) {
            return;
        }

        button.disabled = true;
        fetch('/admobile/order/sheet/action', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams({
                action_mode: 'inspection_complete',
                idx: button.dataset.orderIdx,
                bidx: button.dataset.bidx,
                pidx: button.dataset.pidx
            })
        })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (!result.success) {
                    throw new Error(result.message || '완료 처리에 실패했습니다.');
                }
                window.location.reload();
            })
            .catch(function(error) {
                window.alert(error.message);
                button.disabled = false;
            });
    });
</script>
