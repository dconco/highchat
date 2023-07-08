<?php 
    session_start();
    //check if the cookie is set
    if (isset($_COOKIE["user_id"])) {
        if (!isset($_SESSION["user_id"])) {  
            $_SESSION["user_id"] = $_COOKIE["user_id"]; 
        }
    } else {
        $userid = htmlspecialchars($_GET['user_id']);
        header("location: /login.php?next=/chats/message.php?user_id=$userid");
        die("Session Expire! Login Again");
        exit();
    }
    
    include_once("./configs/db.php");
    
    $incoming_id = htmlspecialchars($_GET['user_id']);
    $outgoing_id = $_COOKIE["user_id"];
    
    if (!isset($incoming_id)) {
        include_once "../error/index.html";
        exit;
    }
    
    $sqlUser = "SELECT * FROM users WHERE user_id = '{$incoming_id}'";
    $res = $conn->query($sqlUser);
    
    if ($res->num_rows > 0) {
        $data = $res->fetch_assoc();
        
        $time = time();
        if ($time - $data["status"] >= 300) {
            // status is offline
            $status = "Offline";
        } else {
            // status is active
            $status = "Online";
        }
        
        if ($incoming_id == $outgoing_id) {
            $name = $data['fullname'];
            $fullname = (strlen($name) > 15) ? substr($name, 0, 15) . '... (you)' : $name . ' (you)';
        } else {
            $name = $data['fullname'];
            $fullname = (strlen($name) > 20) ? substr($name, 0, 20) . '...' : $name;
            
        }
    } else {
        include_once "../error/index.html";
        exit;
    }
    
    /* LOGGED IN USER */
    $sql = "SELECT * FROM users WHERE user_id = '$outgoing_id'";
    $result = $conn->query($sql)->fetch_assoc();
    
    /* ACHIEVE USER */
    $achieve_sql = "SELECT * FROM achieve WHERE achieve_by = '$outgoing_id' AND achieve = '$incoming_id'";
    $achieve_res = $conn->query($achieve_sql);
    
    if ($achieve_res->num_rows > 0) {
        $achieve_value = "Remove Archive";
    } else {
        $achieve_value = "Archive Chat";
    }
    
    /* BLOCK / UNBLOCK USER */
    $sqlBlock = "SELECT * FROM blocked WHERE block = '$incoming_id' AND block_by = '$outgoing_id'";
    if ($conn->query($sqlBlock)->num_rows > 0) { 
        $user_block = true;
    } else { 
        $user_block = false;
    }
    
    $sqlBlock2 = "SELECT * FROM blocked WHERE (block = {$incoming_id} AND block_by = {$outgoing_id}) 
                           OR (block = {$outgoing_id} AND block_by = {$incoming_id}) ORDER BY id ASC LIMIT 1";
    if ($conn->query($sqlBlock2)->num_rows > 0) {
        $block_res = $conn->query($sqlBlock2)->fetch_assoc();
    } else {
        $user_block2 = 'none';
    }
    
    ## FOLLOW OR UNFOLLOW
    # select all followers
    $sqlFollowers = "SELECT * FROM follow WHERE follow = {$incoming_id} AND (follow_by 
            NOT IN (SELECT friend_id FROM friends WHERE user_id = {$incoming_id}) AND follow_by 
            NOT IN (SELECT user_id FROM friends WHERE friend_id = {$incoming_id})) 
            ORDER BY id DESC"; // select all followers
    
    # select all followings
    $sqlFollowing = "SELECT * FROM follow WHERE follow_by = {$incoming_id} AND (follow 
            NOT IN (SELECT friend_id FROM friends WHERE user_id = {$incoming_id}) AND follow 
            NOT IN (SELECT user_id FROM friends WHERE friend_id = {$incoming_id})) 
            ORDER BY id DESC"; //select all Followings
            
    $followers = $conn->query($sqlFollowers);
    $following = $conn->query($sqlFollowing);
    
    ## FRIENDS
    $sqlFriends = "SELECT * FROM friends WHERE user_id = {$incoming_id} OR friend_id = {$incoming_id}";
    $friends = $conn->query($sqlFriends);
    
    /* VERIFY MARK */
    if ($followers->num_rows >= 20 && $res["email_verify"] === $res["email"]) {
        $verify = "<i class='bi-check-circle-fill' 
                    onclick='Alert(`This user has been verified by Spyrochat!`)' 
                    style='font-size:13px;color:green'></i>";
    } else {
        $verify = "";
    }
    
    ## USER SHOULD FOLLOW OR UNFOLLOW
    $sqlFollow = "SELECT * FROM follow WHERE follow = {$incoming_id} AND follow_by = {$outgoing_id}"; // if user follow him
    $sqlFollow2 = "SELECT * FROM follow WHERE follow = {$outgoing_id} AND follow_by = {$incoming_id}"; // if he follow user
    
    if ($conn->query($sqlFollow)->num_rows > 0 && $conn->query($sqlFollow2)->num_rows > 0) { # if they follow eachother
        $follow = "Friends";
        $bgcolor = "bg-secondary";
    } 
    else if ($conn->query($sqlFollow)->num_rows > 0) { # if user follow him
        $follow = "- Unfollow";
        $bgcolor = "bg-danger";
    }
    else if ($conn->query($sqlFollow2)->num_rows > 0) { # if he follow user
        $follow = "+ Follow Back";
        $bgcolor = "bg-primary";
    } 
    else { # if user did not follow him
        $follow = "+ Follow";
        $bgcolor = "bg-success";
    }
