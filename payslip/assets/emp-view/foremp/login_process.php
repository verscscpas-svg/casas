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

// ESCAPE INPUT
$identifier = mysqli_real_escape_string($conn, $identifier);

// QUERY
$sql    = "SELECT * FROM employees WHERE (empNo = '$identifier' OR email = '$identifier') LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials. Please try again.']);
    exit;
}

$employee = mysqli_fetch_assoc($result);

// ✅ TEMPORARY DEBUG — remove after fixing
// echo json_encode([
//     'debug_hash'   => $employee['password'],
//     'debug_input'  => $password,
//     'debug_verify' => password_verify($password, $employee['password']),
//     'debug_emp'    => $employee,
// ]);
// exit;

// ✅ AUTO REHASH — kung hindi pa hashed ang password sa DB
if (substr($employee['password'], 0, 4) !== '$2y$') {
    $newHash = password_hash($employee['password'], PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE employees SET password = '$newHash' WHERE id = '{$employee['id']}'");
    $employee['password'] = $newHash;
}

// VERIFY PASSWORD
if (!password_verify($password, trim($employee['password']))) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials. Please try again.']);
    exit;
}

// BLOCK superadminS
$designation = strtolower(trim($employee['designation'] ?? ''));
if ($designation === 'superadmin') {
    echo json_encode([
        'success' => false,
        'message' => 'You are not supposed to use this. Please use the Admin Side Login.'
    ]);
    exit;
}

// SET SESSION
$_SESSION['logged_in']  = true;
$_SESSION['emp_id']     = $employee['id'];
$_SESSION['empNo']      = $employee['empNo'];
$_SESSION['empName']    = $employee['empName'] ?? '';
$_SESSION['email']      = $employee['email']   ?? '';
$_SESSION['Role']       = $employee['designation'] ?? '';
$_SESSION['last_login'] = date('M d, Y g:i A');

$empNo = $employee['empNo'];

mysqli_close($conn);

echo json_encode([
    'success'  => true,
    'message'  => 'Login successful.',
    'redirect' => 'emp-login/index.php?id=' . $employee['id'],
    'empNo'    => $empNo,
]);
exit;
