<?
if( $_POST['quickmode'] == "on" ){
	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>오나디비</ul>
</div>

<div class="left-menu-wrap">
	<ul <? if( $pageName == "member_list_onadb") echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/onadb/member_list.php'"><li>오나비디 회원</li></ul>
	<ul <? if( $pageName == "prd_comment") echo "class='leftMenuNow' "; ?> onclick="location.href='/admin2/onadb/prd_comment.php'"><li>상품 코멘트</li></ul>
</div>
