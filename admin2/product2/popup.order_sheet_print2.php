<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../lib/inc_common.php";

$pageGroup = "product2";
$pageName = "order_sheet_print_popup";

// Composer autoload
require_once('../../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

// 변수 초기화
$idx = $_GET['idx'] ?? $_POST['idx'] ?? "";
$code = $_GET['code'] ?? $_POST['code'] ?? "";
$excel = $_GET['excel'] ?? $_POST['excel'] ?? "";
$excel_mode = $_GET['excel_mode'] ?? $_POST['excel_mode'] ?? "";
$mode = $_GET['mode'] ?? $_POST['mode'] ?? "";

$_oo_idx = securityVal($idx);
$_code = securityVal($code);
$_excel = securityVal($excel);
$_excel_mode = securityVal($excel_mode);
$_mode = securityVal($mode);

// 주요 변수 초기화
$_order_sec_json = [];
$_order_sec_data = [];
$oo_data = [];

// DB에서 주문 데이터 가져오기
if( $_oo_idx ){
	$btn_text_submit = "주문서 수정";
	
	$oo_data = wepix_fetch_array(wepix_query_error("select * from ona_order where oo_idx = '".$_oo_idx."' "));
	
	// 배열 검증
	if (!is_array($oo_data) || empty($oo_data)) {
		$oo_data = ['oo_json' => '[]', 'oo_name' => ''];
	}

	$_order_sec_json2 = $oo_data['oo_json'] ?? '[]';
	$_order_sec_json = json_decode($_order_sec_json2, true);
	
	// JSON 디코드 결과 검증
	if (!is_array($_order_sec_json)) {
		$_order_sec_json = [];
	}
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

// 주문 JSON의 bidx(ona_order_prd.oop_idx)로 실제 상품그룹 가격코드를 복원한다.
$oopCodeByIdx = [];
$oopIdxList = [];
foreach ($_order_sec_json as $orderGroup) {
	if (!is_array($orderGroup)) {
		continue;
	}
	$oopIdx = (int)($orderGroup['bidx'] ?? 0);
	if ($oopIdx > 0) {
		$oopIdxList[$oopIdx] = $oopIdx;
	}
}
if (!empty($oopIdxList)) {
	$oopIdxSql = implode(',', array_map('intval', array_values($oopIdxList)));
	$oopResult = wepix_query_error("select oop_idx, oop_code from ona_order_prd where oop_idx in (".$oopIdxSql.")");
	while ($oopRow = wepix_fetch_array($oopResult)) {
		$oopIdx = (int)($oopRow['oop_idx'] ?? 0);
		if ($oopIdx > 0) {
			$oopCodeByIdx[$oopIdx] = (string)($oopRow['oop_code'] ?? '');
		}
	}
}

// 주문수량/실패수량 요약
$orderProductCount = 0;
$orderQuantity = 0;
$failedProductCount = 0;
$failedQuantity = 0;
foreach ($_order_sec_json as $orderGroup) {
	$selectedProducts = is_array($orderGroup) ? ($orderGroup['selpd'] ?? []) : [];
	if (!is_array($selectedProducts)) {
		continue;
	}

	foreach ($selectedProducts as $selectedProduct) {
		if (!is_array($selectedProduct)) {
			continue;
		}

		$quantity = (int)($selectedProduct['qty'] ?? 0);
		$orderProductCount++;
		$orderQuantity += $quantity;
		if (!empty($selectedProduct['false']) || isset($ooFalsePidxMap[(int)($selectedProduct['pidx'] ?? 0)])) {
			$failedProductCount++;
			$failedQuantity += $quantity;
		}
	}
}
$stockInProductCount = $orderProductCount - $failedProductCount;
$stockInQuantity = $orderQuantity - $failedQuantity;

// 엑셀 다운로드 처리
if( $_excel == "ok" ){

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();

	// 헤더 설정
	if( $_mode == "stock" ){
		$headers = ['재고코드', '이미지', '상품명', 'JAN코드', 'P코드', '코드3', '수량'];
	} else {
		$headers = ['재고코드', '이미지', '한글 상품명', '원어명', '품번', '인보이스명', '주문수량', '주문단가', '합가격', '소재', '바코드', '', '원산지'];
	}

	// 헤더 스타일 설정
	$headerStyle = [
		'font' => ['bold' => true, 'size' => 11],
		'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']],
		'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
		'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
	];

	// 헤더 작성
	$col = 'A';
	foreach ($headers as $header) {
		$sheet->setCellValue($col.'1', $header);
		$sheet->getStyle($col.'1')->applyFromArray($headerStyle);
		$sheet->getColumnDimension($col)->setWidth(15);
		$col++;
	}

	// 이미지 열 너비 조정
	$sheet->getColumnDimension('B')->setWidth(12);
	// 상품명 열 너비 조정
	$sheet->getColumnDimension('C')->setWidth(30);

	// 데이터 작성
	$row = 2;
	$excelTotalQty = 0;
	$excelTotalPrice = 0;
	for ($i=0; $i<count($_order_sec_json); $i++){
		if (!isset($_order_sec_json[$i]) || !is_array($_order_sec_json[$i])) {
			continue;
		}
		
		$_select_json = $_order_sec_json[$i]['selpd'] ?? [];
		$_oop_code = $oopCodeByIdx[(int)($_order_sec_json[$i]['bidx'] ?? 0)] ?? '';
		if (!is_array($_select_json)) {
			$_select_json = [];
		}

		for ($z=0; $z<count($_select_json); $z++){
			if (!isset($_select_json[$z]) || !is_array($_select_json[$z])) {
				continue;
			}

			$_idx = $_select_json[$z]['pidx'] ?? '';
			$_qty = $_select_json[$z]['qty'] ?? 0;
			if (!empty($_select_json[$z]['false']) || isset($ooFalsePidxMap[(int)($_select_json[$z]['pidx'] ?? 0)])) {
				continue;
			}

			$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
			$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx,ps_stock from prd_stock where ps_prd_idx = '".$_idx."' "));
			
			if (!is_array($comparison_data) || empty($comparison_data)) {
				$comparison_data = [
					'CD_IMG' => '', 'CD_INV_NAME1' => '', 'CD_NAME_OG' => '', 'CD_NAME' => '',
					'CD_CODE' => '', 'CD_CODE2' => '', 'CD_CODE3' => '', 'CD_INV_NAME2' => '',
					'CD_INV_MATERIAL' => '', 'CD_COO' => '', 'CD_IDX' => ''
				];
			}
			
			if (!is_array($stock_data) || empty($stock_data)) {
				$stock_data = ['ps_idx' => '', 'ps_stock' => 0];
			}

			$_price_fn = $comparison_data['CD_PRICE_FN'] ?? $comparison_data['cd_price_fn'] ?? [];
			if (is_string($_price_fn)) {
				$_price_fn = json_decode($_price_fn, true);
			}
			if (!is_array($_price_fn)) {
				$_price_fn = [];
			}
			$_unit_price = (float)($_price_fn[$_oop_code] ?? 0);
			$_qty_number = (float)preg_replace('/[,\s]/', '', (string)$_qty);
			$_total_price = $_unit_price * $_qty_number;
			$_qty_text = number_format($_qty_number, ($_qty_number == floor($_qty_number)) ? 0 : 2);
			$_unit_price_text = number_format($_unit_price, ($_unit_price == floor($_unit_price)) ? 0 : 2);
			$_total_price_text = number_format($_total_price, ($_total_price == floor($_total_price)) ? 0 : 2);

			// 인보이스명1 처리
			if( !empty($comparison_data['CD_INV_NAME1']) ){
				$_name_og = $comparison_data['CD_INV_NAME1'];
			}elseif( empty($comparison_data['CD_INV_NAME1']) && !empty($comparison_data['CD_NAME_OG']) ){
				$_name_og = $comparison_data['CD_NAME_OG'];
			}else{
				$_name_og = "";
			}

			// 이미지 경로 처리
			$photo_path = "";
			if( !empty($comparison_data['CD_IMG']) ){
				$_img_path = str_replace('../../data/comparion/', '', $comparison_data['CD_IMG']);
				$_ary_ck_img_name = explode("?", $_img_path);
				$photo_path = "../../data/comparion/".($_ary_ck_img_name[0] ?? '');
			}

			// 데이터 작성
			if( $_mode == "stock" ){
				$sheet->setCellValueExplicit('A'.$row, $stock_data['ps_idx'] ?? '', DataType::TYPE_STRING);
				$sheet->setCellValue('C'.$row, $comparison_data['CD_NAME'] ?? '');
				$sheet->setCellValueExplicit('D'.$row, $comparison_data['CD_CODE'] ?? '', DataType::TYPE_STRING);
				$sheet->setCellValueExplicit('E'.$row, $comparison_data['CD_CODE2'] ?? '', DataType::TYPE_STRING);
				$sheet->setCellValueExplicit('F'.$row, $comparison_data['CD_CODE3'] ?? '', DataType::TYPE_STRING);
				$sheet->setCellValue('G'.$row, $_qty);
			} else {
				$sheet->setCellValueExplicit('A'.$row, $stock_data['ps_idx'] ?? '', DataType::TYPE_STRING);
				$sheet->setCellValue('C'.$row, $comparison_data['CD_NAME'] ?? '');
				$sheet->setCellValue('D'.$row, $_name_og);
				$sheet->setCellValueExplicit('E'.$row, $comparison_data['CD_CODE2'] ?? '', DataType::TYPE_STRING);
				$sheet->setCellValue('F'.$row, $comparison_data['CD_INV_NAME2'] ?? '');
				$sheet->setCellValueExplicit('G'.$row, $_qty_text, DataType::TYPE_STRING);
				$sheet->setCellValueExplicit('H'.$row, $_unit_price_text, DataType::TYPE_STRING);
				$sheet->setCellValueExplicit('I'.$row, $_total_price_text, DataType::TYPE_STRING);
				$sheet->setCellValue('J'.$row, $comparison_data['CD_INV_MATERIAL'] ?? '');
				$sheet->setCellValueExplicit('K'.$row, $comparison_data['CD_CODE'] ?? '', DataType::TYPE_STRING);
				$sheet->setCellValueExplicit('L'.$row, $comparison_data['CD_CODE3'] ?? '', DataType::TYPE_STRING);
				$sheet->setCellValue('M'.$row, $comparison_data['CD_COO'] ?? '');
				$excelTotalQty += $_qty_number;
				$excelTotalPrice += $_total_price;
			}

			// 이미지 삽입
			if( !empty($photo_path) && file_exists($photo_path) ){
				$drawing = new Drawing();
				$drawing->setName('Product Image');
				$drawing->setDescription('Product Image');
				$drawing->setPath($photo_path);
				$drawing->setCoordinates('B'.$row);
				$drawing->setOffsetX(5);
				$drawing->setOffsetY(5);
				$drawing->setHeight(60);
				$drawing->setWorksheet($sheet);
				
				// 행 높이 조정
				$sheet->getRowDimension($row)->setRowHeight(65);
			} else {
				$sheet->setCellValue('B'.$row, '이미지없음');
			}

			// 행 스타일 설정
			$cellStyle = [
				'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
				'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
			];
			$lastCol = $_mode == "stock" ? 'G' : 'M';
			$sheet->getStyle('A'.$row.':'.$lastCol.$row)->applyFromArray($cellStyle);

			$row++;
		}
	}

	if ($_mode != "stock") {
		$sheet->mergeCells('A'.$row.':F'.$row);
		$sheet->setCellValue('A'.$row, '합계');
		$sheet->setCellValueExplicit('G'.$row, number_format($excelTotalQty, ($excelTotalQty == floor($excelTotalQty)) ? 0 : 2), DataType::TYPE_STRING);
		$sheet->setCellValueExplicit('I'.$row, number_format($excelTotalPrice, ($excelTotalPrice == floor($excelTotalPrice)) ? 0 : 2), DataType::TYPE_STRING);
		$sheet->getStyle('A'.$row.':M'.$row)->applyFromArray([
			'font' => ['bold' => true],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
			'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
		]);
	}

	// 다운로드 헤더 설정
	$filename = $_code."_".date('Ymd_Hi',time());
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
	header('Cache-Control: max-age=0');

	$writer = new Xlsx($spreadsheet);
	$writer->save('php://output');
	exit;
}
?>

