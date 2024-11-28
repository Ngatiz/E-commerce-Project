<?php include 'header.php'?>
<?php include 'navbar.php'?>
<?php include 'db.php'?>

<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Get the cart data and total price
$cart = $_SESSION['cart'] ?? [];
$total_price = 0;
foreach ($cart as $product_id => $quantity) {
    $query = "SELECT P_Price FROM products WHERE Product_ID = $product_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        $total_price += $product['P_Price'] * $quantity;
    }
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the shipping information from the form
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Add shipping cost (fixed $5 for simplicity)
    $shipping_cost = 5;
    $grand_total = $total_price + $shipping_cost;

    // Insert order into the database
    $order_query = "INSERT INTO orders (user_email, total_price, shipping_name, shipping_address, shipping_phone, status) 
                    VALUES ('{$_SESSION['email']}', '$grand_total', '$name', '$address', '$phone', 'Pending')";
    if (mysqli_query($conn, $order_query)) {
        // Get the inserted order ID
        $order_id = mysqli_insert_id($conn);

        // Insert each cart item into the order_items table
        foreach ($cart as $product_id => $quantity) {
            $item_query = "INSERT INTO order_items (order_id, product_id, quantity) 
                           VALUES ('$order_id', '$product_id', '$quantity')";
            mysqli_query($conn, $item_query);
        }

        // Clear the cart after placing the order
        unset($_SESSION['cart']);

        // Redirect to a confirmation page
        header("Location: order_confirmation.php?order_id=$order_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>


    <div class="container">
        <h1>Complete Your Order</h1>

        <form action="complete_order.php" method="POST">
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

            <h3>Order Summary</h3>
            <p><strong>Total Price:</strong> $<?php echo number_format($total_price, 2); ?></p>
            <p><strong>Shipping:</strong> $5.00</p>
            <h4><strong>Total: $<?php echo number_format($total_price + 5, 2); ?></strong></h4>

            <button type="submit" class="complete-purchase btn-success">Complete Purchase</button>
        </form>
    </div>

<?php include 'footer.php'?>
