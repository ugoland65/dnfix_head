<?
// 변수 초기화
$prd_data = [];
$_cd_weight_data = [];
$_cd_weight_1 = "";
$_cd_weight_2 = "";
$_cd_weight_3 = "";
$_cd_size_fn_data = [];
$_cd_price_data = [];
$_cd_cost_price_info_data = [];
$_order_invoice_price_data = [];
$_order_price_data = [];

if( $_prd_idx ){
	$prd_data = sql_fetch_array(sql_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_prd_idx."' "));
	
	// 배열 검증
	if (!is_array($prd_data)) {
		$prd_data = [];
	}

	$_cd_weight_data = json_decode($prd_data['cd_weight_fn'] ?? '{}', true);
	if (!is_array($_cd_weight_data)) {
		$_cd_weight_data = [];
	}
	$_cd_weight_1 = $_cd_weight_data['1'] ?? "";
	$_cd_weight_2 = $_cd_weight_data['2'] ?? "";
	$_cd_weight_3 = $_cd_weight_data['3'] ?? "";

	$_cd_size_fn_data = json_decode($prd_data['cd_size_fn'] ?? '{}', true);
	if (!is_array($_cd_size_fn_data)) {
		$_cd_size_fn_data = [];
	}

	$_cd_price_data = json_decode($prd_data['cd_price_fn'] ?? '{}', true);
	if (!is_array($_cd_price_data)) {
		$_cd_price_data = [];
	}
	
	$_cd_cost_price_info_data = json_decode($prd_data['cd_cost_price_info'] ?? '{}', true);
	if (!is_array($_cd_cost_price_info_data)) {
		$_cd_cost_price_info_data = [];
	}

	foreach ( $_cd_price_data as $key => $val ){
		if( $key == "invoice" ){
			$_order_invoice_price_data = $val;
		}else{
			$_order_price_data[$key] = $val;
		}
	}
}


/*
	echo "<pre>";
	print_r($_order_invoice_price_data);
	echo "</pre>";

	echo "<pre>";
	print_r($_cd_price_data);
	echo "</pre>";

	echo "<pre>";
	print_r($_cd_cost_price_info_data);
	echo "</pre>";
*/

	if( !($_cd_cost_price_info_data['VAT'] ?? '') ) $_cd_cost_price_info_data['VAT']  = "포함";

?>

