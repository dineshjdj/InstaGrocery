<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: LoginPage.html');
    exit;
}

$userId = $_SESSION['user_id'];
$totalPrice = 0;

// Fetch cart items with product details for this user
$stmt = $pdo->prepare("
    SELECT c.product_id, c.quantity, p.name, p.price, p.image_url AS image
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Your Cart - InstaGrocery</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<style>
  /* (Keep your full CSS here unchanged) */
  /* Global Styles */
  * {
    box-sizing: border-box;
  }
  body {
    font-family: 'Arial', sans-serif;
    background: #f8f9fa;
    margin: 0;
    padding: 0;
  }
  /* Navbar (header) */
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 50px;
    background-color: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }
  .logo {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    text-decoration: none;
    display: flex;
    align-items: center;
  }
  .logo i {
    color: #10b981;
    margin-right: 10px;
  }
  .nav-links {
    display: flex;
    gap: 20px;
    align-items: center;
  }
  .nav-links a,
  .nav-links button {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    background: none;
    border: none;
    cursor: pointer;
  }
  .btn-primary {
    background-color: #10b981;
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    font-weight: 500;
  }
  .cart-icon {
    font-size: 24px;
  }

  /* Cart container */
  .cart-container {
    max-width: 900px;
    margin: 40px auto 60px;
    padding: 0 20px;
  }
  h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #10b981;
  }
  /* Cart items */
  .cart-item {
    background: #fff;
    display: flex;
    gap: 20px;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
    align-items: center;
  }
  .cart-item img {
    width: 100px;
    height: 100px;
    object-fit: contain;
    border-radius: 6px;
    border: 1px solid #ddd;
  }
  .cart-item-details {
    flex-grow: 1;
  }
  .cart-item-details h3 {
    margin: 0 0 8px 0;
    font-size: 20px;
    color: #333;
  }
  .cart-item-details .price {
    font-weight: 600;
    color: #10b981;
    margin-bottom: 10px;
    font-size: 18px;
  }
  .quantity-container {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .quantity-container input[type="number"] {
    width: 60px;
    padding: 6px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 16px;
  }
  .remove-btn {
    background: #ef4444;
    border: none;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  .remove-btn:hover {
    background: #dc2626;
  }
  /* Cart summary */
  .cart-summary {
    max-width: 900px;
    margin: 30px auto 0;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgb(0 0 0 / 0.05);
    text-align: right;
  }
  .cart-summary .total {
    font-size: 24px;
    font-weight: 700;
    color: #10b981;
    margin-bottom: 20px;
  }
  .checkout-btn {
    background-color: #10b981;
    color: white;
    padding: 14px 30px;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  .checkout-btn:hover {
    background-color: #0f766e;
  }
  p.empty-cart {
    text-align: center;
    font-size: 20px;
    color: #666;
    margin-top: 50px;
  }

  /* Footer styles */
  .footer {
    background-color: #1f2937;
    color: white;
    padding: 50px 20px;
    margin-top: 50px;
  }
  .footer-content {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: 30px;
  }
  .footer-section {
    margin-bottom: 20px;
  }
  .footer-logo {
    display: flex;
    align-items: center;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 15px;
  }
  .footer-logo i {
    margin-right: 10px;
    color: #10b981;
  }
  .footer-links {
    list-style: none;
    padding-left: 0;
  }
  .footer-links li {
    margin-bottom: 10px;
  }
  .footer-links a {
    color: #d1d5db;
    text-decoration: none;
  }
  .contact-info {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
  }
  .contact-info i {
    margin-right: 10px;
    color: #10b981;
  }
  .social-icons {
    display: flex;
    gap: 15px;
  }
  .social-icons a {
    color: white;
    font-size: 18px;
  }
  .footer-bottom {
    text-align: center;
    padding-top: 30px;
    border-top: 1px solid #374151;
    color: #d1d5db;
    font-size: 14px;
  }
</style>

</head>
<body>

<!-- Header / Navbar -->
<nav class="navbar">
  <a href="HomePage.html" class="logo"><i class="fas fa-shopping-cart"></i> InstaGrocery</a>
  <div class="nav-links">
    <a href="products.php">Products</a>
    <a href="LoginPage.html">Log In</a>
    <button class="btn-primary" onclick="window.location.href='signup.html'">Sign Up</button>
    <a href="cart.php"><i class="fas fa-shopping-cart cart-icon"></i></a>
  </div>
</nav>

<!-- Cart content -->
<div class="cart-container">
  <h1>Your Cart</h1>

  <?php if (empty($cartItems)): ?>
    <p class="empty-cart">Your cart is empty.</p>
  <?php else: ?>
    <?php foreach ($cartItems as $item):
      $itemTotal = $item['price'] * $item['quantity'];
      $totalPrice += $itemTotal;
    ?>
    <div class="cart-item">
      <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" />
      <div class="cart-item-details">
        <h3><?= htmlspecialchars($item['name']) ?></h3>
        <div class="price">$<?= number_format($item['price'], 2) ?> each</div>
        <div class="quantity-container">
          <label for="qty-<?= $item['product_id'] ?>">Qty:</label>
          <input id="qty-<?= $item['product_id'] ?>" type="number" min="1" value="<?= $item['quantity'] ?>" onchange="updateQuantity(<?= $item['product_id'] ?>, this.value)" />
        </div>
      </div>
      <div style="text-align: right;">
        <div class="price" style="font-size: 18px;">$<?= number_format($itemTotal, 2) ?></div>
        <button class="remove-btn" onclick="removeItem(<?= $item['product_id'] ?>)">Remove</button>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="cart-summary">
      <div class="total">Total: $<?= number_format($totalPrice, 2) ?></div>
      <button class="checkout-btn" onclick="alert('Proceeding to checkout...')">Proceed to Checkout</button>
    </div>
  <?php endif; ?>
</div>

<!-- Footer -->
<footer class="footer">
  <div class="footer-content">
    <div class="footer-section">
      <div class="footer-logo"><i class="fas fa-shopping-cart"></i> InstaGrocery</div>
      <p>Your go-to grocery store for fresh and fast delivery.</p>
    </div>
    <div class="footer-section">
      <h4>Quick Links</h4>
      <ul class="footer-links">
        <li><a href="HomePage.html">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="LoginPage.html">Log In</a></li>
        <li><a href="signup.html">Sign Up</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h4>Contact Us</h4>
      <div class="contact-info"><i class="fas fa-phone"></i> +1 234 567 890</div>
      <div class="contact-info"><i class="fas fa-envelope"></i> support@instagrocery.com</div>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-square"></i></a>
        <a href="#"><i class="fab fa-twitter-square"></i></a>
        <a href="#"><i class="fab fa-instagram-square"></i></a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    &copy; <?= date("Y") ?> InstaGrocery. All rights reserved.
  </div>
</footer>

<script>
function updateQuantity(productId, quantity) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({productId, quantity})
    })
    .then(res => res.json())
    .then(data => {
        console.log(data.message);
        location.reload();
    });
}

function removeItem(productId) {
    fetch('remove_from_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({productId})
    })
    .then(res => res.json())
    .then(data => {
        console.log(data.message);
        location.reload();
    });
}
</script>

</body>
</html>
