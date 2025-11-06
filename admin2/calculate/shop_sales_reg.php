<?
include "../lib/inc_common.php";
$pageGroup = "calculate";
$pageName = "shop_sales_reg";

		$_ss_idx = securityVal($key);
		$_mode = securityVal($mode);
		$page_link_text = "?action=serch";
		if($s_day){
			$page_link_text .= "&s_day=".$s_day;
		}
		if($e_day){
			$page_link_text .= "&e_day=".$e_day;
		}
		if($search_shop){
			$page_link_text .= "&search_shop=".$search_shop;
		}
		if($search_category){
			$page_link_text .= "&search_category=".$search_category;
		}
		if($search_guide){
			$page_link_text .= "&search_guide=".$search_guide;
		}



		if($_mode == 'modify'){
			$shop_sales_data =  wepix_fetch_array(wepix_query_error("select * from "._DB_SHOP_SALES." where SS_IDX = ".$_ss_idx));


			$_view_ss_name = $shop_sales_data[SS_NAME];
			$_view_ss_date = date("Y-m-d",strtotime ($shop_sales_data[SS_SALE_DATE]));
			$_view_ss_bkg_idx = $shop_sales_data[SS_BKG_IDX];
			$_view_ss_guide_name = $shop_sales_data[SS_GUIDE_NAME];
			$_view_ss_sale_name = $shop_sales_data[SS_SALE_NAME];
			$_view_ss_kind_hp = $shop_sales_data[SS_KIND_HP];
			$_view_ss_product_kind = $shop_sales_data[SS_PRODUCT_KIND];
	
			$_view_ss_personel = $shop_sales_data[SS_PERSONEL];
			$_view_ss_dc_count = $shop_sales_data[SS_DC_COUNT];
			$_view_ss_sale_price = $shop_sales_data[SS_SALE_PRICE];
			$_view_ss_report_price = $shop_sales_data[SS_REPORT_PRICE];
			$_view_ss_cash = $shop_sales_data[SS_CASH];
			$_view_ss_credit_price = $shop_sales_data[SS_CREDIT_PRICE];
			$_view_ss_credit_deposit = $shop_sales_data[SS_CREDIT_DEPOSIT];
			$_view_ss_memo = $shop_sales_data[SS_MEMO];
			
			$_bkg_maching_date = strtotime($shop_sales_data[SS_SALE_DATE]);
			$_bkg_maching_date_st = strtotime("-15 day" ,$_bkg_maching_date);
			$_bkg_maching_date_ed = strtotime("+15 day" ,$_bkg_maching_date);

			$guide_data =  wepix_fetch_array(wepix_query_error("select GD_ID from "._DB_GUIDE." where GD_NAME = '".$_view_ss_guide_name."' "));
			$bkg_result =  wepix_query_error("select BKG_IDX,BKG_NAME from "._DB_BOOKING_GROUP." where BKG_GID_ID ='".$guide_data[GD_ID]."' and BKG_STATE != 'Y'  and BKG_START_DATE >= ".$_bkg_maching_date_st." and BKG_END_DATE <= ".$_bkg_maching_date_ed." order by BKG_IDX desc");
			
			$page_title_text = "샵매출 수정";
			$submit_btn_text = "샵매출 수정";

		}else{
			$page_title_text = "샵매출 등록";
			$submit_btn_text = "샵매출 등록";

		}

		$guide_query = "select * from "._DB_GUIDE."  order by GD_NICK ASC ";
		$guide_result2 = wepix_query_error($guide_query);
		$alliance_query =  wepix_query_error("select * from "._DB_ALLIANCE_SHOP." order by AS_NAME asc ");
		
include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:100%; }
.table-style{ width:100%; }

</STYLE>
<script type='text/javascript'>

var guideSelectData = "";
<?
$guide_result = wepix_query_error($guide_query);
while($guide_list = wepix_fetch_array($guide_result)) {
?>
	guideSelectData += "<option value='<?=$guide_list[GD_NAME]?>'><?=$guide_list[GD_NICK]?></option>";
<? } ?>


