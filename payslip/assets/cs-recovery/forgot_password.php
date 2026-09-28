<?php

/**
 * forgot_password.php
 * Uses PHPMailer + Gmail SMTP for reliable email sending.
 * Place in: assets/cs-recovery/
 */

header('Content-Type: application/json');

// Load PHPMailer (3 files lang, no Composer needed)

require_once 'vendor/phpmailer/phpmailer/src/Exception.php';
require_once 'vendor/phpmailer/phpmailer/src/SMTP.php';
require_once 'vendor/phpmailer/phpmailer/src/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load DB connection
require_once '../employee/process/connection.php'; // adjust path kung kailangan — $conn (mysqli)

// ── Gmail SMTP Config — PALITAN ITO ───────────────────────────────
$gmail_address  = 'hrcasasanluis@gmail.com';     // ← Gmail mo
$gmail_app_pass = 'eugl jsnq kynw vyz';     // ← 16-char App Password (may spaces ok)
$sender_name    = 'Casas San Luis & Co — Payslip System';
// ─────────────────────────────────────────────────────────────────

$identifier = trim($_POST['identifier'] ?? '');

if ($identifier === '') {
  echo json_encode(['success' => false, 'message' => 'No identifier provided.']);
  exit;
}

// ── Find employee ─────────────────────────────────────────────────
$stmt = $conn->prepare(
  "SELECT id, email FROM employees
     WHERE empNo = ? OR email = ?
     LIMIT 1"
);
$stmt->bind_param('ss', $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();
$emp    = $result->fetch_assoc();
$stmt->close();

if (!$emp) {
  echo json_encode(['success' => false, 'message' => 'No account found in the system.']);
  exit;
}

$empId    = (int) $emp['id'];
$empEmail = $emp['email'];

// ── Check password_resets table ───────────────────────────────────
$stmt = $conn->prepare(
  "SELECT id, reset_count FROM password_resets WHERE emp_id = ? LIMIT 1"
);
$stmt->bind_param('i', $empId);
$stmt->execute();
$result   = $stmt->get_result();
$resetRow = $result->fetch_assoc();
$stmt->close();

$isFirstTime = ($resetRow === null);

// ── Generate password ─────────────────────────────────────────────
$newPassword    = $isFirstTime ? 'pogiangsupervisor' : generateRandomPassword(10);
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

// ── Save hashed password to employees ────────────────────────────
$stmt = $conn->prepare("UPDATE employees SET password = ? WHERE id = ?");
$stmt->bind_param('si', $hashedPassword, $empId);
$stmt->execute();
$stmt->close();

// ── Upsert password_resets ────────────────────────────────────────
if ($isFirstTime) {
  $stmt = $conn->prepare(
    "INSERT INTO password_resets (emp_id, reset_count, last_reset_at)
         VALUES (?, 1, NOW())"
  );
  $stmt->bind_param('i', $empId);
  $stmt->execute();
  $stmt->close();
} else {
  $stmt = $conn->prepare(
    "UPDATE password_resets
         SET reset_count = reset_count + 1, last_reset_at = NOW()
         WHERE emp_id = ?"
  );
  $stmt->bind_param('i', $empId);
  $stmt->execute();
  $stmt->close();
}

// ── Send email via PHPMailer ──────────────────────────────────────
try {
  $mail = new PHPMailer(true);

  // SMTP settings
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = $gmail_address;
  $mail->Password   = $gmail_app_pass;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587;

  // Sender & recipient
  $mail->setFrom($gmail_address, $sender_name);
  $mail->addAddress($empEmail);

  // Content
  $mail->isHTML(true);
  $mail->CharSet = 'UTF-8';
  $mail->Subject = 'Your New Password — Casas San Luis & Co Payslip System';
  $mail->Body    = buildEmailBody($newPassword, $isFirstTime);
  $mail->AltBody = "Your new password is: $newPassword\n\n"
    . ($isFirstTime
      ? "This is your first-time reset password. Please change it after logging in."
      : "This is a randomly generated password. Please keep it safe.");

  $mail->send();

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  // Password was already changed — log error, still return success
  // so the user knows to check email (or admin can check logs)
  error_log('[ForgotPassword] Mailer error for emp_id=' . $empId . ': ' . $mail->ErrorInfo);
  echo json_encode([
    'success' => false,
    'message' => 'Password was reset but email failed to send. Please contact your administrator.'
  ]);
}


// ═════════════════════════════════════════════════════════════════
// Helpers
// ═════════════════════════════════════════════════════════════════

function generateRandomPassword(int $length = 10): string
{
  $chars    = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#$';
  $password = '';
  $max      = strlen($chars) - 1;
  for ($i = 0; $i < $length; $i++) {
    $password .= $chars[random_int(0, $max)];
  }
  return $password;
}

function buildEmailBody(string $password, bool $isFirst): string
{
  $note = $isFirst
    ? '<p style="color:#e07b00;font-size:13px;margin:0 0 8px;">
               ⚠️ This is your first-time reset password.
               Please change it after logging in for your security.
           </p>'
    : '<p style="color:#294169;font-size:13px;margin:0 0 8px;">
               This is a randomly generated password. Keep it safe and update it after login.
           </p>';

  return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f0f4fb;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;">
    <tr><td align="center">
      <table width="480" cellpadding="0" cellspacing="0"
             style="background:#fff;border-radius:16px;overflow:hidden;
                    box-shadow:0 8px 32px rgba(41,65,105,.12);">

        <tr><td style="background:linear-gradient(90deg,#294169,#4281e7);padding:28px 36px;">
          <h2 style="margin:0;color:#fff;font-size:20px;font-weight:700;">Casas San Luis &amp; Co</h2>
          <p style="margin:4px 0 0;color:rgba(255,255,255,.75);font-size:13px;">Payslip   System</p>
        </td></tr>

        <tr><td style="padding:32px 36px;">
          <p style="color:#1a2a44;font-size:15px;margin:0 0 10px;">Hello,</p>
          <p style="color:#4a607e;font-size:14px;line-height:1.6;margin:0 0 22px;">
            A password reset was requested for your account. Here is your new temporary password:
          </p>
          <div style="background:#f0f4fb;border:1.5px solid #d8e3f0;border-radius:10px;
                      padding:18px 24px;text-align:center;margin-bottom:18px;">
            <span style="font-size:24px;font-weight:700;letter-spacing:3px;
                         color:#294169;font-family:monospace;">
              {$password}
            </span>
          </div>
          {$note}
          <p style="color:#94aac4;font-size:12px;margin:24px 0 0;">
            If you did not request this reset, please contact your administrator immediately.
          </p>
        </td></tr>

        <tr><td style="background:#f8fafd;padding:16px 36px;border-top:1px solid #e8eff8;">
          <p style="color:#94aac4;font-size:11px;margin:0;text-align:center;">
            Casas San Luis &amp; Co Payslip System &copy; 2023 &nbsp;·&nbsp; V 1.0.0.1
          </p>
        </td></tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}
