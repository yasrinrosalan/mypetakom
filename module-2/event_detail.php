<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}

if (!isset($_GET['event_id'])) {
    echo "No event ID provided.";
    exit;
}

$event_id = intval($_GET['event_id']);
$advisor_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT event_name, event_date, location, status, approval_letter, event_qr_code_url 
                        FROM event 
                        WHERE event_id = ? AND user_id = ?");
$stmt->bind_param("ii", $event_id, $advisor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Event not found or access denied.";
    exit;
}

$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['event_name']) ?> - Event Details</title>
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
                <div class="card shadow-sm p-4 bg-white">
                    <h2 class="fw-bold mb-4">Event Details</h2>

                    <p><strong>Event Name:</strong> <?= htmlspecialchars($event['event_name']) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($event['status']) ?></p>

                    <p><strong>Approval Letter:</strong>
                        <?php if (!empty($event['approval_letter'])): ?>
                            <a href="../uploads/<?= urlencode($event['approval_letter']) ?>" target="_blank">
                                <?= htmlspecialchars($event['approval_letter']) ?>
                            </a>
                        <?php else: ?>
                            <em>No approval letter uploaded.</em>
                        <?php endif; ?>
                    </p>

                    <p><strong>QR Code:</strong></p>
                    <?php if (!empty($event['event_qr_code_url']) && file_exists('../' . $event['event_qr_code_url'])): ?>
                        <div class="text-center mb-3">
                            <img src="../<?= $event['event_qr_code_url'] ?>" alt="QR Code" class="img-fluid"
                                style="max-width: 200px;">
                        </div>
                        <div class="text-center">
                            <a href="../<?= $event['event_qr_code_url'] ?>" class="btn btn-outline-primary"
                                download>Download QR Code</a>
                        </div>
                    <?php else: ?>
                        <p><em>No QR Code available for this event.</em></p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>