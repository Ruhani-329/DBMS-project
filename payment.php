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

// Get booking ID and provider ID from query parameters
$booking_id = $_GET['booking_id'] ?? null;
$provider_id = $_GET['provider_id'] ?? null;

if (!$booking_id || !$provider_id) {
    echo "Invalid booking details.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle payment method selection
    $payment_method = $_POST['payment_method'] ?? null;

    if ($payment_method) {
        $stmt = $conn->prepare("INSERT INTO payment (booking_id, payment_method) VALUES (?, ?)");
        $stmt->bind_param("is", $booking_id, $payment_method);

        if ($stmt->execute()) {
            echo "<script>alert('Payment method selected successfully!'); window.location.href = 'confirmation.php';</script>";
        } else {
            echo "<script>alert('Error saving payment method. Please try again.'); window.history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please select a payment method.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .header h1 {
            color: #ff7e5f;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .payment-options {
            margin-top: 20px;
        }

        .payment-options button {
            background: #ff7e5f;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1rem;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
            margin: 10px;
        }

        .payment-options button:hover {
            background: #feb47b;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Select Payment Method</h1>
        </div>
        <form method="POST">
            <div class="payment-options">
                <button type="submit" name="payment_method" value="Payment After Service">Payment After Service</button>
                <button type="submit" name="payment_method" value="Online Payment">Online Payment</button>
            </div>
        </form>
    </div>
</body>
</html>
