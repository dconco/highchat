<?php
    session_start();
    //check if the cookie is set
    if (isset($_COOKIE["user_id"])) {
        if (!isset($_SESSION["user_id"])) {  
            $_SESSION["user_id"] = $_COOKIE["user_id"]; 
        }
    } else {
        header("location: /login.php?next=/settings.php");
        die("Session Expired! Login Again!");
        exit();
    }
    
    include_once "./configs/db.php";
    $sql = "SELECT * FROM users WHERE user_id = '{$_SESSION['user_id']}'";
    $data = $conn->query($sql)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SETTINGS | HIGHCHAT</title>
    <?php include_once("./extras/header.php") ?>

    <!-- CSS LINKS -->
    <link rel="stylesheet" href="./css/edit.css">
    <!-- JAVASCRIPT LINKS -->
    <script src="./js/edit.js" type="text/javascript"></script>
</head>
<body>
    <header>
        <!-- TOP NAVIGATION -->
        <div class="navbar bg-secondary fixed-top" data-aos="fade-down">
            <div class="container-fluid">
                <div class="navbar-brand" data-aos="zoom-out" data-aos-delay="700">
                    <a href="/"><img src="./img/icon.png" alt="" style="width:60px"></a>
                    <span class="text-light logo-text">HighChat - Settings</span>
                </div>
            </div>
        </div>
    </header>
    
    <main>
        <div class="container mt-3">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#pro-pic">Profile Picture</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#about">About Info</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#pwd-div">Password</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#security">Security Mode</a>
                </li>
            </ul>
        
            <!-- Tab panes -->
            <form action="#" enctype="multipart/form-data">
                <div class="tab-content" style="width:100%">
                    <!-- EDIT PROFILE PICTURE -->
                    <div id="pro-pic" class="container tab-pane active"><br>
                        <h3>Update your Profile Picture</h3>
                        
                        <div class="mt-2 img-thumbnail bg-secondary rounded-circle d-flex m-auto" style="border:8px solid grey; width:300px; height:300px; min-width:300px; min-height:300px;  max-width:300px; max-height:300px">
                            <img class="img-fluid rounded-circle" style="width:100%;height:100%;object-fit:cover" src="/profile_pictures/<?php echo $data['img'] ?>" alt="">
                        </div>
                        
                        <input type="file" name="image" id="image" hidden class="form-control mt-3">
                        <div id="pro_pic_btn" onclick="$('#image').click()" class="btn btn-primary mt-3 m-auto justify-content-center d-flex" style="width:80%; border:2px solid white">Upload an Image</div>
                        <p id="val" class="mt-2 text-center"></p>
                    </div>
                    
                    <!-- EDIT ABOUT INFO -->
                    <div id="about" class="container tab-pane fade"><br>
                        <h3>Update Info About Yourself</h3>
                        
                        <br>
                        <label for="fname" class="pb-2">Enter your Fullname</label>
                        <br><input type="text" id="fname" name="fname" class="p-2 w-100 form-control">
                        
                        <br>
                        <label for="username" class="pb-2">Edit your Username</label>
                        <br><input type="text" id="username" name="username" value="<?php echo $data["username"] ?>" class="p-2 w-100 form-control">
                        
                        <br>
                        <label for="email" class="pb-2">Enter your Email</label>
                        <br><input type="email" id="email" name="email" class="p-2 w-100 form-control">
                        
                        <br>
                        <label for="country" class="pb-2">Select your Country</label>
                        <select class="form-select p-2" name="country" id="country">
                            <?php include_once "extras/countries.php"; ?>
                        </select>
                        
                        <br>
                        <label for="about" class="pb-2">Write a short Bio of Yourself</label>
                        <br><textarea id="about" name="about" class="p-2 w-100 h-50 form-control"></textarea>
                    </div>
                    
                    <!-- EDIT PASSWORD -->
                    <div id="pwd-div" class="container tab-pane fade"><br>
                        <h3>Update Your Password</h3>
                        
                        <br>
                        <label for="pwd" class="pb-2">Confirm your Old Password</label>
                        <br><input type="password" name="pwd" id="pwd" class="p-2 mb-2 w-100 form-control">
                        
                        <br>
                        <label for="newPwd" class="pb-2">Create a New Password</label>
                        <br><input type="password" name="newPwd" id="newPwd" class="p-2 w-100 form-control">
                        
                        <br>
                        <label for="newConPwd" class="pb-2">Confirm your New Password</label>
                        <br><input type="password" name="newConPwd" id="newConPwd" class="p-2 w-100 form-control">
                        
                        <a href='./reset.php'><span class="text-danger mb-2" style="font-weight:bolder; font:15px Consolas Arial">Forgotten Password?</span></a>
                    </div>
                    
                    <!-- SECURITY MODE -->
                    <div id="security" class="container tab-pane fade"><br>
                        <h3>Account Info</h3>
                        
                        <div class="d-flex text-white btn btn-danger m-1 p-2 delete-acct">
                            Permanently Delete Account
                        </div>
                    </div>
                    <!-- END SECURITY MODE -->
                    
                    <br>
                    <button type="submit" name="update" value="update" id="update" class="btn btn-success d-flex mt-3 p-2 justify-content-center" style="border:2px solid white; margin-left:auto; margin-right:20px; font-size:1.3pc; width:150px">Save Changes</button>
                    <div class="alert alert-success m-3" role="alert"></div>
                
                </div>
            </form>
        </div>
    </main>
    
    
    <div id="preloader"></div>
    <h2 id="desktop-view" class="text-center">Only for Mobile View</h2>
    
    <script type="text/javascript" charset="utf-8">
        $(".delete-acct").click(function() {
            if (confirm("Deleted Account cannot be refund! \nNote that if you delete your account everything you've done on Spyrochat will be deleted! \nAre you sure to continue?")) {
                let val = prompt("Confirm your password to continue", "******");
                
                if (val) {
                    $.ajax({
                        type: "GET",
                        url: "/deleteAcct.php",
                        data: "pwd="+val,
                        success: function(data) {
                            if (data === "success") {
                                window.location = "/logout.php";
                            } else {
                                alert(data);
                            }
                        }
                    })
                }
            }
        })
    </script>
    
</body>
</html>