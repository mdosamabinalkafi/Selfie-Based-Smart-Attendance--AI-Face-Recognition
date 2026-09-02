<?php
include 'config.php';

if(!isset($_SESSION['login'])){
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);

$morning_present_start = $data['morning_present_start'] ?? '09:00';
$morning_present_end = $data['morning_present_end'] ?? '09:15';
$morning_late_start = $data['morning_late_start'] ?? '09:16';
$morning_late_end = $data['morning_late_end'] ?? '09:30';
$afternoon_present_start = $data['afternoon_present_start'] ?? '14:00';
$afternoon_present_end = $data['afternoon_present_end'] ?? '14:15';
$afternoon_late_start = $data['afternoon_late_start'] ?? '14:16';
$afternoon_late_end = $data['afternoon_late_end'] ?? '14:30';
$auto_absent_time = $data['auto_absent_time'] ?? '16:00';

// Delete old schedule
$conn->query("DELETE FROM attendance_schedule");

// Insert new schedule with auto_absent_time
$stmt = $conn->prepare("INSERT INTO attendance_schedule 
    (morning_present_start, morning_present_end, morning_late_start, morning_late_end, 
     afternoon_present_start, afternoon_present_end, afternoon_late_start, afternoon_late_end, 
     auto_absent_time) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "sssssssss",
    $morning_present_start, $morning_present_end, $morning_late_start, $morning_late_end,
    $afternoon_present_start, $afternoon_present_end, $afternoon_late_start, $afternoon_late_end,
    $auto_absent_time
);

if($stmt->execute()){
    echo json_encode(['success' => true, 'message' => 'Schedule saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save schedule: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>