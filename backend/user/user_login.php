<?php
include '../config.php';
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);

$emp_id = $data['emp_id'] ?? '';
$password = $data['password'] ?? '';

$stmt = $conn->prepare("SELECT id, emp_id, name, email, phone, department, designation, password FROM users WHERE emp_id = ?");
$stmt->bind_param("s", $emp_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if(password_verify($password, $user['password'])) {
        unset($user['password']);
        
        // ✅ Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['emp_id'] = $user['emp_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['login'] = true;
        
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Employee ID not found']);
}
$stmt->close();
$conn->close();
?>