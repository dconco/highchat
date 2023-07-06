<?php 
    session_start();
    //check if the cookie is set
    if (isset($_COOKIE["user_id"])) {
        if (!isset($_SESSION["user_id"])) {  
            $_SESSION["user_id"] = $_COOKIE["user_id"]; 
        }
    } else {
        header("location: /login.php?next=/chats/archived.php");
        die("Session Expired! Login Again!");
    }
   
    include_once("./configs/db.php");
    
    $user_id = $_COOKIE["user_id"];

    // get all achieved users from the database
    $sql = "SELECT * FROM users WHERE user_id IN 
                (SELECT achieve FROM achieve WHERE achieve_by = '$user_id') 
                ORDER BY status DESC";
                
    $result = $conn->query($sql);
    
    //get only the loggedin user from the database
    $sqlUser = "SELECT * FROM users WHERE user_id = '$user_id'" or die();
    $user = $conn->query($sqlUser)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>VIEW ACHIEVED CHATS | HIGHCHAT</title>
    <?php include_once("../extras/header.php") ?>

    <!-- CSS LINKS -->
    <link rel="stylesheet" href="./css/index.css">
    <!-- JAVASCRIPT LINKS -->
    <script src="./js/index.js" type="text/javascript"></script>
</head>
<body>
    <header>
        <!-- TOP NAVIGATION -->
        <div class="navbar bg-secondary fixed-top" data-aos="fade-down">
            <div class="container-fluid">
                <div class="navbar-brand" data-aos="zoom-out" data-aos-delay="700">
                    <a href="/"><img src="../img/icon.png" alt="" style="width:60px"></a>
                    <span class="text-light logo-text">HighChat - Archived</span>
                </div>
            </div>
          
            <!-- SEARCH FIELDS -->
            <div class="container search-wrapper input-group" data-aos="slide-left" data-aos-delay="1000">
                <input type="search" id="searchArchive" class="form-control" placeholder="Search Archived List...">
                <button class="btn btn-success" id="search_btn" onclick="$('#searchArchive').focus()"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </header>
    
    <!-- MAIN CONTENT -->
    <main class="container flex-column">
        <?php 
            if ($result->num_rows > 0) {
                include_once "../encryption/decrypt.php";
                include_once("configs/archive_users.php");
                echo $output;
            } else {
                echo "<h3 class='text-center' style='font-size:20px;'>No Users on Archived List!</h3>";
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
            <h4 class="text-left block m-auto"><i class="bi bi-dash-circle-fill" style="font-size:15px"></i> <span></span></h4>
            <h4 class="text-left delete m-auto"><i class="bi bi-trash-fill" style="font-size:17px"></i> Delete Chat</h4>
            <h4 class="text-left unarchive m-auto"><i class="bx bxs-archive-out" style="font-size:17px"></i> Unarchive Chat</h4>
            <h4 class="text-left read m-auto"><i class="bi bi-circle-fill" style="font-size:15px"></i> <span></span></h4>
            <h4 class="text-center menu-close m-auto"><i class="bi bi-x-circle" style="font-size:2pc"></i></h4>
        </div>
    </div>
    
    <!-- FOOTER -->
    <footer class="container d-flex flex-row m-auto">
        <a href="/chats/"><i class="bi bi-messenger text-dark" style="font-size:27px"></i></a>
        <a href="/groups/"><i class="bi bi-people-fill text-dark"></i></a>
        <a href="/users/"><i class="bi bi-person-plus-fill text-dark"></i></a>
        <a href="/story/"><i class="bx bx-edit text-dark"></i></a>
        <a href="/chats/archived.php" name="active"><i class="bx bxs-archive-in text-success"></i></a>
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
        function Menu(user_id, status, block) 
        {
            $(".menu").slideToggle();
            $(".menu").attr("user_id", `${user_id}`);
            
            //status read/unread
            if (status === "seen") {
                $(".txt .read span").html("Mark as Unread");
            } else {
                $(".txt .read span").html("Mark as Read");
            }
            
            //block/unblock
            if (block == "true") {
                $(".txt .block span").html("Unblock User");
            } else {
                $(".txt .block span").html("Block User");
            }
        }
        
        /* Mark as Read/Unread Function */
        $(".txt .read").click(() => 
        {
            let user_id = $(".menu").attr("user_id");
            
            if ($(".txt .read span").html() == "Mark as Read") 
            {
                $.ajax({
                    type: "GET",
                    url: "/chats/configs/read.php",
                    data: `user_id=${user_id}`,
                    success: function() {
                        Alert("You've Read this Chat!")
                        window.location.reload();
                    }
                })
            } else 
            {
                $.ajax({
                    type: "GET",
                    url: "/chats/configs/unread.php",
                    data: `user_id=${user_id}`,
                    success: function() {
                        Alert("You've Unread this Chat!")
                        window.location.reload();
                    }
                })
            }
        })
        
        /* Block/Unblock Function */
        $(".txt .block").click(() => 
        {
            let user_id = $(".menu").attr("user_id");
            
            if ($(".txt .block span").html() == "Block User") 
            {
                $.ajax({
                    type: "GET",
                    url: "/chats/configs/block.php",
                    data: `user_id=${user_id}`,
                    success: function(data) {
                        if (data === "success") {
                            Alert("You've Blocked this User!")
                            window.location.reload();
                        } 
                        else {Alert(data)}
                    }
                })
            } else 
            {
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
        
        /* Delete Chats Function */
        $(".txt .delete").click(() => 
        {
            let user_id = $(".menu").attr("user_id");
            
            if (confirm("Deleted Chats cannot be undone! Are you sure to proceed?")) 
            {
                $.ajax({
                    type: "GET",
                    url: "/chats/configs/clearChats.php",
                    data: `incoming_id=${user_id}`,
                    success: function() {
                        Alert("You've successfully cleared all Messages!")
                        window.location.reload();
                    }
                })
                window.location.reload();
            }
        })
        
        /* Unarchive Chats Function */
        $(".txt .unarchive").click(() => 
        {
            let user_id = $(".menu").attr("user_id");
        
            $.ajax({
                type: "GET",
                url: "/chats/configs/removeAchieve.php",
                data: `user_id=${user_id}`,
                success: function(data) {
                    Alert(data);
                }
            })
        })
    
        /* SEARCH ARCHIVE USER FUNCTION */
        let main = $("main").html();
        $("#searchArchive").on("keyup input change select", () => {
            let q = $("#searchArchive").val();
            
            if (q !== "") {
                $.ajax({
                    type: "GET",
                    url: "/chats/configs/searchArchived.php",
                    data: "q=" + q,
                    success: function(data) {
                        $("main").html(data);
                    }
                })
            } else {
                $("main").html(main);
            }
        })
        
        /* CUSTOM ALERT FUNCTION */
        function Alert(msg) 
        {
            $(function() {
                $("#alert div").text(msg);
                $("#alert").fadeIn("slow");
                
                setTimeout(function() {
                    $("#alert").fadeOut("slow");
                }, 4000);
            });
        }
    </script>
</body>
</html>