<?php
$prdPk = (int)($prd_pk ?? 0);
$content = (isset($content) && is_array($content)) ? $content : [];
$summaryPoints = (isset($content['summary_points']) && is_array($content['summary_points'])) ? $content['summary_points'] : [];
$specs = (isset($content['specs']) && is_array($content['specs'])) ? $content['specs'] : [];
$specs = array_values(array_filter($specs, static function ($spec): bool {
    return is_array($spec) && trim((string)($spec['value'] ?? '')) !== '';
}));
if (empty($summaryPoints)) {
    $summaryPoints = [['text' => '']];
}
if (empty($specs)) {
    $specs = [['code' => '', 'name' => '', 'value' => '']];
}
$updatedAt = trim((string)($content['updated_at'] ?? ''));
$adminName = trim((string)($content['admin_name'] ?? ''));
$deployVersion = (int)($content['deploy_version'] ?? 0);
$deployVersionCode = trim((string)($content['deploy_version_code'] ?? ''));
$specsSaved = !empty($content['specs_saved']);
$specsIsRecommended = !empty($content['specs_is_recommended']);
$canRecommendSpecs = !empty($can_recommend_specs);
$canDeploy = !empty($can_deploy);
$godoContent = (isset($godo_content) && is_array($godo_content)) ? $godo_content : [];
$godoHasCode = !empty($godoContent['has_godo_code']);
$godoRegistered = !empty($godoContent['registered']);
$godoDeployVersion = (int)($godoContent['deploy_version'] ?? 0);
$godoDeployVersionCode = trim((string)($godoContent['deploy_version_code'] ?? ''));
$godoMatchesLocal = !empty($godoContent['matches_local']);
$godoError = trim((string)($godoContent['error'] ?? ''));
$godoFound = !empty($godoContent['found']);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700&family=Noto+Sans+KR:wght@400;600;700&display=swap');

