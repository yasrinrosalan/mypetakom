<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../module-1/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pageTitle = "My Event-Based Merit Summary";

$sql = "SELECT 
            e.event_name AS EventName,
            m.m_description AS EventLevel,
            cr.cr_description AS RoleName,
            CASE 
                WHEN m.m_description = 'International' AND cr.cr_description = 'Main committee' THEN 100
                WHEN m.m_description = 'International' AND cr.cr_description = 'Committee' THEN 70
                WHEN m.m_description = 'International' AND cr.cr_description = 'Participant' THEN 50
                WHEN m.m_description = 'National' AND cr.cr_description = 'Main committee' THEN 80
                WHEN m.m_description = 'National' AND cr.cr_description = 'Committee' THEN 50
                WHEN m.m_description = 'National' AND cr.cr_description = 'Participant' THEN 40
                WHEN m.m_description = 'State' AND cr.cr_description = 'Main committee' THEN 60
                WHEN m.m_description = 'State' AND cr.cr_description = 'Committee' THEN 40
                WHEN m.m_description = 'State' AND cr.cr_description = 'Participant' THEN 30
                WHEN m.m_description = 'District' AND cr.cr_description = 'Main committee' THEN 40
                WHEN m.m_description = 'District' AND cr.cr_description = 'Committee' THEN 30
                WHEN m.m_description = 'District' AND cr.cr_description = 'Participant' THEN 15
                WHEN m.m_description = 'UMPSA' AND cr.cr_description = 'Main committee' THEN 30
                WHEN m.m_description = 'UMPSA' AND cr.cr_description = 'Committee' THEN 20
                WHEN m.m_description = 'UMPSA' AND cr.cr_description = 'Participant' THEN 5
                ELSE 0
            END AS MeritPoint
        FROM merit_award ma
        JOIN event e ON ma.event_id = e.event_id
        JOIN merit m ON ma.m_id = m.m_id
        LEFT JOIN committee c ON ma.user_id = c.user_id AND ma.event_id = c.event_id
        LEFT JOIN c_role cr ON c.role_id = cr.cr_id
        WHERE ma.user_id = ?
        ORDER BY e.event_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$totalMerit = 0;
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

                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">Merit Summary Based on Event Roles</div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Event Name</th>
                                    <th>Event Level</th>
                                    <th>Your Role</th>
                                    <th>Merit Point</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['EventName']) ?></td>
                                            <td><?= htmlspecialchars($row['EventLevel']) ?></td>
                                            <td><?= htmlspecialchars($row['RoleName']) ?></td>
                                            <td><?= $row['MeritPoint'] ?></td>
                                        </tr>
                                        <?php $totalMerit += $row['MeritPoint']; ?>
                                    <?php endwhile; ?>
                                    <tr class="table-success">
                                        <td colspan="3"><strong>Total Merit</strong></td>
                                        <td><strong><?= $totalMerit ?></strong></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No merit records found.</td>
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