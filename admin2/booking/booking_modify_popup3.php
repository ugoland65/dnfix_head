<?
include "../lib/inc_common.php";

$pageGroup = "booking";
$pageName = "booking_reg";


	$_mode = securityVal($mode);
	$_bkp_idx = securityVal($key);


	$page_title_text = "New Booking";
	$submit_btn_text = "Save";
	$golf_dispaly ='display:none';
    $option_dispaly ='display:none';
	$_view_bkp_booking_date =  date("Y-m-d",$wepix_now_time);

	$booking_type_array2 = array("HM", "FA", "GP", "RO", "GF", "PKG", "ICT", "INS");
	$search_box = "";

	//검색박스 - 부킹 수동상태
	$ary_scb_booking_kind = "";
	for( $i=0; $i<count($booking_kind_array); $i++ ){
		if( ${"search_check_box_".$booking_kind_array[$i]} == 'on' ){
			$ary_scb_booking_kind[] = $booking_kind_array[$i];
			$search_box .= "&search_check_box_".$booking_kind_array[$i]."=on";
		}
    }
	//검색박스 - 지역
	$ary_scb_booking_area = "";
	for( $i=0; $i<count($booking_area_array); $i++ ){
		if( ${"search_check_box_".$booking_area_array[$i]} == 'on' ){
		
			$search_box .= "&search_check_box_".$booking_area_array[$i]."=on";
		}
	}
	//부킹종류
	$ary_scb_booking_type = "";
	for( $i=0; $i<count($booking_type_array2); $i++ ){
		if( ${"search_check_box_".$booking_type_array2[$i]} == 'on' ){
			$ary_scb_booking_type[] = $booking_type_array2[$i];
			$search_box .= "&search_check_box_".$booking_type_array2[$i]."=on";
		}
	}

if($_mode == 'modify'){
		

		

		$bk_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING." where BKP_IDX = '".$_bkp_idx."' "));

		if($bk_data[BKP_MACHING_CODE] == ''){
            $randomNum = mt_rand(10, 99);
            $bkp_maching_code = $bk_data[BKP_AGENCY].$randomNum.$_bkp_idx;
            wepix_query_error("update "._DB_BOOKING." set BKP_MACHING_CODE = '".$bkp_maching_code."' where BKP_IDX = '".$_bkp_idx."'");
        }

		if($bk_data[BKP_TYPE] == 'GF'){
			$golf_dispaly='';
		}

		$_view_bkp_idx = $bk_data[BKP_IDX];
		$_view_bkg_code = $bk_data[BKP_BKG_CODE]; //그룹 코드
		$_view_bkp_type = $bk_data[BKP_TYPE]; //

		$_view_team_name = $bk_data[BKP_TEAM_NAME]; //팀 이름
		$_view_bkp_req_date = date("Y-m-d H:i:s", $bk_data[BKP_REQ_DATE]);
		$_view_bkp_booking_date = date("Y-m-d",$bk_data[BKP_BOOKING_DATE]);

			$_view_bkp_booking_mod_date = date("Y-m-d",$bk_data[BKP_BOOKING_MO_DATE]);

		$_view_agency_yn = $bk_data[BKP_AGENCY_CONFIRM_YN];
		$_view_bkp_start_date = date("Y-m-d", $bk_data[BKP_START_DATE]);
		$_view_bkp_arrive_date = date("Y-m-d", $bk_data[BKP_ARRIVE_DATE]);
		if($bk_data[BKP_START_DATE2] != 0 ){
		 $_view_bkp_start_date2 = date("Y-m-d", $bk_data[BKP_START_DATE2]);
		 $_view_bkp_arrive_date2 = date("Y-m-d", $bk_data[BKP_ARRIVE_DATE2]);
        }
		$_view_bkp_arrive_flight = $bk_data[BKP_ARRIVE_FLIGHT];
		$_view_bkp_arrive_flight2 = $bk_data[BKP_ARRIVE_FLIGHT2];
		$_view_bkp_start_flight = $bk_data[BKP_START_FLIGHT];
		$_view_bkp_start_flight2 = $bk_data[BKP_START_FLIGHT2];
		$_view_bkp_first_money = $bk_data[BKP_FIRST_MONEY];
		$_view_bkp_land_fee = $bk_data[BKP_LAND_FEE];
		$_view_maching_code = $bk_data[BKP_MACHING_CODE];
		$_view_admin_memo = $bk_data[BKP_MEMO_ADMIN];
		$_view_memo = $bk_data[BKP_MEMO];
		$_view_mod_log = $bk_data[BKP_MOD_LOG];

		
		$_ary_bkp_guest = explode("│",$bk_data[BKP_GUEST]); //게스트
		$_ary_similan = explode(",",$bk_data[BKP_SIMILAN]); 
		$_ary_guest_age = explode("│",$bk_data[BKP_GUEST_AGE]);
		$_ary_guest_birth = explode("│",$bk_data[BKP_GUEST_BIRTH]);
		$_ary_guest_pass_num = explode("│",$bk_data[BKP_GUEST_PASSPORT_NUM]);
		$_ary_guest_pass_date = explode("│",$bk_data[BKP_GUEST_PASSPORT_DATE]);
		$_ary_land_fee_text = explode("│",$bk_data[BKP_LAND_FEE_TEXT]);


		$_ary_bkp_hotel = explode("│",$bk_data[BKP_HOTEL]); //호텔
		$_ary_bkp_head_count = explode("│",$bk_data[BKP_HOT_HEAD_COUNT]);
		$_ary_bkp_head_count_c = explode("│",$bk_data[BKP_HOT_HEAD_COUNT_CHILD]);
		$_ary_bkp_hot_cf_num = explode("│",$bk_data[BKP_HOT_CONFIRM_NUM]);
		$_ary_bkp_hot_memo = explode("│",$bk_data[BKP_HOT_MEMO]);
		$_ary_bkp_hot_check_in = explode("│",$bk_data[BKP_HOT_CHECK_IN]); //체크인
		$_ary_bkp_hot_check_out = explode("│",$bk_data[BKP_HOT_CHECK_OUT]); //체크아웃
		$_ary_bkp_hot_bed_type = explode("│",$bk_data[BKP_HOT_BED_TYPE]); //베드타입
		$_ary_bkp_room_num = explode("│",$bk_data[BKP_ROOM_NUMBER]); //방번호
		$_ary_bkp_hot_option = explode("│",$bk_data[BKP_HOT_ALLIN_YN]);	
		$_ary_bkp_hot_booking_state = explode("│",$bk_list[BKP_HOT_BOOKING_STATE]);


		$_ary_bkp_hot_option_price = explode("│",$bk_data[BKP_HOT_ALLIN_PRICE]);
		$_ary_bkp_hot_booking_state = explode("│",$bk_data[BKP_HOT_BOOKING_STATE]);
		$_ary_schedule_day = explode("│",$bk_data[BKP_SCHEDULE_DAY]);
		$_ary_bkp_hot_total_price = explode("│",$bk_data[BKP_HOT_TOTAL_PRICE]);

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
		
		$_ary_bkp_idx = explode(",",$bkg_data[BKG_BKP_IDX]);
		for($i=0;$i<count($_ary_bkp_idx);$i++){
			if($_ary_bkp_idx[$i] != ''){
				$bkp_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING." where BKP_IDX = ".$_ary_bkp_idx[$i]));
				$_ary_bkp_team_name[] = $bkp_data[BKP_TEAM_NAME];
				$_ary_bkg_hotel[] =  $bkp_data[BKP_HOTEL]; //호텔
				$_ary_bkg_schedule_day[] = $bkp_data[BKP_SCHEDULE_DAY];
				$_ary_bkp_start_date[] = date("d-M-y",$bkp_data[BKP_START_DATE]);
				$_ary_bkp_arrive_date[] = date("d-M-y",$bkp_data[BKP_ARRIVE_DATE]);
				$_ary_bkp_start_flight[] = str_replace(" ","",strtoupper($bkp_data[BKP_START_FLIGHT]));
				$_ary_bkp_arrive_flight[] = str_replace(" ","",strtoupper($bkp_data[BKP_ARRIVE_FLIGHT]));
				$_ary_bkp_first_money[] = number_format($bkp_data[BKP_FIRST_MONEY]);

				$buy_pd_sum = wepix_fetch_array(wepix_query_error("select sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BK_IDX = '".$_ary_bkp_idx[$i]."' "));
				$_show_total_buy_pd = $buy_pd_sum[total_price];

				$_view_bkp_use_tm[] = number_format($_show_total_buy_pd); // 총사용 T.M
				$_view_bkp_over_tm[] = number_format($_show_total_buy_pd-$bkp_data[BKP_FIRST_MONEY]); //총 추가 T.M

				$_view_bkp_discount_rate[] = $bkp_data[BKP_DISCOUNT_RATE];
				$_view_bkp_charge_tm[] = number_format(($_show_total_buy_pd-$bkp_data[BKP_FIRST_MONEY]) * (100 - $bkp_data[BKP_DISCOUNT_RATE]) / 100);
				$_view_bkp_total_tm += ($_show_total_buy_pd-$bkp_data[BKP_FIRST_MONEY]) * (100 - $bkp_data[BKP_DISCOUNT_RATE]) / 100;
			}
		}

		$bkp_query = "select * from "._DB_BOOKING_PARENT." where BKP_BKG_CODE = '".$_view_bkg_code."' order by BKP_START_DATE asc";
		$bkp_result = wepix_query_error($bkp_query);
		
		$wanted_query = "select * from "._DB_WANTED." where WP_BKP_MACHING_CODE = '".$_view_maching_code."' and WP_KIND = 'WANTED' order by WP_REG_DATE asc";
		$wanted_result = wepix_query_error($wanted_query);

		$wanted_query2 = "select * from "._DB_WANTED." where WP_BKP_MACHING_CODE = '".$_view_maching_code."' and WP_KIND = 'CONFIRM' order by WP_REG_DATE asc";
		$wanted_result2 = wepix_query_error($wanted_query2);

		$page_title_text = "Modify Booking";
		$submit_btn_text = "Save";

		$_view_land_fee = 0;
		for($a=0;$a<count($_ary_land_fee_text);$a++){
			$_ary2_bkp_land_fee_text = explode("/",$_ary_land_fee_text[$a]);

			if($_ary2_bkp_land_fee_text[2] != 0 && $_ary2_bkp_land_fee_text[3] != 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[2] *($_ary2_bkp_land_fee_text[3] * $_ary2_bkp_land_fee_text[1]);
			}elseif($_ary2_bkp_land_fee_text[2] != 0 && $_ary2_bkp_land_fee_text[3] == 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[1] * $_ary2_bkp_land_fee_text[2];
			}elseif($_ary2_bkp_land_fee_text[2] == 0 && $_ary2_bkp_land_fee_text[3] != 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[1] * $_ary2_bkp_land_fee_text[3];
			}
		}
	}
	
	$agc_result = wepix_query_error("select * from AGENCY where AG_KIND = 'A' and AG_VIEW = 'Y' and AG_DEL_YN='N' order by AG_COMPANY asc");
	$agc_result2 = wepix_query_error("select * from AGENCY where AG_KIND = 'B' and AG_VIEW = 'Y' and AG_DEL_YN='N' order by AG_COMPANY asc");
		
	while($agc_data = wepix_fetch_array($agc_result)){
		$agc_company[] = $agc_data[AG_COMPANY];
	}
	
	while($agc_data2 = wepix_fetch_array($agc_result2)){
		$agc_company2[] = $agc_data2[AG_COMPANY];
	}


	$page_link_text = "?search_mode=".$search_mode."&search_kind=".$search_kind."&search_st=".$search_st."&search_et=".$search_et."&search_date_kind=".$search_date_kind."&search_text=".$search_text."".$search_box."&sort_kind=".$sort_kind."&order_by=".$order_by;

	$_show_get_url = $page_link_text."&pn=".$_pn;
	
	$agc_company_array = implode(",",$agc_company);
	$agc_company_array2 = implode(",",$agc_company2);

	$hotel_query = "select HOT_IDX,HOT_NAME from "._DB_HOTEL." where HOT_VIEW = 'Y' order by HOT_NAME asc ";
    $roomtype_query = "select ROC_IDX,ROC_NAME,ROC_HOT_IDX from "._DB_HOTEL_ROOM_TYPE." where ROC_VIEW = 'Y' order by ROC_NAME asc ";
	$golf_query= "select * from "._DB_GOLF." where GF_VIEW = 'Y'";



