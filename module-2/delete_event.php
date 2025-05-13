<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Advisor') {
    echo "Access denied. Only Event Advisors are allowed.";
    exit;
}


$advisor_id = $_SESSION['user_id'];

if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);

    $stmt = $conn->prepare("SELECT attendance_id FROM event_attendance_slot WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $aid = $row['attendance_id'];
        $delAttendance = $conn->prepare("DELETE FROM attendance WHERE attendance_id = ?");
        $delAttendance->bind_param("i", $aid);
        $delAttendance->execute();
        $delAttendance->close();
    }

    // 2. Delete event attendance slots
    $stmt = $conn->prepare("DELETE FROM event_attendance_slot WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();

    // 3. Delete merit awards
    $stmt = $conn->prepare("DELETE FROM merit_award WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();

    // 4. Delete committee assignments
    $stmt = $conn->prepare("DELETE FROM committee WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();

    // 5. Delete QR code image if it exists
    $stmt = $conn->prepare("SELECT event_qr_code_url FROM event WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->bind_result($qr_path);
    $stmt->fetch();
    $stmt->close();

    if ($qr_path && file_exists($qr_path)) {
        unlink($qr_path);
    }

    // 6. Delete the event
    $stmt = $conn->prepare("DELETE FROM event WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "✅ Event deleted successfully.";
} else {
    $_SESSION['success'] = "❌ Invalid event ID.";
}

// Redirect back
header("Location: advisor_dashboard.php");
exit;
