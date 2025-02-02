<?php
include 'db_config.php'; // Ensure this connects to your database

// Retrieve booking ID and status
$booking_id = $_POST['booking_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$booking_id || !$status) {
    die("Invalid request.");
}

// Update booking status
$sql = "UPDATE booking SET status = ? WHERE booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $booking_id);

if ($stmt->execute()) {
    if ($status === 'Successful') {
        echo "<h1>Service Request Accepted Successfully</h1>";
    } elseif ($status === 'Rejected') {
        echo "<h1>Service Request Rejected Successfully</h1>";
    }
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
