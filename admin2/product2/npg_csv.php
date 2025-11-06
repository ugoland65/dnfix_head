<?
$pageGroup = "product2";
$pageName = "cafe24_sms";

include "../lib/inc_common.php";

$_a_mode = securityVal($a_mode);
	
function arr_sort( $array, $key, $sort ){
  $keys = array();
  $vals = array();
  foreach( $array as $k=>$v ){
    $i = $v[$key].'.'.$k;
    $vals[$i] = $v;
    array_push($keys, $k);
  }
  unset($array);

  if( $sort=='asc' ){
    ksort($vals);
  }else{
    krsort($vals);
  }
  
  $ret = array_combine( $keys, $vals );

  unset($keys);
  unset($vals);
  
  return $ret;
}


if( $_a_mode == "after_file" ){
	
	setlocale(LC_CTYPE, 'ko_KR.eucKR'); 
	//setlocale(LC_CTYPE, 'ko_KR.utf8');

	extract($_FILES['userfile']); 
	if (!(($type == 'application/octet-stream' || $type == 'application/vnd.ms-excel' || $type == 'application/haansoftcsv') && preg_match("/\.csv$/i", $name))) {  
	    msg('엑셀 CSV 파일이 아닙니다.'); 
		exit;
	}

	$fp = fopen($tmp_name, 'r'); 
	$count = 0;
	$count2 = 0;

	while ($row = fgetcsv($fp, 100000, ',')) { 
		$count++;
		$_query = array();
		
		$_name_jp = iconv("euc-kr","utf-8",$row[2]);
		$_name = iconv("euc-kr","utf-8",$row[3]);
		$_name_en = iconv("euc-kr","utf-8",$row[4]);
		$_jan = $row[5];
		$_code1 = iconv("euc-kr","utf-8",$row[6]);
		$_coo = iconv("euc-kr","utf-8",$row[9]);

		$_name_jp = securityVal($_name_jp);
		$_name = securityVal($_name);

	//echo $_name_jp."<br>";

		if( $_jan ){
			$_jan2 = str_replace(" ","",$_jan);
			$comparison_data = wepix_fetch_array(wepix_query_error("select 
			CD_IDX, CD_CODE, CD_INV_NAME2, CD_CODE3
			from "._DB_COMPARISON." where replace(CD_CODE,' ','') = '".$_jan2."' "));
			

			if( $comparison_data[CD_CODE] ){

				if( $_name_en != "" && $comparison_data[CD_INV_NAME2] == "" ){
					$_name_en = securityVal($_name_en);
					$_query[] = " CD_INV_NAME2 = '".$_name_en."' ";

					//echo $_jan2."/".$comparison_data[CD_INV_NAME2]."/".$_name_en."<br>";
				}

				if( $_code1 != "" && $comparison_data[CD_CODE3] == "" ){
					$_code1 = securityVal($_code1);
					$_query[] = " CD_CODE3 = '".$_code1."' ";
					//echo $_jan2."/".$comparison_data[CD_CODE]."/".$_code1."<br>";
				}

				if( $_coo != "" ){
					$_coo = securityVal($_coo);

					$_coo2 =  strtolower($_coo);
					if( $_coo2 == "japan" || $_coo2 == "china" ){
						$_query[] = " CD_COO = '".$_coo2."' ";
					}
					//echo $_jan2."/".$comparison_data[CD_CODE]."/".$_code1."<br>";
				}

				$_query_text = implode(",", $_query);

				if( $_query_text ){
					echo $_query_text."<br>";
					wepix_query_error("update "._DB_COMPARISON." set ".$_query_text." WHERE CD_IDX = ".$comparison_data[CD_IDX]." ");
				}

			}

			//echo $_jan2."/".$comparison_data[CD_CODE]."/".$comparison_data[CD_INV_NAME2]."<br>";
/*
			$_aa = '{"namejp":"'.$_name_jp.'","name":"'.$_name.'"}';
			//echo $_aa."<br>";
			$_json_arry[] = $_aa;
*/
		}
	}


	$_json_data_implode = '['.implode(",", $_json_arry).']';

	$_json_data = json_decode($_json_data_implode, true);

/*
	$_json_data = arr_sort( $_json_data,'count', 'desc' );

	$s_date = min($_date_min_max_arry);
	$e_date = max($_date_min_max_arry);
*/

}

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>재고관리 (엑셀)</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

<?
if( $_a_mode == "after_file" ){
?>

		<div id="" class="list-box-layout3 display-table">
			<ul class="display-table-cell width-50p v-align-top">
				
				<div class="list-box-layout3-wrap" id="stock_prd_list">
<?

	echo "<pre>";
	print_r($_json_data);
	echo "</pre>";

echo $_json_data_implode."<br>";
?>
					<table class="table-list" id="stock_prd_cart">

<?
$total_qty_sum = 0;

for ($i=0; $i<count($_json_data); $i++){

	$_name_jp = $_json_data[$i]['namejp'];
	$_name = $_json_data[$i]['name'];

?>
	<tr>
		<td><?=$_name_jp?></td>
		<td><?=$_name?></td>
		<td></td>
		<td></td>
	</tr>
<? } ?>

					</table>
					</form>

				</div>

			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell v-align-top">

				<div class="list-box-layout3-wrap">

<!-- /<?=$_json_data?>/ -->
<?
/*
	echo "<pre>";
	print_r($_json_data);
	echo "</pre>";
*/
?>

<?=date('Y-m-d H:i:s', $s_date)?> ~ <?=date('Y-m-d H:i:s', $e_date)?>
<br><br>


				</div>

			</ul>
		</div>

		<div class="list-bottom-btn-wrap">
			<ul class="list-top-total">
				<span class="count"></span>
			</ul>
			<ul class="list-top-btn">
				<!-- <button type="button" id="bklist_open" class="btnstyle1 btnstyle1-primary btnstyle1-md" onclick="dayStock()">재고 입출고 등록하기</button> -->
			</ul>
		</div>

<?}else{?>
<form action="npg_csv.php" method="post" enctype="multipart/form-data" onSubmit="return confirm('파일을 올리시겠습니까?\n입력하신 내용을 다시 한번 확인해주시기 바랍니다.');">
<input type="hidden" name="a_mode" value="after_file">

<table cellpadding="0" cellspacing="0" border="0" class="exstyle2">
<tr>
<td>엑셀파일 :</td>
<td><input name="userfile" type="file" id="데이터 찾기" size="50"></td>
</tr>
<tr><td colspan="2"><input type="submit" value=" 재고 엑셀 올리기 " class="inputbutton2"></td></tr>
</table>
</form>
<? } ?>

	</div>
</div>

<script type="text/javascript"> 
<!-- 
function dayStock(){
	$("#stock_day").val($("#_stock_day").val());
	$("#day_stock_form").submit();
}
//--> 
</script> 

<?
include "../layout/footer.php";
exit;
?>