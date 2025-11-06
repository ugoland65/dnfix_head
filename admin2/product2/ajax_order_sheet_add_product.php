<?
include "../lib/inc_common.php";

$_brand_idx = securityVal($brand_idx);
$_oog_code = securityVal($oog_code);
$_oo_idx = securityVal($oo_idx);

$oog_data = wepix_fetch_array(wepix_query_error("select * from ona_order_group where oog_code = '".$_oog_code."' "));

$_oprice_mode = $oog_data[oog_code];
$_ary_or_cg_data = $oog_data[oog_data];
$_ary_or_cg_all = explode("|", $_ary_or_cg_data);

$front_num = "1";
for ($i=0; $i<count($_ary_or_cg_all); $i++){

	$_ary_or_cg = explode("/", $_ary_or_cg_all[$i]);
	$_or_cg_brand = $_ary_or_cg[0];
	$_ary_or_cg_goods = "";
	$_ary_or_cg_goods = explode(",", $_ary_or_cg[1]);

	//echo $_ary_or_cg_all[$i]."<br><br>";

	if( $_or_cg_brand == $_brand_idx ){
		$front_num = "2";
		$show_brand = $_ary_or_cg[0];
		$show_idx = $_ary_or_cg[1];
	}else{
		${"arry_".$front_num}[] = $_ary_or_cg_all[$i];
	}

}

$arry_1_implode = implode("|", $arry_1);
$arry_2_implode = implode("|", $arry_2);

//echo count($arry_1)."/".count($arry_2);

?>

<br><br>
<input type="hidden" id="arry_1_implode" value="<?=$arry_1_implode?>">
<input type="hidden" id="arry_2_implode" value="<?=$arry_2_implode?>">

<textarea name="" rows="" cols="" id="new_idx"><?=$show_idx?></textarea>
<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="addGoodsModify()">수정</button>

<script type="text/javascript"> 
<!-- 
function addGoodsModify(){

	var arry_1_implode = $('#arry_1_implode').val();
	var arry_2_implode = $('#arry_2_implode').val();
	var new_idx = $('#new_idx').val();

	var new_sum_idx = "";

	if( arry_1_implode ){
		new_sum_idx += arry_1_implode + "|";
	}
		new_sum_idx += "<?=$show_brand?>/" + new_idx;

	if( arry_2_implode ){
		new_sum_idx += "|" + arry_2_implode;
	}

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data : { 
			a_mode : "orderSheetProductModify",
			oog_code : "<?=$_oog_code?>",
			oog_data : new_sum_idx
		},
		success: function(getdata) {
			makedata = getdata.split('|');
			ckcode = makedata[1];
			msg = makedata[3];
			if(ckcode=="Processing_Complete"){
				//alert(msg);
				location.reload();
			}else if(ckcode=="Value_null"){

			}
		}
	});

}
//--> 
</script> 
<?
exit;
?>