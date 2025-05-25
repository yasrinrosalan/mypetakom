<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}

$advisor_id = $_SESSION['user_id'];

if (!isset($_GET['event_id'])) {
    echo "Invalid event ID.";
    exit;
}

$event_id = intval($_GET['event_id']);
$stmt = $conn->prepare("SELECT * FROM event WHERE event_id = ? AND user_id = ?");
$stmt->bind_param("ii", $event_id, $advisor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Event not found or access denied.";
    exit;
}

$event = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $location = $_POST['location'];
    $status = $_POST['status'];
    $merit = isset($_POST['merit_applied']) ? 1 : 0;

    $update = $conn->prepare("UPDATE event SET event_name = ?, event_date = ?, location = ?, status = ?, merit_applied = ? WHERE event_id = ?");
    $update->bind_param("ssssii", $name, $date, $location, $status, $merit, $event_id);
    $update->execute();

    $_SESSION['success'] = "Event updated successfully.";
    header("Location: advisor_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Event</title>
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
                <h2 class="fw-bold mb-4">Edit Event</h2>

                <form method="POST" class="bg-white p-4 rounded shadow-sm">
                    <div class="mb-3">
                        <label class="form-label">Event Name:</label>
                        <input type="text" name="event_name" class="form-control"
                            value="<?= htmlspecialchars($event['event_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Event Date:</label>
                        <input type="date" name="event_date" class="form-control" value="<?= $event['event_date'] ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location:</label>
                        <input type="text" name="location" class="form-control"
                            value="<?= htmlspecialchars($event['location']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status:</label>
                        <select name="status" class="form-select" required>
                            <option value="Upcoming" <?= $event['status'] === 'Upcoming' ? 'selected' : '' ?>>Upcoming
                            </option>
                            <option value="Postponed" <?= $event['status'] === 'Postponed' ? 'selected' : '' ?>>
                                Postponed</option>
                            <option value="Cancelled" <?= $event['status'] === 'Cancelled' ? 'selected' : '' ?>>
                                Cancelled</option>
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="merit_applied" id="merit_applied"
                            <?= $event['merit_applied'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="merit_applied">Apply for merit</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Event</button>
                </form>
            </main>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>