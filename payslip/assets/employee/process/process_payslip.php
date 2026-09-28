<?php
session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $employee_id = intval($_POST['employee_id'] ?? 0);
    $work_days   = intval($_POST['work_days'] ?? 0);

    $cut_off_start = $_POST['cut_off_start'] ?? '';
    $cut_off_end   = $_POST['cut_off_end'] ?? '';
    $payroll_date  = $_POST['payroll_date'] ?? '';

    // =========================
    // VALIDATION
    // =========================
    if (empty($cut_off_start) || empty($cut_off_end)) {
        echo "<script>
            alert('Cut-off start and end date are required!');
            window.history.back();
        </script>";
        exit();
    }

    // =========================
    // AUTO-FILL PAYROLL DATE
    // =========================
    if (empty($payroll_date)) {
        $payroll_date = $cut_off_end;
    }

    // =========================
    // DUPLICATE CHECK
    // =========================
    $check = $conn->prepare("
        SELECT id
        FROM payslips
        WHERE employee_id = ?
        AND cut_off_start = ?
        AND cut_off_end = ?
        LIMIT 1
    ");

    $check->bind_param(
        "iss",
        $employee_id,
        $cut_off_start,
        $cut_off_end
    );

    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>
            alert('This employee already has a payslip for this cut-off period!');
            window.history.back();
        </script>";
        exit();
    }

    $check->close();

    // =========================
    // EARNINGS
    // =========================
    $regular_holiday = floatval($_POST['regular_holiday'] ?? 0);

    // CURRENT OVERTIME HOURS
    $overtime = floatval($_POST['overtime'] ?? 0);

    $restday_ot = floatval($_POST['restday_ot'] ?? 0);
    $special_ot = floatval($_POST['special_ot'] ?? 0);
    $allowance  = floatval($_POST['allowance'] ?? 0);
    $adjustment = floatval($_POST['adjustment'] ?? 0);
    $thirteenth = floatval($_POST['thirteenth'] ?? 0);

    // =========================
    // HOURS FOR RECORD
    // =========================
    $reg_holiday_hrs = $regular_holiday;
    $ot_hrs          = $overtime;
    $rd_ot_hrs       = $restday_ot;
    $spec_ot_hrs     = $special_ot;

    // =========================
    // PREVIOUS OVERTIME
    // =========================
    $extra_ot_hour = floatval($_POST['extra_ot_hour'] ?? 0);
    $extra_ot_rate = floatval($_POST['extra_ot_rate'] ?? 0);

    // Previous OT amount
    $extra_ot_amount = $extra_ot_hour * $extra_ot_rate;

    // Current OT hours + Previous OT hours
    $total_overtime = $ot_hrs + $extra_ot_hour;

    // =========================
    // DEDUCTIONS
    // =========================
    $tax        = floatval($_POST['tax'] ?? 0);
    $sss        = floatval($_POST['sss'] ?? 0);
    $pagibig    = floatval($_POST['pagibig'] ?? 0);
    $philhealth = floatval($_POST['philhealth'] ?? 0);
    $late       = floatval($_POST['late'] ?? 0);
    $absent     = floatval($_POST['absent'] ?? 0);
    $SSSLoan    = floatval($_POST['sssloan'] ?? 0);
    $HDMFLoan   = floatval($_POST['hdmfloan'] ?? 0);

    // =========================
    // GET EMPLOYEE DATA
    // =========================
    $stmt = $conn->prepare("
        SELECT salary
        FROM employees
        WHERE id = ?
    ");

    $stmt->bind_param("i", $employee_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $emp = $result->fetch_assoc();

    if (!$emp) {
        echo "<script>
            alert('Employee not found!');
            window.history.back();
        </script>";
        exit();
    }

    $stmt->close();

    // =========================
    // BASIC COMPUTATION
    // =========================
    $salary = floatval($emp['salary']);

    $daily_rate = round(
        ($salary * 12) / 261,
        2
    );

    $basic_pay = $salary / 2;

    $holiday_pay =
        $daily_rate *
        2 *
        $regular_holiday;

    /*
     * NOTE:
     * Final OT pay computation will be done
     * in preview-payslip.php.
     *
     * Current OT uses current salary rate.
     * Previous OT uses extra_ot_rate.
     */

    // Temporary gross
    // Final gross will be recalculated in preview
    $gross =
        $basic_pay +
        $holiday_pay +
        $allowance +
        $adjustment +
        $thirteenth;

    $total_deductions =
        $tax +
        $sss +
        $pagibig +
        $philhealth +
        $late +
        $absent +
        $SSSLoan +
        $HDMFLoan;

    $net = $gross - $total_deductions;

    // =========================
    // SAVE TO SESSION
    // =========================
    $_SESSION['payslip_data'] = [

        // Employee
        'employee_id' => $employee_id,

        // Payroll
        'cut_off_start' => $cut_off_start,
        'cut_off_end'   => $cut_off_end,
        'payroll_date'  => $payroll_date,
        'work_days'     => $work_days,

        // Basic Pay
        'basic_pay' => $basic_pay,

        // Regular Holiday
        'regular_holiday' => $regular_holiday,
        'holiday_pay'     => $holiday_pay,
        'reg_holiday'     => $reg_holiday_hrs,

        // =====================
        // CURRENT OVERTIME
        // =====================
        'overtime' => $overtime,

        // =====================
        // TOTAL OT HOURS
        // Current + Previous
        // Ito ang mase-save sa `ot`
        // =====================
        'ot' => $total_overtime,

        // =====================
        // PREVIOUS OVERTIME
        // =====================
        'extra_ot_hour'   => $extra_ot_hour,
        'extra_ot_rate'   => $extra_ot_rate,
        'extra_ot_amount' => $extra_ot_amount,

        // Optional total OT field
        'total_ot' => $total_overtime,

        // Other OT
        'restday_ot' => $restday_ot,
        'special_ot' => $special_ot,
        'rd_ot'      => $rd_ot_hrs,
        'spec_ot'    => $spec_ot_hrs,

        // Other Earnings
        'allowance'  => $allowance,
        'adjustment' => $adjustment,
        'thirteenth' => $thirteenth,

        // Deductions
        'tax'        => $tax,
        'sss'        => $sss,
        'pagibig'    => $pagibig,
        'philhealth' => $philhealth,
        'late'       => $late,
        'absent'     => $absent,
        'sssloan'    => $SSSLoan,
        'hdmfloan'   => $HDMFLoan,

        // Totals
        'total_deductions' => $total_deductions,
        'gross'            => $gross,
        'net'              => $net
    ];

    // =========================
    // REDIRECT TO PREVIEW
    // =========================
    header("Location: preview-payslip.php");
    exit();
}
