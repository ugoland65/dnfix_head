<div class="page-title">
	<h1>My page</h1>
</div>
<div class="u-wrap">
	<div class="u-layout">
		<ul class="u-profile">
			<div class="profile-img"><img src="/public/onadb/img/non-profile.png" /></div>
			<div class="profile-box">
				<ul>
                    Level : <b><?=$auth['level'] ?? 0?></b>
                </ul>
				<ul>
                    포인트 : <b><?=number_format($auth['point'] ?? 0)?></b>
                </ul>
				<ul>
                    기여도 점수 : <b><?=$auth['score'] ?? 0?></b>
                </ul>
			</div>
		</ul>
		<ul class="u-body">

			<div class="form-wrap">
				<form id="mypage_form">
				<input type="hidden" name="id" value="<?=$auth['id'] ?? ''?>" >
					<div class="form-group">
						<label for="nick">닉네임</label>
						<input type="email" class="form-control" id="nick" name="nick" value="<?=$auth['nick'] ?? ''?>" placeholder="이메일을 입력하세요">
						<!-- <p class="help-block">닉네임 변경은 ( 500 point ) 차감됩니다.</p> -->
					</div>
					<div class="form-group m-t-10">
						<label for="exampleInputEmail1">가입시 이메일</label>
						<p class="value"><?=$auth['email'] ?? ''?></p>
						<p class="help-block">가입시 이메일은 수정이 불가능 합니다.</p>
					</div>
					<div class="form-group m-t-10">
						<label for="new_pw">패스워드 변경</label>
						<input type="password" class="form-control" id="new_pw"  name="new_pw" placeholder="변경할 암호를 입력해 주세요.">
						<p class="help-block">패스워드를 새롭게 변경하고 싶다면 입력해 주세요.</p>
					</div>
					<div class="form-group m-t-10">
						<label for="old_pw">패스워드 </label>
						<input type="password" class="form-control" id="old_pw" name="old_pw" placeholder="현재 암호를 입력해 주세요.">
						<p class="help-block">정보를 변경하기 위해서는 현재 패스워드를 입력해야 합니다.</p>
					</div>
				</form>
				
				<div class="btn-wrap m-t-20">
					<a href="#" class="btn-s1 cyan block" id="mypage_submit">정보 변경</a>
				</div>

			</div>

		</ul>
	</div>
</div>

<script type="text/javascript">
<!-- 
const userMypage = function() {

	function modify(){

		if( $('#old_pw').val() == "" ){
			$('#old_pw').focus();
			alert("정보를 변경하시려면 현재 패스워드를 입력해 주세요.");
			//showAlert("NOTICE", "정보를 변경하시려면 패스워드를 입력해 주세요.", "alert2" );
			return false;
		}

		var formData = $("#mypage_form").serializeArray();

		ajaxRequest('/mypage', formData, {} )
			.then(res => {
				if (res.success == true) {
					alert('정보변경이 완료되었습니다.');
					location.reload();
				} else {
					alert(res.msg);
				}
			})
			.catch(error => {
				//dnAlert('Error', '상태 변경 실패', 'red');
				alert('Error');
				throw new Error('AJAX 요청 실패');
			});

		/*
		$.ajax({
			url: "/processing-mypage",
			data : formData,
			type: "POST",
			dataType: "json",
			success: function(res){
				if ( res.success == true ){
					showAlert("Success", "정보변경이 완료되었습니다.", "alert2", "good" );
					return false;
				}else if ( res.success == "not_login" ){
					alert(res.msg);
					location.href='/login';
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
				//$(obj).attr('disabled', false);
			}
		});
		*/

	}

	return {
		modify
	};

}();

$(function(){

	$("#mypage_submit").click(function(){
		userMypage.modify();
	});

});
//--> 
</script> 