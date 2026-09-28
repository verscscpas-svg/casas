<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payslip System — Casas San Luis & Co</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="foremp/style.css">
    <link rel="stylesheet" href="../cs-recovery/forgot_password.css">
    <link rel="shortcut icon" href="img/cslogos.png" type="image/x-icon">
</head>

<body>

    <!-- background -->
    <div class="bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="geo"></div>
        <div class="stripe"></div>
    </div>

    <!-- layout -->
    <div class="stage">

        <!-- LEFT -->
        <div class="left-panel">
            <div class="company-badge">
                <div class="badge-logo">
                    <img src="img/cs.png" width="50" height="50"
                        style="object-fit:contain; border-radius:8px;"
                        alt="Casas San Luis logo" />
                </div>
                <div class="badge-text">
                    <div class="badge-company">Casas San Luis &amp; Co</div>
                    <div class="badge-type">Payslip Management System</div>
                </div>
            </div>

            <h1 class="hero-title">Smart Payslip,<br /><em>Seamless</em><br />Operations.</h1>
            <p class="hero-desc">Streamline employee compensation, automate deductions, and generate reports — all in one secure platform built for your team.</p>

            <div class="stats">
                <div class="stat">
                    <div class="stat-num">100%</div>
                    <div class="stat-label">Accurate</div>
                </div>
                <div class="stat">
                    <div class="stat-num">256-bit</div>
                    <div class="stat-label">Encrypted</div>
                </div>
                <div class="stat">
                    <div class="stat-num">24 / 7</div>
                    <div class="stat-label">Accessible</div>
                </div>
            </div>
        </div>

        <!-- ILLUSTRATION -->
        <div class="illustration">
            <svg viewBox="0 0 440 320" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- desk surface -->
                <ellipse cx="220" cy="298" rx="200" ry="18" fill="rgba(0,0,0,.15)" />

                <!-- monitor stand -->
                <rect x="208" y="240" width="24" height="30" rx="3" fill="rgba(255,255,255,.12)" />
                <rect x="188" y="268" width="64" height="8" rx="4" fill="rgba(255,255,255,.15)" />

                <!-- monitor body -->
                <rect x="80" y="60" width="280" height="185" rx="16" fill="rgba(41,65,105,.7)" stroke="rgba(255,255,255,.15)" stroke-width="1.5" />
                <rect x="88" y="68" width="264" height="169" rx="10" fill="rgba(26,42,68,.9)" />

                <!-- screen glow -->
                <rect x="88" y="68" width="264" height="169" rx="10" fill="url(#screenGlow)" opacity=".6" />

                <!-- screen content: header bar -->
                <rect x="96" y="76" width="248" height="22" rx="4" fill="rgba(66,129,231,.25)" />
                <circle cx="107" cy="87" r="4" fill="rgba(255,255,255,.2)" />
                <rect x="118" y="83" width="80" height="7" rx="3" fill="rgba(255,255,255,.3)" />
                <rect x="310" y="83" width="28" height="7" rx="3" fill="rgba(66,129,231,.5)" />

                <!-- table rows -->
                <rect x="96" y="106" width="248" height="18" rx="3" fill="rgba(255,255,255,.04)" />
                <rect x="96" y="128" width="248" height="18" rx="3" fill="rgba(66,129,231,.07)" />
                <rect x="96" y="150" width="248" height="18" rx="3" fill="rgba(255,255,255,.04)" />
                <rect x="96" y="172" width="248" height="18" rx="3" fill="rgba(66,129,231,.07)" />
                <rect x="96" y="194" width="248" height="18" rx="3" fill="rgba(255,255,255,.04)" />
                <rect x="96" y="216" width="248" height="16" rx="3" fill="rgba(66,129,231,.07)" />

                <!-- row content lines -->
                <rect x="104" y="111" width="40" height="6" rx="2" fill="rgba(255,255,255,.3)" />
                <rect x="104" y="133" width="52" height="6" rx="2" fill="rgba(255,255,255,.3)" />
                <rect x="104" y="155" width="36" height="6" rx="2" fill="rgba(255,255,255,.3)" />
                <rect x="104" y="177" width="48" height="6" rx="2" fill="rgba(255,255,255,.3)" />
                <rect x="104" y="199" width="42" height="6" rx="2" fill="rgba(255,255,255,.3)" />
                <rect x="104" y="221" width="38" height="6" rx="2" fill="rgba(255,255,255,.3)" />

                <!-- salary columns -->
                <rect x="270" y="111" width="50" height="6" rx="2" fill="rgba(66,129,231,.7)" />
                <rect x="270" y="133" width="44" height="6" rx="2" fill="rgba(66,129,231,.7)" />
                <rect x="270" y="155" width="56" height="6" rx="2" fill="rgba(66,129,231,.7)" />
                <rect x="270" y="177" width="40" height="6" rx="2" fill="rgba(66,129,231,.7)" />
                <rect x="270" y="199" width="48" height="6" rx="2" fill="rgba(66,129,231,.7)" />
                <rect x="270" y="221" width="52" height="6" rx="2" fill="rgba(66,129,231,.7)" />

                <!-- status pills -->
                <rect x="320" y="109" width="20" height="10" rx="5" fill="rgba(52,199,89,.4)" />
                <rect x="320" y="131" width="20" height="10" rx="5" fill="rgba(52,199,89,.4)" />
                <rect x="320" y="153" width="20" height="10" rx="5" fill="rgba(255,204,0,.3)" />
                <rect x="320" y="175" width="20" height="10" rx="5" fill="rgba(52,199,89,.4)" />
                <rect x="320" y="197" width="20" height="10" rx="5" fill="rgba(52,199,89,.4)" />
                <rect x="320" y="219" width="20" height="10" rx="5" fill="rgba(255,149,0,.3)" />

                <!-- floating card: total Payslip -->
                <g filter="url(#cardShadow)" opacity="0.95">
                    <rect x="290" y="38" width="140" height="66" rx="12" fill="white" />
                    <rect x="290" y="38" width="140" height="4" rx="2" fill="url(#btnGrad)" />
                    <text x="302" y="60" font-size="9" fill="#6b84aa" font-family="sans-serif" letter-spacing="1">TOTAL Payslip</text>
                    <text x="302" y="78" font-size="18" fill="#1a2a44" font-family="Georgia,serif" font-weight="700">₱#####</text>
                    <text x="302" y="92" font-size="8" fill="#4281e7" font-family="sans-serif">▲ 3.2% this month</text>
                </g>

                <!-- floating card: employees -->
                <g filter="url(#cardShadow)" opacity="0.92">
                    <rect x="14" y="100" width="120" height="60" rx="12" fill="white" />
                    <rect x="14" y="100" width="120" height="4" rx="2" fill="url(#btnGrad)" />
                    <text x="26" y="121" font-size="9" fill="#6b84aa" font-family="sans-serif" letter-spacing="1">EMPLOYEES</text>
                    <text x="26" y="142" font-size="22" fill="#1a2a44" font-family="Georgia,serif" font-weight="700">8</text>
                    <text x="68" y="142" font-size="8" fill="#4281e7" font-family="sans-serif">Active</text>
                </g>

                <!-- defs -->
                <defs>
                    <linearGradient id="screenGlow" x1="88" y1="68" x2="88" y2="237" gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#4281e7" stop-opacity=".15" />
                        <stop offset="1" stop-color="#294169" stop-opacity="0" />
                    </linearGradient>
                    <linearGradient id="btnGrad" x1="0" y1="0" x2="140" y2="0" gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#294169" />
                        <stop offset="1" stop-color="#4281e7" />
                    </linearGradient>
                    <filter id="cardShadow" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="6" stdDeviation="8" flood-color="#294169" flood-opacity=".18" />
                    </filter>
                </defs>
            </svg>
        </div>

        <!-- RIGHT (card) -->
        <div class="right-panel">
            <div class="card">
                <div class="card-accent"></div>

                <div class="card-logo-row">
                    <div class="card-logo">
                        <img src="img/cs.png" width="28" height="28" class="cardlogo"
                            style="object-fit:contain; border-radius:6px;"
                            alt="Casas San Luis logo" />
                    </div>
                    <div>
                        <div class="card-company-name"> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Casas San Luis &amp; Co</div>
                        <div class="card-company-sub">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Payslip System</div>
                    </div>
                </div>

                <h2 class="card-heading">Welcome back</h2>
                <p class="card-sub">Sign in to access your Payslip dashboard</p>

                <div class="form-group">
                    <label>Employee ID / Email</label>
                    <div class="input-wrap">
                        <input type="text" id="user" placeholder="e.g. EMP-0012 or cscpas@gmail.com" autocomplete="off" spellcheck="false" />
                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap" style="position:relative;">
                        <input type="password" id="pass" placeholder="Enter your password" style="padding-right: 44px;" />
                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <!-- toggle button -->
                        <button type="button" id="togglePass"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
             background:none; border:none; cursor:pointer; padding:0;
             display:flex; align-items:center; color:#6b84aa;">
                            <!-- eye icon (show) -->
                            <svg id="eyeShow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <!-- eye-off icon (hide) — hidden by default -->
                            <svg id="eyeHide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                </div>



                <div class="forgot-row">
                    <a href="#" id="forgotPasswordLink">Forgot password?</a>
                </div>

                <button class="btn" id="loginBtn">
                    <span class="btn-text">Sign In to Dashboard</span>
                    <div class="btn-loader">
                        <span></span><span></span><span></span>
                    </div>
                </button>

                <div class="divider"><span>secured by</span></div>

                <div class="secure-note">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    V 1.0.0.1 &nbsp;·&nbsp; Casas San Luis &amp; Co © 2023
                </div>
            </div>
        </div>
    </div>

    <!-- success -->
    <div class="success-overlay" id="successOverlay">
        <div class="success-check">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </div>
        <div class="success-title">Access Granted</div>
        <div class="success-msg">Loading your Payslip dashboard…</div>
    </div>
    <!-- =====================================================================
     FORGOT PASSWORD MODAL
     Paste this entire block just before </body> in your index.php
     (right after your other content, before the <script> tags)
     ===================================================================== -->

    <div class="fp-overlay" id="fpOverlay">
        <div class="fp-modal" id="fpModal">

            <!-- Accent bar -->
            <div class="fp-accent"></div>

            <!-- Close button -->
            <button class="fp-close" id="fpClose" aria-label="Close">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>

            <!-- ─── STEP 1: Enter ID / Email ─── -->
            <div class="fp-step" id="fpStep1">

                <div class="fp-icon-wrap">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        <circle cx="12" cy="16" r="1" fill="currentColor" />
                    </svg>
                </div>

                <h3 class="fp-title">Forgot Password?</h3>
                <p class="fp-desc">
                    Enter your Employee ID or registered email address and
                    we'll send a new password directly to your email.
                </p>

                <div class="fp-field">
                    <label for="fpIdentifier">Employee ID or Email</label>
                    <div class="fp-input-wrap">
                        <input
                            type="text"
                            id="fpIdentifier"
                            placeholder="e.g. EMP-0012 or you@email.com"
                            autocomplete="off"
                            spellcheck="false" />
                        <svg class="fp-input-icon" width="15" height="15"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                    <!-- Live feedback message -->
                    <div class="fp-feedback" id="fpFeedback"></div>
                </div>

                <button class="fp-btn" id="fpSendBtn">
                    <span class="fp-btn-text">Send New Password</span>
                    <div class="fp-btn-loader">
                        <span></span><span></span><span></span>
                    </div>
                </button>

            </div>
            <!-- ─── END STEP 1 ─── -->

            <!-- ─── STEP 2: Success ─── -->
            <div class="fp-step fp-step-hidden" id="fpStep2">

                <div class="fp-success-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="white" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>

                <h3 class="fp-title">Password Sent!</h3>
                <p class="fp-desc">
                    A new password has been sent to your registered email address.<br />
                    Please check your inbox and use it to log in.
                </p>

                <button class="fp-btn" id="fpDoneBtn">Back to Login</button>

            </div>
            <!-- ─── END STEP 2 ─── -->

        </div>
    </div>
    <script src="foremp/jquery.js"></script>
    <script src="foremp/login_process.js"></script>
    <script src="../cs-recovery-emp/forgot_password.js"></script>
</body>

</html>