<?php

/**
 * check_employee.php
 * Live AJAX endpoint — checks if identifier exists in employees table.
 * Returns JSON: { found: bool, email_hint: string }
 *
 * Place in root (same folder as index.php).
 */

header('Content-Type: application/json');

require_once '../employee/process/connection.php'; // gives us $conn (mysqli)

$identifier = trim($_POST['identifier'] ?? '');

if ($identifier === '') {
    echo json_encode(['found' => false]);
    exit;
}

// Prepared statement — secure against SQL injection
$stmt = $conn->prepare(
    "SELECT email FROM employees
     WHERE empNo = ? OR email = ?
     LIMIT 1"
);
$stmt->bind_param('ss', $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();
$row    = $result->fetch_assoc();
$stmt->close();

if ($row) {
    // Mask email: jo**@gmail.com
    $email  = $row['email'];
    $parts  = explode('@', $email);
    $masked = substr($parts[0], 0, 2)
        . str_repeat('*', max(strlen($parts[0]) - 2, 2))
        . '@' . $parts[1];

    echo json_encode(['found' => true, 'email_hint' => $masked]);
} else {
    echo json_encode(['found' => false]);
}
