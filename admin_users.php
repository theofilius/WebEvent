<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

// Delete user
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_users.php');
}

// Edit user logic
if(isset($_GET['edit'])){
   $edit_id = $_GET['edit'];
   $select_user = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$edit_id'") or die('query failed');
   if(mysqli_num_rows($select_user) > 0){
      $fetch_user = mysqli_fetch_assoc($select_user);
   } else {
      header('location:admin_users.php');
   }
}

// Update user details (Name, Email, Password)
if(isset($_POST['update_user'])){
   $update_user_id = $_POST['update_user_id'];
   $update_name = $_POST['update_name'];
   $update_email = $_POST['update_email'];
   $update_password = $_POST['update_password'];

   // Hash the new password
   $hashed_password = password_hash($update_password, PASSWORD_DEFAULT);

   // Update the database with the new information
   mysqli_query($conn, "UPDATE `users` SET name = '$update_name', email = '$update_email', password = '$hashed_password' WHERE id = '$update_user_id'") or die('query failed');

   header('location:admin_users.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">

   <h1 class="title">User Accounts</h1>

   <div class="box-container">
      <?php
         $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
         while($fetch_users = mysqli_fetch_assoc($select_users)){
      ?>
      <div class="box">
         <p> user id : <span><?php echo $fetch_users['id']; ?></span> </p>
         <p> username : <span><?php echo $fetch_users['name']; ?></span> </p>
         <p> email : <span><?php echo $fetch_users['email']; ?></span> </p>
         <p> user type : <span style="color:<?php if($fetch_users['user_type'] == 'admin'){ echo 'var(--orange)'; } ?>"><?php echo $fetch_users['user_type']; ?></span> </p>
         <a href="admin_users.php?edit=<?php echo $fetch_users['id']; ?>" class="option-btn">Edit User</a>
         <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>" onclick="return confirm('delete this user?');" class="delete-btn">Delete User</a>
      </div>
      <?php
         };
      ?>
   </div>

</section>

<!-- Edit User Form -->
<?php if(isset($_GET['edit'])): ?>
<section class="form-container">
   <form action="" method="post">
      <input type="hidden" name="update_user_id" value="<?php echo $fetch_user['id']; ?>">
      <h3>Edit User</h3>
      <input type="text" name="update_name" value="<?php echo $fetch_user['name']; ?>" class="box" required>
      <input type="email" name="update_email" value="<?php echo $fetch_user['email']; ?>" class="box" required>
      <input type="password" name="update_password" placeholder="Enter new password" class="box" required>
      <input type="submit" value="Update User" name="update_user" class="btn">
   </form>
</section>
<?php endif; ?>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>
