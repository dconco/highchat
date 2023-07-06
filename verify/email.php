<?php
    session_start();
    //check if the cookie is set
    if (isset($_COOKIE["user_id"])) {
        if (!isset($_SESSION["user_id"])) {  
            $_SESSION["user_id"] = $_COOKIE["user_id"]; 
        }
    } else {
        header("location: /login.php?next=/verify/email.php");
        die("Session Expired! Login Again!");
    }
    
    include_once "../configs/db.php";
    $user_id = $_SESSION["user_id"];
    
    $sql = "SELECT * FROM users WHERE user_id = {$user_id}";
    
    if ($query = $conn->query($sql)->fetch_assoc()) {
        if ($query['email'] === $query['email_verify']) {
            exit("You've already verify your Email");
        }
        
        include_once "../mail.php";
        
        //output email splitted
        $sub_email = substr($query["email"], 0, 4);
        $email_type = end(explode("@", $query["email"]));
        $email = $sub_email . "*****" . $email_type;
        
        // generate access_token
        function access_token($length)
        {
            $stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $stringLength = strlen($stringSpace);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString = $randomString . $stringSpace[rand(0, $stringLength - 1)];
            }
            return $randomString;
        }
        
        //send email
        $link = $_SERVER["HTTP_HOST"];
        
        $accessToken = access_token(150);
        $email_to = $query["email"];
        $subject = "Verify your Email to Complete Registration on HighChat!";
        
        //email message
        $message = "<div style='padding:10px; margin:10px; text-align:center; border:1.5px solid grey; border-radius:8px'>
                        <img src='{$link}/img/icon.png' style='width:100px; text-align:left; margin-left:5px' />
                        <br />
                        
                        <h2>Hello {$query['firstname']} {$query['lastname']}</h2>,
                        <br />
                        
                        <h3 style='color:black'>You're Almost Done! <br>Complete your Registration on HighChat</h3>
                        
                        <p style='font-size:16px; color:black'>To complete your registration on HighChat, Click the button below to verify your Email!</p>
                        <a href='{$link}/verify/verify_email.php?type=verify_email&email={$email_to}&key={$accessToken}'><button type='button' style='color:white; background-color:blue; padding:8px; font-size:15px; border:none; border-radius:5px'>Verify Email</button></a>
                        
                        <p style='font-size:14px; color:grey'>If your Email is verified successfully, then you'll have access to all features on HighChat. Your account won't be marked as Spam!</p>
                    </div>";
        
        if (!isset($_SESSION["verify"])) 
        {
            $mail = send_mail($email_to, $subject, $message);
            if (!$mail) {
                $output = '<div class="alert alert-danger mt-1" style="font-size:13px">Error while sending verification link!</div>';
            } 
            else {
                $newVerify = "UPDATE users SET email_verify = '{$accessToken}' WHERE user_id = '{$user_id}'";
                $conn->query($newVerify);
                
                $_SESSION["verify"] = "TRUE";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>VERIFY EMAIL | HIGHCHAT</title>
    <?php include_once("../extras/header.php") ?>
    
    <style>
        @import "../fonts/fonts.css";
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Dancing+Script:wght@600&family=Roboto+Slab:wght@500&family=Ubuntu&family=Playfair:wght@500&family=Roboto&display=swap');
        
        h3 {
            font-weight: bolder;
            font: 1.8pc Teko-Medium;
        }
        span {
            font: 1.1pc Ubuntu, Courier;
            font-weight: bolder;
        }
        span.bottom {
            margin-top: 10px;
            font: 1pc Dancing Script, cursive;
            font-weight: bolder;
        }
        a {
            text-decoration: none;
        }
        p {
            color: grey;
            margin-top: 5px;
            text-align: left;
            margin-bottom: -10px;
            font: 0.8pc Courgette-Regular;
        }
    </style>
</head>
<body class="p-4 pt-5">
    <div class="container card bg-light p-3 d-flex text-center justify-content-center" style="max-width: 400px">
        <h3 class="text-primary">VERIFY EMAIL</h3>
        <hr style="margin-top:-5px" />
        
        <span class="text-muted">
            Verification link as been sent to your Email!
            <br />
            <span style="font-size:12px"><?php echo $email ?></span>
        </span>
        
        <span class="text-muted mb-2 bottom">
            Click the Link in your Email to get Started! <a href="">Resend Link</a>
        </span>
        <?php echo $output; ?>
        
        <p>Powered by Highchat</p>
    </div>
</body>
</html>