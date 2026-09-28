<?php
session_start();
include 'connection.php';

// 🔹 Check if session data exists
if (!isset($_SESSION['payslip_data'])) {
    echo "No data!";
    exit();
}
$designation = strtolower(trim($_SESSION['Role'] ?? ''));

if ($designation !== 'supervisor') {
    header('Location: ../../../index.php');
    exit;
}
$data = $_SESSION['payslip_data'];
// FIX: EmpName vs empName issue
$fullName  = $_SESSION['EmpName'] ?? $_SESSION['empName'] ?? 'Employee';

// FIX: first name still works
$firstName = explode(' ', trim($fullName))[0];
// 🔹 Get employee info from DB
$emp_id = $data['employee_id'];
$res = $conn->query("SELECT * FROM employees WHERE id = $emp_id");
if ($res->num_rows == 0) {
    die("Employee not found.");
}
$emp = $res->fetch_assoc();

// 🔹 Compute daily rate (semi-monthly)
$salary = floatval($emp['salary']);
$daily_rate = round(($salary * 12) / 261, 2);

// 🔹 Preprocess all numeric fields to floats
$regular_holiday_days = floatval($data['regular_holiday'] ?? 0);
$overtime_hours       = floatval($data['overtime'] ?? 0);
$restday_hours        = floatval($data['restday_ot'] ?? 0);
$special_hours        = floatval($data['special_ot'] ?? 0);
$allowance            = floatval($data['allowance'] ?? 0);
$adjustment           = floatval($data['adjustment'] ?? 0);
$thirteenth           = floatval($data['thirteenth'] ?? 0);
$tax                  = floatval($data['tax'] ?? 0);
$sss                  = floatval($data['sss'] ?? 0);
$pagibig              = floatval($data['pagibig'] ?? 0);
$philhealth           = floatval($data['philhealth'] ?? 0);
$late                 = floatval($data['late'] ?? 0);
$absent               = floatval($data['absent'] ?? 0);
$work_days            = floatval($data['work_days'] ?? 0); // visual only
$SSSLoan            = floatval($data['sssloan'] ?? 0);
$HDMFLoan            = floatval($data['hdmfloan'] ?? 0);
// Previous Overtime
$extra_ot_hour = floatval($data['extra_ot_hour'] ?? 0);
$extra_ot_rate = floatval($data['extra_ot_rate'] ?? 0);

// Previous OT amount = Hour × Rate
$extra_ot_amount = $extra_ot_hour * $extra_ot_rate * 1.25;

// 🔹 Compute Basic Pay (full semi-monthly for display)
$basic_pay_display = $salary / 2;

// 🔹 Compute OT & holiday pays
$regular_holiday_hours = floatval($data['regular_holiday'] ?? 0);
$hourly_rate     = $daily_rate / 8;
$holiday_pay = ($daily_rate / 8)  * $regular_holiday_hours;
// $overtime_pay    = $overtime_hours * $hourly_rate * 1.25;
// Current overtime pay
$current_overtime_pay = $overtime_hours * $hourly_rate * 1.25;
$current_ot_pay = $overtime_hours + $extra_ot_hour;
// Add previous overtime
$overtime_pay = $current_overtime_pay + $extra_ot_amount;

$restday_pay     = $restday_hours * $hourly_rate * 1.3; // per your formula
$special_pay     = $special_hours * $hourly_rate;
// 🔹 Save computed amounts to session so they persist to DB
$_SESSION['payslip_data']['overtime_pay'] = $overtime_pay;
$_SESSION['payslip_data']['restday_ot_pay'] = $restday_pay;
$_SESSION['payslip_data']['special_pay'] = $special_pay;
$_SESSION['payslip_data']['holiday_pay'] = $holiday_pay;
$_SESSION['payslip_data']['extra_ot_hour'] = $extra_ot_hour;
$_SESSION['payslip_data']['extra_ot_rate'] = $extra_ot_rate;
$_SESSION['payslip_data']['extra_ot_amount'] = $extra_ot_amount;
$_SESSION['payslip_data']['current_overtime_pay'] = $current_overtime_pay;
$_SESSION['payslip_data']['overtime_pay'] = $overtime_pay; // make sure this is saved too

// 🔹 Compute absent deduction and adjust basic pay for gross/net
$absent_deduction = $absent * $daily_rate;
$basic_pay = $basic_pay_display - $absent_deduction;

// 🔹 Compute gross, total deductions, net
$gross = $basic_pay_display + $holiday_pay + $overtime_pay + $restday_pay + $special_pay + $allowance + $adjustment + $thirteenth;
$total_deductions = $tax + $sss + $pagibig + $philhealth + $late + $absent_deduction + $SSSLoan + $HDMFLoan;  // absent already applied in basic
$net = $gross - $total_deductions;

