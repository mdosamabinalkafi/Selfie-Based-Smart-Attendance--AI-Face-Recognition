<?php
include 'config.php';

header("Content-Type: text/plain");

$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// Database থেকে credentials চেক করুন
$stmt = $conn->prepare("SELECT username, password FROM admin_credentials WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    // Direct comparison (since we're not using hashing yet)
    if($password === $row['password']){
        $_SESSION['login'] = true;
        echo "OK";
    } else {
        echo "FAIL";
    }
} else {
    echo "FAIL";
}

$stmt->close();
?>