.prd-content-layout { display: flex; gap: 16px; align-items: flex-start; }
.prd-content-editor { flex: 1; min-width: 0; }
.prd-content-preview-col { width: 550px; flex-shrink: 0; position: sticky; top: 80px; }
.prd-content-section-title { font-weight: 700; margin-bottom: 6px; color: #111827; }
.prd-content-preview-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; }
.prd-content-preview-label { margin: 0; font-weight: 700; color: #111827; }
.prd-content-preview-actions { display: flex; gap: 6px; flex-wrap: wrap; }
.prd-content-preview-scaler { width: 100%; overflow: hidden; background: #101113; border-radius: 9px; }
.prd-content-preview-inner { width: 1000px; transform-origin: top left; }
.prd-content-real-preview { position: fixed; inset: 0; z-index: 12000; display: flex; flex-direction: column; }
.prd-content-real-preview[hidden] { display: none; }
.prd-content-real-preview__backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, .72); }
.prd-content-real-preview__panel { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }
.prd-content-real-preview__bar { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 10px 16px; background: #111827; color: #fff; }
.prd-content-real-preview__bar strong { font-size: 14px; }
.prd-content-real-preview__stage { flex: 1; overflow: auto; padding: 20px; }
.prd-content-real-preview__device { margin: 0 auto; background: #101113; box-shadow: 0 16px 40px rgba(0, 0, 0, .35); }
.prd-content-real-preview.is-pc .prd-content-real-preview__device { width: 1000px; }
.prd-content-real-preview.is-mobile .prd-content-real-preview__device { width: 390px; border: 10px solid #1f2937; border-radius: 28px; overflow: hidden; }
.prd-content-real-preview .new-goods2-wrap { width: 100%; }

.prd-content-repeat { width: 100%; border-collapse: collapse; }
.prd-content-repeat th,
.prd-content-repeat td { border: 1px solid #e5e7eb; padding: 6px; vertical-align: middle; }
.prd-content-repeat th { background: #f8fafc; text-align: center; font-weight: 600; }
.prd-content-repeat input[type="text"],
.prd-content-repeat textarea { width: 100%; box-sizing: border-box; }
.prd-content-repeat textarea.prd-content-summary-text {
    height: 42px;
    min-height: 42px;
    line-height: 1.4;
    resize: vertical;
}
.prd-content-repeat .prd-content-order { width: 46px; text-align: center; color: #6b7280; }
.prd-content-repeat .prd-content-manage { width: 120px; text-align: center; white-space: nowrap; }
.prd-content-repeat .prd-content-name { width: 180px; }
.prd-content-actions { margin-top: 8px; display: flex; gap: 6px; flex-wrap: wrap; }
.prd-content-title-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.prd-content-title-row h1 { margin: 0; }
.prd-content-title-row .prd-content-deploy-btn { margin-left: 2px; }
.prd-content-version { display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 999px; background: #111827; color: #fff; font-size: 12px; font-weight: 700; line-height: 1.4; }
.prd-content-version.is-empty { background: #e5e7eb; color: #6b7280; font-weight: 600; }
.prd-content-version.is-sync { background: #047857; }
.prd-content-version.is-diff { background: #c2410c; }
.prd-content-version.is-error { background: #b91c1c; }
.prd-content-version-code { display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 6px; background: #f3f4f6; color: #111827; font-size: 12px; font-weight: 600; font-family: Consolas, Monaco, monospace; user-select: all; }
.prd-content-meta { margin-top: 8px; color: #6b7280; font-size: 12px; }
.prd-content-meta code { font-family: Consolas, Monaco, monospace; color: #111827; }
.prd-content-spec-notice { margin: 0 0 8px; padding: 8px 10px; border: 1px solid #fde68a; background: #fffbeb; border-radius: 6px; color: #92400e; font-size: 13px; line-height: 1.5; }
.prd-content-spec-notice p { margin: 0; }
.prd-content-spec-notice p + p { margin-top: 3px; }

.new-goods2-wrap { width: 1000px; margin: 0; background: #101113; border: 1px solid #000; border-top-left-radius: 9px; border-top-right-radius: 9px; padding: 30px 64px; box-sizing: border-box; overflow: hidden; }
.new-goods2-wrap .g2-name-en { text-align: left; font-size: 16px; line-height: 120%; padding: 0 0 10px !important; color: #999; font-family: 'Nanum Gothic', sans-serif; }
.new-goods2-wrap .g2-name { text-align: left; font-size: 28px; color: #eee; font-weight: 600; }
.new-goods2-wrap .g2-explanation { color: #eee; margin-top: 20px; font-size: 15px; line-height: 150%; text-align: left; padding-left: 5px; box-sizing: border-box; }
.new-goods2-wrap .g2-explanation p.highlight { display: inline-block; font-weight: 600; font-size: 17px; color: #ff6e6e; margin-top: 20px; }
.new-goods2-wrap .g2-explanation .maker-comment { margin: 20px 0 20px; }
.new-goods2-wrap .g2-point-title,
.new-goods2-wrap .g2-spec-title { display: inline-block; font-family: 'Noto Sans KR', sans-serif; font-size: 13px; font-weight: 600; border-radius: 4px; padding: 1px 10px; box-sizing: border-box; }
.new-goods2-wrap .g2-point { margin-top: 30px; }
.new-goods2-wrap .g2-point-title-ul { text-align: left; }
.new-goods2-wrap .g2-point-title { background-color: #ffc300; color: #000; }
.new-goods2-wrap .g2-point-box { margin-top: 10px; }
.new-goods2-wrap .g2-point-box li { color: #ffc424; text-align: left; font-size: 15px; height: 28px; line-height: 25px; padding-left: 28px; background-image: url("https://showdang.co.kr/data/dg_image/site/g2_point_icon.png"); background-size: 19px 18px; background-repeat: no-repeat; box-sizing: border-box; background-position: 0 3px; margin-bottom: 2px; }
.new-goods2-wrap .g2-spec { margin-top: 30px; box-sizing: border-box; }
.new-goods2-wrap .g2-spec-title-ul { margin-bottom: 10px; text-align: left; }
.new-goods2-wrap .g2-spec-title { background-color: #444; color: #fff; }
.new-goods2-wrap .g2-spec ul li { text-align: left; line-height: 24px; margin-bottom: 2px; font-size: 14px; color: #eee; padding-left: 3px; box-sizing: border-box; }
.new-goods2-wrap .g2-spec ul li::before { content: "●"; font-size: 9px; line-height: 24px; color: #eee; }
.new-goods2-wrap .g2-spec ul li label { color: #ccc; display: inline-block; margin-right: 4px; }

.prd-content-real-preview.is-mobile .new-goods2-wrap { background-color: #161719; border: 0; border-radius: 0; padding: 0 0 20px; }
.prd-content-real-preview.is-mobile .g2-name-en { margin: 20px 0 0; padding: 0 10px; font-size: 0.9em; color: #aaa; font-family: 'Noto Sans KR', sans-serif; font-weight: 500; line-height: 1.3; }
.prd-content-real-preview.is-mobile .g2-name { margin: 7px 0 0; padding: 0 10px; text-align: left; font-size: 1.3em; line-height: 1.35; font-weight: 600; color: #eee; }
.prd-content-real-preview.is-mobile .g2-explanation { margin-top: 8px; padding: 0 10px; font-size: 1em; line-height: 140%; font-family: 'Noto Sans KR', sans-serif; font-weight: 500; text-align: left; box-sizing: border-box; color: #eee; }
.prd-content-real-preview.is-mobile .g2-explanation p.highlight { display: inline-block; font-family: 'Noto Sans KR', sans-serif; font-weight: 500; font-size: 1.3em; line-height: 140%; color: #ffbb00; margin: 15px 0; }
.prd-content-real-preview.is-mobile .g2-explanation .maker-comment { margin: 12px 0; }
.prd-content-real-preview.is-mobile .g2-point { margin-top: 20px; padding: 0 10px; }
.prd-content-real-preview.is-mobile .g2-point-title,
.prd-content-real-preview.is-mobile .g2-spec-title { color: #fff; width: 60px; height: 20px; text-align: center; line-height: 20px; padding: 0 5px; border-radius: 3px; box-sizing: border-box; font-size: 11px; }
.prd-content-real-preview.is-mobile .g2-point-title { background-color: #ffc300; color: #000; }
.prd-content-real-preview.is-mobile .g2-spec-title { background-color: #444; color: #fff; }
.prd-content-real-preview.is-mobile .g2-point-box { margin-top: 10px; }
.prd-content-real-preview.is-mobile .g2-point-box li { font-size: 1em; line-height: 140%; margin-bottom: 6px; color: #ffc424; height: auto; background-image: url('https://showdang.co.kr/data/dg_image/site/g2_point_icon.png'); background-size: 17px 16px; background-repeat: no-repeat; background-position: 0 2px; padding-left: 22px; box-sizing: border-box; }
.prd-content-real-preview.is-mobile .g2-spec { margin: 30px 0 20px; padding: 0 10px; box-sizing: border-box; }
.prd-content-real-preview.is-mobile .g2-spec-title-ul { padding-bottom: 10px; margin-bottom: 0; }
.prd-content-real-preview.is-mobile .g2-spec ul { box-sizing: border-box; }
.prd-content-real-preview.is-mobile .g2-spec ul li { font-size: 1em; line-height: 140%; box-sizing: border-box; position: relative; padding: 0 0 0 10px; margin-bottom: 5px; font-family: 'Noto Sans KR', sans-serif; vertical-align: top; display: flex; gap: 4px; color: #eee; }
.prd-content-real-preview.is-mobile .g2-spec ul li::before { top: 7px; left: 0; width: 4px; height: 4px; content: ""; display: block; position: absolute; border-radius: 50%; background-color: rgba(196, 196, 198, 1); font-size: 0; line-height: 0; color: transparent; }
.prd-content-real-preview.is-mobile .g2-spec ul li label { display: inline-block; font-size: 1em; color: #aaa; font-weight: 600; width: auto !important; box-sizing: border-box; margin: 0 !important; padding: 0 !important; }
.prd-content-real-preview.is-mobile { font-size: 14px; }

@media (max-width: 1280px) {
    .prd-content-layout { flex-direction: column; }
    .prd-content-preview-col { width: 100%; position: static; }
}
</style>

<div class="prd-content-layout">
    <div class="prd-content-editor">
        <form id="prd_detail_content_form" autocomplete="off">
            <input type="hidden" name="prd_pk" value="<?= $prdPk ?>">

            <table class="table-style">
                <tbody>
                    <tr>
                        <td class="none-bg title">
                            <div class="prd-content-title-row">
                                <h1>상품 컨텐츠 관리</h1>
                                <?php if ($deployVersion > 0) { ?>
                                    <span class="prd-content-version">인트라넷 v<?= $deployVersion ?></span>
                                    <?php if ($deployVersionCode !== '') { ?>
                                        <span class="prd-content-version-code" title="고도몰 배포서버 비교용 코드"><?= htmlspecialchars($deployVersionCode, ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php } ?>
                                <?php } else { ?>
                                    <span class="prd-content-version is-empty">인트라넷 배포버전 없음</span>
                                <?php } ?>
                                <?php if (!$godoHasCode) { ?>
                                    <span class="prd-content-version is-empty">고도몰 상품번호 없음</span>
                                <?php } elseif ($godoError !== '') { ?>
                                    <span class="prd-content-version is-error" title="<?= htmlspecialchars($godoError, ENT_QUOTES, 'UTF-8') ?>">고도몰 조회 실패</span>
                                <?php } elseif (!$godoFound) { ?>
                                    <span class="prd-content-version is-empty">고도몰 상품 없음</span>
                                <?php } elseif (!$godoRegistered) { ?>
                                    <span class="prd-content-version is-empty">고도몰 미배포</span>
                                <?php } else { ?>
                                    <span class="prd-content-version <?= $godoMatchesLocal ? 'is-sync' : 'is-diff' ?>">
                                        고도몰 <?= $godoDeployVersion > 0 ? 'v' . $godoDeployVersion : '배포됨' ?><?= $godoMatchesLocal ? ' · 동기화' : ' · 다름' ?>
                                    </span>
                                    <?php if ($godoDeployVersionCode !== '') { ?>
                                        <span class="prd-content-version-code" title="고도몰에 저장된 배포코드"><?= htmlspecialchars($godoDeployVersionCode, ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($canDeploy && !$godoMatchesLocal) { ?>
                                    <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm prd-content-deploy-btn" id="prd_content_deploy_btn">
                                        현재 버전으로 고도몰 배포
                                    </button>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="prd-content-section-title">상품원어명</div>
                            <input type="text" name="original_name" maxlength="255" value="<?= htmlspecialchars((string)($content['original_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="일본어 또는 중국어 상품명">
                            <div class="admin-guide-text">- 상세페이지에 노출할 원어명입니다. 미저장 시 상품 DB 원어명을 불러옵니다.</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="prd-content-section-title">상품한국명</div>
                            <input type="text" name="korean_name" maxlength="255" value="<?= htmlspecialchars((string)($content['korean_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="한국어 상품명">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="prd-content-section-title">상품 타이틀</div>
                            <input type="text" name="title" maxlength="500" value="<?= htmlspecialchars((string)($content['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="상세페이지 타이틀">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="prd-content-section-title">메이커 코멘트</div>
                            <textarea name="maker_comment" rows="6"><?= htmlspecialchars((string)($content['maker_comment'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="prd-content-section-title">MD 코멘트</div>
                            <textarea name="md_comment" rows="6"><?= htmlspecialchars((string)($content['md_comment'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="prd-content-section-title">상품요약 포인트</div>
                            <table class="prd-content-repeat">
                                <thead>
                                    <tr>
                                        <th class="prd-content-order">순서</th>
                                        <th>요약 포인트</th>
                                        <th class="prd-content-manage">관리</th>
                                    </tr>
                                </thead>
                                <tbody id="prd_content_summary_list">
                                    <?php foreach ($summaryPoints as $index => $point) { ?>
                                        <tr class="prd-content-summary-row">
                                            <td class="prd-content-order"><?= $index + 1 ?></td>
                                            <td>
                                                <textarea name="summary_point_text[]" class="prd-content-summary-text" rows="2" placeholder="상세페이지에 넣을 요약 포인트를 입력하세요"><?= htmlspecialchars((string)($point['text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                            </td>
                                            <td class="prd-content-manage">
                                                <button type="button" class="btnstyle1 btnstyle1-xs" data-move="up">▲</button>
                                                <button type="button" class="btnstyle1 btnstyle1-xs" data-move="down">▼</button>
                                                <button type="button" class="btnstyle1 btnstyle1-xs" data-remove="summary">삭제</button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div class="prd-content-actions">
                                <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" id="prd_content_summary_add">+ 요약 포인트 추가</button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="prd-content-section-title">스펙</div>
                            <div id="prd_content_spec_notice" class="prd-content-spec-notice" <?= ($specsSaved && !$specsIsRecommended) ? 'hidden' : '' ?>>
                                <p data-notice="not-created" <?= $specsSaved ? 'hidden' : '' ?>>스펙정보가 아직 생성되지 않았습니다.</p>
                                <p data-notice="recommended" <?= $specsIsRecommended ? '' : 'hidden' ?>>추천값을 미리 넣어두었습니다. 확인 후 저장해 주세요.</p>
                            </div>
                            <table class="prd-content-repeat">
                                <thead>
                                    <tr>
                                        <th class="prd-content-order">순서</th>
                                        <th class="prd-content-name">항목명</th>
                                        <th>내용</th>
                                        <th class="prd-content-manage">관리</th>
                                    </tr>
                                </thead>
                                <tbody id="prd_content_spec_list">
                                    <?php foreach ($specs as $index => $spec) { ?>
                                        <tr class="prd-content-spec-row">
                                            <td class="prd-content-order"><?= $index + 1 ?></td>
                                            <td>
                                                <input type="hidden" name="spec_code[]" value="<?= htmlspecialchars((string)($spec['code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="text" name="spec_name[]" value="<?= htmlspecialchars((string)($spec['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="예: 전장, 재질">
                                            </td>
                                            <td>
                                                <input type="text" name="spec_value[]" value="<?= htmlspecialchars((string)($spec['value'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="예: 180mm">
                                            </td>
                                            <td class="prd-content-manage">
                                                <button type="button" class="btnstyle1 btnstyle1-xs" data-move="up">▲</button>
                                                <button type="button" class="btnstyle1 btnstyle1-xs" data-move="down">▼</button>
                                                <button type="button" class="btnstyle1 btnstyle1-xs" data-remove="spec">삭제</button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div class="prd-content-actions">
                                <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" id="prd_content_spec_add">+ 스펙 항목 추가</button>
                                <?php if ($canRecommendSpecs) { ?>
                                    <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" id="prd_content_spec_recommend">추천값으로 생성</button>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </form>

        <div class="text-center m-t-10">
            <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-lg" id="prd_content_save_btn">
                <i class="far fa-check-circle"></i> 저장
            </button>
            <?php if ($deployVersion > 0 || $updatedAt !== '' || $adminName !== '') { ?>
                <div class="prd-content-meta">
                    <?php if ($deployVersion > 0) { ?>배포버전 v<?= $deployVersion ?><?php } ?>
                    <?php if ($deployVersionCode !== '') { ?><?= $deployVersion > 0 ? ' · ' : '' ?><code><?= htmlspecialchars($deployVersionCode, ENT_QUOTES, 'UTF-8') ?></code><?php } ?>
                    <?php if ($updatedAt !== '' || $adminName !== '') { ?>
                        <?= ($deployVersion > 0 || $deployVersionCode !== '') ? ' · ' : '' ?>최근 저장<?= $adminName !== '' ? ': ' . htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') : '' ?><?= $updatedAt !== '' ? ' · ' . htmlspecialchars($updatedAt, ENT_QUOTES, 'UTF-8') : '' ?>
                    <?php } ?>
                    <?php if ($godoHasCode) { ?>
                        · 고도몰
                        <?php if ($godoError !== '') { ?>
                            조회 실패
                        <?php } elseif (!$godoFound) { ?>
                            상품 없음
                        <?php } elseif (!$godoRegistered) { ?>
                            미배포
                        <?php } else { ?>
                            <?= $godoDeployVersion > 0 ? 'v' . $godoDeployVersion : '배포됨' ?><?= $godoMatchesLocal ? ' 동기화' : ' 다름' ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="prd-content-preview-col">
        <div class="prd-content-preview-head">
            <div class="prd-content-preview-label">미리보기</div>
            <div class="prd-content-preview-actions">
                <button type="button" class="btnstyle1 btnstyle1-sm" data-real-preview="pc">PC버전 실사이즈</button>
                <button type="button" class="btnstyle1 btnstyle1-sm" data-real-preview="mobile">모바일화면보기</button>
            </div>
        </div>
        <div id="prd_content_preview_scaler" class="prd-content-preview-scaler">
            <div id="prd_content_preview_inner" class="prd-content-preview-inner">
                <div id="prd_content_preview" class="new-goods2-wrap" data-pdc-version="<?= htmlspecialchars($deployVersionCode, ENT_QUOTES, 'UTF-8') ?>"></div>
            </div>
        </div>
    </div>
</div>

<div id="prd_content_real_preview" class="prd-content-real-preview" hidden>
    <div class="prd-content-real-preview__backdrop" data-real-preview-close="1"></div>
    <div class="prd-content-real-preview__panel">
        <div class="prd-content-real-preview__bar">
            <strong id="prd_content_real_preview_title">실사이즈 미리보기</strong>
            <button type="button" class="btnstyle1 btnstyle1-sm" data-real-preview-close="1">닫기</button>
        </div>
        <div class="prd-content-real-preview__stage">
            <div class="prd-content-real-preview__device">
                <div id="prd_content_real_preview_body" class="new-goods2-wrap"></div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var prdPk = <?= $prdPk ?>;
    var specsSaved = <?= $specsSaved ? 'true' : 'false' ?>;
    var specsRecommended = <?= $specsIsRecommended ? 'true' : 'false' ?>;
    var deployVersion = <?= (int)$deployVersion ?>;
    var deployVersionCode = <?= json_encode($deployVersionCode, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var godoMatchesLocal = <?= $godoMatchesLocal ? 'true' : 'false' ?>;

    function escapeHtml(text) {
        return $('<div>').text(text || '').html();
    }

    function nl2br(text) {
        return escapeHtml(text).replace(/\r\n|\r|\n/g, '<br>');
    }

    function formatDeployError(err) {
        var res = err || {};
        if (res.responseJSON) {
            res = res.responseJSON;
        }
        var message = '';
        if (typeof res.message === 'string' && res.message !== '') {
            message = res.message;
        } else if (typeof res.msg === 'string' && res.msg !== '') {
            message = res.msg;
        }
        if (!message && res.debug) {
            var debugLines = [];
            if (res.stage) debugLines.push('단계: ' + res.stage);
            if (res.error_code) debugLines.push('코드: ' + res.error_code);
            if (res.debug.godo_http_code) debugLines.push('고도몰 HTTP: ' + res.debug.godo_http_code);
            if (res.debug.godo_url) debugLines.push('고도몰 URL: ' + res.debug.godo_url);
            if (res.debug.godo_raw) debugLines.push('고도몰 응답: ' + res.debug.godo_raw);
            if (res.debug.log_error) debugLines.push('배포로그 오류: ' + res.debug.log_error);
            if (res.debug.exception) debugLines.push('예외: ' + res.debug.exception + ' @ ' + (res.debug.file || ''));
            message = debugLines.join('\n');
        }
        return nl2br(message || '배포에 실패했습니다. 브라우저 네트워크 탭에서 /deploy 응답을 확인해 주세요.');
    }

    function refreshOrder($list) {
        $list.children('tr').each(function(index) {
            $(this).find('.prd-content-order').text(index + 1);
        });
    }

    function ensureMinRow($list, builder) {
        if ($list.children('tr').length === 0) {
            $list.append(builder());
            refreshOrder($list);
        }
    }

    function summaryRowHtml(text) {
        return ''
            + '<tr class="prd-content-summary-row">'
            + '  <td class="prd-content-order"></td>'
            + '  <td><textarea name="summary_point_text[]" class="prd-content-summary-text" rows="2" placeholder="상세페이지에 넣을 요약 포인트를 입력하세요">' + escapeHtml(text || '') + '</textarea></td>'
            + '  <td class="prd-content-manage">'
            + '    <button type="button" class="btnstyle1 btnstyle1-xs" data-move="up">▲</button>'
            + '    <button type="button" class="btnstyle1 btnstyle1-xs" data-move="down">▼</button>'
            + '    <button type="button" class="btnstyle1 btnstyle1-xs" data-remove="summary">삭제</button>'
            + '  </td>'
            + '</tr>';
    }

    function specRowHtml(name, value, code) {
        return ''
            + '<tr class="prd-content-spec-row">'
            + '  <td class="prd-content-order"></td>'
            + '  <td>'
            + '    <input type="hidden" name="spec_code[]" value="' + escapeHtml(code || '') + '">'
            + '    <input type="text" name="spec_name[]" value="' + escapeHtml(name || '') + '" placeholder="예: 전장, 재질">'
            + '  </td>'
            + '  <td><input type="text" name="spec_value[]" value="' + escapeHtml(value || '') + '" placeholder="예: 180mm"></td>'
            + '  <td class="prd-content-manage">'
            + '    <button type="button" class="btnstyle1 btnstyle1-xs" data-move="up">▲</button>'
            + '    <button type="button" class="btnstyle1 btnstyle1-xs" data-move="down">▼</button>'
            + '    <button type="button" class="btnstyle1 btnstyle1-xs" data-remove="spec">삭제</button>'
            + '  </td>'
            + '</tr>';
    }

    function showSpecNotice(recommendedApplied) {
        specsRecommended = !!recommendedApplied;
        var $notice = $('#prd_content_spec_notice');
        var showNotCreated = !specsSaved;
        $notice.find('[data-notice="not-created"]').prop('hidden', !showNotCreated);
        $notice.find('[data-notice="recommended"]').prop('hidden', !specsRecommended);
        $notice.prop('hidden', !showNotCreated && !specsRecommended);
        if (specsRecommended) {
            $notice.find('[data-notice="recommended"]').text(
                specsSaved
                    ? '추천값으로 다시 채웠습니다. 확인 후 저장해 주세요.'
                    : '추천값을 미리 넣어두었습니다. 확인 후 저장해 주세요.'
            );
        }
    }

    function applyRecommendedSpecs(specs) {
        var $list = $('#prd_content_spec_list');
        $list.empty();
        var filled = [];
        if (specs && specs.length) {
            for (var i = 0; i < specs.length; i++) {
                if ($.trim(specs[i].value || '') !== '') {
                    filled.push(specs[i]);
                }
            }
        }
        if (!filled.length) {
            $list.append(specRowHtml('', '', ''));
        } else {
            for (var j = 0; j < filled.length; j++) {
                $list.append(specRowHtml(filled[j].name, filled[j].value, filled[j].code));
            }
        }
        refreshOrder($list);
        showSpecNotice(true);
        renderPreview();
    }

    function collectSummaryPoints() {
        var points = [];
        $('#prd_content_summary_list textarea[name="summary_point_text[]"]').each(function() {
            var text = $.trim($(this).val() || '');
            if (text !== '') {
                points.push({ text: text });
            }
        });
        return points;
    }

    function collectSpecs() {
        var specs = [];
        $('#prd_content_spec_list .prd-content-spec-row').each(function() {
            var name = $.trim($(this).find('input[name="spec_name[]"]').val() || '');
            var value = $.trim($(this).find('input[name="spec_value[]"]').val() || '');
            var code = $.trim($(this).find('input[name="spec_code[]"]').val() || '');
            if (value !== '') {
                specs.push({ code: code, name: name, value: value });
            }
        });
        return specs;
    }

    function fitPreview() {
        var scaler = document.getElementById('prd_content_preview_scaler');
        var inner = document.getElementById('prd_content_preview_inner');
        if (!scaler || !inner) {
            return;
        }
        var scale = scaler.clientWidth / 1000;
        if (!(scale > 0)) {
            scale = 1;
        }
        inner.style.transform = 'scale(' + scale + ')';
        inner.style.width = '1000px';
        scaler.style.height = Math.ceil(inner.scrollHeight * scale) + 'px';
    }

    function buildPreviewHtml() {
        var originalName = $.trim($('#prd_detail_content_form input[name="original_name"]').val() || '');
        var koreanName = $.trim($('#prd_detail_content_form input[name="korean_name"]').val() || '');
        var title = $.trim($('#prd_detail_content_form input[name="title"]').val() || '');
        var makerComment = $.trim($('#prd_detail_content_form textarea[name="maker_comment"]').val() || '');
        var mdComment = $.trim($('#prd_detail_content_form textarea[name="md_comment"]').val() || '');
        var points = collectSummaryPoints();
        var specs = collectSpecs();

        var html = '';
        if (deployVersionCode) {
            html += '<div class="g2-pdc-version" data-pdc-version="' + escapeHtml(deployVersionCode) + '" hidden></div>';
        }
        html += '<div class="g2-name-en">' + escapeHtml(originalName) + '</div>';
        html += '<div class="g2-name">' + escapeHtml(koreanName) + '</div>';

        var explanation = '';
        if (title !== '') {
            explanation += '<p class="highlight">' + escapeHtml(title) + '</p>';
        }
        if (makerComment !== '') {
            explanation += '<div class="maker-comment">[메이커 코멘트]<br>' + nl2br(makerComment) + '</div>';
        }
        if (mdComment !== '') {
            explanation += '<div class="maker-comment">[MD 코멘트]<br>' + nl2br(mdComment) + '</div>';
        }
        if (explanation !== '') {
            html += '<div class="g2-explanation">' + explanation + '</div>';
        }

        if (points.length) {
            html += '<div class="g2-point">';
            html += '  <ul class="g2-point-title-ul"><div class="g2-point-title">POINT</div></ul>';
            html += '  <ul class="g2-point-box">';
            for (var i = 0; i < points.length; i++) {
                html += '<li>' + escapeHtml(points[i].text) + '</li>';
            }
            html += '  </ul>';
            html += '</div>';
        }

        if (specs.length) {
            html += '<div class="g2-spec">';
            html += '  <ul class="g2-spec-title-ul"><div class="g2-spec-title">SPEC</div></ul>';
            html += '  <ul>';
            for (var j = 0; j < specs.length; j++) {
                var specName = specs[j].name ? escapeHtml(specs[j].name) + ' :' : '';
                html += '<li>';
                if (specName !== '') {
                    html += '<label>' + specName + '</label>';
                }
                html += ' ' + escapeHtml(specs[j].value);
                html += '</li>';
            }
            html += '  </ul>';
            html += '</div>';
        }

        return html;
    }

    function syncRealPreview() {
        var $overlay = $('#prd_content_real_preview');
        if ($overlay.prop('hidden')) {
            return;
        }
        $('#prd_content_real_preview_body').html(buildPreviewHtml());
    }

    function closeRealPreview() {
        $('#prd_content_real_preview').prop('hidden', true).removeClass('is-pc is-mobile');
        $('body').css('overflow', '');
    }

    function openRealPreview(mode) {
        var isMobile = mode === 'mobile';
        var $overlay = $('#prd_content_real_preview');
        $overlay
            .toggleClass('is-mobile', isMobile)
            .toggleClass('is-pc', !isMobile)
            .prop('hidden', false);
        $('#prd_content_real_preview_title').text(
            isMobile ? '모바일 화면 · 390px' : 'PC 실사이즈 · 1000px'
        );
        $('#prd_content_real_preview_body').html(buildPreviewHtml());
        $('body').css('overflow', 'hidden');
    }

    function renderPreview() {
        $('#prd_content_preview').html(buildPreviewHtml());
        fitPreview();
        syncRealPreview();
    }

    $('#prd_content_summary_add').on('click', function() {
        var $list = $('#prd_content_summary_list');
        $list.append(summaryRowHtml(''));
        refreshOrder($list);
        renderPreview();
    });

    $('#prd_content_spec_add').on('click', function() {
        var $list = $('#prd_content_spec_list');
        $list.append(specRowHtml('', '', ''));
        refreshOrder($list);
        renderPreview();
    });

    $('#prd_content_spec_recommend').on('click', function() {
        var hasFilled = collectSpecs().length > 0;
        if (hasFilled && !confirm('현재 스펙을 추천값으로 다시 채울까요?')) {
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        ajaxRequest('/admin/product/detail_content/recommend_specs', {
            prd_pk: prdPk
        }).done(function(res) {
            if (res && res.success && res.data && res.data.specs) {
                applyRecommendedSpecs(res.data.specs);
                toast2('success', '상품 컨텐츠', res.message || '추천값을 생성했습니다.');
            } else {
                showAlert('Error', (res && (res.message || res.msg)) || '추천값 생성에 실패했습니다.', 'alert2');
            }
        }).fail(function(err) {
            showAlert('Error', (err && err.message) ? err.message : '에러', 'alert2');
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    $(document).off('.prdDetailContent').on('click.prdDetailContent', '#prd_content_summary_list [data-remove], #prd_content_spec_list [data-remove]', function() {
        var type = $(this).attr('data-remove');
        var $list = type === 'spec' ? $('#prd_content_spec_list') : $('#prd_content_summary_list');
        $(this).closest('tr').remove();
        ensureMinRow($list, type === 'spec' ? function() { return specRowHtml('', '', ''); } : function() { return summaryRowHtml(''); });
        refreshOrder($list);
        renderPreview();
    });

    $(document).on('click.prdDetailContent', '#prd_content_summary_list [data-move], #prd_content_spec_list [data-move]', function() {
        var direction = $(this).attr('data-move');
        var $row = $(this).closest('tr');
        var $list = $row.parent();
        if (direction === 'up') {
            $row.prev('tr').before($row);
        } else {
            $row.next('tr').after($row);
        }
        refreshOrder($list);
        renderPreview();
    });

    $(document).on('click.prdDetailContent', '[data-real-preview]', function() {
        openRealPreview($(this).attr('data-real-preview'));
    });
    $(document).on('click.prdDetailContent', '[data-real-preview-close]', closeRealPreview);
    $(document).off('keydown.prdDetailContentReal').on('keydown.prdDetailContentReal', function(event) {
        if (event.key === 'Escape' && !$('#prd_content_real_preview').prop('hidden')) {
            closeRealPreview();
        }
    });

    $(document).on('input.prdDetailContent change.prdDetailContent', '#prd_detail_content_form input, #prd_detail_content_form textarea', renderPreview);
    $(window).off('resize.prdDetailContent').on('resize.prdDetailContent', fitPreview);

    $('#prd_content_deploy_btn').on('click', function() {
        var confirmMessage = godoMatchesLocal
            ? '고도몰에 이미 같은 버전이 있습니다. v' + deployVersion + '을 다시 배포할까요?'
            : '현재 버전 v' + deployVersion + '을 고도몰에 배포할까요?';
        if (!confirm(confirmMessage)) {
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        ajaxRequest('/admin/product/detail_content/deploy', {
            prd_pk: prdPk
        }).done(function(res) {
            if (res && res.success) {
                toast2('success', '상품 컨텐츠', res.message || '고도몰에 배포했습니다.');
                if (typeof prdInfo === 'object' && typeof prdInfo.mode === 'function') {
                    prdInfo.mode('', 'content');
                }
            } else {
                showAlert('고도몰 배포 실패', formatDeployError(res), 'alert2');
            }
        }).fail(function(err) {
            showAlert('고도몰 배포 실패', formatDeployError(err), 'alert2');
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    $('#prd_content_save_btn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true);
        ajaxRequest('/admin/product/detail_content/save', {
            prd_pk: prdPk,
            original_name: $.trim($('#prd_detail_content_form input[name="original_name"]').val() || ''),
            korean_name: $.trim($('#prd_detail_content_form input[name="korean_name"]').val() || ''),
            title: $.trim($('#prd_detail_content_form input[name="title"]').val() || ''),
            maker_comment: $('#prd_detail_content_form textarea[name="maker_comment"]').val() || '',
            md_comment: $('#prd_detail_content_form textarea[name="md_comment"]').val() || '',
            summary_points: JSON.stringify(collectSummaryPoints()),
            specs: JSON.stringify(collectSpecs())
        }).done(function(res) {
            if (res && res.success) {
                toast2('success', '상품 컨텐츠', res.message || '저장했습니다.');
                if (typeof prdInfo === 'object' && typeof prdInfo.mode === 'function') {
                    prdInfo.mode('', 'content');
                }
            } else {
                showAlert('Error', (res && (res.message || res.msg)) || '저장에 실패했습니다.', 'alert2');
            }
        }).fail(function(err) {
            showAlert('Error', (err && err.message) ? err.message : '에러', 'alert2');
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    renderPreview();
})();
</script>
