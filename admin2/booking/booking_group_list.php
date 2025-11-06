<?
	$pageGroup = "booking";
	$pageName = "booking_group_list";

	include "../lib/inc_common.php";

	$_pn = securityVal($pn);

	$search_sql = "where BKG_IDX > 0 ";
	
	$akind = $_GET['akind'];
	$page_link_text = "?list=save";
	
	

	if(!$s_day){
		$today_date = date('Y-m-d'); 
		$day_of_the_week = date('w'); 
		$_day_of_the_date = strtotime($today_date." -".$day_of_the_week."days");
		$s_day = date('Y-m-d', $_day_of_the_date);
		$e_day = date('Y-m-d', strtotime("+10 day" , $_day_of_the_date));
	}


	if($s_day){
		$_show_start_date = strtotime($s_day);
		$search_sql.= " and BKG_START_DATE >= ".$_show_start_date;
		$page_link_text .= "&s_day=".$s_day;
	}
	if($e_day){
		$dend2 = explode("-",$e_day);
        $_show_end_date = mktime(23,59,59,$dend2[1],$dend2[2],$dend2[0]);

		$search_sql.= " and BKG_START_DATE <= ".$_show_end_date;
		$page_link_text .= "&e_day=".$e_day;
	}
	if($search_type){
		$search_sql.= " and BKG_TYPE =  '".$search_type."'";
		$page_link_text .= "&search_type=".$search_type;
	}
	if($search_guide){
		$search_sql.= " and BKG_GID_ID = '".$search_guide."'";
		$page_link_text .= "&search_guide=".$search_guide;
	}
	if($search_bkg_idx){
		$search_sql.= " and BKG_IDX = '".$search_bkg_idx."'";
		
	}
	/*
	if($search_text){
		$str .= "INSTR(LOWER($필드명), LOWER('$검색어'))";
		$search_sql.= " and SS_GUIDE_NAME = '".$search_text."'";
		$page_link_text .= "&search_text=".$search_text;
	}
    */
	//배정 상태
	if( $akind != "" AND $akind != "all" ){
		$search_sql .= " and BKG_KIND = '".$akind."' ";
	}



	$total_count = wepix_counter(_DB_BOOKING_GROUP, $search_sql);

	$list_num = 20;
	$page_num = 10;

	// 전체 페이지 계산
	$total_page  = @ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
	$counter = $total_count - (($_pn - 1) * $list_num);

	$query = "select * from "._DB_BOOKING_GROUP." ".$search_sql." order by BKG_START_DATE desc limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$paging_url = _A_PATH_BOOKING_GROUP_LIST.$page_link_text."&pn=";
	$_view_paging = publicPaging($_pn, $total_page, $list_num, $page_num, $paging_url);



    
include "../layout/header.php";
?>

<div id="contents_head">
	<h1>부킹그룹 리스트</h1>
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

					<table class="table-list">
						<tr>
							<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
							<th class="tl-idx">고유번호</th>
							<th width="60px">그룹타입</th>
							<th>그룹이름</th>
							<th width="70px">진행현황</th>
							<th width="60px">부킹수</th>
							<th width="60px">인원수</th>
							<th width="100px">담당 가이드</th>
							<th width="120px">그룹시작 날짜</th>
							<th width="120px">그룹 끝 날짜</th>
							<th width="140px">관리</th>
	
						</tr>
