<?php
session_start();

$success_message = '';
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

include '../db_config.php';

if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}


$advisor_id = $_SESSION['user_id'];


function getCount($conn, $advisor_id, $status = null)
{
    $count = 0;
    if ($status) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM event WHERE advisor_id = ? AND status = ?");
        $stmt->bind_param("is", $advisor_id, $status);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM event WHERE advisor_id = ?");
        $stmt->bind_param("i", $advisor_id);
    }
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

$totalEvents = getCount($conn, $advisor_id);
$upcomingEvents = getCount($conn, $advisor_id, 'Upcoming');
$postponedEvents = getCount($conn, $advisor_id, 'Postponed');
$cancelledEvents = getCount($conn, $advisor_id, 'Cancelled');

$stmt = $conn->prepare("SELECT event_id, event_name, event_date, status, merit_applied FROM event WHERE advisor_id = ? ORDER BY event_date DESC");
$stmt->bind_param("i", $advisor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>MyPetakom</title>
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
                <h2 class="fw-bold mb-3">My Events - Advisor Dashboard</h2>

                <?php if (!empty($success_message)) : ?>
                    <div class="alert alert-success"> <?= $success_message ?> </div>
                <?php endif; ?>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Events</h5>
                                <p class="card-text fs-4"><?= $totalEvents ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Upcoming</h5>
                                <p class="card-text fs-4"><?= $upcomingEvents ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-dark bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Postponed</h5>
                                <p class="card-text fs-4"><?= $postponedEvents ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <h5 class="card-title">Cancelled</h5>
                                <p class="card-text fs-4"><?= $cancelledEvents ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Merit Applied</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                                        <td><?= htmlspecialchars($row['event_date']) ?></td>
                                        <td><?= $row['status'] ?></td>
                                        <td><?= $row['merit_applied'] ? "Yes" : "No" ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-info text-white"
                                                href="event_detail.php?event_id=<?= $row['event_id'] ?>">View</a>
                                            <a class="btn btn-sm btn-secondary"
                                                href="generate_event_qr.php?generate=1&event_id=<?= $row['event_id'] ?>">QR</a>
                                            <a class="btn btn-sm btn-warning"
                                                href="update_event.php?event_id=<?= $row['event_id'] ?>">Edit</a>
                                            <a class="btn btn-sm btn-danger"
                                                href="delete_event.php?event_id=<?= $row['event_id'] ?>"
                                                onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No events found for this advisor.</p>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>