$popup_browser_title = "부킹수정-".$_view_bkp_idx."-".$_view_team_name;
include "../layout/header_popup.php";
?>

<STYLE TYPE="text/css">
.save-btn-wrap{ z-index:300; padding:10px 10px; position:fixed; bottom:10px; right:10px; background-color:rgba(0,0,0,0.4); border:1px solid #000000; text-align:center; vertical-align:middle; }
.save-btn-wrap button{ }

.bvFixNav{ z-index:300; position:fixed; top:0px; left:0px; }
.booking-view-tap-wrap{ width:100%; background-color:#f5f5f5; box-sizing:border-box; border-bottom:1px solid #c4c4c4; }
.booking-view-tap{ display:table; table-layout:fixed; width:100%; }
.booking-view-tap ul{ display:table-cell; vertical-align:middle; border-right:1px solid #c4c4c4; border-left:1px solid #fcfcfc; } 
.booking-view-tap ul a { overflow:hidden; display:inline-block; width:100%; height:43px; line-height:43px; text-align:center;  text-align:center; vertical-align:middle; white-space:nowrap; text-overflow:ellipsis;  box-sizing:border-box; padding:0 !important; color:#333; cursor:pointer; }
.booking-view-tap ul.active{
	border-left:1px solid #00a9ff;
}
.booking-view-tap ul.active a{ 	background: -webkit-linear-gradient(180deg, #0088cc, #0044cc);
	background:    -moz-linear-gradient(180deg, #0088cc, #0044cc);
	background:     -ms-linear-gradient(180deg, #0088cc, #0044cc);
	background:      -o-linear-gradient(180deg, #0088cc, #0044cc);
	background:         linear-gradient(180deg, #0088cc, #0044cc); color:#fff; }

.section-wrap2{ box-sizing:border-box; padding:59px 20px 20px;   }
.section-title2{ width:100%; background-color:#434c60; border:1px solid #2c3549; box-sizing:border-box; padding:7px 10px; border-radius:3px; display:table; margin-bottom:5px;  }
.section-title2 ul{ display:table-cell; }
.section-title2 h2{ color:#e8f7ff; font-size:15px; margin:0 !important;}
.section-title2 .close-btn{ width:60px; }
.section-title2-sub{ margin-top:10px; font-size:14px; height:26px; line-height:26px; font-weight:bold; }
.section-contents{ margin-bottom:30px; }

.hotel-option-wrap{}
.hotel-option-wrap ul{  margin:0 !important; padding:0 !important; }

.folded-wrap-td{ padding: 1px !important; background-color:#e4e4e4 !important; }
.folded-table tr td{ background-color:#f7f7f7 !important; }
</STYLE>

<div class="save-btn-wrap">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="bookingModify();" > 
		<i class="far fa-check-circle"></i>
		저장
	</button>
</div>

<div class="booking-view-tap-wrap bvFixNav">
	<div class="booking-view-tap">
		<ul class="active"><a target="#S_detail1">Basic</a></ul>
		<ul><a target="#S_detail2">Agency</a></ul>
		<ul><a target="#S_detail11">FAST TRACK</a></ul>
		<ul><a target="#S_detail3">Guest</a></ul>
		<ul><a target="#S_detail4">Hotel</a></ul>
	
		<ul><a target="#S_detail10">Golf</a></ul>
		<ul><a target="#S_detail5">수배서/확정서</a></ul>
		<ul><a target="#S_detail6">Group</a></ul>
		<ul><a target="#S_detail7">일정</a></ul>
		<ul><a target="#S_detail8">청구서</a></ul>
		<ul><a target="#S_detail9">메모</a></ul>

	</div>
</div>

<div class="section-wrap2">
	<form name='form1'  action='<?=_A_PATH_BOOKING_OK?>' method='post' enctype="multipart/form-data">
	<div class="section-title2" id="S_detail1">
		<ul><h2>Basic Information</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_1')" id="section_contents_1_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>
	<div class="section-contents" id="section_contents_1" closeOpenState="open">
		<table class="table-reg th-150" >
			<tr>
				<th>Team Name</th>
				<td><?=$_view_team_name?></td>
				<th>Team Number</th>
				<td><?=$_view_bkp_idx?></td>
			</tr>

			<tr>
				<th>Area</th>
				<td>
					<select name = 'bkp_area' id = 'bkp_area'>
					<option value=''>Select Area</option>
					<?
							$area_query = "select * from ".$db_t_AREA." where AREA_KIND = 'L' order by AREA_IDX asc";
							$area_result = wepix_query_error($area_query);
							while($area_list = wepix_fetch_array($area_result)){
					?>
								<option value="<?=$area_list[AREA_CODE]?>"  <? if( $bk_data[BKP_AREA]== $area_list[AREA_CODE]  ) echo "selected"; ?> ><?=$area_list[AREA_CODE]?></option>
							<? } ?>
					</select>
				</td>
				<th>Tour Type</th>
				<td>
					<select name="bkp_type" id="bkp_type" class="select1" onchange="select_bkp_type();">
							<option value="">Select Tour Type</option>
						<?
						$area_query = "select * from ".$db_t_BOOKING_SETTING." where BKS_KIND = 'B' order by BKS_IDX asc";
						$area_result = wepix_query_error($area_query);
						while($area_list = wepix_fetch_array($area_result)){
						?>
							<option value="<?=$area_list[BKS_VALUE]?>"  <? if( $bk_data[BKP_TYPE] == $area_list[BKS_VALUE]  ) echo "selected"; ?> ><?=$area_list[BKS_NAME]?></option>
						<? } ?>
					</select>
				</td>
			</tr>

			<tr>
				<th>State</th>
				<td>
					<select name='bkp_kind'>			  
						<option value='NEW' <?if($bk_data[BKP_KIND] == 'NEW'){ echo "selected";}?>>NEW</option>
						<option value='AMEND' <?if($bk_data[BKP_KIND] == 'AMEND'){ echo "selected";}?>>AMEND</option>
						<option value='BLOCK' <?if($bk_data[BKP_KIND] == 'BLOCK'){ echo "selected";}?>>BLOCK</option>
						<option value='CANCEL' <?if($bk_data[BKP_KIND] == 'CANCEL'){ echo "selected";}?>>CANCEL</option>
						<option value='DUPE' <?if($bk_data[BKP_KIND] == 'DUPE'){ echo "selected";}?>>DUPE</option>
					</select>
				</td>
				<th>Guide</th>
				<td><?=$bk_data[BKP_GUIDE_ID]?></td>
			</tr>

		<tr>
			<th>IN/OUT</th>
			<td>

				<table cellspacing="0" cellpadding="0" class="table-none-new" >
					<tr>
						<td class="p-3"><input type="text" placeholder="in Transfer Date"  readonly name="date_start2"  id='date-range1' value="<?=$_view_bkp_start_date2?>" style="width:100px;"/></td>
						<td class="p-3"><input type="text" placeholder="out Transfer Date" readonly  name="date_end2" id='date-range0' value="<?=$_view_bkp_arrive_date2?>"  style="width:100px;"/></td>	</tr>
					<tr>
						<td class="p-3"><input type="text" placeholder="in Date"  name="date_start" id='date-range2' value="<?=$_view_bkp_start_date?>"  style="width:100px;"/></td>
						<td class="p-3"><input type="text" placeholder="out Date"   name="date_end" id='date-range3' value="<?=$_view_bkp_arrive_date?>" style="width:100px;"/></td>
					</tr>
				</table>
								
			</td>
			<th>Flight</th>
				<td>
				<table cellspacing="0" cellpadding="0" class="table-none-new" >
					<tr>
						<td class="p-3">
							<input type='text' placeholder="in Transfer Flight" style="width:140px; text-transform:uppercase;" name="bkp_start_flight2" value='<?=$_view_bkp_start_flight2?>'>
						</td>
						<td class="p-3">
							<input type='text' title="in flight" placeholder="in Flight" style="width:120px; text-transform:uppercase;" name="bkp_start_flight" value='<?=$_view_bkp_start_flight?>'>
						</td>
						<td class="p-3"></td>
					</tr>
					<tr>
						<td class="p-3"></td>
						<td class="p-3">
							<input type='text' title="out flight" placeholder="out Flight" style="width:120px; text-transform:uppercase;" name="bkp_arrive_flight" value='<?=$_view_bkp_arrive_flight?>'>
						</td>
						<td class="p-3">
							<input type='text' placeholder="out Transfer Flight" style="width:145px; text-transform:uppercase;" name="bkp_arrive_flight2" value='<?=$_view_bkp_arrive_flight2?>'>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<th>First Booking Date</th>
				<td><input type="text" name="booking_date"  value="<?=$_view_bkp_booking_date?>"  onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)"  style="width:85px; "/></td>
				<th>Change Booking Date</th>
				<td><input type="text"  maxlength='10' name="booking_date2" value="<?=$_view_bkp_booking_mod_date?>" readonly style="width:85px; "  /></td>
			</tr>
			<tr>
				<th >Provide T/M</th>
				<td colspan="3" >
					<input type='text' style="width:150px;" name='bkp_money_first'  id='bkp_money_first' value="<?=number_format($_view_bkp_first_money)?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
				</td>	
			</tr>
			<tr>
				<th>Registration date</th>
				<td><?=$_view_bkp_req_date?></td>
				<th>Registrant</th>
				<td><?=$bk_data[BKP_RESERVER]?></td>
			</tr>
		</table>
	</div>

	<div class="section-title2"  id="S_detail2">
		<ul><h2>Agency</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_2')" id="section_contents_2_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>
	<div class="section-contents" id="section_contents_2" closeOpenState="open">
		<table class="table-reg th-150" >
			<tr>
				<th>Agency</th>
				<td>
					 <?   $angcy_text = explode("-",$bk_data[BKP_AGNCY_TEXT]);?>
					<input type='text' name='bkp_agency_1' id='bkp_agency_1' style="width:100px;" value='<?=$angcy_text[0]?>'>
					<input type='text' name='bkp_agency_2' id='bkp_agency_2' style="width:100px;" value='<?=$angcy_text[1]?>'>
				</td>
				<th>Agency State</th>
				<td>
				<? if($_view_agency_yn == 'N'){ ?>
					<button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-xs m-t-3 width-50" onclick="agency_cf('<?=$bk_data[BKP_IDX]?>', 'N');"><i class="fas fa-angle-double-right"></i> W/T</button>
				<? }elseif( $_view_agency_yn == 'Y'){ ?>
					<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs m-t-3 width-50" onclick="agency_cf('<?=$bk_data[BKP_IDX]?>', 'Y');"><i class="far fa-check-square"></i> C/F</button>
				<? } ?>
				</td>
			</tr>
			<tr>
				<th >Maching Code</th>
				<td colspan='3'>
					<?=$_view_maching_code?>
				</td>
			</tr>
		</table>
	</div>

	<div class="section-title2">
		<ul><h2>Land Fee</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_3')" id="section_contents_3_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>
	<div class="section-contents" id="section_contents_3" closeOpenState="open">
		<div>
			<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:180px; margin:5px; float:right;" onclick="optionAdd()"> <i class="fas fa-plus-circle"></i> Add Land Fee Option </button>
		</div>
		<div>
			<table class="table-reg th-center"  id='bk_land_table' name='bk_land_table' >
				<tr>
					<th>Name</th>
					<th>Price (￦)</th>
					<th>Personnel</th>
					<th>Nights</th>
				</tr>
	<?
	for($i=0;$i<count($_ary_land_fee_text);$i++){
		$_ary_land_fee = explode("/",$_ary_land_fee_text[$i]);
	?>
				<tr>
					<td>
						<select name='bkp_landfee_name[]' class="width-full">
							<?for($l=0;$l<count($_ad_land_fee_text_array);$l++){?>
								<option value='<?=$l?>' <?if($_ary_land_fee[0] == $l){ echo "selected" ;}?>><?=$_ad_land_fee_text_array[$l]?></option>
							<?}?>
						</select>
					</td>
					<td><input type='text' name='bkp_landfee[]' value='<?=number_format($_ary_land_fee[1])?>' onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></td>
					<td><input type='text' name='bkp_landfee_people[]' value='<?=$_ary_land_fee[2]?>'></td>
					<td><input type='text' name='bkp_landfee_sn[]' value='<?=$_ary_land_fee[3]?>'></td>
				</tr>	
	<? } ?>
			</table>
		</div>
		<div class="text-right m-t-10" style="font-size:14px; font-weight:bold;">
			Total Land Fee : <span  style="font-size:18px;"><?=number_format($_view_land_fee)?></span> ￦
		</div>
	</div>

	<div class="section-title2" id="S_detail11">
		<ul><h2>Fast Track</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_1')" id="section_contents_1_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>
	<div class="section-contents" id="section_contents_1" closeOpenState="open">
	
	</div>

	<div class="section-title2" id="S_detail3">
		<ul><h2>Guest : ( <?=count($_ary_bkp_guest)?> )</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_4')" id="section_contents_4_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>
	<div class="section-contents" id="section_contents_4" closeOpenState="open">
		<table class="table-reg th-center" id="guestPt">
			<tr>
				<th style="width:65px !important;">Title</th>
				<th style="width:100px !important;">Korea Name</th>
				<th style="width:200px !important;">English Name</th>
                <th style="width:160px !important;">Birthday d/m/y</th>
                <th style="width:100px">Age</th>
                <th style="width:200px !important;">Passport number</th>
                <th>Similan</th>
                <th style="width:150px !important;">Guest ID</th>
				<th style="width:40px !important;"></th>
			</tr>
			<?
				for( $i=0; $i<count($_ary_bkp_guest); $i++ ){
					$_ary2_guest_info = explode("/",$_ary_bkp_guest[$i]);
					$_view2_guest_title = $_ary2_guest_info[0];
					$_view2_guest_name_kr = $_ary2_guest_info[1];
					$_view2_guest_name_en = strtoupper($_ary2_guest_info[2]);
					$_view2_guest_id = $_ary2_guest_info[3];

					$_view_birth_y = substr($_ary_guest_birth[$i],0,4);
					$_view_birth_m = substr($_ary_guest_birth[$i],4,2);
					$_view_birth_d = substr($_ary_guest_birth[$i],6,2);

					$birthday1 = date("Ymd",strtotime($_ary_guest_birth[$i]));
					$nowday1 = date("Ymd");
					$_view2_guest_age = floor(($nowday1 - $birthday1) / 10000);

					if($_ary_guest_birth[$i] == ''){
						$_view2_guest_age = '';
					}
			?>
			<tr  id='trid2_mo_<?=$i?>'>
				<td><select name='ges_kind[]' class='select1'>
				<?
				for( $z=0; $z<count($guest_kind_array); $z++ ){
				?>
							<option value='<?=$guest_kind_array[$z]?>' <? if( $_view2_guest_title == $guest_kind_array[$z] ) echo 'selected'; ?>><?=$guest_kind_array[$z]?></option>
				<? } ?>
			</select>
				</td>
				<td><input type='text' name='ges_ko[]' class='inputtext1' style="font-size:14px; font-weight:bold;" value="<?=$_view2_guest_name_kr?>"></td>
				<td><input type='text' name='ges_en[]' class='inputtext1 text-transform-uppercase' value="<?=$_view2_guest_name_en?>"></td>
				<input type='hidden' name='ges_Id[]' value="<?=$_view2_guest_id?>">
				<td class="text-center">
					<select name='ges_birth_d[]'>
						<option value='0'>0</option>
					<?
					for($d=1;$d<=31;$d++){
						if($d < 10){
							$value = '0'.$d;
						}else{
							$value = $d;
						}
					?>
						<option value='<?=$value?>' <?if($_view_birth_d == $value){ echo "selected";}?>><?=$d?></option>
					<?}?>
					</select>
					
					<select name='ges_birth_m[]' >
						<option value='0'>0</option>
					<?
					for($m=1;$m<=12;$m++){
						if($m < 10){
							$value = '0'.$m;
						}else{
							$value = $m;
						}
					?>
						<option value='<?=$value?>' <?if($_view_birth_m == $value){ echo "selected";}?>><?=$m?></option>
					<?}?>
					</select>

					<select name='ges_birth_y[]'>
						<option value='0'>0000</option>
					<?
					$birth_y = date('Y',$wepix_now_time);
					for($y=1930;$y<=$birth_y;$y++){?>
						<option value='<?=$y?>' <?if($_view_birth_y == $y){ echo "selected";}?> ><?=$y?></option>
					<?}?>
					</select>
					<!--<input type='text' name='ges_birth[]' class='inputtext1' value="<?=$_ary_guest_birth[$i]?>">-->
				</td>    
				<td><input type='text' name='ges_age[]' class='inputtext1' value="<?=$_view2_guest_age?>"></td>
				<td><input type='text' name='ges_passport_num[]' class='inputtext1' value="<?=$_ary_guest_pass_num[$i]?>"></td>
				<td class="text-center"><input type='checkbox' name='ges_similan[]' class="checkSelect" value='Y' <?if($_ary_similan[$i] == 'Y'){echo "checked";}?>></td>
				<td><input type='text' name='ges_Id[]' class='inputtext1' value="<?=$_view2_guest_id?>"></td>
				<td class="text-center"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onClick="guestDel('mo_<?=$i?>')"> <i class="far fa-trash-alt"></i> </button></td>
			</tr>
			<? } ?>
		</table>
	</div>

	<div class="section-title2" id="S_detail4">
		<ul><h2>Hotel : ( <?=count($_ary_bkp_hotel)?> )</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_5')" id="section_contents_5_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>
	<div class="section-contents" id="section_contents_5" closeOpenState="open">
		<table class="table-reg th-center" >
			<tr>
				<th style="width:85px !important;">check in/out</th>
				<th style="width:auto !important;">hotel / room type / bed type</th>
				<th style="width:220px !important;">Option</th>
				<th style="width:100px !important;"><span data-toggle="tooltip" data-placement="top" title="Confirmation No">CN</span></th>
				<th style="width:50px !important;"><span data-toggle="tooltip" data-placement="top" title="Personnel adult / child">P a/c</span></th>
				<th style="width:50px !important;" ><span data-toggle="tooltip" data-placement="top" title="Room qty / Nights">R/N</span></th>
				<th style="width:90px !important;">Total Price</th>
				<!-- <th>Etc</th> -->
				<th style="width:40px !important;"></th>
			</tr>

			<?
				for( $i=0; $i<count($_ary_bkp_hotel); $i++ ){
					$hotel_num = $i+10;
					$_ary2_hotel_info = explode(":",$_ary_bkp_hotel[$i]);
					$_ary2_hotel_option = explode(",",$_ary_bkp_hot_option[$i]);
					$_ary2_schedule = explode("/",$_ary_schedule_day[$i]);
					$_ary2_bkp_hot_total_price = explode("/",$_ary_bkp_hot_total_price[$i]);
					

					$_show2_hotel_idx = $_ary2_hotel_info[0];
					$_show2_room_idx = $_ary2_hotel_info[2];
					$_view2_hotel_name = $_ary2_hotel_info[1];
					
					$_view_total_hotel_price += $_ary2_bkp_hot_total_price[2];

					$_view2_room_type = $_ary2_hotel_info[3];
					$_view2_hotel_check_in_day = $_ary_bkp_hot_check_in[$i];
					$_view2_hotel_check_out_day = $_ary_bkp_hot_check_out[$i];
					$_view2_hotel_bed_type = $_ary_bkp_hot_bed_type[$i];
					$_view2_hotel_stay_kr_text = $_ary2_schedule[0];
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
					
					$_check_in_count = strlen($_view2_hotel_check_in_day);

					if($_check_in_count == 8){
						$_view2_hotel_check_in_day = "20".$_view2_hotel_check_in_day;
						$_view2_hotel_check_out_day = "20".$_view2_hotel_check_out_day;
					}
			?>
				<tr id='trid_<?=$hotel_num?>'>
					<td class="text-center">
						<input type="text" name="bkp_hot_check_in[]"   onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)" maxlength='8' value="<?=$_view2_hotel_check_in_day?>" /><br>
						<input type="text" name="bkp_hot_check_out[]" class="m-t-5" onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)" maxlength='8' value="<?=$_view2_hotel_check_out_day?>" />
					</td>
					<td >
						<div>
							<select name='hotelN[]' id='hotelSe_mo_<?=$i?>' class='select1' onchange="doRoomChange('mo_<?=$i?>');" style="font-size:14px; font-weight:bold;">
								<option value='0:none'>Select Hotel</option>
							<?
							${'hotel_result_'.$i} = wepix_query_error($hotel_query);
							while( ${'hotel_list_'.$i} = wepix_fetch_array(${'hotel_result_'.$i})) {
							?>
								<option value='<?=${'hotel_list_'.$i}[HOT_IDX]?>:<?=${'hotel_list_'.$i}[HOT_NAME]?>' <? if( ${'hotel_list_'.$i}['HOT_IDX'] == $_show2_hotel_idx ) echo 'selected'; ?>><?=${'hotel_list_'.$i}[HOT_NAME]?><?=count($_ary_bkp_hotel)?></option>
							<? } ?>
							</select>
						</div>
						<div class="m-t-5">
							<select name='rtN[]' id='room_select_box_mo_<?=$i?>' class='select1'>
								<option value='0:none'>Select Room Type</option>
							<?
							$roomtype_query_mo = "select ROC_IDX,ROC_NAME,ROC_HOT_IDX from ".$db_t_ROOM_TYPE_DB." where ROC_VIEW = 'Y' and ROC_HOT_IDX = '".$_show2_hotel_idx."' order by ROC_NAME asc ";
							${'roomtype_'.$i} = wepix_query_error($roomtype_query_mo);
							while( ${'roomtype_list_'.$i} = wepix_fetch_array(${'roomtype_'.$i})) {
							?>
								<option value='<?=${'roomtype_list_'.$i}['ROC_IDX']?>:<?=${'roomtype_list_'.$i}['ROC_NAME']?>' <? if( ${'roomtype_list_'.$i}['ROC_IDX'] ==  $_show2_room_idx ) echo 'selected'; ?>><?=${'roomtype_list_'.$i}[ROC_NAME]?></option>

							<? } ?>
							</select>
						</div>
						<div class="m-t-5">
							<select name='bed_type[]'  class='select1' style='margin-top:5px;'>
								<option value='Double' <?if($_view2_hotel_bed_type == 'Double') echo "selected"; ?>>Double</option>
								<option value='DBL+EX' <?if($_view2_hotel_bed_type == 'DBL+EX') echo "selected"; ?>>DBL+EX </option>
								<option value='Double(S)' <?if($_view2_hotel_bed_type == 'Double(S)') echo "selected"; ?>>Double(S)</option>
								<option value='Twin+EX' <?if($_view2_hotel_bed_type == 'Twin+EX') echo "selected"; ?>>Twin+EX</option>
								<option value='Twin' <?if($_view2_hotel_bed_type == 'Twin') echo "selected"; ?>>Twin</option>
								<option value='Triple' <?if($_view2_hotel_bed_type == 'Triple') echo "selected"; ?>>Triple</option>
							</select>
							<input type='text' name='priceN[]' id='priceN_<?=$i?>' value='<?=$_ary2_schedule[1]?>' class='width-100' onkeyUP="sb_qt_plus(<?=$i?>);">
						</div>
						<div>
						</div>
						<div>
						</div>
					</td>
					<td >
						<div class="hotel-option-wrap">
							<?          
								 $_view2_allin_op = explode(",",$_ary_bkp_hot_option[$i]);
								 $_view2_allin_op_price = explode(",",$_ary_bkp_hot_option_price[$i]);
								 $hot_option_num = $i+2;
							?>
							<ul class="text-right"><input type='button' value='+ ADD OPTION' class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="hot_option_Plus('<?=$i?>')"></ul>
							<ul style="margin-top:4px !important;">
								<div id='hot_option_<?=$i?>'>
								<?
									for($hot_op=0;$hot_op<count($_view2_allin_op);$hot_op++){
								?>
									<ul class="text-right">
										<select name='hotel_chbox_<?=$i?>[]'>
											<?for( $ha=0; $ha<count($hotel_all_inclusive_array); $ha++ ){ ?>
											<option value='<?=$hotel_all_inclusive_array[$ha]?>' <?if($_view2_allin_op[$hot_op] == $hotel_all_inclusive_array[$ha]) echo "selected"; ?>><?=$hotel_all_inclusive_array[$ha]?></option>
											<?}?>
										</select>
										<input type='text' style='width:80px;' name='hotel_chbox_price_<?=$i?>[]'  id='hotel_chbox_price_<?=$i?>_<?=$hot_op?>' value='<?=$_view2_allin_op_price[$hot_op]?>' onChange="option_total_price('<?=$i?>',<?=$hot_op?>)">
									</ul>
								<? } ?>
								</div>
							</ul>
						</div>
					</td>
					<td >
						<input type='text' name='bkp_hotel_conf_num[]' value='<?=$_ary_bkp_hot_cf_num[$i]?>'>
						<button type="button" class="btnstyle1 <?=$show_hotel_sate_style?> btnstyle1-xs width-full m-t-5" onclick='chKind(<?=$i?>,<?=$_ary_bkp_hot_booking_state[$i]?>,<?=$bk_data[BKP_IDX]?>);'><i class="<?=$show_hotel_sate_icon?>"></i> <?=$_view2_hotel_reservation_state?></button>
					</td>
					<td>
						<input type='text' name='hot_head[]' value='<?=$_ary_bkp_head_count[$i]?>'>
						<input type='text' name='hot_head_c[]' class="m-t-5" value='<?=$_ary_bkp_head_count_c[$i]?>'>
					</td>
					<td class="text-center">
						<input type='text' name='hot_qty[]' id='hot_qty_<?=$i?>'  onkeyUP="sb_qt_plus(<?=$i?>);" value='<?=$_view2_room_count?>'>
						<input type='text' name='sdN[]' id='sbN_<?=$i?>' class="m-t-5" onkeyUP="sb_qt_plus(<?=$i?>);" value='<?=$_view2_hotel_stay_kr_text?>'>
					</td>
					<td class="text-center"> 
						<input type='text' name='hot_option_rate[]' id='hot_option_rate_<?=$i?>' class="m-t-5" placeholder="Total Option" value='<?=$_ary2_bkp_hot_total_price[0]?>'>
						<input type='text' name='hot_room_rate[]' id='price_total_<?=$i?>'  class="m-t-5" placeholder="Total Room" value='<?=$_ary2_bkp_hot_total_price[1]?>'>
						<input type='text' name='hot_total_price[]' id='hot_total_price_<?=$i?>' class="m-t-5" placeholder="Total Hotel " value='<?=$_ary2_bkp_hot_total_price[2]?>' readonly>
					</td>
					<td class="text-center"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="hotelDel('<?=$hotel_num?>');"> <i class="far fa-trash-alt"></i> </button></td>
				</tr>
				<? } ?>
		</table>
	</div>


	<?
		if($_view_bkp_type == 'GF' ){
	?>
	<div class="section-title2" id="S_detail10">
		<ul><h2>Golf</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_11')" id="section_contents_11_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>

	<div class="section-contents" id="section_contents_11" closeOpenState="open">
		<div>
			<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:180px; margin:5px; float:right;" onclick="optionAdd()"> <i class="fas fa-plus-circle"></i> Add golf </button>
		</div>
		<table class="table-reg th-center"  id="golfPt">
				<tr class="text-center ">
					<th style="width:40px !important;">Num</th>
					<th style="width:250px !important;">golf / date / time</th>
					<th class="tds1" style="width:40px; !important;"><span data-toggle="tooltip" data-placement="top" title="Personnel">P</span></th>
					<th style="width:80px !important;"><span data-toggle="tooltip" data-placement="top" title="Cart Usage fee">C.U.F</span></th>
					<th style="width:50px !important;"><span data-toggle="tooltip" data-placement="top" title="hole numbers">hole N</span></th>
					<th class="tds1">Add hole</th>
					<th class="tds1">Coupon Whether</th>
					<th class="tds1">caddie Fee</th>
					<th class="tds1">Total Price</th>
					<th style="width:40px !important;"></th>
				</tr>

				<?if($_view_bkp_type == 'GF' ){
					
					$go_result =wepix_query_error("select * from "._DB_BOOKING_GOLF." where BG_BKP_IDX = '".$_bkp_idx."'");
					$num=0;
					$num_ct = 0;
					while($go_list =  wepix_fetch_array($go_result)){
						$num_ct++;
				?>
				<tr style='text-align:center;' id='trid3_mo_<?=$num?>' >
					<input type='hidden' name='mo_golf[]' value='<?=$go_list[BG_IDX]?>'>
					<td class="tds2">No.<?=$num_ct?></td>
					<td class="tds2">
						<select name='mo_golf_name[]' >
							
				<?
				$golf_result = wepix_query_error($golf_query);
				while($golf_list = wepix_fetch_array($golf_result)) {
				?>
				<option value='<?=$golf_list[GF_IDX]?>' <?if($go_list[BG_NAME] == $golf_list[GF_NAME]) echo "selected"; ?>><?=$golf_list[GF_NAME]?></option>
				<? } ?>
						</select><br/>
						<input class="m-t-5" type="text" name = 'mo_t_up_date[]' style='width:90px;'  name="t_up_date[]" value="<?=$go_list[BG_ST_DATE]?>" onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)"  maxlength='8' value=''/>
						<input type='text' style='width:50px;' class="m-t-5" name = 'mo_t_up_time[]' value='<?=$go_list[BG_ST_TIME]?>'>
						<select name='mo_gf_am_pm[]' class="m-t-5">
							<option value='am' <?if($go_list[BG_TIME] == 'am') echo "selected"; ?>>AM</option>
							<option value='pm' <?if($go_list[BG_TIME] == 'pm') echo "selected"; ?>>PM</option>
						</select>
					</td>
					<td class="text-center"><input type='text' name = 'mo_gf_haed_ct[]' value='<?=$go_list[BG_HEAD_CT]?>'></td>
					 <td class="text-center">
						<select name='mo_gf_cart[]'>
							<option value='single' <?if($go_list[BG_CART] == 'single') echo "selected"; ?>>Single</option>
							<option value='double' <?if($go_list[BG_CART] == 'double') echo "selected"; ?>>Double</option>
						</select>
					</td>
					<td class="text-center">
						<select name='mo_holl_count[]'>
							<option value='9' <?if($go_list[BG_HOLL_CT] == '9') echo "selected"; ?>>9</option>
							<option value='18' <?if($go_list[BG_HOLL_CT] == '18') echo "selected"; ?>>18</option>
							<option value='36' <?if($go_list[BG_HOLL_CT] == '36') echo "selected"; ?>>36</option>
						</select>
					</td>
					<td class="text-center">
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_HOLL_ADD_YN] == '0') echo "checked"; ?> name='mo_add_holl_<?=$num?>' value='0'>없음</label>
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_HOLL_ADD_YN] == '9') echo "checked"; ?> name='mo_add_holl_<?=$num?>' value='9'>9홀</label>
						<label  class='p-r-5'><input type='radio' <?if($go_list[BG_HOLL_ADD_YN] == '18') echo "checked"; ?> name='mo_add_holl_<?=$num?>' value='18'>18홀</label>
					</td>
					<td class="text-center">
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_COUPON_YN] == 'N') echo "checked"; ?> name='mo_coupon_<?=$num?>' value='N'>미사용</label>
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_COUPON_YN] == '9') echo "checked"; ?> name='mo_coupon_<?=$num?>' value='9'>9홀</label>
						<br/>
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_COUPON_YN] == '18') echo "checked"; ?> name='mo_coupon_<?=$num?>' value='18'>18홀</label>
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_COUPON_YN] == 'Set') echo "checked"; ?> name='mo_coupon_<?=$num?>' value='Set'>Set</label>
					</td>
					<td class="text-center">
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_CADDIE_YN] == 'N') echo "checked"; ?> name='mo_caddie_yn_<?=$num?>' value='N'>미 사용</label>
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_CADDIE_YN] == 'Y') echo "checked"; ?> name='mo_caddie_yn_<?=$num?>' value='Y'>사용</label>
					</td>
					<td class="text-left">
						<input type='text' readonly value='<?=$go_list[BG_TOTAL_PRICE]?>'>
					</td>
					<td class="tds2"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3 " onClick='mo_golfDel("<?=$num?>","<?=$go_list[BG_IDX]?>")'><i class="far fa-trash-alt"></i></button>
					</td>                   
				</tr>
				<?
				++$num;
				}?>

				<?}?>
				</table>
	</div>
	<?}?>

	<div class="section-title2" id="S_detail5">
		<ul><h2>수배서 / 확정서</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_6')" id="section_contents_6_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>
	<div class="section-contents" id="section_contents_6" closeOpenState="open">

		<div class="section-title2-sub">수배서</div>
		<table class="table-reg th-150" >
			<tr>
				<th>booking letter</th>
				<td><input type="file" name="upload" id="upload" multiple /></th>
			</tr>
			<?
			$wented_num = 0;
			while($wented_list = wepix_fetch_array($wanted_result)){
				$wented_idx = $wented_list[WP_IDX];
				$wented_id = $wented_list[WP_REG_ID];
				$wented_date = date("d-M-y H:s",$wented_list[WP_REG_DATE]);
				$wented_num++;
				?>
			<tr>
				<th>booking letter <?=$wented_num?></th>
				<td>Registrant : <?=$wented_id?> / Registered Time : <?=$wented_date?>  <input type='button' value='수배서 보기' onclick="bookingWented('<?=$wented_idx?>');" ></td>
			</tr>
			<?}?>
		</table>

		<div class="section-title2-sub">확정서</div>
		<table class="table-reg th-150" >
			<tr>
				<th >확정서</th>
				<td ><input type="file" name="upload2" id="upload2" multiple /></th>
			</tr>
			<?
			$wented_num = 0;
			while($wented_list2 = wepix_fetch_array($wanted_result2)){
				$wented_idx = $wented_list2[WP_IDX];
				$wented_id = $wented_list2[WP_REG_ID];
				$wented_date = date("d-M-y H:s",$wented_list2[WP_REG_DATE]);
				$wented_num++;
				?>
			<tr>
				<th >확정서 <?=$wented_num?></th>
				<td >Registrant : <?=$wented_id?> / Registered Time : <?=$wented_date?>  <input type='button' value='확정서 보기' onclick="bookingWented('<?=$wented_idx?>');" ></td>
			</tr>
			<? } ?>
		</table>
	</div>

	<div class="section-title2" id="S_detail6">
		<ul><h2>Group</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_7')" id="section_contents_7_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>
	<div class="section-contents" id="section_contents_7" closeOpenState="open">
		<div class="section-title2-sub">Group Info</div>
		<table class="table-reg th-150">
			<tr>
				<th>Group Name</th>
				<td><?=$_view_bkg_name?></td>
				<th>Group Number</th>
				<td><?=$_view_bkg_idx?></td>
			</tr>
			<tr>
				<th>Group Team Count</th>
				<td><?=$_view_bkg_team_count?></td>
				<th>Group Personnel</th>
				<td><?=$_view_bkg_head_count?></td>
			</tr>
		</table>

		<div class="section-title2-sub">Group Team : (<?=$_view_bkg_team_count?>)</div>
        <table class="table-style">
			<tr>
				<th class="text-center this-team-num">팀번호</th>
				<th class="text-center this-team-name">Name</th>
				<th class="text-center">IN</th>
				<th class="text-center">OUT</th>
				<th class="text-center">Hotel</th>
				<th class="text-center">Basic TM / Total TM</th>
				<th class="text-center">Over TM / Receve TM</th>
				<th class="text-center">Info</th>
		   </tr>
