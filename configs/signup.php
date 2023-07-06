<?php
    session_start();
    
    include_once("db.php");
    
    $gender = htmlspecialchars($_POST["gender"]);
    $country = htmlspecialchars($_POST["country"]);
    $pwd = $conn->real_escape_string(htmlspecialchars($_POST["pwd"]));
    $pwd2 = $conn->real_escape_string(htmlspecialchars($_POST["pwd2"]));
    $fname = $conn->real_escape_string(htmlspecialchars($_POST["fname"]));
    $lname = $conn->real_escape_string(htmlspecialchars($_POST["lname"]));
    $email = $conn->real_escape_string(htmlspecialchars($_POST["email"]));
    $status = ["success" => "", "error" => ""];
    
    $lower1 = strtolower($fname);
    $lower2 = strtolower($lname);
    $dave = str_replace(" ", "", $lower1 . $lower2);
    
    include_once "./namesIgnore.php";
    
    if ($fname == "" || $lname == "" || $country == "" || $gender == "" || $email == "" || $pwd == "" || $pwd2 == "") {
        $status["error"] = "All fields are required!";
        
    } else if ($pwd !== $pwd2) {
        $status["error"] = "Password doesn't Match";
    } else if (strlen($fname) > 15 || strlen($fname) < 3 || strlen($lname) > 15 || strlen($lname) < 3) {
        $status["error"] = "Firstname & Lastname field must be less than 15 and greater than 2!";
    } else if (ctype_space($fname) || ctype_space($lname)) {
        $status["error"] = "Firstname & Lastname should not contain only Space! ";
                
    } else if (strlen($pwd) < 6) {
        $status["error"] = "Password must be greater than 5!";
        
    } else if (in_array($lower1, $names) || in_array($lower2, $names) || $dave == "daveconco" || $dave == "spyrochat" || $dave == "highchat" || $dave == "highchats" || $dave == "onipededavid") {
        $status["error"] = "The Firstname or Lastname Provided is Forbidden!";
    
    
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
                        firstname, 
                        lastname, '',
                        email, 
                        gender, 
                        password, 
                        admin, 
                        status, 
                        img, 
                        mode, 
                        about, 
                        country, 
                        otp, 
                        email_verify,
                        acct_status
                    ) VALUES (
                        '$userid', 
                        '$fname', 
                        '$lname', 
                        '$email', 
                        '$gender', 
                        '$hashPwd', 
                        'false', 
                        '$time', 
                        'user.png', 
                        'none', 
                        '', 
                        '$country', 
                        '', '', ''
                    )";
            
            if ($conn->query($sql)) 
            {
                //let user follow Spyrochat
                $sqlId = "SELECT user_id FROM users WHERE id = 1"; //select spyrochat information
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
                            
                $status["success"] = "Register Successful!";
                
                $_SESSION["user_id"] = $userid;
                setcookie('user_id', $userid, time() + (86400 * 7), '/'); //set the user cookies for 7 days
                
            } else {
                $status["error"] = "Error while trying to Register!";
            }
        }
    }
    
    $res = json_encode($status);
    echo $res;