<?	
$_row_num = 0;
while($bkg_list = wepix_fetch_array($result)){ 
	$guide_data = wepix_fetch_array(wepix_query_error("select GD_NICK from "._DB_GUIDE." where GD_ID = '".$bkg_list[BKG_GID_ID]."' "));
	$_view2_start_date = date("y.m.d", $bkg_list[BKG_START_DATE]);
	$_view2_end_date = date("y.m.d", $bkg_list[BKG_END_DATE]);
	$_view_booking_state = $bkg_list[BKG_STATE];
	$_submit_btn_color = 'btnstyle1-primary';
	$_submit_btn_name = '정산 하기';
	if($_view_booking_state == 'Y'){
		$_submit_btn_name = '정산 수정';
		$_submit_btn_color = 'btnstyle1-success';
	}

	$_row_num++;
	$trcolor = "#ffffff";
	if($_row_num%2 == 0){
		$trcolor = "#eee";
	}
?>
						<tr  id="trid_<?=$bkg_list[BKG_IDX]?>" bgcolor="<?=$trcolor?>">
							<td class="tl-check"><input type="checkbox" name="key_check[]"  class="checkSelect" value="<?=$list[BKG_IDX]?>"></td>
							<td class="tl-idx"><?=$bkg_list[BKG_IDX]?></td>
							<td><?=$bkg_list[BKG_TYPE]?></td>
							<td class="text-left"><?=$bkg_list[BKG_NAME]?></td>
							<td></td>
							<td><?=$bkg_list[BKG_BKP_COUNT]?></td>
							<td><?=$bkg_list[BKG_HEAD_COUNT]?></td>
							<td><b><?=$guide_data[GD_NICK]?></b><br> (<?=$bkg_list[BKG_GID_ID]?>)</td>
							<td><?=$_view2_start_date?></td>
							<td><?=$_view2_end_date?></td>
							<td>
								<input type="button" value="그룹수정 " style="margin-bottom:5px !important;" onclick="location.href='<?=_A_PATH_BOOKING_GROUP_REG?><?=$page_link_text?>&idx=<?=$bkg_list[BKG_IDX]?>'"><br>
								

						<button type="button" id="" class="btnstyle1 <?=$_submit_btn_color?> btnstyle1-sm" onclick="CalculateModify('<?=$bkg_list[BKG_IDX]?>');" > 
							<?=$_submit_btn_name?>
						</button>
<!-- 
								<input type="button" id='onTeam_<?=$bkg_list[BKG_IDX]?>' value="팀보기" class="btnstyle1 btnstyle1-inverse btnstyle1-sm"  onclick="goTeam('<?=$bkg_list[BKG_IDX]?>')">
								<input type="button" id='offTeam_<?=$bkg_list[BKG_IDX]?>' value="팀접기" style='display:none;' class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="del_showTable('<?=$bkg_list[BKG_IDX]?>')">
 -->
							</td>
						</tr>
<? } ?>
					</table>

				</div><!-- #list_box2 -->
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
					<form name='search_form' method='post' action="<?=_A_PATH_BOOKING_GROUP_LIST?>">
					<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
					<ul class="filter-from-ui m-t-5">
						<select name="search_type">
							<option value="" >타입선택</option>
<?
for ($i=0; $i<count($booking_type_array); $i++){
?>
							<option value="<?=$booking_type_array[$i]['code']?>" <?if($booking_type_array[$i]['code'] == $search_type){ echo "selected";}?>><?=$booking_type_array[$i]['code']?> : <?=$booking_type_array[$i]['ename']?></option>
<? } ?>
						</select>
					</ul>
					<ul class="filter-from-ui m-t-5">
						<select name="search_guide">
							<option value="" >가이드</option>
<?
	$guide_query = "select * from "._DB_GUIDE." order by GD_NICK asc";
	$guide_result = wepix_query_error($guide_query);
	while($guide_list = wepix_fetch_array($guide_result)){
?>
							<option value="<?=$guide_list[GD_ID]?>" <?if($guide_list[GD_ID] == $search_guide){ echo "selected";}?>><?=$guide_list[GD_NICK]?> ( <?=$guide_list[GD_NAME]?> )</option>
<? } ?>
						</select>
					</ul>
					<ul class="filter-from-ui m-t-5">
						<input type="text" id="s_day" name="s_day" value="<?=$s_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="시작일" readonly /> ~
						<input type="text" id="e_day" name="e_day" value="<?=$e_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="종료일" readonly />
					</ul>
					<ul class="filter-from-ui m-t-5">
						<input type='text' name='search_bkg_idx' id='board_subject' size='20' value="<?=$search_text?>" placeholder="검색어">
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
function CalculateModify(key){
	
	window.open("<?=_A_PATH_GROUP_CALCUATE?>?idx="+key, "overlap_"+key, "width=1280,height=980,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
function goSerch(){

	var form1 = document.search_form;
	form1.submit();
}
<!-- 
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
?>