<style type="text/css">
.table-style tr th{ text-align:center; }
#standard_order_price{ display:none; }
#cost_calculation_info{ display:none; }
.table-style td.none-bg{ background-color:#dddddd; border:none !important; padding: 0 !important; }
/*
.button-wrap{ display:none; }
*/

.cost-calculation-info{ background-color:#333; border:1px solid #000; border-radius:5px; padding:15px 25px; color:#eee; }
.cost-calculation-info p{ margin:3px 0; } 
.cost-calculation-info p.notice{ color:#ffef40; }
.cost-calculation-info b{ color:#afdaff; }

.calculation-show-box{  background-color:#000; border:1px solid #444; border-radius:5px; padding:8px 15px; color:#eee;  }
.calculation-show-box > ul{ padding:3px 5px; }
.calculation-show-box > ul b.point2{ color:#99ffce; font-size:14px; }

.price-f-box input{ background-color:#000; color:#afdaff; border:1px solid #333; }
</style>

<div class="crm-title">
	<h3>가격 정보</h3>
</div> 

<form id="form1">
<input type="hidden" name="idx" value="<?=$_prd_idx?>">
<table class="table-style border01 width-full">
	<tr>
		<th style="width:140px;">수입국가</th>
		<td >

		<?
		if (!isset($_arr_national)) {
			$_arr_national = [];
		}
		for ($i=0; $i<count($_arr_national); $i++){
			if (!is_array($_arr_national[$i])) continue;
		?>
		<label><input type="radio" name="cd_national" value="<?=$_arr_national[$i]['code'] ?? ''?>" <? if( ($prd_data['cd_national'] ?? '') == ($_arr_national[$i]['code'] ?? '') ) echo "checked"; ?> onclick="prdInfoPrice.costCalculation()"> <?=$_arr_national[$i]['name'] ?? ''?>(<?=$_arr_national[$i]['code'] ?? ''?>)</label>
		<? } ?>

			<?
			/*
			<label><input type="radio" name="cd_national" value="jp" <? if( $prd_data['cd_national'] == "jp" ) echo "checked"; ?> onclick="prdInfoPrice.costCalculation()"> 일본</label>
			<label><input type="radio" name="cd_national" value="cn" <? if( $prd_data['cd_national'] == "cn" ) echo "checked"; ?> onclick="prdInfoPrice.costCalculation()"> 중국</label>
			<label><input type="radio" name="cd_national" value="kr" <? if( $prd_data['cd_national'] == "kr" ) echo "checked"; ?> onclick="prdInfoPrice.costCalculation()">  한국</label>
			<label><input type="radio" name="cd_national" value="dollar" <? if( $prd_data['cd_national'] == "dollar" ) echo "checked"; ?> onclick="prdInfoPrice.costCalculation()">  달러</label>
			*/
			?>

		</td>
	</tr>
	<tr>
		<th >중량</th>
		<td >
			상품중량 : <input type='text' name='cd_weight_1' id='cd_weight_1' style='width:80px;'  value="<?=$_cd_weight_1 ?? ''?>">
			전체중량 : <input type='text' name='cd_weight_2' id='cd_weight_2' style='width:80px;'  value="<?=$_cd_weight_2 ?? ''?>" onkeyUP="prdInfoPrice.costCalculation()">
			실측중량 : <input type='text' name='cd_weight_3' id='cd_weight_3' style='width:80px;'  value="<?=$_cd_weight_3 ?? ''?>" onkeyUP="prdInfoPrice.costCalculation()" > 
			<div class="admin-guide-text">
				- 단위 g (숫자만 등록할것)
			</div>
		</td>
	</tr>
	<tr>
		<th>포장 사이즈</th>
		<td>
			가로(W) : <input type='text' name='invoice_size_w' value="<?=$_cd_size_fn_data['invoice']['W'] ?? ''?>" style="width:60px">
			세로(H) : <input type='text' name='invoice_size_h' value="<?=$_cd_size_fn_data['invoice']['H'] ?? ''?>" style="width:60px">
			깊이(D) : <input type='text' name='invoice_size_d' value="<?=$_cd_size_fn_data['invoice']['D'] ?? ''?>" style="width:60px">
			&nbsp;&nbsp;
			CBM : <input type='text' name='invoice_size_cbm' id='invoice_size_cbm' value="<?=$_cd_size_fn_data['invoice']['cbm'] ?? ''?>" style="width:60px">
			<input type="checkbox" name="invoice_size_cbm_mode" value="hand" <?if (($_cd_size_fn_data['invoice']['cbm_mode'] ?? '') == "hand" ) echo "checked"; ?>> CBM 수동입력
			<div class="admin-guide-text">
				- 단위 mm (숫자만 등록할것)
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="none-bg" style="height:10px;"></td>
	</tr>

	<tr>
		<th >쑈당몰 판매가</th>
		<td >
			<input type='text' name='cd_sale_price' value="<?=number_format($prd_data['cd_sale_price'] ?? 0)?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;' > 원
			
			<? 
				if( ($prd_data['cd_sale_price'] ?? 0) > 0 && ($prd_data['cd_cost_price'] ?? 0) > 0 ){ 
			?>
				| 마진 : <b><?=number_format($prd_data['cd_sale_price'] - $prd_data['cd_cost_price'])?></b>
				( <b><?=round( ($prd_data['cd_sale_price'] - $prd_data['cd_cost_price']) / $prd_data['cd_sale_price'] * 100, 2)?></b> % )
				<? if( $prd_data['cd_sale_price'] > 29999 ){ ?>
					| 3만 무배 마진 : <b><?=number_format($prd_data['cd_sale_price'] - ($prd_data['cd_cost_price'] + 2500))?></b> 
					( <b style="color:#ff0000"><?=round( ($prd_data['cd_sale_price'] - ($prd_data['cd_cost_price'] + 2500) ) / $prd_data['cd_sale_price'] * 100, 2)?></b> % )
				<? } ?>
			<? } ?>

		</td>
	</tr>

	<tr>
		<th >원가</th>
		<td >
			<div>
				<input type='text' name="cd_cost_price" id="cd_cost_price" value="<?=number_format($prd_data['cd_cost_price'] ?? 0)?>" onkeyUP="GC.commaInput( this.value, this );" style='width:100px;' > 원
				<label class="m-l-20"><input type="radio" name="cd_cost_price_vat" value="포함" <? if( ($_cd_cost_price_info_data['VAT'] ?? '') == "포함" ) echo "checked"; ?>> VAT 포함</label>
				<label><input type="radio" name="cd_cost_price_vat" value="미포함" <? if( ($_cd_cost_price_info_data['VAT'] ?? '') == "미포함" ) echo "checked"; ?>> VAT 미포함</label>
			</div>
			<div class="m-t-6">
				<textarea name="cd_cost_price_memo" style="height:80px;" placeholder="원가 메모"><?=$prd_data['cd_cost_price_memo'] ?? ''?></textarea>
			</div>
		</td>
	</tr>

	<tr>
		<th >주문서 가격</th>
		<td >
			<div>
			<?
			foreach ( $_order_price_data as $key => $val ){
				if( $key ){
			?>
				<ul>
					<label>
						<input type="radio" name="order_price_code" id="order_price_code" value="<?=$key?>" <? if( $key == ($_cd_cost_price_info_data['기준주문가코드'] ?? '') ) echo "checked"; ?> onclick="prdInfoPrice.orderPriceChange('<?=$val?>')">
						<b><?=$key?></b> : (<?=number_format($val,2)?>) 
						<? if( $_order_invoice_price_data[$key] ?? '' ){?>
							| invoice 다운 : <b><?=$_order_invoice_price_data[$key]?></b>
						<? } ?>
					</label>
				</ul>
			<? } } ?>
			</div>
		</td>
	</tr>

	<tr>
		<th >원가 계산기</th>
		<td >

			<div id="cost_cal_msg" style="display:none; color:#ff0000; font-size:17px;">[주문종류]를 선택해주세요.</div>
		
			<? if( $_cd_size_fn_data['invoice']['cbm'] ?? '' ) { ?>
			<div class="p-b-6">
				CBM : <?=$_cd_size_fn_data['invoice']['cbm']?> x 1.25 = (<?=($_cd_size_fn_data['invoice']['cbm']*1.25)?>) /
				<?=($_cd_size_fn_data['invoice']['cbm']*1.25)?> x 88,000 = <b><?=number_format(($_cd_size_fn_data['invoice']['cbm']*1.25)*88000)?></b>
				(해운 예상 배송비)
			</div>
			<? } ?>

			<div>
				<select name="cost_cal_kind" id="cost_cal_kind" class="m-r-10" onchange="prdInfoPrice.costCalKindChange(this.value)">
					<option value="">주문종류</option>
					<option value="중국주문" <? if( ($_cd_cost_price_info_data['주문종류'] ?? '') == "중국주문" ) echo "selected"; ?>>중국주문</option>
					<option value="일본주문" <? if( ($_cd_cost_price_info_data['주문종류'] ?? '') == "일본주문" ) echo "selected"; ?>>일본주문</option>
				</select>

				주문가 : <input type="text" name="cost_cal_price" id="cost_cal_price" class="width-80 m-r-10" value="<?=$_cd_cost_price_info_data['주문가'] ?? ''?>" onkeyUP="prdInfoPrice.costCalculationNew()">
				적용환율 : <input type="text" name="cost_cal_exchange_rate" id="cost_cal_exchange_rate" class="width-50 m-r-10" value="<?=$_cd_cost_price_info_data['적용환율'] ?? ''?>" onkeyUP="prdInfoPrice.costCalculationNew()">
				<span id="cost_cal_kind_delivery_text">
					<? if( ($_cd_cost_price_info_data['주문종류'] ?? '') == "중국주문" ){ ?>
						개당 배송비
					<? }elseif( ($_cd_cost_price_info_data['주문종류'] ?? '') == "일본주문" ){ ?>
						kg당 배송비
					<? }else{ ?>
						배송비
					<? } ?>
				</span> : <input type="text" name="cost_cal_delivery" id="cost_cal_delivery" class="width-80" value="<?=$_cd_cost_price_info_data['배송비'] ?? ''?>" onkeyUP="prdInfoPrice.costCalculationNew()" >

				<!-- <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="alert('준비중');" >해외주문 기본값 설정</button> -->

			</div>
			<div class="m-t-6">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdInfoPrice.costCalculationNew()" >원가 계산기 실행</button>
			</div>

			<div id="cost_calculation_info_new" class="cost-calculation-info m-t-7">
				<div class="price-f-box" >
					<?
						if( $_cd_cost_price_info_data['관세율'] ?? '' ){
							$_this_cost_cal_tariff = $_cd_cost_price_info_data['관세율'];
						}else{
							$_this_cost_cal_tariff = "6.5";
						}

						if( $_cd_cost_price_info_data['부대비용'] ?? '' ){
							$_this_cal_incidental_cost = $_cd_cost_price_info_data['부대비용'];
						}else{
							$_this_cal_incidental_cost = "1000";
						}
					?>
					관세율 : <input type="text" name="cost_cal_tariff" id="cost_cal_tariff" class="width-50" value="<?=$_this_cost_cal_tariff?>">% 
					<span id="incidental_cost_box" style="<? if( ($_cd_cost_price_info_data['주문종류'] ?? '') == "일본주문" ){ ?>display:none;<? } ?>">
					&nbsp; | &nbsp;
					부대비용 (B/L 문서, 통관수수료, 원산지증명) : <input type="text" name="cost_cal_incidental_cost" id="cost_cal_incidental_cost" class="width-50" value="<?=$_this_cal_incidental_cost?>">원
					</span>

					<!-- 부가세율 :  <input type="text" name="cost_cal_vat" id="cost_cal_vat" class="width-50" value="11">% -->
				</div>
				<div class='calculation-show-box m-t-5' id="cost_calculation_detail">
				</div>
			</div>

		</td>
	</tr>

	<tr>
		<th >판매가 계산기</th>
		<td >
			<div>
				적용할 원가가격 : <input type="text" name="estimated_selling_price" id="estimated_selling_price" value="<?=number_format($prd_data['cd_cost_price'] ?? 0)?>" onkeyUP="GC.commaInput( this.value, this ); prdInfoPrice.salePriceCalculation();" class="width-80 m-r-10" >
			</div>
			<div class="m-t-6">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdInfoPrice.salePriceCalculation()" >판매가 계산기 실행</button>
			</div>
			<div class="cost-calculation-info m-t-7">
				<div id="estimated_selling_price_result" class="calculation-show-box">
				</div>
			</div>
		</td>
	</tr>

	<tr>
		<th >책정 원가<br>(구버전)<br>신버전 사용바람</th>
		<td >
			
			<!--  -->
			<div id="standard_order_price" class="m-t-7">
				
				<table class="table-style border01 width-full">
					<tr>
						<th class="text-center" style="width:120px;">기준 주문가</th>
						<td>
						<?
						foreach ( $_order_price_data as $key => $val ){
							if( $key ){
						?>
							<label><input type="radio" name="order_price" id="order_price_<?=$key?>" value="<?=$key?>" data-price="<?=$val?>" 
								<? if( $key == ($_cd_cost_price_info_data['기준주문가코드'] ?? '') ) echo "checked"; ?> 
								onclick="prdInfoPrice.costCalculation()"> <?=$key?> (<?=number_format($val,2)?>)</label>
						<? } } ?>
							
						</td>
					</tr>
				</table>
			</div>

			<div id="cost_calculation_info" class="cost-calculation-info m-t-7">
			</div>

		</td>
	</tr>

</table>
</form>

<style type="text/css">
	.button-wrap-back{ height:60px; }
	.button-wrap{ width:calc(100% - 205px); height:60px; line-height:60px; text-align:center; background:rgba(0,0,0,.4); border-top:1px solid #000; position:fixed; bottom:0; right:0;  }
</style>
<div class="button-wrap-back">
</div>
<div class="button-wrap">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdInfoPrice.costSave(this);" >수 정</button>
</div>


<script type="text/javascript"> 
<!-- 
var prdInfoPrice = function() {

	var yen = <?=$yen ?? 1000?>;
	var yen_cn = <?=$yen_cn ?? 193?>;
	var kg_p = <?=$kg_p ?? 6000?>;
	var cn_delivery_p = <?=$delivery_p_cn ?? 2800?>;

	var costCalMsg = function(msg) {
		$("#cost_cal_msg").show().html(msg);
	};

	return {

		init : function() {

		},
		
		changeValue : function(mode,v) { //prdInfoPrice.changeValue
			if( mode == "yen_cn" ){
				yen_cn = v;
			}else if( mode == "cn_delivery_p" ){
				cn_delivery_p = GC.uncomma(v);
			}
			prdInfoPrice.costCalculation();
		},

		//
		orderPriceChange : function(v) {
			$("#cost_cal_price").val(v);
			prdInfoPrice.costCalculationNew();
		},

		//신버전 원가계산기
		costCalculationNew : function() {

			var carMsgCk = "ok";
			var carMsgCkMsg = "";

			var costCalKind = $("#cost_cal_kind").val();
			var costCalPrice = $("#cost_cal_price").val();
			var costCalExchangeRate = $("#cost_cal_exchange_rate").val();

			if( !costCalKind ){ carMsgCk = "no"; carMsgCkMsg += "[주문종류]를 선택해주세요."; }
			if( !costCalPrice ){ carMsgCk = "no";  /*$("#cost_cal_price").focus(); */ carMsgCkMsg += "<br>[주문가]를 입력해주세요.";  }
			if( !costCalExchangeRate ){ carMsgCk = "no";  /* $("#cost_cal_exchange_rate").focus(); */ carMsgCkMsg += "<br>[적용환율]를 입력해주세요.";  }

			var tariffRate = $("#cost_cal_tariff").val();
			if( !tariffRate ){ carMsgCk = "no"; carMsgCkMsg += "<br>관세율 값을 입력해주세요."; }

			if( carMsgCk == "no" ){
				costCalMsg(carMsgCkMsg);
			}else{
				$("#cost_cal_msg").hide();
			}

			var _html = "";

			var op_won = costCalPrice * costCalExchangeRate; //원전환
			var tariff_p = Math.round(op_won*(tariffRate/100),0); //관세
			var vat_p = Math.round((op_won + tariff_p)*0.1,0); //부가세
			var sum_tv = tariff_p + vat_p;

			var incidentalCost = $("#cost_cal_incidental_cost").val()*1;
			var costCalDelivery = $("#cost_cal_delivery").val()*1;

			var plus1 = "";
			var plus2 = "";

			/*
			if( incidentalCost > 0 ){
				sum_tv = sum_tv + incidentalCost;
				sum_tv2 = sum_tv2 + incidentalCost;
				plus1 = " | 부대비용 : <b>"+ GC.comma(incidentalCost) +"</b>";
			}
			*/

			if( costCalKind == "중국주문"){

				var invoiceSizeCBM = $("#invoice_size_cbm").val();
				if( !invoiceSizeCBM ){ alert("포장 사이즈의 [CBM]값을 입력해주세요."); return false; }

				/*
				var vatRate = $("#cost_cal_vat").val();
				if( !vatRate ){ alert("부가세율 값을 입력해주세요."); return false; }
				*/
			
				//var op_won2 = (op_won*0.35); //원전환
				var op_won2 = (costCalPrice*0.65) * costCalExchangeRate; //원전환
				//costCalPrice * costCalExchangeRate; //원전환
				var tariff_p2 = Math.round(op_won2*(tariffRate/100),0); //관세2
				var vat_p2 = Math.round((op_won2 + tariff_p2)*0.1,0); //부가세2
				var sum_tv2 = tariff_p2 + vat_p2;
			
				if( incidentalCost > 0 ){
					sum_tv = sum_tv + incidentalCost;
					sum_tv2 = sum_tv2 + incidentalCost;
					plus1 = " | 부대비용 : <b>"+ GC.comma(incidentalCost) +"</b>";
				}

				if( costCalDelivery > 0 ){
					sum_tv = sum_tv + costCalDelivery;
					sum_tv2 = sum_tv2 + costCalDelivery;
					plus2 = " | 배송비 : <b>"+ GC.comma(costCalDelivery) +"</b>";
				}

				var sum_otv = op_won + sum_tv;
				var sum_otv2 = op_won + sum_tv2;

				_html += "<ul>(₩)원전환 : ￥<b>"+ GC.comma(costCalPrice) + "</b> -> <b class='point2'>"+ GC.comma(op_won) + "</b>원 ( 적용환율 : <b>"+ costCalExchangeRate +"</b> )</ul>";
				_html += "<ul>정산신고 | 관세 : <b>"+ GC.comma(tariff_p)+"</b> | 부가세 : <b>"+GC.comma(vat_p)+"</b>"+ plus1 + plus2 +" = 합 : <b>"+GC.comma(sum_tv)+"</b>";
				_html += " | 총합 : <b class='point2'>"+ GC.comma(sum_otv) + "</b>원</ul>";
				_html += "<ul>다운신고(35%) | 관세 : <b>"+ GC.comma(tariff_p2)+"</b> | 부가세 : <b>"+GC.comma(vat_p2)+"</b>"+ plus1 + plus2 +" = 합 : <b>"+GC.comma(sum_tv2)+"</b>";
				_html += " | 총합 : <b class='point2'>"+ GC.comma(sum_otv2) + "</b>원</ul>";

			}else if( costCalKind == "일본주문"){

				if( !costCalDelivery ){ $("#cost_cal_delivery").focus(); alert("배송비 값을 입력해주세요. 배송비값은 kg당 배송비 입니다."); return false; }

				var _weight_2 = $("#cd_weight_2").val();
				var _weight_3 = $("#cd_weight_3").val();

				/*
				if( !_weight_3 ){ alert("실측중량 정보가 없습니다."); return false; }
				*/			
				var weight = "";
				if( _weight_3 == "" && _weight_2 > 0 ){
					
					weight = _weight_2;
					_html += "<input type='hidden' name='weight_mode' value='전체중량' >";
					_html += "<input type='hidden' name='weight' value='"+ _weight_2 +"' >";
				
				}else if( _weight_3 > 0 ){

					weight = _weight_3;
					_html += "<input type='hidden' name='weight_mode' value='실측중량' >";
					_html += "<input type='hidden' name='weight' value='"+ _weight_3 +"' >";

				}

				if( costCalDelivery > 0 ){

					var delivery_p  = Math.round(weight * (costCalDelivery * 0.001),0); // 배송비
					
					vat_p = Math.round((op_won + tariff_p + delivery_p)*0.1,0); //부가세
					sum_tv = tariff_p + vat_p + delivery_p;
					plus2 = " | 배송비 : <b>"+ GC.comma(delivery_p) +"</b>( kg/"+ GC.comma(costCalDelivery) + " )";
				}


				var sum_otv = op_won + sum_tv;

				_html += "<ul>(₩)원전환 : ￥<b>"+ GC.comma(costCalPrice) + "</b> -> <b class='point2'>"+ GC.comma(op_won) + "</b>원 ( 적용환율 : <b>"+ costCalExchangeRate +"</b> )</ul>";
				_html += "<ul>관세 : <b>"+ GC.comma(tariff_p)+"</b> | 부가세 : <b>"+GC.comma(vat_p)+"</b>"+ plus1 + plus2 +" = 합 : <b>"+GC.comma(sum_tv)+"</b></ul>";
				_html += "<ul>총합 : <b class='point2'>"+ GC.comma(sum_otv) + "</b>원</ul>";
				_html += "<ul><button type='button' id='' class='btnstyle1 btnstyle1-xs' onclick='prdInfoPrice.goCostPrice("+ sum_otv +")' >총합 원가로 입력</button></ul>";

			}

			$("#cost_calculation_detail").html(_html);

		},

		//원가로 입력
		goCostPrice : function(v) {

			$("#cd_cost_price").val(GC.comma(v));
			$("#estimated_selling_price").val(GC.comma(v));
			prdInfoPrice.salePriceCalculation();

		},

		//주문종류 체인지
		costCalKindChange : function(v) {

			if( v == "중국주문" ){
				$("#cost_cal_kind_delivery_text").html('개당 배송비');
				$("#incidental_cost_box").show();
			}else if( v == "일본주문" ){
				$("#cost_cal_kind_delivery_text").html('kg당 배송비');
				$("#incidental_cost_box").hide();
			}else{
				$("#cost_cal_kind_delivery_text").html('배송비');
				("#incidental_cost_box").show();
			}

		},

		//판매가 계산기
		salePriceCalculation : function() {

			var estimatedSellingPrice = $("#estimated_selling_price").val();
			estimatedSellingPrice = GC.uncomma(estimatedSellingPrice);
			if( !estimatedSellingPrice ){ $("#estimated_selling_price").focus(); alert("판매할 원가를 입력해주세요."); return false;  }

			var inst_arr = [50,45,40,35,30,25,20,15,10,5];

			var _html = "";

			for (var i = 0; i < inst_arr.length; i++) {
				var inst_per = 1- (inst_arr[i]/100);
				var inst_value = Math.round(estimatedSellingPrice/inst_per);

				_html += "<ul>예상 판매가 ( "+ inst_arr[i] +"% ) <b>"+ GC.comma(inst_value)+"</b>원 | 마진 <b>"+ GC.comma(inst_value - estimatedSellingPrice)+"</b>원";
				if( inst_value >= 30000 ){
					_html += " | 3만무배 <b>"+ GC.comma((inst_value - estimatedSellingPrice)-2500)+"</b>원";
				}
				_html += "</ul>";

			} //for END

			$("#estimated_selling_price_result").html(_html);

		},

		//구버전 원가계산기
		costCalculation : function() {

			var _national = $(':input:radio[name=cd_national]:checked').val();
			var _weight_2 = $("#cd_weight_2").val();
			var _weight_3 = $("#cd_weight_3").val();
			var _html = "";


			// 수입국가가 일본, 중국일경우
			if( _national == "jp" || _national == "cn" ){
				
				//$(".button-wrap").hide();
				$("#standard_order_price").show();
				$("#cost_calculation_info").show();

				//일본
				if( _national == "jp" ){
					
					if( _weight_3 == "" ){
						_html += "<p class='notice'>※ 실측중량 정보가 없습니다.</p>";
					}

					var str = "";

					if( _weight_3 == "" && _weight_2 > 0 ){
						var weight = _weight_2;
						_html += "<p>※ 실측중량 정보가 없어 전체중량 ( <b>"+ _weight_2 +"</b>g )로 계산합니다.</p>";

						str += "<input type='hidden' name='weight_mode' value='전체중량' >";
						str += "<input type='hidden' name='weight' value='"+ _weight_2 +"' >";

					}else if( _weight_3 > 0 ){
						var weight = _weight_3;
						_html += "<p>- 계산중량 ( <b>"+ _weight_3 +"</b>g )</p>";

						str += "<input type='hidden' name='weight_mode' value='실측중량' >";
						str += "<input type='hidden' name='weight' value='"+ _weight_3 +"' >";

					}
				
				}
				
				var _order_price_key = $(':input:radio[name=order_price]:checked').val();
				
				if( _order_price_key == undefined ){
					_html += "<p class='notice'>※ [기준 주문가]를 선택해 주세요.</p>";
				}else{
					
					var _order_price = $("#order_price_"+_order_price_key).data("price");
					
					_html += "<p> - 기준 주문가 ( <b>"+ _order_price_key +"</b> : <b>"+ GC.comma(_order_price) +"</b> )</p>";

					//일본
					if( _national == "jp" ){

						_html += "<p> - 일본 환율 ( <b>"+ GC.comma(yen) +"</b> ) | 상품당 배송비 ( <b>"+ GC.comma(kg_p) +"</b> ) </p>";

						var op_won = _order_price * (yen/100); //원가 원전환
						var delivery_p  = weight * (kg_p * 0.001); // 배송비
						var tariff_p = Math.round(op_won*0.08,0); //관세
						var tariff_vat_p = Math.round((op_won + tariff_p)*0.1,0); //부가세

					// 중국
					}else if( _national == "cn" ){

						_html += '<div class="price-f-box" > - 중국 환율 : ';
						_html += '<input type="text" class="" name="1" value="'+ GC.comma(yen_cn) +'" style="width:60px;" onkeyUP="prdInfoPrice.changeValue(\'yen_cn\', this.value)"  />';
						_html += ' &nbsp;&nbsp; | &nbsp;&nbsp; ';
						_html += '상품당 배송비 : ';
						_html += '<input type="text" class="" name="2" value="'+ GC.comma(cn_delivery_p) +'" style="width:80px;" onkeyUP="prdInfoPrice.changeValue(\'cn_delivery_p\', this.value); " />';
						_html += '</div>';

						var op_won = _order_price * yen_cn; //원가 원전환
						var delivery_p  = cn_delivery_p; // 배송비
						var tariff_p = Math.round((op_won*0.3)*0.08,0); //관세
						var tariff_vat_p = Math.round((op_won + tariff_p)*0.11,0); //부가세

					}

						/*
						var tariff_p = Math.round(op_won*0.08,0); //관세
						var tariff_vat_p = Math.round((op_won + tariff_p)*0.1,0); //부가세
						*/

						var tax_p = tariff_p + tariff_vat_p + delivery_p;
						var cost_p = op_won + tax_p;



					str += "<div class='calculation-show-box m-t-5' >"
						+ "<ul>원가 계산</ul>"
						+ "<ul>(₩)원전환 : <b>"+ GC.comma(_order_price) + "</b>￥ -> <b class='point2'>"+ GC.comma(op_won) + "</b>원 </ul>"
						+ "<ul>관세(8%) : <b>"+ GC.comma(tariff_p)+"</b>원 /부가세 : <b>"+ GC.comma(tariff_vat_p)+"</b>원 / 배송비 : <b>"+GC.comma(delivery_p)+"</b>원"
						+ " = <b class='point2'>"+GC.comma(tax_p)+"</b>원</ul>"
						+ "<ul class='m-b-15'>원가 : <b class='point2'>"+GC.comma(cost_p)+"</b>원</ul>";

					var inst_arr = [50,45,40,35,30,25,20,15,10,5];

					/*
					var inst_value = cost_p/0.5;
					str += "<ul class='m-t-10'>예상 판매가 (50%) <b>"+ GC.comma(inst_value)+"</b>원 | 마진 <b>"+ GC.comma(inst_value - cost_p)+"</b>원";
					if( inst_value >= 30000 ){
						str += " | 3만무배 <b>"+ GC.comma((inst_value - cost_p)-2500)+"</b>원";
					}
					str += "</ul>";
					*/

					for (var i = 0; i < inst_arr.length; i++) {
						var inst_per = 1- (inst_arr[i]/100);
						var inst_value = Math.round(cost_p/inst_per);
						
						str += "<ul>예상 판매가 ( "+ inst_arr[i] +"% ) <b>"+ GC.comma(inst_value)+"</b>원 | 마진 <b>"+ GC.comma(inst_value - cost_p)+"</b>원";
						if( inst_value >= 30000 ){
							str += " | 3만무배 <b>"+ GC.comma((inst_value - cost_p)-2500)+"</b>원";
						}
						str += "</ul>";

						//str += "<ul>"+ inst_per +" / "+ inst_value +"</ul>";
					}

					str += "<input type='hidden' name='oprice' value='"+ _order_price +"' >"
						+ "<input type='hidden' name='oprice_key' value='"+ _order_price_key +"' >"
						+ "<input type='hidden' name='ex_rate' value='"+ yen +"' >"
						+ "<input type='hidden' name='kg_p' value='"+ kg_p +"' >"
						+ "<input type='hidden' name='tax' value='"+ tariff_p +"' >"
						+ "<input type='hidden' name='vat' value='"+ tariff_vat_p +"' >"
						+ "<input type='hidden' name='delivery' value='"+ delivery_p +"' >"
						+ "<input type='hidden' name='op_won' value='"+ op_won +"' >"
						+ "<input type='hidden' name='cost_p' value='"+ cost_p +"' >"

						+ "</div>";

					_html += str;

					//_html += "<button type='button' id='' class='btnstyle1 btnstyle1-primary btnstyle1-sm m-t-6' onclick='prdInfoPrice.costCalculationSave();' >계산된 원가 등록</button>";
				}

			}else if( _national == "kr" || _national == "dollar" ){
				
				//$(".button-wrap").show();
				$("#standard_order_price").hide();
				$("#cost_calculation_info").hide();

			}

			$("#cost_calculation_info").html(_html);
		
		},

		costCalculationSave : function() {

			var formData = $("#form1").serializeArray();
			formData.push({name: "a_mode", value: "costCalculationSave"});

			$.ajax({
				url: "/ad/processing/prd",
				data: formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "상품가격정보", "상품가격정보 변경완료");
						prdView.mode('price');
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

		costSave : function() {

			var formData = $("#form1").serializeArray();
			formData.push({name: "a_mode", value: "costSave"});

			$.ajax({
				url: "/ad/processing/prd",
				data: formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "상품가격정보", "상품가격정보 변경완료");
						prdView.mode('price');
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

	};

}();


prdInfoPrice.costCalculation();


<?
	if( ($_cd_cost_price_info_data['주문종류'] ?? '') && ($_cd_cost_price_info_data['주문가'] ?? '') && ($_cd_cost_price_info_data['적용환율'] ?? '') ){
?>
prdInfoPrice.costCalculationNew();
<? } ?>

<?
	if( $prd_data['cd_cost_price'] ?? 0 ){
?>
prdInfoPrice.salePriceCalculation();
<? } ?>
//--> 
</script> 