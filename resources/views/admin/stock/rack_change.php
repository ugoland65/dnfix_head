<div class="os-form-wrap">

    <form id="rackChangeForm">
        <input type="hidden" name="mode" value="<?=$mode?>" >
        <input type="hidden" name="code" value="<?=$code?>" >

        <table class="table-style border01 width-full">

            <tr>
                <th> <?=$mode == 'group' ? '선택 랙 그룹' : '선택 랙 코드'?></th>
                <td colspan="3">
                    <?=$code?>
                </td>
            </tr>
            <tr>
                <th><?=$mode == 'group' ? '변경할 랙 그룹명' : '이동할 랙 코드'?></th>
                <td colspan="3">
                    <input type='text' name='change_code'  value="" autocomplete="off" >
                </td>
            </tr>
            <tr>
                <th>주의사항</th>
                <td colspan="3">
                    <b>랙 그룹명 변경</b><br/>
                    - 랙 그룹을 새로운 그룹명으로 변경합니다.<br/>
                    - 변경할 랙 코드는 중복될 수 없습니다.<br/>
                    - 기존에 랙 그룹이 존재한다면 랙 그룹을 변경할 수 없습니다.<br/>
                    <br/>
                    <b>랙 상품 이동</b><br/>
                    - 변경할 랙 코드로 상품을 이동합니다.<br/>
                    - 한번 이동된 상품은 다시 되돌릴 수 없습니다.<br/>
                    - 신중하게 이동해주세요.
                </td>
            </tr>

        </table>
    </form>
    <div class="m-t-10 text-center">
        <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="rackChange.save(this);" >전송</button>
    </div>

</div>
<script type="text/javascript">
var rackChange = (function() {

    const API_ENDPOINTS = {
        saveRackChange: '/admin/stock/save_rack_change',
    };

    /**
     * 랙 그룹 변경 저장
     * 
     */
    function save() {

        const formData = $("#rackChangeForm").serialize();
        
        ajaxRequest(API_ENDPOINTS.saveRackChange, formData, {})
            .then(res => {
                if (res.success) {
                    alert('랙 그룹 변경 완료');
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