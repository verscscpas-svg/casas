    <?php
    require_once "connection.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $empNo        = mysqli_real_escape_string($conn, $_POST['empNo']);
        $empName      = mysqli_real_escape_string($conn, $_POST['empName']);
        $designation  = mysqli_real_escape_string($conn, $_POST['designation']);
        $email        = mysqli_real_escape_string($conn, $_POST['email']);

        $salary       = str_replace(['₱', ','], '', $_POST['salary']);
        $tinNo        = str_replace('-', '', $_POST['tinNo']);
        $sssNo        = str_replace('-', '', $_POST['sssNo']);
        $philHealthNo = str_replace('-', '', $_POST['philHealthNo']);
        $pagIbigNo    = str_replace('-', '', $_POST['pagIbigNo']);

        // 🔑 Default password = empNo
        $password  = password_hash($empNo, PASSWORD_DEFAULT);
        $status    = "Employed";

        // CHECK DUPLICATE
        $check = mysqli_query($conn, "SELECT empNo FROM employees WHERE empNo = '$empNo'");
        if (mysqli_num_rows($check) > 0) {
            die("Employee already exists!");
        }

        /* =========================
        IMAGE UPLOAD
        ========================= */
        $uploadDir = "uploads/";
        $imageName = null;

        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                die("Invalid image format. Allowed: jpg, jpeg, png, gif, webp");
            }

            // Use original filename (same pattern as your reference)
            $imageName = $_FILES['image']['name'];
            $tempPath  = $_FILES['image']['tmp_name'];

            // Create folder if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!move_uploaded_file($tempPath, $uploadDir . $imageName)) {
                die("Failed to upload image.");
            }
        }

        /* =========================
        INSERT
        ========================= */
        $sql = "INSERT INTO employees 
            (empNo, empName, designation, email, salary, tinNo, sssNo, philHealthNo, pagIbigNo, password, status, image)
            VALUES 
            ('$empNo','$empName','$designation','$email','$salary','$tinNo','$sssNo','$philHealthNo','$pagIbigNo','$password','$status','$imageName')";

        if (mysqli_query($conn, $sql)) {
            header("Location: ../add-emp.php?success=1");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
