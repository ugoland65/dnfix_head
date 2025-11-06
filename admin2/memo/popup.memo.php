<?
$pageGroup = "memo";
$pageName = "popup_memo";

include "../lib/inc_common.php";

	$_pn = securityVal($pn);
	$_mode = securityVal($mode);
	$_idx= securityVal($idx);

include "../layout/header_popup.php";
?>

<STYLE TYPE="text/css">
.memo-wrap{ width:100%; display:table; }
.memo-wrap-ul{  width:50%;  display:table-cell; }
.footer-btn-wrap{ width:100%; height: 60px; position:fixed; box-sizing:border-box; padding-top:8px;  bottom:0; border-top:1px solid #ddd; text-align:center; background-color:#f7f7f7; }
</STYLE>

<div class="memo-wrap">
	<ul class="memo-wrap-ul">리스트</ul>
	<ul class="memo-wrap-ul">

		<form name="memo" id="memo" action="/admin2/memo/processing.memo.php" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="a_mode" value="new" >
		<input type="hidden" name="am_mode" value="<?=$_mode?>" >
		<input type="hidden" name="am_target_idx" value="<?=$_idx?>" >
		
		<textarea name="am_memo" id="am_memo"></textarea>
		</form>
		
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="memoSubmit()"><i class="fas fa-sticky-note"></i> 저장</button>
	</ul>
</div>

<div class="footer-btn-wrap">
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="memoSubmit()"><i class="fas fa-sticky-note"></i> 저장</button>
	<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="goDel('<?=$list[SD_IDX]?>');"><i class="far fa-trash-alt"></i> 닫기</button>
</div>

<script type="text/javascript"> 
<!-- 
function memoSubmit(){
	$("#memo").submit();
}
//--> 
</script> 

<?
include "../layout/footer_popup.php";
exit;
?>