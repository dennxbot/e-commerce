<?php
session_start();
include('../includes/db_connection.php');
include('../includes/functions.php');

if (!isAdmin()) {
    redirect('../login.php');
}

// Basic statistics (you can add more)
$stmt = $conn->query("SELECT COUNT(*) FROM users");
$num_users = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM products");
$num_products = $stmt->fetchColumn();

// Add more statistics (e.g., number of orders, total revenue) if needed

include('../includes/admin_header.php');
?>

<h2>Dashboard</h2>

<p>Welcome, Admin!</p>

<div class="stats">
    <div class="stat">
        <h3>Total Users</h3>
        <p><?php echo $num_users; ?></p>
    </div>
    <div class="stat">
        <h3>Total Products</h3>
        <p><?php echo $num_products; ?></p>
    </div>
    </div>

<?php include('../includes/footer.php'); ?>