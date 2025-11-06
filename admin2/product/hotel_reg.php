<?
include "../lib/inc_common.php";

$pageGroup = "product";
$pageName = "hotel_reg";


 if($mode == 'new'){
	$page_title_text = "호텔 등록";
	$submit_btn_text = "호텔 등록";
 }elseif($mode == 'modify'){
	 $hotel_data = wepix_fetch_array(wepix_query_error("select * from "._DB_HOTEL." where HOT_IDX = '".$key."' "));
     $room_type_result = wepix_query_error("select ROC_IDX,ROC_NAME,ROC_FULL_NAME  from "._DB_HOTEL_ROOM_TYPE." where ROC_HOT_IDX = '".$hotel_data[HOT_IDX]."' ");
	 $page_title_text = "호텔 수정";
	 $submit_btn_text = "호텔 수정";
}
 
include "../layout/header.php";
?>
<div id="contents_head">
	<h1><?=$title_hotel_req?></h1>
</div>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
.guide-profile-wrap{ width:100%; height:150px; margin-bottom:10px; }
.guide-profile{ width:150px; height:150px; border:1px solid #9096a3; border-radius:10px;  }
</STYLE>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div class="table-wrap">
			<form name='form1' action='hotel_ok.php' method='post'>
			 <input type='hidden' name='action_mode' value='<?= $mode ?>'>
			 <input type='hidden' name='mokey' value='<?= $key ?>'>
				<table cellspacing="1" cellpadding="0" class="table-style" id=''>

				 <tr class="tds1">
				  <th>지역</th>

				  <td class="tds2">
				  <select name='hotelarea' id='hotelarea'>
						<option value="">=지역추가=</option>
						<?
						$area_query = "select * from ".$db_t_AREA." where AREA_KIND = 'L' order by AREA_IDX asc";
						$area_result = wepix_query_error($area_query);
						while($area_list = wepix_fetch_array($area_result)){
						?>
							<option value="<?=$area_list[AREA_CODE]?>"  <? if( $hotel_data[HOT_AREA]== $area_list[AREA_CODE]  ) echo "selected"; ?> ><?=$area_list[AREA_CODE]?></option>
						<? } ?>
				    </select>
				  </td>

				 </tr>
				 <tr>
					 <th class="tds1">Hotel Full Name</th>
					 <td class="tds2"><input type='text' name='hot_full_name' id='hot_full_name' size='60' value="<?=$hotel_data[HOT_FULL_NAME]?>" ></td>
				 </tr>
				 <tr>
					 <th class="tds1">Hotel</th>
					 <td class="tds2"><input type='text' name='hotelName' id='hotelName' size='60' value="<?=$hotel_data[HOT_NAME]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">All Inclusive</th>
					 <td class="tds2"><input type='text' name='Inclusive' id='Inclusive' size='60' value="<?=$hotel_data[HOT_INCLUSIVE]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">All-In (Food)</th>
					 <td class="tds2"><input type='text' name='all_in_food_price' id='all_in_food_price' size='60' value="<?=$hotel_data[HOT_All_IN_FOOD_PRICE]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">All-In (Drink)</th>
					 <td class="tds2"><input type='text' name='all_in_drink_price' id='all_in_drink_price' size='60' value="<?=$hotel_data[HOT_All_IN_DRINK_PRICE]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">Full Board</th>
					 <td class="tds2"><input type='text' name='full_board_price' id='full_board_price' size='60' value="<?=$hotel_data[HOT_FUll_BOARD_PRICE]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">Half Board</th>
					 <td class="tds2"><input type='text' name='half_board_price' id='half_board_price' size='60' value="<?=$hotel_data[HOT_HALF_BOARD_PRICE]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">Extra Bed</th>
					 <td class="tds2"><input type='text' name='extra_bed_price' id='extra_bed_price' size='60' value="<?=$hotel_data[HOT_EXTRA_BED_PRICE]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">Extra Person</th>
					 <td class="tds2"><input type='text' name='extra_person_price' id='extra_person_price' size='60' value="<?=$hotel_data[HOT_EXTRA_PERSON_PRICE]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">Gala Dinner</th>
					 <td class="tds2"><input type='text' name='gala_dinner_price' id='gala_dinner_price' size='60' value="<?=$hotel_data[HOT_GALA_DINNER_PRICE]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">Late Check out (16.00)</th>
					 <td class="tds2"><input type='text' name='late_1600_price' id='late_1600_price' size='60' value="<?=$hotel_data[HOT_LATE_1600_PRICE]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">Late Check out (18.00)</th>
					 <td class="tds2"><input type='text' name='late_1800_price' id='late_1800_price' size='60' value="<?=$hotel_data[HOT_LATE_1800_PRICE]?>" ></td>
				 </tr>
                 <tr>
					 <th class="tds1">Late Check out (after 18.00)</th>
					 <td class="tds2"><input type='text' name='late_after_1800_price' id='late_after_1800_price' size='60' value="<?=$hotel_data[HOT_LATE_AFTER_1800_PRICE]?>" ></td>
				 </tr>

				 <tr>
				    <th class="tds1">Room type</th>
					<td class="tds2">
					<table cellspacing="1" cellpadding="0" class="table-style">
						 <? while($room_type_data =  wepix_fetch_array($room_type_result)){
							 $i++;
							 ?>
						   <input type='hidden' name='rmda[]' value='<?=$room_type_data[ROC_IDX]?>'>
						 <tr>
							<th class="tds1">Room type Full Name</th>
							<td class="tds2"><input type='text' name='room_full_name<?=$room_type_data[ROC_IDX]?>'  value='<?=$room_type_data[ROC_FULL_NAME]?>'>
							<th class="tds1">Room type </th>
							<td class="tds2"><input type='text' name='room_name_<?=$room_type_data[ROC_IDX]?>'  value='<?=$room_type_data[ROC_NAME]?>'>
							<input type='button' value='수정' onclick="javascript:DoRoomModify('<?=$room_type_data[ROC_IDX]?>');">
							<input type='button' value='삭제' onclick="javascript:DoRoomDel('<?=$room_type_data[ROC_IDX]?>');"></td>
						 </tr>
						<? } //while end?>
					</table>
					</td>
				 </tr>
				 <? 
				 if($mode == 'modify'){ ?>
				 <tr>
				     <th class="tds1">룸 타입</th>
					 <td class="tds2">
					 <div class="plusBtnWrap"><input type="button" value="룸 추가" class="plusBtn" onclick="roomPlus()"></div>
						<table cellspacing="1" cellpadding="0" class="table-style" id="roomPt">
							<tr>
								<th class="tds1">룸타입 풀 이름</th>
								<th class="tds1">룸타입 이름</th>
								<th class="tds1" style="width:30px;">삭제</th>
							</tr>
						</table>					 
					 </td>
				 </tr>
				 <?}?>


				 <tr>
				   <th class="tds1">노출</th>
				   <td class="tds2">
					<input type="radio" name="hotelView" value="Y" <? if( $hotel_data[HOT_VIEW]=="Y" OR !$hotel_data[HOT_VIEW] ) echo "checked"; ?> >노출
					<input type="radio" name="hotelView"  value="N" <? if( $hotel_data[HOT_VIEW]=="N") echo "checked"; ?>>비노출
				   </td>
				 </tr>
				</table>
		
			</form>
			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_PRODUCT_HOTEL_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doHotelSubmit();" > 
						<i class="far fa-check-circle"></i>
						<?=$submit_btn_text?>
					</button>
				</ul>
			</div>
		</div>
	</div>
	
</div>


<script type='text/javascript'>
			   function doHotelSubmit(){
				    var form = document.form1;
					form.submit();
				  };

			   function DoRoomDel(idx){
					
					var form = document.form1;
					form.action_mode.value = "room_Del";
                    form.action ="hotel_ok.php?key="+idx;
					form.submit();

			   }
		      function DoRoomModify(idx){
					
					var form = document.form1;
					form.action_mode.value = "room_modify";
                    form.action ="hotel_ok.php?key="+idx;
					form.submit();

			   }

               
                var rowcount_room = 1;
				var roomPlus=function(){
					rowcount_room++;
					var showHtml = ""
						+"<tr id='trid2_"+ rowcount_room +"' style='text-align:center;'>"
						+"<td><input type='text' name='room_full_name[]' ></td>"
						+"<td><input type='text' name='room_type[]' class='inputtext1'></td>"
						+"<td><input type=\"button\" \ onClick='roomDel("+ rowcount_room +")' value='삭제'></td>"
						+"</tr>";

					$("#roomPt").append(showHtml);
				};

				var roomDel = function(key){
					$('#trid2_'+key).remove();
				};


</script>
<?

include "../layout/footer.php";
exit;
?>