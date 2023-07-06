<?php
    session_start();
    include_once "db.php";
    $user_id = htmlspecialchars($_GET["user_id"]);
    
    $sql = "DELETE FROM blocked WHERE block = '$user_id' AND block_by = '{$_SESSION['user_id']}'";
    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "error";
    }