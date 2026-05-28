<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --------------------
// 0. Start session para sa rate limiting
// --------------------
session_start();

// Response helper
function jsonExit($success, $message, $code = 200)
{
  http_response_code($code);
  echo json_encode([
    'success' => $success,
    'message' => $message,
    'code'    => $code
  ]);
  exit;
}
// --------------------
// 1. CAPTCHA Verification
// --------------------
$recaptchaSecret   = "6LdzUGYsAAAAAEaLq4qxUIHT-I-VNM1jE69Nckrw";
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
$userIP            = $_SERVER['REMOTE_ADDR'] ?? '';

if (!$recaptchaResponse) {
  jsonExit(false, 'Please complete the CAPTCHA.', 400);
}

$response     = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}&remoteip={$userIP}");
$responseData = json_decode($response);

if (!$responseData->success) {
  jsonExit(false, 'CAPTCHA verification failed. Please try again.', 400);
}

// --------------------
// 2. Layer 2: Session-based rate limit (1 send bawat 60 segundo)
// --------------------
$now = time();
$cooldown = 60; // seconds

if (isset($_SESSION['last_contact_sent'])) {
  $elapsed = $now - $_SESSION['last_contact_sent'];
  if ($elapsed < $cooldown) {
    $wait = $cooldown - $elapsed;
    jsonExit(false, "Please wait {$wait} seconds before sending another message.", 429);
  }
}

// --------------------
// 3. Sanitize & Validate
// --------------------
function sanitize($data)
{
  return htmlspecialchars(strip_tags(trim($data)));
}

$name    = sanitize($_POST['name']    ?? '');
$email   = sanitize($_POST['email']   ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$message = sanitize($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
  jsonExit(false, 'All fields are required.', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  jsonExit(false, 'Invalid email address.', 400);
}

// --------------------
// 4. Layer 3: Database rate limit (max 3 bawat email bawat oras)
// --------------------
// --------------------
// 4. Layer 3: Database rate limit (max 3 bawat email bawat oras)
// --------------------
try {
  $limitCheck = $pdo->prepare("
        SELECT COUNT(*) FROM contact_messages
        WHERE email = :email
          AND submitted_at >= NOW() - INTERVAL 1 HOUR
    ");
  $limitCheck->execute([':email' => $email]);
  $count = (int) $limitCheck->fetchColumn();

  if ($count >= 3) {
    jsonExit(false, 'Too many messages from this email. Please try again later.', 429);
  }
} catch (PDOException $e) {
  error_log($e->getMessage());
  jsonExit(false, 'Server error. Please try again.', 500);
}

// --------------------
// 5. Save to database
// --------------------
try {
  $stmt = $pdo->prepare("
        INSERT INTO contact_messages (name, email, subject, message)
        VALUES (:name, :email, :subject, :message)
    ");
  $stmt->execute([
    ':name'    => $name,
    ':email'   => $email,
    ':subject' => $subject,
    ':message' => $message,
  ]);
} catch (PDOException $e) {
  error_log($e->getMessage());
  jsonExit(false, 'Database error.', 500);
}

// Itala ang oras ng huling pagpapadala sa session
$_SESSION['last_contact_sent'] = $now;

// --------------------
// 6. Send Email
// --------------------
$mail = new PHPMailer(true);
try {
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'web.casassanluis@gmail.com';
  $mail->Password   = 'ngfl xypp xkio zpwg';
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587;

  $mail->setFrom($email, $name . ' via Contact Form');
  $mail->addAddress('web.casassanluis@gmail.com');
  $mail->addReplyTo($email, $name);
  $mail->addEmbeddedImage(__DIR__ . '/CSL&CO.png', 'company_logo');
  $mail->isHTML(true);
  $mail->Subject = "Inquiry From $name";

  $mail->Body = '
<!doctype html><html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;background:#f4f6f8;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:6px;overflow:hidden;">
<tr><td style="background:#0f172a;padding:20px;text-align:center;">
  <img src="cid:company_logo" alt="Logo" style="max-height:60px"/>
  <h2 style="color:#fff;margin:10px 0 0">CASAS SAN LUIS &amp; CO.</h2>
</td></tr>
<tr><td style="padding:25px">
  <h3 style="margin-top:0;color:#111827">New Contact Form Message</h3>
  <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;font-size:14px">
    <tr><td style="background:#f9fafb;border:1px solid #e5e7eb;font-weight:bold;width:120px;">Name</td><td style="border:1px solid #e5e7eb">' . $name . '</td></tr>
    <tr><td style="background:#f9fafb;border:1px solid #e5e7eb;font-weight:bold;">Email</td><td style="border:1px solid #e5e7eb">' . $email . '</td></tr>
    <tr><td style="background:#f9fafb;border:1px solid #e5e7eb;font-weight:bold;">Subject</td><td style="border:1px solid #e5e7eb">' . $subject . '</td></tr>
    <tr><td style="background:#f9fafb;border:1px solid #e5e7eb;font-weight:bold;vertical-align:top;">Message</td><td style="border:1px solid #e5e7eb">' . $message . '</td></tr>
  </table>
</td></tr>
<tr><td style="background:#f9fafb;padding:15px;text-align:center;font-size:12px;color:#6b7280;">
  Sent from your website contact form.<br/>&copy; ' . date('Y') . ' Casas San Luis &amp; Co
</td></tr>
</table>
</td></tr></table>
</body></html>';

  $mail->send();
  jsonExit(true, 'Message sent successfully!');
} catch (Exception $e) {
  jsonExit(true, 'Message saved but email failed: ' . $mail->ErrorInfo);
}
