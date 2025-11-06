<form name='file_upload_form'  id='file_upload_form' method='post' enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="a_mode" value="bankStatementExcelUpload" >

<table class="table-style border01 width-full">
	<tr>
		<th style="width:100px;">엑셀파일</th>
		<td>
			<input name="userfile" id="excel_file" type="file" id="데이터 찾기">
		</td>
	</tr>
</table>

</form>

<div class="m-t-10 text-center">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="bankStatementExcelUpload.action(this);" >업로드</button>
</div>

<!-- 
		
		<input type="hidden" name="a_mode" value="stock_excel">
		<table>
			<tr>
				<td>엑셀파일 : &nbsp;</td>
				<td><input name="userfile" id="excel_file" type="file" id="데이터 찾기"></td>
				<td><input type="submit" value=" 재고 엑셀 올리기 " class="btnstyle1 btnstyle1-success btnstyle1-sm"></td>
			</tr>
		</table>
		</form>
 -->



<script type="text/javascript"> 
<!-- 
var bankStatementExcelUpload = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		action : function( obj ) {

			var fileCheck = document.getElementById("excel_file").value;
			if( !fileCheck ){
				showAlert("Error", "파일을 첨부해 주세요", "alert2" );
				return false;
			}

			var form = $('#file_upload_form')[0];
			var fileData = new FormData(form);

			fileData.append("fileObj", $("#excel_file")[0].files[0]);

			$.ajax({
				url: "/ad/processing/accounting",
				data: fileData,
				type: "POST",
				dataType: "json",
				contentType : false,
				processData : false,
				success: function(res){
					if ( res.success == true ){

						showAlert("Good", "총 ("+ res.count +") | 중복 ("+ res.count1 +") | 신규 ("+ res.count2 +")", "alert2", "good" );
						return false;

					}else{
						showAlert("Error", res.msg, "dialog" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {

				}
			});	

		}
	};

}();
//--> 
</script> 