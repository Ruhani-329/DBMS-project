<?php
session_start();
include 'db_config.php'; // Include the database connection file

// Check if the provider is logged in
if (!isset($_SESSION['provider_id'])) {
    header("Location: login_provider.html");
    exit();
}

$provider_id = $_SESSION['provider_id'];

// Handle the subscription payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $expiry_date = date('Y-m-d H:i:s', strtotime('+1 month'));

    // Insert the subscription record into the database
    $sql = "INSERT INTO subscriptions (provider_id, amount, expiry_date, payment_status) 
            VALUES ('$provider_id', 50.00, '$expiry_date', 'pending')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Subscription initiated. Please complete the payment via $payment_method.');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickHire Subscription</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #ff9966, #ff5e62); /* Updated gradient to match admin login */
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 50px;
            text-align: center;
        }
        .content h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .content p {
            font-size: 18px;
            color: #555;
            margin: 15px 0;
            line-height: 1.6;
        }
        .benefits {
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }
        .benefits ul {
            list-style: none;
            padding: 0;
        }
        .benefits li {
            margin: 10px 0;
            color: #333;
            font-size: 16px;
        }
        .benefits li::before {
            content: "✅";
            margin-right: 10px;
            color: #ff7e5f;
        }
        .payment-options {
            margin: 30px 0;
            text-align: center;
        }
        .payment-options img {
            margin: 10px;
            width: 100px;
            height: auto;
        }
        .subscribe-button {
            background: #ff7e5f;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .subscribe-button:hover {
            background: #e0695b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Unlock Your Full Potential with QuickHire! 🌟</h1>
        </div>
        <div class="content">
            <h2>🌟 Stand Out. Get Hired. Earn More.</h2>
            <p>🎯 Why Limit Yourself?<br>With QuickHire's subscription, you'll gain exclusive access to features that can elevate your profile and supercharge your success.</p>
            <div class="benefits">
                <p><strong>💎 Premium Benefits You Can't Miss:</strong></p>
                <ul>
                    <li>Priority Listing – Be seen first by top clients.</li>
                    <li>Unlimited Applications – Never miss an opportunity again.</li>
                    <li>Advanced Analytics – Track and improve your performance.</li>
                    <li>24/7 Support – We’re here to back you up anytime.</li>
                </ul>
            </div>
            <p><strong>💼 Invest in Yourself – It’s Worth It!</strong><br>Starting at just 50 tk, you’ll open the doors to endless opportunities and higher earnings.</p>
            <p><strong>🚨 Don’t Wait – Time is Money!</strong><br>Join thousands of successful providers already growing their careers with QuickHire.</p>
            <div class="payment-options">
                <p><strong>Pay with:</strong></p>
                <img src="bkash-logo.png" alt="bKash">
                <img src="nagad-logo.png" alt="Nagad">
                <img src="rocket-logo.png" alt="Rocket">
            </div>
            <form method="POST">
                <label for="payment_method">Select Payment Method:</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                    <option value="rocket">Rocket</option>
                </select>
                <div style="margin-top: 20px;">
                    <button type="submit" class="subscribe-button">👉 Upgrade Now</button>
                </div>
            </form>
            <p><strong>💡 Remember:</strong><br>The future belongs to those who take action now. Don’t let this chance slip away!</p>
        </div>
    </div>
</body>
</html>
