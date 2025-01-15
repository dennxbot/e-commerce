<?php
session_start();
include('includes/db_connection.php');
include('includes/functions.php');

if (!isLoggedIn()) {
    redirect('login.php');
}

// Update quantities if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... (rest of your update quantity logic) ...
}

// Get cart items
$stmt = $conn->prepare("
    SELECT ci.id, ci.quantity, p.name, p.price, p.image
    FROM cart_items ci
    INNER JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = :user_id
");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('includes/header.php');
?>

<div class="cart-container"> 
    <h2>Your Cart</h2>

    <?php if (empty($cart_items)): ?>
        <p class="cart-empty">Your cart is empty.</p>
    <?php else: ?>
        <form method="post" action="cart.php">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($cart_items as $item):
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td><img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" width="50"></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <input type="number" class="update-quantity" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0">
                            </td>
                            <td>₱<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <a class="remove-item" href="remove_from_cart.php?id=<?php echo $item['id']; ?>">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-total">
                Total: $<?php echo number_format($total, 2); ?>
            </div>

            <button type="submit" class="cart-update-button">Update Cart</button>
        </form>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>