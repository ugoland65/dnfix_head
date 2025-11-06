<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\payment; 

$payment = new payment(); 

$result = $payment->paymentRegIndex();
?>
<div>
	<div>

	</div>
	<div>

		<form id="form_payment">

		<? if( !empty($_idx) ){ ?>
		<input type="hidden" name="idx" value="<?=$_idx?>" >
		<? } ?>

		<input type="hidden" name="mode" value="<?=$result['target']['mode']?>" >

		<? if( $result['target']['mode'] == "payment" ){ ?>
		<input type="hidden" name="target_idx" value="<?=$result['target']['target_idx'] ?? ""?>" >
		<? } ?>
			
		<table class="table-style border01 width-full">

			<? if( $result['target']['mode'] == "payment" ){ ?>
			<tr>
				<th style="width:100px;">종류</th>
				<td >
					<select name="target_idx">
					<?
					foreach ($result['target']['kind'] as $row ) {
					?>
						<option value="<?=$row['idx']?>" ><?=$row['name']?></option>
					<? } ?>
					</select>
				</td>
			</tr>
			<? }else{ ?>
			<tr>
				<th style="width:100px;">결제 타겟</th>
				<td >
					[<?=$result['target']['mode_text']?>] <?=$result['target']['name']?>
				</td>
			</tr>
			<? } ?>

			<tr>
				<th>결제 금액</th>
				<td >
					<input type='text' name='price' id='price' class="price price_point" value=""  >
				</td>
			</tr>
			<tr>
				<th>입금은행</th>
				<td >
					<input type='text' name='bank'  value="" class="width-full"  autocomplete="off" >
				</td>
			</tr>
			<tr>
				<th>결제 희망일</th>
				<td>
					<div class="calendar-input">
						<input type='text' name='desired_date'  value="<?=date('Y-m-d')?>" autocomplete="off" > 
					</div>
				</td>
			</tr>
			<tr>
				<th>비고</th>
				<td >
					<input type='text' name='memo'  value="" class="width-full"  autocomplete="off" >
				</td>
			</tr>

		</table>
		</form>

		<div class="m-t-10 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="paymentReg.createPayment(this);" >신청</button>
		</div>

	</div>
</div>

<?
/*
	echo "<pre>";
	print_r($result);
	echo "</pre>";
*/
?>

<script type="text/javascript"> 
<!-- 

const paymentReg = (function() {

	const API_ENDPOINTS = {
		createPayment: "/ad/proc/Admin/payment/createPayment",
	};

	return {

		// 초기화
		init() {
			console.log('orderSheetMainList module initialized.');
		},

		createPayment() {

			var price = document.getElementById('price').value.trim();
			if (!price) {
				alert('결제 금액을 입력해주세요.');
				return;
			}

			var form = $('#form_payment')[0];
			var formData = new FormData(form);

			ajaxRequest(API_ENDPOINTS.createPayment, formData, { 
				processData: false, 
				contentType: false 
			})
			.done(res => {
				if (res.status === "success") {

					alert("그룹지출 등록되었습니다.");
					location.reload();

				} else {
					dnAlert('Error', res.message || '상태 변경 실패', 'red'); // 실패 시 메시지 표시
				}
			})
			.catch(error => {
				dnAlert('Error', '상태 변경 실패', 'red');
				throw new Error('AJAX 요청 실패');
			});

		},

	}

})();	



$(function(){

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}

});
//--> 
</script> 