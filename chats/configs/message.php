<?php
    include_once "db.php";
    include_once "../../encryption/encrypt.php";
    
    $outgoing_id = $_POST['outgoing_id'];
    $incoming_id = $_POST['incoming_id'];
    $msg = htmlspecialchars($_POST["msg"]);
    $output = "";
    
    
    if (!empty($msg) && strlen($msg) <= 4000) 
    {
        $sqlBlock = "SELECT * FROM blocked WHERE 
            (block = '$incoming_id' AND block_by = '$outgoing_id') 
                OR (block = '$outgoing_id' AND block_by = '$incoming_id')";
                    
        if ($conn->query($sqlBlock)->num_rows > 0) {
            echo "Failed to send Message";
        }
        else {
            
            $encrypt = encrypt($msg); //encrypt message
            
            // get encrypted info
            $key = $encrypt["key"];
            $encryptMsg = $encrypt["value"];
            $iv_value = $encrypt["iv_value"];
            
            $sql = "INSERT INTO messages (
                        incoming_msg_id, 
                        outgoing_msg_id, 
                        msg, 
                        status, 
                        clear_outgoing, 
                        clear_incoming, 
                        type, 
                        src,
                        encrypt_key,
                        iv_value
                    )
                    VALUES (
                        '$incoming_id', 
                        '$outgoing_id', 
                        '$encryptMsg', 
                        '', '', '', 
                        'text', 
                        '',
                        '$key',
                        '$iv_value'
                    )";
            
            if ($conn->query($sql)) {
                echo "Message Sent";
            } else {
                echo "Failed to send Message";
            }
        }
    } 
    else {
        echo "The Message has exceeded the limit!";
    }