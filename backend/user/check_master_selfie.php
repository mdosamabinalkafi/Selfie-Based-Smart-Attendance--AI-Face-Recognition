<?php
include '../config.php';
header("Content-Type: application/json; charset=utf-8");

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if(!$user_id) {
    echo '{"exists":false}';
    exit;
}

$stmt = $conn->prepare("SELECT id FROM user_master_selfie WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$exists = $stmt->num_rows > 0;
$stmt->close();

echo '{"exists":' . ($exists ? 'true' : 'false') . '}';
$conn->close();
?>