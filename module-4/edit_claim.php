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
$upload_dir = '../uploads/letters/';
$pageTitle = "Edit Merit Claim";

// Fetch claim
if (!$claim_id) {
    $_SESSION['message'] = "Invalid claim ID.";
    header("Location: claim_merit.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM merit_claim WHERE claim_id = ? AND user_id = ?");
$stmt->bind_param("ii", $claim_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Claim not found.";
    header("Location: claim_merit.php");
    exit;
}

$row = $result->fetch_assoc();

if ($row['status'] === 'Submitted') {
    $_SESSION['message'] = "You cannot edit a submitted claim.";
    header("Location: claim_merit.php");
    exit;
}

$current_file = $row['participation_letter'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['participation_letter'])) {
    $file = $_FILES['participation_letter'];

    if ($file['error'] !== 0) {
        $_SESSION['message'] = "Upload error: " . $file['error'];
        header("Location: edit_claim.php?id=$claim_id");
        exit;
    }

    $new_filename = time() . '_' . basename($file['name']);
    $targetPath = $upload_dir . $new_filename;

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Delete old file
        $oldPath = $upload_dir . $current_file;
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }

        // Update DB
        $update = $conn->prepare("UPDATE merit_claim SET participation_letter = ?, status = 'Pending' WHERE claim_id = ? AND user_id = ?");
        $update->bind_param("sii", $new_filename, $claim_id, $user_id);
        $update->execute();

        $_SESSION['message'] = "Claim updated successfully.";
    } else {
        $_SESSION['message'] = "Failed to upload new letter.";
    }

    header("Location: claim_merit.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/app.css">
</head>

<body class="bg-light">
    <?php include '../layout/header.php'; ?>
    <div class="container-fluid" style="padding-top: 80px;">
        <div class="row">
            <?php include '../layout/sidebar.php'; ?>
            <main class="col-md-10 p-4">
                <h2 class="mb-3"><?= $pageTitle ?></h2>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">Update Your Participation Letter</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="participation_letter" class="form-label">Current File</label><br>
                                <a href="<?= $upload_dir . $current_file ?>"
                                    target="_blank"><?= htmlspecialchars($current_file) ?></a>
                            </div>
                            <div class="mb-3">
                                <label for="participation_letter" class="form-label">Upload New File (PDF)</label>
                                <input type="file" name="participation_letter" accept="application/pdf" required
                                    class="form-control">
                            </div>
                            <button type="submit" class="btn btn-warning">Update Claim</button>
                            <a href="claim_merit.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>