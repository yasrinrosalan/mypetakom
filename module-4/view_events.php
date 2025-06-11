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


$stmt = $conn->prepare("SELECT e.event_id, e.event_name, e.event_date, e.status FROM event e 
                        JOIN attendance a ON e.event_id = e.event_id 
                        WHERE a.user_id = ? ORDER BY e.event_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$events_result = $stmt->get_result();

if ($events_result->num_rows == 0) {
    $no_events_message = "You have not participated in any events yet.";
} else {
    $no_events_message = null;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Events - Student Dashboard</title>
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
                <h2 class="fw-bold mb-3">Your Events</h2>

                <!-- Display message if no events found -->
                <?php if ($no_events_message): ?>
                    <div class="alert alert-warning"><?= $no_events_message ?></div>
                <?php else: ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Event Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $events_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                                        <td><?= htmlspecialchars(date('F j, Y', strtotime($row['event_date']))) ?></td>
                                        <td>
                                            <?php
                                            // Display attendance status
                                            $status = htmlspecialchars($row['status']);
                                            if ($status == 'approved') {
                                                echo "<span class='badge bg-success'>Approved</span>";
                                            } elseif ($status == 'pending') {
                                                echo "<span class='badge bg-warning'>Pending</span>";
                                            } else {
                                                echo "<span class='badge bg-danger'>Rejected</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>

            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>