<?
include "../lib/inc_common.php";
$pageGroup = "booking";
$pageName = "booking_group_view";

	$_mode = $_GET['mode'];
	$_num = $_GET['array'];

	$query = "select * from ".$db_t_BOOKING_GROUP." WHERE BKG_IDX = '".$idx."'";
	$data = wepix_fetch_array(wepix_query_error($query));
	$bkp_idx_array = explode(",",$data[BKG_BKP_IDX]);

	$page_link_text = "?list=save";
	if($s_day){
		$page_link_text .= "&s_day=".$s_day;
	}
	if($e_day){
		$page_link_text .= "&e_day=".$e_day;
	}
	if($search_type){
		$page_link_text .= "&search_type=".$search_type;
	}
	if($search_guide){
		$page_link_text .= "&search_guide=".$search_guide;
	}


include "../layout/header.php";
?>

<STYLE TYPE="text/css">
.table-style { width:100%; }
</STYLE>
<script type="text/javascript"> 
function autoHypendate(e, oThis ){
    var num_arr = [ 
            97, 98, 99, 100, 101, 102, 103, 104, 105, 96,
            48, 49, 50, 51, 52, 53, 54, 55, 56, 57
        ]
        var key_code = ( e.which ) ? e.which : e.keyCode;
        if( num_arr.indexOf( Number( key_code ) ) != -1 ){
            var len = oThis.value.length;
            if( len == 2 ) oThis.value += "-";
            if( len == 5 ) oThis.value += "-";
        }
}

function addTourFee(key){
	var add_value = $("#add_tour_fee").val();

	$.ajax({
		type : "POST",
		url : "<?=_A_PATH_BOOKING_OK?>",
		data : {
			a_mode:"groupTourfeeAdd" , 
			bkg_idx:key,
			add_value:add_value
			},
		error : function(a,b,c){
			
		},
		success : function(data){
			//alert(data);
			location.reload();
		}
	});


}
function bookingModify(key){
	var form = document.formN1;

	form.submit();
}
</script>

