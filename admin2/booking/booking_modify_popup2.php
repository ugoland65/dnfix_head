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
.table-wrap{ width:100%; }
.table-style{ width:100%; }
</STYLE>

<link rel="stylesheet" href="/admin2/css/daterangepicker.css" />
<script src="/admin2/js/moment.min.js"></script>
<script src="/admin2/js/jquery.daterangepicker.js"></script>
<script src="/admin2/js/demo.js"></script>
<script type='text/javascript'>

$(function() {
    var languages = '<?php echo $agc_company_array;?>';
    var languages_array =  languages.split(",");  
    var languages2 = '<?php echo $agc_company_array2;?>';
    var languages_array2 =  languages2.split(",");  
    
	$( "#bkp_agency_1" ).autocomplete({
		source: languages_array
	});
    $( "#bkp_agency_2" ).autocomplete({
		source: languages_array2
	});
});

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
function agency_cf(key){
    var form = document.form1;
    form.a_mode.value='agency_cf';
    form.submit();

}


var optionCount = 0;



var optionDel=function(num){
	$('#option_tr_'+num).remove();
};
var guestDel = function(key){
    $('#trid2_'+key).remove();
    gt_count--;
};

		function newSelecCo(selectKey){ 

		 var co_idx = $("#bkp_agency_1 option:selected").val();

			$.ajax({
					type : "POST",
					url : "../manager/agency_co_ok.php",
					data : {mode:"selectCo_2",select_key:selectKey ,co_idx:co_idx},
					error : function(){
					},
					success : function(data){
						$("#bkp_agency_2").html(data) ;
					}
				});

		}
$(document).ready(function() {

    $("#text_en").bind("keyup", function() {

    $( this ).val($( this ).val().toUpperCase());

    }); 

});

function sb_qt_plus(value){
    var hot_qty = 1*$("#hot_qty_"+value).val();
    var sbN = 1*$("#sbN_"+value).val();
	var option_total_price = 1*$("#hot_option_rate_"+value).val();



    var priceN = 1*$('#priceN_'+value).val(); 
    var price_total = (priceN * sbN) * hot_qty;

	var hot_total_price = option_total_price + price_total;
    $('#price_total_'+value).val(price_total);
	$('#hot_total_price_'+value).val(hot_total_price);
    
}

function option_total_price(value,key){

	var room_total_price = 1*$("#price_total_"+value).val();
	var option_total_price = 0;
	var option_price_text = $("[id^='hotel_chbox_price_"+value+"']").map(function(){
		return this.value;
	}).get();
	for(var i=0;i<option_price_text.length;i++){
	 option_total_price += 1*option_price_text[i];

	}
    var price_total =option_total_price;
	var hot_total_price = room_total_price + price_total;
    $('#hot_option_rate_'+value).val(price_total);
    $('#hot_total_price_'+value).val(hot_total_price);

}

		
<!--
var mode = "<?php echo $a_mode;?>"
var rowcount_hot = 1;
var rowcount_option = 1;
var rowcount_gst = 1;
var rowcount_golf = 1;
var golf_count = 1;
var rowcount_ft = 1;
if(mode == 'modify'){
   var gt_count = (1*"<?php echo $bkp_guest_count;?>")+1;
}else{
   var gt_count = 1;
}
var hotelSelectData = "";
var roomtypeSelectData = "";
var scheduledateSelectData = "";
var guestKindSelectData = "";
var golfSelectData="";
var qtySelectData="";
var hotel_all_inclusive_Data ="";
var land_fee_text_Data = "";
var birthYData = "";
var birthMData = "";
var birthDData = "";

    function doRoomChange(id,selectd){

    	var hot_idx =  $("#hotelSe_" + id + " option:selected").val();
		
		 $.ajax({
             type : "POST",
             url : "booking_ok.php",
	         data :{a_mode:"selectRoom",key:hot_idx,selectd_key:selectd},
            error:function(request,status,error){
	        },
            success : function(data){

					$("#room_select_box_"+id).html(data) ;
            }
        });
	   }
<?
$hotel_result = wepix_query_error($hotel_query);
while($hotel_list = wepix_fetch_array($hotel_result)) {
?>
	hotelSelectData += "<option value='<?=$hotel_list[HOT_IDX]?>:<?=$hotel_list[HOT_NAME]?>' ><?=$hotel_list[HOT_NAME]?></option>";
<? } ?>

<?

$roomtype_result = wepix_query_error($roomtype_query);
while($roomtype_list = wepix_fetch_array($roomtype_result)) {
?>
	roomtypeSelectData += "<option value='<?=$roomtype_list[ROC_IDX]?>:<?=$hotel_list[HOT_NAME]?>' ><?=$roomtype_list[ROC_NAME]?></option>";
<? } ?>
	<?
	$birth_y = date('Y',$wepix_now_time);
	for($y=1930;$y<=$birth_y;$y++){?>
		birthYData += "<option value='<?=$y?>'><?=$y?></option>";
	<?}?>
	<?
	for($m=1;$m<=12;$m++){
		if($m < 10){
			$value = '0'.$m;
		}else{
			$value = $m;
		}
	?>
		birthMData += "<option value='<?=$value?>'><?=$m?></option>";
	<?}?>
	<?
	for($d=1;$d<=31;$d++){
		if($d < 10){
			$value = '0'.$d;
		}else{
			$value = $d;
		}
	?>
		birthDData += "<option value='<?=$value?>'><?=$d?></option>";
	<?}?>
<?
$sdNum = 1;
for( $i=0; $i<count($schedule_date_array); $i++ ){
?>
	scheduledateSelectData += "<option value='<?=$sdNum?>' ><?=$sdNum?>N</option>";
<?$sdNum++; } ?>

<?
for( $i=0; $i<count($guest_kind_array); $i++ ){
?>
	guestKindSelectData += "<option value='<?=$guest_kind_array[$i]?>'><?=$guest_kind_array[$i]?></option>";
<? } ?>

<?
for( $i=0; $i<count($_ad_land_fee_text_array); $i++ ){
?>
	land_fee_text_Data += "<option value='<?=$i?>'><?=$_ad_land_fee_text_array[$i]?></option>";
<? } ?>

<?
for( $z=1; $z<10; $z++ ){
?>
	qtySelectData += "<option value='<?=$z?>'><?=$z?></option>";
<? } ?>

