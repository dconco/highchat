<?php
    $output = "";
    
    while ($data = $result->fetch_assoc()) {

        $sqlFollow = "SELECT * FROM follow WHERE follow = {$data['user_id']} AND follow_by = {$user_id}"; // if user follow him
        $sqlFollow2 = "SELECT * FROM follow WHERE follow = {$user_id} AND follow_by = {$data['user_id']}"; // if he follow user
            
        if ($conn->query($sqlFollow)->num_rows > 0 && $conn->query($sqlFollow2)->num_rows > 0) { # if they follow eachother
            $follow = "Friends";
            $color = "grey";
        } 
        else if ($conn->query($sqlFollow)->num_rows > 0) { # if user follow him
            $follow = "Unfollow";
            $color = "darkred";
        }
        else if ($conn->query($sqlFollow2)->num_rows > 0) { # if he follow user
            $follow = "Follow Back";
            $color = "blue";
        } 
        else { # if user did not follow him
            $follow = "Follow";
            $color = "green";
        }
        
        /* VERIFY MARK */
        $sqlFollowers = "SELECT * FROM follow WHERE follow = {$data['user_id']} AND (follow_by 
            NOT IN (SELECT friend_id FROM friends WHERE user_id = {$data['user_id']}) AND follow_by 
            NOT IN (SELECT user_id FROM friends WHERE friend_id = {$data['user_id']})) 
            ORDER BY id DESC"; // select all followers
            
        $followers = $conn->query($sqlFollowers);
        
        if ($followers->num_rows >= 20 && $data["email_verify"] === $data["email"]) {
            $verify2 = "true";
            $verify = "<i class='bi-check-circle-fill' style='font-size:13px;color:green'></i>";
        } 
        else if ($data["email_verify"] !== $data["email"]) {
            $verify = "";
            $verify2 = "none";
        } 
        else {
            $verify = "";
            $verify2 = "false";
        }
    
        /* BLOCK / UNBLOCK USER */
        $sqlBlock = "SELECT * FROM blocked WHERE block = {$data['user_id']} AND block_by = {$user_id}";
        if ($conn->query($sqlBlock)->num_rows > 0) {
            $user_block = 'true';
        } else {
            $user_block = 'false';
        }
        
        $sqlBlock2 = "SELECT * FROM blocked WHERE (block = {$data['user_id']} AND block_by = {$user_id}) 
                                                OR (block_by = {$data['user_id']} AND block = {$user_id}) LIMIT 1";
                                                
        if ($conn->query($sqlBlock2)->num_rows > 0) { #if user us blocked
            $block_res = $conn->query($sqlBlock2)->fetch_assoc();
        
            $b = "seen";
            $ss = "<i class='bi bi-dash-circle-fill text-danger'></i>";
            if ($block_res["block_by"] === $user_id) {
                $body = "<i class='bi bi-dash-circle'></i> You Blocked this user!";
            } else {
                $body = "<i class='bi bi-dash-circle'></i> You've been Blocked!";
            }
        } else {
            if (strlen($data["about"]) > 33) {
                $body = substr($data["about"], 0, 33) . '...';
            } else if (empty($data["about"])) {
                $body = 'No About!'; 
            } else {
                $body = $data["about"];
            }
            
            $time = time();
            if ($time - $data["status"] >= 300) {
                // status is offline
                $ss = "<i class='offline bi bi-circle-fill'></i>";
            } else {
                // status is active
                $ss = "<i class='active bi bi-circle-fill'></i>";
            }
        }
        
        if ($data["user_id"] === $user_id) {
            $fullname = $data['fullname'] . ' (you)';
        } else {
            $fullname = $data['fullname'];
            $followElem = 
            "• 
            <span id='{$data['user_id']}' onclick='follow({$data['user_id']})' style='font-size:13px;color:{$color}'>{$follow}</span>";
        }

        $output .= "<div class='d-flex flex-row main-cont' oncontextmenu='Menu({$data['user_id']}, `{$user_block}`, `{$verify2}`)' data-aos='fade-up'>
                        <!-- PROFILE PICTURE -->
                        <div class='img-fluid bg-secondary rounded-circle profile-img' onclick='user({$data['user_id']})'>
                            <img class='rounded-circle img-fluid d-flex mx-auto m-auto' style='object-fit:cover; width:100%; height:100%' src='/profile_pictures/{$data['img']}' alt=''>
                        </div>
            
                        <!-- CHAT MESSAGE -->
                        <div class='msg' style='font-weight:bolder'>
                            <div class='name'>
                                {$fullname}
                                {$verify}
                                {$followElem}
                            </div>
                            <div class='body'>{$body}</div>
                        </div>
            
                        <!-- ACTIVE STATUS -->
                        <div class='status'>
                            {$ss}
                        </div>
                    </div>";
    }