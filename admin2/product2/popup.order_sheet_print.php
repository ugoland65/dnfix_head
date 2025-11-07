<?
include "../lib/inc_common.php";
$pageGroup = "product2";
$pageName = "order_sheet_print_popup";

// PHPExcel 사용 안함 - 주석처리
// require_once("../../class/phpexcel/PHPExcel.php");

// 변수 초기화
$idx = $_GET['idx'] ?? $_POST['idx'] ?? "";
$code = $_GET['code'] ?? $_POST['code'] ?? "";
$excel = $_GET['excel'] ?? $_POST['excel'] ?? "";
$excel_mode = $_GET['excel_mode'] ?? $_POST['excel_mode'] ?? "";

$_oo_idx = securityVal($idx);
$_code = securityVal($code);
$_excel = securityVal($excel);
$_excel_mode = securityVal($excel_mode);

// 엑셀 다운로드 요청 시 메시지 출력
if( $_excel == "ok" ){
	echo "<script>alert('PHPExcel 기능이 비활성화되었습니다.'); history.back();</script>";
	exit;
}

/* PHPExcel 사용 안함 - 주석처리 시작
$objPHPExcel = new PHPExcel();

if( $_excel_mode == "noimg" ){

	$objPHPExcel -> setActiveSheetIndex(0)
	-> setCellValue("A1", "재고코드")
	-> setCellValue("B1", "상품명(국문)")
	-> setCellValue("C1", "상품명(일어)")
	-> setCellValue("D1", "상품명(영문)")
	-> setCellValue("E1", "소재")
	-> setCellValue("F1", "JAN")
	-> setCellValue("G1", "CODE2")
	-> setCellValue("H1", "CODE3")
	-> setCellValue("I1", "QTY");

}else{
	$objPHPExcel -> setActiveSheetIndex(0)
	-> setCellValue("A1", "재고코드")
	-> setCellValue("B1", "이미지")
	-> setCellValue("C1", "상품명(국문)")
	-> setCellValue("D1", "상품명(일어)")
	-> setCellValue("E1", "상품명(영문)")
	-> setCellValue("F1", "소재")
	-> setCellValue("G1", "JAN")
	-> setCellValue("H1", "CODE2")
	-> setCellValue("I1", "CODE3")
	-> setCellValue("J1", "제조국")
	-> setCellValue("K1", "QTY");
}


$ona_order_data = wepix_fetch_array(wepix_query_error("select * from ona_order where oo_idx = '".$_oo_idx."' "));

$_ary_save_data_oo_c_idx = explode(",", $ona_order_data[oo_c_idx]);
$_ary_save_data_oo_memo = explode("★", $ona_order_data[oo_memo]);
$_ary_save_data_oo_qty = explode(",", $ona_order_data[oo_qty]);
$_ary_save_data_oo_unit_state = explode(",", $ona_order_data[oo_unit_state]);

/*
echo $ona_order_data[oo_c_idx];
echo "<br>";
echo $ona_order_data[oo_memo];
echo "<br>";
echo $ona_order_data[oo_qty];
exit;
*/

$num = 1;

for ($i=0; $i<count($_ary_save_data_oo_c_idx); $i++){

	$_idx = $_ary_save_data_oo_c_idx[$i];
	$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
	$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx,ps_stock from prd_stock where ps_prd_idx = '".$_idx."' "));

	if( $comparison_data[CD_INV_NAME1] ){
		$_name_og = $comparison_data[CD_INV_NAME1];
	}elseif( !$comparison_data[CD_INV_NAME1] && $comparison_data[CD_NAME_OG] ){
		$_name_og = $comparison_data[CD_NAME_OG];
	}else{
		$_name_og = "";
	}


	if( $stock_data[ps_idx] ){
		$_ps_idx = $stock_data[ps_idx];
	}else{
		$_ps_idx = "재고코드없음";
	}
/* --------------------------------------------------------------------------------------- */
	//$num = $i + 1;
	$num++;

if( $_excel == "ok" ){



	if( $_excel_mode == "noimg" ){
		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A".$num, $_ps_idx)
		-> setCellValue("B".$num, $comparison_data[CD_NAME])
		-> setCellValue("C".$num, $_name_og)
		-> setCellValue("D".$num, $comparison_data[CD_INV_NAME2])
		-> setCellValue("E".$num, $comparison_data[CD_INV_MATERIAL])
		-> setCellValueExplicit("F".$num, $comparison_data[CD_CODE])
		-> setCellValue("G".$num, $comparison_data[CD_CODE2])
		-> setCellValue("H".$num, $comparison_data[CD_CODE3])
		-> setCellValue("I".$num, $_ary_save_data_oo_qty[$i]);

	}else{



		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A".$num, $_ps_idx)
		-> setCellValue("B".$num, "")
		-> setCellValue("C".$num, $comparison_data[CD_NAME])
		-> setCellValue("D".$num, $_name_og)
		-> setCellValue("E".$num, $comparison_data[CD_INV_NAME2])
		-> setCellValue("F".$num, $comparison_data[CD_INV_MATERIAL])
		-> setCellValueExplicit("G".$num, $comparison_data[CD_CODE])
		-> setCellValue("H".$num, $comparison_data[CD_CODE2])
		-> setCellValue("I".$num, $comparison_data[CD_CODE3])
		-> setCellValue("J".$num, $comparison_data[CD_COO])
		-> setCellValue("k".$num, $_ary_save_data_oo_qty[$i]);

	}

	if( $_excel_mode != "noimg" && $comparison_data[CD_IMG] ){
		
		$iCol = "B"; // 컬럼번호
		$iRow = $num; // 행번호

/*
		$_ary_ck_img_name = explode(".", $comparison_data[CD_IMG]);
		$_img_name = $_ary_ck_img_name[0].".jpg";

		$photo_path = "../../data/comparion/".$_img_name; // 이미지 경로

		// 확장자 가져오기
		$ext = strtolower(pathinfo($_out_img, PATHINFO_EXTENSION));
*/
		$_img_path = str_replace('../../data/comparion/','', $comparison_data[CD_IMG]);
		$_ary_ck_img_name = explode("?", $_img_path);
		$photo_path = "../../data/comparion/".$_ary_ck_img_name[0]; // 이미지 경로


		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Photo '.$iRow);
		$objDrawing->setDescription('Photo '.$iRow);
		$objDrawing->setPath($photo_path);
		$objDrawing->setResizeProportional(true);
		$objDrawing->setWidth(80);
		$objDrawing->setOffsetX(0);
		$objDrawing->setOffsetY(0);
		$objDrawing->setCoordinates($iCol.$iRow);
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		$objPHPExcel->getActiveSheet()->getRowDimension($iRow)->setRowHeight(60); // 행높이 설정



	}

	$objPHPExcel->getActiveSheet()->getStyle ( "A".$num.":k".$num )->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER );
}
/* --------------------------------------------------------------------------------------- */
}

