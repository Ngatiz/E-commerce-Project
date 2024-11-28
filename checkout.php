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
?>

<div class="container">
    <h1 class="my-4">Checkout</h1>

    <?php if (empty($cart)): ?>
        <div class="alert alert-warning">
            Your cart is empty. <a href="index.php">Continue shopping</a> to add items to your cart.
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <h3>Your Items</h3>
                <?php foreach ($cart as $product_id => $quantity): ?>
                    <?php
                    // Fetch product details from the database
                    $query = "SELECT P_Name, P_Price, Image_URL FROM products WHERE Product_ID = $product_id";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $product = mysqli_fetch_assoc($result);
                        $item_total = $product['P_Price'] * $quantity;
                        $total_price += $item_total;
                    ?>
                    <div class="card mb-4">
                        <div class="row no-gutters">
                            <div class="col-md-4">
                                <img src="<?php echo htmlspecialchars($product['Image_URL']); ?>" class="card-img" alt="<?php echo htmlspecialchars($product['P_Name']); ?>">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['P_Name']); ?></h5>
                                    <p><strong>Price:</strong> $<?php echo number_format($product['P_Price'], 2); ?></p>
                                    <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
                                    <p><strong>Total:</strong> $<?php echo number_format($item_total, 2); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                <?php endforeach; ?>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <p><strong>Subtotal:</strong> $<?php echo number_format($total_price, 2); ?></p>
                        <p><strong>Shipping:</strong> $5.00</p> <!-- You can adjust the shipping cost or make it dynamic -->
                        <h4>Total: $<?php echo number_format($total_price + 5, 2); ?></h4>

                        <form action="complete_order.php" method="POST">
                            <h5>Shipping Information</h5>
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Shipping Address</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">Complete Purchase</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="row mt-4">
    <div class="col-md-12 text-center">
        <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
    </div>
</div>

<?php include 'footer.php'?>
