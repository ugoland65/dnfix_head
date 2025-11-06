<?
include "../lib/inc_common.php";
	$pageGroup = "booking";
	$pageName = "plan_template_req";
	
	
	$_mode = securityVal($mode);
	$_tp_idx = securityVal($key);

	if( $_mode == "modify" ){
		$paln_template_data = wepix_fetch_array(wepix_query_error("select * from "._DB_TRAVEL_PLAN_TEMPLATE." where TP_IDX = '".$_tp_idx."' "));
		$page_title_text = "확정서 수정";
		$submit_btn_text = "확정서 수정";

		$paln_template_goods_query ="select * from "._DB_TRAVEL_PLAN_TEMPLATE_GOODS." where TPG_TP_CODE = '".$paln_template_data[TP_CODE]."' ";
		$paln_template_goods_result = wepix_query_error($paln_template_goods_query);

	}else{
		$page_title_text = "확정서 등록";
		$submit_btn_text = "확정서 등록";
	}
 		
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
		+"<tr>"
		+"<th rowspan='2'>"+ scheduleCount +"일</th>"
		+"<td class='tds2'><textarea name='tpg_area[]'></textarea></td>"
		+"<td class='tds2'><textarea name='tpg_traffic[]'></textarea></td>"
		+"<td class='tds2'><textarea name='tpg_paln_text[]'></textarea></td>"
		+"</tr>"
		+"<tr>"
		+"<td class='tds2' colspan='3'>"
		+"호텔:<input style='width:150px;' type='text' name='tpg_hotel[]'>"
		+"조식:<input style='width:150px;' type='text' name='tpg_food1[]'>"
		+"중식:<input style='width:150px;' type='text' name='tpg_food2[]'>"
		+"저녁:<input style='width:150px;' type='text' name='tpg_food3[]'>"
		+"<button type='button' class='btnstyle1 btnstyle1-info btnstyle1-sm' style='margin-left:3px;' onclick='pdChoice(" + scheduleCount + ");' > <i class='fas fa-plus-circle'></i> 일정 추가</button>"
		+"<input type='text' readonly style='width:150px;' name='tpg_pd_key_"+scheduleCount+"' id='tpg_pd_key_"+scheduleCount+"'>"
		+"</td>"
		+"</tr>";

	$("#plusSchedule_table").append(showHtml);
};
  
