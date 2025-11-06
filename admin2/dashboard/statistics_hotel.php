<?
$pageGroup = "dashboard";
$pageName = "statistics_hotel";

include "../lib/inc_common.php";

	$_cur_y = securityVal($cur_y);
	$_cur_m = securityVal($cur_m);
	$_sort_mode = securityVal($sort_mode);

	if( !$_sort_mode) $_sort_mode = "TOTAL";

	//넘어오는 년월정보가 없으면 현재 년월을..
	if( !$_cur_y ) $_cur_y = date(Y);
	if( !$_cur_m ) $_cur_m = date(m);

	$hotel_area_color = array('#F0F8FF','#E6E6FA','#FFF0F5','#FFE4E1','#FFDAB9','#FAF0E6');

	$hotel_query = "select HOT_IDX, HOT_AREA, HOT_NAME from "._DB_HOTEL." ".$hotel_where." order by HOT_NAME asc";
	$hotel_result = wepix_query_error($hotel_query);
	while($hotel_list = wepix_fetch_array($hotel_result)){

		$_ary_hotel[$hotel_list[HOT_AREA]][] = $hotel_list[HOT_NAME];
		$_ary_hotel_idx[$hotel_list[HOT_AREA]][] = $hotel_list[HOT_IDX];

		$_count_sum = 0;
		$_count_sum1 = 0;
		$_count_sum2 = 0;

		for($a=1; $a<=12; $a++){

			${"start_".$a} =mktime(0, 0, 0, $a , 1, $_cur_y);
			${"end_".$a} =mktime(23, 59, 59, $a+1 , 0, $_cur_y);
			
			${"count_".$hotel_list[HOT_IDX]."_M".$a} = wepix_fetch_array(wepix_query_error("select SUM(STH_N) as sum
				from "._DB_HOTEL_STATISTICS."
				where STH_BKP_KIND != 'CANCEL'
				and STH_HOT_IDX = '".$hotel_list[HOT_IDX]."'
				and STH_IN >= '".${"start_".$a}."' and STH_IN <= '".${"end_".$a}."'
			"));

			$_count_sum += ${"count_".$hotel_list[HOT_IDX]."_M".$a}[sum];

			if( $a==6 ){
				$_count_sum1 = $_count_sum;
			}elseif( $a>6 ){
				$_count_sum2 += ${"count_".$hotel_list[HOT_IDX]."_M".$a}[sum];
			}

		} //for END

		${"_ary_hotel_".$hotel_list[HOT_AREA]}[] = array(
			"AREA" => $hotel_list[HOT_AREA], 
			"NAME" => $hotel_list[HOT_NAME],
			"IDX" => $hotel_list[HOT_IDX],
			"M1" => ${"count_".$hotel_list[HOT_IDX]."_M1"}[sum],
			"M2" => ${"count_".$hotel_list[HOT_IDX]."_M2"}[sum],
			"M3" => ${"count_".$hotel_list[HOT_IDX]."_M3"}[sum],
			"M4" => ${"count_".$hotel_list[HOT_IDX]."_M4"}[sum],
			"M5" => ${"count_".$hotel_list[HOT_IDX]."_M5"}[sum],
			"M6" => ${"count_".$hotel_list[HOT_IDX]."_M6"}[sum],
			"M7" => ${"count_".$hotel_list[HOT_IDX]."_M7"}[sum],
			"M8" => ${"count_".$hotel_list[HOT_IDX]."_M8"}[sum],
			"M9" => ${"count_".$hotel_list[HOT_IDX]."_M9"}[sum],
			"M10" => ${"count_".$hotel_list[HOT_IDX]."_M10"}[sum],
			"M11" => ${"count_".$hotel_list[HOT_IDX]."_M11"}[sum],
			"M12" => ${"count_".$hotel_list[HOT_IDX]."_M12"}[sum],
			"F_HALF" => $_count_sum1,
			"S_HALF" => $_count_sum2,
			"TOTAL" => $_count_sum
		);
	}
 
include "../layout/header.php";
?>
<link href="https://fonts.googleapis.com/css?family=Fredoka+One" rel="stylesheet">
<STYLE TYPE="text/css">
.table-style{ width:100%; }
.hotel-count{ text-align:right; }
.table-sname{ height:32px !important; cursor:pointer; }
.table-sname.active{ color:#0000ff; }
.area-name{  height:28px !important; text-align:center; }
.head_btn select{ height:40px; background-color:#dddddd; font-family: 'Fredoka One', cursive; text-align:center; font-size:26px; }
</STYLE>
<div id="contents_head">
	<h1>호텔 통계</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		
		<div class="head_btn">
					<select name="" id="calendar_year" onchange="calendal_ch()">
						<option value="2018" <? if($_cur_y=="2018") echo "selected"; ?>>2018</option>
						<option value="2019" <? if($_cur_y=="2019") echo "selected"; ?>>2019</option>
						<option value="2020" <? if($_cur_y=="2020") echo "selected"; ?>>2020</option>
						<option value="2021" <? if($_cur_y=="2021") echo "selected"; ?>>2021</option>
					</select>
		</div>

<table cellspacing="1" cellpadding="0" class="table-style" >
	<tr>
		<th class="table-sname <? if($_sort_mode == "NAME") echo "active"; ?>" onclick="location.href='<?=_A_PATH_DASHBOARD_STICS_HOTEL?>?cur_y=<?=$_cur_y?>&sort_mode=NAME' ">호텔명<? if($_sort_mode == "NAME") echo " ▼"; ?></th>
<?
for($a=1; $a<=12; $a++){
?>
		<th class="table-sname <? if($_sort_mode == "M".$a) echo "active"; ?>" onclick="location.href='<?=_A_PATH_DASHBOARD_STICS_HOTEL?>?cur_y=<?=$_cur_y?>&sort_mode=M<?=$a?>' " width="80px"><?=$a?> 월<? if($_sort_mode == "M".$a) echo " ▼"; ?></th>
<?
	if($a==6){
?>
		<th class="table-sname <? if($_sort_mode == "F_HALF") echo "active"; ?>" onclick="location.href='<?=_A_PATH_DASHBOARD_STICS_HOTEL?>?cur_y=<?=$_cur_y?>&sort_mode=F_HALF' " width="100px">상반기 합계<? if($_sort_mode == "F_HALF") echo " ▼"; ?></th>
<? } ?>
<? } ?>
		<th class="table-sname <? if($_sort_mode == "S_HALF") echo "active"; ?>" onclick="location.href='<?=_A_PATH_DASHBOARD_STICS_HOTEL?>?cur_y=<?=$_cur_y?>&sort_mode=S_HALF' " width="100px">하반기 합계<? if($_sort_mode == "S_HALF") echo " ▼"; ?></th>
		<th class="table-sname <? if($_sort_mode == "TOTAL") echo "active"; ?>" onclick="location.href='<?=_A_PATH_DASHBOARD_STICS_HOTEL?>?cur_y=<?=$_cur_y?>&sort_mode=TOTAL' " width="100px">TOTAL<? if($_sort_mode == "TOTAL") echo " ▼"; ?></th>
	</tr>

<?
for($i=0; $i<count($bva_tr_area); $i++){

	$_inst_area_code = $bva_tr_area[$i]['code'];
	$_ary_hotel_area = ${"_ary_hotel_".$_inst_area_code};

?>
	<tr style="background-color:<?=$hotel_area_color[$i]?>;">
		<td colspan="16" class="area-name" style="/* border:none; */"><b><?=$_inst_area_code?></b> ( <?=$bva_tr_area_name_ko[$_inst_area_code]?> )</td>
	</tr>

<?
if($_sort_mode !="NAME" AND $_sort_mode){
	$_ary_hotel_area = wepixArraySort($_ary_hotel_area, $_sort_mode, "rsort");
}

$_sum_m1 = 0;
$_sum_m2 = 0;
$_sum_m3 = 0;
$_sum_m4 = 0;
$_sum_m5 = 0;
$_sum_m6 = 0;
$_sum_m7 = 0;
$_sum_m8 = 0;
$_sum_m9 = 0;
$_sum_m10 = 0;
$_sum_m11 = 0;
$_sum_m12 = 0;

$_sum_f_half = 0;
$_sum_s_half = 0;
$_sum_total = 0;

$_row_num = 0;

for($z=0; $z<count($_ary_hotel_area); $z++){

	$_row_num++;
	$_background_color = "#E8E8E8";
	if($_row_num%2 == 0){
		$_background_color = "#CFCFCF";
	}

	$_count_sum = 0;
	$_count_sum2 = 0;

	$_sum_m1 += $_ary_hotel_area[$z]['M1'];
	$_sum_m2 += $_ary_hotel_area[$z]['M2'];
	$_sum_m3 += $_ary_hotel_area[$z]['M3'];
	$_sum_m4 += $_ary_hotel_area[$z]['M4'];
	$_sum_m5 += $_ary_hotel_area[$z]['M5'];
	$_sum_m6 += $_ary_hotel_area[$z]['M6'];
	$_sum_m7 += $_ary_hotel_area[$z]['M7'];
	$_sum_m8 += $_ary_hotel_area[$z]['M8'];
	$_sum_m9 += $_ary_hotel_area[$z]['M9'];
	$_sum_m10 += $_ary_hotel_area[$z]['M10'];
	$_sum_m11 += $_ary_hotel_area[$z]['M11'];
	$_sum_m12 += $_ary_hotel_area[$z]['M12'];
	$_sum_f_half += $_ary_hotel_area[$z]['F_HALF'];
	$_sum_s_half += $_ary_hotel_area[$z]['S_HALF'];
	$_sum_total += $_ary_hotel_area[$z]['TOTAL'];
?>
	<tr style="background-color:<?=$hotel_area_color[$i]?>;">
		<td><?=$_ary_hotel_area[$z]['NAME']?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M1'] > 0 ) ? number_format($_ary_hotel_area[$z]['M1']) : "-" ?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M2'] > 0 ) ? number_format($_ary_hotel_area[$z]['M2']) : "-" ?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M3'] > 0 ) ? number_format($_ary_hotel_area[$z]['M3']) : "-" ?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M4'] > 0 ) ? number_format($_ary_hotel_area[$z]['M4']) : "-" ?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M5'] > 0 ) ? number_format($_ary_hotel_area[$z]['M5']) : "-" ?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M6'] > 0 ) ? number_format($_ary_hotel_area[$z]['M6']) : "-" ?></td>
		<td class="hotel-count"><b><?=number_format($_ary_hotel_area[$z]['F_HALF'])?></b></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M7'] > 0 ) ? number_format($_ary_hotel_area[$z]['M7']) : "-" ?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M8'] > 0 ) ? number_format($_ary_hotel_area[$z]['M8']) : "-" ?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M9'] > 0 ) ? number_format($_ary_hotel_area[$z]['M9']) : "-" ?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M10'] > 0 ) ? number_format($_ary_hotel_area[$z]['M10']) : "-" ?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M11'] > 0 ) ? number_format($_ary_hotel_area[$z]['M11']) : "-" ?></td>
		<td class="hotel-count" style="background-color:<?=$_background_color?>;"><?=($_ary_hotel_area[$z]['M12'] > 0 ) ? number_format($_ary_hotel_area[$z]['M12']) : "-" ?></td>
		<td class="hotel-count"><b><?=number_format($_ary_hotel_area[$z]['S_HALF'])?></b></td>
		<td class="hotel-count"><b><?=number_format($_ary_hotel_area[$z]['TOTAL'])?></b></td>
	</tr>
<? } ?>
	<tr style="background-color:<?=$hotel_area_color[$i]?>;">
		<td>지역 총합</td>
		<td class="hotel-count"><b><?=number_format($_sum_m1)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m2)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m3)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m4)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m5)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m6)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_f_half)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m7)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m8)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m9)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m10)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m11)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_m12)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_s_half)?></b></td>
		<td class="hotel-count"><b><?=number_format($_sum_total)?></b></td>
	</tr>
	<tr>
		<td colspan="16" style="border:none; height:8px; padding:0 !important;"></td>
	</tr>
<? } ?>

</table>

	</div>
</div>
<script type='text/javascript'>
function calendal_ch(){
	//alert($("#calendar_year").val()+"/"+$("#calendar_month").val());
   location.href="<?=_A_PATH_DASHBOARD_STICS_HOTEL?>?cur_y="+ $("#calendar_year").val() +"&sort_mode=<?=$_sort_mode?>";
}
</script>
<?
include "../layout/footer.php";
exit;
?>