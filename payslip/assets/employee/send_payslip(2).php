<?php
// ✅ TEMPORARY DEBUG — tanggalin pagkatapos maayos
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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
use Mpdf\Mpdf;

require __DIR__ . '/vendor/autoload.php';

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

// =========================
// GET DATA
// =========================
$res = $conn->query("
SELECT
  e.email,
  e.empName,
  e.empNo,
  e.tinNo,
  e.sssNo,
  e.pagIbigNo,
  e.philHealthNo,
  e.designation,
  e.salary,
  p.cut_off_start,
  p.cut_off_end,
  p.payroll_date,
  p.work_days,
  p.basic_pay,
  p.reg_holiday,
  p.regular_holiday,
  p.ot,
  p.overtime_pay,
  p.rd_ot,
  p.restday_ot,
  p.spec_ot,
  p.special_ot,
  p.allowance,
  p.adjustment,
  p.thirteenth_month,
  p.tax,
  p.sss,
  p.pagibig,
  p.philhealth,
  p.late,
  p.absent,
  p.absent_deduction,
  p.total_deductions,
  p.gross_pay,
  p.net_pay
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
$start       = date("F j",    strtotime($row['cut_off_start']));
$end         = date("F j, Y", strtotime($row['cut_off_end']));
$payrollDate = date("Ymd",    strtotime($row['payroll_date']));

// Readable format para sa display sa PDF
$cut_start_display = date("M d, Y", strtotime($row['cut_off_start']));
$cut_end_display   = date("M d, Y", strtotime($row['cut_off_end']));
$payroll_display   = date("M d, Y", strtotime($row['payroll_date']));

// =========================
// COMPANY NAME
// =========================
$company = "CS_Emp";

// =========================
// PDF FILE NAME
// =========================
$pdfFileName = $company . "_" . $empNo . "_" . "(" . $payrollDate . ")" . ".pdf";

// =========================
// FORMAT NUMBERS
// =========================
$daily_rate      = number_format(($row['salary'] * 12) / 261, 2);
$basic_pay       = number_format($row['basic_pay'], 2);
$reg_holiday_hrs = number_format($row['reg_holiday']);
$reg_holiday_amt = number_format($row['regular_holiday'], 2);
$ot_hrs          = number_format($row['ot']);
$ot_amt          = number_format($row['overtime_pay'], 2);
$rd_ot_hrs       = number_format($row['rd_ot']);
$rd_ot_amt       = number_format($row['restday_ot'], 2);
$spec_ot_hrs     = number_format($row['spec_ot']);
$spec_ot_amt     = number_format($row['special_ot'], 2);
$allowance       = number_format($row['allowance'], 2);
$adjustment      = number_format($row['adjustment'], 2);
$thirteenth      = number_format($row['thirteenth_month'], 2);
$tax             = number_format($row['tax'], 2);
$sss             = number_format($row['sss'], 2);
$pagibig         = number_format($row['pagibig'], 2);
$philhealth      = number_format($row['philhealth'], 2);
$late            = number_format($row['late'], 2);
$absent_days     = intval($row['absent']);
$absent_ded      = number_format($row['absent_deduction'], 2);
$total_ded       = number_format($row['total_deductions'], 2);
$gross_pay       = number_format($row['gross_pay'], 2);
$net_pay         = number_format($row['net_pay'], 2);

// =========================
// LOGO PATH
// =========================
$logoPath = __DIR__ . "/img/cslogos.png";

// =========================
// mPDF-COMPATIBLE CSS
// NOTE: mPDF ay may limitadong CSS support.
//   - Walang box-sizing, display:inline-block, margin:auto
//   - Font family: gamitin lang "courier", "helvetica", "arial", "times"
//   - Borders: ilagay sa td/table, hindi sa div
//   - Alignment: gamitin width % sa td para sa layout
// =========================
$css1Content = '
body {
    font-family: courier;
    font-size: 11px;
    background: #ffffff;
    padding: 15px;
    text-transform: uppercase;
}
.payslip-wrapper {
    width: 100%;
}

/* ── COMPANY HEADER ── */
.company-table {
    width: 100%;
    margin-bottom: 18px;
}
.company-table td {
    vertical-align: middle;
    padding: 0;
}
.COMP {
    font-size: 17px;
    font-weight: bold;
    padding-left: 12px;
    color: #000000;
    letter-spacing: 1px;
}

/* ── INFO GRID ── */
.info-table {
    width: 100%;
    margin-bottom: 0;
}
.info-table td {
    vertical-align: top;
    padding: 0;
    width: 50%;
}
.info-inner {
    width: 100%;
}
.info-inner td {
    font-size: 11px;
    padding: 2px 0;
    vertical-align: top;
}
.lbl {
    width: 110px;
    font-weight: bold;
    color: #000000;
}

/* ── DASHED DIVIDER SECTION ── */
.divider-outer {
    width: 100%;
    border-top: 1px dashed #888888;
    border-bottom: 1px dashed #888888;
    margin-top: 12px;
    margin-bottom: 14px;
}
.divider-table {
    width: 100%;
}
.divider-table td {
    width: 50%;
    vertical-align: top;
    padding: 8px 0;
    font-size: 11px;
}

/* ── EARNINGS & DEDUCTIONS ── */
.calc-table {
    width: 100%;
    margin-top: 8px;
}
.calc-table td {
    width: 50%;
    vertical-align: top;
    padding: 0;
}
.left-calc {
    padding-right: 10px;
}
.right-calc {
    padding-left: 10px;
}
.column-table {
    width: 100%;
}
.column-table th {
    border-bottom: 1px solid #000000;
    padding: 3px 2px;
    text-align: left;
    font-size: 11px;
    font-weight: bold;
}
.column-table th.amt,
.column-table td.amt {
    text-align: right;
}
.column-table td {
    font-size: 11px;
    padding: 2px 2px;
}
.column-table tr.subtotal td {
    border-top: 1px solid #000000;
    font-weight: bold;
}

/* ── TOTALS SECTION ── */
.totals-wrap {
    width: 100%;
    margin-top: 20px;
}
.totals-spacer {
    width: 55%;
}
.totals-table {
    width: 45%;
    vertical-align: top;
}
.totals-inner {
    width: 100%;
}
.totals-inner td {
    font-size: 11px;
    padding: 3px 4px;
}
.totals-inner .amt {
    text-align: right;
}
.totals-inner tr.line td {
    border-bottom: 1px solid #000000;
}
.totals-inner tr.net td {
    font-weight: bold;
    font-size: 12px;
    border-top: 1px solid #000000;
}

/* ── FOOTER NOTE ── */
.info-note {
    font-size: 9px;
    font-style: italic;
    color: #555555;
    text-align: center;
    margin-top: 28px;
    border-top: 1px solid #cccccc;
    padding-top: 8px;
}
';

// =========================
// HTML CONTENT
// NOTE: Lahat ng layout ay ginagawa gamit ang nested tables
//       para siguradong gumagana sa mPDF.
//       Ang display:inline-block ay pinalitan ng td width.
// =========================
$htmlContent = "
<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <style>{$css1Content}</style>
</head>
<body>
<div class='payslip-wrapper'>

  <!-- ═══ COMPANY HEADER ═══ -->
  <table class='company-table'>
    <tr>
      <td width='80'>
        <img src='{$logoPath}' style='width:65px; height:auto;'>
      </td>
      <td>
        <span class='COMP'>CASAS SAN LUIS &amp; CO.</span>
      </td>
    </tr>
  </table>

  <!-- ═══ EMPLOYEE INFO GRID ═══ -->
  <table class='info-table'>
    <tr>
      <!-- LEFT COLUMN -->
      <td>
        <table class='info-inner'>
          <tr>
            <td class='lbl'>EMP NO:</td>
            <td>{$row['empNo']}</td>
          </tr>
          <tr>
            <td class='lbl'>EMP NAME:</td>
            <td>{$row['empName']}</td>
          </tr>
          <tr>
            <td class='lbl'>CUT-OFF:</td>
            <td>{$cut_start_display} - {$cut_end_display}</td>
          </tr>
          <tr>
            <td class='lbl'>PAYROLL DATE:</td>
            <td>{$payroll_display}</td>
          </tr>
        </table>
      </td>
      <!-- RIGHT COLUMN -->
      <td>
        <table class='info-inner'>
          <tr>
            <td class='lbl'>WORK DAYS:</td>
            <td>{$row['work_days']}</td>
          </tr>
          <tr>
            <td class='lbl'>DESIGNATION:</td>
            <td>{$row['designation']}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- ═══ DASHED DIVIDER WITH DAILY RATE & IDs ═══ -->
  <table class='divider-outer'>
    <tr>
      <td style='padding:0;'>
        <table class='divider-table'>
          <tr>
            <!-- LEFT -->
            <td>
              <table class='info-inner'>
                <tr>
                  <td class='lbl'>DAILY RATE:</td>
                  <td>{$daily_rate}</td>
                </tr>
                <tr>
                  <td class='lbl'>TIN NO.:</td>
                  <td>{$row['tinNo']}</td>
                </tr>
              </table>
            </td>
            <!-- RIGHT -->
            <td>
              <table class='info-inner'>
                <tr>
                  <td class='lbl'>SSS NO.:</td>
                  <td>{$row['sssNo']}</td>
                </tr>
                <tr>
                  <td class='lbl'>PAG-IBIG NO.:</td>
                  <td>{$row['pagIbigNo']}</td>
                </tr>
                <tr>
                  <td class='lbl'>PHILHEALTH:</td>
                  <td>{$row['philHealthNo']}</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- ═══ EARNINGS & DEDUCTIONS ═══ -->
  <table class='calc-table'>
    <tr>

      <!-- ── EARNINGS ── -->
      <td class='left-calc'>
        <table class='column-table'>
          <thead>
            <tr>
              <th>EARNINGS</th>
              <th class='amt'>AMOUNT</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>BASIC PAY</td>
              <td class='amt'>{$basic_pay}</td>
            </tr>
            <tr>
              <td>REG HOLIDAY ( {$reg_holiday_hrs} HR/S )</td>
              <td class='amt'>{$reg_holiday_amt}</td>
            </tr>
            <tr>
              <td>OVERTIME ( {$ot_hrs} HR/S )</td>
              <td class='amt'>{$ot_amt}</td>
            </tr>
            <tr>
              <td>RESTDAY OT ( {$rd_ot_hrs} HR/S )</td>
              <td class='amt'>{$rd_ot_amt}</td>
            </tr>
            <tr>
              <td>SPECIAL OT ( {$spec_ot_hrs} HR/S )</td>
              <td class='amt'>{$spec_ot_amt}</td>
            </tr>
            <tr>
              <td>ALLOWANCE</td>
              <td class='amt'>{$allowance}</td>
            </tr>
            <tr>
              <td>ADJUSTMENT</td>
              <td class='amt'>{$adjustment}</td>
            </tr>
            <tr>
              <td>13TH MONTH</td>
              <td class='amt'>{$thirteenth}</td>
            </tr>
          </tbody>
        </table>
      </td>

      <!-- ── DEDUCTIONS ── -->
      <td class='right-calc'>
        <table class='column-table'>
          <thead>
            <tr>
              <th>DEDUCTIONS</th>
              <th class='amt'>AMOUNT</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>TAX</td>
              <td class='amt'>{$tax}</td>
            </tr>
            <tr>
              <td>SSS</td>
              <td class='amt'>{$sss}</td>
            </tr>
            <tr>
              <td>PAG-IBIG</td>
              <td class='amt'>{$pagibig}</td>
            </tr>
            <tr>
              <td>PHILHEALTH</td>
              <td class='amt'>{$philhealth}</td>
            </tr>
            <tr>
              <td>LATE</td>
              <td class='amt'>{$late}</td>
            </tr>
            <tr>
              <td>ABSENT ( {$absent_days} DAY/S )</td>
              <td class='amt'>{$absent_ded}</td>
            </tr>
            <tr class='subtotal'>
              <td>TOTAL DEDUCTIONS</td>
              <td class='amt'>{$total_ded}</td>
            </tr>
          </tbody>
        </table>
      </td>

    </tr>
  </table>

  <!-- ═══ TOTALS ═══ -->
  <table class='totals-wrap'>
    <tr>
      <td class='totals-spacer'>&nbsp;</td>
      <td class='totals-table'>
        <table class='totals-inner'>
          <tr>
            <td>GROSS PAY</td>
            <td class='amt'>{$gross_pay}</td>
          </tr>
          <tr class='line'>
            <td>TOTAL DEDUCTIONS</td>
            <td class='amt'>{$total_ded}</td>
          </tr>
          <tr class='net'>
            <td>NET PAY</td>
            <td class='amt'>{$net_pay}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- ═══ FOOTER NOTE ═══ -->
  <p class='info-note'>
    * THIS IS A SYSTEM-GENERATED PAYSLIP. NO SIGNATURE REQUIRED. *
  </p>

</div>
</body>
</html>
";

// =========================
// TMP FOLDER
// =========================
$tmpDir = __DIR__ . "/tmp/";

if (!is_dir($tmpDir)) {
  mkdir($tmpDir, 0755, true);
}

if (!is_writable($tmpDir)) {
  response("error", "tmp/ folder is not writable.");
}

// =========================
// FILE PATH
// =========================
$pdfFile = $tmpDir . $pdfFileName;

// =========================
// GENERATE PDF VIA mPDF
// =========================
try {
  $mpdf = new Mpdf([
    'format'        => 'A4',
    'orientation'   => 'P',
    'margin_top'    => 15,
    'margin_bottom' => 15,
    'margin_left'   => 15,
    'margin_right'  => 15,
    'tempDir'       => $tmpDir,
    // Gamitin ang DejaVu font para sa mas magandang rendering
    // kung may naka-install na custom font, i-set dito
    'default_font'  => 'dejavusansmono',
  ]);

  $mpdf->WriteHTML($htmlContent);
  $mpdf->Output($pdfFile, \Mpdf\Output\Destination::FILE);

} catch (\Mpdf\MpdfException $e) {
  response("error", "mPDF error: " . $e->getMessage());
} catch (\Exception $e) {
  response("error", "General error during PDF: " . $e->getMessage());
}

if (!file_exists($pdfFile)) {
  response("error", "PDF generation failed — file not created.");
}

// =========================
// EMAIL via PHPMailer
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

  if (!file_exists($logoPath)) {
    response("error", "Logo not found: " . $logoPath);
  }

  $mail->addEmbeddedImage($logoPath, 'company_logo', 'cslogos.png', 'base64', 'image/png');

  $mail->Body = "
<div style='font-family: Arial, sans-serif; background:#f4f8ff; padding:30px;'>
  <div style='max-width:600px; margin:auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.1);'>

    <!-- HEADER -->
    <div style='background: #1e88e5; padding:24px; text-align:center;'>
      <img src='cid:company_logo' style='width:65px; height:65px; border-radius:50%; border:3px solid white; padding:5px; background-color:white; display:block; margin:0 auto 10px;'>
      <h2 style='color:white; margin:0; font-size:18px; letter-spacing:1px;'>CASAS SAN LUIS &amp; CO.</h2>
      <p style='color:#e3f2fd; margin:6px 0 0; font-size:12px;'>Official Payslip System</p>
    </div>

    <!-- BODY -->
    <div style='padding:28px; color:#333333;'>
      <h3 style='color:#1e88e5; margin:0 0 16px; font-size:15px;'>PAYSLIP NOTIFICATION</h3>

      <p style='margin:0 0 10px;'>Hi <b style='text-transform:uppercase;'>{$name}</b>,</p>

      <p style='margin:0 0 16px;'>Your payslip for the period:</p>

      <div style='display:inline-block; padding:10px 16px; background:#e3f2fd; border-radius:8px; font-weight:bold; color:#0d47a1; font-size:14px;'>
        {$start} &ndash; {$end}
      </div>

      <p style='margin:18px 0 0;'>is now ready. Please see the attached PDF.</p>

      <div style='margin-top:20px; padding:14px 16px; background:#f1f8ff; border-left:4px solid #1e88e5; border-radius:0 8px 8px 0;'>
        <b>Note:</b> This is a system-generated email. No signature is required.
      </div>

      <p style='margin-top:24px; margin-bottom:0;'>
        Best regards,<br>
        <b style='color:#1e88e5;'>Casas San Luis Payslip System</b>
      </p>
    </div>

    <!-- FOOTER -->
    <div style='background:#1565c0; color:white; text-align:center; padding:12px 20px; font-size:11px;'>
      &copy; " . date('Y') . " CASAS SAN LUIS &amp; CO. &nbsp;|&nbsp; Confidential Payslip Document
    </div>

  </div>
</div>
";

  $mail->AltBody = "Hi {$name}, your payslip for {$start} - {$end} is attached. Please see the PDF file.";

  $mail->addAttachment($pdfFile, $pdfFileName);

  $mail->send();

  // CLEANUP: burahin ang PDF sa tmp pagkatapos ma-send
  @unlink($pdfFile);

  response("success", "Payslip sent successfully to {$email}!");

} catch (Exception $e) {
  // Cleanup kahit may error
  if (file_exists($pdfFile)) {
    @unlink($pdfFile);
  }
  response("error", "Mail error: " . $mail->ErrorInfo);
}