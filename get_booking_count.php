<?php
include 'db_config.php'; // Ensure this connects to your database

header('Content-Type: application/json');

// Fetch provider ID
$provider_id = $_GET['provider_id'] ?? null;
if (!$provider_id) {
    echo json_encode(['count' => 0]);
    exit();
}

// Query to get the count of bookings with status 'Pending'
$sql = "SELECT COUNT(*) AS count FROM booking WHERE provider_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(['count' => $row['count']]);
$stmt->close();
$conn->close();
?>
