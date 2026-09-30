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

$id = intval($_GET['id']);

// 🔹 Get payslip + employee info
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
$daily_rate = ($data['work_days'] > 0) ? ($data['basic_pay'] + ($data['absent'] * ($data['basic_pay'] / $data['work_days']))) / $data['work_days'] : 0;

// absent deduction
$absent_deduction = $data['absent'] * $daily_rate;
// 🔹 Optional: Compute daily rate from basic pay and work days
$daily_rate = ($data['work_days'] > 0) ? ($data['basic_pay'] / $data['work_days']) : 0;
$fixrate = ($data['basic_pay'] * 2 * 12 / 261);

// Basic Pay
$basic_pay_display = $data['basic_pay'] ?? 0;

// Regular Holiday
$regular_holiday_days = $data['regular_holiday'] ?? 0; // number of days
$holiday_pay = $data['holiday_pay'] ?? 0; // computed holiday pay

// Overtime
$overtime_hours = $data['overtime'] ?? 0;
$overtime_pay = $data['overtime_pay'] ?? 0;

// Rest Day Overtime
$restday_hours = $data['restday_ot'] ?? 0;
$restday_pay = $data['restday_pay'] ?? 0;

// Special Overtime
$special_hours = $data['special_ot'] ?? 0;
$special_pay = $data['special_pay'] ?? 0;

// Allowance, Adjustment, 13th Month
$allowance = $data['allowance'] ?? 0;
$adjustment = $data['adjustment'] ?? 0;
$thirteenth = $data['thirteenth'] ?? 0;
$extra_ot_hrs =  $data['extra_ot_hour'] ?? 0;

$total_ot_hrs = $extra_ot_hrs + $overtime_hours;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>
    <link rel="stylesheet" href="css/pre-pay.css">
    <link rel="shortcut icon" href="../../img/cslogos.png" type="image/x-icon">
    <link rel="stylesheet" href="css/view-payslip.css">
    <link rel="stylesheet" href="css/preview-view-payslip.css">
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
            <div class="payslip-container" id="payslip-container">
                <div class="COMPANY">
                    <div class="IMGLOGO"><img src="../../img/cs.png" alt=" "></div>
                    <h1 class="COMP">CASAS SAN LUIS & CO.</h1>
                </div>

                <!-- HEADER -->
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
                                <td class="text-right"><?= number_format($data['basic_pay'], 2) ?></td>
                            </tr>

                            <tr>
                                <td class="reghol">
                                    Regular Holiday(<span class="hours"><?= $data['reg_holiday'] ?></span>Hr/s)
                                </td>
                                <td class="text-right"><?= number_format($data['regular_holiday'], 2) ?></td>
                            </tr>

                            <tr>
                                <td>
                                    Overtime(<span class="hours"><?= $data['ot'] ?></span>Hr/s)
                                </td>
                                <td class="text-right"><?= number_format($data['overtime_pay'], 2) ?></td>
                            </tr>

                            <tr>
                                <td>
                                    Rest Day Overtime(<span class="hours"><?= $data['rd_ot'] ?></span>Hr/s)
                                </td>
                                <td class="text-right"><?= number_format($data['restday_ot'], 2) ?></td>
                            </tr>

                            <tr>
                                <td>
                                    Special Overtime(<span class="hours"><?= $data['spec_ot'] ?></span>Hr/s)
                                </td>
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
                        <script>
                            document.querySelectorAll('.hours').forEach(function(element) {
                                let value = parseFloat(element.textContent);

                                if (!isNaN(value)) {
                                    element.textContent = value;
                                }
                            });
                        </script>

                    </table>

                    <!-- DEDUCTIONS -->
                    <table class="column-table">
                        <thead>
                            <tr>
                                <th>&nbsp;&nbsp;&nbsp;&nbsp;DEDUCTIONS</th>
                                <th class="text-right">AMOUNT</th>
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
                                <td class="up"> &nbsp;&nbsp;</td>
                                <td class="text-right">&nbsp; </td>
                            </tr>


                        </tbody>
                    </table>
                </div>

                <!-- TOTALS -->
                <!-- <div class="totals-section">
                    <div class="total-row"><span>GROSS PAY:</span> <span><?= number_format($data['gross_pay'], 2) ?></span></div>
                    <div class="total-row under"><span>DEDUCTIONS:</span> <span><?= number_format($data['total_deductions'], 2) ?></span></div>
                    <div class="total-row" style="font-weight:bold;"><span>NET PAY:</span> <span><?= number_format($data['net_pay'], 2) ?></span></div>
                </div> -->
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
            <div class="btn-function">
                <button class="print" id="print">PRINT</button><br>
                <button class="send" id="send">SEND</button>
            </div>
        </div>
        <script src="js/jquery.js"></script>
        <script src="js/previ-view-payslip.js"></script>
        <script src="js/print.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            /* =========================
        SINGLE DECLARATION ONLY
        ========================= */
            const PAYSLIP_ID = <?= json_encode($id) ?>;

            /* =========================
               CONVERT IMAGES TO BASE64
               para automatic ang logo sa PDF
            ========================= */
            async function convertImagesToBase64(html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const imgs = doc.querySelectorAll('img');

                for (let img of imgs) {
                    if (!img.src.startsWith('data:')) {
                        try {
                            const response = await fetch(img.src);
                            const blob = await response.blob();
                            const base64 = await new Promise(resolve => {
                                const reader = new FileReader();
                                reader.onload = () => resolve(reader.result);
                                reader.readAsDataURL(blob);
                            });
                            img.src = base64;
                        } catch (e) {
                            console.warn("Could not convert image:", img.src);
                        }
                    }
                }

                return doc.body.innerHTML;
            }

            /* =========================
               SEND PAYSLIP
            ========================= */
            document.getElementById("send").addEventListener("click", async function() {

                Swal.fire({
                    title: "Preparing...",
                    text: "Please wait",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // FIX: i-convert muna ang images sa base64 bago i-send
                const rawHtml = document.getElementById("payslip-container").innerHTML;
                const payslipHTML = await convertImagesToBase64(rawHtml);

                const formData = new FormData();
                formData.append("html", payslipHTML);
                formData.append("id", PAYSLIP_ID);

                fetch("send_payslip.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.status === "success" ? "success" : "error",
                            title: data.status === "success" ? "Sent!" : "Failed",
                            text: data.message
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Something went wrong"
                        });
                    });
            });


            /* =========================
               PREVIEW PDF
            ========================= */



            /* =========================
               OPTIONAL PRINT BUTTON
            ========================= */
            // document.getElementById("print").addEventListener("click", function() {
            //     window.print();
            // });
        </script>
</body>

</html>