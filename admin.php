<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<?php include 'db.php'; ?>

<?php
// Start session for messages
session_start();

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_row'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];

    if (!empty($name) && !empty($description) && !empty($price) && !empty($image_url)) {
        $query = "INSERT INTO products (P_Name, P_Description, P_Price, Image_URL) 
                  VALUES ('$name', '$description', '$price', '$image_url')";
        mysqli_query(mysql: $conn, query: $query);
        $_SESSION['message'] = "Product added successfully!";
    } else {
        $_SESSION['error'] = "All fields must be filled to add a product!";
    }
}

// Handle Edit Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];

    if (!empty($product_id) && !empty($name) && !empty($description) && !empty($price) && !empty($image_url)) {
        $query = "UPDATE products 
                  SET P_Name='$name', P_Description='$description', P_Price='$price', Image_URL='$image_url' 
                  WHERE Product_ID='$product_id'";
        mysqli_query(mysql: $conn, query: $query);
        $_SESSION['message'] = "Product updated successfully!";
    } else {
        $_SESSION['error'] = "All fields must be filled to edit the product!";
    }
}

// Handle Delete Product
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM products WHERE Product_ID='$delete_id'";
    mysqli_query(mysql: $conn, query: $query);
    $_SESSION['message'] = "Product deleted successfully!";
}

// Fetch All Products
$query = "SELECT * FROM products";
$result = mysqli_query(mysql: $conn, query: $query);
?>

<div class="container mt-5">
    <h2>Admin - Manage Products</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="admin">
        <table id="resizableTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $product['Product_ID']; ?></td>
                    <td><?php echo $product['P_Name']; ?></td>
                    <td><?php echo $product['P_Description']; ?></td>
                    <td>shs<?php echo $product['P_Price']; ?></td>
                    <td><?php echo $product['Image_URL']; ?></td>
                    <td>
                        <!-- Edit Button -->
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $product['Product_ID']; ?>">Edit</button>

                        <!-- Delete Button -->
                        <a href="admin?delete_id=<?php echo $product['Product_ID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $product['Product_ID']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $product['Product_ID']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel<?php echo $product['Product_ID']; ?>">Edit Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="admin">
                                    <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo $product['P_Name']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" class="form-control" required><?php echo $product['P_Description']; ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" name="price" class="form-control" value="<?php echo $product['P_Price']; ?>" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image_url" class="form-label">Image URL</label>
                                        <input type="text" name="image_url" class="form-control" value="<?php echo $product['Image_URL']; ?>" required>
                                    </div>
                                    <button type="submit" name="edit_product" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>

                <!-- Row for Adding New Product -->
                <tr>
                    <td>Auto-generated</td>
                    <td><input type="text" name="name" class="form-control" placeholder="Enter name" required></td>
                    <td><textarea name="description" class="form-control" placeholder="Enter description" required></textarea></td>
                    <td><input type="number" name="price" class="form-control" placeholder="Enter price" step="0.01" required></td>
                    <td><input type="text" name="image_url" class="form-control" placeholder="Enter image URL" required></td>
                    <td>
                        <button type="submit" name="add_row" class="btn btn-success">Add Product</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        $("#resizableTable").colResizable({
            liveDrag: true,
            minWidth: 20
        });

        // Hide the success message after 3 seconds
        setTimeout(function() {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 3000);
    });
</script>