<?
if( $_POST['quickmode'] == "on" ){
	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul><?=_LG_LMT_MEMBERS?><!-- 회원가입 --></ul>
</div>

<div class="left-menu-mid-title">
	<ul><?=_LG_LMT_MEMBERS_1?><!-- 일반회원 --></ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "member_list") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_MEMBER_LIST?>'"><li><?=_LG_LMT_MEMBERS_1_1?><!-- 일반회원 목록 --></li></ul>
	<ul <? if( $pageName == "member_req") echo "class='leftMenuNow' "; ?>onclick="location.href='<?=_A_PATH_MEMBER_REG?>?mode=new'"><li><?=_LG_LMT_MEMBERS_1_2?><!-- 회원 등록 --></li></ul>
</div>



<div class="left-menu-mid-title">
	<ul><?=_LG_LMT_MEMBERS_3?></ul>
</div>
<div class="left-menu-wrap">
    <ul <? if( $pageName == "admin_list") echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_MEMBER_A_LIST?>'"><li><?=_LG_LMT_MEMBERS_3_1?><!-- 운영자 목록 --></li></ul>
	<ul <? if( $pageName == "admin_reg") echo "class='leftMenuNow' "; ?>onclick="location.href='<?=_A_PATH_MEMBER_A_REG?>?mode=new'"><li><?=_LG_LMT_MEMBERS_3_2?><!-- 운영자 등록 --></li></ul>
</div>