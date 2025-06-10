<?php
session_start();
include 'db.php'; // Ensure this line is present

// Check if the PDO connection is established
if (!isset($pdo)) {
    die("Database connection failed.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InstaGrocery - Browse Products</title>
    <style>
        /* Global Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 50px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            color: #333;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
        }
        
        .logo i {
            color: #10b981;
            margin-right: 10px;
            font-size: 28px;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background-color: #10b981;
            color: white;
        }
        
        .cart-icon {
            font-size: 24px;
            color: #333;
            margin-left: 20px;
        }
        
        /* Products Header */
        .products-header {
            padding: 30px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .products-header h1 {
            font-size: 32px;
            color: #333;
        }
        
        .search-filter {
            display: flex;
            gap: 15px;
        }
        
        .search-bar {
            position: relative;
        }
        
        .search-bar input {
            padding: 10px 15px;
            padding-left: 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }
        
        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .sort-dropdown, .filter-btn {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
        }
        
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .product-category {
            color: #777;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
                .product-price {
            color: #10b981;
            font-weight: bold;
            font-size: 18px;
            margin-right: 10px;
        }
        
        .add-to-cart {
            width: 100%;
            padding: 10px;
            background-color: #10b981;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            margin-top: 10px;
        }
        
        .add-to-cart:hover {
            background-color: #0e9f6e;
        }
        
        /* Footer */
        .footer {
            background-color: #1f2937;
            color: white;
            padding: 50px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 24px;
            font-weight: bold;
        }
        
        .footer-logo i {
            margin-right: 10px;
            color: #10b981;
        }
        
        .footer-description {
            max-width: 300px;
            color: #d1d5db;
            margin-bottom: 20px;
        }
        
        .footer-section {
            margin-right: 30px;
        }
        
        .footer-section h3 {
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .footer-links {
            list-style: none;
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
            margin-top: 20px;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="HomePage.html" class="logo">
            <i class="fas fa-shopping-cart"></i>
            InstaGrocery
        </a>
        <div class="nav-links">
            <a href="products.php">Products</a>
            <a href="LoginPage.html">Log In</a>
            <button class="btn btn-primary" onclick="window.location.href='signup.html'">Sign Up</button>
            <a href="cart.php"><i class="fas fa-shopping-cart cart-icon"></i></a>
        </div>
    </nav>

    <div class="container">
        <div class="products-header">
            <h1>Browse Products</h1>
            <div class="search-filter">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search products...">
                </div>
                <select class="sort-dropdown">
                    <option value="name">Sort by: Name</option>
                    <option value="price-low">Sort by: Price (Low to High)</option>
                    <option value="price-high">Sort by: Price (High to Low)</option>
                    <option value="popularity">Sort by: Popularity</option>
                </select>
                <button class="filter-btn">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>
        </div>

        <div class="products-grid">
    <?php
    // Fetch products from the database
    $stmt = $pdo->query("SELECT * FROM products");
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="product-card">';
        echo '<img src="' . htmlspecialchars($product['image_url']) . '" alt="' . htmlspecialchars($product['name']) . '" class="product-image">';
        echo '<div class="product-info">';
        echo '<h3 class="product-title">' . htmlspecialchars($product['name']) . '</h3>';
        echo '<div class="product-category">' . htmlspecialchars($product['category']) . '</div>';
        echo '<span class="product-price">$' . number_format($product['price'], 2) . '</span>';
        echo '<button class="add-to-cart" data-id="' . $product['id'] . '">';
        echo '<i class="fas fa-shopping-cart"></i> Add to Cart</button>';
        echo '</div></div>';
    }
    ?>
</div>

    </div>

    <script>
        // Add to cart functionality
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const quantity = 1; // Default quantity

                // Send AJAX request to add product to cart
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ productId, quantity })
                })
                .then(response => response.json())
                .then(data => {
                    // Handle response
                    console.log(data.message);
                    alert(data.message); // Optional: Show a message to the user
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>
</html>
