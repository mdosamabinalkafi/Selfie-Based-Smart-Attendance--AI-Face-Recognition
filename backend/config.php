<?php

$host = "sql210.infinityfree.com";
$user = "if0_41809408";
$pass = "XSHAh7HmosMEP2b";
$db   = "if0_41809408_osamabinalkafii";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");



// ✅ Session start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ✅ Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) || isset($_SESSION['login']);
}

// ✅ Get current user ID
function getUserId() {
    return $_SESSION['user_id'] ?? 0;
}


// English to Bangla mapping
function getFacilityBangla($english) {
    $map = [
        'extra_break' => 'এক্সট্রা ব্রেক',
        'early_leave' => 'নির্ধারিত সময়ের আগে অফিস ত্যাগ',
        'shift_change' => 'বিকল্প কর্মী ছাড়া শিফট পরিবর্তন',
        'other_break' => 'অন্য সময়ে বিরতি',
        'exam_delay' => 'পরীক্ষাজনিত বিলম্ব সুবিধা'
    ];
    return $map[$english] ?? $english;
}

// Bangla to English mapping
function getFacilityEnglish($bangla) {
    $map = [
        'এক্সট্রা ব্রেক' => 'extra_break',
        'নির্ধারিত সময়ের আগে অফিস ত্যাগ' => 'early_leave',
        'বিকল্প কর্মী ছাড়া শিফট পরিবর্তন' => 'shift_change',
        'অন্য সময়ে বিরতি' => 'other_break',
        'পরীক্ষাজনিত বিলম্ব সুবিধা' => 'exam_delay'
    ];
    return $map[$bangla] ?? $bangla;
}
?>