<?
	include "../lib/inc_common.php";

	setlocale(LC_CTYPE, 'ko_KR.eucKR'); 
	//setlocale(LC_CTYPE, 'ko_KR.utf8');

	extract($_FILES['userfile']); 
	if (!(($type == 'application/octet-stream' || $type == 'application/vnd.ms-excel' || $type == 'application/haansoftcsv') && preg_match("/\.csv$/i", $name))) {  
	    msg('엑셀 CSV 파일이 아닙니다.'); 
		exit;
	}

	$fp = fopen($tmp_name, 'r'); 
	$count = 0;
	while ($row = fgetcsv($fp, 100000, ',')) { 
		$count++;
		
		$_name = iconv("euc-kr","utf-8",$row[0]);
		$_prdCode = $row[4];
		$_qty = $row[6]*1;

		if( $count > 1 && $_name != "" ){
			if( $_prdCode ){
				$stock_code[] = $_prdCode;
				if( ${'stock_'.$_prdCode} > 0 ){
					${'stock_'.$_prdCode} = ${'stock_'.$_prdCode} + $_qty;
				}else{
					${'stock_'.$_prdCode} = $_qty;
				}

			}
		}
		//echo $row[0]."/".iconv("euc-kr","utf-8",$row[0])."/".$row[4]."<br>";

	}

	$stock_code = array_unique($stock_code);
	sort($stock_code);

for ($i=0; $i<count($stock_code); $i++){
	$stock_data = wepix_fetch_array(wepix_query_error("select ps_prd_idx from prd_stock where ps_idx = '".$stock_code[$i]."' "));
	$comparison_data = wepix_fetch_array(wepix_query_error("select CD_NAME from "._DB_COMPARISON." where CD_IDX = '".$stock_data[ps_prd_idx]."' "));

	echo $comparison_data[CD_NAME]."/".$stock_code[$i]." = ".${'stock_'.$stock_code[$i]}."<br>";
}

/*
  echo "<br><br><br>".count($stock_code)."<br><br><br>";
  echo "<pre>\n";
  print_r($stock_code);
  echo "</pre>\n";
*/
?>