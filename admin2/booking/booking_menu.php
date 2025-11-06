<?
if( $_POST['quickmode'] == "on" ){
	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul><?=_LG_LMT_BOOKINGS?></ul>
</div>

<div class="left-menu-mid-title">
	<ul>부킹 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "booking_group_list" OR $pageName == "booking_group_view" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_BOOKING_GROUP_LIST?>'"><li>Booking Group list</li></ul>
	<!--<ul <? if( $pageName == "booking_group_list" OR $pageName == "booking_group_view" ) echo "class='leftMenuNow' "; ?>  ><li>Booking Group list</li></ul>-->
	<ul <? if( $pageName == "booking_list" OR $pageName == "booking_view" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_BOOKING_LIST?>'"><li>Booking list</li></ul>
	<!--<ul <? if( $pageName == "booking_now_list" ) echo "class='leftMenuNow' "; ?>  ><li>Day booking list</li></ul>-->
    <ul <? if( $pageName == "New Booking Reg" ) echo "class='leftMenuNow' "; ?>  onclick="location.href='<?=_A_PATH_BOOKING_MODIFY_POPUP?>?mode=new'"><li>New Booking Reg</li></ul>
<?
if( $_ad_level > 9 ){
?>
	<ul <? if( $pageName == "del_booking_list" ) echo "class='leftMenuNow' "; ?>  onclick="location.href='<?=_A_PATH_BOOKING_LIST?>?mode=delList'"><li> Booking Delete List</li></ul>
<?
}
?>
</div>

<div class="left-menu-mid-title">
	<ul>기타 관리</ul>
</div>
<div class="left-menu-wrap">
    <ul <? if( $pageName == "fast_track_list") echo "class='leftMenuNow' "; ?> ><li>Fast Track</li></ul>
    <ul <? if( $pageName == "booking_land_fee") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_BOOKING_LAND_FEE_LIST?>'" ><li>KR Cost of Tour</li></ul> 	
    <ul <? if( $pageName == "personal_list") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PERSONAL_PAYMENT_LIST?>'" ><li>개인 결제</li></ul> 	
</div>


<div class="left-menu-mid-title">
	<ul>확정서 샘플</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "plan_template_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PLAN_TEMPLATE_LIST?>'"><li>확정서 샘플 목록</li></ul>
	<ul <? if( $pageName == "plan_template_req" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PLAN_TEMPLATE_REG?>?mode=new'"><li>확정서 샘플 등록</li></ul>
</div>