<?php
require 'connection.php'; // your DB connection file

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payslipId = intval($_POST['payslip_id']);
    $uploadDir = 'uploads/proof/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = mime_content_type($_FILES['proof_image']['tmp_name']);

        if (!in_array($mimeType, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
            exit;
        }

        $ext      = pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION);
        $filename = 'proof_' . $payslipId . '_' . time() . '.' . $ext;
        $dest     = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $dest)) {
            $safeFilename = mysqli_real_escape_string($conn, $filename);
            $updateQuery  = "UPDATE payslips SET proof_of_payment = '$safeFilename' WHERE id = $payslipId";

            if (mysqli_query($conn, $updateQuery)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'DB update failed.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'File move failed.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file received.']);
    }
}
