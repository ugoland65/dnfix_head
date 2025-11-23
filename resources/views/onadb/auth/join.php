<?php
extends_layout('onadb.layout.auth_layout');
?>
<div class='join-wrap'>
	<div class="join-logo"><a href='/'><img src="/public/onadb/img/header_logo.png" /></a></div>
	<div class='join-box'>

		<div id="join_step1">
			<div class='join-title'>
				회원가입
			</div>
			<form id="joinform">
			<input type="hidden" name="a_mode" value="join">
			<div class='join-form'>
				<ul>
					<label>
						<i class="far fa-check-circle" id="check_icon_ID"></i>
						<input type='text' name='join_id' id='join_id' placeholder="아이디" class="ck-input" autocomplete="off" >
						<a href="#" class="btn-s1 red mini" id="join_id_check">중복확인</a>
					</label>
				</ul>
				<ul><label><input type='password' name='password' id='password' placeholder="비밀번호"></label></ul>
				<ul><label><input type='password' name='password_ck' id='password_ck' placeholder="비밀번호 확인"></label></ul>
				<ul>
					<label>
						<i class="far fa-check-circle" id="check_icon_NICK"></i>
						<input type='text' name='join_nick' id='join_nick' placeholder="닉네임" class="ck-input" autocomplete="off" >
						<a href="#" class="btn-s1 red mini" id="join_nick_check">중복확인</a>
					</label>
				</ul>
				<ul>
					<label>
						<i class="far fa-check-circle" id="check_icon_EMAIL"></i>
						<input type='text' name='join_email' id='join_email' placeholder="이메일" class="ck-input" autocomplete="off" >
						<a href="#" class="btn-s1 red mini" id="join_email_check">중복확인</a>
					</label>
				</ul>
				<ul style="margin-top:10px;">
					<a href="#" class="btn-s1 cyan block" id="join_submit">회원가입</a>
				</ul>
				<ul style="margin-top:3px;">
					<a href="/" class="btn-s1 gray block" id="back_home">메인으로 돌아가기</a>
				</ul>
			</div>
			</form>
		</div>

		<div id="join_step2">
			<div class='join-title'>
				회원가입이 완료되었습니다.
			</div>
			<div class='join-form m-t-20'>
				<ul>
					<a href="/login" class="btn-s1 cyan block" id="go_login">로그인</a>
				</ul>
			</div>
		</div>

	</div>
	<div class="join-copyright ">
		<p>Copyright ©<b>ONA DB</b> All rights reserved.</p>
		<p>help.onadb@gmail.com</p>
	</div>
</div>

