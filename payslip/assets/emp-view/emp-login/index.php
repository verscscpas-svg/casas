<?php include 'welcome_modal.php';
?><?php


    if (empty($_SESSION['logged_in'])) {
        header('Location: ../index.php');
        exit;
    }
    $designation = strtolower(trim($_SESSION['Role'] ?? ''));

    if ($designation == 'superadmin') {
        header('Location: ../../index.php');
        exit;
    }
    // ✅ FIXED: consistent session name
    $fullName  = $_SESSION['empName'] ?? 'Employee';
    $firstName = explode(' ', trim($fullName))[0];

    require_once "../foremp/connection.php";

    if (isset($_GET['id'])) {

        $id = $_GET['id'];

        $sql = "SELECT * FROM employees WHERE id = '$id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "Employee not found";
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
    <!-- <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/add-emp.css">
    <link rel="stylesheet" href="css/view-emp.css">
    <link rel="stylesheet" href="css/index.css"> -->
    <!-- Add this CSS inside your <style> tag -->
    <style>
        /* ─── BASE STYLES & INITIALIZATION ─── */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #303c5a;
            color: #333;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ─── SIDEBAR STYLES ─── */
        .sidebar {
            position: fixed;
            top: 0;
            left: -260px;
            /* Nakatago sa mobile sa simula */
            width: 260px;
            height: 100vh;
            background: #1a2a44;
            color: #fff;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: left 0.3s ease;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Kapag active ang sidebar (bubukas) */
        .sidebar.active {
            left: 0;
        }

        #sidebarOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .sidebar.active #sidebarOverlay {
            display: block;
        }

        .topbar {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .compname {
            font-size: 16px;
            font-weight: 600;
        }

        .sidebar .list {
            list-style: none;
            padding: 20px 0;
            flex-grow: 1;
        }

        .sidebar .list .li li {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .sidebar .list .li li:hover,
        .sidebar .list .li li.active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #e05252;
        }

        .sidebar .list .li li a {
            color: #fff;
            text-decoration: none;
            font-size: 15px;
        }

        .logout-btn {
            margin: 20px;
            padding: 12px;
            background: #e05252;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background: #c84343;
        }

        /* ─── MAIN CONTENT AREA ─── */
        .main {
            flex-grow: 1;
            width: 100%;
            transition: padding-left 0.3s ease;
        }

        .navbar {
            background: #fff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .navbar button {
            background: none;
            border: none;

            cursor: pointer;
            color: #1a2a44;
        }

        .nav {
            font-size: 18px;
            font-weight: 600;
        }

        .user {
            font-size: 10px;
            color: #666;
        }

        .content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ─── RESUME / PROFILE CONTAINER ─── */
        .resume-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            /* Stacked sa mobile */
            margin-bottom: 25px;
        }

        .left-panel {
            background: #1a2a44;
            color: #fff;
            padding: 30px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .left-panel img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 15px;
        }

        .left-panel h2 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .left-panel p {
            font-size: 14px;
            opacity: 0.8;
        }

        .right-panel {
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .section h3 {
            font-size: 16px;
            color: #1a2a44;
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .info {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            font-size: 14px;
        }

        .label {
            font-weight: 600;
            color: #6b84aa;
        }

        .qr-btn {
            background: #f5f7fb;
            color: #1a2a44;
            border: 1px solid #dde3f0;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .qr-btn:hover {
            background: #e8ecf5;
        }

        /* ─── PAYSLIP TABLE STYLES ─── */
        .payslip-section {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
            /* Scrollable kapag sumobra ang lapad sa maliit na screen */
        }

        .payslip-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .payslip-table th {
            background: #1a2a44;
            color: #fff;
            padding: 12px;
            font-weight: 600;
        }

        .payslip-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .payslip-table tr:hover {
            background-color: #f9fbfd;
        }

        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            /* Bumababa ang button kapag masikip */
        }

        .view-btn,
        .proof-view-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        .view-btn {
            background: #1a2a44;
            color: white;
        }

        .proof-view-btn {
            background: #28a745;
            color: white;
        }

        .no-proof-badge {
            font-size: 11px;
            color: #999;
            font-style: italic;
        }

        /* ─── MODAL OVERLAYS & POPUPS ─── */
        .modal-overlay,
        .qr-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-box,
        .qr-modal {
            background: white;
            border-radius: 12px;
            padding: 20px;
            position: relative;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .qr-modal {
            max-width: 400px;
            text-align: center;
        }

        .qr-modal img,
        .modal-box img {
            width: 100%;
            height: auto;
            max-height: 70vh;
            object-fit: contain;
            border-radius: 8px;
            margin-top: 10px;
        }

        .qr-modal-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        .btn-gray {
            background: #f5f7fb;
            color: #333;
        }

        /* ─── ANIMATION ─── */
        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }


        /* ─── 📱 RESPONSIVE MEDIA QUERIES ─── */

        /* Desktop Screens (992px pataas) */
        @media (min-width: 992px) {
            .sidebar {
                left: 0;
                /* Palaging nakalabas ang sidebar */
            }

            .main {
                padding-left: 260px;
                /* Iuurong ang main content pakanan */
            }

            .navbar button {
                display: none;
                /* Itago ang hamburger menu icon sa malalaking screen */
            }

            .resume-container {
                flex-direction: row;
                /* Magkatabi ang kaliwa at kanang panel */
            }

            .left-panel {
                width: 30%;
                border-right: 1px solid rgba(0, 0, 0, 0.1);
            }

            .right-panel {
                width: 70%;
            }

            .info {
                grid-template-columns: 1fr 1fr;
                /* Magiging 2 columns ang data fields */
            }

            .qr-btn {
                width: max-content;
                /* Sakto lang sa text ang lapad sa desktop */
                padding: 10px 20px;
            }
        }

        /* Tablet Screens (768px hanggang 991px) */
        @media (min-width: 768px) and (max-width: 991px) {
            .info {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <div id="sidebarOverlay" onclick="closeSidebar()"></div>
        <div class="topbar">
            <a href="index.php"> <img src="css/cslogos.png" alt="Logo" class="logo">
            </a>
            <h2 class="compname">Casas San Luis Payroll</h2>
        </div>
        <ul class="list">
            <div class="li">
                <li onclick="window.location='process/settings.php?id=<?php echo (int)$row['id']; ?>'">
                    ⚙️<span><a href="process/settings.php?id=<?php echo (int)$row['id']; ?>">Settings</a></span>
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
    align-items:center; justify-content:center;">
        <div style="
        background:#fff; border-radius:16px;
        padding:2rem 2.25rem; max-width:360px; width:90%;
        text-align:center;
        box-shadow:0 20px 50px rgba(41,65,105,.2);
        animation:slideUp .3s cubic-bezier(.34,1.56,.64,1) both;">
            <!-- Icon -->
            <div style="
            width:60px; height:60px; border-radius:50%;
            background:#fff3f3; margin:0 auto 1.25rem;
            display:flex; align-items:center; justify-content:center;">
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
            <h3 class=" nav">Employee</h3>
            <!-- ✅ FIXED: show actual logged in name -->
            <div class="user">Welcome <?php echo htmlspecialchars($fullName); ?></div>
        </div>

        <div class="content">

            <div class="resume-container">


                <!-- LEFT PANEL -->
                <div class="left-panel">
                    <img src="../../employee/process/uploads/<?php echo $row['image']; ?>" alt="Employee Image">
                    <h2><?php echo $row['empName']; ?></h2>
                    <p>Employee ID: <?php echo $row['empNo']; ?></p>


                </div>


                <!-- RIGHT PANEL -->
                <div class="right-panel">

                    <div class="section">
                        <h3>Contact Information</h3>
                        <div class="info">
                            <div><span class="label">Email:</span> <?php echo $row['email']; ?></div>
                        </div>
                    </div>

                    <!-- Wrapper for single eye toggle coverage -->
                    <div style="position: relative;">

                        <!-- Single Eye Toggle - Upper Right Corner -->
                        <button onclick="toggleSensitive()" title="Show/Hide Sensitive Info" style="
            position: absolute;
            top: 0;
            right: 0;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #000000;
            z-index: 1;
        ">
                            <svg id="mainEyeOff" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-5 0-9.27-3.11-11-7 1.03-2.26 2.62-4.18 4.58-5.54" />
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c5 0 9.27 3.11 11 7a11.1 11.1 0 0 1-1.93 3.09" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                            <svg id="mainEyeOn" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>

                        <!-- Employment Details -->
                        <div class="section">
                            <h3>Employment Details</h3>
                            <div class="info">
                                <div><span class="label">Designation:</span> <?php echo $row['designation']; ?></div>
                                <div>
                                    <span class="label">Salary:</span>
                                    <span class="sensitive-masked">₱ <?php echo preg_replace('/\d/', '*', number_format($row['salary'], 2)); ?></span>
                                    <span class="sensitive-real" style="display:none;">₱ <?php echo number_format($row['salary'], 2); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Government IDs -->
                        <div class="section">
                            <br>
                            <h3>Government ID Numbers</h3>
                            <div class="info">
                                <?php
                                include 'process/format.php';

                                $tin        = formatTIN($row['tinNo']);
                                $sss        = formatSSS($row['sssNo']);
                                $philhealth = formatPhilHealth($row['philHealthNo']);
                                $pagibig    = formatPagIbig($row['pagIbigNo']);

                                $tinMasked        = preg_replace('/\d/', '*', $tin);
                                $sssMasked        = preg_replace('/\d/', '*', $sss);
                                $philhealthMasked = preg_replace('/\d/', '*', $philhealth);
                                $pagibigMasked    = preg_replace('/\d/', '*', $pagibig);
                                ?>
                                <div>
                                    <span class="label">TIN:</span>
                                    <span class="sensitive-masked"><?php echo $tinMasked; ?></span>
                                    <span class="sensitive-real" style="display:none;"><?php echo $tin; ?></span>
                                </div>
                                <div>
                                    <span class="label">SSS:</span>
                                    <span class="sensitive-masked"><?php echo $sssMasked; ?></span>
                                    <span class="sensitive-real" style="display:none;"><?php echo $sss; ?></span>
                                </div>
                                <div>
                                    <span class="label">PhilHealth:</span>
                                    <span class="sensitive-masked"><?php echo $philhealthMasked; ?></span>
                                    <span class="sensitive-real" style="display:none;"><?php echo $philhealth; ?></span>
                                </div>
                                <div>
                                    <span class="label">Pag-IBIG:</span>
                                    <span class="sensitive-masked"><?php echo $pagibigMasked; ?></span>
                                    <span class="sensitive-real" style="display:none;"><?php echo $pagibig; ?></span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- end relative wrapper -->

                    <?php $qrPath = '../../employee/process/uploads/' . htmlspecialchars($row['bankqr'], ENT_QUOTES); ?>
                    <button class="qr-btn" onclick="openQRModal('<?php echo $qrPath; ?>')">
                        View Bank QR Code
                    </button>

                </div>

                <script>
                    function toggleSensitive() {
                        const masked = document.querySelectorAll('.sensitive-masked');
                        const real = document.querySelectorAll('.sensitive-real');
                        const eyeOff = document.getElementById('mainEyeOff');
                        const eyeOn = document.getElementById('mainEyeOn');

                        const isHidden = real[0].style.display === 'none';

                        masked.forEach(el => el.style.display = isHidden ? 'none' : 'inline');
                        real.forEach(el => el.style.display = isHidden ? 'inline' : 'none');
                        eyeOff.style.display = isHidden ? 'none' : 'inline';
                        eyeOn.style.display = isHidden ? 'inline' : 'none';
                    }
                </script>
            </div>

            <!-- PAYSLIP TABLE -->

            <div class="section payslip-section">
                <h3>Payslip History</h3>
                <table class="payslip-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Payslip</th>
                            <th>Cut-off Period</th>
                            <th>Payroll Date</th>
                            <th>
                                Net Pay
                                <button onclick="toggleNetPayCol()" title="Show/Hide Net Pay" style="
                        background: none;
                        border: none;
                        cursor: pointer;
                        padding: 2px 4px;
                        color: #ffffff;
                        vertical-align: middle;
                    ">
                                    <svg id="npEyeOff" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-5 0-9.27-3.11-11-7 1.03-2.26 2.62-4.18 4.58-5.54" />
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c5 0 9.27 3.11 11 7a11.1 11.1 0 0 1-1.93 3.09" />
                                        <line x1="1" y1="1" x2="23" y2="23" />
                                    </svg>
                                    <svg id="npEyeOn" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $empId = $row['id'];
                        $payslipQuery = mysqli_query(
                            $conn,
                            "SELECT * FROM payslips WHERE employee_id = '$empId' ORDER BY payroll_date DESC"
                        );

                        $counter = 1;
                        if (mysqli_num_rows($payslipQuery) > 0):
                            while ($ps = mysqli_fetch_assoc($payslipQuery)):
                                $payrollDate  = date('M d, Y', strtotime($ps['payroll_date']));
                                $cutStart     = date('M d', strtotime($ps['cut_off_start']));
                                $cutEnd       = date('M d, Y', strtotime($ps['cut_off_end']));
                                $netPay       = number_format($ps['net_pay'], 2);
                                $netPayMasked = preg_replace('/\d/', '*', $netPay);
                                $label        = "Payslip - " . date('M d, Y', strtotime($ps['payroll_date']));
                                $proofImage   = !empty($ps['proof_of_payment']) ? $ps['proof_of_payment'] : null;
                        ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td><?php echo $label; ?></td>
                                    <td><?php echo $cutStart . " - " . $cutEnd; ?></td>
                                    <td><?php echo $payrollDate; ?></td>
                                    <td>
                                        <span class="np-masked">₱ <?php echo $netPayMasked; ?></span>
                                        <span class="np-real" style="display:none;">₱ <?php echo $netPay; ?></span>
                                    </td>
                                    <td class="action-btns">

                                        <!-- View Payslip -->
                                        <a href="current-payslip.php?id=<?php echo $ps['id']; ?>">
                                            <button class="view-btn">View</button>
                                        </a>

                                        <!-- Proof of Payment -->
                                        <?php if ($proofImage): ?>
                                            <button class="proof-view-btn"
                                                data-id="<?php echo $ps['id']; ?>"
                                                data-image="<?php echo htmlspecialchars($proofImage); ?>"
                                                data-label="<?php echo htmlspecialchars($label); ?>"
                                                onclick="viewProofOfPayment(this)">
                                                View Proof
                                            </button>
                                        <?php else: ?>
                                            <span class="no-proof-badge">No proof of payment attached file yet</span>
                                        <?php endif; ?>

                                    </td>
                                </tr>
                            <?php
                            endwhile;
                        else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">No payslips found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>




            <!-- ✅ VIEW PROOF MODAL (view only, walang delete) -->
            <div id="viewProofModal" class="modal-overlay">
                <div class="modal-box" style="max-width:600px;">
                    <h3 id="viewProofTitle"></h3>
                    <img id="viewProofImage" src="" alt="Proof of Payment" />
                    <div class="modal-footer" style="justify-content:flex-end;">
                        <button class="btn btn-gray" onclick="closeViewProofModal()">
                            Close
                        </button>
                    </div>
                </div>
            </div>



        </div>
    </div>
    <!-- QR Code Modal -->
    <div class="qr-modal-overlay" id="qrModalOverlay" onclick="closeQRModal(event)">
        <div class="qr-modal">
            <button class="qr-modal-close" onclick="closeQRModal()">&times;</button>
            <img id="qrImage" src="" alt="Upload Your preferred Bank Qr Code first " />
        </div>
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/view.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>

</html>