<?
$pageGroup = "dashboard";
$pageName = "bridge";

include "../lib/inc_common.php";
include "../layout/header.php";

	$today_date = date("Y-m-d");
	$yesterday_date =date("Y-m-d", strtotime("-1 day"));
	
	if(!$s_day){
		$s_day = $today_date;
		$e_day = $today_date;
	}

	$_s_day = $s_day." 00:00:00";
	$_e_day = $e_day." 23:59:59";
	$search_sql = "WHERE bl_ip != '119.193.168.78' AND bl_datetime >= '".$_s_day."' AND bl_datetime <= '".$_e_day."' ";
	$cl_result = wepix_query_error("SELECT bl_prd_idx,count(DISTINCT bl_ip) ip_count FROM bridge_log ".$search_sql."  GROUP BY bl_prd_idx ORDER BY ip_count DESC");


?>

<div id="contents_head">
	<h1>접속 통계</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">

					<table class="table-list" style='width:900px !important ; float:left; margin-right:60px;'>
						<tr>
							<th>상품명</th>
							<th>업체</th>
							<th>총클릭수</th>
							<th>순인원</th>
						</tr>
						
<?
							while($cl_list = wepix_fetch_array($cl_result)){
								$comparison_data = wepix_fetch_array(wepix_query_error("select CD_NAME,CD_IDX from "._DB_COMPARISON." where CD_IDX = '".$cl_list[bl_prd_idx]."' "));
								$total_count = wepix_counter("bridge_log", $search_sql." AND bl_prd_idx = '".$cl_list[bl_prd_idx]."'");
								//$unique_count = wepix_counter2("bridge_log", $search_sql." AND bl_prd_idx = '".$cl_list[bl_prd_idx]."'","bl_ip");
?>								
							<tr>
								<td><span onclick="comparisonQuick('<?=$comparison_data[CD_IDX]?>');" style="cursor:pointer;"><?=$comparison_data[CD_NAME]?></span></td>	
								<td>
									<table>
										<tr>
											<th colspan='8'>업체명 / 클릭수</th>
										
										</tr>
										<?
											$cl_result2 = wepix_query_error("SELECT COUNT(bl_idx) AS link_count , bl_link_idx AS link_idx FROM bridge_log WHERE bl_prd_idx = '".$cl_list[bl_prd_idx]."' AND bl_ip != '119.193.168.78' AND bl_datetime >= '".$_s_day."' AND bl_datetime <= '".$_e_day."' GROUP BY bl_link_idx ORDER BY link_count desc");
											$while_num = 0;
											$while_num2 = 0;

										while($cl_list2 = wepix_fetch_array($cl_result2)){
											$comparison_data = wepix_fetch_array(wepix_query_error("select CL_SD_IDX from "._DB_COMPARISON_LINK." where CL_IDX = '".$cl_list2[link_idx]."' "));
											$site_data = wepix_fetch_array(wepix_query_error("select SD_NAME from "._DB_SITE." where SD_IDX = '".$comparison_data[CL_SD_IDX]."' "));
											if($while_num2 == 0) $crown_icon = "<i class='fas fa-crown' style='color:#FFDC3C;'; ></i>";
											if($while_num2 == 1) $crown_icon = "<i class='fas fa-crown' style='color:#b4b4b4;'; ></i>";
											if($while_num2 == 2) $crown_icon = "<i class='fas fa-crown' style='color:#A05C37;'; ></i>";
											if($while_num2 > 2) $crown_icon = "";

											?>
										<?if($while_num2 <= 4){?>
											<?if($while_num == 0){?><tr><?}?>
													<td style='text-align:left;'><?=$crown_icon?> <?=$site_data[SD_NAME]?></td>
													<td><?=$cl_list2[link_count]?></td>
											<?if($while_num == 3){?></tr> <?$while_num = 0;}?>
										<?}?>
										<?if($while_num2 > 4){?>
											<?if($while_num == 0){?><tr><?}?>
												<td style='text-align:left;'><?=$site_data[SD_NAME]?></td>
												<td><?=$cl_list2[link_count]?></td>
											<?if($while_num == 4){?></tr> <?$while_num = 0;}?>
										
										<?}?>
										<?  $while_num++; $while_num2++;?>
										<?}?>
									</table>
								</td>
								<td><?=number_format($total_count)?></td>
								<td><?=number_format($cl_list[ip_count])?></td>
							</tr>
						<?}?>
					</table>


				</div><!-- #list_box2 -->
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
					<form name='search_form' method='post' action="bridge_log.php">
					<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
					
					<ul class="filter-from-ui m-t-5">
						<input type="text" id="s_day" name="s_day" value="<?=$s_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="시작일" readonly /> ~
						<input type="text" id="e_day" name="e_day" value="<?=$e_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="종료일" readonly />
					</ul>
					
					<ul class="filter-from-ui m-t-5">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="goSerch();" > 
							<i class="fas fa-search"></i> 검색
						</button>
					</ul>
				</div>
				</form>
			</ul>
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>
	</div>
 </div>

<link rel="stylesheet" href="/admin2/css/daterangepicker.css" />
<script src="/admin2/js/moment.min.js"></script>
<script src="/admin2/js/jquery.daterangepicker.js"></script>
<script type="text/javascript"> 
<!-- 

function goSerch(){

	var form1 = document.search_form;
	form1.submit();
}

$(function(){
/*
	$("#s_day").datepicker();
	$("#e_day").datepicker();
*/
	$('#s_day').dateRangePicker(
	{
		separator : ' to ',
		getValue: function()
		{
			if ($('#date-range200').val() && $('#date-range201').val() )
				return $('#date-range200').val() + ' to ' + $('#date-range201').val();
			else
				return '';
		},
		setValue: function(s,s1,s2)
		{
			$('#s_day').val(s1);
			$('#e_day').val(s2);
		}
	});
});
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>
