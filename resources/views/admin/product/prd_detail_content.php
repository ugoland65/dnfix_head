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
    $productImage = trim((string)($product_image ?? ''));
    $imageStoragePath = trim((string)($image_storage_path ?? ''));
    $imageLibrary = (isset($image_library) && is_array($image_library)) ? $image_library : [];
    $bottomItems = (isset($content['bottom_items']) && is_array($content['bottom_items'])) ? $content['bottom_items'] : [];
    $bottomPosition = trim((string)($content['bottom_position'] ?? 'bottom'));
    if ($bottomPosition !== 'top') {
        $bottomPosition = 'bottom';
    }
    $bottomDeployVersion = (int)($content['bottom_deploy_version'] ?? 0);
    $bottomDeployVersionCode = trim((string)($content['bottom_deploy_version_code'] ?? ''));
    $bottomUpdatedAt = trim((string)($content['bottom_updated_at'] ?? ''));
    $bottomAdminName = trim((string)($content['bottom_admin_name'] ?? ''));
    $canDeployBottom = !empty($can_deploy_bottom);
    $godoBottomContent = (isset($godo_bottom_content) && is_array($godo_bottom_content)) ? $godo_bottom_content : [];
    $godoBottomHasCode = !empty($godoBottomContent['has_godo_code']);
    $godoBottomRegistered = !empty($godoBottomContent['registered']);
    $godoBottomDeployVersion = (int)($godoBottomContent['deploy_version'] ?? 0);
    $godoBottomDeployVersionCode = trim((string)($godoBottomContent['deploy_version_code'] ?? ''));
    $godoBottomMatchesLocal = !empty($godoBottomContent['matches_local']);
    $godoBottomError = trim((string)($godoBottomContent['error'] ?? ''));
    $godoBottomFound = !empty($godoBottomContent['found']);
    $prdContentDeployOverview = static function (
        bool $hasCode,
        string $error,
        bool $found,
        bool $registered,
        bool $matchesLocal,
        int $intranetVersion
    ): array {
        if (!$hasCode) {
            return ['class' => 'is-empty', 'text' => '상품번호 없음', 'title' => ''];
        }
        if ($error !== '') {
            return ['class' => 'is-error', 'text' => '조회 실패', 'title' => $error];
        }
        if (!$found) {
            return ['class' => 'is-empty', 'text' => '상품 없음', 'title' => ''];
        }
        if ($intranetVersion <= 0) {
            return ['class' => 'is-empty', 'text' => '미저장', 'title' => ''];
        }
        if (!$registered) {
            return ['class' => 'is-empty', 'text' => '미배포', 'title' => ''];
        }
        if ($matchesLocal) {
            return ['class' => 'is-sync', 'text' => '배포됨 · 동기화', 'title' => ''];
        }
        return ['class' => 'is-diff', 'text' => '배포됨 · 다름', 'title' => ''];
    };
    $topDeployOverview = $prdContentDeployOverview(
        $godoHasCode,
        $godoError,
        $godoFound,
        $godoRegistered,
        $godoMatchesLocal,
        $deployVersion
    );
    $bottomDeployOverview = $prdContentDeployOverview(
        $godoBottomHasCode,
        $godoBottomError,
        $godoBottomFound,
        $godoBottomRegistered,
        $godoBottomMatchesLocal,
        $bottomDeployVersion
    );
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700&family=Noto+Sans+KR:wght@400;600;700&display=swap');

