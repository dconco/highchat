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
        $sql = "DELETE FROM follow WHERE follow = {$user_id} AND follow_by = {$session_id}";
                
        if ($conn->query($sql)) {
            ## FOLLOW OR UNFOLLOW
            $sqlFollow = "SELECT * FROM follow WHERE follow = {$user_id} AND follow_by = {$session_id}"; // if user follow him
            $sqlFollow2 = "SELECT * FROM follow WHERE follow = {$session_id} AND follow_by = {$user_id}"; // if he follow user
            
            if ($conn->query($sqlFollow2)->num_rows > 0) { # if he follow user
                $conn->query(
                    "DELETE FROM friends WHERE 
                        (user_id = {$session_id} AND friend_id = {$user_id})
                        OR (user_id = {$user_id} AND friend_id = {$session_id})"
                );
                
                echo "Follow Back";
                exit();
            } 
            else { # if he did not follow user
                echo "Follow";
                exit();
            }
            echo "Follow";
        } else {
            echo "Error while trying to Unfollow!";
        }
    } else {
        echo "Unable to Unollow this user! You can give us some feedback if you found it wrong!";
    }