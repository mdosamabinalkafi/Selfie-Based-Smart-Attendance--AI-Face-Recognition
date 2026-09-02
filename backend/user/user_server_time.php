<?php
include '../config.php';
header("Content-Type: application/json; charset=utf-8");

// ✅ Force Bangladesh Time
date_default_timezone_set('Asia/Dhaka');

echo json_encode([
    'success' => true,
    'time' => date('H:i:s'),
    'date' => date('Y-m-d'),
    'datetime' => date('Y-m-d H:i:s'),
    'timezone' => 'Asia/Dhaka',
    'hour' => (int)date('H')
]);
?>