<?
$golf_result = wepix_query_error($golf_query);
while($golf_list = wepix_fetch_array($golf_result)) {
?>
	golfSelectData += "<option value='<?=$golf_list[GF_IDX]?>' ><?=$golf_list[GF_NAME]?></option>";
<? } ?>

<?
for( $ha=0; $ha<count($hotel_all_inclusive_array); $ha++ ){
?>
    hotel_all_inclusive_Data += "<option value='<?=$hotel_all_inclusive_array[$ha]?>'><?=$hotel_all_inclusive_array[$ha]?></option>";
<? } ?>

var optionAdd=function(){
	
	optionCount++;

	var showHtml2 =""
	+"<tr id='option_tr_"+ optionCount +"'>"
	+"<td class='tds2'>"
	+"<select name='bkp_landfee_name[]' >"
	+land_fee_text_Data
	+"</select>"
	+"</td>"
	+"<td class='tds2'><input type='text' name='bkp_landfee[]'  value='0'></td>"
	+"<td class='tds2'><input type='text' name='bkp_landfee_people[]'  value='0'></td>"
	+"<td class='tds2'><input type='text' name='bkp_landfee_sn[]'  value='0'></td>"
	+"</tr>";


	
	$("#bk_land_table").append(showHtml2);
};

var hotelPlus=function(){

	rowcount_hot++;

	
	var daterangeper_st = (rowcount_hot*2);
	var daterangeper_ed = (rowcount_hot*2)+1;
	var showHtml = ""
		+"<tr id='trid_"+ rowcount_hot +"' style='text-align:center;'>"
		
		+"<td class='tds2'><input type='text' id='date-range"+ daterangeper_st +"' name='bkp_hot_check_in[]' readonly />"
		+"<input type='text' id='date-range"+ daterangeper_ed +"' name='bkp_hot_check_out[]' readonly /></td>"
		+"<td class='tds2'text-left'>"
  		+"<div><select name='hotelN[]'  id='hotelSe_"+ rowcount_hot +"' onchange=\"javascript:doRoomChange('"+ rowcount_hot +"');\"  class='select1'>"
  		+"<option value='0:none' >== 호텔 선택 ==</option>"
		+ hotelSelectData
		+"</select></div>"	
  		+"<div><select name='rtN[]' id='room_select_box_"+ rowcount_hot +"'' class='select1' style='margin-top:5px;'>"
  		+"<option value='0:none'>== 룸타입 선택 ==</option>"
		+"</select>"
		+"<select name='bed_type[]'  id='bed_type_"+ rowcount_hot +"' style='margin-top:5px; margin-left:5px;' class='select1'>"
        +"<option value='Double'>Double</option>"
        +"<option value='Double(S)'>Double(S)</option>"
        +"<option value='DBL+EX'>DBL+EX </option>"
        +"<option value='Twin+EX'>Twin+EX</option>"
        +"<option value='Twin'>Twin</option>"
        +"<option value='Triple'>Triple</option>"
		+"</select>"
		+"<input type='text' name='priceN[]' id='priceN_"+ rowcount_hot +"' class='width-100' onkeyUP=\"sb_qt_plus('"+rowcount_hot+"')\"><br/>"
		+"</div>"
		+"</td>"
	    +"<td class='tds2'><input type='text' name='bkp_hotel_conf_num[]' value=''></td>"
		+"<td class='tds2'text-right '>"
		+"<input type='button' value='+ 옵션추가' class=\"btnstyle1 btnstyle1-success btnstyle1-xs m-b-5\" style='float:right;'  onclick=\"hot_option_Plus('"+rowcount_hot+"')\"><br/>"
		+"<table cellspacing='1' cellpadding='0' class='table-new' id='hot_option_"+ rowcount_hot +"' >"
		+"<tr><td>"
		+"<select name='hotel_chbox_"+ rowcount_hot +"[]' >"
		+hotel_all_inclusive_Data
        +"</select>"
		+"<input type='text' style='width:80px;' name='hotel_chbox_price_"+ rowcount_hot +"[]'  id='hotel_chbox_price_"+ rowcount_hot +"_1' value='0' onChange=\"option_total_price('"+rowcount_hot+"','1')\">"
		+"</td></tr>"
		+"</table>"
		+"</td>"
        +"<td class='tds2'>"
  		+"<input type='text' name='hot_head[]'  style='width:30px;'/>"
  		+"<input type='text' name='hot_head_c[]' style='width:30px;'/>"
		+"</td>"
        +"<td class='tds2'>"
        +"<input type='text' name='hot_qty[]' id='hot_qty_"+ rowcount_hot +"' style='width:30px;' onkeyUP=\"sb_qt_plus('"+rowcount_hot+"')\" />"
		+"<input type='text' name='sdN[]' id='sbN_"+ rowcount_hot +"' style='width:30px;' onkeyUP=\"sb_qt_plus('"+rowcount_hot+"')\" />"
        +"</td>"
        +"<td class='tds2' class='text-center'>"
		+"<input type='text' name='hot_option_rate[]' id='hot_option_rate_"+ rowcount_hot +"' placeholder='Total Option' >"
		+"<input type='text' name='hot_room_rate[]' id='price_total_"+ rowcount_hot +"'  placeholder='Total Room' readonly class='width-100'>"
		+"<input type='text' name='hot_total_price[]' id='hot_total_price_"+ rowcount_hot +"' placeholder='Total Hotel' readonly>"
		+"</td>"
		+"<td class='tds2'><textarea name='bkp_hotel_memo[]'></textarea></td>"


		+"<td class='tds2'><button type=\"button\" class=\"btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3 \" onclick=\"hotelDel('"+rowcount_hot+"');\"><i class=\"far fa-trash-alt\"></i> 삭제</button></td>"
		+"</tr>";

	

	$("#hotelPt").append(showHtml);
	pickerPlus(daterangeper_st,daterangeper_ed);

};

