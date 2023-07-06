<?php 
    session_start();
    include_once("db.php");
    
    if (isset($_SESSION["user_id"])) {
        $time = time();
        $sql = "UPDATE users SET status = {$time} WHERE user_id = {$_SESSION['user_id']}";
        $conn->query($sql);
    }