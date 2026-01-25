<div id="contents_head">
	<h1>C/S 관리</h1>
	<h3>C/S 목록입니다.</h3>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div id="list_new_wrap">

            <div class="table-wrap5 m-t-5">
				<div class="scroll-wrap">

					<table class="table-st1">
						<thead>
							<tr>
								<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
								<th class="list-idx">고유번호</th>
								<th class="">상태</th>
								<th class="">주문번호</th>
								<th class="">주문일</th>
								<th class="">회원ID</th>
                                <th class="">요청내용</th>
                                <th class="">댓글</th>
                                <th class="">등록자</th>
								<th class="">등록일</th>
                                <th class="">상태변경</th>
                                <th class="">처리내용</th>
                                <th class="">처리자</th>
                                <th class="">처리일</th>
							</tr>
						</thead>
						<tbody>
                        <?php
                            foreach ($csRequestList as $row) {
                        ?>
                            <tr>
                                <td><input type="checkbox" name="check_idx[]" value="<?= $row['idx'] ?>"></td>
                                <td class="list-idx"><?= $row['idx'] ?></td>
                                <td><?= $row['cs_status'] ?></td>
                                <td><a href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= $row['order_no'] ?>" target="_blank"><b><?= $row['order_no'] ?></b></a></td>
                                <td><?= $row['order_date'] ?></td>
                                <td>
                                    <a href="javascript:godoMemberCrm('<?= $row['mem_no'] ?>');"><?= $row['mem_id'] ?></a><br>
                                    <a href="javascript:godoMemberCrm('<?= $row['mem_no'] ?>');"><?= $row['group_nm'] ?></a>
                                </td>
                                <td><?= nl2br($row['cs_body']) ?></td>
                                <td class="text-left">
                                    <button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="footerGlobal.comment('cs','<?=$row['idx']?>')" >
                                        댓글
                                        <? if( $row['comment_count'] > 0 ) { ?> : <b><?=$row['comment_count']?></b><? } ?>
                                    </button>
                                </td>
                                <td><?= $row['reg_name'] ?></td>
                                <td><?= date('Y.m.d H:i', strtotime($row['created_at'])) ?></td>
                                <td><button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="csDetail('<?=$row['idx']?>')" >상태변경</button></td>
                                <td><?= nl2br($row['process_action']) ?></td>
                                <td><?= $row['processor_name'] ?></td>
                                <td>
                                    <?php if( $row['processor_date'] ) { ?>
                                        <?= date('Y.m.d H:i', strtotime($row['processor_date'])) ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>

        </div>
	</div>
</div>
<script>
    function godoMemberCrm(mem_no){
        window.open(
            "http://gdadmin.dnfix202439.godomall.com/share/member_crm.php?popupMode=yes&navTabs=summary&memNo="+ mem_no, 
            "crm_member_"+mem_no, "width=1190,height=850,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
    }

    function csDetail(idx){
        openDialog("/admin/cs/cs_detail/" + idx, { idx: idx }, "C/S 상세", "800px");
    }
</script>