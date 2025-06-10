<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$productId = (int)($data['productId'] ?? 0);
$quantity = (int)($data['quantity'] ?? 1);

if ($productId <= 0 || $quantity <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid product or quantity']);
    exit;
}

$stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
$stmt->execute([$quantity, $userId, $productId]);

echo json_encode(['status' => 'success', 'message' => 'Quantity updated']);
