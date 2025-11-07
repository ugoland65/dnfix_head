<?
	// 변수 초기화
	$_oo_idx = $_GET['oo_idx'] ?? $_POST['oo_idx'] ?? "";
	$_oop_idx = $_GET['oop_idx'] ?? $_POST['oop_idx'] ?? "";
	$_form_view = $_GET['form_view'] ?? $_POST['form_view'] ?? "";
	
	$_select_json = [];
	$save_qty = [];
	
	$oo_data = sql_fetch_array(sql_query_error("select oo_state, oo_json, oo_false, oo_sum_exchange_rate from ona_order where oo_idx = '".$_oo_idx."' "));
	$oop_data = sql_fetch_array(sql_query_error("select * from ona_order_prd where oop_idx = '".$_oop_idx."' "));

	if (!is_array($oo_data)) {
		$oo_data = [];
	}
	if (!is_array($oop_data)) {
		$oop_data = [];
	}

	$form_view = $_form_view;
	

	$_oop_json_check_data = substr($oop_data['oop_data'] ?? '', 0, 1);
	if( $_oop_json_check_data == "[" ){
		$_oop_json = $oop_data['oop_data'] ?? '[]';
	}else{
		$_oop_json = '['.($oop_data['oop_data'] ?? '').']';
	}

	$_oop_jsondata = json_decode($_oop_json, true);
	if (!is_array($_oop_jsondata)) {
		$_oop_jsondata = [];
	}

	$_oop_code = $oop_data['oop_code'] ?? '';

	$_select_json3 = $oo_data['oo_json'] ?? '[]';
	$_select_json4 = json_decode($_select_json3, true);
	if (!is_array($_select_json4)) {
		$_select_json4 = [];
	}


	$_select_order_all = json_decode($oo_data['oo_json'] ?? '[]', true);
	if (!is_array($_select_order_all)) {
		$_select_order_all = [];
	}

	for ( $z=0; $z<count($_select_order_all); $z++ ){
		if( ($_select_order_all[$z]['bidx'] ?? '') == $_oop_idx ){
			$_select_json = $_select_order_all[$z]['selpd'] ?? [];
			break;
		}
	}

	if (!is_array($_select_json)) {
		$_select_json = [];
	}
	
	$_oprice_sum_qty = 0; //상품 총수량

	for ($i=0; $i<count($_select_json); $i++){
		
		$save_id = $_select_json[$i]['pidx'] ?? '';
		${"_save_data_".$save_id} = "ok";
		${"_save_data_memo_".$save_id} = $_select_json[$i]['memo'] ?? '';

		$_oprice_sum_qty = $_oprice_sum_qty + (int)($_select_json[$i]['qty'] ?? 0); //상품 총수량

		if( ($_select_json[$i]['false'] ?? false) == true ){
			${"_false_idx_".$save_id} = "ok";
			${"_false_qty_".$save_id} = $_select_json[$i]['qty'] ?? 0;
			${"_false_memo_".$save_id} =  $_select_json[$i]['memo'] ?? '';
		}
		$save_qty[$save_id] = $_select_json[$i]['qty'] ?? 0;

	}

	$_false_check_data = substr($oo_data['oo_false'] ?? '', 0, 1);
	if( $_false_check_data == "[" ){
		$_false_data = $oo_data['oo_false'] ?? '[]';
	}else{
		$_false_data = '['.($oo_data['oo_false'] ?? '').']';
	}
	//$_false_data = "[".$oo_data['oo_false']."]";
	$_false_json = json_decode($_false_data, true);
	if (!is_array($_false_json)) {
		$_false_json = [];
	}

	for ($z=0; $z<count($_false_json); $z++){
		$pidx = $_false_json[$z]['pidx'] ?? '';
		${"_false_idx_".$pidx} = "ok";
		${"_false_qty_".$pidx} = $_false_json[$z]['qty'] ?? 0;
		${"_false_memo_".$pidx} = $_false_json[$z]['memo'] ?? '';
		$save_qty[$pidx] = $_false_json[$z]['qty'] ?? 0;
	}

?>

