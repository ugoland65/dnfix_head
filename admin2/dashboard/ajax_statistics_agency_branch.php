<?
include "../lib/inc_common.php";

	$_bkp_agency = securityVal($key);
	$_cur_y = securityVal($cur_y);
	$_cur_m = securityVal($cur_m);

	//넘어오는 년월정보가 없으면 현재 년월을..
	if( !$_cur_y ) $_cur_y = date(Y); 
    if( !$_cur_m ) $_cur_m = date(m); 
    if($_cur_m != 10){
		$_cur_m_num = str_replace("0", "", $_cur_m); 
	}
	$cur_d = date(d);

	//해당 년월의 총 날짜수를 구한다.
	$tot_days = getTotaldays2($_cur_y, $_cur_m);

	$tstamp = mktime(0,0,0,$_cur_m,1,$_cur_y); //해당 년월의 1일에 해당하는 timestamp값을 구한다.
	$tstamp_end = mktime(23,59,59,$_cur_m,$tot_days,$_cur_y); //해당 년월의 마지막 해당하는 timestamp값을 구한다.

	$bk_where = " where BKP_START_DATE >= ".$tstamp." and BKP_START_DATE <= ".$tstamp_end." and BKP_AGENCY = '".$_bkp_agency."' ";
    $bk_query = "select BKP_IDX, BKP_TYPE, BKP_KIND, BKP_BUSINESS from "._DB_BOOKING." ".$bk_where." order by BKP_START_DATE asc";
	$bk_result = wepix_query_error($bk_query);
	$test_count = 0;
	while($bk_list = wepix_fetch_array($bk_result)){

		if($bk_list[BKP_KIND] == "CANCEL"){
			${"_ary_".$bk_list[BKP_BUSINESS]."_".$bk_list[BKP_TYPE]."_CANCEL"}[] = $bk_list[BKP_IDX];
		}else{
			${"_ary_".$bk_list[BKP_BUSINESS]."_".$bk_list[BKP_TYPE]}[] = $bk_list[BKP_IDX];
		}

	}


	$agency_where = " where AG_DEL_YN='N' and AG_KIND='B' and AG_CO_IDX = '".$_bkp_agency."' ";
	$agency_query = "select AG_IDX, AG_COMPANY from "._DB_AGENCY." ".$agency_where." order by AG_COMPANY asc ";
	$agency_result = wepix_query_error($agency_query);
	while($agency_list = wepix_fetch_array($agency_result)){		
		//$_ary_agency[] = $agency_list[AG_IDX];

		$_count_hm = count(${"_ary_".$agency_list[AG_IDX]."_HM"});
		$_count_hm_cancel = count(${"_ary_".$agency_list[AG_IDX]."_HM_CANCEL"});
		$_count_hm_sum = $_count_hm + $_count_hm_cancel;

		$_count_fa = count(${"_ary_".$agency_list[AG_IDX]."_FA"});
		$_count_fa_cancel = count(${"_ary_".$agency_list[AG_IDX]."_FA_CANCEL"});
		$_count_fa_sum = $_count_fa + $_count_fa_cancel;

		$_count_pkg = count(${"_ary_".$agency_list[AG_IDX]."_PKG"});
		$_count_pkg_cancel = count(${"_ary_".$agency_list[AG_IDX]."_PKG_CANCEL"});
		$_count_pkg_sum = $_count_pkg + $_count_pkg_cancel;

		$_count_ict = count(${"_ary_".$agency_list[AG_IDX]."_ICT"});
		$_count_ict_cancel = count(${"_ary_".$agency_list[AG_IDX]."_ICT_CANCEL"});
		$_count_ict_sum = $_count_ict + $_count_ict_cancel;

		$_count_gf = count(${"_ary_".$agency_list[AG_IDX]."_GF"});
		$_count_gf_cancel = count(${"_ary_".$agency_list[AG_IDX]."_GF_CANCEL"});
		$_count_gf_sum = $_count_gf + $_count_gf_cancel;

		$_count_ro = count(${"_ary_".$agency_list[AG_IDX]."_RO"});
		$_count_ro_cancel = count(${"_ary_".$agency_list[AG_IDX]."_RO_CANCEL"});
		$_count_ro_sum = $_count_ro + $_count_ro_cancel;

		$_count_total = $_count_hm + $_count_fa + $_count_pkg + $_count_ict + $_count_gf + $_count_ro;
		$_count_total_cancel = $_count_hm_cancel + $_count_fa_cancel + $_count_pkg_cancel + $_count_ict_cancel + $_count_gf_cancel + $_count_ro_cancel;
		$_count_total_sum = $_count_total + $_count_total_cancel;

		$_ary_agency[] = array(
			"NAME" => $agency_list[AG_COMPANY],
			"IDX" => $agency_list[AG_IDX],
			"HM" => $_count_hm,
			"HM_CANCEL" => $_count_hm_cancel,
			"HM_SUM" => $_count_hm_sum,
			"FA" => $_count_fa,
			"FA_CANCEL" => $_count_fa_cancel,
			"FA_SUM" => $_count_fa_sum,
			"PKG" => $_count_pkg,
			"PKG_CANCEL" => $_count_pkg_cancel,
			"PKG_SUM" => $_count_pkg_sum,
			"ICT" => $_count_ict,
			"ICT_CANCEL" => $_count_ict_cancel,
			"ICT_SUM" => $_count_ict_sum,
			"GF" => $_count_gf,
			"GF_CANCEL" => $_count_gf_cancel,
			"GF_SUM" => $_count_gf_sum,
			"RO" => $_count_ro,
			"RO_CANCEL" => $_count_ro_cancel,
			"RO_SUM" => $_count_ro_sum,
			"TOTAL" => $_count_total,
			"TOTAL_CANCEL" => $_count_total_cancel,
			"TOTAL_SUM" => $_count_total_sum
		);
	}
