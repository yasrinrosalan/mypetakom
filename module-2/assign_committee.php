<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied.";
    exit;
}

$advisor_id = $_SESSION['user_id'];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'], $_POST['user_id'], $_POST['role_id'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_POST['user_id'];
    $role_id = $_POST['role_id'];

    // untuk prevent dari duplicate assignment
    $check = $conn->prepare("SELECT * FROM committee WHERE event_id = ? AND user_id = ?");
    $check->bind_param("ii", $event_id, $user_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO committee (event_id, user_id, role_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $event_id, $user_id, $role_id);
        $stmt->execute();
        $success_message = "✅ Committee member assigned successfully!";
    } else {
        $success_message = "⚠️ This student is already assigned for the selected event.";
    }
}

// untuk load semua students
$students = $conn->query("SELECT user_id, name FROM user WHERE role_type = 'Student' ORDER BY name ASC");

// untuk load semua events yang advisor manage
$events = $conn->prepare("SELECT event_id, event_name FROM event WHERE user_id = ?");
$events->bind_param("i", $advisor_id);
$events->execute();
$events_result = $events->get_result();

// untuk load semua committee roles
$roles = $conn->query("SELECT cr_id, cr_description FROM c_role ORDER BY cr_description ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Assign Committee - MyPetakom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/app.css">
</head>

<body class="bg-light">
    <?php include '../layout/header.php'; ?>
    <div class="container-fluid" style="padding-top: 80px;">
        <div class="row">
            <?php include '../layout/sidebar.php'; ?>
            <main class="col-md-10 p-4">
                <h2 class="fw-bold mb-4">Assign Committee to Event</h2>

                <?php if (!empty($success_message)) : ?>
                <div class="alert alert-info"><?= $success_message ?></div>
                <?php endif; ?>

                <form method="POST" class="bg-white p-4 rounded shadow-sm">
                    <div class="mb-3">
                        <label class="form-label">Select Event:</label>
                        <select name="event_id" class="form-select" required>
                            <option value="">-- Select Event --</option>
                            <?php while ($row = $events_result->fetch_assoc()): ?>
                            <option value="<?= $row['event_id'] ?>"><?= htmlspecialchars($row['event_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Student:</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Select Student --</option>
                            <?php while ($row = $students->fetch_assoc()): ?>
                            <option value="<?= $row['user_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Committee Role:</label>
                        <select name="role_id" class="form-select" required>
                            <option value="">-- Select Role --</option>
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