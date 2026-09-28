<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$results = [];

// Test 1: vendor/autoload.php
$vendorPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendorPath)) {
    try {
        require_once $vendorPath;
        $results['vendor_autoload'] = 'OK';
    } catch (Throwable $e) {
        $results['vendor_autoload'] = 'ERROR: ' . $e->getMessage();
    }
} else {
    $results['vendor_autoload'] = 'FILE NOT FOUND at ' . $vendorPath;
}

// Test 2: connection.php
$connPath = __DIR__ . '/process/connection.php'; // adjust kung ibang folder
if (file_exists($connPath)) {
    try {
        require_once $connPath;
        $results['connection'] = isset($pdo) ? 'OK - PDO connected' : 'WARNING - $pdo not set';
    } catch (Throwable $e) {
        $results['connection'] = 'ERROR: ' . $e->getMessage();
    }
} else {
    $results['connection'] = 'FILE NOT FOUND at ' . $connPath;
}

// Test 3: cURL available?
$results['curl_enabled'] = function_exists('curl_init') ? 'OK' : 'NOT AVAILABLE';

// Test 4: PHPMailer available?
$results['phpmailer'] = class_exists('PHPMailer\PHPMailer\PHPMailer') ? 'OK' : 'NOT FOUND (vendor issue)';

// Test 5: Logo file
$logoPath = __DIR__ . '/CSL&CO.png';
$results['logo_file'] = file_exists($logoPath) ? 'OK - found' : 'NOT FOUND at ' . $logoPath;

// Test 6: PHP version
$results['php_version'] = phpversion();

// Test 7: Session
try {
    session_start();
    $results['session'] = 'OK';
} catch (Throwable $e) {
    $results['session'] = 'ERROR: ' . $e->getMessage();
}

echo json_encode($results, JSON_PRETTY_PRINT);
