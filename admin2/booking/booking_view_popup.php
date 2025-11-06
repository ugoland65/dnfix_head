<?
$pageGroup = "booking";
$pageName = "booking_view_popup";

include "../lib/inc_common.php";

	$_bkp_idx = securityVal($key);

	$bk_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING." where BKP_IDX = '".$_bkp_idx."' "));

	$_view_bkp_idx = $bk_data[BKP_IDX];
	$_view_bkg_code = $bk_data[BKP_BKG_CODE]; //그룹 코드

	$_view_team_name = $bk_data[BKP_TEAM_NAME]; //팀 이름
	$_view_bkp_req_date = date("Y-m-d H:i:s", $bk_data[BKP_REQ_DATE]);
	$_view_bkp_booking_date = date("Y-m-d",$bk_data[BKP_BOOKING_DATE]);
	$_view_bkp_booking_mod_date = date("Y-m-d",$bk_data[BKP_BOOKING_MO_DATE]);

	$_view_bkp_start_date = date("Y-m-d", $bk_data[BKP_START_DATE]);
	$_view_bkp_arrive_date = date("Y-m-d", $bk_data[BKP_ARRIVE_DATE]);

	$_ary_bkp_guest = explode("│",$bk_data[BKP_GUEST]); //게스트

	$_ary_bkp_hotel = explode("│",$bk_data[BKP_HOTEL]); //호텔
	$_ary_bkp_hot_check_in = explode("│",$bk_data[BKP_HOT_CHECK_IN]); //체크인
	$_ary_bkp_hot_check_out = explode("│",$bk_data[BKP_HOT_CHECK_OUT]); //체크아웃
	$_ary_bkp_hot_bed_type = explode("│",$bk_data[BKP_HOT_BED_TYPE]); //베드타입
	$_ary_bkp_hot_option = explode("│",$bk_data[BKP_HOT_ALLIN_YN]);
	$_ary_bkp_hot_booking_state = explode("│",$bk_data[BKP_HOT_BOOKING_STATE]);
	$_ary_schedule_day = explode("│",$bk_data[BKP_SCHEDULE_DAY]);

	$agency_head_data = wepix_fetch_array(wepix_query_error("select AG_COMPANY from "._DB_AGENCY."  where AG_IDX = '".$bk_data[BKP_AGENCY]."'"));
	$_view2_agency_head = $agency_head_data[AG_COMPANY];

	$agency_branch_data = wepix_fetch_array(wepix_query_error("select AG_COMPANY from "._DB_AGENCY."  where AG_IDX = '".$bk_data[BKP_BUSINESS]."'"));
	$_view2_agency_branch = $agency_branch_data[AG_COMPANY];

	if( $_view2_agency_branch ){
		$_view2_agency = $_view2_agency_head." > <b>".$_view2_agency_branch."</b>";
	}else{
		$_view2_agency = "<b>".$_view2_agency_head."</b>";
	}

	//그룹정보
	$bkg_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING_GROUP." where BKG_CODE ='".$_view_bkg_code."' "));
	$_view_bkg_name = $bkg_data[BKG_NAME]; //그룹 이름
	$_view_bkg_idx = $bkg_data[BKG_IDX]; //그룹고유번호
	$_view_bkg_team_count = $bkg_data[BKG_BKP_COUNT]; //그룹 팀수
	$_view_bkg_head_count = $bkg_data[BKG_HEAD_COUNT]; //그룹 인원수

	$bkp_query = "select * from "._DB_BOOKING_PARENT." where BKP_BKG_CODE = '".$_view_bkg_code."' order by BKP_START_DATE asc";
	$bkp_result = wepix_query_error($bkp_query);

include "../layout/header_popup.php";
?>

