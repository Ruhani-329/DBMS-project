<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quickhire";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST['date'])) {
    $bookingDate = $_POST['date'];
    $currentDate = date('Y-m-d');

    if (strtotime($bookingDate) < strtotime($currentDate)) {
        die("Invalid booking date. Booking date cannot be in the past.");
    }
}


// Get booking data from the form
$user_id = $_SESSION['user_id'];
$provider_id = $_POST['provider_id'];
$booking_date = $_POST['date'];
$time_slot = $_POST['time'];
$message = $_POST['message'];
$paymentMethod = $_POST['payment_method'];

// Validate inputs
if (empty($booking_date) || empty($time_slot)) {
    echo "<script>alert('Please fill all required fields.'); window.history.back();</script>";
    exit();
}

// Insert booking into the database
$stmt = $conn->prepare("INSERT INTO booking (user_id, provider_id, date, time, status, message, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
$status = "Pending";
$stmt->bind_param("iisssss", $user_id, $provider_id, $booking_date, $time_slot, $status, $message, $paymentMethod);

if ($stmt->execute()) {
    $booking_id = $stmt->insert_id; // Get the newly created booking ID
    echo "'Booking is pending!"; 
} else {
    echo "<script>alert('Error in confirming booking. Please try again.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
