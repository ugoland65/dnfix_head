<style>
    .layout-style1 {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        box-sizing: border-box;

        >ul {

            width: 350px;
            height: 100%;
            padding: 0;

            .layout-style1-section-top {
                height: 30px;
            }

            .layout-style1-section {
                height: calc(100% - 30px);
                border: 1px solid #ccc;
                background-color: #fff;
            }

        }

        >ul:first-child {
            flex: 1;
        }

    }

    .order_num_list {

        display: none;

        .table-st1 {
            border-top: 1px solid #b4b4b4;
            border-left: 1px solid #b4b4b4;
            border-bottom: 1px solid #b4b4b4;

            thead tr {
                position: static;
            }

            tfoot>tr {
                position: static;
            }
        }
    }
</style>

<div id="contents_head">
    <h1>고도몰 주문 상품별 가져오기</h1>
    <div class="m-l-20">
        <select name="mode" id="mode">
            <option value="p" <?= $mode == 'p' ? 'selected' : '' ?>>결제완료</option>
            <option value="g" <?= $mode == 'g' ? 'selected' : '' ?>>준비중</option>
            <option value="d" <?= $mode == 'd' ? 'selected' : '' ?>>배송중</option>
            <option value="ds" <?= $mode == 'ds' ? 'selected' : '' ?>>배송완료</option>
        </select>
        <label class="calendar-input">
            <input type='text' name='start_date' id="start_date" value="<?= $start_date ?? date('Y-m-d') ?>">
        </label>
        ~
        <label class="calendar-input">
            <input type='text' name='end_date' id="end_date" value="<?= $end_date ?? date('Y-m-d') ?>">
        </label>
        <button type="button" id="searchBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm ">조회</button>
    </div>
</div>

