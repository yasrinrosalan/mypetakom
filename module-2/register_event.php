<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}


$advisor_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $status = $_POST['status'];
    $advisor_id = $_POST['advisor_id'];
    $geolocation = $_POST['geolocation'];
    $merit_applied = isset($_POST['merit_applied']) ? 1 : 0;

    $approval_letter = $_FILES['approval_letter']['name'];
    $tmp = $_FILES['approval_letter']['tmp_name'];
    $upload_path = "../uploads/" . basename($approval_letter);

    if (move_uploaded_file($tmp, $upload_path)) {
        $stmt = $conn->prepare("INSERT INTO event (event_name, event_date, location, status, advisor_id, approval_letter, merit_applied, geolocation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisis", $event_name, $event_date, $location, $status, $advisor_id, $approval_letter, $merit_applied, $geolocation);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Event registered successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Database error: {$stmt->error}</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert alert-danger'>❌ File upload failed.</div>";
    }
}

// Fetch advisors from the database
$advisors = $conn->query("SELECT staff_id, name FROM staff ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register New Event - MyPetakom</title>
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
                <h2 class="fw-bold mb-4">Register New Event</h2>

                <?= $message ?? '' ?>

                <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
                    <div class="mb-3">
                        <label class="form-label">Event Name:</label>
                        <input type="text" name="event_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Event Date:</label>
                        <input type="date" name="event_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location:</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status:</label>
                        <select name="status" class="form-select" required>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Postponed">Postponed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Advisor:</label>
                        <select name="advisor_id" class="form-select" required>
                            <option value="">-- Select Advisor --</option>
                            <?php while ($row = $advisors->fetch_assoc()) : ?>
                                <option value="<?= $row['staff_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Geolocation:</label>
                        <input type="text" name="geolocation" class="form-control" required
                            placeholder="e.g., 3.745,-98.056">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Approval Letter (PDF/Image):</label>
                        <input type="file" name="approval_letter" class="form-control" accept=".pdf,.jpg,.jpeg,.png"
                            required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="merit_applied" name="merit_applied">
                        <label class="form-check-label" for="merit_applied">Apply for merit</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Register Event</button>
                </form>
            </main>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>