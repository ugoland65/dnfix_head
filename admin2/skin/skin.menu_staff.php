<?
if( isset($_POST['quickmode']) && $_POST['quickmode'] == "on" ){
//	include "../lib/inc_common.php";
}

// 변수 초기화
$_page = $_page ?? '';
$_get1 = $_get1 ?? '';
?>
<div class="left-menu-title">
	<ul>인사/업무</ul>
</div>

<div class="left-menu-mid-title">
	<ul>인사관리</ul>
</div>
<div class="left-menu-wrap">

	<?php /*
	<ul <? if( $_page == "staff") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/staff'"><li>직원목록</li></ul>
	*/ ?>

	<ul <? if( $pageCode == "staff_list") echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/staff/list'"><li>직원/계정 관리</li></ul>
	<ul <? if( $_page == "staff_holiday") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/staff_holiday'"><li>휴가/월차/반차</li></ul>
</div>
