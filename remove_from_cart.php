<?php
session_start();
include('includes/db_connection.php');
include('includes/functions.php');

if (!isLoggedIn()) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $cart_item_id = sanitizeInput($_GET['id']);

    // Delete the item from the cart
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $cart_item_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
}

redirect('cart.php');