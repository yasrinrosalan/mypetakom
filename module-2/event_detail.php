<?php
include '../db_config.php';

if (!isset($_GET['event_id'])) {
    echo "❌ No event ID provided.";
    exit;
}

$event_id = intval($_GET['event_id']);
$stmt = $conn->prepare("SELECT event_name, event_date, location, status, approval_letter, event_qr_code_url FROM event WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ Event not found.";
    exit;
}

$event = $result->fetch_assoc();

if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}

$advisor_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['event_name']) ?> - MyPetakom Event Details</title>
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
                <h2 class="fw-bold mb-4">Event Details</h2>

                <div class="bg-white p-4 rounded shadow-sm">
                    <p><strong>Event Name:</strong> <?= htmlspecialchars($event['event_name']) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($event['status']) ?></p>
                    <p><strong>Approval Letter:</strong>
                        <a href="../uploads/<?= $event['approval_letter'] ?>" target="_blank">
                            <?= $event['approval_letter'] ?>
                        </a>
                    </p>

                    <?php if (!empty($event['event_qr_code_url']) && file_exists($event['event_qr_code_url'])): ?>
                        <p><strong>QR Code:</strong></p>
                        <div class="text-center">
                            <img src="../<?= $event['event_qr_code_url'] ?>" alt="Event QR Code" class="img-fluid"
                                style="max-width: 200px;">
                        </div>
                    <?php else: ?>
                        <p><em>No QR Code available for this event yet.</em></p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>