$_SESSION['payslip_data']['overtime_pay']   = $overtime_pay;
$_SESSION['payslip_data']['restday_ot_pay'] = $restday_pay;
$_SESSION['payslip_data']['special_pay']    = $special_pay;
$_SESSION['payslip_data']['holiday_pay']    = $holiday_pay;
$_SESSION['payslip_data']['absent_deduction'] = $absent_deduction;
$_SESSION['payslip_data']['gross']          = $gross;
$_SESSION['payslip_data']['total_deductions'] = $total_deductions;
$_SESSION['payslip_data']['net']            = $net;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>
    <link rel="stylesheet" href="../css/pre-pay.css">
    <link rel="shortcut icon" href="../../../img/cslogos.png" type="image/x-icon">



    <link rel="stylesheet" href="../css/preview-view-payslip.css">
</head>

<body>

    <div class="sidebar" id="sidebar">
        <div class="topbar">
            <a href="../index.php"> <img src="../../../img/cslogos.png" alt="Logo" class="logo">
            </a>
            <h2 class="compname">Casas San Luis Payroll</h2>
        </div>
        <ul class="list">
            <li onclick="location.href='../index.php'" style="cursor:pointer;">
                🏠<span> Dashboard</span>
            </li>

            <li onclick="history.back()" style="cursor:pointer;">
                ⬅️<span> BACK</span>
            </li>
        </ul>
        <button class="logout-btn" onclick="confirmLogout()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" y1="12" x2="9" y2="12" />
            </svg>
            Logout
        </button>
    </div>

    <!-- ── Logout confirmation modal ──────────────────────────────────────────── -->
    <div id="logoutOverlay" style="
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,0.45);
    align-items:center; justify-content:center;
