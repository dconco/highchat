<?php
    session_start();
    include_once "db.php";
    $user_id = $_GET["user_id"];
    $achieve_by = $_SESSION["user_id"];
    
    $sql = "INSERT INTO achieve (achieve, achieve_by) 
                    VALUES ('$user_id', '$achieve_by')";
    
    
    if ($conn->query($sql) === true) {
        echo "Chat Archive! Be patient, your changes will soon take place.";
    } else {
        echo "Unable to add chat to Archive list!";
    }