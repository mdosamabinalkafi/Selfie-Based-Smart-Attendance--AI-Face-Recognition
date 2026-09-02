<?php
include 'config.php';

if(!isset($_SESSION['login'])){
    die("Unauthorized");
}

$data = json_decode(file_get_contents("php://input"), true);

$date = $data['date'] ?? '';
$name = $data['name'] ?? '';
$empid = $data['empid'] ?? '';
$facility_bangla = $data['facility'] ?? '';
$start_time = $data['start_time'] ?? '';
$end_time = $data['end_time'] ?? '';
$duration = $data['duration'] ?? 0;
$note = $data['note'] ?? '';

if(empty($date) || empty($name) || empty($empid) || empty($facility_bangla)){
    die("Please fill all required fields");
}

$facility = getFacilityEnglish($facility_bangla);

if($duration > 45){
    $priority = 'High';
} elseif($duration >= 1 && $duration <= 45){
    $priority = 'Low';
} else {
    $priority = 'Neutral';
}

$stmt = $conn->prepare("
INSERT INTO records
(date, name, emp_id, facility, start_time, end_time, duration, priority, note)
VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssssss",
    $date,
    $name,
    $empid,
    $facility,
    $start_time,
    $end_time,
    $duration,
    $priority,
    $note
);

if($stmt->execute()){
    echo "Saved Successfully!";
} else {
    echo "Failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>