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
$pageTitle = "My Event-Based Merit Summary";


$event_name = "Mobility Program to Thailand";
$event_level = "International";
$role_name = "Main Committee";
$merit_point = 100;

// Fetch student info
$stmt_student = $conn->prepare("SELECT s.student_id, s.student_hard_copy, s.program, s.year, u.name 
                                FROM student s 
                                JOIN user u ON s.user_id = u.user_id 
                                WHERE s.user_id = ?");
$stmt_student->bind_param("i", $user_id);
$stmt_student->execute();
$result_student = $stmt_student->get_result();
$student = $result_student->fetch_assoc();
$stmt_student->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>
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
                <h2 class="fw-bold mb-3">Student Merit Profile</h2>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <?php if ($student['student_hard_copy']): ?>
                            <div class="text-center mb-3">
                                <img src="<?= htmlspecialchars($student['student_hard_copy']) ?>" alt="Student Picture"
                                    class="img-fluid rounded" style="max-width: 200px; border: 1px solid #ddd;">
                            </div>
                        <?php else: ?>
                            <div class="text-center mb-3 text-muted">No student picture available.</div>
                        <?php endif; ?>

                        <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
                        <p><strong>Student ID:</strong> <?= htmlspecialchars($student['student_id']) ?></p>
                        <p><strong>Program:</strong> <?= htmlspecialchars($student['program']) ?></p>
                        <p><strong>Year:</strong> <?= htmlspecialchars($student['year']) ?></p>
                    </div>
                </div>

                <!-- Static Merit Display -->
                <div class="card shadow-sm mb-4">
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
                                <tr>
                                    <td><?= htmlspecialchars($event_name) ?></td>
                                    <td><?= htmlspecialchars($event_level) ?></td>
                                    <td><?= htmlspecialchars($role_name) ?></td>
                                    <td><?= $merit_point ?></td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="3"><strong>Total Merit</strong></td>
                                    <td><strong><?= $merit_point ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="../module-4/student_dashboard.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-arrow-left-circle-fill me-2"></i> Back to Dashboard
                    </a>
                </div>
            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>