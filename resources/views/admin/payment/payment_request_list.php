<style type="text/css">
    .copy-cell-wrap {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        max-width: 100%;
    }

    .copy-target {
        min-width: 0;
    }

    .copy-btn {
        border: 1px solid #d0d7de;
        background: #f2f4f7;
        color: #57606a;
        border-radius: 6px;
        width: 20px;
        height: 20px;
        padding: 0;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .copy-btn:hover {
        background: #e9edf2;
    }

    .copy-btn.is-copied {
        border-color: #16a34a;
        background: #16a34a;
        color: #ffffff;
    }

    .copy-btn.is-copied .copy-icon {
        display: none;
    }

    .copy-btn.is-copied::before {
        content: '✓';
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
    }

    .copy-btn.is-copied::after {
        content: '복사됨';
        position: absolute;
        top: -22px;
        left: 50%;
        transform: translateX(-50%);
        background: #111827;
        color: #ffffff;
        font-size: 10px;
        line-height: 1;
        padding: 3px 6px;
        border-radius: 4px;
        white-space: nowrap;
        z-index: 2;
    }

    .copy-btn .copy-icon {
        width: 16px;
        height: 16px;
        display: block;
        color: currentColor;
    }
</style>

<div id="contents_head">
	<h1>결제요청 관리</h1>
	<h3>결제요청 목록입니다.</h3>

    <div id="head_write_btn">
		<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="paymentRequestCreate()">
			<i class="fas fa-plus-circle"></i>
			결제요청 등록
		</button>
		<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="personalPaymentCreate()" style="margin-left:6px;">
			<i class="fas fa-plus-circle"></i>
			개인결제 등록
		</button>
	</div>

</div>
<?php $tab = ($tab ?? '') === 'personal' ? 'personal' : 'vendor'; ?>
<div style="display:flex; gap:8px; margin:0 0 12px;">
    <a class="btnstyle1 <?= $tab === 'vendor' ? 'btnstyle1-primary' : '' ?> btnstyle1-sm" href="/admin/payment/payment_request_list">거래처 결제요청</a>
    <a class="btnstyle1 <?= $tab === 'personal' ? 'btnstyle1-primary' : '' ?> btnstyle1-sm" href="/admin/payment/payment_request_list?tab=personal">개인결제 정산</a>
</div>
<?php if ($tab === 'personal') {
    $personalList = $personalList ?? [];
    $personalSummary = $personalSummary ?? [];
?>
<div id="contents_body">
    <div id="contents_body_wrap">
        <div id="list_new_wrap">
            <div class="table-top">
                <ul class="total">
                    정산요청 <b><?= number_format((float)($personalSummary['request_total'] ?? 0)) ?></b>원
                    · 검토중 <b><?= number_format((float)($personalSummary['review_total'] ?? 0)) ?></b>원
                    · 지급예정 <b><?= number_format((float)($personalSummary['scheduled_total'] ?? 0)) ?></b>원
                    · 미지급 <b><?= number_format((float)($personalSummary['unpaid_total'] ?? 0)) ?></b>원
                    · 지급완료 <b><?= number_format((float)($personalSummary['paid_total'] ?? 0)) ?></b>원
                </ul>
                <ul>
                    <select name="s_status" id="s_status">
                        <option value="">정산상태</option>
                        <?php foreach (['정산요청', '확인중', '지급예정', '지급완료', '보류', '반려'] as $statusOption) { ?>
                            <option value="<?= $statusOption ?>" <?= ($s_status ?? '') === $statusOption ? 'selected' : '' ?>><?= $statusOption ?></option>
                        <?php } ?>
                    </select>
                </ul>
                <ul>
                    <input type="text" id="s_use_date_start" value="<?= htmlspecialchars((string)($s_use_date_start ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="사용일 시작" style="width:110px;">
                    <input type="text" id="s_use_date_end" value="<?= htmlspecialchars((string)($s_use_date_end ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="사용일 종료" style="width:110px;">
                </ul>
                <ul>
                    <input type="text" id="s_keyword" placeholder="신청자, 사용처, 사용내용" value="<?= htmlspecialchars((string)($s_keyword ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </ul>
                <ul>
                    <button type="button" id="searchBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm"><i class="fas fa-search"></i> 검색</button>
                </ul>
                <ul>
                    <button type="button" id="searchResetBtn" class="btnstyle1 btnstyle1-sm"><i class="fas fa-undo"></i> 검색초기화</button>
                </ul>
            </div>
            <div class="table-wrap5 m-t-5">
                <div class="scroll-wrap">
                    <table class="table-st1">
                        <thead>
                            <tr>
                                <th>번호</th>
                                <th>상태</th>
                                <th>신청자</th>
                                <th>사용일자</th>
                                <th>사용처</th>
                                <th>사용내용</th>
                                <th>결제수단</th>
                                <th>결제금액</th>
                                <th>정산요청</th>
                                <th>지급계좌</th>
                                <th>증빙</th>
                                <th>상세</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($personalList)) { ?>
                                <tr><td colspan="12">등록된 개인결제 정산요청이 없습니다.</td></tr>
                            <?php } ?>
                            <?php foreach ($personalList as $row) { ?>
                                <tr>
                                    <td><?= (int)($row['idx'] ?? 0) ?></td>
                                    <td><?= htmlspecialchars((string)($row['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($row['applicant_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($row['use_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($row['vendor'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= nl2br(htmlspecialchars((string)($row['purpose'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></td>
                                    <td><?= htmlspecialchars((string)($row['pay_method'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-right"><?= number_format((float)($row['payment_amount'] ?? 0)) ?></td>
                                    <td class="text-right"><b><?= number_format((float)($row['request_amount'] ?? 0)) ?></b></td>
                                    <td><?= htmlspecialchars((string)($row['bank'] ?? ''), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string)($row['bank_account_masked'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-center"><?= (int)($row['file_count'] ?? 0) ?></td>
                                    <td><button type="button" class="btnstyle1 btnstyle1-sm" onclick="personalPaymentDetail('<?= (int)($row['idx'] ?? 0) ?>')">상세</button></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap"><?= $personalPaginationHtml ?? '' ?></div>
</div>
<?php } else { ?>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div id="list_new_wrap">

            <div class="table-top">
                <ul class="total">
                    Total : <span><b><?= number_format($pagination['total']) ?></b></span> &nbsp; | &nbsp;
                    <span><b><?= $pagination['current_page'] ?></b></span> / <?= $pagination['last_page'] ?> page
                </ul>

				<ul>
					<select name="s_status" id="s_status">
						<option value="">처리상태</option>
						<option value="요청" <? if ($s_status == '요청') echo "selected"; ?>>요청</option>
						<option value="처리완료" <? if ($s_status == '처리완료') echo "selected"; ?>>처리완료</option>
						<option value="반려" <? if ($s_status == '반려') echo "selected"; ?>>반려</option>
					</select>
				</ul>
				<ul>
					<input type="text" name="s_keyword" id="s_keyword" placeholder="결제은행, 요청내용, 주문번호" value="<?= $s_keyword ?? '' ?>">
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
								<th class="">요청금액</th>
                                <th class="">결제은행</th>
                                <th class="">요청내용</th>
								<th class="">희망결제일</th>
                                <th class="">요청자</th>
								<th class="">요청일</th>
                                <th class="">참조위치</th>
                                <th class="">참조번호</th>
                                <th class="">첨부파일</th>

                                <th class="">상태변경</th>

                                <th class="">참조 주문번호<br>(고도몰)</th>

                                <th class="">결제완료일</th>
                                <th class="">결제완료자</th>
                                <th class="">처리내용</th>

							</tr>
						</thead>
						<tbody>
                        <?php
                            foreach ($paymentRequestList as $row) {

                                if( $row['status'] == '처리완료' ){
                                    $tr_class = 'status_end2';

                                }elseif( $row['status'] == '처리중' ){
                                    $tr_class = 'status_bl';

                                }elseif( $row['status'] == '요청' ){
                                        $tr_class = 'status_bl';
                                }else{
                                    $tr_class = '';
                                }

                                if( $row['currency'] == 'KRW' ){
                                    $bank = "[".$row['bank']."] ".$row['bank_account']." (".$row['depositor'].")";
                                }else{
                                    $bank = nl2br($row['foreign_account']);
                                }
                                $bank = trim((string)$bank);
                                $bankAccountForCopy = trim((string)($row['bank_account'] ?? ''));

                                if( $row['kind'] == 'order_sheet' ){
                                    $reference_location = '주문발주';
                                    $reference_number = $row['kind_idx'];
                                }elseif( $row['kind'] == 'godo_refund' ){
                                    $reference_location = '고도몰 주문';
                                    $reference_number = $row['meta_json']['godo_order_no'] ?? '';
                                }else{
                                    $reference_location = '';
                                    $reference_number = '';
                                }

                        ?>
                            <tr class="<?= $tr_class ?>">
                                <td><input type="checkbox" name="check_idx[]" value="<?= $row['idx'] ?>"></td>
                                <td class="list-idx"><?= $row['idx'] ?></td>
                                <td><?= $row['category'] ?></td>
                                <td><?= $row['status'] ?></td>
                                <td class="text-right">
                                    <?= $row['currency'] ?> <b><?= number_format($row['amount']) ?></b>
                                </td>
                                <td style="min-width:350px; white-space:normal;">
                                    <span class="copy-cell-wrap" >
                                        <span class="copy-target"><?= $bank ?></span>
                                        <?php if ($bankAccountForCopy !== '') { ?>
                                            <button type="button" class="copy-btn" title="복사" aria-label="은행계좌 복사" data-copy-text="<?= htmlspecialchars($bankAccountForCopy, ENT_QUOTES, 'UTF-8') ?>" onclick="copyCellText(this)">
                                                <svg class="copy-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"></rect>
                                                    <path d="M5 15V7C5 5.9 5.9 5 7 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                </svg>
                                            </button>
                                        <?php } ?>
                                    </span>
                                </td>
                                <td><?= nl2br($row['memo']) ?></td>
                                <td><?= $row['request_date'] ?></td>
                                <td><?= $row['ad_name'] ?></td>
                                <td><?= date('Y.m.d H:i', strtotime($row['created_at'])) ?></td>
                                <td class="text-center"><?= $reference_location ?></td>
                                <td class="text-center">
                                    <?php if( $row['kind'] == 'order_sheet' ){ ?>
                                        <button type="button" class="btnstyle1 btnstyle1-sm" onclick="orderSheet.osView(this, '<?= $row['kind_idx'] ?>','main')">주문서 : #<?= $row['kind_idx'] ?></button>
                                    <?php }elseif( $row['kind'] == 'purchase_order' ){ ?>
                                        <a class="btnstyle1 btnstyle1-sm" style="text-decoration:none;" href="/admin/order/purchase/detail?idx=<?= (int)($row['kind_idx'] ?? 0) ?>" target="_blank" rel="noopener noreferrer">발주 : #<?= (int)($row['kind_idx'] ?? 0) ?></a>
                                    <?php }elseif( $row['kind'] == 'godo_refund' ){ ?>
                                        <a class="btnstyle1 btnstyle1-sm" style="text-decoration:none;" href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= $row['meta_json']['godo_order_no'] ?>" target="_blank" rel="noopener noreferrer">고도몰 주문 : <?= $row['meta_json']['godo_order_no'] ?></a>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php $invoiceFilePath = trim((string)($row['invoice_file_path'] ?? '')); ?>
                                    <?php if ($invoiceFilePath !== '' && strpos($invoiceFilePath, '/data/payment_request/') === 0) { ?>
                                        <a
                                            href="<?= htmlspecialchars($invoiceFilePath, ENT_QUOTES, 'UTF-8') ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="btnstyle1 btnstyle1-info btnstyle1-xs"
                                            title="<?= htmlspecialchars(basename($invoiceFilePath), ENT_QUOTES, 'UTF-8') ?>"
                                            style="text-decoration:none;" 
                                        ><i class="fas fa-file-download"></i> 첨부파일 보기</a>
                                    <?php } else { ?>
                                        <span class="text-gray">-</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <button type="button" class="btnstyle1 btnstyle1-sm" onclick="paymentRequestDetail('<?= $row['idx'] ?>')">상태변경</button>
                                </td>

                                <!-- 참조 주문번호 -->
                                <td>
                                    <?php
                                    $godoOrderNos = $row['meta_json']['godo_order_no'] ?? [];
                                    if (!is_array($godoOrderNos)) {
                                        $godoOrderNos = [$godoOrderNos];
                                    }
                                    $godoOrderNos = array_values(array_filter(array_map(static function ($orderNo) {
                                        return trim((string)$orderNo);
                                    }, $godoOrderNos), static function ($orderNo) {
                                        return $orderNo !== '';
                                    }));
                                    ?>
                                    <?php if (!empty($godoOrderNos)) { ?>
                                        <?php foreach ($godoOrderNos as $godoOrderNo) { ?>
                                            <a
                                                class="btnstyle1 btnstyle1-sm"
                                                style="display:block; margin-bottom:3px; text-decoration:none;"
                                                href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= rawurlencode($godoOrderNo) ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >고도몰 주문 : <?= htmlspecialchars($godoOrderNo, ENT_QUOTES, 'UTF-8') ?></a>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span class="text-gray">-</span>
                                    <?php } ?>
                                </td>
                                
                                <!-- 결제 완료일 -->
                                <td><?= $row['process_date'] ?? '' ?></td>
                                <td><?= $row['approved_ad_name'] ?? '' ?></td>
                                <td><?= $row['process_memo'] ?? '' ?></td>
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
<?php } ?>
<script src="/admin2/js/order_sheet.js?ver=<?=time()?>"></script>
<script>

    function copyTextWithFallback(text) {
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'fixed';
        textarea.style.top = '-9999px';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);

        textarea.focus();
        textarea.select();
        textarea.setSelectionRange(0, textarea.value.length);

        var copied = false;
        try {
            copied = document.execCommand('copy');
        } catch (e) {
            copied = false;
        }
        document.body.removeChild(textarea);
        return copied;
    }

    function setCopyButtonFeedback(button) {
        if (!button) return;
        button.classList.add('is-copied');
        button.title = '복사됨';
        setTimeout(function() {
            button.classList.remove('is-copied');
            button.title = '복사';
        }, 1200);
    }

    function copyCellText(button) {
        var textNode = button && button.parentNode ? button.parentNode.querySelector('.copy-target') : null;
        var attrText = button ? String(button.getAttribute('data-copy-text') || '').trim() : '';
        var text = attrText !== '' ? attrText : (textNode ? (textNode.textContent || '').trim() : '');
        if (text === '') {
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                setCopyButtonFeedback(button);
            }).catch(function() {
                if (copyTextWithFallback(text)) {
                    setCopyButtonFeedback(button);
                } else {
                    alert('복사에 실패했습니다.');
                }
            });
            return;
        }

        if (copyTextWithFallback(text)) {
            setCopyButtonFeedback(button);
        } else {
            alert('복사에 실패했습니다.');
        }
    }

    function paymentRequestCreate(){
        openDialog("/admin/payment/payment_request_create", { mode: 'create' }, "결제요청 생성", "800px", "POST" );
    }

    function personalPaymentCreate(){
        openDialog("/admin/payment/personal_payment_create", { mode: 'create' }, "개인결제 정산요청", "860px", "POST" );
    }

    function personalPaymentDetail(idx){
        openDialog("/admin/payment/personal_payment_detail", { idx: idx }, "개인결제 정산 상세", "860px", "GET" );
    }

    function paymentRequestDetail(idx){
        openDialog("/admin/payment/payment_request_detail", { mode: 'detail', idx: idx }, "결제요청 상세", "800px", "GET" );
    }

	// 검색 파라미터 수집 공통 함수
	function getSearchParams(additionalParams) {
		var params = {};

		// 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
		var fields = {
			'tab': <?= json_encode($tab ?? 'vendor') ?>,
			's_status': $("#s_status").val(),
			's_keyword': $("#s_keyword").val(),
			's_use_date_start': $("#s_use_date_start").val(),
			's_use_date_end': $("#s_use_date_end").val(),
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
		location.href = '/admin/payment/payment_request_list' + (queryString ? '?' + queryString : '');
	}

    $(function() {

        $("#searchBtn").on('click', function() {
            // 검색 파라미터 수집
            var params = getSearchParams();

            // 페이지 이동
            navigateWithParams(params);
        });

        $("#searchResetBtn").on('click', function() {
            location.href = '/admin/payment/payment_request_list<?= ($tab ?? '') === 'personal' ? '?tab=personal' : '' ?>';
        });
        if (typeof clareCalendar !== 'undefined') {
            $('#s_use_date_start, #s_use_date_end').datepicker(clareCalendar);
        }

    });

</script>