<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quickhire";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$email = $_POST['email'];
$contact = $_POST['contact'];
$address = $_POST['address'];
$raw_password = $_POST['password'];
$hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

// SQL to register a new user
$sql = "INSERT INTO users (name, email, contact, address, password) VALUES (?, ?, ?, ?, ?)";

// Prepare statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}

// Bind parameters and execute
$stmt->bind_param("sssss", $name, $email, $contact, $address, $hashed_password);
if ($stmt->execute()) {
    echo "New user registered successfully!";
    // Redirect or do something after successful registration
    // For now, let's redirect to a simple welcome page or login page
    header("Location: welcome.html"); // Adjust the redirection to your login or another page
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
