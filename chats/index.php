<?php 
    session_start();
    //check if the cookie is set
    if (isset($_COOKIE["user_id"])) {
        if (!isset($_SESSION["user_id"])) {  
            $_SESSION["user_id"] = $_COOKIE["user_id"]; 
        }
    } else {
        header("location: /login.php?next=/chats/");
        die("Session Expired! Login Again!");
    }
   
    include_once("./configs/db.php");
    
    $user_id = $_COOKIE["user_id"];

    // get all users that has chats from the database without Archive
    $sql = "SELECT * FROM users WHERE user_id NOT IN 
            (SELECT achieve FROM achieve WHERE achieve_by = '$user_id')
            ORDER BY status DESC";
                
    $result = $conn->query($sql);
    
    //get only the loggedin user from the database
    $sqlUser = "SELECT * FROM users WHERE user_id = '$user_id'" or die();
    $user = $conn->query($sqlUser)->fetch_assoc();
    (!empty($user["username"]) && $username = $user["username"]);
    
    ## FOLLOW OR UNFOLLOW
    # select all followers
    $sqlFollowers = "SELECT * FROM follow WHERE follow = {$user_id} AND (follow_by 
            NOT IN (SELECT friend_id FROM friends WHERE user_id = {$user_id}) AND follow_by 
            NOT IN (SELECT user_id FROM friends WHERE friend_id = {$user_id})) 
            ORDER BY id DESC"; // select all followers
    
    # select all followings
    $sqlFollowing = "SELECT * FROM follow WHERE follow_by = {$user_id} AND (follow 
            NOT IN (SELECT friend_id FROM friends WHERE user_id = {$user_id}) AND follow 
            NOT IN (SELECT user_id FROM friends WHERE friend_id = {$user_id})) 
            ORDER BY id DESC"; //select all followings
    
    # count the numbers of followers and followings
    $followers = $conn->query($sqlFollowers);
    $following = $conn->query($sqlFollowing);
    
    ## FRIENDS
    $sqlFriends = "SELECT * FROM friends WHERE user_id = {$user_id} OR friend_id = {$user_id}";
    $friends = $conn->query($sqlFriends);
    
    $user_link = $_SERVER['HTTP_HOST'] . "/chats/message.php?" . ($username ? "username=" . $username : "user_id=" . $user_id);
    echo "<script>var user_link = {$user_link}</script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>HIGHCHAT | CONNECTS THE WORLD TOGETHER TO CHAT</title>
    <?php include_once("../extras/header.php") ?>

    <!-- CSS LINKS -->
    <link rel="stylesheet" href="./css/index.css">
    <!-- JAVASCRIPT LINKS -->
    <script src="./js/index.js" type="text/javascript"></script>
