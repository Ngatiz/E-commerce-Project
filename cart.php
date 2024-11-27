<?php include 'header.php'?>
<?php include 'navbar.php'?>
<?php include 'db.php'?>

<?php
session_start(); // Start the session to store cart data

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Add product to cart (validate and sanitize input)
if (isset($_POST['add_to_cart'])) {
    // Sanitize and validate product ID
    $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT); // Remove any non-numeric characters
    if (!is_numeric($product_id) || $product_id <= 0) {
        die("Invalid Product ID");
    }

    // Sanitize and validate quantity
    $quantity = filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT); // Remove any non-numeric characters
    if (!is_numeric($quantity) || $quantity <= 0) {
        die("Invalid quantity");
    }

    // Add to cart if valid
    $_SESSION['cart'][$product_id] = $quantity;  // Store quantity for the product
}

// Remove product from cart
if (isset($_GET['remove'])) {
    // Sanitize and validate the product ID before removing
    $product_id = filter_var($_GET['remove'], FILTER_SANITIZE_NUMBER_INT);
    if (!is_numeric($product_id) || $product_id <= 0) {
        die("Invalid Product ID for removal");
    }
    unset($_SESSION['cart'][$product_id]);
}
?>

<h1>Your Cart</h1>

<?php
// Display cart items
if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty!</p>";
} else {
    echo "<ul>";
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        echo "<li>Product ID: $product_id | Quantity: $quantity <a href='cart?remove=$product_id'>Remove</a></li>";
    }
    echo "</ul>";
}
?>

<!-- Add product to cart -->
<form action="cart" method="POST">
    <label for="product_id">Product ID: </label>
    <input type="text" name="product_id" required><br>
    <label for="quantity">Quantity: </label>
    <input type="number" name="quantity" value="1" min="1" required><br>
    <button type="submit" name="add_to_cart">Add to Cart</button>
</form>

<?php include 'footer.php'?>