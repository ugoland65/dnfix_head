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
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="paymentRequestCreate()" > 
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
					<select name="s_status" id="s_status">
						<option value="">처리상태</option>
						<option value="요청" <? if ($s_status == '요청') echo "selected"; ?>>요청</option>
						<option value="처리완료" <? if ($s_status == '처리완료') echo "selected"; ?>>처리완료</option>
						<option value="반려" <? if ($s_status == '반려') echo "selected"; ?>>반려</option>
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
								<th class="">요청금액</th>
                                <th class="">결제은행</th>
                                <th class="">요청내용</th>
								<th class="">희망결제일</th>
                                <th class="">요청자</th>
								<th class="">요청일</th>
                                <th class="">참조위치</th>
                                <th class="">참조번호</th>

                                <th class="">상태변경</th>
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
                                        <button type="button" class="btnstyle1 btnstyle1-sm" onclick="orderSheet.osView(this, '<?= $row['kind_idx'] ?>','main')">#<?= $row['kind_idx'] ?></button>
                                    <?php }elseif( $row['kind'] == 'godo_refund' ){ ?>
                                        <a href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= $row['meta_json']['godo_order_no'] ?>" target="_blank"><?= $row['meta_json']['godo_order_no'] ?></a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <button type="button" class="btnstyle1 btnstyle1-sm" onclick="paymentRequestDetail('<?= $row['idx'] ?>')">상태변경</button>
                                </td>
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

    function paymentRequestDetail(idx){
        openDialog("/admin/payment/payment_request_detail", { mode: 'detail', idx: idx }, "결제요청 상세", "800px", "GET" );
    }

	// 검색 파라미터 수집 공통 함수
	function getSearchParams(additionalParams) {
		var params = {};

		// 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
		var fields = {
			's_status': $("#s_status").val(),
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
            location.href = '/admin/payment/payment_request_list';
        });

    });

</script>