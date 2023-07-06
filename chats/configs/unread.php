<?php
    session_start();
    include_once "db.php";
    $user_id = htmlspecialchars($_GET["user_id"]);
    
    $sqlMsg = "SELECT * FROM messages 
                WHERE outgoing_msg_id = {$user_id} AND incoming_msg_id = {$_SESSION['user_id']} ORDER BY msg_id DESC LIMIT 1";
                                        
    if ($conn->query($sqlMsg)->num_rows > 0) {
        $msg_id = $conn->query($sqlMsg)->fetch_assoc()["msg_id"];
        
        $sql = "UPDATE messages SET status = '' WHERE msg_id = '$msg_id'";
        
        if ($conn->query($sql)) {
            echo "success";
        }
    }