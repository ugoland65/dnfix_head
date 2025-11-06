<?
	include "../lib/inc_common.php";
	include 'board_inc.php';

	$board_view_check_data =  wepix_fetch_array(wepix_query_error("select * from "._DB_BOARD_VIEW_CHECK." where BOARD_CODE = '".$_b_code."' and UID = '".$_b_key."' "));
	$_ary_board_view_check = explode("|",$board_view_check_data[BOARD_VIEW_CHECK]);
	$_ary_board_view_check_date = explode("|",$board_view_check_data[BOARD_VIEW_CHECK_DATE]);
?>

<STYLE TYPE="text/css">
.view-check-list{ width:500px; border:1px solid #dedede; background-color:#f5f8f9; box-sizing:border-box; padding:10px;  }
.view-check-list .td{ height:30px; border-bottom:1px solid #dedede; vertical-align:middle;  }
</STYLE>
<div>
<b><?=count($_ary_board_view_check)?></b>명 확인
</div>
<div class="view-check-list display-table">
	<div class="display-table-row">
		<ul class="td display-table-cell">아이디</ul>
		<ul class="td display-table-cell">닉네임</ul>
		<ul class="td display-table-cell">읽은시간</ul>
	</div>
<? 
	for($i=0;$i<count($_ary_board_view_check);$i++){
		$view_check_cont = explode("/",$_ary_board_view_check[$i]);
		$guide_data = wepix_fetch_array(wepix_query_error("select GD_NICK from "._DB_GUIDE." where GD_ID = '".$view_check_cont[1]."' "));
?>
	<div class="display-table-row">
		<ul class="td display-table-cell"><?=$view_check_cont[1]?></ul>
		<ul class="td display-table-cell"><?=$guide_data[GD_NICK]?></ul>
		<ul class="td display-table-cell"><?=date("y-m-d H:i",$_ary_board_view_check_date[$i])?></ul>
	</div>
<? } ?>
</div>