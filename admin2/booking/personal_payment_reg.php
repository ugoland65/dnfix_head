<?
include "../lib/inc_common.php";

$pageGroup = "booking";
$pageName = "personal_list";


	$_mode = securityVal($mode);
	$_key = securityVal($idx);

	$pg_data = wepix_fetch_array(wepix_query_error("select * from "._DB_PAYMENT_GATE." where PG_IDX = '".$_key."' "));



include "../layout/header.php";
?>

<STYLE TYPE="text/css">
.table-wrap{ width:100%; }
.table-style{ width:100%; }

</STYLE>
<link rel="stylesheet" href="/admin2/css/daterangepicker.css" />
<script src="/admin2/js/moment.min.js"></script>
<script src="/admin2/js/jquery.daterangepicker.js"></script>
<script src="/admin2/js/demo.js"></script>


<div id="contents_head">
	<h1>개인결제 상세보기</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">
			
			<table  cellspacing="1" cellpadding="0" class="table-style" >
				<tr>
					<th class="tds1">IDX</th>
					<td class="tds2"><?=$pg_data[PG_IDX]?></td>
				</tr>
				<tr>
					<th class="tds1">청구서 IDX (BILL)</th>
					<td class="tds2"><?=$pg_data[PG_BILL_IDX]?></td>
				</tr>
				<tr>
					<th class="tds1">MODE</th>
					<td class="tds2"><?=$pg_data[PG_MODE]?></td>
				</tr>
				<tr>
					<th class="tds1">CODE</th>
					<td class="tds2"><?=$pg_data[PG_CODE]?></td>
				</tr>
				<tr>
					<th class="tds1">STATE</th>
					<td class="tds2"><?=$pg_data[PG_STATE]?></td>
				</tr>
				<tr>
					<th class="tds1">KIND</th>
					<td class="tds2"><?=$pg_data[PG_KIND]?></td>
				</tr>
				<tr>
					<th class="tds1">이름</th>
					<td class="tds2"><?=$pg_data[PG_NAME]?></td>
				</tr>
				<tr>
					<th class="tds1">환율</th>
					<td class="tds2"><?=$pg_data[PG_EXCHANGE]?></td>
				</tr>

				<tr>
					<th class="tds1">기본설정화폐</th>
					<td class="tds2"><?=$pg_data[PG_BASE_CURRENCY]?></td>
				</tr>
				<tr>
					<th class="tds1">결제 화폐</th>
					<td class="tds2"><?=$pg_data[PG_PAYMENT_CURRENCY]?></td>
				</tr>
				<tr>
					<th class="tds1">부가율</th>
					<td class="tds2"><?=$pg_data[PG_SURTAX_RATE]?></td>
				</tr>
				<tr>
					<th class="tds1">부가세</th>
					<td class="tds2"><b><?=$gva_currency_simbol[$pg_data[PG_PAYMENT_CURRENCY]]?></b><?=$pg_data[PG_SURTAX]?></td>
				</tr>
				<tr>
					<th class="tds1">상품가격</th>
					<td class="tds2"><b><?=$gva_currency_simbol[$pg_data[PG_PAYMENT_CURRENCY]]?></b><?=$pg_data[PG_PRICE]?></td>
				</tr>
				<tr>
					<th class="tds1">결제 총가격</th>
					<td class="tds2"><b><?=$gva_currency_simbol[$pg_data[PG_PAYMENT_CURRENCY]]?></b><?=$pg_data[PG_TOTAL_PRICE]?></td>
				</tr>
				<tr>
					<th class="tds1">유저 ID</th>
					<td class="tds2"><?=$pg_data[PG_USER_ID]?></td>
				</tr>
				<tr>
					<th class="tds1">결제 날짜</th>
					<td class="tds2"><?=date("Y-m-d",$pg_data[PG_DATE])?></td>
				</tr>

				<tr>
					<th class="tds1">결제창 발행 아이디</th>
					<td class="tds2"><?=$pg_data[PG_REG_ID]?></td>
				</tr>
				<tr>
					<th class="tds1">결제창 발행 날짜</th>
					<td class="tds2"><?=date("Y-m-d",$pg_data[PG_REG_DATE])?></td>
				</tr>
				<tr>
					<th class="tds1">부킹그룹 idx</th>
					<td class="tds2"><?=$pg_data[PG_EXTRA_1]?></td>
				</tr>
				<?if($pg_data[PG_STATE] == 'approval'){?>
					<tr>
						<th class="tds1">환불</th>
						<td class="tds2"><input type='button' onclick="doRefund('<?=$pg_data[PG_IDX]?>','<?=$pg_data[PG_CODE]?>');" value='환불'></td>
					</tr>
				<?}else{?>
					<tr>
						<th class="tds1">취소 코드</th>
						<td class="tds2"><?=$pg_data[PG_CANCEL_CODE]?></td>
					</tr>
					<tr>
						<th class="tds1">취소 수수료</th>
						<td class="tds2">$<?=$pg_data[PG_CANCEL_FEE]?></td>
					</tr>
					<tr>
						<th class="tds1">취소 진행 ID</th>
						<td class="tds2"><?=$pg_data[PG_CANCEL_ID]?></td>
					</tr>
					<tr>
						<th class="tds1">취소 날짜</th>
						<td class="tds2"><?=date("Y-m-d",$pg_data[PG_CANCEL_DATE])?></td>
					</tr>
				<?}?>
			</table>
	
	

		</div>
		
				<div class="page-btn-wrap">
					<ul class="page-btn-left">
						<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_PERSONAL_PAYMENT_LIST?>'" > 
							<i class="fas fa-arrow-left"></i>
							List
						</button>
					</ul>
					<ul class="page-btn-right">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="bookingModify();" > 
							<i class="far fa-check-circle"></i>
							<?=$submit_btn_text?>
						</button>
					</ul>
				</div>
	</div>
</div>

<script type="text/javascript"> 
	function doRefund(key,code){
		if(confirm('환불시 환불취소는 불가능합니다. 진행하시겠습니까?')){
			location.href='<?=_A_PATH_BOOKING_OK?>?a_mode=doRefund&key='+key+'&code='+code;
		}
	}
</script> 

<?
include "../layout/footer.php";
exit;
?>