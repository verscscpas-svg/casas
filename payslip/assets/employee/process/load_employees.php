<?php
require_once "connection.php";

$includeResigned = isset($_GET['resigned']) && $_GET['resigned'] == 1;

$sql = "SELECT * FROM employees";
if (!$includeResigned) {
    $sql .= " WHERE status = 'employed'";
}
$sql .= " ORDER BY empNo ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>";
        if (!empty($row['image'])) {
            echo "<img src='process/uploads/{$row['image']}' width='50' height='50' style='border-radius:50%;'>";
        } else {
            echo "No Image";
        }
        echo "</td>";
        echo "<td>{$row['empNo']}</td>";
        echo "<td>{$row['empName']}</td>";
        echo "<td>{$row['designation']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td><button class='view-btn' onclick='viewEmp({$row['id']})'>View</button></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No employees found</td></tr>";
}
