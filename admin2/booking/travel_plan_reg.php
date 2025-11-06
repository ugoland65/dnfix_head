<?
$pageGroup = "booking";
$pageName = "travel_report_reg";
include "../lib/inc_common.php";

	
	
	$_bkp_idx = securityVal($bkp_key);
	$tr_data = wepix_fetch_array(wepix_query_error("select *  from  "._DB_TRAVEL_PLAN." where TR_BKP_IDX = ".$_bkp_idx));
	

	if($tr_data[TR_IDX]){
		$_tr_key =  $tr_data[TR_IDX];
		
		$_view_tr_code = $tr_data[TR_TP_CODE]; //확정서 제목
		$_view_tr_title = $tr_data[TR_TITLE]; //확정서 제목
		$_view_tr_price_text = $tr_data[TR_PRICE_TEXT]; //확정서 가격 텍스트란
		$_view_tr_inclusion = $tr_data[TR_INCLUSION]; //확정서 포함사항
		$_view_tr_not_inclusion = $tr_data[TR_NOT_INCLUSION]; //확정서 불포함사항
		$_view_tr_hotel_contact = $tr_data[TR_HOTEL_CONTACT]; //확정서 호텔 연락처
		$_view_tr_lacal_contact = $tr_data[TR_LACAL_CONTACT]; //확정서 현지 연락처
		$_view_tr_meeting = $tr_data[TR_MEETING]; //확정서 미팅
		$_view_tr_memo = $tr_data[TR_MEMO]; //확정서 메모
		$_ary_tr_day_num = explode("│",$tr_data[TR_DAY_NUM]);  //확정서 일정 일차

		$_ary_tr_area = explode("│",$tr_data[TR_AREA]); //확정서 일정 지역
		$_ary_tr_traffic = explode("│",$tr_data[TR_TRAFFIC]);//확정서 일정 교통편
		$_ary_tr_time = explode("│",$tr_data[TR_TIME]);  //확정서 일정 시간
		$_ary_tr_plan_text = explode("│",$tr_data[TR_PLAN_TEXT]); //확정서 일정 설명
		$_ary_tr_hotel = explode("│",$tr_data[TR_HOTEL]); //확정서 일정 호텔
		$_ary_tr_food = explode("│",$tr_data[TR_FOOD]);  //확정서 일정 식사
		$_ary_tr_pd_key = explode("│",$tr_data[TR_PD_KEY]);  //확정서 일정 상품
		$_view_tr_reg_id = $tr_data[TR_REG_ID]; //확정서 작성자
		$_view_tr_reg_date = date("y-m-d",$tr_data[TR_REG_DATE]); //확정서 작성일자
		$_view_tr_mod_id = $tr_data[TR_MOD_ID]; //확정서 수정자
		$_view_tr_mod_date = date("y-m-d",$tr_data[TR_MOD_DATE]); //확정서 수정일자

		$page_title_text = "확정서 수정";
		$submit_btn_text = "확정서 수정";
	    $_mode = "modify";
	}else{
		$page_title_text = "확정서 등록";
		$submit_btn_text = "확정서 등록";
		 $_mode = "new";
	}


		
	

	$tp_result = wepix_query_error("select TP_IDX,TP_CODE,TP_TITLE from "._DB_TRAVEL_PLAN_TEMPLATE);


include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-style{ width:100% !important; }
.btn-wrap-center{ margin:10px 0 5px; text-align:center; }
.btn-wrap{ margin:15px 0 5px; text-align:right; }
</STYLE>

<script type='text/javascript'>

function doTemplateSubmit(){
	var form = document.form1;
	form.submit();
}


function pdChoice(num) { 

	$.ajax({
		type: "post",
		url : "<?=_A_PATH_PRODUCT_CHOICE?>",
		data : {"num":num},
		success: function(getdata) {
			$("#popup_layer_body").html(getdata);
			showPopup('1000', '600', 'ajax');
	   }
	});
}


function pdSelctFinal(num){
	
	var mpsPd = $("#tpg_pd_key_"+num).val();
	var finalPdKeyArray = finalPdKeyCheck.join("/");

	if(mpsPd != ""){
		mpsPd += "/"+finalPdKeyArray;
	}else{
		mpsPd += finalPdKeyArray;
	}
	
	
	//alert(finalPdKeyArray);
	$("#tpg_pd_key_"+num).val(mpsPd);
	closedPopup();

	
}


