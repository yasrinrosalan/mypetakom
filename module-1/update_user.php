<?php
include '../db_config.php';

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    $query = "UPDATE users SET name='$name', email='$email' WHERE id=$id";
    mysqli_query($conn, $query);

    echo "Profile updated.";
}

?>
<form method="post">
    User ID: <input type="text" name="id" required><br>
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    <input type="submit" name="update" value="Update">
</form>