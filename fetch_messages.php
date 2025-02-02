<?php
session_start();
include 'db_config.php'; // Ensure this connects to your database

// Check if provider is logged in
if (!isset($_SESSION['provider_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$provider_id = $_SESSION['provider_id'];
$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid user ID"]);
    exit();
}

// Fetch messages from the database
$stmt = $conn->prepare("SELECT sender_id, message_text, sent_at FROM messages WHERE (sender_id = ? OR receiver_id = ?) OR (sender_id = ? OR receiver_id = ?) ORDER BY sent_at");
$stmt->bind_param("iiii", $provider_id, $user_id, $user_id, $provider_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);

$stmt->close();
$conn->close();
?>
