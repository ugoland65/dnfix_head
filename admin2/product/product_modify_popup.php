<?
$pageGroup = "product";
$pageName = "product_modify_popup";

include "../lib/inc_common.php";

	$_pd_idx = securityVal($key);
	$pd_data = wepix_fetch_array(wepix_query_error("select * from "._DB_PRODUCT_TRAVEL." where PD_IDX = '".$_pd_idx."' "));

	$pdc_id = $pd_data[PD_PDC_ID];

	$pdc_depth_0 = category_depth_code($pdc_id,'0');
	$pdc_depth_1 = category_depth_code($pdc_id,'1');
	$pdc_depth_2 = category_depth_code($pdc_id,'2');
	$pdc_depth_3 = category_depth_code($pdc_id,'3');

	$_view_pd_price_o = number_format($pd_data[PD_PRICE_O]);
	$_view_pd_cost_rate = $pd_data[PD_COST_RATE];
	$_view_pd_sale_price = number_format($pd_data[PD_SALE_PRICE]);

	$_view_pd_price_v = number_format($pd_data[PD_PRICE_V]);
	$_view_pd_price_v_child = number_format($pd_data[PD_PRICE_V_CHILD]);


	$_ary_pd_option = explode("│", $pd_data[PD_OPTION]);
	$_ary_pd_option_price_vm = explode("│", $pd_data[PD_OPTION_PRICE_VM]);
	$_ary_pd_option_price_free = explode("│", $pd_data[PD_OPTION_PRICE_FREE]);
	$_ary_pd_option_price_cost = explode("│", $pd_data[PD_OPTION_PRICE_COST]);
	$_ary_pd_option_price_vm_chlid = explode("│", $pd_data[PD_OPTION_PRICE_VM_CHILD]);
	$_ary_pd_option_price_free_chlid = explode("│", $pd_data[PD_OPTION_PRICE_FREE_CHILD]);
	$_ary_pd_option_price_cost_chlid = explode("│", $pd_data[PD_OPTION_PRICE_COST_CHILD]);
	$_ary_pd_option_cont = explode("&*&", $pd_data[PD_OPTION_CONT]);

	$_ary_pd_mobile_thum = explode(".", str_replace("../../uploads/product/","",$pd_data[PD_MOBILE_THUM]));
	$_view_pd_mobile_thum_s = "../../uploads/product/".$_ary_pd_mobile_thum[0]."_s.".$_ary_pd_mobile_thum[1];
	$_view_pd_mobile_thum = $pd_data[PD_MOBILE_THUM];

	$_ary_pd_mobile_sliding = explode("│", $pd_data[PD_MOBILE_SLIDING]);

include "../layout/header_popup.php";
?>
<STYLE TYPE="text/css">
.cate-select{ width:150px !important; }
.price{ width:100px !important; }
.price-s{ width:70px !important; }

.option-table tr td { padding:5px !important; }
.option-no-td{ width:40px !important; text-align:center; }
.option-price-td{ width:130px !important; text-align:center;  }
.option-cont-td{ width:300px !important;}
.option-del-td{ width:40px !important; text-align:center; }
.option-cont{ height:54px !important; }

.option-price-wrap{ display:table; width:100%;  }
.option-price-wrap ul{ display:table-row; }
.option-price-wrap ul li{ display:table-cell; }
.option-price-name{ font-size:11px; width:40px; text-align:right; box-sizing:border-box;  padding-right:4px; }

