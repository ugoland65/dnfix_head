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
$mode = $_GET['mode'] ?? $_POST['mode'] ?? "";

$_oo_idx = securityVal($idx);
$_code = securityVal($code);
$_excel = securityVal($excel);
$_excel_mode = securityVal($excel_mode);
$_mode = securityVal($mode);

// 엑셀 다운로드 요청 시 메시지 출력
if( $_excel == "ok" ){
	echo "<script>alert('PHPExcel 기능이 비활성화되었습니다.'); history.back();</script>";
	exit;
}

/* PHPExcel 사용 안함 - 주석처리 시작
$objPHPExcel = new PHPExcel();

if( $_excel == "ok" ){
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

	//이값이 있다는것은 수정임
	if( $_oo_idx ){

		$btn_text_submit = "주문서 수정";

		$oo_data = wepix_fetch_array(wepix_query_error("select * from ona_order where oo_idx = '".$_oo_idx."' "));

		$_order_sec_json2 = $oo_data['oo_json'];
		$_order_sec_json = json_decode($_order_sec_json2,true);

	}
/*
	echo "<pre>";
	print_r($_order_sec_json);
	echo "</pre>";
	exit;
*/

if( $_excel == "ok" ){


	//$num = 1;
	$num = 1;
	$count = 1;

	for ($i=0; $i<count($_order_sec_json); $i++){
	//for ($i=0; $i<17; $i++){

		$_os_data_idx = $_order_sec_json[$i]['bidx'];
		$_order_sec_data[$_os_data_idx]['item'] = $_order_sec_json[$i]['item'];
		$_order_sec_data[$_os_data_idx]['qty'] = $_order_sec_json[$i]['qty'];
		$_order_sec_data[$_os_data_idx]['price'] = $_order_sec_json[$i]['price'];

		$_select_json = $_order_sec_json[$i]['selpd'];


		for ($z=0; $z<count($_select_json); $z++){ 
		//for ($z=0; $z<4; $z++){ 

			$num++;
			$count++;

			$_idx = $_select_json[$z]['pidx'];
			$_qty = $_select_json[$z]['qty'];

			$prd_data = wepix_fetch_array(wepix_query_error("select 
				A.CD_NAME, A.CD_IMG, A.CD_CODE, A.CD_CODE2, A.cd_code_fn, A.CD_INV_NAME1, A.CD_INV_NAME2, A.CD_INV_MATERIAL, A.CD_NAME_OG, A.CD_COO,
				B.ps_idx,
				C.BD_NAME 
				from "._DB_COMPARISON." A 
				left join prd_stock B ON (A.CD_IDX = B.ps_prd_idx ) 
				left join "._DB_BRAND." C  ON (A.CD_BRAND_IDX = C.BD_IDX ) 
				where CD_IDX = '".$_idx."' "));

			/*
			$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
			$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx,ps_stock from prd_stock where ps_prd_idx = '".$_idx."' "));
			*/

			$_cd_code_data = json_decode($prd_data['cd_code_fn'], true);	
			

			if( $prd_data['CD_INV_NAME1'] ){
				$_name_og = $prd_data['CD_INV_NAME1'];
			}elseif( !$prd_data['CD_INV_NAME1'] && $prd_data['CD_NAME_OG'] ){
				$_name_og = $prd_data['CD_NAME_OG'];
			}else{
				$_name_og = "-";
			}


			if( $prd_data['ps_idx'] ){
				$_ps_idx = $prd_data['ps_idx'];
			}else{
				$_ps_idx = "X";
			}

			$_jancode = $prd_data['CD_CODE'];
			if( $prd_data['CD_CODE'] == "undefined" ) $_jancode = "";

			$_code3 = $_cd_code_data[$_code];
			if( $_cd_code_data[$_code] == "undefined" ) $_code3 = "";

			$objPHPExcel -> setActiveSheetIndex(0)
			-> setCellValue("A".$num, $_ps_idx)
			-> setCellValue("B".$num, "")
			-> setCellValue("C".$num, $prd_data['CD_NAME'])
			-> setCellValue("D".$num, $_name_og)
			-> setCellValue("E".$num, $prd_data['CD_INV_NAME2'])
			-> setCellValue("F".$num, $prd_data['CD_INV_MATERIAL'])
			-> setCellValueExplicit("G".$num, $_jancode)
			-> setCellValue("H".$num, $prd_data['CD_CODE2'])
			-> setCellValue("I".$num, $_code3)
			-> setCellValue("J".$num, $prd_data['CD_COO'])
			-> setCellValue("k".$num, $_qty);

			$iCol = "B"; // 컬럼번호

			$_ary_ck_img_name = "";
			$_img_path = str_replace('../../data/comparion/','', $prd_data['CD_IMG']);
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
			
			//$objPHPExcel->getActiveSheet()->getStyle ( "A".$num.":k".$num )->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER );

		} // for Z END




	}

		$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(9);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(12);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(15);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("D") -> setWidth(60);
		$objPHPExcel -> getActiveSheet() -> getColumnDimension("G") -> setWidth(17);

		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:K%s", $count)) -> getBorders() -> getAllBorders() -> setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:K%s", $count)) -> getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:K%s", $count)) -> getAlignment() -> setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER );
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:K%s", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:K%s", $count)) -> getFont() -> setName('굴림') -> setSize(9);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("F1:F%s", $count)) -> getFont() -> setBold(true) -> setName('굴림') -> setSize(10);
		$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("J1:J%s", $count)) -> getFont() -> setBold(true) -> setName('굴림') -> setSize(10);

		$objPHPExcel -> getActiveSheet() -> getStyle("A1:K1") -> getFont() -> setBold(true);
		$objPHPExcel -> getActiveSheet() -> getStyle("A1:K1") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("CECBCA");

}

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

