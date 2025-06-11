<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php'; 

// Redirect if the user is not a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../module-1/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pageTitle = "View Merit Score";

// List of events to be displayed in the dropdown
$events = [
    ['event_id' => 1, 'event_name' => 'Mobility Program to Thailand'],
    ['event_id' => 2, 'event_name' => 'National Hackathon SmartCity'],
    ['event_id' => 3, 'event_name' => 'Pahang Excel Master'],
    ['event_id' => 4, 'event_name' => 'Community Cleanup'],
    ['event_id' => 5, 'event_name' => 'COMBAT']
];

// Merit score rules based on event and role
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
        'Participant' => 15
    ],
    5 => [
        'Main Committee' => 30,
        'Committee' => 20,
        'Participant' => 5
    ]
];

$selected_merit_score = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;
    $role = $_POST['role'] ?? null;

    if (empty($event_id) || !is_numeric($event_id)) {
        $_SESSION['message'] = "Please select a valid event.";
        header("Location: merit_score_display.php");
        exit;
    }

    if (empty($role)) {
        $_SESSION['message'] = "Please select a valid role.";
        header("Location: merit_score_display.php");
        exit;
    }

    // Retrieve the merit score based on selected event and role
    if (isset($merit_scores[$event_id][$role])) {
        $selected_merit_score = $merit_scores[$event_id][$role];
    } else {
        $_SESSION['message'] = "Invalid event or role selected.";
        header("Location: merit_score_display.php");
        exit;
    }
}
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

                <!-- Merit Score Form -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">Select Event and Role</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Select Event</label>
                                <select name="event_id" id="event_id" class="form-select" required>
                                    <option value="">-- Select an Event --</option>
                                    <?php foreach ($events as $event): ?>
                                        <option value="<?= $event['event_id'] ?>"><?= htmlspecialchars($event['event_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Select Role</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="Main Committee">Main Committee</option>
                                    <option value="Committee">Committee</option>
                                    <option value="Participant">Participant</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">Show Merit Score</button>
                        </form>
                    </div>
                </div>

                <?php if ($selected_merit_score !== null): ?>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-success text-white">Your Merit Score</div>
                        <div class="card-body">
                            <p class="h4">Your Merit Score: <?= $selected_merit_score ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
