<?php
session_start();

// Database connection
include 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$sender_id = $_POST['sender_id'];
$message_text = $_POST['message_text'];

if (empty($receiver_id) || empty($message_text)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit();
}

// Insert message into database
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $sender_id, $receiver_id, $message_text);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Message sent']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
}

$stmt->close();
$conn->close();
?>