<script type="text/javascript"> 
<!-- 
const join = (() => {

    const API_ENDPOINTS = {
        check_availability: "/check-availability",
		join_register: "/join-register",
    };

	var check_join_id = "";
	var check_join_nick = "";
	var check_join_email = "";

    /**
     * 중복확인
	 * 
     * @param {string} mode - 중복확인 모드 (ID, NICK, EMAIL)
     * @param {string} value - 중복확인 값
     */
    function check( mode, value ){

        ajaxRequest(API_ENDPOINTS.check_availability, 
            { mode, value }, {})
            .then(res => {
                if (res.success == true ){

					if( mode == "ID" ){
						check_join_id = value;
					}else if( mode == "NICK" ){
						check_join_nick = value;
					}else if( mode == "EMAIL" ){
						check_join_email = value;
					}

					if( res.result == true ){
						$("#check_icon_"+ mode).attr("class","far fa-check-circle");
						alert("사용할 수 없습니다.");
						return false;
					}else{
						$("#check_icon_"+ mode).attr("class","fas fa-check-circle i-check-on");
						alert("사용이 가능합니다.");
						return false;
					}

                }else{
                    alert(res.msg);
                    return false;
                }
            })
            .catch(err => {
                console.error(err);
                alert(err.message);
                return false;
            });

        /*
        $.ajax({
            url: "/processing-member",
            data: { "a_mode":"joinCheck", "mode":mode, "value":value },
            type: "POST",
            dataType: "json",
            success: function(res){
                if (res.success == true ){

                    if( mode == "ID" ){
                        check_join_id = value;
                    }else if( mode == "NICK" ){
                        check_join_nick = value;
                    }else if( mode == "EMAIL" ){
                        check_join_email = value;
                    }

                    $("#check_icon_"+ mode).attr("class","fas fa-check-circle i-check-on");
                    showAlert("Good!", res.msg, "alert2", "good" );
                    return false;

                }else{
                    $("#check_icon_"+ mode).attr("class","far fa-check-circle");
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

	/**
	 * 회원가입
	 */
	function register(){
		
		if( check_join_id == "" ){
			$('#join_id').focus();
			alert("아이디 중복확인을 해주세요.");
			return false;
		}

		if( check_join_id != $('#join_id').val() ){
			$('#join_id').focus();
			alert("아이디 중복확인을 다시 해주세요.");
			return false;
		}

		if( check_join_nick == "" ){
			$('#join_nick').focus();
			alert("닉네임 중복확인을 해주세요.");
			return false;
		}

		if( check_join_nick != $('#join_nick').val() ){
			$('#join_nick').focus();
			alert("닉네임 중복확인을 다시 해주세요.");
			return false;
		}

		if( check_join_email == "" ){
			$('#join_email').focus();
			alert("이메일 중복확인을 해주세요.");
			return false;
		}

		if( check_join_email != $('#join_email').val() ){
			$('#join_email').focus();
			alert("이메일 중복확인을 다시 해주세요.");
			return false;
		}

		var formData = $("#joinform").serializeArray();

		ajaxRequest(API_ENDPOINTS.join_register, formData, {})
			.then(res => {
				if (res.success == true ){
					$("#join_step1").hide();
					$("#join_step2").show();
				}else{
					alert(res.msg);
					return false;
				}
			})
			.catch(err => {
				console.error(err);
				alert(err.message);
				return false;
			});

	}

	return {
		check,
		register
	};

})();

$(function(){

    $('#join_id_check').click(function(){
		if( $('#join_id').val() == "" ){
			$('#join_id').focus();
            alert("아이디를 입력해주세요");
			return false;
		}
		join.check('ID', $('#join_id').val());
    });

    $('#join_nick_check').click(function(){
		if( $('#join_nick').val() == "" ){
			$('#join_nick').focus();
            alert("닉네임을 입력해주세요");
			return false;
		}
		join.check('NICK', $('#join_nick').val());
    });

    $('#join_email_check').click(function(){
		if( $('#join_email').val() == "" ){
			$('#join_email').focus();
            alert("이메일을 입력해주세요");
			return false;
		}
		var regEmail = /[\w\-]{2,}[@][\w\-]{2,}([.]([\w\-]{2,})){1,3}$/;
		if(!regEmail.test($('#join_email').val())) {
			$('#join_email').focus();
            alert("올바른 이메일을 입력해주세요");
			return false;
		}
		join.check('EMAIL', $('#join_email').val());
    });

    $('#join_submit').click(function(){

		if( $('#join_id').val() == "" ){
			$('#join_id').focus();
            alert("아이디를 입력해주세요");
			return false;
		}

		if( $('#password').val() == "" ){
			$('#password').focus();
            alert("비밀번호를 입력해 주세요.");
			return false;
		}

		if( $('#password_ck').val() == "" ){
			$('#password_ck').focus();
            alert("비밀번호 확인을 입력해 주세요.");
			return false;
		}

		if( $('#password').val() != $('#password_ck').val() ){
            alert("비밀번호와 비밀번호 확인이 일치하지 않습니다.");
			return false;
		}

		if( $('#join_nick').val() == "" ){
			$('#join_nick').focus();
            alert("닉네임을 입력해주세요");
			return false;
		}

		if( $('#join_email').val() == "" ){
			$('#join_email').focus();
            alert("이메일을 입력해주세요");
			return false;
		}

		var regEmail = /[\w\-]{2,}[@][\w\-]{2,}([.]([\w\-]{2,})){1,3}$/;
		if(!regEmail.test($('#join_email').val())) {
			$('#join_email').focus();
            alert("올바른 이메일을 입력해주세요");
			return false;
		}

		join.register();

    });

});

//--> 
</script> 