function pickerPlus(st,ed){
	$('#date-range'+st).dateRangePicker(
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
				var _s1 = s1.substring(2);
				var _s2 = s2.substring(2);
				$('#date-range'+st).val(s1);
				$('#date-range'+ed).val(s2);
			}
		});
}
var hot_option_Plus=function(value){
	rowcount_option++;
	var showHtml = ""
		+"<tr id='trid_op_"+value+"' style='text-align:center;'><td>"
		+"<select name='hotel_chbox_"+value+"[]' >"
		+hotel_all_inclusive_Data
        +"</select>"
		+"<input type='text' style='width:80px;' name='hotel_chbox_price_"+ value +"[]' id='hotel_chbox_price_"+ value +"_"+ rowcount_option +"' value='0' onChange=\"option_total_price('"+value+"','"+rowcount_option+"')\">"
		+"</td></tr>";

		
		$("#hot_option_"+value).append(showHtml);
}

var guestPlus=function(){

	rowcount_gst++;
    
	var showHtml = ""
		+"<tr id='trid2_"+ rowcount_gst +"' style='text-align:center;'>"
     
		+"<td class='tds2'>"
  		+"<select name='ges_kind[]' class='select1'>"
		+ guestKindSelectData
		+"</select>"	
		+"</td>"
		+"<td class='tds2'><input type='text' name='ges_ko[]' class='inputtext1'></td>"
		+"<td class='tds2'><input type='text' name='ges_en[]' id='text_en' class='inputtext_en_1 text-transform-uppercase'></td>"
		+"<td class='tds2'>"
		+"<select name='ges_birth_d[]' class='select1'>"
		+"<option value=''>Day</option>"
		+birthDData
		+"</select>"
		+"<select name='ges_birth_m[]' class='select1'>"
		+"<option value=''>Month</option>"
		+birthMData
		+"</select>"
		+"<select name='ges_birth_y[]' class='select1'>"
		+"<option value=''>Year</option>"
		+birthYData
		+"</select>"
		+"</td>"
		+"<td class='tds2'><input type='text' name='ges_age[]' class='inputtext1' ></td>"
		+"<td class='tds2'><input type='text' name='ges_passport_num[]' class='inputtext1'></td>"
		+"<td class='tds2'><input type='checkbox' name='ges_similan[]' class='checkSelect' value='Y'></td>"
		+"<td class='tds2'><input type='text' name='ges_passport_date[]' class='inputtext1'></td>"
		+"<td class='tds2'><button type=\"button\" class=\"btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3 \" onclick=\"guestDel('"+rowcount_gst+"');\"><i class=\"far fa-trash-alt\"></i> 삭제</button></td>"
		+"</tr>";

	$("#guestPt").append(showHtml);
    gt_count++;
};

var golfPlus=function(){

rowcount_golf++;
var showHtml = ""
    +"<tr id='trid3_"+ rowcount_golf +"' style='text-align:center;'>"
    +"<td class='tds2'>No."+golf_count+"</td>"
    +"<td class='tds2'>"
    +"<select class='select1' name='golf_name[]'>"
    + golfSelectData
    +"</select>"	
    +"</td>"

	+"<td class='tds2'><input type='text' id='bkp_golf_data_"+ rowcount_golf +"' name='t_up_date[]' onkeyup=\"autoHypendate(event, this)\" onkeypress=\"autoHypendate(event, this)\"  maxlength='8' value='' style='width:90px;' /></td>"


    +"<td class='tds2'><input type='text' name='t_up_time[]'></td>"
    +"<td class='tds2'><input type='text' name='gf_haed_ct[]'></td>"
    +"<td  class='tds2'>"
    +"<select name='gf_cart[]'>"
    +"<option value='single'>Single</option>"
    +"<option value='double'>Double</option>"
    +"</select>"
	+"</td>"
    +"<td class='tds2'>"
    +"<select class='select1' name='holl_count[]'>"
    +"<option value='9'>9</option>"
    +"<option value='18'>18</option>"
    +"<option value='36'>36</option>"
    +"</select>"
    +"</td>"
   
    +"<td class='tds2'>"
    +"<select class='select1' name='gf_am_pm[]'>"
    +"<option value='am''>AM</option>"
    +"<option value='pm'>PM</option>"
    +"</select>"
    +"</td>"

    +"<td class='tds2'>"
    +"<label class='p-r-5'><input type='radio' name='add_holl_"+ rowcount_golf +"' value='0' checked>없음</label> "
    +"<label class='p-r-5'><input type='radio' name='add_holl_"+ rowcount_golf +"' value='9'>9홀</label> "
    +"<label class='p-r-5'><input type='radio' name='add_holl_"+ rowcount_golf +"' value='18'>18홀</label></td>"
    +"<td class='tds2'>"
    +"<label class='p-r-5'><input type='radio' name='coupon_"+ rowcount_golf +"' value='N' checked>미사용</label>"
    +"<label class='p-r-5'><input type='radio' name='coupon_"+ rowcount_golf +"' value='9'>9홀</label>"
    +"<label class='p-r-5'><input type='radio' name='coupon_"+ rowcount_golf +"' value='18'>18홀</label>"
    +"<label class='p-r-5'><input type='radio' name='coupon_"+ rowcount_golf +"' value='Set'>Set</label>"
	+"</td>"
    +"<td class='tds2'>"
    +"<label class='p-r-5'><input type='radio' name='caddie_yn_"+ rowcount_golf +"' value='N' checked>미 사용</label>"
    +"<label class='p-r-5'><input type='radio' name='caddie_yn_"+ rowcount_golf +"' value='Y'>사용</label>"
	+"</td>"
    +"<td class='tds2'>"
    +"<input type='text' readonly>"
    +"</td>"
    +"<td class='tds2'><button type=\"button\" class=\"btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3 \" onclick=\"golfDel('"+rowcount_golf+"');\"><i class=\"far fa-trash-alt\"></i> 삭제</button></td>"

    +"</tr>";
    golf_count++;
$("#golfPt").append(showHtml);

		
};


var FastTrackPlus=function(count){
var ct = 0;
for(var ct=0;ct<count;ct++){

var showHtml = ""
    +"<tr id='trid4' style='text-align:center;'>"
    +"<th class='tds1'>VC No.</th>"
    +"<td><input type='text' name='va_no[]' id='va_no_"+ rowcount_ft +"' onchange='doFastTrackNo()'></td>"
    +"</tr>";

    rowcount_ft++;
    $("#fastTrackPt").append(showHtml);
    }
};

var hotelDel = function(key){
    $('#trid_'+key).remove();
};

var guestDel = function(key){
    $('#trid2_'+key).remove();
    gt_count--;
};

