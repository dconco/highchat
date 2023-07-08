<?php
    session_start();
    
    include_once("db.php");
    
    $gender = htmlspecialchars($_POST["gender"]);
    $country = htmlspecialchars($_POST["country"]);
    $pwd = $conn->real_escape_string(htmlspecialchars($_POST["pwd"]));
    $pwd2 = $conn->real_escape_string(htmlspecialchars($_POST["pwd2"]));
    $fname = $conn->real_escape_string(htmlspecialchars($_POST["fname"]));
    $email = $conn->real_escape_string(htmlspecialchars($_POST["email"]));
    $status = ["success" => "", "error" => ""];
    
    $ignore = strtolower($fname);
    
    include_once "./namesIgnore.php";
    
    if ($fname == "" || $country == "" || $gender == "" || $email == "" || $pwd == "" || $pwd2 == "") {
        $status["error"] = "All fields are required!";
        
    } else if ($pwd !== $pwd2) {
        $status["error"] = "Password doesn't Match";
    } else if (strlen($fname) > 30 || strlen($fname) < 3) {
        $status["error"] = "Fullname field must be less than 30 and greater than 2! ";
    } else if (ctype_space($fname)) {
        $status["error"] = "Fullname should not contain only Space! ";
                
    } else if (strlen($pwd) < 6) {
        $status["error"] = "Password must be greater than 5!";
        
    } else if (in_array($ignore, $names)) {
        $status["error"] = "Fullname Provided is Forbidden!";
    
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //email validation
        $status["error"] = "Invalid Email Address!";
    } else {
        
        $sql_email = "SELECT email FROM users WHERE email='$email'";
        $result = $conn->query($sql_email);
        if ($result->num_rows > 0) 
        {
           $status["error"] = "The Email is Associated with Another Person!";
        } 
        else
        {
            $time = time();
            $userid = rand(time(), 99999999); //generate user unique id
            $hashPwd = password_hash($pwd, PASSWORD_DEFAULT); // hash password
            
            /* Add users information to the database */
            $sql = "INSERT INTO users (
                        user_id, 
                        fullname,
                        username, 
                        email, 
                        gender, 
                        admin, 
                        status, 
                        img, 
                        country, 
                        acct_status,
                        otp,
                        password
                    ) VALUES (
                        '', 
                        '', '',
                        '', 
                        '', 
                        '', 
                        '', 
                        '', 
                        '', 
                        '', '', 
                        ''
                    )";
            
            if ($conn->query($sql)) 
            {
                //let user follow Spyrochat
                /*$sqlId = "SELECT user_id FROM users WHERE id = 1"; //select spyrochat information
                $spyro = $conn->query($sqlId)->fetch_assoc()["user_id"];
                
                $sqlAdd = "INSERT INTO follow (
                                follow,
                                follow_by
                            ) 
                            VALUES (
                                {$spyro},
                                {$userid}
                            )";
                $conn->query($sqlAdd);
                            */
                $status["success"] = "Register Successful!";
                
                $_SESSION["user_id"] = $userid;
                setcookie('user_id', $userid, time() + (86400 * 30), '/'); //set the user cookies for 30 days
                
            } else {
                $status["error"] = "Error while trying to Register!";
            }
        }
    }
    
    $res = json_encode($status);
    echo $res;