<?php include 'welcome_modal.php';

$fullName  = $_SESSION['EmpName'] ?? $_SESSION['empName'] ?? 'Employee';
$firstName = explode(' ', trim($fullName))[0];
$empNo     = $_SESSION['empNo'] ?? $_SESSION['EmpNo'] ?? ''; // ✅ idagdag ito
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
    <link rel="icon" href="../../img/cs.png">

</head>

<body>


    <!-- ── Sidebar ────────────────────────────────────────────────────────────── -->
    <div class="sidebar" id="sidebar">
        <div class="topbar">
            <a href="index.php">
                <img src="../../img/cslogos.png" alt="Logo" class="logo">
            </a>
            <h2 class="compname">Casas San Luis Payroll</h2>
        </div>

        <ul class="list">
            <li onclick="location.href='index.php'" style="cursor:pointer;">
                👤<span> Employee</span>
            </li>
            <li onclick="location.href='summary.php?id=<?= htmlspecialchars($empNo) ?>'" style="cursor:pointer;">
                🧾<span> Summary</span>
            </li>

            <li onclick="location.href='settings/settings.php?id=<?= htmlspecialchars($empNo) ?>'" style="cursor:pointer;">
                ⚙️<span> Settings</span>
            </li>
        </ul>

        <!-- Logout button pinned to bottom of sidebar -->
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
            <h3 class="nav">Dashboard</h3>
            <!-- Uses session name set during login -->
            <div class="user" style="text-transform: capitalize;">Welcome <?= htmlspecialchars($fullName) ?></div>
        </div>

        <div class=" content">

            <!-- TOP BUTTONS -->
            <div class="top-buttons">
                <button class="addemp" onclick="addemp()">Add Employee</button>
                <button id="viewAllBtn">View All Employee</button>
                <button onclick="create()">Create Payslip</button>
            </div>

            <!-- TABLE -->
            <table>
                <thead>
                    <tr>
                        <th> </th>
                        <th>Employee No</th>
                        <th>Employee Name</th>
                        <th>Designation</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once "process/connection.php";

                    $sql = "SELECT * FROM employees WHERE status = 'employed' ORDER BY empNo ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row['image'])) { ?>
                                        <img src="process/uploads/<?php echo htmlspecialchars($row['image']); ?>" width="50" height="50" style="border-radius:50%;">
                                    <?php } else { ?>
                                        <span>No Image</span>
                                    <?php } ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['empNo']); ?></td>
                                <td style="text-transform: uppercase;"><?php echo htmlspecialchars($row['empName']); ?></td>
                                <td><?php echo htmlspecialchars($row['designation']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <button class="view-btn" onclick="viewEmp(<?php echo (int)$row['id']; ?>)">View</button>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6'>No employees found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/jquery.js"></script>
    <script src="js/emp.js"></script>
    <script>
        // ── Sidebar toggle ────────────────────────────────────────────────────────────
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        // ── Logout confirmation ───────────────────────────────────────────────────────
        function confirmLogout() {
            const overlay = document.getElementById('logoutOverlay');
            overlay.classList.add('show');
        }

        function cancelLogout() {
            const overlay = document.getElementById('logoutOverlay');
            overlay.classList.remove('show');
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

</body>

</html>