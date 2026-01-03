<?php
extends_layout('admin.layout.layout', [
    'pageGroup2' => 'staff',
    'pageCode' => 'staff_list'
]);
?>
<div id="contents_head">
	<h1>직원/계정 관리</h1>
    <?php 
        if( $auth['ad_level'] == 100 ){
    ?>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="staff.reg()" > 
			<i class="fas fa-plus-circle"></i>
			신규 직원등록
		</button>
	</div>
    <?php } ?>
</div>
<div id="contents_body">
    <div id="contents_body_wrap" >

        <!-- 검색 영역 -->
        <div class="top-search-wrap">
            <ul>
                <select name="work_status" id="work_status" >
                    <option value="">재직상태</option>
                    <option value="재직중" <? if( $work_status == '재직중' ) echo "selected";?> >재직중</option>
                    <option value="휴직" <? if( $work_status == '휴직' ) echo "selected";?> >휴직</option>
                    <option value="퇴사" <? if( $work_status == '퇴사' ) echo "selected";?> >퇴사</option>
                    <option value="기타" <? if( $work_status == '기타' ) echo "selected";?> >기타</option>
                </select>
            </ul>
        </div>

        <div id="list_new_wrap" class="m-t-10">
            <div class="table-wrap5">
                <div class="scroll-wrap">

                    <table class="table-st1">
                        <thead>
                            <tr class="list">
                                <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                                <th class="list-idx">고유번호</th>
                                <th class="list-idx">재직상태</th>
                                <th class="">사번</th>
                                <th class="">직책</th>
                                <th class="">직함</th>
                                <th class="">부서</th>
                                <th>직원명</th>
                                <th>아이디</th>
                                <th>닉네임</th>
                                <th>고용형태</th>
                                <th>생년월일</th>
                                <th>입사일</th>
                                <th>구글 아이디</th>
                                <th>연락처</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ($adminList as $admin) { ?>
                                <tr>
                                    <td><input type="checkbox" name="check_idx[]" value="<?=$admin['idx']?>"></td>
                                    <td><?=$admin['idx']?></td>
                                    <td><?=$admin['ad_work_status']?></td>
                                    <td><?=$admin['ad_employee_id']?></td>
                                    <td><?=$admin['ad_role']?></td>
                                    <td><?=$admin['ad_title']?></td>
                                    <td><?=$admin['ad_department']?></td>
                                    <td><?=$admin['ad_name']?></td>
                                    <td><?=$admin['ad_id']?></td>
                                    <td><?=$admin['ad_nick']?></td>
                                    <td><?=$admin['ad_job_type']?></td>
                                    <td><?=$admin['ad_birth']?></td>
                                    <td><?=$admin['ad_joining']?></td>
                                    <td>
                                        <?php if( $admin['ad_work_status'] == '재직중' || $auth['ad_level'] == 100 ){ ?>
                                            <?=$admin['ad_google']?>
                                        <?php } else { ?>
                                            ***@gmail.com
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if( $admin['ad_work_status'] == '재직중' || $auth['ad_level'] == 100 ){ ?>
                                            <?=$admin['ad_data']['tel'] ?? '-'?>
                                        <?php } else { ?>
                                            ***-****-****
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if( $auth['ad_id'] == $admin['ad_id'] || $auth['ad_level'] == 100 ){
                                        ?>
                                        <button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="staff.view(this, '<?=$admin['idx']?>')"> 수정 </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <? } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"><?=$paginationHtml?></div>
</div>
<script type="text/javascript">

const staff = function() {
    
    /**
     * 신규 직원등록
     */
    function reg() {
        openDialog('/admin/staff/reg', { }, '신규 직원등록');
    }

    /**
     * 직원 상세 조회
     */
    function view(obj, idx) {
        openDialog('/admin/staff/info', { idx }, '직원 상세');
    }

    return {
        reg,
        view
    }
}();

$(function() {

    //재직상태 변경
    $('#work_status').change(function() {
        var work_status = $(this).val();
        window.location.href = '/admin/staff/list?work_status=' + work_status;
    });

})();
</script>
