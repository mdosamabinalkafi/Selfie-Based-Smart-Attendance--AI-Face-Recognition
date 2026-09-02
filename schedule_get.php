<?php
include 'config.php';
header("Content-Type: application/json; charset=utf-8");

$result = $conn->query("SELECT * FROM attendance_schedule ORDER BY id DESC LIMIT 1");

if($result && $result->num_rows > 0){
    $data = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'morning_present_start' => substr($data['morning_present_start'], 0, 5),
        'morning_present_end' => substr($data['morning_present_end'], 0, 5),
        'morning_late_start' => substr($data['morning_late_start'], 0, 5),
        'morning_late_end' => substr($data['morning_late_end'], 0, 5),
        'afternoon_present_start' => substr($data['afternoon_present_start'], 0, 5),
        'afternoon_present_end' => substr($data['afternoon_present_end'], 0, 5),
        'afternoon_late_start' => substr($data['afternoon_late_start'], 0, 5),
        'afternoon_late_end' => substr($data['afternoon_late_end'], 0, 5),
        'auto_absent_time' => substr($data['auto_absent_time'], 0, 5)
    ]);
} else {
    echo json_encode([
        'success' => true,
        'morning_present_start' => '09:00',
        'morning_present_end' => '09:15',
        'morning_late_start' => '09:16',
        'morning_late_end' => '09:30',
        'afternoon_present_start' => '14:00',
        'afternoon_present_end' => '14:15',
        'afternoon_late_start' => '14:16',
        'afternoon_late_end' => '14:30',
        'auto_absent_time' => '16:00'
    ]);
}
$conn->close();
?>