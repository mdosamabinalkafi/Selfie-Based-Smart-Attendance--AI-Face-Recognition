<?php
include 'config.php';

if(!isset($_SESSION['login'])){
    die("Unauthorized");
}

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? 0;
$date = $data['date'] ?? '';
$name = $data['name'] ?? '';
$empid = $data['empid'] ?? '';
$facility_bangla = $data['facility'] ?? '';
$start_time = $data['start_time'] ?? '';
$end_time = $data['end_time'] ?? '';
$duration = $data['duration'] ?? 0;
$note = $data['note'] ?? '';

$facility = getFacilityEnglish($facility_bangla);

if($duration > 45){
    $priority = 'High';
} elseif($duration >= 1 && $duration <= 45){
    $priority = 'Low';
} else {
    $priority = 'Neutral';
}

$stmt = $conn->prepare("
UPDATE records SET 
    date = ?,
    name = ?,
    emp_id = ?,
    facility = ?,
    start_time = ?,
    end_time = ?,
    duration = ?,
    priority = ?,
    note = ?
WHERE id = ?
");

$stmt->bind_param(
    "sssssssssi",
    $date,
    $name,
    $empid,
    $facility,
    $start_time,
    $end_time,
    $duration,
    $priority,
    $note,
    $id
);

if($stmt->execute()){
    echo "Updated Successfully!";
} else {
    echo "Update Failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>