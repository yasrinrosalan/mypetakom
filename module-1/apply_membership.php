<?php
include '../db_config.php';
session_start();

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $student_card = $_FILES['student_card']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($student_card);

    move_uploaded_file($_FILES["student_card"]["tmp_name"], $target_file);

    $query = "INSERT INTO membership_requests (user_id, student_card, status) VALUES ('$user_id', '$student_card', 'pending')";
    mysqli_query($conn, $query);

    echo "Request submitted successfully.";
}
?>
<form method="post" enctype="multipart/form-data">
    Upload Student Card: <input type="file" name="student_card" required><br>
    <input type="submit" name="submit" value="Submit">
</form>