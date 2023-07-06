<?php
    session_start();
    include_once "../../configs/db.php";
    
    $user_id = htmlspecialchars($_GET["user_id"]);
    $session_id = $_SESSION["user_id"];
    
    if (!isset($user_id) && !isset($session_id) || $user_id === $session_id) {
        echo "Error! It seems the Login has Expired!";
        exit();
    }
    
    #check if user has verified his email
    $verify = "SELECT email_verify, email FROM users WHERE user_id = {$session_id}";
    $verifyRes = $conn->query($verify)->fetch_assoc();
    if ($verifyRes["email_verify"] === $verifyRes["email"]) 
    {
        $sql = "INSERT INTO follow (
                    follow,
                    follow_by
                ) 
                VALUES (
                    {$user_id},
                    {$session_id}
                )";
                
        if ($conn->query($sql)) 
        {
            ## FOLLOW OR UNFOLLOW
            $sqlFollow = "SELECT * FROM follow WHERE follow = {$user_id} AND follow_by = {$session_id}"; // if user follow him
            $sqlFollow2 = "SELECT * FROM follow WHERE follow = {$session_id} AND follow_by = {$user_id}"; // if he follow user
            
            if ($conn->query($sqlFollow)->num_rows > 0 && $conn->query($sqlFollow2)->num_rows > 0) { # if they follow eachother
                $conn->query(
                    "INSERT INTO friends (
                        user_id,
                        friend_id
                    ) 
                    VALUES (
                        {$session_id},
                        {$user_id}
                    )"
                );
                
                echo "Friends";
                exit();
            } 
            else {
                echo "Unfollow";
                exit();
            }
            echo "Unfollow";
        } 
        else {
            echo "Error while trying to Follow!";
        }
    } else {
        echo "Unable to Follow this user! You can give us some feedback if you found it wrong!";
    }