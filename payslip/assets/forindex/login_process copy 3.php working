<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$identifier = trim($_POST['user'] ?? '');
$password   = trim($_POST['pass'] ?? '');

if (empty($identifier) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

$sql  = "SELECT * FROM employees WHERE (empNo = ? OR Email = ?) LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    exit;
}

$stmt->bind_param('ss', $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials. Please try again.']);
    exit;
}

$employee = $result->fetch_assoc();

$passwordMatch = password_verify($password, $employee['password']);
// Plain-text fallback (remove if using hashed passwords):
// $passwordMatch = ($password === $employee['password']);

if (!$passwordMatch) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials. Please try again.']);
    exit;
}

// Set session
$_SESSION['logged_in'] = true;
$_SESSION['empNo']     = $employee['empNo'];
$_SESSION['EmpName']   = $employee['EmpName'] ?? '';
$_SESSION['Email']     = $employee['Email']   ?? '';
$_SESSION['Role']      = $employee['Role']    ?? 'employee';
$_SESSION['last_login'] = date('M d, Y g:i A');

// Tell the front-end to clear the welcome flag so modal always
// appears once on every fresh login
$empNo = $employee['empNo'];

$stmt->close();
$conn->close();

echo json_encode([
    'success'      => true,
    'message'      => 'Login successful.',
    'redirect'     => 'assets/employee/index.php',  // adjust to your dashboard path
    'empNo'        => $empNo,                // sent so JS can clear the right sessionStorage key
]);
exit;
