<?php
extends_layout('onadb.layout.auth_layout');
?>

    <div class='login-wrap'>
        <div class="login-logo"><a href='/'><img src="/public/onadb/img/header_logo.png" alt="오나디비" /></a></div>
        <div class="login-box">
            <form method="POST" id="loginform">
				<fieldset class="login-form">
				<? if( $returnUrl ){?>
				<input type='hidden' name='returnUrl' value='<?=$returnUrl ?? '/'?>'>
				<? } ?>
				<div class='login-form'>
					<ul><label><input type='text' name='user_id' id='user_id' placeholder="아이디"></label></ul>
					<ul><label><input type='password' name='user_pw' id='user_pw' placeholder="비밀번호"></label></ul>
					<ul class="m-t-10">
						<a href="#" class="btn-s1 cyan block" id="login_submit">로그인</a>
					</ul>
					<ul class="m-t-10 login-link">
						<a href="/">메인으로</a> | 
						<a href="/join">회원가입</a>
					</ul>
				</div>
				</fieldset>
            </form>
        </div>
        <div class="join-copyright ">
            <p>Copyright ©<b>ONA DB</b> All rights reserved.</p>
            <p>help.onadb@gmail.com</p>
        </div>
    </div>

<script type="text/javascript"> 
<!--
$(function(){

    $('#login_submit').click(function(){

		if( $('#user_id').val() == "" ){
			$('#user_id').focus();
			showAlert("NOTICE", "아이디를 입력해주세요", "alert2" );
			return false;
		}

		if( $('#user_pw').val() == "" ){
			$('#user_pw').focus();
			showAlert("NOTICE", "비밀번호를 입력해 주세요.", "alert2" );
			return false;
		}

		//login.submit();
		$('#loginform').submit();
		return false;

    });

});
//--> 
</script>
<?php if (!empty($_SESSION['_flash'])): ?>
    <script>
        <?php foreach ($_SESSION['_flash'] as $type => $message): ?>
            alert("<?= addslashes($message) ?>");
        <?php endforeach; ?>
    </script>
<?php endif; ?>