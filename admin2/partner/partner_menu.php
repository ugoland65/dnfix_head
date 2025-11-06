<?
if( $_POST['quickmode'] == "on" ){
	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>파트너 관리</ul>
</div>

<div class="left-menu-mid-title">
	<ul>에이전시 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "agency_list") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PARTNER_AG_LIST?>'"><li>에이전시 목록</li></ul>
	<ul <? if( $pageName == "agency_reg") echo "class='leftMenuNow' "; ?>onclick="location.href='<?=_A_PATH_PARTNER_AG_REG?>'"><li>에이전시 등록</li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>제휴샵 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "partner_shop_list") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PARTNER_ALLIANCE_LIST?>'"><li>제휴샵 목록</li></ul>
	<ul <? if( $pageName == "partner_shop_reg") echo "class='leftMenuNow' "; ?>onclick="location.href='<?=_A_PATH_PARTNER_ALLIANCE_REG?>'"><li>제휴샵 등록</li></ul>
</div>