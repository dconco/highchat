<?php
    $msg_id = htmlspecialchars($_GET["msg_id"]);
    
    include_once("db.php");
    
    $sql = "DELETE FROM messages WHERE msg_id = $msg_id";
    if ($conn->query($sql) === TRUE) {
        echo "Deleted Successfully";
    }