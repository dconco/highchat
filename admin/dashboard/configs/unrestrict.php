<?php
    session_start();
    $id = $_POST["id"];
    $user_id = $_SESSION["user_id"];
    
    if (!isset($user_id) || !isset($id)) {
        echo "419";
        exit;
    }
    
    include_once "../../../configs/db.php";
    $sql = "SELECT * FROM users WHERE user_id = {$user_id}";
    $sqlQuery = $conn->query($sql)->fetch_object();
    
    if ($sqlQuery->admin === "false") {
        echo "Unauthorized!";
        exit;
    }
    

    $sql_banned = "UPDATE users SET acct_status = 'unrestricted' WHERE user_id = $id";
    if ($conn->query($sql_banned)) {
        echo "Successfully Unrestricted User " . $id . " 😓😞";
    } else {
        echo "An Error Occurred!";
    }
    exit;