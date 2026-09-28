<?php
// config.php (modern PHP)

// Database configuration
// Database configuration
$host = "localhost";
$user = "u277653476_casaspayslip";
$pass = "@Casassanluisco2023";
$db   = "u277653476_casas_payslip";

// Create connection using mysqli
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8");
