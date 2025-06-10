<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$productId = (int)($data['productId'] ?? 0);
$quantity = (int)($data['quantity'] ?? 1);

if ($productId <= 0 || $quantity <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid product or quantity']);
    exit;
}

// Check if the product exists
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$stmt->execute([$productId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit;
}

// Check if item already in cart
$stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $productId]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    // Update quantity
    $newQuantity = $existing['quantity'] + $quantity;
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$newQuantity, $userId, $productId]);
} else {
    // Insert new item
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $productId, $quantity]);
}

echo json_encode(['status' => 'success', 'message' => 'Product added to cart successfully']);