var golfDel = function(key){
    $('#trid3_'+key).remove();
};
var FastTrackDel = function(){
    $('#trid4').remove();
};
function mo_golfDel(key,idx){
    
    $.ajax({
		type : "POST",
		url : "booking_ok.php",
		data : {action_mode:"golf_del",mokey:idx},
		success : function(data){
            location.reload();
		}
		});

			
};
//--> 

function select_bkp_type(){
    var select_val = $("#bkp_type option:selected").val();
    var option_tr = document.getElementById("option_tr");
    
    if(select_val == 'GF'){
       option_tr.style.display = '';
     }else{
        option_tr.style.display = 'none';
     }

}

function agency_cf(key){
    var form = document.formN1;
    form.action_mode.value='agency_cf';
    form.action = "/admin/booking/booking_ok.php";
    form.submit();

}

var guest_count = '<?=$bkp_guest_count?>';  
function fastTrack_yn(value,idx){
    
    var con = document.getElementById("ft_div");
    if(value == 'Y'){
        FastTrackPlus(guest_count);
        con.style.display = 'block';
    }else if(value == 'N'){
        if (confirm("비사용시 기존에 입력한 Fast Track 정보는 삭제됩니다") == true){   
            con.style.display = 'none';
            for(var a=0;a<guest_count;a++){
                FastTrackDel();
            }
            $.ajax({
                type : "POST",
                url : "booking_ok.php",
                data : {action_mode:"fastTrack_del",mokey:idx},
                success : function(data){
                    location.reload();
                }
		    });
        }else{   
            return false;
        }
        
    }

}
</script>

<div id="contents_head">
	<h1><?=$page_title_text?></h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">

	
	<form name='form1' action='<?=_A_PATH_BOOKING_OK?>' method='post' enctype="multipart/form-data">
		<?if($_mode == 'modify'){?>
			<input type='hidden' name='a_mode' value='bookingModify'>
			<input type='hidden' name='key' value='<?=$_bkp_idx?>'>
			<input type='hidden' name='modify_maching_code' value='<?=$_view_maching_code?>'>
			<input type='hidden' name='show_get_url' value='<?=$_show_get_url?>'>
		<?}else{?>
		    <input type='hidden' name='a_mode' value='bookingNew'>
		<?}?>
			<input type='hidden' name='bkp_similan_ck'>
	
        <div class="section-title">
			<h2>Basic Information</h2>
        </div>

		<table cellspacing="1" cellpadding="0" class="table-style" >
		<?if($_mode == 'modify'){?>
			<tr>
				<th class="tds1">Team Name</th>
				<td class="tds2"><?=$_view_team_name?></td>
				<th class="tds1">Team Number</th>
				<td class="tds2"><?=$_view_bkp_idx?></td>
			</tr>
		<?}?>
			<tr>
				<th class="tds1">Area</th>
				<td class="tds2">
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
				<th class="tds1">Tour Type</th>
				<td class="tds2">
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
				<th class="tds1">State</th>
				<td class="tds2">
				<select name='bkp_kind'>			  
					<option value='NEW' <?if($bk_data[BKP_KIND] == 'NEW'){ echo "selected";}?>>NEW</option>
					<option value='AMEND' <?if($bk_data[BKP_KIND] == 'AMEND'){ echo "selected";}?>>AMEND</option>
					<option value='BLOCK' <?if($bk_data[BKP_KIND] == 'BLOCK'){ echo "selected";}?>>BLOCK</option>
					<option value='CANCEL' <?if($bk_data[BKP_KIND] == 'CANCEL'){ echo "selected";}?>>CANCEL</option>
					<option value='DUPE' <?if($bk_data[BKP_KIND] == 'DUPE'){ echo "selected";}?>>DUPE</option>
				</select>
				</td>
				<th class="tds1">Guide</th>
				<td class="tds2"><?=$bk_data[BKP_GUIDE_ID]?></td>
				
			</tr>
			<tr>
			<th class="tds1">IN/OUT</th>
			<td class="tds2">

				<table cellspacing="0" cellpadding="0" class="table-none-new" >
					<tr>
						<td class="p-3"><input type="text" placeholder="in Transfer Date"  readonly name="date_start2"  id='date-range1' value="<?=$_view_bkp_start_date2?>" style="width:100px;"/></td>
						<td class="p-3"><input type="text" placeholder="out Transfer Date" readonly  name="date_end2" id='date-range0' value="<?=$_view_bkp_arrive_date2?>"  style="width:100px;"/></td>					
					</tr>
					<tr>
						<td class="p-3"><input type="text" placeholder="in Date"  name="date_start" id='date-range2' value="<?=$_view_bkp_start_date?>"  style="width:100px;"/></td>
						<td class="p-3"><input type="text" placeholder="out Date"   name="date_end" id='date-range3' value="<?=$_view_bkp_arrive_date?>" style="width:100px;"/></td>
					</tr>
				</table>
								
			</td>
			<th class="tds1">Flight</th>
			<td class="tds2">

			<table cellspacing="0" cellpadding="0" class="table-none-new" >
				<tr>
					<td class="p-3">
						<input type='text' title="in Transfer flight" placeholder="in Transfer Flight" style="width:140px; text-transform:uppercase;" name="bkp_start_flight2" value='<?=$_view_bkp_start_flight2?>'>
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
						<input type='text' title="out Transfer flight" placeholder="out Transfer Flight" style="width:145px; text-transform:uppercase;" name="bkp_arrive_flight2" value='<?=$_view_bkp_arrive_flight2?>'>
					</td>
				</tr>
			</table>


				</td>
			</tr>

			<tr>
				<th class="tds1">First Booking Date</th>
				<td class="tds2"><input type="text" name="booking_date"  value="<?=$_view_bkp_booking_date?>"  onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)"  style="width:85px; "/></td>
				<th class="tds1">Change Booking Date</th>
				<td class="tds2"><input type="text"  maxlength='10' name="booking_date2" value="<?=$_view_bkp_booking_mod_date?>" readonly style="width:85px; "  /></td>
			</tr>
