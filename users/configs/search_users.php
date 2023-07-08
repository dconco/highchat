<?php
    include_once("../../configs/db.php");
    
    $user_id = $_COOKIE["user_id"];
    $q = htmlspecialchars($_GET["q"]);
    
    $sql = "SELECT * FROM users WHERE (fullname LIKE '%$q%')";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        include_once("all_users.php");
        echo $output;
    } else {
        echo "No user matches your search";
    }