.option-btn{ margin-bottom:5px; text-align:right; }
</STYLE>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCct0tvS5JB5-N-14pwRgdyHDwQAjM79Tc&callback=initMap" async defer></script>
<script type='text/javascript'>
	var map;
	var markers = [];
    function initMap() {
	    // Create the map with no initial style specified.
        // It therefore has default styling.
		var myLatlng = {lat: 7.962797425283777, lng: 98.34307626686001};
        map = new google.maps.Map(document.getElementById('map'), {
          center: myLatlng,
          zoom: 13,
          mapTypeControl: false
        });
		
        // Add a style-selector control to the map.
        var styleControl = document.getElementById('style-selector-control');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(styleControl);

		map.addListener('click', function(event) {
			placeMarker(event.latLng);
        });
	}
	function placeMarker(location) {
			markers = [];
			var marker = new google.maps.Marker({
				position: location,
				map: map,
			});
			
			var infowindow = new google.maps.InfoWindow({
				content: 'Latitude: ' + location.lat() + '<br>Longitude: ' + location.lng()
				
			});
			$('#pd_x_gps').val(location.lat());
			$('#pd_y_gps').val(location.lng());
			
			deleteMarkers();
			markers.push();
	}

	function deleteMarkers() {
			clearMarkers();
			marker = [];
	}

	function clearMarkers() {
		setMapOnAll(null);
	}

	function setMapOnAll(map) {
		for (var i = 0; i < marker.length; i++) {
		marker[i].setMap(map);
		}
	}

   function goSave(){

			var form = document.form1;
			var depth_1 =  $("#pdc_depth_1 option:selected").val();
			var depth_2 =  $("#pdc_depth_2 option:selected").val();
			var depth_3 =  $("#pdc_depth_3 option:selected").val();
			var depth_4 =  $("#pdc_depth_4 option:selected").val();
			var depth_txt_1 =$("#pdc_depth_1 option:selected").text();
			var depth_txt_2 =$("#pdc_depth_2 option:selected").text();
			var depth_txt_3 =$("#pdc_depth_3 option:selected").text();
			var depth_txt_4 =$("#pdc_depth_4 option:selected").text();



			form.pd_id.value = depth_1;   

			form.pd_ctagory.value = "<span class ='big my'>"+ depth_txt_1 +"</span>";
			if(depth_2 != null  && depth_2 != ""){
				form.pd_id.value = depth_2;
				 form.pd_ctagory.value = "<span class ='big'>"+ depth_txt_1 +"</span> <span class='compare'> > </span><span class ='middle my'>"+ depth_txt_2 +"</span>";
			}
			if(depth_3 != null && depth_3 != ""){
				 form.pd_id.value = depth_3;   
				form.pd_ctagory.value = "<span class ='big'>"+ depth_txt_1 +"</span><span class='compare'> > </span> <span class ='middle'>"+ depth_txt_2 +"</span> <span class='compare'> > </span> <span class='small my'>"+ depth_txt_3 +"</span>";
			}
			if(depth_4 != null && depth_4 != ""){
				 form.pd_id.value = depth_4;   
				 
			}



              form.submit();
	   
};
</script>
<div id="wrap2">
    
    <div class="mFixNav fixed">
		<div class="info">
			<?=$pd_data[PD_NAME]?>
		</div>
        <ul class="nav">
			<li class="selected"><a href="#QA_detail1">기본정보</a></li>
			<li><a href="#QA_detail2">여행정보</a></li>
			<li><a href="#QA_detail3">노출정보</a></li>
			<li><a href="#QA_detail4">판매정보</a></li>
			<li><a href="#QA_detail5">이미지정보</a></li>
			<li><a href="#QA_detail6">상세설명</a></li>
			<li><a href="#QA_detail7">추가정보</a></li>
        </ul>
	</div>
	
    <form name='form1' method='post' action='<?=_A_PATH_PRODUCT_OK?>' enctype="multipart/form-data">
    <input type='hidden' name='pd_id' id='pd_id'>
	<input type='hidden' name='mokey' value='<?=$_pd_idx?>'>
    <input type='hidden' name='pd_ctagory'>
	<input type='hidden' name='a_mode' value='product_modify'>
	<input type='hidden' name='file_mobile_thum_text' id='file_mobile_thum_text' value="<?=$pd_data[PD_MOBILE_THUM]?>" >

	<div id="QA_detail1" class="section">
        <div class="section-title">
			<h2>기본 정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds11">상품명</th>
				<td class="tds33" colspan="3"><input type='text' name='pd_name' id='pd_name' size='60' value="<?=$pd_data[PD_NAME]?>" ></td>
			</tr>
			<tr>
				<th class="tds11">상품명 (영문)</th>
				<td class="tds33" colspan="3"><input type='text' name='pd_name_eg' id='pd_name_eg' size='60' value="<?=$pd_data[PD_NAME_EG]?>" ></td>
			</tr>
			<tr>
				<th class="tds11">고유코드</th>
				<td class="tds33" colspan="3"><input type='text' name='PD_SYSTEM_CODE' id='PD_SYSTEM_CODE'  value="<?=$pd_data[PD_SYSTEM_CODE]?>"></td>
			</tr>
		</table>
	</div>

	<div id="QA_detail2" class="section">
        <div class="section-title">
			<h2>여행 정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds11">상품 지역</th>
				<td class="tds33" colspan="3">
					<select name='pd_area'  id='pd_area' value=''>
						<option value=''>== 지역선택 ==</option>
						<?
						$area_query = "select * from "._DB_AREA." where AREA_NATION_ISO = 'TH' and AREA_KIND='L' and area_view='Y' order by AREA_NAME asc ";
						$area_result = wepix_query_error($area_query);
						while($area_list = wepix_fetch_array($area_result)){
						?>
						<option value="<?=$area_list[AREA_CODE]?>"  <? if( $pd_data[PD_AREA]== $area_list[AREA_CODE]  ) echo "selected"; ?> ><?=$area_list[AREA_NAME]?></option>
						<? } ?>
					</select>
				</td>
			</tr>
			<tr>
				<th class="tds11">투어 종류</th>
				<td class="tds33" colspan="3">
                    <select name='pd_kind2' id='pd_kind2'>
						<option value=''>== 투어 종류 선택 ==</option>
						<option value='ALL_NIGHT' <?if($pd_data[PD_KIND2] == 'ALL_NIGHT') echo 'selected'; ?>>종일</option>
						<option value='HALF_DAY' <?if($pd_data[PD_KIND2] == 'HALF_DAY') echo 'selected'; ?>>반일</option>
						<option value='TAXI' <?if($pd_data[PD_KIND2] == 'TAXI') echo 'selected'; ?>>택시 단독투어</option>
                    </select>
				</td>
			</tr>
			<tr>
				<th class="tds11">그룹 타입</th>
				<td class="tds33" colspan="3">
                    <select name='pd_kind3' id='pd_kind3'>
						<option value=''>== 그룹 타입 선택 ==</option>
						<option value='JOIN' <?if($pd_data[PD_KIND3] == 'JOIN') echo 'selected'; ?>>단체</option>
						<option value='SOLO' <?if($pd_data[PD_KIND3] == 'SOLO') echo 'selected'; ?>>단독</option>
					</select>
				</td>
			</tr>
		</table>
	</div>

	<div id="QA_detail3" class="section">
        <div class="section-title">
			<h2>노출 정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds11">분류</th>
				<td class="tds33" colspan="3">

					<select  name='pdc_depth_1' id='pdc_depth_1' class="cate-select" onchange="new_depth('1',this.value);">
						<option value="">=== 1차 분류 ===</option>
						<?
						/*
						$pdc_depth_1_query = "select PDC_NAME, PDC_ID, PDC_IDX from "._DB_PRODUCT_CATAGORY_TRAVEL." where PDC_DEPTH = '0' and PDC_NEW_KIND ='G' order by PDC_ID asc ";
						$pdc_depth_1_result = wepix_query_error($pdc_depth_1_query);
						while($pdc_depth_1_list = wepix_fetch_array($pdc_depth_1_result)){
						?>
						<option value="<?=$pdc_depth_1_list[PDC_ID]?>"  <? if( $pdc_depth_1_list[PDC_ID]== $pdc_depth_1 ) echo "selected"; ?>><?=$pdc_depth_1_list[PDC_NAME]?></option>
                        <? } 
						*/
						?>
					</select>
					<select name='pdc_depth_2' id="pdc_depth_2" class="cate-select" onchange="new_depth('2',this.value);" >
						<option value="">=== 2차 분류 ===</option>
					</select>
					<select name='pdc_depth_3' id="pdc_depth_3" class="cate-select" onchange="new_depth('3',this.value);">
						<option value="">=== 3차 분류 ===</option>
					</select>
					<select name='pdc_depth_4' id="pdc_depth_4" class="cate-select">
						<option value="">=== 4차 분류 ===</option>
					</select>
				</td>
			</tr>

			<tr>
				<th class="tds11">여행분류</th>
				<td class="tds33" colspan="3">
					<select name='pd_catagory2' id='pd_catagory2'>
						<option value=''>== 분류선택 ==</option>
						<?
						$pdc2_query = "select PDC_NAME, PDC_ID, PDC_IDX from "._DB_PRODUCT_CATAGORY_TRAVEL." where PDC_DEPTH = '0' and PDC_NEW_KIND ='U' order by PDC_ID asc ";
						$pdc2_result = wepix_query_error($pdc2_query);
						while($pdc2_list = wepix_fetch_array($pdc2_result)){
						?>
							<option value='<?=$pdc2_list[PDC_ID]?>' <?if($pd_data[PD_CATAGORY2] == $pdc2_list[PDC_ID]) echo 'selected'; ?>><?=$pdc2_list[PDC_NAME]?></option>
						<? } ?>
					</select>
				</td>
			</tr>
			<tr>
				<th class="tds11">정산분류</th>
				<td class="tds33" colspan="3">
