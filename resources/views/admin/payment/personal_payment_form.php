<?php
$settlement = $settlement ?? [];
$admins = $admins ?? [];
$payMethods = $payMethods ?? [];
$statuses = $statuses ?? [];
$mode = (string)($mode ?? 'create');
$editable = !empty($editable);
$isLocked = $mode === 'modify' && (string)($settlement['status'] ?? '') === '지급완료';
$bankOptions = ['국민은행', '신한은행', '우리은행', '하나은행', '농협은행', '기업은행', '카카오뱅크', '토스뱅크', '케이뱅크', 'SC제일은행', '씨티은행', '부산은행', '경남은행', '광주은행', '전북은행', '대구은행', '수협은행', '우체국', '새마을금고', '신협'];
$selectedApplicant = (int)($settlement['applicant_ad_pk'] ?? 0);
$money = static function ($value): string {
    $number = (float)$value;
    return number_format($number, $number == floor($number) ? 0 : 2);
};
?>
<form id="personal_payment_form" enctype="multipart/form-data">
    <input type="hidden" name="mode" value="<?= htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($mode === 'modify') { ?>
        <input type="hidden" name="idx" value="<?= (int)($settlement['idx'] ?? 0) ?>">
    <?php } ?>

    <table class="table-style border01 width-full">
        <colgroup>
            <col width="140px">
            <col>
        </colgroup>
        <tbody>
            <?php if ($mode === 'modify') { ?>
                <tr>
                    <th>고유번호</th>
                    <td>#<?= (int)($settlement['idx'] ?? 0) ?> · <?= htmlspecialchars((string)($settlement['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string)($settlement['ad_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)</td>
                </tr>
            <?php } ?>
            <tr>
                <th>신청자</th>
                <td>
                    <select name="applicant_ad_pk" <?= $editable ? '' : 'disabled' ?>>
                        <option value="">선택</option>
                        <?php foreach ($admins as $admin) { ?>
                            <option value="<?= (int)($admin['idx'] ?? 0) ?>" <?= $selectedApplicant === (int)($admin['idx'] ?? 0) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string)($admin['ad_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                <?php if (!empty($admin['ad_department'])) { ?>(<?= htmlspecialchars((string)$admin['ad_department'], ENT_QUOTES, 'UTF-8') ?>)<?php } ?>
                            </option>
                        <?php } ?>
                        <?php if ($selectedApplicant > 0 && !in_array($selectedApplicant, array_map('intval', array_column($admins, 'idx')), true)) { ?>
                            <option value="<?= $selectedApplicant ?>" selected><?= htmlspecialchars((string)($settlement['applicant_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                        <?php } ?>
                    </select>
                    <div class="admin-guide-text">실제 개인 자금으로 결제한 직원입니다. 기본값은 로그인한 직원입니다.</div>
                </td>
            </tr>
            <tr>
                <th>사용일자</th>
                <td><div class="calendar-input"><input type="text" name="use_date" value="<?= htmlspecialchars((string)($settlement['use_date'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8') ?>" <?= $editable ? '' : 'readonly' ?>></div></td>
            </tr>
            <tr>
                <th>사용처</th>
                <td><input type="text" name="vendor" value="<?= htmlspecialchars((string)($settlement['vendor'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:100%;" <?= $editable ? '' : 'readonly' ?>></td>
            </tr>
            <tr>
                <th>사용내용 및 업무목적</th>
                <td><textarea name="purpose" rows="5" <?= $editable ? 'required' : 'readonly' ?>><?= htmlspecialchars((string)($settlement['purpose'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></td>
            </tr>
            <tr>
                <th>결제수단</th>
                <td>
                    <select name="pay_method" <?= $editable ? '' : 'disabled' ?>>
                        <?php foreach ($payMethods as $payMethod) { ?>
                            <option value="<?= htmlspecialchars($payMethod, ENT_QUOTES, 'UTF-8') ?>" <?= (string)($settlement['pay_method'] ?? '') === $payMethod ? 'selected' : '' ?>><?= htmlspecialchars($payMethod, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>결제금액</th>
                <td><input type="text" name="payment_amount" id="personal_payment_amount" class="price" value="<?= $money($settlement['payment_amount'] ?? 0) ?>" style="width:140px;" <?= $editable ? '' : 'readonly' ?>> 원</td>
            </tr>
            <tr>
                <th>정산요청 금액</th>
                <td>
                    <input type="text" name="request_amount" id="personal_request_amount" class="price" value="<?= $money($settlement['request_amount'] ?? ($settlement['payment_amount'] ?? 0)) ?>" style="width:140px;" <?= $editable ? '' : 'readonly' ?>> 원
                    <div class="admin-guide-text">처음에는 결제금액과 같게 들어가며, 필요한 경우 따로 수정합니다.</div>
                </td>
            </tr>
            <tr>
                <th>지급받을 계좌</th>
                <td>
                    <select name="bank" <?= $editable ? '' : 'disabled' ?>>
                        <option value="">은행 선택</option>
                        <?php foreach ($bankOptions as $bankName) { ?>
                            <option value="<?= htmlspecialchars($bankName, ENT_QUOTES, 'UTF-8') ?>" <?= (string)($settlement['bank'] ?? '') === $bankName ? 'selected' : '' ?>><?= htmlspecialchars($bankName, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php } ?>
                    </select>
                    <input type="text" name="bank_account" value="<?= htmlspecialchars((string)($settlement['bank_account'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:180px;" placeholder="계좌번호" <?= $editable ? '' : 'readonly' ?>>
                    예금주 <input type="text" name="depositor" value="<?= htmlspecialchars((string)($settlement['depositor'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:120px;" <?= $editable ? '' : 'readonly' ?>>
                    <div class="admin-guide-text">이전에 등록한 계좌가 있으면 자동으로 불러옵니다.</div>
                </td>
            </tr>
            <tr>
                <th>증빙자료</th>
                <td>
                    <?php if ($editable) { ?>
                        <input type="file" name="evidence_files[]" multiple accept=".pdf,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,image/*,application/pdf">
                        <div class="admin-guide-text">영수증 등 PDF, Excel, 이미지를 여러 장 첨부할 수 있습니다. 파일당 20MB입니다.</div>
                    <?php } ?>
                    <?php foreach (($settlement['files'] ?? []) as $file) { ?>
                        <?php $filePath = (string)($file['file_path'] ?? ''); ?>
                        <?php if (strpos($filePath, '/data/personal_payment/') === 0) { ?>
                            <div><a href="<?= htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars((string)($file['original_name'] ?? basename($filePath)), ENT_QUOTES, 'UTF-8') ?></a></div>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
            <?php if ($mode === 'modify') { ?>
                <tr>
                    <th>회계 과목</th>
                    <td>
                        <input type="text" name="account_subject" value="<?= htmlspecialchars((string)($settlement['account_subject'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:100%;" <?= $isLocked ? 'readonly' : '' ?>>
                        <div class="admin-guide-text">신청 항목이 아닙니다. 재무팀 회계 처리 때 입력합니다.</div>
                    </td>
                </tr>
                <tr>
                    <th>정산 상태</th>
                    <td>
                        <?php if ($isLocked) { ?>
                            지급완료
                        <?php } else { ?>
                            <select name="status" id="personal_payment_status">
                                <?php foreach ($statuses as $status) { ?>
                                    <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= (string)($settlement['status'] ?? '') === $status ? 'selected' : '' ?>><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </td>
                </tr>
                <tr id="personal_paid_row">
                    <th>지급 처리</th>
                    <td>
                        지급금액 <input type="text" name="paid_amount" id="personal_paid_amount" class="price" value="<?= $money($settlement['paid_amount'] ?? ($settlement['request_amount'] ?? 0)) ?>" style="width:120px;" <?= $isLocked ? 'readonly' : '' ?>> 원
                        지급일자 <input type="text" name="paid_date" class="personal-paid-date" value="<?= htmlspecialchars((string)($settlement['paid_date'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8') ?>" style="width:110px;" <?= $isLocked ? 'readonly' : '' ?>>
                        <textarea name="paid_memo" rows="3" placeholder="지급 메모" <?= $isLocked ? 'readonly' : '' ?>><?= htmlspecialchars((string)($settlement['paid_memo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (!empty($settlement['paid_ad_name'])) { ?>
                            <div class="admin-guide-text">처리자 <?= htmlspecialchars((string)$settlement['paid_ad_name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php } ?>
                    </td>
                </tr>
                <?php if (!empty($settlement['logs'])) { ?>
                    <tr>
                        <th>상태 이력</th>
                        <td>
                            <?php foreach ($settlement['logs'] as $log) { ?>
                                <div><?= htmlspecialchars((string)($log['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars((string)($log['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars((string)($log['ad_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string)($log['memo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</form>
<div class="m-t-10 text-center">
    <?php if (!$isLocked) { ?>
        <button type="button" id="personal_payment_save" class="btnstyle1 btnstyle1-primary btnstyle1-lg">저장</button>
    <?php } else { ?>
        <div class="admin-guide-text">지급완료된 정산요청은 지급이력을 변경할 수 없습니다.</div>
    <?php } ?>
</div>
<script>
    $(function() {
        var requestAmountTouched = <?= $mode === 'modify' ? 'true' : 'false' ?>;
        if (typeof clareCalendar !== 'undefined') {
            $('.calendar-input input, .personal-paid-date').datepicker(clareCalendar);
        }

        function digits(value) {
            return String(value || '').replace(/,/g, '');
        }

        $(document).on('input', '#personal_payment_form input.price', function() {
            var value = $(this).val();
            if (typeof GC !== 'undefined' && typeof GC.commaInput === 'function') {
                GC.commaInput(value, this);
            }
        });

        $('#personal_payment_amount').on('input', function() {
            if (!requestAmountTouched) {
                $('#personal_request_amount').val($(this).val());
            }
        });
        $('#personal_request_amount').on('input', function() {
            requestAmountTouched = digits($(this).val()) !== digits($('#personal_payment_amount').val());
        });

        function togglePaidRow() {
            var status = String($('#personal_payment_status').val() || '');
            $('#personal_paid_row').toggle(status === '지급완료' || <?= $isLocked ? 'true' : 'false' ?>);
        }
        $('#personal_payment_status').on('change', togglePaidRow);
        togglePaidRow();

        $('#personal_payment_save').on('click', function() {
            var $button = $(this);
            if ($button.prop('disabled')) {
                return;
            }
            var form = document.getElementById('personal_payment_form');
            $('#personal_payment_form').find(':disabled').each(function() {
                $(this).data('wasDisabled', true).prop('disabled', false);
            });
            var formData = new FormData(form);
            $('#personal_payment_form').find(':input').each(function() {
                if ($(this).data('wasDisabled')) {
                    $(this).prop('disabled', true);
                }
            });
            $button.prop('disabled', true);
            ajaxRequest('/admin/payment/personal_payment_save', formData, {
                processData: false,
                contentType: false
            }).then(function(res) {
                if (!res.success) {
                    $button.prop('disabled', false);
                    alert(res.message || '저장에 실패했습니다.');
                    return;
                }
                if (window.opener && !window.opener.closed) {
                    window.opener.location.href = '/admin/payment/payment_request_list?tab=personal';
                }
                window.close();
                setTimeout(function() {
                    window.location.href = '/admin/payment/payment_request_list?tab=personal';
                }, 200);
            }).catch(function(err) {
                $button.prop('disabled', false);
                alert(err.message || '저장에 실패했습니다.');
            });
        });
    });
</script>
