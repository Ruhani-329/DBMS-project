<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.html");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "quickhire");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch notifications
$user_id = $_SESSION['user_id'];
$notifications = [];
$sql = "SELECT b.booking_id, p.name AS provider_name, b.status 
        FROM booking b 
        JOIN provider p ON b.provider_id = p.provider_id 
        WHERE b.user_id = ? AND b.status IN ('Successful', 'Rejected')
        ORDER BY b.date DESC, b.time DESC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickHire - User Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #FF7E5F, #FEB47B);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .header-bar {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            background-color: #ff7e5f;
            color: white;
            padding: 15px 30px;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            border-radius: 0 0 20px 20px;
            width: fit-content;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        .notification-popup {
            position: absolute;
            top: 60px;
            right: 30px;
            width: 300px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            padding: 15px;
            z-index: 10;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .notification-popup h4 {
            margin: 0;
            color: #ff7e5f;
            font-size: 1.2rem;
        }

        .notification-item {
            margin-bottom: 10px;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 0.9rem;
            color: #333;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item strong {
            color: #ff7e5f;
        }

        .notification-item span {
            display: block;
            font-size: 0.85rem;
            color: #888;
        }

        .profile-container {
            background: white; /* Changed to solid white for a more prominent effect */
            border-radius: 15px;
            padding: 40px; /* Increased padding */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); /* Slightly stronger shadow */
            width: 80%; /* Increased width */
            max-width: 90%; /* Ensures responsiveness */
            margin: 20px auto; /* Adds some spacing around */
        }

        .profile-details {
            text-align: center;
        }

        .profile-details h1 {
            font-size: 2rem;
            color: #333;
        }

        .profile-details p {
            margin: 5px 0;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
            text-align: center;
        }

        .action-buttons a {
            padding: 8px 15px;
            background-color: #ff7e5f;
            color: white;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 0.9rem;
        }

        .action-buttons a:hover {
            background-color: #e76f59;
        }

        .home-logout-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .logout {
            color: #ff7e5f;
            text-decoration: none;
            font-weight: bold;
        }

        .logout:hover {
            color: #e76f59;
        }
    </style>
    <script>
        function toggleNotifications() {
            const popup = document.querySelector('.notification-popup');
            popup.style.display = popup.style.display === 'flex' ? 'none' : 'flex';
        }
    </script>
</head>
<body>
    <div class="header-bar">
        QuickHire - User Profile
    </div>

    <div class="notification-icon" onclick="toggleNotifications()">
        &#128276; <span id="notification-count" style="position: absolute; top: 0; right: 0; background-color: red; color: white; font-size: 0.8rem; border-radius: 50%; width: 18px; height: 18px; display: flex; justify-content: center; align-items: center;"></span>
    </div>

    <div class="notification-popup">
        <h4>Notifications</h4>
        <?php if (count($notifications) > 0): ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item">
                    <strong>Provider:</strong> <?php echo htmlspecialchars($notification['provider_name']); ?><br>
                    <span>Status: <?php echo htmlspecialchars($notification['status']); ?></span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No new notifications</p>
        <?php endif; ?>
    </div>

    <div class="profile-container">
        <div class="profile-details">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($_SESSION['contact']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($_SESSION['address']); ?></p>
        </div>
        <div class="action-buttons">
            <div class="home-logout-row">
            <a href="home.php">Home</a>

            <a href="previous_bookings.php">Previous Bookings</a>
            <!-- <a href="points.php">Points</a> -->
            <a href="edit_profile.php">Edit Profile</a>
            
                <a href="user_logout.php" class="logout">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
