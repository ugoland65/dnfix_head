<?
$pageGroup = "booking";
$pageName = "estimate_view_popup";

include "../lib/inc_common.php";

	$_bkp_idx = securityVal($key);
	$tr_data = wepix_fetch_array(wepix_query_error("select *  from  "._DB_TRAVEL_PLAN." where TR_BKP_IDX = ".$_bkp_idx));
	$bk_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOOKING." where BKP_IDX = '".$_bkp_idx."' "));

	if($tr_data[TR_IDX]){
		$_tr_key =  $tr_data[TR_IDX];
		$_view_bk_st_date = date("y-m-d",$bk_data[BKP_START_DATE]);
		$_view_bk_ed_date = date("y-m-d",$bk_data[BKP_ARRIVE_DATE]);
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



include "../layout/header_popup.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
tr{text-align:center;}
table {
  border-collapse: separate;
  border-spacing: 0 10px;

}
</STYLE>
<div id="wrap">
<table cellspacing="1" cellpadding="0" style='margin: 0 130px;'>
	<tr>
		<th><img src='/admin2/img/nirvana_mini_logo.png'> <span style='font-size:40px; vertical-align:middle;'>THE NIRVANA</span></td>
	</tr>
	<tr>
		<th>67/149 Moo5, Petchkaseam Rd. Kukkak, Takuapa, Phang nga, Thailand. 82190</th>
	</tr>
	<tr>
		<th>TEL : +66 (0)76 410 540 ~ 1 FAX : +66 (0)76 486 450 IP TEL : 070 8259 2625</th>
	</tr>
	<tr>
		<th><span style='line-height:150%; font-size:40px; '><?=$_view_tr_title?> 확정서</span></th>
	</tr>
</table>
<table  cellspacing="1" cellpadding="0" class="table-style">
	<tr>
		<th>견적가</td>
		<td colspan='3'>69만원/1인</td>
	</tr>
	<tr>
		<th>기간</td>
		<td><?=$_view_bk_st_date?> ~ <?=$_view_bk_ed_date?></td>
		<th>인원</td>
		<td>15+1TC</td>
	</tr>
	<tr>
		<th>리조트</td>
		<td>로빈슨</td>
		<th>룸타입</td>
		<td>더블룸(구.디럭스) 6트윈 + 1트리플 + 1싱글</td>
	</tr>
	<tr>
		<th>포함 사항</td>
		<td colspan='3'> <?=$_view_tr_inclusion?></td>
	</tr>
	<tr>
		<th>불포함 사항</td>
		<td colspan='3'> <?=$_view_tr_not_inclusion?></td>
	</tr>
	<tr>
		<th>기타 사항</td>
		<td colspan='3'>  <?=$_view_tr_memo?> </td>
	</tr>
</table>
<br/>
<table  cellspacing="1" cellpadding="0" class="table-style">
	<tr>
		<th>일자</td>
		<th>지역</td>
		<th>교통편</td>
		<th>시간</td>
		<th>행사일정</td>
		<th>식사</td>
	</tr>
<?	
	for($i=0;$i<count($_ary_tr_day_num);$i++){
	   $num = $i+1;
	   $tpg_food = explode("/",$_ary_tr_food[$i]);	
	   $tpg_pd_key = explode("/",$_ary_tr_pd_key[$i]);	
?>
	<tr>
		<td>제 <?=$_ary_tr_day_num[$i]?> 일 </td>
		<td><?=$_ary_tr_time[$i]?></td>
		<td><?=$_ary_tr_day_num[$i]?></td>
		<td><?=$_ary_tr_traffic[$i]?></td>
		<td><?=$_ary_tr_plan_text[$i]?></td>
		<td>조 : <?=$tpg_food[0]?> <br/> 중 : <?=$tpg_food[1]?> <br/> 석 : <?=$tpg_food[2]?></td>
	</tr>
<?}?>
</table>






</div>
<?
include "../layout/footer_popup.php";
exit;
?>