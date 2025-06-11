<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}

$user_id = $_SESSION['user_id'];

// untuk fetch semua committee assignments untuk advisor
$stmt = $conn->prepare("
    SELECT 
        c.committee_id, 
        e.event_name, 
        u.name AS student_name, 
        r.cr_description AS role_description 
    FROM event e
    JOIN committee c ON e.event_id = c.event_id
    JOIN user u ON c.user_id = u.user_id
    JOIN c_role r ON c.role_id = r.cr_id
    WHERE e.user_id = ?
    ORDER BY e.event_name ASC, r.cr_description ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// untuk group committee assignments by event
$committees = [];
while ($row = $result->fetch_assoc()) {
    $committees[$row['event_name']][] = [
        'committee_id' => $row['committee_id'],
        'student' => $row['student_name'],
        'role' => $row['role_description']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Committee Assignments - MyPetakom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                <h2 class="fw-bold mb-4">My Committee Assignments</h2>

                <?php if (count($committees) > 0): ?>
                    <?php foreach ($committees as $eventName => $members): ?>
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white fw-semibold">
                                <?= htmlspecialchars($eventName) ?>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-sm committee-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($members as $member): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($member['student']) ?></td>
                                                <td><?= htmlspecialchars($member['role']) ?></td>
                                                <td>
                                                    <!-- Edit Button -->
                                                    <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal"
                                                        data-bs-target="#editCommitteeModal"
                                                        data-committee-id="<?= $member['committee_id'] ?>"
                                                        data-event-name="<?= $eventName ?>"
                                                        data-student-name="<?= $member['student'] ?>"
                                                        data-role="<?= $member['role'] ?>">
                                                        Edit
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <a class="btn btn-sm btn-danger"
                                                        href="delete_committee.php?committee_id=<?= $member['committee_id'] ?>"
                                                        onclick="return confirm('Are you sure you want to delete this assignment?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No committee assignments found.</p>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Edit Committee Modal -->
    <div class="modal fade" id="editCommitteeModal" tabindex="-1" aria-labelledby="editCommitteeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCommitteeModalLabel">Edit Committee Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="update_committee_process.php">
                        <div class="mb-3">
                            <label class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="modal-event-name" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Student Name</label>
                            <input type="text" class="form-control" id="modal-student-name" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role_id" id="modal-role" class="form-select" required>
                                <option value="">-- Select Role --</option>
                                <option value="1">Chairperson</option>
                                <option value="2">Secretary</option>
                                <option value="3">Treasurer</option>
                                <option value="4">Logistics</option>
                                <option value="5">Promotion</option>
                            </select>
                        </div>

                        <input type="hidden" name="committee_id" id="modal-committee-id">
                        <button type="submit" class="btn btn-success w-100">Update Role</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <script>
        // modal
        $('#editCommitteeModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // untuk trigger button 
            var committeeId = button.data('committee-id');
            var eventName = button.data('event-name');
            var studentName = button.data('student-name');
            var role = button.data('role');

            // modal punya content
            $('#modal-committee-id').val(committeeId);
            $('#modal-event-name').val(eventName);
            $('#modal-student-name').val(studentName);
            $('#modal-role').val(role);
        });
    </script>

</body>

</html>