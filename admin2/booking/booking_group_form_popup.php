<?
include "../lib/inc_common.php";

	$_mode = $_GET['mode'];
	$_num = $_GET['array'];

	$_bkp_idx = explode(",",$_num);
	count($_bkp_idx);

// ************************************************************************************************************************************************
// 부킹그룹 신규 생성
// ************************************************************************************************************************************************
	if( $_mode == "new" ){
		$title_text = "Creating a Booking Group";


		$redate = wepix_fetch_array(wepix_query_error("select 
		MIN(BKP_START_DATE) as valmin,
		MAX(BKP_ARRIVE_DATE) as valmax from "._DB_BOOKING_PARENT." WHERE BKP_IDX in (".$_num.") " ));

		$start_date =$redate[valmin];
        $end_date  = $redate[valmax];
        //$new_end_date = 

		$query = "select * from "._DB_BOOKING_PARENT." WHERE BKP_IDX in (".$_num.") order by BKP_IDX desc";
		$result = wepix_query_error($query);
		


		$total_guest_instant_count = 0;

		while($list = wepix_fetch_array($result)){
			$bkp_guest_instant = explode("│",$list[BKP_GUEST]);
			$total_guest_instant_count += count($bkp_guest_instant);
		}

// ************************************************************************************************************************************************
// 부킹그룹 팀 추가
// ************************************************************************************************************************************************
	}elseif( $_mode == "add" ){
		$title_text = "Add team";

		$bg_query = "select * from "._DB_BOOKING_GROUP." where BKG_STATE = 'N' and BKG_START_DATE > 1568732399 order by BKG_START_DATE desc";
		$bg_result = wepix_query_error($bg_query);
	}


include "../layout/header_popup.php";
?>
<script type="text/javascript"> 
<!-- 
	function doSubmit(){
        
		var form1 = document.formN1;
		if( $("#bkp_guide_id").val() == "" ){
			alert("가이드 선택해주세요.");
			
			$("#bkp_guide_id").focus();
			return false;
		}
        
		if( form1.bkg_name.value == "" ){
        
			var st_dt = form1.bkg_start_date.value;
			var ed_dt = form1.bkg_end_date.value;
        
            var sub_st_dt =  st_dt.substring(5,10);
            var replace_st_dt = sub_st_dt.replace("-", "/");
            var gd_nick =  $("#bkp_guide_id option:selected").text();
        
			form1.bkg_name.value = "[" + gd_nick + "] " + replace_st_dt +"주";
        
		}

		form1.submit();
	}
//ㄷㄹ력
 $(document).ready(function(){   
		  $( "#bkg_start_date").datepicker({
			dateFormat: 'yy-mm-dd'
		  });
		  $( "#bkg_end_date").datepicker({
			dateFormat: 'yy-mm-dd'
		  });
});
</script>
<STYLE TYPE="text/css">
.table-style { width:100%; }
</STYLE>


<div id="popup_contents_head">
	<h1><?=$title_text?></h1>
</div>

<div id="popup_contents_body" style="padding:10px;">
	<div class="table-wrap">
<?
// ************************************************************************************************************************************************
// 부킹 신규 그룹 생성
// ************************************************************************************************************************************************
	if( $_mode == "new" ){
?>
		<form cellspacing="1" cellpadding="0" action="/admin2/booking/booking_ok.php" method="post" name="formN1">
		<input type="hidden" name="a_mode" value="newBookingGroup">
		<input type="hidden" name="bkp_idx" value="<?=$_num?>">
		<input type="hidden" name="bkg_bkp_count" value="<?=count($_bkp_idx)?>">
		<input type="hidden" name="bkg_head_count" value="<?=$total_guest_instant_count?>">

		<table cellspacing="1" cellpadding="0" class="table-style">
			<tr>
				<th class="tds1">그룹명 <?=$_num?></th>
				<td class="tds2" colspan="3">
					<input type='text' name='bkg_name' id='bkg_name' style="width:250px;">
					<div class="explain">
						그룹명을 선택하지 않을경우<br>
						"[가이드 닉네임] (MM/DD)" 주 자동 지정됩니다.
					</div>
				</td>
			</tr>
            <tr>
				<th class="tds1">그룹 타입</th>
				<td class="tds2" colspan="3">
                        <select name="bkp_type" id="bkp_type" class="select1">
                        <option value="">그룹타입 선택</option>
						<?
						$area_query = "select * from ".$db_t_BOOKING_SETTING." where BKS_KIND = 'B' order by BKS_IDX asc";
						$area_result = wepix_query_error($area_query);
						while($area_list = wepix_fetch_array($area_result)){
						?>
							<option value="<?=$area_list[BKS_VALUE]?>"  <? if( $data[BKP_TYPE] == $area_list[BKS_VALUE]  ) echo "selected"; ?> ><?=$area_list[BKS_NAME]?></option>
						<? } ?>
						</select>
				</td>
			</tr>
			<tr>
				<th class="tds1">Tour Fee</th>
				<td class="tds2" colspan="3" ><input type='text' style='width:150px;' name='bkp_tour_fee'></td>
			</tr>
			<tr>
				<th class="tds1">가이드 ID </th>
				<td class="tds2" colspan="3">
					<select name="bkp_guide_id" id="bkp_guide_id" class="select1">
							<option value="">가이드 선택</option>
						<?
						$gd_query = "select * from GUIDE_MEMBER where GD_STATE = '1' order by GD_IDX desc";
						$gd_result = wepix_query_error($gd_query);
						while($gd_list = wepix_fetch_array($gd_result)){
						?>
							<option value="<?=$gd_list[GD_ID]?>"  <? if( $data[BKP_GUIDE_ID ]== $gd_list[GD_ID]  ) echo "selected"; ?> ><?=$gd_list[GD_NICK]?></option>
						<? } ?>
					</select>
					<div class="explain">
						가이드를 지정할경우 이미 가이드가 지정되어 있더라도 새롭게 지정됩니다.
					</div>
				</td>
			</tr>
			<?
                                $bkp_start_date = date("Y-m-d", $start_date);
                                $title_st_dt = date("m/d", $start_date);
								$bkp_arrive_date =  date("Y-m-d", strtotime($bkp_start_date." +6 days"));
			?>
			<tr>
				<th class="tds1">시작 날짜</th>
				<td class="tds2" >
					<img src="/admin/img/calendar.png" align="absmiddle" style="margin:1px 2px 0 0;" alt="팀 시작 날짜" title="팀 시작 날짜" /> 
					<input type="text" id="bkg_start_date" name="bkg_start_date" value="<?=$bkp_start_date?>" style="width:80px; cursor:pointer;" readonly />
                    
				</td>
				<th class="tds1">종료 날짜 </th>
				<td class="tds2">
					<img src="/admin/img/calendar.png" align="absmiddle" style="margin:1px 2px 0 0;" alt="팀 끝 날짜" title="팀 끝 날짜" /> 
				    <input type="text" id="bkg_end_date" name="bkg_end_date" value="<?=$bkp_arrive_date?>" style="width:80px; cursor:pointer;" readonly />
				</td>
			</tr>
			<tr>
				<th class="tds1">부킹수</th>
				<td class="tds2" ><?=count($_bkp_idx)?></td>
				<th class="tds1">선택된 부킹 고유번호 </th>
				<td class="tds2"><?=$_num?></td>
			</tr>
			<tr>
				<th class="tds1">인원수</th>
				<td class="tds2" ><?=$total_guest_instant_count?></td>
				<th class="tds1"></th>
				<td class="tds2"></td>
			</tr>

		</table>
		</form>

<?
// ************************************************************************************************************************************************
// 부킹 지정
// ************************************************************************************************************************************************
	}elseif( $_mode == "add" ){
?>
		<form cellspacing="1" cellpadding="0" action="/admin2/booking/booking_ok.php"  method="post" name="formN1">
		<input type="hidden" name="a_mode" value="modifyBookingGroup"> 
		<input type="hidden" name="bkp_idx" value="<?=$_num?>">
        <input type='hidden' name='bkg_name' id='bkg_name' value="">
        <input type="hidden" id="bkg_start_date" name="bkg_start_date" value="<?=$bkp_start_date?>" />
        <input type="hidden" id="bkg_end_date" name="bkg_end_date" value="<?=$bkp_start_date?>"/>
        <?
                                $bkp_start_date = date("Y-m-d", $start_date);
                                $title_st_dt = date("m/d", $start_date);
								$bkp_arrive_date =  date("Y-m-d", strtotime($bkp_start_date." +6 days"));
			?>
		<table cellspacing="1" cellpadding="0" class="table-style">
			<tr>
				<th class="tds1">그룹명 <?=$_num?></th>
				<td class="tds2" colspan="3">
					<select name="bkg_idx">
						<?
							while($bg_list = wepix_fetch_array($bg_result)){
						?>
						<option value="<?=$bg_list[BKG_IDX]?>"><?=$bg_list[BKG_NAME]?></option>
						<? } ?>
					</select>
				</td>
			</tr>
		</table>
		</form>
<? } ?>
	</div>
	<div class="submitBtnWrap"><input type="button" value="그룹지정 완료" onclick="doSubmit();" class="submitBtn"></div>
</div>


<?
include "../layout/footer_popup.php";
exit;
?>