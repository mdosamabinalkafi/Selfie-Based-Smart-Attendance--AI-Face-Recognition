<?php
include '../config.php';
header("Content-Type: application/json; charset=utf-8");

$user_id = $_SESSION['user_id'] ?? 0;
if(!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// ✅ Get attendance stats (present, late, absent, holiday)
$result = $conn->query("
    SELECT 
        COUNT(CASE WHEN status = 'present' THEN 1 END) as present,
        COUNT(CASE WHEN status = 'late' THEN 1 END) as late,
        COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent,
        COUNT(CASE WHEN status = 'holiday' THEN 1 END) as holidays
    FROM attendance 
    WHERE user_id = $user_id
");

$data = $result->fetch_assoc();

// ✅ Calculate total approved leave days (not count)
$leaveResult = $conn->query("
    SELECT leave_type, from_date, to_date 
    FROM leave_requests 
    WHERE user_id = $user_id AND status = 'employeeroved'
");

$totalLeaveDays = 0;
while($leave = $leaveResult->fetch_assoc()) {
    $from = new DateTime($leave['from_date']);
    $to = new DateTime($leave['to_date']);
    $days = $from->diff($to)->days + 1;
    if($leave['leave_type'] === 'half') $days = 0.5;
    $totalLeaveDays += $days;
}

echo json_encode([
    'success' => true,
    'present' => (int)($data['present'] ?? 0),
    'late' => (int)($data['late'] ?? 0),
    'absent' => (int)($data['absent'] ?? 0),
    'leaves' => $totalLeaveDays,  // ✅ Total days (not count)
    'holidays' => (int)($data['holidays'] ?? 0)
]);
$conn->close();
?>