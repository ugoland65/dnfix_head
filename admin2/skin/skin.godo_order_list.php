<?php
/*
=================================================================================
뷰 파일: admin2/skin/skin.godo_order_list.php
호출경로 : /ad/showdang/godo_order_list
설명: 고도몰 주문서 조회 화면
작성자: Lion65
수정일: 2025-04-07
=================================================================================

GET
@getParam {int} $_prd_idx - 상품 시퀀스

CONTROLLER
/application/Controllers/Admin/OrderController.php

*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\OrderController;

$orderController = new OrderController();

$viewData = $orderController->godoOrderList();

$scmMapping = [
    0  => ['name' => '오류', 'partner_key' => null, 'display'=>'none' ],
    1  => ['name' => '주식회사 디엔픽스', 'partner_key' => null, 'display'=>'none' ],
    2  => ['name' => '모브X', 'partner_key' => null, 'display'=>'none' ],
    3  => ['name' => '모브', 'partner_key' => 3],
    4  => ['name' => '공급사사입', 'partner_key' => null, 'display'=>'none'],
    5  => ['name' => '바니컴퍼니', 'partner_key' => 8],
    6  => ['name' => '바이담', 'partner_key' => 10],
    7  => ['name' => '해외직구', 'partner_key' => null, 'display'=>'none'],
    8  => ['name' => '그린쉘프', 'partner_key' => 12],
    9  => ['name' => '울컨코리아', 'partner_key' => 7],
    10 => ['name' => '모노프로', 'partner_key' => 11],
    11 => ['name' => '핑크에그', 'partner_key' => 9],
    12 => ['name' => '리퍼브', 'partner_key' => null, 'display'=>'none'],
    13 => ['name' => 'MSHb2b', 'partner_key' => 5],
    14 => ['name' => 'JPDOLL', 'partner_key' => 14],
    15 => ['name' => '도라토이', 'partner_key' => 6],
    16 => ['name' => '대형', 'partner_key' => null, 'display'=>'none'],
    17 => ['name' => '리퍼브', 'partner_key' => null, 'display'=>'none'],
    18 => ['name' => '랜덤박스', 'partner_key' => null, 'display'=>'none'],
    19 => ['name' => '예비1', 'partner_key' => null, 'display'=>'none'],
    20 => ['name' => '예비2', 'partner_key' => null, 'display'=>'none'],
];

?>
<style>
.layout-style1{
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	gap: 10px;
	box-sizing: border-box;
	> ul{

		width: 33%;
		height: 100%;
		padding: 0;
		.layout-style1-section-top{
			height:30px;
		}
		.layout-style1-section{
			height: calc(100% - 30px);
			border: 1px solid #ccc;
			background-color: #fff;
		}

	}
	> ul:first-child{
		flex: 1;
	}

}
</style>

<div id="contents_head">
	<h1>고도몰 주문 가져오기</h1>
    <div class="m-l-10">
		<label class="calendar-input">
			<input type='text' name='start_date'  id="start_date" value="<?=$viewData['start_date'] ?? date('Y-m-d')?>" >
		</label>
		~
		<label class="calendar-input">
			<input type='text' name='end_date'  id="end_date" value="<?=$viewData['end_date'] ?? date('Y-m-d')?>" >
		</label>
		<button type="button" id="search_btn" class="btnstyle1 btnstyle1-success">조회</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
	
		<div class="layout-style1">
			<ul>
				<div class="layout-style1-section-top">
					<h3>자사 보유상품</h3>
				</div>
				<div class="layout-style1-section">
					<div id="" class="table-wrap5">
						<div class=" scroll-wrap">
						<table class="table-st1">
							<thead>
								<tr>
									<th class="list-idx">No</th>
									<th class="">주문번호</th>
									<th class="">주문일</th>
									<th class="">결제일</th>
									<th class="">결제방법</th>
									<th class="">상품</th>
									<th class="">총상품금액</th>
									<th class="">결제금액</th>
									<th class="">환불금액</th>
									<th class="">정산금액</th>
									<th class="">총원가</th>
									<th class="">총수익</th>
									<th class="">수익률</th>
									<th class="">대상상품</th>
									<th class="">보유상품</th>
									<th class="">미보유</th>
									<th class="">원가상품</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$no = 0;

									$totalGoodsPrice = 0;
									$totalSalePrice = 0;
									$totalRefundPrice = 0;
									$totalFinalPrice = 0;
									$totalCost = 0;
									$totalMargin = 0;
									$totalMarginRate = 0;

									foreach ($viewData['orderMargin'] as $order) {
										$no++;
										$totalGoodsPrice += $order['totalGoodsPrice'];
										$totalSalePrice += $order['salePrice'];
										$totalRefundPrice += $order['refundPrice'];
										$totalFinalPrice += $order['finalPrice'];
										$totalCost += $order['totalCost'];
										$totalMargin += $order['marginAmount'];
										$totalMarginRate += $order['marginRate'];

										if( $order['goodsCnt'] != $order['costGoodsCount'] ){
											$trClass = "notice";
										}else{
											$trClass = "";
										}
								?>
								<tr class="<?=$trClass?>">	
									<td><?=$no?></td>
									<td>
										<a href="http://gdadmin.dnfix202439.godomall.com/order/order_view.php?orderNo=<?=$order['orderNo']?>" target="_blank"><?=$order['orderNo']?></a>
									</td>
									<td>
										<?=date('Y-m-d', strtotime($order['orderDate']))?>
									</td>
									<td>
										<?=date('Y-m-d', strtotime($order['paymentDt']))?>
									</td>
									<td>
										<?=$order['settleKind']?>
									</td>
									<!-- 상품개수 -->
									<td class="text-center">
										<?=$order['orderGoodsCnt']?>
									</td>
									<!-- 총상품금액 -->
									<td class="text-right">
										<?=number_format($order['totalGoodsPrice'])?>
									</td>
									<!-- 결제금액 -->
									<td class="text-right">
										<?=number_format($order['salePrice'])?>
									</td>
									<!-- 환불금액 -->
									<td class="text-right">
										<?php if( $order['refundPrice'] > 0 ){ ?>
											<?=number_format($order['refundPrice'])?>
										<?php } ?>
									</td>
									<!-- 정산금액 -->
									<td class="text-right">
										<?=number_format($order['finalPrice'])?>
									</td>
									<!-- 총원가 -->
									<td class="text-right">
										<?=number_format($order['totalCost'])?>
									</td>
									<!-- 총수익 -->
									<td class="text-right">
										<?=number_format($order['marginAmount'])?>
									</td>
									<!-- 수익률 -->
									<td class="text-right">
										<?=$order['marginRate']?>%
									</td>
									<!-- 대상상품 -->
									<td class="text-center">
										<?=$order['goodsCnt']?>
									</td>
									<!-- 보유상품 -->
									<td class="text-center">
										<?=$order['scm_my']?>
									</td>
									<!-- 미보유상품 -->
									<td class="text-center">
										<?php if( $order['scm_not_my'] > 0 ){ ?>
											<?=$order['scm_not_my']?>
										<?php } ?>
									</td>
									<!-- 원가상품 -->
									<td class="text-center">
										<?=$order['costGoodsCount']?>
									</td>
								</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="6">
										Total
									</th>
									<th class="text-right">
										<?=number_format($totalGoodsPrice)?>
									</th>
									<th class="text-right">
										<?=number_format($totalSalePrice)?>
									</th>
									<!-- 환불금액 합계-->
									<th class="text-right">
										<?=number_format($totalRefundPrice)?>
									</th>
									<!-- 정산금액 합계-->
									<th class="text-right">
										<?=number_format($totalFinalPrice)?>
									</th>
									<th class="text-right">
										<?=number_format($totalCost)?>
									</th>
									<th class="text-right">
										<?=number_format($totalMargin)?>
									</th>
									<?php
										$avgMarginRate = $totalMargin / $totalSalePrice * 100;
									?>
									<th class="text-center">
										<?=number_format($avgMarginRate)?> %
									</th>
									<th class="text-center" colspan="100%">
										
									</th>
			
								</tr>
							</tfoot>
							</table>

						</div>
					</div>
				</div>
			</ul>
			<ul>
				<div class="layout-style1-section-top">
					<h3>에러 항목</h3>
				</div>
				<div class="layout-style1-section">
					<div id="" class="table-wrap5">
						<div class=" scroll-wrap">
							<table class="table-st1">
								<thead>
								<tr>
									<th class="list-idx">No</th>
									<th class="">에러내용</th>
									<th class="">에러상세</th>
								</tr>
								</thead>
								<tbody>
									<?php
										$errorCodeMapping = [
											'scm_code_not_match' => '상품 코드 공급사 상품 매칭 실패',
											'cost_price_not_found' => '원가 정보 없음',
											'product_data_not_found' => '상품 데이터 없음',
										];
										$i = 0;
										foreach ($viewData['errorList'] as $error) {
											$i++;

											$errorText = "";
											$errorDetail = "";

											if( empty($error['error_code']) ){
												$errorText = "( ".$error['code']." )"." 에러코드 없음";
												$errorDetail = "주문번호 : ".$error['orderNo']."
													<br>상품명 : ".($error['orderGoods']['goodsNm'] ?? "상품명 없음")."
													<br>상품코드 : ".($error['orderGoods']['goodsCd'] ?? "상품코드 없음")."
													<br>공급사번호 : ".($error['orderGoods']['scmNo'] ?? "공급사번호 없음")."
													<br>공급사이름 : ".($scmMapping[$error['orderGoods']['scmNo']]['name'] ?? "공급사이름 없음");	
				
											
											}elseif( $error['error_code'] == 'scm_code_not_match' ){
												$errorText = "( ".$error['code']." )"."<br>".$errorCodeMapping[$error['error_code']];
												$errorDetail = "주문번호 : ".$error['orderNo']."
													<br>상품명 : ".$error['orderGoods']['goodsNm']."
													<br>상품코드 : ".$error['orderGoods']['goodsCd']."
													<br>공급사번호 : ".$error['orderGoods']['scmNo']."
													<br>공급사이름 : ".$scmMapping[$error['orderGoods']['scmNo']]['name'];
											
											}elseif( $error['error_code'] == 'cost_price_not_found' ){
												
												$errorText = "( ".$error['code']." )"."<br>".$errorCodeMapping[$error['error_code']];
												
												$goodsNm = $error['dbGoods']['goods_name'] ?? "상품명 없음";
												$goodsCd = $error['code'] ?? "상품코드 없음";
												$scmNo = $error['scmNo'] ?? null;

												$errorDetail = "주문번호 : ".$error['orderNo']."
													<br>상품명 : <b>".$goodsNm."</b>	
													<br>재고코드 : ".$goodsCd;

												if( !empty($scmNo) ){
													$errorDetail .= "<br>공급사번호 : ".$scmNo."<br>공급사이름 : <b>".$scmMapping[$scmNo]['name']."</b>";
												}

											}
									?>
									<tr>	
										<td><?=$i?></td>
										<td>

											<?php
											/*
											<div>
												<?=$error['error_code']?>
											</div>
											*/
											?>
											
											<div>
												<?=$errorText?>
											</div>

											<?php
												if( $error['error_code'] == 'cost_price_not_found' ){
											?>
											<div>
												<?php
													if( $error['mode'] == 'stock' ){
												?>
													<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
													onclick="onlyAD.prdView('<?=$error['idx']?>','price');" >상품정보</button>
												<?php }else{ ?>
													<button type="button" id="" class="btnstyle1 btnstyle1-warning btnstyle1-xs" 
													onclick="onlyAD.prdProviderQuick('<?=$error['idx']?>','price');" >공급사상품</button>
												<?php } ?>

											</div>
											<?php } ?>

										</td>
										<td><?=$errorDetail?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>

							<?php
								/*
								echo "<pre>";
								print_r($viewData['errorList']);
								echo "</pre>";
								*/
							?>
						</div>

					</div>
				</div>
			</ul>
		</div>	
		
		<div>errorList</div>
			<?php
				echo "<pre>";
				print_r($viewData['errorList']);
				echo "</pre>";
			?>
		<?php
		/*
		<div class="">
			
		<div>setGoodsList</div>
			<?php
				echo "<pre>";
				print_r($viewData['setGoodsList']);
				echo "</pre>";
			?>

			<div>test</div>
			<?php
				echo "<pre>";
				print_r($viewData['test']);
				echo "</pre>";
			?>

			<div>orderData</div>
			<?php
				echo "<pre>";
				print_r($viewData['orderData']);
				echo "</pre>";
			?>

<div>orderMargin</div>
			<?php
				echo "<pre>";
				print_r($viewData['orderMargin']);
				echo "</pre>";
			?>
		</div>
		*/
		?>
		<div id="contents_body_bottom_padding"></div>
	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script type="text/javascript"> 
<!-- 

	$('#search_btn').on('click', function(){
		location.href = "/ad/order/godo_order_list?mode=ds&start_date="+$('#start_date').val()+"&end_date="+$('#end_date').val();
	});
//--> 
</script>		