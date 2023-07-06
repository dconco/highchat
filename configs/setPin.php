<?php
    $pin = htmlspecialchars($_GET["pin"]);
    
    session_start();
    include_once("db.php");
    $sql = "UPDATE users SET mode = '$pin' WHERE user_id = '{$_SESSION['user_id']}'";
    
    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "Error while trying to set Security Pin! Please try again!";
    }