<? /* ?>
					<select name='pdKind' id='pdKind'>
						<option value=''>== 분류선택 ==</option>
						<option value='TOUR' selected>TOUR</option>
						<option value='FOOD' <?if($pd_data[PD_KIND] == 'FOOD') echo 'selected'; ?>>FOOD</option>
						<option value='OTHRE_PAY' <?if($pd_data[PD_KIND] == 'OTHRE_PAY') echo 'selected'; ?>>OTHRE PAY</option>
					</select>
<? */ ?>
				</td>
			</tr>
			<tr>
				<th class="tds11">관리 노출</th>
				<td class="tds33" colspan="3">
					<label><input type="radio" name="pd_view" value="Y" <? if( $pd_data[PD_VIEW]=="Y" OR !$pd_data[PD_VIEW] ) echo "checked"; ?> > 노출</label>
					<label><input type="radio" name="pd_view"  value="N" <? if( $pd_data[PD_VIEW]=="N") echo "checked"; ?>> 비노출</label>
				</td>
			</tr>
			<tr>
				<th class="tds11">판매 노출</th>
				<td class="tds33" colspan="3">
					<label><input type="radio" name="pd_use_yn" checked value="Y" <? if( $pd_data[PD_USE_YN]=="Y" ) echo "checked"; ?> > 노출</label>
					<label><input type="radio" name="pd_use_yn"  value="N" <? if( $pd_data[PD_USE_YN]=="N") echo "checked"; ?>> 비노출</label>
				</td>
			</tr>
		</table>
	</div>

	<div id="QA_detail4" class="section">
        <div class="section-title">
			<h2>판매 정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
				<tr>
                    <th>정가</th>
                    <td><input type='text' name='pd_price_o' value="<?= $_view_pd_price_o?>" style="width:100px;" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></td>

                </tr>
				<tr>
                    <th>원가 정산율</th>
                    <td><input type='text' name='pd_cost_rate' value="<?= $_view_pd_cost_rate?>" style="width:35px;"> %</td>
                </tr>
                <tr>
                    <th>판매가 (V.M)</th>
                    <td>
                        성인 : <input type='text' name='pd_price_vm' value="<?= number_format($pd_data[PD_PRICE_VM])?>" style="width:100px;" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
                        아동 : <input type='text' name='pd_price_vm_child' value="<?= number_format($pd_data[PD_PRICE_VM_CHILD])?>" style="width:100px;" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
                    </td>
                </tr>
                <tr>
                    <th>판매가 (FREE)</th>
                    <td>
                        성인 : <input type='text' name='pd_price_free' value="<?= number_format($pd_data[PD_PRICE_FREE])?>" style="width:100px;" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
                        아동 : <input type='text' name='pd_price_free_child' value="<?= number_format($pd_data[PD_PRICE_PREE_CHILD])?>" style="width:100px;" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
                    </td>
                </tr>
                <tr>
                    <th>판매원가</th>
                    <td>
                        성인 : <input type='text' name='pd_cost_price' value="<?= number_format($pd_data[PD_COST_PRICE])?>" style="width:100px;" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
                        아동 : <input type='text' name='pd_cost_price_child' value="<?= number_format($pd_data[PD_COST_PRICE_CHILD])?>" style="width:100px;" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">
                    </td>
                </tr>
            <tr>
				<th class="tds11">상품 옵션</th>
				<td class="tds33" colspan="3">
					<label><input type="radio" name="pd_option_yn" onchange="optionyn('Y')" value="Y"  <?if($pd_data[PD_OPTION_YN] == 'Y'){ echo "checked";}?>> 있음</label>
					<label><input type="radio" name="pd_option_yn" onchange="optionyn('N')" value="N" <?if($pd_data[PD_OPTION_YN] == 'N' || $pd_data[PD_OPTION_YN] == ''){ echo "checked";}?>>없음</label>
				</td>
			</tr>
			<tr>
				<th class="tds11">상품 옵션 내용</th>
				<td class="tds33" colspan="3">
					<div class="option-btn">
						<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:150px" onclick="optionAdd()"> <i class="fas fa-plus-circle"></i> 옵션 추가</button>
					</div>
					<table id='pd_option_table' name='pd_option_table' class="table-style option-table">
						<tr>
							<th class="option-no-td">순번</th>
							<th class="option-name-td">옵션이름</th>
							<th class="option-price-td">옵션가격(성인/아동)</th>
							<th class="option-cont-td">옵션설명</th>
							<th class="option-del-td">삭제</th>
						</tr>
