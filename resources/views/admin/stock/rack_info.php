<div class="os-form-wrap">

    <form id="rackInfoForm">

        <? if( !empty($rackInfo['idx']) ){ ?>
            <input type="hidden" name="idx" value="<?=$rackInfo['idx']?>" >
        <? } ?>

        <table class="table-style border01 width-full">

            <tr>
                <th>랙 이름</th>
                <td colspan="3">
                    <input type='text' name='name'  value="<?=$rackInfo['name'] ?? ''?>" autocomplete="off" >
                </td>
            </tr>
            <tr>
                <th>랙 코드</th>
                <td colspan="3">
                    <input type='text' name='code'  value="<?=$rackInfo['code'] ?? ''?>" autocomplete="off" >
                    <div class="admin-guide-text">
                        - 상품에 지정될 코드
                    </div>
                </td>
            </tr>
            <tr>
                <th>메모</th>
                <td colspan="3">
                    <textarea name="memo"><?=$rackInfo['memo'] ?? ''?></textarea>
                </td>
            </tr>

        </table>
    </form>
    <div class="m-t-10 text-center">
        <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="rackInfo.save(this);" >전송</button>
    </div>

</div>

<script type="text/javascript">
var rackInfo = (function() {

    const API_ENDPOINTS = {
        saveRack: '/admin/stock/save_rack',
    };

    /**
     * 랙 저장
     * 
     */
    function save() {

        const formData = $("#rackInfoForm").serialize();

        ajaxRequest(API_ENDPOINTS.saveRack, formData, {})
            .then(res => {
                if (res.success) {
                    alert('랙 등록 완료');
                    window.location.reload();
                } else {
                    console.error('저장 실패:', res.message);
                }

            })
            .catch(err => {
                console.error('저장 오류:', err);
            });

    }

    return {
        save,
    };

})();
</script>
