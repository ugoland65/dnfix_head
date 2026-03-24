<form id="payment_request_detail_form">

    <input type="hidden" name="mode" value="<?= $mode ?>">
    <input type="hidden" name="apiMode" value="<?= $apiMode ?>">
    <?php if ($mode == 'create') { ?>

    <?php } else { ?>
        <input type="hidden" name="idx" value="<?= $paymentRequest['idx'] ?>">
    <?php } ?>

    <table class="table-style border01 width-full">
        <colgroup>
            <col width="120px">
            <col>
        </colgroup>

        <tbody>
            
                <input type="hidden" name="kind" value="<?= $kind ?>">
                <input type="hidden" name="kind_idx" value="<?= $kind_idx ?>">

                <?php if ($mode == 'modify') { ?>
                    <tr>
                        <th>고유번호</th>
                        <td><?= $idx ?></td>
                    </tr>
                    <tr>
                        <th>작성일</th>
                        <td><?= $paymentRequest['created_at'] ?? '' ?> (<?= $paymentRequest['ad_name'] ?? '' ?>)</td>
                    </tr>
                <?php } ?>
                
                <tr>
                    <th>분류</th>
                    <td>
                        <select name="category" id="category">
                            <option value="기타" <?= $category == "기타" ? "selected" : "" ?>>기타</option>
                            <option value="환불" <?= $category == "환불" ? "selected" : "" ?>>환불</option>
                            <option value="주문발주" <?= $category == "주문발주" ? "selected" : "" ?>>주문발주</option>
                            <option value="예치금충전" <?= $category == "예치금충전" ? "selected" : "" ?>>예치금충전</option>
                        </select>

                        <div class="admin-guide-text">
                            - 주문발주 : 인트라넷 주문발주 연관된 결제건만<br>
                        </div>
                    </td>
                </tr>

                <tr id="godo_order_no_row">
                    <th>고도몰 주문번호</th>
                    <td>
                        <input type="text" name="godo_order_no" id="godo_order_no" value="<?= $paymentRequest['godo_order_no'] ?? '' ?>" style="width:200px;">
                        <div class="admin-guide-text">
                            - 환불일때는 고도몰 주문번호가 필수 입력사항입니다.
                        </div>
                    </td>
                </tr>

                <?php if( $kind == 'order_sheet' ) { ?>
                    <tr>
                        <th>주문서 번호</th>
                        <td><?= $kind_idx ?></td>
                    </tr>
                <?php } ?>
                
                <tr>
                    <th>요청금액</th>
                    <td>
                        <select name="currency" id="currency">
                            <option value="KRW" <?= ($currency == "KRW" || $currency == "원") ? "selected" : "" ?>>KRW 원</option>
                            <option value="JPY" <?= ($currency == "JPY" || $currency == "엔") ? "selected" : "" ?>>JPY 엔</option>
                            <option value="CNY" <?= ($currency == "CNY" || $currency == "위안") ? "selected" : "" ?>>CNY 위안</option>
                            <option value="USD" <?= ($currency == "USD" || $currency == "달러") ? "selected" : "" ?>>USD 달러</option>
                        </select>
                        <input type="text" name="amount" id="amount" value="<?= number_format((float)($amount ?? 0), ((float)($amount ?? 0) == floor((float)($amount ?? 0))) ? 0 : 2) ?>" class="price">

                        <div class="admin-guide-text">
                            - 요청금액 잘 확인해주세요.
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>부가세 포함 여부</th>
                    <td>
                        <select name="is_vat" id="is_vat">
                            <option value="Y" <?= $is_vat == "Y" ? "selected" : "" ?>>포함</option>
                            <option value="N" <?= $is_vat == "N" ? "selected" : "" ?>>미포함</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>무통장 입금자명</th>
                    <td>
                        <input type="text" name="depositor_name" id="depositor_name" value="<?= $paymentRequest['depositor_name'] ?? '주식회사 디엔픽스' ?>">
                    </td>
                </tr>
                <tr>
                    <th>결제 희망일</th>
                    <td class="">
                        <div class="calendar-input"><input type="text" name="request_date" id="request_date" value="<?= $paymentRequest['request_date'] ?? date('Y-m-d') ?>"></div>
                    </td>
                </tr>
                <tr id="domestic_account_row">
                    <th>결제계좌</th>
                    <td>
                        <?php
                        $bankOptions = [
                            '국민은행',
                            '신한은행',
                            '우리은행',
                            '하나은행',
                            '농협은행',
                            '기업은행',
                            '카카오뱅크',
                            '토스뱅크',
                            '케이뱅크',
                            'SC제일은행',
                            '씨티은행',
                            '부산은행',
                            '경남은행',
                            '광주은행',
                            '전북은행',
                            '대구은행',
                            '수협은행',
                            '우체국',
                            '새마을금고',
                            '신협',
                        ];
                        ?>
                        <select name="bank" id="bank">
                            <option value="">은행 선택</option>
                            <?php foreach ($bankOptions as $idx => $bankName) { ?>
                                <?php
                                // 기존 숫자 저장값(1~5 등)과 은행명 저장값 모두 대응
                                $legacyCode = (string)($idx + 1);
                                $selected = ((string)($bank ?? '') === $bankName) || ((string)($bank ?? '') === $legacyCode);
                                ?>
                                <option value="<?= $bankName ?>" <?= $selected ? "selected" : "" ?>><?= $bankName ?></option>
                            <?php } ?>
                        </select>
                        <input type="text" name="bank_account" id="bank_account" value="<?= $bank_account ?? '' ?>">

                        예금주 :
                        <input type="text" name="depositor" id="depositor" value="<?= $depositor ?? '' ?>">
                        <div class="admin-guide-text">
                            - 결제계좌 잘 확인해주세요.
                        </div>
                    </td>
                </tr>
                <tr id="foreign_account_row">
                    <th>해외계좌</th>
                    <td>
                        <textarea name="foreign_account" id="foreign_account" rows="4" style="height:80px;"><?= $importAccount ?? '' ?></textarea>
                    </td>
                </tr>
                
                <tr>
                    <th>요청내용</th>
                    <td>
                        <textarea name="memo" id="memo" rows="10"><?= $paymentRequest['memo'] ?? '' ?></textarea>
                    </td>
                </tr>
                <?php if ($mode == 'modify') { ?>
                <tr>
                    <th>상태변경</th>
                    <td>
                        <select name="status" id="status">
                            <option value="요청" <?= $paymentRequest['status'] == "요청" ? "selected" : "" ?>>요청</option>
                            <option value="처리완료" <?= $paymentRequest['status'] == "처리완료" ? "selected" : "" ?>>결제완료</option>
                            <option value="반려" <?= $paymentRequest['status'] == "반려" ? "selected" : "" ?>>반려</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>결제완료일</th>
                    <td class="">
                        <div class="calendar-input"><input type="text" name="process_date" id="process_date" value="<?= $paymentRequest['process_date'] ?? date('Y-m-d') ?>"></div>
                    </td>
                </tr>
                <tr>
                    <th>처리내용</th>
                    <td>
                        <textarea name="process_memo" id="process_memo" rows="10"><?= $paymentRequest['process_memo'] ?? '' ?></textarea>
                    </td>
                </tr>
            <?php } ?>

        </tbody>

    </table>

