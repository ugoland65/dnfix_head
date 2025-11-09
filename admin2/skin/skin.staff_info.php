<?
if( $_idx ){

	$data = wepix_fetch_array(wepix_query_error("select * from admin WHERE idx = '".$_idx."' "));

	$_ad_data = json_decode($data['ad_data'], true);


	$_ad_data_arr = array(
		"address" => $_ad_data['address'] ?? '',
		"tel" => $_ad_data['tel'] ?? '',
		"contact" => array(
			"name" => $_ad_data['contact']['name'] ?? '',
			"relationship" => $_ad_data['contact']['relationship'] ?? '',
			"tel" => $_ad_data['contact']['tel'] ?? ''
		)
	);


}else{
	$_target_name = $_ad_name;

	$_date_s = date("Y-m-d");
	$_date_e = date("Y-m-d");

}

	$_state_text['1'] = "신청중";
	$_state_text['2'] = "확인중";
	$_state_text['3'] = "승인";
	$_state_text['4'] = "반려";

?>
<style type="text/css">
.ad-profile-img2{ width:100px; height:100px; box-sizing:border-box;  overflow:hidden; border-radius:50%; }
.ad-profile-img2 img{ width:100%; }
</style>

	<form id="form1">

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" value="ad_modify" >
	<input type="hidden" name="idx" value="<?=$_idx?>" >
<? }else{ ?>
	<input type="hidden" name="a_mode" value="ad_reg" >
<? } ?>

	<table class="table-style border01 width-full">

	<? if( $_idx ){ ?>
		<tr>
			<th style="width:150px;">아이디</th>
			<td>
				<?=$data['ad_id']?>
			</td>
		</tr>
		<tr>
			<th style="width:150px;">패스워드</th>
			<td>
				<input type='text' name='new_ad_pw'  value="" autocomplete="off" >
				<label><input type="checkbox" name="new_pw_change" value="ok" > 패스워드 변경시 체크</label>
			</td>
		</tr>
	<? }else{ ?>
		<tr>
			<th style="width:150px;">아이디</th>
			<td>
				<input type='text' name='ad_id'  value="<?=$data['ad_id']?>" autocomplete="off" >
			</td>
		</tr>
		<tr>
			<th style="width:150px;">패스워드</th>
			<td>
				<input type='text' name='ad_pw'  value="<?=$data['ad_pw']?>" autocomplete="off" >
			</td>
		</tr>
	<? } ?>


		<tr>
			<th style="width:150px;">이름</th>
			<td>
				<input type='text' name='ad_name'  value="<?=$data['ad_name']?>" autocomplete="off" >
			</td>
		</tr>
		<tr>
			<th style="width:150px;">닉네임</th>
			<td>
				<input type='text' name='ad_nick'  value="<?=$data['ad_nick']?>" autocomplete="off" >
			</td>
		</tr>
		<tr>
			<th>생년월일</th>
			<td>
				<div class="calendar-input" style="display:inline-block;"><input type="text" name="ad_birth"  value="<?=$data['ad_birth']?>" style="width:100px;"  autocomplete="off" ></div>
			</td>
		</tr>
		<tr>
			<th>입사일</th>
			<td>
				<div class="calendar-input" style="display:inline-block;"><input type="text" name="ad_joining"  value="<?=$data['ad_joining']?>" style="width:100px;"  autocomplete="off" ></div>
			</td>
		</tr>
		<tr>
			<th>프로필 이미지</th>
			<td>

				<div class="ad-profile-img2"><img src="/data/uploads/<?=$data['ad_image']?>" alt=""></div>

				<div class="m-t-5">
					<input name="upload_file" id="upload_file_profile" type="file" >
				</div>

				<div class="m-t-5">
					<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="staffInfo.fileUpload('profile');" >프로필 이미지 업로드</button>
				</div>

				※ 최대 200px x 200px 이상시 자동 리사이징됨
			</td>
		</tr>
		<tr>
			<th style="width:150px;">주소</th>
			<td>
				<input type='text' name='ad_address'  value="<?=$_ad_data['address']?>" autocomplete="off" >
			</td>
		</tr>
		<tr>
			<th style="width:150px;">연락처</th>
			<td>
				<input type='text' name='ad_tel'  value="<?=$_ad_data['tel']?>" autocomplete="off" >
			</td>
		</tr>
		<tr>
			<th>비상연락처</th>
			<td>
				<table class="table-style border01">
					<tr>
						<th>이름</th>
						<td><input type='text' name='ad_contact_name'  value="<?=$_ad_data['contact']['name']?>" autocomplete="off" ></td>
						<th>관계</th>
						<td><input type='text' name='ad_contact_relationship'  value="<?=$_ad_data['contact']['relationship']?>" autocomplete="off" ></td>
					</tr>
					<tr>
						<th>연락처</th>
						<td colspan="3"><input type='text' name='ad_contact_tel'  value="<?=$_ad_data['contact']['tel']?>" autocomplete="off" ></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>텔레그램 토큰</th>
			<td>
				<input type='text' name='ad_telegram_token'  value="<?=$data['ad_telegram_token']?>"  class="width-full" >
			</td>
		</tr>
		<tr>
			<th>라인 토큰</th>
			<td>
				<input type='text' name='ad_line_token'  value="<?=$data['ad_line_token']?>"  class="width-full" >
			</td>
		</tr>
	</table>
	</form>

	<!-- 파일등록 -->
	<form name='file_upload_form'  id='file_upload_form' method='post' enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="a_mode" value="adProfileFile">
		<input type="hidden" name="smode" id="file_upload_mode" >
		<input type="hidden" name="idx" value="<?=$_idx?>">
	</form>

	<div class="m-t-10 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="staffInfo.save(this);" >전송</button>
	</div>

