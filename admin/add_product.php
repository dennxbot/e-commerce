<?php
session_start();
include('../includes/db_connection.php');
include('../includes/functions.php');

if (!isAdmin()) {
    redirect('../login.php');
}

$errors = [];
$message = "";

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

    // Handle image upload
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

            if (!move_uploaded_file($image_tmp, $upload_path)) {
                $errors[] = "Error uploading image";
            }
        }
    } else {
        $errors[] = "Image is required";
    }

    if (empty($errors)) {
        // Insert product into database
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image, quantity) VALUES (:name, :description, :price, :category_id, :image, :quantity)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $upload_path);
        $stmt->bindParam(':quantity', $quantity);

        if ($stmt->execute()) {
            $message = "Product added successfully!";
        } else {
            $errors[] = "Error adding product";
        }
    }
}

// Get categories for dropdown
$stmt = $conn->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../includes/admin_header.php');
?>

<h2>Add Product</h2>

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
        <input type="text" name="name" id="name" value="<?php echo isset($name) ? $name : ''; ?>">
    </div>
    <div>
        <label for="description">Description:</label>
        <textarea name="description" id="description"><?php echo isset($description) ? $description : ''; ?></textarea>
    </div>
    <div>
        <label for="price">Price:</label>
        <input type="number" name="price" id="price" step="0.01" value="<?php echo isset($price) ? $price : ''; ?>">
    </div>
    <div>
        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php if (isset($category_id) && $category_id == $category['id']) echo 'selected'; ?>><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="image">Image:</label>
        <input type="file" name="image" id="image">
    </div>
    <div>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" value="<?php echo isset($quantity) ? $quantity : ''; ?>">
    </div>
    <button type="submit">Add Product</button>
</form>

<?php include('../includes/footer.php'); ?>