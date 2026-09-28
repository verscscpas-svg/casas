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

require_once "process/connection.php";
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
    <link rel="stylesheet" href="../css/home.css">
    <link rel="shortcut icon" href="../../img/cslogos.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/add-emp.css">
    <link rel="stylesheet" href="../css/add-emp.css">
    <link rel="stylesheet" href="css/view-emp.css">
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
            <div class="user" style="text-transform: capitalize;">Welcome <?= htmlspecialchars($fullName) ?></div>

        </div>

        <div class="content">

            <div class="resume-container">

                <!-- LEFT PANEL -->
                <div class="left-panel">
                    <img src="../employee/process/uploads/<?php echo $row['image']; ?>" alt="Employee Image">

                    <h2><?php echo $row['empName']; ?></h2>
                    <!-- <p><?php echo $row['designation']; ?></p> -->
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

                    <!-- Employment Details + Government IDs wrapped in one relative div -->
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
            color: #ffffff;
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

                        <div class="section">
                            <h3>Government IDs</h3>

                            <?php
                            include 'process/functions/format.php';

                            $tin        = formatTIN($row['tinNo']);
                            $sss        = formatSSS($row['sssNo']);
                            $philhealth = formatPhilHealth($row['philHealthNo']);
                            $pagibig    = formatPagIbig($row['pagIbigNo']);

                            $tinMasked        = preg_replace('/\d/', '*', $tin);
                            $sssMasked        = preg_replace('/\d/', '*', $sss);
                            $philhealthMasked = preg_replace('/\d/', '*', $philhealth);
                            $pagibigMasked    = preg_replace('/\d/', '*', $pagibig);
                            ?>

                            <div class="info">
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

                    <?php $qrPath = 'process/uploads/' . htmlspecialchars($row['bankqr'], ENT_QUOTES); ?>
                    <button class="qr-btn" onclick="openQRModal('<?php echo $qrPath; ?>')">
                        View Bank QR Code
                    </button>

                    <div class="button-row">
                        <a href="update-emp.php?id=<?php echo $row['id']; ?>">
                            <button type="button">Update</button>
                        </a>
                    </div>

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

            <?php
            // ✅ Declare ONCE outside the loop
            function maskNetPayValue($formatted)
            {
                return preg_replace('/\d/', '*', $formatted);
            }
            ?>

            <div class="section payslip-section">
                <h3>Payslip History</h3>
                <table class="payslip-table">
                    <thead>
                        <tr>
                            <th>Payslip</th>
                            <th>Cut-off Period</th>
                            <th>Payroll Date</th>
                            <th>
                                Net Pay
                                <button id="toggleNetPayBtn" onclick="toggleNetPayCol()" title="Show/Hide Net Pay" style="
                        background: none;
                        border: none;
                        cursor: pointer;
                        padding: 2px 4px;
                        color: #ffffff;
                        font-size: 16px;
                        vertical-align: middle;
                    ">
                                    <svg id="npEyeOff" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-5 0-9.27-3.11-11-7 1.03-2.26 2.62-4.18 4.58-5.54" />
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c5 0 9.27 3.11 11 7a11.1 11.1 0 0 1-1.93 3.09" />
                                        <line x1="1" y1="1" x2="23" y2="23" />
                                    </svg>
                                    <svg id="npEyeOn" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
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

                        if (mysqli_num_rows($payslipQuery) > 0):
                            while ($ps = mysqli_fetch_assoc($payslipQuery)):
                                $payrollDate  = date('M d, Y', strtotime($ps['payroll_date']));
                                $cutStart     = date('M d', strtotime($ps['cut_off_start']));
                                $cutEnd       = date('M d, Y', strtotime($ps['cut_off_end']));
                                $netPay       = number_format($ps['net_pay'], 2);
                                $label        = "Payslip - " . date('M d, Y', strtotime($ps['payroll_date']));
                                $proofImage   = !empty($ps['proof_of_payment']) ? $ps['proof_of_payment'] : null;
                                $netPayMasked = maskNetPayValue($netPay); // ✅ call the function, don't declare it here
                        ?>
                                <tr>
                                    <td><?php echo $label; ?></td>
                                    <td><?php echo $cutStart . " - " . $cutEnd; ?></td>
                                    <td><?php echo $payrollDate; ?></td>
                                    <td>
                                        <span class="np-masked">₱ <?php echo $netPayMasked; ?></span>
                                        <span class="np-real" style="display:none;">₱ <?php echo $netPay; ?></span>
                                    </td>
                                    <td class="action-btns">
                                        <a href="current-payslip.php?id=<?php echo $ps['id']; ?>">
                                            <button class="view-btn">View</button>
                                        </a>

                                        <?php if ($proofImage): ?>
                                            <button class="proof-view-btn"
                                                data-id="<?php echo $ps['id']; ?>"
                                                data-image="<?php echo htmlspecialchars($proofImage); ?>"
                                                data-label="<?php echo htmlspecialchars($label); ?>"
                                                onclick="viewProofOfPayment(this)">
                                                View Proof
                                            </button>
                                        <?php else: ?>
                                            <button class="proof-upload-btn"
                                                data-id="<?php echo $ps['id']; ?>"
                                                data-label="<?php echo htmlspecialchars($label); ?>"
                                                onclick="uploadProofOfPayment(this)">
                                                Upload Proof
                                            </button>
                                        <?php endif; ?>

                                        <button class="delete-btn"
                                            data-id="<?php echo $ps['id']; ?>"
                                            data-label="<?php echo htmlspecialchars($label); ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            endwhile;
                        else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">No payslips found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <script>
                function toggleNetPayCol() {
                    const masked = document.querySelectorAll('.np-masked');
                    const real = document.querySelectorAll('.np-real');
                    const eyeOff = document.getElementById('npEyeOff');
                    const eyeOn = document.getElementById('npEyeOn');

                    const isHidden = real[0].style.display === 'none';

                    masked.forEach(el => el.style.display = isHidden ? 'none' : 'inline');
                    real.forEach(el => el.style.display = isHidden ? 'inline' : 'none');

                    eyeOff.style.display = isHidden ? 'none' : 'inline';
                    eyeOn.style.display = isHidden ? 'inline' : 'none';
                }
            </script>

            <!-- ✅ VIEW PROOF MODAL -->
            <div id="viewProofModal" class="modal-overlay">
                <div class="modal-box" style="max-width:600px;">
                    <h3 id="viewProofTitle"></h3>
                    <img id="viewProofImage" src="" alt="Proof of Payment" id="viewProofImage" />
                    <div class="modal-footer">
                        <button class="btn btn-danger" onclick="deleteProofFromModal()">
                            🗑️ Delete Proof
                        </button>
                        <button class="btn btn-gray" onclick="closeViewProofModal()">
                            Close
                        </button>
                    </div>
                </div>
            </div>


            <!-- ✅ UPLOAD PROOF MODAL -->
            <div id="uploadProofModal" class="modal-overlay">
                <div class="modal-box" style="max-width:480px;">
                    <h3 id="uploadProofTitle">Upload Proof of Payment</h3>
                    <input type="hidden" id="uploadPayslipId" value="">

                    <div class="file-upload-wrapper">
                        <div class="file-upload-area" id="dropZone">
                            <input type="file" id="proofFileInput" accept="image/*">
                            <div class="file-upload-icon">
                                <svg viewBox="0 0 24 24" stroke-width="1.8" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                </svg>
                            </div>
                            <p class="file-upload-text"><span>Click to upload</span> or drag and drop</p>
                            <p class="file-upload-hint">PNG, JPG, WEBP — max 5MB</p>
                        </div>

                        <!-- File chosen indicator -->
                        <div class="file-chosen" id="fileChosen">
                            <div class="file-chosen-icon">
                                <svg viewBox="0 0 24 24" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                            <div style="flex:1; overflow:hidden;">
                                <p class="file-chosen-name" id="chosenName"></p>
                                <p class="file-chosen-size" id="chosenSize"></p>
                            </div>
                            <button class="file-remove" id="fileRemove" type="button">✕</button>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div id="uploadPreviewContainer" style="display:none; margin-bottom:14px;">
                        <img id="uploadPreviewImage" src="" alt="Preview"
                            style="width:100%; max-height:260px; object-fit:contain; border:1px solid #e5e7eb; border-radius:8px;" />
                    </div>

                    <div class="modal-footer-right">
                        <button class="btn btn-gray" onclick="closeUploadProofModal()">Cancel</button>
                        <button class="btn btn-success" onclick="submitProofOfPayment()">Upload</button>
                    </div>
                </div>
            </div>


            <!-- QR Code Modal -->
            <div class="qr-modal-overlay" id="qrModalOverlay" onclick="closeQRModal(event)">
                <div class="qr-modal">
                    <H5>EMPLOYEES CAN ONLY CHANGE OR UPLOAD THEIR BANK QR CODE. NOT YOU.😡😤</H5><BR></BR>

                    <button class="qr-modal-close" onclick="closeQRModal()">&times;</button>
                    <img id="qrImage" src="" alt="Bank QR Code" />
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script src="js/jquery.js"></script>
            <script src="js/view.js"></script>
            <script src="js/proof-of-payment.js"></script>



</body>

</html>