</form>
<div class="m-t-10 text-center">
    <button type="button" id="save_btn" class="btnstyle1 btnstyle1-primary btnstyle1-lg">등록</button>
</div>

<script>
    $(function() {

        $('.calendar-input input').datepicker(clareCalendar);

        function toggleForeignAccountRow() {
            var currency = String($('#currency').val() || '').trim();
            // 기존 한글 저장값(원)도 호환
            if (currency === 'KRW' || currency === '원' || currency === '') {
                $('#domestic_account_row').show();
                $('#foreign_account_row').hide();
            } else {
                $('#domestic_account_row').hide();
                $('#foreign_account_row').show();
            }
        }

        function toggleGodoOrderNoInput() {
            var isRefund = String($('#category').val() || '').trim() === '환불';
            $('#godo_order_no').prop('readonly', !isRefund);
            $('#godo_order_no').prop('required', isRefund);

            if (isRefund) {
                $('#godo_order_no_row').show();
            } else {
                $('#godo_order_no').val('');
                $('#godo_order_no_row').hide();
            }
        }

        $('#currency').on('change', toggleForeignAccountRow);
        $('#category').on('change', toggleGodoOrderNoInput);
        toggleForeignAccountRow();
        toggleGodoOrderNoInput();

        $(document).on('input', 'input.price', function() {
            var value = $(this).val();
            if (typeof GC !== 'undefined' && typeof GC.commaInput === 'function') {
                GC.commaInput(value, this);
                return;
            }
            $(this).val(formatNumberWithComma(value));
        });

        $('#save_btn').click(function(e) {
            e.preventDefault();

            var isRefund = String($('#category').val() || '').trim() === '환불';
            var godoOrderNo = String($('#godo_order_no').val() || '').trim();
            if (isRefund && godoOrderNo === '') {
                alert('환불일 때 고도몰 주문번호는 필수입력입니다.');
                $('#godo_order_no').focus();
                return;
            }

            var formData = $('#payment_request_detail_form').serializeArray();
            ajaxRequest('/admin/payment/payment_request_save', formData)
                .then(res => {
                    if (res.success) {
                        alert(res.message);
                        window.location.reload();
                    } else {
                        alert(res.message);
                    }
                })
                .catch(err => {
                    alert(err.message);
                });
        });
    });
</script>