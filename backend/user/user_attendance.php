<?php
include '../config.php';
header("Content-Type: application/json; charset=utf-8");

// ✅ Force Bangladesh Time
date_default_timezone_set('Asia/Dhaka');

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'] ?? 0;
$emp_id = $data['emp_id'] ?? '';
$selfie = $data['selfie'] ?? '';
$is_late_click = $data['is_late_click'] ?? false;
$is_holiday = $data['is_holiday'] ?? false;
$date = date('Y-m-d');
$time = date('H:i:s');

if(!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// ✅ Check if already marked today
$check = $conn->prepare("SELECT id FROM attendance WHERE user_id = ? AND date = ?");
$check->bind_param("is", $user_id, $date);
$check->execute();
$check->store_result();

if($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Attendance already marked today']);
    exit;
}
$check->close();

// ✅ If holiday, mark as holiday
if($is_holiday) {
    $stmt = $conn->prepare("INSERT INTO attendance (user_id, emp_id, date, time, status) VALUES (?, ?, ?, ?, 'holiday')");
    $stmt->bind_param("isss", $user_id, $emp_id, $date, $time);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Holiday marked',
        'time' => $time,
        'status' => 'Holiday'
    ]);
    exit;
}

// Get schedule
$schedule_result = $conn->query("SELECT * FROM attendance_schedule ORDER BY id DESC LIMIT 1");
$schedule = $schedule_result->fetch_assoc();

if(!$schedule) {
    $morning_start = '08:45';
    $morning_end = '09:15';
    $afternoon_start = '14:00';
    $afternoon_end = '14:15';
} else {
    $morning_start = $schedule['morning_present_start'] ?? '08:45';
    $morning_end = $schedule['morning_present_end'] ?? '09:15';
    $afternoon_start = $schedule['afternoon_present_start'] ?? '14:00';
    $afternoon_end = $schedule['afternoon_present_end'] ?? '14:15';
}

// Determine status
$current_time = strtotime($time);
$m_start = strtotime($morning_start);
$m_end = strtotime($morning_end);
$a_start = strtotime($afternoon_start);
$a_end = strtotime($afternoon_end);

$status = 'absent';
$status_text = 'Absent';

if($current_time >= $m_start && $current_time <= $m_end) {
    $status = 'present';
    $status_text = 'Present';
} elseif($current_time >= $a_start && $current_time <= $a_end) {
    $status = 'present';
    $status_text = 'Present';
} elseif($is_late_click) {
    $status = 'late';
    $status_text = 'Late';
}

// ✅ If it's a late click, no selfie needed
if($is_late_click && $status === 'late') {
    $filename = null;
    $stmt = $conn->prepare("INSERT INTO attendance (user_id, emp_id, date, time, selfie, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $emp_id, $date, $time, $filename, $status);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Late attendance recorded',
        'time' => $time,
        'status' => 'Late'
    ]);
    exit;
}

// ✅ Save selfie
if($selfie) {
    $upload_dir = '../../media/selfies/';
    if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $filename = 'selfie_' . $user_id . '_' . time() . '.jpg';
    $image_data = str_replace('data:image/jpeg;base64,', '', $selfie);
    $image_data = str_replace(' ', '+', $image_data);
    file_put_contents($upload_dir . $filename, base64_decode($image_data));
} else {
    $filename = null;
}

$stmt = $conn->prepare("INSERT INTO attendance (user_id, emp_id, date, time, selfie, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $user_id, $emp_id, $date, $time, $filename, $status);
$stmt->execute();
$stmt->close();

echo json_encode([
    'success' => true,
    'message' => 'Attendance marked successfully',
    'time' => $time,
    'status' => $status_text,
    'timezone' => 'Asia/Dhaka'
]);
$conn->close();
?>