<?
		for($i=0;$i<count($_ary_bkp_idx);$i++){
				$_ary2_bkp_hotel = explode("│",$_ary_bkg_hotel[$i]);
			    $_ary2_schedule_day = explode("│",$_ary_bkg_schedule_day[$i]); 
				$_ary_bkp_hotel_name = array();
			for($a=0;$a<count($_ary2_bkp_hotel);$a++){
				$_ary2_hotel_info = explode(":",$_ary2_bkp_hotel[$a]);
				$_ary2_schedule = explode("/",$_ary2_schedule_day[$a]);
				$_ary_bkp_hotel_name[] = $_ary2_hotel_info[1]." ( ".$_ary2_schedule[0]."N )";
			}
			 $_view2_hot_name = implode(",",$_ary_bkp_hotel_name);
			 if($_view_bkp_over_tm[$i] <= 0){
			   $_view2_bkp_over_tm = 0;
			   $_view2_bkp_charge_tm =0;
			 }else{
			   $_view2_bkp_over_tm = $_view_bkp_over_tm[$i];
			   $_view2_bkp_charge_tm = $_view_bkp_charge_tm[$i];
			 }

	if( $_bkp_idx == $_ary_bkp_idx[$i] ){
		$_tr_bgcolor = "#f7e3fb";
	}else{
		$_tr_bgcolor = "#ffffff";
	}
?>
<tr align="center" bgcolor="<?=$_tr_bgcolor?>">
	<td><?=$_ary_bkp_idx[$i]?></td>
	<td ><?=$_ary_bkp_team_name[$i]?></td>
	<td ><?=$_ary_bkp_start_date[$i]?> (<?=$_ary_bkp_start_flight[$i]?>)</td>
	<td ><?=$_ary_bkp_arrive_date[$i]?> (<?=$_ary_bkp_arrive_flight[$i]?>)</td>
	<td ><?=$_view2_hot_name?></td>
	<td ><?=$_ary_bkp_first_money[$i]?> (<?=$_view_bkp_use_tm[$i]?>)</td>
	<td ><?=$_view2_bkp_over_tm?> (<?=$_view2_bkp_charge_tm?>)</td>
	<td >
<?
		if( $_bkp_idx == $_ary_bkp_idx[$i] ){ }else{
?>
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="bookingModify2('<?=$_ary_bkp_idx[$i]?>')" >INFO</button>
<? } ?>
	</td>
</tr>
<?
	} //for END
