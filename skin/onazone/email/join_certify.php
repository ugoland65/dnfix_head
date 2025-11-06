<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>회원 인증 메일</title>
</head>
<body>

<div style="margin:30px auto;width:400px;border:10px solid #f7f7f7">
    <div style="border:1px solid #dedede;">
        <h1 style="padding:30px 30px 0;margin:0;background:#f7f7f7;color:#555;font-size:1.4em">
            <?=$mailer_site_name?> 회원 인증 메일입니다.
        </h1>
        <span style="display:block;padding:10px 30px 30px;background:#f7f7f7;text-align:right">
            <b><?=$_user_id?></b>님 함께 해주셔서 감사합니다.
        </span>
        <p style="margin:20px 0 0;padding:30px 30px 50px;min-height:200px;height:auto !important;height:200px;border-bottom:1px solid #eee">
            아래의 인증하기 버튼을 클릭하시면 <?=$mailer_site_name?> 인증이 완료됩니다.<br>
			<a href="<?=$mailer_certify_url?>" target="_blank"><button style="background:#1779fe; border:1px solid #1779fe; font-size:20px;padding:15px 40px;color:white;font-weight:bold;margin-top:31px;margin-left:10px;border-radius:3px;height:60px;box-sizing:border-box; ">인증하기</button></a><br><br>
        </p>
    </div>
</div>

</body>
</html>