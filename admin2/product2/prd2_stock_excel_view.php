<?
include "../lib/inc_common.php";

	$_idx = securityVal($idx);
	$_sort = securityVal($sort);
	$_mode = securityVal($mode);

	$data = wepix_fetch_array(wepix_query_error("select file_name, data, error from prd_stock_history where uid = '".$_idx."' "));


	$_json_data = json_decode($data['data'],true);
	$_error_data = json_decode($data['error'],true);


	if( $_sort == "brand" ){
		$_json_data = arr_sort( $_json_data,'brand_idx', 'desc' );
	}else{
		$_json_data = arr_sort( $_json_data,'qty', 'desc' );
	}

if( $_mode == "excelDown" ){

	require_once("../../class/phpexcel/PHPExcel.php");

	$objPHPExcel = new PHPExcel();

	$objPHPExcel -> setActiveSheetIndex(0)
	-> setCellValue("A1", "재고코드")
	-> setCellValue("B1", "이미지")
	-> setCellValue("C1", "브랜드명")
	-> setCellValue("D1", "상품명")
	-> setCellValue("E1", "바코드")
	-> setCellValue("F1", "수량")
	-> setCellValue("G1", "패제거");

	$num = 1;
	$count = 1;

	for ($i=0; $i<count($_json_data); $i++){
		$num++;

		$_ps_idx = $_json_data[$i]['ps_idx'];
		$prd_data = wepix_fetch_array(wepix_query_error("select 
			A.ps_stock,
			B.CD_NAME, B.CD_IMG, B.CD_CODE,
			C.BD_NAME 
			from prd_stock A 
			left join "._DB_COMPARISON." B ON (A.ps_prd_idx = B.CD_IDX ) 
			left join "._DB_BRAND." C  ON (B.CD_BRAND_IDX = C.BD_IDX ) 
			where ps_idx = '".$_ps_idx."' "));

		if( $_json_data[$i]['packageOut'] > 0 ){
			$_packageOut = $_json_data[$i]['packageOut'];
		}else{
			$_packageOut = "";
		}

		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A".$num, $_json_data[$i]['ps_idx'])
		-> setCellValue("B".$num, "")
		-> setCellValue("C".$num, $prd_data[BD_NAME])
		-> setCellValue("D".$num, $prd_data[CD_NAME])
		-> setCellValue("E".$num, $prd_data[CD_CODE])
		-> setCellValue("F".$num, $_json_data[$i]['qty'])
		-> setCellValue("G".$num, $_packageOut);

		$iCol = "B"; // 컬럼번호

		$_ary_ck_img_name = "";
		$_img_path = str_replace('../../data/comparion/','', $prd_data[CD_IMG]);
		$_ary_ck_img_name = explode("?", $_img_path);
		$_photo_path2 = "../../data/comparion/".$_ary_ck_img_name[0]; // 이미지 경로
			
		if (file_exists($_photo_path2)) {
			$photo_path = $_photo_path2;
		}else{
			$photo_path = "../img/excel_no_img.png";
		}

		if( !$prd_data['CD_IMG'] ) $photo_path = "../img/excel_no_img.png";

		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Photo '.$num);
		$objDrawing->setDescription('Photo '.$num);
		$objDrawing->setPath($photo_path);
		$objDrawing->setResizeProportional(true);
		$objDrawing->setWidth(80);
		$objDrawing->setOffsetX(0);
		$objDrawing->setOffsetY(0);
		$objDrawing->setCoordinates($iCol.$num);
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		$objPHPExcel->getActiveSheet()->getRowDimension($num)->setRowHeight(60); // 행높이 설정

		$count++;
	
	} //for END

	$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(9);
	$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(12);
	$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(15);
	$objPHPExcel -> getActiveSheet() -> getColumnDimension("D") -> setWidth(60);
	$objPHPExcel -> getActiveSheet() -> getColumnDimension("E") -> setWidth(17);
	$objPHPExcel -> getActiveSheet() -> getColumnDimension("G") -> setWidth(17);

	$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:G%s", $count)) -> getBorders() -> getAllBorders() -> setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:G%s", $count)) -> getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:G%s", $count)) -> getAlignment() -> setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER );
	$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:G%s", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:G%s", $count)) -> getFont() -> setName('굴림') -> setSize(9);
	$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("E1:E%s", $count)) -> getFont() -> setBold(true) -> setName('굴림') -> setSize(10);
	$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("F1:F%s", $count)) -> getFont() -> setBold(true) -> setName('굴림') -> setSize(10);
	$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("G1:G%s", $count)) -> getFont() -> setBold(true) -> setName('굴림') -> setSize(10);

	$objPHPExcel -> getActiveSheet() -> getStyle("A1:G1") -> getFont() -> setBold(true);
	$objPHPExcel -> getActiveSheet() -> getStyle("A1:G1") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("CECBCA");
