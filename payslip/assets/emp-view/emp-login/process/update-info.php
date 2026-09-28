<?php
include '../../foremp/connection.php';

// --- Sanitize inputs ---
$id          = mysqli_real_escape_string($conn, $_POST['id']);
$empNo       = mysqli_real_escape_string($conn, $_POST['empNo']);
$empName     = mysqli_real_escape_string($conn, $_POST['empName']);
$designation = mysqli_real_escape_string($conn, $_POST['designation'] ?? '');
$email       = mysqli_real_escape_string($conn, $_POST['email']);
$password    = mysqli_real_escape_string($conn, $_POST['Password']);
$tin         = mysqli_real_escape_string($conn, $_POST['tinNo']);
$sss         = mysqli_real_escape_string($conn, $_POST['sssNo']);
$phil        = mysqli_real_escape_string($conn, $_POST['philHealthNo']);
$pagibig     = mysqli_real_escape_string($conn, $_POST['pagIbigNo']);
$status      = mysqli_real_escape_string($conn, $_POST['Stats']);

// --- Clean salary - alisin ang ₱, comma, at spaces ---
$salary = str_replace(['₱', ',', ' '], '', $_POST['salary']);
$salary = floatval($salary);
$salary = mysqli_real_escape_string($conn, $salary);

$uploadDir = "../../../employee/process/uploads/";

// --- Fetch current image & bankqr ---
$oldResult = mysqli_query($conn, "SELECT image, bankqr FROM employees WHERE id='$id'");
$oldRow    = mysqli_fetch_assoc($oldResult);
$newImage  = $oldRow['image'];   // default: keep old
$newBankQr = $oldRow['bankqr'];  // default: keep old

/* ========================
   PROFILE IMAGE UPLOAD
======================== */
if (!empty($_FILES['image']['name'])) {
    if (!empty($oldRow['image']) && file_exists($uploadDir . $oldRow['image'])) {
        unlink($uploadDir . $oldRow['image']);
    }
    $newImage = basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newImage);
}

/* ========================
   BANK QR CODE UPLOAD
======================== */
if (!empty($_FILES['bankqr']['name'])) {
    if (!empty($oldRow['bankqr']) && file_exists($uploadDir . $oldRow['bankqr'])) {
        unlink($uploadDir . $oldRow['bankqr']);
    }
    $newBankQr = basename($_FILES['bankqr']['name']);
    move_uploaded_file($_FILES['bankqr']['tmp_name'], $uploadDir . $newBankQr);
}

/* ========================
   PASSWORD HANDLING
======================== */
$passwordField = '';
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $hashedPassword = mysqli_real_escape_string($conn, $hashedPassword);
    $passwordField  = ", Password='$hashedPassword'";
}

/* ========================
   BUILD & RUN QUERY
======================== */
$query = "UPDATE employees SET 
    empNo        = '$empNo',
    empName      = '$empName',
    designation  = '$designation',
    email        = '$email',
    salary       = '$salary',
    tinNo        = '$tin',
    sssNo        = '$sss',
    philHealthNo = '$phil',
    pagIbigNo    = '$pagibig',
    status       = '$status',
    image        = '$newImage',
    bankqr       = '$newBankQr'
    $passwordField
    WHERE id     = '$id'
";

if (mysqli_query($conn, $query)) {
    header("Location: ../index.php?id=" . $id . "&updated=1");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}
