<?php
// config.php (modern PHP)

// Database configuration
$host = "localhost";
$user = "root";
$pass = "";
$db   = "payroll_cs";

// Create connection using mysqli
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8");
