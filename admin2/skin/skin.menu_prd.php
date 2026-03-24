<?
if( isset($_POST['quickmode']) && $_POST['quickmode'] == "on" ){
//	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>상품관리 v.3</ul>
</div>

<div class="left-menu-mid-title">
	<ul>상품 목록</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $_pageN == "prd_reg") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/prd/prd_reg'"><li>상품 등록</li></ul>

	<?php /*
	<ul <? if( $_pageN == "prd_db") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/prd/prd_db'"><li>상품 DB (구)</li></ul>
	*/ ?>

	<ul <? if( $pageNameCode == "prd_db") echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/product/product_db'"><li>상품 DB</li></ul>
	<ul <? if( $_pageN == "prd_main") echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/product/product_stock'"><li>보유상품 관리</li></ul>

</div>
<div class="left-menu-mid-title">
	<ul>위탁 상품관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <?php if( $pageNameCode == "prd_provider" && $s_status == null) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/provider_product/list'">
		<li>위탁 상품관리</li>
	</ul>
	<ul <?php if( $pageNameCode == "prd_provider" && $s_status == '등록대기') echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/provider_product/list?s_godo_sale_status=등록대기'">
		<li>위탁 상품관리 (등록대기)</li>
	</ul>
</div>

<div class="left-menu-mid-title">
	<ul>상품 관리</ul>
</div>
<div class="left-menu-wrap">

	<?php /*
	<ul <? if( $_pageN == "prd_grouping" || $_pageN == "prd_grouping_view" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/prd/prd_grouping'"><li>상품 그룹핑 (구)</li></ul>
	*/ ?>
	
	<ul <? if( $pageNameCode == "product_grouping_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/product/grouping'"><li>상품 그룹핑</li></ul>
	<ul <? if( $_pageN == "set_prd") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/prd/set_prd'"><li>세트 상품</li></ul>
	<ul <? if( $_pageN == "hbti_prd") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/prd/hbti_prd'"><li>HBTI 상품/관리</li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>브랜드</ul>
</div>
<div class="left-menu-wrap">
	<?php /*
	<ul <?php if( $_pageN == "brand" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/prd/brand'">
		<li>브랜드 관리(구)</li>
	</ul>
	*/ ?>
	<ul <?php if( $pageNameCode == "brand" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/brand/list'">
		<li>브랜드 관리</li>
	</ul>
</div>




