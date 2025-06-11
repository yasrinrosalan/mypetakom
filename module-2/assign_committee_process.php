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
$duplicate_names = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'], $_POST['student_id'], $_POST['role_id'])) {
    $event_id = $_POST['event_id'];
    $students = $_POST['student_id'];
    $roles = $_POST['role_id'];

    for ($i = 0; $i < count($students); $i++) {
        $student_id = $students[$i];
        $role_id = $roles[$i];

        // Check for duplicates
        $check = $conn->prepare("SELECT * FROM committee WHERE event_id = ? AND user_id = ?");
        $check->bind_param("ii", $event_id, $student_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO committee (event_id, user_id, role_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $event_id, $student_id, $role_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Get student name for feedback
            $getName = $conn->prepare("SELECT name FROM user WHERE user_id = ?");
            $getName->bind_param("i", $student_id);
            $getName->execute();
            $getName->bind_result($name);
            $getName->fetch();
            $duplicate_names[] = $name;
            $getName->close();
        }

        $check->close();
    }

    if (count($duplicate_names) > 0) {
        $success_message = "Some students were already assigned and skipped: <strong>" . implode(', ', $duplicate_names) . "</strong>";
    } else {
        $success_message = "All committee members assigned successfully.";
    }

    $_SESSION['success_message'] = $success_message;
    header("Location: assign_committee.php");
    exit;
}
