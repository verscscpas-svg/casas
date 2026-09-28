<?php
session_start();
include 'connection.php';

// ✅ Check session
if (!isset($_SESSION['payslip_data'])) {
    echo "No data to save!";
    exit();
}

$data = $_SESSION['payslip_data'];

// ✅ Ensure numeric fields are proper numbers
$numeric_fields = [
    'work_days',
    'basic_pay',
    'holiday_pay',
    'overtime',
    'ot',
    'overtime_pay',
    'restday_ot',
    'restday_ot_pay',
    'special_ot',
    'special_pay',
    'allowance',
    'adjustment',
    'thirteenth',
    'tax',
    'sss',
    'pagibig',
    'philhealth',
    'late',
    'absent',
    'absent_deduction',
    'total_deductions',
    'gross',
    'net',
    'sssloan',
    'hdmfloan',
    'extra_ot_hour',
    'extra_ot_rate',
    'extra_ot_amount'
];

foreach ($numeric_fields as $f) {
    if (
        !isset($data[$f]) ||
        $data[$f] === '' ||
        !is_numeric($data[$f])
    ) {
        $data[$f] = 0;
    }
}

// ==========================================
// ✅ TOTAL OT HOURS
// Current OT + Previous OT
// ==========================================
$total_ot = floatval($data['ot']) + floatval($data['extra_ot_hour']);

// ✅ Ensure absent deduction exists
$absent_deduction = floatval($data['absent_deduction'] ?? 0);

// ✅ Escape strings
$employee_id = intval($data['employee_id']);

$cut_off_start = $conn->real_escape_string(
    $data['cut_off_start']
);

$cut_off_end = $conn->real_escape_string(
    $data['cut_off_end']
);

$payroll_date = $conn->real_escape_string(
    $data['payroll_date']
);

// ==========================================
// ✅ INSERT INTO DATABASE
// ==========================================
$sql = "INSERT INTO payslips (
    employee_id,
    cut_off_start,
    cut_off_end,
    payroll_date,
    work_days,
    basic_pay,
    reg_holiday,
    ot,
    rd_ot,
    spec_ot,
    regular_holiday,
    overtime_pay,
    restday_ot,
    special_ot,
    allowance,
    adjustment,
    thirteenth_month,
    tax,
    sss,
    pagibig,
    philhealth,
    late,
    absent,
    absent_deduction,
    total_deductions,
    gross_pay,
    net_pay,
    sssloan,
    hdmfloan,
    extra_ot_hour,
    extra_ot_rate,
    extra_ot_amount,
    total_ot,
    created_at
) VALUES (
    $employee_id,
    '$cut_off_start',
    '$cut_off_end',
    '$payroll_date',
    {$data['work_days']},
    {$data['basic_pay']},
    {$data['regular_holiday']},
    {$data['ot']},
    {$data['restday_ot']},
    {$data['special_ot']},
    {$data['holiday_pay']},
    {$data['overtime_pay']},
    {$data['restday_ot_pay']},
    {$data['special_pay']},
    {$data['allowance']},
    {$data['adjustment']},
    {$data['thirteenth']},
    {$data['tax']},
    {$data['sss']},
    {$data['pagibig']},
    {$data['philhealth']},
    {$data['late']},
    {$data['absent']},
    $absent_deduction,
    {$data['total_deductions']},
    {$data['gross']},
    {$data['net']},
    {$data['sssloan']},
    {$data['hdmfloan']},
    {$data['extra_ot_hour']},
    {$data['extra_ot_rate']},
    {$data['extra_ot_amount']},
    $total_ot,
    NOW()
)";

// ==========================================
// ✅ EXECUTE QUERY
// ==========================================
if ($conn->query($sql)) {

    $last_id = $conn->insert_id;

    unset($_SESSION['payslip_data']);

    header("Location: ../view-payslip.php?id=$last_id");
    exit();
} else {

    echo "Error: " . $conn->error;
}
