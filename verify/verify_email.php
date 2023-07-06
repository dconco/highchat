<?php
    session_start();
    //check if the cookie is set
    if (isset($_COOKIE["user_id"])) {
        if (!isset($_SESSION["user_id"])) {  
            $_SESSION["user_id"] = $_COOKIE["user_id"]; 
        }
    } else {
        $key = $_GET["key"];
        $type = $_GET["type"];
        $email = $_GET["email"];
        
        header("location: /login.php?next=/verify/verify_email.php?key={$key}&type={$type}&email={$email}");
        die("Session Expired! Login Again!");
    }

    include_once "../configs/db.php";
    $user_id = $_SESSION["user_id"];
    
    $key = $_GET["key"];
    $type = $_GET["type"];
    $email = $_GET["email"];
    
    if (isset($key) && isset($type) && isset($email)) {
        $sql = "SELECT * FROM users WHERE user_id = '{$user_id}'";
        $res = $conn->query($sql)->fetch_assoc();
        
        if ($email === $res["email"] && $key === $res["email_verify"]) {
            echo "Verification Successfull! You'll be redirected in a few seconds!";
            
            $newToken = "UPDATE users SET email_verify = '{$email}' WHERE user_id = '{$user_id}'";
            if ($conn->query($newToken)) {
                echo "<script>window.location = '/users/';</script>";
            }
        } else {
            exit("Invalid or Expired Token!");
        }
    }