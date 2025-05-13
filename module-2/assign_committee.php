<?php
session_start();
include '../db_config.php';

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
    <title>Assign Committee - MyPetakom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/app.css">
</head>

<body class="bg-light">

    <?php include '../layout/header.php'; ?>

    <div class="container-fluid" style="padding-top: 80px;">
        <div class="row">
            <?php include '../layout/sidebar.php'; ?>

            <main class="col-md-10 p-4">
                <h2 class="fw-bold mb-4">Assign Committee Member</h2>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $event_id = $_POST['event_id'];
                    $student_id = $_POST['student_id'];
                    $role = $_POST['role'];

                    $stmt = $conn->prepare("INSERT INTO committee (event_id, student_id, role) VALUES (?, ?, ?)");
                    $stmt->bind_param("iii", $event_id, $student_id, $role);

                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>✅ Committee member assigned successfully!</div>";
                    } else {
                        echo "<div class='alert alert-danger'>❌ Error: " . $stmt->error . "</div>";
                    }

                    $stmt->close();
                }

                $events = $conn->query("SELECT event_id, event_name FROM event ORDER BY event_date DESC");
                $students = $conn->query("SELECT student_id, name FROM student ORDER BY name ASC");
                $roles = $conn->query("SELECT cr_id, cr_description FROM c_role ORDER BY cr_description ASC");
                ?>

                <form method="POST" class="bg-white p-4 rounded shadow-sm">
                    <div class="mb-3">
                        <label class="form-label">Select Event:</label>
                        <select name="event_id" class="form-select" required>
                            <option value="">-- Choose Event --</option>
                            <?php while ($row = $events->fetch_assoc()): ?>
                                <option value="<?= $row['event_id'] ?>"><?= htmlspecialchars($row['event_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Student:</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Choose Student --</option>
                            <?php while ($row = $students->fetch_assoc()): ?>
                                <option value="<?= $row['student_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Role:</label>
                        <select name="role" class="form-select" required>
                            <option value="">-- Choose Role --</option>
                            <?php while ($row = $roles->fetch_assoc()): ?>
                                <option value="<?= $row['cr_id'] ?>"><?= htmlspecialchars($row['cr_description']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Assign Committee</button>
                </form>
            </main>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>