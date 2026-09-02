<?php
include 'config.php';

if(!isset($_SESSION['login'])){
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$newUsername = trim($data['new_username'] ?? '');
$currentPassword = $data['current_password'] ?? '';
$newPassword = $data['new_password'] ?? '';

// Validation
if(empty($newUsername) || strlen($newUsername) < 3){
    echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters']);
    exit;
}

if(empty($currentPassword)){
    echo json_encode(['success' => false, 'message' => 'Current password is required']);
    exit;
}

if(!empty($newPassword) && strlen($newPassword) < 4){
    echo json_encode(['success' => false, 'message' => 'New password must be at least 4 characters']);
    exit;
}

// Check current credentials
$stmt = $conn->prepare("SELECT username, password FROM admin_credentials WHERE username = ?");
$stmt->bind_param("s", $newUsername);
$stmt->execute();
$result = $stmt->get_result();

// Check if new username already exists (but we're updating the same user)
$currentUser = 'front'; // Default
$checkStmt = $conn->prepare("SELECT username FROM admin_credentials LIMIT 1");
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
if($checkResult->num_rows > 0){
    $row = $checkResult->fetch_assoc();
    $currentUser = $row['username'];
}
$checkStmt->close();

// If username is changing, check if new username already exists
if($newUsername !== $currentUser){
    $checkStmt = $conn->prepare("SELECT username FROM admin_credentials WHERE username = ?");
    $checkStmt->bind_param("s", $newUsername);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    if($checkResult->num_rows > 0){
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit;
    }
    $checkStmt->close();
}

// Verify current password
$verifyStmt = $conn->prepare("SELECT password FROM admin_credentials WHERE username = ?");
$verifyStmt->bind_param("s", $currentUser);
$verifyStmt->execute();
$verifyResult = $verifyStmt->get_result();

if($verifyResult->num_rows > 0){
    $row = $verifyResult->fetch_assoc();
    if($row['password'] !== $currentPassword){
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }
}
$verifyStmt->close();

// Update credentials
$finalPassword = !empty($newPassword) ? $newPassword : $currentPassword;

$updateStmt = $conn->prepare("UPDATE admin_credentials SET username = ?, password = ? WHERE username = ?");
$updateStmt->bind_param("sss", $newUsername, $finalPassword, $currentUser);

if($updateStmt->execute()){
    // Update session variable
    $_SESSION['username'] = $newUsername;
    echo json_encode(['success' => true, 'message' => 'Credentials updated successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update credentials']);
}

$updateStmt->close();
?>