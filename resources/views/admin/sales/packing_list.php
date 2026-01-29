<style>
    @media print {
        body {
            margin: 0;
            padding: 0;
            size: A4; /* A4 크기 설정 */
        }

        .order-container {
            width: 210mm;  /* A4 용지 가로 크기 */
            height: 297mm; /* A4 용지 세로 크기 */
            padding: 8mm;
            box-sizing: border-box;
            page-break-after: always; /* 다음 페이지로 이동 */
            break-after: page; /* 최신 브라우저 지원 */
            display: flex;
            flex-direction: column;
            align-items: center;
            border: none;
            box-shadow:none !important;
        }

        /* 마지막 주문서에는 페이지 나누기를 하지 않음 */
        .order-container:last-child {
            page-break-after: auto;
        }

        /* 버튼 및 헤더 숨기기 (프린트 시) */
        .print-button,
        .order-print-header {
            display: none !important;
        }

        /* 프린트 시에도 빨간색 유지 */
        span[style*="color:#ff0000"],
        span[style*="color: #ff0000"],
        b[style*="color:#ff0000"],
        b[style*="color: #ff0000"] {
            color: #ff0000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* 프린트 시에도 파란색 유지 */
        span[style*="color:#0000ff"],
        span[style*="color: #0000ff"],
        b[style*="color:#0000ff"],
        b[style*="color: #0000ff"] {
            color: #0000ff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* 프린트 시에도 보라색 유지 */
        b[style*="color:rgb(94, 31, 240)"],
        b[style*="color: rgb(94, 31, 240)"] {
            color: rgb(94, 31, 240) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* 프린트 시에도 사은품 박스 배경색 유지 */
        .gift-box-100000,
        .gift-box-10000 {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .gift-box-100000 b {
            color: rgb(94, 31, 240) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }

    /* 일반 화면에서 스타일 */
    .order-container {
        width: 210mm;
        height: 297mm;
        padding: 8mm;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        margin-bottom: 10px;
    }
    
    .order-header {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        > h1{
            margin:0;
            padding:0;
            font-size: 16px;
        }
    }
    .order-goods-list {
        width: 100%;
        margin-top: 20px;
        > div{
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
            > ul{
                list-style: none;
                padding:0;
                margin:0;
                display: flex;
                flex-direction: column;
                table{
                    width: 100%;
                    height: 100%;
                    border-collapse: collapse;
                    border: 1px solid #000;
                    tr{
                        td{
                            text-align: left;
                            border: 1px solid #000;
                            padding:0;
                            &.goods-image{
                                width: 22mm;
                                height: 22mm;
                                img{
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                }
                            }
                            &.goods-cnt{
                                width: 10mm;
                                text-align: center;
                            }
                            div{
                                ul{
                                    list-style: none;
                                    padding:0;
                                    margin:0;
                                }
                            }
                        }
                    }
                }

            }
        }
    }
    
    .order-table {
        width: 100%;
        border-collapse: collapse;
    }
    .order-table th, td {
        border: 1px solid black;
        padding: 10px;
        text-align: left;
    }
    .order-table th {
        background-color: #f2f2f2;
    }
    
    .order-add-field{
        width: 100%;
        padding:0;
        margin-top: 10px;
        ul{
            padding:0;
            margin:0;
            box-sizing: border-box;

            /*
            &.package-remove{
                width: 100%;
                margin-top: 10px;
                padding: 10px;
                border: 1px solid #000;
                text-align: center; 
                h3{
                    font-size: 20px;
                    color: #ff0000;
                    font-weight: bold;
                }
            }
            */
        }
        
    }

    .order-total-price{
        width: 100%;
        margin-top: 10px;
        border-bottom: 1px solid #000;
        padding-bottom: 10px;
        text-align: right;
    }

    .order-gift-info{
        width: 100%;
        border-bottom: 1px solid #000;
        padding-bottom: 10px;
        text-align:center;

        display:flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        h2{
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .package-remove{
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            background-color:#eee;
            border: 1px solid #000;
            font-size: 16px;
            font-weight: bold;
            color:#ff0000;
        }
        .gift-box{
            padding: 10px 13px;
            border-radius: 5px;
            display: inline-block;
        }
        .gift-box-100000{
            background-color:#eee;
            border: 1px solid #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            b{
                color:rgb(94, 31, 240);
            }
        }
        .gift-box-10000{
            background-color:#eee;
            border: 1px solid #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            b{
                color:rgb(0, 111, 69);
            }
        }
    }

    .order-receiver-info{
        width: 100%;
        margin-top: 10px;

        .name-point{

            display: flex;
            justify-content: space-between;
            align-items: center;
            h2{
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 10px;
            }

            span{
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 10px;
                color:#0000ff;
            }
        }
    }

    .delivery-note {
        width: 100%;
        margin-top: 10px;
        border-bottom: 1px solid #000;
        padding-bottom: 10px;
        text-align: center;
    }
    
    .print-button {
        margin: 20px;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
    }
</style>
<div class="order-print-header">
    <!-- 프린트 버튼 -->
    <button class="print-button" onclick="window.print();">주문서 인쇄</button>
    <div class="calendar-input">
        <input type="text" name="start_date" value="<?=$start_date?>" placeholder="시작일">
    </div>
    <div class="calendar-input">
        <input type="text" name="end_date" value="<?=$end_date?>" placeholder="종료일">
    </div>
    <button class="search-button" onclick="searchOrders()">주문 검색</button>
</div>

    <!-- 주문서 컨테이너 (수량 1개당 A4 한 장) -->
    <?php 
    foreach ($packingList['data'] as $index => $order) { 
        $index_count = $index + 1;
    ?>
    <div class="order-container">
        <input type="hidden" name="order_date" id="order_date_<?=$order['orderNo']?>" value="<?=$order['regDt']?>">
        <input type="hidden" name="mem_no" id="mem_no_<?=$order['orderNo']?>" value="<?=$order['member']['memNo']?>">
        <input type="hidden" name="mem_id" id="mem_id_<?=$order['orderNo']?>" value="<?=$order['member']['memId']?>">
        <input type="hidden" name="group_nm" id="group_nm_<?=$order['orderNo']?>" value="<?=$order['member']['groupNm']?>">

        <div class="order-header">
            <h1>주문번호 : <?=$order['orderNo']?></h1>
            <h1>주문일자 : <?=$order['regDt']?> ( <b style="color:#ff0000"><?=$index_count?></b> / <?=$packingList['total']?> )</h1>
        </div>
        <div class="order-goods-list">

            <div>
                <?php 
                $count = 0;
                foreach ($order['orderGoods'] as $goods) { 
                    $count++;
                ?>
                <ul>
                    <table style="width: 100%;">
                        <tr>
                            <td class="goods-image" >
                                <img src="<?=$goods['thumbImageUrl']?>" alt="" referrerpolicy="no-referrer">
                                <?php if(!empty($goods['package_volume_level'])) { ?>
                                    <div class="text-center m-t-5 p-b-5" style="color:#999; font-size:11px;">volume : <b  style="color:#0000ff; font-size:14px;"><?=$goods['package_volume_level']?></b></div>
                                <?php } ?>
                            </td>
                            <td class="p-l-10">
                                <div>
                                    <ul class="goods-nm">
                                        <?=$goods['goodsNm']?>
                                    </ul>

                                    <?php if(!empty($goods['bar_code'])) { 
                                        $_bar_code_normal = substr($goods['bar_code'], 0, -5);
                                        $_bar_code_point = substr($goods['bar_code'], -5);
                                    ?>
                                    <ul class="goods-barcode m-t-5">
                                        <b><?=$_bar_code_normal?> <span style="color:#0000ff;"><?=$_bar_code_point?></span></b>
                                    </ul>
                                    <?php } ?>

                                    <ul class="goods-code m-t-5">
                                        
                                        <?php
                                        /*
                                        if($goods['stock'] > 0) { ?>
                                        재고 : <b><?=$goods['stock'] ?? 0?></b>  | 
                                        <?php } 
                                        */
                                        ?>

                                        <?php if(!empty($goods['rack_code'])) { ?>
                                           랙코드 : <b><?=$goods['rack_code']?></b>
                                        <?php } ?>
                                    </ul>

                                    <?php
                                    /*
                                    php if(!empty($goods['package_volume_level'])) { ?>
                                        <ul class="m-t-5">volume : <b style="color:#0000ff;"><?=$goods['package_volume_level']?></b></ul>
                                    <?php } 
                                    */ 
                                    ?>
                                    
                                    <? if(!empty($goods['optionInfo'])) { ?>
                                    <ul class="goods-optionInfo">
                                        <? foreach($goods['optionInfo'] as $option) { ?>
                                            <p><?=$option[0]?> : <b style="font-size:15px; color:#ff0000;"><?=$option[1]?></b></p>
                                        <? } ?>
                                    </ul>
                                    <? } ?>
                                </div>
                            </td>
                            <td class="goods-cnt">
                                <?php if($goods['goodsCnt'] > 1) { ?>
                                    <span style="color:#ff0000;"><b style="font-size:20px; font-weight:900;"><?=$goods['goodsCnt']?></b>개</span>
                                <?php } else { ?>
                                    <?=$goods['goodsCnt']?>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                </ul>
                <?php } ?>
            </div>

        </div>

        <div class="order-total-price">

            주문금액 : <b><?=number_format($order['totalGoodsPrice'])?>원</b>
           
            <?php if( $order['totalDeliveryCharge'] > 0 ){ ?>
            +
            배송비 : <b><?=number_format($order['totalDeliveryCharge'])?>원</b>
            <?php } ?>

            <?php if( $order['useMileage'] > 0 ){ ?>
            - 사용적립금 : <b><?=number_format($order['useMileage'])?>원</b>
            <?php } ?>

            <?php if( $order['dc_info']['totalDcPrice'] > 0 ){ ?>
            - 할인금액 : <b><?=number_format($order['dc_info']['totalDcPrice'])?>원</b>
            (
                <?php foreach($order['dc_info']['list'] as $dc) { ?>
                    <?php if( $dc['column'] != "totalDcPrice" ){ ?>
                        - <?=$dc['name']?> : <b><?=number_format($dc['price'])?>원</b>
                    <?php } ?>
                <?php } ?>
            )
            <?php } ?>
            
            =
            결제금액 : <b><?=number_format($order['settlePrice'])?>원</b> 

        </div>

        <?php
        /*
        echo "<pre>";
        print_r($order['dc_info']);
        echo "</pre>";
        */
        ?>

        <?php 
        $is_package_remove = false;
        if(!empty($order['addField'])) { 
            if( count($order['addField']) > 0 ) {
        ?>
        <div class="order-add-field" >
            <?php 
                foreach ($order['addField'] as $addField) { 
                    if($addField['name'] == '패키지 제거 여부') {
                        if($addField['data'] == '패키지 제거') {
                            $is_package_remove = true;
                        }
                    } else {
            ?>
            <ul>
                <?=$addField['name']?> : <b><?=$addField['data']?></b>
            </ul> 
            <?php } ?>
                
            <?php } ?>
        </div>
        <?php } } ?>

        <div class="order-gift-info m-t-10">

            <?php if($is_package_remove) { ?>
                <p class="package-remove">패키지 제거</p>
            <?php } ?>

            <?php if($order['settlePrice'] >= 100000) { ?>
                <p class="gift-box gift-box-100000">사은품 : <b style="font-size:16px;">10만원 사은품</b></p>
            <?php } elseif($order['settlePrice'] >= 10000) { ?>
                <p class="gift-box gift-box-10000">사은품 : <b style="font-size:16px;">만원 사은품</b></p>
            <?php } ?>
        </div>

        <div class="order-receiver-info">
            
            <div class="name-point">
                <?php
                // 이름 마스킹 처리 (가운데 글자를 * 처리)
                $name = $order['receiverName'];
                $name_length = mb_strlen($name, 'UTF-8');
                if ($name_length >= 2) {
                    if ($name_length == 2) {
                        // 2글자면 첫글자*
                        $masked_name = mb_substr($name, 0, 1, 'UTF-8') . '*';
                    } else {
                        // 3글자 이상이면 가운데 글자들을 * 처리
                        $first = mb_substr($name, 0, 1, 'UTF-8');
                        $last = mb_substr($name, -1, 1, 'UTF-8');
                        $middle_count = $name_length - 2;
                        $masked_name = $first . str_repeat('*', $middle_count) . $last;
                    }
                } else {
                    $masked_name = $name;
                }
                
                // 휴대전화 마스킹 처리 (뒷 4자리를 **** 처리)
                $phone = $order['receiverCellPhone'] ?? '-';
                if ($phone !== '-' && strlen($phone) >= 4) {
                    $masked_phone = substr($phone, 0, -4) . '****';
                } else {
                    $masked_phone = $phone;
                }
                ?>

                <h2>수령자 정보</h2>
                <span><?=$masked_name?> / <?=$masked_phone?></span>

            </div>

            <table class="order-table">
                <tr>
                    <th>수령자명</th>
                    <td><?=$order['receiverName']?></td>
                    <th>회원</th>
                    <td>
                        <?php if(!empty($order['member']['memId'])) { ?>
                            <?=$order['member']['groupNm']?>
                        <?php } else { ?>
                            비회원
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>일반전화</th>
                    <td><?=$order['receiverPhone'] ?? '-'?></td>
                    <th>휴대전화</th>
                    <td><?=$order['receiverCellPhone'] ?? '-'?></td>
                </tr>
                <tr>
                    <th>배송지 주소</th>
                    <td colspan="3">
                        (<?=$order['receiverZonecode'] ?? '-'?>) 
                        <b style="font-size:16px;"><?=$order['receiverAddress'] ?? '-'?></b>
                        <?=$order['receiverAddressSub'] ?? '-'?>
                    </td>
                </tr>
                <tr>
                    <th>배송 메시지</th>
                    <td colspan="3"> <?=$order['orderMemo'] ?? '-'?></td>
                </tr>
            </table>
        </div>

        <div class="delivery-note m-t-20" style="display:flex; gap:12px;">
            <div style="flex:1;">
                <div style="margin-bottom:6px; font-size:16px;">배송 특이사항 (인쇄 전 메모를 입력하세요)</div>
                <textarea class="delivery-note-text" rows="3" style="width:100%; height:100px; box-sizing:border-box; resize:vertical; font-size:15px;" placeholder="예: 경비실 보관, 파손주의 등"></textarea>
                <div class="delivery-note-preview" style="margin-top:6px; min-height:32px; white-space:pre-wrap; border:1px solid #eee; padding:8px; background:#fafafa; font-size:15px;"></div>
            </div>
            <div style="flex:1;">
                <div style="margin-bottom:6px; font-size:16px;">
                    C/S 처리 요청
                    <button class="cs-note-button" onclick="csRequest('<?=$order['orderNo']?>')">C/S 처리 요청</button>
                </div>
                <textarea 
                    id="cs_body_<?=$order['orderNo']?>"
                    class="cs-note-text" 
                    rows="3" 
                    style="width:100%; height:100px; box-sizing:border-box; resize:vertical; font-size:15px;" 
                    placeholder="예: 고객 요청사항, 환불/교환 메모 등">사유:
내용:
처리방안:</textarea>
                <div class="cs-note-preview" style="margin-top:6px; min-height:32px; white-space:pre-wrap; border:1px solid #eee; padding:8px; background:#fafafa; font-size:15px;"></div>
            </div>
        </div>

    </div>
    <?php } ?>

<script>
    // 인쇄 시 입력창 숨김
    const printStyle = document.createElement('style');
    printStyle.innerHTML = `
        @media print {
            .delivery-note-text { display: none !important; }
        }
    `;
    document.head.appendChild(printStyle);

    // 배송 특이사항 / CS 요청 메모 미리보기
    (function(){
        const notes = document.querySelectorAll('.delivery-note');
        notes.forEach(note => {
            const pairs = [
                { input: '.delivery-note-text', preview: '.delivery-note-preview' },
                { input: '.cs-note-text', preview: '.cs-note-preview' },
            ];
            pairs.forEach(pair => {
                const textarea = note.querySelector(pair.input);
                const preview = note.querySelector(pair.preview);
                if (textarea && preview) {
                    textarea.addEventListener('input', function() {
                        preview.textContent = this.value;
                    });
                }
            });
        });
    })();

    function searchOrders() {
        const startDate = document.querySelector('input[name="start_date"]').value;
        const endDate = document.querySelector('input[name="end_date"]').value;
        
        // 현재 URL의 기본 경로 가져오기
        const currentUrl = window.location.pathname;
        
        // URL 파라미터 생성
        const params = new URLSearchParams();
        if (startDate) {
            params.append('start_date', startDate);
        }
        if (endDate) {
            params.append('end_date', endDate);
        }
        
        // 파라미터가 있으면 URL에 추가하여 페이지 이동
        const newUrl = params.toString() ? `${currentUrl}?${params.toString()}` : currentUrl;
        window.location.href = newUrl;
    }

    //c/s 처리 요청
    function csRequest(orderNo) {

        const orderDate = document.getElementById('order_date_' + orderNo).value;
        const memNo = document.getElementById('mem_no_' + orderNo).value;
        const memId = document.getElementById('mem_id_' + orderNo).value;
        const groupNm = document.getElementById('group_nm_' + orderNo).value;
        const csBody = document.getElementById('cs_body_' + orderNo).value;


        const data = {
            orderNo: orderNo,
            orderDate: orderDate,
            memNo: memNo,
            memId: memId,
            groupNm: groupNm,
            csBody: csBody,
        };

        ajaxRequest("/admin/cs/cs_request", data)
            .then(res => {
                if(res.success) {
                    alert('C/S 처리 요청 완료');
                } else {
                    alert(res.message);
                }
            })
            .catch(error => {
                console.error('AJAX 요청 실패:', error);
            });
    }
</script>