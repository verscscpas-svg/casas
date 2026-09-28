<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (empty($_SESSION['logged_in'])) {
  header('Location: ../../index.php');
  exit;
}

$designation = strtolower(trim($_SESSION['Role'] ?? ''));

if ($designation !== 'supervisor') {
  header('Location: ../../index.php');
  exit;
}

include 'process/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
require __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';

header('Content-Type: application/json');

// =========================
// INPUT
// =========================
$html = $_POST['html'] ?? '';
$id   = intval($_POST['id'] ?? 0);

// =========================
// RESPONSE FUNCTION
// =========================
function response($status, $message)
{
  echo json_encode([
    "status"  => $status,
    "message" => $message
  ]);
  exit;
}

if (!$html) {
  response("error", "No payslip data received.");
}

if (!$id) {
  response("error", "No payslip ID received.");
}

// =========================
// GET DATA
// =========================
$res = $conn->query("
  SELECT 
    e.email,
    e.empName,
    e.empNo,
    p.cut_off_start,
    p.cut_off_end,
    p.payroll_date
  FROM payslips p
  JOIN employees e ON p.employee_id = e.id
  WHERE p.id = $id
");

if ($res->num_rows == 0) {
  response("error", "Employee not found.");
}

$row = $res->fetch_assoc();

$email = $row['email'];
$name  = $row['empName'];
$empNo = $row['empNo'];

// =========================
// FORMAT DATES
// =========================
$start       = date("F j", strtotime($row['cut_off_start']));
$end         = date("F j, Y", strtotime($row['cut_off_end']));
$payrollDate = date("Ymd", strtotime($row['payroll_date']));

// =========================
// COMPANY NAME & PDF FILE NAME
// =========================
$company     = "CS_Emp";
$pdfFileName = $company . "_" . $empNo . "_(" . $payrollDate . ").pdf";

// =========================
// CSS — OVERRIDE FONT TO COURIER PRIME (Google Fonts version ng Courier)
// =========================
$css1Path    = __DIR__ . "/css/send.css";
$css1Content = file_exists($css1Path) ? file_get_contents($css1Path) : '';

// OVERRIDE font-family para sure na ma-apply ng PDFShift
$fontOverride = "
  @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap');

  * {
    font-family: 'Courier Prime', 'Courier New', Courier, monospace !important;
  }
";

// =========================
// HTML CONTENT
// =========================
$htmlContent = "
<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <link href='https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&display=swap' rel='stylesheet'>
  <style>
    $fontOverride
    $css1Content
  </style>
</head>
<body>
  <div class='payslip-container'>
    $html
  </div>
  <br><br><br>
  <center>c
    <p class='info'>*This payslip is system generated. No signature required.*</p>
  </center>
</body>
</html>
";

// =========================
// TMP FOLDER
// =========================
$tmpDir = __DIR__ . "/tmp/";

if (!is_dir($tmpDir)) {
  mkdir($tmpDir, 0777, true);
}

if (!is_writable($tmpDir)) {
  response("error", "tmp/ folder is not writable.");
}

// =========================
// FILE PATH
// =========================
$pdfFile = $tmpDir . $pdfFileName;

// =========================
// PDFSHIFT API
// =========================
$apiKey = 'sk_183ee6612f146844eed9f32d70ccdfa0b97b0c71'; // ← API key mo dito

$payload = json_encode([
  'source'    => $htmlContent,
  'landscape' => false,
  'use_print' => true,
  'format'    => 'A4',
  'delay'     => 2000, // ← hintayin ang Google Fonts na mag-load
]);

$ch = curl_init('https://api.pdfshift.io/v3/convert/pdf');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST           => true,
  CURLOPT_POSTFIELDS     => $payload,
  CURLOPT_HTTPHEADER     => [
    'Authorization: Basic ' . base64_encode('api:' . $apiKey),
    'Content-Type: application/json',
  ],
  CURLOPT_TIMEOUT        => 60,
  CURLOPT_CONNECTTIMEOUT => 30,
]);

