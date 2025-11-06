<?
include_once($docRoot."/plugins/PHPMailer/PHPMailerAutoload.php");

//------------------------------------------------------------------------------------
// 이메일 보내기
function mailer($fname, $fmail, $to, $subject, $content, $type=1, $file="", $cc="", $bcc="", $host, $port, $account, $account_pw){
    if ($type != 1)
        $content = nl2br($content);

    $mail = new PHPMailer(); // defaults to using php "mail()"
	
	$mail->IsSMTP(); 
//	$mail->SMTPDebug = 2; 

/*
	$mail->SMTPSecure = "ssl";
	$mail->SMTPAuth = true; 
*/

/*
	$mail->Host = "smtp.gmail.com"; 
	$mail->Port = 465; 
	$mail->Username = "osi.answer@gmail.com";
	$mail->Password = "we12181218"; 
*/
	if($host)$mail->Host = $host; 
	if($port)$mail->Port = $port; 
/*
	if($account)$mail->Username = $account;
	if($account_pw)$mail->Password = $account_pw;
*/

    $mail->CharSet = 'UTF-8';
    $mail->From = $fmail;
    $mail->FromName = $fname;
    $mail->Subject = $subject;
    $mail->AltBody = ""; // optional, comment out and test
    $mail->msgHTML($content);
    $mail->addAddress($to);
    if ($cc)
        $mail->addCC($cc);
    if ($bcc)
        $mail->addBCC($bcc);

    if ($file != "") {
        foreach ($file as $f) {
            $mail->addAttachment($f['path'], $f['name']);
        }
    }
    //return $mail->send();

	if(!$mail->send()) {
		$result = 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
		$result = "sendSuccess";
	}

	return $result;
}


 
?>