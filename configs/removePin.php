<?php
    session_start();
    include_once("db.php");
    $sql = "UPDATE users SET mode = 'none' WHERE user_id = '{$_SESSION['user_id']}'";
    
    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "Error while trying to deactivate security mode! Please try again!";
    }