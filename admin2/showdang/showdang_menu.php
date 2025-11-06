<?
if( $_POST['quickmode'] == "on" ){
	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>쑈당몰 관리</ul>
</div>

<div class="left-menu-mid-title">
	<ul>쑈당몰 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "tag" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/showdang/tag.php'"><li>해시테그 관리</li></ul>
	<ul <? if( $pageName == "brand_link" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/showdang/brand_link.php'"><li>브랜드 링크 관리</li></ul>
	<ul <? if( $pageName == "brand_group" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/showdang/brand_group.php'"><li>브랜드 그룹 관리</li></ul>
</div>
