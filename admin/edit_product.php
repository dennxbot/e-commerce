<?php
session_start();
include('../includes/db_connection.php');
include('../includes/functions.php');

if (!isAdmin()) {
    redirect('../login.php');
}

$errors = [];
$message = "";

if (isset($_GET['id'])) {
    $product_id = sanitizeInput($_GET['id']);

    // Get product data
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        redirect('products.php'); // Redirect if product not found
    }
} else {
    redirect('products.php'); // Redirect if no ID provided
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = sanitizeInput($_POST['price']);
    $category_id = sanitizeInput($_POST['category_id']);
    $quantity = sanitizeInput($_POST['quantity']);

    // Validate data (add more validation)
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($description)) {
        $errors[] = "Description is required";
    }
    if (empty($price) || !is_numeric($price)) {
        $errors[] = "Valid price is required";
    }
    if (empty($category_id)) {
        $errors[] = "Category is required";
    }
    if (empty($quantity) || !is_numeric($quantity)) {
        $errors[] = "Valid quantity is required";
    }

    // Handle image upload (if a new image is selected)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if (!in_array($image_ext, $allowed_extensions)) {
            $errors[] = "Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF";
            } elseif ($image_size > 2097152) { // 2MB limit
            $errors[] = "Image size too large. Maximum size is 2MB";
        } else {
            // Generate a unique name for the image
            $new_image_name = uniqid('', true) . '.' . $image_ext;
            $upload_path = '../uploads/' . $new_image_name;

            if (move_uploaded_file($image_tmp, $upload_path)) {
                // Delete the old image if it exists
                if (!empty($product['image']) && file_exists('../' . $product['image'])) {
                    unlink('../' . $product['image']);
                }
                $product['image'] = 'uploads/' . $new_image_name; // Update the image path
            } else {
                $errors[] = "Error uploading image";
            }
        }
    }

    if (empty($errors)) {
        // Update product in database
        $stmt = $conn->prepare("UPDATE products SET name = :name, description = :description, price = :price, category_id = :category_id, image = :image, quantity = :quantity WHERE id = :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $product['image']); // Use the updated image path
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id', $product_id);

        if ($stmt->execute()) {
            $message = "Product updated successfully!";
            // Refresh product data
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->bindParam(':id', $product_id);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $errors[] = "Error updating product";
        }
    }
}

// Get categories for dropdown
$stmt = $conn->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../includes/admin_header.php');
?>

<h2>Edit Product</h2>

<?php if (!empty($message)): ?>
    <div class="success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo $product['name']; ?>">
    </div>
    <div>
        <label for="description">Description:</label>
        <textarea name="description" id="description"><?php echo $product['description']; ?></textarea>
    </div>
    <div>
        <label for="price">Price:</label>
        <input type="number" name="price" id="price" step="0.01" value="<?php echo $product['price']; ?>">
    </div>
    <div>
        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php if ($product['category_id'] == $category['id']) echo 'selected'; ?>><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="image">Current Image:</label>
        <img src="../<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="100">
    </div>
    <div>
        <label for="image">New Image (optional):</label>
        <input type="file" name="image" id="image">
    </div>
    <div>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" value="<?php echo $product['quantity']; ?>">
    </div>
    <button type="submit">Update Product</button>
</form>

<?php include('../includes/footer.php'); ?>