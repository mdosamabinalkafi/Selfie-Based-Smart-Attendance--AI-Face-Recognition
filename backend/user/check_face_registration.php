<?php
include '../config.php';
header("Content-Type: application/json; charset=utf-8");

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if(!$user_id) {
    echo json_encode(['registered' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM user_faces WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$registered = $stmt->num_rows > 0;
$stmt->close();

echo json_encode(['registered' => $registered]);
$conn->close();
?>
