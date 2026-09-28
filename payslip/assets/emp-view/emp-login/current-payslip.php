<?php

session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: ../index.php');
    exit;
}
$designation = strtolower(trim($_SESSION['Role'] ?? ''));

if ($designation == 'superadmin') {
    header('Location: ../../index.php');
    exit;
}
// FIX: EmpName vs empName issue
$fullName  = $_SESSION['EmpName'] ?? $_SESSION['empName'] ?? 'Employee';

// FIX: first name still works
$firstName = explode(' ', trim($fullName))[0];

require_once "../foremp/connection.php";




if (!isset($_GET['id'])) {
    echo "No payslip ID provided.";
    exit();
}

$id = intval($_GET['id']);

$res = $conn->query("
    SELECT p.*, e.empNo, e.empName, e.designation, e.tinNo, e.sssNo, e.pagIbigNo, e.philHealthNo, e.email
    FROM payslips p
    JOIN employees e ON p.employee_id = e.id
    WHERE p.id = $id
");

if ($res->num_rows == 0) {
    die("Payslip not found!");
}

$data = $res->fetch_assoc();

$daily_rate     = ($data['work_days'] > 0) ? ($data['basic_pay'] / $data['work_days']) : 0;
$fixrate        = ($data['basic_pay'] * 2 * 12 / 261);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
    <!-- Reuse existing CSS mo -->
    <!-- <link rel="stylesheet" href="css/pre-pay.css"> -->
    <!-- <link rel="stylesheet" href="css/view-payslip.css"> -->
    <!-- <link rel="stylesheet" href="css/preview-view-payslip.css"> -->
    <!-- <link rel="stylesheet" href="css/currentpayslip.css"> -->
    <!-- <link rel="stylesheet" href="css/home.css"> -->
    <link rel="shortcut icon" href="css/cslogos.png" type="image/x-icon">
    <!-- <link rel="stylesheet" href="css/current.css"> -->
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #303c5a;
            color: #1a2a44;
            font-size: 15px;
            line-height: 1.6
        }

        a {
            text-decoration: none;
            color: inherit
        }

        /* ─── Sidebar ─────────────────────────────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 240px;
            background: #1a2a44;
            color: #fff;
            display: flex;
            flex-direction: column;
            z-index: 200;
            transition: transform .28s cubic-bezier(.4, 0, .2, 1)
        }

        .topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 16px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, .1)
        }

        .logo {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0
        }

        .compname {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.35;
            color: #e8edf6
        }

        .list {
            list-style: none;
            flex: 1;
            padding: 12px 8px
        }

        .li {
            border-radius: 8px;
            overflow: hidden
        }

        .li li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 8px;
            transition: background .18s;
            color: rgba(255, 255, 255, .8)
        }

        .li li:hover {
            background: rgba(255, 255, 255, .09);
            color: #fff
        }

        .li li a {
            color: inherit;
            font-size: 14px
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 12px;
            padding: 10px 14px;
            background: rgba(224, 82, 82, .15);
            color: #e88;
            border: 1px solid rgba(224, 82, 82, .25);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background .18s
        }

        .logout-btn:hover {
            background: rgba(224, 82, 82, .28)
        }

        /* ─── Sidebar overlay ─────────────────────────────────────────────────── */
        #sidebarOverlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 199;
            background: rgba(0, 0, 0, .4)
        }

        #sidebarOverlay.active {
            display: block
        }

        /* ─── Main ────────────────────────────────────────────────────────────── */
        .main {
            margin-left: 240px;
            min-height: 100vh;
            transition: margin-left .28s
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 24px;
            height: 58px;
            background: #fff;
            border-bottom: 0.5px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .06)
        }

        .navbar button {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: #1a2a44;
            padding: 4px;
            display: flex;
            align-items: center
        }

        .nav {
            font-size: 16px;
            font-weight: 600;
            color: #1a2a44;
            flex: 1
        }

        .user {
            font-size: 13px;
            color: #6b84aa;
            white-space: nowrap
        }

        /* ─── Content ─────────────────────────────────────────────────────────── */
        .content {
            padding: 24px;
            max-width: 860px;
            margin: 0 auto
        }

        /* ─── Payslip container ───────────────────────────────────────────────── */
        .payslip-container {
            background: #fff;
            border: 0.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 20px
        }

        /* ─── Company header ──────────────────────────────────────────────────── */
        .COMPANY {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding-bottom: 18px;
            border-bottom: 2px solid #1a2a44;
            margin-bottom: 20px
        }

        .IMGLOGO img {
            width: 58px;
            height: 58px;
            object-fit: contain
        }

        .COMP {
            font-size: 20px;
            font-weight: 700;
            color: #1a2a44;
            letter-spacing: .5px;
            text-align: center
        }

        /* ─── Info grid ───────────────────────────────────────────────────────── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 32px
        }

        .info-group {
            display: flex;
            gap: 8px;
            font-size: 13px;
            padding: 3px 0;
            border-bottom: 0.5px solid #f0f3f9
        }

        .info-group .label {
            font-weight: 600;
            color: #4a6585;
            min-width: 110px;
            flex-shrink: 0
        }

        .info-group span:last-child {
            color: #1a2a44
        }

        /* ─── Custom info (IDs) ───────────────────────────────────────────────── */
        .custom-info {
            background: #f7f9fd;
            border-radius: 10px;
            padding: 14px 16px;
            border: 0.5px solid #e2e8f0
        }

        .left-info,
        .right-info {
            display: flex;
            flex-direction: column;
            gap: 4px
        }

        /* ─── Earnings / Deductions tables ───────────────────────────────────── */
        .calculation-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            margin-top: 24px;
            border: 0.5px solid #d0d9ea;
            border-radius: 10px;
            overflow: hidden
        }

        .column-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px
        }

        .column-table:first-child {
            border-right: 0.5px solid #d0d9ea
        }

        .column-table thead tr {
            background: #1a2a44;
            color: #fff
        }

        .column-table thead th {
            padding: 10px 12px;
            font-weight: 500;
            font-size: 12px;
            letter-spacing: .4px;
            text-align: left
        }

        .column-table thead th.text-right {
            text-align: right
        }

        .column-table tbody tr {
            border-bottom: 0.5px solid #edf0f7;
            transition: background .15s
        }

        .column-table tbody tr:hover {
            background: #f7f9fd
        }

        .column-table tbody td {
            padding: 8px 12px;
            color: #2d3f5a;
            font-size: 13px
        }

        .column-table tbody td.text-right {
            text-align: right;
            font-variant-numeric: tabular-nums
        }

        .column-table tbody tr:last-child {
            border-bottom: none
        }

        .aside {
            color: #4a6585
        }

        .liitan {
            font-size: 12px
        }

        /* ─── Totals section ──────────────────────────────────────────────────── */
        .totals-section {
            margin-top: 16px;
            border: 0.5px solid #d0d9ea;
            border-radius: 10px;
            overflow: hidden
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border-bottom: 0.5px solid #e8edf6;
            color: #1a2a44
        }

        .total-row:last-child {
            border-bottom: none
        }

        .total-row.under {
            color: #6b84aa;
            font-size: 13px
        }

        .total-row.net {
            background: #1a2a44;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            border-radius: 0 0 9px 9px
        }

        /* ─── Print button ────────────────────────────────────────────────────── */
        .btn-function {
            display: flex;
            justify-content: center;
            margin-top: 8px
        }

        .print {
            padding: 11px 40px;
            background: #1a2a44;
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: .4px;
            transition: opacity .18s
        }

        .print:hover {
            opacity: .85
        }

        /* ─── Logout animation ────────────────────────────────────────────────── */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* ─── Print media ─────────────────────────────────────────────────────── */
        @media print {

            .sidebar,
            .navbar,
            .btn-function,
            #sidebarOverlay {
                display: none !important
            }

            .main {
                margin-left: 0 !important
            }

            .content {
                padding: 0 !important;
                max-width: 100% !important
            }

            .payslip-container {
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important
            }

            .column-table thead tr {
                background: #1a2a44 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact
            }

            .total-row.net {
                background: #1a2a44 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact
            }

            body {
                background: #fff !important
            }
        }

        /* ─── Responsive ──────────────────────────────────────────────────────── */
        @media(max-width:768px) {
            .sidebar {
                transform: translateX(-100%)
            }

            .sidebar.active {
                transform: translateX(0)
            }

            .main {
                margin-left: 0
            }

            .content {
                padding: 14px
            }

            .payslip-container {
                padding: 18px 14px
            }

            .COMP {
                font-size: 15px
            }

            .IMGLOGO img {
                width: 44px;
                height: 44px
            }

            .info-grid {
                grid-template-columns: 1fr
            }

            .info-group .label {
                min-width: 100px
            }

            .calculation-section {
                grid-template-columns: 1fr
            }

            .column-table:first-child {
                border-right: none;
                border-bottom: 0.5px solid #d0d9ea
            }
        }

        @media(max-width:480px) {
            .navbar {
                padding: 0 12px
            }

            .user {
                font-size: 12px
            }

            .COMPANY {
                flex-direction: column;
                gap: 8px
            }

            .COMP {
                font-size: 14px
            }

            .info-group {
                font-size: 12px;
                flex-direction: column;
                gap: 1px
            }

            .info-group .label {
                min-width: unset
            }

            .total-row {
                font-size: 13px;
                padding: 9px 14px
            }

            .total-row.net {
                font-size: 14px
            }

            .column-table tbody td,
            .column-table thead th {
                padding: 7px 8px;
                font-size: 12px
            }

            .print {
                width: 100%;
                padding: 12px
            }
        }
    </style>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <div id="sidebarOverlay" onclick="closeSidebar()"></div>
        <div class="topbar">
            <a href="index.php"><img src="css/cslogos.png" alt="Logo" class="logo"></a>
            <h2 class="compname">Casas San Luis Payroll</h2>
        </div>
        <ul class="list">
            <div class="li">
                <li onclick="window.location='index.php?id=<?php echo (int)$data['employee_id']; ?>'">
                    🏠<span>
                        <a href="#">Home</a>
                    </span>
                </li>
            </div>
            <div class="li">
                <li onclick="window.location='process/settings.php?id=<?php echo (int)$data['employee_id']; ?>'">
                    ⚙️<span>
                        <a href="process/settings.php?id=<?php echo (int)$data['employee_id']; ?>">Settings</a>
                    </span>
                </li>
            </div>
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



    <div class="main">
        <div class="navbar">
            <button onclick="toggleSidebar()">☰</button>
            <h3 class="nav">Payslip</h3>
            <div class="user">Welcome <?= htmlspecialchars($fullName) ?></div>
        </div>

        <div class="content">
            <div class="payslip-container" id="payslip-container">

                <!-- COMPANY HEADER -->
                <div class="COMPANY">
                    <div class="IMGLOGO"><img src="css/cs.png" alt=" "></div>
                    <h1 class="COMP">CASAS SAN LUIS & CO.</h1>
                </div>

                <!-- EMPLOYEE INFO -->
                <div class="info-grid">
                    <div>
                        <div class="info-group"><span class="label">EMP NO:</span> <span><?= htmlspecialchars($data['empNo']) ?></span></div>
                        <div class="info-group"><span class="label">EMP NAME:</span> <span><?= htmlspecialchars($data['empName']) ?></span></div>
                        <div class="info-group"><span class="label">CUT-OFF:</span> <span><?= htmlspecialchars($data['cut_off_start']) ?> - <?= htmlspecialchars($data['cut_off_end']) ?></span></div>
                        <div class="info-group"><span class="label">PAYROLL DATE:</span> <span><?= htmlspecialchars($data['payroll_date']) ?></span></div>
                    </div>
                    <div>
                        <div class="info-group"><span class="label">Work Days:</span> <span><?= htmlspecialchars($data['work_days']) ?></span></div>
                        <div class="info-group"><span class="label">Designation:</span> <span><?= htmlspecialchars($data['designation']) ?></span></div>
                    </div>
                </div>

                <!-- IDs & Daily Rate -->
                <div class="info-grid custom-info" style="margin-top:20px;">
                    <div class="left-info">
                        <div class="info-group">
                            <span class="label">Daily Rate:</span>
                            <span><?= number_format($fixrate, 2) ?></span>
                        </div>
                        <div class="info-group">
                            <span class="label">TIN No.:</span>
                            <span class="tin"><?= htmlspecialchars($data['tinNo']) ?></span>
                        </div>
                    </div>
                    <div class="right-info">
                        <div class="info-group">
                            <span class="label">SSS No.:</span>
                            <span class="sss"><?= htmlspecialchars($data['sssNo']) ?></span>
                        </div>
                        <div class="info-group">
                            <span class="label">Pag-ibig No.:</span>
                            <span class="pagibig"><?= htmlspecialchars($data['pagIbigNo']) ?></span>
                        </div>
                        <div class="info-group">
                            <span class="label">Philhealth No.:</span>
                            <span class="philhealth"><?= htmlspecialchars($data['philHealthNo']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- EARNINGS & DEDUCTIONS -->
                <div class="calculation-section">
                    <table class="column-table">
                        <thead>
                            <tr>
                                <th>EARNINGS</th>
                                <th class="text-right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Pay</td>
                                <td class="text-right"><?= number_format($data['basic_pay'], 2) ?></td>
                            </tr>
                            <tr>
                                <td class="reghol">Regular Holiday (<?= number_format($data['reg_holiday']) ?> Hr) </td>
                                <td class="text-right"><?= number_format($data['regular_holiday'], 2) ?></td>
                            </tr>
                            <tr>
                                <td>Overtime (<?= number_format($data['ot']) ?> Hr)</td>
                                <td class="text-right"><?= number_format($data['overtime_pay'], 2) ?></td>
                            </tr>
                            <tr>
                                <td>Rest Day Overtime (<?= number_format($data['rd_ot']) ?> Hr)</td>
                                <td class="text-right"><?= number_format($data['restday_ot'], 2) ?></td>
                            </tr>
                            <tr>
                                <td>Special Overtime (<?= number_format($data['spec_ot']) ?> Hr)</td>
                                <td class="text-right"><?= number_format($data['special_ot'], 2) ?></td>
                            </tr>
                            <tr>
                                <td>Allowance</td>
                                <td class="text-right"><?= number_format($data['allowance'], 2) ?></td>
                            </tr>
                            <tr>
                                <td>Adjustment</td>
                                <td class="text-right"><?= number_format($data['adjustment'], 2) ?></td>
                            </tr>
                            <tr>
                                <td>13th Month</td>
                                <td class="text-right"><?= number_format($data['thirteenth_month'], 2) ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="column-table">
                        <thead>
                            <tr>
                                <th>&nbsp;&nbsp;&nbsp;&nbsp;DEDUCTIONS</th>
                                <th class="text-right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="aside">&nbsp;Tax</td>
                                <td class="text-right"><?= number_format($data['tax'], 2) ?></td>
                            </tr>
                            <tr>
                                <td class="aside">&nbsp;SSS</td>
                                <td class="text-right"><?= number_format($data['sss'], 2) ?></td>
                            </tr>
                             <tr>
                                <td class="aside">&nbsp;SSS Loan</td>
                                <td class="text-right"><?= number_format($data['sssloan'], 2) ?></td>
                            </tr>
                            <tr>
                                <td class="aside">&nbsp;Pag-ibig</td>
                                <td class="text-right"><?= number_format($data['pagibig'], 2) ?></td>
                            </tr>
                              <tr>
                                <td class="aside">&nbsp;Pag-ibig Loan</td>
                                <td class="text-right"><?= number_format($data['hdmfloan'], 2) ?></td>
                            </tr>
                            <tr>
                                <td class="aside">&nbsp;Philhealth</td>
                                <td class="text-right"><?= number_format($data['philhealth'], 2) ?></td>
                            </tr>
                            <tr>
                                <td class="aside">&nbsp;Employee Loan</td>
                                <td class="text-right"><?= number_format($data['late'], 2) ?></td>
                            </tr>
                            <tr>
                                <td class="liitan aside">&nbsp;Absent (<?= intval($data['absent']) ?> days)</td>
                                <td class="text-right"><?= number_format($data['absent_deduction'], 2) ?></td>
                            </tr>
                            <tr>
                                <td class="aside"><b>&nbsp;Total</b></td>
                                <td class="text-right"><?= number_format($data['total_deductions'], 2) ?></td>
                            </tr>
                            <tr>
                                <td class="up">&nbsp;&nbsp;</td>
                                <td class="text-right">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- TOTALS -->
                <div class="totals-section">
                    <div class="total-row">
                        <span>GROSS PAY:</span>
                        <span><?= number_format($data['gross_pay'], 2) ?></span>
                    </div>
                    <div class="total-row under">
                        <span>DEDUCTIONS:</span>
                        <span><?= number_format($data['total_deductions'], 2) ?></span>
                    </div>
                    <div class="total-row net">
                        <span>NET PAY:</span>
                        <span><?= number_format($data['net_pay'], 2) ?></span>
                    </div>
                </div>

            </div>

            <!-- ACTION BUTTONS -->
            <div class="btn-function">
                <button class="print" id="print">PRINT</button><br>
            </div>
        </div>
    </div>

    <script src="js/jquery.js"></script>
    <script src="js/previ-view-payslip.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- <script src="../../employee/js/print.js"></script> -->
    <script src="js/print.js"></script>
    <script>
        // Sidebar Responsive Toggles
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.remove('active');
        }

        // Sidebar Active Link Tracker base sa kasalukuyang URL o State
        document.addEventListener("DOMContentLoaded", function() {
            const currentUrl = window.location.href;
            const sidebarItems = document.querySelectorAll('.sidebar .list li');

            sidebarItems.forEach(item => {
                // Nagbibigay ng active style sa back button o regular links kung tugma
                if (item.getAttribute('onclick') && item.getAttribute('onclick').includes('history.back')) {
                    // Pwede itong lagyan ng conditional active class kung galing sa sub-page
                }
            });

            // Real-time Preview para sa Profile Image Upload
            const imgUpload = document.getElementById('imageUpload');
            if (imgUpload) {
                imgUpload.addEventListener('change', function() {
                    readURL(this, 'previewImg');
                });
            }

            // Real-time Preview para sa Bank QR Code Upload
            const qrUpload = document.getElementById('bankqrUpload');
            if (qrUpload) {
                qrUpload.addEventListener('change', function() {
                    readURL(this, 'previewBankQr');
                });
            }
        });

        // Helper Function para sa File Preview
        function readURL(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).setAttribute('src', e.target.result);
                }
                reader.読み込む(reader.readAsDataURL(input.files[0]));
            }
        }

        // Logout Modal System
        function confirmLogout() {
            document.getElementById('logoutOverlay').style.display = 'flex';
        }

        function cancelLogout() {
            document.getElementById('logoutOverlay').style.display = 'none';
        }

        function doLogout() {
            window.location.href = 'process/logout.php'; // I-adjust ang logout link kung kinakailangan
        }
    </script>

</body>

</html>