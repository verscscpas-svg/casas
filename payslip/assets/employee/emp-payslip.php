<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: ../../index.php');
    exit;
}

// ✅ ROLE CHECK — kung hindi supervisor, i-redirect
$designation = strtolower(trim($_SESSION['Role'] ?? ''));

if ($designation !== 'supervisor') {
    header('Location: ../../index.php');
    exit;
}

// FIX: EmpName vs empName issue
$fullName  = $_SESSION['EmpName'] ?? $_SESSION['empName'] ?? 'Employee';

// FIX: first name still works
$firstName = explode(' ', trim($fullName))[0];


include 'process/connection.php';

// 🔐 secure GET
$emp_id = intval($_GET['id']);

// kunin employee
$result = $conn->query("SELECT * FROM employees WHERE id = $emp_id");

if ($result->num_rows == 0) {
    die("Employee not found!");
}

$emp = $result->fetch_assoc();

// ==========================
// DAILY RATE
// ==========================
$salary = $emp['salary'];
$daily_rate = round(($salary * 12) / 261, 2);

// ==========================
// SSS COMPUTATION (2025-2026)
// ==========================

// MSC (Monthly Salary Credit)
if ($salary < 5000) {
    $msc = 5000;
} elseif ($salary > 35000) {
    $msc = 35000;
} else {
    $msc = round($salary / 500) * 500;
}

// Contributions
$employee_sss = $msc * 0.05 / 2; // Employee Share (5%)
$employer_sss = $msc * 0.10; // Employer Share (10%)


// EC (Employee Compensation)
$ec = ($msc <= 15000) ? 10 : 30;

// Total SSS
$total_sss = $employee_sss + $employer_sss + $ec;


if ($salary < 10000) {
    $ph_salary = 10000;
} elseif ($salary > 100000) {
    $ph_salary = 100000;
} else {
    $ph_salary = $salary;
}

$employee_philhealth = $ph_salary * 0.025 / 2;

