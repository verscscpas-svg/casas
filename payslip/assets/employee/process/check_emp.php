<?php
require_once "connection.php";

if (isset($_GET['empNo'])) {
    $empNo = $_GET['empNo'];

    $sql = "SELECT * FROM employees WHERE empNo = '$empNo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "exists";
    } else {
        echo "ok";
    }
}
