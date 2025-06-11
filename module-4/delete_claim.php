<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../module-1/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$claim_id = $_GET['id'] ?? null;

if ($claim_id) {
    $sql = "SELECT participation_letter, status FROM merit_claim WHERE claim_id = $claim_id AND user_id = $user_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if ($row['status'] !== 'Submitted') {

            $file = '../uploads/letters/' . $row['participation_letter'];
            if (file_exists($file)) {
                unlink($file);
            }


            $delete = "DELETE FROM merit_claim WHERE claim_id = $claim_id AND user_id = $user_id";
            mysqli_query($conn, $delete);

            $_SESSION['message'] = "Claim deleted.";
        } else {
            $_SESSION['message'] = "Cannot delete submitted claim.";
        }
    } else {
        $_SESSION['message'] = "Claim not found.";
    }
} else {
    $_SESSION['message'] = "Invalid claim ID.";
}

header("Location: claim_merit.php");
exit;
