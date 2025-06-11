<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../module-1/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pageTitle = "Manual Merit Claim";

// List of events 
$events = [
    ['event_id' => 1, 'event_name' => 'Mobility Program to Thailand'],
    ['event_id' => 2, 'event_name' => 'National Hackathon SmartCity'],
    ['event_id' => 3, 'event_name' => 'Pahang Excel Master'],
    ['event_id' => 4, 'event_name' => 'Community Cleanup'],
    ['event_id' => 5, 'event_name' => 'COMBAT']
];


$merit_scores = [
    1 => [
        'Main Committee' => 100,
        'Committee' => 70,
        'Participant' => 50
    ],
    2 => [
        'Main Committee' => 80,
        'Committee' => 50,
        'Participant' => 40
    ],
    3 => [
        'Main Committee' => 60,
        'Committee' => 40,
        'Participant' => 30
    ],
    4 => [
        'Main Committee' => 40,
        'Committee' => 30,
        'Participant' => 20
    ],
    5 => [
        'Main Committee' => 30,
        'Committee' => 20,
        'Participant' => 10
    ]
];

// Default merit score
$selected_merit_score = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null; // Get the selected event ID
    $role = $_POST['role'] ?? null; // Get the selected role

    if (empty($event_id) || !is_numeric($event_id)) {
        $_SESSION['message'] = "Please select a valid event.";
        header("Location: manual_claim_merit.php");
        exit;
    }

    if (empty($role)) {
        $_SESSION['message'] = "Please select a valid role.";
        header("Location: manual_claim_merit.php");
        exit;
    }

    if (isset($merit_scores[$event_id][$role])) {
        $selected_merit_score = $merit_scores[$event_id][$role];
    } else {
        $_SESSION['message'] = "Invalid event or role selected.";
        header("Location: manual_claim_merit.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO merit_claim (user_id, status, event_id, role) VALUES (?, 'Pending', ?, ?)");
    $stmt->bind_param("iis", $user_id, $event_id, $role);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Merit claim submitted successfully.";

        $stmt_award = $conn->prepare("INSERT INTO merit_award (user_id, event_id, total, semester) VALUES (?, ?, ?, ?)");
        $stmt_award->bind_param("iiis", $user_id, $event_id, $selected_merit_score, date('Y'));
        $stmt_award->execute();
        $stmt_award->close();
    } else {
        $_SESSION['message'] = "Failed to submit claim to database.";
    }
    $stmt->close();

    header("Location: manual_claim_merit.php");
    exit;
}

$conn->close();
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

                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endif; ?>

                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">Submit New Merit Claim Manually</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Select Event</label>
                                <select name="event_id" id="event_id" class="form-select" required>
                                    <option value="">-- Select an Event --</option>
                                    <?php foreach ($events as $event): ?>
                                        <option value="<?= $event['event_id'] ?>">
                                            <?= htmlspecialchars($event['event_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Select Role</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="Main Committee">Main Committee</option>
                                    <option value="Committee">Committee</option>
                                    <option value="Participants">Participants</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">Submit Claim</button>
                        </form>

                        <!-- Display Merit Score if Selected -->
                        <?php if ($selected_merit_score !== null): ?>
                            <div class="mt-3">
                                <h4>Your Merit Score: <?= $selected_merit_score ?></h4>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>