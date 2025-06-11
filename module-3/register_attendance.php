<?php
session_start();
include '../db_config.php';

if (!isset($_GET['code'])) {
    echo "Invalid QR code.";
    exit;
}

$code = $_GET['code'];
$student_id = $_SESSION['user_id']; // Assuming student is logged in

// Validate the QR code and retrieve attendance slot information
$stmt = $conn->prepare("SELECT slot_id, event_id FROM event_attendance_slot WHERE attendance_qr_code_url = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Invalid or expired QR code.";
    exit;
}

$slot = $result->fetch_assoc();

// Check if attendance has already been recorded
$stmt = $conn->prepare("SELECT id FROM attendance WHERE student_id = ? AND slot_id = ?");
$stmt->bind_param("ii", $student_id, $slot['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Attendance already recorded.";
    exit;
}

// Record attendance
$stmt = $conn->prepare("INSERT INTO attendance (student_id, event_id, slot_id, timestamp) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iii", $student_id, $slot['event_id'], $slot['id']);

if ($stmt->execute()) {
    echo "Attendance recorded successfully.";
} else {
    echo "Error recording attendance.";
}

?>

<script>
    navigator.geolocation.getCurrentPosition(function(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        // Send this data along with the QR code to the server for verification
    });
</script>