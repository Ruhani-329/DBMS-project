<?php
session_start();
include 'db_config.php'; // Ensure this connects to your database

// // Check if provider is logged in
if (!isset($_SESSION['provider_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}
$data = json_decode(file_get_contents('php://input'), true);
$sender_id = $data['receiver_id'];
$receiver_id = $_SESSION['provider_id'];
$message = $data['message'];

if (!$receiver_id || !$message) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit();
}

// Save message to the database
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Message saved"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Message not saved"]);
}

$stmt->close();
$conn->close();
?>
