<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.html");
    exit();
}

// Check if the booking ID is provided
if (!isset($_GET['booking_id'])) {
    die("Booking ID is missing.");
}

// Database connection
$conn = new mysqli("localhost", "root", "", "quickhire");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$booking_id = intval($_GET['booking_id']);

// Fetch provider details based on the booking
$sql = "SELECT b.provider_id, p.name AS provider_name 
        FROM booking b 
        JOIN provider p ON b.provider_id = p.provider_id 
        WHERE b.booking_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid booking ID.");
}

$booking = $result->fetch_assoc();
$provider_id = $booking['provider_id'];
$provider_name = $booking['provider_name'];

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $ratings = intval($_POST['ratings']);
    $comment = trim($_POST['comment']);

    // Validation: Ensure ratings and comment are not empty
    if (empty($ratings) || empty($comment)) {
        $error = "Please provide a rating and a comment.";
    } else {
        // Insert the review into the database
        $insert_sql = "INSERT INTO review (user_id, provider_id, ratings, comment) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiis", $user_id, $provider_id, $ratings, $comment);

        if ($insert_stmt->execute()) {
            header("Location: user_profile.php?review_success=1");
            exit();
        } else {
            $error = "Error submitting your review. Please try again.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickHire - Leave a Review</title>
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
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .review-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }

        .review-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            resize: none;
            height: 100px;
        }

        .form-group .stars {
            display: flex;
            gap: 5px;
        }

        .stars input {
            display: none;
        }

        .stars label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .stars input:checked ~ label,
        .stars label:hover,
        .stars label:hover ~ label {
            color: #f7c602; /* Golden color */
        }

        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        .submit-btn {
            width: 100%;
            padding: 10px;
            background-color: #ff7e5f;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #e76f59;
        }
    </style>
</head>
<body>
    <div class="review-container">
        <h2>Leave a Review</h2>
        <p><strong>Provider Name:</strong> <?php echo htmlspecialchars($provider_name); ?></p>
        <p><strong>Provider ID:</strong> <?php echo htmlspecialchars($provider_id); ?></p>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Rating:</label>
                <div class="stars">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" name="ratings" id="star-<?php echo $i; ?>" value="<?php echo $i; ?>">
                        <label for="star-<?php echo $i; ?>">&#9733;</label>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="comment">Comment:</label>
                <textarea id="comment" name="comment" placeholder="Write your review here..." required></textarea>
            </div>
            <button type="submit" class="submit-btn">Submit Review</button>
        </form>
    </div>
</body>
</html>