<?if($_mode == 'modify'){?>
			<tr>
				<th class="tds1">Registration date</th>
				<td class="tds2"><?=$_view_bkp_req_date?></td>
				<th class="tds1">Registrant</th>
				<td class="tds2"><?=$bk_data[BKP_RESERVER]?></td>
			</tr>
<?}?>
			<tr>
				<th class="tds1" >Provide T/M</th>
				<td class="tds2" colspan="3" >
					<input type='text' style="width:150px;" name='bkp_money_first'  id='bkp_money_first' value="<?=number_format($_view_bkp_first_money)?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
				</td>	
				
			</tr>
		</table>

	

	
        <div class="section-title">
			<h2>Agency</h2>
        </div>
		<table cellspacing="0" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds1">Agency</th>
				<td class="tds2">
					 <?   $angcy_text = explode("-",$bk_data[BKP_AGNCY_TEXT]);?>
					<input type='text' name='bkp_agency_1' id='bkp_agency_1' style="width:100px;" value='<?=$angcy_text[0]?>'>
					<input type='text' name='bkp_agency_2' id='bkp_agency_2' style="width:100px;" value='<?=$angcy_text[1]?>'>
				</td>
				<th class="tds1">Agency State</th>
				<td class="tds2">
				<? if($_view_agency_yn == 'N'){ ?>
					<button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-xs m-t-3 width-50" onclick="agency_cf('<?=$bk_list[BKP_IDX]?>', 'N');"><i class="fas fa-angle-double-right"></i> W/T</button>
				<? }elseif( $_view_agency_yn == 'Y'){ ?>
					<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs m-t-3 width-50" onclick="agency_cf('<?=$bk_list[BKP_IDX]?>', 'Y');"><i class="far fa-check-square"></i> C/F</button>
				<? } ?>

				</td>
			</tr>
			<?if($_mode == 'modify'){?>
			<tr>
				<th class="tds1" >Maching Code</th>
				<td class="tds2" colspan='3'>
					<?=$_view_maching_code?>
				</td>
			</tr>
			<?}?>
			</table>

	
        <div class="section-title">
			<h2>Land Fee</h2>
			<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:180px; margin:5px; float:right;" onclick="optionAdd()"> <i class="fas fa-plus-circle"></i> Add Land Fee Option </button>
        </div>

		<table cellspacing="0" cellpadding="0" class="table-style"  id='bk_land_table' name='bk_land_table' >
				<tr>
					<th></th>
					<th>Price</th>
					<th>Personnel</th>
					<th>Nights</th>
				</tr>
				<?if($_mode != 'modify'){?>
				<tr>
					<td class="tds2">
					<select name='bkp_landfee_name[]' >
						<?for($l=0;$l<count($_ad_land_fee_text_array);$l++){?>
							<option value='<?=$l?>' <?if($_ary_land_fee[0] == $l){ echo "selected" ;}?>><?=$_ad_land_fee_text_array[$l]?></option>
						<?}?>
					</select>
					</td>
					<td class="tds2"><input type='text' name='bkp_landfee[]' value='0'></td>
					<td class="tds2"><input type='text' name='bkp_landfee_people[]' value='0'></td>
					<td class="tds2"><input type='text' name='bkp_landfee_sn[]' value='0'></td>
				</tr>
				<?}else{
					for($i=0;$i<count($_ary_land_fee_text);$i++){
						$_ary_land_fee = explode("/",$_ary_land_fee_text[$i]);
				?>
				<tr>
					<td class="tds2">
					<select name='bkp_landfee_name[]' >
						<?for($l=0;$l<count($_ad_land_fee_text_array);$l++){?>
							<option value='<?=$l?>' <?if($_ary_land_fee[0] == $l){ echo "selected" ;}?>><?=$_ad_land_fee_text_array[$l]?></option>
						<?}?>
					</select></td>
					<td class="tds2"><input type='text' name='bkp_landfee[]' value='<?=$_ary_land_fee[1]?>'></td>
					<td class="tds2"><input type='text' name='bkp_landfee_people[]' value='<?=$_ary_land_fee[2]?>'></td>
					<td class="tds2"><input type='text' name='bkp_landfee_sn[]' value='<?=$_ary_land_fee[3]?>'></td>
				</tr>	
				<?}
				}?>
				

		</table>
		<table cellspacing="0" cellpadding="0" class="table-style" >
			<tr>
				<td colspan='4' style='border-right:none; border-left:none; border-top:none; border-bottom:none;'>
					<span class="btnstyle1-primary btnstyle1-sm" style="width:150px; margin:5px; float:right;">총 가격 <?=number_format($_view_land_fee)?> 원</span>
				</td>
			</tr>
		</table>

	

	<!-- 손님정보 -->
	
        <div class="section-title">
			<h2>Guest Information</h2>
        </div>	

		<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:150px; margin:5px; float:right;" onclick="guestPlus()"> <i class="fas fa-plus-circle"></i> Add Guest</button>
		<table cellspacing="0" cellpadding="0" class="table-style" id="guestPt">
			<tr>
				
				<th class="p-3">Title</th>
				<th class="p-3" style="width:100px">Korea Name</th>
				<th class="p-3" style="width:200px">English Name</th>
                <th class="p-3">Day Month Year</th>
                <th class="p-3" style="width:100px">Age</th>
                <th class="p-3">Passport number</th>
                <th class="p-3">Similan</th>
                <th class="p-3">Guest ID</th>
				<th class="p-3">Delete</th>
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
			<td class="tds2"><select name='ges_kind[]' class='select1'>
				<option>구분선택</option>
				<?
				for( $z=0; $z<count($guest_kind_array); $z++ ){
				?>
							<option value='<?=$guest_kind_array[$z]?>' <? if( $_view2_guest_title == $guest_kind_array[$z] ) echo 'selected'; ?>><?=$guest_kind_array[$z]?></option>
				<? } ?>
			</select>
			</td>
				<td class="tds2"><input type='text' name='ges_ko[]' class='inputtext1' value="<?=$_view2_guest_name_kr?>"></td>
				<td class="tds2"><input type='text' name='ges_en[]' class='inputtext1 text-transform-uppercase' value="<?=$_view2_guest_name_en?>"></td>
				<input type='hidden' name='ges_Id[]' value="<?=$_view2_guest_id?>">
				<td class="tds2">
				<select name='ges_birth_d[]'>
						<option value=''>Birthday Day</option>
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
						<option value=''>Birthday Month</option>
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
						<option value=''>Birthday Year</option>
					<?
					$birth_y = date('Y',$wepix_now_time);
					for($y=1930;$y<=$birth_y;$y++){?>
						<option value='<?=$y?>' <?if($_view_birth_y == $y){ echo "selected";}?> ><?=$y?></option>
					<?}?>
					</select>
					<!--<input type='text' name='ges_birth[]' class='inputtext1' value="<?=$_ary_guest_birth[$i]?>">-->
				</td>    
				<td class="tds2"><input type='text' name='ges_age[]' class='inputtext1' value="<?=$_view2_guest_age?>"></td>
				<td class="tds2"><input type='text' name='ges_passport_num[]' class='inputtext1' value="<?=$_ary_guest_pass_num[$i]?>"></td>
				<td class="tds2"><input type='checkbox' name='ges_similan[]' class="checkSelect" value='Y' <?if($_ary_similan[$i] == 'Y'){echo "checked";}?>></td>
				<td class="tds2"><input type='text' name='ges_Id[]' class='inputtext1' value="<?=$_view2_guest_id?>"></td>
				<td class="tds2"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onClick="guestDel('mo_<?=$i?>')"><i class="far fa-trash-alt"></i> Delete</button></td>
			</tr>
			<? } ?>
		</table>

	
	<!-- 호텔 정보 -->

        <div class="section-title">
			<h2>Hotel Information</h2>
        </div>

			<div class="plusBtnWrap">
				<!-- <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="hotelPlus()"><i class="fas fa-plus-circle"></i> 호텔추가</button> -->
				<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:150px; margin:5px; float:right;" onclick="hotelPlus()"> <i class="fas fa-plus-circle"></i> Add Hotel</button>
			</div>
		<table cellspacing="1" cellpadding="0" class="table-style" id="hotelPt">
			<tr>
			
				<th style="width:5%">check in/out</th>
				<th style="width:24%">hotel / room type / bed type</th>
				<th style="width:8%">Confirmation No</th>
				<th style="width:18%">Option</th>
				<th style="width:6%">Personnel<br/>adult/child</th>
				<th style="width:6%">Room qty / Nights</th>
				<th style="width:7%">Total Price</th>
				<th style="width:*%">Etc</th>
				<th style="width:5%">관리</th>
				
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
				<td class="tds2" class="text-center">
					<input type="text" name="bkp_hot_check_in[]"   onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)" maxlength='8' value="<?=$_view2_hotel_check_in_day?>" /><br>
					<input type="text" name="bkp_hot_check_out[]"  onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)" maxlength='8' value="<?=$_view2_hotel_check_out_day?>" />
				</td>
				<td class="tds2">
				<select name='hotelN[]' id='hotelSe_mo_<?=$i?>' class='select1' onchange="doRoomChange('mo_<?=$i?>');">
					<option value='0:none'>Select Hotel</option>
						<?
						${'hotel_result_'.$i} = wepix_query_error($hotel_query);
						while( ${'hotel_list_'.$i} = wepix_fetch_array(${'hotel_result_'.$i})) {
						?>
							<option value='<?=${'hotel_list_'.$i}[HOT_IDX]?>:<?=${'hotel_list_'.$i}[HOT_NAME]?>' <? if( ${'hotel_list_'.$i}['HOT_IDX'] == $_show2_hotel_idx ) echo 'selected'; ?>><?=${'hotel_list_'.$i}[HOT_NAME]?></option>
						<? } ?>
					</select><br>
					<br>
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
					<select name='bed_type[]'  class='select1' style='margin-top:5px;'>
						<option value='Double' <?if($_view2_hotel_bed_type == 'Double') echo "selected"; ?>>Double</option>
						<option value='DBL+EX' <?if($_view2_hotel_bed_type == 'DBL+EX') echo "selected"; ?>>DBL+EX </option>
						<option value='Double(S)' <?if($_view2_hotel_bed_type == 'Double(S)') echo "selected"; ?>>Double(S)</option>
						<option value='Twin+EX' <?if($_view2_hotel_bed_type == 'Twin+EX') echo "selected"; ?>>Twin+EX</option>
						<option value='Twin' <?if($_view2_hotel_bed_type == 'Twin') echo "selected"; ?>>Twin</option>
						<option value='Triple' <?if($_view2_hotel_bed_type == 'Triple') echo "selected"; ?>>Triple</option>
				   </select>
				   <input type='text' name='priceN[]' id='priceN_<?=$i?>' value='<?=$_ary2_schedule[1]?>' class='width-100' onkeyUP="sb_qt_plus(<?=$i?>);">
				</td>
				<td class="tds2"><input type='text' name='bkp_hotel_conf_num[]' value='<?=$_ary_bkp_hot_cf_num[$i]?>'>
				<?if($_mode != 'new'){?>
				<button type="button" class="btnstyle1 <?=$show_hotel_sate_style?> btnstyle1-xs width-50" onclick='chKind(<?=$i?>,<?=$bkp_hot_kind[$i]?>,<?=$list[BKP_IDX]?>);'><i class="<?=$show_hotel_sate_icon?>"></i> <?=$_view2_hotel_reservation_state?></button>
				<?}?>
				</td>
				<td class="tds2">
					<?          
							 $_view2_allin_op = explode(",",$_ary_bkp_hot_option[$i]);
							 $_view2_allin_op_price = explode(",",$_ary_bkp_hot_option_price[$i]);
							 $hot_option_num = $i+2;
					?>
							<input type='button' value='+ option' class="btnstyle1 btnstyle1-success btnstyle1-xs m-b-10" style="float:right;" onclick="hot_option_Plus('<?=$i?>')"><br/>
							<table cellspacing='1' cellpadding='0' class='table-new' id='hot_option_<?=$i?>'>
								<?
								 for($hot_op=0;$hot_op<count($_view2_allin_op);$hot_op++){
								?>
								
								<tr>
									<td>
										<select name='hotel_chbox_<?=$i?>[]'>
											<?for( $ha=0; $ha<count($hotel_all_inclusive_array); $ha++ ){ ?>
											<option value='<?=$hotel_all_inclusive_array[$ha]?>' <?if($_view2_allin_op[$hot_op] == $hotel_all_inclusive_array[$ha]) echo "selected"; ?>><?=$hotel_all_inclusive_array[$ha]?></option>
											<?}?>
										</select>
										<input type='text' style='width:80px;' name='hotel_chbox_price_<?=$i?>[]'  id='hotel_chbox_price_<?=$i?>_<?=$hot_op?>' value='<?=$_view2_allin_op_price[$hot_op]?>' onChange="option_total_price('<?=$i?>',<?=$hot_op?>)">
									</td>
								</tr>
								<?}?>
							</table>
				</td>
				<td class="tds2">
				<input type='text' style='width:40px;'name='hot_head[]' value='<?=$_ary_bkp_head_count[$i]?>'>
				<input type='text' style='width:40px;'name='hot_head_c[]' value='<?=$_ary_bkp_head_count_c[$i]?>'>
				</td>
				<td class="tds2" class="text-center">
				<input type='text' style='width:40px;'name='hot_qty[]' id='hot_qty_<?=$i?>'  onkeyUP="sb_qt_plus(<?=$i?>);" value='<?=$_view2_room_count?>'>
				<input type='text' name='sdN[]' style='width:40px;' id='sbN_<?=$i?>' onkeyUP="sb_qt_plus(<?=$i?>);" value='<?=$_view2_hotel_stay_kr_text?>'></td>
				<td class="tds2" class="text-center"> 
					<input type='text' name='hot_option_rate[]' id='hot_option_rate_<?=$i?>' placeholder="Total Option" value='<?=$_ary2_bkp_hot_total_price[0]?>'>
					<input type='text' name='hot_room_rate[]' id='price_total_<?=$i?>'  placeholder="Total Room" value='<?=$_ary2_bkp_hot_total_price[1]?>'>
					<input type='text' name='hot_total_price[]' id='hot_total_price_<?=$i?>'  placeholder="Total Hotel " value='<?=$_ary2_bkp_hot_total_price[2]?>' readonly>
				</td>
				<td class="tds2"><textarea name='bkp_hotel_memo[]'><?=$_ary_bkp_hot_memo[$i]?></textarea></td>
				<td class='tds2'><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="hotelDel('<?=$hotel_num?>');"><i class="far fa-trash-alt"></i> 삭제</button></td>
	
				
			</tr>
			<? } ?>
		</table>

		<table cellspacing="0" cellpadding="0" class="table-style" >
			<tr>
				<td colspan='4' style='border-right:none; border-left:none; border-top:none; border-bottom:none;'>
					<span class="btnstyle1-primary btnstyle1-sm" style="width:150px; margin:5px; float:right;">총 가격 <?=number_format($_view_total_hotel_price)?> 원</span>
				</td>
			</tr>
		</table>
