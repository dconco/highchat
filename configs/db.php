<?php
    $conn = new mysqli("0.0.0.0", "root", "root", "highchat");
    if (!$conn || $conn->connect_error) {
        $status["error"] = "Error while connecting! Please try again.";
        die("Cannot connect with database!");
    }