/*
	$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:D%s", $count)) -> getBorders() -> getAllBorders() -> setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
*/
	$_code = str_replace('.csv','', $data[file_name]);
	$filename = "주문수량_".$_code."_".date('Ymd_Hi',time());

	header("Content-Type:application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header("Cache-Control:max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter -> save("php://output");

	exit;

}
?>

<?
if( count($_error_data['result']) > 0 ){
?>
에러 항목
<div>
	<? for ($i=0; $i<count($_error_data['result']); $i++){ ?>
	<ul><?=$_error_data['result'][$i]?></ul>
	<? } ?>
</div>
<? } ?>

<form id="form2">
<input type="hidden" name="a_mode" value="day_stock">

<table class="table-list" id="">
	<tr>
		<th>재고<br>코드</th>
		<th>브랜드</th>
		<th>상품명</th>
		<th>패킹<br>재거</th>
		<th>단일<br>상품</th>
		<th>세트<br>상품</th>
		<th>출고<br>총합</th>
		<th>현재<br>재고</th>
		<th>남는<br>재고</th>
		<th></th>
		<th></th>
	</tr>
<?

for ($i=0; $i<count($_json_data); $i++){

	$_ps_idx = $_json_data[$i]['ps_idx'];
	$prd_data = wepix_fetch_array(wepix_query_error("select 
		A.ps_stock,
		B.CD_NAME, B.CD_IMG, B.CD_CODE,
		C.BD_NAME 
		from prd_stock A 
		left join "._DB_COMPARISON." B ON (A.ps_prd_idx = B.CD_IDX ) 
		left join "._DB_BRAND." C  ON (B.CD_BRAND_IDX = C.BD_IDX ) 
		where ps_idx = '".$_ps_idx."' "));

	$img_path = '../../data/comparion/'.$prd_data[CD_IMG];

	$_stock_sum = ($prd_data['ps_stock'] - $_json_data[$i]['qty']);

?>
<input type='hidden' name='stock_key[]' value='<?=$_ps_idx?>'>
<input type='hidden' name='stock_mode[]' class="stock-mode-value" value='minus'>
<input type='hidden' name='stock_kind[]' value='판매 (엑셀)'>

	<tr>
		<td><?=$_json_data[$i]['ps_idx']?></td>
<? if( $_mode == "excelDown" ){ ?>
		<td><img src="<?=$img_path?>" style="width:70px; "></td>
<? } ?>
		<td><?=$prd_data[BD_NAME]?></td>
		
<? if( $_mode == "excelDown" ){ ?>
		<td><?=$prd_data[CD_CODE]?></td>
		<td><?=$_json_data[$i]['qty']?></td>
		<td class="text-left"><?=$prd_data['CD_NAME']?></td>
<? }else{ ?>
		<td class="text-left">
			<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?=$_json_data[$i]['cd_idx']?>','info');"">보기</button>
			<?=$prd_data['CD_NAME']?>
		</td>
		<td><? if( $_json_data[$i]['packageOut'] > 0 ){ ?><?=$_json_data[$i]['packageOut']?><? } ?></td>
		<td><?=$_json_data[$i]['one']['qty']?></td>
		<td><?=$_json_data[$i]['set']['qty']?></td>
		<td><input type="text" name="stock_qry[]" style="width:40px; font-size:15px; font-weight:bold; color:#d00000;" placeholder="수량" value="<?=$_json_data[$i]['qty']?>" /></td>
		<td><?=$prd_data['ps_stock']?></td>
		<td><?=$_stock_sum?></td>
		<td class="stock-mode-text">출고</td>
		<td><input type="text" name="stock_memo[]" class="stock-memo" style="width:80px;" value="카페24 엑셀등록" /></td>
<? } ?>

	</tr>
<? } ?>
</table>
</form>