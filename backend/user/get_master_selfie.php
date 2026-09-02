<?php
include '../config.php';
header("Content-Type: application/json; charset=utf-8");

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if(!$user_id) {
    echo '{"success":false,"message":"User ID required"}';
    exit;
}

$stmt = $conn->prepare("SELECT face_descriptor, selfie_image FROM user_master_selfie WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if($row) {
    $descriptor = json_decode($row['face_descriptor'], true);
    echo '{"success":true,"descriptor":' . json_encode($descriptor) . ',"selfie":"' . addslashes($row['selfie_image']) . '"}';
} else {
    echo '{"success":false,"message":"No face registered"}';
}
$conn->close();
?>