?>
		</table>
	</div>

<?
	$schedule_count = wepix_counter2(_DB_SCHEDULE_TRAVEL, "where SC_BK_IDX = '".$_bkp_idx."' ","SC_DAY_NUM");
	$_view_schedule_count = $schedule_count;

	$schedule_result = wepix_query_error("select * from "._DB_SCHEDULE_TRAVEL." where SC_BK_IDX = '".$_bkp_idx."' order by SC_DAY_NUM asc");
?>
	<div class="section-title2" id="S_detail7">
		<ul><h2>일정 : (<?=$_view_schedule_count?>)</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_8')" id="section_contents_8_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>

<?
$_view_schedule_count_total_pd = 0;
$_view_schedule_count_total_bm = 0;
$_view_schedule_count_total_bm_add_sale = 0;
$_view_schedule_count_total_bm_discount = 0;
$_view_schedule_count_total_bm_sum = 0;

$_view_schedule_count_total_cost = 0;
//$_view_schedule_count_total_cost_add_sale = 0;
$_view_schedule_count_total_cost_discount = 0;
?>

	<div class="section-contents" id="section_contents_8" closeOpenState="open">
		<table class="table-reg" >
<?
$_view_schedule_list_count = 0;
while($schedule_list = wepix_fetch_array($schedule_result)){
	$_view_schedule_list_count++;
	$_view2_day_num = $schedule_list[SC_DAY_NUM];
    if( $schedule_list[SC_PUBLIC_YN] == 'Y' ){
		$_view2_schedule_active_text = '공개';
    }else{
		$_view2_schedule_active_text = '비공개';
    }
	$sc_bp_price_count = wepix_counter(_DB_BUY_PRODUCT_TRAVEL, " where BP_SC_DAY_NUM = '".$schedule_list[SC_DAY_NUM]."' and BP_BK_IDX = '".$_bkp_idx."' ");

	$bp_price_result = wepix_query_error("select * from "._DB_BUY_PRODUCT_TRAVEL." where BP_SC_DAY_NUM = '".$schedule_list[SC_DAY_NUM]."' and BP_BK_IDX = '".$_bkp_idx."'" );
    
               $_view2_bp_pirce_cost = 0;
				$_view2_bp_pirce =  0;
              

				//이후 수정해야함 -> while문 sum 19.05.09
                while($bp_price_list = wepix_fetch_array($bp_price_result)){
                    $_show2_bp_pd_saleprice = $bp_price_list[BP_PD_SALE_PRICE]; //상품 판매가격
                    $_show2_bp_qty =  $bp_price_list[BP_QTY]; //상품 수량
                    $_show2_bp_add_sale_price = $bp_price_list[BP_ADD_SALE_PRICE]; // 상품 추가가격
                    $_show2_bp_discount_price = $bp_price_list[BP_DISCOUNT_PRICE]; // 상품 할인가격
				
					$_view2_bp_pirce += $bp_price_list[BP_TOTAL_PRICE]; //상품 총액
					$_view2_bp_pirce_cost += $bp_price_list[BP_TOTAL_COST_PRICE]; //상품 원가 총액
					
				}
	$_view2_bp_count = $sc_bp_price_count;
	$_view2_bp_price = number_format($_view2_bp_pirce);
	$_view_schedule_count_total_bm += $_view2_bp_pirce;
?>
<tr>
	<td class="text-center" style="width:50px !important;"><?=$_view_schedule_list_count?></td>
	<td class="text-center" ><b><?=$_view2_day_num?></b></td>
	<td class="text-center" style="width:50px !important;"><?=$_view2_schedule_active_text?></td>
	<td class="text-center" style="width:50px !important;"><?=$sc_bp_price_count?><? $_view_schedule_count_total_pd += $sc_bp_price_count; ?></td>
	<td class="text-right" style="width:150px !important;"><?=$_view2_bp_price?> ฿</td>
	<td class="text-right" style="width:150px !important;"><?= number_format($_view2_bp_pirce_cost)?> ฿ <? $_view_schedule_count_total_cost += $_view2_bp_pirce_cost?></td>
	<td class="text-center" style="width:60px !important;"><button type="button" id="food_<?=$food_span?>_btn" class="btnstyle1 btnstyle1-primary btnstyle1-sm"  onclick="buyDetailView('food','<?=$food_span?>')">▼</button></td>
</tr>
<tr>
	<td colspan='7' class="folded-wrap-td">
		<table class="table-style border01 folded-table width-full">
<?
	$buy_pd_where = " where BP_SC_DAY_NUM = '".$_view2_day_num."' and BP_BK_IDX = '".$_bkp_idx."' ";
	$buy_pd_result = wepix_query_error("select * from "._DB_BUY_PRODUCT_TRAVEL." ".$buy_pd_where."
		order by 
		CASE BP_PD_KIND
			WHEN 'TOUR' THEN '1'
			WHEN 'FOOD' THEN '2'
			WHEN 'SPA' THEN '3'
			ELSE '9'
		END,
		BP_IDX asc");

$count = 0;
while($buy_pd_list = wepix_fetch_array($buy_pd_result)){
	$count++;

	if(substr($buy_pd_list[BP_PD_CATAGORY_ID],0,2)=="01"){ 
		$_view2_cate_name = "투어";
	}elseif(substr($buy_pd_list[BP_PD_CATAGORY_ID],0,2)=="02"){ 
		$_view2_cate_name = "식사";
	}elseif(substr($buy_pd_list[BP_PD_CATAGORY_ID],0,2)=="03"){
		$_view2_cate_name = "스파";
	}else{
		$_view2_cate_name = "기타";
	}

	$_ary2_bp_pd_optin_name = explode("|",$buy_pd_list[BP_PD_OPTION_NAME]);
	$_ary2_bp_pd_optin_price = explode("|",$buy_pd_list[BP_PD_OPTION_PRICE]);
	$_ary2_bp_pd_optin_price_cost = explode("|",$buy_pd_list[BP_PD_OPTION_COST_PRICE]);
	$_ary2_bp_pd_optin_child_price = explode("|",$buy_pd_list[BP_PD_OPTION_CHILD_SALE_PRICE]);
	$_ary2_bp_pd_optin_child_price_cost = explode("|",$buy_pd_list[BP_PD_OPTION_CHILD_SALE_COST_PRICE]);	
	$_ary2_bp_pd_qty = explode("|",$buy_pd_list[BP_QTY]);
	$_ary2_bp_pd_qty_c = explode("|",$buy_pd_list[BP_QTY_CHILD]);
	$_view_payment_kind = $buy_pd_list[BP_PAYMENT_KIND];

	$pd_data = wepix_fetch_array(wepix_query_error("select PD_OPTION from "._DB_PRODUCT_TRAVEL." where PD_IDX = '".$buy_pd_list[BP_PD_IDX]."' "));
	 //옵션부분 BP_PD_OPTION_NAME 텍스트 저장)
	//$_ary2_pd_option = explode("│",$pd_data[PD_OPTION]);
	//$_view2_buy_pd_option = $_ary2_pd_option[$buy_pd_list[BP_PD_OPTION]];
	$_view2_buy_pd_reservation_date = date("y.m.d H:i", $buy_pd_list[BP_RESERVATION_DATE]);
?>
<tr>
	<td class="text-center" style="width:40px !important;"><?=$count?></td>
	<td class="text-center" style="width:50px !important;"><?=$_view2_cate_name?></td>
	<td>
		<div><b style="font-size:14px;"><?=$buy_pd_list[BP_PD_NAME]?></b></div>
		<div style="margin-top:5px;">
			<?if($_ary2_bp_pd_optin_name[0] != ''){?>
				<?for($i=0;$i<count($_ary2_bp_pd_optin_name);$i++){?>
				<div class="pn-pd-option">
					<?=$_ary2_bp_pd_optin_name[$i]?><br/>
				</div>
					 <?if($_ary2_bp_pd_optin_price[$i] == $_ary2_bp_pd_optin_child_price[$i]){?>
						<span style='color:#FFA07A; font-size:12px;'>฿ <?=number_format($_ary2_bp_pd_optin_price[$i])?>   </span>
						<span style='font-size:11px;'>( ฿ <?=number_format($_ary2_bp_pd_optin_price_cost[$i])?> ) x <?=$_ary2_bp_pd_qty[$i] + $_ary2_bp_pd_qty_c[$i]?></span></br>
					 <?}else{?>
						<span style='color:#FFA07A; font-size:12px;'>Ad ฿<?=number_format($_ary2_bp_pd_optin_price[$i])?></span>
						<span style='font-size:11px;'>( ฿ <?=number_format($_ary2_bp_pd_optin_price_cost[$i])?> ) x <?=$_ary2_bp_pd_qty[$i]?></span></br>
						<span style='color:#6B8E23; font-size:12px;'>Ch ฿<?=number_format($_ary2_bp_pd_optin_child_price[$i])?></span>
						<span style='font-size:11px;'>( ฿ <?=number_format($_ary2_bp_pd_optin_child_price_cost[$i])?> ) x <?=$_ary2_bp_pd_qty_c[$i]?></span></br>
					 <?}?>
				<?}?>
			<?}?>
		</div>
<?
if( $buy_pd_list[BP_PD_SALE_PRICE] > 0 ){
?>
		<div class="pn-pd-price">
<?
if($buy_pd_list[BP_PD_OPTION_NAME] == ''){?>
		 <?if($buy_pd_list[BP_PD_SALE_PRICE] == $buy_pd_list[BP_PD_CHILD_SALE_PRICE]){?>
			<span> ฿ <?=number_format($buy_pd_list[BP_PD_SALE_PRICE])?></span>  x <b><?=$buy_pd_list[BP_QTY] + $buy_pd_list[BP_QTY_CHILD]?></b>  <br/>
		 <?}else{?>
			Ad <span> ฿ <?=number_format($buy_pd_list[BP_PD_SALE_PRICE])?></span>  x <b><?=$buy_pd_list[BP_QTY]?></b>  <br/>
			Ch <span> ฿ <?=number_format($buy_pd_list[BP_PD_CHILD_SALE_PRICE])?></span>  x <b><?=$buy_pd_list[BP_QTY_CHILD]?></b>
		 <?}?>
<?}?>
		</div>
<? } ?>

	</td>
	<td style="width:200px !important;">
<?
if( $buy_pd_list[BP_ADD_SALE_PRICE] > 0 ){
	$_view_schedule_count_total_bm_add_sale += $buy_pd_list[BP_ADD_SALE_PRICE];
?>
			<div class="pn-pd-add-price-wrap">
				<ul class="price">추가금액 : <b>+ ฿ <?=number_format($buy_pd_list[BP_ADD_SALE_PRICE])?> ( ฿ <?=number_format($buy_pd_list[BP_ADD_COST_PRICE])?>)</b> </ul>
				<ul class="memo"><?=$buy_pd_list[BP_ADD_MEMO]?></ul>
			</div>
<? } ?>

<?
if( $buy_pd_list[BP_DISCOUNT_PRICE] > 0 ){
	$_view_schedule_count_total_bm_discount += $buy_pd_list[BP_DISCOUNT_PRICE];
?>
			<div class="pn-pd-discount-price-wrap">
				<ul class="price">할인금액(판매가) : <b>- ฿ <?=number_format($buy_pd_list[BP_DISCOUNT_PRICE])?></b></ul>
				<ul class="memo"><?=$buy_pd_list[BP_DISCOUNT_MEMO]?></ul>
			</div>
<? } ?>

<?
if( $buy_pd_list[BP_DISCOUNT_COST_PRICE] > 0 ){
	$_view_schedule_count_total_cost_discount += $buy_pd_list[BP_DISCOUNT_COST_PRICE];
?>
			<div class="pn-pd-discount-price-wrap">
				<ul class="price">할인금액(원가) : <b>- ฿ <?=number_format($buy_pd_list[BP_DISCOUNT_COST_PRICE])?></b></ul>
				<ul class="memo"><?=$buy_pd_list[BP_DISCOUNT_COST_MEMO]?></ul>
			</div>
<? } ?>

	</td>
	<td style="width:200px !important;">
<?
		$sale_price = $buy_pd_list[BP_TOTAL_PRICE];
		$sale_price_cost = $buy_pd_list[BP_TOTAL_COST_PRICE];

		$_view_schedule_count_total_bm_sum += $sale_price;
?>
		<div class="pn-pd-sale-price-wrap">
			<ul class="price">결제수단 : <b> <?=$_view_payment_kind?> </b></ul>
			<ul class="price">합계금액 : <b>฿ <?=number_format($sale_price)?> </b> (฿ <?=number_format($sale_price_cost)?>)</ul>
		</div>
	</td>
	<td style="width:110px !important;">
		<div>
			<button type="button" id="bp_reservation_active_y" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="bpReservation('<?=$buy_pd_list[BP_IDX]?>');" style="<? if( $buy_pd_list[BP_RESERVATION_YN] == "Y" ) echo "display:none;"; ?>"> <i class="far fa-calendar"></i> 예약처리</button>
			<button type="button" id="bp_reservation_active_n" class="btnstyle1 btn-default btnstyle1-sm" style="color:#ff0000; <? if( $buy_pd_list[BP_RESERVATION_YN] == "N" ) echo "display:none;"; ?>"> <i class="far fa-calendar-check"></i> 예약완료</button>
		</div>
		<div class="m-t-3">
			<span class="bp-reservation-active-date" id="bp_reservation_active_date" style="<? if( $buy_pd_list[BP_RESERVATION_YN] == "N" ) echo "display:none;"; ?>"><?=$_view2_buy_pd_reservation_date?></span>
		</div>
	</td>
	<td style="width:100px !important;">
	</td>
</tr>
<? } ?>
		</table>
	</td>
</tr>
<? } ?>
		</table>
		<div class="text-right m-t-5" >
			<ul>상품 : <?=$_view_schedule_count_total_pd?></ul>
			<ul>
				사용 T.M : ฿ <?=number_format($_view_schedule_count_total_bm + $_view_schedule_count_total_bm_discount - $_view_schedule_count_total_bm_add_sale)?> /
				할인 T.M : ฿ <?=number_format($_view_schedule_count_total_bm_discount)?> /
				추가 T.M : ฿ <?=number_format($_view_schedule_count_total_bm_add_sale)?> /
				<b style="font-size:14px;">합계 T.M : ฿ <?=number_format($_view_schedule_count_total_bm_sum)?></b>
			</ul>
			<ul>
				사용 원가 : ฿ <?=number_format($_view_schedule_count_total_cost + $_view_schedule_count_total_cost_discount)?> /
				할인 원가 : ฿ <?=number_format($_view_schedule_count_total_cost_discount)?> /
				<b style="font-size:14px;">합계 원가 : ฿ <?=number_format($_view_schedule_count_total_cost)?></b>
			</ul>
		</div>
	</div>

	<div class="section-title2" id="S_detail8">
		<ul><h2>청구서</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_9')" id="section_contents_9_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>
	<div class="section-contents" id="section_contents_9" closeOpenState="open">
<?
	$buy_pd_sum = wepix_fetch_array(wepix_query_error("select sum(BP_TOTAL_PRICE) as total_price from "._DB_BUY_PRODUCT_TRAVEL." WHERE BP_BK_IDX = '".$_bkp_idx."' "));
	$_show_total_buy_pd = $buy_pd_sum[total_price];

	$_view_bkp_use_tm = number_format($_show_total_buy_pd);
	$_view_bkp_over_tm = number_format($_show_total_buy_pd - $bk_data[BKP_FIRST_MONEY]); //총 추가 T.M

	$_view_bkp_discount_rate = $bk_data[BKP_DISCOUNT_RATE];
	$_show_bkp_charge_tm = (($_show_total_buy_pd-$bk_data[BKP_FIRST_MONEY]) * (100 - $bk_data[BKP_DISCOUNT_RATE]) / 100);
	$_view_bkp_charge_tm = number_format($_show_bkp_charge_tm);

	//청구서 정보
	$bill_p_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BILL_PARENT_TRAVEL." where PR_BKP_IDX ='".$_bkp_idx."' "));
	$_view_bill_p_total_price = number_format($bill_p_data[PR_TOTAL_PRICE]);

	$_view_bill_p_state = $bill_p_data[PR_STATE];
	$_view_bill_p_r_name = $bill_p_data[PR_R_NAME];
	$_view_bill_p_r_date = $bill_p_data[PR_R_DATE];
	$_view_bill_p_r_price = number_format($bill_p_data[PR_R_PRICE]);
	$_view_bill_p_other_kind = $bill_p_data[PR_OTHER_KIND];
	$_view_bill_p_other_price = number_format($bill_p_data[PR_OTHER_PRICE]);
	$_view_bill_p_other_memo = $bill_p_data[PR_OTHER_MEMO];

	$_view_bill_p_confirm_id = $bill_p_data[PR_CONFIRM_ID];
	$_view_bill_p_confirm_date = date('y.m.d H:i', $bill_p_data[PR_CONFIRM_DATE]);

	$bill_result = wepix_query_error("select * from "._DB_BILL_TRAVEL." where PU_BKP_IDX = '".$_bkp_idx."' order by PU_IDX desc");

	$exchange_rate['D'] = "달러";
	$exchange_rate['W'] = "원화";
	$exchange_rate['G'] = "송금";
	$exchange_rate['B'] = "바트";

	$exchange_sb['D'] = "$";
	$exchange_sb['W'] = '원';
	$exchange_sb['G'] = '원';
	$exchange_sb['B'] = "฿";
