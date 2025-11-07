<?
// 변수 초기화
$_idx = $_GET['idx'] ?? $_POST['idx'] ?? "";
$_openpage = $_GET['openpage'] ?? $_POST['openpage'] ?? "";
$data = [];
$_oo_price_data = [];
$_oo_upload_file = [];
$_oo_express_data = [];
$_oo_approval_date = [];
$_oo_tex_data = [];
$_oo_date_data = [];
$_oo_reg_data = [];
$_json_oo_stock = [];
$_order_sheet_form = [];

if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from ona_order WHERE oo_idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = [];
	}

	$_oo_price_data = json_decode($data['oo_price_data'] ?? '{}', true);
	if (!is_array($_oo_price_data)) {
		$_oo_price_data = [];
	}

	$_oo_upload_file = json_decode($data['oo_upload_file'] ?? '{}', true);
	if (!is_array($_oo_upload_file)) {
		$_oo_upload_file = ['invoice' => [], 'pay_file' => [], 'import_declaration' => []];
	}

	$_oo_price_data_currency = $_oo_price_data['currency'] ?? ''; //주문금액 화폐단위
	$_oo_price_data_pay_fee = $_oo_price_data['pay_fee'] ?? 0; //최종 송금수수료
	$_oo_price_data_pay_price = $_oo_price_data['pay_price'] ?? 0; //최종 합계 송금액

	$_oo_price_data_exchange_charge = $_oo_price_data['exchange_charge'] ?? 0;
	$_oo_price_data_pay_date = $_oo_price_data['pay_date'] ?? '';
	$_oo_price_data_change_price = $_oo_price_data['change_price'] ?? [];


	$_oo_express_data = json_decode($data['oo_express_data'] ?? '{}', true);
	if (!is_array($_oo_express_data)) {
		$_oo_express_data = [];
	}

	$_oo_approval_date = json_decode($data['oo_approval_date'] ?? '{}', true);
	if (!is_array($_oo_approval_date)) {
		$_oo_approval_date = [];
	}

	$_oo_express_data_express_mode = $_oo_express_data['mode'] ?? '';
	$_oo_express_data_express_name = $_oo_express_data['name'] ?? '';
	$_oo_express_data_express_number = $_oo_express_data['number'] ?? '';
	$_oo_express_data_express_report_weight = $_oo_express_data['report_weight'] ?? '';
	$_oo_express_data_express_weight = $_oo_express_data['weight'] ?? '';
	$_oo_express_data_express_cbm = $_oo_express_data['cbm'] ?? '';
	$_oo_express_data_express_box = $_oo_express_data['box'] ?? '';
	$_oo_express_data_express_price = $_oo_express_data['price'] ?? 0;
	$_oo_express_data_express_price_add = $_oo_express_data['price_add'] ?? 0;

	$_oo_tex_data = json_decode($data['oo_tex_data'] ?? '{}', true);
	if (!is_array($_oo_tex_data)) {
		$_oo_tex_data = [];
	}

	$_oo_tex_data_num = $_oo_tex_data['num'] ?? '';
	$_oo_tex_data_report_price = $_oo_tex_data['report_price'] ?? 0;
	$_oo_tex_data_duty_price = $_oo_tex_data['duty_price'] ?? 0;
	$_oo_tex_data_vat_price = $_oo_tex_data['vat_price'] ?? 0;
	$_oo_tex_data_commission = $_oo_tex_data['commission'] ?? 0;

	$_oo_date_data = json_decode($data['oo_date_data'] ?? '{}', true);
	if (!is_array($_oo_date_data)) {
		$_oo_date_data = [];
	}

	//$_oo_date_data_order_send_date = $_oo_date_data['order_send_date'] ?? '';
	$_oo_date_data_in_date = $_oo_date_data['in_date'] ?? '';


	$_oo_price_date = ( ($data['oo_price_date'] ?? 0) > 0 ) ? $data['oo_price_date'] : "" ; 
	$_oo_in_date = ( ($data['oo_in_date'] ?? 0) > 0 ) ? $data['oo_in_date'] : "" ; 
	$_oo_duty_due_date = ( ($data['oo_duty_due_date'] ?? 0) > 0 ) ? $data['oo_duty_due_date'] : "" ; 
	$_oo_duty_settlement_date = ( ($data['oo_duty_settlement_date'] ?? 0) > 0 ) ? $data['oo_duty_settlement_date'] : "" ; 

	$_oo_box = $data['oo_box'] ?? '';
	$_oo_box_weight = ($data['oo_box_weight'] ?? 0)*1;
	$_oo_box_weight_fix = ($data['oo_box_weight_fix'] ?? 0)*1;
	$_oo_express = $data['oo_express'] ?? '';
	$_oo_express_number = $data['oo_express_number'] ?? '';
	$_oo_express_price = ( ($data['oo_express_price'] ?? 0) > 0 ) ? number_format($data['oo_express_price']) : "" ; 
	$_oo_express_price_date = ( ($data['oo_express_price_date'] ?? 0) > 0 ) ? $data['oo_express_price_date'] : "" ; 
	$_oo_express_price_settlement_date = ( ($data['oo_express_price_settlement_date'] ?? 0) > 0 ) ? $data['oo_express_price_settlement_date'] : "" ; 

	$_oo_reg_data = json_decode($data['reg'] ?? '{}', true);
	if (!is_array($_oo_reg_data)) {
		$_oo_reg_data = ['reg' => [], 'mod' => []];
	}

	$_json_oo_stock = json_decode($data['oo_stock'] ?? '{}', true);
	if (!is_array($_json_oo_stock)) {
		$_json_oo_stock = [];
	}

}

	//주문서 폼
	$_where = "";
	$_query = "select * from ona_order_group ".$_where." ORDER BY oog_idx DESC";
	$_result = sql_query_error($_query);
	while($_list = wepix_fetch_array($_result)){
		
		// 배열 검증
		if (!is_array($_list)) {
			continue;
		}
		
		$_order_sheet_form[] = array(
			"idx" => $_list['oog_idx'] ?? '',
			"name" => $_list['oog_name'] ?? ''
		);

	}

	$_os_state_text[1] = "작성중";
	$_os_state_text[2] = "주문전송";
	$_os_state_text[4] = "입금완료";
	$_os_state_text[5] = "입고완료";
	$_os_state_text[7] = "주문종료";

	$_os_pay_mode_list = array("계좌송금","모인","카드결제","예치금");
	//selected
?>

