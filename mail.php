<?php

function send_mail($to, $subject, $msg) {
    include('smtp/PHPMailerAutoload.php');

	$mail = new PHPMailer(); 
	$mail->SMTPDebug  = 0;
	#set debug to 0 to stop showing result 
	$mail->IsSMTP(); 
	$mail->SMTPAuth = true; 
	$mail->SMTPSecure = 'ssl'; 
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 465; 
	$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8';
  
	$mail->Username = "spyrochats@gmail.com"; #your gmail address 
	$mail->Password = "towkrahxbmgdrhoy"; #your gmail app password 
	$mail->SetFrom("spyrochats@gmail.com", "Spyrochat"); #your gmail address 

	$mail->Subject = "$subject";
	$mail->Body = "$msg"; #message
	$mail->AddAddress("$to"); #receiver
	$mail->SMTPOptions = array('ssl' =>array(
		'verify_peer'=>false,
		'verify_peer_name'=>false,
		'allow_self_signed'=>false
	));
	if (!$mail->Send()) {
		$status = false;
	} else{
	    $status = true;
	}
	
	return $status;
}
?>
