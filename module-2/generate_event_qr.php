<?php
include '../db_config.php';
include '../qrlib/qrlib.php';

$base_url = "http://localhost/MINI_PROJECT/module_2/event_detail.php?event_id=";
$events = $conn->query("SELECT event_id, event_name FROM event ORDER BY event_id DESC");

// if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Advisor') {
//     echo "Access denied. Only Event Advisors are allowed.";
//     exit;
// }


// $advisor_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>QR Code Generator - MyPetakom</title>
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
                <h2 class="fw-bold mb-4">Generate QR Code for Events</h2>

                <?php
                if (isset($_GET['generate']) && isset($_GET['event_id'])) {
                    $event_id = intval($_GET['event_id']);
                    $event_url = $base_url . $event_id;
                    $qr_file = "../qrcodes/event_{$event_id}.png";

                    if (!file_exists('../qrcodes')) {
                        mkdir('../qrcodes', 0777, true);
                    }

                    QRcode::png($event_url, $qr_file, QR_ECLEVEL_L, 4);

                    $stmt = $conn->prepare("UPDATE event SET event_qr_code_url = ? WHERE event_id = ?");
                    $relative_path = "qrcodes/event_{$event_id}.png";
                    $stmt->bind_param("si", $relative_path, $event_id);
                    $stmt->execute();
                    $stmt->close();

                    echo "<div class='alert alert-success'>✅ QR Code generated and saved successfully!</div>";

                    echo "<div class='bg-white p-4 rounded shadow-sm text-center'>";
                    echo "<img src='../$relative_path' alt='QR Code' class='img-fluid mb-3' style='max-width: 200px;'>";
                    echo "<div class='d-flex justify-content-center gap-3'>";
                    echo "<a class='btn btn-outline-primary' href='../$relative_path' download>Download QR Code</a>";
                    echo "<a class='btn btn-outline-secondary' href='event_detail.php?event_id=$event_id' target='_blank'>View Event Page</a>";
                    echo "</div></div>";
                }
                ?>

                <form method="GET" class="bg-white p-4 rounded shadow-sm mt-4">
                    <div class="mb-3">
                        <label for="event_id" class="form-label">Select Event:</label>
                        <select name="event_id" id="event_id" class="form-select" required>
                            <option value="">-- Choose Event --</option>
                            <?php while ($row = $events->fetch_assoc()): ?>
                                <option value="<?= $row['event_id'] ?>"><?= htmlspecialchars($row['event_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" name="generate" value="1" class="btn btn-primary">Generate QR Code</button>
                </form>
            </main>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>