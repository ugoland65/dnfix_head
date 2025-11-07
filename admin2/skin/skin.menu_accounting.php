<?
if( isset($_POST['quickmode']) && $_POST['quickmode'] == "on" ){
//	include "../lib/inc_common.php";
}
?>
<div class="left-menu-title">
	<ul>회계관리</ul>
</div>

<div class="left-menu-wrap">
	<ul <? if( $_page == "payment") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/accounting/payment'"><li>결제/입금 관리</li></ul>
	<ul <? if( $_page == "work_end") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/accounting/work_end'"><li>일일 마감</li></ul>
</div>

<? if( $_ad_level == 100 ){ ?>
<div class="left-menu-mid-title">
	<ul>대표 전용</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $_page == "money_plan") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/accounting/money_plan'"><li>운영자금 계획</li></ul>
	<ul <? if( $_page == "ledge") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/accounting/ledge'"><li>입출금 장부</li></ul>
	<ul <? if( $_page == "ledge_chart") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/accounting/ledge_chart'"><li>입출금 장부 통계</li></ul>
	<ul <? if( $_page == "ledge_category") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/accounting/ledge_category'"><li>항목관리</li></ul>
</div>
<? } ?>