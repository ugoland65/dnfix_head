<div id="contents_head">
	<h1>C/S 관리</h1>
	<h3>C/S 목록입니다.</h3>

    <div class="head-count-tap-wrap m-l-20">
        <ul>
            <li>
                <span>전체</span>
            </li>

            <?php foreach ($csRequestCount as $row) { ?>
            <li>
                <span><?= $row['cs_status'] ?></span>
                <b><?= number_format($row['count']) ?></b>
            </li>
            <?php } ?>

            <li>
                <span>처리완료</span>
            </li>
        </ul>
    </div>


    <a href="http://gdadmin.dnfix202439.godomall.com/order/order_list_all.php" target="_blank" class="m-l-20" >
        <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" >
            고도몰 주문통합리스트
        </button>
    </a>
    
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="csCreate()" > 
			<i class="fas fa-plus-circle"></i>
			신규 등록
		</button>
	</div>

</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div id="list_new_wrap">

            <div class="table-top">
                <ul class="total">
                    Total : <span><b><?= number_format($pagination['total']) ?></b></span> &nbsp; | &nbsp;
                    <span><b><?= $pagination['current_page'] ?></b></span> / <?= $pagination['last_page'] ?> page
                </ul>

				<ul>
					<select name="s_cs_status" id="s_cs_status">
						<option value="">처리상태</option>
						<option value="요청" <? if ($s_cs_status == '요청') echo "selected"; ?>>요청</option>
						<option value="처리중" <? if ($s_cs_status == '처리중') echo "selected"; ?>>처리중</option>
                        <option value="요청+처리중" <? if ($s_cs_status == '요청+처리중') echo "selected"; ?>>요청+처리중</option>
						<option value="처리완료" <? if ($s_cs_status == '처리완료') echo "selected"; ?>>처리완료</option>
					</select>
				</ul>
				<ul>
					<input type="text" name="s_keyword" id="s_keyword" placeholder="회원 ID, 주문번호" value="<?= $s_keyword ?? '' ?>">
				</ul>
				<ul>
					<button type="button" id="searchBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm">
						<i class="fas fa-search"></i> 검색
					</button>
				</ul>
                <ul>
                    <button type="button" id="searchResetBtn" class="btnstyle1 btnstyle1-sm">
                        <i class="fas fa-undo"></i> 검색초기화
                    </button>
                </ul>
            </div>
            <div class="table-wrap5 m-t-5">
				<div class="scroll-wrap">

					<table class="table-st1">
						<thead>
							<tr>
								<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
								<th class="list-idx">고유번호</th>
                                <th class="">분류</th>
								<th class="">상태</th>
								<th class="">주문번호</th>
								<th class="">주문일</th>
								<th class="">회원ID</th>
                                <th class="">회원명</th>
                                <th class="">회원전화</th>
                                <th class="">수령자명</th>
                                <th class="">수령자전화</th>
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

                                if( $row['cs_status'] == '처리완료' ){
                                    $tr_class = 'status_end2';

                                }elseif( $row['cs_status'] == '처리중' ){
                                    $tr_class = 'status_bl';

                                }elseif( $row['cs_status'] == '요청' ){
                                        $tr_class = 'status_bl';
                                }else{
                                    $tr_class = '';
                                }

                        ?>
                            <tr class="<?= $tr_class ?>">
                                <td><input type="checkbox" name="check_idx[]" value="<?= $row['idx'] ?>"></td>
                                <td class="list-idx"><?= $row['idx'] ?></td>
                                <td><?= $row['category'] ?></td>
                                <td><?= $row['cs_status'] ?></td>
                                <td><a href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= $row['order_no'] ?>" target="_blank"><b><?= $row['order_no'] ?></b></a></td>
                                <td><?= $row['order_date'] ?></td>
                                <td>
                                    <a href="javascript:godoMemberCrm('<?= $row['mem_no'] ?>');"><?= $row['mem_id'] ?></a><br>
                                    <a href="javascript:godoMemberCrm('<?= $row['mem_no'] ?>');"><?= $row['group_nm'] ?></a>
                                </td>
                                <td><?= $row['mem_name'] ?></td>
                                <td>
                                    <?php if( !empty($row['mem_no']) && !empty($row['mem_phone']) ) { ?>
                                        <button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="godoMemberSms('<?= $row['mem_no'] ?>');"><?= $row['mem_phone'] ?></button>
                                    <?php }elseif( !empty($row['mem_no']) && empty($row['mem_phone']) ) { ?>
                                        <button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="godoMemberCrm('<?= $row['mem_no'] ?>');">회원에게 문자발송</button>
                                    <?php }else{ ?>
                                        <?= $row['mem_phone'] ?>
                                    <?php } ?>
                                </td>
                                <td><?= $row['receiver_name'] ?></td>
                                <td><?= $row['receiver_phone'] ?></td>
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
<div id="contents_bottom">
	<div class="pageing-wrap"><?= $paginationHtml ?></div>
	<div class="m-l-20">

        <?php /*
		선택된 상품 <span id="selected_product_count">0</span>
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" id="workRequestBtn">선택한 상품 업무요청하기</button>
        */ ?>

	</div>
</div>
<script>

    function godoMemberCrm(mem_no){

        window.open(
            "http://gdadmin.dnfix202439.godomall.com/share/member_crm.php?popupMode=yes&navTabs=summary&memNo="+ mem_no, 
            "crm_member_"+mem_no, "width=1190,height=850,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");

    }

    function godoMemberSms(mem_no){

        window.open(
            "http://gdadmin.dnfix202439.godomall.com/member/sms_send.php?receiverMemNo="+ mem_no +"&receiverNm=&receiverPhone=&smsFl=", 
            "sms_member_"+mem_no, "width=1000,height=900,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");

    }

    function csCreate(){
        openDialog("/admin/cs/cs_create", { mode: 'create' }, "C/S 생성", "800px");
    }
    function csDetail(idx){
        openDialog("/admin/cs/cs_detail/" + idx, { idx: idx }, "C/S 상세", "800px");
    }

	// 검색 파라미터 수집 공통 함수
	function getSearchParams(additionalParams) {
		var params = {};

		// 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
		var fields = {
			's_cs_status': $("#s_cs_status").val(),
			's_keyword': $("#s_keyword").val(),
		};

		// 추가 파라미터가 있으면 병합
		if (additionalParams) {
			fields = Object.assign(fields, additionalParams);
		}

		// 유효한 값만 params에 추가
		for (var key in fields) {
			if (fields[key] !== undefined && fields[key] !== null && fields[key] !== '') {
				params[key] = fields[key];
			}
		}

		return params;
	}

	// 검색 파라미터로 페이지 이동
	function navigateWithParams(params) {
		// URL 쿼리 문자열 생성
		var queryString = Object.keys(params)
			.map(function(key) {
				return key + '=' + encodeURIComponent(params[key]);
			})
			.join('&');

		// 페이지 이동
		location.href = '/admin/cs/cs_list' + (queryString ? '?' + queryString : '');
	}

    $(function() {

		$("#searchBtn").on('click', function() {
			// 검색 파라미터 수집
			var params = getSearchParams();

			// 페이지 이동
			navigateWithParams(params);
		});

		$("#searchResetBtn").on('click', function() {
			location.href = '/admin/cs/cs_list';
		});

    });

</script>