<!--
        <div class="section-button">
			<button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs " onclick='bookingInfo("<?=$_view2_bkp_idx?>");'>방번호 전체변경</button>
        </div>
-->


	<div class="section" style="<?=$golf_dispaly?>" id="option_tr">
        <div class="section-title">
			<h2>Golf Information</h2>
        </div>
			<div class="plusBtnWrap"><input type="button" value="골프장 추가" class="plusBtn" onclick="golfPlus()"></div>
			<table cellspacing="1" cellpadding="0" class="table-style"  id="golfPt">
				<tr class="text-center ">
					<th class="tds1">Num</th>
					<th class="tds1">Golf Course Name</th>
					<th class="tds1">use Date</th>
					<th class="tds1">estimat time</th>
					<th class="tds1" style="width:50px;">Personnel</th>
					<th class="tds1">Cart usage fee</th>
					<th class="tds1">hole numbers</th>
					<th class="tds1">Am/Pm</th>
					<th class="tds1">Add hole</th>
					<th class="tds1">Coupon Whether</th>
					<th class="tds1">caddie Fee</th>
					<th class="tds1">Total Price</th>
					<th class="tds1">Delete</th>
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
						</select>
					</td>
					<?
					 $week_num = date("w",strtotime($go_list[BG_ST_DATE]));
					 if($week_num == 0){
						 $golf_color = '#ff0000';
					 }elseif($week_num == 6){
						 $golf_color = '#4169e1';
					 }else{
						$golf_color = "#ffffff";
					 }
					?>
					<td  class="tds2" bgcolor="<?=$golf_color?>">
						<input type="text" name = 'mo_t_up_date[]'  name="t_up_date[]" value="<?=$go_list[BG_ST_DATE]?>" onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)"  maxlength='8' value='' style='width:90px;'/>
					</td>
					<td class="tds2"><input type='text' name = 'mo_t_up_time[]' value='<?=$go_list[BG_ST_TIME]?>'></td>
					<td class="tds2"><input type='text' name = 'mo_gf_haed_ct[]' value='<?=$go_list[BG_HEAD_CT]?>'></td>
					 <td class="tds2">
						<select name='mo_gf_cart[]'>
							<option value='single' <?if($go_list[BG_CART] == 'single') echo "selected"; ?>>Single</option>
							<option value='double' <?if($go_list[BG_CART] == 'double') echo "selected"; ?>>Double</option>
						</select>
					</td>
					<td class="tds2">
						<select name='mo_holl_count[]'>
							<option value='9' <?if($go_list[BG_HOLL_CT] == '9') echo "selected"; ?>>9</option>
							<option value='18' <?if($go_list[BG_HOLL_CT] == '18') echo "selected"; ?>>18</option>
							<option value='36' <?if($go_list[BG_HOLL_CT] == '36') echo "selected"; ?>>36</option>
						</select>
					</td>
				   
					<td class="tds2">
						<select name='mo_gf_am_pm[]'>
							<option value='am' <?if($go_list[BG_TIME] == 'am') echo "selected"; ?>>AM</option>
							<option value='pm' <?if($go_list[BG_TIME] == 'pm') echo "selected"; ?>>PM</option>
						</select>
					</td>          
					<td class="tds2">
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_HOLL_ADD_YN] == '0') echo "checked"; ?> name='mo_add_holl_<?=$num?>' value='0'>없음</label>
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_HOLL_ADD_YN] == '9') echo "checked"; ?> name='mo_add_holl_<?=$num?>' value='9'>9홀</label>
						<label  class='p-r-5'><input type='radio' <?if($go_list[BG_HOLL_ADD_YN] == '18') echo "checked"; ?> name='mo_add_holl_<?=$num?>' value='18'>18홀</label>
					</td>
					<td class="tds2">
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_COUPON_YN] == 'N') echo "checked"; ?> name='mo_coupon_<?=$num?>' value='N'>미사용</label>
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_COUPON_YN] == '9') echo "checked"; ?> name='mo_coupon_<?=$num?>' value='9'>9홀</label>
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_COUPON_YN] == '18') echo "checked"; ?> name='mo_coupon_<?=$num?>' value='18'>18홀</label>
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_COUPON_YN] == 'Set') echo "checked"; ?> name='mo_coupon_<?=$num?>' value='Set'>Set</label>
					</td>
					<td class="tds2">
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_CADDIE_YN] == 'N') echo "checked"; ?> name='mo_caddie_yn_<?=$num?>' value='N'>미 사용</label>
						<label  class='p-r-5'><input type='radio'  <?if($go_list[BG_CADDIE_YN] == 'Y') echo "checked"; ?> name='mo_caddie_yn_<?=$num?>' value='Y'>사용</label>
					</td>
					<td class="tds2">
						<input type='text' readonly value='<?=$go_list[BG_TOTAL_PRICE]?>'>
					</td>
					<td class="tds2"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3 " onClick='mo_golfDel("<?=$num?>","<?=$go_list[BG_IDX]?>")'><i class="far fa-trash-alt"></i> Delete</button>
					</td>                   
				</tr>
				<?
				++$num;
				}?>

				<?}?>
				</table>
	</div>
