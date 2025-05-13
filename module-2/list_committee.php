<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['staff_id'])) {
    echo "Access denied. Please log in.";
    exit;
}

$advisor_id = $_SESSION['staff_id'];

$stmt = $conn->prepare("SELECT e.event_id, e.event_name, s.name AS student_name, r.cr_description FROM event e LEFT JOIN committee c ON e.event_id = c.event_id LEFT JOIN student s ON c.student_id = s.student_id LEFT JOIN c_role r ON c.role = r.cr_id WHERE e.advisor_id = ? ORDER BY e.event_name, r.cr_description");
$stmt->bind_param("i", $advisor_id);
$stmt->execute();
$result = $stmt->get_result();

$committees = [];
while ($row = $result->fetch_assoc()) {
    $committees[$row['event_name']][] = [
        'student' => $row['student_name'],
        'role' => $row['cr_description']
    ];
}

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
    <title>Committee Assignments - MyPetakom</title>
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
                <h2 class="fw-bold mb-4">My Committee Assignments</h2>

                <?php if (count($committees) > 0): ?>
                    <?php foreach ($committees as $event => $members): ?>
                        <div class="mb-4">
                            <h4 class="mb-3 text-primary"><?= htmlspecialchars($event) ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
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
                    <p class="text-muted">No committees assigned yet.</p>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>