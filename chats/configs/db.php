<?php
    $conn = new mysqli("0.0.0.0", "root", "root", "spyro");
    if (!$conn || $conn->connect_error) {
        die("Cannot connect with database!");
    }
    