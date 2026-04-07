<?php
session_start();

// Check if a session variable exists (e.g., 'user_id')
if (!isset($_SESSION['user_id'])) {
    header("Location: ../");
    exit();
}
