<?php
include '../config.php';

if(!isset($_SESSION['login'])){
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header("Content-Type: application/json; charset=utf-8");
$conn->set_charset("utf8mb4");

// Get all attendance records with employee names
$sql = "SELECT a.*, u.name 
        FROM attendance a 
        LEFT JOIN users u ON a.user_id = u.id 
        ORDER BY a.date DESC, a.time DESC";

$result = $conn->query($sql);
$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
$conn->close();
?>