// ✅ USE session values submitted from the form (already stored by your form handler)
$tax        = floatval($data['tax'] ?? 0);
$sss        = floatval($data['sss'] ?? 0);       // ← this now uses the edited value
$pagibig    = floatval($data['pagibig'] ?? 0);   // ← same
$philhealth = floatval($data['philhealth'] ?? 0); // ← same
$SSSLoan = floatval($data['sssloan'] ?? 0); // ← same


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="shortcut icon" href="../../img/cslogos.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/add-emp.css">
    <link rel="stylesheet" href="css/emp.css">
    <link rel="stylesheet" href="css/emp-payslip.css">
    <style>
        .overtime-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .add-extra-ot-btn {
            padding: 4px 10px;
            border: none;
            border-radius: 5px;
            background: #1a2a44;
            color: #ffffff;
            font-size: 12px;
            cursor: pointer;
            transition: 0.2s;
        }

        .add-extra-ot-btn:hover {
            opacity: 0.85;
        }

        .add-extra-ot-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .extra-overtime {
            display: none;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .extra-overtime input {
            width: 100%;
            min-width: 0;
        }

        .multiply-sign {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <div class="topbar">
            <a href="index.php"> <img src="../../img/cslogos.png" alt="Logo" class="logo">
            </a>
            <h2 class="compname">Casas San Luis Payroll</h2>
        </div>
        <ul class="list">
            <li onclick="location.href='index.php'" style="cursor:pointer;">
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



    <div class="main">
        <div class="navbar">
            <button onclick="toggleSidebar()">☰</button>
            <h3 class="nav">Employee </h3>
            <div class="user">Welcome, <?= htmlspecialchars($fullName) ?></div>
        </div>
        <div class="content">
            <form action="process/process_payslip.php" method="POST">

                <input type="hidden" name="employee_id" value="<?= $emp['id'] ?>">

                <!-- EMPLOYEE INFO -->
                <div class="card">
                    <h3>Employee Info</h3>
                    <div class="employee-info">
                        <p><strong>Employee No:</strong> <?= $emp['empNo'] ?></p>
                        <p><strong>Name:</strong> <?= $emp['empName'] ?></p>
                        <p><strong>Designation:</strong> <?= $emp['designation'] ?></p>
                        <p><strong>Salary (Semi):</strong> ₱ <?= number_format($emp['salary'] / 2, 2) ?></p>
                        <p><strong>Daily Rate:</strong> <?= number_format($daily_rate, 2) ?></p>
                    </div>
                </div>



                <div class="card">
                    <h3>Payroll Info <span class="badge" id="period-badge"></span></h3>
                    <div class="grid-2">
                        <div>
                            Cut-off Start
                            <input type="date" name="cut_off_start" id="cut_off_start">
                        </div>
                        <div>
                            Cut-off End
                            <input type="date" name="cut_off_end" id="cut_off_end">
                        </div>
                        <div>
                            Payroll Date
                            <input type="date" name="payroll_date" id="payroll_date">
                        </div>
                        <div>
                            Work Days
                            <input type="number" name="work_days" id="work_days">
                        </div>
                    </div>
                </div>



                <!-- EARNINGS -->
                <div class="card">
                    <h3>Earnings</h3>
                    <div class="grid-2">

                        <div>Regular Holiday (Hr/s)
                            <input type="number" name="regular_holiday" step="0.01">
                        </div>

                        <!-- <div>Overtime (Hours)
                            <input type="number" name="overtime" step="0.01">
                        </div> -->
                        <div class="overtime-field">
                            <div class="overtime-label">
                                <span>Overtime (Hr/s)</span>

                                <button type="button"
                                    id="addExtraOTBtn"
                                    class="add-extra-ot-btn"
                                    onclick="addExtraOvertime()">
                                    + Add Previous Overtime
                                </button>
                            </div>

                            <input type="number" name="overtime" step="0.01">

                            <div id="extraOvertime" class="extra-overtime">
                                <input type="number"
                                    name="extra_ot_hour"
                                    step="0.01"
                                    placeholder="Hour">

                                <span class="multiply-sign">×</span>

                                <input type="number"
                                    name="extra_ot_rate"
                                    step="0.01"
                                    placeholder="Rate">
                            </div>
                        </div>

                        <div>Rest Day Overtime
                            <input type="number" name="restday_ot" step="0.01">
                        </div>

                        <div>Special Overtime
                            <input type="number" name="special_ot" step="0.01" placeholder="Not Available" disabled>
                        </div>

                        <div>Allowance
                            <input type="number" name="allowance" step="0.01">
                        </div>

                        <div>Adjustment
                            <input type="number" name="adjustment" step="0.01">
                        </div>

                        <div>13th Month
                            <input type="number" name="thirteenth" step="0.01">
                        </div>
                    </div>
                </div>

                <!-- DEDUCTIONS -->

                <div class="card">
                    <h3>Deductions
                        <button type="button" id="toggleDeductions" onclick="toggleDeductionsEdit()"
                            style="margin-left:10px; padding:4px 12px; cursor:pointer;">
                            ✏️ Edit
                        </button>
                    </h3>

                    <div class="deductions">
                        <!-- LEFT -->
                        <!-- Replace your existing hidden inputs with these -->
                        <div class="left">
                            <div>Tax
                                <input type="number" step="0.01" value="0.00" name="tax" disabled>
                            </div>

                            <div>SSS
                                <input type="number" id="sss_display" value="<?= $employee_sss ?>" disabled>
                                <input type="hidden" name="sss" id="sss_hidden" value="<?= $employee_sss ?>">
                            </div>

                            <div>Pag-ibig
                                <input type="number" id="pagibig_display" value="100" disabled>
                                <input type="hidden" name="pagibig" id="pagibig_hidden" value="100">
                            </div>

                            <div>Philhealth
                                <input type="number" id="philhealth_display" value="<?= $employee_philhealth ?>" disabled>
                                <input type="hidden" name="philhealth" id="philhealth_hidden" value="<?= $employee_philhealth ?>">
                            </div>


                        </div>

                        <!-- RIGHT -->
                        <div class=" right">
                            <div>Employee Loan
                                <input type="number" name="late" id="EMPLoan_display" value="0.00" disabled>
                                <input type="hidden" name="sssloan" id="EMPLoan_hidden">
                            </div>

                            <div>SSS Loan
                                <input type="number" id="SSSLoan_display" step="0.01" value="0.00" disabled>
                                <input type="hidden" name="sssloan" id="SSSLoan_hidden">
                            </div>
                            <div>Pag-Ibig Loan
                                <input type="number" id="HDMFLoan_display" step="0.01" value="0.00" disabled>
                                <input type="hidden" name="hdmfloan" id="HDMFLoan_hidden">
                            </div>
                            <div>Absent <input type="number" name="absent" step="0.5" disabled></div>
                        </div>
                    </div>
                </div>



                <button class="button" type="submit">Generate Payslip</button>

            </form>
        </div>
        <script src="js/jquery.js"></script>
        <script src="js/payslip.js"></script>

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

            function toggleDeductionsEdit() {
                const card = document.getElementById('toggleDeductions').closest('.card');
                const inputs = card.querySelectorAll('input[type="number"]');
                const btn = document.getElementById('toggleDeductions');
                const isDisabled = inputs[0].disabled;

                inputs.forEach(input => {
                    input.disabled = !isDisabled;
                });

                btn.textContent = isDisabled ? '🔒 Lock' : '✏️ Edit';
            }

            // Sync visible inputs → hidden inputs on change
            document.getElementById('sss_display').addEventListener('input', function() {
                document.getElementById('sss_hidden').value = this.value;
            });
            document.getElementById('pagibig_display').addEventListener('input', function() {
                document.getElementById('pagibig_hidden').value = this.value;
            });
            document.getElementById('philhealth_display').addEventListener('input', function() {
                document.getElementById('philhealth_hidden').value = this.value;
            });
            document.getElementById('SSSLoan_display').addEventListener('input', function() {
                document.getElementById('SSSLoan_hidden').value = this.value;
            });
            document.getElementById('HDMFLoan_display').addEventListener('input', function() {
                document.getElementById('HDMFLoan_hidden').value = this.value;
            });
            document.getElementById('EMPLoan_display').addEventListener('input', function() {
                document.getElementById('EMPLoan_hidden').value = this.value;
            });

            function pad(n) {
                return String(n).padStart(2, '0');
            }

            function fmt(y, m, d) {
                return `${y}-${pad(m)}-${pad(d)}`;
            }

            function computeWorkDays(start, end) {
                let count = 0;
                let cur = new Date(start);
                const last = new Date(end);
                while (cur <= last) {
                    const day = cur.getDay();
                    if (day !== 0 && day !== 6) count++;
                    cur.setDate(cur.getDate() + 1);
                }
                return count;
            }

            function updateCutoff(dateStr) {
                if (!dateStr) return;
                const [y, m, d] = dateStr.split('-').map(Number);
                let startDay, endDay;

                if (d >= 1 && d <= 15) {
                    startDay = 1;
                    endDay = 15;
                } else {
                    startDay = 16;
                    endDay = new Date(y, m, 0).getDate();
                }

                const startStr = fmt(y, m, startDay);
                const endStr = fmt(y, m, endDay);

                document.getElementById('cut_off_start').value = startStr;
                document.getElementById('cut_off_end').value = endStr;
                document.getElementById('work_days').value = computeWorkDays(startStr, endStr);
                document.getElementById('payroll_date').value = endStr;
            }

            // ✅ DOMContentLoaded para laging nag-eexecute after page refresh
            document.addEventListener('DOMContentLoaded', function() {
                const today = new Date();
                const todayStr = `${today.getFullYear()}-${pad(today.getMonth()+1)}-${pad(today.getDate())}`;
                updateCutoff(todayStr);
            });

            function addExtraOvertime() {
                const extraOT = document.getElementById('extraOvertime');
                const addBtn = document.getElementById('addExtraOTBtn');

                // Ipakita ang additional boxes
                extraOT.style.display = 'flex';

                // Isang beses lang puwedeng i-click
                addBtn.disabled = true;
                addBtn.textContent = 'Added';
                addBtn.style.cursor = 'not-allowed';
                addBtn.style.opacity = '0.5';
            }
        </script>
</body>

</html>