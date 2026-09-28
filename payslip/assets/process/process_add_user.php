<?php
include 'connection.php'; // Make sure this path is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Collect POST data
    $emp_no = $_POST['emp_no'];
    $emp_name = $_POST['emp_name'];
    $designation = $_POST['designation'];
    $cutoff_start = $_POST['cutoff_start'];
    $cutoff_end = $_POST['cutoff_end'];
    $payroll_date = $_POST['payroll_date'];
    $work_days = $_POST['work_days'];
    $daily_rate = $_POST['daily_rate'];
    $tin_no = $_POST['tin_no'];
    $sss_no = $_POST['sss_no'];
    $pagibig_no = $_POST['pagibig_no'];
    $philhealth_no = $_POST['philhealth_no'];

    $regular_days = $_POST['regular_days'];
    $regular_pay = $_POST['regular_pay'];
    $regular_holiday = $_POST['regular_holiday'];
    $overtime = $_POST['overtime'];
    $overtime_restday = $_POST['overtime_restday'];
    $overtime_special = $_POST['overtime_special'];
    $transport_allowance = $_POST['transport_allowance'];
    $adjustment = $_POST['adjustment'];
    $thirteenth_month = $_POST['thirteenth_month'];

    $withholding_tax = $_POST['withholding_tax'];
    $sss_contribution = $_POST['sss_contribution'];
    $pagibig_contribution = $_POST['pagibig_contribution'];
    $philhealth_contribution = $_POST['philhealth_contribution'];
    $late_amount = $_POST['late_amount'];
    $absent_amount = $_POST['absent_amount'];
    $total_deductions = $_POST['total_deductions'];

    $gross_pay = $_POST['gross_pay'];
    $net_pay = $_POST['net_pay'];

    // Insert query
    $query = "INSERT INTO emp (
        emp_no, emp_name, designation, cutoff_start, cutoff_end, payroll_date, work_days, daily_rate,
        tin_no, sss_no, pagibig_no, philhealth_no,
        regular_days, regular_pay, regular_holiday, overtime, overtime_restday, overtime_special,
        transport_allowance, adjustment, thirteenth_month,
        withholding_tax, sss_contribution, pagibig_contribution, philhealth_contribution, late_amount, absent_amount, total_deductions,
        gross_pay, net_pay
    ) VALUES (
        '$emp_no','$emp_name','$designation','$cutoff_start','$cutoff_end','$payroll_date','$work_days','$daily_rate',
        '$tin_no','$sss_no','$pagibig_no','$philhealth_no',
        '$regular_days','$regular_pay','$regular_holiday','$overtime','$overtime_restday','$overtime_special',
        '$transport_allowance','$adjustment','$thirteenth_month',
        '$withholding_tax','$sss_contribution','$pagibig_contribution','$philhealth_contribution','$late_amount','$absent_amount','$total_deductions',
        '$gross_pay','$net_pay'
    )";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<p style='color:green;text-align:center;'>Payslip added successfully! <a href='../new.php?page=users'>Add another</a></p>";
    } else {
        echo "<p style='color:red;text-align:center;'>Error: " . mysqli_error($conn) . "</p>";
    }
}
