<?
$pageGroup = "login";
$pageName = "login";

include "../lib/inc_common.php";
?>
<!doctype html>
<head>
	<title>디엔픽스 IBS - DNFIX Integrated Business System</title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="imagetoolbar" content="no" />

	<link rel="shortcut icon" href="<?=_A_FOLDER?>/favicon.ico?v4" />

<STYLE TYPE="text/css">
@charset "utf-8";

html, body, div, dl, dt, dd, ul, ol, li, h1, h2, h3, h4, h5, h6, pre, code, form, fieldset, legend, input, textarea, p, blockquote, th, td, img { margin:0; padding:0; }
body { background-color:#25333d; }
li{ list-style:none; }

#login_wrap{ width:660px; height:250px; margin:200px auto 0; position:relative; }
#login_box{  width:300px; height:250px; top:0; right:0; position:absolute; }
#login_logo{  width:265px; height:193px; padding-top:7px; border-right:1px solid #333f49; top:-10px; left:0; position:absolute; }

#login_input ul li{ height:40px; }
#login_input input[type=text],
#login_input input[type=password]{ width:250px; height:40px; line-height:40px; color:#fff;  padding-left:10px; border:1px solid #6edaa1; border-radius:5px; background-color:#25333d; }
#login_input input[type=submit]{ width:262px; height:40px; line-height:40px; color:#fff;   border:1px solid #6edaa1; border-radius:5px; background-color:#6edaa1; }
</STYLE>
<script src="/admin/js/jquery-1.6.2.js"></script>
<script type='text/javascript'>

$(document).ready(function(){
    var id_key = getCookie("id_key");
	var pw_key = getCookie("pw_key");
    $("#login_id").val(id_key); 
    $("#login_pass").val(pw_key); 
     
    if($("#login_id").val() != ""){
        $("#idSaveCheck").attr("checked", true); 
    }
     
    $("#idSaveCheck").change(function(){
        if($("#idSaveCheck").is(":checked")){ 
            setCookie("id_key", $("#login_id").val(), 365);
			setCookie("pw_key", $("#login_pass").val(), 365); 
        }else{ 
            deleteCookie("id_key");
			deleteCookie("pw_key");
        }
    });
     
    $("#login_id").keyup(function(){ 
        if($("#idSaveCheck").is(":checked")){ 
            setCookie("id_key", $("#login_id").val(), 365); 
        }
    });

    $("#login_pass").keyup(function(){ 
        if($("#idSaveCheck").is(":checked")){ 
            setCookie("pw_key", $("#login_pass").val(), 365); 
        }
    });


});
 
	function setCookie(cookieName, value, exdays){
		var exdate = new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var cookieValue = escape(value) + ((exdays==null) ? "" : "; expires=" + exdate.toGMTString());
		document.cookie = cookieName + "=" + cookieValue;
	}
	 
	function deleteCookie(cookieName){
		var expireDate = new Date();
		expireDate.setDate(expireDate.getDate() - 1);
		document.cookie = cookieName + "= " + "; expires=" + expireDate.toGMTString();
	}
	 
	function getCookie(cookieName) {
		cookieName = cookieName + '=';
		var cookieData = document.cookie;
		var start = cookieData.indexOf(cookieName);
		var cookieValue = '';
		if(start != -1){
			start += cookieName.length;
			var end = cookieData.indexOf(';', start);
			if(end == -1)end = cookieData.length;
			cookieValue = cookieData.substring(start, end);
		}
		return unescape(cookieValue);
	}

</script>
 </head>
 <body>

<div id="login_wrap">
	<div id="login_logo"><img src="/admin2/img/<?=_A_GLOB_LOGOFILE_LOGIN?>" alt=""></div>
	<div id="login_box">
		<form method="post" action="<?=_A_PATH_LOGIN_OK?>" name="admin_login">
		<div id="login_input">
			<ul>
				<li style="margin-bottom:10px;"><input type="text" name="login_id" id="login_id" placeholder="Admin ID" > <input type="checkbox" id="idSaveCheck"></li>
				<li style="margin-bottom:45px;"><input type="password" name="login_pass" id="login_pass" placeholder="Password"></li>
				<li><input type="submit" value="SIGN IN"></li>
			</ul>
		</div>
		</form>

	<div>
</div>

 </body>
</html>

<?
exit;
?>