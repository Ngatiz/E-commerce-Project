<?php include 'header.php'?>
<?php include 'navbar.php'?>
<?php include 'db.php'?>

<?php
// Start the session to manage the cart
session_start();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Check if the product is already in the cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Update the quantity
        $_SESSION['cart'][$product_id] = $_SESSION['cart'][$product_id] + $quantity;
    } else {
        // Add new product to the cart
        $_SESSION['cart'][$product_id] = $quantity;
    }

    // Display a success message (optional)
    $success_message = "Product added to cart successfully!";
}

// Query to fetch products from the database
$query = "SELECT Product_ID, P_Name, P_Price, Image_URL, P_Description FROM products";
$result = mysqli_query(mysql: $conn, query: $query);
?>

<div class="container">
    <div class="row">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php while ($product = mysqli_fetch_assoc(result: $result)): ?>
        <div class="col-sm-4">
            <div class="card">
                <img class="product-img" src="<?php echo $product['Image_URL']; ?>" alt="<?php echo $product['P_Name']; ?>">
                <div class="text">
                    <?php
                    // Fetch the product description
                    $description = $product['P_Description'];

                    // Shorten the description to 20 words or 100 characters
                    $word_limit = 20;
                    $char_limit = 100;

                    // Split the description into words
                    $words = explode(separator: ' ', string: $description);

                    // Truncate to 20 words if necessary
                    $truncated_description = implode(separator: ' ', array: array_slice(array: $words, offset: 0, length: $word_limit));

                    // Ensure description doesn't exceed 100 characters and doesn't cut off mid-word
                    if (strlen(string: $truncated_description) < $char_limit) {
                        $truncated_description = $description;
                    } else {
                        $truncated_description = substr(string: $description, offset: 0, length: $char_limit);
                        $last_space = strrpos(haystack: $truncated_description, needle: ' ');
                        if ($last_space !== false) {
                            $truncated_description = substr(string: $truncated_description, offset: 0, length: $last_space);
                        }
                    }
                    ?>
                    <p><?php echo $truncated_description; ?>...</p>
                    <a href="product?product_id=<?php echo $product['Product_ID']; ?>" class="view-details">View Details</a>
                    <p class="price"><strong>Price:</strong> $<?php echo $product['P_Price']; ?></p>
                </div>
                
                <!-- Add to Cart Form -->
                <form action="index" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>">
                    <label for="quantity">Quantity: </label>
                    <input type="number" name="quantity" class="quantity-input" value="1" min="1" required><br>
                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'footer.php'?>
