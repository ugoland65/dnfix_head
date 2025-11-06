<?
$pageGroup = "comparison";
$pageName = "site_list";
include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	$_idx = securityVal($key);

	$_token = make_token(5,"site");

	if( $_mode == "modify" ){
		$site_data = wepix_fetch_array(wepix_query_error("select * from "._DB_SITE." where SD_IDX = '".$_idx."' "));

		$_site_token = $site_data[SITE_TOKEN];
		if( !$_site_token ){
			$_site_token = $_token;
		}

		$page_title_text = "판매몰 수정";
		$submit_btn_text = "판매몰 수정";

	}else{

		$_site_token = $_token;

		$page_title_text = "판매몰 등록";
		$submit_btn_text = "판매몰 등록";

	}

	$_serch_query = " CL_SD_IDX = '".$_idx."' ";
	$total_count = wepix_counter(_DB_COMPARISON_LINK, $_serch_query);

	wepix_query_error("update "._DB_SITE." set SD_CLINK_COUNT =  '".$total_count."' where SD_IDX = '".$_idx."' ");

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ display:table; }
.table-wrap-left{ width:700px; display:table-cell; }
.table-wrap-right{ display:table-cell; }
.table-style{ width:100%; }
</STYLE>
<script language=javascript>

function goSubmit(){
	var form = document.form1;
	form.submit();
}
function numberWithCommas(x) {
	var form = document.form1;
	n = parseInt(x.replace(/,/g,""));
    form.sd_delivery_free.value = n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

</script>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
    <div id="head_write_btn">
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div class="table-wrap">
			<ul class="table-wrap-left">

				<form name='form1' action='<?=_A_PATH_COMPARISON_OK?>' method='post' enctype="multipart/form-data" autocomplete="off">
				<? if( $_mode == "modify" ){ ?>
					<input type="hidden" name="a_mode" value="siteModify">
					<input type="hidden" name="idx" value="<?=$site_data[SD_IDX]?>">
					<input type="hidden" name="modify_sd_logo" value="<?=$site_data[SD_LOGO]?>">
				<? }else{ ?>
					<input type="hidden" name="a_mode" value="siteNew">
				<? } ?>
					<input type="hidden" name="site_token" value="<?=$_site_token?>">

						<table cellspacing="1" cellpadding="0" class="table-style">

						 <tr>
							 <th class="tds1">이름</th>
							 <td class="tds2"><input type='text' name='sd_name'  size='40' value="<?=$site_data[SD_NAME]?>" ></td>
						 </tr>
						 <tr>
							 <th class="tds1">랭킹</th>
							 <td class="tds2"><input type='text' name='sd_rank'  size='40' value="<?=$site_data[SD_RANK]?>" ></td>
						 </tr>
						 <tr>
							 <th class="tds1">분류</th>
							 <td class="tds2">
								<input type="radio" name='sd_kind' value="brand" <? if( $site_data[SD_KIND]=="brand") echo "checked"; ?>> 브랜드
								<input type="radio" name='sd_kind' value="online" <? if( $site_data[SD_KIND]=="online" OR $site_data[SD_KIND] =="" ) echo "checked"; ?>> 온라인몰
								<input type="radio" name='sd_kind' value="openmarket" <? if( $site_data[SD_KIND]=="openmarket" ) echo "checked"; ?>> 오픈마켓
							 </td>
						 </tr>
						 <tr>
							 <th class="tds1">등록상품수</th>
							 <td class="tds2"><?=$total_count?> / <?=$site_data[SD_CLINK_COUNT]?></td>
						 </tr>
						 <tr>
							<th class="tds1">로고</th>
							<td class="tds2">
								<input type="file" id="sd_logo" name="sd_logo" >
								<? if( $site_data[SD_LOGO] ){ ?>
								<div>
									<img src="../../data/site_logo/<?=$site_data[SD_LOGO]?>" alt="">
								</div>
								<? } ?>
							</td>
						 </tr>
						 <tr>
							 <th class="tds1">도메인</th>
							 <td class="tds2"><input type='text' name='sd_domain' value="<?=$site_data[SD_DOMAIN]?>"></td>
						 </tr>
						<tr>
							<th class="tds1">배송비</th>
							<td class="tds2"><input type='text' name='sd_delivery' value="<?=$site_data[SD_DELIVERY]?>"></td>
						</tr>
						<tr>
							<th class="tds1">무료배송기준</th>
							<td class="tds2"><input type='text' name='sd_delivery_free' value="<?=number_format($site_data[SD_DELIVERY_FREE])?>" onkeyup="numberWithCommas(this.value)"></td>
						</tr>
						 <tr>
							 <th class="tds1">당일배송정보</th>
							 <td class="tds2"><input type='text' name='sd_delivery_time' value="<?=$site_data[SD_DELIVERY_TIME]?>"></td>
						 </tr>
						<tr>
							<th class="tds1">가입 쿠폰</th>
							<td class="tds2"><input type='text' name='sd_join_coupon' value="<?=$site_data[SD_JOIN_COUPON]?>"></td>
						</tr>

						 <tr>
							 <th class="tds1">부가정보</th>
							 <td class="tds2"><input type='text' name='sd_memo' value="<?=$site_data[SD_MEMO]?>"></td>
						 </tr>
						 <tr>
							 <th class="tds1">노출 여부</th>
							 <td class="tds2">
							 <label><input type='radio' name='sd_view' value="Y" <? if( $site_data[SD_ACTIVE]=="Y" OR $site_data[SD_ACTIVE] =="" ) echo "checked"; ?>> Y </label>
							 <label><input type='radio' name='sd_view' value="N" <? if( $site_data[SD_ACTIVE]=="N") echo "checked"; ?>> N </label>
							 </td>
						 </tr>	
						 
						 <tr>
							 <th class="tds1">리스트 노출</th>
							 <td class="tds2">
							 <label><input type='radio' name='sd_list_active' value="Y" <? if( $site_data[SD_LIST_ACTIVE]=="Y" OR $site_data[SD_LIST_ACTIVE] =="" ) echo "checked"; ?>> Y </label>
							 <label><input type='radio' name='sd_list_active' value="N" <? if( $site_data[SD_LIST_ACTIVE]=="N") echo "checked"; ?>> N </label>
							 </td>
						 </tr>
						 
						 <tr>
							 <th class="tds1">관리자 메모</th>
							 <td class="tds2">

								<table cellspacing="0" cellpadding="0" class="table-style">
		<?
		$query = "select * from "._DB_AD_MEMO." where AM_MODE = 'site' and  AM_TARGET_IDX = '".$site_data[SD_IDX]."' order by AM_KEY asc";
		$result = wepix_query_error($query);
		while($list = wepix_fetch_array($result)){
		?>
									<tr>
										<td><?=$list[AM_MEMO]?></td>
										<td><?=date("Y.m.d H:i",$list[AM_DATE])?> | <?=$list[AM_ID]?></td>
									</tr>
		<? } ?>
								</table>

							 </td>
						 </tr>	
						</table>
						
					</form>
					<div class="page-btn-wrap">
						<ul class="page-btn-left">
							<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_SITE_LIST?>'" > 
								<i class="fas fa-arrow-left"></i>
								목록으로
							</button>
						</ul>
						<ul class="page-btn-right">
							<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="goSubmit();" > 
								<i class="far fa-check-circle"></i>
								<?=$submit_btn_text?>
							</button>
						</ul>
					</div>

			</ul>
			<ul class="table-wrap-right">
				
			</ul>

		</div>
	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>