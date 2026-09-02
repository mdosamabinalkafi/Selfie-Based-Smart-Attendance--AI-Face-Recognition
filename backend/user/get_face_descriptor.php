<?php
include '../config.php';
header("Content-Type: application/json; charset=utf-8");

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if(!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

$stmt = $conn->prepare("SELECT face_descriptor FROM user_faces WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if($row) {
    $descriptor = json_decode($row['face_descriptor'], true);
    echo json_encode(['success' => true, 'descriptor' => $descriptor]);
} else {
    echo json_encode(['success' => false, 'message' => 'Face not registered']);
}

$conn->close();
?>