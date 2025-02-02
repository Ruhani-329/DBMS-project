<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $query = "SELECT * FROM provider WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['provider_id'] = $row['provider_id'];
            header("Location: provider_profile.php");
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "Provider not found!";
    }

    $stmt->close();
    $conn->close();
}
?>
