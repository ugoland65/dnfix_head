<?
if( isset($_POST['quickmode']) && $_POST['quickmode'] == "on" ){
//	include "../lib/inc_common.php";
}

// 변수 초기화
$_page = $_page ?? '';
$_get1 = $_get1 ?? '';
?>
<div class="left-menu-title">
	<ul>인사/업무</ul>
</div>

<div class="left-menu-mid-title">
	<ul>인사관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $_page == "staff") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/staff'"><li>직원목록</li></ul>
	<ul <? if( $_page == "staff_holiday") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/staff_holiday'"><li>휴가/월차/반차</li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>업무관리</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $_page == "work_manual" || $_page == "work_manual_reg" || $_page == "work_manual_view" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/work_manual'"><li>업무 매뉴얼</li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>업무 게시판</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( ( $_page == "work_log" || $_page == "work_log_reg" || $_page == "work_log_view" ) && ( !$_get1 || $_get1 == "all" ) ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/work_log'"><li>전체보기</li></ul>
	<ul <? if( ( $_page == "work_log" || $_page == "work_log_reg" || $_page == "work_log_view" ) && $_get1 == "업무일지" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/work_log/업무일지'"><li>업무 일지</li></ul>
	<ul <? if( ( $_page == "work_log" || $_page == "work_log_reg" || $_page == "work_log_view" ) && $_get1 == "프로젝트" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/work_log/프로젝트'"><li>프로젝트</li></ul>
	<ul <? if( ( $_page == "work_log" || $_page == "work_log_reg" || $_page == "work_log_view" ) && $_get1 == "기획안" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/work_log/기획안'"><li>기획안</li></ul>
	<ul <? if( ( $_page == "work_log" || $_page == "work_log_reg" || $_page == "work_log_view" ) && $_get1 == "업무요청" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/work_log/업무요청'"><li>업무요청</li></ul>
	<ul <? if( ( $_page == "work_log" || $_page == "work_log_reg" || $_page == "work_log_view" ) && $_get1 == "공지사항" ) echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/work_log/공지사항'"><li>공지사항</li></ul>
</div>

<div class="left-menu-mid-title">
	<ul>테스트</ul>
</div>
<div class="left-menu-wrap">
	<ul <? if( $_page == "basecode") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/basecode?cate=TreeNode'"><li>Base Code</li></ul>
	<ul <? if( $_page == "basecode3") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/basecode3'"><li>Base Code3</li></ul>
	<ul <? if( $_page == "work") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/work'"><li>업무 리스트</li></ul>
	<ul <? if( $_page == "work_unit") echo "class='leftMenuNow' "; ?> onclick="location.href='/ad/staff/work_unit'"><li>업무 항목 관리</li></ul>
</div>

