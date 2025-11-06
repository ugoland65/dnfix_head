<?
include "../lib/inc_common.php";
?>

<form id="form1">
<input type="hidden" name="a_mode" value="orderSheetShopMake">

<table class="table-basic">
	<tr>
		<th>주문처 이름</th>
		<td><input type='text' name='oog_name' id='oog_name' size='20' value="" style="width:200px;"> 중복불가</td>
	</tr>
	<tr>
		<th>주문처 코드</th>
		<td>
			<input type='text' name='oog_code' id='oog_code' value="" style="width:200px;">
			<div class="admin-guide-text">
				- 중복불가<br>
				- 영문, 영문+숫자로된 업체고유 코드 5자리정도..
			</div>
		</td>
	</tr>
	<tr>
		<th>주문처 국가</th>
		<td>
			<div class="radio-wrap">
				<label><input type="radio" name="oog_group" value="jp" checked ><span>일본</span></label>
				<label><input type="radio" name="oog_group" value="cn" ><span>중국</span></label>
				<label><input type="radio" name="oog_group" value="ko" ><span>국내</span></label>
				<label><input type="radio" name="oog_group" value="dol" ><span>달러 국가</span></label>
				<label><input type="radio" name="oog_group" value="etc" ><span>기타</span></label>
			</div>
		</td>
	</tr>
</table>
</form>

<div class="submit-btn-wrap text-center m-t-10">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="orderSheetShopMake.submit(this)">주문처 생성</button>
<div>

<script type="text/javascript"> 
<!-- 
var orderSheetShopMake = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		submit : function(obj) {

			var oog_name = $("#oog_name").val();

			if( oog_name == "" ||  oog_name == null ){
				showAlert("Error", "주문처 이름을 입력해주세요.", "alert2" );
				return false; 
			}

			var oog_code = $("#oog_code").val();

			if( oog_code == "" ||  oog_code == null ){
				showAlert("Error", "주문처 코드를 입력해주세요.", "alert2" );
				return false; 
			}

			$(obj).attr('disabled', true);
			var formData = $("#form1").serializeArray();

			$.ajax({
				url: "processing.order_sheet.php",
				data : formData, 
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "성공", "주문처 생성이 완료되었습니다.");
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		}
	};

}();
//--> 
</script> 