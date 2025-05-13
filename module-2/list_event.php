<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['staff_id'])) {
    echo "Access denied. Please log in.";
    exit;
}

$advisor_id = $_SESSION['staff_id'];
$stmt = $conn->prepare("SELECT * FROM event WHERE advisor_id = ? ORDER BY event_date DESC");
$stmt->bind_param("i", $advisor_id);
$stmt->execute();
$result = $stmt->get_result();

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
    <title>My Events - Advisor</title>
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
                <h2 class="fw-bold mb-4">List of My Events</h2>

                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Merit</th>
                                    <th>QR Code</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                                        <td><?= htmlspecialchars($row['event_date']) ?></td>
                                        <td><?= htmlspecialchars($row['location']) ?></td>
                                        <td class="text-capitalize"><?= $row['status'] ?></td>
                                        <td><?= $row['merit_applied'] ? 'Yes' : 'No' ?></td>
                                        <td>
                                            <?php if ($row['event_qr_code_url'] && file_exists('../' . $row['event_qr_code_url'])): ?>
                                                <a href="../<?= $row['event_qr_code_url'] ?>" target="_blank"
                                                    class="btn btn-sm btn-info text-white">QR View</a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="event_detail.php?event_id=<?= $row['event_id'] ?>"
                                                    target="_blank">View</a>
                                                <a class="btn btn-sm btn-secondary"
                                                    href="generate_event_qr.php?generate=1&event_id=<?= $row['event_id'] ?>">QR</a>
                                                <a class="btn btn-sm btn-warning"
                                                    href="update_event.php?event_id=<?= $row['event_id'] ?>">Edit</a>
                                                <a class="btn btn-sm btn-danger"
                                                    href="delete_event.php?event_id=<?= $row['event_id'] ?>"
                                                    onclick="return confirm('Are you sure?');">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No events found.</p>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>