<STYLE TYPE="text/css">
.air-icon{ font-size:20px; color:#999; margin:5px 0; }
.air-num{ font-size:15px; font-weight:bold; margin:5px 0; }
</STYLE>

<div id="wrap">

    <div class="mFixNav fixed">
        <ul class="nav">
			<li class="selected"><a href="#QA_detail1">기본정보</a></li>
			<li><a href="#QA_detail2">손님정보</a></li>
			<li><a href="#QA_detail3">호텔정보</a></li>
			<li><a href="#QA_detail4">Fast Track</a></li>
			<li><a href="#QA_detail5">그룹정보</a></li>
			<li><a href="#QA_detail6">일정정보</a></li>
        </ul>
	</div>

	<div id="QA_detail1" class="section">
        <div class="section-title">
			<h2>기본 정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds11">팀 이름</th>
				<td class="tds22"><?=$_view_team_name?></td>
				<th class="tds11">팀 고유번호</th>
				<td class="tds22"><?=$_view_bkp_idx?></td>
			</tr>

			<tr>
				<th class="tds11">지역</th>
				<td class="tds22"><b><?=$bk_data[BKP_AREA]?></b></td>
				<th class="tds11">투어타입</th>
				<td class="tds22"><b><?=$bk_data[BKP_TYPE]?></b> (<?=$bva_tr_booking_type_en[$bk_data[BKP_TYPE]]?>)</td>
			</tr>

			<tr>
				<th class="tds11">상태</th>
				<td class="tds22"><?=$bk_data[BKP_KIND]?></td>
				<th class="tds11">등록일</th>
				<td class="tds22"><?=$_view_bkp_req_date?></td>
			</tr>

			<tr>
				<th class="tds11">최초 부킹일</th>
				<td class="tds22"><?=$_view_bkp_booking_date?></td>
				<th class="tds11">변경 부킹일</th>
				<td class="tds22"><?=$_view_bkp_booking_mod_date?></td>
			</tr>

			<tr>
				<th class="tds11">담당 가이드</th>
				<td class="tds22"><?=$bk_data[BKP_GUIDE_ID]?></td>
				<th class="tds11">예약자</th>
				<td class="tds22"><?=$bk_data[BKP_RESERVER]?></td>
			</tr>
<!-- 
			<tr>
				<td colspan="4" style="padding:0; height:5px; border:none;"></td>
			</tr>
 -->
		</table>

	</div>

	<div class="section">
        <div class="section-title">
			<h2>에이전시</h2>
        </div>
		<table cellspacing="0" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds11">에이전시</th>
				<td class="tds22">
					<?=$_view2_agency?>
				</td>
				<th class="tds11">에이전시 상태</th>
				<td class="tds22">

				</td>
			</tr>
			<tr>
				<th class="tds11">매칭코드</th>
				<td class="tds22">
					<?=$bk_data[BKP_MACHING_CODE]?>
				</td>
				<th class="tds11">지상비</th>
				<td class="tds22">
					<?=number_format($bk_data[BKP_LAND_FEE])?> 원
				</td>
			</tr>
		</table>
	</div>

	<div class="section">
        <div class="section-title">
			<h2>항공 정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds1">IN</th>
				<th class="tds1">OUT</th>
			</tr>
			<tr align="center">
				<td>
					<div class="air-icon"><i class="fas fa-plane-arrival"></i></div>
					<div><?=$_view_bkp_start_date?></div>
					<div class="air-num"><?=$bk_data[BKP_START_FLIGHT]?></div>

				</td>
				<td>
					<div class="air-icon"><i class="fas fa-plane-departure"></i></div>
					<div><?=$_view_bkp_arrive_date?></div>
					<div class="air-num"><?=$bk_data[BKP_ARRIVE_FLIGHT]?></div>
				</td>
			</tr>
		</table>
	</div>

	<!-- 손님정보 -->
	<div id="QA_detail2" class="section">
        <div class="section-title">
			<h2>손님 정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" id="guestPt">
			<tr>
				<th>구분</th>
				<th>한국 이름</th>
				<th>영문 이름</th>
				<th>매칭 ID</th>
			</tr>
			<?
				for( $i=0; $i<count($_ary_bkp_guest); $i++ ){
					$_ary2_guest_info = explode("/",$_ary_bkp_guest[$i]);
					$_view2_guest_title = $_ary2_guest_info[0];
					$_view2_guest_name_kr = $_ary2_guest_info[1];
					$_view2_guest_name_en = strtoupper($_ary2_guest_info[2]);
					$_view2_guest_id = $_ary2_guest_info[3];
			?>
			<tr>
				<td><?=$_view2_guest_title?></td>
				<td><?=$_view2_guest_name_kr?></td>
				<td><?=$_view2_guest_name_en?></td>
				<td><?=$_view2_guest_id?></td>
			</tr>
			<? } ?>
		</table>

	</div>

	<!-- 호텔 정보 -->
	<div id="QA_detail3" class="section">
        <div class="section-title">
			<h2>호텔 정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" id="hotelPt">
			<tr>
				<th class="checkbox-td"></th>
				<th style="width:80px">체크 인/아웃</th>
				<th>호텔 / 룸타입</th>
				<th style="width:100px">베드 타입</th>
				<th style="width:100px">옵션</th>
				<th style="width:40px">객실수</th>
				<th style="width:40px">숙박일</th>
				<th style="width:60px">방번호</th>
				<th style="width:60px">상태</th>
			</tr>
			<?
				for( $i=0; $i<count($_ary_bkp_hotel); $i++ ){
					$_ary2_hotel_info = explode(":",$_ary_bkp_hotel[$i]);
					$_ary2_hotel_option = explode(",",$_ary_bkp_hot_option[$i]);
					$_ary2_schedule = explode("/",$_ary_schedule_day[$i]);

					$_view2_hotel_name = $_ary2_hotel_info[1];
					$_view2_room_type = $_ary2_hotel_info[3];
					$_view2_hotel_check_in_day = $_ary_bkp_hot_check_in[$i];
					$_view2_hotel_check_out_day = $_ary_bkp_hot_check_out[$i];
					$_view2_hotel_bed_type = $_ary_bkp_hot_bed_type[$i];
					$_view2_hotel_stay_kr_text = ( $_ary2_schedule[0] == 0 ) ? "당일" : $_ary2_schedule[0]."박";
					$_view2_room_count = $_ary2_hotel_info[4];

					if( $_ary_bkp_hot_booking_state[$i] == "" ){ $_ary_bkp_hot_booking_state[$i] = 0; }

					if( $_ary_bkp_hot_booking_state[$i] == 0 ){
						$show_hotel_sate_style = "btnstyle1-gary";
						$show_hotel_sate_icon = "fa fa-angle-double-right";
					}elseif( $_ary_bkp_hot_booking_state[$i] == 1 ){
						$show_hotel_sate_style = "btnstyle1-success";
						$show_hotel_sate_icon = "fas fa-angle-double-right";
					}elseif( $_ary_bkp_hot_booking_state[$i] == 2 ){
						$show_hotel_sate_style = "btnstyle1-primary";
						$show_hotel_sate_icon = "far fa-check-square";
					}elseif( $_ary_bkp_hot_booking_state[$i] == 3 ){
						$show_hotel_sate_style = "btnstyle1-danger";
						$show_hotel_sate_icon = "fas fa-ban";
					}else{
						$show_hotel_sate_style = "btnstyle1-danger";
					}

					$_view2_hotel_reservation_state = $bva_tr_hotel_reservation_state[$_ary_bkp_hot_booking_state[$i]];
			?>
			<tr>
				<td><input type="checkbox" name=""></td>
				<td class="text-center">
					<?=$_view2_hotel_check_in_day?><br>
					<?=$_view2_hotel_check_out_day?>
				</td>
				<td>
					<b><?=$_view2_hotel_name?></b><br>
					<?=$_view2_room_type?>
				</td>
				<td><?=$_view2_hotel_bed_type?></td>
				<td>
					<? 
					for($i2=0; $i2<count($_ary2_hotel_option); $i2++){
						if( $_ary2_hotel_option[$i2] != 'none'){
					?>
						<?=$_ary2_hotel_option[$i2]?><br>
					<? }
					} ?>
				</td>
				<td class="text-center"><?=$_view2_room_count?></td>
				<td class="text-center"><?=$_view2_hotel_stay_kr_text?></td>
				<td><input type="text" name=""></td>
				<td><button type="button" class="btnstyle1 <?=$show_hotel_sate_style?> btnstyle1-xs width-50" onclick='chKind(<?=$i?>,<?=$bkp_hot_kind[$i]?>,<?=$list[BKP_IDX]?>);'><i class="<?=$show_hotel_sate_icon?>"></i> <?=$_view2_hotel_reservation_state?></button></td>
			</tr>
			<? } ?>
		</table>
        <div class="section-button">
			<button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs " onclick='bookingInfo("<?=$_view_bkp_idx?>");'>방번호 전체변경</button>
        </div>
	</div>

	<div id="QA_detail4" class="section">
        <div class="section-title">
			<h2>Fast Track</h2>
        </div>
	</div>

	<div id="QA_detail6" class="section">
        <div class="section-title">
			<h2>일정정보</h2>
        </div>
<?
	$schedule_count = wepix_counter2(_DB_SCHEDULE_TRAVEL, "where SC_BK_IDX = '".$_view_bkp_idx."' ","SC_DAY_NUM");
	$_view_schedule_count = $schedule_count;
?>
			<div class="schedule-count-wrap" style='margin-top:10px; margin-bottom:10px;'>
				<span class="schedule-count">총 ( <b><?=$_view_schedule_count?></b> )개의 일정이 등록되어 있습니다.</span>
			</div>

	</div>
<!--
	<div id="QA_detail5" class="section">
        <div class="section-title">
			<h2>그룹정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds11">그룹 이름</th>
				<td class="tds22"><?=$_view_bkg_name?></td>
				<th class="tds11">그룹 고유번호</th>
				<td class="tds22"><?=$_view_bkg_idx?></td>
			</tr>
			<tr>
				<th class="tds11">그룹 팀수</th>
				<td class="tds22"><?=$_view_bkg_team_count?></td>
				<th class="tds11">그룹 총인원수</th>
				<td class="tds22"><?=$_view_bkg_head_count?></td>
			</tr>
		</table>

		<table cellspacing="1" cellpadding="0" class="table-style" style="margin-top:5px;">
			<tr>
				<th class="checkbox-td"></th>
				<th style="width:80px;">타입</th>
				<th>팀이름</th>
				<th style="width:300px;">IN/OUT</th>
				<th  style="width:80px;">보기</th>
			</tr>

<?
while($bkp_list = wepix_fetch_array($bkp_result)){
	$_view2_bkp_idx = $bkp_list[BKP_IDX];
	$_view2_bkp_type = $bkp_list[BKP_TYPE];
	$_view2_team_names = $bkp_list[BKP_TEAM_NAME];

	$_view2_start_flight = $bkp_list[BKP_START_FLIGHT];
	$_view2_arrive_flight = $bkp_list[BKP_ARRIVE_FLIGHT];
	$_view2_start_date = date("d-M", $bkp_list[BKP_START_DATE]);
	$_view2_arrive_date = date("d-M", $bkp_list[BKP_ARRIVE_DATE]);
?>
			<tr>
				<td ></td>
				<td class="text-center"><?=$_view2_bkp_type?></td>
				<td ><?=$_view2_team_names?></td>
				<td class="text-center">(<?=$_view2_start_flight?>) <?=$_view2_start_date?> ~ (<?=$_view2_arrive_flight?>) <?=$_view2_arrive_date?></td>
				<td class="text-center">
					<? if( $_view2_bkp_idx != $_bkp_idx){ ?>
					<button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs " onclick='bookingInfo("<?=$_view2_bkp_idx?>");'>정보보기</button>
					<? } ?>
				</td>
			</tr>
<? } ?>

		</table>
	</div>
-->
</div>

<script type="text/javascript"> 
<!-- 
function bookingInfo(key){
	window.open("<?=_A_PATH_BOOKING_VIEW_POPUP?>?key="+key, "overlap_"+key, "width=900,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
//--> 
</script> 
<?
include "../layout/footer_popup.php";
exit;
?>