<?
for($i=0; $i<count($_ary_pd_option); $i++){

	$_view2_option_name = $_ary_pd_option[$i];
	$_view2_option_price = number_format($_ary_pd_option_price[$i]);
	$_view2_option_price_vm = number_format($_ary_pd_option_price_vm[$i]);
	$_view2_option_price_free = number_format($_ary_pd_option_price_free[$i]);
	$_view2_option_price_cost = number_format($_ary_pd_option_price_cost[$i]);
	$_view2_option_price_vm_chlid = number_format($_ary_pd_option_price_vm_chlid[$i]);
	$_view2_option_price_free_chlid = number_format($_ary_pd_option_price_free_chlid[$i]);
	$_view2_option_price_cost_chlid = number_format($_ary_pd_option_price_cost_chlid[$i]);
	$_view2_option_cont = $_ary_pd_option_cont[$i];
	$_inst2_no = $i + 1;

?>
<tr id="option_tr_<?=$_inst2_no?>">
	<td class="option-no-td"><?=$_inst2_no?></td>
	<td class="option-name-td"><input type='text' name='option_name[]' value='<?=$_view2_option_name?>'></td>
	<td class="option-price-td">
		<div class="option-price-wrap">
			<ul>
				<li class="option-price-name">VM</li>
				<li><input type='text' name='option_price[]' value='<?=$_view2_option_price_vm?>' class="price-s" onkeyUP="is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></li>
                <li><input type='text' name='option_price_child[]' value='<?=$_view2_option_price_vm_chlid?>' class="price-s" onkeyUP="is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></li>
			</ul>
			<ul>
				<li class="option-price-name" style="padding-top:4px !important;">FREE</li>
				<li style="padding-top:4px !important;"><input type='text' name='option_price_free[]' value='<?=$_view2_option_price_free?>' class="price-s" onkeyUP="is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></li>
                <li style="padding-top:4px !important;"><input type='text' name='option_price_free_child[]' value='<?=$_view2_option_price_free_chlid?>' class="price-s" onkeyUP="is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></li>
			</ul>
            <ul>
				<li class="option-price-name" style="padding-top:4px !important;">원가</li>
				<li style="padding-top:4px !important;"><input type='text' name='option_price_cost[]' value='<?=$_view2_option_price_cost?>' class="price-s" onkeyUP="is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></li>
                <li style="padding-top:4px !important;"><input type='text' name='option_price_cost_child[]' value='<?=$_view2_option_price_cost_chlid?>' class="price-s" onkeyUP="is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"></li>
			</ul>
		</div>
	</td>
	<td class="option-cont-td"><textarea name='option_cont[]' class="option-cont" ><?=$_view2_option_cont?></textarea></td>
	<td class="option-del-td"><button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-sm" style="height:40px; " onclick="optionDel('<?=$_inst2_no?>')"> <i class="far fa-trash-alt"></i> </button></td>
</tr>
<? } ?>
					</table>
					
				</td>
			</tr>
		</table>
	</div>

	<div id="QA_detail5" class="section">
        <div class="section-title">
			<h2>이미지 정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds11">목록</th>
				<td class="tds33" colspan="3">
					<input type="file" name="file_mobile_thum" >
					W : <b>500</b>px H : <b>300</b>px
					<img src="<?=$_view_pd_mobile_thum_s?>" style="height:50px; margin-left:20px;">
					(<?=$_view_pd_mobile_thum?>)
				</td>
			</tr>

			<tr>
				<th class="tds11">추가 이미지</th>
				<td class="tds33" colspan="3">
					<div class="option-btn">
						<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:150px" onclick="plusImgAdd()"> <i class="fas fa-plus-circle"></i> 이미지 추가</button>
					</div>
					<table id='plusimg_table' name='plusimg_table' class="table-style plusimg-table">
						<?
						for($i=0; $i<count($_ary_pd_mobile_sliding); $i++){ 
							$i2 = $i+1;
							$_ary2_pd_mobile_sliding_s = explode(".", $_ary_pd_mobile_sliding[$i]);
							$_view2_pd_mobile_sliding_s = "../../uploads/product/".$_ary2_pd_mobile_sliding_s[0]."_s.".$_ary2_pd_mobile_sliding_s[1];
							$_view2_pd_mobile_sliding= $_ary_pd_mobile_sliding[$i];
						?>
						<input type='hidden' name='file_mobile_sliding_text_array[]' value="<?=$_ary_pd_mobile_sliding[$i]?>" >
						<tr>
							<th>추가 이미지 <?= $i2?></th>
							<td colspan="2">
								<input type="file" name="file_mobile_sliding[]" value=''>
								W : <b>600</b>px H : <b>400</b>px
								<img src="<?=$_view2_pd_mobile_sliding_s?>" style="height:50px; margin-left:20px;">
								( <?=$_view2_pd_mobile_sliding?> )
							</td>
						</tr>
						<? } ?>
					</table>
				</td>
			</tr>

		</table>
	</div>

	<div id="QA_detail6" class="section">
        <div class="section-title">
			<h2>상세설명</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
			<tr>
				<th class="tds11">상세내용</th>
				<td class="tds33" colspan="3"><textarea name='pd_detail_cont'><?=$pd_data[PD_DETAIL_CONT]?></textarea></td>
			</tr>
			<tr>
				<th class="tds11">기본설명</th>
				<td class="tds33" colspan="3"><textarea name='pd_cont'><?=$pd_data[PD_CONT]?></textarea></td>
			</tr>
			<tr>
				<th class="tds11">사용방법</th>
				<td class="tds33" colspan="3"><textarea name='pd_directions'><?=$pd_data[PD_DIRECTIONS]?></textarea></td>
			</tr>
			<tr>
				<th class="tds11">여행 스토리</th>
				<td class="tds33" colspan="3"><textarea name='pd_tour_story'><?=$pd_data[PD_TOUR_STORY]?></textarea></td>
			</tr>
			<tr>
				<th class="tds11">포함&불포함 사항</th>
				<td class="tds33" colspan="3"><textarea name='pd_inclusion'><?=$pd_data[PD_INCLUSION]?></textarea></td>
			</tr>
			<tr>
				<th class="tds11">취소 및 환불정책</th>
				<td class="tds33" colspan="3"><textarea name='pd_return_policy'><?=$pd_data[PD_RETURN_POLICY]?></textarea></td>
			</tr>
		</table>
	</div>

	<div id="QA_detail7" class="section">
        <div class="section-title">
			<h2>추가정보</h2>
        </div>
		<table cellspacing="1" cellpadding="0" class="table-style" >
		 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCct0tvS5JB5-N-14pwRgdyHDwQAjM79Tc&callback=initMap" async defer></script>
			<tr>
				<th class="tds11">위치</th>
				<td class="tds33" colspan="3">
					<div>
						좌표 ( X ) : <input type='text'  name='pd_x_gps' id='pd_x_gps' value="<?=$pd_data[PD_X_GPS]?>" style="width:150px; margin-right:20px;"> 
						좌표 ( Y ) : <input type='text' name='pd_y_gps' id='pd_y_gps' value="<?=$pd_data[PD_Y_GPS]?>" style="width:150px;">
					</div>
					<div id="map" style="width:400px; height:180px;"></div>
				</td>
			</tr>
			<tr>
				<th class="tds11">동영상 URL</th>
				<td class="tds33" colspan="3"><input type='text' name='pd_video_url' id='pd_video_url'  value="<?=$pd_data[PD_VIDEO_URL]?>"></td>
			</tr>
			
		</table>
		</form>
	</div>
   
