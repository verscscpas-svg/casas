<?php
require_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Basic data
    $empNo = $_POST['empNo'];
    $empName = $_POST['empName'];
    $designation = $_POST['designation'];
    $email = $_POST['email'];

    // Clean salary (remove ₱ and commas)
    $salary = str_replace(['₱', ','], '', $_POST['salary']);

    // Clean IDs (remove dash)
    $tinNo = str_replace('-', '', $_POST['tinNo']);
    $sssNo = str_replace('-', '', $_POST['sssNo']);
    $philHealthNo = str_replace('-', '', $_POST['philHealthNo']);
    $pagIbigNo = str_replace('-', '', $_POST['pagIbigNo']);

    // 🔑 Auto-set password = empNo (hashed)
    $password = password_hash($empNo, PASSWORD_DEFAULT);

    // 🔥 Check duplicate Employee No
    $check = "SELECT * FROM employees WHERE empNo = '$empNo'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        die("Employee number already exists!");
    }

    // =========================
    // IMAGE UPLOAD
    // =========================
    $imageName = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

        $targetDir = "uploads/";

        // Generate unique filename
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;

        // File type validation
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Invalid image type! Only JPG, JPEG, PNG allowed.");
        }

        // Move file
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            die("Failed to upload image.");
        }
    }

    // =========================
    // INSERT QUERY
    // =========================
    $sql = "INSERT INTO employees 
        (empNo, empName, designation, email, salary, tinNo, sssNo, philHealthNo, pagIbigNo, image, password, status)
        VALUES 
        ('$empNo', '$empName', '$designation', '$email', '$salary', '$tinNo', '$sssNo', '$philHealthNo', '$pagIbigNo', '$imageName', '$password', 'Employed')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../add-emp.php?success=1");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
