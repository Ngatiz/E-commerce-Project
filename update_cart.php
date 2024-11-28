<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $product_id = $_POST['product_id'];
    
    if ($action == 'update') {
        $quantity = $_POST['quantity'];
        $_SESSION['cart'][$product_id] = $quantity;

        // Fetch the product price and calculate totals
        $query = "SELECT P_Price FROM products WHERE Product_ID = $product_id";
        $result = mysqli_query($conn, $query);
        $product = mysqli_fetch_assoc($result);
        $item_total = $product['P_Price'] * $quantity;

        // Calculate grand total
        $grand_total = 0;
        foreach ($_SESSION['cart'] as $id => $qty) {
            $query = "SELECT P_Price FROM products WHERE Product_ID = $id";
            $result = mysqli_query($conn, $query);
            $product = mysqli_fetch_assoc($result);
            $grand_total += $product['P_Price'] * $qty;
        }

        echo json_encode(['item_total' => number_format($item_total, 2), 'grand_total' => number_format($grand_total, 2)]);
    }

    if ($action == 'remove') {
        unset($_SESSION['cart'][$product_id]);

        // Calculate new grand total
        $grand_total = 0;
        foreach ($_SESSION['cart'] as $id => $qty) {
            $query = "SELECT P_Price FROM products WHERE Product_ID = $id";
            $result = mysqli_query($conn, $query);
            $product = mysqli_fetch_assoc($result);
            $grand_total += $product['P_Price'] * $qty;
        }

        echo json_encode(['success' => true, 'grand_total' => number_format($grand_total, 2)]);
    }
}
?>
