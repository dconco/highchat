<?php
    include_once("./db.php");
    
    $email = $conn->real_escape_string(htmlspecialchars($_POST["email"]));
    $pwd = $conn->real_escape_string(htmlspecialchars($_POST["pwd"]));
    $status = ["success" => "", "error" => ""];
    
    if ($email == "" || $pwd == "") {
        $status["error"] = "All fields are required!";
    } else {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        if ($conn->query($sql)->num_rows > 0) {
            $data = $conn->query($sql)->fetch_assoc();
            if (password_verify($pwd, $data["password"])) {
                $status["success"] = "Logged-In Successful!";
                
                $_SESSION["user_id"] = $data["user_id"];
                setcookie('user_id', $data["user_id"], time() + (86400 * 7), '/');
            } else {
                $status["error"] = "Incorrect Password";
            }
        } else {
            $status["error"] = "No user found as the inputted Email!";
        }
        
    }
    
    $res = json_encode($status);
    echo $res;
    