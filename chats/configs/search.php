<?php
    include_once("db.php");
    
    $user_id = $_COOKIE["user_id"];
    $q = htmlspecialchars($_GET["q"]);
    
    $achieve = "SELECT achieve FROM achieve WHERE achieve_by = '$user_id'";
    $sql = "SELECT * FROM users WHERE user_id NOT IN ($achieve) AND fullname LIKE '%$q%' AND NOT user_id = '$user_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        include_once "../../encryption/decrypt.php";
        include_once("users.php");
        echo $output;
    } else {
        echo "No user matches your search";
    }