?>
		<table class="table-reg th-150" >
			<tr>
				<th>지급 T.M</th>
				<td><?=number_format($bk_data[BKP_FIRST_MONEY])?></td>
				<th>총 사용 T.M</th>
				<td><?=$_view_bkp_use_tm?></td>
			</tr>
			<tr>
				<th>초과 T.M</th>
				<td><?=$_view_bkp_over_tm?></td>
				<th>할인</th>
				<td><b><?=$_view_bkp_discount_rate?></b> %</td>
			</tr>
			<tr>
				<th>예상 청구금</th>
				<td><?=$_view_bkp_charge_tm?></td>
				<th>실 청구금</th>
				<td><b><?=$_view_bill_p_total_price?></b> %</td>
			</tr>
<?
	$bill_state_text['A'] = "청구서 발행";
	$bill_state_text['B'] = "청구서 재발행 요청";
	$bill_state_text['C'] = "청구서 재발행";
	$bill_state_text['D'] = "청구서 확인 완료";


?>
			<tr>
				<th>청구서 상태</th>
				<td colspan="3">
						<?=$bill_state_text[$_view_bill_p_state]?>
						<? if( $_view_bill_p_state == "D" ){ ?>
							<br><?=$_view_bill_p_confirm_date?> ( <?=$_view_bill_p_confirm_id?> )
						<? } ?>
				</td>
			</tr>
		</table>
		<table class="table-reg th-center m-t-5" >
			<tr>
				<th style="width:80px !important;">IDX</th>
				<th style="width:80px !important;">종류</th>
				<th style="width:80px !important;">환율</th>
				<th style="width:100px !important;">입금</th>
				<th style="width:100px !important;">환율 적용금</th>
				<th>처리</th>
			</tr>