var shopSalesCount = 1;
function shopSalesAdd(){
	shopSalesCount++;
	var showHtml = ""
				+"<tr>"
				+"<td class='tds2'>"
				+"<select name='ss_gd_name[]'>"
				+"<option value=''>Select Guide</option>"
				+guideSelectData
				+"</select>"
				+"</td>"
				+"<td class='tds2'>"
				+"<select name='ss_sale_name[]'>"
				+"<option value=''>Select</option>"
				+"<option value='N/B'>N/B</option>"
				+"<option value='일반판매'>일반판매</option>"
				+"<option value='외상입금'>외상입금</option>"
				+"<option value='악성처리'>악성처리</option>"
				+"<option value='추가판매'>추가판매</option>"
				+"<option value='고객반품'>고객반품</option>"
				+"</select>"
				+"</td>"
				+"<td class='tds2'>"
				+"<select name='ss_kind_hp[]'>"
				+"<option value=''>Select</option>"
				+"<option value='HM'>HM</option>"
				+"<option value='FA'>FA</option>"
				+"</select>"
				+"</td>"
				+"<td class='tds2'><input type='text' name='ss_personel[]'></td>"
				+"<td class='tds2'><input type='text' name='ss_dis_count[]'></td>"
				+"<td class='tds2'><input type='text' name='ss_sale_price[]'></td>"
				+"<td class='tds2'><input type='text' name='ss_report_price[]'></td>"
				+"<td class='tds2'><input type='text' name='ss_cash[]'></td>"
				+"<td class='tds2'><input type='text' name='ss_credit_price[]'></td>"
				+"<td class='tds2'><input type='text' name='ss_credit_deposit[]'></td>"
				+"<td class='tds2'><input type='text' name='ss_memo[]'></td>"
				+"</tr>";

	$("#plusShopSales_table").append(showHtml);
};

