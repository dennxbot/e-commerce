<?php
session_start();
include('includes/db_connection.php');
include('includes/functions.php');

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = sanitizeInput($_POST['product_id']);
    $quantity = sanitizeInput($_POST['quantity']);

    if (empty($product_id) || empty($quantity) || $quantity < 1) {
        // Handle error - invalid input
        redirect('index.php'); 
    }

    // Check if the product is already in the cart
    $stmt = $conn->prepare("SELECT * FROM cart_items WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        // Update quantity
        $new_quantity = $cart_item['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = :quantity WHERE id = :id");
        $stmt->bindParam(':quantity', $new_quantity);
        $stmt->bindParam(':id', $cart_item['id']);
        $stmt->execute();
    } else {
        // Add new item to cart
        $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->execute();
    }

    redirect('cart.php');
} else {
    redirect('index.php'); // Redirect if not a POST request
}