</script>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div class="table-wrap">
			<form name='form1' action='<?=_A_PATH_TRAVEL_PLAN_OK?>' method='post'>
			<?if( $_mode == "modify" ){?>
			 <input type='hidden' name='key' value='<?= $_tp_idx ?>'>
			 <input type='hidden' name='_tp_code' value='<?=$paln_template_data[TP_CODE]?>'>
			 <input type='hidden' name='action_mode' value='modifyTravelPlanTempalte'>
			<?}else{?>
			 <input type='hidden' name='action_mode' value='newTravelPlanTempalte'>
			<?}?>
                <table style='width:100%;' cellspacing="1" cellpadding="0" class="table-style">
                  <tr>
                    <th style='width:150px;'>확정서명</th>
                    <td class="tds2"><input type='text' name='tp_title' value='<?=$paln_template_data[TP_TITLE]?>'></td>
                  </tr>
				  <tr>
                    <th style='width:150px;'>요금</th>
                    <td class="tds2"><textarea name='tp_price_text'><?=$paln_template_data[TP_PRICE_TEXT]?></textarea></td>
                  </tr>
                  <tr>
                    <th style='width:150px;'>포함사항</th>
                    <td class="tds2"><textarea name='tp_inclusion'><?=$paln_template_data[TP_INCLUSION]?></textarea></td>
                  </tr>
                  <tr>
                    <th style='width:150px;'>불포함사항</th>
                    <td class="tds2"><textarea name='tp_not_inclusion'><?=$paln_template_data[TP_NOT_INCLUSION]?></textarea></td>
                  </tr>
                  <tr>
                    <th style='width:150px;'>호텔연락처</th>
                    <td class="tds2"><input type='text' style='width:300px;' name='tp_hotel_contact' value='<?=$paln_template_data[TP_HOTEL_CONTACT]?>'></td>
                  </tr>
                  <tr>
                    <th style='width:150px;'>현지연락처</th>
                    <td class="tds2"><textarea name='tp_local_contact' style='width:300px;'><?=$paln_template_data[TP_LOCAL_CONTACT]?></textarea></td>
                  </tr>
                  <tr>
                    <th style='width:150px;'>공항미팅</th>
                    <td class="tds2"><textarea name='tp_meeting' style='width:300px;'><?=$paln_template_data[TP_MEETING]?></textarea></td>
                  </tr> 
                  <tr>
                    <th style='width:150px;'>기타안내사항</th>
                    <td class="tds2"> <textarea name='tp_memo' style='width:300px;'><?=$paln_template_data[TP_MEMO]?></textarea></td>
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
                   <tr>
                     <th rowspan='2'>1 일</th>
                     <td class='tds2'><textarea name='tpg_area[]'></textarea></td>
                     <td class='tds2'><textarea  name='tpg_traffic[]'></textarea></td>
                     <td class='tds2'><textarea  name='tpg_paln_text[]'></textarea></td>
                   </tr>
                   <tr>
                     <td class='tds2' colspan='3'>
                        호텔:<input style='width:150px;' type='text' name='tpg_hotel[]'> 
                        조식:<input style='width:150px;' type='text' name='tpg_food1[]'>
                        중식:<input style='width:150px;' type='text' name='tpg_food2[]'> 
                        저녁:<input style='width:150px;' type='text' name='tpg_food3[]'>
						<button type='button' class='btnstyle1 btnstyle1-info btnstyle1-sm' onclick="pdChoice(1);" > <i class='fas fa-plus-circle'></i> 일정 추가</button>
						<input type='text' readonly style='width:150px;' name='tpg_pd_key_1' id='tpg_pd_key_1'>
					</td>
                   </tr>
<?}else{?>
				   <?
					 $num = 1;
					 while($tpg_list =  wepix_fetch_array($paln_template_goods_result)){
						  
						   $tpg_food = explode("/",$tpg_list[TPG_FOOD]);	

				   ?>
				   <tr>
                     <th rowspan='2'><?=$tpg_list[TPG_DAY_NUM]?> 일</th>
                     <td class='tds2'><textarea name='tpg_area[]'><?=$tpg_list[TPG_AREA]?></textarea></td>
                     <td class='tds2'><textarea  name='tpg_traffic[]'><?=$tpg_list[TPG_TRAFFIC]?></textarea></td>
                     <td class='tds2'><textarea  name='tpg_paln_text[]'><?=$tpg_list[TPG_PLAN_TEXT]?></textarea></td>
                   </tr>
                   <tr>
                     <td class='tds2' colspan='3'>
                        호텔:<input style='width:150px;' type='text' name='tpg_hotel[]' value='<?=$tpg_list[TPG_HOTEL]?>'> 
                        조식:<input style='width:150px;' type='text' name='tpg_food1[]' value='<?=$tpg_food[0]?>'>
                        중식:<input style='width:150px;' type='text' name='tpg_food2[]' value='<?=$tpg_food[1]?>' > 
                        저녁:<input style='width:150px;' type='text' name='tpg_food3[]' value='<?=$tpg_food[2]?>'>
						
						<button type='button' class='btnstyle1 btnstyle1-info btnstyle1-sm' onclick="pdChoice('<?=$num?>');" > <i class='fas fa-plus-circle'></i> 일정 추가</button>
						<input type='text' readonly style='width:150px;' name='tpg_pd_key_<?=$num?>' id='tpg_pd_key_<?=$num?>' value='<?=$tpg_list[TPG_PD_KEY]?>'>
					</td>
                   </tr>
				   <?
					   $num++;
				   }?>
<?}?>
                </table>

            </form>

			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_MEMBER_G_LIST?>'" > 
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