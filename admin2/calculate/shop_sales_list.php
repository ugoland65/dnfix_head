<?
include "../lib/inc_common.php";
	$pageGroup = "calculate";
	$pageName = "shop_sales_list";

		$_ss_idx = securityVal($key);
		$_mode = securityVal($mode);

		$shop_sql = "where 1 = 1";

		$_ss_now_date = date("Ymd",$wepix_now_time);
		$page_link_text = "?action=serch";

		if(!$s_day){
			$today_date = date('Y-m-d'); 
			$day_of_the_week = date('w'); 
			$_day_of_the_week = $day_of_the_week -1;
			$_day_of_the_date = strtotime($today_date." -".$_day_of_the_week."days");
			$s_day = date('Y-m-d', $_day_of_the_date);
			$e_day = date('Y-m-d', strtotime("+7 day" , $_day_of_the_date));
		}

		if($s_day){
			$_search_st = str_replace("-","",$s_day);
			$shop_sql.= " and SS_SALE_DATE >= ".$_search_st;
			$page_link_text .= "&s_day=".$s_day;
		}
		if($e_day){
			$_search_et = str_replace("-","",$e_day);
			$shop_sql.= " and SS_SALE_DATE <= ".$_search_et;
			$page_link_text .= "&e_day=".$e_day;
		}
		if($search_shop){
			$shop_sql.= " and SS_NAME =  '".$search_shop."'";
			$page_link_text .= "&search_shop=".$search_shop;
		}
		if($search_category){
			$shop_sql.= " and SS_KIND_HP = '".$search_category."'";
			$page_link_text .= "&search_category=".$search_category;
		}
		if($search_guide){
			$shop_sql.= " and SS_GUIDE_NAME = '".$search_guide."'";
			$page_link_text .= "&search_guide=".$search_guide;
		}


/**                    Paging Start                       **/
//		$url = "https://gmphuket.cafe24.com/pkt_tour/tur_salesdayrpt_api/fGetData/".$_sales_shop_key."/".$_ss_now_date."/".$_ss_now_date."";
//		$url = "https://gmphuket.cafe24.com/pkt_tour/tur_salesdayrpt_api/fGetData/APA91bHpAx9dU8N5Sq8pM20PvbAbdmJCnuHgELPUTWBKQFXhKRFZdObCXvnzog8DtOQ9ye_fv2nQ5h1HZcGuVOpVs1RTSjYqgvIrv8WRwyqH8v7eW7iMqVVk209Mlo7oDupETTzpkH1H/20191213/20191213";
//

		$is_post = false;
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
		$_shop_sales_data = json_decode($response, true);
