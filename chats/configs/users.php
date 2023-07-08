<?php
    $output = "";
    include_once "stickers.php";
    
    while ($data = $result->fetch_assoc()) {
        $sql2 = "SELECT * FROM messages WHERE ((outgoing_msg_id = {$user_id} AND incoming_msg_id = {$data['user_id']})
                            OR (outgoing_msg_id = {$data['user_id']} AND incoming_msg_id = {$user_id})) 
                                AND NOT (clear_outgoing = '{$user_id}' OR clear_incoming = '{$user_id}') ORDER BY msg_id DESC LIMIT 1";
       
        /* BLOCK / UNBLOCK USER */ 
        $sqlBlock = "SELECT * FROM blocked WHERE block = {$data['user_id']} AND block_by = {$user_id}";
        if ($conn->query($sqlBlock)->num_rows > 0) {
            $user_block = 'true';
        } else {
            $user_block = 'false';
        }
        
        /* VERIFY MARK */
        $sqlFollowers = "SELECT * FROM follow WHERE follow = {$data['user_id']} AND (follow_by 
                            NOT IN (SELECT friend_id FROM friends WHERE user_id = {$data['user_id']}) AND follow_by 
                            NOT IN (SELECT user_id FROM friends WHERE friend_id = {$data['user_id']}))
                            ORDER BY id DESC"; // select all followers
                            
        $followers = $conn->query($sqlFollowers);
        if ($followers->num_rows >= 20 && $data['email_verify'] === $data['email']) {
            $verify = "<i class='bi-check-circle-fill' 
                        onclick='Alert(`This user has been verified by Spyrochat!`)' 
                        style='font-size:13px;color:green'></i>";
        } else {
            $verify = "";
        }
        
        if ($conn->query($sql2)->num_rows > 0) { //if there's messages
            $res = $conn->query($sql2)->fetch_assoc();
            $msg = decrypt($res["encrypt_key"], $res["msg"], $res["iv_value"]);
            
            $sqlBlock2 = "SELECT * FROM blocked WHERE (block = {$data['user_id']} AND block_by = {$user_id}) 
                                                    OR (block_by = {$data['user_id']} AND block = {$user_id}) LIMIT 1";
            if ($conn->query($sqlBlock2)->num_rows > 0) {
                $block_res = $conn->query($sqlBlock2)->fetch_assoc();
                
                $b = "seen";
                $ss = "<i class='bi bi-dash-circle-fill text-danger'></i>";
                if ($block_res["block_by"] === $user_id) {
                    $body = "<i class='bi bi-dash-circle'></i> You Blocked this user!";
                } else {
                    $body = "<i class='bi bi-dash-circle'></i> You've been Blocked!";
                }
            } else {
            
                $time = time();
                if ($time - $data["status"] >= 300) {
                    // status is offline
                    $ss = "<i class='offline bi bi-circle-fill'></i>";
                } else {
                    // status is active
                    $ss = "<i class='active bi bi-circle-fill'></i>";
                }
                
                if ($res["outgoing_msg_id"] === $user_id) { //if last message is for the logged-in user
                    $b = "seen";
                    
                    if (in_array($msg, $stickers)) {
                        $body = "You sent a sticker";
                    } else {
                        (strlen($msg) > 27) ? $body = "You: " . substr($msg, 0, 27) . "..." : $body = "You: " . $msg;
                    }
                } else {
                    ($res["status"] === "seen") ? $b = "seen" : $b = "sent";
                    
                    if (in_array($msg, $stickers)) {
                        $body = "Sent you a sticker";
                    } else {
                        (strlen($msg) > 33) ? $body = substr($msg, 0, 33) . "..." : $body = $msg;
                    }
                }
            }
        
        
        $output .= "<div class='d-flex flex-row main-cont' onclick='user({$data['user_id']})' oncontextmenu='Menu({$data['user_id']}, `$b`, `$user_block`)' data-aos='fade-up'>
                        <!-- PROFILE PICTURE -->
                        <div class='img-fluid bg-secondary rounded-circle profile-img'>
                            <img class='rounded-circle img-fluid d-flex mx-auto m-auto' style='object-fit:cover; width:100%; height:100%' src='/profile_pictures/{$data['img']}' alt=''>
                        </div>
            
                        <!-- CHAT MESSAGE -->
                        <div class='msg' style='font-weight:bolder'>
                            <div class='name'>
                                {$data['fullname']}" . (($user_id === $data["user_id"]) ? ' (you)' : '') . "
                                {$verify}
                            </div>
                            <div class='body'>" . (($b === "seen" || $body == "No Messages") ? $body : "<b>$body</b>") . "</div>
                        </div>
            
                        <!-- ACTIVE STATUS -->
                        <div class='status'>
                            {$ss}
                        </div>
                    </div>";
                    
        } else { //if there's no messages
            $body = "No Messages";
        }
    }