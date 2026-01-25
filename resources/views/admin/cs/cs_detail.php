<form id="cs_detail_form">
    <input type="hidden" name="idx" value="<?= $csRequest['idx'] ?>">

    <table class="table-style border01 width-full">
        <colgroup>
            <col width="120px">
            <col>
        </colgroup>

        <tbody>
            <tr>
                <th>주문번호</th>
                <td><?= $csRequest['order_no'] ?></td>
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
            <tr>
        </tbody>
    </table>

</form>
<div class="m-t-10 text-center">
    <button type="button" id="save_btn" class="btnstyle1 btnstyle1-primary btnstyle1-lg" >등록</button>
</div>

<script>
    $(function(){
        $('#save_btn').click(function(e){
            e.preventDefault();
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