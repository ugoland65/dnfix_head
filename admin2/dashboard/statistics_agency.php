<?
$pageGroup = "dashboard";
$pageName = "statistics_agency";

include "../lib/inc_common.php";

	$_cur_y = securityVal($cur_y);
	$_cur_m = securityVal($cur_m);

	if( !$_sort_mode) $_sort_mode = "TOTAL_SUM";

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


	$bk_where = " where BKP_BOOKING_DATE >= ".$tstamp." and BKP_BOOKING_DATE <= ".$tstamp_end." ";
    $bk_query = "select BKP_IDX, BKP_TYPE, BKP_KIND, BKP_AGENCY from "._DB_BOOKING." ".$bk_where." order by BKP_BOOKING_DATE asc";
	$bk_result = wepix_query_error($bk_query);
	$test_count = 0;
	while($bk_list = wepix_fetch_array($bk_result)){

		if($bk_list[BKP_KIND] == "CANCEL"){
			${"_ary_".$bk_list[BKP_AGENCY]."_".$bk_list[BKP_TYPE]."_CANCEL"}[] = $bk_list[BKP_IDX];
		}else{
			${"_ary_".$bk_list[BKP_AGENCY]."_".$bk_list[BKP_TYPE]}[] = $bk_list[BKP_IDX];
		}

/*		
		$test_count++;
		$bk_day_code = date('Y',$bk_list[$_date_colum_name]).date('m',$bk_list[$_date_colum_name]).date('d',$bk_list[$_date_colum_name]);
		${"_ary_".$bk_list[BKP_TYPE]}[$bk_day_code][] = $bk_list[BKP_IDX];
		${"_ary_".$bk_list[BKP_TYPE]."_head_count"}[$bk_day_code][] = count(explode("│",$bk_list[BKP_GUEST]));
*/
	}


	$agency_where = " where AG_DEL_YN='N' and AG_KIND='A' ";
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

include "../layout/header.php";
?>
<link href="https://fonts.googleapis.com/css?family=Fredoka+One" rel="stylesheet">
<STYLE TYPE="text/css">
#calendal_head{ width:90%; margin:0 auto;  }
#calendal_head select{ height:40px; background-color:#dddddd; font-family: 'Fredoka One', cursive; text-align:center; font-size:26px; }

#calendal_head_btn_wrap{ width:100%; display:table; }
#calendal_head_btn_left{ display:table-cell; }
#calendal_head_btn_right{ display:table-cell; text-align:right; }

#calendal_wrap{ width:90%; margin:0 auto;  overflow:hidden; }
.table-style{ width:100%; }
.s-title{ width:50px; font-size:11px; text-align:center; }
.count-box{ text-align:right; }
</STYLE>
<div id="contents_head">
	<h1>에이전시 부킹통계</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div id='calendal_head'>
			<div id='calendal_head_btn_wrap'>
				<ul id='calendal_head_btn_left'>
					<!-- <i class="fas fa-chevron-circle-left"></i>  -->
					<select name="" id="calendar_year" onchange="calendal_ch()">
						<option value="2018" <? if($_cur_y=="2018") echo "selected"; ?>>2018</option>
						<option value="2019" <? if($_cur_y=="2019") echo "selected"; ?>>2019</option>
						<option value="2020" <? if($_cur_y=="2020") echo "selected"; ?>>2020</option>
						<option value="2021" <? if($_cur_y=="2021") echo "selected"; ?>>2021</option>
					</select>
					<select name="" id="calendar_month" onchange="calendal_ch()">
		<?
		for($i=1; $i<13; $i++){
		?>
						<option value="<?=$i?>" <? if($i==$_cur_m) echo "selected"; ?>><?=$i?></option>
		<? } ?>
					</select>
		<!-- 
					<span class='year-name'>2019년</span>
					<span class='month-name'>7월</span>
		 -->
					<!-- <i class="fas fa-chevron-circle-right"></i> -->

					<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="height:30px; !important;" onclick="location.href='<?=_A_PATH_DASHBOARD_STICS_AGENCY?>?sort_mode=<?=$_sort_mode?>&cur_y=<?=date(Y)?>&cur_m=<?=date(m)?>'" > <i class="far fa-calendar-alt"></i> 이번달</button>
				</ul>
				<ul id='calendal_head_btn_right'>
					본사 <b><?=count($_ary_agency)?></b>개 검색되었습니다.
				</ul>
			</div>
		</div>
		<div id="calendal_wrap">

<table cellspacing="1" cellpadding="0" class="table-style" >
	<tr>
		<th colspan="2">본사명</th>
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

if($_sort_mode !="NAME" AND $_sort_mode){
	$_ary_agency = wepixArraySort($_ary_agency, $_sort_mode, "rsort");
}

for($i=0; $i<count($_ary_agency); $i++){

	$_row_num++;
	$_background_color = "#ffffff";
	if($_row_num%2 == 0){
		$_background_color = "#eee";
	}
?>
	<tr style="background-color:<?=$_background_color?>;">
		<td rowspan="3">
			<b><?=$_ary_agency[$i]["NAME"]?></b>&nbsp;
			<button type="button" id="branch_view_btn_show_<?=$_ary_agency[$i]["IDX"]?>" class="btnstyle1 btnstyle1-info btnstyle1-xs" style="width:40px !important;" onclick="branchView('show', '<?=$_ary_agency[$i]["IDX"]?>', '<?=$_cur_y?>', '<?=$_cur_m?>');"><i class="fas fa-caret-down"></i></button>
			<button type="button" id="branch_view_btn_hide_<?=$_ary_agency[$i]["IDX"]?>" class="btnstyle1 btnstyle1-primary btnstyle1-xs" style="width:40px !important; display:none;" onclick="branchView('hide', '<?=$_ary_agency[$i]["IDX"]?>');"><i class="fas fa-caret-up"></i></button>
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
	<tr id="branch_tr_<?=$_ary_agency[$i]["IDX"]?>" style="display:none;" >
		<td colspan="9" style="padding:20px !important; box-sizing:border-box; ">
			<div id="branch_<?=$_ary_agency[$i]["IDX"]?>">
			</div>
		</td>
	</tr>
<? } ?>
</table>

		</div>
		<div style="height:50px;"></div>

	</div>
</div>

<script type='text/javascript'>
function calendal_ch(){
   location.href="<?=_A_PATH_DASHBOARD_STICS_AGENCY?>?sort_mode=<?=$_sort_mode?>&cur_y="+ $("#calendar_year").val() +"&cur_m="+ $("#calendar_month").val();
}

// 지사보기
branchView=function(mode, idx, year, month){
	if( mode == "show" ){
		$("#branch_tr_"+idx).show();
		$("#branch_view_btn_show_"+idx).hide();
		$("#branch_view_btn_hide_"+idx).show();
		$.ajax({
			type: "post",
			url : "<?=_A_PATH_DASHBOARD_STICS_AGENCY_BRANCH?>",
			data : { key : idx, cur_y : year, cur_m : month },
			success: function(oHtml) {
				$('#branch_'+idx).html(oHtml);
			}
		});
	}else{
		$("#branch_tr_"+idx).hide();
		$("#branch_view_btn_show_"+idx).show();
		$("#branch_view_btn_hide_"+idx).hide();
	}

};
</script>
<?
include "../layout/footer.php";
exit;
?>