?>
<table cellspacing="1" cellpadding="0" class="table-style" >
	<tr>
		<th colspan="2">지사명</th>
		<th width="11%">HM</th>
		<th width="11%">FIT ALL</th>
		<th width="11%">PKG</th>
		<th width="11%">INCENTIVE</th>
		<th width="11%">GOLF</th>
		<th width="11%">ROOM ONLY</th>
		<th width="11%">TOTAL</th>
	</tr>
<?
$_row_num = 0;
for($i=0; $i<count($_ary_agency); $i++){

	$_row_num++;
	$_background_color = "#E8E8E8";
	if($_row_num%2 == 0){
		$_background_color = "#CFCFCF";
	}
?>
	<tr style="background-color:<?=$_background_color?>;">
		<td rowspan="3">
			<b><?=$_ary_agency[$i]["NAME"]?></b>
		</td>
		<td class="s-title"></td>
		<td class="count-box"><?=($_ary_agency[$i]["HM"] > 0 ) ? number_format($_ary_agency[$i]["HM"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["FA"] > 0 ) ? number_format($_ary_agency[$i]["FA"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["PKG"] > 0 ) ? number_format($_ary_agency[$i]["PKG"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["ICT"] > 0 ) ? number_format($_ary_agency[$i]["ICT"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["GF"] > 0 ) ? number_format($_ary_agency[$i]["GF"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["RO"] > 0 ) ? number_format($_ary_agency[$i]["RO"]) : "-" ?></td>
		<td class="count-box"><b><?=($_ary_agency[$i]["TOTAL"] > 0 ) ? number_format($_ary_agency[$i]["TOTAL"]) : "-" ?></b></td>
	</tr>
	<tr style="background-color:<?=$_background_color?>;">
		<td class="s-title">CXLD</td>
		<td class="count-box"><?=($_ary_agency[$i]["HM_CANCEL"] > 0 ) ? number_format($_ary_agency[$i]["HM_CANCEL"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["FA_CANCEL"] > 0 ) ? number_format($_ary_agency[$i]["FA_CANCEL"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["PKG_CANCEL"] > 0 ) ? number_format($_ary_agency[$i]["PKG_CANCEL"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["ICT_CANCEL"] > 0 ) ? number_format($_ary_agency[$i]["ICT_CANCEL"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["GF_CANCEL"] > 0 ) ? number_format($_ary_agency[$i]["GF_CANCEL"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["RO_CANCEL"] > 0 ) ? number_format($_ary_agency[$i]["RO_CANCEL"]) : "-" ?></td>
 		<td class="count-box"><b><?=($_ary_agency[$i]["TOTAL_CANCEL"] > 0 ) ? number_format($_ary_agency[$i]["TOTAL_CANCEL"]) : "-" ?></b></td>
	</tr>
	<tr style="background-color:<?=$_background_color?>;">
		<td class="s-title">TOTAL</td>
		<td class="count-box"><b><?=($_ary_agency[$i]["HM_SUM"] > 0 ) ? number_format($_ary_agency[$i]["HM_SUM"]) : "-" ?></b></td>
		<td class="count-box"><?=($_ary_agency[$i]["FA_SUM"] > 0 ) ? number_format($_ary_agency[$i]["FA_SUM"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["PKG_SUM"] > 0 ) ? number_format($_ary_agency[$i]["PKG_SUM"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["ICT_SUM"] > 0 ) ? number_format($_ary_agency[$i]["ICT_SUM"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["GF_SUM"] > 0 ) ? number_format($_ary_agency[$i]["GF_SUM"]) : "-" ?></td>
		<td class="count-box"><?=($_ary_agency[$i]["RO_SUM"] > 0 ) ? number_format($_ary_agency[$i]["RO_SUM"]) : "-" ?></td>
		<td class="count-box"><b><?=($_ary_agency[$i]["TOTAL_SUM"] > 0 ) ? number_format($_ary_agency[$i]["TOTAL_SUM"]) : "-" ?></b></td>
	</tr>
<? } ?>
</table>
<?
exit;
?>