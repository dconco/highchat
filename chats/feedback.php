<?php 
    if (isset($_POST["send"])) {
        $recEmail = $_POST["email"];
        $subject = $_POST["subject"];
        $message = $_POST["message"];
        
        include_once "../mail.php";
        $mail = send_mail($recEmail, $subject, $message);
    
        if (!$mail["success"]) {
            $status = '<div class="alert alert-danger mb-2" style="font-size:12px">' . $mail["error"] . '</div>';
        } else { 
            $status = '<div class="alert alert-success mb-2" style="font-size:12px">' . $mail["success"] . '</div>';
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Send Mail</title>
        <?php include_once "../extras/header.php";  ?>
    </head>
    <body class="p-3">
        <div class="container p-4 mt-5 mx-auto d-flex card">
            <?php echo $status; ?>
            
            <form action="<?php echo $_SERVER['SELF'] ?>" method="POST" class="form-group">
                <input style="font-size:12px" type="email" name="email" class="form-control mb-2" placeholder="Recipients" required />
                <input style="font-size:12px" type="text" name="subject" class="form-control mb-2" placeholder="Subject" required />
                <textarea style="font-size:12px" name="message" class="form-control" placeholder="Compose Message.." required></textarea>
                
                <button type="submit" name="send" class="btn btn-sm btn-success mt-3 float-end">Send</button>
            </form>
        </div>
    </body>
</html>