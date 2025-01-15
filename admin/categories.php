<?php
session_start();
include('../includes/db_connection.php');
include('../includes/functions.php');

if (!isAdmin()) {
    redirect('../login.php');
}

$message = "";

// Add Category
if (isset($_POST['add_category'])) {
    $name = sanitizeInput($_POST['name']);

    if (empty($name)) {
        $message = "Category name is required";
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->bindParam(':name', $name);
        if ($stmt->execute()) {
            $message = "Category added successfully!";
        } else {
            $message = "Error adding category";
        }
    }
}

// Edit Category
if (isset($_POST['edit_category'])) {
    $id = sanitizeInput($_POST['id']);
    $name = sanitizeInput($_POST['name']);

    if (empty($name)) {
        $message = "Category name is required";
    } else {
        $stmt = $conn->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $message = "Category updated successfully!";
        } else {
            $message = "Error updating category";
        }
    }
}

// Delete Category
if (isset($_GET['delete_id'])) {
    $delete_id = sanitizeInput($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->bindParam(':id', $delete_id);
    if ($stmt->execute()) {
        $message = "Category deleted successfully!";
    } else {
        $message = "Error deleting category";
    }
}

// Get all categories
$stmt = $conn->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../includes/admin_header.php');
?>

<h2>Manage Categories</h2>

<?php if (!empty($message)): ?>
    <div class="<?php echo (strpos($message, 'Error') !== false) ? 'error' : 'success'; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<h3>Add Category</h3>
<form method="post">
    <input type="text" name="name" placeholder="Category Name">
    <button type="submit" name="add_category">Add Category</button>
</form>

<h3>Categories</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category): ?>
        <tr>
            <td><?php echo $category['id']; ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                    <input type="text" name="name" value="<?php echo $category['name']; ?>">
                    <button type="submit" name="edit_category">Update</button>
                </form>
            </td>
            <td>
                <a href="categories.php?delete_id=<?php echo $category['id']; ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include('../includes/footer.php'); ?>