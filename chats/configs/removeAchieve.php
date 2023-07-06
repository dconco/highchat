<?php
    session_start();
    include_once "db.php";
    $user_id = $_GET["user_id"];
    $achieve_by = $_SESSION["user_id"];
    
    $sql = "DELETE FROM achieve WHERE achieve = '$user_id' AND achieve_by = '$achieve_by'";
    
    if ($conn->query($sql) === true) {
        echo "Chat removed from Achieved, the changes will soon take place.";
    } else {
        echo "Unable to remove chat from Achieve list! The changes will soon takw place.";
    }