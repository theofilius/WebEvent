<?php
include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

// Get current user details
$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$user_id'") or die('query failed');
$user = mysqli_fetch_assoc($query);

// Initialize CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Update logic
if (isset($_POST['update'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null; // Hash if not empty

    // Prepare the update statement
    $update_query = $conn->prepare("UPDATE `users` SET name = ?, email = ?" . ($hashed_password ? ", password = ?" : "") . " WHERE id = ?");
    
    if ($hashed_password) {
        $update_query->bind_param("sssi", $name, $email, $hashed_password, $user_id);
    } else {
        $update_query->bind_param("ssi", $name, $email, $user_id);
    }

    // Execute the update statement
    if ($update_query->execute()) {
        $message[] = 'Profile updated successfully!';
        $_SESSION['user_name'] = $name; // Update session name
        $_SESSION['user_email'] = $email; // Update session email
    } else {
        $message[] = 'Profile update failed!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="header">
    <div class="header-2">
        <div class="flex">
            <a href="home.php" class="logo">EVENTKU</a>
            <nav class="navbar">
                <a href="home.php">home</a>
                <a href="about.php">about</a>
                <a href="shop.php">shop</a>
                <a href="contact.php">contact</a>
                <a href="orders.php">orders</a>
            </nav>
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <a href="search_page.php" class="fas fa-search"></a>
                <div id="user-btn" class="fas fa-user"></div>
                <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
            </div>
        </div>
    </div>
</header>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '<div class="message"><span>' . htmlspecialchars($msg) . '</span></div>';
    }
}
?>

<div class="form-container">
    <form action="" method="post">
        <h3>Edit Profile</h3>
        <input type="text" name="name" placeholder="Enter your name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="box">
        <input type="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="box">
        <input type="password" name="password" placeholder="Enter your new password" class="box"> <!-- Optional field for new password -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="submit" name="update" value="Update Profile" class="btn">
    </form>
</div>

</body>
</html>
