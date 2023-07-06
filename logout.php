<?php 
    session_start();
    session_destroy();
    if (isset($_COOKIE["user_id"])) {
        setcookie('user_id', '', time() - (86400 * 30), '/');
        header("location: ./login.php");
    } else {
        header("location: ./login.php");
    }
