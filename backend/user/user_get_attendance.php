<?php
include '../config.php';
header("Content-Type: application/json; charset=utf-8");

$user_id = $_SESSION['user_id'] ?? 0;
if(!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$result = $conn->query("SELECT * FROM attendance WHERE user_id = $user_id ORDER BY date DESC LIMIT 30");
$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>