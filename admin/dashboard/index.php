<?php
    session_start();
    $user_id = $_SESSION["user_id"];
    
    if (!isset($user_id)) {
        header("location: /login.php");
        exit;
    }
    
    include_once "../../configs/db.php";
    $sql = "SELECT * FROM users WHERE user_id = {$user_id}";
    $sqlQuery = $conn->query($sql)->fetch_object();
    
    if ($sqlQuery->admin === "false") {
        include_once "../../error/501.html";
        exit;
    }
    
    /* *** USER IS AN ADMIN *** */
    /* *** ADMIN DASHBOARD *** */
    
    /* Ammount of all Users */
    $sql_users = "SELECT * FROM users";
    $users_ammount = $conn->query($sql_users)->num_rows;
    
    /* All Admins */
    $sql_admins = "SELECT * FROM users WHERE admin = 'true'";
    $admins = $conn->query($sql_admins);
    
    /* All Restricted Accounts */
    $sql_restricted = "SELECT * FROM users WHERE acct_status = 'restricted'";
    $restricted = $conn->query($sql_restricted);
    
    /* Ammount of all Messages */
    $sql_messages = "SELECT * FROM messages";
    $messages = $conn->query($sql_messages)->num_rows;
    
    /* Ammount of all Blocked Users */
    $sql_blocked = "SELECT * FROM blocked";
    $blocked = $conn->query($sql_blocked)->num_rows;
    
    /* Ammount of all Archived Users */
    $sql_achieve = "SELECT * FROM achieve";
    $achieve = $conn->query($sql_achieve)->num_rows;
    
    /* Accounts to be Restricted */
    $sql_ban = "SELECT * FROM blocked JOIN users ON blocked.block = users.user_id 
                    WHERE NOT (users.admin = 'true' OR users.acct_status = 'restricted') ORDER BY blocked.id ASC";
    $ban = $conn->query($sql_ban);
    
    $restrict_res = "";
    while ($restrict = $ban->fetch_object()) {
        $final_ban_sql = "SELECT * FROM blocked WHERE block = {$restrict->user_id}";
        $final_ban = $conn->query($final_ban_sql)->num_rows;
        
        if ($final_ban >= 5) {
            $restrict_res .= "<div class='users'>
                                <div class='img' onclick='Redirect({$restrict->user_id})'>
                                    <img src='../../profile_pictures/{$restrict->img}' 
                                        alt='{$restrict->firstname} Profile Picture' />
                                </div>
                                
                                <span>{$restrict->firstname} {$restrict->lastname}</span>
                                <button style='background:darkgreen' onclick='Restrict({$restrict->user_id})'>Restrict</button>
                            </div>";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="theme-color" content="#505b62" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="title" content="Admin Dashboard">
    
        <!-- FAVICON ICONS -->
        <link rel="icon" href="../../img/icon.png" type="image/png" />
        <link rel="shortcut icon" href="../../img/icon.png" type="image/png" />
        <link rel="apple-touch-icon" href="../../img/icon.png" />
        
        <script src="../../assets/jquery/jquery.min.js"></script>
        <script src="../../assets/jquery/jquery2.min.js"></script>
        <script src="../../assets/jquery/jquery-ajax.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        
        <link rel="stylesheet" href="style.css" type="text/css" media="all" />
        <title>Admin Dashboard</title>
    </head>
    <body>
        <!-- ========[ HEADING ]======== -->
        <header>
            <div>
                
            </div>
        </header>
        <!-- ========[ // END HEADING ]======== -->
        
        <!-- ========[ BODY ]======== -->
        <center>
            <h3>Admin Dashboard</h3>
            <p><?php echo $sqlQuery->firstname . ' ' . $sqlQuery->lastname; ?>, You're an Admin of HighChat</p>
        </center>
        
        <br />
        <div class="info">
            <div class="row">
                <div class="users">
                    <strong>All Users</strong>
                    <p><?php echo ($users_ammount === 0 ? 0 : $users_ammount) ?></p>
                </div>
                
                <div class="messages">
                    <strong>All Messages</strong>
                    <p><?php echo ($messages === 0 ? 0 : $messages) ?></p>
                </div>
            </div>
            
            <div class="row">
                <div class="block">
                    <strong>All Blocked Users</strong>
                    <p><?php echo ($blocked === 0 ? 0 : $blocked) ?></p>
                </div>
                
                <div class="achieve">
                    <strong>All Archived Users</strong>
                    <p><?php echo ($achieve === 0 ? 0 : $achieve) ?></p>
                </div>
            </div>
            
            <div class="admins" style="text-align:center; color:white">
                <strong>All Admins</strong>
                <p><?php echo $admins->num_rows ?></p>
            </div>
        </div>
        
        <!-- ADMINS -->
        <h3 style="text-align:center">Admins</h3>
        <div class="user">
            <?php while ($admin = $admins->fetch_object()): ?>
                <div class="users" onclick="Redirect(<?php echo $admin->user_id ?>)">
                    <div class="img">
                        <img src="<?php echo '../../profile_pictures/' . $admin->img ?>" 
                            alt="<?php echo $admin->firstname . '\'s Profile Picture' ?>" />
                    </div>
                    
                    <span><?php echo $admin->firstname . ' ' . $admin->lastname ?></span>
                </div>
            <?php endwhile; ?>
        </div>
        
        <!-- ACCOUNTS TO BE RESTRICTED -->
        <h3 style="text-align:center; margin-top:25px">Accounts to be Restricted</h3>
        <div class="user">
            <?php echo (empty($restrict_res) ? '<p style="text-align:center">No Account to be Restricted Yet!</p>' : $restrict_res); ?>
        </div>
        
        <!-- RESTRICTED ACCOUNTS -->
        <h3 style="text-align:center">Restricted Accounts</h3>
        <div class="user">
            <?php if ($restricted->num_rows > 0): ?>
                <?php while ($restricted_data = $restricted->fetch_object()): ?>
                    <div class="users">
                        <div class="img" onclick="Redirect(<?php echo $restricted_data->user_id ?>)">
                            <img src="<?php echo '../../profile_pictures/' . $restricted_data->img ?>" 
                                alt="<?php echo $restricted_data->firstname . '\'s Profile Picture' ?>" />
                        </div>
                        
                        <span><?php echo $restricted_data->firstname . ' ' . $restricted_data->lastname ?></span>
                        <button onclick="Unrestrict(<?php echo $restricted_data->user_id ?>)">Unrestrict</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center;">There's no Restricted Account Yet!</p>
            <?php endif; ?>
        </div>
        <!-- ========[ // END BODY ]======== -->
        
        <!-- ========[ FOOTER ]======== -->
        <footer>
            
        </footer>
        <!-- ========[ // END FOOTER ]======== -->
        
        <script>
            function Redirect(id) {
                window.location = "/chats/message.php?user_id=" + id;
            }
            
            function Restrict(id) {
                if (confirm("Are you sure to Restrict this User? Which user_id=" + id)) {
                    $.ajax({
                        type: "POST",
                        url: "./configs/restrict.php",
                        data: "id=" + id,
                        success: function (data) {
                            alert(data);
                        }
                    })
                }
            }
            
            function Unrestrict(id) {
                if (confirm("Are you sure to Unrestrict this User? Which user_id=" + id)) {
                    $.ajax({
                        type: "POST",
                        url: "./configs/unrestrict.php",
                        data: "id=" + id,
                        success: function (data) {
                            alert(data);
                        }
                    })
                }
            }
        </script>
    </body>
</html>