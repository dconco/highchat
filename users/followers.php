<?php 
    session_start();
    //check if the cookie is set
    if (isset($_COOKIE["user_id"])) {
        if (!isset($_SESSION["user_id"])) {  
            $_SESSION["user_id"] = $_COOKIE["user_id"]; 
        }
    } else {
        header("location: /login.php?next=/users/followers.php");
        die("Session Expired! Login Again!");
        exit();
    }
   
    include_once("../configs/db.php");
    
    $user_id = $_COOKIE["user_id"];
    
    /* get all followers from the database */
    $sql = "SELECT * FROM follow WHERE follow = {$user_id} AND (follow_by 
            NOT IN (SELECT friend_id FROM friends WHERE user_id = {$user_id}) AND follow_by 
            NOT IN (SELECT user_id FROM friends WHERE friend_id = {$user_id})) 
            ORDER BY id DESC";
            
    $result = $conn->query($sql);
    
    //get only the loggedin user from the database
    $sqlUser = "SELECT * FROM users WHERE user_id = '$user_id'" or die();
    $user = $conn->query($sqlUser)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>YOUR FOLLOWERS | HIGHCHAT</title>
    <?php include_once("../extras/header.php") ?>

    <!-- CSS LINKS -->
    <link rel="stylesheet" href="/chats/css/index.css">
    <!-- JAVASCRIPT LINKS -->
    <script src="/chats/js/index.js" type="text/javascript"></script>
    <script src="configs/all.js" type="text/javascript"></script>