var scheduleCount = 1;

function plusSchedule(){
	scheduleCount++;
	var showHtml = ""
		+"<tr id='tr_goods_"+scheduleCount+"'>"
		+"<th rowspan='2'>"+ scheduleCount +"일</th>"
		+"<td class='tds2'><textarea name='tpg_area[]' id='tpg_area_"+scheduleCount+"'></textarea></td>"
		+"<td class='tds2'><textarea name='tpg_traffic[]' id='tpg_traffic_"+scheduleCount+"'></textarea></td>"
		+"<td class='tds2'><textarea name='tpg_paln_text[]' id='tpg_paln_text_"+scheduleCount+"'></textarea></td>"
		+"</tr>"
		+"<tr id='tr_goods_"+scheduleCount+"'>"
		+"<td class='tds2' colspan='3'>"
		+"호텔:<input style='width:150px;' type='text' name='tpg_hotel[]' id='tpg_hotel_"+scheduleCount+"'>"
		+"조식:<input style='width:150px;' type='text' name='tpg_food1[]' id='tpg_food1_"+scheduleCount+"'>"
		+"중식:<input style='width:150px;' type='text' name='tpg_food2[]' id='tpg_food2_"+scheduleCount+"'>"
		+"저녁:<input style='width:150px;' type='text' name='tpg_food3[]' id='tpg_food3_"+scheduleCount+"'>"
		+"<button type='button' class='btnstyle1 btnstyle1-info btnstyle1-sm' style='margin-left:3px;' onclick='pdChoice(" + scheduleCount + ");' > <i class='fas fa-plus-circle'></i> 일정 추가</button>"
		+"<input type='text' readonly style='width:150px;' name='tpg_pd_key_"+scheduleCount+"' id='tpg_pd_key_"+scheduleCount+"'>"
		+"</td>"
		+"</tr>";

	$("#plusSchedule_table").append(showHtml);
};
 
