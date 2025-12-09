<div id="contents_head">
	<h1>(RACK) 랙 관리</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="prdRack.reg()" > 
			<i class="fas fa-plus-circle"></i>
			신규 랙 생성
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

        코드체계
        1. 섹션(section)
        2. 베이(bay)
        3. 모듈(module)
        4. 단/층(floor)
        <table class="table-style">
            <thead>
                <tr class="list">
                    <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                    <th class="list-idx">고유번호</th>
                    <th class="">랙 이름</th>
                    <th class="">코드그룹</th>
                    <th class="">랙 코드</th>
                    <th class="">상품수</th>
                    <th>비고</th>
                    <th width="60px">관리</th>
                    <th>그룹명변경</th>
                    <th>상품이동</th>
                    <th>삭제</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($rackList as $group => $rack) {
                ?>
                <tr id="trid_<?=$rack['idx']?>" >
                    <td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$rack['idx']?>" ></td>	
                    <td class="list-idx"><?=$rack['idx']?></td>
                    <td class=""><b><?=$rack['name']?></b></td>
                    <td class="text-center"><?=$rack['code_group']?></td>
                    <td class="text-center"><?=$rack['code']?></td>
                    <td class="text-center" data-prd-count="<?=$rack['prd_count'] ?? 0?>">
                        <a href="/admin/product/product_stock?rack_code=<?=$rack['code']?>" target="_blank"><?=$rack['prd_count'] ?? 0?></a>
                    </td>
                    <td><?=$rack['memo'] ?? ''?></td>
                    <td class="text-center">
                        <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" 
                            onclick="prdRack.view(this, '<?=$rack['idx']?>')">수정</button>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-xs" 
                            onclick="prdRack.changeRackGroup('group', '<?=$rack['code_group']?>')">그룹명변경</button>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-xs" 
                            onclick="prdRack.changeRackGroup('move', '<?=$rack['code']?>')">상품이동</button>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" 
                            onclick="prdRack.deleteRack(this, '<?=$rack['idx']?>')">삭제</button>
                    </td>
                <tr>
                <?php } ?>
            </tbody>
		</table>

        <div id="contents_body_bottom_padding"></div>

    </div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>
<script type="text/javascript"> 
<!--

const prdRack = (function() {

    const API_ENDPOINTS = {
        deleteRack: '/admin/stock/delete_rack',
    };

    /**
     * 랙 등록
     */
    function reg() {

        openDialog('/admin/stock/rack_info', { }, '랙 신규생성');
    
    }


    /**
     * 랙 상세보기
     * 
     * @param object $obj
     * @param int $idx 랙 고유번호
     */
    function view(obj, idx) {

        const url = '/admin/stock/rack_info/' + idx;
        openDialog(url, { }, '랙 상세');

    }


    /**
     * 랙 그룹 변경
     * 
     * @param {number} idx - 랙 고유번호
     */
    function changeRackGroup(mode, code) {

        let title = '';

        if( mode == 'group' ){
            title = '랙 그룹명 변경';
        } else if( mode == 'move' ){
            title = '랙 이동';
        }

        openDialog('/admin/stock/rack_change/', { mode, code }, title);

    }


    /**
     * 랙 삭제
     * 
     * @param {number} idx - 랙 고유번호
     */
    function deleteRack(idx) {

        const prdCount = $(`#trid_${idx} td[data-prd-count]`).data('prd-count');

        if( prdCount > 0 ) {
            alert('해당 랙에 상품이 존재할 경우 삭제가 불가능 합니다');
            return;
        }

        dnConfirm('랙 삭제', '해당 랙을 삭제합니다<br/>해당랙에 상품이 존재할 경우 삭제가 불가능 합니다<br/>삭제 하시겠습니까?', function() {
            ajaxRequest(API_ENDPOINTS.deleteRack, { idx: idx }, {})
                .then(res => {
                    if (res.success) {
                        alert('랙 삭제 완료');
                        window.location.reload();
                    } else {
                        console.error('삭제 실패:', res.message);
                    }
                })
                .catch(err => {
                    console.error('삭제 오류:', err);
                });
        }, 'fas fa-trash', 'red', '삭제', 'btn-red', '취소');

    }

    return {
        init: function() {
            console.log('prdRack init');
        },
        reg,
        view,
        deleteRack,
        changeRackGroup,
    };

})();
//--> 
</script>