/*
echo $_shop_sales_data[msg]."/".$_shop_sales_data[data];
exit;
*/
if($_shop_sales_data[msg] == 'OK_DATA'){

	$_del_data = wepix_fetch_array(wepix_query_error("delete from "._DB_SHOP_SALES." where SS_SALE_DATE = '".$_ss_now_date."' and SS_REQ_ACTIVE != 'Y' "));

	for($i=0;$i<count($_shop_sales_data[data]); $i++){
			$_name = $_shop_sales_data[data][$i][site];
			$_sale_date = $_shop_sales_data[data][$i][vdate];
			$_guide_code = $_shop_sales_data[data][$i][guide];
			$_guide_name = $_shop_sales_data[data][$i][guidenm];
			$_sale_id = $_shop_sales_data[data][$i][pangu];
			$_sale_name = $_shop_sales_data[data][$i][pangunm];
			

			if($_shop_sales_data[data][$i][tourgu] == 'H'){
				$_kind_hp = 'HM';
			}elseif($_shop_sales_data[data][$i][tourgu] == 'P'){
				$_kind_hp = 'FA';
			}else{
				$_kind_hp = $_shop_sales_data[data][$i][tourgu];
			}

			$_personel = $_shop_sales_data[data][$i][inwon];
			$_dc_count = $_shop_sales_data[data][$i][dc];
			$_product_kind = $_shop_sales_data[data][$i][itemkd];
			$_exchange_rate = $_shop_sales_data[data][$i][appexch];
			$_sale_price = $_shop_sales_data[data][$i][pangum];
			$_report_price = $_shop_sales_data[data][$i][bogo];
			$_cash = $_shop_sales_data[data][$i][hgum];
			$_credit_price = $_shop_sales_data[data][$i][cgum];
			$_credit_deposit = $_shop_sales_data[data][$i][ibgum];

			$_sale_memo = $_shop_sales_data[data][$i][bigo];
			
			if($_sale_name == '외상입금' || $_sale_name == '고객반품' || $_sale_name == '매출수정'){
				$guide_data =  wepix_fetch_array(wepix_query_error("select GD_ID from "._DB_GUIDE." where GD_NAME = '".$_guide_name."' "));
				if($_sale_memo != ''){
					$dateText = substr($_sale_memo, 0, 10); 
					$dateVal =  str_replace("-", "", $dateText); 
					$_bkg_maching_date = strtotime($dateText);
					$bkg_date2 =  wepix_fetch_array(wepix_query_error("select BKG_IDX from "._DB_BOOKING_GROUP." where BKG_GID_ID ='".$guide_data[GD_ID]."' and BKG_START_DATE <= ".$_bkg_maching_date." and 
					BKG_END_DATE >= ".$_bkg_maching_date.""));
					if($bkg_date2[BKG_IDX]){
						$_bkg_idx =	$bkg_date2[BKG_IDX];
					}else{
						$_bkg_idx = '';
					}
				}
			}else{
				$guide_data =  wepix_fetch_array(wepix_query_error("select GD_ID from "._DB_GUIDE." where GD_NAME = '".$_guide_name."' "));
				$_bkg_maching_date = strtotime($_sale_date);
				$bkg_date =  wepix_fetch_array(wepix_query_error("select BKG_IDX from "._DB_BOOKING_GROUP." where BKG_GID_ID ='".$guide_data[GD_ID]."' and BKG_START_DATE <= ".$_bkg_maching_date." and 
				BKG_END_DATE >= ".$_bkg_maching_date.""));
				if($bkg_date[BKG_IDX]){
					$_bkg_idx =	$bkg_date[BKG_IDX];
				}else{
					$_bkg_idx = '';
				}
			}

		$query = "insert into  "._DB_SHOP_SALES." set
            SS_NAME = '".$_name."',
			SS_BKG_IDX = '".$_bkg_idx."',
			SS_SALE_DATE ='".$_sale_date."',
			SS_GUIDE_CODE = '".$_guide_code."' ,
			SS_GUIDE_NAME = '".$_guide_name."' ,
			SS_SALE_ID = '".$_sale_id."' ,
			SS_SALE_NAME = '".$_sale_name."',
			SS_KIND_HP = '".$_kind_hp."',
			SS_PERSONEL =  '".$_personel."',
			SS_DC_COUNT	 = '".$_dc_count."',
			SS_PRODUCT_KIND = '".$_product_kind."' ,
			SS_EXCHANGE_RATE = '".$_exchange_rate."' ,
			SS_SALE_PRICE = '".$_sale_price."' ,
			SS_REPORT_PRICE = '".$_report_price."',
			SS_CASH = '".$_cash."',
			SS_CREDIT_PRICE	 = '".$_credit_price."',
			SS_CREDIT_DEPOSIT = '".$_credit_deposit."' ,
			SS_MEMO = '".$_sale_memo."' ,
			SS_REG_DATE = '".$wepix_now_time."' ,
			SS_REQ_ACTIVE = 'N' ,
			SS_REG_ID = '".$_ad_id."'";

		$result = wepix_query_error($query);
	}
}
	$total_count = wepix_counter(_DB_SHOP_SALES, $shop_sql);

	$list_num = 50;
	$page_num = 10;

	// 전체 페이지 계산
	$total_page  = @ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
	$counter = $total_count - (($pn - 1) * $list_num);

	$shop_sales_result = wepix_query_error("select * from "._DB_SHOP_SALES." ".$shop_sql." order by SS_SALE_DATE desc, SS_GUIDE_NAME  , SS_PRODUCT_KIND limit ".$from_record.", ".$list_num);
	$_show_get_url = $page_link_text."&pn=".$_pn;
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $_show_get_url."&pn=");

/**                                Paging    End                       **/
include "../layout/header.php";

$trcolor = "#ffffff";
?>


<div id="contents_head">
	<h1>샵매출 목록</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<form name='search' method='post' action="<?=_A_PATH_SHOP_LIST?>">
		<div class="search-wrap">
			<ul class="td search-img"></ul>
			<ul class="td search-body">
				<select name='search_shop'>
					<option value=''>Select Shop</option>
					<?

						$as_result = wepix_query_error("select AS_IDX,AS_NAME,AS_VALUE from "._DB_ALLIANCE_SHOP." where AS_VIEW = 'Y'");
						while($as_list = wepix_fetch_array($as_result)){
						?>
							<option value="<?=$as_list[AS_VALUE]?>"  <? if( $search_shop == $as_list[AS_VALUE]  ) echo "selected"; ?> ><?=$as_list[AS_NAME]?></option>
				    <?  } ?>
				</select>
				<select name='search_category'>
					<option value=''>Select Category</option>
					<?
						$bs_result = wepix_query_error("select * from "._DB_BOOKING_SETTING." where BKS_KIND = 'B' order by BKS_IDX asc");
						while($bs_list = wepix_fetch_array($bs_result)){
						?>
							<option value="<?=$bs_list[BKS_VALUE]?>"  <? if( $search_category == $bs_list[BKS_VALUE]) echo "selected"; ?> ><?=$bs_list[BKS_NAME]?></option>
				    <?  } ?>
				</select>
				<select name='search_guide'>
					<option value=''>Select Guide</option>
					<?
						$gd_result = wepix_query_error("select * from "._DB_GUIDE." where GD_VIEW_YN = 'Y'");
						while($gd_list = wepix_fetch_array($gd_result)){
						?>
							<option value="<?=$gd_list[GD_NAME]?>"  <? if( $search_guide == $gd_list[GD_NAME]) echo "selected"; ?> ><?=$gd_list[GD_NICK]?></option>
				    <?  } ?>
				</select>
			<ul style='margin-top:15px;'>
				 <input type="text" id="s_day" name="s_day" value="<?=$s_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="시작일" readonly /> ~
				 <input type="text" id="e_day" name="e_day" value="<?=$e_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="종료일" readonly />
			</ul>
			</ul>
            <ul class="td search-button">
				<input type="submit" value="Searching">
			</ul>
		</div>
		</form>

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total : <b><?=$total_count?></b></span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list-box">
			<div class="table-wrap">
			<table cellspacing="1" cellpadding="0" class="table-style">
				<tr>
					<th class="tds1" style='width:90px;'>날짜</th>
					<th class="tds1" style='width:70px;'>샵</th>
					<th class="tds1" style='width:150px;'>부킹그룹</th>
					<th class="tds1" style='width:70px;'>가이드</th>
					<th class="tds1" style='width:90px;'>계정</th>
					<th class="tds1" style='width:60px;'>Cate<br/>gory</th>
					<th class="tds1" style='width:40px;'>인원</th>
					<th class="tds1" style='width:50px;'>적용환율</th>
					<th class="tds1">판매금액</th>
					<th class="tds1">보고금액</th>
					<th class="tds1">현금판매</th>
					<th class="tds1">외상판매</th>
					<th class="tds1">외상금액</th>
					<th class="tds1">상세내용</th>
					<th class="tds1">관리</th>

				</tr>
				<?
					$_ex_data = wepix_fetch_array(wepix_query_error("select ER_DOLLAR_MONEY from "._DB_EXCHANGE_RATE." where ER_KIND ='get' order by ER_IDX desc limit 0, 1"));

					while($shop_sales_list = wepix_fetch_array($shop_sales_result)){
						$_view_ss_date = date("d-M-y",strtotime($shop_sales_list[SS_SALE_DATE]));
						$_view_ss_name = $shop_sales_list[SS_NAME];
						$_view_ss_product_kind = $shop_sales_list[SS_PRODUCT_KIND];
						
						$_view_ss_guide_name = $shop_sales_list[SS_GUIDE_NAME];
						$_view_ss_sale_name = $shop_sales_list[SS_NAME];
						$_view_ss_sale_id = $shop_sales_list[SS_SALE_NAME];
						$_view_ss_kind_hp = $shop_sales_list[SS_KIND_HP];
						$_view_ss_personel = $shop_sales_list[SS_PERSONEL];
						$_view_ss_dc_count = $shop_sales_list[SS_DC_COUNT];

						$_view_ss_sale_price = number_format($shop_sales_list[SS_SALE_PRICE]);
						$_view_ss_report_price = number_format($shop_sales_list[SS_REPORT_PRICE]);
						$_view_ss_cash = number_format($shop_sales_list[SS_CASH]);
						$_view_ss_credit_price = number_format($shop_sales_list[SS_CREDIT_PRICE]);
						$_view_ss_credit_deposit = number_format($shop_sales_list[SS_CREDIT_DEPOSIT]);
						$_view_ss_memo = $shop_sales_list[SS_MEMO];
						
						$_view_ss_exchange =  $shop_sales_list[SS_EXCHANGE_RATE];
						if($_view_ss_exchange == 1){
						 $_view_ss_exchange = $_ex_data[ER_DOLLAR_MONEY];
						}

						if($_view_ss_sale_name != 'GML' && $_view_ss_sale_name != 'BRD'){
							$_view_ss_sale_price = number_format((($shop_sales_list[SS_SALE_PRICE] / $_view_ss_exchange)))." (฿".$_view_ss_sale_price.")";
							$_view_ss_report_price = number_format((($shop_sales_list[SS_REPORT_PRICE] / $_view_ss_exchange)))." (฿".$_view_ss_report_price.")";
							$_view_ss_cash = number_format((($shop_sales_list[SS_CASH] / $_view_ss_exchange)))." (฿".$_view_ss_cash.")";
							$_view_ss_credit_price =number_format((($shop_sales_list[SS_CREDIT_PRICE] / $_view_ss_exchange)))." (฿".$_view_ss_credit_price.")";
							$_view_ss_credit_deposit = number_format((($shop_sales_list[SS_CREDIT_DEPOSIT] / $_view_ss_exchange)))." (฿".$_view_ss_credit_deposit.")";
						}
						$bkg_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING_GROUP." WHERE BKG_IDX = '".$shop_sales_list[SS_BKG_IDX]."'"));
				?>
				<tr>
					<td class="tds2"><input type='text' readonly value='<?=$_view_ss_date ?>'></td>
					<td class="tds2"><input type='text' readonly value='<?=$_view_ss_sale_name ?> <?=$_view_ss_product_kind?>'></td>
					<td class="tds2"><?=$bkg_data[BKG_NAME]?></td>
					<td class="tds2"><?=$_view_ss_guide_name?></td>
					<td class="tds2"><input type='text' readonly value='<?=$_view_ss_sale_id ?>'></td>
					<td class="tds2"><input type='text' readonly value='<?=$_view_ss_kind_hp ?>'></td>
					<td class="tds2"><input type='text' readonly value='<?=$_view_ss_personel ?>'></td>
					<td class="tds2"><input type='text' readonly value='<?=$_view_ss_exchange ?>'></td>
					<td class="tds2"><input type='text' readonly value='$<?=$_view_ss_sale_price?>'></td>
					<td class="tds2"><input type='text' readonly value='$<?=$_view_ss_report_price ?>'></td>
					<td class="tds2"><input type='text' readonly value='$<?=$_view_ss_cash ?>'></td>
					<td class="tds2"><input type='text' readonly value='$<?=$_view_ss_credit_price ?>'></td>
					<td class="tds2"><input type='text' readonly value='$<?=$_view_ss_credit_deposit ?>'></td>
					<td class="tds2"><input type='text' readonly value='<?=$_view_ss_memo ?>'></td>
					<td>
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='<?=_A_PATH_SHOP_REG?><?=$page_link_text?>&mode=modify&key=<?=$shop_sales_list[SS_IDX]?>'"> 수정 </button>
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="delShop('<?=$shop_sales_list[SS_IDX]?>');"> 삭제 </button>
					</td>
				</tr>
				<?}?>
			</table>
				
			</div>
		</div><!-- #list-box -->
		<div class="paging-wrap"><?=$view_paging?></div>

	</div>
</div>
<link rel="stylesheet" href="/admin2/css/daterangepicker.css" />
<script src="/admin2/js/moment.min.js"></script>
<script src="/admin2/js/jquery.daterangepicker.js"></script>
<script type="text/javascript"> 
function delShop(key){
	if(confirm('정말 삭제 하시겠습니까?') == true){
		location.href = "<?=_A_PATH_SHOP_OK?>?idx="+key+"&a_mode=delShop";
	}
}


function goSerch(){

	var form1 = document.search_form;
	form1.submit();
}
<!-- 
$(function(){
/*
	$("#s_day").datepicker();
	$("#e_day").datepicker();
*/
	$('#s_day').dateRangePicker(
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
			$('#s_day').val(s1);
			$('#e_day').val(s2);
		}
	});
});
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>