<?php
$pageGroup = 'admobile';
include dirname(__DIR__, 4) . '/admin2/lib/inc_common.php';

$documentTitle = trim((string)($pageTitle ?? '모바일 관리자'));
$adminName = trim((string)($auth['ad_name'] ?? ($_ad_name ?? '관리자')));
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= htmlspecialchars($documentTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        :root { color-scheme: light; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        * { box-sizing: border-box; }
        body { margin: 0; min-width: 320px; background: #f3f5f8; color: #172033; }
        .admobile-header { position: relative; display: flex; align-items: center; min-height: 56px; padding: 12px 16px; background: #121b29; border-bottom: 1px solid #0d1420; }
        .admobile-header h1 { position: absolute; left: 50%; margin: 0; line-height: 0; transform: translateX(-50%); }
        .admobile-logo { display: block; }
        .admobile-logo img { display: block; width: 154px; height: auto; }
        .admobile-menu-toggle { position: static; }
        .admobile-menu-toggle summary { display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; border: 1px solid #526174; border-radius: 6px; color: #e6ebf2; cursor: pointer; font-size: 20px; list-style: none; }
        .admobile-menu-toggle summary::-webkit-details-marker { display: none; }
        .admobile-menu-toggle summary::before { content: "☰"; }
        .admobile-menu-toggle[open] summary::before { content: "×"; font-size: 24px; }
        .admobile-menu-toggle[open]::after { position: fixed; z-index: 1300; inset: 0; background: rgba(16, 24, 40, .45); content: ""; }
        .admobile-navigation { position: fixed; z-index: 1301; top: 0; bottom: 0; left: 0; width: min(280px, calc(100vw - 48px)); overflow: auto; background: #fff; box-shadow: 8px 0 24px rgba(16, 24, 40, .2); }
        .admobile-navigation-close { float: right; margin: 12px; padding: 6px 9px; border: 0; border-radius: 5px; background: #eef1f5; color: #344054; font: inherit; font-size: 12px; }
        .admobile-navigation-group { padding: 22px 16px 12px; color: #667085; font-size: 12px; font-weight: 700; }
        .admobile-navigation a { display: block; padding: 13px 16px 15px 28px; border-top: 1px solid #eef1f5; color: #172033; font-size: 14px; font-weight: 600; text-decoration: none; }
        .admobile-navigation a:active { background: #f3f6fb; }
        .admobile-user { display: flex; gap: 8px; align-items: center; margin-left: auto; color: #c8d0dc; font-size: 13px; }
        .admobile-logout { padding: 5px 8px; border: 1px solid #526174; border-radius: 6px; background: transparent; color: #e6ebf2; font: inherit; font-size: 12px; cursor: pointer; }
        .admobile-main { width: min(100%, 720px); margin: 0 auto; padding: 16px; }
        @media (max-width: 440px) {
            .admobile-header { padding: 12px; }
            .admobile-logo img { width: 132px; }
            .admobile-user > span { display: none; }
        }
    </style>
</head>
<body>
    <header class="admobile-header">
        <details class="admobile-menu-toggle">
            <summary aria-label="메뉴 열기"></summary>
            <nav class="admobile-navigation" aria-label="모바일 관리자 메뉴">
                <button type="button" class="admobile-navigation-close" onclick="this.closest('details').removeAttribute('open')">닫기</button>
                <div class="admobile-navigation-group">상품관리</div>
                <a href="/admobile/product/list">상품 목록</a>
                <div class="admobile-navigation-group">재고/발주</div>
                <a href="/admobile/order/sheet/list">주문(발주)</a>
                <div class="admobile-navigation-group">계정관리</div>
                <a href="/admobile/profile">내 정보 수정</a>
            </nav>
        </details>
        <h1>
            <a class="admobile-logo" href="/admobile/main" aria-label="<?= htmlspecialchars($documentTitle, ENT_QUOTES, 'UTF-8') ?>">
                <img src="/admin2/img/logo_dnfix_ibs2.png" alt="디엔픽스 IBS">
            </a>
        </h1>
        <div class="admobile-user">
            <span><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></span>
            <form method="post" action="/admobile/logout">
                <button class="admobile-logout" type="submit">로그아웃</button>
            </form>
        </div>
    </header>
    <main class="admobile-main">
        <?= $content ?? '' ?>
    </main>
</body>
</html>