<div id="popup_contents_head">
	<h1>Booking Group Modify</h1>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">
	<div class="table-wrap">

		<form cellspacing="1" cellpadding="0" action="<?=_A_PATH_BOOKING_OK?>" method="post" name="formN1">
        <input type='hidden' name='action_mode' value='group_information_modify'>
        <input type='hidden' name='mokey' value='<?=$idx?>'>
		<table cellspacing="1" cellpadding="0" class="table-style">
			<tr>
				<th class="tds1">그룹명</th>
				<td class="tds2" colspan="3" style="width:85px;">
					<input type='text' name='bkg_name' id='bkg_name' style="width:100%;" value='<?=$data[BKG_NAME]?>'>
				</td>
			</tr>
            <tr>
				<th class="tds1">그룹 타입</th>
				<td class="tds2" colspan="3" style="width:85px;">
                        <select name="bkp_type" id="bkp_type" class="select1">
                        <option value="">그룹타입 선택</option>
						<?
						$area_query = "select * from ".$db_t_BOOKING_SETTING." where BKS_KIND = 'B' order by BKS_IDX asc";
						$area_result = wepix_query_error($area_query);
						while($area_list = wepix_fetch_array($area_result)){
						?>
							<option value="<?=$area_list[BKS_VALUE]?>"  <? if( $data[BKG_TYPE] == $area_list[BKS_VALUE]  ) echo "selected"; ?> ><?=$area_list[BKS_NAME]?></option>
						<? } ?>
						</select>
				</td>
			</tr>
			<tr>
				<th class="tds1">가이드 ID </th>
				<td class="tds2" colspan="3" style="width:85px;">
				<?=$data[BKG_GID_ID]?>
				<!--
					<select name="bkp_guide_id" id="bkp_guide_id" class="select1">
							<option value="">가이드 선택</option>
						<?
						$gd_query = "select * from GUIDE_MEMBER";
						$gd_result = wepix_query_error($gd_query);
						while($gd_list = wepix_fetch_array($gd_result)){
						?>
							<option value="<?=$gd_list[GD_ID]?>"  <? if( $data[BKG_GID_ID]== $gd_list[GD_ID]  ) echo "selected"; ?> ><?=$gd_list[GD_NICK]?></option>
						<? } ?>
					</select>
					<div class="explain">
						가이드를 지정할경우 이미 가이드가 지정되어 있더라도 새롭게 지정됩니다.
					</div>
				-->
				</td>
			</tr>
			<tr>
				<th class="tds1">Tour Fee</th>
				<td class="tds2"><?=$data[BKG_TOTAL_TOUR_FEE]?>  
				<input type='text' style='width:150px;' name='add_tour_fee' id='add_tour_fee' value=''> 
				<input type='button' style='width:150px;' onclick="addTourFee('<?=$data[BKG_IDX]?>');" value='투어피 추가'></td>
			</tr>
			<?
                                $bkp_start_date = date("y-m-d", $data[BKG_START_DATE]);
                                $bkp_arrive_date =  date("y-m-d",$data[BKG_END_DATE]);
			?>
			<tr>
				<th class="tds1">시작 날짜</th>
				<td class="tds2" >
					<input type="text" id="bkg_start_date" name="bkg_start_date" value="<?=$bkp_start_date?>" style="width:80px; cursor:pointer;"  onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)" maxlength="8" />
                </td>
				<th class="tds1">종료 날짜 </th>
				<td class="tds2">
				    <input type="text" id="bkg_end_date" name="bkg_end_date" value="<?=$bkp_arrive_date?>" style="width:80px; cursor:pointer;" onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)" maxlength="8"  />
				</td>
			</tr>
			<tr>
				<th class="tds1">부킹수</th>
				<td class="tds2" ><?=count($bkp_idx_array)?></td>
				<th class="tds1">선택된 부킹 고유번호 </th>
				<td class="tds2"><?=$data[BKG_BKP_IDX]?></td>
			</tr>
			<tr>
            </tr>
            <tr>
                <th>부킹팀 정보</th>
                <td class="tds2"  colspan="3">
                    
                        <table cellspacing="1" cellpadding="0" class="table-style">
                        <tr>	
                        <th class="tds1" style="width:25px;">Team Num</th>
                        <th class="tds1" style="width:85px;">IN</th>
                        <th class="tds1">OUT</th>
                        <th class="tds1">Hotel</th>
                        <th class="tds1">Option</th>
                        <th class="tds1">Guest</th>
                        <th class="tds1">Agemcy</th>
						<th class="tds1">관리</th>
                        </tr>
                    <?
                    for($i=0;$i<count($bkp_idx_array);$i++){
                        $bkp_data = wepix_fetch_array(wepix_query_error("select * from ".$db_t_BOOKING_PARENT." where BKP_IDX = '".$bkp_idx_array[$i]."'"));
                        $hotel_data = explode("│",$bkp_data[BKP_HOTEL]);
                        $bkp_hot_kind = explode("│",$bkp_data[BKP_HOT_BOOKING_STATE]);
                        $bkp_hot_option = explode("│",$bkp_data[BKP_HOT_ALLIN_YN]);
                        
                        $bkp_start_date = date("d-M-y", $bkp_data[BKP_START_DATE]);
                        $bkp_arrive_date = date("d-M-y", $bkp_data[BKP_ARRIVE_DATE]);
                        $bkp_start_date2 = date("d-M-y", $bkp_data[BKP_START_DATE2]);
                        $bkp_arrive_date2 = date("d-M-y", $bkp_data[BKP_ARRIVE_DATE2]);
            
                        $bkp_start_flight = $bkp_data[BKP_START_FLIGHT];
                        $bkp_arrive_flight = $bkp_data[BKP_ARRIVE_FLIGHT];
            
                        //호텔 , 박수 , 룸 타입, 손님인스턴스를 가져와 '|' 기준으로 잘라서 배열로 저장
                        $bkp_hoter_array = explode("│",$bkp_data[BKP_HOTEL]);
                        $bkp_schedule_day = explode("│",$bkp_data[BKP_SCHEDULE_DAY]);
                        $bkp_guest_instant = explode("│",$bkp_data[BKP_GUEST]);
            
                        $bkp_booking_date = date("y-m-d", $bkp_data[BKP_BOOKING_DATE]);
                        $_bkp_booking_date_mo = date("y-m-d", $bkp_data[BKP_BOOKING_MO_DATE]);
            
                    
            
                        if($_bkp_booking_date_mo == '70-01-01'){
                            $bkp_booking_date_mo = '';
                        }else{
                            $bkp_booking_date_mo = date("y-m-d", $bkp_data[BKP_BOOKING_MO_DATE]);
                        }
                        ?>
                        <tr>
                        <td class="tds2" ><?=$bkp_data[BKP_IDX]?></td>
                        <td class="tds2" ><?=$bkp_start_date?></td>
                        <td class="tds2" ><?=$bkp_arrive_date?></td>
                        <td class="tds2" >
                        <table cellspacing='1' cellpadding='0'>
                        <?for( $hi=0; $hi<count($hotel_data); $hi++ ){
                            if($hi < 2){
                                $hotel_data2 = explode(":",$hotel_data[$hi]);
                                $hotel_data3 = explode("/",$bkp_schedule_day[$hi]);?>
                        <tr>
                                    <?if($hotel_data2[1] != 'none'){?>
                                        <td><?=$hotel_data2[1]?> (<?=$hotel_data2[3]?>) </td>
                                        <td><?=$hotel_data3[0]?></td>
                                    <?}?>
                                    </tr>
                           <? }
                        }?>
                        </table>
                        
                        </td>
                        <td class="tds2" >
                        <?
                        for($ho=0;$ho<count($bkp_hot_option);$ho++){
                            $bkp_hot_option_array = explode(",",$bkp_hot_option[$ho]);
                            for($h2=0;$h2<count($bkp_hot_option_array);$h2++){
                                if($bkp_hot_option_array[$h2] != 'none'){?>
                                    <?=$bkp_hot_option_array[$h2]?>
                                <?}
                            }
                        }
                        ?></td>
            
                        <td class="tds2" >
                        <table cellspacing='1' cellpadding='0'>
                            <?for( $g=0; $g<count($bkp_guest_instant); $g++ ){
                                $agi = explode("/",$bkp_guest_instant[$g]);
                                $guest_num = $g+1;?>
                                <tr>
                                            
                                            <?if($agi[1] != ''){?>
                                            <td width='30px' class='p-3 text-right f-s-14'><?=$agi[0]?></td>
                                            <td width='50px' class='p-3 text-center'><b><?=$agi[1]?></b></td>
                                            <td class='p-3'><?=strtoupper($agi[2])?></td>
                                            <?}}?>
                                </tr>
                            
                            <? $total_haed = count($bkp_guest_instant) - 2;
                            if($total_haed >= 1){?>
                                <tr>
                                <td class='p-3 text-right' colspan='3'>외 <?=$total_haed?> 명</td> </tr>
                            <?}?>

                        </table>
                        
                            
                    <?
                        $ag = explode("-", $bkp_data[BKP_AGNCY_TEXT]);
                      ?> 
                        <td class="tds2" ><?=$ag[0]?></td>
						<td><input type="button" value="Modify" onclick="location.href='<?=_A_PATH_BOOKING_MODIFY_POPUP?>?key=<?=$data[BKG_BKP_IDX]?>&mode=modify'"></td>   
                    </tr>
                    <?}?>

                    </table>
                
                </td>
				
            </tr>

		</table>
     	</form>
    </div>
	<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_BOOKING_GROUP_LIST?><?=$page_link_text?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="bookingModify('<?=$bkp_data[BKP_IDX]?>');"> 
						<i class="far fa-check-circle"></i>
						그룹 수정
					</button>
				</ul>
			</div>
	
</div>
</div>
<?
include "../layout/footer.php";
?>