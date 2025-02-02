<?php
include 'db_config.php'; // Ensure this connects to your database

// Fetch provider ID
$provider_id = $_GET['provider_id'] ?? null;
if (!$provider_id) {
    echo "No provider specified.";
    exit();
}

// Query to fetch booking details with user information
$sql = "SELECT b.booking_id, u.name AS user_name, b.date, b.time
        FROM booking b
        JOIN users u ON b.user_id = u.user_id
        WHERE b.provider_id = ? AND b.status = 'Pending'
        ORDER BY b.date, b.time";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>
                <strong>{$row['user_name']}</strong> - {$row['date']} at {$row['time']}
                <a href='booking_details.php?booking_id={$row['booking_id']}'>Details</a>
              </li>";
    }
    echo "</ul>";
} else {
    echo "No bookings found.";
}

$stmt->close();
$conn->close();
?>
