<form id="cs_detail_form">

    <input type="hidden" name="mode" value="<?= $mode ?>">
    <input type="hidden" name="apiMode" value="<?= $apiMode ?>">
    <?php if( $mode == 'create' ) { ?>

    <?php } else { ?>
        <input type="hidden" name="idx" value="<?= $csRequest['idx'] ?>">
    <?php } ?>

    <table class="table-style border01 width-full">
        <colgroup>
            <col width="120px">
            <col>
        </colgroup>

        <tbody>
            <?php if( $mode == 'create' ) { ?>
                <tr>
                    <th>분류</th>
                    <td>
                        <select name="category" id="category">
                            <option value="출고준비" <?= $category == "출고준비" ? "selected" : "" ?>>출고준비</option>
                            <option value="환불" <?= $category == "환불" ? "selected" : "" ?>>환불</option>
                            <option value="불량" <?= $category == "불량" ? "selected" : "" ?>>불량(QC)</option>
                            <option value="공급사주문" <?= $category == "공급사주문" ? "selected" : "" ?>>공급사주문</option>
                            <option value="출고지정일" <?= $category == "출고지정일" ? "selected" : "" ?>>출고지정일</option>
                            <option value="입고후출고" <?= $category == "입고후출고" ? "selected" : "" ?>>입고후출고</option>
                            <option value="기타" <?= $category == "기타" ? "selected" : "" ?>>기타</option>
                        </select>
                    </td>
                </tr>
                <tr id="action_date_row">
                    <th>출고일자</th>
                    <td>
                        <div class="calendar-input">
                            <input type="text" name="action_date" id="action_date" value="<?= $actionDate ?>" placeholder="출고일자를 선택하세요.">
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>주문번호</th>
                    <td>

                    <?php if ( !empty($orderNo) ) { ?>
                        <input type="hidden" name="order_no" id="order_no" value="<?= $orderNo ?>">
                        <span><?= $orderNo ?></span>
                    <?php } else { ?>
                        <input type="text" name="order_no" id="order_no" value="">
                        <div class="admin-guide-text">
                            주문번호를 입력하면 주문정보를 자동으로 조회됩니다.
                        </div>
                    <?php } ?>
                    </td>
                </tr>

                <?php if ( !empty($orderDate) ) { ?>
                    <tr>
                        <th>주문일시</th>
                        <td>
                            <input type="hidden" name="order_date" id="order_date" value="<?= $orderDate ?>">
                            <span><?= $orderDate ?></span>
                        </td>
                    </tr>
                <?php } ?>

                <?php if ( !empty($paymentDt) ) { ?>
                    <tr>
                        <th>결제일시</th>
                        <td>
                            <input type="hidden" name="payment_dt" id="payment_dt" value="<?= $paymentDt ?>">
                            <span><?= $paymentDt ?></span>
                        </td>
                    </tr>
                <?php } ?>

                <?php if ( !empty($memNo) ) { ?>
                    <tr>
                        <th>회원번호</th>
                        <td>
                            <input type="hidden" name="mem_no" id="mem_no" value="<?= $memNo ?>">
                            <span><?= $memNo ?></span>
                        </td>
                    </tr>
                <?php } ?>

                <?php if ( !empty($memId) ) { ?>
                    <tr>
                        <th>회원아이디</th>
                        <td>
                            <input type="hidden" name="mem_id" id="mem_id" value="<?= $memId ?>">
                            <span><?= $memId ?></span>
                        </td>
                    </tr>
                <?php } ?>

                <?php if ( !empty($memName) ) { ?>
                    <tr>
                        <th>회원명</th>
                        <td>
                            <input type="hidden" name="mem_name" id="mem_name" value="<?= $memName ?>">
                            <span><?= $memName ?></span>
                        </td>
                    </tr>
                <?php } ?>

                <?php if ( !empty($memPhone) ) { ?>

                    <tr>
                        <th>회원전화</th>
                        <td>
                            <input type="hidden" name="mem_phone" id="mem_phone" value="<?= $memPhone ?>">
                            <span><?= $memPhone ?></span>
                        </td>
                    </tr>
                <?php } ?>

                <?php if ( !empty($groupNm) ) { ?>
                    <tr>
                        <th>그룹명</th>
                        <td>
                            <input type="hidden" name="group_nm" id="group_nm" value="<?= $groupNm ?>">
                            <span><?= $groupNm ?></span>
                        </td>
                    </tr>
                <?php } ?>

                <?php if ( !empty($receiverName) ) { ?>
                    <tr>
                        <th>수령자명</th>
                        <td>
                            <input type="hidden" name="receiver_name" id="receiver_name" value="<?= $receiverName ?>">
                            <span><?= $receiverName ?></span>
                        </td>
                    </tr>
                <?php } ?>

                <?php if ( !empty($receiverPhone) ) { ?>
                    <tr>
                        <th>수령자전화</th>
                        <td>
                            <input type="hidden" name="receiver_phone" id="receiver_phone" value="<?= $receiverPhone ?>">
                            <span><?= $receiverPhone ?></span>
                        </td>
                    </tr>
                <?php } ?>


                <tr>
                    <th>요청내용</th>
                    <td>
                        <textarea name="cs_body" id="cs_body" rows="10"></textarea>
                    </td>
                </tr>
            <?php } else { ?>
                <tr>
                    <th>주문번호</th>
                    <td><?= $csRequest['order_no'] ?></td>
                </tr>
                <tr>
                    <th>분류</th>
                    <td>
                        <select name="category" id="category">
                            <option value="출고준비" <?= $csRequest['category'] == "출고준비" ? "selected" : "" ?>>출고준비</option>
                            <option value="환불" <?= $csRequest['category'] == "환불" ? "selected" : "" ?>>환불</option>
                            <option value="불량" <?= $csRequest['category'] == "불량" ? "selected" : "" ?>>불량(QC)</option>
                            <option value="공급사주문" <?= $csRequest['category'] == "공급사주문" ? "selected" : "" ?>>공급사주문</option>
                            <option value="출고지정일" <?= $csRequest['category'] == "출고지정일" ? "selected" : "" ?>>출고지정일</option>
                            <option value="입고후출고" <?= $csRequest['category'] == "입고후출고" ? "selected" : "" ?>>입고후출고</option>
                            <option value="기타" <?= $csRequest['category'] == "기타" ? "selected" : "" ?>>기타</option>
                        </select>
                    </td>
                </tr>
                <tr id="action_date_row">
                    <th>출고일자</th>
                    <td>
                        <div class="calendar-input">
                            <input type="text" name="action_date" id="action_date" value="<?= $csRequest['action_date'] ?>" placeholder="출고일자를 선택하세요.">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>상태변경</th>
                    <td>
                        <select name="cs_status" id="cs_status">
                            <option value="요청" <?= $csRequest['cs_status'] == "요청" ? "selected" : "" ?>>요청</option>
                            <option value="처리중" <?= $csRequest['cs_status'] == "처리중" ? "selected" : "" ?>>처리중</option>
                            <option value="처리완료" <?= $csRequest['cs_status'] == "처리완료" ? "selected" : "" ?>>처리완료</option>
                            <option value="처리실패" <?= $csRequest['cs_status'] == "처리실패" ? "selected" : "" ?>>처리실패</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>처리내용</th>
                    <td>
                        <textarea name="process_action" id="process_action" rows="10"><?= $csRequest['process_action'] ?></textarea>
                    </td>
                </tr>
            <?php } ?>

        </tbody>

    </table>

</form>
<div class="m-t-10 text-center">
    <button type="button" id="save_btn" class="btnstyle1 btnstyle1-primary btnstyle1-lg" >등록</button>
</div>

<script>

    $(function(){

        $(".calendar-input input").datepicker(clareCalendar);

        function toggleActionDateRow() {
            var category = $('#category').val();
            if (category === '출고지정일') {
                $('#action_date_row').show();
            } else {
                $('#action_date_row').hide();
                $('#action_date').val('');
            }
        }

        if ($('#category').length) {
            toggleActionDateRow();
            $('#category').on('change', toggleActionDateRow);
        }
        
        $('#save_btn').click(function(e){
            e.preventDefault();

            if ($('#category').length) {
                var category = $('#category').val();
                var actionDate = $.trim($('#action_date').val());
                if (category === '출고지정일' && actionDate === '') {
                    alert('출고지정일 분류는 출고일자 입력이 필요합니다.');
                    $('#action_date').focus();
                    return;
                }
            }

            var formData = $('#cs_detail_form').serializeArray();
            ajaxRequest('/admin/cs/update_cs_status', formData)
                .then(res => {
                    if(res.success) {
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