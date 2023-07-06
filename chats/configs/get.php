<?php
    $outgoing_id = htmlspecialchars($_GET["outgoing_id"]);
    $incoming_id = htmlspecialchars($_GET["incoming_id"]);
    
    include_once "db.php";
    include_once "../../encryption/decrypt.php";
    
    $output = "";
    $sql = "SELECT * FROM messages WHERE NOT (clear_outgoing = '{$outgoing_id}' OR clear_incoming = '{$outgoing_id}')
                        AND ((outgoing_msg_id = '{$outgoing_id}' AND incoming_msg_id = '{$incoming_id}')
                            OR (outgoing_msg_id = '{$incoming_id}' AND incoming_msg_id = '{$outgoing_id}')) ORDER BY msg_id ASC";
    
    /* SELECT IMG */
    $userImg = "SELECT img FROM users WHERE user_id = '$incoming_id'";
    $img = $conn->query($userImg)->fetch_assoc();
    
    $res = $conn->query($sql);
    if ($res->num_rows > 0) {
        while ($data = $res->fetch_assoc()) {
            include_once "stickers.php";
            
            $decrypt_msg = decrypt($data["encrypt_key"], $data["msg"], $data["iv_value"]);
            
            //replace to link if message include link
            $pattern = '/((http|https|ftp):\/\/)?([\w]+\.)+[\w]{2,5}([\-\/\?\%\&\;\&;\:\#\@\=\.a-zA-Z0-9_]*)?/';
            $url = '<a href="/webview.html?type=webview&url=$0" target="_blank">$0</a>';
            $msg = preg_replace($pattern, $url, $decrypt_msg);
            
            $msg_id = $data["msg_id"];
            
            if ($data["outgoing_msg_id"] === $outgoing_id) // THIS IS THE SENDER
            {
            
                $status = ($data["status"] === 'seen') ? "seen" : "sent";
                
                switch ($data["type"]) {
                    
                    /* FOR SHOWING OF PICTURES */
                    case 'image':
                    
                    break; //end picture
                    
                    
                    /* FOR SHOWING OF VIDEOS */
                    case 'video':
                    
                    break; //end videos
                    
                    
                    /* FOR SHOWING OF AUDIOS */
                    case 'audio':
                    
                    break; //end audios
                    
                    
                    /* FOR SHOWING OF DOCUMENTS */
                    case 'doc':
                    
                    break; //end documents
                    
                    
                    /* FOR SHOWING OF NORMAL MESSAGE */
                    default:
                        if (in_array($msg, $stickers)) //if the message is a sticker
                        {
                            $output .= "<!-- OUTGOING STICKER -->
                                        <div class='sticker_outgoing d-flex justify-content-end' oncontextmenu='deleteMsg($msg_id)'>
                                            <div class='stick'>
                                                <img src='/stickers/$msg.png' alt='$msg'>
                                                <p class='text-secondary text-end' style='font-size:10px'>$status</p>
                                            </div>
                                        </div>";
                        } 
                        else { // if is only a message
                            $output .= "<!-- OUTGOING MESSAGE -->
                                        <div class='outgoing-msg d-flex justify-content-end' ondblclick='copyMsg(`$msg_id`)' oncontextmenu='deleteMsg($msg_id)' style='margin-right:13px'>
                                            <div class='msg bg-secondary'>
                                                <span class='text-light' id='$msg_id'>" . nl2br($msg) . "</span>
                                            </div>
                                        </div>
                                        <p class='text-secondary status text-end' style='font-size:10px; margin-right:1pc'>$status</p>";
                        }
                    break;
                } //end switch
                
            } 
            else { // THIS IS THE RECEIVER
            
                switch ($data["type"]) {
                    
                    /* FOR SHOWING OF PICTURES */
                    case 'image':
                    
                    break;
                    
                    
                    /* FOR SHOWING OF VIDEOS */
                    case 'video':
                    
                    break;
                    
                    
                    /* FOR SHOWING OF AUDIOS */
                    case 'audio':
                    
                    break;
                    
                    
                    /* FOR SHOWING OF DOCUMENTS */
                    case 'doc':
                    
                    break;
                    
                    
                    /* FOR SHOWING OF NORMAL MESSAGE */
                    default:
                        if (in_array($msg, $stickers)) //if the message is a sticker message
                        {
                            $output .= "<!-- INCOMING STICKER -->
                                        <div class='sticker_incoming d-flex'>
                                            <div class='rounded-circle img-fluid bg-secondary sticker-img' style='border:2px solid #fff;' onclick='profile()'>
                                                <img class='rounded-circle img-fluid d-flex mx-auto m-auto' style='object-fit:cover; width:100%; height:100%' src='/profile_pictures/{$img['img']}' alt=''>
                                            </div>
                                            
                                            <div class='stick'>
                                                <img src='/stickers/$msg.png' alt='$msg'>
                                            </div>
                                        </div>";
                        } 
                        else { // if is normal message
                            $output .= "<!-- INCOMING MESSAGE -->
                                        <div class='incoming-msg d-flex' style='margin-left:13px' ondblclick='copyMsg(`$msg_id`)'>
                                            <div class='rounded-circle bg-secondary img-fluid msg-img' onclick='profile()' style='border:2px solid #fff;'>
                                                <img class='rounded-circle img-fluid d-flex mx-auto m-auto' style='object-fit:cover; width:100%; height:100%' src='/profile_pictures/{$img['img']}' alt=''>
                                            </div>
                                    
                                            <div class='msg'>
                                                <span id='$msg_id'>" . nl2br($msg) . "</span>
                                            </div>
                                        </div>";
                        }
                    break;
                } //end switch
                
            } //end receiver
        } //end while loop
        echo $output;
    } else {
        echo "<p class='text-center' style='font-size:13px'>No messages available! Send Hi! To start conversations.</p>";
    }