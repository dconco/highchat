<?php
    $outgoing_id = $_COOKIE["user_id"];
    $incoming_id = $_GET["incoming_id"];
    
    include_once("db.php");
    
    $sql = "UPDATE messages SET clear_outgoing = '$outgoing_id' WHERE outgoing_msg_id = '$outgoing_id' AND incoming_msg_id = '$incoming_id'";
    $sql2 = "UPDATE messages SET clear_incoming = '$outgoing_id' WHERE outgoing_msg_id = '$incoming_id' AND incoming_msg_id = '$outgoing_id'";
    
    if ($conn->query($sql) && $conn->query($sql2)) {
        echo "All user messages deleted";

        //check if the two users cleared their chats
        $sql = "SELECT * FROM messages WHERE (clear_outgoing = '{$outgoing_id}' OR clear_incoming = '{$outgoing_id}')";
        while ($data = $conn->query($sql)->fetch_assoc()) {
            if (!empty($data["clear_incoming"]) && !empty($data["clear_outgoing"])) {
                $conn->query("DELETE FROM messages WHERE msg_id = '{$data['msg_id']}'");
            }
        }
    } else {
        die();
    }