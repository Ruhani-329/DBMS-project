<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, name, email, contact, address, password FROM users WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['contact'] = $user['contact'];
            $_SESSION['address'] = $user['address'];
            header("Location: user_profile.php");
            exit();
        } else {
            echo "<script>alert('Invalid username or password!'); window.location.href='user_login.html';</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password!'); window.location.href='user_login.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