$pdfContent = curl_exec($ch);
$httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError  = curl_error($ch);
curl_close($ch);

// CHECK PDFSHIFT RESPONSE
if ($httpCode !== 200) {
  response("error", "PDFShift failed. HTTP: $httpCode | Body: " . substr($pdfContent, 0, 500) . " | cURL: $curlError");
}

if (empty($pdfContent)) {
  response("error", "PDFShift returned empty response.");
}

// SAVE PDF
file_put_contents($pdfFile, $pdfContent);

if (!file_exists($pdfFile)) {
  response("error", "PDF file was not saved.");
}

// =========================
// EMAIL
// =========================
$mail = new PHPMailer(true);

try {
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'hrcasasanluis@gmail.com';
  $mail->Password   = 'eugl jsnq kynw vyzs';
  $mail->SMTPSecure = 'tls';
  $mail->Port       = 587;

  $mail->setFrom('hrcasasanluis@gmail.com', 'Casas San Luis Payslip System');
  $mail->addAddress($email, $name);

  $mail->Subject = "PAYSLIP: " . strtoupper($start . " - " . $end . " | " . $name);

  $mail->isHTML(true);
  $mail->CharSet = 'UTF-8';

  $logoPath = __DIR__ . "/img/CSLco.png";
  if (!file_exists($logoPath)) {
    response("error", "Logo not found: " . $logoPath);
  }

  $mail->addEmbeddedImage($logoPath, 'company_logo', 'CSLco.png', 'base64', 'image/png');

  $mail->Body = "
<div style='font-family: Arial, sans-serif; background:#f4f8ff; padding:30px;'>
  <div style='max-width:600px;margin:auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,0.1);'>
    <!-- HEADER -->
    <div style='background:linear-gradient(135deg,#1e88e5,#64b5f6);padding:20px;text-align:center;'>
      <img src='cid:company_logo' style='width:70px;height:70px;border-radius:50%;border:3px solid white;padding:5px;background-color:white;'>
      <h2 style='color:white;margin:10px 0 0;'>CASAS SAN LUIS & CO.</h2>
      <p style='color:#e3f2fd;margin:5px 0;font-size:13px;'>Official Payslip System</p>
    </div>
    <!-- BODY -->
    <div style='padding:25px;color:#333;'>
      <h3 style='color:#1e88e5;margin-bottom:10px;'>PAYSLIP NOTIFICATION</h3>
      <p>Hi <b style='text-transform:uppercase;'>{$name}</b>,</p>
      <p>
        For the payroll period:
        <br><br>
        <span style='display:inline-block;padding:8px 12px;background:#e3f2fd;border-radius:8px;font-weight:bold;color:#0d47a1;'>
          {$start} - {$end}
        </span>
      </p>
      <p style='margin-top:15px;'>Please find your payslip attached to this email.  <a href='https://casassanluisco.com/payslip/assets/emp-view'>
          Click here to check your virtual payslip
        </a></p>

      <!-- CTA BUTTON -->
      <div>
      
      </div>

      <div style='margin-top:20px;padding:15px;background:#f1f8ff;border-left:5px solid #1e88e5;border-radius:8px;'>
        <b>Note:</b> This is a system-generated email. No signature is required.
      </div>
      <p style='margin-top:25px;'>
        Regards,<br>
        <b style='color:#1e88e5;'>Casas San Luis Payslip System</b>
      </p>
    </div>
    <!-- FOOTER -->
    <div style='background:#1e88e5;color:white;text-align:center;padding:12px;font-size:12px;'>
      &copy; " . date('Y') . " CASAS SAN LUIS & CO | Confidential Payslip Document
    </div>
  </div>
</div>
";

  $mail->AltBody = "Hi {$name}, your payslip for {$start} - {$end} is attached.";

  $mail->addAttachment($pdfFile, $pdfFileName);

  $mail->send();

  @unlink($pdfFile);

  response("success", "Payslip sent successfully to $email!");

} catch (Exception $e) {
  @unlink($pdfFile);
  response("error", "Mail error: " . $mail->ErrorInfo);
}