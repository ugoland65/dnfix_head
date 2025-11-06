<div class="left-menu-title">
	<ul>여행상품 관리</ul>
</div>

<div class="left-menu-mid-title">
	<ul>상품 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "product_list") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PRODUCT_LIST?>'"><li>상품 목록</li></ul>
	<ul <? if( $pageName == "product_reg") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PRODUCT_REG?>'"><li>신상품 등록</li></ul>
	<ul <? if( $pageName == "category_list") echo "class='leftMenuNow' "; ?>onclick="location.href='<?=_A_PATH_PRODUCT_CATE_LIST?>'"><li>분류 관리</li></ul>
	<ul <? if( $pageName == "product_main_show") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PRODUCT_MAIN_SHOW?>'"><li>진열 관리</li></ul>
</div>


<div class="left-menu-mid-title">
	<ul>여행 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "area_list") echo "class='leftMenuNow' "; ?>onclick="location.href='<?=_A_PATH_PRODUCT_AREA_LIST?>'"><li>여행지역 관리</li></ul>
</div>


<div class="left-menu-mid-title">
	<ul>호텔 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "hotel_list") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PRODUCT_HOTEL_LIST?>'"><li>호텔 목록</li></ul>
	<ul <? if( $pageName == "hotel_reg") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PRODUCT_HOTEL_REG?>?mode=new'"><li>호텔 등록</li></ul>
</div>


<div class="left-menu-mid-title">
	<ul>골프장 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "golf_list") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PRODUCT_GOLF_LIST?>'"><li>골프장 목록</li></ul>
	<ul <? if( $pageName == "golf_reg") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_PRODUCT_GOLF_REG?>?mode=new'"><li>골프장 등록</li></ul>
</div>