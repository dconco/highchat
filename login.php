<?php 
    session_start();
    //check if the cookie is set
    if (isset($_COOKIE["user_id"])) {
        if (!isset($_SESSION["user_id"])) {    
            $_SESSION["user_id"] = $_COOKIE["user_id"]; 
        }
        header("location: /chats/");
        die("You're already signed in!");
    }
    
    //check if next in url is set
    $next = htmlspecialchars($_REQUEST["next"]);
    if (isset($next)) {
        echo "<script>var next = '$next';</script>";
    }
?>
<!DOCTYPE html>
<html>

<head>
    <?php include_once "./extras/header.php"; ?>
    
    <link rel="stylesheet" href="./css/style.css">
    <title>LOGIN TO HIGHCHAT</title>
</head>
<body>

    <div class="header">
        <div class="cont">
            <div class="logo">
                <img src="/img/icon.png" alt="">
            </div>
            <div class="appnm">Highchat</div>
            
            <div class="form">
                <div class="form_log">LOGIN</div>
                
                <form action="#">
                    <div class="set">
                        <label for="e">Email:</label>
                        <input type="email" name="email" id="e" placeholder="Enter Email...">
                        <div class="err_msg" id="error_em"></div>
                    </div>
                    
                    <div class="set">
                        <label for="p">Password:</label>
                        <input type="password" name="pwd" id="p" placeholder="Enter password...">
                        <div class="err_msg" id="error_ps"></div>
                    </div>
                    <div class="forg" onclick="window.location = '/reset.php'">Forgotten password?</div>
                    
                    <div class="btn_l">
                        <button type="submit" class="log">Login</button>
                    </div>
                    <p class="quest">Create new account?</p>
                    <div class="btnas">
                        <a href="/signup.php">Signup</a>
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

    <script src="js/login.js"></script>

</body>
</html>