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

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM employees WHERE id='$id'");
$row = mysqli_fetch_assoc($result);


$statusQuery = mysqli_query($conn, "SELECT * FROM employees");


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
    <link rel="stylesheet" href="css/upd-emp.css">
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
            <div class="add-form">
                <h3>Update Employee</h3>

                <form action="process/update-emp.php" method="POST" enctype="multipart/form-data" autocomplete="off">

                    <!-- Hidden ID -->
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">


                    <div class="status-group">
                        <label>Status</label>
                        <select name="Stats" required>
                            <option value="Employed" <?= ($row['status'] == "Employed") ? "selected" : ""; ?>>Employed</option>
                            <option value="Resigned" <?= ($row['status'] == "Resigned") ? "selected" : ""; ?>>Resigned</option>
                        </select>
                    </div>
                    <div>
                        <label>Employee No</label>
                        <input type="number" name="empNo" value="<?php echo $row['empNo']; ?>" required>
                    </div>

                    <div>
                        <label>Employee Name</label>
                        <input type="text" name="empName" value="<?php echo $row['empName']; ?>" required>
                    </div>

                    <div>
                        <label>Designation</label>
                        <input type="text" name="designation" value="<?php echo $row['designation']; ?>" required>
                    </div>

                    <div>
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo $row['email']; ?>">
                    </div>

                    <div>
                        <label>Salary</label>
                        <input type="text" name="salary" value="<?php echo $row['salary']; ?>" required>
                    </div>

                    <div>
                        <label>TIN</label>
                        <input type="text" name="tinNo" id="tinNo" maxlength="17"
                            value="<?php echo $row['tinNo']; ?>">
                        <small id="tinMsg"></small>
                    </div>

                    <div>
                        <label>SSS</label>
                        <input type="text" name="sssNo" id="sssNo" maxlength="12"
                            value="<?php echo $row['sssNo']; ?>">
                        <small id="sssMsg"></small>
                    </div>

                    <div>
                        <label>PhilHealth</label>
                        <input type="text" name="philHealthNo" id="philHealthNo" maxlength="14"
                            value="<?php echo $row['philHealthNo']; ?>">
                        <small id="philMsg"></small>
                    </div>

                    <div>
                        <label>Pag-IBIG</label>
                        <input type="text" name="pagIbigNo" id="pagIbigNo" maxlength="14"
                            value="<?php echo $row['pagIbigNo']; ?>">
                        <small id="pagibigMsg"></small>
                    </div>


                    <!-- CURRENT IMAGE -->
                    <div class="file-input">
                        <label>Current Image</label><br>

                        <!-- CLICKABLE IMAGE -->
                        <label for="imageUpload">
                            <img id="previewImg"
                                src="process/uploads/<?php echo $row['image']; ?>"
                                width="120"
                                style="cursor:pointer; border:1px solid #ccc; padding:5px;">
                        </label>

                        <!-- HIDDEN FILE INPUT -->
                        <input type="file" id="imageUpload" name="image" accept="image/*" style="display:none;">
                    </div>

                    <div class="button-row">


                        <div class="button-row">
                            <button type="submit" id="submitBtn">Update</button>
                        </div>
                        <button type="button" onclick="window.history.back()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        <script src="js/jquery.js"></script>
        <script src="js/upd-emp.js"></script>
    </div>

</body>

</html>