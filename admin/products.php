<?php
session_start();
include('../includes/db_connection.php');
include('../includes/functions.php');

if (!isAdmin()) {
    redirect('../login.php');
}

// Delete product
if (isset($_GET['delete_id'])) {
    $delete_id = sanitizeInput($_GET['delete_id']);

    // Get the image path before deleting the product
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->bindParam(':id', $delete_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Delete the product from the database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(':id', $delete_id);
    $stmt->execute();

    // Delete the associated image file if it exists
    if (!empty($product['image']) && file_exists('../' . $product['image'])) {
        unlink('../' . $product['image']);
    }

    redirect('products.php');
}

// Get all products
$stmt = $conn->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../includes/admin_header.php');
?>

<h2>Manage Products</h2>

<a href="add_product.php">Add New Product</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Image</th>
            <th>Price</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo $product['id']; ?></td>
            <td><?php echo $product['name']; ?></td>
            <td><img src="../<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="50"></td>
            <td>₱<?php echo number_format($product['price'], 2); ?></td>
            <td><?php echo $product['category_name']; ?></td>
            <td><?php echo $product['quantity']; ?></td>
            <td>
                <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a>
                <a href="products.php?delete_id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include('../includes/footer.php'); ?>