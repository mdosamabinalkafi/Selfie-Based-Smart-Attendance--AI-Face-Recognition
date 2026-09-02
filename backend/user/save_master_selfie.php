<?php
// ✅ Disable error reporting to avoid extra output
error_reporting(0);
ini_set('display_errors', 0);

include '../config.php';

// ✅ Set header first
header("Content-Type: application/json; charset=utf-8");

// ✅ Get raw input
$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true);

// ✅ If no data, return error
if(!$data) {
    echo '{"success":false,"message":"No data received"}';
    exit;
}

$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
$emp_id = isset($data['emp_id']) ? $data['emp_id'] : '';
$descriptor = isset($data['descriptor']) ? $data['descriptor'] : [];
$selfie = isset($data['selfie']) ? $data['selfie'] : '';

// ✅ Validate
if(!$user_id) {
    echo '{"success":false,"message":"User ID required"}';
    exit;
}

if(empty($selfie)) {
    echo '{"success":false,"message":"Selfie image required"}';
    exit;
}

$descriptor_json = json_encode($descriptor);

// ✅ Check if already exists
$check = $conn->prepare("SELECT id FROM user_master_selfie WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$check->store_result();

if($check->num_rows > 0) {
    // Update
    $stmt = $conn->prepare("UPDATE user_master_selfie SET emp_id = ?, face_descriptor = ?, selfie_image = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $emp_id, $descriptor_json, $selfie, $user_id);
} else {
    // Insert
    $stmt = $conn->prepare("INSERT INTO user_master_selfie (user_id, emp_id, face_descriptor, selfie_image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $emp_id, $descriptor_json, $selfie);
}
$check->close();

if($stmt->execute()) {
    echo '{"success":true,"message":"Face registered successfully"}';
} else {
    echo '{"success":false,"message":"Database error: ' . $stmt->error . '"}';
}

$stmt->close();
$conn->close();
?>