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

// =========================
// GET ID
// =========================
$id = intval($_GET['id'] ?? 0);

if (!$id) {
  die("Invalid ID");
}

// =========================
// FETCH DATA
// =========================
$res = $conn->query("
SELECT p.*, e.empNo, e.empName, e.designation, e.tinNo, e.sssNo,
e.pagIbigNo, e.philHealthNo
FROM payslips p
JOIN employees e ON p.employee_id = e.id
WHERE p.id = $id
");

if ($res->num_rows == 0) {
  die("Payslip not found!");
}

$data = $res->fetch_assoc();

// =========================
// COMPUTATION
// =========================
$fixrate = ($data['work_days'] > 0)
  ? ($data['basic_pay'] * 2 * 12 / 261)
  : 0;

// =========================
// LOGO
// =========================
$imagePath = __DIR__ . "/../../img/cs.png";
$logo = "";

if (file_exists($imagePath)) {
  $logo = base64_encode(file_get_contents($imagePath));
}

// =========================
// CSS
// =========================
$css = "
body {
font-family: Courier New, monospace;
font-size: 12px;
color: #000;
}

/* MAIN CONTAINER */
.payslip-container {
width: 100%;
padding: 20px;
}

/* HEADER */
.COMPANY {
text-align: center;
margin-bottom: 20px;
}

.IMGLOGO img {
width: 80px;
}

.COMP {
font-size: 18px;
font-weight: bold;
}

/* INFO TABLE (REPLACE GRID) */
.info-table {
width: 100%;
margin-bottom: 15px;
}

.info-table td {
padding: 4px 8px;
vertical-align: top;
}

/* LINE SECTIONS */
.section-line {
border-top: 1px dashed #999;
margin: 10px 0;
}

/* EARNINGS + DEDUCTIONS TABLE */
.pay-table {
width: 100%;
border-collapse: collapse;
margin-top: 10px;
}

.pay-table th {
border-bottom: 1px solid #000;
text-align: left;
padding: 5px;
}

.pay-table td {
padding: 5px;
}

.text-right {
text-align: right;
}

/* 2 COLUMN FIX */
.two-col {
width: 100%;
}

.two-col td {
width: 50%;
vertical-align: top;
}

/* TOTALS */
.totals {
margin-top: 20px;
width: 100%;
}

.totals td {
padding: 3px 0;
}

.bold {
font-weight: bold;
}
";

// =========================
// HTML
// =========================
$html = "
<style>
  $css
</style>

<div class='payslip-container' id='payslip-container'>

  <div class='COMPANY'>
    <div class='IMGLOGO'>
      <img src='data:image/png;base64,$logo'>
    </div>
    <h1 class='COMP'>CASAS SAN LUIS & CO.</h1>
  </div>

  <div class='info-grid'>
    <div>
      <div class='info-group'><span class='label'>EMP NO:</span> {$data['empNo']}</div>
      <div class='info-group'><span class='label'>EMP NAME:</span> {$data['empName']}</div>
      <div class='info-group'><span class='label'>CUT-OFF:</span> {$data['cut_off_start']} - {$data['cut_off_end']}</div>
      <div class='info-group'><span class='label'>PAYROLL DATE:</span> {$data['payroll_date']}</div>
    </div>

    <div>
      <div class='info-group'><span class='label'>Work Days:</span> {$data['work_days']}</div>
      <div class='info-group'><span class='label'>Designation:</span> {$data['designation']}</div>
    </div>
  </div>

  <div class='info-grid' style='margin-top:20px;'>
    <div>
      <div><b>Daily Rate:</b> " . number_format($fixrate, 2) . "</div>
      <div><b>TIN:</b> {$data['tinNo']}</div>
    </div>

    <div>
      <div><b>SSS:</b> {$data['sssNo']}</div>
      <div><b>Pag-ibig:</b> {$data['pagIbigNo']}</div>
      <div><b>PhilHealth:</b> {$data['philHealthNo']}</div>
    </div>
  </div>

  <table class='column-table'>
    <tr>
      <th>EARNINGS</th>
      <th class='text-right'>AMOUNT</th>
    </tr>
    <tr>
      <td>Basic Pay</td>
      <td class='text-right'>" . number_format($data['basic_pay'], 2) . "</td>
    </tr>
    <tr>
      <td>Regular Holiday</td>
      <td class='text-right'>" . number_format($data['regular_holiday'], 2) . "</td>
    </tr>
    <tr>
      <td>Overtime</td>
      <td class='text-right'>" . number_format($data['overtime_pay'], 2) . "</td>
    </tr>
    <tr>
      <td>Rest Day OT</td>
      <td class='text-right'>" . number_format($data['restday_ot'], 2) . "</td>
    </tr>
    <tr>
      <td>Special OT</td>
      <td class='text-right'>" . number_format($data['special_ot'], 2) . "</td>
    </tr>
    <tr>
      <td>Allowance</td>
      <td class='text-right'>" . number_format($data['allowance'], 2) . "</td>
    </tr>
    <tr>
      <td>Adjustment</td>
      <td class='text-right'>" . number_format($data['adjustment'], 2) . "</td>
    </tr>
    <tr>
      <td>13th Month</td>
      <td class='text-right'>" . number_format($data['thirteenth_month'], 2) . "</td>
    </tr>
  </table>

  <table class='column-table'>
    <tr>
      <th>DEDUCTIONS</th>
      <th class='text-right'>AMOUNT</th>
    </tr>
    <tr>
      <td>Tax</td>
      <td class='text-right'>" . number_format($data['tax'], 2) . "</td>
    </tr>
    <tr>
      <td>SSS</td>
      <td class='text-right'>" . number_format($data['sss'], 2) . "</td>
    </tr>
    <tr>
      <td>Pag-ibig</td>
      <td class='text-right'>" . number_format($data['pagibig'], 2) . "</td>
    </tr>
    <tr>
      <td>Philhealth</td>
      <td class='text-right'>" . number_format($data['philhealth'], 2) . "</td>
    </tr>
    <tr>
      <td>Late</td>
      <td class='text-right'>" . number_format($data['late'], 2) . "</td>
    </tr>
    <tr>
      <td>Absent ({$data['absent']} days)</td>
      <td class='text-right'>" . number_format($data['absent_deduction'], 2) . "</td>
    </tr>
    <tr>
      <td><b>Total</b></td>
      <td class='text-right'>" . number_format($data['total_deductions'], 2) . "</td>
    </tr>
  </table>

  <div class='totals-section'>
    <div class='total-row'><span>GROSS PAY:</span><span>" . number_format($data['gross_pay'], 2) . "</span></div>
    <div class='total-row under'><span>DEDUCTIONS:</span><span>" . number_format($data['total_deductions'], 2) . "</span></div>
    <div class='total-row' style='font-weight:bold;'><span>NET PAY:</span><span>" . number_format($data['net_pay'], 2) . "</span></div>
  </div>

</div>
";

// =========================
// WKHTMLTOPDF (NEW)
// =========================

$tmpDir = __DIR__ . "/tmp/";
if (!is_dir($tmpDir)) {
  mkdir($tmpDir, 0777, true);
}

$htmlFile = $tmpDir . "payslip_$id.html";
$pdfFile = $tmpDir . "payslip_$id.pdf";

file_put_contents($htmlFile, $html);

// PATH (IMPORTANT)
$wkhtmltopdf = "wkhtmltopdf";
// Windows example:
// $wkhtmltopdf = "C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe";

$command = "$wkhtmltopdf
--enable-local-file-access
--page-size A4
--orientation Portrait
\"$htmlFile\" \"$pdfFile\"";

exec($command);

// CHECK RESULT
if (!file_exists($pdfFile)) {
  die("PDF generation failed");
}

// OUTPUT PDF
header("Content-Type: application/pdf");
readfile($pdfFile);
exit;
