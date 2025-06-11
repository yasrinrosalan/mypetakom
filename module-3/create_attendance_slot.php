<?php
session_start();
include '../db_config.php';

$advisor_id = $_SESSION['user_id'];
$events_query = "SELECT event_id, event_name FROM event WHERE user_id = ?";
$stmt = $conn->prepare($events_query);
$stmt->bind_param("i", $advisor_id);
$stmt->execute();
$events_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Attendance Slot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/app.css">
</head>

<body class="bg-light">

    <?php include '../layout/header.php'; ?>

    <div class="container-fluid" style="padding-top: 80px; background-color: white;">
        <div class="row">
            <?php include '../layout/sidebar.php'; ?>

            <main class="col-md-10 p-4">
                <h2 class="fw-bold mb-3">Create Attendance Slot</h2>
                <form action="process_attendance_slot.php" method="POST">
                    <div class="mb-3">
                        <label for="event_id" class="form-label">Select Event</label>
                        <select class="form-select" id="event_id" name="event_id" required>
                            <option value="" disabled selected>Select an event</option>
                            <?php while ($row = $events_result->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($row['event_id']) ?>">
                                    <?= htmlspecialchars($row['event_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="slot_name" class="form-label">Slot Name</label>
                        <input type="text" class="form-control" id="slot_name" name="slot_name"
                            placeholder="e.g., Morning Session" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Slot</button>
                </form>
            </main>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>