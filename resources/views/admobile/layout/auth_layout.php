<?php
$documentTitle = trim((string)($pageTitle ?? '인트라넷 로그인'));
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= htmlspecialchars($documentTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        :root { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color-scheme: light; }
        * { box-sizing: border-box; }
        body { display: grid; min-width: 320px; min-height: 100dvh; margin: 0; padding: 24px 16px; place-items: center; background: #f3f5f8; color: #172033; }
        .admobile-auth { width: min(100%, 400px); padding: 28px 24px; background: #fff; border: 1px solid #e3e7ee; border-radius: 16px; box-shadow: 0 12px 28px rgba(16, 24, 40, .08); }
    </style>
</head>
<body>
    <main class="admobile-auth"><?= $content ?? '' ?></main>
</body>
</html>
