<?php
    include_once "configs/db.php";
    
    if (isset($_POST["send"])) {
        $email = htmlspecialchars($_POST["email"]);
        if (empty($email)) {
            $status = '<div class="alert alert-danger mt-3" style="font-size:15px">You haven\'t provided any Email!</div>';
        } else {
            $sql = "SELECT email FROM users WHERE email = '$email'";
            if ($conn->query($sql)->num_rows > 0) {
                header("Location: ./send_reset.php?email=$email");
            } else {
                $status = '<rdiv class="alert alert-danger mt-3" style="font-size:15px">There\'s no User with the provided Email!</div>';
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>RESET PASSWORD | SPYROCHAT</title>
    
    <?php include_once "extras/header.php"; ?>
    
    <style>
        @import "fonts/fonts.css";
        
        h3 {
            font-weight: bolder; 
            font: 1.8pc Teko-Medium;
        }
    </style>
</head>
<body class="p-4 pt-5">
    <div class="container card bg-light p-3 d-flex text-center justify-content-center" style="max-width: 400px">
        <h3 class="text-primary">RESET PASSWORD</h3>
        <hr style="margin-top:-5px" />
        
        <br />
        <span class="text-muted mb-2">
            Enter your Email to receive an OTP code to reset your Password.
        </span>
        
        <form action="<?php $_SERVER['SELF'] ?>" method="POST" accept-charset="utf-8">
            
            <?php if (isset($_COOKIE["user_id"])): #if the user is logged in
                $sql = "SELECT email FROM users WHERE user_id='{$_COOKIE['user_id']}'" or die();
                $res = $conn->query($sql)->fetch_array()["email"]; ?>
                
                <input type="email" class="form-control p-2 mt-3 text-muted" name="email" value="<?php echo $res; ?>" placeholder="Enter your Email" style="font-size:15px" />
            
            <?php else: ?>
                <input type="email" class="form-control p-2 mt-3 text-muted" name="email" placeholder="Enter your Email" style="font-size:15px" />
            <?php endif; ?>
            
            <button type="submit" name="send" class="btn btn-sm btn-primary p-2 mt-3 w-100">SEND CODE</button>
        </form>
        
        <?php echo $status; ?>
    </div>
</body>
</html>