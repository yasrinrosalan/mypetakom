<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['committee_id'], $_POST['role_id'])) {
    $committee_id = $_POST['committee_id'];
    $role_id = $_POST['role_id'];

    // Check if the committee belongs to the current advisor
    $stmt = $conn->prepare("SELECT e.user_id FROM committee c JOIN event e ON c.event_id = e.event_id WHERE c.committee_id = ?");
    $stmt->bind_param("i", $committee_id);
    $stmt->execute();
    $stmt->bind_result($event_user_id);
    $stmt->fetch();
    $stmt->close();

    if ($event_user_id != $user_id) {
        echo "You are not authorized to update this committee.";
        exit;
    }

    // untuk update committee role
    $updateStmt = $conn->prepare("UPDATE committee SET role_id = ? WHERE committee_id = ?");
    $updateStmt->bind_param("ii", $role_id, $committee_id);

    if ($updateStmt->execute()) {
        $success_message = "Committee role updated successfully!";
    } else {
        $error_message = "Failed to update the committee role. Please try again.";
    }
    $updateStmt->close();
} else {
    $error_message = "Invalid request.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Committee Role</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/app.css">
</head>

<body class="bg-light">
    <?php include '../layout/header.php'; ?>
    <div class="container-fluid" style="padding-top: 80px;">
        <div class="row">
            <?php include '../layout/sidebar.php'; ?>
            <main class="col-md-10 p-4">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                    <a href="list_committee.php" class="btn btn-primary">Back to Committees</a>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                    <a href="list_committee.php" class="btn btn-primary">Back to Committees</a>
                <?php else: ?>
                    <div class="alert alert-warning">Invalid request. Please try again.</div>
                    <a href="list_committee.php" class="btn btn-primary">Back to Committees</a>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>