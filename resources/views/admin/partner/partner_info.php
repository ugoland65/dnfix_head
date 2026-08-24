<?php
$partner = $partner ?? [];
$partnerInfo = $partnerInfo ?? [];
$categories = $categories ?? [];
$isEditMode = !empty($partner['idx']);
$nation = (string)($partnerInfo['nation'] ?? '한국');
$homepage = is_array($partnerInfo['hp'] ?? null) ? $partnerInfo['hp'] : [];
$contact = is_array($partnerInfo['info'] ?? null) ? $partnerInfo['info'] : [];
$keeper = is_array($partnerInfo['keeper'] ?? null) ? $partnerInfo['keeper'] : [];
?>

<style>
    .partner-info-form .field-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:10px; }
    .partner-info-form .field-grid .field-full { grid-column:1 / -1; }
    .partner-info-form label { display:block; margin-bottom:4px; font-size:12px; font-weight:600; color:#44506a; }
    .partner-info-form input[type="text"],
    .partner-info-form select,
    .partner-info-form textarea { width:100%; box-sizing:border-box; }
    .partner-info-form textarea { min-height:90px; resize:vertical; }
    .partner-info-form .radio-group { display:flex; flex-wrap:wrap; gap:12px; }
</style>

<form id="partner_info_form" class="partner-info-form">
    <input type="hidden" name="idx" value="<?= (int)($partner['idx'] ?? 0) ?>">

    <table class="table-style border01 width-full">
        <tr>
            <th>거래처명</th>
            <td><input type="text" name="name" value="<?= htmlspecialchars((string)($partner['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" required></td>
        </tr>
        <tr>
            <th>종류</th>
            <td>
                <select name="category">
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?= htmlspecialchars((string)$category, ENT_QUOTES, 'UTF-8') ?>" <?= (string)($partner['category'] ?? '') === (string)$category ? 'selected' : '' ?>><?= htmlspecialchars((string)$category, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>국가</th>
            <td>
                <div class="radio-group">
                    <?php foreach (['한국', '일본', '중국', '달러'] as $nationOption) { ?>
                        <label><input type="radio" name="nation" value="<?= $nationOption ?>" <?= $nation === $nationOption ? 'checked' : '' ?>> <?= $nationOption === '달러' ? '그외 달러 국가' : $nationOption ?></label>
                    <?php } ?>
                </div>
            </td>
        </tr>
        <tr>
            <th>은행정보</th>
            <td>
                <div class="field-grid">
                    <div>
                        <label>은행이름</label>
                        <input type="text" name="bank_name" value="<?= htmlspecialchars((string)($partner['bank_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                    <div>
                        <label>계좌번호</label>
                        <input type="text" name="bank_account" value="<?= htmlspecialchars((string)($partner['bank_account'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                    <div>
                        <label>예금주명</label>
                        <input type="text" name="bank_account_name" value="<?= htmlspecialchars((string)($partner['bank_account_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th>홈페이지</th>
            <td>
                <div class="field-grid">
                    <div class="field-full">
                        <label>주소</label>
                        <input type="text" name="hp_url" value="<?= htmlspecialchars((string)($homepage['url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                    <div>
                        <label>아이디</label>
                        <input type="text" name="hp_id" value="<?= htmlspecialchars((string)($homepage['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                    <div>
                        <label>패스워드</label>
                        <input type="text" name="hp_pw" value="<?= htmlspecialchars((string)($homepage['pw'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th>연락처</th>
            <td>
                <div class="field-grid">
                    <div>
                        <label>전화번호</label>
                        <input type="text" name="tel" value="<?= htmlspecialchars((string)($contact['tel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                    <div>
                        <label>이메일</label>
                        <input type="text" name="email" value="<?= htmlspecialchars((string)($contact['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th>담당자</th>
            <td>
                <div class="field-grid">
                    <div>
                        <label>이름</label>
                        <input type="text" name="keeper_name" value="<?= htmlspecialchars((string)($keeper['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                    <div>
                        <label>직급</label>
                        <input type="text" name="keeper_rank" value="<?= htmlspecialchars((string)($keeper['rank'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                    <div>
                        <label>연락처</label>
                        <input type="text" name="keeper_tel" value="<?= htmlspecialchars((string)($keeper['tel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th>메모</th>
            <td><textarea name="memo"><?= htmlspecialchars((string)($partner['memo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></td>
        </tr>
    </table>
</form>

<div class="m-t-10 text-center">
    <button type="button" id="partner_info_save" class="btnstyle1 btnstyle1-primary btnstyle1-lg"><?= $isEditMode ? '수정' : '등록' ?></button>
</div>

<script>
    $('#partner_info_save').on('click', function() {
        var $button = $(this);
        var $form = $('#partner_info_form');
        var partnerName = String($form.find('[name="name"]').val() || '').trim();
        if (partnerName === '') {
            alert('거래처명을 입력해주세요.');
            return;
        }

        $button.prop('disabled', true);
        $.ajax({
            url: '/admin/partner/save',
            type: 'POST',
            dataType: 'json',
            data: $form.serialize() + '&action_url=' + encodeURIComponent(window.location.pathname + window.location.search),
            success: function(res) {
                if (!res || res.success !== true) {
                    alert((res && res.message) ? res.message : '저장에 실패했습니다.');
                    return;
                }
                alert(res.message || '저장되었습니다.');
                window.location.reload();
            },
            error: function(request) {
                alert((request.responseJSON && request.responseJSON.message) || '저장 중 오류가 발생했습니다.');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
</script>
