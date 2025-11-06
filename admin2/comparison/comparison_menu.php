<?
if( $_POST['quickmode'] == "on" ){
	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>가격비교</ul>
</div>

<div class="left-menu-mid-title">
	<ul>가격비교 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "comparison_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_COMPARISON_LIST?>'"><li>가격비교 상품 리스트</li></ul>
	<ul <? if( $pageName == "comparison_list2" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_COMPARISON_LIST2?>'"><li>재고보유 상품</li></ul>
	<ul <? if( $pageName == "comparison_reg" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_COMPARISON_REG?>'"><li>가격비교 상품 등록</li></ul>
</div>


<div class="left-menu-mid-title">
	<ul>가격비교 판매몰 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "site_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_SITE_LIST?>'"><li> 판매몰 리스트</li></ul>
	<ul <? if( $pageName == "ranking_list" ) echo "class='leftMenuNow' "; ?> onclick="location.href='ranking_list.php'"><li> 랭킹 리스트</li></ul>
	<ul <? if( $pageName == "ranking_req" ) echo "class='leftMenuNow' "; ?> onclick="location.href='ranking_req.php'"><li> 랭킹 만들기</li></ul>
</div>


<div class="left-menu-mid-title">
	<ul>순서 관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "comparison_sort" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_COMPARISON_SORT?>'"><li> 정렬 </li></ul>

</div>

