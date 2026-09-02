<?php
include '../config.php';
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? 0;
$emp_id = $data['emp_id'] ?? '';
$descriptor = $data['descriptor'] ?? [];

if(!$user_id || empty($descriptor)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$descriptor_json = json_encode($descriptor);

// Check if already registered
$check = $conn->prepare("SELECT id FROM user_faces WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$check->store_result();

if($check->num_rows > 0) {
    // Update existing
    $stmt = $conn->prepare("UPDATE user_faces SET face_descriptor = ? WHERE user_id = ?");
    $stmt->bind_param("si", $descriptor_json, $user_id);
} else {
    // Insert new
    $stmt = $conn->prepare("INSERT INTO user_faces (user_id, emp_id, face_descriptor) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $emp_id, $descriptor_json);
}
$check->close();

if($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Face registered successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to register face: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>