.top_menu{ position:fixed; width:100%; height:50px; padding:10px 0 0 10px; border-bottom:1px solid #ddd; background-color:#f5f5f5; box-sizing:border-box;  }
.top_menu_back{ width:100%; height:50px; }

.print_body{ padding:10px; }
</STYLE>

<div class="top_menu">

	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="location.href='popup.order_sheet_print2.php?idx=<?=$_oo_idx?>&code=<?=$_code?>&excel=ok'" >엑셀출력</button>

<?
if( $_mode == "stock" ){
?>
	<input type='text' name='oo_name' id='stock_day' style="width:80px;" class="m-l-20" value="<?=date("Y-m-d")?>" >
	<input type='text' name='oo_name' id='stock_all_memo' style="width:200px" value="<?=$oo_data['oo_name']?> 입고"  >
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm " onclick="stockWrite()">재고등록</button>
<? } ?>

</div>
<div class="top_menu_back">
</div>

<div class="print_body">

<form id="form1" name="form1" method="post">
<input type="hidden" name="a_mode" value="stockWriteNew">
<input type="hidden" name="modify_idx" value="<?=$_oo_idx?>">

<table class="exel-table">
<?
for ($i=0; $i<count($_order_sec_json); $i++){
	$_os_data_idx = $_order_sec_json[$i]['bidx'];
	$_order_sec_data[$_os_data_idx]['item'] = $_order_sec_json[$i]['item'];
	$_order_sec_data[$_os_data_idx]['qty'] = $_order_sec_json[$i]['qty'];
	$_order_sec_data[$_os_data_idx]['price'] = $_order_sec_json[$i]['price'];

	$_select_json = $_order_sec_json[$i]['selpd'];

	for ($z=0; $z<count($_select_json); $z++){ 

		$_idx = $_select_json[$z]['pidx'];
		$_qty = $_select_json[$z]['qty'];

		$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
		$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx,ps_stock from prd_stock where ps_prd_idx = '".$_idx."' "));

		$img_path = "";
		if($comparison_data['CD_IMG'] ){
			$img_path = 'http://dgmall.wepix-hosting.co.kr/data/comparion/'.$comparison_data['CD_IMG'];
		}

		$_img_path = str_replace('../../data/comparion/','', $comparison_data['CD_IMG']);
			//$img_name = "../../data/comparion/".$_img_name;
		$_ary_ck_img_name = explode("?", $_img_path);
		$photo_path = "../../data/comparion/".$_ary_ck_img_name[0]; // 이미지 경로

		$ext = strtolower(pathinfo($comparison_data['CD_IMG'], PATHINFO_EXTENSION));

		if( $comparison_data['CD_INV_NAME1'] ){
			$_name_og = $comparison_data['CD_INV_NAME1'];
		}elseif( !$comparison_data['CD_INV_NAME1'] && $comparison_data['CD_NAME_OG'] ){
			$_name_og = $comparison_data['CD_NAME_OG'];
		}else{
			$_name_og = "";
		}

?>
	<tr bgcolor="<?=$trcolor?>">
		<td class="xl65"><?=$stock_data['ps_idx']?></td>
		<td class="xl65">
			<?if($comparison_data['CD_IMG'] ){?><img src="<?=$img_path?>" width="80px" height="80px"><? } ?>
<!-- 
	<br><?=$photo_path?>
			<br><?=$ext?>
			<br><br><?=$_img_path?>
			<br>
 -->
		</td>
		<td class="xl65" style="cursor:pointer;" onclick="onlyAD.prdView('<?=$comparison_data['CD_IDX']?>','info');"><?=$comparison_data['CD_NAME']?></td>

<?
if( $_mode == "stock" ){
?>
		<td class="xl65"><?=$comparison_data['CD_CODE']?></td>
		<td class="xl65"><?=$comparison_data['CD_CODE2']?></td>
		<td class="xl65"><?=$comparison_data['CD_CODE3']?></td>
		<td class="xl65"><?=$_qty?></td>
		<td style="width:50px;">
			<input type="hidden" name="key_check[]" value="<?=$stock_data['ps_idx']?>">
			<input type="hidden" name='unit_stock_<?=$stock_data['ps_idx']?>'  value="<?=$_qty?>">
			<input type='text' name='unit_modify_stock_<?=$stock_data['ps_idx']?>' id="unit_stock_<?=$stock_data['ps_idx']?>" style="width:100%; font-size:15px; font-weight:bold; color:#021aff;" >
		</td>
		<td class="text-left" style="width:150px;">
			<input type='text' name='unit_stock_memo_<?=$stock_data['ps_idx']?>' id="unit_stock_memo_<?=$stock_data['ps_idx']?>" value="" style="width:100%; height:20px;">
		</td>
<? }else{ ?>
		<td class="xl65"><?=$_name_og?></td>
		<td class="xl65"><?=$comparison_data['CD_INV_NAME2']?></td>
		<td class="xl65"><?=$comparison_data['CD_INV_MATERIAL']?></td>
		<td class="xl65"><?=$comparison_data['CD_CODE']?></td>
		<td class="xl65"><?=$comparison_data['CD_CODE2']?></td>
		<td class="xl65"><?=$comparison_data['CD_CODE3']?></td>
		<td class="xl65"><?=$comparison_data['CD_COO']?></td>
		<td class="xl65"><?=$_qty?></td>
<? } ?>

	</tr>
<?
	} }
?>
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
	}); // $.ajax */

}
//--> 
</script> 
<?
include "../layout/footer_popup.php";
exit;
?>
