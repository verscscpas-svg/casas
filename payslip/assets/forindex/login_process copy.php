<?php
session_start();
require_once 'connection.php'; // your existing DB connection

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$identifier = trim($_POST['user'] ?? '');
$password   = trim($_POST['pass'] ?? '');

// Basic validation
if (empty($identifier) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

/*
 * Query: match by empNo OR email.
 * Adjust table/column names below to match your actual schema:
 *   Table   → employees  (change if different)
 *   Columns → empNo, Email, password, EmpName, Role
 */
$sql  = "SELECT * FROM employees 
         WHERE (empNo = ? OR Email = ?) 
         LIMIT 1";
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

/*
 * password check.
 * ─ If you store passwords with password_hash()  → use password_verify()  ✔ (recommended)
 * ─ If you store plain text or MD5/SHA1          → replace with the matching comparison below
 */
$passwordMatch = password_verify($password, $employee['password']);
// Plain-text fallback (remove if using hashed passwords):
// $passwordMatch = ($password === $employee['password']);
// MD5 fallback:
// $passwordMatch = (md5($password) === $employee['password']);

if (!$passwordMatch) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials. Please try again.']);
    exit;
}

// ── Login success: store session ──────────────────────────────────────────────
$_SESSION['logged_in']  = true;
$_SESSION['empNo']      = $employee['empNo'];
$_SESSION['EmpName']    = $employee['EmpName'] ?? '';
$_SESSION['Email']      = $employee['Email']   ?? '';
$_SESSION['Role']       = $employee['Role']    ?? 'employee'; // admin / employee / etc.

$stmt->close();
$conn->close();

echo json_encode([
    'success'  => true,
    'message'  => 'Login successful.',
    'redirect' => 'assets/employee/index.php',   // ← change to your actual dashboard page
    'role'     => $_SESSION['Role'],
]);
exit;