<?
$bill_count = 0;
while($bill_list = wepix_fetch_array($bill_result)){
	$bill_count++;
	$_view2_bill_kind = $bill_list[PU_KIND];
	$_view2_bill_exchange_rate = $bill_list[PU_EXCHANGE_RATE];
	$_view2_bill_defult_money = number_format($bill_list[PU_DEFULT_MONEY]);
	$_view2_bill_money = number_format($bill_list[PU_MONEY]);
	$_view2_bill_total_money += $bill_list[PU_MONEY];

	$_view2_bill_cf_yn = $bill_list[PU_ADMIN_CONFIRM];
	$_view2_bill_cf_yn_id = $bill_list[PU_ADMIN_CONFIRM_ID];
	$_view2_bill_cf_yn_date = date("d-M-y H:i",$bill_list[PU_ADMIN_CONFIRM_DATE]);
?>
			<tr>
				<td class="text-center"><?=$bill_list[PU_IDX]?></td>
				<td class="text-center"><?=$exchange_rate[$_view2_bill_kind]?></td>
				<td class="text-center"><?=$_view2_bill_exchange_rate*1?></td>
				<td class="text-right"><b><?=$_view2_bill_defult_money?></b> <?=$exchange_sb[$_view2_bill_kind]?></td>
				<td class="text-right"><b><?=$_view2_bill_money?></b> ฿</td>
				<td>
		<?if($_view2_bill_cf_yn == 'N'){?>
			<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs m-t-3 width-50" onclick="pu_ok('<?=$bill_list[PU_IDX]?>','bill_part_cf','<?=$_bkg_idx?>')" >
			<i class="far fa-check-square"></i> C/F</button>
		<?}else{?>
			완료 - <?=$_view2_bill_cf_yn_id?> (<?=$_view2_bill_cf_yn_date?>)
		<?}?>
				</td>
			</tr>
<? } ?>
		</table>
	</div>

	<div class="section-title2" id="S_detail9">
		<ul><h2>메모</h2></ul>
		<ul class="text-right"><button type="button" class="btnstyle1 btnstyle1-inverse2 btnstyle1-xs close-btn" onclick="closeOpen('section_contents_10')" id="section_contents_10_closeOpen_btn"><i class="fas fa-angle-down"></i> Close</button></ul>
	</div>
	<div class="section-contents" id="section_contents_10" closeOpenState="open">
		<table class="table-reg" >
			<tr>
				<th >Memo - Admin</th>
				<th >Forwarding details - Guide</th>
			</tr>
			<?
                $bkp_memo_admin = str_replace('<br />','',$_view_admin_memo);
				$_view2_admin_memo = str_replace('옵션 : none ->','',$bkp_memo_admin);
                $_view2_admin_memo = str_replace('옵션 :  -> none','',$bkp_memo_admin);
			?>
			<tr>
				<td >
					<textarea name="bkp_memo_admin" class="textarea1"><?=$_view2_admin_memo?></textarea>
				</td>
				<td >
					<textarea name="bkp_memo" class="textarea1"><?=$_view_memo?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<textarea class="textarea1" readonly> <?=str_replace('<br />','',$_view_mod_log)?></textarea>
				</td>
			</tr>
		</table>
	</div>
	</form>
	