function openPalnTemplate(){

	if(!confirm("템플릿을 변경하시면 기존의 내용은 모두 삭제됩니다.")){
		return false;
	}

	var key = $("#paln_template option:selected").val();
	
	var _key = key.split(',');
    $('#tp_code').val(_key[1]);

	scheduleCount = 1;
	if(key != 0){
		$.ajax({
			url: "travel_plan_ok.php",
			data: {
				"action_mode":"openPalnTemplate",
				"key":_key[0]
			},
			type: "POST",
			dataType: "text",
			success: function(data){

				
				getData = data.split('@#$#@');
				trData = getData[0].split('#*#');
				trgData = getData[1].split('#*#');
				$('#tp_title').val(trData[0]);
				$('#tp_price_text').val(trData[1]);
				$('#tp_inclusion').val(trData[2]);
				$('#tp_not_inclusion').val(trData[3]);
				$('#tp_hotel_contact').val(trData[4]);
				$('#tp_local_contact').val(trData[5]);
				$('#tp_meeting').val(trData[6]);
				$('#tp_memo').val(trData[7]);

				var trg_day =  trgData[1].split('│');
				var trg_area =  trgData[2].split('│');
				var trg_traffic =  trgData[3].split('│');
				var trg_time =  trgData[4].split('│');
				var trg_plan_text =  trgData[5].split('│');
				var trg_hotel =  trgData[6].split('│');
				var trg_food =  trgData[7].split('│');
				var trg_pd_key =  trgData[8].split('│');

			
			
				for(var i=0;i<trgData[0];i++){
					var id_num = i+1;

					if($("#tr_goods_"+id_num).length > 0){
					}else{
						plusSchedule();
					}
					var trg_food_array =  trg_food[i].split('/');
					$('#tpg_area_'+id_num).val(trg_area[i]);
					$('#tpg_traffic_'+id_num).val(trg_traffic[i]);
					$('#tpg_paln_text_'+id_num).val(trg_plan_text[i]);
					$('#tpg_hotel_'+id_num).val(trg_hotel[i]);
					$('#tpg_food1_'+id_num).val(trg_food_array[0]);
					$('#tpg_food2_'+id_num).val(trg_food_array[1]);
					$('#tpg_food3_'+id_num).val(trg_food_array[2]);
					$('#tpg_pd_key_'+id_num).val(trg_pd_key[i]);
				}
			},
			error: function(){
				//에러
			}
		});
	}else{
		$('#tp_title').val('');
		$('#tp_price_text').val('');
		$('#tp_inclusion').val('');
		$('#tp_not_inclusion').val('');
		$('#tp_hotel_contact').val('');
		$('#tp_local_contact').val('');
		$('#tp_meeting').val('');
		$('#tp_memo').val('');
		for(var i=0;i<15;i++){
			var id_num = i+1;
			$('#tpg_area_'+id_num).val('');
			$('#tpg_traffic_'+id_num).val('');
			$('#tpg_paln_text_'+id_num).val('');
			$('#tpg_hotel_'+id_num).val('');
			$('#tpg_food1_'+id_num).val('');
			$('#tpg_food2_'+id_num).val('');
			$('#tpg_food3_'+id_num).val('');
			$('#tpg_pd_key_'+id_num).val('');
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
			<form name='form1' action='<?=_A_PATH_TRAVEL_PLAN_OK?>' method='post'>
			 
			 <input type='hidden' name='bkp_key' value='<?= $_bkp_idx ?>'>
			 <input type='hidden' name='tr_key' value='<?= $_tr_key ?>'>
			 <input type='hidden' name='tp_code' id='tp_code' value='<?=$_view_tr_code?>'>

			<?if( $_mode == "modify" ){?>
			 <input type='hidden' name='action_mode' value='modifyTravelPlan'>
			<?}else{?>
			 <input type='hidden' name='action_mode' value='newTravelPlan'>
			<?}?>
                <table style='width:100%;' cellspacing="1" cellpadding="0" class="table-style">
				  <tr>
					<th>확정서 샘플 가져오기</th>
					<td class="tds2">
					<select name='paln_template' id='paln_template' onchange="openPalnTemplate()">
						<option value='0'>직접입력</option>
					<?while($pt_list = wepix_fetch_array($tp_result)){?>
						<option value='<?=$pt_list[TP_IDX].",".$pt_list[TP_CODE]?>'><?=$pt_list[TP_TITLE]?></option>
					<?}?>
					</select>
					</td>
				  </tr>
                  <tr>
                    <th style='width:150px;'>확정서명</th>
                    <td class="tds2"><input type='text' name='tp_title' id='tp_title' value='<?=$_view_tr_title ?>'></td>
                  </tr>
				  <tr>
                    <th style='width:150px;'>요금</th>
                    <td class="tds2"><textarea name='tp_price_text' id='tp_price_text'><?=$_view_tr_price_text?></textarea></td>
                  </tr>
                  <tr>
                    <th style='width:150px;'>포함사항</th>
                    <td class="tds2"><textarea name='tp_inclusion' id='tp_inclusion'><?=$_view_tr_inclusion ?></textarea></td>
                  </tr>
                  <tr>
                    <th style='width:150px;'>불포함사항</th>
                    <td class="tds2"><textarea name='tp_not_inclusion' id='tp_not_inclusion'><?=$_view_tr_not_inclusion ?></textarea></td>
                  </tr>
                  <tr>
                    <th style='width:150px;'>호텔연락처</th>
                    <td class="tds2"><input type='text' style='width:300px;' name='tp_hotel_contact' id='tp_hotel_contact' value='<?=$_view_tr_hotel_contact ?>'></td>
                  </tr>
                  <tr>
                    <th style='width:150px;'>현지연락처</th>
                    <td class="tds2"><textarea name='tp_local_contact' id='tp_local_contact' style='width:300px;'><?=$_view_tr_lacal_contact ?></textarea></td>
                  </tr>
                  <tr>
                    <th style='width:150px;'>공항미팅</th>
                    <td class="tds2"><textarea name='tp_meeting' id='tp_meeting' style='width:300px;'><?=$_view_tr_meeting ?></textarea></td>
                  </tr> 
                  <tr>
                    <th style='width:150px;'>기타안내사항</th>
                    <td class="tds2"> <textarea name='tp_memo' id='tp_memo' style='width:300px;'><?=$_view_tr_memo ?></textarea></td>
                  </tr>
				</table>

              
                <table style='width:100%;' cellspacing="1" cellpadding="0" class="table-style" id='plusSchedule_table' name='plusSchedule_table'>
				<div class="option-btn">
						<button type='button' class='btnstyle1 btnstyle1-success btnstyle1-sm' style='width:150px; margin-left:86%' onclick='plusSchedule()'> <i class='fas fa-plus-circle'></i>
						일정추가
						</button>
				</div>
                   <tr>
                     <th>일자</th>
                     <th style='width:150px;'>지역</th>
                     <th style='width:150px;'>교통</th>
                     <th>일정내역</th>
                   </tr>
<?
if( $_mode != "modify" ){
?>
                   <tr id='tr_goods_1'>
                     <th rowspan='2'>1 일</th>
                     <td class='tds2'><textarea name='tpg_area[]' id='tpg_area_1'></textarea></td>
                     <td class='tds2'><textarea  name='tpg_traffic[]' id='tpg_traffic_1'></textarea></td>
                     <td class='tds2'><textarea  name='tpg_paln_text[]' id='tpg_paln_text_1'></textarea></td>
                   </tr>
                   <tr id='tr_goods_1'>
                     <td class='tds2' colspan='3'>
                        호텔:<input style='width:150px;' type='text' name='tpg_hotel[]' id='tpg_hotel_1'> 
                        조식:<input style='width:150px;' type='text' name='tpg_food1[]' id='tpg_food1_1'>
                        중식:<input style='width:150px;' type='text' name='tpg_food2[]' id='tpg_food2_1'> 
                        저녁:<input style='width:150px;' type='text' name='tpg_food3[]' id='tpg_food3_1'>
						<button type='button' class='btnstyle1 btnstyle1-info btnstyle1-sm' onclick="pdChoice(1);" > <i class='fas fa-plus-circle'></i> 일정 추가</button>
						<input type='text' readonly style='width:150px;' name='tpg_pd_key_1' id='tpg_pd_key_1'>
					</td>
                   </tr>
<?}else{?>
				   <?
					
					for($i=0;$i<count($_ary_tr_day_num);$i++){
						   $num = $i+1;
						   $tpg_food = explode("/",$_ary_tr_food[$i]);	

				   ?>
				   <tr id='tr_goods_<?=$num?>'>
                     <th rowspan='2'><?=$_ary_tr_day_num[$i]?> 일</th>
                     <td class='tds2'><textarea name='tpg_area[]' id='tpg_area_<?=$num?>'><?=$_ary_tr_area[$i]?></textarea></td>
                     <td class='tds2'><textarea  name='tpg_traffic[]' id='tpg_traffic_<?=$num?>'><?=$_ary_tr_traffic[$i]?></textarea></td>
                     <td class='tds2'><textarea  name='tpg_paln_text[]' id='tpg_paln_text_<?=$num?>'><?=$_ary_tr_plan_text[$i]?></textarea></td>
                   </tr>
                   <tr id='tr_goods_<?=$num?>'>
                     <td class='tds2' colspan='3'>
                        호텔:<input style='width:150px;' type='text' name='tpg_hotel[]' id='tpg_hotel_<?=$num?>' value='<?=$_ary_tr_hotel[$i]?>'> 
                        조식:<input style='width:150px;' type='text' name='tpg_food1[]' id='tpg_food1_<?=$num?>' value='<?=$tpg_food[0]?>'>
                        중식:<input style='width:150px;' type='text' name='tpg_food2[]' id='tpg_food2_<?=$num?>' value='<?=$tpg_food[1]?>' > 
                        저녁:<input style='width:150px;' type='text' name='tpg_food3[]' id='tpg_food3_<?=$num?>' value='<?=$tpg_food[2]?>'>
						<button type='button' class='btnstyle1 btnstyle1-info btnstyle1-sm' onclick="pdChoice('<?=$num?>');" > <i class='fas fa-plus-circle'></i> 일정 추가</button>
						<input type='text' readonly style='width:150px;' name='tpg_pd_key_<?=$num?>' id='tpg_pd_key_<?=$num?>' value='<?=$_ary_tr_pd_key[$i]?>'>
					</td>
                   </tr>
				   <?}?>
<?}?>
                </table>

            </form>

			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_BOOKING_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>

				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doTemplateSubmit();" > 
						<i class="far fa-check-circle"></i>
						<?=$submit_btn_text?>
					</button>
				</ul>
			</div>


			
		</div>
	</div>
</div>


<?
include "../layout/footer.php";
?>