<?php
    $email = htmlspecialchars($_GET["email"]);
    
    if (isset($_POST["send"])) {
        $code1 = $_POST["code1"];
        $code2 = $_POST["code2"];
        $code3 = $_POST["code3"];
        $code4 = $_POST["code4"];
        $code5 = $_POST["code5"];
        $code6 = $_POST["code6"];
        
        if ($code1 === "" || $code2 === "" || $code3 === "" || $code4 === "" || $code5 === "" || $code6 === "")
        {
            $status = '<div class="alert alert-danger mt-3" style="font-size:15px">Incorrect Code!</div>';   
        } else {
            $res = $code1.$code2.$code3.$code4.$code5.$code6;
            echo $res;
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
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
            <h3 class="text-primary">CONFIRM OTP CODE</h3>
            <hr style="margin-top:-5px" />
            
            <br />
            <span class="text-muted mb-3">
                6-Digits OTP Reset Code has been sent to your Email: <?php echo $email; ?> 
                <br />
                Input the Code Below.
            </span>
            
            <form action="<?php echo  $_SERVER['SELF']; ?>" method="POST" accept-charset="utf-8">
                <div class="d-flex form-group">
                    <input type="number" class="form-control text-center p-1 text-muted" name="code1" id="code1" value="" style="font-size:25px; font-weight:bolder; margin:auto; margin-left:5px" />
                    <input type="number" class="form-control text-center p-1 text-muted" name="code2" id="code2" value="" style="font-size:25px; font-weight:bolder; margin:auto; margin-left:5px" />
                    <input type="number" class="form-control text-center p-1 text-muted" name="code3" id="code3" value="" style="font-size:25px; font-weight:bolder; margin:auto; margin-left:5px" />
                    <input type="number" class="form-control text-center p-1 text-muted" name="code4" id="code4" value="" style="font-size:25px; font-weight:bolder; margin:auto; margin-left:5px" />
                    <input type="number" class="form-control text-center p-1 text-muted" name="code5" id="code5" value="" style="font-size:25px; font-weight:bolder; margin:auto; margin-left:5px" />
                    <input type="number" class="form-control text-center p-1 text-muted" name="code6" id="code6" value="" style="font-size:25px; font-weight:bolder; margin:auto; margin-left:5px" />
                </div>
                    
                <button type="submit" name="send" class="btn btn-sm btn-primary p-2 mt-3 w-100">RESET PASSWORD</button>
                <?php echo "$status"; ?>
                
                <span class="text-muted d-flex mt-2" style="font-size:15px">Resend Code</span>
            </form>
        </div>
        
        
        
        <script>
            let code1 = document.getElementById("code1");
            let code2 = document.getElementById("code2");
            let code3 = document.getElementById("code3");
            let code4 = document.getElementById("code4");
            let code5 = document.getElementById("code5");
            let code6 = document.getElementById("code6");
            
            code1.addEventListener('keypress', (e) => {
              code2.focus();
              if (input.value.length > 0) {
                e.preventDefault();
              }
            });
            
            code2.addEventListener('keypress', (e) => {
              code3.focus();
              if (code2.value.length > 0) {
                e.preventDefault();
              }
            });
            
            code3.addEventListener('keypress', (e) => {
              code4.focus();
              if (code3.value.length > 0) {
                e.preventDefault();
              }
            });
            code4.addEventListener('keypress', (e) => {
              code5.focus();
              if (code4.value.length > 0) {
                e.preventDefault();
              }
            });
            code5.addEventListener('keypress', (e) => {
              code6.focus();
              if (code5.value.length > 0) {
                e.preventDefault();
              }
            });
            
            code6.addEventListener('keypress', (e) => {
              if (code6.value.length > 0) {
                e.preventDefault();
              }
            });
            
            //keyup
            code6.addEventListener('keyup', (e) => {
              if (code6.value.length > 0) {
                code6.blur();
              }
            });
            
            
            //on input
            code2.addEventListener('input', (e) => {
              if (code2.value === "") {
                code1.focus();
              }
            });
            
            code3.addEventListener('input', (e) => {
              if (code3.value === "") {
                code2.focus();
              }
            });
            
            code4.addEventListener('input', (e) => {
              if (code4.value === "") {
                code3.focus();
              }
            });
            
            code5.addEventListener('input', (e) => {
              if (code5.value === "") {
                code4.focus();
              }
            });
            
            code6.addEventListener('input', (e) => {
              if (code6.value === "") {
                code5.focus();
              }
            });
        </script>
    </body>
</html>