</div>	

<div style="height:200px"></div>

<script type="text/javascript"> 
<!-- 
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

$(document).ready(function(){
    var findFixNav = $('.bvFixNav');
    var propFixNavHeight = $('.bvFixNav').outerHeight();
	var propFixNavTop = findFixNav.position().top;
	//var propFixNavTop2 = $('.fixed-menu').position().top;

    var lastId,
        topMenu = $(".booking-view-tap"),
        menuItems = topMenu.find("ul a"),
        scrollItems = menuItems.map(function(){
            var item = $($(this).attr("target"));
            if (item.length) { return item; }
        });

    $(document).scroll(function(){
        var fromTop = $(document).scrollTop()+propFixNavTop + 60 ;
        var cur = scrollItems.map(function(){
            if ($(this).offset().top < fromTop)
                return this;
        });
        cur = cur[cur.length-1];
        var id = cur && cur.length ? cur[0].id : "";

        if (lastId !== id) {
            lastId = id;
            menuItems
                .parent().removeClass("active")
                .end().filter("[target=#"+id+"]").parent().addClass("active");
        }
	});

    $('.booking-view-tap ul a').click(function(e){
		$(this).parent().addClass('active').siblings().removeClass('active');
		var propTargetTop = $($(this).attr('target')).offset().top - 59;
		//alert(propTargetTop);
        //$('html,body').stop().animate({scrollTop: propTargetTop}, 300);
		$('html,body').scrollTop(propTargetTop);
	});
});

