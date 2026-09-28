<?php
include 'connection.php';

$id = $_POST['id'];
$empNo = $_POST['empNo'];
$empName = $_POST['empName'];
$designation = $_POST['designation'];
$email = $_POST['email'];
$salary = $_POST['salary'];
$tin = $_POST['tinNo'];
$sss = $_POST['sssNo'];
$phil = $_POST['philHealthNo'];
$pagibig = $_POST['pagIbigNo'];
$status = $_POST['Stats'];

// Define upload folder (same folder na ginagamit sa front-end)
$uploadDir = "uploads/"; // process/uploads/

/* =========================
   IMAGE UPLOAD
========================= */
if (!empty($_FILES['image']['name'])) {

    // ✅ KUHANIN MUNA ANG LUMANG IMAGE
    $oldResult = mysqli_query($conn, "SELECT image FROM employees WHERE id='$id'");
    $oldRow = mysqli_fetch_assoc($oldResult);
    $oldImage = $oldRow['image'];

    // ✅ I-DELETE ANG LUMANG IMAGE (kung hindi siya default/placeholder)
    if (!empty($oldImage) && file_exists($uploadDir . $oldImage)) {
        unlink($uploadDir . $oldImage);
    }

    // ✅ I-UPLOAD ANG BAGONG IMAGE
    $newImage = $_FILES['image']['name'];
    $tempPath = $_FILES['image']['tmp_name'];

    move_uploaded_file($tempPath, $uploadDir . $newImage);

    $query = "UPDATE employees SET 
        empNo='$empNo',
        empName='$empName',
        designation='$designation',
        email='$email',
        salary='$salary',
        tinNo='$tin',
        sssNo='$sss',
        philHealthNo='$phil',
        pagIbigNo='$pagibig',
        status='$status',
        image='$newImage'
        WHERE id='$id'
    ";
} else {

    // Walang bagong image, huwag palitan
    $query = "UPDATE employees SET 
        empNo='$empNo',
        empName='$empName',
        designation='$designation',
        email='$email',
        salary='$salary',
        tinNo='$tin',
        sssNo='$sss',
        philHealthNo='$phil',
        pagIbigNo='$pagibig',
        status='$status'
        WHERE id='$id'
    ";
}

/* EXECUTE */
if (mysqli_query($conn, $query)) {
    header("Location: ../index.php");
} else {
    echo "Error: " . mysqli_error($conn);
}
exit();
