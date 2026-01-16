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
								<th class="">등록일</th>
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
                                <td><?= $row['mem_id'] ?></td>
                                <td><?= $row['cs_body'] ?></td>
                                <td><?= $row['created_at'] ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>

        </div>
	</div>
</div>