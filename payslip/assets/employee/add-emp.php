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
    <link rel="stylesheet" href="css/add-emp.css">
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

    <div class="main">
        <div class="navbar">
            <button onclick="toggleSidebar()">☰</button>
            <h3 class="nav">Employee </h3>
            <div class="user">Welcome, <?= htmlspecialchars($fullName) ?></div>
        </div>

        <div class="content">
            <div class="add-form">
                <h3>Add New Employee</h3>
                <form action="process/addemp.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div>
                        <label>Employee No</label>
                        <input type="number" name="empNo" id="empNo" required>
                        <small id="empError" style="color:red; display:none;">Employee No already exists</small>
                    </div>

                    <div>
                        <label>Employee Name</label>
                        <input type="text" name="empName" placeholder=" LAST NAME, FIRSTNAME M.I" required>
                    </div>

                    <div>
                        <label>Designation</label>
                        <input type="text" name="designation" required>
                    </div>

                    <div>
                        <label>Email</label>
                        <input type="email" name="email" placeholder="@Gmail.com">
                    </div>

                    <div>
                        <label>Salary</label>
                        <input type="text" name="salary" id="salary" placeholder="₱ 0.00" required>
                    </div>


                    <div>
                        <label>TIN</label>
                        <input type="text" name="tinNo" id="tinNo" maxlength="17" placeholder="123-123-123-00000 - 14 Digits">
                        <small id="tinMsg"></small>
                    </div>

                    <div>
                        <label>SSS</label>
                        <input type="text" name="sssNo" id="sssNo" maxlength="12" placeholder="12-3456789-0 - 12 Digits">
                        <small id="sssMsg"></small>
                    </div>

                    <div>
                        <label>PhilHealth</label>
                        <input type="text" name="philHealthNo" id="philHealthNo" maxlength="14" placeholder="12-345678901-2 - 14 Digits">
                        <small id="philMsg"></small>
                    </div>

                    <div>
                        <label>Pag-IBIG</label>
                        <input type="text" name="pagIbigNo" id="pagIbigNo" maxlength="14" placeholder="1234-5678-9012 - 12 Digits">
                        <small id="pagibigMsg"></small>
                    </div>


                    <div class="file-input">
                        <label>Employee Image</label><br>

                        <label for="imageUpload" class="file-label">
                            Choose Image
                        </label>

                        <input type="file" id="imageUpload" name="image" accept="image/*">

                        <!-- IMAGE PREVIEW -->
                        <div class="image-preview">
                            <img id="previewImg" src="" alt="Preview">
                        </div>
                    </div>
                    <div class="button-row">
                        <button type="submit" id="submitBtn" disabled>Submit</button>
                        <button type="button" onclick="ClearForm()">Clear</button>
                    </div>
                </form>
            </div>
        </div>


        <script src="js/add-emp.js"></script>

        <!-- for modal /  -->
        <div id="successModal" class="modal">
            <div class="modal-content">
                <div class="icon">✔</div>
                <h2>Success!</h2>
                <p>Employee has been added successfully.</p>

                <div class="button-row">
                    <button class="btn add" onclick="addMore()">Add More Employee</button>
                    <button class="btn done" onclick="goDone()">Done</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("collapsed");
        }

        function addemp() {
            window.location.href = "add-emp.php";
        }
    </script>
    <!-- jQuery 3.x from jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
</body>

</html>