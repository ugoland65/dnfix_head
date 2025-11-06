<?
$pageGroup = "member";
$pageName = "member_info_popup";

include "../lib/inc_common.php";

$_ajax_mode = securityVal($ajax_mode);
$_oo_idx = securityVal($idx);

$oo_data = wepix_fetch_array(wepix_query_error("select * from ona_order where oo_idx = '".$_oo_idx."' "));

$_oo_price_data = json_decode($oo_data[oo_price_data], true);

$_oo_price_data_currency = $_oo_price_data[currency];
$_oo_price_data_pay_mode = $_oo_price_data[pay_mode];
$_oo_price_data_exchange_rate = $_oo_price_data[exchange_rate];
$_oo_price_data_exchange_charge = $_oo_price_data[exchange_charge];
$_oo_price_data_pay_date = $_oo_price_data[pay_date];
$_oo_price_data_change_price = $_oo_price_data[change_price];

$_oo_express_data = json_decode($oo_data[oo_express_data], true);

$_oo_express_data_express_mode = $_oo_express_data[mode];
$_oo_express_data_express_name = $_oo_express_data[name];
$_oo_express_data_express_number = $_oo_express_data[number];
$_oo_express_data_express_report_weight = $_oo_express_data[report_weight];
$_oo_express_data_express_weight = $_oo_express_data[weight];
$_oo_express_data_express_cbm = $_oo_express_data[cbm];
$_oo_express_data_express_box = $_oo_express_data[box];
$_oo_express_data_express_price = $_oo_express_data[price];
$_oo_express_data_express_price_add = $_oo_express_data[price_add];

$_oo_tex_data = json_decode($oo_data[oo_tex_data], true);

$_oo_tex_data_num = $_oo_tex_data[num];
$_oo_tex_data_report_price = $_oo_tex_data[report_price];
$_oo_tex_data_duty_price = $_oo_tex_data[duty_price];
$_oo_tex_data_vat_price = $_oo_tex_data[vat_price];
$_oo_tex_data_commission = $_oo_tex_data[commission];

$_oo_date_data = json_decode($oo_data[oo_date_data], true);

$_oo_date_data_in_date = $_oo_date_data[in_date];

$_oo_price_date = ( $oo_data[oo_price_date] > 0 ) ? $oo_data[oo_price_date] : "" ; 
$_oo_in_date = ( $oo_data[oo_in_date] > 0 ) ? $oo_data[oo_in_date] : "" ; 
$_oo_duty_due_date = ( $oo_data[oo_duty_due_date] > 0 ) ? $oo_data[oo_duty_due_date] : "" ; 
$_oo_duty_settlement_date = ( $oo_data[oo_duty_settlement_date] > 0 ) ? $oo_data[oo_duty_settlement_date] : "" ; 

$_oo_box = $oo_data[oo_box];
$_oo_box_weight = $oo_data[oo_box_weight]*1;
$_oo_box_weight_fix = $oo_data[oo_box_weight_fix]*1;
$_oo_express = $oo_data[oo_express];
$_oo_express_number = $oo_data[oo_express_number];
$_oo_express_price = ( $oo_data[oo_express_price] > 0 ) ? number_format($oo_data[oo_express_price]) : "" ; 
$_oo_express_price_date = ( $oo_data[oo_express_price_date] > 0 ) ? $oo_data[oo_express_price_date] : "" ; 
$_oo_express_price_settlement_date = ( $oo_data[oo_express_price_settlement_date] > 0 ) ? $oo_data[oo_express_price_settlement_date] : "" ; 

if( $_ajax_mode != "true" ){
	include "../layout/header_popup.php";
}
?>