/*
	if( $_code == "mg" ){
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(12);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(20);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("E") -> setWidth(14);
		$objPHPExcel -> getActiveSheet() -> getStyle("F") -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel -> getActiveSheet() -> getStyle("G") -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}elseif( $_code == "tma" ){
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(20);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(14);
		$objPHPExcel -> getActiveSheet() -> getStyle("C") -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel -> getActiveSheet() -> getStyle("D") -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}else{
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(12);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(20);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("E") -> setWidth(14);
		$objPHPExcel -> getActiveSheet() -> getStyle("F") -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
*/

if( $_excel == "ok" ){

	$filename = $_code."_".date('Ymd_Hi',time());

	header("Content-Type:application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header("Cache-Control:max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter -> save("php://output");
}
?>

<?
include "../layout/header_popup.php";
?>
<STYLE TYPE="text/css">
.exel-table{}
.exel-table tr td{ border:1px solid #b4b4b4; padding:5px; }
</STYLE>

<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="location.href='popup.order_sheet_print.php?idx=<?=$_oo_idx?>&code=<?=$_code?>&excel=ok'" >엑셀출력</button>
<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="location.href='popup.order_sheet_print.php?idx=<?=$_oo_idx?>&code=<?=$_code?>&excel=ok&excel_mode=noimg'" >이미지 없이 엑셀출력</button>

<table class="exel-table">
<!-- 
<thead>
<tr>
<th class="xl65">번호</th>
</tr>
</thead>
 -->
<tbody>
<?
for ($i=0; $i<count($_ary_save_data_oo_c_idx); $i++){
	$_idx = $_ary_save_data_oo_c_idx[$i];
	$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
	$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx,ps_stock from prd_stock where ps_prd_idx = '".$_idx."' "));
	
	$img_path = "";
	if($comparison_data[CD_IMG] ){
		$img_path = 'http://dgmall.wepix-hosting.co.kr/data/comparion/'.$comparison_data[CD_IMG];
	}

	$_img_path = str_replace('../../data/comparion/','', $comparison_data[CD_IMG]);
		//$img_name = "../../data/comparion/".$_img_name;
	$_ary_ck_img_name = explode("?", $_img_path);
	$photo_path = "../../data/comparion/".$_ary_ck_img_name[0]; // 이미지 경로

	$ext = strtolower(pathinfo($comparison_data[CD_IMG], PATHINFO_EXTENSION));

	if( $comparison_data[CD_INV_NAME1] ){
		$_name_og = $comparison_data[CD_INV_NAME1];
	}elseif( !$comparison_data[CD_INV_NAME1] && $comparison_data[CD_NAME_OG] ){
		$_name_og = $comparison_data[CD_NAME_OG];
	}else{
		$_name_og = "";
	}
?>
	<tr bgcolor="<?=$trcolor?>">
		<td class="xl65"><?=$stock_data[ps_idx]?></td>
		<td class="xl65">
			<?if($comparison_data[CD_IMG] ){?><img src="<?=$img_path?>" width="80px" height="80px"><? } ?>
<!-- 
			<br><?=$ext?>
			<br><br><?=$_img_path?>
			<br><br><?=$photo_path?>
 -->
		</td>
		<td class="xl65" style="cursor:pointer;" onclick="comparisonQuick('<?=$comparison_data[CD_IDX]?>','info');"><?=$comparison_data[CD_NAME]?></td>
		<td class="xl65"><?=$_name_og?></td>
		<td class="xl65"><?=$comparison_data[CD_INV_NAME2]?></td>
		<td class="xl65"><?=$comparison_data[CD_INV_MATERIAL]?></td>
		<td class="xl65"><?=$comparison_data[CD_CODE]?></td>
		<td class="xl65"><?=$comparison_data[CD_CODE2]?></td>
		<td class="xl65"><?=$comparison_data[CD_CODE3]?></td>
		<td class="xl65"><?=$comparison_data[CD_COO]?></td>
		<td class="xl65"><?=$_ary_save_data_oo_qty[$i]?></td>
	</tr>
<?
} 



?>
</tbody>
</table>
<script type="text/javascript"> 
<!-- 
//가격비교 퀵 창
function comparisonQuick(idx, vmode){
	if( vmode == undefined ) vmode = "comparison"; 
	window.open("/admin2/comparison/popup.comparison_modify.php?idx="+ idx +"&vmode="+vmode, "comparison_quick_"+idx, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
//--> 
</script> 
<?
include "../layout/footer_popup.php";
exit;
?>
