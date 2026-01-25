<div class="left-menu-title">
	<ul>공급사 관리</ul>
</div>

<div class="left-menu-mid-title">
	<ul>공급사 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <?php if( $_pageN == "partners") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/partners'"><li>공급사 관리 (구)</li></ul>
	<ul <?php if( $_pageN == "supplier") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/supplier'"><li>공급사 관리</li></ul>
	<?php /*
	<ul <?php if( $_pageN == "prd_provider" || $pageNameCode == "prd_provider" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/prd_provider'">
		<li>공급사 상품관리</li>
	</ul>
	*/ ?>
	<ul <?php if( $pageNameCode == "prd_provider" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/provider_product/list'">
		<li>공급사 상품관리</li>
	</ul>

	<ul <?php if( $_pageN == "supplier_product" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/supplier_product'"><li>공급사 사이트 상품DB</li></ul>
	<ul <?php if( $_pageN == "supplier_product_match" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/supplier_product_match'"><li>공급사 상품 매칭</li></ul>
	<ul <?php if( $_pageN == "godo_brand_matching" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/provider/godo_scm_matching'"><li>고도몰 등록 공급사 상품</li></ul>	
</div>