.prd-content-layout { display: flex; gap: 16px; align-items: flex-start; }
.prd-content-editor { flex: 1; min-width: 0; }
.section-title-name{ font-size: 15px; font-weight: 600; color: #111827; margin-bottom:5px; }
.prd-content-preview-col { width: 550px; flex-shrink: 0; position: sticky; top: 80px; display: flex; flex-direction: column; gap: 12px; }
.prd-content-section-title { font-weight: 700; margin-bottom: 6px; color: #111827; }
.prd-preview-panel { border: 1px solid #e5e7eb; border-radius: 9px; background: #fff; overflow: hidden; }
.prd-content-preview-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; margin: 0; padding: 8px 10px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; }
.prd-preview-panel.is-collapsed .prd-content-preview-head { border-bottom: 0; }
.prd-preview-toggle { display: inline-flex; align-items: center; gap: 6px; margin: 0; padding: 0; border: 0; background: none; font-weight: 700; color: #111827; cursor: pointer; line-height: 1.4; }
.prd-preview-toggle::before { content: "▼"; font-size: 10px; color: #6b7280; }
.prd-preview-panel.is-collapsed .prd-preview-toggle::before { content: "▶"; }
.prd-preview-panel-body { padding: 10px; }
.prd-preview-panel.is-collapsed .prd-preview-panel-body { display: none; }
.prd-content-preview-label { margin: 0; font-weight: 700; color: #111827; }
.prd-content-preview-actions { display: flex; gap: 6px; flex-wrap: wrap; }
.prd-content-preview-scaler { width: 100%; overflow: hidden; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 9px; }
#prd_content_bottom_preview_scaler { background: #f3f4f6; }
.prd-content-preview-inner { width: 1000px; transform-origin: top left; }
.prd-content-real-preview { position: fixed; inset: 0; z-index: 12000; display: flex; flex-direction: column; }
.prd-content-real-preview[hidden] { display: none; }
.prd-content-real-preview__backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, .72); }
.prd-content-real-preview__panel { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }
.prd-content-real-preview__bar { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 10px 16px; background: #111827; color: #fff; }
.prd-content-real-preview__bar strong { font-size: 14px; }
.prd-content-real-preview__stage { flex: 1; overflow: auto; padding: 20px; }
.prd-content-real-preview__device { margin: 0 auto; background: #ffffff; box-shadow: 0 16px 40px rgba(0, 0, 0, .35); }
.prd-content-real-preview.is-pc .prd-content-real-preview__device { width: 1000px; }
.prd-content-real-preview.is-mobile .prd-content-real-preview__device { width: 390px; border: 10px solid #1f2937; border-radius: 28px; overflow: hidden; }
.prd-content-real-preview .dnfix-goods-contents { width: 100%; }
.prd-content-real-preview .dnfix-goods-bottom { width: 100%; padding: 10px 0 40px; background: #f3f4f6; }
.dnfix-goods-bottom { padding: 10px 0 40px; min-height: 160px; }
.dnfix-goods-bottom-list {
    width: 100%;
    max-width: 1000px;
    margin: 30px auto 0;
    padding: 10px 50px 50px;
    box-sizing: border-box;
    background: #fff;
    border-radius: 13px;
}
.dnfix-goods-bottom-item {
    margin: 0;
    padding: 40px 0;
    border-bottom: 1px solid #ddd;
}
.dnfix-goods-bottom-item:first-child { padding-top: 0; }
.dnfix-goods-bottom-item:last-child { padding-bottom: 0; border-bottom: 0; }
.dnfix-goods-bottom-img {
    display: block;
    width: 600px;
    max-width: 100%;
    height: auto !important;
    margin: 0 auto;
}
.dnfix-goods-bottom-info {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    padding-top: 30px;
    box-sizing: border-box;
    text-align: center;
    font-size: 16px;
    font-weight: 500;
    line-height: 1.7;
    color: #000;
    word-break: keep-all;
    overflow-wrap: break-word;
}
.prd-content-real-preview.is-mobile .dnfix-goods-bottom-list { width: 100%; margin-top: 16px; padding: 10px 12px 28px; border-radius: 10px; }
.prd-content-real-preview.is-mobile .dnfix-goods-bottom-item { padding: 24px 0; }
.prd-content-real-preview.is-mobile .dnfix-goods-bottom-img { width: 100%; }
.prd-content-real-preview.is-mobile .dnfix-goods-bottom-info { padding-top: 16px; font-size: 14px; }

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
.prd-content-title-row h1 { margin: 0; font-size: 20px; font-weight: 600; }
.prd-content-title-row .prd-content-deploy-btn { margin-left: 2px; }
.prd-content-head-status { display: inline-flex; align-items: center; gap: 6px; }
.prd-content-head-status + .prd-content-head-status { margin-left: 2px; padding-left: 10px; border-left: 1px solid #d1d5db; }
.prd-content-head-status-label { font-size: 12px; font-weight: 700; color: #4b5563; }
.prd-content-version { display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 999px; background: #111827; color: #fff; font-size: 12px; font-weight: 700; line-height: 1.4; }
.prd-content-version.is-empty { background: #e5e7eb; color: #6b7280; font-weight: 600; }
.prd-content-version.is-sync { background: #047857; }
.prd-content-version.is-diff { background: #c2410c; }
.prd-content-version.is-error { background: #b91c1c; }
.prd-content-version-code { display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 6px; background: #f3f4f6; color: #111827; font-size: 12px; font-weight: 600; font-family: Consolas, Monaco, monospace; user-select: all; }
.prd-content-meta { margin-top: 12px; color: #6b7280; font-size: 12px; }
.prd-content-meta code { font-family: Consolas, Monaco, monospace; color: #111827; }
.button-wrap .btnstyle1 { margin: 0 4px; vertical-align: middle; }
.prd-content-spec-notice { margin: 0 0 8px; padding: 8px 10px; border: 1px solid #fde68a; background: #fffbeb; border-radius: 6px; color: #92400e; font-size: 13px; line-height: 1.5; }
.prd-content-spec-notice p { margin: 0; }
.prd-content-spec-notice p + p { margin-top: 3px; }

.dnfix-goods-contents { width: 1000px; margin: 0; background: #ffffff; text-align: left; box-sizing: border-box; padding: 30px 64px; overflow: hidden; color: #111; }
.dnfix-goods-contents h2,
.dnfix-goods-contents h4,
.dnfix-goods-contents p,
.dnfix-goods-contents ul,
.dnfix-goods-contents dl,
.dnfix-goods-contents dt,
.dnfix-goods-contents dd { margin: 0; padding: 0; }
.dnfix-goods-contents ul { list-style: none; }
.dnfix-goods-contents .g3-name-en { font-size: 16px; line-height: 120%; color: #999; font-family: 'Nanum Gothic', sans-serif; padding: 0 0 7px !important; }
.dnfix-goods-contents .g3-name { font-size: 28px; color: #111; font-weight: 600; }
.dnfix-goods-contents .g3-explanation { margin-top: 20px; font-size: 14px; line-height: 150%; }
.dnfix-goods-contents .g3-explanation .highlight { display: inline-block; font-weight: 600; font-size: 18px; color: #ff2928; }
.dnfix-goods-contents .g3-explanation .maker-comment { margin: 20px 0; }
.dnfix-goods-contents .g3-explanation .md-comment { margin: 20px 0; }
.dnfix-goods-contents .g3-explanation .maker-comment-title,
.dnfix-goods-contents .g3-explanation .md-comment-title { font-size: inherit; font-weight: 600; line-height: inherit; }
.dnfix-goods-contents .g3-point { margin-top: 30px; }
.dnfix-goods-contents .g3-point-title,
.dnfix-goods-contents .g3-spec-title { display: inline-block; font-family: 'Noto Sans KR', sans-serif; font-size: 13px; font-weight: 600; border-radius: 4px; padding: 1px 10px; box-sizing: border-box; }
.dnfix-goods-contents .g3-point-title { background-color: #ff2928; color: #fff; }
.dnfix-goods-contents .g3-spec-title { background-color: #444; color: #fff; }
.dnfix-goods-contents .g3-point-list { margin-top: 10px; display: flex; flex-direction: column; gap: 5px; }
.dnfix-goods-contents .g3-point-list > li { color: #ff2928; font-size: 15px; height: 20px; line-height: 120%; padding-left: 25px; background-image: url("https://showdang.co.kr/data/dg_image/site/g2_point_icon.png"); background-size: 19px 18px; background-repeat: no-repeat; box-sizing: border-box; }
.dnfix-goods-contents .g3-spec { margin-top: 30px; }
.dnfix-goods-contents .g3-spec-list { margin-top: 10px; display: flex; flex-direction: column; gap: 6px; }
.dnfix-goods-contents .g3-spec-list .g3-spec-row { display: flex; gap: 6px; font-size: 14px; }
.dnfix-goods-contents .g3-spec-list .g3-spec-row dt { color: #777; }
.dnfix-goods-contents .g3-spec-list .g3-spec-row dt::before { content: "●"; font-size: 9px; color: #777; margin-right: 4px; }
.dnfix-goods-contents .g3-spec-note { margin: 12px 0 0; font-size: 12px; line-height: 1.5; font-weight: 400; color: #999; word-break: keep-all; }

.prd-content-real-preview.is-mobile .dnfix-goods-contents { background: #ffffff; border: 0; border-radius: 0; padding: 20px 16px; }
.prd-content-real-preview.is-mobile .g3-name-en { font-size: 13px; line-height: 130%; padding: 0 0 5px !important; }
.prd-content-real-preview.is-mobile .g3-name { font-size: 20px; line-height: 135%; }
.prd-content-real-preview.is-mobile .g3-explanation { margin-top: 12px; font-size: 14px; line-height: 150%; }
.prd-content-real-preview.is-mobile .g3-explanation .highlight { font-size: 16px; }
.prd-content-real-preview.is-mobile .g3-explanation .maker-comment,
.prd-content-real-preview.is-mobile .g3-explanation .md-comment { margin: 12px 0; }
.prd-content-real-preview.is-mobile .g3-point { margin-top: 20px; }
.prd-content-real-preview.is-mobile .g3-point-title,
.prd-content-real-preview.is-mobile .g3-spec-title { font-size: 11px; border-radius: 3px; padding: 0 8px; height: 20px; line-height: 20px; }
.prd-content-real-preview.is-mobile .g3-point-list { gap: 6px; }
.prd-content-real-preview.is-mobile .g3-point-list > li { height: auto; font-size: 14px; line-height: 140%; background-size: 17px 16px; background-position: 0 2px; padding-left: 22px; }
.prd-content-real-preview.is-mobile .g3-spec { margin: 20px 0 0; }
.prd-content-real-preview.is-mobile .g3-spec-list { gap: 5px; }
.prd-content-real-preview.is-mobile .g3-spec-list .g3-spec-row { gap: 4px; font-size: 14px; line-height: 140%; flex-wrap: wrap; }
.prd-content-real-preview.is-mobile .g3-spec-list .g3-spec-row dt::before { font-size: 8px; }
.prd-content-real-preview.is-mobile .g3-spec-note { margin: 10px 0 0; font-size: 11px; }

.prd-content-page-head { margin-bottom: 12px; }
.prd-content-tab-nav {
    display: flex;
    gap: 0;
    margin: 0 0 14px;
    border-bottom: 1px solid #999;
}
.prd-content-tab-btn {
    width: 200px;
    margin: 0 0 -1px;
    padding: 8px 25px;
    /*
    border: 1px solid transparent;
    */
    border: 1px solid #bbb;
    border-bottom: 0;
    background: none;
    color: #333;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    line-height: 1.4;
}
.prd-content-tab-btn.is-active {
    color: #111827;
    background: #fff;
    border-color: #999;
    border-bottom: 1px solid #fff;
}
.prd-content-tab-panel[hidden] { display: none !important; }
.prd-content-bottom-layout { display: flex; gap: 16px; align-items: flex-start; }
.prd-content-bottom-main { flex: 1; min-width: 0; padding-bottom: 8px; }
.prd-content-library-head { margin-bottom: 12px; }
.prd-content-library-head h2 { margin: 0 0 4px; font-size: 16px; color: #111827; }
.prd-content-library-path { margin: 0 0 8px; color: #6b7280; font-size: 12px; line-height: 1.5; }
.prd-content-library-path code { font-family: Consolas, Monaco, monospace; color: #111827; word-break: break-all; }
.prd-content-library-col {
    width: 340px;
    flex-shrink: 0;
    position: sticky;
    top: 80px;
    max-height: calc(100vh - 160px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 10px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
}
.prd-content-library-tools {
    flex-shrink: 0;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #edf0f4;
}
.prd-content-library-box {
    flex: 1;
    min-height: 0;
    overflow: auto;
}
.prd-content-library-col-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 700;
    color: #111827;
}
.prd-content-library-col-head span {
    padding: 1px 6px;
    border-radius: 999px;
    background: #f3f4f6;
    color: #4b5563;
    font-size: 11px;
    font-weight: 700;
}
.prd-content-library-empty { margin: 18px 0 8px; color: #9ca3af; text-align: center; font-size: 12px; line-height: 1.5; }
.prd-content-library-list { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 6px; }
.prd-content-library-item {
    display: block;
    width: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    border-radius: 5px;
    background: #f8fafc;
    text-align: left;
    cursor: pointer;
}
.prd-content-library-item.is-used {
    border-color: #2563eb;
}
.prd-content-library-item img {
    display: block;
    width: 100%;
    aspect-ratio: 1;
    object-fit: contain;
    background: #fff;
}
.prd-content-library-item .prd-content-library-name,
.prd-content-library-item .prd-content-library-meta {
    display: block;
    padding: 3px 5px 0;
    overflow: hidden;
}
.prd-content-library-item .prd-content-library-name {
    color: #374151;
    font-size: 10px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.prd-content-library-item .prd-content-library-meta {
    padding: 1px 5px 4px;
    color: #6b7280;
    font-size: 10px;
    font-weight: 500;
    line-height: 1.35;
    white-space: normal;
}
.prd-content-library-item .prd-content-library-meta span {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.prd-content-bottom-toolbar { display: flex; flex-wrap: nowrap; gap: 6px; }
.prd-content-bottom-toolbar .btnstyle1 { flex: 1; min-width: 0; width: auto; text-align: center; }
.prd-content-bottom-status { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; margin: 0 0 10px; }
.prd-content-bottom-position {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    margin: 0 0 10px;
    padding: 8px 10px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f8fafc;
    font-size: 13px;
    color: #111827;
}
.prd-content-bottom-position > span { font-weight: 700; }
.prd-content-bottom-position label { display: inline-flex; align-items: center; gap: 5px; margin: 0; cursor: pointer; font-weight: 500; }
.prd-content-bottom-position input { margin: 0; }
.prd-content-bottom-list { display: flex; flex-direction: column; gap: 8px; }
.prd-content-bottom-item {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    padding: 10px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
}
.prd-content-bottom-item-order { display: flex; flex-direction: column; gap: 4px; }
.prd-content-bottom-item-order button {
    width: 28px;
    height: 26px;
    padding: 0;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    background: #f8fafc;
    color: #374151;
    cursor: pointer;
}
.prd-content-bottom-item img {
    width: 88px;
    height: 88px;
    object-fit: contain;
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 6px;
    flex-shrink: 0;
}
.prd-content-bottom-item-body { flex: 1; min-width: 0; }
.prd-content-bottom-item-name { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #111827; }
.prd-content-bottom-item-body textarea {
    width: 100%;
    min-height: 62px;
    box-sizing: border-box;
    resize: vertical;
}
.prd-content-bottom-item-remove {
    flex-shrink: 0;
    height: 28px;
    padding: 0 8px;
    border: 1px solid #fecaca;
    border-radius: 4px;
    background: #fff;
    color: #b91c1c;
    cursor: pointer;
}
@media (max-width: 1100px) {
    .prd-content-bottom-layout { flex-direction: column; }
    .prd-content-library-col { width: 100%; position: static; max-height: none; }
}

@media (max-width: 1280px) {
    .prd-content-layout { flex-direction: column; }
    .prd-content-preview-col { width: 100%; position: static; }
}

.preview-list-wrap{
    background: #1e1f21;
    padding: 20px;
    .prdImg {
        width: 200px;
        height: 200px;
        overflow: hidden;
        margin: 0 auto;
        background-color: #fff;
        border: 1px solid #fff;
        box-sizing: border-box;
        border-radius: 8px;
        padding: 11px;
    }
    .prdImg img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    .prdList-info{
        width: 185px;
        margin: 0 auto;
        padding: 10px 0 0 0;
    }

    .prdList-info .prdList-brand-wrap {
        display: flex;
        align-items: center;
        gap: 5px;
        line-height: 100%;
        padding: 0;
        color: #bfbfbf;
    }

    .prdList-info .prdList-brand-wrap .prdList-brand {
        display: inline-block;
        font-size: 12px;
        background-color: #333;
        color: #bfbfbf;
        border-radius: 3px;
        margin-left: -5px !important;
        cursor: pointer;
        padding: 5px 6px;
        line-height: 130%;
    }

    .prdList-info .prdList-name {
        line-height: 120%;
        font-size: 14px;
        font-weight: 400;
        color: #fff;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-wrap: break-word;
        margin: 5px 0;
    }

    .prdList-info .prdList-summary {
        font-size: 12px;
        color: #bbb;
        margin-bottom: 5px;
        line-height: 120%;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        word-wrap: break-word;
    }

    .prdList-info .prdList-price {
        padding-top: 6px;
    }
    .prdList-info .prdList-price strong.goods-price {
        font-size: 16px;
        font-weight: 600;
        color: #fff;
    }
}
</style>

<div class="prd-content-page-head">
    <div class="prd-content-title-row">
        <h1>상품 컨텐츠 관리</h1>
        <span class="prd-content-head-status">
            <span class="prd-content-head-status-label">상단</span>
            <span class="prd-content-version <?= htmlspecialchars($topDeployOverview['class'], ENT_QUOTES, 'UTF-8') ?>"<?php if ($topDeployOverview['title'] !== '') { ?> title="<?= htmlspecialchars($topDeployOverview['title'], ENT_QUOTES, 'UTF-8') ?>"<?php } ?>>
                <?= htmlspecialchars($topDeployOverview['text'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        </span>
        <span class="prd-content-head-status">
            <span class="prd-content-head-status-label">하단</span>
            <span class="prd-content-version <?= htmlspecialchars($bottomDeployOverview['class'], ENT_QUOTES, 'UTF-8') ?>"<?php if ($bottomDeployOverview['title'] !== '') { ?> title="<?= htmlspecialchars($bottomDeployOverview['title'], ENT_QUOTES, 'UTF-8') ?>"<?php } ?>>
                <?= htmlspecialchars($bottomDeployOverview['text'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        </span>
    </div>
</div>

<div class="prd-content-tab-nav" role="tablist">
    <button type="button" class="prd-content-tab-btn is-active" data-content-tab="top" role="tab" aria-selected="true">상단</button>
    <button type="button" class="prd-content-tab-btn" data-content-tab="bottom" role="tab" aria-selected="false">하단</button>
</div>

<div class="prd-content-tab-panel" data-content-tab-panel="top" role="tabpanel">
    <div class="prd-content-library-head">
        <div class="prd-content-bottom-status">
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
                <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm prd-content-deploy-btn" id="prd_content_deploy_btn">현재 버전으로 고도몰 배포</button>
            <?php } else { ?>
                <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm prd-content-deploy-btn" id="prd_content_deploy_btn" <?= $deployVersion > 0 && $godoHasCode ? '' : 'disabled' ?>>현재 버전으로 고도몰 배포</button>
            <?php } ?>
        </div>
    </div>
<div class="prd-content-layout">
    <div class="prd-content-editor">
        <form id="prd_detail_content_form" autocomplete="off">
            <input type="hidden" name="prd_pk" value="<?= $prdPk ?>">

            <table class="table-style">
                <tbody>

                    <tr>
                        <td class="none-bg ">
                            <h2 class="section-title-name">상품 리스트</h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="prd-content-section-title">간략설명</div>
                            <input type="text" name="list_summary" maxlength="255" value="<?= htmlspecialchars((string)($content['list_summary'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="간략설명">
                            <div class="admin-guide-text">- 상품목록에 노출할 간략설명입니다.</div>
                        </td>
                    </tr>

                    <tr>
                        <td class="none-bg" style="height: 15px;"></td>
                    </tr>
                    <tr>
                        <td class="none-bg ">
                            <h2 class="section-title-name">상품 상세페이지</h2>
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

                    <!-- 스펙 -->
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
        <div class="admin-guide-text m-t-8">- 임시저장은 배포버전을 유지합니다. 저장은 새 배포버전을 만들고, 이후 고도몰 배포가 가능합니다.</div>
    </div>

    <div class="prd-content-preview-col">
        <section class="prd-preview-panel" data-preview-panel="detail">
            <div class="prd-content-preview-head">
                <button type="button" class="prd-preview-toggle" data-preview-toggle="detail" aria-expanded="true">디테일 미리보기</button>
                <div class="prd-content-preview-actions">
                    <button type="button" class="btnstyle1 btnstyle1-sm" data-real-preview="pc" data-real-preview-source="top">PC버전 실사이즈</button>
                    <button type="button" class="btnstyle1 btnstyle1-sm" data-real-preview="mobile" data-real-preview-source="top">모바일화면보기</button>
                </div>
            </div>
            <div class="prd-preview-panel-body">
                <div id="prd_content_preview_scaler" class="prd-content-preview-scaler">
                    <div id="prd_content_preview_inner" class="prd-content-preview-inner">
                        <div id="prd_content_preview" class="dnfix-goods-contents" data-pdc-version="<?= htmlspecialchars($deployVersionCode, ENT_QUOTES, 'UTF-8') ?>"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="prd-preview-panel" data-preview-panel="list">
            <div class="prd-content-preview-head">
                <button type="button" class="prd-preview-toggle" data-preview-toggle="list" aria-expanded="true">리스트 미리보기</button>
            </div>
            <div class="prd-preview-panel-body">
                <div class="preview-list-wrap">
                    <?php if ($productImage !== '') { ?>
                    <div class="prdImg">
                        <img src="<?= htmlspecialchars($productImage, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <?php } ?>
                    <div class="prdList-info">
                        <ul class="prdList-brand-wrap">
                            <span class="prdList-brand">브랜드명</span>
                        </ul>
                        <ul class="prdList-name" id="prd_content_list_name"><?= htmlspecialchars((string)($content['korean_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></ul>
                        <ul class="prdList-summary" id="prd_content_list_summary"><?= htmlspecialchars((string)($content['list_summary'] ?? ''), ENT_QUOTES, 'UTF-8') ?></ul>
                        <ul class="prdList-price">
                            <div class="">
                                <ul>
                                    <strong class="goods-price">99,999</strong>
                                </ul>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
</div>

<div class="button-wrap-back"></div>
<div class="button-wrap">
    <button type="button" class="btnstyle1 btnstyle1-lg" id="prd_content_draft_btn" title="배포버전을 유지하고 저장합니다">임시저장</button>
    <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-lg" id="prd_content_save_btn" title="새 배포버전을 만들고 저장합니다">
        <i class="far fa-check-circle"></i> 저장
    </button>
</div>
</div>

<div class="prd-content-tab-panel" data-content-tab-panel="bottom" role="tabpanel" hidden>
    <div class="prd-content-bottom-layout">
        <div class="prd-content-bottom-main">
            <div class="prd-content-library-head">
                <h2>하단 컨텐츠</h2>
                <div class="prd-content-bottom-status">
                    <?php if ($bottomDeployVersion > 0) { ?>
                        <span class="prd-content-version">인트라넷 v<?= $bottomDeployVersion ?></span>
                        <?php if ($bottomDeployVersionCode !== '') { ?>
                            <span class="prd-content-version-code" title="하단 배포코드"><?= htmlspecialchars($bottomDeployVersionCode, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php } ?>
                    <?php } else { ?>
                        <span class="prd-content-version is-empty">인트라넷 하단 배포버전 없음</span>
                    <?php } ?>
                    <?php if (!$godoBottomHasCode) { ?>
                        <span class="prd-content-version is-empty">고도몰 상품번호 없음</span>
                    <?php } elseif ($godoBottomError !== '') { ?>
                        <span class="prd-content-version is-error" title="<?= htmlspecialchars($godoBottomError, ENT_QUOTES, 'UTF-8') ?>">고도몰 조회 실패</span>
                    <?php } elseif (!$godoBottomFound) { ?>
                        <span class="prd-content-version is-empty">고도몰 상품 없음</span>
                    <?php } elseif (!$godoBottomRegistered) { ?>
                        <span class="prd-content-version is-empty">고도몰 하단 미배포</span>
                    <?php } else { ?>
                        <span class="prd-content-version <?= $godoBottomMatchesLocal ? 'is-sync' : 'is-diff' ?>">
                            고도몰 <?= $godoBottomDeployVersion > 0 ? 'v' . $godoBottomDeployVersion : '배포됨' ?><?= $godoBottomMatchesLocal ? ' · 동기화' : ' · 다름' ?>
                        </span>
                        <?php if ($godoBottomDeployVersionCode !== '') { ?>
                            <span class="prd-content-version-code" title="고도몰 하단 배포코드"><?= htmlspecialchars($godoBottomDeployVersionCode, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($canDeployBottom && !$godoBottomMatchesLocal) { ?>
                        <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" id="prd_content_bottom_deploy_btn">현재 버전으로 고도몰 배포</button>
                    <?php } else { ?>
                        <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" id="prd_content_bottom_deploy_btn" <?= $bottomDeployVersion > 0 && $godoBottomHasCode ? '' : 'disabled' ?>>현재 버전으로 고도몰 배포</button>
                    <?php } ?>
                </div>
                <div class="prd-content-bottom-position">
                    <span>출력위치</span>
                    <label>
                        <input type="radio" name="bottom_position" value="top" <?= $bottomPosition === 'top' ? 'checked' : '' ?>>
                        본문상단
                    </label>
                    <label>
                        <input type="radio" name="bottom_position" value="bottom" <?= $bottomPosition !== 'top' ? 'checked' : '' ?>>
                        본문하단
                    </label>
                </div>
                <?php if ($bottomDeployVersion > 0 || $bottomUpdatedAt !== '' || $bottomAdminName !== '') { ?>
                    <p class="prd-content-library-path">
                        <?php if ($bottomDeployVersion > 0) { ?>하단 배포버전 v<?= $bottomDeployVersion ?><?php } ?>
                        <?php if ($bottomAdminName !== '' || $bottomUpdatedAt !== '') { ?>
                            <?= $bottomDeployVersion > 0 ? ' · ' : '' ?>최근 저장<?= $bottomAdminName !== '' ? ': ' . htmlspecialchars($bottomAdminName, ENT_QUOTES, 'UTF-8') : '' ?><?= $bottomUpdatedAt !== '' ? ' · ' . htmlspecialchars($bottomUpdatedAt, ENT_QUOTES, 'UTF-8') : '' ?>
                        <?php } ?>
                    </p>
                <?php } ?>
            </div>
            <div id="prd_content_bottom_list" class="prd-content-bottom-list"></div>
            <p id="prd_content_bottom_empty" class="prd-content-library-empty">우측 라이브러리에서 이미지를 선택하거나, 새 이미지를 업로드하세요.</p>
            <section class="prd-preview-panel" data-preview-panel="bottom" style="margin-top: 16px;">
                <div class="prd-content-preview-head">
                    <button type="button" class="prd-preview-toggle" data-preview-toggle="bottom" aria-expanded="true">하단 미리보기</button>
                    <div class="prd-content-preview-actions">
                        <button type="button" class="btnstyle1 btnstyle1-sm" data-real-preview="pc" data-real-preview-source="bottom">PC버전 실사이즈</button>
                        <button type="button" class="btnstyle1 btnstyle1-sm" data-real-preview="mobile" data-real-preview-source="bottom">모바일화면보기</button>
                    </div>
                </div>
                <div class="prd-preview-panel-body">
                    <div id="prd_content_bottom_preview_scaler" class="prd-content-preview-scaler">
                        <div id="prd_content_bottom_preview_inner" class="prd-content-preview-inner">
                            <div id="prd_content_bottom_preview" class="dnfix-goods-bottom"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <aside class="prd-content-library-col">
            <div class="prd-content-library-tools">
                <?php if ($imageStoragePath !== '') { ?>
                    <p class="prd-content-library-path">저장소 경로: <code><?= htmlspecialchars($imageStoragePath, ENT_QUOTES, 'UTF-8') ?></code></p>
                <?php } else { ?>
                    <p class="prd-content-library-path">이미지 저장소 경로가 없습니다. 상품 정보수집에서 먼저 설정해 주세요.</p>
                <?php } ?>
                <div class="prd-content-bottom-toolbar">
                    <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="prd_content_library_import_btn" <?= $imageStoragePath === '' ? 'disabled' : '' ?>>
                        이미지저장소 갱신
                    </button>
                    <button type="button" class="btnstyle1 btnstyle1-sm" id="prd_content_library_upload_btn" <?= $imageStoragePath === '' ? 'disabled' : '' ?>>
                        이미지 업로드
                    </button>
                    <input type="file" id="prd_content_library_upload_input" accept="image/jpeg,image/png,image/gif,image/webp" multiple hidden>
                </div>
            </div>
            <div class="prd-content-library-box">
                <div class="prd-content-library-col-head">
                    라이브러리
                    <span id="prd_content_library_count"><?= count($imageLibrary) ?></span>
                </div>
                <div id="prd_content_library_list" class="prd-content-library-list" <?= empty($imageLibrary) ? 'hidden' : '' ?>></div>
                <p id="prd_content_library_empty" class="prd-content-library-empty" <?= empty($imageLibrary) ? '' : 'hidden' ?>>
                    아직 목록화한 이미지가 없습니다.
                </p>
            </div>
        </aside>
    </div>
    <div class="button-wrap-back"></div>
    <div class="button-wrap">
        <button type="button" class="btnstyle1 btnstyle1-lg" id="prd_content_bottom_draft_btn" title="하단 배포버전을 유지하고 저장합니다">임시저장</button>
        <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-lg" id="prd_content_bottom_save_btn" title="하단 새 배포버전을 만들고 저장합니다">
            <i class="far fa-check-circle"></i> 저장
        </button>
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
                <div id="prd_content_real_preview_body" class="dnfix-goods-contents"></div>
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
    var bottomDeployVersion = <?= (int)$bottomDeployVersion ?>;
    var godoBottomMatchesLocal = <?= $godoBottomMatchesLocal ? 'true' : 'false' ?>;
    var imageLibraryItems = <?= json_encode($imageLibrary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> || [];
    var bottomItems = <?= json_encode($bottomItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> || [];
    var bottomPosition = <?= json_encode($bottomPosition, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> || 'bottom';

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

    var realPreviewSource = 'top';

    function fitPreview() {
        fitPreviewScaler('prd_content_preview_scaler', 'prd_content_preview_inner', 'detail');
        fitPreviewScaler('prd_content_bottom_preview_scaler', 'prd_content_bottom_preview_inner', 'bottom');
    }

    function fitPreviewScaler(scalerId, innerId, panelName) {
        var scaler = document.getElementById(scalerId);
        var inner = document.getElementById(innerId);
        if (!scaler || !inner) {
            return;
        }
        if ($('[data-preview-panel="' + panelName + '"]').hasClass('is-collapsed')) {
            return;
        }
        var width = scaler.clientWidth;
        if (!(width > 0)) {
            return;
        }
        var scale = width / 1000;
        inner.style.transform = 'scale(' + scale + ')';
        inner.style.width = '1000px';
        scaler.style.height = Math.ceil(inner.scrollHeight * scale) + 'px';
    }

    function bindPreviewImageFit($root) {
        $root.find('img').each(function() {
            if (this.complete) {
                return;
            }
            $(this).one('load.prdDetailContent error.prdDetailContent', fitPreview);
        });
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
            html += '<div class="g3-pdc-version" data-pdc-version="' + escapeHtml(deployVersionCode) + '" hidden></div>';
        }

        if (originalName !== '' || koreanName !== '') {
            html += '<header class="g3-header">';
            if (originalName !== '') {
                html += '<p class="g3-name-en">' + escapeHtml(originalName) + '</p>';
            }
            if (koreanName !== '') {
                html += '<h2 class="g3-name">' + escapeHtml(koreanName) + '</h2>';
            }
            html += '</header>';
        }

        var explanation = '';
        if (title !== '') {
            explanation += '<p class="highlight">' + escapeHtml(title) + '</p>';
        }
        if (makerComment !== '') {
            explanation += '<div class="maker-comment">';
            explanation += '<h4 class="maker-comment-title">[메이커 코멘트]</h4>';
            explanation += nl2br(makerComment);
            explanation += '</div>';
        }
        if (mdComment !== '') {
            explanation += '<div class="md-comment">';
            explanation += '<h4 class="md-comment-title">[MD 코멘트]</h4>';
            explanation += nl2br(mdComment);
            explanation += '</div>';
        }
        if (explanation !== '') {
            html += '<section class="g3-explanation">' + explanation + '</section>';
        }

        if (points.length) {
            html += '<section class="g3-point">';
            html += '<h4 class="g3-point-title">POINT</h4>';
            html += '<ul class="g3-point-list">';
            for (var i = 0; i < points.length; i++) {
                html += '<li>' + escapeHtml(points[i].text) + '</li>';
            }
            html += '</ul></section>';
        }

        if (specs.length) {
            html += '<section class="g3-spec">';
            html += '<h4 class="g3-spec-title">SPEC</h4>';
            html += '<dl class="g3-spec-list">';
            for (var j = 0; j < specs.length; j++) {
                html += '<div class="g3-spec-row">';
                html += '<dt>' + escapeHtml(specs[j].name) + ' :</dt>';
                html += '<dd>' + escapeHtml(specs[j].value) + '</dd>';
                html += '</div>';
            }
            html += '</dl>';
            html += '<p class="g3-spec-note">※ 사이즈, 중량정보는 브랜드(메이커)에서 제공하는 정보를 기준으로 합니다. 개체별, 측정기구에 따라 차이가 있을 수 있습니다.</p>';
            html += '</section>';
        }

        return html;
    }

    function buildBottomPreviewHtml() {
        var position = currentBottomPosition();
        var html = '<section class="dnfix-goods-bottom-list" aria-label="상품 상세 설명">';
        var hasItem = false;
        bottomItems.forEach(function(item) {
            var url = String((item && item.hosting_url) || '');
            var comment = String((item && item.comment) || '');
            var name = String((item && item.filename) || '');
            if (!url) {
                return;
            }
            if (!name) {
                name = url.split('/').pop() || '';
            }
            hasItem = true;
            html += '<figure class="dnfix-goods-bottom-item">'
                + '<img class="dnfix-goods-bottom-img" alt="' + escapeHtml(name || comment) + '" src="' + escapeHtml(url) + '">'
                + (comment !== '' ? '<figcaption class="dnfix-goods-bottom-info">' + nl2br(comment) + '</figcaption>' : '')
                + '</figure>';
        });
        if (!hasItem) {
            html += '<figure class="dnfix-goods-bottom-item"><figcaption class="dnfix-goods-bottom-info">선택한 이미지가 없습니다.</figcaption></figure>';
        }
        html += '</section>';
        $('#prd_content_bottom_preview').attr('data-pdb-position', position);
        $('#prd_content_real_preview_body.dnfix-goods-bottom').attr('data-pdb-position', position);
        return html;
    }

    function currentPreviewHtml() {
        return realPreviewSource === 'bottom' ? buildBottomPreviewHtml() : buildPreviewHtml();
    }

    function syncRealPreview() {
        var $overlay = $('#prd_content_real_preview');
        if ($overlay.prop('hidden')) {
            return;
        }
        $('#prd_content_real_preview_body').html(currentPreviewHtml());
    }

    function closeRealPreview() {
        $('#prd_content_real_preview').prop('hidden', true).removeClass('is-pc is-mobile');
        $('body').css('overflow', '');
    }

    function openRealPreview(mode, source) {
        realPreviewSource = source === 'bottom' ? 'bottom' : 'top';
        var isMobile = mode === 'mobile';
        var $overlay = $('#prd_content_real_preview');
        $overlay
            .toggleClass('is-mobile', isMobile)
            .toggleClass('is-pc', !isMobile)
            .prop('hidden', false);
        $('#prd_content_real_preview_title').text(
            isMobile ? '모바일 화면 · 390px' : 'PC 실사이즈 · 1000px'
        );
        $('#prd_content_real_preview_body')
            .toggleClass('dnfix-goods-contents', realPreviewSource !== 'bottom')
            .toggleClass('dnfix-goods-bottom', realPreviewSource === 'bottom')
            .html(currentPreviewHtml());
        $('body').css('overflow', 'hidden');
    }

    function renderBottomPreview() {
        var $preview = $('#prd_content_bottom_preview');
        $preview.html(buildBottomPreviewHtml());
        bindPreviewImageFit($preview);
        fitPreview();
        if (realPreviewSource === 'bottom') {
            syncRealPreview();
        }
    }

    function renderPreview() {
        var $preview = $('#prd_content_preview');
        $preview.html(buildPreviewHtml());
        $('#prd_content_list_name').text($.trim($('#prd_detail_content_form input[name="korean_name"]').val() || ''));
        $('#prd_content_list_summary').text($.trim($('#prd_detail_content_form input[name="list_summary"]').val() || ''));
        bindPreviewImageFit($preview);
        fitPreview();
        if (realPreviewSource !== 'bottom') {
            syncRealPreview();
        }
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
        openRealPreview($(this).attr('data-real-preview'), $(this).attr('data-real-preview-source'));
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
        var $btn = $(this);
        if ($btn.prop('disabled')) {
            return;
        }
        var confirmMessage = godoMatchesLocal
            ? '고도몰에 이미 같은 버전이 있습니다. v' + deployVersion + '을 다시 배포할까요?'
            : '현재 버전 v' + deployVersion + '을 고도몰에 배포할까요?';
        if (!confirm(confirmMessage)) {
            return;
        }
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

    function collectSavePayload(saveMode) {
        return {
            prd_pk: prdPk,
            save_mode: saveMode,
            original_name: $.trim($('#prd_detail_content_form input[name="original_name"]').val() || ''),
            korean_name: $.trim($('#prd_detail_content_form input[name="korean_name"]').val() || ''),
            list_summary: $.trim($('#prd_detail_content_form input[name="list_summary"]').val() || ''),
            title: $.trim($('#prd_detail_content_form input[name="title"]').val() || ''),
            maker_comment: $('#prd_detail_content_form textarea[name="maker_comment"]').val() || '',
            md_comment: $('#prd_detail_content_form textarea[name="md_comment"]').val() || '',
            summary_points: JSON.stringify(collectSummaryPoints()),
            specs: JSON.stringify(collectSpecs())
        };
    }

    function saveContent(saveMode, $btn) {
        var $buttons = $('#prd_content_draft_btn, #prd_content_save_btn');
        $buttons.prop('disabled', true);
        ajaxRequest('/admin/product/detail_content/save', collectSavePayload(saveMode)).done(function(res) {
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
            $buttons.prop('disabled', false);
        });
    }

    $('#prd_content_draft_btn').on('click', function() {
        saveContent('draft', $(this));
    });
    $('#prd_content_save_btn').on('click', function() {
        saveContent('version', $(this));
    });

    function previewPanelStorageKey() {
        return 'prd_content_preview_panel_' + prdPk;
    }

    function applyPreviewPanelState(name, expanded) {
        var $panel = $('[data-preview-panel="' + name + '"]');
        $panel.toggleClass('is-collapsed', !expanded);
        $panel.find('[data-preview-toggle="' + name + '"]').attr('aria-expanded', expanded ? 'true' : 'false');
        if (name === 'detail' && expanded) {
            fitPreview();
        }
        if (name === 'bottom' && expanded) {
            fitPreview();
        }
    }

    function loadPreviewPanelState() {
        var state = { detail: true, list: true, bottom: true };
        try {
            var saved = sessionStorage.getItem(previewPanelStorageKey());
            if (saved) {
                var parsed = JSON.parse(saved);
                if (typeof parsed.detail === 'boolean') {
                    state.detail = parsed.detail;
                }
                if (typeof parsed.list === 'boolean') {
                    state.list = parsed.list;
                }
                if (typeof parsed.bottom === 'boolean') {
                    state.bottom = parsed.bottom;
                }
            }
        } catch (e) {}
        applyPreviewPanelState('detail', state.detail);
        applyPreviewPanelState('list', state.list);
        applyPreviewPanelState('bottom', state.bottom);
    }

    function savePreviewPanelState() {
        var state = {
            detail: !$('[data-preview-panel="detail"]').hasClass('is-collapsed'),
            list: !$('[data-preview-panel="list"]').hasClass('is-collapsed'),
            bottom: !$('[data-preview-panel="bottom"]').hasClass('is-collapsed')
        };
        try {
            sessionStorage.setItem(previewPanelStorageKey(), JSON.stringify(state));
        } catch (e) {}
    }

    $(document).on('click.prdDetailContent', '[data-preview-toggle]', function() {
        var name = $(this).attr('data-preview-toggle');
        var $panel = $('[data-preview-panel="' + name + '"]');
        applyPreviewPanelState(name, $panel.hasClass('is-collapsed'));
        savePreviewPanelState();
    });

    function activateContentTab(name) {
        var tabName = name === 'bottom' ? 'bottom' : 'top';
        $('[data-content-tab]').removeClass('is-active').attr('aria-selected', 'false');
        $('[data-content-tab="' + tabName + '"]').addClass('is-active').attr('aria-selected', 'true');
        $('[data-content-tab-panel]').attr('hidden', true);
        $('[data-content-tab-panel="' + tabName + '"]').removeAttr('hidden');
        if (tabName === 'top') {
            fitPreview();
        }
        if (tabName === 'bottom') {
            renderBottomPreview();
        }
    }

    $(document).on('click.prdDetailContent', '[data-content-tab]', function() {
        activateContentTab($(this).attr('data-content-tab'));
    });

    function usedBottomUrls() {
        var map = {};
        bottomItems.forEach(function(item) {
            var url = String((item && item.hosting_url) || '');
            if (url) {
                map[url] = true;
            }
        });
        return map;
    }

    function renderBottomList() {
        var $list = $('#prd_content_bottom_list');
        var $empty = $('#prd_content_bottom_empty');
        var html = '';
        bottomItems.forEach(function(item, index) {
            var url = String((item && item.hosting_url) || '');
            var name = String((item && item.filename) || '');
            var comment = String((item && item.comment) || '');
            if (!url) {
                return;
            }
            if (!name) {
                name = url.split('/').pop() || url;
            }
            html += '<div class="prd-content-bottom-item" data-bottom-index="' + index + '">'
                + '<div class="prd-content-bottom-item-order">'
                + '<button type="button" data-bottom-move="up" title="위로">▲</button>'
                + '<button type="button" data-bottom-move="down" title="아래로">▼</button>'
                + '</div>'
                + '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(name) + '">'
                + '<div class="prd-content-bottom-item-body">'
                + '<span class="prd-content-bottom-item-name">' + escapeHtml(name) + '</span>'
                + '<textarea data-bottom-comment="1" placeholder="이미지 코멘트">' + escapeHtml(comment) + '</textarea>'
                + '</div>'
                + '<button type="button" class="prd-content-bottom-item-remove" data-bottom-remove="1">삭제</button>'
                + '</div>';
        });
        $list.html(html);
        if (html) {
            $empty.attr('hidden', true);
        } else {
            $empty.removeAttr('hidden');
        }
        markUsedLibraryItems();
        renderBottomPreview();
    }

    function addBottomItem(item, silent) {
        var url = String((item && item.hosting_url) || '');
        if (!url) {
            return;
        }
        if (usedBottomUrls()[url]) {
            if (!silent) {
                toast2('info', '하단 컨텐츠', '이미 선택된 이미지입니다.');
            }
            return;
        }
        bottomItems.push({
            library_idx: parseInt((item && (item.library_idx || item.idx)) || 0, 10) || 0,
            filename: String((item && item.filename) || ''),
            hosting_url: url,
            comment: String((item && item.comment) || ''),
            width: parseInt((item && item.width) || 0, 10) || 0,
            height: parseInt((item && item.height) || 0, 10) || 0
        });
        renderBottomList();
    }

    function markUsedLibraryItems() {
        var used = usedBottomUrls();
        $('#prd_content_library_list .prd-content-library-item').each(function() {
            var url = String($(this).attr('data-hosting-url') || '');
            $(this).toggleClass('is-used', !!used[url]);
        });
    }

    function renderImageLibrary(items) {
        imageLibraryItems = items || [];
        var $list = $('#prd_content_library_list');
        var $empty = $('#prd_content_library_empty');
        var $count = $('#prd_content_library_count');
        var html = '';
        var count = 0;
        imageLibraryItems.forEach(function(item) {
            var url = String((item && item.hosting_url) || '');
            var name = String((item && item.filename) || '');
            var dimension = String((item && item.dimension_label) || '');
            var fileSize = String((item && item.file_size_label) || '');
            var meta = String((item && item.meta_label) || '');
            var idx = parseInt((item && item.idx) || 0, 10) || 0;
            var width = parseInt((item && item.width) || 0, 10) || 0;
            var height = parseInt((item && item.height) || 0, 10) || 0;
            if (!url) {
                return;
            }
            if (!name) {
                name = url.split('/').pop() || url;
            }
            var metaHtml = '';
            if (dimension || fileSize) {
                metaHtml = '<span class="prd-content-library-meta">'
                    + (dimension ? '<span>' + escapeHtml(dimension) + '</span>' : '')
                    + (fileSize ? '<span>' + escapeHtml(fileSize) + '</span>' : '')
                    + '</span>';
            }
            html += '<button type="button" class="prd-content-library-item" data-library-idx="' + idx + '" data-hosting-url="' + escapeHtml(url) + '" data-filename="' + escapeHtml(name) + '" data-width="' + width + '" data-height="' + height + '" title="' + escapeHtml(name + (meta ? ' · ' + meta : '')) + '">'
                + '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(name) + '">'
                + '<span class="prd-content-library-name">' + escapeHtml(name) + '</span>'
                + metaHtml
                + '</button>';
            count += 1;
        });
        $list.html(html);
        $count.text(count);
        if (html) {
            $list.removeAttr('hidden');
            $empty.attr('hidden', true);
        } else {
            $list.attr('hidden', true);
            $empty.removeAttr('hidden');
        }
        markUsedLibraryItems();
    }

    function currentBottomPosition() {
        var selected = $.trim($('input[name="bottom_position"]:checked').val() || '');
        return selected === 'top' ? 'top' : 'bottom';
    }

    $(document).on('change.prdDetailContent', 'input[name="bottom_position"]', function() {
        bottomPosition = currentBottomPosition();
        renderBottomPreview();
    });

    function saveBottomContent(saveMode) {
        return ajaxRequest('/admin/product/detail_content/bottom/save', {
            prd_pk: prdPk,
            save_mode: saveMode || 'version',
            bottom_items: JSON.stringify(bottomItems),
            bottom_position: currentBottomPosition()
        });
    }

    $('#prd_content_library_import_btn').on('click', function() {
        var $btn = $(this);
        if ($btn.prop('disabled')) {
            return;
        }
        $btn.prop('disabled', true);
        ajaxRequest('/admin/product/detail_content/image_library/import', {
            prd_pk: prdPk
        }).done(function(res) {
            if (res && res.success) {
                renderImageLibrary((res.data && res.data.items) ? res.data.items : []);
                toast2('success', '이미지 라이브러리', res.message || '라이브러리를 불러왔습니다.');
            } else {
                showAlert('이미지 라이브러리', (res && (res.message || res.msg)) || '폴더 이미지를 불러오지 못했습니다.', 'alert2');
            }
        }).fail(function(err) {
            var message = (err && err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : ((err && err.message) ? err.message : '폴더 이미지를 불러오지 못했습니다.');
            showAlert('이미지 라이브러리', message, 'alert2');
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    $('#prd_content_library_upload_btn').on('click', function() {
        if ($(this).prop('disabled')) {
            return;
        }
        $('#prd_content_library_upload_input').val('').trigger('click');
    });

    $('#prd_content_library_upload_input').on('change', function() {
        var files = this.files;
        if (!files || !files.length) {
            return;
        }
        var formData = new FormData();
        formData.append('prd_pk', prdPk);
        Array.prototype.forEach.call(files, function(file) {
            formData.append('images[]', file);
        });
        var $btn = $('#prd_content_library_upload_btn');
        $btn.prop('disabled', true);
        ajaxRequest('/admin/product/detail_content/image_library/upload', formData, {
            processData: false,
            contentType: false
        }).done(function(res) {
            if (res && res.success) {
                renderImageLibrary((res.data && res.data.items) ? res.data.items : imageLibraryItems);
                ((res.data && res.data.added) ? res.data.added : []).forEach(function(item) {
                    addBottomItem(item, true);
                });
                toast2('success', '이미지 업로드', res.message || '이미지를 추가했습니다.');
            } else {
                showAlert('이미지 업로드', (res && (res.message || res.msg)) || '이미지를 업로드하지 못했습니다.', 'alert2');
            }
        }).fail(function(err) {
            var message = (err && err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : ((err && err.message) ? err.message : '이미지를 업로드하지 못했습니다.');
            showAlert('이미지 업로드', message, 'alert2');
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    $(document).on('click.prdDetailContent', '#prd_content_library_list .prd-content-library-item', function() {
        addBottomItem({
            library_idx: $(this).attr('data-library-idx'),
            filename: $(this).attr('data-filename'),
            hosting_url: $(this).attr('data-hosting-url'),
            width: $(this).attr('data-width'),
            height: $(this).attr('data-height')
        });
    });

    $(document).on('click.prdDetailContent', '[data-bottom-move]', function() {
        var index = parseInt($(this).closest('.prd-content-bottom-item').attr('data-bottom-index'), 10);
        var dir = $(this).attr('data-bottom-move') === 'up' ? -1 : 1;
        var next = index + dir;
        if (isNaN(index) || next < 0 || next >= bottomItems.length) {
            return;
        }
        var current = bottomItems[index];
        bottomItems[index] = bottomItems[next];
        bottomItems[next] = current;
        renderBottomList();
    });

    $(document).on('click.prdDetailContent', '[data-bottom-remove]', function() {
        var index = parseInt($(this).closest('.prd-content-bottom-item').attr('data-bottom-index'), 10);
        if (isNaN(index)) {
            return;
        }
        bottomItems.splice(index, 1);
        renderBottomList();
    });

    $(document).on('input.prdDetailContent', '[data-bottom-comment]', function() {
        var index = parseInt($(this).closest('.prd-content-bottom-item').attr('data-bottom-index'), 10);
        if (isNaN(index) || !bottomItems[index]) {
            return;
        }
        bottomItems[index].comment = $(this).val() || '';
        renderBottomPreview();
    });

    $('#prd_content_bottom_draft_btn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true);
        saveBottomContent('draft').done(function(res) {
            if (res && res.success) {
                toast2('success', '하단 컨텐츠', res.message || '임시저장했습니다.');
            } else {
                showAlert('하단 컨텐츠', (res && (res.message || res.msg)) || '저장하지 못했습니다.', 'alert2');
            }
        }).fail(function(err) {
            showAlert('하단 컨텐츠', (err && err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : '저장하지 못했습니다.', 'alert2');
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    $('#prd_content_bottom_save_btn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true);
        saveBottomContent('version').done(function(res) {
            if (res && res.success) {
                toast2('success', '하단 컨텐츠', res.message || '저장했습니다.');
                if (res.data && res.data.bottom_deploy_version) {
                    bottomDeployVersion = parseInt(res.data.bottom_deploy_version, 10) || bottomDeployVersion;
                    $('#prd_content_bottom_deploy_btn').prop('disabled', false);
                }
                if (res.data && res.data.bottom_position) {
                    bottomPosition = res.data.bottom_position === 'top' ? 'top' : 'bottom';
                    $('input[name="bottom_position"][value="' + bottomPosition + '"]').prop('checked', true);
                }
            } else {
                showAlert('하단 컨텐츠', (res && (res.message || res.msg)) || '저장하지 못했습니다.', 'alert2');
            }
        }).fail(function(err) {
            showAlert('하단 컨텐츠', (err && err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : '저장하지 못했습니다.', 'alert2');
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    $('#prd_content_bottom_deploy_btn').on('click', function() {
        var $btn = $(this);
        if ($btn.prop('disabled')) {
            return;
        }
        var confirmMessage = godoBottomMatchesLocal
            ? '고도몰에 이미 같은 하단 버전이 있습니다. v' + bottomDeployVersion + '을 다시 배포할까요?'
            : '현재 하단 버전 v' + bottomDeployVersion + '을 고도몰에 배포할까요?';
        if (!confirm(confirmMessage)) {
            return;
        }
        $btn.prop('disabled', true);
        ajaxRequest('/admin/product/detail_content/bottom/deploy', {
            prd_pk: prdPk
        }).done(function(res) {
            if (res && res.success) {
                toast2('success', '하단 배포', res.message || '고도몰에 배포했습니다.');
                godoBottomMatchesLocal = true;
            } else {
                showAlert('하단 배포', formatDeployError(res), 'alert2');
            }
        }).fail(function(err) {
            showAlert('하단 배포', formatDeployError(err), 'alert2');
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    renderImageLibrary(imageLibraryItems);
    renderBottomList();
    renderPreview();
    loadPreviewPanelState();
})();
</script>
