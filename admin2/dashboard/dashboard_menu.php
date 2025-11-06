<?
if( $_POST['quickmode'] == "on" ){
	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>통계/현황표</ul>
</div>

<div class="left-menu-mid-title">
	<ul>부킹현황</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "calendar_booking" && $_cmode != "reg" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_DASHBOARD_CDAL_BOOKING?>'"><li>부킹현황</li></ul>
<!-- 
	<ul <? if( $pageName == "calendar_booking" && $_cmode != "reg" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_DASHBOARD_CDAL_BOOKING?>'"><li>부킹현황 (월)</li></ul>
	<ul <? if( $pageName == "calendar_booking_year" && $_cmode != "reg" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_DASHBOARD_BOOKING_YEAR?>'"><li>부킹현황 (년)</li></ul>
 -->
	<ul <? if( $pageName == "calendar_booking" && $_cmode == "reg" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_DASHBOARD_CDAL_BOOKING?>?cmode=reg'"><li>부킹 등록현황</li></ul>
	<ul <? if( $pageName == "statistics_hotel") echo "class='leftMenuNow' "; ?>onclick="location.href='<?=_A_PATH_DASHBOARD_STICS_HOTEL?>'"><li>호텔 박수통계</li></ul>
	<ul <? if( $pageName == "statistics_agency") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_DASHBOARD_STICS_AGENCY?>'"><li>에이전시 부킹통계</li></ul>

</div>

<div class="left-menu-mid-title">
	<ul>방문자</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "visit") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_VISIT_VIEW?>'"><li>접속통계</li></ul>
	<ul <? if( $pageName == "serch") echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/dashboard/search_view.php'"><li>검색통계</li></ul>
	<ul <? if( $pageName == "bridge") echo "class='leftMenuNow' "; ?> onclick="location.href='bridge_log.php'"><li>링크 클릭 통계</li></ul>
	<ul <? if( $pageName == "product") echo "class='leftMenuNow' "; ?> onclick="location.href='product_statistics.php'"><li>기간별 상품 VIEW</li></ul>
</div>

