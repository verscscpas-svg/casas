<?php
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

// =========================
// INPUT
// =========================
$html = $_POST['html'] ?? '';
$id   = intval($_POST['id'] ?? 0);

if (!$html) {
  response("error", "No payslip data received.");
}

// =========================
// FIX IMAGE PATHS PARA SA WKHTMLTOPDF
// =========================
$absoluteImgPath = 'file:///' . str_replace('\\', '/', realpath(__DIR__ . '/../../img')) . '/';
$html = preg_replace(
  '/src=["\'](?:[.\/]*img\/)([^"\']+)["\']/i',
  'src="' . $absoluteImgPath . '$1"',
  $html
);

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
$start       = date("F j",   strtotime($row['cut_off_start']));
$end         = date("F j, Y", strtotime($row['cut_off_end']));
$payrollDate = date("Ymd",   strtotime($row['payroll_date']));

// =========================
// PDF FILE NAME
// =========================
$company     = "CS_Emp";
$pdfFileName = $company . "_" . $empNo . "_(" . $payrollDate . ").pdf";

// =========================
// CSS
// =========================
$css1Path    = __DIR__ . "/css/send.css";
$css1Content = file_exists($css1Path) ? file_get_contents($css1Path) : '';

// =========================
// HTML CONTENT
// =========================
$htmlContent = "
<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <style>$css1Content</style>
</head>
<body>
  <div class='payslip-container'>
    $html
  </div>
  <br><br><br>
  <center>
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
// FILE PATHS
// =========================
$htmlFile = $tmpDir . "payslip_$id.html";
$pdfFile  = $tmpDir . $pdfFileName;

file_put_contents($htmlFile, $htmlContent);

$htmlFileReal = realpath($htmlFile);
if (!$htmlFileReal) {
  response("error", "HTML file could not be saved.");
}

// =========================
// WKHTMLTOPDF
// =========================
$wkhtmltopdf = '"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe"';

$command = $wkhtmltopdf
  . ' --enable-local-file-access'
  . ' --page-size A4'
  . ' --orientation Portrait'
  . ' "' . $htmlFileReal . '"'
  . ' "' . $pdfFile . '"'
  . ' 2>&1';

exec($command, $output, $exitCode);

if ($exitCode !== 0 || !file_exists($pdfFile)) {
  response("error", "wkhtmltopdf failed: " . implode(" | ", $output));
}

// =========================
// EMAIL
// =========================
$mail = new PHPMailer(true);

try {
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'juverserojo09122001@gmail.com';
  $mail->Password   = 'nbju driv ewel lmlr';
  $mail->SMTPSecure = 'tls';
  $mail->Port       = 587;

  $mail->setFrom('juverserojo09122001@gmail.com', 'Casas San Luis Payslip System');
  $mail->addAddress($email, $name);
  $mail->Subject = "PAYSLIP: " . strtoupper($start . " - " . $end . " | " . $name);

  $mail->isHTML(true);
  $mail->CharSet = 'UTF-8';

  // LOGO
  $logoPath = realpath(__DIR__ . "/../../img/cslogos.png");
  if (!$logoPath || !file_exists($logoPath)) {
    response("error", "Logo not found.");
  }
  $mail->addEmbeddedImage($logoPath, 'company_logo', 'cslogos.png', 'base64', 'image/png');

  $mail->Body = "
<div style='font-family:Arial,sans-serif;background:#f4f8ff;padding:30px;'>
  <div style='max-width:600px;margin:auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,0.1);'>
    <div style='background:linear-gradient(135deg,#1e88e5,#64b5f6);padding:20px;text-align:center;'>
      <img src='cid:company_logo' style='width:70px;height:70px;border-radius:50%;border:3px solid white;padding:5px;background:#fff;'>
      <h2 style='color:white;margin:10px 0 0;'>CASAS SAN LUIS & CO.</h2>
      <p style='color:#e3f2fd;margin:5px 0;font-size:13px;'>Official Payslip System</p>
    </div>
    <div style='padding:25px;color:#333;'>
      <h3 style='color:#1e88e5;margin-bottom:10px;'>PAYSLIP NOTIFICATION</h3>
      <p>Hi <b style='text-transform:uppercase;'>{$name}</b>,</p>
      <p>For the payroll period:<br><br>
        <span style='display:inline-block;padding:8px 12px;background:#e3f2fd;border-radius:8px;font-weight:bold;color:#0d47a1;'>
          {$start} - {$end}
        </span>
      </p>
      <p style='margin-top:15px;'>Please find your payslip attached to this email.</p>
      <div style='margin-top:20px;padding:15px;background:#f1f8ff;border-left:5px solid #1e88e5;border-radius:8px;'>
        <b>Note:</b> This is a system-generated email. No signature is required.
      </div>
      <p style='margin-top:25px;'>Regards,<br>
        <b style='color:#1e88e5;'>Casas San Luis Payslip System</b>
      </p>
    </div>
    <div style='background:#1e88e5;color:white;text-align:center;padding:12px;font-size:12px;'>
      &copy; " . date('Y') . " CASAS SAN LUIS & CO | Confidential Payslip Document
    </div>
  </div>
</div>
";

  $mail->AltBody = "Hi {$name}, your payslip for {$start} - {$end} is attached.";
  $mail->addAttachment($pdfFile, $pdfFileName);

  $mail->send();

  @unlink($htmlFile);
  @unlink($pdfFile);

  response("success", "Payslip sent successfully to $email!");
} catch (Exception $e) {
  response("error", "Mail error: " . $mail->ErrorInfo);
}
