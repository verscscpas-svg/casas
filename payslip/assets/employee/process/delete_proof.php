<?php
require 'connection.php'; // your DB connection file

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payslipId = intval($_POST['payslip_id']);

    if (!$payslipId) {
        echo json_encode(['success' => false, 'message' => 'Invalid payslip ID.']);
        exit;
    }

    // ─── Step 1: Get the current filename from DB ─────────────────
    $fetchQuery = mysqli_query($conn, "SELECT proof_of_payment FROM payslips WHERE id = $payslipId");

    if (!$fetchQuery || mysqli_num_rows($fetchQuery) === 0) {
        echo json_encode(['success' => false, 'message' => 'Payslip not found.']);
        exit;
    }

    $payslipRow = mysqli_fetch_assoc($fetchQuery);
    $filename   = $payslipRow['proof_of_payment'];

    // ─── Step 2: Delete the actual file from the server ──────────
    if (!empty($filename)) {
        $filePath = 'uploads/proof/' . $filename;
        if (file_exists($filePath)) {
            unlink($filePath); // delete the image file
        }
    }

    // ─── Step 3: Clear the column in the database ────────────────
    $updateQuery = "UPDATE payslips SET proof_of_payment = NULL WHERE id = $payslipId";

    if (mysqli_query($conn, $updateQuery)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update database.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
