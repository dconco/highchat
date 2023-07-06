<?php
    session_start();
    include_once "db.php";
    
    $country = htmlspecialchars($_POST["country"]);
    $pwd = $conn->real_escape_string(htmlspecialchars($_POST["pwd"]));
    $fname = $conn->real_escape_string(htmlspecialchars($_POST["fname"]));
    $lname = $conn->real_escape_string(htmlspecialchars($_POST["lname"]));
    $username = $conn->real_escape_string(htmlspecialchars($_POST["username"]));
    $email = $conn->real_escape_string(htmlspecialchars($_POST["email"]));
    $about = $conn->real_escape_string(htmlspecialchars($_POST["about"], ENT_QUOTES));
    $newPwd = $conn->real_escape_string(htmlspecialchars($_POST["newPwd"]));
    $newConPwd = $conn->real_escape_string(htmlspecialchars($_POST["newConPwd"]));
    
    $lower1 = strtolower($fname);
    $lower2 = strtolower($lname);
    $dave = str_replace(" ", "", $lower1 . $lower2);
    
    include_once "./namesIgnore.php";
    
    $sqlPwd = "SELECT password FROM users WHERE user_id = '{$_SESSION['user_id']}'";
    $res = $conn->query($sqlPwd)->fetch_assoc();
        
    //firstname update
    if (!empty($fname) && strlen($fname) >= 3 && strlen($fname) <= 15) {
        if (in_array($lower1, $names) || $dave == "daveconco" || $dave == "spyrochat" || $dave == "highchat" || $dave == "highchats" || $dave == "onipededavid") {
            echo "The Firstname Provided is Forbidden! ";
        } else {
            if (ctype_space($fname)) {
                echo "Firstname should not contain only Space! ";
            } else {
                $sql = "UPDATE users SET firstname = '$fname' WHERE user_id = '{$_SESSION['user_id']}'";
                
                // check if the user is updated successfully
                if ($conn->query($sql) === TRUE) {
                    echo "success";
                } else {
                    echo "Unable to update Profile Informations! ";
                }
            }
        }
        
    } else if (!empty($fname) && strlen($fname) < 3 || strlen($fname) > 15) {
        echo "Firstname should be less than 16 and greater than 2! ";
    }
    
    //lastname update
    if (!empty($lname) && strlen($lname) >= 3 && strlen($lname) <= 15) {
        if (in_array($lower2, $names) || $dave == "daveconco" || $dave == "spyrochat" || $dave == "highchat" || $dave == "highchats" || $dave == "onipededavid") {
            echo "The Lastname Provided is Forbidden!";
        } else {
            if (ctype_space($lname)) {
                echo "Lastname should not contain only Space! ";
            } else {
                if ($lname === "???") {
                    $sql = "UPDATE users SET lastname = '' WHERE user_id = '{$_SESSION['user_id']}'";
                } else {
                    $sql = "UPDATE users SET lastname = '$lname' WHERE user_id = '{$_SESSION['user_id']}'";
                }
                
                // check if the user is updated successfully
                if ($conn->query($sql) === TRUE) {
                    echo "success";
                } else {
                    echo "Unable to update Profile Informations! ";
                }
            }
        }
        
    } else if (!empty($lname) && strlen($lname) < 3 || strlen($lname) > 15) {
        echo "Lastname should be less than 16 and greater than 2! ";
    }
    
    //username update
    if (!empty($username)) {
        if (strlen($username) >= 3 || strlen($username) <= 10) {
            $sqlGet = "SELECT username FROM users WHERE username = '$username'";
            $username_small = strtolower($username);
            
            if ($conn->query($sqlGet)->num_rows === 0) { // if username not taken
                $sql = "UPDATE users SET username = '$username_small' WHERE user_id = '{$_SESSION['user_id']}'";
                
                // check if the user is updated successfully
                if ($conn->query($sql) === TRUE) {
                    echo "success";
                } else {
                    echo "Unable to update Profile Informations! ";
                }
            } else {
                echo "The Username is not Available! ";
            }
        } else {
            echo "The Username should be less than 11 & greater than 2! ";
        }
    }
    
    //email update
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sqlGet = "SELECT email FROM users WHERE email = '$email'";
        
        if ($conn->query($sqlGet)->num_rows === 0) {
            $sql = "UPDATE users SET email = '$email' WHERE user_id = '{$_SESSION['user_id']}'";
            
            // check if the user is updated successfully
            if ($conn->query($sql) === TRUE) {
                echo "success";
            } else {
                echo "Unable to update Profile Informations! ";
            }
        } else {
            echo "The Email is associated with another User! ";
        }
        
    } else if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid Email Address! ";
    }
    
    //country update
    if (!empty($country)) {
        $sql = "UPDATE users SET country = '$country' WHERE user_id = '{$_SESSION['user_id']}'";
        
        // check if the user is updated successfully
        if ($conn->query($sql) === TRUE) {
            echo "success";
        } else {
            echo "Unable to update Profile Informations! ";
        }
        
    }
    
    //about bio update
    if (!empty($about)) {
        $sql = "UPDATE users SET about = '$about' WHERE user_id = '{$_SESSION['user_id']}'";
        
        // check if the user is updated successfully
        if ($conn->query($sql) === TRUE) {
            echo "success";
        } else {
            echo "Unable to update Profile Informations! ";
        }
        
    }
    
    //new password update
    if (!empty($newPwd) && strlen($newPwd) > 5) {
        if ($newPwd === $newConPwd) {
            // if password matches the one in database
            if (password_verify($pwd, $res["password"])) {
                
                $nP = password_hash($newPwd, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = '$nP' WHERE user_id = '{$_SESSION['user_id']}'";
                
                // check if the user is updated successfully
                if ($conn->query($sql) === TRUE) {
                    echo "success";
                } else {
                    echo "Unable to update Profile Informations! ";
                }
                
            } else {
                echo "The Confirm Old Password is incorrect! ";
            }
        } else {
            echo "The new Password doesn't match! ";
        }
        
    } else if (!empty($newPwd) && strlen($newPwd) <= 5) {
        echo "New Password should be greater than 5! ";
    }
    
    
    // profile picture upload
    if (isset($_FILES["image"]) && !empty($_FILES["image"]["name"])) {
        $img_name = $_FILES["image"]["name"];
        $img_size = $_FILES["image"]["size"];
        $tmp_name = $_FILES["image"]["tmp_name"];
        
        $img_ext = end(explode(".", $img_name));
        $exts = ["png", "jpg", "jpeg", "webp", "gif"];
        
        if (in_array($img_ext, $exts) === TRUE) {
            $new_img_name = "HighChat_"  . date("d_m_Y") . "_" . rand(time(), 999999999) . "_" . $img_name;
            
            if ($img_size <= 500000) {
                if (move_uploaded_file($tmp_name, "../profile_pictures/" . $new_img_name)) {
                    $curImg = "SELECT img FROM users WHERE user_id = '{$_SESSION['user_id']}'";
                    $imgRes = $conn->query($curImg)->fetch_assoc();
                    if ($imgRes["img"] !== "user.png") {
                        unlink("../profile_pictures/" . $imgRes["img"]);
                    }
                    
                    $sql = "UPDATE users SET img = '$new_img_name' WHERE user_id = '{$_SESSION['user_id']}'";
                    
                    if ($conn->query($sql) === TRUE) {
                        echo "success";
                    }
                }
            } else {
                echo "Image size should not be greater than 500kb! ";
            }
        } else {
            echo "Choose a real Image! ";
        }
    }