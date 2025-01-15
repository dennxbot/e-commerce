<?php
session_start();
include('includes/db_connection.php');
include('includes/functions.php');

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$message = "";

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $current_password = sanitizeInput($_POST['current_password']);
    $new_password = sanitizeInput($_POST['new_password']);
    $confirm_new_password = sanitizeInput($_POST['confirm_new_password']);

    $errors = [];

    // Validate data (add more validation as needed)
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }

    // Check if a new password was entered
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Incorrect current password";
        } elseif ($new_password != $confirm_new_password) {
            $errors[] = "New passwords do not match";
        }
    }

    if (empty($errors)) {
        // Update user data
        $stmt = $conn->prepare("UPDATE users SET full_name = :full_name, email = :email WHERE id = :user_id");
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Update password if a new one was provided
        if (!empty($new_password)) {
            $hashed_password = hashPassword($new_password);
            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :user_id");
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        }

        $message = "Profile updated successfully!";

        // Refresh user data after update
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

include('includes/header.php');
?>

<h2>Edit Profile</h2>

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

<form method="post">
    <div>
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" id="full_name" value="<?php echo $user['full_name']; ?>">
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo $user['email']; ?>">
    </div>
    <div>
        <label for="current_password">Current Password (required to change password):</label>
        <input type="password" name="current_password" id="current_password">
    </div>
    <div>
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password">
    </div>
    <div>
        <label for="confirm_new_password">Confirm New Password:</label>
        <input type="password" name="confirm_new_password" id="confirm_new_password">
    </div>
    <button type="submit">Update Profile</button>
</form>

<?php include('includes/footer.php'); ?>