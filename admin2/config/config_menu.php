<?
if( $_POST['quickmode'] == "on" ){
	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>설정관리</ul>
</div>

<div class="left-menu-mid-title">
	<ul>시스템 설정</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "config_system" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_CONFIG_SYSTEM?>'"><li>환경설정</li></ul>
	<ul <? if( $pageName == "config_open_graph" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_CONFIG_OPEN_GRAPH?>'"><li>오픈그래프</li></ul>
	<ul <? if( $pageName == "config_exchange_rate" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_CONFIG_EXCHANGE_RATE?>'"><li>환율설정</li></ul>
</div>


<div class="left-menu-mid-title">
	<ul>개인 설정</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "config_personal" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_CONFIG_PERSONAL?>'"><li>개인 정보</li></ul>
</div>

<?
if( $_ad_level > 99 ){
?>
<div class="left-menu-mid-title">
	<ul>개발자 메뉴</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $pageName == "config_developer" ) echo "class='leftMenuNow' "; ?> onclick="location.href='<?=_A_PATH_CONFIG_DEVELOPER?>'"><li>개발자 설정</li></ul>
</div>
<? } ?>