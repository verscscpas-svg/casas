<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: ../../index.php');
    exit;
}

// ROLE CHECK
$designation = strtolower(trim($_SESSION['Role'] ?? ''));

if ($designation !== 'supervisor') {
    header('Location: ../../index.php');
    exit;
}

// Name fix
$fullName  = $_SESSION['EmpName'] ?? $_SESSION['empName'] ?? 'Employee';
$firstName = explode(' ', trim($fullName))[0];

require_once "process/connection.php";

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    // ✅ ISA LANG NA QUERY (use correct column)
    $stmt = $conn->prepare("SELECT * FROM employees WHERE empNo = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        header('Location: settings.php?error=employee_not_found');
        exit();
    }
} else {
    echo "No ID provided";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="shortcut icon" href="../../img/cslogos.png" type="image/x-icon">

    <link rel="stylesheet" href="css/summary.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


</head>

<body>

    <div class="sidebar" id="sidebar">
        <div class="topbar">
            <a href="home.php"> <img src="../../img/cslogos.png" alt="Logo" class="logo">
            </a>
            <h2 class="compname">Casas San Luis Payroll</h2>
        </div>
        <ul class="list">

            <li onclick="history.back()" style="cursor:pointer;">
                ⬅️<span> BACK </span>
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
            <div class="user" style="text-transform: capitalize;">Welcome <?= htmlspecialchars($fullName) ?></div>

        </div>





        <div class="content">
            <?php
            // --- DB CONNECTION ---
            require_once 'process/connection.php';
            if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

            $sql = "
                SELECT
                    e.empNo,
                    e.empName,
                    e.designation,
                    p.cut_off_start,
                    p.cut_off_end,
                    p.basic_pay,
                    p.allowance,
                    p.adjustment,
                    p.tax,
                    p.sss,
                    p.pagibig,
                    p.philhealth,
                    p.total_deductions,
                    p.gross_pay,
                    p.net_pay
                FROM payslips p
                INNER JOIN employees e ON p.employee_id = e.id
                ORDER BY e.empNo ASC
                ";

            $result = $conn->query($sql);
            $payslips = [];
            while ($row = $result->fetch_assoc()) {
                $payslips[] = $row;
            }
            $conn->close();
            ?>

            <div class="toolbar">
                <div class="search-wrap">
                    <svg class="search-icon" viewBox="0 0 16 16">
                        <circle cx="6.5" cy="6.5" r="4" />
                        <line x1="10" y1="10" x2="14" y2="14" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search by name or emp no…" oninput="renderTable()">
                </div>
                <div class="filter-btn" id="sortBtn">
                    <button id="sortTrigger" onclick="toggleDropdown(event)">
                        <svg style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round" viewBox="0 0 16 16">
                            <line x1="2" y1="4" x2="14" y2="4" />
                            <line x1="4" y1="8" x2="12" y2="8" />
                            <line x1="6" y1="12" x2="10" y2="12" />
                        </svg>
                        Sort by
                        <svg style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round" viewBox="0 0 12 12">
                            <polyline points="2,4 6,8 10,4" />
                        </svg>
                    </button>
                    <div class="dropdown" id="sortDropdown">
                        <div class="dropdown-item selected" data-sort="empno" onclick="setSort(this)">
                            <svg class="check" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="2,7 5,10 12,3" />
                            </svg>
                            Employee number
                        </div>
                        <div class="dropdown-item" data-sort="name" onclick="setSort(this)">
                            <svg class="check" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="2,7 5,10 12,3" />
                            </svg>
                            Name (alphabetical)
                        </div>
                        <div class="dropdown-item" data-sort="cutoff" onclick="setSort(this)">
                            <svg class="check" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="2,7 5,10 12,3" />
                            </svg>
                            Cut-off start
                        </div>

                    </div>
                </div>
                <span class="count-badge" id="countBadge"></span>
                <button class="export-btn" onclick="exportCSV()">
                    <svg style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round" viewBox="0 0 16 16">
                        <path d="M8 2v8M5 7l3 3 3-3M3 13h10" />
                    </svg>
                    Export CSV
                </button>
            </div>

            <div class="tbl-wrap">
                <table class="payroll-table">
                    <thead>
                        <tr>
                            <th class="left" rowspan="2">empNo</th>
                            <th rowspan="2">cut_off_start / cut_off_end</th>
                            <th class="left" rowspan="2">empName</th>
                            <th colspan="2">salary</th>
                            <th rowspan="2">Taxable<br><span style="font-weight:400;font-size:10px">(basic_pay)</span></th>
                            <th colspan="2">Non-taxable</th>
                            <th rowspan="2">gross_pay</th>
                            <th rowspan="2">W/ tax<br><span style="font-weight:400;font-size:10px">(tax)</span></th>
                            <th rowspan="2">philhealth</th>
                            <th rowspan="2">sss</th>
                            <th rowspan="2">pagibig</th>
                            <th rowspan="2">total_deductions</th>
                            <th rowspan="2">net_pay</th>
                        </tr>
                        <tr class="sub" style="font-size: 10px !important;">
                            <th>Monthly (salary×2)</th>
                            <th>Half (basic_pay)</th>
                            <th>Half salary</th>
                            <th>Others (0.00)</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($payslips as $r):
                            $monthly = $r['basic_pay'] * 2;
                            $half    = $r['basic_pay'];
                            $start   = date('M d', strtotime($r['cut_off_start']));
                            $end     = date('M d, Y', strtotime($r['cut_off_end']));
                        ?>
                            <tr
                                data-empno="<?= htmlspecialchars($r['empNo']) ?>"
                                data-name="<?= strtolower(htmlspecialchars($r['empName'])) ?>"
                                data-cutstart="<?= $r['cut_off_start'] ?>"
                                data-cutend="<?= $r['cut_off_end'] ?>"
                                data-gross="<?= $r['gross_pay'] ?>"
                                data-deduct="<?= $r['total_deductions'] ?>"
                                data-net="<?= $r['net_pay'] ?>"
                                data-export='<?= json_encode([
                                                    "empNo"            => $r['empNo'],
                                                    "cut_off_start"    => $r['cut_off_start'],
                                                    "cut_off_end"      => $r['cut_off_end'],
                                                    "empName"          => $r['empName'],
                                                    "monthly"          => number_format($monthly, 2, '.', ''),
                                                    "basic_pay"        => number_format($half, 2, '.', ''),
                                                    "taxable"          => number_format($half, 2, '.', ''),
                                                    "non_taxable_half" => "0.00",
                                                    "non_taxable_other" => "0.00",
                                                    "gross_pay"        => number_format($r['gross_pay'], 2, '.', ''),
                                                    "tax"              => number_format($r['tax'], 2, '.', ''),
                                                    "philhealth"       => number_format($r['philhealth'], 2, '.', ''),
                                                    "sss"              => number_format($r['sss'], 2, '.', ''),
                                                    "pagibig"          => number_format($r['pagibig'], 2, '.', ''),
                                                    "total_deductions" => number_format($r['total_deductions'], 2, '.', ''),
                                                    "net_pay"          => number_format($r['net_pay'], 2, '.', ''),
                                                ]) ?>'>
                                <td class="emp-id left"><?= htmlspecialchars($r['empNo']) ?></td>
                                <td class="period"><?= $start ?> – <?= $end ?></td>
                                <td class="emp-name left"><?= htmlspecialchars($r['empName']) ?></td>
                                <td><?= number_format($monthly, 2) ?></td>
                                <td><?= number_format($half, 2) ?></td>
                                <td><?= number_format($half, 2) ?></td>
                                <td class="zero">0.00</td>
                                <td class="zero">0.00</td>
                                <td><?= number_format($r['gross_pay'], 2) ?></td>
                                <td><?= $r['tax'] > 0 ? number_format($r['tax'], 2) : '<span class="zero">0.00</span>' ?></td>
                                <td><?= $r['philhealth'] > 0 ? number_format($r['philhealth'], 2) : '<span class="zero">0.00</span>' ?></td>
                                <td><?= $r['sss'] > 0 ? number_format($r['sss'], 2) : '<span class="zero">0.00</span>' ?></td>
                                <td><?= $r['pagibig'] > 0 ? number_format($r['pagibig'], 2) : '<span class="zero">0.00</span>' ?></td>
                                <td><?= number_format($r['total_deductions'], 2) ?></td>
                                <td class="net"><?= number_format($r['net_pay'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="js/jquery.js"></script>
        <script src="js/view.js"></script>
        <script src="js/proof-of-payment.js"></script>
        <script src="js/summary.js"></script>



</body>

</html>