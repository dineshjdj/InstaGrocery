<?php
// login.php
session_start();  // START THE SESSION AT THE TOP

$conn = new mysqli('localhost', 'root', '', 'instagrocery_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    exit;
}

$stmt->bind_result($id, $hashed_password);
$stmt->fetch();

if (password_verify($password, $hashed_password)) {
    $_SESSION['user_id'] = $id;  // SET USER ID IN SESSION HERE
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
}

$stmt->close();
$conn->close();
?>
