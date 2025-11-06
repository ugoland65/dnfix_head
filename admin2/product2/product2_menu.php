<?
if( $_POST['quickmode'] == "on" ){
	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>상품 관리</ul>
</div>

<div class="left-menu-mid-title">
	<ul>상품관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "prd2_list" ) echo "class='leftMenuNow' "; ?> <? /* onclick="location.href='/admin2/product2/prd2_list.php'" */ ?>><li><s> 상품관리 (작업중) </s></li></ul>
	<ul <? if( $pageName == "prd2_check" ) echo "class='leftMenuNow' "; ?> <? /* onclick="location.href='/admin2/product2/prd2_check.php'"*/ ?>><li><s> 상품검수 (작업중) </s></li></ul>
	<ul <? if( $pageName == "prd2_stock" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/product2/prd2_stock.php?mode=ONAHOLE'"><li>재고관리</li></ul>
	<ul <? if( $pageName == "prd2_stock_excel" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/product2/prd2_stock_excel.php'"><li>일일 재고관리 (엑셀)</li></ul>
	<ul <? if( $pageName == "prd2_set_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/product2/prd2_set_list.php'"><li>세트상품 관리</li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>주문서 관리</ul>
</div>
<div class="left-menu-wrap">
	<!-- <ul <? if( $pageName == "order_sheet" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/product2/order_sheet.php'"><li>주문서 생성</li></ul> -->
	<!-- <ul <? if( $pageName == "order_sheet2" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/product2/order_sheet_test2.php'"><li>주문서 v2</li></ul> -->
	<ul <? if( $pageName == "order_sheet3" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/product2/order_sheet_test3.php'"><li>주문서 v3</li></ul>
	<ul <? if( $pageName == "order_sheet_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/product2/order_sheet_list.php'"><li>주문서 목록</li></ul>
	<ul <? if( $pageName == "cafe24_sms" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/product2/cafe24_sms.php'"><li>재입고신청</li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>브랜도 & 제조사 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "brand_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_BRAND_LIST?>?mode=ONAHOLE'"><li>브랜드 리스트</li></ul>

<!-- 
	<ul <? if( $pageName == "brand_group_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/product2/brand_group_list.php'"><li>브랜드 정렬</li></ul>
	<ul <? if( $pageName == "maker_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_MAKER_LIST?>'"><li>제조사 리스트</li></ul>
 -->

</div>

<div class="left-menu-mid-title">
	<ul>구조 설정 관리</ul>
</div>
<div class="left-menu-wrap">
	<!-- <ul <? if( $pageName == "category_list") echo "class='leftMenuNow' "; ?>onclick="location.href='<?=_A_PATH_PRODUCT_CATE_LIST2?>'"><li>분류 관리</li></ul> -->
	<ul <? if( $pageName == "structure_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_STRUCTURE_LIST?>'"><li>구조 설정 리스트</li></ul>
	<ul <? if( $pageName == "keyword_view" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_KEYWORD?>'"><li>키워드 보기</li></ul>

</div>


