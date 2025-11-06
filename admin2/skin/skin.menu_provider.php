<div class="left-menu-title">
	<ul>공급사 관리</ul>
</div>

<div class="left-menu-mid-title">
	<ul>공급사 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $_pageN == "supplier") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/supplier'"><li>공급사 관리</li></ul>
	<ul <? if( $_pageN == "prd_provider") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/prd_provider'"><li>공급사 등록상품 관리</li></ul>
	<ul <? if( $_pageN == "godo_brand_matching" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/godo_scm_matching'"><li>고도몰 공급사상품 매칭</li></ul>	
	<ul <? if( $_pageN == "supplier_product" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/supplier_product'"><li>공급사 상품 가져오기</li></ul>
	<ul <? if( $_pageN == "supplier_product_match" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/supplier_product_match'"><li>공급사 외부 매칭</li></ul>
</div>