<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../module-1/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'Student';

$currentYear = date('Y'); // Get the current year
$totalMerit = 0;
$stmt_total_merit = $conn->prepare("SELECT SUM(total) AS total_merit FROM merit_award ma JOIN event e ON ma.event_id = e.event_id WHERE ma.user_id = ? AND YEAR(e.event_date) = ?");
$stmt_total_merit->bind_param("ii", $user_id, $currentYear); // Use 'ii' for integer parameters
$stmt_total_merit->execute();
$result_total_merit = $stmt_total_merit->get_result();
if ($row_total_merit = $result_total_merit->fetch_assoc()) {
    $totalMerit = $row_total_merit['total_merit'] ?? 0;
}
$stmt_total_merit->close();

$conn->close();

// Generate the URL for the QR code
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$current_script_path = dirname($_SERVER['SCRIPT_NAME']);
$project_root = str_replace('/module-4', '', $current_script_path);
$base_url = "{$protocol}://{$host}{$project_root}";
$qr_target_url = $base_url . "/module-4/view_merit_qr.php?user_id=" . $user_id;

$qr_image_path = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_target_url);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/app.css">
</head>
<body class="bg-light">
    <?php include '../layout/header.php'; ?>
    <div class="container-fluid" style="padding-top: 80px;">
        <div class="row">
            <?php include '../layout/sidebar.php'; ?>
            <main class="col-md-10 p-4">
                <h2 class="fw-bold mb-3">Welcome, <?= htmlspecialchars($user_name) ?></h2>

                <div class="row g-4 mb-4">
                <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm border-0 text-center">
                            <div class="card-body">
                                <i class="bi bi-person-lines-fill fs-1 text-primary"></i>
                                <h5 class="card-title mt-2">My Profile</h5>
                                <p class="card-text">View or update your profile details.</p>
                                <a href="../module-1/user_profile.php" class="btn btn-outline-primary btn-sm">View</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm border-0 text-center">
                            <div class="card-body">
                                <i class="bi bi-card-checklist fs-1 text-success"></i>
                                <h5 class="card-title mt-2">Membership</h5>
                                <p class="card-text">Apply or check membership status.</p>
                                <a href="../module-1/register_membership.php" class="btn btn-outline-success btn-sm">Register</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm border-0 text-center bg-info text-white">
                            <div class="card-body">
                                <i class="bi bi-star-fill fs-1"></i>
                                <h5 class="card-title mt-2">Total Merits (<?= $currentYear ?>)</h5>
                                <p class="card-text fs-2 fw-bold"><?= $totalMerit ?></p>
                                <a href="../module-4/manage_merit.php" class="btn btn-outline-light btn-sm">View Details</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-12">  <div class="card shadow-sm border-0 text-center">
                            <div class="card-body">
                                <i class="bi bi-qr-code fs-1 text-dark"></i>
                                <h5 class="card-title mt-2">My Merit QR Code</h5>
                                <p class="card-text">Scan to view your public merit profile.</p>
                                <?php if ($qr_image_path): ?>
                                    <img src="<?= htmlspecialchars($qr_image_path) ?>" alt="Merit QR Code" class="img-fluid mt-2" style="max-width: 150px; display: block; margin: 0 auto;">
                                    <small class="text-muted mt-2 d-block">Points to: <br><a href="<?= htmlspecialchars($qr_target_url) ?>" target="_blank" style="word-break: break-all;"><?= htmlspecialchars($qr_target_url) ?></a></small>
                                <?php else: ?>
                                    <div class="alert alert-warning mt-2">QR code not available.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>