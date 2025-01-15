<?php
session_start();
include('includes/db_connection.php');
include('includes/functions.php');

if (isLoggedIn()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $full_name = sanitizeInput($_POST['full_name']);

    // Basic Validation (you should add more)
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $errors[] = "Username or email already exists";
    }

    if (empty($errors)) {
        $hashed_password = hashPassword($password);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name) VALUES (:username, :email, :password, :full_name)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':full_name', $full_name);

        if ($stmt->execute()) {
            // Redirect to login page or directly log the user in
            redirect('login.php');
        } else {
            $errors[] = "Error registering user";
        }
    }
}

include('includes/header.php');
?>

<h2>Register</h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post">
    <div>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?php echo isset($username) ? $username : ''; ?>">
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo isset($email) ? $email : ''; ?>">
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password">
    </div>
    <div>
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" id="full_name" value="<?php echo isset($full_name) ? $full_name : ''; ?>">
    </div>
    <button type="submit">Register</button>
</form>

<?php include('includes/footer.php'); ?>