<?php
include "../layout/header_popup.php";
?>
<STYLE TYPE="text/css">
.exel-table{}
.exel-table tr td{ border:1px solid #b4b4b4; padding:5px; }

.top_menu{ position:fixed; width:100%; height:50px; padding:10px 0 0 10px; border-bottom:1px solid #ddd; background-color:#f5f5f5; box-sizing:border-box;  }
.top_menu_back{ width:100%; height:50px; }

.print_body{ padding:10px; }
</STYLE>

<div class="top_menu">

	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="location.href='popup.order_sheet_print2.php?idx=<?=$_oo_idx?>&code=<?=$_code?>&excel=ok'" >엑셀출력</button>

<?php
	if( $_mode == "stock" ){
?>
	<input type='text' name='oo_name' id='stock_day' style="width:80px;" class="m-l-20" value="<?=date("Y-m-d")?>" >
	<input type='text' name='oo_name' id='stock_all_memo' style="width:200px" value="<?=($oo_data['oo_name'] ?? '')?> 입고"  >
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm " onclick="stockWrite()">재고등록</button>
<?php } ?>

		발주수량 : <b>상품 <?=number_format($orderProductCount)?>개(<?=number_format($orderQuantity)?>개)</b>
		/ 실패수량 : <b>상품 <?=number_format($failedProductCount)?>개(<?=number_format($failedQuantity)?>개)</b>
		/ 입고수량 : <b>상품 <?=number_format($stockInProductCount)?>개(<?=number_format($stockInQuantity)?>개)</b>

</div>
<div class="top_menu_back">
</div>

<div class="print_body">

<form id="form1" name="form1" method="post">
<input type="hidden" name="a_mode" value="stockWriteNew">
<input type="hidden" name="modify_idx" value="<?=$_oo_idx?>">

<table class="exel-table">
	<thead>
		<tr>
			<th>재고코드</th>
			<th>이미지</th>
			<th>한글 상품명</th>
			<th>원어명</th>
			<th>품번</th>
			<th>인보이스명</th>
			<th>주문수량</th>
			<th>주문단가<br>Unit Price</th>
			<th>합가격<br>Total Amount</th>
			<th>소재</th>
			<th>바코드</th>
			
			<th></th>
			<th>원산지</th>
			
		</tr>
	</thead>
	<tbody>
<?php

	// 배열 검증
	if (!is_array($_order_sec_json)) {
		$_order_sec_json = [];
	}
	$printTotalQty = 0;
	$printTotalPrice = 0;

	for ($i=0; $i<count($_order_sec_json); $i++){

		// 배열 요소 검증
		if (!isset($_order_sec_json[$i]) || !is_array($_order_sec_json[$i])) {
			continue;
		}
		
		$_os_data_idx = $_order_sec_json[$i]['bidx'] ?? '';
		$_oop_code = $oopCodeByIdx[(int)$_os_data_idx] ?? '';
		$_order_sec_data[$_os_data_idx]['item'] = $_order_sec_json[$i]['item'] ?? '';
		$_order_sec_data[$_os_data_idx]['qty'] = $_order_sec_json[$i]['qty'] ?? 0;
		$_order_sec_data[$_os_data_idx]['price'] = $_order_sec_json[$i]['price'] ?? 0;

		$_select_json = $_order_sec_json[$i]['selpd'] ?? [];
		
		// 배열 검증
		if (!is_array($_select_json)) {
			$_select_json = [];
		}

		for ($z=0; $z<count($_select_json); $z++){

			// 배열 요소 검증
			if (!isset($_select_json[$z]) || !is_array($_select_json[$z])) {
				continue;
			}

			$_idx = $_select_json[$z]['pidx'] ?? '';
			$_qty = $_select_json[$z]['qty'] ?? 0;
			if (!empty($_select_json[$z]['false']) || isset($ooFalsePidxMap[(int)($_select_json[$z]['pidx'] ?? 0)])) {
				continue;
			}

			$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
			$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx,ps_stock from prd_stock where ps_prd_idx = '".$_idx."' "));
			
			// 배열 검증
			if (!is_array($comparison_data) || empty($comparison_data)) {
				$comparison_data = [
					'CD_IMG' => '',
					'CD_INV_NAME1' => '',
					'CD_NAME_OG' => '',
					'CD_NAME' => '',
					'CD_CODE' => '',
					'CD_CODE2' => '',
					'CD_CODE3' => '',
					'CD_INV_NAME2' => '',
					'CD_INV_MATERIAL' => '',
					'CD_COO' => '',
					'CD_IDX' => ''
				];
			}
			
			if (!is_array($stock_data) || empty($stock_data)) {
				$stock_data = ['ps_idx' => '', 'ps_stock' => 0];
			}

			$img_path = "";
			if( !empty($comparison_data['CD_IMG']) ){
				$img_path = 'https://dnfixhead.mycafe24.com//data/comparion/'.$comparison_data['CD_IMG'];
			}

			$_img_path = str_replace('../../data/comparion/','', $comparison_data['CD_IMG'] ?? '');
			//$img_name = "../../data/comparion/".$_img_name;
			$_ary_ck_img_name = explode("?", $_img_path);
			$photo_path = "../../data/comparion/".($_ary_ck_img_name[0] ?? ''); // 이미지 경로

			$ext = strtolower(pathinfo($comparison_data['CD_IMG'] ?? '', PATHINFO_EXTENSION));

			if( !empty($comparison_data['CD_INV_NAME1']) ){
				$_name_og = $comparison_data['CD_INV_NAME1'];
			}elseif( empty($comparison_data['CD_INV_NAME1']) && !empty($comparison_data['CD_NAME_OG']) ){
				$_name_og = $comparison_data['CD_NAME_OG'];
			}else{
				$_name_og = "";
			}

			$_price_fn = $comparison_data['CD_PRICE_FN'] ?? $comparison_data['cd_price_fn'] ?? [];
			if (is_string($_price_fn)) {
				$_price_fn = json_decode($_price_fn, true);
			}
			if (!is_array($_price_fn)) {
				$_price_fn = [];
			}
			$_unit_price = (float)($_price_fn[$_oop_code] ?? 0);
			$_unit_price_text = number_format($_unit_price, ($_unit_price == floor($_unit_price)) ? 0 : 2);
			$_qty_number = (float)preg_replace('/[,\s]/', '', (string)$_qty);
			$_total_price = $_unit_price * $_qty_number;
			$_total_price_text = number_format($_total_price, ($_total_price == floor($_total_price)) ? 0 : 2);
			if ($_mode !== "stock") {
				$printTotalQty += $_qty_number;
				$printTotalPrice += $_total_price;
			}

?>
	<tr bgcolor="<?=$trcolor ?? ''?>">
		<td class="xl65"><?=$stock_data['ps_idx'] ?? ''?></td>
		<td class="xl65">
			<?php if( !empty($comparison_data['CD_IMG']) ){?><img src="<?=$img_path?>" width="80px" height="80px"><?php } ?>
<!-- 
	<br><?=$photo_path?>
			<br><?=$ext?>
			<br><br><?=$_img_path?>
			<br>
 -->
		</td>
		<td class="xl65" style="cursor:pointer;" onclick="onlyAD.prdView('<?=$comparison_data['CD_IDX'] ?? ''?>','info');"><?=$comparison_data['CD_NAME'] ?? ''?></td>

<?php
if( $_mode == "stock" ){
	$ps_idx = $stock_data['ps_idx'] ?? '';
?>
		<td class="xl65"><?=$comparison_data['CD_CODE'] ?? ''?></td>
		<td class="xl65"><?=$comparison_data['CD_CODE2'] ?? ''?></td>
		<td class="xl65"><?=$comparison_data['CD_CODE3'] ?? ''?></td>
		<td class="xl65"><?=$_qty?></td>
		<td style="width:50px;">
			<input type="hidden" name="key_check[]" value="<?=$ps_idx?>">
			<input type="hidden" name='unit_stock_<?=$ps_idx?>'  value="<?=$_qty?>">
			<input type='text' name='unit_modify_stock_<?=$ps_idx?>' id="unit_stock_<?=$ps_idx?>" style="width:100%; font-size:15px; font-weight:bold; color:#021aff;" >
		</td>
		<td class="text-left" style="width:150px;">
			<input type='text' name='unit_stock_memo_<?=$ps_idx?>' id="unit_stock_memo_<?=$ps_idx?>" value="" style="width:100%; height:20px;">
		</td>
<?php }else{ ?>
		<td class="xl65"><?=$_name_og?></td>
		<td class="xl65"><?=$comparison_data['CD_CODE2'] ?? ''?></td>
		<td class="xl65"><?=$comparison_data['CD_INV_NAME2'] ?? ''?></td>
		<td class="xl65"><?=$_qty?></td>
		<td class="xl65"><?=$_unit_price_text?></td>
		<td class="xl65"><?=$_total_price_text?></td>
		<td class="xl65"><?=$comparison_data['CD_INV_MATERIAL'] ?? ''?></td>
		<td class="xl65"><?=$comparison_data['CD_CODE'] ?? ''?></td>
		
		<td class="xl65"><?=$comparison_data['CD_CODE3'] ?? ''?></td>
		<td class="xl65"><?=$comparison_data['CD_COO'] ?? ''?></td>
		
<?php } ?>

	</tr>
<?php
	} }
?>
</tbody>
<tfoot>
<?php if ($_mode !== "stock") { ?>
	<tr>
		<td class="xl65 text-right" colspan="6"><b>합계</b></td>
		<td class="xl65"><b><?=number_format($printTotalQty, ($printTotalQty == floor($printTotalQty)) ? 0 : 2)?></b></td>
		<td class="xl65"></td>
		<td class="xl65"><b><?=number_format($printTotalPrice, ($printTotalPrice == floor($printTotalPrice)) ? 0 : 2)?></b></td>
		<td class="xl65" colspan="4"></td>
	</tr>
<?php } ?>
</tfoot>
</table>
</form>

</div>

<script type="text/javascript"> 
<!-- 
//가격비교 퀵 창
function comparisonQuick(idx, vmode){
	if( vmode == undefined ) vmode = "comparison"; 
	window.open("/admin2/comparison/popup.comparison_modify.php?idx="+ idx +"&vmode="+vmode, "comparison_quick_"+idx, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

function stockWrite(mode){

	var stock_all_memo = $("#stock_all_memo").val();
	var stock_day = $("#stock_day").val();

	//var formData = $("#form1").serialize();
	var formData = $("#form1").serializeArray();
	formData.push({name: "stock_all_memo", value: stock_all_memo});
	formData.push({name: "stock_day", value: stock_day});

	$.ajax({
		cache : false,
		url : "processing.order_sheet.php",
		type : 'POST', 
		data : formData, 
		success : function(res) {
			//alert(res.msg);
			opener.location.reload();
			window.close();
		}, // success 
		error : function(res) {

		}
	}); // $.ajax

}
//--> 
</script> 
<?php
include "../layout/footer_popup.php";
exit;
?>