<style type="text/css">
.number-point{ color:#ff0000; }
.unit-price-sum{ font-size:14px; font-weight:600; color:#021aff; }
.notice-box{ text-align:center; font-size:10px; }
.notice-box i{ font-size:16px; }

.group-state{ display:inline-block; font-size:11px; padding:5px 10px; border-radius:5px;  }
.group-state.normal{ background-color:#eee; border:1px solid #ddd; }
.group-state.ing{ background-color:#95f4ff; border:1px solid #0ed1e8; }
.group-state.end{ background-color:#ffcbcb; border:1px solid #f88080; }
</style>

<div class="ospl-wrap">
	<div class="ospl-top">
		<ul>

			<div>
				Group : <?=$_oop_idx ?? ''?> | <b><?=$oop_data['oop_code'] ?? ''?></b> <button type="button" id="" class="btnstyle1 btnstyle1-sm m-r-20" onclick="orderSheetForm.groupView('<?=$_oop_idx ?? ''?>')" >폼그룹 상품관리</button>
				
				<? if( $form_view == "hidden" ){ ?>
				<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="orderSheetDetail.prdListShow('<?=$_oo_idx ?? ''?>','<?=$_oop_idx ?? ''?>','show');">전체 상품보기</button>
				<? }else{ ?>
				<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="orderSheetDetail.prdListShow('<?=$_oo_idx ?? ''?>','<?=$_oop_idx ?? ''?>','hidden');">주문 상품만보기</button>
				<? } ?>

				<div id="group_state" class="m-l-20 group-state normal">state : 보기중</div>
				<!-- 
				<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="orderSheet.lastInfoReset(this, '<?=$_oop_idx?>')">정보갱신</button>
				<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-xs m-l-15" onclick="thisCateDel();"">이분류 상품 전부 삭제</button>
				<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="unitAction('false')">선택 주문실패</button>
				<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="unitFalseReturn();">선택 실패복귀</button>
				-->
			</div>

			<div class="m-t-10">
				
				<span>
					총 : <b class="number-point"><?=is_array($_oop_jsondata) ? count($_oop_jsondata) : 0?></b> |
					선택 : <b id="group_body_sum_goods_<?=$_oop_idx ?? ''?>" class="number-point"><?=is_array($_select_json) ? count($_select_json) : 0?></b>
					( 
						총수량 : <b id="group_body_sum_qty_<?=$_oop_idx ?? ''?>" class="number-point"><?=number_format($_oprice_sum_qty)?></b>,
						총무게 : <b id="group_body_sum_weight_<?=$_oop_idx ?? ''?>" class="number-point">0</b>
					)
				</span>

				<span class="m-l-20">
				</span>
				
				<!-- 
				oop_idx : <?=$_oop_idx ?? ''?> | 
				<? if( empty($oog_data['price_colum']) ){ ?>
					※가격 그룹이 설정되지 않았습니다.
				<? }else{ ?>
					가격그룹 : <b><?=$_price_colum_name[$oog_data['price_colum']] ?? ''?></b> ( <?=$oog_data['price_colum'] ?? ''?> )
				<? } ?>
				-->

			</div>

		</ul>
		<ul class="btn">

			<button type="button" id="" class="btnstyle1  btnstyle1-sm" onclick="orderSheetDetail.PrdListReload()" > 
				새로고침
			</button>

			<? //if( ($oo_data['oo_state'] ?? 0) < 5 ){ ?>
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="orderSheetDetailPrd.groupOrder('<?=$_oo_idx ?? ''?>','<?=$_oop_idx ?? ''?>','<?=$form_view ?? ''?>');" > 
				그룹상품 저장
			</button>
			<? //} ?>



		</ul>
	</div>
</div>

<div style="height:72px;"></div>

<?
/*
	echo "<pre>";
	print_r($_select_json);
	echo "</pre>";
*/
?>


<table class="table-list">
	<tr>
		<th></th>
		<th>IDX<br>재고코드</th>
		<th>주문코드</th>
		<th>이미지</th>
		<th>상품명</th>
		<th>메모</th>
		<th>가격</th>
		<th>IV가격</th>
		<th>주문수량</th>
		<th>합가격</th>
		<th>재고</th>
		<th>비고</th>
		<th>무게</th>
	</tr>
<?
// 배열 검증 후 count
$_oop_jsondata_count = is_array($_oop_jsondata) ? count($_oop_jsondata) : 0;

for ($z=0; $z<$_oop_jsondata_count; $z++){

	// 배열 요소 검증
	if (!isset($_oop_jsondata[$z]) || !is_array($_oop_jsondata[$z])) {
		continue;
	}

	$_idx = $_oop_jsondata[$z]['idx'] ?? '';
	$_ps_idx = $_oop_jsondata[$z]['stockidx'] ?? '';
	$_pname = $_oop_jsondata[$z]['pname'] ?? '';
	$_om = $_oop_jsondata[$z]['om'] ?? '';
	$_last = $_oop_jsondata[$z]['last'] ?? '';
	$_weight = $_oop_jsondata[$z]['weight'] ?? 0;
	$_state = $_oop_jsondata[$z]['state'] ?? 'on';

	$_cbm = "";

	$_this_line_show = "show";
	if( (!isset(${"_save_data_".$_idx}) || ${"_save_data_".$_idx} != "ok") && (!isset(${"_false_idx_".$_idx}) || ${"_false_idx_".$_idx} != "ok") ){
		if( $form_view == "hidden" ) $_this_line_show = "hidden";
	}

	//-----------------------------------------------------------------------------------------------------------------
	if( $_this_line_show != "hidden" ){

		$_colum = "CD_CODE, CD_CODE2, CD_CODE3, CD_NAME, 
			CD_SUPPLY_PRICE_1, CD_SUPPLY_PRICE_2, CD_SUPPLY_PRICE_3, CD_SUPPLY_PRICE_4, CD_SUPPLY_PRICE_5, CD_SUPPLY_PRICE_6, CD_SUPPLY_PRICE_7, CD_SUPPLY_PRICE_8, CD_SUPPLY_PRICE_9, 
			CD_WEIGHT, CD_WEIGHT2, CD_WEIGHT3, cd_code_fn, cd_weight_fn, cd_price_fn, cd_memo3";

		$_query = "select A.*, B.ps_idx, B.ps_stock, B.ps_cafe24_sms
			from "._DB_COMPARISON." A
			left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX  ) 
			where CD_IDX = '".$_idx."' ";

		$prd_data = sql_fetch_array(sql_query_error($_query));

		// 배열 검증
		if (!is_array($prd_data)) {
			$prd_data = [];
		}

		if( empty($_pname) ) $_pname = $prd_data['CD_NAME'] ?? '';


		$_barcode = $prd_data['CD_CODE'] ?? '';
		$_code2 = $prd_data['CD_CODE2'] ?? '';
		$_code3 = $prd_data['CD_CODE3'] ?? '';

		$img_path = '';
		if( !empty($prd_data['CD_IMG']) ){
			$img_path = '/data/comparion/'.$prd_data['CD_IMG'];
		}

		$_prd_price_data = json_decode($prd_data['cd_price_fn'] ?? '{}', true);
		if (!is_array($_prd_price_data)) {
			$_prd_price_data = [];
		}

		$_price = 0;
		$_show_price = 0;
		$_invoice_price = 0;
		$_show_iv_price  = 0;

		if( isset($_prd_price_data[$_oop_code]) && !empty($_prd_price_data[$_oop_code]) ){
			$_price = $_prd_price_data[$_oop_code];
			$_show_price = number_format($_prd_price_data[$_oop_code],2);
		}

		if( isset($_prd_price_data['invoice'][$_oop_code]) && !empty($_prd_price_data['invoice'][$_oop_code]) ){
			$_invoice_price = $_prd_price_data['invoice'][$_oop_code];
			$_show_iv_price = number_format($_invoice_price,2);
		}

		
		$_cd_size_fn_data = json_decode($prd_data['cd_size_fn'] ?? '{}', true);
		if (!is_array($_cd_size_fn_data)) {
			$_cd_size_fn_data = [];
		}

		if( isset($_cd_size_fn_data['invoice']['cbm']) && !empty($_cd_size_fn_data['invoice']['cbm']) ){
			$_cbm = $_cd_size_fn_data['invoice']['cbm'];
		}

		$_cd_weight_data = json_decode($prd_data['cd_weight_fn'] ?? '{}', true);
		if (!is_array($_cd_weight_data)) {
			$_cd_weight_data = [];
		}
		
		$_cd_weight_1 = $_cd_weight_data['1'] ?? 0;
		$_cd_weight_2 = $_cd_weight_data['2'] ?? 0;
		$_cd_weight_3 = $_cd_weight_data['3'] ?? 0;

		if( $_cd_weight_3 ){
			$_weight = $_cd_weight_3;
		}else{
			$_weight = max($_cd_weight_1, $_cd_weight_2);
		}

		//주문 실패 상태일경우
		if( isset(${"_false_idx_".$_idx}) && ${"_false_idx_".$_idx} == "ok" ){
			$_tr_color= "#adadad";
			$_memo = isset(${"_false_memo_".$_idx}) ? ${"_false_memo_".$_idx} : '';
		}else{
			if( ($prd_data['ps_stock'] ?? 0) == 0 ){
				$_tr_color= "#eee";
			}else{
				$_tr_color= "#fff";
			}
			$_memo = isset(${"_save_data_memo_".$_idx}) ? ${"_save_data_memo_".$_idx} : '';
		}

		if( $_state == "out" ){
			$_tr_color= "#b9d3c0";
		}

		// 주문 총 가격 계산
		$_qty = $save_qty[$_idx] ?? 0;
		$unit_price_sum = $_price * $_qty;
		$save_unit_price = number_format($unit_price_sum, 2);

?>

<tr bgcolor = "<?=$_tr_color?>" id="tr_<?=$_idx?>" >
	
	<!-- 체크 -->
	<td id="checkbox_td_<?=$_idx?>">
		<input type="checkbox" name="key_check[]"  id="checkbox_<?=$_idx?>" class="checkSelect" value="<?=$_idx?>" style="<? if( isset(${"_false_idx_".$_idx}) && ${"_false_idx_".$_idx} == "ok" ){?>display:none;<?}?>">
	</td>

	<!-- 상품 고유번호 -->
	<td>
		<?=$_idx?><br>
		<b style="font-size:13px; color:#2525fa;"><?=$_ps_idx?></b>
	</td>

	<!-- 상품 코드 -->
	<td>
		<b><?=$_code2?></b>
		<? if( !empty($_code3) ){ ?><br><?=$_code3?><? } ?>

		<? if( ($oo_data['oo_state'] ?? 0) > 1 && ($save_qty[$_idx] ?? 0) > 1){ ?>
			<div class="m-t-5">
			<? if( isset(${"_false_idx_".$_idx}) && ${"_false_idx_".$_idx} == "ok" ){ ?>
			<button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="orderSheetDetailPrd.unitFalse(this,'<?=$_idx?>', '<?=$_oo_idx ?? ''?>', '<?=$_oop_idx ?? ''?>','on','<?=$form_view ?? ''?>')" >주문실패복원</button>
			<? }else{ ?>
			<button type="button" class="btnstyle1 btnstyle1-xs" onclick="orderSheetDetailPrd.unitFalse(this,'<?=$_idx?>', '<?=$_oo_idx ?? ''?>', '<?=$_oop_idx ?? ''?>','out','<?=$form_view ?? ''?>')" >주문실패처리</button>
			<? } ?>
			</div>
		<? } ?>

	</td>

	<td style="width:70px;">
		<img src="<?=$img_path?>" style="height:60px; border:1px solid #eee !important; cursor:pointer;" onclick="onlyAD.prdView('<?=$_idx ?? ''?>','info');">
	</td>

	<td class="text-left">

		<div><?=$_barcode?></div>
		<div class="p-t-5 p-b-5 p-l-3">
			<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?=$_idx ?? ''?>','info');">보기</button> <?=$_pname?>
			<? if( !empty($_om) ){ ?><br><span style="color:#ff0000; display:inline-block; margin-top:3px; font-size:11px;"><?=$_om?></span><? } ?>
		</div>

		<? if( !empty($prd_data['cd_memo3']) ){ ?>
			<div class="p-b-5"><span style="color:#ff0000; font-size:13px;"><?=$prd_data['cd_memo3']?></span></div>
		<? } ?>

		<div>
		<? if( $_state == "on" ){ ?>
			<button type="button" id="aa" class="btnstyle1 btnstyle1-xs" onclick="orderSheetDetailPrd.soldOut(this, '<?=$_oop_idx ?? ''?>', '<?=$z?>','out')">단종처리</button>
		<? }else{ ?>
			<button type="button" id="aa" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetDetailPrd.soldOut(this, '<?=$_oop_idx ?? ''?>', '<?=$z?>','on')">단종해제</button>
		<? } ?>

		</div>

	</td>


	<!-- 주문메모 -->
	<td style="width:90px; padding:0 !important; ">
		<textarea name="memo" id="memo_<?=$_idx?>" style="width:100%; height:56px; background-color:transparent;  border:none !important; resize: none; padding:5px; margin:0 !important; box-sizing:border-box;  color:#ff0000;"><?=$_memo?></textarea>

<?
/*
	echo "<pre>";
	print_r($_prd_price_data);
	echo "</pre>";
*/
?>
	</td>


	<!-- 상품가격 -->
	<td class="text-right" id="unit_price_td_<?=$_idx?>" data-price="<?=$_price?>" style="width:100px;" >
		
		<? if( $_price == 0 ){ //GC.commaInput( this.value, this ); ?>
			<input type='text' name='' id="unit_price_<?=$_idx?>" style="width:60px;" value="" onkeyUP=" orderSheetDetail.qtyGogo('<?=$_idx?>', '<?=$_oop_idx ?? ''?>');"><button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-xs m-l-2" onclick="orderSheetDetailPrd.newPrice('<?=$_idx?>', '<?=$oop_data['oop_code'] ?? ''?>')" ><i class="fas fa-save"></i></button>
		<? }else{ ?>
			<input type='hidden' name='' id="unit_price_<?=$_idx?>" value="<?=$_price?>">
			<span>
				<a href="#" class="editable-cd-price editable-click" 
					data-pk="<?=$_show_price?>" 
					data-cdidx="<?=$_idx?>" 
					data-oopcode="<?=$oop_data['oop_code'] ?? ''?>"
					data-oopidx="<?=$_oop_idx ?? ''?>">
					<b><?=$_show_price?></b>
				</a>
			</span>

			<? 
				$_this_won_price = "";
				if( ($oo_data['oo_sum_exchange_rate'] ?? 0) > 0 ){ 
					$_this_won_price = number_format($_price * $oo_data['oo_sum_exchange_rate'],2);
					$_this_won_price = str_replace('.00','',$_this_won_price);
			?>
				<div class="m-t-5">₩ <?=$_this_won_price?></div>
			<? } ?>

		<? } ?>

	</td>

	<!-- 인보이스가격 -->
	<td class="text-right" id="unit_iv_price_td_<?=$_idx?>" data-price="<?=$_invoice_price?>" style="width:70px;" >

		<? if( $_invoice_price == 0 ){ //GC.commaInput( this.value, this ); ?>
			<input type='text' name='' id="unit_iv_price_<?=$_idx?>" style="width:60px;" value=""><button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-xs m-l-2" onclick="orderSheetDetailPrd.newInvoicePrice('<?=$_idx?>', '<?=$oop_data['oop_code'] ?? ''?>')" ><i class="fas fa-save"></i></button>
		<? }else{ ?>
			<input type='hidden' name='' id="unit_iv_price_<?=$_idx?>" value="<?=$_invoice_price?>">
			<span>
				<a href="#" class="editable-cd-iv-price editable-click" 
					data-pk="<?=$_show_iv_price?>" 
					data-cdidx="<?=$_idx?>" 
					data-oopcode="<?=$oop_data['oop_code'] ?? ''?>"
					data-oopidx="<?=$_oop_idx ?? ''?>">
					<b><?=$_show_iv_price?></b>
				</a>
			</span>
		<? } ?>

	</td>

	<!-- 주문수량 -->
	<td style="width:55px;">
		<input type='text' name='cd_code2' id="unit_qty_<?=$_idx?>" style="width:100%; font-size:15px; font-weight:bold; color:<? if( isset(${"_false_idx_".$_idx}) && ${"_false_idx_".$_idx} == "ok" ){ echo "#999"; }else{ echo "#021aff"; } ?>;" value="<?=$save_qty[$_idx] ?? 0?>" onkeyUP="orderSheetDetail.qtyGogo('<?=$_idx?>', '<?=$_oop_idx ?? ''?>');">
	</td>


	<!-- 상품 주문 총 가격 -->
	<td class="text-right width-60">
		<input type="hidden" name="" id="unit_price_sum_<?=$_idx?>" class="unit-price-sum-data" value="<?=$unit_price_sum?>">
		<? if( $_state == "on" ){ ?>
		<span id="order_qty_sum_<?=$_idx?>" class="unit-price-sum" ><?=$save_unit_price?></span>
		<? }else{ ?>
			단종
		<? } ?>
	</td>


	<!-- 상품재고 -->
	<td style="width:30px;">
		<b onclick="onlyAD.prdView('<?=$_idx ?? ''?>','stock');" style="cursor:pointer; <? if( ($prd_data['ps_stock'] ?? 0) == 0 ) echo "color:#aaa;"; ?>"><?=$prd_data['ps_stock'] ?? 0?></b>
	</td>


	<!-- 마지막 입고일 -->
	<td class="text-left" style="width:100px; font-size:11px;">
		<? 
		$_ps_cafe24_sms_data = json_decode($prd_data['ps_cafe24_sms'] ?? '{}', true);
		if (!is_array($_ps_cafe24_sms_data)) {
			$_ps_cafe24_sms_data = [];
		}
		if( ($_ps_cafe24_sms_data['count'] ?? 0) > 0 ){ ?>
		<div style="background-color:#ffb5b5; padding:4px; margin-bottom:3px; border-radius:5px; border:1px solid #cf7979;">
			<ul>입고알림 : <b><?=$_ps_cafe24_sms_data['count'] ?? 0?></b></ul>
			<ul class="m-t-2" style="font-size:10px;"><?=!empty($_ps_cafe24_sms_data['date']) ? date("m.d H:i:s", strtotime($_ps_cafe24_sms_data['date'])) : ''?></ul>
		</div>
		<? } ?>
		<div style="font-size:12px;"><?=$_last?></div>
	</td>


	<!-- 무게 -->
	<td class="text-center" style="width:55px;">
	
		<span id="weight_<?=$_idx?>" class="unit-weight <? if( $_weight_mode=="2" ) echo "no-weight"; ?>" data-weight="<?=$_weight?>">
			<? 
				if( $_weight ){ 
					if( $_weight > 1000 ){
						$_this_weight = number_format($_weight/1000,2)."kg";
					}else{
						$_this_weight = number_format($_weight)."g";
					}
			?>
				<?=$_this_weight?>
			<? }else{ ?>
				<div class="notice-box">
					<!-- <i class="fas fa-exclamation-circle"></i><br> -->무게정보 없습니다.
				</div>
			<? } ?>
		</span>

		<span id="cbm_<?=$_idx?>" class="unit-cbm" data-cbm="<?=$_cbm?>">
			<? if( $_cbm ){ ?>
				cbm : <b><?=$_cbm?></b>
			<? }else{ ?>
				<div class="notice-box">
					<!-- <i class="fas fa-exclamation-circle"></i><br> -->CBM 정보 없습니다.
				</div>
			<? } ?>
		</span>
	</td>

</tr>

<? 
	} 
} // if( $_this_line_show != "hidden" ){
?>
</table>

<script type="text/javascript"> 
<!-- 
function selectGo(){

<? 
$_select_json_count = is_array($_select_json) ? count($_select_json) : 0;
if( $_select_json_count > 0 ){
	for ($i=0; $i<$_select_json_count; $i++){ 
		if (!isset($_select_json[$i]) || !is_array($_select_json[$i])) continue;
		$_pidx = $_select_json[$i]['pidx'] ?? '';
		$_qty = $_select_json[$i]['qty'] ?? 0;
		if( !isset(${"_false_idx_".$_pidx}) || ${"_false_idx_".$_pidx} != "ok" ){
?>
	$("#checkbox_<?=$_pidx?>").attr("checked", true);
	$("#unit_qty_<?=$_pidx?>").val(<?=$_qty?>);
	orderSheetDetail.qtyGogo('<?=$_pidx?>', '<?=$_oop_idx ?? ''?>');

<? } } } ?>

}

selectGo();

var orderSheetDetailPrd = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		group : function() {

			var width = "1400px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "그룹수정",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/partners_info',
						data: { "pmode":"newReg" },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},
				buttons: {
					cancel: {
						text: '닫기',
						action:function () {
							
						}
					},
				}
			});

		},

		//그룹상품 저장
		groupOrder : function( oo_idx, oop_idx, form_view ) {

			/*
			alert(oop_idx);
			return false;
			*/

			/*
			if( $(".checkSelect:checked").length == 0 ){
				if( mode == "noReload" ){
				}else{
					showAlert("Error", "이 그룹에 주문할 상품이 선택된것이 없습니다.", "alert2" );
					return false;
				}
				return false;
			}
			*/

			var item = $(".checkSelect:checked").length;
			var send_idx = new Array();
			var send_price = new Array();
			var send_qty = new Array();
			var send_memo = new Array();

			var total_qty = 0;
			var total_price = 0;
			var total_weight = 0;
			var total_cbm = 0;

			$(".checkSelect:checked").each(function(index){
				var checkbox_id = $(this).val();

				send_idx.push(checkbox_id);
				send_price.push($("#unit_price_"+checkbox_id).val());
				send_qty.push($("#unit_qty_"+checkbox_id).val());
				send_memo.push($("#memo_"+checkbox_id).val());

				if( $("#unit_qty_" + checkbox_id).val() == "" ){
					var plus_oprice_sum_qty = 1;
				}else{
					var plus_oprice_sum_qty = ($("#unit_qty_" + checkbox_id).val()*1);
				}

				total_qty = total_qty + ($("#unit_qty_"+checkbox_id).val()*1);
				total_price = total_price + ($("#unit_price_sum_" + checkbox_id).val()*1);
				total_weight = total_weight + ($("#weight_"+checkbox_id).data('weight') * plus_oprice_sum_qty);
				total_cbm = total_cbm + ($("#cbm_"+checkbox_id).data('cbm') * plus_oprice_sum_qty);

				console.log(total_cbm);
			});

			$.ajax({
				url: "/ad/processing/order_sheet",
				data: { 
					"a_mode" : "orderSheet_groupOrder", 
					"oo_idx" : oo_idx,
					"oop_idx" : oop_idx,
					"item" : item,
					"total_qty" : total_qty,
					"total_price" : total_price,
					"total_weight" : total_weight,
					"total_cbm" : total_cbm,
					"send_idx" : send_idx,
					"send_price" : send_price,
					"send_qty" : send_qty,
					"send_memo" : send_memo 
				},
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						toast2("success", "그룹상품 저장", "설정이 저장되었습니다.");
						orderSheetDetail.groupState("end");
						$("#group_side_sum_qty_"+ oop_idx).data('value',total_qty);
						//location.href='/ad/order/order_sheet/'+ oo_idx +'/'+ oop_idx;
						
						$("#oprice_allsum").html(GC.comma(res.oo_sum_price ?? 0));
						$("#oprice_sum_goods").html(GC.comma(res.oo_sum_goods ?? 0)); //전체 상품
						$("#oprice_sum_qty").html(GC.comma(res.oo_sum_qty ?? 0)); //전체 수량
						$("#oprice_sum_weight").html(GC.comma(res.oo_sum_weight ?? 0)+"g"); //전체 무게

						$("#oprice_sum_goods_"+ oop_idx).html(GC.comma(res.group_sum_goods ?? 0)); //그룹 주문 상품
			/*
			'group_sum_goods' => $_item, 'group_sum_qty' => $_total_qty, 'group_sum_weight' => $_total_weight, 'group_sum_price' => $_total_price,
			'oo_sum_goods' => $_oo_sum_goods, 'oo_sum_qty' => $_oo_sum_qty, 'oo_sum_weight' => $_oo_sum_weight, 'oo_sum_price' => $_oo_sum_price 
			*/

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

		//상품 단종처리
		soldOut : function( obj, oop_idx, num, soldoutmode ) {
		
			$(obj).attr('disabled', true);
			$.ajax({
				url: "/ad/processing/order_sheet",
				data: { "a_mode":"orderSheet_soldOut", "oop_idx":oop_idx, "num":num, "soldoutmode":soldoutmode },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						if( soldoutmode == "out" ){
							toast2("success", "단종처리", "단종 처리 완료되었습니다.");
						}else{
							toast2("success", "단종해제", "단종 해제 처리 완료되었습니다.");
						}
						orderSheetDetail.prdListShow( '<?=$_oo_idx ?? ''?>', '<?=$_oop_idx ?? ''?>' );
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

		},

		//상품 가격 생성
		newPrice : function( idx, oop_code ) {

			var nprice = $("#unit_price_"+ idx).val();
			if( !nprice || nprice == 0 ){
				$("#unit_price_"+ idx).focus();
				showAlert("Error", "가격을 입력해주세요.", "alert2" );
				return false;
			}

			//_price = GC.uncomma(nprice);
			_price = nprice;

			$.ajax({
				url: "/ad/processing/order_sheet",
				data: { "a_mode":"orderSheet_price_new", "reg_mode":"newprice", "idx":idx, "oop_code":oop_code, "price":_price },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){

						toast2("success", "가격 저장", "가격이 저장되었습니다.");

						var Html = "<input type='hidden' id='unit_price_"+ idx +"' value='"+ _price +"'>"
						+ "<b>"+ nprice +"</b> ";

						$("#unit_price_td_" + idx).html(Html).data("price",_price);
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

		},

		//인보이스 가격 생성
		newInvoicePrice : function( idx, oop_code ) {

			var nprice = $("#unit_iv_price_"+ idx).val();
			if( !nprice || nprice == 0 ){
				$("#unit_iv_price_"+ idx).focus();
				showAlert("Error", "인보이스 가격을 입력해주세요.", "alert2" );
				return false;
			}

			//_price = GC.uncomma(nprice);
			_price = nprice;

			$.ajax({
				url: "/ad/processing/order_sheet",
				data: { "a_mode":"orderSheet_price_new", "reg_mode":"newinvoiceprice", "idx":idx, "oop_code":oop_code, "price":_price },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){

						toast2("success", "가격 저장", "가격이 저장되었습니다.");

						var Html = "<input type='hidden' id='unit_iv_price_"+ idx +"' value='"+ _price +"'>"
						+ "<b>"+ nprice +"</b> ";

						$("#unit_iv_price_td_" + idx).html(Html).data("price",_price);
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

		},

		// 주문실패 처리
		unitFalse : function( obj, pidx, oo_idx, oop_idx, mode, form_view ) {
		
			var pidx_memo = $("#memo_"+ pidx).val();

			$(obj).attr('disabled', true);
			$.ajax({
				url: "/ad/processing/order_sheet",
				data: { "a_mode":"orderSheet_unitFalse", "pidx":pidx, "oo_idx":oo_idx, "oop_idx":oop_idx, "unit_false_mode":mode, "pidx_memo":pidx_memo },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						location.href='/ad/order/order_sheet/'+ oo_idx +'/'+ oop_idx;
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
		
		},

	};

}();


$(function(){

	// 가격변경
	$('.editable-cd-price').editable({
			type: 'text',
			url: '/ad/processing/order_sheet',
			title: '가격 변경',
			inputclass:'testinput',
			params: function(params) {
				params.a_mode = 'orderSheet_price_modify';
				params.cd_idx = $(this).data('cdidx');
				params.oop_code = $(this).data('oopcode');
				params.mod_mode = "price";
				return params;
			},
			ajaxOptions: {
				type: 'POST',
				dataType: 'json'
			},
			display: function(value, response) {
				return false;
			},
			success: function(res) {
				if(res.success === true) {
					$(this).html("<b>"+ GC.comma(res.uprice) +"</b>");
					$("#unit_price_" + $(this).data('cdidx')).val(res.uprice);
					orderSheetDetail.qtyGogo($(this).data('cdidx'), $(this).data('oopidx'));
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
			validate: function(value) {
				if($.trim(value) == '') {
					return '빈 값은 입력할 수 없습니다.';
				}
			}
	});

	// 인보이스 가격변경
	$('.editable-cd-iv-price').editable({
			type: 'text',
			url: '/ad/processing/order_sheet',
			title: '인보이스 가격변경',
			inputclass:'testinput',
			params: function(params) {
				params.a_mode = 'orderSheet_price_modify';
				params.cd_idx = $(this).data('cdidx');
				params.oop_code = $(this).data('oopcode');
				params.mod_mode = "invoicePrice";
				return params;
			},
			ajaxOptions: {
				type: 'POST',
				dataType: 'json'
			},
			display: function(value, response) {
				return false;
			},
			success: function(res) {
				if(res.success === true) {
					$(this).html("<b>"+ GC.comma(res.uprice) +"</b>");
					$("#unit_iv_price_" + $(this).data('cdidx')).val(res.uprice);
					//orderSheetDetail.qtyGogo($(this).data('cdidx'), $(this).data('oopidx'));
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
			validate: function(value) {
				if($.trim(value) == '') {
					return '빈 값은 입력할 수 없습니다.';
				}
			}
	});
});
//--> 
</script> 