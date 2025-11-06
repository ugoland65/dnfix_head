<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\GodoApiController;

$godoApiController = new GodoApiController();
$data = $godoApiController->godoOrderPrintIndex();
	
/*
echo "<pre>";
print_r($data);
echo "</pre>";
*/

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>주문서 출력</title>

	<script src="/plugins/jquery/jquery-3.6.0.min.js"></script>

	<!-- jqueryui -->
	<link rel="stylesheet" href="/plugins/jquery-ui-1.13.2/jquery-ui.min.css">
	<script src="/plugins/jquery-ui-1.13.2/jquery-ui.min.js"></script>

	<script src="/assets/js/common.ad.js?ver=<?=$wepix_now_time?>"></script>
	<script src="/admin2/js/common.js?ver=<?=$wepix_now_time?>"></script>

	<script src="/assets/js/global.js?t=<?=time()?>"></script>

	<link href="/admin2/css/common.css?ver=<?=$wepix_now_time?>" rel="stylesheet" >
	<link href="/admin2/css/layout.css?ver=<?=$wepix_now_time?>" rel="stylesheet" >
	<link href="/admin2/css/page.css?ver=<?=$wepix_now_time?>" rel="stylesheet" >

    <link href="/admin2/css/v2-style.css?t=<?=time()?>" rel="stylesheet" >
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

            /* 버튼 숨기기 (프린트 시) */
            .print-button {
                display: none;
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
                                &.goods-image{
                                    width: 20mm;
                                    height: 20mm;
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
            }
            
        }

        .order-total-price{
            width: 100%;
            margin-top: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            text-align: right;
        }

        .order-receiver-info{
            margin-top: 10px;
            h2{
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 10px;
            }
        }

		
        .print-button {
            margin: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="order-print-header">
        <!-- 프린트 버튼 -->
        <button class="print-button" onclick="window.print();">주문서 인쇄</button>
        <div class="calendar-input">
            <input type="text" name="start_date" value="<?=$data['start_date']?>" placeholder="시작일">
        </div>
        <div class="calendar-input">
            <input type="text" name="end_date" value="<?=$data['end_date']?>" placeholder="종료일">
        </div>
        <button class="search-button" onclick="searchOrders()">주문 검색</button>
    </div> 
    <?
/*
		echo "<pre>";
		print_r($data);
		echo "</pre>";
*/
	?>	

    <!-- 주문서 컨테이너 (수량 1개당 A4 한 장) -->
    <?php 
    foreach ($data['orderData']['data'] as $index => $order) { 
        $index_count = $index + 1;
    ?>
    <div class="order-container">
        <div class="order-header">
            <h1>주문번호 : <?=$order['orderNo']?></h1>
            <h1>주문일자 : <?=$order['regDt']?> ( <b style="color:#ff0000"><?=$index_count?></b> / <?=$data['orderData']['total']?> )</h1>
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
                                <img src="<?=$goods['thumbImageUrl']?>" alt="">
                            </td>
                            <td>
                                <div>
                                    <ul class="goods-nm">
                                        <b><?=$goods['goodsNm']?></b>
                                    </ul>

                                    <?php if(!empty($goods['bar_code'])) { ?>
                                    <ul class="goods-barcode">
                                        <b><?=$goods['bar_code'] ?? '없음'?></b>
                                    </ul>
                                    <?php } ?>

                                    <ul class="goods-code">
                                        재고 : <b><?=$goods['stock'] ?? 0?></b>
                                        <?php if(!empty($goods['rack_code'])) { ?>
                                            | 랙코드 : <b><?=$goods['rack_code']?></b>
                                        <?php } ?>
                                    </ul>
                                    
                                    <? if(!empty($goods['optionInfo'])) { ?>
                                    <ul class="goods-optionInfo">
                                        <? foreach($goods['optionInfo'] as $option) { ?>
                                            <p><?=$option[0]?> : <b><?=$option[1]?></b></p>
                                        <? } ?>
                                    </ul>
                                    <? } ?>
                                </div>
                            </td>
                            <td class="goods-cnt">
                                <b><?=$goods['goodsCnt']?></b>
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
            if(!empty($order['addField'])) { 
                if( count($order['addField']) > 0 ) {
            ?>
            <div class="order-add-field" >
                <?php foreach ($order['addField'] as $addField) { ?>
                
                <?php if($addField['name'] == '패키지 제거 여부') { ?>
                    <?php if($addField['data'] == '패키지 제거') { ?>
                        <ul class="package-remove">
                            <h3>패키지 제거</h3>
                        </ul>
                    <?php } ?>
                <?php } else { ?>
                <ul>
                    <?=$addField['name']?> : <b><?=$addField['data']?></b>
                </ul> 
                <?php } ?>
					
                <?php } ?>
            </div>
            <?php } } ?>

            <div class="order-receiver-info">
                <h2>수령자 정보</h2>
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
                            <?=$order['receiverAddress'] ?? '-'?>
                            <?=$order['receiverAddressSub'] ?? '-'?>
                        </td>
                    </tr>
                    <tr>
                        <th>배송 메시지</th>
                        <td colspan="3"> <?=$order['orderMemo'] ?? '-'?></td>
                    </tr>
                </table>
            </div>

     </div>
    <?php } ?>

    <script>
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
    </script>
</body>
</html>

