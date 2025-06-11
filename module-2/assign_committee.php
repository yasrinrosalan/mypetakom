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
        $success_message = "Committee member assigned successfully!";
    } else {
        $success_message = "This student is already assigned for the selected event.";
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

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-info"><?= $_SESSION['success_message'] ?></div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>


                <form method="POST" action="assign_committee_process.php" class="bg-white p-4 rounded shadow-sm">
                    <div class="mb-4">
                        <label for="event_id" class="form-label fw-semibold">Select Event</label>
                        <select name="event_id" id="event_id" class="form-select" required>
                            <option value="">-- Select Event --</option>
                            <?php
                            while ($event = $events_result->fetch_assoc()) {
                                echo "<option value='{$event['event_id']}'>{$event['event_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div id="committee-rows">
                        <div class="row g-3 align-items-end committee-row mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Student</label>
                                <select name="student_id[]" class="form-select" required>
                                    <option value="">-- Select Student --</option>
                                    <?php
                                    while ($student = $students->fetch_assoc()) {
                                        echo "<option value='{$student['user_id']}'>{$student['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Committee Role</label>
                                <select name="role_id[]" class="form-select" required>
                                    <option value="">-- Select Role --</option>
                                    <?php
                                    while ($role = $roles->fetch_assoc()) {
                                        echo "<option value='{$role['cr_id']}'>{$role['cr_description']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger w-100 remove-row">Remove</button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-secondary" id="add-row">
                            <i class="bi bi-plus-circle me-1"></i> Add Another
                        </button>
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-person-plus me-1"></i> Assign Committees
                    </button>
                </form>


            </main>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('add-row').addEventListener('click', function() {
            const container = document.getElementById('committee-rows');
            const row = container.querySelector('.committee-row').cloneNode(true);

            // untuk clearkan value
            row.querySelectorAll('select').forEach(select => select.value = '');
            container.appendChild(row);
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                const rows = document.querySelectorAll('.committee-row');
                if (rows.length > 1) {
                    e.target.closest('.committee-row').remove();
                }
            }
        });
    </script>


</body>

</html>