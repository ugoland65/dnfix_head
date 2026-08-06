<?php
$admin = $admin ?? [];
$adData = is_array($admin['ad_data'] ?? null) ? $admin['ad_data'] : [];
$contact = is_array($adData['contact'] ?? null) ? $adData['contact'] : [];
$profileImage = trim((string)($admin['ad_image'] ?? ''));
?>
<section class="admobile-profile">
    <div class="admobile-profile-heading">
        <a href="/admobile/main" aria-label="메뉴로 돌아가기">‹</a>
        <h2>계정관리</h2>
    </div>

    <form id="admobile-profile-form" enctype="multipart/form-data">
        <section class="admobile-profile-card admobile-profile-image-card">
            <div class="admobile-profile-image">
                <?php if ($profileImage !== '') { ?>
                    <img id="profile-image-preview" src="/data/uploads/<?= htmlspecialchars($profileImage, ENT_QUOTES, 'UTF-8') ?>" alt="프로필 이미지">
                <?php } else { ?>
                    <img id="profile-image-preview" src="" alt="프로필 이미지" hidden>
                    <span id="profile-image-empty">이미지 없음</span>
                <?php } ?>
            </div>
            <label class="admobile-profile-image-button" for="profile_image">프로필 이미지 변경</label>
            <input id="profile_image" name="profile_image" type="file" accept="image/jpeg,image/png,image/gif">
        </section>

        <section class="admobile-profile-card">
            <label>아이디</label>
            <input type="text" value="<?= htmlspecialchars((string)($admin['ad_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" readonly>
            <label for="ad_nick">닉네임</label>
            <input id="ad_nick" name="ad_nick" type="text" value="<?= htmlspecialchars((string)($admin['ad_nick'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <label for="ad_birth">생년월일</label>
            <input id="ad_birth" name="ad_birth" type="date" value="<?= htmlspecialchars((string)($admin['ad_birth'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <label for="ad_google">구글 아이디</label>
            <input id="ad_google" name="ad_google" type="text" value="<?= htmlspecialchars((string)($admin['ad_google'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </section>

        <section class="admobile-profile-card">
            <h3>비밀번호 변경</h3>
            <label for="new_password">새 비밀번호</label>
            <input id="new_password" name="new_password" type="password" autocomplete="new-password">
            <label for="password_confirm">새 비밀번호 확인</label>
            <input id="password_confirm" name="password_confirm" type="password" autocomplete="new-password">
        </section>

        <section class="admobile-profile-card">
            <h3>연락처</h3>
            <label for="ad_tel">연락처</label>
            <input id="ad_tel" name="ad_tel" type="tel" value="<?= htmlspecialchars((string)($adData['tel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <label for="ad_address">주소</label>
            <textarea id="ad_address" name="ad_address" rows="3"><?= htmlspecialchars((string)($adData['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            <label for="ad_contact_name">비상연락처 이름</label>
            <input id="ad_contact_name" name="ad_contact_name" type="text" value="<?= htmlspecialchars((string)($contact['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <label for="ad_contact_relationship">관계</label>
            <input id="ad_contact_relationship" name="ad_contact_relationship" type="text" value="<?= htmlspecialchars((string)($contact['relationship'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <label for="ad_contact_tel">비상연락처</label>
            <input id="ad_contact_tel" name="ad_contact_tel" type="tel" value="<?= htmlspecialchars((string)($contact['tel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </section>

        <button class="admobile-profile-save" type="submit">내 정보 저장</button>
        <p id="admobile-profile-message" aria-live="polite"></p>
    </form>
</section>

<style>
    .admobile-profile-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .admobile-profile-heading a { color: #344054; font-size: 30px; line-height: 24px; text-decoration: none; }
    .admobile-profile-heading h2 { margin: 0; font-size: 18px; }
    .admobile-profile { max-width: 520px; }
    .admobile-profile-card { display: grid; gap: 7px; margin-bottom: 12px; padding: 15px; border: 1px solid #e3e7ee; border-radius: 10px; background: #fff; }
    .admobile-profile-card h3 { margin: 0 0 3px; font-size: 15px; }
    .admobile-profile-card label { color: #475467; font-size: 12px; font-weight: 700; }
    .admobile-profile-card input, .admobile-profile-card textarea { width: 100%; padding: 10px; border: 1px solid #cfd6e1; border-radius: 6px; color: #172033; font: inherit; }
    .admobile-profile-card input[readonly] { background: #f2f4f7; color: #667085; }
    .admobile-profile-image-card { justify-items: center; }
    .admobile-profile-image { display: flex; align-items: center; justify-content: center; width: 104px; height: 104px; overflow: hidden; border-radius: 50%; background: #eef2f8; color: #98a2b3; font-size: 12px; }
    .admobile-profile-image img { width: 100%; height: 100%; object-fit: cover; }
    .admobile-profile-image-button { padding: 8px 11px; border-radius: 6px; background: #eef2f8; color: #344054 !important; cursor: pointer; }
    #profile_image { display: none; }
    .admobile-profile-save { width: 100%; padding: 13px; border: 0; border-radius: 7px; background: #3056a8; color: #fff; font: inherit; font-weight: 700; }
    #admobile-profile-message { min-height: 18px; margin: 8px 0 0; color: #067647; font-size: 13px; text-align: center; }
</style>

<script>
    (function() {
        var form = document.getElementById('admobile-profile-form');
        var imageInput = document.getElementById('profile_image');
        var imagePreview = document.getElementById('profile-image-preview');
        var imageEmpty = document.getElementById('profile-image-empty');
        var message = document.getElementById('admobile-profile-message');

        imageInput.addEventListener('change', function() {
            var file = imageInput.files[0];
            if (!file) return;
            imagePreview.src = URL.createObjectURL(file);
            imagePreview.hidden = false;
            if (imageEmpty) imageEmpty.hidden = true;
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            var button = form.querySelector('button[type="submit"]');
            button.disabled = true;
            message.textContent = '';

            fetch('/admobile/profile', {
                method: 'POST',
                body: new FormData(form)
            })
                .then(function(response) { return response.json(); })
                .then(function(result) {
                    if (!result.success) throw new Error(result.message || '저장에 실패했습니다.');
                    message.style.color = '#067647';
                    message.textContent = result.message;
                })
                .catch(function(error) {
                    message.style.color = '#b42318';
                    message.textContent = error.message;
                })
                .finally(function() {
                    button.disabled = false;
                });
        });
    })();
</script>