<div id="contents_body">
    <div id="contents_body_wrap">

        <div class="layout-style1">
            <ul>
                <div class="scroll-wrap">
                    <table class="table-st1">
                        <thead>
                            <tr>
                                <th class="">No</th>
                                <th class="">재고코드</th>
                                <th class="">이미지</th>
                                <th class="">상품명</th>
                                <th class="">주문수량</th>
                                <th class="">주문</th>
                                <th class="">패키지 제거</th>
                                <th class="">출고수량</th>
                                <th class="">현재재고</th>
                                <th class="">남은재고</th>
                                <th class="">고도몰<br/>현재고</th>
                                <th class="">고도몰<br/>상품관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 0;
                                foreach ($orderList['stock'] as $key => $item) {
                                    $no++;

                                    $img_path = "";

                                    if( $item['product_data']['img_mode'] == 'out' ){
                                        $img_path = $item['product_data']['CD_IMG'];
                                    }else{
                                        if( $item['product_data']['CD_IMG'] ){
                                            $img_path = '/data/comparion/'.$item['product_data']['CD_IMG'];
                                        }
                                    }

                                    //재고 등록후 품절
                                    if( $item['after_goods_qty'] == 0 ){
                                        $tr_class = 'green';
                                    }else{
                                        $tr_class = '';
                                    }
                            ?>
                                <tr class="<?= $tr_class ?>">
                                    <td><?= $no ?></td>
                                    <td><?= $key ?></td>
                                    <td class="p-5">
                                        <p onclick="onlyAD.prdView('<?=$item['product_data']['CD_IDX']?>','info');" style="cursor:pointer;" ><img src="<?=$img_path?>" style="height:50px; border:1px solid #eee !important;"></p>
                                    </td>
                                    <td>
                                        <?php if( $item['after_goods_qty'] == 0 ){ ?>
                                            <p><span class="text-red">재고 등록후 품절</span></p>
                                        <?php } ?>
                                        <p onclick="onlyAD.prdView('<?=$item['product_data']['CD_IDX']?>','info');" style="cursor:pointer;" ><b><?= $item['product_data']['CD_NAME'] ?></b></p>
                                        <?= $item['goodsNm'] ?>
                                    </td>
                                    
                                    <td class="text-right"><?= $item['order_qty'] ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btnstyle1 btnstyle1-xs order-toggle">▼</button>
                                    </td>
                                    <td class="text-right">
                                        <?= $item['package_remove_qty'] > 0 ? $item['package_remove_qty'] : '' ?>
                                    </td>
                                    <td class="text-right"><b style="font-size:15px;"><?= $item['goods_qty'] ?></b></td>
                                    <td class="text-right"><?= $item['product_data']['ps_stock'] ?></td>
                                    <td class="text-right"><?= $item['after_goods_qty'] ?></td>
                                    <td class="text-right">
                                        <p><?= $item['stockFl'] == 'y' ? '' : '재고관리안함' ?></p>
                                        <?= $item['totalStock'] ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin(<?= $item['goodsNo'] ?>);">관리자 상품보기</button>
                                    </td>

                                </tr>
                                <tr class="order_num_list">
                                <td colspan="100%" >
                                    <table class="table-st1">
                                        <thead>
                                            <tr>
                                                <th>주문번호</th>
                                                <th>주문일시</th>
                                                <th>결제일시</th>
                                                <th>주문수량</th>
                                                <th>패키지 제거</th>
                                                <th>C/S 요청</th>
                                                <th>회원정보</th>
                                                <th>회원핸드폰</th>
                                                <th>주문메모</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($item['order_info'] as $order) { ?>
                                                <tr>
                                                    <td>
                                                        <a href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?= $order['orderNo'] ?>" target="_blank"><?= $order['orderNo'] ?></a>
                                                    </td>
                                                    <td class="text-center"><?= $order['orderDate'] ?></td>
                                                    <td class="text-center"><?= $order['paymentDt'] ?></td>
                                                    <td class="text-center"><?= $order['goods_qty'] ?></td>
                                                    <td class="text-right">
                                                        <?= $order['package_remove_qty'] > 0 ? $order['package_remove_qty'] : '' ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btnstyle1 btnstyle1-xs"
                                                            data-order-no="<?= $order['orderNo'] ?>"
                                                            data-order-date="<?= $order['orderDate'] ?>"
                                                            data-payment-dt="<?= $order['paymentDt'] ?>"
                                                            data-mem-no="<?= $order['member']['memNo'] ?? '' ?>"
                                                            data-mem-id="<?= $order['member']['memId'] ?? '' ?>"
                                                            data-mem-nm="<?= $order['member']['memNm'] ?? '' ?>"
                                                            data-mem-phone="<?= $order['member']['cellPhone'] ?? '' ?>"
                                                            data-group-nm="<?= $order['member']['groupNm'] ?? '' ?>"
                                                            data-receiver-name="<?= $order['receiverName'] ?? '' ?>"
                                                            data-receiver-phone="<?= $order['receiverCellPhone'] ?? '' ?>"
                                                            onclick="csCreate(this);">C/S 요청</button>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if( !empty($order['member']) ){ ?>
                                                            <a href="javascript:godoMemberCrm('<?= $order['member']['memNo'] ?>');">
                                                                <?= $order['member']['memNm'] ?> /
                                                                <?= $order['member']['memId'] ?> /
                                                                <?= $order['member']['groupNm'] ?>
                                                            </a>
                                                        <?php } else { ?>
                                                            <span class="text-red">비회원</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if( !empty($order['member']) ){ ?>
                                                            <button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="godoMemberSms('<?= $order['member']['memNo'] ?>');"><?= $order['member']['cellPhone'] ?></button>
                                                        <?php } else { ?>
                                                            <span class="text-red">비회원</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= $order['orderMemo'] ?>
                                                    </td>
                      
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>
            </ul>
            <ul>
                <div class="scroll-wrap">
                </div>
            </ul>
        </div>

    </div>
</div>

<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script>

	/**
     * C/S 요청
     * @param string $orderNo 주문번호
     * @param string $memNo 회원번호
     * @param string $memId 회원ID
     * @param string $memName 회원명
     * @param string $memPhone 회원전화
     * @param string $receiverName 수령자명
     * @param string $receiverPhone 수령자전화
     */
    function csCreate(button){

        var orderNo = $(button).data('order-no');
        var orderDate = $(button).data('order-date');
        var paymentDt = $(button).data('payment-dt');
        var memNo = $(button).data('mem-no');
        var memId = $(button).data('mem-id');
        var memName = $(button).data('mem-nm');
        var memPhone = $(button).data('mem-phone');
        var groupNm = $(button).data('group-nm');
        var receiverName = $(button).data('receiver-name');
        var receiverPhone = $(button).data('receiver-phone');

        var data = {
            mode: 'create',
            apiMode: 'none',
            category: '고도몰주문상품별',
            orderNo: orderNo,
            orderDate: orderDate,
            paymentDt: paymentDt,
            memNo: memNo,
            memId: memId,
            memName: memName,
            memPhone: memPhone,
            groupNm: groupNm,
            receiverName: receiverName,
            receiverPhone: receiverPhone
        };
        openDialog("/admin/cs/cs_create", data, "C/S 생성", "800px");

    }

	// 검색 파라미터 수집 공통 함수
	function getSearchParams(additionalParams) {
		var params = {};

		// 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
		var fields = {
			'mode': $("#mode").val(),
			'start_date': $("#start_date").val(),
			'end_date': $("#end_date").val(),
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
		location.href = '/admin/order/godo_order' + (queryString ? '?' + queryString : '');
	}

    $(function() {

        if ($(".calendar-input input").length) {
            $(".calendar-input input").datepicker(clareCalendar);
        }

        $(document)
            .off('click.stockExcelOrderToggle', '.order-toggle')
            .on('click.stockExcelOrderToggle', '.order-toggle', function() {
            var $targetRow = $(this).closest('tr').next('.order_num_list');
            if ($targetRow.length === 0) {
                return;
            }
            $targetRow.toggle();
            $(this).text($targetRow.is(':visible') ? '▲' : '▼');
        });

		$("#searchBtn").on('click', function() {
			// 검색 파라미터 수집
			var params = getSearchParams();

			// 페이지 이동
			navigateWithParams(params);
		});

    });
</script>