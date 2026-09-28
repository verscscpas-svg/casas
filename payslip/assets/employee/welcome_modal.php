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
$fullName  = $_SESSION['EmpName'] ?? $_SESSION['empName'] ?? 'Employee';
$firstName = explode(' ', trim($fullName))[0];
// FIX: EmpName vs empName issue
$fullName  = $_SESSION['EmpName'] ?? $_SESSION['empName'] ?? 'Employee';

// FIX: first name still works
$firstName = explode(' ', trim($fullName))[0];
// empNo (unchanged but safe fallback added)
$empNo = $_SESSION['empNo'] ?? '';

// FIX: Role / designation mismatch
$role = $_SESSION['Role'] ?? $_SESSION['designation'] ?? 'Employee';
$role = ucfirst($role);

// last login unchanged
$lastLogin = $_SESSION['last_login'] ?? date('M d, Y g:i A');

date_default_timezone_set('Asia/Manila');

function getGreeting()
{
    $h = (int) date('G');

    if ($h < 12) return 'morning';
    if ($h < 17) return 'afternoon';
    return 'evening';
}
?>

<!-- ✅ FAVICON ADDED -->
<link rel="shortcut icon" href="../../img/cslogos.png" type="image/x-icon">

<!-- IMPORTANT: display:none — JS controls visibility -->
<div id="welcomeOverlay" style="
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,0.45);
    align-items:center; justify-content:center;
">
    <div style="
        background:#fff; border-radius:20px;
        padding:2.25rem 2.5rem;
        max-width:420px; width:90%;
        text-align:center;
        box-shadow:0 24px 60px rgba(41,65,105,.18);
        animation:slideUp .35s cubic-bezier(.34,1.56,.64,1) both;
    ">
        <div style="
            width:68px; height:68px; border-radius:50%;
            background:#e8f5ec;
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 1.25rem;
        ">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none"
                stroke="#2e7d5c" stroke-width="2.2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
        </div>

        <p style="font-size:13px;color:#6b84aa;margin:0 0 4px;letter-spacing:.06em;text-transform:uppercase;">Welcome back</p>

        <h2 style="font-size:22px;font-weight:700;margin:0 0 .5rem;color:#1a2a44;font-family:'Playfair Display',Georgia,serif;">
            Good <?= getGreeting() ?>, <?= htmlspecialchars($firstName) ?>!
        </h2>

        <p style="font-size:14px;color:#6b84aa;margin:0 0 1.75rem;line-height:1.6;">
            Signed in as Employee No# <strong style="color:#1a2a44;"><?= htmlspecialchars($empNo) ?></strong>.
            Your payroll dashboard is ready.
        </p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:1.5rem;">
            <div style="background:#f5f7fb;border-radius:10px;padding:12px;">
                <p style="font-size:11px;color:#6b84aa;margin:0 0 4px;text-transform:uppercase;letter-spacing:.05em;">Role</p>
                <p style="font-size:14px;font-weight:600;color:#1a2a44;margin:0;">
                    <?= htmlspecialchars($role) ?>
                </p>
            </div>

            <div style="background:#f5f7fb;border-radius:10px;padding:12px;">
                <p style="font-size:11px;color:#6b84aa;margin:0 0 4px;text-transform:uppercase;letter-spacing:.05em;">Last login (Please Note that this is not 100% Accurate)</p>
                <p style="font-size:14px;font-weight:600;color:#1a2a44;margin:0;">
                    <?= htmlspecialchars($lastLogin) ?>
                </p>
            </div>
        </div>

        <button onclick="closeWelcome()" style="
            width:100%;padding:11px;
            font-size:14px;font-weight:600;
            background:linear-gradient(135deg,#294169,#4281e7);
            color:#fff;border:none;border-radius:10px;cursor:pointer;
            transition:opacity .2s;
        " onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
            Go to dashboard →
        </button>

        <p style="font-size:12px;color:#aab4c8;margin:1rem 0 0;">
            Closing automatically in <span id="wCountdown">10</span>s
        </p>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0
        }

        to {
            opacity: 1
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0
        }

        to {
            transform: translateY(0);
            opacity: 1
        }
    }

    @keyframes popIn {
        from {
            transform: scale(.5);
            opacity: 0
        }

        to {
            transform: scale(1);
            opacity: 1
        }
    }

    #welcomeOverlay.hide {
        animation: fadeOut .3s ease forwards;
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            pointer-events: none
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        var key = 'welcomed_<?= addslashes($empNo) ?>';
        var overlay = document.getElementById('welcomeOverlay');

        if (!sessionStorage.getItem(key)) {

            sessionStorage.setItem(key, '1');

            overlay.style.display = 'flex';
            overlay.style.animation = 'fadeIn .25s ease both';

            var s = 10;
            var el = document.getElementById('wCountdown');

            var iv = setInterval(function() {
                s--;
                if (el) el.textContent = s;

                if (s <= 0) {
                    clearInterval(iv);
                    closeWelcome();
                }
            }, 1000);
        }
    });

    function closeWelcome() {
        var o = document.getElementById('welcomeOverlay');
        if (!o) return;

        o.classList.add('hide');

        setTimeout(function() {
            o.style.display = 'none';
        }, 300);
    }
</script>