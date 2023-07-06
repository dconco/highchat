<?php
    session_start();
    include_once "configs/db.php";
    $pwd = htmlspecialchars($_GET["pwd"]);
    
    if (isset($_SESSION["user_id"])) {
        $sql1 = "SELECT * FROM users WHERE user_id = '{$_SESSION['user_id']}'";
        $res = $conn->query($sql1);
        
        if ($res) {
            $resFinal = $res->fetch_assoc();
            
            if (password_verify($pwd, $resFinal["password"])) 
            {
            
                if ($resFinal["img"] !== "user.png") { //if there is profile picture
                    unlink("profile_pictures/" . $resFinal["img"]);
                }
                
                $sql = "DELETE FROM users WHERE user_id = '{$_SESSION['user_id']}'";
                $sql2 = "DELETE FROM messages WHERE outgoing_msg_id = '{$_SESSION['user_id']}' OR incoming_msg_id = '{$_SESSION['user_id']}'";
                $sql3 = "DELETE FROM achieve WHERE achieve = '{$_SESSION['user_id']}' OR achieve_by = '{$_SESSION['user_id']}'";
                $sql4 = "DELETE FROM blocked WHERE block = '{$_SESSION['user_id']}' OR block_by = '{$_SESSION['user_id']}'";
                $sql5 = "DELETE FROM follow WHERE follow = '{$_SESSION['user_id']}' OR follow_by = '{$_SESSION['user_id']}'";
                $sql6 = "DELETE FROM friends WHERE user_id = '{$_SESSION['user_id']}' OR friend_id = '{$_SESSION['user_id']}'";
 
                if ($conn->query($sql) && $conn->query($sql2) && $conn->query($sql3) && $conn->query($sql4) && $conn->query($sql5) && $conn->query($sql6)) {
                   echo "success";
                } else {
                    echo "Unable to Delete Account!";
                }
            } else {
                echo "Incorrect Password!";
            }
        }
    }