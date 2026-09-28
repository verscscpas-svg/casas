<?php
session_start();
session_unset();
session_destroy();

// Redirect to login page — adjust path if needed
header('Location: ../../index.php');
exit;