function changeShop(val){
	
	if(val == 'TM'){
	   $("#change_th_1").text("TM 추가금액");
	   $("#change_th_2").text("인정매출");
	}else{
	   $("#change_th_1").text("판매금액");
	   $("#change_th_2").text("보고금액");
	}
}
</script>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">

			<form name='form1' id='form1' action='<?=_A_PATH_SHOP_OK?>' method='post'>
			<?if($_mode == 'modify'){?>
				<input type="hidden" name="a_mode" value="shopSalesModify">
				<input type="hidden" name="ss_idx" value="<?=$shop_sales_data[SS_IDX]?>">
			<?}else{?>
				<input type="hidden" name="a_mode" value="shopSalesNew">
			<?}?>
			<input type="text" id="search_st" name="search_st" value="<?=$_view_ss_date?>" class="date-input" style="width:150px; float:left; margin:5px; background-color:#fff; background-position: 5px 5px; cursor:pointer;" readonly />
			<select  style="width:150px; margin:5px; " name='ss_shop_name' onchange='changeShop(this.value);'>
					<option value=''>Select Shop</option>
					<?
					while($as_list = wepix_fetch_array($alliance_query)){
						?>
							<option value="<?=$as_list[AS_VALUE]?>" <?if($_view_ss_name ==  $as_list[AS_VALUE]){echo "selected";}?> ><?=$as_list[AS_NAME]?></option>
				    <?  } ?>
			</select>

			<select  style="width:150px; margin:5px; " name='ss_product_kind'>
				<option value=''>Select Shop Kind</option>
				<option value='A'  <?if($_view_ss_product_kind ==  'A'){echo "selected";}?> >A</option>
				<option value='B'  <?if($_view_ss_product_kind ==  'B'){echo "selected";}?> >B</option>
				<option value='C'  <?if($_view_ss_product_kind ==  'C'){echo "selected";}?> >C</option>
				<option value='BKK'  <?if($_view_ss_product_kind ==  'BKK'){echo "selected";}?> >BKK</option>	
			</select>
			<?if($_mode != 'modify'){?>
			<button type="button" id=""class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:150px; margin:5px; float:right;" onclick="shopSalesAdd()"> <i class="fas fa-plus-circle"></i> 매출 추가</button>
			<?}?>

			<table cellspacing="1" cellpadding="0" class="table-style" id='plusShopSales_table'>
				<tr>
					<th class="tds1">가이드명</th>
				<?if($_mode == 'modify'){?>
					<th class="tds1">부킹그룹</th>
				<?}?>
					<th class="tds1">계정</th>
					<th class="tds1">Cate<br/>gory</th>
					<th class="tds1">인원</th>
					<th class="tds1">DC율</th>
					<th class="tds1"><span id='change_th_1'>판매금액</span></th>
					<th class="tds1"><span id='change_th_2'>보고금액</span></th>
					<th class="tds1">현금판매</th>
					<th class="tds1">외상판매</th>
					<th class="tds1">외상금액</th>
					<th class="tds1">상세내용</th>
				</tr>
				<tr>
					<td class="tds2">
						<select name='ss_gd_name[]'>
							<option value=''>Select Guide</option>
							<?while($guide_list = wepix_fetch_array($guide_result2)){?>
								<option value='<?=$guide_list[GD_NAME]?>' <?if($_view_ss_guide_name == $guide_list[GD_NAME]){echo "selected";}?> ><?=$guide_list[GD_NICK]?></option>
							<?}?>
						</select>
					</td>
					<?if($_mode == 'modify'){?>
					<td class="tds2">
						<select name='ss_group_name[]'>
							<option value='0'>Select Group</option>
							<?while($bkg_list = wepix_fetch_array($bkg_result)){?>
								<option value='<?=$bkg_list[BKG_IDX]?>' <?if($_view_ss_bkg_idx == $bkg_list[BKG_IDX]){echo "selected";}?> ><?=$bkg_list[BKG_NAME]?></option>
							<?}?>
						</select>
					</td>
					<?}?>
					<td class="tds2">
						<select name='ss_sale_name[]'>
							<option value=''>Select</option>
							<option value='N/B' <?if($_view_ss_sale_name == 'N/B'){echo "selected";}?>>N/B</option>
							<option value='일반판매' <?if($_view_ss_sale_name == '일반판매'){echo "selected";}?> >일반판매</option>
							<option value='외상입금' <?if($_view_ss_sale_name == '외상입금'){echo "selected";}?> >외상입금</option>
							<option value='악성처리' <?if($_view_ss_sale_name == '악성처리'){echo "selected";}?> >악성처리</option>
							<option value='추가판매' <?if($_view_ss_sale_name == '추가판매'){echo "selected";}?> >추가판매</option>
							<option value='고객반품' <?if($_view_ss_sale_name == '고객반품'){echo "selected";}?> >고객반품</option>
						</select>
					</td>
					<td class="tds2" >
						<select name='ss_kind_hp[]'>
							<option value='HM'>HM</option>
							<option value='FA'>FA</option>
						</select>
					</td>
					<td class="tds2"><input type='text' name='ss_personel[]' value='<?=$_view_ss_personel ?>'></td>
					<td class="tds2"><input type='text' name='ss_dis_count[]' value='<?=$_view_ss_dc_count ?>'></td>
					<td class="tds2"><input type='text' name='ss_sale_price[]' value='<?=$_view_ss_sale_price ?>'></td>
					<td class="tds2"><input type='text' name='ss_report_price[]' value='<?=$_view_ss_report_price ?>'></td>
					<td class="tds2"><input type='text' name='ss_cash[]' value='<?=$_view_ss_cash ?>'></td>
					<td class="tds2"><input type='text' name='ss_credit_price[]' value='<?=$_view_ss_credit_price ?>'></td>
					<td class="tds2"><input type='text' name='ss_credit_deposit[]' value='<?=$_view_ss_credit_deposit ?>'></td>
					<td class="tds2"><input type='text' name='ss_memo[]' value='<?=$_view_ss_memo ?>'></td>

					<?
						
					
					?>
				</tr>
			</table>
			</form>
			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_SHOP_LIST?><?=$page_link_text?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doShopSubmit();" > 
						<i class="far fa-check-circle"></i>
						<?=$submit_btn_text?>
					</button>
				</ul>
			</div>
		</div>
		<div style="height:60px;"></div>
	</div>
</div>

<script type="text/javascript"> 
<!-- 
// Submit
function doShopSubmit(){

		var form = document.form1;
		form.submit();
}
</script>
<?
include "../layout/footer.php";
exit;
?>