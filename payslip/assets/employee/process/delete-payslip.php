<?php
include 'connection.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id']);

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
    exit();
}

$result = mysqli_query($conn, "DELETE FROM payslips WHERE id = '$id'");

if ($result) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}
