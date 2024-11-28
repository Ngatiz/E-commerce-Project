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
    <h1>Your Cart</h1>
    
    <?php if (empty($cart)): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="index.php">Continue shopping</a> .
        </div>
    <?php else: ?>
        <div class="row">
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
                <div class="col-sm-4 mb-4" id="product-<?php echo $product_id; ?>">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($product['Image_URL']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['P_Name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['P_Name']); ?></h5>
                            <p class="card-text"><strong>Price:</strong> $<?php echo number_format($product['P_Price'], 2); ?></p>
                            <p class="card-text"><strong>Quantity:</strong>
                                <input type="number" id="quantity-<?php echo $product_id; ?>" value="<?php echo $quantity; ?>" min="1" class="form-control-sm quantity-input">
                            </p>
                            <p class="card-text"><strong>Total:</strong> $<span id="total-<?php echo $product_id; ?>"><?php echo number_format($item_total, 2); ?></span></p>
                            <button class="btn btn-danger btn-sm remove-item" data-product-id="<?php echo $product_id; ?>">Remove</button>
                        </div>
                    </div>
                </div>
                <?php
                } else {
                    // If the product is no longer in the database, allow removal
                    echo "<div class='col-sm-4 mb-4'>
                            <div class='card'>
                                <div class='card-body'>
                                    <p class='text-danger'>Product no longer available</p>
                                    <button class='btn btn-danger btn-sm remove-item' data-product-id='$product_id'>Remove</button>
                                </div>
                            </div>
                          </div>";
                }
                ?>
            <?php endforeach; ?>
        </div>

        <div class="cart-total text-center mt-4">
            <h4>Grand Total: $<span id="grand-total"><?php echo number_format($total_price, 2); ?></span></h4>
            <a href="index" class="btn btn-secondary">Continue Shopping</a>
            <a href="checkout" class="btn btn-success">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<div class="row mt-4">
    <div class="col-md-12 text-center">
        <a href="signout" class="btn btn-outline-danger">Sign out</a>
    </div>
</div>

<?php include 'footer.php'?>

<!-- AJAX Script -->
<script>
    // Handle quantity change
    document.querySelectorAll('.quantity-input').forEach(function(input) {
        input.addEventListener('input', function() {
            var product_id = this.id.split('-')[1];
            var quantity = this.value;
            updateCart(product_id, quantity);
        });
    });

    // Handle remove item
    document.querySelectorAll('.remove-item').forEach(function(button) {
        button.addEventListener('click', function() {
            var product_id = this.getAttribute('data-product-id');
            removeItem(product_id);
        });
    });

    function updateCart(product_id, quantity) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_cart.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                document.getElementById('total-' + product_id).textContent = response.item_total;
                document.getElementById('grand-total').textContent = response.grand_total;
            }
        };
        xhr.send('action=update&product_id=' + product_id + '&quantity=' + quantity);
    }

    function removeItem(product_id) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_cart.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    document.getElementById('product-' + product_id).remove();
                    document.getElementById('grand-total').textContent = response.grand_total;
                }
            }
        };
        xhr.send('action=remove&product_id=' + product_id);
    }
</script>