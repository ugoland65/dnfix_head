<?
if( isset($_POST['quickmode']) && $_POST['quickmode'] == "on" ){
//	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>오나디비</ul>
</div>
<!-- 
<div class="left-menu-mid-title">
	<ul>대표 전용</ul>
</div>
 -->
<div class="left-menu-wrap">
	<ul <? if( $_page == "onadb_member") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/onadb/onadb_member'" ><li>onaDB 회원</li></ul>
	<ul <? if( $_page == "onadb_prd_comment") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/onadb/onadb_prd_comment'" ><li>onaDB 상품코멘트</li></ul>
	<ul <? if( $_page == "onadb_brand") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/onadb/onadb_brand'" ><li>onaDB 브랜드정렬</li></ul>
	<ul <? if( $_page == "onadb_brand") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/onadb/onadb_board/notice'" ><li>onaDB 공지사항</li></ul>
</div>
