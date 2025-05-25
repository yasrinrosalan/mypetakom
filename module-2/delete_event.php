<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}

$advisor_id = $_SESSION['user_id'];

if (!isset($_GET['event_id'])) {
    $_SESSION['success'] = "Invalid request. Event ID missing.";
    header("Location: list_event.php");
    exit;
}

$event_id = intval($_GET['event_id']);

// untuk verify kalau event_id tu betul-betul advisor punya yang login
$verify = $conn->prepare("SELECT event_qr_code_url FROM event WHERE event_id = ? AND user_id = ?");
$verify->bind_param("ii", $event_id, $advisor_id);
$verify->execute();
$result = $verify->get_result();

if ($result->num_rows === 0) {
    $_SESSION['success'] = "Event not found or access denied.";
    header("Location: list_event.php");
    exit;
}

$row = $result->fetch_assoc();
$qr_path = '../' . $row['event_qr_code_url'];

// delete qr code kalau ada
if (!empty($row['event_qr_code_url']) && file_exists($qr_path)) {
    unlink($qr_path);
}

// delete event dari database
$delete = $conn->prepare("DELETE FROM event WHERE event_id = ?");
$delete->bind_param("i", $event_id);
$delete->execute();

$_SESSION['success'] = "Event deleted successfully.";
header("Location: list_event.php");
exit;