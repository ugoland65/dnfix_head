<?
$pageGroup = "product2";
$pageName = "cafe24_sms";

include "../lib/inc_common.php";

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>재입고 알람 신청 리스트</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<form action="processing.order_sheet.php" method="post" enctype="multipart/form-data" 
			onSubmit="return confirm('파일을 올리시겠습니까?\n입력하신 내용을 다시 한번 확인해주시기 바랍니다.');">
			<input type="hidden" name="a_mode" value="cafe24Sms">

			<table cellpadding="0" cellspacing="0" border="0" class="exstyle2">
			<tr>
				<td>엑셀 파일 :</td>
				<td><input name="userfile" type="file" id="데이터 찾기" size="50"></td>
				<td><input type="submit" value=" 재입고 알람 엑셀 등록 " class="btnstyle1 btnstyle1-primary btnstyle1-sm"></td>
			</tr>
			</table>
		</form>

		<div id="" class="list-box-layout3 display-table">
			<ul class="display-table-cell width-40p v-align-top">

<div class="list-box-layout3-wrap" id="">

<table class="table-list">
<?
$query = "select * from cafe24_sms ".$_where." order by uid desc limit 0, 25";
$result = wepix_query_error($query);
while($list = wepix_fetch_array($result)){
?>
	<tr bgcolor="<?=$_tr_color?>" onclick="cafe24Sms.view('<?=$list[uid]?>')" style="cursor:pointer;">
		<td><?=$list[uid]?></td>
		<td><?=$list[reg_time]?></td>
		<td><?=$list[file_name]?></td>
		<td>
			<?=date('Y-m-d H:i:s', $list[s_date])?> ~<br>
			<?=date('Y-m-d H:i:s', $list[e_date])?>
		</td>
		<td><?=$list['count']?></td>
		<td><?=$list['reg_id']?></td>
	</tr>
<? } ?>
</table>

</div>

	</ul>
	<ul class="display-table-cell width-10"></ul>
	<ul class="display-table-cell v-align-top">

<div id="view" class="list-box-layout3-wrap">
</div>

	</ul>
</div>

	</div>
</div>

<script type="text/javascript"> 
<!-- 

var cafe24Sms = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		view : function(idx) {

			$.ajax({
				url: "cafe24_sms_view.php",
				data: { "idx":idx },
				type: "POST",
				dataType: "html",
				success: function(html){
					$("#view").html(html);
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {

				}
			});

		}
	};

}();

//가격비교 퀵 창
function comparisonQuick(idx, vmode){
	if( vmode == undefined ) vmode = "comparison"; 
	window.open("/admin2/comparison/popup.comparison_modify.php?idx="+ idx +"&vmode="+vmode, "comparison_quick_"+idx, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

function dayStock(){
	$("#stock_day").val($("#_stock_day").val());
	$("#day_stock_form").submit();
}
//--> 
</script> 

<?
include "../layout/footer.php";
exit;
?>