<section class="admobile-login">
    <h1>인트라넷</h1>
    <p>관리자 계정으로 로그인해주세요.</p>

    <?php if (!empty($errorMessage)) { ?>
        <div class="admobile-login-error" role="alert"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php } ?>

    <form method="post" action="/admobile/login">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <label for="login_id">아이디</label>
        <input id="login_id" name="login_id" type="text" inputmode="text" autocomplete="username" required autofocus>

        <label for="login_password">비밀번호</label>
        <input id="login_password" name="login_password" type="password" autocomplete="current-password" required>

        <button type="submit">로그인</button>
    </form>
</section>

<style>
    .admobile-login h1 { margin: 0; font-size: 25px; }
    .admobile-login > p { margin: 8px 0 24px; color: #667085; font-size: 14px; }
    .admobile-login form { display: grid; gap: 8px; }
    .admobile-login label { margin-top: 8px; font-size: 14px; font-weight: 600; }
    .admobile-login input { width: 100%; height: 46px; padding: 0 12px; border: 1px solid #cfd6e1; border-radius: 8px; font: inherit; }
    .admobile-login input:focus { outline: 2px solid #8eb6ff; border-color: #3b82f6; }
    .admobile-login button { height: 48px; margin-top: 16px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
    .admobile-login-error { margin-bottom: 16px; padding: 10px 12px; border-radius: 8px; background: #fef2f2; color: #b42318; font-size: 14px; }
</style>
