<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quickhire";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$error_message = ''; // Initialize variable for error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = sanitizeInput($_POST['name']);
    $location = sanitizeInput($_POST['location']);
    $experience = (int) sanitizeInput($_POST['experience']);
    $address = sanitizeInput($_POST['address']);
    $contact = sanitizeInput($_POST['contact']);
    $email = sanitizeInput($_POST['email']);
    $category = sanitizeInput($_POST['category']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // File upload for image
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = "uploads/";
        $image = $upload_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email or name already exists
    if (empty($error_message)) {
        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM provider WHERE email = ? OR name = ?");
        $stmt_check->bind_param("ss", $email, $name);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();

        if ($count > 0) {
            $error_message = "Email or Name is already in use. Please use a unique one.";
        }
        $stmt_check->close();
    }

    // If no error, proceed with insertion
    if (empty($error_message)) {
        $stmt_insert = $conn->prepare("INSERT INTO provider 
            (name, location, password, experience, address, contact, email, category, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt_insert->bind_param(
            "sssisisss",
            $name,
            $location,
            $hashed_password,
            $experience,
            $address,
            $contact,
            $email,
            $category,
            $image
        );

        if ($stmt_insert->execute()) {
            session_start();
            $_SESSION['provider_id'] = $conn->insert_id; // Save provider_id in session
            echo "<script>alert('Provider registration successful!'); window.location.href='provider_profile.php';</script>";
        } else {
            // Display more detailed error message
            echo "Error inserting into provider table: " . $stmt_insert->error;
        }

        $stmt_insert->close();
    }
}

$conn->close();
?>
