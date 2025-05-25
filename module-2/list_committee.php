<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}

$user_id = $_SESSION['user_id'];

// untuk fetch data committee yang student terlibat
$stmt = $conn->prepare("
    SELECT 
        e.event_name, 
        u.name AS student_name, 
        r.cr_description 
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

// Group by event
$committees = [];
while ($row = $result->fetch_assoc()) {
    $committees[$row['event_name']][] = [
        'student' => $row['student_name'],
        'role' => $row['cr_description']
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?= htmlspecialchars($member['student']) ?></td>
                                    <td><?= htmlspecialchars($member['role']) ?></td>
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
    <?php include '../layout/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
    document.querySelectorAll('.committee-table').forEach(function(table) {
        new DataTable(table, {
            paging: false,
            searching: true,
            ordering: true,
            info: false
        });
    });
    </script>
</body>

</html>