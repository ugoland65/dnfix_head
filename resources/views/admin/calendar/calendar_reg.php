<?php
$calendarData = is_array($calendarData ?? null) ? $calendarData : [];
$mentionTarget = is_array($mentionTarget ?? null) ? $mentionTarget : [];
$selectedTargetIds = is_array($selectedTargetIds ?? null) ? $selectedTargetIds : [];
$calendarKinds = is_array($calendarKinds ?? null) ? $calendarKinds : [];
$isEditMode = !empty($idx);
?>

<form id="calendar_reg_form">
    <input type="hidden" name="idx" value="<?= (int)($idx ?? 0) ?>">
    <input type="hidden" name="mode" value="<?= htmlspecialchars((string)($calendarData['mode'] ?? '일반'), ENT_QUOTES, 'UTF-8') ?>">

    <table class="table-style border01 width-full">
        <tr>
            <th style="width:120px">날짜</th>
            <td><?= htmlspecialchars((string)($date ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <tr>
            <th style="width:120px">일시</th>
            <td>
                <input type="datetime-local" name="date_s" value="<?= htmlspecialchars((string)($date_s ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                ~
                <input type="datetime-local" name="date_e" value="<?= htmlspecialchars((string)($date_e ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </td>
        </tr>
        <tr>
            <th>제목</th>
            <td>
                <input type="text" name="subject" value="<?= htmlspecialchars((string)($calendarData['subject'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" class="width-full">
            </td>
        </tr>
        <tr>
            <th>모드</th>
            <td>
                <div class="m-t-5">
                    <label><input type="radio" name="open" value="전체공개" <?= (empty($calendarData['open']) || (string)$calendarData['open'] === '전체공개') ? 'checked' : '' ?>> 전체공개</label>
                    <label><input type="radio" name="open" value="개인" <?= ((string)($calendarData['open'] ?? '') === '개인') ? 'checked' : '' ?>> 개인</label>
                </div>

                <div class="m-t-7">
                    종류 :
                    <select name="kind">
                        <?php foreach ($calendarKinds as $kindName) { ?>
                            <option value="<?= htmlspecialchars((string)$kindName, ENT_QUOTES, 'UTF-8') ?>" <?= ((string)($calendarData['kind'] ?? '') === (string)$kindName) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string)$kindName, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php } ?>
                    </select>

                    상태 :
                    <select name="state">
                        <option value="I" <?= ((string)($calendarData['state'] ?? 'I') === 'I') ? 'selected' : '' ?>>진행</option>
                        <option value="E" <?= ((string)($calendarData['state'] ?? '') === 'E') ? 'selected' : '' ?>>완료</option>
                        <option value="C" <?= ((string)($calendarData['state'] ?? '') === 'C') ? 'selected' : '' ?>>취소</option>
                    </select>
                </div>
            </td>
        </tr>

        <tr>
            <th>참여자</th>
            <td>
                <?php foreach ($mentionTarget as $member) { ?>
                    <?php
                        $memberIdx = (string)($member['idx'] ?? '');
                        $isChecked = in_array($memberIdx, $selectedTargetIds, true);
                    ?>
                    <label>
                        <input type="checkbox" name="target_mb_id[]" value="<?= htmlspecialchars($memberIdx, ENT_QUOTES, 'UTF-8') ?>" <?= $isChecked ? 'checked' : '' ?>>
                        <?= htmlspecialchars((string)($member['ad_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </label>
                <?php } ?>
            </td>
        </tr>

        <tr>
            <th>메모</th>
            <td>
                <textarea name="memo"><?= htmlspecialchars((string)($calendarData['memo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </td>
        </tr>
    </table>
</form>

<div class="m-t-10" style="display:flex; justify-content:space-between; align-items:center;">
    <div>
        <?php if ($isEditMode) { ?>
            <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="calendarReg.remove(this);">삭제</button>
        <?php } ?>
    </div>
    <div>
        <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="calendarReg.save(this);">등록</button>
    </div>
</div>

<script type="text/javascript">
var calendarReg = function() {
    return {
        remove: function(obj) {
            var idx = String($('input[name="idx"]').val() || '').trim();
            if (!idx) {
                alert('삭제할 일정이 없습니다.');
                return;
            }

            if (!confirm('삭제시 복구되지 않습니다')) {
                return;
            }

            $(obj).attr('disabled', true);

            $.ajax({
                url: '/admin/main/calendar/delete',
                data: { idx: idx },
                type: "POST",
                dataType: "json",
                success: function(res) {
                    if (res.success === true) {
                        toast2("success", "캘린더 삭제", "캘린더 일정이 삭제되었습니다.");
                        if (typeof calendarWindow !== 'undefined' && calendarWindow) {
                            calendarWindow.close();
                        }
                        calendar.view();
                    } else {
                        showAlert("Error", res.msg || "삭제 실패", "alert2");
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    showAlert("Error", "에러", "alert2");
                },
                complete: function() {
                    $(obj).attr('disabled', false);
                }
            });
        },
        save: function(obj) {
            var subject = String($('input[name="subject"]').val() || '').trim();
            if (!subject) {
                alert('제목을 입력해주세요.');
                $('input[name="subject"]').focus();
                return;
            }

            $(obj).attr('disabled', true);

            var isEditMode = <?= $isEditMode ? 'true' : 'false' ?>;
            var requestUrl = isEditMode ? '/admin/main/calendar/save' : '/admin/main/calendar/create';
            var formData = $("#calendar_reg_form").serializeArray();

            $.ajax({
                url: requestUrl,
                data: formData,
                type: "POST",
                dataType: "json",
                success: function(res) {
                    if (res.success === true) {
                        toast2("success", "캘린더 등록", "캘린더 등록이 저장되었습니다.");
                        if (typeof calendarWindow !== 'undefined' && calendarWindow) {
                            calendarWindow.close();
                        }
                        calendar.view();
                    } else {
                        showAlert("Error", res.msg || "저장 실패", "alert2");
                    }
                },
                error: function(request, status, error) {
                    console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    showAlert("Error", "에러", "alert2");
                },
                complete: function() {
                    $(obj).attr('disabled', false);
                }
            });
        }
    };
}();
</script>
