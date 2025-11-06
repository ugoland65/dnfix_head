<?
include "../lib/inc_common.php";
	$pageGroup = "product";
	$pageName = "golf_reg";
	

	$_mode = securityVal($mode);
	$_gf_idx = securityVal($key);

	

 if($_mode == 'new'){
	$page_title_text = "골프 등록";
	$submit_btn_text = "골프 등록";
 }elseif($_mode == 'modify'){
	 $golf_data = wepix_fetch_array(wepix_query_error("select * from "._DB_GOLF." where GF_IDX = '".$_gf_idx."' "));
	 $page_title_text = "골프 수정";
	 $submit_btn_text = "골프 수정"; 
 }

include "../layout/header.php";
?>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
</div>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
.guide-profile-wrap{ width:100%; height:150px; margin-bottom:10px; }
.guide-profile{ width:150px; height:150px; border:1px solid #9096a3; border-radius:10px;  }
</STYLE>
<script type='text/javascript'>
	   function goSave(){
			var form = document.form1;
			form.submit();
		  };

	   function DoRoomDel(idx){
			
			var form = document.form1;
			form.action_mode.value = "Del";
			form.action ="room_ok.php?key="+idx;
			form.submit();

	   }
	  function DoRoomModify(idx){
			
			var form = document.form1;
			form.action_mode.value = "modify";
			form.action ="room_ok.php?key="+idx;
			form.submit();

	   }
</script>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div class="table-wrap">
			<form name='form1' action='golf_ok.php' method='post'>
			 <input type='hidden' name='action_mode' value='<?= $_mode ?>'>
			 <input type='hidden' name='mokey' value='<?= $_gf_idx ?>'>
				<table cellspacing="1" cellpadding="0" class="table-style" id=''>

				 <tr class="tds1">
				  <th>Area</th>

				  <td class="tds2">
				  <select name='gf_area' id='gf_area'>
						<option value="">= Area =</option>
						<?
						$area_query = "select * from ".$db_t_AREA." where AREA_KIND = 'L' order by AREA_IDX asc";
						$area_result = wepix_query_error($area_query);
						while($area_list = wepix_fetch_array($area_result)){
						?>
							<option value="<?=$area_list[AREA_CODE]?>"  <? if( $golf_data[GF_AREA]== $area_list[AREA_CODE]  ) echo "selected"; ?> ><?=$area_list[AREA_CODE]?></option>
						<? } ?>
				    </select>
				  </td>

				 </tr>
				 <tr>
					 <th class="tds1">Name</th>
					 <td class="tds2"><input type='text' name='gf_name' id='gf_name' size='60' value="<?=$golf_data[GF_NAME]?>" ></td>
                 </tr>
                 <tr>
					 <th class="tds1">Coupon Code</th>
					 <td class="tds2"><input type='text' name='gf_code' id='gf_code' size='20' value="<?=$golf_data[GF_COUPON_CODE]?>" ></td>
                 </tr>
                 <tr>
					 <th class="tds1">Green Fee - 9 holes</th>
					 <td class="tds2"><input type='text' name='gf_green_fee_09' id='gf_green_fee_09' size='20' value="<?=$golf_data[GF_GREEN_9_FEE]?>" ></td>
                 </tr>
                 <tr>
					 <th class="tds1">Green Fee - 18 holes</th>
					 <td class="tds2"><input type='text' name='gf_green_fee_18' id='gf_green_fee_18' size='20' value="<?=$golf_data[GF_GREEN_18_FEE]?>" ></td>
                 </tr>
                 <tr>
					 <th class="tds1">Green Fee - 36 holes</th>
					 <td class="tds2"><input type='text' name='gf_green_fee_36' id='gf_green_fee_36' size='20' value="<?=$golf_data[GF_GREEN_36_FEE]?>" ></td>
                 </tr>
                 <tr>
					 <th class="tds1">Green Fee 9 holes Coupon</th>
					 <td class="tds2"><input type='text' name='gf_green_9_coupon_fee' id='gf_green_9_coupon_fee' size='20' value="<?=$golf_data[GF_GREEN_9_COUPON_FEE]?>" ></td>
                 </tr>
                 <tr>
					 <th class="tds1">Green Fee 18 holes Coupon</th>
					 <td class="tds2"><input type='text' name='gf_green_18_coupon_fee' id='gf_green_18_coupon_fee' size='20' value="<?=$golf_data[GF_GREEN_18_COUPON_FEE]?>" ></td>
                 </tr>
                 <tr>
					 <th class="tds1">Green Fee Set Coupon</th>
					 <td class="tds2"><input type='text' name='gf_green_set_coupon_fee' id='gf_green_set_coupon_fee' size='20' value="<?=$golf_data[GF_GREEN_SET_COUPON_FEE]?>" ></td>
                 </tr>
                 <tr>
					 <th class="tds1">Caddie Fee</th>
					 <td class="tds2"><input type='text' name='gf_caddie_fee' id='gf_caddie_fee' size='20' value="<?=$golf_data[GF_CADDIE_FEE]?>" ></td>
                 </tr>
                 <tr>
					 <th class="tds1">Cart Fee (Single)</th>
					 <td class="tds2"><input type='text' name='gf_cart_fee_1' id='gf_cart_fee_1' size='20' value="<?=$golf_data[GF_CART_FEE]?>" ></td>
                 </tr>
                 <tr>
					 <th class="tds1">Cart Fee (Double)</th>
					 <td class="tds2"><input type='text' name='gf_cart_fee_2' id='gf_cart_fee_2' size='20' value="<?=$golf_data[GF_DOUBLE_CART_FEE]?>" ></td>
				 </tr>
				 <tr>
				   <th class="tds1">Exposure/non</th>
				   <td class="tds2">
					<input type="radio" name="gf_view" value="Y" <? if( $golf_data[GF_VIEW]=="Y" OR !$golf_data[GF_VIEW] ) echo "checked"; ?> > Exposure
					<input type="radio" name="gf_view"  value="N" <? if( $golf_data[GF_VIEW]=="N") echo "checked"; ?>> non Exposure
				   </td>
				 </tr>
				</table>
				
			</form>
			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_PRODUCT_GOLF_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doGolfSubmit();" > 
						<i class="far fa-check-circle"></i>
						<?=$submit_btn_text?>
					</button>
				</ul>
			</div>
			
		</div>
			
	</div>
</div>
<script type='text/javascript'>
  function doGolfSubmit(){
	var form = document.form1;
	form.submit();
  };
</script>


<?
include "../layout/footer.php";
?>