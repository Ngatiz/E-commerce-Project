<?php include 'header.php'?>
<?php include 'navbar.php'?>
<?php include 'db.php'?>

<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login");
    exit();
}

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Check if the cart is empty
$cart = $_SESSION['cart'] ?? [];
$total_price = 0;

// Add product to cart (validate and sanitize input)
if (isset($_POST['add_to_cart'])) {
    // Sanitize and validate product ID
    $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
    if (!is_numeric($product_id) || $product_id <= 0) {
        die("Invalid Product ID");
    }

    // Sanitize and validate quantity
    $quantity = filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT);
    if (!is_numeric($quantity) || $quantity <= 0) {
        die("Invalid quantity");
    }

    // Add to cart if valid
    $_SESSION['cart'][$product_id] = $quantity;
}

// Remove product from cart
if (isset($_GET['remove'])) {
    // Sanitize and validate the product ID before removing
    $product_id = filter_var($_GET['remove'], FILTER_SANITIZE_NUMBER_INT);

    // Check if the product exists in the database
    if (is_numeric($product_id) && $product_id > 0) {
        $query = "SELECT P_Name FROM products WHERE Product_ID = $product_id";
        $result = mysqli_query($conn, $query);

        // If product does not exist in database
        if (mysqli_num_rows($result) === 0) {
            // Remove from the cart anyway if not found in the database
            unset($_SESSION['cart'][$product_id]);
            // Optionally, you can display a message that the item was removed
            $success_message = "Product removed from cart.";
        } else {
            // If product exists in the database, continue with the normal removal
            unset($_SESSION['cart'][$product_id]);
            $success_message = "Product removed from cart.";
        }
    }
}
?>

<h3 class="cart-title">Your Cart</h3>

<?php
// Display cart items
if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty!</p>";
    // Button to redirect to the index page
    echo "<a href='index' class='btn btn-outline-secondary'>Return home</a>";
} else {
    echo "<ul>";
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        // Only run the query if product_id is valid
        if (is_numeric($product_id) && $product_id > 0) {
            $query = "SELECT P_Name FROM products WHERE Product_ID = $product_id";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                $product = mysqli_fetch_assoc($result);
                echo "<li>Product: " . $product['P_Name'] . " | Quantity: $quantity <a href='cart?remove=$product_id'>Remove</a></li>";
            } else {
                // If the product doesn't exist, display a message and offer to remove it
                echo "<li>Product ID: $product_id (No longer available) | Quantity: $quantity <a href='cart?remove=$product_id'>Remove</a></li>";
            }
        }
    }
    echo "</ul>";
}
?>

    <div class="row mt-4">
        <div class="col-md-12 text-center">
            <a href="signout" class="btn btn-outline-danger">Sign out</a>
        </div>
    </div>
<?php include 'footer.php'?>