</div>

<div id="footer">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="goSave();" > 
		<i class="far fa-check-circle"></i> 상품 수정
	</button>
</div>


<script type="text/javascript"> 
<!-- 
function showDepthChange(ct_id, depth_id, depth, target_id){ 

		$.ajax({
			type : "POST",
			url : "<?=_A_PATH_PRODUCT_CATE_SELECT_SHOW?>",
			data : { ct_id : ct_id, depth_id : depth_id, depth : depth },
			error : function(){
			},
			success : function(data){
				$("#"+ target_id).html(data) ;
			}
        });

}

showDepthChange("<?=$pdc_id?>", "", "0", "pdc_depth_1");
showDepthChange("<?=$pdc_id?>", "<?=$pdc_depth_1?>", "1", "pdc_depth_2");
showDepthChange("<?=$pdc_id?>", "<?=$pdc_depth_2?>", "2", "pdc_depth_3");
showDepthChange("<?=$pdc_id?>", "<?=$pdc_depth_3?>", "3", "pdc_depth_4");

var optionCount = <?=count($_ary_pd_option)?>*1;

var optionAdd=function(){
	
	optionCount++;

    var showHtml = ""
		+"<tr id='option_tr_"+ optionCount +"'>"
		+"<td class='option-no-td'>"+ optionCount +"</td>"
		+"<td class='option-name-td'><input type='text' name='option_name[]'></td>"
		+"<td class='option-price-td'>"
		+"<div class='option-price-wrap'>"
		+"<ul>"
		+"<li class='option-price-name'>VM</li>"
		+"<li><input type='text' name='option_price[]' class='price-s' onkeyUP='is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);'></li>"
        +"<li><input type='text' name='option_price_child[]' class='price-s' onkeyUP='is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);'></li>"
		+"</ul>"
		+"<ul>"
		+"<li class='option-price-name' style='padding-top:4px !important;'>Free</li>"
		+"<li style='padding-top:4px !important;'><input type='text' name='option_price_free[]' class='price-s' onkeyUP='is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);'></li>"
        +"<li style='padding-top:4px !important;'><input type='text' name='option_price_free_child[]' class='price-s' onkeyUP='is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);'></li>"
		+"</ul>"
        +"<ul>"
		+"<li class='option-price-name' style='padding-top:4px !important;'>원가</li>"
		+"<li style='padding-top:4px !important;'><input type='text' name='option_price_cost[]' class='price-s' onkeyUP='is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);'></li>"
        +"<li style='padding-top:4px !important;'><input type='text' name='option_price_cost_child[]' class='price-s' onkeyUP='is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);'></li>"
		+"</ul>"
		+"</div>"
		+"</td>"
		+"<td class='option-cont-td'><textarea name='option_cont[]' class='option-cont'></textarea></td>"
		+"<td class='option-del-td'><button type='button' class='btnstyle1 btnstyle1-danger btnstyle1-sm' style='height:40px;' onclick='optionDel(\""+ optionCount +"\")'> <i class='far fa-trash-alt'></i> </button></td>"
		+"</tr>";

	$("#pd_option_table").append(showHtml);
};

var optionDel=function(num){
	$('#option_tr_'+num).remove();
};

var plusImgCount = <?=count($_ary_pd_mobile_sliding)?>*1;

var plusImgAdd=function(){

	plusImgCount++;

    var showHtml = ""
		+"<tr>"
		+"<th>추가 이미지 "+ plusImgCount +"</th>"
		+"<td>"
		+"<input type='file' name='file_mobile_sliding[]'>"
		+"W : <b>600</b>px H : <b>400</b>px"
		+"</td>"
		+"<td class='option-del-td'><button type='button' class='btnstyle1 btnstyle1-danger btnstyle1-sm' onclick='optionDel(\""+ optionCount +"\")'> <i class='far fa-trash-alt'></i> </button></td>"
		+"</tr>";

	$("#plusimg_table").append(showHtml);
};
//--> 
</script> 

<?
include "../layout/footer_popup.php";
exit;
?>