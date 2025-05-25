<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $status = $_POST['status'];
    $merit_applied = isset($_POST['merit_applied']) ? 1 : 0;
    $generate_qr = isset($_POST['generate_qr']);

    $approval_letter = $_FILES['approval_letter']['name'];
    $tmp = $_FILES['approval_letter']['tmp_name'];

    $upload_dir = "../uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // check kalau dah ada
    }
    $upload_path = $upload_dir . basename($approval_letter);

    // check nama sama
    $check = $conn->prepare("SELECT event_id FROM event WHERE event_name = ?");
    $check->bind_param("s", $event_name);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "<div class='alert alert-danger'>An event with that name already exists. Please choose another name.</div>";
    } else {
        if (move_uploaded_file($tmp, $upload_path)) {
            $stmt = $conn->prepare("INSERT INTO event (user_id, event_name, event_date, location, status, approval_letter, merit_applied) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssi", $user_id, $event_name, $event_date, $location, $status, $approval_letter, $merit_applied);

            if ($stmt->execute()) {
                $event_id = $stmt->insert_id;
                $message = "<div class='alert alert-success'>Event registered successfully.</div>";

                //untuk generate qr code
                if ($generate_qr) {
                    require_once '../qrlib/qrlib.php';
                    $qr_folder = "../qrcodes";
                    if (!is_dir($qr_folder)) mkdir($qr_folder, 0777, true);
                    $qr_path = "$qr_folder/event_$event_id.png";
                    $qr_link = "http://localhost/MINI_PROJECT/module_2/event_detail.php?event_id=$event_id";
                    QRcode::png($qr_link, $qr_path, QR_ECLEVEL_L, 4);
                    $relative_qr_path = "qrcodes/event_$event_id.png";

                    $update = $conn->prepare("UPDATE event SET event_qr_code_url = ? WHERE event_id = ?");
                    $update->bind_param("si", $relative_qr_path, $event_id);
                    $update->execute();
                    $update->close();

                    $message .= "<div class='alert alert-success'>QR Code generated automatically.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>Database error: {$stmt->error}</div>";
            }
            $stmt->close();
        } else {
            $message = "<div class='alert alert-danger'>File upload failed.</div>";
        }
    }

    $check->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register New Event - MyPetakom</title>
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
                <h2 class="fw-bold mb-4">Register New Event</h2>

                <?= $message ?>

                <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
                    <div class="mb-3">
                        <label class="form-label">Event Name</label>
                        <input type="text" name="event_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Event Date</label>
                        <input type="date" name="event_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Postponed">Postponed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Approval Letter (PDF/Image)</label>
                        <input type="file" name="approval_letter" class="form-control" accept=".pdf,.jpg,.jpeg,.png"
                            required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="merit_applied" id="merit_applied">
                        <label class="form-check-label" for="merit_applied">Apply for merit</label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="generate_qr" id="generate_qr">
                        <label class="form-check-label" for="generate_qr">Generate QR Code</label>
                    </div>


                    <button type="submit" class="btn btn-primary">Register Event</button>
                </form>
            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>