<style type="text/css">
.price{ width:100px !important; }
.price.price_point{  font-weight:bold; color:#ff0000; }
.change-price{ display:table; }
.change-price > ul{ display:table-row; }
.change-price > ul > li{ display:table-cell; padding:0 3px 2px 0; }
.change-price .change-price-body{ width:300px; }
</style>

<form name='form1' id='form1'>
<input type="hidden" name="a_mode" value="orderSheetDetail">
<input type="hidden" name="modify_idx" value="<?=$oo_data[oo_idx]?>">

<div class="crm-detail-info">
	<table class="table-style">
		<tr>
			<th class="tds1">이름</th>
			<td class="tds2"><input type='text' name='oo_name' value="<?=$oo_data[oo_name]?>"></td>
		</tr>

		<tr>
			<th class="tds1">주문금액</th>
			<td class="tds2">

				<div>
					<input type='text' name='oo_sum_price' class="price" value="<?=number_format($oo_data[oo_sum_price])?>" >
					<select name="currency">
						<option value="엔" <? if( $_oo_price_data_currency == "엔" ) echo "selected"; ?>>엔</option>
						<option value="원" <? if( $_oo_price_data_currency == "원" ) echo "selected"; ?>>원</option>
						<option value="위안" <? if( $_oo_price_data_currency == "위안" ) echo "selected"; ?>>위안</option>
						<option value="달러" <? if( $_oo_price_data_currency == "달러" ) echo "selected"; ?>>달러</option>
					</select>
				</div>

				<div class="m-t-5"><button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="orderSheetView.addChangePrice();" >주문 변동금액 추가</button></div>
				<div id="change_price" class="change-price m-t-5">
<?
for ($i=0; $i<count($_oo_price_data_change_price); $i++){

	$_change_price_mode = $_oo_price_data_change_price[$i]['mode'];
	$_change_price_body = $_oo_price_data_change_price[$i]['body'];
	$_change_price_price = $_oo_price_data_change_price[$i]['price'];
?>
					<ul>
						<li>
							<select name="change_price_mode[]">
								<option value="할인" <? if( $_change_price_mode == "할인" ) echo "selected"; ?>>할인</option>
								<option value="추가" <? if( $_change_price_mode == "추가" ) echo "selected"; ?>>추가</option>
							</select>
						<li>
						<li><input type='text' name='change_price_body[]' class= 'change-price-body' value="<?=$_change_price_body?>" placeholder="사유"><li>
						<li><input type='text' name='change_price_price[]' class="price" placeholder="금액" value="<?=number_format($_change_price_price)?>"><li>
						<li><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="orderSheetView.delChangePrice(this)" ><i class="fas fa-minus-circle"></i> 삭제</button><li>
					</ul>
<? } ?>

				</div>
				<div>
					확정 주문 금액 : <input type='text' name='oo_fn_price' class="price" value="<?=number_format($oo_data[oo_fn_price])?>" >
				</div>
			</td>
		</tr>

		<tr>
			<th class="tds1">송금정보</th>
			<td class="tds2">		
				<div>
					<select name="pay_mode">
						<option value="일반 계좌송금" <? if( $_oo_price_data_pay_mode == "일반 계좌송금" ) echo "selected"; ?>>일반 계좌송금</option>
						<option value="하나은행 송금" <? if( $_oo_price_data_pay_mode == "하나은행 송금" ) echo "selected"; ?>>하나은행 송금</option>
						<option value="모인 - 장철영 계정" <? if( $_oo_price_data_pay_mode == "모인 - 장철영 계정" ) echo "selected"; ?>>모인 - 장철영 계정</option>
						<option value="모인 - 권윤호 계정" <? if( $_oo_price_data_pay_mode == "모인 - 권윤호 계정" ) echo "selected"; ?>>모인 - 권윤호 계정</option>
						<option value="달러 - 권윤호 카드" <? if( $_oo_price_data_pay_mode == "달러 - 권윤호 카드" ) echo "selected"; ?>>달러 - 권윤호 카드</option>
						<option value="달러 - 회사 카드" <? if( $_oo_price_data_pay_mode == "달러 - 회사 카드" ) echo "selected"; ?>>달러 - 회사 카드</option>
					</select>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					송금액 : <input type='text' name='oo_price_kr' class="price price_point" value="<?=number_format($oo_data[oo_price_kr])?>"  style='width:100px;' >원
				</div>
				<div class="m-t-5">
					환율 : <input type='text' name='exchange_rate' value="<?=$_oo_price_data_exchange_rate?>" style='width:50px;' >
					&nbsp;&nbsp;
					송금수수료 : <input type='text' name='exchange_charge' value="<?=number_format($_oo_price_data_exchange_charge)?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);" style='width:100px;' >원
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					송금일 : <div class="calendar-input" style="display:inline-block;"><input type='text' name='pay_date'  value="<?=$_oo_price_data_pay_date?>" ></div>
				</div>
			</td>
		</tr>

		<tr>
			<th class="tds1">배송</th>
			<td class="tds2">

				<table class="table-style">
					<tr>
						<td>배송방식</td>
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
								<option value="중국 해운 이안로지스틱" <? if( $_oo_express_data_express_name == "중국 해운 이안로지스틱" ) echo "selected"; ?>>중국 해운 이안로지스틱</option>
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
							배송비 : <input type='text' name='express_price' class="price price_point" value="<?=number_format($_oo_express_data_express_price)?>"  style='width:100px;' >원 / 
							추가배송비(용달등) : <input type='text' name='express_price_add' class="price price_point" value="<?=number_format($_oo_express_data_express_price_add)?>"  style='width:100px;' >원
						</td>
					</tr>
				</table>

			</td>
		</tr>

		<tr>
			<th class="tds1">관부가세</th>
			<td class="tds2">
				<table class="table-style">
					<tr>
						<td>수입신고번호</td>
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
							관세 : <input type='text' name='tex_duty_price' class="price price_point" value="<?=number_format($_oo_tex_data_duty_price)?>"  style='width:100px;' >원 / 
							부가세 : <input type='text' name='tex_vat_price' class="price price_point" value="<?=number_format($_oo_tex_data_vat_price)?>"  style='width:100px;' >원
						</td>
					</tr>
					<tr>
						<td>관세사 수수료</td>
						<td>
							<input type='text' name='tex_commission' class="price price_point" value="<?=number_format($_oo_tex_data_commission)?>"  style='width:100px;' >원
						</td>
					</tr>
				</table>

			</td>
		</tr>




		<tr>
			<th class="tds1">입고</th>
			<td class="tds2">
				입고일 : <div class="calendar-input" style="display:inline-block;"><input type='text' name='in_date'  value="<?=$_oo_date_data_in_date?>" ></div>
			</td>
		</tr>

	</table>
</div> 

	</form> 

<div class="text-center m-t-10">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-submit" onclick="goSave();" > 
		<i class="far fa-check-circle"></i> 수정
	</button>
</div>

<script type="text/javascript"> 
<!-- 

if( $(".calendar-input input").length ){
	$(".calendar-input input").datepicker(clareCalendar);
}

var orderSheetView = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		addChangePrice : function() {

			var html = '<ul>'
							+ '<li>'
							+ '<select name="change_price_mode[]">'
							+ '<option value="할인">할인</option>'
							+ '<option value="추가">추가</option>'
							+ '</select>'
							+ '<li>'
							+ '<li><input type="text" name="change_price_body[]" class= "change-price-body" placeholder="사유"><li>'
							+ '<li><input type="text" name="change_price_price[]" class="price" placeholder="금액"><li>'
							+ '<li><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="orderSheetView.delChangePrice(this)" ><i class="fas fa-minus-circle"></i> 삭제</button><li>'
							+ '</ul>';

			$("#change_price").append(html);
		},
		delChangePrice : function(obj) {
			$(obj).parent().parent().remove();
		}
	};

}();

$(function(){
	GC.makeCommaInput('.price');
});

function goSave(){
	//$("#form1").submit();

	var formData = $("#form1").serializeArray();

	$.ajax({
		cache : false,
		url : "processing.order_sheet.php",
		type : 'POST', 
		data : formData, 
		success : function(res) {
			alert(res.msg);
		}, // success 
		error : function(res) {

		}
	}); // $.ajax */
}
//--> 
</script> 

<?
if( $_ajax_mode != "true" ){
	include "../layout/footer_popup.php";
}
exit;
?>