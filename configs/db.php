<?php
    $conn = new mysqli("0.0.0.0", "root", "root", "spyro");
    if (!$conn || $conn->connect_error) {
        $status["error"] = "Error while connecting! Please try again.";
        die("Cannot connect with database!");
    }