<?
if( isset($_POST['quickmode']) && $_POST['quickmode'] == "on" ){
//	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>재고/발주</ul>
</div>

<div class="left-menu-mid-title">
	<ul>발주 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $_page == "order_sheet_main") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/order/order_sheet_main'"><li>주문서 리스트</li></ul>

	<? /*
	<ul <? if( $_page == "order_sheet") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/order/order_sheet'"><li>주문서 v.4</li></ul>
	*/ ?>

	<ul <? if( $_page == "order_sheet_form") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/order/order_sheet_form'"><li>주문서 폼</li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>고도몰 연동</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $_page == "godo_order_list") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/order/godo_order_list?mode=ds'"><li>주문 가져오기</li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>재고 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $_page == "stock_chart") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/order/stock_chart'"><li>재고/원가 현황</li></ul>
	<ul <? if( $_page == "rack") echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/stock/rack_list'"><li>(RACK) 랙 관리</li></ul>
	<ul onclick='window.open("/admin2/product2/popup.brand_stock.php", "brand_stock", "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");'><li>브랜드별 재고 파일</li></ul>
	<ul <? if( $_page == "stock_excel") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/order/stock_excel'"><li>일일 재고관리 (엑셀) </li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>거래처 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $_page == "partners") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/order/partners'"><li>거래처 목록</li></ul>
</div>