function closeOpen(id){
	var closeOpenState = $("#"+id).attr("closeOpenState");
	if( closeOpenState == "open" ){
		$("#"+id).hide();
		$("#"+id+"_closeOpen_btn").html('<i class="fas fa-angle-up"></i> Open');
		$("#"+id).attr("closeOpenState","close");
	}else{
		$("#"+id).show();
		$("#"+id+"_closeOpen_btn").html('<i class="fas fa-angle-down"></i> Close');
		$("#"+id).attr("closeOpenState","open");
	}
}

function agency_cf(key,state){
	
	$.ajax({
		type : "POST",
		url : "<?=_A_PATH_BOOKING_OK?>",
		data : {a_mode:"agency_cf",state:state ,key:key,submit_mode:"bkt-list"},
		error : function(){
			
		},
		success : function(data){
			location.reload();
		}
	});

}

function chKind(num,value,mokey){

	$.ajax({
		url: "<?=_A_PATH_BOOKING_OK?>",
		data: {
			"a_mode":"htkindCh_list",
			"mokey":mokey,
			"kind":value,
            "num":num,
			"submit_mode":"bkt-list"
		},
		type: "POST",
		dataType: "text",
		success: function(data){
            location.reload();
		},
		error: function(){
			//에러
		}
	});
			
}
//--> 
</script>

<?
include "../layout/footer_popup.php";
exit;
?>