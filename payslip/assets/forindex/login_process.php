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

$identifier = mysqli_real_escape_string($conn, $identifier);

$sql = "SELECT * FROM employees WHERE (empNo = '$identifier' OR Email = '$identifier') LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials. Please try again.']);
    exit;
}

$employee = mysqli_fetch_assoc($result);

$passwordMatch = password_verify($password, $employee['password']);

if (!$passwordMatch) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials. Please try again.']);
    exit;
}

// ✅ ROLE CHECK — only superadmins allowed on this login page
$designation = strtolower(trim($employee['designation'] ?? ''));

if ($designation !== 'supervisor') {
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Please use the Employee Login page instead.'
    ]);
    exit;
}

$_SESSION['logged_in']  = true;
$_SESSION['empNo']      = $employee['empNo'];
$_SESSION['empName']    = $employee['empName'] ?? '';
$_SESSION['Email']      = $employee['Email']   ?? '';
$_SESSION['Role']       = $employee['designation'] ?? 'designation';
$_SESSION['last_login'] = date('M d, Y g:i A');

$empNo = $employee['empNo'];

mysqli_close($conn);

echo json_encode([
    'success'  => true,
    'message'  => 'Login successful.',
    'redirect' => 'assets/employee/index.php',
    'empNo'    => $empNo,
]);
exit;
