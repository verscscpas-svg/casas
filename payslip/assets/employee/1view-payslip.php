<?php
include 'process/connection.php';

$id = intval($_GET['id']);

// 🔹 Get payslip + employee info
$res = $conn->query("
    SELECT p.*, e.empNo, e.empName, e.designation, e.tinNo, e.sssNo, e.pagIbigNo, e.philHealthNo
    FROM payslips p
    JOIN employees e ON p.employee_id = e.id
    WHERE p.id = $id
");

if ($res->num_rows == 0) {
    die("Payslip not found!");
}

// $data = $res->fetch_assoc();
// $daily_rate = ($data['work_days'] > 0) ? ($data['basic_pay'] + ($data['absent'] * ($data['basic_pay'] / $data['work_days']))) / $data['work_days'] : 0;

// // absent deduction
// $absent_deduction = $data['absent'] * $daily_rate;
// // 🔹 Optional: Compute daily rate from basic pay and work days
// $daily_rate = ($data['work_days'] > 0) ? ($data['basic_pay'] / $data['work_days']) : 0;
// $fixrate = ($data['basic_pay'] * 2 * 12 / 261);

// // Basic Pay
// $basic_pay_display = $data['basic_pay'] ?? 0;

// // Regular Holiday
// $regular_holiday_days = $data['regular_holiday'] ?? 0; // number of days
// $holiday_pay = $data['holiday_pay'] ?? 0; // computed holiday pay

// // Overtime
// $overtime_hours = $data['overtime'] ?? 0;
// $overtime_pay = $data['overtime_pay'] ?? 0;

// // Rest Day Overtime
// $restday_hours = $data['restday_ot'] ?? 0;
// $restday_pay = $data['restday_pay'] ?? 0;

// // Special Overtime
// $special_hours = $data['special_ot'] ?? 0;
// $special_pay = $data['special_pay'] ?? 0;

// // Allowance, Adjustment, 13th Month
// $allowance = $data['allowance'] ?? 0;
// $adjustment = $data['adjustment'] ?? 0;
// $thirteenth = $data['thirteenth'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Payslip</title>
    <link rel="stylesheet" href="css/view-payslip.css">
</head>

<body>

</body>

</html>