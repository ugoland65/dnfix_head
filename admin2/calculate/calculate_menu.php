<div class="left-menu-title">
	<ul><?=_LG_GNB_TRAVEL_CALCULATE?></ul>
</div>

<div class="left-menu-mid-title">
	<ul>쇼핑샵 매출</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "shop_sales_list" OR $pageName == "shop_sales_view" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_SHOP_LIST?>'" ><li>쇼핑매출 리스트</li></ul>
	<ul <? if( $pageName == "shop_sales_reg") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_SHOP_REG?>'" ><li>쇼핑매출 등록</li></ul>

</div>

<div class="left-menu-mid-title">
	<ul>정산 관리</ul>
</div>
<div class="left-menu-wrap">

</div>