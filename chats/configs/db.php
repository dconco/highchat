<?php
    $conn = new mysqli("0.0.0.0", "root", "root", "highchat");
    if (!$conn || $conn->connect_error) {
        die("Cannot connect with database!");
    }
    