<?
	// 변수 초기화
	$_idx = $_GET['idx'] ?? $_POST['idx'] ?? "";
	$_selpd_groups = [];
	
	$oo_data = sql_fetch_array(sql_query_error("select A.oo_name, A.oo_json, A.oo_false, A.oo_stock, B.oog_brand from ona_order A left join ona_order_group B ON B.oog_idx = A.oo_form_idx where A.oo_idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($oo_data)) {
		$oo_data = [];
	}

	$ooFalseRows = json_decode((string)($oo_data['oo_false'] ?? '[]'), true);
	if (!is_array($ooFalseRows)) {
		$ooFalseRows = json_decode('[' . trim((string)($oo_data['oo_false'] ?? '')) . ']', true);
	}
	if (!is_array($ooFalseRows)) {
		$ooFalseRows = [];
	}
	$ooFalsePidxMap = [];
	foreach ($ooFalseRows as $ooFalseRow) {
		if (!is_array($ooFalseRow)) {
			continue;
		}
		$ooFalsePidx = (int)($ooFalseRow['pidx'] ?? 0);
		if ($ooFalsePidx > 0) {
			$ooFalsePidxMap[$ooFalsePidx] = true;
		}
	}

	$_json_oo_stock = json_decode($oo_data['oo_stock'] ?? '{}', true);
	if (!is_array($_json_oo_stock)) {
		$_json_oo_stock = [];
	}

	$_order_sec_json2 = $oo_data['oo_json'] ?? '[]';
	$_select_order = json_decode($_order_sec_json2, true);
	if (!is_array($_select_order)) {
		$_select_order = [];
	}

	$oogBrand = json_decode((string)($oo_data['oog_brand'] ?? '[]'), true);
	if (!is_array($oogBrand)) {
		$oogBrand = json_decode('[' . trim((string)($oo_data['oog_brand'] ?? '')) . ']', true);
	}
	if (!is_array($oogBrand)) {
		$oogBrand = [];
	}

	$oopSortByIdx = [];
	$oopFormNameByIdx = [];
	$oopSort = 0;
	foreach ($oogBrand as $brandRow) {
		if (!is_array($brandRow)) {
			continue;
		}
		$oopIdx = (int)($brandRow['oop_idx'] ?? 0);
		if ($oopIdx <= 0 || isset($oopSortByIdx[$oopIdx])) {
			continue;
		}
		$oopSortByIdx[$oopIdx] = $oopSort;
		$oopSort++;
		$formName = trim((string)($brandRow['name'] ?? ''));
		if ($formName !== '') {
			$oopFormNameByIdx[$oopIdx] = $formName;
		}
	}

	// 배열 검증 후 count
	$_select_order_count = is_array($_select_order) ? count($_select_order) : 0;

	$oopIdxList = [];
	for ( $z=0; $z<$_select_order_count; $z++ ){
		if (!isset($_select_order[$z]) || !is_array($_select_order[$z])) {
			continue;
		}
		$oopIdx = (int)($_select_order[$z]['bidx'] ?? 0);
		if ($oopIdx > 0) {
			$oopIdxList[$oopIdx] = $oopIdx;
		}
	}

	$oopNameByIdx = [];
	if (!empty($oopIdxList)) {
		$oopIdxSql = implode(',', array_map('intval', array_values($oopIdxList)));
		$oopResult = sql_query_error("select oop_idx, oop_name from ona_order_prd where oop_idx in (".$oopIdxSql.")");
		while ($oopRow = sql_fetch_array($oopResult)) {
			$oopIdx = (int)($oopRow['oop_idx'] ?? 0);
			if ($oopIdx > 0) {
				$oopNameByIdx[$oopIdx] = trim((string)($oopRow['oop_name'] ?? ''));
			}
		}
	}

	for ( $z=0; $z<$_select_order_count; $z++ ){
		
		// 배열 요소 검증
		if (!isset($_select_order[$z]) || !is_array($_select_order[$z])) {
			continue;
		}

		$_ary_selpd = $_select_order[$z]['selpd'] ?? [];
		if (!is_array($_ary_selpd)) {
			continue;
		}

		$groupBidx = (int)($_select_order[$z]['bidx'] ?? 0);
		$groupName = $oopFormNameByIdx[$groupBidx] ?? ($oopNameByIdx[$groupBidx] ?? '');
		if ($groupName === '') {
			$groupName = $groupBidx > 0 ? ('그룹 ' . $groupBidx) : '폼그룹';
		}

		$groupItems = [];
		$_ary_selpd_count = count($_ary_selpd);
		
		for ($i=0; $i<$_ary_selpd_count; $i++){ 
			
			// 배열 요소 검증
			if (!isset($_ary_selpd[$i]) || !is_array($_ary_selpd[$i])) {
				continue;
			}

			if (!empty($_ary_selpd[$i]['false']) || isset($ooFalsePidxMap[(int)($_ary_selpd[$i]['pidx'] ?? 0)])) {
				continue;
			}

			if( empty($_ary_selpd[$i]['false']) ){
				$_false = false;
			}else{
				$_false = $_ary_selpd[$i]['false'];
			}

			$groupItems[] = array(
				"pidx" => $_ary_selpd[$i]['pidx'] ?? '',
				"qty" => $_ary_selpd[$i]['qty'] ?? 0,
				"false" => $_false
			);

		}

		if (empty($groupItems)) {
			continue;
		}

		$_selpd_groups[] = array(
			"bidx" => $groupBidx,
			"name" => $groupName,
			"items" => $groupItems
		);
	}

	usort($_selpd_groups, static function ($a, $b) use ($oopSortByIdx) {
		$aIdx = (int)($a['bidx'] ?? 0);
		$bIdx = (int)($b['bidx'] ?? 0);
		$aSort = $oopSortByIdx[$aIdx] ?? PHP_INT_MAX;
		$bSort = $oopSortByIdx[$bIdx] ?? PHP_INT_MAX;
		if ($aSort === $bSort) {
			return $aIdx <=> $bIdx;
		}
		return $aSort <=> $bSort;
	});

/*
	echo "<pre>";
	print_r($_select_order);
	echo "</pre>";

	echo "<pre>";
	print_r($_selpd);
	echo "</pre>";

	echo "<pre>";
	print_r($_json_oo_stock);
	echo "</pre>";
*/
?>



<form id="form_os_stock">
<input type="hidden" name="a_mode" value="os_allStock" >
<input type="hidden" name="os_idx" value="<?=$_idx ?? ''?>" >

<? if( ($_json_oo_stock['state'] ?? '') == "in" ){ ?>
	<div>
		재고 일괄 등록이 완료된 상태입니다. ( <?=!empty($_json_oo_stock['reg']['date']) ? date ("y.m.d H:i:s", strtotime($_json_oo_stock['reg']['date'])) : ''?> | <?=$_json_oo_stock['reg']['id'] ?? ''?> )
	</div>
<? } ?>
	<div>
		재고 등록일 : <div class="calendar-input" style="display:inline-block;"><input type='text' name='stock_day'  value="<?=date("Y-m-d")?>" ></div>
		<input type='text' name='stock_all_memo' id='stock_all_memo' style="width:200px" value="<?=($oo_data['oo_name'] ?? '') . ' 입고'?>"  >
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm " onclick="osStock.allStock()">재고등록</button>
	</div>


<table class="table-list m-t-10">
	<tr>
		<th>상품번호<br>재고코드</th>
		<th>이미지</th>
		<th>상품명</th>
		<th>바코드</th>
		<th>현재고</th>
		<th>주문수량</th>
		<th>실입고수량</th>
		<th>메모</th>
	</tr>
<?
$_selpd_groups_count = is_array($_selpd_groups) ? count($_selpd_groups) : 0;

for ( $g=0; $g<$_selpd_groups_count; $g++ ){
	if (!isset($_selpd_groups[$g]) || !is_array($_selpd_groups[$g])) {
		continue;
	}

	$_group_items = $_selpd_groups[$g]['items'] ?? [];
	if (!is_array($_group_items) || empty($_group_items)) {
		continue;
	}
	$_group_name = (string)($_selpd_groups[$g]['name'] ?? '폼그룹');
?>
	<tr>
		<th colspan="8" class="text-left"><?= htmlspecialchars($_group_name, ENT_QUOTES, 'UTF-8') ?></th>
	</tr>
<?
	$_group_items_count = count($_group_items);
	for ( $i=0; $i<$_group_items_count; $i++ ){
	
	// 배열 요소 검증
	if (!isset($_group_items[$i]) || !is_array($_group_items[$i])) {
		continue;
	}
			
	$_pidx = $_group_items[$i]['pidx'] ?? '';
	$_qty = $_group_items[$i]['qty'] ?? 0;
	$_false = $_group_items[$i]['false'] ?? false;
	if (!empty($_false) || isset($ooFalsePidxMap[(int)$_pidx])) {
		continue;
	}

	$prd_data = sql_fetch_array(sql_query_error("select 
		A.CD_NAME, A.CD_IMG, A.CD_CODE, A.CD_CODE2, A.cd_code_fn, A.CD_INV_NAME1, A.CD_INV_NAME2, A.CD_INV_MATERIAL, A.CD_NAME_OG, A.CD_COO,
		B.ps_idx, B.ps_stock,
		C.BD_NAME 
		from "._DB_COMPARISON." A 
		left join prd_stock B ON (A.CD_IDX = B.ps_prd_idx ) 
		left join "._DB_BRAND." C  ON (A.CD_BRAND_IDX = C.BD_IDX ) 
		where CD_IDX = '".$_pidx."' "));

	// 배열 검증
	if (!is_array($prd_data)) {
		$prd_data = [];
	}

	$img_path = '';
	if( !empty($prd_data['CD_IMG']) ){
		$img_path = '/data/comparion/'.$prd_data['CD_IMG'];
	}

	$_pname = $prd_data['CD_NAME'] ?? '';

	$_tr_color= "#fff";
	if( $_false == true ){
		$_tr_color= "#eee";
	}
?>
	<tr bgcolor = "<?=$_tr_color?>">
		<td><?=$_pidx ?? ''?><br><b><?=$prd_data['ps_idx'] ?? ''?></b></td>
		<td style="width:70px;">
			<img src="<?=$img_path?>" style="height:60px; border:1px solid #eee !important;">
		</td>
		<td class="text-left">
			<div><?=$prd_data['BD_NAME'] ?? ''?></div>
			<div><button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?=$_pidx ?? ''?>','info');"">보기</button> <?=$_pname?></div>
		</td>
		<td><?=$prd_data['CD_CODE'] ?? ''?></td>
		<td style="width:70px;">
			<?=number_format($prd_data['ps_stock'] ?? 0)?>
		</td>
		<td style="width:70px;">
			<?=number_format($_qty)?>
		</td>
		<td style="width:70px;">
			<? if( $_false == true ){ ?>
				주문실패
			<? }else{ ?>
				<input type="hidden" name="ps_idx[]" value="<?=$prd_data['ps_idx'] ?? ''?>">
				<input type="text" name="s_qty[]" style="width:100%; font-size:14px; font-weight:bold;" value="<?=$_qty?>" >
			<? } ?>
		</td>
		<td style="width:150px;">
			<? if( $_false == true ){ ?>
			<? }else{ ?>
				<input type="text" name="s_memo[]" style="width:100%;" value="" >
			<? } ?>
		</td>
	</tr>
<? } } ?>
</table>
</form>

<script type="text/javascript"> 
<!-- 
var osStock = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		//일괄등록
		allStock : function() {

			var formData = $("#form_os_stock").serializeArray();

			//$(obj).attr('disabled', true);
			$.ajax({
				url: "/ad/processing/order_sheet",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						alert("재고가 일괄 등록되었습니다.");
						location.reload();
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