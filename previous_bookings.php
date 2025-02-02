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

// Fetch previous bookings for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT b.booking_id, b.provider_id, p.name AS provider_name, b.status, b.time, b.date
        FROM booking b
        JOIN provider p ON b.provider_id = p.provider_id
        WHERE b.user_id = ?
        ORDER BY b.date DESC, b.time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Store bookings
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickHire - Previous Bookings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #FF7E5F, #FEB47B);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .header-bar {
            background-color: #ff7e5f;
            color: white;
            padding: 15px 30px;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 1000px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            font-size: 0.9rem;
            color: #333;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #ff7e5f;
            color: white;
            font-weight: 600;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .no-bookings {
            text-align: center;
            margin: 20px 0;
            font-size: 1.1rem;
            color: #555;
        }

        .back-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff7e5f;
            color: white;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #e76f59;
        }

        .review-btn {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 15px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: bold;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .review-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="header-bar">
        QuickHire - Previous Bookings
    </div>

    <div class="table-container">
        <?php if (count($bookings) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Provider ID</th>
                        <th>Provider Name</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['provider_id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['provider_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['status']); ?></td>
                            <td><?php echo htmlspecialchars($booking['time']); ?></td>
                            <td><?php echo htmlspecialchars($booking['date']); ?></td>
                            <td>
                                <?php if ($booking['status'] === 'Successful'): ?>
                                    <a href="review.php?booking_id=<?php echo $booking['booking_id']; ?>" class="review-btn">Review</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-bookings">No previous bookings found.</p>
        <?php endif; ?>
    </div>

    <a href="user_profile.php" class="back-btn">Back to Profile</a>
</body>
</html>
