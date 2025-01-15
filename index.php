<?php
session_start();
include('includes/db_connection.php');
include('includes/functions.php');
include('includes/header.php');

$sql = "SELECT p.*, c.name AS category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";

// Filter by category
if (isset($_GET['category_id'])) {
    $category_id = sanitizeInput($_GET['category_id']);
    $sql .= " WHERE p.category_id = :category_id";
}

// Sort products
if (isset($_GET['sort'])) {
    $sort = sanitizeInput($_GET['sort']);
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY p.price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY p.price DESC";
            break;
        case 'name_asc':
            $sql .= " ORDER BY p.name ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY p.name DESC";
            break;
        default:
            $sql .= " ORDER BY p.id DESC"; // Default sorting
    }
} else {
    $sql .= " ORDER BY p.id DESC"; // Default sorting
}

$stmt = $conn->prepare($sql);
if (isset($category_id)) {
    $stmt->bindParam(':category_id', $category_id);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <h2>Products</h2>

    <div>
        <h3>Categories</h3>
        <ul>
            <li><a href="index.php">All Categories</a></li>
            <?php
            $cat_stmt = $conn->query("SELECT * FROM categories");
            $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($categories as $category): ?>
                <li>
                    <a href="index.php?category_id=<?php echo $category['id']; ?>">
                        <?php echo $category['name']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div>
        <form method="get">
            <label for="sort">Sort by:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="id_desc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'id_desc') echo 'selected'; ?>>Newest</option>
                <option value="price_asc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') echo 'selected'; ?>>Price (Low to High)</option>
                <option value="price_desc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') echo 'selected'; ?>>Price (High to Low)</option>
                <option value="name_asc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') echo 'selected'; ?>>Name (A-Z)</option>
                <option value="name_desc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') echo 'selected'; ?>>Name (Z-A)</option>
            </select>
            <?php if (isset($_GET['category_id'])): ?>
                <input type="hidden" name="category_id" value="<?php echo $_GET['category_id']; ?>">
            <?php endif; ?>
        </form>
    </div>

    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                <h3><?php echo $product['name']; ?></h3>
                <p class="price">₱<?php echo number_format($product['price'], 2); ?></p>
                <p class="category">Category: <?php echo $product['category_name']; ?></p>
                <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

<?php include('includes/footer.php'); ?>