<?
include "../lib/inc_common.php";
$pageGroup = "product2";
$pageName = "order_sheet_print_popup";

require_once("../../class/phpexcel/PHPExcel.php");

$_brand = securityVal($brand);
$_excel = securityVal($excel);
$_view = securityVal($view);

	$objPHPExcel = new PHPExcel();

	$objPHPExcel -> setActiveSheetIndex(0)
	-> setCellValue("A1", "출력시간")
	-> setCellValue("B1", "")
	-> setCellValue("C1", "")
	-> setCellValue("D1", "")
	-> setCellValue("E1", "")
	-> setCellValue("F1", "");

	$objPHPExcel -> setActiveSheetIndex(0)
	-> setCellValue("A2", "재고코드")
	-> setCellValue("B2", "이미지")
	-> setCellValue("C2", "바코드")
	-> setCellValue("D2", "상품명")
	-> setCellValue("D2", "재고")
	-> setCellValue("F2", "체크");


if( $_view == "all"){
	$_view_query = "";
}else{
	$_view_query = "  AND STOCK.ps_stock > 0 ";
}

	$query = "select * from "._DB_COMPARISON." PRD left join prd_stock STOCK ON (PRD.CD_IDX = STOCK.ps_prd_idx)
		WHERE PRD.CD_BRAND_IDX = '".$_brand."' ".$_view_query." order by STOCK.ps_stock asc ";
	$result = wepix_query_error($query);

/*
	$query = "select * from "._DB_COMPARISON." WHERE CD_BRAND_IDX = '".$_brand."' order by CD_IDX asc ";
*/
	$num = 1;
	$count = 1;

if( $_brand ){

	while($list = wepix_fetch_array($result)){

		$num++;

		$_ary_stock_idx[] = $list[ps_prd_idx];
		//$_ary_cd_img[] = $list[CD_IMG];
		$_ary_cd_name[] = $list[CD_NAME];
		$_ary_stock[] = $list[ps_stock];
		$_ary_code[] = $list[CD_CODE];

		$_cd_weight_data = json_decode($list[cd_weight_fn], true);
		
		if( $_cd_weight_data['3'] == "" ){
			$_weight = "상품 실측 중량 정보 없음";
			//echo $_weight."<br>";
		}else{
			$_weight = $_cd_weight_data['3']."g";
		}
		
		//echo $list[cd_weight_fn]."/".$_cd_weight_data['1']."/".$_cd_weight_data['2']."/".$_cd_weight_data['3']."<br>";

		$_ary_weight[] = $_weight;

		$objPHPExcel -> setActiveSheetIndex(0)
		-> setCellValue("A".$num, $list[ps_prd_idx])
		-> setCellValue("B".$num, "")
		-> setCellValue("C".$num, $list[CD_CODE])
		-> setCellValue("D".$num, $list[CD_NAME])
		-> setCellValue("E".$num, $list[ps_stock])
		-> setCellValue("F".$num, $_weight);

		$iCol = "B"; // 컬럼번호
		$iRow = $num; // 행번호

		$_ary_ck_img_name = "";
		$_img_path = str_replace('../../data/comparion/','', $list[CD_IMG]);
		$_ary_ck_img_name = explode("?", $_img_path);
		$photo_path = "../../data/comparion/".$_ary_ck_img_name[0]; // 이미지 경로
		$_ary_cd_img[] = $photo_path;
		
	if (file_exists($photo_path)) {

		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Photo '.$iRow);
		$objDrawing->setDescription('Photo '.$iRow);
		$objDrawing->setPath($photo_path);
		$objDrawing->setResizeProportional(true);
		$objDrawing->setWidth(90);
		$objDrawing->setOffsetX(0);
		$objDrawing->setOffsetY(0);
		$objDrawing->setCoordinates($iCol.$iRow);
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		$objPHPExcel->getActiveSheet()->getRowDimension($iRow)->setRowHeight(60); // 행높이 설정
	
	}

		//$objPHPExcel->getActiveSheet()->getStyle ( "A".$num.":J".$num )->getAlignment ()->setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER );
		$count++;

/*
		$testimg = $list[CD_IMG];
		if( !$list[CD_IMG] ) $testimg="aaa";

		echo "(".$num." | ".$list[CD_IDX].") ".$testimg." | ".$photo_path."<br>";
*/
	}
}


$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(9);
$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(13);
$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(60);
$objPHPExcel -> getActiveSheet() -> getColumnDimension("D") -> setWidth(12);

$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:D%s", $count)) -> getAlignment() -> setVertical (PHPExcel_Style_Alignment::VERTICAL_CENTER );
$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:A%s", $count)) -> getAlignment() -> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:D%s", $count)) -> getBorders() -> getAllBorders() -> setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


if( $_excel == "ok" ){

	$filename = $_code."_".date('Ymd_Hi',time());

	header("Content-Type:application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=".$filename.".xls");
	header("Cache-Control:max-age=0");

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter -> save("php://output");
}

include "../layout/header_popup.php";
?>