">
        <div style="
        background:#fff; border-radius:16px;
        padding:2rem 2.25rem; max-width:360px; width:90%;
        text-align:center;
        box-shadow:0 20px 50px rgba(41,65,105,.2);
        animation:slideUp .3s cubic-bezier(.34,1.56,.64,1) both;
    ">
            <!-- Icon -->
            <div style="
            width:60px; height:60px; border-radius:50%;
            background:#fff3f3; margin:0 auto 1.25rem;
            display:flex; align-items:center; justify-content:center;
        ">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                    stroke="#e05252" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
            </div>

            <h3 style="font-size:18px;font-weight:700;color:#1a2a44;margin:0 0 .5rem;
                   font-family:'Playfair Display',Georgia,serif;">Sign out?</h3>
            <p style="font-size:14px;color:#6b84aa;margin:0 0 1.75rem;line-height:1.6;">
                You'll be returned to the login page. Any unsaved changes will be lost.
            </p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <button onclick="cancelLogout()" style="
                padding:10px; font-size:14px; font-weight:600;
                background:#f5f7fb; color:#1a2a44;
                border:1px solid #dde3f0; border-radius:10px;
                cursor:pointer; transition:background .2s;
            " onmouseover="this.style.background='#e8ecf5'"
                    onmouseout="this.style.background='#f5f7fb'">
                    Cancel
                </button>
                <button onclick="doLogout()" style="
                padding:10px; font-size:14px; font-weight:600;
                background:#e05252; color:#fff;
                border:none; border-radius:10px;
                cursor:pointer; transition:opacity .2s;
            " onmouseover="this.style.opacity='.85'"
                    onmouseout="this.style.opacity='1'">
                    Yes, logout
                </button>
            </div>
        </div>
    </div>

    <script>
        // ── Sidebar toggle ────────────────────────────────────────────────────────────
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        // ── Logout confirmation ───────────────────────────────────────────────────────
        function confirmLogout() {
            const overlay = document.getElementById('logoutOverlay');
            overlay.style.display = 'flex'; // ✅ directly set display
        }

        function cancelLogout() {
            const overlay = document.getElementById('logoutOverlay');
            overlay.style.display = 'none'; // ✅ hide it back
        }

        function doLogout() {
            window.location.href = 'process/logout.php'; // adjust path if needed
        }

        // Close overlay on backdrop click
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('logoutOverlay');
            if (overlay) {
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) cancelLogout();
                });
            }
        });
    </script>


    <div class="main">
        <div class="navbar">
            <button onclick="toggleSidebar()">☰</button>
            <h3 class="nav">Employee </h3>
            <div class="user" style="text-transform: capitalize;">Welcome, <?= htmlspecialchars($fullName) ?></div>

        </div>
        <div class="content">
            <div class="payslip-container">

                <div class="COMPANY">
                    <div class="IMGLOGO">
                        <img src="../../../img/cs.png" alt="logo">
                    </div>
                    <h1 class="COMP">CASAS SAN LUIS & CO.</h1>
                </div>

                <!-- HEADER -->
                <div class="info-grid">
                    <div>
                        <div class="info-group"><span class="label">EMP NO:</span> <span><?= htmlspecialchars($emp['empNo']) ?></span></div>
                        <div class="info-group"><span class="label">EMP NAME:</span> <span><?= htmlspecialchars($emp['empName']) ?></span></div>
                        <div class="info-group"><span class="label">CUT-OFF:</span> <span><?= htmlspecialchars($data['cut_off_start']) ?> - <?= htmlspecialchars($data['cut_off_end']) ?></span></div>
                        <div class="info-group"><span class="label">PAYROLL DATE:</span> <span><?= htmlspecialchars($data['payroll_date']) ?></span></div>
                    </div>
                    <div>
                        <div class="info-group"><span class="label">Work Days:</span> <span><?= $work_days ?></span></div>
                        <div class="info-group"><span class="label">Designation:</span> <span><?= htmlspecialchars($emp['designation']) ?></span></div>
                    </div>
                </div>

                <!-- DAILY RATE & IDs -->
                <div class="info-grid custom-info" style="margin-top:20px;">
                    <div class="left-info">
                        <div class="info-group">
                            <span class="label">Daily Rate:</span>
                            <span><?= number_format($daily_rate, 2) ?></span>
                        </div>
                        <div class="info-group"><span class="label">TIN No.:</span> <span class="tin"><?= htmlspecialchars($emp['tinNo']) ?></span></div>
                    </div>
                    <div class="right-info">

                        <div class="info-group"><span class="label">SSS No.:</span> <span class="sss"><?= htmlspecialchars($emp['sssNo']) ?></span></div>
                        <div class="info-group"><span class="label">Pag-ibig No.:</span> <span class="pagibig"><?= htmlspecialchars($emp['pagIbigNo']) ?></span></div>
                        <div class="info-group"><span class="label">Philhealth No.:</span> <span class="philhealth"><?= htmlspecialchars($emp['philHealthNo']) ?></span></div>
                    </div>
                </div>

                <!-- EARNINGS -->
                <div class="calculation-section">
                    <table class="column-table">
                        <thead>
                            <tr>
                                <th>EARNINGS</th>
                                <th class="text-right">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Pay</td>
                                <td class="text-right"><?= number_format($basic_pay_display, 2) ?></td>
                            </tr>
                            <tr>
                                <td>Regular Holiday(<?= $regular_holiday_days ?>Hr/s)</td>
                                <td class="text-right"><?= number_format($holiday_pay, 2) ?></td>
                            </tr>
                            <tr>
                                <td>Overtime(<?= $current_ot_pay ?>Hr/s)</td>
                                <td class="text-right"><?= number_format($overtime_pay, 2) ?></td>
                            </tr>
                            <tr>
                                <td>Rest Day Overtime(<?= $restday_hours ?>Hr/s)</td>
                                <td class="text-right"><?= number_format($restday_pay, 2) ?></td>
                            </tr>

                            <tr>
                                <td>Special Overtime(<?= $special_hours ?>Hr/s)</td>
                                <td class="text-right"><?= number_format($special_pay, 2) ?></td>
                            </tr>
                            <tr>
                                <td>Allowance</td>
                                <td class="text-right"><?= number_format($allowance, 2) ?></td>
                            </tr>
                            <tr>
                                <td>Adjustment</td>
                                <td class="text-right"><?= number_format($adjustment, 2) ?></td>
                            </tr>
                            <tr>
                                <td>13th Month</td>
                                <td class="text-right"><?= number_format($thirteenth, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- DEDUCTIONS -->
                    <table class="column-table">
                        <thead>
                            <tr>
                                <th>DEDUCTIONS</th>
                                <th class="text-right">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="up">Tax</td>
                                <td class="text-right"><?= number_format($tax, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="up">SSS</td>
                                <td class="text-right"><?= number_format($sss, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="up">SSS Loan</td>
                                <td class="text-right"><?= number_format($SSSLoan, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="up">Pag-ibig</td>
                                <td class="text-right"><?= number_format($pagibig, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="up">Pag-ibig Loan</td>
                                <td class="text-right"><?= number_format($HDMFLoan, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="up">Philhealth</td>
                                <td class="text-right"><?= number_format($philhealth, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="up">Employee Loan</td>
                                <td class="text-right"><?= number_format($late, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="up">Absent(<?= $absent ?>)</td>
                                <td class="text-right"><?= number_format($absent_deduction, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="up"> Total Deductions</td>
                                <td class="text-right"> <?= $total_deductions ?> </td>
                            </tr>
                            <tr>
                                <td class="up">&nbsp;&nbsp;&nbsp;</td>
                                <td class="text-right"> &nbsp;</td>
                            </tr>


                        </tbody>
                    </table>
                </div>

                <!-- TOTALS -->
                <div class="totals-section">
                    <div class="total-row"><span>GROSS PAY:</span> <span><?= number_format($gross, 2) ?></span></div>
                    <div class="total-row under"><span>DEDUCTIONS:</span> <span><?= number_format($total_deductions, 2) ?></span></div>
                    <div class="total-row" style="font-weight:bold;"><span>NET PAY:</span> <span><?= number_format($net, 2) ?></span></div>
                </div>

                <center>
                    <div class="buttons">
                        <form action="save_payslip.php" method="POST">
                            <button type="submit" class="save">💾 Save Payslip</button>
                        </form>
                        <br>
                        <!-- <a href="../emp-payslip.php?id=<?= $emp_id ?>"><button class="edit">✏️ Edit</button></a> -->
                        <button onclick="history.back()" class="edit">✏️ Edit</button>
                    </div>

                </center>

            </div>
            <script src=" ../js/jquery.js"></script>
            <script src="../js/previ-view-payslip.js"></script>
        </div>

        <script src="../js/jquery.js"></script>
        <script src="../js/payslip.js"></script>





</body>

</html>