</head>
<body>
    <header>
        <!-- FULL NAVIGATION -->
        <div class="nav-full bg-dark">
            <div class="nav-cont d-flex flex-column">
                <i class="bi bi-x-circle nav-close"></i>
                
                <div class="img-thumbnail bg-dark rounded-circle d-flex mx-auto nav-img" oncontextmenu="download('<?php echo $user['img'] ?>')">
                    <img class="rounded-circle img-fluid d-flex mx-auto m-auto" style="object-fit:center; width:100%; height:100%" src="/profile_pictures/<?php echo $user['img'] ?>" alt="<?php echo ($user['firstname'] . '\'s Profile Picture')  ?>">
                </div>
                
                <span class="text-white text-center nav-header">
                    <?php echo $user['fullname'] ?>
                    
                    <?php if ($followers->num_rows >= 20 && $user['email_verify'] === $user['email']): ?>
                        <i class='bi-check-circle-fill' style='font-size:13px;color:green' onclick='This user has been verified by Spyrochat!'></i>
                    <?php endif; ?>
                </span>
                <hr class="nav-hr bg-white">
            </div>
            
            <!-- NAVIGATION LINKS -->
            <div class="container nav-cont2 d-flex flex-column">
                <div class="d-flex flex-row justify-content-center about">
                    <span class="badge bg-success gender"><?php echo $user['gender']; ?></span>
                    <span class="badge bg-success country"><?php echo $user['country']; ?></span>
                    <span class="badge bg-success settings"><a href="/settings.php"><i class="bi bi-gear-fill text-white" style="font-size:1pc"></i></a></span>
                </div>
                
                <p class="text-white text-center mt-3 m-1" style="font-size:0.8pc">
                    <?php 
                        echo (empty($user['about'])) ? 
                            "No About! 
                            <a href='/settings.php' class='text-white' style='font-weight:bold'>
                                Click here to write short a Bio of Yourself!
                            </a>"
                        : nl2br($user['about']); 
                    ?>
                </p>
                
                <?php if ($user['email_verify'] !== $user['email']): ?>
                   <p class="text-danger text-center mt-3" style="font-size:0.7pc;margin-bottom:-25px" onclick="Alert('You won\'t be able to access some functions on Spyrochat! And your Account will be marked as Spam!')">
                       You've not verify your Email. <code>Learn More.</code>
                       <br>
                       <a href="/verify/email.php">Verify Now!</a>
                   </p>
                <?php endif; ?>
                
                <div class="nav-link d-flex flex-column text-center">
                    <!-- FOLLOW NUMBERS DESIGN -->
                    <div class="d-flex flex-column justify-content-center mb-3 m-2" style="font-size:1.2pc">
                        <span class="badge bg-secondary p-3 m-1" onclick="Alert('<?php echo $friends->num_rows ?> Friends')"><?php echo $friends->num_rows ?> Friends</span>
                        <span class="badge bg-secondary p-3 m-1" onclick="Alert('<?php echo $followers->num_rows ?> Followers')"><?php echo $followers->num_rows ?> Followers</span>
                        <span class="badge bg-secondary p-3 m-1" onclick="Alert('<?php echo $following->num_rows ?> Following')"><?php echo $following->num_rows ?> Following</span>
                    </div>
                    
                    <!-- COPY LINK DESIGN -->
                    <div class="d-flex mb-2" id="user_link_copy">
                        <span class="badge bg-secondary p-1 pb-2 align-items-center d-flex" style="font-style:italic; font-size:12px; border-radius:4px 0 0 4px">Your Link</span>
                        <span class="bg-light p-1 pb-2" id="link_copy" style="font-style:italic; font-size:12px; overflow-x:auto; white-space:nowrap"><?php echo $user_link; ?></span>
                        <span class="badge bg-secondary pt-2 pb-2 pr-5 pl-5 align-items-center d-flex" style="font-size:14px; border-radius:0 4px 4px 0"><i class="bx bx-copy-alt"></i></span>
                    </div>
                    
                    <a href="/logout.php" class="logout text-danger">Logout</a>
                </div>
                <div class="text-center text-white" style="font-size:0.8pc; margin-top:20px; margin-bottom:15px">&copy; 2023 - <?php echo date('Y') ?> | <a class="text-white" href="/"><b>HighChat</b></a></div>
            </div>
        </div>
        
        <!-- TOP NAVIGATION -->
        <div class="navbar bg-secondary fixed-top" data-aos="fade-down">
            <div class="container-fluid">
                <div class="navbar-brand" data-aos="zoom-out" data-aos-delay="700">
                    <a href="/"><img src="../img/icon.png" alt="" style="width:60px"></a>
                    <span class="text-light logo-text">HighChat</span>
                </div>
            
                <i class="bi bi-list nav-list" data-aos="flip-left" data-aos-delay="1000"></i>
            </div>
          
            <!-- SEARCH FIELDS -->
            <div class="container search-wrapper input-group" data-aos="slide-left" data-aos-delay="1000">
                <input type="search" id="search" class="form-control" placeholder="Search...">
                <button class="btn btn-success" onclick="$('#search').focus()" id="search_btn"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </header>
    
    <!-- MAIN CONTENT -->
    <main class="container flex-column">
        <?php 
            if ($result->num_rows > 0) {
                include_once "../encryption/decrypt.php";
                include_once("configs/users.php");
                
                if (empty($output)) {
                    echo "<h3 class='text-center' style='font-size:14px'>All the Users you've chatted will Appear here! <br>To start chat, tap the User+ button below!</h3>";
                } else {
                    echo $output;
                }
            } else {
                echo "<h3 class='text-center' style='font-size:14px'>All the Users you've chatted will Appear here! <br>To start chat, tap the User+ button below!</h3>";
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
            <h4 class="text-left archive m-auto"><i class="bx bxs-archive-in" style="font-size:17px"></i> Archive Chat</h4>
            <h4 class="text-left read m-auto"><i class="bi bi-circle-fill" style="font-size:15px"></i> <span></span></h4>
            <h4 class="text-center menu-close m-auto"><i class="bi bi-x-circle" style="font-size:2pc"></i></h4>
        </div>
    </div>
    
    <!-- FOOTER -->
    <footer class="container d-flex flex-row m-auto">
        <a href="/chats/"><i class="bi bi-messenger text-success" style="font-size:27px"></i></a>
        <a href="/groups/"><i class="bi bi-people-fill text-dark"></i></a>
        <a href="/users/"><i class="bi bi-person-plus-fill text-dark"></i></a>
        <a href="/story/"><i class="bx bx-edit text-dark"></i></a>
        <a href="/chats/archived.php"><i class="bx bxs-archive-in text-dark"></i></a>
        <a href="/settings.php"><i class="bi bi-gear-fill text-dark" style="font-size:27px"></i></a>
    </footer>
    
    <!-- CUSTOM ALERT DESIGNED -->
    <div id="alert" style="z-index:9999; display:none">
        <div class="d-flex text-center text-light justify-content-center fixed-bottom m-3 p-2" style="background-color:#5d5d5de5; font-size:14px; border-radius:20px; box-shadow:0.1px 0.1px 1px #dadada, -0.1px -0.1px 1px #dadada"></div>
    </div>
    
    <!-- CUSTOM LOADER DESIGNED -->
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
                        Alert("You've Read the Message!")
                        window.location.reload();
                    }
                })
            } 
            else {
                $.ajax({
                    type: "GET",
                    url: "/chats/configs/unread.php",
                    data: `user_id=${user_id}`,
                    success: function() {
                        Alert("You've Unread this Message!")
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
            } 
            else {
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
                        Alert("All Messages Deleted Successful!")
                        window.location.reload();
                    }
                })
                window.location.reload();
            }
        })
        
        /* Archive Chats Function */
        $(".txt .archive").click(() => 
        {
            let user_id = $(".menu").attr("user_id");
        
            $.ajax({
                type: "GET",
                url: "/chats/configs/achieve.php",
                data: `user_id=${user_id}`,
                success: function(data) {
                    Alert(data);
                }
            })
        })
        
        //user id copy
        $("#user_link_copy").dblclick(() => {
            alert(user_link)
            //window.navigator.clipboard.writeText(link);
            Alert("Link Copied!")
        })
        
        //redirect to user link
        $("#user_link_copy").click(() => {
            //let link = document.getElementById("link_copy").textContent;
            alert()
            //window.location.replace(link);
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