<?if($_view_bkg_name){?>
        <div class="section-title">
			<h2>Group Information</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds1">Group Name</th>
				<td class="tds2"><?=$_view_bkg_name?></td>
				<th class="tds1">Group Number</th>
				<td class="tds2"><?=$_view_bkg_idx?></td>
			</tr>
			<tr>
				<th class="tds1">Group Team Count</th>
				<td class="tds2"><?=$_view_bkg_team_count?></td>
				<th class="tds1">Group Personnel</th>
				<td class="tds2"><?=$_view_bkg_head_count?></td>
			</tr>
		</table>
<?}?>
		<div class="section-title">
			<h2>Memo</h2>
		</div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
				<tr>
                    <th class="tds1" colspan="2">Memo - Admin</th>
					<th class="tds1" colspan="2">Forwarding details - Guide</th>
                    
					
				</tr>
                <?
                $bkp_memo_admin = str_replace('<br />','',$_view_admin_memo);
				$_view2_admin_memo = str_replace('옵션 : none ->','',$bkp_memo_admin);
                $_view2_admin_memo = str_replace('옵션 :  -> none','',$bkp_memo_admin);
                
                ?>
				<tr>
                    <td class="tds2" colspan="2" >
						<textarea name="bkp_memo_admin" class="textarea1"><?=$_view2_admin_memo?></textarea>
					</td>
					<td class="tds2" colspan="2" >
						<textarea name="bkp_memo" class="textarea1"><?=$_view_memo?></textarea>
					</td>
				</tr>
                <tr>
                    <td class="tds2" colspan="4" >
						<textarea class="textarea1" readonly> <?=str_replace('<br />','',$_view_mod_log)?></textarea>
					</td>
				</tr>

		</table>

		<div class="section-title">
			<h2>booking letter</h2>
		</div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds1">booking letter</th>
				<td class="tds2"><input type="file" name="upload" id="upload" multiple /></th>
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
				<th class="tds1">booking letter <?=$wented_num?></th>
				<td class="tds2">Registrant : <?=$wented_id?> / Registered Time : <?=$wented_date?>  <input type='button' value='수배서 보기' onclick="bookingWented('<?=$wented_idx?>');" ></td>
			</tr>
			<?}?>

		</table>

		<div class="section-title">
			<h2>확정서 등록</h2>
		</div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds1">확정서</th>
				<td class="tds2"><input type="file" name="upload2" id="upload2" multiple /></th>
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
				<th class="tds1">확정서 <?=$wented_num?></th>
				<td class="tds2">Registrant : <?=$wented_id?> / Registered Time : <?=$wented_date?>  <input type='button' value='확정서 보기' onclick="bookingWented('<?=$wented_idx?>');" ></td>
			</tr>
			<?}?>

		</table>

		<div class="section-title">
			<h2>청구서 리스트</h2>
		</div>
		<table cellspacing="1" cellpadding="0" class="table-style" >

			<?
			$wented_num = 0;
			$bill_result = wepix_query_error("select * from "._DB_BILL_TRAVEL." where PU_BKP_IDX = '".$key."' order by PU_IDX desc");
			while($bill_list = wepix_fetch_array($bill_result)){

				?>
			<tr>
				<th class="tds1">확정서 <?=$bill_list[PU_IDX]?></th>
				<td class="tds2">Registrant : <?=$wented_id?> / Registered Time : <?=$wented_date?>  <input type='button' value='확정서 보기' onclick="bookingWented('<?=$wented_idx?>');" ></td>
			</tr>
			<?}?>

		</table>
	</form>

		</div>
		
				<div class="page-btn-wrap">
					<ul class="page-btn-left">
						<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="window.close();" > 
							Close
						</button>
					</ul>
					<ul class="page-btn-right">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="bookingModify();" > 
							<i class="far fa-check-circle"></i>
							<?=$submit_btn_text?>
						</button>
					</ul>
				</div>
	</div>
