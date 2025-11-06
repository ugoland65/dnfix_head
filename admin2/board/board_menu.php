<?
if( $_POST['quickmode'] == "on" ){
	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>게시판 관리</ul>
</div>

<div class="left-menu-mid-title">
	<ul>설정</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "board_main" && $_cmode != "reg" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_BOARD_MAIN?>'"><li>게시판 설정</li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>운영 게시판</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "board_product_review" AND $_mode == "" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_BOARD_PD_REVIEW?>'"><li>상품 후기</li></ul>
<?
//가격비교
if( _A_GLOB_GNB_ACTIVE_COMPARISON == "on" ){
?>
	<ul <? if( $pageName == "board_product_review" AND $_mode == "comment" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_BOARD_PD_REVIEW?>?mode=comment'"><li>가격비교 상품후기</li></ul>
<? } ?>
</div>

<div class="left-menu-mid-title">
	<ul>일반 게시판</ul>
</div>
<div class="left-menu-wrap">
<?
	$_menu_b_code = securityVal($b_code);

	$_menu_bo_c_where = "  ";
	$_menu_bo_c_query = "select BAC_NAME, BOARD_CODE from "._DB_BOARD_A_CONFIG." ".$_menu_bo_c_where."order by UID desc ";
	$_menu_bo_c_result = wepix_query_error($_menu_bo_c_query);
	while($_menu_bo_c_list = wepix_fetch_array($_menu_bo_c_result)){
?>
	<ul <? if( $pageName != "board_main" AND $_menu_b_code == $_menu_bo_c_list[BOARD_CODE] ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_BOARD_LIST?>?b_code=<?=$_menu_bo_c_list[BOARD_CODE]?>'"><li><?=$_menu_bo_c_list[BAC_NAME]?></li></ul>
<? } ?>
</div>