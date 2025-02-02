<?php
include 'db_config.php';
session_start();

if (!isset($_SESSION['provider_id'])) {
    header("Location: login_provider.html");
    exit();
}

$provider_id = $_SESSION['provider_id'];
$query = "SELECT * FROM provider WHERE provider_id='$provider_id'";
$result = mysqli_query($conn, $query);
$provider = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f7fc;
        }

        header {
            background: linear-gradient(90deg, #ff7e5f, #feb47b);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .profile-header h2 {
            font-size: 28px;
            color: #333;
        }

        .profile-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            font-size: 16px;
            color: #555;
        }

        .profile-info label {
            font-weight: 600;
            color: #444;
        }

        .profile-info .info {
            font-size: 18px;
            color: #333;
        }

        footer {
            text-align: center;
            margin-top: 50px;
            background: #333;
            color: white;
            padding: 20px;
            font-size: 14px;
        }

.btn-edit, .btn-logout {
            background-color: #feb47b;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            color: white;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-edit {
            background-color: #feb47b;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            color: white;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-edit:hover {
            background-color: #ff7e5f;
        }

        .btn-subscription {
            background-color: #4CAF50;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            color: white;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .btn-subscription:hover {
            background-color: #388E3C;
        }

        .notification {
            position: relative;
            display: inline-block;
        }

        .notification .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 12px;
        }
        .notification-icon {
            position: absolute;
            top: 15px;
            right: 30px;
            cursor: pointer;
            font-size: 2rem;
            color: #ff7e5f;
            transition: color 0.3s ease;
        }

        .notification-icon:hover {
            color: #e0a1c6;
        }
    </style>
</head>
<body>
    <header>
        QuickHire Provider Profile
    </header>

    <div class="notification notification-icon" id="notificationButton" onclick="showBookingDetails()">
        &#128276;
        <span id="notificationCount" style="position: absolute; top: 0; right: 0; background-color: red; color: white; font-size: 0.8rem; border-radius: 50%; width: 18px; height: 18px; display: flex; justify-content: center; align-items: center;"></span>
    </div>

    <div class="container">
        <div class="profile-header">
            <!-- Profile Picture (Optional) -->
            <img src="avarter.png" alt="Provider Image">
            <h2>Welcome, <?= htmlspecialchars($provider['name']); ?>!</h2>
            <p>Your Profile Information</p>
        </div>

        <div class="profile-info">
            <div>
                <label for="email">Email</label>
                <p class="info"><?= htmlspecialchars($provider['email']); ?></p>
            </div>
            <div>
                <label for="category">Service Category</label>
                <p class="info"><?= htmlspecialchars($provider['category']); ?></p>
            </div>
            <div>
                <label for="experience">Years of Experience</label>
                <p class="info"><?= htmlspecialchars($provider['experience']); ?> years</p>
            </div>
            <div>
                <label for="contact">Contact Number</label>
                <p class="info"><?= htmlspecialchars($provider['contact']); ?></p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <!-- Edit Profile Button -->
            
            
            <!-- Subscription Payment Button -->
            <button onclick="window.location.href='subscription_payment.php'" class="btn-subscription">
                Subscription Payment
            </button>
            <div style="text-align: center; margin-top: 30px;">
            <button class="btn-logout" onclick="window.location.href='welcome.html'">Logout</button>
    </div>
        </div>
    </div>

    <footer>
        &copy; 2025 QuickHire - All Rights Reserved
    </footer>

    <script>
        // Fetch booking count dynamically via AJAX
        function updateNotificationCount() {
            fetch('get_booking_count.php?provider_id=<?php echo $provider_id; ?>')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('notificationCount').innerText = data.count;
                });
        }
        updateNotificationCount(); // Initial update
        setInterval(updateNotificationCount, 60000); // Refresh every minute

        // Show booking details in a semi-popup
        function showBookingDetails() {
            fetch('get_booking_details.php?provider_id=<?php echo $provider_id; ?>')
                .then(response => response.text())
                .then(html => {
                    const popup = document.getElementById('bookingDetailsPopup');
                    popup.innerHTML = html;
                    popup.style.display = 'block';
                });
        }
    </script>

    <!-- Semi-Popup Container -->
    <div id="bookingDetailsPopup" style="display: none; position: fixed; top: 20%; left: 30%; width: 40%; background: white; border: 1px solid #ccc; border-radius: 10px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.5); z-index: 1000;">
        <button onclick="document.getElementById('bookingDetailsPopup').style.display = 'none';" style="float: right;">Close</button>
        <h3>Booking Requests</h3>
        <div id="bookingDetailsContent">
            <!-- Booking details will be dynamically loaded here -->
        </div>
    </div>
</body>
</html>