</div>

<script type="text/javascript"> 
<!-- 
var mode = "<?=$mode?>";

if(mode != 'modify'){
    hotelPlus();
	hotelPlus();
	guestPlus();
	guestPlus();
    golfPlus();
	golfPlus();
	golfPlus();
}

function bookingInfo(key){
	window.open("<?=_A_PATH_BOOKING_VIEW_POPUP?>?key="+key, "overlap_"+key, "width=900,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
function bookingWented(key){
	window.open("<?=_A_PATH_BOOKING_WENTED_POPUP?>?key="+key, "overlap_"+key, "width=900,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no");
}


function bookingModify(){

	if($("#bkp_type").val() == ''){
		alert('Please select tour type.');
		return false;
	}
	if($("#bkp_area").val() == ''){
		alert('Please select an area.');
		return false;
	}




	var send_array = Array();
	var send_cnt = 0;
	var chkbox = $(".checkSelect");

	for(i=0;i<chkbox.length;i++) {
		if (chkbox[i].checked == true){
			send_array[send_cnt] = 'Y';
			send_cnt++;
		}else{
			send_array[send_cnt] = 'N';
			send_cnt++;
		}
	}
	var form = document.form1;
	form.bkp_similan_ck.value= send_array;
	form.submit();
}

//--> 
</script> 

<?
include "../layout/footer_popup.php";
exit;
?>