<script type="text/javascript"> 
<!-- 
var staffInfo = function() {

	var B;

	var C = function() {
	};

	return {
		
		init : function() {

		},

		save : function(obj) {

			$(obj).attr('disabled', true);

			var formData = $("#form1").serializeArray();

			$.ajax({
				url: "/ad/processing/staff",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						//toast2("success", "충/환전 설정", "설정이 저장되었습니다.");
						alert("등록되었습니다.");
						location.reload();
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		},

		// 파일업로드
		fileUpload : function( mode ) {
		
			var fileCheck = document.getElementById("upload_file_"+ mode).value;
			if( !fileCheck ){
				showAlert("Error", "파일을 첨부해 주세요", "alert2" );
				return false;
			}

			$("#file_upload_mode").val(mode);

			var form = $('#file_upload_form')[0];
			var imgData = new FormData(form);

			imgData.append("fileObj", $("#upload_file_" + mode)[0].files[0]);

			$.ajax({
				url: "/ad/processing/staff",
				data: imgData,
				type: "POST",
				dataType: "json",
				contentType : false,
				processData : false,
				success: function(res){
					if ( res.success == true ){

						//toast2("success", "파일등록", "파일이 성공적으로 등록되었습니다.");
						alert("등록되었습니다.");
						location.reload();
						/*
						var html = '<div class="file-line m-t-5">'
							+ '<i class="far fa-save fa-flip-horizontal"></i>'
							+ ' <a href="/data/uploads/'+ res.filename +'" target="_blank">'+ res.filename +'</a>'
							+ ' :: '+ res.reg_id +' ( '+ res.reg_date +' )'
							+ ' <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.fileDel(this, \''+ mode +'\', \''+ res.idx +'\' ,\''+ res.filename +'\')" >'
							+ '<i class="fas fa-trash-alt"></i>'
							+ '</button>'
							+ '</div>';
						*/

						$("#file_line_wrap_" + mode).append(html);	
						$("#upload_file_"+ mode).val("");

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

$(function(){

	var clareCalendar2 = {
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		weekHeader: 'Wk',
		dateFormat: 'yy-mm-dd', //형식(20120303)
		autoSize: false, //오토리사이즈(body등 상위태그의 설정에 따른다)
		changeMonth: true, //월변경가능
		changeYear: true, //년변경가능
		showMonthAfterYear: true, //년 뒤에 월 표시
		buttonImageOnly: false, //이미지표시
		yearRange: '1983:2030' //1990년부터 2020년까지
	};

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar2);
	}

});
//--> 
</script> 