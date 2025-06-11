<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php'; // Ensure this path is correct for your authentication logic

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../module-1/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pageTitle = "Claim Missing Merit";
$upload_dir = '../uploads/letters/'; // Ensure this directory exists and is writable

// Handle success/error message from session
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Fetch list of active events
$events = [];
$stmt_events = $conn->prepare("SELECT event_id, event_name FROM event ORDER BY event_date DESC");
$stmt_events->execute();
$result_events = $stmt_events->get_result();
while ($row_event = $result_events->fetch_assoc()) {
    $events[] = $row_event;
}
$stmt_events->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['participation_letter'])) {
    $file = $_FILES['participation_letter'];
    $event_id = $_POST['event_id'] ?? null; // Get the selected event ID

    if (empty($event_id) || !is_numeric($event_id)) {
        $_SESSION['message'] = "Please select a valid event.";
        header("Location: claim_merit.php");
        exit;
    }

    // File upload error checks
    if ($file['error'] !== 0) {
        $_SESSION['message'] = "Upload error: " . $file['error'];
        header("Location: claim_merit.php");
        exit;
    }

    $original_filename = basename($file['name']);
    $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    $filename = time() . '_' . uniqid() . '.' . $file_extension; // Unique filename
    $targetPath = $upload_dir . $filename;


    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Set appropriate permissions
    }

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Insert into merit_claim dgn event_id
        $stmt = $conn->prepare("INSERT INTO merit_claim (user_id, participation_letter, status, event_id) VALUES (?, ?, 'Pending', ?)");
        $stmt->bind_param("isi", $user_id, $filename, $event_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Merit claim submitted successfully.";

            // amik merit score from the merit table
            $stmt_event = $conn->prepare("SELECT m.m_score 
                                          FROM merit m 
                                          INNER JOIN event e ON m.m_id = e.merit_applied
                                          WHERE e.event_id = ?");
            $stmt_event->bind_param("i", $event_id);
            $stmt_event->execute();
            $result_event = $stmt_event->get_result();
            $event_data = $result_event->fetch_assoc();

            $merit_score = $event_data['m_score'] ?? 0;

            $stmt_award = $conn->prepare("INSERT INTO merit_award (user_id, event_id, m_id, total, semester) 
                                          VALUES (?, ?, ?, ?, ?)");
            $stmt_award->bind_param("iiisi", $user_id, $event_id, $event_data['merit_applied'], $merit_score, date('Y'));
            $stmt_award->execute();
            $stmt_award->close();
        } else {
            $_SESSION['message'] = "Failed to submit claim to database.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Failed to upload letter.";
    }

    header("Location: claim_merit.php");
    exit;
}

// Fetch  claims utk student, join 'event' table utk display event name
$claims = [];
$stmt = $conn->prepare("SELECT mc.*, e.event_name FROM merit_claim mc LEFT JOIN event e ON mc.event_id = e.event_id WHERE mc.user_id = ? ORDER BY mc.claim_id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $claims[] = $row;
}
$stmt->close();
$conn->close();
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

                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endif; ?>

                <!-- Link to the new manual merit claim page -->
                <div class="mb-4">
                    <a href="manual_claim_merit.php" class="btn btn-primary">Claim Merit Manually</a>
                </div>

                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">Submit New Claim</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Select Event</label>
                                <select name="event_id" id="event_id" class="form-select" required>
                                    <option value="">-- Select an Event --</option>
                                    <?php foreach ($events as $event): ?>
                                        <option value="<?= $event['event_id'] ?>">
                                            <?= htmlspecialchars($event['event_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="participation_letter" class="form-label">Upload Participation Letter
                                    (PDF)</label>
                                <input type="file" name="participation_letter" accept="application/pdf" required
                                    class="form-control">
                                <div class="form-text">Max file size: 5MB. Allowed formats: PDF.</div>
                            </div>
                            <button type="submit" class="btn btn-success">Submit Claim</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">My Merit Claims</div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Claim ID</th>
                                    <th>Event Name</th>
                                    <th>Event ID</th> <!-- Added Event ID column -->
                                    <th>View Letter</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($claims)): ?>
                                    <?php foreach ($claims as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['claim_id']) ?></td>
                                            <td><?= htmlspecialchars($row['event_name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($row['event_id'] ?? 'N/A') ?></td>
                                            <!-- Displaying Event ID -->
                                            <td>
                                                <?php if (!empty($row['participation_letter'])): ?>
                                                    <a href="<?= htmlspecialchars($upload_dir . $row['participation_letter']) ?>"
                                                        target="_blank" class="btn btn-sm btn-info">View</a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['status']) ?></td>
                                            <td>
                                                <?php if ($row['status'] !== 'Approved' && $row['status'] !== 'Rejected'): ?>
                                                    <a href="edit_claim.php?id=<?= htmlspecialchars($row['claim_id']) ?>"
                                                        class="btn btn-sm btn-warning me-1">Edit</a>
                                                    <a href="delete_claim.php?id=<?= htmlspecialchars($row['claim_id']) ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this claim? This action cannot be undone.');">Delete</a>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">No Actions</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No claims found. Submit one above!
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>