<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Please log in as an Advisor.";
    exit;
}

$user_id = $_SESSION['user_id'];

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM event WHERE user_id = ? AND (event_name LIKE ? OR status LIKE ?) ORDER BY event_date DESC");
    $like = "%$search%";
    $stmt->bind_param("iss", $user_id, $like, $like);
} else {
    $stmt = $conn->prepare("SELECT * FROM event WHERE user_id = ? ORDER BY event_date DESC");
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Events - Advisor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../styles/app.css">
</head>

<body class="bg-light">
    <?php include '../layout/header.php'; ?>
    <div class="container-fluid" style="padding-top: 80px;">
        <div class="row">
            <?php include '../layout/sidebar.php'; ?>
            <main class="col-md-10 p-4">
                <h2 class="fw-bold mb-4">List of My Events</h2>

                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>

                <form method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="Search event name or status..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-outline-secondary">Search</button>
                    </div>
                </form>

                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    Add New Event
                </button>

                <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table id="eventsTable" class="table table-bordered table-striped">
                        <thead class="table-dark">
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
                                    <a href="generate_event_qr.php?generate=1&event_id=<?= $row['event_id'] ?>"
                                        class="btn btn-sm btn-secondary">Generate QR</a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a class="btn btn-sm btn-outline-primary"
                                            href="event_detail.php?event_id=<?= $row['event_id'] ?>">View</a>
                                        <a class="btn btn-sm btn-warning"
                                            href="update_event.php?event_id=<?= $row['event_id'] ?>">Edit</a>
                                        <a class="btn btn-sm btn-danger"
                                            href="delete_event.php?event_id=<?= $row['event_id'] ?>"
                                            onclick="return confirm('Are you sure to delete this event?');">Delete</a>
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

    <!-- add event punya modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEventModalLabel">Add New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="register_event.php" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="event_name" class="form-label">Event Name</label>
                            <input type="text" class="form-control" name="event_name" id="event_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="event_date" class="form-label">Event Date</label>
                            <input type="date" class="form-control" name="event_date" id="event_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" id="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" class="form-select" id="status" required>
                                <option value="Upcoming">Upcoming</option>
                                <option value="Postponed">Postponed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="merit_applied" id="merit_applied">
                            <label class="form-check-label" for="merit_applied">Apply for Merit</label>
                        </div>
                        <div class="mb-3">
                            <label for="approval_letter" class="form-label">Approval Letter (PDF/Image)</label>
                            <input type="file" name="approval_letter" id="approval_letter" class="form-control"
                                accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#eventsTable').DataTable({
            order: [
                [1, "desc"]
            ],
            columnDefs: [{
                orderable: false,
                targets: [5, 6]
            }]
        });

        // untuk refresh page 
        if (performance.navigation.type === 2) {
            location.reload(true);
        }
    });
    </script>
</body>

</html>