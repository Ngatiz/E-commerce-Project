<?php include 'header.php'?>
<?php include 'navbar.php'?>
<?php include 'db.php'?>

<?php
// Check if the product ID is passed
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    // Fetch the product details
    $query = "SELECT Product_ID, P_Name, P_Description, P_Price, Image_URL FROM Products WHERE Product_ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if the product exists
    if ($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <img src="<?php echo htmlspecialchars($row['Image_URL']); ?>" alt="<?php echo htmlspecialchars($row['P_Name']); ?>" class="img-fluid">
                </div>
                <div class="col-sm-6">
                    <h1><?php echo htmlspecialchars($row['P_Name']); ?></h1>
                    <p><strong>Price:</strong> $<?php echo number_format($row['P_Price'], 2); ?></p>
                    <p><?php echo htmlspecialchars($row['P_Description']); ?></p>
                    <form method="post" action="cart">
                        <input type="hidden" name="product_id" value="<?php echo $row['Product_ID']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['P_Name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo $row['P_Price']; ?>">
                        <button type="submit" class="button">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo "<p>Product not found.</p>";
    }
} else {
    echo "<p>Invalid product ID.</p>";
}
?>

<?php include 'footer.php'?>