</head>
<body>
    <header>
        <!-- TOP NAVIGATION -->
        <div class="navbar bg-secondary fixed-top" data-aos="fade-down">
            <div class="container-fluid">
                <div class="navbar-brand" data-aos="zoom-out" data-aos-delay="700">
                    <a href="/"><img src="/img/icon.png" alt="" style="width:60px"></a>
                    <span class="text-light logo-text">HighChat - Followers</span>
                </div>
            </div>
          
            <!-- SEARCH FIELDS -->
            <div class="container search-wrapper input-group" data-aos="slide-left" data-aos-delay="1000">
                <input type="search" id="searchUser" class="form-control" placeholder="Search your Followers...">
                <button class="btn btn-success" id="search_btn" onclick="$('#searchUser').focus()"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </header>
    
    <!-- MAIN CONTENT -->
    <main class="container d-flex flex-column">
        <!-- LINKS -->
        <div class="text-center links d-flex m-auto">
            <!-- FRIENDS -->
            <a href="/users/friends.php">
                <div class="p-1 card text-secondary bg-transparent" style="padding:10px 20px 10px 20px !important;font-size:17px">Friends</div>
            </a>
            
            <!-- FOLLOWERS -->
            <a href="/users/followers.php">
                <div class="p-1 card text-secondary active" style="padding:10px 20px 10px 20px !important;font-size:17px">Followers</div>
            </a>
            
            <!-- FOLLOWING -->
            <a href="/users/following.php">
                <div class="p-1 card text-secondary bg-transparent" style="padding:10px 20px 10px 20px !important;font-size:17px">Following</div>
            </a>
        </div>
        <hr />
        
        <!-- ALL USERS -->
        <?php 
            if ($result->num_rows > 0) {
                include_once("configs/followers.php");
                echo $output;
            } else {
                echo "<h3 class='text-center' style='font-size:20px'>You don't have any Followers yet!</h3>";
            }
        ?>
    </main>
    
    <!-- SHORT MENU -->
    <div class="container-fluid bg-light flex-column p-2 menu">
        <div class="opt text-center">
            <h3>OPTIONS</h3>
            <hr />
        </div>
        <div class="txt mx-auto">
            <h4 class="text-left lock m-auto"><i class="bi bi-lock-fill" style="font-size:18px"></i> Lock Chat</h4>
            <h4 class="text-left link m-auto"><i class="bi bi-link-45deg" style="font-size:18px; font-weight:bolder"></i> Copy Link</h4>
            <h4 class="text-left block m-auto"><i class="bi bi-dash-circle-fill" style="font-size:15px"></i> <span></span></h4>
            <h4 class="text-left message m-auto"><i class="bi bi-messenger" style="font-size:15px"></i> Send Message</h4>
            <p class="text-center verify" style="font-size:13px"></p>
            <h4 class="text-center menu-close m-auto"><i class="bi bi-x-circle" style="font-size:2pc"></i></h4>
        </div>
    </div>
    
    
    <!-- FOOTER -->
    <footer class="container d-flex flex-row m-auto">
        <a href="/chats/"><i class="bi bi-messenger text-dark" style="font-size:27px"></i></a>
        <a href="/groups/"><i class="bi bi-people-fill text-dark"></i></a>
        <a href="/users/"><i class="bi bi-person-plus-fill text-dark"></i></a>
        <a href="/story/"><i class="bx bx-edit text-dark"></i></a>
        <a href="/chats/archived.php"><i class="bx bxs-archive-in text-dark"></i></a>
        <a href="/settings.php"><i class="bi bi-gear-fill text-dark" style="font-size:27px"></i></a>
    </footer>
    
    <!-- CUSTOM ALERT DESIGNED -->
    <div id="alert" style="z-index:9999">
        <div class="d-flex text-center text-light justify-content-center fixed-bottom m-3 p-2" style="background-color:#5d5d5de5; font-size:14px; border-radius:20px; box-shadow:0.1px 0.1px 1px #dadada, -0.1px -0.1px 1px #dadada"></div>
    </div>
    
    <div id="preloader"></div>
    <h2 id="desktop-view" class="text-center">Desktop view not available for now!</h2>
    
    
    <script>
        /* User redirect Function */
        function user(user_id) {
            window.location = "/chats/message.php?user_id=" + user_id;
        }
        /* Menu Options Function */
        function Menu(user_id, block, verify) 
        {
            $(".menu").slideToggle();
            $(".menu").attr("user_id", `${user_id}`);
            
            //block/unblock
            if (block === "true") {
                $(".txt .block span").html("Unblock User");
            } else {
                $(".txt .block span").html("Block User");
            }
            
            //verify
            if (verify === "true") 
            {
                $(".txt .verify").addClass("text-success")
                $(".txt .verify").removeClass("text-danger")
                $(".txt .verify").removeClass("text-warning")
                $(".txt .verify").html("This Account has been verified by Spyrochat!");
            } 
            else if (verify === "false") 
            {
                $(".txt .verify").addClass("text-warning")
                $(".txt .verify").removeClass("text-danger")
                $(".txt .verify").removeClass("text-success")
                $(".txt .verify").html("This Account has not been verified!");
            } 
            else 
            { // Account has not verified email
                $(".txt .verify").addClass("text-danger")
                $(".txt .verify").removeClass("text-success")
                $(".txt .verify").removeClass("text-warning")
                $(".txt .verify").html("This Account has not been verified! And might be at Risk!");
            }
        }
        
        /* Block/Unblock Function */
        $(".txt .block").click(() => 
        {
            let user_id = $(".menu").attr("user_id");
            
            if ($(".txt .block span").html() == "Block User") {
                $.ajax({
                    type: "GET",
                    url: "/chats/configs/block.php",
                    data: `user_id=${user_id}`,
                    success: function() {
                        Alert("You've Blocked this User!")
                        window.location.reload();
                    }
                })
            } else {
                $.ajax({
                    type: "GET",
                    url: "/chats/configs/unblock.php",
                    data: `user_id=${user_id}`,
                    success: function() {
                        Alert("You've Unblocked this User!")
                        window.location.reload();
                    }
                })
            }
        })
    
        /* SEARCH USER FUNCTION */
        let main = $("main").html();
        $("#searchUser").on("keyup input change select", () => 
        {
            let q = $("#searchUser").val();
            
            if (q !== "") {
                $.ajax({
                    type: "GET",
                    url: "configs/search_users.php",
                    data: "q=" + q,
                    success: function(data) {
                        $("main").html(data);
                    }
                })
            } else {
                $("main").html(main);
            }
        })
        
        /* COPY LINK */
        $(".link").click(() => {
            let user_id = $(".menu").attr("user_id");
            let link = window.location.host + "/chats/message.php?user_id=" + user_id;
            window.navigator.clipboard.writeText(link)
            Alert("Link Copied to Clipboard!")
        })
        
        /* SEND MESSAGE */
        $(".message").click(() => {
            let user_id = $(".menu").attr("user_id");
            user(user_id)
        })
        
        /* CUSTOM ALERT FUNCTION */
        function Alert(msg) {
            $(function() {
                $("#alert div").text(msg);
                $("#alert").fadeIn("slow");
                
                setTimeout(function() {
                    $("#alert").fadeOut("slow");
                }, 4000);
            })
        }
    </script>
</body>
</html>