<style type="text/css">
.price{ width:100px !important; }
.price.price_point{  font-weight:bold; color:#ff0000; }
.change-price{ display:table; }
.change-price > ul{ display:table-row; }
.change-price > ul > li{ display:table-cell; padding:0 3px 2px 0; }
.change-price .change-price-body{ width:300px; }

.file-line{ background-color:#f7f7f7; border:1px solid #ddd; padding:6px; border-radius:4px; }

.input-point-ani {
  animation: ip-ani 1.5s cubic-bezier(.36,.07,.19,.97) both;
  transform: translate3d(0, 0, 0);
  backface-visibility: hidden;
}

@-webkit-keyframes ip-ani {
    0% { background-color: #ffb5b5; border:1px solid #646e83; }
    20% { background-color: #ffecec; border:1px solid #ff0000; }
    40% { background-color: #ffb5b5; border:1px solid #646e83; }
    60% { background-color: #ffecec; border:1px solid #ff0000; }
    80% { background-color: #ffb5b5; border:1px solid #646e83; }
    100% { background-color: #ffecec; border:1px solid #ff0000; }
}
</style>

<script type="text/javascript"> 
<!-- 
 var openPage = "<?=$_openpage ?? ''?>";
//--> 
</script>

	<form id="form1">

	<? if( $_idx ){ ?>
		<input type="hidden" name="a_mode" value="orderSheet_modify" >
		<input type="hidden" name="idx" value="<?=$_idx ?? ''?>" >
	<? }else{ ?>
		<input type="hidden" name="a_mode" value="orderSheet_reg" >
	<? } ?>

	<table class="table-style border01 width-full">
		
		<? if( $_idx ){ ?>
		<!-- 주문서 번호 -->
		<tr>
			<th style="width:140px;">주문서 번호</th>
			<td >
				<b><?=$_idx ?? ''?></b>
			</td>
		</tr>
		<? } ?>

		<!-- 주문서 이름 -->
		<tr>
			<th style="width:140px;">주문서 이름</th>
			<td >
				<input type='text' name='oo_name'  value="<?=$data['oo_name'] ?? ''?>" autocomplete="off" class="width-500">
			</td>
		</tr>

		<tr>
			<th>P/O CODE</th>
			<td >
				<input type='text' name='oo_po_name'  value="<?=$data['oo_po_name'] ?? ''?>" autocomplete="off" class="width-200">
				PURCHASE ORDER Offer No ( 무역 서류 주문서 P/O 발송시 사내 고유넘버 )
			</td>
		</tr>

		<!-- 주문서폼 IDX -->
		<tr>
			<th>주문서폼 (상품그룹)</th>
			<td >
				<select name="oo_form_idx">
					<option value="0">==  주문서 폼 선택 ==</option>
					<? 
					// 배열 검증 후 count
					$_order_sheet_form_count = is_array($_order_sheet_form) ? count($_order_sheet_form) : 0;
					
					for ($i=0; $i<$_order_sheet_form_count; $i++){ 
						// 배열 요소 검증
						if (!isset($_order_sheet_form[$i]) || !is_array($_order_sheet_form[$i])) {
							continue;
						}
					?>
					<option value="<?=$_order_sheet_form[$i]["idx"] ?? ''?>" <? if( ($data['oo_form_idx'] ?? 0) == ($_order_sheet_form[$i]["idx"] ?? '') ) echo "selected"; ?>><?=$_order_sheet_form[$i]["name"] ?? ''?></option>
					<? } ?>
				</select>
				<? if( $_idx && ($data['oo_form_idx'] ?? 0) == 0 ){ ?><span style="color:#ff0000;">※ 주문서 폼 미지정!!!</span><? } ?>
			</td>
		</tr>

		<!-- 수입형태 -->
		<tr>
			<th>수입형태</th>
			<td >
				<label><input type="radio" name="oo_import" value="국내" <? if( empty($data['oo_import']) || ($data['oo_import'] ?? '') == "국내" ) echo "checked"; ?> > 국내 주문</label>
				<label><input type="radio" name="oo_import" value="수입" <? if( ($data['oo_import'] ?? '') == "수입" ) echo "checked"; ?> > 수입 주문</label>
				<label><input type="radio" name="oo_import" value="구매대행" <? if( ($data['oo_import'] ?? '') == "구매대행" ) echo "checked"; ?> > 구매대행</label>
			</td>
		</tr>

		<tr>
			<th>주문서 화폐/환율</th>
			<td >
				주문서 금액 화폐
				<select name="sum_currency">
					<option value="원" <? if( ($data['oo_sum_currency'] ?? '') == "원" ) echo "selected"; ?>>원</option>
					<option value="엔" <? if( ($data['oo_sum_currency'] ?? '') == "엔" ) echo "selected"; ?>>엔</option>
					<option value="위안" <? if( ($data['oo_sum_currency'] ?? '') == "위안" ) echo "selected"; ?>>위안</option>
					<option value="달러" <? if( ($data['oo_sum_currency'] ?? '') == "달러" ) echo "selected"; ?>>달러</option>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				적용환율 : 
				<input type="text" name='sum_exchange_rate' class="price" value="<?=$data['oo_sum_exchange_rate'] ?? ''?>">
				※ 국내주문은 안써도 됨
			</td>
		</tr>

		<tr>
			<th>메모</th>
			<td>
				<textarea name="oo_memo" style="height:70px"><?=$data['oo_memo'] ?? ''?></textarea>
			</td>
		</tr>

		<? if( $_idx ){ ?>
		<!-- 주문상태 -->
		<tr>
			<th>주문상태</th>
			<td>
				<div class="os-state-btn-wrap">
					<button type="button" id="" class="btnstyle1 <? if( ($data['oo_state'] ?? '') == "1" ) echo "btnstyle1-info"; ?> btnstyle1-sm" onclick="orderSheetReg.stateModify(this, '1', '<?=$_idx ?? ''?>')" data-name="작성중" >작성중</button>
					<button type="button" id="" class="btnstyle1 <? if( ($data['oo_state'] ?? '') == "2" ) echo "btnstyle1-info"; ?> btnstyle1-sm" onclick="orderSheetReg.stateModify(this, '2', '<?=$_idx ?? ''?>')" data-name="주문전송" >주문전송</button>
					<button type="button" id="" class="btnstyle1 <? if( ($data['oo_state'] ?? '') == "4" ) echo "btnstyle1-info"; ?> btnstyle1-sm" onclick="orderSheetReg.stateModify(this, '4', '<?=$_idx ?? ''?>')" data-name="입금완료" >입금완료</button>
					<button type="button" id="" class="btnstyle1 <? if( ($data['oo_state'] ?? '') == "5" ) echo "btnstyle1-info"; ?> btnstyle1-sm" onclick="orderSheetReg.stateModify(this, '5', '<?=$_idx ?? ''?>')" data-name="입고완료" >입고완료</button>
					<button type="button" id="" class="btnstyle1 <? if( ($data['oo_state'] ?? '') == "7" ) echo "btnstyle1-info"; ?> btnstyle1-sm" onclick="orderSheetReg.stateModify(this, '7', '<?=$_idx ?? ''?>')" data-name="주문종료" >주문종료</button>
				</div>
				<? /*
				<div>현재상태 : <?=$_os_state_text[$data['oo_state']]?></div>
				<div>
					변경 : <select name="oo_state" id="oo_state" >
						<option value="1" <? if( $data['oo_state'] == "1" ) echo "selected"; ?>>작성중</option>
						<option value="2" <? if( $data['oo_state'] == "2" ) echo "selected"; ?>>주문전송</option>
						<option value="4" <? if( $data['oo_state'] == "4" ) echo "selected"; ?>>입금완료</option>
						<option value="5" <? if( $data['oo_state'] == "5" ) echo "selected"; ?>>입고완료</option>
						<option value="7" <? if( $data['oo_state'] == "7" ) echo "selected"; ?>>주문종료</option>
					</select>
				</div>
				*/ ?>
				<div class="m-t-5">
					<? 
					// 배열 검증 및 초기화
					$_oo_date_data_state = $_oo_date_data['state'] ?? [];
					if (!is_array($_oo_date_data_state)) {
						$_oo_date_data_state = [];
					}
					$_oo_date_data_state_count = count($_oo_date_data_state);
					
					for ($i=0; $i<$_oo_date_data_state_count; $i++){ 
						// 배열 요소 검증
						if (!isset($_oo_date_data_state[$i]) || !is_array($_oo_date_data_state[$i])) {
							continue;
						}
					?>
					<ul>
						<?=$_os_state_text[$_oo_date_data_state[$i]['state_before'] ?? ''] ?? ''?> -> <?=$_os_state_text[$_oo_date_data_state[$i]['state_after'] ?? ''] ?? ''?>
						:: <?=$_oo_date_data_state[$i]['id'] ?? ''?> ( <?=$_oo_date_data_state[$i]['date'] ?? ''?> )
					</ul>
					<? } ?>
				</div>
			</td>
		</tr>

		<? if( ($data['oo_state'] ?? 0) > 4 ){ ?>
		<tr>
			<th>재고 일괄등록</th>
			<td>
				
				<? if( ($_json_oo_stock['state'] ?? '') == "in" ){ ?>
					<span style="">재고일괄등록 완료 ( <?=!empty($_json_oo_stock['reg']['date']) ? date ("y.m.d <b>H:i</b>", strtotime($_json_oo_stock['reg']['date'])) : ''?> ) | <?=$_json_oo_stock['reg']['name'] ?? ''?></span>
				<? }else{ ?>
					<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheet.osStock('<?=$_idx ?? ''?>');">재고 일괄등록</button>
				<? } ?>
			</td>
		</tr>
		<? } ?>

		<!-- 주문정보 -->
		<tr>
			<th>주문정보</th>
			<td>
				<table class="table-style width-full">
					
					<tr>
						<td style="width:120px;">상품 주문가격</td>
						<td>
							<input type='text' name='oo_sum_price' class="price" value="<?=number_format($data['oo_sum_price'] ?? 0)?>" >
							<select name="currency">
								<option value="엔" <? if( $_oo_price_data_currency == "엔" ) echo "selected"; ?>>엔</option>
								<option value="원" <? if( $_oo_price_data_currency == "원" ) echo "selected"; ?>>원</option>
								<option value="위안" <? if( $_oo_price_data_currency == "위안" ) echo "selected"; ?>>위안</option>
								<option value="달러" <? if( $_oo_price_data_currency == "달러" ) echo "selected"; ?>>달러</option>
							</select>
						</td>
					</tr>

					<!-- 확정 주문 금액 -->
					<tr>
						<td>주문서 발송일</td>
						<td>
							<div class="calendar-input" style="display:inline-block;"><input type='text' name='order_send_date'  value="<?=$_oo_date_data['order_send_date'] ?? ''?>" ></div>
						</td>
					</tr>

					<!-- 가격변동사항 -->
					<tr>
						<td >가격변동사항</td>
						<td>
							<div id="change_price" class="change-price">
								<?
								// 배열 검증
								if (!is_array($_oo_price_data_change_price)) {
									$_oo_price_data_change_price = [];
								}
								$_oo_price_data_change_price_count = count($_oo_price_data_change_price);
								
								for ( $i=0; $i<$_oo_price_data_change_price_count; $i++){
									// 배열 요소 검증
									if (!isset($_oo_price_data_change_price[$i]) || !is_array($_oo_price_data_change_price[$i])) {
										continue;
									}
									
									$_change_price_mode = $_oo_price_data_change_price[$i]['mode'] ?? '';
									$_change_price_body = $_oo_price_data_change_price[$i]['body'] ?? '';
									$_change_price_price = $_oo_price_data_change_price[$i]['price'] ?? 0;
								?>
								<ul>
									<li>
										<select name="change_price_mode[]">
											<option value="할인" <? if( $_change_price_mode == "할인" ) echo "selected"; ?>>할인</option>
											<option value="추가" <? if( $_change_price_mode == "추가" ) echo "selected"; ?>>추가</option>
										</select>
									<li>
									<li><input type="text" name='change_price_price[]' class="price" placeholder="금액" value="<?=number_format($_change_price_price,2)?>"><li>
									<li><input type="text" name='change_price_body[]' class= 'change-price-body' value="<?=$_change_price_body?>" placeholder="사유"><li>
									<li><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="orderSheetReg.delChangePrice(this)" ><i class="fas fa-minus-circle"></i> 삭제</button><li>
								</ul>
								<? } ?>
							</div>
							<div class="m-t-5"><button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.addChangePrice();" >주문 변동금액 추가</button></div>
						</td>
					</tr>

					<!-- 확정 주문 금액 -->
					<tr>
						<td>확정 주문 금액</td>
						<td><? /* onkeyUP="GC.commaInput( this.value, this );" */ ?>
							<input type='text' name='oo_fn_price' id='oo_fn_price' class="price price_point" value="<?=number_format($data['oo_fn_price'] ?? 0,2)?>"  >
						</td>
					</tr>

					<!-- 인보이스 -->
					<tr>
						<td>주문 관련 파일</td>
						<td>
							
							<div id="file_line_wrap_invoice">
								<? 
								// 배열 검증
								$_invoice_files = $_oo_upload_file['invoice'] ?? [];
								if (is_array($_invoice_files)) {
									foreach ( $_invoice_files as $key => $val ){
										if (!is_array($val)) continue;
										
										if( !empty($val['view_name']) ){
											$_this_filename = $val['view_name']." ( ".($val['name'] ?? '')." )";
										}else{
											$_this_filename = $val['name'] ?? '';
										}
								?>
								<div class="file-line m-t-5">
									<i class="far fa-save fa-flip-horizontal"></i> 
									<a href="/data/uploads/<?=$val['name'] ?? ''?>" target="_blank"><?=$_this_filename?></a>
									:: <?=$val['id'] ?? ''?> ( <?=$val['date'] ?? ''?> )
									<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.fileDel(this, 'invoice', '<?=$_idx ?? ''?>' ,'<?=$val['name'] ?? ''?>')" >
										<i class="fas fa-trash-alt"></i>
									</button>
								</div>
								<? 
									}
								}
								?>
							</div>

							<div class="m-t-5">
								<input type="text" name="upload_file_invoice_name" id="upload_file_invoice_name" style="width:200px;" placeholder="노출될 파일이름" >
								<input name="upload_file_invoice" id="upload_file_invoice" type="file" class="m-t-5">
							</div>

							<div class="m-t-5">
								<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.fileUpload('invoice');" >주문관련파일 업로드</button>
							</div>

						</td>
					</tr>

				</table>
			</td>
		</tr>

		<!-- 결제정보 -->
		<tr>
			<th>결제 (송금)정보
			<!-- <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheet.osViewReset('438')" >새로고침</button> -->
			</th>
			<td>
				<table class="table-style width-full">
					<tr>
						<td style="width:120px;">결제처리</td>
						<td>
							<?
								$_pay_list = $_oo_price_data['pay_list'] ?? [];
								if (!is_array($_pay_list)) {
									$_pay_list = [];
								}
							?>
							<div id="add_pay_list" class="m-t-5">
								<? 
								$_pay_list_count = count($_pay_list);
								
								for ($i=0; $i<$_pay_list_count; $i++){ 

									// 배열 요소 검증
									if (!isset($_pay_list[$i]) || !is_array($_pay_list[$i])) {
										continue;
									}

									$_this_pay_mode = $_pay_list[$i]['pay_mode'] ?? '';
									$_this_pay_price = $_pay_list[$i]['pay_price'] ?? 0;
									$_this_pay_date = $_pay_list[$i]['pay_date'] ?? '';
									$_this_pay_memo = $_pay_list[$i]['pay_memo'] ?? '';

								?>
								<ul class="m-t-5">
									<span class="display-inline-block">
										<select name="pay_mode[]">
											<? 
											$_os_pay_mode_list_count = is_array($_os_pay_mode_list) ? count($_os_pay_mode_list) : 0;
											for ( $z=0; $z<$_os_pay_mode_list_count; $z++ ){ 
												if (!isset($_os_pay_mode_list[$z])) continue;
											?>
											<option value="<?=$_os_pay_mode_list[$z] ?? ''?>" <? if( $_this_pay_mode == ($_os_pay_mode_list[$z] ?? '') ) echo "selected"; ?>><?=$_os_pay_mode_list[$z] ?? ''?></option>
											<? } ?>
										</select>
									</span>
									<span class="display-inline-block">
										결제금 : <input type="text" name="pay_price[]" class="price price_point" value="<?=number_format($_this_pay_price)?>" onkeyUP="GC.commaInput( this.value, this );" style="width:80px;" > 원
									</span>
									<span class="display-inline-block m-l-10">
										결제일 : <div class="calendar-input" style="display:inline-block;"><input type="text" name="pay_date[]"  value="<?=$_this_pay_date?>" style="width:80px;" ></div>
									</span>
									<span class="display-inline-block">
										<input type="text" name="pay_memo[]" value="<?=$_this_pay_memo?>" style="width:250px;" placeholder="메모" >
									</span>
									<span class="display-inline-block">
										<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.addPayListDel(this)" ><i class="fas fa-trash-alt"></i></button>
									</span>
								</ul>
								<? } ?>
							</div>
							<div class="m-t-5"><button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.addPayList();" >결제정보 추가</button></div>
						</td>
					</tr>
					<tr>
						<td>최종 결제수수료</td>
						<td>
							<input type='text' name='pay_fee' value="<?=number_format($_oo_price_data_pay_fee)?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;' > 원
						</td>
					</tr>
					<tr>
						<td>최종 합계 결제액</td>
						<td>
							<input type='text' name='oo_price_kr' id='oo_price_kr' class="price price_point" value="<?=number_format($data['oo_price_kr'] ?? 0)?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;' > 원
							( ※ 예치금 결제도 결제액으로 포함 )
						</td>
					</tr>

					<!-- 송금확인증 -->
					<tr>
						<td>결제 관련 파일</td>
						<td>

							<div id="file_line_wrap_pay">
								<? 
								// 배열 검증
								$_pay_files = $_oo_upload_file['pay_file'] ?? [];
								if (is_array($_pay_files)) {
									foreach ( $_pay_files as $key => $val ){
										if (!is_array($val)) continue;
										
										if( !empty($val['view_name']) ){
											$_this_filename = $val['view_name']." ( ".($val['name'] ?? '')." )";
										}else{
											$_this_filename = $val['name'] ?? '';
										}
								?>
								<div class="file-line m-t-5">
									<i class="far fa-save fa-flip-horizontal"></i> 
									<a href="/data/uploads/<?=$val['name'] ?? ''?>" target="_blank"><?=$_this_filename?></a>
									:: <?=$val['id'] ?? ''?> ( <?=$val['date'] ?? ''?> )
									<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.fileDel(this, 'pay', '<?=$_idx ?? ''?>' ,'<?=$val['name'] ?? ''?>')" >
										<i class="fas fa-trash-alt"></i>
									</button>
								</div>
								<? 
									}
								}
								?>
							</div>

							<div class="m-t-5">
								<input type="text" name="upload_file_pay_name" id="upload_file_pay_name" style="width:200px;" placeholder="노출될 파일이름" >
								<input name="upload_file" id="upload_file_pay" type="file" class="m-t-5">
							</div>

							<div class="m-t-5">
								<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.fileUpload('pay');" >송금 확인증 업로드</button>
							</div>

						</td>
					</tr>
				</table>

			</td>
		</tr>

		<!-- 배송 -->
		<tr>
			<th>배송</th>
			<td>
				<table class="table-style width-full">
					<tr>
						<td style="width:120px;">배송방식</td>
						<td>
							<label><input type="radio" name="express_mode" value="국내택배" <? if($_oo_express_data_express_mode=="국내택배") echo "checked"; ?>> 국내택배</label>
							<label><input type="radio" name="express_mode" value="국내화물" <? if($_oo_express_data_express_mode=="국내화물") echo "checked"; ?>> 국내화물</label>
							<label><input type="radio" name="express_mode" value="항공" <? if($_oo_express_data_express_mode=="항공") echo "checked"; ?>> 항공</label>
							<label><input type="radio" name="express_mode" value="해운" <? if($_oo_express_data_express_mode=="해운") echo "checked"; ?>> 해운</label>
						</td>
					</tr>
					<tr>
						<td>배송사</td>
						<td>
							<select name="express_name">
								<option value="항공 FEDEX" <? if( $_oo_express_data_express_name == "항공 FEDEX" ) echo "selected"; ?>>항공 FEDEX</option>
								<option value="항공 DHL" <? if( $_oo_express_data_express_name == "항공 DHL" ) echo "selected"; ?>>항공 DHL</option>
								<option value="항공 UPS" <? if( $_oo_express_data_express_name == "항공 UPS" ) echo "selected"; ?>>항공 UPS</option>
								<option value="중국 해운 이안로지스틱" <? if( $_oo_express_data_express_name == "중국 해운 이안로지스틱" ) echo "selected"; ?>>중국 해운 이안로지스틱</option>
								<option value="중국 해운 구매대행" <? if( $_oo_express_data_express_name == "중국 해운 구매대행" ) echo "selected"; ?>>중국 해운 구매대행</option>
								<option value="일본 해운 파테스" <? if( $_oo_express_data_express_name == "일본 해운 파테스" ) echo "selected"; ?>>일본 해운 파테스</option>
								<option value="일본 해운 HTNS" <? if( $_oo_express_data_express_name == "일본 해운 HTNS" ) echo "selected"; ?>>일본 해운 HTNS</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>송장번호</td>
						<td><input type='text' name='express_number' value="<?=$_oo_express_data_express_number?>" style='width:300px;' ></td>
					</tr>
					<tr>
						<td>중량 / CBM / 박스</td>
						<td>
							신고서 중량 : <input type='text' name='express_report_weight' value="<?=$_oo_express_data_express_report_weight?>" style='width:80px;' > kg / 
							청구 중량 : <input type='text' name='express_weight' value="<?=$_oo_express_data_express_weight?>" style='width:80px;' > kg / 
							CBM : <input type='text' name='express_cbm' value="<?=$_oo_express_data_express_cbm?>" style='width:60px;' > /
							박스수 : <input type='text' name='express_box' value="<?=$_oo_express_data_express_box?>" style='width:60px;' > 박스
						</td>
					</tr>
					<tr>
						<td>배송비</td>
						<td>
							배송비 : <input type='text' name="express_price" id="express_price" class="price price_point" value="<?=number_format($_oo_express_data_express_price)?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;' >원 
						</td>
					</tr>

					<!-- 배송비 결제기한 -->
					<tr>
						<td>배송비 결제기한</td>
						<td>
							<div class="calendar-input" style="display:inline-block;"><input type="text" name="expressApprovalPayment_date"  id="expressApprovalPayment_date" value="<?=$_oo_approval_date['express']['approval']['date'] ?? ''?>" ></div>
							
							<? 
							if( !empty($_oo_approval_date['express']['approval']['date']) ){
								$_this_calendar_idx = $_oo_approval_date['express']['approval']['calendar_idx'] ?? '';
							?>
								<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.approvalPayment('<?=$_idx ?? ''?>', 'express', 'modify', '<?=$_this_calendar_idx?>');" >배송비 결제기한 수정</button>
								
								<? if( !empty($_oo_approval_date['express']['approval']['calendar_idx']) ){ ?>
								<div class="m-t-10">
									캘린더 노출중 ( <?=$_this_calendar_idx?> )
									
									<? if( ($_oo_approval_date['express']['approval']['calendar_state'] ?? '') == "E" ){ ?>
										처리완료 ( <?=!empty($_oo_approval_date['express']['approval']['calendar_reg']['date']) ? date ("y.m.d <b>H:i</b>", strtotime($_oo_approval_date['express']['approval']['calendar_reg']['date'])) : ''?> :: <?=$_oo_approval_date['express']['approval']['calendar_reg']['id'] ?? ''?>)
									<? }else{ ?>
									<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs" onclick="orderSheetReg.calendarOk('<?=$_idx ?? ''?>', 'express', '<?=$_this_calendar_idx?>')" >완료처리</button>
									<? } ?>

									<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.calendarDel('<?=$_idx ?? ''?>', 'express', '<?=$_this_calendar_idx?>')" ><i class="fas fa-trash-alt"></i> 캘린더 삭제</button>
								</div>
								<? } ?>

							<? }else{ ?>
								<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.approvalPayment('<?=$_idx ?? ''?>', 'express', 'new');" >배송비 결제기한 등록</button>
							<? } ?>

						</td>
					</tr>

					<tr>
						<td>추가 배송비</td>
						<td>
							추가배송비(용달등) : <input type='text' name='express_price_add' class="price price_point" value="<?=number_format($_oo_express_data_express_price_add)?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;' >원
							<div class="m-t-5">
								<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.addPayList();" >추가 배송비</button>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<!-- 관부가세 -->
		<tr>
			<th>관부가세<br>(수입 전용)</th>
			<td>
				<table class="table-style width-full">
					<tr>
						<td style="width:120px;">수입신고번호</td>
						<td>
							<input type='text' name='tex_num' value="<?=$_oo_tex_data_num?>" style='width:300px;'>
						</td>
					</tr>
					<tr>
						<td>수입신고가격</td>
						<td>
							<input type='text' name='tex_report_price' class="price" value="<?=number_format($_oo_tex_data_report_price)?>"  style='width:100px;' >원
						</td>
					</tr>
					<tr>
						<td>관/부가세</td>
						<td>
							관세 : <input type='text' name='tex_duty_price' id='tex_duty_price' class="price price_point" value="<?=number_format($_oo_tex_data_duty_price)?>"  style='width:100px;' >원 / 
							부가세 : <input type='text' name='tex_vat_price' id='tex_vat_price' class="price price_point" value="<?=number_format($_oo_tex_data_vat_price)?>"  style='width:100px;' >원
						</td>
					</tr>

					<!-- 관/부가세 결제기한 -->
					<tr>
						<td>관/부가세 결제기한</td>
						<td>
							<div class="calendar-input" style="display:inline-block;"><input type="text" name="texApprovalPayment_date"  id="texApprovalPayment_date" value="<?=$_oo_approval_date['tax']['approval']['date'] ?? ''?>" ></div>
							
							<? 
							if( !empty($_oo_approval_date['tax']['approval']['date']) ){
								$_this_calendar_idx = $_oo_approval_date['tax']['approval']['calendar_idx'] ?? '';
							?>
								<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.approvalPayment('<?=$_idx ?? ''?>', 'tax', 'modify', '<?=$_this_calendar_idx?>');" >관/부가세 결제기한 수정</button>

								<? if( !empty($_oo_approval_date['tax']['approval']['calendar_idx']) ){ ?>
								<div class="m-t-10">
									캘린더 노출중 ( <?=$_this_calendar_idx?> )

									<? if( ($_oo_approval_date['tax']['approval']['calendar_state'] ?? '') == "E" ){ ?>
										처리완료 ( <?=!empty($_oo_approval_date['tax']['approval']['calendar_reg']['date']) ? date ("y.m.d <b>H:i</b>", strtotime($_oo_approval_date['tax']['approval']['calendar_reg']['date'])) : ''?> :: <?=$_oo_approval_date['tax']['approval']['calendar_reg']['id'] ?? ''?>)
									<? }else{ ?>
									<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs" onclick="orderSheetReg.calendarOk('<?=$_idx ?? ''?>', 'tax', '<?=$_this_calendar_idx?>')" >완료처리</button>
									<? } ?>

									<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.calendarDel('<?=$_idx ?? ''?>', 'tax', '<?=$_this_calendar_idx?>')" ><i class="fas fa-trash-alt"></i> 캘린더에서 삭제</button>
								
								</div>
								<? } ?>
							<? }else{ ?>
								<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.approvalPayment('<?=$_idx ?? ''?>', 'tax', 'new');" >관/부가세 결제기한 등록</button>
							<? } ?>

						</td>
					</tr>

					<tr>
						<td>관세사 수수료</td>
						<td>
							<input type='text' name='tex_commission' class="price price_point" value="<?=number_format($_oo_tex_data_commission)?>"  style='width:100px;' >원
						</td>
					</tr>


					<tr>
						<td>수입/세금 관련파일</td>
						<td>
							
							<div id="file_line_wrap_import_declaration">
								<? 
								// 배열 검증
								$_import_declaration_files = $_oo_upload_file['import_declaration'] ?? [];
								if (is_array($_import_declaration_files)) {
									foreach ( $_import_declaration_files as $key => $val ){
										if (!is_array($val)) continue;
										
										if( !empty($val['view_name']) ){
											$_this_filename = $val['view_name']." ( ".($val['name'] ?? '')." )";
										}else{
											$_this_filename = $val['name'] ?? '';
										}
								?>
								<div class="file-line m-t-5">
									<i class="far fa-save fa-flip-horizontal"></i> 
									<a href="/data/uploads/<?=$val['name'] ?? ''?>" target="_blank"><?=$_this_filename?></a>
									:: <?=$val['id'] ?? ''?> ( <?=$val['date'] ?? ''?> )
									<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.fileDel(this, 'import_declaration', '<?=$_idx ?? ''?>' ,'<?=$val['name'] ?? ''?>')" >
										<i class="fas fa-trash-alt"></i>
									</button>
								</div>
								<? 
									}
								}
								?>
							</div>

							<div class="m-t-5">
								<input type="text" name="upload_file_import_declaration_name" id="upload_file_import_declaration_name" style="width:200px;" placeholder="노출될 파일이름" >
								<input name="upload_file_import_declaration" id="upload_file_import_declaration" type="file" class="m-t-5">
							</div>

							<div class="m-t-5">
								<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="orderSheetReg.fileUpload('import_declaration');" >수입신고필증 업로드</button>
							</div>

						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<th>입고</th>
			<td>
				입고일 : <div class="calendar-input" style="display:inline-block;"><input type='text' name='in_date' id='in_date'  value="<?=$_oo_date_data_in_date?>" ></div>
			</td>
		</tr>

		<tr>
			<th>Log</th>
			<td>
				<div>
					<ul>등록 : <?=$_oo_reg_data['reg']['date'] ?? ''?> ( <?=$_oo_reg_data['reg']['id'] ?? ''?> )</ul>
					<? 
					$_oo_reg_data_mod = $_oo_reg_data['mod'] ?? [];
					if (!is_array($_oo_reg_data_mod)) {
						$_oo_reg_data_mod = [];
					}
					$_oo_reg_data_mod_count = count($_oo_reg_data_mod);
					
					for ($i=0; $i<$_oo_reg_data_mod_count; $i++){ 
						// 배열 요소 검증
						if (!isset($_oo_reg_data_mod[$i]) || !is_array($_oo_reg_data_mod[$i])) {
							continue;
						}
					?>
					<ul>수정 : <?=$_oo_reg_data_mod[$i]['date'] ?? ''?> ( <?=$_oo_reg_data_mod[$i]['id'] ?? ''?> )</ul>
					<? } ?>
				</div>
			</td>
		</tr>
		<? } ?>

	</table>
	</form>

	<div class="m-t-10 text-center">
		<?  if( $_idx ){ ?>
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="orderSheetReg.save(this, 'stay');" >저장후 남아있기</button>
		<? }else{ ?>
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="orderSheetReg.save(this);" >신규주문서 생성</button>
		<? } ?>
	</div>

	<!-- 파일등록 -->
	<form name='file_upload_form'  id='file_upload_form' method='post' enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="a_mode" value="orderSheetFile">
		<input type="hidden" name="smode" id="file_upload_mode" >
		<input type="hidden" name="sname" id="file_upload_name" >
		<input type="hidden" name="idx" value="<?=$_idx ?? ''?>">
	</form>

<script type="text/javascript"> 
<!-- 
var orderSheetReg = function() {

	var thisOsState = "<?=$data['oo_state'] ?? ''?>";
	var osPayModeList = <?=json_encode($_os_pay_mode_list ?? [], JSON_UNESCAPED_UNICODE);?>;

<? if( $_idx ){ ?>
	var _oo_fn_price = "<?=$data['oo_fn_price'] ?? 0?>";
	var _oo_price_kr = "<?=$data['oo_price_kr'] ?? 0?>";
	var _tex_duty_price = "<?=$_oo_tex_data_duty_price ?? 0?>";
	var _tex_vat_price = "<?=$_oo_tex_data_vat_price ?? 0?>";
	var _in_date = "<?=$_oo_date_data_in_date ?? ''?>";
<? } ?>

	var C = function() {
	};

	return {
		init : function() {

		},

		//송금정보 추가
		stateModify : function( obj, state, idx ) {

			var oo_import = $(':input:radio[name=oo_import]:checked').val();
			var state_name = $(obj).data("name");
			var content_msg = '주문상태를 ('+ state_name +')로 변경하시겠습니까?';
			var _data = { "a_mode":"os_State", "idx":idx, "state":state };

			$("#oo_fn_price").removeClass('input-point-ani');

			// 입금완료
			if( state > 2 ){

				if( !_oo_fn_price || _oo_fn_price == "0" ){
					$("#oo_fn_price").addClass('input-point-ani').focus();
					showAlert("Error", "(확정 주문 금액)을 입력해주세요.<br>입금완료 처리는 (확정 주문 금액), (최종 합계 결제액)값이.<br> 있어야 변경 가능합니다.<br>※ 입력후 하단 저장 버튼 완료 해야 변경이 가능합니다.", "alert2" );
					return false;
				}

				if( !_oo_price_kr || _oo_price_kr == "0" ){
					$("#oo_price_kr").addClass('input-point-ani').focus();
					showAlert("Error", "(최종 합계 결제액)을 입력해주세요.<br>입금완료 처리는 (확정 주문 금액), (최종 합계 결제액)값이.<br> 있어야 변경 가능합니다.<br>※ 입력후 하단 저장 버튼 완료 해야 변경이 가능합니다.", "alert2" );
					return false;
				}

			}

			if( oo_import == "수입" ){
				
				if( state > 4 ){
					
					if( !_tex_duty_price || _tex_duty_price == "0" ){
						$("#tex_duty_price").addClass('input-point-ani').focus();
						showAlert("Error", "(관세)가 비어있습니다.<br>수입형태가 (수입주문)일 경우 (관세),(부가세),(입고일)를 <br>입력해야만 (입고완료 이상) 변경이 가능합니다.<br>※ 입력후 하단 저장 버튼 완료 해야 변경이 가능합니다.", "alert2" );
						return false;
					}

					if( !_tex_vat_price || _tex_vat_price == "0" ){
						$("#tex_vat_price").addClass('input-point-ani').focus();
						showAlert("Error", "(부가세)가 비어있습니다.<br>수입형태가 (수입주문)일 경우 (관세),(부가세),(입고일)를 <br>입력해야만 (입고완료 이상) 변경이 가능합니다.<br>※ 입력후 하단 저장 버튼 완료 해야 변경이 가능합니다.", "alert2" );
						return false;
					}

				}

			}

			if( state > 4 ){
				
				var _in_date = $("#in_date").val();

				//입고일
				if( !_in_date || _in_date == "" ){
						
					$("#in_date").addClass('input-point-ani').focus();
					showAlert("Error", "(입고일)이 비어있습니다.<br>※ 입력후 하단 저장 버튼 완료 해야 변경이 가능합니다.", "alert2" );
					return false;

				}

				content_msg = '주문상태를 ( 입고완료 )로 변경하시겠습니까?<br>저장될 입고일은 ('+ _in_date +') 입니다.';
				_data = { "a_mode":"os_State", "idx":idx, "state":state, "in_date":_in_date };
			}

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '주문상태 변경',
				content: content_msg,
				type: 'blue',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '변경하기',
						btnClass: 'btn-blue',
						action: function(){

							$.ajax({
								url: "/ad/processing/order_sheet",
								data: _data,
								type: "POST",
								dataType: "json",
								success: function(res){
									if (res.success == true ){
										
										/*
										$(".os-state-btn-wrap button").removeClass('btnstyle1-info');
										$(obj).addClass('btnstyle1-info');
										*/

										toast2("success", "주문상태", "주문상태가 변경되었습니다.");
							
										if( openPage == "global" ){
											onlyAD.orderSheetReset(idx,'global');
										}else{
											orderSheet.osViewReset(idx);
										}

										if( state > 4 ){
											orderSheet.osStock(idx);
										}

									}else{
										showAlert("Error", res.msg, "alert2" );
										return false;
									}
								},
								error: function(request, status, error){
									console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
									showAlert("Error", "에러", "alert2" );
									return false;
								},
								complete: function() {
									//$(obj).attr('disabled', false);
								}
							});

						}
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});

		},

		//송금정보 추가
		addPayList : function() {

			var html = '<ul class="m-t-5">'
				+ '<span class="display-inline-block">'
				+ '<select name="pay_mode[]">';

			for (var i = 0; i < osPayModeList.length; i++) {
				html += '<option value="'+ osPayModeList[i] +'" >'+ osPayModeList[i] +'</option>';
			}

				html += '</select>'
				+ '<span class="display-inline-block">'
				+ ' 결제금 : <input type="text" name="pay_price[]" class="price price_point" value="" onkeyUP="GC.commaInput( this.value, this );" style="width:80px;" > 원'
				+ '</span>'
				+ '<span class="display-inline-block m-l-10">'
				+ ' 결제일 : <div class="calendar-input" style="display:inline-block;"><input type="text" name="pay_date[]"  value="" style="width:80px;" ></div>'
				+ '</span>'
				+ '<span class="display-inline-block">'
				+ ' <input type="text" name="pay_memo[]" value="" style="width:250px;" placeholder="메모" >'
				+ '</span>'
				+ '<span class="display-inline-block">'
				+ ' <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.addPayListDel(this)" ><i class="fas fa-trash-alt"></i></button>'
				+ '</span>'
				+ '</ul>';

			$("#add_pay_list").append(html);
			$(".calendar-input input").datepicker(clareCalendar);

		},

		//송금정보 추가 삭제
		addPayListDel : function(obj) {
			$(obj).parents( 'ul' ).remove();
		},

		//주문 변동금액 추가
		addChangePrice : function() {

			var html = '<ul>'
				+ '<li>'
				+ '<select name="change_price_mode[]">'
				+ '<option value="할인">할인</option>'
				+ '<option value="추가">추가</option>'
				+ '</select>'
				+ '<li>'
				+ '<li><input type="text" name="change_price_price[]" class="price" placeholder="금액"><li>'
				+ '<li><input type="text" name="change_price_body[]" class= "change-price-body" placeholder="사유"><li>'
				+ '<li><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="orderSheetReg.delChangePrice(this)" ><i class="fas fa-minus-circle"></i> 삭제</button><li>'
				+ '</ul>';

			$("#change_price").append(html);

		},

		//주문 변동금액 추가 삭제
		delChangePrice : function(obj) {
			$(obj).parent().parent().remove();
		},

		// 파일업로드
		fileUpload : function( mode ) {

			var fileCheck = document.getElementById("upload_file_"+ mode).value;
			if( !fileCheck ){
				showAlert("Error", "파일을 첨부해 주세요", "alert2" );
				return false;
			}

			$("#file_upload_mode").val(mode);
			$("#file_upload_name").val($("#upload_file_"+ mode + "_name").val());

			var form = $('#file_upload_form')[0];
			var imgData = new FormData(form);

			imgData.append("fileObj", $("#upload_file_" + mode)[0].files[0]);

			$.ajax({
				url: "/ad/processing/order_sheet",
				data: imgData,
				type: "POST",
				dataType: "json",
				contentType : false,
				processData : false,
				success: function(res){
					if ( res.success == true ){

						toast2("success", "파일등록", "파일이 성공적으로 등록되었습니다.");

						var html = '<div class="file-line m-t-5">'
							+ '<i class="far fa-save fa-flip-horizontal"></i>'
							+ ' <a href="/data/uploads/'+ res.filename +'" target="_blank">'+ res.filename +'</a>'
							+ ' :: '+ res.reg_id +' ( '+ res.reg_date +' )'
							+ ' <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.fileDel(this, \''+ mode +'\', \''+ res.idx +'\' ,\''+ res.filename +'\')" >'
							+ '<i class="fas fa-trash-alt"></i>'
							+ '</button>'
							+ '</div>';

						$("#file_line_wrap_" + mode).append(html);	
						$("#upload_file_"+ mode).val("");

					}else{
						showAlert("Error", res.msg, "dialog" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {

				}
			});	

		},

		// 파일삭제
		fileDel : function( obj, mode, idx, filename ) {

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제하시겠습니까?',
				content: '( '+ filename +' ) 파일을 삭제합니다.<br>삭제시 복구되지 않습니다.',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '삭제하기',
						btnClass: 'btn-red',
						action: function(){

							if( mode == "invoice" || mode == "pay" || mode == "import_declaration" ){
								var a_mode = "orderSheet_pay_file_del";
							}

							$.ajax({
								url: "/ad/processing/order_sheet",
								data : { "a_mode" : a_mode, "idx" : idx, "smode" : mode, "filename" : filename  },
								type: "POST",
								dataType: "json",
								success: function(res){
									if ( res.success == true ){
										//toast2("success", "삭제완료", "삭제가 완료되었습니다.");
										//$(obj).parents( 'div' ).remove();

										if( openPage == "global" ){
											onlyAD.orderSheetReset(idx,'global');
										}else{
											orderSheet.osViewReset(idx);
										}

									}else{
										showAlert("Error", res.msg, "alert2" );
										return false;
									}
								},
								error: function(request, status, error){
									console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
									showAlert("Error", "에러", "alert2" );
									return false;
								},
								complete: function() {
									$(obj).attr('disabled', false);
								}
							});

						}
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});

		},

		//결제기한
		approvalPayment : function( idx, ap_mode, reg_mode, calendar_idx ) {
		
			// 배송비 결제기한
			if( ap_mode == "express" ){
				
				if( !$("#express_price").val() || $("#express_price").val() == "0" ){
					$("#express_price").addClass('input-point-ani').focus();
					showAlert("Error", "배송비를 입력해주세요.", "alert2" );
					return false;
				}

				if( !$("#expressApprovalPayment_date").val() ){
					$("#expressApprovalPayment_date").addClass('input-point-ani').focus();
					showAlert("Error", "배송비 결제기한 날짜를 입력해주세요.", "alert2" );
					return false;
				}

				var _date = $("#expressApprovalPayment_date").val();
				var _price = GC.uncomma($("#express_price").val());

			}else if( ap_mode == "tax" ){

				if( !$("#tex_duty_price").val() || $("#tex_duty_price").val() == "0" ){
					$("#tex_duty_price").addClass('input-point-ani').focus();
					showAlert("Error", "관세를 입력해주세요.<br>관/부가세를 모두 입력하셔야 합니다.", "alert2" );
					return false;
				}

				if( !$("#tex_vat_price").val() || $("#tex_vat_price").val() == "0" ){
					$("#tex_vat_price").addClass('input-point-ani').focus();
					showAlert("Error", "부가세를 입력해주세요.<br>관/부가세를 모두 입력하셔야 합니다.", "alert2" );
					return false;
				}

				if( !$("#texApprovalPayment_date").val() ){
					$("#texApprovalPayment_date").addClass('input-point-ani').focus();
					showAlert("Error", "관/부가세 결제기한 날짜를 입력해주세요.", "alert2" );
					return false;
				}

				var _date = $("#texApprovalPayment_date").val();
				var _price = GC.uncomma($("#tex_duty_price").val()) + GC.uncomma($("#tex_vat_price").val());

			}


			

			if( reg_mode == "new" ){
				var data = { 
					"a_mode" : "ApprovalPayment", 
					"calendar_a_mode" : "new",
					"ap_mode" : ap_mode,
					"idx" : idx, 
					"date" : _date,
					"price" : _price,
				};
			}else{
				var data = { 
					"a_mode" : "ApprovalPayment", 
					"calendar_a_mode" : "modify",
					"calendar_idx" : calendar_idx,
					"ap_mode" : ap_mode,
					"idx" : idx, 
					"date" : _date,
					"price" : _price,
				};
			}

			$.ajax({
				url: "/ad/processing/order_sheet",
				data : data,
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						toast2("success", "결제기한 설정", "결제기한이 저장되었습니다.<br>캘린더에 등록되었습니다.");
						if( openPage == "global" ){
							onlyAD.orderSheetReset(idx,'global');
						}else{
							orderSheet.osViewReset(idx);
						}
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					//$(obj).attr('disabled', false);
				}
			});

		},

		// 캘린더 완료처리
		calendarOk : function( idx, ap_mode, calendar_idx ) {

			$.confirm({
				boxWidth : '500px',
				useBootstrap : false,
				icon: 'fas fa-exclamation-triangle',
				title: '캘린더 완료처리 하시겠습니까?',
				content: '※주의 완료처리를 하면 기존에 작성중인 내용이 사라집니다.<br>작성중인 내용이 있다면 저장을 먼저 해주세요',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '캘린더 완료처리 하기',
						btnClass: 'btn-red',
						action: function(){

							$.ajax({
								url: "/ad/processing/order_sheet",
								data: { "a_mode":"calendarOk", "idx":idx, "ap_mode":ap_mode, "calendar_idx":calendar_idx },
								type: "POST",
								dataType: "json",
								success: function(res){
									if (res.success == true ){
										toast2("success", "캘린더 완료", "캘린더 완료처리되었습니다.");
										
										if( openPage == "global" ){
											onlyAD.orderSheetReset(idx,'global');
										}else{
											orderSheet.osViewReset(idx);
										}		

									}else{
										showAlert("Error", res.msg, "alert2" );
										return false;
									}
								},
								error: function(request, status, error){
									console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
									showAlert("Error", "에러", "alert2" );
									return false;
								},
								complete: function() {
									//$(obj).attr('disabled', false);
								}
							});

						}
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});
		},

		//결제기한 캘린더 삭제
		calendarDel : function( idx, ap_mode, calendar_idx ) {

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제 하시겠습니까?',
				content: '캘린더에 등록된 내용만 삭제합니다.<br>캘린더에서 삭제되도 다시 캘린더로 등록할 수 있습니다.',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '확인',
						btnClass: 'btn-red',
						action: function(){

							$.ajax({
								url: "/ad/processing/order_sheet",
								data: { "a_mode":"calendarDel", "idx":idx, "ap_mode":ap_mode, "calendar_idx":calendar_idx },
								type: "POST",
								dataType: "json",
								success: function(res){
									if (res.success == true ){
										toast2("success", "캘린더 삭제", "캘린더가 삭제되었습니다.");
										
										if( openPage == "global" ){
											onlyAD.orderSheetReset(idx,'global');
										}else{
											orderSheet.osViewReset(idx);
										}

									}else{
										showAlert("Error", res.msg, "alert2" );
										return false;
									}
								},
								error: function(request, status, error){
									console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
									showAlert("Error", "에러", "alert2" );
									return false;
								},
								complete: function() {
									//$(obj).attr('disabled', false);
								}
							});

						}
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});

		},

		// 수정
		save : function(obj, savemode) {

			//$(obj).attr('disabled', true);

			var formData = $("#form1").serializeArray();

			$.ajax({
				url: "/ad/processing/order_sheet",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						
						<?  if( $_idx ){ ?>
							if( savemode == "stay" ){
								toast2("success", "주문서 수정", "주문서가 수정 변경 되었습니다.");
								orderSheet.osViewReset('<?=$_idx?>');
							}else{
								location.reload();
							}

						<? }else{ ?>
							location.href='/ad/order/order_sheet/'+res.key;
						<? } ?>

						<? 
								/*
						if( $_openmode == "main" ){ ?>
							alert("등록되었습니다.");
						<? }else{ ?>
						
							<? 

								if( $_idx ){ ?>
								toast2("success", "주문서 수정", "주문서가 수정 변경 되었습니다.");
								//orderSheet.osViewReset('<?=$_idx?>');
								location.reload();
							<? }else{ ?>
								location.href='/ad/order/order_sheet/'+res.key;
							<? 
								}

							?>
						
						<? } 
													*/	
						?>

					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					//$(obj).attr('disabled', false);
				}
			});

		}
	};

}();

$(function(){

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}

});
//--> 
</script> 