<?php
session_start();

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

// Get the provider ID
$provider_id = $_POST['provider_id'];

// Example logic: Store the hire record (Modify as needed)
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("INSERT INTO hires (user_id, provider_id, hire_date) VALUES (?, ?, NOW())");
$stmt->bind_param("ii", $user_id, $provider_id);

if ($stmt->execute()) {
    echo "<script>alert('Provider hired successfully!'); window.location.href = 'home.php';</script>";
} else {
    echo "<script>alert('Error hiring provider. Please try again.'); window.location.href = 'find.php';</script>";
}

$stmt->close();
$conn->close();
?>