?>
<!-- END PHP CODES -->

<!-- HTML STARTS -->
<!DOCTYPE html>
<html lang="en">
<head>
    
    <title><?php echo $data['fullname'] ?> is on Highchat</title>
    <?php include_once("../extras/header.php") ?>

    <!-- CSS LINKS -->
    <link rel="stylesheet" href="./css/message.css">
    <!-- JAVASCRIPT LINKS -->
    <script src="./js/message.js" type="text/javascript"></script>
</head>
<body>
    <header class="container-fluid fixed-top bg-secondary d-flex align-items-center">
        <div class="rounded-circle top-img bg-secondary" onclick="profile()" style="width:55px; height:55px; min-width:55px; min-height:55px; max-width:55px; max-height:55px; object-fit:cover; border:3px solid #fff;">
            <img class="rounded-circle img-fluid d-flex align-items-center mx-auto m-auto" style="width:100%; height:100%; object-fit:cover" src="/profile_pictures/<?php echo $data['img'] ?>" alt="<?php echo ($data['firstname'] . '\'s Profile Picture')  ?>">
        </div>
        
        <div class="top-info" onclick="profile()">
            <div class="name"><?php echo $fullname; ?></div>
            <div class="status">~ <?php echo $status; ?></div>
        </div>
        
        <div class="menu">
            <i class="bi bi-three-dots"></i>
        </div>
    </header>
    
    <!-- USERS PROFILE CONTANER -->
    <div class="nav-full bg-dark">
        <div class="nav-cont d-flex flex-column">
            <i class="bi bi-x-circle nav-close"></i>
            
            <div class="img-thumbnail bg-secondary rounded-circle d-flex mx-auto nav-img" oncontextmenu="download('<?php echo $data['img'] ?>')">
                <img class="rounded-circle img-fluid d-flex mx-auto m-auto" style="object-fit:center; width:100%; height:100%" src="/profile_pictures/<?php echo $data['img'] ?>" alt="<?php echo ($data['firstname'] . '\'s Profile Picture')  ?>">
            </div>
            
            <span class="text-white text-center nav-header"><?php echo $data['fullname'] . ' ' . $verify; ?></span>
            <hr class="nav-hr bg-white">
        </div>
        
        <!-- NAVIGATION LINKS -->
        <div class="container nav-cont2 d-flex flex-column">
            <div class="d-flex flex-row justify-content-center about">
                <span class="badge bg-success country"><?php echo $data['country']; ?></span>
                <span class="badge bg-success gender"><?php echo $data['gender']; ?></span>
            </div>
            
            <!-- ABOUT -->
            <div class="text-center mt-3" style="font-size:0.8pc; color:#dcdcdc">
                <?php echo (empty($data['about'])) ? 'No About!' :  nl2br($data['about']); ?>
            </div>
            
            <!-- FOLLOWERS -->
            <div class="d-flex flex-column justify-content-center mb-3 mt-2 m-2" style="font-size:1.2pc">
                <br>
                <span class="badge <?php echo $bgcolor;  ?> follow p-3 m-1"><?php echo $follow ?></span>
                <span class="badge bg-secondary p-3 m-1" onclick="Alert('<?php echo $friends->num_rows ?> Friends')"><?php echo $friends->num_rows ?> Friends</span>
                <span class="badge bg-secondary p-3 m-1" onclick="Alert('<?php echo $followers->num_rows ?> Followers')"><?php echo $followers->num_rows ?> Followers</span>
                <span class="badge bg-secondary p-3 m-1" onclick="Alert('<?php echo $following->num_rows ?> Followings')"><?php echo $following->num_rows ?> Followings</span>
            </div>
        </div>
    </div>
    
    <!-- MENU CONTAINER -->
    <div class="menu-div bg-secondary flex-column">
        <div id="achieve" user-id="<?php echo $incoming_id; ?>"><i class="bi bi-archive"></i><i id="php_achieve"><?php echo $achieve_value; ?></i></div>
        <hr>
        <div id="clear"><i class="bi bi-trash3"></i><i>Clear all Chats</i></div>
        <hr>
        <div id="mark" user-id="<?php echo $incoming_id ?>"><i class="bi bi-circle-fill"></i><i>Mark as Unread</i></div>
        
        <!-- BLOCK USER LINK -->
        <?php if ($incoming_id !== $outgoing_id): ?>
            <hr>
            <?php if ($user_block === true): ?>
                <div id="unblock" user-id="<?php echo $incoming_id ?>"><i class="bi bi-dash-circle"></i><i>Unblock User</i></div>
            <?php else: ?>
                <div id="block" user-id="<?php echo $incoming_id ?>"><i class="bi bi-dash-circle"></i><i>Block User</i></div>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>
    
    <!-- MAIN CHAT -->
    <main style="margin-top:90px">
        <div class="main" style="margin-bottom:65px">
            <!-- ALL USER CHATS -->
        </div>
        
        <!-- STICKERS CONTAINER -->
        <div class="sticker-wrapper m-auto fixed-bottom">
            <div class="sticker-cont">
                <div class="close-sticker d-flex justify-content-end">
                    <i class="bi bi-x-square"></i>
                </div>
                
                <img onclick="Sticker('sticker_angry')" src="/stickers/sticker_angry.png" alt="sticker_angry">
                <img onclick="Sticker('sticker_confused')" src="/stickers/sticker_confused.png" alt="sticker_confused">
                <img onclick="Sticker('sticker_devil')" src="/stickers/sticker_devil.png" alt="sticker_devil">
                <img onclick="Sticker('sticker_eye_tongue')" src="/stickers/sticker_eye_tongue.png" alt="sticker_eye_tongue">
                <img onclick="Sticker('sticker_happy')" src="/stickers/sticker_happy.png" alt="sticker_happy">
                <img onclick="Sticker('sticker_head_ring')" src="/stickers/sticker_head_ring.png" alt="sticker_head_ring">
                <img onclick="Sticker('sticker_heart_happy')" src="/stickers/sticker_heart_happy.png" alt="sticker_heart_happy">
                <img onclick="Sticker('sticker_heart_kiss')" src="/stickers/sticker_heart_kiss.png" alt="sticker_heart_kiss">
                <img onclick="Sticker('sticker_nose_water_drop')" src="/stickers/sticker_nose_water_drop.png" alt="sticker_nose_water_drop">
                <img onclick="Sticker('sticker_sad')" src="/stickers/sticker_sad.png" alt="sticker_sad">
                <img onclick="Sticker('sticker_sad_water_drop')" src="/stickers/sticker_sad_water_drop.png" alt="sticker_sad_water_drop">
                <img onclick="Sticker('sticker_sleep')" src="/stickers/sticker_sleep.png" alt="sticker_sleep">
                <img onclick="Sticker('sticker_smile')" src="/stickers/sticker_smile.png" alt="sticker_smile">
                <img onclick="Sticker('sticker_smile_glass')" src="/stickers/sticker_smile_glass.png" alt="sticker_smile_glass">
                <img onclick="Sticker('sticker_smile_tongue')" src="/stickers/sticker_smile_tongue.png" alt="sticker_smile_tongue">
                <img onclick="Sticker('sticker_surprise')" src="/stickers/sticker_surprise.png" alt="sticker_surprise">
                <img onclick="Sticker('sticker_wow')" src="/stickers/sticker_wow.png" alt="sticker_wow">
            
                <!-- NEW STICKERS -->
                <img onclick="Sticker('sticker_alien_big')" src="/stickers/sticker_alien_big.png" alt="sticker_alien_big">
                <img onclick="Sticker('sticker_alien_small')" src="/stickers/sticker_alien_small.png" alt="sticker_alien_small">
                <img onclick="Sticker('sticker_alien_tongue')" src="/stickers/sticker_alien_tongue.png" alt="sticker_alien_tongue">
                <img onclick="Sticker('sticker_angry_heart')" src="/stickers/sticker_angry_heart.png" alt="sticker_angry_heart">
                <img onclick="Sticker('sticker_heart_cap')" src="/stickers/sticker_heart_cap.png" alt="sticker_heart_cap">
                <img onclick="Sticker('sticker_care')" src="/stickers/sticker_care.png" alt="sticker_care">
                <img onclick="Sticker('sticker_chef_heart')" src="/stickers/sticker_chef_heart.png" alt="sticker_chef_heart">
                <img onclick="Sticker('sticker_crazy')" src="/stickers/sticker_crazy.png" alt="sticker_crazy">
                <img onclick="Sticker('sticker_dark')" src="/stickers/sticker_dark.png" alt="sticker_dark">
                <img onclick="Sticker('sticker_dark_king')" src="/stickers/sticker_dark_king.png" alt="sticker_dark_king">
                <img onclick="Sticker('sticker_light')" src="/stickers/sticker_light.png" alt="sticker_light">
                <img onclick="Sticker('sticker_double')" src="/stickers/sticker_double.png" alt="sticker_double">
                <img onclick="Sticker('sticker_ghost')" src="/stickers/sticker_ghost.png" alt="sticker_ghost">
                <img onclick="Sticker('sticker_heart_break')" src="/stickers/sticker_heart_break.png" alt="sticker_heart_break">
                <img onclick="Sticker('sticker_heart_happy')" src="/stickers/sticker_heart_happy.png" alt="sticker_heart_happy">
                <img onclick="Sticker('sticker_heart_tongue')" src="/stickers/sticker_heart_tongue.png" alt="sticker_heart_tongue">
                <img onclick="Sticker('sticker_heart_smile')" src="/stickers/sticker_heart_smile.png" alt="sticker_heart_smile">
                <img onclick="Sticker('sticker_hero')" src="/stickers/sticker_hero.png" alt="sticker_hero">
                <img onclick="Sticker('sticker_heart_up')" src="/stickers/sticker_heart_up.png" alt="sticker_heart_up">
                <img onclick="Sticker('sticker_hot_water_drop')" src="/stickers/sticker_hot_water_drop.png" alt="sticker_hot_water_drop">
                <img onclick="Sticker('sticker_laugh')" src="/stickers/sticker_laugh.png" alt="sticker_laugh">
                <img onclick="Sticker('sticker_mask_heart')" src="/stickers/sticker_mask_heart.png" alt="sticker_mask_heart">
                <img onclick="Sticker('sticker_pleading')" src="/stickers/sticker_pleading.png" alt=sticker_pleading"">
                <img onclick="Sticker('sticker_pleading_heart')" src="/stickers/sticker_pleading_heart.png" alt="sticker_pleading_heart">
                <img onclick="Sticker('sticker_pleading_kiss')" src="/stickers/sticker_pleading_kiss.png" alt="sticker_pleading_kiss">
                <img onclick="Sticker('sticker_pleading_water_drop')" src="/stickers/sticker_pleading_water_drop.png" alt="sticker_pleading_water_drop">
                <img onclick="Sticker('sticker_pleading_angry')" src="/stickers/sticker_pleading_angry.png" alt="sticker_pleading_angry">
                <img onclick="Sticker('sticker_pleading_up')" src="/stickers/sticker_pleading_up.png" alt="sticker_pleading_up">
                <img onclick="Sticker('sticker_pleading_down')" src="/stickers/sticker_pleading_down.png" alt="sticker_pleading_down">
                <img onclick="Sticker('sticker_pleading_happy')" src="/stickers/sticker_pleading_happy.png" alt="sticker_pleading_happy">
                <img onclick="Sticker('sticker_pleading_surprise')" src="/stickers/sticker_pleading_surprise.png" alt="sticker_pleading_surprise">
                <img onclick="Sticker('sticker_pleading_mouth')" src="/stickers/sticker_pleading_mouth.png" alt="sticker_pleading_mouth">
                <img onclick="Sticker('sticker_thinking')" src="/stickers/sticker_thinking.png" alt="sticker_thinking">
                <img onclick="Sticker('sticker_yep')" src="/stickers/sticker_yep.png" alt="sticker_yep">
            </div>
        </div>
        
        <!-- MESSAGE BOX -->
        <?php if ($user_block2 === 'none'): #if user is not blocked ?>
            <div class="container-fluid mb-2 d-flex flex-row fixed-bottom msg-cont">
                <button class="btn btn-secondary msg-menu"><i class="bi bi-caret-up-fill"></i></button>
                <div class="msg-field" style="width:100%">
                    <form action="#">
                        <textarea class="form-control" id="message" id1="<?php echo $outgoing_id ?>" id2="<?php echo $incoming_id ?>" placeholder="Write Message"></textarea>
                        <button class="btn btn-success msg-btn" id="send_msg"><i class="bi bi-arrow-right-circle"></i></button>
                    </form>
                </div>
            </div>
        <?php else: #if user is blocked ?>
            <div class="container-fluid d-flex mx-auto text-center fixed-bottom p-2" style="color:#000; background-color:#a5a5a5e6; font-size:13px; height:50px; border-top:1px solid #fff">
                <?php if ($block_res["block_by"] === $outgoing_id): #you blocked the user ?>
                    You've Blocked this user.
                <?php else: #the user blocked you ?>
                    This user has Blocked you.
                <?php endif; ?>
                 
                 &nbsp;You won't be able to send/receive messages from this user or see each other Status.
            </div>
        <?php endif; ?>
        
        <!-- SHORT MENU CONTAINER -->
        <div class="short-menu bg-secondary flex-column">
            <div id="sticker"><i class="bi bi-emoji-smile"></i><i>Send Stickers</i></div>
            <hr>
            <div id="voice-call"><i class="bi bi-image"></i><i>Voice Call</i></div>
            <hr>
            <div id="video-call"><i class="bi bi-camera-video"></i><i>Video Call</i></div>
            <hr>
            <div id="media"><i class="bi bi-image"></i><i>Send Medias</i></div>
            <hr>
            <div id="docs"><i class="bi bi-file-earmark-text"></i><i>Send Docs</i></div>
        </div>
    </main>
    
    <!-- CUSTOM ALERT DESIGNED -->
    <div id="alert" style="z-index:99999; display:none">
        <div class="d-flex text-center text-light justify-content-center fixed-bottom m-3 p-2" style="background-color:#5d5d5de5; font-size:14px; border-radius:20px; box-shadow:0.1px 0.1px 1px #dadada, -0.1px -0.1px 1px #dadada"></div>
    </div>
    <h2 id="desktop-view" class="text-center">Desktop view not available for now!</h2>
    
    
    <script>
        <?php echo("var incoming_id = '$incoming_id';
                    var outgoing_id = '$outgoing_id';");
        ?>
        
        /* DELETE MESSAGE FUNCTION*/
        function deleteMsg(msg_id) 
        {
            if (confirm("Are you sure to delete this message?")) 
            {
                $.ajax({
                    type: "GET",
                    url: "/chats/configs/deleteMsg.php",
                    data: `msg_id=${msg_id}`,
                    success: function() {   
                        // AUDIO PLAY
                        let audio = new Audio();
                        audio.src = "../sounds/notification.mp3";
                        audio.play();
                        audio.removeNode();
                    }
                })
            }
        }
        
        /* COPY MESSAGE FUNCTION */
        function copyMsg(msg_id) 
        {
            let msg = $(`#${msg_id}`).text();
            window.navigator.clipboard.writeText(msg);
            Alert("Text Copied!");
            
            // AUDIO PLAY
            let audio = new Audio();
            audio.src = "../sounds/notification.mp3";
            audio.play();
            audio.removeNode();
        }
        
        /* PROFILE FUNCTION */
        function profile() 
        {
            $(".nav-full").show()
            $(".nav-full").animate({
                marginRight: "0"
            })
        }
        
        /* FOLLOW AND UNFOLLOW FUNCTION */
        $(".follow").click(() => 
        {
            let text = $(".follow").text();
            
            if (text === "+ Follow Back" || text === "+ Follow") 
            {
                $.ajax({
                    type: "GET",
                    url: "/users/configs/follow.php",
                    data: "user_id=" + incoming_id,
                    success: function() {
                        window.location.reload();
                    }
                })
            } 
            else 
            {
                if (confirm("Are you sure to unfriend or unfollow this user?")) 
                {
                    $.ajax({
                        type: "GET",
                        url: "/users/configs/unfollow.php",
                        data: "user_id=" + incoming_id,
                        success: function() {
                            window.location.reload();
                        }
                    })
                }
            }
        })
        
        /* CUSTOM ALERT FUNCTION */
        function Alert(msg) 
        {
            $(function() 
            {
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