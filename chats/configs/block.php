<?php
    session_start();
    include_once "db.php";
    $user_id = htmlspecialchars($_GET["user_id"]);
    
    $user_sql = "SELECT acct_status FROM users WHERE user_id = {$_SESSION['user_id']}";
    $user = $conn->query($user_sql)->fetch_object();
    
    if ($user->acct_status === "restricted") { //if user account is restricted
        echo "Your Account has been Restricted!";
        exit;
    }

    $sql = "INSERT INTO blocked (block, block_by)
        VALUES ('$user_id', '{$_SESSION['user_id']}')";
    
    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "There was an Uncaught Error!";
    }