<style type="text/css">
.exel-table{}
.exel-table tr td{ border:1px solid #b4b4b4; padding:5px; }

#brand_stock_group_modify{ border:3px solid #333; padding:10px; }
.brand-stock-modify-list-wrap{ border:1px solid #ddd; background-color:#f7f7f7; }
.brand-stock-modify-list-wrap > div{ padding:5px; margin:7px 0 7px 7px; border:1px solid #999; border-radius:4px; display:inline-block; }
.brand-stock-modify-list-wrap > div > ul{ display:inline-block; }

.input-brand-group-name{ width:100px !important; }
.input-brand-idx{ width:100px !important; }
</style>

<div id="brand_stock_group_modify">
	
	<form id="form1">
	<input type="hidden" name="a_mode" value="brand_stock_reg" >
	<div class="p-10">
		그룹명 : <input type="text" name="brand_group_name" id="brand_group_name" class="input-brand-group-name">
		브랜드 IDX : <input type="text" name="brand_idx" id="brand_idx"  class="input-brand-idx">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="brandStock.reg(this);" >그룹추가</button>
		※ 브랜드 IDX (브랜드 리스트의 고유번호), 복수 등록일때는 구분자 / ex) 23/35/64
	</div>
	</form>

<?
	$json_url = "../../config_file/brand_stock_group.json";
	$json_string = file_get_contents($json_url);
	$json_data = json_decode($json_string, true);
?>


<form id="form2">
<input type="hidden" name="a_mode" value="brand_stock_modify" >

<div class="brand-stock-modify-list-wrap">
<? for ($i=0; $i<count($json_data); $i++){ ?>
	<div>
		<ul><i class="fas fa-arrows-alt"></i></ul>
		<ul><input type="text" name="brand_group_name[]" value="<?=$json_data[$i]['name']?>" style="width:100px"></ul>
		<ul><input type="text" name="brand_idx[]" value="<?=$json_data[$i]['idx']?>" style="width:100px"></ul>
		<ul><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="brandStock.addDel(this)" ><i class="fas fa-trash-alt"></i></button></ul>
	</div>
<? } ?>
</div>
</form>

<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm m-t-5" onclick="brandStock.modify(this);" >그룹 수정</button>
※삭제시 수정을 눌러야 적용됩니다.
</div>

<!-- 
<div class="p-l-10 p-t-5 p-b-5">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="" >그룹관리</button>
</div>
 -->

<div class="p-l-10">

<? for ($i=0; $i<count($json_data); $i++){ ?>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=<?=$json_data[$i]['idx']?>'"><?=$json_data[$i]['name']?></button>
<? } ?>

<!-- 
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=8'">매직 아이즈</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=14'">핫파워즈</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=13'">키테루키테루</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=12'">타마토이즈</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=6'">토이즈하트</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=7'">라이드제팬</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=10'">NPG</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=20'">막코스</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=22'">YELOLAB</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=63'">G프로젝트</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=64'">PPP</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=<?=$_view?>&brand=33'">EXE</button>
 -->
</div>

<br><br>
<?
if( $_view == "all"){
?>
<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=no&brand=<?=$_brand?>'">재고 있는것만 보기</button>
<?}else{ ?>
<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?view=all&brand=<?=$_brand?>'">0 재고 보기</button>
<? }?>

<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="location.href='/admin2/product2/popup.brand_stock.php?brand=<?=$_brand?>&excel=ok'" >엑셀출력</button>

<table class="exel-table">
<?
for ($i=0; $i<count($_ary_stock_idx); $i++){
	$img_path = 'http://dgmall.wepix-hosting.co.kr/data/comparion/'.$_ary_cd_img[$i];
?>
	<tr bgcolor="<?=$trcolor?>">
		<td class="xl65"><?=$_ary_stock_idx[$i]?></td>
		<td class="xl65"><img src="<?=$img_path?>" width="80px" height="80px"></td>
		<td class="xl65"><?=$_ary_code[$i]?></td>
		<td class="xl65" <? if( $_ary_stock_idx[$i] ){ ?>onclick="comparisonQuick('<?=$_ary_stock_idx[$i]?>','info');" <? } ?>><?=$_ary_cd_name[$i]?></td>
		<td class="xl65"><?=$_ary_stock[$i]?></td>
		<td class="xl65"><?=$_ary_weight[$i]?></td>
	</tr>
<? } ?>
</table>

<script type="text/javascript"> 
<!-- 
//가격비교 퀵 창
function comparisonQuick(idx, vmode){
	if( vmode == undefined ) vmode = "comparison"; 
	window.open("/admin2/comparison/popup.comparison_modify.php?idx="+ idx +"&vmode="+vmode, "comparison_quick_"+idx, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

var brandStock = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		reg : function(obj) {

			var showHtml = '';

			showHtml += ''
			+ '<div>'
			+ '<ul><i class="fas fa-arrows-alt"></i></ul> '
			+ '<ul><input type="text" name="brand_group_name[]" class="input-brand-group-name" value="'+ $('#brand_group_name').val() +'" ></ul> '
			+ '<ul><input type="text" name="brand_idx[]" class="input-brand-idx" value="'+ $('#brand_idx').val() +'" ></ul> '
			+ '<ul><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="" ><i class="fas fa-trash-alt"></i></button></ul>'
			+ '</div>';

			$(".brand-stock-modify-list-wrap").append(showHtml);

/*
			var formData = $("#form1").serializeArray();

			$(obj).attr('disabled', true);
			$.ajax({
				url: "processing.prd2_stock.php",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "기본 설정", "설정이 저장되었습니다.");
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
					loading("off");
					$(obj).attr('disabled', false);
				}
			});
*/

		},
		modify : function(obj) {

			var formData = $("#form2").serializeArray();

			$(obj).attr('disabled', true);
			$.ajax({
				url: "processing.prd2_stock.php",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						//toast2("success", "기본 설정", "설정이 저장되었습니다.");
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
					loading("off");
					$(obj).attr('disabled', false);
				}
			});

		},
		addDel : function(obj) {
			$(obj).parent().parent().remove();
		}
	};

}();

$(function(){
	$( ".brand-stock-modify-list-wrap" ).sortable();
});

//--> 
</script> 

<?
include "../layout/footer_popup.php";
exit;
?>