<?php 
    session_start();
    //check if the cookie is set
    if (isset($_COOKIE["user_id"])) {
        if (!isset($_SESSION["user_id"])) {    
            $_SESSION["user_id"] = $_COOKIE["user_id"]; 
        }
        header("location: /chats/");
        exit("You're already signed in!");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>REGISTER ON HIGHCHAT</title>
    <?php include_once("./extras/header.php") ?>

    <!-- CSS LINKS -->
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

    <div class="header"> 
        <div class="cont">
            <div class="logo" onclick="window.location = '/'">
                <img src="/img/icon.png" alt="">
            </div>
            <div class="appnm">Highchat</div>
            
            <div class="form">
                <div class="form_heada">SIGN UP</div>
                <form action="#">
                    <div class="set">
                        <label for="in_fn">Firstname:</label>
                        <input type="text" name="fname" id="in_fn" placeholder="Enter first name...">
                    </div>
                    
                    <div class="set">
                        <label for="in_ln">Lastname:</label>
                        <input type="text" name="lname" id="in_ln" placeholder="Enter last name...">
                        <div class="err_msg" id="error_ln"></div>
                    </div>
                    
                    <div class="set">
                        <label for="in_em">Email:</label>
                        <input type="email" name="email" class="j" id="in_em" placeholder="Enter Email...">
                        <div class="err_msg" id="error_em"></div>
                    </div>
                    
                    <div class="set">
                        <label for="in_pw">Password:</label>
                        <input type="password" name="pwd" id="in_pw1" placeholder="Enter password...">
                    </div>
        
                    <div class="set">
                        <label for="in_pw">Confirm Password:</label>
                        <input type="password" name="pwd2" id="in_pw2" placeholder="Confirm password...">
                        <div class="err_msg" id="error_ps"></div>
                    </div>
                    
                    <div class="in_sets">
                        <div class="in_set">
                            <label for="in_ct">Select Country</label>
                            <select name="country" id="in_ct">
                                <?php include_once "extras/countries.php"; ?>
                            </select>
                        </div>
                        
                        <div class="in_set" style="margin-left:5px">
                            <label for="gnr">Gender</label>
                            <select name="gender" id="gnr">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="non-binary" disabled>Non-Binary</option>
                            </select>
                        </div>
                    </div>
                    <div class="err_msg" id="error_all"></div>
                    
                    <div class="btn text-center d-flex justify-content-center">
                        <button type="submit" name="submit" id="sumt">Signup</button>
                    </div>
                    <p class="quest">
                        Already have an account?
                    </p>
                    <div class="btna">
                        <a href="/login.php">Login</a>
                    </div>
                </form>
            
                <div class="foota">
                    <div class="n">Developed by Highchat</div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="load" id="loada">
        <div class="l"></div>
    </div>
    
    
    <!-- JAVASCRIPT LINKS -->
    <script src="js/validate.js" type="text/javascript"></script>
</body>
</html>















